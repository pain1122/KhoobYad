<?php
session_start();
$uid = $_SESSION['uid'];
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/user.php");
$data = array();
$data['data'] = array();
$lid = $_GET['lid'];
$sessions_count = $_GET['count'];
$query = "SELECT `post_id` FROM `post` WHERE `post_type` = 'session' AND `post_parent` = $lid LIMIT $sessions_count , 1";
$session_arr = base::FetchAssoc($query);
$session_id = $session_arr['post_id'];
if ($session_id) {
  $user = new user($uid);
  $classes = json_decode($user->get_user_meta('classes'), true);
  if (is_countable($classes) && preg_match('/"' . preg_quote($session_id, '/') . '"/i', json_encode($classes))) {
    print_r($classes);
  } else {
    if (empty($classes[$lid]))
      $classes[$lid] = [];
    // print_r($classes);
    array_push($classes[$lid], $session_id);
    $session = new post($session_id);
    $session_arr['id'] = $session->get_id();
    $session_arr['title'] = $session->get_title();
    $session_arr['link'] = $session->get_excerpt();
    // $session_arr['price'] = $session->get_meta('price');
    $session_arr['price'] = '120,000,000';
    $session_arr['date'] = $session->get_meta('date');
    $session_arr['videos'] = explode(',', $session->get_meta('videos'));
    $session_arr['files'] = explode(',', $session->get_meta('files'));
    array_push($data['data'], $session_arr);
    $classes = json_encode($classes, JSON_UNESCAPED_UNICODE);
    $user->insert_user_meta(['classes' => $classes]);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }
}
