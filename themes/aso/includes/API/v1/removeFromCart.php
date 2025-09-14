<?php 
session_start();
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
$item_id = $_GET['id'];
if (isset($_SESSION['cart'][$item_id])) {
    unset($_SESSION['cart'][$item_id]);
}