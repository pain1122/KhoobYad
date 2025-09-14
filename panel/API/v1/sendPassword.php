<?php
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");

$username = $_GET['username'];
return $username;