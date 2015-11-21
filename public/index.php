<?php

// Keep session data for 120 days, unless explicit destroy in code
ini_set('session.gc_maxlifetime', 1 * 60 * 60 * 24 * 120);

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

if (@$_SERVER['HTTP_HOST'] == 'localhost') {
    define('APPLICATION_ENV', 'development');
}

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
]));

/** Zend_Application */
require_once APPLICATION_PATH . '/Debug.php';
require_once APPLICATION_PATH . '/../vendor/autoload.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

// we only run the application if this file were NOT included (otherwise, the file was included to access misc functions)
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    $application->run();
}
