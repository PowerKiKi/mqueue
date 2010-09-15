<?php

class FeedController extends Zend_Controller_Action
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
			
		$contextSwitch->addActionContext('index', 'atom')
		->initContext();
    }

    public function indexAction()
    {
    	if ($this->getRequest()->getParam('user'))
    	{
    		$user = Default_Model_UserMapper::find($this->getRequest()->getParam('user'));
    		if ($user)
    		{
    			$this->view->activity = Default_Model_StatusMapper::getActivityForUser($user);
    			$this->view->title = $this->view->translate('mQueue - Activity for %s', array($user->nickname));
    		}
    	}
    	
    	if ($this->getRequest()->getParam('movie'))
    	{
    		$movie = Default_Model_MovieMapper::find($this->getRequest()->getParam('movie'));
    		if ($movie)
    		{
    			$this->view->activity = Default_Model_StatusMapper::getActivityForMovie($movie);
    			$this->view->title = $this->view->translate('mQueue - Activity for %s', array($movie->getTitle()));
    		}
    	}
		
    	if (!isset($this->view->activity))
    	{
			$this->view->activity = Default_Model_StatusMapper::getActivity();
			$this->view->title = $this->view->translate('mQueue - Overall activity');
    	}
    }

}

