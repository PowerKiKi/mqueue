<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;

// Secure cookie usage
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.gc_maxlifetime', 365 * 86400);
ini_set('session.cookie_samesite', 'None');

// Load configuration
$config = require __DIR__ . '/config.php';

$dependencies = $config['dependencies'];
$dependencies['services']['config'] = $config;

// Build container
global $container;
$container = new ServiceManager($dependencies);

return $container;
