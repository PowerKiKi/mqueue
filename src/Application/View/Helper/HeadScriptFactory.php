<?php

namespace Application\View\Helper;

use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

class HeadScriptFactory
{
    /**
     * Return a configured invoicer.
     */
    public function __invoke(ContainerInterface $container): HeadScript
    {
        $config = $container->get('config');
        $helpers = $container->get(HelperPluginManager::class);

        return new HeadScript(
            $config['minimize'],
            $helpers->get(CacheStamp::class),
        );
    }
}
