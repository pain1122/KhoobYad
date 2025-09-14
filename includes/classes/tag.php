<?php
class tag extends Base
{
    protected $con;
    protected $tag_id, $count, $parent = 0;
    protected $name, $slug, $icon, $type, $description = "";

    function __CONSTRUCT($tag_identifier = "")
    {
        $this->con = $GLOBALS['con'];
        if (is_numeric($tag_identifier)) {
            $this->tag_id = $tag_identifier;
        } elseif (is_string($tag_identifier) && strlen($tag_identifier) > 0 &&  $tag_identifier != "new_tag") {
            $this->slug = urlencode(str_replace(' ', '-', trim($tag_identifier)));
            $this->tag_id = base::FetchAssoc("SELECT `tag_id` FROM `tag` WHERE `slug` = '{$this->slug}'")['tag_id'];
        }
        if ( $tag_identifier == "new_tag") {
            base::RunQuery("INSERT INTO `tag` (`name`,`slug`) VALUES ('','')");
            $this->tag_id = $this->con->insert_id;
            base::RunQuery("INSERT INTO `tag_meta` (`tag_id`,`type`) VALUES ('{$this->tag_id}','')");
        }
    }
    public function get_id()
    {
        return $this->tag_id;
    }


    static function get_taxonomies(array $args = null)
    {
        $defaults = [
            'type'              => blog_cat,
            'parent'            => null,
            'identicator'       => null,
            'query'             => '',
            'orderby'           => 'tag_id',
            'order'             => 'ASC'
        ];

        if (is_countable($args) && count($args) > 0)
            $parsed_args = array_merge($defaults, $args);
        else
            $parsed_args = $defaults;

        $tag_id = -1;
        $parent_id = -1;
        $type = $parsed_args['type'];
        $parent = $parsed_args['parent'];
        $taxonomy_query = $parsed_args['query'];
        if ($parsed_args['orderby'] == 'name') {
            $parsed_args['orderby'] = '`tag`.`name`';
        } else {
            $parsed_args['orderby'] = '`tag`.`tag_id`';
        }
        if (strlen($taxonomy_query) == 0) {
            if (is_numeric($parsed_args['identicator'])) {
                $tag_id = $parsed_args['identicator'];
            } elseif (is_string($parsed_args['identicator']) && strlen($parsed_args['identicator']) > 0) {
                $name = str_replace(' ', '-', trim($parsed_args['identicator']));
                $slug = urlencode($name);
                $tag_id = base::FetchAssoc("SELECT `tag`.`tag_id` FROM `tag` 
                INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` 
                WHERE (`tag`.`name` = '$name' || `tag`.`slug` = '$slug') 
                AND `tag_meta`.`type` = '$type'")['tag_id'];
            } else {
                $tag_id = 0;
            }

            if(! is_null($parent)){
				if (intval($parent) >= 0) {
					$parent_id = intval($parent);
				} elseif (is_string($parent) && strlen($parent) > 0) {
					$slug = urlencode(str_replace(' ', '-', $parent));
					$parent_id = base::FetchAssoc("SELECT `tag`.`tag_id` FROM `tag` 
					INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` 
					WHERE (`tag`.`name` = '$slug' || `tag`.`slug` = '$slug') AND `tag_meta`.`type` = '$type'")['tag_id'];
				}
			}

            $id_con = '';
            $parent_con = '';

            if ($tag_id > 0)
                $id_con = " AND `tag`.`tag_id` = $tag_id";

            if ($parent_id >= 0)
                $parent_con = " AND `tag_meta`.`parent` = $parent_id";


            $taxonomy_query = "SELECT `tag`.`name`,`tag`.`tag_id`,`tag`.`slug` FROM `tag`
            INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
            WHERE `tag_meta`.`type` = '$type'
            $parent_con
            $id_con
            GROUP BY `tag`.`tag_id`
            ORDER BY {$parsed_args['orderby']} {$parsed_args['order']}";
        }

        $taxonomies = base::FetchArray($taxonomy_query);
        return $taxonomies;
    }

    public function get_meta($key)
    {
        if ($key != "") {
            $post_meta_query = "SELECT `meta_value` FROM `tag_meta2` WHERE `type` = '$key' AND `tag_id` = {$this->tag_id}";
            $post_meta = base::FetchAssoc($post_meta_query);
        }
        if (!is_null($post_meta))
            return html_entity_decode($post_meta['meta_value'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
        else
            return "";
    }


    public function set_tag_meta($meta_array)
    {
        foreach ($meta_array as $key => $value) {
            base::RunQuery("DELETE FROM `tag_meta2` WHERE `tag_id` = {$this->tag_id} AND `type` = '$key'");
                base::RunQuery("INSERT INTO `tag_meta2`( `tag_id`, `type`, `meta_value`) 
                VALUES ({$this->tag_id},'$key','$value')");
        }
    }


    public function get_seo_title()
    {
        if (!$this->seo_title || strlen($this->seo_title) == 0) {
            $this->seo_title =$this->get_meta(seo_title_name);
        }
        if(empty($this->seo_title)){
            $this->get_type();
            if($this->type == blog_cat || $this->type == blog_tag)
                $this->seo_title = sprintf(base::get_option('articles_cat_seo'),$this->get_name());
            elseif($this->type == product_cat || $this->type == product_tag)
                $this->seo_title = sprintf(base::get_option('products_cat_seo'),$this->get_name());
        }
        return $this->seo_title;
    }



    public function get_seo_desc()
    {
        if (!$this->seo_desc || strlen($this->seo_desc) == 0) {
            $this->seo_desc =$this->get_meta(seo_desc_name);
        }
        if(empty($this->seo_title)){
            $this->get_type();
            if($this->type == blog_cat || $this->type == blog_tag)
                $this->seo_title = sprintf(base::get_option('articles_cat_desc'),$this->get_name());
            elseif($this->type == product_cat || $this->type == product_tag)
                $this->seo_title = sprintf(base::get_option('products_cat_desc'),$this->get_name());
        }
        return $this->seo_desc;
    }



    public function get_seo_keywords()
    {
        if (!$this->seo_keywords || strlen($this->seo_keywords) == 0) {
            $this->seo_keywords =$this->get_meta(seo_keywords_name);
        }
        return $this->seo_keywords;
    }

    public function get_name()
    {
        if (!$this->name || strlen($this->name) == 0) {
            $this->name = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` = {$this->tag_id}")['name'];
        }
        return $this->name;
    }
    public function set_name($new_name)
    {
        $this->name = $new_name;
        base::RunQuery("UPDATE `tag` SET `name` = '{$this->name}' WHERE `tag_id` = {$this->tag_id}");
    }

    public function get_slug()
    {
        if (!$this->slug || strlen($this->slug) == 0) {
            $this->slug = base::fetchassoc("SELECT `slug` FROM `tag` WHERE `tag_id` = {$this->tag_id}")['slug'];
        }
        return $this->slug;
    }
    public function set_slug($new_slug)
    {
        $this->slug = $new_slug;
        base::RunQuery("UPDATE `tag` SET `slug` = '{$this->slug}' WHERE `tag_id` = {$this->tag_id}");
    }

    public function get_type()
    {
        if (!$this->type || strlen($this->type) == 0) {
            $this->type = base::FetchAssoc("SELECT `type` FROM `tag_meta` WHERE `tag_id` = {$this->tag_id}")['type'];
        }
        return $this->type;
    }
    public function set_type($new_type)
    {
        $this->type = $new_type;
        base::RunQuery("UPDATE `tag_meta` SET `type` = '{$this->type}' WHERE `tag_id` = {$this->tag_id}");
    }

    public function get_description()
    {
        if (!$this->description || strlen($this->description) == 0) {
            $this->description = base::FetchAssoc("SELECT `description` FROM `tag_meta` WHERE `tag_id` = {$this->tag_id}")['description'];
        }
        return html_entity_decode($this->description,ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    }
    public function set_description($new_description)
    {
        $this->description = $new_description;
        base::RunQuery("UPDATE `tag_meta` SET `description` = '{$this->description}' WHERE `tag_id` = {$this->tag_id}");
    }



    public function get_parent()
    {
        if (!$this->parent || strlen($this->parent) == 0) {
            $this->parent = base::FetchAssoc("SELECT `parent` FROM `tag_meta` WHERE `tag_id` = {$this->tag_id}")['parent'];
        }
        return $this->parent;
    }
    public function set_parent($new_parent)
    {
        $this->parent = $new_parent;
        base::RunQuery("UPDATE `tag_meta` SET `parent` = {$this->parent} WHERE `tag_id` = {$this->tag_id}");
    }



    public function get_count()
    {
        if (!$this->count || strlen($this->count) == 0) {
            $this->count = base::FetchAssoc("SELECT `count` FROM `tag_meta` WHERE `tag_id` = {$this->tag_id}")['count'];
        }
        return $this->count;
    }
    public function set_count($new_count)
    {
        $this->count = $new_count;
        base::RunQuery("UPDATE `tag_meta` SET `count` = {$this->count} WHERE `tag_id` = {$this->tag_id}");
    }

    public function get_url($format = null)
    {
        if(empty($format)){
            if (!$this->url || strlen($this->url) == 0) {
                $this->get_slug();
                $this->get_type();
                $this->url = site_url.$this->type.'/'.$this->slug;
            }
        }else{
            $this->url = $format;
        }
        return $this->url;
    }
}
