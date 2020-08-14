<?php

class template_msg {

function start_rows ($order_list) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$STD->tags['root_url']}act=msg&param=04">
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformtitle" style="width: 5%">&nbsp;</td>
  <td class="sformtitle" style="width: 40%"><a href="{$order_list['t']['url']}"><u>Subejct</u></a> {$order_list['t']['img']}</td>
  <td class="sformtitle" style="width: 25%"><a href="{$order_list['u']['url']}"><u>Sender</u></a> {$order_list['u']['img']}</td>
  <td class="sformtitle" style="width: 25%"><a href="{$order_list['d']['url']}"><u>Date</u></a> {$order_list['d']['img']}</td>
  <td class="sformtitle" style="width: 5%">&nbsp;</td>
</tr>
HTML;
}

function end_rows ($pages) {
global $STD;
return <<<HTML
<tr>
  <td class="sformtitle" colspan="5" style="padding:2px">
  <table border="0" cellspacing="0" cellpadding="2" width="100%"><tr>
    <td width="5%" align="center"><img src="{$STD->tags['image_path']}/msg_compose.gif" /></td>
    <td style="font-weight:bold"><a href="{$STD->tags['root_url']}act=msg&param=05">[Compose Message]</a></td>
    <td align="right"><input type="submit" value="Delete" /> Selected Messages</td>
  </tr></table></td>
</tr>
</table>
</form>
<div class="sformstrip">Pages: {$pages}</div>
</div>
HTML;
}

function msg_row ($msg) {
global $STD;
return <<<HTML
<tr>
  <td class="sformfree" align="center">{$msg['icon']}</td>
  <td class="sformfree">{$msg['title']}</td>
  <td class="sformfree">{$msg['sender']}</td>
  <td class="sformfree">{$msg['date']}</td>
  <td class="sformfree" align="center"><input type='checkbox' name='mid[]' value='{$msg['mid']}' /></td>
</tr>
HTML;
}

function msg_view ($msg) {
global $STD;
return <<<HTML
<script type='text/javascript'>
<!--
  function check_delete () {
  	form_check = confirm('Are you sure you want to delete this message?');
  	
  	if (form_check == true) {
  		return true;
  	} else {
  		return false;
  	}
  }
-->
</script>
<div class="sform">
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformtitle" style="width: 20%">{$msg['sender']}</td>
  <td class="sformtitle" style="width: 80%"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr>
    <td>{$msg['title']}</td>
    <td align="right"><a href="{$msg['report_url']}"><img src="{$STD->tags['image_path']}/report.gif" alt="[!]" title="Report this Message" border="0" /></a></td>
  </tr></table></td>
</tr>
<tr>
  <td class="sformfree">&nbsp;</td>
  <td class="sformfree">{$msg['body']}<br />&nbsp;</td>
</tr>
<tr>
  <td class="sformtitle">&nbsp;</td>
  <td class="sformtitle" align="right"><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr>
  	<td style="font-weight:normal">{$msg['date']}</td>
    <td align="right"><a href="{$STD->tags['root_url']}act=msg&amp;param=07&amp;mid={$msg['mid']}">[Reply]</a> &nbsp; 
    <a href="{$STD->tags['root_url']}act=msg&amp;param=04&amp;mid={$msg['mid']}" onclick="return check_delete();">[Delete]</a></td>
  </tr></table></td>
</tr>
</table>
</div>
HTML;
}

function msg_compose ($msg) {
global $STD;
return <<<HTML
<script type="text/javascript">
<!--
  function set_staff () {
  	document.compose.recipient.disabled = true;
  }
  function set_other () {
  	document.compose.recipient.disabled = false;
  }
-->
</script>
<div class="sform">
<form name="compose" method="post" action="{$STD->tags['root_url']}act=msg&amp;param=06">
<div class="sformstrip">Compose a new message</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Send To</td>
  <td class="sformright"><input type="radio" name="to" value="staff" onclick="set_staff();" /> Site Staff<br />
  	<input type="radio" name="to" value="other" checked="checked" onclick="set_other();" /> Other Recipient
  </td>
<tr>
  <td class="sformleft">Recipient</td>
  <td class="sformright"><input type="text" name="recipient" size="40" value="{$msg['recipient']}" /></td>
</tr>
<tr>
  <td class="sformleft">Subject</td>
  <td class="sformright"><input type="text" name="subject" size="40" value="{$msg['subject']}" /></td>
</tr>
</table>
<div class="sformstrip">Write your message</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Message</td>
  <td class="sformright"><textarea name="body" rows="6" cols="40">{$msg['body']}</textarea></td>
</tr>
</table>
<div class="sformstrip" style="text-align: center"><input type="submit" value="Send Message" /></div>
</form>
</div>
HTML;
}

}