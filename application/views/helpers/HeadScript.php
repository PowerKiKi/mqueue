<?php
class Default_View_Helper_HeadScript extends Zend_View_Helper_HeadScript
{
	/**
	 * Override parent to support timestamp, compilation and concatenation.
	 * Compiled and concatened files must pre-exist (compiled by external tools).
	 * @param string $method
	 * @param array $args
	 * @return Default_View_Helper_HeadScript
	 */
    public function __call($method, $args)
    {
		if (strpos($method, 'File'))
		{
			$fileName = $args[0];
			
			// If file will be concatened, use concatenation system instead
			if (is_array($fileName))
			{
				// If we are in development, actually don't concatenate anything
				if (APPLICATION_ENV == 'development')
				{
					foreach ($fileName[1] as $f)
					{
						$args[0] = $this->view->cacheStamp($f);
						parent::__call($method, $args);
					}
					
					return $this;
				}
				// Otherwise use pre-existing concatenated file
				else
				{
					$fileName = $fileName[0];
				}
			}
			
			$args[0] = $this->view->cacheStamp($fileName);
		}
		
		return parent::__call($method, $args);
	}
}
