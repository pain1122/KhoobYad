<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/tag.php");

$type = $_GET['type'];
$name = $_GET['name'];
$status = 'ok';
$slug = urlencode($name);
if ($type == 'post' || $type == 'product') {
    $query = "SELECT `post_id` FROM `post` 
    WHERE (`post_title` = '$name' || `post_name` = '$slug') 
    AND `post_type` = '$type'";

    $post_id = base::FetchAssoc($query)['post_id'];
    if ($post_id && $post_id > 0)
        $status = 'exsists';
} else {
    $query = "SELECT `tag`.`tag_id` FROM `tag` 
    INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` 
    WHERE (`tag`.`name` = '$name' || `tag`.`slug` = '$slug') 
    AND `tag_meta`.`type` = '$type'";

    $tag_id = base::FetchAssoc($query)['tag_id'];
    if ($tag_id && $tag_id > 0)
        $status = 'exsists';
}
echo json_encode($status);
