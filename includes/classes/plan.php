<?php
class plan extends Base
{
    protected $con;
    protected $id,$plan_user,$plan_parent,$plan_id,$plan_session_id,$plan_duration = 0;
    protected $plan_title,$plan_status,$plan_type,$plan_content = "";

    function __CONSTRUCT($identifier = "")
    {
        $this->con = $GLOBALS['con'];
        if (is_numeric($identifier)) {
            $this->id = $identifier;
        } elseif (is_string($identifier) && strlen($identifier)>0 && $identifier != "new_plan") {
            $this->plan_slug = urlencode(str_replace(' ', '-', trim($identifier)));
            $this->id = parent::FetchAssoc("SELECT `id` FROM `plans` WHERE `title` = '{$this->plan_slug}'")['id'];
        }
        if ($identifier == "new_plan") {
            parent::RunQuery("INSERT INTO `plans` (`title`) VALUES ('')");
            $this->id = $this->con->insert_id;
        }
    }


/**
     * static
     *
     * @param  array $args
     * $args = [
     *      'plan_type'   => 'week',
     *      'user_id'     => 0,
     *      'skip_plans'  => 0,
     *      'numberplans' => 12,
     *      'plan_status' => 'any',
     *      'title'       => '',
     *      'date'        => array($date_min,$date_max),
     *      'plan_id'     => 0,
     *      'parent'      => 0,
     *      'session_id'  => 0,
     *      'orderby'     => '$plan_title,$plan_id',
     *      'order'       => 'DESC',
     *      'exclude'     => array($id)
     * ]
     * @return array id
     */
    static function get_plans(array $args = null)
    {
        $defaults = array(
          'plan_type'   => 'week',
          'user_id'     => 0,
          'skip_plans'  => 0,
          'numberplans' => 12,
          'plan_status' => 'any',
          'title'       => '',
          'date'        => array(),
          'plan_id'     => 0,
          'session_id'  => 0,
          'parent'      => 0,
          'orderby'     => 'id',
          'order'       => 'DESC',
          'exclude'     => array()
        );

        $title_con = $date_con = $status_con = $parent_con = $exclude_con = $plan_id_con = $session_id_con = $user_id_con = '';

        if (is_countable($args) && count($args) > 0)
            $parsed_args = array_merge($defaults, $args);
        else
            $parsed_args = $defaults;

        if (is_countable($parsed_args['exclude']) && count($parsed_args['exclude']) > 0) {
            $parsed_args['exclude'] = array_unique($parsed_args['exclude']);
        }

        if (is_countable($parsed_args['date']) && count($parsed_args['date']) == 2) {
            $min = $parsed_args['date'][0];
            $max = $parsed_args['date'][1];
            $date_con = " AND (ROUND(UNIX_TIMESTAMP(`plans`.`date`)) >= $min AND ROUND(UNIX_TIMESTAMP(`plans`.`date`)) <= $max)";
            if ($max == 0) {
                $date_con = " AND ROUND(UNIX_TIMESTAMP(`plans`.`date`)) >= $min";
            }
        }

        if (strlen($parsed_args['title']) > 0) {
            $name_arr = explode(' ', $parsed_args['title']);
            foreach ($name_arr as $arr) {
                $title_con .= " AND `title` LIKE '%$arr%'";
            }
        }

        if (intval($parsed_args['user_id']) > 0) {
            $filter_user_id = $parsed_args['user_id'];
            $user_id_con .= " AND `user_id` = '$filter_user_id'";
        }

        if (intval($parsed_args['parent']) > 0) {
            $filter_parent = $parsed_args['parent'];
            $parent_con .= " AND `parent` = '$filter_parent'";
        }

        if (intval($parsed_args['plan_id']) > 0) {
            $filter_plan_id = $parsed_args['plan_id'];
            $plan_id_con .= " AND `plan_id` = '$filter_plan_id'";
        }

        if (intval($parsed_args['session_id']) > 0) {
            $filter_session_id = $parsed_args['session_id'];
            $session_id_con .= " AND `session_id` = '$filter_session_id'";
        }
        
        if ($parsed_args['plan_status'] == 'failed') {
            $parsed_args['plan_status'] = 3;
        } else if ($parsed_args['plan_status'] == 'practiced') {
            $parsed_args['plan_status'] = 2;
        }else if ($parsed_args['plan_status'] == 'completed') {
            $parsed_args['plan_status'] = 1;
        }else if ($parsed_args['plan_status'] == 'unfinished') {
            $parsed_args['plan_status'] = 4;
        } else {
            $parsed_args['plan_status'] = 0;
        }
        if (intval($parsed_args['plan_status']) > 0) {
            $filter_status = $parsed_args['plan_status'];
            $status_con .= " AND `status` = '$filter_status'";
        }
        if (is_countable($parsed_args['exclude']) && count($parsed_args['exclude']) > 0) {
            $banned_ids = '';
            foreach ($parsed_args['exclude'] as $id) {
                if (!strpos($banned_ids, $id)) {
                    $banned_ids .= $id . ',';
                }
            }
            if (strlen($banned_ids) > 0) {
                $banned_ids = rtrim(trim($banned_ids), ',');
                $exclude_con = " AND `plans`.`id` NOT IN ($banned_ids)";
            }
        }
        $plan_query =
            "SELECT * FROM `plans`
        WHERE `plans`.`type` = '{$parsed_args['plan_type']}'
        $status_con
        $title_con
        $user_id_con
        $date_con
        $plan_id_con
        $parent_con
        $session_id_con
        $exclude_con
        GROUP BY `plans`.`id`
        ORDER BY {$parsed_args['orderby']} {$parsed_args['order']}
        LIMIT {$parsed_args['skip_plans']}, {$parsed_args['numberplans']}";
        $plans = base::FetchArray($plan_query);
        return $plans;
    }


    public function get_id(){
        return $this->id;
    }
    
    public function get_user()
    {
        if (!$this->plan_user || strlen($this->plan_user) == 0) {
            $this->plan_user = parent::fetchassoc("SELECT `user_id` FROM `plans` WHERE `id` = {$this->id}")['user_id'];
        }
        return $this->plan_user;
    }
    public function set_user($new_user)
    {
        $this->plan_user = $new_user;
        parent::RunQuery("UPDATE `plans` SET `user_id` = '{$this->plan_user}' WHERE `id` = {$this->id}");
    }



    public function get_title()
    {
        if (!$this->plan_title || strlen($this->plan_title) == 0) {
            $this->plan_title = urldecode(parent::fetchassoc("SELECT `title` FROM `plans` WHERE `id` = {$this->id}")['title']);
        }
        return $this->plan_title;
    }
    public function set_title($new_title)
    {
        $this->plan_title = $new_title;
        parent::RunQuery("UPDATE `plans` SET `title` = '{$this->plan_title}' WHERE `id` = {$this->id}");
    }


    public function get_content()
    {
        if (!$this->plan_content || strlen($this->plan_content) == 0) {
            $this->plan_content = urldecode(parent::fetchassoc("SELECT `content` FROM `plans` WHERE `id` = {$this->id}")['content']);
        }
        return $this->plan_content;
    }
    public function set_content($new_content)
    {
        $this->plan_content = $new_content;
        parent::RunQuery("UPDATE `plans` SET `content` = '{$this->plan_content}' WHERE `id` = {$this->id}");
    }



    public function get_status()
    {
        if (!$this->plan_status || strlen($this->plan_status) == 0) {
            $this->plan_status = parent::fetchassoc("SELECT `status` FROM `plans` WHERE `id` = {$this->id}")['status'];
        }
        return $this->plan_status;
    }
    public function set_status($new_status)
    {
        $this->plan_status = $new_status;
        parent::RunQuery("UPDATE `plans` SET `status` = '{$this->plan_status}' WHERE `id` = {$this->id}");
    }



    public function get_parent()
    {
        if (!$this->plan_parent || strlen($this->plan_parent) == 0) {
            $this->plan_parent = parent::fetchassoc("SELECT `parent` FROM `plans` WHERE `id` = {$this->id}")['parent'];
        }

        return $this->plan_parent;
    }
    public function set_parent($new_parent)
    {
        $this->plan_parent = $new_parent;
        parent::RunQuery("UPDATE `plans` SET `parent` = $this->plan_parent WHERE `id` = {$this->id}");
    }



    public function get_plan()
    {
        if (!$this->plan_id || strlen($this->plan_id) == 0) {
            $this->plan_id = parent::fetchassoc("SELECT `plan_id` FROM `plans` WHERE `id` = {$this->id}")['id'];
        }

        return $this->plan_parent;
    }
    public function set_plan($new_plan)
    {
        $this->plan_id = $new_plan;
        parent::RunQuery("UPDATE `plans` SET `plan_id` = $this->plan_id WHERE `id` = {$this->id}");
    }



    public function get_session()
    {
        if (!$this->plan_session_id || strlen($this->plan_session_id) == 0) {
            $this->plan_session_id = parent::fetchassoc("SELECT `session_id` FROM `plans` WHERE `id` = {$this->id}")['parent'];
        }

        return $this->plan_parent;
    }
    public function set_session($new_session_id)
    {
        $this->plan_session_id = $new_session_id;
        parent::RunQuery("UPDATE `plans` SET `session_id` = $this->plan_session_id WHERE `id` = {$this->id}");
    }



    public function get_type()
    {
        if (!$this->plan_type || strlen($this->plan_type) == 0) {
            $this->plan_type = parent::fetchassoc("SELECT `type` FROM `plans` WHERE `id` = {$this->id}")['type'];
        }
        return $this->plan_type;
    }

    
    public function set_type($new_type)
    {
        $this->plan_type = $new_type;
        parent::RunQuery("UPDATE `plans` SET `type` = '{$this->plan_type}' WHERE `id` = {$this->id}");
    }


    public function get_duration()
    {
        if (!$this->plan_duration || strlen($this->plan_duration) == 0) {
            $this->plan_duration = parent::fetchassoc("SELECT `duration` FROM `plans` WHERE `id` = {$this->id}")['duration'];
        }
        return $this->plan_duration;
    }

    
    public function set_duration($new_duration)
    {
        $this->plan_duration = $new_duration;
        parent::RunQuery("UPDATE `plans` SET `duration` = {$this->plan_duration} WHERE `id` = {$this->id}");
    }
} 