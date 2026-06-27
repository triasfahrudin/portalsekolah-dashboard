<?php

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->helper(array('url', 'libs', 'form','alert'));
        $this->load->database();

        $this->load->libraries(array('form_validation', 'ciqrcode','alert','session'));

    }

   public function _page_output($output = null)
    {
        // $output['user_id']      = $this->user_id;
        // $output['nama_lengkap'] = $this->session->userdata('user_nama');
        $this->load->view('master_view.php', (array) $output);
    }

    public function profile($token_login){        

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
            $crud->field_type('wali_kelas', 'hidden');
            $crud->field_type('foto','hidden');
            $crud->field_type('dev_unique_id', 'hidden');


            $crud->display_as('nuptk','NUPTK');

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
                    redirect(site_url('webview/pegawai/profile/' . $token_login . '/edit/' . $user_id), 'reload');
                }
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

        // $this->session->set_userdata('active_menu',site_url('webview/kepsek/ganti_password/' . $user_id));
        $user_id = $this->session->userdata('user_id');

        if (!empty($_POST['pass_lama'])) {

            $password = $this->input->post('pass_lama');

            $cek_user = $this->db->get_where('pegawai', array('id' => $user_id));

            if ($cek_user->num_rows() > 0) {

                $user = $cek_user->row_array();

                if (password_verify($password, $user['password'])) {

                    if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                        $this->alert->set('alert-danger', 'Password baru / ulangan tidak boleh kosong');
                        redirect(site_url('webview/kepsek/ganti-password/' . $user_id), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('webview/kepsek/ganti-password/' . $user_id), 'reload');
                        } else {
                            $this->db->where('id', $user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('webview/kepsek/ganti-password/' . $user_id), 'reload');
                        }
                    }

                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('webview/kepsek/ganti-password/' . $user_id), 'reload');

                }

            } else {

            }
        }

        $data['user_id'] = $user_id;
        $data['page_name'] = 'ganti_password';
        $data['page_title'] = 'Ganti Password';

        $this->_page_output($data);
    }
}
