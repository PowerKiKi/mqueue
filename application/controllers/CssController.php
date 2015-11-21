<?php

class CssController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->headers('text/css');
    }

    public function gravatarCssAction()
    {
        $this->view->users = \mQueue\Model\UserMapper::fetchAll();
    }
}
