<?php

class JsController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->headers('application/javascript');
	}

	public function remoteJsAction()
	{
		// action body
	}

	public function mqueueUserJsAction()
	{
		// action body
	}
	
}









