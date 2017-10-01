<?php

class StatusController extends Zend_Controller_Action
{
    public function init(): void
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('list', 'json')
                ->addActionContext('index', 'json')
                ->initContext();
    }

    public function indexAction(): void
    {
        $jsonCallback = $this->_request->getParam('jsoncallback');
        if ($jsonCallback) {
            $this->_helper->layout->setLayout('jsonp');
            $this->view->jsonCallback = $jsonCallback;
        }

        $idMovie = \mQueue\Model\Movie::extractId($this->_request->getParam('movie'));

        if ($idMovie == null) {
            throw new Exception('no valid movie specified.');
        }

        // If new rating is specified and we are logged in, save it and create movie if needed
        $rating = $this->_request->getParam('rating');
        if (isset($rating) && \mQueue\Model\User::getCurrent()) {
            $movie = \mQueue\Model\MovieMapper::find($idMovie);

            if ($movie == null) {
                $movie = \mQueue\Model\MovieMapper::getDbTable()->createRow();
                $movie->setId($idMovie);
                $movie->save();
            }
            $status = $movie->setStatus(\mQueue\Model\User::getCurrent(), $rating);
        } else {
            $status = \mQueue\Model\StatusMapper::find($idMovie, \mQueue\Model\User::getCurrent());
        }

        if (!$jsonCallback) {
            $this->view->status = $status;
        } else {
            $html = $this->view->statusLinks($status);
            $this->view->status = $html;
            $this->view->id = $status->getUniqueId();
        }
    }

    public function listAction(): void
    {
        $jsonCallback = $this->_request->getParam('jsoncallback');
        if ($jsonCallback) {
            $this->_helper->layout->setLayout('jsonp');
            $this->view->jsonCallback = $jsonCallback;
        }

        $idMovies = [];
        foreach (explode(',', trim($this->_request->getParam('movies'), ',')) as $idMovie) {
            $idMovie = \mQueue\Model\Movie::extractId($idMovie);
            if ($idMovie) {
                $idMovies[] = $idMovie;
            }
        }

        $statuses = \mQueue\Model\StatusMapper::findAll($idMovies, \mQueue\Model\User::getCurrent());

        $json = [];
        foreach ($statuses as $s) {
            $html = $this->view->statusLinks($s);
            $json[$s->getUniqueId()] = $html;
        }

        $this->view->status = $json;
    }

    public function graphAction(): void
    {
        $percent = $this->_request->getParam('percent');
        $user = \mQueue\Model\UserMapper::find($this->getParam('user'));
        $data = \mQueue\Model\StatusMapper::getGraph($user, $percent);
        $chart = [
            'chart' => [
                'zoomType' => 'x',
            ],
            'title' => [
                'text' => '',
            ],
            'xAxis' => [
                'type' => 'datetime',
            ],
            'yAxis' => [
                'title' => [
                    'text' => 'Movies',
                ],
                'min' => 0,
            ],
            'series' => $data,
        ];

        if ($percent) {
            $chart['chart']['type'] = 'area';
            $chart['yAxis']['title']['text'] = $chart['yAxis']['title']['text'] . ' [%]';
            $chart['plotOptions'] = [
                'area' => [
                    'stacking' => 'percent',
                    'marker' => [
                        'enabled' => false,
                    ],
                ],
            ];
        }

        echo Zend_Json::encode($chart, Zend_Json::TYPE_ARRAY);
        die();
    }
}
