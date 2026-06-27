<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auto routing dimatikan agar hanya route eksplisit yang bisa diakses.
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

$routes->get('/pegawai', 'Home::pegawai');
$routes->get('/kabupaten_pegawai/(:num)', 'Home::kabupaten_pegawai/$1');
$routes->get('/kecamatan_pegawai/(:num)', 'Home::kecamatan_pegawai/$1');
$routes->get('/sekolah_pegawai/(:num)', 'Home::sekolah_pegawai/$1');

$routes->get('/sekolah', 'Home::sekolah');
$routes->get('/kabupaten_sekolah/(:num)', 'Home::kabupaten_sekolah/$1');
$routes->get('/kecamatan_sekolah/(:num)', 'Home::kecamatan_sekolah/$1');
$routes->get('/data_sekolah/(:num)', 'Home::data_sekolah/$1');

$routes->get('/siswa', 'Home::siswa');
$routes->get('/kabupaten_siswa/(:num)', 'Home::kabupaten_siswa/$1');
$routes->get('/kecamatan_siswa/(:num)', 'Home::kecamatan_siswa/$1');
$routes->get('/sekolah_siswa/(:num)', 'Home::sekolah_siswa/$1');

$routes->get('/siswa_akhir', 'Home::siswa_akhir');
$routes->get('/kabupaten_siswa_akhir/(:num)', 'Home::kabupaten_siswa_akhir/$1');
$routes->get('/kecamatan_siswa_akhir/(:num)', 'Home::kecamatan_siswa_akhir/$1');
$routes->get('/sekolah_siswa_akhir/(:num)', 'Home::sekolah_siswa_akhir/$1');

$routes->get('/siswa_tidak_sekolah', 'Home::siswa_tidak_sekolah');
$routes->get('/kabupaten_siswa_tidak_sekolah/(:num)', 'Home::kabupaten_siswa_tidak_sekolah/$1');
$routes->get('/kecamatan_siswa_tidak_sekolah/(:num)', 'Home::kecamatan_siswa_tidak_sekolah/$1');
