<?php
session_start();
include_once("../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/user.php");
$grades = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_grade'");
$fields = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_course'");


if(isset($_POST['submit'])){
    $code = intval($_POST['Code']);
    $number = $_POST['Number'];
    $name = htmlspecialchars($_POST['name']);
    $fname = htmlspecialchars($_POST['fname']);
    $grade = explode('-',$_POST['Grade']);
    $grade_name = htmlspecialchars($grade[1]);
    $grade_id = intval($grade[0]);
    $field = explode('-',$_POST['Field']);
    $field_name = htmlspecialchars($field[1]);
    $field_id = intval($field[0]);
    $course = htmlspecialchars($_POST['course']);
    // print_r($_POST);
    
    if(preg_match("/09\d{9}/m",$number) == 1 && $grade_id > 0 && $field_id > 0 && preg_match("/\d{4}/m",$code) == 1){
        $obj = new user($number);
        $user_id = $obj->get_id();
        $sent_code = $obj->get_user_meta('code');
        if($code == $sent_code){
            $obj->set_display_name($name);
            $obj->set_nick_name($name." ".$fname);
            $obj->set_login($number);
            $obj->set_password($sent_code);
            $metas = [
                'grade'         => $grade_id,
                'fos'           => $field_id,
                'phonenumber'   => $number,
                'class_groups'  => '',
                "role"          => "student",
                "course"        => $course,
                "firstname"     => $name,
                "lastname"      => $fname,
                
            ];
            $obj->insert_user_meta($metas);
            $select_q = "SELECT `post_id` FROM `post` WHERE `post_title` LIKE '% $grade_name%' AND `post_title` LIKE '% $field_name%' AND `post_title` LIKE '% $course%' AND `post_type` = 'class-book';";
            $post_id = base::FetchAssoc($select_q)['post_id'];
            if($post_id){
                base::send_sms($number, "کاربر گرامی
                یه صد بیست خوش آمدید
                نام کاربری : $number
                رمز عبور : $code
                لینک ورود به پنل : https://my10020.ir/panel");
                $user_classes = json_encode([$post_id => []], JSON_UNESCAPED_UNICODE);
                $obj->insert_user_meta(['classes' => $user_classes]);
                $_SESSION['uid'] = $user_id;
                $_SESSION['LAST_ACTIVITY'] = intval(time()) + (86400 * 14);
                base::redirect(site_url."/panel/index.php?page=product/view.php&id=$post_id");
            }
        }else{
            echo "<script>alert('کد وارد شده اشتباه است')</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fa" class="light-style layout-navbar-fixed layout-menu-fixed" dir="rtl" data-theme="theme-default" data-assets-path="panel/assets/" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <title>فرم ثبت نام</title>

    <meta name="description" content="">

    <!-- Favicon -->
    <link rel="icon" href="assets/img/icon.png">
    <meta name="msapplication-TileImage" content="assets/img/icon.png" />
    <link rel="apple-touch-icon" href="assets/img/icon.png" />

    <!-- Icons -->
    <link rel="stylesheet" href="panel/assets/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="panel/assets/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="panel/assets/vendor/fonts/flag-icons.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/core.css">
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/theme-semi-dark.css">
    <link rel="stylesheet" href="panel/assets/css/demo.css">
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/rtl.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="panel/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="panel/assets/vendor/libs/typeahead-js/typeahead.css">
    <link rel="stylesheet" href="panel/assets/vendor/libs/bootstrap-select/bootstrap-select.css">
    <link rel="stylesheet" href="panel/assets/vendor/libs/select2/select2.css">
    <link rel="stylesheet" href="panel/assets/vendor/libs/dist/css/.min.css">
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="panel/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="panel/assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="container">
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="text-center mt-5">
                        <img src="panel/assets/img/logo_color.png" style="height: 150px;max-width: 100%;object-fit: contain;" class="mx-auto mb-4">
                    </div>
                    <!-- Default -->
                    <div class="row">
                        <!-- Validation Wizard -->
                        <div class="col-12 mb-4">
                            <form action="" method="POST" class="card-body card">
                            <h4 class="py-3 breadcrumb-wrapper mb-4">فرم ثبت نام</h4>
                                <!-- Account Details -->
                                <div id="account-details" class="content">
                                    <div class="content-header mb-3">
                                        <h6 class="mb-1">اطلاعات اولیه</h6>
                                        <small>اطلاعات خود را وارد کنید.</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="name">نام</label>
                                            <input type="text" name="name" id="name" class="form-control" oninvalid="this.setCustomValidity('نام خود را وارد کنید')" placeholder="نام خود را وارد کنید" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="fname">نام خانوادگی</label>
                                            <input type="text" name="fname" id="fname" class="form-control" oninvalid="this.setCustomValidity('نام خانوادگی خود را وارد کنید')" placeholder="نام خانوادگی خود را وارد کنید" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="Grade">پایه تحصیلی</label>
                                            <select class="select2" name="Grade" oninvalid="this.setCustomValidity('پایه تحصیلی خود را انتخاب کنید')" id="Grade" required>
                                                <option></option>
                                                <?php foreach ($grades as $sgrade) : ?>
                                                    <option value="<?php echo $sgrade['tag_id'].'-'.$sgrade['name']; ?>"><?php echo $sgrade['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="Field">رشته تحصیلی</label>
                                            <select class="select2" name="Field" oninvalid="this.setCustomValidity('رشته تحصیلی خود را انتخاب کنید')" id="Field" required>
                                                <option></option>
                                                <?php foreach ($fields as $field) : ?>
                                                    <option value="<?php echo $field['tag_id'].'-'.$field['name']; ?>"><?php echo $field['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="course">دوره درخواستی</label>
                                            <select class="select2" name="course" oninvalid="this.setCustomValidity('دوره درخواستی خود را انتخاب کنید')" id="course" required>
                                                <option></option>
                                                <option value="حضوری">حضوری</option>
                                                <option value="آنلاین">آنلاین</option>
                                                <option value="پکیج">پکیج</option>
                                                <option value="مشاوره">برنامه مطالعاتی</option>
                                            </select>
                                        </div>
                                        <hr>
                                        <div class="col-sm-6 mx-auto">
                                            <label class="form-label" for="Number">شماره تماس</label>
                                            <input type="tel" name="Number" id="Number" class="form-control text-start" dir="ltr" pattern="^09\d{9}$" placeholder="09*********">
                                            <span id="login-message"></span>
                                        </div>
                                        <div class="col-sm-6 mx-auto">
                                            <label class="form-label" for="Code">کد تایید</label>
                                            <input type="text" name="Code" id="Code" class="form-control" pattern="^\d{4}$" placeholder="کد احراز هویت خود را وارد کنید">
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <button type="button" onclick="get_code($('#Number').val())" class="btn btn-primary  btn-submit">ارسال کد</button>
                                            <button type="submit" name="submit" class="btn btn-primary  btn-submit">ثبت</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="content-backdrop fade"></div>
                </div>
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->
        <script>
            var getJSON = function(url, callback) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.responseType = 'json';
                xhr.onload = function() {
                    var status = xhr.status;
                    if (status === 200) {
                        products = [];
                        callback(null, xhr.response);
                    } else {
                        callback(status, xhr.response);
                    }
                };

                xhr.send();
            };
            function get_code(number) {
                if(number.length == 11){
                    getJSON('panel/API/v1/Send-sms.php?phone=' + number, function(err, data) {
                        $('#login-message').text(data);
                    });
                }else{
                    alert('شماره تلفن وارد شده نادرست است');
                }
            }
        </script>
        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script src="panel/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="panel/assets/vendor/js/bootstrap.js"></script>
        <script src="panel/assets/vendor/libs/select2/select2.js"></script>
        <script src="panel/assets/vendor/libs/jdate/jdate.js"></script>
        <script src="panel/assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
        <script src="panel/assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
        <!-- Main JS -->
        <script src="panel/assets/js/main.js"></script>

        <!-- Page JS -->

        <script src="panel/assets/js/form-wizard-numbered.js"></script>
        <script src="panel/assets/js/form-wizard-validation.js"></script>
        <script src="panel/assets/js/main.js"></script>
    </div>
</body>

</html>