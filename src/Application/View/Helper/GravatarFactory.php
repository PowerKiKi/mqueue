<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\HtmlAttributes;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class GravatarFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Gravatar
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new Gravatar(
            $helpers->get(HtmlAttributes::class),
        );
    }
}
