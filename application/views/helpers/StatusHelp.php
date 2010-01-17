<?php

class Default_View_Helper_StatusHelp extends Zend_View_Helper_Abstract
{
	public function statusHelp()
	{
		$result = '<ul>';
		$result .= '<li><span class="status current status_need">' . $this->view->translate('Need') . '</span>: ' . $this->view->translate('I want to see this movie') . '</li>';
		$result .= '<li><span class="status current status_bad">' . $this->view->translate('Bad') . '</span>: ' . $this->view->translate('Boring movie, I wasted my time') . '</li>';
		$result .= '<li><span class="status current status_ok">' . $this->view->translate('OK') . '</span>: ' . $this->view->translate('Enjoyable movie (most movies)') . '</li>';
		$result .= '<li><span class="status current status_excellent">' . $this->view->translate('Excellent') . '</span>: ' . $this->view->translate('Really good, I would watch it twice') . '</li>';
		$result .= '<li><span class="status current status_favorite">' . $this->view->translate('Favorite') . '</span>: ' . $this->view->translate('Incredibly awesome, the kind of movie you must watch many times regurlarly') . '</li>';
		$result .= '</ul>';
		return $result;
	}

}
?>