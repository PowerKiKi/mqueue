<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->view->users = Default_Model_UserMapper::fetchAll();
	}

	public function newAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_User();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$user = Default_Model_UserMapper::getDbTable()->createRow();
				$user->nickname = $values['nickname'];
				$user->email = $values['email'];
				$user->password = sha1($values['password']);
				$user->save();

				Default_Model_User::setCurrent($user);

				$this->_helper->FlashMessenger('new user created !');
				$this->view->messages = $this->_helper->FlashMessenger->getMessages();
				 
				return $this->_helper->redirector('index', 'movie');
			}
		}

		$this->view->form = $form;
	}

	public function loginAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_Login();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($request->getPost()))
			{

				$values = $form->getValues();

				$user = Default_Model_UserMapper::findEmailPassword($values['email'], $values['password']);
				if ($user)
				{
					if ($values['remember'])
						Zend_Session::rememberMe(1 * 60 * 60 * 24 * 31 * 2); // Cookie for two months
					
					Default_Model_User::setCurrent($user);

					$this->_helper->FlashMessenger('logged in !');
					$this->view->messages = $this->_helper->FlashMessenger->getMessages();

					return $this->_helper->redirector('index', 'movie');
				}
			}
		}

		$this->view->form = $form;
	}

	public function logoutAction()
	{
		Default_Model_User::setCurrent(null);
	}

	public function viewAction()
	{  
		if ($this->getRequest()->getParam('id'))
		{
			$this->view->user = Default_Model_UserMapper::find($this->getRequest()->getParam('id'));
		}
		else
		{
			$this->view->user = Default_Model_User::getCurrent();
		}
		
		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'feed', 'action' => 'index', 'user' => $this->view->user->id, 'format' => 'atom'), null, true), 'application/rss+xml', $this->view->translate('mQueue - Activity for %s', array($this->view->user->nickname)));
		
		if (!$this->view->user)
		{
			throw new Exception($this->view->translate('User not found'));
		}
		
	}
}


