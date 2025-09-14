<?php
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
$products = array();
$products['products'] = array();
$product_ids = array();

$product_name = $_GET['name'];
$name_arr = explode(' ', $_GET['name']);
$name_query = "";
foreach ($name_arr as $arr) {
    if ($arr == $name_arr[0])
        $arr_query .= "`post_title` LIKE '%$arr%'";
    else
        $arr_query .= " AND `post_title` LIKE '%$arr%'";
}
$min_price = intval($_GET['min']);
$max_price = intval($_GET['max']);
$page = $_GET['page'];


if ($min_price > 1) {
    $min_price = $min_price;
} else {
    $min_price = 1;
}

if ($max_price > 1) {
    $max_price = $max_price;
} else {
    $max_price = 100000000;
}

if ($page > 1) {
    $min = ($page - 1) * 18;
} else {
    $min = 0;
}
$sort = "ORDER BY `post`.`modify_date` DESC";
$tag_str = "";
if (strlen($_GET['tags'])) {
    $filter_tags = explode(",", $_GET['tags']);
    $tags = [];
    foreach ($filter_tags as $tag) {
        if($tag > 0)
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
    INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    INNER JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post_type` = 'product'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND (`post_meta`.`key` = '_price' OR `post_meta`.`key` = '_regular_price')
    AND CONVERT(`post_meta`.`value`, unsigned integer) >= $min_price
    AND CONVERT(`post_meta`.`value`, unsigned integer) <= $max_price
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort
        LIMIT $min, 18";
$all_products = $functions->Fetcharray($query);

$count_q = "SELECT `post`.`post_id` from `post` 
    INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
    INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
    INNER JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = 'product'
    AND `post`.`post_status` = 'publish'
    AND `post`.`post_parent` = 0
    AND (`post_meta`.`key` = '_price' OR `post_meta`.`key` = '_regular_price')
    AND CONVERT(`post_meta`.`value`, unsigned integer) >= $min_price
    AND CONVERT(`post_meta`.`value`, unsigned integer) <= $max_price
    $tag_str
    AND ($arr_query)
    GROUP BY `post`.`post_id`
    $sort;";

$count = count($functions->FetchArray($count_q));
$pages = ceil($count/18);
foreach ($all_products as $product) :
    $post_id = $product['post_id'];
    $obj = new product($post_id);
    $post_title = $obj->get_title();
    $stock_status = $obj->get_stock_status();
    $url = $obj->get_url();
    $post_name = $obj->get_slug();
    $date = $obj->get_post_date();
    $regular_price = $obj->get_regular_price();
    $_sale_price = $obj->get_sale_price();
    $final_price = $obj->get_price();
    $url = $obj->get_url();
    $tag = $obj->get_cats()[0]['name'];
    $img = $obj->get_thumbnail_src();
    $variant = $functions->fetchassoc("SELECT `post_id` FROM `post` WHERE `post_type` = 'product' AND `post_parent` = $post_id");
    if(is_countable($variant) && count($variant)){
        $product["product_type"] = 'product_variable';
    }
    if ($_sale_price > 0)
        $_sale_price = $final_price;
    else
        $regular_price = $final_price;

    $product["post_title"] = $post_title;
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
    array_push($products['products'], $product);
endforeach;

$products['pages'] = $pages;
echo json_encode($products);
