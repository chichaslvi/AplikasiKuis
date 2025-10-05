
   
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


    $routes->group('reviewer', ['filter' => 'rolefilter:reviewer'], function($routes) {
    // Dashboard (biarin sesuai permintaanmu)
    $routes->get('dashboard', 'Dashboard::reviewer');  
    $routes->get('reports', 'ReportController::index');        
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1');
    $routes->get('report/download/(:num)', 'ReportController::download/$1');
    $routes->get('kuis', 'KuisController::index');
    $routes->get('kuis/detail/(:num)', 'KuisController::detail/$1');
    $routes->get('kuis/archive/(:num)', 'KuisController::archive/$1');
    
 $routes->get('reports', 'ReportController::index');        // daftar semua kuis
    $routes->get('report/detail/(:num)', 'ReportController::detail/$1'); // detail nilai peserta
    $routes->get('report/download/(:num)', 'ReportController::download/$1');
