<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-01-16 10:24:24 --> Query error: Table 'presensi_2.nama_rombel' doesn't exist - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `nama_rombel`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `nama_rombel` `f` ON `a`.`nama_rombel` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-16 10:24:24 --> ErrorException [ 1 ]: Error Number: 1146 / Table 'presensi_2.nama_rombel' doesn't exist / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `nama_rombel`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `nama_rombel` `f` ON `a`.`nama_rombel` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1539 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1539 ]
ERROR - 2025-01-16 11:15:37 --> Query error: Table 'presensi_2.tanggal_masuk_sekolah' doesn't exist - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_masuk_sekolah`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_masuk_sekolah` `f` ON `a`.`tanggal_masuk_sekolah` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-16 11:15:37 --> ErrorException [ 1 ]: Error Number: 1146 / Table 'presensi_2.tanggal_masuk_sekolah' doesn't exist / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_masuk_sekolah`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_masuk_sekolah` `f` ON `a`.`tanggal_masuk_sekolah` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1540 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1540 ]
ERROR - 2025-01-16 12:17:23 --> 404 Page Not Found: /index
ERROR - 2025-01-16 12:17:23 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-16 11:26:52 --> Query error: Table 'presensi_2.tanggal_keluar' doesn't exist - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_keluar` `f` ON `a`.`tanggal_keluar` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-16 11:26:52 --> ErrorException [ 1 ]: Error Number: 1146 / Table 'presensi_2.tanggal_keluar' doesn't exist / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` AS `tanggal_keluar`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tanggal_keluar` `f` ON `a`.`tanggal_keluar` = `f`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1540 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1540 ]
ERROR - 2025-01-16 18:44:13 --> Severity: Warning --> Trying to access array offset on value of type int /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php 484
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 485 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 486 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 487 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 488 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 489 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 491 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 492 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 493 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 494 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 495 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 496 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 497 ]
ERROR - 2025-01-16 18:44:13 --> ErrorException [ 2 ]: Trying to access array offset on value of type int ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/api_helper.php [ 498 ]
