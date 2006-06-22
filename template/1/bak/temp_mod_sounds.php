<?php

class template_mod_sounds {

function resdb_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left" height="75">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip" colspan="2">
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
        <td rowspan="2" width="3%" style="padding:5px">
          {$res['type1']}
        </td>
        <td valign="top" width="97%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="23">
          Downloads: <b>{$res['downloads']}</b>
        </td>
        <td valign="bottom" width="100%" colspan="3">
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
  <div class="sform">
  <table class="sformtable" cellspacing="0">
  <tr>
    <td align="left" height="100" valign="top">
      <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
          <td height="25" width="60%" class="sformsubstrip">
            <b class="highlight">{$res['title']}</b>
          </td>
          <td height="25" width="25%" class="sformstrip">
  	      By: <b>{$res['author']}</b>
          </td>
          <td height="25" width="15%" class="sformstrip" style="text-align:right;padding:2px">
            {$res['email_icon']} {$res['website_icon']}
          </td>
        </tr>
        <tr>
          <td valign="top" width="100%" colspan="3" height='50'>
            {$res['description']}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
  </div>
  <br />
  <table border="0" cellspacing="0" cellpadding="0" width="95%">
    <tr>
      <td width="51%" valign="top">
        <div class="sform" style="width:100%">
        <div class="sformstrip">Update History</div>
        <table class="sformtable" cellspacing="0" style="width:100%">
  	    <tr>
  	      <td colspan="2">
  	        &nbsp;
  	      </td>
  	    </tr>
          {$res['version_history']}
        </table>
        </div>
        <table border="0" cellpadding="2" cellspacing="0" align="left" style="margin-top:4px">
          <tr>
            <td>
	          <img src="{$STD->tags['image_path']}/report.gif" alt="[!]" /> 
	        </td>
	        <td>
	          <a href="{$res['report_url']}">&nbsp;Report This Submission</a>
	        </td>
	      </tr>
        </table>
      </td>
      <td width="3%">
        &nbsp;
      </td>
      <td width="46%" valign="top">
        <div class="sform" style="width:100%">
        <table class="sformtable" cellspacing="0" cellpadding="2" style="width:100%">
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

function manage_row ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left" height="75">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip" colspan="2">
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
        <td rowspan="2" width="3%" style="padding:5px">
          {$res['type1']}
        </td>
        <td valign="top" width="97%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="25">
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

function manage_row_queued ($res, $cat) {
global $STD;
return <<<HTML
<tr>
  <td class="sformlowline" style="padding:0px;text-align:left;background-color:#E5E5E5" height="75">
    <table border="0" cellpadding="2" cellspacing="0" width="100%" style="height: 100%">
      <tr>
        <td height="25" width="60%" class="sformsubstrip" style="background-color:#FCA6A6" colspan="2">
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
        <td rowspan="2" width="3%" style="padding:5px">
          {$res['type1']}
        </td>
        <td valign="top" width="97%" height="25" colspan="3">
           {$res['description']}
        </td>
      </tr>
      <tr>
        <td valign="bottom" height="25">
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

function manage_page ($res, $token) {
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
  <td class="sformright"><input type="text" name="title" size="40" value="{$res['title']}" /></td>
</tr>
<tr>
  <td class="sformleft">Additional Authors<br /><span style="font-size:8pt">(Separate names with commas)</span></td>
  <td class="sformright"><input type="text" name="author_override" size="40" value="{$res['author_override']}" /></td>
</tr>
<tr>
  <td class="sformleft">Description</td>
  <td class="sformright"><textarea name="description" rows="4" cols="40">{$res['description']}</textarea></td>
</tr>
</table>
<div class="sformstrip">Categorization.  Expand the lists to associate categories with this submission.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Sound Format</td>
  <td class="sformright">{$res['cat1']}</td>
</tr>
<tr>
  <td class="sformleft">Contents</td>
  <td class="sformright"><a href="javascript:show_hide('m_1');">(Expand / Collapse)</a></td>
</tr>
<tr id="m_1" style="">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright">{$res['cat2']}</td>
</tr>
<tr>
  <td class="sformleft">Associated Games</td>
  <td class="sformright"><a href="javascript:show_hide('m_2');">(Expand / Collapse)</a></td>
</tr>
<tr id="m_2" style="display:none">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright">{$res['cat3']}</td>
</tr>
</table>
<div class="sformstrip">Manage Files</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">File <a href="javascript:show_hide('m_4');">(Replace)</a></td>
  <td class="sformright"><a href="{$STD->tags['root_url']}act=resdb&amp;param=02&amp;c={$STD->template['c']}&amp;id={$res['rid']}">[View / Download]</a></td>
</tr>
<tr id="m_4" style="display:none">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright"><input type="file" name="file" size="40" /></td>
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
<div class="sformstrip">Fill in information about your submission.</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Sound Format</td>
  <td class="sformright">{$res['cat1']}</td>
</tr>
<tr>
  <td class="sformleft">Contents</td>
  <td class="sformright"><a href="javascript:show_hide('m_1');">(Expand / Collapse)</a></td>
</tr>
<tr id="m_1" style="">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright">{$res['cat2']}</td>
</tr>
<tr>
  <td class="sformleft">Associated Games</td>
  <td class="sformright"><a href="javascript:show_hide('m_2');">(Expand / Collapse)</a></td>
</tr>
<tr id="m_2" style="display:none">
  <td class="sformleft">&nbsp;</td>
  <td class="sformright">{$res['cat3']}</td>
</tr>
</table>
<div class="sformstrip">Select Files to upload</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">File</td>
  <td class="sformright"><input type="file" name="file" size="40" /></td>
</tr>
</table>
<div class="sformstrip">Add a title and description</div>
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Title</td>
  <td class="sformright"><input type="text" name="title" value="{$res['title']}" size="40" /></td>
</tr>
<tr>
  <td class="sformleft">Description</td>
  <td class="sformright"><textarea name="description" rows="4" cols="40">{$res['description']}</textarea></td>
</tr>
</table>
HTML;
}

}