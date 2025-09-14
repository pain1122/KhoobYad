<?php
include_once('header.php');
?>


<script type="text/javascript" src="/themes/aso/includes/assets/js/slick.min.js"></script>
<main>
    <?php
    $slider_id = Base::FetchAssoc("SELECT `post_id` FROM `post` WHERE `post_type` = 'slider' AND `post_title` = 'firstpage'")['post_id'];
    $slider = new post($slider_id);
    $images = Base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_parent` = $slider_id AND `post_type` = 'slider_image'");
    ?>
    <section class="home-slider" id="lightning">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 <?php if (!$functions->ismobile()) echo 'col-lg-8 mb-2 mb-lg-0'; ?>">
                    <div class="main-slider">
                        <?php
                        if (is_countable($images) && count($images) > 0) :
                            for ($i = 0; $i < count($images); $i++) :
                                $image = new post($images[$i]['post_id']);
                        ?>
                                <div>
                                    <img src="<?php echo $image->get_title(); ?>">
                                    <h3> <?php echo $image->get_meta('title'); ?> </h3>
                                    <p> <?php echo $image->get_meta('text'); ?> </p>
                                </div>
                            <?php
                            endfor;
                            if ($functions->ismobile()) : ?>
                                <?php if (strlen($functions->get_option('slider_top_banner', $con)) > 0) : ?>
                                    <div>
                                        <a href="<?php echo $functions->get_option('slider_top_banner_link', $con) ?>"><img src="<?php echo $functions->get_option('slider_top_banner', $con) ?>"></a>
                                    </div>
                                <?php endif; ?>
                                <?php if (strlen($functions->get_option('slider_bottom_banner', $con)) > 0) : ?>
                                    <div>
                                        <a href="<?php echo $functions->get_option('slider_bottom_banner_link', $con) ?>"><img src="<?php echo $functions->get_option('slider_bottom_banner', $con) ?>"></a>
                                    </div>
                                <?php endif; ?>
                        <?php endif;
                        endif;
                        ?>
                    </div>
                </div>
                <?php if (!$functions->ismobile()) : ?>
                    <div class="col-12 col-lg-4 slider-banner d-flex">
                        <?php if (strlen($functions->get_option('slider_top_banner', $con)) > 0) : ?>
                            <div class="banner-holder">
                                <a href="<?php echo $functions->get_option('slider_top_banner_link', $con) ?>"><img src="<?php echo $functions->get_option('slider_top_banner', $con) ?>"></a>
                            </div>
                        <?php endif; ?>
                        <?php if (strlen($functions->get_option('slider_bottom_banner', $con)) > 0) : ?>
                            <div class="banner-holder">
                                <a href="<?php echo $functions->get_option('slider_bottom_banner_link', $con) ?>"><img src="<?php echo $functions->get_option('slider_bottom_banner', $con) ?>"></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    $offers = $functions->get_option('offers');
    $sale_start = $functions->get_option('sale_start');
    $sale_end = $functions->get_option('sale_end');
    if ($offers && $sale_start < intval(time()) && $sale_end > intval(time())) :
    ?>
        <section class="sec-product backed" id="lightning">
            <div class="container">
                <div class="row">
                    <div class="col-12 sec-title">
                        <h2 class="d-flex align-items-center">
                            <i class="lightning"></i>
                            <?php echo $functions->get_language($_SESSION['lang'], 'index_offers'); ?>
                            <div class="mr-4 mb-0 sec-header">
                                <a href="/big-sale"><?php echo $functions->get_language($_SESSION['lang'], 'index_show_all'); ?></a>
                            </div>
                        </h2>
                        <div class="countdown">
                            <div class="clock"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="special-offer owl-carousel">
                            <?php
                            $offers = explode(',', $offers);
                            foreach ($offers as $product) :
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
    <?php if (strlen($functions->get_option('offers_right_banner')) > 0) : ?>
        <section class="sec-banner1">
            <div class="container">
                <?php if ($functions->ismobile()) : ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="banner-slider owl-carousel">
                                <?php if (strlen($functions->get_option('offers_right_banner')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('offers_right_banner_link') ?>"><img src="<?php echo $functions->get_option('offers_right_banner') ?>"></a>
                                <?php endif; ?>
                                <?php if (strlen($functions->get_option('offers_left_banner')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('offers_left_banner_link') ?>"><img src="<?php echo $functions->get_option('offers_left_banner') ?>"></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row justify-content-center">
                        <?php if (strlen($functions->get_option('offers_right_banner')) > 0) : ?>
                            <div class="col-12 col-md-6 mb-4 mb-md-0 banner-holder">
                                <a href="<?php echo $functions->get_option('offers_right_banner_link') ?>"><img src="<?php echo $functions->get_option('offers_right_banner') ?>"></a>
                            </div>
                        <?php endif; ?>
                        <?php if (strlen($functions->get_option('offers_left_banner')) > 0) : ?>
                            <div class="col-12 col-md-6 mb-4 mb-md-0 banner-holder">
                                <a href="<?php echo $functions->get_option('offers_left_banner_link') ?>"><img src="<?php echo $functions->get_option('offers_left_banner') ?>"></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif;
    $main_page_access = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access'");
    $main_page_access_link = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access_link'");
    $main_page_access_image = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access_image'");
    if (is_countable($main_page_access) && count($main_page_access) > 0) : ?>
        <section class="sec-categories">
            <div class="container-fluid">
                <div class="quick-access">
                    <div class="sec-title">
                        <h2><?php echo $functions->get_language($_SESSION['lang'], 'index_favorite_categories'); ?></h2>
                    </div>
                    <?php for ($i = 0; $i < count($main_page_access); $i++) : ?>
                        <a href="<?php echo $main_page_access_link[$i]['value']; ?>" class="cat-item">
                            <?php if (strlen($main_page_access_image[$i]['value']) > 0) : ?><div class="img"><img src="<?php echo $main_page_access_image[$i]['value']; ?>" /></div><?php endif; ?>
                            <h4><?php echo $main_page_access[$i]['value'] ?></h4>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
    <?php
    endif;
    $cats1 = json_decode(str_replace("'", '"', $functions->get_option('category_section1')), true);
    if (is_countable($cats1) && count($cats1) > 0) :
        $cat1_id = $cats1[0]['value'];
        $view_max1 = '';
        $view1 = '';
        $order1 = 'ORDER BY `post`.`post_date` DESC';
        if ($functions->get_option('product_category_sort1') == 'top') {
            $view_max1 = ",`view`.`max` ";
            $view1 = "INNER JOIN (SELECT * , COUNT(`item_id`) AS `max` FROM `items_order` GROUP BY `item_id` ORDER BY `max` DESC ) `view` ON `post`.`post_id` = `view`.`item_id`";
            $order1 = "ORDER BY `max` DESC";
        }
        $product_cat_query1 = "SELECT `post`.`post_id`,`tag_relationships`.`tag_id` $view_max1 from `post` 
        INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
        INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
        $view1
        WHERE `post_type` = 'product' 
        AND `post_parent` = 0
        AND (`post_meta`.`key` = '_price' AND `post_meta`.`value` > 0)
        AND `post_status` = 'publish'
        AND `tag_relationships`.`tag_id` = $cat1_id
        GROUP BY `post`.`post_id`
        $order1
        LIMIT 0,9";
        $product_cat1 = $functions->Fetcharray($product_cat_query1);
        $tag_url1 = $functions->Fetchassoc("SELECT `slug` FROM `tag` WHERE tag_id = $cat1_id")['slug'];

    ?>
        <section class="sec-product">
            <div class="container">
                <div class="row">
                    <div class="col-12 sec-header">
                        <h2><?php echo $functions->get_language($_SESSION['lang'], 'index_favorite_category1'); ?></h2>
                        <a href="/product-category/<?php echo $tag_url1 ?>"><?php echo $functions->get_language($_SESSION['lang'], 'index_show_all_category1'); ?></a>
                    </div>
                    <div class="col-12">
                        <div class="product-slider owl-carousel">
                            <?php
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
    <?php endif; ?>
    <?php if (strlen($functions->get_option('categories_banner1')) > 0) : ?>
        <section class="sec-banner2">
            <div class="container">
                <?php if ($functions->ismobile()) :
                ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="banner-slider2 banner-slider owl-carousel">
                                <?php if (strlen($functions->get_option('categories_banner1')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('categories_banner1_link') ?>"><img src="<?php echo $functions->get_option('categories_banner1') ?>"></a>
                                <?php endif; ?>
                                <?php if (strlen($functions->get_option('categories_banner2')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('categories_banner2_link') ?>"><img src="<?php echo $functions->get_option('categories_banner2') ?>"></a>
                                <?php endif; ?>
                                <?php if (strlen($functions->get_option('categories_banner3')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('categories_banner3_link') ?>"><img src="<?php echo $functions->get_option('categories_banner3') ?>"></a>
                                <?php endif; ?>
                                <?php if (strlen($functions->get_option('categories_banner4')) > 0) : ?>
                                    <a href="<?php echo $functions->get_option('categories_banner4_link') ?>"><img src="<?php echo $functions->get_option('categories_banner4') ?>"></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else :
                ?>
                    <div class="row justify-content-center">
                        <?php if (strlen($functions->get_option('categories_banner1')) > 0) : ?>
                            <div class="col-6 col-lg-3 banner-holder">
                                <a href="<?php echo $functions->get_option('categories_banner1_link') ?>"><img src="<?php echo $functions->get_option('categories_banner1') ?>"></a>
                            </div>
                        <?php endif; ?>
                        <?php if (strlen($functions->get_option('categories_banner2')) > 0) : ?>
                            <div class="col-6 col-lg-3 banner-holder">
                                <a href="<?php echo $functions->get_option('categories_banner2_link') ?>"><img src="<?php echo $functions->get_option('categories_banner2') ?>"></a>
                            </div>
                        <?php endif; ?>
                        <?php if (strlen($functions->get_option('categories_banner3')) > 0) : ?>
                            <div class="col-6 col-lg-3 banner-holder">
                                <a href="<?php echo $functions->get_option('categories_banner3_link') ?>"><img src="<?php echo $functions->get_option('categories_banner3') ?>"></a>
                            </div>
                        <?php endif; ?>
                        <?php if (strlen($functions->get_option('categories_banner4')) > 0) : ?>
                            <div class="col-6 col-lg-3 banner-holder">
                                <a href="<?php echo $functions->get_option('categories_banner4_link') ?>"><img src="<?php echo $functions->get_option('categories_banner4') ?>"></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php
    $cats2 = json_decode(str_replace("'", '"', $functions->get_option('category_section2')), true);
    if (is_countable($cats2) && count($cats2) > 0) :
        $cat2_id = $cats2[0]['value'];
        $view_max2 = '';
        $view2 = '';
        $order2 = 'ORDER BY `post`.`post_date` DESC';
        if ($functions->get_option('product_category_sort2') == 'top') {
            $view_max2 = ",`view`.`max` ";
            $view2 = "INNER JOIN (SELECT * , COUNT(`item_id`) AS `max` FROM `items_order` GROUP BY `item_id` ORDER BY `max` DESC ) `view` ON `post`.`post_id` = `view`.`item_id`";
            $order2 = "ORDER BY `max` DESC";
        }
        $product_cat_query2 = "SELECT `post`.`post_id`,`tag_relationships`.`tag_id` $view_max2 from `post` 
        INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
        INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
        $view2
        WHERE `post_type` = 'product' 
        AND `post_parent` = 0
        AND (`post_meta`.`key` = '_price' AND `post_meta`.`value` > 0)
        AND `post_status` = 'publish'
        AND `tag_relationships`.`tag_id` = $cat2_id
        GROUP BY `post`.`post_id`
        $order2
        LIMIT 0,9";
        $product_cat2 = $functions->Fetcharray($product_cat_query2);
        $tag_url2 = $functions->Fetchassoc("SELECT `slug` FROM `tag` WHERE tag_id = $cat2_id")['slug'];

    ?>
        <section class="sec-product">
            <div class="container">
                <div class="row">
                    <div class="col-12 sec-header">
                        <h2><?php echo $functions->get_language($_SESSION['lang'], 'index_favorite_category2'); ?></h2>
                        <a href="/product-category/<?php echo $tag_url2 ?>"><?php echo $functions->get_language($_SESSION['lang'], 'index_show_all_category2'); ?></a>
                    </div>
                    <div class="col-12">
                        <div class="product-slider owl-carousel">
                            <?php
                            foreach ($product_cat2 as $product) :
                                $product = new product($product['post_id']);
                                include('product-part.php');
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php if (strlen($functions->get_option('new_banner1')) > 0) : ?>
        <section class="sec-banner1">
            <div class="container">
                <?php if ($functions->ismobile()) : ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="banner-slider owl-carousel">
                                <?php for ($i = 1; $i < 4; $i++) :
                                    if (strlen($functions->get_option('new_banner' . $i)) > 0) : ?>

                                        <a href="<?php echo $functions->get_option('new_banner_link' . $i) ?>"><img alt="<?php echo $functions->get_option('new_banner_alt' . $i); ?>" src="<?php echo $functions->displayphoto($functions->get_option('new_banner' . $i)) ?>"></a>
                                <?php endif;
                                endfor; ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row justify-content-center">
                        <?php for ($i = 1; $i < 4; $i++) :
                            if (strlen($functions->get_option('new_banner' . $i)) > 0) : ?>
                                <div class="<?php echo 'col-6'; ?> banner-holder">
                                    <a href="<?php echo $functions->get_option('new_banner_link' . $i) ?>"><img alt="<?php echo $functions->get_option('new_banner_alt' . $i); ?>" src="<?php echo $functions->displayphoto($functions->get_option('new_banner' . $i)) ?>"></a>
                                </div>
                        <?php endif;
                        endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <section class="sec-product">
        <div class="container">
            <?php
            $view_max1 = '';
            $view1 = '';
            $order1 = 'ORDER BY `post`.`post_date` DESC';
            $this_title = $functions->get_language($_SESSION['lang'], 'index_show_newest');
            $button = $functions->get_language($_SESSION['lang'], 'index_show_all_newest');
            if ($functions->get_option('product_new_sort') == 'top') {
                $this_title = $functions->get_language($_SESSION['lang'], 'index_show_sales');
                $button = $functions->get_language($_SESSION['lang'], 'index_show_all_sales');
                $view_max1 = ",`view`.`max` ";
                $view1 = "INNER JOIN (SELECT * , COUNT(`item_id`) AS `max` FROM `items_order` GROUP BY `item_id` ORDER BY `max` DESC ) `view` ON `post`.`post_id` = `view`.`item_id`";
                $order1 = "ORDER BY `max` DESC";
            }
            $new_products = $functions->Fetcharray("SELECT `post`.`post_id`$view_max1 from `post` 
         INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
         INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
         INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
         $view1
         WHERE `post_type` = 'product' 
         AND `post_status` = 'publish'
         AND `post_parent` = 0
         AND (`post_meta`.`key` = '_stock_status' && `post_meta`.`value` != 'outofstock')
         AND `tag_meta`.`type` = 'product_cat'
         GROUP BY `post`.`post_id`
         $order1
         LIMIT 0,9");
            ?>
            <div class="row">
                <div class="col-12 sec-header">
                    <h2><?php echo $this_title; ?></h2>
                    <a href="/shop"><?php echo $button; ?></a>
                </div>
                <div class="col-12">
                    <div class="product-slider owl-carousel">
                        <?php
                        foreach ($new_products as $product) :
                            $product = new product($product['post_id']);
                            include('product-part.php');
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="sec-blog">
        <div class="container">
            <div class="row">
                <div class="col-12 sec-header">
                    <i class="blog-icon"></i>
                    <h2><?php echo $functions->get_language($_SESSION['lang'], 'blog-title'); ?></h2>
                    <a href="/blog"><?php echo $functions->get_language($_SESSION['lang'], 'index_show_all_blog'); ?></a>
                </div>
                <div class="blog-slider owl-carousel">
                    <?php
                    $article_cat = $functions->get_post(8, "new", 0);
                    foreach ($article_cat as $post) :
                        $post = new blog($post['post_id']);
                        include('post-part.php');
                    endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/themes/aso/includes/assets/js/flipclock.min.js"></script>
<?php if (isset($_POST['newsletter'])) { ?>
    <script>
        Swal.fire(
            '<?php echo $functions->get_language($_SESSION['lang'], 'newsletter_alert_title'); ?>',
            '<?php echo $functions->get_language($_SESSION['lang'], 'newsletter_alert_message'); ?>',
            'success'
        );
    </script>
<?php } ?>
<script>
    var clock;

    $(document).ready(function() {
        var end = <?php echo intval($functions->get_option('sale_end')) * 1000 ?>;
        var time = end - Date.now();
        if (time <= 0)
            time = 0;
        clock = new FlipClock($('.clock'), time / 1000, {
            clockFace: 'HourCounter',
            autoStart: true,
            countdown: true,
            callbacks: {
                stop: function() {
                    $('.message').html('<?php echo $functions->get_language($_SESSION['lang'], 'index_offer_ended'); ?>')
                }
            }
        });
        $('.main-slider').slick({
            rtl: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            <?php if ($slider->get_meta('autoplay')) echo "autoplay : {$slider->get_meta('autoplay')}," ?>
            
            <?php if ($slider->get_meta('infinite')) echo "infinite : {$slider->get_meta('infinite')}," ?>
            <?php if ($slider->get_meta('vertical')) echo "vertical : {$slider->get_meta('vertical')}" ?>
        });
    });
    $(window).on('resize orientationchange', function() {
        $('.main-slider').slick('unslick');
        $('.main-slider').slick({
            rtl: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            <?php if ($slider->get_meta('autoplay')) echo "autoplay : {$slider->get_meta('autoplay')}," ?>
            <?php if ($slider->get_meta('lazyload')) //echo "lazyload : '{$slider->get_meta('lazyload')}'," ?>
            <?php if ($slider->get_meta('infinite')) echo "infinite : {$slider->get_meta('infinite')}," ?>
            <?php if ($slider->get_meta('vertical')) echo "vertical : {$slider->get_meta('vertical')}" ?>
        });
    });
    setTimeout(function(){ $(".main-slider").slick("setPosition") },1000);
</script>

<?php include_once('footer.php'); ?>