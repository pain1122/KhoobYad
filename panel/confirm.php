<?php
session_start();
include_once("../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
include_once(base_dir . "/includes/classes/shop-order.php");
$amount = $_SESSION['final_price'] * 10;
$user_shop_order = $_SESSION['order_id'];
$Authority = $_GET['Authority'];
$data = array("merchant_id" => "a3307c24-7ba4-4a11-aa42-17cd7cf1f5eb", "authority" => $Authority, "amount" => $amount);
$jsonData = json_encode($data);
$ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));

$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result, true);


if ($err) {
    echo "cURL Error #:" . $err;
    base::redirect(site_url . 'panel/index.php?page=order/all-order.php');
} else {
    if ($result['data']['code'] == 100) {
        $get_items = base::Fetcharray("SELECT * FROM `items_order` WHERE order_id = $user_shop_order");
        $user = new user($_SESSION['uid']);
        $user_classes = $user->get_user_meta('classes');
        if($user_classes)
            $user_classes = json_decode($user_classes,true);
        else
            $user_classes = [];
        foreach ($get_items as $item) {
            $quantity = $item['qty'];
            $item_id = $item['item_id'];
            $item_qty = base::Fetchassoc("SELECT * FROM `post_meta` WHERE `post_id` = $item_id AND `key` = '_stock'")['value'];
            $update_qty = $item_qty - $quantity;
            if ($item_qty > 0) {
                $update_quantity = base::RunQuery("UPDATE `post_meta` SET `value` = (`value` - $quantity) WHERE `post_id` = $item_id AND `key` = '_stock'") or die($con->error);
                if ($update_qty < 1) {
                    base::RunQuery("UPDATE `post_meta` SET `value` = 'outofstock' WHERE `key` = '_stock_status' AND `post_id` = $item_id") or die($con->error);
                }
            }
            $user_classes[$item_id] = [];
        }
        $RefID = $result['data']['ref_id'];
        $query = "UPDATE `post` SET `post_content` = '$RefID', `post_status` = 'completed' WHERE `post_id` = $user_shop_order;";
        base::RunQuery($query);
        if (strlen($_SESSION['coupon_code']) > 0) {
            $code = preg_replace('/\s+/', '', $_SESSION['coupon_code']);
            $select_coupon = "SELECT * FROM `post` WHERE `post_type` = 'coupon' AND `post_content` = '$code'";
            $select_coupon_res = base::FetchAssoc($select_coupon);
            $coupon_id = $select_coupon_res['post_id'];
            $coupon_uses = $obj->get_meta('uses');
            if ($coupon_uses > 0) {
                $coupon_uses -= 1;
                base::RunQuery("UPDATE `post_meta` SET `value` = $coupon_uses WHERE `post_id` = $coupon_id AND `key` = 'uses'");
            }
        }
        unset($_SESSION['coupon_code']);
        unset($_SESSION['cart']);
        unset($_SESSION['local']);
        unset($_SESSION['final_price']);
        $phonenumber = $user->get_login();
        $user_classes = json_encode($user_classes, JSON_UNESCAPED_UNICODE);
        $user->insert_user_meta(['classes' => $user_classes]);
        base::send_sms($phonenumber, "yi36d21ilpqxpel", "orderid", $user_shop_order);
        base::redirect(site_url . 'panel/index.php?page=order/all-order.php');
    } else if ($result['erros']['code'] != -9) {
        // $query = "UPDATE `post` SET `post_status` = 'failed' WHERE `post_id` = $user_shop_order;";
        // base::RunQuery($query);
        $phonenumber = $user->get_login();
        // base::send_sms($phonenumber, "0ypz8ullndkaflf", "orderid", $user_shop_order);
        base::redirect(site_url . 'panel/index.php?page=order/all-order.php');
    } else {
        base::redirect(site_url . 'panel/index.php?page=order/all-order.php');
    }
}
