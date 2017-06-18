<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;

class Rating extends Zend_View_Helper_Abstract
{
    /**
     * Returns some help text about statuses for end-user
     * @param int $rating
     * @return string
     */
    public function rating(int $rating)
    {
        $result = '<span class="mqueue_status current mqueue_status_' . $rating . '" title="' . \mQueue\Model\Status::$ratings[$rating] . '"><span>' . \mQueue\Model\Status::$ratings[$rating] . '</span></span>';

        return $result;
    }
}
