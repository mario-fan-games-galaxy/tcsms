<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/menu.php --
// handles side display of menu and categorization
//------------------------------------------------------------------

$component = new component_menu;

class component_menu
{
    public function get_menu($id)
    {
        global $TAG, $session;
        
        $session->touch_data('menu');
        $exp = $session->data['menu'];
        if (empty($session->data['menu'])) {
            $exp = array();
        }
        
        if (strpos($id, ',') !== false) {
            $id_parts = explode(',', $id);
            $exp[$id_parts[0]] = $id_parts[1];
        } elseif (isset($exp[$id])) {
            unset($exp[$id]);
        } else {
            $siblings = component_menu::get_siblings($id);
            reset($siblings);
            while (list(, $v) = each($siblings)) {
                if (isset($exp[$v]) && $exp[$v] == '') {
                    unset($exp[$v]);
                }
            }
            
            $exp[$id] = '';
        }

        //$session->save_data('menu', $exp);
        $session->data['menu'] = $exp;

        return component_menu::load_menu();
    }
    
    public function get_siblings($id)
    {
        global $TAG;
        
        $siblings = array();
        
        if ($TAG->flatnode[$id][1] == 0) {
            reset($TAG->flatnode);
            while (list($k, $v) = each($TAG->flatnode)) {
                if ($k != $id && $v[1] == 0) {
                    $siblings[] = $k;
                }
            }
        } else {
            $pid = $TAG->flatnode[$id][1];
            reset($TAG->flatnode[$pid][2]);
            while (list(, $v) = each($TAG->flatnode[$pid][2])) {
                if ($v != $id) {
                    $siblings[] = $v;
                }
            }
        }

        return $siblings;
    }
    
    public function build_menu()
    {
    }
    
    // [+] top_closed, [-] top_open, [-] sub_sel, sub_open, sub_close
    // [-] tree_exp, [+] tree_extra
    // [+] subnode, [+] subnode_first, [ ] subleaf, [ ] subleaf_first, [ ] leaf
    public function format_menu_row($type, $base, $id, $text, $filter=array())
    {
        global $IN, $STD;
        
        $filter = @join(',', $filter);
        
        $icon_width = '0';
        $link = "javascript:expand_menu('$id');";
        $txtlink = $link;
        $url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=01&c=$base&filter=$filter");
        
        // Link
        switch ($type) {
            case 'subnode':
            case 'subnode_first':
                $txtlink = "javascript:expand_link('$id', '$url');"; break;
            case 'leaf':
            case 'subleaf':
            case 'subleaf_first':
                $txtlink = $url; break;
        }
        
        $tree_icon = "";
        
        // Icon
        switch ($type) {
            case 'top_closed':
            case 'tree_extra':
                $tree_icon = ""; break;
            case 'subnode_first':
                $tree_icon = "<img src='{{image_path}}/angle.gif'>&nbsp;"; break;
            case 'subnode':
                $tree_icon = "<img src='{{image_path}}/blank.gif'>&nbsp;"; break;
            case 'subleaf_first':
                $tree_icon = "<img src='{{image_path}}/angle.gif'>&nbsp;"; break;
            case 'subleaf':
                $tree_icon = "<img src='{{image_path}}/blank.gif'>&nbsp;"; break;
            case 'leaf':
                $tree_icon = ""; break;
        }
        
        // Text
        if (in_array($type, array('top_closed','top_open','sub_sel','sub_open','tree_exp','tree_extra'))) {
            $text = "<b>$text</b>";
        }
        
        // Width
        if (in_array($type, array('subnode_first','subnode','subleaf_first','subleaf'))) {
            $icon_width = 14;
        }
        
        // Row
        $html = "<table border='0' cellpadding='0' cellspacing='0'><tr>
				   <td valign='top' width='$icon_width'>$tree_icon</td>
				   <td><a href=\"$txtlink\">$text</a></td></tr></table>";
        
        // Block
        switch ($type) {
            case 'top_closed':
                $html = "<tr><td bgcolor='#42427B' width='100%'>$html</td></tr>"; break;
            case 'top_open':
                $html = "<tr><td bgcolor='#6669AC' width='100%'>$html</td></tr>"; break;
            case 'sub_sel':
                $html = "<tr><td bgcolor='#535699' width='100%'>$html</td></tr>"; break;
            case 'sub_open':
                $html = "<tr><td bgcolor='#313163' width='100%'>"; break;
            case 'sub_close':
                $html = "</td></tr>"; break;
        }
        
        return $html;
    }
    
    public function load_menu()
    {
        global $IN, $STD, $CFG, $TAG;
        
        $templ = new template;
        
        $templ->setTemplate('menu');
        $templ->addTag('home_url', $STD->encode_url($_SERVER['PHP_SELF']));
        $templ->addTag('rules_url', $STD->encode_url($_SERVER['PHP_SELF'], 'act=submit&param=01'));
        $templ->addTag('staff_url', $STD->encode_url($_SERVER['PHP_SELF'], 'act=staff'));
        $templ->addTag('links_url', $STD->encode_url($_SERVER['PHP_SELF'], 'act=links'));
        $templ->addTag('menu', component_menu::build_menu());
        
        return $templ->build();
    }
}
