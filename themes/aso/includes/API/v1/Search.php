<?php
use ParagonIE\Sodium\Core\Curve25519\Ge\P2;
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
$all_result = array();
$content = $_GET['content'];
$content_arr = explode(' ', $_GET['content']);
$arr_query = "";
foreach ($content_arr as $arr) {
    if ($arr == $content_arr[0])
        $arr_query .= "`post_title` LIKE '%$arr%'";
    else
        $arr_query .= " AND `post_title` LIKE '%$arr%'";
}
$filter_join_q = $filter_q = '';
$filter = $functions->Fetchassoc("SELECT * FROM `tag` WHERE `slug` = '" . $_GET['filter'] . "';")['tag_id'];
if ($filter > 0) {
    $filter_join_q = "INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`";
    $filter_q = "AND `tag_relationships`.`tag_id` = $filter";
}

$query = "SELECT `post`.`post_id` from `post`
    INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
    $filter_join_q
    WHERE `post_type` = 'product' 
    AND `post_status` = 'publish' 
    AND `post_meta`.`key` = '_price' 
    AND `post_meta`.`value` > 0
    AND `post_parent` = 0
    AND (($arr_query) OR `post_content` LIKE '%$content%')
    $filter_q
    GROUP BY `post`.`post_id`
    LIMIT 0, 10";
$query_result = $functions->FetchArray($query);

foreach ($query_result as $product) :
    if (!in_array($product, $all_result)) {
        $post_id = $product['post_id'];
        $obj = new product($post_id);
        $price = $obj->get_price();
        $_regular_price = $obj->get_regular_price();
        $_sale_price = $obj->get_sale_price();
        if ($obj->display_post_image()) {
            $img = $obj->display_post_image();
            $product["img"] = $img;
        }
        if ($_sale_price > 0)
            $_sale_price = $price;
        else
            $_regular_price = $price;
        $product["_regular_price"] = $_regular_price;
        if (isset($_sale_price))
            $product["_sale_price"] = $_sale_price;
        $stock_status = $obj->get_stock_status();
        $product["stock_status"] = $stock_status;
        $product["post_title"] = $obj->get_title();
        $product["url"] = $obj->get_url();
        array_push($all_result, $product);
    }
endforeach;

echo json_encode($all_result);