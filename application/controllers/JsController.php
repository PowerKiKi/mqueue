<?php

class JsController extends Zend_Controller_Action
{
    public function init(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->headers('application/javascript');
    }

    public function remoteJsAction(): void
    {
        // action body
    }

    public function mqueueUserJsAction(): void
    {
        // action body
    }
}
