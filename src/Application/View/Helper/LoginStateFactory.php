<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class LoginStateFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginState
    {
        $helpers = $container->get(HelperPluginManager::class);

        return new LoginState(
            $helpers->get(EscapeHtml::class),
            $helpers->get(Gravatar::class),
            $helpers->get(UrlHelper::class),
        );
    }
}
