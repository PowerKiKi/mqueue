<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$mapper = new Default_Model_UserMapper();
		$this->view->users = $mapper->fetchAll();

		$mapper = new Default_Model_StatusMapper();
		$this->view->activity = $mapper->getActivity();
	}

	public function newAction()
	{
		$request = $this->getRequest();
		$form    = new Default_Form_User();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($request->getPost()))
			{
				$values = $form->getValues();
				$mapper = new Default_Model_UserMapper();
				$user = $mapper->getDbTable()->createRow();
				$user->nickname = $values['nickname'];
				$user->email = $values['email'];
				$user->password = sha1($values['password']);
				$user->save();

				$session = new Zend_Session_Namespace();
				$session->idUser = $user->id;

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
				$mapper = new Default_Model_UserMapper();

				$user = $mapper->findEmailPassword($values['email'], $values['password']);
				if ($user)
				{
					if ($values['remember'])
						Zend_Session::rememberMe(1 * 60 * 60 * 24 * 31 * 2); // Cookie for two months
					
					$session = new Zend_Session_Namespace();
					$session->idUser = $user->id;
					

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
		$session = new Zend_Session_Namespace();
		$session->idUser = null;
	}

	public function viewAction()
	{  
		if ($this->getRequest()->getParam('nickname'))
		{
			$mapper = new Default_Model_UserMapper();
			$this->view->user = $mapper->findNickname($this->getRequest()->getParam('nickname'));
		}
		else
		{
			$this->view->user = Default_Model_User::getCurrent();
		}
		
		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'feed', 'user' => $this->view->user->nickname, 'format' => 'atom'), null, true), 'application/rss+xml', $this->view->translate('Activity for %s', array($this->view->user->nickname)));
		
		if (!$this->view->user)
		{
			throw new Exception($this->view->translate('User not found'));
		}
		
	}
}


