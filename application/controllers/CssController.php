<?php

class CssController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->disableLayout();
	}

	public function gravatarCssAction()
	{
		$mapper = new Default_Model_UserMapper();
		$this->view->users = $mapper->fetchAll();
	}


}

