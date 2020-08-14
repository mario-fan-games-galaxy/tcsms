<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// lib/news.php --
// News functions
//------------------------------------------------------------------

//require_once ROOT_PATH.'lib/std.php';

class news extends table_frame
{
    public function get($uid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('n.nid'	=> $uid));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function insert()
    {
        global $CFG, $DB, $STD;
        
        $this->clean($this->data);
        
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_news ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['nid'] = $DB->get_insert_id();
        
        return $this->data['nid'];
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['nid'])) {
            $id = $this->data['nid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('nid'	=> $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_news WHERE $where");
        
        // Remove associated comments
        $where = $DB->format_db_where_string(array('rid'	=> $id,
                                                   'type'	=> 2));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_comments WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['nid'])) {
            return false;
        }
        
        $nid = $this->data['nid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('nid'	=> $nid));
        $DB->query("UPDATE {$CFG['db_pfx']}_news SET $upd WHERE $where");
        
        $this->data['nid'] = $nid;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['uid']				= (!isset($data[$p.'uid']))				? 0		: $data[$p.'uid'];
        $ndata['date']				= (!isset($data[$p.'date']))			? time(): $data[$p.'date'];
        $ndata['title']				= (!isset($data[$p.'title']))			? ''	: $data[$p.'title'];
        $ndata['message']			= (!isset($data[$p.'message']))			? ''	: $data[$p.'message'];
        $ndata['comments']			= (!isset($data[$p.'comments']))		? 0		: $data[$p.'comments'];
        $ndata['update_tag']		= (!isset($data[$p.'update_tag']))		? 0		: $data[$p.'update_tag'];
        
        $this->data = $ndata;
    }
    
    public function query_use($item, $val=null)
    {
        global $TPL;
        
        switch ($item) {
            case 'n_user': case 'n_group': break;
            default: $TPL->preprocess_error("user_class: Invalid USE TAG: $item");
        }
        
        if (!in_array($item, $this->use)) {
            $this->use[] = $item;
        }
    }
        
    public function query_build()
    {
        global $STD, $CFG;
        
        $select = "n.*";
        $from = "{$CFG['db_pfx']}_news n ";
        
        if (in_array('n_user', $this->use)) {
            $select .= $this->compiled_select('users', 'nu');
            $select .= ',nug.name_prefix nu_name_prefix,nug.name_suffix nu_name_suffix';
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users nu ON (n.uid = nu.uid) ";
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_groups nug ON (nu.gid = nug.gid) ";
        }
        
        return array('select' => $select, 'from' => $from);
    }
}
