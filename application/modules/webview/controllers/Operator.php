<?php

class Operator extends CI_Controller
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

    public function profile($token_login)
    {

        $act              = $this->uri->segment(5);
        $user_id_provided = $this->uri->segment(6);

        $cek_token_login = cek_token_login($token_login);
        $user_level      = $cek_token_login['level'];
        $user_id         = $cek_token_login['id'];

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('sekolah');
            $crud->set_subject('Profile Sekolah');

            $crud->required_fields('nama');

            $crud->columns('npsn', 'nama', 'alamat', 'kelurahan', 'status', 'rekap_kehadiran');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('password', 'hidden');
            $crud->field_type('provinsi', 'hidden');
            $crud->field_type('level', 'readonly');
            $crud->field_type('status', 'readonly');
            $crud->field_type('npsn', 'readonly');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
			$crud->field_type('latitude', 'hidden');
			$crud->field_type('longitude', 'hidden');
			$crud->field_type('map', 'hidden');
			$crud->field_type('logo', 'hidden');
			$crud->field_type('login_aktif', 'hidden');
			
			$crud->set_relation('provinsi', 'wilayah_provinsi', 'nama');
			$crud->set_relation('kabupaten', 'wilayah_kabupaten', 'nama');
			$crud->set_relation('kecamatan', 'wilayah_kecamatan', 'nama');
			$crud->set_relation('kelurahan', 'wilayah_kelurahan', 'nama');

			$crud->field_type('provinsi', 'readonly');
			$crud->field_type('kabupaten', 'readonly');
			$crud->field_type('kecamatan', 'readonly');
			$crud->field_type('kelurahan', 'readonly');


            $crud->display_as('nama', 'Nama');
            $crud->display_as('npsn', 'NPSN');
            $crud->field_type('foto', 'hidden');

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
                    redirect(site_url('webview/operator/profile/' . $token_login . '/edit/' . $user_id), 'reload');
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

    public function ganti_password($token_login)
    {

        $cek_token_login = cek_token_login($token_login);
        $user_level      = $cek_token_login['level'];
        $user_id         = $cek_token_login['id'];

        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('sekolah', array('id' => $user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('webview/operator/ganti-password/' . $token_login), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('webview/operator/ganti-password/' . $token_login), 'reload');
                        } else {
                            $this->db->where('id', $user_id);
                            $this->db->update('sekolah', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('webview/operator/ganti-password/' . $token_login), 'reload');
                        }
                    }

                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('webview/operator/ganti-password/' . $token_login), 'reload');

                }

            }
        }

        $data['page_name']  = 'ganti_password';
        $data['page_title'] = 'Ganti Password';

        $this->_page_output($data);
    }
}
