<?php


class Controller_Difference_Routine extends Controller
{

	function overview()
	{
	
		$view = $this->getView();
		
		$routineModel = $this->getModel('routines');
	
	
		$view->assign('NavigationTitle', 'All Routines');
		$view->assign('DestinationTitle', grabTitle('dest', $this->getSession()));
		$view->assign('SourceTitle', grabTitle('source', $this->getSession()));
		
	
		$resource = $routineModel->differenceMap();
		
		
		$view->assign('compiledRoutines', $resource);
		
		$view->display('routine_overview');	
	
	}



	function create()
	{
		$tableviewview = Request::getVar('routine');
		
		$view = $this->getView();
		$statement = $this->getModel('routines')->createRoutine($tableviewview);

		$view->assign('sql', $statement);
		$view->assign('SourceStructure', $this->getModel('routines')->structure('source', $tableviewview));
		
		$view->display('routine_resolution');
	
	}
	
	
	function drop()
	{
		$model = $this->getModel('routines');
		$sql = $model->dropRoutine(Request::getVar('routine'));
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->display('routine_resolution');
	
	
	
	}


	function resolve()
	{
		$model = $this->getModel('routines');
		$routine = Request::getVar('routine');
		$sql = $model->changeRoutine($routine);
		$view = $this->getView();
		$view->assign('sql', $sql);
		$view->assign('DestinationStructure', $this->getModel('routines')->structure('dest', $routine));
		$view->assign('SourceStructure', $this->getModel('routines')->structure('source', $routine));
		$view->assign('NavigationTitle', 'Routine Resolve');
		$view->display('routine_resolution');
	
	
	
	}
	
	function routine_view()
	{
		$view = $this->getView();
		$routine = Request::getVar('routine');
		$dbside = Request::getVar('dbside');
				
		$view->assign('NavigationTitle', 'Routine Structure');
		$view->assign('LocationTitle', grabTitle($dbside, $this->getSession()));
		$view->assign('RoutineStructure', $this->getModel('routines')->structure($dbside, $routine));
		
		$view->display('routine_view');
	}
	
	
	
	
	function resolveall()
	{
		
		$model = $this->getModel('routines');
		$sql = $model->resolveAll();
		$view = $this->getView();
		$view->assign('sql', $sql);
		
		$view->display('routine_resolution');
	}

	
}