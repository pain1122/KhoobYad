<?php
session_start();
$_SESSION['lang'] = 'fa';
include_once("../includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/tag.php");
include_once(base_dir . "/includes/classes/user.php");
include_once(base_dir . "/includes/classes/plan.php");
include_once(base_dir . "/includes/classes/shop-order.php");
$uid = $_SESSION['uid'];
if ($uid == 0 || $uid == null) {
    session_destroy();
    ob_flush();
    $_SESSION['turn-back-url'] = $_SERVER['REQUEST_URI'];
    base::redirect("login.php");
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 180000)) {
    // last request was more than 5 hours ago
    session_destroy();
    ob_flush();
    base::redirect("login.php");
} else if ($_SESSION['remember']) {
    $_SESSION['LAST_ACTIVITY'] = intval(time() + (86400 * 14));
} else {
    $_SESSION['LAST_ACTIVITY'] = intval(time());
}
if (!empty($uid)) {
    $user = new User($uid);
    $nickname = $user->get_nick_name();
    $user_granted_access = $user->get_user_meta('access');
    $role = $user->get_user_meta('role');
    $avatar = base::displayphoto($user->get_user_meta('avatar'));
    $number = $user->get_login();
    $user_classes = json_decode($user->get_user_meta('classes'), true);
    $subscription = $user->get_user_meta('subscription');
    $is_in_plan_league = $user->get_user_meta('plans_league');
    $is_in_defind_plan_league = $user->get_user_meta('defind_plans_league');
    $user_order_items = base::get_user_orders_items($uid);
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'];
}
$menu_array = [
    "داشبورد" => [
        "url" => "?page=dashboard.php",
        "access" => "dashboard",
        "icon" => "menu-icon tf-icons bx bx-home-circle",
        "Sub" => []
    ],
    "نوشته ها" => [
        "url" => "javascript:void(0);",
        "access" => "articles",
        "icon" => "menu-icon tf-icons bx bx-spreadsheet",
        "sub" => [
            "همه نوشته ها" => [
                "url" => "?page=post/all-posts.php",
                "access" => "all_articles",
            ],
            "افزودن نوشته" => [
                "url" => "?page=post/add-post.php",
                "access" => "add_article",
            ],
            " دسته بندی نوشته ها" => [
                "url" => "?page=tag/edit-tag.php&type=category",
                "access" => "article_category",
            ],
            "برچسب ها نوشته ها" => [
                "url" => "?page=tag/edit-tag.php&type=post_tag",
                "access" => "article_tag",
            ]
        ]
    ],
    "کلاس های آنلاین" => [
        "url" => "javascript:void(0);",
        "access" => "classes",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه کلاس ها" => [
                "url" => "?page=product/all-products.php&type=online-class",
                "access" => "all_classes",
            ],
            "کلاس های آنلاین من" => [
                "url" => "?page=product/my-products.php&type=online-class",
                "access" => "my_classes",
            ],
            "افزودن کلاس " => [
                "url" => "?page=product/add-online-class.php",
                "access" => "add_class",
            ],
            " دسته بندی کلاس ها" => [
                "url" => "?page=tag/edit-tag.php&type=online_class_category",
                "access" => "class_category",
            ],
            "برچسب ها کلاس ها" => [
                "url" => "?page=tag/edit-tag.php&type=online_class_tag",
                "access" => "class_tag",
            ]
        ]
    ],
    "آف باکس ها" => [
        "url" => "javascript:void(0);",
        "access" => "offboxes",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه آف باکس ها" => [
                "url" => "?page=product/all-products.php&type=off-boxes",
                "access" => "all_offboxes",
            ],
            "آف باکس های من" => [
                "url" => "?page=product/my-products.php&type=off-boxes",
                "access" => "my_offboxes",
            ],
            "افزودن آف باکس " => [
                "url" => "?page=product/add-off-boxes.php",
                "access" => "add_offbox",
            ],
            " دسته بندی آف باکس ها" => [
                "url" => "?page=tag/edit-tag.php&type=off_boxes_category",
                "access" => "offbox_category",
            ],
            "برچسب ها آف باکس ها" => [
                "url" => "?page=tag/edit-tag.php&type=off_boxes_tag",
                "access" => "offbox_tag",
            ]
        ]
    ],
    "کلاس بوک ها" => [
        "url" => "javascript:void(0);",
        "access" => "class_books",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه کلاس بوک ها" => [
                "url" => "?page=product/all-products.php&type=class-book",
                "access" => "all_class_books",
            ],
            "کلاس بوک های من" => [
                "url" => "?page=product/my-products.php&type=class-book",
                "access" => "my_class_books",
            ],
            "افزودن کلاس بوک" => [
                "url" => "?page=product/add-class-book.php",
                "access" => "add_class_book",
            ],
            " دسته بندی کلاس بوک ها" => [
                "url" => "?page=tag/edit-tag.php&type=class_book_category",
                "access" => "class_book_category",
            ],
            "برچسب ها کلاس بوک ها" => [
                "url" => "?page=tag/edit-tag.php&type=class_book_tag",
                "access" => "class_book_category",
            ]
        ]
    ],
    "حل المسائل ها" => [
        "url" => "javascript:void(0);",
        "access" => "solution_books",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه حل المسائل ها" => [
                "url" => "?page=product/all-products.php&type=solution-book",
                "access" => "all_solution_books",
            ],
            "حل المسائل های من" => [
                "url" => "?page=product/my-products.php&type=solution-book",
                "access" => "my_solution_books",
            ],
            "افزودن حل المسائل" => [
                "url" => "?page=product/add-solution-book.php",
                "access" => "add_solution_book",
            ],
            " دسته بندی حل المسائل ها" => [
                "url" => "?page=tag/edit-tag.php&type=solution_book_category",
                "access" => "solution_book_category",
            ],
            "برچسب ها حل المسائل ها" => [
                "url" => "?page=tag/edit-tag.php&type=solution_book_tag",
                "access" => "solution_book_category",
            ]
        ]
    ],
    "تست بوک ها" => [
        "url" => "javascript:void(0);",
        "access" => "test_books",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه تست بوک ها" => [
                "url" => "?page=test-books/all-test-books.php",
                "access" => "all_test_books",
            ],
            "افزودن تست بوک" => [
                "url" => "?page=test-books/add-test-book.php",
                "access" => "add_test_book",
            ]
        ]
    ],
    "آزمون های تستی" => [
        "url" => "javascript:void(0);",
        "access" => "exams",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه آزمون ها" => [
                "url" => "?page=exams/all-exams.php",
                "access" => "all_exams",
            ],
            "افزودن آزمون " => [
                "url" => "?page=exams/add-exam.php",
                "access" => "add_exam",
            ]
        ]
    ],
    "آزمون ها تشریحی" => [
        "url" => "javascript:void(0);",
        "access" => "exams",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه آزمون ها" => [
                "url" => "?page=descriptive-exams/all-descriptive-exams.php",
                "access" => "all_exams",
            ],
            "افزودن آزمون " => [
                "url" => "?page=descriptive-exams/add-descriptive-exam.php",
                "access" => "add_exam",
            ]
        ]
    ],
    // "اسلایدر ها" => [
    //     "url" => "javascript:void(0);",
    //     "access" => "sliders",
    //     "icon" => "menu-icon tf-icons bx bx-carousel",
    //     "sub" => [
    //         "همه اسلایدر ها" => [
    //             "url" => "?page=sliders/all-sliders.php",
    //             "access" => "all_sliders",
    //         ],
    //         "افزودن اسلایدر " => [
    //             "url" => "?page=sliders/add-slider.php",
    //             "access" => "add_slider",
    //         ]
    //     ]
    // ],
    "کاربران" => [
        "url" => "javascript:void(0);",
        "access" => "users",
        "icon" => "menu-icon tf-icons bx bx-user",
        "sub" => [
            "همه دانش آموزان" => [
                "url" => "?page=user/all-students.php",
                "access" => "all_users",
            ],
            "همه همکاران" => [
                "url" => "?page=user/all-employees.php",
                "access" => "all_employees",
            ],
            "پایه تحصیلی" => [
                "url" => "?page=tag/edit-tag.php&type=study_grade",
                "access" => "study_grade",
            ],
            "رشته تحصیلی" => [
                "url" => "?page=tag/edit-tag.php&type=study_course",
                "access" => "study_course",
            ],
            "کلاس ها" => [
                "url" => "?page=user/all-classes.php",
                "access" => "all_groups",
            ]
        ]
    ],
    "سفارشات" => [
        "url" => "javascript:void(0);",
        "access" => "orders",
        "icon" => "menu-icon tf-icons bx bx-food-menu",
        "sub" => [
            "همه سفارشات" => [
                "url" => "?page=order/all-order.php",
                "access" => "all_orders",
            ],
            "گزارشات" => [
                "url" => "?page=order/reports.php",
                "access" => "reports",
            ],
            "کوپن ها" => [
                "url" => "?page=order/coupon.php",
                "access" => "coupons",
            ]
        ]
    ],
    "مشاوره" => [
        "url" => "javascript:void(0);",
        "access" => "tickets",
        "icon" => "menu-icon tf-icons bx bx-support",
        "sub" => [
            "ثبت کانال" => [
                "url" => "?page=counsel/submit-ticket.php",
                "access" => "add_ticket",
            ],
            "همه کانال ها" => [
                "url" => "?page=counsel/tickets.php",
                "access" => "all_tickets",
            ],
            "رفع اشکال ها" => [
                "url" => "?page=counsel/tickets.php&status=debug",
                "access" => "all_debugs",
            ]
        ]
    ],
    "برنامه تحصیلی" => [
        "url" => "javascript:void(0);",
        "access" => "plans",
        "icon" => "menu-icon tf-icons bx bx-calendar",
        "sub" => [
            "همه برنامه ها" => [
                "url" => "?page=plans/all-plans.php",
                "access" => "all_plans",
            ],
            "برنامه های من" => [
                "url" => "?page=plans/my-plans.php&uid=$uid",
                "access" => "my_plans",
            ]
        ]
    ],
    "خوب یار" => [
        "url" => "javascript:void(0);",
        "access" => "plans",
        "icon" => "menu-icon tf-icons bx bx-calendar-event",
        "sub" => [
            "تعریف برنامه" => [
                "url" => "?page=defined-plans/add-plan.php",
                "access" => "add_defined_plan",
            ],
            "همه برنامه های تعریف شده" => [
                "url" => "?page=defined-plans/all-defined-plans.php",
                "access" => "all_defined_plans",
            ],
            "گزارشات" => [
                "url" => "?page=defined-plans/defiend-plans-report.php",
                "access" => "all_defined_plans",
            ],
            "اضافه کردن برنامه به دانش آموز" => [
                "url" => "?page=defined-plans/arrange-plan.php&uid=$uid",
                "access" => "add_defined_plans",
            ],
            "برنامه های من" => [
                "url" => "?page=defined-plans/my-defined-plans.php&uid=$uid",
                "access" => "my_defined_plans",
            ]
        ]
    ],
    "تنظیمات" => [
        "url" => "javascript:void(0);",
        "access" => "settings",
        "icon" => "menu-icon tf-icons bx bx-cog",
        "sub" => [
            "تنظیمات سایت" => [
                "url" => "?page=general/site-settings.php",
                "access" => "site_settings",
            ],
            "صفحه اصلی" => [
                "url" => "?page=general/home-page.php",
                "access" => "main_page",
            ],
            "مگا منو" => [
                "url" => "?page=general/mega-menu.php",
                "access" => "mega_menu",
            ],
            "زبان" => [
                "url" => "?page=general/add_title.php&lang=fa",
                "access" => "languages",
            ],
            "آپلود سنتر" => [
                "url" => "?page=general/upload-center.php",
                "access" => "upload",
            ]
        ]
    ]
];
$students_menu_array = [
    "داشبورد" => [
        "url" => "?page=dashboard.php",
        "access" => "dashboard",
        "icon" => "menu-icon tf-icons bx bx-home-circle",
        "Sub" => []
    ],
    "کلاس های آنلاین" => [
        "url" => "javascript:void(0);",
        "access" => "classes",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه کلاس ها" => [
                "url" => "?page=product/all-products.php&type=online-class",
                "access" => "all_classes",
            ],
            "کلاس های آنلاین من" => [
                "url" => "?page=product/my-products.php&type=online-class",
                "access" => "my_classes",
            ]
        ]
    ],
    "دوره ها" => [
        "url" => "javascript:void(0);",
        "access" => "courses",
        "icon" => "menu-icon tf-icons bx bxs-book",
        "sub" => [
            "آف باکس ها" => [
                "url" => "javascript:void(0);",
                "access" => "offboxes",
                "icon" => "menu-icon tf-icons bx bx-copy",
                "sub" => [
                    "همه آف باکس ها" => [
                        "url" => "?page=product/all-products.php&type=off-boxes",
                        "access" => "all_offboxes",
                    ],
                    "آف باکس های من" => [
                        "url" => "?page=product/my-products.php&type=off-boxes",
                        "access" => "my_offboxes",
                    ]
                ]
            ],
            "کلاس بوک ها" => [
                "url" => "javascript:void(0);",
                "access" => "class_books",
                "icon" => "menu-icon tf-icons bx bx-copy",
                "sub" => [
                    "همه کلاس بوک ها" => [
                        "url" => "?page=product/all-products.php&type=class-book",
                        "access" => "all_class_books",
                    ],
                    "کلاس بوک های من" => [
                        "url" => "?page=product/my-products.php&type=class-book",
                        "access" => "my_class_books",
                    ]
                ]
            ],
        ]
    ],
    "مکمل ها" => [
        "url" => "javascript:void(0);",
        "access" => "complimentries",
        "icon" => "menu-icon tf-icons bx bxs-book",
        "sub" => [
            "حل المسائل ها" => [
                "url" => "javascript:void(0);",
                "access" => "solution_books",
                "icon" => "menu-icon tf-icons bx bx-copy",
                "sub" => [
                    "همه حل المسائل ها" => [
                        "url" => "?page=product/all-products.php&type=solution-book",
                        "access" => "all_solution_books",
                    ],
                    "حل المسائل های من" => [
                        "url" => "?page=product/my-products.php&type=solution-book",
                        "access" => "my_solution_books",
                    ]
                ]
            ],
            "تست بوک ها" => [
                "url" => "javascript:void(0);",
                "access" => "test_books",
                "icon" => "menu-icon tf-icons bx bx-copy",
                "sub" => [
                    "همه تست بوک ها" => [
                        "url" => "?page=test-books/all-test-books.php",
                        "access" => "all_test_books",
                    ],
                ]
            ],
        ]
    ],
    "آزمون های تستی" => [
        "url" => "javascript:void(0);",
        "access" => "exams",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه آزمون ها" => [
                "url" => "?page=exams/all-exams.php",
                "access" => "all_exams",
            ]
        ]
    ],
    "آزمون ها تشریحی" => [
        "url" => "javascript:void(0);",
        "access" => "descriptive-exams",
        "icon" => "menu-icon tf-icons bx bx-copy",
        "sub" => [
            "همه آزمون ها" => [
                "url" => "?page=descriptive-exams/all-descriptive-exams.php",
                "access" => "all_descriptive_exams",
            ]
        ]
    ],
    "برنامه تحصیلی" => [
        "url" => "javascript:void(0);",
        "access" => "plans",
        "icon" => "menu-icon tf-icons bx bx-calendar",
        "sub" => [
            "برنامه های من" => [
                "url" => "?page=plans/my-plans.php&uid=$uid",
                "access" => "my_plans",
            ]
        ]
    ],
    "خوب یار" => [
        "url" => "javascript:void(0);",
        "access" => "plans",
        "icon" => "menu-icon tf-icons bx bx-calendar-event",
        "sub" => [
            "اضافه کردن برنامه به هفته" => [
                "url" => "?page=defined-plans/arrange-plan.php&uid=$uid",
                "access" => "all_defined_plans",
            ],
            "برنامه های من" => [
                "url" => "?page=defined-plans/my-defined-plans.php&uid=$uid",
                "access" => "my_defined_plans",
            ]
        ]
    ],
    "مشاوره" => [
        "url" => "javascript:void(0);",
        "access" => "tickets",
        "icon" => "menu-icon tf-icons bx bx-support",
        "sub" => [
            "همه کانال ها" => [
                "url" => "?page=counsel/tickets.php",
                "access" => "all_tickets",
            ]
        ]
    ],
];


if (isset($_GET['type']))
    $type = "&type=" . $_GET['type'];
$page = $_GET['page'] . $type;
if ($role == 'admin') {
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` WHERE `post_type` = 'chat' AND `post_status` = 'ongoing' ORDER BY `post_id` DESC;";
} elseif ($role == 'school') {
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` WHERE `post_type` = 'chat' AND `post_status` = 'ongoing' AND `school` = $uid ORDER BY `post_id` DESC;";
} else {
    if (is_countable($user_classes) && count($user_classes) > 0) {
        $classes = $user_classes;
        $classes_q = "OR (`key` = 'clases' AND (";
        for ($i = 0; $i < count($classes); $i++) {
            if ($i === 0)
                $classes_q .= "`value` LIKE '%$classes[$i]%'";
            else
                $classes_q .= " OR `value` LIKE '%$class[$i]%'";
        }
        $classes_q .= '))';
    } else {
        $classes_q = '';
    }
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id` WHERE `post_type` = 'chat' AND `post_status` = 'ongoing' AND ((`key` = 'members' AND `value` LIKE '%$uid%') $classes_q) GROUP BY `post`.`post_id` ORDER BY `post`.`post_id` DESC;";
}
$subjects = $functions->FetchArray($subject_query, $con);
if (is_countable($subjects) && count($subjects) > 0) {
    foreach ($subjects as $subject)
        $subjec_json[] = $subject['post_id'];
    $subjec_json = implode(',', $subjec_json);
    $new_message =  $functions->FetchAssoc("SELECT `post`.`post_id` FROM `post_meta` INNER JOIN `post` ON `post`.`post_id` = `post_meta`.`post_id` WHERE `post_parent` IN ($subjec_json) AND `key` = 'users' AND `value` NOT LIKE '%\"$uid\"%'");
}
if ($new_message)
    $new_message = '<i class="bx bxs-bell-ring mr-2"></i>';
else
    $new_message = '';

$uriSegment = '?'.explode("?", $_SERVER['REQUEST_URI'])[1];
function hasAccess($menuArray, $uriSegment) {
    foreach ($menuArray as $menuItem) {
        // Check if the current menu item has a URL and matches the target URI
        if (isset($menuItem['url']) && $menuItem['url'] === $uriSegment) {
            return true;
        }
        
        // If the current menu item has submenus, search recursively
        if (isset($menuItem['sub']) && is_array($menuItem['sub'])) {
            if (hasAccess($menuItem['sub'], $uriSegment)) {
                return true;
            }
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="fa" class="light-style layout-navbar-fixed layout-menu-fixed" dir="rtl" data-theme="theme-semi-dark" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <?php if (strpos($page, 'pdf') === false) { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <?php } ?>




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
    <link rel="stylesheet" href="assets/vendor/libs/fancybox/fancybox.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" /> -->

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="assets/vendor/js/template-customizer.js"></script>
    <link rel="stylesheet" href="assets/vendor/libs/sweetalert2/sweetalert2.css">
    <script src="assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
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
    </script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <?php
            include_once('side-bar.php');
            ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php
                include_once('navbar.php');
                ?>
                <!-- Navbar -->

                <!-- Content -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <?php
                        if (isset($_GET['page']) && strpos($_GET['page'], "../") === false) {
                            // if($role == 'student'){
                            //     if (hasAccess($students_menu_array, $uriSegment)) {
                                    include_once($_GET['page']);
                            //     } else {
                            //         base::redirect("?page=dashboard.php");
                            //     }
                            // }else{
                            //     if (hasAccess($menu_array, $uriSegment)) {
                            //         include_once($_GET['page']);
                            //     } else {
                            //         base::redirect("?page=dashboard.php");
                            //     }
                            // }
                            
                        } else {
                            base::redirect("?page=dashboard.php");
                        }
                        ?>
                    </div>
                </div>
                <div class="content-backdrop fade"></div>
                <!-- Content -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <script src="assets/vendor/js/bootstrap.js"></script>

    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/libs/toastr/toastr.js"></script>
    <script src="assets/vendor/libs/fancybox/fancybox.umd.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        $(document).ready(function() {
            let item = $('.menu-item .active');
            var item_parent = item.parent();
            item_parent = item_parent.parent().addClass("open");
            var formChanged = false;    
            $('.theme-form input').on('input change', function() {
                formChanged = true;
            });
            $('.theme-form textarea').on('input change', function() {
                formChanged = true;
            });
            $('.theme-form select').on('input change', function() {
                formChanged = true;
            });
            
         
            $('.theme-form').submit(function() {
                formChanged = false;
            });
           
            $(window).on('beforeunload', function() {        
                if (formChanged) {
                    return 'تغییرات خود را ذخیره نمیکنی؟';
                }
            });  
        });

        function genString(length) {
            var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var result = '';
            for (var i = 0; i < length; i++) {
                result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
            }
            return result;
        }

        function imageReplace(name, event) {
            var output = document.getElementById(name);
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        };
        var loadFile = function(elem, event) {
            var index = elem.name;
            imageReplace(index, event);
        };

        function getFileExt(event, output) {

            if (!event || !event.target || !event.target.files || event.target.files.length === 0) {
                return;
            }

            const name = event.target.files[0].name;
            const lastDot = name.lastIndexOf('.');
            const ext = name.substring(lastDot + 1);
            $('#' + output + '').html(ext + '.');
        }
        var typingTimer;
        var doneTypingInterval = 1000;
        // $('#search-bar-content').keyup(function() {
        //     clearTimeout(typingTimer);
        //     if ($('#search-bar-content').val()) {
        //         typingTimer = setTimeout(Search, doneTypingInterval);
        //         $('.search-results').slideDown();
        //     } else {
        //         $('.search-results').slideUp();
        //     }
        // });

        function separate(Number) {
            Number += '';
            Number = Number.replace(',', '');
            x = Number.split('.');
            y = x[0];
            z = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(y))
                y = y.replace(rgx, '$1' + ',' + '$2');
            return y + z;
        }

        const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))

        function replace_digits(input) {
            var temp_inp = input.value;
            var replaced = p2e(temp_inp);
            input.value = replaced;
        }

        function Search(element, element_id) {
            var content = $(element).val();
            var result_div = $(element).parent().find('.search-results');
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                if (content && content.length > 0) {
                    getJSON('API/v1/Search.php?content=' + content + "&filter=''", function(err, data) {
                        $(result_div).html('');
                        if (data !== null && data.length > 0) {
                            for (var i = 0; i < data.length; i++) {
                                if (data[i]["_sale_price"] == "") {
                                    $(result_div).append('<div onclick="addProduct(' + data[i]['post_id'] + ', \'' + data[i]['post_title'] + '\',\'' + element_id + '\')" class="search-result"><img src="' + data[i]['img'] + '" width="100" height="100"><div class="thumb-det"><p>' + data[i]["post_title"] + '</p><div class="thumb-price"><ins>' + separate(parseInt(data[i]["_regular_price"])) + ' <span>تومان</span></ins></div></div></div>');
                                } else {
                                    $(result_div).append('<div onclick="addProduct(' + data[i]['post_id'] + ', \'' + data[i]['post_title'] + '\',\'' + element_id + '\')" class="search-result"><img src="' + data[i]['img'] + '" width="100" height="100"><div class="thumb-det"><p>' + data[i]["post_title"] + '</p><div class="thumb-price"><ins>' + separate(parseInt(data[i]["_sale_price"])) + ' <span>تومان</span></ins><del>' + separate(parseInt(data[i]["_regular_price"])) + ' <span>تومان</span></del></div></div></div>');
                                }
                            }
                        } else {
                            $(result_div).append('<div class="search-result"><p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">محصولی پیدا نشد</p></div>');
                        }
                        $(result_div).slideDown();
                    });
                } else {
                    $(result_div).slideUp();
                }

            }, doneTypingInterval);
        }

        function remcomp(id, element_id) {
            $(this).closest(".complementary").remove();
            var val = $('#' + element_id).val();
            if (val.indexOf(id) == 0) {
                val = val.replace(id + ',', '');
            } else {
                val = val.replace(',' + id, '');
            }
            $('#' + element_id).val(val);
        }

        function addProduct(id, title, element_id) {
            var cval = $('#' + element_id).val();
            if (cval === '')
                cval = id;
            else
                cval = cval + ',' + id;
            $('#' + element_id).val(cval);
            $('#' + element_id).parent().find('.complementaries').append('<div class="complementary"><p>' + title + '</p><span class="close" onclick="remcomp(' + id + ',\'' + element_id + '\');$(this).parent().remove();">✖</span></div>');
        }

        $('body').click(function(e) {
            if (!e.target.className == "search-results" || !$(e.target).parents(".search-bar").length) {
                $('.search-results').slideUp();
            }
        });
        $('.product-search').click(function() {
            if ($(this).val()) {
                $('.search-results').slideDown();
            }
        });

        function insert_sku(element, id) {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                var input = $('#' + id);
                var text = $(element).val().trim();
                text = text.replace(/\ /g, "-");
                input.val(text).trigger('change');
            }, doneTypingInterval);
        }

        function validate_name(element, type) {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                var name = $(element).val().trim();
                $(element).parent().addClass('position-relative');
                $(element).parent().append('<div class="spinner-border spinner-border-sm" id="validate-loading" style="position: absolute;left: 5px;bottom: 10px;z-index : 10;"></div>');
                getJSON('API/v1/Validate.php?name=' + name + '&type=' + type, function(err, data) {
                    if (data != null && data.length > 0) {
                        $(element).parent().find(".validate-failed").remove();
                        if (data != 'ok') {
                            $(element).parent().append('<p class="alert alert-danger validate-failed">این نام قبلا استفاده شده است</p>');
                        }
                    }
                });
                $(element).parent().find("#validate-loading").remove();
                $(element).parent().removeClass('position-relative');
            }, doneTypingInterval);
        }

        function countChar(element, max, destinaton) {
            var num = 0;
            var cur_char = $('#' + destinaton + ' span:first-child');
            var max_char = $('#' + destinaton + ' span:last-child');
            cur_char.css("color", "inherit");
            if (element.nodeName.toLowerCase() === 'input') {
                num = $(element).val().length;
            } else {
                num = $(element).text().length;
            }
            cur_char.text(num);

            if (num >= max - 10 && num <= max) {
                cur_char.css("color", "green");
            } else if (num > max) {
                cur_char.css("color", "red");
            }
        };
        $('.menu-inner > .menu-item').each(function(i, obj) {
            const child = $(obj).children(".menu-sub").children();
            if (child.length == 0) {
                obj.style.display = 'none';
            }
        });
    </script>
</body>

</html>