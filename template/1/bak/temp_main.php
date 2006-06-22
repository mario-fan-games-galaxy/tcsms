<?php

class template_main {


function news_header () {
global $STD;
return <<<HTML
HTML;
}

function news_archive_header ($from, $to) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$STD->tags['root_url']}act=main&amp;param=08">
<div class="sformstrip">Select a range of updates</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleftw" style="width:50%; font-weight:bold">From: &nbsp; {$from['m']} {$from['d']} {$from['y']}</td>
  <td class="sformleftw" style="width:50%; font-weight:bold">To: &nbsp; {$to['m']} {$to['d']} {$to['y']}</td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Go" /></div>
</form>
</div>
<br />
HTML;
}

function news_footer () {
global $STD;
return <<<HTML
<a href="{$STD->tags['root_url']}act=main&amp;param=08">Updates Archive</a>
HTML;
}

function news_row ($news) {
global $STD;
return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="0" cellpadding="3">
<tr>
  <td height="25" width="20%" class="sformstrip">
    <b class="highlight">{$news['author']}</b>
  </td>
  <td height="25" class="sformstrip">
    {$news['title']}
  </td>
  <td height="25" class="sformstrip" style="text-align:right;font-weight:normal">{$news['date']}</td>
</tr>
<tr>
  <td width="20%" valign="top">{$news['icon']}</td>
  <td valign="top" width="80%" colspan="2">
    {$news['message']}<br />&nbsp;
  </td>
</tr>
<tr>
  <td width="20%" style='border-top:1px solid #444466'></td>
  <td valign="top" width="80%" colspan="2" style='border-top:1px solid #444466'>
    <a href="{$STD->tags['root_url']}act=main&amp;param=02&amp;id={$news['nid']}">View Comments ({$news['comments']})</a> | 
    <a href="{$STD->tags['root_url']}act=main&amp;param=02&amp;id={$news['nid']}&amp;exp=1#reply">Leave Comment</a>
  </td>
</tr>
<tr>
  <td height="6" colspan="3" class="sformdark">
  </td>
</tr>
</table>
</div>
<br />
HTML;
}

function news_update_header () {
global $STD;
return <<<HTML
<span class='highlight'><b>Recent Additions</b></span>
<br /><div class='sform'>
HTML;
}

function news_update_footer () {
global $STD;
return <<<HTML
</div>
HTML;
}

function news_no_updates () {
global $STD;
return <<<HTML
<table class='sformtable' cellspacing='1'><tr>
<td height='25' class='sformstrip' style='text-align:center'>No recent additions since last update.</td>
</tr></table>
HTML;
}

function news_gen_mod_header ($name) {
global $STD;
return <<<HTML
<div class='sformstrip'>$name</div>
HTML;
}

function news_gen_mod_footer () {
global $STD;
return <<<HTML
HTML;
}

function news_gen_block_header ($name) {
global $STD;
return <<<HTML
<table class='sformtable' cellspacing='1'>
HTML;
}

function news_gen_block_header_col ($name, $id) {
global $STD;
return <<<HTML
<div class="sformsubstrip" style="text-align: center">
  <a href="javascript:show_hide('$id');" style="text-decoration:underline">Click to see newly added $name</a></div>
<table id="$id" class='sformtable' style='display:none' cellspacing='1'>
HTML;
}

function news_gen_block_footer () {
global $STD;
return <<<HTML
</table>
HTML;
}

function news_gen_updblock_header ($name) {
global $STD;
return <<<HTML
<div class='sformsubstrip'>Updated $name</div>
<table class='sformtable' cellspacing='1'>
HTML;
}

function news_gen_updblock_header_col ($name, $id) {
global $STD;
return <<<HTML
<div class="sformsubstrip" style="text-align: center">
  <a href="javascript:show_hide('$id');" style="text-decoration:underline">Click to see updated $name</a></div>
<table id="$id" class='sformtable' style='display:none' cellspacing='1'>
HTML;
}

function news_gen_updblock_footer () {
global $STD;
return <<<HTML
</table>
HTML;
}

function news_gen_block_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td class='sformleftw'><a href='{$res['url']}'><b>{$res['title']}</b></a></td>
  <td class='sformleftw' width='30%'>By {$res['username']}</td>
</tr>
HTML;
}

function comments_header () {
global $STD;
return <<<HTML
<script type='text/javascript'>
<!--
  function check_delete () {
  	form_check = confirm('Are you sure you want to delete this comment?');
  	
  	if (form_check == true) {
  		return true;
  	} else {
  		return false;
  	}
  }
-->
</script>
<table border="0" cellspacing="0" cellpadding="0" width="95%">
    <tr>
      <td class="tablecell1" colspan="2">
        <span class="boxheader">Comments</span>
      </td>
    </tr>
</table>
<div class="sform" id="comments">
<table class="sformtable" cellspacing="0">
HTML;
}

function comments_footer () {
global $STD;
return <<<HTML
</table>
</div>
<br />
HTML;
}

function comments_add ($comment_url, $aexpand) {
global $STD;
return <<<HTML
<table width="95%" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="100%" align="right">
      <span style="font-size:14pt"><a id="reply" href="javascript:show_hide('addc');">Add Comment</a></span>
    </td>
  </tr>
</table>
<div class="sform" id="addc" style="$aexpand">
  <form method="post" action="{$comment_url}">
  <table class="sformtable" cellspacing="0" cellpadding="2">
    <tr>
      <td align="center">
        <br />
        <textarea name="message" cols="40" rows="4"></textarea>
        <br />
        <input type="submit" value="Add Comment" />
        <br />&nbsp;
      </td>
    </tr>
  </table>
  </form>
</div>
<br />
HTML;
}

function comments_none () {
global $STD;
return <<<HTML
<tr>
<td height="25" class="sformstrip" style="text-align:center">No comments have been left.</td>
</tr>
HTML;
}

function comments_row ($comment) {
global $STD;
return <<<HTML
<tr>
  <td height="25" width="55%" class="sformstrip">
    <b class="highlight">{$comment['author']}</b>
  </td>
  <td height="25" class="sformstrip">
    {$comment['date']}
  </td>
  <td height="25" class="sformstrip" style="text-align:right;padding:2px">
    {$comment['email_icon']} {$comment['website_icon']} <a href="{$comment['report_url']}">
    <img src="{$STD->tags['image_path']}/report.gif" border="0" alt="[!]" title="Report This Comment" /></a>
    {$comment['delete_icon']}
  </td>
</tr>
<tr>
  <td valign="top" height="50" width="100%" colspan="3">
    {$comment['message']}
  </td>
</tr>
<tr>
  <td height="6" colspan="3" class="sformdark">
  </td>
</tr>
HTML;
}

function report_sub ($id, $url, $title) {
global $STD;
return <<<HTML
<form method='post' action='{$url}'>
<input type='hidden' name='id' value='{$id}'>
<div class="sform">
<div class="sformstrip">Report a submission</div>
<table class="sformtable" cellspacing="1">
  <tr>
    <td class="sformleft"><b>Submission</b></td>
    <td class="sformright"><b>{$title}</b></td>
  </tr>
  <tr>
    <td class="sformleft"><b>Report</b><br />Enter your report in this box to alert site staff to a submission theft or objectionable content.</td>
    <td class="sformright"><textarea rows='6' cols='40' name='report'></textarea></td>
  </tr>
</table>
<div class="sformstrip" style="text-align:center"><input type='submit' value='Send Report' class='button' /></div>
</div>
</form>
HTML;
}

function report_sub_com ($id, $url, $title, $c_author) {
global $STD;
return <<<HTML
<form method='post' action='{$url}'>
<input type='hidden' name='id' value='{$id}'>
<div class="sform">
<div class="sformstrip">Report a comment</div>
<table class="sformtable" cellspacing='1'>
  <tr>
    <td class="sformleft"><b>Submission</b></td>
    <td class="sformright"><b>{$title}</b></td>
  </tr>
  <tr>
    <td class="sformleft"><b>Comment By</b></td>
    <td class="sformright">{$c_author}</td>
  </tr>
  <tr>
    <td class="sformleft"><b>Report</b><br />Enter your report in this box to alert site staff to objectionable content in this comment.</td>
    <td class="sformright"><textarea rows='6' cols='40' name='report'></textarea></td>
  </tr>
</table>
<div class="sformstrip" style="text-align:center"><input type='submit' value='Send Report' class='button' /></div>
</div>
</form>
HTML;
}

function report_news_com ($id, $url, $title, $c_author) {
global $STD;
return <<<HTML
<form method='post' action='{$url}'>
<input type='hidden' name='id' value='{$id}'>
<div class="sform">
<div class="sformstrip">Report a comment</div>
<table class="sformtable" cellspacing='1'>
  <tr>
    <td class="sformleft"><b>News Entry</b></td>
    <td class="sformright"><b>{$title}</b></td>
  </tr>
  <tr>
    <td class="sformleft"><b>Comment By</b></td>
    <td class="sformright">{$c_author}</td>
  </tr>
  <tr>
    <td class="sformleft"><b>Report</b><br />Enter your report in this box to alert site staff to objectionable content in this comment.</td>
    <td class="sformright"><textarea rows='6' cols='40' name='report'></textarea></td>
  </tr>
</table>
<div class="sformstrip" style="text-align:center"><input type='submit' value='Send Report' class='button' /></div>
</div>
</form>
HTML;
}

function report_msg ($id, $url, $title, $m_author) {
global $STD;
return <<<HTML
<form method='post' action='{$url}'>
<input type='hidden' name='id' value='{$id}'>
<div class="sform">
<div class="sformstrip">Report a message</div>
<table class="sformtable" cellspacing='1'>
  <tr>
    <td class="sformleft"><b>Sender</b></td>
    <td class="sformright">{$m_author}</td>
  </tr>
  <tr>
    <td class="sformleft"><b>Subject</b></td>
    <td class="sformright">{$title}</td>
  </tr>
  <tr>
    <td class="sformleft"><b>Report</b><br />Enter your report in this box to alert site staff to objectionable content in this message.</td>
    <td class="sformright"><textarea rows='6' cols='40' name='report'></textarea></td>
  </tr>
</table>
<div class="sformstrip" style="text-align:center"><input type='submit' value='Send Report' class='button' /></div>
</div>
</form>
HTML;
}

function staff_page () {
global $STD;
return <<<HTML
<div class="sform">
<div class="sformstrip">Website Staff</div>
<table class="sformtable" cellspacing="0" cellpadding="4">
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Thunder Dragon</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Founder</td>
  <td valign="top" width="60%">Other Roles: Quality Control, Forum Administrator</td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/ree_sav3.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Retriever II</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Webmaster</td>
  <td valign="top" width="60%">Other Roles: Host, Forum Administrator</td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>ShadowMan</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Quality Control</td>
  <td valign="top" width="60%">Other Roles: Forum Administrator</td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Parakarry</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Quality Control</td>
  <td valign="top" width="60%"></td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Black Squirrel</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Quality Control</td>
  <td valign="top" width="60%"></td>
</tr>
</table>
<div class="sformstrip">Forum Staff</div>
<table class="sformtable" cellspacing="0" cellpadding="4">
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Klobber</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Administrator</td>
  <td valign="top" width="60%"></td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Trasher</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Moderator</td>
  <td valign="top" width="60%"></td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Jeff Silvers</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Moderator</td>
  <td valign="top" width="60%"></td>
</tr>
<tr>
  <td valign="top" width="10%" rowspan="2"><img src="http://jaquadro.homedns.org/~test/tcsms_icon.png"></td>
  <td height="18" colspan="2" style='border-bottom: 1px solid #333344'><b>Jak</b></td>
</tr>
<tr>
  <td valign="top" width="30%">Primary Role: Moderator</td>
  <td valign="top" width="60%"></td>
</tr>
</table>
<div class="sformstrip">Special Thanks</div>
<table class="sformtable" cellspacing="0" cellpadding="4">
<tr>
  <td height="18" style='border-bottom: 1px solid #333344'><b>Willy Goldwater</b> generously pays part of MFGG's hosting bill each month.</td>
</tr>
</table>
</div>
<br />
HTML;
}

}
?>