<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
// 
// resource.php --
// Resource Abstraction Layer
//------------------------------------------------------------------

require_once ROOT_PATH.'lib/std.php';

class resource extends table_frame {
	
	var $module = null;
	
	function resource () {
		global $IN, $STD;
		
		if (!empty($IN['c']))
			$this->module = $STD->modules->get_module($IN['c']);
	}
	
	function update_module ($mid) {
		global $DB, $CFG, $STD;
		
		if ($this->module && $this->module['mid'] == $mid)
			return;

		$mrow = $STD->modules->get_module($mid);
		if (!$mrow)
			$STD->error("Attempt to call a non-existant module id: '{$mid}'.");
		
		$this->module = $mrow;
	}		
	
	function get ($rid) {
		global $CFG, $DB, $STD;
		
		// Build Query
		$qp = $this->query_build();
		
		$where = $DB->format_db_where_string(array('r.rid'	=> $rid));
		$DB->query("SELECT {$qp['select']} FROM {$qp['from']} WHERE $where {$this->condition}");
		$this->data = $DB->fetch_row();
		
		return $this->data;
	}

	function getByType ($type) {
		global $CFG, $DB, $STD;
		
		// Build Query
		$qp = $this->query_build();
		
		$where = $DB->format_db_where_string(array('r.type' => $type));
		$this->cquery = $DB->query("SELECT {$qp['select']} FROM {$qp['from']} ".
								   "WHERE $where {$this->condition} {$this->order} {$this->limit}");
		
		return $this->cquery;
	}
	
	function countByType ($type) {
		global $CFG, $DB, $STD;
		
		// Build Query
		$qp = $this->query_build();
		
		$where = $DB->format_db_where_string(array('r.type' => $type));
		$DB->query("SELECT COUNT(*) AS cnt FROM {$qp['from']} WHERE $where {$this->condition}");
		
		return $DB->fetch_row();
	}
	
	function create ($data = array()) {	
		global $STD;

		$this->clean($data);
		
		if (in_array('extention', $this->use)) {
			require_once ROOT_PATH."component/modules/{$this->module['module_file']}";
			$mod = new $this->module['class_name'];
			$data = $mod->return_ex_data($data);
			$data = $this->clear_prefix($data, 'e.');
			$this->data = array_merge($this->data, $data);
		}
		
	}
	
	function insert () {
		global $CFG, $DB;
		
		if (in_array('extention', $this->use)) {
			require_once ROOT_PATH."component/modules/{$this->module['module_file']}";
			$mod = new $this->module['class_name'];
			$data = $mod->return_ex_data($this->data);
			$data = $this->clear_prefix($data, 'e.');
			
			$ins = $DB->format_db_values($data);
			$DB->query("INSERT INTO {$CFG['db_pfx']}_{$this->module['table_name']}
						({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
			$this->data['eid'] = $DB->get_insert_id();
		}
		
		$this->clean($this->data);
		
		$ins = $DB->format_db_values($this->data);
		$DB->query("INSERT INTO {$CFG['db_pfx']}_resources ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
		
		$this->data['rid'] = $DB->get_insert_id();
	}
	
	function update () {
		global $CFG, $DB, $STD;
		
		if (empty($this->data) || empty($this->data['rid']))
			return false;
		
		$save = $this->data;
		
		$rid = $this->data['rid'];
		$data = array();
		$update = "{$CFG['db_pfx']}_resources r";
		$where = array('r.rid' => $rid);
		
		if (in_array('extention', $this->use)) {
			require_once ROOT_PATH."component/modules/{$this->module['module_file']}";
			$mod = new $this->module['class_name'];
			$data = $mod->return_ex_data($this->data);
			$update .= ", {$CFG['db_pfx']}_{$this->module['table_name']} e";
			$where['e.eid'] = $this->data['eid'];
		}
		
		$this->clean($this->data);
		$data = $data + $this->prefix('r.');
		
		$upd = $DB->format_db_update_values($data);
		$where = $DB->format_db_where_string($where);
		$DB->query("UPDATE $update SET $upd WHERE $where");
		
		$this->data = $save;
		
		$this->data['rid'] = $rid;
	}
	
	function clean ($data, $p='') {
		$ndata = array();
		
		$ndata['type']				= (!isset($data[$p.'type']))				? 0		: $data[$p.'type'];
		$ndata['eid']				= (!isset($data[$p.'eid']))					? 0		: $data[$p.'eid'];
		$ndata['uid']				= (!isset($data[$p.'uid']))					? 0		: $data[$p.'uid'];
		$ndata['created']			= (!isset($data[$p.'created']))				? time(): $data[$p.'created'];
		$ndata['updated']			= (!isset($data[$p.'updated']))				? 0		: $data[$p.'updated'];
		$ndata['title']				= (!isset($data[$p.'title']))				? ''	: $data[$p.'title'];
		$ndata['description']		= (!isset($data[$p.'description']))			? ''	: $data[$p.'description'];
		$ndata['author_override']	= (!isset($data[$p.'author_override']))		? ''	: $data[$p.'author_override'];
		$ndata['website_override']	= (!isset($data[$p.'website_override']))	? ''	: $data[$p.'website_override'];
		$ndata['weburl_override']	= (!isset($data[$p.'weburl_override']))		? ''	: $data[$p.'weburl_override'];
		$ndata['queue_code']		= (!isset($data[$p.'queue_code']))			? 0		: $data[$p.'queue_code'];
		$ndata['ghost']				= (!isset($data[$p.'ghost']))				? 0		: $data[$p.'ghost'];
		$ndata['update_reason']		= (!isset($data[$p.'update_reason']))		? ''	: $data[$p.'update_reason'];
		$ndata['accept_date']		= (!isset($data[$p.'accept_date']))			? 0		: $data[$p.'accept_date'];
		$ndata['update_accept_date']= (!isset($data[$p.'update_accept_date']))	? 0		: $data[$p.'update_accept_date'];
		$ndata['decision']			= (!isset($data[$p.'decision']))			? ''	: $data[$p.'decision'];
		$ndata['catwords']			= (!isset($data[$p.'catwords']))			? ''	: $data[$p.'catwords'];
		$ndata['comments']			= (!isset($data[$p.'comments']))			? 0		: $data[$p.'comments'];
		$ndata['comment_date']		= (!isset($data[$p.'comment_date']))		? 0		: $data[$p.'comment_date'];
		
		$this->data = $ndata;
	}
	
	function query_use ($item, $value=null) {
		global $STD;
		
		switch ($item) {
			case 'r_user': case 'extention': case 'module': case 'filter_single': 
			case 'filter': case 'r_user_sess': break;
			default: $STD->template->preprocess_error("resource_class: Invalid USE TAG: $item");
		}
		
		if (!in_array($item, $this->use))
			$this->use[] = $item;
		
		if ($value)
			$this->use_val[$item] = $value;
		
		if ($item == 'r_user_sess' && !in_array('r_user', $this->use))
			$this->use[] = 'r_user';
		
		if ($item == 'extention' && !$value)
			$STD->template->preprocess_error("resource_class: extention USE TAG without matching value");
		
		if ($item == 'filter' && !$value)
			$STD->template->preprocess_error("resource_class: filter USE TAG without matching value");
		
		if ($item == 'extention')
			$this->update_module($value);
	}
	
	function query_build_nolink () {
		global $CFG;
		
		$select = "r.*";
		$from = "{$CFG['db_pfx']}_resources r ";
		
		return array('select' => $select, 'from' => $from);
	}
	
	function query_build () {
		global $CFG;
		
		$select = "r.*";
		$from = "{$CFG['db_pfx']}_resources r ";
		
		if (in_array('extention', $this->use)) {
			$select .= ",e.*";
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_{$this->module['table_name']} e ON (r.eid = e.eid) ";
		}
		
		if (in_array('r_user', $this->use)) {
			$select .= $this->compiled_select('users', 'ru');
			$select .= ',rug.name_prefix ru_name_prefix,rug.name_suffix ru_name_suffix';
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_users ru ON (r.uid = ru.uid) ";
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_groups rug ON (ru.gid = rug.gid) ";
		}
		
		if (in_array('r_user_sess', $this->use)) {
			$select .= ",rus.uid rus_uid,rus.time rus_time";
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_sessions rus ON (ru.uid = rus.uid) ";
		}
		
		if (in_array('module', $this->use)) {
			$select .= ",m.*";
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_modules m ON (r.type = m.mid) ";
		}
		
		if (in_array('filter', $this->use)) {
			reset($this->use_val['filter']);
			while (list($k,$v) = each($this->use_val['filter'])) {
				if ($v > 0) {
					$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_multi fm{$k} ON (fm{$k}.rid = r.rid) ";
					$this->query_condition("fm{$k}.fid = '{$v}'");
				}
			}
		}
				   
		if (in_array('filter_single', $this->use)) {
			$select .= ",l.name l_name,l.short_name l_short_name";
			$from .= "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_multi fm ON (fm.rid = r.rid) ".
					 "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_list l ON (fm.fid = l.fid) ".
					 "LEFT OUTER JOIN {$CFG['db_pfx']}_filter_group fg ON (l.gid = fg.gid) ";
		}
		
		return array('select' => $select, 'from' => $from);
	}
	
	//------------------------
	
	function move ($newcat) {
		global $DB, $CFG, $TAG, $STD;
		
		if (empty($this->data))
			$STD->error("Attempt to move a record that hasn't been initialized");
		
		if (!preg_match('/^[0-9]+$/', $newcat))
			$newcat = $TAG->get_id_by_tag($newcat);
		
		if ($newcat == 0 || $TAG->data[$newcat]['type'] != 1)
			$STD->error("Invalid Parent Tag specified");
		
		// Set tags
		$tlist = explode(',', $this->data['tag']);
		for ($x=0; $x<sizeof($tlist); $x++) {
			if ($tlist[$x] == $this->data['ext_type'])
				$tlist[$x] = $newcat;
		}
		
		sort($tlist);
		$this->data['tag'] = @join(',', $tlist);
		$this->data['tag'] = preg_replace('/^,|,$/', '', $this->data['tag']);
		
		// Move files
		$oldcat = $this->data['ext_type'];
		if (!empty($this->data['file']) && file_exists("file/{$oldcat}/{$this->data['file']}")) {
			if (!file_exists("file/$newcat"))
				$STD->error("File Directory \"$newcat\" does not exist.");
			if (!@copy("file/{$oldcat}/{$this->data['file']}", "file/{$newcat}/{$this->data['file']}"))
				$STD->error("Could not relocate file to new directory");
			@unlink("file/{$oldcat}/{$this->data['file']}");
		}
		
		if (!empty($this->data['thumbnail']) && file_exists("file/{$oldcat}/{$this->data['thumbnail']}")) {
			if (!file_exists("thumbnail/$newcat"))
				$STD->error("Thumbnail Directory \"$newcat\" does not exist.");
			if (!@copy("file/{$oldcat}/{$this->data['thumbnail']}", "file/{$newcat}/{$this->data['thumbnail']}"))
				$STD->error("Could not relocate thumbnail to new directory");
			@unlink("file/{$oldcat}/{$this->data['file']}");
		}
		
		// Change extention
		$where = $DB->format_db_where_string(array('eid'	=>	$this->data['id']));
		$DB->query("DELETE FROM {$TAG->data[$oldcat]['table']} WHERE $where");
		
		$DB->query("INSERT INTO {$TAG->data[$newcat]['table']} () VALUES ()");
		$new_eid = $DB->get_insert_id();
		
		// Update record
		$this->data['ext_type'] = $newcat;
		$this->data['ext_id'] = $new_eid;
		
		return true;
	}
	
	function remove ($id = 0) {
		global $STD, $DB, $CFG, $TAG;
		
		if (empty($this->data))
			$STD->error("Attempt to remove a record that hasn't been initialized.");
		
		$this->update_module($this->data['type']);

		$where = $DB->format_db_where_string(array('eid'	=> $this->data['eid']));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_{$this->module['table_name']} WHERE $where");
		
		//$mod = new $this->module['class_name'];	
		$mod = $STD->modules->new_module($this->module['mid']);	
		while (list($k,$v) = each ($mod->file_restrictions)) {
			@unlink("$k/{$this->data['type']}/{$this->data[$k]}");
		}
		
		$where = $DB->format_db_where_string(array('rid'	=>	$this->data['rid']));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_resources WHERE $where");
		
		$where = $DB->format_db_where_string(array('rid'	=>	$this->data['rid'],
												   'type'	=>	1));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_comments WHERE $where");
		
		$this->data = array();
	}
	
	function create_ghost ($fields=array(), $auxdata=array()) {
		global $STD, $DB, $CFG, $TAG;
		
		if (empty($this->data))
			$STD->error("Attempt to create a ghost from a record that hasn't been initialized.");
		
		if ($this->data['ghost'] > 0)
			return $this->update_ghost($fields);
		
		$ghost = new resource;
		$ghost->_copy($this);
		
		reset($fields);
		while (list($k,$v) = each($fields))
			$ghost->data[$k] = $v;
		
		$ghost->data['eid'] = 0;
		$ghost->data['queue_code'] = 5;
		$ghost->insert();
		
		$this->data['ghost'] = $ghost->data['rid'];
		$this->data['queue_code'] = 2;
		$this->update();
		
		return $ghost;
	}
	
	function update_ghost ($fields=array()) {
		global $STD, $DB, $CFG;
		
		if (empty($this->data))
			$STD->error("Attempt to update the ghost of a record that hasn't been initialized.");
		
		$ghost = new resource;
		$ghost->_copy($this);
		if (!$ghost->get($this->data['ghost']))
			$STD->error("Attempt to update a ghost that doesn't exist");
		
		unset($fields['rid']);
		unset($fields['eid']);
		unset($fields['ghost']);
		unset($fields['queue_code']);
		
		reset($fields);
		while (list($k,$v) = each($fields))
			$ghost->data[$k] = $v;
		
		$ghost->update();
		
		return $ghost;
	}
	
	function remove_ghost () {
		global $STD, $DB, $CFG, $TAG;
		
		if (empty($this->data))
			$STD->error("Attempt to remove the ghost of a record that hasn't been initialized.");
		
		$ghost = new resource;
		$ghost->_copy($this);
		if (!$ghost->get($this->data['ghost']))
			$STD->error("Attempt to remove a ghost that doesn't exist");
		
		$this->update_module($this->data['type']);
		
		$mod = $STD->modules->new_module($this->module['mid']);
		while (list($k,$v) = each ($mod->file_restrictions)) {
			if ($this->data[$k] == $ghost->data[$x])
				unset($ghost->data[$k]);
		}
		
		$where = $DB->format_db_where_string(array('rid'	=> $ghost->data['rid']));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_filter_multi WHERE $where");
		
		$ghost->remove();
		
		$this->data['ghost'] = 0;
		$this->data['queue_code'] = 4;
		$this->update();
	}
	
	function apply_ghost () {
		global $STD, $DB, $CFG, $TAG;
		
		if (empty($this->data))
			$STD->error("Attempt to apply the ghost of a record that hasn't been initialized.");
		
		$ghost = new resource;
		$ghost->_copy($this);
		
		if (!$ghost->get($this->data['ghost']))
			$STD->error("Attempt to apply a ghost that doesn't exist");
		
		$this->update_module($this->data['type']);
		
		$rid = $this->data['rid'];
		$eid = $this->data['eid'];
		
		$old_update = $this->data['updated'];
		$old_files = array();
		
		$mod = $STD->modules->new_module($this->module['mid']);
		while (list($k,$v) = each($mod->file_restrictions)) {
			$old_files[$k] = $this->data[$k];
		}
		
		$where = $DB->format_db_where_string(array('rid'	=> $this->data['rid']));
		$DB->query("DELETE FROM {$CFG['db_pfx']}_filter_multi WHERE $where");
		
		$this->data = $ghost->data;
		$this->data['rid'] = $rid;
		$this->data['eid'] = $eid;
		
		$where = $DB->format_db_where_string(array('rid'	=> $ghost->data['rid']));
		$upd = $DB->format_db_update_values(array('rid'		=> $this->data['rid']));
		$DB->query("UPDATE {$CFG['db_pfx']}_filter_multi SET $upd WHERE $where");
		
		while (list($k,$v) = each ($old_files)) {
			($this->data[$k] == $v)
				? $ghost->data[$k] = ''
				: $ghost->data[$k] = $v;
		}

		$ghost->remove();
		
		$this->data['ghost'] = 0;
		$this->data['queue_code'] = 0;
		$this->update();
		
		// Apply Version Update if necessary
	/*	if ($old_update != $this->data['updated']) {
			$ins = $DB->format_db_values(array('rid'	=> $this->data['rid'],
											   'change'	=> $this->data['update_reason'],
											   'date'	=> $this->data['updated']));
			$DB->query("INSERT INTO {$CFG['db_pfx']}_version ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
		}*/
	}
	
	function like_search_all ($string) {
		global $STD, $DB, $CFG;
		
		if (!empty($string)) {
			$string = preg_replace("/\\\\?'/", "\\'", $string);
			$this->query_condition("(r.title LIKE '%$string%' OR r.description LIKE '%$string%' OR r.catwords LIKE '%$string%')");
		}
		
		// Build Query
		$qp = $this->query_build();
		
		$this->cquery = $DB->query("SELECT {$qp['select']}, 1 as relevance FROM {$qp['from']} ".
								   "WHERE 1=1 {$this->condition} {$this->order} {$this->limit}");
		
		return $this->cquery;
	}
	
	function like_search_count ($string) {
		global $STD, $DB, $CFG;
		
		if (!empty($string)) {
			$string = preg_replace("/\\\\?'/", "\\'", $string);
			$this->query_condition("(r.title LIKE '%$string%' OR r.description LIKE '%$string%' OR r.catwords LIKE '%$string%')");
		}
		
		// Build Query
		$qp = $this->query_build();
		
		$this->cquery = $DB->query("SELECT COUNT(*) as cnt, 1 as relevance FROM {$qp['from']} ".
								   "WHERE 1=1 {$this->condition} {$this->order} {$this->limit}");
		
		$count = $DB->fetch_row();
		return $count;
	}
	
	function full_text_search_all ($string, $boolean) {
		global $STD, $DB, $CFG;
		
		// Build Query
		$qp = $this->query_build();
		
		if ($boolean) {
			$boolean = "IN BOOLEAN MODE";
			$this->order = "ORDER BY relevance DESC";
		}
		else
			$boolean = "";
		
		$string = preg_replace("/\\\\?'/", "\\'", $string);
		
		$this->cquery = $DB->query("SELECT {$qp['select']},MATCH (title,description,catwords) AGAINST ('$string' $boolean) AS relevance ".
								   "FROM {$qp['from']} ".
								   "WHERE MATCH (title,description,catwords) AGAINST ('$string' $boolean) ".
								   "{$this->condition} {$this->order} {$this->limit}");
		
		return $this->cquery;
	}
	
	function full_text_search_count ($string, $boolean) {
		global $STD, $DB, $CFG;
		
		// Build Query
		$qp = $this->query_build();
		
		if ($boolean) {
			$boolean = "IN BOOLEAN MODE";
			$this->order = "ORDER BY relevance DESC";
		}
		else
			$boolean = "";
		
		$string = trim(preg_replace("/\\\\?'/", "\\'", $string));
		
		$this->cquery = $DB->query("SELECT COUNT(*) AS cnt, MAX(MATCH (title,description,catwords) AGAINST ('$string' $boolean)) as relevance ".
								   "FROM {$qp['from']} ".
								   "WHERE MATCH (title,description,catwords) AGAINST ('$string' $boolean) ".
								   "{$this->condition}");

		$count = $DB->fetch_row();
		return $count;
	}
	
	function format_username ($row, $prefix='') {
		global $STD;
		
		$user = '<b>N/A</b>';
		
		if (!empty($row[$prefix.'username']) && !empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0)
			$user = $row[$prefix.'username'];
		
		if (!empty($row['author_override']))
			$user = '<i>'.$row['author_override'].'</i>';
		
		if (!empty($row[$prefix.'uid']) && $row[$prefix.'uid'] > 0 && $STD->user['acp_users'])
			$user = "<a href='".$STD->encode_url($_SERVER['PHP_SELF'],"act=ucp&param=02&u={$row[$prefix.'uid']}")."'>$user</a>";
		
		return $user;
	}
	
	function format_username_simple ($row) {
		
		$user = '<b>N/A</b>';
		
		if (!empty($row['username']) && !empty($row['uid']) && $row['uid'] > 0)
			$user = $row['username'];
		
		if (!empty($row['author_override']))
			$user = $row['author_override'];
		
		return $user;
	}
}

?>
