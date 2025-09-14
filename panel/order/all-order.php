<?php if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    base::RunQuery("DELETE `post`,`post_meta`
    FROM `post` INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id`
    WHERE `post`.`post_id` = " . $post_id);
    $items = base::FetchArray("SELECT `items_order_id` FROM `items_order` WHERE `order_id` = $post_id ");
    foreach($items as $item)
        base::RunQuery("DELETE FROM `items_order_meta` WHERE `order_item_id` = $item");
} ?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<div class="card">
    <h5 class="card-header">همه سفارشات</h5>
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

<div class="modal fade " id="showFactor" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-xl my-4" role="document">
        <div class="modal-content p-4">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <h4 class="mb-1">جزییات سفارش شماره : <span id="order_id"></span></h4>
            <hr class="my-2">

            <div class="row">
                <div class="col-1 ml-auto">#</div>
                <div class="col-4 ml-auto">نام آیتم</div>
                <div class="col-2 ml-auto">دسته بندی</div>
                <div class="col-1 ml-auto">تعداد</div>
                <div class="col-2 ml-auto">مبلغ</div>
                <div class="col-1 ml-auto">مبلغ کل</div>
                <div class="col-1 ml-auto">حذف</div>
            </div>

            <div id="order_items">
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary ml-3" data-bs-dismiss="modal" aria-label="Close">بستن</button>
                <a href="" id="printFactorButton" class="btn btn-primary">پرینت</a>
            </div>

        </div>
    </div>
</div>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>

<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script>
    var dt_ajax_table = $('.datatables-ajax');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: 'API/v1/GetOrders.php?uid=<?php echo $uid; ?>'
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
                        return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                    }
                },
                {
                    // status
                    targets: 1,
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        var $status = full['status'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $status + '</span>';
                    }
                },
                {
                    // number
                    targets: 2,
                    render: function(data, type, full, meta) {
                        var $number = full['number'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $number + '</span>';
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
                        return '<span class="badge bg-label-warning">' + $date + '</span>';
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
                            return 'جزئیات سفارش شماره ' + data['post_id'];
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
        getJSON('API/v1/GetOrderItems.php?order_id=' + order_id, function(err, data) {
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
                $('#printFactorButton').attr('href', 'order/print-order.php?order_id=' + data['order_id']);
            }
        });
    };
</script>