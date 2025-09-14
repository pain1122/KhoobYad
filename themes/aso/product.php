<?php
include_once('header.php');
$post_atts = $obj->get_meta('description');
$post_title = $obj->get_title();
$post_content = $obj->get_content();
$company_phonenumber = $functions->get_option('phone_number1');
$limit = intval($obj->get_restrict());
$stock = intval($obj->get_stock());
if ($stock == null)
    $stock = 10000000;
if ($limit == 0)
    $limit = $stock;
$stock_status = $obj->get_stock_status();
$cats = $obj->get_cats();
$tags = $obj->get_tags();
$attrbuites = $obj->get_taxonomy('product_attribute');
$price = $obj->get_meta('_regular_price');
$price_off = $obj->get_meta('_sale_price');
$final_price = $obj->get_meta('_price');
if ($price_off > 0)
    $price_off = $final_price;
else
    $price = $final_price;
if(!$price)
    $price = 0;
if ($stock_status == 'instock')
    $button = "<span class=\"button\" onclick=\"addToCart(" . $id . ",parseInt($('#quantity').val()))\">".$functions->get_language($_SESSION['lang'], 'add_to_cart')."</span>";
if ($stock_status == 'outofstock' || $stock < 1)
    $button = "<span class=\"button disabled\">".$functions->get_language($_SESSION['lang'], 'outofstock')."</span>";
if ($stock_status == 'call' || $price == 0)
    $button = "<a href=\"tel:" . $company_phonenumber . "\" class=\"button\">".$functions->get_language($_SESSION['lang'], 'product_call')."</a>";

if (isset($_POST['sub-comment'])) {
    $comment = new post('new');
    $comment_id = $comment->get_id();
    $comment_text = $_POST['comment-text'];
    $rate = $_POST['rating'][0];
    $quality_rate = $_POST['quality-rate'];
    $buy_rate = $_POST['buy-rate'];
    $name = $_POST['name'];
    $comment->set_title($name);
    $comment->set_content($comment_text);
    $comment->set_parent($id);
    $comment->set_post_type('comment');
    $comment->set_status('waiting');

    $metas = [
        'rate' => $rate,
        'quality-rate' => $quality_rate,
        'buy-rate' => $buy_rate
    ];
    $comment->insert_meta($metas);
}
$quality_rate_avg = "SELECT SUM(`value`) / COUNT(`meta_id`) as 'Avg' FROM `post_meta` INNER JOIN `post` ON `post`.`post_id` = `post_meta`.`post_id` WHERE `key` = 'quality-rate' AND `post_type` = 'comment' AND `post_status` = 'accepted' AND `post_parent` = $id;";
$quality_rate_avg_res = intval($functions->FetchAssoc($quality_rate_avg)['Avg']);
$buy_rate_avg = "SELECT SUM(`value`) / COUNT(`meta_id`) as 'Avg' FROM `post_meta` INNER JOIN `post` ON `post`.`post_id` = `post_meta`.`post_id` WHERE `key` = 'buy-rate' AND `post_type` = 'comment' AND `post_status` = 'accepted' AND `post_parent` = $id;";
$buy_rate_avg_res = intval($functions->FetchAssoc($buy_rate_avg)['Avg']);
$product_rating = ($buy_rate_avg_res + $quality_rate_avg_res) / 2;
$thumbnail = $obj->get_thumbnail_src();
$complementaries = $obj->get_meta('complementaries');
$replacements = $obj->get_meta('replacements');


$galleries = json_decode($obj->get_meta('galleries'), true);
$video_id = $obj->get_meta('_video_id');
$video_cover_id = $obj->get_meta('_video_cover_id');
if (strlen($video_id) > 0)
    $video = $functions->Fetchassoc("SELECT * FROM `post` WHERE `post_id` = $video_id");
if (strlen($video_cover_id) > 0)
    $video_cover = $functions->Fetchassoc("SELECT * FROM `post` WHERE `post_id` = $video_cover_id");

if ($_POST['notif_sale'] == 'true' && $user_id > 0) {
    if (is_countable($notif_stock) && ($notif_sale) > 0) {
        $functions->RunQuery("DELETE FROM `post_meta` 
        WHERE `post_id` = $id AND `key` = 'notif_sale' AND `value` = '$phonenumber'") or die($con->error);
    } else {
        $functions->RunQuery("INSERT INTO `post_meta`( `post_id`, `key`, `value`) 
        VALUES ($id,'notif_sale','$phonenumber')") or die($con->error);
    }
}
if ($_POST['notif_stock'] == 'true' && $user_id > 0) {
    if (is_countable($notif_stock) && ($notif_stock) > 0) {
        $functions->RunQuery("DELETE FROM `post_meta` 
        WHERE `post_id` = $id AND `key` = 'notif_stock' AND `value` = '$phonenumber'") or die($con->error);
    } else {
        $functions->RunQuery("INSERT INTO `post_meta`( `post_id`, `key`, `value`) 
        VALUES ($id,'notif_stock','$phonenumber')") or die($con->error);
    }
}
$notif_sale = $functions->Fetchassoc("SELECT * FROM `post_meta` WHERE `post_id` = $id AND `key` = 'notif_sale' AND `value` = '$phonenumber'");
$notif_stock = $functions->Fetchassoc("SELECT * FROM `post_meta` WHERE `post_id` = $id AND `key` = 'notif_stock' AND `value` = '$phonenumber'");

$var_query = "SELECT `tag_meta`.`tag_id`,`tag_meta`.`parent` FROM `post`
INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id`
INNER JOIN `tag` ON `tag`.`tag_id` = `post_meta`.`value`
INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
WHERE `post`.`post_parent` = $id
AND `post`.`post_type` = 'product'
GROUP BY `post_meta`.`value`
ORDER BY `key` DESC";
$variables = $functions->Fetcharray($var_query);
if (is_countable($variables) && count($variables) > 0) {
    $var_attr = [];
    foreach ($variables as $variable) {
        if ($var_attr[$variable['parent']])
            array_push($var_attr[$variable['parent']], $variable['tag_id']);
        else
            $var_attr[$variable['parent']] = [$variable['tag_id']];
    }
    $button = "";
}
?>
<main class="single-product">
    <?php if (($stock_status == 'outofstock' || $stock < 1) && strlen($replacements) > 0) : ?>
        <section class="sec-product">
            <div class="container">
                <div class="row">
                    <div class="col-12 sec-title">
                        <h2><?php echo $functions->get_language($_SESSION['lang'], 'replacements'); ?></h2>
                    </div>
                    <div class="col-12">
                        <div class="product-slider owl-carousel">
                            <?php
                            $replacements = str_replace(',,', '', $replacements);
                            $replacements = explode(',', $replacements);
                            foreach ($replacements as $product) :
                                $product = new product($product);
                                include('product-part.php');
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="product-header">
        <div class="container">
            <div class="product-wrapper">
                <span class="rating"><?php echo $product_rating; ?></span>
                <div class="row">
                    <div class="col-8 mx-auto col-md-6 col-lg-4 mb-4 mb-md-0">
                        <div id="sync1" class="product-galery owl-carousel">
                            <?php if (strlen($video) > 0) : ?>
                                <div class="gallery-item" data-src="<?php echo $thumbnail; ?>">
                                    <video controlsList="nodownload" <?php if (strlen($video) > 0) : ?> poster="<?php echo $video_cover; ?>" <?php endif; ?>>
                                        <source src="<?php echo $video; ?>" type="video/mp4">
                                        <?php echo $functions->get_language($_SESSION['lang'], 'browser_support_video'); ?>
                                    </video>
                                </div>
                            <?php endif;
                            if (strlen($thumbnail) > 0) : ?>
                                <div class="gallery-item" data-src="<?php echo $thumbnail; ?>">
                                    <img loading="lazy" src="<?php echo $thumbnail; ?>" alt="<?php echo $obj->get_meta('image_alt'); ?>">
                                </div>
                            <?php
                            endif;
                            foreach ($galleries as $src => $alt) :
                                if(!empty($src)){
                            ?>
                                <div class="gallery-item" data-src="<?php echo $src; ?>">
                                    <img loading="lazy" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>">
                                </div>
                            <?php }
                            endforeach; ?>
                        </div>
                        <div id="sync2" class="product-galery owl-carousel d-none d-md-block">
                            <?php if (strlen($video) > 0) : ?>
                                <div class="gallery-item video">
                                    <?php if (strlen($video_cover) > 0) : ?><img loading="lazy" src="<?php echo $video_cover; ?>" alt="<?php echo $obj->get_meta('image_alt'); ?>"><?php endif; ?>
                                </div>
                            <?php endif;
                            if (strlen($thumbnail) > 0) : ?>
                                <div class="gallery-item">
                                    <img loading="lazy" src="<?php echo $thumbnail; ?>" alt="<?php echo $thumbnail; ?>">
                                </div>
                            <?php
                            endif;
                            foreach ($galleries as $src => $alt) :
                                if(!empty($src)){
                            ?>
                                <div class="gallery-item" data-src="<?php echo $src; ?>">
                                    <img loading="lazy" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>">
                                </div>
                            <?php }
                            endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-8 d-flex flex-column">
                        <div class="pro-title order-1">
                            <div class="pro-name" id="product-name">
                                <h1><?php echo $post_title; ?></h1>
                            </div>
                        </div>
                        <?php if (strlen($post_atts) > 0) : ?>
                            <div class="pro-attr order-4 order-md-2">
                                <h3><?php echo $functions->get_language($_SESSION['lang'], 'product_descriptions_title'); ?></h3>
                                <div class="att-wrapper body-post">
                                    <?php
                                    echo $post_atts;
                                    ?>
                                </div>
                            </div>
                        <?php
                        endif;
                        if (is_countable($var_attr)) {
                            echo "<div class='pro-vars order-2 order-md-3'>";
                            foreach ($var_attr as $key => $value) {
                                echo '<div class="d-flex align-items-center mb-4">';
                                $parent_var = new tag($key);
                                $parent_var_name = $parent_var->get_name();
                                echo "<strong>$parent_var_name : </strong>";
                                if ($value == reset($var_attr)) {
                                    foreach ($value as $attr) {
                                        $var = new tag($attr);
                                        $var_name = $var->get_name();
                                        echo "<p onClick='add_var(this);' id='varid-$attr'>$var_name</p>";
                                    }
                                }
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        ?>
                        <div class="notif-form order-3">
                            <form name="form" action="" method="post" id="notif-form">
                                <label><input type="hidden" value="false" name="notif_sale"><input type="checkbox" id="notif_sale" <?php if ($notif_sale) echo 'checked'; ?>><?php echo $functions->get_language($_SESSION['lang'], 'sale_notif'); ?></label>
                                <?php if ($stock_status == 'outofstock' || $stock < 1) : ?>
                                    <label><input type="hidden" value="false" name="notif_stock"><input type="checkbox" id="notif_stock" <?php if ($notif_stock) echo 'checked'; ?>><?php echo $functions->get_language($_SESSION['lang'], 'stock_notif'); ?></label>
                                <?php endif; ?>
                            </form>
                            <?php if ($obj->get_meta('consult')) : ?>
                                <a class="button" style="width: 170px;margin-top:15px;" href="tel:<?php echo $company_phonenumber ?>"><?php echo $functions->get_language($_SESSION['lang'], 'buy_advise'); ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="pro-count order-2 order-md-4">
                            <span id="base_price" class="d-none"><?php if ($price_off) {
                                                                        echo $price_off;
                                                                    } else if ($price) {
                                                                        echo $price;
                                                                    } ?></span>
                            <form action="" method="post" name="cart">
                                <?php if (($stock > 0 || $stock == null) && $stock_status == 'instock') { ?>
                                    <span id="q_text" <?php if (is_countable($var_attr) || $price == 0) echo "style= display:none;" ?>><?php echo $functions->get_language($_SESSION['lang'], 'cart_item_count'); ?></span>
                                    <div class="counter" <?php if (is_countable($var_attr) || $price == 0) echo "style= display:none;" ?>>
                                        <p class="plus" id="plus" onclick="change_quantity(this)"></p>
                                        <input type="text" name="quantity" id="quantity" min="1" max="<?php echo $limit ?>" value="1" onkeydown="return false">
                                        <p class="minus" id="minus" onclick="change_quantity(this)"></p>
                                    </div>
                                    <div class="pro-price">
                                        <?php if(empty($variables) && $price > 0):
                                            if ($price_off) : ?>
                                                <del><?php echo number_format($price); ?> <span>تومان</span></del>
                                                <span><ins id="pro-price"><?php echo number_format($price_off); ?></ins> تومان</span>
                                            <?php else : ?>
                                                <span><ins id="pro-price"><?php echo number_format($price); ?></ins> تومان</span>
                                            <?php endif;
                                        endif; ?>
                                    </div>
                                <?php }
                                echo "<div class='price-button'>".$button."</div>"; ?>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="sec-attr">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="attr-wrapper">
                        <div class="attr-item">
                            <div>
                                <span><?php echo $functions->get_language($_SESSION['lang'], 'service_advantages1'); ?></span>
                            </div>
                        </div>
                        <div class="attr-item">
                            <div>
                                <span><?php echo $functions->get_language($_SESSION['lang'], 'service_advantages2'); ?></span>
                            </div>
                        </div>
                        <div class="attr-item">
                            <div>
                                <span><?php echo $functions->get_language($_SESSION['lang'], 'service_advantages3'); ?></span>
                            </div>
                        </div>
                        <div class="attr-item">
                            <div>
                                <span><?php echo $functions->get_language($_SESSION['lang'], 'service_advantages4'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="sec-properties mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="prperties-header">
                        <span id="pro-1" class="active"><?php echo $functions->get_language($_SESSION['lang'], 'product_content_title'); ?></span>
                        <span id="pro-2"><?php echo $functions->get_language($_SESSION['lang'], 'product_attributes_title'); ?></span>
                        <span id="pro-3"><?php echo $functions->get_language($_SESSION['lang'], 'product_comments_title'); ?></span>
                    </div>
                    <div class="properties-wrapper">
                        <div id="pro-1c" class="content-box body-post" style="display: block;">
                            <?php
                            echo $post_content;
                            ?>
                        </div>
                        <div id="pro-2c" class="content-box body-post">
                            <div class="moshakhasat-product">
                                <div class="body-moshakhasat">
                                    <?php
                                    foreach ($attrbuites as $attrbuite) :
                                        $selected_attribute_id = $attrbuite['tag_id'];
                                        $attribute_name =  $attrbuite['name'];
                                        $selected_attribute = new tag($selected_attribute_id);
                                        $parent_attr = $selected_attribute->get_parent();
                                        $parent_attr = new tag($parent_attr);
                                        $parent_name = $parent_attr->get_name();
                                    ?>
                                        <div class="moshakhasat-product-part">
                                            <div class="name-moshakhasat">
                                                <span class="span-1"><?php echo $parent_name; ?></span>
                                            </div>
                                            <div class="amount-moshakhasat">
                                                <?php echo $attribute_name; ?>
                                            </div>
                                        </div>
                                    <?php
                                    endforeach;
                                    ?>
                                </div>

                            </div>
                        </div>
                        <div id="pro-3c" class="content-box">
                            <section class="sec-comment my-0">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <h4><?php echo $functions->get_language($_SESSION['lang'], 'comments_form_title'); ?></h4>
                                            <div class="comment-box">
                                                <form role="form" action="" method="POST">
                                                    <textarea name="comment-text" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'comments_text_placeholder'); ?>"></textarea>
                                                    <div>
                                                        <select name="rating[]">
                                                            <option value="<?php echo $functions->get_language($_SESSION['lang'], 'comments_rate1'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'comments_rate1'); ?></option>
                                                            <option value="<?php echo $functions->get_language($_SESSION['lang'], 'comments_rate2'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'comments_rate2'); ?></option>
                                                            <option value="<?php echo $functions->get_language($_SESSION['lang'], 'comments_rate3'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'comments_rate3'); ?></option>
                                                            <option value="<?php echo $functions->get_language($_SESSION['lang'], 'comments_rate4'); ?>" selected><?php echo $functions->get_language($_SESSION['lang'], 'comments_rate4'); ?></option>
                                                        </select>
                                                        <label><?php echo $functions->get_language($_SESSION['lang'], 'quality_rate'); ?><input type="range" name="quality-rate" min="1" max="10" value="10" class="slider" id="quality_range">
                                                            <p id="quality"></p>
                                                        </label>
                                                        <label><?php echo $functions->get_language($_SESSION['lang'], 'buy_rate'); ?><input type="range" name="buy-rate" min="1" max="10" value="10" class="slider" id="worth_range">
                                                            <p id="worth"></p>
                                                        </label>
                                                        <input type="text" name="name" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'comment_name_placeholder'); ?>">
                                                        <button type="submit" name="sub-comment"><?php echo $functions->get_language($_SESSION['lang'], 'comment_button'); ?></button>
                                                    </div>
                                                </form>
                                            </div>

                                            <?php
                                            $comment_query = "SELECT * FROM `post` WHERE `post_type` = 'comment' AND `post_status` =  'accepted' AND `post_parent` = " . $id;
                                            $comment_res = $functions->FetchArray($comment_query);
                                            if ($comment_res) : ?>
                                                <div class="contents-dispplay">
                                                    <div class="pro-rating">
                                                        <h4><?php echo $functions->get_language($_SESSION['lang'], 'comments_title'); ?></h4>
                                                        <div class="scores">
                                                            <lable><?php echo $functions->get_language($_SESSION['lang'], 'buy_rate'); ?> <span><?php echo $quality_rate_avg_res ?></span><span>/10</span><progress max="10" value="<?php echo $quality_rate_avg_res ?>"></progress></lable>
                                                            <lable><?php echo $functions->get_language($_SESSION['lang'], 'quality_rate'); ?> <span><?php echo $buy_rate_avg_res ?></span><span>/10</span><progress max="10" value="<?php echo $buy_rate_avg_res ?>">"></progress>
                                                            </lable>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    foreach ($comment_res as $comment) :
                                                        $single_comment = "SELECT * FROM `post_meta` WHERE `post_id` = " . $comment['post_id'];
                                                        $single_comment_res = $functions->FetchArray($single_comment);
                                                    ?>
                                                        <div class="comments-wrapper">
                                                            <ul>
                                                                <li class="user">
                                                                    <div class="comment-item">
                                                                        <div class="user">
                                                                            <img src="/themes/aso/includes/assets/image/user.png">
                                                                            <span><?php echo $comment['post_title'] ?></span>
                                                                        </div>
                                                                        <div class="message">
                                                                            <h5><?php echo $single_comment_res[0]['value']; ?></h5>
                                                                            <p><?php echo $comment['post_content'] ?></p>
                                                                        </div>
                                                                        <div class="scores">
                                                                            <lable><?php echo $functions->get_language($_SESSION['lang'], 'buy_rate'); ?> <span><?php echo $single_comment_res[1]['value']; ?></span><span>/10</span><progress id="file" min="0" max="10" value="<?php echo $single_comment_res[1]['value']; ?>"></progress></lable>
                                                                            <lable><?php echo $functions->get_language($_SESSION['lang'], 'quality_rate'); ?> <span><?php echo $single_comment_res[2]['value']; ?></span><span>/10</span><progress id="file" min="0" max="10" value="<?php echo $single_comment_res[2]['value']; ?>"></progress></lable>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php
                    if (is_countable($tags) && count($tags) > 0) : ?>
                        <div class="tag-wrapper">
                            <span><?php echo $functions->get_language($_SESSION['lang'], 'tags'); ?></span>
                            <?php foreach ($tags as $tag) :
                                if (end($tags)['tag_id'] == $tag['tag_id']) : ?>
                                    <a href="<?php echo "/product-tag/" . $tag['slug']; ?>"><?php echo $tag['name'] ?></a>
                                <?php else : ?>
                                    <a href="<?php echo "/product-tag/" . $tag['slug']; ?>"><?php echo $tag['name'] ?></a> ,
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php if (is_countable($complementaries) && count($complementaries) > 0) { ?>
        <section class="sec-product">
            <div class="container">
                <div class="row">
                    <div class="col-12 sec-title">
                        <h2><?php echo $functions->get_language($_SESSION['lang'], 'complementaries'); ?></h2>
                    </div>
                    <div class="col-12">
                        <div class="product-slider owl-carousel">
                            <?php
                            $complementaries = str_replace(',,', '', $complementaries);
                            $complementaries = explode(',', $complementaries);
                            foreach ($complementaries as $product) :
                                $product = new product($product);
                                include('product-part.php');
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
    <section class="sec-product">
        <div class="container">
            <div class="row">
                <div class="col-12 sec-title">
                    <h2><?php echo $functions->get_language($_SESSION['lang'], 'related_products'); ?></h2>
                </div>
                <div class="col-12">
                    <div class="product-slider owl-carousel">
                        <?php
                        $array = array_values($cats);
                        $product_cat1 = $functions->get_product(9, "new", $array[0], 0);
                        foreach ($product_cat1 as $product) :
                            $product = new product($product['post_id']);
                            include('product-part.php');
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<link rel="stylesheet" type="text/css" href="/themes/aso/includes/assets/css/select2.css">
<link rel="stylesheet" href="/themes/aso/includes/assets/css/lightgallery.css" />
<script src="/themes/aso/includes/assets/js/select2.full.min.js"></script>
<script src="/themes/aso/includes/assets/js/select2-custom.js"></script>
<script src="/themes/aso/includes/assets/js/lightgallery.min.js"></script>
<script src="/themes/aso/includes/assets/js/lg-fullscreen.min.js"></script>
<script src="/themes/aso/includes/assets/js/lg-thumbnail.min.js"></script>
<script src="/themes/aso/includes/assets/js/lg-zoom.min.js"></script>
<?php include_once('footer.php'); ?>
<script>
    var limit = <?php echo $limit ?>;

    function change_quantity(elem) {

        var id = elem.id;
        var quantity = document.getElementById('quantity');
        var quanty = parseInt(quantity.value);
        var price = document.getElementById("base_price").innerHTML;
        var total = document.getElementById("pro-price").innerHTML;
        var total = total.replace(/,/g, "");
        var total = parseInt(total, 10);
        var price = price.replace(/,/g, "");
        var price = parseInt(price, 10);

        if (id === "plus" && parseInt(quantity.value) < limit) {
            quanty += 1;
            quantity.value = parseInt(quantity.value) + 1;
            total = price * quanty;
            total = total.toLocaleString(undefined);
            document.getElementById("pro-price").innerHTML = total;

        } else if (id === "minus" && parseInt(quantity.value) > 1) {
            quanty -= 1;
            quantity.value = parseInt(quantity.value) - 1;
            total = price * quanty;
            total = total.toLocaleString(undefined);
            document.getElementById("pro-price").innerHTML = total;
        }
    }

    function uprice(select) {

        var price_normal = document.getElementById("price-normal");
        var price_off = document.getElementById("price-off");

        var price_final = document.getElementById("price-final");

        if (select.value > 2) {
            price_normal.style.color = "#aaa";
            price_normal.style.border = "2px solid #aaa";
            price_normal.style.opacity = "0.5";

            price_off.style.color = "#478b26";
            price_off.style.border = "2px solid #478b26";
            price_off.style.opacity = "1";

            var price_finaly = price_off.innerText.replace(/,/g, "");
            var p = select.value * price_finaly;


            price_final.innerText = separate(p);


        } else if (select.value > 1) {
            price_off.style.color = "#aaa";
            price_off.style.border = "2px solid #aaa";
            price_off.style.opacity = "0.5";

            price_normal.style.color = "#4361ee";
            price_normal.style.border = "2px solid #4361ee";
            price_normal.style.opacity = "1";

            var price_finaly = price_normal.innerText.replace(/,/g, "");
            var p = select.value * price_finaly;


            price_final.innerText = separate(p);


        } else {
            price_off.style.color = "#aaa";
            price_off.style.border = "2px solid #aaa";
            price_off.style.opacity = "0.5";

            price_normal.style.color = "#4361ee";
            price_normal.style.border = "2px solid #4361ee";
            price_normal.style.opacity = "1";

            var price_finaly = price_normal.innerText.replace(/,/g, "");
            var p = select.value * price_finaly;

            price_final.innerText = separate(p);
        }

        function separate(Number) {
            Number += '';
            Number = Number.replace(',', '');
            x = Number.split('.');
            y = x[0];
            z = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(y))
                y = y.replace(rgx, '$1' + ',' + '$2');
            return y + z;
        }
        price_final.innerText = separate(p);

    }

    function add_var(element) {
        if (!$(element).hasClass('active')) {
            $(element).parent().nextAll().find('p').remove();
            $(element).parent().find('p').removeClass('active');
            $(element).addClass('active');
            var next_var = $(element).parent().next();
            var vars = 'first_var';
            $(".pro-vars p.active").each(function() {
                vars += ',' + $(this).attr('id').replace('varid-', '');
            });
            getJSON("/themes/aso/includes/API/v1/get-var.php?post_id=<?php echo $id; ?>&var=" + vars, function(err, data) {
                if (data['post_id']) {
                    var post_id = data['post_id'];
                    var price = data['price'];
                    var price_off = data['sale-price'];
                    var title = data['title'];
                    var stock = data['stock'];
                    var stock_status = data['_stock_status'];
                    $('#product-name h1').text(title);
                    $('#base_price').text(price);
                    var price_html = "<span><ins id='pro-price'>" + separate(price) + "</ins> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>";
                    if (price_off > 0) {
                        $('#base_price').text(price_off);
                        price_html = "<del>" + separate(price) + "<span> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></del><span><ins id='pro-price'>" + separate(price_off) + "</ins> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>";
                    }
                    $('.pro-price').html(price_html);
                    $('.pro-price').removeClass('d-none');
                    $('.price-button').html("<span class=\"button\" onclick=\"addToCart(" + post_id + ",parseInt($('#quantity').val()))\"><?php echo $functions->get_language($_SESSION['lang'], 'add_to_cart') ?></span>");
                    $('.counter input').val(1);
                    $('.counter').show();
                    $('#q_text').show();
                    if (stock == 0 || stock_status == 'outofstock' || stock_status == 'call') {
                        $('#base_price').text('0');
                        price_html = '';
                        $('.pro-price').addClass('d-none');
                        $('.counter').hide();
                        $('#q_text').hide();
                        if (stock_status == 'outofstock') {
                            $('.price-button').html("<span class=\"button disabled\"><?php echo $functions->get_language($_SESSION['lang'], 'outofstock') ?></span>");
                        } else {
                            $('.price-button').html("<a href=\"tel:<?php echo $company_phonenumber; ?>\" class=\"button\"><?php echo $functions->get_language($_SESSION['lang'], 'product_call') ?></a>");
                        }
                    }
                } else {
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var name = data[i]['name'];
                            var id = data[i]['id'];
                            var code = data[i]['color_code'];
                            if(code)
                                $(next_var).append("<p style='backgorund-color:" + code + "' class='color-variable' onClick='add_var(this);' id='varid-" + id + "' alt='" + name + "'></p>");
                            else
                                $(next_var).append("<p onClick='add_var(this);' id='varid-" + id + "'>" + name + "</p>");
                        }
                    }
                }
            });
        }
    }

    var quality_range = document.getElementById("quality_range");
    var worth_range = document.getElementById("worth_range");
    var quality_score = document.getElementById("quality");
    var worth_score = document.getElementById("worth");
    quality_score.innerHTML = quality_range.value;
    worth_score.innerHTML = worth_range.value;
    quality_range.oninput = function() {
        quality_score.innerHTML = " " + this.value + " ";
    }
    worth_range.oninput = function() {
        worth_score.innerHTML = " " + this.value + " ";
    }


    $("#notif-form input").on('change', function() {
        <?php if ($user_id) : ?>
            let name = $(this).attr('id');
            if ($(this).is(':checked')) {
                $("#notif-form input[name='" + name + "']").attr('value', 'true');
            } else {
                $("#notif-form input[name='" + name + "']").attr('value', 'true');
            }
            this.form.submit()
        <?php else : ?>
            $('.muodal.account').addClass('open');
        <?php endif; ?>
    });

    $('#state_selector').on('change', function() {
        if ($(this).val() == 'تهران') {
            $('#d-hour').text('24 ');
        } else {
            $('#d-hour').text('48 ');
        }
    });
    if ($('#sync1 .gallery-item').length > 0) {
        lightGallery(document.getElementById('sync1'), {
            thumbnail: true,
            selector: ".gallery-item",
            zoom: true,
        });
    }
</script>