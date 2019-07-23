<?php

use mQueue\Model\UserMapper;

class CssController extends Zend_Controller_Action
{
    public function init(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->headers('text/css');
    }

    public function gravatarCssAction(): void
    {
        $this->view->users = UserMapper::fetchAll();
    }
}
