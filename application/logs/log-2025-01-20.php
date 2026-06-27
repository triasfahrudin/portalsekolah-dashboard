<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-01-20 09:22:41 --> Query error: Table 'presensi_2.tanggal_keluar' doesn't exist - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_keluar` `f` ON `a`.`tanggal_keluar` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-20 09:22:41 --> ErrorException [ 1 ]: Error Number: 1146 / Table 'presensi_2.tanggal_keluar' doesn't exist / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_keluar` `f` ON `a`.`tanggal_keluar` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1541 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1541 ]
ERROR - 2025-01-20 09:23:13 --> Query error: Unknown column 'f.nama' in 'field list' - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-20 09:23:13 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'f.nama' in 'field list' / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1541 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1541 ]
ERROR - 2025-01-20 09:23:54 --> Query error: Unknown column 'f.tanggal_keluar' in 'field list' - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-20 09:23:54 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'f.tanggal_keluar' in 'field list' / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`tanggal_keluar`, `a`.`tgl_masuk_sekolah`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1541 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1541 ]
ERROR - 2025-01-20 11:05:51 --> 404 Page Not Found: /index
ERROR - 2025-01-20 11:05:51 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-20 11:43:44 --> 404 Page Not Found: /index
ERROR - 2025-01-20 11:43:44 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-20 12:32:28 --> 404 Page Not Found: /index
ERROR - 2025-01-20 12:32:28 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
