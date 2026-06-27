<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends CI_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $sekolah_id;
    private $kelas_id;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));

        $this->breadcrumbs->load_config('default');

        $user_level       = $this->session->userdata('user_level');
        $this->user_id    = $this->session->userdata('user_id');
        $this->sekolah_id = $this->session->userdata('user_sekolah_id');
        $this->kelas_id   = $this->session->userdata('user_kelas_id');

        $this->base_breadcrumbs = '/siswa';

        if ($user_level !== 'SISWA') {
            redirect(site_url('signin'), 'reload');
        }

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    public function _page_output($output = null)
    {
        $output['user_id']      = $this->user_id;
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

    public function jadwal_pelajaran()
    {
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Jadwal Pelajaran', $this->base_breadcrumbs . '/jadwal-pelajaran');

        $data['page_name']  = 'jadwal_pelajaran';
        $data['page_title'] = 'Jadwal Pelajaran';
        $this->_page_output($data);
    }

    public function load_events()
    {

        header('content-type: application/json');

        // $user_id = $this->input->post('user_id');

        // $exclude_date = array();
        // $data_weekend = array();

        // $this->db->where('YEAR(fulldate)', date('Y'));
        // $this->db->where('MONTH(fulldate)', date('m'));
        // $weekend = $this->db->get_where('dates', array('weekend' => 1));

        // foreach ($weekend->result_array() as $row) {
        //     $data_weekend[] = array(
        //         "name"      => "Libur",
        //         "date"      => $row['fulldate'],
        //         "type"      => "holiday",
        //         "everyYear" => false,
        //     );

        //     $exclude_date[] = $row['fulldate'];
        // }

        // $data_libur = array();

        // $this->db->where('YEAR(tgl)', date('Y'));
        // $this->db->where('MONTH(tgl)', date('m'));
        // $libur = $this->db->get('hari_libur');

        // foreach ($libur->result_array() as $row) {
        //     $data_libur[] = array(
        //         "name"      => $row['keterangan'],
        //         "date"      => $row['tgl'],
        //         "type"      => "holiday",
        //         "everyYear" => false,
        //     );

        //     $exclude_date[] = $row['tgl'];
        // }

        // $this->db->where('YEAR(fulldate)', date('Y'));
        // $this->db->where('MONTH(fulldate)', date('m'));
        // $this->db->where_not_in('fulldate', $exclude_date);
        // $hari_aktif = $this->db->get('dates');
        // $data_aktif = array();

        // foreach ($hari_aktif->result_array() as $row) {
        //     $data_aktif[] = array(
        //         "name"      => '<h3>Jadwal Pelajaran</h3><script>load_day_event("' . $row['dayofweek'] . '","' . $row['fulldate'] . '")</script>',
        //         "date"      => $row['fulldate'],
        //         "type"      => "normal",
        //         "everyYear" => false,
        //     );
        // }

        // echo json_encode(array('events' => array_merge($data_weekend, $data_libur, $data_aktif)));

        $this->db->select(
            "a.fulldate,
             a.dayofweek,
             IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) as status_hari"
        );
        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->where("YEAR(a.fulldate)", date('Y'));
        $this->db->where("MONTH(a.fulldate)", date('m'));
        $this->db->group_by("a.fulldate");
        $presensi = $this->db->get("dates a");

        foreach ($presensi->result_array() as $row) {
            $data_hari[] = array(
                "name"      => '<h3>Jadwal Pelajaran</h3><script>load_day_event("' . $row['dayofweek'] . '","' . $row['fulldate'] . '")</script>',
                "date"      => $row['fulldate'],
                "type"      => ($row['status_hari'] === 'AKTIF' ? "normal" : "holiday"),
                "everyYear" => false,
            );
        }

        echo json_encode(array('events' => array_merge($data_hari)));
    }

    public function load_day_event()
    {
        header('content-type: application/json');
        $dayofweek = $this->input->post('dayofweek');
        $fullday   = $this->input->post('fullday');

        $hari_array = array('MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU');

        $this->db->select('a.id,
                          a.jam_mulai,a.jam_selesai,
                          b.nama AS matapelajaran,
                          c.nama_lengkap AS pengampu');
        $this->db->join('matapelajaran b', 'a.matapelajaran_id = b.id', 'left');
        $this->db->join('pegawai c', 'a.pegawai_id = c.id', 'left');
        $this->db->where('a.kelas_id', $this->kelas_id);
        $this->db->where('a.hari', $hari_array[$dayofweek]);
        $this->db->order_by('a.jam_mulai ASC');
        // $this->db->limit(999);

        $jadwal_mengajar = $this->db->get('jadwal_mengajar a');

        $presensi_siswa = array();

        foreach ($jadwal_mengajar->result_array() as $row) {

            $this->db->select('a.jam_absen,a.status');
            $this->db->join('guru_mengajar b', 'a.guru_mengajar_id = b.id', 'left');
            $this->db->join('jadwal_mengajar c', 'b.jadwal_mengajar_id = c.id', 'left');
            $this->db->where('c.id', $row['id']);
            $this->db->where('a.siswa_id', $this->user_id);
            $this->db->where('DATE(a.jam_absen)', $fullday);

            $presensi = $this->db->get('kehadiran_siswa_detail a');

            if ($presensi->num_rows() > 0) {
                $ps               = $presensi->row_array();
                $presensi_siswa[] = array(
                    'jam_mulai'     => $row['jam_mulai'],
                    'jam_selesai'   => $row['jam_selesai'],
                    'matapelajaran' => $row['matapelajaran'],
                    'pengampu'      => $row['pengampu'],
                    'presensi'      => $ps['status'],
                );
            } else {
                $presensi_siswa[] = array(
                    'jam_mulai'     => $row['jam_mulai'],
                    'jam_selesai'   => $row['jam_selesai'],
                    'matapelajaran' => $row['matapelajaran'],
                    'pengampu'      => $row['pengampu'],
                    'presensi'      => '-',
                );
            }
        }

        echo json_encode(
            array(
                'table_jadwal' => $this->load->view(
                    'div_jadwal_pelajaran',
                    array(
                        'data'     => $presensi_siswa,
                        'tanggal'  => $fullday,
                        'cek_izin' => $this->db->get_where('izin_siswa', array('siswa_id' => $this->user_id, 'tgl_izin' => $fullday)),
                    ),
                    true
                ),
            )
        );
    }

    public function pengajuan_izin()
    {

        $this->form_validation->set_rules('tanggal_izin', 'Tanggal Izin', 'required');
        $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'siswa_id'      => $this->user_id,
                'tgl_izin'      => $this->input->post('tanggal_izin'),
                'jenis_izin'    => $this->input->post('jenis_izin'),
                'keterangan'    => $this->input->post('keterangan'),
                'tgl_pengajuan' => date('Y-m-d H:i:s'),
            );

            if (!empty($_FILES['file_pendukung']['name'])) {

                $upload['upload_path']   = './uploads';
                $upload['allowed_types'] = 'jpeg|jpg|pdf';
                $upload['encrypt_name']  = true;
                $upload['max_size']      = 1024;

                $this->load->library('upload', $upload);

                if (!$this->upload->do_upload('file_pendukung')) {
                    $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah');
                    redirect(site_url('siswa/jadwal_pelajaran'), 'reload');
                } else {
                    $success   = $this->upload->data();
                    $file_name = $success['file_name'];

                    $data['file'] = $file_name;
                }
            }

            $this->db->insert('izin_siswa', $data);

            $this->alert->set('alert-success', 'Data permohonan izin berhasil diajukan');
            redirect(site_url('siswa/jadwal-pelajaran'), 'reload');
        }
    }

    public function batalkan_izin($izin_siswa_id)
    {

        $izin_siswa_id = simple_crypt($izin_siswa_id, 'd');

        $this->db->where('id', $izin_siswa_id);
        $this->db->delete('izin_siswa');

        $this->alert->set('alert-success', 'Data permohonan izin berhasil dibatalkan');
        redirect(site_url('siswa/jadwal-pelajaran'), 'reload');
    }

    public function profile()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa');
            $crud->set_subject('Profile');

            $crud->required_fields('nama_lengkap');
            $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');

            // $crud->set_relation('kelas_id','kelas','nama_kelas');

            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('sekolah_id', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('terakhir_login', 'readonly');
            $crud->field_type('nisn', 'readonly');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('aktif', 'hidden');
            $crud->display_as('nisn', 'NISN');
            $crud->display_as('kelas_id', 'Kelas');
            $crud->set_field_upload('foto', 'uploads');

            $crud->callback_edit_field('kelas_id', function ($value, $primary_key) {

                $this->db->where('id', $value);
                $q = $this->db->get('kelas');

                if ($q->num_rows() > 0) {
                    $kelas = $q->row_array();
                    return '<div id="field-kelas_id" class="readonly_label">' . $kelas['nama_kelas'] . '</div>';
                } else {
                    return '<div id="field-kelas_id" class="readonly_label">BELUM ADA DATA</div>';
                }
            });

            $crud->callback_after_insert(function ($post_array, $primary_key) {
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

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Profil', $this->base_breadcrumbs . '/profile');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
                // $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            } elseif ($state === 'edit') {

                $curr_user_id = $this->uri->segment(4);

                if ($curr_user_id != $this->user_id) {
                    redirect(site_url('siswa/profile/edit/' . $this->user_id), 'reload');
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
            }

            $crud->unset_back_to_list();
            $extra = array('page_title' => 'Kelola Profil');

            $output = $crud->render();

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

            $cek_user = $this->db->get_where('siswa', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('siswa/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('siswa/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('siswa', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('siswa/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('siswa/ganti-password'), 'reload');
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
}
