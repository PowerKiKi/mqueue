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
		foreach ($languages as $val => $name)
		{
			$result .= '<a class="language language_' . $val . '" href="?lang=' . $val . '" title="' . $name . '"><span>' . $name . '</span></a> ';
		}
		$result .= '</div>';

		return $result;
	}
}

?>
