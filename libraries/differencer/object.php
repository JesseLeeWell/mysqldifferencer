<?php



class StartingObject
{
	
	
	function getSession($name = 'main')
	{
		static $s;
		
		session_set_cookie_params( (365*1440*60) );
	
		if (empty($s[$name]))
			$s[$name] = new PartitionedSession($name);
		
		return $s[$name];
	
	}

	function getDatabase($type)
	{
		$session = $this->getSession();
		return getdb($type, $session);

	}

	
	

	
	function getModel($name = null)
	{
		$class = ($name) ? 'Model_'.$name : 'Model';
	
		require_once 'models/'.strtolower($class).'.php';
	
	
		$new = new $class;
		return $new;
	
	
	}	
	
}