<?php

class template_resdb
{
    public function filter_row($boxes, $f_url)
    {
        global $STD;
        return <<<HTML
<div class="sform">
<form method="post" action="{$f_url}">
<div class="sformstrip">Narrow Selection</div>
<div class="sformblock">
  {$boxes}
</div>
<div class="sformstrip"><input type="submit" value="Refine Selection" class="button" /></div>
</div>
</form>
<br />
HTML;
    }

    public function filter_box($name, $box)
    {
        global $STD;
        return <<<HTML
<table border="0" style="display:inline"><tr><td><b>{$name}:</b><br />{$box}</td></tr></table>
HTML;
    }

    public function start_rows()
    {
        global $STD;
        return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="1">
HTML;
    }

    public function end_rows()
    {
        global $STD;
        return <<<HTML
HTML;
    }

    public function row_footer($pages, $order, $order_url)
    {
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
<div class="sformstrip">
    Pages: {$pages}
</div>
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
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
HTML;
    }

    public function version_history($vh, $title)
    {
        global $STD;
        return <<<HTML
<div class="sform">
<div class="sformstrip">Complete Update History: {$title}</div>
<table class="sformtable" cellspacing="1">
  {$vh}
</table>
</div>
HTML;
    }

    public function version_row($date, $desc)
    {
        global $STD;
        return <<<HTML
<tr>
  <td class="sformleftw" style="width:20%; font-weight:bold" valign="top">{$date}</td>
  <td class="sformright">{$desc}</td>
</tr>
HTML;
    }

    public function version_empty()
    {
        global $STD;
        return <<<HTML
<tr>
  <td class="sformleftw" colspan="2" style="text-align:center">No History</td>
</tr>
HTML;
    }
}
