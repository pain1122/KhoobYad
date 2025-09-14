<?php
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
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
if(isset($_GET['uid']))
  $user_q = "AND `post`.`author` = {$_GET['uid']}";
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_id` = " . intval($search) . "
    OR `post_meta`.`value` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id`,CONVERT(`post_meta`.`value`,unsigned integer) as `price` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'shop_order'
    AND `post_meta`.`key` = 'sum'
    $user_q
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$all_posts = base::FetchArray($query);
if(is_countable($all_posts))
$count = count(($all_posts));
else
$count = 0;
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();


foreach ($all_posts as $post) :
  $obj = new order($post['post_id']);
  $post_id = $post['post_id'];
  $status = $obj->get_status();
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
  $post['op']  = '<button type="button" class="button" onclick="ShowFactor(' . $post_id . ')">نمایش</button>';
  array_push($posts['data'], $post);
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
