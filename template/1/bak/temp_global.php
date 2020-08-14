<?php

class template_global
{
    public function message($msg)
    {
        global $STD;
        return <<<HTML
<br />
<div class="sform" style="width:60%">
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
	<div class="sform" style="width:60%">
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

    public function html_head()
    {
        global $STD;
        return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
  <head>
    <title>MFGG - Mario Fan Games Galaxy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <!-- <link rel="shortcut icon" href="/favicon.ico" /> -->
    <link rel="stylesheet" href="{$STD->tags['template_path']}/style.css" type="text/css" />
    <script type="text/javascript">
      <!--
      {$STD->tags['sajax']}
      
      var newUrl = '';
      
      function expand_menu_cb(newdata) {
        document.getElementById('menu').innerHTML = newdata;
      }
      
      function expand_link_cb(newdata) {
        window.location = newUrl;
      }
      
      function expand_menu(num) {
        x_component_menu__get_menu(num, expand_menu_cb);
      }
      
      function expand_link(num, url) {
        newUrl = url;
        x_component_menu__get_menu(num, expand_link_cb);
      }
      -->
    </script>
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
  <table border="0" cellspacing="0" cellpadding="4" width="100%">
  <tr>
    <td width="150" valign="top" height="61">
    <div align="center">
      <img src="{$STD->tags['image_path']}/logo2.gif" width="76" height="71" alt="Logo Image" />
    </div>
    </td>
    <td height="61">
    <div align="center">
      <img src="{$STD->tags['image_path']}/title.gif" width="500" height="70" alt="Title Image" />
    </div>
    </td>
  </tr>
  </table>
  </div>
  <br />
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
HTML;
    }

    public function site_menu($login)
    {
        global $STD;
        return <<<HTML
<td width="150" valign="top">
<div class="menu">
<div class="menutitle">Main</div>
<div class="menusection">
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=main">Updates</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=staff">Staff</a></div>
  <div class="menuitem"><a href="{$STD->tags['root_url']}act=submit&amp;param=01">Submission Rules</a></div>
  <div class="menuitem"><a href="http://forums.mfgg.net/">Message Board</a></div>
</div>
<div class="menutitle">Content</div>
<div class="menusection">
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=1">Sprites</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=2">Games</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=4">How-Tos</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=5">Sounds</a></div>
  <div class="menutiem"><a href="{$STD->tags['root_url']}act=resdb&amp;param=01&amp;c=6">Misc</a></div>
</div>
<div class="menutitle">Search</div>
<div class="menusection">
  <form method="get" action="{$STD->tags['root_url']}">
  <input type="hidden" name="act" value="search" />
  <input type="hidden" name="param" value="02" />
  <div class="menuitem">
    <input type="text" name="search" alt="search" size="18" class="sidetextbox" /></div>
  <div class="menuitem">
    <input type="submit" value="Go" class="sidebutton" /> &nbsp; 
    <!--<a href="{$STD->tags['root_url']}act=search&amp;param=01">Advanced</a>--></div>
  </form>
</div>
</div>
<br />
{$login}
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
    <input type="text" name="username" value="username" alt="username" size="18" class="sidetextbox" /></div>
  <div class="menuitem">
    <input type="password" name="password" value="" alt="password" size="18" class="sidetextbox" /></div>
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

    public function menu_loggedin($username, $messages)
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
</div>
</div>
HTML;
    }

    public function content_header($new_message)
    {
        global $STD;
        return <<<HTML
<td valign="top" align="center">
{$new_message}
HTML;
    }

    public function page_header($title)
    {
        global $STD;
        return <<<HTML
<div style="width:95%; text-align:left">
  <span class="boxheader">{$title}</span>
</div>
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
  <div style="width:95%; text-align:left; border-top: 1px solid #444466;">
    All Nintendo material is &copy; Nintendo.  MFGG does not own any user-submitted content, which is &copy; the
    submitter or another party.  All remaining material is &copy; MFGG. MFGG is a non-profit site.
    Please read the Disclaimer.
    <br /><br />
    <div style="text-align:center">Powered By: Taloncrossing SMS v0.8, &copy; 2006 Taloncrossing.com</div>
  </div>
  </td>
</tr>
</table>
</body>
</html>
HTML;
    }

    public function new_messages($msg)
    {
        global $STD;
        return <<<HTML
<div align="center">
<div class="message">You have a new message: <b>$msg</b></div>
</div>
<br />
HTML;
    }

    // Not a true skin component
    public function wrapper($template, $out)
    {
        global $STD;
    
        $output  = $template->html_head();
        $output .= $template->site_header();
    
        if (!$STD->user['uid']) {
            $menu = $template->menu_login();
        } else {
            $menu = $template->menu_loggedin($STD->user['username'], $STD->user['new_msgs']);
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
