<?php

defined('BASEPATH') || exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Dinas extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $provinsi_id;
    private $kabupaten_id;
    private $user_level;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert', 'api', 'cronjob'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));

        $this->breadcrumbs->load_config('default');

        $this->user_level   = $this->session->userdata('user_level');
        $this->user_id      = $this->session->userdata('user_id');
        $this->provinsi_id  = $this->session->userdata('provinsi_id');
        $this->kabupaten_id = $this->session->userdata('kabupaten_id');
        $this->logo         = $this->session->userdata('user_foto');

        $this->base_breadcrumbs = '/dinas';

        if (!in_array($this->user_level, array('DINAS', 'DINAS-SMA', 'DINAS-SMK', 'DINAS-SLB'))) {
            redirect(site_url('signin'), 'reload');
        }

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    public function _page_output($output = null)
    {

        $userName = $this->session->userdata('user_nama');

        $titlesToRemove = ['Hj', 'H.', 'Dr.', 'M.Sc.', 'S.T.', 'S.E.', 'M.T.', 'S.Pd.', 'M.Pd', '.', ',']; // Daftar gelar yang akan dihapus

        $output['user_id']      = $this->user_id;
        $output['nama_lengkap'] = str_replace($titlesToRemove, "", $userName);
        $output['user_level']   = $this->user_level;
        $this->load->view('master_page.php', (array) $output);
    }

    public function index()
    {
        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);

        $data['page_name']  = 'beranda';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function reset_token_pegawai($pegawai_id)
    {
        $this->db->set('token_login', 'NULL', false);
        $this->db->where('id', $pegawai_id);
        $this->db->update('pegawai');

        $this->alert->set('alert-success', 'Token berhasil direset');

        redirect(site_url('dinas/kelola-pegawai'), 'reload');
    }

    public function reset_password_pegawai($pegawai_id)
    {

        $cek = $this->db->get_where('pegawai', array('id' => $pegawai_id))->row_array();

        $npsn_pass = password_hash($cek['nuptk'], PASSWORD_DEFAULT);

        $this->db->set('password', "'" . $npsn_pass . "'", false);
        $this->db->where('id', $pegawai_id);
        $this->db->update('pegawai');

        $this->alert->set('alert-success', 'Password berhasil direset dan diganti menjadi NIP');

        redirect(site_url('dinas/kelola-pegawai'), 'reload');
    }

    // public function kelola_pegawai()
    // {
    //     try {
    //         $this->load->library(array('grocery_CRUD'));
    //         $crud = new Grocery_CRUD();

    //         $crud->set_table('pegawai');
    //         $crud->set_subject('Pegawai');
    //         // $crud->where('sekolah_id', $this->sekolah_id);
    //         $crud->where('dinas_provinsi', $this->provinsi_id);
    //         $crud->where('dinas_kabupaten', $this->kabupaten_id);
    //         $crud->order_by('nama_lengkap', 'ASC');

    //         $crud->required_fields('jabatan', 'nuptk', 'nama_lengkap');
    //         $crud->columns('nama_lengkap', 'jabatan', 'reset', 'nilai_presensi');

    //         $crud->field_type('dinas_provinsi', 'hidden', $this->provinsi_id);
    //         $crud->field_type('dinas_kabupaten', 'hidden', $this->kabupaten_id);
    //         $crud->field_type('sekolah_id', 'hidden', 0);
    //         $crud->field_type('id', 'hidden');
    //         $crud->field_type('aktif', 'hidden');

    //         $crud->display_as('dinas_pangkat', 'Pangkat/Golongan');
    //         $crud->display_as('dinas_unit_kerja', 'Unit Kerja');
    //         $crud->field_type('status_pegawai', 'hidden');
    //         $crud->display_as('nuptk', 'NIP');

    //         // $crud->field_type('jabatan','enum',array('GURU','TU','KEPALA','SATPAM','KEBERSIHAN'));

    //         $crud->field_type('tgl_update', 'hidden');
    //         $crud->field_type('password', 'hidden');
    //         $crud->field_type('token_id', 'hidden');
    //         $crud->field_type('terakhir_login', 'hidden');
    //         $crud->field_type('wali_kelas', 'hidden');
    //         // $crud->field_type('sekolah_id', 'hidden', 0);
    //         $crud->field_type('dev_unique_id', 'hidden');
    //         $crud->field_type('token_login', 'hidden');

    //         // $crud->display_as('status_pegawai', 'Status');
    //         $crud->display_as('nama_lengkap', 'Nama');

    //         $crud->callback_after_insert(function ($post_array, $primary_key) {

    //             $this->db->where('id', $primary_key);
    //             $this->db->update(
    //                 'pegawai',
    //                 array(
    //                     'password'   => password_hash($post_array['nuptk'], PASSWORD_DEFAULT),
    //                     'tgl_update' => date('Y-m-d H:i:s'),
    //                 )
    //             );
    //         });

    //         $crud->callback_before_insert(array($this, 'generate_id_for_pegawai'));

    //         $crud->callback_after_update(function ($post_array, $primary_key) {

    //             $this->db->where('id', $primary_key);
    //             $this->db->update(
    //                 'pegawai',
    //                 array(
    //                     'tgl_update' => date('Y-m-d H:i:s'),
    //                 )
    //             );
    //         });

    //         $crud->callback_column('jabatan', function ($value, $row) {

    //             return $value;
    //         });

    //         $crud->callback_column('reset', function ($value, $row) {

    //             return '<a onclick="return confirm(\'anda yakin melakukan reset token untuk pegawai ini?\');" class="text-danger" href="' . site_url('dinas/reset-token-pegawai/' . $row->id) . '">TOKEN</a>&nbsp;-&nbsp;<a onclick="return confirm(\'anda yakin melakukan reset password untuk pegawai ini?\');" class="text-success" href="' . site_url('dinas/reset-password-pegawai/' . $row->id) . '">PASSWORD</a>';
    //         });

    //         $crud->callback_column('nilai_presensi', function ($value, $row) {

    //             if (!empty($_POST)) {
    //                 $bulan = $this->input->post('filter_bulan');
    //                 $tahun = $this->input->post('filter_tahun');

    //                 $this->db->select('IFNULL(SUM(c.nilai),0) AS nilai_presensi');
    //                 $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $tahun . ' AND MONTH(b.jam_masuk) = ' . $bulan, 'left');
    //                 $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
    //                 $this->db->where('a.id', $row->id);
    //                 $this->db->group_by('a.id');
    //                 $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
    //                 $presensi = $this->db->get('pegawai a')->row_array();

    //                 return '<span class="badge badge-info">' . $presensi['nilai_presensi'] . '</span>&nbsp;-&nbsp;<a href="' . site_url('dinas/download_presensi_pegawai_dinas/' . $row->id . '/' . $tahun . '/' . $bulan) . '" class="btn btn-success btn-sm" data-toggle="tooltip" title="Download"><i class="fas fa-cloud-download-alt"></i></a>';
    //             } else {
    //                 return '-';
    //             }
    //         });

    //         $crud->set_field_upload('foto', 'uploads');

    //         $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
    //         $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-pegawai');

    //         $state = $crud->getState();
    //         if ($state === 'insert_validation') {
    //             $crud->set_rules('nuptk', 'NUPTK', 'is_unique[pegawai.nuptk]|required');
    //             $crud->set_rules('email', 'Email', 'is_unique[pegawai.email]|valid_email');
    //         } elseif ($state === 'update_validation') {
    //             $crud->set_rules('email', 'Email', 'valid_email');
    //         } elseif ($state === 'add') {
    //             $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-pegawai/add');
    //         } elseif ($state === 'edit') {

    //             // $primary_key = $this->uri->segment(4);

    //             // $cek = $this->db->get_where('pegawai', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

    //             // if ($cek->num_rows() == 0) {
    //             //     redirect(site_url('operator/kelola-pegawai'), 'reload');
    //             // }

    //             $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-pegawai/edit');
    //         }

    //         $extra = array('page_title' => 'Kelola Pegawai');
    //         // $keterangan = '<br/>* Anda dapat meng-import data pegawai dari file Microsoft Excel. Klik di <a class="text-danger" href="' . site_url('operator/import-pegawai') . '">SINI</a>&nbsp;untuk melakukannya';
    //         $keterangan = '<br/>* Reset token digunakan jika pegawai mengganti perangkat untuk login';

    //         if (!empty($_POST['filter_bulan'])) {
    //             $bulan = $this->input->post('filter_bulan');
    //             $tahun = $this->input->post('filter_tahun');

    //             $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

    //             $bobot_normal = $cek_bobot['nilai'];
    //             $hari_aktif   = _get_hari_aktif($tahun, $bulan);

    //             $keterangan .= '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong><br/>';
    //             $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="' . site_url('dinas/download-rekap-presensi-pegawai-dinas/' . $tahun . '/' . $bulan) . '">disini</a></strong><br/>';
    //             $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('dinas/download-rekap-harian-pegawai-dinas', $tahun, $bulan);
    //         }

    //         $extra['keterangan'] = $keterangan;

    //         $crud->unset_clone();
    //         $crud->unset_read();

    //         $output = $crud->render();

    //         $output = array_merge((array) $output, $extra);

    //         $this->_page_output($output);
    //     } catch (Exception $e) {
    //         show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    //     }
    // }

    public function kelola_dinas_kabupaten()
    {
        $kabupaten_id = $this->uri->segment(3);

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Pegawai');

            $crud->where('dinas_provinsi', $this->provinsi_id);
            $crud->where('dinas_kabupaten', $kabupaten_id);
            $crud->order_by('nama_lengkap', 'ASC');

            $crud->required_fields('jabatan', 'nuptk', 'nama_lengkap');
            $crud->columns('nama_lengkap', 'jabatan', 'reset', 'nilai_presensi');

            $crud->field_type('dinas_provinsi', 'hidden', $this->provinsi_id);
            $crud->field_type('dinas_kabupaten', 'hidden', $kabupaten_id);
            $crud->field_type('sekolah_id', 'hidden', 0);
            $crud->field_type('id', 'hidden');
            $crud->field_type('aktif', 'hidden');

            $crud->display_as('dinas_pangkat', 'Pangkat/Golongan');
            $crud->display_as('dinas_unit_kerja', 'Unit Kerja');
            $crud->field_type('status_pegawai', 'hidden');
            $crud->field_type('nip', 'hidden');
            $crud->field_type('nik', 'hidden');
            $crud->field_type('sk_pengangkatan', 'hidden');
            $crud->field_type('tmt_pengangkatan', 'hidden');
            $crud->display_as('nuptk', 'NIP');

            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('wali_kelas', 'hidden');

            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');

            // $crud->display_as('status_pegawai', 'Status');
            $crud->display_as('nama_lengkap', 'Nama');

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

            $crud->callback_before_insert(array($this, 'generate_id_for_pegawai'));

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

                return $value;
            });

            $crud->callback_column('reset', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset token untuk pegawai ini?\');" class="text-danger" href="' . site_url('dinas/reset-token-pegawai/' . $row->id) . '">TOKEN</a>&nbsp;-&nbsp;<a onclick="return confirm(\'anda yakin melakukan reset password untuk pegawai ini?\');" class="text-success" href="' . site_url('dinas/reset-password-pegawai/' . $row->id) . '">PASSWORD</a>';
            });

            $crud->callback_column('nilai_presensi', function ($value, $row) {

                if (!empty($_POST)) {
                    $bulan = $this->input->post('filter_bulan');
                    $tahun = $this->input->post('filter_tahun');

                    $this->db->select('IFNULL(SUM(c.nilai),0) AS nilai_presensi');
                    $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $tahun . ' AND MONTH(b.jam_masuk) = ' . $bulan, 'left');
                    $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
                    $this->db->where('a.id', $row->id);
                    $this->db->group_by('a.id');
                    $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
                    $presensi = $this->db->get('pegawai a')->row_array();

                    return '<span class="badge badge-info">' . $presensi['nilai_presensi'] . '</span>&nbsp;-&nbsp;<a href="' . site_url('dinas/download_presensi_pegawai_dinas/' . $row->id . '/' . $tahun . '/' . $bulan) . '" class="btn btn-success btn-sm" data-toggle="tooltip" title="Download"><i class="fas fa-cloud-download-alt"></i></a>';
                } else {
                    return '-';
                }
            });

            $crud->set_field_upload('foto', 'uploads');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-dinas');
            $this->breadcrumbs->push('Dinas Kabupaten ', $this->base_breadcrumbs . '/kelola-dinas-kabupaten/' . $kabupaten_id);

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('nuptk', 'NUPTK', 'is_unique[pegawai.nuptk]|required');
                $crud->set_rules('email', 'Email', 'is_unique[pegawai.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-dinas-kabupaten' . $kabupaten_id . '/add');
            } elseif ($state === 'edit') {

                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-dinas-kabupaten' . $kabupaten_id . '/edit');
            }

            $kab = $this->db->get_where('wilayah_kabupaten', array('id' => $kabupaten_id))->row_array();

            $extra = array('page_title' => 'Kelola Pegawai Dinas ' . $kab['nama']);
            // $keterangan = '<br/>* Anda dapat meng-import data pegawai dari file Microsoft Excel. Klik di <a class="text-danger" href="' . site_url('operator/import-pegawai') . '">SINI</a>&nbsp;untuk melakukannya';
            $keterangan = '<br/>* Reset token digunakan jika pegawai mengganti perangkat untuk login';

            if (!empty($_POST['filter_bulan'])) {
                $bulan = $this->input->post('filter_bulan');
                $tahun = $this->input->post('filter_tahun');

                $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

                $bobot_normal = $cek_bobot['nilai'];
                $hari_aktif   = _get_hari_aktif($tahun, $bulan);

                $keterangan .= '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong><br/>';
                $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="' . site_url('dinas/presensi-bulanan-dinas/' . $tahun . '/' . $bulan) . '">disini</a></strong><br/>';
                $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('dinas/presensi-harian-dinas', $tahun, $bulan);
            }

            $extra['keterangan'] = $keterangan;

            $crud->unset_clone();
            $crud->unset_read();
            // $crud->unset_add();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_dinas()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('wilayah_kabupaten');
            $crud->set_subject('Kelola Pegawai Dinas');

            $crud->where('provinsi_id', $this->provinsi_id);
            $crud->where_in('id', explode(',', $this->kabupaten_id));
            $crud->order_by('nama', 'ASC');

            $crud->columns('nama', 'kelola');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('kelola', function ($value, $row) {
                return '<a class="text-success" href="' . site_url('dinas/kelola-dinas-kabupaten/' . $row->id) . '">Kelola</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-dinas');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-dinas/add');
            } elseif ($state === 'edit') {
            }

            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_add();

            $output = $crud->render();
            $extra  = array('page_title' => 'Kelola Pegawai');
            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function generate_id_for_pegawai($post_array)
    {
        $post_array['id'] = generate_uuid();
        return $post_array;
    }

    public function profile()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('user');
            $crud->set_subject('Profile');

            $crud->required_fields('nama_lengkap');
            $crud->columns('username', 'nama_lengkap', 'email', 'telp');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('terakhir_login', 'readonly');
            $crud->field_type('username', 'readonly');
            $crud->field_type('level', 'readonly');
            $crud->field_type('provinsi_id', 'hidden', 15);
            $crud->field_type('kabupaten_id', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');

            // $crud->display_as('kabupaten_id', 'Wilayah Kerja');
            $crud->display_as('foto', 'Logo');

            $crud->set_field_upload('foto', 'uploads');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'user',
                    array(
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            // $crud->set_relation('kabupaten_id', 'wilayah_kabupaten', 'nama');

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
                    redirect(site_url('dinas/profile/edit/' . $this->user_id), 'reload');
                }

                $crud->set_js("assets/js/map.js?v=" . date("YmdHis"));
                $crud->set_js('https://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places', false);

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
            }

            $crud->callback_add_field('map', array($this, 'show_map_field'));
            $crud->callback_edit_field('map', array($this, 'show_map_field'));

            $crud->unset_back_to_list();
            $extra = array('page_title' => 'Kelola Sekolah');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
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

    private function _cek_wilayah($jenis, $str_kab = '-', $str_kec = '-', $str_kel = '-')
    {

        $ret = '';

        $str_kab = !empty($str_kab) ? $str_kab : '-';
        $str_kec = !empty($str_kec) ? $str_kec : '-';
        $str_kel = !empty($str_kel) ? $str_kel : '-';

        if ($jenis === 'kelurahan') {

            $this->db->select('a.nama AS kelurahan, b.nama AS kecamatan,
                               c.nama AS kabupaten, d.nama AS provinsi,
                               a.id AS kelurahan_id, b.id AS kecamatan_id,
                               c.id AS kabupaten_id, d.id AS provinsi_id');
            $this->db->join('wilayah_kecamatan b', 'a.kecamatan_id = b.id', 'left');
            $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
            $this->db->join('wilayah_provinsi d', 'c.provinsi_id = d.id', 'left');
            $this->db->where('d.id', $this->provinsi_id);
            $this->db->like('a.nama', $str_kel);
            $this->db->like('b.nama', $str_kec);
            $this->db->like('c.nama', $str_kab);

            $q = $this->db->get("wilayah_kelurahan a");

            if ($q->num_rows() > 0) {
                $arr_q = $q->row_array();
                $ret   = $arr_q['kelurahan_id'];
            }
        } elseif ($jenis === 'kecamatan') {

            $this->db->select('b.nama AS kecamatan, c.nama AS kabupaten, d.nama AS provinsi,
                               b.id AS kecamatan_id, c.id AS kabupaten_id, d.id AS provinsi_id');
            $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
            $this->db->join('wilayah_provinsi d', 'c.provinsi_id = d.id', 'left');
            $this->db->where('d.id', $this->provinsi_id);
            $this->db->like('b.nama', $str_kec);
            $this->db->like('c.nama', $str_kab);

            $q = $this->db->get("wilayah_kecamatan b");

            if ($q->num_rows() > 0) {
                $arr_q = $q->row_array();
                $ret   = $arr_q['kecamatan_id'];
            }
        } elseif ($jenis === 'kabupaten') {

            $this->db->select('c.nama AS kabupaten, d.nama AS provinsi,
                                c.id AS kabupaten_id, d.id AS provinsi_id');
            $this->db->join('wilayah_provinsi d', 'c.provinsi_id = d.id', 'left');
            $this->db->where('d.id', $this->provinsi_id);
            $this->db->like('c.nama', $str_kab);

            $q = $this->db->get("wilayah_kabupaten c");

            if ($q->num_rows() > 0) {
                $arr_q = $q->row_array();
                $ret   = $arr_q['kabupaten_id'];
            }
        }

        return $ret;
    }

    public function perbarui_data_sekolah()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('wilayah_kabupaten');
            $crud->where_in('id', explode(',', $this->kabupaten_id));
            $crud->set_subject('Perbarui Data Sekolah');
            $crud->order_by('nama', 'ASC');

            $crud->columns('id', 'nama', 'statistik', 'perbarui');

            // $crud->display_as('status_pegawai', 'Status');
            $crud->display_as('nama', 'Nama kabupaten');
            $crud->display_as('id', 'Kode');

            $crud->callback_column('perbarui', function ($value, $row) {

                return '<a onclick="Loader.open()" class="text-danger" href="' . site_url('dinas/getsekolah/' . $row->id) . '">Perbarui</a>';
            });

            $crud->callback_column('statistik', function ($value, $row) {

                $this->db->select(
                    "SUM(IF(LEVEL = 'SMA',1,0)) AS sma,
                     SUM(IF(LEVEL = 'SMK',1,0)) AS smk,
                     SUM(IF(LEVEL = 'SLB',1,0)) AS slb"
                );
                $this->db->where('kabupaten', $row->id);
                $sekolah = $this->db->get('sekolah')->row_array();

                return nl2br('SMA: ' . $sekolah['sma'] . '<br>SMK: ' . $sekolah['smk'] . '<br>SLB: ' . $sekolah['slb']);
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
            $this->breadcrumbs->push('Perbarui Data Sekolah', $this->base_breadcrumbs . '/perbarui_data_sekolah');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
            } elseif ($state === 'edit') {
            }

            $crud->unset_edit();
            $crud->unset_add();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_delete();

            $wilayah = explode(',', $this->kabupaten_id);

            $this->db->select('nama');
            $this->db->where_in('id', $wilayah);
            $wils = $this->db->get('wilayah_kabupaten');

            $namaWilayah = array();
            foreach ($wils->result() as $row) {
                $namaWilayah[] = $row->nama;
            }

            $implodedString = implode(', ', $namaWilayah);

            $extra      = array('page_title' => 'Kelola Sekolah');
            $keterangan = 'Wilayah yang dapat dikelola: <b>' . $implodedString . '</b>';

            $extra['keterangan'] = $keterangan;

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    //
    //     if (!empty($_POST)) {

    //         $config['upload_path']   = './uploads';
    //         $config['allowed_types'] = 'xls';
    //         $config['encrypt_name']  = true;

    //         $this->load->library('upload', $config);

    //         if (!$this->upload->do_upload('userfile')) {

    //             $this->alert->set('alert-danger', '<p class="text-danger">' . $this->upload->display_errors() . '</p>', true);
    //         } else {
    //             $extension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);

    //             if ($extension == 'xlsx') {
    //                 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx ();
    //             } else {
    //                 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls ();
    //             }

    //             // file path
    //             $spreadsheet    = $reader->load($_FILES['userfile']['tmp_name']);
    //             $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    //             $arrayCount = count($allDataInSheet);

    //             $j            = 0;
    //             $new_rows     = 0;
    //             $updated_rows = 0;

    //             // //baris data dimulai dari baris ke 2
    //             for ($i = 2; $i <= $arrayCount; ++$i) {

    //                 $npsn      = trim($allDataInSheet[$i]["A"]);
    //                 $level     = strtoupper(trim($allDataInSheet[$i]["B"]));
    //                 $status    = strtoupper(trim($allDataInSheet[$i]["C"]));
    //                 $nama      = strtoupper(trim($allDataInSheet[$i]["D"]));
    //                 $provinsi  = $this->provinsi_id;
    //                 $kabupaten = $this->_cek_wilayah('kabupaten', strtoupper(trim($allDataInSheet[$i]["E"])));
    //                 $kecamatan = $this->_cek_wilayah('kecamatan', strtoupper(trim($allDataInSheet[$i]["E"])), strtoupper(trim($allDataInSheet[$i]["F"])));
    //                 $kelurahan = $this->_cek_wilayah('kelurahan', strtoupper(trim($allDataInSheet[$i]["E"])), strtoupper(trim($allDataInSheet[$i]["F"])), strtoupper(trim($allDataInSheet[$i]["G"])));
    //                 $alamat    = trim($allDataInSheet[$i]["H"]);

    //                 if ($npsn === "") {
    //                     continue;
    //                 }

    //                 $r = $this->db->get_where('sekolah', array('npsn' => $npsn));

    //                 if ($r->num_rows() == 0) {
    //                     //hanya data baru aja yang dimasukkin
    //                     $in = array(
    //                         'npsn'      => $npsn,
    //                         'password'  => password_hash($npsn, PASSWORD_DEFAULT),
    //                         'level'     => $level,
    //                         'status'    => $status,
    //                         'nama'      => $nama,
    //                         'provinsi'  => $provinsi,
    //                         'kabupaten' => $kabupaten,
    //                         'kecamatan' => $kecamatan,
    //                         'kelurahan' => $kelurahan,
    //                         'alamat'    => $alamat,
    //                     );

    //                     $this->db->insert('sekolah', $in);
    //                     ++$new_rows;
    //                 } else {
    //                     //lets update
    //                     $up = array(
    //                         'level'     => $level,
    //                         'status'    => $status,
    //                         'nama'      => $nama,
    //                         'provinsi'  => $provinsi,
    //                         'kabupaten' => $kabupaten,
    //                         'kecamatan' => $kecamatan,
    //                         'kelurahan' => $kelurahan,
    //                         'alamat'    => $alamat,
    //                     );

    //                     $this->db->where('npsn', $npsn);
    //                     $this->db->update('sekolah', $up);

    //                     ++$updated_rows;
    //                 }

    //                 ++$j;
    //             }

    //             $success   = $this->upload->data();
    //             $file_name = $success['file_name'];

    //             @unlink('./uploads/' . $file_name);
    //             $this->alert->set('alert-success', 'Data berhasil dimasukkan dengan ' . $new_rows . ' Data baru ' . 'dan ' . $updated_rows . ' Data Lama / Data Update', true);
    //         }
    //     }

    //     $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
    //     $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
    //     $this->breadcrumbs->push('Import Data', $this->base_breadcrumbs . '/import-sekolah');

    //     $data['page_name']  = 'import_sekolah';
    //     $data['page_title'] = 'Import Data Sekolah';
    //     $this->_page_output($data);
    // }

    public function kehadiran_siswa()
    {
        $sekolah_id = $this->uri->segment(3);
        $qry        = $this->db->get_where('sekolah', array('id' => $sekolah_id));

        if ($qry->num_rows() == 0) {
            redirect(site_url('dinas/kelola-sekolah'), 'reload');
        }

        $sekolah = $qry->row_array();

        if (!empty($_POST)) {

            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $hari_aktif = _get_hari_aktif($tahun, $bulan);

            $keterangan = '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari<br/>';
            $keterangan .= 'Download laporan rekap untuk bulan terpilih <strong><a class="text-danger" href="' . site_url('dinas/download-rekap-presensi-siswa/' . $sekolah_id . '/' . $tahun . '/' . $bulan) . '">disini</a></strong>';
            $data['keterangan'] = $keterangan;

            // $this->db->select('a.id,
            //                    a.nisn,
            //                    a.nama_lengkap');
            // $this->db->join('kehadiran_siswa b', 'a.id = b.siswa_id  AND YEAR(b.tgl) = ' . $tahun . ' AND MONTH(b.tgl) = ' . $bulan, 'left');
            // $this->db->where('a.sekolah_id', $sekolah_id);
            // $this->db->where('a.aktif', 'YA');
            // $this->db->group_by('a.id');
            // $this->db->order_by('a.nama_lengkap ASC');
            // $data['presensi'] = $this->db->get('siswa a');

            $data['presensi'] = $this->db->query("SELECT a.id,
                                                         a.nisn,
                                                         a.nama_lengkap,
                                                         IFNULL(CONCAT(b.hadir),'-') AS hadir,
                                                         IFNULL(CONCAT(b.alpa),'-') AS alpa,
                                                         IFNULL(CONCAT(b.izin),'-') AS izin,
                                                         IFNULL(CONCAT(b.sakit),'-') AS sakit
                                                FROM siswa a
                                                LEFT JOIN (
                                                    SELECT tgl,
                                                           siswa_id,
                                                           `status`,
                                                             SUM(IF(status = 'HADIR',1,0)) AS hadir,
                                                             SUM(IF(status = 'ALPA',1,0)) AS alpa,
                                                             SUM(IF(status = 'IZIN',1,0)) AS izin,
                                                             SUM(IF(status = 'SAKIT',1,0)) AS sakit
                                                    FROM kehadiran_siswa a
                                                    GROUP BY a.siswa_id
                                                    ) b ON a.id = b.siswa_id AND YEAR(b.tgl) = '$tahun' AND MONTH(b.tgl) = '$bulan'
                                                WHERE a.sekolah_id = '$sekolah_id' AND a.aktif = 'YA'
                                                GROUP BY a.id
                                                ORDER BY b.hadir DESC, a.nama_lengkap ASC");
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Kehadiran Siswa', $this->base_breadcrumbs . '/kehadiran-siswa/' . $sekolah_id);

        $data['sekolah']    = $sekolah;
        $data['page_name']  = 'kehadiran_siswa';
        $data['page_title'] = 'Kehadiran Siswa <br/>' . $sekolah['nama'];
        $this->_page_output($data);
    }

    public function download_rekap_presensi_pegawai_dinas($filter_tahun, $filter_bulan)
    {

        $data = array();

        $cek = $this->db->get_where('wilayah_kabupaten', array('id' => $this->kabupaten_id))->row_array();

        $data['kabupaten'] = $cek['nama'];
        $data['logo']      = $this->logo;
        $data['header']    = 'DAFTAR HADIR PEGAWAI';

        //get kepala sekolah info

        $data['kepsek_nama']  = '-kepsek belum di set-';
        $data['kepsek_nuptk'] = '-kepsek belum di set-';

        $this->db->select("a.fulldate,a.day,
		                   IF(a.dayofweek = 7,'L',IF(COUNT(b.id) > 0,'L','A')) AS `status_hari`");
        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->group_by('a.fulldate');
        $tanggalan = $this->db->get_where('dates a', array('a.year' => $filter_tahun, 'a.month' => $filter_bulan));

        //get color for bobot 0
        $zero_color = $this->db->get_where('bobot', array('nilai' => 0))->row_array();

        $s_select = '';
        foreach ($tanggalan->result_array() as $t) {
            if ($t['status_hari'] === 'L') {
                $s_select .= '\'L|#FFFFFF\' AS `.' . $t['day'] . '`,';
            } else {
                $s_select .= 'IFNULL(CONCAT(b' . $t['day'] . '.nilai,\'|\',b' . $t['day'] . '.warna),\'0|' . $zero_color['warna'] . '\') AS `.' . $t['day'] . '`,';
            }
        }

        $data['jml_hari'] = $tanggalan->num_rows();

        $s_select = substr($s_select, 0, -1);

        $this->db->select("a.nuptk,
							a.nama_lengkap,
							a.jabatan," . $s_select . ",
							SUM(IF(b.status_masuk = 'IZIN-SAKIT',1,0)) AS S,
							SUM(IF(b.status_masuk = 'IZIN-TIDAK-MASUK',1,0)) AS I,
							SUM(IF(b.status_masuk = 'ALPA',1,0)) AS A,
							SUM(IF(b.status_masuk = 'NORMAL' OR b.status_masuk = 'TELAT' OR b.status_masuk = 'IZIN-TELAT' ,1,0)) AS JML", false);

        foreach ($tanggalan->result_array() as $t) {
            $this->db->join("(SELECT a.pegawai_id,b.nilai,b.warna
							 FROM kehadiran_pegawai a
							 LEFT JOIN bobot b ON a.status_masuk = b.status_masuk AND a.status_pulang = b.status_pulang
							 WHERE YEAR(a.jam_masuk) = $filter_tahun AND MONTH(a.jam_masuk) = $filter_bulan AND DAY(a.jam_masuk) = " . $t['day'] . "
							 GROUP BY a.pegawai_id) b" . $t['day'], "a.id = b" . $t['day'] . ".pegawai_id", "left");
        }

        $this->db->join("(SELECT a.pegawai_id,
						  		 a.status_masuk
						  FROM   kehadiran_pegawai a
						  WHERE  Year(a.jam_masuk) = $filter_tahun AND Month(a.jam_masuk) = $filter_bulan
						  GROUP  BY a.pegawai_id,DATE(a.jam_masuk)) b", 'a.id = b.pegawai_id', 'left');

        $this->db->where('a.dinas_provinsi', $this->provinsi_id);
        $this->db->where('a.dinas_kabupaten', $this->kabupaten_id);
        $this->db->where('a.aktif', 'YA');
        $this->db->group_by('a.id');

        $this->db->order_by('a.nama_lengkap ASC');
        $presensi = $this->db->get('pegawai a');

        Modules::run("export/pdf_rekap_presensi_pegawai_dinas", $data, $filter_tahun, bulan($filter_bulan), $presensi);
    }

    public function download_rekap_presensi_pegawai($sekolah_id, $filter_tahun, $filter_bulan)
    {

        $data = array();

        $this->db->select('a.id AS sekolah_id,
		                   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
		                   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $cek = $this->db->get_where('sekolah a', array('a.id' => $sekolah_id))->row_array();

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

        $this->db->select("a.fulldate,a.day,
		                   IF(a.dayofweek = 7,'L',IF(COUNT(b.id) > 0,'L','A')) AS `status_hari`");
        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->group_by('a.fulldate');
        $tanggalan = $this->db->get_where('dates a', array('a.year' => $filter_tahun, 'a.month' => $filter_bulan));

        //get color for bobot 0
        $zero_color = $this->db->get_where('bobot', array('nilai' => 0))->row_array();

        $s_select = '';
        foreach ($tanggalan->result_array() as $t) {
            if ($t['status_hari'] === 'L') {
                $s_select .= '\'L|#FFFFFF\' AS `.' . $t['day'] . '`,';
            } else {
                $s_select .= 'IFNULL(CONCAT(b' . $t['day'] . '.nilai,\'|\',b' . $t['day'] . '.warna),\'0|' . $zero_color['warna'] . '\') AS `.' . $t['day'] . '`,';
            }
        }

        $data['jml_hari'] = $tanggalan->num_rows();

        $s_select = substr($s_select, 0, -1);

        $this->db->select("a.nuptk,
							a.nama_lengkap,
							a.jabatan," . $s_select . ",
							SUM(IF(b.status_masuk = 'IZIN-SAKIT',1,0)) AS S,
							SUM(IF(b.status_masuk = 'IZIN-TIDAK-MASUK',1,0)) AS I,
							SUM(IF(b.status_masuk = 'ALPA',1,0)) AS A,
							SUM(IF(b.status_masuk = 'NORMAL' OR b.status_masuk = 'TELAT' OR b.status_masuk = 'IZIN-TELAT' ,1,0)) AS JML", false);

        foreach ($tanggalan->result_array() as $t) {
            $this->db->join("(SELECT a.pegawai_id,b.nilai,b.warna
							 FROM kehadiran_pegawai a
							 LEFT JOIN bobot b ON a.status_masuk = b.status_masuk AND a.status_pulang = b.status_pulang
							 WHERE YEAR(a.jam_masuk) = $filter_tahun AND MONTH(a.jam_masuk) = $filter_bulan AND DAY(a.jam_masuk) = " . $t['day'] . "
							 GROUP BY a.pegawai_id) b" . $t['day'], "a.id = b" . $t['day'] . ".pegawai_id", "left");
        }

        $this->db->join("(SELECT a.pegawai_id,
						  		 a.status_masuk
						  FROM   kehadiran_pegawai a
						  WHERE  Year(a.jam_masuk) = $filter_tahun AND Month(a.jam_masuk) = $filter_bulan
						  GROUP  BY a.pegawai_id,DATE(a.jam_masuk)) b", 'a.id = b.pegawai_id', 'left');

        $this->db->where('a.sekolah_id', $sekolah_id);
        $this->db->where('a.aktif', 'YA');
        $this->db->group_by('a.id');

        $this->db->order_by('a.nama_lengkap ASC');
        $presensi = $this->db->get('pegawai a');

        Modules::run("export/pdf_rekap_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
    }

    public function download_rekap_presensi_siswa($sekolah_id, $filter_tahun, $filter_bulan)
    {

        $cek = $this->db->get_where('sekolah', array('id' => $sekolah_id))->row_array();

        // $this->db->select('a.nuptk,
        //                    a.nama_lengkap,
        //                    a.jabatan,
        //                    IFNULL(SUM(c.nilai),0) AS nilai_presensi');
        // $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $filter_tahun . ' AND MONTH(b.jam_masuk) = ' . $filter_bulan, 'left');
        // $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
        // $this->db->where('a.sekolah_id', $sekolah_id);
        // $this->db->where('a.aktif', 'YA');
        // $this->db->group_by('a.id');
        // $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
        // $presensi = $this->db->get('pegawai a');

        $presensi = $this->db->query("SELECT a.nisn,
                                              a.nama_lengkap,
                                              IFNULL(CONCAT(b.hadir),'-') AS hadir,
                                              IFNULL(CONCAT(b.alpa),'-') AS alpa,
                                              IFNULL(CONCAT(b.izin),'-') AS izin,
                                              IFNULL(CONCAT(b.sakit),'-') AS sakit
                                    FROM siswa a
                                    LEFT JOIN (
                                        SELECT tgl,
                                               siswa_id,
                                               `status`,
                                                 SUM(IF(status = 'HADIR',1,0)) AS hadir,
                                                 SUM(IF(status = 'ALPA',1,0)) AS alpa,
                                                 SUM(IF(status = 'IZIN',1,0)) AS izin,
                                                 SUM(IF(status = 'SAKIT',1,0)) AS sakit
                                        FROM kehadiran_siswa a
                                        GROUP BY a.siswa_id
                                        ) b ON a.id = b.siswa_id AND YEAR(b.tgl) = '$filter_tahun' AND MONTH(b.tgl) = '$filter_bulan'
                                    WHERE a.sekolah_id = '$sekolah_id' AND a.aktif = 'YA'
                                    GROUP BY a.id
                                    ORDER BY b.hadir DESC, a.nama_lengkap ASC");

        $report_header = 'Laporan Presensi Siswa ' . $cek['nama'] . '&nbsp;(' . $cek['npsn'] . '&nbsp;)<br/>';
        $report_header .= 'Bulan ' . $filter_bulan . '&nbsp;Tahun ' . $filter_tahun;

        Modules::run("export/pdf", slugify('presensi-siswa-' . $cek['nama'] . '-' . $filter_tahun . '-' . $filter_bulan), $presensi, $report_header);
    }

    public function kehadiran_pegawai()
    {
        $sekolah_id = $this->uri->segment(3);

        $qry = $this->db->get_where('sekolah', array('id' => $sekolah_id));

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
            $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="' . site_url('dinas/download-rekap-presensi-pegawai/' . $sekolah_id . '/' . $tahun . '/' . $bulan) . '">disini</a></strong><br/>';
            $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('dinas/download-rekap-harian-pegawai' . '/' . $sekolah_id, $tahun, $bulan);

            $data['keterangan'] = $keterangan;

            $this->db->select('a.id,
                               a.nuptk,
                               a.nama_lengkap,
                               a.jabatan,
                               IFNULL(SUM(c.nilai),0) AS nilai_presensi');
            $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $tahun . ' AND MONTH(b.jam_masuk) = ' . $bulan, 'left');
            $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
            $this->db->where('a.sekolah_id', $sekolah_id);
            $this->db->where('a.aktif', 'Aktif');
            $this->db->group_by('a.id');
            $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
            $data['presensi'] = $this->db->get('pegawai a');
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Kehadiran Pegawai', $this->base_breadcrumbs . '/kehadiran-pegawai/' . $sekolah_id);

        $data['sekolah']    = $sekolah;
        $data['page_name']  = 'kehadiran_pegawai';
        $data['page_title'] = 'Kehadiran Pegawai <br/>' . $sekolah['nama'];
        $this->_page_output($data);
    }

    public function download_presensi_pegawai_dinas($pegawai_id, $filter_tahun, $filter_bulan)
    {

        $data = array();

        $this->db->select('a.nama_lengkap,
						   a.nuptk,
						   d.nama AS nama_kabupaten');
        $this->db->join('wilayah_kabupaten d', 'a.dinas_kabupaten = d.id', 'left');
        $this->db->where('a.id', $pegawai_id);
        $cek = $this->db->get('pegawai a')->row_array();

        $data['nama_pegawai'] = $cek['nama_lengkap'];
        $data['nuptk']        = $cek['nuptk'];
        $data['kabupaten']    = $cek['nama_kabupaten'];
        $data['logo']         = $this->logo;
        $data['header']       = 'DAFTAR HADIR PEGAWAI';

        $data['kepsek_nama']  = '-kepsek belum di set-';
        $data['kepsek_nuptk'] = '-kepsek belum di set-';

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

        Modules::run("export/pdf_presensi_pegawai_dinas", $data, $filter_tahun, bulan($filter_bulan), $presensi);
    }

    public function download_rekap_harian_pegawai_dinas($filter_tahun, $filter_bulan, $filter_hari)
    {

        $data = array();

        $cek = $this->db->get_where('wilayah_kabupaten', array('id' => $this->kabupaten_id))->row_array();

        $data['kabupaten'] = $cek['nama'];
        $data['logo']      = $this->logo;
        $data['header']    = 'DAFTAR HADIR PEGAWAI DINAS';

        //get kepala sekolah info

        $data['kepsek_nama']  = '-kepsek belum di set-';
        $data['kepsek_nuptk'] = '-kepsek belum di set-';

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
        $this->db->where("a.dinas_provinsi", $this->provinsi_id);
        $this->db->where("a.dinas_kabupaten", $this->kabupaten_id);
        $presensi = $this->db->get("pegawai a");

        Modules::run("export/pdf_rekap_harian_presensi_pegawai_dinas", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);
    }

    public function download_rekap_harian_pegawai($sekolah_id, $filter_tahun, $filter_bulan, $filter_hari, $ext = 'pdf')
    {

        $data = array();

        $this->db->select('a.id,
						   a.nama,
						   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
						   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $this->db->where('a.id', $sekolah_id);
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
            "a.nuptk,a.nama_lengkap,a.jabatan,
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
        $this->db->where("a.sekolah_id", $sekolah_id);

        // $presensi = $this->db->get("pegawai a");

        // Modules::run("export/pdf_rekap_harian_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);

        if ($ext === 'pdf') {
            $this->db->order_by("a.nama_lengkap ASC");
            $presensi = $this->db->get("pegawai a");

            Modules::run("export/pdf_rekap_harian_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);
        } else {
            $this->db->order_by("a.nama_lengkap DESC");
            $presensi = $this->db->get("pegawai a");

            Modules::run("export/xls_rekap_harian_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $filter_hari, $presensi);
        }
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

    public function download_presensi_siswa($siswa_id, $filter_tahun, $filter_bulan)
    {

        $cek = $this->db->get_where('siswa', array('id' => $siswa_id))->row_array();

        $this->db->select(
            "a.fulldate AS tanggal,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS `Status Hari`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status),'-'))) AS `Presensi`"
        );

        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->join("kehadiran_siswa c", "a.fulldate = DATE(c.tgl) AND c.siswa_id = $siswa_id", "left");
        $this->db->where("YEAR(a.fulldate)", $filter_tahun);
        $this->db->where("MONTH(a.fulldate)", $filter_bulan);
        $this->db->group_by("a.fulldate");
        $presensi = $this->db->get("dates a");

        $report_header = 'Laporan Presensi Siswa ' . $cek['nama_lengkap'] . '&nbsp;(' . $cek['nisn'] . '&nbsp;)<br/>';
        $report_header .= 'Bulan ' . $filter_bulan . '&nbsp;Tahun ' . $filter_tahun;

        Modules::run("export/pdf", slugify('presensi-siswa-' . $cek['nama_lengkap'] . '-' . $filter_tahun . '-' . $filter_bulan), $presensi, $report_header);

        // $this->load->module('export');
        // $this->export->pdf('test',$q);
    }

    public function detail_presensi_pegawai()
    {
        header('content-type: application/json');

        $data = array();

        $pegawai_id = $this->input->post('pegawai_id');
        $bulan      = $this->input->post('filter_bulan');
        $tahun      = $this->input->post('filter_tahun');

        $this->db->select(
            "a.fulldate,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS libur,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_masuk,'-'))) AS jam_masuk,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_pulang,'-'))) AS jam_pulang,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status_masuk,'-',c.status_pulang),'-'))) AS status,
            IF(a.dayofweek = 7,0,IF(COUNT(b.id) > 0,0,IFNULL(d.nilai,0))) AS nilai"
        );

        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->join("kehadiran_pegawai c", "a.fulldate = DATE(c.jam_masuk) AND c.pegawai_id = $pegawai_id", "left");
        $this->db->join("bobot d", "c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang", "left");
        $this->db->where("YEAR(a.fulldate)", $tahun);
        $this->db->where("MONTH(a.fulldate)", $bulan);
        $this->db->group_by("a.fulldate");
        $data['presensi'] = $this->db->get("dates a");

        echo json_encode(array('presensi' => $this->load->view('div_kehadiran_pegawai', $data, true)));
    }

    public function detail_presensi_siswa()
    {
        header('content-type: application/json');

        $data = array();

        $siswa_id = $this->input->post('siswa_id');
        $bulan    = $this->input->post('filter_bulan');
        $tahun    = $this->input->post('filter_tahun');

        $this->db->select(
            "a.fulldate,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS libur,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status),'-'))) AS status"
        );

        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->join("kehadiran_siswa c", "a.fulldate = DATE(c.tgl) AND c.siswa_id = $siswa_id", "left");
        $this->db->where("YEAR(a.fulldate)", $tahun);
        $this->db->where("MONTH(a.fulldate)", $bulan);
        $this->db->group_by("a.fulldate");
        $data['presensi'] = $this->db->get("dates a");

        echo json_encode(array('presensi' => $this->load->view('div_kehadiran_siswa', $data, true)));
    }

    // public function load_events()
    // {
    //     header('content-type: application/json');

    //     $pegawai_id = $this->input->post('pegawai_id');
    //     // $bulan      = $this->input->post('filter_bulan');
    //     // $tahun      = $this->input->post('filter_tahun');

    //     $data_weekend = array();

    //     $weekend = $this->db->get_where('dates', array('weekend' => 1));

    //     foreach ($weekend->result_array() as $row) {
    //         $data_weekend[] = array(
    //             "name"      => "Libur",
    //             "date"      => $row['fulldate'],
    //             "type"      => "holiday",
    //             "everyYear" => false,
    //         );
    //     }

    //     $data_libur = array();
    //     $libur      = $this->db->get('hari_libur');

    //     foreach ($libur->result_array() as $row) {
    //         $data_libur[] = array(
    //             "name"      => $row['keterangan'],
    //             "date"      => $row['tgl'],
    //             "type"      => "holiday",
    //             "everyYear" => false,
    //         );
    //     }

    //     $data_presensi = array();

    //     $this->db->select('status_masuk,status_pulang,DATE(tgl_update) AS tgl_update');
    //     $presensi = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $pegawai_id));

    //     foreach ($presensi->result_array() as $row) {
    //         $data_presensi[] = array(
    //             "name"      => "Masuk:" . $row['status_masuk'],
    //             "date"      => $row['tgl_update'],
    //             "type"      => strtolower($row['status_masuk']),
    //             "everyYear" => false,
    //         );

    //         $data_presensi[] = array(
    //             "name"      => "Pulang:" . $row['status_pulang'],
    //             "date"      => $row['tgl_update'],
    //             "type"      => strtolower($row['status_pulang']),
    //             "everyYear" => false,
    //         );
    //     }

    //     echo json_encode(array('events' => array_merge($data_weekend, $data_libur, $data_presensi)));

    // $data = array();
    // $this->db->select('a.fulldate AS tgl,
    //                    b.jam_masuk AS jam_masuk,
    //                    b.jam_pulang, b.status_masuk,
    //                    b.status_pulang,
    //                    c.nilai AS nilai_presensi');
    // $this->db->join('kehadiran_pegawai b', 'a.fulldate = DATE(b.jam_masuk) AND  b.pegawai_id = ' . $pegawai_id, 'left');
    // $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
    // $this->db->where('YEAR(a.fulldate)', $tahun);
    // $this->db->where('MONTH(a.fulldate)', $bulan);
    // $this->db->order_by('a.fulldate');

    // $presensi = $this->db->get('dates a');

    // $weekend = $this->db->get_where('dates',
    //     array(
    //         'weekend'           => 1,
    //         'YEAR(a.fulldate)'  => $tahun,
    //         'MONTH(a.fulldate)' => $bulan,
    //     )
    // );

    // $data_weekend = array();
    // foreach ($weekend->result_array() as $row) {
    //     $data_weekend[] = array(
    //         "date"      => $row['fulldate']
    //     );
    // }

    // $data_presensi = array();
    // foreach ($presensi->result_array() as $key) {
    //     $data_presensi[] = array(

    //     );
    // }

    // echo json_encode(array('presensi' => $this->load->view('div_kehadiran_pegawai', $data, true)));

    // }
    
    // Update 03 Februari 2025 nambah field export data siswa
   // public function export_siswa($npsn)
    //{
      //  $this->db->select('a.nisn,a.nik,a.nama_lengkap,
        //                   c.nama as Kabupaten,
          //                 d.nama AS kecamatan,
            //               e.nama AS kelurahan,
              //             a.tempat_lahir,
    //                       a.tanggal_lahir,
    //                      a.nama_ayah,
    //                       a.nama_ibu_kandung,
    //                       a.nama_rombel,
    //                       a.jk,
    //                       a.alamat');
    //    $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
    //    $this->db->join('wilayah_kabupaten c', 'a.kabupaten = c.id', 'left');
    //    $this->db->join('wilayah_kecamatan d', 'a.kecamatan = d.id', 'left');
    //   $this->db->join('wilayah_kelurahan e', 'a.kelurahan = e.id', 'left');
        

    //    $this->db->where('b.npsn', $npsn);
    //    $this->db->order_by('a.nama_lengkap', 'asc');
    //    $q = $this->db->get('siswa a');

    //    Modules::run("export/excel", 'data-siswa-' . $npsn, $q);
    //}
    
    // Update Data Alumni SMA 30 Mei 2025

    public function export_siswa($npsn)
    {
        $this->db->select('a.nama_lengkap,
                           a.tanggal_keluar AS tanggal lulus SNBT SNPMB,
                           a.jurusan,
                           a.perguruan_tinggi');
        $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'a.kabupaten = c.id', 'left');
        $this->db->join('wilayah_kecamatan d', 'a.kecamatan = d.id', 'left');
        $this->db->join('wilayah_kelurahan e', 'a.kelurahan = e.id', 'left');
        

        $this->db->where('b.npsn', $npsn);
        $this->db->order_by('a.nama_lengkap', 'asc');
        $q = $this->db->get('siswa a');

        Modules::run("export/excel", 'data-siswa-' . $npsn, $q);
    }

    

    public function export_pegawai($npsn)
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

        Modules::run("export/excel", 'data-pegawai-' . $npsn, $q);
    }

    public function export_data($tipe, $url)
    {
        $url      = base64url_decode($url);
        $filetype = explode('-', $url)[0];
        $npsn     = explode('-', $url)[1];
        $this->db->select('a.nip AS NIP,
                           a.status_pegawai AS STATUS,
                           a.nama_lengkap AS NAMA,
                           a.gelar_depan AS GLRDEPAN,
                           a.gelar_belakang AS GRLBELAKANG,
                           IF(a.jk = "L","1","2") AS KDJENKEL,
                           a.tempat_lahir AS TEMPATLHR,
                           a.tgl_lahir AS TGLLHR,
                           a.jml_istri AS JISTRI_SUAMI,
                           a.jml_anak AS JANAK,
                           a.kode_satker AS KDSATKER,
                           a.no_rek AS NO_REK,
                           a.npwp AS NPWP,
                           a.telp AS NO_HP,
                           a.alamat AS ALAMAT,
                           CASE 
                              WHEN COUNT(dp.id) = (SELECT COUNT(*) FROM jenis_dokumen) THEN "lengkap"
                              ELSE COUNT(dp.id)
                           END AS dokumen');
        $this->db->join('sekolah b', 'a.sekolah_id = b.id', 'left');
        $this->db->join('dokumen_pegawai dp', 'a.id = dp.pegawai_id', 'left');
        $this->db->where('b.npsn', $npsn);

        if ($tipe == 'pppk') {
            $this->db->where('a.status_pegawai', 'PPPK');
        } elseif ($tipe == 'pns') {
            $this->db->where('a.status_pegawai', 'PNS');
        }else{
            $tipe = 'semua';
        }

        $this->db->group_by('a.nip,
                         a.status_pegawai,
                         a.nama_lengkap,
                         a.gelar_depan,
                         a.gelar_belakang,
                         a.jk,
                         a.tempat_lahir,
                         a.tgl_lahir,
                         a.jml_istri,
                         a.jml_anak,
                         a.kode_satker,
                         a.no_rek,
                         a.npwp,
                         a.telp,
                         a.alamat');

        $this->db->order_by('a.nama_lengkap', 'asc');
        $q = $this->db->get('pegawai a');

        if ($filetype === 'excel') {
            Modules::run("export/excel", $tipe . '-' . $npsn, $q);
        } else {
            Modules::run("export/pdf", $tipe . '-' . $npsn, $q);
        }
    }

    // public function export_all_pegawai()
    // {
    //     $requestData['job']['url'] = site_url('cronjob/exportAllPegawai');
    //     $requestData['job']['enabled'] = true;
    //     $requestData['job']['saveResponses'] = true;
    //     $requestData['job']['schedule'] = [
    //         'timezone' => 'Asia/Jakarta',
    //         'expiresAt' => 0,
    //         'hours' => [-1],
    //         'mdays' => [-1],
    //         'minutes' => [-1],
    //         'months' => [-1],
    //         'wdays' => [-1]
    //     ];

    //     create_cronjob($requestData);

    //     // $this->session->set_userdata('export_pegawai', 'Y');
    //     create_file(FCPATH . 'temp/export/pegawai/sedang.proses','proses...');

    //     $this->alert->set('alert-info', 'Proses export data akan dimulai dalam 1 menit...<br/>Proses ini memakan waktu lama. Harap tunggu...<br/>Refresh halaman secara berkala');
    //     redirect(site_url('dinas/kelola-sekolah'), 'reload');
    // }

    // public function export_all_siswa()
    // {
    //     $requestData['job']['url'] = site_url('cronjob/exportAllSiswa');
    //     $requestData['job']['enabled'] = true;
    //     $requestData['job']['saveResponses'] = true;
    //     $requestData['job']['schedule'] = [
    //         'timezone' => 'Asia/Jakarta',
    //         'expiresAt' => 0,
    //         'hours' => [-1],
    //         'mdays' => [-1],
    //         'minutes' => [-1],
    //         'months' => [-1],
    //         'wdays' => [-1]
    //     ];

    //     create_cronjob($requestData);

    //     // $this->session->set_userdata('export_siswa', 'Y');
    //     create_file(FCPATH . 'temp/export/siswa/sedang.proses','proses...');

    //     $this->alert->set('alert-info', 'Proses export data akan dimulai dalam 1 menit...<br/>Proses ini memakan waktu lama. Harap tunggu...<br/>Refresh halaman secara berkala');
    //     redirect(site_url('dinas/kelola-sekolah'), 'reload');
    // }

    public function perbarui_data_siswa_pegawai()
    {
        $this->db->where('cron_update_data', 'Y');
        $q = $this->db->get('sekolah');

        reset_cronjob(site_url('cronjob/getPesertaDidikAndPegawai'));
        if ($q->num_rows() == 0) {
            $this->db->update('sekolah', array('cron_update_data' => 'Y'));
        }

        redirect(site_url('dinas/kelola-sekolah'), 'reload');
    }

    // private function _cek_export_pegawai()
    // {
    //     // Mendapatkan path folder
    //     $path = FCPATH . 'temp/export/siswa/';

    //     // Membuka direktori
    //     $dir = opendir($path);

    //     // Mengecek file dengan ekstensi .xlsx
    //     $found = false;
    //     while (($file = readdir($dir)) !== false) {
    //         // Mendapatkan ekstensi file
    //         $ext = pathinfo($file, PATHINFO_EXTENSION);

    //         // Jika file .xlsx ditemukan, ubah flag menjadi true
    //         if ($ext === 'xlsx') {
    //             $found = true;
    //             break;
    //         }
    //     }

    //     // Menutup direktori
    //     closedir($dir);

    //     // Mengembalikan nilai true jika file .xlsx ditemukan
    //     return $found ? 'true' : 'false';
    // }

    public function kelola_sekolah()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('sekolah');
            $crud->set_subject('Sekolah');
            $crud->where_in('kabupaten', explode(',', $this->kabupaten_id ?? ''));

            if (in_array($this->user_level, array('DINAS-SMA', 'DINAS-SMK', 'DINAS-SLB'))) {

                $parts  = explode("-", $this->user_level);
                $result = array_pop($parts);

                $crud->where('level', $result);
            }

            $crud->required_fields('npsn', 'level', 'nama', 'status');

            $crud->columns('npsn', 'level', 'nama', 'status',  'alamat', 'pegawai', 'siswa', /*'kehadiran',*//*'token',*/ 'password');
            $crud->field_type('id', 'hidden');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('latitude', 'hidden');
            $crud->field_type('longitude', 'hidden');
            $crud->field_type('map', 'hidden');
            $crud->field_type('akses_internet', 'readonly');
            $crud->field_type('akses_internet_2', 'readonly');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('provinsi', 'hidden', $this->provinsi_id);
            $crud->field_type('kabupaten', 'hidden', $this->kabupaten_id);

            $crud->display_as('nama', 'Nama');
            $crud->display_as('npsn', 'NPSN');
            // $crud->display_as('pegawai_siswa', 'Export data');
            $crud->display_as('pegawai', 'Pegawai');

            $crud->set_field_upload('logo', 'uploads');

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
            // $crud->set_relation('kabupaten', 'wilayah_kabupaten', 'nama', array('provinsi_id' => $this->provinsi_id));
            $crud->set_relation('kecamatan', 'wilayah_kecamatan', 'nama', array('kabupaten_id' => $this->kabupaten_id));
            $crud->set_relation('kelurahan', 'wilayah_kelurahan', 'nama');

            // $crud->callback_column('statistik', function ($value, $row) {

            //     return '<a href="' . site_url('dinas/pegawai/' . $row->id) . '">' . $pegawai->num_rows() .'&nbsp;Pegawai</a>&nbsp; - &nbsp;<a href="' . site_url('dinas/siswa/' . $row->id) . '">'. $siswa->num_rows() .'&nbsp;Siswa</a>';
            // });

            // $crud->callback_column('kehadiran', function ($value, $row) {
            //     $pegawai = $this->db->get_where('pegawai', array('sekolah_id' => $row->id, 'aktif' => 'Aktif'));
            //     $siswa   = $this->db->get_where('siswa', array('sekolah_id' => $row->id, 'aktif' => 'Ya'));

            //     return '[<a href="' . site_url('dinas/kehadiran-pegawai/' . $row->id) . '">' . $pegawai->num_rows() . '&nbsp;Pegawai</a>]&nbsp;-&nbsp;[<a href="' . site_url('dinas/kehadiran-siswa/' . $row->id) . '">' . $siswa->num_rows() . '&nbsp;Siswa</a>]';
            // });

            $crud->callback_column('pegawai', function ($value, $row) {

                $link = '<div class="center-cell">';
                $link .= '<a href="' . site_url('dinas/export-pegawai/' . $row->npsn) . '" title="Ekspor Data"><i class="fas fa-download"></i></a>';
                $link .= '&nbsp;&nbsp;';
                $link .= '<a href="' . site_url('dinas/detail-pegawai/' . $row->npsn) . '" title="Lihat Detail"><i class="fas fa-eye"></i></a>';
                $link .= '&nbsp;&nbsp;';
                $link .= '<a href="' . site_url('dinas/update-pegawai/' . $row->npsn) . '" title="Perbarui Data"><i class="fas fa-sync"></i></a>';
                $link .= '<br>' . relative_time($row->tgl_update_pegawai);
                $link .= '</div>';


                return $link;
            });
            // update 30 Mei 2025
            $crud->callback_column('siswa', function ($value, $row) {

                $link = '<div class="center-cell">';
                $link .=  '<a href="' . site_url('dinas/export-siswa/' . $row->npsn) . '" title="Ekspor Data"><i class="fas fa-download"></i></a>';
                $link .= '&nbsp;&nbsp;';
                // $link .= '<a href="' . site_url('dinas/update-siswa/' . $row->npsn) . '" title="Perbarui Data"><i class="fas fa-sync"></i></a>';
                
                $link .= '<br>' . relative_time($row->tgl_update_siswa);
                $link .= '</div>';

                return $link;
            });

            // $crud->callback_column('alamat', function ($value, $row) {

            //     $kelurahan = $this->db->get_where('wilayah_kelurahan',array('id' => $row->kelurahan));
            //     if($kelurahan->num_rows() > 0){
            //         $kel = $kelurahan->row_array();
            //         return $row->alamat . ' Kel.' . $kel['nama'];
            //     }else{
            //         return $row->alamat;
            //     }

            // });

            $crud->callback_column('token', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset token untuk operator sekolah ini?\');" class="text-danger" href="' . site_url('dinas/reset-token-operator/' . $row->id) . '">RESET</a>';
            });


            $crud->callback_column('password', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset password untuk operator ini?\');" class="text-danger" href="' . site_url('dinas/reset-password-operator/' . $row->id) . '">RESET</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
                $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            }

            $crud->unset_edit();
            $crud->unset_add();
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_export();
            $crud->unset_delete();

            $this->load->library('Gc_Dependent_Select');

            $fields = array(
                // first field:
                // 'provinsi'  => array( // first dropdown name
                //     'table_name'       => 'wilayah_provinsi', // table of country
                //     'title'            => 'nama', // country title
                //     'relate'           => null, // the first dropdown hasn't a relation
                //     'data-placeholder' => 'Pilih Provinsi', //dropdown's data-placeholder:
                // ),
                // second field
                // 'kabupaten' => array( // second dropdown name
                //     'table_name'       => 'wilayah_kabupaten', // table of state
                //     'title'            => 'nama', // state title
                //     'id_field'         => 'id', // table of state: primary key
                //     'relate'           => null, // table of state:
                //     'data-placeholder' => 'Pilih Kabupaten', //dropdown's data-placeholder:
                // ),
                // third field. same settings
                'kecamatan' => array(
                    'table_name'       => 'wilayah_kecamatan',
                    'title'            => 'nama', // now you can use this format )))
                    'id_field' => 'id',
                    'relate'           => null,
                    'data-placeholder' => 'Pilih Kecamatan',
                ),
                'kelurahan' => array(
                    'table_name'       => 'wilayah_kelurahan',
                    'title'            => 'nama', // now you can use this format )))
                    'id_field' => 'id',
                    'relate'           => 'kecamatan_id',
                    'data-placeholder' => 'Pilih Kelurahan',
                ),
            );

            $config = array(
                'main_table'         => 'sekolah',
                'main_table_primary' => 'id',
                "url"                => site_url('/dinas/kelola-sekolah/'),
                'ajax_loader'        => base_url() . 'assets/ajax-loader.gif',
            );

            $wilayah = new Gc_dependent_select($crud, $fields, $config);
            $js      = $wilayah->get_js();

            $extra      = array('page_title' => 'Kelola Sekolah');
            //$keterangan = '<br/>* Anda dapat memperbarui data sekolah. Klik di <a class="text-danger" href="' . site_url('dinas/perbarui-data-sekolah') . '">SINI</a>&nbsp;untuk melakukannya';
            $keterangan = '<br/>* Anda dapat memperbarui data sekolah. Klik di <a class="text-danger" href="' . site_url('dinas/perbarui-data-sekolah') . '">SINI</a>&nbsp;untuk melakukannya';

            $q_total = $this->db->get('sekolah');

            $this->db->where('cron_update_data', 'Y');
            $q = $this->db->get('sekolah');

            if ($q->num_rows() > 0) {
                $keterangan .= '<br/>* Update data siswa & pegawai: Progress update data ' . ($q_total->num_rows() - $q->num_rows()) . ' dari ' . $q_total->num_rows() . ' total sekolah - Tekan F5 untuk memperbarui';
                $keterangan .= '&nbsp;(Jika progress update data tidak berubah dalam waktu 5 menit, Klik <a class="text-danger" href="' . site_url('dinas/perbarui-data-siswa-pegawai') . '">RESET UPDATE</a>)';
            //} else {
               // $keterangan .= '<br/>* Update data siswa & pegawai: Klik di <a class="text-danger" href="' . site_url('dinas/perbarui-data-siswa-pegawai') . '">SINI</a>&nbsp;untuk melakukannya';
              }else {
                $keterangan .= '<br/>* Update data siswa & pegawai: Klik di <a class="text-danger" href="#">SINI</a>&nbsp;untuk melakukannya';
            }

            // if (file_exists(FCPATH . 'temp/export/siswa/sedang.proses')) {
            //     $keterangan .= '<br/>* Export data siswa : dalam proses ... <img src="' . base_url() . 'uploads/loading.gif" width="30">';
            // } else {
            //     // Mendapatkan path folder
            //     $path = FCPATH . 'temp/export/siswa/';

            //     // Mendapatkan informasi file zip terbaru
            //     $latest_zip = get_latest_zip_file($path);

            //     if($latest_zip['file'] != '-'){
            //         $keterangan .= '<br/>* Export data siswa : klik di <a class="text-danger" href="' . site_url('dinas/export-all-siswa') . '">sini</a>&nbsp;untuk memulai (file export terakhir dibuat tanggal : ' . date('d-m-Y H:i:s', $latest_zip['mtime']) .' download di <a class="text-danger" href="' . site_url('temp/export/siswa/' . $latest_zip['file']) . '">sini</a>)';
            //     }else{
            //         $keterangan .= '<br/>* Export data siswa : klik di <a class="text-danger" href="' . site_url('dinas/export-all-siswa') . '">sini</a>&nbsp;untuk memulai';
            //     }

            // }

            // if (file_exists(FCPATH . 'temp/export/pegawai/sedang.proses')) {
            //     $keterangan .= '<br/>* Export data pegawai : dalam proses ... <img src="' . base_url() . 'uploads/loading.gif" width="30">';
            // } else {
            //     $path = FCPATH . 'temp/export/pegawai/';

            //     // Mendapatkan informasi file zip terbaru
            //     $latest_zip = get_latest_zip_file($path);

            //     if($latest_zip['file'] != '-'){
            //         $keterangan .= '<br/>* Export data pegawai : klik di <a class="text-danger" href="' . site_url('dinas/export-all-pegawai') . '">sini</a>&nbsp;untuk memulai (file export terakhir dibuat tanggal : ' . date('d-m-Y H:i:s', $latest_zip['mtime']) .' download di <a class="text-danger" href="' . site_url('temp/export/pegawai/' . $latest_zip['file']) . '">sini</a>)';
            //     }else{
            //         $keterangan .= '<br/>* Export data pegawai : klik di <a class="text-danger" href="' . site_url('dinas/export-all-pegawai') . '">sini</a>&nbsp;untuk memulai';
            //     }
            // }

            // $keterangan .= '<br/>* Reset token digunakan jika operator mengganti perangkat untuk login ';

            $extra['keterangan'] = $keterangan;
            $output              = $crud->render();
            $output->output .= $js;

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }


    public function update_pegawai($npsn)
    {
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        _getPtk($token, $npsn);
        $this->alert->set('alert-success', 'Data pegawai berhasil diperbarui');

        redirect(site_url('dinas/kelola-sekolah'), 'reload');
    }

    public function update_siswa($npsn)
    {
        $decodedToken = json_decode(getToken(), true);
        $token        = $decodedToken['return']['token'];

        _getPesertaDidik($token, $npsn);
        $this->alert->set('alert-success', 'Data peserta didik berhasil diperbarui');

        redirect(site_url('dinas/kelola-sekolah'), 'reload');
    }

    public function detail_pegawai()
    {
        $slug = $this->uri->segment(3);

        $this->breadcrumbs->push('Beranda', '/dinas');
        $this->breadcrumbs->push('Kelola Sekolah', '/dinas/kelola-sekolah');
        $this->breadcrumbs->push('Detail Pegawai', '/dinas/detail-pegawai/' . $slug);

        $data['page_name']  = 'pegawai_ajax';
        $data['page_title'] = 'Data Pegawai';

        $this->_page_output($data);
    }

    public function proses_izin_kepsek($izin_id, $status_izin)
    {

        $status_izin = strtoupper($status_izin);

        $this->db->where('id', $izin_id);
        $this->db->update('izin_pegawai', array('status_izin' => $status_izin));

        $cek = $this->db->get_where('izin_pegawai', array('id' => $izin_id));

        if ($cek->num_rows() > 0) {
            $izin = $cek->row_array();

            $jenis_izin = $izin['jenis_izin'];
            $pegawai_id = $izin['pegawai_id'];
            $tgl_izin   = $izin['tgl_izin'];

            if ($status_izin === 'TERIMA') {

                $cek_kehadiran = $this->db->get_where(
                    'kehadiran_pegawai',
                    array(
                        'pegawai_id'      => $pegawai_id,
                        'DATE(jam_masuk)' => $tgl_izin,
                    )
                );

                $status_kehadiran = ($cek_kehadiran->num_rows() > 0) ? 'update' : 'insert';

                if ($jenis_izin === 'MASUK-TELAT') {

                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update('kehadiran_pegawai', array('status_masuk' => 'IZIN-TELAT'));
                    } else {
                        $this->db->insert(
                            'kehadiran_pegawai',
                            array(
                                'pegawai_id'   => $pegawai_id,
                                'jam_masuk'    => date('Y-m-d') . ' 07:00:00',
                                'status_masuk' => 'IZIN-TELAT',
                            )
                        );
                    }
                } elseif ($jenis_izin === 'PULANG-CEPAT') {

                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update('kehadiran_pegawai', array('status_pulang' => 'IZIN-CEPAT'));
                    } else {
                        $this->db->insert(
                            'kehadiran_pegawai',
                            array(
                                'pegawai_id'    => $pegawai_id,
                                'jam_pulang'    => date('Y-m-d') . ' 07:00:00',
                                'status_pulang' => 'IZIN-CEPAT',
                            )
                        );
                    }
                } elseif ($jenis_izin === 'IZIN-TIDAK-MASUK') {

                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update(
                            'kehadiran_pegawai',
                            array(
                                'status_masuk'  => 'IZIN-TIDAK-MASUK',
                                'status_pulang' => 'IZIN-TIDAK-MASUK',
                            )
                        );
                    } else {
                        $this->db->insert(
                            'kehadiran_pegawai',
                            array(
                                'pegawai_id'    => $pegawai_id,
                                'jam_masuk'     => date('Y-m-d') . ' 07:00:00',
                                'jam_pulang'    => date('Y-m-d') . ' 12:00:00',
                                'status_masuk'  => 'IZIN-TIDAK-MASUK',
                                'status_pulang' => 'IZIN-TIDAK-MASUK',
                            )
                        );
                    }
                } elseif ($jenis_izin === 'IZIN-SAKIT') {

                    if ($status_kehadiran === 'update') {

                        $kehadiran = $cek_kehadiran->row_array();
                        $this->db->where('id', $kehadiran['id']);
                        $this->db->update(
                            'kehadiran_pegawai',
                            array(
                                'status_masuk'   => 'IZIN-SAKIT',
                                'status_pulang'  => 'IZIN-SAKIT',
                                'pengajuan_izin' => 'IZIN-SAKIT',
                            )
                        );
                    } else {
                        $this->db->insert(
                            'kehadiran_pegawai',
                            array(
                                'pegawai_id'    => $pegawai_id,
                                'jam_masuk'     => date('Y-m-d') . ' 07:00:00',
                                'jam_pulang'    => date('Y-m-d') . ' 12:00:00',
                                'status_masuk'  => 'IZIN-SAKIT',
                                'status_pulang' => 'IZIN-SAKIT',
                            )
                        );
                    }
                }
            }
        }

        redirect(site_url('dinas/izin_kepsek'), 'reload');
    }

    public function izin_kepsek()
    {

        $data = array();

        $this->db->select('a.id,
		                  b.nuptk,
		                  b.nama_lengkap,
						  c.nama AS sekolah,
						  a.tgl_pengajuan, a.tgl_izin,
                          a.jenis_izin,
						  a.keterangan,
						  a.`file`,
						  a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id AND b.jabatan = "KEPALA"', 'left');
        $this->db->join('sekolah c', 'b.sekolah_id = c.id', 'left');
        $this->db->where('a.status_izin = "PENDING" AND c.provinsi = ' . $this->provinsi_id);

        $izin_kepsek = $this->db->get('izin_pegawai a');

        // $wilayah = explode(',', $this->kabupaten_id);

        //     $this->db->select('nama');
        //     $this->db->where_in('id', $wilayah);
        //     $wils = $this->db->get('wilayah_kabupaten');

        $this->db->select('a.id,
		                  b.nuptk,
		                  b.nama_lengkap,
						  b.dinas_unit_kerja AS sekolah,
						  a.tgl_pengajuan,
						  a.tgl_izin,
                          a.jenis_izin,
						  a.keterangan,
						  a.`file`,
						  a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');
        $this->db->where('a.status_izin', "PENDING");
        $this->db->where('b.dinas_provinsi', $this->provinsi_id);
        $this->db->where_in('b.dinas_kabupaten', explode(',', $this->kabupaten_id));

        $izin_dinas = $this->db->get('izin_pegawai a');

        $results = array();
        if ($izin_kepsek->num_rows()) {
            $results = array_merge($results, $izin_kepsek->result());
        }

        if ($izin_dinas->num_rows()) {
            $results = array_merge($results, $izin_dinas->result());
        }

        $data['izin'] = $results;

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Permohonan Izin', $this->base_breadcrumbs . '/izin_kepsek');

        $data['keterangan'] = 'Berikut ini adalah data permohonan izin';
        $data['page_name']  = 'izin_kepsek';
        $data['page_title'] = 'Permohonan Izin';

        $this->_page_output($data);
    }

    public function reset_token_operator($sekolah_id)
    {
        $this->db->set('token_login', 'NULL', false);
        $this->db->where('id', $sekolah_id);
        $this->db->update('sekolah');

        $this->alert->set('alert-success', 'Token berhasil direset');

        redirect(site_url('dinas/kelola-sekolah'), 'reload');
    }

    public function reset_password_operator($sekolah_id)
    {

        $cek = $this->db->get_where('sekolah', array('id' => $sekolah_id))->row_array();

        $npsn_pass = password_hash($cek['npsn'], PASSWORD_DEFAULT);

        $this->db->set('password', "'" . $npsn_pass . "'", false);
        $this->db->where('id', $sekolah_id);
        $this->db->update('sekolah');

        $this->alert->set('alert-success', 'Password berhasil direset dan diganti menjadi NPSN');

        redirect(site_url('dinas/kelola-sekolah'), 'reload');
    }

    public function ganti_password()
    {
        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('user', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('dinas/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('dinas/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('user', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('dinas/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('dinas/ganti-password'), 'reload');
                }
            }
        }

        $data['page_name'] = 'ganti_password';

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Ganti Password', $this->base_breadcrumbs . '/ganti_password');

        $data['page_title'] = 'Ganti Password';

        $this->_page_output($data);
    }

    public function getSekolah($kodeKabupaten)
    {
        // header('content-type: application/json');
        //dapatkan token
        $decodedToken = json_decode(getToken(), true);
        
       
        $token        = $decodedToken['return']['token'];

        $this->db->where('kabupaten_id', $kodeKabupaten);
        $kecamatan = $this->db->get('wilayah_kecamatan');

        foreach ($kecamatan->result_array() as $row) {
            $kode_kecamatan        = $row['id'];
            $jsonDataSekolah       = getSekolah($token, $kode_kecamatan);
            $decodedOutputFilePath = json_decode($jsonDataSekolah, true);

            if (isset($decodedOutputFilePath['error'])) {
                // Handle the error
                continue; // Skip to the next iteration
            }

            $outputPathFile = $decodedOutputFilePath['return']['outputFilePath'];

            $jsonData = file_get_contents($outputPathFile);

            $data = json_decode($jsonData, true);

            $arrBentukPendidikan = array('SMA', 'SMK', 'SLB');

            foreach ($data as $item) {
                $sekolahId         = $item['sekolah_id'];
                $bentukPendidikan  = $item['bentuk_pendidikan'];
                $statusSekolah     = $item['status_sekolah'];
                $npsn              = $item['npsn'];
                $password          = 'INITIAL_PASSWORD';
                $nama              = $item['nama'];
                $email             = $item['email'];
                $nomor_telepon     = $item['nomor_telepon'];
                $kodeProvinsi      = $item['kode_provinsi'];
                $kodeKabupaten     = $item['kode_kabupaten'];
                $kodeKecamatan     = $item['kode_kecamatan'];
                $kodeDesaKelurahan = $item['kode_desa_kelurahan'];
                $alamatJalan       = $item['alamat_jalan'];
                $lintang           = $item['lintang'];
                $bujur             = $item['bujur'];
                $akses_internet    = $item['akses_internet'];
                $akses_internet_2  = $item['akses_internet_2'];

                //nambah informasi akses internet 20 Januari 2025
                if (in_array($bentukPendidikan, $arrBentukPendidikan)) {

                    $set = array(
                        'id'         => $sekolahId,
                        'level'      => $bentukPendidikan,
                        'status'     => $statusSekolah,
                        'npsn'       => $npsn,
                        'password'   => $password,
                        'nama'       => $nama,
                        'email'      => $email,
                        'nomor_telp' => $nomor_telepon,
                        'provinsi'   => $kodeProvinsi,
                        'kabupaten'  => $kodeKabupaten,
                        'kecamatan'  => $kodeKecamatan,
                        'kelurahan'  => $kodeDesaKelurahan,
                        'alamat'     => $alamatJalan,
                        'akses_internet'=>$akses_internet,
                        'akses_internet_2'=>$akses_internet_2,
                        'latitude'   => $lintang,
                        'longitude'  => $bujur,
                        'tgl_update' => date('Y-m-d H:i:s'),
                    );

                    $exclude_columns = array('password', 'latitude', 'longitude');
                    $this->db->on_duplicate('sekolah', $set, $exclude_columns);
                }
            }
        }

        // echo json_encode(array('status' => 'success'));
        $this->alert->set('alert-success', 'Data berhasil diperbarui');
        redirect(site_url('dinas/perbarui-data-sekolah'), 'reload');
    }

    public function export_all_siswa()
    {
        $path = FCPATH . 'temp/export/siswa/';
        zipFolder($path, 'data-siswa');

        $this->load->helper('download');
        $file = file_get_contents($path . 'data-siswa.zip'); // Read the file's contents

        @force_download('data-siswa.zip', $file);
    }

    public function export_all_pegawai()
    {
        $path = FCPATH . 'temp/export/pegawai/';
        zipFolder($path, 'data-pegawai');

        $this->load->helper('download');
        $file = file_get_contents($path . 'data-pegawai.zip'); // Read the file's contents

        @force_download('data-pegawai.zip', $file);
    }

    public function updateStatusNISN()
    {
        $this->db->query("
            UPDATE verval_data_siswa vds
            INNER JOIN siswa s ON UPPER(REPLACE(REPLACE(vds.nama_lengkap,'.',''),' ','')) = UPPER(REPLACE(REPLACE(s.nama_lengkap,'.',''),' ',''))
            INNER JOIN sekolah sc ON s.sekolah_id = sc.id
            SET  vds.status_nisn = 'SESUAI',
                 vds.nama_ayah = s.nama_ayah,
                 vds.nama_ibu_kandung = s.nama_ibu_kandung,
                 vds.nama_rombel = s.nama_rombel,
                 vds.nama_lengkap_dapodik = s.nama_lengkap
            WHERE vds.nisn = s.nisn
                  AND REPLACE(vds.nama_sekolah,' ','') = REPLACE(sc.nama,' ','')
        ");
    }

    public function updateStatusNIK()
    {
        $this->db->query("
            UPDATE verval_data_siswa vds
            INNER JOIN siswa s ON UPPER(REPLACE(REPLACE(vds.nama_lengkap,'.',''),' ','')) = UPPER(REPLACE(REPLACE(s.nama_lengkap,'.',''),' ',''))
            INNER JOIN sekolah sc ON s.sekolah_id = sc.id
            SET vds.status_nik = 'SESUAI',
                vds.nama_ayah = s.nama_ayah,
                vds.nama_ibu_kandung = s.nama_ibu_kandung,
                vds.nama_rombel = s.nama_rombel,
                vds.nama_lengkap_dapodik = s.nama_lengkap
            WHERE vds.nik = s.nik
              AND REPLACE(vds.nama_sekolah,' ','') = REPLACE(sc.nama,' ','')
        ");
    }

    //update nama_ayah, nama_ibu_kandung, nama_rombel

    // public function updateNamaAyahNamaIbuKandungNamaRombel()
    // {
    //     $this->db->query("
    //         UPDATE verval_data_siswa vds
    //         INNER JOIN siswa s ON UPPER(REPLACE(REPLACE(vds.nama_lengkap,'.',''),' ','')) = UPPER(REPLACE(REPLACE(s.nama_lengkap,'.',''),' ',''))
    //         INNER JOIN sekolah sc ON s.sekolah_id = sc.id
    //         SET vds.nama_ayah = s.nama_ayah,
    //             vds.nama_ibu_kandung = s.nama_ibu_kandung,
    //             vds.nama_rombel = s.nama_rombel
    //         WHERE vds.status_nik = 'SESUAI'
    //               AND vds.status_nisn = 'SESUAI'
    //               AND REPLACE(vds.nama_sekolah,' ','') = REPLACE(sc.nama,' ','')
    //     ");
    // }

    public function updateKeterangan()
    {
        $this->db->query("
            UPDATE verval_data_siswa vds
            INNER JOIN siswa s ON vds.nisn = s.nisn
            INNER JOIN sekolah sc ON s.sekolah_id = sc.id
            SET vds.nama_lengkap_dapodik = s.nama_lengkap,
                vds.keterangan = CONCAT('NISN ditemukan dengan nama: ', s.nama_lengkap , ' dan Sekolah: ', sc.nama)
            WHERE vds.status_nisn IS NULL
              AND vds.status_nik IS NULL
        ");
    }

    public function start_verval_data_siswa()
    {
        $this->db->query("UPDATE verval_data_siswa
                          SET status_nisn = NULL,
                              status_nik = NULL,
                              validasi = NULL,
                              keterangan=NULL");

        $this->updateStatusNISN();
        $this->updateStatusNIK();
        // $this->updateNamaAyahNamaIbuKandungNamaRombel();
        $this->updateKeterangan();

        // $this->db->query("
        // UPDATE `verval_data_siswa`
        // SET
        //     `status_nisn` = COALESCE(`status_nisn`, 'TIDAK SESUAI'),
        //     `status_nik` = COALESCE(`status_nik`, 'TIDAK SESUAI'),
        //     `validasi` =
        //         CASE
        //             WHEN `status_nisn` IS NULL OR `status_nik` IS NULL THEN 'TIDAK VALID'
        //             WHEN `status_nisn` = 'SESUAI' AND `status_nik` = 'SESUAI' THEN 'VALID'
        //             WHEN `status_nisn` = 'TIDAK SESUAI' OR `status_nik` = 'TIDAK SESUAI' THEN 'TIDAK VALID'
        //             ELSE 'PENDING'
        //         END;");

        //nisn              nik         validasi
        //tidak sesuai      sesuai      tidak valid

        $this->db->query("
            UPDATE `verval_data_siswa`  SET  `status_nisn` = 'TIDAK SESUAI', `validasi` = 'TIDAK VALID'
            WHERE `status_nisn` IS NULL AND `status_nik` IS NOT NULL;
        ");

        //sesuai      tidak sesuai      tidak valid
        $this->db->query("
            UPDATE `verval_data_siswa` SET `status_nik` = 'TIDAK SESUAI', `validasi` = 'TIDAK VALID'
            WHERE  `status_nik` IS NULL AND `status_nisn` IS NOT NULL;
        ");

        //sesuai      sesuai      valid
        $this->db->query("
            UPDATE `verval_data_siswa`  SET `validasi` = 'VALID'
            WHERE `status_nik` = 'SESUAI' AND `status_nisn` = 'SESUAI';
        ");

        //null      null      pending
        $this->db->query("
            UPDATE `verval_data_siswa`  SET  `status_nik` = 'PENDING',  `status_nisn` = 'PENDING',  `validasi` = 'PENDING'
            WHERE `status_nik` IS NULL  AND `status_nisn` IS NULL;
        ");

        $this->db->query("
            UPDATE `verval_data_siswa`  SET  `keterangan` = 'NISN dan NIK tidak ditemukan'
            WHERE  `status_nik` = 'PENDING'  AND `status_nisn`  = 'PENDING'  AND validasi = 'PENDING'  AND keterangan = '';
        ");

        // $this->db->query("
        //     UPDATE `verval_data_siswa`
        //     SET
        //         `status_nisn` =
        //             CASE
        //                 WHEN `status_nisn` IS NULL AND `status_nik` IS NOT NULL THEN 'TIDAK SESUAI'
        //                 WHEN `status_nik` = 'SESUAI' AND `status_nisn` = 'SESUAI' THEN 'SESUAI'
        //                 ELSE `status_nisn`
        //             END,
        //         `status_nik` =
        //             CASE
        //                 WHEN `status_nik` IS NULL AND `status_nisn` IS NOT NULL THEN 'TIDAK SESUAI'
        //                 ELSE `status_nik`
        //             END,
        //         `validasi` =
        //             CASE
        //                 WHEN `status_nik` = 'SESUAI' AND `status_nisn` = 'SESUAI' THEN 'VALID'
        //                 WHEN `status_nik` IS NULL AND `status_nisn` IS NULL THEN 'PENDING'
        //                 ELSE 'TIDAK VALID'
        //             END,
        //         `keterangan` =
        //             CASE
        //                 WHEN `status_nik` = 'PENDING' AND `status_nisn` = 'PENDING' AND validasi = 'PENDING' AND keterangan = '' THEN 'NISN dan NIK tidak ditemukan'
        //                 ELSE `keterangan`
        //             END
        //     WHERE
        //         (`status_nisn` IS NULL AND `status_nik` IS NOT NULL)
        //         OR (`status_nik` IS NULL AND `status_nisn` IS NOT NULL)
        //         OR (`status_nik` = 'SESUAI' AND `status_nisn` = 'SESUAI')
        //         OR (`status_nik` IS NULL AND `status_nisn` IS NULL AND validasi = 'PENDING' AND keterangan = '');
        // ");

        // $this->db->limit(100);
        $this->db->select('nisn,nik,no_kk,nama_lengkap,nama_lengkap_dapodik,nama_sekolah,
                           nama_ayah,nama_ibu_kandung,nama_rombel,
                           status_nisn,status_nik,validasi,keterangan');
        $q = $this->db->get('verval_data_siswa');

        // echo "<script>Loader.close();</script>";

        Modules::run("export/excel_verval_siswa", 'verval-siswa', $q);

        // $this->alert->set('alert-success', 'Verifikasi dan validasi berhasil dilakukan');
        // redirect(site_url('dinas/verval-data-siswa'), 'reload');
        // echo '<script>window.location.href = "' . site_url('dinas/verval-data-siswa') . '";</script>';

        // $data = $query->result_array();

        // $excel = Excel::create(['Sheet1']);
        // $sheet = $excel->sheet();

        // // Write heads
        // $sheet->writeRow(['NISN', 'NIK', 'No. KK', 'Nama lengkap', 'Nama Sekolah', 'Status NISN', 'Status NIK', 'Keterangan']);

        // // Write data
        // foreach ($data as $rowData) {
        //     $rowOptions = [
        //         'height' => 20,
        //     ];
        //     $sheet->writeRow($rowData, $rowOptions);
        // }

        // $excel->save('verval_data_siswa.xlsx');
        // $excel->download('verval_data_siswa.xlsx');
    }

    public function set_verifikasi_berkas()
    {
        $verifikasi         = $this->uri->segment(3);
        $dokumen_pegawai_id = $this->uri->segment(4);

        $user_id = $this->session->userdata('user_id');

        if (!empty($_POST['alasan'])) {

            $updateArr = array(
                'verifikasi' => $verifikasi,
                'alasan'     => $this->input->post('alasan'),
            );

            $this->db->where('id', $dokumen_pegawai_id);
            $this->db->update('dokumen_pegawai', $updateArr);
        } else {

            $updateArr = array(
                'verifikasi' => $verifikasi,
            );

            $this->db->where('id', $dokumen_pegawai_id);
            $this->db->update('dokumen_pegawai', $updateArr);
        }

        $this->db->select('a.alasan,b.telp as no_hp,c.nama AS nama_dok,a.verifikasi');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');
        $this->db->join('jenis_dokumen c', 'a.jenis_dokumen_id = c.id', 'left');
        $this->db->where('a.id', $dokumen_pegawai_id);
        $dp = $this->db->get('dokumen_pegawai a');

        $pendaftar = $dp->row_array();

        $no_hp    = $pendaftar['no_hp'];
        $nama_dok = $pendaftar['nama_dok'];
        $alasan   = $pendaftar['alasan'];

        if ($verifikasi === 'diterima') {

            echo '<span class="badge bg-success">Diterima</span>';

            //jika jumlah 
            $jd = $this->db->get('jenis_dokumen');
            $jml_dok = $jd->num_rows();
            
            $jml_dokpeg = 0;

            foreach($dp->result_array() as $d){
                $jml_dokpeg += $d['verifikasi'] == 'diterima' ? 1 : 0;
            }

            if ($jml_dok == $jml_dokpeg) {
                $message = "Seluruh berkas unggahan anda telah disetujui." . '\n';
                $message .= "@Disdik Prov. Jambi";

                $data = sendWa($no_hp, $message);

            }
        } else {

            $message = "Berkas : " . $nama_dok . " DITOLAK ." . '\n';
            $message .= "Catatan: " . $alasan . ", ";
            $message .= "segera lakukan perbaikan berkas" . '\n';
            $message .= "@Disdik Prov. Jambi";

            $data = sendWa($no_hp, $message);

            echo '<span class="badge bg-danger">Ditolak</span>';
        }
    }

    public function load_dokumen()
    {
        $pegawai_id = $this->input->get('pegawai_id');
        $slug       = base64url_decode($this->input->get('slug'));

        $dok = "<table class='table table-striped'>";
        $dok .= " <tbody>";
        $jenis_dokumen = $this->db->get('jenis_dokumen');

        $i = 1;
        foreach ($jenis_dokumen->result_array() as $jd) {

            $dok .= " <tr id=\"tr_" . $i . "\" data-bs-toggle=\"collapse\" data-bs-target=\"#r" . $i . "\">";
            $dok .= "   <td>" . $jd['nama'] . "</td>";

            $cek = $this->db->get_where('dokumen_pegawai', array('jenis_dokumen_id' => $jd['id'], 'pegawai_id' => $pegawai_id));
            if ($cek->num_rows() > 0) {
                $dokumen_pegawai = $cek->row_array();
                $dok .= "<td><a href='" . site_url('uploads/dokumen/' . $dokumen_pegawai['file_dokumen']) . "'>Download</a></td>";
                $dok .= "<td id='td_" . $dokumen_pegawai['id'] . "'>";
                $status_dok = $dokumen_pegawai['verifikasi'];
                if ($status_dok === 'pending') {
                    $dok .= '<span class="badge bg-secondary">Belum verifikasi</span> | ';
                    $dok .= '<a id="link_diterima_' . $dokumen_pegawai['id'] . '" class="diterima" onclick="change_status(' . $dokumen_pegawai['id'] . ',\'diterima\')">TERIMA</a> | ';
                    $dok .= '<a id="link_ditolak_' . $dokumen_pegawai['id'] . '" class="ditolak" onclick="change_status(' . $dokumen_pegawai['id'] . ',\'ditolak\')">TOLAK</a></td>';

                    $dok .= "<script>";
                    $dok .= "   const trElement = document.querySelector('#tr_" . $i . "');trElement.removeAttribute('data-bs-toggle');";
                    $dok .= "</script>";
                } elseif ($status_dok === 'diterima') {
                    $dok .= '<span class="badge bg-success">Diterima</span>';

                    $dok .= "<script>";
                    $dok .= "   const trElement = document.querySelector('#tr_" . $i . "');trElement.removeAttribute('data-bs-toggle');";
                    $dok .= "</script>";
                } else {
                    $dok .= '<span class="badge bg-danger">Ditolak <i class="bi bi-chevron-down"></i></span>';
                }

                $dok .= "</td>";
            } else {
                $dok .= "<td colspan=2><span class=\"badge bg-warning text-dark\">Belum diunggah</span></td>";

                $dok .= "<script>";
                $dok .= "   const trElement = document.querySelector('#tr_" . $i . "');trElement.removeAttribute('data-bs-toggle');";
                $dok .= "</script>";
            }

            $dok .= " </tr>";

            if ($cek->num_rows() > 0) {
                $dokumen_pegawai = $cek->row_array();

                $status_dok = $dokumen_pegawai['verifikasi'];
                if ($status_dok === "ditolak") {
                    $alasan = $dokumen_pegawai['alasan'];

                    $dok .= "<tr class=\"collapse accordion-collapse show table-danger\" id=\"r" . $i . "\" data-bs-parent=\".table\">";
                    $dok .= "   <td colspan=\"3\" class=\"link-danger text-center\">Alasan penolakan: " . $alasan . "</td>";
                    $dok .= "</tr>";
                }
            }

            $i++;
        }

        $dok .= " </tbody>";
        $dok .= "</table>";

        echo $dok;
    }

    public function verval_data_siswa()
    {

        if (!empty($_POST)) {
            $this->form_validation->set_rules('txtNama', 'Kolom nama', 'required');
            $this->form_validation->set_rules('txtAsalSekolah', 'Kolom Asal Sekolah', 'required');
            $this->form_validation->set_rules('txtNik', 'Kolom NIK', 'required');
            $this->form_validation->set_rules('txtKk', 'Kolom KK', 'required');
            $this->form_validation->set_rules('txtNisn', 'Kolom NISN', 'required');

            if ($this->form_validation->run() == true) {

                $txtNamaLengkap = $this->input->post('txtNama');
                $txtAsalSekolah = $this->input->post('txtAsalSekolah');
                $txtNik         = $this->input->post('txtNik');
                $txtKk          = $this->input->post('txtKk');
                $txtNisn        = $this->input->post('txtNisn');

                if (!empty($_FILES['file']['name'])) {
                    $upload['upload_path']   = './uploads';
                    $upload['allowed_types'] = 'xlsx';
                    $upload['encrypt_name']  = true;
                    $upload['max_size']      = 1024 * 5; //5 MB

                    $this->load->library('upload', $upload);

                    if (!$this->upload->do_upload('file')) {
                        $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah <br/>' . $this->upload->display_errors());
                        redirect(site_url('dinas/verval-data-siswa'), 'reload');
                    } else {

                        $success   = $this->upload->data();
                        $file_name = $success['file_name'];

                        $reader      = new Xlsx();
                        $spreadsheet = $reader->load(FCPATH . 'uploads/' . $file_name);
                        $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                        // Menyimpan data ke array
                        $data = array();
                        foreach ($sheetData as $key => $row) {
                            // Mulai mengambil data dari baris ke-3
                            if ($key < 3) {
                                continue;
                            }

                            $data[] = array(
                                'nisn'         => str_replace("'", "", $row[$txtNisn]),
                                'nik'          => $row[$txtNik],
                                'no_kk'        => $row[$txtKk],
                                'nama_lengkap' => $row[$txtNamaLengkap],
                                'nama_sekolah' => $row[$txtAsalSekolah],
                            );
                        }

                        $this->db->truncate('verval_data_siswa');
                        // Memasukkan data ke database
                        foreach ($data as $d) {
                            $this->db->insert('verval_data_siswa', $d);
                        }

                        // echo "<script>Loader.open();</script>";
                        // $this->alert->set('alert-success', 'Data Acuan berhasil diimport<br/>Klik Ok untuk memulai verifikasi dan validasi data siswa');
                        redirect(site_url('dinas/start-verval-data-siswa'), 'reload');
                        // echo "<script>Loader.close();</script>";
                    }
                }
            }
        }

        $data['keterangan'] = '';
        $data['page_name']  = 'verval_data_siswa';
        $data['page_title'] = 'Verifikasi dan Validasi Data Siswa';

        $this->_page_output($data);
    }
}
