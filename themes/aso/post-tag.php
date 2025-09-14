<?php

include_once('header.php');
$post_id = $post['post_id'];
$tag = $tag_type['tag_id'];
?>
<section class="list-post">
    <?php echo '<p>'.$post_id.'</p>'; ?>
    <div class="container" >
        <div class="contact-list">
            <div class="row">
                <?php
                    $allpost = $functions->get_post(8, "new", $tag );
                    foreach ($allpost as $post) :
                        $post = new blog($post['post_id']);
                        echo '<div class="col-12 col-sm-6 col-lg-4 mb-4">';
                            include('list-post-item.php');
                        echo '</div>';
                    endforeach; 
                ?>
            </div>
        </div>
    </div>
</section>
        <?php include_once('footer.php'); ?>


       