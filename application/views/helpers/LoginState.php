<?php

class Default_View_Helper_LoginState extends Zend_View_Helper_Abstract
{
	public function loginState()
	{
		$result = '';
		$session = new Zend_Session_Namespace();
		if (isset($session->idUser))
		{
			$mapper = new Default_Model_UserMapper();
			$user = $mapper->find($session->idUser);
			
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'logout'), 
				'default', 
				true) . '">' . $this->view->translate('Logout') . '</a>';
		
			$result .= ' logged as ' . $user->nickname;
		}
		else
		{
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'login'), 
				'default', 
				true) . '">' . $this->view->translate('Login') . '</a>';
		
			$result .= ' <a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'new'), 
				'default', 
				true) . '">' . $this->view->translate('Subscribe') . '</a>';
			$result .= ' not logged in';
		}
		
		return $result;
	}

}
?>