<?php
if ($id > 0) {
    $newid = $id;
} else {
    $newid = 0;
}
$type = $_GET['type'];
$cateogory_type = str_replace('-', '_', $type) . '_category';
$categories = $functions->FetchArray("SELECT `tag`.`tag_id` FROM `tag` 
INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` 
WHERE `type` = '$cateogory_type' GROUP BY `tag`.`slug`");
if (strlen($_GET['name']) > 0)
    $name = $_GET['name']; ?>
<section class="category">
    <div class="container">
        <div class="row">
            <div class="col-12 position-relative">
                <div class="mb-5 row align-items-end">
                    <label class="form-label mb-0 col-3">جستوجو نام
                        <input type="text" class="form-control" value="<?php echo $name; ?>" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'shop_search_placeholder'); ?>" id="name">
                    </label>
                    <?php if (is_countable($categories) && count($categories) > 0) { ?>
                        <label class="form-label mr-3 mb-0 col-3">دسته بندی
                            <select class="form-control js-example-basic-single">
                                <option>انتخاب</option>
                                <?php foreach ($categories as $cat) {
                                    $obj = new tag($cat['tag_id']);
                                    $slug = $obj->get_slug();
                                    $name = $obj->get_name();
                                    echo "<option value='$slug'>$name</option>";
                                } ?>
                            </select>
                        </label>
                    <?php } ?>
                    <button type="button" class="btn btn-primary mr-3 col-1" onclick="Show(document.getElementById('name').value,tags,page,sort)">فیلتر</button>
                    <label class="form-label mr-auto mb-0 col-3">مرتب سازی
                        <select class="form-control js-example-basic-single" id="sort" onchange="Show(document.getElementById('name').value, tags, page,document.getElementById('sort').value)">
                            <option selected value="new">جدید ترین</option>
                            <option value="old">قدیمی ترین</option>
                        </select>
                    </label>
                </div>
                <div id="product_item" class="product_item">
                    <div class="row">
                    </div>
                </div>
                <div class="notif">
                    <p id="error"></p>
                </div>
                <div class="cutome-pagination" id="pagination">
                </div>
            </div>
        </div>
    </div>
</section>
<script>
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
    var user_id = <?php echo $uid; ?>;

    function Show(name = '', tags = [], page = 1, sort = 'new') {
        getJSON('API/v1/My-Product.php?type=<?php echo $type; ?>&name=' + name + "&tags=" + tags + "&page=" + page + "&sort=" + sort + "&user_id=" + user_id, function(err, data) {
            var error = document.getElementById("error");
            var allpages = data['pages'];
            $('#product_item .row').html('');
            if (data['products'].length > 0) {
                error.innerText = '';
                for (var i = 0; i < data['products'].length; i++) {
                    var post_id = parseInt(data['products'][i]['post_id']);
                    var title = data['products'][i]['post_title'];
                    var description = data['products'][i]['description'];
                    var image = data['products'][i]['img'];
                    $('.product_item .row').append('<div class="col-6 col-lg-4 mb-4"><div class="product-card"><a class="d-block w-100" href="?page=product/view.php&id=' + post_id + '"><img loading="lazy" src="' + image + '" alt="' + title + '" /></a><div><a href="?page=product/view.php&id=' + post_id + '"><h4 title="' + title + '">' + title + '</h4></a><p>' + description + '</p></div></div></div>');
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
                        pagin += "<li><span onclick=\"Show(document.getElementById(\'name\').value, tags, " + pages + ", sort)\">" + pages + "</span></li>";
                    }
                }
                document.getElementById('pagination').innerHTML = pagin;
            } else {
                error.innerText = "<?php echo $functions->get_language($_SESSION['lang'], 'product_not_found') ?>";
            }
        });
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0;
    }
    Show(document.getElementById('name').value, tags = tags, page, sort);
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