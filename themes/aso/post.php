<?php
include_once('header.php');
$post_id = $obj->get_id();
$post_title = $obj->get_title();
$date = $obj->get_post_date();
$content = $obj->get_content();
$slug = urldecode($obj->get_slug());
if (strpos($slug,'/') >= 0)
    $url = $obj->get_url('date');
else
    $url = $obj->get_url();
$likes = $functions->FetchAssoc("SELECT count(`meta_id`) as `count` FROM `post_meta` where `key` = 'like' AND `post_id` = " . $id);
$query_liked = "SELECT * FROM `post_meta` WHERE `post_id` = '$post_id' AND `key` = 'like' AND `value` = '$user_id';";
$liked_res = $functions->FetchAssoc($query_liked);
$image = $obj->get_thumbnail_src();
$author = $obj->get_author();
$tags = $obj->get_tags();
$categories = $obj->get_cats();
$recomandeds = $obj->get_meta( 'recomandeds');
if ($author > 0) {
    $author = new user($author);
    $name = $author->get_display_name();
    if (strlen($name) == 0)
        $name = $functions->get_language($_SESSION['lang'], 'author_post');
} else {
    $name = $functions->get_language($_SESSION['lang'], 'author_post');
}
?>

<section class="post-page">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-9 mb-4 mb-md-0">
                <div class="header-post-1">
                    <div class="post-header">
                        <h1><?php echo $post_title; ?></h1>
                    </div>
                    <img loading="lazy" class="thumbnail" src="<?php echo $image ?>">
                </div>
                <div class="post-info">
                    <div><span><?php echo $functions->get_language($_SESSION['lang'], 'blog_author_name'); ?> :</span><span><?php echo $name; ?></span></div>
                    <div><span><?php echo $functions->get_language($_SESSION['lang'], 'blog_release_date'); ?> :</span> <span><?php echo jdate('Y/m/j', $date); ?></span></div>
                    <div><span><?php echo $functions->get_language($_SESSION['lang'], 'blog_reading_time'); ?> :</span><span><?php echo $functions->display_read_time($content); ?></span></div>
                </div>
                <div class="body-post">
                    <?php echo $content; ?>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <aside class="blog-side">
                    <?php if (strlen($recomandeds) > 0) {
                        $recomandeds = explode(',', $recomandeds); ?>
                        <div>
                            <span><?php echo $functions->get_language($_SESSION['lang'], 'recomanded_products'); ?></span>
                            <div class="recomanded-product-slider owl-carousel">
                                <?php
                                foreach ($recomandeds as $product) :
                                    $product = new product($product);
                                    include('product-part.php');
                                endforeach;
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="sim-blogs">
                        <span><?php echo $functions->get_language($_SESSION['lang'], 'similar_blogs'); ?></span>
                        <div class="simblog-slider owl-carousel">
                            <?php
                            $article_cat = $functions->get_post(5, "new", 0);
                            foreach ($article_cat as $post) :
                                $post = new blog($post['post_id']);
                                include('post-part.php');
                            endforeach; ?>
                        </div>
                    </div>
                    <?php if (is_countable($tags) && count($tags) > 0) : ?>
                        <div class="tag-wrapper">
                            <span><?php echo $functions->get_language($_SESSION['lang'], 'tags'); ?></span>
                            <?php foreach ($tags as $tag) :
                                if (end($tags)['tag_id'] == $tag['tag_id']) : ?>
                                    <a href="<?php echo "/post-tag/" . $tag['slug']; ?>"><?php echo $tag['name'] ?></a>
                                <?php else : ?>
                                    <a href="<?php echo "/post-tag/" . $tag['slug']; ?>"><?php echo $tag['name'] ?></a> ,
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>

    </div>
</section>
<script>
    function share(link) {
        navigator.clipboard.writeText(link);
        addToast();
    }

    function addToast() {
        $.Toast("<?php echo $functions->get_language($_SESSION['lang'], 'share'); ?>", "<?php echo $functions->get_language($_SESSION['lang'], 'share_alert_url_text'); ?>", "success", {
            has_icon: true,
            has_close_btn: true,
            stack: true,
            fullscreen: false,
            timeout: 3000,
            sticky: false,
            has_progress: true,
            rtl: false,
        });
    }
</script>
<?php include_once('footer.php'); ?>