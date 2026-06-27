<?php

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Cronjob extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'api', 'libs', 'cronjob'));
        $this->load->library(array('session'));
    }

    private function _cek_libur($tgl)
    {
        $this->db->select('a.fulldate,IF(a.dayofweek = 7,"YA",IF(b.id > 0,"YA","TIDAK")) AS libur');
        $this->db->join('hari_libur b', 'a.fulldate = b.tgl', 'left');
        $this->db->where('a.fulldate', $tgl);
        $dates = $this->db->get('dates a')->row_array();

        return $dates['libur'];
    }

    private function update_batch_remove_old_photos($updateArray)
    {
        $this->db->update_batch('kehadiran_pegawai', $updateArray, 'id');
    }

    /**
     * Fungsi 'remove_old_photos' dalam PHP digunakan untuk menghapus foto-foto lama dari database dan memperbarui
     * Catatan yang sesuai.
     */
    public function remove_old_photos()
    {

        /*Kode ini menjalankan kueri database untuk memilih 'id', 'DATE(jam_masuk)' sebagai 'tgl',
        'IFNULL(foto_masuk,'empty')' sebagai 'f_masuk', dan 'IFNULL(foto_pulang,'empty')' sebagai 'f_pulang'
        dari tabel 'kehadiran_pegawai'. Kueri menyertakan kondisi untuk memilih baris saja
        di mana 'DATE(jam_masuk)' kurang dari atau sama dengan 10 hari yang lalu dari tanggal saat ini dan
        kolom 'cleaned_file_photo' diatur ke 'N'. Hasil kueri disimpan di variabel.*/

        $old_photos = $this->db->query(
            "SELECT id,DATE(jam_masuk) AS tgl,
                    IFNULL(foto_masuk,'empty') AS f_masuk,
                    IFNULL(foto_pulang,'empty') AS f_pulang
            FROM kehadiran_pegawai
            WHERE DATE(jam_masuk) <= (DATE(NOW()) - INTERVAL 10 DAY)  AND cleaned_file_photo = 'N'
            ORDER BY DATE(jam_masuk) DESC"
        );

        echo "row to smash:" . $old_photos->num_rows();

        $updateArray = array();
        foreach ($old_photos->result_array() as $old) {

            @unlink('uploads/' . $old['f_masuk']);
            @unlink('uploads/' . $old['f_pulang']);

            $updateArray[] = array(
                'id'                 => $old['id'],
                'cleaned_file_photo' => 'Y',
            );
        }

        $chunk = array_chunk($updateArray, 100);
        for ($i = 0; $i < count($chunk); $i++) {
            $this->update_batch_remove_old_photos($chunk[$i]);
            echo ".";
        }
    }

    // public function start_bot_check()
    // {
    //     $this->output->set_header('HTTP/1.0 200 OK');
    //     $this->output->set_header('HTTP/1.1 200 OK');
    //     $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    //     $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
    //     $this->output->set_header('Cache-Control: post-check=0, pre-check=0');
    //     $this->output->set_header('Pragma: no-cache');

    //     //cek apakah diatas jam 11 malam

    //     if (date("G") >= 23) {
    //         // current time is greater than 11:00 AM
    //         //start check
    //         $current_date = date('Y-m-d');
    //         $cek          = $this->_cek_libur($current_date);

    //         if ($cek === 'TIDAK') {

    //             $cek_bot = $this->db->get_where('cronjon_status', array('DATE(start_at)' => $current_date));
    //             if ($cek_bot->num_rows() == 0) {
    //                 $start_at = date("Y-m-d H:i:s");

    //                 //smashing alpa pegawai
    //                 $this->db->query(
    //                     "INSERT INTO kehadiran_pegawai(pegawai_id,jam_masuk,jam_pulang,status_masuk,status_pulang)
    //                      SELECT id,'$current_date 23:00:00','$current_date 23:00:00','ALPA','ALPA'
    //                      FROM pegawai
    //                      WHERE aktif = 'YA'
    //                            AND id NOT IN (SELECT pegawai_id FROM kehadiran_pegawai WHERE DATE(jam_masuk) = '$current_date')"
    //                 );

    //                 $pegawai_alfa = $this->db->affected_rows();

    //                 //smashing alpa siswa
    //                 $this->db->query(
    //                     "INSERT INTO kehadiran_siswa(siswa_id,jam_absen,`status`)
    //                      SELECT id,'$current_date','ALPA'
    //                      FROM siswa
    //                      WHERE aktif = 'YA'
    //                            AND id NOT IN (SELECT siswa_id FROM kehadiran_siswa WHERE DATE(jam_absen) = '$current_date')"
    //                 );

    //                 $siswa_alfa = $this->db->affected_rows();

    //                 $end_at = date("Y-m-d H:i:s");

    //                 $this->db->insert(
    //                     'cronjon_status',
    //                     array(
    //                         'start_at' => $start_at,
    //                         'end_at'   => $end_at,
    //                         'result'   => '{pegawai_alfa:' . $pegawai_alfa . ',siswa_alfa:' . $siswa_alfa . '}',
    //                     )
    //                 );
    //             }
    //         }
    //     }

    //     echo "Cronbot_job_end_at_" . date("Y-m-d H:i:s");
    // }

    /**
     * The function inserts the current date and the corresponding jadwal_mengajar_id into the
     * guru_mengajar table, based on the day of the week, and updates the tgl_update field if there is a
     * duplicate key.
     */

    //jalankan setiap jam 5 pagi
    public function start_cek_gurumengajar()
    {
        $this->output->set_header('HTTP/1.0 200 OK');
        $this->output->set_header('HTTP/1.1 200 OK');
        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $currentDate = date('Y-m-d');
        $cek         = $this->_cek_libur($currentDate);
        if ($cek === 'TIDAK') {
            $this->db->query("
            INSERT INTO guru_mengajar(tgl,jadwal_mengajar_id)
            SELECT CURDATE(),id
            FROM jadwal_mengajar
            WHERE hari = (
                          CASE
                               WHEN DAYOFWEEK(CURDATE()) = 1 THEN 'MINGGU'
                               WHEN DAYOFWEEK(CURDATE()) = 2 THEN 'SENIN'
                               WHEN DAYOFWEEK(CURDATE()) = 3 THEN 'SELASA'
                               WHEN DAYOFWEEK(CURDATE()) = 4 THEN 'RABU'
                               WHEN DAYOFWEEK(CURDATE()) = 5 THEN 'KAMIS'
                               WHEN DAYOFWEEK(CURDATE()) = 6 THEN 'JUMAT'
                               WHEN DAYOFWEEK(CURDATE()) = 7 THEN 'SABTU'
                              END)
            ORDER BY jam_mulai ASC
            ON DUPLICATE KEY UPDATE tgl_update = NOW()
        ");
        }
    }

    //jalankan setiap jam 5 sore
    public function start_cek_presensi()
    {
        $this->output->set_header('HTTP/1.0 200 OK');
        $this->output->set_header('HTTP/1.1 200 OK');
        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        //$currentHour = date("G");
        // if ($currentHour >= 23) {
        $currentDate = date('Y-m-d');
        $cek         = $this->_cek_libur($currentDate);
        if ($cek === 'TIDAK') {

            $cek_bot = $this->db->get_where('cronjon_status', array('DATE(start_at)' => $currentDate));
            if ($cek_bot->num_rows() == 0) {
                $startAt     = date("Y-m-d H:i:s");
                $endAt       = null;
                $pegawaiAlfa = 0;
                $siswaAlfa   = 0;

                // Get the list of active employees who have not checked in for the current date
                $pegawaiQuery = "INSERT INTO kehadiran_pegawai(pegawai_id, jam_masuk, jam_pulang, status_masuk, status_pulang)
                                 SELECT p.id, '$currentDate 23:00:00', '$currentDate 23:00:00', 'ALPA', 'ALPA'
                                 FROM pegawai p
                                 WHERE p.aktif = 'YA'
                                        AND p.id NOT IN (SELECT kp.pegawai_id FROM kehadiran_pegawai kp WHERE DATE(kp.jam_masuk) = '$currentDate')";
                $this->db->query($pegawaiQuery);
                $pegawaiAlfa = $this->db->affected_rows();
                // Get the list of active students who have not checked in for the current date

                /*'' adalah kueri SQL yang menyisipkan catatan ke dalam tabel 'kehadiran_siswa'
                . Ini memilih 'id' siswa aktif ('siswa_id') dari tabel 'siswa'
                yang belum check-in untuk tanggal saat ini. Kolom 'jam_absen' disetel ke
                tanggal saat ini, dan kolom 'status' disetel ke 'ALPA'. Kueri ini memastikan
                bahwa semua siswa aktif yang belum check-in untuk tanggal saat ini akan ditandai
                sebagai 'ALPA' dalam tabel 'kehadiran_siswa'.*/

                $siswaQuery = "INSERT INTO kehadiran_siswa(siswa_id, jam_absen, `status`)
                                   SELECT s.id, '$currentDate', 'ALPA'
                                   FROM siswa s
                                   WHERE s.aktif = 'YA'
                                        AND s.id NOT IN (SELECT ks.siswa_id FROM kehadiran_siswa ks WHERE DATE(ks.jam_absen) = '$currentDate')";
                $this->db->query($siswaQuery);
                $siswaAlfa = $this->db->affected_rows();
                $endAt     = date("Y-m-d H:i:s");
                // Insert the cronjon status into the database
                $this->db->insert(
                    'cronjon_status',
                    array(
                        'start_at'     => $startAt,
                        'end_at'       => $endAt,
                        'pegawai_alfa' => $pegawaiAlfa,
                        'siswa_alfa'   => $siswaAlfa,
                    )
                );
            }
        }
        // }
        echo "Cronbot_job_end_at_" . date("Y-m-d H:i:s");
    }

    public function getPesertaDidikAndPegawai()
    {

        //dapatkan token
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        $this->db->select('npsn');
        $this->db->where('cron_update_data', 'Y');
        $this->db->limit(1);
        $sekolah = $this->db->get('sekolah');

        if ($sekolah->num_rows() > 0) {

            $npsn = $sekolah->row()->npsn;

            _getPesertaDidik($token, $npsn);
            $this->exportAllSiswa($npsn);


            _getPtk($token, $npsn);
            $this->exportAllPegawai($npsn);

            $this->db->where('npsn', $npsn);
            $this->db->update('sekolah', array('cron_update_data' => 'N'));

            echo $npsn;
        }

        //data pegawai

    }

    // private function _getPesertaDidik($token, $npsn)
    // {
    //     $jsonDataPtk           = getPesertaDidik($token, $npsn);
    //     $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    //     $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    //     $jsonData = file_get_contents($outputPathFile);
    //     // Mendekode konten JSON menjadi struktur data yang dapat diakses
    //     $data = json_decode($jsonData, true);

    //     if (isset($data[0]['keterangan'])) {
    //         //Data tidak ditemukan, atau NPSN diluar wilayah akses
    //         echo "Data tidak ditemukan, atau NPSN diluar wilayah akses";
    //     } else {

    //         foreach ($data as $item) {

    //             $pesertaDidikId     = $item['peserta_didik_id']; //peserta_didik_id
    //             $sekolahId          = $item['sekolah_id']; //sekolah_id
    //             $rombonganBelajarId = $item['rombongan_belajar_id'];
    //             $nisn               = custom_trim($item['nisn']);
    //             $nik                = custom_trim($item['nik']);
    //             $no_kk              = custom_trim($item['no_kk']);
    //             $password           = 'INITIAL_PASSWORD'; //password_hash($nisn, PASSWORD_DEFAULT);
    //             $nama               = $item['nama'];
    //             $jenisKelamin       = $item['jenis_kelamin'];
    //             $agama              = $item['agama']; //
    //             $email              = custom_trim($item['email']);
    //             $tempat_lahir       = custom_trim($item['tempat_lahir']);
    //             $tanggal_lahir      = $item['tanggal_lahir'];
    //             $nama_ayah          = $item['nama_ayah'];
    //             $nama_ibu_kandung   = $item['nama_ibu_kandung'];
    //             /** tingkat_pendidikan dimasukkan dalam nama_rombel,
    //              *  karena lebih mudah dilakukan daripada menggunakan $tingkat_pendidikan
    //              *  dan menyesuaikan dengan kolom database  */
    //             $nama_rombel = $item['tingkat_pendidikan'];

    //             $nomorTeleponSeluler = custom_trim($item['nomor_telepon_seluler']);
    //             $kodeProvinsi        = $item['kode_provinsi'];
    //             $desa_kelurahan      = $item['desa_kelurahan'];
    //             $kecamatan           = $item['kecamatan'];
    //             $kabupaten           = $item['kabupaten'];
    //             $provinsi            = $item['provinsi'];
    //             $kodeKabupaten       = $item['kode_kabupaten']; //k
    //             $kodeKecamatan       = $item['kode_kecamatan']; //
    //             $kodeWilayah         = $item['kode_wilayah']; //
    //             $alamatJalan         = $item['alamat_jalan'];
    //             $tanggalMasukSekolah = $item['tanggal_masuk_sekolah']; //tanggal_masuk_sekolah
    //             $tanggalKeluar       = $item['tanggal_keluar']; //tanggal_keluar
    //             $jenisKeluar         = $item['jenis_keluar']; //jenis_keluar

    //             $set = array(

    //                 'id'                => $pesertaDidikId,
    //                 'sekolah_id'        => $sekolahId,
    //                 'kelas_id'          => $rombonganBelajarId,
    //                 'nisn'              => $nisn,
    //                 'nik'               => $nik,
    //                 'no_kk'             => $no_kk,
    //                 'password'          => $password,
    //                 'nama_lengkap'      => $nama,
    //                 'jk'                => $jenisKelamin,
    //                 'agama'             => $agama,
    //                 'email'             => $email,
    //                 'tempat_lahir'      => $tempat_lahir,
    //                 'tanggal_lahir'     => $tanggal_lahir,
    //                 'nama_ayah'         => $nama_ayah,
    //                 'nama_ibu_kandung'  => $nama_ibu_kandung,
    //                 'nama_rombel'       => $nama_rombel,
    //                 'telp'              => $nomorTeleponSeluler,
    //                 'provinsi'          => $kodeProvinsi,
    //                 'kabupaten'         => $kodeKabupaten,
    //                 'kecamatan'         => $kodeKecamatan,
    //                 'kelurahan'         => $kodeWilayah,
    //                 'alamat'            => $alamatJalan,
    //                 'tgl_masuk_sekolah' => $tanggalMasukSekolah,
    //                 'tanggal_keluar'    => $tanggalKeluar,
    //                 'jenis_keluar'      => $jenisKeluar,
    //                 'tgl_update'        => date('Y-m-d H:i:s'),
    //             );

    //             $exclude_columns = array('password', 'id');
    //             $this->db->on_duplicate('siswa', $set, $exclude_columns);
    //         }
    //     }

    //     unlink($outputPathFile);
    //     $this->exportAllSiswa($npsn);
    // }

    // private function _getPtk($token, $npsn)
    // {
    //     $jsonDataPtk           = getPtk($token, $npsn);
    //     $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    //     $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    //     $jsonData = file_get_contents($outputPathFile);
    //     // Mendekode konten JSON menjadi struktur data yang dapat diakses
    //     $data = json_decode($jsonData, true);

    //     if (isset($data[0]['keterangan'])) {
    //         //Data tidak ditemukan, atau NPSN diluar wilayah akses
    //         echo "Data tidak ditemukan, atau NPSN diluar wilayah akses";
    //     } else {
    //         foreach ($data as $item) {
    //             $ptkId             = $item['ptk_id'];
    //             $sekolahId         = $item['sekolah_id'];
    //             $nuptk             = $item['nuptk'];
    //             $nip               = $item['nip'];
    //             $nama              = $item['nama'];
    //             $tempatLahir       = $item['tempat_lahir'];
    //             $tanggalLahir      = $item['tanggal_lahir'];
    //             $nik               = $item['nik'];
    //             $jenisKelamin      = $item['jenis_kelamin'];
    //             $agama             = $item['agama'];
    //             $skPengangkatan    = $item['sk_pengangkatan'];
    //             $tmtPengangkatan   = $item['tmt_pengangkatan'];
    //             $statusKepegawaian = $item['status_kepegawaian']; //status_pegawai
    //             $jenisPtk          = $item['jenis_ptk']; //jabatan
    //             $password          = 'INITIAL_PASSWORD'; //password_hash($nuptk, PASSWORD_DEFAULT);
    //             $alamatJalan       = $item['alamat_jalan'];
    //             $email             = $item['email'];
    //             $telp              = $item['no_hp'];
    //             $statusKeaktifan   = $item['status_keaktifan']; //aktif

    //             $riwayatSertifikasiBidangStudi                 = $item['riwayat_sertifikasi_bidang_studi'];
    //             $riwayatSertifikasiJenisSertifikasi            = $item['riwayat_sertifikasi_jenis_sertifikasi'];
    //             $riwayatSertifikasiTahunSertifikasi            = $item['riwayat_sertifikasi_tahun_sertifikasi'];
    //             $riwayatSertifikasiNomorSertifikat             = $item['riwayat_sertifikasi_nomor_sertifikat'];
    //             $riwayatSertifikasiNrg                         = $item['riwayat_sertifikasi_nrg'];
    //             $riwayatSertifikasiNomorPeserta                = $item['riwayat_sertifikasi_nomor_peserta'];
    //             $riwayatPendidikanFormalBidangStudi            = $item['riwayat_pendidikan_formal_bidang_studi'];
    //             $riwayatPendidikanFormalJenjangPendidikan      = $item['riwayat_pendidikan_formal_jenjang_pendidikan'];
    //             $riwayatPendidikanFormalGelarAkademik          = $item['riwayat_pendidikan_formal_gelar_akademik'];
    //             $riwayatPendidikanFormalSatuanPendidikanFormal = $item['riwayat_pendidikan_formal_satuan_pendidikan_formal'];
    //             $riwayatPendidikanFormalFakultas               = $item['riwayat_pendidikan_formal_fakultas'];
    //             $riwayatPendidikanFormalKependidikan           = $item['riwayat_pendidikan_formal_kependidikan'];
    //             $riwayatPendidikanFormalTahunMasuk             = $item['riwayat_pendidikan_formal_tahun_masuk'];
    //             $riwayatPendidikanFormalTahunLulus             = $item['riwayat_pendidikan_formal_tahun_lulus'];
    //             $riwayatPendidikanFormalNim                    = $item['riwayat_pendidikan_formal_nim'];
    //             $riwayatPendidikanFormalStatusKuliah           = $item['riwayat_pendidikan_formal_status_kuliah'];
    //             $riwayatPendidikanFormalSemester               = $item['riwayat_pendidikan_formal_semester'];
    //             $riwayatPendidikanFormalIpk                    = $item['riwayat_pendidikan_formal_ipk'];

    //             $set = array(
    //                 'id'                                                 => $ptkId,
    //                 'sekolah_id'                                         => $sekolahId,
    //                 'nuptk'                                              => $nuptk,
    //                 'nip'                                                => $nip,
    //                 'nama_lengkap'                                       => $nama,
    //                 'tempat_lahir'                                       => $tempatLahir,
    //                 'tgl_lahir'                                          => $tanggalLahir,
    //                 'nik'                                                => $nik,
    //                 'jk'                                                 => $jenisKelamin,
    //                 'agama'                                              => $agama,
    //                 'sk_pengangkatan'                                    => $skPengangkatan,
    //                 'tmt_pengangkatan'                                   => $tmtPengangkatan,
    //                 'jabatan'                                            => $jenisPtk,
    //                 'status_pegawai'                                     => $statusKepegawaian,
    //                 'password'                                           => $password,
    //                 'alamat'                                             => $alamatJalan,
    //                 'email'                                              => $email,
    //                 'telp'                                               => $telp,
    //                 'aktif'                                              => $statusKeaktifan,

    //                 'riwayat_sertifikasi_bidang_studi'                   => $riwayatSertifikasiBidangStudi,
    //                 'riwayat_sertifikasi_jenis_sertifikasi'              => $riwayatSertifikasiJenisSertifikasi,
    //                 'riwayat_sertifikasi_tahun_sertifikasi'              => $riwayatSertifikasiTahunSertifikasi,
    //                 'riwayat_sertifikasi_nomor_sertifikat'               => $riwayatSertifikasiNomorSertifikat,
    //                 'riwayat_sertifikasi_nrg'                            => $riwayatSertifikasiNrg,
    //                 'riwayat_sertifikasi_nomor_peserta'                  => $riwayatSertifikasiNomorPeserta,
    //                 'riwayat_pendidikan_formal_bidang_studi'             => $riwayatPendidikanFormalBidangStudi,
    //                 'riwayat_pendidikan_formal_jenjang_pendidikan'       => $riwayatPendidikanFormalJenjangPendidikan,
    //                 'riwayat_pendidikan_formal_gelar_akademik'           => $riwayatPendidikanFormalGelarAkademik,
    //                 'riwayat_pendidikan_formal_satuan_pendidikan_formal' => $riwayatPendidikanFormalSatuanPendidikanFormal,
    //                 'riwayat_pendidikan_formal_fakultas'                 => $riwayatPendidikanFormalFakultas,
    //                 'riwayat_pendidikan_formal_kependidikan'             => $riwayatPendidikanFormalKependidikan,
    //                 'riwayat_pendidikan_formal_tahun_masuk'              => $riwayatPendidikanFormalTahunMasuk,
    //                 'riwayat_pendidikan_formal_tahun_lulus'              => $riwayatPendidikanFormalTahunLulus,
    //                 'riwayat_pendidikan_formal_nim'                      => $riwayatPendidikanFormalNim,
    //                 'riwayat_pendidikan_formal_status_kuliah'            => $riwayatPendidikanFormalStatusKuliah,
    //                 'riwayat_pendidikan_formal_semester'                 => $riwayatPendidikanFormalSemester,
    //                 'riwayat_pendidikan_formal_ipk'                      => $riwayatPendidikanFormalIpk,
    //                 'tgl_update'                                         => date('Y-m-d H:i:s'),
    //             );

    //             $exclude_columns = array('password', 'id');
    //             $this->db->on_duplicate('pegawai', $set, $exclude_columns);
    //         }
    //     }

    //     unlink($outputPathFile);
    //     $this->exportAllPegawai($npsn);
    // }

    public function bulk_excel($path, $fileName, $recordSet)
    {
        // Membuat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // Mendapatkan sheet aktif
        $sheet = $spreadsheet->getActiveSheet();

        // Mendapatkan nama kolom dari record set
        $columns = $recordSet->list_fields();

        // Menambahkan header kolom ke sheet
        $columnIndex = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($columnIndex . '1', $column);
            $sheet->getStyle($columnIndex . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '1')->getFont()->setBold(true);
            // $sheet->getStyle($columnIndex . '1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT); // Mengatur format sel sebagai teks
            $sheet->getColumnDimension($columnIndex)->setAutoSize(true); // Mengatur lebar kolom otomatis
            $columnIndex++;
        }

        // Menambahkan data dari record set ke sheet
        $rowIndex = 2;
        foreach ($recordSet->result_array() as $row) {
            $columnIndex = 'A';
            foreach ($columns as $column) {
                $sheet->setCellValue($columnIndex . $rowIndex, $row[$column]);
                $columnIndex++;
            }
            $rowIndex++;
        }

        // Membuat objek Writer untuk menyimpan spreadsheet ke file
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Nama file yang akan diunduh (tanpa ekstensi)
        $filePath = $path . $fileName . '.xlsx';

        // Menyimpan spreadsheet ke file
        try {
            // Menyimpan spreadsheet ke file
            $writer->save($filePath);

            return "File Excel berhasil disimpan di: $filePath";
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            // Penanganan kesalahan jika drive penuh atau direktori output tidak ada
            return "Terjadi kesalahan saat menyimpan file Excel: " . $e->getMessage();
        }
    }

    // public function exportAllPegawai()
    // {
    //     $sekolah = $this->db->get('sekolah');

    //     $cronjobInfo = cek_cronjob(site_url('cronjob/exportAllPegawai'));
    //     //segera hapus cronjob supaya tidak berulang
    //     delete_cronjob($cronjobInfo['jobId']);

    //     foreach ($sekolah->result_array() as $s) {
    //         $this->db->select('a.nuptk,a.nip,a.nama_lengkap,a.tempat_lahir,a.tgl_lahir,
    //                        a.jabatan,a.status_pegawai,a.sk_pengangkatan,a.tmt_pengangkatan');
    //         $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
    //         $this->db->where('b.npsn', $s['npsn']);
    //         $this->db->order_by('a.nama_lengkap', 'asc');
    //         $q = $this->db->get('pegawai a');

    //         $this->bulk_excel(FCPATH . 'temp/export/pegawai/', cleanFilename('pegawai-' . $s['npsn'] . '-' . $s['nama']), $q);
    //     }

    //     zipFolder(FCPATH . 'temp/export/pegawai/', 'Export-pegawai-' . date('d-m-y H:i:s'));
    //     unlink(FCPATH . 'temp/export/pegawai/sedang.proses');

    // }

    // public function exportAllSiswa()
    // {
    //     $this->db->order_by('npsn', 'asc');
    //     $sekolah = $this->db->get('sekolah');

    //     $cronjobInfo = cek_cronjob(site_url('cronjob/exportAllSiswa'));
    //     //segera hapus cronjob supaya tidak berulang
    //     delete_cronjob($cronjobInfo['jobId']);

    //     foreach ($sekolah->result_array() as $s) {
    //         $this->db->select('a.nisn,a.nik,a.nama_lengkap,
    //         c.nama as Kabupaten,
    //         d.nama AS kecamatan,
    //         e.nama AS kelurahan,a.alamat');
    //         $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
    //         $this->db->join('wilayah_kabupaten c', 'a.kabupaten = c.id', 'left');
    //         $this->db->join('wilayah_kecamatan d', 'a.kecamatan = d.id', 'left');
    //         $this->db->join('wilayah_kelurahan e', 'a.kelurahan = e.id', 'left');
    //         $this->db->where('b.npsn', $s['npsn']);
    //         $this->db->order_by('a.nama_lengkap', 'asc');
    //         $q = $this->db->get('siswa a');

    //         $this->bulk_excel(FCPATH . 'temp/export/siswa/', cleanFilename('siswa-' . $s['npsn'] . '-' . $s['nama']), $q);
    //         // echo $s['npsn'] . '&nbsp';
    //     }

    //     zipFolder(FCPATH . 'temp/export/siswa/', 'Export-siswa-' . date('d-m-y H:i:s'));
    //     unlink(FCPATH . 'temp/export/siswa/sedang.proses');

    // }

    public function exportAllPegawai($npsn)
    {

        $this->db->select('a.nuptk,a.nip,a.nama_lengkap,a.tempat_lahir,a.tgl_lahir,
                           a.jabatan,a.status_pegawai,a.sk_pengangkatan,a.tmt_pengangkatan,                           
                           riwayat_pendidikan_formal_bidang_studi AS `Bidang Studi`,
                           riwayat_pendidikan_formal_jenjang_pendidikan AS `Jenjang Pendidikan`,
                           riwayat_sertifikasi_bidang_studi AS `Sertifikasi Bidang Studi`                           
                           ');
        $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
        $this->db->where('b.npsn', $npsn);
        $this->db->order_by('a.nama_lengkap', 'asc');
        $q = $this->db->get('pegawai a');

        $sekolah = $this->db->get_where('sekolah', array('npsn' => $npsn))->row_array();

        $this->bulk_excel(FCPATH . 'temp/export/pegawai/', cleanFilename('pegawai-' . $npsn . '-' . $sekolah['nama']), $q);
    }

    public function exportAllSiswa($npsn)
    {

        $this->db->select('a.nisn,a.nik,a.nama_lengkap,
            c.nama as Kabupaten,
            d.nama AS kecamatan,
            e.nama AS kelurahan,
            a.alamat');
        $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'a.kabupaten = c.id', 'left');
        $this->db->join('wilayah_kecamatan d', 'a.kecamatan = d.id', 'left');
        $this->db->join('wilayah_kelurahan e', 'a.kelurahan = e.id', 'left');
        $this->db->where('b.npsn', $npsn);
        $this->db->order_by('a.nama_lengkap', 'asc');
        $q = $this->db->get('siswa a');

        $sekolah = $this->db->get_where('sekolah', array('npsn' => $npsn))->row_array();

        $this->bulk_excel(FCPATH . 'temp/export/siswa/', cleanFilename('siswa-' . $npsn . '-' . $sekolah['nama']), $q);
    }


    // public function set_all_passwd_same()
    // {
    //     $batch_size = 10;  // Ukuran batch untuk memperbarui
    //     $offset = 0;

    //     while (true) {
    //         // Ambil data pegawai dalam batch kecil
    //         $this->db->where('password','INITIAL_PASSWORD');
    //         $peg = $this->db->limit($batch_size, $offset)->get('pegawai')->result_array();

    //         if (empty($peg)) {
    //             break;  // Jika tidak ada lagi data, keluar dari loop
    //         }

    //         $data = array();

    //         foreach ($peg as $row) {
    //             $data[] = array(
    //                 'id' => $row['id'],
    //                 'password' => password_hash($row['nik'], PASSWORD_DEFAULT)
    //             );
    //         }

    //         // Update batch
    //         $this->db->update_batch('pegawai', $data, 'id');

    //         $offset += $batch_size;  // Pindahkan ke batch berikutnya
    //     }
    // }


    public function test_excel()
    {
        $file_path = './uploads/verval-siswa.xlsx';

        $reader      = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($file_path);
        // $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Ambil data dari baris ke-3
        // $excel_data = [];
        // foreach ($sheetData as $key => $row) {
        //     // Mulai mengambil data dari baris ke-3
        //     if ($key < 2) continue;

        //     $excel_data[] = array(
        //         'status' => $row['H'] // Kolom ke-5
        //     );
        // }

        // Hitung jumlah 'VALID', 'TIDAK VALID', dan 'PENDING' menggunakan filter
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();
        $lastRow   = $worksheet->getHighestRow();

        // $valid_count = $worksheet->filterColumn('E', 'VALID')->getHighestDataRow() - 2;
        // $tidak_valid_count = $worksheet->filterColumn('E', 'TIDAK VALID')->getHighestDataRow() - 2;
        // $pending_count = $worksheet->filterColumn('E', 'PENDING')->getHighestDataRow() - 2;
        $worksheet->setCellValue('A' . ($lastRow + 1), 'Jumlah VALID: ');
        $worksheet->setCellValue('B' . ($lastRow + 1), '=COUNTIF(H2:H' . $lastRow . ', "VALID")');

        $worksheet->setCellValue('C' . ($lastRow + 1), 'Jumlah TIDAK VALID: ');
        $worksheet->setCellValue('D' . ($lastRow + 1), '=COUNTIF(H2:H' . $lastRow . ', "TIDAK VALID")');

        $worksheet->setCellValue('E' . ($lastRow + 1), 'Jumlah PENDING: ');
        $worksheet->setCellValue('F' . ($lastRow + 1), '=COUNTIF(H2:H' . $lastRow . ', "PENDING")');

        // echo $validCount;
        // Data untuk ditampilkan pada view
        // $view_data = array(
        //     'valid_count' => $valid_count,
        //     'tidak_valid_count' => $tidak_valid_count,
        //     'pending_count' => $pending_count
        // );
    }
}
