<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if (\mQueue\Model\User::getCurrent()) {
            $this->_helper->redirector('index', 'movie');
        } else {
            $this->_helper->redirector('index', 'activity');
        }
    }

}
