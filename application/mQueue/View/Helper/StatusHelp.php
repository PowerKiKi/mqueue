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
        $result .= '<li><span class="mqueue_status current mqueue_status_1" title="' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Need] . '"><span>' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Need] . '</span></span>: ' . $this->view->translate('I want to see this movie') . '</li>';
        $result .= '<li><span class="mqueue_status current mqueue_status_2" title="' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Bad] . '"><span>' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Bad] . '</span></span>: ' . $this->view->translate('Boring movie, I wasted my time') . '</li>';
        $result .= '<li><span class="mqueue_status current mqueue_status_3" title="' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Ok] . '"><span>' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Ok] . '</span></span>: ' . $this->view->translate('Enjoyable movie (most movies)') . '</li>';
        $result .= '<li><span class="mqueue_status current mqueue_status_4" title="' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Excellent] . '"><span>' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Excellent] . '</span></span>: ' . $this->view->translate('Excellent, I would watch it twice') . '</li>';
        $result .= '<li><span class="mqueue_status current mqueue_status_5" title="' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Favorite] . '"><span>' . \mQueue\Model\Status::$ratings[\mQueue\Model\Status::Favorite] . '</span></span>: ' . $this->view->translate('Incredibly awesome, the kind of movie you must watch many times regularly') . '</li>';
        $result .= '</ul>';

        return $result;
    }
}
