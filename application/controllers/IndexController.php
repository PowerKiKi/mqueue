<?php

use mQueue\Model\User;

class IndexController extends Zend_Controller_Action
{
    public function init(): void
    {
        // Initialize action controller here
    }

    public function indexAction(): void
    {
        if (User::getCurrent()) {
            $this->_helper->redirector('index', 'movie');
        } else {
            $this->_helper->redirector('index', 'activity');
        }
    }
}
