<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/tag.php");
include_once(base_dir . "/includes/classes/user.php");



$type = $_GET['type'];
$low = $_GET['low'];
$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'post_title',
  2 => 'post_status',
  3 => 'name',
  4 => 'author',
  5 => 'post_date',
  6 => 'stock'
);
$where = '';
$low_q = "AND `post_meta`.`key` = '_price'";
if ($low == 'true') {
  $low_q = "AND (`post_meta`.`key` = '_stock' AND `post_meta`.`value` < 20)";
}
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_status` Like '%" . $search . "%'
    OR `post`.`post_id` = " . intval($search) . "
    OR `tag`.`name` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id`,`tag`.`name`,`tag`.`tag_id`,CONVERT(`post_meta`.`value`,unsigned integer) as `stock`  FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = '$type'
    AND `post`.`post_parent` = 0
    $low_q
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
    
$query_count = "SELECT `post`.`post_id`,`tag`.`name`,CONVERT(`post_meta`.`value`,unsigned integer) as `stock`  FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = '$type'
    AND `post`.`post_parent` = 0
    $low_q
    $where
    GROUP BY `post`.`post_id`";
// echo $query;
$all_posts = base::FetchArray($query);
if (is_countable($all_posts))
  $count = count(base::FetchArray($query_count));
else
  $count = 0;
$cateogory_type = str_replace('-','_',$type).'_category';
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();


foreach ($all_posts as $post) :
  if ($post['post_id'] > 0) :
    $obj = new product($post['post_id']);
    $obj->set_post_type($type);
    $post_id = $post['post_id'];
    $tag_id = $post['tag_id'];
    $post_title = $obj->get_title();
    $url = $obj->get_url();
    $status = $obj->get_status();
    $author = $obj->get_author();
    $author = new user($author);
    $author_name = $author->get_display_name();
    $date = $obj->get_post_date();
    $stock = $post['stock'];
    $post['title'] = $post_title;
    $post['status'] = $status;
    $post['tag'] = "<a href='index.php?page=tag/edit-tag.php&type=$cateogory_type&id=$tag_id'>" . $post['name'] . "</a>";
    $post['author'] = $author_name;
    $post['date'] = jdate('Y/m/j', $date);
    unset($post['stock']);
    $post['stock'] = number_format($stock);
    unset($post['name']);
    unset($post['tag_id']);
    $post['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="?page=product/view.php&id='. $post_id .'" target="_blank"><i class="fa-regular fa-eye me-1"></i> نمایش</a>
      <a class="dropdown-item" href="index.php?page=product/add-' . $type . '.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=product/all-products.php&type=' . $type . '&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
