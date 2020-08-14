<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// mysql.php --
// MySQL Database Abstraction Layer
//------------------------------------------------------------------

class db_driver
{
    public $connection		= null;
    public $cache			= array();
    public $query_count	= 0;
    public $last_query		= null;
    public $debug_out		= '';
    public $debug			= 0;
    public $total_time		= 0;
    
    public function connect()
    {
        global $CFG;

        //$this->connection = @mysql_connect(
        $this->connection = @mysqli_connect(
            $CFG['db_host'],
            $CFG['db_user'],
            $CFG['db_pass']
        );
        
        if (!$this->connection) {
            $this->error('Fatal Error: Cannot connect to MySQL Server on '.$CFG['db_host']);
        }
        
        //if (!mysql_select_db($CFG['db_db']))
        if (!mysqli_select_db($this->connection, $CFG['db_db'])) {
            $this->error('Fatal Error: Cannot find database: '.$CFG['db_db']);
        }
    }
    
    public function query($query)
    {
        global $CFG;
        
        if ($this->debug) {
            $start_time = microtime();
        }

        //$this->last_query = mysql_query($query);
        $this->last_query = mysqli_query($this->connection, $query);
        
        // Debug display formatted after mySQL Debug display from Invision Power Board 1.3
        
        if ($this->debug) {
            $end_time = microtime();
            $time = $end_time - $start_time;
            
            (preg_match('/^SELECT/', $query)) ? $query_type = 'SELECT' : $query_type = 'NON SELECT';
            
            $bgcolor1 = '#DDDDDD';
            $bgcolor2 = '#EEEEEE';
            if ($query_type == 'SELECT') {
                //$quer = mysql_query("EXPLAIN $query");
                $quer = mysqli_query("EXPLAIN $query");
                $bgcolor1 = '#CFD1FB';
                $bgcolor2 = '#E9EAFD';
            }
            
            $this->debug_out .= "<table width='95%' border='1' cellpadding='4' cellspacing='0' align='center' style='font-family:verdana; font-size:10pt'>
								   <tr><td colspan='8' bgcolor='$bgcolor1' style='font-size:12pt'><b>$query_type Query</b></td></tr>
								   <tr><td colspan='8' bgcolor='$bgcolor2' style='font-family:courier'>$query</td></tr>";
            
            if ($query_type == 'SELECT') {
                $this->debug_out .= "<tr bgcolor='$bgcolor1'>
									   <td><b>table</b></td><td><b>type</b></td>
									   <td><b>possible_keys</b></td><td><b>key</b></td>
									   <td><b>key_len</b></td><td><b>ref</b></td>
									   <td><b>rows</b></td><td><b>Extra</b></td>
									 </tr>";
                                     
                while ($v = $this->fetch_row($quer)) {
                    switch ($v['type']) {
                        case 'ALL': case 'index': $style = '#F9EDB3'; break;
                        default: $style = '#B3F9B5'; break;
                    }
                    
                    $this->debug_out .= "<tr>
										   <td>{$v['table']}&nbsp;</td>
										   <td bgcolor='$style'>{$v['type']}&nbsp;</td>
										   <td>{$v['possible_keys']}&nbsp;</td><td>{$v['key']}&nbsp;</td>
										   <td>{$v['key_len']}&nbsp;</td><td>{$v['ref']}&nbsp;</td>
										   <td>{$v['rows']}&nbsp;</td><td>{$v['Extra']}&nbsp;</td>
										 </tr>";
                }
            }
            
            $time = round($time, 6);
            $this->total_time += $time;
            if ($time > 0.1) {
                $time = "<span style='color:red'><b>$time</b></span>";
            }
            
            $this->debug_out .= "<tr>
								   <td colspan='8' bgcolor='$bgcolor1' style='font-size:12pt'><b>MySQL Time:</b> $time</td>
								 </tr></table><br />";
        }
        
        // End Debug
        
        //if (!$this->last_query)
        if (mysqli_error($this->connection)) {
            $this->error('MySQL Error - There was an error in the following query:'.$query);
        }
        
        $this->cache[] = $this->last_query;
        $this->query_count++;
        
        return $this->last_query;
    }
    
    public function show_debug()
    {
        $this->debug_out .= "<table width='95%' border='1' cellpadding='4' cellspacing='0' align='center' style='font-family:verdana; font-size:10pt'>
							   <tr>
							     <td colspan='8' bgcolor='#FFFFFF' style='font-size:12pt'><b>Total MySQL Time:</b> {$this->total_time}</td>
							   </tr></table><br />";
        
        return $this->debug_out;
    }
    
    public function fetch_row($rid='', $type=MYSQLI_ASSOC)
    {
        if ($type === false) {
            $type = MYSQL_ASSOC;
        }
        
        if ($rid == '') {
            $rid = $this->last_query;
        }
        
        //$row = mysql_fetch_array($rid, $type);
        $row = mysqli_fetch_array($rid, $type);
        
        return $row;
    }
    
    public function get_num_rows($rid='')
    {
        if ($rid == '') {
            $rid = $this->last_query;
        }
        
        //$num_rows = mysql_num_rows($rid);
        $num_rows = mysqli_num_rows($rid);
        
        return $num_rows;
    }
    
    public function get_affected_rows()
    {
        
        //$affected = mysql_affected_rows($this->connection);
        $affected = mysqli_affected_rows($this->connection);
        
        return $affected;
    }
    
    public function free_result($rid='')
    {
        if ($rid == '') {
            $rid = $this->last_query;
        }
            
        //@mysql_free_result($rid);
        @mysqli_free_result($rid);
    }
    
    public function close_db()
    {
        reset($this->cache);
        while (list(, $val) = each($this->cache)) {
            //@mysql_free_result($rid);
            @mysqli_free_result($rid);
        }
        
        //@mysql_close($this->connection);
        @mysqli_close($this->connection);
    }
    
    public function get_query_count()
    {
        return $this->query_count;
    }
    
    public function get_insert_id()
    {
        
        //$id = mysql_insert_id($this->connection);
        $id = mysqli_insert_id($this->connection);
        
        return $id;
    }
    
    public function clean_value($value)
    {
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        
        //$value = mysql_real_escape_string ($value, $this->connection);
        $value = mysqli_real_escape_string($this->connection, $value);
        
        return $value;
    }
    
    public function error($msg)
    {
        global $CFG;
        
        //$errCode = mysql_errno();
        //$error   = mysql_error();
        $errCode = mysqli_errno();
        $error   = mysqli_error();
        
        $html = "<html><head><title>Database Error</title></head>
				 <body style='font-family:Arial'><h1>Database Error</h1>
				 An unrecoverable database error has ouccurred.  Please wait a few minutes and
				 try again.  If the error still persists, please contact the 
				 <a href='mailto:{$CFG['admin_email']}'>Site Staff</a> with the error information
				 below.<br /><br /><p align='center'>
				 <table border='0' cellspacing='0' cellpadding='2' width='80%'><tr>
				 <td width='100%' style='font-family:Courier New'>
				 $msg<br><br>Mysql Error: $errCode<br>$error
				 </td></tr></table></p></body></html>";
        
        @ob_get_clean();
             
        echo $html;
        
        exit;
    }
    
    public function format_db_values($data)
    {
        $fields = '';
        $values = '';
        
        reset($data);
        while (list($key, $val) = each($data)) {
            $fields .= "`$key`,";
            //$values .= "'" . str_replace("'", "\\'", $val) . "',";
            $values .= "'" . $this->clean_value($val) . "',";
        }
        
        $fields = preg_replace("/,$/", '', $fields);
        $values = preg_replace("/,$/", '', $values);
        
        return array('FIELDS' => $fields, 'VALUES' => $values);
    }
    
    public function format_db_update_values($data)
    {
        $string = '';
        
        reset($data);
        while (list($key, $val) = each($data)) {
            //$val = str_replace("'", "\\'", $val);
            $val = $this->clean_value($val);
            $string .= "$key = '$val',";
        }
        
        $string = preg_replace("/,$/", '', $string);
        
        return $string;
    }
    
    public function format_db_where_string($data)
    {
        $string = '';
        
        reset($data);
        while (list($key, $val) = each($data)) {
            //$val = str_replace("'", "\\'", $val);
            $val = $this->clean_value($val);
            $string .= "$key = '$val' AND ";
        }
        
        $string = preg_replace("/ AND $/", '', $string);
        
        return $string;
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
    
    public function makeSimpleSelect($table_args, $select_args, $where_args, $order=null, $limit=null)
    {
        $sql = 'SELECT ' . $this->makeSimpleSelectString($select_args) . ' FROM ' .
            $this->makeSimpleTableString($table_args) . ' WHERE ' .	$this->makeSimpleWhere($where_args);
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
