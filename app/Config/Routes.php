<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api/auth', function ($routes) {
    $routes->post('login', 'AuthController::login');
    $routes->post('refresh', 'AuthController::refresh');
    $routes->post('logout', 'AuthController::logout');
    $routes->post('validate', 'AuthController::validateToken');
});
