<?php

declare(strict_types=1);

namespace Application\Middleware;

use Laminas\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;

class DetectBrowserLocaleFactory
{
    public function __invoke(ContainerInterface $container): DetectBrowserLocaleMiddleware
    {
        $translator = $container->get(TranslatorInterface::class);

        return new DetectBrowserLocaleMiddleware($translator);
    }
}
