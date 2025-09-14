<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
include_once("../../../includes/config.php");
include_once("../../../includes/classes/post.php");
$userid = $_SESSION['uid'];
$id = $_GET['post_id'];
if ($id > 0) {
    // query to get the subject
    $query = "SELECT `post_id` FROM `post` 
    WHERE `post_type` = 'chat' AND `post_id` = $id";
    $chat = base::FetchAssoc($query);
    // if there is a subject
    if ($chat['post_id'] > 0) {
        $chat = new post($id);
        $chat->set_post_type('chat');
        $title = $chat->get_title();
        $pined_message = $chat->get_content('pined_message');
        $members = json_decode($chat->get_meta('members'),true);
        if(is_countable($members) && count($members) > 0)
            $members = count($members);
        else
            $members = 0;
        $all = array();
        $all['pined_message'] = $pined_message;
        $all['title'] = $title;
        $all['members'] = $members;
        // select all the messages of the subject
        $query = "SELECT `post_id`,`author`,`mime_type`,`post_content`,`post_title`,`post_excerpt`,ROUND(UNIX_TIMESTAMP(`post_date`)) as `date` FROM `post` 
        WHERE `post_type` = 'message' AND `post_parent` = $id  ;";
        // creat an array of it
        $messages = base::FetchArray($query);
        foreach ($messages as $message) {
            // get username of the messenger
            $user = $message['post_excerpt'];
            // get the date of the message
            $date = jdate('H:i Y/m/j', $message['date']);
            // check user role
            if ($message['author'] !== $userid) {
                $role = 'left';
            } else {
                $role = 'right';
            }
            $post_id = $message['post_id'];
            $users = base::FetchAssoc("SELECT `value` FROM `post_meta` WHERE `key` = 'users' AND `post_id` = $post_id")['value'];
            if($users[0]){
                $users = json_decode($users,true);
                $users = array_unique($users);
            }else{
                $users = [];
            }
            $users[] = $userid;
            $users = json_encode($users);
            base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = $post_id AND `key` = 'users'");
            base::RunQuery("INSERT INTO `post_meta` (`post_id`,`key`,`value`) VALUES ($post_id,'users','$users')");
            // add to array
            $message['userid'] = $userid;
            $message['username'] = $user;
            $message['date'] = $date;
            $message['role'] = $role;
            if (empty($message['post_title'])) {
                $message['post_title'] = '';
            }
            if ($message['mime_type'] != 'text') {
                $media = str_replace("\"", "", $message['post_content']);
                $name = substr($media, 13);
                $file_path = site_url.upload_folder.$media;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_URL, $file_path); //specify the url
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $head = curl_exec($ch);
                $file_size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                $file_size =  base::FileSizeConvert($file_size);
                if ($message['mime_type'] == 'image') {
                    $photo = "<div class='file-wrapper'><div class='file-info'><img src ='" . base::displayphoto($media) . "'><span>" . $file_size . "</span></div><a class='file-link' href='" . $file_path . "' download></a></div>";
                } elseif ($message['mime_type'] == 'file') {
                    $photo = "<div class='file-wrapper'><div class='file-info'><strong>" . $name . "</strong><span>" . $file_size . "</span></div><a class='file-link' href='" . $file_path . "' download></a></div>";
                }
                $message['photo'] = $photo;
            }
            // push array to another array
            $tickets[] = $message;
        }
        $all['tickets'] = $tickets;
        // return json of the new array
        echo json_encode($all);
    }
}
