<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// userlib.php --
// Common User Functions and Session Control
//------------------------------------------------------------------

//----------------------------------------------------------------------------------------
// acp_message : ACP Message Class
// Manipulates and dispatches messages in the ACP Message Center
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

//require_once ROOT_PATH.'lib/std.php';

class acp_message extends table_frame
{
    public function get($mid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qparts = $this->query_build();

        $where = $DB->format_db_where_string(array('m.mid' => $mid));
        $DB->query("SELECT {$qparts['select']} FROM {$qparts['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function insert()
    {
        global $CFG, $DB;
        
        $this->clean($this->data);
            
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_admin_msg ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['mid'] = $DB->get_insert_id();
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['mid'])) {
            $id = $this->data['mid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('mid' => $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_admin_msg WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['mid'])) {
            return false;
        }
            
        $mid = $this->data['mid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('mid'	=> $mid));
        $DB->query("UPDATE {$CFG['db_pfx']}_admin_msg SET $upd WHERE $where");
        
        $this->data['mid'] = $mid;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['sender']		= (!isset($data[$p.'sender']))			? 0		: $data[$p.'sender'];
        $ndata['date']			= (!isset($data[$p.'date']))			? time(): $data[$p.'date'];
        $ndata['title']			= (!isset($data[$p.'title']))			? ''	: $data[$p.'title'];
        $ndata['message']		= (!isset($data[$p.'message']))			? ''	: $data[$p.'message'];
        $ndata['handled_by']	= (!isset($data[$p.'handled_by']))		? 0		: $data[$p.'handled_by'];
        $ndata['handle_date']	= (!isset($data[$p.'handle_date']))		? 0		: $data[$p.'handle_date'];
        $ndata['type']			= (!isset($data[$p.'type']))			? 0		: $data[$p.'type'];
        $ndata['aux']			= (!isset($data[$p.'aux']))				? 0		: $data[$p.'aux'];
        $ndata['admin_comment']	= (!isset($data[$p.'admin_comment']))	? ''	: $data[$p.'admin_comment'];
        $ndata['user_inform']	= (!isset($data[$p.'user_inform']))		? 0		: $data[$p.'user_inform'];
        $ndata['conversation']	= (!isset($data[$p.'conversation']))	? 0		: $data[$p.'conversation'];
        
        $this->data = $ndata;
    }
    
    public function query_use($item, $val=null)
    {
        global $TPL;
        
        switch ($item) {
            case 'm_user': case 'aux_resource': case 'aux_comment': case 'aux_message':
            case 'r_user': case 'c_user': case 'h_user': break;
            default: $TPL->preprocess_error("comment_class: Invalid USE TAG: $item");
        }
        
        if (!in_array($item, $this->use)) {
            $this->use[] = $item;
        }
        
        if ($item == 'r_user' && !in_array('aux_resource', $this->use)) {
            $this->use[] = 'aux_resource';
        }
        if ($item == 'c_user' && !in_array('aux_comment', $this->use)) {
            $this->use[] = 'aux_comment';
        }
    }
    
    public function query_build()
    {
        global $CFG;
        
        $select = "m.*";
        $from = "{$CFG['db_pfx']}_admin_msg m ";
        
        if (in_array('m_user', $this->use)) {
            $select .= $this->compiled_select('users', 'mu');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users mu ON (m.sender = mu.uid) ";
        }
        
        if (in_array('h_user', $this->use)) {
            $select .= $this->compiled_select('users', 'hu');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users hu ON (m.handled_by = hu.uid) ";
        }
        
        if (in_array('aux_comment', $this->use)) {
            $select .= $this->compiled_select('comments', 'c');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_comments c ON (m.aux = c.cid) ";
        }
        
        if (in_array('c_user', $this->use)) {
            $select .= $this->compiled_select('users', 'cu');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users cu ON (c.uid = cu.uid) ";
        }
        
        if (in_array('aux_resource', $this->use)) {
            $select .= $this->compiled_select('resources', 'r');
            (in_array('aux_comment', $this->use))
                ? $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_resources r ON (c.rid = r.rid) "
                : $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_resources r ON (m.aux = r.rid) ";
        }
        
        if (in_array('r_user', $this->use)) {
            $select .= $this->compiled_select('users', 'ru');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users ru ON (r.uid = ru.uid) ";
        }
        
        if (in_array('aux_message', $this->use)) {
            $select .= $this->compiled_select('messages', 'p');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_messages p ON (p.mid = m.aux) ";
        }
        
        return array('select' => $select, 'from' => $from);
    }
}

//----------------------------------------------------------------------------------------
// message : Message Class
// Interface to message table, used for site-wise messenger
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

class message extends table_frame
{
    public function get($mid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('m.mid' => $mid));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function getByReceiver($rec, $dir=0)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $this->query_condition("m.folder = '$dir'");
        
        $where = $DB->format_db_where_string(array('m.owner' => $rec));
        $this->cquery = $DB->query("SELECT {$qp['select']} FROM {$qp['from']} ".
                                   "WHERE $where {$this->condition} {$this->order} {$this->limit}");
        
        return $this->cquery;
    }
    
    public function insert()
    {
        global $CFG, $DB, $STD;
        
        $this->clean($this->data);
            
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_messages ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['mid'] = $DB->get_insert_id();
    }
    
    public function dispatch($disp_err=0)
    {
        global $CFG, $DB, $STD;
        
        $USER = new user;
        $USER->query_use('group');
        $USER->get($this->data['receiver']);
        
        $err = 0;
        if (!$USER) {
            $err = 1;
        } elseif (!$USER->data['can_msg']) {
            $err = 2;
        } elseif ($USER->data['cur_msgs'] >= $USER->data['msg_capacity']) {
            $err = 3;
        }
        
        if ($err > 0) {
            if ($disp_err) {
                switch ($err) {
                    case 1: $STD->error("The target recipient of this message does not exist."); break;
                    case 2: $STD->error("This user is now allowed to recieve messages."); break;
                    case 3: $STD->error("This user cannot recieve new messages because his or her inbox is full."); break;
                }
            }
            return false;
        }
        //$STD->debug($this);
        $this->insert();
        
        $USER->data['new_msgs']++;
        $USER->data['cur_msgs']++;
        $USER->data['disp_msg'] = 1;
        $USER->update();
        
        // Reflect changes in current user if necessary
        if ($USER->data['uid'] == $STD->user['uid']) {
            $STD->user['new_msgs']++;
            $STD->user['cur_msgs']++;
            $STD->user['disp_msg'] = 1;
        }
        
        return true;
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['mid'])) {
            $id = $this->data['mid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('mid' => $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_messages WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['mid'])) {
            return false;
        }
            
        $mid = $this->data['mid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('mid'	=> $mid));
        $DB->query("UPDATE {$CFG['db_pfx']}_messages SET $upd WHERE $where");
        
        $this->data['mid'] = $mid;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['sender']		= (!isset($data[$p.'sender']))			? 0		: $data[$p.'sender'];
        $ndata['receiver']		= (!isset($data[$p.'receiver']))		? 0		: $data[$p.'receiver'];
        $ndata['owner']			= (!isset($data[$p.'owner']))			? 0		: $data[$p.'owner'];
        $ndata['date']			= (!isset($data[$p.'date']))			? time(): $data[$p.'date'];
        $ndata['title']			= (!isset($data[$p.'title']))			? ''	: $data[$p.'title'];
        $ndata['message']		= (!isset($data[$p.'message']))			? ''	: $data[$p.'message'];
        $ndata['msg_read']		= (!isset($data[$p.'msg_read']))		? 0		: $data[$p.'msg_read'];
        $ndata['read_date']		= (!isset($data[$p.'read_date']))		? 0		: $data[$p.'read_date'];
        $ndata['folder']		= (!isset($data[$p.'folder']))			? 0		: $data[$p.'folder'];
        $ndata['conversation']	= (!isset($data[$p.'conversation']))	? 0		: $data[$p.'conversation'];
        
        $this->data = $ndata;
    }
    
    public function query_use($item, $val=null)
    {
        global $TPL;
        
        switch ($item) {
            case 's_user': case 'r_user': break;
            default: $TPL->preprocess_error("comment_class: Invalid USE TAG: $item");
        }
        
        if (!in_array($item, $this->use)) {
            $this->use[] = $item;
        }
    }
    
    public function query_build()
    {
        global $CFG;
        
        $select = "m.*";
        $from = "{$CFG['db_pfx']}_messages m ";
        
        if (in_array('s_user', $this->use)) {
            $select .= $this->compiled_select('users', 'su');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users su ON (m.sender = su.uid) ";
        }
        
        if (in_array('r_user', $this->use)) {
            $select .= $this->compiled_select('users', 'ru');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users ru ON (m.receiver = ru.uid) ";
        }
        
        return array('select' => $select, 'from' => $from);
    }
}

//----------------------------------------------------------------------------------------
// comment : Comment Class
// Interface to comment table
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

class comment extends table_frame
{
    public function get($cid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qparts = $this->query_build();

        $where = $DB->format_db_where_string(array('c.cid' => $cid));
        $DB->query("SELECT {$qparts['select']} FROM {$qparts['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function getByResource($rid)
    {
        global $CFG, $DB, $STD;
        
        $qparts = $this->query_build();
        
        $where = $DB->format_db_where_string(array('c.rid' => $rid));
        $this->cquery = $DB->query("SELECT {$qparts['select']} FROM {$qparts['from']} ".
                                   "WHERE $where {$this->condition} {$this->order} {$this->limit}");
        
        return $this->cquery;
    }

    public function insert()
    {
        global $CFG, $DB;
        
        $this->clean($this->data);
            
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_comments ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['cid'] = $DB->get_insert_id();
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['cid'])) {
            $id = $this->data['cid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('cid' => $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_comments WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['cid'])) {
            return false;
        }
            
        $cid = $this->data['cid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('cid'	=> $cid));
        $DB->query("UPDATE {$CFG['db_pfx']}_comments SET $upd WHERE $where");
        
        $this->data['cid'] = $cid;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['rid']		= (!isset($data[$p.'rid']))			? 0		: $data[$p.'rid'];
        $ndata['uid']		= (!isset($data[$p.'uid']))			? 0		: $data[$p.'uid'];
        $ndata['date']		= (!isset($data[$p.'date']))		? time(): $data[$p.'date'];
        $ndata['message']	= (!isset($data[$p.'message']))		? ''	: $data[$p.'message'];
        $ndata['type']		= (!isset($data[$p.'type']))		? 0		: $data[$p.'type'];
        $ndata['ip']		= (!isset($data[$p.'ip']))			? ''	: $data[$p.'ip'];
        
        $this->data = $ndata;
    }
    
    public function query_use($item, $val=null)
    {
        global $TPL;
        
        switch ($item) {
            case 'resource': case 'c_user': case 'r_user': case 'news': case 'n_user': break;
            default: $TPL->preprocess_error("comment_class: Invalid USE TAG: $item");
        }
        
        if (!in_array($item, $this->use)) {
            $this->use[] = $item;
        }
        
        if ($item == 'r_user' && !in_array('resource', $this->use)) {
            $this->use[] = 'resource';
        }
        
        if ($item == 'n_user' && !in_array('news', $this->use)) {
            $this->use[] = 'news';
        }
    }

    public function query_build()
    {
        global $CFG;
        
        $select = "c.*";
        $from = "{$CFG['db_pfx']}_comments c ";
        
        if (in_array('resource', $this->use)) {
            $select .= $this->compiled_select('resources', 'r');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_resources r ON (c.rid = r.rid) ";
        }
        
        if (in_array('c_user', $this->use)) {
            $select .= $this->compiled_select('users', 'cu');
            $select .= ',cug.name_prefix cu_name_prefix,cug.name_suffix cu_name_suffix';
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users cu ON (c.uid = cu.uid) ";
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_groups cug ON (cu.gid = cug.gid) ";
        }
        
        if (in_array('r_user', $this->use)) {
            $select .= $this->compiled_select('users', 'ru');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users ru ON (r.uid = ru.uid) ";
        }
        
        if (in_array('news', $this->use)) {
            $select .= $this->compiled_select('news', 'n');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_news n ON (c.rid = n.nid) ";
        }
        
        if (in_array('n_user', $this->use)) {
            $select .= $this->compiled_select('users', 'nu');
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users nu ON (n.uid = nu.uid) ";
        }
        
        return array('select' => $select, 'from' => $from);
    }
}
