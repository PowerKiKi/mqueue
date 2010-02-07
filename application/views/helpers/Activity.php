<?php

class Default_View_Helper_Activity extends Zend_View_Helper_Abstract
{
	public function activity($activity, $hiddenColumns = array())
	{
		$result = '<table class="activity">';
		
		$result .= '<tr>';
		if (!in_array('date', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Date') . '</th>';
		if (!in_array('user', $hiddenColumns)) $result .= '<th>' . $this->view->translate('User') . '</th>';
		if (!in_array('movie', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Movie') . '</th>';
		if (!in_array('status', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Rating') . '</th>';
		$result .= '</tr>';
		
		$count = 0;
		foreach ($activity as $a)
		{
			$result .= '<tr>';
			if (!in_array('date', $hiddenColumns)) $result .= '<td>' . $a['status']->dateUpdate . '</td>';
			if (!in_array('user', $hiddenColumns)) $result .= '<td><a href="' . $this->view->url(array(
										'controller' => 'user',
										'action' => 'view',
										'nickname' => $a['user']->nickname
										),
									'default', true) . '">' . $this->view->gravatar($a['user']). ' ' . $this->view->escape($a['user']->nickname) . '</td>';
			if (!in_array('movie', $hiddenColumns)) $result .= '<td><a href="' . $this->view->url(array(
										'controller' => 'movie',
										'action' => 'view',
										'idMovie' => $a['movie']->id
										),
									'default', true) . '">' . $this->view->escape($a['movie']->getTitle()) . '</td>';
			if (!in_array('status', $hiddenColumns)) $result .= '<td>' . $this->view->statusLinks($a['status']) . '</td>';
			
			$result .= '</tr>';
			 
			if (++$count == 25)
			{
				break;
			}
		}
		
		return $result . '</table>';
	}

}
?>