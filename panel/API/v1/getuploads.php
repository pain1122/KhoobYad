<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");




$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'post_title',
  2 => 'post_status',
  3 => 'name',
  4 => 'author',
  5 => 'post_date'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND `post`.`post_title` Like '%" . $search . "%'";
}
$query = "SELECT `post_id`,`guid`,`post_title` FROM `post` 
    WHERE `post`.`post_type` = 'attachment'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY `post_id` DESC
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `post_id`,`guid`,`post_title` FROM `post` 
    WHERE `post`.`post_type` = 'attachment'
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
  if ($post['post_id'] > 0) :
    $obj = new post($post['post_id']);
    $obj->set_post_type('attachment');
    $post['title'] = $obj->get_title();
    $post['post_id'] = $obj->get_id();
    $post['op']  = '
    <div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <button onclick="copy(\''.$post['guid'].'\')" class="dropdown-item" type="button"><i class="bx bx-copy-alt me-1"></i>کپی لینک</button>
      <a class="dropdown-item" href="index.php?page=general/upload-center.php&delete=' . $post['post_id'] . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
