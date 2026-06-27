<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH . "third_party/MX/Loader.php";

class MY_Loader extends MX_Loader
{

    public function database($params = '', $return = false, $active_record = null)
    {
        // Grab the super object
        $CI = &get_instance();

        // Do we even need to load the database class?
        if (class_exists('CI_DB') and $return == false and $active_record == null and isset($CI->db) and is_object($CI->db)) {
            return false;
        }

        require_once BASEPATH . 'database/DB.php';

        $db = DB($params, $active_record);

        // Load extended DB driver
        $custom_db_driver      = config_item('subclass_prefix') . 'DB_' . $db->dbdriver . '_driver';
        $custom_db_driver_file = APPPATH . 'core/' . $custom_db_driver . '.php';

        if (file_exists($custom_db_driver_file)) {
            require_once $custom_db_driver_file;

            $db = new $custom_db_driver(get_object_vars($db));
        }

        // Return DB instance
        if ($return === true) {
            return $db;
        }

        // Initialize the db variable. Needed to prevent reference errors with some configurations
        $CI->db = '';
        $CI->db = &$db;
    }

}
