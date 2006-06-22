<?php

class template_adm_modq {

function message ($message) {
global $STD;
return <<<HTML
<br />
{$message}
HTML;
}

function sub_list_header ($olinks, $tab_index, $tab_url, $boxes, $type) {
global $STD;
return <<<HTML
Here you can accept or decline new submissions, or modify older ones.  Submissions that have been updated or reported will also appear at the top of the queue.
<br />
<br />
<div id="menu_1" align="center" style="display:none">
<form method="post" action="{$STD->tags['root_url']}act=modq&amp;param=05&amp;c={$type}">
<table cellspacing="0" cellpadding="2" width="90%" style="border:2px solid #FF6169">
    <tr>
 		  <td colspan="2" class="header" style="background-color: #FF6169; color:white;">
 		    <table border="0" cellspacing="0" cellpadding="0" width="100%">
 		      <tr>
 		        <td width="50%">
 		          Remove Item
 		        </td>
 		        <td width="50%" align="right">
 		          <a href="javascript:show_hide(1);show_hide(2);">
 		            <img src="{$STD->tags['image_path']}/red_close.gif" border="0" alt="[X]" title="Close Window" /></a>
 		        </td>
 		      </tr>
 		    </table>
 		  </td>
 		</tr>
	  <tr>
 		  <td class="title_fixed" valign="top" style="background-color:#FFD1CF;">
 		    Admin Comment
 		  </td>
 		  <td class="field_fixed" style="background-color:#FFD1CF;">
 		    <input type="hidden" name="rid" id="rid_d" value="" />
 		    <input type="hidden" name="omit_comment" value="0" />
 		    <input type="hidden" name="virus_check" value="1" />
  		  <textarea name="admincomment" rows="4" cols="30" class="textbox"></textarea>
  		  {$boxes['dq']}
  		</td>
 		</tr>
 		<tr>
 		  <td class="title_fixed" valign="top" style="background-color:#FFD1CF;">
 		    Complete Form
 		  </td>
 		  <td class="field_fixed" style="background-color:#FFD1CF;">
  		  <input type="submit" value="DROP Record" class="button" style="background-color: #FF6169; color: white" />
  		</td>
 		</tr>
 	</table>
 	<br />
</form>
</div>

<div id="menu_3" align="center" style="display:none">
<form method="post" action="{$STD->tags['root_url']}act=modq&amp;param=04&amp;c={$type}">
<table cellspacing="0" cellpadding="2" width="90%" style="border:2px solid #5D669A;">
    <tr>
 		  <td colspan="2" class="header">
 		    <table border="0" cellspacing="0" cellpadding="0" width="100%">
 		      <tr>
 		        <td width="50%">
 		          Accept Item
 		        </td>
 		        <td width="50%" align="right">
 		          <a href="javascript:show_hide(3);show_hide(2);">
 		            <img src="{$STD->tags['image_path']}/blue_close.gif" border="0" alt="[X]" title="Close Window" /></a>
 		        </td>
 		      </tr>
 		    </table>
 		  </td>
 		</tr>
	  <tr>
 		  <td class="title_fixed" valign="top">
 		    Admin Comment
 		  </td>
 		  <td class="field_fixed">
  		  <textarea name="admincomment" rows="4" cols="30" class="textbox" onclick="uc_box("oc1");"></textarea>
  		  <br /><input type="checkbox" name="omit_comment" id="oc1" value="1" class="checkbox" checked="checked" /> Do not include comment
  		  {$boxes['vc']}
  		  {$boxes['dq']}
  		</td>
 		</tr>
 		<tr>
 		  <td class="title_fixed" valign="top">
 		    Complete Form
 		  </td>
 		  <td class="field_fixed">
 		    <input type="hidden" name="rid" id="rid_a" value="" />
  		  <input type="submit" value="Accept Submission" class="button" />
  		</td>
 		</tr>
 	</table>
 	<br />
</form>
</div>

<div id="menu_4" align="center" style="display:none">
<form method="post" action="{$STD->tags['root_url']}act=modq&amp;param=06&amp;c={$type}">
<table cellspacing="0" cellpadding="2" width="90%" style="border:2px solid #5D669A;">
    <tr>
 		  <td colspan="2" class="header">
 		    <table border="0" cellspacing="0" cellpadding="0" width="100%">
 		      <tr>
 		        <td width="50%">
 		          Re-queue Item
 		        </td>
 		        <td width="50%" align="right">
 		          <a href="javascript:show_hide(4);show_hide(2);">
 		            <img src="{$STD->tags['image_path']}/blue_close.gif" border="0" alt="[X]" title="Close Window" /></a>
 		        </td>
 		      </tr>
 		    </table>
 		  </td>
 		</tr>
	  <tr>
 		  <td class="title_fixed" valign="top">
 		    Admin Comment
 		  </td>
 		  <td class="field_fixed">
 		    <input type="hidden" name="rid" id="rid_r" value="" />
 		    <input type="hidden" name="omit_comment" value="0" />
 		    <input type="hidden" name="virus_check" value="1" />
  		  <textarea name="admincomment" rows="4" cols="30" class="textbox"></textarea>
  		</td>
 		</tr>
 		<tr>
 		  <td class="title_fixed" valign="top">
 		    Complete Form
 		  </td>
 		  <td class="field_fixed">
  		  <input type="submit" value="Re-Queue Submission" class="button" />
  		</td>
 		</tr>
 	</table>
 	<br />
</form>
</div>

<!-- Normal Page -->

<div id="menu_2" align="center">
<div style="width: 90%">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td width="20%" valign="bottom"><div class="{$tab_index[0]}"><a href="{$tab_url}&amp;tab=0">Unmoderated</a></div></td>
  <td width="20%" valign="bottom"><div class="{$tab_index[1]}"><a href="{$tab_url}&amp;tab=1">Moderated</a></div></td>
  <td>&nbsp;</td>
</tr>
</table>
</div>
<div class="rowfield">
<table class="rowtable" cellspacing="1">
<tr>
  <td class="rowtitle" width="4%">&nbsp;</td>
  <td class="rowtitle" width="58%"><a href="{$olinks['t']['url']}">Title</a> {$olinks['t']['img']}</td>
  <td class="rowtitle" width="18%"><a href="{$olinks['u']['url']}">Author</a> {$olinks['u']['img']}</td>
  <td class="rowtitle" width="12%"><a href="{$olinks['d']['url']}">Created</a> {$olinks['d']['img']}</td>
  <td class="rowtitle" width="8%">&nbsp;</td>
</tr>
HTML;
}

function sub_list_footer ($pages) {
global $STD;
return <<<HTML
</table>
</div>
<div style="width: 90%; text-align: left">Pages: {$pages}</div>
<br />
</div>
HTML;
}

function sub_list_row ($res) {
global $STD;
return <<<HTML
<tr>
  <td class="rowcell2" style="text-align: center;">{$res['qcode']}</td>
  <td class="rowcell2">{$res['title']}</td>
  <td class="rowcell2">{$res['author']}</td>
  <td class="rowcell2">{$res['date']}</td>
  <td class="rowcell2" style="text-align: center;">{$res['action']}</td>
</tr>
HTML;
}

function edit_form_header ($res, $form) {
global $STD;
return <<<HTML
<script type='text/javascript'>
  function check_move() {
      id = get_by_id('change_to');
      
      if (id.value != '') {
          form_check = confirm("Warning: You have chosen to move this submission to a new parent category.  All extended data for this submission will be lost!\\\n\\\nDo you still wish to continue?");
      } else {
          form_check = true;
      }
    
      if (form_check == true) {
          return true;
      } else {
          return false;
      }
  }

  function check_drop() {
      form_check = confirm("Warning: Dropping this record will permanently delete them from the database.\\\n\\\nDo you still wish to continue?");
    
      if (form_check == true) {
          document.res_edit.action = '{$form['url']}&param=05&c={$res['type']}&virus_check=1';
          return true;
      } else {
          return false;
      }
  }
  
  function check_restore() {
      form_check = confirm("Warning: This will drop all user-supplied changes to this submission since the last time it was accepted, and restore the old files.\\\n\\\nDo you still wish to continue?");
      
      if (form_check == true) {
          document.res_edit.rid.value = '{$form['prerid']}';
          document.res_edit.action = '{$form['url']}&param=10&c={$res['type']}&virus_check=1';
          return true;
      } else {
          return false;
      }
  }
  
  function check_submit() {
		
      if (document.res_edit.author.value == '' && document.res_edit.author_override.value == '') {
          alert("You must associate this submission with a Creator, or use a User Override.");
          return false;
      }
      
      if (document.res_edit.title.value == '' || document.res_edit.description.value == '') {
      	  alert("You must provide a title and a description for this submission.");
          return false;
      }
      
  	  if (document.res_edit.admincomment.value == '' && document.res_edit.omit_comment.checked == false) {
  	      alert("You have not selected to omit an admin comment.  Please add one now.");
  	      return false;
  	  }
  	  
  	  return true;
  }
</script>
<form method='post' name='res_edit' action="{$form['url']}&amp;param=03" enctype='multipart/form-data' onsubmit="return check_submit()">
  <input type='hidden' name='rid' value="{$res['rid']}" />
  <input type='hidden' name='c' value="{$res['type']}" />
  <input type='hidden' name='security_token' value="{$form['security_token']}" />
	<div align='center'>
	  <div id='ghost' style="{$form['ghost_style']}">
	  &nbsp;
	  <table border='0' cellspacing='0' cellpadding='1' width='90%' style='border:2px solid #ff6169; background-color:#ffd1cf;'>
	  <tr>
	    <td width='100%' style='font-weight: bold'>This modified submission is a ghost copy of the original.  You can restore the original submission at the bottom of the page.</td>
	  </tr>
	  </table>
	  </div>
		<table border='0' cellspacing='0' cellpadding='1' width='90%'>
		
  		<tr id='menu_10' style='display:none'>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed' style='background-color:#FBFCCE'>
  		    Parent categories define what type the submission is, and also define additional type-specific fields a submission can have, such as number of reviews associated with a game.  If necessary, submissions can be moved to a different parent category, however any type-specific data will be lost in the conversion.
  		  </td>
  		</tr>
  		<tr>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed'>
  		    &nbsp;
  		  </td>
  		</tr>
HTML;
}

function edit_form_footer ($res, $form) {
global $STD;
return <<<HTML
  		<tr>
  		  <td class='title_fixed' valign='top'>
  		    Admin Comment <a href='javascript:show_hide(18);'><img src='{$STD->tags['image_path']}/info.gif' border='0' alt='?' /></a>
  		  </td>
  		  <td class='field_fixed'>
  		    <textarea name='admincomment' rows='4' cols='30' class='textbox'></textarea>
  		    <br /><input type='checkbox' name='omit_comment' value='1' class='checkbox' /> Do not inform the user of this modification
  		  </td>
  		</tr>
  		<tr id='menu_18' style='display:none'>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed' style='background-color:#FBFCCE'>
  		    Admin Comments are meant to inform users of what you're doing to change their submissions, such as changing the description or title to something more appropriate, or changing the categories or tags.  If the modification is very trivial, you can chose to not sent a comment by checking the box below, but it's strongly advised you leave comments for any significent changes.  
  		  </td>
  		</tr>
  		<tr>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed'>
  		    &nbsp;
  		  </td>
  		</tr>
  		<tr style="{$form['ghost_style']}">
	      <td class='title_fixed' valign='top'>
	        User-Supplied Update Reason <a href='javascript:show_hide(20);'><img src='{$STD->tags['image_path']}/info.gif' border='0' alt='?' /></a>
	      </td>
	      <td class='field_fixed' valign='top'>
	        <textarea name='reason' class='textbox' rows='4' cols='40' disabled='disabled' style='background-color:#EEEEEE'>{$res['update_reason']}</textarea>
	      </td>
	    </tr>
	    <tr id='menu_20' style='display:none'>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed' style='background-color:#FBFCCE'>
  		    This is the user's explaination for the update.  If the user has changed the file, this will also appear in the submission's version history.  If no valid reason for the update is given, the submission should be restored.
  		  </td>
  		</tr>
  		<tr style="{$form['ghost_style']}">
	      <td class='title_fixed' valign='top'>
	        Restore Submission <a href='javascript:show_hide(19);'><img src='{$STD->tags['image_path']}/info.gif' border='0' alt='?' /></a>
	      </td>
	      <td class='field_fixed' valign='top'>
	        <input type='submit' value='Restore' class='button' onclick='check_restore()' />
	      </td>
	    </tr>
	    <tr id='menu_19' style='display:none'>
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed' style='background-color:#FBFCCE'>
  		    If this user has made unacceptable changes to the submission, or another problem has ouccured, use this button to restore the submission to its previous state.  The user's changes will be dropped.
  		  </td>
  		</tr>
  		<tr style="{$form['ghost_style']}">
  		  <td class='title_fixed'>
  		    &nbsp;
  		  </td>
  		  <td class='field_fixed'>
  		    &nbsp;
  		  </td>
  		</tr>
  		<tr>
  		  <td class='title_fixed' valign='top'>
  		    Complete Form
  		  </td>
  		  <td class='field_fixed'>
          <input type='submit' class='button' value='Update Record' onclick='return check_move();' />
		  <input name="dq_override" value="1" type="hidden" />
          <input type='submit' class='button' value='DROP Record' style='background-color: #FF6169; color: white' onclick='return check_drop();' />
  		  </td>
  		</tr>
		</table>
	</div>
</form>
HTML;
}

function create ($url, $token, $type) {
global $STD;
return <<<HTML
Select a root type for the new submission
<br /><br />
<div align='center'>
  <form method='post' action='{$url}'>
  <input type='hidden' name='security_token' value='{$token}' />
  <table border='0' cellspacing='0' cellpadding='1' width='90%'>
    <tr>
      <td class='title_fixed'>
        Root Type
      </td>
      <td class='field_fixed'>
        {$type}
      </td>
    </tr>
    <tr>
      <td class='title_fixed'>
        Continue
      </td>
      <td class='field_fixed'>
        <input type='submit' value='Create Submission' class='button' />
      </td>
    </tr>
  </table>
  </form>
</div>
HTML;
}

}

?>