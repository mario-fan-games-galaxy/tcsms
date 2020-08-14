<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/modq.php --
// Moderation Queue portion of ACP
//------------------------------------------------------------------

// Queue Codes
// 0: Accepted submission
// 1: New submission
// 2: Updated submission
// 3: Reported submission
// 4: Re-queued submission
// 5: Ghost submission (real update)

$component = new component_adm_modq;

class component_adm_modq
{
    public $html		= "";
    public $mod_html	= "";
    public $output		= "";
    
    public $cp_header	= '';
    
    public function init()
    {
        global $STD, $IN, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('adm_modq');
        
        if (!$STD->user['acp_modq']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        switch ($IN['param']) {
            case 1:	$this->show_list(); break;
            case 2:	$this->show_edit(); break;
            case 3:	$this->do_edit();	break;
            case 4:	$this->do_action('a');	break;
            case 5:	$this->do_action('d');	break;
            case 6:	$this->do_action('r');	break;
            case 7:	$this->do_action('m');	break;
            case 8:	$this->show_create();	break;
            case 9:	$this->do_create();	break;
            case 10:	$this->do_action('u');	break;
        }

        $STD->template->display($this->output);
    }
    
    public function show_list()
    {
        global $DB, $CFG, $IN, $STD;
        
        if (empty($IN['tab'])) {
            $IN['tab'] = 0;
        }
            
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
        
        $tab_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=01&c={$IN['c']}");
        $tab_index = array_fill(0, 2, 'tabinactive');
        
        $tab_index[$IN['tab']] = 'tabactive';
        
        //------------------------------------------------
        // Sort ordering
        //------------------------------------------------
        
        $order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=01&tab={$IN['tab']}&c={$IN['c']}&st={$IN['st']}");
        
        $order_list = array('t' => 'r.title', 'u' => "CONCAT(r.author_override,IFNULL(ru.username,''))", 'd' => 'r.created');
        $order_default = array('t', 'a');
        
        $order = $STD->order_translate($order_list, $order_default);
        $order_links = $STD->order_links($order_list, $order_url, $order_default);
        
        $module = $STD->modules->get_module($IN['c']);
        
        // Header
        if ($CFG['adm_virus_check'] && $module['ext_files'] > 0) {
            $boxes['vc'] = "<br /><input type='checkbox' name='virus_check' /> I have checked that the included files are virus free";
        } else {
            $boxes['vc'] = "";
        }
            
        if ($STD->user['acp_super']) {
            $boxes['dq'] = "<br /><input type='checkbox' name='dq_override' /> Override Decision Queue";
        } else {
            $boxes['dq'] = "";
        }
        
        $this->output = $STD->global_template->page_header("Mod Queue - {$module['module_name']}");
            
        $this->output .= $this->html->sub_list_header($order_links, $tab_index, $tab_url, $boxes, $IN['c']);
        
        //------------------------------------------------
        // Get unmodded list
        //------------------------------------------------

        $RES = new resource;
        $RES->query_use('extention', $module['mid']);
        $RES->query_use('r_user');
        $RES->query_order($order[0], $order[1]);
        $RES->query_limit($IN['st'], 30);
        
        switch ($IN['tab']) {
            case 0:	$RES->query_condition('queue_code > 0 AND queue_code <> 5'); break;
            case 1: $RES->query_condition('queue_code = 0'); break;
        }
        
        $RES->getByType($IN['c']);
        
        while ($RES->nextItem()) {
            $RES->data['title'] = $STD->safe_display($RES->data['title']);
            $this->output .= $this->format_list_row($RES->data);
        }

        $DB->free_result();
        
        $count = $RES->countByType($IN['c']);
        $pages = $STD->paginate($IN['st'], $count['cnt'], 30, "act=modq&param=01&tab={$IN['tab']}&c={$IN['c']}&o={$IN['o']}");

        //------------------------------------------------
        
        $this->output .= $this->html->sub_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_edit()
    {
        global $IN, $CFG, $DB, $STD;
        
        //------------------------------------------------
        // Init
        //------------------------------------------------
        
        $module_record = $STD->modules->get_module($IN['c']);
        
        if (empty($IN['c']) || ($templ = $module_record['template']) == '') {
            $STD->error("Invalid Module Specified");
        }
        
        $RES = new resource;
        $RES->query_use('extention', $module_record['mid']);
        $RES->query_use('r_user');
        if (!$RES->get($IN['rid'])) {
            $STD->error("Resource with ID \"{$IN['rid']}\" does not exist.");
        }
        
        // Do we need to display a ghost?
        $PREGHOST = null;
        if ($RES->data['ghost'] > 0) {
            $PREGHOST = $STD->_clone($RES);
            if (!$RES->get($RES->data['ghost'])) {
                $STD->error("Ghost ID \"{$RES->data['ghost']}\" does not exist.");
            }
        }

        //------------------------------------------------
        
        //	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules");
        
        $name_arr = array('---');
        $val_arr = array('');
        reset($STD->modules->module_set);
        while (list(, $row) = each($STD->modules->module_set)) {
            //	while ($row = $DB->fetch_row()) {
            $val_arr[] = $row['mid'];
            $name_arr[] = $row['full_name'];
        }
        
        $root_cats = $STD->make_select_box('change_to', $val_arr, $name_arr, '', 'selectbox');
        
        //------------------------------------------------
        // Build Template
        //------------------------------------------------
        
        $module = $STD->modules->new_module($IN['c']);
        //	$module = new $MODULE['class_name'];
        $module->init();
        
        $this->mod_html = $STD->template->useTemplate('acp_' . $module_record['template']);
        
        $data = $module->acp_edit_prep_data($RES->data);
        $data['type_name_list'] = array('value' => $val_arr, 'name' => $name_arr, 'sel' => '');

        $form = array();
        $form['security_token'] = $STD->make_form_token();
        $form['url'] = $STD->encode_url($_SERVER['PHP_SELF'], 'act=modq');
        
        ($PREGHOST)
            ? $form['prerid'] = $PREGHOST->data['rid']
            : $form['prerid'] = $RES->data['rid'];
        ($PREGHOST)
            ? $form['ghost_style'] = ''
            : $form['ghost_style'] = 'display:none';
        
        //------------------------------
        
        $this->output = $STD->global_template->page_header('Modify Resource');
        $this->output .= $this->html->edit_form_header($data, $form);
        
        $this->output .= $this->mod_html->acp_edit_form($data);
        
        $this->output .= $this->html->edit_form_footer($data, $form);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_edit()
    {
        global $IN, $STD, $CFG, $DB;
        
        require_once ROOT_PATH.'lib/message.php';
        
        // Validate
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The request did not originate from this site, or you attempted to repeat a completed transaction.");
        }
        
        $RES = new resource;
        if (!$RES->get($IN['rid'])) {
            $STD->error("Resource with ID \"{$IN['rid']}\" does not exist.");
        }
        
        // Raw Clean (Remember to undo before display!)
        if (isset($IN['title'])) {
            $IN['title'] = $STD->rawclean_value($_POST['title']);
        }
        
        $module = $STD->modules->new_module($IN['c']);
        //	$module = new $MODULE['class_name'];
        $module->init();
        $module->acp_data_check();
        
        $RES = $module->acp_update_data();
        
        $exmsg = '';
        if (empty($IN['type'])) {
            $ecat = $IN['c'];
        } else {
            $ecat = $RES->data['type'];
            $exmsg = "You have changed the parent category for this record.  All type-specific data for this record is now blank.";
        }
        
        // Dispatch Message
        if (empty($IN['omit_comment']) && $RES->data['uid'] != 0) {
            $MSG = new message;
            $MSG->create();
            $MSG->data['receiver'] = $RES->data['uid'];
            $MSG->data['owner'] = $RES->data['uid'];
            $MSG->data['folder'] = 0;
            $MSG->data['title'] = "Submission Modified";
            $MSG->data['message'] = "Your submission: <b>{$RES->data['title']}</b>, was modified by the site staff with the following comment:
							 	     <br /><br />{$IN['admincomment']}";
            $MSG->dispatch();
            
            $MSG->data['conversation'] = $MSG->data['mid'];
            $MSG->update();
        }

        $this->output = $STD->global_template->page_header('Changes Saved');
        
        $url1 = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=02&c={$ecat}&rid={$IN['rid']}");
        $url2 = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=01&c={$ecat}");
        $message = "Changes to this record were saved.  $exmsg
			<p align='center'><a href='$url1'>
			Return to the record update page</a>
			<br /><a href='$url2'>Return to the Mod Queue</a></p>";
            
        $this->output .= $STD->global_template->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_action($acode)
    {
        global $IN, $STD, $DB, $CFG;
        
        require_once ROOT_PATH.'lib/message.php';
        
        if (empty($IN['rid'])) {
            $IN['rid'] = 0;
        }
        
        $module = $STD->modules->get_module($IN['c']);
        
        $res = new resource;
        $res->query_use('extention', $module['mid']);
        
        if (!$res->get($IN['rid'])) {
            $STD->error("Invalid resource id \"{$IN['rid']}\" specified.");
        }
        $data = $res->data;
        
        if (empty($IN['admincomment']) && empty($IN['omit_comment']) && $res->data['uid'] > 0) {
            $STD->error("You must enter an admin comment for this action.");
        }
        
        if ($CFG['adm_virus_check'] == 1 && $module['ext_files'] > 0 && empty($IN['virus_check'])) {
            $STD->error("You must virus check all files before accepting them.");
        }
        
        $MSG = new message;
        $MSG->create();
        
        // If we're adding or declining, we need to keep track of the decision queue, and make sure we have sufficient
        // decisions from different mods, and then decide to add or drop on that decision.  In case of a tie, an
        // additional decision will be required.
        
        if (!empty($IN['dq_override']) && !$STD->user['acp_super']) {
            $STD->error("Only root admins can override the decision queue");
        }
        
        if (empty($IN['dq_override']) && ($acode == 'a' || $acode == 'd')) {
            ($acode == 'a')
                ? $scode = 1 : $scode = 0;
                
            $req = $module['num_decisions'];
            $dq = explode(';', $res->data['decision']);
            $aq = array();
            $ycnt = 0;
            $ncnt = 0;
            
            while (list(, $v) = each($dq)) {
                if (empty($v)) {
                    break;
                }
                    
                $pair = explode(',', $v);
                $aq[$pair[0]] = $pair[1];
                ($pair[1] == 1)
                    ? $ycnt++ : $ncnt++;
            }

            $dq = $res->data['decision'];
            
            if (!isset($aq[$STD->user['uid']])) {
                $dq .= ";{$STD->user['uid']},$scode";
                $aq[$STD->user['uid']] = $scode;
                ($scode == 1)
                    ? $ycnt++ : $ncnt++;
            }

            $dq = preg_replace("/^;/", "", $dq);
            
            // Now we can make a decision
            
            if ($ycnt + $ncnt < $req) {
                $acode = 'du';
            } elseif ($ycnt == $ncnt) {
                $acode = 'du';
            } elseif ($ycnt > $ncnt) {
                $acode = 'a';
            } else {
                $acode = 'd';
            }
        }
        
        // Now let's act on the decision we've made
        $recip_uid = $res->data['uid'];
        
        if ($acode == 'du') {
            //$res->data['queue_code'] = 4;
            $res->data['decision'] = $dq;
            $res->update();
        }
        
        if ($acode == 'a' && $res->data['ghost'] > 0) {
            // Accept Update (Accept Ghost)
            $old_create_date = $res->data['created'];
            $old_accept_date = $res->data['accept_date'];
            $old_update_date = $res->data['updated'];
            
            $res->apply_ghost();
            
            $mod = $STD->modules->new_module($IN['c']);
            $mod->mod_action($res, 'au');
            
            $res->data['created'] = $old_create_date;
            $res->data['accept_date'] = $old_accept_date;
            $res->data['updated'] = $old_update_date;
            
            $res->data['decision'] = '';
            $res->data['queue_code'] = 0;

            if ($res->data['accept_date'] == 0) {
                $res->data['accept_date'] = time();
            }
                
            $res->update();

            if ($res->data['updated'] > $res->data['update_accept_date']) {
                $res->data['update_accept_date'] = time();
                $res->update();
                
                $ins = $DB->format_db_values(array('rid'	=> $res->data['rid'],
                                                   'change'	=> $res->data['update_reason'],
                                                   'date'	=> $res->data['updated']));
                $DB->query("INSERT INTO {$CFG['db_pfx']}_version ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
            }
            
            $MSG->data['title'] = 'Modification Accepted';
            $MSG->data['message'] = "Modifications to your submission: <b>{$data['title']}</b>, were accepted with the following comment:
									 <br /><br />{$IN['admincomment']}";
        } elseif ($acode == 'a') {
            // Accept Submission

            // set accepted dates
            if ($res->data['accept_date'] == 0) {
                $res->data['accept_date'] = time();
            }
            
            $mod = $STD->modules->new_module($IN['c']);
            $mod->mod_action($res, 'a');
            
            $res->data['decision'] = '';
            $res->data['queue_code'] = 0;
            $res->update();
            
            $MSG->data['title'] = 'Submission Accepted';
            $MSG->data['message'] = "Your submission: <b>{$data['title']}</b>, was accepted to the site with the following comment:
							 	     <br /><br />{$IN['admincomment']}";
        }
        
        if ($acode == 'd') {
            // Decline Submission
            
            $res->data['decision'] = '';
            
            ($res->data['queue_code'] == 0)
                ? $code = 'dq' : $code = 'd';
            
            $mod = $STD->modules->new_module($IN['c']);
            $mod->mod_action($res, $code);
            
            if ($res->data['ghost'] > 0) {
                $res->remove_ghost();
            }
            
            $res->remove();
            $MSG->data['title'] = 'Submission Declined';
            $MSG->data['message'] = "Your submission: <b>{$data['title']}</b>, was either declined from the queue, 
									 or removed from the existing database by the site staff with the following comment:
							 	     <br /><br />{$IN['admincomment']}";
        }
        
        if ($acode == 'r') {
            // Re-Queue Submission
            
            $mod = $STD->modules->new_module($IN['c']);
            $mod->mod_action($res, 'r');
        
            $res->data['queue_code'] = 4;
            $res->data['decision'] = '';
            $res->update();
            
            $MSG->data['title'] = 'Submission Re-queued';
            $MSG->data['message'] = "Your submission: <b>{$data['title']}</b>, was put back into the queue by the site staff with the following comment:
							 	     <br /><br />{$IN['admincomment']}";
        }
        
        if ($acode == 'u') {
            // Undo Update (Delete Ghost)
            
            $res->remove_ghost();
            
            $res->data['ghost'] = 0;
            $res->data['queue_code'] = 0;
            $res->data['decision'] = '';
            $res->update();
            
            $mod = $STD->modules->new_module($IN['c']);
            $mod->mod_action($res, 'du');
            
            $MSG->data['title'] = 'Modification Declined';
            $MSG->data['message'] = "Your submission: <b>{$data['title']}</b> was restored to its previous state.  Your modifications were declined with the following comment:
									 <br /><br />{$IN['admincomment']}";
        }
        
        if ($acode == 'm') {
            //		if (empty($IN['change_to']))
    //			$STD->error("You must select a new parent category");
    //
    //		$res->move($IN['change_to']);
    //		$res->data['queued'] = 4;
    //		$res->update();
    //
    //		$c = $res->data['ext_type'];
        }
        
        // Dispatch Message
        if (empty($IN['omit_comment']) && $recip_uid > 0 && !empty($MSG->data['title'])) {
            $MSG->data['receiver'] = $recip_uid;
            $MSG->data['owner'] = $recip_uid;
            $MSG->data['folder'] = 0;
            $MSG->dispatch();
            
            $MSG->data['conversation'] = $MSG->data['mid'];
            $MSG->update();
        }
        
        // Some hackish stuff
        $dt = date("n.j.y", time());
        
        $fh = fopen(ROOT_PATH.'staff_log.txt', 'a');
        fwrite($fh, "{$STD->user['uid']}\t$acode\t$dt\n");
        fclose($fh);
        
        $location = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=01&c={$IN['c']}");
        $location = preg_replace('/&amp;/', '&', $location);
        
        header("Location: $location");
        exit;
    }
    
    public function show_create()
    {
        global $STD, $DB, $CFG;
        
        $tn = array();
        $tv = array();
        
        //	$DB->query("SELECT mid,full_name FROM {$CFG['db_pfx']}_modules");
        //	while ($row = $DB->fetch_row()) {
        reset($STD->modules->module_set);
        while (list(, $row) = each($STD->modules->module_set)) {
            $tn[] = $row['mid'];
            $tv[] = $row['full_name'];
        }
        
        $box = $STD->make_select_box('type', $tn, $tv, '', 'selectbox');
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=modq&param=09');
        
        $this->output = $STD->global_template->page_header('Create New Submission');
        
        $this->output .= $this->html->create($url, $STD->make_form_token(), $box);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_create()
    {
        global $STD, $IN, $DB, $CFG;
        
        // Validate
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The request did not originate from this site, or you attempted to repeat a completed transaction.");
        }
        
        $IN['c'] = $IN['type'];
        
        $module = $STD->modules->get_module($IN['c']);
        if (!$module) {
            $STD->error("Invalid Root Type selected.");
        }
            
        //	$where = $DB->format_db_where_string(array('mid'	=> $IN['type']));
        //	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE $where");
        
        //	$MODULE = $DB->fetch_row();
        //	if (!$MODULE)
        //		$STD->error("Invalid Root Type selected.");
        
        $data = array(	'type'	=> $module['mid'],
                        'title'	=> 'Blank Submission',
                        'author_override'	=> 'N/A',
                        'queue_code'	=> 1);
                        
        $RES = new resource;
        $RES->query_use('extention', $module['mid']);
        $RES->create($data);
        $RES->insert();
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=02&c={$module['mid']}&rid={$RES->data['rid']}");
        $url = str_replace('&amp;', '&', $url);
        header("Location: $url");
        exit;
    }
    
    public function format_list_row(&$row)
    {
        global $IN, $STD;
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=modq&param=02&c={$IN['c']}&rid={$row['rid']}");
        
        $data = array();
        $data['title'] = "<a href='$url'>{$row['title']}</a>";
        $data['author'] = $STD->format_username($row, 'ru_');
        $data['date'] = $STD->make_date_short($row['created']);
        $data['qcode'] = "<a href='$url'>".$this->format_queue_code($row)."</a>";
        
        if ($row['queue_code'] == 0) {
            $actions = "<a href=\"javascript:show_hide(4);show_hide(2);set_id('rid_r',{$row['rid']});\"><img src='{$STD->tags['image_path']}/flag.gif' border='0' alt='Q' title='Re-Queue' /></a>
						<a href=\"javascript:show_hide(1);show_hide(2);set_id('rid_d',{$row['rid']});\"><img src='{$STD->tags['image_path']}/minus.gif' border='0' alt='-' title='Decline' /></a>";
        } else {
            $actions = "<a href=\"javascript:show_hide(3);show_hide(2);set_id('rid_a',{$row['rid']});\"><img src='{$STD->tags['image_path']}/plus.gif' border='0' alt='+' title='Accept' /></a>
						<a href=\"javascript:show_hide(1);show_hide(2);set_id('rid_d',{$row['rid']});\"><img src='{$STD->tags['image_path']}/minus.gif' border='0' alt='-' title='Decline' /></a>";
        }
        
        $data['action'] = $actions;
        
        return $this->html->sub_list_row($data);
    }
    
    public function format_queue_code($row)
    {
        global $STD;
        
        $code = 'unknown.gif';
        $alt = 'Unknown Queue Code';
        switch ($row['queue_code']) {
            case 0: $code = 'q_normal.gif'; $alt = 'Normal'; break;
            case 1: $code = 'q_new.gif'; $alt = 'New'; break;
            case 2: $code = 'q_update.gif'; $alt = 'User Updated'; break;
            case 3: $code = 'q_report.gif'; $alt = 'Reported'; break;
            case 4: $code = 'q_flag.gif'; $alt = 'Re-queued'; break;
        }
        
        // special handling
        if ($row['decision']) {
            $dq = explode(';', $row['decision']);
            $aq = array();
            
            while (list(, $v) = each($dq)) {
                $pair = explode(',', $v);
                $aq[] = $pair[0];
            }
            
            if (in_array($STD->user['uid'], $aq)) {
                $code = 'q_req_nodecision.gif';
            } else {
                $code = 'q_req_decision.gif';
            }
        }
        
        return "<img src='{$STD->tags['image_path']}/$code' border='0' alt='qc' title='$alt' />";
    }
}
