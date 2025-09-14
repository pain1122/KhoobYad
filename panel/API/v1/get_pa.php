<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");


$select_cat = "SELECT * FROM `tag_meta` 
INNER join `tag` ON tag_meta.tag_id = tag.tag_id 
WHERE `tag_meta`.parent = '{$_GET['id']}'
GROUP BY `tag`.`tag_id`;";

$tags = base::fetcharray($select_cat);
$json = json_encode($tags);
if(strlen($json) > 0)
echo $json;
else
echo "{[]}";
