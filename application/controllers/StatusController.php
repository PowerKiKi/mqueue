<?php

class StatusController extends Zend_Controller_Action
{
	public function init()
	{
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->addActionContext('list', 'json')
		->initContext();
			//$this->_helper->contextSwitch()->setAutoJsonSerialization(false);
	}

	public function indexAction()
	{
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

		$this->view->status = $status;
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
			$this->view->status = $s;
			$html = $this->view->render('status/index.phtml');
			$json[$s->idMovie] = $html;
		}
		
		$this->view->status = $json;
	}
}




