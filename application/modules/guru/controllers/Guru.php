<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $sekolah_id;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));
        $this->load->model('Doc_model', 'doc_m');

        $this->breadcrumbs->load_config('default');

        $user_level       = $this->session->userdata('user_level');
        $this->user_id    = $this->session->userdata('user_id');
        $this->sekolah_id = $this->session->userdata('user_sekolah_id');

        $this->base_breadcrumbs = '/guru';

        if ($user_level !== 'GURU') {
            redirect(site_url('signin'), 'reload');
        }

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    public function _page_output($output = null)
    {

        $active_pelajaran = $this->_get_active_pelajaran();
        if (!empty($active_pelajaran)) {
            $id = $active_pelajaran[0]['id'];

            $next_pelajaran = $this->_get_next_pelajaran($id);

            $output['active_pelajaran'] = $active_pelajaran;
            $output['next_pelajaran']   = $next_pelajaran;
        } else {
            $output['active_pelajaran'] = $active_pelajaran;
            $first_pelajaran            = $this->_get_first_pelajaran();
            $output['next_pelajaran']   = $first_pelajaran;
        }

        $output['user_id']      = $this->user_id;
        $output['nama_lengkap'] = $this->session->userdata('user_nama');
        $this->load->view('master_page.php', (array) $output);
    }

    public function _get_active_pelajaran()
    {
        $this->db->select('a.id, a.dokumentasi, a.uraian,
                           CONCAT(a.tgl," ",b.jam_mulai) AS jam_mulai,
                           CONCAT(a.tgl," ", b.jam_selesai) AS jam_selesai,
                           IFNULL(a.jam_mulai, "-") AS time_in, IFNULL(a.jam_selesai, "-") AS time_out,
                           IFNULL(a.foto_mulai, "-") AS foto_in, IFNULL(a.foto_selesai, "-") AS foto_out,
                           c.nama_kelas, d.nama AS nama_pelajaran
                        ');
        $this->db->from('guru_mengajar a');
        $this->db->join('jadwal_mengajar b', 'a.jadwal_mengajar_id = b.id', 'left');

        $this->db->join('kelas c', 'b.kelas_id = c.id', 'left');
        $this->db->join('matapelajaran d', 'b.matapelajaran_id = d.id', 'left');

        $this->db->where('b.pegawai_id', $this->user_id);
        $this->db->where('a.tgl', 'DATE(NOW())', false);
        $this->db->where('DATE_FORMAT(NOW(),"%H:%i:%s") > b.jam_mulai', "", false);
        $this->db->order_by('b.jam_mulai', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function _get_next_pelajaran($active_pelajaran_id)
    {
        $this->db->select('a.id, a.dokumentasi, a.uraian,
                           CONCAT(a.tgl," ",b.jam_mulai) AS jam_mulai,
                           CONCAT(a.tgl," ", b.jam_selesai) AS jam_selesai,
                           IFNULL(a.jam_mulai, "-") AS time_in, IFNULL(a.jam_selesai, "-") AS time_out,
                           IFNULL(a.foto_mulai, "-") AS foto_in, IFNULL(a.foto_selesai, "-") AS foto_out,
                           c.nama_kelas, d.nama AS nama_pelajaran
                        ');
        $this->db->from('guru_mengajar a');
        $this->db->join('jadwal_mengajar b', 'a.jadwal_mengajar_id = b.id', 'left');

        $this->db->join('kelas c', 'b.kelas_id = c.id', 'left');
        $this->db->join('matapelajaran d', 'b.matapelajaran_id = d.id', 'left');

        $this->db->where('b.pegawai_id', $this->user_id);
        $this->db->where('a.tgl', 'DATE(NOW())', false);
        $this->db->where('a.id >', $active_pelajaran_id);
        $this->db->order_by('b.jam_mulai', 'ASC');
        $this->db->limit(1);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function _get_first_pelajaran()
    {
        $this->db->select('a.id, a.dokumentasi, a.uraian,
                           CONCAT(a.tgl," ",b.jam_mulai) AS jam_mulai,
                           CONCAT(a.tgl," ", b.jam_selesai) AS jam_selesai,
                           IFNULL(a.jam_mulai, "-") AS time_in, IFNULL(a.jam_selesai, "-") AS time_out,
                           IFNULL(a.foto_mulai, "-") AS foto_in, IFNULL(a.foto_selesai, "-") AS foto_out,
                           c.nama_kelas, d.nama AS nama_pelajaran
                        ');
        $this->db->from('guru_mengajar a');
        $this->db->join('jadwal_mengajar b', 'a.jadwal_mengajar_id = b.id', 'left');

        $this->db->join('kelas c', 'b.kelas_id = c.id', 'left');
        $this->db->join('matapelajaran d', 'b.matapelajaran_id = d.id', 'left');

        $this->db->where('b.pegawai_id', $this->user_id);
        $this->db->where('a.tgl', 'DATE(NOW())', false);
        $this->db->order_by('b.jam_mulai', 'ASC');
        $this->db->limit(1);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function index()
    {
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);

        $data['page_name']  = 'beranda';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function pengajuan_izin()
    {

        $data = array();

        if (!empty($_POST)) {
            $this->form_validation->set_rules('tgl_izin', 'Tanggal Izin', 'required');
            $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
            $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

            if ($this->form_validation->run() == true) {

                $in = array(
                    'tgl_pengajuan' => date('Y-m-d'),
                    'pegawai_id'    => $this->user_id,
                    'tgl_izin'      => $this->input->post('tgl_izin'),
                    'jenis_izin'    => $this->input->post('jenis_izin'),
                    'keterangan'    => $this->input->post('keterangan'),
                    'status_izin'   => 'PENDING',
                );

                if (!empty($_FILES['file']['name'])) {

                    $upload['upload_path']   = './uploads';
                    $upload['allowed_types'] = 'jpeg|jpg|pdf';
                    $upload['encrypt_name']  = true;
                    $upload['max_size']      = 1024;

                    $this->load->library('upload', $upload);

                    if (!$this->upload->do_upload('file')) {
                        $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah');
                        redirect(site_url('guru/pengajuan-izin'), 'reload');
                    } else {
                        $success   = $this->upload->data();
                        $file_name = $success['file_name'];

                        $data['file'] = $file_name;
                    }
                }

                $this->db->insert('izin_pegawai', $in);

                $this->alert->set('alert-success', 'Data permohonan izin berhasil diajukan');
                redirect(site_url('guru/pengajuan-izin'), 'reload');
            }
        }

        $this->db->select('a.tgl_pengajuan, a.tgl_izin,
                           a.jenis_izin, IFNULL(a.keterangan,"-") AS keterangan, a.`file`,a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');
        $this->db->join('sekolah c', 'b.sekolah_id = c.id', 'left');
        $this->db->order_by('a.tgl_izin DESC');
        $this->db->where('b.id', $this->user_id);
        $data['izin'] = $this->db->get('izin_pegawai a');

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Pengajuan Izin', $this->base_breadcrumbs . '/pengajuan-izin');

        $data['keterangan'] = 'Anda bisa mengajukan permohonan izin kepada kepala sekolah sekaligus melihat riwayat permohonan anda';
        $data['page_name']  = 'pengajuan_izin';
        $data['page_title'] = 'Pengajuan Izin';

        $this->_page_output($data);
    }

    public function kelola_izin_siswa($status_izin, $izin_id)
    {

        $izin_id = simple_crypt($izin_id, 'd');

        if ($status_izin === 'terima') {
            $this->db->where('id', $izin_id);
            $this->db->update('izin_siswa', array('status_izin' => 'TERIMA'));

            $cek = $this->db->get_where('izin_siswa', array('id' => $izin_id));

            if ($cek->num_rows() > 0) {
                $izin = $cek->row_array();

                $jenis_izin = $izin['jenis_izin'];
                $siswa_id   = $izin['siswa_id'];
                $tgl_izin   = $izin['tgl_izin'];

                $cek_kehadiran = $this->db->get_where(
                    'kehadiran_siswa',
                    array(
                        'siswa_id'  => $siswa_id,
                        'DATE(tgl)' => $tgl_izin,
                    )
                );

                $status_kehadiran = ($cek_kehadiran->num_rows() > 0) ? 'update' : 'insert';

                if ($jenis_izin === 'SAKIT') {

                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update('kehadiran_siswa', array('status' => 'SAKIT'));
                    } else {
                        $this->db->insert(
                            'kehadiran_siswa',
                            array(
                                'siswa_id' => $siswa_id,
                                'tgl'      => date('Y-m-d'),
                                'status'   => 'SAKIT',
                            )
                        );
                    }
                } elseif ($jenis_izin === 'LAINNYA') {
                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update('kehadiran_siswa', array('status' => 'IZIN'));
                    } else {
                        $this->db->insert(
                            'kehadiran_siswa',
                            array(
                                'siswa_id' => $siswa_id,
                                'tgl'      => date('Y-m-d'),
                                'status'   => 'IZIN',
                            )
                        );
                    }
                }
            }
        } else {
            $this->db->where('id', $izin_id);
            $this->db->update('izin_siswa', array('status_izin' => 'TOLAK'));
        }

        $this->alert->set('alert-success', 'Status permohonan izin telah diubah');
        redirect(site_url('guru/izin_siswa'), 'reload');
    }

    public function presensi()
    {

        $data = array();

        $keterangan = '<br/>Data riwayat presensi yang anda lakukan';

        if (!empty($_POST)) {
            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            // $bobot_normal = $cek_bobot['nilai'];
            $hari_aktif = _get_hari_aktif($tahun, $bulan);

            // $keterangan .= '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong>&nbsp(Nilai anda&nbsp;:&nbsp;<div style="display: inline" id="nilai_pegawai"></div>)';
            $keterangan .= '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;';

            // $this->db->select('a.fulldate AS tgl,
            //                    b.jam_masuk AS jam_masuk,
            //                    b.jam_pulang,
            //                    b.status_masuk,
            //                    b.status_pulang,
            //                    c.nilai AS nilai_presensi');
            // $this->db->join('kehadiran_pegawai b', 'a.fulldate = DATE(b.jam_masuk) AND  b.pegawai_id = ' . $this->user_id, 'left');
            // $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
            // $this->db->where('YEAR(a.fulldate)', $tahun);
            // $this->db->where('MONTH(a.fulldate)', $bulan);
            // $this->db->order_by('a.fulldate');

            // $data['presensi'] = $this->db->get('dates a');

            $this->db->select(
                "a.fulldate,
                IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS libur,
                IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_masuk,'-'))) AS jam_masuk,
                IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_pulang,'-'))) AS jam_pulang,
                IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status_masuk,'-',c.status_pulang),'-'))) AS status,
                IF(a.dayofweek = 7,0,IF(COUNT(b.id) > 0,0,IFNULL(d.nilai,0))) AS nilai"
            );

            $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
            $this->db->join("kehadiran_pegawai c", "a.fulldate = DATE(c.jam_masuk) AND c.pegawai_id = '$this->user_id'", "left");
            $this->db->join("bobot d", "c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang", "left");
            $this->db->where("YEAR(a.fulldate)", $tahun);
            $this->db->where("MONTH(a.fulldate)", $bulan);
            $this->db->group_by("a.fulldate,a.dayofweek,c.jam_masuk,c.jam_pulang,c.status_masuk,c.status_pulang");
            $data['presensi'] = $this->db->get("dates a");
        }

        $data['keterangan'] = $keterangan;
        $data['page_name']  = 'riwayat_presensi';
        $data['page_title'] = 'Riwayat Presensi';
        $this->_page_output($data);
    }

    public function izin_siswa()
    {
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Permohonan Izin', $this->base_breadcrumbs . '/permohonan_izin');

        $pegawai = $this->db->get_where('pegawai', array('id' => $this->user_id))->row_array();
        //echo 'test' . $pegawai['wali_kelas'];
        if (!is_null($pegawai['wali_kelas'])) {
            $wali_kelas = explode(',', $pegawai['wali_kelas']);

            $data = array();
            if (count($wali_kelas) > 0) {
                /*
                SELECT b.nama_lengkap,c.nama_kelas AS kelas
                FROM izin_siswa a
                LEFT JOIN siswa b ON a.siswa_id = b.id
                LEFT JOIN kelas c ON b.kelas_id = c.id
                WHERE b.kelas_id IN (1,2,3)
                 */

                $this->db->select('a.id AS izin_siswa_id,b.nama_lengkap,c.nama_kelas,a.jenis_izin,a.keterangan,a.file');
                $this->db->join('siswa b', 'a.siswa_id = b.id', 'left');
                $this->db->join('kelas c', 'b.kelas_id = c.id', 'left');
                $this->db->where_in('b.kelas_id', $wali_kelas);
                $this->db->where('status_izin', 'PENDING');

                $data['izin'] = $this->db->get('izin_siswa a');
            }
        }

        $data['keterangan'] = 'Data permohonan izin yang dilakukan oleh siswa dengan anda sebagai wali kelasnya.';
        $data['page_name']  = 'izin_siswa';
        $data['page_title'] = 'Izin Siswa';
        $this->_page_output($data);
    }

    public function hapus_siswa_kelas($siswa_id, $kelas_id)
    {

        $siswa_id = simple_crypt($siswa_id, 'd');
        $kelas_id = simple_crypt($kelas_id, 'd');

        $cek = $this->db->get_where('siswa', array('id' => $siswa_id));
        if ($cek->num_rows() > 0) {
            $this->db->where('id', $siswa_id);
            $this->db->update('siswa', array('kelas_id' => 0));
        }

        redirect(site_url('guru/kelola-siswa-kelas/' . $kelas_id));
    }

    public function kelola_siswa_kelas($kelas = null)
    {

        if (!empty($_POST)) {
            $kelas_id = $this->input->post('kelas_id');
            $siswa    = $this->input->post('siswa');

            foreach ($siswa as $value) {
                $this->db->where('id', $value);
                $this->db->update('siswa', array('kelas_id' => $kelas_id));
            }
        }

        $data = array();
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Data Kelas', $this->base_breadcrumbs . '/kelola-siswa-kelas');

        if ($kelas === null) {
            $pegawai = $this->db->get_where('pegawai', array('id' => $this->user_id))->row_array();

            $list_kelas = explode(',', $pegawai['wali_kelas'] ?? '');

            $this->db->where_in('id', $list_kelas);
            $data['list_kelas'] = $this->db->get('kelas');

            $data['page_name']  = 'siswa_kelas/list_kelas';
            $data['page_title'] = 'Kelas';
        } else {

            //cek apakah kelas ini memang di kelola oleh pegawai ini?
            $cek            = $this->db->get_where('pegawai', array('id' => $this->user_id))->row_array();
            $kelas_dikelola = explode(',', $cek['wali_kelas']);

            // $this->db->where_in('id',$kelas_dikelola);

            if (!in_array($kelas, $kelas_dikelola)) {
                redirect(site_url('guru/kelola-siswa-kelas'), 'reload');
            }

            $this->breadcrumbs->push('Data Siswa Kelas', $this->base_breadcrumbs . '/kelola-siswa-kelas/' . $kelas);

            $data['kelas_id']   = $kelas;
            $data['sekolah_id'] = $this->sekolah_id;

            $cek = $this->db->get_where('kelas', array('id' => $kelas));

            if ($cek->num_rows() > 0) {
                $db_kelas = $cek->row_array();
                // $data['nama_kelas'] =
                $data['page_title'] = 'Siswa Kelas ' . $db_kelas['nama_kelas'];
            } else {
                redirect(site_url('guru'), 'reload');
            }

            $this->db->order_by('nama_lengkap', 'ASC');
            $data['list_siswa'] = $this->db->get_where('siswa', array('kelas_id' => $kelas));
            $data['page_name']  = 'siswa_kelas/list_siswa';
        }

        $this->_page_output($data);
    }

    public function profile()
    {

        // try {
        //     $this->load->library(array('grocery_CRUD'));
        //     $crud = new Grocery_CRUD();

        //     $crud->set_table('pegawai');
        //     $crud->set_subject('Profile');

        //     $crud->required_fields('nama_lengkap');
        //     $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');

        //     $crud->field_type('jk', 'dropdown', array('L' => 'Laki-laki', 'P' => 'Perempuan'));

        //     $crud->set_relation('sekolah_id', 'sekolah', 'nama');
        //     $crud->display_as('sekolah_id', 'Unit Kerja');
        //     // $crud->field_type('alamat', 'textarea');

        //     $readonly_fields = [
        //         'sekolah_id',
        //         'terakhir_login',
        //         'nik',
        //         'jabatan',
        //         'status_pegawai',
        //         'agama',
        //     ];

        //     // Set each field as hidden
        //     foreach ($readonly_fields as $field) {
        //         $crud->field_type($field, 'readonly');
        //     }

        //     $hidden_fields = [
        //         'id',
        //         'password',
        //         'token_id',
        //         'token_login',
        //         'dev_unique_id',
        //         'aktif',
        //         'tgl_update',
        //         'dinas_pangkat',
        //         'dinas_unit_kerja',
        //         'dinas_provinsi',
        //         'dinas_kabupaten',
        //         'kdstapeg',
        //         'kdpangkat',
        //         'gapok',
        //         'mkgolt',
        //         'blgolt',
        //         'prsngapok',
        //         'kdeselon',
        //         'tjeselon',
        //         'kdfungsi',
        //         'tjfungsi',
        //         'tmtgaji',
        //         'tmtkontrak',
        //         'tatkontrak',
        //         'tmt_gaji',
        //         'tmt_pengangkatan',
        //         'sk_pengangkatan',
        //         'riwayat_sertifikasi_bidang_studi',
        //         'riwayat_sertifikasi_jenis_sertifikasi',
        //         'riwayat_sertifikasi_tahun_sertifikasi',
        //         'riwayat_sertifikasi_nomor_sertifikat',
        //         'riwayat_sertifikasi_nrg',
        //         'riwayat_sertifikasi_nomor_peserta',
        //         'riwayat_pendidikan_formal_bidang_studi',
        //         'riwayat_pendidikan_formal_jenjang_pendidikan',
        //         'riwayat_pendidikan_formal_gelar_akademik',
        //         'riwayat_pendidikan_formal_satuan_pendidikan_formal',
        //         'riwayat_pendidikan_formal_fakultas',
        //         'riwayat_pendidikan_formal_kependidikan',
        //         'riwayat_pendidikan_formal_tahun_masuk',
        //         'riwayat_pendidikan_formal_tahun_lulus',
        //         'riwayat_pendidikan_formal_nim',
        //         'riwayat_pendidikan_formal_status_kuliah',
        //         'riwayat_pendidikan_formal_semester',
        //         'riwayat_pendidikan_formal_ipk',
        //         'last_pass_reset',
        //         'wali_kelas',
        //         'terakhir_login',
        //         'jabatan',
        //         'status_pegawai',
        //         'agama'
        //     ];

        //     // Set each field as hidden
        //     foreach ($hidden_fields as $field) {
        //         $crud->field_type($field, 'hidden');
        //     }

        //     // Array for display_as
        //     $display_as_fields = [
        //         'nuptk'  => 'NUPTK',
        //         'nip'    => 'NIP',
        //         'nik'    => 'NIK',
        //         'jk'     => 'Jenis kelamin',
        //         'no_rek' => 'Nomor Rekening',
        //         'kode_skpd' => 'Kode SKPD',
        //         'kode_satker' => 'Kode Satker',
        //         'tgl_lahir' => 'Tanggal Lahir',
        //         'npwp' => 'NPWP'
        //     ];

        //     // Set display_as for each field
        //     foreach ($display_as_fields as $field => $display_name) {
        //         $crud->display_as($field, $display_name);
        //     }

        //     $crud->callback_edit_field('wali_kelas', function ($value, $primary_key) {
        //         $exp = explode(',', $value ?? '');
        //         $this->db->where_in('id', $exp);
        //         $kelas = $this->db->get('kelas');

        //         $kelas_wali = "";
        //         foreach ($kelas->result_array() as $key) {
        //             $kelas_wali .= $key['nama_kelas'] . " ,";
        //         }

        //         $kelas_wali = substr($kelas_wali, 0, -1);

        //         return '<div id="field-wali_kelas" class="readonly_label">' . $kelas_wali . '</div>';
        //     });

        //     $crud->set_field_upload('foto', 'uploads');

        //     $crud->callback_after_insert(function ($post_array, $primary_key) {
        //     });

        //     $crud->callback_after_update(function ($post_array, $primary_key) {

        //         $this->db->where('id', $primary_key);
        //         $this->db->update(
        //             'user',
        //             array(
        //                 'tgl_update' => date('Y-m-d H:i:s'),
        //             )
        //         );
        //     });

        //     $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        //     $this->breadcrumbs->push('Profil', $this->base_breadcrumbs . '/profile');

        //     $state = $crud->getState();
        //     if ($state === 'insert_validation') {
        //         // $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
        //         // $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
        //     } elseif ($state === 'update_validation') {
        //         $crud->set_rules('email', 'Email', 'valid_email');
        //     } elseif ($state === 'add') {
        //         // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
        //     } elseif ($state === 'edit') {

        //         $curr_user_id = $this->uri->segment(4);

        //         if ($curr_user_id != $this->user_id) {
        //             redirect(site_url('guru/profile/edit/' . $this->user_id), 'reload');
        //         }

        //         $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
        //     } elseif ($state === 'list') {
        //         redirect(site_url('guru/profile/edit/' . $this->user_id), 'reload');
        //     }

        //     $crud->unset_back_to_list();
        //     $extra = array('page_title' => 'Kelola Profil');

        //     $crud->set_relation('kode_skpd', 'skpd', 'nama');
        //     $crud->set_relation('kode_satker', 'satker', 'nama');

        //     $this->load->library('Gc_Dependent_Select');

        //     $fields = array(
        //         'kode_skpd'   => array(
        //             'table_name'       => 'skpd',
        //             'title'            => 'nama', // now you can use this format )))
        //             'id_field' => 'kode',
        //             'relate'           => null,
        //             'data-placeholder' => 'Pilih SKPD',
        //         ),
        //         'kode_satker' => array(
        //             'table_name'       => 'satker',
        //             'title'            => 'nama', // now you can use this format )))
        //             'id_field'         => 'kode',
        //             'relate'           => 'kode_skpd',
        //             'data-placeholder' => 'Pilih Satker',
        //         ),
        //     );

        //     $uri_segment_kode = $this->uri->segment(4);
        //     $config = array(
        //         'main_table'         => 'pegawai',
        //         'main_table_primary' => 'id',
        //         "url"                => site_url('/guru/profile/edit/' . $uri_segment_kode . '/'),
        //         'ajax_loader'        => base_url() . 'assets/ajax-loader.gif',
        //     );

        //     $satker = new Gc_dependent_select($crud, $fields, $config);
        //     $js     = $satker->get_js();

        //     $output = $crud->render();
        //     $output->output .= $js;

        //     $output = array_merge((array) $output, $extra);

        //     $this->_page_output($output);
        // } catch (Exception $e) {
        //     show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        // }

        try {
            // $crud = $this->grocerycrudconfig->getCrud();

            $crud = Modules::run("profile/index");

            $base_breadcrumbs = 'guru/profile';

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // Custom validation rules for insert
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'edit') {
                $curr_user_id = $this->uri->segment(4);
                if ($curr_user_id != $this->user_id) {
                    redirect(site_url($base_breadcrumbs . '/edit/' . $this->user_id), 'reload');
                }
                $this->breadcrumbs->push('Ubah', $base_breadcrumbs . '/edit');
            } elseif ($state === 'list') {
                redirect(site_url($base_breadcrumbs . '/edit/' . $this->user_id), 'reload');
            }

            $extra = array('page_title' => 'Kelola Profil');
            $output = $crud->render();
            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }


    public function dokumen()
    {
        $user_id          = $this->session->userdata('user_id');
        
        
        $data['dokumen'] = $this->doc_m->get_document($user_id);
        
        $data['page_title'] = 'Dokumen';
        $data['page_name']  = 'dokumen';
        $this->_page_output($data);
    }


    public function upload_dokumen()
    {

        if (!empty($_FILES['file_dokumen']['name'])) {

            $jenis_dokumen_id = $this->input->post('jenis_dokumen_id');
            $user_id          = $this->session->userdata('user_id');

            $upload['upload_path']   = './uploads/dokumen';
            $upload['allowed_types'] = 'zip|rar|jpeg|jpg|png|bmp|pdf|doc|docx|xls|xlsx';
            $upload['encrypt_name']  = false;
            $upload['file_name']     = generate_doc_name($user_id, $jenis_dokumen_id);
            $upload['overwrite']     = true;
            $upload['max_size']      = 1024;

            $this->load->library('upload', $upload);

            if (!$this->upload->do_upload('file_dokumen')) {
                // $data['msg'] = $this->upload->display_errors();
                $this->alert->set('alert-danger', "Ada kesalahan! Periksa kembali file yang anda unggah" . '<br>' . "Pastikan file yang anda unggah memiliki format yang diijinkan dan besarnya tidak lebih dari 1 MB");
                redirect(site_url('guru/dokumen'), 'reload');
            } else {
                $success   = $this->upload->data();
                $file_name = $success['file_name'];

                $set = array(
                    'pegawai_id'     => $user_id,
                    'jenis_dokumen_id' => $jenis_dokumen_id,
                    'file_dokumen'     => $file_name,
                    'verifikasi'       => 'pending',
                );
                $exclude_columns = array();
                $this->db->on_duplicate('dokumen_pegawai', $set, $exclude_columns);

                $this->alert->set('alert-success', 'File dokumen berhasil diunggah');
                redirect(site_url('guru/dokumen'), 'reload');
            }
        }
    }



    public function jadwal_mengajar()
    {
        //
        $this->db->query("
            INSERT INTO guru_mengajar(tgl,jadwal_mengajar_id)
            SELECT CURDATE(),id
            FROM jadwal_mengajar
            WHERE pegawai_id = '$this->user_id' AND
                hari = (
                CASE
                     WHEN DAYOFWEEK(CURDATE()) = 1 THEN 'MINGGU'
                     WHEN DAYOFWEEK(CURDATE()) = 2 THEN 'SENIN'
                     WHEN DAYOFWEEK(CURDATE()) = 3 THEN 'SELASA'
                     WHEN DAYOFWEEK(CURDATE()) = 4 THEN 'RABU'
                     WHEN DAYOFWEEK(CURDATE()) = 5 THEN 'KAMIS'
                     WHEN DAYOFWEEK(CURDATE()) = 6 THEN 'JUMAT'
                     WHEN DAYOFWEEK(CURDATE()) = 7 THEN 'SABTU'
                    END)
            ON DUPLICATE KEY UPDATE tgl_update = NOW()
        ");

        $data['jadwal_mengajar'] = get_jadwal_mengajar($this->user_id, 'only-this-week');

        $keterangan = '<br/>* Jadwal yang ditampilkan hanya pada minggu yang berjalan';

        $data['keterangan'] = $keterangan;

        $data['pegawai_id'] = $this->user_id;
        $data['page_name']  = 'jadwal_mengajar';
        $data['page_title'] = 'Jadwal mengajar minggu ini';
        $this->_page_output($data);
    }

    public function ganti_password()
    {
        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('pegawai', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('guru/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('guru/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('guru/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('guru/ganti-password'), 'reload');
                }
            } else {
            }
        }

        $data['page_name'] = 'ganti_password';

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Ganti Password', $this->base_breadcrumbs . '/ganti_password');

        $data['page_title'] = 'Ganti Password';

        $this->_page_output($data);
    }

    public function generate_nama_dokumen($jadwal_mengajar_id)
    {
        $this->db->select('a.pegawai_id,a.hari,b.nama_kelas,c.nama AS mata_pelajaran');
        $this->db->from('jadwal_mengajar a');
        $this->db->join('kelas b', 'a.kelas_id = b.id');
        $this->db->join('matapelajaran c', 'a.matapelajaran_id = c.id');
        $this->db->where('a.id', $jadwal_mengajar_id);
        $row = $this->db->get();

        $nameLst = $row->row_array();
        $tgl     = date('Y-m-d');

        return slugify($nameLst['pegawai_id'] . '_' . $tgl . '_' . strtolower($nameLst['hari']) . '_' . $nameLst['nama_kelas'] . '_' . $nameLst['mata_pelajaran']);
    }

    public function upload_dokumentasi()
    {
        // $tgl                = date('Y-m-d');
        $jadwal_mengajar_id = $this->input->post('jadwal_mengajar_id');
        $tgl                = $this->input->post('tgl');

        $file_types = array(
            'tgl'          => $tgl,
            'dokumentasi'  => 'doc|docx|xls|xlsx',
            'foto_mulai'   => 'jpeg|jpg|png|bmp',
            'foto_selesai' => 'jpeg|jpg|png|bmp',
        );
        foreach ($file_types as $file_type => $allowed_types) {
            if (!empty($_FILES[$file_type]['name'])) {
                $upload['upload_path']   = './uploads/dokumentasi';
                $upload['allowed_types'] = $allowed_types;
                $upload['encrypt_name']  = false;
                $upload['file_name']     = $this->generate_nama_dokumen($jadwal_mengajar_id);
                $upload['overwrite']     = true;
                $upload['max_size']      = 2048; // Increased the file size limit to 2MB
                $this->load->library('upload', $upload);

                if (!$this->upload->do_upload($file_type)) {
                    $this->alert->set('alert-danger', "Ada kesalahan! Periksa kembali file yang anda unggah" . '<br>' . "Pastikan file yang anda unggah memiliki format yang diijinkan dan besarnya tidak lebih dari 2 MB");

                    redirect(site_url('guru/jadwal-mengajar'), 'reload');
                } else {
                    $success   = $this->upload->data();
                    $file_name = $success['file_name'];
                    $set       = array(
                        'tgl'                => $tgl,
                        'jadwal_mengajar_id' => $jadwal_mengajar_id,
                        'verifikasi'         => 'pending',
                        $file_type           => $file_name,
                        'tgl_update'         => date("Y-m-d H:i:s"),
                    );
                    $exclude_columns = array();
                    $this->db->on_duplicate('guru_mengajar', $set, $exclude_columns);

                    $this->alert->set('alert-success', 'File dokumen berhasil diunggah');
                    redirect(site_url('guru/jadwal-mengajar'), 'reload');
                }
            } else {
                // Jika file tidak diunggah, cek apakah ada teks di textarea
                $uraian = $this->input->post('uraian'); // Ambil teks dari textarea

                $set = array(
                    'tgl'                => $tgl,
                    'jadwal_mengajar_id' => $jadwal_mengajar_id,
                    'uraian'             => $uraian,
                    'verifikasi'         => 'pending',
                    'tgl_update'         => date("Y-m-d H:i:s"),
                );

                $exclude_columns = array();
                $this->db->on_duplicate('guru_mengajar', $set, $exclude_columns);
            }
        }
    }

    // public function upload_dokumentasi()
    // {

    //     $tgl                = date('Y-m-d');
    //     $jadwal_mengajar_id = $this->input->post('jadwal_mengajar_id');

    //     if (!empty($_FILES['dokumentasi']['name'])) {

    //         $upload['upload_path']   = './uploads/dokumentasi';
    //         $upload['allowed_types'] = 'doc|docx|xls|xlsx';
    //         $upload['encrypt_name']  = false;
    //         $upload['file_name']     = $this->generate_nama_dokumen($jadwal_mengajar_id);
    //         $upload['overwrite']     = true;
    //         $upload['max_size']      = 1024;

    //         $this->load->library('upload', $upload);

    //         if (!$this->upload->do_upload('dokumentasi')) {

    //             $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah\n\rPastikan file yang anda unggah memiliki format yang diijinkan dan besarnya tidak lebih dari 1 MB');
    //             redirect(site_url('guru/jadwal-mengajar'), 'reload');
    //         } else {
    //             $success   = $this->upload->data();
    //             $file_name = $success['file_name'];

    //             $set = array(
    //                 'tgl'                => $tgl,
    //                 'jadwal_mengajar_id' => $jadwal_mengajar_id,
    //                 'file_dokumentasi'   => $file_name,
    //             );

    //             $exclude_columns = array();
    //             $this->db->on_duplicate('guru_mengajar', $set, $exclude_columns);
    //         }
    //     }

    //     if (!empty($_FILES['foto']['name'])) {
    //         $upload['upload_path']   = './uploads/dokumentasi';
    //         $upload['allowed_types'] = 'jpeg|jpg|png|bmp';
    //         $upload['encrypt_name']  = false;
    //         $upload['file_name']     = $this->generate_nama_dokumen($jadwal_mengajar_id);
    //         $upload['overwrite']     = true;
    //         $upload['max_size']      = 1024;

    //         $this->load->library('upload', $upload);

    //         if (!$this->upload->do_upload('foto')) {

    //             $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah\n\rPastikan file yang anda unggah memiliki format yang diijinkan dan besarnya tidak lebih dari 1 MB');
    //             redirect(site_url('guru/jadwal-mengajar'), 'reload');
    //         } else {
    //             $success   = $this->upload->data();
    //             $file_name = $success['file_name'];

    //             $set = array(
    //                 'tgl'                => $tgl,
    //                 'jadwal_mengajar_id' => $jadwal_mengajar_id,
    //                 'foto'   => $file_name,
    //             );

    //             $exclude_columns = array();
    //             $this->db->on_duplicate('guru_mengajar', $set, $exclude_columns);
    //         }
    //     }
    // }
}
