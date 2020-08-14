<?php

class template_adm_main
{
    public function main_page($notepad, $url, $data, $delurl, $uid)
    {
        global $STD;
        $ret = <<<HTML
From the ACP you'll be able to manage users and submissions, update the front page, respond to messages, and control how the site operates.  You should always log out when you finish.
<br /><br />
<div align="center">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td width="50%">
    <form method="post" action="{$url}" name="notepadform">
		<b>Staff Discussion</b> <input type="submit" value="Submit" class="button" tabindex="2" /><br />
		<textarea id="notepad" name="notepad" rows="2" cols="100" style="border:1px solid black; background-color:#FBFCCE;" tabindex="1"></textarea><br />
    </form>
  </td>
  <td>
    &nbsp;
  </td>
</tr>
</table><br /><div class="rowfield" style="width:100%">
<table class="rowtable" cellspacing="1" width="100%">
HTML;
        foreach ($data as $dat) {
            // rowcell2
            $ret .= "<tr>
		<td width=\"12%\" class=\"rowtitle\" align=\"right\" valign=\"top\"><a href=\"{$dat['uidurl']}\">{$dat['name']}</a></td>
		<td class=\"rowcell2 canquote\" onClick=\"quote('{$dat['name']}', {$dat['id']});\">{$dat['message']}</td>
		<td width=\"23%\" class=\"rowtitle\" valign=\"top\">{$dat['date']}<div style=\"display:none\" id=\"msg{$dat['id']}\">{$dat['raw']}</div>";
            if ($dat['uid'] == $uid) {
                $ret .= " <a title=\"Delete\" href=\"{$delurl}&id={$dat['id']}\" style=\"color:maroon\" onClick=\"if(!confirm('Are you sure?'))return false;\">X</a>";
            }
            $ret .= "</td></tr>";
        }
        $ret .= <<<HTML
</table></div>
</div>
HTML;
        return $ret;
    }
}
/*CREATE TABLE `tcsms`.`tsms_chat` (
`id` INT UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT PRIMARY KEY ,
`uid` INT UNSIGNED NOT NULL ,
`date` INT NOT NULL ,
`message` TEXT NOT NULL
) ENGINE = MYISAM ;*/
