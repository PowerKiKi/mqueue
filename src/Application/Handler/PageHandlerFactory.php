<?php

declare(strict_types=1);

namespace Application\Handler;

use Application\View\Helper\StatusLinks;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\View\HelperPluginManager;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

final class PageHandlerFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return is_a($requestedName, PageHandler::class, true);
    }

    /**
     * @template T of PageHandler
     *
     * @param class-string<T> $requestedName
     *
     * @return T
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageHandler
    {
        $router = $container->get(RouterInterface::class);
        $template = $container->get(TemplateRendererInterface::class);
        $helpers = $container->get(HelperPluginManager::class);

        return new $requestedName(
            $router,
            $template,
            $helpers->get(ServerUrlHelper::class),
            $helpers->get(UrlHelper::class),
            $helpers->get(StatusLinks::class),
        );
    }
}
