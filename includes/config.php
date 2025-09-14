<?php 
// session_start();

// $_SESSION['debug'] = false;

// if($_SESSION['debug']){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);
// }

define("site_url", "https://my10020.ir/");
define("blog_url", "blog/");
define("product_url", "product/");
define("blog_cat", "category");
define("blog_tag", "post_tag");
define("product_url", "product");
define("product_cat", "product_cat");
define("product_tag", "product_tag");
define("product_brand", "product_brand");
define("base_dir", "/home/h288319/public_html/");
define("upload_folder", "content/uploads/");
define("seo_title_name", "_aioseop_title");
define("seo_desc_name", "_aioseop_description");
define("seo_keywords_name", "_aioseop_keywords");


include_once(base_dir . '/includes/classes/base.php');
$functions = new base();

include_once(base_dir . '/includes/jalali.php');
$date = jdate('Y/m/j');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = new mysqli("localhost", "h288319_10020_user", 'Jt=$S$eW.K9@', "h288319_10020");
$con->set_charset("utf8mb4");
