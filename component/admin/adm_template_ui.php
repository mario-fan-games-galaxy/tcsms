<?php

class adm_template_ui {

	function modq_menu () {
		global $STD, $DB, $CFG;
		
		$menus = '';
		
	//	$DB->query("SELECT mid,module_name FROM {$CFG['db_pfx']}_modules");
	//	while ($row = $DB->fetch_row()) {
		reset($STD->modules->module_set);
		while (list(,$row) = each ($STD->modules->module_set)) {
			$url = $STD->encode_url( $_SERVER['PHP_SELF'], "act=modq&param=01&c={$row['mid']}" );
			$menus .= ":: <a href='$url'>{$row['module_name']}</a><br />\n";
		}
		
		$url = $STD->encode_url( $_SERVER['PHP_SELF'], 'act=modq&param=08' );
		$menus .= "<hr>\n";
		$menus .= ":: <a href='$url'>Create New</a><br />\n";
		
		return $menus;
	}

}

?>