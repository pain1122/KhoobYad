<?php
session_start();
include_once("../includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
if ($_SESSION['uid'] != 0 || $_SESSION['uid'] != null)
    base::redirect("index.php");
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 180000)) {
    base::redirect("index.php");
}
$error = '';
?>
<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <title>ورود به پنل | صد 20</title>

    <meta name="description" content="">
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <!-- Favicon -->
    <link rel="icon" href="assets/img/icon.png">
    <meta name="msapplication-TileImage" content="assets/img/icon.png" />
    <link rel="apple-touch-icon" href="assets/img/icon.png" />

    <!-- Icons -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-semi-dark.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="assets/css/demo.css">
    <link rel="stylesheet" href="assets/vendor/css/rtl/rtl.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
    <link rel="stylesheet" href="assets/vendor/libs/sweetalert2/sweetalert2.css">
    <script src="assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <!-- Vendor -->
    <link rel="stylesheet" href="assets/vendor/libs/formvalidation/dist/css/formValidation.min.css">

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css">
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
</head>
<?php

if (isset($_POST['phone-number'])) {

    if (isset($_POST['password'])) {
        $username = $_POST['phone-number'];
        $password = md5($_POST['password']);
        $remember = $_POST['remember'];
        $login_query = "SELECT `user_id` FROM `users` WHERE `login` = '$username' AND  `password` = '$password' ";
        $uid = base::FetchAssoc($login_query)['user_id'];
        if ($uid > 0)
            $role = base::FetchAssoc("SELECT `value` FROM `user_meta` WHERE `user_id` = '$uid' AND  `key` = 'role'");
        if ($uid > 0) {
            $_SESSION['uid'] = $uid;
            if (isset($remember)) {
                $_SESSION['LAST_ACTIVITY'] = intval(time()) + (86400 * 14);
            } else {
                $_SESSION['LAST_ACTIVITY'] = intval(time());
            }
            if($_SESSION['turn-back-url'])
                base::redirect($_SESSION['turn-back-url']);
            elseif ($role == "teacher") {
                base::redirect('https://khoobyad.ir/panel/index.php?page=counsel/tickets.php&status=debug');
            } else
                base::redirect("index.php");
        } else {
            $error = "<small class='alert alert-dark alert-dismissible d-flex align-items-center mb-2 p-2 py-1' role='alert'>کاربری با این مشخصات وجود ندارد!<button type='button' class='btn-close p-2' data-bs-dismiss='alert' aria-label='Close'></button></small>";
        }
    } else {
        $username = $_POST['phone-number'];
        $obj = new user($username);
        $password = base::Generate_Random(4);
        $obj->set_password($password);
        base::send_sms($username, "رمز ورود جدید شما:
              $password");
    }
}
?>

<body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center" style="background:url('assets/img/Khoobyad-login.webp') no-repeat center;background-size: cover;">
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img width="200px" src="assets/img/logo_color.png">
                    </div>
                    <!-- /Logo -->
                    <h5 class="mb-2">به پنل اختصاصی صد بیست خوش آمدید!</h5>
                    <p class="mb-4">هم بیست بگیر، هم صد بزن</p>

                    <form id="formAuthentication" class="mb-3" method="post">
                        <?php echo $error; ?>
                        <div class="mb-3">
                            <label class="form-label">نام کاربری / شماره تلفن</label>
                            <input type="text" id="phone-number-mask" class="form-control phone-number-mask text-start" dir="ltr" name="phone-number" placeholder="نام کاربری / شماره تلفن خود را وارد کنید" autofocus required>
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">رمز عبور</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control text-start" dir="ltr" name="password" placeholder="············" aria-describedby="password">
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember">
                                <label class="form-check-label"> مرا به خاطر بسپار </label>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary d-grid w-100">ورود</button>
                        <br>
                        <div class="mb-3">
                            <input type="submit" name="otp" id="otp" value="ارسال رمز جدید" class="btn btn-primary d-grid w-100">
                        </div>
                    </form>
                    <p class="text-center">
                        <span>حساب کاربری ندارید؟</span>
                        <a href="/panel/sign-in.php">
                            <span>ثبت نام</span>
                        </a>
                    </p>

                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/libs/hammer/hammer.js"></script>

    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/pages-auth.js"></script>
    <script>
        jQuery('#otp').click(function(event) {
            event.preventDefault();
            var newForm = jQuery('<form>', {
                'action': '/panel/login.php',
                'method': 'POST'
            }).append(jQuery('<input>', {
                'name': 'otp',
                'value': 'otp',
                'type': 'hidden'
            })).append(jQuery('<input>', {
                'name': 'phone-number',
                'value': document.getElementById("phone-number-mask").value
            }));
            $(document.body).append(newForm);
            newForm.submit();
        });
    </script>

</body>

</html>