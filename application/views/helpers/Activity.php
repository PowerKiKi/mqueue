<?php

class Default_View_Helper_Activity extends Zend_View_Helper_Abstract
{
	public function activity($activity, $hiddenColumns = array())
	{
		$result = '<table class="activity">';
		
		$result .= '<tr>';
		$result .= '<th>' . $this->view->translate('Date') . '</th>';
		$result .= '<th>' . $this->view->translate('User') . '</th>';
		$result .= '<th>' . $this->view->translate('Movie') . '</th>';
		$result .= '<th>' . $this->view->translate('Rating') . '</th>';
		$result .= '</tr>';
		
		foreach ($activity as $a)
		{
			$result .= '<tr>';
			$result .= '<td>' . $a['status']->dateUpdate . '</td>';
			$result .= '<td><a href="' . $this->view->url(array(
										'controller' => 'user',
										'action' => 'view',
										'nickname' => $a['user']->nickname
										),
									'default', true) . '">' . $this->view->escape($a['user']->nickname) . '</td>';
			$result .= '<td><a href="' . $this->view->url(array(
										'controller' => 'movie',
										'action' => 'view',
										'idMovie' => $a['movie']->id
										),
									'default', true) . '">' . $this->view->escape($a['movie']->getTitle()) . '</td>';
			$result .= '<td>' . $this->view->statusLinks($a['status']) . '</td>';
			
			$result .= '</tr>'; 
		}
		
		return $result . '</table>';
	}

}
?>