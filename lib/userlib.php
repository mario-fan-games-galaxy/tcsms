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
// user : User Class
// Stores user information and manipulates users.
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

//require_once ROOT_PATH.'lib/std.php';

class user extends table_frame
{
    public $data			= array();
    public $rights_list	= array();
    
    public function get($uid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('u.uid'	=> $uid));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function getByName($username)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('u.username'	=> $username));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function getByLogin($username, $password)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('username'	=> $username,
                                                   'password'	=> $password));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function insert()
    {
        global $CFG, $DB, $STD;
        
        $this->clean($this->data);
        
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_users ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['uid'] = $DB->get_insert_id();
        
        return $this->data['uid'];
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['uid'])) {
            $id = $this->data['uid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('uid'	=> $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_users WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['uid'])) {
            return false;
        }
        
        $uid = $this->data['uid'];
        $udata = $this->data;
        $this->clean($this->data);
        
        // Never let this happen again
        if (empty($this->data['gid'])) {
            $session->bots = 0;
            $backtrace = debug_backtrace();
            $state  = "<pre>BACKTRACE:<br>" . print_r($backtrace, 1) . "<br><br>";
            $state .= "SESSION:<br>" . print_r($session, 1) . "<br><br>";
            $state .= "STD:<br>" . print_r($STD, 1) . "<br><br>";
            exit("<b>CRITICAL:</b> LOSS OF USER ACCOUNT INFORMATION DETECTED.  Execution has aborted before permenant user account corruption could occurr.<br><br>Dumping State:<br><br>$state");
        }
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('uid'	=> $uid));
        $DB->query("UPDATE {$CFG['db_pfx']}_users SET $upd WHERE $where");
        
        //$this->data['uid'] = $uid;
        $this->data = $udata;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['gid']				= (!isset($data[$p.'gid']))				? 0		: $data[$p.'gid'];
        $ndata['username']			= (!isset($data[$p.'username']))		? ''	: $data[$p.'username'];
        $ndata['password']			= (!isset($data[$p.'password']))		? ''	: $data[$p.'password'];
        $ndata['email']				= (!isset($data[$p.'email']))			? ''	: $data[$p.'email'];
        $ndata['website']			= (!isset($data[$p.'website']))			? ''	: $data[$p.'website'];
        $ndata['weburl']			= (!isset($data[$p.'weburl']))			? ''	: $data[$p.'weburl'];
        $ndata['icon']				= (!isset($data[$p.'icon']))			? ''	: $data[$p.'icon'];
        $ndata['aim']				= (!isset($data[$p.'aim']))				? ''	: $data[$p.'aim'];
        $ndata['icq']				= (!isset($data[$p.'icq']))				? ''	: $data[$p.'icq'];
        $ndata['msn']				= (!isset($data[$p.'msn']))				? ''	: $data[$p.'msn'];
        $ndata['yim']				= (!isset($data[$p.'yim']))				? ''	: $data[$p.'yim'];
        $ndata['def_order_by']		= (!isset($data[$p.'def_order_by']))	? ''	: $data[$p.'def_order_by'];
        $ndata['def_order']			= (!isset($data[$p.'def_order']))		? ''	: $data[$p.'def_order'];
        $ndata['skin']				= (!isset($data[$p.'skin']))			? 0		: $data[$p.'skin'];
        $ndata['registered_ip']		= (!isset($data[$p.'registered_ip']))	? ''	: $data[$p.'registered_ip'];
        $ndata['items_per_page']	= (!isset($data[$p.'items_per_page']))	? 0		: $data[$p.'items_per_page'];
        $ndata['show_email']		= (!isset($data[$p.'show_email']))		? 0		: $data[$p.'show_email'];
        $ndata['first_submit']		= (!isset($data[$p.'first_submit']))	? 0		: $data[$p.'first_submit'];
        $ndata['cookie']			= (!isset($data[$p.'cookie']))			? ''	: $data[$p.'cookie'];
        $ndata['comments']			= (!isset($data[$p.'comments']))		? 0		: $data[$p.'comments'];
        $ndata['new_msgs']			= (!isset($data[$p.'new_msgs']))		? 0		: $data[$p.'new_msgs'];
        $ndata['join_date']			= (!isset($data[$p.'join_date']))		? time(): $data[$p.'join_date'];
        $ndata['timezone']			= (!isset($data[$p.'timezone']))		? 0		: $data[$p.'timezone'];
        $ndata['dst']				= (!isset($data[$p.'dst']))				? 0		: $data[$p.'dst'];
        $ndata['disp_msg']			= (!isset($data[$p.'disp_msg']))		? 0		: $data[$p.'disp_msg'];
        $ndata['icon_dims']			= (!isset($data[$p.'icon_dims']))		? ''	: $data[$p.'icon_dims'];
        $ndata['cur_msgs']			= (!isset($data[$p.'cur_msgs']))		? 0		: $data[$p.'cur_msgs'];
        $ndata['show_thumbs']		= (!isset($data[$p.'show_thumbs']))		? 0		: $data[$p.'show_thumbs'];
        $ndata['use_comment_msg']	= (!isset($data[$p.'use_comment_msg']))	? 0		: $data[$p.'use_comment_msg'];
        $ndata['use_comment_digest']= (!isset($data[$p.'use_comment_digest']))	? 0		: $data[$p.'use_comment_digest'];
        $ndata['last_visit']		= (!isset($data[$p.'last_visit']))		? 0		: $data[$p.'last_visit'];
        $ndata['last_activity']		= (!isset($data[$p.'last_activity']))	? 0		: $data[$p.'last_activity'];
        
        $this->data = $ndata;
    }
    
    public function query_use($item, $val = null)
    {
        global $STD;
                
        switch ($item) {
            case 'group': case 'session': break;
            default: $STD->template->preprocess_error("user_class: Invalid USE TAG: $item");
        }
        
        if (!in_array($item, $this->use)) {
            $this->use[] = $item;
        }
    }
    
    public function query_build()
    {
        global $CFG;
        
        $select = "u.*";
        $from = "{$CFG['db_pfx']}_users u ";
        
        if (in_array('group', $this->use)) {
            $select .= ",g.*";
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_groups g ON (u.gid = g.gid) ";
        }
        
        if (in_array('session', $this->use)) {
            $select .= ",s.time AS s_time,s.sessid AS s_sessid,s.ip AS s_ip";
            $from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_sessions s ON (u.uid = s.uid) ";
        }
        
        return array('select' => $select, 'from' => $from);
    }
    
    public function compile_digest()
    {
        global $DB, $CFG, $STD, $session;
        
        if ($this->data['use_comment_msg'] == 0 || $this->data['use_comment_digest'] == 0) {
            return;
        }
        
        $last_active = time() - $this->data['last_visit'];

        if ($last_active < 3600) {
            return;
        }
        
        $session->touch_data('digest');
        if ($session->data['digest'] == 1) {
            return;
        }
        
        $session->data['digest'] = 1;

        require_once ROOT_PATH.'lib/message.php';
        
        $cutoff = $this->data['last_visit'] + 15*60;

        //$where = $DB->format_db_where_string(array('c.type'	=> 1,
        //										   'r.uid'	=> $this->data['uid']));
        //$DB->query("SELECT DISTINCT r.rid,r.type,r.title FROM {$CFG['db_pfx']}_resources r
        //			LEFT JOIN {$CFG['db_pfx']}_comments c ON (r.rid = c.rid) WHERE {$where} AND c.date > '{$cutoff}'");
        
        $where = $DB->format_db_where_string(array('r.uid' => $this->data['uid']));
        $DB->query("SELECT r.rid,r.type,r.title FROM {$CFG['db_pfx']}_resources r WHERE {$where} AND r.comment_date > '{$cutoff}'");
        
        if ($DB->get_num_rows() == 0) {
            return;
        }
        
        $mesg = "You have recieved one or more comments on the following submissions since your last visit:<br /><ul>";

        while ($row = $DB->fetch_row()) {
            $location = "act=resdb&param=02&c={$row['type']}&id={$row['rid']}&st=new";
            $mesg .= "<li><a href='{%site_url%}?$location'>{$row['title']}</a></li>";
        }
        
        $mesg .= "</ul>";
        
        $MSG = new message;
        $MSG->data['receiver'] = $this->data['uid'];
        $MSG->data['owner'] = $this->data['uid'];
        $MSG->data['title'] = "Comments received on one or more submissions.";
        $MSG->data['message'] = $mesg;
        $MSG->dispatch();
        
        $MSG->data['conversation'] = $MSG->data['mid'];
        $MSG->update();
    }
}

//----------------------------------------------------------------------------------------
// group : Group Class
// Manage User Groups
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

class group extends table_frame
{
    public function get($gid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $where = $DB->format_db_where_string(array('gid'	=> $gid));
        $DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }

    public function insert()
    {
        global $CFG, $DB, $STD;
        
        $this->clean($this->data);
        
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_groups ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['gid'] = $DB->get_insert_id();
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['gid'])) {
            $id = $this->data['gid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('gid'	=> $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_groups WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['gid'])) {
            return false;
        }
        
        $gid = $this->data['gid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('gid'	=> $gid));
        $DB->query("UPDATE {$CFG['db_pfx']}_groups SET $upd WHERE $where");
        
        $this->data['gid'] = $gid;
    }

    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['group_name']	= (!isset($data[$p.'group_name']))		? ''	: $data[$p.'group_name'];
        $ndata['group_title']	= (!isset($data[$p.'group_title']))		? ''	: $data[$p.'group_title'];
        $ndata['msg_capacity']	= (!isset($data[$p.'msg_capacity']))	? 0		: $data[$p.'msg_capacity'];
        $ndata['moderator']		= (!isset($data[$p.'moderator']))		? 0 	: $data[$p.'moderator'];
        $ndata['acp_access']	= (!isset($data[$p.'acp_access']))		? 0 	: $data[$p.'acp_access'];
        $ndata['acp_modq']		= (!isset($data[$p.'acp_modq']))		? 0		: $data[$p.'acp_modq'];
        $ndata['acp_users']		= (!isset($data[$p.'acp_users']))		? 0		: $data[$p.'acp_users'];
        $ndata['acp_news']		= (!isset($data[$p.'acp_news']))		? 0 	: $data[$p.'acp_news'];
        $ndata['acp_msg']		= (!isset($data[$p.'acp_msg']))			? 0 	: $data[$p.'acp_msg'];
        $ndata['acp_super']		= (!isset($data[$p.'acp_super']))		? 0 	: $data[$p.'acp_super'];
        $ndata['can_submit']	= (!isset($data[$p.'can_submit']))		? 0 	: $data[$p.'can_submit'];
        $ndata['can_comment']	= (!isset($data[$p.'can_comment']))		? 0 	: $data[$p.'can_comment'];
        $ndata['can_report']	= (!isset($data[$p.'can_report']))		? 0 	: $data[$p.'can_report'];
        $ndata['can_modify']	= (!isset($data[$p.'can_modify']))		? 0		: $data[$p.'can_modify'];
        $ndata['can_msg']		= (!isset($data[$p.'can_msg']))			? 0		: $data[$p.'can_msg'];
        $ndata['can_msg_users']	= (!isset($data[$p.'can_msg_users']))	? 0		: $data[$p.'can_msg_users'];
        $ndata['edit_comment']	= (!isset($data[$p.'edit_comment']))	? 0		: $data[$p.'edit_comment'];
        $ndata['delete_Comment']= (!isset($data[$p.'delete_comment']))	? 0		: $data[$p.'delete_comment'];
        $ndata['use_bbcode']	= (!isset($data[$p.'use_bbcode']))		? 0		: $data[$p.'use_bbcode'];
        $ndata['name_prefix']	= (!isset($data[$p.'name_prefix']))		? 0		: $data[$p.'name_prefix'];
        $ndata['name_suffix']	= (!isset($data[$p.'name_suffix']))		? 0		: $data[$p.'name_suffix'];
        
        $this->data = $ndata;
    }
    
    public function query_build()
    {
        global $CFG;
        
        $select = "g.*";
        $from = "{$CFG['db_pfx']}_groups g ";
        
        return array('select' => $select, 'from' => $from);
    }
}

//----------------------------------------------------------------------------------------
// session : Session Handling Class
// Authenitcates users and handles session data
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

class session
{
    
    //var $sess_id		= '';
    //var $failed			= false;
    //var $read_resources = array();
    
    // ---
    
    public $phpver			= '';
    public $sess_id		= '';
    public $sess_cookie	= '';
    public $sess_active	= 0;
    public $using_cookies	= 0;
    public $location		= '';
    public $sess_fail		= '';
    
    public $user			= array();
    public $data			= array();
    
    public $bots			= array();
    
    //function session () {
    public function __construct()
    {
        $this->phpver = phpversion();
        
        $this->bots = array(
            'Alexa'				=> 'ia_archiver',
            'Baiduspider'		=> 'Baiduspider',
            'Exabot'			=> 'Exabot',
            'Gigabot'			=> 'Gigabot',
            'Google'			=> 'Googlebot',
            'Google Adsense'	=> 'Mediapartners-Google',
            'MSN'				=> 'msnbot',
            'MSRBOT'			=> 'MSRBOT',
            'Twiceler'			=> 'Twiceler',
            'Yahoo'				=> 'Yahoo! Slurp',
        );
    }
    
    public function is_bot()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        
        foreach ($this->bots as $k => $v) {
            if (preg_match("/{$v}/i", $ua)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function get_session_id()
    {
        $sess = '';
        
        $this->sess_fail .= '.get';
        $cookie = $this->get_cookie('sess');

        if (!empty($cookie)) {
            $cookie = trim($cookie);
            list($id, $hashcode) = explode(',', $cookie);
            
            $this->sess_fail .= '.1';
            
            if (isset($id) && isset($hashcode)) {
                $this->sess_fail .= '.2';
                
                if (preg_match("/^[A-Fa-f0-9]{32}$/", $hashcode)) {
                    $this->sess_cookie = $hashcode;
                    $sess = $id;
                    $this->sess_fail .= '.3';
                }
            }
            
            $this->using_cookies = 1;
        } elseif (!empty($_GET['sess'])) {
            $sess = trim($_GET['sess']);
            $this->sess_fail .= '.4';
        }
        
        if (!preg_match("/^[A-Fa-f0-9]{32}$/", trim($sess))) {
            $sess = '';
            $this->sess_fail .= '.5';
        }
        
        return $sess;
    }
    
    public function create_session_id($hash=0, $resident=0, $uid=0)
    {
        $sess = md5(uniqid(mt_rand(), true));
        $cookie_val = $sess . ',' . $hash;
        
        $time = 0;
        if ($resident) {
            $time = time() + 3600*24*355;
        }
        
        $this->set_cookie('sess', $cookie_val, 3600*24*365);

        return $sess;
    }
    
    public function get_cookie($name)
    {
        global $CFG;
        
        $cookie = $CFG['cookie_prefix'] . $name;
        if (empty($_COOKIE[$cookie])) {
            return '';
        }
        
        return $_COOKIE[$cookie];
    }
    
    public function set_cookie($name, $value, $expire = 0)
    {
        global $CFG;
        
        $expdate = '';
        
        if ($expire != 0) {
            $expdate = gmdate("D, d-M-Y H:i:s \\G\\M\\T", time() + $expire);
            $expdate = "; expires={$expdate}";
        }
        
        $data = rawurlencode("{$CFG['cookie_prefix']}{$name}") . '=' . rawurlencode($value);
        $path = !empty($CFG['cookie_path']) ? "; path={$CFG['cookie_path']}" : '';
        $domain = !empty($CFG['cookie_domain']) ? "; domain={$CFG['cookie_domain']}" : '';
        
        header("Set-Cookie: {$data}{$expdate}{$path}{$domain}; HttpOnly", false);
    }
    
    public function log_set_cookie($user, $name, $value, $expire = 0)
    {
        global $CFG;
        
        $expdate = '';
        
        if ($expire != 0) {
            $expdate = gmdate("D, d-M-Y H:i:s \\G\\M\\T", time() + $expire);
            $expdate = "; expires={$expdate}";
        }
        
        $data = rawurlencode("{$CFG['cookie_prefix']}{$name}") . '=' . rawurlencode($value);
        $path = !empty($CFG['cookie_path']) ? "; path={$CFG['cookie_path']}" : '';
        $domain = !empty($CFG['cookie_domain']) ? "; domain={$CFG['cookie_domain']}" : '';
        
        $fh = fopen("cookie_log.txt", "a");
        flock($fh, LOCK_EX);
        fwrite($fh, "[{$user}]\tSet-Cookie: {$data}{$expdate}{$path}{$domain}; HttpOnly [{$_SERVER['REMOTE_ADDR']}]\n");
        flock($fh, LOCK_UN);
        fclose($fh);

        header("Set-Cookie: {$data}{$expdate}{$path}{$domain}; HttpOnly", false);
    }
    
    public function clear_cookie($name)
    {
        $this->set_cookie($name, '', -31536000);
    }
    
    public function authorize()
    {
        global $CFG, $STD, $DB, $IN;
        
        $this->sess_id = $this->get_session_id();

        $session_active = 1;
        $session_pass = 1;
        
        $this->sess_fail .= '.auth';
        
        if (empty($this->sess_id)) {
            $session_active = 0;
            $session_pass = 0;
            $this->sess_fail .= '.1';
        }
        
        $dbsess = array();
        
        if ($session_active) {
            $DB->query("SELECT s.*, u.username, u.cookie, u.last_visit, u.last_activity, u.last_ip
						 FROM {$CFG['db_pfx']}_sessions s 
						   LEFT JOIN {$CFG['db_pfx']}_users u ON (s.uid = u.uid)
						 WHERE s.sessid = '{$this->sess_id}'");
            
            $this->sess_fail .= '.2';
            
            //-------------------------------------------------
            // Does a session by that identifier exist?
        
            if ($DB->get_num_rows() == 0) {
                $session_active = 0;
                $this->sess_fail .= '.3';
            }
        }
        
        if ($session_active) {
            $dbsess = $DB->fetch_row();
            $DB->free_result();
            
            $this->sess_fail .= '.4';
        
            //-------------------------------------------------
            // Did the session time out?  Delete all outdated
            // sessions if this is the case.
            
            $time = time() - 3600;
            if ($dbsess['time'] < $time) {
                $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions WHERE time < '{$time}'");
                $session_active = 0;
                $this->sess_fail .= '.5';
            }
        }
            
        //-------------------------------------------------
        // Check that sessions matches some user checks
        
        if ($session_active) {
            $this->sess_fail .= '.6';
            
            if (empty($this->sess_cookie)) {
                $this->sess_fail .= '.7';
                //-----------------------------------------
                // Check for guest session
                
                if ($dbsess['uid'] == 0) {
                    $session_pass = 0;
                    $this->sess_fail .= '.8';
                }
            } else {
                $this->sess_fail .= '.9';
                
                //-----------------------------------------
                // When the session cookie is present, we
                // check that the stored user hash is correct.
                
                if ($dbsess['cookie'] != $this->sess_cookie) {
                    $session_pass = 0;
                    $this->sess_fail .= '.10';
                }
            }
            
            //---------------------------------------------
            // We verify the first 2 octets of the IP which
            // still gives a little wiggle room for ISP
            // proxies wrecking havoc.
            
            $remote_addr = trim($_SERVER['REMOTE_ADDR']);
            if (!preg_match("/^\d{1,3}\.\d{1,3}.\d{1,3}.\d{1,3}$/", $remote_addr) && $remote_addr != '::1') {
                $session_pass = 0;
                $this->sess_fail .= '.11';
            }
            
            $db_ip = preg_replace("/\.\d{1,3}\.\d{1,3}$/", '', $dbsess['ip']);
            $curr_ip = preg_replace("/\.\d{1,3}\.\d{1,3}$/", '', $_SERVER['REMOTE_ADDR']);
            if ($db_ip != $curr_ip && $remote_addr != '::1') {
                $session_pass = 0;
                $this->sess_fail .= '.12';
            }
            
            //---------------------------------------------
            // We also enforce a user-agent check.
            
            $user_agent = substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 72);
            if ($dbsess['user_agent'] != $user_agent) {
                $session_pass = 0;
                $this->sess_fail .= '.13';
                $this->fault = "{$_SERVER['HTTP_USER_AGENT']} == {$dbsess['user_agent']}";
                $this->fetch = $dbsess;
            }
        } elseif (!empty($this->sess_cookie)) {
            $this->sess_fail .= '.14';
            
            //---------------------------------------------
            // If the session is inactive, we can recreate
            // it if a valid cookie is present.
            
            $DB->query("SELECT u.uid, u.username, u.cookie, u.last_visit, u.last_activity, u.last_ip
						 FROM {$CFG['db_pfx']}_users u
						 WHERE u.cookie = '{$this->sess_cookie}'");
            
            if ($DB->get_num_rows() == 0) {
                $session_pass = 0;
                $this->sess_fail .= '.15';
                $this->sess_fail .= '.[' . $this->get_cookie('sess') . ']';
            } else {
                $dbsess = $DB->fetch_row();
                $DB->free_result();
                
                $this->sess_fail .= '.16';
                
                //---------------------------------------------
                // For restoring sessions, we want the IP to be
                // reasonably close to what it was last time.
                // This makes cookies a little safer.
                
                $remote_addr = trim($_SERVER['REMOTE_ADDR']);
                if (!preg_match("/^\d{1,3}\.\d{1,3}.\d{1,3}.\d{1,3}$/", $remote_addr)) {
                    $session_pass = 0;
                    $this->sess_fail .= '.17';
                }
                
                $db_ip = preg_replace("/\.\d{1,3}\.\d{1,3}$/", '', $dbsess['last_ip']);
                $curr_ip = preg_replace("/\.\d{1,3}\.\d{1,3}$/", '', $_SERVER['REMOTE_ADDR']);
                if ($db_ip != $curr_ip) {
                    $session_pass = 0;
                    $this->sess_fail .= '.18';
                }
            }
        } else {
            $session_pass = 0;
            $this->sess_fail .= '.19';
        }
        
        //-------------------------------------------------
        // IP Ban
        
        $banned = 0;
        $mip = explode('.', $_SERVER['REMOTE_ADDR']);
        $blacklist = explode(',', $CFG['blacklist']);
        while (list(, $val) = each($blacklist)) {
            $bip = explode('.', $val);
            
            for ($x=0; $x<sizeof($bip); $x++) {
                if ($bip[$x] == '*') {
                    $banned = 1;
                }
                if ($mip[$x] != $bip[$x]) {
                    break;
                }
                if ($x == 3) {
                    $banned = 1;
                }
            }
        }
        
        $whitelist = explode(',', $CFG['whitelist']);
        
        if (!empty($STD->user['uid']) && in_array($STD->user['uid'], $whitelist)) {
            $banned = 0;
        }
        
        if ($IN['act'] == 'login') {
            $banned = 0;
        }

        if ($banned) {
            $session_pass = 0;
            $this->sess_fail .= '.20';
        }
        
        //-------------------------------------------------
        // Set or update the appropriate session
        
        if ($session_pass) {
            if ($session_active) {
                $this->update_session($dbsess);
                $this->load_user($dbsess);
                $this->sess_fail .= '.AP';
            } else {
                $this->create_session($dbsess);
                $this->load_user($dbsess);
                $this->sess_fail .= '.IP';
            }
        } else {
            if ($session_active) {
                $this->update_guest_session($dbsess);
                $this->load_guest($dbsess);
                $this->sess_fail .= '.AF';
            } else {
                $this->create_guest_session($dbsess);
                $this->load_guest($dbsess);
                $this->sess_fail .= '.IF';
            }
        }

        $this->sess_active = 1;
        
        return true;
    }
    
    public function create_session($dbsess, $resident=0)
    {
        global $DB, $CFG;
        
        // Adsense & co. does NOT like sessions
        if ($this->is_bot()) {
            $this->sess_id = '';
            return;
        }
        
        $this->sess_id = $this->create_session_id($dbsess['cookie'], $resident, $dbsess['uid']);
        
        $basetime = time();
        $remote_addr = trim($_SERVER['REMOTE_ADDR']);
        $user_agent = substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 72);

        if (!preg_match("/^\d{1,3}\.\d{1,3}.\d{1,3}.\d{1,3}$/", $remote_addr)) {
            $remote_addr = '0.0.0.0';
        }
        
        $time = $basetime - 3600;
        $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions 
					WHERE time < '$time' OR sessid = '{$this->sess_id}' OR (uid > 0 AND uid = {$dbsess['uid']})");
                    
        $location = '';
        if (preg_match('/admin\.php/', $_SERVER['SCRIPT_FILENAME'])) {
            $location = 'admin';
        }
        
        $fields = $DB->format_db_values(array('uid'			=> $dbsess['uid'],
                                                'time'			=> $basetime,
                                                'sessid'		=> $this->sess_id,
                                                'ip'			=> $remote_addr,
                                                'user_agent'	=> $user_agent,
                                                'location'		=> $location,
                                                'sessdata'		=> ''));
        $DB->query("INSERT INTO {$CFG['db_pfx']}_sessions ({$fields['FIELDS']}) 
					 VALUES ({$fields['VALUES']})");

        $update = $DB->format_db_update_values(array('last_ip'		=> $remote_addr,
                                                       'last_visit'		=> $dbsess['last_activity'],
                                                       'last_activity'	=> $basetime));
        $where = $DB->format_db_where_string(array('uid'	=> $dbsess['uid']));
        $DB->query("UPDATE {$CFG['db_pfx']}_users SET {$update} WHERE {$where}");
        
        $this->data = array();
    }
    
    public function create_guest_session($dbsess=array())
    {
        $dbsess['uid'] = 0;
        $dbsess['cookie'] = '00000000000000000000000000000000';
        $dbsess['last_activity'] = 0;
        $dbsess['last_ip'] = '0.0.0.0';

        $this->create_session($dbsess, 1, 0);
    }
    
    public function update_session($dbsess)
    {
        global $DB, $CFG;
        
        // Adsense & co. does NOT like sessions
        if ($this->is_bot()) {
            $this->sess_id = '';
            return;
        }
        
        $basetime = time();
        $remote_addr = trim($_SERVER['REMOTE_ADDR']);
        $user_agent = substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 72);
        
        if (!preg_match("/^\d{1,3}\.\d{1,3}.\d{1,3}.\d{1,3}$/", $remote_addr)) {
            $remote_addr = '0.0.0.0';
        }
        
        $location = '';
        if (preg_match('/admin\.php/', $_SERVER['SCRIPT_FILENAME'])) {
            $location = 'admin';
        }
        
        $fields = $DB->format_db_update_values(array('time'		=> $basetime,
                                                       'ip'			=> $remote_addr,
                                                       'user_agent'	=> $user_agent,
                                                       'location'	=> $location));
        $DB->query("UPDATE {$CFG['db_pfx']}_sessions SET {$fields} 
					 WHERE sessid = '{$this->sess_id}'");
        
        $update = $DB->format_db_update_values(array('last_ip'		=> $remote_addr,
                                                       'last_activity'	=> $basetime));
        $where = $DB->format_db_where_string(array('uid'	=> $dbsess['uid']));
        $DB->query("UPDATE {$CFG['db_pfx']}_users SET {$update} WHERE {$where}");
        
        $this->data = unserialize($dbsess['sessdata']);
    }
    
    public function update_guest_session($dbsess)
    {
        $dbsess['uid'] = 0;
        $dbsess['sessdata'] = serialize(array());
        
        $this->update_session($dbsess);
    }
    
    public function clear_session()
    {
        global $DB, $CFG;
        
        $where_clause = "sessid = '{$this->sess_id}'";
        if (!empty($this->user['uid'])) {
            $where_clause .= " OR uid = '{$this->user['uid']}'";
        }
        
        $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions WHERE {$where_clause}");
        
        $this->sess_id = '';
        $this->sess_cookie = '';
        $this->sess_active = 0;
        $this->user = array();
        $this->data = array();
        
        $this->clear_cookie('sess');
        
        /*$time = time() - 3600*24;
        if (version_compare ($this->phpver, '5.2.0') >= 0) {
            return setcookie ('sess', false, $time, '/', '', false, true);
        } else {
            return setcookie ('sess', false, $time, '/', '', false);
        }*/
    }
    
    /*function remember_session () {
        global $STD;

        $_SESSION['cookie'] = $STD->user['cookie'];
        $cookie = "{$STD->user['uid']},{$STD->user['cookie']}";

        if (!$STD->set_cookie('session', $cookie))
            $STD->error("Could not store session in cookie.  Check your browser's cookie settings, or uncheck the 'Remember Me' box when logging in if applicable.  You will still be logged in until you close your browser window.");
    }*/
    
    public function load_user($dbsess)
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->sess_id)) {
            return $this->load_guest();
        }
        
        $USER = new user;
        $USER->query_use('group');
        $USER->get($dbsess['uid']);

        $STD->userobj = $USER;
        $STD->user =& $STD->userobj->data;
        
        $this->user =& $STD->userobj->data;
        
        $USER->compile_digest();
        
        return $USER;
    }
    
    public function load_guest($dbsess=array())
    {
        global $STD, $CFG;
        
        $STD->userobj = new user;
        $STD->user =& $STD->userobj->data;
        
        $this->user =& $STD->userobj->data;
        
        $group = new group;
        $group->get($CFG['guest_access']);
        
        $STD->userobj->create();
        $STD->userobj->data = array_merge($STD->userobj->data, $group->data);
        
        $STD->user['uid'] = 0;
        $STD->user['username'] = 'Guest';
        $STD->user['show_thumbs'] = 1;
        $STD->user['timezone'] = null;
        $STD->user['dst'] = null;

        return $STD->userobj;
    }
    
    public function check_login($username, $password, $remember=0)
    {
        global $DB, $CFG, $STD;
        
        $this->clear_session();
        
        $password = md5($password);
        $username = $DB->clean_value($username);
        
        $DB->query("SELECT u.* FROM {$CFG['db_pfx']}_users u 
					 WHERE u.username = '{$username}' AND u.password = '{$password}'");
            
        if ($DB->get_num_rows() == 0) {
            $this->create_guest_session();
            $this->load_guest();
            return false;
        }
            
        $dbrow = $DB->fetch_row();
        $DB->free_result();

        $this->create_session($dbrow, $remember);
        $this->load_user($dbrow);
        
        if ($remember) {
            //$this->remember_session();
        }
        
        return true;
    }
    
    public function check_logout()
    {
        $ret = $this->clear_session();
        
        $this->create_guest_session();
        $this->load_guest();
        
        return $ret;
    }
    
    public function logged_in()
    {
        return (!empty($this->user) && !empty($this->user['uid']));
    }
    
    public function save_data()
    {
        global $CFG, $DB;
        
        if (empty($this->sess_id)) {
            return false;
        }
        
        $sessdata = serialize($this->data);
        $sessdata = $DB->clean_value($sessdata);
        
        $DB->query("UPDATE {$CFG['db_pfx']}_sessions SET sessdata = '{$sessdata}'
					 WHERE sessid = '{$this->sess_id}'");
        
        return true;
    }
    
    public function touch_data()
    {
        $params = func_get_args();
        
        foreach ($params as $param) {
            if (!isset($this->data[$param])) {
                $this->data[$param] = '';
            }
        }
    }
    
    public function protect_request()
    {
        $this->data['request_token'] = md5(uniqid(rand(), true));
        
        return $this->data['request_token'];
    }
    
    public function check_request($token='')
    {
        if (empty($token) && !empty($_POST['request_token'])) {
            $token = $_POST['request_token'];
        }
        
        return ($this->data['request_token'] == $token);
    }
    
    public function rewrite_url($url)
    {
        $url = preg_replace("/&(amp;)?/", '&amp;', $url);
        
        if ($this->using_cookies) {
            return $url;
        }
        
        // Adsense & co. does NOT like sessions
        if ($this->is_bot()) {
            return $url;
        }
        
        $qpos = strpos($url, '?');
        if (!$qpos) {
            return $url . "?sess={$this->sess_id}";
        }

        return substr_replace($url, "sess={$this->sess_id}&amp;", $qpos+1, 0);
    }

    /*/ ---

    function authorize () {
        global $CFG, $DB, $STD, $TPL, $IN;

        if (empty($_COOKIE['PHPSESSID']) && !empty($IN['sess']))
            session_id($IN['sess']);

        session_start();

        //------------------------------------------------------------------
        // Time out expired sessions

        $time = time() - 3600;
        if (isset($_SESSION['logged']) && $_SESSION['logged'] < $time) {
            $_SESSION['logged'] = 0;
        }

        if (isset($_SESSION['guest_logged']) && $_SESSION['guest_logged'] < $time) {
            $_SESSION['guest_logged'] = 0;
        }

        //$cookie = $STD->get_cookie('session');
        //trigger_error("<pre>".print_r($cookie,1)." (This is not an error - this is part of a temporary debugging session.  Please ignore it.)</pre>", E_USER_WARNING);

        $session_pass = 0;

        //------------------------------------------------------------------
        // Does a session allready exist?

        if (!empty($_SESSION['logged']) || !empty($_SESSION['guest_logged'])) {

            $USER = new user;
            $USER->query_use('group');
            $USER->query_use('session');
            $USER->query_condition("s.cookie = '{$_SESSION['cookie']}' ".
                                   "AND s.ip = '{$_SERVER['REMOTE_ADDR']}' ".
                                   "AND s.sessid = '".session_id()."'".
                                   "AND s.user_agent = '".substr($_SERVER['HTTP_USER_AGENT'], 0, 72)."'");

            if ($USER->get($_SESSION['uid'])) {
                $this->load_user($USER);
                $this->update_session();

                $session_pass = 1;
            } elseif (!empty($_SESSION['guest_logged'])) {
                $this->load_guest();
                $this->update_guest_session();

                $session_pass = 1;
            }
            //} else {
                // Was the session abruptly interrupted?
                //$this->load_guest();
                //$this->update_guest_session();
            //}
        }

        //------------------------------------------------------------------
        // Nope, but perhaps inside a cookie?

        if (!$session_pass && ($cookie = $STD->get_cookie('session')) && !empty($cookie)) {
            if (strpos($cookie, ',')) {

                list($id,$hashcode) = explode(',', $cookie);

                if (isset($id) and isset($hashcode)) {
                    $USER = new user;
                    $USER->query_use('group');
                    $USER->query_condition("cookie = '$hashcode'");

                    if ($USER->get($id)) {
                        $this->load_user($USER);
                        $this->load_session();
                        $USER->compile_digest();

                        $session_pass = 1;
                    }
                }
                //} else {
                    //$this->load_guest();
                //}
            }
        }

        //------------------------------------------------------------------
        // Nope, this person is definitely not logged in.  So let's give them a default session.

        if (!$session_pass) {
            $this->load_guest();
        }

        //------------------------------------------------------------------
        // Is this user IP banned from the site?

        $banned = 0;
        $mip = explode('.', $_SERVER['REMOTE_ADDR']);
        $blacklist = explode(',', $CFG['blacklist']);
        while (list(,$val) = each($blacklist)) {
            $bip = explode('.', $val);

            for ($x=0; $x<sizeof($bip); $x++) {
                if ($bip[$x] == '*')
                    $banned = 1;
                if ($mip[$x] != $bip[$x])
                    break;
                if ($x == 3)
                    $banned = 1;
            }
        }

        $whitelist = explode(',', $CFG['whitelist']);

        if (!empty($STD->user['uid']) && in_array($STD->user['uid'], $whitelist))
            $banned = 0;

        if ($IN['act'] == 'login')
            $banned = 0;

        if ($banned) {
            $this->clear_session();
            //$STD->error("Your access to {$CFG['site_name']} has been revoked.");
            return false;
        }

        return true;
    }

    function check_login ($username, $password, $remember) {
        global $CFG, $DB, $STD;

        $this->clear_session();

        $USER = new user;
        $USER->query_use('group');

        if ($USER->getByLogin($username, md5($password))) {
            $this->load_user($USER);
            $this->load_session();
            $USER->compile_digest();

            if ($remember)
                $this->remember_session();

            return true;
        }
        $this->load_guest();

        return false;
    }

    function remember_session () {
        global $STD;

        $_SESSION['cookie'] = $STD->user['cookie'];
        $cookie = "{$STD->user['uid']},{$STD->user['cookie']}";

        if (!$STD->set_cookie('session', $cookie))
            $STD->error("Could not store session in cookie.  Check your browser's cookie settings, or uncheck the 'Remember Me' box when logging in if applicable.  You will still be logged in until you close your browser window.");
    }

    function load_user ($user) {
        global $CFG, $DB, $STD;

        $STD->userobj = $user;
        $STD->user =& $STD->userobj->data;

        unset($STD->user['ip']);
        unset($STD->user['sid']);
        unset($STD->user['sessid']);
        unset($STD->user['time']);
    }

    function load_guest () {
        global $STD, $CFG;

        $STD->userobj = new user;
        $STD->user =& $STD->userobj->data;

        $group = new group;
        $group->get($CFG['guest_access']);

        if (!isset($_SESSION['guest_logged']) || !$_SESSION['guest_logged'])
            $this->load_guest_session();
            //$this->clear_session();

        $STD->userobj->create();
        $STD->userobj->data = array_merge($STD->userobj->data, $group->data);

        $STD->user['uid'] = 0;
        $STD->user['username'] = 'Guest';
        $STD->user['show_thumbs'] = 1;

        //$_SESSION['guest_logged'] = time();
    }

    function load_session () {
        global $STD, $CFG, $DB;

        $_SESSION['username']	= htmlspecialchars($STD->user['username']);
        $_SESSION['cookie']		= $STD->user['cookie'];
        $_SESSION['uid']		= $STD->user['uid'];
        $_SESSION['last_dl']	= 0;
        $_SESSION['consec_dl']	= 0;

        $this->save_read_resources();

        $basetime = time();

        if (empty($_SESSION['logged'])) {
            $time = $basetime - 3600;
            $sessid = session_id();
            $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions WHERE time < '$time' OR sessid = '$sessid'");

            $fields = $DB->format_db_values(array('uid'		=> $STD->user['uid'],
                                                  'time'	=> $basetime,
                                                  'sessid'	=> $sessid,
                                                  'cookie'	=> $STD->user['cookie'],
                                                  'ip'		=> $_SERVER['REMOTE_ADDR'],
                                                  'user_agent'	=> substr($_SERVER['HTTP_USER_AGENT'], 0, 72)));
            $DB->query("INSERT INTO {$CFG['db_pfx']}_sessions ({$fields['FIELDS']}) VALUES ({$fields['VALUES']})");

            $fields = $DB->format_db_update_values(array('last_visit'		=> $STD->user['last_activity'],
                                                         'last_activity'	=> $basetime));
            $where = $DB->format_db_where_string(array('uid'	=> $STD->user['uid']));
            $DB->query("UPDATE {$CFG['db_pfx']}_users SET {$fields} WHERE {$where}");

            $STD->user['last_visit'] = $STD->user['last_activity'];
            $STD->user['last_activity'] = $basetime;
        }

        $_SESSION['logged']				= $basetime;
        $_SESSION['guest_logged']		= 0;
    }

    function load_guest_session () {
        global $STD, $CFG, $DB;

        $_SESSION['username']	= 'Guest';
        $_SESSION['cookie']		= '';
        $_SESSION['uid']		= 0;
        $_SESSION['last_dl']	= 0;
        $_SESSION['consec_dl']	= 0;

        $this->save_read_resources();

        if (!isset($_SESSION['guest_logged']) || !$_SESSION['guest_logged']) {
            $time = time() - 3600;
            $sessid = session_id();
            $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions WHERE time < '$time' OR sessid = '$sessid'");

            $fields = $DB->format_db_values(array('uid'		=> 0,
                                                  'time'	=> time(),
                                                  'sessid'	=> $sessid,
                                                  'cookie'	=> '',
                                                  'ip'		=> $_SERVER['REMOTE_ADDR'],
                                                  'user_agent'	=> substr($_SERVER['HTTP_USER_AGENT'], 0, 72)));
            $DB->query("INSERT INTO {$CFG['db_pfx']}_sessions ({$fields['FIELDS']}) VALUES ({$fields['VALUES']})");
        }

        $_SESSION['logged']				= 0;
        $_SESSION['guest_logged']		= time();
    }

    function update_session () {
        global $STD, $CFG, $DB;

        $_SESSION['username']	= htmlspecialchars($STD->user['username']);
        $_SESSION['cookie']		= $STD->user['cookie'];
        $_SESSION['uid']		= $STD->user['uid'];

        $this->restore_read_resources();

        $basetime = time();

        if (isset($_SESSION['logged']) && $_SESSION['logged']) {
            $where = $DB->format_db_where_string(array('uid'	=> $STD->user['uid'],
                                                       'sessid'	=> session_id()));
            $update = $DB->format_db_update_values(array('time'		=> $basetime));
            $DB->query("UPDATE {$CFG['db_pfx']}_sessions SET $update WHERE $where");

            $where = $DB->format_db_where_string(array('uid'	=> $STD->user['uid']));
            $update = $DB->format_db_update_values(array('last_activity'	=> $basetime));
            $DB->query("UPDATE {$CFG['db_pfx']}_users SET $update WHERE $where");

            $STD->user['last_activity'] = $basetime;
        }

        $_SESSION['logged']				= $basetime;
        $_SESSION['guest_logged']		= 0;
    }

    function update_guest_session () {
        global $STD, $CFG, $DB;

        $_SESSION['username']	= 'Guest';
        $_SESSION['cookie']		= '';
        $_SESSION['uid']		= 0;

        $this->restore_read_resources();

        if (isset($_SESSION['guest_logged']) && $_SESSION['guest_logged']) {
            $where = $DB->format_db_where_string(array('uid'	=>	0,
                                                       'sessid'	=>	session_id()));
            $update = $DB->format_db_update_values(array('time'		=> time()));
            $DB->query("UPDATE {$CFG['db_pfx']}_sessions SET $update WHERE $where");
        }

        $_SESSION['logged']				= 0;
        $_SESSION['guest_logged']		= time();
    }

    function clear_session () {
        global $CFG, $STD, $DB;

        //if (isset($STD->user['uid']) && $STD->user['uid'] > 0) {
        if (!empty($_SESSION['logged']) || !empty($_SESSION['guest_logged'])) {
            //$where	= $DB->format_db_where_string(array('uid'	=> $STD->user['uid']));
            $where = $DB->format_db_where_string(array('sessid'	=> session_id()));
            $DB->query("DELETE FROM {$CFG['db_pfx']}_sessions WHERE $where");
        }

        if (!$STD->set_cookie('session', '', -1))
            $STD->error("Could not clear session cookie.  You may still be logged in.");

        $_SESSION['username']	= '';
        $_SESSION['cookie']		= '';
        $_SESSION['uid']		= 0;
        $_SESSION['remember']	= 0;
        $_SESSION['logged']		= 0;
        $_SESSION['guest_logged'] = 0;
        $_SESSION['data'] 		= array();
    }

    function save_data ($name, $value) {

        if (!isset($_SESSION['data']))
            $_SESSION['data'] = array();

        $_SESSION['data'][$name] = $value;
    }

    function restore_data ($name) {

        if (!isset($_SESSION['data']))
            return false;

        if (!isset($_SESSION['data'][$name]))
            return false;

        return $_SESSION['data'][$name];
    }

    function add_read_resource ($rid) {

        $this->read_resources[$rid] = time();
    }

    function get_read_resource ($rid) {

        if (empty($this->read_resources[$rid]))
            return 0;

        return $this->read_resources[$rid];
    }

    function save_read_resources () {
        global $STD;

        $pairs = array();
        reset($this->read_resources);
        while (list($k,$v) = each($this->read_resources)) {
            $pairs[] = "$k,$v";
        }

        $raw = @join(";", $pairs);

        //$_SESSION['read_resources'] = $raw;
        return $STD->set_cookie("read_resources", $raw);
    }

    function restore_read_resources () {
        global $STD;

        if (!$raw = $STD->get_cookie("read_resources")) {
        //if (empty($_SESSION['read_resources'])) {
            $this->read_resources = array();
            return false;
        }

        //$pairs = explode(";", $_SESSION['read_resources']);
        $pairs = explode(";", $raw);

        reset($pairs);
        while (list(,$v) = each($pairs)) {
            $parts = explode(",", $v);
            $this->read_resources[$parts[0]] = $parts[1];
        }

        return true;
    }*/
}
