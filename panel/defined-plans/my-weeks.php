<?php
if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    base::RunQuery("DELETE FROM `plans` WHERE `id` = $post_id OR `parent` = $post_id OR `parent` IN (SELECT `id` FROM `plans` WHERE `parent` = $post_id);");
}
$user_id = $_GET['uid'];
$weeks = plan::get_plans(['plan_type' => 'week', 'user_id' => $user_id, 'order' => 'ASC']);
$week_score_colors = ['#39da8a', '#00cfdd', '#fdac41', '#ff5b5c'];
?>
<div class="row">
    <?php if (is_countable($weeks) && count($weeks) > 0) {
        for ($i = 1; $i < count($weeks) + 1; $i++) {
            $week_id = $weeks[$i - 1]['id'];
            $week = new plan($week_id);
            $week_score = $week->get_status();
            if (empty($week_score))
                $week_score = 4;
            $week_title = "هفته $i";
            $week_message = $week->get_content();
            if (empty($week_message))
                $week_message = "توضیحی برای این هفته نوشته نشده است!"; ?>
            <div class="col-12 col-lg-6 mb-3 position-relative">
            <a href="?page=defined-plans/my-weeks.php&uid=<?php echo $user_id; ?>&delete=<?php echo $week_id; ?>" class="btn-close text-reset mt-1 mr-1 position-absolute" style="z-index:2"></a>
                <a href="?page=defined-plans/my-defined-plans.php&uid=<?php echo $user_id; ?>&week=<?php echo $week_id; ?>" class="card card-body" style="background-color: <?php echo $week_score_colors[intval($week_score)-1]?>;color:#fff">
                    <p class="d-flex align-items-center"><?php echo $week_title; ?></p>
                    <p><?php echo $week_message; ?></p>
                </a>
            </div>
    <?php }
    } ?>
</div>