<?php 
if (isset($_POST['remove-item'])) {
    $item_id = $_POST['remove-item'];
    if ($is_cart == 'false') {
        $order = base::FetchAssoc("SELECT `qty`,`items_order_id` FROM `items_order` WHERE `order_id` = $order_id AND `item_id` = $item_id");
        $qty = intval($order['qty']);
        $order_item_id = $record['items_order_id'];
        $item_qty = intval($product->get_meta('_stock'));
        if ($item_qty && $item_qty != null && $item_qty >= 0) {
            $item_qty += $qty;
            base::RunQuery("UPDATE `post_meta` SET `value` = '$item_qty' WHERE `key` = '_stock' AND `post_id` = $item_id;");
            $stock_status = $product->get_meta('_stock_status');
            if ($stock_status == 'outofstock')
                base::RunQuery("UPDATE `post_meta` SET `value` = 'instock' WHERE `key` = '_stock_status' AND `post_id` = $item_id;");
        }
        $old_price = intval($obj->get_meta('sum'));
        $total = intval($obj->get_item_meta($order_item_id, 'total'));
        $new_price = $old_price - $total;
        base::RunQuery("UPDATE `post_meta` SET `value` = '$new_price' WHERE `post_id` = $order_id AND `key` = 'sum'");

        $query = "DELETE FROM `items_order` WHERE `order_id` = $order_id AND `item_id` = $item_id;";
        base::RunQuery($query);
        $meta_query = "DELETE FROM `items_order_meta` WHERE `order_item_id` = $order_item_id;";
        base::RunQuery($meta_query);
    } else {
        unset($_SESSION['cart'][$item_id]);
        unset($cart[$item_id]);
    }
}
if(isset($_POST['delete-cart-item'])){
    $item_id = $_POST['delete-cart-item'];
    unset($_SESSION['cart'][$item_id]);
    unset($cart[$item_id]);
}
if (isset($_POST['add-cart-item'])) {
    $item_id = $_POST['add-cart-item'];
    if (!isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = 1;
    }
}
?>

<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <?php
            foreach ($menu_array as $menu => $detail) :
                foreach ($detail['sub'] as $sub_menu => $sub_detail) :
                    if (strpos($sub_detail['url'], $page) !== false) : ?>
                        <h6 class="breadcrumb-wrapper mb-0">
                            <span class="text-muted fw-light"><?php echo $menu ?> / </span><?php echo $sub_menu ?>
                        </h6>
            <?php break;
                    endif;
                endforeach;
            endforeach;
            ?>
            <ul class="navbar-nav flex-row align-items-center ms-auto">

                <!-- Mini Cart -->
                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-cart bx-sm"></i>
                        <?php if(is_countable($_SESSION['cart']) && count($_SESSION['cart'])>0){ ?> 
                            <span class="badge rounded-pill bg-info text-white badge-notifications"><?php echo count($_SESSION['cart']); ?></span>
                        <?php } ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto secondary-font">سبد خرید</h5>
                                <a href="/panel/index.php?page=order/edit-order.php&cart=true" class="btn btn-primary">ثبت سفارش</a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <form action="" method="POST" class="row row-bordered overflow-visible g-0">
                                <?php
                                $count = 0;
                                if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    $cart = $_SESSION['cart'];

                                    foreach ($cart as $item_id => $item_count) :
                                        if ($item_id == null)
                                            break;
                                        $cart = new post($item_id);
                                        $product_id = $cart->get_id();
                                        $title = $cart->get_title();
                                        $type = $cart->get_type();
                                        $stock_status = $cart->get_status();
                                        $price = $cart->get_meta('_price');
                                        $thumbnail = $cart->display_post_image();
                                        $parent = $cart->get_parent();
                                        if($parent > 0) {
                                            $parent_product = new post($parent);
                                            $parent_title = $parent_product->get_title();
                                            $thumbnail = $parent_product->display_post_image();
                                            $title = "$parent_title - $title";
                                            $product_id = $parent;
                                        }
                                        if ($stock_status == 'outofstock') {
                                            unset($_SESSION['cart'][$item_id]);
                                            unset($cart[$item_id]);
                                            break;
                                        }
                                ?>
                                        <div class="mini-cart-item">
                                            <div class="wmc-remove">
                                                <button type="submit" name="delete-cart-item" value="<?php echo $item_id ?>" class="remove remove_from_cart_button">X</span>
                                            </div>
                                            <div class="wmc-image">
                                                    <img width="100" height="100" src="<?php echo $thumbnail ?>" class="img-cart-list" alt="" loading="lazy">
                                            </div>
                                            <div class="wmc-details">
                                                    <p><?php echo $title ?></p>
                                                <div class="item-detail">
                                                    <p>
                                                        <span class="wmc-price"><span class="cart-Price-amount amount"><?php echo number_format($price); ?></span><span class="Price-currencySymbol"> تومان</span></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                } else { ?>
                                    <span class="error-text text-center p-3">سبد خرید شما خالی است</span>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                </li>
                <!--/ Mini Cart -->

                <!-- Style Switcher -->
                <!-- <li class="nav-item me-2 me-xl-0">
                    <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                        <i class="bx bx-sm"></i>
                    </a>
                </li> -->
                <!--/ Style Switcher -->

                <!-- Quick links  -->
                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-grid-alt bx-sm"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto secondary-font">میانبرها</h5>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <?php if ((strpos($user_granted_access, "orders") !== false || strpos($user_granted_access, "product_low") !== false)) : ?>
                                <div class="row row-bordered overflow-visible g-0">
                                    <?php if (strpos($user_granted_access, "product_low") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-package fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=product/running-low.php" class="stretched-link">انبارگردانی</a>
                                            <small class="text-muted mb-0">محصولات رو به اتمام</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (strpos($user_granted_access, "product_attribute") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-food-menu fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=tag/pa-tag.php&type=product_attribiute" class="stretched-link">مشخصات محصول</a>
                                            <small class="text-muted mb-0">ویژگی ها</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ((strpos($user_granted_access, "reports") !== false || strpos($user_granted_access, "coupones") !== false)) : ?>
                                <div class="row row-bordered overflow-visible g-0">
                                    <?php if (strpos($user_granted_access, "reports") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-pie-chart-alt-2 fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=order/reports.php" class="stretched-link">فروشگاه</a>
                                            <small class="text-muted mb-0">گزارشات فروش</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (strpos($user_granted_access, "coupones") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-purchase-tag fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=order/coupon.php" class="stretched-link">تخفیفات</a>
                                            <small class="text-muted mb-0">کوپن ها</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ((strpos($user_granted_access, "main_page") !== false || strpos($user_granted_access, "site_setting") !== false)) : ?>
                                <div class="row row-bordered overflow-visible g-0">
                                    <?php if (strpos($user_granted_access, "site_setting") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-desktop fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=general/site-settings.php" class="stretched-link">هویت</a>
                                            <small class="text-muted mb-0">اطلاعات سایت</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (strpos($user_granted_access, "main_page") !== false) : ?>
                                        <div class="dropdown-shortcuts-item col">
                                            <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                                <i class="bx bx-cog fs-4"></i>
                                            </span>
                                            <a href="/panel/index.php?page=general/home-page.php" class="stretched-link">صفحه اصلی</a>
                                            <small class="text-muted mb-0">تنظیمات صفحه اصلی</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <!-- Quick links -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="<?php if (!empty($avatar)) echo $avatar;
                                        else echo "assets/img/avatars/1.png"; ?>" alt class="rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/panel/index.php?page=user/profile.php&uid=<?php echo $uid; ?>">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online mt-1">
                                            <img src="<?php if (!empty($avatar)) echo $avatar;
                                                        else echo "assets/img/avatars/1.png"; ?>" alt class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block"><?php echo $nickname; ?></span>
                                        <small><?php echo $role; ?></small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/panel/index.php?page=user/profile.php&uid=<?php echo $uid; ?>">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle">پروفایل</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/panel/index.php?page=order/all-order.php">
                                <span class="d-flex align-items-center align-middle">
                                    <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                    <span class="flex-grow-1 align-middle">سفارشات</span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="logout.php" target="_blank">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">خروج</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>