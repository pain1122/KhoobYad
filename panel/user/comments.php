<?php
if (isset($_POST['action'])) {
    $posts = $_POST['posts'];

    foreach ($posts as $post) {
        if ($_POST['action'] == "delete") {
            //DELETE Tag
            base::RunQuery("DELETE `post`,`post_meta` FROM `post` INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` WHERE `post`.`post_id` = $post;");
        } else {
            base::RunQuery("UPDATE `post` SET `post_status` = '" . $_POST['action'] . "' WHERE `post_id` = $post");
        }
    }
}
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<div class="card">
    <h5 class="card-header">کامنت ها</h5>
    <form action="" method="POST" class="card-datatable table-responsive card-body">
        <table class="datatables-posts table border-top">
            <div class="d-flex">
                <div class="col-8 col-md-4">
                    <select name="action" class="select2">
                        <option disabled selected>کارهای دسته‌جمعی</option>
                        <option value="delete">حذف</option>
                        <option value="accepted">پذیرفتن</option>
                        <option value="rejected">رد کردن</option>
                    </select>
                </div>
                <button class="btn btn-primary mr-3" type="submit"> اجرا </button>
            </div>
            <thead>
                <tr>
                    <th data-sortable="false" style="width: 40px;"><input onclick="toggle()" class='form-check-input' type='checkbox'></th>
                    <th>آیدی</th>
                    <th>نویسنده</th>
                    <th>متن</th>
                    <th>امتیاز</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
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
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script>
    function toggle() {
        $('.comment-counter').prop('checked', true);
    }
    $('.select2').select2();
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
                        url: 'API/v1/GetComments.php'
                    },
                    columns: [
                        // columns according to JSON
                        {
                            data: 'input'
                        },
                        {
                            data: 'post_id'
                        },
                        {
                            data: 'author'
                        },
                        {
                            data: 'content'
                        },
                        {
                            data: 'rate'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'date'
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            render: function(data, type, full, meta) {
                                var $input = full['input'];
                                return '<span class="text-truncate">' + $input + '</span>';
                            }
                        }, {
                            // className: 'control',
                            targets: 1,
                            render: function(data, type, full, meta) {
                                var $id = full['post_id'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                            }
                        },
                        {
                            // author
                            targets: 2,
                            render: function(data, type, full, meta) {
                                var $author = full['author'];
                                return '<span class="badge bg-label-warning">' + $author + '</span>';
                            }
                        },
                        {
                            // content 
                            targets: 3,
                            responsivePriority: 4,
                            render: function(data, type, full, meta) {
                                var $content = full['content'];
                                return "<span class='text-truncate'>" + $content + '</span>';
                            }
                        },{
                            // rate
                            targets: 4,
                            render: function(data, type, full, meta) {
                                var $rate = full['rate'];
                                return '<span class="badge bg-label-warning">' + $rate + '</span>';
                            }
                        },
                        {
                            // status
                            targets: 5,
                            render: function(data, type, full, meta) {
                                var $status = full['status'];
                                return "<span class='text-truncate'>" + $status + '</span>';
                            }
                        },
                        {
                            // date
                            targets: 6,
                            render: function(data, type, full, meta) {
                                var $date = full['date'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $date + '</span>';
                            }
                        }
                    ],
                    order: [
                        [1, 'DESC']
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });
            }
        })
    });
</script>