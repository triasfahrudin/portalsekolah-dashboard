<?php

defined('BASEPATH') or exit('No direct script access allowed');

//https://betterexplained.com/articles/how-to-optimize-your-site-with-gzip-compression/
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
    ob_start("ob_gzhandler");
} else {
    ob_start();
}

class Signin extends MX_Controller
{
    private $data = array();

    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();

        $this->load->library(array('form_validation', 'recaptcha', 'session', 'alert'));

        $this->load->helper(array('url', 'libs', 'alert'));

        if (get_settings('site_is_offline') === 'YA') {
            redirect('signin/offline_page', 'reload');
        }
    }

    /**
     * Redirect
     */
    public function create_user()
    {
       
    }

    private function _verify_password_md5_compat($input_password, $stored_password, $table, $id)
    {
        if (preg_match('/^[a-f0-9]{32}$/i', $stored_password)) {
            if (md5($input_password) === $stored_password) {
                $new_password = password_hash($input_password, PASSWORD_DEFAULT);
                $this->db->where('id', $id);
                $this->db->update($table, array('password' => $new_password));
                return true;
            }
            return false;
        }

        return password_verify($input_password, $stored_password);
    }

    public function _page_output($output = null)
    {
        $this->load->view('master_page.php', (array) $output);
    }

    public function tgl_indo($tanggal)
    {
        $bulan = array(
            1 =>
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember',
        );
        $pecahkan = explode('-', $tanggal);

        return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
    }

    public function index()
    {

        //set an date and time to work with
        // $start = '2014-06-01 14:00:00';

        // //display the converted time
        // echo date('Y-m-d H:i',strtotime('+1 hour +20 minutes',strtotime($start)));

        // $login_time = date("Y-m-d H:i:s");
        // $endTime = date("Y-m-d H:i:s", strtotime("+5 minutes", strtotime('00:10:00')));

        // echo ($login_time > $endTime) ? 'true':'false';

        if (!empty($_POST)) {

            switch ($_POST['submit']) {
                case 'login':
                    if (!stripos(base_url(), '127.0.0.1')) {

                        $captcha_answer = $this->input->post('g-recaptcha-response');
                        $response       = $this->recaptcha->verifyResponse($captcha_answer);

                        if (!$response['success']) {
                            redirect(site_url('signin'), 'reload');
                        }
                    }

                    $this->form_validation->set_rules('username', 'Username', 'required');
                    $this->form_validation->set_rules('password', 'Password', 'required');

                    if ($this->form_validation->run() == true) {

                        $username = $this->input->post('username');
                        $password = $this->input->post('password');

                        //urutan cek : user,operator(sekolah),pegawai,siswa / walimurid

                        //user
                        $cek = $this->db->get_where('user', array('username' => $username));
                        if ($cek->num_rows() > 0) {
                            $user = $cek->row_array();

                            if ($this->_verify_password_md5_compat($password, $user['password'], 'user', $user['id'])) {
                                $this->session->set_userdata(
                                    array(
                                        'user_id'       => $user['id'],
                                        'user_username' => $user['username'],
                                        'user_nama'     => $user['nama_lengkap'],
                                        'user_email'    => $user['email'],
                                        'user_level'    => $user['level'],
                                        'provinsi_id'   => $user['provinsi_id'],
                                        'kabupaten_id'  => $user['kabupaten_id'],
                                        'user_foto'     => _get_logo_for_document('user', $user['id']),
                                    )
                                );

                                if (in_array($user['level'], array('SUPERUSER', 'DINAS'))) {
                                    redirect(site_url(strtolower($user['level'])), 'reload');
                                } else {
                                    //,'DINAS-SMA','DINAS-SMK','DINAS-SLB'
                                    redirect(site_url('dinas'), 'reload');
                                }
                            }
                        }

                        //operator sekolah
                        $cek = $this->db->get_where('sekolah', array('npsn' => $username));
                        if ($cek->num_rows() > 0) {
                            $user        = $cek->row_array();
                            $db_password = $user['password'];

                            if ($db_password === 'INITIAL_PASSWORD') {
                                $npsn         = $user['npsn'];
                                $new_password = password_hash($npsn, PASSWORD_DEFAULT);

                                $this->db->where('id', $user['id']);
                                $this->db->update('sekolah', array('password' => $new_password));

                                $db_password = $new_password;
                            }

                            if ($this->_verify_password_md5_compat($password, $db_password, 'sekolah', $user['id'])) {

                                if ($user['login_aktif'] === 'Y') {

                                    $this->session->set_userdata(
                                        array(
                                            'user_id'       => $user['id'],
                                            'user_username' => $user['npsn'],
                                            'user_npsn'     => $user['npsn'],
                                            'user_nama'     => $user['nama'],
                                            'user_email'    => $user['email'],
                                            'user_logo'     => _get_logo_for_document('sekolah', $user['id']),
                                            'user_level'    => 'OPERATOR',
                                        )
                                    );

                                    redirect(site_url('operator'), 'reload');
                                } else {
                                    $this->alert->set('alert-danger', 'Sekolah anda belum mengaktifkan akun<br/> Silahkan berkordinasi dengan Operator Dinas');

                                    redirect(site_url('signin'), 'reload');
                                }
                            }
                        }

                        //pegawai
                        $this->db->select('id,IFNULL(sekolah_id,0) AS sekolah_id,
                                            nuptk,nama_lengkap,email,token_id,jabatan,password,
                                            IFNULL(token_login,"EMPTY_LOGIN_TOKEN") AS token_login');
                        $cek = $this->db->get_where('pegawai', array('nik' => $username));

                        if ($cek->num_rows() > 0) {
                            $user = $cek->row_array();

                            $db_password = $user['password'];

                            /* 08:06:2024
                             * penggunaan INITIAL_PASSWORD tidak digunakan lagi, karena sudah ada fasilitas reset password
                             */

                            // if ($db_password === 'INITIAL_PASSWORD') {
                            //     $nuptk        = $user['nuptk'];
                            //     $new_password = password_hash($nuptk, PASSWORD_DEFAULT);

                            //     $this->db->where('id', $user['id']);
                            //     $this->db->update('pegawai', array('password' => $new_password));

                            //     $db_password = $new_password;
                            // }

                            if ($this->_verify_password_md5_compat($password, $db_password, 'pegawai', $user['id'])) {

                                $sekolah_id_length = strlen($user['sekolah_id']);

                                if ($sekolah_id_length == 36) {

                                    $sekolah = $this->db->get_where('sekolah', array('id' => $user['sekolah_id']))->row_array();

                                    if ($sekolah['login_aktif'] === 'Y') {

                                        if ($user['jabatan'] === 'Kepala Sekolah') {

                                            $this->session->set_userdata(
                                                array(
                                                    'user_sekolah_id' => $user['sekolah_id'],
                                                    'user_id'         => $user['id'],
                                                    'user_username'   => $user['nuptk'],
                                                    'user_nama'       => $user['nama_lengkap'],
                                                    'user_email'      => $user['email'],
                                                    'user_level'      => 'KEPSEK',
                                                    'user_token'      => $user['token_id'],
                                                    'user_logo'       => _get_logo_for_document('kepsek', $user['id']),
                                                )
                                            );

                                            redirect(site_url('kepsek'), 'reload');
                                        } elseif ($user['jabatan'] === 'Guru Mapel') {

                                            $this->session->set_userdata(
                                                array(
                                                    'user_sekolah_id' => $user['sekolah_id'],
                                                    'user_id'         => $user['id'],
                                                    'user_username'   => $user['nuptk'],
                                                    'user_nama'       => $user['nama_lengkap'],
                                                    'user_email'      => $user['email'],
                                                    'user_level'      => 'GURU',
                                                    'user_token'      => $user['token_id'],
                                                )
                                            );
                                            redirect(site_url('guru'), 'reload');
                                        } else {

                                            $this->session->set_userdata(
                                                array(
                                                    'user_sekolah_id' => $user['sekolah_id'],
                                                    'user_id'         => $user['id'],
                                                    'user_username'   => $user['nuptk'],
                                                    'user_nama'       => $user['nama_lengkap'],
                                                    'user_email'      => $user['email'],
                                                    'user_level'      => 'PEGAWAI',
                                                    'user_token'      => $user['token_id'],
                                                )
                                            );
                                            redirect(site_url('pegawai'), 'reload');
                                        }
                                    } else {
                                        $this->alert->set('alert-danger', 'Sekolah anda belum mengaktifkan akun<br/> Silahkan berkordinasi dengan Operator Dinas');
                                        redirect(site_url('signin'), 'reload');
                                    }
                                } else {
                                    //pegawai dinas
                                    $this->session->set_userdata(
                                        array(
                                            'user_provinsi'  => $user['dinas_provinsi'],
                                            'user_kabupaten' => $user['dinas_kabupaten'],

                                            'user_id'        => $user['id'],
                                            'user_username'  => $user['nuptk'],
                                            'user_nama'      => $user['nama_lengkap'],
                                            'user_email'     => $user['email'],
                                            'user_level'     => 'PEGAWAI-DINAS',
                                            'user_token'     => $user['token_id'],
                                        )
                                    );
                                    redirect(site_url('dinas/pegawai'), 'reload');
                                }
                            }
                        } //pegawai

                        $cek = $this->db->get_where('siswa', array('nisn' => $username));
                        if ($cek->num_rows() > 0) {
                            $user = $cek->row_array();

                            $db_password = $user['password'];

                            if ($db_password === 'INITIAL_PASSWORD') {
                                $nisn         = $user['nisn'];
                                $new_password = password_hash($nisn, PASSWORD_DEFAULT);

                                $this->db->where('id', $user['id']);
                                $this->db->update('siswa', array('password' => $new_password));

                                $db_password = $new_password;
                            }

                            if ($this->_verify_password_md5_compat($password, $db_password, 'siswa', $user['id'])) {
                                // exit(0);
                                $this->session->set_userdata(
                                    array(
                                        'user_id'         => $user['id'],
                                        'user_username'   => $user['nisn'],
                                        'user_nama'       => $user['nama_lengkap'],
                                        'user_email'      => $user['email'],
                                        'user_sekolah_id' => $user['sekolah_id'],
                                        'user_kelas_id'   => $user['kelas_id'],
                                        'user_level'      => 'SISWA',
                                        'user_token'      => $user['token_id'],
                                    )
                                );

                                redirect(site_url('siswa'), 'reload');
                            }
                        }
                    }

                    break;
                case 'reset-password':

                    $wa      = $this->input->post('txtWa');
                    $nik     = $this->input->post('txtNik');
                    $namaIbu = $this->input->post('txtNamaIbu');

                    $this->db->where('nik', $nik);
                    $this->db->where('nama_ibu_kandung', $namaIbu);
                    $cek = $this->db->get('pegawai');

                    if ($cek->num_rows() > 0) {


                        $last_pass_reset = $cek->row_array()['last_pass_reset'];

                        if (validateDate($last_pass_reset)) {
                            $date_exp = new DateTime($last_pass_reset);
                            $date_now = new DateTime();

                            if ($date_exp > $date_now) {
                                $this->alert->set('alert-danger', 'Mohon tunggu 3 menit sebelum request Reset Password kembali', false);
                                redirect(site_url('signin'), 'reload');
                                break;
                            }
                        }

                        $user_id   = $cek->row_array()['id'];
                        $pass_baru = generateRandomString(5);
                        $dateTime  = new DateTime();
                        $dateTime->modify('+3 minutes');
                        $last_pass_reset = $dateTime->format('Y-m-d H:i:s');

                        $message = "Password baru anda adalah: " . $pass_baru . '\n';
                        $message .= "Gunakan password baru ini untuk login ke akun anda" . '\n';

                        $this->db->where('id', $user_id);
                        $this->db->update(
                            'pegawai',
                            array(
                                'password'        => password_hash($pass_baru, PASSWORD_DEFAULT),
                                'last_pass_reset' => $last_pass_reset,
                                'telp' => $wa
                            )
                        );

                        sendWa($wa, $message);

                        $this->alert->set('alert-success', 'Password berhasil diupdate');
                        redirect(site_url('signin'), 'reload');
                    } else {
                        $this->alert->set('alert-danger', 'Data yang anda masukkan tidak ditemukan');
                        redirect(site_url('signin'), 'reload');
                    }

                    break;
                default:
                    break;
            }
        }

        // $first_date          = date('Y-m') . '-01';
        // $persentase_presensi = array();

        // for ($i = 9; $i >= 0; $i--) {
        //     $tahun = date('Y', strtotime('-' . $i . ' months', strtotime($first_date)));
        //     $bulan = date('m', strtotime('-' . $i . ' months', strtotime($first_date)));

        //     $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

        //     $bobot_normal = $cek_bobot['nilai'];
        //     $hari_aktif   = _get_hari_aktif($tahun, $bulan);

        //     $this->db->select("a.`level`,
        //                       SUM(IFNULL(d.nilai, 0)) AS nilai,
        //                       COUNT(DISTINCT b.id) AS jml_pegawai ");
        //     $this->db->join("pegawai b", "a.id = b.sekolah_id", "left");
        //     $this->db->join("kehadiran_pegawai c", "b.id = c.pegawai_id AND YEAR(c.jam_masuk) = '$tahun' AND MONTH(c.jam_masuk) = '$bulan'", "left");
        //     $this->db->join("bobot d", "c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang", "left");
        //     $this->db->group_by("a.level");
        //     $stat = $this->db->get("sekolah a");

        //     //SD => ??, SMP => ??, SMA=> ??
        //     $result_sd  = "";
        //     $result_smp = "";
        //     $result_sma = "";

        //     foreach ($stat->result_array() as $key) {
        //         $nilai_maks = (($hari_aktif) * ($bobot_normal)) * $key['jml_pegawai'];

        //         // echo "nilai max:" . $nilai_maks;

        //         if ($key['level'] === 'SD') {
        //             $result_sd .= ROUND((100 * $key['nilai']) / ($nilai_maks), 2);
        //             // $result_smp .= "0,";
        //             // $result_sma .= "0,";
        //         }

        //         if ($key['level'] === 'SMP') {
        //             // $result_sd .= "0,";
        //             $result_smp .= ROUND((100 * $key['nilai']) / ($nilai_maks), 2);
        //             // $result_sma .= "0,";
        //         }

        //         if ($key['level'] === 'SMA') {
        //             // $result_sd .= "0,";
        //             // $result_smp .= "0,";
        //             $result_sma .= ROUND((100 * $key['nilai']) / ($nilai_maks), 2);
        //         }
        //     }

        //     $persentase_presensi[] = array(
        //         'kode'       => $tahun . '-' . $bulan,
        //         'result_sd'  => !empty($result_sd) ? $result_sd : "0",
        //         'result_smp' => !empty($result_smp) ? $result_smp : "0",
        //         'result_sma' => !empty($result_sma) ? $result_sma : "0",
        //     );
        // }

        // $data['persentase_presensi'] = $persentase_presensi;
        $data['tgl_sekarang'] = $this->tgl_indo(date('Y-m-d'));
        // $data['presensi_sd']         = $this->_get_presensi_sekolah('SD');
        // $data['presensi_smp']        = $this->_get_presensi_sekolah('SMP');
        // $data['presensi_sma']        = $this->_get_presensi_sekolah('SMA');
        $data['penjelasan_presensi'] = $this->db->get('bobot');

        $data['page_name']  = 'default';
        $data['page_title'] = 'Signin';

        $this->_page_output($data);

        // $this->load->view('signin', $data);

        //compress_output();
    }

    private function _get_presensi_sekolah($level_sekolah)
    {

        $this->db->select('a.npsn, a.nama,IFNULL(SUM(d.nilai),0) AS nilai');
        $this->db->join('pegawai b', 'a.id = b.sekolah_id', 'left');
        $this->db->join('kehadiran_pegawai c', 'b.id = c.pegawai_id AND YEAR(c.jam_masuk) = ' . date('Y') . ' AND MONTH(c.jam_masuk) = ' . date('n'), 'left');
        $this->db->join('bobot d', 'c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang', 'left');
        $this->db->where('a.level', $level_sekolah);
        $this->db->group_by('a.id');
        $this->db->order_by('IFNULL(SUM(d.nilai),0) DESC');

        return $this->db->get('sekolah a');
    }

    // public function get_pegawai_pensiun()
    // {
    //     $usia_pensiun = $this->config->item('usia_pensiun');

    //     $this->load->library('Datatables');

    //     if (!empty($_POST)) {
    //         $satuanPendidikan = $this->input->post('satuan_pendidikan');
    //         $timeline         = $this->input->post('timeline');

    //         $this->datatables->select('pegawai.nama_lengkap,pegawai.tgl_lahir,sekolah.level,sekolah.nama AS nama_sekolah');
    //         $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id', 'left');
    //         $this->datatables->from('pegawai');
    //         $this->datatables->where('DATE_FORMAT(CURDATE() + INTERVAL ' . $timeline . ' YEAR , \'%yyyy\') =  DATE_FORMAT(pegawai.tgl_lahir + INTERVAL ' . $usia_pensiun . ' YEAR,\'%yyyy\')');

    //         if ($satuanPendidikan !== 'ALL') {
    //             $this->datatables->where('sekolah.level', $satuanPendidikan);
    //         }

    //         echo $this->datatables->generate();
    //     } else {
    //         $data['page_name']  = 'pensiun';
    //         $data['page_title'] = 'PNS yang Pensiun';

    //         $this->_page_output($data);
    //     }
    // }

    public function get_pegawai_pensiun()
    {
        $usia_pensiun = $_ENV['USIA_PENSIUN'];

        $this->load->library('Datatables');

        if (!empty($_POST)) {
            $satuanPendidikan = $this->input->post('satuan_pendidikan');
            $timeline         = $this->input->post('timeline');

            $this->datatables->select('pegawai.nama_lengkap, pegawai.tgl_lahir, sekolah.level, sekolah.nama AS nama_sekolah');
            $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id', 'left');
            $this->datatables->from('pegawai');

            $dateComparison = "DATE_FORMAT(CURDATE() + INTERVAL $timeline YEAR, '%Y') = DATE_FORMAT(pegawai.tgl_lahir + INTERVAL $usia_pensiun YEAR, '%Y')";
            $this->datatables->where($dateComparison);

            if ($satuanPendidikan !== 'ALL') {
                $this->datatables->where('sekolah.level', $satuanPendidikan);
            }

            $kabupaten_id = $this->input->post('kabupaten_id');
            if ($kabupaten_id) {
                $this->datatables->where('sekolah.kabupaten', $kabupaten_id);
            }

            echo $this->datatables->generate();
        } else {
            $data['page_name']  = 'pensiun';
            $data['page_title'] = 'PNS yang Pensiun';
            $data['kabupaten']  = $this->db->get('wilayah_kabupaten');

            $this->_page_output($data);
        }
    }
    // public function get_pegawai_ultah()
    // {
    //     $this->load->library('Datatables');

    //     if (!empty($_POST)) {
    //         $satuanPendidikan = $this->input->post('satuan_pendidikan');
    //         $timeline         = $this->input->post('timeline');

    //         if ($timeline === 'hari-ini') {
    //             $this->datatables->select('pegawai.nama_lengkap,pegawai.tgl_lahir,sekolah.level,sekolah.nama AS nama_sekolah');
    //             $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id','left');
    //             $this->datatables->from('pegawai');
    //             $this->datatables->where('DATE_FORMAT(pegawai.tgl_lahir, \'%m-%d\') = DATE_FORMAT(CURDATE(), \'%m-%d\')');
    //             if ($satuanPendidikan !== 'ALL') {
    //                 $this->datatables->where('sekolah.level',$satuanPendidikan);
    //             }

    //             echo $this->datatables->generate();
    //         } elseif ($timeline === 'besok') {
    //             $this->datatables->select('pegawai.nama_lengkap,pegawai.tgl_lahir,sekolah.level,sekolah.nama AS nama_sekolah');
    //             $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id','left');
    //             $this->datatables->from('pegawai');
    //             $this->datatables->where('DATE_FORMAT(tgl_lahir, \'%m-%d\') = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 DAY), \'%m-%d\')');
    //             if ($satuanPendidikan !== 'ALL') {
    //                 $this->datatables->where('sekolah.level',$satuanPendidikan);
    //             }

    //             echo $this->datatables->generate();
    //         } else {
    //             //minggu depan
    //             $this->datatables->select('pegawai.nama_lengkap,pegawai.tgl_lahir,sekolah.level,sekolah.nama AS nama_sekolah');
    //             $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id','left');
    //             $this->datatables->from('pegawai');
    //             $this->datatables->where('DATE_FORMAT(tgl_lahir, \'%m-%d\') BETWEEN DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), \'%m-%d\') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 13 DAY), \'%m-%d\')');
    //             if ($satuanPendidikan !== 'ALL') {
    //                 $this->datatables->where('sekolah.level',$satuanPendidikan);
    //             }

    //             echo $this->datatables->generate();
    //         }
    //     } else {
    //         $data['page_name']  = 'ultah';
    //         $data['page_title'] = 'PNS yang Ultah';

    //         $this->_page_output($data);
    //     }
    // }

    public function get_pegawai_ultah()
    {
        $this->load->library('Datatables');

        if (!empty($_POST)) {
            $satuanPendidikan = $this->input->post('satuan_pendidikan');
            $timeline         = $this->input->post('timeline');

            $this->datatables->select('pegawai.nama_lengkap, pegawai.tgl_lahir, sekolah.level, sekolah.nama AS nama_sekolah');
            $this->datatables->join('sekolah', 'pegawai.sekolah_id = sekolah.id', 'left');
            $this->datatables->from('pegawai');
            if ($satuanPendidikan !== 'ALL') {
                $this->datatables->where('sekolah.level', $satuanPendidikan);
            }

            if ($timeline === 'hari-ini') {
                $this->datatables->where("DATE_FORMAT(pegawai.tgl_lahir, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')");
            } elseif ($timeline === 'besok') {
                $this->datatables->where("DATE_FORMAT(tgl_lahir, '%m-%d') = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '%m-%d')");
            } else {
                // minggu depan
                $this->datatables->where("DATE_FORMAT(tgl_lahir, '%m-%d') BETWEEN DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 13 DAY), '%m-%d')");
            }

            $kabupaten_id = $this->input->post('kabupaten_id');
            if ($kabupaten_id) {
                $this->datatables->where('sekolah.kabupaten', $kabupaten_id);
            }

            echo $this->datatables->generate();
        } else {
            $data['page_name']  = 'ultah';
            $data['page_title'] = 'PNS yang Ultah';
            $data['kabupaten']  = $this->db->get('wilayah_kabupaten');

            $this->_page_output($data);
        }
    }

    public function pegawai_terakhir_presensi()
    {
        header('content-type: application/json');

        $this->db->select('a.tgl_update,
                           a.jam_masuk,
                           a.jam_pulang,
                           a.status_masuk,
                           a.status_pulang,
						   IFNULL(a.foto_pulang,a.foto_masuk) AS foto,
                           b.nama_lengkap AS nama_pegawai,
                           c.nama AS nama_sekolah');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');
        $this->db->join('sekolah c', 'b.sekolah_id = c.id', 'left');
        $this->db->where('DATE(a.tgl_update)', date('Y-m-d'));
        $this->db->limit(5);
        $this->db->order_by('a.tgl_update DESC');

        $presensi = $this->db->get('kehadiran_pegawai a');

        if ($presensi->num_rows() > 0) {

            $p           = $presensi->row_array();
            $last_update = $p['tgl_update'];

            $old_update = $this->session->userdata('last_update', $last_update);

            if ($last_update !== $old_update) {
                $this->session->set_userdata('last_update', $last_update);

                echo json_encode(
                    array(
                        'presensi_terakhir_div' => $this->load->view('ajax_presensi_terakhir', array('presensi' => $presensi), true),
                        'play_sound'            => 'true',
                    )
                );
            } else {
                echo json_encode(
                    array(
                        'presensi_terakhir_div' => $this->load->view('ajax_presensi_terakhir', array('presensi' => $presensi), true),
                        'play_sound'            => 'false',
                    )
                );
            }
        } else {

            $last_update = '0000:00:00';
            $this->session->set_userdata('last_update', $last_update);

            echo json_encode(
                array(
                    'presensi_terakhir_div' => '<div class="alert alert-warning text-center">Data presensi untuk hari ini belum ada</div>',
                    'play_sound'            => 'false',
                )
            );
        }
    }

    public function get_list_hari_aktif()
    {
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');

        echo _get_list_hari_aktif("", $tahun, $bulan, 'cookie');
    }

    public function show_monitoring()
    {

        $sekolah_id = $this->input->get('sekolah_id');
        echo $this->load->view('table_monitoring', array('sekolah_id' => $sekolah_id), false);
    }

    public function show_prestasi()
    {

        $this->db->select('b.nama_lengkap AS nama_lengkap,
                           CONCAT_WS(" ",c.nama,a.keterangan) AS prestasi,
                           a.kelas,
                           a.tahun,
                           IFNULL(a.sertifikat,"belum_upload") AS sertifikat');
        $this->db->join('siswa b', 'a.siswa_id = b.id', 'left');
        $this->db->join('prestasi c', 'a.prestasi_id = c.id', 'left');
        $this->db->where('b.sekolah_id', $this->input->get('sekolah_id'));
        $this->db->order_by('a.tahun DESC,CONCAT_WS(" ",c.nama,a.keterangan) ASC');
        // $this->db->order_by('CONCAT_WS(" ",c.nama,a.keterangan)', 'ASC');
        $query = $this->db->get('siswa_prestasi a');

        echo $this->load->view('table_prestasi', array('rs' => $query), false);
    }

    public function show_ekstrakulikuler()
    {

        $this->db->select('UPPER(b.nama_lengkap) AS nama_lengkap');
        $this->db->select('GROUP_CONCAT(CONCAT_WS(" - ", c.nama, IF(a.status = "AKTIF",1,0)) ORDER BY c.nama SEPARATOR "<br>") AS ekstrakulikuler');
        $this->db->from('siswa_ekstrakulikuler a');
        $this->db->join('siswa b', 'a.siswa_id = b.id', 'left');
        $this->db->join('ekstrakulikuler c', 'a.ekstrakulikuler_id = c.id', 'left');
        $this->db->where('b.sekolah_id', $this->input->get('sekolah_id'));
        $this->db->group_by('b.id');
        $this->db->order_by('b.nama_lengkap');
        $query = $this->db->get();

        echo $this->load->view('table_ekstrakulikuler', array('rs' => $query), false);
    }

    public function show_alumni()
    {
        $this->db->select('UPPER(nama_lengkap) AS nama_lengkap,
                           YEAR(tanggal_keluar) AS tahun_lulus,
                           jurusan AS jur,
                           perguruan_tinggi AS pt');
        $this->db->from('siswa');
        $this->db->where('sekolah_id', $this->input->get('sekolah_id'));
        $this->db->where('jurusan IS NOT NULL');
        $this->db->where('perguruan_tinggi IS NOT NULL');
        $query = $this->db->get();
        echo $this->load->view('table_alumni', array('query' => $query), false);
    }

    public function download_sibismaklb()
    {
        $jenis      = $this->input->post('jenis');
        $sekolah_id = $this->input->post('sekolah_id');
        $nama_file  = $this->input->post('nama_file');

        $query         = null;
        $report_header = "";

        if ($jenis == 1) {
            $this->db->select('b.nama_lengkap AS nama_lengkap,
                           CONCAT_WS(" ",c.nama,a.keterangan) AS prestasi,
                           a.tahun');
            $this->db->join('siswa b', 'a.siswa_id = b.id', 'left');
            $this->db->join('prestasi c', 'a.prestasi_id = c.id', 'left');
            $this->db->where('b.sekolah_id', $sekolah_id);
            $this->db->order_by('a.tahun DESC,CONCAT_WS(" ",c.nama,a.keterangan) ASC');
            // $this->db->order_by('CONCAT_WS(" ",c.nama,a.keterangan)', 'ASC');
            $query = $this->db->get('siswa_prestasi a');

            $report_header = 'PORTAL SEKOLAH - ' . strtoupper(unslugify($nama_file));
        } elseif ($jenis == 2) {
            $this->db->select('UPPER(b.nama_lengkap) AS nama_lengkap');
            // $this->db->select('GROUP_CONCAT(CONCAT_WS(" - ", c.nama, a.status ) ORDER BY c.nama SEPARATOR "<br>") AS ekstrakulikuler');
            $this->db->select('GROUP_CONCAT(CONCAT_WS(" - ", c.nama,
                CASE
                    WHEN a.status = "AKTIF" THEN "Aktif"
                    WHEN a.status = "NON_AKTIF" THEN "Tidak Aktif"
                    ELSE a.status
                END
            ) ORDER BY c.nama SEPARATOR "<br>") AS ekstrakulikuler');
            $this->db->from('siswa_ekstrakulikuler a');
            $this->db->join('siswa b', 'a.siswa_id = b.id', 'left');
            $this->db->join('ekstrakulikuler c', 'a.ekstrakulikuler_id = c.id', 'left');
            $this->db->where('b.sekolah_id', $sekolah_id);
            $this->db->group_by('b.id');
            $this->db->order_by('b.nama_lengkap');
            $query         = $this->db->get();
            $report_header = 'PORTAL SEKOLAH - ' . strtoupper(unslugify($nama_file));
        } else {
            $this->db->select('UPPER(nama_lengkap) AS nama_lengkap,
                           YEAR(tanggal_keluar) AS tahun_lulus,
                           jurusan AS `Jurusan`,
                           perguruan_tinggi AS `Perguruan Tinggi`');
            $this->db->from('siswa');
            $this->db->where('sekolah_id', $sekolah_id);
            $this->db->where('jurusan IS NOT NULL');
            $this->db->where('perguruan_tinggi IS NOT NULL');
            $query         = $this->db->get();
            $report_header = 'PORTAL SEKOLAH - ' . strtoupper(unslugify($nama_file));
        }

        Modules::run("export/pdf", $nama_file, $query, $report_header);
    }

    public function get_sibismaklb($bentuk_pendidikan = 'sma')
    {

        $data = [];

        if ($bentuk_pendidikan === 'smk') {
            $data['bp'] = 'smk';
        } elseif ($bentuk_pendidikan === 'slb') {
            $data['bp'] = 'slb';
        } else {
            $data['bp'] = 'sma'; //default
        }

        if (!empty($_POST)) {

            $this->load->library('Datatables');

            $this->datatables->select('id,nama');
            $this->datatables->from('sekolah');
            $this->datatables->where('level', strtoupper($data['bp']));

            $kabupaten_id = $this->input->post('kabupaten_id');
            if ($kabupaten_id) {
                $this->datatables->where('kabupaten', $kabupaten_id);
            }

            echo $this->datatables->generate();
        } else {

            $data['tgl_sekarang'] = $this->tgl_indo(date('Y-m-d'));
            $data['kabupaten']    = $this->db->get('wilayah_kabupaten');

            $data['page_name']  = 'sibismaklb';
            $data['page_title'] = 'Signin';

            $this->_page_output($data);
        }
    }

    public function get_monitoring($bentuk_pendidikan = 'sma')
    {
        $data = [];

        if ($bentuk_pendidikan === 'smk') {
            $data['bp'] = 'smk';
        } elseif ($bentuk_pendidikan === 'slb') {
            $data['bp'] = 'slb';
        } else {
            $data['bp'] = 'sma'; //default
        }

        if (!empty($_POST)) {
            $this->load->library('Datatables');

            $this->datatables
                ->select('id,nama')
                ->from('sekolah')
                ->where('level', strtoupper($data['bp']));

            $kabupaten_id = $this->input->post('kabupaten_id');
            if ($kabupaten_id) {
                $this->datatables->where('kabupaten', $kabupaten_id);
            }

            echo $this->datatables->generate();
        } else {
            $data['tgl_sekarang'] = $this->tgl_indo(date('Y-m-d'));
            $data['kabupaten']    = $this->db->get('wilayah_kabupaten');
            $data['page_name']    = 'monitoring';
            $data['page_title']   = 'Monitoring';

            $this->_page_output($data);
        }
    }

    public function get_detail_monitoring()
    {
        $sekolah_id      = $this->input->post('sekolah_id');
        $tahun           = $this->input->post('tahun');
        $bulan           = $this->input->post('bulan');
        $tanggal         = $this->input->post('tanggal');
        $status_mengajar = $this->input->post('status_mengajar'); //semua = 0, mengajar = 1, tidak mengajar = 2

        $this->load->library('Datatables');
        //nama,tanggal,hari, jam_mulai,jam_selesai,kelas,mata_pelajaran,status

        $this->datatables->select('a.id,c.nama_lengkap,
                                   DATE_FORMAT(a.tgl, "%d/%m/%Y") AS tgl,b.hari, b.jam_mulai,b.jam_selesai,
                                   d.nama_kelas,e.nama AS matapelajaran,
                                   IF(
                                    (a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL)
                                     AND a.foto_mulai IS NOT NULL
                                     AND a.foto_selesai IS NOT NULL
                                     AND a.verifikasi != "tolak","terima","tolak") AS status');

        $this->datatables->from('guru_mengajar a');

        $this->datatables->join('jadwal_mengajar b', 'a.jadwal_mengajar_id = b.id', 'left');
        $this->datatables->join('pegawai c', 'b.pegawai_id = c.id', 'left');
        $this->datatables->join('kelas d', 'b.kelas_id = d.id', 'left');
        $this->datatables->join('matapelajaran e', 'b.matapelajaran_id = e.id', 'left');

        $this->datatables->where('c.sekolah_id', $sekolah_id);
        $this->datatables->where('YEAR(a.tgl)', $tahun);
        $this->datatables->where('MONTH(a.tgl)', $bulan);
        if ($tanggal != 0) {
            $this->datatables->where('DAY(a.tgl)', $tanggal);
        }

        if ($status_mengajar == 0) {
        } elseif ($status_mengajar == 1) {
            // $this->datatables->where('a.verifikasi = "terima"');
            $this->datatables->where('((a.dokumentasi IS NOT NULL OR a.uraian IS NOT NULL) AND a.foto IS NOT NULL AND a.verifikasi != "tolak")');
        } else {
            $this->datatables->where('((a.dokumentasi IS NULL AND a.uraian IS NULL) OR a.foto IS NULL OR a.verifikasi = "tolak")');
        }

        echo $this->datatables->generate();
    }
}
