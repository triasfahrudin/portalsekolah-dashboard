<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
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

        $this->base_breadcrumbs = '/pegawai';

        if ($user_level !== 'PEGAWAI') {
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

    public function presensi()
    {

        $data = array();

        if (!empty($_POST)) {
            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $data['keterangan'] = 'Data riwayat presensi yang anda lakukan';

            
            $this->db->select('a.fulldate AS tgl,
                               b.jam_masuk AS jam_masuk,
                               b.jam_pulang, b.status_masuk,
                               b.status_pulang,
                               c.nilai AS nilai_presensi');
            $this->db->join('kehadiran_pegawai b', 'a.fulldate = DATE(b.jam_masuk) AND  b.pegawai_id = ' . $this->user_id, 'left');
            $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
            $this->db->where('YEAR(a.fulldate)', $tahun);
            $this->db->where('MONTH(a.fulldate)', $bulan);
            $this->db->order_by('a.fulldate');

            $data['presensi'] = $this->db->get('dates a');

        }

        $data['page_name']  = 'riwayat_presensi';
        $data['page_title'] = 'Riwayat Presensi';
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
                        redirect(site_url('pegawai/pengajuan-izin'), 'reload');
                    } else {
                        $success   = $this->upload->data();
                        $file_name = $success['file_name'];

                        $data['file'] = $file_name;

                    }
                }

                $this->db->insert('izin_pegawai', $in);

                $this->alert->set('alert-success', 'Data permohonan izin berhasil diajukan');
                redirect(site_url('pegawai/pengajuan-izin'), 'reload');

            }
        }

        $this->db->select('a.tgl_pengajuan, a.tgl_izin,
                           a.jenis_izin, IFNULL(a.keterangan,"-") AS keterangan, 
						   a.`file`,a.status_izin');
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
        //             redirect(site_url('pegawai/profile/edit/' . $this->user_id), 'reload');
        //         }

        //         $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
        //     } elseif ($state === 'list') {
        //         redirect(site_url('pegawai/profile/edit/' . $this->user_id), 'reload');
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
        //         "url"                => site_url('/pegawai/profile/edit/' . $uri_segment_kode . '/'),
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

            $base_breadcrumbs = 'pegawai/profile';

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
                $this->alert->set('alert-danger', 'Ada kesalahan! Periksa kembali file yang anda unggah\n\rPastikan file yang anda unggah memiliki format yang diijinkan dan besarnya tidak lebih dari 1 MB');
                redirect(site_url('pegawai/dokumen'), 'reload');
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
                redirect(site_url('pegawai/dokumen'), 'reload');
            }
        }
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
                        redirect(site_url('pegawai/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('pegawai/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('pegawai/ganti-password'), 'reload');
                        }
                    }

                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('pegawai/ganti-password'), 'reload');

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
