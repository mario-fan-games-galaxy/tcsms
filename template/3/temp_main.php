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
<div class="sformstrip" style="text-align:center"><input type="submit" value="Go" class="button" /></div>
</form>
</div>
<br />
HTML;
}

function news_footer () {
global $STD;
return <<<HTML
HTML;
}

function news_row ($news) {
global $STD;
return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="0" cellpadding="3">
<tr>
  <td height="25" style="width: 150px; text-align: center;" class="sformstrip">
    <b class="highlight stafflink">{$news['author']}</b>
  </td>
  <td height="25" class="sformstrip">
    {$news['title']}
  </td>
  <td height="25" class="sformstrip" style="text-align:right;font-weight:normal">{$news['date']}</td>
</tr>
<tr>
  <td valign="top" align="center"><br />{$news['icon']}</td>
  <td valign="top" colspan="2">
    {$news['message']}<br />&nbsp;
  </td>
</tr>
<tr>
  <td class="topstrip"></td>
  <td class="topstrip" valign="top" colspan="2">
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
<br />
<div class='newsform'>
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
<td height='25' class='newsstrip' style='text-align:center'>No recent additions since last update.</td>
</tr></table>
HTML;
}

function news_gen_mod_header ($name) {
global $STD;
return <<<HTML
<div class='newsstrip'>{$name}</div>
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
<div class="newssubstrip" style="text-align: center">
  <a href="javascript:show_hide('$id');" style="text-decoration:underline" class="outlink">Click to see newly added $name</a></div>
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
<div class='newssubstrip'>Updated $name</div>
<table class='sformtable' cellspacing='1'>
HTML;
}

function news_gen_updblock_header_col ($name, $id) {
global $STD;
return <<<HTML
<div class="newssubstrip" style="text-align: center">
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
  <td class='newsleftw'><a href='{$res['url']}'><b>{$res['title']}</b></a></td>
  <td class='newsleftw' width='30%'>By {$res['username']}</td>
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

function comments_footer ($pages, $url) {
global $STD;
return <<<HTML
</table>
<div class="sformstrip">
    Pages: {$pages} <span style='font-weight:normal'>| <a href='$url&amp;st=new'>Last Unread</a></span>
</div>
</div>
<br />
HTML;
}

function comments_add ($comment_url, $aexpand) {
global $STD;
return <<<HTML
<div style="text-align:right">
  <span style="font-size:14pt"><a id="reply" href="javascript:show_hide('addc');">Add Comment</a></span>
</div>
<br />
<div style="text-align: center; margin-left: auto; margin-right: auto;">
<script type="text/javascript"><!--
google_ad_client = "pub-2961670651465400";
/* 728x90, created 9/5/08 */
google_ad_slot = "3082258390";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<br />
<div class="sform" id="addc" style="$aexpand">
  <form method="post" action="{$comment_url}">
  <table class="sformtable" cellspacing="0" cellpadding="2">
    <tr>
      <td align="center">
        <br />
        <textarea name="message" cols="50" rows="10" class="textbox"></textarea>
        <br />
        <input type="submit" value="Add Comment" class="button" />
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
  <td width="55%" class="sformstrip">
    <b class="highlight">{$comment['author']}</b><a name="c{$comment['cid']}">
  </td>
  <td class="sformstrip">
    {$comment['date']}
  </td>
  <td class="sformstrip" style="text-align:right;padding:2px">
    <span style="vertical-align:middle">{$comment['report_icon']}</span>
    <span style="vertical-align:middle">{$comment['delete_icon']}</span>
    <span style="vertical-align:middle">{$comment['edit_icon']}</span>
    <span style="vertical-align:middle">{$comment['quote_icon']}</span>
  </td>
</tr>
<tr>
  <td class="sformblock" valign="top" width="100%" colspan="3">
    {$comment['message']}
    <br />&nbsp;
  </td>
</tr>
<tr>
  <td height="6" colspan="3" class="sformdark">
  </td>
</tr>
HTML;
}

function comments_edit ($comment, $chtml, $comment_url) {
global $STD;
return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="0">
{$chtml}
</table>
</div>
<br />
<form method="post" action="{$comment_url}">
<div class="sform">
<div class="sformstrip">Edit the comment below</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Comment</td>
  <td class="sformright"><textarea name="message" cols="50" rows="10" class="textbox">{$comment}</textarea></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Save Edit" class="button" /></div>
</div>
</form>
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
    <td class="sformright"><textarea rows='10' cols='50' name='report' class="textbox"></textarea></td>
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
    <td class="sformright"><textarea rows='10' cols='50' name='report' class="textbox"></textarea></td>
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
    <td class="sformright"><textarea rows='10' cols='50' name='report' class="textbox"></textarea></td>
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
    <td class="sformright"><textarea rows='10' cols='50' name='report' class="textbox"></textarea></td>
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
<style>img{border:0;}</style>
<table border="0" cellpadding="8" cellspacing="0" width="100%"><tr>

<!-- VINNYVIDEO -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=7546"><img
					src="https://www.mfgg.net/staff/icons/vinnyvideo.png"><br>
				<b>VinnyVideo</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1535"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a title="MSN ID"
					href="javascript:void(0)"
					onClick="prompt('Copy the MSN ID below and add it as contact in your client.','VHamilton002[AT-nospam]gmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/msn.png"></a></div>

				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Administrator</b><br>
				Mainsite Admin<br>
				</td>
			</tr>
		</table>
		</td>
		
<!-- CRUISE ELROY -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=13124"><img
					src="https://www.mfgg.net/staff/icons/cruise.png"><br>
				<b>Cruise Elroy</b><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=2967"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Administrator<br/>Quality Control</b><br>
				</td>
			</tr>
		</table>
		</td>
		
<!-- HYLIANDEV -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=12949"><img
					src="https://www.mfgg.net/staff/icons/hyliandev.png"><br>
				<b>HylianDev</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=2727"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Moderator</b><br>
				</td>
			</tr>
		</table>
		</td>

<!-- GATO -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="https://www.mfgg.net/staff/flags/mx.gif"></div>
				<div style="text-align: center;"><a
					href="https://www.mfgg.net/index.php?act=user&param=01&uid=2369"><img
					src="http://www.mfgg.net/staff/icons/gato.png"><br>
				<b>Gato</b></a><br>
				<a title="Forum Profile"
					href="https://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1516"><img
					src="https://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control<br/>Forum Moderator</b><br>
				</td>
			</tr>
		</table>
		</td>

<!-- ALEX 
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/se.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=7253"><img
					src="http://www.mfgg.net/staff/icons/alex.png"><br>
				<b>Alex</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=229"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a
					title="Email" href="javascript:void(0)"
					onClick="prompt('Copy the email address below and paste it into the \'To\' field of your email client.','idv_x[AT-nospam]hotmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/email.png"></a><a
					title="AIM Screen Name" href="javascript:void(0)"
					onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','alexanderidv'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/aim.png"></a><a title="MSN ID"
					href="javascript:void(0)"
					onClick="prompt('Copy the MSN ID below and add it as contact in your client.','idv_x[AT-nospam]hotmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/msn.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control</b><br>
				</td>

			</tr>
		</table>
		</td>
		-->
		
<!-- YOSHBERT -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/ca.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=5993"><img
					src="https://www.mfgg.net/staff/icons/yoshbert.png"><br>
				<b>Yoshbert</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1596"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Mainsite
				Admin</b><br>
				</td>
			</tr>
		</table>
		</td>
</tr><tr>
		
<!-- CHAOXYS -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=3751"><img
					src="http://www.mfgg.net/staff/icons/chaoxys.png"><br>
				<b>Chaoxys</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=61"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
					Control</b><br>
				</td>
			</tr>
		</table>
		</td>
		
<!-- DUSTINVGMASTER -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=11138"><img
					src="http://www.mfgg.net/staff/icons/dustinvgmaster.png"><br>
				<b>Dustinvgmaster</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1851"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control<br/>Forum Moderator</b><br>
				</td>
			</tr>
		</table>
		</td>
		
<!-- ZERO KIRBY -->
	<td width="20%" height="100%">
	<table class="sform sformtablex" cellpadding="0" cellspacing="0"
		height="100%">
		<tr>
			<td width="100%" class="sformstrip staff">
			<div style="position: absolute;"><img
				style="position: relative;"
				src="http://www.mfgg.net/staff/flags/us.gif"></div>
			<div style="text-align: center;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=4237"><img
				src="http://www.mfgg.net/staff/icons/zerokirby.png"><br>
			<b>Zero Kirby</b></a><br>
			<a title="Forum Profile"
				href="https://forums.mfgg.net/memberlist.php?mode=viewprofile&u=139"><img
				src="http://www.mfgg.net/staff/forumprofile.png"></a><a
				title="AIM Screen Name" href="javascript:void(0)"
				onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','ZeroKirbyGames'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
				src="http://www.mfgg.net/staff/aim.png"></a><a title="MSN ID"
				href="javascript:void(0)"
				onClick="prompt('Copy the MSN ID below and add it as contact in your client.','ZeroKirbyGames[AT-nospam]hotmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
				src="http://www.mfgg.net/staff/msn.png"></a></div>
			</td>
		</tr>
		<tr>
			<td width="100%"
				style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control<br/>Forum Moderator</b><br>
			</td>
		</tr>
	</table>
	</td>

<!-- LANGTON -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/gb.gif"></div>
				<div style="text-align: center;"><a
					href="https://www.mfgg.net/index.php?act=user&param=01&uid=12896"><img
					src="https://www.mfgg.net/staff/icons/langton.png"><br>
				<b>Langton</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=2867"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control</b><br>
				</td>
			</tr>
		</table>
		</td>

<!-- YOSHIN -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/ca.gif"></div>
				<div style="text-align: center;"><a
					href="https://www.mfgg.net/index.php?act=user&param=01&uid=9318"><img
					src="https://www.mfgg.net/staff/icons/yoshin.png"><br>
				<b>Yoshin</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=4250"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Wiki Sysop</b><br>
				</td>
			</tr>
		</table>
		</td>
	</tr><tr>
		
<!-- THUNDER DRAGON -->
	<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=6"><img
					src="http://www.mfgg.net/staff/icons/thunderdragon.png"><br>
				<b>Thunder Dragon</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=59"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>

				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Founder</b><br>
				</td>
			</tr>
		</table>
	</td>

<!-- NITE SHADOW 
	<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=17"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><img
					src="https://www.mfgg.net/staff/icons/nite.png"><br>
				<b>Nite Shadow</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1827"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Wiki
				Sysop</b><br>
				</td>
			</tr>
		</table>
		</td>
-->
		
<!-- TECHOKAMI -->
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=53"><img
					src="http://www.mfgg.net/staff/icons/techokami.png"><br>
				<b>Techokami</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=62"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a
					title="Email" href="javascript:void(0)"
					onClick="prompt('Copy the email address below and paste it into the \'To\' field of your email client.','techokami[AT-nospam]gdarcade[DOT-nospam]taloncrossing.com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/email.png"></a><a title="MSN ID"
					href="javascript:void(0)"
					onClick="prompt('Copy the MSN ID below and add it as contact in your client.','techokami[AT-nospam]hotmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/msn.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Security
				Ninja</b>
				</td>
			</tr>
		</table>
		</td>
<!-- VITIMAN
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=4335"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><img
					src="http://www.mfgg.net/staff/icons/rystar.png"><br>
				<b>Vitiman</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=2263"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Wiki
				Sysop</b><br>
				</td>
			</tr>
		</table>
		</td>
	ELYK
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=4748"><img
					src="http://www.mfgg.net/staff/icons/elyk.png"><br>
				<b>Elyk</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=166"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a
					title="Email" href="javascript:void(0)"
					onClick="prompt('Copy the email address below and paste it into the \'To\' field of your email client.','eelyk19[AT-nospam]hotmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/email.png"></a><a
					title="AIM Screen Name" href="javascript:void(0)"
					onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','e1yk19'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/aim.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Administrator</b><br>
				Site Moderator<br>
				Quality Control<br>
				</td>

			</tr>
		</table>
		</td>
	MASON
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><img
					src="http://www.mfgg.net/staff/icons/mason.png"><br>
				<b>Mason</b><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1542"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Wiki
				Sysop</b><br>
				</td>
			</tr>
		</table>
		</td>
    
	CHAR
	<td width="20%" height="100%">
	<table class="sform sformtablex" cellpadding="0" cellspacing="0"
		height="100%">
		<tr>
			<td width="100%" class="sformstrip staff">
			<div style="position: absolute;"><img
				style="position: relative;"
				src="http://www.mfgg.net/staff/flags/in.gif"></div>
			<div style="text-align: center;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=3586"><img
				src="http://www.mfgg.net/staff/icons/char.png"><br>
			<b>Char</b></a><br>
			<a title="Forum Profile"
				href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=63"><img
				src="http://www.mfgg.net/staff/forumprofile.png"></a><a
				title="Email" href="javascript:void(0)"
				onClick="prompt('Copy the email address below and paste it into the \'To\' field of your email client.','elite.charizard[AT-nospam]gmail[DOT-nospam]com'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
				src="http://www.mfgg.net/staff/email.png"></a><a
				title="AIM Screen Name" href="javascript:void(0)"
				onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','EliteCharizard'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
				src="http://www.mfgg.net/staff/aim.png"></a><a title="MSN ID"
				href="javascript:void(0)"
				onClick="prompt('Copy the MSN ID below and add it as contact in your client.','char[AT-nospam]charhost[DOT-nospam]co.cc'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
				src="http://www.mfgg.net/staff/msn.png"></a></div>
			</td>
		</tr>
		<tr>
			<td width="100%"
				style="text-align: center; vertical-align: middle; height: 56px"><b>Webmaster</b><br>
			Quality Control<br>
			Site Moderator<br>
			</td>

		</tr>
	</table>
	</td>

	GAMEBOY
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=5150"><img
					src="http://www.mfgg.net/staff/icons/gameboy.png"><br>
				<b>Gameboy</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1517"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Moderator</b><br>
				</td>
			</tr>
		</table>
		</td>
		
	FUJIKO
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/ca.gif"></div>
				<div style="text-align: center;"><img
					src="http://www.mfgg.net/staff/icons/fujiko.png"><br>
				<b>Fujiko</b><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=2410"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Moderator</b><br>
				</td>
			</tr>
		</table>
		</td>
		
	ULTRAMARIO
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/fi.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=2567"><img
					src="http://www.mfgg.net/staff/icons/ultramario.png"><br>
				<b>Ultramario</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=306"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a
					title="Email" href="javascript:void(0)"
					onClick="prompt('Copy the email address below and paste it into the \'To\' field of your email client.','antti.taipale[AT-nospam]pp3[DOT-nospam]inet.fi'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/email.png"></a><a
					title="AIM Screen Name" href="javascript:void(0)"
					onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','Ultramario120666'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/aim.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Quality
				Control</b><br>
				</td>
			</tr>
		</table>
		</td>
		
	GUINEA
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">
			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/at.gif"></div>
				<div style="text-align: center;"><a
					href="http://www.mfgg.net/index.php?act=user&param=01&uid=579"><img
					src="http://www.mfgg.net/staff/icons/guinea.png"><br>
				<b>Guinea</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=55"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a><a
					title="AIM Screen Name" href="javascript:void(0)"
					onClick="prompt('Copy the AIM screen name below and add it as contact in your client.','Guinea3000'.replace('[AT-nospam]','@').replace('[DOT-nospam]','.'));void(0);"><img
					src="http://www.mfgg.net/staff/aim.png"></a></div>

				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Administrator</b><br>
				Mainsite Admin<br>
				</td>
			</tr>
		</table>
		</td>
		
	DJ COCO
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=7029"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/at.gif"></div>
				<div style="text-align: center;"><img
					src="http://www.mfgg.net/staff/icons/djcoco.png"><br>
				<b>DJ Coco</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1741"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%" style="text-align: center; vertical-align: middle; height: 56px">
				<b>Forum Administrator</b><br>
				Mainsite Admin
				</td>
			</tr>
		</table>
		</td>
		
	MILES
		<td width="20%" height="100%">
		<table class="sform sformtablex" cellpadding="0" cellspacing="0"
			height="100%">

			<tr>
				<td width="100%" class="sformstrip staff">
				<div style="position: absolute;"><a
				href="http://www.mfgg.net/index.php?act=user&param=01&uid=1847"><img
					style="position: relative;"
					src="http://www.mfgg.net/staff/flags/us.gif"></div>
				<div style="text-align: center;"><img
					src="https://www.mfgg.net/staff/icons/miles.png"><br>
				<b>Miles</b></a><br>
				<a title="Forum Profile"
					href="http://forums.mfgg.net/memberlist.php?mode=viewprofile&u=1763"><img
					src="http://www.mfgg.net/staff/forumprofile.png"></a></div>
				</td>
			</tr>
			<tr>
				<td width="100%"
					style="text-align: center; vertical-align: middle; height: 56px"><b>Forum Administrator</b><br>Quality Control<br>
				</td>
			</tr>
		</table>
		</td>

		
--></tr></table>
<br>
<table class="sformtablex sform" style="width: 99%" align="center" cellpadding="4" cellspacing="0">
<tr><td colspan="2" class="sformstrip">Special Thanks</td></tr>
<tbody>

<tr>
<td width="15%" height="18"><b>Retriever II</b></td>
<td height="18">Currently hosting MFGG, has made immense contributions to MFGG including the system running the site. Former webmaster.</td>
</tr><tr>
<td width="15%" height="18"><b>Black Squirrel</b></td>
<td height="18">Former Admin and great QC Admin. Handled 10,000 submissions.</td>
</tr><tr>
<td width="15%" height="18"><b>Willy Goldwater</b></td>
<td height="18">Generously paid part of MFGG's hosting bill in the past.</td>
</tr><tr>
<td width="15%" height="18"><b>Techokami</b></td>
<td height="18">In-depth testing, security help, and preparation for MFGG 2.0</td>
</tr><tr>
<td width="15%" height="18"><b>DJ Yoshiman</b></td>
<td height="18">Stuck around MFGG since time began.</td>
</tr><tr>
<td width="15%" height="18"><b>Kritter</b></td>
<td height="18">An original staffer of MFGG and creator of the original classic skin.</td>
</tr><tr>
<td width="15%" height="18"><b>Char</b></td>
<td height="18">Former webmaster who contributed greatly to our site</td>
</tr><tr>
<td width="15%" height="18"><b>Guinea</b></td>
<td height="18">Former admin who served as staff member for nearly a decade and led MFGG's first Community Fangame Project.</td>
</tr><tr>
<td width="15%" height="18"><b>Medaforcer</b></td>
<td height="18">Used to volunteer administering badges to forum members.</td>
</tr><tr>
<td width="15%" height="18"><b>Hylke Bons</b></td>
<td height="18">MSN and AIM icons used on this page. (The other icons are from the Tango set)</td>
</tr><tr><td colspan="2" class="sformstrip">Staff icons made by <i>Alex, Ashura, Bacteriophage, Cruise Elroy, Dexy, Fujiko, Gato, Old Yuanxian, and Stixdude</i>.</td></tr>
</tbody></table>
HTML;
}


function comments_add_full ($comment, $comment_url) {
global $STD;
return <<<HTML
<form method="post" action="{$comment_url}">
<div class="sform">
<div class="sformstrip">Add your comment below</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Comment</td>
  <td class="sformright"><textarea name="message" cols="50" rows="10" class="textbox">{$comment}</textarea></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Add comment" class="button" /></div>
</div>
</form>
HTML;
}

}
?>
