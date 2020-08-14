<?php

class template_search
{
    public function simple_results_header()
    {
        global $STD;
        return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="0" cellpadding="0">
HTML;
    }

    public function simple_results_footer($pages)
    {
        global $STD;
        return <<<HTML
</table>
<div class="sformstrip">Pages: {$pages}</div>
</div>
HTML;
    }

    public function simple_no_results()
    {
        global $STD;
        return <<<HTML
<tr><td>
<div class="sformblock" style="text-align:center; margin-bottom:1px; padding:10px">
No results found for this search string</div>
</td></tr>
HTML;
    }

    public function simple_results_row($res)
    {
        global $STD;
        return <<<HTML
<tr>
  <td style="border-bottom:1px solid gray" align="left">
    <div class="sformstrip"><span class="highlight">{$res['full_name']}</span> <b>-></b> 
          <a href="{$STD->tags['root_url']}act=resdb&param=02&c={$res['type']}&id={$res['rid']}">
          <b>{$res['title']}</b></a></div>
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td class="sformsubstrip"width="70%">
          Relevance: <b>{$res['relevance']}</b>
        </td>
        <td class="sformsubstrip" width="30%">
	      By: <b>{$res['author']}</b>
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="50" colspan="3">
           {$res['description']}
        </td>
      </tr>
    </table>
  </td>
</tr>
HTML;
    }

    public function advanced_search($form_fields)
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=search&amp;param=03">
<div class="sform">
<div class="sformstrip">Search Terms</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Search By Phrase</td>
  <td class="sformright"><input type="text" name="search" size="40" class="textbox" />
</tr>
<tr>
  <td class="sformleft">Search By Member</td>
  <td class="sformright"><input type="text" name="m" size="40" class="textbox" /><br />
    <input type="checkbox" name="me" class="checkbox" value="1" /> Match Exact Name
</tr>
</table>
<div class="sformstrip">Search Constraints</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Search By Module</td>
  <td class="sformright">{$form_fields['type_list']}<br />
    <input type="checkbox" name="sh" class="checkbox" value="1" /> Show results for hidden (*) modules</td>
</tr>
<tr>
  <td class="sformleft">Search By Submit Date</td>
  <td class="sformright">{$form_fields['date']} {$form_fields['date_dir']}</td>
</tr>
</table>
<div id="page" class="sformblock" style="display:none"></div>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Do Search" class="button" /></div>
</div>
</form>
HTML;
    }

    public function constraint_block($rows)
    {
        global $STD;
        return <<<HTML
<table border="0" cellspacing="0" cellpadding="2" width="100%">
{$rows}
</table>
HTML;
    }

    public function constraint_row($name, $select)
    {
        global $STD;
        return <<<HTML
<tr>
  <td width="35%">&nbsp; &nbsp; {$name}</td>
  <td>{$select}</td>
</tr>
HTML;
    }

    public function search_tip($msg)
    {
        global $STD;
        return <<<HTML
<div class="message"><b>Search Tip:</b> $msg</div>
<br />
HTML;
    }
}
