<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/modq.php --
// Moderation Queue portion of ACP
//------------------------------------------------------------------

// Message Codes
// 1: Reported Submission
// 2: Reported Comment
// 3: Reported Personal Message
// 4: General Message
// 5: Removal Request

$component = new component_adm_manage;

class component_adm_manage
{
    public $html		= "";
    public $output		= "";
    
    public $cp_header	= '';
    
    public function init()
    {
        global $STD, $TPL, $IN, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('adm_manage');
        
        if (!$STD->user['acp_msg']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        switch ($IN['param']) {
            case 1:	$this->show_message_ctr(); break;
            case 2:	$this->show_message(); break;
            case 3:	$this->close_message(); break;
            case 4:	$this->remove_comment(); break;
            case 5:	$this->show_site_on_off(); break;
            case 6:	$this->do_site_on_off(); break;
            case 7:   $this->do_comment_sync(); break;
            case 8:   $this->do_avgscore_recalc(); break;
        }
        
        //	$cp_content = $TPL->build();
        //
        //	require_once ROOT_PATH.'component/admin/adm_main.php';
        //
        //	$TPL->setTemplate('main_acp');
        //	$TPL->addTag('cp_header', $this->cp_header);
        //	$TPL->addTag('cp_content', $cp_content);
        //
        //	component_adm_main::menus();
        //
        //	if (!$STD->user['acp_users'])
        //		$TPL->addTag('ucp_style', "style='display:none'");
        //	else
        //		$TPL->addTag('ucp_style', "");
        
        //	$time = time() - 60*20;
        //	$DB->query("SELECT username FROM {$CFG['tables']['user']} WHERE last_loc LIKE 'ACP,%' AND last_time > $time ORDER BY last_time DESC");
        //
        //	$names = '';
        //	while ($name = $DB->fetch_row()) {
        //		$names .= "{$name['username']}, ";
        //	}
        //	$names = preg_replace('/,[ ]$/', '', $names);
        //
        //	$TPL->addTag('active_users', $names);
        //	$TPL->addTag('site_url', $CFG['root_url']);
        //
        //	$TPL->display();
        $STD->template->display($this->output);
    }
    
    public function show_message_ctr()
    {
        global $CFG, $DB, $IN, $STD;
        
        require_once ROOT_PATH.'lib/message.php';
        
        if (empty($IN['tab'])) {
            $IN['tab'] = 0;
        }
            
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
            
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        
        // Tabbing

        $tab_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=01&o={$IN['o']}&st={$IN['st']}");
        $tab_index = array_fill(0, 2, 'tabinactive');
        
        $tab_index[$IN['tab']] = 'tabactive';
        
        // Order shenanigans
        $order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=01&tab={$IN['tab']}&st={$IN['st']}");
        
        $order_list = array('m' => 'm.title', 'u' => 'mu.username', 'd' => 'm.date');
        $order_default = array('d', 'd');
        
        $order = $STD->order_translate($order_list, $order_default);
        $order_links = $STD->order_links($order_list, $order_url, $order_default);
        
        // Some output
        $this->output .= $STD->global_template->page_header('Message Center');
        
        $this->output .= $this->html->msg_list_header($tab_index, $tab_url, $order_links);
        
        $MSG = new acp_message;
        $MSG->query_use('m_user');
        $MSG->query_order($order[0], $order[1]);
        $MSG->query_limit($IN['st'], 20);

        if ($IN['tab'] == 0) {
            $MSG->query_condition('m.handled_by = 0');
        } else {
            $MSG->query_condition('m.handled_by > 0');
        }
            
        $MSG->getAll();

        while ($MSG->nextItem()) {
            $data = $MSG->data;
            $turl = $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=02&t={$data['type']}&id={$data['mid']}");
            $data['title'] = "<a href=\"$turl\">{$data['title']}</a>";
            $data['sender'] = $STD->format_username($data, 'mu_');
            $data['code'] = $this->format_msg_code($data['type']);
            $data['date'] = $STD->make_date_short($data['date']);
            
            $this->output .= $this->html->msg_list_row($data);
        }
        
        $DB->free_result();
        
        $count = $MSG->countAll();
        $pages = $STD->paginate($IN['st'], $count['cnt'], 20, "act=manage&param=01&tab={$IN['tab']}&o={$IN['o']}");

        $this->output .= $this->html->msg_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_message()
    {
        global $CFG, $DB, $IN, $STD;
        
        require_once ROOT_PATH.'lib/message.php';

        // Aquire Message
        $MSG = new acp_message;
        $MSG->query_use('m_user');
        $MSG->query_use('h_user');
        
        switch ($IN['t']) {
            case 1: $MSG->query_use('aux_resource'); break;
            case 2: $MSG->query_use('aux_resource');
                    $MSG->query_use('aux_comment'); break;
            case 3: $MSG->query_use('aux_message'); break;
            case 5: $MSG->query_use('aux_resource'); break;
        }
        
        if (!$MSG->get($IN['id'])) {
            $STD->error("The requested message does not exist");
        }
        
        // Actions
        if ($IN['t'] == 1 && empty($MSG->data['r_rid'])) {
            $actions = "+ Associated Submission Removed<br />";
        } elseif ($IN['t'] == 1) {
            $actions = "+ <a href='".$STD->encode_url(
                $_SERVER['PHP_SELF'],
                "act=modq&param=02&c={$MSG->data['r_type']}&rid={$MSG->data['r_rid']}"
            ).
                       "'>Go to associated submission in Mod Queue</a><br />";
        } elseif ($IN['t'] == 2 && empty($MSG->data['c_cid'])) {
            $actions = "+ Reported Comment Removed<br />";
        } elseif ($IN['t'] == 2) {
            $actions = "+ <a href='".$STD->encode_url(
                $_SERVER['PHP_SELF'],
                "act=manage&param=04&id={$IN['id']}"
            ).
                       "'>Delete Comment</a><br />";
        } elseif ($IN['t'] == 5 && empty($MSG->data['r_rid'])) {
            $actions = "+ Associated Submission Removed<br />";
        } elseif ($IN['t'] == 5) {
            $actions = "+ <a href='".$STD->encode_url(
                $_SERVER['PHP_SELF'],
                "act=modq&param=02&c={$MSG->data['r_type']}&rid={$MSG->data['r_rid']}"
            ).
                       "'>Go to associated submission in Mod Queue</a><br />";
        } else {
            $actions = "";
        }
        
        // Closure
        if ($MSG->data['handled_by'] == 0) {
            $show = 'display:none';
            $status = '<b>Open</b>';
            $closed_by = '';
            $inform = '';
            $actions .= "+ <a href=\"javascript:show_hide('close_frm1');show_hide('close_frm2');
		 				 show_hide('close_frm3');show_hide('close_frm4');show_hide('close_frm5')\">Close Message</a><br />";
        } else {
            $show = '';
            $status = 'Closed';
            $closed_by = $MSG->data['hu_username'];
            ($MSG->data['user_inform'])
                ? $inform = 'The user was informed of this resolution'
                : $inform = 'The user was NOT informed of this resolution';
        }
        
        if (empty($actions)) {
            $actions = "+ No Actions";
        }
        
        $this->output = $STD->global_template->page_header('View Message: ' . $this->get_type($MSG->data['type']));
    
        $close_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=03&id={$MSG->data['mid']}");
        
        $data = $MSG->data;
        $data['sender'] = $STD->format_username($data, 'mu_');
        $data['report_date'] = $STD->make_date_time($data['date']);
        $data['close_date'] = $STD->make_date_time($data['handle_date']);
        $data['type'] = $this->get_type($data['type']);
        $data['status'] = $status;
        $data['closed_by'] = $closed_by;
        $data['show_close'] = $show;
        $data['actions'] = $actions;
        $data['inform'] = $inform;
        
        $this->output .= $this->html->msg_page($data, $close_url);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function close_message()
    {
        global $IN, $STD, $DB, $CFG;
        
        require_once ROOT_PATH.'lib/message.php';

        $MSG = new acp_message;
        if (!$MSG->get($IN['id'])) {
            $STD->error("The requested message does not exist.");
        }
        
        // Do we need to inform the user?
        if ($IN['inform'] == 'yes') {
            $PM = new message;
            $PM->create();
            $PM->data['sender'] = 0;
            $PM->data['receiver'] = $MSG->data['sender'];
            $PM->data['owner'] = $MSG->data['sender'];
            $PM->data['folder'] = 0;
            $PM->data['date'] = time();
            $PM->data['title'] = "Re: " . preg_replace("/^Re:\s/", "", $MSG->data['title']);
            $PM->data['message'] = $IN['resolution'];
            $PM->data['conversation'] = $MSG->data['conversation'];

            $PM->dispatch();
            
            if ($PM->data['conversation'] == 0) {
                $PM->data['conversation'] = $PM->data['mid'];
                $PM->update();
                
                $MSG->data['conversation'] = $PM->data['conversation'];
            }
            
            $MSG->data['user_inform'] = 1;
        }
        
        $MSG->data['handled_by'] = $STD->user['uid'];
        $MSG->data['handle_date'] = time();
        $MSG->data['admin_comment'] = $IN['resolution'];
        $MSG->update();

        $loc = str_replace('&amp;', '&', $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=02&t={$MSG->data['type']}&id={$IN['id']}"));
        header("Location: $loc");
        exit;
    }
    
    public function remove_comment()
    {
        global $IN, $STD, $DB, $CFG;
        
        require_once ROOT_PATH.'lib/message.php';
        
        $MSG = new acp_message;
        if (!$MSG->get($IN['id'])) {
            $STD->error("The requested message does not exist.");
        }
        
        if ($MSG->data['type'] != 2) {
            $STD->error("There is no comment associated with this message.");
        }
            
        $COM = new comment;
        if (!$COM->get($MSG->data['aux'])) {
            $STD->error("The requested comment does not exist.");
        }
        
        $COM->remove();
        
        $loc = str_replace('&amp;', '&', $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=02&t={$MSG->data['type']}&id={$IN['id']}"));
        header("Location: $loc");
        exit;
    }
    
    public function show_site_on_off()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (!$STD->user['acp_super']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
            
        $this->output = $STD->global_template->page_header('Site On/Off');
        
        $form_elements = array();
        $form_elements['offline'] = $STD->make_yes_no('offline', $CFG['site_offline']);
        
        $data = array();
        $data['message'] = str_replace("<br />", "\n", $CFG['offline_msg']);
        
        $this->output .= $this->html->site_on_off($data, $form_elements, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_comment_sync()
    {
        global $IN, $STD, $DB, $CFG;
        
        $this->output = $STD->global_template->page_header('Comment Sync');
        
        $syncmsg = "Starting synchronization...<br /><br />";
        
        $cq = $DB->query("SELECT rid FROM {$CFG['db_pfx']}_resources");
        while ($row = $DB->fetch_row($cq)) {
            $cc = $DB->query("SELECT COUNT(*) AS cnt, MAX(date) as date FROM {$CFG['db_pfx']}_comments WHERE type = 1 AND rid = {$row['rid']}");
            $crow = $DB->fetch_row($cc);
            
            if (empty($crow['date'])) {
                $crow['date'] = 0;
            }
    
            $syncmsg .= "{$row['rid']}: {$crow['cnt']} @ {$crow['date']}<br />";
    
            $DB->query("UPDATE {$CFG['db_pfx']}_resources SET comments = {$crow['cnt']}, comment_date = {$crow['date']} WHERE rid = {$row['rid']}");
        }

        $syncmsg .= "<br />Synchronization Complete.";
        $this->output .= $syncmsg;
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_avgscore_recalc()
    {
        global $IN, $STD, $DB, $CFG;
        
        $this->output = $STD->global_template->page_header('Average Score Recalculation');
        
        $syncmsg = "Starting recalculation...<br /><br />";
        
        $cq = $DB->query("SELECT rid, eid FROM {$CFG['db_pfx']}_resources WHERE type = 2");
        while ($row = $DB->fetch_row($cq)) {
            $cc = $DB->query("SELECT COUNT(*) AS cnt, SUM(score) as totalscore FROM {$CFG['db_pfx']}_res_reviews WHERE gid = {$row['rid']}");
            $crow = $DB->fetch_row($cc);
            
            if (empty($crow['totalscore'])) {
                $crow['totalscore'] = 0;
            }
    
            $syncmsg .= "{$row['rid']}: {$crow['totalscore']} / {$crow['cnt']}<br />";
    
            $DB->query("UPDATE {$CFG['db_pfx']}_res_games SET num_revs = {$crow['cnt']}, rev_score = {$crow['totalscore']} WHERE eid = {$row['eid']}");
        }

        $syncmsg .= "<br />Recalculation Complete.";
        $this->output .= $syncmsg;
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_site_on_off()
    {
        global $IN, $STD, $DB, $CFG;
        
        // Permissions
        if (!$STD->user['acp_super']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The drop request did not originate from this site, or your request has allready been processed.");
        }
        
        $CFG['site_offline'] = $IN['offline'];
        $msg = $STD->rawclean_value($_POST['message']);
        $msg = preg_replace("/\n/", "<br />", $msg);
        $CFG['offline_msg'] = preg_replace("/\r/", "", $msg);
        
        // Write settings
        require_once ROOT_PATH.'component/admin/adm_conf.php';
        $component->rewrite_settings($CFG);
        
        // Done
        $url_main = $STD->encode_url($_SERVER['PHP_SELF']);
        $url_back = $STD->encode_url($_SERVER['PHP_SELF'], "act=manage&param=05");
        $message = "The site settings were updated successfully.
					<p align='center'><a href='$url_back'>Return to Site On/Off</a><br />
					<a href='$url_main'>Return to the main page</a></p>";
                    
        $this->output = $STD->global_template->page_header('Update Successful');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function format_msg_code($type)
    {
        global $STD;
        
        $code = 'unknown.gif';
        $alt = 'Unknown Message Type';
        switch ($type) {
            case 1: $code = 'q_report.gif'; $alt = 'Reported Submission'; break;
            case 2: $code = 'q_report.gif'; $alt = 'Reported Comment'; break;
            case 3: $code = 'q_report.gif'; $alt = 'Reported Personal Message'; break;
            case 4: $code = 'q_normal.gif'; $alt = 'General Message'; break;
            case 5: $code = 'q_remove.gif'; $alt = 'Removal Request'; break;
        }
        
        return "<img src='{$STD->tags['image_path']}/$code' border='0' alt='mc' title='$alt' />";
    }
    
    public function get_type($type)
    {
        switch ($type) {
            case 1: return 'Report';
            case 2: return 'Report';
            case 3: return 'Report';
            case 4: return 'General Message';
            case 5: return 'Removal Request';
        }
        
        return '';
    }
}
