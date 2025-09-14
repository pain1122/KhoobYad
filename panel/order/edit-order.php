<?php
session_start();
include_once("../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/shop-order.php");
$company_name = base::get_option('company_name');
$company_providence = base::get_option('providence');
$company_address = base::get_option('address');
$company_phone1 = base::get_option('phone_number1');
$company_phone2 = base::get_option('phone_number2');
$company_phone3 = base::get_option('phone_number3');
$is_cart = 'false';
if (isset($_GET['cart']))
    $is_cart = 'true';
$order_id = $_GET['id'];
if ($order_id) {
    $obj = new order($order_id);
    $coupon_price = $obj->get_coupon_price();
}


if (isset($_POST['submit-factor'])) {
    $user_name = $_POST['user_name'];
    $user_phone = $_POST['user_phone'];
    $order_sum = $_POST['order_sum'];
    $user_notes = $_POST['user_notes'];
    if ($is_cart == 'false') {
        $obj->set_user_name($user_name);
        $obj->set_user_phone($user_phone);
        $obj->set_sum($order_sum);
        $obj->set_user_notes($user_notes);
    } else {
        $obj = new order('new');
        $order_id = $obj->get_id();
        $obj->set_author($uid);
        $obj->set_title($user_phone . '-' . $user_name);
        $obj->set_status('failed');
        $obj->set_excerpt($factor_seller);

        $post_meta_content = array(
            'user_name' => $user_name,
            'user_id' => $uid,
            'user_phone' => $user_phone,
            'user_notes' => $user_notes,
            'sum' => $order_sum,
            'coupon' => $price_coupon,
            'coupon_code' => $code
        );

        $obj->insert_meta($post_meta_content);

        $cart = $_SESSION['cart'];
        $pay = $order_sum;
        foreach ($cart as $item_id => $item_count) {
            $item = new post($item_id);
            $type = $item->get_type();
            $item = new product($item_id);
            $item->set_post_type($type);
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
                $obj->set_item(['id' => $item_id, 'qty' => $item_count, 'price' => $price_id, 'total' => $count_price_id, 'off' => $off, 'coupon' => $coupon_id]);
            }
        }
        $_SESSION['final_price'] = $pay;
        $_SESSION['order_id'] = $order_id;
        $callback_url = site_url . "panel/index.php?page=order/returned.php";
        $functions->ZarinpalPay($pay, $callback_url, $uid);
        $functions->redirect(site_url . 'panel/index.php?page=order/all-order.php');
    }
}
if ($is_cart == 'false') {
    $posts = array();
    $order_sum = $obj->get_sum();
    $date = $obj->get_post_date();
    $user_name = $obj->get_user_name();
    $user_phone = $obj->get_user_phone();
    $user_notes = $obj->get_user_notes();
    $items = $obj->get_items();
} else if ($is_cart == 'true' && is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $order_sum = 0;
    $cart = $_SESSION['cart'];
    foreach ($cart as $item_id => $item_count):
        $item = new post($item_id);
        $price = $item->get_meta('_price');
        $order_sum += $price;
    endforeach;
}

?>
<form action="" method="post" class="row invoice-edit">
    <div class="col-lg-9 col-12 mb-lg-0 mb-4">
        <div class="card invoice-preview-card">
            <div class="card-body">
                <div class="row p-sm-3 p-0">
                    <div class="col-md-6 mb-md-0 mb-4">
                        <div class="d-flex align-items-center svg-illustration mb-4 gap-2">
                            <span class="app-brand-logo demo">
                                <img width="100%" src="assets/img/logo_color.png">
                            </span>
                            <span class="app-brand-text h3 mb-0 fw-bold">خوب یاد</span>
                        </div>
                        <p class="mb-1"><?php echo $company_providence; ?></p>
                        <p class="mb-1"><?php echo $company_address; ?></p>
                        <p class="mb-0"><span class="d-inline-block" dir="ltr"><?php echo $company_phone1; ?></span> ،
                            <span class="d-inline-block" dir="ltr"><?php echo $company_phone2; ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-2">
                            <?php if (!empty($order_id)): ?>
                                <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                    <span class="h4 text-capitalize mb-0 text-nowrap">صورتحساب #</span>
                                </dt>
                                <dd class="col-sm-6 d-flex justify-content-md-end">
                                    <div class="w-px-150">
                                        <input type="text" class="form-control" disabled value="<?php echo $order_id; ?>">
                                    </div>
                                </dd>
                            <?php endif; ?>
                            <dt class="col-sm-6 mb-2 mb-sm-0 pt-1 text-md-end">
                                <span class="fw-normal">تاریخ:</span>
                            </dt>
                            <dd class="col-sm-6 d-flex justify-content-md-end">
                                <div class="w-px-150">
                                    <input type="text" class="form-control" disabled
                                        value="<?php echo jdate('Y/m/j', $date); ?>">
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>

                <hr class="my-4 mx-n4">

                <div class="row p-sm-3 p-0">
                    <div class="col-12 col-sm-6">
                        <h6>جزئیات گیرنده</h6>
                        <table class="lh-2">
                            <tbody>
                                <tr>
                                    <td class="pe-3" style="min-width:100px;">نام :</td>
                                    <td><input type="text" class="form-control" name="user_name"
                                            value="<?php echo $nickname; ?>" required></td>
                                </tr>
                                <tr>
                                    <td class="pe-3" style="min-width:100px;">شماره تلفن :</td>
                                    <td><input type="text" class="form-control" name="user_phone"
                                            value="<?php echo $user->get_user_meta('phonenumber'); ?>" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6>جزئیات صورتحساب</h6>
                        <table class="lh-2">
                            <tbody>
                                <tr>
                                    <td class="pe-3" style="min-width:100px;">مجموع فاکتور:</td>
                                    <td>
                                        <div class="d-flex"><?php if ($role == 'admin') { ?><input type="number"
                                                    name="order_sum" value="<?php echo $order_sum; ?>"
                                                    onkeyup="replace_digits(this)" class="form-control ml-2"><?php } else {
                                            echo $order_sum;
                                        } ?>
                                            تومان</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="mx-n4">

                <div class="table-responsive">
                    <table class="table border-top m-0">
                        <thead>
                            <tr>
                                <th class="px-2" width="20px">#</th>
                                <th class="px-2">نام آیتم</th>
                                <th class="px-2" width="130px">مبلغ</th>
                                <th class="px-2" width="40px">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (is_countable($items)) {
                                foreach ($items as $item) {
                                    $item_id = $item['item_id'];
                                    $items_order_id = $item['items_order_id'];
                                    $item_title = $obj->get_item_title($item_id);
                                    $price = $obj->get_item_meta($items_order_id, 'price');
                                    $sum = $obj->get_item_meta($items_order_id, 'total');
                                    $parent = $obj->get_parent();
                                    if ($parent > 0) {
                                        $parent_product = new post($parent);
                                        $parent_title = $parent_product->get_title();
                                        $item_title = "$parent_title - $item_title";
                                    }
                                    ?>
                                    <tr>
                                        <td class="px-2"><?php echo $item_id; ?></td>
                                        <td class="px-2"><?php echo $item_title; ?></td>
                                        <td class="px-2"><?php echo number_format($price); ?> تومان</td>
                                        <td class="px-2"><button type="submit" name="remove-item"
                                                value="<?php echo $item_id; ?>" class="btn btn-sm p-0 bx bx-x fs-5"></button>
                                        </td>
                                    </tr>
                                <?php }
                            } else if ($is_cart == 'true' && is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                $cart = $_SESSION['cart'];
                                foreach ($cart as $item_id => $item_count):
                                    if ($item_id == null)
                                        break;
                                    $cart = new post($item_id);
                                    $type = $cart->get_type();
                                    $cart = new product($item_id);
                                    $cart->set_post_type($type);
                                    $item_title = $cart->get_title();
                                    $stock_status = $cart->get_status();
                                    $parent = $cart->get_parent();
                                    if ($parent > 0) {
                                        $parent_product = new post($parent);
                                        $parent_title = $parent_product->get_title();
                                        $item_title = "$parent_title - $item_title";
                                    }
                                    if ($stock_status == 'outofstock') {
                                        unset($_SESSION['cart'][$item_id]);
                                        unset($cart[$item_id]);
                                        break;
                                    } else {
                                        $price = $cart->get_price();
                                    } ?>
                                        <tr>
                                            <td class="px-2"><?php echo $item_id; ?></td>
                                            <td class="px-2"><?php echo $item_title; ?></td>
                                            <td class="px-2"><?php echo number_format($price); ?> تومان</td>
                                            <td class="px-2"><button type="submit" name="remove-item"
                                                    value="<?php echo $item_id; ?>" class="btn btn-sm p-0 bx bx-x fs-5"></button>
                                            </td>
                                        </tr>
                                <?php endforeach;
                            } ?>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="note" class="form-label fw-semibold">یادداشت:</label>
                            <textarea name="user_notes" class="form-control" rows="2"
                                id="note"><?php echo $user_notes; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-12 invoice-actions">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-primary d-grid w-100 mb-3" type="submit" name="submit-factor">ثبت</button>
            </div>
        </div>
    </div>
    </div>
</form>