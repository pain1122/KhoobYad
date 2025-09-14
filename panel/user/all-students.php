<?php
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $fname = $_POST['fname'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $access = $_POST['user_access'];
    if(empty($access))
        $access = [];
    $user_id = null;
    if (isset($_GET['uid'])) {
        $user_id = intval($_GET['uid']);
    }


    if (strlen($phone_number) > 0) {
        $obj = new user($phone_number);
        $user_id = $obj->get_id();
        $obj->set_display_name($name . " " . $fname);
        $obj->set_nick_name($name . " " . $fname);
        $obj->set_login($phone_number);
        $obj->set_user_email($email);
        if (strlen($password) > 0) {
            echo 'hi';
            $obj->set_password($password);
        }
        $metas = [
            'subscription' => "{$_POST['subscription']}",
            'grade' => "{$_POST['grade']}",
            'fos' => "{$_POST['fos']}",
            'birth' => "{$_POST['birthday']}",
            'class_groups' => '',
            "role" => "student",
            "firstname" => $name,
            "lastname" => $fname,
            "access" => join(",", $access)
        ];
        $obj->insert_user_meta($metas);
        // if (!isset($_GET['uid'])) {
        //     base::RunQuery("INSERT INTO `post` (`author`,`post_type`,`post_status`) VALUES ($user_id,'study_plan','active')");
        // }
    }
}
if (isset($_GET['add_plan'])) {
    $user_id = intval($_GET['add_plan']);
    $study_plans = base::FetchAssoc("SELECT * FROM `post` WHERE `post_type` = 'study_plan' AND `author` = $user_id");
    if (empty($study_plans)) {
        $query = "INSERT INTO `post`( `author`, `post_status`,`post_type`) VALUES ($user_id,'active','study_plan')";
        base::RunQuery($query);
    }
}
if (isset($_GET['uid'])) {
    $user_id = intval($_GET['uid']);
    if ($user_id > 0) {
        $obj = new user($user_id);
        $user_name = $obj->get_user_meta('firstname');
        $user_fname = $obj->get_user_meta('lastname');
        $user_phone_number = $obj->get_login();
        if (empty($user_phone_number))
            echo ("<script>alert('شماره قبلا ثبت شده')</script>");
        $user_email = $obj->get_user_email();
        $user_role = $obj->get_user_meta('role');
        $user_access = $obj->get_user_meta('access');
        $subscription = $obj->get_user_meta('subscription');
        $grade = $obj->get_user_meta('grade');
        $fos = $obj->get_user_meta('fos');
        $birthday = $obj->get_user_meta('birth');
    }
}
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id > 0) {
        $delete_q = "DELETE `users`,`user_meta` FROM `users` LEFT JOIN `user_meta` ON `user_meta`.`user_id` = `users`.`user_id` WHERE `users`.`user_id` = $user_id";
        $functions->RunQuery($delete_q);
    }
}
$grades = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_grade'");
$fields = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_course'");

$access_array = array();
foreach ($students_menu_array as $menu => $detail):
    foreach ($detail['sub'] as $sub_menu => $sub_detail):
        $access_array[$sub_menu] = $sub_detail['access'];
        if (!empty($sub_detail['sub'])):
            foreach ($sub_detail['sub'] as $sub_menu2 => $sub_detail2):
                $access_array[$sub_menu2] = $sub_detail2['access'];
            endforeach;
        endif;
    endforeach;
endforeach;
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
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
                        <input required onkeyup="replace_digits(this);" name="name" class="form-control" type="text"
                            value="<?php echo $user_name; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">نام خانوادگی</label>
                        <input required onkeyup="replace_digits(this);" name="fname" class="form-control" type="text"
                            value="<?php echo $user_fname; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">شماره تلفن</label>
                        <input required onkeyup="replace_digits(this);" name="phone_number" class="form-control"
                            type="tel" value="<?php echo $user_phone_number; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">ایمیل</label>
                        <input onkeyup="replace_digits(this);" type="email" name="email" class="form-control"
                            value="<?php echo $user_email; ?>">
                    </div>
                    <div class="mb-3 col-12 ">
                        <label class="form-label">تاریخ تولد</label>
                        <input type="text" name="birthday" class="form-control" placeholder="YYYY/MM/DD"
                            id="flatpickr-date" required>
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">اشتراک</label>
                        <select class="select2" name="subscription">
                            <option></option>
                            <option value="online-class" <?php if ($subscription == "online-class")
                                echo "selected"; ?>>
                                کلاس های آنلاین</option>
                            <option value="class-book" <?php if ($subscription == "class-book")
                                echo "selected"; ?>>کلاس
                                بوک ها</option>
                            <option value="test-book" <?php if ($subscription == "test-book")
                                echo "selected"; ?>>تست بوک
                                ها</option>
                            <option value="exam" <?php if ($subscription == "exam")
                                echo "selected"; ?>>آزمون های کیت
                            </option>
                            <option value="counsel" <?php if ($subscription == "counsel")
                                echo "selected"; ?>>خوب یار
                            </option>
                            <option value="all" <?php if ($subscription == "all")
                                echo "selected"; ?>>اشتراک خوب</option>
                        </select>
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">پایه تحصیلی</label>
                        <select class="select2" name="grade">
                            <option></option>
                            <?php foreach ($grades as $sgrade): ?>
                                <option value="<?php echo $sgrade['tag_id']; ?>" <?php if ($sgrade['tag_id'] == $grade)
                                       echo "selected"; ?>><?php echo $sgrade['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">رشته تحصیلی</label>
                        <select class="select2" name="fos">
                            <option></option>
                            <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field['tag_id']; ?>" <?php if ($field['tag_id'] == $fos)
                                       echo "selected"; ?>><?php echo $field['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">رمز عبور</label>
                        <input onkeyup="replace_digits(this);" name="password" class="form-control" type="password">
                    </div>
                    <div class="col-12">
                        <label class="form-label">دسترسی ها</label>
                        <div class="row">
                            <?php foreach ($access_array as $title => $key): ?>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input <?php if (strpos($user_access, $key) !== false)
                                            echo "checked" ?>
                                                type="checkbox" name="user_access[]" value="<?php echo $key ?>"
                                            id="flexSwitchCheckChecked" class="form-check-input">
                                        <label class="form-check-label small"><?php echo $title ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
                                    <th class="all">آیدی</th>
                                    <th class="all">نام</th>
                                    <th class="all">شماره تلفن</th>
                                    <th>دوره درخواستی</th>
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
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<?php if (empty($birthday))
    $birthday = intval(time()); ?>
<script>
    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'انتخاب',
                dropdownParent: $this.parent()
            });
        });
    }
    const flatpickrDate = document.querySelector('#flatpickr-date');
    if (flatpickrDate) {
        flatpickrDate.flatpickr({
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            defaultDate: ["<?php echo $birthday; ?>"]
        });
    }
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
                    url: 'API/v1/GetUsers.php?type=student'
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
                        data: 'course'
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
                    responsivePriority: 5,
                    render: function (data, type, full, meta) {
                        var $id = full['user_id'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                    }
                },
                {
                    // User full name 
                    targets: 1,
                    responsivePriority: 4,
                    render: function (data, type, full, meta) {
                        var $name = full['name'],
                            $id = full['user_id'];
                        var $row_output =
                            '<a href="index.php?page=user/profile.php&uid=' + $id + '">' +
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
                    render: function (data, type, full, meta) {
                        var $phone_number = full['phone_number'];
                        return "<span class='text-truncate d-flex align-items-center'>" + $phone_number + '</span>';
                    }
                },
                {
                    // email
                    targets: 3,
                    render: function (data, type, full, meta) {
                        var $email = full['course'];

                        return '<span class="fw-semibold">' + $email + '</span>';
                    }
                },
                {
                    // date
                    targets: 4,
                    render: function (data, type, full, meta) {
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
                    render: function (data, type, full, meta) {
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
                            header: function (row) {
                                var data = row.data();
                                return 'جزئیات کاربر ' + data['name'];
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