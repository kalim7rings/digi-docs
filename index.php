<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/vendor/autoload.php';

session_start();

// Instantiate the app
require __DIR__ . '/config/constants.php';
switch (_PROJECT_MODE){
    case 'LIVE':
        $settings = require __DIR__ . '/config/settings.php';
        break;
    default:
        $settings = require __DIR__ . '/config/local.settings.php';
        break;
}

$app = app($settings);

// Set up dependencies
require __DIR__ . '/config/dependencies.php';

// Register middleware
require __DIR__ . '/config/middleware.php';

// Register routes
require __DIR__ . '/config/routes.php';

// Run app
$app->run();
