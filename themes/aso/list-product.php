<?php
include_once('header.php');
if ($id > 0) {
    $newid = $id;
} else {
    $newid = '';
}
if ($newid > 0) {
    $highest = $functions->FetchAssoc("SELECT CONVERT(`post_meta`.`value`,unsigned integer) as `price` FROM `post_meta` INNER JOIN `tag_relationships` ON `tag_relationships`.`object_id` = `post_meta`.`post_id` WHERE `post_meta`.`key` = '_price' AND `tag_relationships`.`tag_id` = $newid GROUP BY `post_meta`.`post_id` ORDER BY `price` DESC")['price'];
} else {
    $highest = $functions->Fetchassoc("SELECT MAX(CONVERT(`value`, unsigned integer)) as `max` FROM `post_meta` WHERE `key` = '_price';")['max'];
}
$categories = $functions->FetchArray("SELECT `tag`.`tag_id` FROM `tag` INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` WHERE `parent` = $newid AND `type` = 'product_cat' GROUP BY `tag`.`slug`");
$base = pow(10, strlen($highest) - 2);
$highest = round(($highest / $base) + 0.5) * $base;
if (strlen($_GET['name']) > 0)
    $name = $_GET['name']; ?>
<section class="category">
    <div class="container">
        <div class="row">
            <div id="sidebar" class="col-lg-3 order-lg-1 <?php if ($is_brand == true) echo 'd-none'; ?>">
                <div class="sidebar">
                    <span class="close"></span>
                    <form id="filter-form">
                        <div class="attribut widget-side">
                            <label class="col-12 col-form-label label-sidebar widget-title"><?php echo $functions->get_language($_SESSION['lang'], 'shop_search_title'); ?></label>
                            <div class="attr">
                                <input type="text" value="<?php echo $name; ?>" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'shop_search_placeholder'); ?>" id="name">
                                <div slider id="slider-distance">
                                    <div>
                                        <div inverse-left style="width:70%;"></div>
                                        <div inverse-right style="width:70%;"></div>
                                        <div range style="left:0%;right:0%;"></div>
                                        <span thumb style="right:0%;"></span>
                                        <span thumb style="right:calc(100% - 10px);"></span>
                                        <div sign style="right:0%;">
                                            <span id="value">0</span>
                                        </div>
                                        <div sign style="left:0;">
                                            <span id="value"><?php echo $highest; ?></span>
                                        </div>
                                    </div>
                                    <input type="range" id="min" value="0" max="<?php echo $highest; ?>" min="0" step="<?php echo $highest / 100; ?>" oninput="this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);let value = (this.value/parseInt(this.max))*100;var children = this.parentNode.childNodes[1].childNodes;children[3].style.width=value+'%';children[5].style.right=value+'%';children[7].style.right=value+'%';children[11].childNodes[1].innerHTML=this.value;" />
                                    <input type="range" id="max" value="<?php echo $highest; ?>" max="<?php echo $highest; ?>" min="0" step="<?php echo $highest / 100; ?>" oninput="this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));let value = (this.value/parseInt(this.max))*100;var children = this.parentNode.childNodes[1].childNodes;children[1].style.width=(100-value)+'%';children[5].style.left=(100-value)+'%';children[9].style.right= 'calc('+value+'% - 10px)';children[13].childNodes[1].innerHTML=this.value;" />
                                </div>
                                <div class="price-slider-amount">
                                    <button name="submit" type="button" class="button" onclick="Show(document.getElementById('name').value , document.getElementById('min').value,  document.getElementById('max').value,tags,page,sort)"><?php echo $functions->get_language($_SESSION['lang'], 'shop_search_submit'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php if (is_countable($categories) && count($categories) > 0) { ?>
                        <div class="attribut widget-side">
                            <label class="col-12 col-form-label label-sidebar widget-title"><?php echo $functions->get_language($_SESSION['lang'], 'shop_category_title'); ?></label>
                            <div class="attr">
                                <select class="form-control js-example-basic-single" onchange="window.location.href = '<?php echo site_url; ?>product-category/'+this.value">
                                    <option>انتخاب دسته بندی</option>
                                    <?php foreach ($categories as $cat) {
                                        $obj = new tag($cat['tag_id']);
                                        $slug = $obj->get_slug();
                                        $name = $obj->get_name();
                                        echo "<option value='$slug'>$name</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <?php } else {
                        //show product attributes
                        $get_products = $functions->Fetcharray("SELECT `object_id` FROM `tag_relationships` 
                        WHERE `tag_id` = $newid");
                        $object_ids = $attr_ids = '0';
                        foreach ($get_products as $product) {
                            $object_ids .= ',' . $product['object_id'];
                        }
                        $product_attributes = "SELECT `tag`.`tag_id`,`tag`.`slug`,`tag`.`name` FROM `tag`  
                        INNER JOIN `tag_relationships` ON `tag_relationships`.`tag_id` = `tag`.`tag_id`
                        INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
                        WHERE `tag_relationships`.`object_id` IN ($object_ids) AND `tag_meta`.`type` LIKE 'pa_%'
                        GROUP BY `tag`.`tag_id`";
                        $pas = $functions->FetchArray($product_attributes);
                        if (is_countable($pas) && count($pas) > 0) {
                        ?>
                            <div class="attribut widget-side">
                                <label class="col-12 col-form-label label-sidebar widget-title"><?php echo $functions->get_language($_SESSION['lang'], 'shop_filter_title'); ?></label>
                                <div class="attr" style="display:none;">
                                    <?php
                                    foreach ($pas as $attr) {
                                    ?>
                                        <label><input onclick="addfilter(this); Show(name = document.getElementById('name').value, min = document.getElementById('min').value, max = document.getElementById('max').value,tags,page=1,sort='new');" class="checkboxes" type="checkbox" name="<?php echo  $attr['tag_id'] ?>" id="cat-<?php echo $attr['slug'] ?>" value="<?php echo $attr['tag_id']; ?>"> <?php echo $attr['name']; ?></label>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <i></i>
                            </div>
                    <?php
                        }
                    } ?>
                </div>
            </div>

            <div class="col order-lg-2 position-relative">
                <div class="mb-5 d-flex align-items-center">
                    <span class="filter-show mb-0"><?php echo $functions->get_language($_SESSION['lang'], 'shop_advanced_filter'); ?></span>
                    <select class="form-control js-example-basic-single" id="sort" onchange="Show(document.getElementById('name').value, document.getElementById('min').value, document.getElementById('max').value, tags, page,document.getElementById('sort').value)">
                        <option value="price_desc"><?php echo $functions->get_language($_SESSION['lang'], 'shop_filter_exspensive'); ?></option>
                        <option value="price_asc"><?php echo $functions->get_language($_SESSION['lang'], 'shop_filter_cheep'); ?></option>
                        <option selected value="new"><?php echo $functions->get_language($_SESSION['lang'], 'shop_filter_new'); ?></option>
                        <option value="old"><?php echo $functions->get_language($_SESSION['lang'], 'shop_filter_old'); ?></option>
                    </select>
                </div>
                <div id="product_item" class="product_item">
                    <div class="row">
                    </div>
                </div>
                <div class="notif">
                    <p id="error"></p>
                </div>
                <ul class="cutome-pagination" id="pagination">
                </ul>
            </div>
        </div>
        <?php if (strlen($content) > 0) { ?>
            <div class="cat-meta mb-5">
                <div class="hideContent body-post">
                    <?php
                    echo $content;
                    ?>
                </div>
                <div class="show-more">
                    <span><?php echo $functions->get_language($_SESSION['lang'], 'shop_content_more'); ?></span>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?php include_once('footer.php'); ?>
<script>
    $(".show-more span").on("click", function() {
        var $this = $(this);
        var $content = $this.parent().prev("div.body-post");
        var linkText = $this.text();
        if (linkText === "<?php echo $functions->get_language($_SESSION['lang'], 'shop_content_more'); ?>") {
            linkText = "<?php echo $functions->get_language($_SESSION['lang'], 'shop_content_fewer'); ?>";
            $content.css({
                "max-height": "5000px",
                "padding-bottom": "20px"
            });
        } else {
            linkText = "<?php echo $functions->get_language($_SESSION['lang'], 'shop_content_more'); ?>";
            $content.css({
                "max-height": "300px",
                "padding-bottom": "0px"
            });
        };

        $this.text(linkText);
    });

    function separate(Number) {
        Number += '';
        Number = Number.replace(',', '');
        x = Number.split('.');
        y = x[0];
        z = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(y))
            y = y.replace(rgx, '$1' + ',' + '$2');
        return y + z;
    }

    var page = 1;
    var pages = 1;
    var pagenavigation = page;
    var tags = [<?php echo $newid ?>];
    var sort = 'new';

    function addfilter(input) {
        var checkboxes = document.getElementsByClassName("checkboxes");
        tags = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked)
                tags.push(checkboxes[i].value);
        }
        page = 1;
    }

    function Show(name = '', min = 0, max = 0, tags = [], page = 1, sort = 'new') {

        getJSON('/themes/aso/includes/API/v1/List-Product.php?name=' + name + "&min=" + min + "&max=" + max + "&tags=" + tags + "&page=" + page + "&sort=" + sort, function(err, data) {
            var error = document.getElementById("error");
            var allpages = data['pages'];
            $('#product_item .row').html('');
            if (data['products'].length > 0) {
                error.innerText = '';
                for (var i = 0; i < data['products'].length; i++) {
                    var post_id = parseInt(data['products'][i]['post_id']);
                    var tag = data['products'][i]['tag'];
                    var product_variable = data['products'][i]['product_type'];
                    var title = data['products'][i]['post_title'];
                    var image = data['products'][i]['img'];
                    var url = data['products'][i]['url'];
                    var price_off = parseInt(data['products'][i]['_sale_price']);
                    var price = parseInt(data['products'][i]['_regular_price']);
                    if (price_off > 0 && price < 1) {
                        price = price_off;
                        price_off = 0;
                    }
                    if (price < 1)
                        price = 0;

                    var sale = '';
                    var cart = '';
                    var button = '<span>' + separate(price) + ' <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span>';
                    if (price_off > 0) {
                        var off = ((price - price_off) * 100) / price
                        var percent = off.toFixed(0);
                        button = '<del>' + separate(price) + ' <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></del><ins>' + separate(price_off) + ' <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></ins>';
                        sale = '<div class="sale"><span>%' + percent + '</span> <?php echo $functions->get_language($_SESSION['lang'], 'off'); ?></div>';
                    }
                    if (price < 1)
                        button = '';
                    if (product_variable == 'product_variable') {
                        cart = "<a href='" + url + "' class='button'><?php echo $functions->get_language($_SESSION['lang'], 'view_product'); ?></a>";
                    } else {
                        cart = '<span class="button" onclick="addToCart(' + post_id + ')"><?php echo $functions->get_language($_SESSION['lang'], 'add_to_cart'); ?></span>';
                        var stock_status = data['products'][i]['stock_status'];
                        if (stock_status == 'outofstock')
                            cart = '<span class="button disabled"><?php echo $functions->get_language($_SESSION['lang'], 'outofstock'); ?></span>';
                        if (stock_status == 'call')
                            cart = '<a href="tel:<?php echo $functions->get_option('phone'); ?>" class="button phone"><?php echo $functions->get_language($_SESSION['lang'], 'product_call'); ?></a>';
                    }
                    $('.product_item .row').append('<div class="col-6 col-lg-4 mb-4"><div class="product-card"><div class="pc-header">' + sale + '<i class="heart" onclick="addToFavorites(' + post_id + ')" value=""></i></div><a href="' + url + '"><img loading="lazy" src="' + image + '" alt="' + title + '" /></a><div><a href="' + url + '"><h4 title="' + title + '">' + title.substr(0, 50) + '...' + '</h4></a><div class="price-card">' + button + '</div>' + cart + '</div></div></div>');


                    var wishlist = <?php echo json_encode($_SESSION['wishlist']); ?>;
                    var arrWishlist = Object.values(wishlist);

                    if (arrWishlist.includes(post_id)) {
                        document.getElementsByClassName('heart')[i].classList.add('wished');
                    }
                }
                var pagin = "";
                var first_dots = second_dots = false;
                for (pages = 1; pages <= allpages; pages++) {
                    if (pages > 2 && pages < page - 2) {
                        if (first_dots == false)
                            pagin += "<span class='first-dots'>...</span>";
                        first_dots = true;
                    } else if (pages == page) {
                        pagin += "<li><span class='active'>" + page + "</span></li>";
                    } else if (pages > page + 2 && pages < allpages - 1) {
                        if (second_dots == false)
                            pagin += "<span class='second-dots'>...</span>";
                        second_dots = true;
                    } else {
                        pagin += "<li><span onclick=\"Show(document.getElementById(\'name\').value, document.getElementById(\'min\').value, document.getElementById(\'max\').value, tags, " + pages + ", sort)\">" + pages + "</span></li>";
                    }
                }
                document.getElementById('pagination').innerHTML = pagin;
                $('.pc-header .heart').click(function() {
                    if ($(this).hasClass('wished')) {
                        $(this).removeClass('wished');
                    } else {
                        $(this).addClass('wished');
                    }
                });
            } else {
                error.innerText = "<?php echo $functions->get_language($_SESSION['lang'], 'product_not_found') ?>";
            }
        });
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0;
    }

    function next() {
        page++;
        Show(document.getElementById('name').value, document.getElementById('min').value, document.getElementById('max').value, tags, page, sort)
    }

    function prev() {
        if (page > 1) {
            page--;
            Show(document.getElementById('name').value, document.getElementById('min').value, document.getElementById('max').value, tags, page, sort)
        }
    }

    Show(document.getElementById('name').value, min = 0, max = 0, tags = tags, page, sort);
    var getJSON = function(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'json';
        xhr.onload = function() {
            var status = xhr.status;
            if (status === 200) {
                products = [];
                callback(null, xhr.response);
            } else {
                callback(status, xhr.response);
            }
        };

        xhr.send();
    };
</script>