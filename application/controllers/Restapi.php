<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Restapi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->__resTraitConstruct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array('url', 'libs'));
    }



    //===================================================




    //===================================================








    private function _update_token_login($table_name, $user_id)
    {
        $uuid = generate_uuid();

        $this->db->where('id', $user_id);
        $this->db->update($table_name, array('token_login' => $uuid));

        return $uuid;
    }

    /**
     * The function `doAttendance` is used to handle the attendance process for employees, including
     * checking the login token, saving the employee's photo, checking the employee's location, and
     * recording the attendance time..
     */
    public function doAttendance()
    {

        header('content-type: application/json');

        $user_loginToken = $this->input->post('loginToken');
        $user_foto       = $this->input->post('foto');
        $user_latitude   = $this->input->post('latitude');
        $user_longitude  = $this->input->post('longitude');

        if ($user_foto !== "NO_IMAGE") {

            $image = str_replace('data:image/png;base64,', '', $user_foto);
            $image = str_replace(' ', '+', $image);

            $imageName = generate_uuid() . '.png';
            file_put_contents('uploads/' . $imageName, base64_decode($image));
            $user_foto = $imageName;
        }

        $cek_token_login = cek_token_login($user_loginToken);
        $user_level      = $cek_token_login['level'];
        $user_id         = $cek_token_login['id'];

        $arr_pegawai_level = array('KEPSEK', 'GURU', 'PEGAWAI', 'PEGAWAI-DINAS');

        if (in_array($user_level, $arr_pegawai_level)) {

            $max_jarak_presensi = get_settings('max_jarak_presensi_dalam_meter');
            $jarak              = _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude);

            //==
            if ($jarak > $max_jarak_presensi) {

                $response["error"]     = true;
                $response['error_msg'] = '[E:001] Anda berada diluar wilayah yang diperbolehkan untuk melakukan Presensi!';
                echo json_encode($response);
            } else {

                $cek = $this->db->get_where('pegawai', array('id' => $user_id));

                if ($cek->num_rows() > 0) {
                    $user         = $cek->row_array();
                    $kehadiran_id = 0;

                    $jam_aktif_sekolah = _get_jam_aktif_sekolah($user_id, $user_level);

                    //cek apakah udah presensi masuk ?
                    $cek = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));

                    if ($cek->num_rows() > 0) {

                        //pulang
                        // $cek = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_pulang)' => date("Y-m-d")));

                        if ($jam_aktif_sekolah->num_rows() > 0) {

                            $jam_aktif  = $jam_aktif_sekolah->row_array();
                            $jam_pulang = strtotime($jam_aktif['jam_pulang']);
                            $toleransi  = $jam_aktif['toleransi_pulang'];
                            // $time = strtotime('07:00:00');
                            //$startTime = date("H:i:s", strtotime('-10 minutes', $time));
                            $logout_time = date("Y-m-d H:i:s");
                            $endTime     = date("Y-m-d H:i:s", strtotime("-$toleransi minutes", $jam_pulang));

                            if ($logout_time > $endTime) {
                                $q         = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
                                $kehadiran = $q->row_array();
                                $this->db->where('id', $kehadiran['id']);
                                $this->db->update(
                                    'kehadiran_pegawai',
                                    array(
                                        'jam_pulang'    => $logout_time,
                                        'status_pulang' => 'NORMAL',
                                        'tgl_update'    => $logout_time,
                                        'foto_pulang'   => $user_foto,
                                    )
                                );
                                $kehadiran_id = $kehadiran['id'];

                                // redirect(site_url('signin/face-detection-' . $jenis . '/' . base64url_encode($kehadiran_id . '::' . uniqid())), 'reload');
                            } else {

                                $q         = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
                                $kehadiran = $q->row_array();
                                $this->db->where('id', $kehadiran['id']);
                                $this->db->update(
                                    'kehadiran_pegawai',
                                    array(
                                        'jam_pulang'    => $logout_time,
                                        'status_pulang' => 'CEPAT',
                                        'tgl_update'    => $logout_time,
                                        'foto_pulang'   => $user_foto,
                                    )
                                );
                                $kehadiran_id = $kehadiran['id'];

                                // redirect(site_url('signin/face-detection-' . $jenis . '/' . base64url_encode($kehadiran_id . '::' . uniqid())), 'reload');

                            }

                            $response["error"]     = false;
                            $response['error_msg'] = 'Presensi pulang berhasil dilakukan';

                            echo json_encode($response);
                        } else {
                            $response["error"]     = true;
                            $response['error_msg'] = '[E:003] Data jam aktif untuk level sekolah anda belum di setting!<br>Hubungi operator sekolah anda untuk melakukan setting';
                            echo json_encode($response);
                        }
                    } else {

                        if ($jam_aktif_sekolah->num_rows() > 0) {
                            //==============================================
                            $jam_aktif = $jam_aktif_sekolah->row_array();
                            $jam_masuk = strtotime($jam_aktif['jam_masuk']);
                            $toleransi = $jam_aktif['toleransi_masuk'];
                            // $time = strtotime('07:00:00');
                            //$startTime = date("H:i:s", strtotime('-10 minutes', $time));
                            $login_time = date("Y-m-d H:i:s");
                            $endTime    = date("Y-m-d H:i:s", strtotime("+$toleransi minutes", $jam_masuk));

                            // echo $login_time . ' ' . $endTime;

                            // $login_time = date("Y-m-d H:i:s");
                            // $endTime = date("Y-m-d H:i:s", strtotime("+5 minutes", strtotime('00:10:00')));
                            // echo ($login_time > $endTime) ? 'true':'false';

                            // echo $endTime;
                            if ($login_time < $endTime) {
                                //masuk normal
                                $this->db->insert(
                                    'kehadiran_pegawai',
                                    array(
                                        'pegawai_id'   => $user_id,
                                        'jam_masuk'    => $login_time,
                                        'status_masuk' => 'NORMAL',
                                        'tgl_update'   => $login_time,
                                        'foto_masuk'   => $user_foto,
                                    )
                                );
                                $kehadiran_id = $this->db->insert_id();
                            } else {
                                $this->db->insert(
                                    'kehadiran_pegawai',
                                    array(
                                        'pegawai_id'   => $user_id,
                                        'jam_masuk'    => $login_time,
                                        'status_masuk' => 'TELAT',
                                        'tgl_update'   => $login_time,
                                        'foto_masuk'   => $user_foto,
                                    )
                                );
                                $kehadiran_id = $this->db->insert_id();
                            }

                            $response["error"]     = false;
                            $response['error_msg'] = 'Presensi masuk berhasil dilakukan';

                            echo json_encode($response);
                        } else {
                            $response["error"]     = true;
                            $response['error_msg'] = '[E:003] Data jam aktif untuk level sekolah anda belum di setting!<br>Hubungi operator sekolah anda untuk melakukan setting';
                            echo json_encode($response);
                        }
                    }
                }
            } //==

        } else {
            $response["error"]     = true;
            $response["error_msg"] = "Presensi hanya untuk Pegawai";
            echo json_encode($response);
        }
    }

    public function login()
    {
        header('content-type: application/json');

        $this->load->library('form_validation');

        if (isset($_POST['username']) && isset($_POST['password'])) {

            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run()) {

                $username    = $this->input->post('username');
                $password    = $this->input->post('password');
                $login_token = $this->input->post('login_token');

                //urutan cek : user,operator(sekolah),pegawai,siswa / walimurid
                $this->db->select('id,username,nama_lengkap,email,
				                   level,IFNULL(provinsi_id,0) AS provinsi_id,
								   foto, token_id,password,
                                   IFNULL(token_login,"EMPTY_LOGIN_TOKEN") AS token_login');
                $cek = $this->db->get_where('user', array('username' => $username));
                if ($cek->num_rows() > 0) {
                    $user = $cek->row_array();

                    if (password_verify($password, $user['password'])) {

                        $foto = !empty($user['foto']) ? site_url('uploads/' . $user["foto"]) : site_url('uploads/no_image.jpg');

                        $response["error"]                = false;
                        $response["user"]["id"]           = $user['id'];
                        $response["user"]["username"]     = $user['username'];
                        $response["user"]["nama_lengkap"] = $user['nama_lengkap'];
                        $response["user"]["email"]        = $user['email'];
                        $response["user"]["level"]        = $user['level'];
                        $response["user"]["provinsi_id"]  = $user['provinsi_id'];
                        $response["user"]["sekolah_id"]   = 666; //karena ngga ada sekolah id untuk user, maka gunain aja 666
                        $response["user"]["token_id"]     = $user['token_id'];
                        $response["user"]["foto"]         = $foto;

                        if ($user['token_login'] !== 'EMPTY_LOGIN_TOKEN') {

                            if (empty($login_token)) {

                                $response["user"]["status_token_login"] = "STATUS_INVALID";
                            } elseif ($user['token_login'] === $login_token) {

                                $response["user"]["token_login"]        = $this->_update_token_login('user', $user['id']);
                                $response["user"]["status_token_login"] = "STATUS_VALID";
                            }
                        } else {

                            if (empty($login_token)) {

                                $response["user"]["status_token_login"] = "STATUS_VALID";
                                $response["user"]["token_login"]        = $this->_update_token_login('user', $user['id']);
                            } else {
                                $response["user"]["status_token_login"] = "STATUS_INVALID";
                            }
                        }

                        echo json_encode($response);
                    } else {

                        $response["error"]     = true;
                        $response["error_msg"] = "Password tidak valid!";
                        echo json_encode($response);
                    }
                } else {

                    $this->db->select('id,npsn,nama,email,token_id,logo,password,
                                       IFNULL(token_login,"EMPTY_LOGIN_TOKEN") AS token_login,
									   login_aktif');
                    $cek = $this->db->get_where('sekolah', array('npsn' => $username));
                    if ($cek->num_rows() > 0) {
                        $user = $cek->row_array();

                        if ($user['login_aktif'] === 'Y') {
                            if (password_verify($password, $user['password'])) {

                                $foto = !empty($user['logo']) ? site_url('uploads/' . $user["foto"]) : site_url('uploads/no_image.jpg');

                                $response["error"]                = false;
                                $response["user"]["id"]           = $user['id'];
                                $response["user"]["sekolah_id"]   = $user['id']; //sekolah id = id untuk operator
                                $response["user"]["username"]     = $user['npsn'];
                                $response["user"]["nama_lengkap"] = $user['nama'];
                                $response["user"]["email"]        = $user['email'];
                                $response["user"]["level"]        = "OPERATOR";
                                $response["user"]["token_id"]     = $user['token_id'];
                                $response["user"]["foto"]         = $foto;
                                // $response["user"]["token_login"]  = $this->_update_token_login('sekolah', $user['id']);

                                if ($user['token_login'] !== 'EMPTY_LOGIN_TOKEN') {

                                    if (empty($login_token)) {

                                        $response["user"]["status_token_login"] = "STATUS_INVALID";
                                    } elseif ($user['token_login'] === $login_token) {

                                        $response["user"]["token_login"]        = $this->_update_token_login('sekolah', $user['id']);
                                        $response["user"]["status_token_login"] = "STATUS_VALID";
                                    }
                                } else {

                                    if (empty($login_token)) {

                                        $response["user"]["status_token_login"] = "STATUS_VALID";
                                        $response["user"]["token_login"]        = $this->_update_token_login('sekolah', $user['id']);
                                    } else {
                                        $response["user"]["status_token_login"] = "STATUS_INVALID";
                                    }
                                }

                                echo json_encode($response);
                            } else {

                                $response["error"]     = true;
                                $response["error_msg"] = "Password tidak valid!";
                                echo json_encode($response);
                            }
                        } else {
                            $response["error"]     = true;
                            $response["error_msg"] = "Sekolah anda belum mengaktifkan akun. Silahkan berkordinasi dengan Operator Dinas";
                            echo json_encode($response);
                        }
                    } else {

                        $this->db->select('id,IFNULL(sekolah_id,0) AS sekolah_id,
						                   nuptk,nama_lengkap,email,token_id,jabatan,password,
                                           IFNULL(token_login,"EMPTY_LOGIN_TOKEN") AS token_login');
                        $cek = $this->db->get_where('pegawai', array('nuptk' => $username));
                        if ($cek->num_rows() > 0) {
                            $user = $cek->row_array();

                            if (password_verify($password, $user['password'])) {

                                $foto              = !empty($user['foto']) ? site_url('uploads/' . $user["foto"]) : site_url('uploads/no_image.jpg');
                                $response["error"] = false;

                                $response["user"]["sekolah_id"]   = $user['sekolah_id'];
                                $response["user"]["id"]           = $user['id'];
                                $response["user"]["username"]     = $user['nuptk'];
                                $response["user"]["nama_lengkap"] = $user['nama_lengkap'];
                                $response["user"]["email"]        = $user['email'];
                                $response["user"]["token_id"]     = $user['token_id'];
                                $response["user"]["foto"]         = $foto;

                                if ($user['sekolah_id'] > 0) {

                                    if ($user['jabatan'] === 'Kepala Sekolah') {
                                        $response["user"]["level"] = "KEPSEK";
                                    } elseif ($user['jabatan'] === 'Guru Mapel') {
                                        $response["user"]["level"] = "GURU";
                                    } else {

                                        $response["user"]["level"] = "PEGAWAI";
                                    }

                                    $sekolah = $this->db->get_where('sekolah', array('id' => $user['sekolah_id']))->row_array();

                                    if ($sekolah['login_aktif'] === 'Y') {
                                        // $response["user"]["token_login"] = $this->_update_token_login('pegawai', $user['id']);


                                        if ($user['token_login'] !== 'EMPTY_LOGIN_TOKEN') {


                                            if (empty($login_token)) {

                                                $response["user"]["status_token_login"] = "STATUS_INVALID";
                                            } elseif ($user['token_login'] === $login_token) {

                                                $response["user"]["token_login"]        = $this->_update_token_login('pegawai', $user['id']);
                                                $response["user"]["status_token_login"] = "STATUS_VALID";
                                            }
                                        } else {

                                            if (empty($login_token)) {

                                                $response["user"]["status_token_login"] = "STATUS_VALID";
                                                $response["user"]["token_login"]        = $this->_update_token_login('pegawai', $user['id']);
                                            } else {
                                                $response["user"]["status_token_login"] = "STATUS_INVALID";
                                            }
                                        }

                                        echo json_encode($response);
                                    } else {
                                        $response["error"]     = true;
                                        $response["error_msg"] = "Sekolah anda belum mengaktifkan akun. Silahkan berkordinasi dengan Operator Dinas";
                                        echo json_encode($response);
                                    }
                                } else {
                                    $response["user"]["level"] = "PEGAWAI-DINAS";

                                    if ($user['token_login'] !== 'EMPTY_LOGIN_TOKEN') {

                                        if (empty($login_token)) {

                                            $response["user"]["status_token_login"] = "STATUS_INVALID";
                                        } elseif ($user['token_login'] === $login_token) {

                                            $response["user"]["token_login"]        = $this->_update_token_login('pegawai', $user['id']);
                                            $response["user"]["status_token_login"] = "STATUS_VALID";
                                        }
                                    } else {

                                        if (empty($login_token)) {

                                            $response["user"]["status_token_login"] = "STATUS_VALID";
                                            $response["user"]["token_login"]        = $this->_update_token_login('pegawai', $user['id']);
                                        } else {
                                            $response["user"]["status_token_login"] = "STATUS_INVALID";
                                        }
                                    }

                                    echo json_encode($response);
                                }
                            } else {
                                $response["error"]     = true;
                                $response["error_msg"] = "Password tidak valid!";
                                echo json_encode($response);
                            }
                        } else {

                            $this->db->select('id,sekolah_id,nisn,nama_lengkap,
											   email,token_id,foto,password,
                                               IFNULL(token_login,"EMPTY_LOGIN_TOKEN") AS token_login');
                            $cek = $this->db->get_where('siswa', array('nisn' => $username));
                            if ($cek->num_rows() > 0) {
                                $user = $cek->row_array();

                                if (password_verify($password, $user['password'])) {

                                    $foto = !empty($user['foto']) ? site_url('uploads/' . $user["foto"]) : site_url('uploads/no_image.jpg');

                                    $response["error"]                = false;
                                    $response["user"]["sekolah_id"]   = $user['sekolah_id'];
                                    $response["user"]["id"]           = $user['id'];
                                    $response["user"]["username"]     = $user['nisn'];
                                    $response["user"]["nama_lengkap"] = $user['nama_lengkap'];
                                    $response["user"]["email"]        = $user['email'];
                                    $response["user"]["level"]        = "SISWA";
                                    $response["user"]["token_id"]     = $user['token_id'];
                                    $response["user"]["foto"]         = $foto;

                                    $response["user"]["status_token_login"] = "STATUS_VALID";
                                    $response["user"]["token_login"]        = $this->_update_token_login('siswa', $user['id']);

                                    echo json_encode($response);
                                } else {

                                    $response["error"]     = true;
                                    $response["error_msg"] = "Password tidak valid!";
                                    echo json_encode($response);
                                }
                            } else {

                                $response["error"]     = true;
                                $response["error_msg"] = "Username tidak ditemukan !";
                                echo json_encode($response);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Send Token ID
     * 
     * This function is responsible for updating the token ID and device unique ID 
     * for different user levels in the database.
     * 
     * @return void
     */
    public function send_tokenid()
    {
        header('content-type: application/json');

        // Check if user_level and user_id are set in the POST request
        if (isset($_POST['user_level']) && isset($_POST['user_id'])) {

            // Initialize the response array
            $response = [
                "error" => false,
                "error_msg" => "Send Token BERHASIL"
            ];

            // Get the values from the POST request
            $user_level    = $this->input->post('user_level');
            $user_id       = $this->input->post('user_id');
            $token_id      = $this->input->post('token_id');
            $dev_unique_id = $this->input->post('dev_unique_id');

            // Update the corresponding table based on the user level
            $this->db->where('id', $user_id);

            $tables = [
                'OPERATOR' => 'sekolah',
                'KEPSEK' => 'pegawai',
                'GURU' => 'pegawai',
                'PEGAWAI' => 'pegawai',
                'PEGAWAI-DINAS' => 'pegawai',
                'SISWA' => 'siswa',
                'DINAS' => 'user',
                'SUPERUSER' => 'user',
            ];

            if (isset($tables[$user_level])) {
                $this->db->update(
                    $tables[$user_level],
                    [
                        'token_id' => $token_id,
                        'dev_unique_id' => $dev_unique_id
                    ]
                );
            } else {
                // Invalid user level, set error and error message in the response
                $response["error"] = true;
                $response["error_msg"] = "Nama tabel tidak valid";
            }

            // Encode the response array as JSON and echo it
            echo json_encode($response);
        } else {
            // user_level or user_id not set in the POST request, set error and error message in the response
            $response = [
                "error" => true,
                "error_msg" => "Send Token ID GAGAL!"
            ];
            echo json_encode($response);
        }
    }


    public function pegawai_ajax()
    {
        //get data
        $this->load->library('Datatables');

        $slug = $this->input->post('slug');

        $this->db->where('npsn', $slug);
        $skl = $this->db->get('sekolah');
        $sekolah = $skl->row_array();

        // $set_jenis_dokumen = $kategori['set_jenis_dokumen'];

        $this->datatables->select(
            "a.id,
             CONCAT('tr_',a.id) AS DT_RowId,
             a.nip,
             a.jk,
             a.status_pegawai AS status_pegawai,
             UPPER(a.nama_lengkap) AS nama_lengkap,
             a.gelar_depan AS gelar_depan,
             a.gelar_belakang AS gelar_belakang,
             a.tempat_lahir AS tempat_lahir,
             a.tgl_lahir AS tgl_lahir,
             a.jml_istri,
             a.jml_anak,
             (SELECT nama FROM skpd WHERE kode = a.kode_skpd) AS nama_skpd,
             (SELECT nama FROM satker WHERE kode = a.kode_satker) AS nama_satker,             
             CONCAT(
                  LPAD(CAST((SELECT COUNT(*) FROM `dokumen_pegawai` WHERE `pegawai_id` = `a`.`id`) AS UNSIGNED),2,'0'),
                  '-',
                  LPAD(CAST((SELECT COUNT(*) FROM `dokumen_pegawai` WHERE `pegawai_id` = `a`.`id` AND `verifikasi` = 'diterima') AS UNSIGNED),2,'0'),
                  '-',
                  LPAD(CAST((SELECT COUNT(*) FROM `dokumen_pegawai` WHERE `pegawai_id` = `a`.`id` AND `verifikasi` = 'ditolak') AS UNSIGNED),2,'0')
              ) AS dokumen"
        );

        $this->datatables->from('pegawai a');
        $this->datatables->group_by('a.id');
        $this->datatables->where('a.sekolah_id', $sekolah['id']);

        
        echo $this->datatables->generate();
    }
}
