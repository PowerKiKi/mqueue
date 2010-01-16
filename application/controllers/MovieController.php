<?php

class MovieController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $mapper = new Default_Model_MovieMapper();
        		$movies = $mapper->fetchAll();
        		
        		$this->view->entries = $movies;
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


