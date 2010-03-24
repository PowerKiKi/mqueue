<?php

class CssController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->disableLayout();
	}

	public function gravatarCssAction()
	{
		$this->view->users = Default_Model_UserMapper::fetchAll();
	}


}

