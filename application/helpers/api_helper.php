<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The function getToken retrieves an access token from an API using the provided credentials and
 * returns the token along with its expiration date.
 *
 * @return a JSON-encoded string. The string contains information about the token retrieval process. If
 * the retrieval is successful, the string will include the access token and its expiration date. If
 * there is an error during the retrieval process, the string will include an error message.
 */
// function getToken()
// {
//     $CI                = &get_instance();
//     $api_key           = $_ENV['API_KEMENDIKBUD_KEY'];
//     $api_username      = $_ENV['API_KEMENDIKBUD_USERNAME'];
//     $api_password      = $_ENV['API_KEMENDIKBUD_PASSWORD'];
//     $url_api_kemdikbud = $_ENV['API_KEMENDIKBUD_URL'];

//     $curl = curl_init();

//     $url        = $url_api_kemdikbud . '/token';
//     $postFields = array('username' => $api_username, 'password' => $api_password);
//     $headers    = array(
//         'X-API-Key: ' . $api_key,
//     );

//     curl_setopt_array($curl, array(
//         CURLOPT_URL            => $url,
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING       => '',
//         CURLOPT_MAXREDIRS      => 10,
//         CURLOPT_TIMEOUT        => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
//         CURLOPT_SSL_VERIFYPEER => false,
//         CURLOPT_CUSTOMREQUEST  => 'POST',
//         CURLOPT_POSTFIELDS     => $postFields,
//         CURLOPT_HTTPHEADER     => $headers,
//     ));

//     $response = curl_exec($curl);
//     $error    = curl_error($curl);

//     if ($error) {
//         $result = array(
//             'error' => 'Failed to retrieve token: ' . $error,
//         );
//         return json_encode($result);
//     }

//     curl_close($curl);

//     $decodedResponse = json_decode($response, true);

//     // echo $response;
//     // exit();

//     $result = array(
//         'msg'    => 'Done',
//         'return' => array(
//             'token'        => $decodedResponse['access_token'],
//             'expired_date' => $decodedResponse['expired_date'],
//         ),
//     );

//     return json_encode($result);
// }

function getToken()
{
    $CI                = &get_instance();
    $api_key           = $_ENV['API_KEMENDIKBUD_KEY'];
    $api_username      = $_ENV['API_KEMENDIKBUD_USERNAME'];
    $api_password      = $_ENV['API_KEMENDIKBUD_PASSWORD'];
    $url_api_kemdikbud = $_ENV['API_KEMENDIKBUD_URL'];

    // Define cache file
    $cache_file = APPPATH . 'cache/token_cache.json';

    // Check if cache file exists and is not older than 20 minutes
    if (file_exists($cache_file)) {
        $cache_content = file_get_contents($cache_file);
        $cache_data    = json_decode($cache_content, true);

        if ($cache_data) {
            $cached_time = strtotime($cache_data['cached_time']);
            if ((time() - $cached_time) < 1200) { // 1200 seconds = 20 minutes
                return json_encode([
                    'msg'    => 'Cached',
                    'return' => [
                        'token'        => $cache_data['token'],
                        'expired_date' => $cache_data['expired_date'],
                    ],
                ]);
            }
        }
    }

    $curl = curl_init();

    $url        = $url_api_kemdikbud . '/token';
    $postFields = array('username' => $api_username, 'password' => $api_password);
    $headers    = array(
        'X-API-Key: ' . $api_key,
    );

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_HTTPHEADER     => $headers,
    ));

    $response = curl_exec($curl);
    $error    = curl_error($curl);

    if ($error) {
        $result = array(
            'error' => 'Failed to retrieve token: ' . $error,
        );
        return json_encode($result);
    }

    curl_close($curl);

    $decodedResponse = json_decode($response, true);

    $result = array(
        'msg'    => 'Done',
        'return' => array(
            'token'        => $decodedResponse['access_token'],
            'expired_date' => $decodedResponse['expired_date'],
        ),
    );

    // Cache the token and expiry date
    $cache_data = [
        'token'        => $decodedResponse['access_token'],
        'expired_date' => $decodedResponse['expired_date'],
        'cached_time'  => date('c'), // ISO 8601 format current time
    ];
    file_put_contents($cache_file, json_encode($cache_data));

    return json_encode($result);
}

/**
 * The function `fetchDataFromAPI` is used to retrieve data from an API, save the response to a file,
 * and return the file path.
 *
 * @param access_token The access token is a security credential that is used to authenticate and
 * authorize the API request. It is typically obtained by the user through an authentication process
 * and then passed to the API for subsequent requests.
 * @param endpoint The "endpoint" parameter is the specific API endpoint that you want to fetch data
 * from. It is a string that represents the specific resource or data you want to retrieve from the
 * API. For example, it could be "users", "products", or "orders".
 * @param param The "param" parameter is a string that represents additional parameters or filters that
 * you want to pass to the API endpoint. It is appended to the endpoint URL to specify the specific
 * data you want to retrieve from the API.
 *
 * @return a JSON-encoded string. The returned string contains information about the result of the API
 * request. If the request is successful, it includes the path to the saved API response file. If there
 * is an error, it includes an error message.
 */
function fetchDataFromAPI($access_token, $endpoint, $param)
{
    $CI                = &get_instance();
    $api_key           = $_ENV['API_KEMENDIKBUD_KEY'];
    $url_api_kemdikbud = $_ENV['API_KEMENDIKBUD_URL'];

    $curl = curl_init();

    $url     = $url_api_kemdikbud . $endpoint . $param;
    $headers = array(
        'Authorization: Bearer ' . $access_token,
        'X-API-Key: ' . $api_key,
    );

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => $headers,
    ));

    $response = curl_exec($curl);
    $error    = curl_error($curl);

    if ($error) {
        $result = array(
            'error' => 'Failed to retrieve data from API: ' . $error,
        );
        return json_encode($result);
    }

    curl_close($curl);

    $outputPathFile = FCPATH . 'temp/api' . $endpoint . '-' . $param . '.json';
    $success        = file_put_contents($outputPathFile, $response);

    if (!$success) {
        $result = array(
            'error' => 'Failed to save API response to file.',
        );
        return json_encode($result);
    }

    $result = array(
        'msg'    => 'Done',
        'return' => array(
            'outputFilePath' => $outputPathFile,
        ),
    );

    return json_encode($result);
}

function getSekolah($access_token, $kode_kecamatan)
{
    return fetchDataFromAPI($access_token, '/sekolah', '?kode_kecamatan=' . $kode_kecamatan);
}

function getPtk($access_token, $npsn)
{
    return fetchDataFromAPI($access_token, '/ptk', '?npsn=' . $npsn);
}

function getPesertaDidik($access_token, $npsn)
{
    return fetchDataFromAPI($access_token, '/peserta-didik', '?npsn=' . $npsn);
}

function getRombonganBelajar($access_token, $npsn)
{
    return fetchDataFromAPI($access_token, '/rombongan-belajar', '?npsn=' . $npsn);
}

function getRuang($access_token, $npsn)
{
    return fetchDataFromAPI($access_token, '/ruang', '?npsn=' . $npsn);
}

//============
function _getPtk($token, $npsn)
{

    $CI                    = &get_instance();
    $jsonDataPtk           = getPtk($token, $npsn);
    $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    $jsonData = file_get_contents($outputPathFile);
    // Mendekode konten JSON menjadi struktur data yang dapat diakses
    $data = json_decode($jsonData, true);

    if (isset($data[0]['keterangan'])) {
        //Data tidak ditemukan, atau NPSN diluar wilayah akses
        echo "Data tidak ditemukan, atau NPSN diluar wilayah akses";
    } else {
        foreach ($data as $item) {
            $ptkId             = $item['ptk_id'];
            $sekolahId         = $item['sekolah_id'];
            $nuptk             = $item['nuptk'];
            $nip               = $item['nip'];
            $nama              = $item['nama'];
            $tempatLahir       = $item['tempat_lahir'];
            $tanggalLahir      = $item['tanggal_lahir'];
            $nik               = $item['nik'];
            $jenisKelamin      = $item['jenis_kelamin'];
            $agama             = $item['agama'];
            $skPengangkatan    = $item['sk_pengangkatan'];
            $tmtPengangkatan   = $item['tmt_pengangkatan'];
            $statusKepegawaian = $item['status_kepegawaian']; //status_pegawai
            $jenisPtk          = $item['jenis_ptk']; //jabatan
            $password          = 'INITIAL_PASSWORD'; //password_hash($nuptk, PASSWORD_DEFAULT);
            $alamatJalan       = $item['alamat_jalan'];
            $email             = $item['email'];
            $telp              = $item['no_hp'];
            $statusKeaktifan   = $item['status_keaktifan']; //aktif

            $riwayatSertifikasiBidangStudi                 = $item['riwayat_sertifikasi_bidang_studi'];
            $riwayatSertifikasiJenisSertifikasi            = $item['riwayat_sertifikasi_jenis_sertifikasi'];
            $riwayatSertifikasiTahunSertifikasi            = $item['riwayat_sertifikasi_tahun_sertifikasi'];
            $riwayatSertifikasiNomorSertifikat             = $item['riwayat_sertifikasi_nomor_sertifikat'];
            $riwayatSertifikasiNrg                         = $item['riwayat_sertifikasi_nrg'];
            $riwayatSertifikasiNomorPeserta                = $item['riwayat_sertifikasi_nomor_peserta'];
            $riwayatPendidikanFormalBidangStudi            = $item['riwayat_pendidikan_formal_bidang_studi'];
            $riwayatPendidikanFormalJenjangPendidikan      = $item['riwayat_pendidikan_formal_jenjang_pendidikan'];
            $riwayatPendidikanFormalGelarAkademik          = $item['riwayat_pendidikan_formal_gelar_akademik'];
            $riwayatPendidikanFormalSatuanPendidikanFormal = $item['riwayat_pendidikan_formal_satuan_pendidikan_formal'];
            $riwayatPendidikanFormalFakultas               = $item['riwayat_pendidikan_formal_fakultas'];
            $riwayatPendidikanFormalKependidikan           = $item['riwayat_pendidikan_formal_kependidikan'];
            $riwayatPendidikanFormalTahunMasuk             = $item['riwayat_pendidikan_formal_tahun_masuk'];
            $riwayatPendidikanFormalTahunLulus             = $item['riwayat_pendidikan_formal_tahun_lulus'];
            $riwayatPendidikanFormalNim                    = $item['riwayat_pendidikan_formal_nim'];
            $riwayatPendidikanFormalStatusKuliah           = $item['riwayat_pendidikan_formal_status_kuliah'];
            $riwayatPendidikanFormalSemester               = $item['riwayat_pendidikan_formal_semester'];
            $riwayatPendidikanFormalIpk                    = $item['riwayat_pendidikan_formal_ipk'];

            $namaIbuKandung = $item['nama_ibu_kandung'];

            $set = array(
                'id'                                                 => $ptkId,
                'sekolah_id'                                         => $sekolahId,
                'nuptk'                                              => $nuptk,
                'nip'                                                => $nip,
                'nama_lengkap'                                       => $nama,
                'tempat_lahir'                                       => $tempatLahir,
                'tgl_lahir'                                          => $tanggalLahir,
                'nik'                                                => $nik,
                'jk'                                                 => $jenisKelamin,
                'agama'                                              => $agama,
                'sk_pengangkatan'                                    => $skPengangkatan,
                'tmt_pengangkatan'                                   => $tmtPengangkatan,
                'jabatan'                                            => $jenisPtk,
                'status_pegawai'                                     => $statusKepegawaian,
                'password'                                           => $password,
                'alamat'                                             => $alamatJalan,
                'email'                                              => $email,
                'telp'                                               => $telp,
                'aktif'                                              => $statusKeaktifan,

                'riwayat_sertifikasi_bidang_studi'                   => $riwayatSertifikasiBidangStudi,
                'riwayat_sertifikasi_jenis_sertifikasi'              => $riwayatSertifikasiJenisSertifikasi,
                'riwayat_sertifikasi_tahun_sertifikasi'              => $riwayatSertifikasiTahunSertifikasi,
                'riwayat_sertifikasi_nomor_sertifikat'               => $riwayatSertifikasiNomorSertifikat,
                'riwayat_sertifikasi_nrg'                            => $riwayatSertifikasiNrg,
                'riwayat_sertifikasi_nomor_peserta'                  => $riwayatSertifikasiNomorPeserta,
                'riwayat_pendidikan_formal_bidang_studi'             => $riwayatPendidikanFormalBidangStudi,
                'riwayat_pendidikan_formal_jenjang_pendidikan'       => $riwayatPendidikanFormalJenjangPendidikan,
                'riwayat_pendidikan_formal_gelar_akademik'           => $riwayatPendidikanFormalGelarAkademik,
                'riwayat_pendidikan_formal_satuan_pendidikan_formal' => $riwayatPendidikanFormalSatuanPendidikanFormal,
                'riwayat_pendidikan_formal_fakultas'                 => $riwayatPendidikanFormalFakultas,
                'riwayat_pendidikan_formal_kependidikan'             => $riwayatPendidikanFormalKependidikan,
                'riwayat_pendidikan_formal_tahun_masuk'              => $riwayatPendidikanFormalTahunMasuk,
                'riwayat_pendidikan_formal_tahun_lulus'              => $riwayatPendidikanFormalTahunLulus,
                'riwayat_pendidikan_formal_nim'                      => $riwayatPendidikanFormalNim,
                'riwayat_pendidikan_formal_status_kuliah'            => $riwayatPendidikanFormalStatusKuliah,
                'riwayat_pendidikan_formal_semester'                 => $riwayatPendidikanFormalSemester,
                'riwayat_pendidikan_formal_ipk'                      => $riwayatPendidikanFormalIpk,
                'nama_ibu_kandung'                                   => $namaIbuKandung,
                'tgl_update'                                         => date('Y-m-d H:i:s'),
            );

            $exclude_columns = array('password', 'id');
            $CI->db->on_duplicate('pegawai', $set, $exclude_columns);
        }
    }

    $CI->db->where('npsn', $npsn);
    $CI->db->update('sekolah', array('tgl_update_pegawai' => date('Y-m-d H:i:s')));

    unlink($outputPathFile);
    // $this->exportAllPegawai($npsn);
}

function _ruang($token, $npsn)
{
    $CI                    = &get_instance();
    $jsonData              = getRuang($token, $npsn);
    $decodedOutputFilePath = json_decode($jsonData, true);
    $outputPathFile        = $decodedOutputFilePath['return']['outputFilePath'];
    $jsonData              = file_get_contents($outputPathFile);
    $data                  = json_decode($jsonData, true);

    if (isset($data[0]['keterangan'])) {
        echo "Data tidak ditemukan, atau NPSN diluar wilayah akses";
    } else {
        foreach ($data as $item) {
            $set = array(
                'id_ruang'                => $item['id_ruang'],
                'semester_id'             => $item['semester_id'],
                'sekolah_id'              => $item['sekolah_id'],
                'id_tanah'                => $item['id_tanah'],
                'id_bangunan'             => $item['id_bangunan'],
                'jenis_prasarana_id'      => $item['jenis_prasarana_id'],
                'jenis_prasarana'         => $item['jenis_prasarana'],
                'nama_tanah'              => $item['nama_tanah'],
                'nama_bangunan'           => $item['nama_bangunan'],
                'kd_ruang'                => $item['kd_ruang'],
                'nm_ruang'                => $item['nm_ruang'],
                'lantai'                  => $item['lantai'],
                'panjang'                 => $item['panjang'],
                'lebar'                   => $item['lebar'],
                'reg_pras'                => $item['reg_pras'],
                'kapasitas'               => $item['kapasitas'],
                'luas_ruang'              => $item['luas_ruang'],
                'luas_plester_m2'         => $item['luas_plester_m2'],
                'luas_plafon_m2'          => $item['luas_plafon_m2'],
                'luas_dinding_m2'         => $item['luas_dinding_m2'],
                'luas_daun_jendela_m2'    => $item['luas_daun_jendela_m2'],
                'luas_daun_pintu_m2'      => $item['luas_daun_pintu_m2'],
                'panj_kusen_m'            => $item['panj_kusen_m'],
                'luas_tutup_lantai_m2'    => $item['luas_tutup_lantai_m2'],
                'panj_inst_listrik_m'     => $item['panj_inst_listrik_m'],
                'jml_inst_listrik'        => $item['jml_inst_listrik'],
                'panj_inst_air_m'         => $item['panj_inst_air_m'],
                'jml_inst_air'            => $item['jml_inst_air'],
                'panj_drainase_m'         => $item['panj_drainase_m'],
                'luas_finish_struktur_m2' => $item['luas_finish_struktur_m2'],
                'luas_finish_plafon_m2'   => $item['luas_finish_plafon_m2'],
                'luas_finish_dinding_m2'  => $item['luas_finish_dinding_m2'],
                'luas_finish_kpj_m2'      => $item['luas_finish_kpj_m2'],
                'rusak_lisplang_talang'   => $item['rusak_lisplang_talang'],
                'ket_lisplang_talang'     => $item['ket_lisplang_talang'],
                'rusak_rangka_plafon'     => $item['rusak_rangka_plafon'],
                'ket_rangka_plafon'       => $item['ket_rangka_plafon'],
                'rusak_tutup_plafon'      => $item['rusak_tutup_plafon'],
                'ket_tutup_plafon'        => $item['ket_tutup_plafon'],
                'rusak_bata_dinding'      => $item['rusak_bata_dinding'],
                'ket_bata_dinding'        => $item['ket_bata_dinding'],
                'rusak_plester_dinding'   => $item['rusak_plester_dinding'],
                'ket_plester_dinding'     => $item['ket_plester_dinding'],
                'rusak_daun_jendela'      => $item['rusak_daun_jendela'],
                'ket_daun_jendela'        => $item['ket_daun_jendela'],
                'rusak_daun_pintu'        => $item['rusak_daun_pintu'],
                'ket_daun_pintu'          => $item['ket_daun_pintu'],
                'rusak_kusen'             => $item['rusak_kusen'],
                'ket_kusen'               => $item['ket_kusen'],
                'rusak_tutup_lantai'      => $item['rusak_tutup_lantai'],
                'ket_penutup_lantai'      => $item['ket_penutup_lantai'],
                'rusak_inst_listrik'      => $item['rusak_inst_listrik'],
                'ket_inst_listrik'        => $item['ket_inst_listrik'],
                'rusak_inst_air'          => $item['rusak_inst_air'],
                'ket_inst_air'            => $item['ket_inst_air'],
                'rusak_drainase'          => $item['rusak_drainase'],
                'ket_drainase'            => $item['ket_drainase'],
                'rusak_finish_struktur'   => $item['rusak_finish_struktur'],
                'ket_finish_struktur'     => $item['ket_finish_struktur'],
                'rusak_finish_plafon'     => $item['rusak_finish_plafon'],
                'ket_finish_plafon'       => $item['ket_finish_plafon'],
                'rusak_finish_dinding'    => $item['rusak_finish_dinding'],
                'ket_finish_dinding'      => $item['ket_finish_dinding'],
                'rusak_finish_kpj'        => $item['rusak_finish_kpj'],
                'ket_finish_kpj'          => $item['ket_finish_kpj'],
                'berfungsi'               => $item['berfungsi'],
                'blob_id'                 => $item['blob_id'],
                'bobot_kerusakan'         => $item['bobot_kerusakan'],
                'kondisi'                 => $item['kondisi'],
            );

            // Gunakan fungsi on_duplicate dari CI untuk memasukkan atau memperbarui data
            $exclude_columns = array('id_ruang');
            $CI->db->on_duplicate('ruang', $set, $exclude_columns);
        }
    }

    unlink($outputPathFile);
}

function _getPesertaDidik($token, $npsn)
{
    $CI                    = &get_instance();
    $jsonDataPtk           = getPesertaDidik($token, $npsn);
    $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    $jsonData = file_get_contents($outputPathFile);
    // Mendekode konten JSON menjadi struktur data yang dapat diakses
    $data = json_decode($jsonData, true);

    if (isset($data[0]['keterangan'])) {
        //Data tidak ditemukan, atau NPSN diluar wilayah akses
        echo "Data tidak ditemukan, atau NPSN diluar wilayah akses";
    } else {

        foreach ($data as $item) {

            $pesertaDidikId     = $item['peserta_didik_id']; //peserta_didik_id
            $sekolahId          = $item['sekolah_id']; //sekolah_id
            $rombonganBelajarId = $item['rombongan_belajar_id'];
            $nisn               = custom_trim($item['nisn']);
            $nik                = custom_trim($item['nik']);
            $no_kk              = custom_trim($item['no_kk']);
            $password           = 'INITIAL_PASSWORD'; //password_hash($nisn, PASSWORD_DEFAULT);
            $nama               = $item['nama'];
            $jenisKelamin       = $item['jenis_kelamin'];
            $agama              = $item['agama']; //
            $email              = custom_trim($item['email']);
            $tempat_lahir       = custom_trim($item['tempat_lahir']);
            $tanggal_lahir      = $item['tanggal_lahir'];
            $nama_ayah          = $item['nama_ayah'];
            $nama_ibu_kandung   = $item['nama_ibu_kandung'];
            /** tingkat_pendidikan dimasukkan dalam nama_rombel,
             *  karena lebih mudah dilakukan daripada menggunakan $tingkat_pendidikan
             *  dan menyesuaikan dengan kolom database  */
            $nama_rombel = $item['tingkat_pendidikan'];

            $nomorTeleponSeluler = custom_trim($item['nomor_telepon_seluler']);
            $kodeProvinsi        = $item['kode_provinsi'];
            $desa_kelurahan      = $item['desa_kelurahan'];
            $kecamatan           = $item['kecamatan'];
            $kabupaten           = $item['kabupaten'];
            $provinsi            = $item['provinsi'];
            $kodeKabupaten       = $item['kode_kabupaten']; //k
            $kodeKecamatan       = $item['kode_kecamatan']; //
            $kodeWilayah         = $item['kode_wilayah']; //
            $alamatJalan         = $item['alamat_jalan'];
            $tanggalMasukSekolah = $item['tanggal_masuk_sekolah']; //tanggal_masuk_sekolah
            $tanggalKeluar       = $item['tanggal_keluar']; //tanggal_keluar
            $jenisKeluar         = $item['jenis_keluar']; //jenis_keluar

            $set = array(

                'id'                => $pesertaDidikId,
                'sekolah_id'        => $sekolahId,
                'kelas_id'          => $rombonganBelajarId,
                'nisn'              => $nisn,
                'nik'               => $nik,
                'no_kk'             => $no_kk,
                'password'          => $password,
                'nama_lengkap'      => $nama,
                'jk'                => $jenisKelamin,
                'agama'             => $agama,
                'email'             => $email,
                'tempat_lahir'      => $tempat_lahir,
                'tanggal_lahir'     => $tanggal_lahir,
                'nama_ayah'         => $nama_ayah,
                'nama_ibu_kandung'  => $nama_ibu_kandung,
                'nama_rombel'       => $nama_rombel,
                'telp'              => $nomorTeleponSeluler,
                'provinsi'          => $kodeProvinsi,
                'kabupaten'         => $kodeKabupaten,
                'kecamatan'         => $kodeKecamatan,
                'kelurahan'         => $kodeWilayah,
                'alamat'            => $alamatJalan,
                'tgl_masuk_sekolah' => $tanggalMasukSekolah,
                'tanggal_keluar'    => $tanggalKeluar,
                'jenis_keluar'      => $jenisKeluar,
                'tgl_update'        => date('Y-m-d H:i:s'),
            );

            $exclude_columns = array('password', 'id');
            $CI->db->on_duplicate('siswa', $set, $exclude_columns);
        }
    }

    $CI->db->where('npsn', $npsn);
    $CI->db->update('sekolah', array('tgl_update_siswa' => date('Y-m-d H:i:s')));

    unlink($outputPathFile);
    // $this->exportAllSiswa($npsn);
}
