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
				$mapper = new Default_Model_StatusMapper();
        		
        		if ($idMovie != null)
				{
					$status = $mapper->find(1, $idMovie);
        		}
				else
				{
        			throw new Exception('no movie specified.');
				}
				
				
        		$rating = $this->_request->getParam('rating');
        		if (isset($rating))
        		{
        			$status->rating = $rating;
        			$mapper->save($status);
        		}
        		
        		$this->view->status = $status;
    }

}



