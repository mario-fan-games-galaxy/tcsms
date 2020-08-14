<?php

class template_login
{
    public function register($reg_url, $token)
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$reg_url}">
<input type="hidden" name="security_token" value="{$token}" />
<table class="tableheader">
<tr>
  <td class="tablecell1" width="20%"><br />
    Username<span class="hilight">*</span>
  </td>
  <td class="tablecell2" width="80%"><br />
    <input type="text" name="username" size="40" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
    Password<span class="hilight">*</span>
  </td>
  <td class="tablecell2">
    <input type="password" name="password" size="40" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
    Email<span class="hilight">*</span>
  </td>
  <td class="tablecell2">
    <input type="text" name="email" size="40" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
    Personal Image
  </td>
  <td class="tablecell2">
    <input type="text" name="image" size="40" value="http://" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
    Website
  </td>
  <td class="tablecell2">
    <input type="text" name="website" size="40" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
    Website URL
  </td>
  <td class="tablecell2">
    <input type="text" name="weburl" value="http://" size="40" class="textbox" />
  </td>
</tr>
<tr>
  <td class="tablecell1">
  </td>
  <td class="tablecell2">
    <input type="submit" name="submit" value="Register" class="button" />
  </td>
</tr>
</table>
</form>
</div>
HTML;
    }
}
