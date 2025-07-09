<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We GET a performance increase by specifying the default
// route since we don't have to scan directories.

// Rute Publik
$routes->GET('/', 'Home::index');
$routes->GET('/product/(:segment)', 'Home::product/$1');

// Rute Setup Awal (hanya bisa diakses jika belum ada admin)
$routes->match(['GET', 'POST'], '/setup', 'Setup::index');

// Rute Admin
$routes->GET('/admin', 'Admin\Admin::index'); // Redirect ke dashboard jika sudah login
$routes->GET('/admin/login', 'Admin\Admin::login');
$routes->POST('/admin/login', 'Admin\Admin::authenticate');
$routes->GET('/admin/logout', 'Admin\Admin::logout');

// Grup Rute Admin yang Dilindungi Filter
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->GET('dashboard', 'Admin\Admin::dashboard');

    // Rute untuk Konfigurasi Website
    $routes->GET('settings', 'Admin\Admin::settings');
    $routes->POST('settings', 'Admin\Admin::updateSettings');
    $routes->POST('settings/upload-logo', 'Admin\Admin::uploadLogo');
    $routes->POST('settings/upload-hero', 'Admin\Admin::uploadHero');

    // Rute untuk CRUD Produk
    $routes->GET('products', 'Admin\Product::index');
    $routes->GET('products/new', 'Admin\Product::new');
    $routes->POST('products/create', 'Admin\Product::create');
    $routes->GET('products/edit/(:num)', 'Admin\Product::edit/$1');
    $routes->POST('products/update/(:num)', 'Admin\Product::update/$1');
    $routes->GET('products/delete/(:num)', 'Admin\Product::delete/$1');
});

// ================= RUTE BARU =================
// Tambahkan rute untuk aksi reset, dilindungi oleh filter auth
$routes->POST('/setup/reset', 'Setup::reset', ['filter' => 'auth']);
// ============================================

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
