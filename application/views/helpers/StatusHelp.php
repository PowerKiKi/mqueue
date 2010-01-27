<?php

class Default_View_Helper_StatusHelp extends Zend_View_Helper_Abstract
{
	public function statusHelp()
	{
		$result = '<ul>';
		$result .= '<li><span class="status current status_1"><span>' . Default_Model_Status::$ratings[Default_Model_Status::Need] . '</span></span>: ' . $this->view->translate('I want to see this movie') . '</li>';
		$result .= '<li><span class="status current status_2"><span>' . Default_Model_Status::$ratings[Default_Model_Status::Bad] . '</span></span>: ' . $this->view->translate('Boring movie, I wasted my time') . '</li>';
		$result .= '<li><span class="status current status_3"><span>' . Default_Model_Status::$ratings[Default_Model_Status::Ok] . '</span></span>: ' . $this->view->translate('Enjoyable movie (most movies)') . '</li>';
		$result .= '<li><span class="status current status_4"><span>' . Default_Model_Status::$ratings[Default_Model_Status::Excellent] . '</span></span>: ' . $this->view->translate('Excellent, I would watch it twice') . '</li>';
		$result .= '<li><span class="status current status_5"><span>' . Default_Model_Status::$ratings[Default_Model_Status::Favorite] . '</span></span>: ' . $this->view->translate('Incredibly awesome, the kind of movie you must watch many times regularly') . '</li>';
		$result .= '</ul>';

		return $result;
	}
}
?>