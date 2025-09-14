<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/tag.php");

$params = $columns = $totalRecords = $taxonomies = array();
$params = $_REQUEST;
$columns = array(
  0 => 'tag_id',
  1 => 'name',
  2 => 'slug',
);
$where = '';
$type = $_GET['type'];
$parent = $_GET['parent'];
if ($parent)
  $parnet_q = "AND `tag_meta`.`parent` = $parent";
else
  $parnet_q = "AND `tag_meta`.`parent` = 0";
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
  $parnet_q 
  GROUP BY `tag`.`tag_id`
  ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
  LIMIT " . $params['start'] . "," . $params['length'] . ";";
$count_query = "SELECT `tag`.`tag_id` FROM `tag`
INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
WHERE `tag_meta`.`type` = '$type'
$where
$parnet_q 
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
  if($taxonomy_id) :
    $obj = new tag($taxonomy_id);
    $taxonomy_name = $obj->get_name();
    $taxonomy_slug = urldecode($obj->get_slug());
    $operations = '<a class="dropdown-item" href="index.php?page=tag/pa-tag.php&type=' . $type . '&parent=' . $taxonomy_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> پیکربندی</a><a class="dropdown-item" href="index.php?page=tag/pa-tag.php&type=' . $type . '&id=' . $taxonomy_id . '&edit=true" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>';
    if ($parent)
      $operations = '<a class="dropdown-item" href="index.php?page=tag/pa-tag.php&type=' . $type . '&parent=' . $parent . '&id=' . $taxonomy_id . '&edit=true" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>';
    if (strpos($taxonomy_slug, 'pa_') === 0 || strpos($taxonomy_slug, 'var_') === 0) {
      $parent_tag = $obj->get_parent();
      if (strpos($taxonomy_slug, 'pa_') === 0)
        $taxonomy_slug = str_replace('pa_', '', $taxonomy_slug);
      elseif (strpos($taxonomy_slug, 'var_') === 0)
        $taxonomy_slug = str_replace('var_', '', $taxonomy_slug);
      $operations = '<a class="dropdown-item" href="index.php?page=tag/pa-tag.php&type=' . $type . '&parent=' . $parent_tag . '&id=' . $taxonomy_id . '" target="_blank"><i class="bx bx-edit-alt me-1"></i> ویرایش</a>';
    }
    $taxonomy['name'] = $taxonomy_name;
    $taxonomy['slug'] = $taxonomy_slug;
    $taxonomy['op']  = '<div class="dropdown">
      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="bx bx-dots-vertical-rounded"></i>
      </button>
      <div class="dropdown-menu">
        ' . $operations . '
        <a class="dropdown-item" href="' . $taxonomy_id . '"><i class="bx bx-trash me-1"></i> حذف</a>
      </div>
    </div>';
    array_push($taxonomies['data'], array_values($taxonomy));
  endif;
endforeach;

echo json_encode($taxonomies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
