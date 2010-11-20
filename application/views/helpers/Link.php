<?php

class Default_View_Helper_Link extends Zend_View_Helper_Abstract
{
	public function link(Default_Model_Movie $movie, $showUrl = false)
	{
		$url =  $movie->getImdbUrl();
		$result = '<a class="imdb' .($showUrl ? '' : ' hideUrl') . '" title="' . $url . '" href="' . $url . '"><span>&nbsp;' . $url . '</span></a>';
		
		return $result;
	}
}

?>
