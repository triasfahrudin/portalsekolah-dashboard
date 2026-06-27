<?php
namespace App\Controllers;

use App\Models\PegawaiModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    private function requireNumericId($id): string
    {
        $id = trim((string) $id);

        if ($id === '' || !ctype_digit($id)) {
            throw new PageNotFoundException('Data tidak ditemukan');
        }

        return $id;
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $queryPegawai = $db->query('SELECT COUNT(id) AS total FROM pegawai');
        $resultPegawai = $queryPegawai->getRow();

        $querySekolah = $db->query('SELECT COUNT(id) AS total FROM sekolah');
        $resultSekolah = $querySekolah->getRow();

        $querySiswa = $db->query('SELECT COUNT(id) AS total FROM siswa');
        $resultSiswa = $querySiswa->getRow();

        $querySiswaAkhir = $db->query("SELECT 
            COUNT(DISTINCT a.id) AS total
            FROM siswa_akhir a
            JOIN sekolah_siswa_akhir b ON a.sekolah_id = b.id
            WHERE b.provinsi = '100000';
        ");
        $resultSiswaAkhir = $querySiswaAkhir->getRow();

        $querySiswaTidakSekolah = $db->query("SELECT COUNT(id) AS total FROM ats WHERE provinsi = '100000'");
        $resultSiswaTidakSekolah = $querySiswaTidakSekolah->getRow();

        //======================================== KODE GRAFIK PEGAWAI ========================================//
        $queryGrafikPegawai = $db->query('
                SELECT 
                    a.jk,
                    a.status_pegawai,
                    c.id AS provinsi_id,
                    c.nama AS provinsi,
                    COUNT(*) AS jumlah
                FROM pegawai AS a
                JOIN sekolah AS b ON a.sekolah_id = b.id
                JOIN wilayah_provinsi AS c ON b.provinsi = c.id
                GROUP BY c.id, c.nama, a.jk, a.status_pegawai
                ORDER BY c.id
            ');

        $resultGrafikPegawai = $queryGrafikPegawai->getResultArray();

        $data_pegawai = [];
        $status_list_pegawai = [];

        foreach ($resultGrafikPegawai as $row) {
            $provinsi_id_pegawai = $row['provinsi_id'];
            $provinsi_pegawai = $row['provinsi'];
            $status_pegawai = $row['status_pegawai'];
            $jk_pegawai = strtoupper(trim($row['jk'] ?? ''));
            $jumlah_pegawai = $row['jumlah'];

            if (!in_array($status_pegawai, $status_list_pegawai)) {
                $status_list_pegawai[] = $status_pegawai;
            }

            if (!isset($data_pegawai[$provinsi_id_pegawai])) {
                $data_pegawai[$provinsi_id_pegawai] = [
                    'provinsi_nama' => $provinsi_pegawai,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_pegawai[$provinsi_id_pegawai][$status_pegawai])) {
                $data_pegawai[$provinsi_id_pegawai][$status_pegawai] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            // validasi hanya L / P
            if (!in_array($jk_pegawai, ['L', 'P'])) {
                $jk_pegawai = 'Jml'; // atau skip
            }

            $data_pegawai[$provinsi_id_pegawai][$status_pegawai]['Jml'] += $jumlah_pegawai;
            if (isset($data_pegawai[$provinsi_id_pegawai][$status_pegawai][$jk_pegawai])) {
                $data_pegawai[$provinsi_id_pegawai][$status_pegawai][$jk_pegawai] += $jumlah_pegawai;
            }

            $data_pegawai[$provinsi_id_pegawai]['total']['Jml'] += $jumlah_pegawai;
            $data_pegawai[$provinsi_id_pegawai]['total'][$jk_pegawai] += $jumlah_pegawai;
        }
        //======================================== END KODE GRAFIK PEGAWAI ========================================//

        //======================================== KODE GRAFIK SEKOLAH ========================================//
        $querySekolah = $db->query("
                SELECT 
                    c.id AS kabupaten_id, 
                    c.nama AS kabupaten, 
                    a.level, 
                    a.status, 
                    COUNT(*) as total 
                FROM sekolah as a
                JOIN wilayah_provinsi as b ON a.provinsi = b.id
                JOIN wilayah_kabupaten as c ON a.kabupaten = c.id
                WHERE b.id = '100000'
                GROUP BY c.id, c.nama, a.level, a.status
                ORDER BY c.nama DESC
            ");

        $sekolahResult = $querySekolah->getResultArray();

        $data_sekolah = [];
        $level_list = [];

        foreach ($sekolahResult as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten_nama = $row['kabupaten'];
            $level = $row['level'];
            $status = $row['status'];
            $total = $row['total'];

            if (!in_array($level, $level_list)) {
                $level_list[] = $level;
            }

            $urutan_jenjang = ['SLB', 'SMA', 'SMK'];

            usort($level_list, function ($a, $b) use ($urutan_jenjang) {
                $indexA = array_search($a, $urutan_jenjang);
                $indexB = array_search($b, $urutan_jenjang);
                return $indexA - $indexB;
            });

            if (!isset($data_sekolah[$kabupaten_id])) {
                $data_sekolah[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten_nama,
                    'total' => [],
                ];
            }

            if (!isset($data_sekolah[$kabupaten_id][$level])) {
                $data_sekolah[$kabupaten_id][$level] = ['Jml' => 0, 'NEGERI' => 0, 'SWASTA' => 0];
            }

            $data_sekolah[$kabupaten_id][$level]['Jml'] += $total;
            $data_sekolah[$kabupaten_id][$level][$status] += $total;
        }
        //======================================== END KODE GRAFIK SEKOLAH ========================================//

        //======================================== KODE GRAFIK SISWA ========================================//
        $queryGrafikSiswa = $db->query("
                SELECT 
                    a.jk,
                    b.level as level_siswa,
                    d.id AS kabupaten_id,
                    d.nama as kabupaten,
                    COUNT(*) AS jumlah
                FROM siswa AS a
                JOIN sekolah AS b ON a.sekolah_id = b.id
                JOIN wilayah_provinsi AS c ON b.provinsi = c.id
                JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
                WHERE c.id = '100000'
                GROUP BY d.id, d.nama, a.jk, b.level
                ORDER BY d.id
            ");

        $resultGrafikSiswa = $queryGrafikSiswa->getResultArray();

        $data_siswa = [];
        $status_list_siswa = [];

        foreach ($resultGrafikSiswa as $row) {
            $kabupaten_id_siswa = $row['kabupaten_id'];
            $kabupaten_siswa = $row['kabupaten'];
            $level_siswa = $row['level_siswa'];
            $jk_siswa = $row['jk'];
            $jumlah_siswa = $row['jumlah'];

            // Validasi jenis kelamin (jk_siswa) agar hanya "L" atau "P"
            if (!in_array($jk_siswa, ['L', 'P'])) {
                // Jika jk_siswa tidak valid, set default (misalnya 'L' atau 'P')
                $jk_siswa = 'L';  // atau kamu bisa memilih nilai default lain sesuai kebutuhan
            }

            if (!in_array($level_siswa, $status_list_siswa)) {
                $status_list_siswa[] = $level_siswa;
            }

            if (!isset($data_siswa[$kabupaten_id_siswa])) {
                $data_siswa[$kabupaten_id_siswa] = [
                    'kabupaten_nama' => $kabupaten_siswa,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kabupaten_id_siswa][$level_siswa])) {
                $data_siswa[$kabupaten_id_siswa][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            // Pastikan key untuk jk_siswa valid sebelum mengakses array
            if (!isset($data_siswa[$kabupaten_id_siswa][$level_siswa][$jk_siswa])) {
                $data_siswa[$kabupaten_id_siswa][$level_siswa][$jk_siswa] = 0;  // Initialize jika belum ada
            }

            $data_siswa[$kabupaten_id_siswa][$level_siswa]['Jml'] += $jumlah_siswa;
            $data_siswa[$kabupaten_id_siswa][$level_siswa][$jk_siswa] += $jumlah_siswa;

            $data_siswa[$kabupaten_id_siswa]['total']['Jml'] += $jumlah_siswa;
            $data_siswa[$kabupaten_id_siswa]['total'][$jk_siswa] += $jumlah_siswa;
        }
        //======================================== END KODE GRAFIK SISWA ========================================//

        //======================================== KODE GRAFIK SISWA AKHIR ========================================//
        $queryGrafikSiswaAkhir = $db->query("
                SELECT 
                    a.jk,
                    b.level as level_siswa,
                    d.id AS kabupaten_id,
                    d.nama as kabupaten,
                    COUNT(*) AS jumlah
                FROM siswa_akhir AS a
                JOIN sekolah_siswa_akhir AS b ON a.sekolah_id = b.id
                JOIN wilayah_provinsi_siswa_akhir AS c ON b.provinsi = c.id
                JOIN wilayah_kabupaten_siswa_akhir as d ON b.kabupaten = d.id
                WHERE c.id = '100000'
                GROUP BY d.id, d.nama, a.jk, b.level
                ORDER BY d.id
            ");

        $resultGrafikSiswaAkhir = $queryGrafikSiswaAkhir->getResultArray();

        $data_siswa_akhir = [];
        $status_list_siswa_akhir = [];

        foreach ($resultGrafikSiswaAkhir as $row) {
            $kabupaten_id_siswa = $row['kabupaten_id'];
            $kabupaten_siswa = $row['kabupaten'];
            $level_siswa = $row['level_siswa'];
            $jk_siswa = $row['jk'];
            $jumlah_siswa = $row['jumlah'];

            if (!in_array($level_siswa, $status_list_siswa_akhir)) {
                $status_list_siswa_akhir[] = $level_siswa;
            }

            if (!isset($data_siswa_akhir[$kabupaten_id_siswa])) {
                $data_siswa_akhir[$kabupaten_id_siswa] = [
                    'kabupaten_nama' => $kabupaten_siswa,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa_akhir[$kabupaten_id_siswa][$level_siswa])) {
                $data_siswa_akhir[$kabupaten_id_siswa][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa_akhir[$kabupaten_id_siswa][$level_siswa]['Jml'] += $jumlah_siswa;
            $data_siswa_akhir[$kabupaten_id_siswa][$level_siswa][$jk_siswa] += $jumlah_siswa;

            $data_siswa_akhir[$kabupaten_id_siswa]['total']['Jml'] += $jumlah_siswa;
            $data_siswa_akhir[$kabupaten_id_siswa]['total'][$jk_siswa] += $jumlah_siswa;
        }
        //======================================== END KODE GRAFIK SISWA AKHIR ========================================//

        //======================================== KODE GRAFIK SISWA TIDAK SEKOLAH ========================================//
        $queryGrafikSiswaTidakSekolah = $db->query("
                SELECT 
                    a.jk,
                    b.id AS kabupaten_id,
                    b.nama AS kabupaten,
                    COUNT(*) AS jumlah
                FROM ats AS a
                JOIN wilayah_kabupaten AS b ON a.kabupaten = b.id
                WHERE b.provinsi_id = '100000'
                GROUP BY b.id, b.nama, a.jk
                ORDER BY b.id
            ");

        $resultGrafikSiswaTidakSekolah = $queryGrafikSiswaTidakSekolah->getResultArray();

        $data_siswa_tidak_sekolah = [];
        $jk_list_ats = ['L', 'P'];

        foreach ($resultGrafikSiswaTidakSekolah as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten_nama = $row['kabupaten'];
            $jk = strtoupper(trim($row['jk'] ?? ''));
            $jumlah = $row['jumlah'];

            if (!in_array($jk, ['L', 'P'])) {
                $jk = 'L';
            }

            if (!isset($data_siswa_tidak_sekolah[$kabupaten_id])) {
                $data_siswa_tidak_sekolah[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten_nama,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            $data_siswa_tidak_sekolah[$kabupaten_id]['total']['Jml'] += $jumlah;
            $data_siswa_tidak_sekolah[$kabupaten_id]['total'][$jk] += $jumlah;
        }
        //======================================== END KODE GRAFIK SISWA TIDAK SEKOLAH ========================================//

        $data = [
            'title' => 'Dashboard Sekolah',
            'totalPegawai' => $resultPegawai ? $resultPegawai->total : 0, // memastikan data tidak null
            'totalSiswa' => $resultSiswa ? $resultSiswa->total : 0,
            'totalSiswaAkhir' => $resultSiswaAkhir ? $resultSiswaAkhir->total : 0,
            'totalSiswaTidakSekolah' => $resultSiswaTidakSekolah ? $resultSiswaTidakSekolah->total : 0,
            'totalSekolah' => $resultSekolah ? $resultSekolah->total : 0,
            'data_pegawai' => $data_pegawai,
            'status_list_pegawai' => $status_list_pegawai,
            'data_siswa' => $data_siswa,
            'data_siswa_akhir' => $data_siswa_akhir,
            'data_siswa_tidak_sekolah' => $data_siswa_tidak_sekolah,
            'status_list_siswa' => $status_list_siswa,
            'status_list_siswa_akhir' => $status_list_siswa_akhir,
            'data_sekolah' => $data_sekolah,
            'level_list' => $level_list
        ];

        return view('dashboard_sekolah', $data);
    }

    //======================================== PEGAWAI ========================================//
    public function pegawai()
    {
        $db = \Config\Database::connect();

        $query = $db->query('
            SELECT 
                a.jk,
                a.status_pegawai,
                c.id AS provinsi_id,
                c.nama AS provinsi,
                COUNT(*) AS jumlah
            FROM pegawai AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            GROUP BY c.id, c.nama, a.jk, a.status_pegawai
            ORDER BY c.id
        ');

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $provinsi = $row['provinsi'];
            $status_pegawai = $row['status_pegawai'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($status_pegawai, $status_list)) {
                $status_list[] = $status_pegawai;
            }

            if (!isset($data_pegawai[$provinsi_id])) {
                $data_pegawai[$provinsi_id] = [
                    'provinsi_nama' => $provinsi,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_pegawai[$provinsi_id][$status_pegawai])) {
                $data_pegawai[$provinsi_id][$status_pegawai] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_pegawai[$provinsi_id][$status_pegawai]['Jml'] += $jumlah;

            $jk = strtoupper(trim($row['jk']));

            if ($jk !== 'L' && $jk !== 'P') {
                $jk = 'Jml'; // fallback aman
            }

            if (!isset($data_pegawai[$provinsi_id][$status_pegawai][$jk])) {
                $data_pegawai[$provinsi_id][$status_pegawai][$jk] = 0;
            }

            $data_pegawai[$provinsi_id][$status_pegawai][$jk] += $jumlah;

            $data_pegawai[$provinsi_id]['total']['Jml'] += $jumlah;
            $data_pegawai[$provinsi_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_pegawai' => $data_pegawai,
            'status_list' => $status_list
        ];

        return view('v_pegawai', $data);
    }

    public function kabupaten_pegawai($provinsi_id)
    {
        $provinsi_id = $this->requireNumericId($provinsi_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status_pegawai,
                d.id AS kabupaten_id,
                d.nama as kabupaten,
                COUNT(*) AS jumlah
            FROM pegawai AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
            WHERE c.id = ?
            GROUP BY d.id, d.nama, a.jk, a.status_pegawai
            ORDER BY d.id
        ", [$provinsi_id]);

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten = $row['kabupaten'];
            $status_pegawai = $row['status_pegawai'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($status_pegawai, $status_list)) {
                $status_list[] = $status_pegawai;
            }

            if (!isset($data_pegawai[$kabupaten_id])) {
                $data_pegawai[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_pegawai[$kabupaten_id][$status_pegawai])) {
                $data_pegawai[$kabupaten_id][$status_pegawai] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $jk = strtoupper(trim($row['jk']));

            if ($jk !== 'L' && $jk !== 'P') {
                $jk = 'Jml';
            }

            if (!isset($data_pegawai[$kabupaten_id][$status_pegawai][$jk])) {
                $data_pegawai[$kabupaten_id][$status_pegawai][$jk] = 0;
            }

            $data_pegawai[$kabupaten_id][$status_pegawai]['Jml'] += $jumlah;
            $data_pegawai[$kabupaten_id][$status_pegawai][$jk] += $jumlah;

            $data_pegawai[$kabupaten_id]['total']['Jml'] += $jumlah;
            $data_pegawai[$kabupaten_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_pegawai' => $data_pegawai,
            'status_list' => $status_list
        ];

        return view('v_kabupaten_pegawai', $data);
    }

    public function kecamatan_pegawai($kabupaten_id)
    {
        $kabupaten_id = $this->requireNumericId($kabupaten_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status_pegawai,
                e.id AS kecamatan_id,
                e.nama as kecamatan,
                c.id as provinsi_id,
                COUNT(*) AS jumlah
            FROM pegawai AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan as e ON b.kecamatan = e.id
            WHERE d.id = ?
            GROUP BY e.id, e.nama, a.jk, a.status_pegawai
            ORDER BY e.id
        ", [$kabupaten_id]);

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $kecamatan_id = $row['kecamatan_id'];
            $kecamatan = $row['kecamatan'];
            $status_pegawai = $row['status_pegawai'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($status_pegawai, $status_list)) {
                $status_list[] = $status_pegawai;
            }

            if (!isset($data_pegawai[$kecamatan_id])) {
                $data_pegawai[$kecamatan_id] = [
                    'kecamatan_nama' => $kecamatan,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_pegawai[$kecamatan_id][$status_pegawai])) {
                $data_pegawai[$kecamatan_id][$status_pegawai] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_pegawai[$kecamatan_id][$status_pegawai]['Jml'] += $jumlah;
            $data_pegawai[$kecamatan_id][$status_pegawai][$jk] += $jumlah;

            $data_pegawai[$kecamatan_id]['total']['Jml'] += $jumlah;
            $data_pegawai[$kecamatan_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'provinsi_id' => $provinsi_id,
            'data_pegawai' => $data_pegawai,
            'status_list' => $status_list
        ];

        return view('v_kecamatan_pegawai', $data);
    }

    public function sekolah_pegawai($kecamatan_id)
    {
        $kecamatan_id = $this->requireNumericId($kecamatan_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status_pegawai,
                b.id AS sekolah_id,
                b.nama as sekolah,
                d.id as kabupaten_id,
                COUNT(*) AS jumlah
            FROM pegawai AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan as e ON b.kecamatan = e.id
            WHERE e.id = ?
            GROUP BY b.id, b.nama, a.jk, a.status_pegawai
            ORDER BY e.id
        ", [$kecamatan_id]);

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $sekolah_id = $row['sekolah_id'];
            $sekolah = $row['sekolah'];
            $status_pegawai = $row['status_pegawai'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($status_pegawai, $status_list)) {
                $status_list[] = $status_pegawai;
            }

            if (!isset($data_pegawai[$sekolah_id])) {
                $data_pegawai[$sekolah_id] = [
                    'sekolah_nama' => $sekolah,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_pegawai[$sekolah_id][$status_pegawai])) {
                $data_pegawai[$sekolah_id][$status_pegawai] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_pegawai[$sekolah_id][$status_pegawai]['Jml'] += $jumlah;
            $data_pegawai[$sekolah_id][$status_pegawai][$jk] += $jumlah;

            $data_pegawai[$sekolah_id]['total']['Jml'] += $jumlah;
            $data_pegawai[$sekolah_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'kabupaten_id' => $kabupaten_id,
            'data_pegawai' => $data_pegawai,
            'status_list' => $status_list
        ];

        return view('v_sekolah_pegawai', $data);
    }

    public function data_pegawai($sekolah_id)
    {
        $this->requireNumericId($sekolah_id);

        throw new PageNotFoundException('Akses data pegawai tidak tersedia untuk publik');
    }
    //======================================== END PEGAWAI ========================================//

    //======================================== SEKOLAH ========================================//
    public function sekolah()
    {
        $db = \Config\Database::connect();

        $query = $db->query('
            SELECT 
                b.id AS provinsi_id, 
                b.nama AS provinsi, 
                a.level, a.status, 
                COUNT(*) as total 
            FROM sekolah as a
            JOIN wilayah_provinsi b ON a.provinsi = b.id
            GROUP BY b.id, a.level, a.status
        ');

        $sekolah = $query->getResultArray();

        return view('v_sekolah', ['sekolah' => $sekolah]);
    }

    public function kabupaten_sekolah($provinsi_id)
    {
        $provinsi_id = $this->requireNumericId($provinsi_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                c.id AS kabupaten_id, 
                c.nama AS kabupaten, 
                a.level, 
                a.status, 
                COUNT(*) as total 
            FROM sekolah as a
            JOIN wilayah_provinsi as b ON a.provinsi = b.id
            JOIN wilayah_kabupaten as c ON a.kabupaten = c.id
            WHERE c.provinsi_id = ?
            GROUP BY c.id, a.level, a.status
        ", [$provinsi_id]);

        $sekolah = $query->getResultArray();

        return view('v_kabupaten_sekolah', ['sekolah' => $sekolah]);
    }

    public function kecamatan_sekolah($kabupaten_id)
    {
        $kabupaten_id = $this->requireNumericId($kabupaten_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                b.id AS provinsi_id, 
                d.id AS kecamatan_id, 
                d.nama AS kecamatan, 
                a.level, 
                a.status, 
                COUNT(*) as total 
            FROM sekolah as a
            JOIN wilayah_provinsi as b ON a.provinsi = b.id
            JOIN wilayah_kabupaten as c ON a.kabupaten = c.id
            JOIN wilayah_kecamatan as d ON a.kecamatan = d.id
            WHERE d.kabupaten_id = ?
            GROUP BY d.id, a.level, a.status
        ", [$kabupaten_id]);

        $kecamatan = $query->getResultArray();

        return view('v_kecamatan_sekolah', ['kecamatan' => $kecamatan]);
    }

    public function data_sekolah($kecamatan_id)
    {
        $kecamatan_id = $this->requireNumericId($kecamatan_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                c.id AS kabupten_id, 
                a.id AS sekolah_id, 
                a.nama AS sekolah, 
                a.level, 
                a.status, 
                COUNT(*) as total 
            FROM sekolah as a
            JOIN wilayah_provinsi as b ON a.provinsi = b.id
            JOIN wilayah_kabupaten as c ON a.kabupaten = c.id
            JOIN wilayah_kecamatan as d ON a.kecamatan = d.id
            WHERE d.id = ?
            GROUP BY a.id, a.level, a.status
        ", [$kecamatan_id]);

        $sekolah = $query->getResultArray();

        return view('v_data_sekolah', ['sekolah' => $sekolah]);
    }
    //======================================== END SEKOLAH ========================================//

    //======================================== SISWA ========================================//
    public function siswa()
    {
        $db = \Config\Database::connect();

        $query = $db->query('
        SELECT 
            a.jk,
            b.level as level_siswa,
            c.id AS provinsi_id,
            c.nama AS provinsi,
            COUNT(*) AS jumlah
        FROM siswa AS a
        JOIN sekolah AS b ON a.sekolah_id = b.id
        JOIN wilayah_provinsi AS c ON b.provinsi = c.id
        GROUP BY c.id, c.nama, a.jk, b.level
        ORDER BY c.id
    ');

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $provinsi = $row['provinsi'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            // Validasi jenis kelamin (jk) agar hanya "L" atau "P"
            if (!in_array($jk, ['L', 'P'])) {
                // Jika jk tidak valid, set ke nilai default "L" atau "P"
                $jk = 'L';  // Atau bisa set ke 'P' jika sesuai kebutuhan
            }

            // Pastikan level_siswa ada di dalam status_list
            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            // Inisialisasi array untuk provinsi jika belum ada
            if (!isset($data_siswa[$provinsi_id])) {
                $data_siswa[$provinsi_id] = [
                    'provinsi_nama' => $provinsi,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            // Inisialisasi array untuk level_siswa jika belum ada
            if (!isset($data_siswa[$provinsi_id][$level_siswa])) {
                $data_siswa[$provinsi_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            // Inisialisasi jika key untuk jenis kelamin belum ada
            if (!isset($data_siswa[$provinsi_id][$level_siswa][$jk])) {
                $data_siswa[$provinsi_id][$level_siswa][$jk] = 0;  // Initialize jika belum ada
            }

            // Update jumlah siswa berdasarkan level dan jenis kelamin
            $data_siswa[$provinsi_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$provinsi_id][$level_siswa][$jk] += $jumlah;

            // Update total jumlah siswa untuk provinsi
            $data_siswa[$provinsi_id]['total']['Jml'] += $jumlah;
            $data_siswa[$provinsi_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_siswa', $data);
    }

    public function kabupaten_siswa($provinsi_id)
    {
        $provinsi_id = $this->requireNumericId($provinsi_id);
        $db = \Config\Database::connect();

        $query = $db->query("
                                SELECT 
                                    COALESCE(a.jk, 'L') AS jk,
                                    b.level AS level_siswa,
                                    d.id AS kabupaten_id,
                                    d.nama AS kabupaten,
                                    COUNT(*) AS jumlah
                                FROM siswa AS a
                                JOIN sekolah AS b ON a.sekolah_id = b.id
                                JOIN wilayah_provinsi AS c ON b.provinsi = c.id
                                JOIN wilayah_kabupaten AS d ON b.kabupaten = d.id
                                WHERE c.id = ?
                                GROUP BY d.id, d.nama, COALESCE(a.jk, 'L'), b.level
                                ORDER BY d.id
                            ", [$provinsi_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten = $row['kabupaten'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk']; // sudah pasti L/P
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$kabupaten_id])) {
                $data_siswa[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kabupaten_id][$level_siswa])) {
                $data_siswa[$kabupaten_id][$level_siswa] = [
                    'Jml' => 0,
                    'L' => 0,
                    'P' => 0
                ];
            }

            $data_siswa[$kabupaten_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$kabupaten_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kabupaten_siswa', $data);
    }

    public function kecamatan_siswa($kabupaten_id)
    {
        $kabupaten_id = $this->requireNumericId($kabupaten_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                COALESCE(a.jk, 'L') AS jk,
                b.level as level_siswa,
                e.id AS kecamatan_id,
                e.nama as kecamatan,
                c.id as provinsi_id,
                COUNT(*) AS jumlah
            FROM siswa AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan as e ON b.kecamatan = e.id
            WHERE d.id = ?
            GROUP BY e.id, e.nama, COALESCE(a.jk, 'L'), b.level
            ORDER BY e.id
        ", [$kabupaten_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $kecamatan_id = $row['kecamatan_id'];
            $kecamatan = $row['kecamatan'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$kecamatan_id])) {
                $data_siswa[$kecamatan_id] = [
                    'kecamatan_nama' => $kecamatan,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kecamatan_id][$level_siswa])) {
                $data_siswa[$kecamatan_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$kecamatan_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$kecamatan_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'provinsi_id' => $provinsi_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kecamatan_siswa', $data);
    }

    public function sekolah_siswa($kecamatan_id)
    {
        $kecamatan_id = $this->requireNumericId($kecamatan_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                b.level as level_siswa,
                b.id AS sekolah_id,
                b.nama as sekolah,
                d.id as kabupaten_id,
                COUNT(*) AS jumlah
            FROM siswa AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan as e ON b.kecamatan = e.id
            WHERE e.id = ?
            GROUP BY b.id, b.nama, a.jk, b.level
            ORDER BY e.id
        ", [$kecamatan_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $sekolah_id = $row['sekolah_id'];
            $sekolah = $row['sekolah'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$sekolah_id])) {
                $data_siswa[$sekolah_id] = [
                    'sekolah_nama' => $sekolah,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$sekolah_id][$level_siswa])) {
                $data_siswa[$sekolah_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$sekolah_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$sekolah_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$sekolah_id]['total']['Jml'] += $jumlah;
            $data_siswa[$sekolah_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'kabupaten_id' => $kabupaten_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_sekolah_siswa', $data);
    }
    //======================================== END SISWA ========================================//

    //======================================== SISWA AKHIR ========================================//
    public function siswa_akhir()
    {
        $db = \Config\Database::connect();

        $query = $db->query('
            SELECT 
                a.jk,
                b.level as level_siswa,
                c.id AS provinsi_id,
                c.nama AS provinsi,
                COUNT(*) AS jumlah
            FROM siswa_akhir AS a
            JOIN sekolah_siswa_akhir AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi_siswa_akhir AS c ON b.provinsi = c.id
            GROUP BY c.id, c.nama, a.jk, b.level
            ORDER BY c.id
        ');

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $provinsi = $row['provinsi'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$provinsi_id])) {
                $data_siswa[$provinsi_id] = [
                    'provinsi_nama' => $provinsi,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$provinsi_id][$level_siswa])) {
                $data_siswa[$provinsi_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$provinsi_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$provinsi_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$provinsi_id]['total']['Jml'] += $jumlah;
            $data_siswa[$provinsi_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_siswa_akhir', $data);
    }

    public function kabupaten_siswa_akhir($provinsi_id)
    {
        $provinsi_id = $this->requireNumericId($provinsi_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                b.level as level_siswa,
                d.id AS kabupaten_id,
                d.nama as kabupaten,
                COUNT(*) AS jumlah
            FROM siswa_akhir AS a
            JOIN sekolah_siswa_akhir AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi_siswa_akhir AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten_siswa_akhir as d ON b.kabupaten = d.id
            WHERE c.id = ?
            GROUP BY d.id, d.nama, a.jk, b.level
            ORDER BY d.id
        ", [$provinsi_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten = $row['kabupaten'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$kabupaten_id])) {
                $data_siswa[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kabupaten_id][$level_siswa])) {
                $data_siswa[$kabupaten_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$kabupaten_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$kabupaten_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kabupaten_siswa_akhir', $data);
    }

    public function kecamatan_siswa_akhir($kabupaten_id)
    {
        $kabupaten_id = $this->requireNumericId($kabupaten_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                b.level as level_siswa,
                e.id AS kecamatan_id,
                e.nama as kecamatan,
                c.id as provinsi_id,
                COUNT(*) AS jumlah
            FROM siswa_akhir AS a
            JOIN sekolah_siswa_akhir AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi_siswa_akhir AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten_siswa_akhir as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan_siswa_akhir as e ON b.kecamatan = e.id
            WHERE d.id = ?
            GROUP BY e.id, e.nama, a.jk, b.level
            ORDER BY e.id
        ", [$kabupaten_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $kecamatan_id = $row['kecamatan_id'];
            $kecamatan = $row['kecamatan'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$kecamatan_id])) {
                $data_siswa[$kecamatan_id] = [
                    'kecamatan_nama' => $kecamatan,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kecamatan_id][$level_siswa])) {
                $data_siswa[$kecamatan_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$kecamatan_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$kecamatan_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'provinsi_id' => $provinsi_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kecamatan_siswa_akhir', $data);
    }

    public function sekolah_siswa_akhir($kecamatan_id)
    {
        $kecamatan_id = $this->requireNumericId($kecamatan_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                b.level as level_siswa,
                b.id AS sekolah_id,
                b.nama as sekolah,
                d.id as kabupaten_id,
                COUNT(*) AS jumlah
            FROM siswa_akhir AS a
            JOIN sekolah_siswa_akhir AS b ON a.sekolah_id = b.id
            JOIN wilayah_provinsi_siswa_akhir AS c ON b.provinsi = c.id
            JOIN wilayah_kabupaten_siswa_akhir as d ON b.kabupaten = d.id
            JOIN wilayah_kecamatan_siswa_akhir as e ON b.kecamatan = e.id
            WHERE e.id = ?
            GROUP BY b.id, b.nama, a.jk, b.level
            ORDER BY e.id
        ", [$kecamatan_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $sekolah_id = $row['sekolah_id'];
            $sekolah = $row['sekolah'];
            $level_siswa = $row['level_siswa'];
            $jk = $row['jk'];
            $jumlah = $row['jumlah'];

            if (!in_array($level_siswa, $status_list)) {
                $status_list[] = $level_siswa;
            }

            if (!isset($data_siswa[$sekolah_id])) {
                $data_siswa[$sekolah_id] = [
                    'sekolah_nama' => $sekolah,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$sekolah_id][$level_siswa])) {
                $data_siswa[$sekolah_id][$level_siswa] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            $data_siswa[$sekolah_id][$level_siswa]['Jml'] += $jumlah;
            $data_siswa[$sekolah_id][$level_siswa][$jk] += $jumlah;

            $data_siswa[$sekolah_id]['total']['Jml'] += $jumlah;
            $data_siswa[$sekolah_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'kabupaten_id' => $kabupaten_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_sekolah_siswa_akhir', $data);
    }
    //======================================== END SISWA AKHIR ========================================//

    //======================================== DATA SISWA AKHIR ========================================//
    public function data_siswa_akhir($sekolah_id)
    {
        $this->requireNumericId($sekolah_id);

        throw new PageNotFoundException('Akses data siswa tidak tersedia untuk publik');
    }
    //======================================== END DATA SISWA AKHIR ========================================//

    //======================================== SISWA TIDAK SEKOLAH ========================================//
    public function siswa_tidak_sekolah()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status,
                b.id AS kabupaten_id,
                b.nama AS kabupaten,
                COUNT(*) AS jumlah
            FROM ats AS a
            JOIN wilayah_kabupaten AS b ON a.kabupaten = b.id
            JOIN wilayah_provinsi AS c ON b.provinsi_id = c.id
            WHERE c.id = '100000'
            GROUP BY b.id, b.nama, a.jk, a.status
            ORDER BY b.id
        ");

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $kabupaten = $row['kabupaten'];
            $status = $row['status'];
            $jk = strtoupper(trim($row['jk'] ?? ''));
            $jumlah = $row['jumlah'];

            if (!in_array($status, $status_list)) {
                $status_list[] = $status;
            }

            if (!in_array($jk, ['L', 'P'])) {
                $jk = 'L';
            }

            if (!isset($data_siswa[$kabupaten_id])) {
                $data_siswa[$kabupaten_id] = [
                    'kabupaten_nama' => $kabupaten,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kabupaten_id][$status])) {
                $data_siswa[$kabupaten_id][$status] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            if (!isset($data_siswa[$kabupaten_id][$status][$jk])) {
                $data_siswa[$kabupaten_id][$status][$jk] = 0;
            }

            $data_siswa[$kabupaten_id][$status]['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id][$status][$jk] += $jumlah;

            $data_siswa[$kabupaten_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kabupaten_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_siswa_tidak_sekolah', $data);
    }

    public function kabupaten_siswa_tidak_sekolah($kabupaten_id)
    {
        $kabupaten_id = $this->requireNumericId($kabupaten_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status,
                b.id AS kecamatan_id,
                b.nama AS kecamatan,
                c.id AS provinsi_id,
                COUNT(*) AS jumlah
            FROM ats AS a
            JOIN wilayah_kecamatan AS b ON a.kecamatan = b.id
            JOIN wilayah_kabupaten AS c ON b.kabupaten_id = c.id
            WHERE c.id = ?
            GROUP BY b.id, b.nama, a.jk, a.status
            ORDER BY b.id
        ", [$kabupaten_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id = $row['provinsi_id'];
            $kecamatan_id = $row['kecamatan_id'];
            $kecamatan = $row['kecamatan'];
            $status = $row['status'];
            $jk = strtoupper(trim($row['jk'] ?? ''));
            $jumlah = $row['jumlah'];

            if (!in_array($status, $status_list)) {
                $status_list[] = $status;
            }

            if (!in_array($jk, ['L', 'P'])) {
                $jk = 'L';
            }

            if (!isset($data_siswa[$kecamatan_id])) {
                $data_siswa[$kecamatan_id] = [
                    'kecamatan_nama' => $kecamatan,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$kecamatan_id][$status])) {
                $data_siswa[$kecamatan_id][$status] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            if (!isset($data_siswa[$kecamatan_id][$status][$jk])) {
                $data_siswa[$kecamatan_id][$status][$jk] = 0;
            }

            $data_siswa[$kecamatan_id][$status]['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id][$status][$jk] += $jumlah;

            $data_siswa[$kecamatan_id]['total']['Jml'] += $jumlah;
            $data_siswa[$kecamatan_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'provinsi_id' => $provinsi_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kabupaten_siswa_tidak_sekolah', $data);
    }

    public function kecamatan_siswa_tidak_sekolah($kecamatan_id)
    {
        $kecamatan_id = $this->requireNumericId($kecamatan_id);
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
                a.status,
                b.id AS sekolah_id,
                b.nama AS sekolah,
                c.id AS kabupaten_id,
                COUNT(*) AS jumlah
            FROM ats AS a
            JOIN sekolah AS b ON a.sekolah_id = b.id
            JOIN wilayah_kabupaten AS c ON b.kabupaten = c.id
            JOIN wilayah_kecamatan AS d ON b.kecamatan = d.id
            WHERE d.id = ?
            GROUP BY b.id, b.nama, a.jk, a.status
            ORDER BY b.id
        ", [$kecamatan_id]);

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id = $row['kabupaten_id'];
            $sekolah_id = $row['sekolah_id'];
            $sekolah = $row['sekolah'];
            $status = $row['status'];
            $jk = strtoupper(trim($row['jk'] ?? ''));
            $jumlah = $row['jumlah'];

            if (!in_array($status, $status_list)) {
                $status_list[] = $status;
            }

            if (!in_array($jk, ['L', 'P'])) {
                $jk = 'L';
            }

            if (!isset($data_siswa[$sekolah_id])) {
                $data_siswa[$sekolah_id] = [
                    'sekolah_nama' => $sekolah,
                    'total' => ['Jml' => 0, 'L' => 0, 'P' => 0]
                ];
            }

            if (!isset($data_siswa[$sekolah_id][$status])) {
                $data_siswa[$sekolah_id][$status] = ['Jml' => 0, 'L' => 0, 'P' => 0];
            }

            if (!isset($data_siswa[$sekolah_id][$status][$jk])) {
                $data_siswa[$sekolah_id][$status][$jk] = 0;
            }

            $data_siswa[$sekolah_id][$status]['Jml'] += $jumlah;
            $data_siswa[$sekolah_id][$status][$jk] += $jumlah;

            $data_siswa[$sekolah_id]['total']['Jml'] += $jumlah;
            $data_siswa[$sekolah_id]['total'][$jk] += $jumlah;
        }

        $data = [
            'kabupaten_id' => $kabupaten_id,
            'data_siswa' => $data_siswa,
            'status_list' => $status_list
        ];

        return view('v_kecamatan_siswa_tidak_sekolah', $data);
    }
    //======================================== END SISWA TIDAK SEKOLAH ========================================//
}
