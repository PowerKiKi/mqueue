<?php

namespace Application\View\Helper;

use Psr\Container\ContainerInterface;

class GoogleAnalyticsFactory
{
    /**
     * Return a configured invoicer.
     */
    public function __invoke(ContainerInterface $container): GoogleAnalytics
    {
        $config = $container->get('config');

        return new GoogleAnalytics($config['googleAnalyticsTrackingCode']);
    }
}
