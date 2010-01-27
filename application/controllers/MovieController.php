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
		
		// Set up the paginator
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		$mapper = new Default_Model_MovieMapper();
		$this->view->paginator = Zend_Paginator::factory($mapper->getFilteredQuery($idUser));
		$this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator->setItemCountPerPage($this->_getParam('perPage', 20));
	}

	public function viewAction()
	{
		// Should display status from all users for this movie
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


