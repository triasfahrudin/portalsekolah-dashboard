<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Doc_model extends CI_Model
{

    public function get_document($user_id)
    {
        $this->db->select(
            "a.id, a.nama,
             b.alasan, IFNULL(b.file_dokumen,'belum') AS user_file_dokumen,
		     IF(IFNULL(b.file_dokumen,'belum') = 'belum','-',b.verifikasi) AS verifikasi"
        );
        $this->db->join('dokumen_pegawai b', "a.id = b.jenis_dokumen_id AND b.pegawai_id = '$user_id'", 'LEFT OUTER');
        return $this->db->get('jenis_dokumen a');
    }
}
