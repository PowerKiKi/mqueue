<?php

class Default_View_Helper_Link extends Zend_View_Helper_Abstract
{
	/**
	 * Create a link to IMDb page for specified movie.
	 * @param Default_Model_Movie $movie
	 * @param boolean $showUrl
	 */
	public function link(Default_Model_Movie $movie, $showUrl = false)
	{
		$url =  $movie->getImdbUrl();
		$result = '<a class="imdb' .($showUrl ? '' : ' hideUrl') . '" title="' . $url . '" href="' . $url . '"><span>&nbsp;' . $url . '</span></a>';
		
		return $result;
	}
}

