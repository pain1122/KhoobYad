<?php
if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    base::RunQuery("UPDATE `post` SET `post_type` = 'deleted_weekly_plan' WHERE `post_id` = " . $post_id);
}
$user_id = $_GET['uid'];
$weeks_q = "SELECT `post_id` FROM `post` WHERE `post_type` = 'weekly_plan' AND `author` = $user_id";
$weeks = base::FetchArray($weeks_q);
$week_score_colors = ['#39da8a', '#00cfdd', '#fdac41', '#ff5b5c'];
?>
<div class="row">
    <?php if (is_countable($weeks) && count($weeks) > 0) {
        for ($i = 1; $i < count($weeks) + 1; $i++) {
            $week_id = $weeks[$i - 1]['post_id'];
            $week_score = intval(base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = {$week_id} AND `key` = 'week_score';")['value']);
            if (empty($week_score))
                $week_score = 4;
            $week_title = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = {$week_id} AND `key` = 'week_title';")['value'];
            if (empty($week_title))
                $week_title = "هفته $i";
            $week_message = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = {$week_id} AND `key` = 'week_message';")['value'];
            if (empty($week_message))
                $week_message = "توضیحی برای این هفته نوشته نشده است!"; ?>
            <div class="col-12 col-lg-6 mb-3 position-relative">
            <a href="?page=plans/my-weeks.php&uid=<?php echo $user_id; ?>&delete=<?php echo $week_id; ?>" class="btn-close text-reset mt-1 mr-1 position-absolute" style="z-index:2"></a>
                <a href="?page=plans/my-plans.php&uid=<?php echo $user_id; ?>&week=<?php echo $week_id; ?>" class="card card-body" style="background-color: <?php echo $week_score_colors[intval($week_score)-1]?>;color:#fff">
                    <p class="d-flex align-items-center"><?php echo $week_title; ?></p>
                    <p><?php echo $week_message; ?></p>
                </a>
            </div>
    <?php }
    } ?>
</div>