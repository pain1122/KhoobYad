<?php
if (isset($_POST['submit'])) {
    $class_id = 'new_tag';
    if (isset($_GET['id']))
        $class_id = $_GET['id'];
    $obj = new tag($class_id);
    $class_id = $obj->get_id();
    $class_name = $_POST['class_name'];
    $obj->set_name($class_name);
    $obj->set_type('class_group');
    $members = $_POST['members'];
    if (is_countable($members) && count($members)) {
        $members = implode(',', $members);
        $add_q = "UPDATE `user_meta` SET `value` = CONCAT(`value`, '$class_id,') WHERE `user_id` IN ($members) AND `key` = 'class_groups';";
        base::RunQuery($add_q);
    }
    if (!isset($_GET['id']))
    base::redirect($_SERVER['REQUEST_URI'] . "&id=$class_id");
}
$selected_members = [];
$title = "افزودن کلاس";
if (isset($_GET['id'])) {
    $class_id = intval($_GET['id']);
    $obj = new tag($class_id);
    $name = $obj->get_name();
    $selected_members = array_column(base::FetchArray("SELECT `user_id` FROM `user_meta` WHERE `key` = 'class_groups' AND `value` LIKE '%$class_id%'"),'user_id');
    $title = "ویرایش کلاس";
}
if(isset($_GET['delete_class'])){
    $class_id = intval($_GET['delete_class']);
    $selected_members = array_column(base::FetchArray("SELECT `user_id` FROM `user_meta` WHERE `key` = 'class_groups' AND `value` LIKE '%$class_id%'"),'user_id');
    $members = implode(',',array_filter($selected_members));
    $delet_q = "UPDATE `user_meta` SET `value` = REPLACE(`value`, '$class_id,','') WHERE `user_id` IN ($members) AND `key` = 'class_groups';";
    base::RunQuery($delet_q);
    $delet_q = "DELETE `tag` FROM `tag` LEFT JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` WHERE `tag`.`tag_id` = $class_id";
    base::RunQuery($delet_q);
    base::redirect("index.php?page=user/all-classes.php");
}
if(isset($_GET['delete_member']) && isset($_GET['id'])){
    $class_id = intval($_GET['id']);
    $members = implode(',',array_filter($selected_members));
    $delet_q = "UPDATE `user_meta` SET `value` = REPLACE(`value`, '$class_id,','') WHERE `user_id` IN ($members) AND `key` = 'class_groups';";
    base::RunQuery($delet_q);
    base::redirect("index.php?page=user/all-classes.php&id=$class_id");
}
$select_members = "SELECT `users`.`user_id`,`nicename` FROM `users` INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id` WHERE `key` = 'role' AND `value` = 'student'";
$members = base::FetchArray($select_members);
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<div class="row">
<div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h4 class="card-title mb-0"><?php echo $title; ?></h4>
            </div>
            <div class="card-body p-3">
                <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3 col-12">
                        <label class="form-label">نام</label>
                        <input required onkeyup="replace_digits(this);" onchange="insert_sku(this,'slug')" name="class_name" class="form-control" type="text" value="<?php echo $name; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">انتخاب اعضا : </label>
                            <select class="form-control select2" name="members[]" multiple="multiple">
                                <option></option>
                                <?php foreach ($members as $member) : ?><option value="<?php echo $member['user_id']; ?>" <?php if (in_array($member['user_id'],$selected_members)) echo 'selected';?>><?php echo $member['nicename']; ?></option><?php endforeach; ?>
                            </select>
                    </div>
                    <div class="card-footer col-12">
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
                                    <th>نام</th>
                                    <th data-sortable="false">عملیات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </form>
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
        const select2 = $('.select2');
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'انتخاب',
                    dropdownParent: $this.parent()
                });
            });
        }
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
                    url: 'API/v1/GetMemberClasses.php?id=<?php echo $class_id; ?>'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
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
                            var $id = full['id'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                        }
                    },
                    {
                        // User full name 
                        targets: 1,
                        responsivePriority: 4,
                        render: function(data, type, full, meta) {
                            var $name = full['name'],
                                $id = full['id'];
                            var $row_output =
                                '<a href="index.php?page=user/all-classes.php&id=' + $id + '">' +
                                '<span class="fw-semibold">' +
                                $name +
                                '</span>' +
                                '</a>';
                            return $row_output;
                        }
                    },
                    {
                        // op
                        targets: 2,
                        responsivePriority: 2,
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