<?php



class Model_Variables extends ModelObject
{
	

	function allInDatabase($dbside, $kind, $search)
	{
		static $result;
		
		
		if (empty($result[$dbside]))
		{
			$fulldb = $this->getSession()->get($dbside.'_database');
		
		
			if ($search)
				$query = "SHOW $kind LIKE '%$search%'";
			else
				$query = "SHOW $kind";
				
			$db = $this->getDatabase($dbside);

			$temp = $db->returnAssocTable($query);

		
		}
		
		
		$return = array();
		
		foreach ($temp as $row)
		{
			$return[$row['Variable_name']] = $row['Value'];
		}
		
		
		return $return;
	}
	
	
	
	

	
}