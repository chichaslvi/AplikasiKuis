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

    // ==================
    // Dashboard & General
    // ==================
    $routes->get('dashboard', 'Dashboard::admin');
    $routes->get('users', 'UserController::index');   // daftar user
    $routes->get('roles', 'RoleController::index');   // halaman roles
    $routes->get('reports', 'ReportController::index');

    // ==================
    // Manajemen Kuis
    // ==================
    $routes->get('kuis', 'KuisController::index');
    $routes->get('kuis/create', 'KuisController::create');
    $routes->post('kuis/store_kuis', 'KuisController::store_kuis');
    $routes->post('kuis/import_excel', 'SoalController::import_excel');
    $routes->get('kuis/edit/(:num)', 'KuisController::edit/$1');
    $routes->post('kuis/update/(:num)', 'KuisController::update/$1');
    $routes->get('kuis/delete/(:num)', 'KuisController::delete/$1');
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1');
    $routes->get('kuis/archive/(:num)', 'KuisController::archive/$1');



    $routes->get('kuis/archive/(:num)', 'KuisController::archive/$1');
     $routes->get('kuis/detail/(:num)', 'KuisController::detail/$1');

    // ==================
    // Manajemen User
    // ==================

    // Admin & Reviewer
    $routes->get('users/create_admin', 'UserController::create_admin'); 
    $routes->post('users/store_admin', 'UserController::store_admin');  

    // Agent
    $routes->get('users/create_agent', 'UserController::create_agent'); 
    $routes->post('users/store_agent', 'UserController::store_agent');  
    $routes->get('users/edit_agent/(:num)', 'UserController::edit_agent/$1');   
    $routes->post('users/update_agent/(:num)', 'UserController::update_agent/$1'); 

    // Umum (semua user)
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');
    $routes->get('users/deactivate/(:num)', 'UserController::deactivate/$1');
    $routes->get('users/activate/(:num)', 'UserController::activate/$1');

    // Filter berdasarkan role
    $routes->get('users/role/(:alpha)', 'UserController::index/$1');

    // ==================
    // Role Management (Kategori Agent & Team Leader)
    // ==================
    $routes->get('roles/index', fn() => redirect()->to('/admin/roles'));

    // Simpan data baru
    $routes->post('roles/storeKategori', 'RoleController::storeKategori');
    $routes->post('roles/storeTeam', 'RoleController::storeTeam');

    // Nonaktifkan
    $routes->get('roles/nonaktifkanKategori/(:num)', 'RoleController::nonaktifkanKategori/$1');
    $routes->get('roles/nonaktifkanTeam/(:num)', 'RoleController::nonaktifkanTeam/$1');
    $routes->get('roles/deactivateKategori/(:num)', 'RoleController::nonaktifkanKategori/$1'); // alias
    $routes->get('roles/deactivateTeam/(:num)', 'RoleController::nonaktifkanTeam/$1'); // alias

    // Soft Delete
    $routes->get('roles/deleteKategori/(:num)', 'RoleController::deleteKategori/$1');
    $routes->get('roles/deleteTeam/(:num)', 'RoleController::deleteTeam/$1');

    // Aktivasi kembali
    $routes->get('roles/aktifkanKategori/(:num)', 'RoleController::aktifkanKategori/$1');
    $routes->get('roles/activateKategori/(:num)', 'RoleController::activateKategori/$1'); // alias
    $routes->get('roles/aktifkanTeam/(:num)', 'RoleController::aktifkanTeam/$1');
    $routes->get('roles/activateTeam/(:num)', 'RoleController::activateTeam/$1'); // alias

    // Hapus permanen
    $routes->get('roles/destroyKategori/(:num)', 'RoleController::destroyKategori/$1');
    $routes->get('roles/destroyTeam/(:num)', 'RoleController::destroyTeam/$1');
});
