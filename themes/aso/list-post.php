<?php

include_once('header.php');

if (!isset($_POST['page'])) {
    $page = 1;
} else {
    $page = $_POST['page'];
}
$results_per_page = 12;
$page_first_result = ($page - 1) * $results_per_page;
$post_count_query = "SELECT COUNT(`post_id`) as `count` from `post`
WHERE `post_type` = 'post' AND `post_status` = 'publish' ;";
$number_of_result = $functions->Fetchassoc($post_count_query)["count"];
$number_of_page = ceil($number_of_result / $results_per_page);

$post_query = "SELECT `post_id` from `post`
    WHERE `post_type` = 'post' AND `post_status` = 'publish'
    ORDER BY `post_date` DESC
    LIMIT $page_first_result , $results_per_page";
$allpost = $functions->FetchArray($post_query);
?>
<section class="list-post">
    <div class="container">
        <div class="contact-list">
            <div class="row">
                <?php
                foreach ($allpost as $post) :
                    $post = new blog($post['post_id']);
                    echo '<div class="col-12 col-sm-6 col-lg-4 mb-4">';
                    include('post-part.php');
                    echo '</div>';
                endforeach;
                ?>
                <div class="cutome-pagination mx-auto" id="pagination">
                    <form method="POST">
                        <div class="page-listproduct">
                            <?php if ($page > 1) { ?>
                                <li><button name="page" type="submit" class="prev" value="<?php echo $page - 1; ?>"><i></i></button></li>
                                <li><button name="page" type="submit" value="<?php echo $page - 1; ?>"><?php echo $page - 1; ?></button></li>
                            <?php } ?>
                            <li><button name="page" type="submit" class="active" value="<?php echo $page; ?>"><?php echo $page; ?></button></li>
                            <?php
                            if ($page == $number_of_page - 2) { ?>
                                <li><button name="page" type="submit" value="<?php echo $page + 2; ?>"><?php echo $page + 2; ?></button></li>
                            <?php }
                            if ($page < $number_of_page) { ?>
                                <li><button name="page" type="submit" value="<?php echo $page + 1; ?>"><?php echo $page + 1; ?></button></li>
                                <li><button name="page" type="submit" class="next" value="<?php echo $page + 1; ?>"><i></i></button></li>
                            <?php } ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once('footer.php'); ?>