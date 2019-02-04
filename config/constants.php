<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('_PROJECT_MODE', 'UAT'); // LIVE for production
define('HTTP_HOST', $protocol.$_SERVER['HTTP_HOST']);
define('BASE_PATH', '/');