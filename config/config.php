<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

$aggregator = new ConfigAggregator([
    Mezzio\Tooling\ConfigProvider::class,
    Mezzio\LaminasView\ConfigProvider::class,
    Mezzio\Router\LaminasRouter\ConfigProvider::class,
    Laminas\Router\ConfigProvider::class,
    Laminas\HttpHandlerRunner\ConfigProvider::class,

    Laminas\I18n\ConfigProvider::class,
    Laminas\Form\ConfigProvider::class,
    Mezzio\Session\Ext\ConfigProvider::class,
    Mezzio\Session\ConfigProvider::class,
    Mezzio\Flash\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),
    Mezzio\Helper\ConfigProvider::class,
    Mezzio\ConfigProvider::class,
    Mezzio\Router\ConfigProvider::class,
    Mimmi20\Mezzio\Navigation\ConfigProvider::class,
    Mimmi20\Mezzio\Navigation\LaminasView\ConfigProvider::class,
    Laminas\Diactoros\ConfigProvider::class,

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),

    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
