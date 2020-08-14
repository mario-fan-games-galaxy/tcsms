<?php

class template_acp_mod_games
{
    public function acp_edit_form($res)
    {
        global $STD;
        return <<<HTML
<tr>
  <td class='title_fixed' valign='top'>
    Completion
  </td>
  <td class='field_fixed'>
	{$res['cat1']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Genre
  </td>
  <td class='field_fixed'>
    {$res['cat2']}
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td style='border-bottom:1px solid #666666; font-size:14pt'>&#8212;&#8212; Part 2</td>
  <td style='border-bottom:1px solid #666666' valign='bottom'><b>Base Data and Information</b></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Title
  </td>
  <td class='field_fixed'>
    <input type='text' name='title' value="{$res['title']}" size='40' class='textbox' />
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Creator <a href="javascript:show_hide('f1_h2a');show_hide('f1_h2b');show_hide('f1_h2c');">
				 <img src='./template/admin/images/info.gif' border='0' alt='[Info]' /></a>
  </td>
  <td class='field_fixed'>
    <input type='text' name='author' value="{$res['username']}" size='40' class='textbox' /> {$res['usericon']}
  </td>
</tr>
<tr id='f1_h2a' style='display:none'>
  <td class='title_fixed'>
    &nbsp;
  </td>
  <td class='field_fixed' style='background-color:#FBFCCE'>
    The creator field specifies a registered user this submission belongs to.  This name can be visibly overridden by specifying a username override.  If this submission doesn't belong to a registered user, a username override value can be used instead.  At least one of the two fields must have a value.
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Username Override
  </td>
  <td class='field_fixed'>
    <input type='text' name='author_override' value="{$res['author_override']}" size='40' class='textbox' />
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Website Override
  </td>
  <td class='field_fixed'>
    <input type='text' name='website_override' value="{$res['website_override']}" size='40' class='textbox' /> {$res['website']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Website URL Override
  </td>
  <td class='field_fixed'>
    <input type='text' name='weburl_override' value="{$res['weburl_override']}" size='40' class='textbox' /> {$res['weburl']}
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Description
  </td>
  <td class='field_fixed'>
    <textarea rows='6' cols='38' name='description' class='textbox'>{$res['description']}</textarea>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Date Submitted
  </td>
  <td class='field_fixed'>
    {$res['created']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Last Updated
  </td>
  <td class='field_fixed'>
    {$res['updated']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Total Views
  </td>
  <td class='field_fixed'>
    {$res['views']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Total Downloads
  </td>
  <td class='field_fixed'>
    {$res['downloads']}
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    File <a href="javascript:show_hide('f1_3a');show_hide('f1_3b');show_hide('f1_3c');">(Replace)</a>
  </td>
  <td class='field_fixed'>
    <a href="{$res['file']}">[View / Download]</a>
  </td>
</tr>
<tr id='f1_3a' style='display:none'>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr id='f1_3b' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    Upload File
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    <input type='file' name='file' class='textbox' size='40' /> -OR-
  </td>
</tr>
<tr id='f1_3c' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    Specify Filename
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    <input type='text' name='file_name' value='' size='40' class='textbox' />
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Preview <a href="javascript:show_hide('f1_4a');show_hide('f1_4b');show_hide('f1_4c');">(Replace)</a>
  </td>
  <td class='field_fixed'>
    {$res['preview']}
  </td>
</tr>
<tr id='f1_4a' style='display:none'>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr id='f1_4b' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    Upload File
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    <input type='file' name='preview' class='textbox' size='40' /> -OR-
  </td>
</tr>
<tr id='f1_4c' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    Specify Filename
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    <input type='text' name='preview_name' value='' size='40' class='textbox' />
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Thumbnail <a href="javascript:show_hide('f1_5a');show_hide('f1_5b');show_hide('f1_5c');show_hide('f1_5d');">(Replace)</a>
  </td>
  <td class='field_fixed'>
    {$res['thumbnail']}
  </td>
</tr>
<tr id='f1_5a' style='display:none'>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr id='f1_5b' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    Upload File
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-top: 1px solid gray;'>
    <input type='file' name='thumbnail' class='textbox' size='40' /> -OR-
  </td>
</tr>
<tr id='f1_5c' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB;'>
    Specify Filename
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB;'>
    <input type='text' name='thumbnail_name' value='' size='40' class='textbox' /> -OR-
  </td>
</tr>
<tr id='f1_5d' style='display:none'>
  <td valign='top' class='title_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    Generate From Preview
  </td>
  <td class='field_fixed' style='background-color: #BDC5EB; border-bottom: 1px solid gray;'>
    <input type='checkbox' name='thumbnail_gen' value='1' class='checkbox' />
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td style='border-bottom:1px solid #666666; font-size:14pt'>&#8212;&#8212; Part 3</td>
  <td style='border-bottom:1px solid #666666' valign='bottom'><b>Commit Changes</b></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
HTML;
    }
}
