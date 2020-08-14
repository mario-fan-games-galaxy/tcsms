<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// lib/fieldset.php --
// Controlls dynamic form generation
//------------------------------------------------------------------

class fieldset {
	
	var $skindata	= array();
	var $html		= '';
	var $cur_field	= 0;
	var $cur_part	= 1;
	var $in_subset 	= 0;
	var $subset_cnt = 0;
	var $subset_id	= '';
	
	function fieldset ($skin='') {
		global $CFG,$STD;
		
		if (!$skin)
			$skin = template::determine_template();
		$tpath = ROOT_PATH.$CFG['template_path']."/$skin";
		
		$tpl = file_get_contents("$tpath/formparts.html");
		
		$this->skindata = preg_split("/<!--\/\/[A-Za-z]*\/\/-->/", $tpl);
		array_shift($this->skindata);
	}
	
	function addField ($type, $title, $data=array()) {
		
		$newdata = $this->switchtype($type, $data);
		
		if ($this->in_subset && empty($data['sstype']))
			$skin = 2;
		elseif ($this->in_subset && $data['sstype'] == 'start')
			$skin = 4;
		elseif ($this->in_subset && $data['sstype'] == 'end')
			$skin = 5;
		elseif ($this->in_subset && $data['sstype'] == 'startend')
			$skin = 6;
		elseif ($this->in_subset && $data['sstype'] == 'info')
			$skin = 7;
		else
			$skin = 1;
		
		$cnt = $this->cur_field;
		$html = str_replace('{{title}}', $title."{{title_$cnt}}", $this->skindata[$skin]);
		$html = str_replace('{{field}}', $newdata."{{field_$cnt}}", $html);
		
		if ($this->in_subset) {
			$html = str_replace('{{id}}', $this->subset_id . chr(ord('a')+$this->subset_cnt), $html);
			$this->subset_cnt++;
		}
		
		$this->html .= $html;
		$this->cur_field++;
	}
	
	function appendField ($type, $data) {
		
		$newdata = $this->switchtype($type, $data);
		
		!empty($data['nl']) ? $br = '<br />' : $br = '';
		
		$cnt = $this->cur_field - 1;
		$this->html = str_replace("{{field_$cnt}}", ' '.$br.$newdata."{{field_$cnt}}", $this->html);
	}
	
	function appendTitle ($type, $data) {
		
		$newdata = $this->switchtype($type, $data);
		
		$cnt = $this->cur_field - 1;
		$this->html = str_replace("{{title_$cnt}}", ' '.$newdata."{{title_$cnt}}", $this->html);
	}
	
	function addBreak () {
		
		if ($this->in_subset) {
			$html = $this->skindata[8];
			$this->html .= str_replace('{{id}}', $this->subset_id . chr(ord('a')+$this->subset_cnt), $html);
			$this->subset_cnt++;
		} else
			$this->html .= $this->skindata[3];
	}
	
	function infoBlock ($id, $data) {
		
		$oldid = $this->subset_id;
		$oldis = $this->in_subset;
		$oldssc = $this->subset_cnt;
		
		$this->subset_id = $id;
		$this->in_subset = 1;
		$this->subset_cnt = 0;
		
		$this->addField('text', '', array('v' => $data, 'sstype' => 'info'));
		
		$this->subset_id = $oldid;
		$this->in_subset = $oldis;
		$this->subset_cnt = $oldssc;
	}
	
	function addPart ($title) {
		
		$html = str_replace("{{title}}", $title, $this->skindata[0]);
		$this->html .= str_replace("{{part}}", 'Part '.$this->cur_part, $html);
		$this->cur_part++;
	}
	
	function startSubset ($ssid) {
		
		$this->in_subset = 1;
		$this->subset_cnt = 0;
		$this->subset_id = $ssid;
	}
	
	function endSubset () {
		
		$this->in_subset = 0;
		$this->subset_id = '';
	}
	
	function build() {
		
		$this->html = preg_replace("/\{\{(field|title)_[0-9]+\}\}/", '', $this->html);
		
		return $this->html;
	}
	
	function switchtype ($type, &$data) {
		
		switch ($type) {
			case 'text'			:	return $this->proc_text($data);
			case 'textbox'		:	return $this->proc_textbox($data);
			case 'textfield'	:	return $this->proc_textfield($data);
			case 'select'		:	return $this->proc_select($data);
			case 'checkbox'		:	return $this->proc_checkbox($data);
			case 'checkboxlist'	:	return $this->proc_checkboxlist($data);
			case 'yesno'		:	break;
			case 'link'			:	return $this->proc_link($data);
			case 'expandlink'	:	return $this->proc_expandlink($data);
			case 'visicon'		:	return $this->proc_visicon($data);
			case 'visiconlink'	:	return $this->proc_visiconlink($data);
			case 'csvbox'		:	return $this->proc_csvbox($data);
			case 'csvlinks'		:	return $this->proc_csvlinks($data);
			case 'image'		:	return $this->proc_image($data);
			case 'infoicon'		:	return $this->proc_infoicon($data);
			case 'infoblock'	:	break;
			case 'button'		:	break;
			case 'file'			:	return $this->proc_file($data);
			case 'hidden'		:	return $this->proc_hidden($data);
		}
	}
	
	function proc_text (&$data) {
		
		return $data['v'];
	}
	
	function proc_textbox (&$data) {
		
		$html = "<input type='text' name='{$data['name']}' value='{$data['v']}' size='40' class='textbox' />";
		return $html;
	}
	
	function proc_textfield (&$data) {	
		
		$html = "<textarea rows='6' cols='38' name='{$data['name']}' class='textbox'>{$data['v']}</textarea>";
		return $html;
	}
	
	function proc_select (&$data) {
		
		$html = "<select name='{$data['name']}' size='1' class='selectbox'>";
		
		for ($x=0; $x<sizeof($data['v']['value']); $x++) {
			$selected = '';
			if ($data['v']['value'][$x] == $data['v']['sel'])
				$selected = "selected='selected'";
				
			$html .= "<option value='{$data['v']['value'][$x]}' $selected>{$data['v']['name'][$x]}</option>";
		}
		
		$html .= "</select>";
		
		return $html;
	}
	
	function proc_checkbox (&$data) {
		
		empty($data['checked']) ? $checked = '' : $checked = "checked='checked'";
		
		$html = "<input type='checkbox' name='{$data['name']}' value='{$data['value']}' $checked /> {$data['v']}";
		return $html;
	}
	
	function proc_checkboxlist (&$data) {
		
		$html = '';
		
		for ($x=0; $x<sizeof($data['v']['value']); $x++) {
			$name = htmlentities($data['v']['name'][$x]);
			$selected = '';
			if ($data['v']['sel'][$x])
				$selected = "checked='checked'";
				
			$html .= "<input type='checkbox' name='{$data['name']}' value='{$data['v']['value'][$x]}' 
				$selected /> {$name}<br />";
		}
		
		$html = preg_replace("/<br \/>$/", '', $html);
		
		return $html;
	}
	
	function proc_link (&$data) {
		global $CFG, $STD;
		
		$html = "<a href='" . $STD->encode_url($CFG['root_url'].'/'.$data['url']) . "'>{$data['v']}</a>";
		return $html;
	}
	
	function proc_expandlink (&$data) {
		
		$html = "<a href=\"javascript:show_hide('{$data['id']}a');show_hide('{$data['id']}b');show_hide('{$data['id']}c');\">{$data['v']}</a>";
		return $html;
	}
	
	function proc_visicon (&$data) {
		
		empty($data['vis']) ? $icon = 'not_visible.gif' : $icon = 'visible.gif';
		
		$html = "<img src='{{image_path}}/$icon' alt='[X]' title='{$data['v']}' />";
		return $html;
	}
	
	function proc_visiconlink (&$data) {
		global $STD;
		
		empty($data['vis']) ? $icon = 'not_visible.gif' : $icon = 'visible.gif';
		
		$html = "<img src='{{image_path}}/$icon' alt='[X]' title='{$data['v']}' border='0' />";
		
		if (!empty($data['linkvis']))
			$html = "<a href='" . $STD->encode_url($_SERVER['PHP_SELF'], $data['url']) . "'>$html</a>";
		
		return $html;
	}
	
	function proc_csvbox (&$data) {
		
		$tags = @join(',', $data['v']);
		
		$html = "<textarea id='{$data['id']}_csv' rows='4' cols='38' 
			name='{$data['name']}' class='textbox'>$tags</textarea>";
			
		return $html;
	}
	
	function proc_csvlinks (&$data) {
		
		$html = '';
		sort($data['v']);
		reset($data['v']);
		while (list(,$v) = each($data['v'])) {
			$html .= "<a href=\"javascript:add_tag('{$data['id']}_csv','{$v}');\">{$v}</a>, ";
		}
		
		$html = preg_replace("/,[ ]$/", '', $html);
		
		return $html;
	}
	
	function proc_image (&$data) {
		
		$html = "<img src='{$data['v']}' border='0' alt='image' />";
		return $html;
	}
	
	function proc_infoicon (&$data) {
		
		$html = "<a href=\"javascript:show_hide('{$data['id']}a');show_hide('{$data['id']}b');show_hide('{$data['id']}c');\">
				 <img src='{{image_path}}/info.gif' border='0' alt='[Info]' /></a>";
		return $html;
	}
	
	function proc_file (&$data) {
		
		$html = "<input type='file' name='{$data['name']}' class='textbox' size='40' />";
		return $html;
	}
	
	function proc_hidden (&$data) {
		
		$html = "<input type='hidden' name='{$data['name']}' value='{$data['value']}' />";
		return $html;
	}
}