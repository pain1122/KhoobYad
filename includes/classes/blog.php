<?php

class blog extends post
{
    protected $thumbnail = 0;
    protected $image_alt, $seo_title, $seo_desc, $seo_keywords, $no_index, $url, $thumbnail_src = "";
    protected $cats, $tags = [];

    function __construct($post_identifier = "")
    {
        parent::__construct($post_identifier);
        $this->post_type = 'post';
    }


    /**
     * static
     *
     * @param  array $args
     * $args = [
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
        WHERE `post`.`post_type` = 'post'
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



    public function get_taxonomy($type)
    {
        $taxonomies_query = "SELECT `t`.`name`,`t`.`tag_id`,`t`.`slug` FROM `tag` `t`
        INNER JOIN `tag_meta` `tm` ON `t`.`tag_id` = `tm`.`tag_id`
        INNER JOIN `tag_relationships` `tr` ON `tr`.`tag_id` = `t`.`tag_id`
        WHERE `tr`.`object_id` = {$this->post_id} AND `tm`.`type` = '$type'";
        $taxonomies = $this->FetchArray($taxonomies_query);
        return $taxonomies;
    }
    public function set_taxonomy($type, array $new_tags)
    {
        $tags = $this->get_taxonomy($type);

        if (is_countable($tags) && count($tags) > 0) {
            foreach ($tags as $tag) :
                $dlt_query = "DELETE FROM `tag_relationships` where `object_id` = {$this->post_id} AND `tag_id` = {$tag['tag_id']}";
                base::RunQuery($dlt_query);
            endforeach;
        }
        foreach ($new_tags as $name => $id) :
            if (intval($id) > 0) {
                $insert_query = "INSERT INTO `tag_relationships`(`object_id`, `tag_id`) VALUES ({$this->post_id} , $id)";
                base::RunQuery($insert_query);
            }
        endforeach;
    }



    public function get_thumbnail()
    {
        if (!$this->thumbnail || strlen($this->thumbnail) == 0) {
            $this->thumbnail = $this->get_meta('_thumbnail_id');
        }
        return $this->thumbnail;
    }



    public function get_thumbnail_src()
    {
        if ($this->thumbnail_src == "") {
            $this->thumbnail_src = base::FetchAssoc("SELECT `guid` FROM `post` WHERE `post_id` = {$this->get_thumbnail()}")['guid'];
        }
        if (strpos($this->thumbnail_src, upload_folder) === false && strlen($this->thumbnail_src) > 0) {
            $this->thumbnail_src = site_url . upload_folder . $this->thumbnail_src;
        }
        return $this->thumbnail_src;
    }
    public function set_thumbnail_src($new_thumbnail_src)
    {
        $this->get_thumbnail();
        if ($this->thumbnail > 0) {
            base::RunQuery("DELETE FROM `post` WHERE `post_id` = {$this->thumbnail}");
            $this->thumbnail = 0;
        }
        if (strlen($new_thumbnail_src) > 0) {
            base::RunQuery("INSERT INTO `post`(`post_title`,`post_content`,`post_status`,`guid`,`post_type`,`mime_type`,`post_parent`) 
            VALUES ('','','publish','$new_thumbnail_src','image','image',{$this->post_id})");
            $this->thumbnail = $GLOBALS['con']->insert_id;
            $this->insert_meta(['_thumbnail_id' => $this->thumbnail]);
        }
        $this->thumbnail_src = $new_thumbnail_src;
    }



    public function get_image_alt()
    {
        if (!$this->image_alt || strlen($this->image_alt) == 0) {
            $this->image_alt = $this->get_meta('image_alt');
        }
        return $this->image_alt;
    }



    public function get_seo_title()
    {
        if (!$this->seo_title || strlen($this->seo_title) == 0) {
            $this->seo_title = $this->get_meta(seo_title_name);
        }
        if(empty($this->seo_title)){
            $this->get_type();
            if($this->type == 'post')
                $this->seo_title = sprintf(base::get_option('articles_seo'),$this->get_title());
            elseif($this->type == 'product')
                $this->seo_title = sprintf(base::get_option('products_seo'),$this->get_title());
        }
        return $this->seo_title;
    }



    public function get_seo_desc()
    {
        if (!$this->seo_desc || strlen($this->seo_desc) == 0) {
            $this->seo_desc = $this->get_meta(seo_desc_name);
        }
        if(empty($this->seo_title)){
            $this->get_type();
            if($this->type == 'post')
                $this->seo_title = sprintf(base::get_option('articles_desc'),$this->get_title());
            elseif($this->type == 'product')
                $this->seo_title = sprintf(base::get_option('products_desc'),$this->get_title());
        }
        return $this->seo_desc;
    }



    public function get_seo_keywords()
    {
        if (!$this->seo_keywords || strlen($this->seo_keywords) == 0) {
            $this->seo_keywords = $this->get_meta(seo_keywords_name);
        }
        return $this->seo_keywords;
    }



    public function get_no_index()
    {
        if (!$this->no_index || strlen($this->no_index) == 0) {
            $this->no_index = $this->get_meta('noindex');
        }
        return $this->no_index;
    }



    public function get_cats()
    {
        if (!$this->cats || count($this->cats) == 0) {
            $this->cats = $this->get_taxonomy(blog_cat);
        }
        return $this->cats;
    }


    public function set_cats(array $new_cats)
    {
        $cats = $this->get_cats();

        if (is_countable($cats) && count($cats) > 0) {
            foreach ($cats as $cat) :
                $id = $cat['tag_id'];
                if ($id != 0 || $id != null || strlen($id) > 0) {
                    $dlt_query = "DELETE FROM `tag_relationships` where `object_id` = {$this->post_id} AND `tag_id` = $id";
                    base::RunQuery($dlt_query);
                }
            endforeach;
        }
        foreach ($new_cats as $name => $id) :
            if ($id != 0 || $id != null || strlen($id) > 0) {
                $insert_query = "INSERT INTO `tag_relationships`(`object_id`, `tag_id`) VALUES ({$this->post_id} , $id)";
                base::RunQuery($insert_query);
            }
        endforeach;
    }


    public function get_tags()
    {
        if (!$this->tags || count($this->tags) == 0) {
            $this->tags = $this->get_taxonomy(blog_tag);
        }
        return $this->tags;
    }
    public function set_tags(array $new_tags)
    {
        $tags = $this->get_tags();

        if (is_countable($tags) && count($tags) > 0) {
            foreach ($tags as $tag) :
                $id = $tag['tag_id'];
                if ($id != 0 || $id != null || strlen($id) > 0) {
                    $dlt_query = "DELETE FROM `tag_relationships` where `object_id` = {$this->post_id} AND `tag_id` = $id";
                    base::RunQuery($dlt_query);
                }
            endforeach;
        }
        foreach ($new_tags as $name => $id) :
            if ($id != 0 || $id != null || strlen($id) > 0) {
                $insert_query = "INSERT INTO `tag_relationships`(`object_id`, `tag_id`) VALUES ({$this->post_id} , $id)";
                base::RunQuery($insert_query);
            }
        endforeach;
    }



    public function get_url($format = null)
    {
        if (empty($format)) {
            if (!$this->url || strlen($this->url) == 0) {
                $this->get_slug();
                $this->url = site_url . blog_url .'/'. $this->post_slug;
            }
        } else {
            if ($format == 'date') {
                if (!$this->post_date || strlen($this->post_date) == 0) {
                    $this->post_date = $this->get_post_date();
                }
                if (!$this->post_slug || strlen($this->post_slug) == 0) {
                    $this->post_slug = $this->get_guid();
                }
                $this->url = date('Y/m/d', strtotime($this->post_date)) . "/" . $this->post_slug;
            } else {
                $this->url = $format;
            }
        }
        return $this->url;
    }
}
