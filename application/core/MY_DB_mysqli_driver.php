<?php
class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{

    final public function __construct($params)
    {
        parent::__construct($params);
    }

    
    public function _duplicate_insert($table, $values, $exclude_columns = array())
    {

        $exclude_columns = array_map(function ($column) {
            return "`$column`";
        }, $exclude_columns);
        
        $updatestr = array();
        $keystr    = array();
        $valstr    = array();

        foreach ($values as $key => $val) {

            if (!in_array($key, $exclude_columns)) {
                $updatestr[] = $key . " = " . $val;
            }
            
            $keystr[] = $key;
            $valstr[] = $val;
        }

        $sql = "INSERT INTO " . $table . " (" . implode(', ', $keystr) . ") ";
        $sql .= "VALUES (" . implode(', ', $valstr) . ") ";
        $sql .= "ON DUPLICATE KEY UPDATE " . implode(', ', $updatestr);

        return $sql;
    }

    public function _multi_duplicate_insert($table, $values)
    {
        $updatestr = array();
        $keystr    = array();
        $valstr    = null;
        $entries   = array();

        $temp  = array_keys($values);
        $first = $values[$temp[0]];

        foreach ($first as $key => $val) {
            $updatestr[] = $key . " = VALUES(" . $key . ")";
            $keystr[]    = $key;
        }

        foreach ($values as $entry) {
            $valstr = array();
            foreach ($entry as $key => $val) {
                $valstr[] = $val;
            }
            $entries[] = '(' . implode(', ', $valstr) . ')';
        }

        $sql = "INSERT INTO " . $table . " (" . implode(', ', $keystr) . ") ";

        $sql .= "VALUES " . implode(', ', $entries);
        $sql .= "ON DUPLICATE KEY UPDATE " . implode(', ', $updatestr);

        return $sql;
    }

    

    public function on_duplicate($table = '', $set = null, $exclude_columns = array())
    {
        if (!is_null($set)) {
            $this->set($set);
        }

        if (count($this->qb_set) == 0) {
            if ($this->db_debug) {
                return $this->display_error('db_must_use_set');
            }
            return false;
        }

        if ($table == '') {
            if (!isset($this->qb_from[0])) {
                if ($this->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return false;
            }

            $table = $this->qb_from[0];
        }

        $is_multi = false;
        foreach (array_keys($set) as $k => $v) {
            if ($k === $v) {
                $is_multi = true; //is not assoc
                break;
            }
        }

        if ($is_multi) {
            $sql = $this->_multi_duplicate_insert($this->protect_identifiers($table, true, null, false), $this->qb_set);
        } else {
            $sql = $this->_duplicate_insert($this->protect_identifiers($table, true, null, false), $this->qb_set, $exclude_columns);
        }

        $this->_reset_write();
        return $this->query($sql);
    }

}
