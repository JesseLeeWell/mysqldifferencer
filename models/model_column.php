<?php



class Model_Column extends ModelObject
{
	// Show all columns of a given database
	
	function allInDatabase($dbside)
	{
		static $results;
		
		
		if (empty($results[$dbside]))
		{		
			$fulldb = $this->getSession()->get($dbside.'_database');

		
			$db = $this->getDatabase($dbside);
			$query = "
			SELECT 
				TABLE_NAME,
				COLUMN_NAME,
				COLUMN_DEFAULT, 
				IS_NULLABLE, 
				COLUMN_TYPE, 
				CHARACTER_MAXIMUM_LENGTH, 
				NUMERIC_PRECISION, 
				EXTRA, 
				COLUMN_COMMENT,
				CHARACTER_SET_NAME,
				COLLATION_NAME
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE TABLE_SCHEMA='$fulldb'
			ORDER BY ORDINAL_POSITION ASC";
				

			$temp = cache_get(__CLASS__ . __FUNCTION__ . file_name_for_cache($db));
			
			if (empty($temp))
			{

				$temp = $db->returnAssocTable($query);
			
				cache_set(__CLASS__ . __FUNCTION__ . file_name_for_cache($db), $temp);

			}
			
			
			$results[$dbside] = $temp;
		}
	
	
		return $results[$dbside];
	
	
	
	}
	
	
	

	function show($dbside, $table)
	{
		$allTables = $this->allInDatabase($dbside);
		
		$rebuiltResult = array();
		
		foreach ($allTables as $oneTable)
		{
			if ($oneTable['TABLE_NAME'] == $table)
				$rebuiltResult[] = Arrays::blacklist($oneTable, 'TABLE_NAME');
		
		}

		return $rebuiltResult;
	}
	

	// Show the full structure of a given column

	function structure($dbside, $table, $column)
	{
		$allTables = $this->show($dbside, $table);
		
		foreach ($allTables as $oneTable)
		{
			if ($oneTable['COLUMN_NAME'] == $column)
			{
				return Arrays::blacklist($oneTable, 'TABLE_NAME, COLUMN_NAME');
			}
		}

		return null;
	}
	
	
	function addColumn($table, $column)
	{
		$db = $this->getDatabase('source');
	
		$columns = $db->returnAssocTable("DESCRIBE $table");
		

		$previousColumn = null;
		$def = null;
		
		foreach ($columns as $val)
		{
			if ($val['Field'] != $column)
			{
				if (empty($def))
					$previousColumn = $val['Field'];
			}
			else
			{
				$def = $this->structure('source', $table, $column);
			}
		}
	
		$position = ($previousColumn) ? "AFTER `$previousColumn`" : "FIRST";
		$extra = ($def['EXTRA']) ? $def['EXTRA'] . " PRIMARY KEY": "";

		$allowNull = $def['IS_NULLABLE'] == 'NO' ? 'NOT NULL' : 'NULL';
		
		if ($def['COLUMN_DEFAULT'] === null && $allowNull == 'NOT NULL')
			$default = "";
		else if ($def['COLUMN_DEFAULT'] === null && $allowNull == 'NULL')
			$default = "DEFAULT NULL";
		else
			$default = "DEFAULT '".addslashes($def['COLUMN_DEFAULT'])."'";
		
		
		$segments = array();
		
		if ($def['CHARACTER_SET_NAME'])
			$segments[] = "CHARACTER SET '".$def['CHARACTER_SET_NAME']."'";
			
		if ($def['COLLATION_NAME'])
			$segments[] = "COLLATE '".$def['COLLATION_NAME']."'";

		$segments[] = $allowNull;
		
		if ($default)
			$segments[] = $default;
		
		if ($extra)
			$segments[] = $extra;		

		if ($position)
			$segments[] = $position;


		$statement = "ALTER TABLE `$table` ADD COLUMN `$column` ". $def['COLUMN_TYPE'] . " " . implode(" ", $segments);
		
		return $statement;
	
	}
	
	
	function changeColumn($table, $column)
	{
		$sql = $this->addColumn($table, $column);
		
		return str_replace("ADD COLUMN", "MODIFY COLUMN", $sql);
	}
	
	function dropColumn($table, $column)
	{
		return ("ALTER TABLE `$table` DROP COLUMN `$column`");
	}
	
	
	
	
	function differenceMap($table)
	{
		$sourceTableFull = $this->show('source', $table);
		$destinationTableFull = $this->show('dest', $table);
	
	
		$sourceTable = extractColumnFromResult($sourceTableFull, 'COLUMN_NAME');
		$destinationTable = extractColumnFromResult($destinationTableFull, 'COLUMN_NAME');
				
		$unique = differenceMerge($sourceTable, $destinationTable);
		
		$map = array();
		
		foreach ($unique as $column)
		{
			
			if (in_array($column, $sourceTable) && in_array($column, $destinationTable))
			{
				if ($this->structure('source', $table, $column) == $this->structure('dest', $table, $column))
					$map[$column] = "";
				else
					$map[$column] = "resolve";
			}
			else if (in_array($column, $sourceTable))
			{
				$map[$column] = "add";
			}
			else
			{
				$map[$column] = "drop";
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
			$function .= "column";
			
			$statements[] = str_replace($starter, "", $this->$function($table, $column));
		
		}
		
		if (empty($statements))
			return "";

		return $starter . "\n" . implode(",\n", $statements);
	
	}
	
	

}