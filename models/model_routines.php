<?php



class Model_Routines extends ModelObject
{

	function allInDatabase($dbside)
	{
		static $results;
		
		
		if (empty($results[$dbside]))
		{
			$fulldb = $this->getSession()->get($dbside.'_database');

			$db = $this->getDatabase($dbside);
			$query = "SELECT ROUTINE_NAME, ROUTINE_TYPE, ROUTINE_DEFINITION, IS_DETERMINISTIC, SQL_MODE, ROUTINE_COMMENT FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA='$fulldb'";
			$results[$dbside] = $db->returnAssocTable($query);
		}
		
		return $results[$dbside];
	}
	
	
	
	
	
	function structure($dbside, $routine, $ignoreLineEndings = true)
	{
		$returns = $this->allInDatabase($dbside);
		
		foreach ($returns as $return)
		{
			if ($return['ROUTINE_NAME'] == $routine)
			{
				if ($ignoreLineEndings)
				{
					$return['ROUTINE_DEFINITION'] = str_replace("\r\n", "\n", $return['ROUTINE_DEFINITION']);
					$return['ROUTINE_DEFINITION'] = str_replace("\n\r", "\n", $return['ROUTINE_DEFINITION']);
					$return['ROUTINE_MD5'] = md5($return['ROUTINE_DEFINITION']);
					$return['ROUTINE_LENGTH'] = strlen($return['ROUTINE_DEFINITION']);
				}

				return $return;			
			}
				
		
		}
	
		return null;
	}
	
	
	
	function createRoutine($routine)
	{
		$routineDef = $this->structure('source', $routine);
	

		$db = $this->getDatabase('source');
		$results = $db->returnAssocRow("SHOW CREATE ".$routineDef['ROUTINE_TYPE']." $routine");
	
		$inputString = $results['Create '.ucwords(strtolower($routineDef['ROUTINE_TYPE']))];
		
		return preg_replace('/(CREATE) (.*) (PROCEDURE|FUNCTION) (.*)/m', '$1 $3 $4', $inputString); 
	}
	
	
	function dropRoutine($routine)
	{
		$routineDef = $this->structure('dest', $routine);

		$query = "DROP ".$routineDef['ROUTINE_TYPE']." IF EXISTS `$routine`";
		
		return $query;
	}
		
	
	function changeRoutine($routine)
	{
		$return[] = $this->dropRoutine($routine);
		$return[] = $this->createRoutine($routine);

		return $return;
	}
	
	
	
	
	function differenceMap()
	{
	
		$sourceTableIndexes = $this->allInDatabase('source');
		$destinationTableIndexes = $this->allInDatabase('dest');
	
		$justSourceNames = extractColumnFromResult($sourceTableIndexes, "ROUTINE_NAME");
		$justDestNames = extractColumnFromResult($destinationTableIndexes, "ROUTINE_NAME");
	
		$unique = differenceMerge($justSourceNames, $justDestNames);

		$map = array();


		foreach ($unique as $tableview)
		{
			
				
			if (in_array($tableview, $justSourceNames) && in_array($tableview, $justDestNames))
			{
				if ($this->structure('source', $tableview) == $this->structure('dest', $tableview))
					$map[$tableview] = "";
				else
					$map[$tableview] = "resolve";
			}
			else if (in_array($tableview, $justSourceNames))
			{
				$map[$tableview] = "create";
			}
			else
			{
				$map[$tableview] = "drop";
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
				
			$function .= "Routine";
			
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
