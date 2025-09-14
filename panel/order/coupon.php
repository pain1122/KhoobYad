<?php
if (isset($_POST['submit'])) {
    $obj = new post('new');
    $post_id = $obj->get_id();
    $obj->set_post_type('coupon');
    $name = $_POST['name'];
    $code = $_POST['code_e'];
    if (strlen($_POST['code_r']) > 0)
        $code = $_POST['code_r'];
    $exclusive = $_POST['exclusive'];
    if ($exclusive == 'true')
        $user_id = $_POST['user_number'];
    $discount = $_POST['discount'];
    $uses = $_POST['uses'];
    $sale_date = explode('-', $_POST['sale_date']);
    $sale_start = $sale_end = 0;
    if ($sale_date[1] > 0) {
        $sale_start = intval(time()) + $sale_date[0];
        $sale_end = intval(time()) + $sale_date[1];
    }
    $obj->set_author($uid);
    $obj->set_post_modify(gmdate("Y-m-d\ H:i:s", $sale_start));
    $obj->set_content($code);
    $obj->set_title($name);
    $metas = [
        "coupon" => $discount,
        "expire-time" => $sale_end,
        "start-time" => $sale_start,
        "uses" => $uses,
        "user-id" => $user_id,
        "exclusive" => $exclusive
    ];
    $obj->insert_meta($metas);
}
$sale_start = $sale_end = intval(time());
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $obj = new post($post_id);
    $name = $obj->get_title();
    $code = $obj->get_content();
    $value = $obj->get_meta('coupon');
    $sale_end = $obj->get_meta('expire-time');
    $sale_start = $obj->get_meta('start-time');
    $uses = $obj->get_meta('uses');
    $exclusive = $obj->get_meta('exclusive');
    $user_id = $obj->get_meta('user-id');
}
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">

<div class="row">
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h4 class="card-title mb-0">افزودن</h4>
            </div>
            <div class="card-body p-3">
                <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3 col-12">
                        <label class="form-label">نام</label>
                        <input required onkeyup="replace_digits(this);" name="name" class="form-control" type="text" value="<?php echo $name; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">کد کوپن</label>
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" onclick="$('input[name=\'code_r\']').val('');" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">
                                        اختصاصی
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" onclick="$('input[name=\'code_e\']').val('');" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">
                                        رندوم
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                    <input class="form-control" placeholder="کد اختصاصی خود را وارد کنید" value="<?php echo $code; ?>" name="code_e" type="text">
                                </div>
                                <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                    <div id="random1">
                                        <input class="form-control" placeholder="بر روی دکمه زیر کلیک کنید" value="<?php echo $code; ?>" onkeydown="return false" name="code_r" type="text">
                                        <span class="btn btn-sm btn-primary mt-2">تولید کد</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-12">
                        <div class="d-flex flex-wrap align-items-center">
                            <label class="form-label w-100">دسترسی</label>
                            <input name="exclusive" onclick="$('#user_phone').slideUp();" class="form-check-input mt-0 ml-1" id="type-variable" type="radio" value="false" <?php if ($exclusive == 'false' || !$exclusive) echo 'checked' ?>>
                            <label class="form-check-label ml-3"> عمومی </label>
                            <input name="exclusive" onclick="$('#user_phone').slideDown();" class="form-check-input mt-0 ml-1" id="type-simple" type="radio" value="true" <?php if ($exclusive == 'true') echo 'checked' ?>>
                            <label class="form-check-label"> اختصاصی </label>
                        </div>
                    </div>
                    <div class="mb-3 col-12" <?php if ($exclusive == 'false' || !$exclusive) echo "style='display: none;'" ?> id="user_phone">
                        <label class="form-label">شماره کاربر</label>
                        <input onkeyup="replace_digits(this);" name="user_number" class="form-control" type="text" value="<?php echo $user_id; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">مقدار تخفیف</label>
                        <input required onkeyup="replace_digits(this);" name="discount" class="form-control" type="text" value="<?php echo $value; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">تعداد مصرف</label>
                        <input class="form-control" name="uses" type="number" min="0" required onkeyup="replace_digits(this);" value="<?php echo $uses; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">انتخاب بازه</label>
                        <input type="text" name="range" class="form-control" placeholder="YYYY/MM/DD تا YYYY/MM/DD" id="flatpickr-range">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit" class="btn btn-primary btn-pill"> ذخیره کنید </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card">
            <form class="theme-form" action="" method="post">
                <div class="card-body">
                    <div class="card-datatable text-nowrap overflow-auto">
                        <table class="datatables-posts table table-bordered">
                            <thead>
                                <tr>
                                    <th>آیدی</th>
                                    <th>نام کوپن</th>
                                    <th>کد کوپن</th>
                                    <th>وضغیت</th>
                                    <th>مقدار</th>
                                    <th>اختصاصی</th>
                                    <th>تعداد استفاده</th>
                                    <th>آیدی کاربر</th>
                                    <th>تاریخ آغاز</th>
                                    <th>تاریخ انقضا</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script>
    $('#random1 span').click(function() {
        var rand = genString(10);
        $('#random1 input').val(rand);
    });
    const flatpickrRange = document.querySelector('#flatpickr-range');
    if (typeof flatpickrRange != undefined) {
        flatpickrRange.flatpickr({
            mode: 'range',
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            defaultDate: ["<?php echo jdate("Y/m/d", $sale_start); ?>", "<?php echo jdate("Y/m/d", $sale_end); ?>"]
        });
    }
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
                        url: 'API/v1/GetCoupon.php'
                    },
                    columns: [
                        // columns according to JSON
                        {
                            data: 'post_id'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'code'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'value'
                        },
                        {
                            data: 'exclusive'
                        },
                        {
                            data: 'uses'
                        },
                        {
                            data: 'user_id'
                        },
                        {
                            data: 'sale_start'
                        },
                        {
                            data: 'sale_end'
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
                                var $name = full['name'],
                                    $id = full['post_id'];
                                var $row_output =
                                    '<a href="index.php?page=order/coupon.php&id=' + $id + '">' +
                                    '<span class="fw-semibold">' +
                                    $name +
                                    '</span>' +
                                    '</a>';
                                return $row_output;
                            }
                        },
                        {
                            // code
                            targets: 2,
                            render: function(data, type, full, meta) {
                                var $status = full['code'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $status + '</span>';
                            }
                        },
                        {
                            // status
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var $tag = full['status'];

                                return '<span class="fw-semibold">' + $tag + '</span>';
                            }
                        },
                        {
                            // value
                            targets: 4,
                            render: function(data, type, full, meta) {
                                var $status = full['value'];
                                return '<span class="badge bg-label-warning">' + $status + '</span>';
                            }
                        },
                        {
                            // exclusive
                            targets: 5,
                            render: function(data, type, full, meta) {
                                var $date = full['exclusive'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $date + '</span>';
                            }
                        },
                        {
                            // uses
                            targets: 6,
                            render: function(data, type, full, meta) {
                                var $stock = full['uses'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $stock + '</span>';
                            }
                        },
                        {
                            // user_id
                            targets: 7,
                            render: function(data, type, full, meta) {
                                var $stock = full['user_id'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $stock + '</span>';
                            }
                        },
                        {
                            // sale_start
                            targets: 8,
                            render: function(data, type, full, meta) {
                                var $stock = full['sale_start'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $stock + '</span>';
                            }
                        },
                        {
                            // sale_end
                            targets: 9,
                            render: function(data, type, full, meta) {
                                var $stock = full['sale_end'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $stock + '</span>';
                            }
                        },
                        {
                            // op
                            targets: 10,
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
                                    return 'جزئیات ' + data['name'];
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