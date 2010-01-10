<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// action body
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
				$user->password = $values['password'];
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


}
