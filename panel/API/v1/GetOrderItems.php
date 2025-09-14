<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/shop-order.php");


$order_id = $_GET['order_id'];
$posts = array();
if(empty($order_id))
    exit;
$obj = new order($order_id);
$post_id = $post['post_id'];
$user_phone = $obj->get_user_phone();
$user_providence = $obj->get_user_providence();
$user_address = $obj->get_user_address();
$user_shipping_price = intval($obj->get_user_shipping_price());
$payment = $obj->get_payment();
$shipping = $obj->get_shipping();
$days = $obj->get_days();
$items = $obj->get_items();
$items_html = '';
$posts['order_id'] = $order_id;
$posts['user_providence'] = $user_providence;
$posts['user_address'] = $user_address;
$posts['user_phone'] = $user_phone;
$posts['user_shipping_price'] = number_format($user_shipping_price);
$posts['payment'] = $payment;
$posts['shipping'] = $shipping;
$posts['days'] = $days;
foreach ($items as $item) {
  $item_id = $item['item_id'];
  if ($item_id > 0) {
    $product = new product($item_id);
    $items_order_id = $item['items_order_id'];
    $type = $product->get_type();
    $cateogory_type = str_replace('-','_',$type).'_category';
    $tags = $product->get_taxonomy($cateogory_type);
    $item_title = $product->get_title();
    $item_qty = $item['qty'];
    $price = $obj->get_item_meta($items_order_id, 'price');
    $sum = $obj->get_item_meta($items_order_id, 'total');
    $tag_str = $tags[0]['name'];
    $items_html .= '
    <hr class="my-2">
    <form action="" method="post" class="row">
      <div class="col-1 ml-auto">' . $item_id . '</div>
      <div class="col-4 ml-auto">' . $item_title . '</div>
      <div class="col-2 ml-auto">' . $tag_str . '</div>
      <div class="col-1 ml-auto">' . $item_qty . '</div>
      <div class="col-2 ml-auto">' . number_format($price) . '</div>
      <div class="col-1 ml-auto">' . number_format($sum) . '</div>
      <div class="col-1 ml-auto"><button type="submit" name="delete_item" value="' . $item_id . '" class="btn btn-sm btn-danger">حذف</button></div>
    </form>
  ';
  }
}
$posts['items_html'] = $items_html;


echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
