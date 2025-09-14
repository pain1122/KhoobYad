<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/user.php");




$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'post_title',
  2 => 'post_status',
  3 => 'author',
  4 => 'count',
  5 => 'post_date'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_status` Like '%" . $search . "%'
    OR `post`.`post_id` = " . intval($search) . ")";
}
$uid = $_GET['uid'];
if($uid > 0) {
  $user = new user($uid);
  $role = $user->get_user_meta('role');
  if($role == 'school')
  $user_q = "AND `post`.`school` = $uid";
  else
  $user_q = "AND `post`.`author` = $uid";
}
$query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post`
    WHERE `post_type` = 'chat'
    AND `post_status` = 'ongoing'
    $where
    $user_q
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post`
    WHERE `post_type` = 'chat'
    AND `post_status` = 'ongoing'
    $where
    $user_q
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
  if ($post['post_id'] > 0) :
    $obj = new blog($post['post_id']);
    $post_id = $post['post_id'];
    $post_title = $obj->get_title();
    $status = $obj->get_status();
    $author = $obj->get_author();
    $date = $obj->get_post_date();
    $url = $obj->get_url();
    $count = json_decode($obj->get_meta('members'), true);
    if (is_countable($count))
        $count = count($count);
    $post['title'] = $post_title;
    $post['status'] = $status;
    $post['author'] = $author;
    $post['count'] = $count;
    $post['date'] = jdate('Y/m/j', $date);
    $post['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="javascript:void(0);" onclick="chat_start('.$post_id.')" target="_blank"><i class="fa-regular fa-eye me-1"></i> نمایش</a>
      <a class="dropdown-item" href="index.php?page=counsel/submit-ticket.php&chat_id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=counsel/all-tickets.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
