<?php
session_start();
use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");
$user_id = $_SESSION['uid'];
$test_book = $_GET['ID'];
$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'user_name',
  1 => 'date',
  2 => 'score',
  3 => 'correct',
  4 => 'wrong'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_title` Like '%" . $search . "%'";
}
$query = "SELECT `post_id` FROM `post` 
    WHERE `post`.`post_type` = 'test_book_result'
    AND `post_parent` = $test_book
    AND `author` = $user_id
    $where
    ORDER BY `post`.`post_excerpt` * 1 DESC
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$query_count = "SELECT `post_id` FROM `post` 
    WHERE `post`.`post_type` = 'test_book_result'
    AND `post_parent` = $test_book
    AND `author` = $user_id
    $where
    GROUP BY `post`.`post_id`";
// echo $query;
$all_posts = base::FetchArray($query);
// print_r($all_posts);
if (is_countable($all_posts))
  $count = count(base::FetchArray($query_count));
else
  $count = 0;
$posts['draw'] = intval($params['draw']);
$posts['recordsTotal'] = intval($count);
$posts['recordsFiltered'] = intval($count);
$posts['data'] = array();


$test_book = new post($test_book);
$lessons = json_decode($test_book->get_meta('lessons'), true);
if (is_countable($lessons))
  $lessons_count = count($lessons);
foreach ($all_posts as $post):
  if ($post['post_id'] > 0):
    $obj = new post($post['post_id']);
    $post_id = $post['post_id'];
    $author_name = $obj->get_title();
    $score = $obj->get_excerpt();
    $date = $obj->get_post_date();
    $post['post_id'] = $post_id;
    $post['date'] = jdate('Y/m/j', $date);
    $post['score'] = round($score);
    $post['user_name'] = $author_name;
    array_push($posts['data'], $post);
    $rank++;
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
