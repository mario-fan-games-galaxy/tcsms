<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/admin/panel.php --
// Panel Manager
//------------------------------------------------------------------

$component = new component_adm_panel;

class component_adm_panel
{
    public $html		= "";
    public $mod_html	= "";
    public $output		= "";
    
    public $cp_header	= '';
    
    public function init()
    {
        global $STD, $IN, $DB, $CFG;
        
        $this->html = $STD->template->useTemplate('adm_panel');
        
        //if (!$STD->userobj->check_rights('ACP_MODQ'))
        //	$STD->error("You do not have access to this area of the ACP.");
        
        switch ($IN['param']) {
            case  1:	$this->show_panel_list(); break;
            case  2:	$this->hide_unhide(); break;
            case  3:	$this->move_panel(); break;
            case  4:	$this->edit_panel(); break;
        }

        $STD->template->display($this->output);
    }
    
    public function show_panel_list()
    {
        global $STD, $DB, $CFG;
        
        $this->output .= $STD->global_template->page_header("Panel Management");
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_panels");
        
        // Get rows ahead of time and count some stats
        $rows = array();
        $maxes = array('L' => 0, 'R' => 0, 'C' => 0, 'U' => 0, 'D' => 0, 'US' => array(), 'DS' => array());
        while ($pr = $DB->fetch_row()) {
            $rows[] = $pr;
            if ($pr['row'] > $maxes[$pr['column']]) {
                $maxes[$pr['column']] = $pr['row'];
            }
        }
        
        // Some constants
        $icon_vis = "<img src='{$STD->tags['image_path']}/icon_visible.png' class='click_button' alt='Hide' title='Click to hide panel' />";
        $icon_vislock = "<img src='{$STD->tags['image_path']}/icon_visible_lock.png' class='dis_button' alt='--' title='This panel cannot be hidden' />";
        $icon_invis = "<img src='{$STD->tags['image_path']}/icon_hidden.png' class='click_button' alt='Show' title='Click to reveal panel' />";
        $icon_x = "<img src='{$STD->tags['image_path']}/icon_x.png' class='click_button' alt='X' title='Delete Panel' />";
        
        // Let's build our current preview so the rest of the form makes more sense
        $this->output .= $this->html->panel_preview_header();
        
        $sections = $this->build_panel_preview($rows);
        
        $this->output .= $this->html->panel_preview_region($sections);
        $this->output .= $this->html->panel_preview_footer();
        
        $this->output .= $this->html->panel_list_header();
        
        foreach ($rows as $pr) {
            // Some more constants
            $vis_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=02&id={$pr['pid']}");
            $edit_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=04&pid={$pr['pid']}");
            
            // Deletable?
            $pr['delete_icon'] = ($pr['can_delete'] == 1) ? $icon_x : "";
                
            // Hidden?
            $pr['hidden_icon'] = ($pr['visible'] == 1) ? $icon_vis : $icon_invis;
            $pr['hidden_icon'] = ($pr['can_hide'])
                ? "<a href='$vis_url'>{$pr['hidden_icon']}</a>"
                : $icon_vislock;
                
            // Move and location
            $pr['move_icon'] = $this->panel_list_moves($pr, $maxes);
            // Fusion
            $pr['fuse_icon'] = '';
            // Editing
            $pr['edit'] = "<a href='$edit_url'>[Edit]</a>";
                
            $this->output .= $this->html->panel_list_row($pr);
        }
        
        $this->output .= $this->html->panel_list_footer();
        
        // Some global settings
        $options = array();
        $options['expand'] = $STD->make_yes_no('expand', $CFG['panel_expand']);
        $options['maximize'] = $STD->make_yes_no('maximize', $CFG['panel_maximize']);
        
        $this->output .= $this->html->panel_man($options);
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function build_panel_preview($rows)
    {
        global $STD, $CFG;
        
        $sections = array('L' => array(), 'R' => array(), 'C' => array(), 'U' => array(), 'D' => array());
        
        // Expandable content?
        if ($CFG['panel_maximize']) {
            $max_icon = "<img src='{$STD->tags['image_path']}/icon_expand.png' />";
        } else {
            $max_icon = '';
        }
            
        foreach ($rows as $pr) {
            if ($pr['visible'] == 0) {
                continue;
            }
                
            if (in_array($pr['column'], array('R', 'C', 'L'))) {
                if ($pr['panel_type'] == 'content') {
                    $sections[$pr['column']][$pr['row']] = $this->html->panel_content_box($pr['panel_name'], $max_icon);
                } else {
                    $sections[$pr['column']][$pr['row']] = $this->html->panel_preview_box($pr['panel_name']);
                }
            } else {
                if (!isset($sections[$pr['column']][$pr['row']])) {
                    $sections[$pr['column']][$pr['row']] = array();
                }
                $sections[$pr['column']][$pr['row']][$pr['strip_order']] = $this->html->panel_preview_strip($pr['panel_name']);
            }
        }
        
        // Sort accordingly
        $sections['U'] = array_map("keysort", $sections['U']);
        $sections['D'] = array_map("keysort", $sections['D']);
        $sections = array_map("keysort", $sections);
        
        // Some pre-building
        $col_count = 3;
        $center_width = 60;
        
        if ($CFG['panel_expand'] && empty($sections['L'])) {
            $col_count--;
            $center_width += 20;
        }
        if ($CFG['panel_expand'] && empty($sections['R'])) {
            $col_count--;
            $center_width += 20;
        }
        
        $columns = array(
            'L'	=> "<td style=\"padding:8px; width:20%; vertical-align:top\">{$sections['L']}</td>",
            'C'	=> "<td style=\"padding:8px; width:{$center_width}%; vertical-align:top\">{$sections['C']}</td>",
            'R' => "<td style=\"padding:8px; width:20%; vertical-align:top\">{$sections['R']}</td>",
        );
        
        if ($CFG['panel_expand'] && empty($sections['L'])) {
            $columns['L'] = '';
        }
        if ($CFG['panel_expand'] && empty($sections['R'])) {
            $columns['R'] = '';
        }
        
        $sections['M'] = @join('', $columns);
        $sections['columns'] = $col_count;
        
        return $sections;
    }
    
    public function panel_list_moves($ple, $maxes)
    {
        global $STD;
        
        $move_urls = array(
            'l'		=> $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=03&dir=l&id={$ple['pid']}"),
            'r'		=> $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=03&dir=r&id={$ple['pid']}"),
            'u'		=> $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=03&dir=u&id={$ple['pid']}"),
            'd'		=> $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=03&dir=d&id={$ple['pid']}"),
        );
        
        $avail_icons = array(
            'l'		=> "<img src='{$STD->tags['image_path']}/icon_left.png' class='click_button' alt='<' title='Move Left' />",
            'ld'	=> "<img src='{$STD->tags['image_path']}/icon_left_d.png' class='dis_button' alt='x' />",
            'r'		=> "<img src='{$STD->tags['image_path']}/icon_right.png' class='click_button' alt='>' title='Move Right' />",
            'rd'	=> "<img src='{$STD->tags['image_path']}/icon_right_d.png' class='dis_button' alt='x' />",
            'u'		=> "<img src='{$STD->tags['image_path']}/icon_up.png' class='click_button' alt='^' title='Move Up' />",
            'ud'	=> "<img src='{$STD->tags['image_path']}/icon_up_d.png' class='dis_button' alt='x' />",
            'd'		=> "<img src='{$STD->tags['image_path']}/icon_down.png' class='click_button' alt='v' title='Move Down' />",
            'dd'	=> "<img src='{$STD->tags['image_path']}/icon_down_d.png' class='dis_button' alt='x' />",
        );
        
        $avail_icons['l'] = "<a href='{$move_urls['l']}'>{$avail_icons['l']}</a>";
        $avail_icons['r'] = "<a href='{$move_urls['r']}'>{$avail_icons['r']}</a>";
        $avail_icons['u'] = "<a href='{$move_urls['u']}'>{$avail_icons['u']}</a>";
        $avail_icons['d'] = "<a href='{$move_urls['d']}'>{$avail_icons['d']}</a>";
        
        $icons = array(
            'up'	=> $avail_icons['u'],
            'down'	=> $avail_icons['d'],
            'left'	=> $avail_icons['l'],
            'right'	=> $avail_icons['r'],
        );
        
        // Standard Column L/R
        
        if ($ple['column'] == 'L') {
            $icons['left'] = $avail_icons['ld'];
        } elseif ($ple['column'] == 'R') {
            $icons['right'] = $avail_icons['rd'];
        } elseif ($ple['column'] == 'C' && $ple['can_column_lr'] == 0) {
            $icons['left'] = $avail_icons['ld'];
            $icons['right'] = $avail_icons['rd'];
        }
        
        // Strip L/R
        if ($ple['column'] == 'U' || $ple['column'] == 'D') {
            if ($ple['strip_order'] == 1) {
                $icons['left'] = $avail_icons['ld'];
            }
            $icons['right'] = $avail_icons['rd'];
        }
        
        // Up / Down
        if ($ple['row'] == 1 && !$ple['can_strip']) {
            $icons['up'] = $avail_icons['ud'];
        }
        if ($ple['row'] == $maxes[$ple['column']] && !$ple['can_strip']) {
            $icons['down'] = $avail_icons['dd'];
        }
        
        // Strip U/D
        if ($ple['column'] == 'U' && $ple['row'] == 1) {
            $icons['up'] = $avail_icons['ud'];
        }
        if ($ple['column'] == 'D' && $ple['row'] == $maxes['D']) {
            $icons['down'] = $avail_icons['dd'];
        }
        
        return @join('', $icons);
    }
    
    public function hide_unhide()
    {
        global $STD, $IN, $DB, $CFG;
        
        if (empty($IN['id'])) {
            $STD->error("No panel selected to show or hide.");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_panels WHERE pid = '{$IN['id']}'");
        
        $row = $DB->fetch_row();
        if (!$row) {
            $STD->error("Invalid panel id specified.");
        }
        
        if (!$row['can_hide']) {
            $STD->error("You cannot change the visibility state of this panel.");
        }
        
        // Determine values, determine and if necessary, update rows
        if ($row['visible'] == 1) {
            $row['visible'] = 0;
            $new_row = 0;
            $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET row = row - 1 ".
                       "WHERE row > {$row['row']} AND p.column = '{$row['column']}'");
        } else {
            $row['visible'] = 1;
            $DB->query("SELECT max(row) AS mrow FROM {$CFG['db_pfx']}_panels p ".
                       "WHERE p.column = '{$row['column']}' GROUP BY p.column");
            $nv = $DB->fetch_row();
            $new_row = $nv['mrow']+1;
        }

        // Update visibility state
        $DB->query("UPDATE {$CFG['db_pfx']}_panels ".
                   "SET visible = '{$row['visible']}', fuse_up = '0', fuse_down = '0', row = '$new_row' ".
                   "WHERE pid = '{$IN['id']}'");
        
        // Clear panel fusing
        if ($row['fuse_up']) {
            $DB->query("UPDATE {$CFG['db_pfx']}_panels SET fuse_down = '0' WHERE pid = '{$row['fuse_up']}'");
        }
        
        if ($row['fuse_down']) {
            $DB->query("UPDATE {$CFG['db_pfx']}_panels SET fuse_up = '0' WHERE pid = '{$row['fuse_down']}'");
        }
        
        // Exit
        $url = str_replace("&amp;", '&', $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=01"));
        header("Location: $url");
        
        exit;
    }
    
    public function move_panel()
    {
        global $STD, $IN, $CFG, $DB;
        
        if (empty($IN['id'])) {
            $STD->error("No panel selected to move.");
        }
        
        if (!in_array($IN['dir'], array('u', 'd', 'l', 'r'))) {
            $STD->error("Invalid direction selected.");
        }
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_panels WHERE pid = '{$IN['id']}'");
        
        $row = $DB->fetch_row();
        if (!$row) {
            $STD->error("Invalid panel id specified.");
        }
        
        if ($row['visible'] == 0) {
            $STD->error("Cannot move a hidden panel.");
        }
        
        // Moving up
        if ($IN['dir'] == 'u') {
            if ($row['row'] == 1 && $row['column'] == 'U') {
                $STD->error("Cannot move selected panel any higher.");
            }
            if ($row['row'] == 1 && in_array($row['column'], array('L','R','C')) && !$row['can_strip']) {
                $STD->error("Cannot move selected panel any higher.");
            }
            
            if ($row['row'] > 1) {
                $new_row = $row['row'] - 1;
                $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET row = row + 1 ".
                           "WHERE p.column = '{$row['column']}' AND p.row = $new_row");
                $DB->query("UPDATE {$CFG['db_pfx']}_panels SET row = $new_row WHERE pid = {$IN['id']}");
            } else {
                if ($row['column'] == 'D' && $row['can_column_lr']) {
                    $target = 'L';
                } elseif ($row['column'] == 'D' && $row['can_column_c']) {
                    $target = 'C';
                } else {
                    $target = 'U';
                }
                    
                $DB->query("SELECT max(row) AS mrow FROM {$CFG['db_pfx']}_panels p ".
                           "WHERE p.column = '$target' GROUP BY p.column");
                $nv = $DB->fetch_row();
                $new_row = $nv['mrow'] + 1;
                $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = $new_row, p.column = '$target' ".
                           "WHERE pid = {$IN['id']}");
                if ($target == 'U') {
                    $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = p.row - 1 ".
                               "WHERE p.column = '{$row['column']}'");
                }
            }
        }
        
        // Moving down
        if ($IN['dir'] == 'd') {
            $DB->query("SELECT max(row) AS mrow FROM {$CFG['db_pfx']}_panels p ".
                           "WHERE p.column = '{$row['column']}' GROUP BY p.column");
            $nv = $DB->fetch_row();
            $limit = $nv['mrow'];
            
            if ($row['row'] == $limit && $row['column'] == 'D') {
                $STD->error("Cannot move selected panel any lower.");
            }
            if ($row['row'] == $limit && in_array($row['column'], array('L','R','C')) && !$row['can_strip']) {
                $STD->error("Cannot move selected panel any lower.");
            }
            
            if ($row['row'] < $limit) {
                $new_row = $row['row'] + 1;
                $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET row = row - 1 ".
                           "WHERE p.column = '{$row['column']}' AND p.row = $new_row");
                $DB->query("UPDATE {$CFG['db_pfx']}_panels SET row = $new_row WHERE pid = {$IN['id']}");
            } else {
                if ($row['column'] == 'U' && $row['can_column_lr']) {
                    $target = 'L';
                } elseif ($row['column'] == 'U' && $row['can_column_c']) {
                    $target = 'C';
                } else {
                    $target = 'D';
                }

                $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = p.row + 1 WHERE p.column = '$target'");
                $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = 1, p.column = '$target' ".
                           "WHERE pid = {$IN['id']}");
            }
        }
        
        // Moving left
        if ($IN['dir'] == 'l') {
            if ($row['column'] == 'L') {
                $STD->error("Cannot move selected panel any further left.");
            }
            if ($row['column'] == 'C' && !$row['can_column_lr']) {
                $STD->error("Cannot move selected panel any further left.");
            }
            if ($row['column'] == 'U' || $row['column'] == 'D') {
                $STD->error("Cannot move selected panel left or right.");
            }
            
            if ($row['column'] == 'R' && $row['can_column_c']) {
                $target = 'C';
            } else {
                $target = 'L';
            }
            
            $DB->query("SELECT max(row) AS mrow FROM {$CFG['db_pfx']}_panels p ".
                       "WHERE p.column = '$target' GROUP BY p.column");
            $nv = $DB->fetch_row();
            $new_row = $nv['mrow']+1;
            
            $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = $new_row, p.column = '$target' ".
                       "WHERE pid = {$IN['id']}");
            $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = p.row - 1 ".
                       "WHERE p.column = '{$row['column']}' AND p.row > {$row['row']}");
        }
        
        // Moving right
        if ($IN['dir'] == 'r') {
            if ($row['column'] == 'R') {
                $STD->error("Cannot move selected panel any further right.");
            }
            if ($row['column'] == 'C' && !$row['can_column_lr']) {
                $STD->error("Cannot move selected panel any further right.");
            }
            if ($row['column'] == 'U' || $row['column'] == 'D') {
                $STD->error("Cannot move selected panel left or right.");
            }
            
            if ($row['column'] == 'L' && $row['can_column_c']) {
                $target = 'C';
            } else {
                $target = 'R';
            }
            
            $DB->query("SELECT max(row) AS mrow FROM {$CFG['db_pfx']}_panels p ".
                       "WHERE p.column = '$target' GROUP BY p.column");
            $nv = $DB->fetch_row();
            $new_row = $nv['mrow']+1;
            
            $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = $new_row, p.column = '$target' ".
                       "WHERE pid = {$IN['id']}");
            $DB->query("UPDATE {$CFG['db_pfx']}_panels p SET p.row = p.row - 1 ".
                       "WHERE p.column = '{$row['column']}' AND p.row > {$row['row']}");
        }
        
        
        // Exit
        $url = str_replace("&amp;", '&', $STD->encode_url($_SERVER['PHP_SELF'], "act=panel&param=01"));
        header("Location: $url");
        
        exit;
    }
    
    public function edit_panel()
    {
        global $STD, $DB, $CFG, $IN;
        
        if (empty($IN['pid'])) {
            $STD->error("No panel selected to edit.");
        }
        
        $this->output .= $STD->global_template->page_header("Panel Properties");
        
        $DB->query("SELECT * FROM {$CFG['db_pfx']}_panels WHERE pid = '{$IN['pid']}'");
        $prow = $DB->fetch_row();
        if (!$prow) {
            $STD->error("Invalid panel selected to edit.");
        }
        
        // Some properties
        $properties = array();
        $properties['name'] = $prow['panel_name'];
        $properties['style'] = $prow['style'];
        $properties['hide_header'] = $STD->make_yes_no('hide_header', $prow['hide_header']);
        
        $this->output .= $this->html->edit_properties($properties);
        
        $this->output .= $STD->global_template->page_footer();
    }
}

function keysort($array)
{
    ksort($array);
    $array = @join('', $array);
    return $array;
}
