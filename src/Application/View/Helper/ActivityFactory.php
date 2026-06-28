<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class ActivityFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Activity
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new Activity(
            $helpers->get(Gravatar::class),
            $helpers->get(Movie::class),
            $helpers->get(StatusLinks::class),
            $helpers->get(UrlHelper::class),
        );
    }
}
