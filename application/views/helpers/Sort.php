<?php

class Default_View_Helper_Sort extends Zend_View_Helper_Abstract
{
	public function sort($label, $column, $sort, $sortOrder)
	{
		$orders = array('desc' => 'asc', 'asc' => 'desc');
		if (!in_array($sortOrder, $orders)) $sortOrder = reset($orders);
		
		$url = $this->view->url(array('sort' => $column, 'sortOrder' => $orders[$sortOrder]));
		$result = '<a class="sort ' . ($column == $sort ? $sortOrder : '') . '" title="' . $this->view->escape($this->view->translate('Sort by "%s"', array($label))) . '" href="' . $url . '">' . $label . '</a>';

		return $result;
	}
}

?>
