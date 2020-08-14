<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/main.php --
// Main page index and news display
//------------------------------------------------------------------

$component = new component_adm_main;

class component_adm_main
{
    public $html	= null;
    public $output	= "";
    
    public function init()
    {
        global $STD, $TPL, $DB, $CFG, $IN;
        
        //$STD->userobj->update_timeloc('ACP,Index');
        //	if (!$STD->user['acp_users'])
        //		$TPL->addTag('ucp_style', "style='display:none'");
        //	else
        //		$TPL->addTag('ucp_style', "");
        
        $this->html = $STD->template->useTemplate('adm_main');
        
        switch ($IN['param']) {
            case 2:	$this->update_notepad();
            // no break
            case 9:   $this->delete_notepad();
            // no break
            default:	$this->show_main();
        }
        
        //	$cp_content = $TPL->build();
        
        //	$TPL->setTemplate('main_acp');
        //	$TPL->addTag('cp_header', 'Welcome');
        //	$TPL->addTag('cp_content', $cp_content);
        
        //	$time = time() - 60*20;
        //	$DB->query("SELECT username FROM {$CFG['db_pfx']['user']} WHERE last_loc LIKE 'ACP,%' AND last_time > $time ORDER BY last_time DESC");
        
        //	$names = '';
        //	while ($name = $DB->fetch_row()) {
        //		$names .= "{$name['username']}, ";
        //	}
        //	$names = preg_replace('/,[ ]$/', '', $names);
        
        //	$TPL->addTag('active_users', $names);
        //	$TPL->addTag('active_users', '');
        //	$TPL->addTag('site_url', $CFG['root_url']);
        
        //	component_adm_main::menus();
        
        //	if (!$STD->user['acp_users'])
        //		$TPL->addTag('ucp_style', "style='display:none'");
        //	else
        //		$TPL->addTag('ucp_style', "");
        
        //	$TPL->display();
        $STD->template->display($this->output);
    }
    
    public function show_main()
    {
        global $STD, $CFG, $DB;
        
        $notepad = ''; /*if (file_exists(ROOT_PATH.'component/include/acpnotepad.php'))
            include ROOT_PATH.'component/include/acpnotepad.php';
        $notepad = str_replace('&', '&amp;', $notepad); */
        
        $notepad_url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=main&param=02');
        $delurl = $STD->encode_url($_SERVER['PHP_SELF'], 'act=main&param=9');
        $cq = $DB->query("SELECT sc.id, sc.uid, sc.date, sc.message, u.username FROM {$CFG['db_pfx']}_staffchat AS sc LEFT JOIN {$CFG['db_pfx']}_users AS u ON sc.uid = u.uid ORDER BY date DESC LIMIT 100");
        $data = array();
        $dat = array();
        $parser = new parser;
        while ($row = $DB->fetch_row($cq)) {
            $dat['id'] = $row['id'];
            $dat['name'] = $row['username'];
            $dat['uid'] = $row['uid'];
            $dat['uidurl'] = $STD->encode_url($_SERVER['PHP_SELF'], 'act=ucp&param=02&u='.$row['uid']);
            $dat['message'] = nl2br($parser->convert(stripslashes($row['message'])));
            $dat['raw'] = str_replace(array("[gonzo]", "[/gonzo]"), array("[img]", "[/img]"), stripslashes($row['message']));
            $dat['date'] = $STD->make_date_time($row['date']);
            $data[] = $dat;
        }
        
        $this->output .= $STD->global_template->page_header('Welcome');
        $this->output .= $this->html->main_page($notepad, $notepad_url, $data, $delurl, $STD->user['uid']);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function update_notepad()
    {
        global $STD, $IN, $DB, $CFG;
        
        $in = htmlentities(addslashes(stripslashes($_POST['notepad'])));
        $uid = $STD->user['uid'];
        $date = time();

        $DB->query("INSERT INTO {$CFG['db_pfx']}_staffchat (id, uid, date, message) VALUES (NULL, $uid, $date, '$in')");
        
        /*$fh = fopen(ROOT_PATH.'component/include/acpnotepad.php', 'w');
        fwrite($fh, $notepad);
        fclose($fh);*/
        $url = str_replace('&amp;', '&', $STD->encode_url($_SERVER['PHP_SELF'], 'act=main'));
        header("Location: $url");
        exit;
    }
    
    public function delete_notepad()
    {
        global $STD, $DB, $CFG;
        $uid = $STD->user['uid'];
        $id = addslashes(stripslashes($_GET['id']));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_staffchat WHERE id=$id AND uid=$uid LIMIT 1");
        
        $url = str_replace('&amp;', '&', $STD->encode_url($_SERVER['PHP_SELF'], 'act=main'));
        header("Location: $url");
    }
    
    public function menus()
    {
        global $STD;
        
        $menus = '';

        //	$DB->query("SELECT mid,module_name FROM {$CFG['db_pfx']}_modules");
        //	while ($row = $DB->fetch_row()) {
        reset($STD->modules->module_set);
        while (list(, $row) = each($STD->modules->module_set)) {
            $menus .= ":: <a href='{{root_url}}act=modq&amp;param=01&amp;c={$row['mid']}'>{$row['module_name']}</a><br />";
        }
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=modq&param=08');
        $menus .= ":: <a href='$url'>Create New</a><br />";
        
        //$TPL->addTag('modq_menu', $menus);
    }
}
