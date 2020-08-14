<?php

class template_panel {

var $title = '';
var $content = '';
var $html = '';

function render_panel_area () {
global $STD;

$pt = $STD->template->useTemplate('panels');

$this->html = '';
$this->html .= $STD->global_template->panel_area_start();
$this->html .= $STD->global_template->panel_column_left_start();

// MAIN PANEL
	$this->html .= $STD->global_template->panel_start('Main');
	$this->html .= $pt->link_region_start();
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=main\">Updates</a>");
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=staff\">Staff</a>");
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=submit&amp;param=01\">Submission Rules</a>");
	$this->html .= $pt->link_entry("<a href=\"http://mfgg.taloncrossing.com\">Message Board</a>");
	$this->html .= $pt->link_region_end();
	$this->html .= $STD->global_template->panel_end();

// LOGIN PANEL
if (in_array($STD->user['gid'], array(4,10))) {
	$this->html .= $STD->global_template->panel_start('Login');
	$this->html .= $pt->login_region_start();
	$this->html .= $pt->login_form();
	$this->html .= $pt->login_region_end();
	$this->html .= $STD->global_template->panel_end();
}

// USER PANEL
if (!in_array($STD->user['gid'], array(4, 10))) {
	$this->html .= $STD->global_template->panel_start($STD->user['username']);
	$this->html .= $pt->link_region_start();
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=user&amp;param=02\">Preferences</a>");
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=msg&amp;param=01\">Messages ({$STD->user['new_msgs']})</a>");
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=user&amp;param=03\">My Submissions</a>");
	if ($STD->user['can_submit'] == 1) {
		$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=submit&amp;param=02\">Submit File</a>");
	}
	$this->html .= $pt->link_entry("<a href=\"{$STD->tags['root_url']}act=login&amp;param=03\">Log Out</a>");
	if ($STD->user['acp_access'] == 1) {
		$this->html .= $pt->link_separator();
		$this->html .= $pt->link_entry("<a href=\"admin.php\">Admin CP</a>");
	}
	$this->html .= $pt->link_region_end();
	$this->html .= $STD->global_template->panel_end();
}

// QUICK SEARCH PANEL
	$this->html .= $STD->global_template->panel_start('Quick Search');
	$this->html .= $pt->qsearch_region_start();
	$this->html .= $pt->qsearch_form();
	$this->html .= $pt->qsearch_region_end();
	$this->html .= $STD->global_template->panel_end();

// AFFILIATES PANEL
	$this->html .= $STD->global_template->panel_start('Affiliates');
	$this->html .= $pt->link_region_start();
	$this->html .= $pt->link_region_end();
	$this->html .= $STD->global_template->panel_end();

$this->html .= $STD->global_template->panel_column_left_end();
$this->html .= $STD->global_template->panel_column_center_start();

// MAIN PANEL
	$this->html .= $STD->global_template->panel_start($this->title);
	$this->html .= $this->content;
	$this->html .= $STD->global_template->panel_end();

$this->html .= $STD->global_template->panel_column_center_end();
$this->html .= $STD->global_template->panel_area_end();

return $this->html;
}

}

?>