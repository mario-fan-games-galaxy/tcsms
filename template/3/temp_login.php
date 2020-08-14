<?php

class template_login
{
    public function register($reg_url, $token)
    {
        global $STD;
        return <<<HTML
<div class="sform">
<form method="post" action="{$reg_url}">
<input type="hidden" name="security_token" value="{$token}" />
<div class="sformstrip">Welcome to registration</div>
<div class="sformblock">Registering an account will allow you to submit files or comment on other user's work, among
  other things.  Fields marked with an asterisk (<span class="highlight">*</span>) are required.
  All other fields are optional.  Remember to visit your <span class="highlight">Preferences</span> page after
  registering to customize your browsing preferences.
</div>
<div class="sformstrip">Fill out the form below to register a new account</div>
<table class="sformtable">
<tr>
  <td class="sformleft">Username<span class="highlight">*</span></td>
  <td class="sformright"><input type="text" name="username" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Password<span class="highlight">*</span></td>
  <td class="sformright"><input type="password" name="password" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Email<span class="highlight">*</span></td>
  <td class="sformright"><input type="text" name="email" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Personal Image</td>
  <td class="sformright"><input type="text" name="image" size="40" value="http://" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Website Name</td>
  <td class="sformright"><input type="text" name="website" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Website Address</td>
  <td class="sformright"><input type="text" name="weburl" value="http://" size="40" class="textbox" /></td>
</tr>
</table>
<div class="sformstrip">Security Code</div>
<table class="sformtable">
<tr>
  <td class="sformleft">Copy the letters that appear in the image into the textbox.</td>
  <td class="sformright">
    <table border="0" cellpadding="0" cellspacing="0">
      <tr><td align="center"><img src="{$STD->tags['root_url']}act=main&param=12&dd={$token}" /></td></tr>
      <tr><td align="center"><input type="text" name="regcode" size="6" class="textbox" style="margin-top: 3px" /></td></tr>
    </table>
  </td>
</tr>
</table>
<div class="sformstrip" style="text-align:center">
  <input type="submit" name="submit" value="Register" class="button" />
</div>
</form>
</div>
HTML;
    }

    public function lost_password($reg_url, $token)
    {
        global $STD;
        return <<<HTML
<div class="sform">
<form method="post" action="{$reg_url}">
<input type="hidden" name="security_token" value="{$token}" />
<div class="sformstrip">Recover from a lost password</div>
<div class="sformblock">Use this form to change your password if you've forgotton it.  A link to a page where you can
  choose a new password will be emailed to the address on your account.
</div>
<div class="sformstrip">Fill out the form below</div>
<table class="sformtable">
<tr>
  <td class="sformleft">Your username</td>
  <td class="sformright"><input type="text" name="username" size="40" class="textbox" /></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Submit" class="button" /></div>
</div>
</form>
</div>
HTML;
    }

    public function change_password($reg_url, $token, $cookie)
    {
        global $STD;
        return <<<HTML
<div class="sform">
<form method="post" action="{$reg_url}">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="cookie" value="{$cookie}" />
<div class="sformstrip">Change your password</div>
<table class="sformtable">
<tr>
  <td class="sformleft">Enter your username</td>
  <td class="sformright"><input type="text" name="username" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Enter new password</td>
  <td class="sformright"><input type="password" name="pass1" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Retype new password</td>
  <td class="sformright"><input type="password" name="pass2" size="40" class="textbox" /></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" class="button" value="Submit" /></div>
</div>
HTML;
    }
}
