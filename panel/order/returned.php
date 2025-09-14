<?php
$amount = $_SESSION['final_price'] * 10;
$guid = explode("/", $_SERVER['REDIRECT_URL']);
$user_shop_order = $guid[2];
$Authority = $_GET['Authority'];

$data = array("merchant_id" => "b9bb4a7c-bb88-4d72-9a31-57c1f8b63e39", "authority" => $Authority, "amount" => $amount);
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
    $functions->redirect(site_url . 'panel/index.php?page=order/all-order.php');
} else {
    if ($result['data']['code'] == 100) {
        $get_items = $functions->Fetcharray("SELECT * FROM `items_order` WHERE order_id = $user_shop_order");

        foreach ($get_items as $item) {
            $quantity = $item['qty'];
            $item_id = $item['item_id'];
            $item_qty = $functions->Fetchassoc("SELECT * FROM `post_meta` WHERE `post_id` = $item_id AND `key` = '_stock'")['value'];
            $update_qty = $item_qty - $quantity;
            if ($item_qty > 0) {
                $update_quantity = $functions->RunQuery("UPDATE `post_meta` SET `value` = (`value` - $quantity) WHERE `post_id` = $item_id AND `key` = '_stock'") or die($con->error);
                if ($update_qty < 1) {
                    $functions->RunQuery("UPDATE `post_meta` SET `value` = 'outofstock' WHERE `key` = '_stock_status' AND `post_id` = $item_id") or die($con->error);
                }
            }

        }
        $RefID = $result['data']['ref_id'];
        $query = "UPDATE `post` SET `post_content` = '$RefID', `post_status` = 'processing' WHERE `post_id` = $user_shop_order;";
        $functions->RunQuery($query);
        if (strlen($_SESSION['coupon_code']) > 0) {
            $code = preg_replace('/\s+/', '', $_SESSION['coupon_code']);
            $select_coupon = "SELECT * FROM `post` WHERE `post_type` = 'coupon' AND `post_content` = '$code'";
            $select_coupon_res = $functions->FetchAssoc($select_coupon);
            $coupon_id = $select_coupon_res['post_id'];
            $coupon_uses = $obj->get_meta('uses');
            if ($coupon_uses > 0) {
                $coupon_uses -= 1;
                $functions->RunQuery("UPDATE `post_meta` SET `value` = $coupon_uses WHERE `post_id` = $coupon_id AND `key` = 'uses'");
            }
        }
        unset($_SESSION['coupon_code']);
        unset($_SESSION['cart']);
        unset($_SESSION['final_price']);
        $phonenumber = $functions->Fetchassoc("SELECT `login` FROM `users` WHERE `user_id` = " . $_SESSION['user_info']["uid"])['login'];
        $functions->send_sms($phonenumber, "yi36d21ilpqxpel", "orderid", $user_shop_order);
        $functions->redirect(site_url . 'panel/index.php?page=order/all-order.php');
    } else if ($result['erros']['code'] != -9) {
        // $query = "UPDATE `post` SET `post_status` = 'failed' WHERE `post_id` = $user_shop_order;";
        // $functions->RunQuery($query);
        $phonenumber = $functions->Fetchassoc("SELECT `login` FROM `users` WHERE `user_id` = " . $_SESSION['user_info']["uid"])['login'];
        // $functions->send_sms($phonenumber, "0ypz8ullndkaflf", "orderid", $user_shop_order);
        $functions->redirect(site_url . 'panel/index.php?page=order/all-order.php');

    } else {
        $functions->redirect(site_url . 'panel/index.php?page=order/all-order.php');
    }
}
