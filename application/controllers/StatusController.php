<?php

class StatusController extends Zend_Controller_Action
{
	public function init()
	{
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->addActionContext('list', 'json')
		->addActionContext('index', 'json')
		->initContext();
			//$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
	}

	public function indexAction()
	{			
		$jsonCallback = $this->_request->getParam('jsoncallback');
		if ($jsonCallback)
		{
			$this->_helper->layout->setLayout('jsonp');
			$this->view->jsonCallback = $jsonCallback;
		}
		
		$idMovie = $this->_request->getParam('movie');

		if ($idMovie == null)
		throw new Exception('no movie specified.');
		 
		$session = new Zend_Session_Namespace();		
		$status = Default_Model_StatusMapper::find($session->idUser, $idMovie);
		 

		// If new rating specified and we are logged in, save it and create movie if needed
		$rating = $this->_request->getParam('rating');
		if (isset($rating) && Default_Model_User::getCurrent())
		{
			$movie = Default_Model_MovieMapper::find($status->idMovie);
			 
			if ($movie == null)
			{
				$movie = Default_Model_MovieMapper::getDbTable()->createRow();
				$movie->id = $status->idMovie;
				$movie->save();
			}
			$status->rating = $rating;
			$status->save();
		}

		
		if (!$jsonCallback)
		{
			$this->view->status = $status;	
		}
		else
		{
			$json = array();
			$html = $this->view->statusLinks($status);
			$this->view->status = $html;
			$this->view->id = $status->getUniqueId();
		}
	}

	public function listAction()
	{
		$jsonCallback = $this->_request->getParam('jsoncallback');
		if ($jsonCallback)
		{
			$this->_helper->layout->setLayout('jsonp');
			$this->view->jsonCallback = $jsonCallback;
		}
			
		$idMovies = explode(',', trim($this->_request->getParam('movies'), ','));

		
		$session = new Zend_Session_Namespace();
		$statuses = Default_Model_StatusMapper::findAll($session->idUser, $idMovies);
		
		$json = array();
		foreach ($statuses as $s)
		{
			$html = $this->view->statusLinks($s);
			$json[$s->getUniqueId()] = $html;
		}
		
		$this->view->status = $json;
	}
}




