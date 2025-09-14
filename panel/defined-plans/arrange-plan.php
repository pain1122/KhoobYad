<?php
$student_id = $_GET['uid'];
$student = new user($student_id);
$prefered_duration = $student->get_user_meta('prefered_duration');
$fos = $student->get_user_meta('fos');
$grade = $student->get_user_meta('grade');
if ($grade == 12) {
    $plan_q = "SELECT `post_id`,`post_title` FROM `post`
    INNER JOIN `tag_relationships` AS `course` ON `course`.`object_id` = `post`.`post_id`
    INNER JOIN `tag_relationships` AS `grade` ON `grade`.`object_id` = `post`.`post_id`
    WHERE `post_type` = 'defined_plan' AND `grade`.`tag_id` IN (10,11,12);";
} else {
    $plan_q = "SELECT `post_id`,`post_title` FROM `post`
    INNER JOIN `tag_relationships` AS `course` ON `course`.`object_id` = `post`.`post_id`
    INNER JOIN `tag_relationships` AS `grade` ON `grade`.`object_id` = `post`.`post_id`
    WHERE `post_type` = 'defined_plan' AND (`course`.`tag_id` = $fos AND `grade`.`tag_id` = $grade);";
}
$plans = base::FetchArray($plan_q);
$plans_html = "SELECT * FROM `plans` WHERE plan_id > 0 AND user_id = $student_id GROUP BY plan_id;";
foreach ($plans as $plan) {
    $plan_id = $plan['post_id'];
    $plan_name = $plan['post_title'];
    $plans_html .= "<option value='$plan_id'>$plan_name</option>";
}
$week_days = ['شنبه', 'یک شنبه', 'دو شنبه', 'سه شنبه', 'چهار شنبه', 'پنج شنبه', 'جمعه'];
$user_lessons_list = base::fetchArray("SELECT `plan_id` FROM `plans` WHERE plan_id > 0 AND user_id = $student_id GROUP BY plan_id;");
$lessons_list = [];
if (is_countable($user_lessons_list)) {
    foreach ($user_lessons_list as $lesson)
        $lessons_list[] = $lesson['plan_id'];
}
if (isset($_POST['submit'])) {
    $week = new plan('new_plan');
    $week->set_type('week');
    $week_id = $week->get_id();
    $week->set_status(0);
    $week->set_user($student_id);
    unset($_POST['submit']);

    $prefered_duration = $_POST['prefered_duration'];
    unset($_POST['prefered_duration']);
    $student->insert_user_meta(['prefered_duration' => $prefered_duration]);
    $day_count = 0;
    foreach ($_POST as $days => $fields) {
        if (!empty($days) && !empty($fields['lessons']) && !empty($fields['range'])) {
            $plan_count = 1;
            $day = new plan('new_plan');
            $day->set_title($days);
            $day->set_type('day');
            $day->set_parent($week_id);
            $day_id = $day->get_id();
            $day->set_user($student_id);
            $day->set_status(0);
            $day_arr = [];
            $lessons = $fields['lessons'];
            $range = $fields['range'];
            if (is_countable($lessons)) {
                for ($i = 0; $i < count($lessons); $i++) {
                    if (!empty($lessons[$i] && !empty($range[$i]))) {
                        $lesson = new blog($lessons[$i]);
                        $lesson->set_post_type('defined_plan');
                        $lesson_title = $lesson->get_title();
                        $lesson_id = $lesson->get_id();
                        $lesson_range = $range[$i];
                        if ($day_id > 0 && $lesson_id > 0 && strlen($lesson_title) > 0):
                            $plan = new plan('new_plan');
                            $plan->set_type('plan');
                            $plan->set_title($lesson_title);
                            $plan->set_parent($day_id);
                            $plan->set_plan($lesson_id);
                            $plan->set_user($student_id);
                            $plan->set_content($lesson_range);
                            $plan->set_status(0);
                            $day_arr[] = $plan_count;
                            if (!in_array($lesson_id, $lessons_list)) {
                                $session_id = base::fetchassoc("SELECT `post_id` FROM `post` WHERE `post_parent` = $lesson_id AND `post_type` = 'defined_plan' LIMIT 1;")['post_id'];
                                $lessons_list[] = $lesson_id;
                                $session_duration_q = "SELECT `value` FROM `post_meta` WHERE `post_id` = $session_id AND `key` = 'time'";
                                $session_duration = base::fetchAssoc($session_duration_q)['value'];
                                $session_duration = intval($session_duration);
                                if ($session_duration > $prefered_duration) {
                                    $session_duration = $prefered_duration;
                                }
                                $plan->set_duration($session_duration);
                                $plan->set_session($session_id);
                            }
                            $plan_count++;
                        endif;
                    }
                }
            }
            $format['week'][$week_days[$day_count]] = $day_arr;
            $day_count++;
        }
    }
    $format = json_encode($format, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $student->insert_user_meta(['weeks-format' => $format]);
    base::redirect("index.php?page=defined-plans/my-defined-plans.php&uid=$student_id");
}
?>
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<form action="" method="post" enctype="multipart/form-data">
    <div class="accordion mb-4" id="collapsibleSection">
        <div class="card">
            <div class="card-header">
                <h3>ثبت برنامه برای دانش آموز <?php echo $student_name; ?></h3>
                <!-- <p class="alert alert-success">جهت دانلود فایل راهنما <a href="" download>اینجا کلیک کنید</a>.</p> -->
            </div>
            <div class="card-body">
                <ul class="p-3 mb-3" id="plans_container">
                    <li class="row mb-4">
                        <h5>دروس روز شنبه</h5>
                        <div id="saturday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="saturday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="saturday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز یکشنبه</h5>
                        <div id="sunday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="sunday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="sunday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز دو شنبه</h5>
                        <div id="monday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="monday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="monday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز سه شنبه</h5>
                        <div id="tuesday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="tuesday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="tuesday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز چهار شنبه</h5>
                        <div id="wednesday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="wednesday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="wednesday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز پنجشنبه</h5>
                        <div id="thursday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="thursday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="thursday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                    <li class="row mb-4">
                        <h5>دروس روز جمعه</h5>
                        <div id="friday" class="row">
                            <div class="col-6 col-md-4 col-lg-2 mb-2"><label class="form-label">بازه مطالعه
                                    برنامه</label><input class="form-control mt-2 mb-4" name="friday[range][]"
                                    placeholder="مثلا 9 الی 10"><select class="select2" name="friday[lessons][]">
                                    <option>انتخاب جلسه</option><?php echo $plans_html; ?>
                                </select></div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary add-lesson mt-3"><i
                                        class="fa fa-plus-square"></i></button>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="row mt-4 align-items-end">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">بازه مطالعه دلخواه (به دقیقه)</label>
                        <input class="form-control mt-2" name="prefered_duration"
                            value="<?php echo $prefered_duration; ?>" placeholder="مثلا 90">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <button type="submit" name="submit" class="btn btn-success p-2">
                            <i class="fa-regular fa-pen-to-square"></i> ثبت</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script>

    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'انتخاب',
                dropdownParent: $this.parent()
            });
        });
    }
    $('.add-lesson').click(function () {
        $(this).parent().parent().find('select').select2('destroy');
        var select_clone = $(this).parent().parent().find('.col-6:first-child').clone();
        $(select_clone).find('select').prop("selected", false);
        $(select_clone).find('input').val('');
        $(select_clone).prepend(`<button type="button" class="btn-close text-reset ml-2" onclick="$(this).parent().remove();"></button>`);
        $(this).parent().before(select_clone);
        $(this).parent().parent().find('select').select2({
            placeholder: 'انتخاب',
            dropdownParent: $(this).parent()
        });
    });
</script>