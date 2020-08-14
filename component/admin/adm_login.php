<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// component/login.php --
// handles side display of login and login pages
//------------------------------------------------------------------

$component = new component_adm_login;

class component_adm_login
{
    public $html		= "";
    public $output		= "";
    
    public function init()
    {
        global $IN, $STD;
        
        $this->html = $STD->template->useTemplate('adm_login');

        switch ($IN['param']) {
            case 1: $this->show_login(); break;
            case 2: $this->do_login(); break;
            case 3: $this->do_logout(); break;
        }
        
        $STD->template->display($this->output);
    }
    
    public function show_login($error='')
    {
        global $IN, $STD, $CFG;
        
        if (!empty($error)) {
            $error = $this->html->error_msg($error);
        }
            
        $login_url = $STD->encode_url($_SERVER['PHP_SELF'], 'act=login&param=02');
        
        $this->output = $this->html->login_screen($login_url, $STD->make_form_token(), $error);
        
        $STD->template->content_only = 1;
    }
    
    public function do_login()
    {
        global $IN, $STD, $session, $TPL;

        if (empty($IN['username']) || empty($IN['password'])) {
            return $this->show_login("The username or password field was left blank.");
        }
        
        if ($session->check_login($IN['username'], $IN['password'], 1)) {
            header("Location: ".$STD->encode_url($_SERVER['PHP_SELF'], 'act=main'));
            exit;
        } else {
            return $this->show_login("Your username or password was incorrect.");
        }
    }
    
    public function do_logout()
    {
        global $STD, $session;
        
        $session->clear_session();
        
        header("Location: ".$STD->encode_url($_SERVER['PHP_SELF']));
        exit;
    }
}
