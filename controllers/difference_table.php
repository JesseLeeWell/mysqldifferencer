<?php


class Controller_Difference_Table extends Controller
{

	function overview()
	{
	
		$view = $this->getView();
		
		$view->assign('NavigationTitle', 'All Tables');
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		$view->assign('SourceTitle', grabTitle('source', $this->getSession()));
		
	
		$tableModel = $this->getModel('table');
	


		
		$view->assign('compiledTables', $tableModel->differenceMap());
		
		$view->display('table_overview');
	
	}
	
	
	
	
	function table_view()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
		$dbside = Request::getVar('dbside');
				
		$view->assign('NavigationTitle', 'Table: '.$table);

		$view->assign('TableStructure', $this->getModel('table')->structure($dbside, $table));
		$view->assign('Columns', $this->getModel('column')->show($dbside, $table));
		$view->assign('Indexes', $this->getModel('index')->show($dbside, $table));
		
		$view->display('table_view');
		
	}
	

	
	
	function drop()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
				
		$view->assign('sql', $this->getModel('table')->dropTable($table));
		$view->assign('DestinationStructure', $this->getModel('table')->structure('dest', $table));
		
		$view->display('table_resolution');
	
	}	
	
	function rename()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
				
		$view->assign('sql', $this->getModel('table')->renameTable($table));
		$view->assign('DestinationStructure', $this->getModel('table')->structure('dest', $table));
		
		$view->display('table_resolution');
	
	}	

	function create()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
		
		$view->assign('sql', $this->getModel('table')->addTable($table));
		$view->assign('SourceStructure', $this->getModel('table')->structure('source', $table));
		
		$view->display('table_resolution');
	}
	
	
	function recreate()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
		
		$view->assign('sql', $this->getModel('table')->recreateTable($table));
		$view->assign('SourceStructure', $this->getModel('table')->structure('source', $table));
		
		$view->display('table_resolution');
	}
	
	
	
	function resolve()
	{
		$model = $this->getModel('table');
		$table = Request::getVar('table');
		$sql = $model->changetable($table);
		$view = $this->getView();
		$view->assign('SourceStructure', $this->getModel('table')->structure('source', $table));
		$view->assign('DestinationStructure', $this->getModel('table')->structure('dest', $table));
		$view->assign('sql', $sql);
		
		$view->display('table_resolution');
	}
	
	
	
	function resolveall()
	{
		
		$model = $this->getModel('table');
		$sql = $model->resolveAll();
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->display('table_resolution');
	}


}