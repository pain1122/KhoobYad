<?php

include_once('header.php');

$sort = "new"; // new, old, expensive, cheap, rand
$number = 12;
$tag = $tag_type['tag_id'];
$stock = null;
$page = 1;

if (isset($_POST['orderby'])) {
    $sort = $_POST['orderby'];
}

if (isset($_POST['stock'])) {
    $stock = 0;
}

if (isset($_POST['s'])) {
    $search = $_POST['s'];
    $search_history = $_POST['s'];
}

$query = "SELECT COUNT(DISTINCT `value`) AS 'Count' FROM `post_meta` WHERE `key` = '_price';"; 
$data_step = $functions->FetchAssoc($query)['Count'];

$query = "SELECT * FROM `post_meta` WHERE `key` = '_price' 
ORDER BY `post_meta`.`value` ASC;"; 
$min_price = $functions->FetchAssoc($query)['value'];

$query = "SELECT * FROM `post_meta` WHERE `key` = '_price'
ORDER BY `post_meta`.`value` DESC;"; 
$max_price = $functions->FetchAssoc($query)['value'];

?>
<section class="product-category">
    <div class="container">
            <div class="row">
                <div class="col order-lg-2">
                    <form class="product-ordering" method="post" action="">
                        <select name="orderby" class="orderby" aria-label="سفارش خرید" onchange="this.form.submit()">
                            <option value="new" <?php if (isset($_POST['orderby']) && $_POST['orderby'] == "new") echo 'selected="selected"';?>>مرتب‌سازی بر اساس آخرین</option>
                            <option value="old" <?php if (isset($_POST['orderby']) && $_POST['orderby'] == "old") echo 'selected="selected"';?>>مرتب‌سازی بر اساس قدیمی ترین</option>
                            <option value="expensive" <?php if (isset($_POST['orderby']) && $_POST['orderby'] == "expensive") echo 'selected="selected"';?>>مرتب‌سازی بر اساس گرانترین</option>
                            <option value="cheap" <?php if (isset($_POST['orderby']) && $_POST['orderby'] == "cheap") echo 'selected="selected"';?>>مرتب‌سازی بر اساس ارزانترین</option>
                            <option value="rand" <?php if (isset($_POST['orderby']) && $_POST['orderby'] == "rand") echo 'selected="selected"';?>>مرتب سازی رندوم</option>
                        </select>
                        <label class="btn-switch">
                            <input type="checkbox" name="stock" class="switch-input" id="shp-checkbox" value="true" onchange="this.form.submit()" <?php if (isset($_POST['stock'])) echo 'checked';?>>
                            <span class="btn-slider btn-round"></span>
                        </label>
                        <input type="hidden" name="paged" value="1">
                    </form>
                    <div class="row">
                        <?php
                        $allproducts = $functions->get_product($number, $sort, $tag, $page);
                        foreach ($allproducts as $product) :
                        ?>
                            <div class="col-sm-6 col-lg-4 pro-item-wrapper">
                                <div class="product-item-parent">
                                    <div class="product-item-top">
                                        <?php
                                        $product = new product($product['post_id']);
                                        include('product-part.php');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        <?php include_once('footer.php'); ?>