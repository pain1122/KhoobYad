<?php
$error = false;
if (isset($_GET['exam']) && $uid > 0) {
    $user = new user($uid);
    $username = $user->get_nick_name();
    $role = $user->get_user_meta('role');
    $fos = $user->get_user_meta('fos');
    $exams = json_decode($user->get_user_meta('descriptive-exams'), true);
    if (!$exams)
        $exams = [];
    elseif (in_array($_GET['exam'], $exams)) {
        echo "<script>
        alert('شما قبلا در این آزمون شرکت کرده اید و اجازه ورود مجدد ندارید!');
        window.location.replace('" . site_url . "panel/index.php?page=exams/descriptive-rankings.php&exam=" . $_GET['exam'] . "');
        </script>";
    }
    $post_id = intval($_GET['exam']);
    $obj = new post($post_id);
    $title = $obj->get_title();
    $term_start = $obj->get_meta('term_start');
    $term_end = $obj->get_meta('term_end');
    $on_time = $obj->get_meta('on_time');
    $duration = $obj->get_meta('duration');
    $descriptive_exam_questions = $obj->get_meta('descriptive_exam_questions');
    $members = json_decode($obj->get_meta('members'), true);
    if (empty($members))
        $members = [];
    $is_in_exam = false;
    foreach ($members as $member) {
        if ($member['value'] == $uid)
            $is_in_exam = true;
    }
    $classes = json_decode($obj->get_meta('classes'), true);
    if (is_countable($classes) && count($classes) > 0) {
        foreach ($classes as $key => $class) {
            $selected_members_q = "SELECT `user_id` FROM `user_meta` WHERE `key` = 'class_groups' AND `value` LIKE '%{$class['value']}%'";
            $selected_members = base::FetchArray($selected_members_q);
            if (is_countable($selected_members) && count($selected_members) > 0){
                foreach ($selected_members as $member){
                    $member_id = $member['user_id'];
                    if($member_id == $uid)
                        $is_in_exam = true;
                    $members[] = $member_id;
                }
            }  
        }
    }

    if ($role == 'student' && !$is_in_exam && !in_array($post_id, $exams)) {
        base::redirect(site_url . "panel/index.php?page=dashboard.php");
    }
    $date = time();

    $remaining_time = $term_end - $date;
    if ($duration > 0 && $remaining_time > $duration * 60)
        $remaining_time = $duration * 60;
    if ($remaining_time < 0)
        $remaining_time = 0;
    // if($role != 'admin' || $role != 'school'){
    //     base::redirect(site_url . "panel/index.php?page=dashboard.php");
    // }
} else {
    base::redirect(site_url . "panel/index.php?page=dashboard.php");
}
if (isset($_POST['submit'])) {
    $record = new post('new_post');
    $record_id = $record->get_id();
    $record->set_post_type('descriptive_exam_result');
    $record->set_title($username);
    $record->set_parent($post_id);
    $record->set_author($uid);
    $record->set_status('finished');
    $post_image = $_FILES['answer_sheet'];
    array_push($exams, $post_id);
    if (strlen($post_image['tmp_name']) > 1) {
        $image = $functions->upload($post_image);
        $image = site_url . upload_folder . $image;
        $insert_post_query = "INSERT INTO `post`( `post_title`, `guid`, `post_type`, `mime_type`,`post_parent`) 
        VALUES ('$image_alt','$image','attachment','media','$record_id')";
        base::RunQuery($insert_post_query);
    }
    $user->insert_user_meta(['descriptive-exams' => json_encode($exams, JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE)]);
    base::redirect(site_url . "panel/index.php?page=descriptive-exams/descriptive-result.php&exam=$record_id");
}
?>
<script>
    $('html').addClass('layout-menu-collapsed');
    $('#layout-menu').remove();
    $('#layout-navbar').remove();
</script>
<style>
    .layout-navbar-fixed .layout-wrapper:not(.layout-without-menu) .layout-page {
        padding: 0 !important;
    }

    .df-ui-wrapper.df-ui-controls {
        z-index: 2;
    }

    .df-ui-wrapper.df-ui-controls>* {
        display: none;
    }

    .df-ui-btn.df-ui-page {
        display: block;
    }

    .lesson-question div {
        width: 30px;
        text-align: center;
    }

    .lesson-question div label {
        position: relative;
    }

    .lesson-question div label input {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: 2;
        cursor: pointer;
    }

    .lesson-question div label span {
        width: 30px;
        height: 14px;
        border-radius: 10px;
        border: 2px solid #ff5b5c;
        display: block;
        transition: .5s;
    }

    .lesson-question div label input:checked+span {
        background: #ff5b5c;
    }
</style>
<script src=" https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/js/dflip.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/css/dflip.min.css "
    rel="stylesheet">
<div class="row g-3">
    <div class="col-12 md-4">
        <div class="card-body card">
            <div class="row g-3">
                <div class="col-12 col-md-6 d-none d-md-block">
                    <p class="mb-1"><span class="fw-bold">نام دانش آموز : </span><?php echo $username; ?></p>
                    <p class="mb-1"><span class="fw-bold">رشته تحصیلی دانش آموز : </span><?php $fos; ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><span class="fw-bold">عنوان آزمون‌ : </span><span
                            class="h4 mb-0"><?php echo $title; ?></span></p>
                    <p class="mb-1"><span class="fw-bold">شروع آزمون‌ :
                        </span><?php echo jdate('Y/m/j', $term_start); ?></p>
                </div>
                <div class="col-12">
                    <p class="alert alert-success">زمان باقی مانده: <span id="timer"></span></p>
                    <?php if (($date >= $term_start + 300 && $on_time == 'true') || $date >= $term_end) {
                        $error = true; ?>
                        <p class="alert alert-dark">مهلت ورود به جلسه سپری شده است.</p>
                    <?php } ?>
                </div>
            </div>
            <form action="" method="post" class="row align-items-end" enctype="multipart/form-data" >
                <div class="form-group mb-2 mb-lg-0 col-12 col-lg-4">
                    <h2 class="form-label">بارگزاری فایل پاسخنامه</h2>
                    <input onchange="loadFile(this, event)" name="answer_sheet" type="file" class="form-control">
                </div>
                <button type="submit" name="submit" class="btn btn-primary p-2 col-12 col-lg-2" id="form-submit"><i
                        class="fa-regular fa-pen-to-square"></i> ثبت جواب ها</button>
            </form>
        </div>
    </div>
    <?php if ($error == false || $role != 'student'): ?>
        <div class="col-12">
            <div class="_df_book" source="<?php echo $descriptive_exam_questions; ?>"></div>
        </div>
    <?php endif; ?>
</div>
<script>
    var countDownDate = <?php echo $remaining_time * 1000; ?>;
    var x = setInterval(function () {
        // Find the distance between now and the count down date
        countDownDate = countDownDate - 1000;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(countDownDate / (1000 * 60 * 60 * 24));
        var hours = Math.floor((countDownDate % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((countDownDate % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((countDownDate % (1000 * 60)) / 1000);
        if (days < 1)
            days = 0;
        if (hours < 1)
            hours = 0;
        if (minutes < 1)
            minutes = 0;
        if (seconds < 1)
            seconds = 0;

        // Output the result in an element with id="timer"
        document.getElementById("timer").innerHTML = days + ":" + hours + ":"
            + minutes + ":" + seconds;

        // If the count down is over, write some text 
        if (countDownDate < 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "وقت تمام است";
        }
    }, 1000);

    $('#exam-form').submit(function () {
        var c = confirm("ثبت نهایی و اتمام آزمون؟");
        return c; //you can just return c because it will be true or false
    });
</script>