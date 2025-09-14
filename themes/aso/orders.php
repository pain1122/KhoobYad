<?php
include_once('header.php');
if (isset($_POST['user_info'])) {
    $user->set_nick_name($_POST['firstname'] . ' ' . $_POST['lastname']);
    $user->set_user_email($_POST['email']);
    $metas = [
        'state'         => "{$_POST['providence']}",
        'city'          => "{$_POST['city']}",
        'address'       => "{$_POST['address']}",
        'phonenumber'   => "{$_POST['phone_number']}",
        'birth'         => "{$_POST['birthday']}",
        'gender'        => "{$_POST['gender']}",
        'firstname'     => "{$_POST['firstname']}",
        'lastname'      => "{$_POST['lastname']}",
    ];
    $user->insert_user_meta($metas);
}
$username = $user->get_nick_name();
$email = $user->get_user_email();
$providence = $user->get_user_meta('state');
$city = $user->get_user_meta('city');
$address = $user->get_user_meta('address');
$phone_number = $user->get_user_meta('phonenumber');
$birthday = $user->get_user_meta('birth');
$gender = $user->get_user_meta('gender');
$firstname = $user->get_user_meta('firstname');
$lastname = $user->get_user_meta('lastname');
$wishlist_query = "SELECT `value` as `post_id` FROM `user_meta` WHERE `user_id` = '$user_id' AND `key` = 'wishlist';";
$wishlist = $functions->FetchArray($wishlist_query);
?>
<link rel="stylesheet" href="/themes/aso/includes/assets/css/boxicons.css">
<link rel="stylesheet" href="/themes/aso/includes/assets/css/datatables.bootstrap5.css">
<link rel="stylesheet" href="/themes/aso/includes/assets/css/responsive.bootstrap5.css">
<link rel="stylesheet" href="/themes/aso/includes/assets/css/flatpickr.css">
<div class="container light-style">
    <div class="row gy-4">
        <div class="col-xl-4 col-lg-5">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="user-avatar-section">
                                <div class="d-flex align-items-center flex-column">
                                    <div class="user-info text-center">
                                        <h5 class="mb-2"><?php echo $username; ?></h5>
                                    </div>
                                </div>
                            </div>
                            <h5 class="pb-2 border-bottom mb-4 secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'account_info'); ?></h5>
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_email'); ?></span>
                                        <span><?php echo $email ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_providense'); ?></span>
                                        <span><?php echo $providence ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_city'); ?></span>
                                        <span><?php echo $city ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_address'); ?></span>
                                        <span><?php echo $address ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_phonenumber'); ?></span>
                                        <span class="d-inline-block" dir="ltr"><?php echo $phone_number ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_birth'); ?></span>
                                        <span class="d-inline-block" dir="ltr"><?php echo jdate('Y/m/j', $birthday) ?></span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-bold me-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_gender'); ?></span>
                                        <span class="d-inline-block" dir="ltr"><?php echo $gender ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="pb-2 border-bottom mb-4 secondary-font"><?php echo $functions->get_language($_SESSION['lang'], 'account_favorite'); ?></h5>
                            <div class="whishlist-slider owl-carousel">
                                <?php foreach ($wishlist as $product) :
                                    $product = new product($product['post_id']);
                                    include('product-part.php');
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7">
            <div class="card card-body mb-4">
                <h5 class="mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'account_orders'); ?></h5>
                <div class="card-datatable text-nowrap overflow-auto">
                    <table class="datatables-ajax table table-bordered">
                        <thead>
                            <tr>
                                <th>آیدی</th>
                                <th>وضعیت</th>
                                <th>شماره مویابل</th>
                                <th>قیمت</th>
                                <th>تاریخ</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card card-body mb-4">
                <h5 class="mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit'); ?></h5>
                <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_name'); ?></label>
                        <input name="firstname" class="form-control" type="text" value="<?php echo $firstname; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_fname'); ?></label>
                        <input name="lastname" class="form-control" type="text" value="<?php echo $lastname; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_phonenumber'); ?></label>
                        <input name="phone_number" class="form-control" type="tel" value="<?php echo $phone_number; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_email'); ?></label>
                        <input onkeyup="replace_digits(this);" type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_date'); ?></label>
                        <input type="text" name="birthday" class="form-control" placeholder="YYYY/MM/DD" id="flatpickr-date">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label d-block mb-2"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_gender'); ?></label>
                        <div class="form-check form-check-inline">
                            <input name="gender" class="form-check-input mr-0 ml-1" id="type-simple" type="radio" value="female" <?php if ($gender == 'female' || !$gender) echo 'checked' ?>>
                            <label class="form-check-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_female'); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="gender" class="form-check-input mr-0 ml-1" id="type-variable" type="radio" value="male" <?php if ($gender == 'male') echo 'checked' ?>>
                            <label class="form-check-label"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_male'); ?></label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="submit" name="user_info" class="btn btn-primary btn-pill mt-md-3"><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_submit'); ?></button>
                    </div>
                </form>
            </div>
            <div class="card card-body">
                <h5 class="mb-3"><?php echo $functions->get_language($_SESSION['lang'], 'return_request_title'); ?></h5>
                <form class="theme-form row" action="" method="post">
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'return_request_name'); ?></label>
                        <input name="lastname" class="form-control" type="text" value="<?php echo $lastname; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'return_request_phonenumber'); ?></label>
                        <input name="phone_number" class="form-control" type="tel" value="<?php echo $phone_number; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'return_request_description'); ?></label>
                        <textarea name="message" class="form-control" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'return_request_desc_placeholder'); ?>"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="returned" class="btn btn-primary btn-pill mt-md-3"><?php echo $functions->get_language($_SESSION['lang'], 'return_request_submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="showFactor" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-xl my-4" role="document">
        <div class="modal-content p-4">
            <h4 class="mb-1"><?php echo $functions->get_language($_SESSION['lang'], 'factor_title'); ?> : <span id="order_id"></span></h4>
            <hr class="my-2">
            <div class="row">
                <div class="col-md-6 ml-auto">
                    <h5><?php echo $functions->get_language($_SESSION['lang'], 'factor_details'); ?></h5>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_providence'); ?> : <span id="user_providence"></span></p>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_address'); ?> : <span id="user_address"></span></p>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'account_edit_phonenumber'); ?> : <span id="user_phone"></span></p>
                </div>
                <div class="col-md-6 ml-auto">
                    <h5><?php echo $functions->get_language($_SESSION['lang'], 'factor_shipping'); ?></h5>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'factor_shipping_price'); ?> : <span id="user_shipping_price"></span></p>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'factor_payment_methode'); ?> : <span id="payment"></span></p>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'factor_shipping_methode'); ?> : <span id="shipping"></span></p>
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'factor_delivery_day'); ?> : <span id="days"></span></p>
                </div>
            </div>
            <hr class="my-2">

            <div class="row">
                <div class="col-md-1 ml-auto">#</div>
                <div class="col-md-4 ml-auto">نام آیتم</div>
                <div class="col-md-2 ml-auto">دسته بندی</div>
                <div class="col-md-1 ml-auto">تعداد</div>
                <div class="col-md-2 ml-auto">مبلغ</div>
                <div class="col-md-1 ml-auto">مبلغ کل</div>
                <div class="col-md-1 ml-auto">حذف</div>
            </div>

            <div id="order_items">
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary ml-3" onclick="$('#showFactor').fadeOut();$('#showFactor').removeClass('show');"><?php echo $functions->get_language($_SESSION['lang'], 'factor_close'); ?></button>
                <a href="" id="printFactorButton" target="_blank" class="btn btn-primary"><?php echo $functions->get_language($_SESSION['lang'], 'factor_print'); ?></a>
            </div>

        </div>
    </div>
</div>
<script src="/themes/aso/includes/assets/js/jquery.dataTables.js"></script>
<script src="/themes/aso/includes/assets/js/fa.js"></script>
<script src="/themes/aso/includes/assets/js/tables-datatables-advanced.js"></script>
<script src="/themes/aso/includes/assets/js/jdate.js"></script>
<script src="/themes/aso/includes/assets/js/flatpickr-jdate.js"></script>
<script src="/themes/aso/includes/assets/js/fa-jdate.js"></script>
<script src="/themes/aso/includes/assets/js/datatables-bootstrap5.js"></script>
<script src="/themes/aso/includes/assets/js/datatables.responsive.js"></script>
<script src="/themes/aso/includes/assets/js/responsive.bootstrap5.js"></script>
<?php if (empty($birthday)) $birthday = intval(time()); ?>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";

    function responsive_filemanager_callback(field_id) {
        let image_url = $('#' + field_id).val();
        $('#post_image').attr("src", image_url);
        close_window();
    }

    function close_window() {
        Fancybox.getInstance().close();
    }
    const flatpickrDate = document.querySelector('#flatpickr-date');
    if (flatpickrDate) {
        flatpickrDate.flatpickr({
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            defaultDate: ["<?php echo $birthday; ?>"]
        });
    }

    var dt_ajax_table = $('.datatables-ajax');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: '/themes/aso/includes/API/v1/GetOrders.php?uid=<?php echo $user_id; ?>'
            },
            columns: [
                // columns according to JSON
                {
                    data: 'post_id'
                },
                {
                    data: 'status'
                },
                {
                    data: 'number'
                },
                {
                    data: 'price'
                },
                {
                    data: 'date'
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
                        return "<span class='text-truncate'>" + $id + '</span>';
                    }
                },
                {
                    // status
                    targets: 1,
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        var $status = full['status'];
                        return "<span class='text-truncate'>" + $status + '</span>';
                    }
                },
                {
                    // number
                    targets: 2,
                    render: function(data, type, full, meta) {
                        var $number = full['number'];
                        return "<span class='text-truncate'>" + $number + '</span>';
                    }
                },
                {
                    // price
                    targets: 3,
                    render: function(data, type, full, meta) {
                        var $price = full['price'];

                        return '<span class="fw-semibold">' + $price + '</span>';
                    }
                },
                {
                    // date
                    targets: 4,
                    render: function(data, type, full, meta) {
                        var $date = full['date'];
                        return '<span class="text-truncate">' + $date + '</span>';
                    }
                },
                {
                    // op
                    targets: 5,
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
                            return '<?php echo $functions->get_language($_SESSION['lang'], 'factor_title'); ?> : ' + data['post_id'];
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

    function filter_tb(input) {
        dt_ajax.api().ajax.reload();
    }


    function ShowFactor(order_id) {
        getJSON('/themes/aso/includes/API/v1/GetOrderItems.php?order_id=' + order_id, function(err, data) {
            if (data != null) {
                $('#order_id').text(data['order_id']);
                $('#user_providence').text(data['user_providence']);
                $('#user_address').text(data['user_address']);
                $('#user_phone').text(data['user_phone']);
                $('#user_shipping_price').text(data['user_shipping_price']);
                $('#payment').text(data['payment']);
                $('#shipping').text(data['shipping']);
                $('#days').text(data['days']);
                $('#order_items').html(data['items_html']);
                $('#printFactorButton').attr('href', 'print-order?order_id=' + data['order_id']);
                $('#showFactor').fadeIn();
                $('#showFactor').addClass('show');
            }
        });
    };
</script>
<?php include_once('footer.php'); ?>