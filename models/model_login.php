<?php



class Model_Login extends ModelObject
{

	
	function databases()
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