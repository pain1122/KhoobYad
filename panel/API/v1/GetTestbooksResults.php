<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/user.php");

$test_book = $_GET['ID'];
$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
  0 => 'rank',
  1 => 'user_name',
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
    $where
    AND `author` > 0
    ORDER BY `post`.`post_excerpt` * 1 DESC
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$query_count = "SELECT `post_id` FROM `post` 
    WHERE `post`.`post_type` = 'test_book_result'
    AND `post_parent` = $test_book
    $where
    AND `author` > 0
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
$rank = $params['start'] + 1;
foreach ($all_posts as $post):
  if ($post['post_id'] > 0):
    $obj = new post($post['post_id']);
    $post_id = $post['post_id'];
    $author_name = $obj->get_title();
    $score = $obj->get_excerpt();
    $correct = $obj->get_meta('corrects');
    $wrong = $obj->get_meta('wrong');
    $ng = $obj->get_meta('ng');
    $post['post_id'] = $post_id;
    $post['rank'] = $rank;
    $post['user_name'] = $author_name;
    $post['score'] = round($score);
    $post['correct'] = $correct;
    $post['wrong'] = $wrong;
    $post['ng'] = $ng;
    array_push($posts['data'], $post);
    $rank++;
  endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
