<?php

namespace Application\View\Helper;

class GoogleAnalytics
{
    public function __construct(
        private readonly string $trackingCode,
    ) {}

    /**
     * Returns javascript code for Google Analytics.
     */
    public function __invoke(): string
    {
        if (!$this->trackingCode) {
            return '';
        }

        $result = <<<STRING
            <script async src="https://www.googletagmanager.com/gtag/js?id=$this->trackingCode"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '$this->trackingCode');
            </script>
            STRING;

        return $result;
    }
}
