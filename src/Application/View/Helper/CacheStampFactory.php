<?php

namespace Application\View\Helper;

use Psr\Container\ContainerInterface;

class CacheStampFactory

{
    /**
     * Return a configured invoicer.
     */
    public function __invoke(ContainerInterface $container): CacheStamp
    {
        $config = $container->get('config');

        return new CacheStamp($config['minimize']);
    }
}
