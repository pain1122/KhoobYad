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
$coupon_error = "";
$final_price = 0;

if (isset($_POST['minus-item'])) {
    $p_id = $_POST['prod_id'];
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
    $p_id = $_POST['prod_id'];
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

if (isset($_POST['delete-coupon'])) {
    unset($_SESSION['coupon_code']);
    $price_coupon = 0;
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}

if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart = $_SESSION['cart'];
    $cart_total = 0;

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
            $count += $item_count;
            $cart_total += $price_total;
        }
    endforeach;
    $final_price = $cart_total;

    $recomandeds = '';
	foreach ($cart as $item_id => $item_count) :
		$complementaries = $obj->get_meta( 'complementaries');
		if (strlen($recomandeds) == 0)
			$recomandeds = $complementaries;
		else
			$recomandeds = $recomandeds . ',' . $complementaries;
		$recomandeds = str_replace(',,',',',$recomandeds);
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
            $coupon_for = $obj->get_meta( 'user-id');
            $min_price = $obj->get_meta( 'min_price');
            $uses = intval($obj->get_meta( 'uses'));
            if ($status == 'publish') {
                if (!empty($min_price) && $final_price >= $min_price) {
                    if (! empty($coupon_for)) {
                        if ($coupon_for == $user_id && $select_uses < $uses) {
                            $_SESSION['coupon_code'] = $code;
                            $price_coupon = $obj->get_meta( 'coupon');
                            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_success');
                            $functions->RunQuery("INSERT INTO `user_meta` (`user_id`,`key`,`value`) VALUE ($user_id,'coupon','$code')");
                        } else {
                            $coupon_error = $functions->get_language($_SESSION['lang'], 'coupon_use_resricted');
                        }
                    } elseif (!empty($uses) && $select_uses < $uses) {
                        $_SESSION['coupon_code'] = $code;
                        $price_coupon = $obj->get_meta( 'coupon');
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
    $coupon_uses = $obj->get_meta( 'uses');
    $price_coupon = $obj->get_meta( 'coupon');
    $expire = $obj->get_meta( 'expire-time');
    $coupon_uid = $obj->get_meta( 'user-id');
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
        $postal_code = $_POST['postal_code'];
        $notes = $_POST['notes'];
        $payment = $_POST['payment'];
        $days = $_POST['days'];
        $providence = $_POST['providence'];
        $city = $_POST['city'];
        if(!$providence || $providence == '' || $providence == 'empty')
            $providence = 'تهران';
        if(!$city || $city == $functions->get_language($_SESSION['lang'], 'choose_city'))
            $city = 'تهران';
        $address = $_POST['address'];
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
                if ($get_seller_char == "I")
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
        $obj = new order('new);
        $order_id = $obj->get_id();
        $obj->set_author($user_id);
        $obj->set_title($phonenumber.'-'. $fullname);
        $obj->set_status('failed');
        $obj->set_excerpt($factor_seller);

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
            'shipping' => $shipping,
            'days' => $days
        );

        $obj->insert_meta($post_meta_content);


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
                $obj->set_sum($pay);
                unset($_SESSION['cart'][$item_id]);
            } else {
                $obj->set_item(['id' => $item_id,'qty' => $item_count,'price' => $price_id, 'total' => $count_price_id,'off' => $off,'coupon_id' => $coupon_id]);
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
            $obj->set_sum(0);
            unset($_SESSION['coupon_code']);
            unset($_SESSION['cart']);
            unset($_SESSION['local']);
            unset($_SESSION['final_price']);
            $functions->redirect(site_url.'orders');
        }
    }
}

if (isset($_POST['delete'])) {
    unset($_SESSION['cart'][$_POST['prod']]);
    $code = $_SESSION['coupon_code'];
    unset($_SESSION['coupon_code']);
    $price_coupon = 0;
    $functions->RunQuery("DELETE FROM `user_meta` WHERE `user_id` = $user_id AND `key` = 'coupon' AND `value` = '$code'");
}
if ($cart_total >= $free_delivery) : 
    $s_post = 0;
    $s_delivery = 0;
else : 
    $s_post = $functions->get_option('s_post');
    $s_delivery = $functions->get_option('s_delivery');
endif;
?>

<div class="muodal rules">
        <div class="container" style="overflow: auto; max-height: 800px;">
            <a href="/"><img src="<?php echo site_url.upload_folder; ?>img_40369153Asokala-min.webp"></a>
            <div class="cm-contact">
            <?php echo $functions->get_option('rules_text'); ?>
            </div>
        </div>
    </div>
<script>
    var delivery = <?php echo $s_post ?>;
</script>
<link rel="stylesheet" href="/themes/aso/includes/assets/css/select2.css" />
<section class="cart-page">
    <div class="container">
        <h1 class="header_form"><?php echo $functions->get_language($_SESSION['lang'], 'cart_page_title') ?></h1>
        <div class="cart-page">
            <div class="product-cart" <?php if (!is_countable($_SESSION['cart'])) echo 'style="border: none; margin-bottom: 50px;;"'; ?>>
                <table class="shop_table_product">
                    <thead>
                        <tr>
                            <?php if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                                <th class="product-remove"></th>
                                <th class="product-thumbnail">تصویر</th>
                                <th class="product-name">نام محصول</th>
                                <th class="product-price">قیمت</th>
                                <th class="product-quantity">تعداد</th>
                                <th class="product-subtotal">جمع کل</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            foreach ($cart as $item_id => $item_count) :
                                $list_query = "SELECT `post_id` FROM `post` WHERE `post_id` = $item_id";
                                $item_id = $functions->Fetchassoc($list_query)['post_id'];
                                if($item_id){
                                    $cart = new product($item_id);
                                    $url = $cart->get_url();
                                    $title = $cart->get_title();
                                    $price = $cart->get_price();
                                    $stock_status = $cart->get_status();
                                    $parent = $cart->get_parent();
                                    if ($stock_status == 'outofstock') {
                                        unset($_SESSION['cart'][$item_id]);
                                        unset($cart[$item_id]);
                                        break;
                                    } else {
                                        $price = $cart->get_price();
                                        if ($parent > 0) {
                                            $cart = new product($parent);
                                            $url = $cart->get_url();
                                            $thumbnail = $cart->display_post_image();
                                        } else {
                                            $thumbnail = $cart->display_post_image();
                                        }
                                        $count += $item_count;
                                    }
                                ?>
                                    <tr class="cart_item">
                                        <td class="product-remove">
                                            <form action="" method="post" class="">
                                                <input type="hidden" name="prod" value="<?php echo $item_id ?>">
                                                <input type="submit" value="X" name="delete" class="remove remove_from_cart_button">
                                            </form>

                                        </td>
                                        <td class="product-thumbnail">
                                            <a href="<?php echo $url; ?>"><img width="60" height="60" src="<?php echo $thumbnail ?>" class="product_cart_thumbnail" alt="" loading="lazy"></a>
                                        </td>
                                        <td class="product-name">
                                            <a href="<?php echo $url ?>"><?php echo $title ?></a>
                                        </td>
                                        <td class="product-price">
                                            <span class="cart-Price-amount">
                                                <?php echo number_format($price); ?>
                                                <span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></span>
                                        </td>

                                        <td class="product-quantity">
                                            <span class="count">
                                                <form method="post" action="" style="display: flex;justify-content: space-between;align-items: center;width: 100%;">
                                                    <button class="button" type="submit" name="plus-item">+</button>
                                                    <span><?php echo number_format($item_count); ?> عدد</span>
                                                    <button class="button" type="submit" name="minus-item">-</button>
                                                    <input type="hidden" name="prod_id" value="<?php echo $item_id; ?>">
                                                </form>
                                            </span>

                                        </td>

                                        <td class="product-subtotal">
                                            <span class="cart-Price-amount">
                                                <?php echo number_format($price * $item_count); ?><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></span>
                                        </td>
                                    </tr>
                                <?php }
                            endforeach;
                        } else { ?>
                            <span class="error-text"><?php echo $functions->get_language($_SESSION['lang'],'empty_cart'); ?></span>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
            <?php if ($recomandeds) : ?>
                <div class="mt-5" <?php if (is_countable($_SESSION['cart']) && !count($_SESSION['cart']) > 0) echo 'style="border: none; margin-bottom: 50px;;"'; ?>>
                    <div class="row">
                        <div class="col-12 sec-title">
                            <h2><?php echo $functions->get_language($_SESSION['lang'], 'recomanded_products'); ?></h2>
                        </div>
                        <div class="col-12">
                            <div class="product-slider owl-carousel">
                                <?php
                                $recomandeds = explode(',',$recomandeds);
                                foreach ($recomandeds as $product) :
                                    $product = new product($product);
                                    include('product-part.php');
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php
            if (isset($_SESSION['user_info']["uid"]) && is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>

                <form method="post" class="coupon-form" action="">
                    <div class="form-group-coupon-label">
                        <label><?php echo $functions->get_language($_SESSION['lang'],'cart_coupon_question'); ?></label>
                    </div>
                    <div class="form-group-coupon">
                        <input class="form-group-coupon-code" placeholder="<?php echo $functions->get_language($_SESSION['lang'],'cart_coupon_placeholder'); ?>" name="coupon-code" type="text" value="<?php if ($_SESSION['coupon_code']) echo $_SESSION['coupon_code']; ?>">
                        <button class="button" type="submit" name="coupon"><?php echo $functions->get_language($_SESSION['lang'],'cart_coupon_submit'); ?></button>
                        <?php if (isset($_SESSION['coupon_code'])) : ?>
                            <button class="button delete-coupon" type="submit" name="delete-coupon"><?php echo $functions->get_language($_SESSION['lang'],'cart_coupon_remove'); ?></button>
                        <?php endif; ?>
                    </div>
                    <p class="coupon-error"><?php echo $coupon_error; ?></p>
                </form>
                <form role="form" action="" method="POST">
                    <div class="info-cart">
                        <table cellspacing="0" class="shop_table">
                            <tbody>
                                <tr class="cart-subtotal">
                                    <th>قیمت</th>
                                    <td data-title="قیمت"><span class="Price-amount">
                                            <?php echo number_format($cart_total); ?><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>
                                        </span></td>
                                </tr>
                                <tr class="cart-subtotal">
                                    <th>هزینه ارسال</th>
                                    <td class="d-flex flex-wrap" data-title="هزینه ارسال">
                                        <p class="ml-3"><input id="post" onclick="$(this).removeAttr('checked');change_final_price(this);" type="radio" class="ml-1" name="shipping" value="post" title="<?php echo $s_post ?>" checked> پست (<?php echo $s_post ?> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?>)</p>
                                        <p id="delivery"><input onclick="$(this).removeAttr('checked');change_final_price(this);" type="radio" class="ml-1" name="shipping" value="peyk" title="<?php echo $s_delivery ?>"> پیک (<?php echo $s_delivery ?> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?>)</p>
                                    </td>
                                </tr>
                                <?php if (isset($_SESSION['coupon_code'])) : ?>
                                    <tr class="cart-subtotal">
                                        <th>تخفیف کوپن</th>
                                        <td data-title="تخفیف کوپن">
                                            <span id="coupon-price" class="Price-amount">
                                                <?php echo number_format($price_coupon); ?><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="order-total">
                                    <th>مجموع</th>
                                    <td data-title="مجموع"><strong><span id="final_price" class="Price-amount">
                                                <?php
                                                if ($final_price > 0) :
                                                    echo number_format($final_price);
                                                ?>
                                            </span><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></strong>
                                    <?php else : ?>
                                        <span class="Price-currencySymbol">رایگان</span></strong>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-info-pay row mt-5">
                        <!--action -> verify-->
                        <label class="form-label col-12 col-md-6 mb-4" style="text-align: right;">نام و نام خانوادگی
                            <div class="form-items">
                                <input class="form-control" required name="fullname" type="text" placeholder="نام و نام خانوادگی خود را وارد کنید" value="<?php echo $firstname . ' ' . $lastname?>">
                            </div>
                        </label>
                        <label class="form-label col-12 col-md-6 mb-4" style="text-align: right;">تلفن
                            <div class="form-items">
                                <input class="form-control" onchange="replace_digits(this)" type="text" required name="phone" placeholder="شماره تلفن خود را وارد کنید" value="<?php echo $phonenumber ?>">
                            </div>
                        </label>
                        <div class="ir-select col-12 col-md-12 mb-12 row">
                            <label class="form-label col-3 col-md-6 mb-3" style="text-align: right;">انتخاب استان
                                <div class="form-items">
                                    <select required name="providence" onChange="check_city(this);" class="ir-province form-control js-example-basic-single"><option value="تهران" selected>تهران</option></select>
                                </div>
                            </label>
                            <label class="form-label col-3 col-md-6 mb-3" style="text-align: right;">انتخاب شهر
                                <div class="form-items">
                                    <select onChange="check_city(this);" class="ir-city form-control js-example-basic-single" required name="city" id="providence">
                                    <option value="تهران" selected>تهران</option>
                                        <option disbaled>انتخاب شهر</option>
                                    </select>
                                </div>
                            </label>
                        </div>
                        <label class="form-label col-12 mb-4" style="text-align: right;"> آدرس با ذکر شهر
                            <div class="form-items">
                                <textarea name="address" id="" required rows="2" placeholder="آدرس کامل به همراه شهر خود را وارد کنید"><?php echo $address ?></textarea>
                            </div>
                        </label>
                        <label class="form-label col-12 col-md-4 mb-4" style="text-align: right;">کد پستی
                            <div class="form-items">
                                <input class="form-control" type="text" name="postal_code" placeholder="کد پستی خود را وارد کنید" value="<?php echo $postal_code ?>">
                            </div>
                        </label>
                        <label class="form-label col-12 col-md-4 mb-4" style="text-align: right;">طریقه پرداخت
                            <div class="form-items d-flex align-items-center py-2">
                                <p class="ml-3 d-flex align-items-center"><input style="width: unset !important;" type="radio" id="gateway" class="ml-1" name="payment" required value="online" checked>درگاه بانکی</p>
                                <div style="display: none" id="darmahal">
                                    <p class="d-flex align-items-center"><input style="width: unset !important;" type="radio" class="ml-1" name="payment" value="recived">پرداخت در محل</p>
                                </div>
                            </div>
                        </label>
                        <label class="form-label col-12 mb-4" style="text-align: right;"> یادداشت
                            <div class="form-items">
                                <textarea name="notes" id="" rows="4" placeholder="متن یادداشت خود را وارد کنید"></textarea>
                            </div>
                        </label>
                        <label class="form-label col-12 mb-4" id="ddate" style="text-align: right;display: none"> انتخاب روز تحویل
                            <div class="form-items days-form owl-carousel">
                                <?php
                                $day = 86400;
                                $today = date('Y-m-d');
                                $unixtoday = strtotime($today) + $day;
                                for ($i = 1; $i < 9; $i++) {
                                    $unixtoday = $unixtoday + $day;
                                    if (jdate('l', $unixtoday) !== 'جمعه') {
                                ?><label> <?php echo jdate('l', $unixtoday) . '<br>' . jdate('Y/m/j', $unixtoday); ?>
                                            <input name="days" type="radio" value="<?php echo jdate('Y/m/j', $unixtoday); ?>">
                                        </label>
                                <?php }
                                } ?>
                            </div>
                        </label>
                        <label class="form-label col-12 mb-4" style="text-align: right;cursor:pointer"><input style="width: 20px !important;height: 20px;display:inline-block;vertical-align: middle;margin-left:10px;" required type="checkbox" name="rules">موافقت با <a onclick="$('.rules').addClass('open'); $('.rules').click(function(e) {if (!e.target.className == 'container' || !$(e.target).parents('.rules').length) {$(this).removeClass('open');}});"><?php echo $functions->get_language($_SESSION['lang'], 'cart_rules') ?></a>
                        </label>
                        <button class="button" type="submit" name="pay" id="pay"><?php echo $functions->get_language($_SESSION['lang'], 'cart_submit'); ?></button>
                    </div>
                </form>
            <?php elseif (!isset($_SESSION['user_info']["uid"])) : ?>
                <div class="error" style="margin-bottom: 50px; text-align: center;">
                    <h1 class="my-4" style="text-align: center;font-size:22px;"><?php echo $functions->get_language($_SESSION['lang'], 'cart_login'); ?></h1>
                    <span class="button cart-login"> <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-in'); ?> / <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-up'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<script src="https://github.com/KayvanMazaheri/ir-city-select/releases/download/v0.2.0/ir-city-select.min.js"></script>
<script>
    var price = $('.Price-amount').html();
    var int_price = price.replace("\t", "").replace(/,/g, "");
    const before_shipping_price = parseInt(int_price);

    var c_price = document.getElementById("coupon-price");
    if (c_price) {
        var int_c = c_price.firstChild.data.replace("\t", "").replace(/,/g, "");
        var coupon_price = parseInt(int_c);
    }

    if (c_price) {
        var final_price = before_shipping_price + delivery - coupon_price;
    } else {
        var final_price = before_shipping_price + delivery
    }
    const final = document.getElementById("final_price");
    final.innerHTML = new Intl.NumberFormat().format(final_price) + " <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?> ";

    function change_final_price(element) {
        var post_price = parseInt(element.getAttribute("title"));
        let deliverydate = document.getElementById("ddate");
        let days = document.getElementsByName("days");
        if (c_price) {
            var final_price = before_shipping_price + post_price - coupon_price;
        } else {
            var final_price = before_shipping_price + post_price
        }
        const final = document.getElementById("final_price");
        final.innerHTML = new Intl.NumberFormat().format(final_price) + " <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?> ";

        if (element.id == "post") {
            let cod = document.getElementById("darmahal");
            cod.style.display = "none";

            let gateway = document.getElementById("gateway");
            gateway.checked = true;

            deliverydate.style.display = "none";
            for (var i = 0; i < days.length; i++) {
                days[i].required = false;
            }

        } else {
            let cod = document.getElementById("darmahal");
            cod.style.display = "block";
            let gateway = document.getElementById("gateway");
            gateway.checked = true;
            deliverydate.style.display = "block";
                for (var i = 0; i < days.length; i++) {
                    days[i].required = true;
                }
        }

    }

    function check_city(select) {
        let peyk = document.getElementById("delivery");
        let post = document.getElementById("post");
        let deliverydate = document.getElementById("ddate");
        let days = document.getElementsByName("days");

        let gateway = document.getElementById("gateway");
        let darmahal = document.getElementById("darmahal");
        if (select.value == "تهران") {
            peyk.hidden = false;

            let post = document.getElementById("post");
            if(post.checked == true){
                deliverydate.style.display = "none";
                for (var i = 0; i < days.length; i++) {
                days[i].required = false;
                }
            }else{
                deliverydate.style.display = "block";
                for (var i = 0; i < days.length; i++) {
                    days[i].required = true;
                }
            }
        } else {
            peyk.hidden = true;
            let cod = document.getElementById("darmahal");
            cod.style.display = "none";
            post.checked = true;
            gateway.checked = true;

            deliverydate.style.display = "none";
            for (var i = 0; i < days.length; i++) {
                days[i].required = false;
            }

        }

    }
</script>
<script src="/themes/aso/includes/assets/js/select2.full.min.js"></script>
<script src="/themes/aso/includes/assets/js/select2-custom.js"></script>
<?php include_once('footer.php'); ?>
