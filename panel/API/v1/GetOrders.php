<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
include_once(base_dir . "/includes/classes/shop-order.php");

$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'post_status',
  2 => 'number',
  3 => 'price',
  4 => 'post_date',

);

$where = $user_q = '';
$uid = $_GET['uid'];
if($uid > 0) {
  $user = new user($uid);
  $role = $user->get_user_meta('role');
  if($role == 'school')
  $user_q = "AND `post`.`school` = $uid";
  else
  $user_q = "AND `post`.`author` = $uid";
}
$search = urlencode($params['search']['value']);
if (!empty($search)) {
  $where = " AND (`post`.`post_id` = " . intval($search) . "
    OR `post_meta`.`value` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id`,CONVERT(`post_meta`.`value`,unsigned integer) as `price` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'shop_order'
    $user_q
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$query_count = "SELECT `post`.`post_id` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'shop_order'
    $user_q
    $where
    GROUP BY `post`.`post_id`";
$all_posts = base::FetchArray($query);
if (is_countable($all_posts))
  $count = count(base::FetchArray($query_count));
else
  $count = 0;
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();

foreach ($all_posts as $post) :
  if(!empty($post['post_id'])){
    $obj = new order($post['post_id']);
    $post_id = $post['post_id'];
    $status = base::get_lang_title('fa',$obj->get_status());
    $sum = $obj->get_sum();
    if(strlen($sum) == 0)
      $sum = 0;
    $date = $obj->get_post_date();
    $user_phone = $obj->get_user_phone();
    $post['status'] = $status;
    $post['number'] = $user_phone;
    unset($post['price']);
    $post['price'] = number_format($sum);
    $post['date'] = jdate('Y/m/j', $date);
    $post['op']  = '
    <div class="dropdown">
      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
      </button>
      <div class="dropdown-menu">
        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#showFactor" onclick="ShowFactor(' . $post_id . ')"><i class="fa-regular fa-eye me-1"></i> نمایش</button>
        <a class="dropdown-item" href="index.php?page=order/edit-order.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
        <a class="dropdown-item" href="index.php?page=order/all-order.php&id=' . $post_id . '" target="_blank"><i class="bx bx-trash me-1"></i> حذف</a>
      </div>
    </div>';
    array_push($posts['data'], $post);
  }
endforeach;


echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
