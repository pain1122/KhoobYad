<?php
$post_id = $post->get_id();
$slug = urldecode($post->get_slug());
if (strpos($slug, '/') != null)
    $url = $post->get_url('date');
else
    $url = $post->get_url();
$title = $post->get_title();
$image = $post->get_thumbnail_src();
$content = $post->get_content();
?>
<div class="blog-card">
    <a href="<?php echo "{$url}" ?>"><img class="lazyload" src="<?php echo $image ?>"></a>
    <div class="blog-footer">
        <a href="<?php echo "{$url}" ?>">
            <h4><?php echo $title ?></h4>
        </a>
        <p><?php echo mb_substr(strip_tags($content), 0, 145); ?></p>
        <div class="blog-details">
            <?php
            $likes = "SELECT count(`meta_id`) as `count` FROM `post_meta` where `key` = 'like' AND `post_id` = " . $post_id;
            $likes = $functions->FetchAssoc($likes);
            $query_liked = "SELECT * FROM `post_meta` WHERE `post_id` = '$post_id' AND `key` = 'like' AND `value` = '$user_id';";
            $liked_res = $functions->FetchAssoc($query_liked);
            ?>
            <p><?php echo $functions->display_read_time($content); ?><span><?php echo $functions->get_language($_SESSION['lang'], 'reading_time'); ?></span></p>
            <i class="heart <?php if ($liked_res) echo "liked"; ?>" onclick="like(<?php echo $post_id ?>)"><span class="like-text" id="like-text"><?php echo $likes['count'] ?></span></i>
        </div>
    </div>
</div>