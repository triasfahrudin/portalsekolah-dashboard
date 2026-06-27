<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
    }


    public function index()
    {
        $this->load->library(array('grocery_CRUD'));
        $crud = new Grocery_CRUD();
        $crud->set_table('pegawai');
        $crud->set_subject('Profile');

        $crud->required_fields('nama_lengkap');
        $crud->columns('nuptk', 'nama_lengkap', 'email', 'telp');
        $crud->field_type('jk', 'dropdown', array('L' => 'Laki-laki', 'P' => 'Perempuan'));
        $crud->set_relation('sekolah_id', 'sekolah', 'nama');
        $crud->display_as('sekolah_id', 'Unit Kerja');

        $readonly_fields = [/*'sekolah_id',*/ 'terakhir_login', 'nik', 'jabatan', 'status_pegawai', 'agama'];
        foreach ($readonly_fields as $field) {
            $crud->field_type($field, 'readonly');
        }

        $hidden_fields = [
            'id', 'password', 'token_id', 'token_login', 'dev_unique_id', 'aktif', 'tgl_update',
            'dinas_pangkat', 'dinas_unit_kerja', 'dinas_provinsi', 'dinas_kabupaten', 'kdstapeg',
            'kdpangkat', 'gapok', 'mkgolt', 'blgolt', 'prsngapok', 'kdeselon', 'tjeselon', 'kdfungsi',
            'tjfungsi', 'tmtgaji', 'tmtkontrak', 'tatkontrak', 'tmt_gaji', 'tmt_pengangkatan', 'sk_pengangkatan',
            'riwayat_sertifikasi_bidang_studi', 'riwayat_sertifikasi_jenis_sertifikasi', 'riwayat_sertifikasi_tahun_sertifikasi',
            'riwayat_sertifikasi_nomor_sertifikat', 'riwayat_sertifikasi_nrg', 'riwayat_sertifikasi_nomor_peserta',
            'riwayat_pendidikan_formal_bidang_studi', 'riwayat_pendidikan_formal_jenjang_pendidikan',
            'riwayat_pendidikan_formal_gelar_akademik', 'riwayat_pendidikan_formal_satuan_pendidikan_formal',
            'riwayat_pendidikan_formal_fakultas', 'riwayat_pendidikan_formal_kependidikan',
            'riwayat_pendidikan_formal_tahun_masuk', 'riwayat_pendidikan_formal_tahun_lulus',
            'riwayat_pendidikan_formal_nim', 'riwayat_pendidikan_formal_status_kuliah',
            'riwayat_pendidikan_formal_semester', 'riwayat_pendidikan_formal_ipk', 'last_pass_reset',
            'wali_kelas', 'terakhir_login', 'jabatan', 'status_pegawai', 'agama'
        ];
        foreach ($hidden_fields as $field) {
            $crud->field_type($field, 'hidden');
        }

        $display_as_fields = [
            'nuptk'  => 'NUPTK', 'nip'    => 'NIP', 'nik'    => 'NIK', 'jk'     => 'Jenis kelamin',
            'no_rek' => 'Nomor Rekening', 'kode_skpd' => 'Kode SKPD', 'kode_satker' => 'Kode Satker',
            'tgl_lahir' => 'Tanggal Lahir', 'npwp' => 'NPWP','jml_istri' => 'Jumlah Istri/Suami',
        ];
        foreach ($display_as_fields as $field => $display_name) {
            $crud->display_as($field, $display_name);
        }

        $crud->set_field_upload('foto', 'uploads');

        $crud->callback_after_insert(function ($post_array, $primary_key) {
            // Additional logic after insert if needed
        });

        $crud->callback_after_update(function ($post_array, $primary_key) {
            $this->ci->db->where('id', $primary_key);
            $this->ci->db->update('user', ['tgl_update' => date('Y-m-d H:i:s')]);
        });

        $crud->unset_back_to_list();

        return $crud;
    }
}
