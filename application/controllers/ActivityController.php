<?php

class ActivityController extends Zend_Controller_Action
{

    public function init()
    {
		// Init the Context Switch Action helper
		$contextSwitch = $this->_helper->contextSwitch();

		// Add the new context
		$contextSwitch->setContexts(array(
				'atom' => array(
					'suffix'  => 'atom'),
				'rss' => array(
					'suffix'  => 'rss'),
			));
			
		$contextSwitch->addActionContext('index', 'atom')->initContext();
    }

    public function indexAction()
    {
    	// By default we show overall activity
    	$item = null;
    	$this->view->title = $this->view->translate('Overall activity');
    	
    	// Try to show user's actitvity
    	if ($this->getRequest()->getParam('user'))
    	{
    		$item = Default_Model_UserMapper::find($this->getRequest()->getParam('user'));
    		if ($item)
    		{
    			$this->view->title = $this->view->translate('Activity for %s', array($item->nickname));
    		}
    	}

    	// Try to show movie's actitvity
    	if ($this->getRequest()->getParam('movie'))
    	{
    		$item = Default_Model_MovieMapper::find($this->getRequest()->getParam('movie'));
    		if ($item)
    		{
    			$this->view->title = $this->view->translate('Activity for %s', array($item->getTitle()));
    		}
    	}
		
		// Store perPage option in session
		$perPage = 25;
		$session = new Zend_Session_Namespace();
		if (isset($session->perPage)) $perPage = $session->perPage;
		if ($this->_getParam('perPage')) $perPage = $this->_getParam('perPage');
		$session->perPage = $perPage;
		
		// Always send much more data via Atom feed
		if ($this->_helper->contextSwitch()->getCurrentContext() == 'atom')
		{
			$perPage = 200;
		}
		
		$this->view->activity = Zend_Paginator::factory(Default_Model_StatusMapper::getActivityQuery($item));
		$this->view->activity->setCurrentPageNumber($this->_getParam('page'));
		$this->view->activity->setItemCountPerPage($perPage);
    }

}

