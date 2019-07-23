<?php

use mQueue\Model\MovieMapper;
use mQueue\Model\StatusMapper;
use mQueue\Model\UserMapper;

class ActivityController extends Zend_Controller_Action
{
    public function init(): void
    {
        // Init the Context Switch Action helper
        $contextSwitch = $this->_helper->contextSwitch();

        // Add the new context
        $contextSwitch->setContexts([
            'rss' => ['suffix' => 'rss'],
        ]);

        $contextSwitch->addActionContext('index', 'rss')->initContext();
    }

    public function indexAction(): void
    {
        // By default we show overall activity
        $item = null;
        $this->view->title = $this->view->translate('Overall activity');

        // Try to show user's activity
        if ($this->getRequest()->getParam('user')) {
            $item = UserMapper::find($this->getRequest()->getParam('user'));
            if ($item) {
                $this->view->title = $this->view->translate('Activity for %s', [$item->nickname]);
            }
        }

        // Try to show movie's activity
        if ($this->getRequest()->getParam('movie')) {
            $item = MovieMapper::find($this->getRequest()->getParam('movie'));
            if ($item) {
                $this->view->title = $this->view->translate('Activity for %s', [$item->getTitle()]);
            }
        }

        $this->view->activity = $this->_helper->createPaginator(StatusMapper::getActivityQuery($item));
    }
}
