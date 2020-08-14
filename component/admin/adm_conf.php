<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/modq.php --
// Moderation Queue portion of ACP
//------------------------------------------------------------------

$component = new component_adm_conf;

class component_adm_conf
{
    public $html		= "";
    public $mod_html	= "";
    public $output		= "";
    
    public $cp_header	= '';
    
    public function init()
    {
        global $STD, $IN, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('adm_conf');
        
        if (!$STD->user['acp_super']) {
            $STD->error("You do not have access to this area of the ACP.");
        }
        
        switch ($IN['param']) {
            case 1:	$this->show_filter_group(); break;
            case 2:	$this->edit_filter_group(); break;
            case 3:	$this->do_edit_filter_group(); break;
            case 4:	$this->do_add_filter_entry(); break;
            case 5:	$this->do_edit_filter_list(); break;
            case 6:	$this->do_delete_filter_entry(); break;
        }

        $STD->template->display($this->output);
    }
    
    public function show_filter_group()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['st'])) {
            $IN['st'] = 0;
        }
        if (empty($IN['o'])) {
            $IN['o'] = null;
        }
        
        // Start Output
        $this->output .= $STD->global_template->page_header('Manage Filter Groups');
        
        $this->output .= $this->html->filter_group_header();
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group");
        
        while ($fg = $DB->fetch_row()) {
            $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=conf&param=02&gid={$fg['gid']}");
            $fg['name'] = "<a href='$url'>{$fg['name']}</a>";
            
            $this->output .= $this->html->filter_group_row($fg);
        }
        
        $this->output .= $this->html->filter_group_footer();
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function edit_filter_group()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['gid'])) {
            $STD->error("No Group ID provided.");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group WHERE gid = '{$IN['gid']}'");
        $gid = $DB->fetch_row();
        
        if (!$gid) {
            $STD->error("Invalid Group ID");
        }
        
        // Start Output
        $this->output = $STD->global_template->page_header('Manage Filter Group');
        
        $token = $STD->make_form_token();
        
        $this->output .= $this->html->filter_group_detail($gid, $token);
        $this->output .= $this->html->filter_list_header($gid, $token);
        
        // Get the members
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_list WHERE gid = '{$IN['gid']}' ORDER BY name");
        
        while ($fl = $DB->fetch_row()) {
            $keywords = explode(",", $fl['search_tags']);
            for ($x=0; $x<sizeof($keywords); $x++) {
                $keywords[$x] = preg_replace("/^[Ss]_/", "", $keywords[$x]);
            }
            $fl['search_tags'] = @join(",", $keywords);
            
            $this->output .= $this->html->filter_list_row($fl);
        }
        
        $this->output .= $this->html->filter_list_footer();
        
        $this->output .= $this->html->filter_list_add($gid, $token);
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_edit_filter_group()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['gid'])) {
            $STD->error("You cannot edit a non-existant filter group");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group WHERE gid = '{$IN['gid']}'");
        $fg = $DB->fetch_row();
        
        if (!$fg) {
            $STD->error("You cannot edit a non-existant filter group");
        }
        
        if (empty($IN['name'])) {
            $STD->error("You must provide a name for this group");
        }
            
        if (empty($IN['keyword'])) {
            $STD->error("You must provide a keyword for this group");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group WHERE keyword = '{$IN['keyword']}' ".
                   "AND gid <> '{$IN['gid']}'");
                   
        if ($DB->fetch_row()) {
            $STD->error("The keyword your provided is not unique.  Please use a different one");
        }
            
        $upd = $DB->format_db_update_values(array('name'	=> $IN['name'],
                                                  'keyword'	=> $IN['keyword']));
        $DB->query("UPDATE {$CFG['db_pfx']}_filter_group SET $upd WHERE gid = '{$IN['gid']}'");
        
        $dest = $STD->encode_url($_SERVER['PHP_SELF'], "act=conf&param=02&gid={$IN['gid']}");
        $dest = str_replace("&amp;", "&", $dest);
        header("Location: $dest");
        exit;
    }
    
    public function do_add_filter_entry()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['gid'])) {
            $STD->error("You cannot add filter entries to a non-existant group");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group WHERE gid = '{$IN['gid']}'");
        $fg = $DB->fetch_row();
        
        if (!$fg) {
            $STD->error("You cannot add filter entries to a non-existant group");
        }
        
        if (empty($IN['name'])) {
            $STD->error("You must provide a name for the entry");
        }
        
        $keywords = explode(",", $IN['keywords']);
        for ($x=0; $x<sizeof($keywords); $x++) {
            if (strlen($keywords[$x]) < 4 && !empty($keywords[$x])) {
                $keywords[$x] = "s_".$keywords[$x];
            }
        }
        $keywords = @join(",", $keywords);
        
        $IN['name'] = str_replace("&#39;", "'", $IN['name']);
        $IN['short_name'] = str_replace("&#39;", "'", $IN['short_name']);
        
        $ins = $DB->format_db_values(array('gid'			=> $IN['gid'],
                                           'name'			=> $IN['name'],
                                           'short_name'		=> $IN['short_name'],
                                           'search_tags'	=> $keywords));
        $DB->query("INSERT INTO {$CFG['db_pfx']}_filter_list ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $dest = $STD->encode_url($_SERVER['PHP_SELF'], "act=conf&param=02&gid={$IN['gid']}");
        $dest = str_replace("&amp;", "&", $dest);
        header("Location: $dest");
        exit;
    }
    
    public function do_edit_filter_list()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['gid'])) {
            $STD->error("No filter group selected");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_group WHERE gid = '{$IN['gid']}'");
        $fg = $DB->fetch_row();
        
        if (!$fg) {
            $STD->error("Invalid filter group selected");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_list WHERE gid = '{$IN['gid']}'");
        
        $updar = array();
        while ($fl = $DB->fetch_row()) {
            $fid = $fl['fid'];
            if (!isset($IN['name'][$fid])) {
                continue;
            }
            
            // Keywords
            $keywords = explode(",", $IN['keywords'][$fid]);
            for ($x=0; $x<sizeof($keywords); $x++) {
                if (strlen($keywords[$x]) < 4 && !empty($keywords[$x])) {
                    $keywords[$x] = "s_".$keywords[$x];
                }
            }
            $keywords = @join(",", $keywords);
            
            $IN['name'][$fid] = str_replace("&#39;", "'", $IN['name'][$fid]);
            $IN['short_name'][$fid] = str_replace("&#39;", "'", $IN['short_name'][$fid]);
            
            if ($IN['name'][$fid] == $fl['name'] && $IN['short_name'][$fid] == $fl['short_name'] &&
                strtolower($keywords) == strtolower($fl['search_tags'])) {
                continue;
            }
            
            $upd = $DB->format_db_update_values(array('name'		=> $IN['name'][$fid],
                                                      'short_name'	=> $IN['short_name'][$fid],
                                                      'search_tags'	=> $keywords));
            $DB->query("UPDATE {$CFG['db_pfx']}_filter_list SET $upd WHERE fid = '{$fid}'");
        }
        
        $dest = $STD->encode_url($_SERVER['PHP_SELF'], "act=conf&param=02&gid={$IN['gid']}");
        $dest = str_replace("&amp;", "&", $dest);
        header("Location: $dest");
        exit;
    }
    
    public function do_delete_filter_entry()
    {
        global $IN, $STD, $DB, $CFG;
        
        if (empty($IN['fid'])) {
            $STD->error("No filter entry selected");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_filter_list WHERE fid = '{$IN['fid']}'");
        $fl = $DB->fetch_row();
        
        if (!$fl) {
            $STD->error("Invalid filter entry selected");
        }
        
        $DB->query("DELETE FROM {$CFG['db_pfx']}_filter_multi WHERE fid = '{$IN['fid']}'");
        
        $DB->query("DELETE FROM {$CFG['db_pfx']}_filter_list WHERE fid = '{$IN['fid']}'");
        
        $dest = $STD->encode_url($_SERVER['PHP_SELF'], "act=conf&param=02&gid={$fl['gid']}");
        $dest = str_replace("&amp;", "&", $dest);
        header("Location: $dest");
        exit;
    }
    
    public function rewrite_settings($settings)
    {
        global $STD;
        
        $data = "<?php\n\n\$CFG = ";
        $data .= $this->build_array($settings);
        $data .= ";\n\n?>";
        
        $fh = fopen(ROOT_PATH.'settings.php', 'w');
        if (!$fh) {
            return false;
        }
            
        fwrite($fh, $data);
        fclose($fh);
        
        return true;
    }
    
    public function build_array($arr, $nesting=1)
    {
        $data = "array(\n";
        
        $max_len = 0;
        foreach ($arr as $k => $v) {
            if (strlen($k) > $max_len) {
                $max_len = strlen($k);
            }
        }
        $max_len += 2;
        
        foreach ($arr as $k => $v) {
            $data .= str_repeat("\t", $nesting) . str_pad("'$k'", $max_len) . " => ";
            if (is_array($v)) {
                $data .= $this->build_array($v, $nesting+1) . ",\n";
            } elseif (is_int($v)) {
                $data .= "$v,\n";
            } else {
                $data .= "\"" . addslashes($v) . "\",\n";
            }
        }
        
        $data .= str_repeat("\t", $nesting - 1) . ")";
        
        return $data;
    }
}
