<?php

class Default_View_Helper_LanguageSelector extends Zend_View_Helper_Abstract
{
	public function languageSelector()
	{
		$languages = array(
			'en' => 'English',
			'ko' => '한국어',
			'fr' => 'Français',
			);
		
		$result = '<div class="language_selector">';
		$params = $_GET;
		foreach ($languages as $val => $name)
		{
			$params['lang'] = $val;
			$result .= '<a class="language language_' . $val . '" href="' . $this->view->urlParams($params) . '" title="' . $name . '"><span>' . $name . '</span></a> ';
		}
		$result .= '</div>';

		return $result;
	}
}

?>
