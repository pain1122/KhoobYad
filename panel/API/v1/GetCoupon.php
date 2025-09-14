<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");

$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'post_title',
  2 => 'post_content',
  3 => 'post_status'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_content` Like '%" . $search . "%'
    OR `post`.`post_id` = " . intval($search) . ")";
}
$query = "SELECT `post`.`post_id` FROM `post`
  INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
  WHERE `post`.`post_type` = 'coupon'
  $where
  GROUP BY `post`.`post_id`
  ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
  LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `post`.`post_id` FROM `post`
  INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
  WHERE `post`.`post_type` = 'coupon'
  $where
  GROUP BY `post`.`post_id`";
$all_posts = base::FetchArray($query);
if (is_countable($all_posts))
  $count = count(base::FetchArray($count_query));
else
  $count = 0;
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();


foreach ($all_posts as $post) :
  $post_id = $post['post_id'];
  if ($post_id > 0) {
    $obj = new post($post_id);
    $name = $obj->get_title();
    $code = $obj->get_content();
    $status = $obj->get_status();
    $value = $obj->get_meta('coupon');
    $sale_end = $obj->get_meta('expire-time');
    $sale_start = $obj->get_meta('start-time');
    if ($sale_end > 0)
      $sale_end = jdate('Y/m/j', $sale_end);
    else
      $sale_end = 'بدون تاریخ';
    if ($sale_start > 0)
      $sale_start = jdate('Y/m/j', $sale_start);
    else
      $sale_start = 'بدون تاریخ';
    $uses = $obj->get_meta('uses');
    $exclusive = $obj->get_meta('exclusive');
    if ($exclusive == 'true')
      $exclusive = 'بله';
    else
      $exclusive = 'خیر';
    $user_id = $obj->get_meta('user-id');
    if (strlen($user_id) == 0)
      $user_id = '-';
    $post['name'] = $name;
    $post['code'] = $code;
    $post['status'] = $status;
    $post['value'] = $value;
    $post['exclusive'] = $exclusive;
    $post['uses'] = $uses;
    $post['user_id'] = $user_id;
    $post['sale_start'] = $sale_start;
    $post['sale_end'] = $sale_end;
    $post['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=order/coupon.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=order/coupon.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($posts['data'], $post);
  }
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
