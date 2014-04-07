<?php

class ActivityController extends Zend_Controller_Action
{

    public function init()
    {
        // Init the Context Switch Action helper
        $contextSwitch = $this->_helper->contextSwitch();

        // Add the new context
        $contextSwitch->setContexts(array(
            'rss' => array('suffix' => 'rss')
        ));

        $contextSwitch->addActionContext('index', 'rss')->initContext();
    }

    public function indexAction()
    {
        // By default we show overall activity
        $item = null;
        $this->view->title = $this->view->translate('Overall activity');

        // Try to show user's actitvity
        if ($this->getRequest()->getParam('user')) {
            $item = \mQueue\Model\UserMapper::find($this->getRequest()->getParam('user'));
            if ($item) {
                $this->view->title = $this->view->translate('Activity for %s', array($item->nickname));
            }
        }

        // Try to show movie's actitvity
        if ($this->getRequest()->getParam('movie')) {
            $item = \mQueue\Model\MovieMapper::find($this->getRequest()->getParam('movie'));
            if ($item) {
                $this->view->title = $this->view->translate('Activity for %s', array($item->getTitle()));
            }
        }

        $this->view->activity = $this->_helper->createPaginator(\mQueue\Model\StatusMapper::getActivityQuery($item));
    }

}
