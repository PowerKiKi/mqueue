<?php

class Default_View_Helper_Movie extends Zend_View_Helper_Abstract
{
	/**
	 * Returns a string for a movie. It is composed of a link to IMDb and the movie title which links to the movie page.
	 * @param Default_Model_Movie $movie
	 */
	public function movie(Default_Model_Movie $movie)
	{
		$result = $this->view->link($movie);
		
		$user = Default_Model_User::getCurrent();
		if ($user)
		{
			$status = $movie->getStatus($user);
			$title = $this->view->translate('Your rating is : %s', array($status->getName()));
		}
		else
		{
			$title = $this->view->translate('Your are not logged in');
		}
		
		$movieUrl = $this->view->url(array('controller' => 'movie', 'action' => 'view', 'id' => $movie->id), 'singleid', true);
		$result .= ' <a title="' . $title . '" href="' . $movieUrl . '">' . $this->view->escape($movie->getTitle()) . '</a>';

		return $result;
	}
}

