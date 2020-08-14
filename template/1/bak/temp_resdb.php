<?php

class template_resdb {

function filter_row ($boxes, $f_url) {
global $STD;
return <<<HTML
<div class="sform" style="overflow: auto">
<form method="post" action="{$f_url}">
<div class="sformstrip">Narrow Selection</div>
<div class="sformblock">
  <table border="0" cellspacing="0" cellpadding="2">
  {$boxes}
  </table>
</div>
<div class="sformstrip"><input type="submit" value="Refine Selection" /></div>
</div>
</form>
<br />
HTML;
}

function filter_box ($name, $box) {
global $STD;
return <<<HTML
<td><b>{$name}:</b><br />{$box}&nbsp;</td>
HTML;
}

function start_rows () {
global $STD;
return <<<HTML
<div class="sform">
<table class="sformtable" cellspacing="1">
HTML;
}

function end_rows () {
global $STD;
return <<<HTML
HTML;
}

function row_footer ($pages, $order, $order_url) {
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
HTML;
}

}
?>