<?php

class template_mod_reviews {

/*function resdb_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left" height="100">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip">
          <a href="{$STD->tags['root_url']}act=resdb&param=02&c={$STD->tags['c']}&id={$res['rid']}">
          <b>{$res['title']}</b></a>
        </td>
        <td height="25" width="25%" class="sformstrip">
	      By: <b>{$res['author']}</b>
        </td>
        <td height="25" width="15%" class="sformstrip" style="text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="50" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="23">
          Downloads: <b>{$res['downloads']}</b>
        </td>
        <td valign="bottom" width="100%" colspan="2">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="50%" style="font-size:8pt">
                Added: {$res['created']}
              </td>
              <td width="50%" style="font-size:8pt">
                {$res['updated']}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
</tr>
HTML;
}*/

function resdb_page ($res) {
global $STD;
return <<<HTML
<div class="sform">
<div class="sformstrip">Review Information</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Game Reviewed</td>
  <td class="sformright">{$res['game_title']}, by {$res['game_author']}</td>
</tr>
<tr>
  <td class="sformleft">Review Author</td>
  <td class="sformright">{$res['author']}</td>
</tr>
<tr>
  <td class="sformleft">Created</td>
  <td class="sformright">{$res['created']}</td>
</tr>
</table>
</div>
<br />
<div class="sform">
<div class="sformstrip">General Commentary and Game Overview</div>
<div class="sformblock">{$res['commentary']}<br />&nbsp;</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Pros</td>
  <td class="sformright">{$res['pros']}<br />&nbsp;</td>
</tr>
<tr>
  <td class="sformleft">Cons</td>
  <td class="sformright">{$res['cons']}<br />&nbsp;</td>
</tr>
</table>
<div class="sformstrip">Impressions</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Gameplay<br />
    <div style='text-align:center'><b>{$res['gameplay_score']} / 10</b></div></td>
  <td class="sformright">{$res['gameplay']}<br />&nbsp;</td>
</tr>
<tr>
  <td class="sformleft">Graphics<br />
    <div style='text-align:center'><b>{$res['graphics_score']} / 10</b></div></td>
  <td class="sformright">{$res['graphics']}<br />&nbsp;</td>
</tr>
<tr>
  <td class="sformleft">Sound<br />
    <div style='text-align:center'><b>{$res['sound_score']} / 10</b></div></td>
  <td class="sformright">{$res['sound']}<br />&nbsp;</td>
</tr>
<tr>
  <td class="sformleft">Replay<br />
    <div style='text-align:center'><b>{$res['replay_score']} / 10</b></div></td>
  <td class="sformright">{$res['replay']}<br />&nbsp;</td>
</tr>
</table>
<div class="sformstrip">Final Words</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft"><div style='text-align:center'>{$res['score']}</div></td>
  <td class="sformright">{$res['description']}</td>
</tr>
</table>
</div>
<div class="sform" style="border:0px";
<table border="0" cellpadding="2" cellspacing="0" align="left" style="margin-top:4px">
  <tr>
    <td>
      <img src="{$STD->tags['image_path']}/report.gif" alt="[!]" /> 
    </td>
    <td>
      <a href="{$res['report_url']}">&nbsp;Report This Review</a>
    </td>
  </tr>
</table>
</div>
<br />
<br />
HTML;
}

function manage_row ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left" height="100">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip">
          <a href="{$STD->tags['root_url']}act=user&amp;param=06&amp;c={$cat}&amp;rid={$res['rid']}">
          <b>{$res['title']}</b></a>
        </td>
        <td height="25" width="25%" class="sformstrip">
	      By: <b>{$res['author']}</b>
        </td>
        <td height="25" width="15%" class="sformstrip" style="text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="50" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="25">
          Score: <b>{$res['score']} / 10</b>
        </td>
        <td valign="bottom" width="100%" colspan="2">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="50%" style="font-size:8pt">
                Added: {$res['created']}
              </td>
              <td width="50%" style="font-size:8pt">
                {$res['updated']}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
</tr>
HTML;
}

function manage_row_queued ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left;background-color:#E5E5E5" height="100">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip" style="background-color:#FCA6A6">
          <a href="{$STD->tags['root_url']}act=user&amp;param=06&amp;c={$cat}&amp;rid={$res['rid']}">
          <b>{$res['title']}</b></a> <span style='color:#F33737'>(QUEUED)</span>
        </td>
        <td height="25" width="25%" class="sformstrip" style="background-color:#FB7D7D">
	      By: <b>{$res['author']}</b>
        </td>
        <td height="25" width="15%" class="sformstrip" style="background-color:#FB7D7D;text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="50" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="25">
          Score: <b>{$res['score']} / 10</b>
        </td>
        <td valign="bottom" width="100%" colspan="2">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="50%" style="font-size:8pt">
                Added: {$res['created']}
              </td>
              <td width="50%" style="font-size:8pt">
                {$res['updated']}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
</tr>
HTML;
}

function manage_page ($res, $token) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$STD->tags['root_url']}act=user&amp;param=07" enctype="multipart/form-data">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="c" value="{$res['type']}" />
<input type="hidden" name="rid" value="{$res['rid']}" />
<input type="hidden" name="gid" value="{$res['gid']}" />
<div class="sformstrip">Information about your submission.  These values cannot be changed.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Type</td>
  <td class="sformright">{$res['type_name']}</td>
</tr>
<tr>
  <td class="sformleft">Date Submitted</td>
  <td class="sformright">{$res['created']}</td>
</tr>
<tr>
  <td class="sformleft">Last Updated</td>
  <td class="sformright">{$res['updated']}</td>
</tr>
<tr>
  <td class="sformleft">Number of Views</td>
  <td class="sformright">{$res['views']}</td>
</tr>
</table>
<div class="sformstrip">Game under review</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Title</td>
  <td class="sformright">{$res['game_title']} (ID #{$res['gid']})</td>
</tr>
<tr>
  <td class="sformleft">Author</td>
  <td class="sformright">{$res['game_author']}</td>
</tr>
</table>
<div class="sformstrip">General Commentary and Game Overview</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Commentary and Overiview</td>
  <td class="sformright"><textarea name="commentary" cols="42" rows="12">{$res['commentary']}</textarea></td>
</tr>
</table>
<div class="sformstrip">Pros and Cons</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Pros</td>
  <td class="sformright"><textarea name="pros" cols="42" rows="5">{$res['pros']}</textarea></td>
</tr>
<tr>
  <td class="sformleft">Cons</td>
  <td class="sformright"><textarea name="cons" rows="5" cols="42">{$res['cons']}</textarea></td>
</tr>
</table>
<div class="sformstrip">Final Impressions and Scoring</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Gameplay</td>
  <td class="sformright"><textarea name="gameplay" rows="5" cols="42">{$res['gameplay']}</textarea><br />
  	Score: {$res['gameplay_score']}</td>
</tr>
<tr>
  <td class="sformleft">Graphics</td>
  <td class="sformright"><textarea name="graphics" rows="5" cols="42">{$res['graphics']}</textarea><br />
    Score: {$res['graphics_score']}</td>
</tr>
<tr>
  <td class="sformleft">Sound</td>
  <td class="sformright"><textarea name="sound" rows="5" cols="42">{$res['sound']}</textarea><br />
    Score: {$res['sound_score']}</td>
</tr>
<tr>
  <td class="sformleft">Replay</td>
  <td class="sformright"><textarea name="replay" rows="5" cols="42">{$res['replay']}</textarea><br />
    Score: {$res['replay_score']}</td>
</tr>
</table>
<div class="sformstrip">Final Words and Overall Score</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Final Words (be concise)</td>
  <td class="sformright"><textarea name="description" rows="3" cols="42">{$res['description']}</textarea><br />
    Score: {$res['score']}</td>
</tr>
</table>
<div class="sformstrip">Short description of this update</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Reason</td>
  <td class="sformright"><textarea name="reason" rows="4" cols="40"></textarea>
</tr>
</table>
<div class="sformstrip" style="text-align: center">
  <input type="submit" value="Update Submission" />
  <input type="submit" name="rem" value="Request Removal" />
</div>
</form>
</div>
HTML;
}

function submit_form ($res) {
global $STD;
return <<<HTML
<div class="sformstrip">Game under review</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Title</td>
  <td class="sformright">{$res['game_title']} (ID #{$res['gid']})
    <input type="hidden" name="gid" value="{$res['gid']}" /></td>
</tr>
<tr>
  <td class="sformleft">Author</td>
  <td class="sformright">{$res['game_author']}</td>
</tr>
</table>
<div class="sformstrip">General Commentary and Game Overview</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Commentary and Overiview</td>
  <td class="sformright"><textarea name="commentary" cols="42" rows="12"></textarea></td>
</tr>
</table>
<div class="sformstrip">Pros and Cons</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Pros</td>
  <td class="sformright"><textarea name="pros" cols="42" rows="5"></textarea></td>
</tr>
<tr>
  <td class="sformleft">Cons</td>
  <td class="sformright"><textarea name="cons" rows="5" cols="42"></textarea></td>
</tr>
</table>
<div class="sformstrip">Final Impressions and Scoring</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Gameplay</td>
  <td class="sformright"><textarea name="gameplay" rows="5" cols="42"></textarea><br />
  	Score: {$res['gameplay_score']}</td>
</tr>
<tr>
  <td class="sformleft">Graphics</td>
  <td class="sformright"><textarea name="graphics" rows="5" cols="42"></textarea><br />
    Score: {$res['graphics_score']}</td>
</tr>
<tr>
  <td class="sformleft">Sound</td>
  <td class="sformright"><textarea name="sound" rows="5" cols="42"></textarea><br />
    Score: {$res['sound_score']}</td>
</tr>
<tr>
  <td class="sformleft">Replay</td>
  <td class="sformright"><textarea name="replay" rows="5" cols="42"></textarea><br />
    Score: {$res['replay_score']}</td>
</tr>
</table>
<div class="sformstrip">Final Words and Overall Score</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Final Words (be concise)</td>
  <td class="sformright"><textarea name="description" rows="3" cols="42"></textarea><br />
    Score: {$res['score']}</td>
</tr>
</table>
HTML;
}

function news_update_block_header ($name) {
global $STD;
return <<<HTML
<div class='sformstrip'>Games</div>
<table class='sformtable' cellspacing='1'>
HTML;
}

function news_update_block_footer () {
global $STD;
return <<<HTML
</table>
HTML;
}

function news_update_block_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td width='100' height='100' rowspan='3'>{$res['thumbnail']}</td>
  <td class='sformleftw' colspan='2' height='20'><a href='{$res['url']}'><b>{$res['title']}</b></a></td>
</tr>
<tr>
  <td class='sformleftw' height='20'>[{$res['type']}]</td>
  <td class='sformleftw' width='30%'>By {$res['username']}</td>
</tr>
<tr>
  <td class='sformleftw' colspan='2' valign='top'>{$res['description']}</td>
</tr>
HTML;
}

}