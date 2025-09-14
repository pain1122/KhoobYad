<?php
$error = false;
if(isset($_POST['submit']) && $role !='student'){
    $post_id = intval($_GET['exam']);
    $obj = new post($post_id);
    $score = $_POST['score'];
    $teacher_notes = $_POST['teacher-notes'];
    $correct = $_POST['correct'];
    $wrong = $_POST['wrong'];
    $ng = $_POST['ng'];
    $metas = [
        'teacher_notes' => $teacher_notes,
        'correct' => $correct,
        'wrong' => $wrong,
        'ng' => $ng
    ];
    $obj->set_excerpt($score);
    $obj->insert_meta($metas);
}
if (isset($_GET['exam']) && $_GET['exam'] > 0) {
    $user = new user($uid);
    $username = $user->get_nick_name();
    $role = $user->get_user_meta('role');
    $grade = $user->get_user_meta('grade');
    $fos = $user->get_user_meta('fos');
    $post_id = intval($_GET['exam']);
    $obj = new post($post_id);
    $answers = json_decode($obj->get_content(), true);
    $score = $obj->get_excerpt();
    $correct = $obj->get_meta('correct');
    $wrong = $obj->get_meta('wrong');
    $ng = $obj->get_meta('ng');
    $teacher_notes = $obj->get_meta('teacher_notes');
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
    if (!$score)
        $score = 'تعیین نشده';
    $answer = base::FetchAssoc("SELECT `guid` FROM `post` WHERE `post_parent` = $post_id AND `post_type` = 'attachment'")['guid'];
    // print_r($answers);
    $now = time();
} else {
    base::redirect(site_url . "panel/index.php?page=dashboard.php");
}
?>
<div class="row g-3">
    <span class="bx bx-pencil show-answers d-flex d-md-none"></span>
    <div class="col-12 md-4">
        <div class="card-body card">
            <div class="row justify-content-between g-3">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><span class="fw-bold">نام دانش آموز : </span><?php echo $username; ?></p>
                    <p class="mb-1"><span class="fw-bold">پایه تحصیلی دانش آموز : </span><?php echo $grade; ?></p>
                    <p class="mb-1"><span class="fw-bold">رشته تحصیلی دانش آموز : </span><?php $fos; ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><span class="fw-bold">عنوان آزمون‌ : </span><span
                            class="h4 mb-0"><?php echo $title; ?></span></p>
                    <p class="mb-1"><span class="fw-bold">شروع آزمون‌ :
                        </span><?php echo jdate('H:i - Y/m/j', $term_start); ?></p>
                    <p class="mb-1"><span class="fw-bold">پایان آزمون‌ :
                        </span><?php echo jdate('H:i - Y/m/j', $term_end); ?></p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-2">
                        <h6>نمره: <b><?php echo $score; ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-2">
                        <h6>سوالات درست: <b><?php echo $correct; ?></b></h5>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-2">
                        <h6>سوالات غلط: <b><?php echo $wrong; ?></b></h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card p-2">
                        <h6>بدون پاسخ: <b><?php echo $ng; ?></b></h6>
                    </div>
                </div>
                <?php if($teacher_notes) : ?>
                    <p><strong>یادداشت مصحح:</strong> <?php echo $teacher_notes; ?></p>
                <?php endif; ?>
                <?php if ($now > $term_end): ?>
                    <div class="col-12">
                        <a href="<?php echo $exam_answers; ?>" class="btn btn-primary" download>پاسخنامه تشریحی</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($role != 'student'): ?>
        <div class="col-12 md-4">
            <form action="" method="post" class="card-body card">
                <div class="row justify-content-between g-3">
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">نمره آزمون</label>
                        <input type="text" name="score" value="<?php echo $score; ?>" class="form-control"
                            placeholder="نمره دانش آموز را وارد کنید.">
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">سوالات درست</label>
                        <input type="number" name="correct" value="<?php echo $correct; ?>" class="form-control"
                            placeholder="سوالات درست دانش آموز را وارد کنید.">
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">سوالات غلط</label>
                        <input type="number" name="wrong" value="<?php echo $wrong; ?>" class="form-control"
                            placeholder="سوالات غلط دانش آموز را وارد کنید.">
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">سوالات بدون پاسخ</label>
                        <input type="number" name="ng" value="<?php echo $ng; ?>" class="form-control"
                            placeholder="سوالات بدون پاسخ دانش آموز را وارد کنید.">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">یادداشت مصحح</label>
                        <textarea name="teacher-notes" class="form-control"
                            placeholder="یادداشت مصحح را وارد کنید."><?php echo $teacher_notes; ?></textarea>
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <a href="<?php echo $answer; ?>" download class="btn btn-primary p-2"><i
                                class="fas fa-download ml-2"></i> دانلود فایل پاسخ</a>
                        <button type="submit" name="submit" class="btn btn-success p-2"><i
                                class="fa-regular fa-pen-to-square"></i> انتشار</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <div class="card mb-4" id="lessons-wrapper">
            <div class="nav-align-top">
                <div class="tab-content" style="box-shadow: none;" id="lesson-content">
                    <?php for ($i = 1; $i < $lessons_count + 1; $i++) {
                        $questions = $lessons[$i]['questions'];
                        $raw = $answers["lessons"][$i]['raw'];
                        $score = round($answers["lessons"][$i]['score']);
                        $lesson_rank = 0;
                        $rank_q = "SELECT `author` FROM `post` INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` WHERE `key` = '" . $lessons[$i]['title'] . "' AND `post`.`post_id` IN (SELECT `post`.`post_id` FROM `post` WHERE `post_parent` = $parent_id AND `post_type` = 'exam_result') ORDER BY `post_meta`.`value` DESC;";
                        // echo $rank_q;
                        $rank_resault = base::FetchArray($rank_q);
                        for ($x = 1; $x <= count($rank_resault); $x++):
                            if ($rank_resault[$x - 1]['author'] == $uid):
                                $lesson_rank = $x;
                                break;
                            endif;
                        endfor;

                        ?>
                        <div class='tab-pane fade <?php if ($i == 1)
                            echo 'active show'; ?>' id='lesson-content<?php echo $i; ?>' role='tabpanel'>
                            <h5 class='w-100 mb-4'>سوالات آزمون <?php echo $title; ?></h5>
                            <?php $j = 1;
                            foreach ($questions as $question) {
                                $answer_topic = $question['answer_topic'];
                                $answer_video = $question['answer_video'];
                                $answer_status = $answers["lessons"][$i]["questions"][$j]["correct"];
                                ?>
                                <div class="accordion" id="collapsibleSection">
                                    <div class="card accordion-item border-1 <?php if ($j == 1)
                                        echo 'active'; ?>">
                                        <h2 class="accordion-header">
                                            <button type="button" aria-expanded="<?php if ($j == 1)
                                                echo 'true';
                                            else
                                                echo 'false'; ?>" class="accordion-button <?php if ($j != 1)
                                                      echo 'collapsed'; ?>" data-bs-toggle="collapse"
                                                data-bs-target="#<?php echo "lesson-$i-question-$j"; ?>">
                                                سوال شماره <?php echo $j; ?>
                                                <?php if ($answer_topic): ?>
                                                    <span class="badge bg-label-warning mr-3">مبحث :
                                                        <?php echo $answer_topic; ?></span>
                                                <?php endif; ?>
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse <?php if ($j == 1)
                                            echo 'show'; ?>" id="<?php echo "lesson-$i-question-$j"; ?>"
                                            data-bs-parent="#collapsibleSection">
                                            <?php if ($now > $term_end): ?>
                                                <div class="accordion-body d-flex align-items-center flex-wrap">
                                                    <a class="btn btn-primary btn-sm p-2 mr-3 mb-2"
                                                        href="<?php echo $answer_video; ?>" data-bs-toggle="tooltip"
                                                        data-bs-offset="0,8" data-bs-placement="top" data-color="primary" title=""
                                                        data-bs-original-title="لینک ویدئو سوال"><i class="bx bx-link"></i></a>
                                                </div>
                                            <?php endif; ?>
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