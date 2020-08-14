<?php

class template_adm_global {

function message ($message) {
global $STD;
return <<<HTML
<br />
{$message}
HTML;
}

function error ($error) {
global $STD;
return <<<HTML
<tr>
  <td class="header">
  Error
  </td>
</tr>
<tr>
  <td class="body">
<br />
{$error}
</td>
</tr>
HTML;
}

function html_head () {
global $STD;
return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
  <head>
    <title>Admin Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /> 
    <style type="text/css">
        <!--
        body { font-family: Verdana, Arial, Helectiva, Sans-Serif; font-size:10pt }
        a:link, a:visited {text-decoration:none; color: #0000FF }
        .header { background: #5D669A; color: #FFFFFF; font-size:14pt; width:100% }
        .subheader { background: #7D86BA; color: #FFFFFF; font-size:13pt; width:100% }
        .body { background: #E1E4F9; color: #000000; font-size:11pt; width:100% }
        .textbox { background: #F6F7FF; color:#000000; font-size:10pt; border:1px solid #000000; padding:1px }
        .selectbox { background: #F6F7FF; color:#000000; font-size:10pt }
        .button { background: #B8BDDF; color:#000000; font-size:10pt; border:1px solid #000000 }
        .category { color: #CB3723; font-size:12pt; font-weight:bold }
        .title_fixed { font-weight: bold; font-size:11pt; color: #4B4D5F; width: 30% }
        .title { font-weight: bold; font-size:11pt; color: #4B4D5F }
        .highlight { color: #2749DC }
        .highlight2 { color: #CB3723 }
        .options { font-size:12pt; color: #4B4D5F }
        .options2 { font-size:12pt; color: #4B4D5F; background-color: #C8CDEF }
        .field_fixed { font-size:12pt; color: #4B4D5F; width: 70% }
        .field { font-size:12pt; color:#4B4D5F }
        .field2 { font-size:12pt; color: #4B4D5F; background-color: #C8CDEF }
        .options_small { font-size:10pt; color: #4B4D5F }
        .options_small2 { font-size:10pt; color: #4B4D5F; background-color: #C8CDEF }
        
        .errheader { background: #F56C65; color: #FFFFFF; font-size:14pt; width:100% }
        .errbody { background: #FFCDCB; color: #000000; font-size:11pt; width:100% }
        
        .dis_button { border: 1px solid #C8CDEF; }
        .click_button { border: 1px solid #C8CDEF; }
        .click_button:hover { border: 1px solid #7D86BA; background-color: #B8BDDF; }
        .rep_box { border: 1px dashed #000000; width:90%; margin-left: auto; margin-right: auto; }
        .rep_box .quotetitle { width: 90%; margin-left: auto; margin-right: auto; border-bottom: 1px solid black; }
        .rep_box .quote { width: 90%; margin-left: auto; margin-right: auto; }
        
        .rowfield { width: 90%; border: 1px solid #000000; text-align: left; }
        .rowtable { width: 100%; border: 0px; font-size: 10pt; text-align: left; }
        .rowstrip { background-color: #9CA4D4; padding: 3px; font-weight: bold; }
        .rowcell1 { background-color: #E1E4F9; padding: 3px; }
        .rowcell2 { background-color: #C8CDEF; padding: 3px; }
        .rowcell3 { background-color: #B8BDDF; padding: 3px; }
        .rowcell4 { background-color: #99A1C9; padding: 3px; }
        .rowtitle { background-color: #7D86BA; padding: 5px; color: #FFFFFF; font-weight: bold; font-size: 10pt; }
        .rowtitle a:link, .rowtitle a:visited { color: #FFFFFF; text-decoration: underline; }
        .tabactive { background-color: #7D86BA; padding: 5px; color: #FFFFFF; font-weight: bold; border:1px solid #000000; padding:5px; border-bottom:0px; }
        .tabactive a:link, .tabactive a:visited { color: #FFFFFF; }
        .tabinactive { background-color: #B8BDDF; padding: 3px; border:1px solid #000000; border-bottom:0px; padding:3px; }
        .tabinactive a:link, .tabinactive a:visited { color: #000000; }
        .quotetitle { width: 95%; padding: 4px; padding-left: 0px; margin-left: auto; margin-right: auto; margin-top: 2px; font-size: 8pt; font-weight: bold; }
		.quote { width: 95%; padding: 4px; margin-left: auto; margin-right: auto; margin-bottom: 2px; border: 1px solid #004F00; background-color: #E1E4F9; }
		.canquote { cursor: pointer; }
        -->
    </style>
	<script>
		function quote(name, id) {
			var elem = document.notepadform.notepad;
			elem.value += "[quote="+name+"]"+document.getElementById('msg'+id).innerHTML + "[/quote]\\n";
			elem.focus();
		}
	</script>
  </head>
HTML;
}

function site_content_header () {
global $STD;
return <<<HTML
<body>
HTML;
}

function site_content_footer () {
global $STD;
return <<<HTML
</body>
</html>
HTML;
}

function site_header ($site_url) {
global $STD;
return <<<HTML
<body>
<script type="text/javascript" src="{$STD->tags['template_path']}/global.js"></script>
<div align="center">
<table border="0" cellpadding="4" cellspacing="0" width="98%" style="border:2px solid #000000">
<tr>
  <td class="header">Admin Control Panel</td>
</tr>
<tr>
<td class="body">
<table border="0" cellpadding="1" cellspacing="0" width="100%">
<tr>
  <td width="50%" class="options">
    <a href="{$STD->tags['root_url']}act=main">ACP Home</a> | <a href="{$site_url}">Site Home</a>
  </td>
  <td width="50%" align="right" class="options">
    Logged in as: <b>{$STD->user['username']}</b> (<a href="{$STD->tags['root_url']}act=login&amp;param=03">Log out</a>)
  </td>
</tr>
<tr>
  <td width="100%" colspan="2" class="options">
    Current Active Users: {{active_users}}
  </td>
</tr>
</table>
</td>
</tr>
</table>
</div>
<br />
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
HTML;
}

function site_menu ($modq_menu) {
global $STD;
return <<<HTML
<td width="15%" valign="top">
<table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:2px solid #000000">
<tr>
  <td class="header">
  Submissions
  </td>
</tr>
<tr>
<td class="body" style="font-size:10pt">
    {$modq_menu}
	</td>
</tr>
</table>
<br />
<div {{ucp_style}}>
<table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:2px solid #000000">
<tr>
  <td class="header">
  Users
  </td>
</tr>
<tr>
	  <td class="body" style="font-size:10pt">
  :: <a href="{$STD->tags['root_url']}act=ucp&amp;param=01">Manage Users</a><br />
  :: <a href="{$STD->tags['root_url']}act=ucp&amp;param=14">Find Users</a><br />
  :: <a href="{$STD->tags['root_url']}act=ucp&amp;param=07">Manage Groups</a><br />
  :: <a href="{$STD->tags['root_url']}act=ucp&amp;param=06">Ban Settings</a>
  </td>
</tr>
</table>
</div>
<br />
<div>
<table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:2px solid #000000">
<tr>
  <td class="header">
  News
  </td>
</tr>
<tr>
  <td class="body" style="font-size:10pt">
  :: <a href="{$STD->tags['root_url']}act=news&amp;param=01">New Entry</a><br />
  :: <a href="{$STD->tags['root_url']}act=news&amp;param=03">Modify Entry</a>
  </td>
</tr>
</table>
</div>
<br />
<div>
<table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:2px solid #000000">
<tr>
  <td class="header">
  Manage
  </td>
</tr>
<tr>
  <td class="body" style="font-size:10pt">
  :: <a href="{$STD->tags['root_url']}act=manage&amp;param=01">Message Ctr</a><br />
  :: <a href="{$STD->tags['root_url']}act=manage&amp;param=05">Site On/Off</a><br />
  :: <a href="{$STD->tags['root_url']}act=conf&amp;param=01">Filter Groups</a><br />
  :: <a href="{$STD->tags['root_url']}act=panel&amp;param=01">Panels</a>
  </td>
</tr>
</table>
</div>
</td>
<td width="2%">
&nbsp;
</td>
HTML;
}

function content_header () {
global $STD;
return <<<HTML
<td width="83%" valign="top">
<table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:2px solid #000000">
HTML;
}

function page_header ($title) {
global $STD;
return <<<HTML
<tr>
  <td class="header">
  {$title}
  </td>
</tr>
<tr>
  <td class="body">
HTML;
}

function page_footer () {
global $STD;
return <<<HTML
  </td>
</tr>
HTML;
}

function content_footer () {
global $STD;
return <<<HTML
</table>
</td>
HTML;
}

function site_footer () {
global $STD;
return <<<HTML
</tr>
</table>
</div>
</body>
</html>
HTML;
}

// Not a true skin component
function wrapper ($template, $out) {
	global $CFG,$STD;
	
	$output  = $template->html_head();
	$output .= $template->site_header( $CFG['root_url'].'/index.php' );
	
	$output .= $template->site_menu( $STD->global_template_ui->modq_menu() );
	$output .= $template->content_header();
	
	$output .= $out;
	
	$output .= $template->content_footer();
	$output .= $template->site_footer();
	
	return $output;
}

}

?>