<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Superuser extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));

        $this->breadcrumbs->load_config('default');

        $level                  = $this->session->userdata('user_level');
        $this->user_id          = $this->session->userdata('user_id');
        $this->base_breadcrumbs = '/superuser';

        if ($level !== 'SUPERUSER') {
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
        $this->breadcrumbs->push('Dashboard', '/superuser');

        $data['page_name']  = 'beranda';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function reset_token_pengguna($user_id)
    {
        $this->db->set('token_login', 'NULL', false);
        $this->db->where('id', $user_id);
        $this->db->update('user');

        $this->alert->set('alert-success', 'Token berhasil direset');

        redirect(site_url('superuser/kelola-pengguna'), 'reload');
    }

    public function reset_password_pengguna($user_id)
    {

        $cek = $this->db->get_where('user', array('id' => $user_id))->row_array();

        $username_pass = password_hash($cek['username'], PASSWORD_DEFAULT);

        $this->db->set('password', "'" . $username_pass . "'", false);
        $this->db->where('id', $user_id);
        $this->db->update('user');

        $this->alert->set('alert-success', 'Password berhasil direset dan diganti menjadi username');

        redirect(site_url('superuser/kelola-pengguna'), 'reload');
    }

    public function bobot_presensi()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('bobot');
            $crud->set_subject('Bobot Presensi');
            $crud->order_by('id', 'ASC');

            $crud->required_fields('nilai');

            $crud->columns('status_masuk', 'status_pulang', 'nilai', 'warna');
            $crud->field_type('status_masuk', 'readonly');
            $crud->field_type('status_pulang', 'readonly');

            $crud->field_type('tgl_update', 'hidden');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_edit_field('warna', function ($value, $primary_key) {
                return '<input id="field-warna" name="warna" class="color-picker form-control" value="' . $value . '" />';
            });

            $crud->callback_column('warna', function ($value, $row) {
                return "<div style='color:" . $value . "'>" . $value . "</div>";
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Bobot Presensi', $this->base_breadcrumbs . '/bobot_presensi');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/bobot_presensi/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/bobot_presensi/add');
            } elseif ($state === 'list') {
                // $config_bobot = 'NORMAL:NORMAL:5;NORMAL:IZIN-CEPAT:4;NORMAL:CEPAT:3;';
                // $config_bobot .= 'TELAT:NORMAL:3;TELAT:IZIN-CEPAT:2;TELAT:CEPAT:1;';
                // $config_bobot .= 'IZIN-TELAT:NORMAL:4;IZIN-TELAT:IZIN-CEPAT:3;IZIN-TELAT:CEPAT:2;';
                // $config_bobot .= 'IZIN-TIDAK-MASUK:IZIN-TIDAK-MASUK:3;';
                // $config_bobot .= 'ALPA:ALPA:0';

                // $sekolah_id = $this->sekolah_id;

                // $expl_config = explode(';', $config_bobot);

                // foreach ($expl_config as $key) {
                //     $exp_key = explode(':', $key);

                //     $status_masuk  = $exp_key[0];
                //     $status_pulang = $exp_key[1];
                //     $nilai         = $exp_key[2];

                //     $tgl_update = date('Y-m-d H:i:s');

                //     $this->db->query("INSERT INTO bobot(sekolah_id,status_masuk,status_pulang,nilai)
                //                       VALUES($sekolah_id,'$status_masuk','$status_pulang',$nilai)
                //                       ON DUPLICATE KEY UPDATE tgl_update = '$tgl_update'");
                // }
            }

            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_read();

            $extra = array('page_title' => 'Kelola Bobot Presensi');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function jam_kerja()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('jam_aktif_sekolah');
            $crud->set_subject('Jam Kerja');
            $crud->order_by('id', 'ASC');
            // $crud->where('sekolah_id', $this->sekolah_id);

            $crud->required_fields('jam_masuk,jam_pulang,toleransi_masuk,toleransi_pulang');

            $crud->columns('level_sekolah', 'hari', 'jam_masuk', 'jam_pulang', 'toleransi_masuk', 'toleransi_pulang');
            // $crud->field_type('sekolah_id', 'hidden', $this->sekolah_id);
            $crud->field_type('tgl_update', 'hidden');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_edit_field('hari', function ($value, $primary_key) {
                return '<div id="field-hari" class="readonly_label">' . $value . '</div>';
            });

            $crud->callback_edit_field('level_sekolah', function ($value, $primary_key) {
                return '<div id="field-level_sekolah" class="readonly_label">' . $value . '</div>';
            });

            $crud->callback_column('toleransi_masuk', function ($value, $row) {
                return $value . '&nbsp;Menit';
            });

            $crud->callback_column('toleransi_pulang', function ($value, $row) {
                return $value . '&nbsp;Menit';
            });

            // $crud->display_as('toleransi','Toleransi (menit)');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Jam Kerja', $this->base_breadcrumbs . '/jam_kerja');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/jam_kerja/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/jam_kerja/edit');
            } elseif ($state === 'list') {

                // $config = 'SENIN-07:00:00-12:00:00-5;';
                // $config .= 'SELASA-07:00:00-12:00:00-5;';
                // $config .= 'RABU-07:00:00-12:00:00-5;';
                // $config .= 'KAMIS-07:00:00-12:00:00-5;';
                // $config .= 'JUMAT-07:00:00-11:00:00-5;';
                // $config .= 'SABTU-07:00:00-12:00:00-5';

                // $sekolah_id = $this->sekolah_id;

                // // echo $config;
                // // exit();
                // $expl_config = explode(';', $config);

                // foreach ($expl_config as $key) {
                //     // echo $key;
                //     // exit();
                //     $exp_key = explode('-', $key);

                //     // echo count($exp_key);
                //     // exit(0);

                //     $hari = $exp_key[0];
                //     // echo $exp_key[1];
                //     // exit();
                //     $jam_masuk  = $exp_key[1];
                //     $jam_pulang = $exp_key[2];
                //     $toleransi  = $exp_key[3];

                //     $tgl_update = date('Y-m-d H:i:s');
                //     // echo $hari;

                //     $this->db->query("INSERT INTO jam_aktif_sekolah(sekolah_id,hari,jam_masuk,jam_pulang,toleransi)
                //                       VALUES($sekolah_id,'$hari','$jam_masuk','$jam_pulang',$toleransi)
                //                       ON DUPLICATE KEY UPDATE tgl_update = '$tgl_update'");
                // }

            }

            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_read();

            $extra               = array('page_title' => 'Kelola Jam Kerja');
            $extra['keterangan'] = '<br/>Jam masuk & jam keluar dalam bentuk format waktu 24 jam (misal : 08:00);
                                    <br/>Toleransi dalam menit';

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function hari_libur()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('hari_libur');
            $crud->set_subject('Hari Libur');
            $crud->order_by('tgl', 'ASC');
            $crud->where("DATE_FORMAT(tgl,'%Y-%m')", date('Y-m'));

            $crud->required_fields('tgl,keterangan');

            $crud->columns('tgl', 'keterangan');

            $crud->display_as('tgl', 'Tanggal');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Hari Libur', $this->base_breadcrumbs . '/hari_libur');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/hari_libur/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/hari_libur/edit');
            } elseif ($state === 'list') {
            }

            // $crud->unset_add();
            // $crud->unset_delete();
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_edit();

            $bulan = array(
                1 =>
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            );

            $extra               = array('page_title' => 'Kelola hari libur');
            $extra['keterangan'] = 'Kelola Hari libur pada ' . $bulan[date('n')] . '&nbsp;' . date('Y');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_satker($skpd)
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('satker');
            $crud->where('kode_skpd', $skpd);

            $crud->required_fields('kode', 'nama');

            $crud->columns('kode', 'nama');

            $crud->field_type('kode_skpd', 'hidden', $skpd);

            $crud->unset_read();
            $crud->unset_clone();

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola SKPD', $this->base_breadcrumbs . '/kelola-skpd');
            $this->breadcrumbs->push('Kelola Satker', $this->base_breadcrumbs . '/kelola-satker');


            $extra = array('page_title' => 'Kelola Satker');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);

        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_skpd()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('skpd');
            $crud->set_subject('Data SKPD');

            $crud->required_fields('kode', 'nama');

            $crud->columns('kode', 'nama', 'satker');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('satker', function ($value, $row) {

                return '<a href="' . site_url('superuser/kelola-satker/' . $row->kode) . '">Kelola</a>';
            });

           
            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola SKPD', $this->base_breadcrumbs . '/kelola-skpd');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-skpd/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-skpd/edit');
            } elseif ($state === 'list') {
            }

            // $crud->unset_add();
            // $crud->unset_delete();
            $crud->unset_read();
            $crud->unset_clone();
            // $crud->unset_edit();

            $extra = array('page_title' => 'Kelola SKPD');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function settings($act = null, $param = null)
    {
        $this->breadcrumbs->push('Dashboard', '/superuser');
        $this->breadcrumbs->push('Setting', '/settings');

        $data['breadcrumbs'] = $this->breadcrumbs->show();

        if ($act === 'upload') {
            if (!empty($_FILES['img']['name'])) {
                $upload                  = array();
                $upload['upload_path']   = './uploads';
                $upload['allowed_types'] = 'jpeg|jpg|png';
                $upload['encrypt_name']  = true;

                $this->load->library('upload', $upload);

                if (!$this->upload->do_upload('img')) {
                    echo $this->upload->display_errors();
                    exit();
                } else {

                    $title = $this->input->post('title');

                    $success  = $this->upload->data();
                    $value    = $success['file_name'];
                    $file_ext = $success['file_ext'];

                    $this->db->where('title', $title);
                    $this->db->update('settings', array('value' => $value, 'tipe' => 'image'));

                    redirect('superuser/settings');
                }
            }
        } elseif ($act === 'edt') {
            $value = $this->input->post('value');

            $this->db->where('title', $param);
            $this->db->update('settings', array('value' => $value));

            exit(0);
        }

        $this->db->order_by('title ASC');
        $data['setting'] = $this->db->get('settings');
        // $data['keterangan'] = 'Jangan ';
        $data['page_name']  = 'settings';
        $data['page_title'] = 'Data Settings';

        $this->_page_output($data);
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
            $crud->field_type('provinsi_id', 'readonly');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('provinsi_id', 'hidden');
            $crud->field_type('kabupaten_id', 'hidden');
            $crud->field_type('latitude', 'hidden');
            $crud->field_type('longitude', 'hidden');
            $crud->field_type('map', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');

            $crud->set_field_upload('foto', 'uploads');

            // $crud->display_as('provinsi_id', 'Wilayah Kerja');

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

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            // $this->breadcrumbs->push('Profil', $this->base_breadcrumbs . '/profile');

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
                    redirect(site_url('superuser/profile/edit/' . $this->user_id), 'reload');
                }

                $this->breadcrumbs->push('Ubah Profil', $this->base_breadcrumbs . '/profile/edit');
            }

            $crud->unset_back_to_list();
            $extra = array('page_title' => 'Kelola Sekolah');

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

            $cek_user = $this->db->get_where('user', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('superuser/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('superuser/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('user', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('superuser/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('superuser/ganti-password'), 'reload');
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

    public function export_pns_pppk()
    {
        // $data['page_name'] = 'export_pns_pppk';

        // $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        // $this->breadcrumbs->push('Export PNS dan PPPK', $this->base_breadcrumbs . '/export_pns_pppk');

        // $data['page_title'] = 'Export PNS dan PPPK';

        // $this->_page_output($data);

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->where('status_pegawai', 'pns');
            $crud->or_where('status_pegawai', 'pppk');
            $crud->set_subject('PNS & PPPK');

            $crud->columns('nuptk', 'nip', 'nama_lengkap', 'status_pegawai', 'sekolah', 'sk_pengangkatan', 'tmt_pengangkatan');

            $crud->callback_column('sekolah', function ($value, $row) {
                $sek = $this->db->get_where('sekolah', array('id' => $row->sekolah_id))->row_array();
                return $sek['nama'];
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            // $this->breadcrumbs->push('Profil', $this->base_breadcrumbs . '/profile');
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_add();

            $extra = array('page_title' => 'Kelola Sekolah');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function lihat_sekolah_kabupaten($kabupaten_id)
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('sekolah');
            $crud->set_subject('Sekolah');
            $crud->where('kabupaten', $kabupaten_id);

            $crud->required_fields('npsn', 'level', 'nama', 'status');

            $crud->columns('npsn', 'nama', 'alamat', /*'kelurahan',*/'status', 'pegawai', 'siswa' /*, 'rekap_kehadiran'*/);
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('kabupaten', 'hidden', $kabupaten_id);

            $crud->display_as('nama', 'Nama Sekolah');
            $crud->display_as('npsn', 'NPSN');

            // $crud->set_relation('provinsi', 'wilayah_provinsi', 'nama', array('id' => $this->provinsi_id));
            // $crud->set_relation('kabupaten', 'wilayah_kabupaten', 'nama', array('provinsi_id' => $provinsi_id));
            $crud->set_relation('kecamatan', 'wilayah_kecamatan', 'nama');
            $crud->set_relation('kelurahan', 'wilayah_kelurahan', 'nama');

            $crud->callback_column('pegawai', function ($value, $row) {
                return '<a href="' . site_url('superuser/lihat-pegawai-' . $row->kabupaten . '-' . $row->id) . '">Lihat</a>';
            });

            $crud->callback_column('siswa', function ($value, $row) {
                return '<a href="' . site_url('superuser/lihat-siswa-' . $row->kabupaten . '-' . $row->id) . '">Lihat</a>';
            });

            $crud->callback_column('alamat', function ($value, $row) {

                $kelurahan = $this->db->get_where('wilayah_kelurahan', array('id' => $row->kelurahan));
                if ($kelurahan->num_rows() > 0) {
                    $kel = $kelurahan->row_array();
                    return $row->alamat . ' Kel.' . $kel['nama'];
                } else {
                    return $row->alamat;
                }
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pengguna', $this->base_breadcrumbs . '/kelola-pengguna');
            $this->breadcrumbs->push('Lihat Sekolah', $this->base_breadcrumbs . '/lihat-sekolah-kabupaten-' . $kabupaten_id);

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
                $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            } elseif ($state === 'edit') {
                // $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            }

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
                'kabupaten' => array( // second dropdown name
                    'table_name' => 'wilayah_kabupaten', // table of state
                    'title' => 'nama', // state title
                    'id_field' => 'id', // table of state: primary key
                    'relate' => null, // table of state:
                    'data-placeholder' => 'Pilih Kabupaten', //dropdown's data-placeholder:
                ),
                // third field. same settings
                'kecamatan' => array(
                    'table_name'       => 'wilayah_kecamatan',
                    'title'            => 'nama', // now you can use this format )))
                    'id_field' => 'id',
                    'relate'           => 'kabupaten_id',
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

            $extra = array('page_title' => 'Kelola Sekolah');
            // $extra['keterangan'] = 'Anda dapat meng-import data sekolah dari file Microsoft Excel. Klik di <a class="text-danger" href="' . site_url('dinas/import-sekolah') . '">SINI</a>&nbsp;untuk melakukannya';

            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_edit();

            $output = $crud->render();
            $output->output .= $js;

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_pengguna()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('user');
            $crud->set_subject('Pengguna');

            $crud->required_fields('username', 'password', 'nama_lengkap', 'email', 'level');

            $crud->columns('username', 'level', 'token', 'password');
            $crud->field_type('tgl_update', 'hidden');

            $crud->add_fields(array('username', 'nama_lengkap', 'email', 'password', 'level', 'kabupaten_id'));
            $crud->edit_fields(array('username', 'nama_lengkap', 'email', 'level', 'kabupaten_id'));
            $crud->display_as('username', 'User Name');
            $crud->display_as('kabupaten_id', 'Wilayah Kerja');

            $this->db->order_by('nama ASC');
            $dokuments = $this->db->get('wilayah_kabupaten');

            $set_kabupaten = array();
            foreach ($dokuments->result_array() as $row) {
                $set_kabupaten[$row['id']] = $row['nama'];
            }

            $crud->field_type('kabupaten_id', 'multiselect', $set_kabupaten);

            //$crud->set_relation('kabupaten_id', 'wilayah_kabupaten', 'nama',array('provinsi_id' => '100000'));

            $crud->callback_column('token', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset token untuk pengguna ini?\');" class="text-danger" href="' . site_url('superuser/reset-token-pengguna/' . $row->id) . '">RESET</a>';
            });

            $crud->callback_column('password', function ($value, $row) {

                return '<a onclick="return confirm(\'anda yakin melakukan reset password untuk pengguna ini?\');" class="text-danger" href="' . site_url('superuser/reset-password-pengguna/' . $row->id) . '">RESET</a>';
            });

            $crud->callback_after_insert(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update(
                    'user',
                    array(
                        'password'   => password_hash($post_array['password'], PASSWORD_DEFAULT),
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
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

            $crud->callback_column('level', function ($value, $row) {
                if ($value === 'SUPERUSER') {
                    return '<p class="text-danger" style="display:inline">SUPERUSER</p>';
                } elseif (in_array($value, array('DINAS', 'DINAS-SMA', 'DINAS-SMK', 'DINAS-SLB'))) {
                    $wilayah = explode(',', $row->kabupaten_id ?? '');
                    $this->db->where_in('id', $wilayah);
                    $p = $this->db->get('wilayah_kabupaten');

                    $ret = "";
                    foreach ($p->result_array() as $row) {
                        $ret .= 'DINAS ' . $row['nama'] . '<br>';
                    }
                    return $ret;
                    //return '<p class="text-default" style="display:inline"><a href="' . site_url('superuser/lihat-sekolah-kabupaten-' . $row->kabupaten_id) . '">DINAS&nbsp;' . $p['nama'] . '</a></p>';
                }
            });

            $this->breadcrumbs->push('Beranda', '/superuser');
            $this->breadcrumbs->push('Kelola Pengguna', '/superuser/kelola-pengguna');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                $crud->set_rules('username', 'User Name', 'is_unique[user.username]|required');
                $crud->set_rules('email', 'Email', 'is_unique[user.email]|valid_email|required');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email|required');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/superuser/kelola-pengguna/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', '/superuser/kelola-pengguna/add');
            }

            $crud->unset_clone();
            $crud->unset_read();

            $extra               = array('page_title' => 'Kelola Pengguna');
            $extra['keterangan'] = 'Klik kolom level untuk detail';
            $output              = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function jenis_dokumen()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('jenis_dokumen');
            $crud->set_subject('Jenis Dokumen Pegawai');

            $crud->columns('nama');

            $crud->required_fields('nama');

            $this->breadcrumbs->push('Dashboard', '/superuser/index');
            $this->breadcrumbs->push('Jenis Dokumen', '/superuser/jenis_dokumen');

            $extra = array('page_title' => 'Jenis Dokumen');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function lihat_pegawai($kabupaten_id, $sekolah_id)
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Pegawai');
            $crud->where('sekolah_id', $sekolah_id);

            $crud->required_fields('jabatan', 'status_pegawai', 'nuptk', 'nama_lengkap', 'jk');

            $crud->columns('nuptk', 'nama_lengkap', 'jabatan', 'status_pegawai' /*, 'opsi'*/);
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('wali_kelas', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $sekolah_id);

            $crud->display_as('nuptk', 'NUPTK');

            // $crud->callback_after_insert(function ($post_array, $primary_key) {

            //     $this->db->where('id', $primary_key);
            //     $this->db->update('pegawai',
            //         array(
            //             'password'   => password_hash($post_array['nuptk'], PASSWORD_DEFAULT),
            //             'tgl_update' => date('Y-m-d H:i:s'),
            //         )
            //     );
            // });

            // $crud->callback_after_update(function ($post_array, $primary_key) {

            //     $this->db->where('id', $primary_key);
            //     $this->db->update('pegawai',
            //         array(
            //             'tgl_update' => date('Y-m-d H:i:s'),
            //         )
            //     );
            // });

            $crud->callback_column('jabatan', function ($value, $row) {
                // return '<a href="' . site_url('dinas/rekap-kehadiran/' . simple_crypt($row->id, 'e')) . '">Lihat</a>';

                // if($value === 'GURU'){
                //     return $value . '  <a href="' . site_url('operator/jadwal-mengajar/' . $row->id) . '">JADWAL MENGAJAR</a>';
                // }else{
                //     return $value;
                // }

                return $value;
            });

            // $crud->callback_column('opsi', function ($value, $row) {

            //     if ($row->jabatan === 'GURU') {
            //         return '<a href="' . site_url('operator/jadwal-mengajar/' . $row->id) . '">JADWAL MENGAJAR</a>';
            //     } else {
            //         return '-';
            //     }
            // });

            // $crud->set_field_upload('foto', 'uploads/pegawai');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pengguna', '/superuser/kelola-pengguna');
            $this->breadcrumbs->push('Sekolah', $this->base_breadcrumbs . '/lihat-sekolah-kabupaten-' . $kabupaten_id);
            $this->breadcrumbs->push('Pegawai', $this->base_breadcrumbs . '/lihat-pegawai-kabupaten-' . $kabupaten_id . '-' . $sekolah_id);

            $state = $crud->getState();

            if ($state === 'insert_validation') {
                // $crud->set_rules('nuptk', 'NUPTK', 'is_unique[pegawai.nuptk]|required');
                // $crud->set_rules('email', 'Email', 'is_unique[pegawai.email]|valid_email');
            } elseif ($state === 'update_validation') {
                // $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-pegawai/add');
            } elseif ($state === 'edit') {
                // $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-pegawai/edit');
            }

            $extra = array('page_title' => 'Kelola Pegawai');
            // $extra['keterangan'] = 'Anda dapat meng-import data pegawai dari file Microsoft Excel. Klik di <a class="text-danger" href="' . site_url('operator/import-pegawai') . '">SINI</a>&nbsp;untuk melakukannya';

            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function lihat_siswa($provinsi_id, $sekolah_id)
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('siswa');
            $crud->set_subject('Siswa');
            $crud->where('sekolah_id', $sekolah_id);

            $crud->required_fields('nisn', 'email', 'nama_lengkap', 'jk');

            $crud->columns('nama_lengkap', 'nisn');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('kelas_id', 'hidden');
            $crud->field_type('sekolah_id', 'hidden', $sekolah_id);

            $crud->display_as('nisn', 'NISN');

            // $crud->callback_after_insert(function ($post_array, $primary_key) {

            //     $this->db->where('id', $primary_key);
            //     $this->db->update('siswa',
            //         array(
            //             'password'   => password_hash($post_array['nisn'], PASSWORD_DEFAULT),
            //             'tgl_update' => date('Y-m-d H:i:s'),
            //         )
            //     );
            // });

            // $crud->callback_after_update(function ($post_array, $primary_key) {

            //     $this->db->where('id', $primary_key);
            //     $this->db->update('siswa',
            //         array(
            //             'tgl_update' => date('Y-m-d H:i:s'),
            //         )
            //     );
            // });

            // $crud->set_field_upload('foto', 'uploads/siswa');

            // $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            // $this->breadcrumbs->push('Kelola Pegawai', $this->base_breadcrumbs . '/kelola-siswa');

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Pengguna', '/superuser/kelola-pengguna');
            $this->breadcrumbs->push('Sekolah', $this->base_breadcrumbs . '/lihat-sekolah-provinsi-' . $provinsi_id);
            $this->breadcrumbs->push('Siswa', $this->base_breadcrumbs . '/lihat-siswa-provinsi-' . $provinsi_id . '-' . $sekolah_id);

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // $crud->set_rules('nisn', 'NISN', 'is_unique[siswa.nuptk]|required');
                // $crud->set_rules('email', 'Email', 'is_unique[siswa.email]|valid_email');
            } elseif ($state === 'update_validation') {
                // $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-siswa/add');
            } elseif ($state === 'edit') {
                // $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-siswa/add');
            }

            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $extra = array('page_title' => 'Kelola Pegawai');
            // $extra['keterangan'] = 'Anda dapat meng-import data siswa dari file Microsoft Excel. Klik di <a class="text-danger" href="' . site_url('operator/import-siswa') . '">SINI</a>&nbsp;untuk melakukannya';

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function truncate_previous_attendance()
    {
        $currentYear = date('Y');

        // Delete records from 'dates' table
        $this->db->where('YEAR(fulldate) <', $currentYear)->delete('dates');

        // Delete records from 'guru_mengajar' table
        $this->db->where('YEAR(tgl) <', $currentYear)->delete('guru_mengajar');

        // Delete files and records from 'guru_mengajar' table
        $this->db->select('dokumentasi, foto')->where('YEAR(tgl) <', $currentYear);
        $query = $this->db->get('guru_mengajar');
        foreach ($query->result() as $row) {
            @unlink('uploads/dokumentasi/' . $row->dokumentasi);
            @unlink('uploads/dokumentasi/' . $row->foto);
        }
        $this->db->where('YEAR(tgl) <', $currentYear)->delete('guru_mengajar');

        // Delete records from 'hari_libur' table
        $this->db->where('YEAR(tgl) <', $currentYear)->delete('hari_libur');

        // Delete records from 'izin_pegawai' table
        $this->db->where('YEAR(tgl_izin) <', $currentYear)->delete('izin_pegawai');

        // Delete records from 'izin_siswa' table
        $this->db->where('YEAR(tgl_izin) <', $currentYear)->delete('izin_siswa');

        // Delete files and records from 'kehadiran_pegawai' table
        $this->db->select('foto_masuk, foto_pulang')->where('YEAR(jam_masuk) <', $currentYear);
        $query = $this->db->get('kehadiran_pegawai');
        foreach ($query->result() as $row) {
            @unlink('uploads/' . $row->foto_masuk);
            @unlink('uploads/' . $row->foto_pulang);
        }
        $this->db->where('YEAR(jam_masuk) <', $currentYear)->delete('kehadiran_pegawai');

        // Delete records from 'kehadiran_siswa' table
        $this->db->where('YEAR(tgl) <', $currentYear)->delete('kehadiran_siswa');

        // Set success alert
        $this->alert->set('alert-success', 'Data berhasil dihapus');

        redirect(site_url('superuser/settings'), 'reload');
    }
}
