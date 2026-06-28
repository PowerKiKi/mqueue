<?php

namespace Application\View\Helper;

use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\Flash\FlashMessages;
use Mezzio\Session\Session;

/**
 * View Helper to Display Flash Messages.
 */
class FlashMessenger
{
    public function __construct(
        private readonly EscapeHtml $escapeHtml,
        private readonly EscapeHtmlAttr $escapeHtmlAttr,
    ) {}

    public function __invoke(): string
    {
        $session = session_status() === PHP_SESSION_ACTIVE ? $_SESSION : [];

        $flashMessages = FlashMessages::createFromSession(new Session($session));

        $result = '';
        foreach ($flashMessages->getFlashes() as $level => $message) {
            $result .= '<div class="flashmessenger ' . ($this->escapeHtmlAttr)($level) . '">' . ($this->escapeHtml)($message) . '</div>';
        }

        return $result;
    }
}
