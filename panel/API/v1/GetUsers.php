<?php
session_start();
use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/user.php");

$user = new user($_SESSION['uid']);
$uid = $user->get_id();
$role = $user->get_user_meta('role');

$low = $_GET['low'];
$params = $columns = $totalRecords = $users = array();
$params = $_REQUEST;
$columns = array(
  0 => 'user_id',
  1 => 'login',
  2 => 'nicename',
  3 => 'user_email',
  4 => 'user_registered'
);
$where = $type = $parent = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`users`.`login` Like '%" . $search . "%'
    OR `users`.`nicename` Like '%" . $search . "%'
    OR `users`.`display_name` Like '%" . $search . "%'
    OR `users`.`nicename` Like '%" . urlencode($search) . "%'
    OR `users`.`display_name` Like '%" . urlencode($search) . "%'
    OR `users`.`user_email` Like '%" . $search . "%')";
}
if (!empty($_GET['parent'])) {
  $parent = " AND `users`.`school` = {$_GET['parent']}";
}
if (!empty($_GET['type'])) {
  if ($_GET['type'] == 'partners') {
    $page_url = "all-employees";
    $type = " AND `user_meta`.`key` = 'role' AND `user_meta`.`value` IN ('teacher','adviser','support')";
  } else {
    $page_url = "all-students";
    if($role == 'admin')
      $type = " AND `user_meta`.`key` = 'role' AND `user_meta`.`value` = '{$_GET['type']}'";
    else{
      $asigned_users = base::FetchArray("SELECT `user_id` FROM `user_meta` WHERE `key` = 'adviser' AND `value` = $uid");
      if(is_countable($asigned_users))
        $asigned_users = implode(',', array_column($asigned_users, 'user_id'));
      else
        $asigned_users = 0;
      $type = " AND `user_meta`.`key` = 'role' AND `user_meta`.`value` = '{$_GET['type']}' AND `users`.`user_id` IN($asigned_users)";
    }
  }

}
$query = "SELECT `users`.`user_id` FROM `users`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
where true  
    $where
    $parent
    $type
    GROUP BY `users`.`user_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
    // echo $query;
$count_query = "SELECT `users`.`user_id`   FROM `users`
INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
where true 
    $where
    $parent
    $type
    GROUP BY `users`.`user_id`";
$all_users = base::FetchArray($query);
if (is_countable($all_users))
  $count = count(base::FetchArray($count_query));
else
  $count = 0;
$users['draw'] = intval($params['draw']);
$users['recordsTotal'] = $count;
$users['recordsFiltered'] = $count;
$users['data'] = array();
foreach ($all_users as $user):
  if ($user['user_id'] > 0):
    $obj = new user($user['user_id']);
    $user_id = $user['user_id'];
    $name = $obj->get_user_meta('firstname') . " " . $obj->get_user_meta('lastname');
    $phone_number = $obj->get_login();
    $email = $obj->get_user_email();
    $date = $obj->get_registered();
    $user['name'] = $name;
    $user['phone_number'] = $phone_number;
    $user['course'] = $obj->get_user_meta('course');
    $user['date'] = jdate('Y/m/j', $date);
    if ($_GET['type'] == 'student') {
      $user['op'] = '<div class="dropdown">
      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="index.php?page=user/profile.php&uid=' . $user_id . '"><i class="fa-regular fa-eye me-1"></i> پروفایل</a>
        <a class="dropdown-item" href="index.php?page=user/' . $page_url . '.php&uid=' . $user_id . '"><i class="bx bx-edit-alt me-1"></i> ویرایش سریع</a>
        <a class="dropdown-item" href="index.php?page=plans/my-plans.php&uid=' . $user_id . '"><i class="bx bx-calendar me-1"></i> افزودن برنامه تحصیلی</a>
        <a class="dropdown-item" href="index.php?page=defined-plans/arrange-plan.php&uid=' . $user_id . '"><i class="bx bx-calendar me-1"></i> افزودن برنامه تحصیلی هوشمند</a>
        <a class="dropdown-item" href="index.php?page=user/' . $page_url . '.php&delete=' . $user_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
      </div>
    </div>';
    } else {
      $user['op'] = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=user/profile.php&uid=' . $user_id . '"><i class="fa-regular fa-eye me-1"></i> پروفایل</a>
      <a class="dropdown-item" href="index.php?page=user/' . $page_url . '.php&uid=' . $user_id . '"><i class="bx bx-edit-alt me-1"></i> ویرایش سریع</a>
      <a class="dropdown-item" href="index.php?page=user/' . $page_url . '.php&delete=' . $user_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    }

    array_push($users['data'], $user);
  endif;
endforeach;

echo json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
