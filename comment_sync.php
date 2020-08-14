<?php
//------------------------------------------------------------------
// Taloncrossing Submission Management System 1.0
//------------------------------------------------
// Copyright 2005 Justin Aquadro
//
// comment_sync.php --
// Does a full comment recount for every resource
//------------------------------------------------------------------

error_reporting(E_ALL);
set_time_limit(0);
set_magic_quotes_runtime(0);

define('ROOT_PATH', './');

//------------------------------------------------

require ROOT_PATH.'settings.php';

require ROOT_PATH.'lib/db_drivers/'.$CFG['db_driver'].'.php';

$DB = new db_driver;
$DB->connect();

echo "Starting synchronization...<br /><br />";

$cq = $DB->query("SELECT rid FROM {$CFG['db_pfx']}_resources");
while ($row = $DB->fetch_row($cq)) {
    $cc = $DB->query("SELECT COUNT(*) AS cnt, MAX(date) as date FROM {$CFG['db_pfx']}_comments WHERE type = 1 AND rid = {$row['rid']}");
    $crow = $DB->fetch_row($cc);
    
    if (empty($crow['date'])) {
        $crow['date'] = 0;
    }
    
    echo "{$row['rid']}: {$crow['cnt']} @ {$crow['date']}<br />";
    
    $DB->query("UPDATE {$CFG['db_pfx']}_resources SET comments = {$crow['cnt']}, comment_date = {$crow['date']} WHERE rid = {$row['rid']}");
}

echo "<br />Synchronization Complete.";
