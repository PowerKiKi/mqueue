<?php

namespace Application\View\Helper;

use Application\Model\User;
use Laminas\View\Helper\HtmlAttributes;

class Gravatar
{
    public function __construct(
        private readonly HtmlAttributes $htmlAttributes,
    ) {}

    /**
     * Return a span with CSS class for a user on gravatar.
     *
     * @param 'big'|'medium'|'small' $size
     */
    public function __invoke(User $user, string $size = 'small'): string
    {
        $attribs = [
            'class' => 'gravatar user_' . $user->id . ' ' . $size,
            'title' => $user->nickname,
        ];
        $result = '<span' . ($this->htmlAttributes)($attribs) . '"></span>';

        return $result;
    }
}
