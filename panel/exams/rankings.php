<?php if (isset($_GET['exam'])) {
    $post_id = intval($_GET['exam']);
    $obj = new post($post_id);
    $title = $obj->get_title();
    $participated = false;
    $user = new user($uid);
    $exams = json_decode($user->get_user_meta('exams'), true);
    if(!$exams)
        $exams = [];
    if (in_array($post_id, $exams)) {
        $query = "SELECT `post`.`post_id`,`post_excerpt`,`post_title`,`value` FROM `post` 
        INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
        WHERE `post`.`post_type` = 'exam_result'
        AND `post_parent` = $post_id
        AND `author` = $uid
        GROUP BY `post`.`post_id`;";
        $result = base::FetchAssoc($query);
        $participated = true;
    }
} else {
    base::redirect(site_url . "panel/index.php?page=dashboard.php");
} ?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<div class="card">
    <div class="card-header">
        <h5>رتبه بندی آزمون <?php echo $title; ?></h5>
    </div>
    <?php if ($participated == true): ?>
        <div class="card-body">
            <span class="p-2 bg-label-warning ml-3">نام و نام خانوادگی: <?php echo $result['post_title']; ?></span>
            <!-- <span class="badge bg-label-warning">نام و نام خانوادگی: <?php echo $result['value']; ?></span> -->
            <span class="p-2 bg-label-warning ml-3">تراز: <?php echo round($result['post_excerpt']); ?></span>
            <a class="btn btn-info" id="participant-link"
                href="?page=exams/result.php&exam=<?php echo $result['post_id']; ?>" target="_blank"><i
                    class="fa-regular fa-eye me-1"></i> کارنامه</a>
        </div>
    <?php endif; ?>
    <div class="card-datatable text-nowrap overflow-auto">
        <table class="datatables-posts table table-bordered">
            <thead>
                <tr>
                    <th>رتبه</th>
                    <th>نام</th>
                    <th>تراز</th>
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
    $(window).resize(function () {
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
    $(window).ready(function () {
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
                    url: 'API/v1/GetExamResults.php?ID=<?php echo $post_id; ?>'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'rank'
                    },
                    {
                        data: 'user_name'
                    },
                    {
                        data: 'score'
                    },
                ],
                columnDefs: [{
                    // For Responsive
                    // className: 'control',
                    targets: 0,
                    render: function (data, type, full, meta) {
                        var $rank = full['rank'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $rank + '</span>';
                    }
                },
                {
                    // User name 
                    targets: 1,
                    responsivePriority: 4,
                    <?php if ($role != 'student') { ?>
                            render: function (data, type, full, meta) {
                            var $user_name = full['user_name'],
                                $id = full['post_id'];
                            var $row_output =
                                '<a href="index.php?page=exams/result.php&exam=' + $id + '">' +
                                '<span class="fw-semibold">' +
                                $user_name +
                                '</span>' +
                                '</a>';
                            return $row_output;
                        }
                    <?php } else { ?>
                            render: function (data, type, full, meta) {
                            var $user_name = full['user_name'];
                            var $row_output = '<span class="fw-semibold">' + $user_name + '</span>';
                            return $row_output;
                        }
                    <?php } ?>
                },
                {
                    // score
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $score = full['score'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $score + '</span>';
                    }
                }
                ],
                order: [
                    [0, 'ASC']
                ],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'جزئیات ' + data['title'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
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