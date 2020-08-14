<?php

set_time_limit(0);

require "./lib/std.php";
require "./file.lib.php";

$STD = new std;

$dbc = mysql_connect("localhost", "mfgg", "gYh7r78$") or mysql_error();
mysql_select_db("mfgg_mainsite", $dbc) or mysql_error($dbc);

$do = new directoryObject($_SERVER['DOCUMENT_ROOT'] . "/file/2");
$flist = $do->traverse();

$linked = 0;
$unlinked = 0;

while ($obj = $flist->getFile()) {
    $query = mysql_query("SELECT r.*, g.* FROM tsms_resources r LEFT JOIN tsms_res_games g ON (r.eid = g.eid) WHERE r.type=2 AND g.file = '{$obj->name}'", $dbc);
    if (!$query) {
        exit("Error: " . mysql_error());
    }
    
    if (mysql_num_rows($query) > 0) {
        echo $obj->name . "<br>";
        $linked++;
    } else {
        echo "<font color='red'>{$obj->name}</font><br>";
        $unlinked++;
        
        $do->deleteFile($obj->path);
    }
}

echo "<br>Linked: $linked<br>Unlinked: $unlinked<br>";

/*



$query = mysql_query("SELECT r.*, g.* FROM tsms_resources r LEFT JOIN tsms_res_games g ON (r.eid = g.eid) WHERE r.type=2", $dbc);
if (!$query)
    exit ("Error: " . mysql_error());

echo "<pre>";
while ($row = mysql_fetch_assoc($query))
{
    $q2 = mysql_query("SELECT COUNT(*) AS cnt, IFNULL(SUM(score), 0) AS scr FROM tsms_res_reviews WHERE gid = {$row['rid']}", $dbc);
    $r2 = mysql_fetch_assoc($q2);

    mysql_query("UPDATE tsms_res_games SET num_revs = '{$r2['cnt']}', rev_score = '{$r2['scr']}' WHERE eid = '{$row['eid']}'", $dbc) or exit(mysql_error());
    echo "{$row['eid']}, {$r2['cnt']}, {$r2['scr']}\n";
}
echo "</pre>";*/
