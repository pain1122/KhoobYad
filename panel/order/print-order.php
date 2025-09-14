<?php
include_once("../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/user.php");
include_once(base_dir . "/includes/classes/shop-order.php");
$order_id = $_GET['order_id'];
if (empty($order_id))
    base::redirect(site_url . 'panel/index.php?page=order/all-order.php');
$company_name = base::get_option('company_name');
$company_providence = base::get_option('providence');
$company_address = base::get_option('address');
$company_phone1 = base::get_option('phone_number1');
$company_phone2 = base::get_option('phone_number2');
$company_phone3 = base::get_option('phone_number3');

$posts = array();
$obj = new order($order_id);
$order_sum = $obj->get_sum();
$date = $obj->get_post_date();
$user_name = $obj->get_user_name();
$user_phone = $obj->get_user_phone();
$user_providence = $obj->get_user_providence();
$user_address = $obj->get_user_address();
$user_shipping_price = intval($obj->get_user_shipping_price());
$user_notes = $obj->get_user_notes();
$payment = $obj->get_payment();
$shipping = $obj->get_shipping();
$days = $obj->get_days();
$items = $obj->get_items();
?>
<!DOCTYPE html>
<html lang="fa" class="light-style" dir="rtl" data-theme="theme-default" data-assets-path="/panel/assets/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title><?php echo $company_name; ?></title>
    <link rel="stylesheet" href="/panel/assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="/panel/assets/vendor/css/rtl/rtl.css">
    <link rel="stylesheet" href="/panel/assets/vendor/css/pages/app-invoice-print.css">
</head>

<body>
    <div class="invoice-print p-5">
        <div class="d-flex justify-content-between flex-row">
            <div class="mb-4">
                <div class="d-flex align-items-center svg-illustration mb-3 gap-2">
                    <span class="app-brand-logo demo">
                        <img width="40px" src="../assets/img/logo_color.png">
                    </span>
                    <span class="app-brand-text h3 mb-0 fw-bold">خوب یاد</span>
                </div>
                <p class="mb-1"><?php echo $company_providence; ?></p>
                <p class="mb-1"><?php echo $company_address; ?></p>
                <p class="mb-0"><span class="d-inline-block" dir="ltr"><?php echo $company_phone1; ?></span> ، <span
                        class="d-inline-block" dir="ltr"><?php echo $company_phone2; ?></span></p>
            </div>
            <div>
                <h4>صورتحساب #<?php echo $order_id; ?></h4>
                <div class="mb-2">
                    <span>تاریخ ثبت :</span>
                    <span class="fw-semibold"><?php echo jdate('Y/m/j', $date); ?></span>
                </div>
            </div>
        </div>

        <hr>

        <div class="row d-flex justify-content-between mb-4">
            <div class="col-12 col-sm-6">
                <h6>جزئیات گیرنده</h6>
                <table class="lh-2">
                    <tbody>
                        <tr>
                            <td class="pe-3" style="min-width:100px;">نام :</td>
                            <td><?php echo $user_name; ?></td>
                        </tr>
                        <tr>
                            <td class="pe-3" style="min-width:100px;">شماره تلفن :</td>
                            <td><?php echo $user_phone; ?></td>
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
                            <td><?php echo number_format($order_sum); ?> تومان</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border-top m-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام آیتم</th>
                        <th>تعداد</th>
                        <th>مبلغ</th>
                        <th>مبلغ کل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $items_sum = 0;
                    $count = 1;
                    foreach ($items as $item) {
                        $item_id = $item['item_id'];
                        $items_order_id = $item['items_order_id'];
                        $item_title = $obj->get_item_title($item_id);
                        $item_qty = $item['qty'];
                        $price = $obj->get_item_meta($items_order_id, 'price');
                        $sum = $obj->get_item_meta($items_order_id, 'total');
                        $items_sum += $sum;
                        ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo $item_title; ?></td>
                            <td><?php echo $item_qty; ?></td>
                            <td><?php echo number_format($price); ?> تومان</td>
                            <td><?php echo number_format($sum); ?> تومان</td>
                        </tr>
                        <?php $count++;
                    } ?>
                    <tr>
                        <td colspan="3" class="align-top px-4 py-3">
                            <p class="mb-2">
                                <span class="me-1 fw-bold">فروشنده:</span>
                                <span><?php echo $company_name; ?></span>
                            </p>
                            <span>با تشکر از اعتماد شما</span>
                        </td>
                        <td class="text-end px-4 py-3">
                            <p class="mb-2">جمع جزء :</p>
                            <p class="mb-2">تخفیف :</p>
                            <p class="mb-0">جمع کل :</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="fw-semibold mb-2"><?php echo number_format($items_sum); ?> تومان</p>
                            <p class="fw-semibold mb-2"><?php echo number_format($coupon); ?> تومان</p>
                            <p class="fw-semibold mb-0"><?php echo number_format($order_sum); ?> تومان</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-12 lh-1-85 mt-3">
                <span class="fw-semibold">یادداشت :</span>
                <span><?php echo $user_notes; ?></span>
            </div>
        </div>
    </div>
    <script src="/panel/assets/js/app-invoice-print.js"></script>
</body>

</html>