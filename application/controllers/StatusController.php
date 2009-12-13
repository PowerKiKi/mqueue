<?php

class StatusController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('iframe');
        		$idMovie = $this->_request->getParam('movie');
        		if ($idMovie == null)
        			throw new Exception('no movie specified.');
        		
                $mapper = new Default_Model_StatusMapper();
        		$status = $mapper->find(1, $idMovie);
        		
        		$rating = $this->_request->getParam('rating');
        		if (isset($rating))
        		{
        			$status->rating = $rating;
        			$mapper->save($status);
        		}
        		
        		$this->view->status = $status;
    }

}



