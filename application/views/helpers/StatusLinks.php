<?php

class Default_View_Helper_StatusLinks extends Zend_View_Helper_Abstract
{
	public function statusLinks(Default_Model_Status $status)
	{
		$result = '<div class="status_links status_links_' . $status->getUniqueId() .'">';
		foreach (Default_Model_Status::$ratings as $val => $name)
		{
			$class = strtolower($name) . ($status->rating == $val ? ' current' : '');
			$url = $this->view->serverUrl() . $this->view->url(array(
														'controller' => 'status',
														'movie' => $status->idMovie,
														'rating' => ($val == $this->status->rating) ? 0 : $val
			),
													'default', 
			true);
			$result .= '<a class="status status_' . $class . '" href="' . $url . '" title="' . $name . '"><span>' . $name . '</span></a>';

		}
		$result .= '</div>';

		return $result;
	}
}

?>
