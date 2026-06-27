<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-02-21 17:26:51 --> Severity: Warning --> Undefined array key "USIA_PENSIUN" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 525
ERROR - 2025-02-21 17:26:51 --> Severity: Warning --> Undefined array key "USIA_PENSIUN" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 525
ERROR - 2025-02-21 17:26:51 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ' '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10' at line 4 - Invalid query: SELECT `pegawai`.`nama_lengkap`, `pegawai`.`tgl_lahir`, `sekolah`.`level`, `sekolah`.`nama` AS `nama_sekolah`
FROM `pegawai`
LEFT JOIN `sekolah` ON `pegawai`.`sekolah_id` = `sekolah`.`id`
WHERE DATE_FORMAT(CURDATE() + INTERVAL 0 YEAR, '%Y') = DATE_FORMAT(pegawai.tgl_lahir + INTERVAL  YEAR, '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-21 17:26:51 --> ErrorException [ 1 ]: Error Number: 1064 / You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ' '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10' at line 4 / SELECT `pegawai`.`nama_lengkap`, `pegawai`.`tgl_lahir`, `sekolah`.`level`, `sekolah`.`nama` AS `nama_sekolah`
FROM `pegawai`
LEFT JOIN `sekolah` ON `pegawai`.`sekolah_id` = `sekolah`.`id`
WHERE DATE_FORMAT(CURDATE() + INTERVAL 0 YEAR, '%Y') = DATE_FORMAT(pegawai.tgl_lahir + INTERVAL  YEAR, '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2025-02-21 17:27:02 --> Severity: Warning --> Undefined array key "USIA_PENSIUN" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 525
ERROR - 2025-02-21 17:27:02 --> Severity: Warning --> Undefined array key "USIA_PENSIUN" /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/modules/signin/controllers/Signin.php 525
ERROR - 2025-02-21 17:27:02 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ' '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10' at line 4 - Invalid query: SELECT `pegawai`.`nama_lengkap`, `pegawai`.`tgl_lahir`, `sekolah`.`level`, `sekolah`.`nama` AS `nama_sekolah`
FROM `pegawai`
LEFT JOIN `sekolah` ON `pegawai`.`sekolah_id` = `sekolah`.`id`
WHERE DATE_FORMAT(CURDATE() + INTERVAL 0 YEAR, '%Y') = DATE_FORMAT(pegawai.tgl_lahir + INTERVAL  YEAR, '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-21 17:27:02 --> ErrorException [ 1 ]: Error Number: 1064 / You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ' '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10' at line 4 / SELECT `pegawai`.`nama_lengkap`, `pegawai`.`tgl_lahir`, `sekolah`.`level`, `sekolah`.`nama` AS `nama_sekolah`
FROM `pegawai`
LEFT JOIN `sekolah` ON `pegawai`.`sekolah_id` = `sekolah`.`id`
WHERE DATE_FORMAT(CURDATE() + INTERVAL 0 YEAR, '%Y') = DATE_FORMAT(pegawai.tgl_lahir + INTERVAL  YEAR, '%Y')
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
