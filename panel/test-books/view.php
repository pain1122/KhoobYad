<?php
$error = false;
if (isset($_GET['test-book'])) {
    $user = new user($uid);
    $username = $user->get_nick_name();
    $role = $user->get_user_meta('role');
    $fos = $user->get_user_meta('fos');
    $test_books = json_decode($user->get_user_meta('test-books'), true);
    if (!$test_books)
        $test_books = [];
    // elseif (in_array($_GET['test-book'], $test_books)) {
    //     echo "<script>
    //     alert('شما قبلا در این آزمون شرکت کرده اید و اجازه ورود مجدد ندارید!');
    //     window.location.replace('" . site_url . "panel/index.php?page=test-books/rankings.php&test-book=" . $_GET['test-book'] . "');
    //     </script>";
    // }
    $post_id = intval($_GET['test-book']);
    $obj = new post($post_id);
    $title = $obj->get_title();
    $duration = intval($obj->get_meta('duration'));
    $negative_score = $obj->get_meta('negative_score');
    if ($negative_score === false)
        $negative_score = 'ندارد';
    else
        $negative_score = 'دارد';
    $test_book_questions = $obj->get_meta('test_book_questions');
    $members = json_decode($obj->get_meta('members'), true);
    if (empty($members))
        $members = [];
    $is_in_test_book = false;
    foreach ($members as $member) {
        if ($member['value'] == $uid)
            $is_in_test_book = true;
    }
    $classes = json_decode($obj->get_meta('classes'), true);
    $lessons = json_decode($obj->get_meta('lessons'), true);
    if (is_countable($lessons)) {
        $max_answers = 0;
        $lessons_count = count($lessons);
        foreach ($lessons as $lesson) {
            $questions = $lesson['questions'];
            foreach ($questions as $question) {
                $answer_count = $question['answer_count'];
                if ($answer_count > $max_answers)
                    $max_answers = $answer_count;
            }
        }
    }
    if (is_countable($classes) && count($classes) > 0) {
        foreach ($classes as $key => $class) {
            $selected_members_q = "SELECT `user_id` FROM `user_meta` WHERE `key` = 'class_groups' AND `value` LIKE '%{$class['value']}%'";
            $selected_members = base::FetchArray($selected_members_q)['user_id'];
            if (is_countable($selected_members) && count($selected_members) > 0)
                foreach ($selected_members as $member)
                    $members[] = $member;
        }
    }

    if ($role == 'student' && !$is_in_test_book && !in_array($post_id, $test_books)) {
        base::redirect(site_url . "panel/index.php?page=dashboard.php");
    }
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
    unset($_POST['submit']);
    $record = new post('new_post');
    $record_id = $record->get_id();
    $record->set_post_type('test_book_result');
    $record->set_title($username);
    $record->set_parent($post_id);
    $record->set_author($uid);
    $record->set_status('finished');
    array_push($test_books, $post_id);

    $all_correct = $all_wrong = $all_ng = 0;
    $answers = $_POST;
    $questions = [];
    $j = 1;
    $final_score_arr = ['scores' => 1, 'sum' => 0];
    for ($i = 1; $i < $lessons_count + 1; $i++) {
        $questions = $lessons[$i]['questions'];
        $coefficient = intval($lessons[$i]['coefficient']);
        $correct = $wrong = $ng = 0;
        $question_count = 1;
        foreach ($questions as $question) {
            $answer_count = $question['answer_count'];
            $answer_option = $question['answer_option'];
            $answer_video = $question['answer_video'];
            if ($answers["lessons"][$i]["questions"][$question_count]["answer_option"] == $answer_option) {
                $answers["lessons"][$i]["questions"][$question_count]["correct"] = "true";
                $correct++;
            } elseif ($answers["lessons"][$i]["questions"][$question_count]["answer_option"] == 0) {
                $answers["lessons"][$i]["questions"][$question_count]["correct"] = "NG";
                $ng++;
            } else {
                $answers["lessons"][$i]["questions"][$question_count]["correct"] = "false";
                $wrong++;
            }
            $answers["lessons"][$i]["questions"][$question_count]["video"] = $answer_video;
            $j++;
            $question_count++;
        }
        $answers["lessons"][$i]['correct'] = $correct;
        $answers["lessons"][$i]['wrong'] = $wrong;
        $answers["lessons"][$i]['ng'] = $ng;
        $all_correct += $correct;
        $all_wrong += $wrong;
        $all_ng += $ng;
        $correct *= 3;
        if(!$questions)
            $questions = [''];
        $raw = floor((($correct - $wrong) / (count($questions) * 3)) * 100);

        $score = floor($raw * $coefficient * 10) + 5000;
        // echo "نمره خام درس $i = $raw. ضریب درس $i = $coefficient. تراز درس $i = $score.<br>";
        $record->insert_meta([$lessons[$i]['title'] => $score]);
        $answers["lessons"][$i]["score"] = $score;
        $answers["lessons"][$i]["raw"] = $raw;
        $final_score_arr['scores'] += $score * $coefficient;
        $final_score_arr['sum'] += $coefficient;
    }
    // print_r($final_score_arr);
    $final_score = $final_score_arr['scores'] / $final_score_arr['sum'];
    // echo "<br>$final_score";
    $answers = json_encode($answers, JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    $record->set_content($answers);
    $record->insert_meta(['corrects' => $all_correct, 'wrong' => $all_wrong, 'ng' => $all_ng]);
    $record->set_excerpt($final_score);
    $user->insert_user_meta(['test-books' => json_encode($test_books, JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE)]);
    base::redirect(site_url . "panel/index.php?page=test-books/result.php&test-book=$record_id");
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
    <span class="show-answers d-flex d-md-none">پاسخبرگ</span>
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
                    <p class="mb-1"><span class="fw-bold">نمره منفی : </span><?php echo $negative_score; ?></p>
                </div>
                <?php if (!isset($_GET['type']) || $_GET['type'] != 'practice'): ?>
                    <div class="col-12">
                        <p class="alert alert-success">زمان باقی مانده: <span id="timer"></span></p>
                    </div>
                <?php endif; ?>
                <div class="mt-4 col-12 col-lg-2">
                    <button type="submit" onclick="$('#form-submit').trigger('click');" name="submit"
                        class="btn w-100 btn-primary p-2"><i class="fa-regular fa-pen-to-square"></i> ثبت جواب
                        ها</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($error == false || $role != 'student'): ?>
        <div class="col-12 col-lg-7">
            <div class="_df_book" source="<?php echo $test_book_questions; ?>"></div>
        </div>
        <div class="col-12 col-lg-5" id="answers-sheet">
            <form action="" method="post" class="card-body card p-2 p-md-4 pb-5" id="test-book-form"
                style="height: 95vh;overflow:auto">
                <div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-1">
                    <h6 class="col-1 d-flex align-items-center ml-2">سوال</h6>
                    <?php for ($coun_answers = 1; $coun_answers <= $max_answers; $coun_answers++) { ?>
                        <div class="text-center ml-1 mb-2">
                            <?php echo $coun_answers; ?>
                        </div>
                    <?php } ?>
                </div>
                <?php $j = 1;
                for ($i = 1; $i < $lessons_count + 1; $i++) {
                    $questions = $lessons[$i]['questions'];
                    $question_count = 1;
                    foreach ($questions as $question) {
                        $answer_count = $question['answer_count'];
                        $answer_option = $question['answer_option'] ?>
                        <div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-1">
                            <h6 class="col-1 d-flex align-items-center ml-2"><?php echo $j; ?> -</h6>
                            <input class="d-none" type="radio"
                                id="lessons<?php echo $i; ?>questions<?php echo $question_count; ?>answer_option"
                                name="lessons[<?php echo $i; ?>][questions][<?php echo $question_count; ?>][answer_option]"
                                value="0" checked>
                            <?php for ($coun_answers = 1; $coun_answers < $answer_count + 1; $coun_answers++) { ?>
                                <div class=" answer ml-1 mb-2">
                                    <label><input type="radio"
                                            name="lessons[<?php echo $i; ?>][questions][<?php echo $question_count; ?>][answer_option]"
                                            value="<?php echo $coun_answers; ?>"><span></span></label>
                                </div>
                            <?php } ?>
                            <button class="btn btn-xs btn-danger mr-auto" type="button"
                                onclick="removeAnswer('lessons<?php echo $i; ?>questions<?php echo $question_count; ?>answer_option')">حذف
                                جواب</button>
                        </div>
                        <?php $j++;
                        $question_count++;
                    } ?>
                <?php } ?>
                <button type="submit" name="submit" id="form-submit" class="d-none"></button>
            </form>
        </div>
    <?php endif; ?>
</div>
<script>
    <?php if (!isset($_GET['type']) || $_GET['type'] != 'practice'): ?>
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
    <?php endif; ?>
    function removeAnswer(id) {
        $("#" + id).trigger("click");
    }
    $('.show-answers').click(function () {
        if ($('#answers-sheet').hasClass('active')) {
            $('#answers-sheet').removeClass('active');
            $(this).text('پاسخبرگ');
        } else {
            $('#answers-sheet').addClass('active');
            $(this).text('دفترچه');
        }
    })

    $('#test-book-form').submit(function () {
        var c = confirm("ثبت نهایی و اتمام آزمون؟");
        return c; //you can just return c because it will be true or false
    });
</script>