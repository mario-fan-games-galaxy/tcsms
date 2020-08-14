<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// std.php --
// Template, Error Reporting, and Misc function library
//------------------------------------------------------------------

//----------------------------------------------------------------------------------------
// class template : Template Parsing and Display Class
// Handles loading, parsing, and printing of templates and data
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//----------------------------------------------------------------------------------------

class template
{
    public $template	= '';
    public $cur_temp	= null;
    public $tags		= array();
    public $out		= '';
    public $meta		= '';
    public $skin		= 0;
    public $override 	= array();
    public $php_errors = array();
    
    public function init()
    {
        //		global $SAJAX;
        
        //		require_once ROOT_PATH.'component/menu.php';
        
        ob_start();
        
        set_error_handler('php_err_handler');
        
        //		$SAJAX->sajax_allow("component_menu__get_menu");
    }
    
    //	function setTemplate ($template_name) {
    //		global $CFG;
//
    //		$tdir = $this->determine_template();
    //		$this->tags = array();
//
    //		if (!file_exists(ROOT_PATH.$CFG['template_path'].'/'.$tdir.'/'.$template_name.'.html'))
    //			$this->preprocess_error("Could not locate template: $template_name");
//
    //		$this->template = file_get_contents(ROOT_PATH.$CFG['template_path'].'/'.$tdir.'/'.$template_name.'.html');
    //	}
    
    public function useTemplate($template_name)
    {
        global $CFG, $STD;
        
        $tdir = $this->determine_template();
        $temp_path = ROOT_PATH.$CFG['template_path'].'/'.$tdir.'/temp_'.$template_name.'.php';
        $temp_name = 'template_' . $template_name;
        
        if (!file_exists($temp_path)) {
            $this->preprocess_error("Could not locate template: $template_name");
        }
        
        require_once $temp_path;
        
        $this->cur_temp = new $temp_name;
        
        return $this->cur_temp;
    }
    
    //	function addTag ($name, $value) {
//
    //		$this->tags[$name] = $value;
    //	}
    
    //	function addMeta ($meta) {
//
    //		$this->meta .= $meta;
    //	}
    
    //	function build () {
//
    //		$this->standard_tags();
    //		$this->out = $this->template;
//
    //		reset ($this->tags);
    //		while (list($key,$val) = each($this->tags)) {
    //			if (is_a($val, 'template'))
    //				$val = $val->build();
//
    //			$this->out = str_replace('{{'.$key.'}}', $val, $this->out);
    //		}
//
    //		return $this->out;
    //	}
    
    //	function site_level_tags () {
    //		global $STD, $SAJAX;
//
    //		if (isset($this->override['no_parse_site_tags']))
    //			return;
//
    //		require_once ROOT_PATH.'lib/Sajax.php';
//
    //		// Login Menu
    //		require_once ROOT_PATH.'component/login.php';
    //		$this->addTag('login', component_login::load_menu());
//
    //		// Menu
    //		require_once ROOT_PATH.'component/menu.php';
    //		$this->addTag('menu', component_menu::load_menu());
//
    //		$SAJAX->sajax_export("component_menu__get_menu");
    //		$SAJAX->sajax_handle_client_request();
    //	}
    
    public function display($out, $title='')
    {
        global $DB, $STD, $session;
        
        $STD->global_template->title = $title;
        
        ob_get_clean();
        
        if ($DB->debug) {
            echo $DB->show_debug();
            ob_flush();
            exit;
        }
        
        if (!isset($STD->global_template)) {
            $STD->global_template = $STD->template->useTemplate('global');
        }
        
        //$STD->tags['sajax'] = $STD->sajax->sajax_get_javascript();
                
        // We need a special mechanism for hiding all the default site elements if necessary.  This is done with the
        // content_only flag, and uses 2 special functions in the global template.
        if (empty($STD->template->content_only)) {
            $output = $STD->global_template->wrapper($STD->global_template, $out);
        } else {
            $output  = $STD->global_template->html_head();
            $output .= $STD->global_template->site_content_header();
            $output .= $out;
            $output .= $STD->global_template->site_content_footer();
        }
        
        $err = @join('<br /><br />', $this->php_errors);
        
        $sess = "<!--({$session->sess_fail}) Session Status-->";
        
        echo $sess . $err . $output;
        
        ob_flush();
    }
    
    public function error($data)
    {
        global $STD;
        
        if (empty($STD->global_template)) {
            if (!empty($STD->template->override['template']) && $STD->template->override['template'] == 'admin') {
                $STD->global_template = $this->useTemplate('adm_global');
                $STD->global_template_ui = new adm_template_ui;
            } else {
                $STD->global_template = $this->useTemplate('global');
                $STD->global_template_ui = new template_ui;
            }
            
            $STD->tags = $STD->template->global_tags();
        }

        $STD->template->display($STD->global_template->error($data));
        exit;
    }
        
    public function preprocess_error($msg)
    {
        global $CFG;
        
        ob_get_clean();
        
        $html = "<html><head><title>Fatal Error</title></head>
				 <body style='font-family:Arial'><h1>Fatal Error</h1>
				 An unrecoverable error has ouccurred.  Please wait a few minutes and
				 try again.  If the error still persists, please contact the 
				 <a href='mailto:{$CFG['admin_email']}'>Site Staff</a> with the error information
				 below.<br /><br /><p align='center'>
				 <table border='0' cellspacing='0' cellpadding='2' width='80%'><tr>
				 <td width='100%' style='font-family:Courier New'>
				 $msg
				 </td></tr></table></p></body></html>";
                 
        echo $html;
        
        ob_flush();
        
        exit;
    }
    
    //	function standard_tags () {
    //		global $CFG, $STD;
//
    //		$this->addTag('template_url', ROOT_PATH.$CFG['template_path'].'/'.$this->skin);
    //		$this->addTag('image_path', ROOT_PATH.$CFG['template_path'].'/'.$this->skin.'/images');
    //		$this->addTag('global_image_path', ROOT_PATH.$CFG['template_path'].'/images');
    //		$this->addTag('skin', $this->skin);
    //		$this->addTag('PHP_SELF', $_SERVER['PHP_SELF']);
    //		$this->addTag('root_url', $STD->make_root_url($_SERVER['PHP_SELF']));
    //	}
    
    public function global_tags()
    {
        global $CFG, $STD, $IN;
        
        $this->skin = $this->determine_template();
        
        $tags = array(
            'root_path'			=> ROOT_PATH,
            'template_path'		=> ROOT_PATH.$CFG['template_path'].'/'.$this->skin,
            'image_path'		=> ROOT_PATH.$CFG['template_path'].'/'.$this->skin.'/images',
            'global_image_path'	=> ROOT_PATH.$CFG['template_path'].'/images',
            'skin'				=> $this->skin,
            'PHP_SELF'			=> $_SERVER['PHP_SELF'],
            'root_url'			=> $STD->make_root_url($_SERVER['PHP_SELF']),
            'c'					=> $IN['c'],
        );
        
        return $tags;
    }
    
    public function determine_template()
    {
        global $CFG, $STD;
        
        $skin = $CFG['template_default'];

        if (isset($STD->user['skin']) && $STD->user['skin'] > 0) {
            $skin = $STD->user['skin'];
        }
        
        if (isset($this->override['template'])) {
            $skin = $this->override['template'];
        }
            
        $this->skin = $skin;
        
        return $skin;
    }
}

//----------------------------------------------------------------------------------------
// class std : Standard Function Class
// Provides collection of standard functions to be used by rest of script
//----------------------------------------------------------------------------------------
// Depends on:	template ($TPL)
//				configuration ($CFG)
//				db_driver ($DB)
//----------------------------------------------------------------------------------------

class std
{
    public $sajax				= null;
    public $global_template	= null;
    public $template			= null;
    public $tags				= array();
    public $user				= array();
    public $userobj			= null;
    
    public function _clone($obj)
    {
        if (PHP_VERSION < 5) {
            return $obj;
        } else {
            return clone $obj;
        }
    }
    
    public function error($msg)
    {
        global $STD, $CFG;
        
        if ($CFG['site_offline'] && !$STD->user['acp_access']) {
            $STD->offline = 1;
            $STD->template->display($CFG['offline_msg']);
            exit;
        }
        
        $STD->template->error($msg);
    }
    
    public function debug($msg)
    {
        global $STD;
        
        if (is_array($msg) || is_object($msg)) {
            $STD->template->preprocess_error('<pre>'.print_r($msg, 1).'</pre>');
        } else {
            $STD->template->preprocess_error($msg);
        }
    }
    
    public function get_cookie($cookie_name)
    {
        global $CFG;
        
        if (!isset($_COOKIE[($CFG['cookie_prefix'].$cookie_name)])) {
            return false;
        }
        
        return $this->clean_value($_COOKIE[$CFG['cookie_prefix'].$cookie_name]);
    }
    
    public function set_cookie($cookie_name, $value, $time=1)
    {
        global $CFG;
        
        if ($time < 0) {
            $time = -60*60;
        } else {
            $time = 60*60*24*365;
        }
            
        return setcookie($CFG['cookie_prefix'].$cookie_name, $value, time()+$time);
    }
    
    public function br2nl($text)
    {
        return preg_replace("/<\s*br(\s+\/)?>/", "\n", $text);
    }
    
    public function parse_input()
    {
        $IN = array();
        
        reset($_GET);
        while (list($key, $val) = @each($_GET)) {
            $IN[$key] = $this->clean_value($val);
        }
        
        reset($_POST);
        while (list($key, $val) = @each($_POST)) {
            $IN[$key] = $this->clean_value($val);
        }
        
        if (!isset($IN['act'])) {
            $IN['act'] = '0';
        }
        if (!isset($IN['param'])) {
            $IN['param'] = '0';
        }
        if (!isset($IN['c'])) {
            $IN['c'] = '0';
        }
        
        return $IN;
    }
    
    public function clean_value($value)
    {
        if (is_array($value)) {
            while (list($key, $val) = @each($value)) {
                $value[$key] = $this->clean_value($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                //$value = stripslashes($value);
            }
        
            $value = trim(rtrim($value));
            $value = str_replace("'", "&#39;", $value);
            $value = str_replace("\"", "&quot;", $value);
            $value = str_replace("<", "&lt;", $value);
            $value = str_replace(">", "&gt;", $value);
            $value = preg_replace("/\n/", "<br />", $value);
            $value = preg_replace("/\r/", "", $value);
        }
        
        return $value;
    }
    
    public function rawclean_value($value)
    {
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        
        $value = trim(rtrim($value));
        $value = preg_replace("/\r/", "", $value);
            
        return $value;
    }
    
    public function safe_display($value)
    {
        $value = str_replace("'", "&#39;", $value);
        $value = str_replace("\"", "&quot;", $value);
        $value = str_replace("<", "&lt;", $value);
        $value = str_replace(">", "&gt;", $value);
        $value = preg_replace("/\n/", "<br />", $value);
        $value = preg_replace("/\r/", "", $value);
        
        return $value;
    }
    
    public function standard_char($value)
    {
        $value = preg_replace("/\&.*;/", "", $value);
        
        return $value;
    }
    
    public function make_form_token()
    {
        global $session;
        
        $session->data['form_token'] = md5(uniqid(rand()));
        
        return $session->data['form_token'];
    }
    
    public function clear_form_token()
    {
        global $session;
        
        $session->data['form_token'] = '';
    }
    
    public function validate_form($token)
    {
        global $session;

        return true;
        
        // Users no longer seem to have consistent form_token
        $session->touch_data('form_token');
        if ($session->data['form_token'] == $token) {
            return true;
        }
        
        return false;
    }
    
    public function get_regex($pattern_name)
    {
        switch ($pattern_name) {
            case 'email':
                return "/^[A-Za-z0-9._-]+@[A-Za-z0-9._-]+\.([A-Za-z]{2,4}|[A-Za-z]{2,4}\.[A-Za-z]{2,4})$/";
            case 'url':
                return "/^http:\/\/(www\.|)[A-Za-z0-9._-]+\.([A-Za-z]{2,4}|[A-Za-z]{2,4}\.[A-Za-z]{2,4})(((\/(~|)[^~\/\\$:><?]+)+(\?.+|))|\/|)$/i";
            case 'nat_delim':
                return "/((\s*,\s*|\s+)and|(\s*,\s*|\s+)&)\s+|(,\s*)/i";
            default:
                return '';
        }
    }
    
    public function csv_merge($arr)
    {
        if (empty($arr)) {
            return '';
        }
        
        $out = '';
        reset($arr);
        while (list($k, $v) = each($arr)) {
            if (is_array($v)) {
                $arr[$k] = $this->csv_merge($v);
            }
            $out .= $v.',';
        }
        
        $out = preg_replace('/,$/', '', $out);
        
        return $out;
    }
    
    public function safe_file_name($file)
    {
        $sn = explode('.', $file);
        if (sizeof($sn) == 1) {
            $ext = 'unknown';
        } else {
            $ext = $sn[(sizeof($sn)-1)];
            unset($sn[(sizeof($sn)-1)]);
        }
        $sn = @join('.', $sn);
        
        $tail = time().'.'.$ext;
        $sn = preg_replace("/[^A-Za-z0-9]/", '_', $sn);
        $sn = substr($sn, 0, 63-strlen($tail));
        $sn .= $tail;
        
        return $sn;
    }
    
    public function file_check_restrictions($dir, $cat, $file)
    {
        global $CFG, $TAG, $STD;
        
        $cat = $TAG->data[$cat]['tagname'];
        
        // Do restrictions exist?
        if (empty($CFG['file_restrictions'][$dir][$cat])) {
            return;
        }
            
        $rset = $CFG['file_restrictions'][$dir][$cat];
        
        // Check allowed mimes
        if (!empty($rset['mime']) && !in_array($file['type'], $rset['mime'])) {
            $STD->error("File \"{$file['name']}\" ($dir) is not a valid file type.  This file must be one of the following types: {$rset['ext']}");
        }
        
        // Height and Width check for images
        if (!empty($rset['width'])) {
            $info = getimagesize($file['tmp_name']);
            if ($info[0] != $rset['width']) {
                $STD->error("Image file \"{$file['name']}\" ($dir) must be exactly {$rset['width']} pixels wide.");
            }
        }
        
        if (!empty($rset['height'])) {
            $info = getimagesize($file['tmp_name']);
            if ($info[1] != $rset['height']) {
                $STD->error("Image file \"{$file['name']}\" ($dir) must be exactly {$rset['height']} pixels high.");
            }
        }
        
        // Size Restrictions?
        if (!empty($rset['max_size']) && $file['size'] > $rset['max_size']) {
            $STD->error("File \"{$file['name']}\" ($dir) is larger than the maximum allowed file size of {$rset['max_size']}.");
        }
    }
    
    public function limit_string($str, $maxlen=32768)
    {
        if (strlen($str) > $maxlen) {
            $str = substr($str, 0, $maxlen);
        }
        
        // Chop up words
        $str = str_replace("<br />", " <br /> ", $str);
        $str = explode(' ', $str);
        
        // trim URLs
        $str = preg_replace_callback(
            "/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/\%?=~_|!:,.;]*[-A-Z0-9+&@#\/\%=~_|]/i",
            array(&$this, 'shrink_url'),
            $str
        );
        
        //for ($x=0; $x<sizeof($str); $x++)
        //	$str[$x] = wordwrap($str[$x], 45, "<br />", 1);
        
        $str = @join(' ', $str);
        $str = str_replace(" <br /> ", "<br />", $str);
        
        return $str;
    }
    
    public function shrink_url($matches)
    {
        $url = $matches[0];
        
        $url = preg_replace("/&(?![#A-Za-z0-9]+;)/", "&amp;", $url);
        
        if (strlen($url) <= 55) {
            return $url;
        }
        
        $p1 = substr($url, 0, 30);
        $p2 = substr($url, -15);
        
        return "<a href=\"{$url}\">{$p1}...{$p2}</a>";
    }
    
    public function swap_values($current, $v1, $v2)
    {
        if ($current == $v1) {
            return $v2;
        }
        return $v1;
    }
    
    public function swap(&$v1, &$v2)
    {
        $temp = $v1;
        $v1 = $v2;
        $v2 = $temp;
    }
    
    public function translate_date($date)
    {
        global $STD;
        
        if ($STD->user['timezone'] !== null) {
            $date += $STD->user['timezone']*3600;
        }
        if ($STD->user['dst']) {
            $date += 3600;
        }
            
        return $date;
    }
    
    public function make_date_time($date)
    {
        global $STD, $CFG;
        
        if ($STD->user['timezone'] !== null) {
            $date += $STD->user['timezone']*3600;
        }
        if ($STD->user['dst']) {
            $date += 3600;
        }
        
        return gmdate($CFG['date_time_format'], max($date, 0));
    }
    
    public function make_date_short($date)
    {
        global $STD, $CFG;
        
        if ($STD->user['timezone'] !== null) {
            $date += $STD->user['timezone']*3600;
        }
        if ($STD->user['dst']) {
            $date += 3600;
        }
        
        return gmdate($CFG['date_short'], max($date, 0));
    }
    
    public function timezone_box($timezone)
    {
        global $STD;
        
        $vals = array('-12','-11','-10','-9','-8','-7','-6','-5','-4','-3','-2','-1','0',
                      '1','2','3','4','5','6','7','8','9','10','11','12','13','14');
        $nams = array('(GMT - 12:00 hours)',
                      '(GMT - 11:00 hours)',
                      '(GMT - 10:00 hours)',
                      '(GMT - 9:00 hours)',
                      '(GMT - 8:00 hours)',
                      '(GMT - 7:00 hours)',
                      '(GMT - 6:00 hours)',
                      '(GMT - 5:00 hours)',
                      '(GMT - 4:00 hours)',
                      '(GMT - 3:00 hours)',
                      '(GMT - 2:00 hours)',
                      '(GMT - 1:00 hours)',
                      '(GMT + 0:00 hours)',
                      '(GMT + 1:00 hours)',
                      '(GMT + 2:00 hours)',
                      '(GMT + 3:00 hours)',
                      '(GMT + 4:00 hours)',
                      '(GMT + 5:00 hours)',
                      '(GMT + 6:00 hours)',
                      '(GMT + 7:00 hours)',
                      '(GMT + 8:00 hours)',
                      '(GMT + 9:00 hours)',
                      '(GMT + 10:00 hours)',
                      '(GMT + 11:00 hours)',
                      '(GMT + 12:00 hours)',
                      '(GMT + 13:00 hours)',
                      '(GMT + 14:00 hours)');
        
        if ($timezone === null) {
            $timezone = 0;
        }
            
        return $STD->make_select_box('timezone', $vals, $nams, $timezone, 'selectbox');
    }
    
    public function make_select_box($name, $value, $text, $selected, $class='', $other='')
    {
        if (!preg_match('/\[|\]/', $name)) {
            $idname = "id='$name'";
        } else {
            $idname = '';
        }
            
        $box = "<select name='$name' $idname size='1' class='$class' $other>\n";
        for ($x=0; $x<sizeof($value); $x++) {
            ($selected == $value[$x]) ? $sel = "selected='selected'" : $sel = '';
            $box .= "<option value='{$value[$x]}' $sel>{$text[$x]}</option>\n";
        }
        $box .= "</select>";
        
        return $box;
    }
    
    public function make_yes_no($name, $selected, $disabled='')
    {
        $sel1 = '';
        $sel2 = '';
        ($selected == 1) ? $sel1 = "checked='checked'" : $sel2 = "checked='checked'";
        ($disabled == 1) ? $dis = "disabled='disabled'" : $dis = '';
        $box = "<input type='radio' name='$name' value='1' class='radiobutton' $sel1 $dis /> Yes &nbsp; 
			<input type='radio' name='$name' value='0' class='radiobutton' $sel2 $dis /> No";
        
        return $box;
    }
    
    public function make_checkbox($name, $value, $selected='')
    {
        ($selected) ? $selected = "checked='checked'" : $selected = '';
        $box = "<input type='checkbox' name='$name' value='1' $selected' />";
        
        return $box;
    }
    
    public function make_checkboxlist($name, $value, $text, $selected)
    {
        $html = '';
        
        for ($x=0; $x<sizeof($value); $x++) {
            $txtname = htmlentities($text[$x]);
            $lselected = '';
            if ($selected[$x]) {
                $lselected = "checked='checked'";
            }
                
            $html .= "<input type='checkbox' name='{$name}' value='{$value[$x]}' 
				$lselected /> {$txtname}<br />";
        }
        
        $html = preg_replace("/<br \/>$/", '', $html);
        
        return $html;
    }
    
    public function encode_url($url, $args='')
    {
        global $session;
        
        if (!empty($args)) {
            $url .= "?{$args}";
        }

        /*$url = htmlspecialchars($url);
        $add = '';

        if (session_id() && empty($_COOKIE['PHPSESSID'])) {
            $add = '?sess='.session_id();
            if (!empty($args))
                $add .= '&amp;';
        }

        if (empty($add) && !empty($args))
            $add = '?';

        $add .= htmlspecialchars($args);

        return $url.$add;*/
        
        return $session->rewrite_url($url);
    }
    
    public function make_root_url($urlarg)
    {
        $url = $this->encode_url($urlarg);
        if (preg_match('/\?/', $url)) {
            $url .= '&amp;';
        } else {
            $url .= '?';
        }
        
        return $url;
    }
    
    public function tag_urls($data)
    {
        global $CFG;
        
        $data = preg_replace("/{$_SERVER['PHP_SELF']}\??(sess=\w{16}&?)?/", "{%site_url%}?", $data);
        $data = preg_replace("/{$CFG['root_url']}\??(sess=\w{16}&?)?/", "{%site_url%}?", $data);
        
        return $data;
    }
    
    public function untag_urls($data)
    {
        $data = preg_replace_callback("/\{%site_url%\}\??/", array(&$this, 'untag_urls_callback'), $data);
        
        return $data;
    }
    
    public function untag_urls_callback($match)
    {
        global $CFG, $session;
        
        $url = $this->encode_url($CFG['root_url']);
        
        if (preg_match("/\?$/", $match[0])) {
            if ($session->sess_id && !$session->using_cookies) {
                $url .= '&';
            } else {
                $url .= '?';
            }
        }
        
        return $url;
    }
    
    public function nat_substr($str, $len)
    {
        while (($chr = substr($str, $len-1, 1)) != false && $chr != ' ') {
            $len++;
        }
        return substr($str, 0, $len);
    }
    
    public function format_username(&$row, $prefix='', $stag='')
    {
        global $STD;
        
        $user = '<b>N/A</b>';
        
        if (!empty($row[$prefix.'username']) && !empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0) {
            $user = $row[$prefix.'username'];
        }
        
        if (!empty($row['author_override'])) {
            $user = $row['author_override'];
        }
        
        if (!empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0) {
            if (!empty($stag)) {
                $url = "{%site_url%}?act=user&param=01&uid={$row[$prefix.'uid']}";
            } else {
                $url = $STD->encode_url("index.php", "act=user&param=01&uid={$row[$prefix.'uid']}");
            }
            
            if (!empty($row[$prefix.'name_prefix'])) {
                $user = $row[$prefix.'name_prefix'] . $user;
            }
            if (!empty($row[$prefix.'name_suffix'])) {
                $user = $user . $row[$prefix.'name_suffix'];
            }
                
            $user = "<a href='$url'>$user</a>";
        }
        
        return $user;
    }
    
    public function get_email_icon(&$row, $prefix='')
    {
        global $CFG, $IN, $STD;
        
        if (!$row[$prefix.'show_email']) {
            $email = '';
        } else {
            $addr = str_replace('@', ' _AT_ ', $row[$prefix.'email']);
            $addr = str_replace('.', ' _DOT_ ', $addr);
            $uaddr = str_replace(' ', '%20', $addr);
            $email = "<a href='mailto:$uaddr'>
				<img src='{$STD->tags['image_path']}/email.gif' border='0' alt='[E]' title='Email: $addr' /></a>";
        }
        
        return $email;
    }
    
    public function get_website_icon(&$row, $prefix='')
    {
        global $CFG, $IN, $STD;
        
        empty($row[$prefix.'weburl']) && empty($row[$prefix.'weburl_override'])
            ? $ws_icon = 'home_nolink.gif'
            : $ws_icon = 'home.gif';
        empty($row[$prefix.'website'])
            ? $website = ''
            : $website = "
				<img src='{$STD->tags['image_path']}/$ws_icon' border='0' alt='[W]' title='Website: {$row[$prefix.'website']}' />";
        if (!empty($row[$prefix.'website_override'])) {
            $website = "
				<img src='{$STD->tags['image_path']}/$ws_icon' border='0' alt='[W]' title='Website: {$row[$prefix.'website_override']}' />";
        }
        if (!empty($row[$prefix.'weburl_override'])) {
            $website = "<a href='{$row[$prefix.'weburl_override']}'>$website</a>";
        } elseif (!empty($row[$prefix.'weburl'])) {
            $website = "<a href='{$row[$prefix.'weburl']}'>$website</a>";
        }
        
        return $website;
    }
    
    public function get_user_icon(&$row, $prefix='')
    {
        global $CFG, $IN, $STD;
        
        if (empty($row[$prefix.'icon'])) {
            if (empty($CFG['default_icon'])) {
                return '';
            }
                
            return "<img src='{$CFG['default_icon']}' border='0' alt='No Icon' />";
        }
            
        if (empty($row[$prefix.'icon_dims'])) {
            $dims = explode("x", $CFG['def_icon_dims']);
        } else {
            $dims = explode("x", $row[$prefix.'icon_dims']);
        }
        
        if (empty($dims[0]) || empty($dims[1])) {
            $dims = explode("x", $CFG['def_icon_dims']);
        }
        
        $icon = "<img src='{$row[$prefix.'icon']}' width='{$dims[0]}' height='{$dims[1]}' border='0' alt='User Icon' />";
        
        return $icon;
    }
    
    public function get_page_prefs()
    {
        global $CFG, $STD, $session;
        
        $session->touch_data('pagesize');
        if (!empty($session->data['pagesize'])) {
            return $session->data['pagesize'];
        }
        if (!empty($STD->userobj->data['items_per_page'])) {
            return $STD->userobj->data['items_per_page'];
        }
        return $CFG['default_pagesize'];
    }
    
    public function paginate($start, $total, $perpage, $url)
    {
        global $STD;
        
        if ($total == 0) {
            return '';
        }
        
        $pcount = ceil($total / $perpage);
        $selpage = floor($start / $perpage)+1;
        $inc = 10;
        if ($pcount > 200) {
            $inc = 100;
        }
        if ($pcount > 2000) {
            $inc = 1000;
        }
        

        $pages = '';
        $pages_s = '';
        $pages_e = '';
        
        if ($selpage > 3) {
            $ppfx = '';
            
            if (($selpage + 5) >= 2*$inc) {
                $ppfx = '<sub>...</sub> ';
                $pg = (floor(($selpage+5)/$inc)-1) * $inc;
                $ofst = ($pg - 1) * $perpage;
                $furl = $STD->encode_url($_SERVER['PHP_SELF'], $url."&st=$ofst");
                $ppfx .= "<a href='$furl' style='text-decoration:underline'>&#171; $pg</a> ";
            }
            
            $furl = $STD->encode_url($_SERVER['PHP_SELF'], $url."&st=0");
            $pages_s = "<span style='text-decoration:underline; font-weight:normal'>&#171; First</span>";
            $pages_s = "<a href='$furl'>$pages_s</a><span style='font-weight:normal'> {$ppfx}<sub>...</sub> </span>";
        }
        
        if (($pcount - $selpage) > 2) {
            $ppfx = '';
            
            if (($pcount - $selpage - 5) >= $inc) {
                $ppfx = '<sub>...</sub> ';
                $pg = (floor(($selpage+5)/$inc)+1) * $inc;
                $ofst = ($pg - 1) * $perpage;
                $furl = $STD->encode_url($_SERVER['PHP_SELF'], $url."&st=$ofst");
                $ppfx .= "<a href='$furl' style='text-decoration:underline'>$pg &#187;</a> ";
            }
            
            $endoffset = ($pcount - 1) * $perpage;
            $furl = $STD->encode_url($_SERVER['PHP_SELF'], $url."&st=$endoffset");
            $pages_e = "<span style='text-decoration:underline; font-weight:normal'>Last &#187;</span>";
            $pages_e = "<span style='font-weight:normal'> {$ppfx}<sub>...</sub> </span><a href='$furl'>$pages_e</a>";
        }
        
        for ($cur = max(1, $selpage - 2); $cur <= ($selpage + 2) && $cur <= $pcount; $cur++) {
            $offset = ($cur - 1) * $perpage;
            $purl = $STD->encode_url($_SERVER['PHP_SELF'], $url."&st=$offset");
            
            if ($cur == $selpage) {
                $pages .= " <a href='$purl' style='text-decoration:underline; font-weight:bold'>$cur</a> ";
            } else {
                $pages .= " <a href='$purl' style='text-decoration:underline; font-weight:normal'>$cur</a> ";
            }
        }
        
        $pages = preg_replace("/[ ]$/", '', $pages);
        $pages = "<span style='font-weight:normal'>($pcount)</span> {$pages_s}{$pages}{$pages_e}";
        
        return $pages;
    }
    
    public function order_translate($orderlist, $defaults)
    {
        global $STD, $IN;
        
        if (!empty($IN['o1'])) {
            $op = array($IN['o1'], $IN['o2']);
        } elseif (empty($IN['o'])) {
            $op = $defaults;
        } else {
            $op = explode(',', $IN['o']);
        }
        
        while (list($k, $v) = each($orderlist)) {
            if ($k == $op[0]) {
                $op[0] = $v;
            }
        }
        
        if ($op[1] == 'd') {
            $op[1] = 'DESC';
        } else {
            $op[1] = 'ASC';
        }
        
        return $op;
    }
    
    public function order_links($orderlist, $url, $defaults)
    {
        global $IN, $STD;
        
        if (empty($IN['o'])) {
            $op = $defaults;
        } else {
            $op = explode(',', $IN['o']);
        }
            
        $asc = "<img src='{$STD->tags['image_path']}/ASC.gif' />";
        $desc = "<img src='{$STD->tags['image_path']}/DESC.gif' />";
        
        $out = array();
        
        while (list($k, $v) = each($orderlist)) {
            $out[$k] = array();
            $dir = $defaults[1];
            
            if ($op[0] == $k && $op[1] == 'a') {
                $dir = 'd';
                $out[$k]['img'] = $asc;
            } elseif ($op[0] == $k && $op[1] == 'd') {
                $dir = 'a';
                $out[$k]['img'] = $desc;
            } else {
                $out[$k]['img'] = '';
            }
            
            $out[$k]['url'] = "{$url}&amp;o={$k},{$dir}";
        }
        
        return $out;
    }
    
    public function captcha($str)
    {
        
        //flush();
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
        header("Pragma: no-cache");
        header("Content-Type: image/jpeg");
        
        $image_w = 200;
        $image_h = 60;
        
        $x_step = $image_w / strlen($str);
        
        //$im = imagecreatetruecolor( $image_w, $image_h );
        $im = imagecreate($image_w, $image_h);

        $white = ImageColorAllocate($im, 255, 255, 255);
        $black = ImageColorAllocate($im, 0, 0, 100);
        $lines = ImageColorAllocate($im, 100, 100, 200);
        $lines2 = ImageColorAllocate($im, 30, 30, 140);
        $bg = ImageColorAllocate($im, 140, 140, 240);
        
        imageFill($im, 0, 0, $bg);
        
        //-------------------------------------------------
        // Prepare character data
        
        preg_match_all("/./", $str, $match);
        $chars = $match[0];
        
        $characters = array();
        
        for ($x=0; $x < sizeof($chars); $x++) {
            $font = getcwd() . '/fonts/geodesic.ttf';
            $size = 30;
            $angle = rand(-20, 20);
            $color = $black; // ImageColorAllocate( $im, 0, 0, 0 );
            
            $tb = ImageTTFBBox($size, $angle, $font, $chars[$x]);
            
            $width = max($tb[0], $tb[2], $tb[4], $tb[6]) - min($tb[0], $tb[2], $tb[4], $tb[6]) + 5;
            $height = max($tb[1], $tb[3], $tb[5], $tb[7]) - min($tb[1], $tb[3], $tb[5], $tb[7]);
            $ledge = $tb[6] - $tb[0];
            
            $characters[$x] = array( 'char'		=> $chars[$x],
                                     'font'		=> $font,
                                     'size'		=> $size,
                                     'angle'	=> $angle,
                                     'color'	=> $color,
                                     'width'	=> $width,
                                     'height'	=> $height,
                                     'ledge'	=> $ledge );
        }
        
        //-------------------------------------------------
        // Determine some basic dimensioning data
        
        $t_width = 0;
        $t_height = 0;
        
        for ($x=0; $x < sizeof($characters); $x++) {
            $t_width += ($characters[$x]['width'] - abs($characters[$x]['ledge']));
            $t_height = max($t_height, $characters[$x]['height']);
        }
        
        //-------------------------------------------------
        // Score the surface
        
        for ($x=0; $x < 150; $x++) {
            $st_x = rand(0, $image_w);
            $st_y = rand(0, $image_h);
            $en_x = rand(0, $image_w);
            $en_y = rand(0, $image_h);
            
            (rand(0, 100) < 50) ? $st_y = 0
                                : $st_x = 0;
            (rand(0, 100) < 50) ? $en_x = $image_w
                                : $en_y = $image_h;
            if ($st_x == $en_x) {
                $st_x = $image_w - $st_x;
            }
            if ($st_y == $en_y) {
                $st_y = $image_h - $st_y;
            }
            
            ($x < 50) ? $col = $lines2
                        : $col = $lines;

            ImageLine($im, $st_x, $st_y, $en_x, $en_y, $col);
        }
        
        //-------------------------------------------------
        // Write out characters to canvas
        
        $x_ptr = ($image_w - $t_width) / 2;
        $y_ptr = $image_h - ($image_h - $characters[0]['height']) / 2;
        
        for ($x=0; $x < sizeof($characters); $x++) {
            $characters[$x]['x'] = $x_ptr;
            $characters[$x]['y'] = $y_ptr;
            
            ImageTTFText(
                $im,
                $characters[$x]['size'],
                $characters[$x]['angle'],
                $x_ptr,
                $y_ptr,
                $characters[$x]['color'],
                $characters[$x]['font'],
                $characters[$x]['char']
            );
            
            $x_ptr += $characters[$x]['width'] - abs($characters[$x]['ledge']);
            $y_ptr += rand(-2, 2);
        }
        
        //-------------------------------------------------
        // Tie characters together with thick lines to make
        // partitioning characters a challenge
            
        /*ImageSetThickness( $im, 4 );

        $x1 = $characters[0]['x'] + rand( 0, $characters[0]['width'] );
        $y1 = $characters[0]['y'] - rand( 0, $characters[0]['height'] / 2 );

        for ( $x=0; $x < sizeof($characters); $x++ ) {
            if ( $x < sizeof($characters) - 1 ) {

                $x2 = $characters[$x+1]['x'] + rand( 0, $characters[$x+1]['width'] );
                $y2 = $characters[$x+1]['y'] - rand( 0, $characters[$x+1]['height'] / 2 );

                while ( $y1 == $y2 ) {
                    $y2 = $characters[$x+1]['y'] - rand( 0, $characters[$x+1]['height'] / 2 );
                }

                ImageLine( $im, $x1, $y1, $x2, $y2, $black );

                $x1 = $x2;
                $y1 = $y2;
            }
        }*/
        
        //-------------------------------------------------
        // A few extra lines for obscurity
        
        imagesetthickness($im, 5);
        
        for ($x=0; $x < 0; $x++) {
            $st_x = 0;
            $st_y = rand(0, $image_h);
            $en_x = $image_w;
            $en_y = rand(0, $image_h);

            ImageLine($im, $st_x, $st_y, $en_x, $en_y, $lines);
        }

        //-------------------------------------------------
        // Write Image to screen
        
        ImageJPEG($im, null, 100);
        ImageDestroy($im);
        
        exit;
    }
}

//----------------------------------------------------------------------------------------
// class table_frame : Template Class for table objects to extend from
//----------------------------------------------------------------------------------------
// Depends on:	template ($TPL)
//				configuration ($CFG)
//				db_driver ($DB)
//----------------------------------------------------------------------------------------

class table_frame
{
    public $use		= array();
    public $use_val	= array();
    public $order		= '';
    public $limit		= '';
    public $condition	= '';
    public $data		= array();
    public $cquery		= null;
    
    public function _copy($tfo)
    {
        $this->use = $tfo->use;
        $this->use_val = $tfo->use_val;
        $this->order = $tfo->order;
        $this->limit = $tfo->limit;
        $this->condition = $tfo->condition;
        $this->data = $tfo->data;
        $this->cquery = $tfo->cquery;
    }
    
    public function get($mid)
    {
        global $CFG, $DB, $STD;
        return false;
    }
    
    public function getAll()
    {
        global $CFG, $DB, $STD;
        
        $qparts = $this->query_build();
        
        $this->cquery = $DB->query("SELECT {$qparts['select']} FROM {$qparts['from']} ".
                                   "WHERE 1=1 {$this->condition} {$this->order} {$this->limit}");
        
        return $this->cquery;
    }
    
    public function countAll()
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qp = $this->query_build();
        
        $DB->query("SELECT COUNT(*) AS cnt FROM {$qp['from']} WHERE 1=1 {$this->condition}");
        return $DB->fetch_row();
    }
    
    public function nextItem()
    {
        global $DB;
        $this->data = $DB->fetch_row($this->cquery);
        return $this->data;
    }
    
    public function create($data = array())
    {
        $this->clean($data);
    }
    
    public function insert()
    {
        global $CFG, $DB;
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
    }
    
    public function clean($data)
    {
        $this->data = array();
    }
    
    public function prefix($prefix)
    {
        $ndata = array();
        
        reset($this->data);
        while (list($k, $v) = each($this->data)) {
            $ndata[$prefix.$k] = $v;
        }
        
        return $ndata;
    }
    
    public function clear_prefix($data, $prefix)
    {
        $ndata = array();
        
        reset($data);
        while (list($k, $v) = each($data)) {
            $k = preg_replace("/^$prefix/", '', $k);
            $ndata[$k] = $v;
        }
        
        return $ndata;
    }
    
    public function query_use($item, $val=null)
    {
        global $STD;
    }
    
    public function query_unuse($item)
    {
        if (($key = array_search($item, $this->use)) !== false) {
            unset($this->use[$key]);
        }
    }
    
    public function query_order($by, $dir)
    {
        //if (strpos ($by, ',') === false) {
        $this->order = "ORDER BY $by $dir";
        //}
        //else {
        //	$bys = preg_split ("/\s*,\s*/", $by);
        //	$this->order = "ORDER BY";
        //	foreach ($bys as $bit) {
        //		$this->order .= " {$bit} {$dir},";
        //	}
        //	$this->order = preg_replace ("/,$/", '', $this->order);
        //}
    }
    
    public function query_limit($st, $ln)
    {
        $st = intval($st);
        $ln = intval($ln);
        
        $this->limit = "LIMIT $st,$ln";
    }
    
    public function query_condition($cond)
    {
        $this->condition .= " AND $cond";
    }
    
    public function clear_condition()
    {
        $this->condition = "";
    }
    
    public function query_build()
    {
        global $CFG;
        return array('select' => '', 'from' => '');
    }
    
    public function query_build_nolink()
    {
        global $CFG;
        return array('select' => '', 'from' => '');
    }
    
    public function compiled_select($table, $p)
    {
        switch ($table) {
            case 'users':
                return ",{$p}.uid {$p}_uid,{$p}.username {$p}_username,{$p}.email {$p}_email,{$p}.website ".
                       "{$p}_website,{$p}.weburl {$p}_weburl,{$p}.show_email {$p}_show_email,{$p}.icon {$p}_icon,".
                       "{$p}.aim {$p}_aim,{$p}.icq {$p}_icq,{$p}.msn {$p}_msn,{$p}.yim {$p}_yim,{$p}.comments ".
                       "{$p}_comments,{$p}.new_msgs {$p}_new_msgs,{$p}.join_date {$p}_join_date,{$p}.timezone ".
                       "{$p}_timezone,{$p}.dst {$p}_dst,{$p}.icon_dims {$p}_icon_dims,{$p}.cur_msgs {$p}_cur_msgs,".
                       "{$p}.show_thumbs {$p}_show_thumbs,{$p}.use_comment_msg {$p}_use_comment_msg,".
                       "{$p}.use_comment_digest {$p}_use_comment_digest,{$p}.last_visit {$p}_last_visit,".
                       "{$p}.last_activity {$p}_last_activity";
            case 'groups':
                return ",{$p}.gid {$p}_gid,{$p}.name_prefix {$p}_name_prefix,{$p}.name_suffix {$p}_name_suffix,".
                       "{$p}.use_bbcode {$p}_use_bbcode";
            case 'resources':
                return ",{$p}.rid {$p}_rid,{$p}.eid {$p}_eid,{$p}.uid {$p}_uid,{$p}.title {$p}_title,".
                       "{$p}.author_override {$p}_author_override,{$p}.type {$p}_type,{$p}.comments {$p}_comments,".
                       "{$p}.comment_date {$p}_comment_date";
            case 'comments':
                return ",{$p}.cid {$p}_cid,{$p}.rid {$p}_rid,{$p}.uid {$p}_uid,{$p}.date {$p}_date,{$p}.message ".
                       "{$p}_message,{$p}.ip {$p}_ip";
            case 'news':
                return ",{$p}.nid {$p}_nid,{$p}.uid {$p}_uid,{$p}.date {$p}_date,{$p}.title {$p}_title,".
                       "{$p}.message {$p}_message,{$p}.comments {$p}_comments";
            case 'messages':
                return ",{$p}.mid {$p}_mid,{$p}.sender {$p}_sender,{$p}.receiver {$p}_receiver,{$p}.owner {$p}_owner,".
                       "{$p}.date {$p}_date,{$p}.title {$p}_title,{$p}.message {$p}_message,{$p}.msg_read ".
                       "{$p}_msg_read,{$p}.read_date {$p}_read_date";
        }
        
        return '';
    }
}

function php_err_handler($no, $str, $file, $line, $ctx)
{
    global $STD;
    
    switch ($no) {
        case 2: $no = 'E_WARNING'; break;
        case 8: $no = 'E_NOTICE'; break;
        case 256: $no = 'E_USER_ERROR'; break;
        case 512: $no = 'E_USER_WARNING'; break;
        case 1024: $no = 'E_USER_NOTICE'; break;
    }
    
    $STD->template->php_errors[] = "<b>$no</b>: $str in <b>$file</b> on line <b>$line</b>";
}
