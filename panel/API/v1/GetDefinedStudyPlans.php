<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/tag.php");


$params = $columns = $totalRecords = $plans = array();
$params = $_REQUEST;
$columns = array(
  0 => 'post_id',
  1 => 'title',
  2 => 'course',
  3 => 'grade',
  4 => 'sessions'
);
$where = '';
$search = $params['search']['value'];
if (!empty($search)) {
  $where = " AND (`post`.`post_title` Like '%" . $search . "%'
    OR `post`.`post_title` Like '%" . urlencode($search) . "%'
    OR `post`.`post_id` = " . intval($search) . "
    OR `tag`.`name` Like '%" . $search . "%')";
}
$query = "SELECT `post`.`post_id` FROM `post` 
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = 'defined_plan'
    AND `post`.`post_parent` = 0
    AND `post`.`post_status` = 'publish'
    $where
    GROUP BY `post`.`post_id`
    ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
    LIMIT " . $params['start'] . "," . $params['length'] . ";";
    
$query_count = "SELECT `post`.`post_id` FROM `post` 
    LEFT JOIN `tag_relationships` ON `post`.`post_id` = `tag_relationships`.`object_id`
    LEFT JOIN `tag` ON `tag`.`tag_id` = `tag_relationships`.`tag_id`
    WHERE `post`.`post_type` = 'defined_plan'
    AND `post`.`post_parent` = 0
    AND `post`.`post_status` = 'publish'
    $where
    GROUP BY `post`.`post_id`";
// echo $query;
$all_posts = base::FetchArray($query);
if (is_countable($all_posts))
  $count = count(base::FetchArray($query_count));
else
  $count = 0;
$cateogory_type = str_replace('-','_','defined_plan').'_category';
$plans['draw'] = intval($params['draw']);
$plans['recordsTotal'] = intval($count);
$plans['recordsFiltered'] = intval($count);
$plans['data'] = array();

foreach ($all_posts as $plan) :
  if ($plan['post_id'] > 0) :
    $obj = new blog($plan['post_id']);
    $obj->set_post_type('defined_plan');
    $plan_id = $plan['post_id'];
    $plan_title = $obj->get_title();
    $plan_slug = $obj->get_slug();
    $cats = $obj->get_taxonomy('study_course');
    $tags = $obj->get_taxonomy('study_grade');
    $variables = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_parent` = $plan_id AND `post_type` = 'defined_plan'");
    $plan['title'] = $plan_title;
    $plan['course'] = $cats[0]['name'];
    $plan['grade'] = $tags[0]['name'];
    $plan['sessions'] = count($variables);
    $plan['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=defined-plans/add-plan.php&id=' . $plan_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=defined-plans/all-defined-plans.php&delete=' . $plan_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($plans['data'], $plan);
  endif;
endforeach;

echo json_encode($plans, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
