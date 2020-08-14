<?php

class template_submit {

function submit_page ($urlparts, $token, $type_list, $def_disp) {
global $STD;
return <<<HTML
<script type="text/javascript">
<!--
function do_form() {
	disable_element('exform_submit');
	show('exform_submsg');
	scroll_page();
	return true;
}

function scroll_page() {
	window.scrollBy(0,100);
}
-->
</script>
<div class="sform">
<form method="get" name="subselect" action="{$urlparts['form1']}" enctype="multipart/form-data">
{$urlparts['sess']}
<input type="hidden" name="act" value="submit" />
<input type="hidden" name="param" value="{$urlparts['param1']}" />
<div class="sformstrip">Choose a submission type.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Submission Type</td>
  <td class="sformright">{$type_list}</td>
</tr>
</table>
</form>
<form method="post" name="subform" action="{$urlparts['form2']}" enctype="multipart/form-data" onsubmit="return do_form()">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="c" value="{$STD->tags['c']}" />
<div id="page" style="{$def_disp['style']}">
{$def_disp['module']}
</div>
<div id="select_page" style="{$def_disp['astyle']}">
<div class="sformblock" style="text-align:center"><br />Select a submission type to expand the form<br />&nbsp;</div>
</div>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Submit!" id="exform_submit" class="button" /></div>
<div id="exform_submsg" class="sformblock" style="display:none; text-align:center"><br />
Your submission is being transfered.  Please do not leave this page until the transfer is completed.<br />&nbsp;</div>
</form>
</div>
HTML;
}

function type_select ($options) {
global $STD;
return <<<HTML
<select name="c" size="1" class="selectbox" onchange="if(this.options[this.selectedIndex].value != -1){ document.subselect.submit() }">
{$options}
</select> <input type="submit" value="Change" class="button" />
HTML;
}

function invalid_module () {
global $STD;
return <<<HTML
<div class="sformblock" style="text-align:center"><br />Invalid Module Requested<br />&nbsp;</div>
HTML;
}

function rules ($url, $rules, $show_extra) {
global $STD;
return <<<HTML
<div class="sform">
<div class="sformblock" style="{$show_extra}">
  <span class='highlight' style="font-weight:bold">Welcome to the MFGG submission page.  Because this is your first time making a submission, 
  please review the rules below to ensure your submission gets posted on the site.  After you complete 
  your first submission, you will not be shown this page again.</span><br /><br /></div>
<div class="sformblock">{$rules}</div>
<div class="sformblock" style="text-align:center;{$show_extra}">
  <form method='post' action='$url'>
  <input type='checkbox' name='rules_agree' class="checkbox" /> I have read the rules and agree to follow them.<br />
  <input type='submit' name='rules_continue' value='Continue' class="button" /></form></div>
</div>
HTML;
}

}