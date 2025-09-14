<?php
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
$id = $_GET['id'];
if (!in_array($id, $_SESSION['wishlist'])) {
    array_push($_SESSION['wishlist'], $id);
} else {
    unset($_SESSION['wishlist'][$id]);
}
if ($_SESSION['user_info']) {
    $user_id = $_SESSION['user_info']["uid"];
    $wishlist = $_SESSION['wishlist'];

    foreach ($wishlist as $item) {
        $query = "SELECT `umeta_id` FROM `user_meta` WHERE `user_id` = '$user_id' AND `key` = 'wishlist' AND `value` = '$item';";
        $res = $functions->FetchAssoc($query);
        if ($res) {
            $query = "DELETE FROM `user_meta` WHERE `user_meta`.`umeta_id` = " . $res["umeta_id"];
            $functions->RunQuery($query);
        } else {
            $query = "INSERT INTO `user_meta` (`umeta_id`, `user_id`, `key`, `value`) 
            VALUES (NULL, '$user_id', 'wishlist', '$item');";
            $functions->RunQuery($query);
        }
    }
    unset($_SESSION['wishlist']);
    $_SESSION['wishlist'] = array();
}
