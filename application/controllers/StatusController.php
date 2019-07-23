<?php

use mQueue\Model\Movie;
use mQueue\Model\MovieMapper;
use mQueue\Model\StatusMapper;
use mQueue\Model\User;
use mQueue\Model\UserMapper;

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

        $idMovie = Movie::extractId($this->_request->getParam('movie'));

        if ($idMovie == null) {
            throw new Exception('no valid movie specified.');
        }

        // If new rating is specified and we are logged in, save it and create movie if needed
        $rating = $this->_request->getParam('rating');
        if (isset($rating) && User::getCurrent()) {
            $movie = MovieMapper::find($idMovie);

            if ($movie == null) {
                $movie = MovieMapper::getDbTable()->createRow();
                $movie->setId($idMovie);
                $movie->save();
            }
            $status = $movie->setStatus(User::getCurrent(), $rating);
        } else {
            $status = StatusMapper::find($idMovie, User::getCurrent());
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
            $idMovie = Movie::extractId($idMovie);
            if ($idMovie) {
                $idMovies[] = $idMovie;
            }
        }

        $statuses = StatusMapper::findAll($idMovies, User::getCurrent());

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
        $user = UserMapper::find($this->getParam('user'));
        $data = StatusMapper::getGraph($user, $percent);
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
