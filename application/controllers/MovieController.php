<?php

class MovieController extends Zend_Controller_Action
{

    public function init()
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

    public function indexAction()
    {
        // Check there is at least one user, otherwise the whole page will crash
        if (!\mQueue\Model\User::getCurrent() && !\mQueue\Model\UserMapper::getDbTable()->fetchRow()) {
            throw new Exception('At least one user must exist to access this page');
        }

        $form = new \mQueue\Form\Filters();
        $this->view->formFilter = $form;

        // Detect if at least one filter was submitted
        $submitted = false;
        foreach ($this->getRequest()->getParams() as $key => $filter) {
            if (preg_match('/^filter\d+$/', $key)) {
                $submitted = true;
            }
        }

        // If was submitted and do not want to clear, try to validate values
        if ($submitted && !$this->_getParam('clear', false)) {
            if (!$form->isValid($this->getRequest()->getParams())) {
                $this->_helper->FlashMessenger(['warning' => _tr('Filter is invalid.')]);
                $form->setDefaults([]);
            }
        }
        // If we submitted a quicksearch, set default values to search with any status
        elseif ($this->_getParam('search')) {
            $form->setDefaults([
                'filter1' => [
                    'user' => \mQueue\Model\User::getCurrent() ? 0 : \mQueue\Model\UserMapper::fetchAll()->current()->id,
                    'status' => -2,
                    'title' => $this->_getParam('search'),
                ],
            ]);
        }
        // Otherwise clear the filter
        else {
            $form->setDefaults([]);
        }

        // Gather users selected in filters
        $this->view->users = [];
        $filters = $form->getValues();
        foreach ($filters as $key => $filter) {
            if (!preg_match('/^filter\d+$/', $key))
                continue;

            $this->view->users[$filter['user']] = \mQueue\Model\UserMapper::find($filter['user']);
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
        for ($i = 0; $i < $usersCount; $i++) {
            $allowedSortingKey[] = 'status' . $i;
        }
        $sort = $this->_helper->createSorting('sort', $allowedSortingKey);

        // Set up the paginator: Apply pagination only if there is no special context (so it is normal html rendering)
        $this->view->paginator = $this->_helper->createPaginator(\mQueue\Model\MovieMapper::getFilteredQuery($filters, $sort));
    }

    public function viewAction()
    {
        if ($this->getRequest()->getParam('id')) {
            $this->view->movie = \mQueue\Model\MovieMapper::find($this->getRequest()->getParam('id'));
        }

        if (!$this->view->movie) {
            throw new Exception($this->view->translate('Movie not found'));
        }

        $this->view->users = \mQueue\Model\UserMapper::fetchAll();
        $this->view->movieActivity = $this->_helper->createPaginator(\mQueue\Model\StatusMapper::getActivityQuery($this->view->movie));
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\Movie();

        if ($this->_getParam('id')) {
            if ($form->isValid($request->getParams())) {
                $values = $form->getValues();
                $movie = \mQueue\Model\MovieMapper::find(\mQueue\Model\Movie::extractId($values['id']));
                if (!$movie) {
                    $movie = \mQueue\Model\MovieMapper::getDbTable()->createRow();
                    $movie->setId($values['id']);
                    $movie->save();
                    $this->_helper->FlashMessenger(_tr('A movie was added.'));
                }

                $this->view->movies = [$movie];
            }
        }

        $this->view->form = $form;
    }

    public function importAction()
    {
        $request = $this->getRequest();
        $form = new \mQueue\Form\Import();
        $form->setDefaults(['favoriteMinimum' => 9, 'excellentMinimum' => 7, 'okMinimum' => 5]);
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($request->getPost())) {
            if (\mQueue\Model\User::getCurrent() == null) {
                $this->_helper->FlashMessenger(['error' => _tr('You must be logged in.')]);

                return;
            }

            $values = $form->getValues();
            $page = file_get_contents($values['url']);

            $pattern = '|<a href="/title/tt(\d{7})/">.*</td>\s*<td.*>(\d+(\.\d)*)</td>|U';
            preg_match_all($pattern, $page, $matches);

            $movies = [];
            $matchesCount = count($matches[1]);
            for ($i = 0; $i < $matchesCount; $i++) {
                $id = $matches[1][$i];
                $imdbRating = $matches[2][$i];

                $movie = \mQueue\Model\MovieMapper::find($id);
                if (!$movie) {
                    $movie = \mQueue\Model\MovieMapper::getDbTable()->createRow();
                    $movie->setId($id);
                    $movie->save();
                }

                if ($imdbRating >= $values['favoriteMinimum'])
                    $rating = \mQueue\Model\Status::Favorite;
                elseif ($imdbRating >= $values['excellentMinimum'])
                    $rating = \mQueue\Model\Status::Excellent;
                elseif ($imdbRating >= $values['okMinimum'])
                    $rating = \mQueue\Model\Status::Ok;
                else
                    $rating = \mQueue\Model\Status::Bad;

                $movie->setStatus(\mQueue\Model\User::getCurrent(), $rating);
                $movies [] = $movie;
            }

            $count = count($movies);
            if ($count) {
                $this->_helper->FlashMessenger(_tr('Movies imported.'));
                $this->view->movies = $movies;
            } else {
                $this->_helper->FlashMessenger(['warning' => _tr('No movies found for import.')]);
            }
        }
    }

}
