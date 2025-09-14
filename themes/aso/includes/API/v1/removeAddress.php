<?php 
session_start();
header('Content-Type: application/json; charset=utf-8');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
$name = urldecode($_GET['name']);
$user_id = $_SESSION['user_info']["uid"];
$user = new user($user_id);
$addresses = json_decode($user->get_user_meta('addresses'), true);
if(is_countable($addresses) && array_key_exists($name,$addresses) !== false){
    unset($addresses[$name]);
    $addresses = json_encode($addresses, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $user->insert_user_meta(['addresses' => $addresses]);
    echo 'true';
}else{
    echo 'false';
}