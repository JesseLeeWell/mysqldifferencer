<?php



class Model_Table extends ModelObject
{

	

	// Show the full structure of a given table

	function showfull($dbside)
	{
		static $result;
		
		if (empty($result[$dbside]))
		{
	
			$fulldb = $this->getSession()->get($dbside.'_database');
			$db = $this->getDatabase($dbside);
			
			$temp = cache_get(__CLASS__ . __FUNCTION__ . file_name_for_cache($db));
			
			if (empty($temp))
			{
				$temp = $db->returnAssocTable("SELECT 
					it.TABLE_NAME,
					it.ENGINE, 
	--				it.TABLE_COMMENT, 
					it.ROW_FORMAT, 
					ic.CHARACTER_SET_NAME,
					ic.COLLATION_NAME
				FROM 
					INFORMATION_SCHEMA.TABLES it 
				JOIN INFORMATION_SCHEMA.COLLATIONS ic ON it.TABLE_COLLATION = ic.COLLATION_NAME
				WHERE it.TABLE_SCHEMA='$fulldb'");
				
				cache_set(__CLASS__ . __FUNCTION__ . file_name_for_cache($db), $temp);

			}
			
			$result[$dbside] = $temp;
		}
		
		
		return $result[$dbside];
	}
	
	

	
	// Show the full structure of a given table

	function structure($dbside, $table)
	{
		$allTables = $this->showfull($dbside);
		
		foreach ($allTables as $oneTable)
		{
			if ($oneTable['TABLE_NAME'] == $table)
				return $oneTable;
		
		}

		return null;
	}
	
	
	
	function renameTable($table)
	{
		$oldTable = LibStrings::getPiece($table, " : ", -1);
//		$newTable = LibStrings::getPiece($table, " : ", 1);
	
		$structure = $this->getModel('column')->show('dest', $oldTable);
		
		$destinationTableFull = $this->showfull('source');
		$destinationTable = extractColumnFromResult($destinationTableFull, 'TABLE_NAME');

		$newtable = null;
		
		
		foreach ($destinationTable as $tables)
		{
			$thisstructure = $this->getModel('column')->show('source', $tables);
			
			if ($structure == $thisstructure)
			{
				$newtable = $tables;
			}
		}
		
		
		return "RENAME TABLE $oldTable TO $newtable";
	}
	
	
	
	
	function recreateTable($oldTable)
	{
		$createStatement = $this->addTable($oldTable);
		
		$newTable = $oldTable."__mysql_diff_".time();
		
		$newTableQuoted = "`$newTable`";
		$oldTableQuoted = "`$oldTable`";
		
		$createStatement = str_replace("CREATE TABLE ".$oldTableQuoted, "CREATE TABLE ".$newTableQuoted, $createStatement);
		
		$allColumnsSource = Arrays::extractColumn($this->getModel('column')->show('source', $oldTable), "COLUMN_NAME");
		$allColumnsDest = Arrays::extractColumn($this->getModel('column')->show('dest', $oldTable), "COLUMN_NAME");
		
		$allColumns = array_intersect($allColumnsSource, $allColumnsDest);
		
		$selects = $this->objectArrayQuoting($allColumns);
		
		
		$sqls = array();
		$sqls[] = $createStatement;
		$sqls[] = "INSERT INTO $newTableQuoted ($selects) \nSELECT $selects \nFROM $oldTableQuoted";
		$sqls[] = "DROP TABLE $oldTableQuoted";
		$sqls[] = "RENAME TABLE $newTableQuoted TO $oldTableQuoted";
	
		return $sqls;
	
	}
	
	
	
	
	function multipleAlterTable($table)
	{
		$change[] = $this->changeTable($table);
		$change[] = $this->getModel('column')->resolveAll($table);
		$change[] = $this->getModel('index')->resolveAll($table);
	
	
		$leader = "ALTER TABLE `$table` ";
	
		$newChange = array();
	
		foreach ($change as $cha)
		{
			$cha = trim($cha); 
		
			if (empty($cha))
				continue;
		
			$newChange[] = str_replace($leader, "", $cha);
		}
		
		return $leader.implode(", \n", $newChange);
		
	}
	
	
	
	
	function addTable($table)
	{
		$sourcedb = $this->getDatabase('source');
		$definition = $sourcedb->returnAssocRow("SHOW CREATE TABLE `$table`");
		
		$truDef = preg_replace('/ AUTO_INCREMENT=(\d+)/', '', $definition['Create Table']);
		
//		$return[] = "DROP TABLE IF EXISTS `$table`";
		$return[] = $truDef;
		
		return $truDef;
	}
	
	
	function dropTable($table)
	{
		return "DROP TABLE `$table`";
	
	}
	
	
	function changeTable($table)
	{
		$src_structure = $this->structure('source', $table);
		$dest_structure = $this->structure('dest', $table);
	
		$pieces = array();
		
		if ($src_structure['ROW_FORMAT'] != $dest_structure['ROW_FORMAT'])
			$pieces[] = "ROW_FORMAT = ".$src_structure['ROW_FORMAT']."";

		// if ($src_structure['CHECKSUM'] != $dest_structure['CHECKSUM'])
			// $pieces[] = "CHECKSUM = ".$src_structure['CHECKSUM']."";

		// if ($src_structure['TABLE_COMMENT'] != $dest_structure['TABLE_COMMENT'])
			// $pieces[] = "COMMENT = '".$src_structure['TABLE_COMMENT']."'";
			
		if ($src_structure['ENGINE'] != $dest_structure['ENGINE'])
			$pieces[] = "ENGINE = '".$src_structure['ENGINE']."'";
			
		if ($src_structure['CHARACTER_SET_NAME'] != $dest_structure['CHARACTER_SET_NAME'])
			$pieces[] = "DEFAULT CHARACTER SET ".$src_structure['CHARACTER_SET_NAME']."";
			
		if ($src_structure['COLLATION_NAME'] != $dest_structure['COLLATION_NAME'])
			$pieces[] = "COLLATE ".$src_structure['COLLATION_NAME']."";



		if (empty($pieces))
			return "";
			
		$statement = "ALTER TABLE `$table` ".implode(" ", $pieces);
	
		return $statement;
	}
	
	
	
	
	function differenceMap()
	{
		$sourceTableFull = $this->showfull('source');
		$destinationTableFull = $this->showfull('dest');

		$sourceTable = extractColumnFromResult($sourceTableFull, 'TABLE_NAME');
		$destinationTable = extractColumnFromResult($destinationTableFull, 'TABLE_NAME');

		$unique = differenceMerge($sourceTable, $destinationTable);
		
		
		$resource = array();


		foreach ($unique as $table)
		{
			
			if (in_array($table, $sourceTable) == false)
			{
				$resource[$table] = "drop";
			}
			else if (in_array($table, $destinationTable) == false)
			{			
				$resource[$table] = "create";
			}
			else
			{
				$pieces = array();
			
				if ($this->structure('source', $table) != $this->structure('dest', $table))
					$pieces[] = "resolve";
					
				if ($this->getModel('column')->show('source', $table) != $this->getModel('column')->show('dest', $table))
					$pieces[] = "columns";

				if ($this->getModel('index')->show('source', $table) != $this->getModel('index')->show('dest', $table))
					$pieces[] = "indexes";
					
				$resource[$table] = implode(", ", $pieces);
			}

		}
		
		
//		print_pre($resource); exit; 
		
		// Detect renames..
		
		foreach ($resource as $table => $instruction)
		{
			if ($instruction != 'create')
				continue;
				
			$createStructure = $this->getModel('column')->show('source', $table);
			
			$matches = 0;
		
			foreach ($resource as $subt => $subins)
			{
				
				if ($subins != 'drop')
					continue;
					
					
				$dropStructure = $this->getModel('column')->show('dest', $subt);				
				
				
				if ($dropStructure == $createStructure)
				{
					$keyname = $table." : ".$subt;
					$matches++;
					
					// print_pre($keyname); 
					// print_pre($dropStructure); 
					
				}
				
			
			}
			
			if ($matches > 1)
			{
				// print_pre('Multiple matches');
				// print_pre($keyname); 
			}
			else if ($matches == 1)
			{
				
				// print_pre($table); 
				// print_pre($subt); 
				$destItem = LibStrings::getPiece($keyname, " : ", 0);
				$sourceItem = LibStrings::getPiece($keyname, " : ", 1);
				
				$resource[$keyname] = "rename";
				unset($resource[$destItem]);
				unset($resource[$sourceItem]);
				
				// print_pre("renamed confirmed"); 
				// print_pre($resource); 
			}
		
		}
		
//			print_pre($resource); exit;

		return $resource;
	
	}
	
	function resolveAll()
	{
	
		$statements = array();
		
		
		$map = $this->differenceMap();
		
		foreach ($map as $table => $instructionFull)
		{
			
			$instruction = LibStrings::getPiece($instructionFull, " : ", 0);
			
			if (empty($instruction))
				continue;
			
			
			if ($instruction == 'create')
				$function = "add";
			else if ($instruction == 'resolve')
				$function = "change";
			else if ($instruction == 'drop')
				$function = $instruction;
			else if ($instruction == 'rename')
				$function = $instruction;
			else if ($instruction)
				$function = 'multipleAlter';
				
			$function .= "Table";
			
			$statement = $this->$function($table);
			
			if (is_array($statement))
			{
				$statements = array_merge($statements, $statement);
			}
			else
			{
				$statements[] = $statement;
			}
		}
		
		
		return $statements;
	
	}
	

}