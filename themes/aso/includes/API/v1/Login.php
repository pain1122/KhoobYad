<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
$phone = strval($_GET['phone']);
$user = new user($phone);
$user_id = $user->get_id();
$submit_code = $_GET['code'];
$code = $user->get_user_meta('code');
$user_info = [];
if ($submit_code == $code) {
    $_SESSION['user_info'] = array("uid" => $user_id, "user" => $phone);
    $login_container_text = 'ورود موفقیت آمیز بود';
    $user->insert_user_meta(['phone_number' => $phone]);
    $wishlist = $_SESSION['wishlist'];
    $firstname = $user->get_user_meta('firstname');
    $lastname = $user->get_user_meta('lastname');
    $phonenumber = $user->get_user_meta('phone_number');
    $providence = $user->get_user_meta('providence');
    $city = $user->get_user_meta('city');
    $address = $user->get_user_meta('address');
    foreach ($wishlist as $item) {
        $query = "SELECT * FROM `user_meta` WHERE `user_id` = '$user_id' AND `value` = '$item';";
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

    $user_info = [
        'status' => 200,
        'message' => $login_container_text,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'phonenumber' => $phonenumber,
        'providence' => $providence,
        'city' => $city,
        'address' => $address
    ];
} else {
    $user_info = [
        'status' => 100,
        'message' => $login_container_text
    ];
    $login_container_text = 'کد وارد شده اشتباه است';
}

$json = json_encode($user_info);

echo $json;
?>