<?php

include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");


$select_cat = "SELECT * FROM `tag_meta` 
INNER join `tag` ON tag_meta.tag_id = tag.tag_id 
WHERE `tag_meta`.type = 'pa_{$_GET['slug']}'; ";

$tags = $functions->Fetcharray($select_cat);
$json = json_encode($tags);

echo $json;
