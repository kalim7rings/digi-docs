<?php
// Application middleware
$app->add(new \App\Http\Middleware\CsrfResponseHeader(new \Slim\Csrf\Guard));
