<?php



class Model_Data extends ModelObject
{


	function grab($dbside, $table, $starting, $limit, $what = '*')
	{
		$query = $this->grabQuery($dbside, $table, $starting, $limit, $what);
	
		$db = $this->getDatabase($dbside);
		$primaryKeyData =  $this->retrievePrimaryKeyData($dbside, $table);		
		$primaryKeyColumn = $primaryKeyData[0]['COLUMN_NAME'];
		$return = $db->returnAssocTable($query);

		return $this->reIndexData($return, $primaryKeyColumn);
	}
	
	
	function grabQuery($dbside, $table, $starting, $limit, $what = '*')
	{
		if (is_array($what))
		{
			$what = "`".implode('`, `', $what)."`";
		}
	
		$starting = is_numeric($starting) ? $starting : 0;
		$limit = is_numeric($limit) ? $limit : 0;
	
		$db = $this->getDatabase($dbside);
		$primaryKeyData =  $this->retrievePrimaryKeyData($dbside, $table);		
		$primaryKeyColumn = $primaryKeyData[0]['COLUMN_NAME'];
		
		$query = "SELECT $what FROM $table WHERE $primaryKeyColumn >= $starting ORDER BY $primaryKeyColumn ASC LIMIT $limit";
		return $query;
	}
	
	
	function metadata($dbside, $table)
	{
	
		$fulldb = $this->getSession()->get($dbside.'_database');
		$db = $this->getDatabase($dbside);
	
		$return = $db->returnAssocRow("SELECT 
				it.TABLE_ROWS,
				CONCAT(FORMAT(it.DATA_LENGTH, 0), ' bytes') as DATA_LENGTH, 
				it.CHECKSUM
			FROM 
				INFORMATION_SCHEMA.TABLES it 
			WHERE it.TABLE_SCHEMA='$fulldb' AND it.TABLE_NAME='$table'");	
	
		// $db->returnAssocRow("OPTIMIZE TABLE $table");
		// $db->returnAssocRow("ANALYZE TABLE $table");
		$tmp = $db->returnAssocRow("CHECKSUM TABLE $table");
	
		$return['CHECKSUM'] = $tmp['Checksum'];
		
		return $return;
	}
	


	function fastmetadata($dbside)
	{
	
		$fulldb = $this->getSession()->get($dbside.'_database');
		$db = $this->getDatabase($dbside);
	
		$return = $db->returnAssocTable("SELECT 
				it.TABLE_NAME,
				it.TABLE_ROWS,
				CONCAT(FORMAT(it.DATA_LENGTH, 0), ' bytes') as DATA_LENGTH, 
				it.CHECKSUM
			FROM 
				INFORMATION_SCHEMA.TABLES it 
			WHERE it.TABLE_SCHEMA='$fulldb'");	
			
			
		$newer = array();
		
		foreach ($return as $row)
		{
			$newer[$row['TABLE_NAME']] = $row;
		}
	
		
		return $newer;
	}
	





	
	function remotecopy($table, $limit = 100)
	{
		$table = cleanifyEntityName($table);
		$db = $this->getDatabase('source');
		$alldata = $db->returnAssocTable('SELECT * FROM '.$table.' LIMIT '.$limit);
				
		$statements = array();
		
		foreach ($alldata as $row)
		{
		
			$pieces = array();
		
			foreach ($row as $cell)
			{
				if ($cell === null)
					$pieces[] = "NULL";
				else
					$pieces[] = "'".addslashes($cell). "'";
			
			}
		
			$statements[] = "(" . implode(", ", $pieces) . ")";
		
		}
	
		$sqls = array();
		
		
		if (count($statements) > 0)
		{
			$sqls[] = "TRUNCATE $table";
			$sqls[] = "INSERT INTO $table VALUES \n".implode(", \n", $statements);
		}
	
		return $sqls;
	}
	
	

	function localcopy($table)
	{
		$session = $this->getSession();
		$source = $session->get('source_database');
		$dest = $session->get('dest_database');
	
		$sourceColumnData = $this->getModel('column')->show('source', $table);
		$destColumnData = $this->getModel('column')->show('dest', $table);
		$sourceColumns = Arrays::extractColumn($sourceColumnData, "COLUMN_NAME");
		$destColumns = Arrays::extractColumn($destColumnData, "COLUMN_NAME");
		
		
		$allColumns = array_intersect($destColumns, $sourceColumns); 
		$selects = $this->objectArrayQuoting($allColumns);
		
		$sqls = array();
		$sqls[] = "TRUNCATE $dest.$table";
		$sqls[] = "INSERT INTO $dest.$table \n($selects) \nSELECT $selects \nFROM $source.$table";
	
		return $sqls;
	}
	
	
	function differenceMap()
	{
		$tableModel = $this->getmodel('table');
			
		$sourceTableFull = $tableModel->showfull('source');
		$destinationTableFull = $tableModel->showfull('dest');

		$sourceTable = extractColumnFromResult($sourceTableFull, 'TABLE_NAME');
		$destinationTable = extractColumnFromResult($destinationTableFull, 'TABLE_NAME');
		
		$metasource = $this->fastmetadata('source');
		$metadest = $this->fastmetadata('dest');
		

		$unique = differenceMerge($sourceTable, $destinationTable);
		
		
		$resource = array();


		foreach ($unique as $table)
		{
			
			if (in_array($table, $sourceTable) == false)
			{
				$resource[$table] = "no source";
			}
			else if (in_array($table, $destinationTable) == false)
			{			
				$resource[$table] = "no dest";
			}
			else if ($metasource[$table] == $metadest[$table])
			{
				$resource[$table] = "equal";
			}
			else			
			{
				$resource[$table] = "difference";
			}

		}	
		
		
		return $resource;
	
	}
	
	
	/*

	function orderByConstraints($dbside, $table)
	{
		$columns = $this->constrainedColumns($dbside, $table);
		
		if (count($columns) == 0)
			return "";
		
		$pieces = array();
		
		foreach ($columns as $col)
		{
			$pieces[] = "`$col` DESC";
		}
		
		return "ORDER BY ".implode(", ", $pieces);
	}
	
	
	
	
	function constrainedColumns($dbside, $table)
	{
		$db = $this->getDatabase($dbside);
		$fulldb = $this->getSession()->get($dbside.'_database');
		
		return $db->returnColumn("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA='$fulldb' AND TABLE_NAME='$table' ORDER BY ORDINAL_POSITION ASC");
	
	
	
	}
	
	*/
	
	
	
	function retrievePrimaryKeyData($dbside, $table)
	{
		
		$db = $this->getDatabase($dbside);
		$fulldb = $this->getSession()->get($dbside.'_database');
		
		$return = $db->returnAssocTable("
		SELECT * 
		FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE tc 
		JOIN INFORMATION_SCHEMA.COLUMNS isc ON tc.TABLE_SCHEMA = isc.TABLE_SCHEMA AND tc.TABLE_NAME = isc.TABLE_NAME AND tc.COLUMN_NAME = isc.COLUMN_NAME
		WHERE tc.TABLE_SCHEMA='$fulldb' AND tc.TABLE_NAME='$table' AND tc.CONSTRAINT_NAME='PRIMARY'");
		
		return $return;
		
	}
	
	
	
	
	function checkPrimaryKeyUsability($dbside, $table)
	{
		$return = $this->retrievePrimaryKeyData($dbside, $table);
	
	
		if (count($return) == 1)
		{
//			if ($return[0]['EXTRA'] == 'auto_increment')
				if ($return[0]['COLUMN_KEY'] == 'PRI')
					if (stripos($return[0]['COLUMN_TYPE'], "int") !== false)
						return true;
		}
	
		
		return false;
	
	
	}
	
	
	
	
	
	function tabularDifferenceMap($table, $sourceData, $destData)
	{
	
		$primaryKeyData =  $this->retrievePrimaryKeyData('source', $table);
		$primaryKeyColumn = $primaryKeyData[0]['COLUMN_NAME'];
		
	
		$sourceIds = extractColumnFromResult($sourceData, $primaryKeyColumn);
		$destIds = extractColumnFromResult($destData, $primaryKeyColumn);
		$unique = differenceMerge($sourceIds, $destIds);

		
		$reIndexSourceData = $this->reIndexData($sourceData, $primaryKeyColumn);
		$reIndexDestData = $this->reIndexData($destData, $primaryKeyColumn);


		$resource = array();


		foreach ($unique as $table)
		{
			
			if (in_array($table, $sourceIds) == false)
			{
				$resource[$table] = "delete";
			}
			else if (in_array($table, $destIds) == false)
			{			
				$resource[$table] = "insert";
			}
			else
			{
				$resource[$table] = ($reIndexSourceData[$table] == $reIndexDestData[$table]) ? '' : 'update';
			}	

		}	


		return $resource;


		
	//	print_pre($resource); exit;
		
	
	
	}
	
	
	
	function reIndexData($data, $primaryKeyColumn)
	{
		$newData = array();
		
		
		foreach ($data as $datum)
		{
			$primaryKeyValue = $datum[$primaryKeyColumn];
			$newData[$primaryKeyValue] = $datum;
		}
		
		return $newData;
	}
	
	
	
/*	
	function primaryKeyDifference($source, $dest, $table)
	{
	
		$newsource = array();
		$sourcePrimaries = $this->constrainedColumns('source', $table);
	
		foreach ($source as $key => $val)
		{
			$keyPiece = array();
		
			foreach ($sourcePrimaries as $col)
			{
				$keyPiece[] = $val[$col];
			}
		
			$keyvalue = implode(" ", $keyPiece);
			$newsource[$keyvalue] = $val;
		}
		
	
		$newdest = array();
		$sourcePrimaries = $this->constrainedColumns('dest', $table);
	
		foreach ($dest as $key => $val)
		{
			$keyPiece = array();
		
			foreach ($sourcePrimaries as $col)
			{
				$keyPiece[] = $val[$col];
			}
		
			$keyvalue = implode(" ", $keyPiece);
			$newdest[$keyvalue] = $val;
		}
		
		
		return $this->sequentialDifference($newsource, $newdest);
	
	
	}
	
	
	
	function sequentialDifference($resource, $redest)
	{
	
		$map = array();
		
		$srcKeys = array_keys($resource);
		$destKeys = array_keys($redest);
		
		$allKeys = differenceMerge($srcKeys, $destKeys);
		
		
		// print_pre($srcKeys); 
		// print_pre($destKeys); 
		// print_pre($allKeys); 
		
	
		foreach ($allKeys as $firstIndex)
		{
		
			if ( array_key_exists($firstIndex, $redest) == false )
			{
				$map[$firstIndex] = "in source";
			}
			else if ( array_key_exists($firstIndex, $resource) == false )
			{
				$map[$firstIndex] = "in dest";
			}
			else if ($redest[$firstIndex] != $resource[$firstIndex])
			{
				$map[$firstIndex] = "changed";
			}
			else
			{
				$map[$firstIndex] = "";
			}
			
			


		}
		
	
		return $map;
	
	}
	
	*/
	
	
	function generateQueries($table, $diffMap, $source)
	{
		$sqls = array();
		
		$primaryKeyData =  $this->retrievePrimaryKeyData('source', $table);
		$primaryKeyColumn = $primaryKeyData[0]['COLUMN_NAME'];

		foreach ($diffMap as $pk => $instruction)
		{
			if ($instruction == "delete")
			{
				$sqls[$pk] = "DELETE FROM $table WHERE $primaryKeyColumn = $pk";
			}
			else if ($instruction == "update")
			{
				$sets = array();
				
				foreach ($source[$pk] as $key => $val)
				{
					if ($key == $primaryKeyColumn)
						continue;
				
					if ($val === null)
						$sets[] = "`$key`=NULL";
					else
						$sets[] = "`$key`='".addslashes($val)."'";
				}				
			
				$sqls[$pk] = "UPDATE $table SET ".implode(", ", $sets)." WHERE $primaryKeyColumn = $pk";
			}
			else if ($instruction == "insert")
			{
				$cols = array();
				$vals = array();
				
				foreach ($source[$pk] as $key => $val)
				{
					$cols[] = "`$key`";
					
					if ($val === null)
						$vals[] = "NULL";
					else
						$vals[] = "'".addslashes($val)."'";
				}				

				$sqls[$pk] = "INSERT INTO $table (".implode(", ", $cols).") VALUES (".implode(", ", $vals).")";
			}
		
		}
	
	
	
		return $sqls;
	
	
	
	}
	
	
	
	
	function tableColumns($dbside, $table)
	{
		
		$db = $this->getDatabase($dbside);
		$fulldb = $this->getSession()->get($dbside.'_database');
		
		$return = $db->returnColumn("
		SELECT isc.COLUMN_NAME
		FROM INFORMATION_SCHEMA.COLUMNS isc
		WHERE isc.TABLE_SCHEMA='$fulldb' AND isc.TABLE_NAME='$table'");
		
		return $return;
		
	}
	
	

}