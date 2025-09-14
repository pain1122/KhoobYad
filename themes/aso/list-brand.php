<?php

include_once('header.php');

?>
<section class="list-brand">
    <div class="container">
        <?php
        $brand_query = "SELECT * FROM `tag_meta` `tm`
        INNER JOIN `tag` `t` ON `tm`.`tag_id` = `t`.`tag_id`
        WHERE `tm`.`parent` = 2328";
        $brand_icon = $functions->FetchArray($brand_query);
        for ($i = 0; $i < count($brand_icon); $i++) :
        ?>
            <div class="brand-img-item">
                <a href="/tag/<?php echo $brand_icon[$i]['slug'] ?>">
                    <img src="content/uploads/images/<?php echo $brand_icon[$i]['icon'] ?>" alt="<?php echo $brand_icon[$i]['slug']; ?>">
                </a>
            </div>
        <?php endfor; ?>
    </div>
</section>
<?php include_once('footer.php'); ?>