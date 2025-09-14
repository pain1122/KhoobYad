<?php
session_start();
$uid = $_SESSION['uid'];
if (!empty($_GET['uid']))
  $uid = $_GET['uid'];
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/plan.php");
include_once(base_dir . "/includes/classes/user.php");
$id = $_GET['id'];
$session_id = $_GET['session_id'];
$day = base::fetchAssoc("SELECT `id` FROM `plans` WHERE `id` = (SELECT `parent` FROM plans WHERE `id` = $id AND user_id = $uid) AND user_id = $uid;")['id'];
$week = base::fetchAssoc("SELECT `id` FROM `plans` WHERE `id` = (SELECT `parent` FROM plans WHERE `id` = $day AND user_id = $uid) AND user_id = $uid;")['id'];
$user_weeks = base::fetchArray("SELECT `id` FROM `plans` WHERE `parent` = 0 AND user_id = $uid;");
$days_of_week = base::fetchArray("SELECT `id` FROM `plans` WHERE `parent` = $week AND user_id = $uid;");
$prev_day_complete = true;
$prev_day = $prev_week = 0;
foreach ($days_of_week as $days) {
  if ($days['id'] == $day)
    break;
  else
    $prev_day = $days['id'];
}
foreach ($user_weeks as $weeks) {
  if ($weeks['id'] == $week)
    break;
  else
    $prev_week = $weeks['id'];
}
if ($prev_day > 0) {
  $prev_day_sessions = plan::get_plans(['plan_type' => 'plan', 'user_id' => $uid, 'parent' => $prev_day]);
  foreach ($prev_day_sessions as $prev_session) {
    if ($prev_session['status'] == 0) {
      $prev_day_complete = false;
      break;
    }
  }
} else if ($prev_week > 0) {
  $prev_week_days = plan::get_plans(['plan_type' => 'day', 'user_id' => $uid, 'parent' => $prev_week]);
  foreach ($prev_week_days as $prev_week_day) {
    $prev_week_day_id = $prev_week_day['id'];
    $prev_day_sessions = plan::get_plans(['plan_type' => 'plan', 'user_id' => $uid, 'parent' => $prev_week_day_id]);
    foreach ($prev_day_sessions as $prev_session) {
      if ($prev_session['status'] === 0) {
        
        $prev_day_complete = false;
        break;
      }
    }
  }
}
$data = array();
$verify = base::fetchAssoc("SELECT * FROM `plans` WHERE `id` = $id AND `user_id` = $uid");
if ($prev_day_complete === true) {
  if ($session_id > 0 && $verify['id'] > 0) {
    $plan_id = $verify['plan_id'];
    $user = new user($uid);
    $prefered_duration = intval($user->get_user_meta('prefered_duration'));
    if (!$prefered_duration)
      $prefered_duration = 60;
    $session_status = base::fetchAssoc("SELECT `session_id`,`status` FROM `plans`
    WHERE `user_id` = $uid AND `plan_id` = $plan_id AND `id` < $id ORDER BY `id` DESC")['status'];
    $session_duration_q = "SELECT `value` FROM `post_meta` WHERE `post_id` = $session_id AND `key` = 'time'";
    $session_duration = intval(base::fetchAssoc($session_duration_q)['value']);
    if (!$session_duration) {
      $session_duration = 0;
      $needed_sessions = 1;
    } else {
      $needed_sessions = ceil($session_duration / $prefered_duration);
    }
    $sessions_passed_q = "SELECT COUNT(`id`) as `passed` FROM `plans` WHERE `user_id` = $uid AND `session_id` = $session_id AND `id` < $id AND `status` BETWEEN 1 AND 3 ORDER BY `id` DESC";
    $successful_sessions = base::fetchAssoc($sessions_passed_q)['passed'];
    if ($session_status < 3) {
      $session = new blog($session_id);
      $session->set_post_type('defined_plan');
      $data['title'] = $session->get_title() . ' (' . $verify['content'] . ')';
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    } else if ($session_status > 2) {
      $session = new blog($session_id);
      $session->set_post_type('defined_plan');
      $data['title'] = $session->get_title();
      $data['title'] = "مرور جهت جبران جلسه {$data['title']}";
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      $data['session_id'] = $session_id;
      base::RunQuery("UPDATE `plans` SET `session_id`='$session_id' WHERE `id` = $id");
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    } else if ($needed_sessions > $successful_sessions && $session_status > 0) {
      $session = new blog($session_id);
      $session->set_post_type('defined_plan');
      $data['title'] = $session->get_title();
      $data['title'] = "ادامه مبحث جلسه {$data['title']}";
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      $data['session_id'] = $session_id;
      base::RunQuery("UPDATE `plans` SET `session_id`='$session_id' WHERE `id` = $id");
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    }
  } else if ($id && $verify['id'] > 0) {
    $plan_id = $verify['plan_id'];
    $user = new user($uid);
    $prefered_duration = intval($user->get_user_meta('prefered_duration'));
    if (!$prefered_duration)
      $prefered_duration = 60;
    $new_session_q = "SELECT `session_id`,`status` FROM `plans`
    WHERE `user_id` = $uid AND `plan_id` = $plan_id AND `id` < $id ORDER BY `id` DESC";
    $new_session = base::fetchAssoc($new_session_q);
    $session_id = $new_session['session_id'];
    $session_status = $new_session['status'];
    $session_duration_q = "SELECT `value` FROM `post_meta` WHERE `post_id` = $session_id AND `key` = 'time'";
    $session_duration = intval(base::fetchAssoc($session_duration_q)['value']);

    $needed_sessions = ceil($session_duration / $prefered_duration);
    $sessions_passed_q = "SELECT COUNT(`id`) as `passed` FROM `plans` WHERE `user_id` = $uid AND `session_id` = $session_id AND `id` < $id AND `status` BETWEEN 1 AND 3 ORDER BY `id` DESC";
    $successful_sessions = base::fetchAssoc($sessions_passed_q)['passed'];
    if ($session_status >= 3) {
      $session = new blog($session_id);
      $session->set_post_type('defined_plan');
      $duration = $session_duration;
      $data['title'] = $session->get_title();
      $data['title'] = "مرور جهت جبران جلسه {$data['title']}";
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      $data['session_id'] = $session_id;
      $data['phase'] = 1;
      base::RunQuery("UPDATE `plans` SET `session_id`='$session_id', `duration` = $duration WHERE `id` = $id");
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    } else if ($needed_sessions > $successful_sessions && $session_status > 0) {
      $session = new blog($session_id);
      $session->set_post_type('defined_plan');
      $completed_time = $successful_sessions * $session_duration;
      $duration = $session_duration - $duration;
      $data['title'] = $session->get_title();
      $data['title'] = "ادامه مبحث جلسه {$data['title']}";
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      $data['session_id'] = $session_id;
      $data['phase'] = 2;
      base::RunQuery("UPDATE `plans` SET `session_id` = '$session_id', `duration` = $duration WHERE `id` = $id");
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    } elseif ($session_status > 0 && $session_status < 3) {
      $next_session_q = "SELECT `post_id` FROM `post` WHERE `post_parent` = $plan_id AND `post_id` > $session_id AND `post_type` = 'defined_plan'";
      $next_session = base::FetchAssoc($next_session_q);
      $next_session_id = $next_session['post_id'];
      $session = new blog($next_session_id);
      $session->set_post_type('defined_plan');
      $duration = $session->get_meta('time');
      $data['title'] = $session->get_title();
      $data['desc'] = $session->get_meta('desc');
      $data['video'] = $session->get_meta('video');
      $data['note'] = $session->get_meta('note');
      $data['video_title'] = $session->get_meta('video_title');
      $data['note_title'] = $session->get_meta('note_title');
      if (empty($data['video']))
        $data['video'] = '[]';
      if (empty($data['note']))
        $data['note'] = '[]';
      if (empty($data['video_title']))
        $data['video_title'] = '[]';
      if (empty($data['note_title']))
        $data['note_title'] = '[]';
      $data['session_id'] = $next_session_id;
      $data['phase'] = 3;
      base::RunQuery("UPDATE `plans` SET `session_id` = '$next_session_id', `duration` = $duration WHERE `id` = $id");
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    } else {
      $data['title'] = '';
      $data['desc'] = '';
      $data['video'] = '';
      $data['note'] = '';
      $data['error'] = 'برای مشاهده برنامه جدید، برنامه قبلی را امتیاز دهی کنید.';
      echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      die();
    }
  }
} else {
  $data['title'] = '';
  $data['desc'] = '';
  $data['video'] = '';
  $data['note'] = '';
  $data['error'] = 'برای مشاهده برنامه های امروز، تمام برنامه های روز قبل را امتیاز دهی کنید.';
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
