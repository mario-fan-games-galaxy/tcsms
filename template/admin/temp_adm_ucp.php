<?php

class template_adm_ucp {

function message ($message) {
global $STD;
return <<<HTML
<br />
{$message}
HTML;
}

function ucp_list_header ($tab_index, $tab_url, $olinks) {
global $STD;
return <<<HTML
<div align="center">
<br />
<div style="width: 90%">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td valign="bottom"><div class="{$tab_index[0]}"><a href="{$tab_url}&amp;tab=0">ALL</a></div></td>
  <td valign="bottom"><div class="{$tab_index[1]}"><a href="{$tab_url}&amp;tab=1">#</a></div></td>
  <td valign="bottom"><div class="{$tab_index[2]}"><a href="{$tab_url}&amp;tab=2">A</a></div></td>
  <td valign="bottom"><div class="{$tab_index[3]}"><a href="{$tab_url}&amp;tab=3">B</a></div></td>
  <td valign="bottom"><div class="{$tab_index[4]}"><a href="{$tab_url}&amp;tab=4">C</a></div></td>
  <td valign="bottom"><div class="{$tab_index[5]}"><a href="{$tab_url}&amp;tab=5">D</a></div></td>
  <td valign="bottom"><div class="{$tab_index[6]}"><a href="{$tab_url}&amp;tab=6">E</a></div></td>
  <td valign="bottom"><div class="{$tab_index[7]}"><a href="{$tab_url}&amp;tab=7">F</a></div></td>
  <td valign="bottom"><div class="{$tab_index[8]}"><a href="{$tab_url}&amp;tab=8">G</a></div></td>
  <td valign="bottom"><div class="{$tab_index[9]}"><a href="{$tab_url}&amp;tab=9">H</a></div></td>
  <td valign="bottom"><div class="{$tab_index[10]}"><a href="{$tab_url}&amp;tab=10">I</a></div></td>
  <td valign="bottom"><div class="{$tab_index[11]}"><a href="{$tab_url}&amp;tab=11">J</a></div></td>
  <td valign="bottom"><div class="{$tab_index[12]}"><a href="{$tab_url}&amp;tab=12">K</a></div></td>
  <td valign="bottom"><div class="{$tab_index[13]}"><a href="{$tab_url}&amp;tab=13">L</a></div></td>
  <td valign="bottom"><div class="{$tab_index[14]}"><a href="{$tab_url}&amp;tab=14">M</a></div></td>
  <td valign="bottom"><div class="{$tab_index[15]}"><a href="{$tab_url}&amp;tab=15">N</a></div></td>
  <td valign="bottom"><div class="{$tab_index[16]}"><a href="{$tab_url}&amp;tab=16">O</a></div></td>
  <td valign="bottom"><div class="{$tab_index[17]}"><a href="{$tab_url}&amp;tab=17">P</a></div></td>
  <td valign="bottom"><div class="{$tab_index[18]}"><a href="{$tab_url}&amp;tab=18">Q</a></div></td>
  <td valign="bottom"><div class="{$tab_index[19]}"><a href="{$tab_url}&amp;tab=19">R</a></div></td>
  <td valign="bottom"><div class="{$tab_index[20]}"><a href="{$tab_url}&amp;tab=20">S</a></div></td>
  <td valign="bottom"><div class="{$tab_index[21]}"><a href="{$tab_url}&amp;tab=21">T</a></div></td>
  <td valign="bottom"><div class="{$tab_index[22]}"><a href="{$tab_url}&amp;tab=22">U</a></div></td>
  <td valign="bottom"><div class="{$tab_index[23]}"><a href="{$tab_url}&amp;tab=23">V</a></div></td>
  <td valign="bottom"><div class="{$tab_index[24]}"><a href="{$tab_url}&amp;tab=24">W</a></div></td>
  <td valign="bottom"><div class="{$tab_index[25]}"><a href="{$tab_url}&amp;tab=25">X</a></div></td>
  <td valign="bottom"><div class="{$tab_index[26]}"><a href="{$tab_url}&amp;tab=26">Y</a></div></td>
  <td valign="bottom"><div class="{$tab_index[27]}"><a href="{$tab_url}&amp;tab=27">Z</a></div></td>
  <td>&nbsp;</td>
</tr>
</table>
</div>
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="60%"><a href="{$olinks['u']['url']}">Username</a> {$olinks['u']['img']}</td>
  <td class="rowtitle" width="40%"><a href="{$olinks['g']['url']}">Group</a> {$olinks['g']['img']}</td>
</tr>
HTML;
}

function ucp_list_footer ($pages) {
global $STD;
return <<<HTML
</table>
</div>
<div style="width: 90%; text-align: left">{$pages}</div>
<br />
</div>
HTML;
}

function ucp_list_row ($user) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2">
    <a href="{$STD->tags['root_url']}act=ucp&amp;param=02&amp;u={$user['uid']}">{$user['username']}</a></td>
  <td class="rowcell2">{$user['group_name']}</td>
</tr>
HTML;
}

function ucp_list_norows () {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2" colspan="2" style="text-align:center">No users found</td>
</tr>
HTML;
}

function ucp_find_users ( ) {
global $STD;
return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=15">
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Quick Search</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3">Username contains...</td>
  <td width="60%" class="rowcell2"><input type="text" name="username" size="40" class="textbox" /></td>
</tr>
<tr>
  <td colspan="2" class="rowcell4" style="text-align:center">
    <input type="submit" value="Find Users" class="button" /></td>
</tr>
</table>
</div>
</form>
<br />
HTML;
}

function ucp_find_email ( ) {
global $STD;
return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=17">
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Quick Search</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3">Email contains...</td>
  <td width="60%" class="rowcell2"><input type="text" name="email" size="40" class="textbox" /></td>
</tr>
<tr>
  <td colspan="2" class="rowcell4" style="text-align:center">
    <input type="submit" value="Find Users" class="button" /></td>
</tr>
</table>
</div>
</form>
<br />
HTML;
}

function ucp_find_ip ( ) {
global $STD;
return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=19">
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Quick Search</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3">IP address contains...</td>
  <td width="60%" class="rowcell2"><input type="text" name="ip" size="40" class="textbox" /></td>
</tr>
<tr>
  <td colspan="2" class="rowcell4" style="text-align:center">
    <input type="submit" value="Find Users" class="button" /></td>
</tr>
</table>
</div>
</form>
<br />
HTML;
}

function ucp_find_list_header () {
global $STD;
return <<<HTML
<div align="center">
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="60%">Username</td>
  <td class="rowtitle" width="40%">Group</td>
</tr>
HTML;
}

function ucp_edit_user ($user, $form_elements, $token) {
global $STD;
return <<<HTML
<script type="text/javascript">
  function check_drop() {
      form_check = confirm('Warning: Dropping this user will permanently delete them from the database.\n\nDo you still wish to continue?');
    
      if (form_check == true) {
          return true;
      } else {
          return false;
      }
  }
  function check_comment_purge() {
      form_check2 = confirm('Warning: You have just selected to permanently delete EVERY comment this user has ever made on this site.\n\nDo you still wish to continue?');
    
      if (form_check2 == true) {
          return true;
      } else {
          return false;
      }
  }
</script>

Use this page to modify the selected user.  To change the user's password, fill in the password boxes, leave them blank otherwise.
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=03">
<input type="hidden" name="uid" value="{$user['uid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<br />
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="30%" class="title">User ID</td>
  <td width="70%" class="field"><b>{$user['uid']}</b></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Username</td>
  <td width="70%" class="field"><input type="text" name="username" class="textbox" size="40" value="{$user['username']}" /></td>
</tr>
<tr>
  <td width="30%" class="title">Email</td>
  <td width="70%" class="field"><input type="text" name="email" class="textbox" size="40" value="{$user['email']}" /></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title" valign="top">Group</td>
  <td width="70%" class="field">{$form_elements['group']}</td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Registered IP</td>
  <td width="70%" class="field"><b>{$user['registered_ip']}&nbsp;</b></td>
</tr>
<tr>
  <td width="30%" class="title">Current IP</td>
  <td width="70%" class="field"><b>{$user['s_ip']}&nbsp;</b></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">New Password</td>
  <td width="70%" class="field"><input type="password" name="password" class="textbox" size="40" value="" /></td>
</tr>
<tr>
  <td width="30%" class="title">Retype Password</td>
  <td width="70%" class="field"><input type="password" name="password2" class="textbox" size="40" value="" /></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Website</td>
  <td width="70%" class="field"><input type="text" name="website" class="textbox" size="40" value="{$user['website']}" /></td>
</tr>
<tr>
  <td width="30%" class="title">Website URL</td>
  <td width="70%" class="field"><input type="text" name="weburl" class="textbox" size="40" value="{$user['weburl']}" /></td>
</tr>
<tr>
  <td width="30%" class="title">Personal Icon</td>
  <td width="70%" class="field"><input type="text" name="icon" class="textbox" size="40" value="{$user['icon']}" /></td>
</tr>
<tr>
  <td width="30%" class="title">Show Email</td>
  <td width="70%" class="field">{$form_elements['show_email']}</td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Default Order</td>
  <td width="70%" class="field">{$form_elements['order']}</td>
</tr>
<tr>
  <td width="30%" class="title">Skin</td>
  <td width="70%" class="field">{$form_elements['skin']}</td>
</tr>
<tr>
  <td width="30%" class="title">Items per Page</td>
  <td width="70%" class="field">{$form_elements['items_per_page']}</td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Complete Form</td>
  <td width="70%" class="field"><input type="submit" class="button" value="Update User" /> 
    <input type="submit" name="drop_item" class="button" value="DROP User" style="background-color: #FF6169; color: white" onclick="return check_drop();" /> 
    <input type="submit" name="comment_purge" class="button" value="PURGE User Comments" style="background-color: #FF6169; color: white" onclick="return check_comment_purge();" />
  </td>
</tr>
</table>
</div>
</form>
HTML;
}

function group_list_header ($olinks) {
global $STD;
return <<<HTML
<div align="center">
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="50%"><a href="{$olinks['g']['url']}">Group</a> {$olinks['g']['img']}</td>
  <td class="rowtitle" width="25%">ACP Access</td>
  <td class="rowtitle" width="25%">Moderator</td>
</tr>
HTML;
}

function group_list_footer ($pages, $menu) {
global $STD;
return <<<HTML
</table>
</div>
<div style="width: 90%; text-align: left">Pages: {$pages}</div>
<br />
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=10">
<table border="0" cellspacing="0" cellpadding="2" width="90%">
<tr>
  <td width="100%" colspan="3" align="center">
    <input type="submit" value="Create Group Based On" class="button" /> {$menu}
  </td>
</tr>
</table>
<br />
</form>
</div>
HTML;
}

function group_list_row ($group) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2">
    <a href="{$STD->tags['root_url']}act=ucp&amp;param=08&amp;gid={$group['gid']}">{$group['group_name']}</a></td>
  <td class="rowcell2">{$group['acp']}</td>
  <td class="rowcell2">{$group['mod']}</td>
</tr>
HTML;
}

function group_edit ($group, $form_elements, $token) {
global $STD;
return <<<HTML
<script type="text/javascript">
  function check_drop() {
      form_check = confirm('Warning: Dropping this group will permanently delete them from the database.\n\nDo you still wish to continue?');
    
      if (form_check == true) {
          return true;
      } else {
          return false;
      }
  }
</script>

Use this page to modify the selected group.
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=09">
<input type="hidden" name="gid" value="{$group['gid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Group Settings</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3">Group ID</td>
  <td width="60%" class="rowcell2">{$group['gid']}</td>
</tr>
<tr>
  <td class="rowcell3">Group Name</td>
  <td class="rowcell2"><input type="text" name="group_name" class="textbox" size="40" value="{$group['group_name']}" /></td>
</tr>
<tr>
  <td class="rowcell3">Group Title</td>
  <td class="rowcell2"><input type="text" name="group_title" class="textbox" size="40" value="{$group['group_title']}" /></td>
</tr>
<tr>
  <td class="rowcell3">Inbox Capacity</td>
  <td class="rowcell2"><input type="text" name="msg_capacity" class="textbox" size="6" value="{$group['msg_capacity']}" /></td>
</tr>
</table>
<div class="rowtitle">Display Settings</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3">Name Prefix</td>
  <td width="60%" class="rowcell2"><input type="text" name="name_prefix" class="textbox" size="60" value="{$group['name_prefix']}" /></td>
</tr>
<tr>
  <td class="rowcell3">Name Suffix</td>
  <td class="rowcell2"><input type="text" name="name_suffix" class="textbox" size="60" value="{$group['name_suffix']}" /></td>
</tr>
</table>
</div>
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">General Permissions</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3" valign="top">Can Submit</td>
  <td width="60%" class="rowcell2">{$form_elements['can_submit']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Add Comment</td>
  <td class="rowcell2">{$form_elements['can_comment']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Send Reports</td>
  <td class="rowcell2">{$form_elements['can_report']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Modify Own Submissions</td>
  <td class="rowcell2">{$form_elements['can_modify']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Use Messenger</td>
  <td class="rowcell2">{$form_elements['can_msg']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Message Other Users</td>
  <td class="rowcell2">{$form_elements['can_msg_users']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Edit Own Comments</td>
  <td class="rowcell2">{$form_elements['edit_comment']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Delete Own Comments</td>
  <td class="rowcell2">{$form_elements['delete_comment']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Can Use BBCode</td>
  <td class="rowcell2">{$form_elements['use_bbcode']}</td>
</tr>
</table>
</div>
<br />
<div class="rowfield" style="margin-left:auto; margin-right:auto">
<div class="rowtitle">Elevated Permissions</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3" valign="top">Moderator</td>
  <td width="60%" class="rowcell2">{$form_elements['moderator']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">ACP Access</td>
  <td class="rowcell2">{$form_elements['acp_access']}</td>
</tr>
</table>
<div class="rowtitle">Specific ACP Permissions</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="40%" class="rowcell3" valign="top">Mod Queue</td>
  <td width="60%" class="rowcell2">{$form_elements['acp_modq']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">User Control</td>
  <td class="rowcell2">{$form_elements['acp_users']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">News</td>
  <td class="rowcell2">{$form_elements['acp_news']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Messages</td>
  <td class="rowcell2">{$form_elements['acp_msg']}</td>
</tr>
<tr>
  <td class="rowcell3" valign="top">Root Admin</td>
  <td class="rowcell2">{$form_elements['acp_super']}</td>
</tr>
</table>
</div>
<br />
<div style="text-align:center">
  <input type="submit" class="button" value="Update Group" /> 
  <input type="submit" name="drop_item" class="button" value="DROP Group" style="background-color: #FF6169; color: white" />
</div>
<br />
</form>
HTML;
}

function group_drop ($group, $form_elements, $token) {
global $STD;
return <<<HTML
Select a new group to merge existing users into
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=12">
<input type="hidden" name="gid" value="{$group['gid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<br />
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="30%" class="title">Group to Drop</td>
  <td width="70%" class="field"><b>{$group['group_name']}</b></td>
</tr>
<tr>
  <td width="30%" class="title">Group to Merge</td>
  <td width="70%" class="field"><b>{$form_elements['group_menu']}</b></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Complete Form</td>
  <td width="70%" class="field"><input type="submit" class="button" value="Drop Group" /></td>
</tr>
</table>
</div>
</form>
HTML;
}

function ban_settings ($blacklist, $whitelist, $emaillist, $token) {
global $STD;
return <<<HTML
You can setup IP banning (blacklisting) and user exceptions (whitelisting) on this page.  Blacklisting should only be used as a last resort.
<form method="post" action="{$STD->tags['root_url']}act=ucp&amp;param=13">
<input type="hidden" name="security_token" value="{$token}" />
<br />
<div align="center">
<table border="0" cellspacing="0" cellpadding="1" width="90%">
<tr>
  <td width="30%" class="title" valign="top">Blacklist</td>
  <td width="70%" class="field">Enter one IP per line.  Use an asterisk (*) as a wildcard.</td>
</tr>
<tr>
  <td width="30%" class="title" valign="top">&nbsp;</td>
  <td width="70%" class="field"><textarea name="blacklist" class="textbox" rows="8" cols="40">{$blacklist}</textarea></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title" valign="top">Whitelist</td>
  <td width="70%" class="field">Enter one user ID per line.</td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field"><textarea name="whitelist" class="textbox" rows="8" cols="40">{$whitelist}</textarea></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title" valign="top">Email Banlist</td>
  <td width="70%" class="field">Enter one email address per line. <b>Note that this only applies to new registrations, you should still ban user accounts normally.</b></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field"><textarea name="emaillist" class="textbox" rows="8" cols="40">{$emaillist}</textarea></td>
</tr>
<tr>
  <td width="30%" class="title">&nbsp;</td>
  <td width="70%" class="field">&nbsp;</td>
</tr>
<tr>
  <td width="30%" class="title">Complete Form</td>
  <td width="70%" class="field"><input type="submit" class="button" value="Update Ban Settings" /></td>
</tr>
</table>
</div>
</form>
HTML;
}

}

?>