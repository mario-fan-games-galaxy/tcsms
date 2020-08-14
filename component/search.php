<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/search.php --
// Handles searching
//------------------------------------------------------------------

$component = new component_search;

class component_search
{
    public $html		= "";
    public $mod_html	= "";
    public $output		= "";
    
    public function init()
    {
        global $IN, $STD;
        
        $this->html = $STD->template->useTemplate('search');
        
        //$STD->sajax->sajax_allow("component_search__show_form_page");
        
        switch ($IN['param']) {
            case 1: $this->show_advanced(); break;
            case 2: $this->simple_search(); break;
            case 3: $this->advanced_search(); break;
        }
        
        //$TPL->template = $this->output;
        $STD->template->display($this->output);
    }
    
    // AJAX STRING show_form_page(INT cat)
    //
    // AJAX-callable routine that produces a list of constraints for the selected module
    
    public function show_form_page($cat)
    {
        global $IN, $STD, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('search');
        
        $IN['c'] = $cat;
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE mid = '$cat'");
        $mod = $DB->fetch_row();
        if (!$mod) {
            return $this->html->invalid_module();
        }
        
        $DB->query("SELECT f.fid,f.gid,f.name,g.name AS group_name,g.keyword FROM {$CFG['db_pfx']}_filter_list f ".
                   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_group g ON (f.gid = g.gid) ".
                   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_use u ON (u.gid = g.gid) ".
                   "WHERE u.mid = {$mod['mid']}");
        
        $groups = array();
        while ($row = $DB->fetch_row()) {
            $gid = $row['gid'];
            if (!isset($groups[$gid])) {
                $groups[$gid] = array('narr' => array('---'), 'varr' => array(0), 'gn' => $row['group_name']);
            }
            
            $groups[$gid]['narr'][] = $row['name'];
            $groups[$gid]['varr'][] = $row['fid'];
        }
        
        $output = '';
        while (list($k, $v) = each($groups)) {
            $box = $STD->make_select_box("filter[$k]", $v['varr'], $v['narr'], '', 'selectbox');
            $output .= $this->html->constraint_row($v['gn'], $box);
        }

        return $this->html->constraint_block($output);
    }
    
    public function show_advanced()
    {
        global $IN, $STD, $DB, $CFG;
        
        // Generate the necessary HTML before tagging
        //$STD->sajax->sajax_export("component_search__show_form_page");
        //$STD->sajax->sajax_handle_client_request();
        
        // Get Module List
        $type_list = "<select name=\"c\" size=\"1\" class=\"selectbox\">\n";
        $type_list .= "<option value=\"0\">All Modules</option>\n";
        
        $DB->query("SELECT mid,full_name,hidden FROM {$CFG['db_pfx']}_modules");
        while ($row = $DB->fetch_row()) {
            if ($row['hidden'] == 1) {
                $row['full_name'] .= '*';
            }
            $type_list .= "<option value=\"{$row['mid']}\"> {$row['full_name']}</option>\n";
        }
        
        $type_list .= "</select>\n";
        
        $form_fields = array();
        $form_fields['type_list'] = $type_list;
        
        $days_v = array('0','1','2','7','30','90','180','365');
        $days_n = array('Any Date','Today','Yesterday','7 Days','30 Days','90 Days','180 Days','365 Days');
        $form_fields['date'] = $STD->make_select_box('d', $days_v, $days_n, '0', 'selectbox');
        $form_fields['date_dir'] = $STD->make_select_box('dd', array('0','1'), array('And Newer','And Older'), '0', 'selectbox');
        
        // Output
        $this->output = $STD->global_template->page_header('Advanced Search');
        
        $this->output .= $this->html->advanced_search($form_fields);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function get_boolean($term)
    {
        if (preg_match("/&quot;|\*|\+|\-|\"/", $term)) {
            return 1;
        }
        
        return 0;
    }
    
    public function get_rawterms($term)
    {
        global $STD;
        
        $term = $STD->rawclean_value($term);
        
        preg_match_all("/\".+\"|[\w_\'\+\*-]+/", $term, $string, PREG_PATTERN_ORDER);
        
        return $string[0];
    }
    
    public function get_search_string($term)
    {
        global $STD;

        // Format a search string
        $fstring = '';
        foreach ($term as $v) {
            if (strlen($v) < 4) {
                $fstring .= " s_$v";
            } else {
                $fstring .= " $v";
            }
        }
        
        return $fstring;
    }
    
    public function format_results($data, $rawterms, $relevance)
    {
        global $STD;

        $data['author'] = $STD->format_username($data, 'ru_');
        $data['relevance'] = (int)($data['relevance'] / $relevance * 100) . '%';
        
        foreach ($rawterms as $v) {
            if (empty($v) or strlen($v) < 2) {
                continue;
            }
            
            $v = str_replace("\"", "", $v);
            $v = preg_replace("/^\+/", "", $v);
            $v = preg_replace("/^\-/", "", $v);
            $v = str_replace("*", "\w*", $v);
            
            $pattern = "/({$v})/i";
            if (strlen($v) < 4) {
                $pattern = "/(^({$v})(?=[^\w\d])|(?<=[^\w\d])({$v})(?=[^\w\d])|(?<=[^\w\d])({$v})$)/i";
            }
            
            $data['title'] = preg_replace($pattern, "<span class='search_hl'>\\1</span>", $data['title']);
            $data['description'] = preg_replace($pattern, "<span class='search_hl'>\\1</span>", $data['description']);
        }
        
        return $data;
    }
    
    public function simple_search()
    {
        global $IN, $STD;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        require ROOT_PATH.'lib/resource.php';
        
        if (empty($IN['search'])) {
            $STD->error("You must enter a search string");
        }
        
        $boolean = $this->get_boolean($IN['search']);
        $string = $this->get_rawterms($_GET['search']);
        
        $fstring = $this->get_search_string($string);
        
        // We can do a query now
        $RES = new resource;
        $RES->query_use('r_user');
        $RES->query_use('module');
        $RES->query_condition("r.queue_code = 0");
        $RES->query_condition("m.hidden = 0");
        
        $count_rel = $RES->full_text_search_count($fstring, $boolean);
        $count = $count_rel['cnt'];
        $relevance = $count_rel['relevance'];
        
        if ($count == 0 && $boolean == 0) {
            $boolean = 1;
            $count_rel = $RES->full_text_search_count($fstring, $boolean);
            $count = $count_rel['cnt'];
            $relevance = $count_rel['relevance'];
        }

        $RES->query_limit($IN['st'], $STD->get_page_prefs());
        $RES->full_text_search_all($fstring, $boolean);

        $this->output .= $STD->global_template->page_header('Search Results');
        $this->output .= $this->html->simple_results_header();

        while ($RES->nextItem()) {
            $data = $this->format_results($RES->data, $string, $relevance);
            
            $this->output .= $this->html->simple_results_row($data);
        }

        if ($count == 0) {
            $this->output .= $this->html->simple_no_results();
        }

        $pages = $STD->paginate($IN['st'], $count, $STD->get_page_prefs(), "act=search&param=02&search={$IN['search']}");

        $this->output .= $this->html->simple_results_footer($pages);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function advanced_search()
    {
        global $IN, $STD;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
            
        require ROOT_PATH.'lib/resource.php';
        
        if (empty($IN['search']) && empty($IN['m'])) {
            $STD->error("You must enter a search string or a member name");
        }
        
        if (!empty($IN['search'])) {
            $boolean = $this->get_boolean($IN['search']);
            if (isset($_POST['search'])) {
                $string = $this->get_rawterms($_POST['search']);
            } else {
                $string = $this->get_rawterms($_GET['search']);
            }
        
            $fstring = $this->get_search_string($string);
        } else {
            $boolean = 1;
            $string = array();
        }
        
        // We can do a query now
        $RES = new resource;
        $RES->query_use('r_user');
        $RES->query_use('module');
        $RES->query_condition("r.queue_code = 0");
        
        
        // member stuff
        if (!empty($IN['m'])) {
            $user = $IN['m'];
            if (empty($IN['me'])) {
                $user = "%{$IN['m']}%";
            }
            $RES->query_condition("(ru.username LIKE '{$user}' OR r.author_override LIKE '{$user}')");
        }
        
        // other constraints
        if (!empty($IN['c'])) {
            $RES->query_condition("r.type = '{$IN['c']}'");
        }
        
        if (empty($IN['sh']) && !empty($IN['c'])) {
            $RES->query_condition("(m.hidden = 0 OR r.type = '{$IN['c']}')");
        } elseif (!empty($IN['c'])) {
            $RES->query_condition("r.type = '{$IN['c']}'");
        } elseif (empty($IN['sh'])) {
            $RES->query_condition("m.hidden = 0");
        }
        
        if (!empty($IN['d'])) {
            $d = gmdate("j", time());
            $m = gmdate("n", time());
            $y = gmdate("Y", time());
            $base_time = gmmktime(0, 0, 0, $m, $d, $y);
            
            $factor = $IN['d'];
            if ($IN['d'] <= 2) {
                $factor--;
            }
                
            $base_time -= $factor*24*3600;

            if (empty($IN['dd'])) {
                $RES->query_condition("r.created > '$base_time'");
            } else {
                $base_time += 3600*24;
                $RES->query_condition("r.created < '$base_time'");
            }
        }
        
        // Get count
        if (!empty($IN['search'])) {
            $count_rel = $RES->full_text_search_count($fstring, $boolean);
        } else {
            $count_rel = $RES->like_search_count('');
        }
        
        $count = $count_rel['cnt'];
        $relevance = $count_rel['relevance'];
        
        if ($count == 0 && $boolean == 0) {
            $boolean = 1;
            $count_rel = $RES->full_text_search_count($fstring, $boolean);
            $count = $count_rel['cnt'];
            $relevance = $count_rel['relevance'];
        }
        
        $this->output .= $STD->global_template->page_header('Search Results');
        $this->output .= $this->html->simple_results_header();
            
        if ($count > 0) {
            $RES->query_limit($IN['st'], $STD->get_page_prefs());
            
            if (!empty($IN['search'])) {
                $RES->full_text_search_all($fstring, $boolean);
            } else {
                $RES->like_search_all('');
            }

            while ($RES->nextItem()) {
                $data = $this->format_results($RES->data, $string, $relevance);
                
                $this->output .= $this->html->simple_results_row($data);
            }
        } else {
            $this->output .= $this->html->simple_no_results();
        }
        
        if (empty($IN['search'])) {
            $IN['search'] = '';
        }
        if (empty($IN['m'])) {
            $IN['m'] = '';
        }
        if (empty($IN['me'])) {
            $IN['me'] = '0';
        }
        if (empty($IN['c'])) {
            $IN['c'] = '0';
        }
        if (empty($IN['sh'])) {
            $IN['sh'] = '0';
        }
        if (empty($IN['d'])) {
            $IN['d'] = '0';
        }
        if (empty($IN['dd'])) {
            $IN['dd'] = '0';
        }
        
        $page_url = "act=search&param=03&search={$IN['search']}&m={$IN['m']}&me={$IN['me']}&c={$IN['c']}&sh={$IN['sh']}&d={$IN['d']}&dd={$IN['d']}";
        $pages = $STD->paginate($IN['st'], $count, $STD->get_page_prefs(), $page_url);

        $this->output .= $this->html->simple_results_footer($pages);
        $this->output .= $STD->global_template->page_footer();
    }
    
    /*	function advanced_search () {
            global $IN, $STD;

            if (empty($IN['st']))
                $IN['st'] = 0;

            require ROOT_PATH.'lib/resource.php';

            if ( empty( $IN['search'] ) )
                $STD->error("You must enter a search string");

            $boolean = 0;

            if ( preg_match( "/&quot;|\*|\+|\-/", $IN['search'] ) )
                $boolean = 1;

            $IN['search'] = str_replace( "&quot;", "\"", $IN['search']);

            preg_match_all( "/\".+\"|[\w_'\+\*-]+/", $IN['search'], $string, PREG_PATTERN_ORDER);
            $string = $string[0];

            // Format a search string
            $fstring = "";
            while ( list(,$v) = each( $string ) ) {
                if (strlen($v) < 4)
                    $fstring .= " s_$v";
                else
                    $fstring .= " $v";
            }

            // We can do a query now
            $RES = new resource;
            $RES->query_use( 'r_user' );
            $RES->query_use( 'module' );
            $RES->query_condition( "r.queue_code = 0" );

            $count = $RES->full_text_search_count( $fstring, $boolean );

            $RES->full_text_search_all( $fstring, $boolean );

            $this->output .= $STD->global_template->page_header('Search Results');
            $this->output .= $this->html->simple_results_header();

            $relevance = 0;

            while ( $RES->nextItem() ) {
                if ($relevance == 0)
                    $relevance = $RES->data['relevance'];
                if ($relevance < 1)
                    $relevance = 1;

                $data = $RES->data;
                $data['author'] = $STD->format_username( $data, 'ru_' );
                $data['relevance'] = (int)($data['relevance'] / $relevance * 100) . '%';

                reset($string);
                while ( list(,$v) = each ($string) ) {
                    if ( empty($v) )
                        continue;

                    $v = str_replace( "\"", "", $v );
                    $v = preg_replace( "/^\+/", "", $v );
                    $v = preg_replace( "/^\-/", "", $v );
                    $v = str_replace( "*", "\w*", $v );

                    $data['title'] = preg_replace( "/({$v})/i", "<span class='search_hl'>\\1</span>", $data['title'] );
                    $data['description'] = preg_replace( "/({$v})/i", "<span class='search_hl'>\\1</span>", $data['description'] );
                }

                $this->output .= $this->html->simple_results_row( $data );
            }

            $pages = $STD->paginate( $IN['st'], $count, $STD->get_page_prefs(), "act=search&param=02&search={$IN['search']}" );

            $this->output .= $this->html->simple_results_footer( $pages );
            $this->output .= $STD->global_template->page_footer();
        }*/
}
