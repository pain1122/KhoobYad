<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
use InstagramScraper\Model\Like;
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
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
  6 => 'price'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_status` Like '%" . $search . "%'
    OR `post`.`post_id` = " . intval($search) . ")";
}
$id = $_GET['id'];
if(!empty($id)){
  $where .= "AND `post`.`post_id` = $id";
}
$query = "SELECT `post`.`post_id` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'descriptive-exam'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$query_count = "SELECT `post`.`post_id` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'descriptive-exam'
    $where
    GROUP BY `post`.`post_id`";
// echo $query;
$all_posts = base::FetchArray($query);
if (is_countable($all_posts))
  $count = count(base::FetchArray($query_count));
else
  $count = 0;
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();
$user_id = $_SESSION['uid'];
$user = new user($user_id);
$role = $user->get_user_meta('role');
$user_classes = json_decode($user->get_user_meta('classes'), true);
$user_order_items = base::get_user_orders_items($uid);
$descriptive_exam = json_decode($user->get_user_meta('descriptive-exams'), true);
if (empty($descriptive_exam)) {
  $descriptive_exam = array();
}
foreach ($all_posts as $post):
  if ($post['post_id'] > 0):
    $obj = new product($post['post_id']);
    $obj->set_post_type('descriptive-exam');
    $post_id = $post['post_id'];
    $post_title = $obj->get_title();
    $url = $obj->get_url();
    $status = $obj->get_status();
    $author = $obj->get_author();
    $author = new user($author);
    $author_name = $author->get_display_name();
    $date = $obj->get_post_date();
    $price = intval($obj->get_price());
    $post['title'] = $post_title;
    $post['status'] = $status;
    $post['author'] = $author_name;
    $post['date'] = jdate('Y/m/j', $date);
    $post['price'] = number_format($price);
    if ($role == 'student') {
      $term_start = $obj->get_meta('term_start');
      $term_end = $obj->get_meta('term_end');
      $time = time();
      if ((is_countable($user_classes) && preg_match('/"' . preg_quote($post_id, '/') . '"/i', json_encode($user_classes))) || ($user_order_items && array_search($post_id, $user_order_items)) || $price === 0 || ($subscription == 'exam' || $subscription == 'all')) {

        if (in_array($post_id, $descriptive_exam)) {
          $query = "SELECT `post_id` FROM `post` 
        WHERE `post`.`post_type` = 'descriptive_exam_result'
        AND `post_parent` = $post_id
        AND `author` = $user_id";
          $record_id = base::FetchAssoc($query)['post_id'];
          $button = '<a class="btn btn-sm btn-primary" href="?page=descriptive-exams/result.php&exam=' . $record_id . '">نمایش کارنامه</a>';
        } else if ($time >= $term_end || $status == 'finished') {
          $button = "<span class='btn btn-sm btn-secondary'>غیر فعال</span>";
        } else {
          $button = "<a href='?page=descriptive-exams/view.php&exam=$post_id' class='btn btn-sm btn-primary'>شرکت در آزمون</a>";
        }
      } else {
        $button = "<button class='btn btn-success mt-1 mb-0' value='$post_id' type='submit' name='add-cart-item'><i class='fas fa-shopping-cart me-2'></i>" . number_format($price) . " تومان</button>";
      }
      $post['op'] = $button;
    } else {
      $post['op'] = '<div class="dropdown">
      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="?page=descriptive-exams/descriptive-view.php&exam=' . $post_id . '" target="_blank"><i class="fa-regular fa-eye me-1"></i> نمایش</a>
        <button type="button" value="https://my10020.ir/panel/index.php?page=descriptive-exams/all-descriptive-exams.php&id=' . $post_id . '" class="dropdown-item" onclick="copyToClipBoard(this)" ><i class="fa-regular fa-share me-1"></i> اشتراک گذاری</button>
        <a class="dropdown-item" href="?page=descriptive-exams/descriptive-rankings.php&exam=' . $post_id . '" target="_blank"><i class="fa-solid fa-ranking-star"></i> نمایش رتبه بندی</a>
        <a class="dropdown-item" href="index.php?page=descriptive-exams/add-descriptive-exam.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
        <a class="dropdown-item" href="index.php?page=descriptive-exams/all-descriptive-exams.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
      </div>
    </div>';
    }
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
