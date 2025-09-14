<?php
$error = false;
if (isset($_GET['exam']) && $_GET['exam'] > 0) {
    $post_id = intval($_GET['exam']);
    $obj = new post($post_id);
    $answers = json_decode($obj->get_content(), true);
    $score = $obj->get_excerpt();
    $correct = $obj->get_meta('corrects');
    $wrong = $obj->get_meta('wrong');
    $ng = $obj->get_meta('ng');
    $parent_id = $obj->get_parent();
    $parent = new post($parent_id);
    $title = $parent->get_title();
    $term_start = $parent->get_meta('term_start');
    $term_end = $parent->get_meta('term_end');
    $negative_score = $parent->get_meta('negative_score');
    $exam_answers = $parent->get_meta('exam_answers');
    if ($negative_score === false)
        $negative_score = 'ندارد';
    else
        $negative_score = 'دارد';
    $lessons = json_decode($parent->get_meta('lessons'), true);
    if (is_countable($lessons))
        $lessons_count = count($lessons);
    $exam_predictions = $parent->get_meta('exam_prediction');
    $exam_prediction = 'نامشخص';
    if (!empty($exam_predictions)) {
        $exam_predictions = json_decode($exam_predictions, true);
        $matched = false;
        $lowestMin = PHP_INT_MAX;
        $highestRankForMin = null;
        $score = round($score);
        foreach ($exam_predictions as $score_range => $rank_range) {
            list($min, $max) = array_map('intval', explode('-', $score_range));
            if ($min < $lowestMin) {
                $lowestMin = $min;
                $highestRankForMin = explode('-', $rank_range)[1];
            }
            if ($score >= $min && $score <= $max) {
                $exam_prediction = $rank_range;
                $matched = true;
                break;
            }
        }
        if (!$matched && $score < $lowestMin) {
            $exam_prediction = $highestRankForMin . '+';
        }
    }
    $user_id = $obj->get_author();
    $user = new user($user_id);
    $username = $user->get_nick_name();
    $role = $user->get_user_meta('role');
    $grade = $user->get_user_meta('grade');
    $fos = $user->get_user_meta('fos');
    $rank_q = "WITH author_scores AS (SELECT author, MAX(post_excerpt*1) AS top_score FROM post
        WHERE `post_type`= 'exam_result'
        AND post_parent = $parent_id
        AND author > 0
        GROUP BY author
    ), ranked AS ( SELECT author, top_score, RANK() OVER (ORDER BY top_score DESC) AS author_rank FROM author_scores)
    SELECT author_rank FROM ranked WHERE author = $user_id;";
    $rank = base::FetchAssoc($rank_q)['author_rank'];
    if (!$rank)
        $rank = 'تعیین نشده';
    $now = time();
    if (!empty($grade)) {
        $grade = new tag($grade);
        $grade = $grade->get_name();
    }
    if (!empty($fos)) {
        $fos = new tag($fos);
        $fos = $fos->get_name();
    }
} else {
    base::redirect(site_url . "panel/index.php?page=dashboard.php");
}
?>
<script src="assets/vendor/libs/plyr/plyr.js"></script>
<link rel="stylesheet" href="assets/vendor/libs/plyr/plyr.css">
<div class="row g-3">
    <span class="bx bx-pencil show-answers d-flex d-md-none"></span>
    <div class="col-12 md-4">
        <div class="card-body card">
            <div class="row justify-content-between g-3">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><span class="fw-bold">نام دانش آموز : </span><?php echo $username; ?></p>
                    <p class="mb-1"><span class="fw-bold">پایه تحصیلی دانش آموز : </span><?php echo $grade; ?></p>
                    <p class="mb-1"><span class="fw-bold">رشته تحصیلی دانش آموز : </span><?php echo $fos; ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><span class="fw-bold">عنوان آزمون‌ : </span><span
                            class="h4 mb-0"><?php echo $title; ?></span></p>
                    <p class="mb-1"><span class="fw-bold">شروع آزمون‌ :
                        </span><?php echo jdate('H:i - Y/m/j', $term_start); ?></p>
                    <p class="mb-1"><span class="fw-bold">پایان آزمون‌ :
                        </span><?php echo jdate('H:i - Y/m/j', $term_end); ?></p>
                    <p class="mb-1"><span class="fw-bold">نمره منفی : </span><?php echo $negative_score; ?></p>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>رتبه: <b><?php echo $rank; ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>تراز: <b><?php echo round($score); ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>تخمین رتبه: <b><?php echo $exam_prediction; ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>سوالات درست: <b><?php echo $correct; ?></b></h5>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>سوالات غلط: <b><?php echo $wrong; ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card p-2">
                        <h6>بدون پاسخ: <b><?php echo $ng; ?></b></h6>
                    </div>
                </div>
                <?php if ($now > $term_end): ?>
                    <div class="col-12">
                        <a href="<?php echo $exam_answers; ?>" class="btn btn-primary" download>پاسخنامه تشریحی</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card mb-4<?php if (!$lessons_count)
            echo " d-none"; ?>" id="lessons-wrapper">
            <div class="nav-align-top">
                <ul class="nav nav-pills p-4 justify-content-between justify-content-sm-start" role="tablist"
                    id="lesson-tab">
                    <?php for ($i = 1; $i < $lessons_count + 1; $i++) { ?>
                        <li id="lesson-tab<?php echo $i; ?>">
                            <button type="button" class="nav-link px-2 px-md-4 <?php if ($i == 1)
                                echo 'active'; ?>" role="tab" data-bs-toggle="tab"
                                data-bs-target="#lesson-content<?php echo $i; ?>"
                                aria-controls="lesson-content<?php echo $i; ?>" aria-selected="true">درس
                                <?php echo $lessons[$i]['title']; ?>
                            </button>
                        </li>
                    <?php } ?>
                </ul>
                <div class="tab-content" style="box-shadow: none;" id="lesson-content">
                    <?php for ($i = 1; $i < $lessons_count + 1; $i++) {
                        $questions = $lessons[$i]['questions'];
                        $raw = $answers["lessons"][$i]['raw'];
                        $score = round($answers["lessons"][$i]['score']);
                        $rank_q = "WITH course_scores AS (SELECT p.author, CAST(pm.`value` AS UNSIGNED) AS score FROM post AS p
                            INNER JOIN post_meta AS pm ON pm.post_id = p.post_id
                            WHERE pm.`key` = '" . $lessons[$i]['title'] . "'
                            AND p.post_parent = $parent_id
                            AND p.post_type = 'exam_result'
                        ), ranked_course AS (SELECT author, score, RANK() OVER (ORDER BY score DESC) AS course_rank FROM course_scores)
                        SELECT course_rank FROM ranked_course WHERE author = $user_id;";
                        $lesson_rank = base::FetchAssoc($rank_q)['course_rank'];
                        $predictions = $lessons[$i]['prediction'];
                        $prediction = 'نامشخص';
                        $matched = false;
                        $lowestMin = PHP_INT_MAX;
                        $highestRankForMin = null;
                        if (is_countable($predictions) && count($predictions) > 0) {
                            foreach ($predictions as $rank_range => $score_range) {
                                list($min, $max) = array_map('intval', explode('-', $score_range));
                                if ($min < $lowestMin) {
                                    $lowestMin = $min;
                                    $highestRankForMin = explode('-', $rank_range)[1];
                                }
                                if ($score >= $min && $score <= $max) {
                                    $prediction = $rank_range;
                                    $matched = true;
                                    break;
                                }
                            }
                            if (!$matched && $score < $lowestMin) {
                                $prediction = $highestRankForMin . '+';
                            }
                        }
                        ?>
                        <div class='tab-pane fade <?php if ($i == 1)
                            echo 'active show'; ?>' id='lesson-content<?php echo $i; ?>' role='tabpanel'>
                            <h5 class='w-100 mb-4'>سوالات درس <?php echo $lessons[$i]['title']; ?></h5>
                            <div class="row mb-4">
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="card p-2 p-md-3">
                                        <p>درصد : <b><?php echo $raw; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card p-2 p-md-3 mb-3">
                                        <p>تراز : <b><?php echo $score; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card p-2 p-md-3 mb-3">
                                        <p>تخمین رتبه:
                                            <b><?php echo $prediction; ?></b>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card p-2 p-md-3 mb-3">
                                        <p>رتبه : <b><?php echo $lesson_rank; ?></b></p>
                                    </div>
                                </div>
                            </div>
                            <?php $j = 1;
                            foreach ($questions as $question) {
                                // print_r($question);
                                $answer_topic = $question['answer_topic'];
                                $answer_option = $question['answer_option'];
                                $answer_video = $question['answer_video'];
                                $answered_option = $answers["lessons"][$i]["questions"][$j]["answer_option"];
                                $answer_status = $answers["lessons"][$i]["questions"][$j]["correct"];
                                ?>
                                <div class="accordion" id="collapsibleSection">
                                    <div class="card accordion-item border-1 <?php if ($j == 1)
                                        echo 'active'; ?>">
                                        <h2 class="accordion-header">
                                            <button type="button" aria-expanded="<?php if ($j == 1)
                                                echo 'true';
                                            else
                                                echo 'false'; ?>" class="accordion-button flex-wrap <?php if ($j != 1)
                                                      echo 'collapsed'; ?>" data-bs-toggle="collapse"
                                                data-bs-target="#<?php echo "lesson-$i-question-$j"; ?>">
                                                <p class="m-0">
                                                    <?php if ($answer_status == 'true'): ?>
                                                        <i class='bx bxs-check-circle ml-2'></i>
                                                    <?php elseif ($answer_status == 'false'): ?>
                                                        <i class='bx bxs-x-circle ml-2'></i>
                                                    <?php else: ?>
                                                        <i class='bx bxs-error-circle ml-2'></i>
                                                    <?php endif; ?>
                                                    سوال شماره <?php echo $j; ?>
                                                    <?php if ($answer_topic): ?>
                                                        <span class="btn-xs bg-label-warning d-block mt-2">مبحث :
                                                            <?php echo $answer_topic; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </p>
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse <?php if ($j == 1)
                                            echo 'show'; ?>" id="<?php echo "lesson-$i-question-$j"; ?>"
                                            data-bs-parent="#collapsibleSection">
                                            <div class="accordion-body d-flex align-items-center flex-wrap">
                                                <?php if ($now > $term_end): ?>
                                                    <span class="ml-auto mb-2">
                                                        <i class='bx bxs-check-square'></i>
                                                        جواب صحیح: گزینه <?php echo $answer_option; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="ml-auto mb-2">
                                                    <i class='bx bx-check-square'></i>
                                                    انتخاب شما: گزینه <?php echo $answered_option; ?>
                                                </span>
                                                <?php if (!empty($answer_video)) { ?>
                                                    <a class="btn btn-primary btn-sm p-2  d-flex align-items-center"
                                                        href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#video"
                                                        onclick="loadVideo('<?php echo $answer_video; ?>');">
                                                        <i class="fa-solid fa-eye ml-2"></i> ویدئو پاسخ
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $j++;
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="video" tabindex="-1" aria-hidden="true" style="background: #000;">
    <div class="modal-dialog modal-xl modal-simple" style="top: 20vh">
        <div class="modal-content p-0 pt-4" style="background:none;box-shadow:none">
            <div class="modal-body p-0 d-flex align-items-center justify-content-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <video controlsList="nodownload" id="videoPlayer" controls width="80%">
                    <source id="videoPlayersrc" src="" type="video/mp4" />
                </video>
            </div>
        </div>
    </div>
</div>
<script>
    const player2 = new Plyr('#videoPlayer2');
    const src = document.getElementById("videoPlayersrc");
    const player = document.getElementById("videoPlayer");

    function loadVideo(file) {
        player.pause();
        src.setAttribute("src", file);
        player.load();
    }
</script>