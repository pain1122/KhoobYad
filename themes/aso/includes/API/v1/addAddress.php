<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
$name = urldecode($_GET['name']);
$title = urldecode($_GET['title']);
if(strlen($title) == 0 ){
    $title = $name;
}
$state = $_GET['providence'];
$city = $_GET['city'];
$postcode = $_GET['postcode'];
$address = $_GET['address'];
$user_id = $_SESSION['user_info']["uid"];
$user = new user($user_id);
$addresses = json_decode($user->get_user_meta('addresses'), true);
if (is_countable($addresses)) {
    unset($addresses[$name]);
    $addresses[$title] = ['name' => $title, 'state' => $state, 'city' => $city, 'address' => $address, 'postcode' => $postcode];
    $addresses = json_encode($addresses, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $user->insert_user_meta(['addresses' => $addresses]);
    echo json_encode($addresses);
} else {
    $addresses = [$name => ['name' => $name, 'state' => $state, 'city' => $city, 'address' => $address, 'postcode' => $postcode]];
    $addresses = json_encode($addresses, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $user->insert_user_meta(['addresses' => $addresses]);
    echo 'true';
}
