<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keeps the global namespace clean.
 */
(function (): void {
    /** @var Psr\Container\ContainerInterface $container */
    $container = require 'config/container.php';

    global $app;
    /** @var Mezzio\Application $app */
    $app = $container->get(Mezzio\Application::class);
    $factory = $container->get(Mezzio\MiddlewareFactory::class);

    // Execute programmatic/declarative middleware pipeline and routing
    // configuration statements
    (require 'config/pipeline.php')($app, $factory, $container);
    (require 'config/routes.php')($app, $factory, $container);

    // we only run the application if this file was NOT included (otherwise, the file was included to access misc functions)
    if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
        $app->run();
    }
})();
