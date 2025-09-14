<?php


$report_start = $repoort_end = 0;
if(isset($_POST['report_date'])){
    $report_date = explode('-', $_POST['report_date']);
    if ($report_date[1] > 0) {
            $report_start = intval($report_date[0]);
            $report_end = intval($report_date[1]);
            $range_query = " AND UNIX_TIMESTAMP(`post_date`) > $report_start AND UNIX_TIMESTAMP(`post_date`) < $report_end ";
    }
}

$post_id_query = "SELECT `post_id` as `id` FROM `post` WHERE `post_type` = 'shop_order' $range_query";
$order_data = Base::fetchassoc("SELECT SUM(CAST(`amount`.`value` AS UNSIGNED)) as `sm`,`orders`.`id`,`orders_count`.`cnt`,`shipping`.`price`,`coupone`.`coupone_price` FROM `post` `orders`
JOIN (SELECT GROUP_CONCAT(CONCAT(\"'\",`post_id`,\"'\")) as `id` FROM `post` WHERE `post_type` = 'shop_order') as `orders`
JOIN (SELECT COUNT(`post_id`) as `cnt` FROM `post` WHERE `post_id` IN ($post_id_query)) as `orders_count`
JOIN (SELECT SUM(CAST(`value` AS UNSIGNED)) as `price` FROM `post_meta` WHERE `key` = 'user_shipping_price' AND `post_id` IN ($post_id_query)) as `shipping`
JOIN (SELECT SUM(CAST(`value` AS UNSIGNED)) as `coupone_price` FROM `post_meta` WHERE `key` = 'coupon' AND `post_id` IN ($post_id_query)) as `coupone`
INNER JOIN `items_order` `items` ON `orders`.`post_id` = `items`.`order_id`
INNER JOIN `items_order_meta` `amount` ON `items`.`items_order_id` = `amount`.`order_item_id`
WHERE `amount`.`key` = 'total' AND `orders`.`post_id` IN ($post_id_query)");

$items = Base::fetcharray("SELECT items_order.item_id,items_order.order_id,SUM(items_order.qty) as `qty`,post.post_id,post.post_title,post_meta.value FROM `items_order`
INNER JOIN `post` ON  `items_order`.`item_id` = `post`.`post_id`
INNER JOIN `post_meta` ON `post`.`post_id` = post_meta.post_id
WHERE `items_order`.`order_id` IN ({$order_data['id']}') AND `post_meta`.`key` = '_stock'
GROUP BY `item_id`");

?>
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
<div class="row g-4 mb-4">
    <div class="col-sm-12 col-xl-6">
        <div class="card">
            <div class="card-body d-flex align-items-start justify-content-between">
                <div class="row col-11 align-items-start justify-content-between">
                    <div class="content-left col-12 col-md-6">
                        <span class="secondary-font fw-medium">انتخاب بازه ی گزارش گیری</span>
                        <div class="d-flex align-items-baseline mt-2">
                            <form action="" method="post">
                                <input type="text" name="report_date" class="form-control" onblur="this.form.submit()" placeholder="YYYY/MM/DD تا YYYY/MM/DD" id="flatpickr-range">
                            </form>
                        </div>
                    </div>
                    <div class="content-right col-12 col-md-6 mt-3 mt-md-0">
                        <span class="secondary-font fw-medium">بازه ی انتخابی</span>
                        <div class="d-flex align-items-baseline mt-2">
                        <h4 class="mb-0 me-2 small"><?php if($report_start > 0) echo jdate('Y/m/j',$report_start) . " - " . jdate('Y/m/j',$report_end);  ?></h4>
                        </div>
                    </div>
                </div>
                <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-user bx-sm"></i>
                    </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="secondary-font fw-medium">فروش</span>
                        <div class="d-flex align-items-baseline mt-2">
                        <h4 class="mb-0 me-2 small"><?php  if($order_data['sm'] > 0) echo number_format($order_data['sm']) ?></h4>
                            <small class="text-success">تومان</small>
                        </div>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-user bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="secondary-font fw-medium">تعداد سفارشات</span>
                        <div class="d-flex align-items-baseline mt-2">
                            <h4 class="mb-0 me-2 small"><?php if($order_data['cnt'] > 0)echo number_format($order_data['cnt']) ?></h4>
                        </div>
                    </div>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="bx bx-user-plus bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="secondary-font fw-medium">مجموع مبلغ حمل و نقل</span>
                        <div class="d-flex align-items-baseline mt-2">
                            <h4 class="mb-0 me-2 small"><?php  if($order_data['price'] > 0) echo number_format($order_data['price']) ?></h4>
                            <small class="text-success">تومان</small>
                        </div>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="bx bx-group bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="secondary-font fw-medium">مجموع کپن های استفاده شده</span>
                        <div class="d-flex align-items-baseline mt-2">
                            <h4 class="mb-0 me-2 small"><?php if($order_data['coupone_price'] > 0) echo number_format($order_data['coupone_price']) ?></h4>
                            
                        </div>
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="bx bx-user-voice bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Users List Table -->
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title">فیلتر جستجو</h5>
        <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-users table border-top">
            <thead>
                <tr>
                    <th>آیدی</th>
                    <th>نام محصول</th>
                    <th>تعداد فروش</th>
                    <th>تعداد باقیمانده</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(is_countable($items) && count($items)>0):
                foreach ($items as $item) :
                 
                ?>
                <tr>
                    <td><?php echo $item['post_id'] ?></td>
                    <td><?php echo $item['post_title'] ?></td>
                    <td><?php echo $item['qty'] ?></td>
                    <td><?php echo $item['value'] ?></td>
                </tr>
                <?php 
                endforeach;
            endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-buttons/datatables-buttons.js"></script>
<script src="assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js"></script>
<script src="assets/vendor/libs/jszip/jszip.min.js"></script>
<script src="assets/vendor/libs/datatables-buttons/buttons.html5.js"></script>
<script src="assets/vendor/libs/datatables-buttons/buttons.print.js"></script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script>
    const flatpickrRange = document.querySelector('#flatpickr-range');
    if (typeof flatpickrRange != undefined) {
        flatpickrRange.flatpickr({
            mode: 'range',
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            onClose: function(selectedDates, dateStr, instance) {
                let form = document.getElementsByTagName('form');
                form[0].submit();
            }
        });
    }
    $(window).resize(function() {
        if ($(window).width() < 560) {
            $('.datatables-users tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-users tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }

    });
    $(function() {
        $(window).ready(function() {
            if ($(window).width() < 560) {
                $('.datatables-users tbody td,.modal-body tbody td').css({
                    'font-size': '12px',
                    'padding': '15px'
                });
            } else {
                $('.datatables-users tbody td,.modal-body tbody td').css({
                    'font-size': '14px',
                    'padding': '0.625rem 1.5rem'
                });
            }
            // Variable declaration for table
            var dt_user_table = $('.datatables-users');

            // Users datatable
            if (dt_user_table.length) {
                var dt_user = dt_user_table.DataTable({
                    select: true,
                    order: [
                        [0, 'asc']
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
                });
            }

            // Filter form control to default size
            // ? setTimeout used for multilingual table initialization
            setTimeout(() => {
                $('.dataTables_filter .form-control').removeClass('form-control-sm');
                $('.dataTables_length .form-select').removeClass('form-select-sm');
            }, 1000);
        })
    });
</script>