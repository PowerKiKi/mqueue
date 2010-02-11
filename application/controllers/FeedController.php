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
    	$mapper = new Default_Model_StatusMapper();
    	if ($this->getRequest()->getParam('user'))
    	{
    		$mapperUser = new Default_Model_UserMapper();
    		$user = $mapperUser->findNickname($this->getRequest()->getParam('user'));
    		if ($user)
    		{
    			$this->view->activity = $mapper->getActivityForUser($user);
    			$this->view->title = $this->view->translate('Activity for %s', array($user->nickname));
    		}
    	}
    	
    	if ($this->getRequest()->getParam('movie'))
    	{
    		$mapperMovie = new Default_Model_MovieMapper();
    		$movie = $mapperMovie->find($this->getRequest()->getParam('movie'));
    		if ($movie)
    		{
    			$this->view->activity = $mapper->getActivityForMovie($movie);
    			$this->view->title = $this->view->translate('Activity for %s', array($movie->getTitle()));
    		}
    	}
    	
    	if (!$this->view->activity)
    	{
			$this->view->activity = $mapper->getActivity();
			$this->view->title = $this->view->translate('Overall activity');
    	}
    }

}

