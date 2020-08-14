<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// component/modules/module_base.php --
// Root Module
//------------------------------------------------------------------

class module {
	
	var $extable			= '';
	var $file_restrictions	= array();
	
	function check_file_restrictions ($file, $type, $ecode='') {
		global $STD, $TPL;
		
		if (empty($this->file_restrictions[$type]))
			$TPL->preprocess_error("Bad filetype supplied to file check routine.");
		
		if (empty($_FILES[$file]))
			$this->error_save("No file supplied.", $ecode);
		
		// Rudimentary File Check
		switch ($_FILES[$file]['error']) {
			case 1: $this->error_save("A file you attempted to upload exceeded the maximum file size allowed by the server.  Please contact a staff member about your submission.", $ecode); break;
			case 2: $this->error_save("A file you attempted to upload exceeded the maximum file size allowed by the server.  Please contact a staff member about your submission.", $ecode); break;
			case 3: $this->error_save("Your file did not finish transfering to the server.  Please try again and contact a staff member if the problem persists.", $ecode); break;
			case 4: $this->error_save("A file you attempted to upload was not found or doesn't exist.  Check that the path to the file and the filename are correct.", $ecode); break;
			case 6: $this->error_save("The server was not able to appropriately handle your submission.  Please contact a staff member.", $ecode); break;
		}
			
		// Mime Check
		if (isset($this->file_restrictions[$type]['mime'])) {
			$exts = @join(', ', $this->file_restrictions[$type]['ext']);
			$pred_type = $this->mime_check($_FILES[$file]['tmp_name'], $_FILES[$file]['name'], $_FILES[$file]['type']);
			if (!in_array($pred_type, $this->file_restrictions[$type]['mime']))
				$this->error_save("Invalid file type for file <b>{$_FILES[$file]['name']}</b>, file must 
					be of one of the following types: $exts.<br /><br />Your file is of type: 
					<b>{$_FILES[$file]['type']}</b>", $ecode);
		}
		
		// Size Check
		if (isset($this->file_restrictions[$type]['size'])) {
			$sizes = $this->file_restrictions[$type]['size'];
			if ($_FILES[$file]['size'] < $sizes[0])
				$this->error_save("File <b>{$_FILES[$file]['name']}</b> is smaller than the minimum allowed 
					size of <b>{$sizes[2]}</b> for this type.", $ecode);
			
			if ($_FILES[$file]['size'] > $sizes[1])
				$this->error_save("File <b>{$_FILES[$file]['name']}</b> is larger than the maximum allowed 
					size of <b>{$sizes[3]}</b> for this type.", $ecode);
		}
			
		// Dimention Check
		if (isset($this->file_restrictions[$type]['width'])) {
			$width = $this->file_restrictions[$type]['width'];
			$info = getimagesize($_FILES[$file]['tmp_name']);
			if ($info[0] < $width[0] || $info[0] > $width[1])
				$this->error_save("Image file <b>{$_FILES[$file]['name']}</b> must be between <b>{$width[0]}</b> and 
					<b>{$width[1]}</b> pixels wide.", $ecode);
		}
		
		if (isset($this->file_restrictions[$type]['height'])) {
			$height = $this->file_restrictions[$type]['height'];
			$info = getimagesize($_FILES[$file]['tmp_name']);
			if ($info[1] < $height[0] || $info[1] > $height[1])
				$this->error_save("Image file <b>{$_FILES[$file]['name']}</b> must be between <b>{$height[0]}</b> and 
					<b>{$height[1]}</b> pixels high.", $ecode);
		}
		
		return true;
	}
	
	function move_file ($file, $type, $ecode='') {
		global $IN, $STD, $TPL;
		
		// Check that directories exist
		if (!file_exists(ROOT_PATH."$type/{$IN['c']}"))
			$STD->template->preprocess_error("Destination directory \"$type/{$IN['c']}\" does not exist.");
		
		if (empty($_FILES[$file]))
			$this->error_save("No file supplied.", $ecode);
		
		// Move File
		$fname = $STD->safe_file_name($_FILES[$file]['name']);
		if (!move_uploaded_file($_FILES[$file]['tmp_name'], ROOT_PATH."$type/{$IN['c']}/$fname"))
			$this->error_save("Could not relocate one or more of your files to their proper locations.  Please contact a staff member.", $ecode);
		
		return $fname;
	}
	
	// Returns the most probable mime type for the file
	function mime_check ($file, $name='', $mime='') {
		global $STD;
		
		require ROOT_PATH.'mime_info.php';
		
		if (!file_exists($file))
			return false;
		
		$mfe = array();
		
		if (!empty($name))
			$filename = basename($name);
		else
			$filename = basename($file);
			
		// get file extention - third tier criteria
		$fileparts = explode(".", $file);
		$ext = strtoupper( $fileparts[ sizeof($fileparts)-1 ] );

		// get supplied mime-type - second tier criteria
		// supplied by $mime
		
		// examine file contents for magic patterns - first tier criteria
		if (!empty($MIME_INFO[$ext]))
			$mfe = $MIME_INFO[$ext];
			
		$fh = fopen($file, "rb");
		
		$pattern_match = '';
		
		if (!empty($mfe) && !empty($mfe[4])) {
			fseek($fh, $mfe[2]);
			$fbin = fread($fh, $mfe[3]);
			if (preg_match("/{$mfe[4]}/", $fbin))
				$pattern_match = $mfe[0];
		} else {
			foreach($MIME_INFO as $k => $mf) {
				if ($pattern_match)
					break;
				if (empty($mf[4]))
					continue;
					
				fseek($fh, $mf[2]);
				$fbin = fread($fh, $mf[3]);
				if (preg_match("/{$mf[4]}/", $fbin))
					$pattern_match = $mf[0];
			}
		}
		
		fclose($fh);
		
		// Draw a conclusion
		if ($pattern_match && $pattern_match == $mime)
			return $pattern_match;
		
		if (!empty($mfe) && $mfe[0] == $pattern_match)
			return $pattern_match;
		
		if (!empty($mfe) && $mfe[0] != $pattern_match && $mime)
			return $mime;
		
		if ($pattern_match)
			return $pattern_match;
		
		if ($mime)
			return $mime;
		
		return 'unknown/unknown';
	}
			
	
	function format_filesize ($file) {
	
		$size = filesize($file);
		
		if ($size < 1024)
			return "$size bytes";
		if ($size < 1024*1024)
			return round($size/1024, 2) . ' KB';
		if ($size < 1024*1024*1024)
			return round($size/(1024*1024), 2) . 'MB';
		if ($size < 1024*1024*1024*1024)
			return round($size/(1024*1024*1024), 2) . 'GB';
	}
	
	function get_thumbnail (&$row, $skin='') {
		global $CFG, $IN, $STD;
		
		if (empty($skin))
			$skin = $STD->tags['skin'];
			
		if (!empty($row['thumbnail']) && file_exists(ROOT_PATH."thumbnail/{$row['type']}/{$row['thumbnail']}"))
			return "<img src='{$CFG['root_url']}/thumbnail/{$row['type']}/{$row['thumbnail']}' border='0' alt='Thumbnail' />";
		else
			return "<img src='{$CFG['root_url']}/template/$skin/images/t_unavailable.png' border='0' alt='Thumbnail Unavailable' />";
	}
	
	function get_image (&$row, $type) {
		global $CFG, $IN;
		
		if (!empty($row[$type]) && file_exists(ROOT_PATH."$type/{$IN['c']}/{$row[$type]}"))
			return "<img src='{$CFG['root_url']}/$type/{$IN['c']}/{$row[$type]}' border='0' alt='$type' />";
		else
			return '';
	}
	
	function get_filesize (&$row) {
		global $CFG, $IN;
		
		if (!empty($row['file']) && file_exists(ROOT_PATH."file/{$IN['c']}/{$row['file']}"))
			return $this->format_filesize(ROOT_PATH."file/{$IN['c']}/{$row['file']}");
		else
			return 'File Unavailable';
	}
	
	function get_email_icon (&$row, $prefix='') {
		global $CFG, $IN, $STD;
		
		if (!$row[$prefix.'show_email']) {
			$email = '';
		} else {
			$addr = str_replace('@', ' _AT_ ', $row[$prefix.'email']);
			$addr = str_replace('.', ' _DOT_ ', $addr);
			$uaddr = str_replace(' ', '%20', $addr);
			$email = "<a href='mailto:$uaddr'>
				<img src='{$STD->tags['image_path']}/email.gif' border='0' alt='[E]' title='$addr' /></a>";
		}
		
		return $email;
	}
	
	function get_website_icon (&$row, $prefix='') {
		global $CFG, $IN, $STD;
		
		empty($row[$prefix.'weburl']) && empty($row[$prefix.'weburl_override'])
			? $ws_icon = 'home_nolink.gif'
			: $ws_icon = 'home.gif';
		empty($row[$prefix.'website'])
			? $website = ''
			: $website = "
				<img src='{$STD->tags['image_path']}/$ws_icon' border='0' alt='[W]' title='{$row[$prefix.'website']}' />";
		if (!empty($row[$prefix.'website_override']))
			$website = "
				<img src='{$STD->tags['image_path']}/$ws_icon' border='0' alt='[W]' title='{$row[$prefix.'website_override']}' />";
		if (!empty($row[$prefix.'weburl_override']))
			$website = "<a href='{$row[$prefix.'weburl_override']}'>$website</a>";
		elseif (!empty($row[$prefix.'weburl']))
			$website = "<a href='{$row[$prefix.'weburl']}'>$website</a>";
		
		return $website;
	}
	
	function build_thumbnail ($src, $dest, $width, $height, $minwidth=0, $minheight=0) {
		global $STD;
		
		if (!file_exists($src))
			return false;
			
		list($cw, $ch, $type) = getimagesize($src);
		
		$cch = $ch;
		$ccw = $cw;
		
		if ($ccw > $width) {
			$cch = ($width / $ccw) * $cch;
			$ccw = $width;
		}
		
		if ($cch > $height) {
			$ccw = ($height / $cch) * $ccw;
			$cch = $height;
		}
		
		if ($ccw < $minwidth)
			$ccw = $minwidth;
		if ($cch < $minheight)
			$cch = $minheight;	
		
		switch ($type) {
			case 1: $image_src = imagecreatefromgif($src); break;
			case 2: $image_src = imagecreatefromjpeg($src); break;
			case 3: $image_src = imagecreatefrompng($src); break;
			default: return false;
		}
		
		$image_new = imagecreatetruecolor($ccw, $cch);
		$black = imagecolorallocate($image_new, 0, 0, 0);
		imagefill($image_new, 0, 0, $black);
		imagecopyresampled($image_new, $image_src, 0, 0, 0, 0, floor($ccw), floor($cch), $cw, $ch);
		
		return imagepng($image_new, $dest);
	}
	
	function get_version_history ($rid) {
		global $CFG, $DB;
		
		$fields = array('rid'	=> $rid);
		$where = $DB->format_db_where_string($fields);
		
		$list = $DB->query("SELECT * FROM {$CFG['db_pfx']}_version WHERE {$where} ORDER BY date DESC");
		
		return $list;
	}
	
	function format_username (&$row, $prefix='') {
		global $STD;
		
		$user = '<b>N/A</b>';
		
		if (!empty($row[$prefix.'username']) && !empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0)
			$user = $row[$prefix.'username'];
		
		if (!empty($row['author_override']))
			$user = $row['author_override'];
		
		if (!empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0)
			$user = "<a href='".$STD->encode_url($_SERVER['PHP_SELF'],"act=user&param=01&uid={$row[$prefix.'uid']}")."'>$user</a>";
		
		return $user;
	}
	
	// Interface Stubs
	function user_submit_view_prep () {
		global $TPL;
		$TPL->preprocess_error("user_submit_view_prep() stub not implemented for active module.");
	}
	
	function error_save ($error, $code=0) {
		global $STD, $IN, $session;
		
		$IN['err_save'] = $code;
		
		$session->data['err_save'] = $IN;
		
		$STD->error($error);
	}
	
	function add_filters ($resid, $data) {
		global $CFG, $DB;
		
		$da = array();
		while (list(,$v) = each($data)) {
			if (is_array($v))
				for ($x=0; $x<sizeof($v); $x++)
					$da[] = "('{$v[$x]}','$resid')";
			else
				$da[] = "('$v','$resid')";
		}

		$ins = @join(',', $da);
		
		$DB->query("INSERT INTO {$CFG['db_pfx']}_filter_multi VALUES $ins");
	}
	
	function clear_filters ($resid) {
		global $CFG, $DB;
		
		$where = $DB->format_db_where_string(array('rid'	=> $resid));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_filter_multi WHERE $where");	
	}
	
	function make_catwords ($resid) {
		global $STD, $CFG, $DB, $IN;
		
		$where = $DB->format_db_where_string(array('rid'	=> $resid,
												   'u.mid'	=> $IN['c']));
		$DB->query("SELECT f.name,f.search_tags,u.store_keywords FROM {$CFG['db_pfx']}_filter_multi m ".
				   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_list f ON (m.fid = f.fid) ".
				   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_group g ON (f.gid = g.gid) ".
				   "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_use u ON (u.gid = g.gid) ".
				   "WHERE $where");
		
		$autokeywords = array();
		
		while ($row = $DB->fetch_row()) {

			if ( !empty($row['store_keywords']) ) {
				$name = preg_replace("/[^\w\s]/", "", $row['name']);
				$autokeywords[] = strtolower($name);
			}
			
			$stags = explode(",", strtolower( $row['search_tags'] ));
			
			while (list(,$v) = each ($stags)) {
				if ( empty($v) )
					continue;
				$autokeywords[] = $v;
			}
		}

		$autokeywords = array_unique($autokeywords);
		$keywords = @join(",", $autokeywords);
		
		return $keywords;
	}
		
	
	function parse_keywords ($keywords) {
		
		$keywords = explode(",", $keywords);
		for ($x=0; $x<sizeof($keywords); $x++) {
			$keywords[$x] = preg_replace( "/^S_/i", "", $keywords[$x] );
		}
		
		return @join(",", $keywords);
	}
	
	// ARRAY make_cat_tags (INT cat [, ARRAY tags])
	//
	// Returns an array of data to be used in the creation of one or more form boxes, namely select boxes and
	// sets of checkboxes.  The number of arrays returned and format of the arrays depends upon the nodedef type
	// of the category.  A typical array will have the format: value, name, selected.
	
	function make_catset ($gkey, &$data, $def=array()) {
		global $IN, $STD;
		
		$varr = array(0); $narr = array('---');
		$selected = 0;
		
		reset ($data);
		while (list(,$v) = each($data)) {
			if ($v['keyword'] == $gkey) {
				$varr[] = $v['fid'];
				$narr[] = $v['name'];
				if (in_array($v['fid'], $def))
					$selected = $v['fid'];
			}
		}
		
		return array('value' => $varr, 'name' => $narr, 'sel' => $selected);
	}
	
	function make_catsetmulti ($gkey, &$data, $def=array()) {
		global $IN, $STD;
		
		$varr = array(); $narr = array(); $sarr = array();
		
		reset ($data);
		while (list(,$v) = each($data)) {
			if ($v['keyword'] == $gkey) {
				$varr[] = $v['fid'];
				$narr[] = $v['name'];
				
				in_array($v['fid'], $def) ? $sarr[] = 1 : $sarr[] = 0;
			}
		}
		
		return array('value' => $varr, 'name' => $narr, 'sel' => $sarr);
	}
	
	// Optional components
	
	function mod_action (&$RES, $code) {
		// a  : accept
		// au : accept user-mod
		// d  : decline
		// dq : decline-queued
		// du : decline user-mod
		// r  : requeue
		// e  : admin-modify
		// eq : admin-modify-queued
	}
}