<?php
session_start();
include_once('../../../includes/config.php');
$userid = $_SESSION['uid'];

$id = $_GET['id'];
if ($id > 0) {

    $query = "SELECT * FROM `post` 
    WHERE `post_type` = 'chat' AND `post_id` = $id";
    $res = base::FetchAssoc($query);
    if (!empty($res)) {
        $text = $_GET['text'];
        $type = 'text';
        $media = $_GET['image'];
        $nickname = base::FetchAssoc("SELECT `nicename` FROM `users` WHERE `user_id` = $userid")['nicename'];
        $media = json_decode($media, true)['path'];
        if (strlen($media) > 0) {
            $type = 'image';
            $file_info = pathinfo($media, PATHINFO_EXTENSION);
            $allow = ["jpg", "png", "svg", "webp", "jpeg", "gif"];
            if (!in_array($file_info, $allow))
                $type = 'file';
        }
        $title = $_GET['title'];
        if (strlen($media) > 0) {
            $query = "INSERT INTO `post`( `author`,`post_content`, `post_title`,`post_excerpt`,`post_status`,`post_type`,`post_parent`, `mime_type`)
            VALUES ('$userid','$media','$title','$nickname','sent','message',$id,'$type');";
        } else {
            $query = "INSERT INTO `post`( `author`,`post_content`, `post_title`,`post_excerpt`,`post_status`,`post_type`,`post_parent`, `mime_type`)
            VALUES ('$userid','$text','','$nickname','sent','message',$id,'$type');";
        }
        base::RunQuery($query);
    }
}
