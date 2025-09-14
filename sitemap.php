<?php
Header('Content-type: text/xml');
include_once('includes/config.php');
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
error_reporting(~E_ALL);

function get_tag_meta_key($tag_id, $key, $con)
{
    $query = "SELECT * FROM `tag_meta2` WHERE tag_id = $tag_id AND `type` = '$key';";
    $res = Base::FetchAssoc($query, $con);
    return $res['meta_value'];
}

$post_array = [];
$get_posts = "SELECT * FROM `post`
inner join `post_meta` on `post`.`post_id` = `post_meta`.`post_id`
 WHERE (`post_type` = 'product' OR `post_type` = 'post')
 AND (`post_status` = 'publish' OR `post_meta`.`key` = 'noindex') 
 GROUP BY `post`.`post_id`";

$posts_url = Base::fetcharray($get_posts);
foreach ($posts_url as $post_url) {
    if ($post_url['noindex'] != 'on') {
        if ($post_url['post_type'] == "post")
            $url = "https://" . $_SERVER['SERVER_NAME'] . "/" . $post_url['post_name'];
        else
            $url = "https://" . $_SERVER['SERVER_NAME'] . "/product/" . $post_url['post_name'];

        $array = [
            "url" => $url,
            "lastmod" => $post_url['modify_date']
        ];
        array_push($post_array, $array);
    }
}

$get_tags = "SELECT *,`tag`.`tag_id` as `tid` FROM `tag` 
inner join `tag_meta` on `tag`.`tag_id` = tag_meta.tag_id 
WHERE `type` = 'product_cat' OR `type` = 'product_tag' OR `type` = 'category'";

$tags_url = Base::fetcharray($get_tags);
foreach ($tags_url as $tag_url) {
    if (get_tag_meta_key($tag_url['tid'], 'noindex', $con) != "on") {
        if ($tag_url['type'] == "product_cat")
            $url = "https://" . $_SERVER['SERVER_NAME'] . "/product-category/" . $tag_url['slug'];
        else if ($tag_url['type'] == "product_tag")
            $url = "https://" . $_SERVER['SERVER_NAME'] . "/product-tag/" . $tag_url['slug'];
        else if ($tag_url['type'] == "category")
            $url = "https://" . $_SERVER['SERVER_NAME'] . "/category/" . $tag_url['slug'];

        $array = [
            "url" => $url,
            "lastmod" => $tag_url['modify_date']
        ];
        array_push($post_array, $array);
    }
}

?>

<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
foreach ($post_array as $post) :
    echo "<url>";
    if (strlen($post['url']) > 0)
        echo "<loc>".$post['url']."</loc>";
    if (strlen($post['lastmod']) > 0)
    echo "<lastmod>".gmdate('Y-m-d\TH:i:s+00:00', strtotime($post['lastmod']))."</lastmod>";
    else
    echo "<lastmod>".gmdate('Y-m-d\TH:i:s+00:00', strtotime(date("D M d, Y G:i")))."</lastmod>";
    
?>

<?php
echo "</url>";
endforeach;
?>
</urlset>

