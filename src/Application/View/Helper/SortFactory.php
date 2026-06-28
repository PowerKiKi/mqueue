<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class SortFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Sort
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new Sort(
            $helpers->get(EscapeHtml::class),
            $helpers->get(EscapeHtmlAttr::class),
            $helpers->get(UrlParams::class),
        );
    }
}
