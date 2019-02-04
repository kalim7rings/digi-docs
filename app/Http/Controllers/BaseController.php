<?php

namespace App\Http\Controllers;

use Psr\Container\ContainerInterface;

class BaseController {

    protected $settings;

    protected $csrf;

    protected $logger;

    protected $view;

    protected $validator;

    protected $guzzle;

    protected $session;

    public function __construct(ContainerInterface $container)
    {
        $this->settings = $container->get('settings')['app'];
        $this->csrf = $container->csrf;
        $this->logger = $container->logger;
        $this->view = $container->view;
        $this->validator = $container->validator;
        $this->guzzle = $container->guzzle;
        $this->session = $container->session;
    }
}