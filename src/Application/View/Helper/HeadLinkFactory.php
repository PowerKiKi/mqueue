<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class HeadLinkFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): HeadLink
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new HeadLink(
            $helpers->get(CacheStamp::class),
        );
    }
}
