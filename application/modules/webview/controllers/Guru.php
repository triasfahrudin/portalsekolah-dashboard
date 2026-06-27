<?php

class Guru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->helper(array('url', 'libs', 'form', 'alert'));
        $this->load->database();

        $this->load->libraries(array('form_validation', 'ciqrcode', 'alert', 'session'));

    }

    public function _page_output($output = null)
    {
        // $output['user_id']      = $this->user_id;
        // $output['nama_lengkap'] = $this->session->userdata('user_nama');
        $this->load->view('master_view.php', (array) $output);
    }

    public function pengajuan_izin($token_login)
    {
        $cek_token_login = cek_token_login($token_login);
        $user_level      = $cek_token_login['level'];
        $user_id         = $cek_token_login['id'];

        $data['page_name']  = 'pengajuan_izin';
        $data['page_title'] = 'Pengajuan Izin';
        $this->_page_output($data);
    }

    public function profile($token_login)
    {

        $act              = $this->uri->segment(5);
        $user_id_provided = $this->uri->segment(6);
        $cek_token_login  = cek_token_login($token_login);
        $user_level       = $cek_token_login['level'];
        $user_id          = $cek_token_login['id'];

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Profile');

            $crud->required_fields('nama_lengkap');
            $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('sekolah_id', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('terakhir_login', 'readonly');
            $crud->field_type('nuptk', 'readonly');
            $crud->field_type('jabatan', 'readonly');
            $crud->field_type('status_pegawai', 'readonly');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('foto', 'hidden');
			$crud->field_type('dinas_pangkat', 'hidden');
			$crud->field_type('dinas_unit_kerja', 'hidden');
			$crud->field_type('dinas_provinsi', 'hidden');
			$crud->field_type('dinas_kabupaten', 'hidden');
			$crud->field_type('aktif', 'readonly');

            $crud->callback_edit_field('wali_kelas', function ($value, $primary_key) {
                $exp = explode(',', $value);
                $this->db->where_in('id', $exp);
                $kelas = $this->db->get('kelas');

                $kelas_wali = "";
                foreach ($kelas->result_array() as $key) {
                    $kelas_wali .= $key['nama_kelas'] . " ,";
                }

                $kelas_wali = substr($kelas_wali, 0, -1);

                return '<div id="field-wali_kelas" class="readonly_label">' . $kelas_wali . '</div>';

            });

            $crud->display_as('nuptk', 'NUPTK');
            // $crud->set_field_upload('foto', 'uploads');

            $crud->callback_after_insert(function ($post_array, $primary_key) {

            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update('pegawai',
                    array(
                        'tgl_update' => date('Y-m-d H:i:s'),
                    )
                );
            });

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
                // $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'add') {
                // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
            } elseif ($state === 'edit') {
                if ($user_id_provided != $user_id) {
                    redirect(site_url('webview/guru/profile/' . $token_login . '/edit/' . $user_id), 'reload');
                }
                // $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
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

    public function guru_mengajar($jadwal_mengajar_id)
    {

        $guru_mengajar_id = 0;
        $cek              = $this->db->get_where('guru_mengajar',
            array(
                'jadwal_mengajar_id' => $jadwal_mengajar_id,
                'DATE(jam_masuk)'    => date('Y-m-d'),
            )
        );

        if ($cek->num_rows() == 0) {
            $this->db->insert('guru_mengajar',
                array(
                    'jadwal_mengajar_id' => $jadwal_mengajar_id,
                    'jam_masuk'          => date("Y-m-d H:i:s"),
                )
            );

            $guru_mengajar_id = $this->db->insert_id();
        } else {
            $guru_mengajar = $cek->row_array();

            $guru_mengajar_id = $guru_mengajar['id'];
        }

        $jadwal_mengajar = $this->db->get_where('jadwal_mengajar', array('id' => $jadwal_mengajar_id))->row_array();
        $kelas_diajar_id = $jadwal_mengajar['kelas_id'];

        // SELECT a.nisn, a.nama_lengkap,COUNT(b.id),IFNULL(b.`status`,'PENDING') AS status
        // FROM siswa a
        // LEFT JOIN kehadiran_siswa b ON a.id = b.siswa_id AND b.guru_mengajar_id = 1
        // WHERE a.kelas_id = 1
        // GROUP BY a.id

        $this->db->select("a.id,a.nisn,
                          a.nama_lengkap,
                          COUNT(b.id),
                          IFNULL(b.`status`,'PENDING') AS status");
        $this->db->join('kehadiran_siswa_detail b', 'a.id = b.siswa_id AND b.guru_mengajar_id = ' . $guru_mengajar_id, 'LEFT');
        $this->db->where('a.kelas_id', $kelas_diajar_id);
        $this->db->group_by('a.id');
        $this->db->order_by('COUNT(b.id)', 'ASC');
        $this->db->order_by('a.nama_lengkap', 'ASC');

        $kehadiran_siswa = $this->db->get('siswa a');

        $kehadiran = array();
        foreach ($kehadiran_siswa->result_array() as $key) {
            //cek apakah siswa ini mengajukan izin sakit pada hari ini?
            $cek_sakit = $this->db->get_where('izin_siswa',
                array(
                    'tgl_izin'    => date('Y-m-d'),
                    'siswa_id'    => $key['id'],
                    'status_izin' => 'TERIMA',
                )
            );

            if ($cek_sakit->num_rows() > 0) {

                $izin_siswa = $cek_sakit->row_array();

                $cek_kehadiran_siswa_detail = $this->db->get_where(
                    'kehadiran_siswa_detail',
                    array(
                        'guru_mengajar_id' => $guru_mengajar_id,
                        'siswa_id'         => $key['id'],
                        'DATE(jam_absen)'  => date('Y-m-d'),
                    )
                );

                if ($cek_kehadiran_siswa_detail->num_rows() == 0) {

                    $this->db->insert('kehadiran_siswa_detail',
                        array(
                            'guru_mengajar_id' => $guru_mengajar_id,
                            'siswa_id'         => $key['id'],
                            'jam_absen'        => date('Y-m-d H:i:s'),
                            'status'           => ($izin_siswa['jenis_izin'] === 'SAKIT') ? 'SAKIT' : 'IZIN',
                        )
                    );
                } 

                $kehadiran[] = array(
                    'id'           => $key['id'],
                    'nama_lengkap' => $key['nama_lengkap'],
                    'status'       => ($izin_siswa['jenis_izin'] === 'SAKIT') ? 'SAKIT' : 'IZIN',
                    'changeable'   => 'TIDAK',
                );

            } else {
                $kehadiran[] = array(
                    'id'           => $key['id'],
                    'nama_lengkap' => $key['nama_lengkap'],
                    'status'       => $key['status'],
                    'changeable'   => 'YA',
                );

            }

        }

        $data['kehadiran_siswa']  = $kehadiran_siswa;
        $data['guru_mengajar_id'] = $guru_mengajar_id;
        $data['jadwal_mengajar']  = $jadwal_mengajar_id;

        $cek = $this->db->get_where('guru_mengajar',
            array(
                'jadwal_mengajar_id' => $jadwal_mengajar_id,
                'DATE(jam_masuk)'    => date('Y-m-d'),
                'DATE(jam_selesai)'  => date('Y-m-d'),
            )
        );

        if ($cek->num_rows() > 0) {
            $data['keterangan']    = '<br/>Sesi mengajar telah anda akhiri. Perubahan status tidak dapat dilakukan';
            $data['sesi_berakhir'] = 'true';
        } else {
            $data['keterangan']    = '<br/>Setelah sesi mengajar selesai, silahkan klik <a href="' . site_url('webview/guru/jam-mengajar-selesai/' . $guru_mengajar_id . '/' . $jadwal_mengajar_id) . '">disini</a><br>Klik tombol pada kolom status untuk merubah status presensi siswa';
            $data['sesi_berakhir'] = 'false';
        }

        $data['page_name']  = 'guru_mengajar';
        $data['page_title'] = 'Guru mengajar';
        $this->_page_output($data);

    }

    public function jam_mengajar_selesai($guru_mengajar_id, $jadwal_mengajar_id)
    {

        $this->db->where('id', $guru_mengajar_id);
        $this->db->update('guru_mengajar', array('jam_selesai' => date("Y-m-d H:i:s")));

        redirect(site_url('webview/guru/guru-mengajar/' . $jadwal_mengajar_id), 'reload');

    }

    public function set_kehadiran_siswa($guru_mengajar_id, $jadwal_mengajar_id, $siswa_id, $status)
    {
        $jam_absen = date("Y-m-d H:i:s");

        $this->db->query(
            "INSERT INTO kehadiran_siswa_detail(guru_mengajar_id,siswa_id,jam_absen,status)
             VALUES($guru_mengajar_id,$siswa_id,'$jam_absen','$status')
             ON DUPLICATE KEY UPDATE jam_absen = '$jam_absen',status = '$status'"
        );

        //cek kehadiran_siswa

        redirect(site_url('webview/guru/guru_mengajar/' . $jadwal_mengajar_id), 'reload');

    }

    public function jadwal_mengajar($index_param)
    {
        try {

            $exp_param = explode('::', $index_param);

            //token_login::latitude::longitude
            $token_login    = $exp_param[0];
            $user_latitude  = $exp_param[1];
            $user_longitude = $exp_param[2];

            $cek_token_login = cek_token_login($token_login);

            $user_level = $cek_token_login['level'];
            $user_id    = $cek_token_login['id'];

            $cek = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date('Y-m-d')));
            if ($cek->num_rows() == 0) {
                $this->alert->set('alert-danger', 'Lakukan presensi sebelum mengakses menu ini!');
                redirect(site_url('webview/index/' . $index_param), 'reload');
            }

            $day        = date('w');
            $hari_array = array('MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU');

            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('jadwal_mengajar');
            $crud->set_subject('Jadwal mengajar');
            $crud->where('pegawai_id', $user_id);
            $crud->where('hari', $hari_array[$day]);
            $crud->order_by('jam_mulai', 'ASC');

            $crud->set_relation('kelas_id', 'kelas', 'nama_kelas');
            $crud->set_relation('matapelajaran_id', 'matapelajaran', 'nama');
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_read();
            $crud->unset_print();
            $crud->unset_export();

            $crud->columns(['jam_mulai', 'jam_selesai', 'kelas_id', 'matapelajaran_id', 'presensi']);

            $crud->callback_column('presensi', function ($value, $row) {
                return '<a class="btn btn-primary" href="' . site_url('webview/guru/guru-mengajar/' . $row->id) . '">Masuk</a>';
            });

            $crud->display_as('kelas_id', 'Kelas');
            $crud->display_as('matapelajaran_id', 'Matapelajaran');

            $extra               = array('page_title' => 'Kelola Profil');
            $extra['keterangan'] = 'Jadwal mengajar hari ' . $hari_array[$day] . ' tanggal ' . date('d-m-Y');

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }

    }

    public function ganti_password($token_login)
    {

        $cek_token_login = cek_token_login($token_login);
        $user_level      = $cek_token_login['level'];
        $user_id         = $cek_token_login['id'];

        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('pegawai', array('id' => $user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('webview/guru/ganti-password/' . $token_login), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('webview/guru/ganti-password/' . $token_login), 'reload');
                        } else {
                            $this->db->where('id', $user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('webview/guru/ganti-password/' . $token_login), 'reload');
                        }
                    }

                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('webview/guru/ganti-password/' . $token_login), 'reload');

                }

            }
        }

        $data['page_name']  = 'ganti_password';
        $data['page_title'] = 'Ganti Password';

        $this->_page_output($data);
    }
}
