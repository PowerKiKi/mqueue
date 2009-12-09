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
		
		$this->view->movie = $movie;

    }


}



