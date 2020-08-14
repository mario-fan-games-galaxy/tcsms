<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/modules/gfx.php --
// Graphics Root Type module
//------------------------------------------------------------------

require_once ROOT_PATH.'component/modules/module_base.php';
require_once ROOT_PATH.'lib/resource.php';

class mod_games extends module
{
    public function init()
    {
        global $CFG;
        
        $this->extable = $CFG['db_pfx'].'_res_games';
        
        $this->file_restrictions = array(
            'file'	=> array(
                'mime'	=> array('application/zip','application/x-zip-compressed','application/x-zip'),
                'ext'	=> array('ZIP'),
                'size'	=> array(0, 1024*1024*50, '0B', '50MB'),
            ),
            'thumbnail'	=> array(
                'mime'	=>array('image/gif','image/png','image/x-png','image/x-gif'),
                'ext'	=> array('PNG','GIF'),
                'size'	=> array(0,1024*30, '0B', '30KB'),
                'width'	=> array(100, 100),
                'height'=> array(100, 100),
            ),
            'preview'	=> array(
                'mime'	=> array('image/gif','image/png','image/x-png','image/x-gif'),
                'ext'	=> array('PNG','GIF'),
                'size'	=> array(0, 1024*200, '0B', '200KB'),
                'width'	=> array(100, 320),
                'height'=> array(100, 240),
            ),);
    }
    
    public function get_max_sizes()
    {
        return array('file'			=> $this->file_restrictions['file']['size'][3],
                     'thumbnail'	=> $this->file_restrictions['thumbnail']['size'][3],
                     'preview'		=> $this->file_restrictions['preview']['size'][3]);
    }
    
    public function return_ex_data(&$resdata)
    {
        $exdata = array();
        
        $exdata['e.views']		= (!isset($resdata['views']))		? 0		: $resdata['views'];
        $exdata['e.downloads']	= (!isset($resdata['downloads']))	? 0		: $resdata['downloads'];
        $exdata['e.file']		= (!isset($resdata['file']))		? ''	: $resdata['file'];
        $exdata['e.preview']	= (!isset($resdata['preview']))		? ''	: $resdata['preview'];
        $exdata['e.thumbnail']	= (!isset($resdata['thumbnail']))	? ''	: $resdata['thumbnail'];
        $exdata['e.file_mime']	= (!isset($resdata['file_mime']))	? ''	: $resdata['file_mime'];
        $exdata['e.num_revs']	= (!isset($resdata['num_revs']))	? 0		: $resdata['num_revs'];
        $exdata['e.rev_score']	= (!isset($resdata['rev_score']))	? 0		: $resdata['rev_score'];
        
        return $exdata;
    }
    
    public function extra_order()
    {
        $order_names = array('n' => 'Downloads', 'v' => 'Views', 's' => 'Score');
        $order_list = array('n' => 'e.downloads', 'v' => 'e.views', 's' => 'e.rev_score / e.num_revs');
        
        return array($order_names, $order_list);
    }
    
    public function update_block($module, $time, $limit)
    {
        global $STD;
        
        // Sub Template
        $STPL = new template;
        $shtml = $STPL->useTemplate('mod_games');
        
        // Initialize
        $RES = new resource;
        $RES->module = $module;
        $RES->query_use('extention', $module['mid']);
        $RES->query_use('r_user');
        $RES->query_use('filter_single');
        $RES->query_condition("r.accept_date >= '$time'");
        $RES->query_condition("r.accept_date < '$limit'");
        $RES->query_condition("fg.keyword = 'GAME_TYPE'");
        $RES->query_condition("r.queue_code = '0'");
        $RES->getByType($module['mid']);
            
        $num_items = 0;
        $output = $shtml->news_update_block_header($module['full_name']);
        
        while ($RES->nextItem()) {
            $RES->data['url'] = "{%site_url%}?act=resdb&param=02&c={$RES->data['type']}&id={$RES->data['rid']}";
            $RES->data['username'] = $STD->format_username($RES->data, 'ru_', 1);
            //$RES->data['thumbnail'] = "<img src='thumbnail/2/{$RES->data['thumbnail']}' />";
            $RES->data['thumbnail'] = $this->get_thumbnail($RES->data, $STPL->determine_template());
            $RES->data['description'] = $STD->nat_substr($RES->data['description'], 100) . ' ...';
            
            (!empty($RES->data['l_short_name']))
                ? $RES->data['type'] = $RES->data['l_short_name'] : $RES->data['type'] = $RES->data['l_name'];
            
            $output .= $shtml->news_update_block_row($RES->data);
            $num_items++;
        }
        
        $output .= $shtml->news_update_block_footer();
        
        // Now we have to see if there's updated items
        $RES->clear_condition();
        $RES->query_condition("r.update_accept_date >= '$time'");
        $RES->query_condition("r.update_accept_date < '$limit'");
        $RES->query_condition("r.accept_date < '$time'");
        $RES->query_condition("r.queue_code = '0'");
        $RES->query_condition("fg.keyword = 'GAME_TYPE'");
        $RES->getByType($module['mid']);
        
        $num_upd = 0;
        $upd = $shtml->news_upd_update_block_header($module['full_name'], "nmod_{$module['mid']}_".time());
        
        while ($RES->nextItem()) {
            $RES->data['url'] = "{%site_url%}?act=resdb&param=02&c={$RES->data['type']}&id={$RES->data['rid']}";
            $RES->data['username'] = $STD->format_username($RES->data, 'ru_');
            //$RES->data['thumbnail'] = "<img src='thumbnail/2/{$RES->data['thumbnail']}' />";
            $RES->data['thumbnail'] = $this->get_thumbnail($RES->data);
            $RES->data['description'] = $STD->nat_substr($RES->data['description'], 100) . ' ...';
            
            (!empty($RES->data['l_short_name']))
                ? $RES->data['type'] = $RES->data['l_short_name'] : $RES->data['type'] = $RES->data['l_name'];
                
            $upd .= $shtml->news_update_block_row($RES->data);
            $num_upd++;
        }
        
        $upd .= $shtml->news_upd_update_block_footer();
        
        if (!$num_upd) {
            $upd = '';
        }
        
        $output .= $upd;
        
        if (!$num_items && !$num_upd) {
            $output = '';
        }
        
        return $output;
    }
        
    //-------------------------------------------------------------------------------------------------
    // Data Check Functions
    //-------------------------------------------------------------------------------------------------
    
    public function common_data_check()
    {
        global $IN, $STD;
        
        // Check for completed required fields
        if (empty($IN['cat1']) || empty($IN['cat2'])) {
            $this->error_save("You must chose a value for the genre and completion categories.", 'submit');
        }
        
        if (empty($IN['title'])) {
            $this->error_save("You must provide a title.");
        }
        
        if (empty($IN['description'])) {
            $this->error_save("You must provide a description.");
        }
    }
    
    public function user_submit_data_check()
    {
        global $IN, $STD;
        
        $this->common_data_check();
        
        if (empty($_FILES['file']) || empty($_FILES['file']['name'])) {
            $STD->error("You must provide a file.");
        }
        
        if (empty($_FILES['preview']) || empty($_FILES['preview']['name'])) {
            $STD->error("You must provide a preview screenshot.");
        }
        
        // Advanced Checking
        $this->check_file_restrictions('file', 'file', 'submit');
        $this->check_file_restrictions('preview', 'preview', 'submit');
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $this->check_file_restrictions('thumbnail', 'thumbnail', 'submit');
        }
    }
    
    public function user_manage_data_check()
    {
        global $IN, $STD;
        
        $this->common_data_check();
        
        if (empty($IN['reason'])) {
            $STD->error("You must give a reason for this update.  This will appear in your submission's update box.  Your changes may not be accepted without a valid reason.");
        }
        
        // Advanced Checking
        if (!empty($_FILES['file']['name'])) {
            $this->check_file_restrictions('file', 'file');
        }
        
        if (!empty($_FILES['preview']['name'])) {
            $this->check_file_restrictions('preview', 'preview');
        }
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $this->check_file_restrictions('thumbnail', 'thumbnail');
        }
    }
    
    public function acp_data_check()
    {
        global $IN, $STD;
        
        $this->common_data_check();
        
        if (empty($IN['author']) && empty($IN['author_override'])) {
            $STD->error("You must provide either a valid Creator/Username, or a Username Override, or both.");
        }
        
        if (empty($IN['admincomment']) && empty($IN['omit_comment']) && !empty($IN['author'])) {
            $STD->error("You did not choose to omit an admin comment.  Please go back and enter one.");
        }
        
        // Advanced Checking
        if (!empty($_FILES['file']['name'])) {
            $this->check_file_restrictions('file', 'file');
        }
        
        if (!empty($_FILES['preview']['name'])) {
            $this->check_file_restrictions('preview', 'preview');
        }
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $this->check_file_restrictions('thumbnail', 'thumbnail');
        }
        
        if (!empty($IN['author'])) {
            $USER = new user;
            if (!$USER->getByName($IN['author'])) {
                $STD->error("Invalid Creator/Username entered.  Leave blank to not associate a registered user.");
            }
        }
    }
    
    //-------------------------------------------------------------------------------------------------
    // Data Display Prep Functions
    //-------------------------------------------------------------------------------------------------
    
    public function common_prep_data(&$row)
    {
        global $IN, $STD;
        
        $data['rid'] = $row['rid'];
        $data['type'] = $row['type'];
        $data['description'] = $row['description'];
        $data['title'] = $row['title'];
        $data['username'] = $row['ru_username'];
        $data['author_override'] = $row['author_override'];
        $data['website_override'] = $row['website_override'];
        $data['weburl_override'] = $row['weburl_override'];
        $data['views'] = $row['views'];
        $data['downloads'] = $row['downloads'];
        $data['update_reason'] = $row['update_reason'];
        $data['comments'] = $row['comments'];

        (empty($row['created']))
            ? $data['created'] = 'Unknown'
            : $data['created'] = $STD->make_date_time($row['created']);
        (empty($row['updated']))
            ? $data['updated'] = 'Never'
            : $data['updated'] = $STD->make_date_time($row['updated']);
        
        $data['thumbnail'] = $this->get_thumbnail($row);
        $data['preview'] = $this->get_image($row, 'preview');
        $data['file'] = "file/{$IN['c']}/{$row['file']}";
        $data['average_score'] = empty($row['num_revs'])
            ? '__'
            : round($row['rev_score'] / $row['num_revs'], 1) . ' / 10';
        
        $module = $STD->modules->get_module($data['type']);
        
        $data['type_name'] = $module['full_name'];
        
        return $data;
    }
    
    //-------------------------------------------------------------------------------------------------
    // Data Display Prep Functions :: Editing Subset
    //-------------------------------------------------------------------------------------------------
    
    public function common_edit_prep_data(&$row)
    {
        global $IN, $STD, $DB, $CFG, $session;
        
        $data = $this->common_prep_data($row);
        
        $data['description'] = $STD->br2nl($data['description']);
        
        // Build Category Elements
        
        $DB->query("SELECT l.fid,l.name,g.keyword,m.fid as fhit FROM {$CFG['db_pfx']}_filter_list l ".
                   "LEFT JOIN {$CFG['db_pfx']}_filter_use u ON (l.gid = u.gid) ".
                   "LEFT JOIN {$CFG['db_pfx']}_filter_group g ON (l.gid = g.gid) ".
                   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_multi m ON (l.fid = m.fid AND m.rid = '{$data['rid']}') ".
                   "WHERE u.mid = '{$IN['c']}' ORDER BY l.name");

        $access = array();
        $selected = array();
        while ($arow = $DB->fetch_row()) {
            $access[] = $arow;
            if ($arow['fhit'] > 0) {
                $selected[] = $arow['fid'];
            }
        }
        
        $session->touch_data('err_save');
        if (!empty($session->data['err_save'])) {
            $err = $session->data['err_save'];
            $selected = array_merge($selected, $err['cat1']);
            $selected = array_merge($selected, $err['cat2']);
        }
        
        $data['cat1'] = $this->make_catset('COMPLETION', $access, $selected);
        $data['cat2'] = $this->make_catset('GAME_TYPE', $access, $selected);
        
        $data['cat1'] = $STD->make_select_box('cat1', $data['cat1']['value'], $data['cat1']['name'], $data['cat1']['sel'], 'selectbox');
        $data['cat2'] = $STD->make_select_box('cat2', $data['cat2']['value'], $data['cat2']['name'], $data['cat2']['sel'], 'selectbox');
        
        return $data;
    }
    
    public function submit_prep_data()
    {
        global $IN, $STD, $session;
        
        $res = new resource;
        
        // Recover from error?
        $session->touch_data('err_save');
        if (!empty($session->data['err_save'])) {
            $err = $session->data['err_save'];
            $res->create($err);
        } else {
            $res->create();
        }

        $res->data = array_merge($res->data, $res->clear_prefix($this->return_ex_data($res->data), 'e.'));
        $res->data['rid'] = 0;
        $res->data['ru_username'] = '';

        $data = $this->common_edit_prep_data($res->data);

        return $data;
    }
    
    public function manage_prep_data(&$row)
    {
        global $IN, $STD;
        
        $data = $this->common_edit_prep_data($row);

        $data['author_override'] = '';
        if (preg_match($STD->get_regex('nat_delim'), $row['author_override'])) {
            $add_authors = preg_split($STD->get_regex('nat_delim'), $row['author_override']);
            array_shift($add_authors);
            $data['author_override'] = @join(', ', $add_authors);
        }

        return $data;
    }
    
    public function acp_edit_prep_data(&$row)
    {
        global $IN, $STD;
        
        $data = $this->common_edit_prep_data($row);
        
        empty($row['ru_website'])
            ? $data['website'] = "<img src='{$STD->tags['image_path']}/not_visible.gif' alt='[X]' title='User Website: None' border='0' />"
            : $data['website'] = "<img src='{$STD->tags['image_path']}/visible.gif' alt='[O]' title='User Website: {$row['ru_website']}' border='0' />";
            
        empty($row['ru_weburl'])
            ? $data['weburl'] = "<img src='{$STD->tags['image_path']}/not_visible.gif' alt='[X]' title='User Website: None' border='0' />"
            : $data['weburl'] = "<img src='{$STD->tags['image_path']}/visible.gif' alt='[O]' title='User Website: {$row['ru_weburl']}' border='0' />";
        
        $uurl = $STD->encode_url($_SERVER['PHP_SELF'], "act=ucp&param=02&u={$row['uid']}");
        empty($row['ru_username'])
            ? $data['usericon'] = "<img src='{$STD->tags['image_path']}/not_visible.gif' alt='[X]' title='No User Associated' border='0' />"
            : $data['usericon'] = "<a href='$uurl'><img src='{$STD->tags['image_path']}/visible.gif' alt='[O]' title='Click to view user' border='0' /></a>";

        //	($STD->user['acp_users'] && !empty($row['ru_username']))
        //		? $data['usericon']['v'] = 'Click to View User'
        //		: $data['usericon']['linkvis'] = 0;
        
        return $data;
    }
    
    //-------------------------------------------------------------------------------------------------
    // Data Display Prep Functions :: Viewing Subset
    //-------------------------------------------------------------------------------------------------
    
    public function common_view_prep_data(&$row)
    {
        global $IN, $STD, $DB, $CFG;
        
        $data = $this->common_prep_data($row);
        
        $data['author'] = $STD->format_username($row, 'ru_');
        $data['email_icon'] = $this->get_email_icon($row, 'ru_');
        $data['website_icon'] = $this->get_website_icon($row, 'ru_');
        $data['filesize'] = $this->get_filesize($row);
        
        $data['title'] = $STD->safe_display($data['title']);
        $data['completion'] = '';
        $data['genre'] = '';
        
        if (!empty($IN['id'])) {
            $DB->query("SELECT l.fid,l.name,g.keyword,m.fid as fhit FROM {$CFG['db_pfx']}_filter_list l ".
                       "LEFT JOIN {$CFG['db_pfx']}_filter_use u ON (l.gid = u.gid) ".
                       "LEFT JOIN {$CFG['db_pfx']}_filter_group g ON (l.gid = g.gid) ".
                       "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_multi m ON (l.fid = m.fid AND m.rid = '{$data['rid']}') ".
                       "WHERE u.mid = '{$IN['c']}' AND m.rid = '{$data['rid']}'");

            while ($arow = $DB->fetch_row()) {
                if ($arow['fhit'] > 0 && $arow['keyword'] == 'COMPLETION') {
                    $data['completion'] = $arow['name'];
                }
                if ($arow['fhit'] > 0 && $arow['keyword'] == 'GAME_TYPE') {
                    $data['genre'] = $arow['name'];
                }
            }
        }
        
        return $data;
    }
    
    public function resdb_prep_data(&$row)
    {
        global $IN, $STD, $session;
        
        $data = $this->common_view_prep_data($row);
        
        $data['created'] = $STD->make_date_short($row['created']);
        
        if (strlen($data['description']) > 250) {
            $data['description'] = $STD->nat_substr($data['description'], 250) . ' ...';
        }

        $data['file_url'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=02&c={$IN['c']}&id={$data['rid']}");
        $data['dl_url'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=03&c={$IN['c']}&id={$data['rid']}");
        
        $page_icon = "<img src=\"{$STD->tags['image_path']}/viewpagevw.gif\" border=\"0\" alt=\"[Page]\" style=\"display:inline; vertical-align:middle\" title=\"View Submission's Page\" />";
        $dl_icon = "<img src=\"{$STD->tags['image_path']}/viewpagedn.gif\" border=\"0\" alt=\"[DL]\" style=\"display:inline; vertical-align:middle\" title=\"Download Submission\" />";
        
        if (empty($session->data['rr'])) {
            $session->data['rr'] = array();
        }
        $rr = empty($session->data['rr'][$data['rid']]) ? 0 : $session->data['rr'][$data['rid']];
        
        if ($row['comment_date'] > $STD->user['last_visit'] &&
            $row['comment_date'] > $rr) {
            $c_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=02&c={$IN['c']}&id={$data['rid']}&st=new");
            $data['new_comments'] = "<a href=\"$c_url\"><img src=\"{$STD->tags['image_path']}/newcomment.gif\" border=\"0\" alt=\"[NEW]\" style=\"display:inline; vertical-align:middle\" title=\"Goto last unread comment\" /></a>";
        } else {
            $data['new_comments'] = '';
        }
        
        $data['page_icon'] = "<a href=\"{$data['file_url']}\">$page_icon</a>";
        $data['dl_icon'] = "<a href=\"{$data['dl_url']}\">$dl_icon</a>";
        
        (!$row['updated'])
            ? $data['updated'] = ''
            : $data['updated'] = 'Updated: ' . $STD->make_date_short($row['updated']);
            
        if (!empty($row['updated']) && time() - $row['updated'] < 60*60*24*14) {
            $data['updated'] = "<span class='highlight'>{$data['updated']}</span>";
        }
        
        if (!$STD->user['show_thumbs']) {
            $data['thumbnail'] = '';
        }
            
        return $data;
    }
    
    public function resdb_prep_page_data(&$res)
    {
        global $IN, $STD, $DB, $CFG;
        
        $data = $this->common_view_prep_data($res);
        
        $data['created'] = $STD->make_date_time($res['created']);

        if (time() - $res['updated'] < 60*60*24*14) {
            $data['updated'] = "<span class='highlight'>{$data['updated']}</span>";
        }
        
        $dl_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=03&c={$IN['c']}&id={$IN['id']}");
        ($data['filesize'] == 'File Unavailable')
            ? $data['download_text'] = 'Download Unavailable'
            : $data['download_text'] = "<a href='$dl_url'>View / Download</a>";
        
        // Version History
        
        $data['version_history'] = '';
        $dblist = $this->get_version_history($IN['id']);
        $rows_returned = $DB->get_num_rows();
        if ($rows_returned == 0) {
            $data['version_history'] = "<tr><td colspan='2' align='center'>No History</td></tr>";
        }

        for ($x=0; $x<min(2, $rows_returned); $x++) {
            $row = $DB->fetch_row($dblist);
            $vdate = $STD->make_date_short($row['date']);
            $data['version_history'] .= "<tr><td width='25%' valign='top'><b>$vdate&nbsp;</b></td>
										   <td width='75%' valign='top'>{$row['change']}</td></tr>";
        }
        
        if ($rows_returned > 2) {
            $data['version_history'] .= "<tr><td colspan='2' align='center'><br /><a href='javascript:version_history()'>
										 View Complete History</a></td></tr>";
        }
        
        
        // Game Reviews
        if ($STD->modules->bound_child($res['type'], 'res_reviews')) {
            $mod_record = $STD->modules->get_module('res_reviews');
            
            $mod_html = $STD->template->useTemplate('mod_games');

            $data['reviews'] = '';
            $data['add_review'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=submit&param=02&c={$mod_record['mid']}&gid={$data['rid']}");
        
            $REVIEW = new resource;
            $REVIEW->query_use('extention', 'res_reviews');
            $REVIEW->query_use('r_user');
            $REVIEW->query_condition("r.type = '{$mod_record['mid']}'");
            $REVIEW->query_condition("e.gid = '{$IN['id']}'");
            $REVIEW->query_condition("r.queue_code = '0'");
            $REVIEW->query_order('r.created', 'DESC');
            $REVIEW->getAll();
            
            while ($REVIEW->nextItem()) {
                $REVIEW->data['author'] = $STD->format_username($REVIEW->data, 'ru_');
                $REVIEW->data['date'] = $STD->make_date_time($REVIEW->data['created']);
                $data['reviews'] .= $mod_html->game_reviews_row($REVIEW->data);
            }
        }

        return $data;
    }
    
    //-------------------------------------------------------------------------------------------------
    // Data Manipulating and Updating Functions
    //-------------------------------------------------------------------------------------------------
    
    public function common_update_data()
    {
        global $IN, $STD;
        
        $auxdata = array();
        
        $RES = new resource;
        $RES->query_use('extention', str_replace('tsms_', '', $this->extable));
        
        if (!isset($IN['rid']) || !$RES->get($IN['rid'])) {
            $RES->create();
        }
        
        $ORIG = $RES->data;
        
        $RES->data['title'] = $IN['title'];
        $RES->data['description'] = $IN['description'];
        
        $auxdata['cat_completion'] = $IN['cat1'];
        $auxdata['cat_game_type'] = $IN['cat2'];
        
        return array($RES, $auxdata, $ORIG);
    }
    
    public function user_update_submit_data()
    {
        global $IN, $STD;
        
        list($RES, $auxdata) = $this->common_update_data();
        
        $RES->data['uid'] = $STD->user['uid'];
        $RES->data['type'] = $IN['c'];
        $RES->data['queue_code'] = 1;
        
        $RES->data['file'] = $this->move_file('file', 'file', 'submit');
        $RES->data['file_mime'] = $_FILES['file']['type'];
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $RES->data['thumbnail'] = $this->move_file('thumbnail', 'thumbnail', 'submit');
        } else {
            $name = "autothumb_".time().".png";
            $src = $_FILES['preview']['tmp_name'];
            $dest = ROOT_PATH."thumbnail/{$IN['c']}/$name";
            $max_width = $this->file_restrictions['thumbnail']['width'][1];
            $max_height = $this->file_restrictions['thumbnail']['height'][1];
            $min_width = $this->file_restrictions['thumbnail']['width'][0];
            $min_height = $this->file_restrictions['thumbnail']['height'][0];
            $maketest = $this->build_thumbnail($src, $dest, $max_width, $max_height, $min_width, $min_height);
            
            if ($maketest) {
                $RES->data['thumbnail'] = $name;
            }
        }
        
        $RES->data['preview'] = $this->move_file('preview', 'preview', 'submit');
        
        $RES->insert();
        
        $values = array($auxdata['cat_completion'], $auxdata['cat_game_type']);
        $this->add_filters($RES->data['rid'], $values);
        
        return $RES;
    }
    
    public function user_update_manage_data()
    {
        global $IN, $STD;
        
        list($RES, $auxdata, $ORIG) = $this->common_update_data();
        
        $RES->data['author_override'] = '';
        if (!empty($IN['author_override'])) {
            $add_authors = preg_split($STD->get_regex('nat_delim'), $IN['author_override']);
            $add_authors = @join(', ', $add_authors);
            $RES->data['author_override'] = "{$STD->user['username']}, $add_authors";
        }
        
        if (!empty($_FILES['file']['name']) && $RES->data['accept_date'] > 0) {
            $RES->data['updated'] = time();
        }
        
        $RES->data['update_reason'] = $IN['reason'];
        
        if (!empty($_FILES['file']['name'])) {
            $RES->data['file'] = $this->move_file('file', 'file');
            $RES->data['file_mime'] = $_FILES['file']['type'];
        }
        
        if (!empty($_FILES['preview']['name'])) {
            $RES->data['preview'] = $this->move_file('preview', 'preview');
        }
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $RES->data['thumbnail'] = $this->move_file('thumbnail', 'thumbnail');
        }
        
        $fields = $RES->data;
        $RES->data = $ORIG;
        
        $ghost = $RES->create_ghost($fields);
        
        // Add Filters
        $this->clear_filters($ghost->data['rid']);
        
        $values = array($auxdata['cat_completion'], $auxdata['cat_game_type']);
        $this->add_filters($ghost->data['rid'], $values);
        
        return $RES;
    }
    
    public function acp_update_data()
    {
        global $IN, $STD;
        
        list($RES, $auxdata) = $this->common_update_data();
        
        $RES->data['author_override'] = $IN['author_override'];
        $RES->data['website_override'] = $IN['website_override'];
        $RES->data['weburl_override'] = $IN['weburl_override'];
        
        if (!empty($IN['file_name'])) {
            $RES->data['file'] = $IN['file_name'];
        }
        if (!empty($IN['preview_name'])) {
            $RES->data['preview'] = $IN['preview_name'];
        }
        if (!empty($IN['thumbnail_name'])) {
            $RES->data['thumbnail'] = $IN['thumbnail_name'];
        }
        
        if (!empty($IN['author'])) {
            $USER = new user;
            $USER->getByName($IN['author']);
            $RES->data['uid'] = $USER->data['uid'];
        } else {
            $RES->data['uid'] = 0;
        }
        
        if (!empty($_FILES['file']['name'])) {
            $RES->data['file'] = $this->move_file('file', 'file');
            $RES->data['file_mime'] = $_FILES['file']['type'];
        }
        
        if (!empty($_FILES['preview']['name'])) {
            $RES->data['preview'] = $this->move_file('preview', 'preview');
        }
        
        if (!empty($_FILES['thumbnail']['name'])) {
            $RES->data['thumbnail'] = $this->move_file('thumbnail', 'thumbnail');
        }
        
        if (!empty($RES->data['preview']) && !empty($IN['thumbnail_gen'])) {
            $name = "autothumb_".time().".png";
            $src = ROOT_PATH."preview/{$IN['c']}/{$RES->data['preview']}";
            $dest = ROOT_PATH."thumbnail/{$IN['c']}/$name";
            $max_width = $this->file_restrictions['thumbnail']['width'][1];
            $max_height = $this->file_restrictions['thumbnail']['height'][1];
            $min_width = $this->file_restrictions['thumbnail']['width'][0];
            $min_height = $this->file_restrictions['thumbnail']['height'][0];
            $maketest = $this->build_thumbnail($src, $dest, $max_width, $max_height, $min_width, $min_height);
            
            if ($maketest) {
                $RES->data['thumbnail'] = $name;
            }
        }
        
        $RES->update();
        
        // Add Filters
        $this->clear_filters($IN['rid']);
        
        $values = array($auxdata['cat_completion'], $auxdata['cat_game_type']);
        $this->add_filters($IN['rid'], $values);
        
        return $RES;
    }
}
