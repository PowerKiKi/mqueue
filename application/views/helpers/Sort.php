<?php

class Default_View_Helper_Sort extends Zend_View_Helper_Abstract
{
	/**
	 * Return an HTML links to be able to sort (will typically be in table header)
	 * @param string $label
	 * @param string $column
	 * @param string $sort
	 * @param string $sortOrder
	 * @param array $additionalParameters
	 */
	public function sort($label, $column, $sort, $sortOrder, array $additionalParameters = null)
	{
		if (is_null($additionalParameters))
			$additionalParameters = array();
			
		$orders = array('desc' => 'asc', 'asc' => 'desc');
		if (!in_array($sortOrder, $orders)) $sortOrder = reset($orders);
		
		$url = $this->view->urlParams(array_merge($additionalParameters, array('sort' => $column, 'sortOrder' => $orders[$sortOrder])));
		$result = '<a class="sort ' . ($column == $sort ? $sortOrder : '') . '" title="' . $this->view->escape($this->view->translate('Sort by "%s"', array($label))) . '" href="' . $url . '">' . $this->view->escape($label) . '</a>';

		return $result;
	}
}

?>
