<?php



abstract class controller extends StartingObject
{
	function getView()
	{

		static $tpl;
		
		if (empty($tpl))
		{
			$tpl = new Skinner();
			$tpl->setConfig('template_path', 'views');
			$tpl->setConfig('debug', false);
			
			$tpl->assign('PrefetchedDestinationTitle', grabTitle('dest', $this->getSession()));
			$tpl->assign('PrefetchedSourceTitle', grabTitle('source', $this->getSession()));
		}

		return $tpl;		
	
	}
	



}