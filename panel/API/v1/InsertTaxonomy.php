<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/tag.php");

$type = $_GET['type'];
$desc = $_GET['desc'];
$name = $_GET['name'];
$slug = urlencode(str_replace(' ', '-', $_GET['slug']));
$parent = intval($_GET['parent']);
if (!$parent)
    $parent = 0;
$query = "SELECT `tag`.`tag_id` FROM `tag` 
INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` 
WHERE (`tag`.`name` = '$name' || `tag`.`slug` = '$slug') 
AND `tag_meta`.`type` = '$type'";
$tag_id = base::FetchAssoc($query)['tag_id'];

if ($tag_id || $tag_id > 0 || !$slug) {
    $err = [
        'error' => $query
    ];
    echo json_encode($err);
}
$slug = urldecode($slug);
if ($type == 'product_attribute' && $parent > 0)
    $tag_slug = urlencode(str_replace(' ', '-', 'pa_' . $slug));
elseif ($type == 'product_variable' && $parent > 0)
    $tag_slug = urlencode(str_replace(' ', '-', 'var_' . $slug));
$insert_query = "INSERT INTO `tag`(`name`, `slug`) VALUES ('$name','$tag_slug')";
base::RunQuery($insert_query);
$tag_id = $GLOBALS['con']->insert_id;
$meta_query = "INSERT INTO `tag_meta`(`tag_id`, `type`, `description`, `parent`) VALUES ($tag_id,'$type','$desc',$parent)";
base::RunQuery($meta_query);

$tag = [
    'tag_id'        => $tag_id,
    'name'          => $name,
    'slug'          => urldecode($slug),
    'description'   => $desc,
    'type'          => $type,
    'parent'        => $parent
];
$json[] = $tag;
echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
