<?php
header('Content-Type: application/json');
include_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
$json = $_GET['json'];
$filters = json_decode($json, true);
$products = $functions->get_product(9, NULL, $prod_tag_id, 0);
$search_products = array();
$product_result = array();
$prods = array();
$fillter_tags = array();

foreach ($products as $product) :
    $res = 1;
    foreach ($filters as $filter) :
        if (isset($filters['other']) && $filter == $filters['other']) :
            if (strlen($filters['other']['name']) > 0)
                $name = $filters['other']['name'];
            else
                $name = "";
            if ($name == '' && $res != 0) :
                $res = 1;
            elseif (strpos($product['post_title'], $filters['other']['name']) !== false && $res != 0) :
                $res = 1;
            else :
                $res = 0;
                continue;
            endif;
        else :
            foreach ($filter as $f) :
                if ($f != 0) :
                    $tag_info_q = "SELECT * FROM `tag` `t` 
                    INNER JOIN `tag_meta` `tm` ON `t`.`tag_id` = `tm`.`tag_id` 
                    Where `t`.`tag_id` = $f";
                    $tag_info = $functions->FetchAssoc($tag_info_q);
                    $key = $tag_info['parent'] . " ";
                    $value = $tag_info['tag_id'];
                    $fillter_tags[$key] .= $value;
                    $tag_q = "SELECT * FROM `tag_relationships` `tr`
                    INNER JOIN `tag_meta` `tm` ON `tr`.`tag_id` = `tm`.`tag_id`
                    WHERE `tr`.`tag_id` = $f AND `tr`.`object_id` = " . $product['post_id'];
                    $tag = $functions->FetchAssoc($tag_q);
                endif;
                if ($res == 0) :
                    continue;
                elseif ($tag['object_id'] > 0 || $tag['object_id'] != null) :

                    $product = array_merge($product, array($key => $value));
                    $res = 1;
                elseif ($f == 0) :
                    $res = 1;
                elseif (!array_key_exists($key, $product)) :
                    $res = 0;
                endif;
            endforeach;
        endif;
    endforeach;
    if ($res == 1) :
        $meta_q = "SELECT * FROM `post_meta` WHERE `post_id` = " . $product['post_id'];

        $metas = $functions->Fetcharray($meta_q);
        $product_meta = array();
        foreach ($metas as $meta) :
            $key = $meta['key'];
            $value = $meta['value'];
            $product_meta = array_merge($product_meta, array($key => $value));
        endforeach;
        $search_products = array_merge($product, $product_meta);
        $prods[] = $search_products;
    endif;
endforeach;
foreach ($prods as $prod) :
    $final_product = array();
    $prod_code = $prod['post_id'];
    $meta_query = "SELECT `name`,`parent` FROM `tag` `t` 
    INNER JOIN `tag_relationships` `tr` ON `t`.`tag_id` = `tr`.`tag_id` 
    INNER JOIN `tag_meta` `tm` ON `t`.`tag_id` = `tm`.`tag_id` 
    Where `tr`.`object_id` = $prod_code";

    $meta_ha = $functions->FetchArray($meta_query);

    $img = $obj->display_post_image();
    $price = number_format($prod['_regular_price']);

    $price_off = number_format($prod['_sale_price']);
    $img_brand = "content/uploads/images/" . $functions->get_tag($prod['post_id'], 'product_attribute', 102)['icon'];


    $final_product = array_merge($final_product, array("id" => $prod['post_id']));
    $final_product = array_merge($final_product, array("title" => $prod['post_title']));
    $final_product = array_merge($final_product, array("guidm" => "/product/" . $prod['post_name']));
    $final_product = array_merge($final_product, array("guidh" => "/product/" . $prod['post_name']));
    $final_product = array_merge($final_product, array("image" => $img));
    $final_product = array_merge($final_product, array("price" => $price));
    $final_product = array_merge($final_product, array("price_off" => $price_off));
    if ($price_off > 0)
        $percent = (($price - $price_off) * 100) / $price;
    $final_product = array_merge($final_product, array("price_off_percent" => (int)$percent . "%"));
    $final_product = array_merge($final_product, array("stock" => $prod['stock']));
    foreach ($meta_ha as $meta) :
        $key = $meta['parent'] . " ";
        $value = $meta['name'];
        $final_product = array_merge($final_product, array($key => $value));
    endforeach;
    $product_result[] = $final_product;
endforeach;

if ($filters['other']['page'] == 0)
    $page = 1;
elseif (isset($filters['other']['page']))
    $page = $filters['other']['page'];
else
    $page = 1;
    
$count = 9;
$max = $page * $count;
$min = $max - $count;
for ($i = $min; $i < $max; $i++) {
    if ($product_result[$i]['id'] > 1) {
        $r_p['data'][] =  $product_result[$i];
    }
}
$r_p['size'] = count($product_result);
echo json_encode($r_p);
