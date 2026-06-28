<?php

declare(strict_types=1);

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            Doctrine\ORM\EntityManager::class => 'doctrine.entity_manager.orm_default',
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class => Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class,
            Psr\Log\LoggerInterface::class => Application\Service\Logger::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            'doctrine.entity_manager.orm_default' => Roave\PsrContainerDoctrine\EntityManagerFactory::class,
            Application\Middleware\DetectBrowserLocaleMiddleware::class => Application\Middleware\DetectBrowserLocaleFactory::class,
            Application\Middleware\AuthenticationMiddleware::class => Application\Middleware\AuthenticationFactory::class,
            Application\View\Helper\GoogleAnalytics::class => Application\View\Helper\GoogleAnalyticsFactory::class,
            Doctrine\Migrations\Configuration\Migration\ConfigurationLoader::class => Roave\PsrContainerDoctrine\Migrations\ConfigurationLoaderFactory::class,
            Doctrine\Migrations\DependencyFactory::class => Roave\PsrContainerDoctrine\Migrations\DependencyFactoryFactory::class,
            Doctrine\DBAL\Logging\Middleware::class => Application\DBAL\Logging\MiddlewareFactory::class,
        ],
        'abstract_factories' => [
            Application\Handler\PageHandlerFactory::class,
        ],
    ],
];
