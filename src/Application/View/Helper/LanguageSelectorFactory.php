<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class LanguageSelectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LanguageSelector
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new LanguageSelector(
            $helpers->get(UrlParams::class),
        );
    }
}
