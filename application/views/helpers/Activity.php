<?php

class Default_View_Helper_Activity extends Zend_View_Helper_Abstract
{
	public function activity(Zend_Paginator $activity, $hiddenColumns = array())
	{
		
		$result = '<table class="activity">';
		
		$result .= '<tr>';
		if (!in_array('date', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Date') . '</th>';
		if (!in_array('user', $hiddenColumns)) $result .= '<th>' . $this->view->translate('User') . '</th>';
		if (!in_array('movie', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Movie') . '</th>';
		if (!in_array('status', $hiddenColumns)) $result .= '<th>' . $this->view->translate('Rating') . '</th>';
		$result .= '</tr>';
		
		$cacheUser = array();
		$cacheMovie = array();
		foreach ($activity as $status)
		{
			if (!array_key_exists($status->idUser, $cacheUser))
			{
				$cacheUser[$status->idUser] = Default_Model_UserMapper::find($status->idUser);
			}
			$user = $cacheUser[$status->idUser]; 
			
			if (!array_key_exists($status->idMovie, $cacheMovie))
			{
				$cacheMovie[$status->idMovie] = Default_Model_MovieMapper::find($status->idMovie);
			}
			$movie = $cacheMovie[$status->idMovie];
			
			
			$result .= '<tr>';
			if (!in_array('date', $hiddenColumns)) $result .= '<td class="dateUpdate timestamp" title="' . $status->getDateUpdate()->get(Zend_Date::ISO_8601) . '">' . $status->dateUpdate . '</td>';
			if (!in_array('user', $hiddenColumns)) $result .= '<td class="user"><a href="' . $this->view->url(array(
										'controller' => 'user',
										'action' => 'view',
										'id' => $user->id
										),
									'default', true) . '">' . $this->view->gravatar($user). ' ' . $this->view->escape($user->nickname) . '</a></td>';
			if (!in_array('movie', $hiddenColumns)) $result .= '<td class="movie">' . $this->view->movie($movie) . '</td>';
			if (!in_array('status', $hiddenColumns)) $result .= '<td class="rating">' . $this->view->statusLinks($status) . '</td>';
			
			$result .= '</tr>';
		}
		
		return $result . '</table>';
	}

}
?>