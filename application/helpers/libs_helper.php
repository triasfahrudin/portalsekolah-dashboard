<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * https://gist.github.com/milesich/6240132
 * Calculate geodesic distance (in meters) between two points specified by
 * latitude/longitude using Vincenty inverse formula for ellipsoids
 *
 * from: Vincenty inverse formula - T Vincenty, "Direct and Inverse
 * Solutions of Geodesics on the Ellipsoid with application of nested
 * equations", Survey Review, vol XXII no 176, 1975
 * http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf
 *
 * Ported from JavaScript to PHP Martin Milesich - http://milesich.com
 *
 * Original JavaScript version
 * http://www.movable-type.co.uk/scripts/latlong-vincenty.html
 *
 * @param float $lat1 in form 52.2166667
 * @param float $lat2 in form 52.35
 * @param float $lon1 in form 5.9666667
 * @param float $lon2 in form 4.9166667
 * @return float      in form 73.174873 (meters)
 */
function _distVincenty($lat1, $lon1, $lat2, $lon2)
{
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    $lon1 = deg2rad($lon1);
    $lon2 = deg2rad($lon2);

    $a = 6378137;
    $b = 6356752.3142;
    $f = 1 / 298.257223563; // WGS-84 ellipsoid

    $L = $lon2 - $lon1;

    $U1 = atan((1 - $f) * tan($lat1));
    $U2 = atan((1 - $f) * tan($lat2));

    $sinU1 = sin($U1);
    $cosU1 = cos($U1);
    $sinU2 = sin($U2);
    $cosU2 = cos($U2);

    $lambda  = $L;
    $lambdaP = 2 * M_PI;

    $iterLimit = 20;

    while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0) {
        $sinLambda = sin($lambda);
        $cosLambda = cos($lambda);
        $sinSigma  = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) +
            ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) *
            ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

        if ($sinSigma == 0) {
            return 0;
        }
        // co-incident points

        $cosSigma   = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
        $sigma      = atan2($sinSigma, $cosSigma); // was atan2
        $alpha      = asin($cosU1 * $cosU2 * $sinLambda / $sinSigma);
        $cosSqAlpha = cos($alpha) * cos($alpha);
        $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
        $C          = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
        $lambdaP    = $lambda;
        $lambda     = $L + (1 - $C) * $f * sin($alpha) *
            ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma *
                (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
    }
    if ($iterLimit == 0) {
        return false;
    }
    // formula failed to converge

    $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
    $A   = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
    $B   = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));

    $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) -
        $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));

    $s = $b * $A * ($sigma - $deltaSigma);

    $s = round($s, 3); // round to 1mm precision

    return $s;
}

function fix_namafile($nama_file)
{
    // Memisahkan nama file berdasarkan tanda "-"
    $delimiter = '-';
    $parts     = explode($delimiter, $nama_file);

    if (count($parts) > 2) {
        //pegawai-69979825-SMAN_14_SAROLANGUN.xlsx'
        // Mengambil bagian pertama sebagai prefiks (SMAN_14_SAROLANGUN)
        $npsn = $parts[1];
        $nama = $parts[2];

        $info = pathinfo($nama);

        // Mengambil bagian nama file tanpa ekstensi
        $nama_sekolah = $info['filename'];

        // Menggabungkan bagian-bagian nama file dengan urutan yang diinginkan
        $nama_file_baru = $nama_sekolah . $delimiter . $npsn . '.xlsx';
    }

    return $nama_file_baru;
}

function zipFolder($Path, $fileName)
{

    // Buat array untuk menyimpan nama file
    $files = array();

    // Buka direktori
    $dir = opendir($Path);

    // Baca semua file dalam direktori
    while (($file = readdir($dir)) !== false) {
        // Tambahkan nama file ke array
        if (is_file($Path . $file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext == 'xlsx') {
                $files[] = $file;
            }
        }
    }

    // Buat file zip
    $zip = new ZipArchive();
    // // Menghapus file lama jika ada
    if (file_exists($Path . $fileName . '.zip')) {
        unlink($Path . $fileName . '.zip');
    }

    $zip->open($Path . $fileName . '.zip', ZipArchive::CREATE);

    sort($files);
    // Tambahkan semua file ke file zip
    // $i = 1;
    foreach ($files as $file) {

        // Mencari posisi simbol "-" kedua
        // $posisi_dash = strpos($file, '-', strpos($file, '-') + 1);
        // $fileName = substr($file, $posisi_dash + 1);

        $fix_file = fix_namafile($file);

        $zip->addFile($Path . $file, $fix_file);
        // echo $i . '. ' . $fileName . '<br/>';
        // $i++;
    }

    // exit();

    // Tutup file zip
    $zip->close();
}

function cleanFilename($string)
{
    // Daftar karakter yang tidak valid
    $invalidChars = array('"', '<', '>', ':', '\\', '|', '?', '*', '/');

    // Hapus semua karakter yang tidak valid dari string
    $cleanString = str_replace($invalidChars, '', $string);

    // Ganti spasi dengan garis bawah
    $cleanString = str_replace(' ', '_', $cleanString);

    // Kembalikan string yang telah dibersihkan
    return $cleanString;
}

function custom_trim($string)
{
    if (is_null($string)) {
        return "";
    }

    return trim($string);
}

function custom_stripslashes($value)
{

    if ($value != "") {
        $value = stripslashes($value);
    }
    return $value;
}

/**
 * The function `_cek_lokasi_pegawai` calculates the distance between a user's location and the
 * location of a school or government office where the user works.
 *
 * @param user_id The user_id parameter is the ID of the user or employee for whom we want to check the
 * location.
 * @param user_latitude The latitude of the user's location.
 * @param user_longitude The user_longitude parameter is the longitude coordinate of the user's
 * location.
 *
 * @return the value of the variable .
 */
function _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude)
{

    $CI = &get_instance();

    $jarak = -1;

    $CI->db->select('IFNULL(b.id,0) AS sekolah_id,
		                   a.dinas_provinsi,
						   a.dinas_kabupaten,
		                   b.latitude,
						   b.longitude');
    $CI->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
    $cek_lokasi = $CI->db->get_where('pegawai a', array('a.id' => $user_id));

    if ($cek_lokasi->num_rows() > 0) {
        $lokasi = $cek_lokasi->row_array();

        if ($lokasi['sekolah_id'] > 0) {
            $jarak = _distVincenty($lokasi['latitude'], $lokasi['longitude'], $user_latitude, $user_longitude);
        } else {
            //pegawai dinas
            $dinas_kabupaten = $lokasi['dinas_kabupaten'];
            $dinas_provinsi  = $lokasi['dinas_provinsi'];

            $cek_lokasi_dinas = $CI->db->get_where('user', array('provinsi_id' => $dinas_provinsi, 'kabupaten_id' => $dinas_kabupaten));

            if ($cek_lokasi_dinas->num_rows() > 0) {
                $lokasi = $cek_lokasi_dinas->row_array();
                $jarak  = _distVincenty($lokasi['latitude'], $lokasi['longitude'], $user_latitude, $user_longitude);
            } else {
                $jarak = 666;
            }
        }
    } else {
        $jarak = 666;
    }

    return $jarak;
}

function _get_hari_aktif($tahun, $bulan)
{
    $CI = &get_instance();

    // $CI->db->select('a.fulldate,SUM(IF(a.dayofweek = 7,0,IF(b.id > 0,0,1))) AS libur');
    // $CI->db->join('hari_libur b', 'a.fulldate = b.tgl', 'left');
    // $CI->db->where('YEAR(a.fulldate)', $tahun);
    // $CI->db->where('MONTH(a.fulldate)', $bulan);
    // $CI->db->group_by('YEAR(a.fulldate),MONTH(a.fulldate)');
    // $dates = $CI->db->get('dates a');

    $CI->db->select('SUM(IF(a.dayofweek = 7,0,IF(b.id > 0,0,1))) AS libur');
    $CI->db->join('hari_libur b', 'a.fulldate = b.tgl', 'left');
    $CI->db->where('YEAR(a.fulldate)', $tahun);
    $CI->db->where('MONTH(a.fulldate)', $bulan);
    $dates = $CI->db->get('dates a');

    $libur = $dates->row_array();

    return $libur['libur'];
}

/**
 * The function `_get_list_hari_aktif` retrieves a list of active days in a given month and year, with
 * links to download PDF and XLS files for each active day.
 *
 * @param url The parameter "url" is a string that represents the base URL of the website or
 * application where the function is being used. It is used to generate the download URLs for the PDF
 * and XLS files.
 * @param tahun The parameter "tahun" represents the year in which the dates are being filtered.
 * @param bulan The parameter "bulan" represents the month in Indonesian language. It is used to filter
 * the dates in the database based on the specified month.
 *
 * @return a string that represents a list of active days in a given month and year.
 */
function _get_list_hari_aktif($url, $tahun, $bulan, $cmd = 'report')
{

    $CI = &get_instance();

    $CI->db->select('DISTINCT a.day,IF(a.dayofweek = 7,0,IF(b.id > 0,0,1)) AS libur', false);
    $CI->db->join('hari_libur b', 'a.fulldate = b.tgl', 'left');
    $CI->db->where('YEAR(a.fulldate)', $tahun);
    $CI->db->where('MONTH(a.fulldate)', $bulan);
    $CI->db->order_by('a.day ASC');
    $dates = $CI->db->get('dates a');

    $r = "";

    foreach ($dates->result_array() as $d) {

        if ($d['libur'] == "1") {

            if ($cmd === 'report') {
                $pdf_url = site_url($url) . '/' . $tahun . '/' . $bulan . '/' . $d['day'];
                $xls_url = site_url($url) . '/' . $tahun . '/' . $bulan . '/' . $d['day'] . '/xls';

                $r .= '<a data-toggle="tooltip" title="' . $d['day'] . '&nbsp;' . bulan($bulan) . '&nbsp;' . $tahun . '" class="link-hari text-success" href="#!" onclick="download_file(\'' . $pdf_url . '\',\'' . $xls_url . '\')"  >' . $d['day'] . '</a>-';
            } elseif ($cmd === 'cookie') {

                $r .= '<a data-toggle="tooltip" title="' . $d['day'] . '&nbsp;' . bulan($bulan) . '&nbsp;' . $tahun . '" class="link-hari text-success" href="#!" onclick="set_cookie(\'' . $d['day'] . '\')"  >' . $d['day'] . '</a>-';
            }
        } else {

            $r .= $d['day'] . '-';
        }
    }

    $r .= '<a id="link-semua-hari" data-toggle="tooltip" title="Semua Tanggal ' . '&nbsp;' . bulan($bulan) . '&nbsp;' . $tahun . '" class="link-hari badge badge-success" href="#!" onclick="set_cookie(\'0\')">(Semua tanggal)</a>-';

    $r = substr($r, 0, -1);
    return "Tanggal:&nbsp;" . $r;
}

/**
 * The function "get_jadwal_mengajar" retrieves the teaching schedule for a given employee ID in
 * ascending order of day and time.
 *
 * @param pegawai_id The parameter `` is the ID of the employee or teacher for whom you want
 * to retrieve the teaching schedule.
 *
 * @return the result of a database query. Specifically, it is returning the result of a query to
 * retrieve the teaching schedule (jadwal mengajar) for a given employee (pegawai) ID.
 */
function get_jadwal_mengajar($pegawai_id, $cmd = "show-all")
{
    $CI = &get_instance();

    if ($cmd === 'show-all') {
        $CI->db->select('a.id, a.hari,
                     a.jam_mulai,a.jam_selesai,b.nama_kelas AS nama_kelas,
                     c.nama AS matapelajaran,
                     (CASE
                        WHEN a.hari = "SENIN" THEN 1
                        WHEN a.hari = "SELASA" THEN 2
                        WHEN a.hari = "RABU" THEN 3
                        WHEN a.hari = "KAMIS" THEN 4
                        WHEN a.hari = "JUMAT" THEN 5
                        WHEN a.hari = "SABTU" THEN 6
                        WHEN a.hari = "MINGGU" THEN 7
                      END) AS sort');
        $CI->db->join('kelas b', 'a.kelas_id = b.id', 'left');
        $CI->db->join('matapelajaran c', 'a.matapelajaran_id = c.id', 'left');
        $CI->db->order_by('sort,a.jam_mulai ASC');
        return $CI->db->get_where('jadwal_mengajar a', array('a.pegawai_id' => $pegawai_id));
    } else {
        $CI->db->select("a.id,a.jadwal_mengajar_id,
        a.tgl,b.hari,b.jam_mulai,b.jam_selesai,c.nama_kelas,d.nama as matapelajaran,
        IFNULL(a.dokumentasi,'belum') AS dokumentasi,
        IFNULL(a.uraian,'') AS uraian,
        IFNULL(a.foto_mulai,'belum') AS foto_mulai,
        IFNULL(a.foto_selesai,'belum') AS foto_selesai,
        IFNULL(a.verifikasi,'belum') AS verifikasi");
        $CI->db->join('jadwal_mengajar b', 'a.jadwal_mengajar_id  = b.id', 'left');
        $CI->db->join('kelas c', 'b.kelas_id = c.id', 'left');
        $CI->db->join('matapelajaran d', 'b.matapelajaran_id = d.id', 'left');

        // Menentukan rentang tanggal minggu ini
        $today       = date('Y-m-d');
        $startOfWeek = date('Y-m-d', strtotime('this week', strtotime($today)));
        $endOfWeek   = date('Y-m-d', strtotime('next week', strtotime($today)) - 1);

        // Menambahkan kondisi WHERE untuk rentang tanggal
        $CI->db->where("a.tgl BETWEEN '$startOfWeek' AND '$endOfWeek'");

        $CI->db->order_by('a.tgl,b.jam_mulai ASC');
        return $CI->db->get_where('guru_mengajar a', array('b.pegawai_id' => $pegawai_id));
    }
}

/**
 * The function "get_verifikasi_status_and_doc" retrieves the verification status and documentation
 * files for a given teaching schedule and date.
 *
 * @param jadwal_mengajar_id The parameter "jadwal_mengajar_id" is an identifier for a teaching
 * schedule. It is used to uniquely identify a specific teaching schedule in the database.
 * @param tgl The parameter "tgl" represents the date for which you want to retrieve the verification
 * status and documentation.
 *
 * @return an array with three keys: 'file_dok', 'foto_dok', and 'verifikasi'. The values of these keys
 * depend on the result of the database query. If the query returns a row, the values will be taken
 * from that row. If the query does not return any rows, the values will be set to 'belum'.
 */

function get_verifikasi_status_and_doc($jadwal_mengajar_id, $tgl)
{
    $CI = &get_instance();
    $CI->db->select(
        'IFNULL(dokumentasi,"belum") AS file_dok,
         IFNULL(foto_mulai,"belum") AS foto_mulai,
         IFNULL(foto_selesai,"belum") AS foto_selesai,
         IFNULL(verifikasi,"belum") AS verifikasi,
         uraian'
    );
    $CI->db->where('jadwal_mengajar_id', $jadwal_mengajar_id);
    $CI->db->where('tgl', $tgl);
    $dok = $CI->db->get('guru_mengajar');

    $ret = array();
    if ($dok->num_rows() > 0) {
        $ret['file_dok']     = $dok->row_array()['file_dok'];
        $ret['uraian']       = $dok->row_array()['uraian'];
        $ret['foto_mulai']   = $dok->row_array()['foto_mulai'];
        $ret['foto_selesai'] = $dok->row_array()['foto_selesai'];
        $ret['verifikasi']   = $dok->row_array()['verifikasi'];
    } else {
        $ret['file_dok']     = 'belum';
        $ret['foto_mulai']   = 'belum';
        $ret['foto_selesai'] = 'belum';
        $ret['verifikasi']   = 'belum';
        $ret['uraian']       = $dok->row_array()['uraian'];
    }

    return $ret;
}

/**
 * The function `_get_jam_aktif_sekolah` retrieves the active school hours for a given employee and
 * user level in PHP.
 *
 * @param pegawai_id The `pegawai_id` parameter is the ID of the employee. It is used to retrieve the
 * active school hours for the specific employee.
 * @param user_level The user_level parameter is a string that represents the level of the user. It can
 * have two possible values: "PEGAWAI-DINAS" or any other value.
 *
 * @return the result of the database query.
 */
function _get_jam_aktif_sekolah($pegawai_id, $user_level)
{
    $CI = &get_instance();

    $day        = date('w');
    $hari_array = array('MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU');

    $hari_sekarang = $hari_array[$day];

    if ($user_level === 'PEGAWAI-DINAS') {
        $CI->db->select('a.jam_masuk,a.jam_pulang,a.toleransi_masuk,a.toleransi_pulang');
        $where = array('a.hari' => $hari_sekarang, 'a.level_sekolah' => 'DINAS');
        $CI->db->where($where);
    } else {
        $CI->db->select('a.jam_masuk,a.jam_pulang,a.toleransi_masuk,a.toleransi_pulang');
        $CI->db->join('sekolah b', 'a.level_sekolah = b.`level`', 'left');
        $CI->db->join('pegawai c', 'b.id = c.sekolah_id', 'left');
        $CI->db->where('c.id', $pegawai_id);
        $CI->db->where('a.hari', $hari_sekarang);
    }

    return $CI->db->get('jam_aktif_sekolah a');
}

function cek_token_login($token_login)
{

    $CI = &get_instance();

    $data = array();

    $cek_user = $CI->db->get_where('user', array('token_login' => $token_login));
    if ($cek_user->num_rows() > 0) {
        $user          = $cek_user->row_array();
        $data['level'] = $user['level'];
        $data['id']    = $user['id'];
    } else {
        $cek_operator = $CI->db->get_where('sekolah', array('token_login' => $token_login));
        if ($cek_operator->num_rows() > 0) {
            $user          = $cek_operator->row_array();
            $data['level'] = 'OPERATOR';
            $data['id']    = $user['id'];
        } else {

            $cek_pegawai = $CI->db->get_where('pegawai', array('token_login' => $token_login));
            if ($cek_pegawai->num_rows() > 0) {
                $user       = $cek_pegawai->row_array();
                $data['id'] = $user['id'];

                if ($user['jabatan'] === "KEPALA") {
                    $data['level'] = 'KEPSEK';
                } elseif ($user['jabatan'] === "GURU") {
                    $data['level'] = 'GURU';
                } else {
                    if ($user['sekolah_id'] > 0) {
                        //$user['jabatan'] === "TU" || $user['jabatan'] === "SATPAM" || $user['jabatan'] === "KEBERSIHAN"
                        $data['level'] = 'PEGAWAI';
                    } else {
                        $data['level'] = 'PEGAWAI-DINAS';
                    }
                }
            } else {

                $cek_siswa = $CI->db->get_where('siswa', array('token_login' => $token_login));
                if ($cek_siswa->num_rows() > 0) {
                    $user          = $cek_siswa->row_array();
                    $data['id']    = $user['id'];
                    $data['level'] = 'SISWA';
                } else {

                    $data['id']    = 666;
                    $data['level'] = 'AKUN_TIDAK_DITEMUKAN';
                }
            }
        }
    }

    return $data;
}

if (!function_exists('sendWa')) {
    function sendWa($jid, $msg)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://whatsva.id/api/sendMessageText');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"instance_key": "' . $_ENV['WHATSVA_INSTANCE_KEY'] . '", "jid": "' . $jid . '", "message": "' . $msg . '"}');

        $headers   = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }
}

function sendNotification($token, $pesan)
{

    define('API_ACCESS_KEY', 'AAAAjTpLprA:APA91bEU0Gp-CBDlZu6IYRscunkKAiq5E0c1OoinnK-FAHKjnopRdivnKlGqj9K5WSo-0kBS_Jd-17bxpOC0wwamwyRDTGTYM88uEXQouAohro32UOJyIQvTDIRHehuJ6R1lu63Nqyue');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    $notification = [
        'title' => $pesan['title'],
        'body'  => $pesan['body_of_message'],
        'icon'  => 'myIcon',
        'sound' => 'mySound',
    ];

    $extraNotificationData = [
        "message"  => $notification,
        "moredata" => 'dd',
    ];

    $fcmNotification = [
        'to'   => $token, //single token
        'notification' => $notification,
        'data' => $extraNotificationData,
    ];

    $headers = [
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
}

if (!function_exists('get_settings')) {
    function get_settings($title)
    {
        $CI = &get_instance();

        $result  = "";
        $setting = $CI->db->get_where('settings', array('title' => $title))->row();
        if ($setting->tipe === 'options') {
            $result_array = explode(';', $setting->value);
            $result       = $result_array[1];
        } else {
            $result = $setting->value;
        }

        return $result;
    }
}

//level => dinas, sekolah, kepala_sekolah
function _get_logo_for_document($level, $id)
{

    $CI   = &get_instance();
    $logo = '_default_logo.png';

    switch ($level) {
        case 'user':

            $CI->db->select('IFNULL(foto,"-") AS foto');
            $cek = $CI->db->get_where('user', array('id' => $id));
            if ($cek->num_rows() > 0) {
                $c = $cek->row_array();
                if (file_exists('uploads/' . $c['foto'])) {
                    $logo = $c['foto'];
                }
            }

            break;
        case 'sekolah':

            $CI->db->select('IFNULL(logo,"-") AS logo');
            $cek = $CI->db->get_where('sekolah', array('id' => $id));
            if ($cek->num_rows() > 0) {
                $c = $cek->row_array();
                if (file_exists('uploads/' . $c['logo'])) {
                    $logo = $c['logo'];
                } else {

                    $CI->db->select('IFNULL(b.foto,"-") AS logo');
                    $CI->db->join('user b', 'a.provinsi = b.provinsi_id AND a.kabupaten = b.kabupaten_id', 'left');
                    $cek = $CI->db->get_where('sekolah a', array('a.id' => $id));

                    if ($cek->num_rows() > 0) {
                        $c = $cek->row_array();
                        if (file_exists('uploads/' . $c['logo'])) {
                            $logo = $c['logo'];
                        }
                    }
                }
            }

            break;
        case 'kepsek':

            $CI->db->select('IFNULL(b.logo,"-") AS logo');
            $CI->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
            $cek = $CI->db->get_where('pegawai a', array('a.id' => $id));

            if ($cek->num_rows() > 0) {
                $c = $cek->row_array();
                if (file_exists('uploads/' . $c['logo'])) {
                    $logo = $c['logo'];
                } else {

                    $CI->db->select('IFNULL(c.foto,"-") AS logo');
                    $CI->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
                    $CI->db->join('user c', 'b.provinsi = c.provinsi_id AND b.kabupaten = c.kabupaten_id', 'left');
                    $cek = $CI->db->get_where('pegawai a', array('a.id' => $id));

                    if ($cek->num_rows() > 0) {
                        $c = $cek->row_array();
                        if (file_exists('uploads/' . $c['logo'])) {
                            $logo = $c['logo'];
                        }
                    }
                }
            }

            break;

        default:
    }

    return $logo;
}

function bulan($index)
{
    $namaBulan = array(
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    );
    return $namaBulan[$index - 1];
}

function generate_barcode($file_name, $barcode_text, $scale = 2, $fontsize = 18, $thickness = 30, $dpi = 72)
{
    // CREATE BARCODE GENERATOR
    // Including all required classes
    require_once APPPATH . 'libraries/Barcode/BCGFontFile.php';
    require_once APPPATH . 'libraries/Barcode/BCGColor.php';
    require_once APPPATH . 'libraries/Barcode/BCGDrawing.php';
    // require_once( APPPATH . 'libraries/Barcode/BCGcode39extended.barcode.php');

    // Including the barcode technology
    // Ini bisa diganti-ganti mau yang 39, ato 128, dll, liat di folder Barcode
    require_once APPPATH . 'libraries/Barcode/BCGcode39.barcode.php';
    // require_once( APPPATH . 'libraries/Barcode/BCGcode128.barcode.php');
    // require_once( APPPATH . 'libraries/Barcode/BCGcode93.barcode.php');

    // Loading Font
    // kalo mau ganti font, jangan lupa tambahin dulu ke folder font, baru loadnya di sini
    $font = new BCGFontFile(APPPATH . 'libraries/font/Arial.ttf', $fontsize);

    // Text apa yang mau dijadiin barcode, biasanya kode produk
    // $text = md5($barcode_text);

    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);

    $drawException = null;
    try {
        $code = new BCGcode39(); // kalo pake yg code39, klo yg lain mesti disesuaikan
        $code->setScale($scale); // Resolution
        $code->setThickness($thickness); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($barcode_text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
    1 - Filename (empty : display on screen)
    2 - Background color */
    $drawing = new BCGDrawing('', $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setDPI($dpi);
        $drawing->setBarcode($code);
        $drawing->draw();
    }
    // ini cuma labeling dari sisi aplikasi saya, penamaan file menjadi png barcode.
    $filename_img_barcode = $file_name . '.png';
    // folder untuk menyimpan barcode
    $drawing->setFilename(FCPATH . 'uploads/barcode/' . $filename_img_barcode);
    // proses penyimpanan barcode hasil generate
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    return $filename_img_barcode;
}

// function terbilang($x)
// {
//     $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
//     if ($x < 12) {
//         return " " . $abil[$x];
//     } elseif ($x < 20) {
//         return Terbilang($x - 10) . "belas";
//     } elseif ($x < 100) {
//         return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
//     } elseif ($x < 200) {
//         return " seratus" . Terbilang($x - 100);
//     } elseif ($x < 1000) {
//         return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
//     } elseif ($x < 2000) {
//         return " seribu" . Terbilang($x - 1000);
//     } elseif ($x < 1000000) {
//         return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
//     } elseif ($x < 1000000000) {
//         return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
//     }
// }

function terbilang($x)
{
    if (!is_numeric($x) || $x < 0) {
        return "Invalid input";
    }
    $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    if ($x < 12) {
        return " " . $abil[$x];
    } elseif ($x < 20) {
        return $abil[$x - 10] . "belas";
    } elseif ($x < 100) {
        return $abil[floor($x / 10)] . " puluh" . $abil[$x % 10];
    } elseif ($x < 200) {
        return " seratus" . terbilang($x - 100);
    } elseif ($x < 1000) {
        return terbilang(floor($x / 100)) . " ratus" . terbilang($x % 100);
    } elseif ($x < 2000) {
        return " seribu" . terbilang($x - 1000);
    } elseif ($x < 1000000) {
        return terbilang(floor($x / 1000)) . " ribu" . terbilang($x % 1000);
    } elseif ($x < 1000000000) {
        return terbilang(floor($x / 1000000)) . " juta" . terbilang($x % 1000000);
    } else {
        return "Number too large";
    }
}

function generate_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function unslugify($slug)
{
    // Ubah tanda "-" menjadi spasi
    $text = str_replace('-', ' ', $slug);

    return $text;
}

function generate_doc_name($user_nik, $jenis_dokumen_id)
{
    $ci            = &get_instance();
    $jenis_dokumen = $ci->db->get_where('jenis_dokumen', array('id' => $jenis_dokumen_id))->row_array();

    return slugify($user_nik . '-' . $jenis_dokumen['nama']);
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function generateRandomString($length = 10)
{
    $characters       = '23456789abcdefghjkmnpqrstwxyz';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; ++$i) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

if (!function_exists('base64url_encode')) {
    function base64url_encode($data, $pad = '')
    {
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if (!$pad) {
            $data = rtrim($data, '=');
        }
        return $data;
        // $encoded = base64_encode($data);
        // $encoded = str_replace(array('+', '/', '='), array('-', '_', ''), $encoded);
        // return urlencode($encoded); // Gunakan urlencode untuk mengamankan URL
    }
}

/**
 * Retrieves the value of a specific column from a given table based on the provided ID.
 *
 * @param string $table_name The name of the table to retrieve the value from.
 * @param int $id The ID used to identify the row in the table.
 * @param string $column_name The name of the column to retrieve the value from.
 * @throws None
 * @return mixed|null The value of the specified column for the row with the given ID, or null if no such row exists.
 */
function get_column_value($table_name, $id, $column_name)
{
    $ci    = &get_instance();
    $query = $ci->db->select($column_name)
        ->from($table_name)
        ->where('id', $id)
        ->get();

    if ($query->num_rows() > 0) {
        $row = $query->row();
        return $row->$column_name;
    } else {
        return null;
    }
}

if (!function_exists('base64url_decode')) {
    function base64url_decode($encodedData)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $encodedData));
        // $encodedData = urldecode($encodedData);
        // $encodedData = str_replace(array('-', '_'), array('+', '/'), $encodedData);
        // $paddedLength = strlen($encodedData) % 4;
        // if ($paddedLength > 0) {
        //     $encodedData .= substr('====', $paddedLength);
        // }
        // return base64_decode($encodedData);
    }
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */

if (!function_exists('file_pathinfo')) {
    function file_pathinfo($filePath)
    {
        $fileParts = pathinfo($filePath);

        if (!isset($fileParts['filename'])) {
            $fileParts['filename'] = substr($fileParts['basename'], 0, strrpos($fileParts['basename'], '.'));
        }

        return $fileParts;
    }
}

if (!function_exists('username_from_email')) {
    function username_from_email($emailaddress)
    {
        $parts = explode("@", $emailaddress);
        return '<strong>' . $parts[0] . '</strong>';
    }
}

function removeTitles($name, $titles)
{
    // Buat pola regex dari daftar gelar
    $patterns = array_map(function ($title) {
        return '/\b' . preg_quote($title, '/') . '\b/i';
    }, $titles);

    // Menghilangkan semua pola dari string
    $nameWithoutTitles = preg_replace($patterns, '', $name);

    // Menghilangkan spasi berlebih yang mungkin tersisa setelah penghapusan
    $nameWithoutTitles = preg_replace('/\s+/', ' ', $nameWithoutTitles);

    // Menghapus spasi di awal dan akhir string
    return trim($nameWithoutTitles);
}

if (!function_exists('relative_time')) {
    function relative_time($date)
    {
        $date = substr($date ?? '', 0, 10);
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            $date_array = preg_split('/[-\.\/ ]/', $date);
            return date('j M Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
        } elseif (empty($date)) {
            return '';
        }
    }
}

if (!function_exists('is_valid_email')) {
    // function is_valid_email($emailaddress)
    // {
    //     $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

    //     if (preg_match($pattern, $emailaddress) === 1) {
    //         // emailaddress is valid
    //         return true;
    //     }

    //     return false;
    // }

    function is_valid_email($emailaddress)
    {
        if (filter_var($emailaddress, FILTER_VALIDATE_EMAIL)) {
            // emailaddress is valid
            return true;
        }
        return false;
    }
}

if (!function_exists('remove_time_from_datetime')) {
    function remove_time_from_datetime($datetime)
    {
        $ex = explode(' ', $datetime);
        return $ex[0];
    }
}

if (!function_exists('nicetime')) {
    // function nicetime($date)
    // {
    //     if (empty($date)) {
    //         return 'tidak ada tanggal yang dimasukkan';
    //     }

    //     $periods = array('detik', 'menit', 'jam', 'hari', 'minggu', 'bulan', 'tahun', 'dekade');
    //     $lengths = array('60', '60', '24', '7', '4.35', '12', '10');

    //     $now       = time();
    //     $unix_date = strtotime($date);

    //     // check validity of date
    //     if (empty($unix_date)) {
    //         return 'Bad date';
    //     }

    //     // is it future date or past date
    //     if ($now > $unix_date) {
    //         $difference = $now - $unix_date;
    //         $tense      = 'yang lalu';
    //     } else {
    //         $difference = $unix_date - $now;
    //         $tense      = 'dari sekarang';
    //     }

    //     for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; ++$j) {
    //         $difference /= $lengths[$j];
    //     }

    //     $difference = round($difference);

    //     return "$difference $periods[$j] {$tense}";
    // }

    function nicetime($date)
    {
        if (empty($date)) {
            return 'No date provided.';
        }
        $now       = time();
        $unix_date = strtotime($date);
        if (!$unix_date) {
            return 'Invalid date.';
        }
        $difference = abs($now - $unix_date);
        $tense      = ($now >= $unix_date) ? 'ago' : 'from now';
        $periods    = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lengths    = array('60', '60', '24', '7', '4.35', '12', '10');
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; ++$j) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        return "$difference $periods[$j] $tense";
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

function filterHtml($input)
{
    // Remove HTML comments, but not SSI
    $input = preg_replace('/<!--[^#](.*?)-->/s', '', $input);

    // The content inside these tags will be spared:
    $doNotCompressTags = ['script', 'pre', 'textarea'];
    $matches           = [];

    foreach ($doNotCompressTags as $tag) {
        $regex = "!<{$tag}[^>]*?>.*?</{$tag}>!is";

        // It is assumed that this placeholder could not appear organically in your
        // output. If it can, you may have an XSS problem.
        $placeholder = "@@<'-placeholder-$tag'>@@";

        // Replace all the tags (including their content) with a placeholder, and keep their contents for later.
        $input = preg_replace_callback(
            $regex,
            function ($match) use ($tag, &$matches, $placeholder) {
                $matches[$tag][] = $match[0];
                return $placeholder;
            },
            $input
        );
    }

    // Remove whitespace (spaces, newlines and tabs)
    $input = trim(preg_replace('/[ \n\t]+/m', ' ', $input));

    // Iterate the blocks we replaced with placeholders beforehand, and replace the placeholders
    // with the original content.
    foreach ($matches as $tag => $blocks) {
        $placeholder       = "@@<'-placeholder-$tag'>@@";
        $placeholderLength = strlen($placeholder);
        $position          = 0;

        foreach ($blocks as $block) {
            $position = strpos($input, $placeholder, $position);
            if ($position === false) {
                throw new \RuntimeException("Found too many placeholders of type $tag in input string");
            }
            $input = substr_replace($input, $block, $position, $placeholderLength);
        }
    }

    return $input;
}

if (!function_exists('compress_output')) {
    //http://jeromejaglale.com/doc/php/codeigniter_compress_html
    //http://stackoverflow.com/questions/5312349/minifying-final-html-output-using-regular-expressions-with-codeigniter
    function compress_output()
    {
        $CI = &get_instance();

        $buffer = $CI->output->get_output();
        /*$re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
        [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
        [^<]*+        # Either zero or more non-"<" {normal*}
        (?:           # Begin {(special normal*)*} construct
        <           # or a < starting a non-blacklist tag.
        (?!/?(?:textarea|pre|script)\b)
        [^<]*+      # more non-"<" {normal*}
        )*+           # Finish "unrolling-the-loop"
        (?:           # Begin alternation group.
        <           # Either a blacklist start tag.
        (?>textarea|pre|script)\b
        | \z          # or end of file.
        )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six'; */

        // $buffer = preg_replace($re, " ", $buffer);
        $buffer = filterHtml($buffer);

        $CI->output->set_output($buffer);
        // $CI->output->_display();
    }
}

if (!function_exists('limit_text')) {
    function limit_text($string, $limit)
    {
        if ($string !== null) {
            $string = strip_tags($string);

            if (strlen($string) > $limit) {
                $stringCut = substr($string, 0, $limit);
                $string    = substr($stringCut, 0, strrpos($stringCut, ' ')) . '...';
            }
        }

        return $string;
    }
}

if (!function_exists('hide_email')) {
    function hide_email($email)
    {
        return substr($email, 0, 3) . '****' . substr($email, strpos($email, '@'));
    }
}

if (!function_exists('load_image')) {
    function load_image($image_path, $width, $height, $zoom = 1, $crop = 1)
    {
        // return site_url('timthumb?src='.site_url($image_path).'&h='.$height.'&w='.$width.'&zc=0');
        return site_url('thumb?src=' . site_url($image_path) . '&size=' . $width . 'x' . $height . '&zoom=' . $zoom . '&crop=' . $crop);
    }
}

if (!function_exists('convert_sql_date_to_date')) {
    function convert_sql_date_to_date($date, $php_date_format = 'd-m-Y')
    {
        //2017-05-17
        //17/05/2017
        $date = substr($date, 0, 10);

        if (!empty($date) && $date != '0000-00-00' && $date != '1970-01-01') {
            list($year, $month, $day) = explode('-', $date);
            $date                     = date($php_date_format, mktime(0, 0, 0, $month, $day, $year));
        } else {
            $date = '';
        }

        return $date;
    }
}

/**
 * Fungsi ini digunakan untuk mengkonversi tanggal ke format tanggal SQL pada bahasa pemrograman PHP.
 *
 * @param string $date Tanggal yang akan dikonversi.
 * @param string $php_date_format Format tanggal pada PHP (opsional).
 *
 * @return string Tanggal yang telah dikonversi ke format tanggal SQL.
 */
if (!function_exists('convert_date_to_sql_date')) {
    function convert_date_to_sql_date($date, $php_date_format = '')
    {
        $date = substr($date, 0, 10);
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            //If it's already a sql-date don't convert it!
            return $date;
        } elseif (empty($date)) {
            return '';
        }

        $date_array = preg_split('/[-\.\/ ]/', $date);
        if ($php_date_format == 'd/m/Y') {
            $sql_date = date('Y-m-d', mktime(0, 0, 0, $date_array[1], $date_array[0], $date_array[2]));
        } elseif ($php_date_format == 'm/d/Y') {
            $sql_date = date('Y-m-d', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
        } else {
            $sql_date = $date;
        }

        return $sql_date;
    }
}

if (!function_exists('send_email')) {
    function send_email($recipient_email_address, $subject, $message, $attachment)
    {
        $CI = &get_instance();
        $CI->load->library('My_PHPMailer');

        $mail = new PHPMailer();

        //$mail->SMTPDebug = 3;

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth   = true;

        $mail->Username = $CI->config->item('mail_Username');
        $mail->Password = $CI->config->item('mail_Password');
        $mail->setFrom($CI->config->item('mail_Username'), $CI->config->item('mail_setFrom'));
        $mail->addReplyTo($CI->config->item('mail_Username'), $CI->config->item('mail_setFrom'));
        $mail->addAddress($recipient_email_address, preg_replace('/@.*?$/', '', $recipient_email_address));
        if ($attachment !== 'none') {
            $mail->AddAttachment($attachment);
        }
        $mail->Subject = $subject;
        $mail->msgHTML($message);

        if (!$mail->send()) {
            //return false;
            //echo 'Message could not be sent.';
            echo 'Mailer Error: <pre>' . $mail->ErrorInfo . '</pre>';
            //exit(0);
        }

        //return true;
    }
}

/**
 * Encrypt and decrypt
 *
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
 *
 * @param string $string string to be encrypted/decrypted
 * @param string $action what to do with this? e for encrypt, d for decrypt
 */
function simple_crypt($string, $action = 'e')
{
    // you may change these values to your own
    $secret_key     = 'XygQ9PEV4kCbrdF7';
    $secret_iv      = '9hdMHxKssGQTPLfE';
    $output         = false;
    $encrypt_method = "AES-256-CBC";
    $key            = hash('sha256', $secret_key);
    $iv             = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

/**
 * Fungsi ini digunakan untuk mengganti string dengan array.
 *
 * @param string $inputString String yang akan dimodifikasi.
 * @param array $replaceArray Array asosiatif yang berisi kunci dan nilai untuk penggantian string.
 *
 * @return string String yang telah dimodifikasi dengan penggantian yang spesifik.
 */

function replaceStringWithArray($inputString, $replaceArray)
{
    // Lakukan penggantian string berdasarkan kunci dan nilai dari array
    foreach ($replaceArray as $search => $replace) {
        $inputString = str_replace($search, $replace, $inputString);
    }
    return $inputString;
}

/**
 * The function countWeeksInMonth calculates the number of weeks in a given month and year.
 *
 * @param year The year parameter is the year for which you want to count the number of weeks in a
 * specific month.
 * @param month The month parameter is the numeric representation of the month, ranging from 1 to 12.
 *
 * @return the number of weeks in a given month and year.
 */
function countWeeksInMonth($year, $month)
{
    $firstDayOfMonth = date("$year-$month-01");
    $lastDayOfMonth  = date("$year-$month-t", strtotime($firstDayOfMonth));

    $firstWeek = date('W', strtotime($firstDayOfMonth));
    $lastWeek  = date('W', strtotime($lastDayOfMonth));

    $weeksInMonth = $lastWeek - $firstWeek + 1;

    return $weeksInMonth;
}

function getDateByDayName($dayName)
{
    $days = [
        'SENIN'  => 1,
        'SELASA' => 2,
        'RABU'   => 3,
        'KAMIS'  => 4,
        'JUMAT'  => 5,
        'SABTU'  => 6,
        'MINGGU' => 7,
    ];

    $ci   = &get_instance();
    $year = date("Y");
    $week = date("W");

    return $ci->db->get_where(
        'dates',
        array(
            'year'      => $year,
            'week'      => $week,
            'dayofweek' => $days[$dayName],
        )
    )->row_array()['fulldate'];
}

function create_file($path, $content)
{

    // Membuka file untuk ditulis
    $file = fopen($path, 'w');

    // Menulis konten ke file
    fwrite($file, $content);

    // Menutup file
    fclose($file);
}

// function get_latest_zip_file($path) {
//     // Mendapatkan semua file di folder
//     $files = scandir($path);

//     // Menyimpan nama file zip dan waktu modifikasi terakhir
//     $latest_file = null;
//     $latest_mtime = 0;

//     // Looping semua file
//     foreach ($files as $file) {
//         // Mendapatkan informasi file
//         $file_path = $path . $file;
//         $mtime = filemtime($file_path);

//         // Jika file zip dan waktu modifikasi lebih baru, hapus file zip lama dan simpan informasi file
//         if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
//             if ($mtime > $latest_mtime) {
//                 if ($latest_file !== null) {
//                     unlink($path . $latest_file);
//                 }

//                 $latest_file = $file;
//                 $latest_mtime = $mtime;
//             } else {
//                 unlink($file_path);
//             }
//         }
//     }

//     // Mengembalikan nama file dan waktu modifikasi terakhir
//     if ($latest_file === null) {
//         return array(
//             'file' => '-',
//             'mtime' => '-',
//         );
//     } else {
//         return array(
//             'file' => $latest_file,
//             'mtime' => $latest_mtime,
//         );
//     }
// }

// function getDateByDayName($dayName)
// {
//     $days = [
//         'MINGGU' => 0,
//         'SENIN' => 1,
//         'SELASA' => 2,
//         'RABU' => 3,
//         'KAMIS' => 4,
//         'JUMAT' => 5,
//         'SABTU' => 6,
//     ];

//     // Mendapatkan indeks hari saat ini (0 untuk Minggu, 1 untuk Senin, dst.)
//     $currentDayIndex = date('w');

//     // Mendapatkan indeks hari yang diinginkan
//     $desiredDayIndex = $days[$dayName];

//     // Menghitung selisih hari antara hari saat ini dan hari yang diinginkan
//     $dayDiff = $desiredDayIndex - $currentDayIndex;

//     // Jika selisih negatif, artinya hari yang diinginkan telah berlalu dalam minggu ini
//     // Maka tambahkan 7 hari untuk mendapatkan tanggal dalam minggu ini
//     if ($dayDiff < 0) {
//         $dayDiff += 7;
//     }

//     // Mendapatkan tanggal hari ini
//     $currentDate = new DateTime();

//     // Menambahkan selisih hari untuk mendapatkan tanggal dalam minggu ini
//     $currentDate->add(new DateInterval('P' . $dayDiff . 'D'));

//     // Mengembalikan tanggal dalam format yang diinginkan (misalnya, 'Y-m-d')
//     return $currentDate->format('Y-m-d');
// }
