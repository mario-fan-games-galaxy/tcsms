<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// component/messenger.php --
// Handles sending and receiving of messages
//------------------------------------------------------------------

$component = new component_messenger;

class component_messenger {

	var $html		= "";
	var $mod_html	= "";
	var $output		= "";
	var $dir		= 0;
	
	function init () {
		global $IN, $STD;
		
		require_once ROOT_PATH.'lib/message.php';
		
		$this->html = $STD->template->useTemplate('msg');
		
		if (!$STD->user['uid'])
			$STD->error("You must be logged in to use the Message Center");
		//$IN['param'] = 6;
		
		switch ($IN['param']) {
			case 1: $this->show_inbox(); break;
			case 2: $this->read_msg(); break;
			case 3: $this->show_report(); break;
			case 4: $this->do_delete_msg(); break;
			case 5: $this->show_compose(); break;
			case 6: $this->send_msg(); break;
			case 7: $this->show_reply(); break;
			case 8: $this->do_report(); break;
			case 9: $this->change_folder(); break;
		}
		
		//$TPL->template = $this->output;
		$STD->template->display( $this->output );
	}
	
	function show_inbox () {
		global $IN, $CFG, $STD;
		
		if (empty($IN['dir']))
			$IN['dir'] = 0;
		
		$this->dir = $IN['dir'];
		
		if (empty($IN['st']))
			$IN['st'] = 0;
		
		if (empty($IN['o']))
			$IN['o'] = null;
		
		if ($STD->user['new_msgs'] > 0 && $IN['dir'] == 0) {
			$STD->userobj->data['new_msgs'] = 0;
			$data = $STD->userobj->data;
			$STD->userobj->update();
			$STD->userobj->data = $data;
		}
		
		// Order shenanigans
		$order_url = $STD->encode_url( $_SERVER['PHP_SELF'], "act=msg&param=01&dir={$IN['dir']}&st={$IN['st']}" );
		
		$order_list = array('t' => 'm.title', 'u' => 'su.username', 'd' => 'm.date');
		$order_default = array('d', 'd');
		
		$order = $STD->order_translate( $order_list, $order_default );
		$order_links = $STD->order_links( $order_list, $order_url, $order_default );
		
		//------------------------------------------------
		// Start Page
		//------------------------------------------------
		
		$this->output .= $STD->global_template->page_header('Message Center');
		
		$percent = round($STD->user['cur_msgs'] * 100 / $STD->user['msg_capacity']);
		$box = $this->html->storage_box($STD->user['cur_msgs'], $STD->user['msg_capacity'], $percent);
		
		$other = "onchange=\"if(this.options[this.selectedIndex].value != -1){ document.msgform.submit() }\"";
		$dirsel = $STD->make_select_box('dir', array('0','1'), array('Inbox','Sent Items'), $IN['dir'], 'selectbox', $other);
		
		($this->dir == 0)
			? $dir_name = 'Sender'
			: $dir_name = 'Receiver';
		
		$this->output .= $this->html->start_rows( $order_links, $box, $dirsel, $dir_name );
		
		$MSG = new message;
		$MSG->query_use('s_user');
		$MSG->query_use('r_user');
		$MSG->query_order($order[0], $order[1]);
		$MSG->query_limit($IN['st'], $this->get_page_prefs());
		$MSG->getByReceiver($STD->user['uid'], $IN['dir']);

		while ($MSG->nextItem()) {
			$this->output .= $this->html->msg_row( $this->format_msg_row($MSG->data) );
		}
		
		$MSG->query_condition("m.owner = '{$STD->user['uid']}'");
		$rcnt = $MSG->countAll();
		
		$pages = $STD->paginate($IN['st'], $rcnt['cnt'], $this->get_page_prefs(), "act=msg&param=01&o={$IN['o']}&dir={$IN['dir']}");
		
		$this->output .= $this->html->end_rows($pages);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function read_msg () {
		global $IN, $CFG, $STD;
		
		$MSG = new message;
		$MSG->query_use('s_user');
		
		if (!$MSG->get($IN['mid']))
			$STD->error("The message you're trying to read does not exist.");
		
		if ($MSG->data['owner'] != $STD->user['uid'])
			$STD->error("You do not have permission to view this message.");
		
		if ($MSG->data['msg_read'] == 0 && $STD->user['new_msgs'] > 0) {
			$STD->userobj->data['new_msgs']--;
			$STD->userobj->update();
		}
		
		// Get conversation history
		$CMSG = new message;
		$CMSG->query_use('s_user');
		$CMSG->query_condition("m.owner = '{$STD->user['uid']}'");
		$CMSG->query_condition("m.conversation = '{$MSG->data['conversation']}'");
		$CMSG->query_condition("m.date < '{$MSG->data['date']}'");
		$CMSG->query_order("m.date", "ASC");
		$CMSG->getAll();
		
		$msg_history = '';
		while ($CMSG->nextItem()) {
			$CMSG->data['message'] = $STD->untag_urls($CMSG->data['message']);
			$msg_history .= $this->html->msg_history_row( $this->format_msg_view($CMSG->data) );
		}
		
		//------------------------------------------------
		// Print Page
		//------------------------------------------------
		
		$this->output .= $STD->global_template->page_header('Message Center');
		
		if (!empty($msg_history))
			$this->output .= $this->html->msg_history($msg_history);
		
		$mdata = $MSG->data;
		$mdata['message'] = $STD->untag_urls($mdata['message']);
		$this->output .= $this->html->msg_view( $this->format_msg_view($mdata) );
		
		$this->output .= $STD->global_template->page_footer();
		
		// Mark as read?
		
		if ($MSG->data['msg_read'] == 0) {
			$MSG->data['msg_read'] = 1;
			$MSG->data['read_date'] = time();
			$MSG->update();
		}
	}
	
	function do_delete_msg () {
		global $STD, $IN;

		if (empty($IN['mid']))
			$STD->error("No messages selected for deletion.");
		
		$MSG = new message;
		
		if (!is_array($IN['mid']))
			$IN['mid'] = array($IN['mid']);
		
		$msg_cnt = 0;
		
		reset ($IN['mid']);
		while (list(,$v) = each($IN['mid'])) {
			if (!$MSG->get($v))
				$STD->error("Invalid message selected for deletion.");
			if ($MSG->data['owner'] != $STD->user['uid'])
				$STD->error("You do not have permission to delete this message.");
				
			$MSG->remove();
			$msg_cnt++;
		}
		
		$STD->userobj->data['cur_msgs'] -= $msg_cnt;
		
		$data = $STD->userobj->data;
		$STD->userobj->update();
		$STD->userobj->data = $data;
		
		$redir = $STD->encode_url($_SERVER['PHP_SELF'], "act=msg&param=01");
		$redir = str_replace("&amp;", "&", $redir);
		
		header("Location: $redir");
		exit;
	}
	
	function show_compose () {
		global $STD, $IN;
		
		if (!$STD->user['can_msg'])
			$STD->error("You do nothave permission to send messages.");
		
		$data['recipient'] = '';
		$data['subject'] = '';
		$data['body'] = '';
		
		if (!empty($IN['uid'])) {
			$USER = new user;
			if ($USER->get($IN['uid']))
				$data['recipient'] = $USER->data['username'];
		}
		
		$this->output .= $STD->global_template->page_header('Compose Message');
		
		$this->output .= $this->html->msg_compose($data, $STD->make_form_token(), 0, 0);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function show_reply () {
		global $IN, $STD;
		
		if (!$STD->user['can_msg'])
			$STD->error("You do not have permission to send messages.");
			
		$MSG = new message;
		$MSG->query_use('s_user');
		if (!$MSG->get($IN['mid']))
			$STD->error("The message you are replying to does not exist.");
		
		if ($MSG->data['owner'] != $STD->user['uid'])
			$STD->error("You do not have permission to reply to this message.");
		
		$MSG->data['title'] = preg_replace("/^Re:\s/", "", $MSG->data['title']);
		
		$data['recipient'] = $MSG->data['su_username'];
		$data['subject'] = $STD->nat_substr("Re: {$MSG->data['title']}", 128);
		$data['body'] = '';
		
		// Get conversation history
		$CMSG = new message;
		$CMSG->query_use('s_user');
		$CMSG->query_condition("m.owner = '{$STD->user['uid']}'");
		$CMSG->query_condition("m.conversation = '{$MSG->data['conversation']}'");
		$CMSG->query_order("m.date", "ASC");
		$CMSG->getAll();
		
		$msg_history = '';
		while ($CMSG->nextItem()) {
			$CMSG->data['message'] = $STD->untag_urls($CMSG->data['message']);
			$msg_history .= $this->html->msg_history_row( $this->format_msg_view($CMSG->data) );
		}
		
		// Output
		
		$this->output .= $STD->global_template->page_header('Message Center');
		
		if (!empty($msg_history))
			$this->output .= $this->html->msg_history($msg_history);
		
		if ($MSG->data['sender'] == 0)
			$staff = 1;
		else
			$staff = 0;
		
		$this->output .= $this->html->msg_compose($data, $STD->make_form_token(), $IN['mid'], $staff);
		
		$this->output .= $STD->global_template->page_footer();
	}
	
	function send_msg () {
		global $STD, $IN;
		
		if (!$STD->user['can_msg'])
			$STD->error("You do not have permission to send messages.");
		
		if ($IN['to'] == "other" && !$STD->user['can_msg_users'])
			$STD->error("You do not have permission to send messages to other registered users.");
		
		if (!$STD->validate_form($IN['security_token']))
			$STD->error("Your request did not originate from a proper location, or you are attempting to re-process your request.");
		
		if ($IN['to'] == "other" && empty($IN['recipient']))
			$STD->error("Your must enter the name of registered member in the recipient field.");
		
		if (empty($IN['subject']))
			$STD->error("You must include a subject with your message.");
			
		if (empty($IN['body']))
			$STD->error("You must write a message.");
			
		require_once ROOT_PATH.'lib/parser.php';
		$PARSER = new parser();
		
		$MSG = null;
		
		if ($IN['to'] == "other") {
			$recip_user = new user;
			if (!$recip_user->getByName($IN['recipient']))
				$STD->error("No user by the name \"{$IN['recipient']}\" exists.");
			
			$MSG = new message;
			
			// reply?
			if ($IN['reply'] > 0) {
				$RMSG = new message;
				if (!$RMSG->get($IN['reply']))
					$STD->error("You attempted to reply to an invalid message");
				
				if ($RMSG->data['owner'] != $STD->user['uid'])
					$STD->error("You cannot reply to a message that you do not own");
				
				$MSG->data['conversation'] = $RMSG->data['conversation'];
			}
			
			$MSG->data['sender'] = $STD->user['uid'];
			$MSG->data['receiver'] = $recip_user->data['uid'];
			$MSG->data['owner'] = $recip_user->data['uid'];
			$MSG->data['date'] = time();
			$MSG->data['folder'] = 0;
			$MSG->data['title'] = $STD->nat_substr($IN['subject'], 128);
			$MSG->data['message'] = $STD->limit_string($IN['body'], 10240);
			
			$MSG->data['message'] = $PARSER->convert($MSG->data['message']);
			
			$MSG->dispatch(1);
			
			if ($IN['reply'] == 0) {
				$MSG->data['conversation'] = $MSG->data['mid'];
				$MSG->update();
			}
			
			// Are we keeping a copy?
			if (!empty($IN['copy'])) {
				$MSG->data['owner'] = $STD->user['uid'];
				$MSG->data['title'] = "Sent: " . $MSG->data['title'];
				$MSG->data['folder'] = 1;
				$MSG->data['msg_read'] = 1;
				
				if ($STD->user['cur_msgs'] < $STD->user['msg_capacity']) {
					$MSG->insert();
					
					$STD->user['cur_msgs']++;
					$STD->userobj->update();
				}
			}
		} else {
			$ACPM = new acp_message;
			$ACPM->data['conversation'] = 0;
			
			// reply?
			if ($IN['reply'] > 0) {
				$RMSG = new message;
				if (!$RMSG->get($IN['reply']))
					$STD->error("You attempted to reply to an invalid message");
				
				if ($RMSG->data['owner'] != $STD->user['uid'])
					$STD->error("You cannot reply to a message that you do not own");
				
				$ACPM->data['conversation'] = $RMSG->data['conversation'];
			}
			
			$time = time();
			
			// Format message for local save
			if (!empty($IN['copy'])) {
				$MSG = new message;
				
				$MSG->data['sender'] = $STD->user['uid'];
				$MSG->data['receiver'] = 0;
				$MSG->data['owner'] = $STD->user['uid'];
				$MSG->data['date'] = $time;
				$MSG->data['folder'] = 1;
				$MSG->data['msg_read'] = 1;
				$MSG->data['title'] = "Sent: " . $STD->nat_substr($IN['subject'], 128);
				$MSG->data['message'] = $STD->limit_string($IN['body'], 10240);
				$MSG->data['message'] = $PARSER->convert($MSG->data['message']);
				$MSG->data['conversation'] = $ACPM->data['conversation'];
				
				if ($STD->user['cur_msgs'] < $STD->user['msg_capacity']) {
					$MSG->insert();
				
					$STD->user['cur_msgs']++;
					$STD->userobj->update();
					
					if (empty($MSG->data['conversation'])) {
						$MSG->data['conversation'] = $MSG->data['mid'];
						$MSG->update();
						
						$ACPM->data['conversation'] = $MSG->data['conversation'];
					}
				}
			}
			
			// Finish creating admin message
			$ACPM->data['sender'] = $STD->user['uid'];
			$ACPM->data['date'] = $time;
			$ACPM->data['title'] = $STD->nat_substr($IN['subject'], 128);
			$ACPM->data['message'] = $STD->limit_string($IN['body'], 10240);
			$ACPM->data['type'] = 4;
			
			$ACPM->data['message'] = $PARSER->convert($ACPM->data['message']);
			
			$ACPM->insert();
		}
		
		//------------------------------------------------
		// Message
		//------------------------------------------------
		
	//	$this->output .= $this->html->page_header();
		
		$url = $STD->encode_url($_SERVER['PHP_SELF']);
		$url2 = $STD->encode_url($_SERVER['PHP_SELF'], "act=msg&param=01");
		
		$message = "Your message has been successfully dispatched.
			<p align='center'><a href='$url2'>Return to Message Center</a><br />
			<a href='$url'>Return to the main page</a></p>";
		
		$this->output .= $STD->global_template->message($message);
		
		$STD->clear_form_token();
		
	//	$this->output .= $this->html->page_footer();
	}
	
	function change_folder () {
		global $STD, $IN;
	
		$redir = $STD->encode_url($_SERVER['PHP_SELF'], "act=msg&param=01&dir={$IN['dir']}");
		$redir = str_replace("&amp;", "&", $redir);
		
		header("Location: $redir");
		exit;
	}
	
	function format_msg_base ($MSG) {
		global $STD, $CFG;
		
		require_once ROOT_PATH.'component/modules/module_base.php';
		
		$data = array();
		
		$mod_base = new module;
		
		// Determine Message Type
		if ($MSG['sender'] == 0 && $MSG['msg_read'] == 0)
			$data['icon'] = "<img src='{$STD->tags['image_path']}/msg_sys_new.gif' alt='[NEW]' title='Unread System Message' />";
		elseif ($MSG['sender'] == 0 && $MSG['msg_read'] == 1)
			$data['icon'] = "<img src='{$STD->tags['image_path']}/msg_sys_read.gif' alt='[READ]' title='System Message' />";
		elseif ($MSG['sender'] > 0 && $MSG['msg_read'] == 0)
			$data['icon'] = "<img src='{$STD->tags['image_path']}/msg_new.gif' alt='[NEW]' title='Unread Personal Message' />";
		elseif ($MSG['sender'] > 0 && $MSG['msg_read'] == 1)
			$data['icon'] = "<img src='{$STD->tags['image_path']}/msg_read.gif' alt='[READ]' title='Personal Message' />";
		
		// Create Username Link
		($MSG['sender'] == 0)
			? $data['sender'] = $CFG['staff_name']
			: $data['sender'] = $mod_base->format_username($MSG, 'su_');
		if ($this->dir == 1)
			($MSG['receiver'] == 0)
				? $data['sender'] = $CFG['staff_name']
				: $data['sender'] = $mod_base->format_username($MSG, 'ru_');
		
		$data['title'] = $MSG['title'];
		$data['date'] = $STD->make_date_time($MSG['date']);
		$data['mid'] = $MSG['mid'];
		
		return $data;
	}
	
	function format_msg_row ($MSG) {
		global $STD;
		
		$data = $this->format_msg_base($MSG);

		$msg_url = $STD->encode_url($_SERVER['PHP_SELF'], "act=msg&param=02&mid={$MSG['mid']}");
		$MSG['title'] = "<a href='$msg_url'>{$MSG['title']}</a>";
		
		($MSG['msg_read'] == 0)
			? $data['title'] = "<b>{$MSG['title']}</b>"
			: $data['title'] = $MSG['title'];

		return $data;
	}
	
	function format_msg_view ($MSG) {
		global $STD;
		
		$data = $this->format_msg_base($MSG);
		
		$data['body'] = $MSG['message'];
		$data['report_url'] = $STD->encode_url($_SERVER['PHP_SELF'], "act=main&param=05&type=3&id={$MSG['mid']}");
		
		return $data;
	}
	
	function get_page_prefs () {
		global $CFG, $STD, $session;
		
		$session->touch_data ('pagesize');
		if (!empty ($session->data['pagesize']) )
			return $session->data['pagesize'];
		if (!empty($STD->userobj->data['items_per_page']))
			return $STD->userobj->data['items_per_page'];
		return $CFG['default_pagesize'];
	}
}

?>