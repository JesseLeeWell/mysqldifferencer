<?php


class Controller_Difference_Index extends Controller
{

	function overview()
	{
		$view = $this->getView();
		
		$table = Request::getVar('table');
		$view->assign('NavigationTitle', 'Indexes of '.$table);
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		
		$view->assign('table', $table);
		
		$indexModel = $this->getModel('index');
		
		
		
		
		$resource = $indexModel->differenceMap($table);
		
		
		
		$view->assign('compiledIndexes', $resource);

		
		$view->display('index_overview');
	}
	
	
	
	
	function index_view()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
		$index = Request::getVar('index');
		$dbside = Request::getVar('dbside');
				
		$view->assign('NavigationTitle', 'Index: '.$table.'.'.$index);
		$view->assign('IndexStructure', $this->getModel('index')->structure($dbside, $table, $index));
		
		$view->display('index_view');
	
	}
	




	function add()
	{
		$table = Request::getVar('table');
		$column = Request::getVar('index');
		
		$view = $this->getView();
		$statement = $this->getModel('index')->addIndex($table, $column);

		$view->assign('sql', $statement);
		
		$view->display('index_resolution');
	
	}
	
	
	function drop()
	{
		$model = $this->getModel('index');
		$sql = $model->dropIndex(Request::getVar('table'), Request::getVar('index'));
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->display('index_resolution');
	
	
	
	}


	function resolve()
	{
		$model = $this->getModel('index');
		$table = Request::getVar('table');
		$index = Request::getVar('index');
		$sql = $model->changeIndex($table, $index);
		
		
		
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('SourceStructure', $this->getModel('index')->show('source', $table, $index));
		$view->assign('DestinationStructure', $this->getModel('index')->show('dest', $table, $index));
		
		$view->display('index_resolution');
	
	}
	
	function resolveall()
	{
		$table = Request::getVar('table');
		
		$model = $this->getModel('index');
		$sql = $model->resolveAll($table);
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('SourceStructure', $this->getModel('table')->structure('source', $table));
		$view->assign('DestinationStructure', $this->getModel('table')->structure('dest', $table));
		
		$view->display('column_resolution');
	}
}