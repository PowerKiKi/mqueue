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
				'rss' => array('suffix'  => 'rss'),
			));

		$contextSwitch->addActionContext('index', 'csv')->addActionContext('index', 'rss')->initContext();
	}

	public function indexAction()
	{
		// Check there is at least one user, otherwise the whole page will crash
		if (!Default_Model_User::getCurrent() && !Default_Model_UserMapper::getDbTable()->fetchRow())
		{
			throw new Exception('At least one user must exist to access this page');
		}

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
				$this->_helper->FlashMessenger(array('warning' => _tr('Filter is invalid.')));
				$form->setDefaults(array());
			}
		}
		// If we submitted a quicksearch, set default values to search with any status
		elseif ($this->_getParam('search'))
		{
			$form->setDefaults(array(
				'filter1' => array(
					'user' => Default_Model_User::getCurrent() ? 0 : Default_Model_UserMapper::fetchAll()->current()->id,
					'status' => -2,
					'title' => $this->_getParam('search'),
				)
			));
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

		// If we ouput rss, we force sorting by date
		if ($this->_helper->contextSwitch()->getCurrentContext() == 'rss')
		{
            $this->getRequest()->setParam('sort', $filters['filter1']['withSource'] ? 'dateSearch' : 'date');
			$this->getRequest()->setParam('sortOrder', 'desc');
		}
		$this->view->permanentParams = $form->getValues();
		$this->view->filterName = $form->getValuesText();
		unset($this->view->permanentParams['addFilter']);


        $allowedSortingKey = array('title', 'date', 'dateSearch');
        for ($i = 0; $i < count($this->view->users); $i++)
        {
            $allowedSortingKey[] = 'status' . $i;
        }
        $sort = $this->_helper->createSorting('sort', $allowedSortingKey);

		// Set up the paginator: Apply pagination only if there is no special context (so it is normal html rendering)
		$this->view->paginator = $this->_helper->createPaginator(Default_Model_MovieMapper::getFilteredQuery($filters, $sort));
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

		$this->view->users = Default_Model_UserMapper::fetchAll();
		$this->view->movieActivity = $this->_helper->createPaginator(Default_Model_StatusMapper::getActivityQuery($this->view->movie));
	}

	public function addAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Movie();

		if ($this->_getParam('id'))
		{
			if ($form->isValid($request->getParams()))
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

				$movie->setStatus(Default_Model_User::getCurrent(), $rating);
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
