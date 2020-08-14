<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// module.php --
// Module interface and control
//------------------------------------------------------------------

//require_once ROOT_PATH.'lib/std.php';

class module_record extends table_frame
{
    public $module_set		= array();
    public $module_set_n	= array();
    public $initialized	= 0;
    
    public function get($mid)
    {
        global $CFG, $DB, $STD;
        
        // Build Query
        $qparts = $this->query_build();

        $where = $DB->format_db_where_string(array('m.mid' => $mid));
        $DB->query("SELECT {$qparts['select']} FROM {$qparts['from']} WHERE $where {$this->condition}");
        $this->data = $DB->fetch_row();
        
        return $this->data;
    }
    
    public function insert()
    {
        global $CFG, $DB;
        
        $this->clean($this->data);
            
        $ins = $DB->format_db_values($this->data);
        $DB->query("INSERT INTO {$CFG['db_pfx']}_modules ({$ins['FIELDS']}) VALUES ({$ins['VALUES']})");
        
        $this->data['mid'] = $DB->get_insert_id();
    }
    
    public function remove($id = 0)
    {
        global $CFG, $DB, $STD;
        
        if (!$id && !empty($this->data['mid'])) {
            $id = $this->data['mid'];
        }
        if (!$id) {
            return false;
        }
        
        $where = $DB->format_db_where_string(array('mid' => $id));
        $DB->query("DELETE FROM {$CFG['db_pfx']}_modules WHERE $where");
    }
    
    public function update()
    {
        global $CFG, $DB, $STD;
        
        if (empty($this->data) || empty($this->data['mid'])) {
            return false;
        }
            
        $mid = $this->data['mid'];
        $this->clean($this->data);
        
        $upd = $DB->format_db_update_values($this->data);
        $where = $DB->format_db_where_string(array('mid'	=> $mid));
        $DB->query("UPDATE {$CFG['db_pfx']}_modules SET $upd WHERE $where");
        
        $this->data['mid'] = $mid;
    }
    
    public function clean($data, $p='')
    {
        $ndata = array();
        
        $ndata['keyword']		= (!isset($data[$p.'keyword']))			? ''	: $data[$p.'keyword'];
        $ndata['module_name']	= (!isset($data[$p.'module_name']))		? ''	: $data[$p.'module_name'];
        $ndata['class_name']	= (!isset($data[$p.'class_name']))		? ''	: $data[$p.'class_name'];
        $ndata['full_name']		= (!isset($data[$p.'full_name']))		? ''	: $data[$p.'full_name'];
        $ndata['table_name']	= (!isset($data[$p.'table_name']))		? ''	: $data[$p.'table_name'];
        $ndata['module_file']	= (!isset($data[$p.'module_file']))		? ''	: $data[$p.'module_file'];
        $ndata['template']		= (!isset($data[$p.'template']))		? ''	: $data[$p.'template'];
        $ndata['num_decisions']	= (!isset($data[$p.'num_decisions']))	? 0		: $data[$p.'num_decisions'];
        $ndata['proc_order']	= (!isset($data[$p.'proc_order']))		? 0		: $data[$p.'proc_order'];
        $ndata['custom_update']	= (!isset($data[$p.'custom_update']))	? 0		: $data[$p.'custom_update'];
        $ndata['hidden']		= (!isset($data[$p.'hidden']))			? 0		: $data[$p.'hidden'];
        $ndata['children']		= (!isset($data[$p.'children']))		? ''	: $data[$p.'children'];
        $ndata['ext_files']		= (!isset($data[$p.'ext_files']))		? 0		: $data[$p.'ext_files'];
        $ndata['news_show']		= (!isset($data[$p.'news_show']))		? 0		: $data[$p.'news_show'];
        $ndata['news_show_collapsed']	= (!isset($data[$p.'news_show_collapsed']))	? 0		: $data[$p.'news_show_collapsed'];
        $ndata['news_upd']		= (!isset($data[$p.'news_upd']))		? 0		: $data[$p.'news_upd'];
        $ndata['news_upd_collapsed']	= (!isset($data[$p.'news_upd_collapsed']))	? 0		: $data[$p.'news_upd_collapsed'];
        
        $this->data = $ndata;
    }
    
    public function query_build()
    {
        global $CFG;
        
        $select = "m.*";
        $from = "{$CFG['db_pfx']}_modules m ";
        
        return array('select' => $select, 'from' => $from);
    }
    
    //--------------------------------------------
    
    public function load_module_list()
    {
        global $STD;
        
        $num_modules = 0;
        
        $this->getAll();
        while ($this->nextItem()) {
            $id = $this->data['mid'];
            $name = $this->data['table_name'];
            $this->data['children'] = explode(',', $this->data['children']);
            
            $this->module_set[$id] = $this->data;
            $this->module_set_n[$name] =& $this->module_set[$id];
            $num_modules++;
        }
        
        $this->initialized = 1;
        
        return $num_modules;
    }
    
    public function get_module($id)
    {
        global $STD;
        
        if (!$this->initialized) {
            $this->load_module_list();
        }
        
        if (is_numeric($id)) {
            if (isset($this->module_set[$id])) {
                return $this->module_set[$id];
            } else {
                return null;
            }
        }
        
        if (isset($this->module_set_n[$id])) {
            return $this->module_set_n[$id];
        }
        
        return null;
    }
    
    public function new_module($id)
    {
        global $STD;
        
        $mod = $this->get_module($id);
        if (!$mod) {
            return false;
        }
        
        require_once ROOT_PATH.'component/modules/'.$mod['module_file'];
        
        return new $mod['class_name'];
    }
    
    public function bound_child($mid, $id)
    {
        global $STD;
        
        $mod = $this->get_module($mid);
        
        if (!is_numeric($id)) {
            $cmod = $this->get_module($id);
            $id = $cmod['mid'];
        }
        
        if (in_array($id, $mod['children'])) {
            return true;
        }
        
        return false;
    }
    
    // Determines if $mid has a parent module $id
    public function bound_parent($mid, $id)
    {
        global $STD;
        
        $mod = $this->get_module($id);
        
        if (!is_numeric($id)) {
            $cmod = $this->get_module($mid);
            $mid = $cmod['mid'];
        }
        
        if (in_array($mid, $mod['children'])) {
            return true;
        }
        
        return false;
    }
    
    public function parent_id($mid)
    {
        global $STD;
        
        if (!$this->initialized) {
            $this->load_module_list();
        }
        
        reset($this->module_set);
        while (list($k, $v) = each($this->module_set)) {
            if (in_array($mid, $v['children'])) {
                return $k;
            }
        }
        
        return 0;
    }
}
