<?php

defined('BASEPATH') or exit('No direct script access allowed');

//  'user_provinsi'  => $user['dinas_provinsi'],
//                                         'user_kabupaten' => $user['dinas_kabupaten'],

//                                         'user_id'        => $user['id'],
//                                         'user_username'  => $user['nuptk'],
//                                         'user_nama'      => $user['nama_lengkap'],
//                                         'user_email'     => $user['email'],
//                                         'user_level'     => 'PEGAWAI-DINAS',
//                                         'user_token'     => $user['token_id'],

class Pegawai extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $provinsi_id;
	private $kabupaten_id;
	

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'alert'));
        $this->load->library(array('form_validation', 'session', 'alert', 'breadcrumbs'));

        $this->breadcrumbs->load_config('default');

		$this->provinsi_id  = $this->session->userdata('user_provinsi');
        $this->kabupaten_id = $this->session->userdata('user_kabupaten');
		$this->user_id      = $this->session->userdata('user_id');
        $user_level         = $this->session->userdata('user_level');
       
        $this->base_breadcrumbs = '/dinas/pegawai';

        if ($user_level !== 'PEGAWAI-DINAS') {
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
        $this->load->view('pegawai/master_page.php', (array) $output);
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
                        redirect(site_url('pegawai/pengajuan-izin'), 'reload');
                    } else {
                        $success   = $this->upload->data();
                        $file_name = $success['file_name'];

                        $data['file'] = $file_name;

                    }
                }

                $this->db->insert('izin_pegawai', $in);

                $this->alert->set('alert-success', 'Data permohonan izin berhasil diajukan');
                redirect(site_url('dinas/pegawai/pengajuan-izin'), 'reload');

            }
        }

        $this->db->select('a.tgl_pengajuan, a.tgl_izin,
                           a.jenis_izin, 
						   IFNULL(a.keterangan,"-") AS keterangan, 
						   a.`file`,a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');        
        $this->db->order_by('a.tgl_izin DESC');
        $this->db->where('b.id', $this->user_id);
        $data['izin'] = $this->db->get('izin_pegawai a');

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Pengajuan Izin', $this->base_breadcrumbs . '/pengajuan-izin');

        $data['keterangan'] = 'Anda bisa mengajukan permohonan izin sekaligus melihat riwayat permohonan anda';
        $data['page_name']  = 'pengajuan_izin';
        $data['page_title'] = 'Pengajuan Izin';

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


	public function profile()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Profile');

            $crud->required_fields('nama_lengkap');
            $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');
            $crud->field_type('tgl_update', 'hidden');
            // $crud->field_type('sekolah_id', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('terakhir_login', 'readonly');
            $crud->field_type('nuptk', 'readonly');
            $crud->field_type('jabatan', 'readonly');
            $crud->field_type('status_pegawai', 'readonly');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('wali_kelas', 'hidden');
            $crud->field_type('aktif', 'hidden');

            $crud->field_type('id', 'hidden');


            // $crud->set_relation('sekolah_id','sekolah','nama');
            // $crud->display_as('sekolah_id', 'Unit Kerja');
            $crud->field_type('sekolah_id', 'hidden');
			$crud->field_type('dinas_provinsi', 'hidden');
			$crud->field_type('dinas_kabupaten', 'hidden');
			$crud->field_type('status_pegawai', 'hidden');
			$crud->field_type('dinas_pangkat', 'readonly');
			$crud->display_as('dinas_pangkat', 'Pangkat/Golongan');
			$crud->field_type('dinas_unit_kerja', 'readonly');
			$crud->display_as('dinas_unit_kerja', 'Unit Kerja');

            $crud->display_as('nuptk', 'NIP');
            $crud->set_field_upload('foto', 'uploads');

            $crud->callback_after_insert(function ($post_array, $primary_key) {

            });

            $crud->callback_after_update(function ($post_array, $primary_key) {

                $this->db->where('id', $primary_key);
                $this->db->update('user',
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

                $curr_user_id = $this->uri->segment(5);

                if ($curr_user_id != $this->user_id) {
                    redirect(site_url('dinas/pegawai/profile/edit/' . $this->user_id), 'reload');
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
            } elseif ($state === 'list') {
                redirect(site_url('dinas/pegawai/profile/edit/' . $this->user_id), 'reload');
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

            $cek_user = $this->db->get_where('pegawai', array('id' => $this->user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('dinas/pegawai/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('dinas/pegawai/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('dinas/pegawai/ganti-password'), 'reload');
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
