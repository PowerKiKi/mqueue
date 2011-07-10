<?php

class Default_View_Helper_AlternateFormats extends Zend_View_Helper_Abstract
{
	
	protected static $supportedFormats = array(
		'atom' => array(
			'name' => 'Atom',
			'mime' => 'application/atom+xml',
		),
		'csv' => array(
			'name' => 'CSV',
			//'mime' => 'text/csv',
		),
	);
	
	/**
	 * Returns an HTML table of activities
	 * @param array formats
	 * @return string
	 */
	public function alternateFormats(array $formats, $title = null)
	{
		$formatLinks = array();
		foreach ($formats as $format => $url)
		{
			// Inject format and locale parameters
			if (strpos($url, '?') === false)
				$url .= '?';
			else
				$url .= '&';
			$url .= 'format=' . $format . '&lang=' . Zend_Registry::get('Zend_Locale')->getLanguage();
			
			$formatLinks []= '<a class="' . $format . '" href="' . $url . '">' . self::$supportedFormats[$format]['name'] . '</a>';
			if ($title && isset(self::$supportedFormats[$format]['mime']))
			{
				$this->view->headLink()->appendAlternate($url, self::$supportedFormats[$format]['mime'], $title);
			}
		}
		
		$result = '<p class="alternateFormats">' . $this->view->translate('Also available in:') . ' ' . join(' | ', $formatLinks) . '</p>';
		
		return $result;
	}
}
