<?php

namespace mQueue\View\Helper;

use mQueue\Model\Status;
use Zend_View_Helper_Abstract;

class Rating extends Zend_View_Helper_Abstract
{
    /**
     * Returns some help text about statuses for end-user
     *
     * @param int $rating
     *
     * @return string
     */
    public function rating(int $rating)
    {
        $result = '<span class="mqueue_status current mqueue_status_' . $rating . '" title="' . Status::$ratings[$rating] . '"><span>' . Status::$ratings[$rating] . '</span></span>';

        return $result;
    }
}
