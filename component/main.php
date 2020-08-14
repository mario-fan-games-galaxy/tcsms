<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/main.php --
// Main page index, news display, commenting, and reporting
//------------------------------------------------------------------

$component = new component_main;

class component_main
{
    public $html		= "";
    public $output		= "";
    
    public function init()
    {
        global $STD, $IN;
        
        $this->html = $STD->template->useTemplate('main');
        
        switch ($IN['param']) {
            case  2: $this->show_news_single(); break;
            case  3: $this->add_comment(); break;
            case  4: $this->do_delete_comment(); break;
            case  5: $this->show_report(); break;
            case  6: $this->do_report(); break;
            case  7: $this->show_staff(); break;
            case  8: $this->show_news_archive(); break;
            case  9: $this->show_edit_comment(); break;
            case 10: $this->do_edit_comment(); break;
            case 11: $this->show_add_comment(); break;
            case 12: $this->show_sec_image(); break;
            default: $this->show_news(); break;
        }
        
        $STD->template->display($this->output);
        //	$TPL->template = $this->output;
    //	$TPL->display();
    }
    
    public function show_news()
    {
        global $STD;
        
        require_once ROOT_PATH.'lib/news.php';
        
        $this->output .= $STD->global_template->page_header('Updates');
        $this->output .= $this->html->news_header();
        
        $NEWS = new news;
        $NEWS->query_use('n_user');
        $NEWS->query_limit('0', '10');
        $NEWS->query_order('date', 'DESC');
        $NEWS->getAll();
        
        while ($NEWS->nextItem()) {
            $data = $NEWS->data;
            $data['date'] = $STD->make_date_time($data['date']);
            $data['author'] = $STD->format_username($data, 'nu_');
            $data['icon'] = $STD->get_user_icon($data, 'nu_');
            $data['message'] = $STD->untag_urls($data['message']);
            $this->output .= $this->html->news_row($data);
        }
        
        $this->output .= $this->html->news_footer();
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_news_single()
    {
        global $STD, $IN;
        
        require_once ROOT_PATH.'lib/news.php';
        require_once ROOT_PATH.'lib/message.php';
        
        if (!empty($IN['st']) && $IN['st'] == 'new') {
            $this->last_unread_comments(2, $IN['id'], "act=main&param=02&id={$IN['id']}");
        }
        
        $this->output .= $STD->global_template->page_header('Updates');
        $this->output .= $this->html->news_header();
        
        $NEWS = new news;
        $NEWS->query_use('n_user');
        
        if (!$NEWS->get($IN['id'])) {
            $STD->error("News entry does not exist");
        }
        
        $data = $NEWS->data;
        $data['date'] = $STD->make_date_time($data['date']);
        $data['author'] = $STD->format_username($data, 'nu_');
        $data['icon'] = $STD->get_user_icon($data, 'nu_');
        $data['message'] = $STD->untag_urls($data['message']);
        $this->output .= $this->html->news_row($data);
        
        $this->output .= $this->html->news_footer();
        
        // Comments
        $this->build_comments(2, $IN['id'], "act=main&param=02&id={$IN['id']}");

        $this->output .= $STD->global_template->page_footer();
    }
        
    public function add_comment()
    {
        global $CFG, $STD, $DB, $IN;
        
        require_once ROOT_PATH.'lib/message.php';
        
        if (!$STD->user['can_comment']) {
            $STD->error("You do not have permission to leave comments.");
        }
        
        if (strlen($IN['message']) < 8) {
            $STD->error("Comment is insufficient length.");
        }
        
        $IN['message'] = $STD->limit_string($IN['message'], 4096);
        
        require_once ROOT_PATH.'lib/parser.php';
        $PARSER = new parser;
        
        $IN['message'] = preg_replace("/\[\/quote\]<br \/><br \/>/i", "[/quote]<br />", $IN['message']);
        $IN['message'] = $PARSER->convert($IN['message']);
        
        $COM = new comment;
        
        if ($IN['type'] == 1) {
            require_once ROOT_PATH.'lib/resource.php';
            
            $module = $STD->modules->new_module($IN['c']);
            //$module = new $MODULE['class_name'];
            $module->init();
        
            $RES = new resource;
            $RES->query_use('r_user');
            if (!$RES->get($IN['id'])) {
                $STD->error("Invalid resource selected");
            }
            
            $COM->create(array('rid' => $IN['id'],
                               'uid' => $STD->user['uid'],
                               'message' => $IN['message'],
                               'type' => $IN['type'],
                               'ip' => $_SERVER['REMOTE_ADDR']));
            
            $RES->data['comments']++;
            $RES->data['comment_date'] = time();
            $RES->update();
            
            $location = "act=resdb&param=02&c={$IN['c']}&id={$IN['id']}&st=new";
            
            // Notify user
            if ($RES->data['uid'] != $STD->user['uid'] &&
                $RES->data['ru_use_comment_msg'] == 1 &&
                ($RES->data['ru_use_comment_digest'] == 0 || (time()-$RES->data['ru_last_activity']) < 15*60)) {
                $MSG = new message;
                $MSG->data['receiver'] = $RES->data['uid'];
                $MSG->data['owner'] = $RES->data['uid'];
                $MSG->data['title'] = "Comment received on submission";
                $MSG->data['message'] = "You have received a new comment on your submission: <a href='{%site_url%}?$location'>{$RES->data['title']}</a>";
                $MSG->dispatch();
                
                $MSG->data['conversation'] = $MSG->data['mid'];
                $MSG->update();
            }
        } else {
            require_once ROOT_PATH.'lib/news.php';
            
            $NEWS = new news;
            if (!$NEWS->get($IN['id'])) {
                $STD->error("Invalid news item selected");
            }
            
            $COM->create(array('rid' => $IN['id'],
                               'uid' => $STD->user['uid'],
                               'message' => $IN['message'],
                               'type' => $IN['type'],
                               'ip' => $_SERVER['REMOTE_ADDR']));
            
            $NEWS->data['comments']++;
            $NEWS->update();
            
            $location = "act=news&param=02&id={$IN['id']}";
        }
        
        $COM->insert();
        
        $STD->userobj->data['comments']++;
        $STD->userobj->update();
        
        $location = $STD->encode_url($_SERVER['PHP_SELF'], $location);
        $location = str_replace("&amp;", '&', $location);
        
        header("Location: $location");
        exit;
    }
    
    public function do_delete_comment()
    {
        global $CFG, $STD, $IN;
        
        require_once ROOT_PATH.'lib/message.php';
        
        $COM = new comment;
        if (!$COM->get($IN['cid'])) {
            $STD->error("Attempt to delete a comment that does not exist.");
        }
            
        $perm = 0;
        if ($STD->user['delete_comment'] && $COM->data['uid'] == $STD->user['uid']) {
            $perm = 1;
        } elseif ($STD->user['moderator']) {
            $perm = 1;
        }
        
        if (!$perm) {
            $STD->error("You do not have permission to delete comments.");
        }

        if ($IN['type'] == 1) {
            require_once ROOT_PATH.'lib/resource.php';
            
            $module = $STD->modules->new_module($IN['c']);
            $module->init();
        
            $RES = new resource;
            if (!$RES->get($COM->data['rid'])) {
                $STD->error("Invalid resource selected");
            }
            
            $LCOM = new comment;
            $LCOM->query_order('date', 'DESC');
            $LCOM->query_condition('type = 1');
            $LCOM->query_condition("rid = '{$RES->data['rid']}'");
            $LCOM->query_condition("cid <> '{$IN['cid']}'");
            $LCOM->query_limit(0, 1);
            $LCOM->getAll();
            
            if ($LCOM->nextItem()) {
                $RES->data['comment_date'] = $LCOM->data['date'];
            } else {
                $RES->data['comment_date'] = 0;
            }
            
            $RES->data['comments']--;
            $RES->update();
        } elseif ($IN['type'] == 2) {
            require_once ROOT_PATH.'lib/news.php';
            
            $NEWS = new news;
            if (!$NEWS->get($COM->data['rid'])) {
                $STD->error("Invalid news item selected");
            }
            
            $NEWS->data['comments']--;
            $NEWS->update();
        }
        
        $COM->remove();
        
        switch ($IN['type']) {
            case '1': $url = "act=resdb&param=02&c={$IN['c']}&id={$IN['rid']}"; break;
            case '2': $url = "act=main&param=02&id={$IN['rid']}"; break;
            default: $url = ""; break;
        }
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], $url);
        $url = str_replace("&amp;", "&", $url);
        
        header("Location: $url");
        exit;
    }
    
    public function build_comments($type, $id, $url='')
    {
        global $STD, $IN;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        
        $this->html = $STD->template->useTemplate('main');
        
        $this->output .= $this->html->comments_header();
        
        $COM = new comment;
        $COM->query_use('c_user');
        $COM->query_order('c.date', 'ASC');
        $COM->query_limit($IN['st'], '40');
        $COM->query_condition("c.type = '$type'");
        $COM->query_condition("c.rid = '$id'");
        $COM->getAll();
        
        $num_comments = 0;
        
        while ($COM->nextItem()) {
            switch ($type) {
                case 1: $stype = 2; break;
                case 2: $stype = 4; break;
            }
            
            $delete_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=04&type={$type}&c={$IN['c']}&rid={$IN['id']}&cid={$COM->data['cid']}");
            $edit_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=09&type={$type}&c={$IN['c']}&rid={$IN['id']}&cid={$COM->data['cid']}");
            $report_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=05&type={$stype}&id={$COM->data['cid']}");
            $quote_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=11&q={$COM->data['cid']}");
            
            $COM->data['author'] = $STD->format_username($COM->data, 'cu_');
            
            //$COM->data['email_icon'] = $STD->get_email_icon($COM->data, 'cu_');
            //$COM->data['website_icon'] = $STD->get_website_icon($COM->data, 'cu_');
            $COM->data['date'] = $STD->make_date_time($COM->data['date']);

            ($STD->user['moderator'] || ($STD->user['delete_comment'] && $STD->user['uid'] == $COM->data['uid']))
                ? $COM->data['delete_icon'] = "<a href='$delete_url' onclick='return check_delete();'><img src='{$STD->tags['image_path']}/delete.gif' border='0' alt='[Del]' title='Delete Comment' /></a>"
                : $COM->data['delete_icon'] = '';
            ($STD->user['moderator'] || ($STD->user['edit_comment'] && $STD->user['uid'] == $COM->data['uid']))
                ? $COM->data['edit_icon'] = "<a href='$edit_url'><img src='{$STD->tags['image_path']}/edit.gif' border='0' alt='[Edit]' title='Edit Comment' /></a>"
                : $COM->data['edit_icon'] = '';
            ($STD->user['moderator'] || $STD->user['can_report'])
                ? $COM->data['report_icon'] = "<a href='$report_url'><img src='{$STD->tags['image_path']}/report.gif' border='0' alt='[Report]' title='Report Comment' /></a>"
                : $COM->data['report_icon'] = '';
            ($STD->user['can_comment'])
                ? $COM->data['quote_icon'] = "<a href='$quote_url'><img src='{$STD->tags['image_path']}/quote.gif' border='0' alt='[Quote]' title='Quote Comment' /></a>"
                : $COM->data['quote_icon'] = '';

            $this->output .= $this->html->comments_row($COM->data);
            $num_comments++;
        }
        
        $rcnt = $COM->countAll();
        $pages = $STD->paginate($IN['st'], $rcnt['cnt'], 40, $url);
        
        if (!$num_comments) {
            $this->output .= $this->html->comments_none();
        }
        
        $this->output .= $this->html->comments_footer($pages, $STD->encode_url($_SERVER['PHP_SELF'], $url));
        
        (!empty($IN['exp']))
            ? $aexpand = '' : $aexpand = 'display:none';
            
        if ($STD->user['can_comment']) {
            $this->output .= $this->html->comments_add($STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=03&type={$type}&c={$IN['c']}&id={$IN['id']}"), $aexpand);
        }
        
        return $this->output;
    }
    
    public function last_unread_comments($type, $id, $url)
    {
        global $STD, $IN, $session;
        
        $date = $STD->user['last_visit'];
        
        if (empty($session->data['rr'])) {
            $session->data['rr'] = array();
        }
        $rr = empty($session->data['rr'][$id]) ? 0 : $session->data['rr'][$id];
        
        if ($rr > $date) {
            $date = $rr;
        }
        
        $COM = new comment;
        $COM->query_condition("c.type = '{$type}'");
        $COM->query_condition("c.rid = '{$id}'");
        $COM->query_condition("date < '{$date}'");
        $cnt = $COM->countAll();
        $st = ($cnt['cnt']+1) - (($cnt['cnt']+1) % 40);
        
        $COM = new comment;
        $COM->query_condition("c.type = '{$type}'");
        $COM->query_condition("c.rid = '{$id}'");
        $COM->query_condition("date >= '{$date}'");
        $COM->query_order('c.date', 'asc');
        $COM->query_limit(0, 1);
        $COM->getAll();
        
        $row = $COM->nextItem();
        if ($row) {
            $entry = "#c{$COM->data['cid']}";
        } else {
            $COM = new comment;
            $COM->query_condition("c.type = '{$type}'");
            $COM->query_condition("c.rid = '{$id}'");
            $COM->query_order('c.date', 'desc');
            $COM->query_limit(0, 1);
            $COM->getAll();
            
            $row = $COM->nextItem();
            if ($row) {
                $entry = "#c{$COM->data['cid']}";
            } else {
                $entry = '';
            }
                
            $st = $cnt['cnt'] - ($cnt['cnt'] % 40);
        }
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], "{$url}&st={$st}{$entry}");
        $url = str_replace("&amp;", "&", $url);
        header("Location: $url");
        exit;
    }
    
    public function show_report()
    {
        global $CFG, $STD, $DB, $IN;
        
        if (!$STD->user['can_report'] && !$STD->user['moderator']) {
            $STD->error("You must be logged in and have permission to report objectionable submissions.");
        }
        
        if ($IN['type'] == 1) {
            require_once ROOT_PATH.'lib/resource.php';
            
            $OBJ = new resource;
            $OBJ->get($IN['id']);
        } elseif (in_array($IN['type'], array(2,4))) {
            require_once ROOT_PATH.'lib/message.php';
            
            $OBJ = new comment;
            $OBJ->query_use('c_user');
            switch ($IN['type']) {
                case 2: $OBJ->query_use('resource'); break;
                case 4: $OBJ->query_use('news'); break;
            }
            $OBJ->get($IN['id']);
        } elseif ($IN['type'] == 3) {
            require_once ROOT_PATH.'lib/message.php';
            
            $OBJ = new message;
            $OBJ->query_use('s_user');
            $OBJ->get($IN['id']);
        }
        
        if (!$OBJ) {
            $STD->error("This subject for this report does not exist");
        }
        
        if ($IN['type'] == 3 && $OBJ->data['receiver'] != $STD->user['uid']) {
            $STD->error("You cannot report a message you do not own.");
        }
        
        $this->output .= $STD->global_template->page_header('Send Report');
        
        $rep_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=06&type={$IN['type']}");
        
        switch ($IN['type']) {
            case 1: $this->output .= $this->html->report_sub($IN['id'], $rep_url, $OBJ->data['title']); break;
            case 2: $this->output .= $this->html->report_sub_com($IN['id'], $rep_url, $OBJ->data['r_title'], $OBJ->data['cu_username']); break;
            case 3: $this->output .= $this->html->report_msg($IN['id'], $rep_url, $OBJ->data['title'], $OBJ->data['su_username']); break;
            case 4: $this->output .= $this->html->report_news_com($IN['id'], $rep_url, $OBJ->data['n_title'], $OBJ->data['cu_username']); break;
        }
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_report()
    {
        global $CFG, $STD, $DB, $IN;
        
        if (!$STD->user['can_report'] && !$STD->user['moderator']) {
            $STD->error("You must be logged in and have permission to report objectionable submissions.");
        }
        
        if (empty($IN['report'])) {
            $STD->error("You must provide an explaination for your report.");
        }
        
        $IN['report'] = $STD->limit_string($IN['report'], 2048);
        
        if ($IN['type'] == 1) {
            require_once ROOT_PATH.'lib/resource.php';
            
            $OBJ = new resource;
            $OBJ->query_use('r_user');
            $OBJ->get($IN['id']);
        } elseif (in_array($IN['type'], array(2,4))) {
            require_once ROOT_PATH.'lib/message.php';
            
            $OBJ = new comment;
            $OBJ->query_use('c_user');
            switch ($IN['type']) {
                case 2: $OBJ->query_use('r_user'); break;
                case 4: $OBJ->query_use('n_user'); break;
            }
            $OBJ->get($IN['id']);
        } elseif ($IN['type'] == 3) {
            require_once ROOT_PATH.'lib/message.php';
            
            $OBJ = new message;
            $OBJ->query_use('s_user');
            $OBJ->get($IN['id']);
        }
        
        if (!$OBJ) {
            $STD->error("The subject for this report does not exist.");
        }
        
        if ($IN['type'] == 3 && $OBJ->data['receiver'] != $STD->user['uid']) {
            $STD->error("You cannot report a message you do not own.");
        }
        
        switch ($IN['type']) {
            case 1: $rep_url = "act=resdb&param=02&c={$OBJ->data['type']}&id={$OBJ->data['rid']}"; break;
            case 2: $rep_url = "act=resdb&param=02&c={$OBJ->data['r_type']}&id={$OBJ->data['r_rid']}"; break;
            case 3: $rep_url = "act=msg&param=01"; break;
            case 4: $rep_url = "act=main&param=02&id={$OBJ->data['n_nid']}"; break;
        }
        
        require ROOT_PATH.'lib/parser.php';
        $PARSER = new parser;
        
        $rep_url = $STD->encode_url($_SERVER['PHP_SELF'], $rep_url);
        
        switch ($IN['type']) {
            case 1:
                $title = "Reported: {$OBJ->data['title']}";
                $mesg1 = "Reported Submission: <a href='$rep_url'>{$OBJ->data['title']}</a> by {$OBJ->data['ru_username']}<br /><br />";
                $mesg2 = "";
                $retrn = "Return to viewing submission";
                $rtype = 1; break;
            case 2:
                $title = "Reported: Comment";
                $mesg1 = "Reported Comment:<br /><br /><div class='rep_box'>{$OBJ->data['message']}</div><br />Comment By: {$OBJ->data['cu_username']}";
                $mesg2 = "<br />In: <a href='$rep_url'>{$OBJ->data['r_title']}</a><br /><br />";
                $retrn = "Return to viewing submission";
                $rtype = 2; break;
            case 3:
                $title = "Reported: Personal Message";
                $mesg1 = "Reported Personal Message:<br />Subject: {$OBJ->data['title']}<br /><br />";
                $mesg2 = "<div class='rep_box'>{$OBJ->data['message']}</div><br />Message By: {$OBJ->data['su_username']}<br /><br />";
                $retrn = "Return to message center";
                $rtype = 3; break;
            case 4:
                $title = "Reported: Comment";
                $mesg1 = "Reported Comment:<br /><br /><div class='rep_box'>{$OBJ->data['message']}</div><br />Comment By: {$OBJ->data['cu_username']}";
                $mesg2 = "<br />In News Entry: <a href='$rep_url'>{$OBJ->data['n_title']}</a><br /><br />";
                $retrn = "Return to viewing news entry";
                $rtype = 2; break;
        }
        
        $IN['report'] = $PARSER->convert($IN['report']);
        $message = $mesg1.$mesg2.
                   "------------------------------------------------------<br />".
                   "Reported By: {$STD->user['username']}<br /><br />".
                   "The above submission was reported for the following reason:<br /><br />{$IN['report']}";
        
        $insert = $DB->format_db_values(array('sender'	=> $STD->user['uid'],
                                              'date'	=> time(),
                                              'title'	=> $title,
                                              'message'	=> $message,
                                              'type'	=> $rtype,
                                              'aux'		=> $IN['id']));
        $DB->query("INSERT INTO {$CFG['db_pfx']}_admin_msg ({$insert['FIELDS']}) VALUES ({$insert['VALUES']})");
        
        //------------------------------------------------
        // Message
        //------------------------------------------------
        
        $url2 = $STD->encode_url($_SERVER['PHP_SELF'], '');
        
        $message = "The report was successfully sent to the site staff.
			Your report will be reviewed by a staff member shortly.
			<p align='center'><a href='$rep_url'>$retrn</a><br />
			<a href='$url2'>Return to the main page</a></p>";
        
        $this->output .= $STD->global_template->message($message);
    }
    
    public function show_staff()
    {
        global $STD;

        $this->output .= $STD->global_template->page_header('Staff');
        
        $this->output .= $this->html->staff_page();
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_news_archive()
    {
        global $STD, $IN;
        
        require_once ROOT_PATH.'lib/news.php';
        
        // Get date constraints
        $curr_yr = gmdate("Y", $STD->translate_date(time()));
        
        $NEWSOLD = new news;
        $NEWSOLD->query_order('date', 'ASC');
        $NEWSOLD->query_limit('0', '1');
        $NEWSOLD->getAll();
        
        if (!$NEWSOLD->nextItem()) {
            $base_yr = 1980;
        } else {
            $base_yr = gmdate("Y", $STD->translate_date($NEWSOLD->data['date']));
        }
        
        // Get selected dates
        
        if (!empty($IN['from_d'])) {
            $from_day = $IN['from_d'];
            $from_mon = $IN['from_m'];
            $from_year = $IN['from_y'];
            $to_day = $IN['to_d'];
            $to_mon = $IN['to_m'];
            $to_year = $IN['to_y'];
        } else {
            $from_day = gmdate("j", $STD->translate_date(time()));
            $from_mon = gmdate("n", $STD->translate_date(time()));
            $from_year = $curr_yr;
            $to_day = 1;
            $to_mon = $from_mon;
            $to_year = $curr_yr;
        }
        
        $upper = gmmktime(0, 0, 0, $from_mon, $from_day, $from_year)+60*60*24-1;
        $lower = gmmktime(0, 0, 0, $to_mon, $to_day, $to_year);
        $ordir = 'DESC';
        
        if ($upper < $lower) {
            $STD->swap($upper, $lower);
            $ordir = 'ASC';
        }
        
        // Build date arrays
        
        $curr_day = gmdate("j", $STD->translate_date(time()));
        $curr_mon = gmdate("n", $STD->translate_date(time()));
        
        $d_array = range(1, 31);
        $y_array = range($base_yr, $curr_yr);
        $m_array = range(1, 12);
        $m_array_n = array('January','February','March','April','May','June','July','August','September','October','November','December');
        
        $from = array();
        $from['d'] = $STD->make_select_box('from_d', $d_array, $d_array, $from_day, 'selectbox');
        $from['m'] = $STD->make_select_box('from_m', $m_array, $m_array_n, $from_mon, 'selectbox');
        $from['y'] = $STD->make_select_box('from_y', $y_array, $y_array, $from_year, 'selectbox');
        
        $to = array();
        $to['d'] = $STD->make_select_box('to_d', $d_array, $d_array, $to_day, 'selectbox');
        $to['m'] = $STD->make_select_box('to_m', $m_array, $m_array_n, $to_mon, 'selectbox');
        $to['y'] = $STD->make_select_box('to_y', $y_array, $y_array, $to_year, 'selectbox');
        
        // Begin Output
        
        $this->output .= $STD->global_template->page_header('Updates Archive');
        $this->output .= $this->html->news_archive_header($from, $to);
        
        $NEWS = new news;
        $NEWS->query_use('n_user');
        //	$NEWS->query_limit('0', '10');
        $NEWS->query_order('date', $ordir);
        $NEWS->query_condition("date >= '$lower'");
        $NEWS->query_condition("date <= '$upper'");
        $NEWS->getAll();
        
        while ($NEWS->nextItem()) {
            $data = $NEWS->data;
            $data['date'] = $STD->make_date_time($data['date']);
            $data['author'] = $STD->format_username($data, 'nu_');
            $data['icon'] = $STD->get_user_icon($data, 'nu_');
            $data['message'] = preg_replace("/\{%site_url%\}/", '', $data['message']);
            $this->output .= $this->html->news_row($data);
        }
        
        $this->output .= $this->html->news_footer();
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_edit_comment()
    {
        global $STD, $IN;
        
        require_once ROOT_PATH.'lib/message.php';
        require_once ROOT_PATH.'lib/parser.php';
        
        $PARSER = new parser;
        
        $COM = new comment;
        $COM->query_use('c_user');
        
        if (!$COM->get($IN['cid'])) {
            $STD->error("Attempt to edit a comment that does not exist.");
        }
        
        $perm = 0;
        if ($STD->user['uid'] == $COM->data['uid'] && $STD->user['edit_comment']) {
            $perm = 1;
        } elseif ($STD->user['moderator']) {
            $perm = 1;
        }
        
        if (!$perm) {
            $STD->error("You do not have permission to edit this comment.");
        }
        
        // Generate comment
        $COM->data['author'] = $STD->format_username($COM->data, 'cu_');
        $COM->data['date'] = $STD->make_date_time($COM->data['date']);
        $COM->data['report_icon'] = '';
        $COM->data['delete_icon'] = '';
        $COM->data['edit_icon'] = '';
        $COM->data['quote_icon'] = '';
        $chtml = $this->html->comments_row($COM->data);
        
        $COM->data['message'] = $PARSER->unconvert($COM->data['message']);
        
        $curl = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=10&type={$IN['type']}&c={$IN['c']}&rid={$IN['rid']}&cid={$IN['cid']}");
        
        $this->output = $STD->global_template->page_header("Edit Comment");
        
        $this->output .= $this->html->comments_edit($COM->data['message'], $chtml, $curl);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_edit_comment()
    {
        global $STD, $IN;
        
        require_once ROOT_PATH.'lib/message.php';
        require_once ROOT_PATH.'lib/parser.php';
        
        $PARSER = new parser;
        $COM = new comment;
        
        if (!$COM->get($IN['cid'])) {
            $STD->error("Attempt to edit a comment that does not exist.");
        }
        
        $perm = 0;
        if ($STD->user['uid'] == $COM->data['uid'] && $STD->user['edit_comment']) {
            $perm = 1;
        } elseif ($STD->user['moderator']) {
            $perm = 1;
        }
        
        if (!$perm) {
            $STD->error("You do not have permission to edit this comment.");
        }
        
        if (strlen($IN['message']) < 8) {
            $STD->error("Comment is insufficient length.");
        }
        
        $COM->data['message'] = $PARSER->convert($IN['message']);
        
        $COM->update();
        
        switch ($IN['type']) {
            case '1': $url = "act=resdb&param=02&c={$IN['c']}&id={$IN['rid']}"; break;
            case '2': $url = "act=main&param=02&id={$IN['rid']}"; break;
            default: $url = ""; break;
        }
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], $url);
        $url = str_replace("&amp;", "&", $url);
        
        header("Location: $url");
        exit;
    }
    
    public function show_add_comment()
    {
        global $STD, $IN, $CFG, $session;
        
        if (!$STD->user['can_comment']) {
            $STD->error("You do not have permission to leave comments.");
        }
            
        require_once ROOT_PATH.'lib/message.php';
        require_once ROOT_PATH.'lib/parser.php';
        
        $PARSER = new parser;
        
        $COM = new comment;
        $COM->query_use('resource');
        $COM->query_use('c_user');
        
        if (!$COM->get($IN['q'])) {
            $STD->error("Attempt to edit a comment that does not exist.");
        }
        
        $date = $STD->make_date_time($COM->data['date']);
        $message = $PARSER->unconvert($COM->data['message']);
        
        if ($CFG['quote_nesting'] == 0) {
            $message = preg_replace("/\[quote(=.+?)?\](.*)\[\/quote\]\n*/is", "", $message);
        }
        
        $message = preg_replace("/\n*$/", "", $message);
        
        if (strpos($COM->data['cu_username'], ',') !== false) {
            $COM->data['cu_username'] = "\"" . $COM->data['cu_username'] . "\"";
        }
            
        $data = "[quote=".$COM->data['cu_username'].",".$date."]".$message."[/quote]\n";
        
        $type = $COM->data['type'];
        $c = $COM->data['r_type'];
        $id = $COM->data['rid'];
        
        $curl = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=03&type={$type}&c={$c}&id={$id}");
        
        $this->output = $STD->global_template->page_header("Add Comment");
        
        $this->output .= $this->html->comments_add_full($data, $curl);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_sec_image()
    {
        global $STD, $DB, $CFG, $session;
        
        $sess = $session->sess_id;

        $DB->query("SELECT regcode FROM {$CFG['db_pfx']}_sec_images WHERE sessid = '{$sess}'");
        $row = $DB->fetch_row();
        
        if (!$row) {
            $STD->captcha(" ");
        } else {
            $STD->captcha($row['regcode']);
        }
        
        //exit;
    }
}
