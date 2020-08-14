<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// component/resourcedb.php --
// Displays submissions
//------------------------------------------------------------------

$component = new component_resourcedb;

class component_resourcedb {
	
	var $html		= "";
	var $mod_html	= "";
	var $output		= "";
	
	function init () {
		global $IN, $STD;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		$this->html = $STD->template->useTemplate('resdb');
		
		if (!empty($IN['c'])) {
			$module = $STD->modules->get_module($IN['c']);
			
			if (!$module)
				$STD->error("The selected module does not exist.");
			
			$this->mod_html = $STD->template->useTemplate( $module['template'] );
		}
		
		switch ($IN['param']) {
			case 1: $this->show_list(); break;
			case 2: $this->show_page(); break;
			case 3: $this->do_download(); break;
			case 4: $this->version_history(); break;
		}
		
		//$TPL->template = $this->output;
		$STD->template->display( $this->output );
	}
	
	function show_list() {
		global $DB, $IN, $CFG, $TAG, $STD, $session;
		
		if (empty($IN['st']))
			$IN['st'] = 0;
		
		if (empty($IN['o']))
			$IN['o'] = '';
			
		if (empty($IN['filter']))
			$IN['filter'] = '';
			
		// Should we re-format the filter?
		if (!empty($IN['filter']) && is_array($IN['filter'])) {
			$nf = '';
			reset($IN['filter']);
			while (list($k,$v) = each($IN['filter'])) {
				if ($v > 0)
					$nf .= ",{$k}.{$v}";
			}
			$nf = preg_replace("/^,/", "", $nf);

			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=01&c={$IN['c']}&o={$IN['o']}&filter=$nf");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}
		
		// Should we re-format the order?
		if (!empty($IN['o1'])) {
			$order = "{$IN['o1']},{$IN['o2']}";
			
			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=01&c={$IN['c']}&st={$IN['st']}&o=$order&filter={$IN['filter']}");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}

		// On Then!
		$module_record = $STD->modules->get_module($IN['c']);

		if ($module_record['hidden'])
			$STD->error("This module cannot be indexed.");
			
		$module = $STD->modules->new_module($IN['c']);

		$module->init();
		
		//------------------------------------------------
		// Filter Boxes
		//------------------------------------------------
		
		if (empty($IN['filter']))
			$filter = array();
		elseif (!is_array($IN['filter'])) {
			$filter = array();
			$tfilter = explode(',', $IN['filter']);
			while (list(,$v) = each($tfilter)) {
				$pair = explode(".", $v);
				$filter[$pair[0]] = $pair[1];
			}
		}
		else
			$filter = $IN['filter'];

		$DB->query("SELECT f.fid,f.gid,f.name,g.name AS group_name,g.keyword,u.precedence AS ugid ".
		           "FROM {$CFG['db_pfx']}_filter_use u ".
				   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_group g ON (g.gid = u.gid) ".
				   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_list f ON (f.gid = g.gid) ".
				   "WHERE u.mid = {$module_record['mid']} ORDER BY f.name");
		
		$groups = array();
		while ($row = $DB->fetch_row()) {
			$gid = $row['gid'];
			$uid = $row['ugid'];
			if (!isset($groups[$uid]))
				$groups[$uid] = array('narr' => array('---'), 'varr' => array(0), 'gif' => $gid, 'gn' => $row['group_name']);
			
			$groups[$uid]['gid'] = $gid;
			$groups[$uid]['narr'][] = $row['name'];
			$groups[$uid]['varr'][] = $row['fid'];
		}
		
		ksort($groups);
		
		$boxes = '';
		while (list($k,$v) = each($groups)) {
			$k = $v['gid'];
			(!empty($filter[$k]))
				? $selected = $filter[$k] : $selected = 0;
				
			$box = $STD->make_select_box("filter[$k]", $v['varr'], $v['narr'], $selected, 'selectbox');
			$boxes .= $this->html->filter_box($v['gn'], $box);
		}
		
		//------------------------------------------------
		// Start Page
		//------------------------------------------------
		
		$filter_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=01&c={$IN['c']}&o={$IN['o']}");
		
		$this->output .= $STD->global_template->page_header($module_record['full_name']);
		
		$this->output .= $this->html->filter_row($boxes, $filter_url);
		$this->output .= $this->html->start_rows();
		
		//------------------------------------------------
		// Sort out filtering, ordering, etc
		//------------------------------------------------

		$order_names = array('t' => 'Title', 'a' => 'Author', 'd' => 'Date', 'u' => 'Updated', 'c' => 'Comments', 'cd' => 'Comment Date');
		$order_list = array('t' => 'r.title', 'a' => "CONCAT(r.author_override,IFNULL(ru.username,''))",
						    'd' => 'r.rid', 'u' => 'IF(r.updated>0,r.updated,r.rid)',
						    'c' => 'r.comments', 'cd' => 'IF(r.comment_date>0,r.comment_date,r.created)');
		$order_default = array($CFG['default_order_by'], $CFG['default_order']);
		
		$ex_order = $module->extra_order();
		$order_names = array_merge($order_names, $ex_order[0]);
		$order_list = array_merge($order_list, $ex_order[1]);
		
		// Set some defaults for the order boxes
		if (!empty($STD->user['order_def_by']))
			$order_default[0] = $STD->user['order_def_by'];
		if (!empty($STD->user['order_def']))
			$order_default[1] = $STD->user['order_def'];
		if (!empty($IN['o']))
			$order_default = explode(',', $IN['o']);
		
		$order = $STD->order_translate( $order_list, $order_default );
	//	$order_links = $STD->order_links( $order_list, $order_url, $order_default );

		//------------------------------------------------
		// Resource Rows
		//------------------------------------------------
		
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		$RES->query_use('r_user');
		if (sizeof($filter) > 0)
			$RES->query_use('filter', $filter);
		$RES->query_order($order[0], $order[1]);
		$RES->query_limit($IN['st'], $STD->get_page_prefs());
		$RES->query_condition('r.queue_code IN (0,2)');
		$RES->query_condition('r.accept_date > 0');
		$RES->getByType($IN['c']);

		$rowlist = array();
		
		while ($RES->nextItem()) {
			$data = $module->resdb_prep_data($RES->data);
			$this->output .= $this->mod_html->resdb_row($data);
		}
		
		$DB->free_result();
		
		$RES->query_unuse('extention');
		$RES->query_unuse('r_user');
		$RES->clear_condition();
		$RES->query_condition('r.queue_code IN (0,2)');
		$RES->query_condition('r.accept_date > 0');
		
		//------------------------------------------------
		// Page Numbering and Ordering
		//------------------------------------------------
		
		$order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=01&c={$IN['c']}&st={$IN['st']}&filter={$IN['filter']}");
		$order_p = @join(',', $order_default);
		
		$rcnt = $RES->countByType($IN['c']);
		$pages = $STD->paginate($IN['st'], $rcnt['cnt'], $STD->get_page_prefs(), "act=resdb&param=01&c={$IN['c']}&o=$order_p&filter={$IN['filter']}");

		$selbox1 = $STD->make_select_box('o1', array_keys($order_names), array_values($order_names), $order_default[0], 'selectbox');
		$selbox2 = $STD->make_select_box('o2', array('a','d'), array('Ascending Order','Descending Order'), $order_default[1], 'selectbox');
		
		$this->output .= $this->html->end_rows();
		$this->output .= $this->html->row_footer($pages, "$selbox1$selbox2", $order_url);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function show_page () {
		global $DB, $IN, $CFG, $STD, $session;
		
		require_once ROOT_PATH.'lib/message.php';
		require_once ROOT_PATH.'component/main.php';
		
		if (!empty($IN['st']) && $IN['st'] == 'new') {
			$component->last_unread_comments(1, $IN['id'], "act=resdb&param=02&c={$IN['c']}&id={$IN['id']}");
		}
		
		$module = $STD->modules->new_module($IN['c']);
		$module->init();
		
		$module_record = $STD->modules->get_module($IN['c']);
		
		//------------------------------------------------
		// Resource
		//------------------------------------------------
	
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		$RES->query_use('r_user');
		if (!$RES->get($IN['id']))
			$STD->error("Invalid resource selected");
		
		if (!in_array($RES->data['queue_code'], array(0,2)) && $RES->data['uid'] != $STD->user['uid'])
			$STD->error("You do not have permission to view this resource.");

		$data = $module->resdb_prep_page_data($RES->data);
		
		$data['report_url'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=05&type=1&id={$IN['id']}");
		
		$this->output .= $STD->global_template->page_header($module_record['full_name']);
		$this->output .= $this->mod_html->resdb_page($data);
		
		//------------------------------------------------
		// Comments
		//------------------------------------------------

		$this->output .= $component->build_comments(1, $IN['id'], "act=resdb&param=02&c={$IN['c']}&id={$IN['id']}");
		
		$this->output .= $STD->global_template->page_footer();
		
		$RES->data['views']++;
		$RES->update();
		
		$session->touch_data ('rr');		
		if ($RES->data['comment_date'] > $STD->user['last_visit']) {
			$session->data['rr'][$RES->data['rid']] = time();
			//$session->add_read_resource($RES->data['rid']);
			//$session->save_read_resources();
		}
	}
	
	function do_download () {
		global $DB, $IN, $CFG, $STD, $session;
		
		require ROOT_PATH.'mime_info.php';
		
		$valid = explode(",", $CFG['link_domains']);
		$pass = 0;
		
		foreach ($valid as $v) {
			if (!isset($_SERVER['HTTP_REFERER'])) {
				$pass = 1;
			} else if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $v) !== FALSE) {
				$pass = 1;
			}
		}
		
		if (!$pass) {
			$newurl = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=02&c={$IN['c']}&id={$IN['id']}");
			$STD->error("You are attempting to download a file from another site.  
						 Not only is hotlinking a theft of our bandwidth, but the sites that typically hotlink our 
						 files have no permission from the authors to distribute their work.<br /><br />
						 You can download this file by <a href='$newurl'>visiting its page</a>.");
		}
		
		$module = $STD->modules->new_module($IN['c']);
		$module->init();
		
		$module_record = $STD->modules->get_module($IN['c']);
		
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		
		if (!$RES->get($IN['id']))
			$STD->error("Invalid resource selected");
		
		if (!in_array($RES->data['queue_code'], array(0,2)) && $RES->data['uid'] != $STD->user['uid'])
			$STD->error("You do not have permission to view this resource.");

		$file = ROOT_PATH."file/{$IN['c']}/{$RES->data['file']}";

		if (!file_exists($file))	
			$STD->error("The download for <b>{$RES->data['title']}</b> does not exist.");
		
		// count consecutive downloads
		$session->touch_data ('last_dl', 'consec_dl');
		
		if ($session->data['last_dl'] == $IN['id'])
			$session->data['consec_dl']++;
		else
			$session->data['consec_dl'] = 1;
		
		if ($session->data['consec_dl'] > $CFG['max_consec_dl'])
			$STD->error("You cannot download the same submission more than {$CFG['max_consec_dl']} times consecutively.");
		
		$session->data['last_dl'] = $IN['id'];
		
		// Fetch file data
		$filesize = filesize($file);
		
		$type = $RES->data['file_mime'];
		$name = preg_replace("/[0-9]*(\.\w+)$/", "\\1", $RES->data['file']);
		
		$RES->data['downloads']++;
		$RES->update();

		// extention..
		$filebits = explode('.', $file);
		$ext = strtoupper( $filebits[ sizeof($filebits)-1 ] );
		
		$disposition = "attachment";
		
		if (isset($MIME_INFO[$ext]))
			$disposition = $MIME_INFO[$ext][1];
		
		while (@ob_end_clean());
		//session_write_close();
		
		/*if (preg_match ("/gzip/i", $_SERVER['HTTP_ACCEPT_ENCODING']) ) {
			$cachefile = ROOT_PATH."file/c_{$IN['c']}/{$RES->data['file']}.gz";
			if (!file_exists ($cachefile) ) {
				$dt = file_get_contents ($file);
				$gzdt = gzencode ($dt, 9);
				$gzfp = fopen ($cachefile, 'w');
				fwrite ($gzfp, $gzdt);
				fclose ($gzfp);
			}
			$filesize = filesize ($cachefile);
			$file = $cachefile;
			
			header("Content-Encoding: gzip");
		}*/
	
		header("Cache-Control: ");
		header("Pragma: ");
		header("Content-Length: {$filesize}");
		header("Content-Type: {$type}");
		header("Content-Transfer-Encoding: binary");
		
		header("Content-Disposition: {$disposition}; filename=\"{$name}\"");
		header("Content-Description: \"{$name}\"");

		$fp = fopen($file, "rb");
		fpassthru($fp);
		fclose($fp);

		exit;
	}
	
	function version_history () {
		global $DB, $IN, $CFG, $STD, $session;
		
		$RES = new resource;
		
		if (!$RES->get($IN['rid']))
			$STD->error("Invalid resource selected");
		
		if (!in_array($RES->data['queue_code'], array(0,2)) && $RES->data['uid'] != $STD->user['uid'])
			$STD->error("You do not have permission to view this resource.");
		
		$fields = array('rid'	=> $IN['rid']);
		$where = $DB->format_db_where_string($fields);
		
		$list = $DB->query("SELECT * FROM {$CFG['db_pfx']}_version WHERE {$where} ORDER BY date DESC");
		
		$vh = '';
		if ($DB->get_num_rows() == 0)
			$vh = $this->html->version_empty();
		
		while ($row = $DB->fetch_row()) {
			$vdate = $STD->make_date_short($row['date']);
			$vh .= $this->html->version_row($vdate, $row['change']);
		}
		
		$STD->popup_window = 1;
		$this->output .= $this->html->version_history($vh, $RES->data['title']);
	}
}
?>