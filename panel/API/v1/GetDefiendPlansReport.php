<?php
session_start();

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
$current_user = new user($_SESSION['uid']);


$params = $columns = $totalRecords = $users = array();
$params = $_REQUEST;
$columns = array(
  0 => 'id',
  1 => 'nicename',
  2 => 'login',
  3 => 'grade',
  4 => 'fos',
  5 => 'op'

);
$where = $parent = $show = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`users`.`login` Like '%" . $search . "%'
    OR `users`.`nicename` Like '%" . $search . "%'
    OR `users`.`display_name` Like '%" . $search . "%'
    OR (`key` = 'grade' AND `value` like '%" . $search . "%')
    OR (`key` = 'fos' AND `value` like '%" . $search . "%')
    )";
}

if($current_user->get_user_meta('role') == 'adviser')
$where .= " AND `user_meta`.`key` = 'adviser' AND `user_meta`.`value` = {$current_user->get_id()}";
$query = "SELECT `users`.`user_id`,`plans`.`id` FROM `users`
INNER JOIN `plans` ON `users`.`user_id` = `plans`.`user_id`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
WHERE `type` = 'plan'
    $where
    AND `user_meta`.`key` = 'role' AND `user_meta`.`value` = 'student'
    GROUP BY `plans`.`user_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
    
$count_query = "SELECT `users`.`user_id`,`plans`.`id` FROM `users`
INNER JOIN `plans` ON `users`.`user_id` = `plans`.`user_id`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
where `type` = 'plan'
    $where
    AND `user_meta`.`key` = 'role' AND `user_meta`.`value` = 'student'
    GROUP BY `plans`.`user_id`";
$all_users = base::FetchArray($query);
if (is_countable($all_users))
  $count = count(base::FetchArray($count_query));
else
  $count = 0;
$users['draw'] = intval($params['draw']);
$users['recordsTotal'] = $count;
$users['recordsFiltered'] = $count;
$users['data'] = array();
foreach ($all_users as $user) :
  if ($user['user_id'] > 0) :
    $user_id = $user['user_id'];
    $obj = new user($user_id);
    $name = $obj->get_nick_name();
    $phone_number = $obj->get_login();
    $grade = $obj->get_grade();
    $fos = $obj->get_fos();
    $user['name'] = $name;
    $user['phone_number'] = $phone_number;
    $user['grade'] = $grade;
    $user['fos'] = $fos;
    $user['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=defined-plans/arrange-plan.php&uid=' . $user_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> افزودن برنامه</a>
    </div>
  </div>';
    array_push($users['data'], $user);
  endif;
endforeach;

echo json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
