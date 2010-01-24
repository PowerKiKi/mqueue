<?php

class Default_View_Helper_Navigation extends Zend_View_Helper_Abstract
{
	public function navigation()
	{
		$result = '';

		$session = new Zend_Session_Namespace();
		$mapper = new Default_Model_UserMapper();
		$user = $mapper->find($session->idUser);
			
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'movie'),
				'default', 
		true) . '">' . $this->view->translate('Movies') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'user'),
				'default', 
		true) . '">' . $this->view->translate('Users') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller' => 'faq'),
				'default', 
		true) . '">' . $this->view->translate('FAQ') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'about'),
				'default', 
		true) . '">' . $this->view->translate('About') . '</a> ';
		
		return $result;
	}

}
?>