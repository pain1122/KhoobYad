<?php
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
$products = array();
$products['products'] = array();
$product_ids = array();

$uid = intval($_GET['user_id']);
if (empty($uid))
    exit;
$user = new User($uid);
$user_classes = array_keys(json_decode($user->get_user_meta('classes'), true));
$user_classes = implode(',',$user_classes);
$type = $_GET['type'];
$product_name = $_GET['name'];
$name_arr = explode(' ', $_GET['name']);
$name_query = "";
foreach ($name_arr as $arr) {
    if ($arr == $name_arr[0])
        $arr_query .= "`post_title` LIKE '%$arr%'";
    else
        $arr_query .= " AND `post_title` LIKE '%$arr%'";
}
$page = $_GET['page'];


if ($page > 1) {
    $min = ($page - 1) * 18;
} else {
    $min = 0;
}
$sort = "ORDER BY `post`.`modify_date` DESC";
$tag_str = "";
if ($_GET['tags'] > 0) {
    $filter_tags = explode(",", $_GET['tags']);
    $tags = [];
    foreach ($filter_tags as $tag) {
        if ($tag > 0)
            array_push($tags, $functions->get_category_branch($tag, []));
    }
    foreach ($tags as $tag) {
        $filter_tags = array_merge($tag, $filter_tags);
    }
    $filter_tags = str_replace(['\"', '[', ']'], '', json_encode($filter_tags));
    $tag_str = " AND (`tag_relationships`.`tag_id` IN  ($filter_tags) OR `tag_meta`.`parent` IN ($filter_tags))";
}
if ($_GET['sort'] == 'new') {
    $sort = "ORDER BY `post`.`post_date` DESC";
}
if ($_GET['sort'] == 'old') {
    $sort = "ORDER BY `post`.`post_date` ASC";
}

$query = "SELECT `post`.`post_id` from `post` 
    LEFT JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post_type` = '$type'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND `post`.`post_id` IN ($user_classes)
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort
        LIMIT $min, 18";
$all_products = $functions->Fetcharray($query);
$count_q = "SELECT COUNT(`post`.`post_id`) AS `count` from `post` 
    LEFT JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = '$type'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND `post`.`post_id` IN ($user_classes)
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort;";

$count = $functions->FetchArray($count_q);
if($count)
    $count = count($count);
else
    $count = 0;
$pages = ceil($count / 18);
foreach ($all_products as $product) :
    $post_id = $product['post_id'];
    if ($post_id) :
        $obj = new product($post_id);
        $obj->set_post_type($type);
        $post_title = $obj->get_title();
        $description = $obj->get_meta('description');
        if (strlen($description) > 150)
            $description = mb_substr($description, 0, 150) . ' ...';
        $img = $obj->get_thumbnail_src();

        $product["post_title"] = $post_title;
        $product["description"] = $description;
        if ($img) {
            $product["img"] = $img;
        }
        array_push($products['products'], $product);
    endif;
endforeach;

$products['pages'] = $pages;
echo json_encode($products);
