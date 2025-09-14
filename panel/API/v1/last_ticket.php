<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
$userid = $_SESSION['uid'];
$id = $_GET['pid'];
if ($id > 0) {
    $query = "SELECT `post_id` FROM `post` 
    WHERE `post_type` = 'chat' AND `post_id` = $id";
    $res = base::FetchAssoc($query)['post_id'];
    if (!empty($res)) {
        $last_query = "SELECT `post_id`,`author`,`post_title`,`mime_type`,`post_content`,`post_excerpt`,ROUND(UNIX_TIMESTAMP(`post_date`)) as `date` FROM `post`
        WHERE `post_id` = (SELECT max(`post_id`) from `post` WHERE `post_parent` = $res)
        AND `post_type` = 'message';";
        $last = base::FetchAssoc($last_query);
        if(!empty($last)){
            $user = $last['post_excerpt'];
            $date = jdate('Y/m/j', $last['date']);
            if ($last['author'] != $userid) {
                $role = 'left';
            } else {
                $role = 'right';
            }
            $post_id = $last['post_id'];
            $users = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `key` = 'users' AND `post_id` = $post_id")['value'];
            $last['users']= $users;
            if($users[0]){
                $users = json_decode($users,true);
                if(!in_array($userid,$users))
                    $last['not_seen'] = true;
            }else{
                $last['not_seen'] = true;
            }
            $last['username'] = $user;
            $last['title'] = $last['post_title'];
            $last['date'] = $date;
            $last['role'] = $role;
            $last['file'] = 'false';
            if($last['mime_type'] == 'file'){
                $last['file'] = 'true';
            }
        }else {
            $last['no-message'] = 'پیامی یافت نشد';
        }
        echo json_encode($last,JSON_UNESCAPED_UNICODE);
    }
}
