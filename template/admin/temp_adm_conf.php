<?php

class template_adm_conf
{
    public function filter_group_header()
    {
        global $STD;
        return <<<HTML
<div align="center">
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="45%">Group</td>
  <td class="rowtitle" width="45%">Keyword</td>
  <td class="rowtitle" width="10%">&nbsp;</td>
</tr>
HTML;
    }

    public function filter_group_row($fg)
    {
        global $STD;
        return <<<HTML
<tr>
  <td class="rowcell2">{$fg['name']}</td>
  <td class="rowcell2">{$fg['keyword']}</td>
  <td class="rowcell2" style="text-align:center">[Delete]</td>
</tr>
HTML;
    }

    public function filter_group_footer()
    {
        global $STD;
        return <<<HTML
</table>
</div>
</div>
<br />
HTML;
    }

    public function filter_group_detail($fg, $token)
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=conf&amp;param=03">
<input type="hidden" name="gid" value="{$fg['gid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<br />
<div class="rowfield">
<div class="rowtitle">Group Detail</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td width="30%" class="rowcell3">Group Name</td>
  <td width="70%" class="rowcell2"><input type="text" name="name" size="40" value="{$fg['name']}" /></td>
</tr>
<tr>
  <td class="rowcell3">Group Keyword</td>
  <td class="rowcell2"><input type="text" name="keyword" size="40" value="{$fg['keyword']}" /><br />
    <span style="font-size:8pt">Change this value only if you know what you're doing</span></td>
</tr>
</table>
<div class="rowstrip" style="text-align:center"><input type="submit" value="Update Group Filter" /></div>
</div>
<br />
</div>
</form>
HTML;
    }

    public function filter_list_header($fg, $token)
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=conf&amp;param=05">
<input type="hidden" name="gid" value="{$fg['gid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<div class="rowfield">
<div class="rowtitle">Filter Entries</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="40%">Filter Name</td>
  <td class="rowtitle" width="20%">Short Name</td>
  <td class="rowtitle" width="30%">Search Keys</td>
  <td class="rowtitle" width="10%">&nbsp;</td>
</tr>
HTML;
    }

    public function filter_list_footer()
    {
        global $STD;
        return <<<HTML
</table>
<div class="rowstrip" style="text-align:center"><input type="submit" value="Update Filter Entries" /></div>
</div>
</div>
</form>
<br />
HTML;
    }

    public function filter_list_row($fl)
    {
        global $STD;
        return <<<HTML
<tr>
  <td class="rowcell2"><input type="text" name="name[{$fl['fid']}]" value="{$fl['name']}" size="30" /></td>
  <td class="rowcell2"><input type="text" name="short_name[{$fl['fid']}]" value="{$fl['short_name']}" size="14" /></td>
  <td class="rowcell2"><input type="text" name="keywords[{$fl['fid']}]" value="{$fl['search_tags']}" size="20" /></td>
  <td class="rowcell2" style="text-align:center">
    <a href="{$STD->tags['root_url']}act=conf&amp;param=06&amp;fid={$fl['fid']}">[Delete]</a></td>
</tr>
HTML;
    }

    public function filter_list_add($fg, $token)
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=conf&amp;param=04">
<input type="hidden" name="gid" value="{$fg['gid']}" />
<input type="hidden" name="security_token" value="{$token}" />
<div align="center">
<div class="rowfield">
<div class="rowtitle">Add New Entry</div>
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowcell3" width="30%">Full Name</td>
  <td class="rowcell2" width="70%"><input type="text" name="name" size="40" /></td>
</tr>
<tr>
  <td class="rowcell3">Short Name</td>
  <td class="rowcell2"><input type="text" name="short_name" size="40" /></td>
</tr>
<tr>
  <td class="rowcell3">Search Keywords</td>
  <td class="rowcell2"><input type="text" name="keywords" size="40" /></td>
</tr>
</table>
<div class="rowstrip" style="text-align:center"><input type="submit" value="Add Filter Entry" /></div>
</div>
</div>
</form>
<br />
HTML;
    }
}
