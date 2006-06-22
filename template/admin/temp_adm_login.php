<?php

class template_adm_login {

function login_screen ($login_url, $token, $error) {
global $STD;
return <<<HTML
<form method="post" action="{$login_url}">
<div style="width:70%; margin-left:auto; margin-right:auto;">
<input type="hidden" name="security_token" value="{$token}" />
<br />
<table border="0" cellpadding="4" cellspacing="0" width="100%" style="border:2px solid #000000;">
<tr>
  <td class="header">Login</td>
</tr>
<tr>
<td class="body">
Enter your login details to continue
<div style="margin-left:20px">
<br />
<table border="0" cellspacing="0" cellpadding="1" width="100%">
  <tr>
    <td class="title_fixed">
      <label for="username">Username</label>
    </td>
    <td class="field_fixed">
      <input type="text" id="username" name="username" size="40" class="textbox" alt="Username" />
    </td>
  </tr>
  <tr>
    <td class="title_fixed">
      <label for="password">Password</label>
    </td>
    <td class="field_fixed">
      <input type="password" id="password" name="password" size="40" class="textbox" alt="Password" />
    </td>
  </tr>
</table>
</div>
<br />
<div style="text-align:center">
  <input type="submit" value="Login" class="button" />
</div>
</td>
</tr>
</table>
</div>
</form>
<br />
{$error}
HTML;
}

function error_msg ($msg) {
global $STD;
return <<<HTML
<div style="width:70%; margin-left:auto; margin-right:auto;">
<table border="0" cellpadding="4" cellspacing="0" width="100%" style="border:2px solid #000000;">
<tr>
  <td class="errheader">Error</td>
</tr>
<tr>
<td class="errbody">
<div style="margin-left:20px; padding-top:5px; padding-bottom:5px; font-weight:bold">
{$msg}
</div>
</td>
</tr>
</table>
</div>
HTML;
}

}

?>