<?php

namespace Application\View\Helper;

use Mezzio\LaminasView\UrlHelper;

class Footer
{
    public function __construct(
        private readonly UrlHelper $url,
    ) {}

    /**
     * Returns the website footer.
     */
    public function __invoke(): string
    {
        return '<a href="' . ($this->url)('about') . '">' . _tr('about mQueue') . '</a> ';
    }
}
