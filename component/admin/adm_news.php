<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/news.php --
// News portion of ACP
//------------------------------------------------------------------

$component = new component_adm_news;

class component_adm_news
{
    public $html		= "";
    public $output		= "";
    
    public $cp_header = '';
    
    public function init()
    {
        global $STD, $IN, $DB, $CFG;
        
        require ROOT_PATH.'lib/news.php';
        
        $this->html = $STD->template->useTemplate('adm_news');
        
        if (!$STD->user['acp_news']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        switch ($IN['param']) {
            case  1: $this->show_newsform(); break;
            case  2: $this->add_news(); break;
            case  3: $this->edit_news(); break;
            case  4: $this->edit_single_news(); break;
            case  5: $this->do_delete_news(); break;
            case  6: $this->do_edit_news(); break;
        }
        
        //	$cp_content = $TPL->build();
        
        //	$TPL->setTemplate('main_acp');
        //	$TPL->addTag('cp_header', $this->cp_header);
        //	$TPL->addTag('cp_content', $this->output);
        //	if (!$STD->user['acp_users'])
        //		$TPL->addTag('ucp_style', "style='display:none'");
        //	else
        //		$TPL->addTag('ucp_style', "");
        //
        //	$TPL->addTag('site_url', $CFG['root_url']);
        
        //	require_once ROOT_PATH.'component/admin/adm_main.php';
        //
        //	component_adm_main::menus();

        //	$TPL->template = $this->output;
        $STD->template->display($this->output);
    }
    
    public function show_newsform()
    {
        global $IN, $STD, $CFG, $DB;

        $this->output = $STD->global_template->page_header('Add News Entry');
        $this->output .= $this->html->add_news($STD->make_form_token());
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function add_news()
    {
        global $IN, $STD;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The update request did not originate from this site, or your request has allready been processed.");
        }
        
        if (empty($IN['title']) || empty($IN['content'])) {
            $STD->error("A required field was not filled in.");
        }

        $NEWS = new news;
        
        $NEWS->data['uid'] = $STD->user['uid'];
        $NEWS->data['date'] = time();
        $NEWS->data['title'] = $IN['title'];
        
        // Parser
        require_once ROOT_PATH.'lib/parser.php';
        $parser = new parser;
        
        $IN['content'] = $parser->convert($IN['content']);

        if (strpos($IN['content'], "{%recent_updates%}") !== false) {
            $IN['content'] = preg_replace("/\{%recent_updates%\}/", $this->create_recent_updates(), $IN['content'], 1);
            $IN['content'] = preg_replace("/(<br\s*\/?>)*(<!--s_recent-->)/", "<br /><br /><!--s_recent-->", $IN['content']);
            $IN['content'] = preg_replace("/(<!--e_recent-->)(<br\s*\/?>|\r|\n)*/", "<!--e_recent--><br /><br />", $IN['content']);
            $NEWS->data['update_tag'] = 1;
        }
        
        $NEWS->data['message'] = $IN['content'];
        $NEWS->insert();
        
        // Done
        $message = "Your news entry has successfully been created.
					<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Entry Added');
        $this->output .= $this->html->message($message);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function edit_news()
    {
        global $IN, $STD;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
        
        // Order shenanigans
        $order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=news&param=03&st={$IN['st']}");
        
        $order_list = array('t' => 'n.title', 'u' => 'nu.username', 'd' => 'n.date');
        $order_default = array('d', 'd');
        
        $order = $STD->order_translate($order_list, $order_default);
        $order_links = $STD->order_links($order_list, $order_url, $order_default);
        
        // Prep Header
        //	$this->cp_header = 'Modify News';
        
        $this->output = $STD->global_template->page_header('Modify News');
        
        $this->output .= $this->html->edit_header($order_links);
            
        $NEWS = new news;
        $NEWS->query_use('n_user');
        $NEWS->query_order($order[0], $order[1]);
        $NEWS->query_limit($IN['st'], 30);
        $NEWS->getAll();
        
        while ($NEWS->nextItem()) {
            $data = $NEWS->data;
            $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=news&param=04&nid={$data['nid']}");
            $durl = $STD->encode_url($_SERVER['PHP_SELF'], "act=news&param=05&nid={$data['nid']}");
            $data['title'] = "<a href='$url'>{$data['title']}</a>";
            $data['author'] = $data['nu_username'];
            $data['date'] = $STD->make_date_short($data['date']);
            $data['delete'] = "<a href='$durl'>[Delete]</a>";
            
            $this->output .= $this->html->edit_row($data);
        }
        
        $count = $NEWS->countAll();
        $pages = $STD->paginate($IN['st'], $count['cnt'], 30, "act=news&param=03&o={$IN['o']}");
        
        $this->output .= $this->html->edit_footer($pages);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function edit_single_news()
    {
        global $IN, $STD;
        
        $NEWS = new news;
        $NEWS->query_use('n_user');
        
        if (!$NEWS->get($IN['nid'])) {
            $STD->error("Could not find news entry by that id.");
        }
        
        //	$this->cp_header = 'Modify News Entry';
        require_once ROOT_PATH.'lib/parser.php';
        $parser = new parser;
        
        $NEWS->data['message'] = $parser->unconvert($NEWS->data['message']);
        $NEWS->data['message'] = preg_replace("/<!--s_recent-->[\\x00-\\xFF]*<!--e_recent-->/", "{%recent_updates%}", $NEWS->data['message']);
        
        $this->output = $STD->global_template->page_header('Modify News Entry');
        
        $this->output .= $this->html->edit_entry($NEWS->data, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_edit_news()
    {
        global $IN, $STD;
        
        // Validation
        if (!$STD->validate_form($IN['security_token'])) {
            $STD->error("The update request did not originate from this site, or your request has allready been processed.");
        }

        $NEWS = new news;
        
        if (!$NEWS->get($IN['nid'])) {
            $STD->error("Could not find news entry by that id.");
        }
            
        $NEWS->data['title'] = $IN['title'];

        if (strpos($IN['content'], "{%recent_updates%}") !== false) {
            $IN['content'] = preg_replace("/\{%recent_updates%\}/", $this->create_recent_updates($NEWS->data['date']), $IN['content'], 1);
            $IN['content'] = preg_replace("/(<br\s*\/?>)*(<!--s_recent-->)/", "<br /><br /><!--s_recent-->", $IN['content']);
            $IN['content'] = preg_replace("/(<!--e_recent-->)(<br\s*\/?>|\r|\n)*/", "<!--e_recent--><br /><br />", $IN['content']);
            $NEWS->data['update_tag'] = 1;
        } else {
            $NEWS->data['update_tag'] = 0;
        }
        
        require_once ROOT_PATH.'lib/parser.php';
        $parser = new parser;
        
        $IN['content'] = $parser->convert($IN['content']);
        
        $NEWS->data['message'] = $IN['content'];
        $NEWS->update();
        
        // Done
        $message = "Your news entry has successfully been modified.
					<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->page_header('Entry Modified');
        
        $this->output .= $this->html->message($message);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_delete_news()
    {
        global $IN, $STD;
        
        $NEWS = new news;
        
        if (!$NEWS->get($IN['nid'])) {
            $STD->error("Could not find news entry by that id.");
        }
        
        $NEWS->remove();
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=news&param=03");
        $url = str_replace("&amp;", "&", $url);
        
        header("Location: $url");
        exit;
    }
    
    public function create_recent_updates($limit=0)
    {
        global $IN, $STD, $DB, $CFG;
        
        require_once ROOT_PATH.'lib/resource.php';
        
        // set up the main template to draw from
        $STPL = new template;
        $shtml = $STPL->useTemplate('main');
        $output = '';
        
        if (!$limit) {
            $limit = time();
        }
        
        // determine the span of time we must consider
        $NEWS = new news;
        $NEWS->query_order('n.date', 'DESC');
        $NEWS->query_limit(0, 1);
        $NEWS->query_condition("n.update_tag = '1'");
        $NEWS->query_condition("n.date < '$limit'");
        
        $NEWS->getAll();
        $NEWS->nextItem();

        $def_span = $limit-3600*24*$CFG['news_update_span'];
        
        if (!$NEWS) {
            $time = $def_span;
        } else {
            $time = max($NEWS->data['date'], $def_span);
        }

        // Get the module list.  We need them all.
        $cq = $DB->query("SELECT * FROM {$CFG['db_pfx']}_modules ORDER BY proc_order");

        $output = "<!--s_recent-->\n";
        $output .= $shtml->news_update_header();
        $additions = '';
        
        // We can't use our resource class here - too inefficent
        while ($row = $DB->fetch_row($cq)) {
            if ($row['custom_update'] == 1) {
                // This module is flagged for a custom updates format, so we have to pass the responsibility
                // onto it and hope for the best.
                require_once ROOT_PATH."component/modules/{$row['module_file']}";
            
                $module = new $row['class_name'];
            
                $additions .= $module->update_block($row, $time, $limit);
            } else {
                // Use the generic ram-saving display for this module.
                $additions .= $this->generic_update_block($row, $time, $limit, $shtml);
            }
        }
        
        if (!$additions) {
            $additions = $shtml->news_no_updates();
        }
        
        $output .= $additions;
        $output .= $shtml->news_update_footer();
        $output .= "<!--e_recent-->\n";
        
        return $output;
    }
    
    public function generic_update_block($module, $time, $limit, $shtml)
    {
        global $STD;
        
        $RES = new resource;
        $RES->module = $module;
        $RES->query_use('extention', $module['mid']);
        $RES->query_use('r_user');
        $RES->query_condition("r.accept_date >= '$time'");
        $RES->query_condition("r.accept_date < '$limit'");
        $RES->query_condition("r.queue_code = '0'");
        $RES->getByType($module['mid']);
            
        $num_items = 0;
        
        if ($module['news_show'] || $module['news_upd']) {
            $output = $shtml->news_gen_mod_header($module['full_name']);
        }
        
        if ($module['news_show']) {
            if (!$module['news_show_collapsed']) {
                $output .= $shtml->news_gen_block_header($module['full_name']);
            } else {
                $output .= $shtml->news_gen_block_header_col($module['full_name'], "nmod_{$module['mid']}_".time());
            }
                
            while ($RES->nextItem()) {
                $RES->data['url'] = "{%site_url%}?act=resdb&param=02&c={$RES->data['type']}&id={$RES->data['rid']}";
                $RES->data['username'] = $STD->format_username($RES->data, 'ru_', 1);
                    
                $output .= $shtml->news_gen_block_row($RES->data);
                $num_items++;
            }
            
            $output .= $shtml->news_gen_block_footer();
        }
        
        $num_upd = 0;
        
        if ($module['news_upd']) {
            // Now we have to see if there's updated items
            $RES->clear_condition();
            $RES->query_condition("r.update_accept_date >= '$time'");
            $RES->query_condition("r.update_accept_date < '$limit'");
            $RES->query_condition("r.accept_date < '$limit'");
            $RES->query_condition("r.queue_code = '0'");
            $RES->getByType($module['mid']);
 
            if (!$module['news_upd_collapsed']) {
                $upd = $shtml->news_gen_updblock_header($module['full_name']);
            } else {
                $upd = $shtml->news_gen_updblock_header_col($module['full_name'], "nmod_{$module['mid']}_".time());
            }
            
            while ($RES->nextItem()) {
                $RES->data['url'] = "{%site_url%}?act=resdb&param=02&c={$RES->data['type']}&id={$RES->data['rid']}";
                $RES->data['username'] = $STD->format_username($RES->data, 'ru_');
                
                $upd .= $shtml->news_gen_block_row($RES->data);
                $num_upd++;
            }
            
            $upd .= $shtml->news_gen_updblock_footer();
        }
        
        if ($module['news_show'] || $module['news_upd']) {
            $output .= $shtml->news_gen_mod_footer();
        }
        
        if (!$num_upd) {
            $upd = '';
        }
        
        $output .= $upd;
        
        if (!$num_items && !$num_upd) {
            $output = '';
        }
            
        return $output;
    }
}
?>
		
		