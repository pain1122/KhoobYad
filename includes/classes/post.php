<?php
class post extends Base
{
    protected $con;
    protected $post_id,$post_author,$post_parent = 0;
    protected $post_date,$post_content,$post_title,$post_excerpt,$post_status,$post_slug,$post_modified,$post_guid,$post_type,$mime_type = "";

    function __CONSTRUCT($post_identifier = "")
    {
        $this->con = $GLOBALS['con'];
        if (is_numeric($post_identifier)) {
            $this->post_id = $post_identifier;
        } elseif (is_string($post_identifier) && strlen($post_identifier)>0 && $post_identifier != "new_post") {
            $this->post_slug = urlencode(str_replace(' ', '-', trim($post_identifier)));
            $this->post_id = parent::FetchAssoc("SELECT `post_id` FROM `post` WHERE `post_name` = '{$this->post_slug}'")['post_id'];
        }
        if ($post_identifier == "new_post") {
            parent::RunQuery("INSERT INTO `post` (`post_name`) VALUES ('')");
            $this->post_id = $this->con->insert_id;
        }
    }


/**
     * static
     *
     * @param  array $args
     * $args = [
     *      'post_type'  => 'post',
     *      'skip_posts'  => 0,
     *      'numberposts' => 12,
     *      'post_status' => 'publish',
     *      'title'       => '',
     *      'date'        => array($date_min,$date_max),
     *      'category'    => 0,
     *      'tag'         => 0,
     *      'orderby'     => '$date,$name',
     *      'order'       => 'DESC',
     *      'exclude'     => array($post_id)
     * ]
     * @return array post_id
     */
    static function get_posts(array $args = null)
    {
        $defaults = array(
            'post_type'        => 'post',
            'skip_posts'       => 0,
            'numberposts'      => 12,
            'post_status'      => 'publish',
            'title'            => '',
            'date'             => array(),
            'category'         => 0,
            'tag'              => 0,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'exclude'          => array()
        );

        if (is_countable($args) && count($args) > 0)
            $parsed_args = array_merge($defaults, $args);
        else
            $parsed_args = $defaults;

        if (is_countable($parsed_args['exclude']) && count($parsed_args['exclude']) > 0) {
            $parsed_args['exclude'] = array_unique($parsed_args['exclude']);
        }

        $title_con = $date_con = $tag_join = $tax_con = $tag_ids = $exclude_con = '';
        if (strlen($parsed_args['title']) > 0) {
            $name_arr = explode(' ', $parsed_args['title']);
            foreach ($name_arr as $arr) {
                $title_con .= " AND `post_title` LIKE '%$arr%'";
            }
        }
        if (is_countable($parsed_args['date']) && count($parsed_args['date']) == 2) {
            $min = $parsed_args['date'][0];
            $max = $parsed_args['date'][1];
            $date_con = " AND (ROUND(UNIX_TIMESTAMP(`post`.`post_date`)) >= $min AND ROUND(UNIX_TIMESTAMP(`post`.`post_date`)) <= $max)";
            if ($max == 0) {
                $date_con = " AND ROUND(UNIX_TIMESTAMP(`post`.`post_date`)) >= $min";
            }
        }
        if ($parsed_args['orderby'] == 'date') {
            $parsed_args['orderby'] = '`post`.`post_date`';
        } else if ($parsed_args['orderby'] == 'name') {
            $parsed_args['orderby'] = '`post`.`post_title`';
        } else {
            $parsed_args['orderby'] = '`post`.`post_id`';
        }

        if ($parsed_args['post_status'] == 'publish') {
            $parsed_args['post_status'] = 'publish';
        } else if ($parsed_args['post_status'] == 'draft') {
            $parsed_args['post_status'] = 'draft';
        } else {
            $parsed_args['post_status'] = 'publish';
        }

        if ($parsed_args['category'] > 0) {
            if (!strpos($tag_ids, $parsed_args['category'])) {
                $tag_ids .= $parsed_args['category'] . ',';
            }
        }
        if ($parsed_args['tag'] > 0) {
            if (!strpos($tag_ids, $parsed_args['tag'])) {
                $tag_ids .= $parsed_args['tag'] . ',';
            }
        }
        if (strlen($tag_ids) > 0) {
            $tag_ids = rtrim(trim($tag_ids), ',');
        }
        if ($parsed_args['category'] > 0 || $parsed_args['tag'] > 0) {
            $tag_join = " INNER JOIN `tag_relationships` ON `tag_relationships`.`object_id` = `post`.`post_id` INNER JOIN `tag_meta` on `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`";
            $tax_con = "AND (`tag_relationships`.`tag_id` IN  ($tag_ids) OR `tag_meta`.`parent` IN ($tag_ids))";
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
                $exclude_con = " AND `post`.`post_id` NOT IN ($banned_ids)";
            }
        }
        $post_query =
            "SELECT `post`.`post_id` FROM `post`
        $tag_join
        WHERE `post`.`post_type` = '{$parsed_args['post_type']}'
        AND `post`.`post_status` = '{$parsed_args['post_status']}'
        $title_con
        $date_con
        $tax_con
        $exclude_con
        GROUP BY `post`.`post_id`
        ORDER BY {$parsed_args['orderby']} {$parsed_args['order']}
        LIMIT {$parsed_args['skip_posts']}, {$parsed_args['numberposts']}";
        $posts = base::FetchArray($post_query);
        return $posts;
    }


    public function get_id(){
        return $this->post_id;
    }


    
    public function get_author()
    {
        if (!$this->post_author || strlen($this->post_author) == 0) {
            $this->post_author = parent::fetchassoc("SELECT `author` FROM `post` WHERE `post_id` = {$this->post_id}")['author'];
        }
        return $this->post_author;
    }
    public function set_author($new_author)
    {
        $this->post_author = $new_author;
        parent::RunQuery("UPDATE `post` SET `author` = '{$this->post_author}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_post_date()
    {
        if (!$this->post_date || strlen($this->post_date) == 0) {
            $this->post_date = parent::fetchassoc("SELECT ROUND(UNIX_TIMESTAMP(`post_date`)) as `post_date` FROM `post` WHERE `post_id` = {$this->post_id}")['post_date'];
        }

        return $this->post_date;
    }
    public function set_post_date($new_date)
    {
        $this->post_date = $new_date;
        parent::RunQuery("UPDATE `post` SET `post_date` = '{$this->post_date}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_content()
    {
        if (!$this->post_content || strlen($this->post_content) == 0) {
            $this->post_content = parent::fetchassoc("SELECT `post_content` FROM `post` WHERE `post_id` = {$this->post_id}")['post_content'];
        }
        return html_entity_decode($this->post_content,ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    }
    public function set_content($new_content)
    {
        $this->post_content = $new_content;
        parent::RunQuery("UPDATE `post` SET `post_content` = '{$this->post_content}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_title()
    {
        if (!$this->post_title || strlen($this->post_title) == 0) {
            $this->post_title = urldecode(parent::fetchassoc("SELECT `post_title` FROM `post` WHERE `post_id` = {$this->post_id}")['post_title']);
        }
        return $this->post_title;
    }
    public function set_title($new_title)
    {
        $this->post_title = $new_title;
        parent::RunQuery("UPDATE `post` SET `post_title` = '{$this->post_title}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_excerpt()
    {
        if (!$this->post_excerpt || strlen($this->post_excerpt) == 0) {
            $this->post_excerpt = parent::fetchassoc("SELECT `post_excerpt` FROM `post` WHERE `post_id` = {$this->post_id}")['post_excerpt'];
        }
        return $this->post_excerpt;
    }
    public function set_excerpt($new_excerpt)
    {
        $this->post_excerpt = $new_excerpt;
        parent::RunQuery("UPDATE `post` SET `post_excerpt` = '{$this->post_excerpt}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_status()
    {
        if (!$this->post_status || strlen($this->post_status) == 0) {
            $this->post_status = parent::fetchassoc("SELECT `post_status` FROM `post` WHERE `post_id` = {$this->post_id}")['post_status'];
        }
        return $this->post_status;
    }
    public function set_status($new_status)
    {
        $this->post_status = $new_status;
        parent::RunQuery("UPDATE `post` SET `post_status` = '{$this->post_status}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_slug()
    {
        if (!$this->post_slug || strlen($this->post_slug) == 0) {
            $this->post_slug = parent::fetchassoc("SELECT `post_name` FROM `post` WHERE `post_id` = {$this->post_id}")['post_name'];
        }
        return $this->post_slug;
    }
    public function set_slug($new_slug)
    {
        $this->post_slug = $new_slug;
        parent::RunQuery("UPDATE `post` SET `post_name` = '{$this->post_slug}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_post_modify()
    {
        if (!$this->post_modified || strlen($this->post_modified) == 0) {
            $this->post_modified = parent::fetchassoc("SELECT ROUND(UNIX_TIMESTAMP(`modify_date`)) as `modify_date` FROM `post` WHERE `post_id` = {$this->post_id}")['modify_date'];
        }

        return $this->post_modified;
    }
    public function set_post_modify($new_modify)
    {
        $this->post_modified = $new_modify;
        parent::RunQuery("UPDATE `post` SET `modify_date` = '{$this->post_modified}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_parent()
    {
        if (!$this->post_parent || strlen($this->post_parent) == 0) {
            $this->post_parent = parent::fetchassoc("SELECT `post_parent` FROM `post` WHERE `post_id` = {$this->post_id}")['post_parent'];
        }

        return $this->post_parent;
    }
    public function set_parent($new_parent)
    {
        $this->post_parent = $new_parent;
        parent::RunQuery("UPDATE `post` SET `post_parent` = $this->post_parent WHERE `post_id` = {$this->post_id}");
    }


    public function get_guid()
    {
        if (!$this->post_guid || strlen($this->post_guid) == 0) {
            $this->post_guid = parent::fetchassoc("SELECT `guid` FROM `post` WHERE `post_id` = {$this->post_id}")['guid'];
        }
        return $this->post_guid;
    }
    public function set_guid($new_guid)
    {
        $this->post_guid = $new_guid;
        parent::RunQuery("UPDATE `post` SET `guid` = '{$this->post_guid}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_type()
    {
        if (!$this->post_type || strlen($this->post_type) == 0) {
            $this->post_type = parent::fetchassoc("SELECT `post_type` FROM `post` WHERE `post_id` = {$this->post_id}")['post_type'];
        }
        return $this->post_type;
    }

    
    public function set_post_type($new_type)
    {
        $this->post_type = $new_type;
        parent::RunQuery("UPDATE `post` SET `post_type` = '{$this->post_type}' WHERE `post_id` = {$this->post_id}");
    }



    public function get_mime_type()
    {
        if (!$this->mime_type || strlen($this->mime_type) == 0) {
            $this->mime_type = parent::fetchassoc("SELECT `mime_type` FROM `post` WHERE `post_id` = {$this->post_id}")['mime_type'];
        }
        return $this->mime_type;
    }
    public function set_mime_type($new_mime)
    {
        $this->mime_type = $new_mime;
        parent::RunQuery("UPDATE `post` SET `mime_type` = '{$this->mime_type}' WHERE `post_id` = {$this->post_id}");
    }

    public function get_meta($key)
    {
        if ($key != "") {
            $post_meta_query = "SELECT `value` FROM `post_meta` WHERE `key` = '$key' AND `post_id` = {$this->post_id}";
            $post_meta = base::FetchAssoc($post_meta_query);
        }
        if (!is_null($post_meta))
            return html_entity_decode($post_meta['value'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
        else
            return "";
    }

    function insert_meta(array $meta_array)
    {

        foreach ($meta_array as $key => $value) {
            base::RunQuery("DELETE FROM `post_meta` 
                        WHERE `post_id` = {$this->post_id} AND `key` = '$key'");
                base::RunQuery("INSERT INTO `post_meta`( `post_id`, `key`, `value`) 
            VALUES ({$this->post_id},'$key','$value')");
        }
    }

    public function display_post_image()
    {
        $thumb_id = $this->get_meta('_thumbnail_id');
        if ($thumb_id > 0) {
            $image_url = base::FetchAssoc("SELECT * FROM `post` WHERE `post_id` = $thumb_id")['guid'];
            if (strpos($image_url, upload_folder) === false) {
                $image_url = str_replace(site_url, "", $image_url);
                $image_url = site_url . upload_folder . $image_url;
            }

            return $image_url;
        }
    }
} 