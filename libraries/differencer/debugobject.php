<?php


class DebugObject extends StartingObject
{


	function dblog($dbside)
	{
		$db = $this->getDatabase($dbside);
				
		return $db->info;
	
	
	
	
	}










}
