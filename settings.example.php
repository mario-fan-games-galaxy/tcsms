<?php

$CFG = array(
	'site_name'        => "Mario Fan Games Galaxy",
	'root_url'         => "", // TODO: put your TCSMS install URL here
	'link_domains'     => "", // TODO: put your TCSMS install URL here? not sure what other domains do but this is a CSV string
	'db_driver'        => "mysql",
	'db_host'          => "localhost",
	'db_user'          => "root",
	'db_pass'          => "",
	'db_db'            => "mfgg",
	'cookie_prefix'    => "mfgg_",
	'cookie_path'      => "/",
	'cookie_domain'    => "localhost",
	'db_pfx'           => "tsms",
	'admin_email'      => "", // TODO: put admin's email here
	'template_path'    => "template",
	'template_default' => 2,
	'blacklist'        => "",
	'whitelist'        => "",
	'emaillist'        => "",
	'default_order_by' => "d",
	'default_order'    => "d",
	'default_pagesize' => 20,
	'acp_access'       => 1,
	'banned_access'    => 10,
	'guest_access'     => 4,
	'date_time_format' => "M j Y, g:i A",
	'date_format'      => "M j Y",
	'date_short'       => "m/d/y",
	'max_icon_dims'    => "80x80",
	'def_icon_dims'    => "80x80",
	'default_icon'     => "", // TODO: put your default icon URL here
	'staff_name'       => "<i>Site Staff</i>",
	'site_offline'     => "0",
	'offline_msg'      => "The site is offline due to an emergency caused by the recent software update.  Would the user that replied to my update please visit the MFGG forums and send me a PM with all of their user information.<br /><br />Thanks.  The site will be back up when the source of user account corruption is found.<br /><br />-- Retriever II",
	'news_update_span' => 14,
	'adm_virus_check'  => 1,
	'panel_expand'     => 1,
	'panel_maximize'   => 1,
	'quote_nesting'    => 0,
	'mail_out'         => "", // TODO: put your SMTP email here
	'mail_interface'   => "sendmail",
	'smtp_host'        => "",
	'smtp_port'        => "25",
	'smtp_user'        => "",
	'smtp_pass'        => "",
	'max_consec_dl'    => 3,
	'cat_order'        => array(
		'0' => "1",
		'4' => "9,10",
	),
);

?>