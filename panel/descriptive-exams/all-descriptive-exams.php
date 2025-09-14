<?php if(isset($_GET['delete'])){
    $post_id = $_GET['delete'];
    base::RunQuery("UPDATE `post` SET `post_type` = 'exam-deleted' WHERE `post_id` = " . $post_id);
} 
$specific_id = $_GET['id'];
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
    <div class="card">
        <h5 class="card-header">همه آزمون ها</h5>
        <form action="" method="POST" class="card-datatable text-nowrap overflow-auto">
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
        </form>
    </div>
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
                        url: 'API/v1/GetDescriptiveExams.php?id=<?php echo $specific_id; ?>'
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
                            data: 'price'
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
                                var $name = full['title']
                                return "<span class='text-truncate d-flex align-items-center'>" + $name + '</span>';
                            }
                        },
                        {
                            // status
                            targets: 2,
                            <?php if($role == 'student') echo "visible: false,"; ?>
                            render: function(data, type, full, meta) {
                                var $status = full['status'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $status + '</span>';
                            }
                        },
                        {
                            // author
                            targets: 3,
                            <?php if($role == 'student') echo "visible: false,"; ?>
                            render: function(data, type, full, meta) {
                                var $status = full['author'];
                                return '<span class="badge bg-label-warning">' + $status + '</span>';
                            }
                        },
                        {
                            // date
                            targets: 4,
                            <?php if($role == 'student') echo "visible: false,"; ?>
                            render: function(data, type, full, meta) {
                                var $date = full['date'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $date + '</span>';
                            }
                        },
                        {
                            // price
                            targets: 5,
                            render: function(data, type, full, meta) {
                                var $price = full['price'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $price + '</span>';
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