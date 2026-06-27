<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-01-19 16:28:40 --> 404 Page Not Found: /index
ERROR - 2025-01-19 16:28:40 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 17:28:12 --> 404 Page Not Found: /index
ERROR - 2025-01-19 17:28:12 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 17:53:29 --> Query error: Table 'presensi_2.tgl_masuk_sekolah' doesn't exist - Invalid query: SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` as `tgl_masuk_sekolah`, `g`.`nama` as `tanggal_keluar`, `h`.`nama` as `jenis_keluar`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tgl_masuk_sekolah` `f` ON `a`.`tgl_masuk_sekolah` = `f`.`id`
LEFT JOIN `tanggal_keluar` `g` ON `a`.`tanggal_keluar` = `g`.`id`
LEFT JOIN `jenis_keluar` `h` ON `a`.`jenis_keluar` = `h`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC
ERROR - 2025-01-19 17:53:29 --> ErrorException [ 1 ]: Error Number: 1146 / Table 'presensi_2.tgl_masuk_sekolah' doesn't exist / SELECT `a`.`nisn`, `a`.`nik`, `a`.`nama_lengkap`, `c`.`nama` as `Kabupaten`, `d`.`nama` AS `kecamatan`, `e`.`nama` AS `kelurahan`, `f`.`nama` as `tgl_masuk_sekolah`, `g`.`nama` as `tanggal_keluar`, `h`.`nama` as `jenis_keluar`, `a`.`alamat`
FROM `siswa` `a`
LEFT JOIN `sekolah` `b` ON `a`.`sekolah_id` = `b`.`id`
LEFT JOIN `wilayah_kabupaten` `c` ON `a`.`kabupaten` = `c`.`id`
LEFT JOIN `wilayah_kecamatan` `d` ON `a`.`kecamatan` = `d`.`id`
LEFT JOIN `wilayah_kelurahan` `e` ON `a`.`kelurahan` = `e`.`id`
LEFT JOIN `tgl_masuk_sekolah` `f` ON `a`.`tgl_masuk_sekolah` = `f`.`id`
LEFT JOIN `tanggal_keluar` `g` ON `a`.`tanggal_keluar` = `g`.`id`
LEFT JOIN `jenis_keluar` `h` ON `a`.`jenis_keluar` = `h`.`id`
WHERE `b`.`npsn` = '10500219'
ORDER BY `a`.`nama_lengkap` ASC / Filename: modules/dinas/controllers/Dinas.php / Line Number: 1548 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/dinas/controllers/Dinas.php [ 1548 ]
ERROR - 2025-01-19 19:41:30 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:30 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:32 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:32 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:33 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:33 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:33 --> Severity: Warning --> Undefined array key "HTTP_ACCEPT_ENCODING" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 6
ERROR - 2025-01-19 19:41:33 --> ErrorException [ 8192 ]: substr_count(): Passing null to parameter #1 ($haystack) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php [ 6 ]
ERROR - 2025-01-19 19:41:33 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 19:41:33 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:34 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:34 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:35 --> Severity: Warning --> Undefined array key "HTTP_ACCEPT_ENCODING" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 6
ERROR - 2025-01-19 19:41:35 --> ErrorException [ 8192 ]: substr_count(): Passing null to parameter #1 ($haystack) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php [ 6 ]
ERROR - 2025-01-19 19:41:35 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 19:41:35 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:37 --> Severity: Warning --> Undefined array key "HTTP_ACCEPT_ENCODING" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 6
ERROR - 2025-01-19 19:41:37 --> ErrorException [ 8192 ]: substr_count(): Passing null to parameter #1 ($haystack) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php [ 6 ]
ERROR - 2025-01-19 19:41:37 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 19:41:37 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:38 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:38 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:38 --> Severity: Warning --> Undefined array key "HTTP_ACCEPT_ENCODING" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 6
ERROR - 2025-01-19 19:41:38 --> ErrorException [ 8192 ]: substr_count(): Passing null to parameter #1 ($haystack) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php [ 6 ]
ERROR - 2025-01-19 19:41:38 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 19:41:38 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:42 --> Severity: Warning --> Undefined array key "HTTP_ACCEPT_ENCODING" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 6
ERROR - 2025-01-19 19:41:42 --> ErrorException [ 8192 ]: substr_count(): Passing null to parameter #1 ($haystack) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php [ 6 ]
ERROR - 2025-01-19 19:41:42 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 19:41:42 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 19:41:42 --> 404 Page Not Found: /index
ERROR - 2025-01-19 19:41:42 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2025-01-19 20:10:48 --> 404 Page Not Found: ../modules/signin/controllers/Signin/wp_login.php
ERROR - 2025-01-19 20:10:48 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
