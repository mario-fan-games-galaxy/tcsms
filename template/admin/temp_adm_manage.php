<?php

class template_adm_manage {

function message ($message) {
global $STD;
return <<<HTML
<br />
{$message}
HTML;
}

function site_on_off ($data, $form_elements, $token) {
global $STD;
return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=manage&amp;param=06">
<input type="hidden" name="security_token" value="{$token}" />
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Turn site online or offline</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="30%" class="rowcell3">Site Offline</td>
  <td width="70%" class="rowcell2">{$form_elements['offline']}</td>
</tr>
<tr>
  <td class="rowcell3">Offline Message</td>
  <td class="rowcell2">
    <textarea name="message" rows="10" cols="50" class="textbox">{$data['message']}</textarea>
  </td>
</tr>
<tr>
  <td colspan="2" class="rowcell4" style="text-align:center">
    <input type="submit" value="Update Site" class="button" /></td>
</tr>
</table>
</div>
</form>
<br />
HTML;
}

function msg_list_header ($tab_index, $tab_url, $olinks) {
global $STD;
return <<<HTML
<div align="center">
<br />
<div style="width: 90%">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td width="20%" valign="bottom"><div class="{$tab_index[0]}"><a href="{$tab_url}&amp;tab=0">Open</a></div></td>
  <td width="20%" valign="bottom"><div class="{$tab_index[1]}"><a href="{$tab_url}&amp;tab=1">Closed</a></div></td>
  <td>&nbsp;</td>
</tr>
</table>
</div>
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="4%">&nbsp;</td>
  <td class="rowtitle" width="65%"><a href="{$olinks['m']['url']}">Message</a> {$olinks['m']['img']}</td>
  <td class="rowtitle" width="21%"><a href="{$olinks['u']['url']}">Sender</a> {$olinks['u']['img']}</td>
  <td class="rowtitle" width="10%"><a href="{$olinks['d']['url']}">Date</a> {$olinks['d']['img']}</td>
</tr>
HTML;
}

function msg_list_footer ($pages) {
global $STD;
return <<<HTML
</table>
</div>
<div style="width: 90%; text-align: left">Pages: {$pages}</div>
<br />
</div>
HTML;
}

function msg_list_row ($msg) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2">{$msg['code']}</td>
  <td class="rowcell2">{$msg['title']}</td>
  <td class="rowcell2">{$msg['sender']}</td>
  <td class="rowcell2">{$msg['date']}</td>
</tr>
HTML;
}

function msg_page ($msg, $close_url) {
global $STD;
return <<<HTML
After handling a message, the message should be closed so it isn't responded to by multiple staff members.  After being closed, the message will be archived for future review.
<div align="center">
<br />
<form method="post" action="{$close_url}">
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="20%" class="title">Title</td>
  <td width="80%" class="field"><b>{$msg['title']}</b></td>
</tr>
<tr>
  <td width="20%" class="title">Date</td>
  <td width="80%" class="field">{$msg['report_date']}</td>
</tr>
<tr>
  <td width="20%" class="title">Sent By</td>
  <td width="80%" class="field">{$msg['sender']}</td>
</tr>
<tr>
  <td width="20%" class="title">Message Type</td>
  <td width="80%" class="field">{$msg['type']}</td>
</tr>
<tr>
  <td width="20%" class="title">Status</td>
  <td width="80%" class="field"><span class="highlight2">{$msg['status']}</span></td>
</tr>
<tr>
  <td width="20%" class="title">&nbsp;</td>
  <td width="80%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="20%" class="title" valign="top">Message</td>
  <td width="80%" class="field" style="background-color:#FFFFFF; border:1px solid black; color:#000000;">
    {$msg['message']}
  </td>
</tr>
<tr style="{$msg['show_close']}">
  <td width="20%" class="title">&nbsp;</td>
  <td width="80%" class="field">&nbsp;</td>
</tr>
<tr style="{$msg['show_close']}">
  <td width="20%" class="title">Closed By</td>
  <td width="80%" class="field">{$msg['closed_by']}</td>
</tr>
<tr style="{$msg['show_close']}">
  <td width="20%" class="title">Close Date</td>
  <td width="80%" class="field">{$msg['close_date']}</td>
</tr>
<tr style="{$msg['show_close']}">
  <td width="20%" class="title" valign="top">Staff Notes</td>
  <td width="80%" class="field" style="background-color:#FFFFFF; border:1px solid black; color:#000000;">
    {$msg['admin_comment']}
  </td>
</tr>
<tr style="{$msg['show_close']}">
  <td width="20%" class="title">&nbsp;</td>
  <td width="80%" class="field" style="font-size:10pt; text-align:center">{$msg['inform']}</td>
</tr>
<tr>
  <td width="20%" class="title">&nbsp;</td>
  <td width="80%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="20%" class="title" valign="top">Actions</td>
  <td width="80%" class="field">{$msg['actions']}</td>
</tr>
<tr>
  <td width="20%" class="title">&nbsp;</td>
  <td width="80%" class="field">&nbsp;</td>
</tr>
<tr id="close_frm1" style="display:none">
<td style="border-bottom:1px solid #666666; font-size:14pt" colspan="2">&#8212;&#8212; Close Message</td>
</tr>
<tr id="close_frm2" style="display:none">
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr id="close_frm3" style="display:none">
  <td width="20%" class="title" valign="top">Description of Resolution</td>
  <td width="80%" class="field"><textarea rows="6" cols="40" name="resolution" class="textbox"></textarea></td>
</tr>
<tr id="close_frm5" style="display:none">
  <td width="20%" class="title" valign="top">Send to user?</td>
  <td width="80%" class="field"><input type="radio" name="inform" value="yes" checked="checked" /> Yes 
    <input type="radio" name="inform" value="no" /> No<br />&nbsp;</td>
</tr>
<tr id="close_frm4" style="display:none">
  <td width="20%" class="title">Submit</td>
  <td width="80%" class="field"><input type="submit" value="Close Message" class="button" /></td>
</tr>
</table>
</form>
</div>
HTML;
}

}

?>