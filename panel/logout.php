<?php
session_start();
session_destroy();
ob_flush();
echo "<script> window.location.replace('login.php')</script>";
?>