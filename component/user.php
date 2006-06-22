<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// component/user.php --
// Handles User CP and other user functions
//------------------------------------------------------------------

$component = new component_user;

class component_user {

	var $html 		= "";
	var $mod_html 	= "";
	var $output		= "";
	var $title		= "";
	
	function init () {
		global $IN, $STD;
		
		require ROOT_PATH.'lib/mailer.php';
		
		$this->html = $STD->template->useTemplate('user');
		
		if (!empty($IN['c'])) {
			$module = $STD->modules->get_module($IN['c']);
			$this->mod_html = $STD->template->useTemplate( $module['template'] );
		}
		
		switch ($IN['param']) {
			case 1: $this->show_user(); break;
			case 2: $this->show_ucp_prefs(); break;
			case 3: $this->show_manage_sub_list(); break;
			case 4: $this->do_edit_prefs(); break;
			case 5:	$this->get_email(); break;
			case 6: $this->show_manage_item(); break;
			case 7: $this->do_manage_item() ; break;
			case 8: $this->do_req_remove(); break;
			case 9: $this->show_public_user(); break;
		}
		
		//$TPL->template = $this->output;
		$STD->template->display( $this->output, $this->title );
	}
	
	function show_user() {
		global $IN, $STD;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		$user = new user;
		$user->query_use('group');
		
		if (empty($IN['uid']) || !$user->get($IN['uid']))
			$STD->error("User does not exist.");
		
		$RES = new resource;
		$RES->query_condition("r.uid = {$user->data['uid']}");
		$subs = $RES->countAll();
		
		$user->data['join_date'] = $STD->make_date_time($user->data['join_date']);
		$user->data['submissions'] = $subs['cnt'];
		$user->data['reviews'] = 0;
		
		$email = "<i>Not Provided</i>";
		if (!empty($user->data['email']) && $user->data['show_email']) {
			$email = $user->data['email'];
			$email = str_replace(' ', '%20', $email);
			$email = str_replace('@', ' _AT_ ', $email);
			$email = str_replace('.', ' _DOT_ ', $email);
			$email_im = "<img src='{$_SERVER['PHP_SELF']}?act=user&param=05&uid={$user->data['uid']}' border='0' alt='$email' />";
			$email = "<a href='mailto:$email'>$email_im</a>";
		}
		$user->data['email'] = $email;
		
		$website = "<i>Not Provided</i>";
		if (!empty($user->data['website']))
			$website = $user->data['website'];
		elseif (!empty($user->data['weburl']))
			$website = $user->data['weburl'];
		if (!empty($user->data['weburl']))
			$website = "<a href='{$user->data['weburl']}'>$website</a>";
		$user->data['website'] = $website;
		
		$user->data['aim'] = (!empty($user->data['aim'])) ? $user->data['aim'] : "<i>Not Provided</i>";
		$user->data['icq'] = (!empty($user->data['icq'])) ? $user->data['icq'] : "<i>Not Provided</i>";
		$user->data['msn'] = (!empty($user->data['msn'])) ? $user->data['msn'] : "<i>Not Provided</i>";
		$user->data['yim'] = (!empty($user->data['yim'])) ? $user->data['yim'] : "<i>Not Provided</i>";
		
		$this->output .= $STD->global_template->page_header($user->data['username']);
		$this->output .= $this->html->userpage($user->data);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function show_ucp_prefs() {
		global $STD, $CFG, $DB, $session;
		
		if (!$STD->user['uid'])
			$STD->error("You must be registered to access account preferences.");
		
		// We need to generate our skins list
		$query = $DB->query("SELECT sid,name FROM {$CFG['db_pfx']}_skins WHERE hidden = '0'");
		
		$skin_val = array(0); $skin_name = array('---');
		while ($srow = $DB->fetch_row()) {
			$skin_val[] = $srow['sid'];
			$skin_name[] = $srow['name'];
		}
		
		$form_elements = array();
		
		$form_elements['order_by'] = $STD->make_select_box('def_order_by', array('','d','t','a','u'), array('---','Date','Title','Author','Last Update'), $STD->user['def_order_by'], 'selectbox');
		$form_elements['order'] = $STD->make_select_box('def_order', array('','a','d'), array('---','Ascending Order','Descending Order'), $STD->user['def_order'], 'selectbox');
		$form_elements['skin'] = $STD->make_select_box('skin', $skin_val, $skin_name, $STD->user['skin'], 'selectbox');
		$form_elements['items'] = $STD->make_select_box('items_per_page', array('0','20','40','60','80','100'), array('---','20','40','60','80','100'), $STD->user['items_per_page'], 'selectbox');
		$form_elements['show_email'] = $STD->make_yes_no('show_email', $STD->user['show_email']);
		$form_elements['timezone'] = $STD->timezone_box($STD->user['timezone']);
		$form_elements['dst'] = $STD->make_checkbox('dst', 1, $STD->user['dst']);
		$form_elements['time'] = $STD->make_date_time(time());
		$form_elements['max_dims'] = $CFG['max_icon_dims'];
		$form_elements['show_thumbs'] = $STD->make_yes_no('show_thumbs', $STD->user['show_thumbs']);
		$form_elements['use_comment_msg'] = $STD->make_yes_no('use_comment_msg', $STD->user['use_comment_msg']);
		$form_elements['use_comment_digest'] = $STD->make_yes_no('use_comment_digest', $STD->user['use_comment_digest']);
		
		if (empty($STD->user['icon_dims'])) {
			$STD->user['icon_dimw'] = '';
			$STD->user['icon_dimh'] = '';
		} else {
			$dims = explode("x", $STD->user['icon_dims']);
			$STD->user['icon_dimw'] = $dims[0];
			$STD->user['icon_dimh'] = $dims[1];
		}
		
		//$this->output .= $STD->global_template->page_header('Preferences');
		$this->title = "Preferences";
		$this->output .= $this->html->prefs_page($STD->user, $form_elements, $STD->make_form_token());
		//$this->output .= $STD->global_template->page_footer();
	}
	
	function do_edit_prefs () {
		global $STD, $DB, $IN, $CFG;
		
		$updates = array();
		
		// Validation
		if (!$STD->validate_form($IN['security_token']))
			$STD->error("The update request did not originate from this site, or your request has allready been processed.");
		
		if (!$STD->user['uid'])
			$STD->error("You must be registered to perform this action.");
		
		if ($STD->user['uid'] != $IN['uid'])
			$STD->error("Attempt to modify another user's account data.");
		
		if (!preg_match($STD->get_regex('email'), $IN['email']))
			$STD->error("Email address is invalid.");
		
		if (!preg_match($STD->get_regex('url'), $IN['weburl']) || empty($IN['website']))
			$IN['weburl'] = '';
		
		if (!preg_match($STD->get_regex('url'), $IN['icon']))
			$IN['icon'] = '';
		
		if ($IN['dimw'] != "" || $IN['dimh'] != "") {
			$max_dims = explode("x", $CFG['max_icon_dims']);
		
			$IN['dimw'] = max($IN['dimw'], 1);
			$IN['dimw'] = min($IN['dimw'], $max_dims[0]);
			$IN['dimh'] = max($IN['dimh'], 1);
			$IN['dimh'] = min($IN['dimh'], $max_dims[1]);
		}
		
		// Password Change
		if (!empty($IN['opass']) || !empty($IN['npass1']) || !empty($IN['npass2'])) {
			if (empty($IN['opass']))
				$STD->error("You must type in your old password to change your password.");
			if (empty($IN['npass1']))
				$STD->error("You must provide a new password to change your password.  If you do not want to change your password, leave all password fields blank.");
			if (empty($IN['npass2']))
				$STD->error("You must retype your new password to change your password.");
			if ($IN['npass1'] != $IN['npass2'])
				$STD->error("Your new passwords did not match.  Please retype them.");
			if (md5($IN['opass']) != $STD->user['password'])
				$STD->error("Your old password was incorrect.  You must provide a correct password to change your password.");
			$updates['password'] = md5($IN['npass1']);
		}
		
		if (empty($IN['dst']))
			$IN['dst'] = 0;
		
		$updates['email'] = $IN['email'];
		$updates['website'] = $IN['website'];
		$updates['weburl'] = $IN['weburl'];
		$updates['icon'] = $IN['icon'];
		$updates['aim'] = $IN['aim'];
		$updates['icq'] = $IN['icq'];
		$updates['msn'] = $IN['msn'];
		$updates['yim'] = $IN['yim'];
		$updates['def_order_by'] = $IN['def_order_by'];
		$updates['def_order'] = $IN['def_order'];
		$updates['skin'] = $IN['skin'];
		$updates['items_per_page'] = $IN['items_per_page'];
		$updates['show_email'] = $IN['show_email'];
		$updates['timezone'] = $IN['timezone'];
		$updates['dst'] = $IN['dst'];
		$updates['icon_dims'] = "{$IN['dimw']}x{$IN['dimh']}";
		$updates['show_thumbs'] = $IN['show_thumbs'];
		$updates['use_comment_msg'] = $IN['use_comment_msg'];
		$updates['use_comment_digest'] = $IN['use_comment_digest'];
		
		$fields = $DB->format_db_update_values($updates);
		$where = $DB->format_db_where_string(array('uid'	=> $IN['uid']));
		$DB->query("UPDATE {$CFG['db_pfx']}_users SET $fields WHERE $where");
		
		//------------------------------------------------
		// Output
		//------------------------------------------------
		
		$url = $STD->encode_url($_SERVER['PHP_SELF']);
		$url2 = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=02");
		
	//	$this->output .= $this->html->page_header();
		$this->output .= $STD->global_template->message("Your account preferences were updated successfully.
								 <p align='center'><a href='$url2'>Return to User Preferences</a><br />
								 <a href='$url'>Return to the main page</a></p>");
	//	$this->output .= $this->html->page_footer();
		
		$STD->clear_form_token();
	}
	
	function show_manage_sub_list () {
		global $STD, $CFG, $DB, $IN;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		if (!$STD->user['can_modify'])
			$STD->error("You do not have permission to modify your submissions.");
		
		if (empty($IN['st']))
			$IN['st'] = 0;
		
		if (empty($IN['o']))
			$IN['o'] = null;
		
		// Should we re-format the order?
		if (!empty($IN['o1'])) {
			$order = "{$IN['o1']},{$IN['o2']}";
			
			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=03&c={$IN['c']}&st={$IN['st']}&o=$order");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}
		
		// Did we change the target?
		if (!empty($_POST['c'])) {
			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=03&c={$IN['c']}&st={$IN['st']}&o={$IN['o']}");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}

		//------------------------------------------------
		// Make sure we have a default module
		//------------------------------------------------
		
		$module_record = null;
		$STD->modules->load_module_list();
		
		$name_arr = array(); $val_arr = array();
		reset($STD->modules->module_set);
		while (list(,$row) = each ($STD->modules->module_set)) {
			$val_arr[] = $row['mid'];
			$name_arr[] = $row['full_name'];

			if ((empty($module_record) && empty($IN['c'])) || $row['mid'] == $IN['c']) {
				$module_record = $row;
				$module = $STD->modules->new_module($row['mid']);

				$this->mod_html = $STD->template->useTemplate( $module_record['template'] );
			}
		}
		
		if (empty($IN['c']))
			$IN['c'] = $val_arr[0];
		
		$js = "onchange=\"if(this.options[this.selectedIndex].value != -1){ document.changetype.submit() }\"";
		$type_list = $STD->make_select_box('c', $val_arr, $name_arr, $IN['c'], 'selectbox', $js);
		
		$module->init();
		
		//------------------------------------------------
		// Ordering and stuff
		//------------------------------------------------
		
		$order_names = array('t' => 'Title', 'a' => 'Author', 'd' => 'Date', 'u' => 'Updated');
		$order_list = array('t' => 'r.title', 'a' => "CONCAT(r.author_override,IFNULL(ru.username,''))",
						    'd' => 'r.rid', 'u' => 'IF(r.updated>0,r.updated,r.rid)');
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
		
		//------------------------------------------------
		// Start Page
		//------------------------------------------------
		
		$this->output .= $STD->global_template->page_header('My Submissions');
		$this->output .= $this->html->manage_type_row($type_list);
		
		$this->output .= $this->html->manage_start_rows();
		
		//------------------------------------------------
		// Resource Rows
		//------------------------------------------------
		
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		$RES->query_use('r_user');
		$RES->query_order($order[0], $order[1]);
		$RES->query_limit($IN['st'], $STD->get_page_prefs());
		$RES->query_condition("r.uid = '{$STD->user['uid']}' AND r.queue_code <> 5");
		$RES->getByType($IN['c']);
		
		$rowlist = array();

		while ($RES->nextItem()) {
			$data = $module->resdb_prep_data($RES->data);
			if ($RES->data['queue_code'] > 0)
				$data['title'] .= $this->html->queue_text();
				
			$this->output .= $this->mod_html->manage_row($data, $IN['c']);
		}

		$DB->free_result();
		
		//------------------------------------------------
		// Page numbering and ordering
		//------------------------------------------------
		
		$order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=03&c={$IN['c']}&st={$IN['st']}");
		$order_p = @join(',', $order_default);
		
		$rcnt = $RES->countByType($IN['c']);
		$pages = $STD->paginate($IN['st'], $rcnt['cnt'], $STD->get_page_prefs(), "act=user&param=03&c={$IN['c']}&o={$IN['o']}");
		
		$selbox1 = $STD->make_select_box('o1', array_keys($order_names), array_values($order_names), $order_default[0], 'selectbox');
		$selbox2 = $STD->make_select_box('o2', array('a','d'), array('Ascending Order','Descending Order'), $order_default[1], 'selectbox');
		
		$this->output .= $this->html->manage_end_rows($pages, "$selbox1 $selbox2", $order_url);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function get_email() {
		global $STD, $DB, $IN, $CFG;
		
		$user = new user;
		
		if (empty($IN['uid']) || !$user->get($IN['uid']))
			$STD->error("User does not exist.");
		
		if (empty($user->data['email']) || !$user->data['show_email'])
			$STD->error("This user's email address is not available.");
			
		$imglen = strlen($user->data['email'])*7 + 2;
		
		// Create Email image
		$im = imagecreate($imglen, 14);
		$bg = imagecolorallocate($im, 255, 255, 255);
		$txtcolor = imagecolorallocate($im, 0, 0, 0);
		
		imagestring($im, 3, 1, 0, $user->data['email'], $txtcolor);
		
		header("Content-type: image/png");
		imagepng($im);
		exit;
	}
	
	function show_manage_item () {
		global $STD, $DB, $IN, $CFG;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		if (!$STD->user['can_modify'])
			$STD->error("You do not have permission to modify your submissions.");
		
		$module_record = $STD->modules->get_module($IN['c']);
		
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		$RES->query_use('r_user');
		if (!$RES->get($IN['rid']))
			$STD->error("Invalid resource selected");
		
		if ($RES->data['ghost'] > 0) {
			if (!$RES->get($RES->data['ghost']))
				$STD->error("Invalid ghost data encountered");
			$RES->data['rid'] = $IN['rid'];
		}
		
		if ($RES->data['uid'] != $STD->user['uid'])
			$STD->error("You do not have permission to modify this submission.");
		
		//------------------------------------------------
		// Format Data
		//------------------------------------------------
		
		$module = $STD->modules->new_module($IN['c']);
		$module->init();
		
		$data = $module->manage_prep_data($RES->data);
		
		//------------------------------------------------
		// Output
		//------------------------------------------------
		
		$this->output .= $STD->global_template->page_header('Modify Submission');
		
		$this->output .= $this->mod_html->manage_page($data, $STD->make_form_token(), $module->get_max_sizes());
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function do_manage_item() {
		global $STD, $DB, $IN, $CFG;
		
		// Are we redirecting?
		if (!empty($IN['rem'])) {
			$STD->clear_form_token();
			$this->show_remove();
			return;
		}
		
		// No - carry on
		if (!$STD->validate_form($IN['security_token']))
			$STD->error("The submission request did not originate from this site, or you attempted to repeat a completed transaction.");
		
		if (!$STD->user['can_modify'])
			$STD->error("You do not have permission to modify your submissions.");
		
		$module = $STD->modules->new_module($IN['c']);
		if (!$module)
			$STD->error("Suitable module could not be found.");
		
		// Raw clean values (Remember to undo before display!)
		
		if (isset($IN['title']))
			$IN['title'] = $STD->rawclean_value($_POST['title']);
		
		$module->init();
		
		$module->user_manage_data_check();
		$RES = $module->user_update_manage_data();
		
		//------------------------------------------------
		// Output
		//------------------------------------------------
		
	//	$this->output .= $this->html->page_header();

		$username = htmlspecialchars($STD->user['username']);
		$url = $STD->encode_url($_SERVER['PHP_SELF']);
		$message = "Thank you, $username.  Your submission has been updated.  Your submission has been placed back 
			in the moderation queue for approval.  If there is a problem with your changes, your submission will be 
			rolled back to its previous state.  In the meantime, you can continue to make changes to your submission.
			<p align='center'><a href='$url'>Return to the main page</a></p>";
		
		$this->output .= $STD->global_template->message($message);
		
	//	$this->output .= $this->html->page_footer();
		
		$STD->clear_form_token();
	}
	
	function show_remove () {
		global $STD, $IN;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		if (empty($IN['reason']))
			$IN['reason'] = '';
			
		$RES = new resource;
		$RES->query_use('r_user');
		
		if (!$RES->get($IN['rid']))
			$STD->error("Could not find resource.");
		
		if ($STD->user['uid'] != $RES->data['uid'])
			$STD->error("You cannot remove submissions that don't belong to you.");
		
		$form_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=08");
		
		$this->output .= $STD->global_template->page_header('Request Removal');
		
		$this->output .= $this->html->request_remove($IN['rid'], $RES->data['title'], $form_url, $IN['reason']);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function do_req_remove () {
		global $STD, $IN;
		
		require_once ROOT_PATH.'lib/resource.php';
		require_once ROOT_PATH.'lib/message.php';
		
		if (empty($IN['rid']))
			$STD->error("No resource specified.");
		
		$RES = new resource;
		
		if (!$RES->get($IN['rid']))
			$STD->error("Could not find resource.");
		
		if ($STD->user['uid'] != $RES->data['uid'])
			$STD->error("You cannot remove submissions that don't belong to you.");
		
		if (empty($IN['reason']))
			$STD->error("You must provide justification for your request.");
		
		$ACPM = new acp_message;
			
		$ACPM->data['sender'] = $STD->user['uid'];
		$ACPM->data['date'] = time();
		$ACPM->data['title'] = "Removal Request: {$RES->data['title']}";
		$ACPM->data['message'] = $STD->limit_string($IN['reason'], 10240);
		$ACPM->data['type'] = 5;
		$ACPM->data['aux'] = $IN['rid'];
			
		$ACPM->insert();
		
		//------------------------------------------------
		// Message
		//------------------------------------------------

		$url = $STD->encode_url($_SERVER['PHP_SELF']);
		
		$message = "Your request has been sent to the site staff for review.
			<p align='center'><a href='$url'>Return to the main page</a></p>";
		
		$this->output .= $STD->global_template->message($message);
	}
	
	//A Copy of show_manage_sub_list for public, take in a uid ***************
	function show_public_user () {
		global $STD, $CFG, $DB, $IN;
		
		require_once ROOT_PATH.'lib/resource.php';
		
		//Taken from show_user()**************
		$user = new user;
		$user->query_use('group');
		
		if (empty($IN['uid']) || !$user->get($IN['uid']))
			$STD->error("User does not exist.");		
		//End Taken from show_user()*************

		//Public page won't need this check*******************
		//if (!$STD->user['can_modify'])
		//	$STD->error("You do not have permission to modify your submissions.");
		
		if (empty($IN['st']))
			$IN['st'] = 0;
		
		if (empty($IN['o']))
			$IN['o'] = null;
		
		// Should we re-format the order?
		if (!empty($IN['o1'])) {
			$order = "{$IN['o1']},{$IN['o2']}";
			
			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=09&c={$IN['c']}&st={$IN['st']}&o=$order&uid={$user->data['uid']}");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}
		
		// Did we change the target?
		if (!empty($_POST['c'])) {
			$url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=09&c={$IN['c']}&st={$IN['st']}&o={$IN['o']}&uid={$user->data['uid']}");
			$url = str_replace("&amp;", "&", $url);
			header("Location: $url");
			exit;
		}

		//------------------------------------------------
		// Make sure we have a default module
		//------------------------------------------------
		
		$module_record = null;
		$STD->modules->load_module_list();
		
		$name_arr = array(); $val_arr = array();
		reset($STD->modules->module_set);
		while (list(,$row) = each ($STD->modules->module_set)) {
			$val_arr[] = $row['mid'];
			$name_arr[] = $row['full_name'];

			if ((empty($module_record) && empty($IN['c'])) || $row['mid'] == $IN['c']) {
				$module_record = $row;
				$module = $STD->modules->new_module($row['mid']);

				$this->mod_html = $STD->template->useTemplate( $module_record['template'] );
			}
		}
		
		if (empty($IN['c']))
			$IN['c'] = $val_arr[0];
		
		$js = "onchange=\"if(this.options[this.selectedIndex].value != -1){ document.changetype.submit() }\"";
		$type_list = $STD->make_select_box('c', $val_arr, $name_arr, $IN['c'], 'selectbox', $js);
		
		$module->init();
		
		//------------------------------------------------
		// Ordering and stuff
		//------------------------------------------------
		
		$order_names = array('t' => 'Title', 'a' => 'Author', 'd' => 'Date', 'u' => 'Updated');
		$order_list = array('t' => 'r.title', 'a' => "CONCAT(r.author_override,IFNULL(ru.username,''))",
						    'd' => 'r.rid', 'u' => 'IF(r.updated>0,r.updated,r.rid)');
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
		
		//------------------------------------------------
		// Start Page
		//------------------------------------------------
		
		//*********************User's Name Added******************************
		$this->output .= $STD->global_template->page_header($user->data['username'].'\'s Submissions');
		$this->output .= $this->html->public_type_row($type_list, $user->data['uid']);
		
		$this->output .= $this->html->manage_start_rows();
		
		//------------------------------------------------
		// Resource Rows
		//------------------------------------------------
		
		$RES = new resource;
		$RES->query_use('extention', $module_record['mid']);
		$RES->query_use('r_user');
		$RES->query_order($order[0], $order[1]);
		$RES->query_limit($IN['st'], $STD->get_page_prefs());
		$RES->query_condition("r.uid = '{$user->data['uid']}' AND r.queue_code IN (0,2)"); //UID from URL***********
		$RES->getByType($IN['c']);
		
		$rowlist = array();

		while ($RES->nextItem()) {
			$data = $module->resdb_prep_data($RES->data);

			$this->output .= $this->mod_html->public_row($data, $IN['c']); //****manage_row will change to public_row, an dI will make Templates for each type of submission.
		}

		$DB->free_result();
		
		//------------------------------------------------
		// Page numbering and ordering
		//------------------------------------------------
		
		$order_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=user&param=09&c={$IN['c']}&st={$IN['st']}&uid={$user->data['uid']}");
		$order_p = @join(',', $order_default);
		
		$rcnt = $RES->countByType($IN['c']);
		$pages = $STD->paginate($IN['st'], $rcnt['cnt'], $STD->get_page_prefs(), "act=user&param=09&c={$IN['c']}&o={$IN['o']}&uid={$user->data['uid']}");
		
		$selbox1 = $STD->make_select_box('o1', array_keys($order_names), array_values($order_names), $order_default[0], 'selectbox');
		$selbox2 = $STD->make_select_box('o2', array('a','d'), array('Ascending Order','Descending Order'), $order_default[1], 'selectbox');
		
		$this->output .= $this->html->manage_end_rows($pages, "$selbox1 $selbox2", $order_url);
		
		$this->output .= $STD->global_template->page_footer();
	}
}

?>
