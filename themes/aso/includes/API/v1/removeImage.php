<?php
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_q = "DELETE FROM `post` WHERE `post_id` = $id";
    $functions->RunQuery($delete_q);
    $arr = ['status' => 'ok'];
    echo json_encode($arr);
}
?>