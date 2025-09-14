<?php
session_start();
include_once("../includes/config.php");
include_once(base_dir . "/includes/classes/user.php");
$grades = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_grade'");
$fields = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` WHERE `type` = 'study_course'");
if ($_SESSION['uid'] != 0 || $_SESSION['uid'] != null)
  base::redirect("index.php");
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 180000)) {
  base::redirect("index.php");
}
$error = '';
if (isset($_POST['phone-number']) && isset($_POST['password'])) {
  $name = htmlspecialchars($_POST['name']);
  // if ($_POST['fname'])
  //   $name .= ' ' . htmlspecialchars($_POST['fname']);
  $username = $_POST['phone-number'];
  if (preg_match("/09\d{9}/m", $username) == 1) {
    $password = $_POST['password'];
    $remember = $_POST['remember'];
    $login_query = "SELECT `user_id` FROM `users` WHERE `login` = '$username'";
    $uid = base::FetchAssoc($login_query)['user_id'];
    if ($uid > 0) {
      $error = "<small class='alert alert-dark alert-dismissible d-flex align-items-center mb-2 p-2 py-1' role='alert'>این شماره توسط کاربری دیگر استفاده شده است!<button type='button' class='btn-close p-2' data-bs-dismiss='alert' aria-label='Close'></button></small>";
    } else {
      if (!empty($password) && !empty($username)) {
        $obj = new user($username);
        $uid = $obj->get_id();
        $obj->set_display_name($name);
        $obj->set_nick_name($name);
        $obj->set_login($username);
        if (strlen($password) > 0)
          $obj->set_password($password);
        $metas = [
          "role" => "student",
          "firstname" => $_POST['name'],
          "lastname" => $_POST['fname'],
          "phonenumber" => "$username",
          'grade' => "{$_POST['grade']}",
          'fos' => "{$_POST['fos']}"
        ];
        $obj->insert_user_meta($metas);
        $_SESSION['uid'] = $uid;
        $_SESSION['LAST_ACTIVITY'] = intval(time());
        base::send_sms($username, "سلام $name!
        به صد بیست خوش اومدی
        ما تا پایان مسیر موفقیت 
        در کنارت هستیم 
            
        #اینبار_خوب_یاد_بگیر");
        base::redirect("index.php");
      } else {
        $error = "<small class='alert alert-dark alert-dismissible d-flex align-items-center mb-2 p-2 py-1' role='alert'>رمز عبور را وارد کنید!<button type='button' class='btn-close p-2' data-bs-dismiss='alert' aria-label='Close'></button></small>";
      }
    }
  } else {
    $error = "<small class='alert alert-dark alert-dismissible d-flex align-items-center mb-2 p-2 py-1' role='alert'>شماره تلفن وارد شده صحیح نمیباشد!<button type='button' class='btn-close p-2' data-bs-dismiss='alert' aria-label='Close'></button></small>";
  }
}

?>
<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="assets/"
  data-template="vertical-menu-template">

<head>
  <meta charset="utf-8">
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

  <title>ثبت نام | خوب یاد</title>

  <meta name="description" content="">

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
  <!-- Vendor -->
  <link rel="stylesheet" href="assets/vendor/libs/formvalidation/dist/css/formValidation.min.css">
  <link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">

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

<body>
  <!-- Content -->

  <div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center"
        style="background:url('assets/img/Khoobyad-login.webp') no-repeat center;background-size: cover;">
      </div>
      <!-- /Left Text -->

      <!-- Register -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="text-center mb-4">
            <img width="200px" src="assets/img/logo_color.png">
          </div>
          <!-- /Logo -->
          <h4 class="mb-2">ماجراجویی اینجا شروع می‌شود</h4>
          <p class="mb-4">هم بیست بگیر، هم صد بزن</p>

          <form id="formAuthentication" class="mb-3" method="POST">
            <?php echo $error; ?>
            <div class="mb-3">
              <label for="name" class="form-label">نام</label>
              <input type="text" class="form-control text-start persian-only" onkeyup="replace_digits(this);" dir="ltr"
                id="name" name="name" placeholder="نام خود را وارد کنید" autofocus>
            </div>
            <div class="mb-3">
              <label for="fname" class="form-label">نام خانوادگی</label>
              <input type="text" class="form-control text-start persian-only" dir="ltr" onkeyup="replace_digits(this);"
                id="fname" name="fname" placeholder="نام خانوادگی خود را وارد کنید" autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">پایه تحصیلی</label>
              <select class="select2 form-control" name="grade">
                <option></option>
                <?php foreach ($grades as $sgrade): ?>
                  <option value="<?php echo $sgrade['tag_id']; ?>" <?php if ($sgrade['tag_id'] == $grade)
                       echo "selected"; ?>><?php echo $sgrade['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">رشته تحصیلی</label>
              <select class="select2 form-control" name="fos">
                <option></option>
                <?php foreach ($fields as $field): ?>
                  <option value="<?php echo $field['tag_id']; ?>" <?php if ($field['tag_id'] == $fos)
                       echo "selected"; ?>>
                    <?php echo $field['name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="phone-number" class="form-label">شماره تلفن</label>
              <input type="text" pattern="[09][0-9]{9}" class="form-control text-start" dir="ltr"
                onkeyup="replace_digits(this);" id="phone-number" name="phone-number"
                placeholder="شماره تلفن خود را وارد کنید" autofocus required>
            </div>
            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">رمز عبور</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" onkeyup="replace_digits(this);" class="form-control text-start"
                  dir="ltr" name="password" placeholder="············" aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            <button type="submit" class="btn btn-primary d-grid w-100">ثبت نام</button>
          </form>

          <p class="text-center">
            <span>حساب کاربری دارید؟</span>
            <a href="/panel/login.php">
              <span>وارد شوید</span>
            </a>
          </p>
        </div>
      </div>
      <!-- /Register -->
    </div>
  </div>

  <!-- / Content -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/select2/select2.js"></script>
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
    const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))

    function replace_digits(input) {
      var temp_inp = input.value;
      var replaced = p2e(temp_inp);
      input.value = replaced;
    }
    $(".persian-only").on('change keyup paste keydown', function (e) {
      if (just_persian(e.key) === false)
        e.preventDefault();
    });


    function just_persian(str) {
      var p = /^[\u0600-\u06FF\s]+$/;
      if (!p.test(str) && (str != 'Backspace' && str != 'Delete')) {
        return false
      }
      return true;
    }
  </script>
</body>

</html>