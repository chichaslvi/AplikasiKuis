<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================
// Default (root URL) diarahkan ke login
// ==================
$routes->get('/', 'Auth::login');
$routes->post('auth/doLogin', 'Auth::doLogin');
$routes->get('auth/changePassword', 'Auth::changePassword');
$routes->post('auth/updatePassword', 'Auth::updatePassword');
$routes->get('auth/logout', 'Auth::logout');

// ==================
// Admin Routes
// ==================
$routes->group('admin', function($routes) {
    // Dashboard & lainnya
    $routes->get('dashboard', 'Dashboard::admin');
    $routes->get('users', 'UserController::index'); // daftar user
    $routes->get('roles', 'RoleController::index'); 
    $routes->get('kuis', 'KuisController::index');
    $routes->get('reports', 'ReportController::index');
    $routes->get('kuis/create', 'KuisController::create');
    $routes->post('kuis/store', 'KuisController::store');

    // Tambah Admin & Reviewer
    $routes->get('users/create_admin', 'UserController::create_admin'); // form tambah
    $routes->post('users/store_admin', 'UserController::store_admin'); // simpan data

    // Tambah Agent
$routes->get('users/create_agent', 'UserController::create_agent'); // form tambah agent
$routes->post('users/store_agent', 'UserController::store_agent');  // simpan agent

    // Redirect roles/index
    $routes->get('roles/index', function () {
        return redirect()->to('/admin/roles');
    });
});
