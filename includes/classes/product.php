<?php
class product extends blog
{

    protected $price, $regular_price, $sale_price, $video_id, $video_cover_id, $stock, $restrict = 0;
    protected $stock_status, $special, $consult, $complementaries, $replacements = '';
    protected $brands, $cats, $tags = [];

    function __construct($post_identifier = "")
    {
        parent::__construct($post_identifier);
        $this->set_post_type('product');
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
     *      'min_price'   => 0,
     *      'max_price'   => 0,
     *      'stock'       => 0,
     *      'stock_status'=> 'instock',
     *      'orderby'     => '$date,$name,$price,$stock',
     *      'order'       => 'DESC',
     *      'special'     => 'off',
     *      'exclude'     => array(post_ids)
     * ]
     * @return array post_id
     */
    static function get_products(array $args = null)
    {
        $defaults = array(
            'skip_posts'       => 0,
            'numberposts'      => 12,
            'post_status'      => 'publish',
            'title'            => '',
            'date'             => array(),
            'category'         => 0,
            'tag'              => 0,
            'min_price'        => 0,
            'max_price'        => 0,
            'stock'            => 0,
            'stock_status'     => 'instock',
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'special'          => 'off',
            'exclude'          => array()
        );
        if(is_countable($args) && count($args)>0)
            $parsed_args = array_merge($defaults, $args);
        else
            $parsed_args = $defaults;
        $title_con = $date_con = $tag_join = $tax_con = $tag_ids = $price_con = $stock_con = $special_con = $exclude_con = '';
        
        if (is_countable($parsed_args['exclude']) && count($parsed_args['exclude'])>0) {
            $parsed_args['exclude'] = array_unique($parsed_args['exclude']);
        }
        
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
            if (!$max || $max == 0) {
                $date_con = " AND ROUND(UNIX_TIMESTAMP(`post`.`post_date`)) >= $min";
            }
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
        if($parsed_args['min_price'] > 0 || $parsed_args['max_price'] > 0){
            $min_price = $parsed_args['min_price'];
            $max_price = $parsed_args['max_price'];
            $price_con = " AND (`post_meta`.`key` = '_price' AND CONVERT(`post_meta`.`value`, unsigned integer) >= $min_price AND CONVERT(`post_meta`.`value`, unsigned integer) <= $max_price)";
            if(!$max_price || $max_price == 0){
                $price_con = " AND (`post_meta`.`key` = '_price' AND CONVERT(`post_meta`.`value`, unsigned integer) >= $min_price)";
            }
        }
        if ($parsed_args['stock'] > 0) {
            $stock_con = " AND (`post_meta`.`key` = '_stock' AND `post_meta`.`value` = {$parsed_args['stock']})";
        }

        if ($parsed_args['special'] == 'on') {
            $special_con = " AND (`post_meta`.`key` = 'special' AND`post_meta`.`value` = 'on')";
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

        if ($parsed_args['orderby'] == 'date') {
            $parsed_args['orderby'] = '`post`.`post_date`';
        }else if($parsed_args['orderby'] == 'name'){
            $parsed_args['orderby'] = '`post`.`post_title`';
        }else if($parsed_args['orderby'] == 'price'){
            $parsed_args['orderby'] = 'CONVERT(`post_meta`.`value`, unsigned integer)';
            $price_con .= " AND `post_meta`.`key` = '_price'";
        }else if($parsed_args['orderby'] == 'stock'){
            $parsed_args['orderby'] = 'CONVERT(`post_meta`.`value`, unsigned integer)';
            $stock_con .= " AND `post_meta`.`key` = '_stock'";
        }else{
            $parsed_args['orderby'] = '`post`.`post_id`';
        }

        if ($parsed_args['post_status'] == 'publish') {
            $parsed_args['post_status'] = 'publish';
        }else if($parsed_args['post_status'] == 'draft'){
            $parsed_args['post_status'] = 'draft';
        }else{
            $parsed_args['post_status'] = 'publish';
        }
        if ($parsed_args['stock_status'] == 'instock') {
            $parsed_args['stock_status'] = "(`post_meta`.`key` = '_stock_status' AND `post_meta`.`value` = 'instock')";
        }else if($parsed_args['stock_status'] == 'outofstock'){
            $parsed_args['stock_status'] = "(`post_meta`.`key` = '_stock_status' AND `post_meta`.`value` = 'outofstock')";
        }else if($parsed_args['stock_status'] == 'call'){
            $parsed_args['stock_status'] = "(`post_meta`.`key` = '_stock_status' AND `post_meta`.`value` = 'call')";
        }else{
            $parsed_args['stock_status'] = 'true';
        }
        
        $post_query =
        "SELECT `post`.`post_id` FROM `post`
            INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id`
            $tag_join
            WHERE `post`.`post_type` = 'product'
            AND `post`.`post_status` = '{$parsed_args['post_status']}'
            AND {$parsed_args['stock_status']}
            $title_con
            $date_con
            $price_con
            $stock_con
            $special_con
            $tax_con
            $exclude_con
            GROUP BY `post`.`post_id`
            ORDER BY {$parsed_args['orderby']} {$parsed_args['order']}
            LIMIT {$parsed_args['skip_posts']}, {$parsed_args['numberposts']}";
        $posts = base::FetchArray($post_query);
        return $posts;
    }

    public function get_price()
    {
        // if (!$this->price || strlen($this->price) == 0) {
        //     $regular = $this->get_regular_price();
        //     $sale = $this->get_sale_price();
        //     if ($sale)
        //         $this->price = $sale;
        //     else
        //         $this->price = $regular;
        // }
        if (!$this->price || strlen($this->price) == 0) {
            $this->price = $this->get_meta('_price');
        }
        return $this->price;
    }


    
    public function set_price($new_price)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = '_price' AND `post_id` = {$this->post_id}");
        if (strlen($new_price) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'_price',{$this->price})");
        }
        $this->price = $new_price;
    }


    public function get_regular_price()
    {
        if (!$this->regular_price || strlen($this->regular_price) == 0) {
            $this->regular_price = $this->get_meta('_regular_price');
        }
        return $this->regular_price;
    }
    public function set_regular_price($new_regular_price)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = '_regular_price' AND `post_id` = {$this->post_id}");
        if (strlen($new_regular_price) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'_regular_price',{$this->regular_price})");
        }
        $this->regular_price = $new_regular_price;
    }



    public function get_sale_price()
    {
        if (!$this->sale_price || strlen($this->sale_price) == 0) {
            $this->sale_price = $this->get_meta('_sale_price');
        }
        return $this->sale_price;
    }
    public function set_sale_price($new_sale_price)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = '_sale_price' AND `post_id` = {$this->post_id}");
        if (strlen($new_sale_price) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'_sale_price',{$this->sale_price})");
        }
        $this->sale_price = $new_sale_price;
    }



    public function get_video_id()
    {
        if (!$this->video_id || strlen($this->video_id) == 0) {
            $this->video_id = $this->get_meta('video_id');
        }
        return $this->video_id;
    }
    public function set_video_id($new_video_id)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'video_id' AND `post_id` = {$this->post_id}");
        if (strlen($new_video_id) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'video_id',{$this->video_id})");
        }
        $this->video_id = $new_video_id;
    }



    public function get_video_cover_id()
    {
        if (!$this->video_cover_id || strlen($this->video_cover_id) == 0) {
            $this->video_cover_id = $this->get_meta('video_cover_id');
        }
        return $this->video_cover_id;
    }
    public function set_video_cover_id($new_video_cover_id)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'video_cover_id' AND `post_id` = {$this->post_id}");
        if (strlen($new_video_cover_id) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'video_cover_id',{$this->video_cover_id})");
        }
        $this->video_cover_id = $new_video_cover_id;
    }



    public function get_stock()
    {
        if (!$this->stock || strlen($this->stock) == 0) {
            $this->stock = $this->get_meta('_stock');
        }
        return $this->stock;
    }
    public function set_stock($new_stock)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = '_stock' AND `post_id` = {$this->post_id}");
        if (strlen($new_stock) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'_stock',{$this->stock})");
        }
        $this->stock = $new_stock;
    }



    public function get_restrict()
    {
        if (!$this->restrict || strlen($this->restrict) == 0) {
            $this->restrict = $this->get_meta('restrict');
        }
        return $this->restrict;
    }
    public function set_restrict($new_restrict)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'restrict' AND `post_id` = {$this->post_id}");
        if (strlen($new_restrict) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'restrict',{$this->restrict})");
        }
        $this->restrict = $new_restrict;
    }



    public function get_stock_status()
    {
        if (!$this->stock_status || strlen($this->stock_status) == 0) {
            $this->stock_status = $this->get_meta('_stock_status');
        }
        return $this->stock_status;
    }
    public function set_stock_status($new_stock_status)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'stock_status' AND `post_id` = {$this->post_id}");
        if (strlen($new_stock_status) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'stock_status','{$this->stock_status}')");
        }
        $this->stock_status = $new_stock_status;
    }



    public function get_special()
    {
        if (!$this->special || strlen($this->special) == 0) {
            $this->special = $this->get_meta('special');
        }
        return $this->special;
    }
    public function set_special($new_special)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'special' AND `post_id` = {$this->post_id}");
        if (strlen($new_special) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'special','{$this->special}')");
        }
        $this->special = $new_special;
    }



    public function get_consult()
    {
        if (!$this->consult || strlen($this->consult) == 0) {
            $this->consult = $this->get_meta('consult');
        }
        return $this->consult;
    }
    public function set_consult($new_consult)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'consult' AND `post_id` = {$this->post_id}");
        if (strlen($new_consult) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'consult','{$this->consult}')");
        }
        $this->consult = $new_consult;
    }



    public function get_complementaries()
    {
        if (!$this->complementaries || strlen($this->complementaries) == 0) {
            $this->complementaries = $this->get_meta('complementaries');
        }
        return $this->complementaries;
    }
    public function set_complementaries($new_complementaries)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'complementaries' AND `post_id` = {$this->post_id}");
        if (strlen($new_complementaries) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'complementaries','{$this->complementaries}')");
        }
        $this->complementaries = $new_complementaries;
    }



    public function get_replacements()
    {
        if (!$this->replacements || strlen($this->replacements) == 0) {
            $this->replacements = $this->get_meta('replacements');
        }
        return $this->replacements;
    }
    public function set_replacements($new_replacements)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'replacements' AND `post_id` = {$this->post_id}");
        if (strlen($new_replacements) > 0) {
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'replacements','{$this->replacements}')");
        }
        $this->replacements = $new_replacements;
    }



    public function get_tags()
    {
        if (!$this->tags || count($this->tags) == 0) {
            $this->tags = $this->get_taxonomy(product_tag);
        }
        return $this->tags;
    }
    public function set_tags($new_tags)
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



    public function get_cats()
    {
        if (!$this->cats || count($this->cats) == 0) {
            $this->cats = $this->get_taxonomy(product_cat);
        }
        return $this->cats;
    }
    public function set_cats($new_cats)
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



    public function get_brands()
    {
        if (!$this->brands || count($this->brands) == 0) {
            $this->brands = $this->get_taxonomy(product_brand);
        }
        return $this->brands;
    }
    public function set_brands($new_brands)
    {
        $brands = $this->get_brands();

        if (is_countable($brands) && count($brands) > 0) {
            foreach ($brands as $brand) :
                $id = $brand['tag_id'];
                if ($id != 0 || $id != null || strlen($id) > 0) {
                    $dlt_query = "DELETE FROM `tag_relationships` where `object_id` = {$this->post_id} AND `tag_id` = $id";
                    base::RunQuery($dlt_query);
                }
            endforeach;
        }
        foreach ($new_brands as $name => $id) :
            if ($id != 0 || $id != null || strlen($id) > 0) {
                $insert_query = "INSERT INTO `tag_relationships`(`object_id`, `tag_id`) VALUES ({$this->post_id} , $id)";
                base::RunQuery($insert_query);
            }
        endforeach;
    }



    // public function get_variable()
    // {
    //     $variables_query = "SELECT `tag`.`tag_id`,`tag`.`slug` FROM `tag`
    //     INNER JOIN `tag_relationships` ON `tag_relationships`.`tag_id` = `tag`.`tag_id`
    //     INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag_relationships`.`tag_id`
    //     WHERE `tag_meta`.`type` = 'pa'
    //     AND `tag_relationships`.`object_id` = {$this->post_id}";

    //     $variables = $this->FetchArray($variables_query);
    //     $cat_val = array();
    //     if (is_countable($variables) && count($variables) > 0) {
    //         foreach ($variables as $variable) {
    //             $cat_val += [$variable['tag_id'] => $variable['slug']];
    //         }
    //     }
    //     return $cat_val;
    // }


    public function get_url($format = null)
    {
        if (!$this->url || strlen($this->url) == 0) {
            $this->get_slug();
            $this->url = site_url.product_url.'/'.$this->post_slug;
        }
        return $this->url;
    }
}
