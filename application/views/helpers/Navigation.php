<?php

class Default_View_Helper_Navigation extends Zend_View_Helper_Abstract
{
	public function navigation()
	{
		$result = '';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'movie'),
				'default', 
		true) . '">' . $this->view->translate('Movies') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'movie', 'action' => 'add'),
				'default', 
		true) . '">' . $this->view->translate('Add movie') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller'=>'user'),
				'default', 
		true) . '">' . $this->view->translate('Users') . '</a> ';
		
		$result .= '<a href="' . $this->view->serverUrl() . $this->view->url(
		array('controller' => 'faq'),
				'default', 
		true) . '">' . $this->view->translate('FAQ') . '</a> ';
		
		return $result;
	}

}
?>