<?php



class Model_Index extends ModelObject
{
	

	function allInDatabase($dbside)
	{
		static $result;
		
		
		if (empty($result[$dbside]))
		{
			$fulldb = $this->getSession()->get($dbside.'_database');
		
			$query = "
			
			
		SELECT allindexes.*, tc.CONSTRAINT_NAME, tc.CONSTRAINT_TYPE  FROM
		(
			SELECT 
				TABLE_NAME, 
				INDEX_NAME, 
				NON_UNIQUE, 
				COMMENT, 
				GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX ASC SEPARATOR ', ') AS COLUMN_NAMES,
				INDEX_TYPE,
				CONCAT(TABLE_NAME, '.', INDEX_NAME) as FULL_INDEX_NAME
			FROM INFORMATION_SCHEMA.STATISTICS 
			WHERE TABLE_SCHEMA='$fulldb' 
			GROUP BY TABLE_NAME, INDEX_NAME
		)
		as allindexes
		LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc ON allindexes.INDEX_NAME = tc.CONSTRAINT_NAME AND tc.TABLE_SCHEMA='$fulldb' AND allindexes.TABLE_NAME = tc.TABLE_NAME
		ORDER BY FULL_INDEX_NAME
	
		";
			$db = $this->getDatabase($dbside);


			$temp = cache_get(__CLASS__ . __FUNCTION__ . file_name_for_cache($db));
			
			if (empty($temp))
			{

				$temp = $db->returnAssocTable($query);
			
				cache_set(__CLASS__ . __FUNCTION__ . file_name_for_cache($db), $temp);

			}
		

			$result[$dbside] = $temp;
		}
		
		return $result[$dbside];
	}
	
	
	function structure($dbside, $table, $index)
	{
		
		$thesetables = $this->show($dbside, $table);
		
		foreach ($thesetables as $onetable)
		{
			if ($onetable['TABLE_NAME'] == $table && $onetable['INDEX_NAME'] == $index)
				return ($onetable);
		
		}
	
		return null;
	}
	

	function show($dbside, $table)
	{
		$allTables = $this->allInDatabase($dbside);
		
		$rebuiltResult = array();
		
		foreach ($allTables as $oneTable)
		{
			if ($oneTable['TABLE_NAME'] == $table)
				$rebuiltResult[] = $oneTable;
		
		}

		return $rebuiltResult;
	}
	

	
	function dropIndex($table, $column)
	{
		return ("ALTER TABLE `$table` DROP INDEX `$column`");

	}
	
	
	function changeIndex($table, $column)
	{
		$sqls[] = $this->dropIndex($table, $column);
		$sqls[] = $this->addIndex($table, $column);

		return $sqls;
	}
	
	
	
	function addIndex($table, $index)
	{


		$structure = $this->structure('source', $table, $index);
		$type = $structure['CONSTRAINT_TYPE'] ? $structure['CONSTRAINT_TYPE'] : $structure['INDEX_TYPE'];
		$type = "$type INDEX";
		$type = str_replace("BTREE ", "", $type);
		$type = str_replace("PRIMARY KEY INDEX", "PRIMARY KEY", $type);
		
		$ipcollection = "`".str_replace(", ", "`, `", $structure['COLUMN_NAMES'])."`";
		
		if ($type == "PRIMARY KEY")
			$statement = "ALTER TABLE `$table` ADD $type (".$ipcollection.")";
		else
			$statement = "ALTER TABLE `$table` ADD $type `$index` (".$ipcollection.")";
		
		return $statement;
	
	}
	
	
	
	
	function differenceMap($table)
	{
	
		$sourceTableIndexes = $this->show('source', $table);
		$destinationTableIndexes = $this->show('dest', $table);
	
		$justSourceNames = extractColumnFromResult($sourceTableIndexes, "INDEX_NAME");
		$justDestNames = extractColumnFromResult($destinationTableIndexes, "INDEX_NAME");
	
		$unique = differenceMerge($justSourceNames, $justDestNames);

		$map = array();

		foreach ($unique as $index)
		{
		
			$sourceStructure = $this->structure('source', $table, $index);
			$destStructure = $this->structure('dest', $table, $index);
			
			if (in_array($index, $justSourceNames) == false)
			{
				$map[$index] = "drop";
			}
			else if (in_array($index, $justDestNames) == false)
			{
				$map[$index] = "add";
			}
			else if ($sourceStructure != $destStructure)
			{
				$map[$index] = "resolve";
			}	
			else
			{
				$map[$index] = "";
			}

		}	
	
		return $map;
	
	
	}
	
	
	
	function resolveAll($table)
	{
	
		$statements = array();
		
		$starter = "ALTER TABLE `$table` ";
		
		$map = $this->differenceMap($table);
		
		foreach ($map as $column => $instruction)
		{
			
			if (empty($instruction))
				continue;
			
			$function = ($instruction == 'resolve') ? 'change' : $instruction;
			$function .= "Index";
			
			$statements[] = str_replace($starter, "", $this->$function($table, $column));
		
		}
		
		if (empty($statements))
			return "";

		return $starter . "\n" . implode(",\n", $statements);
	
	}	
	
}