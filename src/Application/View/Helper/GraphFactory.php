<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class GraphFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Graph
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new Graph(
            $helpers->get(HeadScript::class),
            $helpers->get(UrlHelper::class),
        );
    }
}
