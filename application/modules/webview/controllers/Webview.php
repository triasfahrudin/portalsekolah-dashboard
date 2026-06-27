<?php

class Webview extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->helper(array('url', 'libs', 'form', 'alert'));
        $this->load->database();

        $this->load->libraries(array('form_validation', 'ciqrcode', 'session', 'alert'));

    }

    public function _page_output($data = null)
    {
        $this->load->view('master_view.php', $data);
    }

    public function index()
    {
        $param     = $this->uri->segment(3);
        $exp_param = explode('::', $param);

        // echo $param;

        //token_login::latitude::longitude
        $token_login    = $exp_param[0];
        $user_latitude  = $exp_param[1];
        $user_longitude = $exp_param[2];

        $cek_token_login = cek_token_login($token_login);

        $user_level = $cek_token_login['level'];
        $user_id    = $cek_token_login['id'];

        $data_qr = "NULLED_QR_CODE";

        $data      = array();
        $user_time = date("Y-m-d H:i:s");

        if ($user_level === 'SUPERUSER') {

            //profile
            //ganti password

            $data['page_name'] = 'beranda/superuser';

        } elseif ($user_level === 'DINAS') {

            //profile
            //ganti password
            $data_qr           = simple_crypt($token_login . '::' . $user_time . '::-1', 'e');
            $data['page_name'] = 'beranda/dinas';

        } elseif ($user_level === 'OPERATOR') {
            //profile
            //ganti password
            $data['user_latitude']  = $user_latitude;
            $data['user_longitude'] = $user_longitude;
            $data_qr                = simple_crypt($token_login . '::' . $user_time . '::-1', 'e');
            $data['page_name']      = 'beranda/operator';

        } elseif ($user_level === 'KEPSEK') {

            //presensi
            //profile
            //acc ijin guru
            //ganti password

			$data['status_presensi'] = '';

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_m = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
            if ($cek_m->num_rows() > 0) {
                $c = $cek_m->row_array();
                $data['status_presensi'] .= 'MASUK:' . $c['status_masuk'] . ' (' . $c['jam_masuk'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'MASUK: BELUM PRESENSI<br/>';
            }

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_p = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_pulang)' => date("Y-m-d")));
            if ($cek_p->num_rows() > 0) {
                $c = $cek_p->row_array();
                $data['status_presensi'] .= 'PULANG:' . $c['status_pulang'] . ' (' . $c['jam_pulang'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'PULANG: BELUM PRESENSI<br/>';
            }

            $data['jarak'] = _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude);

            $data['max_jarak_presensi'] = get_settings('max_jarak_presensi_dalam_meter');
			
            $data['page_name'] = 'beranda/kepsek';

        } elseif ($user_level === 'GURU') {
            //presensi
            //profile
            //jadwal mengajar (=> kelas mulai, presensi siswa, kelas selesai )
            //acc ijin siswa
            //ganti password

            $data['status_presensi'] = '';

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_m = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
            if ($cek_m->num_rows() > 0) {
                $c = $cek_m->row_array();
                $data['status_presensi'] .= 'MASUK:' . $c['status_masuk'] . ' (' . $c['jam_masuk'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'MASUK: BELUM PRESENSI<br/>';
            }

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_p = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_pulang)' => date("Y-m-d")));
            if ($cek_p->num_rows() > 0) {
                $c = $cek_p->row_array();
                $data['status_presensi'] .= 'PULANG:' . $c['status_pulang'] . ' (' . $c['jam_pulang'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'PULANG: BELUM PRESENSI<br/>';
            }

            $data['jarak'] = _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude);

            $data['max_jarak_presensi'] = get_settings('max_jarak_presensi_dalam_meter');
            $data['page_name']          = 'beranda/guru';

        } elseif ($user_level === 'PEGAWAI') {
            //presensi
            //profile
            //ganti password
            $data['status_presensi'] = '';

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_m = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
            if ($cek_m->num_rows() > 0) {
                $c = $cek_m->row_array();
                $data['status_presensi'] .= 'MASUK:' . $c['status_masuk'] . ' (' . $c['jam_masuk'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'MASUK: BELUM PRESENSI<br/>';
            }

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_p = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_pulang)' => date("Y-m-d")));
            if ($cek_p->num_rows() > 0) {
                $c = $cek_p->row_array();
                $data['status_presensi'] .= 'PULANG:' . $c['status_pulang'] . ' (' . $c['jam_pulang'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'PULANG: BELUM PRESENSI<br/>';
            }

            $data['jarak'] = _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude);

            $data['max_jarak_presensi'] = get_settings('max_jarak_presensi_dalam_meter');

            $data['page_name'] = 'beranda/pegawai';
        } elseif ($user_level === 'PEGAWAI-DINAS') {

            $data['status_presensi'] = '';

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_m = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_masuk)' => date("Y-m-d")));
            if ($cek_m->num_rows() > 0) {
                $c = $cek_m->row_array();
                $data['status_presensi'] .= 'MASUK:' . $c['status_masuk'] . ' (' . $c['jam_masuk'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'MASUK: BELUM PRESENSI<br/>';
            }

            $this->db->select('status_masuk,status_pulang, TIME(jam_masuk) AS jam_masuk,TIME(jam_pulang) AS jam_pulang');
            $cek_p = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $user_id, 'DATE(jam_pulang)' => date("Y-m-d")));
            if ($cek_p->num_rows() > 0) {
                $c = $cek_p->row_array();
                $data['status_presensi'] .= 'PULANG:' . $c['status_pulang'] . ' (' . $c['jam_pulang'] . ')<br/>';
            } else {
                $data['status_presensi'] .= 'PULANG: BELUM PRESENSI<br/>';
            }

            $data['jarak'] = _cek_lokasi_pegawai($user_id, $user_latitude, $user_longitude);

            $data['max_jarak_presensi'] = get_settings('max_jarak_presensi_dalam_meter');

            $data['page_name'] = 'beranda/pegawai_dinas';

        } elseif ($user_level === 'SISWA') {
            //presensi
            //profile
            //ganti password
            $data_qr           = simple_crypt($token_login . '::' . $user_time . '::-1', 'e');
            $data['page_name'] = 'beranda/siswa';
        } else {
            $data_qr           = simple_crypt($data_qr, 'e');
            $data['page_name'] = 'beranda/default';
        }

        $data['index_param'] = $param;
        $data['token_login'] = $token_login;
        $data['user_id']     = $user_id;

        $this->_page_output($data);
    }

}
