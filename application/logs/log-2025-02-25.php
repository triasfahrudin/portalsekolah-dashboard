<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-02-25 14:56:29 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2025'
AND MONTH(a.tgl) = '1'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-25 14:56:29 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2025'
AND MONTH(a.tgl) = '1'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2025-02-25 14:56:33 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '1'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-25 14:56:33 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '1'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2025-02-25 14:56:38 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '12'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-25 14:56:38 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'ca6e0779-aab5-4a51-b32f-6f2100353d45'
AND YEAR(a.tgl) = '2024'
AND MONTH(a.tgl) = '12'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
ERROR - 2025-02-25 23:19:11 --> Query error: Unknown column 'a.foto' in 'where clause' - Invalid query: SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'e5331d68-a305-4e11-87d9-02c9cb52b03d'
AND YEAR(a.tgl) = '2025'
AND MONTH(a.tgl) = '2'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10
ERROR - 2025-02-25 23:19:11 --> ErrorException [ 1 ]: Error Number: 1054 / Unknown column 'a.foto' in 'where clause' / SELECT `a`.`id`, `c`.`nama_lengkap`, DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl, `b`.`hari`, `b`.`jam_mulai`, `b`.`jam_selesai`, `d`.`nama_kelas`, `e`.`nama` AS `matapelajaran`, IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak", "terima", "tolak") AS status
FROM `guru_mengajar` `a`
LEFT JOIN `jadwal_mengajar` `b` ON `a`.`jadwal_mengajar_id` = `b`.`id`
LEFT JOIN `pegawai` `c` ON `b`.`pegawai_id` = `c`.`id`
LEFT JOIN `kelas` `d` ON `b`.`kelas_id` = `d`.`id`
LEFT JOIN `matapelajaran` `e` ON `b`.`matapelajaran_id` = `e`.`id`
WHERE `c`.`sekolah_id` = 'e5331d68-a305-4e11-87d9-02c9cb52b03d'
AND YEAR(a.tgl) = '2025'
AND MONTH(a.tgl) = '2'
AND ((a.dokumentasi IS NOT NULL OR `a`.`uraian` IS NOT NULL) AND `a`.`foto` IS NOT NULL AND `a`.`verifikasi` != "tolak")
ORDER BY `nama_lengkap` ASC
 LIMIT 10 / Filename: libraries/Datatables.php / Line Number: 376 ~ /www/wwwroot/tpp.disdik.jambiprov.go.id/pppk/application/libraries/Datatables.php [ 376 ]
