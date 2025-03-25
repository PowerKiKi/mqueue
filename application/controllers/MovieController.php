<?php

use mQueue\Form\Filters;
use mQueue\Model\Movie;
use mQueue\Model\MovieMapper;
use mQueue\Model\StatusMapper;
use mQueue\Model\User;
use mQueue\Model\UserMapper;

class MovieController extends Zend_Controller_Action
{
    public function init(): void
    {
        // Init the Context Switch Action helper
        $contextSwitch = $this->_helper->contextSwitch();

        // Add the new context
        $contextSwitch->setContexts([
            'csv' => ['suffix' => 'csv'],
            'rss' => ['suffix' => 'rss'],
        ]);

        $contextSwitch->addActionContext('index', 'csv')->addActionContext('index', 'rss')->initContext();
    }

    public function indexAction(): void
    {
        // Check there is at least one user, otherwise the whole page will crash
        if (!User::getCurrent() && !UserMapper::getDbTable()->fetchRow()) {
            throw new Exception('At least one user must exist to access this page');
        }

        $form = new Filters();
        $this->view->formFilter = $form;

        // Detect if at least one filter was submitted
        $submitted = false;
        foreach ($this->getRequest()->getParams() as $key => $filter) {
            if (preg_match('/^filter\d+$/', $key)) {
                $submitted = true;
            }
        }

        // If was submitted, try to validate values
        if ($submitted) {
            if (!$form->isValid($this->getRequest()->getParams())) {
                $this->_helper->FlashMessenger(['warning' => _tr('Filter is invalid.')]);
                $form->setDefaults([]);
            }
        } // If we submitted a quicksearch, set default values to search with any status
        elseif ($this->_getParam('search')) {
            $form->setDefaults([
                'filter1' => [
                    'user' => User::getCurrent() ? 0 : UserMapper::fetchAll()->current()->id,
                    'condition' => 'is',
                    'status' => array_merge([0], array_keys(mQueue\Model\Status::$ratings)),
                    'title' => $this->_getParam('search'),
                ],
            ]);
        } // Otherwise clear the filter
        else {
            $form->setDefaults([]);
        }

        // Gather users selected in filters
        $this->view->users = [];
        $filters = $form->getValues();
        foreach ($filters as $key => $filter) {
            if (!preg_match('/^filter\d+$/', $key)) {
                continue;
            }

            $this->view->users[$filter['user']] = UserMapper::find($filter['user']);
        }

        // If we ouput rss, we force sorting by date
        if ($this->_helper->contextSwitch()->getCurrentContext() == 'rss') {
            $this->getRequest()->setParam('sort', $filters['filter1']['withSource'] ? 'dateSearch' : 'date');
            $this->getRequest()->setParam('sortOrder', 'desc');
        }
        $this->view->permanentParams = $form->getValues();
        $this->view->filterName = $form->getValuesText();
        unset($this->view->permanentParams['addFilter']);

        $allowedSortingKey = ['title', 'date', 'dateSearch'];
        $usersCount = count($this->view->users);
        for ($i = 0; $i < $usersCount; ++$i) {
            $allowedSortingKey[] = 'status' . $i;
        }
        $sort = $this->_helper->createSorting('sort', $allowedSortingKey);

        // Set up the paginator: Apply pagination only if there is no special context (so it is normal html rendering)
        $this->view->paginator = $this->_helper->createPaginator(MovieMapper::getFilteredQuery($filters, $sort));
    }

    public function viewAction(): void
    {
        if ($this->getRequest()->getParam('id')) {
            $this->view->movie = MovieMapper::find($this->getRequest()->getParam('id'));
        }

        if (!$this->view->movie) {
            throw new Exception($this->view->translate('Movie not found'));
        }

        $this->view->users = UserMapper::fetchAll();
        $this->view->movieActivity = $this->_helper->createPaginator(StatusMapper::getActivityQuery($this->view->movie));
    }

    public function addAction(): void
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\Movie();

        if ($this->_getParam('id')) {
            if ($form->isValid($request->getParams())) {
                $values = $form->getValues();
                $movie = MovieMapper::find(Movie::extractId($values['id']));
                if (!$movie) {
                    $movie = MovieMapper::getDbTable()->createRow();
                    $movie->setId($values['id']);
                    $movie->save();
                    $this->_helper->FlashMessenger(_tr('A movie was added.'));
                }

                $this->view->movies = [$movie];
            }
        }

        $this->view->form = $form;
    }
}
