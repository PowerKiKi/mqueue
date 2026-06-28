<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

final class FlashMessengerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FlashMessenger
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new FlashMessenger(
            $helpers->get(EscapeHtml::class),
            $helpers->get(EscapeHtmlAttr::class),
        );
    }
}
