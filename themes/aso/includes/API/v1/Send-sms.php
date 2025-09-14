<?php
session_start();
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(~E_ALL);
$phone = strval($_GET['phone']);
if(preg_match("/09\d{9}/m",$phone)){
$user = new user($phone);
$user_id = $user->get_id();
$random = rand(1000, 9999);
$meta = ['code' => $random, 'role' => 'user', 'phone_number' => $phone];
$user->insert_user_meta($meta);
base::send_sms($phone, "کد ورود شما به سامانه خوب یاد: $random");
$login_container_text = 'کد به شماره ' . $phone . ' ارسال شد.';

echo json_encode($login_container_text);
}else{
    $login_container_text = 'لطفا شماره را به درستی با کیبورد انگلیسی وارد نمایید';

    echo json_encode($login_container_text);
}
?>