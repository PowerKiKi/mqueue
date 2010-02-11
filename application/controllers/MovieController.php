<?php

class MovieController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
    	$form = new Default_Form_Filter();
    	$this->view->formFilter = $form;

    	// Initialize session
		$session = new Zend_Session_Namespace();
    	if (!isset($session->filter))
    		$session->filter = array();
    	
    	// If want to clear the filter do so, otherwise try to validate it
    	if ($this->_getParam('clear', false))
    	{
    		$session->filter = array();
    	}
    	else
    	{
	    	$filter = array_merge($session->filter, $this->getRequest()->getParams()); 
	    	
			// Store the filter in session if it's valid
			if ($form->isValid($filter))
			{
				$session->filter = $filter;
			}
    	}

    	// Find the correct filtered user
		$idUser = 0;
		if (isset($session->filter['filterUser']) && (integer)$session->filter['filterUser'] > 0)
			$idUser = (integer)$session->filter['filterUser'];
		elseif (isset($session->idUser))
			$idUser = $session->idUser;
		$this->view->idUser = $idUser;

		// Get the filter for status
		$statusFilter = -1;
		if (isset($session->filter['filterStatus']) && (integer)$session->filter['filterStatus'] >= -1 && (integer)$session->filter['filterStatus'] <= 5)
		{
			$statusFilter = (integer)$session->filter['filterStatus'];
		}
		
		// Set up the paginator
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		$mapper = new Default_Model_MovieMapper();
		$this->view->paginator = Zend_Paginator::factory($mapper->getFilteredQuery($idUser, $statusFilter));
		$this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator->setItemCountPerPage($this->_getParam('perPage', 20));
	}

	public function viewAction()
	{
		$mapper = new Default_Model_MovieMapper();

		if ($this->getRequest()->getParam('idMovie'))
		{
			$this->view->movie = $mapper->find($this->getRequest()->getParam('idMovie'));
		}
		
		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'feed', 'movie' => $this->view->movie->id, 'format' => 'atom'), null, true), 'application/rss+xml', $this->view->translate('Activity for %s', array($this->view->movie->getTitle())));

		if (!$this->view->movie)
		{
			throw new Exception($this->view->translate('Movie not found'));
		}

		$mapperUser = new Default_Model_UserMapper();
		$this->view->users = $mapperUser->fetchAll();
	}

	public function addAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Movie();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$mapper = new Default_Model_MovieMapper();
				$movie = $mapper->getDbTable()->createRow();
				$movie->setId($values['id']);
				$movie->save();

				$this->view->movie = $movie;
				$this->_helper->FlashMessenger('We did something in the last request');
				$this->view->messages = $this->_helper->FlashMessenger->getMessages();
					
				//   return $this->_helper->redirector('index');
			}
		}

		$this->view->form = $form;
	}

}


