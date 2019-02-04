<?php
// DIC configuration
$container = $app->getContainer();

// view renderer view
/*$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};*/
// Register Blade View helper
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    return new \Slim\Views\Blade(
        $settings['template_path'],
        $settings['cache_path']
    );
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, 'error', ['status' => 404, 'message' => 'Page Not Found']);
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, 'error', ['status' => 405, 'message' => 'Not Allowed']);
    };
};

$container['phpErrorHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, 'error', ['status' => 500, 'message' => 'Internal Server Error']);
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, 'error', ['status' => 500, 'message' => 'Internal Server Error']);
    };
};

// Request Validator
$container['validator'] = function ($c) {
    \Respect\Validation\Validator::with('\\App\\Validation\\Rules');

    return new App\Http\Validation\Validator();
};

//Guzzle Http Client
$container['guzzle'] = function($c){
    return new \GuzzleHttp\Client();
};

// Slim Session
$container['session'] = function ($c) {
    return new \SlimSession\Helper;
};