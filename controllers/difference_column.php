<?php


class Controller_Difference_Column extends Controller
{

	function overview()
	{
		
		$table = Request::getVar("table");
		$columnModel = $this->getModel('column');
		
		$view = $this->getView();
		$view->assign('NavigationTitle', 'Columns of '.$table);
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		$view->assign('table', $table);
		$view->assign('compiledColumns', $columnModel->differenceMap($table));

		$view->display('column_overview');
	}

	
	
	
	function column_view()
	{
	
		$view = $this->getView();
		$table = Request::getVar('table');
		$column = Request::getVar('column');
		$dbside = Request::getVar('dbside');
				
		$view->assign('NavigationTitle', 'Column: '.$table.'.'.$column);
		$view->assign('ColumnStructure', $this->getModel('column')->structure($dbside, $table, $column));
		
		$view->display('column_view');
		
	}
	

	

	function add()
	{
		$table = Request::getVar('table');
		$column = Request::getVar('column');
		
		$view = $this->getView();
		$statement = $this->getModel('column')->addColumn($table, $column);

		$view->assign('sql', $statement);
		$view->assign('SourceStructure', $this->getModel('column')->structure('source', $table, $column));
		
		$view->display('column_resolution');
	
	}
	
	
	function drop()
	{
		$table = Request::getVar('table');
		$column = Request::getVar('column');
		
		$model = $this->getModel('column');
		$sql = $model->dropColumn($table, $column);
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->assign('DestinationStructure', $this->getModel('column')->structure('dest', $table, $column));

		
		$view->display('column_resolution');
	}


	function resolve()
	{
		$table = Request::getVar('table');
		$column = Request::getVar('column');
		
		$model = $this->getModel('column');
		$sql = $model->changeColumn($table, $column);
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('SourceStructure', $this->getModel('column')->structure('source', $table, $column));
		$view->assign('DestinationStructure', $this->getModel('column')->structure('dest', $table, $column));
		
		$view->display('column_resolution');
	}
	
	function resolveall()
	{
		$table = Request::getVar('table');
		
		$model = $this->getModel('column');
		$sql = $model->resolveAll($table);
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('SourceStructure', $this->getModel('table')->structure('source', $table));
		$view->assign('DestinationStructure', $this->getModel('table')->structure('dest', $table));
		
		$view->display('column_resolution');
	}
	
	
	
}