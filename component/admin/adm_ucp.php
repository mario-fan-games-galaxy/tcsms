't<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/ucp.php --
// User Control portion of ACP
//------------------------------------------------------------------

$component = new component_adm_ucp;

class component_adm_ucp
{
    public $html		= "";
    public $output		= "";
    
    public function init()
    {
        global $STD, $IN, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('adm_ucp');
        
        if (!$STD->user['acp_users']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        switch ($IN['param']) {
            case  1: $this->show_userlist(); break;
            case  2: $this->show_user_edit(); break;
            case  3: $this->do_user_edit(); break;
            case  4: $this->do_create_user(); break;
            case  5: $this->do_drop_user(); break;
            case  6: $this->show_ban_settings(); break;
            case  7: $this->show_group_list(); break;
            case  8: $this->show_group_edit(); break;
            case  9: $this->do_group_edit(); break;
            case 10: $this->do_create_group(); break;
            case 11: $this->show_drop_group(); break;
            case 12: $this->do_drop_group(); break;
            case 13: $this->edit_ban_settings(); break;
            case 14: $this->show_search_user(); break;
            case 15: $this->do_search_user(); break;
            case 16: $this->do_drop_comments(); break;
            case 17: $this->do_search_email(); break;
            case 18: $this->show_search_email(); break;
            case 19: $this->do_search_ip(); break;
            case 20: $this->show_search_ip(); break;
        }
        
        /*$cp_content = $TPL->build();

        $TPL->setTemplate('main_acp');
        $TPL->addTag('cp_header', $this->cp_header);
        $TPL->addTag('cp_content', $cp_content);
        if (!$STD->user['acp_users'])
            $TPL->addTag('ucp_style', "style='display:none'");
        else
            $TPL->addTag('ucp_style', "");*/
        
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
        
        //	require_once ROOT_PATH.'component/admin/adm_main.php';
        
        //	component_adm_main::menus();
        
        //	$TPL->display();
        $STD->template->display($this->output);
    }
    
    public function show_userlist()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
        
        if (empty($IN['tab'])) {
            $IN['tab'] = 0;
        }
            
        // Tabbing

        $tab_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=01&o={$IN['o']}&st={$IN['st']}");
        $tab_index = array_fill(0, 28, 'tabinactive');
        
        $tab_index[$IN['tab']] = 'tabactive';
        
        $tab_arr = array('','#','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        
        // Order shenanigans
        $order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=01&tab={$IN['tab']}&st={$IN['st']}");
        
        $order_list = array('u' => 'u.username', 'g' => 'g.group_name, u.username');
        $order_default = array('u', 'a');
        
        $order = $STD->order_translate($order_list, $order_default);
        $order_links = $STD->order_links($order_list, $order_url, $order_default);
        
        // Start Output
        
        $this->output = $STD->global_template->page_header('Modify Users');
        
        $this->output .= $this->html->ucp_list_header($tab_index, $tab_url, $order_links);
            
        // Get users
        $USER = new user;
        $USER->query_use('group');
        $USER->query_order($order[0], $order[1]);
        $USER->query_limit($IN['st'], 30);
        
        if (empty($IN['tab'])) {
            $USER->clear_condition();
        } elseif ($IN['tab'] == 1) {
            $USER->query_condition("username REGEXP '^[^A-Za-z]'");
        } else {
            $USER->query_condition("username LIKE '{$tab_arr[$IN['tab']]}%'");
        }

        $USER->getAll();
        
        if ($DB->get_num_rows() < 1) {
            $this->output .= $this->html->ucp_list_norows();
        }
        
        // List Users
        while ($USER->nextItem()) {
            $data = $USER->data;
            $this->output .= $this->html->ucp_list_row($data);
        }
        
        $count = $USER->countAll();
        if ($count['cnt'] <= 30) {
            $pages = "Single Page";
        } else {
            $pages = $STD->paginate($IN['st'], $count['cnt'], 30, "act=ucp&param=01&tab={$IN['tab']}&o={$IN['o']}");
        }
        
        $this->output .= $this->html->ucp_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
        //$TPL->addTag('user_list', $drows);
    }
    
    public function show_search_user()
    {
        global $IN, $STD, $CFG, $DB;
        
        $this->output = $STD->global_template->page_header('Find Users');
        
        $this->output .= $this->html->ucp_find_users();
        $this->output .= $this->html->ucp_find_email();
        $this->output .= $this->html->ucp_find_ip();
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_search_email()
    {
        global $IN, $STD, $CFG, $DB;
        
        $this->output = $STD->global_template->page_header('Find Users By Email');
        
        $this->output .= $this->html->ucp_find_email();
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_search_ip()
    {
        global $IN, $STD, $CFG, $DB;
        
        $this->output = $STD->global_template->page_header('Find Users By IP Address');
        
        $this->output .= $this->html->ucp_find_ip();
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_search_ip()
    {
        global $IN, $STD, $CFG, $DB;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        if (empty($IN['ip'])) {
            $STD->error("You must enter all or part of an IP address to do a quick search.");
        }
        
        // Start Output
        
        $this->output = $STD->global_template->page_header('Modify Users');
        
        $this->output .= $this->html->ucp_find_list_header();
            
        // Get users
        $USER = new user;
        $USER->query_use('group');
        $USER->query_order('u.username', 'ASC');
        $USER->query_limit($IN['st'], 30);
        $USER->query_condition("u.last_ip  LIKE '%" . $IN['ip'] . "%'");

        $USER->getAll();
        
        if ($DB->get_num_rows() < 1) {
            $this->output .= $this->html->ucp_list_norows();
        }
        
        // List Users
        while ($USER->nextItem()) {
            $data = $USER->data;
            $this->output .= $this->html->ucp_list_row($data);
        }
        
        $count = $USER->countAll();
        if ($count['cnt'] <= 30) {
            $pages = "Single Page";
        } else {
            $pages = "Pages: " . $STD->paginate($IN['st'], $count['cnt'], 30, "act=ucp&param=19&ip=".$IN['ip']);
        }
        
        $this->output .= $this->html->ucp_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_search_user()
    {
        global $IN, $STD, $CFG, $DB;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        if (empty($IN['username'])) {
            $STD->error("You must enter all or part of a username to do a quick search.");
        }
        
        // Start Output
        
        $this->output = $STD->global_template->page_header('Modify Users');
        
        $this->output .= $this->html->ucp_find_list_header();
            
        // Get users
        $USER = new user;
        $USER->query_use('group');
        $USER->query_order('u.username', 'ASC');
        $USER->query_limit($IN['st'], 30);
        $USER->query_condition("u.username LIKE '%" . $IN['username'] . "%'");

        $USER->getAll();
        
        if ($DB->get_num_rows() < 1) {
            $this->output .= $this->html->ucp_list_norows();
        }
        
        // List Users
        while ($USER->nextItem()) {
            $data = $USER->data;
            $this->output .= $this->html->ucp_list_row($data);
        }
        
        $count = $USER->countAll();
        if ($count['cnt'] <= 30) {
            $pages = "Single Page";
        } else {
            $pages = "Pages: " . $STD->paginate($IN['st'], $count['cnt'], 30, "act=ucp&param=15&username=".$IN['username']);
        }
        
        $this->output .= $this->html->ucp_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_search_email()
    {
        global $IN, $STD, $CFG, $DB;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        if (empty($IN['email'])) {
            $STD->error("You must enter all or part of an email address to do a quick search.");
        }
        
        // Start Output
        
        $this->output = $STD->global_template->page_header('Modify Users');
        
        $this->output .= $this->html->ucp_find_list_header();
            
        // Get users
        $USER = new user;
        $USER->query_use('group');
        $USER->query_order('u.username', 'ASC');
        $USER->query_limit($IN['st'], 30);
        $USER->query_condition("u.email LIKE '%" . $IN['email'] . "%'");

        $USER->getAll();
        
        if ($DB->get_num_rows() < 1) {
            $this->output .= $this->html->ucp_list_norows();
        }
        
        // List Users
        while ($USER->nextItem()) {
            $data = $USER->data;
            $this->output .= $this->html->ucp_list_row($data);
        }
        
        $count = $USER->countAll();
        if ($count['cnt'] <= 30) {
            $pages = "Single Page";
        } else {
            $pages = "Pages: " . $STD->paginate($IN['st'], $count['cnt'], 30, "act=ucp&param=17&email=".$IN['email']);
        }
        
        $this->output .= $this->html->ucp_list_footer($pages);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_user_edit()
    {
        global $IN, $STD, $CFG, $DB;
        
        //$this->cp_header = 'Modify Users';
        //$TPL->setTemplate('ucp_edit');
        $this->output = $STD->global_template->page_header('Modify Users');
        
        $USER = new user;
        $USER->query_use('group');
        $USER->query_use('session');
        
        if (!$USER->get($IN['u'])) {
            $STD->error("User with ID \"{$IN['u']}\" does not exist.");
        }
        
        $group = new group;
        $group->getAll();
        
        $v = array();
        $n = array();
        while ($group->nextItem()) {
            $v[] = $group->data['gid'];
            $n[] = $group->data['group_name'];
        }
        
        $form_elements = array();
        
        $selbox1 = $STD->make_select_box('group', $v, $n, $USER->data['gid'], 'selectbox');
        $form_elements['group'] = $selbox1;
        $selbox1 = $STD->make_select_box('def_order_by', array('','d','t','a','u'), array('---','Date','Title','Author','Last Update'), $USER->data['def_order_by'], 'selectbox');
        $selbox2 = $STD->make_select_box('def_order', array('','a','d'), array('---','Ascending Order','Descending Order'), $USER->data['def_order'], 'selectbox');
        $form_elements['order'] = "$selbox1 $selbox2";
        $selbox1 = $STD->make_select_box('skin', array('0','1','2','3'), array('---','TCSMS Default','New MFGG','MFGG Classic'), $USER->data['skin'], 'selectbox');
        $form_elements['skin'] = $selbox1;
        $selbox1 = $STD->make_select_box('items_per_page', array('0','20','40','60','80','100'), array('---','20','40','60','80','100'), $USER->data['items_per_page'], 'selectbox');
        $form_elements['items_per_page'] = $selbox1;
        $form_elements['show_email'] = $STD->make_yes_no('show_email', $USER->data['show_email']);

        $data = $USER->data;
        $this->output .= $this->html->ucp_edit_user($data, $form_elements, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
        
        //	$TPL->addTag('show_email', );
    //	$TPL->addTag('security_token', $STD->make_form_token());
    }
    
    public function do_user_edit()
    {
        global $IN, $STD, $CFG, $DB;

        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The update request did not originate from this site, or your request has allready been processed.");
        }
        
        // Drop redirect
        if (!empty($IN['drop_item'])) {
            header("Location: ".$_SERVER['PHP_SELF']."?act=ucp&param=05&u={$IN['uid']}&security_token={$IN['security_token']}");
            exit;
        }
        // Comment Purge redirect
        if (!empty($IN['comment_purge'])) {
            header("Location: ".$_SERVER['PHP_SELF']."?act=ucp&param=16&u={$IN['uid']}&security_token={$IN['security_token']}");
            exit;
        }
        
        $user = new user;
        $user->query_use('group');
        if (!$user->get($IN['uid'])) {
            $STD->error("User with ID \"{$IN['uid']}\" does not exist.");
        }

        // Does username allready exist if being changed?
        if (strlen($IN['username']) > 32) {
            $STD->error("Usernames cannot exceed 32 characters in length");
        }
            
        if ($IN['username'] != $user->data['username']) {
            $user_comp = new user;
            if ($user_comp->getByName($IN['username'])) {
                $STD->error("A user with the username \"{$IN['username']}\" allready exists.");
            }

            $user->data['username'] = $IN['username'];
        }
        
        // Is the password being changed?  Check that fields are consistant
        if (!empty($IN['password'])) {
            if ($IN['password'] != $IN['password2']) {
                $STD->error("The passwords typed into the password boxes must match.");
            }
            
            $user->data['password'] = md5($IN['password']);
        }
        
        // Avoid promotion / demotion
        $group = new group;
        if (!$group->get($IN['group'])) {
            $STD->error("Attempt to move user to a group that doesn't exist.");
        }
            
        if (!$STD->user['acp_super'] && $user->data['gid'] != $IN['group']) {
            if ($user->data['acp_access']) {
                $STD->error("You do not have permission to change another admin's group.");
            }
            if ($group->data['acp_access']) {
                $STD->error("You do not have permission to promote members to admin groups.");
            }
        }

        $user->data['email'] = $IN['email'];
        $user->data['website'] = $IN['website'];
        $user->data['weburl'] = $IN['weburl'];
        $user->data['icon'] = $IN['icon'];
        $user->data['def_order_by'] = $IN['def_order_by'];
        $user->data['def_order'] = $IN['def_order'];
        $user->data['skin'] = $IN['skin'];
        $user->data['items_per_page'] = $IN['items_per_page'];
        $user->data['show_email'] = $IN['show_email'];
        $user->data['gid'] = $IN['group'];

        $user->update();

        // Done
        $url_main = $STD->encode_url($_SERVER['PHP_SELF']);
        $url_back = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=02&u={$IN['uid']}");
        $message = "User <b>{$IN['username']}</b>'s account was updated successfully.
					<p align='center'><a href='$url_back'>Return to editing user</a><br />
					<a href='$url_main'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('User Updated');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_create_user()
    {
        global $IN, $DB, $CFG, $STD;

        $user = new user;
        $user->create(array('username'		=> 'New User',
                            'password'		=> md5(rand()),
                            'registered_ip'	=> '0.0.0.0',
                            'gid'			=> 5));
        $user->insert();
        
        $uid = $user->data['uid'];
        
        header("Location: ".$_SERVER['PHP_SELF']."?act=ucp&param=02&u=$uid");
        exit;
    }
    
    public function do_drop_user()
    {
        global $IN, $DB, $CFG, $STD;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The drop request did not originate from this site, or your request has allready been processed.");
        }

        $user = new user;

        if (!$user->get($IN['u'])) {
            $STD->error("User with ID \"{$IN['u']}\" does not exist.");
        }
        
        $username = $user->data['username'];
        
        $user->remove();
        
        $message = "User \"{$username}\" was successfully dropped from the database.
			<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Drop Successful');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_drop_comments()
    {
        global $IN, $DB, $CFG, $STD;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The drop request did not originate from this site, or your request has allready been processed.");
        }

        $user = new user;

        if (!$user->get($IN['u'])) {
            $STD->error("User with ID \"{$IN['u']}\" does not exist.");
        }
        
        $username = $user->data['username'];
        
        $where = $DB->format_db_where_string(array('uid'	=>	$user->data['uid']));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_comments WHERE $where");
        
        $message = "Comments by user \"{$username}\" were successfully dropped from the database.
			<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Drop Successful');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_ban_settings()
    {
        global $STD, $CFG;

        $this->output = $STD->global_template->page_header('Ban Settings');
        
        $blacklist = str_replace(",", "\n", $CFG['blacklist']);
        $whitelist = str_replace(",", "\n", $CFG['whitelist']);
        $emaillist = str_replace(",", "\n", $CFG['emaillist']);
        
        $this->output .= $this->html->ban_settings($blacklist, $whitelist, $emaillist, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function edit_ban_settings()
    {
        global $STD, $CFG, $IN;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The drop request did not originate from this site, or your request has allready been processed.");
        }
        
        $blacks = explode("<br />", $IN['blacklist']);
        
        // validate blacklist
        foreach ($blacks as $ip) {
            if (empty($ip)) {
                continue;
            }
                
            if (!preg_match("/^\d{1,3}\.(\*|\d{1,3}\.(\*|\d{1,3}\.(\*|\d{1,3})))$/", $ip)) {
                $STD->error("$ip is an invalid blacklist entry");
            }
        }
        
        $blacks = @join(",", $blacks);
        
        $whites = explode("<br />", $IN['whitelist']);
        
        // validate whitelist
        foreach ($whites as $ex) {
            if (empty($ex)) {
                continue;
            }
                
            if (!preg_match("/^\d+$/", $ex)) {
                $STD->error("$ex is an invalid whitelist entry");
            }
        }
        
        $whites = @join(",", $whites);
        
        $mails = explode("<br />", $IN['emaillist']);
        
        // validate email banlist
        foreach ($mails as $fe) {
            if (empty($fe)) {
                continue;
            }
                
            if (!preg_match($STD->get_regex('email'), $fe)) {
                $STD->error("$ex is an invalid whitelist entry");
            }
        }
        
        $mails = @join(",", $mails);
        
        $CFG['blacklist'] = $blacks;
        $CFG['whitelist'] = $whites;
        $CFG['emaillist'] = $mails;
        
        // Write settings
        require_once ROOT_PATH.'component/admin/adm_conf.php';
        $component->rewrite_settings($CFG);
        
        // Done
        $url_main = $STD->encode_url($_SERVER['PHP_SELF']);
        $url_back = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=06");
        $message = "The ban settings were updated successfully.
					<p align='center'><a href='$url_back'>Return to the ban settings</a><br />
					<a href='$url_main'>Return to the main page</a></p>";
                    
        $this->output = $STD->global_template->page_header('Update Successful');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_group_list()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
            
        // Order shenanigans
        $order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=07&st={$IN['st']}");
        
        $order_list = array('g' => 'g.group_name');
        $order_default = array('g', 'a');
        
        $order = $STD->order_translate($order_list, $order_default);
        $order_links = $STD->order_links($order_list, $order_url, $order_default);
        
        // Start output
        
        $this->output = $STD->global_template->page_header('Manage Groups');
        
        $this->output .= $this->html->group_list_header($order_links);
        
        $GROUP = new group;
        $GROUP->query_order($order[0], $order[1]);
        $GROUP->query_limit($IN['st'], 30);
        $GROUP->getAll();
        
        $v = array();
        $n = array();
        while ($GROUP->nextItem()) {
            $data = $GROUP->data;
            (!empty($data['acp_access']))
                ? $data['acp'] = "<span style='color:red'>YES</span>" : $data['acp'] = '';
            (!empty($data['moderator']))
                ? $data['mod'] = "<span style='color:red'>YES</span>" : $data['mod'] = '';
                
            $this->output .= $this->html->group_list_row($data);
            
            $v[] = $GROUP->data['gid'];
            $n[] = $GROUP->data['group_name'];
        }
        
        $count = $GROUP->countAll();
        $pages = $STD->paginate($IN['st'], $count['cnt'], 30, "act=ucp&param=07&o={$IN['o']}");
        
        $menu = $STD->make_select_box('clone', $v, $n, 5, 'selectbox');
        
        $this->output .= $this->html->group_list_footer($pages, $menu);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_group_edit()
    {
        global $IN, $STD, $CFG, $DB;
        
        $this->output = $STD->global_template->page_header('Modify Group');

        $group = new group;
        
        if (!$group->get($IN['gid'])) {
            $STD->error("Group with ID \"{$IN['gid']}\" does not exist.");
        }
        
        (!$STD->user['acp_super'])
            ? $dis = 1 : $dis = 0;
        
        $data = $group->data;
        $form_elements = array();

        $form_elements['can_submit'] = $STD->make_yes_no('can_submit', $group->data['can_submit']);
        $form_elements['can_report'] = $STD->make_yes_no('can_report', $group->data['can_report']);
        $form_elements['can_comment'] = $STD->make_yes_no('can_comment', $group->data['can_comment']);
        $form_elements['can_modify'] = $STD->make_yes_no('can_modify', $group->data['can_modify']);
        $form_elements['can_msg'] = $STD->make_yes_no('can_msg', $group->data['can_msg']);
        $form_elements['can_msg_users'] = $STD->make_yes_no('can_msg_users', $group->data['can_msg_users']);
        $form_elements['edit_comment'] = $STD->make_yes_no('edit_comment', $group->data['edit_comment']);
        $form_elements['delete_comment'] = $STD->make_yes_no('delete_comment', $group->data['delete_comment']);
        $form_elements['use_bbcode'] = $STD->make_yes_no('use_bbcode', $group->data['use_bbcode']);
        
        $form_elements['moderator'] = $STD->make_yes_no('moderator', $group->data['moderator']);
        $form_elements['acp_access'] = $STD->make_yes_no('acp_access', $group->data['acp_access'], $dis);
        $form_elements['acp_modq'] = $STD->make_yes_no('acp_modq', $group->data['acp_modq'], $dis);
        $form_elements['acp_users'] = $STD->make_yes_no('acp_users', $group->data['acp_users'], $dis);
        $form_elements['acp_news'] = $STD->make_yes_no('acp_news', $group->data['acp_news'], $dis);
        $form_elements['acp_msg'] = $STD->make_yes_no('acp_msg', $group->data['acp_msg'], $dis);
        $form_elements['acp_super'] = $STD->make_yes_no('acp_super', $group->data['acp_super'], $dis);

        $this->output .= $this->html->group_edit($data, $form_elements, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_group_edit()
    {
        global $STD, $IN;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The update request did not originate from this site, or your request has allready been processed.");
        }
        
        // Drop redirect
        if (!empty($IN['drop_item'])) {
            header("Location: ".$_SERVER['PHP_SELF']."?act=ucp&param=11&gid={$IN['gid']}");
            exit;
        }
        
        $group = new group;
        if (!$group->get($IN['gid'])) {
            $STD->error("Group with ID {$IN['gid']} does not exist.");
        }
        
        if (!$STD->user['acp_super'] && $group->data['acp_super']) {
            $STD->error("You do not have permission to change root-access groups.");
        }
        
        $group->data['group_name'] = $IN['group_name'];
        $group->data['group_title'] = $IN['group_title'];
        $group->data['msg_capacity'] = $IN['msg_capacity'];
        $group->data['can_submit'] = $IN['can_submit'];
        $group->data['can_comment'] = $IN['can_comment'];
        $group->data['can_report'] = $IN['can_report'];
        $group->data['can_modify'] = $IN['can_modify'];
        $group->data['can_msg'] = $IN['can_msg'];
        $group->data['can_msg_users'] = $IN['can_msg_users'];
        $group->data['edit_comment'] = $IN['edit_comment'];
        $group->data['delete_comment'] = $IN['delete_comment'];
        $group->data['use_bbcode'] = $IN['use_bbcode'];
        $group->data['moderator'] = $IN['moderator'];
        $group->data['name_prefix'] = $STD->rawclean_value($_POST['name_prefix']);
        $group->data['name_suffix'] = $STD->rawclean_value($_POST['name_suffix']);
        
        if ($STD->user['acp_super']) {
            $group->data['acp_access'] = $IN['acp_access'];
            $group->data['acp_modq'] = $IN['acp_modq'];
            $group->data['acp_users'] = $IN['acp_users'];
            $group->data['acp_news'] = $IN['acp_news'];
            $group->data['acp_msg'] = $IN['acp_msg'];
            $group->data['acp_super'] = $IN['acp_super'];
        }
        
        $group->update();
        
        // Done
        $url_main = $STD->encode_url($_SERVER['PHP_SELF']);
        $url_back = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=08&gid={$IN['gid']}");
        $message = "Group <b>{$IN['group_name']}</b> was updated successfully.
					<p align='center'><a href='$url_back'>Return to editing group</a><br />
					<a href='$url_main'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Group Updated');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_create_group()
    {
        global $IN, $DB, $CFG, $STD;

        $group = new group;
        if (!$group->get($IN['clone'])) {
            $STD->error("Attempt to create a group from one that doesn't exist");
        }
        
        if (!$STD->user['acp_super'] && $group->data['acp_super']) {
            $STD->error("You do not have permission to clone a root-access group.");
        }
            
        $group->data['group_name'] .= ' (Copy)';
        $group->data['group_title'] .= ' (Copy)';
        
        $group->insert();
        
        $gid = $group->data['gid'];
        
        header("Location: ".$_SERVER['PHP_SELF']."?act=ucp&param=08&gid=$gid");
        exit;
    }
    
    public function show_drop_group()
    {
        global $IN, $DB, $CFG, $STD;

        $this->output = $STD->global_template->page_header('Drop Group');

        $group = new group;
        
        if (!$group->get($IN['gid'])) {
            $STD->error("Group with ID \"{$IN['gid']}\" does not exist.");
        }
        
        if (!$STD->user['acp_super'] && $group->data['acp_super']) {
            $STD->error("You do not have permission to drop a root-access group.");
        }
        
        $data = $group->data;
        
        $group->getAll();
        $v=array();
        $n=array();
        while ($group->nextItem()) {
            if ($group->data['gid'] != $IN['gid']) {
                $v[] = $group->data['gid'];
                $n[] = $group->data['group_name'];
            }
        }
        
        $form_elements = array();
        $form_elements['group_menu'] = $STD->make_select_box('merge', $v, $n, 5, 'selectbox');
    
        $this->output .= $this->html->group_drop($data, $form_elements, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_drop_group()
    {
        global $IN, $DB, $CFG, $STD;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The drop request did not originate from this site, or your request has allready been processed.");
        }

        $group = new group;
        $merge = new group;

        if (!$group->get($IN['gid'])) {
            $STD->error("Group with ID \"{$IN['gid']}\" does not exist.");
        }
        
        if (!$merge->get($IN['merge'])) {
            $STD->error("Cannot merge into group that does not exist.");
        }
        
        if ($group->data['gid'] == $merge->data['gid']) {
            $STD->error("You cannot merge a group with itself.");
        }
        
        if (!$STD->user['acp_super'] && $group->data['acp_super']) {
            $STD->error("You do not have permission to drop a root-access group.");
        }
        
        if (!$STD->user['acp_super'] && $merge->data['acp_super']) {
            $STD->error("You do not have permission to merge into a root-access group.");
        }
        
        $group_name = $group->data['group_name'];
        
        $where = $DB->format_db_where_string(array('gid'	=> $IN['gid']));
        $upd = $DB->format_db_update_values(array('gid'		=> $IN['merge']));
        $DB->query("UPDATE {$CFG['db_pfx']}_users SET $upd WHERE $where");
        
        $group->remove();
        
        // Done
        $url_main = $STD->encode_url($_SERVER['PHP_SELF']);
        $message = "Group <b>{$group->data['group_name']}</b> was successfully dropped from the database.
					<p align='center'><a href='$url_main'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Group Dropped');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
}

?>