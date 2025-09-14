<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/tag.php");

$params = $columns = $totalRecords = $taxonomies = array();
$params = $_REQUEST;
$columns = array(
  0 => 'tag_id',
  1 => 'icon',
  2 => 'name',
  3 => 'slug',
  4 => 'parent',
  5 => 'count'
);
$where = '';
$type = $_GET['type'];
$search =$params['search']['value'];
if (!empty($search)) {
  $where = " AND (`tag`.`name` Like '%" . $search . "%'
    OR `tag`.`name` Like '%" .  urlencode($search) . "%'
    OR `tag`.`slug` Like '%" . $search . "%'
    OR `tag`.`slug` Like '%" . urlencode($search) . "%'
    OR `tag`.`tag_id` = " . intval($search) . ")";
}
$query = "SELECT `tag`.`tag_id` FROM `tag`
  INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
  WHERE `tag_meta`.`type` = '$type'
  $where
  GROUP BY `tag`.`tag_id`
  ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
  LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `tag`.`tag_id` FROM `tag`
  INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
  WHERE `tag_meta`.`type` = '$type'
  $where
  GROUP BY `tag`.`tag_id`";
$all_taxonomies = base::FetchArray($query);
if (is_countable($all_taxonomies))
  $count = count(base::FetchArray($count_query));
else
  $count = 0;
$taxonomies['draw'] = intval($params['draw']);
$taxonomies['recordsTotal'] = intval($count);
$taxonomies['recordsFiltered'] = intval($count);
$taxonomies['data'] = array();


foreach ($all_taxonomies as $taxonomy) :
  $taxonomy_id = $taxonomy['tag_id'];
  if ($taxonomy_id > 0) :
    $obj = new tag($taxonomy_id);
    $taxonomy_name = $obj->get_name();
    $taxonomy_slug = urldecode($obj->get_slug());
    $parent = $obj->get_parent();
    $count = $obj->get_count();
    $icon = base::displayphoto($obj->get_meta('image'));
    $taxonomy['icon'] = "<img src='$icon' width='50px'>";
    $taxonomy['name'] = $taxonomy_name;
    $taxonomy['slug'] = $taxonomy_slug;
    $taxonomy['parent'] = $parent;
    $taxonomy['count'] = $count;
    $taxonomy['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="index.php?page=tag/edit-tag.php&type=' . $type . '&id=' . $taxonomy_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>
      <a class="dropdown-item" href="index.php?page=tag/edit-tag.php&type=' . $type . '&delete=' . $taxonomy_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
    </div>
  </div>';
    array_push($taxonomies['data'], array_values($taxonomy));
  endif;
endforeach;

echo json_encode($taxonomies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
