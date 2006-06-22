<?php

class template_user {

function userpage ($user) {
global $STD;
return <<<HTML
<div class="sform">
&nbsp;
<table class="sformtable" cellspacing="1">
<tr>
  <td valign="top" align="center" style="width:50%">
    <div class="sform" style="width:90%">
    <div class="sformstrip" style="text-align:center"><span class="highlight">Contact</span></div>
    <table class="sformtable" cellspacing="1">
      <tr>
        <td class="sformleft">PM</td>
        <td class="sformright">
          [<a href="{$STD->tags['root_url']}act=msg&amp;param=05&amp;uid={$user['uid']}">Send Message</a>]</td>
      </tr>
      <tr>
        <td class="sformleft">Email</td>
        <td class="sformirght">{$user['email']}</td>
      </tr>
      <tr>
        <td class="sformleft">AIM Name</td>
        <td class="sformright">{$user['aim']}</td>
      </tr>
      <tr>
        <td class="sformleft">ICQ ID</td>
        <td class="sformright">{$user['icq']}</td>
      </tr>
      <tr>
        <td class="sformleft">MSN ID</td>
        <td class="sformright">{$user['msn']}</td>
      </tr>
      <tr>
        <td class="sformleft">Yahoo Name</td>
        <td class="sformright">{$user['yim']}</td>
      </tr>
    </table>
    </div>
    &nbsp;
  </td>
  <td valign="top" align="center">
    <div class="sform" style="width:90%">
    <div class="sformstrip" style="text-align:center"><span class="highlight">Profile</span></div>
    <table class="sformtable" cellspacing="1">
      <tr>
        <td class="sformleft">Member No.</td>
        <td class="sformright"><b>{$user['uid']}</b></td>
      </tr>
      <tr>
        <td class="sformleft">Join Date</td>
        <td class="sformright">{$user['join_date']}</td>
      </tr>
      <tr>
        <td class="sformleft">Website</td>
        <td class="sformright">{$user['website']}</td>
      </tr>
      <tr>
        <td class="sformleft">Submissions</td>
        <td class="sformright"><b>{$user['submissions']}</b></td>
      </tr>
      <tr>
        <td class="sformleft">Reviews</td>
        <td class="sformright"><b>{$user['reviews']}</b></td>
      </tr>
      <tr>
        <td class="sformleft">Comments</td>
        <td class="sformright"><b>{$user['comments']}</b></td>
      </tr>
    </table>
    </div>
    &nbsp;
  </td>
</tr>
</table>
</div>
HTML;
}

function prefs_page ($user, $form_elements, $token) {
global $STD;
return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=user&amp;param=04">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="uid" value="{$user['uid']}" />
<div class="sform">
<div class="sformstrip">Account Information</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Username</td>
  <td class="sformright"><b>{$user['username']}</b></td>
</tr>
<tr>
  <td class="sformleft">User ID</td>
  <td class="sformright"><b>{$user['uid']}</b></td>
</tr>
</table>
<div class="sformstrip">Profile Information</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Email Address</td>
  <td class="sformright"><input type="text" size="40" name="email" value="{$user['email']}" /></td>
</tr>
<tr>
  <td class="sformleft">Website</td>
  <td class="sformright"><input type="text" size="40" name="website" value="{$user['website']}" /></td>
</tr>
<tr>
  <td class="sformleft">Website URL</td>
  <td class="sformright"><input type="text" size="40" name="weburl" value="{$user['weburl']}" /></td>
</tr>
<tr>
  <td class="sformleft">Personal Icon</td>
  <td class="sformright"><input type="text" size="40" name="icon" value="{$user['icon']}" /></td>
</tr>
<tr>
  <td class="sformleft">Icon Dimensions<br /><span style='font-size:8pt'>Max Dimensions: {$form_elements['max_dims']}</td>
  <td class="sformright"><input type="text" size="4" name="dimw" value="{$user['icon_dimw']}" /> x <input type="text" size="4" name="dimh" value="{$user['icon_dimh']}" /></td>
</tr>
<tr>
  <td class="sformleft">Show Email</td>
  <td class="sformright">{$form_elements['show_email']}</td>
</tr>
</table>
<div class="sformstrip">Instant Messenger Contact Information</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">AIM ID</td>
  <td class="sformright"><input type="text" size="40" name="aim" value="{$user['aim']}" /></td>
</tr>
<tr>
  <td class="sformleft">ICQ Number</td>
  <td class="sformright"><input type="text" size="40" name="icq" value="{$user['icq']}" /></td>
</tr>
<tr>
  <td class="sformleft">MSN ID</td>
  <td class="sformright"><input type="text" size="40" name="msn" value="{$user['msn']}" /></td>
</tr>
<tr>
  <td class="sformleft">YIM ID</td>
  <td class="sformright"><input type="text" size="40" name="yim" value="{$user['yim']}" /></td>
</tr>
</table>
<div class="sformstrip">Time Settings</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Timezone</td>
  <td class="sformright">{$form_elements['timezone']}</td>
</tr>
<tr>
  <td class="sformleft">Daylight Savings</td>
  <td class="sformright">{$form_elements['dst']} Check box if daylight savings is in effect</td>
</tr>
<tr>
  <td class="sformleft">Current Time</td>
  <td class="sformright"><span class="highlight">{$form_elements['time']}</span></td>
</tr>
</table>
<div class="sformstrip">Browsing Defaults</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Default Order</td>
  <td class="sformright">{$form_elements['order_by']} {$form_elements['order']}</td>
</tr>
<tr>
  <td class="sformleft">Skin</td>
  <td class="sformright">{$form_elements['skin']}</td>
</tr>
<tr>
  <td class="sformleft">Items Per Page</td>
  <td class="sformright">{$form_elements['items']}</td>
</tr>
</table>
<div class="sformstrip">Change Password. &nbsp;Leave blank to keep existing password.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Old Password</td>
  <td class="sformright"><input type="password" size="40" name="opass" /></td>
</tr>
<tr>
  <td class="sformleft">New Password</td>
  <td class="sformright"><input type="password" size="40" name="npass1" /></td>
</tr>
<tr>
  <td class="sformleft">Retype Password</td>
  <td class="sformright"><input type="password" size="40" name="npass2" /></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Submit Changes" class="button" /></div>
</div>
</form>
HTML;
}

function manage_type_row ($select) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$STD->tags['root_url']}act=user&param=03">
<div class="sformstrip">
<input type="submit" value="View Type" class="button" /> {$select}
</div>
</form>
HTML;
}

function manage_start_rows () {
global $STD;
return <<<HTML
<table class="sformtable" cellspacing="1">
HTML;
}

function manage_end_rows ($pages, $order, $order_url) {
global $STD;
return <<<HTML
<tr>
  <td class="sformtitle" colspan="2">
  <form method="post" action="{$order_url}">
  <input type="submit" name="reorder" value="Re-Order" class="button" />
  {$order}
  </form>
</td></tr>
</table>
<div class="sformstrip">Pages: {$pages}</div>
</div>
HTML;
}

function request_remove ($rid, $submission, $form_url, $reason) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$form_url}">
<input type="hidden" name="rid" value="{$rid}" />
<div class="sformstrip">Request Submission Removal</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Submission</td>
  <td class="sformright">{$submission}</td>
</tr>
<tr>
  <td class="sformleft">Reason</td>
  <td class="sformright"><textarea name="reason" cols="40" rows="6">{$reason}</textarea></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Submit Request" /></div>
</div>
HTML;
}

}