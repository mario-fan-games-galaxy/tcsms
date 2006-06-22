<?php

class template_acp_mod_reviews {

function acp_edit_form ($res) {
global $STD;
return <<<HTML
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
    Game Associated
  </td>
  <td class='field_fixed'>
    {$res['game_title']} (ID #{$res['gid']})
    <input type="hidden" name="gid" value="{$res['gid']}" />
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Commentary
  </td>
  <td class='field_fixed'>
    <textarea name="commentary" cols="42" rows="12">{$res['commentary']}</textarea>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Pros
  </td>
  <td class='field_fixed'>
    <textarea name="pros" cols="42" rows="5">{$res['pros']}</textarea>
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Cons
  </td>
  <td class='field_fixed'>
    <textarea name="cons" rows="5" cols="42">{$res['cons']}</textarea>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Gameplay
  </td>
  <td class='field_fixed'>
    <textarea name="gameplay" rows="5" cols="42">{$res['gameplay']}</textarea><br />
  	Score: {$res['gameplay_score']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Graphics
  </td>
  <td class='field_fixed'>
    <textarea name="graphics" rows="5" cols="42">{$res['graphics']}</textarea><br />
    Score: {$res['graphics_score']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Sound
  </td>
  <td class='field_fixed'>
    <textarea name="sound" rows="5" cols="42">{$res['sound']}</textarea><br />
    Score: {$res['sound_score']}
  </td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Replay
  </td>
  <td class='field_fixed'>
    <textarea name="replay" rows="5" cols="42">{$res['replay']}</textarea><br />
    Score: {$res['replay_score']}
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
  <td class='title_fixed' valign='top'>
    Final Words
  </td>
  <td class='field_fixed'>
    <textarea rows='6' cols='38' name='description' class='textbox'>{$res['description']}</textarea><br />
    Score: {$res['score']}
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
?>