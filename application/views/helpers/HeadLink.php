<?php

class Default_View_Helper_HeadLink extends Zend_View_Helper_HeadLink
{
	/**
	 * Override parent to inject the last modified time of file.
	 * This avoid browser cache and force reloading when the file changed.
	 * @param string $method
	 * @param array $args
	 * @return type
	 */
    public function __call($method, $args)
    {
		if (strpos($method, 'Stylesheet'))
		{
			$args[0] = $this->view->cacheStamp($args[0]);
		}
		
		return parent::__call($method, $args);
	}
}
