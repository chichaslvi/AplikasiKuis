<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth Routes
$routes->get('/', 'Auth::login');
$routes->post('auth/doLogin', 'Auth::doLogin');


$routes->get('auth/changePassword', 'Auth::changePassword');
$routes->post('auth/updatePassword', 'Auth::updatePassword');

$routes->get('auth/logout', 'Auth::logout');

// Dashboard per Role (contoh saja, nanti bisa dikembangkan)
$routes->get('admin/dashboard', 'Dashboard::admin');
$routes->get('agent/dashboard', 'Dashboard::agent');
$routes->get('reviewer/dashboard', 'Dashboard::reviewer');




