<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// mailer.php --
// Handles dispatching of email through sendmail or SMTP protocol
//------------------------------------------------------------------
// References
// - RFC 788 (SMTP Specifications)
// - RFC 2554 (SMTP AUTH Extention)
//------------------------------------------------------------------

class mailer
{
    public $to;
    public $from;
    public $subject;
    public $message;
    public $headers;
    
    public $smtp_host;
    public $smtp_port;
    public $smtp_user;
    public $smtp_pass;
    public $smtp_conn;
    public $smtp_resp;
    public $smtp_code;
    
    public $interface;
    
    public function mailer()
    {
        global $CFG;
        
        $this->from = $CFG['mail_out'];
        $this->interface = $CFG['mail_interface'];
        
        $this->smtp_host = $CFG['smtp_host'];
        $this->smtp_port = $CFG['smtp_port'];
        $this->smtp_user = $CFG['smtp_user'];
        $this->smtp_pass = $CFG['smtp_pass'];
    }
    
    public function make_headers()
    {
        global $CFG;
        
        $this->headers  = "FROM: \"{$CFG['site_name']}\" <{$this->from}>\n";
        
        if ($this->interface == 'smtp') {
            $this->headers .= "TO: {$this->to}\n";
            $this->headers .= "SUBJECT: {$this->subject}\n";
        }
        
        $this->headers .= "Return-Path: {$this->from}\n";
        $this->headers .= "X-Mailer: PHP Mail Handler\n";
    }
    
    public function clean_value($value)
    {
        $value = str_replace("\r", "", $value);
        $value = str_replace("\n", "", $value);
        $value = str_replace("%0A", "", $value);
        $value = str_replace("%0D", "", $value);
        
        $value = preg_replace("/,+/", ",", $value);
        $value = preg_replace("/[ \t]+/", "", $value);
        $value = preg_replace("/[!#$%^&*\(\)\[\]\{\}\/:;\"']/", "", $value);
        
        return $value;
    }
    
    public function clean_msg($data)
    {
        $data = preg_replace("/^[\r\n]+?(.*)/", "\\1", $data);
        $data = preg_replace("/\r?\n/", "\r\n", $data);
        
        $data = preg_replace("/<br(\s+\/)?>/", "\r\n", $data);
        
        $data = str_replace("&amp;", "&", $data);
        $data = str_replace("&quot;", "\"", $data);
        $data = str_replace("&lt;", "<", $data);
        $data = str_replace("&gt;", ">", $data);
        $data = str_replace("&#60;", ">", $data);
        $data = str_replace("&#62;", "<", $data);
        $data = str_replace("&#39;", "'", $data);
        
        return $data;
    }
    
    public function build_body($data)
    {
        $data = $this->clean_msg($data);
        
        return $data;
    }
    
    public function dispatch()
    {
        global $STD;

        $this->to = $this->clean_value($this->to);
        $this->from = $this->clean_value($this->from);
        
        $this->subject = $this->clean_msg($this->subject);
        $this->message = $this->build_body($this->message);
        
        $this->make_headers();

        if (empty($this->to) || empty($this->from) || empty($this->subject) || empty($this->message)) {
            return false;
        }
        
        if ($this->interface == 'sendmail') {
            if (!@mail($this->to, $this->subject, $this->message, $this->headers)) {
                $STD->error("mail(): Could not dispatch mail.");
            }
        } else {
            $this->smtp_dispatch();
        }
    }
    
    public function smtp_fix_crlf($data)
    {
        $data = str_replace("\r", "", $data);
        $data = str_replace("\n", "\r\n", $data);
        $data = str_replace("\r\n.\r\n", "\r\n..\r\n", $data);
        $data .= "\r\n";
        
        return $data;
    }
    
    public function smtp_get_response()
    {
        $this->smtp_resp = "";
        while ($ln = fgets($this->smtp_conn, 512)) {
            $this->smtp_resp .= $ln;
            if (substr($ln, 3, 1) == ' ') {
                break;
            }
        }
    }
    
    public function smtp_send_cmd($cmd)
    {
        $this->smtp_resp = "";
        $this->smtp_code = "";
        
        fputs($this->smtp_conn, "$cmd\r\n");
        
        $this->smtp_get_response();
        $this->smtp_code = substr($this->smtp_resp, 0, 3);
    }
    
    public function smtp_error($error = '')
    {
        global $STD;
        
        $msg = "SMTP Error on: {$this->smtp_host} ({$this->smtp_port})<br />
				Response Code: {$this->smtp_code}<br />
				Response: {$this->smtp_resp}<br /><br />
				$error<br /><br />Delivery failed.";
        
        $STD->error($msg);
    }
            
    public function smtp_dispatch()
    {
        global $STD;

        $this->smtp_conn = fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 30);
        if (!$this->smtp_conn) {
            $STD->error("Could not connect to the SMTP Server to dispatch mail.");
        }
        
        $this->smtp_get_response();
        $this->smtp_code = substr($this->smtp_resp, 0, 3);
        
        if ($this->smtp_code != 220) {
            $this->smtp_error();
        }
        
        $msg_data = $this->smtp_fix_crlf("{$this->headers}\n{$this->message}");

        if (empty($this->smtp_user) || empty($this->smtp_pass)) {
            $this->smtp_send_cmd("HELO {$this->smtp_host}");
            if ($this->smtp_code != 250) {
                $this->smtp_error("Could not greet mail server.");
            }
        } else {
            $this->smtp_send_cmd("EHLO {$this->smtp_host}");
            if ($this->smtp_code != 250) {
                $this->smtp_error("Could not greet mail server with authentication.");
            }
            
            $this->smtp_send_cmd("AUTH LOGIN");
            if ($this->smtp_code != 334) {
                $this->smtp_error("Server does not support authentication.");
            }
            
            $this->smtp_send_cmd(base64_encode($this->smtp_user));
            if ($this->smtp_code != 334) {
                $this->smtp_error("The server rejected the supplied username.");
            }
            
            $this->smtp_send_cmd(base64_encode($this->smtp_pass));
            if ($this->smtp_code != 235) {
                $this->smtp_error("The server rejected the supplied password.");
            }
        }
        
        // FROM
        $this->smtp_send_cmd("MAIL FROM:{$this->from}");
        if ($this->smtp_code != 250) {
            $this->smtp_error();
        }
        
        // TO
        $this->smtp_send_cmd("RCPT TO:{$this->to}");
        if ($this->smtp_code != 250) {
            $this->smtp_error("Invalid recipient email address: {$this->to}");
        }
        
        // Snail mail
        $this->smtp_send_cmd("DATA");
        if ($this->smtp_code != 354) {
            $this->smtp_error("Error while writing message.");
        }
        
        fputs($this->smtp_conn, "$msg_data\r\n");
        
        // Clear
        $this->smtp_send_cmd(".");
        if ($this->smtp_code != 250) {
            $this->smtp_error("Could not terminate message.");
        }
        
        $this->smtp_send_cmd("QUIT");
        if ($this->smtp_code != 221) {
            $this->smtp_error("Could not clear connection.");
        }
        
        @fclose($this->smtp_conn);
    }
}
