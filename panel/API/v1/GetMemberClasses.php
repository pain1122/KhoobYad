<?php

use InstagramScraper\Model\Like;

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/tag.php");

$params = $columns = $totalRecords = $taxonomies = array();
$params = $_REQUEST;
$columns = array(
    0 => 'id',
    1 => 'name'
);
$where = '';
$id = $_GET['id'];
$search = urlencode($params['search']['value']);
if (!empty($search)) {
    $where = " AND (`name` Like '%" . $search . "%'
    OR `id` = " . intval($search) . "";
}

if (empty($id)) {
    $query = "SELECT `tag`.`tag_id` as `id`,`name` FROM `tag`
        INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
        WHERE `type` = 'class_group'
        $where
        GROUP BY `tag`.`tag_id`
        ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
        LIMIT " . $params['start'] . "," . $params['length'] . ";";

    $count_query = "SELECT `tag`.`tag_id` as `id`,`name` FROM `tag`
        INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
        WHERE `type` = 'class_group'
        $where
        GROUP BY `tag`.`tag_id`";
} else {
    $query = "SELECT `users`.`user_id` as `id`,`nicename` as `name` FROM `users`
        INNER JOIN `user_meta` ON `user_meta`.`user_id` = `users`.`user_id`
        WHERE `key` = 'class_groups' AND `value` LIKE '%$id,%'
        $where
        GROUP BY `users`.`user_id`
        ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . "
        LIMIT " . $params['start'] . "," . $params['length'] . ";";

    $count_query = "SELECT `users`.`user_id` as `id`,`nicename` as `name` FROM `users`
        INNER JOIN `user_meta` ON `user_meta`.`user_id` = `users`.`user_id`
        WHERE `key` = 'class_groups' AND `value` LIKE '%$id,%'
        $where
        GROUP BY `users`.`user_id`";
}
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
    $taxonomy_id = $taxonomy['id'];
    $operations = '<a class="dropdown-item" href="index.php?page=user/all-classes.php&id=' . $taxonomy_id . '"><i class="bx bx-edit-alt me-1"></i> ویرایش</a><a class="dropdown-item" href="index.php?page=user/all-classes.php&delete_class=' . $taxonomy_id . '"><i class="bx bx-trash me-1"></i> حذف</a>';
    if ($id)
        $operations = '<a class="dropdown-item" href="index.php?page=user/all-classes.php&id=' . $id . '&delete_member=' . $taxonomy_id . '"><i class="bx bx-trash me-1"></i> حذف</a>';
    $taxonomy['op']  = '<div class="dropdown">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>
    </button>
    <div class="dropdown-menu">
    ' . $operations . '
    </div>
  </div>';
    array_push($taxonomies['data'], $taxonomy);
endforeach;
echo json_encode($taxonomies, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
