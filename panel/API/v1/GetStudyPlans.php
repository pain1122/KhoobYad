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
  0 => 'post_id',
  1 => 'nicename',
  2 => 'login',
  3 => 'post_status'
);
$where = $parent = $show = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`users`.`login` Like '%" . $search . "%'
    OR `users`.`nicename` Like '%" . $search . "%'
    OR `users`.`display_name` Like '%" . $search . "%'
    OR (`key` = 'grade' AND `value` like '%" . $search . "%')
    OR (`key` = 'fos' AND `value` like '%" . $search . "%')
    OR (`key` = 'adviser' AND `value` like '%" . $search . "%')
    )";
}

if($current_user->get_user_meta('role') == 'adviser')
$where .= " AND `user_meta`.`key` = 'adviser' AND `user_meta`.`value` = {$current_user->get_id()}";
if(! empty($_GET['id'])){
  $id = $_GET['id'];
  $role = base::FetchAssoc("SELECT `value` FROM `user_meta` WHERE `user_id` = $id AND `key` = 'role'")['value'];
  if($role == 'student')
    $show = " AND `post`.`parent` = $id";
  else
    $show = " AND `post`.`author` = $id";
} 
if(! empty($_GET['parent'])){
  $parent = " AND `users`.`school` = {$_GET['parent']}";
  $show = "";
}
$query = "SELECT `users`.`user_id`,`post`.`post_id`,`post`.`post_status` FROM `users`
INNER JOIN `post` ON `users`.`user_id` = `post`.`author`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
WHERE `post_type` = 'daily-plan'

AND `post_status` = 'publish'
    $where
    $parent
    GROUP BY `post`.`author`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `users`.`user_id`,`post`.`post_id` FROM `users`
INNER JOIN `post` ON `users`.`user_id` = `post`.`author`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
where `post_type` = 'daily-plan' 
AND `post_status` = 'publish'
    $where
    $parent
    GROUP BY `post`.`author`";
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
    $post_id = $user['post_id'];
    $obj = new user($user_id);
    $name = $obj->get_nick_name();
    $phone_number = $obj->get_login();
    $grade = $obj->get_grade();
    $fos = $obj->get_fos();
    $adviser = $obj->get_adviser();
    $user['name'] = $name;
    $user['phone_number'] = $phone_number;
    $user['grade'] = $grade;
    $user['fos'] = $fos;
    $user['adviser'] = $adviser;
    $user['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=plans/all-plans.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($users['data'], $user);
  endif;
endforeach;

echo json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
