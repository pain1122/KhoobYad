<?php if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    base::RunQuery("DELETE FROM `post` WHERE `post_id` = " . $post_id);
    base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = " . $post_id);
} ?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<div class="card">
    <h5 class="card-header">همه برگه ها</h5>
    <div class="card-datatable table-responsive">
        <table class="datatables-posts table border-top">
            <thead>
                <tr>
                    <th>آیدی</th>
                    <th>عنوان برگه</th>
                    <th>وضعیت</th>
                    <th>نویسنده</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
        </table>
    </div>
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
    $(function() {
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
                        url: 'API/v1/GetPages.php'
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
                                    '<a href="index.php?page=pages/add-page.php&id=' + $id + '">' +
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
                            // op
                            targets: 5,
                            title: 'عمل‌ها',
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
    });
</script>