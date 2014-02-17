<?php


class FrontController extends Controller
{


	function execute()
	{
	
	

		$task = Request::getVar('task');

		$controller = LibStrings::getPiece($task, ".", 0);

		if (empty($controller))	
			$controller = "navigation";


		$function = LibStrings::getPiece($task, ".", 1);

		if (empty($function))	
			$function = "home";


		$classname = "Controller_".$controller;


		require "controllers/".$controller.".php";

		$working = new $classname;
		$working->$function();


		$session = $this->getSession();
		$session->set('LastPage', Request::selfURL());
			
	
	
	}





}