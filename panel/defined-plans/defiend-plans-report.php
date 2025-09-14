<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">

<div class="card">
    <div class="card-body">
        <div class="card-datatable text-nowrap ">
            <table class="datatables-posts table table-bordered">
                <thead>
                    <tr>
                        <th class="all">آیدی</th>
                        <th class="all">نام</th>
                        <th >شماره تلفن</th>
                        <th>پایه تحصیلی</th>
                        <th>رشته تحصیلی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
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
                    url: 'API/v1/GetDefiendPlansReport.php?id=<?php echo $uid; ?>'
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
                        data: 'phone_number'
                    },
                    {
                        data: 'grade'
                    },
                    {
                        data: 'fos'
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
                            var $id = full['user_id'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                        }
                    },
                    {
                        // User full name 
                        targets: 1,
                        render: function(data, type, full, meta) {
                            var $name = full['name'],
                                $id = full['user_id'];
                            var $row_output =
                                '<a href="index.php?page=defined-plans/my-defined-plans.php&uid=' + $id + '">' +
                                '<span class="fw-semibold text-truncate d-flex align-items-center">' +
                                $name +
                                '</span>' +
                                '</a>';
                            return $row_output;
                        }
                    },
                    {
                        // phone_number
                        targets: 2,
                        render: function(data, type, full, meta) {
                            var $phone_number = full['phone_number'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $phone_number + '</span>';
                        }
                    },
                    {
                        // grade
                        targets: 3,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var $grade = full['grade'];

                            return '<span class="fw-semibold">' + $grade + '</span>';
                        }
                    },
                    {
                        // fos
                        targets: 4,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var $fos = full['fos'];

                            return '<span class="fw-semibold">' + $fos + '</span>';
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
                                return 'برنامه هفتگی';
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