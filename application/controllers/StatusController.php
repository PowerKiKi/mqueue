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
		if ($this->_helper->layout->isEnabled())
			$this->_helper->layout->setLayout('iframe');
			
		$idMovie = $this->_request->getParam('movie');
		$mapper = new Default_Model_StatusMapper();

		if ($idMovie == null)
		throw new Exception('no movie specified.');
		 
		$session = new Zend_Session_Namespace();		
		$status = $mapper->find($session->idUser, $idMovie);
		 

		// If new rating specified, save it and create movie if needed
		$rating = $this->_request->getParam('rating');
		if (isset($rating))
		{

			$movieMapper = new Default_Model_MovieMapper();
			$movie = $movieMapper->find($status->idMovie);
			 
			if ($movie == null)
			{
				$movie = $movieMapper->getDbTable()->createRow();
				$movie->id = $status->idMovie;
				$movie->save();
			}
			$status->rating = $rating;
			$status->save();
		}

		
		if ($this->_helper->layout->isEnabled())
		{
			$this->view->status = $status;	
		}
		else
		{
			$json = array();
			$html = $this->view->statusLinks($status);
			$this->view->status = $html;
			$this->view->id = $status->id;
		}
	}

	public function listAction()
	{
		$idMovies = split(',', $this->_request->getParam('movies'));	

		
		$session = new Zend_Session_Namespace();
		$mapper = new Default_Model_StatusMapper();
		$statuses = $mapper->findAll($session->idUser, $idMovies);
		
		$json = array();
		foreach ($statuses as $s)
		{
			$html = $this->view->statusLink($s);
			$json[$s->idMovie] = $html;
		}
		
		$this->view->status = $json;
	}
}




