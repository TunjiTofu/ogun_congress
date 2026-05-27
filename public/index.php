<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

//define('LARAVEL_START', microtime(true));
//
//// Determine if the application is in maintenance mode...
//if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
//    require $maintenance;
//}
//
//// Register the Composer autoloader...
//require __DIR__.'/../vendor/autoload.php';
//
//// Bootstrap Laravel and handle the request...
///** @var Application $app */
//$app = require_once __DIR__.'/../bootstrap/app.php';
//
//$app->handleRequest(Request::capture());



define('LARAVEL_START', microtime(true));

// Fix Authorization header stripping on cPanel/Apache CGI/FastCGI.
// Apache strips the Authorization header before passing to PHP in some configs.
// This restores it from multiple fallback sources.
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        // Set by mod_rewrite in some configs
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $requestHeaders['Authorization'];
        } elseif (isset($requestHeaders['authorization'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $requestHeaders['authorization'];
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
