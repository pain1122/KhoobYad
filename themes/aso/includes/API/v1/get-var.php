<?php
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/tag.php");
$post_id = $_GET['post_id'];
$var = $_GET['var'];
$filter_var = explode(',', $var);
$where_q = $join_q = '';
for($i = 1; $i < count($filter_var); $i++){
    $join_q .= " INNER JOIN `post_meta` `pm$i` ON `post_meta`.`post_id` = `pm$i`.`post_id`";
    $where_q .= " AND `pm$i`.`key` = '$filter_var[$i]'";
}
$var_q = "SELECT `parent`,`pm".(count($filter_var) - 1)."`.`value` FROM `post`
INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id`
INNER JOIN `tag` ON `tag`.`tag_id` = `post_meta`.`value`
INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
$join_q
WHERE `post_parent` = $post_id
AND `post_type` = 'product'
AND `post_meta`.`key` = 'first_var'
$where_q
GROUP BY `pm".(count($filter_var) - 1)."`.`value`;";
$variables = $functions->FetchArray($var_q);
if (is_countable($variables) && count($variables) > 0) {
    if (is_countable($variables) && count($variables) > 0) {
        $var_attr = [];
        foreach ($variables as $variable) {
            if ($var_attr[$variable['parent']])
                array_push($var_attr[$variable['parent']], $variable['value']);
            else
                $var_attr[$variable['parent']] = [$variable['value']];
        }
        $attrs = [];
        if (is_countable($var_attr)) {
            foreach ($var_attr as $key => $value) {
                $parent_var = new tag($key);
                $parent_var_name = $parent_var->get_name();
                foreach ($value as $attr) {
                    $var = new tag($attr);
                    $var_name = $var->get_name();
                    $new_attr['id'] = $attr;
                    $new_attr['name'] = $var_name;
                    array_push($attrs,$new_attr);
                }
            }
        }
        echo json_encode($attrs);
    }
} else {
    $join_q = $where_q = '';
    for($i = 2; $i < count($filter_var); $i++){
        $join_q .= " INNER JOIN `post_meta` `pm$i` ON `pm1`.`post_id` = `pm$i`.`post_id`";
        $where_q .= " AND `pm$i`.`value` = $filter_var[$i]";
    }
    $var_q = "SELECT `pm1`.`post_id` FROM `post_meta` `pm1` INNER JOIN `post` ON `pm1`.`post_id` = `post`.`post_id` $join_q WHERE `post`.`post_parent` = $post_id AND `pm1`.`value` = $filter_var[1] $where_q GROUP BY `pm1`.post_id";
    $product_id = $functions->FetchAssoc($var_q)['post_id'];
    $obj = new product($product_id);
    $title = $obj->get_title();
    $price = $obj->get_meta('_regular_price');
    $price_off = $obj->get_meta('_sale_price');
    $stock = $obj->get_meta('_stock');
    $_stock_status = $obj->get_meta('_stock_status');
    $color_code = $obj->get_meta('color_code');
    $product = [
        "post_id" => $product_id,
        "title" => $title,
        "price" => $price,
        "sale-price" => $price_off,
        "stock" => $stock,
        "color_code" => $color_code,
        "_stock_status" => $_stock_status
    ];
    echo json_encode($product);
}
