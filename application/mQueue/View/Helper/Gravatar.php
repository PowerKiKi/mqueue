<?php

namespace mQueue\View\Helper;

use mQueue\Model\User;
use Zend_View_Helper_HtmlElement;

class Gravatar extends Zend_View_Helper_HtmlElement
{
    /**
     * Return a span with CSS class for a user on gravatar
     *
     * @param User $user
     * @param string $size small|medium|big
     *
     * @return string
     */
    public function gravatar(User $user, $size = 'small'): string
    {
        $attribs = [
            'class' => 'gravatar user_' . $user->id . ' ' . $size,
            'title' => $user->nickname,
        ];
        $result = '<span' . $this->_htmlAttribs($attribs) . '"></span>';

        return $result;
    }
}
