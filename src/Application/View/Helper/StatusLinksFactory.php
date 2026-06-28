<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\HelperPluginManager;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class StatusLinksFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): StatusLinks
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new StatusLinks(
            $helpers->get(ServerUrl::class),
            $helpers->get(UrlHelper::class),
        );
    }
}
