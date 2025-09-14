<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");

$all_result = $id_all_result = array();
$content = $_GET['content'];
$content_arr = explode(' ', $_GET['content']);
$arr_query = $filter_join = $filter_where = "";
foreach ($content_arr as $arr) {
    $e_arr = urlencode($arr);
    if ($arr == $content_arr[0])
        $arr_query .= "(`post_title` LIKE '%$arr%' OR `post_title` LIKE '%$e_arr %')";
    else
        $arr_query .= " AND (`post_title` LIKE '%$arr%' OR `post_title` LIKE '%$e_arr %')";
}

$filter = base::Fetchassoc("SELECT * FROM `tag` WHERE `slug` = '" . $_GET['filter'] . "';")['tag_id'];
if ($filter > 0) {
    $filter_join = "INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`";
    $filter_where = "AND `tag_relationships`.`tag_id` = $filter";
}
$query = "SELECT `post`.`post_id` from `post` 
    INNER JOIN `post_meta` on `post`.`post_id` = `post_meta`.`post_id` 
    $filter_join
    WHERE `post_type` = 'product' 
    AND `post_status` = 'publish' 
    AND `post_meta`.`key` = '_regular_price'
    AND `post_meta`.`value` > 0
    AND `post`.`post_parent` = 0
    AND ($arr_query)
    $filter_where
    GROUP BY `post`.`post_id`
    LIMIT 0, 10";
$query_result = base::FetchArray($query);

foreach ($query_result as $product) :
    if (!in_array($product, $all_result)) {
        $post_id = $product['post_id'];
        if ($post_id) :
            $obj = new product($post_id);
            $product["post_title"] = $obj->get_title();
            $_regular_price = $obj->get_meta('_regular_price');
            $_sale_price = $obj->get_meta('_sale_price');
            if ($obj->display_post_image()) {
                $img = $obj->display_post_image();
                $product["img"] = $img;
            }
            $product["_regular_price"] = $_regular_price;
            if (isset($_sale_price))
                $product["_sale_price"] = $_sale_price;
            $stock_status = $obj->get_meta('_stock_status');
            $product["stock_status"] = $stock_status;
            array_push($all_result, $product);
        endif;
    }
endforeach;

echo json_encode($all_result);
