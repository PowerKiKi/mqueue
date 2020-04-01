<?php

error_reporting(E_ALL | E_STRICT);

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

require_once __DIR__ . '/../public/index.php';
