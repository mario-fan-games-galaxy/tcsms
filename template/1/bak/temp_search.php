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
        <td class="sformsubstrip" height="25" width="70%">
          Relevance: <b>{$res['relevance']}</b>
        </td>
        <td class="sformsubstrip" height="25" width="30%">
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

    public function advanced_search($type_list)
    {
        global $STD;
        return <<<HTML
<script type='text/javascript'>
<!--
function load_page_cb(newdata) {
  document.getElementById("page").innerHTML = newdata;
}

function load_page(num) {
  document.getElementById("page").innerHTML = "Loading Additional Constraints.  Please Wait.";
  x_component_search__show_form_page(num, load_page_cb);
}
-->
</script>
<form method="post" action="{$STD->tags['root_url']}act=search&amp;param=03">
<div class="sform">
<div class="sformstrip">Search Terms</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Search By Phrase</td>
  <td class="sformright"><input type="text" name="terms" size="40" />
</tr>
<tr>
  <td class="sformleft">Search By Member</td>
  <td class="sformright"><input type="text" name="member" size="40" />
</tr>
</table>
<div class="sformstrip">Search Constraints</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Search By Module</td>
  <td class="sformright">{$type_list}</td>
</tr>
</table>
<div id="page" class="sformblock" style="display:none"></div>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Do Search" /></div>
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
<div align="center">
<div class="message"><b>Search Tip:</b> $msg</div>
</div>
<br />
HTML;
    }
}
