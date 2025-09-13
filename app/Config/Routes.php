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
    $routes->post('kuis/store_kuis', 'KuisController::store_kuis');
    $routes->post('kuis/import_excel', 'SoalController::import_excel');

<<<<<<< Updated upstream
    
=======
>>>>>>> Stashed changes

    // Tambah Admin & Reviewer
    $routes->get('users/create_admin', 'UserController::create_admin'); // form tambah
    $routes->post('users/store_admin', 'UserController::store_admin'); // simpan data

    // Tambah Agent
    $routes->get('users/create_agent', 'UserController::create_agent'); // form tambah agent
    $routes->post('users/store_agent', 'UserController::store_agent');  // simpan agent

    // Edit, Update & Delete User
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1'); // route update
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');

    // **Tambahkan route khusus agent**
    $routes->get('users/edit_agent/(:num)', 'UserController::edit_agent/$1');
    $routes->post('users/update_agent/(:num)', 'UserController::update_agent/$1');

    // Filter berdasarkan role
    $routes->get('users/role/(:alpha)', 'UserController::index/$1');

    // Redirect roles/index
    $routes->get('roles/index', function () {
        return redirect()->to('/admin/roles');
    });

    // 👉 Tambahan baru untuk RoleController
   // 👉 Tambahan baru untuk RoleController
$routes->post('roles/storeKategori', 'RoleController::storeKategori');
$routes->post('roles/storeTeam', 'RoleController::storeTeam');

$routes->get('roles/deleteKategori/(:num)', 'RoleController::deleteKategori/$1');
$routes->get('roles/deleteTeam/(:num)', 'RoleController::deleteTeam/$1');

// 🔥 Route aktivasi
$routes->get('roles/activateKategori/(:num)', 'RoleController::activateKategori/$1');
$routes->get('roles/activateTeam/(:num)', 'RoleController::activateTeam/$1');

});

