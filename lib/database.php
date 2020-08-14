<?php

require_once 'DB.php';
require_once 'error.php';

class Database
{
    public $db = null;
    public $settings = array();
    public $eh = null;

    public function Database(&$settings, $pdb = null)
    {
        $this->settings = $settings;
        $this->eh = new ErrorHandler();

        if ($pdb != null) {
            $this->db = $pdb;
        } else {
            $this->connect();
        }
    }

    public function connect()
    {
        $dsn = array(
            'phptype'  => $this->settings['database'],
            'username' => $this->settings['db_user'],
            'password' => $this->settings['db_pass'],
            'hostspec' => $this->settings['db_host'],
            'database' => $this->settings['db_db']);

        $options = array(
            'debug' => 2,
            'portability' => DB_PORTABILITY_ALL);

        $this->db =& DB::Connect($dsn, $options);

        if (PEAR::isError($this->db)) {
            $this->eh->raiseError(105, $this->db->getMessage());
        }

        $this->db->setFetchMode(DB_FETCHMODE_OBJECT);

        return true;
    }

    public function query($query, $debug = 0)
    {
        $res =& $this->db->query($query);
        if (PEAR::isError($res)) {
            $this->eh->raiseError(101, $this->_errorMessage($res, $debug));
        }

        return $res;
    }

    public function _errorMessage($pearError, $debug)
    {
        $msg = $pearError->getMessage();
        if ($debug > 0) {
            $msg .= ')<br><br><b>Debug Level 1:</b><br>' . $pearError->getUserInfo();
        }

        return $msg;
    }

    public function prepArguments($args)
    {
        $preped = array();
        reset($args);
        while (list($key, $val) = each($args)) {
            $preped[$key] = $this->db->quote($val);
        }
        return $preped;
    }

    public function makeSimpleSelect($table_args, $select_args, $where_args, $order = null, $limit = null)
    {
        $sql = 'SELECT ' . $this->makeSimpleSelectString($select_args) . ' FROM ' .
            $this->makeSimpleTableString($table_args) . ' WHERE ' . $this->makeSimpleWhere($where_args);
        if ($order != null) {
            $sql .= ' ORDER BY ' . $oder;
        }
        if ($limit != null) {
            $sql .= ' LIMIT ' . $limit;
        }

        return $sql;
    }

    public function makeSimpleUpdate($table_args, $set_args, $where_args)
    {
        $sql = 'UPDATE ' . $this->makeSimpleTableString($table_args) . ' SET ' .
            $this->makeSimpleSetList($set_args) . ' WHERE ' . $this->makeSimpleWhere($where_args);
        return $sql;
    }

    public function makeToken()
    {
        return md5(uniqid(rand(), true));
    }

    public function disconnect()
    {
        $this->db->disconnect();
    }

    public function makeSimpleSetList($set_args)
    {
        $setStr = '';
        reset($set_args);
        while (list($key, $val) = each($set_args)) {
            if ($setStr != '') {
                $setStr .= ', ';
            }
            $setStr .= $key . ' = ' . $val;
        }

        return $setStr;
    }

    public function makeSimpleWhere($args)
    {
        $where = '';
        reset($args);
        while (list($key, $val) = each($args)) {
            if ($where != '') {
                $where .= ' AND ';
            }
            $where .= $key . ' = ' . $val;
        }
        return $where;
    }

    public function makeSimpleSelectString($select_args)
    {
        $selStr = '';
        reset($select_args);
        while (list($key, $val) = each($select_args)) {
            if ($selStr != '') {
                $selStr .= ', ';
            }
            if (is_numeric($key)) {
                $selStr .= $val;
            } else {
                $selStr .= $val . ' AS ' . $key;
            }
        }

        return $selStr;
    }

    public function makeSimpleTableString($table_args)
    {
        if (!is_array($table_args)) {
            return $table_args;
        }

        $tableStr = '';
        reset($table_args);
        while (list($key, $val) = each($table_args)) {
            if ($tableStr != '') {
                $tableStr .= ', ';
            }
            $tableStr .= $val . ' ' . $key;
        }

        return $tableStr;
    }
}
