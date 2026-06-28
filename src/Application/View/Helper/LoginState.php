<?php

namespace Application\View\Helper;

use Application\Model\User;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\LaminasView\UrlHelper;

class LoginState
{
    public function __construct(
        private readonly EscapeHtml $escapeHtml,
        private readonly Gravatar $gravatar,
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns a string displaying the login state of the user and buttons to login/off.
     */
    public function __invoke(): string
    {
        $result = '<div class="loginState">';

        $user = User::getCurrent();
        if ($user) {
            $result .= '<a href="' . ($this->url)('user.view', ['id' => $user->id]) . '">' . ($this->gravatar)($user) . ' ' . ($this->escapeHtml)($user->nickname) . '</a> ';
            $result .= '<a href="' . ($this->url)('user.logout') . '">' . _tr('Logout') . '</a> ';
        } else {
            $result .= ' <a href="' . ($this->url)('user.new') . '">' . _tr('Subscribe') . '</a> ';
            $result .= '<a href="' . ($this->url)('user.login') . '">' . _tr('Login') . '</a>';
        }

        return $result . '</div>';
    }
}
