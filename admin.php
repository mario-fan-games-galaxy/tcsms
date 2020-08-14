<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// Admin.php --
// Main Point of execution for Admin CP
//------------------------------------------------------------------

error_reporting(E_ALL);
//set_magic_quotes_runtime(0); // deprecated

define ('ROOT_PATH', './');

//------------------------------------------------

require ROOT_PATH.'settings.php';
require ROOT_PATH.'lib/db_drivers/'.$CFG['db_driver'].'.php';

$DB = new db_driver;
$DB->connect();

if (!empty($_GET['debug']))
	$DB->debug = 1;

//------------------------------------------------

require ROOT_PATH.'lib/std.php';
require ROOT_PATH.'lib/userlib.php';
require ROOT_PATH.'lib/resource.php';
require ROOT_PATH.'lib/parser.php';
require ROOT_PATH.'lib/Sajax.php';
require ROOT_PATH.'lib/module.php';
require ROOT_PATH.'component/admin/adm_template_ui.php';

$STD = new std;

$STD->sajax = new Sajax;
$STD->sajax->sajax_init();
$STD->sajax->sajax_set_request_type('GET');

$STD->template = new template;
$STD->template->init();
$STD->template->override['template'] = 'admin';

$STD->global_template = $STD->template->useTemplate('adm_global');
$STD->global_template_ui = new adm_template_ui;

$STD->modules = new module_record;
$STD->modules->load_module_list();

$IN	= $STD->parse_input();

$session = new session;
if (!$session->authorize()) {
	$IN['act'] = 'login';
	$IN['param'] = '01';
}

$STD->tags = $STD->template->global_tags();

//------------------------------------------------

/*if ($IN['c'] > 0) {
	//$mod_id = $TAG->nodedef[$IN['c']][1];
	empty($IN['c']) ? $mod_id = '0' : $mod_id = $IN['c'];

	$DB->query("SELECT * FROM {$CFG['db_pfx']}_modules WHERE mid = '{$mod_id}'");
	$MODULE = $DB->fetch_row();
	
	empty($MODULE) ? $TPL->preprocess_error("Invalud Module Specified") : false;
	require ROOT_PATH.'component/modules/'.$MODULE['module_file'];
} else 
	$MODULE = null;*/
//------------------------------------------------
// Unbreaking what I broke :(
/*$time = time() - 60*20;
$DB->query("SELECT username FROM {$CFG['db_pfx']}_users WHERE last_loc LIKE 'ACP,%' AND last_time > $time ORDER BY last_time DESC");
		
$names = '';
while ($name = $DB->fetch_row()) {
	$names .= "{$name['username']}, ";
}
$names = preg_replace('/,[ ]$/', '', $names);*/

//------------------------------------------------

if ((empty($STD->user) || $STD->user['uid'] == 0 || !$STD->user['acp_access'])
		&& $IN['act'] != 'login') {
	$IN['act'] = 'login';
	$IN['param'] = 1;
}

switch ($IN['act']) {
	case 'login'	:	require ROOT_PATH.'component/admin/adm_login.php'; break;
	case 'ucp'		:	require ROOT_PATH.'component/admin/adm_ucp.php'; break;
	case 'modq'		:	require ROOT_PATH.'component/admin/adm_modq.php'; break;
	case 'manage'	:	require ROOT_PATH.'component/admin/adm_manage.php'; break;
	case 'news'		:	require ROOT_PATH.'component/admin/adm_news.php'; break;
	case 'conf'		:	require ROOT_PATH.'component/admin/adm_conf.php'; break;
	case 'panel'	:	require ROOT_PATH.'component/admin/adm_panel.php'; break;
	default			:	require ROOT_PATH.'component/admin/adm_main.php'; break;
}

$component->init();

$session->save_data();

$DB->close_db();

?>
