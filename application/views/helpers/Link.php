<?php

class Default_View_Helper_Link extends Zend_View_Helper_Abstract
{
	public function link(Default_Model_Movie $movie, $displayUrl = false)
	{
		$url =  $movie->getImdbUrl();
		$result = '<a class="imdb" title="' . $url . '" href="' . $url . '"><span>' . $this->view->translate('IMDb') . '</span></a>';
		
		if ($displayUrl)
		{
			$result .= ' <a href="' . $url . '">' . $url . '</a>';
		}
		
		return $result;
	}
}

?>
