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
        $this->_helper->layout->setLayout('iframe');
        		$id = $this->_request->getParam('id');
                $mapper = new Default_Model_MovieMapper();
        		$movie = $mapper->find($id);
        		
        		$status = $this->_request->getParam('status');
        		if (isset($status))
        		{
        			$movie->status = $status;
        			$mapper->save($movie);
        		}
        		
        		$this->view->movie = $movie;
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = new Default_Form_Movie();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost()))
			{
                $model = new Default_Model_Movie($form->getValues());
				$this->_helper->FlashMessenger('We did something in the last request');
				
				$this->view->messages = $this->_helper->FlashMessenger->getMessages();
				
                $mapper = new Default_Model_MovieMapper();
                $mapper->save($model);
             //   return $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }
    
}


