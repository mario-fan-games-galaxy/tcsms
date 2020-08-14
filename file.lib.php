<?php

//----------------------------------------//
// File Handler Library v.1.1.0           //
//----------------------------------------//
// Included Classes:                      //
//   -- fileObject                        //
//      - formatSize()                    //
//      - rwxPerms()                      //
//      - shortHandPerms()                //
//      - formatTime()                    //
//   -- fileList                          //
//      - addFile()                       //
//      - removeFile()                    //
//      - clear()                         //
//      - exists()                        //
//      - getFile()                       //
//      - getPointer()                    //
//      - resetPointer()                  //
//      - reverseOrder()                  //
//      - listSort()                      //
//      - merge()                         //
//      - selectRandom()                  //
//      - randomize()                     //
//      - search()                        //
//      - filter()                        //
//   -- directoryObject                   //
//      - traverse()                      //
//      - read()                          //
//      - deleteFile()                    //
//      - uploadFile()                    //
//----------------------------------------//
// Copyright © 2003 Justin Aquadro        //
//----------------------------------------//


class fileObject
{
    public $name;
    public $path;
    public $ext;
    public $size;
    public $lastATime;
    public $lastMTime;
    public $type;
    public $owner;
    public $group;
    public $perms;

    public function fileObject($file)
    {
        $this->path = realpath($file);
        $this->name = basename($file);
        $this->ext = array_pop(explode('.', $this->name, 2));
        if (is_dir($this->path)) {
            $this->ext = null;
        }
        $this->size = filesize($file);
        $this->lastATime = fileatime($file);
        $this->lastMTime = filemtime($file);
        $this->type = filetype($file);
        $this->owner = fileowner($file);
        $this->group = filegroup($file);
        $this->perms = fileperms($file);
    }

    public function formatSize()
    {
        if ($this->size < 1024) {
            return $this->size . " bytes";
        } elseif ($this->size < 1048576) {
            return round($this->size / 1024, 2) . " KB";
        } elseif ($this->size < 1072741824) {
            return round($this->size / 1048576, 2) . " MB";
        } else {
            return round($this->size / 1072741824, 2) . " GB";
        }
    }

    public function rwxPerms()
    {
        $sP = '';

        if ($this->perms & 0x1000) {     // FIFO pipe
            $sP = 'p';
        } elseif ($this->perms & 0x2000) { // Character special
            $sP = 'c';
        } elseif ($this->perms & 0x4000) { // Directory
            $sP = 'd';
        } elseif ($this->perms & 0x6000) { // Block special
            $sP = 'b';
        } elseif ($this->perms & 0x8000) { // Regular
            $sP = '-';
        } elseif ($this->perms & 0xA000) { // Symbolic Link
            $sP = 'l';
        } elseif ($this->perms & 0xC000) { // Socket
            $sP = 's';
        } else {                       // UNKNOWN
            $sP = 'u';
        }

        // owner
        $sP .= (($this->perms & 0x0100) ? 'r' : '-') .
                  (($this->perms & 0x0080) ? 'w' : '-') .
                 (($this->perms & 0x0040) ? (($this->perms & 0x0800) ? 's' : 'x') :
               (($this->perms & 0x0800) ? 'S' : '-'));

        // group
        $sP .= (($this->perms & 0x0020) ? 'r' : '-') .
                 (($this->perms & 0x0010) ? 'w' : '-') .
                 (($this->perms & 0x0008) ? (($this->perms & 0x0400) ? 's' : 'x') :
               (($this->perms & 0x0400) ? 'S' : '-'));

        // world
        $sP .= (($this->perms & 0x0004) ? 'r' : '-') .
                 (($this->perms & 0x0002) ? 'w' : '-') .
                 (($this->perms & 0x0001) ? (($this->perms & 0x0200) ? 't' : 'x') :
               (($this->perms & 0x0200) ? 'T' : '-'));

        return $sP;
    }

    public function shortHandPerms()
    {
        $octalperms = sprintf("%o", $this->perms);

        return (substr($octalperms, -3));
    }

    public function formatTime($type, $time_format="F d, Y, H:i:s")
    {
        $time = (strtolower($type) == 'a') ? $this->lastATime : $this->lastMTime;

        return date($time_format, $time);
    }

    // Comparison Functions for usort comparisons
    public function cmpNames($a, $b)
    {
        $a1 = $a->name;
        $b1 = $b->name;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpNamesNatural($a, $b)
    {
        $a1 = $a->name;
        $b1 = $b->name;

        return strnatcasecmp($a1, $b1);
    }

    public function cmpPaths($a, $b)
    {
        $a1 = $a->path;
        $b1 = $b->path;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpPathsNatural($a, $b)
    {
        $a1 = $a->path;
        $b1 = $b->path;

        return strnatcasecmp($a1, $b1);
    }

    public function cmpSize($a, $b)
    {
        $a1 = $a->size;
        $b1 = $b->size;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpExt($a, $b)
    {
        $a1 = $a->ext;
        $b1 = $b->ext;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpLastA($a, $b)
    {
        $a1 = $a->lastATime;
        $b1 = $b->lastATime;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpLastM($a, $b)
    {
        $a1 = $a->lastMTime;
        $b1 = $b->lastMTime;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpType($a, $b)
    {
        $a1 = $a->type;
        $b1 = $b->type;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }

    public function cmpPerms($a, $b)
    {
        $a1 = $a->perms;
        $b1 = $b->perms;

        if ($a1 == $b1) {
            return 0;
        } else {
            return ($a1 > $b1) ? +1 : -1;
        }
    }
}

class fileList
{
    public $files        = array();
    public $fileCount    = 0;
    public $internal_ptr = 0;

    public function fileList()
    {
        $this->files = array();
        $this->fileCount = 0;
        $this->internal_ptr = 0;
    }

    public function addFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        
        $this->fileCount++;
        $this->files[] = new fileObject($file);
        return true;
    }

    public function removeFile($file)
    {
        $this->fileCount--;
        $temparr = array();

        for ($x=0; $x<sizeof($this->files); $x++) {
            $temparr[$x] = $this->files[$x]->path;
        }

        $key = array_search($file->path, $temparr);

        if (($key !== false) && ($key !== null)) {
            $this->files = array_merge(
                array_slice($this->files, 0, $key),
                array_slice($this->files, $key+1, sizeof($this->files)-1)
            );
            return true;
        } else {
            return false;
        }
    }

    public function clear()
    {
        $this->fileCount = 0;
        $this->files = array();
        
        return true;
    }
    
    public function exists($index)
    {
        if (sizeof($this->files[$index]) > 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getFile($ptr=null)
    {
        if ($ptr !== null) {
            if ($this->exists($ptr)) {
                return $this->files[$ptr];
            } else {
                return false;
            }
        } else {
            if ($this->internal_ptr < sizeof($this->files)) {
                $this->internal_ptr++;
                return $this->files[$this->internal_ptr-1];
            } else {
                return false;
            }
        }
    }
    
    public function getPointer()
    {
        return $this->internal_ptr;
    }
    
    public function resetPointer()
    {
        $this->internal_ptr = 0;
        return true;
    }

    public function reverseOrder()
    {
        $this->files = array_reverse($this->files);
        
        return true;
    }

    public function listSort($criteria, $order="ASCENDING")
    {
        switch ($criteria) {
        case "name":
            usort($this->files, array("fileObject", "cmpNames"));
        break;
        case "name_natural":
            usort($this->files, array("fileObject", "cmpNamesNatural"));
        break;
        case "path":
            usort($this->files, array("fileObject", "cmpPaths"));
        break;
        case "path_natural":
            usort($this->files, array("fileObject", "cmpPathsNatural"));
        break;
        case "size":
            usort($this->files, array("fileObject", "cmpSize"));
        break;
        case "ext":
            usort($this->files, array("fileObject", "cmpExt"));
        break;
        case "lastATime":
            usort($this->files, array("fileObject", "cmpLastA"));
        break;
        case "lastMTime":
            usort($this->files, array("fileObject", "cmpLastM"));
        break;
        case "type":
            usort($this->files, array("fileObject", "cmpType"));
        break;
        case "perms":
            usort($this->files, array("fileObject", "cmpPerms"));
        break;
        default:
            return false;
        break;
        }

        if ($order == "DESCENDING") {
            $this->reverseOrder();
        }
        
        return true;
    }

    public function merge($listb, $order="UNSORTED")
    {
        if (!is_array($listb->files)) {
            return false;
        }
        
        $this->files = array_merge($this->files, $listb->files);
        $this->count += $listb->count;

        if ($order == "SORTED") {
            $this->listSort("name");
        }
        
        return true;
    }

    public function selectRandom()
    {
        $key = rand(0, sizeof($this->files)-1);

        return $this->files[$key];
    }

    public function randomize()
    {
        shuffle($this->files);
        
        return true;
    }
    
    public function search($type, $identifier)
    {
        for ($x=0; $x<sizeof($this->files); $x++) {
            switch ($type) {
            case 'name':
                if ($this->files[$x]->name == $identifier) {
                    return $this->files[$x];
                }
                break;
            case 'path':
                if ($this->files[$x]->path == $identifier) {
                    return $this->files[$x];
                }
                break;
            }
        }
        return false;
    }

    public function filter($method, $key, $key2, $rule="EXCLUDE")
    {
        for ($x=0; $x<sizeof($this->files); $x++) {
            switch ($method) {
            case "name":
                if ((($key2 != '*') && ($key == $this->files[$x]->name)) ||
                (($key2 == '*') && (strpos($this->files[$x]->name, $key) !== false))) {
                    if ($rule == "EXCLUDE") {
                        $this->removeFile($this->files[$x]);
                        $x--;
                    }
                } elseif ($rule == "INCLUDE") {
                    $this->removeFile($this->files[$x]);
                    $x--;
                }
            break;
            case "path":
                if ((($key2 != '*') && ($key == $this->files[$x]->path)) ||
                (($key2 == '*') && (strpos($this->files[$x]->path, $key) !== false))) {
                    if ($rule == "EXCLUDE") {
                        $this->removeFile($this->files[$x]);
                        $x--;
                    }
                } elseif ($rule == "INCLUDE") {
                    $this->removeFile($this->files[$x]);
                    $x--;
                }
            break;
            case "ext":
                if ((($key2 != '*') && ($key == $this->files[$x]->ext)) ||
                (($key2 == '*') && (strpos($this->files[$x]->ext, $key) !== false))) {
                    if ($rule == "EXCLUDE") {
                        $this->removeFile($this->files[$x]);
                        $x--;
                    }
                } elseif ($rule == "INCLUDE") {
                    $this->removeFile($this->files[$x]);
                    $x--;
                }
            break;
            case "size":
                if (($this->files[$x]->size >= $key) && ($this->files[$x]->size <= $key2)) {
                    if ($rule == "EXCLUDE") {
                        $this->removeFile($this->files[$x]);
                        $x--;
                    }
                } elseif ($rule == "INCLUDE") {
                    $this->removeFile($this->files[$x]);
                    $x--;
                }
            break;
            case "type":
                if ($key == $this->files[$x]->type) {
                    if ($rule == "EXCLUDE") {
                        $this->removeFile($this->files[$x]);
                        $x--;
                    }
                } elseif ($rule == "INCLUDE") {
                    $this->removeFile($this->files[$x]);
                    $x--;
                }
            break;
            default:
                return false;
            break;
            }
        }
        return true;
    }
}

class directoryObject
{
    public $originalDir;
    public $currentDir;
    public $rootDir;
    public $extendedPath;
    public $recursiveLevel;

    public function directoryObject($root=null)
    {
        $this->currentDir = getcwd();
        $this->originalDir = $this->currentDir;
        if ($root === null) {
            $this->rootDir = $this->currentDir;
        } else {
            $this->rootDir = $root;
        }
        $this->extendedPath = '';
        $this->recursiveLevel = 0;
    }

    public function traverse($dir=null)
    {
        $fileList = new fileList;
        if ($dir === null) {
            if (!$dir = @opendir($this->rootDir)) {
                return false;
            }
        } elseif ($this->recursiveLevel == 0) {
            if (!$dir = @opendir($dir)) {
                return false;
            }
        }

        while (($file = readdir($dir)) != false) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($this->rootDir.$this->extendedPath.'/'.$file)) {
                    $previousPath = $this->extendedPath;
                    $previousDir = $this->currentDir;
                    $this->extendedPath .= "/$file";
                    $this->currentDir = $this->rootDir.$this->extendedPath;
                    chdir($this->rootDir.$this->extendedPath);
                    $this->recursiveLevel++;

                    $fileList->addFile($this->rootDir.$this->extendedPath);

                    $newdir = opendir($this->rootDir.$this->extendedPath);
                    $fileList2 = $this->traverse($newdir);
                    closedir($newdir);

                    $fileList->merge($fileList2);

                    chdir($this->previousDir);
                    $this->extendedPath = $previousPath;
                    $this->currentDir = $previousDir;
                    $this->recursiveLevel--;
                } else {
                    $fileList->addFile($this->rootDir.$this->extendedPath.'/'.$file);
                }
            }
        }
        $this->currentDir = $this->originalDir;
        chdir($this->currentDir);

        return $fileList;
    }

    public function read($dir=null)
    {
        $fileList = new fileList;
        if ($dir === null) {
            if (!$dir = @opendir($this->rootDir)) {
                return false;
            }
        }

        while (($file = readdir($dir)) != false) {
            if (($file != '.') && ($file != '..')) {
                if (!$fileList->addFile($this->rootDir.'/'.$file)) {
                    return false;
                }
            }
        }

        return $fileList;
    }

    public function deleteFile($file)
    {
        if (is_dir($file)) {
            $dir = opendir($file);
            while (($newfile = readdir($dir)) != false) {
                if (($newfile != '.') && ($newfile != '..')) {
                    if (!$this->deleteFile($file)) {
                        return false;
                    }
                }
            }
            closedir($dir);
            rmdir($file);
        } elseif (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }
    
    public function uploadFile($postName)
    {
        $filename = basename($_FILES[$postName]['name']);
        $filesize = $_FILES[$postName]['size'];
        
        if ($filesize <= 0) {
            return false;
        } elseif (file_exists($this->rootDir.'/'.$filename)) {
            return false;
        } elseif (!@move_uploaded_file($_FILES[$postName]['tmp_name'], $this->rootDir.'/'.$filename)) {
            return false;
        } else {
            return true;
        }
    }
}
