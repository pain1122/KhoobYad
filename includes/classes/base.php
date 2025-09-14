<?php

class Base
{

    public static function FetchArray($query)
    {
        if (isset($_SESSION['debug']) && $_SESSION['debug'])
            return $query;
        $result = array('Result' => false, 'Response' => 'Database Failed!');
        try {
            $sqlArray = $GLOBALS['con']->query($query);
            $Arr = array();
            while ($ar = $sqlArray->fetch_assoc())
                $Arr[] = $ar;
            return $Arr;
        } catch (mysqli_sql_exception $e) {
            $userinfo = json_encode($_SERVER);
            $error = <<<END
            Error: {$e->getmessage()}
            Query: {$query}
            USER INFO: {$userinfo}
            END;
            error_log($error, 3, base_dir . "/myphplogs");
        }
    }

    public static function FetchAssoc($query)
    {
        if ($_SESSION['debug'])
            return $query;
        try {
            $result = array('Result' => false, 'Response' => 'Database Failed!');
            $sqlAssoc = $GLOBALS['con']->query($query) or print(json_encode($result));
            $sqlAssoc = $sqlAssoc->fetch_assoc();
            return $sqlAssoc;
        } catch (mysqli_sql_exception $e) {
            $userinfo = json_encode($_SERVER);
            $error = <<<END
        Error: {$e->getmessage()}
        Query: {$query}
        USER INFO: {$userinfo}
        END;
            error_log($error, 3, base_dir . "/myphplogs");
        }
    }

    public static function get_lang_title($lang, $key)
    {
        $language_title = "SELECT `value` FROM `language` where `lang` = '$lang' and `key` = '$key'";
        $title = base::FetchAssoc($language_title);
        return $title['value'];
    }

    public static function insert_title($lang, $key, $value)
    {
        $delete_query = "DELETE FROM `language` WHERE `lang` = '$lang' AND `key` = '$key'";
        $GLOBALS['con']->query($delete_query);
        $insert_title = "INSERT INTO `language`(`lang`, `key`, `value`) VALUES ('$lang','$key','$value')";
        $GLOBALS['con']->query($insert_title);
    }

    public static function RunQuery($query)
    {
        if ($_SESSION['debug'])
            return $query;
        try {
            $result = array('Result' => false, 'Response' => 'Database Failed!');
            $sqlAssoc = $GLOBALS['con']->query($query) or print(json_encode($result));
            return $sqlAssoc;
        } catch (mysqli_sql_exception $e) {
            $userinfo = json_encode($_SERVER);
            $error = <<<END
        Error: {$e->getmessage()}
        Query: {$query}
        USER INFO: {$userinfo}
        END;
            error_log($error, 3, base_dir . "/myphplogs");
        }
    }

    public static function redirect($path)
    {
        http_response_code(301);
        echo "<script>window.location.href = '$path';</script>";
    }
    public static function ismobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    public static function get_option($key)
    {
        $option_query = "SELECT `value` FROM `options` WHERE `name` = \"$key\" ";
        if ($key != "")
            $post_meta = base::FetchAssoc($option_query);
        return html_entity_decode($post_meta['value'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    }
    public static function get_language($lang,$key)
    {
        $option_query = "SELECT * FROM `language` WHERE `key` = '$key' AND `lang` = '$lang' ";
        if ($key != "")
            $post_meta = base::FetchAssoc($option_query);
        return html_entity_decode($post_meta['value'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    }
    public static function set_option($key, $value)
    {
        if ($key != "") {
            $option_query = "INSERT INTO `options` (`name`,`value`) VALUE (\"$key\",\"$value\")";
            base::RunQuery("DELETE FROM `options` WHERE `name` = '$key'");
            base::RunQuery($option_query);
        }
    }
    
    public static function send_sms($phone, $msg)
    {
        $username = "09369681371";
        $password = "khoobyad4321@";
        $yourSenderNumber = "blacklist"; //your sender number
        $ch = curl_init();

        $body = [
            "username" => $username,
            "password" => $password,
            "senderNumber" => $yourSenderNumber,
            "numbers" => $phone,
            "message" => $msg,
        ];
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "test", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_POSTFIELDS     => http_build_query($body)
        ); 
        $url = "https://niksms.com/fa/publicapi/PtpSms";
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
    
        $content  = curl_exec($ch);
    
        curl_close($ch);
    }

    public static function Generate_Random($max)
    {

        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $token = "";
        for ($i = 0; $i < $max; $i++) {
            $token = $token . $str[rand(0, strlen($str))];
        }
        return $token;
    }



    public static function Upload($file)
    {
        $target_dir = base_dir . upload_folder;
        $file_name = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allow_file = ["docx", "doc", "pdf", "mp3", "mp4", "wav", "avi", "mov", "zip", "xlsx"];
        $allow_img = ["jpg", "png", "jpeg", "gif", "svg", "webp"];
        if (in_array($imageFileType, $allow_img)) {
            $file_name = "img_" . rand(1000000, 99999999) . $file_name;
            $max_file_size = 500 * 1024 * 1024;
        } elseif (in_array($imageFileType, $allow_file)) {
            $file_name = "file_" . rand(1000000, 99999999) . $file_name;
            $max_file_size = 20000 * 1024 * 1024;
        } else {
            die("فرمت این فایل مجاز نیست");
        }
        $target_file = $target_dir . $file_name;
        if (file_exists($target_file)) {
            die($target_file);
        }

        if ($file["size"] > $max_file_size) {
            die('file too large');
        }
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $file_name;
        } else {
            die('upload failed!');
        }
    }
    // public static function Upload_file($file)
    // {
    //     $target_dir = "../wp-content/uploads/";
    //     $file_name = "file_" . rand(1000000, 99999999) . basename($file["name"]);
    //     $target_file = $target_dir . $file_name;
    //     $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    //     if (file_exists($target_file)) {
    //         die('file exist!');
    //     }

    //     if ($file["size"] > 2048 * 100000) {
    //         die('file too large');
    //     }

    //     $allow = ["docx", "doc", "pdf", "mp3", "mp4", "wav", "avi", "mov", "zip", "xlsx"];
    //     // Allow certain file formats
    //     if (!in_array(strtolower($FileType), $allow)) {
    //         die('not allowed!');
    //     }


    //     if (move_uploaded_file($file["tmp_name"], $target_file)) {
    //         return $file_name;
    //     } else {
    //         die('upload failed!');
    //     }
    // }
    public static function isValid($ip)
    {
        $query = "SELECT count(`id`) as `cnt` FROM `security` WHERE `ip` = '$ip' and time >= now() - INTERVAL 1 DAY";
        $count = base::fetchAssoc($query);
        $count = $count['cnt'];
        if ($count >= 5)
            return false;
        else
            return true;
    }


    public static function displayphoto($user_image)
    {
        if (! empty($user_image )) {
            if (strpos($user_image, upload_folder) === false) {
                return site_url . upload_folder . $user_image;
            } else {
                return $user_image;
            }
        } else {
            return null;
        }
    }

    public static function display_read_time($content)
    {
        $count_words = str_word_count($content);

        $read_time = ceil($count_words / 35);

        return $read_time;
    }

    public static function in_array_recursive($val, array $arr)
    {
        $key = array();
        array_walk_recursive($arr, function ($v, $k) use ($val, &$key) {
            if ($v == $val) array_push($key, $k);
        });
        return count($key) > 1 ? $key : array_pop($key);
    }


    public static function get_post($number, $sort, $tag)
    {
        $post_query = "SELECT `post`.`post_id` from `post` 
         INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
         WHERE `post_type` = 'post' 
         And `post_status` = 'publish'
         GROUP BY `post`.`post_id` ";

        if ($tag > 0) {
            $post_query = "SELECT `post`.`post_id` from `post` 
            INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
            INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
            WHERE `post_type` = 'post' 
            And `post_status` = 'publish'
            AND `tag_relationships`.`tag_id` = $tag
            GROUP BY `post`.`post_id` ";
        }

        //ترتیب
        if (strlen($sort) > 0) {

            // جدیدترین
            if ($sort == "new") {
                $sort_query = " ORDER BY `post_date` DESC";
                $post_query  .= $sort_query;
            }
            //قدیمی ترین
            if ($sort == "old") {
                $sort_query = " ORDER BY `post_date` ASC";
                $post_query  .= $sort_query;
            }
        }
        // تعداد
        if ($number > 0) {
            $limit = " LIMIT 0,$number";
            $post_query  .= $limit;
        }
        $list_post = base::FetchArray($post_query);

        return $list_post;
    }

    public static $get_category_branch = array();
    public static function get_category_branch($parent, $tag)
    {
        if (is_countable($tag) && count($tag) > 0) {
            foreach ($tag as $cat) {
                if (!in_array($cat, base::$get_category_branch))
                    array_push(base::$get_category_branch, $cat);
            }
        }
        $query = "SELECT * FROM tag t 
        INNER JOIN tag_meta tm ON t.tag_id = tm.tag_id 
        INNER JOIN tag_relationships tr ON tr.tag_id = t.tag_id 
        WHERE tm.parent = $parent
        GROUP BY t.tag_id;";
        $res = base::fetcharray($query);
        if ($res) {
            foreach ($res as $row) {
                $parent = $row['tag_id'];
                if (!in_array($row['tag_id'], base::$get_category_branch))
                    array_push(base::$get_category_branch, $row['tag_id']);
                base::get_category_branch($parent, base::$get_category_branch);
            }
        }
        return base::$get_category_branch;
    }

    public static function get_product($number, $sort, $tag, $page)
    {
        $product_query = "SELECT `post`.`post_id`,CONVERT(`value`,unsigned integer) as `price` from `post` 
         INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
         WHERE `post_type` = 'product' 
         AND `post`.`post_parent` = 0
         And `post_status` = 'publish'
         GROUP BY `post`.`post_id` ";

        if ($tag > 0) {
            base::$get_category_branch = array();
            $get_category = base::get_category_branch($tag, array($tag));

            $count = 0;
            $ss = "";
            foreach ($get_category as $get) {
                $count++;
                if (count($get_category) == $count) {
                    $ss .= $get['tag_id'];
                } else {
                    $ss .= $get['tag_id'] . ",";
                }
            }

            $product_query = "SELECT `post`.`post_id`,CONVERT(`value`,unsigned integer) as `price` from `post` 
                INNER JOIN `post_meta` on `post`.`post_id`=`post_meta`.`post_id` 
                INNER JOIN `tag_relationships` on `post`.`post_id` = `tag_relationships`.`object_id`
                WHERE `post_type` = 'product'
                AND `post_meta`.`key` = '_price' 
                AND `post`.`post_parent` = 0
                AND `post_status` = 'publish'
                AND `tag_relationships`.`tag_id` IN ($ss)
                GROUP BY `post`.`post_id`";
        }

        //ترتیب
        if (strlen($sort) > 0) {

            // جدیدترین
            if ($sort == "new") {
                $sort_query = " ORDER BY `post_date` DESC";
                $product_query  .= $sort_query;
            }

            //قدیمی ترین
            if ($sort == "old") {
                $sort_query = " ORDER BY `post_date` ASC";
                $product_query  .= $sort_query;
            }

            // گران ترین
            if ($sort == "expensive") {
                $sort_query = " ORDER BY `price` DESC";
                $product_query .= $sort_query;
            }

            // ارزان ترین 
            if ($sort == "cheap") {
                $sort_query = " ORDER BY `price` ASC";
                $product_query .= $sort_query;
            }
            //رندوم
            if ($sort == "rand") {
                $sort_query = " ORDER BY RAND()";
                $product_query   .= $sort_query;
            }
        }
        // تعداد
        if ($page >= 1 && $number > 0) {
            $max = $page * $number;
            $min = $max - $number;
            $limit = " LIMIT $min,$number";
            $product_query  .= $limit;
        } elseif ($number > 0) {
            $limit = " LIMIT 0,$number";
            $product_query  .= $limit;
        }
        $list_product = base::FetchArray($product_query);
        return $list_product;
    }

    public static function ZarinpalPay($price, $call_back_url, $userid)
    {
        $data = array(
            "merchant_id" => "eb828ceb-49ac-4c31-831d-60af823b9c25",
            "amount" => $price * 10,
            "callback_url" =>  $call_back_url, // "https://lotus-attari.com/verify.php"
            "description" => "خرید تست",
            "metadata" => ["email" => "info@email.com", "mobile" => $userid],
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);



        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {
                    base::redirect('https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"]);
                }
            } else {
                echo 'Error Code: ' . $result['errors']['code'];
                echo 'message: ' .  $result['errors']['message'];
            }
        }
    }

    public static function get_user_orders($user_id) {
        $list_orders = [];
        $query = "SELECT `post_id` FROM `post` WHERE `author` = $user_id AND `post_type` = 'shop_order'";
        $list_orders = base::FetchArray($query);
        return $list_orders;
    }
    public static function get_user_orders_items($user_id) {
        $list_items = [];
        $query = "SELECT `item_id` FROM `items_order` WHERE `order_id` IN (SELECT `post_id` FROM `post` WHERE `author` = $user_id AND `post_type` = 'shop_order')";
        $list_items = base::FetchArray($query);
        return $list_items;
    }
}
