<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Operator extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $sekolah_id;
    private $logo;
    private $npsn;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert', 'api'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));

        $this->breadcrumbs->load_config('default');

        $level                  = $this->session->userdata('user_level');
        $this->sekolah_id       = $this->session->userdata('user_id');
        $this->npsn             = $this->session->userdata('user_npsn');
        $this->user_id          = $this->session->userdata('user_id');
        $this->logo             = $this->session->userdata('user_logo');
        $this->base_breadcrumbs = '/operator';

        if ($level !== 'OPERATOR') {
            redirect(site_url('signout'), 'reload');
        }

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    public function _page_output($output = null)
    {
        $output['sekolah_id']   = $this->sekolah_id;
        $output['nama_lengkap'] = $this->session->userdata('user_nama');
        $this->load->view('master_page.php', (array) $output);
    }

    public function index()
    {
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);

        $data['page_name']  = 'beranda';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function import_pegawai()
    {

        if (!empty($_POST)) {

            $config['upload_path']   = './uploads';
            $config['allowed_types'] = 'xls|xlsx';
            $config['encrypt_name']  = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {

                $this->alert->set('alert-danger', '<p class="text-danger">' . $this->upload->display_errors() . '</p>', true);
            } else {

                $extension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);

                if ($extension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                } else {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                }

                // file path
                $spreadsheet    = $reader->load($_FILES['userfile']['tmp_name']);
                $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $arrayCount = count($allDataInSheet);

                $j            = 0;
                $new_rows     = 0;
                $updated_rows = 0;

                // //baris data dimulai dari baris ke 2
                for ($i = 2; $i <= $arrayCount; ++$i) {

                    $nama_lengkap   = strtoupper(custom_trim($allDataInSheet[$i]["A"]));
                    $nuptk          = custom_trim($allDataInSheet[$i]["B"]);
                    $jabatan        = custom_trim($allDataInSheet[$i]["C"]);
                    $status_pegawai = strtoupper(custom_trim($allDataInSheet[$i]["D"]));
                    $jk             = strtoupper(custom_trim($allDataInSheet[$i]["E"]));
                    $alamat         = custom_trim($allDataInSheet[$i]["F"]);
                    $email          = custom_trim($allDataInSheet[$i]["G"]);
                    $telp           = custom_trim($allDataInSheet[$i]["H"]);

                    if ($nuptk === "") {
                        continue;
                    }

                    $r = $this->db->get_where('pegawai', array('nuptk' => $nuptk));

                    if ($r->num_rows() == 0) {
                        //hanya data baru aja yang dimasukkin
                        $in = array(
                            'sekolah_id'     => $this->sekolah_id,
                            'nuptk'          => $nuptk,
                            'password'       => password_hash($nuptk, PASSWORD_DEFAULT),
                            'jabatan'        => $jabatan,
                            'status_pegawai' => $status_pegawai,
                            'nama_lengkap'   => $nama_lengkap,
                            'jk'             => $jk,
                            'alamat'         => $alamat,
                            'email'          => $email,
                            'telp'           => $telp,
                        );

                        $this->db->insert('pegawai', $in);
                        ++$new_rows;
                    } else {
                        //lets update
                        $up = array(
                            'jabatan'        => $jabatan,
                            'status_pegawai' => $status_pegawai,
                            'nama_lengkap'   => $nama_lengkap,
                            'jk'             => $jk,
                            'alamat'         => $alamat,
                            'email'          => $email,
                            'telp'           => $telp,
                        );

                        $this->db->where('nuptk', $nuptk);
                        $this->db->update('pegawai', $up);

                        ++$updated_rows;
                    }

                    ++$j;
                }

                $success   = $this->upload->data();
                $file_name = $success['file_name'];

                @unlink('./uploads/' . $file_name);
                $this->alert->set('alert-success', 'Data berhasil dimasukkan dengan ' . $new_rows . ' Data baru ' . 'dan ' . $updated_rows . ' Data Lama / Data Update', true);
            }
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-pegawai');
        $this->breadcrumbs->push('Import Data', $this->base_breadcrumbs . '/import-pegawai');

        $data['page_name']  = 'import_pegawai';
        $data['page_title'] = 'Import Data Pegawai';
        $this->_page_output($data);
    }

    public function import_siswa()
    {

        if (!empty($_POST)) {

            $config['upload_path']   = './uploads';
            $config['allowed_types'] = 'xls|xlsx';
            $config['encrypt_name']  = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {

                $this->alert->set('alert-danger', '<p class="text-danger">' . $this->upload->display_errors() . '</p>', true);
            } else {
                $extension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);

                if ($extension == 'xlsx') {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                } else {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                }

                // file path
                $spreadsheet    = $reader->load($_FILES['userfile']['tmp_name']);
                $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $arrayCount = count($allDataInSheet);

                $j            = 0;
                $new_rows     = 0;
                $updated_rows = 0;

                // //baris data dimulai dari baris ke 2
                for ($i = 2; $i <= $arrayCount; ++$i) {

                    $nama_lengkap = strtoupper(custom_trim($allDataInSheet[$i]["A"]));
                    $nisn         = custom_trim($allDataInSheet[$i]["B"]);
                    $jk           = strtoupper(custom_trim($allDataInSheet[$i]["C"]));
                    $alamat       = custom_trim($allDataInSheet[$i]["D"]);
                    $email        = custom_trim($allDataInSheet[$i]["E"]);
                    $telp         = custom_trim($allDataInSheet[$i]["F"]);

                    if ($nisn === "") {
                        continue;
                    }

                    $r = $this->db->get_where('siswa', array('nisn' => $nisn));

                    if ($r->num_rows() == 0) {
                        //hanya data baru aja yang dimasukkin
                        $in = array(
                            'sekolah_id'   => $this->sekolah_id,
                            'nisn'         => $nisn,
                            'password'     => password_hash($nisn, PASSWORD_DEFAULT),
                            'nama_lengkap' => $nama_lengkap,
                            'jk'           => $jk,
                            'alamat'       => $alamat,
                            'email'        => $email,
                            'telp'         => $telp,
                        );

                        $this->db->insert('siswa', $in);
                        ++$new_rows;
                    } else {
                        //lets update
                        $up = array(
                            'nama_lengkap' => $nama_lengkap,
                            'jk'           => $jk,
                            'alamat'       => $alamat,
                            'email'        => $email,
                            'telp'         => $telp,
                        );

                        $this->db->where('nisn', $nisn);
                        $this->db->update('siswa', $up);

                        ++$updated_rows;
                    }

                    ++$j;
                }

                $success   = $this->upload->data();
                $file_name = $success['file_name'];

                @unlink('./uploads/' . $file_name);
                $this->alert->set('alert-success', 'Data berhasil dimasukkan dengan ' . $new_rows . ' Data baru ' . 'dan ' . $updated_rows . ' Data Lama / Data Update', true);
            }
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Kelola Siswa', $this->base_breadcrumbs . '/kelola-siswa');
        $this->breadcrumbs->push('Import Data', $this->base_breadcrumbs . '/import-siswa');

        $data['page_name']  = 'import_siswa';
        $data['page_title'] = 'Import Data Siswa';
        $this->_page_output($data);
    }

    public function reset_token_pegawai()
    {
        $pegawai_id = $this->uri->segment(3);

        $this->db->set('token_login', 'NULL', false);
        $this->db->where('id', $pegawai_id);
        $this->db->update('pegawai');

        $this->alert->set('alert-success', 'Token berhasil direset');

        redirect(site_url('operator/kelola-pegawai'), 'reload');
    }

    public function reset_password_pegawai($pegawai_id)
    {

        $cek = $this->db->get_where('pegawai', array('id' => $pegawai_id))->row_array();

        $nuptk_pass = password_hash($cek['nuptk'], PASSWORD_DEFAULT);

        $this->db->set('password', "'" . $nuptk_pass . "'", false);
        $this->db->where('id', $pegawai_id);
        $this->db->update('pegawai');

        $this->alert->set('alert-success', 'Password berhasil direset dan diganti menjadi NUPTK');

        redirect(site_url('operator/kelola-pegawai'), 'reload');
    }

    public function kelola_pegawai()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Pegawai');
            $crud->where('sekolah_id', $this->sekolah_id);
            $crud->order_by('nama_lengkap', 'ASC');

            $crud->required_fields('jabatan', 'status_pegawai', 'nuptk', 'nama_lengkap', 'jk');

            $crud->columns('nuptk', 'nama_lengkap', 'jabatan', 'status_pegawai', 'mengajar');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('id', 'hidden');
            $crud->field_type('nuptk', 'readonly');
            $crud->field_type('nip', 'readonly');

            $crud->field_type('nama_lengkap', 'readonly');
            $crud->field_type('tgl_lahir', 'readonly');
            $crud->field_type('tempat_lahir', 'readonly');
            $crud->field_type('nik', 'readonly');
            $crud->field_type('jk', 'readonly');
            $crud->field_type('agama', 'readonly');
            $crud->field_type('sk_pengangkatan', 'readonly');
            $crud->field_type('tmt_pengangkatan', 'readonly');
            $crud->field_type('jabatan', 'readonly');
            $crud->field_type('status_pegawai', 'readonly');
            $crud->field_type('alamat', 'readonly');
            $crud->field_type('email', 'readonly');
            $crud->field_type('telp', 'readonly');
            $crud->field_type('aktif', 'readonly');

            $crud->field_type('password', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('wali_kelas', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');

            // karena pegawai sekolah, maka semua diset 0
            $crud->field_type('dinas_provinsi', 'hidden', 0);
            $crud->field_type('dinas_kabupaten', 'hidden', 0);
            $crud->field_type('dinas_pangkat', 'hidden');
            $crud->field_type('dinas_unit_kerja', 'hidden');

            $crud->display_as('nuptk', 'NUPTK');
            $crud->display_as('status_pegawai', 'Status');
            $crud->display_as('nama_lengkap', 'Nama');

            // $crud->field_type('jabatan', 'enum', array('GURU', 'TU', 'KEPALA', 'SATPAM', 'KEBERSIHAN'));

            $crud->callback_after_insert(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'pegawai',
                    array(
                        'password'   => password_hash($post_array['nuptk'], PASSWORD_DEFAULT),
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'pegawai',
                    array(
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $crud->callback_column('jabatan', function ($value, $row) {
                // return '<a href="' . site_url('dinas/rekap-kehadiran/' . simple_crypt($row->id, 'e')) . '">Lihat</a>';

                // if($value === 'GURU'){
                //     return $value . '  <a href="' . site_url('operator/jadwal-mengajar/' . $row->id) . '">JADWAL MENGAJAR</a>';
                // }else{
                //     return $value;
                // }

                return $value;
            });

            $crud->callback_column('mengajar', function ($value, $row) {
                // return '<a href="' . site_url('dinas/rekap-kehadiran/' . simple_crypt($row->id, 'e')) . '">Lihat</a>';

                if ($row->jabatan === 'Guru Mapel') {
                    return '<a href="' . site_url('operator/jadwal-mengajar/' . $row->id) . '">JADWAL</a>';
                } else {
                    return '-';
                }
            });

            $crud->callback_column('token', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset token untuk pegawai ini?\');" class="text-danger" href="' . site_url('operator/reset-token-pegawai/' . $row->id) . '">RESET</a>';
            });

            $crud->callback_column('password', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset password untuk pegawai ini?\');" class="text-danger" href="' . site_url('operator/reset-password-pegawai/' . $row->id) . '">RESET</a>';
            });

            $crud->set_field_upload('foto', 'uploads');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-pegawai');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('nuptk', 'NUPTK', 'is_unique[pegawai.nuptk]|required');
                $crud->set_rules('email', 'Email', 'is_unique[pegawai.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-pegawai/add');
            } elseif ($state === 'edit') {

                $primary_key = $this->uri->segment(4);
                //cek
                $cek = $this->db->get_where('pegawai', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

                if ($cek->num_rows() == 0) {
                    redirect(site_url('operator/kelola-pegawai'), 'reload');
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-pegawai/edit');
            }

            $extra      = array('page_title' => 'Kelola Pegawai');
            $keterangan = '<br/>* <a onclick="Loader.open()"  class="text-danger" href="' . site_url('operator/perbarui-data-pegawai') . '">Klik disini</a>&nbsp;untuk memperbarui data pegawai';
           

            $extra['keterangan'] = $keterangan;

            $crud->unset_clone();
            // $crud->unset_read();
            $crud->unset_add();
            // $crud->unset_edit();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function get_mp_guru($pegawai_id)
    {

        $this->db->select('a.matapelajaran_id AS id,b.nama AS nama');
        $this->db->join('matapelajaran b', 'a.matapelajaran_id = b.id', 'left');
        return $this->db->get_where('guru_mapel a', array('pegawai_id' => $pegawai_id));
    }

    public function get_kelas_mapel()
    {

        $pegawai_id    = $this->input->get('pegawai_id');
        $matapelajaran = $this->input->get('matapelajaran');

        $gm  = $this->db->get_where('guru_mapel', array('pegawai_id' => $pegawai_id, 'matapelajaran_id' => $matapelajaran))->row_array();
        $exp = explode(',', $gm['kelas_id']);

        $this->db->where_in('id', $exp);
        $kelas = $this->db->get('kelas');

        $option = '<option value="">Pilih kelas</option>';
        foreach ($kelas->result_array() as $key) {
            $option .= '<option value="' . $key['id'] . '">' . $key['nama_kelas'] . '</option>';
        }

        echo $option;
    }

    public function hapus_jadwal($pegawai_id, $jadwal_id)
    {
        $this->db->where('id', $jadwal_id);
        $this->db->delete('jadwal_mengajar');

        $this->alert->set('alert-success', 'Jadwal mengajar berhasil dihapus');

        redirect(site_url('operator/jadwal_mengajar/' . $pegawai_id), 'reload');
    }

    public function jadwal_mengajar()
    {

        $pegawai_id = $this->uri->segment(3);

        if (!empty($_POST)) {

            $hari             = $this->input->post('hari');
            $jam_mulai        = $this->input->post('jam_mulai');
            $jam_selesai      = $this->input->post('jam_selesai');
            $kelas_id         = $this->input->post('kelas_id');
            $matapelajaran_id = $this->input->post('matapelajaran_id');
            $pegawai_id       = $this->input->post('pegawai_id');

            $this->db->insert(
                'jadwal_mengajar',
                array(
                    'hari'             => $hari,
                    'jam_mulai'        => $jam_mulai,
                    'jam_selesai'      => $jam_selesai,
                    'kelas_id'         => $kelas_id,
                    'matapelajaran_id' => $matapelajaran_id,
                    'pegawai_id'       => $pegawai_id,
                )
            );

            redirect(site_url('operator/jadwal_mengajar/' . $pegawai_id), 'reload');
            // $this->alert->set('alert-success', 'Jadwal mengajar berhasil disimpan ', false);

        }

        if ($pegawai_id == 0) {
            $this->alert->set('alert-warning', 'Pegawai id tidak valid! ');
            redirect(site_url('operator'), 'reload');
        }

        $cek = $this->db->get_where('pegawai', array('id' => $pegawai_id, 'jabatan' => 'Guru Mapel'));
        if ($cek->num_rows() == 0) {
            $this->alert->set('alert-warning', 'Pegawai id tidek valid! / Bukan Guru ');
            redirect(site_url('operator'), 'reload');
        }

        $data['pegawai_id']      = $pegawai_id;
        $data['mp_tersedia']     = $this->get_mp_guru($pegawai_id);
        $data['jadwal_mengajar'] = get_jadwal_mengajar($pegawai_id, 'show-all');
        $data['keterangan']      = 'Data matapelajaran dan kelas yang tersedia untuk pegawai ini berdasarkan dari penunjukan oleh kepala sekolah';
        $data['page_name']       = 'jadwal_mengajar';
        $data['page_title']      = 'Kelola jadwal Mengajar';
        $this->_page_output($data);
    }

    public function reset_password_siswa($siswa_id)
    {

        $cek = $this->db->get_where('siswa', array('id' => $siswa_id))->row_array();

        $nisn_pass = password_hash($cek['nisn'], PASSWORD_DEFAULT);

        $this->db->set('password', "'" . $nisn_pass . "'", false);
        $this->db->where('id', $siswa_id);
        $this->db->update('siswa');

        $this->alert->set('alert-success', 'Password berhasil direset dan diganti menjadi NISN');

        redirect(site_url('operator/kelola-siswa'), 'reload');
    }

    public function kelola_siswa()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa');
            $crud->set_subject('Siswa');
            $crud->where('siswa.sekolah_id', $this->sekolah_id);
            $crud->where('tanggal_keluar', null);
            $crud->order_by('kelas_id', 'ASC');

            $crud->required_fields('nisn', 'email', 'nama_lengkap', 'jk');

            $crud->columns('nama_lengkap', 'nisn', 'nama_rombel', 'password');

            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('kelas_id', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('perguruan_tinggi', 'readonly');
            $crud->field_type('jenis_keluar', 'readonly');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('tanggal_keluar', 'readonly');
            $crud->display_as('nisn', 'NISN');
            $crud->display_as('jk', 'Jenis Kelamin');

            $crud->field_type('id', 'hidden');
            $crud->field_type('nisn', 'readonly', );
            $crud->field_type('nik', 'readonly', );
            $crud->field_type('nama_lengkap', 'readonly', );
            $crud->field_type('jk', 'readonly', );
            $crud->field_type('agama', 'readonly', );
            $crud->field_type('email', 'readonly', );
            $crud->field_type('telp', 'readonly', );

            $crud->field_type('provinsi', 'hidden', );
            $crud->field_type('kabupaten', 'hidden', );
            $crud->field_type('kecamatan', 'hidden', );
            $crud->field_type('kelurahan', 'hidden', );
            $crud->field_type('alamat', 'readonly', );
            $crud->field_type('tgl_masuk_sekolah', 'readonly', );
            $crud->field_type('aktif', 'readonly', );
            

            // $crud->set_relation('kelas_id', 'kelas', 'nama_kelas');

            // $crud->display_as('kelas_id', 'Kelas');

            $crud->callback_after_insert(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'siswa',
                    array(
                        'password'   => password_hash($post_array['nisn'], PASSWORD_DEFAULT),
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'siswa',
                    array(
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $crud->callback_column('password', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset password untuk operator ini?\');" class="text-danger" href="' . site_url('operator/reset-password-siswa/' . $row->id) . '">RESET</a>';
            });

            $crud->set_field_upload('foto', 'uploads');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola siswa', $this->base_breadcrumbs . '/kelola-siswa');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('nisn', 'NISN', 'is_unique[siswa.nuptk]|required');
                $crud->set_rules('email', 'Email', 'is_unique[siswa.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-siswa/add');
            } elseif ($state === 'edit') {

                $primary_key = $this->uri->segment(4);
                //cek
                $cek = $this->db->get_where('siswa', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

                if ($cek->num_rows() == 0) {
                    redirect(site_url('operator/kelola-siswa'), 'reload');
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-siswa/add');
            }

            $extra               = array('page_title' => 'Kelola Siswa');
            $extra['keterangan'] = '<br/>* <a onclick="Loader.open()"  class="text-danger" href="' . site_url('operator/perbarui-data-siswa') . '">Klik disini</a>&nbsp;untuk memperbarui data siswa';

            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_add();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_alumni()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa');
            $crud->set_subject('Siswa');
            $crud->where('siswa.sekolah_id', $this->sekolah_id);
            $crud->where('tanggal_keluar IS NOT NULL');
            $crud->order_by('kelas_id', 'ASC');

            $crud->required_fields('nama_lengkap', 'jk','tanggal_keluar', 'jurusan', 'perguruan_tinggi');

            $crud->columns('nama_lengkap','tanggal_keluar', 'jurusan', 'perguruan_tinggi');

            $crud->field_type('nisn', 'hidden');

            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('nik', 'hidden');
            $crud->field_type('no_kk', 'hidden');
            $crud->field_type('tempat_lahir', 'hidden');
            $crud->field_type('tanggal_lahir', 'hidden');
            $crud->field_type('nama_ayah', 'hidden');
            $crud->field_type('nama_ibu_kandung', 'hidden');
            $crud->field_type('email', 'hidden');
            $crud->field_type('telp', 'hidden');
            $crud->field_type('nama_rombel', 'hidden');
            $crud->field_type('alamat', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('kelas_id', 'hidden');
            $crud->field_type('agama', 'hidden');
           // $crud->field_type('jk', 'hidden');
            $crud->field_type('foto', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);
            // $crud->field_type('id', 'hidden', $uuid);

            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            // $crud->field_type('tanggal_keluar', 'readonly');
            $crud->display_as('nisn', 'NISN');
            $crud->field_type('jenis_keluar', 'hidden');
            $crud->display_as('jk', 'Jenis Kelamin');
            $crud->display_as('nik', 'NIK');
            $crud->display_as('tanggal_keluar', 'Tanggal Lulus SNBT SNPMB');

            $crud->field_type('id', 'hidden');
            $crud->field_type('aktif', 'hidden');
            // $crud->field_type('nisn', 'readonly',);
            // $crud->field_type('nik', 'readonly',);
            // $crud->field_type('nama_lengkap', 'readonly',);
            // $crud->field_type('jk', 'readonly',);
            // $crud->field_type('agama', 'readonly',);
            // $crud->field_type('email', 'readonly',);
            // $crud->field_type('telp', 'readonly',);

            $crud->field_type('provinsi', 'hidden', );
            $crud->field_type('kabupaten', 'hidden', );
            $crud->field_type('kecamatan', 'hidden', );
            $crud->field_type('kelurahan', 'hidden', );
            // $crud->field_type('alamat', 'readonly',);
            $crud->field_type('tgl_masuk_sekolah', 'hidden', );
            $crud->field_type('aktif', 'hidden', 'TIDAK');

            // $crud->set_relation('kelas_id', 'kelas', 'nama_kelas');

            // $crud->display_as('kelas_id', 'Kelas');

            $crud->callback_after_insert(function ($post_array, $primary_key) {

                // $this->db->where('id', $primary_key);
                // $this->db->update(
                //     'siswa',
                //     array(
                //         'password'   => password_hash($post_array['nisn'], PASSWORD_DEFAULT),
                //         'tgl_update' => date('Y-m-d H:i:s'),
                //     )
                // );
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                // $this->db->where('id', $primary_key);
                // $this->db->update(
                //     'siswa',
                //     array(
                //         'tgl_update' => date('Y-m-d H:i:s'),
                //     )
                // );
            });

            // $crud->set_field_upload('foto', 'uploads');

           //$crud->callback_column('jurusan', function ($value, $row) {
               // $html = '<input type="text" id="jur_' . $row->id . '" value="' . $value . '" class="txtJurusan form-control form-control-sm" placeholder="">';
                //return $html;
            //});
            
            //$crud->callback_column('perguruan_tinggi', function ($value, $row) {
              //  $html = '<input type="text" id="pt_' . $row->id . '" value="' . $value . '" class="txtAlumni form-control form-control-sm" placeholder="">';
                //return $html;
            //});

            $script = '// Fungsi untuk mengirim data melalui AJAX
                        function postDataToServer(value, id, jenis) {
                            $.ajax({
                                type: "POST",
                                url: "' . site_url('operator/save-ajax-alumni') . '",
                                data: {
                                    nilai: value,
                                    id: id,
                                    jenis: jenis
                                },
                                success: function(response) {
                                    // Handle response dari server jika diperlukan
                                    console.log(response);
                                },
                                error: function(xhr, status, error) {
                                    // Handle kesalahan jika terjadi
                                    console.error(xhr.responseText);
                                }
                            });
                        }

                        // Menggunakan event listener untuk input text
                        $(document).on("keyup keydown", ".txtAlumni", function() {
                            var id = this.id.split("_")[1];
                            var value = $(this).val(); // Nilai input
                            var jenis = "pt";

                            // Memanggil fungsi untuk mengirim data ke server
                            postDataToServer(value, id, jenis);
                        });';

            $extra['script'] = $script;

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Alumni', $this->base_breadcrumbs . '/kelola-alumni');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // $crud->set_rules('nisn', 'NISN', 'is_unique[siswa.nuptk]|required');
                $crud->set_rules('email', 'Email', 'is_unique[siswa.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-alumni/add');
            } elseif ($state === 'edit') {

                // $primary_key = $this->uri->segment(4);

                // $cek = $this->db->get_where('siswa', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

                // if ($cek->num_rows() == 0) {
                //     redirect(site_url('operator/kelola-siswa'), 'reload');
                // }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-alumni/add');
            }

            $extra               = array('page_title' => 'Kelola Alumni');
            //$extra['keterangan'] = '<br/>* <a onclick="Loader.open()"  class="text-danger" href="' . site_url('operator/perbarui-data-siswa') . '">Klik disini</a>&nbsp;untuk memperbarui data alumni';

            $crud->unset_read();
            $crud->unset_clone();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    private function _get_provinsi_id()
    {
        $sekolah = $this->db->get_where('sekolah', array('id' => $this->sekolah_id))->row_array();
        return $sekolah['provinsi'];
    }

    public function jenis_prestasi()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('prestasi');
            $crud->set_subject('Jenis Prestasi');
            $crud->order_by('nama', 'ASC');
            // $crud->required_fields('nama_kelas');
            $crud->columns('nama', 'kelola_siswa');

            $crud->field_type('id', 'hidden');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('kelola_siswa', function ($value, $row) {
                return '<a class="text-info" href="' . site_url('operator/kelola_prestasi/' . $row->id) . '">Kelola</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Prestasi Siswa', $this->base_breadcrumbs . '/jenis-prestasi');
            // $this->breadcrumbs->push('Jenis Prestasi', $this->base_breadcrumbs . '/jenis-prestasi');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/jenis-prestasi/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/jenis-prestasi/add');
            }

            $crud->unset_clone();
            $crud->unset_read();
            // $crud->unset_add();
            // $crud->unset_edit();

            $extra = array('page_title' => 'Prestasi siswa');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function jenis_ekstrakulikuler()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('ekstrakulikuler');
            $crud->set_subject('Jenis Ekstrakulikuler');
            $crud->order_by('nama', 'ASC');

            // $crud->required_fields('nama_kelas');

            $crud->columns('nama', 'kelola_siswa');

            $crud->field_type('id', 'hidden');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('kelola_siswa', function ($value, $row) {
                return '<a class="text-info" href="' . site_url('operator/kelola_ekstrakulikuler/' . $row->id) . '">Kelola</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            // $this->breadcrumbs->push('Ekstrakulikuler Siswa', $this->base_breadcrumbs . '/kelola-ekstrakulikuler');
            $this->breadcrumbs->push('Jenis Ekstrakulikuler', $this->base_breadcrumbs . '/jenis-ekstrakulikuler');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/jenis-ekstrakulikuler/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/jenis-ekstrakulikuler/add');
            }

            $crud->unset_clone();
            $crud->unset_read();
            // $crud->unset_add();
            // $crud->unset_edit();

            $extra = array('page_title' => 'Jenis Ekstrakulikuler');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function upload_sertifikat()
    {
        if (!empty($_FILES['sertifikat']['name'])) {
            $upload                  = array();
            $upload['upload_path']   = './uploads';
            $upload['allowed_types'] = 'pdf|docx|doc|jpeg|png|bmp|jpg';
            $upload['encrypt_name']  = true;

            $this->load->library('upload', $upload);

            if (!$this->upload->do_upload('sertifikat')) {
                echo $this->upload->display_errors();
                exit();
            } else {

                $id          = $this->input->post('id');
                $prestasi_id = $this->input->post('prestasi_id');

                $success = $this->upload->data();
                $value   = $success['file_name'];

                $this->db->where('id', $id);
                $this->db->update('siswa_prestasi', array('sertifikat' => $value));

                $this->alert->set('alert-info', 'File sertifikat berhasil diupload');

                redirect('operator/kelola-prestasi/' . $prestasi_id);
            }
        }
    }

    public function save_ajax_prestasi()
    {

        $nilai = $this->input->post('nilai');
        $id    = $this->input->post('id');
        $jenis = $this->input->post('jenis');

        /*if ($jenis === 'kelas') {
        $this->db->where('id', $id);
        $this->db->update('siswa_prestasi', array('kelas' => $nilai));
        } else*/
        if ($jenis === 'tahun') {
            $this->db->where('id', $id);
            $this->db->update('siswa_prestasi', array('tahun' => $nilai));
        } elseif ($jenis === 'keterangan') {
            $this->db->where('id', $id);
            $this->db->update('siswa_prestasi', array('keterangan' => $nilai));
        }
    }

    public function save_ajax_alumni()
    {

        $nilai = $this->input->post('nilai');
        $id    = $this->input->post('id');
        $jenis = $this->input->post('jenis');

        if ($jenis === 'pt') {
            $this->db->where('id', $id);
            $this->db->update('siswa', array('perguruan_tinggi' => $nilai));
        }
    }

    public function kelola_prestasi($prestasi_id)
    {
        try {

            $nama_prestasi = get_column_value('prestasi', $prestasi_id, 'nama');
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa_prestasi');
            $crud->set_subject('Siswa');
            $crud->where('prestasi_id', $prestasi_id);

            $crud->field_type('id', 'hidden');
            $crud->field_type('siswa_id', 'hidden');
            $crud->field_type('sertifikat', 'hidden');
            $crud->field_type('prestasi_id', 'hidden', $prestasi_id);
            $crud->field_type('keterangan', 'hidden');
            $crud->field_type('kelas', 'hidden');
            $crud->field_type('tahun', 'hidden');

            $crud->columns('siswa_id', 'keterangan', 'tahun', /*'kelas',*/'sertifikat');

            // $crud->set_relation('prestasi_id', 'prestasi', 'nama');

            // $crud->display_as('prestasi_id', 'Prestasi');
            $crud->display_as('tmp_column', 'Siswa');
            $crud->display_as('keterangan', 'Perolehan');

            $crud->callback_insert(function ($post_array) {

                $siswa_list  = $_POST['siswa'];
                $prestasi_id = $_POST['prestasi_id'];

                $inserted_data = array();

                foreach ($siswa_list as $siswa) {
                    $data = array(
                        'siswa_id'    => $siswa,
                        'prestasi_id' => $prestasi_id,
                    );

                    $inserted_data[] = $data;
                }

                // Memasukkan data ke dalam database menggunakan INSERT IGNORE
                $values = array();
                foreach ($inserted_data as $data) {
                    $values[] = "(
                        '{$data['siswa_id']}',
                        '{$data['prestasi_id']}'
                        )";
                }

                if (!empty($values)) {
                    $values_string = implode(',', $values);
                    $values_string = rcustom_trim($values_string, ',');
                    $sql           = "INSERT IGNORE INTO siswa_prestasi (siswa_id, prestasi_id)
                                      VALUES $values_string";
                    $this->db->query($sql);
                }
            });

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Prestasi siswa', $this->base_breadcrumbs . '/jenis-prestasi');
            $this->breadcrumbs->push('Kelola ' . $nama_prestasi, $this->base_breadcrumbs . '/kelola-prestasi');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-prestasi/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-prestasi/add');
            } elseif ($state === 'list' || $state === 'success') {
                $crud->display_as('siswa_id', 'Nama lengkap');
                // $crud->display_as('kelas_id', 'Kelas');
                // $crud->set_relation('prestasi_id', 'prestasi', 'nama');
                $crud->set_relation('siswa_id', 'siswa', 'nama_lengkap');
            }

            $crud->callback_add_field('tmp_column', array($this, 'add_siswa_field'));

            /*
            $crud->callback_column('kelas', function ($value, $row) {
            $html = '<input type="text" id="kelas_' . $row->id . '" value="' . $value . '" class="txtPrestasi form-control form-control-sm" placeholder="">';
            return $html;
            });
             */

            $crud->callback_column('tahun', function ($value, $row) {
                $html = '<input type="text" id="tahun_' . $row->id . '" value="' . $value . '" class="txtPrestasi form-control form-control-sm" placeholder="">';
                return $html;
            });

            $crud->callback_column('keterangan', function ($value, $row) {
                $html = '<input type="text" id="keterangan_' . $row->id . '" value="' . $value . '" class="txtPrestasi form-control form-control-sm" placeholder="">';
                return $html;
            });

            $crud->callback_column('sertifikat', function ($value, $row) {
                $ret = '';

                if ($value !== null) {
                    $ret .= '<a href="' . site_url('uploads/' . $value) . ' " target="_blank">Lihat File Sertifikat</a>';
                }

                $ret .= '<form action="' . site_url('operator/upload-sertifikat') . '" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="prestasi_id" value="' . $row->prestasi_id . '">
                            <input class="upload" name="sertifikat" onchange="this.form.submit()" multiple="" type="file">
                        </form>';

                return $ret;
            });

            // $crud->callback_column('kelas_id', function ($value, $row) {
            //     $this->db->select('b.nama_kelas');
            //     $this->db->join('kelas b', 'a.kelas_id = b.id', 'left');
            //     $this->db->where('a.id', $row->siswa_id);
            //     $query = $this->db->get('siswa a');

            //     return $query->row_array()['nama_kelas'];
            // });

            $crud->unset_clone();
            $crud->unset_read();
            // $crud->unset_add();
            $crud->unset_edit();

            $extra = array('page_title' => 'Kelola Prestasi ' . $nama_prestasi);
            // $extra['keterangan'] = 'Untuk menambahkan Prestasi, silahkan <a href="' . site_url('operator/jenis-prestasi') . '">Klik disini</a>';
            $script = '// Fungsi untuk mengirim data melalui AJAX
                        function postDataToServer(value, id, jenis) {
                            $.ajax({
                                type: "POST",
                                url: "' . site_url('operator/save-ajax-prestasi') . '",
                                data: {
                                    nilai: value,
                                    id: id,
                                    jenis: jenis
                                },
                                success: function(response) {
                                    // Handle response dari server jika diperlukan
                                    console.log(response);
                                },
                                error: function(xhr, status, error) {
                                    // Handle kesalahan jika terjadi
                                    console.error(xhr.responseText);
                                }
                            });
                        }

                        // Menggunakan event listener untuk input text
                        $(document).on("keyup keydown", ".txtPrestasi", function() {
                            var id = this.id.split("_")[1];
                            var value = $(this).val(); // Nilai input
                             var jenis = this.id.startsWith("kelas") ? "kelas" : (this.id.startsWith("tahun") ? "tahun" : "keterangan"); // Menentukan jenis

                            // Memanggil fungsi untuk mengirim data ke server
                            postDataToServer(value, id, jenis);
                        });';

            $extra['script'] = $script;
            $output          = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function api_get_siswa()
    {

        header('content-type: application/json');
        $param = $this->input->get('q');

        $this->db->select('a.id, a.nama_lengkap,a.nisn,b.nama_kelas');
        $this->db->from('siswa a');
        $this->db->where('a.sekolah_id', $this->sekolah_id);
        $this->db->join('kelas b', 'a.kelas_id = b.id', 'left');
        $this->db->group_start();
        $this->db->where('a.nama_lengkap LIKE', "%$param%");
        $this->db->or_where('a.nisn LIKE', "$param%");
        $this->db->group_end();
        $this->db->order_by('a.nama_lengkap', 'ASC');
        $query = $this->db->get();

        $result = [];

        foreach ($query->result_array() as $row) {
            $result[] = [
                'id'   => $row['id'],
                'text' => strtoupper($row['nama_lengkap']) . ' - ' . $row['nama_kelas'],
            ];
        }

        echo json_encode($result);
    }

    public function add_siswa_field($value = false, $primary_key = false)
    {
        $ret = '<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
        $ret .= '<link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />';
        $ret .= '<style>
                    .select2-container--default .select2-selection--multiple .select2-selection__choice {
                        background-color: #007bff
                    }
                </style>';

        $ret .= '<select class="" id="select_siswa" name="siswa[]" multiple="multiple" style="width:100%">

                </select>';

        $ret .= '<script>

                    $(document).ready(function() {
                        $(\'#select_siswa\').select2({
                            ajax: {
                                url: \'' . site_url('operator/api-get-siswa') . '\',
                                dataType: \'json\',
                                delay: 250,
                                data: function(params) {
                                  return {
                                    q: params.term, // Inputan Select2
                                  };
                                },
                                processResults: function(data) {
                                  return {
                                    results: data
                                  };
                                },
                                cache: true
                              },
                              placeholder: \'Cari siswa\',
                              minimumInputLength: 3,
                        });
                    });


                </script>';
        return $ret;
    }

    public function kelola_ekstrakulikuler($ekstrakulikuler_id)
    {
        try {
            $nama_ekstrakulikuler = get_column_value('ekstrakulikuler', $ekstrakulikuler_id, 'nama');
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa_ekstrakulikuler');
            $crud->set_subject('Siswa');
            $crud->where('ekstrakulikuler_id', $ekstrakulikuler_id);

            $crud->field_type('id', 'hidden');
            $crud->field_type('siswa_id', 'hidden');
            $crud->field_type('status', 'hidden', 'AKTIF');
            $crud->field_type('ekstrakulikuler_id', 'hidden', $ekstrakulikuler_id);

            $crud->columns('siswa_id', 'kelas_id', 'status');

            // $crud->set_relation('ekstrakulikuler_id', 'ekstrakulikuler', 'nama');

            // $crud->display_as('ekstrakulikuler_id', 'Ekstrakulikuler');
            $crud->display_as('tmp_column', 'Siswa');

            $crud->callback_insert(function ($post_array) {

                $siswa_list         = $_POST['siswa'];
                $ekstrakulikuler_id = $_POST['ekstrakulikuler_id'];

                $inserted_data = array();

                foreach ($siswa_list as $siswa) {
                    $data = array(
                        'siswa_id'           => $siswa,
                        'ekstrakulikuler_id' => $ekstrakulikuler_id,
                    );

                    $inserted_data[] = $data;
                }

                // Memasukkan data ke dalam database menggunakan INSERT IGNORE
                $values = array();
                foreach ($inserted_data as $data) {
                    $values[] = "('{$data['siswa_id']}', '{$data['ekstrakulikuler_id']}')";
                }

                if (!empty($values)) {
                    $values_string = implode(', ', $values);
                    $sql           = "INSERT IGNORE INTO siswa_ekstrakulikuler (siswa_id, ekstrakulikuler_id) VALUES $values_string";
                    $this->db->query($sql);
                }
            });

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Ekstrakulikuler Siswa', $this->base_breadcrumbs . '/jenis-ekstrakulikuler');
            $this->breadcrumbs->push('Kelola ' . $nama_ekstrakulikuler, $this->base_breadcrumbs . '/kelola-ekstrakulikuler');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-ekstrakulikuler/add');

                //$crud->set_css("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css",false);
                //$crud->set_js("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js",false);

            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-ekstrakulikuler/add');
            } elseif ($state === 'list' || $state === 'success') {
                $crud->display_as('siswa_id', 'Nama lengkap');
                $crud->display_as('kelas_id', 'Kelas');
                // $crud->display_as('ekstrakulikuler_id', 'Ekstrakulikuler');
                // $crud->set_relation('ekstrakulikuler_id', 'ekstrakulikuler', 'nama');
                $crud->set_relation('siswa_id', 'siswa', 'nama_lengkap');
            }

            $crud->callback_add_field('tmp_column', array($this, 'add_siswa_field')); //lihat function add_siswa_field

            $crud->callback_column('status', function ($value, $row) {

                $html             = "";
                $link_aktifkan    = "set_status(1," . $row->id . ")";
                $link_nonaktifkan = "set_status(0," . $row->id . ")";

                if ($value === 'AKTIF') {
                    // $html = "<div id='ekstra_id_" . $row->id . "'><div style='color:#266927' id='" . $row->id . "'>Aktif (&nbsp;<a href='#' style='color:#ff111f;text-decoration:underline' onclick='" . $link_nonaktifkan . "'>Non Aktifkan</a>&nbsp;)</div></div>";
                    $html .= "<div id='ekstra_id_" . $row->id . "'><input class='togglebtn' id='" . $row->id . "' type='checkbox' checked data-on='Aktif' data-off='Non Aktif' data-toggle='toggle' data-onstyle='success' data-offstyle='danger' onclick='" . $link_nonaktifkan . "'></div>";
                } else {
                    // $html = "<div id='ekstra_id_" . $row->id . "'><div style='color:#ff111f' id='" . $row->id . "'>Non Aktif (&nbsp;<a href='#' style='#266927;text-decoration:underline' onclick='" . $link_aktifkan . "'>Aktifkan</a>&nbsp;)</div></div>";
                    $html .= "<div id='ekstra_id_" . $row->id . "'><input class='togglebtn' id='" . $row->id . "' type='checkbox' data-on='Aktif' data-off='Non Aktif' data-toggle='toggle' data-onstyle='success' data-offstyle='danger' onclick='" . $link_aktifkan . "'></div>";
                }

                return $html;
            });

            $crud->callback_column('kelas_id', function ($value, $row) {
                $this->db->select('b.nama_kelas');
                $this->db->join('kelas b', 'a.kelas_id = b.id', 'left');
                $this->db->where('a.id', $row->siswa_id);
                $query = $this->db->get('siswa a');

                return $query->row_array()['nama_kelas'];
            });

            $crud->unset_clone();
            $crud->unset_read();
            // $crud->unset_add();
            $crud->unset_edit();

            $extra = array('page_title' => 'Kelola Ekstrakulikuler ' . $nama_ekstrakulikuler);
            // $extra['keterangan'] = 'Untuk menambahkan Ekstrakulikuler, silahkan <a href="' . site_url('operator/jenis-ekstrakulikuler') . '">Klik disini</a>';

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    /**
     * The function `ekstrakulikuler_set_status()` updates the status of a student's extracurricular
     * activity and displays the updated status with an option to toggle it.
     */
    public function ekstrakulikuler_set_status()
    {

        $status = $this->input->post('status');
        $id     = $this->input->post('ekstrakulikuler_id');

        $st = array('false' => 'NON_AKTIF', 'true' => 'AKTIF');

        $this->db->where('id', $id);
        $this->db->update('siswa_ekstrakulikuler', array('status' => $st[$status]));

        // $link_aktifkan = "set_status(1," .  $id . ")";
        // $link_nonaktifkan = "set_status(0," .  $id . ")";

        // if ($status == 1) {
        //     echo "<div style='color:#266927' id='" . $id . "'>Aktif (&nbsp;<a href='#' style='color:#ff111f;text-decoration:underline' onclick='" . $link_nonaktifkan . "'>Non Aktifkan</a>&nbsp;)</div>";
        // } else {
        //     echo "<div style='color:#ff111f' id='" . $id . "'>Non Aktif (&nbsp;<a href='#' style='#266927;text-decoration:underline' onclick='" . $link_aktifkan . "'>Aktifkan</a>&nbsp;)</div>";
        // }
    }

    public function plot_point_js()
    {

        $sekolah = $this->db->get_where('sekolah', array('id' => $this->sekolah_id));

        if ($sekolah->num_rows() > 0) {
            $ret       = $sekolah->row_array();
            $latitude  = $ret['latitude'];
            $longitude = $ret['longitude'];
            $script    = '

                var map;
                var marker;
                var circle;
                var geocoder;
                window.onload = function() {
                  geocoder = new google.maps.Geocoder();
                  var latlng = new google.maps.LatLng(' . $latitude . ',' . $longitude . ');
                  var myOptions = {
                      zoom: 18,
                      center: latlng,
                      mapTypeId: google.maps.MapTypeId.SATELLITE
                    };
                    map = new google.maps.Map(document.getElementById("map_field_box"), myOptions);
                      addMarker(map.getCenter());
                      google.maps.event.addListener(map,"click", function(event) {
                    //alert("You cannot reset the location by changing pointer in here");
                      //addMarker(event.latLng);
                    });
                  }

                function addMarker(location) {
                    if(marker) {marker.setMap(null);}
                    marker = new google.maps.Marker({
                      position: location,
                        draggable: true
                    });
                    marker.setMap(map);
                  }';

            echo $script;
        }
    }

    public function download_rekap_presensi_pegawai($filter_tahun, $filter_bulan)
    {

        $data = array();

        $this->db->select('a.id AS sekolah_id,
		                   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
		                   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $cek = $this->db->get_where('sekolah a', array('a.id' => $this->sekolah_id))->row_array();

        $data['sekolah_id']   = $cek['sekolah_id'];
        $data['nama_sekolah'] = $cek['nama_sekolah'];
        $data['kecamatan']    = $cek['nama_kecamatan'];
        $data['kabupaten']    = $cek['nama_kabupaten'];
        $data['logo']         = $this->logo;
        $data['header']       = 'DAFTAR HADIR PEGAWAI';

        //get kepala sekolah info

        $this->db->select('nama_lengkap,nuptk');
        $this->db->where('sekolah_id', $cek['sekolah_id']);
        $this->db->where('jabatan', 'KEPALA');
        $kepsek = $this->db->get('pegawai');

        if ($kepsek->num_rows() > 0) {
            $dt_kepsek            = $kepsek->row_array();
            $data['kepsek_nama']  = $dt_kepsek['nama_lengkap'];
            $data['kepsek_nuptk'] = $dt_kepsek['nuptk'];
        } else {
            $data['kepsek_nama']  = '-kepsek belum di set-';
            $data['kepsek_nuptk'] = '-kepsek belum di set-';
        }

        $this->db->select("a.fulldate,
                           a.day,
                           a.dayofweek,
		                   IF(a.dayofweek = 7,'L',IF(COUNT(b.id) > 0,'L','A')) AS `status_hari`");
        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->group_by('a`.`fulldate`, `a`.`day`, a.dayofweek');
        $tanggalan = $this->db->get_where('dates a', array('a.year' => $filter_tahun, 'a.month' => $filter_bulan));

        //get color for bobot 0
        $zero_color = $this->db->get_where('bobot', array('nilai' => 0))->row_array();

        $s_select = '';
        foreach ($tanggalan->result_array() as $t) {
            // if ($t['status_hari'] === 'L') {
            //     $s_select .= '\'L|#FFFFFF\' AS `.' . $t['day'] . '`,';
            // } else {
            //     $s_select .= 'IFNULL(CONCAT(b' . $t['day'] . '.nilai,\'|\',b' . $t['day'] . '.warna),\'0|' . $zero_color['warna'] . '\') AS `.' . $t['day'] . '`,';
            // }

            if ($t['status_hari'] === 'L') {
                $s_select .= '\'L|#FFFFFF\' AS `.' . $t['day'] . '`,';
            } else {
                $s_select .= 'IFNULL(CONCAT(MAX(b' . $t['day'] . '.nilai),\'|\',GROUP_CONCAT(b' . $t['day'] . '.warna SEPARATOR \',\')),\'0|' . $zero_color['warna'] . '\') AS `.' . $t['day'] . '`,';
            }
        }

        $data['jml_hari'] = $tanggalan->num_rows();

        $s_select = substr($s_select, 0, -1);

        // $this->db->select("a.nuptk,
        //                     a.nama_lengkap,
        //                     a.jabatan," . $s_select . ",
        //                     SUM(IF(b.status_masuk = 'IZIN-SAKIT',1,0)) AS S,
        //                     SUM(IF(b.status_masuk = 'IZIN-TIDAK-MASUK',1,0)) AS I,
        //                     SUM(IF(b.status_masuk = 'ALPA',1,0)) AS A,
        //                     SUM(IF(b.status_masuk = 'NORMAL' OR b.status_masuk = 'TELAT' OR b.status_masuk = 'IZIN-TELAT' ,1,0)) AS JML", false);

        $this->db->select("/*a.nuptk,*/
                            UPPER(a.nama_lengkap) AS `Nama lengkap`,
                            /*a.jabatan,*/" . $s_select . ",
                            SUM(IF(b.status_masuk = 'IZIN-SAKIT',1,0)) AS S,
                            SUM(IF(b.status_masuk = 'IZIN-TIDAK-MASUK',1,0)) AS I,
                            SUM(IF(b.status_masuk = 'ALPA',1,0)) AS A,
                            SUM(IF(b.status_masuk = 'NORMAL' OR b.status_masuk = 'TELAT' OR b.status_masuk = 'IZIN-TELAT' ,1,0)) AS JML", false);

        // foreach ($tanggalan->result_array() as $t) {
        //     $this->db->join("(SELECT a.pegawai_id,b.nilai,b.warna
        //                      FROM kehadiran_pegawai a
        //                      LEFT JOIN bobot b ON a.status_masuk = b.status_masuk AND a.status_pulang = b.status_pulang
        //                      WHERE YEAR(a.jam_masuk) = $filter_tahun AND MONTH(a.jam_masuk) = $filter_bulan AND DAY(a.jam_masuk) = " . $t['day'] . "
        //                      GROUP BY a.pegawai_id) b" . $t['day'], "a.id = b" . $t['day'] . ".pegawai_id", "left");
        // }

        foreach ($tanggalan->result_array() as $t) {
            $this->db->join("(SELECT a.pegawai_id,
                                 MAX(b.nilai) AS nilai,
                                 GROUP_CONCAT(b.warna SEPARATOR ',') AS warna
                         FROM kehadiran_pegawai a
                         LEFT JOIN bobot b ON a.status_masuk = b.status_masuk AND a.status_pulang = b.status_pulang
                         WHERE YEAR(a.jam_masuk) = $filter_tahun AND MONTH(a.jam_masuk) = $filter_bulan AND DAY(a.jam_masuk) = " . $t['day'] . "
                         GROUP BY a.pegawai_id) b" . $t['day'], "a.id = b" . $t['day'] . ".pegawai_id", "left");
        }

        // $this->db->join("(SELECT a.pegawai_id,
        //                            a.status_masuk
        //                   FROM   kehadiran_pegawai a
        //                   WHERE  Year(a.jam_masuk) = $filter_tahun AND Month(a.jam_masuk) = $filter_bulan
        //                   GROUP  BY a.pegawai_id,DATE(a.jam_masuk)) b", 'a.id = b.pegawai_id', 'left');

        $this->db->join(" (SELECT a.pegawai_id,
                                    MAX(a.status_masuk) AS status_masuk
                            FROM   kehadiran_pegawai a
                            WHERE  Year(a.jam_masuk) = $filter_tahun AND Month(a.jam_masuk) = $filter_bulan
                            GROUP  BY a.pegawai_id,DATE(a.jam_masuk)) b", 'a.id = b.pegawai_id', 'left');

        $this->db->where('a.sekolah_id', $this->sekolah_id);
        $this->db->where('a.aktif', 'YA');
        $this->db->group_by('a.id');

        $this->db->order_by('a.nama_lengkap ASC');
        $presensi = $this->db->get('pegawai a');

        Modules::run("export/pdf_rekap_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
    }

    public function kehadiran_pegawai()
    {

        $qry = $this->db->get_where('sekolah', array('id' => $this->sekolah_id));

        if ($qry->num_rows() == 0) {
            redirect(site_url('dinas/kelola-sekolah'), 'reload');
        }

        $sekolah = $qry->row_array();

        if (!empty($_POST)) {

            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            $bobot_normal = $cek_bobot['nilai'];
            $hari_aktif   = _get_hari_aktif($tahun, $bulan);

            $keterangan = '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong><br/>';
            $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="' . site_url('operator/download-rekap-presensi-pegawai/' . $tahun . '/' . $bulan) . '">disini</a></strong><br/>';
            $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('operator/download-rekap-harian-pegawai', $tahun, $bulan);

            $data['keterangan'] = $keterangan;

            $this->db->select('a.id,
                               a.nuptk,
                               a.nama_lengkap,
                               a.jabatan,
                               IFNULL(SUM(c.nilai),0) AS nilai_presensi');
            $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $tahun . ' AND MONTH(b.jam_masuk) = ' . $bulan, 'left');
            $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
            $this->db->where('a.sekolah_id', $this->sekolah_id);
            $this->db->where('a.aktif', 'YA');
            $this->db->group_by('a.id');
            $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
            $data['presensi'] = $this->db->get('pegawai a');
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Kehadiran Pegawai', $this->base_breadcrumbs . '/kehadiran-pegawai/' . $this->sekolah_id);

        $data['sekolah']    = $sekolah;
        $data['page_name']  = 'kehadiran_pegawai';
        $data['page_title'] = 'Kehadiran Pegawai <br/>' . $sekolah['nama'];
        $this->_page_output($data);
    }

    public function download_rekap_harian_pegawai($filter_tahun, $filter_bulan, $filter_hari, $file_ext = 'pdf')
    {

        $data = array();

        $this->db->select('a.id,
						   a.nama,
						   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
						   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $this->db->where('a.id', $this->sekolah_id);
        $cek = $this->db->get('sekolah a')->row_array();

        $data['sekolah_id']   = $cek['id'];
        $data['nama_sekolah'] = $cek['nama'];
        $data['kecamatan']    = $cek['nama_kecamatan'];
        $data['kabupaten']    = $cek['nama_kabupaten'];
        $data['logo']         = $this->logo;
        $data['header']       = 'DAFTAR HADIR HARIAN PEGAWAI';

        //get kepala sekolah info
        $this->db->select('nama_lengkap,nuptk');
        $this->db->where('sekolah_id', $data['sekolah_id']);
        $this->db->where('jabatan', 'KEPALA');
        $kepsek = $this->db->get('pegawai');

        if ($kepsek->num_rows() > 0) {
            $dt_kepsek            = $kepsek->row_array();
            $data['kepsek_nama']  = $dt_kepsek['nama_lengkap'];
            $data['kepsek_nuptk'] = $dt_kepsek['nuptk'];
        } else {
            $data['kepsek_nama']  = '-kepsek belum di set-';
            $data['kepsek_nuptk'] = '-kepsek belum di set-';
        }

        $this->db->select(
            "a.nama_lengkap,
			IFNULL(b.jam_masuk, '-') AS `Jam Masuk`,
			IFNULL(b.jam_pulang, '-') AS `Jam Pulang`,
			IFNULL(CONCAT(b.status_masuk, '#', b.status_pulang), '-') AS `Presensi`,
			IFNULL(e.nilai, 0) AS `Nilai`",
            false
        );

        $this->db->join("kehadiran_pegawai b", "a.id = b.pegawai_id AND YEAR(b.jam_masuk) = '$filter_tahun' AND MONTH(b.jam_masuk) = '$filter_bulan' AND DAY(b.jam_masuk) = '$filter_hari'", "left");
        $this->db->join("dates c", "DATE(b.jam_masuk) = c.fulldate", "left");
        $this->db->join("hari_libur d", "c.fulldate = d.tgl", "left");
        $this->db->join("bobot e", "b.status_masuk = e.status_masuk AND b.status_pulang = e.status_pulang", "left");
        $this->db->where("a.sekolah_id", $this->sekolah_id);
        $presensi = $this->db->get("pegawai a");

        if ($file_ext === 'pdf') {
            Modules::run("export/pdf_rekap_harian_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);
        } else {
            Modules::run("export/xls_rekap_harian_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);
        }
    }

    public function show_map_field($value = false, $primary_key = false)
    {
        return '<p>Perbaiki posisi dengan klik ganda pada lokasi yang ditentukan. Mouse Scrool untuk Zoom-in atau Zoom-out</p>
                <input id="pac-input" class="controls" type="text" placeholder="Pencarian" style="width:200px;margin-top:10px;height: calc(2.25rem + 2px); padding: .375rem .75rem;font-size: 1rem;font-weight: 400;">
                <div id="location-map" style="width:924px; height:350px;"></div>';
    }

    public function resize_map($tipe, $width)
    {
        // echo '$("#map_field_box").attr("style", "height: ' . $height .';width:100%");';
        $style = "";
        if ($tipe === 'percent') {
            $style = $width . '%';
        } else {
            $style = $width . 'px';
        }

        echo 'document.getElementById("map_field_box").style["height"] = "' . $style . '";';
    }

    public function download_presensi_pegawai($pegawai_id, $filter_tahun, $filter_bulan)
    {

        $data = array();

        $this->db->select('a.sekolah_id,
						   a.nama_lengkap,
						   a.nuptk,
						   b.nama AS nama_sekolah,
						   c.nama AS nama_kecamatan,
						   d.nama AS nama_kabupaten');
        $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
        $this->db->join('wilayah_kecamatan c', 'b.kecamatan = c.id', 'left');
        $this->db->join('wilayah_kabupaten d', 'c.kabupaten_id = d.id', 'left');
        $this->db->where('a.id', $pegawai_id);
        $cek = $this->db->get('pegawai a')->row_array();

        $data['sekolah_id']   = $cek['sekolah_id'];
        $data['nama_pegawai'] = $cek['nama_lengkap'];
        $data['nuptk']        = $cek['nuptk'];
        $data['nama_sekolah'] = $cek['nama_sekolah'];
        $data['kecamatan']    = $cek['nama_kecamatan'];
        $data['kabupaten']    = $cek['nama_kabupaten'];
        $data['logo']         = $this->logo;
        $data['header']       = 'DAFTAR HADIR PEGAWAI';

        //get kepala sekolah info
        $this->db->select('nama_lengkap,nuptk');
        $this->db->where('sekolah_id', $cek['sekolah_id']);
        $this->db->where('jabatan', 'KEPALA');
        $kepsek = $this->db->get('pegawai');

        if ($kepsek->num_rows() > 0) {
            $dt_kepsek            = $kepsek->row_array();
            $data['kepsek_nama']  = $dt_kepsek['nama_lengkap'];
            $data['kepsek_nuptk'] = $dt_kepsek['nuptk'];
        } else {
            $data['kepsek_nama']  = '-kepsek belum di set-';
            $data['kepsek_nuptk'] = '-kepsek belum di set-';
        }

        $this->db->select(
            "a.fulldate AS tanggal,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS `Status Hari`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_masuk,'-'))) AS `Jam Masuk`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_pulang,'-'))) AS `Jam Pulang`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status_masuk,'-',c.status_pulang),'-'))) AS `Presensi`,
            IF(a.dayofweek = 7,0,IF(COUNT(b.id) > 0,0,IFNULL(d.nilai,0))) AS `Nilai`"
        );

        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->join("kehadiran_pegawai c", "a.fulldate = DATE(c.jam_masuk) AND c.pegawai_id = $pegawai_id", "left");
        $this->db->join("bobot d", "c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang", "left");
        $this->db->where("YEAR(a.fulldate)", $filter_tahun);
        $this->db->where("MONTH(a.fulldate)", $filter_bulan);
        $this->db->group_by("a.fulldate");
        $presensi = $this->db->get("dates a");

        Modules::run("export/pdf_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
    }

    public function profile_sekolah()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('sekolah');
            $crud->set_subject('Profile Sekolah');

            $crud->required_fields('nama');

            $crud->columns('npsn', 'nama', 'alamat', 'kelurahan', 'status', 'rekap_kehadiran');
            $crud->field_type('id', 'hidden');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('provinsi', 'hidden');
            $crud->field_type('level', 'readonly');
            $crud->field_type('status', 'readonly');
            $crud->field_type('npsn', 'readonly');
            $crud->field_type('nama', 'readonly');
            $crud->field_type('kabupaten', 'readonly');
            $crud->field_type('kecamatan', 'readonly');
            $crud->field_type('kelurahan', 'readonly');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('login_aktif', 'hidden');

            $crud->display_as('nama', 'Nama');
            $crud->display_as('npsn', 'NPSN');

            $crud->set_field_upload('logo', 'uploads');
            // $crud->display_as('foto','logo');

            $crud->callback_after_insert(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'sekolah',
                    array(
                        'password'   => password_hash($post_array['npsn'], PASSWORD_DEFAULT),
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'sekolah',
                    array(
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            // $crud->set_relation('provinsi', 'wilayah_provinsi', 'nama', array('id' => $this->provinsi_id));
            $crud->set_relation('kabupaten', 'wilayah_kabupaten', 'nama', array('provinsi_id' => $this->_get_provinsi_id()));
            $crud->set_relation('kecamatan', 'wilayah_kecamatan', 'nama');
            $crud->set_relation('kelurahan', 'wilayah_kelurahan', 'nama');

            $crud->callback_column('rekap_kehadiran', function ($value, $row) {
                return '<a href="' . site_url('operator/rekap-kehadiran/' . simple_crypt($row->id, 'e')) . '">Lihat</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Profile Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
                // $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            } elseif ($state === 'edit') {

                $curr_sekolah_id = $this->uri->segment(4);

                if ($curr_sekolah_id != $this->sekolah_id) {
                    redirect(site_url('operator/profile-sekolah/edit/' . $this->sekolah_id), 'reload');
                }

                $crud->set_js("assets/js/map.js?v=" . date("YmdHis"));
                $crud->set_js('https://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places', false);

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-sekolah/edit');
            }

            // $crud->change_field_type('latitude', 'hidden');
            // $crud->change_field_type('longitude', 'hidden');

            $crud->callback_add_field('map', array($this, 'show_map_field'));
            $crud->callback_edit_field('map', array($this, 'show_map_field'));

            $this->load->library('Gc_Dependent_Select');

            // $crud->unset_add();
            // $crud->unset_delete();
            $crud->unset_back_to_list();

            // $crud->set_lang_string(
            //     'update_success_message',
            //     'Your data has been successfully stored into the database.<br/>Please wait while you are redirecting to the list page.
            //      <script type="text/javascript">
            //       window.location = "' . site_url('operator/profile-sekolah/edit/' . $this->sekolah_id) . '";
            //      </script>
            //      <div style="display:none">'
            // );

            // $fields = array(

            //     'kabupaten' => array( // second dropdown name
            //         'table_name' => 'wilayah_kabupaten', // table of state
            //         'title' => 'nama', // state title
            //         'id_field' => 'id', // table of state: primary key
            //         'relate' => null, // table of state:
            //         'data-placeholder' => 'Pilih Kabupaten', //dropdown's data-placeholder:
            //     ),
            //     // third field. same settings
            //     'kecamatan' => array(
            //         'table_name'       => 'wilayah_kecamatan',
            //         'title'            => 'nama', // now you can use this format )))
            //         'id_field' => 'id',
            //         'relate'           => 'kabupaten_id',
            //         'data-placeholder' => 'Pilih Kecamatan',
            //     ),
            //     'kelurahan' => array(
            //         'table_name'       => 'wilayah_kelurahan',
            //         'title'            => 'nama', // now you can use this format )))
            //         'id_field' => 'id',
            //         'relate'           => 'kecamatan_id',
            //         'data-placeholder' => 'Pilih Kelurahan',
            //     ),
            // );

            // $config = array(
            //     'main_table'         => 'sekolah',
            //     'main_table_primary' => 'id',
            //     "url"                => site_url('/operator/profile-sekolah/'),
            //     'ajax_loader'        => base_url() . 'assets/ajax-loader.gif',
            // );

            // $wilayah = new Gc_dependent_select($crud, $fields, $config);
            // $js      = $wilayah->get_js();

            $extra = array('page_title' => 'Kelola Sekolah');
            // $extra['keterangan'] = '<br/>Untuk mendapatkan map / lokasi sekolah yang akurat, silahkan login akun operator aplikasi android anda didepan laptop/komputer yang digunakan untuk presensi<br/>
            //                         Masukkan latitude & longitude yang tertera dibawah kode QR aplikasi android pada textbox latitude & latitude pada form dibawah<br/>
            //                         Pastikan anda merefresh layar hp anda beberapa kali untuk mendapatkan lokasi yang akurat';

            $output = $crud->render();
            // $output->output .= $js;

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function ganti_password()
    {
        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('sekolah', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('operator/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('operator/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('sekolah', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('operator/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('operator/ganti-password'), 'reload');
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

    public function kelola_matapelajaran()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('matapelajaran');
            $crud->set_subject('Mata Pelajaran');
            $crud->where('sekolah_id', $this->sekolah_id);

            $crud->required_fields('nama');

            $crud->columns('nama');
            $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Mata Pelajaran', $this->base_breadcrumbs . '/kelola-matapelajaran');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-matapelajaran/add');
            } elseif ($state === 'edit') {

                $primary_key = $this->uri->segment(4);
                $cek         = $this->db->get_where('matapelajaran', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

                if ($cek->num_rows() == 0) {
                    redirect(site_url('operator/kelola-matapelajaran'), 'reload');
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-matapelajaran/add');
            }

            $crud->unset_clone();
            $crud->unset_read();

            $extra  = array('page_title' => 'Kelola Mata Pelajaran');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_kelas()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('kelas');
            $crud->set_subject('Kelas');
            $crud->where('kelas.sekolah_id', $this->sekolah_id);
            $crud->order_by('nama_kelas', 'ASC');

            // $crud->required_fields('nama_kelas');

            $crud->columns('nama_kelas');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);
            $crud->field_type('pegawai_id', 'readonly', );
            $crud->set_relation('pegawai_id', 'pegawai', 'nama_lengkap');

            $crud->field_type('id', 'hidden');
            $crud->field_type('semester_id', 'hidden');
            $crud->field_type('tanggal_mulai', 'hidden');
            $crud->field_type('tanggal_selesai', 'hidden');
            $crud->field_type('jumlah_pembelajaran', 'hidden');
            $crud->field_type('jumlah_anggota_rombel', 'hidden');
            $crud->field_type('keterangan', 'hidden');
            $crud->field_type('nama_kelas', 'readonly');

            $crud->display_as('pegawai_id', 'Wali Kelas');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Kelas', $this->base_breadcrumbs . '/kelola-kelas');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-kelas/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-kelas/add');
            }

            $crud->unset_clone();
            // $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();

            $extra      = array('page_title' => 'Kelola Sekolah');
            $keterangan = '<br/>* <a onclick="Loader.open()"  class="text-danger" href="' . site_url('operator/perbarui-data-kelas') . '">Klik di sini</a>&nbsp;untuk memperbarui data kelas';

            $extra['keterangan'] = $keterangan;
            $output              = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function perbarui_data_kelas()
    {
        $this->getRombel();
    }

    public function perbarui_data_pegawai()
    {
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        _getPtk($token,$this->npsn);

        $this->alert->set('alert-success', 'Data berhasil diperbarui');
        redirect(site_url('operator/kelola-pegawai'), 'reload');
    }
    

    public function perbarui_data_siswa()
    {
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        _getPesertaDidik($token,$this->npsn);

        $this->alert->set('alert-success', 'Data berhasil diperbarui');
        redirect(site_url('operator/kelola-siswa'), 'reload');
    }

    // public function kelola_kelas()
    // {
    //     try {
    //         $this->load->library(array('grocery_CRUD'));
    //         $crud = new Grocery_CRUD();

    //         $crud->set_table('kelas');
    //         $crud->set_subject('Kelas');
    //         $crud->where('sekolah_id',$this->sekolah_id);

    //         $crud->required_fields('pegawai_id','nama_kelas');

    //         $crud->columns('pegawai_id', 'nama_kelas');
    //         $crud->field_type('tgl_update', 'hidden');
    //         $crud->field_type('sekolah_id', 'hidden',$this->sekolah_id);

    //         $crud->display_as('pegawai_id','Wali Kelas');

    //         $crud->callback_after_insert(function ($post_array, $primary_key) {

    //         });

    //         $crud->callback_after_update(function ($post_array, $primary_key) {

    //         });

    //         $guru = $this->db->get_where('pegawai',array('sekolah_id' => $this->sekolah_id,'jabatan' => 'GURU'));

    //         $arr_guru = array();
    //         foreach ($guru->result_array() as $row) {
    //             $arr_guru[$row['id']] = $row['nama_lengkap'];
    //         }

    //         $crud->field_type('pegawai_id', 'dropdown', $arr_guru);

    //         $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
    //         $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-kelas');

    //         $state = $crud->getState();
    //         if ($state === 'insert_validation') {

    //         }elseif($state === 'update_validation'){

    //         }elseif($state === 'add'){
    //             $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs .'/kelola-kelas/add');
    //         }elseif($state === 'edit'){
    //             $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs .'/kelola-kelas/add');
    //         }

    //         $extra = array('page_title' => 'Kelola Kelas');
    //         $output = $crud->render();

    //         $output = array_merge((array) $output, $extra);

    //         $this->_page_output($output);
    //     } catch (Exception $e) {
    //         show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    //     }
    // }

    //kelas / rombel

    public function getRombel()
    {
        //dapatkan token
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        $jsonDataRombel        = getRombonganBelajar($token, $this->npsn);
        $decodedOutputFilePath = json_decode($jsonDataRombel, true);

        $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

        $jsonData = file_get_contents($outputPathFile);
        // Mendekode konten JSON menjadi struktur data yang dapat diakses
        $data = json_decode($jsonData, true);

        //jenis_rombel_keterangan
        $arrFilter = array('Kelas');
        foreach ($data as $item) {

            $rombonganBelajarId    = $item['rombongan_belajar_id'];
            $semesterId            = $item['semester_id'];
            $sekolahId             = $item['sekolah_id'];
            $nama                  = $item['nama'];
            $ptkId                 = $item['ptk_id'];
            $tanggalMulai          = $item['tanggal_mulai'];
            $tanggalSelesai        = $item['tanggal_selesai'];
            $jumlahPembelajaran    = $item['jumlah_pembelajaran'];
            $jumlahAnggotaRombel   = $item['jumlah_anggota_rombel'];
            $jenisRombelKeterangan = $item['jenis_rombel_keterangan'];

            if (in_array($jenisRombelKeterangan, $arrFilter)) {
                $set = array(
                    'id'                    => $rombonganBelajarId,
                    'semester_id'           => $semesterId,
                    'sekolah_id'            => $sekolahId,
                    'nama_kelas'            => $nama,
                    'pegawai_id'            => $ptkId,
                    'tanggal_mulai'         => $tanggalMulai,
                    'tanggal_selesai'       => $tanggalSelesai,
                    'jumlah_pembelajaran'   => $jumlahPembelajaran,
                    'jumlah_anggota_rombel' => $jumlahAnggotaRombel,
                    'keterangan'            => $jenisRombelKeterangan,
                    'tgl_update'            => date('Y-m-d H:i:s'),
                );

                $exclude_columns = array();
                $this->db->on_duplicate('kelas', $set, $exclude_columns);
            }
        }

        unlink($outputPathFile);

        $this->alert->set('alert-success', 'Data berhasil diperbarui');
        redirect(site_url('operator/kelola-kelas'), 'reload');
    }

    //peserta-didik
    // public function getPesertaDidik()
    // {
    //     //dapatkan token
    //     $decodedToken = json_decode(getToken(), true);
    //     $token        = $decodedToken['return']['token'];

    //     $jsonDataPtk           = getPesertaDidik($token, $this->npsn);
    //     $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    //     $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    //     $jsonData = file_get_contents($outputPathFile);
    //     // Mendekode konten JSON menjadi struktur data yang dapat diakses
    //     $data = json_decode($jsonData, true);

    //     foreach ($data as $item) {

    //         $pesertaDidikId      = $item['peserta_didik_id']; //peserta_didik_id
    //         $sekolahId           = $item['sekolah_id']; //sekolah_id
    //         $rombonganBelajarId  = $item['rombongan_belajar_id'];
    //         $nisn                = custom_trim($item['nisn']);
    //         $nik                 = custom_trim($item['nik']);
    //         $no_kk               = custom_trim($item['no_kk']);
    //         $password            = 'INITIAL_PASSWORD'; //password_hash($nisn, PASSWORD_DEFAULT);
    //         $nama                = $item['nama'];
    //         $jenisKelamin        = $item['jenis_kelamin'];
    //         $agama               = $item['agama']; //
    //         $email               = custom_trim($item['email']);
    //         $tempat_lahir        = custom_trim($item['tempat_lahir']);
    //         $tanggal_lahir       = $item['tanggal_lahir'];
    //         $nomorTeleponSeluler = custom_trim($item['nomor_telepon_seluler']);
    //         $kodeProvinsi        = $item['kode_provinsi'];
    //         $kodeKabupaten       = $item['kode_kabupaten']; //k
    //         $kodeKecamatan       = $item['kode_kecamatan']; //
    //         $kodeWilayah         = $item['kode_wilayah']; //
    //         $alamatJalan         = $item['alamat_jalan'];
    //         $tanggalMasukSekolah = $item['tanggal_masuk_sekolah']; //tanggal_masuk_sekolah
    //         $tanggalKeluar       = $item['tanggal_keluar']; //tanggal_keluar
    //         $jenisKeluar         = $item['jenis_keluar']; //jenis_keluar

    //         $set = array(

    //             'id'                => $pesertaDidikId,
    //             'sekolah_id'        => $sekolahId,
    //             'kelas_id'          => $rombonganBelajarId,
    //             'nisn'              => $nisn,
    //             'nik'               => $nik,
    //             'no_kk'             => $no_kk,
    //             'password'          => $password,
    //             'nama_lengkap'      => $nama,
    //             'jk'                => $jenisKelamin,
    //             'agama'             => $agama,
    //             'email'             => $email,
    //             'tempat_lahir'      => $tempat_lahir,
    //             'tanggal_lahir'     => $tanggal_lahir,
    //             'telp'              => $nomorTeleponSeluler,
    //             'provinsi'          => $kodeProvinsi,
    //             'kabupaten'         => $kodeKabupaten,
    //             'kecamatan'         => $kodeKecamatan,
    //             'kelurahan'         => $kodeWilayah,
    //             'alamat'            => $alamatJalan,
    //             'tgl_masuk_sekolah' => $tanggalMasukSekolah,
    //             'tanggal_keluar'    => $tanggalKeluar,
    //             'jenis_keluar'      => $jenisKeluar,
    //             'tgl_update'        => date('Y-m-d H:i:s'),
    //         );

    //         $exclude_columns = array('password', 'id');
    //         $this->db->on_duplicate('siswa', $set, $exclude_columns);
    //     }

    //     unlink($outputPathFile);

    //     $this->alert->set('alert-success', 'Data berhasil diperbarui');
    //     redirect(site_url('operator/kelola-siswa'), 'reload');
    // }

    //Pendidik dan Tenaga Kependidikan (PTK)
    // public function getPtk()
    // {
    //     //dapatkan token
    //     $decodedToken = json_decode(getToken(), true);
    //     $token        = $decodedToken['return']['token'];

    //     $jsonDataPtk           = getPtk($token, $this->npsn);
    //     $decodedOutputFilePath = json_decode($jsonDataPtk, true);

    //     $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

    //     $jsonData = file_get_contents($outputPathFile);
    //     // Mendekode konten JSON menjadi struktur data yang dapat diakses
    //     $data = json_decode($jsonData, true);

    //     foreach ($data as $item) {
    //         $ptkId             = $item['ptk_id'];
    //         $sekolahId         = $item['sekolah_id'];
    //         $nuptk             = $item['nuptk'];
    //         $nip               = $item['nip'];
    //         $nama              = $item['nama'];
    //         $tempatLahir       = $item['tempat_lahir'];
    //         $tanggalLahir      = $item['tanggal_lahir'];
    //         $nik               = $item['nik'];
    //         $jenisKelamin      = $item['jenis_kelamin'];
    //         $agama             = $item['agama'];
    //         $skPengangkatan    = $item['sk_pengangkatan'];
    //         $tmtPengangkatan   = $item['tmt_pengangkatan'];
    //         $statusKepegawaian = $item['status_kepegawaian']; //status_pegawai
    //         $jenisPtk          = $item['jenis_ptk']; //jabatan
    //         $password          = 'INITIAL_PASSWORD'; //password_hash($nuptk, PASSWORD_DEFAULT);
    //         $alamatJalan       = $item['alamat_jalan'];
    //         $email             = $item['email'];
    //         $telp              = $item['no_hp'];
    //         $statusKeaktifan   = $item['status_keaktifan']; //aktif

    //         $riwayatSertifikasiBidangStudi                 = $item['riwayat_sertifikasi_bidang_studi'];
    //         $riwayatSertifikasiJenisSertifikasi            = $item['riwayat_sertifikasi_jenis_sertifikasi'];
    //         $riwayatSertifikasiTahunSertifikasi            = $item['riwayat_sertifikasi_tahun_sertifikasi'];
    //         $riwayatSertifikasiNomorSertifikat             = $item['riwayat_sertifikasi_nomor_sertifikat'];
    //         $riwayatSertifikasiNrg                         = $item['riwayat_sertifikasi_nrg'];
    //         $riwayatSertifikasiNomorPeserta                = $item['riwayat_sertifikasi_nomor_peserta'];
    //         $riwayatPendidikanFormalBidangStudi            = $item['riwayat_pendidikan_formal_bidang_studi'];
    //         $riwayatPendidikanFormalJenjangPendidikan      = $item['riwayat_pendidikan_formal_jenjang_pendidikan'];
    //         $riwayatPendidikanFormalGelarAkademik          = $item['riwayat_pendidikan_formal_gelar_akademik'];
    //         $riwayatPendidikanFormalSatuanPendidikanFormal = $item['riwayat_pendidikan_formal_satuan_pendidikan_formal'];
    //         $riwayatPendidikanFormalFakultas               = $item['riwayat_pendidikan_formal_fakultas'];
    //         $riwayatPendidikanFormalKependidikan           = $item['riwayat_pendidikan_formal_kependidikan'];
    //         $riwayatPendidikanFormalTahunMasuk             = $item['riwayat_pendidikan_formal_tahun_masuk'];
    //         $riwayatPendidikanFormalTahunLulus             = $item['riwayat_pendidikan_formal_tahun_lulus'];
    //         $riwayatPendidikanFormalNim                    = $item['riwayat_pendidikan_formal_nim'];
    //         $riwayatPendidikanFormalStatusKuliah           = $item['riwayat_pendidikan_formal_status_kuliah'];
    //         $riwayatPendidikanFormalSemester               = $item['riwayat_pendidikan_formal_semester'];
    //         $riwayatPendidikanFormalIpk                    = $item['riwayat_pendidikan_formal_ipk'];

    //         $set = array(
    //             'id'                                                 => $ptkId,
    //             'sekolah_id'                                         => $sekolahId,
    //             'nuptk'                                              => $nuptk,
    //             'nip'                                                => $nip,
    //             'nama_lengkap'                                       => $nama,
    //             'tempat_lahir'                                       => $tempatLahir,
    //             'tgl_lahir'                                          => $tanggalLahir,
    //             'nik'                                                => $nik,
    //             'jk'                                                 => $jenisKelamin,
    //             'agama'                                              => $agama,
    //             'sk_pengangkatan'                                    => $skPengangkatan,
    //             'tmt_pengangkatan'                                   => $tmtPengangkatan,
    //             'jabatan'                                            => $jenisPtk,
    //             'status_pegawai'                                     => $statusKepegawaian,
    //             'password'                                           => $password,
    //             'alamat'                                             => $alamatJalan,
    //             'email'                                              => $email,
    //             'telp'                                               => $telp,
    //             'aktif'                                              => $statusKeaktifan,
    //             'riwayat_sertifikasi_bidang_studi'                   => $riwayatSertifikasiBidangStudi,
    //             'riwayat_sertifikasi_jenis_sertifikasi'              => $riwayatSertifikasiJenisSertifikasi,
    //             'riwayat_sertifikasi_tahun_sertifikasi'              => $riwayatSertifikasiTahunSertifikasi,
    //             'riwayat_sertifikasi_nomor_sertifikat'               => $riwayatSertifikasiNomorSertifikat,
    //             'riwayat_sertifikasi_nrg'                            => $riwayatSertifikasiNrg,
    //             'riwayat_sertifikasi_nomor_peserta'                  => $riwayatSertifikasiNomorPeserta,
    //             'riwayat_pendidikan_formal_bidang_studi'             => $riwayatPendidikanFormalBidangStudi,
    //             'riwayat_pendidikan_formal_jenjang_pendidikan'       => $riwayatPendidikanFormalJenjangPendidikan,
    //             'riwayat_pendidikan_formal_gelar_akademik'           => $riwayatPendidikanFormalGelarAkademik,
    //             'riwayat_pendidikan_formal_satuan_pendidikan_formal' => $riwayatPendidikanFormalSatuanPendidikanFormal,
    //             'riwayat_pendidikan_formal_fakultas'                 => $riwayatPendidikanFormalFakultas,
    //             'riwayat_pendidikan_formal_kependidikan'             => $riwayatPendidikanFormalKependidikan,
    //             'riwayat_pendidikan_formal_tahun_masuk'              => $riwayatPendidikanFormalTahunMasuk,
    //             'riwayat_pendidikan_formal_tahun_lulus'              => $riwayatPendidikanFormalTahunLulus,
    //             'riwayat_pendidikan_formal_nim'                      => $riwayatPendidikanFormalNim,
    //             'riwayat_pendidikan_formal_status_kuliah'            => $riwayatPendidikanFormalStatusKuliah,
    //             'riwayat_pendidikan_formal_semester'                 => $riwayatPendidikanFormalSemester,
    //             'riwayat_pendidikan_formal_ipk'                      => $riwayatPendidikanFormalIpk,
    //             'tgl_update'                                         => date('Y-m-d H:i:s'),
    //         );

    //         $exclude_columns = array('password');
    //         $this->db->on_duplicate('pegawai', $set, $exclude_columns);
    //     }

    //     unlink($outputPathFile);

    //     $this->alert->set('alert-success', 'Data berhasil diperbarui');
    //     redirect(site_url('operator/kelola-pegawai'), 'reload');
    // }

    public function kelola_extrakurikuler()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('ekstrakulikuler');
            $crud->set_subject('Ekstrakulikuler');
            $crud->order_by('nama', 'ASC');

            $crud->required_fields('nama');

            $crud->columns('nama');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola ekstrakulikuler', $this->base_breadcrumbs . '/kelola-ekstrakulikuler');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-ekstrakulikuler/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-ekstrakulikuler/add');
            }

            $crud->unset_clone();
            $crud->unset_read();

            $extra  = array('page_title' => 'Kelola ekstrakulikuler');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
