<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use \mQueue\Model\User;

class Gravatar extends Zend_View_Helper_Abstract
{

    /**
     * Return a span with CSS class for a user on gravatar
     * @param \mQueue\Model\User $user
     * @param boolean $small
     */
    public function gravatar(\mQueue\Model\User $user, $small = true)
    {
        $result = '<span class="gravatar user_' . $user->id . ' ' . ($small ? 'small' : 'big') . '"></span>';

        return $result;
    }

}
