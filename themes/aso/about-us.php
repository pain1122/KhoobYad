<?php
include_once('header.php');

$post_info_query = "SELECT * FROM `post` WHERE `post_title` = 'about-us' AND `post_type` = 'page';";
$post_info = $functions->FetchAssoc($post_info_query);
$post_id = $post_info['post_id']; 
$obj = new post($post_id);
?>
<section class="post-page">
    <div class="container">
        <div class="header-post-1">
            <h1><?php echo $obj->get_meta( 'title'); ?></h1>
            <?php $image = $obj->get_meta( 'image');
            if ($image) : ?>
                <img src="<?php echo $functions->displayphoto($image); ?>" alt="<?php echo $guid; ?>">
            <?php endif; ?>
        </div>
        <div class="body-post">
            <?php
            echo $obj->get_meta( 'content');
            ?>
        </div>
    </div>
</section>
<?php include_once('footer.php'); ?>