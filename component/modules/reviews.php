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

class mod_reviews extends module {
	
	function init () {
		global $CFG;
		
		$this->extable = $CFG['db_pfx'].'_res_reviews';
		
		$this->file_restrictions = array();
	}
	
	function get_max_sizes() {
		return array();
	}
	
	function return_ex_data (&$resdata) {
		$exdata = array();
		
		$exdata['e.gid']			= (!isset($resdata['gid']))				? 0		: $resdata['gid'];
		$exdata['e.views']			= (!isset($resdata['views']))			? 0		: $resdata['views'];
		$exdata['e.commentary']		= (!isset($resdata['commentary']))		? ''	: $resdata['commentary'];
		$exdata['e.pros']			= (!isset($resdata['pros']))			? ''	: $resdata['pros'];
		$exdata['e.cons']			= (!isset($resdata['cons']))			? ''	: $resdata['cons'];
		$exdata['e.gameplay']		= (!isset($resdata['gameplay']))		? ''	: $resdata['gameplay'];
		$exdata['e.graphics']		= (!isset($resdata['graphics']))		? ''	: $resdata['graphics'];
		$exdata['e.sound']			= (!isset($resdata['sound']))			? ''	: $resdata['sound'];
		$exdata['e.replay']			= (!isset($resdata['replay']))			? ''	: $resdata['replay'];
		$exdata['e.gameplay_score']	= (!isset($resdata['gameplay_score']))	? 0		: $resdata['gameplay_score'];
		$exdata['e.graphics_score']	= (!isset($resdata['graphics_score']))	? 0		: $resdata['graphics_score'];
		$exdata['e.sound_score']	= (!isset($resdata['sound_score']))		? 0		: $resdata['sound_score'];
		$exdata['e.replay_score']	= (!isset($resdata['replay_score']))	? 0		: $resdata['replay_score'];
		$exdata['e.score']			= (!isset($resdata['score']))			? 0		: $resdata['score'];
		//$exdata['e.oldscore']		= (!isset($resdata['oldscore']))		? 0		: $resdata['oldscore'];
		
		return $exdata;
	}
	
	function extra_order () {
		
		$order_names = array('v' => 'Views', 's' => 'Score');
		$order_list = array('v' => 'e.views', 's' => 'e.score');
		
		return array($order_names, $order_list);
	}
	
	function update_block ($module, $time, $limit) {
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
		$RES->getByType($module['mid']);
			
		$num_items = 0;
		$output = $shtml->news_update_block_header( $module['full_name'] );
		
		while ($RES->nextItem())
		{
			$RES->data['url'] = $STD->encode_url('index.php', "act=resdb&param=02&c={$RES->data['type']}&id={$RES->data['rid']}");
			$RES->data['username'] = $STD->format_username($RES->data, 'ru_');
			$RES->data['thumbnail'] = "<img src='thumbnail/2/{$RES->data['thumbnail']}' />";
			$RES->data['description'] = $STD->nat_substr($RES->data['description'], 100) . ' ...';
			
			(!empty($RES->data['l_short_name']))
				? $RES->data['type'] = $RES->data['l_short_name'] : $RES->data['type'] = $RES->data['l_name'];
			
			$output .= $shtml->news_update_block_row( $RES->data );	

			$num_items++;
		}
		
		$output .= $shtml->news_update_block_footer();
		
		if (!$num_items)
			$output = '';
		
		return $output;
	}
		
	//-------------------------------------------------------------------------------------------------
	// Data Check Functions
	//-------------------------------------------------------------------------------------------------
	
	function common_data_check () {
		global $IN, $STD;
		
		// Check for completed required fields
		if (empty($IN['gid']))
			$this->error_save("No game is associated with this review.");
		
		if (empty($IN['commentary']) || empty($IN['pros']) || empty($IN['cons']) || empty($IN['gameplay']) ||
			empty($IN['graphics']) || empty($IN['sound']) || empty($IN['replay']) || empty($IN['description']))
			$this->error_save("You must fill out all fields.");
		
		if (empty($IN['graphics_score']) || empty($IN['gameplay_score']) || empty($IN['sound_score']) ||
			empty($IN['replay_score']) || empty($IN['score']))
			$this->error_save("You must assign a score to each field, and give an overall score.");
		
		// Make sure we can write a review for this type
		$RES = new resource;
		if (!$RES->get($IN['gid']))
			$this->error_save("An invalid game was associated with this review.");
		
		if (!$STD->modules->bound_parent($IN['c'], $RES->data['type']))
			$this->error_save("An invalud game was associated with this review.");
	}
	
	function user_submit_data_check () {
		global $IN, $STD;
		
		$this->common_data_check();
	}
	
	function user_manage_data_check () {
		global $IN, $STD;
		
		$this->common_data_check();
		
		if (empty($IN['reason']))
			$STD->error("You must give a reason for this update.  This will appear in your submission's update box.  Your changes may not be accepted without a valid reason.");
	}
	
	function acp_data_check () {
		global $IN, $STD;
		
		$this->common_data_check();
		
		if (empty($IN['author']) && empty($IN['author_override']))
			$STD->error("You must provide either a valid Creator/Username, or a Username Override, or both.");
		
		if (empty($IN['admincomment']) && empty($IN['omit_comment']) && !empty($IN['author']))
			$STD->error("You did not choose to omit an admin comment.  Please go back and enter one.");
		
		if (!empty($IN['author'])) {
			$USER = new user;
			if (!$USER->getByName($IN['author']))
				$STD->error("Invalid Creator/Username entered.  Leave blank to now associate a registered user.");
		}
	}
	
	//-------------------------------------------------------------------------------------------------
	// Data Display Prep Functions
	//-------------------------------------------------------------------------------------------------
	
	function common_prep_data (&$row) {
		global $IN, $STD;
		
		$data['rid'] = $row['rid'];
		$data['type'] = $row['type'];
		$data['description'] = $row['description'];
		$data['title'] = $row['title'];
		$data['username'] = $row['ru_username'];
		$data['author_override'] = $row['author_override'];
		$data['website_override'] = $row['website_override'];
		$data['weburl_override'] = $row['weburl_override'];
		$data['gid'] = $row['gid'];
		$data['views'] = $row['views'];
		$data['update_reason'] = $row['update_reason'];

		(empty($row['created']))
			? $data['created'] = 'Unknown'
			: $data['created'] = $STD->make_date_time($row['created']);
		(empty($row['updated']))
			? $data['updated'] = 'Never'
			: $data['updated'] = $STD->make_date_time($row['updated']);
		
		$data['commentary'] = $row['commentary'];
		$data['pros'] = $row['pros'];
		$data['cons'] = $row['cons'];
		$data['gameplay'] = $row['gameplay'];
		$data['graphics'] = $row['graphics'];
		$data['sound'] = $row['sound'];
		$data['replay'] = $row['replay'];
		$data['gameplay_score'] = $row['gameplay_score'];
		$data['graphics_score'] = $row['graphics_score'];
		$data['sound_score'] = $row['sound_score'];
		$data['replay_score'] = $row['replay_score'];
		$data['score'] = $row['score'];
		
		$module = $STD->modules->get_module($data['type']);
		
		$data['type_name'] = $module['full_name'];
		
		$GAME = new resource;
		$GAME->query_use('r_user');
		
		if ( $GAME->get( $data['gid'] ) ) {
			$url = $STD->encode_url("index.php", "act=resdb&param=02&c={$GAME->data['type']}&id={$GAME->data['rid']}");
			$data['game_title'] = "<a href='$url'>{$GAME->data['title']}</a>";
			$data['game_author'] = $STD->format_username( $GAME->data, 'ru_' );
		} else {
			$data['game_title'] = 'N/A';
			$data['game_author'] = 'N/A';
		}
		
		return $data;
	}
	
	//-------------------------------------------------------------------------------------------------
	// Data Display Prep Functions :: Editing Subset
	//-------------------------------------------------------------------------------------------------
	
	function common_edit_prep_data (&$row) {
		global $IN, $STD, $DB, $CFG, $session;
		
		$data = $this->common_prep_data($row);
		
		$data['description']	= $STD->br2nl($data['description']);
		$data['commentary'] 	= $STD->br2nl($data['commentary']);
		$data['pros'] 			= $STD->br2nl($data['pros']);
		$data['cons'] 			= $STD->br2nl($data['cons']);
		$data['gameplay'] 		= $STD->br2nl($data['gameplay']);
		$data['graphics'] 		= $STD->br2nl($data['graphics']);
		$data['sound'] 			= $STD->br2nl($data['sound']);
		$data['replay'] 		= $STD->br2nl($data['replay']);
		
		$scores_v = array('0','1','2','3','4','5','6','7','8','9','10');
		$scores_n = array('---','&nbsp;1 / 10','&nbsp;2 / 10','&nbsp;3 / 10','&nbsp;4 / 10','&nbsp;5 / 10',
								'&nbsp;6 / 10','&nbsp;7 / 10','&nbsp;8 / 10','&nbsp;9 / 10','10 / 10');
		
		$data['gameplay_score'] = $STD->make_select_box('gameplay_score', $scores_v, $scores_n, $data['gameplay_score'], 'selectbox');
		$data['graphics_score'] = $STD->make_select_box('graphics_score', $scores_v, $scores_n, $data['graphics_score'], 'selectbox');
		$data['sound_score'] 	= $STD->make_select_box('sound_score', $scores_v, $scores_n, $data['sound_score'], 'selectbox');
		$data['replay_score'] 	= $STD->make_select_box('replay_score', $scores_v, $scores_n, $data['replay_score'], 'selectbox');
		$data['score'] 			= $STD->make_select_box('score', $scores_v, $scores_n, $data['score'], 'selectbox');

		return $data;
	}
	
	function submit_prep_data () {
		global $IN, $STD, $session;
		
		$res = new resource;
		$res->query_use('extention', $IN['c']);
		
		// Recover from error?
		$session->touch_data ('err_save');
		if (!empty ($session->data['err_save']) ) {
			$err = $session->data['err_save'];
			$res->create($err);
		}
		else
			$res->create();
		
		$res->data = array_merge($res->data, $res->clear_prefix($this->return_ex_data($res->data), 'e.'));
		$res->data['rid'] = 0;
		$res->data['ru_username'] = '';
		$res->data['gid'] = $IN['gid'];

		$data = $this->common_edit_prep_data($res->data);

		return $data;
	}
	
	function manage_prep_data (&$row) {
		global $IN, $STD;
		
		$data = $this->common_edit_prep_data($row);

		return $data;
	}
	
	function acp_edit_prep_data (&$row) {
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
		
		return $data;
	}
	
	//-------------------------------------------------------------------------------------------------
	// Data Display Prep Functions :: Viewing Subset
	//-------------------------------------------------------------------------------------------------
	
	function common_view_prep_data (&$row) {
		global $IN, $STD, $DB, $CFG;
		
		$data = $this->common_prep_data($row);
		
		$data['author'] = $STD->format_username($row, 'ru_');
		$data['email_icon'] = $this->get_email_icon($row, 'ru_');
		$data['website_icon'] = $this->get_website_icon($row, 'ru_');
		
		$data['title'] = $STD->safe_display($data['title']);
		
		return $data;
	}
	
	function resdb_prep_data (&$row) {
		global $IN, $STD;
		
		$data = $this->common_view_prep_data($row);
		
		$data['created'] = $STD->make_date_short($row['created']);
		
		if (strlen($data['description']) > 250)
			$data['description'] = $STD->nat_substr($data['description'], 250) . ' ...';
		
		$data['file_url'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=resdb&param=02&c={$IN['c']}&id={$data['rid']}");
		
		$page_icon = "<img src=\"{$STD->tags['image_path']}/viewpagevw.gif\" border=\"0\" alt=\"[Page]\" style=\"display:inline; vertical-align:middle\" title=\"View Submission's Page\" />";
		
		$data['page_icon'] = "<a href=\"{$data['file_url']}\">$page_icon</a>";

		(!$row['updated'])
			? $data['updated'] = ''
			: $data['updated'] = 'Updated: ' . $STD->make_date_short($row['updated']);
			
		if (!empty($row['updated']) && time() - $row['updated'] < 60*60*24*14)
			$data['updated'] = "<span class='highlight'>{$data['updated']}</span>";
			
		return $data;
	}
	
	function resdb_prep_page_data (&$res) {
		global $IN, $STD, $DB, $CFG;
		
		$data = $this->common_view_prep_data($res);
		
		$data['created'] = $STD->make_date_time($res['created']);

		if (time() - $res['updated'] < 60*60*24*14)
			$data['updated'] = "<span class='highlight'>{$data['updated']}</span>";
		
		$score_img = array('', '1_10.gif', '2_10.gif', '3_10.gif', '4_10.gif', '5_10.gif',
							   '6_10.gif', '7_10.gif', '8_10.gif', '9_10.gif', '10_10.gif');
		//Adding in support for my -1/10 review :P
		$score_img[-1] = '-1_10.gif';
		
		$img_path = "{$STD->tags['root_path']}/template/modules/{$data['type']}/{$score_img[$data['score']]}";
		$data['score'] = "<img src='$img_path' border='0' alt='{$data['score']} / 10' />";
		
		return $data;
	}
	
	//-------------------------------------------------------------------------------------------------
	// Data Manipulating and Updating Functions
	//-------------------------------------------------------------------------------------------------
	
	function common_update_data () {
		global $IN, $STD;
		
		$auxdata = array();
		
		$RES = new resource;
		$RES->query_use('extention', str_replace('tsms_', '', $this->extable));
		
		if (!isset($IN['rid']) || !$RES->get($IN['rid']))
			$RES->create();
		
		$ORIG = $RES->data;
		
		$RES->data['description'] = $IN['description'];
		$RES->data['commentary'] = $IN['commentary'];
		$RES->data['pros'] = $IN['pros'];
		$RES->data['cons'] = $IN['cons'];
		$RES->data['gameplay'] = $IN['gameplay'];
		$RES->data['graphics'] = $IN['graphics'];
		$RES->data['sound'] = $IN['sound'];
		$RES->data['replay'] = $IN['replay'];
		$RES->data['gameplay_score'] = $IN['gameplay_score'];
		$RES->data['graphics_score'] = $IN['graphics_score'];
		$RES->data['sound_score'] = $IN['sound_score'];
		$RES->data['replay_score'] = $IN['replay_score'];

		$RES->data['oldscore'] = $RES->data['score'];
		if ($RES->data['score'] == 0)
			$RES->data['oldscore'] = -1;
		$RES->data['score'] = $IN['score'];
		
		return array($RES, $auxdata, $ORIG);
	}
	
	function user_update_submit_data () {
		global $IN, $STD;
		
		list($RES, $auxdata) = $this->common_update_data();
		
		$RES->data['uid'] = $STD->user['uid'];
		$RES->data['type'] = $IN['c'];
		$RES->data['queue_code'] = 1;
		$RES->data['gid'] = $IN['gid'];
		
		$GAME = new resource;
		$GAME->get($IN['gid']);
		if (!$GAME)
			$STD->error("Newly linked game could not be found.");
				
		$RES->data['title'] = "Review: {$GAME->data['title']}";
		
		$RES->insert();
		
		return $RES;
	}
	
	function user_update_manage_data () {
		global $IN, $STD;
		
		list ($RES, $auxdata, $ORIG) = $this->common_update_data();

		$RES->data['updated'] = time();
		
		$RES->data['update_reason'] = $IN['reason'];
		
		$fields = $RES->data;
		$RES->data = $ORIG;
		
		if ($RES->data['queue_code'] == 0)
			$this->mod_action($RES, 'r');
		
		$ghost = $RES->create_ghost($fields);
		
		return $RES;
	}
	
	function acp_update_data () {
		global $IN, $STD;
		
		list($RES, $auxdata) = $this->common_update_data();
		
		$RES->data['author_override'] = $IN['author_override'];
		$RES->data['website_override'] = $IN['website_override'];
		$RES->data['weburl_override'] = $IN['weburl_override'];
		
		$RES->data['title'] = $IN['title'];
		
		if ($RES->data['gid'] != $IN['gid']) {
			$RES->data['gid'] = $IN['gid'];
			
			$GAME = new resource;
			$GAME->get($IN['gid']);
			if (!$GAME)
				$STD->error("Newly linked game could not be found.");
			
			$RES->data['title'] = "Review: {$GAME->data['title']}";
		}
		
		if (!empty($IN['author'])) {
			$USER = new user;
			$USER->getByName($IN['author']);
			$RES->data['uid'] = $USER->data['uid'];
		} else {
			$RES->data['uid'] = 0;
		}
		
		if ($RES->data['queue_code'] == 0 && $RES->data['oldscore'] > 0) {
			$GAME = new resource;
			$GAME->query_use('extention', $STD->modules->parent_id($RES->data['type']));
			$GAME->get($IN['gid']);
			if (!$GAME)
				$STD->error("Linked game could not be found.");
			
			$diff = $RES->data['score'] - $RES->data['oldscore'];
			$GAME->data['rev_score'] += $diff;
			$GAME->update();
			
			$RES->data['oldscore'] = 0;
		}
		
		$RES->update();
		
		return $RES;
	}
	
	function mod_action (&$RES, $code) {
		global $STD, $CFG, $DB;
		
		$GAME = new resource;
		$GAME->query_use('extention', $STD->modules->parent_id($RES->data['type']));
		$GAME->get($RES->data['gid']);
		
		$num = 0;
		$cum = 0;
			
		if ($code == 'a' || $code == 'au') {
		 	$num = 1;
			$cum = $RES->data['score'];
		}
		
		$DB->query ("SELECT score FROM {$CFG['db_pfx']}_res_reviews v
					   LEFT JOIN {$CFG['db_pfx']}_resources r ON (r.eid = v.eid)
					 WHERE r.queue_code = 0 AND r.rid <> '{$RES->data['rid']}' AND v.gid = '{$RES->data['gid']}'");
		
		while ($row = $DB->fetch_row() ) {
			$num++;
			$cum += $row['score'];
		}
			
		$GAME->data['num_revs'] = $num;
		$GAME->data['rev_score'] = $cum;
		
		$GAME->update();
	}
	
	// RECALIBRATE FUNCTION -- TO BE CALLED WHEN THE CONTAINER RESOURCE IS MODIFIED

}