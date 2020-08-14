<?php
//------------------------------------------------------------------
// Penguinia Content Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// tag.php --
// Tagging and Category Lookup
//------------------------------------------------------------------

//----------------------------------------------------------------------------------------
// tag : Tagging and Category Class
// Stores the category table and relationships
//----------------------------------------------------------------------------------------
// Depends on:	configuration ($CFG)
//				db_driver ($DB)
//				std ($STD)
//----------------------------------------------------------------------------------------

class tag
{
    
    //var $data		= array();
    public $nodelist = array();
    public $leaflist = array();
    public $nodedef = array();
    public $tagdef = array();
    public $flatnode = array();
    
    public $tag_lookup	= array();
    public $parent_cache	= array();
    
    public function load_table()
    {
        global $CFG, $DB, $STD;
        
        //	$DB->query("SELECT * FROM {$CFG['tables']['cat']}");
        //
        //	while ($row = $DB->fetch_row()) {
        //		$this->data[$row['id']] = $row;
        //		$this->tag_lookup[$row['tagname']] = $row['id'];
        //	}
        
        require ROOT_PATH.'category.php';
        
        $this->nodelist = $NODE_LIST;
        $this->leaflist = $LEAF_LIST;
        $this->nodedef = $NODE_DEF;
        $this->tagdef = $TAG_DEF;
        $this->flatnode = $NODE_FLAT;
    }
    
    public function tag_exists($tagid)
    {
        if (is_numeric($tagid) && empty($this->tagdef[(int)$tagid])) {
            return false;
        } elseif (is_numeric($tagid)) {
            return true;
        } else {
            reset($this->tagdef);
            while (list(, $v) = each($this->tagdef)) {
                if ($v[0] == $tagid) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }
    
    //	function has_ancestor ($parent, $tagid) {
//
    //		//$tid = $this->get_id_by_tag($tag);
//
    //		if ($tid == $parent)
    //			return true;
    //		if ($tid == 0)
    //			return false;
//
    //		return $this->has_ancestor($parent, $this->data[$tid]['parent']);
    //	}
    
    //	function get_row ($id) {
//
    //		if (is_array($id)) {
    //			$ret = array();
    //			while (list(,$v) = each($id)) {
    //				$ret[$v] = $this->data[$v];
    //			}
    //		} else
    //			$ret = $this->data[$id];
//
    //		return $ret;
    //	}
    
    // MIXED get_id_by_tag (MIXED tag)
    //
    // $tag: Tag Name, Tag ID, or Array of mixed Tag Names and IDs
    // Takes a tag name and returns its equivilient ID.  If the tag name provided is allready a valid ID,
    // the ID is returned.  If the tag name is an array, an array of corresponding tag IDs is returned.  If
    // a tag name within an array is not valid, it is removed.  Otherwise any invalid tag name passed is
    // returned as 0.
    
    public function get_id_by_tag($tag)
    {
        if (is_numeric($tag) && $this->tag_exists($tag)) {
            return $tag;
        }
        
        if (is_array($tag)) {
            reset($tag);
            while (list($k, $v) = each($tag)) {
                $tag[$k] = $this->get_id_by_tag($v);
                
                if ($tag[$k] == 0) {
                    unset($tag[$k]);
                }
            }
            
            return $tag;
        } else {
            reset($this->tagdef);
            while (list($k, $v) = each($this->tagdef)) {
                if ($tag == $v[0]) {
                    return $k;
                }
            }
            
            return 0;
        }
    }
    
    public function get_parent_trace($id)
    {
        $trace = array();
        $nodelist = $this->nodelist;
        $this->get_parent_trace_rec($id, $nodelist, $trace);
        
        return $trace;
    }
    
    public function get_parent_trace_rec($id, $nodelist, &$trace)
    {
        reset($nodelist);
        while (list($k, $v) = each($nodelist)) {
            if ($k == $this->tagdef[$id][0]) {
                array_push($trace, $k);
                return true;
            } else {
                array_push($trace, $k);
                if ($this->get_parent_trace_rec($id, $v, $trace)) {
                    return true;
                } else {
                    array_pop($trace);
                }
            }
        }
        
        return false;
    }
                
    
    // ARRAY get_node_children (INT id [, ARRAY nodelist])
    //
    // Searches the Node List Hierarchy for the children of id.  If id is found, the array contained by id
    // will be returned.  Otherwise if id is not found, or id has no children, an empty array will be returned.
    
    public function get_node_children($id, $nodelist = null)
    {
        if ($nodelist === null) {
            $nodelist = $this->nodelist;
        }
        
        reset($nodelist);
        while (list($k, $v) = each($nodelist)) {
            if ($k == $this->tagdef[$id][0]) {
                return $v;
            } elseif (sizeof($v) > 0) {
                $dslist = $this->get_node_children($id, $v);
                if (sizeof($dslist > 0)) {
                    return $dslist;
                }
            } else {
                return array();
            }
        }
        
        return array();
    }
    
    //	function get_by_parent ($id) {
//
    //		if (empty($this->parent_cache)) {
    //			reset($this->data);
    //			while (list($key,$row) = each($this->data)) {
    //				$this->parent_cache[$row['parent']][] = $row['id'];
    //			}
    //		}

    //		if (empty($this->parent_cache[$id]))
    //			return array();
//
    //		return $this->get_row($this->parent_cache[$id]);
    //	}
    
    //	function get_root_tag ($tags) {
//
    //		reset($tags);
    //		while (list(,$v) = each($tags)) {
    //			if (!empty($this->data[$v]) && $this->data[$v]['type'] == 1)
    //				return $this->data[$v];
    //		}
//
    //		return false;
    //	}
    
    public function merge($list1, $list2=array())
    {
        if (!is_array($list1)) {
            $list1 = explode(',', $list1);
        }
        if (!is_array($list2)) {
            $list2 = explode(',', $list2);
        }
        
        $mlist = array_merge($list1, $list2);
        
        for ($x=0; $x<sizeof($mlist); $x++) {
            if (!is_numeric($mlist[$x])) {
                $mlist[$x] = $this->get_id_by_tag($mlist[$x]);
            }
        }
        
        $mlist = array_unique($mlist);
        sort($mlist);
        
        if (in_array(0, $mlist)) {
            array_shift($mlist);
        }
        
        $flist = @join(',', $mlist);
        $flist = preg_replace('/^,|,$/', '', $flist);
        
        return $flist;
    }
}
