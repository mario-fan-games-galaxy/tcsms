<?php

class template_adm_news {

function message ($message) {
global $STD;
return <<<HTML
<br />
{$message}
HTML;
}

function add_news ($token) {
global $STD;
return <<<HTML
Write a new news entry here.  {%recent_updates%} will insert the Recent Updates into your message.
<form method="post" action="{$STD->tags['root_url']}act=news&amp;param=02">
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<br />
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="30%" class="title">Title</td>
  <td width="70%" class="field"><input type="text" name="title" size="40" class="textbox" /></td>
</tr>
<tr>
  <td width="30%" class="title">Content</td>
  <td width="70%" class="field"><textarea name="content" rows="8" cols="40" class="textbox"></textarea></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Complete Form</td>
  <td width="70%" class="field"><input type="submit" value="Add Entry" class="button" /></td>
</tr>
</table>
</div>
</form>
HTML;
}

function edit_header ($olinks) {
global $STD;
return <<<HTML
Edit or remove news entries.
<div align='center'>
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="60%"><a href="{$olinks['t']['url']}">Title</a> {$olinks['t']['img']}</td>
  <td class="rowtitle" width="18%"><a href="{$olinks['u']['url']}">Author</a> {$olinks['u']['img']}</td>
  <td class="rowtitle" width="12%"><a href="{$olinks['d']['url']}">Date</a> {$olinks['d']['img']}</td>
  <td class="rowtitle" width="10%">&nbsp;</td>
</tr>
HTML;
}

function edit_footer ($pages) {
global $STD;
return <<<HTML
</table>
</div>
<div style="width: 90%; text-align: left">Pages: {$pages}</div>
<br />
</div>
HTML;
}

function edit_row ($news) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2">{$news['title']}</td>
  <td class="rowcell2">{$news['author']}</td>
  <td class="rowcell2">{$news['date']}</td>
  <td class="rowcell2" style="text-align: center;">{$news['delete']}</td>
</tr>
HTML;
}

function edit_entry ($news, $token) {
global $STD;
return <<<HTML
Write a new news entry here.  
<form method="post" action="{$STD->tags['root_url']}act=news&amp;param=06">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="nid" value="{$news["nid"]}" />
<div align="center">
<br />
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="30%" class="title">Title</td>
  <td width="70%" class="field"><input type="text" name="title" size="40" value="{$news['title']}" class="textbox" /></td>
</tr>
<tr>
  <td width="30%" class="title">Content</td>
  <td width="70%" class="field"><textarea name="content" rows="8" cols="40" class="textbox">{$news['message']}</textarea></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Complete Form</td>
  <td width="70%" class="field"><input type="submit" value="Modify Entry" class="button" /></td>
</tr>
</table>
</div>
</form>
HTML;
}

}

?>