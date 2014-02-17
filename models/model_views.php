<?php



class Model_Views extends ModelObject
{

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
				REPLACE( REPLACE(VIEW_DEFINITION, '/* ALGORITHM=UNDEFINED */ ', '') , '`$fulldb`.', '') as SHORTENED_VIEW_DEFINITION, 
				LENGTH( REPLACE( REPLACE(VIEW_DEFINITION, '/* ALGORITHM=UNDEFINED */ ', '') , '`$fulldb`.', '') ) as VIEW_LENGTH,
				CHECK_OPTION, 
				IS_UPDATABLE
			FROM INFORMATION_SCHEMA.VIEWS 
			WHERE TABLE_SCHEMA='$fulldb'";
			$results[$dbside] = $db->returnAssocTable($query);
		}
		
		return $results[$dbside];
	}
	
	
	
	
	function describe($dbside, $view)
	{
		$fulldb = $this->getSession()->get($dbside.'_database');

		$db = $this->getDatabase($dbside);
		
		$results = $db->returnAssocTable("DESCRIBE `$view`");
	
		return $results;
	}	
	
	function structure($dbside, $view)
	{
		$returns = $this->allInDatabase($dbside);
		
		foreach ($returns as $return)
		{
			if ($return['TABLE_NAME'] == $view)
				return $return;
		
		}
	
		return null;
	}
	
	
	
	function createView($view)
	{
		return $this->changeView($view);		
	}
	
	function showCreateView($view)
	{
		$db = $this->getDatabase('source');
		$query = "SHOW CREATE VIEW `$view`";
		$return = $db->returnAssocRow($query);
		
		return $return['Create View'];		
	}
	
	
	function dropView($view)
	{
		return "DROP VIEW `$view`";	
	}
		
	
	function changeView($view)
	{
	
		$return = $this->showCreateView($view);
		
		$pos = stripos($return, ' as select ');
		$def = "CREATE OR REPLACE VIEW `$view`".substr($return, $pos);
		return $def;
	
	}
	
	
	
	function differenceMap()
	{
	
		$sourceTableIndexes = $this->allInDatabase('source');
		$destinationTableIndexes = $this->allInDatabase('dest');
	
		$justSourceNames = extractColumnFromResult($sourceTableIndexes, "TABLE_NAME");
		$justDestNames = extractColumnFromResult($destinationTableIndexes, "TABLE_NAME");
	
		$unique = differenceMerge($justSourceNames, $justDestNames);

		$map = array();

		foreach ($unique as $tableview)
		{
		
			$sourceStructure = $this->structure('source', $tableview);
			$destStructure = $this->structure('dest', $tableview);
			
			if (in_array($tableview, $justSourceNames) == false)
			{
				$map[$tableview] = "drop";
			}
			else if (in_array($tableview, $justDestNames) == false)
			{
				$map[$tableview] = "create";
			}
			else if ($sourceStructure != $destStructure)
			{
				$map[$tableview] = "resolve";
			}	
			else
			{
				$map[$tableview] = "";
			}

		}	
	
		return $map;
	
	}
	
	function resolveAll()
	{
	
		$statements = array();
		
		
		$map = $this->differenceMap();
		
		foreach ($map as $table => $instruction)
		{
			
			if (empty($instruction))
				continue;
			
			
			if ($instruction == 'resolve')
				$function = "change";
			else 
				$function = $instruction;
				
			$function .= "View";
			
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