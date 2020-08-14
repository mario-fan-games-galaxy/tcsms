<?php

class template_global
{
    public function message($msg)
    {
        global $STD;
        return <<<HTML
<br />
<div class="sform" style="width:75%; margin-left:auto; margin-right:auto">
<table class="sformtable" cellspacing="1">
<tr>
  <td width="100%">$msg</td>
</tr>
</table>
</div>
HTML;
    }

    public function error($msg)
    {
        global $STD;
        return <<<HTML
    <br />
	<div class="sform" style="width:75%; margin-left:auto; margin-right:auto">
	  <div class="sformstrip">Error</div>
	  <table class="sformtable" cellspacing="0">
	    <tr>
	      <td>{$msg}
			<p align='center'><a href='javascript:history.go(-1)'>Return to previous page</a></p>
	      </td>
	    </tr>
	    <tr><td height="6" class="sformdark">
	      </td></tr>
	  </table>
	</div>
HTML;
    }

    public function offline($msg)
    {
        global $STD;
        return <<<HTML
<br />
<div class="sform" style="width:75%; margin-left:auto; margin-right:auto">
<div class="sformstrip">Site Offline</div>
<div class="sformblock">$msg</div>
<div class="sformstrip">Maintenance Login</div>
<form method="post" action="{$STD->tags['root_url']}act=login&amp;param=02">
<table class="sformtable" cellspacing="1">
<tr>
  <td class="sformleft">Username</td>
  <td class="sformright"><input type="text" name="username" size="40" class="textbox" /></td>
</tr>
<tr>
  <td class="sformleft">Password</td>
  <td class="sformrirght"><input type="password" name="password" size="40" class="textbox" /></td>
</tr>
</table>
<div class="sformstrip" style="text-align:center"><input type="submit" value="Login" class="button" /></div>
</form>
</div>
HTML;
    }

    public function popup($out)
    {
        global $STD;
        return <<<HTML
{$out}
HTML;
    }

    public function html_head()
    {
        global $STD;
        return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
  <head>
    <title>MFGG - Mario Fan Games Galaxy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="shortcut icon" href="{$STD->tags['template_path']}/favicon.ico" />
    <link rel="stylesheet" href="{$STD->tags['template_path']}/style.css" type="text/css" />
  </head>
HTML;
    }

    public function site_header()
    {
        global $STD;
        return <<<HTML
<body>
  <script type="text/javascript" src="{$STD->tags['template_path']}/global.js"></script>
  <div class="header">
  <table border="0" cellspacing="0" cellpadding="0" width="100%" style="background: url({$STD->tags['image_path']}/header_br.png);">
  <tr>
    <td>
      <img src="{$STD->tags['image_path']}/header.png" alt="Logo Image" style="display:inline; vertical-align:middle" />
    </td>
  </tr>
  </table>
  </div>
  <div>
  <table border="0" cellspacing="0" cellpadding="0" width="100%" style="background: url({$STD->tags['image_path']}/sidebar.gif); background-repeat: repeat-y;">
  <tr>
HTML;
    }

    public function site_menu($login)
    {
        global $STD;
        return <<<HTML
<td width="135" valign="top">
<div class="canvas_left">
<br />
<br />
<div class="menu">
<div class="menutitle">Main</div>
<div class="menusection">
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=main">Updates</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=staff">Staff</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=submit&amp;param=01">Submission Rules</a></div>
  <div class="menuitem"><a href="http://forums.mfgg.net/">Message Board</a></div>
  <div class="menuitem"><a href="http://wiki.mfgg.net/">Wiki</a></div>
</div>
<div class="menutitle">Content</div>
<div class="menusection">
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=1">Sprites</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=2">Games</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=4">How-Tos</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=5">Sounds</a></div>  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=6">Misc</a></div>
</div>
<div class="menutitle">Search</div>
<div class="menusection">
  <form method="get" action="{$STD->tags['root_url']}">
  <input type="hidden" name="act" value="search" />
  <input type="hidden" name="param" value="02" />
  <div class="menuitem">
    <input type="text" name="search" alt="search" size="14" class="sidetextbox" /></div>
  <div class="menuitem">
    <input type="submit" value="Go" class="sidebutton" /> 
    <a href="{$STD->tags['root_url']}act=search&amp;param=01">Advanced</a></div>
  </form>
</div>
</div>
<br />
{$login}
<br />
<div class="menu">
<div class="menutitle">Affiliates</div>
<div class="menusection" style="text-align:center">
  <div class="menuitem"><a href="http://www.koopatorivm.com/" target="_blank">
    <img src="template/images/kbanner.gif" alt="The Koopatorivm" width="88" height="31" border="0"></a></div>
  <div class="menuitem"><a href="http://metroidr.brpxqzme.net/" target="_blank">
    <img src="template/images/mfmbutton.gif" alt="Metroid Fan Mission" width="88" height="31" border="0"></a></div>
  <div class="menuitem"><a href="http://www.metroidheadquarters.com/" target="_blank">
     <img src="template/images/mhqbutton.gif" alt="Metroid Headquarters" width="88" height="31" border="0"></a></div>
  <div class="menuitem"><a href="http://www.spriters-resource.com/" target="_blank">
    <img src="template/images/tsr.gif" alt="The Spriter's Resource" width="88" height="31" border="0"></a></div>
  <div class="menuitem"><a href="http://www.mariowiki.com/Main_Page" target="_blank">
    <img src="template/images/mariowiki.png" alt="Super Mario Wiki" width="88" height="31" border="0"></a></div>
  <div class="menuitem"><a href="http://tsgk.captainn.net/" target="_blank">
    <img src="template/images/tsgk.png" alt="The ShyGuy Kingdom" width="88" height="31" border="0"></a></div>
  </div>
</div>
<br />
<div class="menu" style="background-color: #333333;  padding: 0px; border:1px solid #444444; border-bottom: 2px solid #444444;">
<div style="background-color: #111111; height: 6px;"></div>
<div class="menusection" style=" padding: 0px; background-color: #585858;">
<script type="text/javascript"><!--
google_ad_client = "pub-2961670651465400";
/* 120x600, created 4/25/08 */
google_ad_slot = "7775940828";
google_ad_width = 120;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
</div>
</div>
</td>
HTML;
    }

    public function menu_login()
    {
        global $STD;
        return <<<HTML
<form method="post" action="{$STD->tags['root_url']}act=login&amp;param=02">
<div class="menu">
<div class="menutitle">Login</div>
<div class="menusection">
  <div class="menuitem">
    <input type="text" name="username" value="username" alt="username" size="13" class="sidetextbox" /></div>
  <div class="menuitem">
    <input type="password" name="password" value="" alt="password" size="13" class="sidetextbox" /></div>
  <div class="menuitem" style="font-size:8pt">
    <a href="{$STD->tags['root_url']}act=login&amp;param=05">I lost my password</a></div>
  <div class="menuitem">
    <input type="submit" name="submit" value="Login" class="sidebutton" /></div>
</div>
<div class="menutitle">Not Registered?</div>
<div class="menusection">
  <div class="menuitem">
    <a href="{$STD->tags['root_url']}act=login&amp;param=01">
    <img src="{$STD->tags['image_path']}/signup.gif" alt="Sign Up" border="0" /></a></div>
  </div>
</div>
</form>
HTML;
    }

    public function menu_loggedin($username, $messages, $extra)
    {
        global $STD;
        return <<<HTML
<div class="menu">
<div class="menutitle">{$username}</div>
<div class="menusection">
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=user&amp;param=02">Preferences</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=msg&amp;param=01">Messages ({$messages})</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=user&amp;param=03">My Submissions</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=submit&amp;param=02">Submit File</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=login&amp;param=03">Log Out</a></div>
  {$extra}
</div>
</div>
HTML;
    }

    public function content_header($new_message)
    {
        global $STD;
        return <<<HTML
<td valign="top" align="center">
<div class="canvas_center">
<br />
{$new_message}
HTML;
    }

    public function page_header($title)
    {
        global $STD;
        return <<<HTML
<div class="header_region">
<table border="0" cellspacing="0" cellpadding="0" style="width:550px; margin-left: auto; margin-right: auto"><tr>
<td style="background:url({$STD->tags['image_path']}/pipe_bg.gif);">
  <img src="{$STD->tags['image_path']}/pipe_left.gif" style="float:left" />
</td><td style="background:url({$STD->tags['image_path']}/pipe_bg.gif);">
<img src="{$STD->tags['image_path']}/pipe_right.gif" style="display:inline; vertical-align: top" /><span class="boxpageheader">
{$title}
</span><img src="{$STD->tags['image_path']}/pipe_left.gif" style="display:inline; vertical-align: top" />
</td><td style="background:url({$STD->tags['image_path']}/pipe_bg.gif);">
  <img src="{$STD->tags['image_path']}/pipe_right.gif" style="float:right" />
</td></tr></table>
<br />
</div>
<br />
HTML;
    }

    public function page_footer()
    {
        global $STD;
        return <<<HTML
HTML;
    }

    public function content_footer()
    {
        global $STD;
        return <<<HTML
</div>
</td>
HTML;
    }

    public function site_footer()
    {
        global $STD;
        return <<<HTML
</tr>
<tr>
  <td></td>
  <td align="center">
  <br />
  <div class="canvas_center" style="border-top: 1px solid #444466;">
    All Nintendo material is &copy; Nintendo.  MFGG does not own any user-submitted content, which is &copy; the
    submitter or a third party.  All remaining material is &copy; MFGG. MFGG is a non-profit site.
    Please read the Disclaimer.
    <br /><br />
    <div style="text-align:center">Powered By: Taloncrossing SMS v0.8.5, &copy; 2008 <a href='http://www.taloncrossing.com'>Taloncrossing.com</a></div>
  </div>
  </td>
</tr>
</table>
</div>
</body>
</html>
HTML;
    }

    public function new_messages($msg)
    {
        global $STD;
        return <<<HTML
<div class="message">You have a new message: <b>$msg</b></div>
<br />
HTML;
    }

    public function admin_link()
    {
        global $STD;
        return <<<HTML
<hr>
<div class="menuitem"><a href="admin.php">Admin CP</a></div>
HTML;
    }

    // Not a true skin component
    public function wrapper($template, $out)
    {
        global $STD, $CFG;
    
        $output  = $template->html_head();
    
        if (isset($STD->offline)) {
            return $output . $template->offline($out);
        }
    
        if (isset($STD->popup_window)) {
            return $output . $template->popup($out);
        }
        
        $output .= $template->site_header();
    
        if (!$STD->user['uid']) {
            $menu = $template->menu_login();
        } elseif (!empty($STD->user['acp_access'])) {
            $menu = $template->menu_loggedin($STD->user['username'], $STD->user['new_msgs'], $template->admin_link());
        } else {
            $menu = $template->menu_loggedin($STD->user['username'], $STD->user['new_msgs'], '');
        }
    
        $new_message = $STD->global_template_ui->new_message();
    
        $output .= $template->site_menu($menu);
        $output .= $template->content_header($new_message);
    
        $output .= $out;
    
        $output .= $template->content_footer();
        $output .= $template->site_footer();
    
        return $output;
    }
}
