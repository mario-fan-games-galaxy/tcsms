<?php

class template_adm_panel {

function panel_preview_header() {
global $STD;
return <<<HTML
<div align="center">
<br />
<div class="rowfield" style="padding:1px">
<div class="rowtitle" style="margin-bottom:1px">Panel Arrangement Preview</div>
<div class="rowcell2" style="padding:16px">
HTML;
}

function panel_preview_region($regions) {
global $STD;
return <<<HTML
<table class="rowtable" cellsapcing="1">
<tr>
  <td colspan="{$regions['columns']}" style="padding:8px">{$regions['U']}</td>
</tr>
<tr>
  {$regions['M']}
</tr>
<tr>
  <td colspan="{$regions['columns']}" style="padding:8px">{$regions['D']}</td>
</tr>
</table>
HTML;
}

function panel_preview_box($name) {
global $STD;
return <<<HTML
<div class="rowcell1" style="border: 1px solid black; padding-top:25px; padding-bottom:25px; width:100%; text-align:center">
<span style="">{$name}</span>
</div>
&nbsp;
HTML;
}

function panel_content_box($name, $exp_icon) {
global $STD;
return <<<HTML
<div class="rowcell1" style="border: 1px solid black; height:auto; width:100%;">
<div style="float:right">{$exp_icon}</div>
<div style="padding-top:100px; padding-bottom:100px; text-align:center">{$name}</div>
</div>
&nbsp;
HTML;
}

function panel_preview_strip($name) {
global $STD;
return <<<HTML
<div class="rowcell1" style="border: 1px solid black; padding-top:2px; padding-bottom:2px; width:100%; text-align:center">
<span style="">{$name}</span>
</div>
HTML;
}

function panel_preview_footer() {
global $STD;
return <<<HTML
</div>
</div>
</div>
HTML;
}

function panel_list_header() {
global $STD;
return <<<HTML
<div align="center">
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="60%">Panel Title</td>
  <td class="rowtitle" width="5%">&nbsp;</td>
  <td class="rowtitle" width="5%">&nbsp;</td>
  <td class="rowtitle" width="5%">&nbsp;</td>
  <td class="rowtitle" width="15%">&nbsp;</td>
  <td class="rowtitle" width="10%">&nbsp;</td>
</tr>
HTML;
}

function panel_list_row($pr) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2">{$pr['panel_name']}</td>
  <td class="rowcell2" style="text-align:center; padding:1px">{$pr['fuse_icon']}</td>
  <td class="rowcell2" style="text-align:center; padding:1px">{$pr['delete_icon']}</td>
  <td class="rowcell2" style="text-align:center; padding:1px">{$pr['hidden_icon']}</td>
  <td class="rowcell2" style="text-align:center; padding:1px">{$pr['move_icon']}</td>
  <td class="rowcell2" style="text-align:center">{$pr['edit']}</td>
</tr>
HTML;
}

function panel_list_footer() {
global $STD;
return <<<HTML
</table>
</div>
</div>
HTML;
}

function panel_man($options) {
global $STD;
return <<<HTML
<div align="center">
<br />
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" colspan="2">Options</td>
</tr>
<tr>
  <td class="rowcell2" width="50%">Expand center column to unsed columns?</td>
  <td class="rowcell2" width="50%">{$options['expand']}</td>
</tr>
<tr>
  <td class="rowcell2">Allow content to be maximized?</td>
  <td class="rowcell2">{$options['maximize']}</td>
</tr>
</table>
</div>
</div>
<br />
HTML;
}

}

?>