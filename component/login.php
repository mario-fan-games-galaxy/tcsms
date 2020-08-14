<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/login.php --
// handles side display of login and login pages
//------------------------------------------------------------------

$component = new component_login;

class component_login
{
    public $html	= null;
    public $output = '';
    
    public function init()
    {
        global $IN, $STD;
        
        $this->html = $STD->template->useTemplate('login');
        
        switch ($IN['param']) {
            case 1: $this->show_register(); break;
            case 2: $this->do_login(); break;
            case 3: $this->do_logout(); break;
            case 4: $this->do_register(); break;
            case 5: $this->show_lost_password(); break;
            case 6: $this->do_password_dispatch(); break;
            case 7: $this->validate_change(); break;
            case 8: $this->do_change_password(); break;
            case 9: $this->show_lost_username(); break;
        }
        
        $STD->template->display($this->output);
    }
    
    public function gettime()
    {
        $time = time();
        return $time;
    }
    
    public function show_register()
    {
        global $IN, $STD, $DB, $CFG, $session;
        
        $sess = $session->sess_id;
        
        $this->output .= $STD->global_template->page_header('Register');
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=login&param=04');
        
        // generate anti-bot code
        
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = '';
    
        for ($x=0; $x<6; $x++) {
            $idx = rand(0, strlen($chars) - 1);
            $str .= $chars{$idx};
        }
        
        $time = time();
        $ctime = $time - 3600;
        
        $DB->query("DELETE FROM {$CFG['db_pfx']}_sec_images 
					 WHERE sessid = '{$sess}' OR time > '$ctime'");
        
        $DB->query("INSERT INTO {$CFG['db_pfx']}_sec_images (sessid,time,regcode) VALUES 
					 ('{$sess}','$time','$str')");
        
        $this->output .= $this->html->register($url, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_login()
    {
        global $IN, $STD, $session;

        if (empty($IN['username']) || empty($IN['password'])) {
            $STD->error("The username or password field was left blank.");
        }
        
        if (!$session->check_login($IN['username'], $IN['password'], 1)) {
            $STD->error("The username or password is incorrect");
        }
        
        header("Location: ".$STD->encode_url($_SERVER['PHP_SELF']));
        exit;
    }
    
    public function do_logout()
    {
        global $STD, $session;
        
        $session->check_logout();
        
        header("Location: ".$STD->encode_url($_SERVER['PHP_SELF']));
        exit;
    }
    
    public function do_register()
    {
        global $IN, $STD, $CFG, $DB, $session;
        
        $sess = $session->sess_id;
        
        // Form Validation
        //if (!$STD->validate_form($IN['security_token']))
        //	$STD->error("The registration request did not originate from this site, or you attempted to repeat a completed transaction.");
        
        // Captcha
        $DB->query("SELECT regcode FROM {$CFG['db_pfx']}_sec_images WHERE sessid = '{$sess}'");
        $row = $DB->fetch_row();
        
        if (!$row) {
            $STD->error("Security Code Invalid");
        } elseif (strtolower($row['regcode']) != strtolower($IN['regcode'])) {
            $STD->error("Security Code Invalid");
        }
        
        // Fields
        
        if (empty($IN['username']) || empty($IN['password']) || empty($IN['email'])) {
            $STD->error("One or more required fields were left blank.");
        }
        
        if (!preg_match($STD->get_regex('email'), $IN['email'])) {
            $STD->error("Email address is invalid.");
        }
        
        if (!preg_match($STD->get_regex('url'), $IN['weburl']) || empty($IN['website'])) {
            $IN['weburl'] = '';
        }
        
        if (!preg_match($STD->get_regex('url'), $IN['image'])) {
            $IN['image'] = '';
        }
        
        $IN['username'] = $STD->standard_char($IN['username']);
        
        // Check if username is a duplicate
        $user = new user;
        if ($user->getByName($IN['username'])) {
            $STD->error("The selected username is allready in use.  Please chose another one.");
        }
        
        // Check for banned email addresses
        $mails = explode(",", $CFG['emaillist']);
        
        // validate email banlist - make 'em lowercase to stop people from fooling me
        foreach ($mails as $fe) {
            if (empty($fe)) {
                continue;
            }
                
            if (strtolower($fe) == strtolower($IN['email'])) {
                $STD->error("Email address is invalid.");
            }
        }
            
        // Process Form
        $user->data['username'] = $IN['username'];
        $user->data['password'] = md5($IN['password']);
        $user->data['email']	= $IN['email'];
        $user->data['icon']		= $IN['image'];
        $user->data['website']	= $IN['website'];
        $user->data['weburl']	= $IN['weburl'];
        $user->data['skin']		= 0;
        $user->data['show_thumbs'] = 1;
        $user->data['use_comment_msg'] = 1;
        $user->data['use_comment_digest'] = 1;
        $user->data['registered_ip']	= $_SERVER['REMOTE_ADDR'];
        $user->data['cookie']	= md5(uniqid(rand()));
        $user->data['join_date']	= time();
        $user->data['gid']		= 5;
        
        if (!$user->insert()) {
            $STD->error("Failed to create new user account.  Please contact an Administrator.");
        }
        
            
        // Display Success
        //	$TPL->setTemplate('message');

        $url = $STD->encode_url($_SERVER['PHP_SELF']);
        $username = htmlspecialchars($IN['username']);
        $message = "Congratulations, your new account, <b>$username</b>, has been registered.<br /><br />
					Use the login form on the left side to login for the first time.  From the menu under your name, 
					you'll be able to submit new files to the site, manage and update your existing submissions, 
					change your viewing preferences for this site, and view messages tracking your submissions.
					<p align='center'><a href='$url'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->message($message);
        //	$TPL->addTag('message', $message);
        
        $STD->clear_form_token();
    }
    
    public function show_lost_password()
    {
        global $IN, $STD;
        
        if (!empty($STD->user['uid'])) {
            $STD->error("This form is for recovering lost passwords.  Please change your password in your preferences page.");
        }
            
        $this->output .= $STD->global_template->page_header('Lost Password');
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=login&param=06&type=pass');
        
        $this->output .= $this->html->lost_password($url, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function show_lost_username()
    {
        global $IN, $STD;
        
        if (!empty($STD->user['uid'])) {
            $STD->error("This form is for recovering lost passwords.  Please change your password in your preferences page.");
        }
            
        $this->output .= $STD->global_template->page_header('Lost Username');
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=login&param=06&type=user');
        
        $this->output .= $this->html->lost_username($url, $STD->make_form_token());
        
        $this->output .= $STD->global_template->page_footer();
    }
    
    public function do_password_dispatch()
    {
        global $IN, $STD, $DB, $CFG;
        
        // Form Validation
        //if (!$STD->validate_form($IN['security_token']))
        //	$STD->error("The registration request did not originate from this site, or you attempted to repeat a completed transaction.");
        
        if ($IN['type'] == 'pass' && empty($IN['username'])) {
            $STD->error("You must enter your username.");
        } elseif ($IN['type'] == 'user' && empty($IN['email'])) {
            $STD->error("You must enter your email address.");
        }
        
        // Check if username exists
        $user = new user;
        if ($IN['type'] == 'pass') {
            $user->query_condition("u.username = '{$IN['username']}'");
        } else {
            $user->query_condition("u.email = '{$IN['email']}'");
        }
        
        $user->getAll();
        if (!$user->nextItem()) {
            if ($IN['type'] == 'pass') {
                $STD->error("The specified user does not exist.");
            } else {
                $STD->error("The specified email address is not on record.");
            }
        }
        
        //if (!$user->getByName($IN['username']))
        //	$STD->error("The specified user does not exist.");
        
        // See if this user has allready set a password reset request recently
        $time = time();
        $time_lim = $time - 3600;
        $DB->query("SELECT lid FROM {$CFG['db_pfx']}_mail_log 
					WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND type = 2 AND date > $time_lim");
        if ($DB->get_num_rows() > 0) {
            $STD->error("You can only submit one password change request per hour.");
        }
        
        // Dispatch an email
        require_once ROOT_PATH.'lib/mailer.php';
        
        $url = "{$CFG['root_url']}/index.php?act=login&param=07&val={$user->data['cookie']}";
        $message = "{$user->data['username']},\n\nThis email is being sent to you because you requested to recover a lost password.  This message was sent from {$_SERVER['REMOTE_ADDR']}.  If you did not request this message, or this is not your IP, ignore this message and contact an administrator.\n\nTo proceed with changing your password, follow the link below:\n$url\n\nThis link contains sensitive information and should not be shared with anyone, just as you would not share your password.\n\nBest regards,\n{$CFG['site_name']} staff\n";
        
        $email = new mailer();
        $email->to = $user->data['email'];
        $email->subject = "Lost Password Request";
        $email->message = $message;

        $email->dispatch();
        
        // Log action
        $DB->query("INSERT INTO {$CFG['db_pfx']}_mail_log (uid,type,date,ip,recipient) VALUES (0,
					'2',$time,'{$_SERVER['REMOTE_ADDR']}','{$user->data['uid']}')");
        
        // Done here
        
        $url = $STD->encode_url($_SERVER['PHP_SELF']);
        $message = "An email has been sent to the address on file with further instructions on changing your password.
					If no email shows up, contact an administrator.
					<p align='center'><a href='$url'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->message($message);
        
        $STD->clear_form_token();
    }
    
    public function validate_change()
    {
        global $STD, $IN;
        
        if (empty($IN['val'])) {
            $STD->error("Invalid validation link supplied.");
        }
        
        if (!empty($STD->user['uid'])) {
            $STD->error("This form is for recovering lost passwords.  Please change your password in your preferences page.");
        }
        
        $user = new user;
        $user->query_condition("cookie = '{$IN['val']}'");
        $user->getAll();
        if (!$user->nextItem()) {
            $STD->error("Invalid validation link supplied.");
        }
        
        $this->output .= $STD->global_template->page_header('Reset Lost Password');
        
        $url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=login&param=08');
        
        $this->output .= $this->html->change_password($url, $STD->make_form_token(), $user->data['cookie']);
        
        $this->output .= $STD->global_template->page_footer();
    }
        
    public function do_change_password()
    {
        global $STD, $IN;
        
        if (empty($IN['username']) || empty($IN['pass1']) || empty($IN['pass2'])) {
            $STD->error("You must fill out all the fields in the form.");
        }
        
        if ($IN['pass1'] != $IN['pass2']) {
            $STD->error("Your passwords do not match.  Please go back and correct this.");
        }
        
        $user = new user;
        $user->query_condition("cookie = '{$IN['cookie']}'");
        if (!$user->getByName($IN['username'])) {
            $STD->error("The username you supplied is not valid for this change request.");
        }
        
        $user->data['password'] = md5($IN['pass1']);
        $user->update();
        
        // Done here
        
        $url = $STD->encode_url($_SERVER['PHP_SELF']);
        $message = "Your password has successfully been changed.  You may now login.
					<p align='center'><a href='$url'>Return to the main page</a></p>";
        
        $this->output = $STD->global_template->message($message);
        
        $STD->clear_form_token();
    }
}
