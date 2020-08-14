<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// lib/parser.php --
// Parses special syntax in posts
//------------------------------------------------------------------

class parser {
	
	var $quote_open = 0;
	var $quote_close = 0;
	
	function convert ($data) {
		global $STD;
		
		if ($STD->user['use_bbcode'])
		{
			$data = preg_replace("/\[b\](.*?)\[\/b\]/is", "<b>\\1</b>", $data);
			$data = preg_replace("/\[i\](.*?)\[\/i\]/is", "<i>\\1</i>", $data);
			$data = preg_replace("/\[u\](.*?)\[\/u\]/is", "<u>\\1</u>", $data);
			$data = preg_replace("/\[s\](.*?)\[\/s\]/is", "<s>\\1</s>", $data);
			$data = preg_replace("/\[sup\](.*?)\[\/sup\]/is", "<sup>\\1</sup>", $data);
			$data = preg_replace("/\[sub\](.*?)\[\/sub\]/is", "<sub>\\1</sub>", $data);
			
			// URLs
			$data = preg_replace_callback("/\[url=(\S+?)\](.*?)\[\/url\]/is", array(&$this, 'convert_url'), $data);
			$data = preg_replace_callback("/\[url\](\S+?)\[\/url\]/i", array(&$this, 'convert_url'), $data);
			$data = preg_replace_callback("/\[gonzo\](\S+?)\[\/gonzo\]/i", array(&$this, 'convert_image'), $data);
			$data = preg_replace_callback("/\[email\](\S+?)\[\/email\]/i", array(&$this, 'convert_email'), $data);
			
			// Quoting
			$data = preg_replace_callback("/\[quote(?:=.+?)?\](.*)\[\/quote\]/is", array(&$this, 'convert_quote'), $data);
		
		}
		
		// Auto-convert URLs
		$data = preg_replace("/(?<!=[\"'])\b(https?|ftp|file):\/\/[-a-zA-Z0-9+&@#\/%?=~_|!:,.;]*[-a-zA-Z0-9+&@#\/%=~_|]/i", "<a href=\"\\0\">\\0</a>", $data);
		// $data = preg_replace("/(<a.+? >.*?)<a.+? >(.*?)<\/\s*a>(.*?<\/\s*a>)/i", "\\1\\2\\3", $data);
		// Commented because it was causing problem - be sure to remove the space between ? and > if you uncomment it.
		
		return $data;
	}
	
	function convert_url ($matches) {
		global $STD;
		
		preg_match("/[-a-zA-Z0-9+&@#\/%?=~_|!:,.;]+/", $matches[1], $nmat);
		$url = str_replace("javascript:", "", $nmat[0]);
		$url = preg_replace("/(^&#39;)|(^&quote;)|(&#39;$)|(&quot;$)/", "", $url);
		
		if (!preg_match("/^[a-zA-Z]+:\/\//", $url)) {
			$url = 'http://' . $url;
		}
		
		if (empty($matches[2])) {
			$url = "<a href=\"{$url}\">{$url}</a>";
		} else {
			$url = "<a href=\"{$url}\">{$matches[2]}</a>";
		}
		
		return $url;
	}
	
	function convert_image ($matches) {
		global $STD;
		
		preg_match("/[-a-zA-Z0-9+&@#\/%?=~_|!:,.;]+/", $matches[1], $nmat);
		$url = str_replace("javascript:", "", $nmat[0]);
		$url = preg_replace("/(^&#39;)|(^&quote;)|(&#39;$)|(&quot;$)/", "", $url);
		
		if (!preg_match("/^[a-zA-Z]+:\/\//", $url)) {
			$url = 'http://' . $url;
		}
		
		if (empty($matches[2])) {
			$url = "<img src=\"{$url}\" alt=\"user posted image\" />";
		} else {
			$url = "<img src=\"{$url}\" alt=\"user posted image\" />";
		}
		
		return $url;
	}
	
	function convert_email ($matches) {
		global $STD;
		
		$matches[2] = $matches[1];
		$matches[1] = 'mailto:'.$matches[1];
		
		
		return $this->convert_url($matches);
	}
	
	function convert_quote ($matches) {
		global $STD, $CFG;
		
		$this->quote_open = 0;
		$this->quote_close = 0;

		$data = $matches[0];
		
		$data = preg_replace_callback("/\[quote\]/", array(&$this, 'convert_quote_open_l1'), $data);
		$data = preg_replace_callback("/\[quote=(.+?)\]/s", array(&$this, 'convert_quote_open_l2'), $data);
		$data = preg_replace_callback("/\[\/quote\]/", array(&$this, 'convert_quote_close'), $data);
		
		if ($this->quote_open != $this->quote_close)
			$STD->error("Improperly nested quotes encountered.");
		
		return $data;
	}
	
	function convert_quote_open_l1 ($matches) {
		global $STD;
		
		$this->quote_open++;
		$data = "<!--QuoteStart--><div class=\"quotetitle\">Quote</div><div class=\"quote\">";
		
		return $data;
	}
	
	function convert_quote_open_l2 ($matches) {
		global $STD, $CFG;
		
		$this->quote_open++;
		
		preg_match("/^(&quot;.+?&quot;|.+?)((?=,)|$)/", $matches[1], $user_part);
		$user_part = $user_part[0];

		$rem = str_replace($user_part, "", $matches[1]);
		
		$user_part = preg_replace("/^&quot;|&quot;$/", "", $user_part);
		$date_part = preg_replace("/^,/", "", $rem);
		
		$qtext = "<span style='font-weight:normal'>(<!--QuoteName-->$user_part";
		if (!empty($rem))
			$qtext .= "<!--QuoteDate--> on $date_part";
		$qtext .= ")</span>";
		
		$data = "<!--QuoteStart--><div class=\"quotetitle\">Quote $qtext</div><div class=\"quote\">";
		
		return $data;
	}
	
	function convert_quote_close ($matches) {
		global $STD;
		
		$this->quote_close++;
		$data = "</div><!--QuoteEnd-->";
		
		return $data;
	}
	
	function unconvert ($data) {
		global $STD;
		
		$data = preg_replace("/<b>(.*?)<\/b>/is", "[b]\\1[/b]", $data);
		$data = preg_replace("/<u>(.*?)<\/u>/is", "[u]\\1[/u]", $data);
		$data = preg_replace("/<i>(.*?)<\/i>/is", "[i]\\1[/i]", $data);
		$data = preg_replace("/<s>(.*?)<\/s>/is", "[s]\\1[/s]", $data);
		$data = preg_replace("/<sup>(.*?)<\/sup>/is", "[sup]\\1[/sup]", $data);
		$data = preg_replace("/<sub>(.*?)<\/sub>/is", "[sub]\\1[/sub]", $data);
		
		// URLs
		$data = preg_replace("/<a\s+href=[\"\']mailto:(\S+?)[\"\']>\s*\\1\s*<\/a>/is", "[email]\\1[/email]", $data);
		$data = preg_replace("/<a\s+href=[\"\'](\S+?)[\"\']>\s*\\1\s*<\/a>/is", "[url]\\1[/url]", $data);
		$data = preg_replace("#<img src=[\"'](\S+?)['\"].+?".">#", "[img]\\1[/img]", $data);
		$data = preg_replace("/<a\s+href=[\"\'](\S+?)[\"\']>(.*?)<\/a>/is", "[url=\\1]\\2[/url]", $data);
		
		// Quotes
		$data = preg_replace("/<!--QuoteStart--><div class=\"quotetitle\">Quote<\/div><div class=\"quote\">/is", "[quote]", $data);
		$data = preg_replace_callback("/<!--QuoteStart--><div class=\"quotetitle\">Quote <span style='font-weight:normal'>\((.+?)\)<\/span><\/div><div class=\"quote\">/is", array(&$this, 'unconvert_quote'), $data);
		$data = preg_replace("/<\/div><!--QuoteEnd-->/i", "[/quote]", $data);
		
		$data = preg_replace("/<br\s*\/?>/i", "\n", $data);
		
		return $data;
	}
	
	function unconvert_quote ($matches) {
		global $STD;
		
		$parts = explode("<!--QuoteDate--> on ", $matches[1]);
		$user = str_replace("<!--QuoteName-->", "", $parts[0]);
		if (strpos($user, ',') !== false)
			$user = "&quot;$user&quot;";
			
		if (sizeof($parts) == 1) {
			$data = "[quote=$user]";
		} else {
			$date = $parts[1];
			$data = "[quote=$user,$date]";
		}
		
		return $data;
	}
}

?>