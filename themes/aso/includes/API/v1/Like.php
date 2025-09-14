<?php

use function PHPSTORM_META\sql_injection_subst;


// header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
$post_id = intval($_GET['pid']);
// $post_id = mysqli_real_escape_string($con, $post_id);

if ($post_id > 0) {
    $query = "SELECT `meta_id` FROM `post_meta` WHERE `post_id` = '$post_id' AND `key` = 'like' AND `value` = '$user_id';";
    $res = $functions->FetchAssoc($query);

    if ($res) {
        $query = "DELETE FROM `post_meta` WHERE `post_meta`.`meta_id` = " . $res["meta_id"];

        $functions->RunQuery($query);
    } else {
        $query = "INSERT INTO `post_meta` (`meta_id`, `post_id`, `key`, `value`) 
        VALUES (NULL, '$post_id', 'like', '$id')";

        $functions->RunQuery($query);
    }
}
