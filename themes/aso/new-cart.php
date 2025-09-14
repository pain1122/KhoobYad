<?php
include_once('header.php');
$post_id = $post['post_id'];

$count = 0;
$price_vahed  = 0;
$price_total = 0;
$price_numberic = 0;
$price_delivery = $functions->get_option('s_delivery');
$free_delivery = $functions->get_option('s_free');
$coupon_for = 0;
$price_coupon = 0;
$coupon_error = "";
$final_price = 0;

if (isset($_POST['minus-item'])) {
    $p_id = $_POST['minus-item'];
    if ($_SESSION['cart'][$p_id] > 1) :
        $_SESSION['cart'][$p_id] -= 1;
    else :
        unset($_SESSION['cart'][$p_id]);
    endif;
    $code = $_SESSION['coupon_code'];
    $price_coupon = 0;
    unset($_SESSION['coupon_code']);
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (isset($_POST['plus-item'])) {
    $p_id = $_POST['plus-item'];
    $obj = new product($p_id);
    $limit = intval($obj->get_restrict());
    $stock = intval($obj->get_stock());
    if ($stock == null)
        $stock = 1000000;
    if ($limit == 0)
        $limit = $stock;
    if ($_SESSION['cart'][$p_id] < $limit) {

        $_SESSION['cart'][$p_id] += 1;
    }
    $code = $_SESSION['coupon_code'];
    $price_coupon = 0;
    unset($_SESSION['coupon_code']);
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (isset($_POST['delete'])) {
    unset($_SESSION['cart'][$_POST['delete']]);
    $code = $_SESSION['coupon_code'];
    unset($_SESSION['coupon_code']);
    $price_coupon = 0;
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (isset($_POST['add-product'])) {
    $p_id = $_POST['add-product'];
    $obj = new product($p_id);
    $limit = intval($obj->get_restrict());
    $stock = intval($obj->get_stock());
    if ($stock == null)
        $stock = 1000000;
    if ($limit == 0)
        $limit = $stock;
    if ($stock > 0) {
        if (!isset($_SESSION['cart'][$p_id])) {
            $_SESSION['cart'][$p_id] = 1;
        } else {
            $_SESSION['cart'][$p_id] += 1;
            if ($_SESSION['cart'][$p_id] > $limit || $_SESSION['cart'][$p_id] > $stock) {
                $_SESSION['cart'][$p_id] -= 1;
                $cart_item['error'] = 'تعداد محصولات از حد مجاز بیشتر است.';
            }
        }
    }
    $code = $_SESSION['coupon_code'];
    $price_coupon = 0;
    unset($_SESSION['coupon_code']);
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (isset($_POST['delete-coupon'])) {
    unset($_SESSION['coupon_code']);
    $price_coupon = 0;
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart = $_SESSION['cart'];
    $cart_total = $all_items = 0;

    foreach ($cart as $item_id => $item_count) :
        if ($item_id == null)
            break;
        $obj = new product($item_id);
        $stock_status = $obj->get_stock_status();
        if ($stock_status == 'outofstock') {
            unset($_SESSION['cart'][$item_id]);
            unset($cart[$item_id]);
            break;
        } else {
            $price = $obj->get_price();
            $price_total  = ($price * $item_count); //  + $price_total;
            $all_items += $item_count;
            $cart_total += $price_total;
        }
    endforeach;
    $final_price = $cart_total;

    $recomandeds = '';
    foreach ($cart as $item_id => $item_count) :
        $complementaries = $obj->get_meta('complementaries');
        if (strlen($recomandeds) == 0)
            $recomandeds = $complementaries;
        else
            $recomandeds = $recomandeds . ',' . $complementaries;
        $recomandeds = str_replace(',,', ',', $recomandeds);
    endforeach;
    $recomandeds = rtrim($recomandeds, ',');
}
if (isset($_POST['coupon'])) {
    $code = preg_replace('/\s+/', '', $_POST['coupon-code']);

    if ($code != null) {
        $select_coupon = "SELECT `post_id` FROM `post` WHERE `post_type` = 'coupon' AND `post_content` = '$code'";
        $select_coupon_res = $functions->FetchAssoc($select_coupon);
        $select_uses = $functions->FetchAssoc("SELECT COUNT(`user_id`) AS `count` FROM `user_meta` WHERE `user_id` = {$_SESSION['user_info']['uid']} AND `key` = 'coupon' AND `value` = '$code'")['count'];
        if ($select_coupon_res) {
            $coupon_id = $select_coupon_res['post_id'];
            $obj = new post($coupon_id);
            $status = $obj->get_status();
            $coupon_for = $obj->get_meta('user-id');
            $min_price = $obj->get_meta('min_price');
            $uses = intval($obj->get_meta('uses'));
            if (!isset($_SESSION['coupon_code']) && $code != $_SESSION['coupon_code']) {
                if ($status == 'publish') {
                    if (!empty($min_price) && $final_price >= $min_price) {
                        if (!empty($coupon_for)) {
                            if ($coupon_for == $user_id && $select_uses < $uses) {
                                $_SESSION['coupon_code'] = $code;
                                $price_coupon = $obj->get_meta('coupon');
                                $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_success');
                                $functions->RunQuery("INSERT INTO `user_meta` (`user_id`,`key`,`value`) VALUE ($user_id,'coupon','$code')");
                            } else {
                                $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_resricted');
                            }
                        } elseif (!empty($uses) && $select_uses < $uses) {
                            $_SESSION['coupon_code'] = $code;
                            $price_coupon = $obj->get_meta('coupon');
                            $functions->RunQuery("INSERT INTO `user_meta` (`user_id`,`key`,`value`) VALUE ($user_id,'coupon','$code')");
                            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_success');
                        } else {
                            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_unavailable');
                            unset($_SESSION['coupon_code']);
                            $price_coupon = 0;
                        }
                    } else {
                        $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_minimum') . number_format($min_price);
                        unset($_SESSION['coupon_code']);
                        $price_coupon = 0;
                    }
                } else {
                    $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_expired');
                    unset($_SESSION['coupon_code']);
                    $price_coupon = 0;
                }
            } else {
                $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_already_applied');
            }
        } else {
            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_incorect');
            unset($_SESSION['coupon_code']);
            $price_coupon = 0;
        }
    } else {
        $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_incorect');
        unset($_SESSION['coupon_code']);
        $price_coupon = 0;
    }
}
if (isset($_SESSION['coupon_code'])) {
    $code = preg_replace('/\s+/', '', $_SESSION['coupon_code']);
    $select_coupon = "SELECT * FROM `post` WHERE `post_type` = 'coupon' AND `post_content` = '$code'";
    $select_coupon_res = $functions->FetchAssoc($select_coupon);
    $coupon_id = $select_coupon_res['post_id'];
    $obj = new post($coupon_id);
    $coupon_uses = $obj->get_meta('uses');
    $price_coupon = $obj->get_meta('coupon');
    $expire = $obj->get_meta('expire-time');
    $coupon_uid = $obj->get_meta('user-id');
    if ($coupon_uses == 0 || ($expire && $expire < time()))
        $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_expired');
    elseif ($coupon_uid && $coupon_uid > 0 && $coupon_uid != $user_id)
        $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_resricted');
    else
        $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_success');
    if ($price_coupon != 0) {
        if ($price_coupon <= 100) {
            $price_coupon = ($cart_total * $price_coupon) / 100;
            $final_price = ($cart_total - $price_coupon);
        } else if ($cart_total > $price_coupon) {
            $final_price = ($cart_total - $price_coupon);
        } else {
            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_over_valued');
        }
    } else {
        $final_price = ($cart_total);
    }
}
if (isset($_POST['pay'])) {
    if (isset($_POST['rules'])) {
        $fullname = $_POST['fullname'];
        $notes = $_POST['notes'];
        $phonenumber = $_POST['phonenumber'];
        $address = $_POST['address'];
        $address = json_decode($address);
        print_r($address);
        $postal_code = $address['postal_code'];
        $providence = $address['providence'];
        $city = $address['city'];
        $address = $address['address'];
        if (!$providence || $providence == '' || $providence == 'empty')
            $providence = 'تهران';
        if (!$city || $city == $functions->get_language($_SESSION['lang'], 'choose_city'))
            $city = 'تهران';
        $payment = $_POST['payment'];
        $shipping = $_POST['shipping'];
        $price_delivery = 0;
        if ($shipping == 'post') :
            $price_delivery = $functions->get_option('s_post');
        else :
            $price_delivery = $functions->get_option('s_delivery');
        endif;
        if ($cart_total >= $free_delivery) {
            $price_delivery = 0;
        }
        $final_price = $cart_total + $price_delivery - $price_coupon;

        // Factor Order
        $get_last_factor = $functions->Fetchassoc("SELECT `post_excerpt` FROM `post` WHERE `post_type` = 'shop_order' ORDER BY `post_id` DESC")['post_excerpt'];
        if (strlen($get_last_factor) > 1) {
            $get_seller_char = $get_last_factor[0];
            $get_seller_count = intval($get_last_factor[1]);

            if ($get_seller_count == 9) {
                if ($get_seller_char == "T")
                    $get_seller_char = "A";
                else
                    $get_seller_char++;

                $get_seller_count = 0;
            } else {
                $get_seller_count++;
            }
            $factor_seller = $get_seller_char . $get_seller_count;
        } else {
            $factor_seller = "A0";
        }
        ///end of factor order
        $order = new order('new);
        $order_id = $order->get_id();
        $order->set_author($user_id);
        $order->set_title($phonenumber . ' - ' . $fullname);
        $order->set_status('failed');
        $order->set_excerpt($factor_seller);

        $post_meta_content = array(
            'user_name' => $fullname,
            'user_id' => $user_id,
            'user_phone' => $phonenumber,
            'user_providence' => $providence,
            'user_city' => $city,
            'user_address' => $address,
            'user_shipping_price' => $price_delivery,
            'user_postal_code' => $postal_code,
            'user_notes' => $notes,
            'sum' => $final_price,
            'coupon' => $price_coupon,
            'coupon_code' => $code,
            'payment' => $payment,
            'shipping' => $shipping
        );

        $order->insert_meta($post_meta_content);


        $cart = $_SESSION['cart'];
        $pay = $final_price;
        foreach ($cart as $item_id => $item_count) {
            $item = new product($item_id);
            $price_id = $item->get_regular_price();
            $price_off_id = $item->get_sale_price();
            $product_status = $item->get_status();
            $off = 0;
            if ($price_off_id) {
                $off = $price_id - $price_off_id;
                $price_id = $price_off_id;
            }
            $count_price_id = $price_id * $item_count;
            if ($product_status == 'outofstock') {
                $pay = $pay - $count_price_id;
                $order->set_sum($pay);
                unset($_SESSION['cart'][$item_id]);
            } else {
                $order->set_item(['id' => $item_id, 'qty' => $item_count, 'price' => $price_id, 'total' => $count_price_id, 'off' => $off, 'coupon' => $coupon_id]);
            }
        }
        $_SESSION['final_price'] = $pay;
        if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0 && $pay > 0) {
            if ($payment == 'online') {
                $functions->ZarinpalPay($pay, site_url."pay/$order_id", $user_id);
            } else {
                $_SESSION['local'] = true;
                $functions->redirect(site_url."pay/$order_id");
            }
        } else {
            $order->set_sum(0);
            unset($_SESSION['coupon_code']);
            unset($_SESSION['cart']);
            unset($_SESSION['local']);
            unset($_SESSION['final_price']);
            $functions->redirect(site_url.'orders');
        }
    }
}
if ($cart_total >= $free_delivery) :
    $s_post = 0;
    $s_delivery = 0;
else :
    $s_post = $functions->get_option('s_post');
    $s_delivery = $functions->get_option('s_delivery');
endif;
$final_price += $s_post;
$addresses = json_decode($user->get_user_meta('addresses'), true);

$wishlist_query = "SELECT `value` as `post_id` FROM `user_meta` WHERE `user_id` = '$user_id' AND `key` = 'wishlist';";
$wishlist = $functions->FetchArray($wishlist_query);
?>
<script>
    var delivery = <?php echo $s_post ?>;
</script>
<link rel="stylesheet" href="/themes/aso/includes/assets/css/formValidation.min.css" />
<link rel="stylesheet" href="/themes/aso/includes/assets/css/select2.css" />
<link rel="stylesheet" href="/themes/aso/includes/assets/css/bs-stepper.css" />
<div class="container">
    <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mt-2">
        <div class="bs-stepper-header m-auto border-0">
            <div class="step active" data-target="#checkout-cart">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-icon">
                        <svg id="wizardCart" xmlns="http://www.w3.org/2000/svg">
                            <g fill-rule="nonzero">
                                <path d="M57.927 34.29V16.765a4 4 0 0 0-4-4h-4.836a.98.98 0 1 0 0 1.963h3.873a3 3 0 0 1 3 3v15.6a3 3 0 0 1-3 3H14.8a4 4 0 0 1-4-4v-14.6a3 3 0 0 1 3-3h3.873a.98.98 0 1 0 0-1.963H10.8V4.909a.98.98 0 0 0-.982-.982H7.715C7.276 2.24 5.752.982 3.927.982A3.931 3.931 0 0 0 0 4.909a3.931 3.931 0 0 0 3.927 3.927c1.825 0 3.35-1.256 3.788-2.945h1.121v38.29a.98.98 0 0 0 .982.983h6.903c-1.202.895-1.994 2.316-1.994 3.927A4.915 4.915 0 0 0 19.637 54a4.915 4.915 0 0 0 4.908-4.91c0-1.61-.79-3.03-1.994-3.926h17.734c-1.203.895-1.994 2.316-1.994 3.927A4.915 4.915 0 0 0 43.2 54a4.915 4.915 0 0 0 4.91-4.91c0-1.61-.792-3.03-1.995-3.926h5.921a.98.98 0 1 0 0-1.964H10.8v-4.91h43.127a4 4 0 0 0 4-4zm-54-27.417a1.966 1.966 0 0 1-1.963-1.964c0-1.083.88-1.964 1.963-1.964.724 0 1.35.398 1.691.982h-.709a.98.98 0 1 0 0 1.964h.709c-.34.584-.967.982-1.69.982zm15.71 45.163a2.949 2.949 0 0 1-2.946-2.945 2.949 2.949 0 0 1 2.945-2.946 2.95 2.95 0 0 1 2.946 2.946 2.949 2.949 0 0 1-2.946 2.945zm23.563 0a2.949 2.949 0 0 1-2.945-2.945 2.949 2.949 0 0 1 2.945-2.946 2.949 2.949 0 0 1 2.945 2.946 2.949 2.949 0 0 1-2.945 2.945z" />
                                <path d="M33.382 27.49c7.58 0 13.745-6.165 13.745-13.745C47.127 6.165 40.961 0 33.382 0c-7.58 0-13.746 6.166-13.746 13.745 0 7.58 6.166 13.746 13.746 13.746zm0-25.526c6.497 0 11.782 5.285 11.782 11.781 0 6.497-5.285 11.782-11.782 11.782S21.6 20.242 21.6 13.745c0-6.496 5.285-11.781 11.782-11.781z" />
                                <path d="M31.77 19.41c.064.052.136.083.208.117.03.015.056.039.086.05a.982.982 0 0 0 .736-.027c.049-.023.085-.066.13-.095.07-.046.145-.083.202-.149l.02-.021.001-.001.001-.002 7.832-8.812a.98.98 0 1 0-1.467-1.304l-7.222 8.126-5.16-4.3a.983.983 0 0 0-1.258 1.508l5.892 4.91z" />
                            </g>
                        </svg>
                    </span>
                    <span class="bs-stepper-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_cart'); ?></span>
                </button>
            </div>
            <div class="line">
                <i class="bx bx-chevron-right"></i>
            </div>
            <div class="step" data-target="#checkout-address">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-icon">
                        <svg id="wizardPayment" xmlns="http://www.w3.org/2000/svg">
                            <g fill-rule="nonzero">
                                <path d="M8.679 23.143h7.714V13.5H8.679v9.643zm1.928-7.714h3.857v5.785h-3.857V15.43zM8.679 34.714h7.714v-9.643H8.679v9.643zM10.607 27h3.857v5.786h-3.857V27zM8.679 46.286h7.714v-9.643H8.679v9.643zm1.928-7.715h3.857v5.786h-3.857v-5.786zM34.714 22.179a.963.963 0 0 0-.964.964v8.678a.963.963 0 1 0 1.929 0v-8.678a.963.963 0 0 0-.965-.964zM34.714 34.714a.963.963 0 0 0-.964.965v8.678a.963.963 0 1 0 1.929 0V35.68a.963.963 0 0 0-.965-.965zM29.893 22.179a.963.963 0 0 0-.964.964v.964a.963.963 0 1 0 1.928 0v-.964a.963.963 0 0 0-.964-.964zM29.893 27a.963.963 0 0 0-.964.964v1.929a.963.963 0 1 0 1.928 0v-1.929a.963.963 0 0 0-.964-.964zM29.893 32.786a.963.963 0 0 0-.964.964v.964a.963.963 0 1 0 1.928 0v-.964a.963.963 0 0 0-.964-.964zM29.893 37.607a.963.963 0 0 0-.964.964V40.5a.963.963 0 1 0 1.928 0v-1.929a.963.963 0 0 0-.964-.964zM29.208 43.672c-.174.183-.28.434-.28.685 0 .26.106.502.28.685.182.173.434.28.685.28.25 0 .501-.107.684-.28a.996.996 0 0 0 .28-.685c0-.25-.106-.502-.28-.684a1 1 0 0 0-1.369 0z" />
                                <path d="M42.286 0H4a4 4 0 0 0-4 4v2.75h2.893v43.184A4.071 4.071 0 0 0 6.959 54h32.367a4.071 4.071 0 0 0 4.067-4.066V6.75h2.893V4a4 4 0 0 0-4-4zm-.822 49.934a2.14 2.14 0 0 1-2.138 2.137H6.96a2.14 2.14 0 0 1-2.138-2.137V4.82H8.68v6.75h7.714v-6.75h8.678v11.326c0 1.199.976 2.174 2.175 2.174h9.151a2.177 2.177 0 0 0 2.174-2.174V4.821h2.893v45.113zM10.607 4.82h3.857v4.822h-3.857V4.82zm22.179 0V6.75h-1.929V4.821h1.929zm3.857 0v1.954c-.082-.01-.162-.025-.246-.025h-1.683V4.821h1.929zm-9.397 11.572a.246.246 0 0 1-.246-.246v-3.636c.082.01.162.025.246.025h1.683v3.857h-1.683zm3.611-3.857h1.683c.136 0 .246.11.246.246v3.365c0 .084.015.164.025.246h-1.954v-3.857zm3.857 3.611v-3.365a2.177 2.177 0 0 0-2.174-2.175h-5.294a.246.246 0 0 1-.246-.246V8.924c0-.135.11-.245.246-.245h9.151c.136 0 .246.11.246.245v7.223c0 .136-.11.246-.246.246H34.96a.246.246 0 0 1-.246-.246zM28.93 6.75h-1.683c-.084 0-.164.015-.246.025V4.821h1.929V6.75zm15.428-1.929h-.964V2.893h-40.5V4.82h-.964V2.93a1 1 0 0 1 1-1h40.428a1 1 0 0 1 1 1V4.82z" />
                                <path d="m57.575 31.14-5.785-5.785a.965.965 0 0 0-1.365 0L44.64 31.14a.963.963 0 1 0 1.363 1.363l4.14-4.14v24.673a.963.963 0 1 0 1.928 0V28.363l4.14 4.14a.962.962 0 0 0 1.364 0 .963.963 0 0 0 0-1.363z" />
                            </g>
                        </svg>
                    </span>
                    <span class="bs-stepper-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_pay'); ?></span>
                </button>
            </div>
            <div class="line">
                <i class="bx bx-chevron-right"></i>
            </div>
            <div class="step">
                <button type="button" class="step-trigger" disabled>
                    <span class="bs-stepper-icon">
                        <svg xmlns="http://www.w3.org/2000/svg">
                            <g fill-rule="nonzero">
                                <path d="M7.2 14.4h13.5a.9.9 0 1 0 0-1.8H7.2a.9.9 0 1 0 0 1.8zM7.2 11.7h8.1a.9.9 0 1 0 0-1.8H7.2a.9.9 0 1 0 0 1.8zM21.6 16.2a.9.9 0 0 0-.9-.9H7.2a.9.9 0 1 0 0 1.8h13.5a.9.9 0 0 0 .9-.9z" />
                                <path d="M49 3.6H27.9V.9a.9.9 0 1 0-1.8 0v2.7H5a5 5 0 0 0-5 5v27.8a5 5 0 0 0 5 5h19.827L13.764 52.464a.899.899 0 1 0 1.272 1.272L26.1 42.673V51.3a.9.9 0 1 0 1.8 0v-8.627l11.064 11.063a.898.898 0 0 0 1.272 0 .899.899 0 0 0 0-1.272L29.173 41.4H49a5 5 0 0 0 5-5V8.6a5 5 0 0 0-5-5zm-.8 36H5.8a4 4 0 0 1-4-4V9.4a4 4 0 0 1 4-4h42.4a4 4 0 0 1 4 4v26.2a4 4 0 0 1-4 4z" />
                                <path d="M36.9 18h4.127L30.24 28.787l-7.464-7.463a.899.899 0 0 0-1.272 0l-11.34 11.34a.899.899 0 1 0 1.272 1.272L22.14 23.233l7.464 7.463a.898.898 0 0 0 1.272 0L42.3 19.273V23.4a.9.9 0 1 0 1.8 0v-6.3a.897.897 0 0 0-.9-.9h-6.3a.9.9 0 1 0 0 1.8z" />
                            </g>
                        </svg>
                    </span>
                    <span class="bs-stepper-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_confirm'); ?></span>
                </button>
            </div>
        </div>
        <div class="bs-stepper-content border-top px-0">
            <form action="" method="POST" id="wizard-checkout-form">
                <div id="checkout-cart" class="content active">
                    <div class="row">
                        <!-- Cart left -->
                        <div class="col-lg-9 mb-3 mb-lg-0">

                            <!-- Shopping bag -->
                            <h5 class="mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_count'); ?> (<?php echo $all_items; ?>)</h5>
                            <ul class="list-group mb-3">
                                <?php
                                if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    foreach ($cart as $item_id => $item_count) :
                                        $shop_item = new product($item_id);
                                        $title = $shop_item->get_title();
                                        $cat = $shop_item->get_cats()[0];
                                        $cat = new tag($cat['tag_id']);
                                        $cat_url = $cat->get_url();
                                        $cat_name = $cat->get_name();
                                        $product_parent = $shop_item->get_parent();
                                        $stock_status = $shop_item->get_stock_status();
                                        $regular_price = intval($shop_item->get_regular_price()) * $item_count;
                                        $sale_price = intval($shop_item->get_sale_price()) * $item_count;
                                        if ($product_parent > 0) {
                                            $parent = new product($product_parent);
                                            $url = $parent->get_url();
                                            $thumbnail = $parent->get_thumbnail_src();
                                            $cat = $parent->get_cats()[0];
                                            $cat = new tag($cat['tag_id']);
                                            $cat_url = $cat->get_url();
                                            $cat_name = $cat->get_name();
                                        } else {
                                            $url = $shop_item->get_url();
                                            $thumbnail = $shop_item->get_thumbnail_src();
                                        }
                                ?>
                                        <li class="list-group-item p-3 position-relative">
                                            <button type="submit" value="<?php echo $item_id; ?>" name="delete" class="close btn-pinned p-2" aria-label="Close" style="left:unset;right:0;top:0"></button>
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <a href="<?php echo $url; ?>"><img width="100" src="<?php echo $thumbnail ?>" class="product_cart_thumbnail" alt="" loading="lazy"></a>
                                                </div>
                                                <div class="col-6 col-md-5">
                                                    <h6 class="fw-normal mb-1 ml-3">
                                                        <a href="<?php echo $url; ?>" class="text-body"><?php echo $title; ?></a>
                                                    </h6>
                                                    <div class="text-muted mb-1">
                                                        <small><?php echo $functions->get_language($_SESSION['lang'], $stock_status) ?></small>
                                                    </div>
                                                    <div class="d-inline-flex align-items-center">
                                                        <button class="btn btn-primary btn-sm ml-2 icon-minus" type="submit" value="<?php echo $item_id; ?>" name="minus-item"></button>
                                                        <span><?php echo $functions->get_language($_SESSION['lang'], 'cart_item_count'); ?> <?php echo number_format($item_count); ?></span>
                                                        <button class="btn btn-primary btn-sm mr-2 icon-plus" type="submit" value="<?php echo $item_id; ?>" name="plus-item"></button>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-4">
                                                    <div class="text-left">
                                                        <div class="mb-3">
                                                            <?php if ($sale_price > 0) : ?>
                                                                <span class="text-primary d-block d-sm-inline"><?php echo number_format($sale_price) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?> / </span><s class="text-muted"><?php echo number_format($regular_price) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?></s>
                                                            <?php else : ?>
                                                                <span class="text-primary"><?php echo number_format($regular_price) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="ml-1 d-none d-sm-inline"><?php echo $functions->get_language($_SESSION['lang'], 'cart_item_category') ?></span>
                                                        <a href="<?php echo $cat_url; ?>" class="ml-1"><?php echo $cat_name; ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    <?php endforeach;
                                } else { ?>
                                    <span class="error-text"><?php echo $functions->get_language($_SESSION['lang'], 'empty_cart'); ?></span>
                                <?php } ?>
                            </ul>

                            <!-- Wishlist -->
                            <?php if (is_countable($_SESSION['cart']) && $recomandeds) : ?>
                                <div class="list-group accordion list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_recommandeds'); ?></span>
                                        <i class="icon-3"></i>
                                    </div>
                                    <div class="accordion-items">
                                        <?php
                                        $recomandeds = explode(',', $recomandeds);
                                        foreach ($recomandeds as $recomanded) :
                                            $recomanded = new product($recomanded);
                                            $product_id = $recomanded->get_id();
                                            $price_sale_product_part = intval($recomanded->get_sale_price());
                                            $price_product_part = intval($recomanded->get_regular_price());
                                            $title = $recomanded->get_title();
                                            $thumbnail = $recomanded->display_post_image();
                                            $url = $recomanded->get_url();
                                            if ($price_product_part == '')
                                                $price_product_part = 0;
                                            $stock_status = $recomanded->get_stock_status();
                                            if ($price_sale_product_part > 0) :
                                                $price = "<div><span class=\"text-primary\">" . number_format($price_sale_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . " / </span><s class=\"text-muted\">" . number_format($price_sale_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . "</s></div>";
                                            else :
                                                $price = "<span class=\"text-primary\">" . number_format($price_sale_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . "</span>";
                                            endif;
                                            $variant = $functions->fetchassoc("SELECT `post_id` FROM `post` WHERE `post_type` = 'product' AND `post_parent` = $product_id");
                                            $button = "<button type=\"submit\" name=\"add-product\" value=\"" . $product_id . "\" class=\"btn btn-sm btn-primary\">" . $functions->get_language($_SESSION['lang'], 'add_to_cart') . "</button>";
                                            if ($stock_status == 'outofstock')
                                                $button = "<span class=\"button disabled\">" . $functions->get_language($_SESSION['lang'], 'outofstock') . "</span>";
                                            if ($stock_status == 'call' || $price == 0)
                                                $button = "<a href=\"tel:" . $functions->get_option('phone') . "\" class=\"button\">" . $functions->get_language($_SESSION['lang'], 'product_call') . "</a>";
                                            if (is_countable($variant) && count($variant)) {
                                                $price = '';
                                                $button = "<a href='$url' class='button'>" . $functions->get_language($_SESSION['lang'], 'view_product') . "</a>";
                                            } ?>
                                            <div class="accordion-item row pt-3">
                                                <a class="col-4 col-md-3 ml-0" href="<?php echo $url; ?>"><img width="100px" src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>"></a>
                                                <div class="col-8 col-md-5">
                                                    <a href="<?php echo $url; ?>" class="text-body"><?php echo $title; ?></a>
                                                    <small class="text-muted"><?php echo $functions->get_language($_SESSION['lang'], $stock_status); ?></small>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <?php echo $price; ?>
                                                    <?php echo $button; ?>
                                                </div>
                                            </div>
                                        <?php endforeach;
                                        ?>
                                    </div>
                                </div>
                            <?php endif;
                            if (is_countable($wishlist) && count($wishlist) > 0) : ?>
                                <div class="list-group accordion list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_favorits'); ?></span>
                                        <i class="icon-3"></i>
                                    </div>
                                    <div class="accordion-items">
                                        <?php foreach ($wishlist as $wish) :
                                            $wish = new product($wish['post_id']);
                                            $product_id = $wish->get_id();
                                            $price_sale_product_part = intval($wish->get_sale_price());
                                            $price_product_part = intval($wish->get_regular_price());
                                            $title = $wish->get_title();
                                            $thumbnail = $wish->display_post_image();
                                            $url = $wish->get_url();
                                            if ($price_product_part == '')
                                                $price_product_part = 0;
                                            $stock_status = $wish->get_stock_status();
                                            if ($price_sale_product_part > 0) :
                                                $price = "<div><span class=\"text-primary\">" . number_format($price_sale_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . " / </span><s class=\"text-muted\">" . number_format($price_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . "</s></div>";
                                            else :
                                                $price = "<span class=\"text-primary\">" . number_format($price_product_part) . " " . $functions->get_language($_SESSION['lang'], 'currency') . "</span>";
                                            endif;
                                            $variant = $functions->fetchassoc("SELECT `post_id` FROM `post` WHERE `post_type` = 'product' AND `post_parent` = $product_id");
                                            $button = "<button type=\"submit\" name=\"add-product\" value=\"" . $product_id . "\" class=\"btn btn-sm btn-primary\">" . $functions->get_language($_SESSION['lang'], 'add_to_cart') . "</button>";
                                            if ($stock_status == 'outofstock')
                                                $button = "<span class=\"btn btn-sm disabled\">" . $functions->get_language($_SESSION['lang'], 'outofstock') . "</span>";
                                            if ($stock_status == 'call' || $price == 0)
                                                $button = "<a href=\"tel:" . $functions->get_option('phone') . "\" class=\"btn btn-sm btn-success\">" . $functions->get_language($_SESSION['lang'], 'product_call') . "</a>";
                                            if (is_countable($variant) && count($variant)) {
                                                $price = '';
                                                $button = "<a href='$url' class='btn btn-sm btn-primary'>" . $functions->get_language($_SESSION['lang'], 'view_product') . "</a>";
                                            } ?>
                                            <div class="accordion-item row pt-3">
                                                <a class="col-4 col-md-3 ml-0" href="<?php echo $url; ?>"><img width="100px" src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>" class="w-px-100"></a>
                                                <div class="col-8 col-md-5">
                                                    <a href="<?php echo $url; ?>" class="text-body"><?php echo $title; ?></a>
                                                    <small class="text-muted"><?php echo $functions->get_language($_SESSION['lang'], $stock_status); ?></small>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <?php echo $price; ?>
                                                    <?php echo $button; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Cart right -->
                        <div class="col-lg-3">
                            <div class="border rounded p-3 mb-3">
                                <!-- Offer -->
                                <h6 class="secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_coupon'); ?></h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <input type="text" name="coupon-code" value="<?php if ($_SESSION['coupon_code']) echo $_SESSION['coupon_code']; ?>" class="form-control w-100" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'cart_form_coupon_placeholder'); ?>" aria-label="<?php echo $functions->get_language($_SESSION['lang'], 'cart_form_coupon_placeholder'); ?>">
                                    </div>
                                    <div class="col-12 d-flex">
                                        <button type="submit" name="coupon" class="btn btn-primary w-100 mt-3"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_coupon_submit'); ?></button>
                                        <?php if ($_SESSION['coupon_code']) { ?>
                                            <button type="submit" name="coupon" class="btn btn-danger w-100 mt-3 mr-3"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_coupon_remove'); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-next"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_next_step'); ?></button>
                        </div>
                    </div>
                </div>

                <div id="checkout-address" class="content">
                        <?php if (isset($_SESSION['user_info']["uid"]) && is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                    <div class="row">
                        <div class="col-lg-9 mb-3 mb-lg-0">
                            <h5><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_address'); ?></h5>
                            <div class="row mb-3" id="address-wrapper">
                                <?php if (is_countable($addresses) && count($addresses) > 0) {
                                    foreach ($addresses as $address) { ?>
                                        <div class="col-8 mx-auto col-lg-4 mb-md-0 mb-2">
                                            <div class="form-check custom-option custom-option-basic <?php if ($address['city'] == $addresses[0]['city']) echo 'checked'; ?>">
                                                <div class="form-check-label custom-option-content">
                                                    <input required name="address" class="form-check-input" type="radio" value='<?php echo json_encode($address, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>' id="customRadioAddress1" <?php if ($address['city'] == $addresses[0]['city']) echo 'checked'; ?>>
                                                    <span class="custom-option-header">
                                                        <span class="h6 mb-0"><?php echo $address['name']; ?></span>
                                                    </span>
                                                    <span class="custom-option-body">
                                                        <small><?php echo $address['state']; ?> - <?php echo $address['city']; ?><br><?php echo $address['address']; ?> - <?php echo $address['postcode']; ?></small>
                                                        <small class="d-flex mt-3">
                                                            <a class="ml-2 btn btn-success btn-sm" onclick="showAddress('<?php echo $address['name']; ?>')"><?php echo $functions->get_language($_SESSION['lang'], 'address_edit'); ?></a>
                                                            <a class="ml-2 btn btn-danger btn-sm" onclick="removeAddress('<?php echo $address['name']; ?>',this)"><?php echo $functions->get_language($_SESSION['lang'], 'address_delete'); ?></a>
                                                        </small>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php }
                                } ?>
                            </div>
                            <button type="button" class="btn btn-primary mb-4" onclick="$('#showFactor input').val('');$('#showFactor').fadeIn();$('#showFactor').addClass('show');"><?php echo $functions->get_language($_SESSION['lang'], 'address_add'); ?></button>
                            <h5><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_reciever'); ?></h5>
                            <div class="row mb-3">
                                <div class="mb-3 col-12 col-md-6">
                                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_fullname'); ?></label>
                                    <input name="fullname" class="form-control w-100" type="text" value="<?php echo $firstname . ' ' . $lastname; ?>" required>
                                </div>
                                <div class="mb-3 col-12 col-md-6">
                                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_phonenumber'); ?></label>
                                    <input name="phonenumber" class="form-control w-100" type="tel" value="<?php echo $phonenumber; ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_note'); ?></label>
                                    <textarea name="notes" class="form-control w-100"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Address right -->
                        <div class="col-lg-3">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_paymethode'); ?></h6>
                                <ul class="list-unstyled mb-4">
                                    <p class="mb-1 d-flex"><input id="post" onclick="$(this).removeAttr('checked');change_final_price(this);" type="radio" class="ml-1" name="shipping" value="post" title="<?php echo $s_post; ?>" required checked=""> پست (<?php echo number_format($s_post) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?>)</p>
                                    <p id="delivery" class="d-flex"><input onclick="$(this).removeAttr('checked');change_final_price(this);" type="radio" class="ml-1" name="shipping" value="peyk" title="<?php echo $s_delivery; ?>"> پیک (<?php echo number_format($s_delivery) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?>)</p>
                                </ul>
                                <h6 class="secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_shipment'); ?></h6>
                                <ul class="list-unstyled">
                                    <p class="mb-1">
                                        <input required style="width: unset !important;vertical-align:middle;" type="radio" id="gateway" class="ml-1" name="payment" value="online" checked>درگاه بانکی
                                    </p>
                                    <p style="display: none" id="darmahal">
                                        <input style="width: unset !important;vertical-align:middle;" type="radio" class="ml-1" name="payment" value="recived">پرداخت در محل
                                    </p>
                                </ul>

                                <hr class="mx-n3">

                                <!-- Price Details -->
                                <h6 class="secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_details'); ?></h6>
                                <dl class="row mb-0">
                                    <dt class="col-6 fw-normal"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_order_sum'); ?></dt>
                                    <dd class="col-6 text-end"><?php echo number_format($cart_total) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?></dd>

                                    <dt class="col-6 fw-normal"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_shipment_price'); ?></dt>
                                    <dd class="col-6 text-end">
                                        <?php if ($cart_total >= $free_delivery) : ?>
                                            <s id="delivery_price"><?php echo number_format($s_post) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?></s> <span class="badge bg-label-success"><?php echo $functions->get_language($_SESSION['lang'], 'free'); ?></span>
                                        <?php else : ?>
                                            <span id="delivery_price"><?php echo number_format($s_post) . ' ' . $functions->get_language($_SESSION['lang'], 'currency'); ?></span>
                                        <?php endif; ?>
                                    </dd>

                                    <hr>

                                    <dt class="col-6"><?php echo $functions->get_language($_SESSION['lang'], 'cart_form_sum'); ?></dt>
                                    <dd class="col-6 fw-semibold text-end mb-0"><span id="final_price"><?php echo number_format($final_price); ?></span> <?php echo $functions->get_language($_SESSION['lang'], 'currency'); ?></dd>
                                </dl>
                            </div>
                            <div class="d-grid">
                                <label class="form-label mb-4 w-100" style="text-align: right;cursor:pointer">
                                    <input style="width: 15px !important;height: 15px;display:inline-block;vertical-align: middle;margin-left:10px;" required type="checkbox" name="rules">موافقت با <a onclick="$('#rules').fadeIn();$('#rules').addClass('open');"><?php echo $functions->get_language($_SESSION['lang'], 'cart_rules') ?></a>
                                </label>
                                <button type="submit" name="pay" class="btn btn-primary">ثبت سفارش</button>
                            </div>
                        </div>
                    </div>
                <?php elseif (!isset($_SESSION['user_info']["uid"])) : ?>
                            <div class="error" style="margin-bottom: 50px; text-align: center;">
                                <h1 class="my-4" style="text-align: center;font-size:22px;"><?php echo $functions->get_language($_SESSION['lang'], 'cart_login'); ?></h1>
                                <span class="button cart-login"> <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-in'); ?> / <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-up'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="checkout-payment" class="content"></div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade " id="showFactor" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-xl my-4" role="document">
        <div class="modal-content p-4">
            <div class="row">
                <div class="col-md-6 ml-auto mb-3">
                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'address_title'); ?></label>
                    <input id="title" class="form-control" type="text">
                </div>
                <div class="col-md-6 ml-auto mb-3">
                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_postcode'); ?></label>
                    <input id="postcode" class="form-control" type="text">
                </div>
                <div class="ir-select col-12 col-md-12 mb-12 row">
                    <label class="form-label col-3 col-md-6 mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_providence'); ?>
                        <div class="form-items">
                            <select required id="providence" onChange="check_city(this);" class="ir-province form-control js-example-basic-single">
                                <option value="تهران" selected>تهران</option>
                            </select>
                        </div>
                    </label>
                    <label class="form-label col-3 col-md-6 mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_city'); ?>
                        <div class="form-items">
                            <select onChange="check_city(this);" class="ir-city form-control js-example-basic-single" required id="city">
                                <option disbaled><?php echo $functions->get_language($_SESSION['lang'], 'select_city'); ?></option>
                            </select>
                        </div>
                    </label>
                </div>
                <div class="mb-3 col-12">
                    <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_address'); ?></label>
                    <textarea id="address" class="form-control"></textarea>
                </div>
            </div>
            <p id="form-errors" class="text-danger m-0 d-none"></p>
            <div class="d-flex mt-3">
                <button id="submit-address" type="button" class="btn btn-primary" onclick="addAddress()"><?php echo $functions->get_language($_SESSION['lang'], 'save_address'); ?></button>
                <button type="button" class="btn btn-primary mr-3" onclick="$('#showFactor').fadeOut();$('#showFactor').removeClass('show');"><?php echo $functions->get_language($_SESSION['lang'], 'factor_close'); ?></button>
            </div>

        </div>
    </div>
</div>
<div class="muodal rules" id="rules">
        <div class="container">
            <div class="cm-contact" style="overflow: auto; max-height: 80vh;">
                <?php echo $functions->get_option('rules_text'); ?>
            </div>
            <div class="d-flex mt-3">
                <button type="button" class="btn btn-primary mr-3" onclick="$('#rules').removeClass('open');"><?php echo $functions->get_language($_SESSION['lang'], 'factor_close'); ?></button>
            </div>
        </div>
    </div>
<!--/ Checkout Wizard -->
<script src="/themes/aso/includes/assets/js/FormValidation.min.js"></script>
<script src="/themes/aso/includes/assets/js/Bootstrap5.min.js"></script>
<script src="/themes/aso/includes/assets/js/AutoFocus.min.js"></script>
<script src="/themes/aso/includes/assets/js/bs-stepper.js"></script>
<script src="/themes/aso/includes/assets/js/wizard-ex-checkout.js"></script>
<script src="/themes/aso/includes/assets/js/ir-city-select.min.js"></script>
<script src="/themes/aso/includes/assets/js/select2.full.min.js"></script>
<script src="/themes/aso/includes/assets/js/select2-custom.js"></script>

<script>
    function showAddress(address_title) {
        getJSON('/themes/aso/includes/API/v1/getAddress.php?name=' + address_title, function(err, data) {
            if (data != null) {
                $('#title').val(address_title);
                $('#address').val(data['address']);
                $('#postcode').val(data['postcode']);
                $.each($('#showFactor select'), function(i, obj) {
                    if ($(obj).data('select2')) {
                        $(obj).select2('destroy');
                    }
                });
                $('#providence').append('<option value="' + data['state'] + '" selected>' + data['state'] + '</option>');
                $('#city').append('<option value="' + data['city'] + '" selected>' + data['city'] + '</option>');
                $("#showFactor select").select2();
                $('#submit-address').attr('onclick', 'addAddress("' + address_title + '");');
                $('#form-errors').text('');
                $('#form-errors').addClass('d-none');
                $('#showFactor').fadeIn();
                $('#showFactor').addClass('show');
            }
        });
    };

    function removeAddress(address_title, element) {
        getJSON('/themes/aso/includes/API/v1/removeAddress.php?name=' + address_title, function(err, data) {
            if (data != null) {
                $('#showFactor input').val('');
                $('#showFactor select').val('');
                $('#submit-address').attr('onclick', 'addAddress();');
                $(element).closest('.col-lg-4').fadeOut();
                $(element).closest('.col-lg-4').remove();
            }
        });
    };

    function addAddress(name) {
        var title = $('#title').val();
        var providence = $('#providence').val();
        var city = $('#city').val();
        var address = $('#address').val();
        var postcode = $('#postcode').val();
        if (title.length < 1) {
            $('#form-errors').text('<?php echo $functions->get_language($_SESSION['lang'], 'address_title_error'); ?>');
            $('#form-errors').removeClass('d-none');
            $('#title').focus();
            return;
        } else if (postcode.length < 1) {
            $('#form-errors').text('<?php echo $functions->get_language($_SESSION['lang'], 'account_edit_postcode_error'); ?>');
            $('#form-errors').removeClass('d-none');
            $('#postcode').focus();
            return;
        } else if (providence.length < 1) {
            $('#form-errors').text('<?php echo $functions->get_language($_SESSION['lang'], 'account_edit_providence_error'); ?>');
            $('#form-errors').removeClass('d-none');
            $('#providence').focus();
            return;
        } else if (city.length < 1) {
            $('#form-errors').text('<?php echo $functions->get_language($_SESSION['lang'], 'account_edit_city_error'); ?>');
            $('#form-errors').removeClass('d-none');
            $('#city').focus();
            return;
        } else if (address.length < 1) {
            $('#form-errors').text('<?php echo $functions->get_language($_SESSION['lang'], 'account_edit_address_error'); ?>');
            $('#form-errors').removeClass('d-none');
            $('#address').focus();
            return;
        } else {
            $('#form-errors').text('');
            $('#form-errors').addClass('d-none');
        }
        if (title.length > 0) {
            getJSON('/themes/aso/includes/API/v1/addAddress.php?name=' + name + '&title=' + title + '&providence=' + providence + '&city=' + city + '&address=' + address + '&postcode=' + postcode, function(err, data) {
                if (data != null) {
                    $('#address-wrapper .checked input').prop('checked', false);
                    $('#address-wrapper .checked').removeClass('checked');
                    $('#address-wrapper input').prop('checked', false);
                    location.reload();
                }
            });
        }
    };
    $('.btn-next').click(function() {
        $('div[data-target="#checkout-address"] button').trigger('click');
    });
    $('.rules').click(function(e) {
        if (!e.target.className == 'container' || !$(e.target).parents('.rules').length) {
            $(this).removeClass('open');
        }
    });



    const before_shipping_price = parseInt(<?php echo $cart_total; ?>);
    const c_price = <?php echo $price_coupon; ?>;
    if (c_price) {
        const coupon_price = parseInt(c_price);
    }

    function change_final_price(element) {
        var post_price = parseInt(element.getAttribute("title"));
        if (c_price) {
            var final_price = before_shipping_price + post_price - coupon_price;
        } else {
            var final_price = before_shipping_price + post_price
        }
        const delivery = document.getElementById("delivery_price");
        const final = document.getElementById("final_price");
        final.innerHTML = new Intl.NumberFormat().format(final_price);
        delivery.innerHTML = new Intl.NumberFormat().format(post_price) + " <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?> ";

        if (element.id == "post") {
            let cod = document.getElementById("darmahal");
            cod.style.display = "none";

            let gateway = document.getElementById("gateway");
            gateway.checked = true;

        } else {
            let cod = document.getElementById("darmahal");
            cod.style.display = "block";
            let gateway = document.getElementById("gateway");
            gateway.checked = true;
        }

    }
</script>
<?php include_once('footer.php'); ?>