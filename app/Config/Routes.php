<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================
// Auth Routes
// ==================
$routes->get('/', 'Auth::login');
$routes->post('auth/doLogin', 'Auth::doLogin');

$routes->get('auth/changePassword', 'Auth::changePassword');
$routes->post('auth/updatePassword', 'Auth::updatePassword');

$routes->get('auth/logout', 'Auth::logout');

// ==================
// Dashboard Routes
// ==================
$routes->get('admin/dashboard', 'Dashboard::admin');
$routes->get('agent/dashboard', 'Dashboard::agent');
$routes->get('reviewer/dashboard', 'Dashboard::reviewer');

// ==================
// Admin Pages (Sidebar Menu)
// ==================
$routes->get('admin/users', 'UserController::index');        // Manajemen User
$routes->get('admin/roles', 'RoleController::index');        // Manajemen Role
$routes->get('admin/soal', 'SoalController::index');         // Manajemen Soal
$routes->get('admin/reports', 'ReportController::index');    // Report Nilai
