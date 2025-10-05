<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================
// Default (root URL) diarahkan ke login
// ==================
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');   // alias biar /login bisa dipakai
$routes->get('auth/login', 'Auth::login'); // supaya redirect RoleFilter tidak 404
$routes->post('auth/doLogin', 'Auth::doLogin');
$routes->get('auth/changePassword', 'Auth::changePassword');
$routes->post('auth/updatePassword', 'Auth::updatePassword');
$routes->get('auth/logout', 'Auth::logout');


// ==================
// Admin Routes
// ==================
$routes->group('admin', ['filter' => 'rolefilter:admin'], function($routes){


    // ==================
    // Dashboard & General
    // ==================
    $routes->get('dashboard', 'Dashboard::admin');
    $routes->get('users', 'UserController::index');   // daftar user
    $routes->get('roles', 'RoleController::index');   // halaman roles
    $routes->get('reports', 'ReportController::index');        // daftar semua kuis
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1'); // detail nilai peserta
    $routes->get('report/download/(:num)', 'ReportController::download/$1');

    // ==================
    // Manajemen Kuis
    // ==================
    $routes->get('kuis', 'KuisController::index');
    $routes->get('kuis/create', 'KuisController::create');
    $routes->post('kuis/store_kuis', 'KuisController::store_kuis');

    $routes->get('kuis/edit/(:num)', 'KuisController::edit/$1');
    $routes->post('kuis/update/(:num)', 'KuisController::update/$1');

    // Action
    $routes->get('kuis/upload/(:num)', 'KuisController::upload/$1');   // ubah status jadi Active
    $routes->get('kuis/delete/(:num)', 'KuisController::delete/$1');   // hapus kuis

    // Detail & Archive
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1');
    $routes->get('kuis/detail/(:num)', 'KuisController::detail/$1');
    $routes->get('kuis/archive/(:num)', 'KuisController::archive/$1');

    // Import soal via Excel
    $routes->post('kuis/import_excel', 'SoalController::import_excel');

    // ⬅️ polling status kuis (real-time update badge di dashboard admin)
    $routes->get('kuis/pollStatus', 'KuisController::pollStatus');


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
    $routes->get('admin/roles/delete-kategori/(:num)', 'Admin\Roles::destroyKategori/$1');

    // Aktivasi kembali
    $routes->get('roles/aktifkanKategori/(:num)', 'RoleController::aktifkanKategori/$1');
    $routes->get('roles/activateKategori/(:num)', 'RoleController::activateKategori/$1'); // alias
    $routes->get('roles/aktifkanTeam/(:num)', 'RoleController::aktifkanTeam/$1');
    $routes->get('roles/activateTeam/(:num)', 'RoleController::activateTeam/$1'); // alias

    // Hapus permanen
    $routes->get('roles/destroyKategori/(:num)', 'RoleController::destroyKategori/$1');
    $routes->get('roles/destroyTeam/(:num)', 'RoleController::destroyTeam/$1');
});

// ==================
// Reviewer Routes
// ==================
$routes->group('reviewer', [
    'filter'    => 'rolefilter:reviewer',
    'namespace' => 'App\Controllers\Reviewer'
], function($routes) {
    $routes->get('dashboard', '\App\Controllers\Dashboard::reviewer');
    // Reviewer
    $routes->get('ganti-password', 'Password::index');
    $routes->post('ganti-password/update', 'Password::update');


    // Report
    $routes->get('reports', 'ReportController::index');        
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1');
    $routes->get('report/download/(:num)', 'ReportController::download/$1');

    // Kuis
    $routes->get('kuis', 'KuisController::index');
    $routes->get('kuis/detail/(:num)', 'KuisController::detail/$1');
    $routes->get('kuis/create', 'KuisController::create');
    $routes->post('kuis/store', 'KuisController::store_kuis');
    $routes->get('kuis/edit/(:num)', 'KuisController::edit/$1');
    $routes->post('kuis/update/(:num)', 'KuisController::update/$1');
    $routes->get('kuis/delete/(:num)', 'KuisController::delete/$1');
    $routes->get('kuis/archive/(:num)', 'KuisController::archive/$1');
    $routes->get('kuis/upload/(:num)', 'KuisController::upload/$1');

});


// ==================
// Agent Routes
// ==================
$routes->group('agent', ['filter' => 'rolefilter:agent'], function($routes) {
    $routes->get('dashboard', 'Agent::dashboard');
    $routes->get('soal/(:num)', 'Agent::soal/$1');
     $routes->get('ganti-password', '\App\Controllers\Password::index');
    $routes->post('ganti-password/update', '\App\Controllers\Password::update');

    // Kuis
    $routes->get('kuis', 'KuisController::agentIndex');               // daftar kuis active
    $routes->get('kuis/soal/(:num)', 'KuisController::kerjakan/$1');  // kerjakan kuis
    $routes->post('kuis/submit', 'Agent::submitKuis');                // submit jawaban

    // Riwayat & Ulangi
    $routes->get('riwayat', 'Agent::riwayat');
    $routes->get('ulangi-quiz/(:num)', 'Agent::ulangiQuiz/$1'); 
});

// ==================
// Alias untuk akses cepat (opsional)
// ==================
$routes->get('dashboard', 'Agent::dashboard'); // alias /dashboard
$routes->get('soal', 'Agent::soal');           // alias /soal
$routes->get('ulangi-quiz', 'Agent::ulangiQuiz'); // alias /ulangi-quiz
$routes->get('ulangi-quiz/(:num)', 'Agent::ulangiQuiz/$1'); // <-- DITAMBAHKAN: /ulangi-quiz/{id}
$routes->get('riwayat', 'Agent::riwayat');
$routes->get('agent/hasil/(:num)', 'Agent::hasil/$1');
$routes->get('agent/hasil/detail/(:num)', 'Agent::detailHasil/$1');

// 👉 Tambahkan alias yang kamu minta (dari blok bawah): agent/kuis/{id} → kerjakan kuis
$routes->get('agent/kuis/(:num)', 'KuisController::kerjakan/$1');
$routes->get('agent/statusKuis', 'Agent::statusKuis');
// ✅ Long-poll untuk realtime update dashboard agent saat admin mengedit kuis aktif
$routes->get('agent/statusKuisLP', 'Agent::statusKuisLP');
