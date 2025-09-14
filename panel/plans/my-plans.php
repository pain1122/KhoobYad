<?php

$user_id = intval($_GET['uid']);
$user = new user($user_id);
$username = $user->get_nick_name();
$grade = $user->get_grade();
$fos = $user->get_fos();
$school = $user->get_school();
$adviser = $user->get_adviser();
$phone_number = $user->get_user_meta('phonenumber');
$firstname = $user->get_user_meta('firstname');
$lastname = $user->get_user_meta('lastname');
$participated = $user->get_user_meta('plans_league');
$avatar = base::displayphoto($user->get_user_meta('avatar'));
if (isset($_POST['add_week'])) {
    $week = new post('new_post');
    $week_id = $week->get_id();
    $week->set_author($user_id);
    $week->set_post_type('weekly_plan');
    $week_title = $_POST['week_title'];
    $meta["week_title"] = $week_title;
    $week_pdf = $_POST['week_pdf'];
    $meta["week_pdf"] = $week_pdf;
    $week->insert_meta($meta);
    $days = ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنجشنبه", "جمعه"];
    for ($i = 0; $i < 7; $i++) {
        $day = new post('new_post');
        $day->set_author($user_id);
        $day->set_parent($week_id);
        $day->set_title($days[$i]);
        $day->set_post_type('daily-plan');
    }
    $current_week = intval($_POST['add_week']) + 1;
    $chat = new post('new_post');
    $chat_id = $chat->get_id();
    $members = [$uid, $user_id];
    $meta['members'] = json_encode($members);
    if ($week_title)
        $chat_title = "گزارش روزانه $week_title";
    else
        $chat_title = "گزارش روزانه مربوط به هفته $current_week";
    $chat->set_title($chat_title);
    $chat->set_author($uid);
    $chat->set_post_type('chat');
    $chat->set_status('plan-report');
    $chat->set_parent($week_id);
    $chat->insert_meta($meta);

    base::redirect("index.php?page=plans/my-plans.php&uid=$user_id&week=$week_id");
}

if (isset($_POST['submit-plan']) && isset($_GET['week'])) {
    $day_number = $_POST['day'];
    $current_week = intval($_GET['week']);
    $day_q = "SELECT `post_id` FROM `post` WHERE `post_type` = 'daily-plan' AND `post_parent` = $current_week";
    $day_id = base::FetchArray($day_q)[$day_number - 1]['post_id'];
    if (!$day_id)
        $day_id = base::FetchAssoc("SELECT `post_parent` FROM `post` WHERE `post_id` = {$_GET['plan']}")['post_parent'];
    $plan_id = $_GET['plan'];
    $where_plan = '';
    if ($plan_id)
        $where_plan = "AND `post_id` != $plan_id";
    $plans_query = "SELECT REPLACE(`post_title`, ':', '') as `start`,REPLACE(`post_excerpt`, ':', '') as `end` FROM `post` WHERE `post_parent` = $day_id $where_plan";
    $plans = base::FetchArray($plans_query);

    $plan_text = $_POST['plan_text'];
    $min_date = $_POST['min_date'];
    $max_date = $_POST['max_date'];
    if ($max_date == '00:00')
        $max_date = '24:00';

    foreach ($plans as $plan) {
        $min = str_replace(':', '', $min_date);
        $max = str_replace(':', '', $max_date);
        if (($min > $plan['start'] && $min < $plan['end']) || ($max > $plan['start'] && $max < $plan['end']) || ($min <= $plan['start'] && $max >= $plan['end'])) {
            $_SESSION['interfere_error'] = 'تداخل در ساعت برنامه ها';
            break;
        }
    }
    if (!$_SESSION['interfere_error']) {
        if (isset($plan_id)) {
            $plan = new post($plan_id);
        } else
            $plan = new post('new_post');

        $plan->set_post_type('plan');
        $meta['score'] = $_POST['score'];

        if ($role == 'student') {
            $meta['comment'] = $plan_text;
        } else {
            $date1 = new DateTime($min_date);
            $date2 = new DateTime($max_date);
            $time_diff = $date1->diff($date2);
            $duration = intval($time_diff->h) * 60 + intval($time_diff->i);
            $class_link1 = $_POST['class_link1'];
            $class_link_title1 = $_POST['class_link_title1'];
            $class_link2 = $_POST['class_link2'];
            $class_link_title2 = $_POST['class_link_title2'];
            $class_link3 = $_POST['class_link3'];
            $class_link_title3 = $_POST['class_link_title3'];
            $meta['class_link1'] = $class_link1;
            $meta['class_link_title1'] = $class_link_title1;
            $meta['class_link2'] = $class_link2;
            $meta['class_link_title2'] = $class_link_title2;
            $meta['class_link3'] = $class_link3;
            $meta['class_link_title3'] = $class_link_title3;
            $plan->set_slug($duration);
            $plan->set_content($plan_text);
            $plan->set_title($min_date);
            $plan->set_excerpt($max_date);
            $plan->set_parent($day_id);
            $plan->set_author($user_id);
        }
        $plan->insert_meta($meta);
    }
    if (isset($_GET['plan']))
        base::redirect("index.php?page=plans/my-plans.php&uid=$user_id&week=$current_week");
}

if (isset($_POST['cansel-plan']) && isset($_GET['week'])) {
    $current_week = intval($_GET['week']);
    base::redirect("index.php?page=plans/my-plans.php&uid=$user_id&week=$current_week");
}
if (isset($_POST['delete-plan']) && isset($_GET['week'])) {
    if (isset($_GET['plan'])) {
        $plan_id = $_GET['plan'];
        base::RunQuery("DELETE FROM `post` WHERE `post_id` = " . $plan_id);
        base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = " . $plan_id);
        $current_week = intval($_GET['week']);
        base::redirect("index.php?page=plans/my-plans.php&uid=$user_id&week=$current_week");
    }
}
if (isset($_POST['week-report']) && isset($_GET['week'])) {
    $week_id = $_GET['week'];
    $score = $_POST['score'];
    $message = $_POST['message'];
    $week = new post($week_id);
    $meta["week_score"] = $score;
    $meta["week_message"] = $message;
    $week->insert_meta($meta);
}

if (isset($_POST['daily-report']) && isset($_GET['week'])) {
    $day_id = $_POST['day_id'];
    $message = $_POST['report-text'];
    if (!empty($day_id)) {
        $day = new post($day_id);
        $meta["daily-report"] = $message;
        $day->insert_meta($meta);
    }
}

$weeks_q = "SELECT `post_id` FROM `post` WHERE `post_type` = 'weekly_plan' AND `author` = $user_id";
$weeks = base::FetchArray($weeks_q);

if (isset($_GET['week'])) {
    $week_id = $_GET['week'];
    $week = new post($week_id);
    $week_message = $week->get_meta("week_message");
    $week_score = $week->get_meta("week_score");
    $week_title = $week->get_meta("week_title");
    $week_pdf = $week->get_meta("week_pdf");
    $chat_id = base::FetchAssoc("SELECT `post_id` FROM `post` WHERE `post_parent` = $week_id AND `post_type` = 'chat'")['post_id'];
    $chat = new post($chat_id);
    $chat_title = $chat->get_title();
    $total_duration = 0;
    $days_q = "SELECT `post_id` FROM `post` WHERE `post_parent` = $week_id  AND `post_type` = 'daily-plan'";
    $days = base::FetchArray($days_q);
    foreach ($days as $day) {
        $duration_q = "SELECT SUM(CAST(`post_name` AS UNSIGNED)) as `duration` FROM `post` 
        INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` 
        WHERE `post_type` = 'plan' AND `post_parent` = {$day['post_id']} 
        AND `key` = 'score' AND `value` BETWEEN 1 AND 2
        GROUP BY `post`.`post_id`";
        $durations = base::fetcharray($duration_q);
        if (is_countable($durations) && count($durations) > 0) {
            foreach ($durations as $duration) {
                $total_duration += intval($duration['duration']);
            }
        }
    }
}

if ($_GET['plan']) {
    $plan_id = $_GET['plan'];
    $plan = new post($plan_id);
    $parent = $plan->get_parent();
    $days = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_parent` = $parent AND `post_type` = 'daily-plan'");
    $day_number = 1;
    if (is_countable($days)) {
        for ($i = 1; $i < count($days); $i++) {
            if ($days[$i - 1]['post_id'] == $plan_id) {
                $day_number = $i;
                break;
            }
        }
    }
    $text = $plan->get_content();
    $start = $plan->get_title();
    $end = $plan->get_excerpt();
    $class_link1 = $plan->get_meta('class_link1');
    $class_link_title1 = $plan->get_meta('class_link_title1');
    $class_link2 = $plan->get_meta('class_link2');
    $class_link_title2 = $plan->get_meta('class_link_title2');
    $class_link3 = $plan->get_meta('class_link3');
    $class_link_title3 = $plan->get_meta('class_link_title3');
    if ($role == 'student')
        $text = $plan->get_meta('comment');
    $score = $plan->get_meta('score');
}

if (isset($_GET['participated'])) {
    $participation = $_GET['participated'];
    if ($participation == true || $participation == false) {
        $user->insert_user_meta(['plans_league' => $participation]);
        $uri = explode('&', $_SERVER['REQUEST_URI']);
        array_pop($uri);
        $uri = implode('&', $uri);
        base::redirect($uri);
    }
}

$week_score_colors = ['#39da8a', '#00cfdd', '#fdac41', '#ff5b5c'];

if ($_SESSION['interfere_error']) {
    echo "<script>Swal.fire('خطا!','{$_SESSION['interfere_error']}','error');</script>";
    unset($_SESSION['interfere_error']);
}

?>
<link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<style>
    .dt-button-spacer {
        margin: 0 10px;
    }

    #weeks-header>* {
        flex: 1 1 0;
    }
</style>
<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-body ">
                <div class="row">
                    <div class="col-12 col-lg-3 mb-3 mb-lg-0 text-center">
                        <?php if (!empty($avatar)): ?>
                            <img class="img-fluid rounded mb-2 mb-lg-0" src="<?php echo $avatar; ?>" height="110"
                                width="110">
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-lg-9">
                        <ul class="list-unstyled mb-0 row">
                            <li class="mb-3 col-12 col-md-6">
                                <span class="fw-bold me-2">نام:</span>
                                <span><?php echo $username; ?></span>
                            </li>
                            <li class="mb-3 col-12 col-md-6">
                                <span class="fw-bold me-2">شماره تلفن:</span>
                                <span class="d-inline-block" dir="ltr"><?php echo $phone_number ?></span>
                            </li>
                            <li class="mb-3 col-12 col-md-6">
                                <span class="fw-bold me-2">نام مدرسه:</span>
                                <span><?php echo $school ?></span>
                            </li>
                            <li class="mb-3 col-12 col-md-6">
                                <span class="fw-bold me-2">مشاور تحصیلی:</span>
                                <span class="d-inline-block" dir="ltr"><?php echo $adviser ?></span>
                            </li>
                            <li class="mb-3 col-12 col-md-6">
                                <span class="fw-bold me-2">پایه تحصیلی:</span>
                                <span><?php echo $grade ?></span>
                            </li>
                            <li class="col-12 col-md-6">
                                <span class="fw-bold me-2">رشته تحصیلی:</span>
                                <span class="d-inline-block" dir="ltr"><?php echo $fos ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" id="weeks-header">
                <h5 class="mb-0"><?php if ($week_title)
                    echo $week_title;
                else
                    echo "هفته ها"; ?></h5>
                <select id="select-week" class="select2" name="week">
                    <?php if (is_countable($weeks)) {
                        echo "<option value='0'>انتخاب</option>";
                        for ($i = 1; $i < count($weeks) + 1; $i++) {
                            $weeks_id = $weeks[$i - 1]['post_id'];
                            $weeks_score = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = {$weeks_id} AND `key` = 'week_score';")['value'];
                            $weeks_title = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = {$weeks_id} AND `key` = 'week_title';")['value']; ?>
                            <option value="<?php echo $weeks_id; ?>" <?php if ($_GET['week'] == $weeks_id)
                                   echo " selected";
                               elseif ($i === 0)
                                   echo " selected";
                               if (!empty($weeks_score))
                                   echo " data-background={$week_score_colors[intval($weeks_score) - 1]}"; ?>>
                                <?php if (!empty($weeks_title))
                                    echo $weeks_title;
                                else
                                    echo "هفته $i"; ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>

            <div class="card-body p-3 pt-0">
                <form action="" method="post" class="row">
                    <?php if ($role != 'student') { ?>
                        <div class="col-12 mb-3 mb-md-0">
                            <a class="btn btn-sm btn-success mr-auto mb-2"
                                href="filemanager/dialog.php?field_id=session<?php echo $sessions_count; ?>_video<?php echo $i; ?>"
                                data-fancybox data-type="iframe" data-preload="false">انتخاب فایل</a>
                            <input type="text" name="week_pdf"
                                class="form-control mb-3" value="<?php echo $week_pdf; ?>" placeholder="فایل خود را انتخاب کنید">
                        </div>
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <input type="text" class="form-control" placeholder="عنوان هفته" name="week_title">
                        </div>
                        <div class="col-6 col-lg-4 text-center">
                            <button type="submit" name="add_week" <?php if (is_countable($weeks))
                                echo "value='" . count($weeks) . "'"; ?> class="btn btn-success p-2">ایجاد هفته</button>
                        </div>
                    <?php } ?>
                    <?php if ($_GET['week']) { ?>
                        <div class="col-6 col-lg-4 text-center<?php if ($role == 'student')
                            echo ' d-none'; ?>">
                            <button type="button" class="btn btn-primary p-2" id="add-category" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasAddTaxonomy">ایجاد برنامه</button>
                        </div>
                    <?php } ?>
                </form>
                <p class="mt-3 mb-3">مجموع تایم مطالعه : <?php echo $total_duration; ?> دقیقه</p>
                <?php if ($participated === 'true') { ?>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] . "&participated=false"; ?>" class="btn btn-secondary">انصراف
                        از لیگ</a>
                <?php } else { ?>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] . "&participated=true"; ?>" class="btn btn-primary">ورود به
                        لیگ</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <table class="datatables-posts table table-bordered study-plans">
                    <thead>
                        <tr>
                            <th width="200px">روز هفته</th>
                            <th>برنامه</th>
                        </tr>
                    </thead>
                </table>
                <?php if ($_GET['week'] && $role != 'student') { ?>
                    <form action="" method="POST" class="row">
                        <div class="col-4 col-lg-2">
                            <label class="form-label">امتیاز عملکرد</label>
                            <select required name="score" class="select2">
                                <option>انتخاب</option>
                                <option value="1" <?php if ($week_score == 1)
                                    echo "selected"; ?>>خوب</option>
                                <option value="2" <?php if ($week_score == 2)
                                    echo "selected"; ?>>متوسط</option>
                                <option value="3" <?php if ($week_score == 3)
                                    echo "selected"; ?>>ضعیف</option>
                                <option value="4" <?php if ($week_score == 4)
                                    echo "selected"; ?>>انجام نشده</option>
                            </select>
                        </div>
                        <div class="col-5 col-lg-8">
                            <label class="form-label">متن روندنما</label>
                            <input type="text" name="message" class="form-control" value="<?php echo $week_message; ?>">
                        </div>
                        <div class="col-3 col-lg-2 text-end">
                            <button type="submit" name="week-report" class="btn btn-success p-2 mt-4">ثبت</button>
                            <a href="?page=plans/my-weeks.php&uid=<?php echo $user_id; ?>"
                                class="btn btn-primary p-2 mt-4 mr-auto">مشاهده عملکردها</a>
                        </div>
                    </form>
                <?php } elseif ($_GET['uid'] && $role == 'student') { ?>
                    <div class="col-3 col-lg-2 text-end mt-4 mr-auto">
                        <a href="?page=plans/my-weeks.php&uid=<?php echo $user_id; ?>" class="btn btn-primary">مشاهده
                            عملکردها</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasAddTaxonomy"
    aria-labelledby="offcanvasAddTaxonomyLabel">
    <div class="offcanvas-header border-bottom">
        <h6 id="offcanvasAddTaxonomyLabel" class="offcanvas-title"><?php if ($_GET['plan'])
            echo "ویرایش برنامه";
        elseif ($role == 'student')
            echo "افزودن یادداشت";
        else
            echo "افزودن برنامه"; ?></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="p-4 mx-0 flex-grow-0">
        <form action="" method="post" class="add-new-user pt-0" id="addNewTaxonomy" data-taxonomy='class_cat'>
            <?php if ($role != 'student' && !$_GET['plan']) { ?>
                <div class="mb-3">
                    <label class="form-label" for="day">روز هفته</label>
                    <select required name="day" id="day" class="select2">
                        <option>انتخاب</option>
                        <option value="1">شنبه</option>
                        <option value="2">یک شنبه</option>
                        <option value="3">دو شنبه</option>
                        <option value="4">سه شنبه</option>
                        <option value="5">چهار شنبه</option>
                        <option value="6">پنج شنبه</option>
                        <option value="7">جمعه</option>
                    </select>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label" for="plan">توضیحات</label>
                <textarea rows="5" class="form-control" name="plan_text" id="plan"><?php echo $text; ?></textarea>
            </div>
            <?php if ($role != 'student') { ?>
                <div class="mb-3">
                    <label class="form-label">شروع برنامه</label>
                    <input type="text" required name="min_date" class="form-control" value="<?php echo $start; ?>"
                        placeholder="HH:MM" id="min_date">
                </div>
                <div class="mb-3">
                    <label class="form-label">پایان برنامه</label>
                    <input type="text" required name="max_date" class="form-control" value="<?php echo $end; ?>"
                        placeholder="HH:MM" id="max_date">
                </div>
                <div class="mb-3">
                    <label class="form-label">عنوان لینک اول</label>
                    <input type="text" name="class_link_title1" class="form-control"
                        value="<?php echo $class_link_title1; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">لینک اول</label>
                    <input type="text" name="class_link1" class="form-control" value="<?php echo $class_link1; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">عنوان لینک دوم</label>
                    <input type="text" name="class_link_title2" class="form-control"
                        value="<?php echo $class_link_title2; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">لینک دوم</label>
                    <input type="text" name="class_link2" class="form-control" value="<?php echo $class_link2; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">عنوان لینک سوم</label>
                    <input type="text" name="class_link_title3" class="form-control"
                        value="<?php echo $class_link_title3; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">لینک سوم</label>
                    <input type="text" name="class_link3" class="form-control" value="<?php echo $class_link3; ?>">
                </div>
            <?php } ?>
            <?php if ($_GET['plan']) { ?>
                <div class="mb-3">
                    <label class="form-label">امتیاز عملکرد</label>
                    <select required name="score" id="day" class="select2">
                        <option>انتخاب</option>
                        <option value="1" <?php if ($score == 1)
                            echo "selected"; ?>>خوب</option>
                        <option value="2" <?php if ($score == 2)
                            echo "selected"; ?>>متوسط</option>
                        <option value="3" <?php if ($score == 3)
                            echo "selected"; ?>>ضعیف</option>
                        <option value="4" <?php if ($score == 4)
                            echo "selected"; ?>>انجام نشده</option>
                    </select>
                </div>
            <?php } ?>
            <button type="submit" name="submit-plan" id="taxonomy-submit"
                class="btn btn-primary me-sm-3 me-1">ثبت</button>
            <button type="submit" name="cansel-plan" class="btn btn-label-secondary"
                data-bs-dismiss="offcanvas">انصراف</button>
            <?php if ($_GET['plan'] && $role != 'student') { ?>
                <button type="submit" name="delete-plan" id="taxonomy-submit"
                    class="btn btn-danger me-sm-3 me-1">حذف</button>
            <?php } ?>
        </form>
    </div>
</div>
<?php if (!empty($chat_id)) { ?>
    <div class="chat-box ticket" id="chat">
        <div class="chat-container">
            <div class="chat-header">
                <span class="fa fa-close close-chat"></span>
                <p id="chat-title"></p>
                <?php if ($role == 'admin' || $role == 'school' || $uid == $author): ?>
                    <a data-fancybox data-type='iframe' data-preload='false'><i class="fa fa-users ml-2"></i><span
                            id="members"></span></a>
                <?php else: ?>
                    <p class="m-0"><i class="fa fa-users ml-2"></i><span id="members"></span></p>
                <?php endif; ?>
            </div>
            <p class="pinned-message d-none" id="pinned-message"></p>
            <div class="chat-content">

            </div>
            <div class="chat-footer">
                <input type="text" placeholder="پیام خود را بنویسید ..." id="message" name="chat">
                <label class="image-icon"><input type="file" accept="image/*"
                        onchange="loadFile(this,event);$('#chat .photo-preveiw').addClass('open');" id="chat-pic"
                        name="pic"></label>
                <label class="file-icon"><input type="file" accept="*"
                        onchange="getFileExt(event,'ext');$('#chat .photo-preveiw').addClass('open file');" id="chat-file"
                        name="pic"></label>
                <button name="submit-chat" id="submit-chat">ارسال</button>
            </div>
        </div>
        <div class="photo-preveiw">
            <div class="container">
                <div class="photo-wrapper">
                    <i class="close" onclick="$('.photo-preveiw').removeClass('open');"></i>
                    <span id='ext'></span>
                    <img src="" id="pic">
                    <input type="text" placeholder="پیام خود را بنویسید ..." id="title">
                    <button id="submit-pic">ارسال</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-buttons/datatables-buttons.js"></script>
<script src="assets/vendor/libs/datatables-buttons/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="assets/vendor/libs/datatables-buttons/buttons.colVis.js"></script>
<script src="assets/vendor/libs/datatables-buttons/buttons.print.js"></script>
<script src="assets/vendor/libs/datatables-buttons/vfs_fonts.js"></script>
<script src="assets/vendor/libs/datatables-buttons/pdfmakeunicode.js"></script>
<script src="assets/vendor/libs/datatables-buttons/buttons.html5.js"></script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script>
    var ids = [];
    $('.chat-box').click(function (e) {
        if (!$(e.target).parents(".chat-box").length || $(e.target).hasClass("close-chat")) {
            $(this).removeClass('open');
        }
    });
    $('.photo-preveiw').click(function (e) {
        if (!$(e.target).parents(".photo-wrapper").length || $(e.target).hasClass("close-preveiw")) {
            $(this).removeClass('open');
            $(this).removeClass('file');
            $('#chat-pic').val('');
            $('#chat-file').val('');
            $('.photo-wrapper img').attr('src', '');
            $('.photo-wrapper span').html('');
            $('.photo-wrapper input').val('');

        }
    });

    function chat_start(post_id) {
        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + post_id, function (err, data) {
            ids = [];
            var title = data['title'];
            var members = data['members'];
            $('#chat .chat-content').html('');
            $('#submit-pic').attr('onclick', 'message(' + post_id + ')');
            $('#submit-chat').attr('onclick', 'message(' + post_id + ')');
            $('#chat-title').text(title);
            var pinned_message = data['pinned_message'];
            $('#pinned-message').addClass('d-none');
            if (pinned_message && pinned_message.length > 0) {
                $('#pinned-message').text(pinned_message);
                $('#pinned-message').removeClass('d-none');
            }
            $('#members').text(members);
            var messages = data['tickets'];
            var message_count = 0;
            if (messages)
                message_count = messages.length;
            <?php if ($role == 'admin' || $role == 'school' || $uid == $author): ?>
                $('#members').parent().attr('href', '?page=counsel/submit-ticket.php&chat_id=' + post_id);
            <?php endif; ?>
            for (var i = 0; i < message_count; i++) {
                if (!ids.includes(messages[i]["post_id"])) {
                    if (messages[i]["mime_type"] == 'text') {
                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                    } else {
                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                    }
                    ids.push(messages[i]["post_id"]);
                }
            }
            $('#chat').addClass('open');
            $('#chat .chat-content').delay(1000).animate({
                scrollTop: $('#chat .chat-content').prop("scrollHeight")
            }, 1000);
            setInterval(get_message, 1000, post_id);
        });
    };

    function get_message(id) {
        if ($('.chat-box').hasClass('open')) {
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                var messages = data['tickets'];
                var message_count = 0;
                if (messages)
                    message_count = messages.length;
                for (var i = 0; i < message_count; i++) {
                    if (!ids.includes(messages[i]["post_id"])) {
                        if (messages[i]["mime_type"] == 'text') {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                        } else {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                        }
                        ids.push(messages[i]["post_id"]);
                        $('#chat .chat-content').animate({
                            scrollTop: $('#chat .chat-content').prop("scrollHeight")
                        }, 1000);
                    }
                }

            });
        }
    }

    function message(id) {
        var text = $('#chat #message').val();
        var image = $('#chat #chat-pic').val();
        var file = $('#chat #chat-file').val();
        $('#chat button').attr('disabled', true);
        $('#chat .photo-wrapper button').attr('disabled', true);
        if (image != '' || file != '') {
            $('.loading').addClass('open');
            var title = $('#chat #title').val();
            if (image != '')
                var file_data = $('#chat #chat-pic').prop('files')[0];
            else
                var file_data = $('#chat #chat-file').prop('files')[0];

            var form_data = new FormData();
            form_data.append('file', file_data);
            $.ajax({
                url: "upload.php",
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    error = ['file too large', 'upload failed!'];
                    image = data;
                    if (!error.includes(image)) {
                        getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&image=' + image + '&title=' + title, function (err, data) { });
                        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                            var messages = data['tickets'];
                            var message_count = 0;
                            if (messages)
                                message_count = messages.length;
                            for (var i = 0; i < message_count; i++) {
                                if (!ids.includes(messages[i]["post_id"])) {
                                    if (messages[i]["mime_type"] == 'text') {
                                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                                    } else {
                                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                                    }
                                    ids.push(messages[i]["post_id"]);
                                }
                            }
                        });

                        $('#chat .photo-preveiw').removeClass('open');
                        $('#chat .photo-preveiw').removeClass('file');
                        $('#chat .chat-footer input').val('');
                        $('#chat .photo-wrapper input').val('');
                        $('#chat .photo-wrapper span').html('');
                        $('#chat .photo-wrapper img').attr('src', '');
                        $('#chat.chat-content').animate({
                            scrollTop: $('#chat .chat-content').prop("scrollHeight")
                        }, 1000);
                        $('.loading').removeClass('open');
                    } else {
                        Swal.fire(
                            'خطا',
                            image,
                            'error'
                        );
                        $('.loading').removeClass('open');
                    }
                }
            });
            setTimeout(() => {
                $('#chat button').attr('disabled', false);
                $('#chat .photo-wrapper button').attr('disabled', false);
            }, 500);
        } else {
            getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&text=' + text, function (err, data) { });
            $('#chat .chat-footer input').val('');
            $('#chat .photo-wrapper input').val('');
            $('#chat .photo-wrapper img').attr('src', '');
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                var messages = data['tickets'];
                var message_count = 0;
                if (messages)
                    message_count = messages.length;
                for (var i = 0; i < message_count; i++) {
                    if (!ids.includes(messages[i]["post_id"])) {
                        if (messages[i]["mime_type"] == 'text') {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                        } else {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                        }
                        ids.push(messages[i]["post_id"]);
                    }
                }
            });
            $('#chat .chat-content').animate({
                scrollTop: $('#chat .chat-content').prop("scrollHeight")
            }, 1000);
        }
        $('#chat button').attr('disabled', false);
        $('#chat .photo-wrapper button').attr('disabled', false);
    };

    function dlpdf(doc) {

        doc.content[0].text = "";
        doc.content[0].image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAABGCAYAAACgyeb0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABAhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjNiOGE4OWFjLTc3NmItNzI0Mi1hNWRhLTY2YTFlYTRjZDNkYyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGQTIwMDBDOTc5MDIxMUVCODg2RURBRTEwN0Y0RjUwNiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGQTIwMDBDODc5MDIxMUVCODg2RURBRTEwN0Y0RjUwNiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjIuMCAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyZmUyZDA0OC1lMTY5LTY3NDQtOTA3Mi04MDg1ODU3NjVmNDkiIHN0UmVmOmRvY3VtZW50SUQ9ImFkb2Jl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";

        doc.pageOrientation = "landscape";
        doc.content[1].table.widths = ['auto', '*'];
        doc.content[1].width = '100%';
        doc.styles.tableHeader.margin = 5;
        pdfMake.fonts = {
            Roboto: {
                normal: 'https://khoobyad.ir/panel/assets/vendor/fonts/farsi-fonts-en-num/iran-sans-400.ttf',
                bold: 'https://khoobyad.ir/panel/assets/vendor/fonts/farsi-fonts-en-num/iran-sans-700.ttf',
                italics: 'https://khoobyad.ir/panel/assets/vendor/fonts/farsi-fonts-en-num/iran-sans-400.ttf',
                bolditalics: 'https://khoobyad.ir/panel/assets/vendor/fonts/farsi-fonts-en-num/iran-sans-700.ttf'
            }
        };
    }
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
    <?php if ($role != 'student') { ?>
        const min_date = document.querySelector('#min_date');
        const max_date = document.querySelector('#max_date');
        if (typeof min_date != undefined) {
            min_date.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                minuteIncrement: 1,
                time_24hr: true
            });
            if (typeof max_date != undefined) {
                $('#min_date').on('change', function () {
                    var min_time = $(this).val();
                    max_date.flatpickr().clear();
                    max_date.flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        minuteIncrement: 1,
                        time_24hr: true,
                        minTime: min_time,
                    });
                })
            }
        }
        if (typeof max_date != undefined) {
            max_date.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                minuteIncrement: 1,
                time_24hr: true
            });
        }
    <?php } ?>
    $(window).resize(function () {
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '.5rem .5rem'
            });
        }
    });
    $(window).ready(function () {
        <?php if (isset($_GET['plan']))
            echo "$('#add-category').trigger('click');"; ?>
        $('#add-category').click(function () {
            $('#addNewTaxonomy input').val('');
            $('#addNewTaxonomy textarea').val('');
            $('#addNewTaxonomy select').prop('selected', true);
        })
        $('#select-week').change(function () {
            var week = $(this).val();
            if (parseInt(week) > 0)
                window.location.replace("/panel/index.php?page=plans/my-plans.php&uid=<?php echo $user_id; ?>&week=" + week);
        });
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '.5rem .5rem'
            });
        }
        // Variable declaration for table
        var dt_user_table = $('.datatables-posts');

        // Users datatable
        if (dt_user_table.length) {
            $('.datatables-posts').on('touchstart', function (e) { });
            var dt_user = dt_user_table.DataTable({
                processing: true,
                serverSide: true,
                select: false,
                searching: false,
                paging: false,
                scrollX: true,
                ordering: false,
                info: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: 'دانلود pdf',
                        action: function (e, dt, node, config) {
                            window.location = '<?php echo $week_pdf; ?>';
                        }
                    },
                    <?php if (!empty($chat_id)) { ?> 'spacer',
                        {
                            text: 'مشاهده چت',
                            class: 'mr-3',
                            action: function (e, dt, node, config) {
                                chat_start(<?php echo $chat_id; ?>);
                            }
                        }
                    <?php } ?>
                    // {
                    //     extend: 'print',
                    //     charset: 'utf-8',
                    //     bom: true
                    // }
                ],
                ajax: {
                    url: 'API/v1/GetStudydays.php?wid=<?php echo $_GET['week']; ?>'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'post_title'
                    },
                    {
                        data: 'plan'
                    },
                ]
            });
        }
    })
</script>