<?php

class template_mod_howtos {

function resdb_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left">
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
      <tr>
        <td width="60%" class="sformsubstrip">
          <a href="{$STD->tags['root_url']}act=resdb&param=02&c={$STD->tags['c']}&id={$res['rid']}">
          <b>{$res['title']}</b></a>
        </td>
        <td width="25%" class="sformstrip">
	      By: <b>{$res['author']}</b>
        </td>
        <td width="15%" class="sformstrip" style="text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td width="33%" class="subtext">
                <span class="vertical-align:middle">Downloads: <b>{$res['downloads']}</b></span>
              </td>
              <td width="33%" class="subtext">
                <span style="vertical-align:middle">Comments: <b>{$res['comments']}</b> </span>{$res['new_comments']}
              </td>
              <td width="33%">
                &nbsp;
              </td>
            </tr>
          </table>
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


function resdb_page ($res) {
global $STD;
return <<<HTML
<script type="text/javascript">
  <!--
  function version_history() {
    window.open('{$STD->tags['root_url']}act=resdb&param=04&rid={$res['rid']}','Complete Version History','scrollbars=yes,menubar=no,height=500,width=500,esizable=yes,toolbar=no,location=no,status=no');
  }
  -->
</script>
  <div class="sform">
  <table class="sformtable" cellspacing="0">
  <tr>
    <td align="left" height="100" valign="top">
      <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
          <td width="60%" class="sformsubstrip">
            <b class="highlight">{$res['title']}</b>
          </td>
          <td width="25%" class="sformstrip">
  	      By: <b>{$res['author']}</b>
          </td>
          <td width="15%" class="sformstrip" style="text-align:right;padding:2px">
            {$res['email_icon']} {$res['website_icon']}
          </td>
        </tr>
        <tr>
          <td valign="top" width="100%" colspan="3" height='50'>
            {$res['description']}
          </td>
        </tr>
        <tr>
          <td valign="top" width="100%" colspan="3" align="right">
            <b>Target Applications:</b> {$res['compat_icons']}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
  </div>
  <br />
  
  <table class="sformtable" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%" valign="top">
        <div class="sform">
        <div class="sformstrip">Update History</div>
        <table class="sformtable" cellspacing="0">
  	    <tr>
  	      <td colspan="2">
  	        &nbsp;
  	      </td>
  	    </tr>
          {$res['version_history']}
        </table>
        </div>
        <div style="padding-top:4px">
          <img src="{$STD->tags['image_path']}/report.gif" alt="[!]" style="display:inline; vertical-align:middle" />
          <a href="{$res['report_url']}" style="vertical-align: middle" class="outlink">Report This Submission</a>
        </div>
      </td>
      <td width="3%">
        &nbsp;
      </td>
      <td width="47%" valign="top">
        <div class="sform">
        <table class="sformtable" cellspacing="0">
          <tr>
            <td width="25" height="25" align="center"><img src="{$STD->tags['image_path']}/time.gif" alt="[O]" /></td>
            <td width="90">Created:</td>
            <td>{$res['created']}</td>
          </tr>
          <tr>
            <td width="25" height="25" align="center"><img src="{$STD->tags['image_path']}/time.gif" alt="[O]" /></td>
            <td>Updated:</td>
            <td>{$res['updated']}</td>
          </tr>
          <tr>
            <td width="25" height="25" align="center"><img src="{$STD->tags['image_path']}/disk.gif" alt="[O]" /></td>
            <td>File Size:</td>
            <td>{$res['filesize']}</td>
          </tr>
          <tr>
            <td width="25" height="25" align="center"><img src="{$STD->tags['image_path']}/gray_arrow.gif" alt="[O]" /></td>
            <td>Views:</td>
            <td>{$res['views']}</td>
          </tr>
          <tr>
            <td width="25" height="25" align="center"><img src="{$STD->tags['image_path']}/green_arrow.gif" alt="[O]" /></td>
            <td>Downloads:</td>
            <td>{$res['downloads']}</td>
          </tr>
          <tr>
            <td colspan="3" align="center">
              <span style="font-size:14pt">{$res['download_text']}</span>
            </td>
          </tr>
        </table>
        </div>
      </td>
    </tr>
  </table>
  <br />
HTML;
}

function public_row ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td width="60%" class="sformsubstrip">
          {$res['dl_icon']}
          <span style="display:inline; vertical-align:middle">
          <a href="{$STD->tags['root_url']}act=resdb&amp;param=02&amp;c={$cat}&amp;id={$res['rid']}">
          <b>{$res['title']}</b></a></span>
        </td>
        <td width="25%" class="sformstrip">
	      By: <b>{$res['author']}</b>
        </td>
        <td width="15%" class="sformstrip" style="text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom">
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
}

function manage_row ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td width="60%" class="sformsubstrip">
          {$res['page_icon']}{$res['dl_icon']}
          <span style="display:inline; vertical-align:middle">
          <a href="{$STD->tags['root_url']}act=user&amp;param=06&amp;c={$cat}&amp;rid={$res['rid']}">
          <b>{$res['title']}</b></a></span>
        </td>
        <td width="25%" class="sformstrip">
	      By: <b>{$res['author']}</b>
        </td>
        <td width="15%" class="sformstrip" style="text-align:right;padding:2px">
          {$res['email_icon']} {$res['website_icon']}
        </td>
      </tr>
      <tr>
        <td valign="top" width="100%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom">
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
}

function manage_page ($res, $token, $max_size) {
global $STD;
return <<<HTML
<div class="sform">
<form method="post" action="{$STD->tags['root_url']}act=user&amp;param=07" enctype="multipart/form-data">
<input type="hidden" name="security_token" value="{$token}" />
<input type="hidden" name="c" value="{$res['type']}" />
<input type="hidden" name="rid" value="{$res['rid']}" />
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
  <td class="sformleft">Number of Downloads</td>
  <td class="sformright">{$res['downloads']}</td>
</tr>
<tr>
  <td class="sformleft">Number of Views</td>
  <td class="sformright">{$res['views']}</td>
</tr>
</table>
<div class="sformstrip">Submission Parameters.  These values define your submission.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Title</td>
  <td class="sformright"><input type="text" name="title" size="40" value="{$res['title']}" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Additional Authors<br /><span style="font-size:8pt">(Separate names with commas)</span></td>
  <td class="sformright"><input type="text" name="author_override" size="40" value="{$res['author_override']}" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Description</td>
  <td class="sformright"><textarea name="description" rows="4" cols="40" class="textbox">{$res['description']}</textarea></td>
</tr>
</table>
<div class="sformstrip">Categorization.  Expand the lists to associate categories with this submission.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Target Application</td>
  <td class="sformright">{$res['cat1']}</td>
</tr>
</table>
<div class="sformstrip">Manage Files</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">File <a href="javascript:show_hide('m_4');">(Replace)</a></td>
  <td class="sformright"><a href="{$STD->tags['root_url']}act=resdb&amp;param=02&amp;c={$STD->tags['c']}&amp;id={$res['rid']}">[View / Download]</a></td>
</tr>
<tr id="m_4" style="display:none">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright"><input type="file" name="file" size="40" class="textbox" />
    <span class="subtext">Max Size: {$max_size['file']}</span></td>
</tr>
</table>
<div class="sformstrip">Short description of this update</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Reason</td>
  <td class="sformright"><textarea name="reason" rows="4" cols="40" class="textbox"></textarea>
</tr>
</table>
<div class="sformstrip" style="text-align: center">
  <input type="submit" value="Update Submission" class="button" />
  <input type="submit" name="rem" value="Request Removal" class="button" />
</div>
</form>
</div>
HTML;
}

function submit_form ($res, $max_size) {
global $STD;
return <<<HTML
<div class="sformstrip">Fill in information about your submission.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Target Application</td>
  <td class="sformright">{$res['cat1']}</td>
</tr>
</table>
<div class="sformstrip">Select Files to upload</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">File</td>
  <td class="sformright"><input type="file" name="file" size="40" class="textbox" />
    <span class="subtext">Max Size: {$max_size['file']} - Formats accepted: ZIP</span></td>
</tr>
</table>
<div class="sformstrip">Add a title and description</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Title</td>
  <td class="sformright"><input type="text" name="title" value="{$res['title']}" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Description</td>
  <td class="sformright"><textarea name="description" rows="4" cols="40" class="textbox">{$res['description']}</textarea></td>
</tr>
</table>
HTML;
}

}
