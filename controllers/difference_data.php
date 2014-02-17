<?php


class Controller_Difference_Data extends Controller
{

	function overview()
	{
	
		$view = $this->getView();
		$model = $this->getModel('data');
		
		$view->assign('NavigationTitle', 'All Data');
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));

		$view->assign('compiledData', $model->differenceMap());
		
		if ( Request::getVar('hidden') )
			$view->assign('preHiddenTables', Request::getVar('hidden'));
		
		$view->display('data_overview');	
	
	}
	
	
	
	function more_options()
	{
	
		
		$table = Request::getVar('table');
		$model = $this->getModel('data');
		
		$map = $model->differenceMap();
		
		$session = $this->getSession();

		$view = $this->getView();
		$view->assign('table', $table);
		$view->assign('NavigationTitle', 'Data Migration Options for <span class="entity">'.$table.'</span>');
		
		$view->assign('rowbyrow', $model->checkPrimaryKeyUsability('source', $table) && $model->checkPrimaryKeyUsability('dest', $table));
		$view->assign('localcopy', $session->get('source_server') == $session->get('dest_server'));
		
		$previousTable = null;
		$nextTable = null;
		$lastIteration = null;
		
		foreach ($map as $wtable => $instruction)
		{
			if ($lastIteration == $table)
			{
				$nextTable = $wtable;
				break;
			}	
			
			if ($wtable != $table)
				$previousTable = $wtable;
			
			$lastIteration = $wtable;
		}
		
		
		if ($previousTable)
			$view->assign('previousTable', '<a href="?task=difference_data.more_options&table='.$previousTable.'">'.$previousTable.'</a>');
			
		if ($nextTable)
			$view->assign('nextTable', '<a href="?task=difference_data.more_options&table='.$nextTable.'">'.$nextTable.'</a>');
		
		
		
		$view->assign('sourceMetaData', $model->metadata('source', $table));
		$view->assign('destMetaData', $model->metadata('dest', $table));
		
		
		$view->display(__FUNCTION__);	
	
	}



	function remotecopy()
	{
		$table = Request::getVar('table');
		$limit = Request::getVar('limit', 150);
		
		$view = $this->getView();
		$statements = $this->getModel('data')->remotecopy($table, $limit);

		$view->assign('sql', $statements);
		
		$view->display('data_fullcopy');
	
	}
	
	function localcopy()
	{
		$table = Request::getVar('table');
		
		$view = $this->getView();
		$statements = $this->getModel('data')->localcopy($table);

		$view->assign('sql', $statements);
		
		$view->display('data_fullcopy');
	
	}
	
	function rowbyrow()
	{
	
		$model = $this->getModel('data');
		$table = Request::getVar('table');
		
		$tableColumns = $model->tableColumns('source', $table);
		
		
		$starting = Request::getVar('starting', 0);
		$limit = Request::getVar('limit', 10);
		$what = Request::getVar('what', $tableColumns);
		$check = Request::getVar('checked');
		$showsome = Request::getVar('showsome');
		
		$sourceData = $model->grab('source', $table, $starting, $limit, $what);
		$destData = $model->grab('dest', $table, $starting, $limit, $what);
		
		$differences = $model->tabularDifferenceMap($table, $sourceData, $destData);
		$newQueries = $model->generateQueries($table, $differences, $sourceData);
		$view = $this->getView();	
		
		$view->assign('table', $table);		
		$view->assign('starting', $starting);		
		$view->assign('tableColumns', $tableColumns);		
		$view->assign('what', $what);		
		$view->assign('limit', $limit);		
		$view->assign('check', $check);		
		$view->assign('showsome', $showsome);		
		$view->assign('grabQuery', $model->grabQuery('dest', $table, $starting, $limit));		
		$view->assign('newQueries', $newQueries);
		$view->assign('SourceData', $sourceData);
		$view->assign('DestinationData', $destData);
		$view->assign('differenceMap', $differences);
		$view->display('data_rowbyrow');
	
	}
	

	
	
	function data_view()
	{
		$view = $this->getView();
		$table = Request::getVar('table');
		$dbside = Request::getVar('dbside');
		$starting = Request::getVar('starting', 0);
		$limit = Request::getVar('limit', 10);
		$model = $this->getModel('data');
				
		$view->assign('NavigationTitle', 'Data in: '.$table);
		$view->assign('data', $model->grab($dbside, $table, $starting, $limit));
		$view->assign('table', $table);		
		$view->assign('dbside', $dbside);		
		$view->assign('starting', $starting);		
		$view->assign('limit', $limit);		
		$view->assign('grabQuery', $model->grabQuery('dest', $table, $starting, $limit));		
		
		$view->display('data_view');
	}
	
}