<?php


class Controller_Difference_Variables extends Controller
{

	function overview()
	{
	
		$view = $this->getView();
		
		$viewModel = $this->getModel('variables');
	
	
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		$view->assign('SourceTitle', grabTitle('source', $this->getSession()));
		
		
		$search = Request::getVar('search');
		$kind = Request::getVar('kind', 'VARIABLES');
		
		$sourceTableIndexes = $viewModel->allInDatabase('source', $kind, $search);
		$destinationTableIndexes = $viewModel->allInDatabase('dest', $kind, $search);

		
		$kinds[] = "VARIABLES";
		$kinds[] = "STATUS";
		
		$view->assign('kindList', $kinds);
		
		
		$view->assign('sourceVariables', $sourceTableIndexes);
		$view->assign('destVariables', $destinationTableIndexes);
		
		$view->display('variable_overview');	
	
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



	
	
}