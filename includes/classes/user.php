<?php
class user extends Base
{
    protected $con;
    protected $user_id = 0;
    protected $user_login, $nick_name, $user_email, $user_registered, $activation_key, $user_status, $password, $display_name, $grade, $fos, $adviser, $school = "";

    function __CONSTRUCT($user_identifier = "")
    { 
        $this->con = $GLOBALS['con'];
        if (is_numeric($user_identifier) && intval($user_identifier) > 0 && intval($user_identifier) < 99999999 ) {
            $this->user_id = $user_identifier;
        }  else if (empty($user_identifier) || strlen($user_identifier) == 0) {
            parent::RunQuery("INSERT INTO `users` (`login`) VALUES ('')");
            $this->user_id = $this->con->insert_id;
        } else{
            $this->user_login = trim($user_identifier);
            $this->user_id = parent::FetchAssoc("SELECT `user_id` FROM `users` WHERE `login` = '{$this->user_login}'")['user_id'];
            if (empty($this->user_id)) {
                parent::RunQuery("INSERT INTO `users` (`login`,`display_name`,`nicename`) VALUES ('{$this->user_login}','{$this->user_login}','{$this->user_login}')");
                $this->user_id = $this->con->insert_id;
            }
        }

    }
    public function get_id()
    {
        return $this->user_id;
    }



    public function get_login()
    {
        if (!$this->user_login || strlen($this->user_login) == 0) {
            $this->user_login = parent::fetchassoc("SELECT `login` FROM `users` WHERE `user_id` = {$this->user_id}")['login'];
        }
        return $this->user_login;
    }
    public function set_login($new_login)
    {
        $this->user_login = $new_login;
        parent::RunQuery("UPDATE `users` SET `login` = '{$this->user_login}' WHERE `user_id` = {$this->user_id}");
    }


    public function get_password()
    {
        if (!$this->password || strlen($this->password) == 0) {
            $this->password = parent::fetchassoc("SELECT `password` FROM `users` WHERE `user_id` = {$this->user_id}")['password'];
        }
        return $this->password;
    }
    public function set_password($new_password)
    {
        $this->password = md5($new_password);
        parent::RunQuery("UPDATE `users` SET `password` = '{$this->password}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_nick_name()
    {
        
        return $this->get_user_meta('firstname') . " " . $this->get_user_meta('lastname');
    }
    public function set_nick_name($new_nick_name)
    {
        $this->nick_name = $new_nick_name;
        parent::RunQuery("UPDATE `users` SET `nicename` = '{$this->nick_name}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_user_email()
    {
        if (!$this->user_email || strlen($this->user_email) == 0) {
            $this->user_email = parent::fetchassoc("SELECT `user_email` FROM `users` WHERE `user_id` = {$this->user_id}")['user_email'];
        }
        return $this->user_email;
    }
    public function set_user_email($new_user_email)
    {
        $this->user_email = $new_user_email;
        parent::RunQuery("UPDATE `users` SET `user_email` = '{$this->user_email}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_registered()
    {
        if (!$this->user_registered || strlen($this->user_registered) == 0) {
            $this->user_registered = parent::fetchassoc("SELECT ROUND(UNIX_TIMESTAMP(`user_registered`)) as `user_registered` FROM `users` WHERE `user_id` = {$this->user_id}")['user_registered'];
        }
        return $this->user_registered;
    }
    public function set_registered($new_user_registered)
    {
        $this->user_registered = $new_user_registered;
        parent::RunQuery("UPDATE `users` SET `user_registered` = '{$this->user_registered}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_activation_key()
    {
        if (!$this->activation_key || strlen($this->activation_key) == 0) {
            $this->activation_key = parent::fetchassoc("SELECT `user_activation_key` FROM `users` WHERE `user_id` = {$this->user_id}")['user_activation_key'];
        }
        return $this->activation_key;
    }
    public function set_activation_key($new_user_activation_key)
    {
        $this->activation_key = $new_user_activation_key;
        parent::RunQuery("UPDATE `users` SET `user_activation_key` = '{$this->activation_key}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_user_status()
    {
        if (!$this->user_status || strlen($this->user_status) == 0) {
            $this->user_status = parent::fetchassoc("SELECT `user_status` FROM `users` WHERE `user_id` = {$this->user_id}")['user_status'];
        }
        return $this->user_status;
    }
    public function set_user_status($new_user_status)
    {
        $this->user_status = $new_user_status;
        parent::RunQuery("UPDATE `users` SET `user_status` = '{$this->user_status}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_display_name()
    {
        if (!$this->display_name || strlen($this->display_name) == 0) {
            $this->display_name = parent::fetchassoc("SELECT `display_name` FROM `users` WHERE `user_id` = {$this->user_id}")['display_name'];
        }
        return $this->display_name;
    }
    public function set_display_name($new_display_name)
    {
        $this->display_name = $new_display_name;
        parent::RunQuery("UPDATE `users` SET `display_name` = '{$this->display_name}' WHERE `user_id` = {$this->user_id}");
    }



    public function get_user_meta($key)
    {
        if ($key != "") {
            $post_meta_query = "SELECT `value` FROM `user_meta` WHERE `key` = '$key' AND `user_id` = {$this->user_id} ORDER BY `umeta_id` DESC";
            $post_meta = base::FetchAssoc($post_meta_query);
        }
        if (!is_null($post_meta))
            return html_entity_decode($post_meta['value'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
        else
            return "";
    }
    public function insert_user_meta(array $meta_array)
    {
        foreach ($meta_array as $key => $value) {

            $old_value = base::FetchAssoc("SELECT `value` FROM `user_meta` WHERE `key` = '$key' AND `user_id` = {$this->user_id} ORDER BY `umeta_id` DESC");
            if (!empty($old_value))
                base::RunQuery("UPDATE `user_meta` SET `value` = '$value' 
                        WHERE `user_id` = {$this->user_id} AND `key` = '$key'");
            else
                base::RunQuery("INSERT INTO `user_meta`( `user_id`, `key`, `value`) 
                VALUES ({$this->user_id},'$key','$value')");
        }
    }

    public function get_grade()
    {
        if (!$this->grade || strlen($this->grade) == 0) {
            $this->grade = $this->get_user_meta('grade');
            if (strlen($this->grade) > 0){
                $this->grade = base::FetchAssoc("SELECT `name` FROM `tag` WHERE `tag_id` = {$this->grade}")['name'];
                if (strlen($this->grade) > 0){
                return $this->grade;
                }else{
                    return 'نامشحص';
                }
            }
        }
    }
    public function get_fos()
    {
        if (!$this->fos || strlen($this->fos) == 0) {
            $this->fos = $this->get_user_meta('fos');
            if (strlen($this->fos) > 0){
                $this->fos = base::FetchAssoc("SELECT `name` FROM `tag` WHERE `tag_id` = {$this->fos}")['name'];
                if (strlen($this->fos) > 0){
                return $this->fos;
                }else{
                    return 'نامشحص';
                }
            }
        }
    }
    public function get_adviser()
    {
        if (!$this->adviser || strlen($this->adviser) == 0) {
            $this->adviser = $this->get_user_meta('adviser');
            if (strlen($this->adviser) > 0){
                $this->adviser = base::FetchAssoc("SELECT `nicename` FROM `users` WHERE `user_id` = {$this->adviser}")['nicename'];
                if (strlen($this->adviser) > 0){
                return $this->adviser;
                }else{
                    return 'نامشحص';
                }
            }
        }
    }
    public function get_school()
    {
        if (!$this->school || strlen($this->school) == 0) {
            $this->school = $this->get_user_meta('school');
            if (strlen($this->school) > 0){
                return $this->school;
            }else{
                return 'نامشحص';
            }
        }
    }
}
