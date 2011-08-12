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

		$idMovie = Default_Model_Movie::extractId($this->_request->getParam('movie'));

		if ($idMovie == null)
			throw new Exception('no valid movie specified.');


		// If new rating is specified and we are logged in, save it and create movie if needed
		$rating = $this->_request->getParam('rating');
		if (isset($rating) && Default_Model_User::getCurrent())
		{
			$movie = Default_Model_MovieMapper::find($idMovie);

			if ($movie == null)
			{
				$movie = Default_Model_MovieMapper::getDbTable()->createRow();
				$movie->setId($idMovie);
				$movie->save();
			}
			$status = $movie->setStatus(Default_Model_User::getCurrent(), $rating);
		}
		else
		{
			$status = Default_Model_StatusMapper::find($idMovie, Default_Model_User::getCurrent());
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

		$statuses = Default_Model_StatusMapper::findAll($idMovies, Default_Model_User::getCurrent());

		$json = array();
		foreach ($statuses as $s)
		{
			$html = $this->view->statusLinks($s);
			$json[$s->getUniqueId()] = $html;
		}

		$this->view->status = $json;
	}
}




