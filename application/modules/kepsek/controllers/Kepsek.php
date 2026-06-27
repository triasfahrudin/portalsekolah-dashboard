<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kepsek extends MX_Controller
{
    private $user_id;
    private $base_breadcrumbs;
    private $sekolah_id;
    private $logo;

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
        $this->logo       = $this->session->userdata('user_logo');

        $this->base_breadcrumbs = '/kepsek';

        if ($user_level !== 'KEPSEK') {
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

    public function load_events()
    {
        header('content-type: application/json');

        $pegawai_id = $this->input->post('pegawai_id');
        // $bulan      = $this->input->post('filter_bulan');
        // $tahun      = $this->input->post('filter_tahun');

        $data_weekend = array();

        $weekend = $this->db->get_where('dates', array('weekend' => 1));

        foreach ($weekend->result_array() as $row) {
            $data_weekend[] = array(
                "name"      => "Libur",
                "date"      => $row['fulldate'],
                "type"      => "holiday",
                "everyYear" => false,
            );
        }

        $data_libur = array();
        $libur      = $this->db->get('hari_libur');

        foreach ($libur->result_array() as $row) {
            $data_libur[] = array(
                "name"      => $row['keterangan'],
                "date"      => $row['tgl'],
                "type"      => "holiday",
                "everyYear" => false,
            );
        }

        $data_presensi = array();

        $this->db->select('status_masuk,status_pulang,DATE(tgl_update) AS tgl_update');
        $presensi = $this->db->get_where('kehadiran_pegawai', array('pegawai_id' => $pegawai_id));

        foreach ($presensi->result_array() as $row) {
            $data_presensi[] = array(
                "name"      => "Masuk:" . $row['status_masuk'],
                "date"      => $row['tgl_update'],
                "type"      => strtolower($row['status_masuk']),
                "everyYear" => false,
            );

            $data_presensi[] = array(
                "name"      => "Pulang:" . $row['status_pulang'],
                "date"      => $row['tgl_update'],
                "type"      => strtolower($row['status_pulang']),
                "everyYear" => false,
            );
        }

        echo json_encode(array('events' => array_merge($data_weekend, $data_libur, $data_presensi)));
    }

    public function presensi_siswa()
    {
        if (!empty($_POST)) {

            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $hari_aktif = _get_hari_aktif($tahun, $bulan);

            $keterangan = '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari<br/>';
            $keterangan .= 'Download laporan rekap untuk bulan terpilih <strong><a class="text-danger" href="' . site_url('kepsek/download-rekap-presensi-siswa/' . $tahun . '/' . $bulan) . '">disini</a></strong>';
            $data['keterangan'] = $keterangan;

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
                                                WHERE a.sekolah_id = '$this->sekolah_id' AND a.aktif = 'YA'
                                                GROUP BY a.id
                                                ORDER BY b.hadir DESC, a.nama_lengkap ASC");
        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        // $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Kehadiran Siswa', $this->base_breadcrumbs . '/presensi-siswa');

        // $data['sekolah']    = $sekolah;
        $data['page_name']  = 'kehadiran_siswa';
        $data['page_title'] = 'Kehadiran Siswa';
        $this->_page_output($data);
    }

    //report
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

    public function download_rekap_presensi_siswa($filter_tahun, $filter_bulan)
    {

        $cek = $this->db->get_where('sekolah', array('id' => $this->sekolah_id))->row_array();

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
                                    WHERE a.sekolah_id = '$this->sekolah_id' AND a.aktif = 'YA'
                                    GROUP BY a.id
                                    ORDER BY b.hadir DESC, a.nama_lengkap ASC");

        $report_header = 'Laporan Presensi Siswa ' . $cek['nama'] . '&nbsp;(' . $cek['npsn'] . '&nbsp;)<br/>';
        $report_header .= 'Bulan ' . $filter_bulan . '&nbsp;Tahun ' . $filter_tahun;

        Modules::run("export/pdf", slugify('presensi-siswa-' . $cek['nama'] . '-' . $filter_tahun . '-' . $filter_bulan), $presensi, $report_header);
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

    public function presensi_pegawai()
    {

        if (!empty($_POST)) {

            // $bulan = $this->input->post('filter_bulan');
            // $tahun = $this->input->post('filter_tahun');

            // $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            // $bobot_normal = $cek_bobot['nilai'];
            // $hari_aktif   = _get_hari_aktif($tahun, $bulan);

            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            $bobot_normal = $cek_bobot['nilai'];
            $hari_aktif   = _get_hari_aktif($tahun, $bulan);

            $keterangan = '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong><br/>';
            $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="#" onclick="download_file(\'' . site_url('kepsek/download-rekap-presensi-pegawai/' . $tahun . '/' . $bulan) . '\',\'' . site_url('kepsek/download-rekap-presensi-pegawai/' . $tahun . '/' . $bulan) . '/xls\'); return false" >disini</a></strong><br/>';
            $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('kepsek/download-rekap-harian-pegawai', $tahun, $bulan);

            $data['keterangan'] = $keterangan;

            $this->db->select('a.id,
                               a.nuptk,
                               a.nama_lengkap,
                               a.jabatan,
                               IFNULL(SUM(c.nilai),0) AS nilai_presensi');
            $this->db->join('kehadiran_pegawai b', 'a.id = b.pegawai_id  AND YEAR(b.jam_masuk) = ' . $tahun . ' AND MONTH(b.jam_masuk) = ' . $bulan, 'left');
            $this->db->join('bobot c', 'b.status_masuk = c.status_masuk AND b.status_pulang = c.status_pulang', 'left');
            $this->db->where('a.sekolah_id', $this->sekolah_id);
            $this->db->where('a.aktif', 'YA');
            $this->db->group_by('a.id');
            $this->db->order_by('IFNULL(SUM(c.nilai),0) DESC, a.nama_lengkap ASC');
            $data['presensi'] = $this->db->get('pegawai a');

            // $data['keterangan'] = 'Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong>';

        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        // $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Presensi Pegawai', $this->base_breadcrumbs . '/presensi-pegawai');

        $data['page_name']  = 'kehadiran_pegawai';
        $data['page_title'] = 'Kehadiran Pegawai';
        $this->_page_output($data);
    }

    public function download_rekap_harian_pegawai($filter_tahun, $filter_bulan, $filter_hari, $ext = 'pdf')
    {

        $data = array();

        $this->db->select('a.id,
						   a.nama,
						   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
						   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $this->db->where('a.id', $this->sekolah_id);
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
        $this->db->where("a.sekolah_id", $this->sekolah_id);

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

    public function download_rekap_presensi_pegawai($filter_tahun, $filter_bulan, $ext = 'pdf')
    {

        $data = array();

        $this->db->select('a.id AS sekolah_id,
		                   a.nama AS nama_sekolah,
						   b.nama AS nama_kecamatan,
		                   c.nama AS nama_kabupaten');
        $this->db->join('wilayah_kecamatan b', 'a.kecamatan = b.id', 'left');
        $this->db->join('wilayah_kabupaten c', 'b.kabupaten_id = c.id', 'left');
        $cek = $this->db->get_where('sekolah a', array('a.id' => $this->sekolah_id))->row_array();

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

        $this->db->where('a.sekolah_id', $this->sekolah_id);
        $this->db->where('a.aktif', 'YA');
        $this->db->group_by('a.id');
        // $this->db->order_by('a.nama_lengkap ASC');

        if ($ext === 'pdf') {

            $this->db->order_by("a.nama_lengkap ASC");
            $presensi = $this->db->get('pegawai a');

            Modules::run("export/pdf_rekap_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
        } else {

            $this->db->order_by("a.nama_lengkap DESC");
            $presensi = $this->db->get('pegawai a');

            Modules::run("export/xls_rekap_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
        }
    }

    // public function profile()
    // {

    //     try {
    //         $this->load->library(array('grocery_CRUD'));
    //         $crud = new Grocery_CRUD();

    //         $crud->set_table('pegawai');
    //         $crud->set_subject('Profile');

    //         $crud->required_fields('nama_lengkap');
    //         $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');
    //         $crud->field_type('tgl_update', 'hidden');
    //         $crud->field_type('password', 'hidden');
    //         $crud->field_type('terakhir_login', 'readonly');
    //         $crud->field_type('nuptk', 'readonly');
    //         $crud->field_type('jabatan', 'readonly');
    //         $crud->field_type('status_pegawai', 'readonly');
    //         $crud->field_type('token_id', 'hidden');
    //         $crud->field_type('token_login', 'hidden');
    //         $crud->field_type('wali_kelas', 'hidden');
    //         $crud->field_type('dev_unique_id', 'hidden');
    //         $crud->field_type('aktif', 'hidden');
    //         $crud->field_type('id', 'hidden');
    //         $crud->field_type('nip', 'readonly');
    //         $crud->field_type('nama_lengkap', 'readonly');
    //         $crud->field_type('tempat_lahir', 'readonly');
    //         $crud->field_type('tgl_lahir', 'readonly');

    //         $crud->field_type('nik', 'readonly');
    //         $crud->field_type('jk', 'readonly');
    //         $crud->field_type('agama', 'readonly');
    //         $crud->field_type('sk_pengangkatan', 'readonly');
    //         $crud->field_type('tmt_pengangkatan', 'readonly');
    //         $crud->field_type('alamat', 'readonly');

    //         $crud->field_type('email', 'readonly');
    //         $crud->field_type('telp', 'readonly');

    //         $crud->field_type('aktif', 'hidden');
    //         $crud->field_type('dinas_pangkat', 'hidden');
    //         $crud->field_type('dinas_unit_kerja', 'hidden');
    //         $crud->field_type('dinas_provinsi', 'hidden');
    //         $crud->field_type('dinas_kabupaten', 'hidden');

    //         $crud->display_as('nuptk', 'NUPTK');
    //         $crud->set_field_upload('foto', 'uploads');

    //         $crud->set_relation('sekolah_id', 'sekolah', 'nama');
    //         $crud->display_as('sekolah_id', 'Unit Kerja');
    //         $crud->field_type('sekolah_id', 'readonly');

    //         $crud->callback_after_insert(function ($post_array, $primary_key) {
    //         });

    //         $crud->callback_after_update(function ($post_array, $primary_key) {

    //             $this->db->where('id', $primary_key);
    //             $this->db->update(
    //                 'user',
    //                 array(
    //                     'tgl_update' => date('Y-m-d H:i:s'),
    //                 )
    //             );
    //         });

    //         $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
    //         $this->breadcrumbs->push('Profil', $this->base_breadcrumbs . '/profile');

    //         $state = $crud->getState();
    //         if ($state === 'insert_validation') {
    //             // $crud->set_rules('npsn', 'NPSN', 'is_unique[sekolah.npsn]|required');
    //             // $crud->set_rules('email', 'Email', 'is_unique[sekolah.email]|valid_email');
    //         } elseif ($state === 'update_validation') {
    //             $crud->set_rules('email', 'Email', 'valid_email');
    //         } elseif ($state === 'add') {
    //             // $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-sekolah/add');
    //         } elseif ($state === 'edit') {

    //             $curr_user_id = $this->uri->segment(4);

    //             if ($curr_user_id != $this->user_id) {
    //                 redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
    //             }

    //             $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
    //         } elseif ($state === 'list') {
    //             redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
    //         }

    //         $crud->unset_back_to_list();
    //         $extra = array('page_title' => 'Kelola Profil');

    //         $output = $crud->render();

    //         $output = array_merge((array) $output, $extra);

    //         $this->_page_output($output);
    //     } catch (Exception $e) {
    //         show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    //     }
    // }

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
        //             redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
        //         }

        //         $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/profile/edit');
        //     } elseif ($state === 'list') {
        //         redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
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
        //         "url"                => site_url('/kepsek/profile/edit/' . $uri_segment_kode . '/'),
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

            $base_breadcrumbs = 'kepsek/profile';

            $state = $crud->getState();
            if ($state === 'insert_validation') {
                // Custom validation rules for insert
            } elseif ($state === 'update_validation') {
                $crud->set_rules('email', 'Email', 'valid_email');
            } elseif ($state === 'edit') {
                $curr_user_id = $this->uri->segment(4);
                if ($curr_user_id != $this->user_id) {
                    redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
                }
                $this->breadcrumbs->push('Ubah', $base_breadcrumbs . '/edit');
            } elseif ($state === 'list') {
                redirect(site_url('kepsek/profile/edit/' . $this->user_id), 'reload');
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
                redirect(site_url('kepsek/dokumen'), 'reload');
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
                redirect(site_url('kepsek/dokumen'), 'reload');
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
                        redirect(site_url('kepsek/ganti-password'), 'reload');
                    } else {
                        $pass_baru   = $this->input->post('pass_baru');
                        $pass_ulangi = $this->input->post('pass_ulangi');

                        if ($pass_baru !== $pass_ulangi) {
                            $this->alert->set('alert-danger', 'Password baru & ulangan harus sama');
                            redirect(site_url('kepsek/ganti-password'), 'reload');
                        } else {
                            $this->db->where('id', $this->user_id);
                            $this->db->update('pegawai', array('password' => password_hash($pass_baru, PASSWORD_DEFAULT)));

                            $this->alert->set('alert-success', 'Password berhasil diupdate');
                            redirect(site_url('kepsek/ganti-password'), 'reload');
                        }
                    }
                } else {

                    $this->alert->set('alert-danger', 'Password Lama Salah');
                    redirect(site_url('kepsek/ganti-password'), 'reload');
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

    /**
     * The function "proses_izin_pegawai" updates the status of a leave request for an employee and
     * performs corresponding actions based on the status.
     *
     * @param izin_id The parameter "izin_id" is the ID of the permission request for an employee. It
     * is used to identify the specific permission request in the database.
     * @param status_izin The parameter `` is the status of the employee's leave request.
     * It can have the values 'TERIMA' (accepted) or any other value (rejected).
     */
    public function proses_izin_pegawai($izin_id, $status_izin)
    {

        $status_izin = strtoupper($status_izin);
        $izin_id     = simple_crypt($izin_id, 'd');

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
                                'status_masuk'  => 'IZIN-SAKIT',
                                'status_pulang' => 'IZIN-SAKIT',
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

        redirect(site_url('kepsek/izin_pegawai'), 'reload');
    }

    /**
     * The function `pengajuan_izin` is used to handle the submission of a leave request form,
     * including validation, file upload, and database insertion.
     */
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
                        redirect(site_url('kepsek/pengajuan-izin'), 'reload');
                    } else {
                        $success   = $this->upload->data();
                        $file_name = $success['file_name'];

                        $data['file'] = $file_name;
                    }
                }

                $this->db->insert('izin_pegawai', $in);

                $this->alert->set('alert-success', 'Data permohonan izin berhasil diajukan');
                redirect(site_url('kepsek/pengajuan-izin'), 'reload');
            }
        }

        $this->db->select('a.tgl_pengajuan, a.tgl_izin,
                           a.jenis_izin, IFNULL(a.keterangan,"-") AS keterangan, a.`file`,a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id', 'left');
        $this->db->join('sekolah c', 'b.sekolah_id = c.id', 'left');
        $this->db->order_by('a.tgl_izin DESC');
        $this->db->where('b.id', $this->user_id);
        $data['izin'] = $this->db->get('izin_pegawai a');

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Pengajuan Izin', $this->base_breadcrumbs . '/pengajuan-izin');

        $data['keterangan'] = 'Anda bisa mengajukan permohonan izin kepada dinas sekaligus melihat riwayat permohonan anda';
        $data['page_name']  = 'pengajuan_izin';
        $data['page_title'] = 'Pengajuan Izin';

        $this->_page_output($data);
    }

    /**
     * The function "izin_pegawai" retrieves and displays pending leave requests from employees in a
     * school.
     */
    public function izin_pegawai()
    {

        $data = array();

        $this->db->select('a.id,b.nuptk, b.nama_lengkap, c.nama AS sekolah, a.tgl_pengajuan, a.tgl_izin,
                           a.jenis_izin, a.keterangan, a.`file`,a.status_izin');
        $this->db->join('pegawai b', 'a.pegawai_id = b.id AND b.jabatan <> "Kepala Sekolah"', 'left', false);
        $this->db->join('sekolah c', 'b.sekolah_id = c.id', 'left');
        $this->db->where('a.status_izin', 'PENDING');
        $this->db->where('c.id', $this->sekolah_id);
        $data['izin'] = $this->db->get('izin_pegawai a');

        $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
        $this->breadcrumbs->push('Izin Pegawai', $this->base_breadcrumbs . '/izin_pegawai');

        $data['keterangan'] = 'Berikut ini adalah data permohonan izin dari Pegawai';
        $data['page_name']  = 'izin_pegawai';
        $data['page_title'] = 'Izin Pegawai';

        $this->_page_output($data);
    }

    /**
     * The function retrieves and displays the attendance details of an employee for a specific month
     * and year.
     */
    public function detail_presensi_pegawai()
    {
        header('content-type: application/json');

        $data = array();

        $pegawai_id = $this->input->post('pegawai_id');
        $bulan      = $this->input->post('filter_bulan');
        $tahun      = $this->input->post('filter_tahun');

        $this->db->select(
            "a.fulldate,IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR','AKTIF')) AS libur,
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

    /**
     * The function `download_presensi_pegawai` retrieves and exports the attendance records of a
     * specific employee in a PDF or Excel format.
     *
     * @param pegawai_id The `pegawai_id` parameter is the ID of the employee for whom the attendance
     * report is being generated.
     * @param filter_tahun The parameter "filter_tahun" is used to filter the data by year. It
     * specifies the year for which the data should be retrieved.
     * @param filter_bulan The parameter "filter_bulan" is used to filter the month for which the
     * attendance data is being downloaded. It represents the month in numeric format, where 1
     * represents January, 2 represents February, and so on.
     * @param ext The "ext" parameter is an optional parameter that specifies the file extension for
     * the downloaded file. By default, it is set to 'pdf'. However, you can pass 'xls' as the value to
     * download the file in Excel format instead of PDF.
     */
    public function download_presensi_pegawai($pegawai_id, $filter_tahun, $filter_bulan, $ext = 'pdf')
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
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_masuk,'-'))) AS `Jam Masuk`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(c.jam_pulang,'-'))) AS `Jam Pulang`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(CONCAT(c.status_masuk,'-',c.status_pulang),'-'))) AS `Presensi`,
            IF(a.dayofweek = 7,'LIBUR',IF(COUNT(b.id) > 0,'LIBUR',IFNULL(d.nilai,0))) AS `Nilai`"
        );

        $this->db->join("hari_libur b", "a.fulldate = b.tgl", "left");
        $this->db->join("kehadiran_pegawai c", "a.fulldate = DATE(c.jam_masuk) AND c.pegawai_id = $pegawai_id", "left");
        $this->db->join("bobot d", "c.status_masuk = d.status_masuk AND c.status_pulang = d.status_pulang", "left");
        $this->db->where("YEAR(a.fulldate)", $filter_tahun);
        $this->db->where("MONTH(a.fulldate)", $filter_bulan);
        $this->db->group_by("a.fulldate");

        if ($ext === 'pdf') {
            $this->db->order_by("a.fulldate ASC");
            $presensi = $this->db->get("dates a");

            Modules::run("export/pdf_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
        } else {

            $this->db->order_by("a.fulldate DESC");
            $presensi = $this->db->get("dates a");

            Modules::run("export/xls_presensi_pegawai", $data, $filter_tahun, bulan($filter_bulan), $presensi);
        }
    }

    public function detail_guru_mapel()
    {

        $pegawai_id = $this->uri->segment(3);

        // echo $pegawai_id;

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('guru_mapel');
            $crud->set_subject('Kelola Mata Pelajaran');
            $crud->where('pegawai_id', $pegawai_id);

            $crud->columns('matapelajaran_id', 'kelas_id');
            $crud->field_type('pegawai_id', 'hidden', $pegawai_id);
            $crud->display_as('kelas_id', 'Kelas');
            $crud->display_as('matapelajaran_id', 'Mata Pelajaran');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            // $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            // $this->breadcrumbs->push('Kelola Guru Mata Pelajaran', $this->base_breadcrumbs . '/kelola-guru-mapel');
            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Guru Wali Kelas & Mapel', $this->base_breadcrumbs . '/kelola-walikelas');

            $this->breadcrumbs->push('Matapelajaran', $this->base_breadcrumbs . '/detail-guru-mapel/' . $pegawai_id);

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/detail-guru-mapel/' . $pegawai_id . '/add');
            } elseif ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/detail-guru-mapel/' . $pegawai_id . '/edit');
            }

            $matpel = $this->db->get_where('matapelajaran', array('sekolah_id' => $this->sekolah_id));

            $arr_matpel = array();
            foreach ($matpel->result_array() as $row) {
                $arr_matpel[$row['id']] = $row['nama'];
            }

            $extra = array('page_title' => 'Kelola Guru Mata pelajaran');

            $keterangan = "";

            if (count($arr_matpel) > 0) {
                $crud->field_type('matapelajaran_id', 'dropdown', $arr_matpel);
            } else {
                $crud->field_type('matapelajaran_id', 'readonly');
                $keterangan .= '<br/>Matapelajaran belum dimasukkan oleh Operator';
            }

            // $crud->field_type('matapelajaran_id', 'dropdown', $arr_matpel);

            //kelas
            // $pegawai = $this->db->get_where('pegawai', array('id' => $pegawai_id))->row_array();

            // $exp_walikelas = explode(',', $pegawai['wali_kelas']);

            // $this->db->where_in('id', $exp_walikelas);
            $this->db->order_by('nama_kelas', 'asc');
            $kelas     = $this->db->get_where('kelas', array('sekolah_id' => $this->sekolah_id));
            $arr_kelas = array();
            foreach ($kelas->result_array() as $row) {
                $arr_kelas[$row['id']] = $row['nama_kelas'];
            }

            if (count($arr_kelas) > 0) {
                $crud->field_type('kelas_id', 'multiselect', $arr_kelas);
            } else {
                $crud->field_type('kelas_id', 'readonly');
                $keterangan .= '<br/>Kelas belum dimasukkan oleh Operator';
            }

            if (count($arr_matpel) == 0 || count($arr_kelas) == 0) {
                $extra['keterangan'] = $keterangan;
            }

            // $crud->unset_add();
            // $crud->unset_delete();
            // $crud->unset_edit();

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kelola_guru_mapel()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Guru Mata Pelajaran');
            $crud->where('sekolah_id', $this->sekolah_id);
            $crud->where('jabatan', 'Guru Mapel');

            // $crud->required_fields('wali_kelas');

            $crud->columns('nama_lengkap', 'nuptk', 'guru_mapel');
            $crud->field_type('sekolah_id', 'hidden');
            $crud->field_type('jabatan', 'hidden');
            $crud->field_type('wali_kelas', 'hidden');
            $crud->field_type('status_pegawai', 'hidden');
            $crud->field_type('nuptk', 'readonly');
            $crud->field_type('password', 'hidden');
            $crud->field_type('nama_lengkap', 'readonly');
            $crud->field_type('jk', 'hidden');
            $crud->field_type('alamat', 'hidden');
            $crud->field_type('email', 'hidden');
            $crud->field_type('telp', 'hidden');
            $crud->field_type('foto', 'hidden');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('token_id', 'hidden');

            $crud->display_as('nuptk', 'NUPTK');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('guru_mapel', function ($value, $row) {
                return '<a href="' . site_url('kepsek/detail-guru-mapel/' . $row->id) . '">Kelola</a>';
            });

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Guru Mata Pelajaran', $this->base_breadcrumbs . '/kelola-guru-mapel');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
            } elseif ($state === 'edit') {
            }

            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_edit();

            $extra  = array('page_title' => 'Kelola Guru Mata pelajaran');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    /**
     * Fungsi "kelola_walikelas" digunakan untuk mengelola dan menampilkan daftar guru yang
     * Ditugaskan sebagai guru kelas dan guru mata pelajaran.
     *
     * @return output dari metode '->render()', yang merupakan array yang berisi rendered
     * HTML output dari antarmuka CRUD.
     */
    public function kelola_walikelas()
    {
        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('pegawai');
            $crud->set_subject('Wali Kelas');
            $crud->where('sekolah_id', $this->sekolah_id);
            $crud->where('jabatan', 'Guru Mapel');

            // $crud->required_fields('wali_kelas');

            $crud->columns('nama_lengkap', 'nuptk', 'wali_kelas', 'guru_mapel');
            $crud->field_type('sekolah_id', 'hidden');
            $crud->field_type('jabatan', 'hidden');
            $crud->field_type('status_pegawai', 'hidden');
            $crud->field_type('nuptk', 'readonly');
            $crud->field_type('password', 'hidden');
            $crud->field_type('nama_lengkap', 'readonly');
            $crud->field_type('jk', 'hidden');
            $crud->field_type('alamat', 'hidden');
            $crud->field_type('email', 'hidden');
            $crud->field_type('telp', 'hidden');
            $crud->field_type('foto', 'hidden');
            $crud->field_type('tgl_update', 'hidden');
            $crud->field_type('terakhir_login', 'hidden');
            $crud->field_type('token_id', 'hidden');
            $crud->field_type('dev_unique_id', 'hidden');
            $crud->field_type('token_login', 'hidden');
            $crud->field_type('id', 'hidden');
            $crud->field_type('sk_pengangkatan', 'hidden');
            $crud->field_type('tmt_pengangkatan', 'hidden');
            $crud->field_type('nip', 'hidden');
            $crud->field_type('tempat_lahir', 'hidden');
            $crud->field_type('tgl_lahir', 'hidden');
            $crud->field_type('nik', 'hidden');
            $crud->field_type('agama', 'hidden');

            $crud->field_type('aktif', 'hidden');
            $crud->field_type('dinas_pangkat', 'hidden');
            $crud->field_type('dinas_unit_kerja', 'hidden');
            $crud->field_type('dinas_provinsi', 'hidden');
            $crud->field_type('dinas_kabupaten', 'hidden');

            $crud->display_as('nuptk', 'NUPTK');

            $crud->callback_after_insert(function ($post_array, $primary_key) {
            });

            $crud->callback_after_update(function ($post_array, $primary_key) {
            });

            $crud->callback_column('wali_kelas', function ($value, $row) {
                return '<a href="' . site_url('kepsek/kelola-walikelas/edit/' . $row->id) . '">Kelola</a>';
            });

            $crud->callback_column('guru_mapel', function ($value, $row) {
                return '<a href="' . site_url('kepsek/detail-guru-mapel/' . $row->id) . '">Kelola</a>';
            });

            $this->db->order_by('nama_kelas', 'asc');
            $kelas = $this->db->get_where('kelas', array('sekolah_id' => $this->sekolah_id));

            if ($kelas->num_rows() > 0) {
                $arr_kelas = array();
                foreach ($kelas->result_array() as $row) {
                    $arr_kelas[$row['id']] = $row['nama_kelas'];
                }

                $crud->field_type('wali_kelas', 'multiselect', $arr_kelas);
            } else {
                $crud->callback_edit_field('wali_kelas', array($this, 'walikelas_msg_not_found'));
            }

            $this->breadcrumbs->push('Beranda', $this->base_breadcrumbs);
            $this->breadcrumbs->push('Kelola Guru Wali Kelas & Mapel', $this->base_breadcrumbs . '/kelola-walikelas');

            $state = $crud->getState();
            if ($state === 'insert_validation') {
            } elseif ($state === 'update_validation') {
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', $this->base_breadcrumbs . '/kelola-walikelas/add');
            } elseif ($state === 'edit') {

                //cek apakah pegawai ini satu sekolah?
                $primary_key = $this->uri->segment(4);

                $cek = $this->db->get_where('pegawai', array('id' => $primary_key, 'sekolah_id' => $this->sekolah_id));

                if ($cek->num_rows() == 0) {
                    redirect(site_url('kepsek/kelola-walikelas'), 'reload');
                } else {
                    $pegawai = $cek->row_array();
                    //harus  guru
                    if ($pegawai['jabatan'] !== 'Guru Mapel') {
                        redirect(site_url('kepsek/kelola-walikelas'), 'reload');
                    }
                }

                $this->breadcrumbs->push('Ubah', $this->base_breadcrumbs . '/kelola-walikelas/add');
            }

            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_read();
            // $crud->unset_edit();

            $extra  = array('page_title' => 'Kelola Wali Kelas');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function walikelas_msg_not_found($value = false, $primary_key = false)
    {
        return 'Data kelas untuk sekolah ini belum dimasukkan oleh Operator!';
    }

    public function set_verifikasi_dokumen()
    {
        $verifikasi       = $this->uri->segment(3);
        $guru_mengajar_id = $this->uri->segment(4);

        if (!empty($_POST['catatan'])) {

            $updateArr = array(
                'verifikasi' => $verifikasi,
                'catatan'    => $this->input->post('catatan'),
            );

            $this->db->where('id', $guru_mengajar_id);
            $this->db->update('guru_mengajar', $updateArr);
        } else {

            $updateArr = array(
                'verifikasi' => $verifikasi,
            );

            $this->db->where('id', $guru_mengajar_id);
            $this->db->update('guru_mengajar', $updateArr);
        }

        if ($verifikasi === 'terima') {
            echo '<span class="badge bg-success">Diterima</span>';
        } else {

            echo '<span class="badge bg-danger">Ditolak</span>';
        }
    }

    public function verifikasi_dokumen_detail()
    {
        $param = base64url_decode($this->uri->segment(3));

        $exp_param = explode('_', $param);

        $tahun      = $exp_param[0];
        $bulan      = $exp_param[1];
        $pegawai_id = $exp_param[2];
        // $data['jadwal_mengajar'] = get_jadwal_mengajar($this->user_id);

        $this->db->select("
                            a.id,a.jadwal_mengajar_id,
                            a.tgl,b.hari,b.jam_mulai,b.jam_selesai,c.nama_kelas,d.nama as matapelajaran,
                            IFNULL(a.dokumentasi,'belum') AS dokumentasi,
                            a.uraian,
                            IFNULL(a.foto_mulai,'belum') AS foto,
                            IFNULL(a.verifikasi,'belum') AS verifikasi
                        ");
        $this->db->join('jadwal_mengajar b', 'a.jadwal_mengajar_id  = b.id', 'left');
        $this->db->join('kelas c', 'b.kelas_id = c.id', 'left');
        $this->db->join('matapelajaran d', 'b.matapelajaran_id = d.id', 'left');

        $this->db->where('b.pegawai_id', $pegawai_id);
        $this->db->where('a.verifikasi', 'pending');

        // Menambahkan kondisi where untuk filter bulan dan tahun pada a.tgl
        $this->db->where("YEAR(a.tgl)", $tahun);
        $this->db->where("MONTH(a.tgl)", $bulan);

        $this->db->order_by('a.tgl, b.jam_mulai ASC');
        $data['jadwal_mengajar'] = $this->db->get_where('guru_mengajar a');


        $keterangan = 'Data yang ditampilkan adalah data yang belum dilakukan verifikasi';
        $data['keterangan'] = $keterangan;

        $data['nama_pegawai'] = get_column_value('pegawai', $pegawai_id, 'nama_lengkap');

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        // $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Validasi Dokumen Presensi', $this->base_breadcrumbs . '/verifikasi-dokumen');
        $this->breadcrumbs->push('Detail', $this->base_breadcrumbs . '/verifikasi-dokumen-detail/' . $param);

        $data['page_name']  = 'verifikasi_dokumen_detail';
        $data['page_title'] = 'Verifikasi Dokumen';
        $this->_page_output($data);
    }

    public function verifikasi_dokumen()
    {
        if (!empty($_POST)) {

            $bulan = $this->input->post('filter_bulan');
            $tahun = $this->input->post('filter_tahun');

            // $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            // $bobot_normal = $cek_bobot['nilai'];
            $hari_aktif = _get_hari_aktif($tahun, $bulan);
            // $jumlah_minggu = countWeeksInMonth($tahun, $bulan);

            // $bulan = $this->input->post('filter_bulan');
            // $tahun = $this->input->post('filter_tahun');

            // $cek_bobot = $this->db->get_where('bobot', array('status_masuk' => 'NORMAL', 'status_pulang' => 'NORMAL'))->row_array();

            // $bobot_normal = $cek_bobot['nilai'];
            // $hari_aktif   = _get_hari_aktif($tahun, $bulan);

            $keterangan = '<br/>Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;<br/>';
            // $keterangan .= 'Download rekap bulanan <strong><a class="text-danger" href="#" onclick="download_file(\'' . site_url('kepsek/download-rekap-presensi-pegawai/' . $tahun . '/' . $bulan) . '\',\'' . site_url('kepsek/download-rekap-presensi-pegawai/' . $tahun . '/' . $bulan) . '/xls\'); return false" >disini</a></strong><br/>';
            // $keterangan .= 'Download rekap harian &nbsp;' . _get_list_hari_aktif('kepsek/download-rekap-harian-pegawai', $tahun, $bulan);

            $data['keterangan'] = $keterangan;

            // $this->db->select(' --a.id,
            //                     a.nuptk,
            //                     a.nama_lengkap,
            //                     a.jabatan

            //                     ');
            // $this->db->where('a.sekolah_id', $this->sekolah_id);
            // $this->db->where('a.aktif', 'Aktif');
            // $this->db->where("(a.jabatan = 'Guru Mapel' OR a.jabatan = 'Guru TIK')"); // Klausa WHERE baru
            // $this->db->group_by('a.id');
            // $this->db->order_by('a.nama_lengkap ASC');
            // $data['presensi'] = $this->db->get('pegawai a');

            // $this->db->select(' a.id,
            //                     a.nuptk,
            //                     a.nama_lengkap,
            //                     a.jabatan,
            //                     SUM(IF(c.dokumentasi IS NULL, 0, IF(c.foto IS NULL, 0, 1))) AS verifikasi,
            //                     ROUND((SUM(IF(c.dokumentasi IS NULL, 0, IF(c.foto IS NULL, 0, 1))) / ' . $hari_aktif . ') * 100) AS ver_1
            //                 ');

            // $this->db->from('pegawai a');
            // $this->db->join('jadwal_mengajar b', 'a.id = b.pegawai_id', 'left');
            // $this->db->join('guru_mengajar c', 'b.id = c.jadwal_mengajar_id AND YEAR(c.tgl) = ' . $tahun . ' AND MONTH(c.tgl) = ' . $bulan, 'left');
            // $this->db->where('a.sekolah_id', $this->sekolah_id);
            // $this->db->where('a.aktif', 'Aktif');
            // $this->db->where_in('a.jabatan', array('Guru Mapel', 'Guru TIK'));
            // $this->db->group_by('a.id, a.nuptk, a.nama_lengkap, a.jabatan');
            // // $this->db->order_by('a.nama_lengkap', 'ASC');

            // $this->db->select("
            //         a.id,
            //         a.nuptk,
            //         a.nama_lengkap,
            //         a.jabatan,
            //         ROUND((SUM(IF(c.dokumentasi IS NULL, 0, IF(c.foto IS NULL, 0, 1))) / 26) * 100) AS ver_1,
            //         ROUND((SUM(IF(d.dokumentasi IS NULL, 0, IF(d.foto IS NULL, 0, 1))) / 26) * 100) AS ver_2,
            //         ROUND((SUM(IF(e.dokumentasi IS NULL, 0, IF(e.foto IS NULL, 0, 1))) / 26) * 100) AS ver_3,
            //         ROUND((SUM(IF(f.dokumentasi IS NULL, 0, IF(f.foto IS NULL, 0, 1))) / 26) * 100) AS ver_4,
            //         ROUND((SUM(IF(g.dokumentasi IS NULL, 0, IF(g.foto IS NULL, 0, 1))) / 26) * 100) AS ver_5
            //     ");
            // $this->db->from("pegawai a");
            // $this->db->join("jadwal_mengajar b", "a.id = b.pegawai_id", "left");
            // $this->db->join("guru_mengajar c", "b.id = c.jadwal_mengajar_id AND YEAR(c.tgl) = " . $tahun . " AND MONTH(c.tgl) = " . $bulan . " AND (WEEK(c.tgl, 5) - WEEK(DATE_SUB(c.tgl, INTERVAL DAYOFMONTH(c.tgl) - 1 DAY), 5) + 1) = 1", "left");
            // $this->db->join("guru_mengajar d", "b.id = d.jadwal_mengajar_id AND YEAR(d.tgl) = " . $tahun . " AND MONTH(d.tgl) = " . $bulan . " AND (WEEK(d.tgl, 5) - WEEK(DATE_SUB(d.tgl, INTERVAL DAYOFMONTH(d.tgl) - 1 DAY), 5) + 1) = 2", "left");
            // $this->db->join("guru_mengajar e", "b.id = e.jadwal_mengajar_id AND YEAR(e.tgl) = " . $tahun . " AND MONTH(e.tgl) = " . $bulan . " AND (WEEK(e.tgl, 5) - WEEK(DATE_SUB(e.tgl, INTERVAL DAYOFMONTH(e.tgl) - 1 DAY), 5) + 1) = 3", "left");
            // $this->db->join("guru_mengajar f", "b.id = f.jadwal_mengajar_id AND YEAR(f.tgl) = " . $tahun . " AND MONTH(f.tgl) = " . $bulan . " AND (WEEK(f.tgl, 5) - WEEK(DATE_SUB(f.tgl, INTERVAL DAYOFMONTH(f.tgl) - 1 DAY), 5) + 1) = 4", "left");
            // $this->db->join("guru_mengajar g", "b.id = g.jadwal_mengajar_id AND YEAR(g.tgl) = " . $tahun . " AND MONTH(g.tgl) = " . $bulan . " AND (WEEK(g.tgl, 5) - WEEK(DATE_SUB(g.tgl, INTERVAL DAYOFMONTH(g.tgl) - 1 DAY), 5) + 1) = 5", "left");
            // $this->db->where("a.sekolah_id", $this->sekolah_id);
            // $this->db->where("a.aktif", "Aktif");
            // $this->db->where_in("a.jabatan", ['Guru Mapel', 'Guru TIK']);
            // $this->db->group_by("a.id, a.nuptk, a.nama_lengkap, a.jabatan");
            // $this->db->order_by("a.nama_lengkap", "ASC");

            $this->db->select("
                    a.id,
                    a.nuptk,
                    a.nama_lengkap,
                    a.jabatan,
                    ROUND(
                            (
                                SUM(
                                   IF(c.verifikasi = 'tolak',0,(IF(c.dokumentasi IS NULL, IF(c.uraian IS NULL,0,IF(c.foto_mulai IS NULL, 0, 1)), IF(c.foto_mulai IS NULL, 0, 1))))
                                ) / COUNT(c.id)
                            ) * 100
                    ) AS ver
                ");
            $this->db->from("pegawai a");
            $this->db->join("jadwal_mengajar b", "a.id = b.pegawai_id", "left");
            $this->db->join("guru_mengajar c", "b.id = c.jadwal_mengajar_id AND YEAR(c.tgl) = " . $tahun . " AND MONTH(c.tgl) = " . $bulan, "left");
            $this->db->where("a.sekolah_id", $this->sekolah_id);
            $this->db->where("a.aktif", "Aktif");
            $this->db->where_in("a.jabatan", ['Guru Mapel', 'Guru TIK']);
            $this->db->group_by("a.id, a.nuptk, a.nama_lengkap, a.jabatan");
            $this->db->order_by("a.nama_lengkap", "ASC");

            $data['presensi'] = $this->db->get();

            // $data['keterangan'] = 'Hari aktif untuk bulan terpilih adalah :&nbsp;<strong>' . $hari_aktif . '</strong>&nbsp;Hari dengan nilai maksimal presensi :&nbsp<strong>' . ($hari_aktif * $bobot_normal) . '</strong>';

        }

        $this->breadcrumbs->push('Dashboard', $this->base_breadcrumbs);
        // $this->breadcrumbs->push('Kelola Sekolah', $this->base_breadcrumbs . '/kelola-sekolah');
        $this->breadcrumbs->push('Validasi Dokumen Presensi', $this->base_breadcrumbs . '/verifikasi-dokumen');

        $data['page_name']  = 'verifikasi_dokumen';
        $data['page_title'] = 'Verifikasi Dokumen Presensi';
        $this->_page_output($data);
    }
}
