<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/user.php");

$id = $_GET['wid'];

$user = new User($_SESSION['uid']);
$role = $user->get_user_meta('role');
$params = $totalRecords = $days = array();
$params = $_REQUEST;
$query = "SELECT `author`,`post_id`,`post_title` FROM `post`
WHERE `post_parent` = $id
AND `post_type` = 'daily-plan'
    GROUP BY `post`.`post_id`
    ORDER BY `post_id` ASC;";
$count_query = "SELECT `post_id` FROM `post`
WHERE `post_parent` = $id
    AND `post_type` = 'daily-plan'
    GROUP BY `post`.`post_id`";
$all_days = base::FetchArray($query);
if (is_countable($all_days))
  $count = count(base::FetchArray($count_query));
else
  $count = 0;
$days['draw'] = intval($params['draw']);
$days['recordsTotal'] = $count;
$days['recordsFiltered'] = $count;
$days['data'] = array();
$day_count = 0;
$plan_incomplete = false;
$incomplete_message = '';
foreach ($all_days as $day) :
  if ($day_count == 7)
    $day_count = 0;
  if ($plan_incomplete)
    break;
  if ($day['post_id'] > 0) :
    $day_id = $day['post_id'];
    $user_id = $day['author'];
    unset($day['post_id']);
    unset($day['author']);
    $day['plan'] = "";
    $day_name = "";
    switch ($day_count) {
      case 0:
        $day_name = "شنبه";
        break;
      case 1:
        $day_name = "یکشنبه";
        break;
      case 2:
        $day_name = "دوشنبه";
        break;
      case 3:
        $day_name = "سه شنبه";
        break;
      case 4:
        $day_name = "چهار شنبه";
        break;
      case 5:
        $day_name = "پنج شنبه";
        break;
      case 6:
        $day_name = "جمعه";
        break;
    }
    $day_duration = 0;
    $duration_q = "SELECT SUM(CAST(`post_name` AS UNSIGNED)) as `duration` FROM `post` 
    INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` 
    WHERE `post_type` = 'plan' AND `post_parent` = {$day_id} 
    AND `key` = 'score' AND `value` != 4 
    GROUP BY `post`.`post_id`";
    $durations = base::fetcharray($duration_q);
    if (is_countable($durations) && count($durations) > 0) {
      foreach ($durations as $duration) {
        $day_duration += intval($duration['duration']);
      }
    }
    $day['post_title'] = "<span onclick='getDailyReport($day_id)' data-bs-toggle='offcanvas' data-bs-target='#dailyReport'>{$day_name} - {$day_duration} دقیقه</span>";
    $plans = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_type` = 'plan' AND `post_parent` = $day_id ORDER BY `post_title` ASC");
    foreach ($plans as $plan) {
      $plan_id = $plan['post_id'];
      if ($plan_id) {
        $class_link_text = '';
        unset($plan['post_id']);
        $plan = new post($plan_id);
        $text = $plan->get_content();
        $start = $plan->get_title();
        $end = $plan->get_excerpt();
        $time1 = strtotime($start);
        $time2 = strtotime($end);
        $difference = round(abs($time2 - $time1) / 3600, 2);
        $comment = $plan->get_meta('comment');
        for ($i = 1; $i < 4; $i++) {
          $class_link = $plan->get_meta("class_link$i");
          $class_link_title = $plan->get_meta("class_link_title$i");
          if (!empty($class_link)) {
            $class_link_text .= "<a target='_blank' class='btn btn-primary btn-xs mx-1' href='$class_link'>$class_link_title</a>";
          }
        }

        $score = $plan->get_meta('score');
        if (!empty($comment)) {
          $comment = "<div class='plan-comment'>$comment</div>";
        }
        if (!empty($score)) {
          if ($score === '1')
            $score = '#39da8a';
          elseif ($score === '2')
            $score = "#00cfdd";
          elseif ($score === '3')
            $score = "#fdac41";
          elseif ($score === '4')
            $score = "#ff5b5c";
          else {
            $score = "#fff";
            if ($role == "student") {
              $plan_incomplete = true;
              $incomplete_message = 'تا رنگ دهی کامل برنامه روز جاری امکان مشاهده روز بعدی را نخواهید داشت';
            }
          }
        } else {
          $score = "#fff";
          if ($role == "student") {
            $plan_incomplete = true;
            $incomplete_message = 'تا رنگ دهی کامل برنامه روز جاری امکان مشاهده روز بعدی را نخواهید داشت';
          }
        }
        $info = "<div class='plan-box' style='min-width:150px;width:150px;background:$score;'><p onclick='window.location.replace(\"/panel/index.php?page=plans/my-plans.php&uid=$user_id&week=$id&plan=$plan_id\")' style='cursor: pointer;'><strong> $start - $end </strong><p> $text </p></p> $class_link_text $comment </div>";
        $day['plan'] .= $info;
      }
    }
    if($incomplete_message != "")
    $day['plan'] .= " <br><p style='background-color: red;color: white;width: 150px;
  text-align: center;align-content: center;padding: 10px;'>" . $incomplete_message . "</p>";
    array_push($days['data'], $day);
    $day_count++;
  endif;
endforeach;

echo json_encode($days, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
