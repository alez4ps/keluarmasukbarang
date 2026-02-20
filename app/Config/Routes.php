<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::process');
$routes->post('/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');

$routes->group('Admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard', 'DAdmin::index');
});

$routes->group('Petugas', ['filter' => 'role:petugas'], function($routes) {
    $routes->get('dashboard', 'DPetugas::index');
});

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/users', 'Users::index');
    $routes->get('/users/create', 'Users::create');
    $routes->post('/users/store', 'Users::store');
    $routes->get('/users/edit/(:num)', 'Users::edit/$1');
    $routes->post('/users/update/(:num)', 'Users::update/$1');
    $routes->get('/users/delete/(:num)', 'Users::delete/$1');
    $routes->get('users/print', 'Users::print');
});

$routes->get('registrasi', 'Barang::index');
$routes->post('registrasi/store', 'Barang::store');
$routes->get('registrasi/edit/(:num)', 'Barang::edit/$1');
$routes->post('registrasi/update/(:num)', 'Barang::update/$1');
$routes->get('registrasi/delete/(:num)', 'Barang::delete/$1');
$routes->get('registrasi/selesai/(:num)', 'Barang::selesai/$1');
$routes->get('registrasi/masuk/(:num)', 'Barang::masuk/$1');
$routes->post('registrasi/prosesMasuk/(:num)', 'Barang::prosesMasuk/$1');
$routes->get('registrasi/masukLangsung/(:num)', 'Barang::masukLangsung/$1');
$routes->get('registrasi/masukTidakKembali/(:num)', 'Barang::masukTidakKembali/$1');
$routes->post('registrasi/prosesMasukTidakKembali/(:num)', 'Barang::prosesMasukTidakKembali/$1');
$routes->get('registrasi/keluar/(:num)', 'Barang::keluar/$1');
$routes->post('registrasi/prosesKeluar/(:num)', 'Barang::prosesKeluar/$1');
$routes->get('registrasi/prosesKeluarLangsung/(:num)', 'Barang::prosesKeluarLangsung/$1');
$routes->get('registrasi/kembali/(:num)', 'Barang::kembali/$1');
$routes->post('registrasi/prosesKembali/(:num)', 'Barang::prosesKembali/$1');
$routes->get('registrasi/kembaliLangsung/(:num)', 'Barang::kembaliLangsung/$1');

$routes->group('logs', function($routes) {
    $routes->get('/', 'BarangLog::index');
    $routes->get('export', 'BarangLog::export');
    $routes->get('barang/(:num)', 'BarangLog::detailBarang/$1');
    $routes->get('laptop/(:num)', 'BarangLog::detailLaptop/$1');
    $routes->post('delete-barang/(:num)', 'BarangLog::deleteBarangLog/$1');
    $routes->post('delete-laptop/(:num)', 'BarangLog::deleteLaptopLog/$1');
});

$routes->get('/barang/laptop', 'Barang::laptop');
$routes->get('/barang/searchLaptop', 'Barang::searchLaptop');
$routes->post('/barang/laptop/store', 'Barang::storeLaptop');
$routes->get('/barang/laptop/edit/(:num)', 'Barang::editLaptop/$1');
$routes->post('/barang/laptop/update/(:num)', 'Barang::updateLaptop/$1');
$routes->get('/barang/laptop/delete/(:num)', 'Barang::deleteLaptop/$1');
$routes->get('/barang/laptop/detail/(:num)', 'Barang::detailLaptop/$1');
$routes->get('/barang/laptop/export', 'Barang::exportLaptop');
$routes->post('/barang/laptop/perpanjang', 'Barang::perpanjangLaptop');
$routes->post('/barang/laptop/change-status/(:num)', 'Barang::changeLaptopStatus/$1');
$routes->get('/barang/laptop/create', 'Barang::createLaptop');
$routes->get('/barang/laptop/print/(:num)', 'Barang::printLaptop/$1');
$routes->get('/barang/laptop/generate-qr/(:num)', 'Barang::generateQR/$1');