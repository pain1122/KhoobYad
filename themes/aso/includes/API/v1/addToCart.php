<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
$item_id = $_GET['id'];
$num = intval($_GET['number']);
$obj = new product($item_id);
$title = $obj->get_title();
$url = $obj->get_url();
$limit = intval($obj->get_restrict());
$stock = intval($obj->get_stock());
$stock_status = $obj->get_stock_status();
$type = $obj->get_type();
$parent = $obj->get_parent();

if (!$limit)
    $limit = 10000;
if (!$stock)
    $stock = 10000;
$cart_item = [];
$cart_item['error'] = "";
if ($num <= $limit && $num <= $stock) {
    if (!isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = $num;
    } else {
        $_SESSION['cart'][$item_id] += $num;
        if ($_SESSION['cart'][$item_id] > $limit || $_SESSION['cart'][$item_id] > $stock) {
            $_SESSION['cart'][$item_id] -= $num;
            $cart_item['error'] = 'تعداد محصولات از حد مجاز بیشتر است.';
        }
    }
} else {
    if ($num > $stock)
        $cart_item['error'] = 'تعداد سفارشات از موجودی بیشتر است.';
    elseif ($num > $limit)
        $cart_item['error'] = 'تعداد محصولات از حد مجاز بیشتر است.';
}

$price = intval($obj->get_price());
if ($num >= 1)
    $price = $price * $num;
if ($parent > 0){
    $obj = new product($parent);
    $thumbnail = $obj->get_thumbnail_src();
    $url = $obj->get_url();
}else{
    $thumbnail = $obj->get_thumbnail_src();
}
$cart_item['post_id'] = $item_id;
$cart_item['url'] = $url;
$cart_item['post_title'] = $title;
$cart_item['thumbnail'] = $thumbnail;
$cart_item['price'] = $price;

echo json_encode($cart_item);
