<?php
$type = $_GET['type'];
if (isset($_GET['delete']) && $role == 'admin') {
    $post_id = $_GET['delete'];
    base::RunQuery("UPDATE `post` SET `post_type` = '$type-deleted' WHERE `post_id` = " . $post_id);
}
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<?php if ($role == 'student') : ?>
    <?php
    if ($id > 0) {
        $newid = $id;
    } else {
        $newid = 0;
    }
    $cateogory_type = str_replace('-','_',$type).'_category';
    $categories = $functions->FetchArray("SELECT `tag`.`tag_id` FROM `tag` INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` WHERE `type` = '$cateogory_type' GROUP BY `tag`.`slug`");
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
                                <select class="form-control js-example-basic-single" id="tags">
                                    <option>انتخاب</option>
                                    <?php foreach ($categories as $cat) {
                                        $obj = new tag($cat['tag_id']);
                                        $slug = $obj->get_slug();
                                        $name = $obj->get_name();
                                        $id = $cat['tag_id'];
                                        echo "<option value='$id'>$name</option>";
                                    } ?>
                                </select>
                            </label>
                        <?php } ?>
                        <button type="button" class="btn btn-primary mr-3 col-1" onclick="Show(document.getElementById('name').value,document.getElementById('tags').value,page,document.getElementById('sort').value)">فیلتر</button>
                        <label class="form-label mr-auto mb-0 col-3">مرتب سازی
                            <select class="form-control js-example-basic-single" id="sort" onchange="Show(document.getElementById('name').value, tags, page,$(this).val())">
                                <option value="price_desc">ارزان ترین</option>
                                <option value="price_asc">گران ترین</option>
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
            getJSON('API/v1/List-Product.php?type=<?php echo $type; ?>&name=' + name + "&tags=" + tags + "&page=" + page + "&sort=" + sort + "&uid=" + user_id, function(err, data) {
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
                        var description = data['products'][i]['description'];
                        var image = data['products'][i]['img'];
                        var price_off = parseInt(data['products'][i]['_sale_price']);
                        var price = parseInt(data['products'][i]['_regular_price']);
                        var owned = data['products'][i]['owned'];
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
                        if (price < 1 || owned == 'true'){
                            button = '<a href="?page=product/view.php&id=' + post_id + '" class="btn btn-primary w-100"><i class="fas fa-gift me-2"></i>رایگان</a>';
                            if(owned == 'true')
                                button = '<a href="?page=product/view.php&id=' + post_id + '" class="btn btn-primary w-100"><i class="fa fa-eye me-2"></i>مشاهده</a>';
                        }else{
                            cart = '<span class="btn btn-primary button" onclick="addToCart(' + post_id + ')"><i class="fas fa-shopping-cart me-2"></i><?php echo $functions->get_language($_SESSION['lang'], 'add_to_cart'); ?></span>';
                            var stock_status = data['products'][i]['stock_status'];
                            if (stock_status == 'outofstock')
                                cart = '<span class="btn btn-primary button disabled"><?php echo $functions->get_language($_SESSION['lang'], 'outofstock'); ?></span>';
                            if (stock_status == 'call')
                                cart = '<a href="tel:<?php echo $functions->get_option('phone'); ?>" class="button phone"><?php echo $functions->get_language($_SESSION['lang'], 'product_call'); ?></a>';
                        }
                        $('.product_item .row').append('<div class="col-6 col-lg-4 mb-4"><div class="product-card"><div class="pc-header">' + sale + '</div><a class="d-block w-100" href="?page=product/view.php&id=' + post_id + '"><img loading="lazy" src="' + image + '" alt="' + title + '" /></a><div><a href="?page=product/view.php&id=' + post_id + '"><h4 title="' + title + '">' + title + '</h4></a><p>' + description +'</p><div class="price-card">' + button + '</div>' + cart + '</div></div></div>');
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
<?php else : ?>
    <div class="card">
        <h5 class="card-header">همه محصولات</h5>
        <div class="card-datatable text-nowrap overflow-auto">
            <table class="datatables-posts table table-bordered">
                <thead>
                    <tr>
                        <th>آیدی</th>
                        <th>نام کامل</th>
                        <th>وضعیت</th>
                        <th>نویسنده</th>
                        <th>تاریخ</th>
                        <th>قیمت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php endif; ?>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>

<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script>
    $(window).resize(function() {
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }

    });
    $(window).ready(function() {
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }
        // Variable declaration for table
        var dt_user_table = $('.datatables-posts');

        // Users datatable
        if (dt_user_table.length) {
            var dt_user = dt_user_table.DataTable({
                processing: true,
                serverSide: true,
                select: true,
                ajax: {
                    url: 'API/v1/GetProducts.php?type=<?php echo $type; ?>'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'post_id'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'author'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'stock'
                    },
                    {
                        data: 'op'
                    }
                ],
                columnDefs: [{
                        // For Responsive
                        // className: 'control',
                        targets: 0,
                        render: function(data, type, full, meta) {
                            var $id = full['post_id'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                        }
                    },
                    {
                        // User full name 
                        targets: 1,
                        responsivePriority: 4,
                        render: function(data, type, full, meta) {
                            var $name = full['title'],
                                $id = full['post_id'];
                            var $row_output =
                                '<a href="index.php?page=product/add-<?php echo $type; ?>.php&id=' + $id + '">' +
                                '<span class="fw-semibold">' +
                                $name +
                                '</span>' +
                                '</a>';
                            return $row_output;
                        }
                    },
                    {
                        // status
                        targets: 2,
                        render: function(data, type, full, meta) {
                            var $status = full['status'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $status + '</span>';
                        }
                    },
                    {
                        // author
                        targets: 3,
                        render: function(data, type, full, meta) {
                            var $status = full['author'];
                            return '<span class="badge bg-label-warning">' + $status + '</span>';
                        }
                    },
                    {
                        // date
                        targets: 4,
                        render: function(data, type, full, meta) {
                            var $date = full['date'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $date + '</span>';
                        }
                    },
                    {
                        // stock
                        targets: 5,
                        render: function(data, type, full, meta) {
                            var $stock = full['stock'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $stock + '</span>';
                        }
                    },
                    {
                        // op
                        targets: 6,
                        title: 'عملیات',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var $op = full['op'];
                            return $op;
                        }
                    }
                ],
                order: [
                    [0, 'DESC']
                ],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'جزئیات ' + data['title'];
                            }
                        }),
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ?
                                    '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
            });
        }
    })
</script>