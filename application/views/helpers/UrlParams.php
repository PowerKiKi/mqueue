<?php

class Default_View_Helper_UrlParams extends Zend_View_Helper_Abstract
{
	public function urlParams(array $params)
	{
		return $this->view->url() . '?' . $this->flatten($params);
	}
	
	/**
	 * Flatten a recursive array in GET parameters (same as HTML form send GET request)
	 * @param array $params
	 * @param string $result
	 * @param string $previousName
	 */
	private function flatten(array $params, $result = null, $previousName = null)
	{
		foreach ($params as $key => $value)
		{
			if ($previousName)
			{
				$name = $previousName . '[' . $key . ']';
			}
			else
			{
				$name = $key;
			}
			
			if (is_array($value))
			{
				$result = $this->flatten($value, $result, $name);
			}
			else
			{
				if ($result)
				{
					$result .= '&amp;';
				}
			
				$result .= $name . '=' . $value;
			}
		}
		
		return $result;
	}

}
?>