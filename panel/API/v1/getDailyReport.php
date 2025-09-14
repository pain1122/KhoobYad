<?php

header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");

$id = $_GET['d_id'];
$message = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `post_id` = $id AND `key` = 'daily-report'");

echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);