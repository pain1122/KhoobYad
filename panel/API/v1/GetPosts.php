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
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_status` Like '%" . $search . "%'
    OR `post`.`post_id` = " . intval($search) . "
    OR `tag`.`name` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id`,`tag`.`name` FROM `post` 
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = 'post'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `post`.`post_id`,`tag`.`name` FROM `post` 
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = 'post'
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
    $obj = new blog($post['post_id']);
    $post_id = $post['post_id'];
    $post_title = $obj->get_title();
    $status = $obj->get_status();
    $author = $obj->get_author();
    $date = $obj->get_post_date();
    $url = $obj->get_url();
    $post['title'] = $post_title;
    $post['status'] = $status;
    $post['tag'] = "<a href='" . $post['name'] . "'>" . $post['name'] . "</a>";
    $post['author'] = $author;
    $post['date'] = jdate('Y/m/j', $date);
    unset($post['name']);
    $post['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="' . $url . '" target="_blank"><i class="fa-regular fa-eye me-1"></i> نمایش</a>
      <a class="dropdown-item" href="index.php?page=post/add-post.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=post/all-posts.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
