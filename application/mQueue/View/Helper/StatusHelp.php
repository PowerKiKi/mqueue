<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class StatusHelp extends Zend_View_Helper_Abstract
{
    /**
     * Returns some help text about statuses for end-user
     * @return string
     */
    public function statusHelp()
    {
        $result = '<ul class="statusHelp">';
        $result .= '<li>' . $this->view->rating(\mQueue\Model\Status::Need) . ': ' . $this->view->translate('I want to see this movie') . '</li>';
        $result .= '<li>' . $this->view->rating(\mQueue\Model\Status::Bad) . ': ' . $this->view->translate('Boring movie, I wasted my time') . '</li>';
        $result .= '<li>' . $this->view->rating(\mQueue\Model\Status::Ok) . ': ' . $this->view->translate('Enjoyable movie (most movies)') . '</li>';
        $result .= '<li>' . $this->view->rating(\mQueue\Model\Status::Excellent) . ': ' . $this->view->translate('Excellent, I would watch it twice') . '</li>';
        $result .= '<li>' . $this->view->rating(\mQueue\Model\Status::Favorite) . ': ' . $this->view->translate('Incredibly awesome, the kind of movie you must watch many times regularly') . '</li>';
        $result .= '</ul>';

        return $result;
    }
}
