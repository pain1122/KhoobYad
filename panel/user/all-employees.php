<?php
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $fname = $_POST['fname'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_role = $_POST['role'];
    $access = $_POST['user_access'];
    if(is_array($access) && !empty($access))
        $access = implode(",", $access);
    $user_id = $phone_number;
    if (isset($_GET['uid'])) {
        $user_id = intval($_GET['uid']);
    }
    
    

    if (strlen($phone_number) > 0) {
        $obj = new user($user_id);
        $user_id = $obj->get_id();
        $obj->set_display_name($name." ".$fname);
        $obj->set_nick_name($name." ".$fname);
        try{
        $obj->set_login($phone_number);
        }catch(Exception $e){
            print("in shomare ghablan sabt shode");
        }
        $obj->set_user_email($email);
        if (strlen($password) > 0){
            $obj->set_password($password);
        }
        $metas = [
            'class_groups' => '',
            'role' => $user_role,
            "access" => $access,
            "firstname"     => $name,
            "lastname"      => $fname
        ];
        $obj->insert_user_meta($metas);
    }
}
$sale_start = $sale_end = intval(time());
if (isset($_GET['uid'])) {
    $user_id = intval($_GET['uid']);
    if ($user_id > 0) {
        $obj = new user($user_id);
        $user_phone_number = $obj->get_login();
        $user_email = $obj->get_user_email();
        $user_role = $obj->get_user_meta('role');
        $user_access = $obj->get_user_meta('access');
        $name = $obj->get_user_meta('firstname');
        $fname = $obj->get_user_meta('lastname');
    }
}
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id > 0) {
        $delete_q = "DELETE `users`,`user_meta` FROM `users` LEFT JOIN `user_meta` ON `user_meta`.`user_id` = `users`.`user_id` WHERE `users`.`user_id` = $user_id";
        $functions->RunQuery($delete_q);
    }
}

$access_array = array();
foreach ($menu_array as $menu => $detail) :
    foreach ($detail['sub'] as $sub_menu => $sub_detail) :
        $access_array[$sub_menu] = $sub_detail['access'];
        if (!empty($sub_detail['sub'])) :
            foreach ($sub_detail['sub'] as $sub_menu2 => $sub_detail2) :
                $access_array[$sub_menu2] = $sub_detail2['access'];
            endforeach;
        endif;
    endforeach;
endforeach;
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
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
                        <label class="form-label">نام خانوادگی</label>
                        <input required onkeyup="replace_digits(this);" name="fname" class="form-control" type="text" value="<?php echo $fname; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">شماره تلفن</label>
                        <input required onkeyup="replace_digits(this);" name="phone_number" class="form-control" type="tel" value="<?php echo $user_phone_number; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">ایمیل</label>
                        <input onkeyup="replace_digits(this);" type="email" name="email" class="form-control" value="<?php echo $user_email; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">رمز عبور</label>
                        <input onkeyup="replace_digits(this);" name="password" class="form-control" type="password">
                    </div>
                    <?php if (($role == 'school' || $role == 'admin')) { ?>
                        <div class="mb-3 col-12">
                            <label class="form-label">انتخاب نقش</label>
                            <select name="role" class="select2">
                                <option>انتخاب نقش</option>
                                <option value='teacher' <?php if($user_role == 'teacher') echo "selected"; ?>>دبیر</option>
                                <option value='adviser' <?php if($user_role == 'adviser') echo "selected"; ?>>مشاور</option>
                                <option value='support' <?php if($user_role == 'support') echo "selected"; ?>>پشتیبان</option>
                            </select>
                        </div>
                        <div class="col-12">
                        <label class="form-label">دسترسی ها</label>
                        <div class="row">
                            <?php foreach ($access_array as $title => $key) : ?>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input <?php if (strpos($user_access, $key) !== false) echo "checked" ?> type="checkbox" name="user_access[]" value="<?php echo  $key ?>" id="flexSwitchCheckChecked" class="form-check-input">
                                        <label class="form-check-label small"><?php echo $title ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php } ?>
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
                                    <th>نام</th>
                                    <th>شماره تلفن</th>
                                    <th>ایمیل</th>
                                    <th>تاریخ</th>
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
                    url: 'API/v1/GetUsers.php?type=partners'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'user_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'phone_number'
                    },
                    {
                        data: 'email'
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
                            var $id = full['user_id'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                        }
                    },
                    {
                        // User full name 
                        targets: 1,
                        responsivePriority: 4,
                        render: function(data, type, full, meta) {
                            var $name = full['name'],
                                $id = full['user_id'];
                            var $row_output =
                                '<a href="index.php?page=user/all-employees.php&uid=' + $id + '">' +
                                '<span class="fw-semibold">' +
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
                        // email
                        targets: 3,
                        render: function(data, type, full, meta) {
                            var $email = full['email'];

                            return '<span class="fw-semibold">' + $email + '</span>';
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
                                return 'جزئیات کاربر ' + data['name'];
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