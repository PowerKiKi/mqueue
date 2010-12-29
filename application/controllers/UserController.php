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

				$this->_helper->FlashMessenger('Subscription complete.');
				 
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

					$this->_helper->FlashMessenger('Logged in.');

					return $this->_helper->redirector('index', 'movie');
				}
				else 
				{
					$this->_helper->FlashMessenger(array('error' => 'Login failed.'));
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
	
		if (!$this->view->user)
		{
			throw new Exception($this->view->translate('User not found'));
		}
		
		$this->view->headLink()->appendAlternate($this->view->serverUrl() . $this->view->url(array('controller' => 'activity', 'action' => 'index', 'user' => $this->view->user->id, 'format' => 'atom'), 'activityUser', true), 'application/atom+xml', $this->view->translate('mQueue - Activity for %s', array($this->view->user->nickname)));
		
		// Store perPage option in session
		$perPage = 25;
		$session = new Zend_Session_Namespace();
		if (isset($session->perPage)) $perPage = $session->perPage;
		if ($this->_getParam('perPage')) $perPage = $this->_getParam('perPage');
		$session->perPage = $perPage;
		
		$this->view->userActivity = Zend_Paginator::factory(Default_Model_StatusMapper::getActivityQuery($this->view->user));
		$this->view->userActivity->setCurrentPageNumber($this->_getParam('page'));
		$this->view->userActivity->setItemCountPerPage($perPage);
	}
}


