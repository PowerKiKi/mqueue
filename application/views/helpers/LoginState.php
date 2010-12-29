<?php

class Default_View_Helper_LoginState extends Zend_View_Helper_Abstract
{
	/**
	 * Returns a string displaying the login state of the user and buttons to login/off
	 * @return string
	 */
	public function loginState()
	{
		$result = '<div class="loginState">';
		
		$user = Default_Model_User::getCurrent();
		if ($user)
		{
			$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
				array('controller'=>'user', 'action' => 'view', 'id' => $user->id), 
				'singleid', 
				true) . '">' . $this->view->gravatar($user) . ' ' . $this->view->escape($user->nickname) . '</a> ';
				
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