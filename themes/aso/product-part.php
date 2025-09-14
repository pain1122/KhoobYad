<?php
$product_id = $product->get_id();
$price_sale_product_part = $product->get_sale_price();
$price_product_part = $product->get_regular_price();
$title = $product->get_title();
$url = $product->get_url();
if($price_product_part == '')
        $price_product_part = 0;
if ($price_sale_product_part > 0)
    $percent = (($price_product_part - $price_sale_product_part) * 100) / $price_product_part;
$stock_status = $product->get_stock_status();
$variant = $functions->fetchassoc("SELECT `post_id` FROM `post` WHERE `post_type` = 'product' AND `post_parent` = $product_id");
$button = "<span class=\"button\" onclick=\"addToCart(" . $product_id . ")\">".$functions->get_language($_SESSION['lang'], 'add_to_cart')."</span>";
if ($stock_status == 'outofstock')
    $button = "<span class=\"button disabled\">".$functions->get_language($_SESSION['lang'], 'outofstock')."</span>";
if ($stock_status == 'call' || $price_product_part == 0)
    $button = "<a href=\"tel:".$functions->get_option('phone') ."\" class=\"button\">".$functions->get_language($_SESSION['lang'], 'product_call')."</a>";
if(is_countable($variant) && count($variant)){
    $button = "<a href='$url' class='button'>".$functions->get_language($_SESSION['lang'], 'view_product')."</a>";
}
?>
<div class="product-card">
    <div class="pc-header">
        <?php if ($price_sale_product_part > 0) { ?>
            <div class="sale"><span><?php echo (int)$percent; ?>%</span> تخفیف</div>
        <?php } ?>
        <?php
        $query = "SELECT `user_id` FROM `user_meta` WHERE `user_id` = '$user_id' AND `key` = 'wishlist' AND `value` = " . $product_id;
        $res = $functions->FetchAssoc($query);
        ?>
        <i class="heart <?php if (in_array($product_id, $_SESSION['wishlist']) || $res) echo "wished"; ?>" onclick="addToFavorites(<?php echo $product_id; ?>)" value=""></i>
    </div>
    <a href="<?php echo $url ?>"><img class="lazyload" src="<?php echo $product->get_thumbnail_src() ?>" alt="<?php echo $product->get_image_alt() ?>" /></a>
    <div>
        <a href="<?php echo $url ?>">
            <h4 title="<?php echo $title ?>"><?php echo mb_substr($title, 0, 50) . ' ...'; ?></h4>
        </a>
        <?php if ($price_product_part > 0){ ?>
        <div class="price-card">
            <?php if ($price_sale_product_part > 0) { ?>
                <del><?php echo number_format(intval($price_product_part)) ?> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></del>
                <ins><?php echo number_format(intval($price_sale_product_part)) ?> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></ins>
            <?php } else { ?>
                <span><?php echo number_format(intval($price_product_part)) ?> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>
            <?php } ?>
        </div>
        <?php } ?>
        <?php echo $button; ?>
    </div>
</div>