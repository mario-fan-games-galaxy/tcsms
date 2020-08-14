<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/submit.php --
// handles new submissions and rules display
//------------------------------------------------------------------

$component = new component_submit;

class component_submit
{
    public $html		= "";
    public $mod_html	= "";
    public $output		= "";
    
    public function init()
    {
        global $STD, $IN, $session, $SAJAX;
        
        $this->html = $STD->template->useTemplate('submit');
        
        //$STD->sajax->sajax_allow("component_submit__show_form_page");
        
        switch ($IN['param']) {
            case 1: $this->show_submission_rules(); break;
            case 2: $this->show_submit_form(); break;
            case 3: $this->do_submit(); break;
        }
        
        //	$TPL->template = $this->output;
        $STD->template->display($this->output);
    }
    
    public function show_submission_rules($show_extra=0)
    {
        global $IN, $STD;
        
        $rules = file_get_contents(ROOT_PATH.'component/include/submit_rules.txt');
        
        (!$show_extra)
            ? $show_extra = "display:none"
            : $show_extra = "";
        
        $extra = '';
        if (!empty($IN['c'])) {
            $extra .= "&c={$IN['c']}";
        }
        if (!empty($IN['gid'])) {
            $extra .= "&gid={$IN['gid']}";
        }

        $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=submit&param=02$extra");
            
        $this->output .= $STD->global_template->page_header('Submission Rules');
        
        $this->output .= $this->html->rules($url, $rules, $show_extra);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    // AJAX STRING show_form_page(INT cat)
    //
    // AJAX-callable routine that produces a particular submit form for the submit page.
    
    public function show_form_page($cat)
    {
        global $IN, $STD;
        
        $this->html = $STD->template->useTemplate('submit');
        
        $IN['c'] = $cat;
        
        //	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE mid = '$cat'");
        //	$mod = $DB->fetch_row();
        $mod = $STD->modules->get_module($IN['c']);
        if (!$mod) {
            return $this->html->invalid_module();
        }
        
        if (!file_exists(ROOT_PATH.'component/modules/'.$mod['module_file'])) {
            return $this->html->invalid_module();
        }
        
        require_once ROOT_PATH.'component/modules/'.$mod['module_file'];
        $module = new $mod['class_name'];
        $module->init();
        
        //	$data = $module->user_submit_view_prep($cat);
        $data = $module->submit_prep_data();

        $this->mod_html = $STD->template->useTemplate($mod['template']);
        
        return $this->mod_html->submit_form($data, $module->get_max_sizes());
    }
    
    public function show_submit_form()
    {
        global $IN, $STD, $session, $SAJAX;
        
        if (!$STD->user['can_submit']) {
            $STD->error("You do not have permission to submit new files to the site.");
        }
                                
        // If user is making first submission, force-feed the rules
        if (!$STD->user['first_submit'] && empty($IN['rules_agree']) && empty($IN['rules_continue'])) {
            $this->show_submission_rules(1);
            return;
        } elseif (!$STD->user['first_submit'] && empty($IN['rules_agree']) && !empty($IN['rules_continue'])) {
            $STD->error("You must check the box at the bottom of the page to agree to the rules before submitting.  You will not be required to do this again after you complete your first submission.");
        }
        
        // Generate the necessary HTML before tagging
        //	$STD->sajax->sajax_export("component_submit__show_form_page");
        //	$STD->sajax->sajax_handle_client_request();
        
        $def_disp['module'] = '';
        $def_disp['style']  = 'display:none';
        $def_disp['astyle'] = '';
        $type = 0;
        
        // Do we need to auto-expand?
        if (!empty($IN['c'])) {
            $def_disp['module'] = $this->show_form_page($IN['c']);
            $def_disp['style'] = '';
            $def_disp['astyle'] = 'display:none';
            $type = $IN['c'];
        }
        
        $session->touch_data('err_save');
        if (!empty($session->data['err_save'])) {
            $errdat = $session->data['err_save'];
            if ($errdat == 'submit') {
                $def_disp['module'] = $this->show_form_page($errdat['c']);
                $def_disp['style']  = '';
                $def_disp['astyle'] = 'display:none';
                $type = $errdat['c'];
            }
        }
        
        $type_list = "<option value=\"0\" onClick=\"javascript:hide('page');show('select_page');\"> ---</option>\n";
        
        $STD->modules->load_module_list();
        reset($STD->modules->module_set);
        $hidden_page = 0;
        while (list(, $row) = each($STD->modules->module_set)) {
            ($row['mid'] == $type)
                ? $sel = "selected='selected'"
                : $sel = '';
            
            if ($row['mid'] == $type && $row['hidden']) {
                $type_list = "<input type=\"hidden\" name=\"c\" value=\"{$row['mid']}\">\n{$row['full_name']}\n";
                $hidden_page = 1;
                break;
            } elseif (!$row['hidden']) {
                $type_list .= "<option value=\"{$row['mid']}\" $sel> {$row['full_name']}</option>\n";
            }
        }
        
        if (!$hidden_page) {
            $type_list = $this->html->type_select($type_list);
        }
            
        $url1 = $STD->encode_url($_SERVER['PHP_SELF'], "act=submit&param=02");
        $url2 = $STD->encode_url($_SERVER['PHP_SELF'], 'act=submit&param=03');
        
        //------------------------------------------------
        // Output
        //------------------------------------------------
        
        $this->output .= $STD->global_template->page_header('Submit Files');
        
        $urlparts = array('form1'	=> $url1,
                          'form2'	=> $url2,
                          'param1'	=> '02',
                          'sess'	=> '');
        
        if ($session->sess_id) {
            $urlparts['sess'] = "<input type=\"hidden\" name=\"sess\" value=\"" . $session->sess_id . "\" />\n";
        }
        if (!empty($IN['rules_agree'])) {
            $urlparts['sess'] .= "<input type=\"hidden\" name=\"rules_agree\" value=\"1\" />\n";
            $urlparts['sess'] .= "<input type=\"hidden\" name=\"rules_continue\" value=\"1\" />";
        }
        
        $this->output .= $this->html->submit_page($urlparts, $STD->make_form_token(), $type_list, $def_disp);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_submit()
    {
        global $IN, $STD, $TAG;
        
        if (!$STD->validate_form($IN['security_token'])) {
            $recovery = "<pre>".print_r($IN, 1)."</pre>";
            $recovery = "<div id='rec_data' style='display:none; margin-left:20px'>{$recovery}</div>";
            
            $STD->error("The submission request did not originate from this site, or you attempted to repeat a completed transaction.
						 <p align='center'><a href=\"javascript:show_hide('rec_data');\">Click here to recover your submission data.</a></p>{$recovery}");
        }
        
        if (!$STD->user['can_submit']) {
            $STD->error("You do not have permission to submit new files to the site.");
        }

        //	$mid = $IN['c'];
        $module = $STD->modules->new_module($IN['c']);
        if (!$module) {
            $STD->error("Suitable module could not be found.");
        }
        //	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE mid = '$mid'");

        //	if ($DB->get_num_rows() < 1)
        //		$STD->error("Suitable module could not be found.");
        //	$mrow = $DB->fetch_row();
            
        //	require_once ROOT_PATH.'component/modules/'.$mrow['module_file'];
        //	$module = new $mrow['class_name'];
        
        // Raw Cleaning (Remember to undo before display!)
        if (isset($IN['title'])) {
            $IN['title'] = $STD->rawclean_value($_POST['title']);
        }
        
        $module->init();
        
        $module->user_submit_data_check();
    
        $RES = $module->user_update_submit_data();
        
        // Quickly do a user check
        if (!$STD->user['first_submit']) {
            $STD->user['first_submit'] = 1;
            $STD->userobj->update();
        }

        $username = htmlspecialchars($STD->user['username']);
        $url = $STD->encode_url($_SERVER['PHP_SELF']);
        $message = "Thank you, $username.  Your submission has been successfully put 
			into our moderation queue.  In the next few days your submission will be reviewed for 
			placement on the site.  You will receive a message via your personal messenger when 
			this happens.
			<p align='center'><a href='$url'>Return to the main page</a></p>";
        
        //	$this->output .= $this->html->page_header();
        
        $this->output .= $STD->global_template->message($message);
        
        //	$this->output .= $this->html->page_footer();
        
        $STD->clear_form_token();
    }
}
