<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// Index.php --
// Main Point of execution for entire CMS
//------------------------------------------------------------------

error_reporting(E_ALL);
set_time_limit(0);
//set_magic_quotes_runtime(0); // deprecated

define ('ROOT_PATH', './');

//------------------------------------------------

require ROOT_PATH.'settings.php';

require ROOT_PATH.'lib/db_drivers/'.$CFG['db_driver'].'.php';

$DB = new db_driver;
$DB->connect();

if (!empty($_GET['debug']))
	$DB->debug = 1;
//$DB->debug = 1;

//------------------------------------------------

require ROOT_PATH.'lib/std.php';
require ROOT_PATH.'lib/userlib.php';
//require ROOT_PATH.'lib/Sajax.php';
require ROOT_PATH.'lib/module.php';
require ROOT_PATH.'component/template_ui.php';

$STD = new std;

//$STD->sajax = new Sajax;
//$STD->sajax->sajax_init();
//$STD->sajax->sajax_set_request_type('GET');

$STD->template = new template;
$STD->template->init();

$STD->modules = new module_record;
//$STD->modules->load_module_list();

$IN	= $STD->parse_input();

$session = new session;
$session->authorize();
/*if (!$session->authorize()) {
	$STD->error("Your access to {$CFG['site_name']} has been revoked.  If you believe this is an error, contact 
		the <a href='mailto:{$CFG['admin_email']}'>site staff</a>.  If you have been granted an exception, login 
		to the site with your username and password.");
	$session->clear_session();
}*/

//trigger_error("<pre>{$_SERVER['REMOTE_ADDR']} (This is not an error - this is part of a temporary debugging session.  Please ignore it.)</pre>", E_USER_WARNING);

$STD->global_template = $STD->template->useTemplate('global');
$STD->global_template_ui = new template_ui;

$STD->tags = $STD->template->global_tags();

//------------------------------------------------

/*$STD->modules = array();
$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules");
while ($mod = $DB->fetch_row()) {
	$STD->modules[$mod['mid']] = $mod;
}

if ($IN['c'] > 0) {
	!isset($STD->modules[$IN['c']]) ? $STD->template->preprocess_error("Invalud Module Specified") : false;
	
	$MODULE = $STD->modules[$IN['c']];
	require_once ROOT_PATH.'component/modules/'.$MODULE['module_file'];
} else
	$MODULE = null;*/
	
/*if ($IN['c'] > 0) {
	//$mod_id = $TAG->nodedef[$IN['c']][1];
	empty($IN['c']) ? $mod_id = '0' : $mod_id = $IN['c'];

	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE mid = '{$mod_id}'");
	$MODULE = $DB->fetch_row();
	
	empty($MODULE) ? $STD->template->preprocess_error("Invalud Module Specified") : false;
	require_once ROOT_PATH.'component/modules/'.$MODULE['module_file'];
} else 
	$MODULE = null;*/

//------------------------------------------------

// Are we offline?
if ($CFG['site_offline'] && !$STD->user['acp_access']) {
	if ($IN['act'] != 'login' || $IN['param'] != 2) {
		$STD->offline = 1;
		$STD->template->display($CFG['offline_msg']);
		exit;
	}
}
//$IN['act'] = 'msg';
switch ($IN['act']) {
	case 'login'	:	require ROOT_PATH.'component/login.php'; break;
	case 'submit'	:	require ROOT_PATH.'component/submit.php'; break;
	case 'user'		:	require ROOT_PATH.'component/user.php'; break;
	case 'resdb'	:	require ROOT_PATH.'component/resourcedb.php'; break;
	case 'msg'		:	require ROOT_PATH.'component/messenger.php'; break;
	case 'comment'	:	require ROOT_PATH.'component/comment.php'; break;
	case 'search'	:	require ROOT_PATH.'component/search.php'; break;
	case 'staff'	:	$IN['param'] = 7;
						require ROOT_PATH.'component/main.php'; break;
	default			:	require ROOT_PATH.'component/main.php'; break;
}

$component->init();

$session->save_data(); //('err_save', false);

$DB->close_db();

?>
