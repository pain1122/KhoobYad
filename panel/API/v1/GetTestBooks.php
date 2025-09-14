<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
use InstagramScraper\Model\Like;
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
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
    WHERE `post`.`post_type` = 'test-book'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$query_count = "SELECT `post`.`post_id` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'test-book'
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
$user = new user($_SESSION['uid']);
$role = $user->get_user_meta('role');
$user_classes = json_decode($user->get_user_meta('classes'), true);
$user_order_items = base::get_user_orders_items($uid);
$test_books = json_decode($user->get_user_meta('test-books'), true);
if (empty($test_books)) {
  $test_books = array();
}
foreach ($all_posts as $post):
  if ($post['post_id'] > 0):
    $obj = new product($post['post_id']);
    $obj->set_post_type('test-book');
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
        $button = "<a href='?page=test-books/view.php&test-book=$post_id&type=practice' class='btn btn-sm btn-primary ml-3' style='width: 80px;'>آموزشی</a>
        <a href='?page=test-books/view.php&test-book=$post_id&type=practical' class='btn btn-sm btn-primary ml-3' style='width: 80px;'>آزمایشی</a>";
        if (in_array($post_id, $test_books)) {
          $button .= '<a class="btn btn-sm btn-primary ml-3" href="?page=test-books/my-tests.php&test-book=' . $post_id . '">نمایش نتایح</a>';
        }
      } else {
        $button = "<button class='btn btn-success mt-1 mb-0' value='$post_id' type='submit' name='add-cart-item'><i class='fas fa-shopping-cart me-2'></i>".number_format($price)." تومان</button>";
      }
      $post['op'] = $button;
    } else {
      $post['op'] = '<div class="dropdown">
      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="?page=test-books/view.php&test-book=' . $post_id . '" target="_blank"><i class="fa-regular fa-eye me-1"></i> نمایش</a>
        <button type="button" value="https://my10020.ir/panel/index.php?page=test-books/all-test-books.php&id=' . $post_id . '" class="dropdown-item" onclick="copyToClipBoard(this)" ><i class="fa-regular fa-share me-1"></i> اشتراک گذاری</button>
        <a class="dropdown-item" href="?page=test-books/rankings.php&test-book=' . $post_id . '" target="_blank"><i class="fa-solid fa-ranking-star"></i> نمایش رتبه بندی</a>
        <a class="dropdown-item" href="index.php?page=test-books/add-test-book.php&id=' . $post_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
        <a class="dropdown-item" href="index.php?page=test-books/all-test-books.php&delete=' . $post_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
      </div>
    </div>';
    }
    array_push($posts['data'], $post);
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
