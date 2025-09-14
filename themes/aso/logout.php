<?php

// session_destroy();
// ob_flush();
unset($_SESSION['user_info']);
//header('Refresh: 1; URL=/');
// if(!isset($_SESSION['user_info'])):
$functions->redirect("/");
// endif;
?>