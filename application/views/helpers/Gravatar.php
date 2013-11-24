<?php

class Default_View_Helper_Gravatar extends Zend_View_Helper_Abstract
{

    /**
     * Return a span with CSS class for a user on gravatar
     * @param Default_Model_User $user
     * @param boolean $small
     */
    public function gravatar(Default_Model_User $user, $small = true)
    {
        $result = '<span class="gravatar user_' . $user->id . ' ' . ($small ? 'small' : 'big') . '"></span>';

        return $result;
    }

}
