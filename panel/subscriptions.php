<?php session_start();
include_once("../includes/config.php");
include_once(base_dir
    . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir
    . "/includes/classes/user.php");
$grades = base::FetchArray("SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN
    `tag_meta` ON `tag`.`tag_id`=`tag_meta`.`tag_id` WHERE `type`='study_grade'");
$fields = base::FetchArray(" SELECT `tag`.`tag_id`,`name` FROM `tag` INNER JOIN `tag_meta` ON
    `tag`.`tag_id`=`tag_meta`.`tag_id` WHERE `type`='study_course'");


if (isset($_POST['submit'])) {
    $code = intval($_POST['Code']);
    $number = $_POST['Number'];
    $name = htmlspecialchars($_POST['name']);
    $fname = htmlspecialchars($_POST['fname']);
    $grade = explode('-', $_POST['Grade']);
    $grade_name = htmlspecialchars($grade[1]);
    $grade_id = intval($grade[0]);
    $field = explode('-', $_POST['Field']);
    $field_name = htmlspecialchars($field[1]);
    $field_id = intval($field[0]);
    $course = htmlspecialchars($_POST['course']);
    // print_r($_POST);

    if (preg_match(" /09\d{9}/m", $number) == 1 && $grade_id > 0 && $field_id > 0 && preg_match("/\d{4}/m", $code) == 1) {
        $obj = new user($number);
        $user_id = $obj->get_id();
        $sent_code = $obj->get_user_meta('code');
        if ($code == $sent_code) {
            $obj->set_display_name($name);
            $obj->set_nick_name($name . " " . $fname);
            $obj->set_login($number);
            $obj->set_password($sent_code);
            $metas = [
                'grade' => $grade_id,
                'fos' => $field_id,
                'phonenumber' => $number,
                'class_groups' => '',
                "role" => "student",
                "course" => $course,
                "firstname" => $name,
                "lastname" => $fname,

            ];
            $obj->insert_user_meta($metas);
            $select_q = "SELECT `post_id` FROM `post` WHERE `post_title` LIKE '% $grade_name%' AND `post_title` LIKE '%
    $field_name%' AND `post_title` LIKE '% $course%' AND `post_type` = 'class-book';";
            $post_id = base::FetchAssoc($select_q)['post_id'];
            if ($post_id) {
                base::send_sms($number, "کاربر گرامی
    یه خوب یاد خوش آمدید
    نام کاربری : $number
    رمز عبور : $code
    لینک ورود به پنل : https://khoobyad.ir/panel/");
                $user_classes = json_encode([$post_id => []], JSON_UNESCAPED_UNICODE);
                $obj->insert_user_meta(['classes' => $user_classes]);
                $_SESSION['uid'] = $user_id;
                $_SESSION['LAST_ACTIVITY'] = intval(time()) + (86400 * 14);
                base::redirect(site_url . "/panel/index.php?page=product/view.php&id=$post_id");
            }
        } else {
            echo "
    <script>alert('کد وارد شده اشتباه است')</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fa" class="light-style layout-navbar-fixed layout-menu-fixed" dir="rtl" data-theme="theme-default"
    data-assets-path="panel/assets/" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <title>خرید اشتراک</title>

    <meta name="description" content="">
    <script src="panel/assets/vendor/libs/jquery/jquery.js"></script>
    <!-- Favicon -->
    <link rel="icon" href="panel/assets/img/icon.png">
    <meta name="msapplication-TileImage" content="panel/assets/img/icon.png" />
    <link rel="apple-touch-icon" href="panel/assets/img/icon.png" />

    <!-- Icons -->
    <link rel="stylesheet" href="panel/assets/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="panel/assets/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="panel/assets/vendor/fonts/flag-icons.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/core.css">
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/theme-semi-dark.css">
    <link rel="stylesheet" href="panel/assets/css/demo.css">
    <link rel="stylesheet" href="panel/assets/vendor/css/rtl/rtl.css">
    <link rel="stylesheet" href="panel/assets/vendor/libs/swiper/swiper.css">
    <link rel="stylesheet" href="panel/assets/vendor/css/pages/ui-carousel.css">

    <!-- Vendors CSS -->
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
                    <div class="card">
                        <!-- Pricing Plans -->
                        <div class="pb-sm-5 pb-2 rounded-top">
                            <div class="container py-5">
                                <div class="text-center">
                                    <img src="panel/assets/img/logo_color.png"
                                        style="height: 150px;max-width: 100%;object-fit: contain;" class="mx-auto mb-4">
                                </div>
                                <h2 class="text-center mb-3 secondary-font">اشتراک مناسب برای موفقیت در درس‌هایتان
                                    را
                                    انتخاب کنید</h2>
                                <p class="text-center">
                                    با اشتراک در پلتفرم آموزشی ما، مسیر یادگیری خود را هموار کنید. پلن‌های متنوع با
                                    امکانات حرفه‌ای، مناسب برای دانش‌آموزان، والدین و مشاوران آموزشی.
                                </p>
                                <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 py-5">
                                    <label class="switch switch-primary ms-sm-5 ps-sm-5 me-0">
                                        <span class="switch-label ps-0 ps-sm-2">ماهانه</span>
                                        <input type="checkbox" class="switch-input price-duration-toggler" checked="">
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label">سالانه</span>
                                    </label>
                                    <div class="mt-n5 ms-n5 ml-2 mb-2 d-none d-sm-block">
                                        <i class="bx bx-subdirectory-left bx-sm rotate-90 text-muted"></i>
                                        <span class="badge badge-sm bg-label-primary rounded-pill">۲ ماه اشتراک
                                            رایگان
                                            با پرداخت سالانه</span>
                                    </div>
                                </div>
                                <div class="swiper" id="swiper-multiple-slides"
                                    style="height: unset !important; position: relative; overflow: hidden;padding-bottom: 50px;">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <h5 class="text-start text-uppercase">کلاس های آنلاین</h5>
                                                    <div class="text-center position-relative mt-n1 mb-4 pb-3">
                                                        <div class="d-flex align-items-center">
                                                            <h1 class="price-toggle text-primary price-yearly mb-0">
                                                                <small>تومان</small> 12,000,000
                                                            </h1>
                                                            <h1
                                                                class="price-toggle text-primary price-monthly mb-0 d-none">
                                                                <small>تومان</small> 1,500,000
                                                            </h1>
                                                            <sub
                                                                class="h5 text-muted pricing-duration mb-1 mb-md-0 ms-1">
                                                                /
                                                                ماهانه</sub>
                                                        </div>
                                                        <small
                                                            class="position-absolute start-0 m-auto price-yearly price-yearly-toggle text-muted">1,000,000
                                                            تومان / سال</small>
                                                    </div>
                                                    <hr>
                                                    <ul class="list-unstyled pt-2 pb-1 lh-1-85">
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            برترین اساتید کشور
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            دسترسی ۷ روز هفته و ۲۴ ساعته
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            پشتیبانی فرد به فرد
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            امکان شرکت در کلاس ها به صورت مبحثی
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            همگام با کلاس های حضوری خوب یاد
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-label-primary d-grid w-100">شروع
                                                        کنید</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="card border border-2 border-primary">
                                                <div class="card-body">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                                        <h5 class="text-start text-uppercase mb-0">کلاسبوک</h5>
                                                        <span class="badge bg-primary rounded-pill">محبوب</span>
                                                    </div>
                                                    <div class="text-center position-relative mt-n1 mb-4 pb-3">
                                                        <div class="d-flex align-items-center">
                                                            <h1 class="price-toggle text-primary price-yearly mb-0">
                                                                <small>تومان</small> 12,000,000
                                                            </h1>
                                                            <h1
                                                                class="price-toggle text-primary price-monthly mb-0 d-none">
                                                                <small>تومان</small> 1,500,000
                                                            </h1>
                                                            <sub
                                                                class="h5 text-muted pricing-duration mb-1 mb-md-0 ms-1">
                                                                /
                                                                ماهانه</sub>
                                                        </div>
                                                        <small
                                                            class="position-absolute start-0 m-auto price-yearly price-yearly-toggle text-muted">1,000,000
                                                            تومان / سال</small>
                                                    </div>
                                                    <hr>
                                                    <ul class="list-unstyled pt-2 pb-1 lh-1-85">
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            بسته معلم‌ خصوصی ویدیوی
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            امکان دانلود جزوات کلاس
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            آموزش خط به خط و نکته به نکته
                                                        </li>
                                                        <li class="mb-2">
                                                            <span class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            حل سوالات تشریحی و‌ تستی استاندارد
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-primary d-grid w-100">شروع کنید</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <h5 class="text-start text-uppercase">تست بوک</h5>
                                                    <div class="text-center position-relative mt-n1 mb-4 pb-3">
                                                        <div class="d-flex align-items-center">
                                                            <h1 class="price-toggle text-primary price-yearly mb-0">
                                                                <small>تومان</small> 12,000,000
                                                            </h1>
                                                            <h1
                                                                class="price-toggle text-primary price-monthly mb-0 d-none">
                                                                <small>تومان</small> 1,500,000
                                                            </h1>
                                                            <sub
                                                                class="h5 text-muted pricing-duration mb-1 mb-md-0 ms-1">
                                                                /
                                                                ماهانه</sub>
                                                        </div>
                                                        <small
                                                            class="position-absolute start-0 m-auto price-yearly price-yearly-toggle text-muted">1,000,000
                                                            تومان / سال</small>
                                                    </div>
                                                    <hr>
                                                    <ul class="list-unstyled pt-2 pb-1 lh-1-85">
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            اولین و تنها کتاب تست آنلاین کشور
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            امکان انتخاب تست های سنجشی و آزمایشی
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            امکان زمانبندی و تایمر در حل تست آزمایشی
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            تست های استاندارد و شناسنامه دار
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            مشاهده ی درصد پاسخگویی در پایان تست زدن
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            نمایش سرفصل ها و امکان رفع اشکال با ویدیو
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            پاسخنامه تشریحی و ویدیوی تست به تست
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-label-primary d-grid w-100">شروع
                                                        کنید</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <h5 class="text-start text-uppercase">آزمون های کیت</h5>
                                                    <div class="text-center position-relative mt-n1 mb-4 pb-3">
                                                        <div class="d-flex align-items-center">
                                                            <h1 class="price-toggle text-primary price-yearly mb-0">
                                                                <small>تومان</small> 12,000,000
                                                            </h1>
                                                            <h1
                                                                class="price-toggle text-primary price-monthly mb-0 d-none">
                                                                <small>تومان</small> 1,500,000
                                                            </h1>
                                                            <sub
                                                                class="h5 text-muted pricing-duration mb-1 mb-md-0 ms-1">
                                                                /
                                                                ماهانه</sub>
                                                        </div>
                                                        <small
                                                            class="position-absolute start-0 m-auto price-yearly price-yearly-toggle text-muted">1,000,000
                                                            تومان / سال</small>
                                                    </div>
                                                    <hr>
                                                    <ul class="list-unstyled pt-2 pb-1 lh-1-85">
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            آزمونک آمادگی( پیش آزمون )
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            درسنامه مروری هفته ای
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            آزمون الکترونیکی شبیه ساز
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            پاسخنامه ی ویدیویی با تحلیل اساتید برتر
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            تخمین و‌ پیش بینی رتبه براساس کنکور
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            برنامه راهبردی جامع و‌ سالانه
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            الگوریتم مطالعاتی
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            آزمون های تشریحی شبیه ساز نهایی
                                                            <small>( در این آزمون وضعیتت فقط با خودت سنجیده میشه و
                                                                میتونی محدوده ی رتبه ت در کنکور رو ببینی)</small>
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-label-primary d-grid w-100">شروع
                                                        کنید</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <h5 class="text-start text-uppercase">خوب یار</h5>
                                                    <div class="text-center position-relative mt-n1 mb-4 pb-3">
                                                        <div class="d-flex align-items-center">
                                                            <h1 class="price-toggle text-primary price-yearly mb-0">
                                                                <small>تومان</small> 12,000,000
                                                            </h1>
                                                            <h1
                                                                class="price-toggle text-primary price-monthly mb-0 d-none">
                                                                <small>تومان</small> 1,500,000
                                                            </h1>
                                                            <sub
                                                                class="h5 text-muted pricing-duration mb-1 mb-md-0 ms-1">
                                                                /
                                                                ماهانه</sub>
                                                        </div>
                                                        <small
                                                            class="position-absolute start-0 m-auto price-yearly price-yearly-toggle text-muted">1,000,000
                                                            تومان / سال</small>
                                                    </div>
                                                    <hr>
                                                    <ul class="list-unstyled pt-2 pb-1 lh-1-85">
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            هوش مصنوعی اختصاصی خوب یار
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            برنامه ریزی و شخصی سازی برنامه مطالعاتی
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            مهندسی دقیق منابع آموزشی و‌مطالعاتی
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            گزارش گیری و‌ بررسی عملکرد فرد به فرد
                                                        </li>
                                                        <li class="mb-2"><span
                                                                class="badge badge-center bg-label-primary me-2"><i
                                                                    class="bx bx-check bx-xs"></i></span>
                                                            طراحی مجدد برنامه ی روزانه براساس عملکرد
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-label-primary d-grid w-100">شروع
                                                        کنید</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                        <!--/ Pricing Plans -->
                        <!-- Pricing Free Trial -->
                        <div class="pricing-free-trial mt-5">
                            <div class="container">
                                <div class="position-relative">
                                    <div
                                        class="d-flex justify-content-between flex-column flex-md-row align-items-center px-5 py-5 pb-md-0 pt-md-4">
                                        <!-- تصویر -->
                                        <div class="text-center">
                                            <img src="panel/assets/img/illustrations/boy-working-light.png"
                                                class="img-fluid scaleX-n1" width="300"
                                                data-app-light-img="illustrations/boy-working-light.png"
                                                data-app-dark-img="illustrations/boy-working-dark.png">
                                        </div>
                                        <!-- متن -->
                                        <div class="text-center text-md-start mt-4 mt-md-3 pt-2 pt-md-0">
                                            <h3 class="text-primary">میخوای همشونو با هم داشته باشی؟ پس بیا اشتراکِ
                                                خوب
                                                رو بگیر!</h3>
                                            <p class="fs-5">
                                                تمام قابلیت های پنل ۱۰۰۲۰
                                                <br> شامل : کلاسبوک ها + بانک تست
                                                <br> + آزمون های تستی و تشریحی + برنامه ریزی مطالعاتی
                                                <br> (یک آموزشگاه شخصی ، در دستان تو )
                                                <br> اونم فقط با هزینه دوتا پکیج!
                                            </p>
                                            <a href="#" class="btn btn-secondary mt-3 mx-2">
                                                ماهانه (3,000,000 تومان)
                                            </a>
                                            <a href="#" class="btn btn-primary mt-3 mx-2">
                                                سالانه (30,000,000 تومان)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--/ Pricing Free Trial -->
                        <!-- Updated Plans Comparison Table -->
                        <div class="pricing-plans-comparison">
                            <div class="container py-5 mt-0 my-md-4">
                                <div class="row">
                                    <div class="col-12 text-center mb-4">
                                        <h2 class="mb-2 secondary-font">پلن آموزشی خود را انتخاب کنید</h2>
                                        <p>با خیال راحت انتخاب کنید؛ بازگشت وجه تا ۴۸ ساعت تضمین‌شده است!</p>
                                    </div>
                                </div>
                                <form action="" method="post" class="row mx-0">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table text-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ویژگی‌ها</th>
                                                        <th scope="col">کلاس‌های آنلاین</th>
                                                        <th scope="col">کلاسبوک</th>
                                                        <th scope="col">تست‌بوک</th>
                                                        <th scope="col">آزمون‌های کیت</th>
                                                        <th scope="col">خوب‌یار</th>
                                                        <th scope="col" class="text-primary">اشتراک خوب</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>کلاس‌های آنلاین</td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>ویدیوهای تدریس خصوصی</td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>حل سوالات تستی و تشریحی</td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>آزمون‌های شبیه‌ساز و رتبه‌بندی</td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>برنامه‌ریزی مطالعاتی شخصی‌سازی‌شده</td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>هوش مصنوعی کمک‌آموزشی</td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>دسترسی کامل به جزوات و دانلود</td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>پشتیبانی فرد به فرد</td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-x text-secondary bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                        <td><i class="bx bx-check text-success bx-sm"></i></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="border-0"></td>
                                                        <td class="border-0"><button type="submit" value="online-class"
                                                                class="btn btn-label-secondary">کلاس‌های
                                                                آنلاینس</button></td>
                                                        <td class="border-0"><button type="submit" value="class-book"
                                                                class="btn btn-label-secondary">کلاسبوک</button>
                                                        </td>
                                                        <td class="border-0"><button type="submit" value="test-book"
                                                                class="btn btn-label-secondary">تست‌بوک</button>
                                                        </td>
                                                        <td class="border-0"><button type="submit" value="exam"
                                                                class="btn btn-label-secondary">کیت</button>
                                                        </td>
                                                        <td class="border-0"><button type="submit" value="counsel"
                                                                class="btn btn-label-secondary">خوب‌یار</button>
                                                        </td>
                                                        <td class="border-0"><button type="submit" value="all"
                                                                class="btn btn-primary">خوب</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Updated FAQs -->
                        <div class="pricing-faqs bg-alt-pricing rounded-bottom">
                            <div class="container py-5 px-lg-5">
                                <div class="row mt-0 mt-md-4">
                                    <div class="col-12 text-center mb-4">
                                        <h4 class="mb-2 secondary-font">سوالات متداول درباره پلن‌های آموزشی</h4>
                                        <p>همه‌ی آنچه باید درباره‌ی امکانات و دسترسی‌های آموزشی بدانید.</p>
                                    </div>
                                </div>
                                <div class="row mx-0">
                                    <div class="col-12">
                                        <div id="faq" class="accordion accordion-header-primary">

                                            <!-- سوال ۱ -->
                                            <div class="card accordion-item active">
                                                <h6 class="accordion-header">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#faq-allinone"
                                                        aria-expanded="true">
                                                        اشتراک «خوب» شامل چه چیزهایی می‌شود؟
                                                    </button>
                                                </h6>
                                                <div id="faq-allinone" class="accordion-collapse collapse show"
                                                    data-bs-parent="#faq">
                                                    <div class="accordion-body lh-2">
                                                        اشتراک کامل «خوب» تمامی امکانات پنل از جمله کلاس‌های آنلاین،
                                                        جزوات و ویدیوها، آزمون‌های تستی و تشریحی، برنامه‌ریزی
                                                        مطالعاتی
                                                        شخصی‌سازی‌شده و هوش مصنوعی را در اختیار شما قرار می‌دهد.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- سوال ۲ -->
                                            <div class="card accordion-item">
                                                <h6 class="accordion-header">
                                                    <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#faq-ai" aria-expanded="false">
                                                        هوش مصنوعی خوب‌یار چطور کار می‌کند؟
                                                    </button>
                                                </h6>
                                                <div id="faq-ai" class="accordion-collapse collapse"
                                                    data-bs-parent="#faq">
                                                    <div class="accordion-body lh-2">
                                                        خوب‌یار با تحلیل دقیق عملکرد شما، منابع آموزشی مناسب را
                                                        پیشنهاد
                                                        داده و برنامه مطالعاتی روزانه را بهینه‌سازی می‌کند. این
                                                        سیستم با
                                                        بررسی مداوم پیشرفت، برنامه را به‌روزرسانی می‌کند.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- سوال ۳ -->
                                            <div class="card accordion-item">
                                                <h6 class="accordion-header">
                                                    <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#faq-tests" aria-expanded="false">
                                                        تفاوت بین تست‌بوک و آزمون‌های کیت چیست؟
                                                    </button>
                                                </h6>
                                                <div id="faq-tests" class="accordion-collapse collapse"
                                                    data-bs-parent="#faq">
                                                    <div class="accordion-body lh-2">
                                                        تست‌بوک برای تمرین تست‌های طبقه‌بندی شده با تحلیل ویدئویی هر
                                                        سوال است. در حالی که آزمون‌های کیت آزمون‌های شبیه‌ساز کنکور
                                                        با
                                                        تخمین رتبه، درسنامه مروری و آزمون تشریحی دارد.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- سوال ۴ -->
                                            <div class="card accordion-item">
                                                <h6 class="accordion-header">
                                                    <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#faq-support" aria-expanded="false">
                                                        چه نوع پشتیبانی ارائه می‌شود؟
                                                    </button>
                                                </h6>
                                                <div id="faq-support" class="accordion-collapse collapse"
                                                    data-bs-parent="#faq">
                                                    <div class="accordion-body lh-2">
                                                        بسته به پلن انتخابی، پشتیبانی می‌تواند شامل ارتباط فردی با
                                                        مشاور، تحلیل ویدئویی پاسخ‌ها، و رفع اشکال با اساتید باشد.
                                                        برای
                                                        پلن «خوب»، پشتیبانی کامل فرد به فرد فراهم است.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- سوال ۵ -->
                                            <div class="card accordion-item mb-0 mb-md-4">
                                                <h6 class="accordion-header">
                                                    <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                                        data-bs-target="#faq-cancel" aria-expanded="false">
                                                        آیا امکان لغو اشتراک و بازگشت وجه وجود دارد؟
                                                    </button>
                                                </h6>
                                                <div id="faq-cancel" class="accordion-collapse collapse"
                                                    data-bs-parent="#faq">
                                                    <div class="accordion-body lh-2">
                                                        بله. تا ۴۸ ساعت پس از خرید، در صورت نارضایتی می‌توانید
                                                        اشتراک
                                                        خود را لغو کرده و وجه پرداختی را به‌طور کامل بازپس بگیرید.
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script src="panel/assets/vendor/libs/jquery/jquery.js"></script>
        <script src="panel/assets/vendor/js/bootstrap.js"></script>
        <!-- endbuild -->
        <!-- Main JS -->
        <script src="panel/assets/js/main.js"></script>
        <script src="panel/assets/vendor/libs/swiper/swiper.js"></script>
        <script src="panel/assets/js/ui-carousel.js"></script>
        <script src="panel/assets/js/pages-pricing.js"></script>
    </div>
</body>

</html>