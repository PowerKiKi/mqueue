<?php

class Default_View_Helper_LoginState extends Zend_View_Helper_Abstract
{
	public function loginState()
	{
		$result = '<div class="loginState">';
		$session = new Zend_Session_Namespace();
		if (isset($session->idUser))
		{
			$mapper = new Default_Model_UserMapper();
			$user = $mapper->find($session->idUser);
			
			$result .= $this->view->translate('logged as ');
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'view', 'nickname' => $user->nickname), 
				'default', 
				true) . '">' .  $user->nickname . '</a> ';
				
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'logout'), 
				'default', 
				true) . '">' . $this->view->translate('Logout') . '</a> ';
		}
		else
		{
			$result .= ' <a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'new'), 
				'default', 
				true) . '">' . $this->view->translate('Subscribe') . '</a> ';
				
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'login'), 
				'default', 
				true) . '">' . $this->view->translate('Login') . '</a>';
		}
		
		return $result . '</div>';
	}

}
?>