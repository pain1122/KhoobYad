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
if ($_GET['sort'] == 'price_desc') {
    $sort = "ORDER BY CONVERT(`post_meta`.`value`, unsigned integer) DESC";
}
if ($_GET['sort'] == 'price_asc') {
    $sort = "ORDER BY CONVERT(`post_meta`.`value`, unsigned integer) ASC";
}
if ($_GET['sort'] == 'new') {
    $sort = "ORDER BY `post`.`post_date` DESC";
}
if ($_GET['sort'] == 'old') {
    $sort = "ORDER BY `post`.`post_date` ASC";
}

$query = "SELECT `post`.`post_id` from `post` 
    INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
    LEFT JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post_type` = '$type'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND (`key` = '_price' OR `key` = '_regular_price')
    AND `post`.`post_id` NOT IN (SELECT `post`.`post_id` FROM `post` INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` WHERE `key` = 'exclusive' AND `value` = 'true')
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort
        LIMIT $min, 18";
$all_products = $functions->Fetcharray($query);

$count_q = "SELECT `post`.`post_id` from `post` 
    INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
    LEFT JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = '$type'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND (`key` = '_price' OR `key` = '_regular_price')
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort;";
$count = count($functions->FetchArray($count_q));
$pages = ceil($count / 18);

$uid = intval($_GET['uid']);
if (!empty($uid)) {
    $user = new User($uid);
    $user_classes = $user->get_user_meta('classes');
    if($user_classes)
        $user_classes = array_keys(json_decode($user_classes, true));
    else
        $user_classes = [];
}
foreach ($all_products as $product) :
    $post_id = $product['post_id'];
    if ($post_id) :
        $obj = new product($post_id);
        $obj->set_post_type($type);
        $post_title = $obj->get_title();
        $stock_status = $obj->get_stock_status();
        $url = $obj->get_url();
        $post_name = $obj->get_slug();
        $date = $obj->get_post_date();
        $regular_price = intval($obj->get_regular_price());
        $_sale_price = intval($obj->get_sale_price());
        $final_price = intval($obj->get_price());
        $description = $obj->get_meta('description');
        if (strlen($description) > 150)
            $description = mb_substr($description, 0, 150) . ' ...';
        $url = $obj->get_url();
        $tag = $obj->get_cats()[0]['name'];
        $img = $obj->get_thumbnail_src();
        if ($_sale_price > 0)
            $_sale_price = $final_price;
        else
            $regular_price = $final_price;
        $product['owned'] = 'false';
        $product["post_title"] = $post_title;
        $product["description"] = $description;
        $product["stock_status"] = $stock_status;
        $product["url"] = $url;
        if ($img) {
            $product["img"] = $img;
        }
        if ($regular_price > 0)
            $product['_regular_price'] = $regular_price;
        else
            $product['_regular_price'] = $final_price;
        if ($_sale_price > 0)
            $product["_sale_price"] = $_sale_price;
        if (isset($tag))
            $product['tag'] = $tag;
        if(in_array($post_id,$user_classes))
            $product['owned'] = 'true';
        array_push($products['products'], $product);
    endif;
endforeach;

$products['pages'] = $pages;
echo json_encode($products);
