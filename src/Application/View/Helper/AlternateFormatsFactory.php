<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class AlternateFormatsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AlternateFormats
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new AlternateFormats(
            $helpers->get(HeadLink::class),
        );
    }
}
