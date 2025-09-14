<?php
if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    base::RunQuery("DELETE `post`,`post_meta`,`items_order`,`items_order_meta` 
    FROM `post`
    INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id`
    INNER JOIN `items_order` ON `items_order`.`order_id` = `post`.`post_id`
    INNER JOIN `items_order_meta` ON `items_order`.`items_order_id` = `items_order_meta`.`order_item_id`
    WHERE `post`.`post_id` = " . $post_id);
}

if (isset($_POST['submit'])) {
    $user_id = $_GET['uid'];
    $user = new user($user_id);
    $user->set_nick_name($_POST['firstname'] . ' ' . $_POST['lastname']);
    $user->set_user_email($_POST['email']);
    if (!empty($_POST['password'])) {
        $password = md5($password);
        $user->set_password($_POST['password']);
    }
    if (is_countable($_POST['classes']) && count($_POST['classes']) > 0) {
        $classes = implode(',', $_POST['classes']);
    } else {
        $classes = '';
    }
    $metas = [
        'subscription' => "{$_POST['subscription']}",
        'grade' => "{$_POST['grade']}",
        'fos' => "{$_POST['fos']}",
        'school' => "{$_POST['school']}",
        'adviser' => "{$_POST['adviser']}",
        'address' => "{$_POST['address']}",
        'phonenumber' => "{$_POST['phone_number']}",
        'birth' => "{$_POST['birthday']}",
        'gender' => "{$_POST['gender']}",
        'nid' => "{$_POST['nid']}",
        'hphone' => "{$_POST['hphone']}",
        'firstname' => "{$_POST['firstname']}",
        'lastname' => "{$_POST['lastname']}",
        'class_groups' => $classes,
        'instagram' => "{$_POST['instagram']}",
        'profile' => 'student'
    ];
    if (strlen($_FILES['avatar']['tmp_name']) > 0) {
        $avatar = base::Upload($_FILES['avatar']);
        $metas['avatar'] = $avatar;
    }
    $user->insert_user_meta($metas);
}
$user_id = $_GET['uid'];
$user = new user($user_id);
$username = $user->get_nick_name();
$email = $user->get_user_email();
$subscription = $user->get_user_meta('subscription');
$grade = $user->get_user_meta('grade');
$fos = $user->get_user_meta('fos');
$school = $user->get_school();
$adviser = $user->get_user_meta('adviser');
$address = $user->get_user_meta('address');
$phone_number = $user->get_user_meta('phonenumber');
$birthday = $user->get_user_meta('birth');
$gender = $user->get_user_meta('gender');
$nid = $user->get_user_meta('nid');
$hphone = $user->get_user_meta('hphone');
$firstname = $user->get_user_meta('firstname');
$lastname = $user->get_user_meta('lastname');
$instagram = $user->get_user_meta('instagram');
$avatar = $user->get_user_meta('avatar');
$class_groups = array_filter(explode(',', $user->get_user_meta('class_groups')));


$classes = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'class_group'");
$grades = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_grade'");
$fields = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_course'");
$advisers = base::FetchArray("SELECT `users`.`user_id`,`nicename` FROM `users` INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id` WHERE `key` = 'role' AND `value` = 'adviser'");
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<div class="card card-body">
    <form autocomplete="off" class="theme-form row" action="" method="post" enctype="multipart/form-data">
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">نام</label>
            <input name="firstname" class="form-control" type="text" value="<?php echo $firstname; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">نام خانوادگی</label>
            <input name="lastname" class="form-control" type="text" value="<?php echo $lastname; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">کد ملی</label>
            <input onkeyup="replace_digits(this);" type="number" name="nid" class="form-control"
                value="<?php echo $nid; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">شماره تلفن</label>
            <input name="phone_number" class="form-control" type="tel" value="<?php echo $phone_number; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">ایمیل</label>
            <input name="email" class="form-control" type="email" value="<?php echo $email; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">شماره ثابت</label>
            <input type="text" name="hphone" class="form-control" value="<?php echo $hphone; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">تاریخ تولد</label>
            <input type="text" name="birthday" class="form-control" placeholder="YYYY/MM/DD" id="flatpickr-date"
                required>
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">اینستاگرام</label>
            <input type="text" name="instagram" class="form-control" value="<?php echo $instagram; ?>">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">آدرس</label>
            <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
        </div>
        <?php if ($role == 'school' || $role == 'admin') { ?>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">نام مدرسه</label>
                <input type="text" name="school" class="form-control" value="<?php echo $school; ?>">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">مشاور تحصیلی</label>
                <select class="select2" name="adviser">
                    <option></option>
                    <?php foreach ($advisers as $sadviser): ?>
                        <option value="<?php echo $sadviser['user_id']; ?>" <?php if ($sadviser['user_id'] == $adviser)
                               echo "selected"; ?>><?php echo $sadviser['nicename']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">کلاس ها</label>
                <select class="select2" name="classes[]" multiple="multiple">
                    <option></option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['tag_id']; ?>" <?php if (in_array($class['tag_id'], $class_groups))
                               echo "selected"; ?>><?php echo $class['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">اشتراک</label>
                <select class="select2" name="subscription">
                    <option></option>
                    <option value="online-class" <?php if ($subscription == "online-class")
                        echo "selected"; ?>>کلاس های
                        آنلاین</option>
                    <option value="class-book" <?php if ($subscription == "class-book")
                        echo "selected"; ?>>کلاس بوک ها
                    </option>
                    <option value="test-book" <?php if ($subscription == "test-book")
                        echo "selected"; ?>>تست بوک ها</option>
                    <option value="exam" <?php if ($subscription == "exam")
                        echo "selected"; ?>>آزمون های کیت</option>
                    <option value="counsel" <?php if ($subscription == "counsel")
                        echo "selected"; ?>>خوب یار</option>
                    <option value="all" <?php if ($subscription == "all")
                        echo "selected"; ?>>اشتراک خوب</option>
                </select>
            </div>
        <?php } ?>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">پایه تحصیلی</label>
            <select class="select2" name="grade">
                <option></option>
                <?php foreach ($grades as $sgrade): ?>
                    <option value="<?php echo $sgrade['tag_id']; ?>" <?php if ($sgrade['tag_id'] == $grade)
                           echo "selected"; ?>><?php echo $sgrade['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">رشته تحصیلی</label>
            <select class="select2" name="fos">
                <option></option>
                <?php foreach ($fields as $field): ?>
                    <option value="<?php echo $field['tag_id']; ?>" <?php if ($field['tag_id'] == $fos)
                           echo "selected"; ?>>
                        <?php echo $field['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label d-block mb-2">جنسیت</label>
            <div class="d-inline-flex align-items-center">
                <input name="gender" class="form-check-input mt-0 ml-1" id="type-simple" type="radio" value="خانم" <?php if ($gender == 'خانم' || !$gender)
                    echo 'checked' ?>>
                    <label class="form-check-label ml-3"> خانم </label>
                </div>
                <div class="d-inline-flex align-items-center">
                    <input name="gender" class="form-check-input mt-0 ml-1" id="type-variable" type="radio" value="آقا"
                    <?php if ($gender == 'آقا')
                    echo 'checked' ?>>
                    <label class="form-check-label"> آقا </label>
                </div>
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">رمز عبور</label>
                <input onkeyup="replace_digits(this);" name="password" class="form-control" type="password">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <label class="form-label">انتخاب آواتار</label>
                <input type="file" class="form-control" accept="image/*" onchange="loadFile(this,event)" name="avatar">
            </div>
            <div class="mb-3 col-12 col-md-6 col-lg-4">
                <img src="<?php echo base::displayphoto($avatar); ?>" id="avatar" height="60px" class="ml-auto d-block">
        </div>
        <div class="mb-3 col-12 col-md-6 col-lg-4">
            <label class="form-label">دوره درخواستی : <?php echo $user->get_user_meta('course') ?></label>
        </div>
        <div class="col-12">
            <button type="submit" name="submit" class="btn btn-primary btn-pill"> ذخیره کنید </button>
        </div>
    </form>
</div>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<?php if (empty($birthday))
    $birthday = intval(time()); ?>
<script>
    function imageReplace(name, event) {
        var output = document.getElementById(name);
        output.src = URL.createObjectURL(event.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // free memory
        }
    };

    var loadFile = function (event, elem) {
        var index = elem.name;
        index = index.replace(/[^a-zA-Z0-9]/g, '_')
        imageReplace(index, event);
    };
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
</script>