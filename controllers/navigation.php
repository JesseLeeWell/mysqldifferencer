<?php


class Controller_Navigation extends Controller
{

	function home()
	{
		$vars = $this->getSession()->get();

		if (count($vars) > 0)
		{
			$view = $this->getView();
	
			$view->display(__FUNCTION__);
		}
		else
			$this->login();
	}


	function swap()
	{
		$vars = $this->getSession()->get();
		$session = $this->getSession();
		
		$formvars = array('server', 'database', 'user', 'password');
		
		
		foreach ($formvars as $input)
		{
			$src = $session->get('source_'.$input);
			$dest = $session->get('dest_'.$input);
			
			$session->set('dest_'.$input, $src);
			$session->set('source_'.$input, $dest);
		}
		
		
		Request::redirect($session->get('LastPage'));
		exit;
		
	}

	
	
	function login()
	{
		$view = $this->getView();
		$vars = $this->getSession()->get();
		
		
		$previousConnections = $this->getSession()->get('previousConnections');
		
		
		if (is_array($previousConnections))
		{
			$previousConnectionsGood["- Choose One -"] = null;
			
			arsort($previousConnections);
			
			foreach ($previousConnections as $value => $popularity)
			{
				$key = Arrays::removeEmptyElements( json_decode($value));
//				unset($key['password']);
			
				$previousConnectionsGood[$key['server']][$key['user']] = $value;
			}
			
			$view->assign('previousConnectionsGood', $previousConnectionsGood);
		}
		else
		{
			$view->assign('previousConnectionsGood', array() );
		}
		
		
		if (is_array($vars))
			foreach ($vars as $key => $val)
				$view->assign($key, $val);


	
		$view->display(__FUNCTION__); 
	
	}

	function credentialsave()
	{
	
		$vars = $_POST;
				
		
		foreach ($vars as $key => $val)
		{
			$this->getSession()->set($key, $val);
		}
	
		$rekey['source_server'] = 'server';
		$rekey['source_user'] = 'user';
		$rekey['source_password'] = 'password';
		$rekey['dest_server'] = 'server';
		$rekey['dest_user'] = 'user';
		$rekey['dest_password'] = 'password';
	
	
		$srcConn = Arrays::whiteList($vars, 'source_server, source_user, source_password');
		$destConn = Arrays::whiteList($vars, 'dest_server, dest_user, dest_password');
	
		$previousConns = $this->getSession()->get('previousConnections');
		$newSource = json_encode(Arrays::alternateKeyNames($srcConn, $rekey));
		$newDest = json_encode(Arrays::alternateKeyNames($destConn, $rekey));
		

		if (isset($previousConns[$newSource]))
		{
			$previousConns[$newSource]++;
		}
		else
		{
			$previousConns[$newSource] = 0;		
		}
		
		if (isset($previousConns[$newDest]))
		{
			$previousConns[$newDest]++;
		}
		else
		{
			$previousConns[$newDest] = 0;		
		}
				
		
		
		$this->getSession()->set('previousConnections', $previousConns);
	

		cache_clear();
	
		Request::redirect("?task=navigation.home");
	
	}
	
	
	function databaselist()
	{
		$dbside = Request::getVar('dbside');
		$server = Request::getVar($dbside."_server");
		$user = Request::getVar($dbside."_user");
		$pass = Request::getVar($dbside."_password");
		
		$session = $this->getSession();
		$preselection = $session->get($dbside.'_database');

		
		
		ob_start();
		
		$connection = new DBO_MySQLi($server, $user, $pass);
		
		ob_end_clean();
		
		$errors = $connection->connection->connect_error;
		
		
		if (empty($errors))
			$returns = $connection->returnColumn("SHOW DATABASES");
		else
			$returns = array();
		
		$returns = array_flip($returns);
		$returns = Arrays::BlackList($returns, "performance_schema, mysql, information_schema");
		$returns = array_flip($returns);
			
		$obj = new stdclass;
		$obj->message = $errors;
		
		if ($returns)
			$obj->content = '<select name="'.$dbside.'_database">'.HtmlElement::SelectOptions($returns, $preselection, true).'</select>';
		else
			$obj->content = '(2) '.LibStrings::truncate($errors, 25);
		
		exit ( json_encode($obj) );
	}
	
	
	
	
	private function cmdescape($cmd)
	{
		return preg_replace('/\W/', '\\\$0', $cmd);
	}
	
	
	private function escapedSessionParameter($index)
	{
		$session = $this->getSession();
		return $this->cmdescape($session->get($index));
	}
	
	
	function backup()
	{
		$view = $this->getView();

		
		$destconnection = "-h".$this->escapedSessionParameter('dest_server')." -u".$this->escapedSessionParameter('dest_user')." -p".$this->escapedSessionParameter('dest_password');
		$sourceconnection = "-h".$this->escapedSessionParameter('source_server')." -u".$this->escapedSessionParameter('source_user')." -p".$this->escapedSessionParameter('source_password');
		
		$destdb = $this->escapedSessionParameter('dest_database');
		$sourcedb = $this->escapedSessionParameter('source_database');
		
		$command_dest = "mysqldump --routines $destconnection $destdb > ${destdb}_`date '+%Y%m%d_%H%M'`.sql";
		$command_source = "mysqldump --routines $sourceconnection $sourcedb > ${sourcedb}_`date '+%Y%m%d_%H%M'`.sql";
		
		
		$sourcedump = "${sourcedb}_temp.sql";
		
		$restoreCommands[] = "mysqldump --routines $sourceconnection $sourcedb > $sourcedump";
		$restoreCommands[] = "mysql $destconnection $destdb < $sourcedump";
		$restoreCommands[] = "rm $sourcedump";
		
		$restore_short = "mysqldump --routines $sourceconnection $sourcedb | mysql $destconnection $destdb";
		
		
		$view->assign('backup_command_dest', $command_dest);
		$view->assign('backup_command_source', $command_source);
		$view->assign('restore_command', implode("\n", $restoreCommands));
		$view->assign('restore_command_short', $restore_short);
		
		$view->display(__FUNCTION__);
	
	}
	
	
	
	


}