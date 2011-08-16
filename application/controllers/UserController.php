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

					
					$referrer = $values['referrer'];
					
					// If we have a valid referer to one page of ourselve (except login or logout), redirect to it
					if (strpos($referrer, $this->view->serverUrl() . $this->view->baseUrl()) === 0
							&& strpos($referrer, $this->view->serverUrl() . $this->view->url(array('controller' => 'user', 'action' => 'login'))) !== 0
							&& strpos($referrer, $this->view->serverUrl() . $this->view->url(array('controller' => 'user', 'action' => 'logout'))) !== 0)
						return $this->_redirect($values['referrer']);
					else
						return $this->_helper->redirector('index', 'movie');
				}
				else 
				{
					$this->_helper->FlashMessenger(array('error' => 'Login failed.'));
				}
			}
		}
		else
		{
			$form->setDefaults(array(
				'remember' => true,
				'referrer' => $this->getRequest()->getServer('HTTP_REFERER'),
				));
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


