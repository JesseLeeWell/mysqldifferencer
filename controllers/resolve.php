<?php


class Controller_Resolve extends Controller
{

	function preview()
	{
	
		$view = $this->getView();
	
		$sqls = Arrays::BlackList(Request::getVars(), 'task, going_to_execute, going_to_plain_text');
		
		
		
		if (isset($_POST['going_to_execute']))
		{
			$view->assign('queries', $sqls);
			$view->display(__FUNCTION__);
		}
		else if (isset($_POST['going_to_plain_text']))
		{
			$view->assign('queries', array_values($sqls));
			$view->display('data_row_plain_text');
		}
	
	
	}
	
	
	
	
	private function install_log()
	{
		$db = $this->getDatabase('dest'); 
		$db->returnAffected("CREATE DATABASE IF NOT EXISTS `_mysql_diff`");
		$db->returnAffected("
CREATE TABLE IF NOT EXISTS `_mysql_diff`.`query_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_server` varchar(45) DEFAULT NULL,
  `source_database` varchar(45) DEFAULT NULL,
  `dest_server` varchar(45) DEFAULT NULL,
  `dest_database` varchar(45) DEFAULT NULL,
  `query` text,
  `affected_rows` int(11) DEFAULT NULL,
  `errno` int,
  `error` text,
  `duration` decimal(9,3) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
			");
	
	}
	
	
	
	private function log_query($query, $affected, $duration)
	{
	
	
		$data = Arrays::whiteList($this->getSession()->get(), 'source_server, source_database, dest_server, dest_database');
		$data['query'] = $query;
		$data['affected_rows'] = $affected;
		$data['duration'] = $duration;
		
		$db = $this->getDatabase('dest'); 
		
		if ($db->connection->error != null)
		{
			$data['errno'] = $db->errorNumber;
			$data['error'] = $db->errorString;
		}
	
		$db->insert("`_mysql_diff`.`query_log`", $data);
	
	
	}
	



	function execute()
	{
		$view = $this->getView();
		
		$db = $this->getDatabase('dest'); 

		$sqls = Arrays::BlackList(Request::getVars(), 'task');


		$this->install_log();
		
		set_time_limit(0);
		$starting = microtime(true);
		$timelapse = 0;
		
		$results = array();
		
		foreach ($sqls as $sql)
		{
			$sql = diff_decode($sql);
			
			$tstart = microtime(true);
			
			$results[$sql] = $db->returnAffected($sql);
			
			$duration = microtime(true) - $starting;
			$timelapse += $duration;
			
			$this->log_query($sql, $results[$sql], $duration);
		}
		
		$view->assign('results', $results);
		$view->assign('errors', $db->info);
		$view->assign('NavigationTitle', 'Execute Queries');
		$view->assign('timelapse', $timelapse);

		cache_clear();
	
		$view->display(__FUNCTION__);
	
	}
	
	
	function queriespreview()
	{
		$view = $this->getView();	
		
		$view->assign('sql', array_values($_POST));
		$view->display('data_row_previews');
	
	}	
	
	
	
	function freeform()
	{
		$view = $this->getView();
	
		
		$view->assign('dest_preloaded', Request::getVar('dest_preload', 'SELECT version()'));
		$view->assign('source_preloaded', Request::getVar('source_preload', 'SELECT version()'));
		
		$view->assign('NavigationTitle', 'Free Form Query');
		$view->display(__FUNCTION__);
	}
	
	
	
	function freeformrun()
	{
		$this->freeform();
	
	}

	


}