<?php



class ModelObject extends StartingObject
{

	function itemQuote($item)
	{
		return "'$item'";
	}
	
	
	
	function dataArrayQuoting($data)
	{
		$new = array();
		
		foreach ($data as $v)
		{
			$new[] = "'".addslashes($v)."'";
		}
	
		return implode(", ", $new);
	}
	
	
	function objectArrayQuoting($data)
	{
		$new = array();
		
		foreach ($data as $v)
		{
			$new[] = "`".($v)."`";
		}
	
		return implode(", ", $new);
	}
	
	
	function quotedDatabase($dbside)
	{
		$fulldb = $this->getSession()->get($dbside.'_database');
		return $this->itemQuote($fulldb);
	
	}
	
	
}