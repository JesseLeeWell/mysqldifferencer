<?php


class Controller_Difference_View extends Controller
{

	function overview()
	{
	
		$view = $this->getView();
		
		$viewModel = $this->getModel('views');
	
	
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		$view->assign('SourceTitle', grabTitle('source', $this->getSession()));
		
	
		
		$resource = $viewModel->differenceMap();
		
		$view->assign('compiledViews', $resource);
		
		// $view->display('view_overview - Copy');	
		$view->display('view_overview');	
	
	}
	
	
	
	
	function view_view()
	{
		$view = $this->getView();
		$tableview = Request::getVar('view');
		$dbside = Request::getVar('dbside');
				
		$view->assign('NavigationTitle', 'View Structure');
		$view->assign('TableStructure', $this->getModel('views')->structure($dbside, $tableview));
		$view->assign('Indexes', $this->getModel('index')->show($dbside, $tableview));
		$view->assign('Columns', $this->getModel('views')->describe($dbside, $tableview));
		
		$view->display('table_view');
	}


	function create()
	{
		$workingView = Request::getVar('view');
		
		$view = $this->getView();
		$model = $this->getModel('views');
		$statement = $model->createView($workingView);

		$view->assign('sql', $statement);
		$view->assign('SourceStructure', $model->structure('source', $workingView));
	//	$view->assign('DestinationStructure', $model->structure('dest', $workingView));
		
		$view->display('view_resolution');
	
	}
	
	
	function drop()
	{
		$model = $this->getModel('views');
		$workingView = Request::getVar('view');
		$sql = $model->dropView($workingView);
		$view = $this->getView();
		$view->assign('sql', $sql);
//		$view->assign('SourceStructure', $model->structure('source', $workingView));
		$view->assign('DestinationStructure', $model->structure('dest', $workingView));
		
		$view->display('view_resolution');
	
	
	
	}


	function resolve()
	{
		$workingView = Request::getVar('view');
		$model = $this->getModel('views');
		$sql = $model->changeView($workingView);
		
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('SourceStructure', $model->structure('source', $workingView));
		$view->assign('DestinationStructure', $model->structure('dest', $workingView));
		
		$view->display('view_resolution');
	}
	
	
	
	
	function resolveall()
	{
		
		$model = $this->getModel('views');
		$sql = $model->resolveAll();
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->display('view_resolution');
	}

	
	
}