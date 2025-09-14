<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");




$params = $columns = $totalRecords = $posts = array();
$params = $_REQUEST;
$columns = array(
    0 => 'post_id',
    1 => 'post_content',
    2 => 'post_status',
    3 => 'author',
    4 => 'post_date'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
    $where = " AND (`post`.`post_content` Like '%" . $search . "%'
    OR `post`.`post_content` Like '%" . urlencode($search) . "%'
    OR `post`.`post_status` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . $search . "%'
    OR `post_meta`.`value` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id`,`post_meta`.`value` as `rate` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'comment'
    AND `post_meta`.`key` = 'rate'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `post`.`post_id`,`post_meta`.`value` as `rate` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
    WHERE `post`.`post_type` = 'comment'
    AND `post_meta`.`key` = 'rate'
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
    if($post['post_id']):
    $obj = new post($post['post_id']);
    $post_id = $post['post_id'];
    $rate = $post['rate'];
    $post = [];
    $post['input'] = "<input class='comment-counter form-check-input mt-0' type='checkbox' name='posts[]' value='$post_id'>";
    $post['post_id'] = $post_id;
    $author = $obj->get_title();
    $post['author'] = $author;
    $content = $obj->get_content();
    $status = $obj->get_status();
    $date = $obj->get_post_date();
    $post['content'] = $content;
    $post['rate'] = $rate;
    $post['status'] = $status;
    $post['date'] = jdate('Y/m/j H:i', $date);
    array_push($posts['data'], $post);
    endif;
endforeach;

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
