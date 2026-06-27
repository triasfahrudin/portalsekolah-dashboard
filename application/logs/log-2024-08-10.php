<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2024-08-10 04:24:17 --> 404 Page Not Found: /index
ERROR - 2024-08-10 04:24:17 --> ErrorException [ 1 ]: The page you requested was not found. ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/core/MY_Exceptions.php [ 513 ]
ERROR - 2024-08-10 07:17:38 --> Severity: Warning --> Trying to access array offset on value of type null /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php 340
ERROR - 2024-08-10 07:17:38 --> ErrorException [ 8192 ]: trim(): Passing null to parameter #1 ($string) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 340 ]
ERROR - 2024-08-10 07:17:38 --> ErrorException [ 2 ]: Trying to access array offset on value of type null ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 340 ]
ERROR - 2024-08-10 07:17:38 --> ErrorException [ 8192 ]: trim(): Passing null to parameter #1 ($string) of type string is deprecated ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 340 ]
ERROR - 2024-08-10 07:45:47 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = '99490e5d-84c5-40fe-97b5-9d3ac7170bba'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '8'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2024-08-10 07:45:47 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = '99490e5d-84c5-40fe-97b5-9d3ac7170bba'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '8'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2024-08-10 07:45:52 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = '99490e5d-84c5-40fe-97b5-9d3ac7170bba'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '8'
AND ((a.dokumentasi IS NULL AND `a`.`uraian` IS NULL) OR `a`.`foto` IS NULL OR `a`.`verifikasi` = "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2024-08-10 07:45:52 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = '99490e5d-84c5-40fe-97b5-9d3ac7170bba'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '8'
AND ((a.dokumentasi IS NULL AND `a`.`uraian` IS NULL) OR `a`.`foto` IS NULL OR `a`.`verifikasi` = "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2024-08-10 08:02:38 --> Severity: Warning --> Undefined array key -1 /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/helpers/libs_helper.php 698
