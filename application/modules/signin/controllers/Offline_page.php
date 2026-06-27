<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Offline_page extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->helper(array('url', 'libs','form','alert'));
        $this->load->libraries(array('form_validation','session','alert'));
        $this->load->database();

        if(get_settings('site_is_offline') === 'TIDAK'){
          redirect('signin','reload');
        }

    }

    public function index()
    {
        $this->load->view('offline_page');//loading in custom error view     
    }
}
