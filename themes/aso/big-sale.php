<?php

include_once('header.php');
$offers = $functions->get_option('offers');
?>
<section class="list-product">
    <div class="container">
        <div class="contact-list">
            <?php if ($offers) : ?>
                <div class="row">
                    <?php
                    $offers = explode(',',$offers);
                    foreach ($offers as $product) :
                        $product = new product($product);
                        echo '<div class="col-6 col-sm-4 col-lg-3 mb-4">';
                            include('product-part.php');
                        echo '</div>';
                    endforeach; ?>
                </div>
            <?php else : ?>
                <h3><?php echo $functions->get_language($_SESSION['lang'],'no_offers_at_the_moment'); ?></h3>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include_once('footer.php'); ?>