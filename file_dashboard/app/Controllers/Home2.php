<?php

namespace App\Controllers;

use App\Models\PegawaiModel;
use App\Models\SiswaModel;

class Home extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $queryPegawai = $db->query("SELECT COUNT(id) AS total FROM pegawai");
        $resultPegawai = $queryPegawai->getRow();

        $querySiswa = $db->query("SELECT COUNT(id) AS total FROM siswa");
        $resultSiswa = $querySiswa->getRow();

        $querySekolah = $db->query("SELECT COUNT(id) AS total FROM sekolah");
        $resultSekolah = $querySekolah->getRow();

        //======================================== KODE GRAFIK PEGAWAI ========================================//
            $queryGrafikPegawai = $db->query("
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
            ");

            $resultGrafikPegawai = $queryGrafikPegawai->getResultArray();

            $data_pegawai = [];
            $status_list = [];

            foreach ($resultGrafikPegawai as $row) {
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
                $data_pegawai[$provinsi_id][$status_pegawai][$jk] += $jumlah;

                $data_pegawai[$provinsi_id]['total']['Jml'] += $jumlah;
                $data_pegawai[$provinsi_id]['total'][$jk] += $jumlah;
            }
        //======================================== END KODE GRAFIK PEGAWAI ========================================//

        $data = [
            'title'             => 'Dashboard Sekolah',
            'totalPegawai'      => $resultPegawai ? $resultPegawai->total : 0, // memastikan data tidak null
            'totalSiswa'        => $resultSiswa ? $resultSiswa->total : 0,
            'totalSekolah'      => $resultSekolah ? $resultSekolah->total : 0,
            'data_pegawai'      => $data_pegawai,
            'status_list'       => $status_list
        ];

        return view('dashboard_sekolah', $data);
    }

    //======================================== PEGAWAI ========================================//
    public function pegawai()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
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
        ");

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
            WHERE c.id = '$provinsi_id'
            GROUP BY d.id, d.nama, a.jk, a.status_pegawai
            ORDER BY d.id
        ");

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id   = $row['kabupaten_id'];
            $kabupaten      = $row['kabupaten'];
            $status_pegawai = $row['status_pegawai'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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
            WHERE d.id = '$kabupaten_id'
            GROUP BY e.id, e.nama, a.jk, a.status_pegawai
            ORDER BY e.id
        ");

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id    = $row['provinsi_id'];
            $kecamatan_id   = $row['kecamatan_id'];
            $kecamatan      = $row['kecamatan'];
            $status_pegawai = $row['status_pegawai'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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
            'provinsi_id'   => $provinsi_id,
            'data_pegawai'  => $data_pegawai,
            'status_list'   => $status_list
        ];

        return view('v_kecamatan_pegawai', $data);
    }

    public function sekolah_pegawai($kecamatan_id)
    {
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
            WHERE e.id = '$kecamatan_id'
            GROUP BY b.id, b.nama, a.jk, a.status_pegawai
            ORDER BY e.id
        ");

        $result = $query->getResultArray();

        $data_pegawai = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id   = $row['kabupaten_id'];
            $sekolah_id     = $row['sekolah_id'];
            $sekolah        = $row['sekolah'];
            $status_pegawai = $row['status_pegawai'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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
            'kabupaten_id'  => $kabupaten_id,
            'data_pegawai'  => $data_pegawai,
            'status_list'   => $status_list
        ];

        return view('v_sekolah_pegawai', $data);
    }
    //======================================== END PEGAWAI ========================================//


    //======================================== SISWA ========================================//
    public function siswa()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
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
        ");

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

        return view('v_siswa', $data);
    }

    public function kabupaten_siswa($provinsi_id)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
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
            WHERE c.id = '$provinsi_id'
            GROUP BY d.id, d.nama, a.jk, b.level
            ORDER BY d.id
        ");

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id   = $row['kabupaten_id'];
            $kabupaten      = $row['kabupaten'];
            $level_siswa    = $row['level_siswa'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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

        return view('v_kabupaten_siswa', $data);
    }

    public function kecamatan_siswa($kabupaten_id)
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                a.jk,
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
            WHERE d.id = '$kabupaten_id'
            GROUP BY e.id, e.nama, a.jk, b.level
            ORDER BY e.id
        ");

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $provinsi_id    = $row['provinsi_id'];
            $kecamatan_id   = $row['kecamatan_id'];
            $kecamatan      = $row['kecamatan'];
            $level_siswa    = $row['level_siswa'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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
            'provinsi_id'   => $provinsi_id,
            'data_siswa'  => $data_siswa,
            'status_list'   => $status_list
        ];

        return view('v_kecamatan_siswa', $data);
    }

    public function sekolah_siswa($kecamatan_id)
    {
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
            WHERE e.id = '$kecamatan_id'
            GROUP BY b.id, b.nama, a.jk, b.level
            ORDER BY e.id
        ");

        $result = $query->getResultArray();

        $data_siswa = [];
        $status_list = [];

        foreach ($result as $row) {
            $kabupaten_id   = $row['kabupaten_id'];
            $sekolah_id     = $row['sekolah_id'];
            $sekolah        = $row['sekolah'];
            $level_siswa    = $row['level_siswa'];
            $jk             = $row['jk'];
            $jumlah         = $row['jumlah'];

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
            'kabupaten_id'  => $kabupaten_id,
            'data_siswa'    => $data_siswa,
            'status_list'   => $status_list
        ];

        return view('v_sekolah_siswa', $data);
    }
    //======================================== END SISWA ========================================//


    //======================================== SEKOLAH ========================================//
    public function sekolah()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                b.id AS provinsi_id, 
                b.nama AS provinsi, 
                a.level, a.status, 
                COUNT(*) as total 
            FROM sekolah as a
            JOIN wilayah_provinsi b ON a.provinsi = b.id
            GROUP BY b.id, a.level, a.status
        ");

        $sekolah = $query->getResultArray();

        return view('v_sekolah', ['sekolah' => $sekolah]);
    }

    public function kabupaten_sekolah($provinsi_id)
    {
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
            WHERE c.provinsi_id = '$provinsi_id'
            GROUP BY c.id, a.level, a.status
        ");

        $sekolah = $query->getResultArray();

        return view('v_kabupaten_sekolah', ['sekolah' => $sekolah]);
    }

    public function kecamatan_sekolah($kabupaten_id)
    {
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
            WHERE d.kabupaten_id = '$kabupaten_id'
            GROUP BY d.id, a.level, a.status
        ");

        $kecamatan = $query->getResultArray();

        return view('v_kecamatan_sekolah', ['kecamatan' => $kecamatan]);
    }

    public function data_sekolah($kecamatan_id)
    {
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
            WHERE d.id = '$kecamatan_id'
            GROUP BY a.id, a.level, a.status
        ");

        $sekolah = $query->getResultArray();

        return view('v_data_sekolah', ['sekolah' => $sekolah]);
    }
    //======================================== END SEKOLAH ========================================//
}
