<?php

class MovieController extends Zend_Controller_Action
{
	public function init()
	{
		// Init the Context Switch Action helper
		$contextSwitch = $this->_helper->contextSwitch();

		// Add the new context
		$contextSwitch->setContexts(array(
				'csv' => array('suffix'  => 'csv'),
				'atom' => array('suffix'  => 'atom'),
			));
			
		$contextSwitch->addActionContext('index', 'csv')->addActionContext('index', 'atom')->initContext();
	}

	public function indexAction()
	{
		$form = new Default_Form_Filters();
		$this->view->formFilter = $form;

		// Detect if at least one filter was submitted
		$submitted = false;
		foreach ($this->getRequest()->getParams() as $key => $filter)
		{
			if (preg_match('/^filter\d+$/', $key))
			{
				$submitted = true;
			}
		}
		
		// If was submitted and do not want to clear, try to validate values
		if ($submitted && !$this->_getParam('clear', false))
		{
			if (!$form->isValid($this->getRequest()->getParams()))
			{
				$form->setDefaults(array());
			}
		}
		// Otherwise clear the filter
		else
		{
			$form->setDefaults(array());
		}

		// Gather users selected in filters
		$this->view->users = array();
		$filters = $form->getValues();
		foreach ($filters as $key => $filter)
		{
			if (!preg_match('/^filter\d+$/', $key))
				continue;
				
			$this->view->users[$filter['user']]= Default_Model_UserMapper::find($filter['user']);
		}
		
		// Store perPage option in session
		$perPage = 25;
		$session = new Zend_Session_Namespace();
		if (isset($session->perPage)) $perPage = $session->perPage;
		if ($this->_getParam('perPage')) $perPage = $this->_getParam('perPage');
		$session->perPage = $perPage;
		
		
		// Defines variables for the view
		$this->view->sort = $this->getRequest()->getParam('sort');
		$this->view->sortOrder = $this->getRequest()->getParam('sortOrder');
		
		// If we ouput atom, we force sorting by date
		if ($this->_helper->contextSwitch()->getCurrentContext() == 'atom')
		{
			$this->view->sort = 'date';
			$this->view->sortOrder = 'desc';
		}
		$this->view->permanentParams = $form->getValues();
		unset($this->view->permanentParams['addFilter']);
		if ($this->view->sort) $this->view->permanentParams['sort'] = $this->view->sort;
		if ($this->view->sortOrder) $this->view->permanentParams['sortOrder'] = $this->view->sortOrder;
		
		
		// Set up the paginator: Apply pagination only if there is no special context (so it is normal html rendering) 
		$this->view->paginator = Zend_Paginator::factory(Default_Model_MovieMapper::getFilteredQuery($filters, $this->view->sort, $this->view->sortOrder));
		switch ($this->_helper->contextSwitch()->getCurrentContext())
		{
			case 'csv': $perPage = 0; break;
			case 'atom': $perPage = 200; break;
			case null: $this->view->paginator->setCurrentPageNumber($this->_getParam('page')); break;
		}
		$this->view->paginator->setItemCountPerPage($perPage);
	}

	public function viewAction()
	{
		if ($this->getRequest()->getParam('id'))
		{
			$this->view->movie = Default_Model_MovieMapper::find($this->getRequest()->getParam('id'));
		}

		if (!$this->view->movie)
		{
			throw new Exception($this->view->translate('Movie not found'));
		}

		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'activity', 'action' => 'index', 'movie' => $this->view->movie->id, 'format' => 'atom'), 'activityMovie', true), 'application/atom+xml', $this->view->translate('mQueue - Activity for %s', array($this->view->movie->getTitle())));
		$this->view->users = Default_Model_UserMapper::fetchAll();
		
		
		// Store perPage option in session
		$perPage = 25;
		$session = new Zend_Session_Namespace();
		if (isset($session->perPage)) $perPage = $session->perPage;
		if ($this->_getParam('perPage')) $perPage = $this->_getParam('perPage');
		$session->perPage = $perPage;
		
		$this->view->movieActivity = Zend_Paginator::factory(Default_Model_StatusMapper::getActivityQuery($this->view->movie));
		$this->view->movieActivity->setCurrentPageNumber($this->_getParam('page'));
		$this->view->movieActivity->setItemCountPerPage($perPage);
	}

	public function addAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Movie();

		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$movie = Default_Model_MovieMapper::find(Default_Model_Movie::extractId($values['id']));
				if (!$movie)
				{
					$movie = Default_Model_MovieMapper::getDbTable()->createRow();
					$movie->setId($values['id']);
					$movie->save();
					$this->_helper->FlashMessenger(_tr('A movie was added.'));					
				}

				$this->view->movies = array($movie);
			}
		}

		$this->view->form = $form;
	}

	public function importAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Import();
		$form->setDefaults(array('favoriteMinimum' => 9, 'excellentMinimum' => 7, 'okMinimum' => 5));
		$this->view->form = $form;

		if ($this->getRequest()->isPost() && $form->isValid($request->getPost()))
		{
			if (Default_Model_User::getCurrent() == null)
			{
				$this->_helper->FlashMessenger(array('error' => _tr('You must be logged in.')));
				return;
			}
			
			$values = $form->getValues();
			$page = file_get_contents($values['url']);
			
			$r = '|<a href="/title/tt(\d{7})/">.*</td>\s*<td.*>(\d+(\.\d)*)</td>|U';
			preg_match_all($r, $page, $matches);
			
			$movies = array();
			for ($i = 0; $i < count($matches[1]); $i++)
			{
				$id = $matches[1][$i];
				$imdbRating = $matches[2][$i];
				
				$movie = Default_Model_MovieMapper::find($id);
				if (!$movie)
				{
					$movie = Default_Model_MovieMapper::getDbTable()->createRow();
					$movie->setId($id);
					$movie->save();
				}

				if ($imdbRating >= $values['favoriteMinimum'])
					$rating = Default_Model_Status::Favorite;
				elseif ($imdbRating >= $values['excellentMinimum'])
					$rating = Default_Model_Status::Excellent;
				elseif ($imdbRating >= $values['okMinimum'])
					$rating = Default_Model_Status::Ok;
				else
					$rating = Default_Model_Status::Bad;
					
				$movie->setStatus(Default_Model_User::getCurrent()->id, $rating);
				$movies []= $movie;
			}

			$count = count($movies);
			if ($count)
			{
				$this->_helper->FlashMessenger(_tr('Movies imported.'));
				$this->view->movies = $movies;
			}	
			else
			{
				$this->_helper->FlashMessenger(array('warning' => _tr('No movies found for import.')));
			}
		}
		
	}


}


