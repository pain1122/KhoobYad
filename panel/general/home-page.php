<?php

if (isset($_POST['slider-banners'])) {
    $slider_top_banner_link = $_POST['slider_top_banner_link'];
    $slider_top_banner = $_POST['slider_top_banner'];
    $slider_bottom_banner_link = $_POST['slider_bottom_banner_link'];
    $slider_bottom_banner = $_POST['slider_bottom_banner'];
    base::set_option('slider_top_banner_link', $slider_top_banner_link);
    base::set_option('slider_top_banner', $slider_top_banner);
    base::set_option('slider_bottom_banner_link', $slider_bottom_banner_link);
    base::set_option('slider_bottom_banner', $slider_bottom_banner);
}
if (isset($_POST['special-offers'])) {
    $offers_right_banner_link = $_POST['offers_right_banner_link'];
    $offers_right_banner = $_POST['offers_right_banner'];
    $offers_left_banner_link = $_POST['offers_left_banner_link'];
    $offers_left_banner = $_POST['offers_left_banner'];
    $sale_date = explode('-', $_POST['sale_date']);
    $sale_start = $sale_end = 0;
    if (intval($sale_date[1]) > 0) {
        $sale_start = $sale_date[0];
        $sale_end = $sale_date[1];
    }
    $offers = $_POST['offers'];
    $offers = str_replace(',,', '', $offers);
    base::set_option('offers_right_banner_link', $offers_right_banner_link);
    base::set_option('offers_right_banner', $offers_right_banner);
    base::set_option('offers_left_banner_link', $offers_left_banner_link);
    base::set_option('offers_left_banner', $offers_left_banner);
    base::set_option('sale_start', $sale_start);
    base::set_option('sale_end', $sale_end);
    base::set_option('offers', $offers);
}
if (isset($_POST['accessibility'])) {
    base::RunQuery("DELETE FROM `options` WHERE `name` LIKE '%main_page_access%'");
    $main_page_access = $_POST['main_page_access'];
    $main_page_access_link = $_POST['main_page_access_link'];
    $main_page_access_image = $_POST['main_page_access_image'];
    for ($j = 0; $j < count($main_page_access); $j++) {
        $text = $main_page_access[$j];
        $link = $main_page_access_link[$j];
        $image = $main_page_access_image[$j];
        if ($image || $text || $link)
            base::RunQuery("INSERT INTO `options` (`name`,`value`) VALUES ('main_page_access','$text'),('main_page_access_link','$link'),('main_page_access_image','$image')");
    }
}
if (isset($_POST['categories'])) {
    $cats1 = $_POST['category_section1'];
    $show_cat1 = $_POST['show_cat1'];
    $cats2 = $_POST['category_section2'];
    $show_cat2 = $_POST['show_cat2'];
    $cats1 = str_replace('"', "'", $cats1);
    $cats2 = str_replace('"', "'", $cats2);
    $categories_banner1_link = $_POST['categories_banner1_link'];
    $categories_banner1 = $_POST['categories_banner1'];
    $categories_banner2_link = $_POST['categories_banner2_link'];
    $categories_banner2 = $_POST['categories_banner2'];
    $categories_banner3_link = $_POST['categories_banner3_link'];
    $categories_banner3 = $_POST['categories_banner3'];
    $categories_banner4_link = $_POST['categories_banner4_link'];
    $categories_banner4 = $_POST['categories_banner4'];
    base::set_option('category_section1', $cats1);
    base::set_option('show_cat1', $show_cat1);
    base::set_option('category_section2', $cats2);
    base::set_option('show_cat2', $show_cat2);
    base::set_option('categories_banner1_link', $categories_banner1_link);
    base::set_option('categories_banner1', $categories_banner1);
    base::set_option('categories_banner2_link', $categories_banner2_link);
    base::set_option('categories_banner2', $categories_banner2);
    base::set_option('categories_banner3_link', $categories_banner3_link);
    base::set_option('categories_banner3', $categories_banner3);
    base::set_option('categories_banner4_link', $categories_banner4_link);
    base::set_option('categories_banner4', $categories_banner4);
}
if (isset($_POST['new-section'])) {
    $new_banner1_link = $_POST['new_banner1_link'];
    $new_banner1 = $_POST['new_banner1'];
    $new_banner2_link = $_POST['new_banner2_link'];
    $new_banner2 = $_POST['new_banner2'];
    $show_new = $_POST['show_new'];
    base::set_option('new_banner1_link', $new_banner1_link);
    base::set_option('new_banner1', $new_banner1);
    base::set_option('new_banner2_link', $new_banner2_link);
    base::set_option('new_banner2', $new_banner2);
    base::set_option('show_new', $show_new);
}

$slider_top_banner_link = base::get_option('slider_top_banner_link');
$slider_top_banner = base::get_option('slider_top_banner');
$slider_bottom_banner_link = base::get_option('slider_bottom_banner_link');
$slider_bottom_banner = base::get_option('slider_bottom_banner');


$offers_right_banner_link = base::get_option('offers_right_banner_link');
$offers_right_banner = base::get_option('offers_right_banner');
$offers_left_banner_link = base::get_option('offers_left_banner_link');
$offers_left_banner = base::get_option('offers_left_banner');
$sale_start = base::get_option('main_sale_start');
$sale_end = base::get_option('main_sale_end');
$offers = base::get_option('offers');


$cats1 = base::get_option('category_section1');
$show_cat1 = base::get_option('show_cat1');
$cats2 = base::get_option('category_section2');
$show_cat2 = base::get_option('show_cat2');
$cats1 = str_replace(['[', ']'], '', $cats1);
$cats1 = str_replace("'", '"', $cats1);
$cats1 = json_decode($cats1, true);
$cats2 = str_replace(['[', ']'], '', $cats2);
$cats2 = str_replace("'", '"', $cats2);
$cats2 = json_decode($cats2, true);
$categories_banner1_link = base::get_option('categories_banner1_link');
$categories_banner1 = base::get_option('categories_banner1');
$categories_banner2_link = base::get_option('categories_banner2_link');
$categories_banner2 = base::get_option('categories_banner2');
$categories_banner3_link = base::get_option('categories_banner3_link');
$categories_banner3 = base::get_option('categories_banner3');
$categories_banner4_link = base::get_option('categories_banner4_link');
$categories_banner4 = base::get_option('categories_banner4');

$new_banner1_link = base::get_option('new_banner1_link');
$new_banner1 = base::get_option('new_banner1');
$new_banner2_link = base::get_option('new_banner2_link');
$new_banner2 = base::get_option('new_banner2');
$show_new = base::get_option('show_new');

$main_page_access = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access'");
$main_page_access_link = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access_link'");
$main_page_access_image = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'main_page_access_image'");
if (empty($sale_end) || empty($sale_start))
    $sale_start = $sale_end = intval(time());
$all_cats = tag::get_taxonomies(['type' => 'product_cat']);


function show_taxonomy(array $tags, $parentId, &$categories)
{
    $branch = "";
    foreach ($tags as $tag) {
        $tagid = intval($tag['tag_id']);
        if ($tagid) :
            $cat = new tag($tagid);
            $tag_parent = $cat->get_parent();
            if ($tag_parent == $parentId) {
                $tagname = '';
                $tagname .= $tag['name']; ?>
                {name: '<?php echo $tagname; ?>', value:<?php echo $tagid; ?>},
<?php
                show_taxonomy($tags, $tagid, $categories);
            }
        endif;
    }
    return $branch;
}

?>
<link rel="stylesheet" href="assets/vendor/libs/tagify/tagify.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
<div class="card">
    <h5 class="card-header">سکشن های صفحه اصلی</h5>
    <div class="nav-align-top">
        <ul class="nav nav-pills mb-3 px-4 justify-content-between justify-content-sm-start" role="tablist">
            <li>
                <button type="button" class="nav-link px-2 px-md-4 active" role="tab" data-bs-toggle="tab" data-bs-target="#slider-banners" aria-controls="slider-banners" aria-selected="true">بنر های اسلایدر</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#special-offers" aria-controls="special-offers" aria-selected="false">پیشنهادات ویژه</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#accessibility" aria-controls="accessibility" aria-selected="false">دسترسی ویژه</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#categories" aria-controls="categories" aria-selected="false">انتخاب دسته ها</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#new-section" aria-controls="new-section" aria-selected="false">بنر جدید ترین ها</button>
            </li>
        </ul>
        <div class="tab-content" style="box-shadow: none;">
            <div class="tab-pane fade active show" id="slider-banners" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="slider_top_banner_link" value="<?php echo $slider_top_banner_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر بالا<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=top-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $slider_top_banner; ?>" type="text" name="slider_top_banner" id="top-banner">
                        <img src="<?php echo $slider_top_banner; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="slider_bottom_banner_link" value="<?php echo $slider_bottom_banner_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر پایین<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=bottom-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $slider_bottom_banner; ?>" type="text" name="slider_bottom_banner" id="bottom-banner">
                        <img src="<?php echo $slider_bottom_banner; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="slider-banners" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="special-offers" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="offers_right_banner_link" value="<?php echo $offers_right_banner_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر راست<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=right-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $offers_right_banner; ?>" type="text" name="offers_right_banner" id="right-banner">
                        <img src="<?php echo $offers_right_banner; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="offers_left_banner_link" value="<?php echo $offers_left_banner_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر چپ<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=left-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $offers_left_banner; ?>" type="text" name="offers_left_banner" id="left-banner">
                        <img src="<?php echo $offers_left_banner; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">انتخاب بازه</label>
                        <input type="text" name="sale_date" class="form-control" placeholder="YYYY/MM/DD تا YYYY/MM/DD" id="flatpickr-range">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <div class="mb-3 search-bar">
                            <label class="form-label">جستوجو محصول</label>
                            <input class="form-control" name="name" onkeyup="replace_digits(this);Search(this,'offers')" type="text" placeholder="نام محصول موردنظر خود را وارد کنید">
                            <div class="position-relative mt-5">
                                <div class="search-results card-body">
                                    <div class="search-result">
                                        <p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">در حال جست و جو</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <input type="hidden" value="<?php echo $offers; ?>" name="offers" id="offers">
                        <div class="complementaries">
                            <?php if (strlen($offers) > 0) :
                                $offers = str_replace(',,', '', $offers);
                                $offers = explode(',', $offers);
                                foreach ($offers as $complimentry) :
                                    $cproduct = base::Fetchassoc("SELECT `post_title`,`post_id` from `post` WHERE `post_id` = $complimentry"); ?>
                                    <div class="complementary">
                                        <p><?php echo $cproduct['post_title'] ?></p>
                                        <span class="close" onclick="remcomp(<?php echo $cproduct['post_id'] ?>,'offers');$(this).parent().remove();">✖</span>
                                    </div>
                            <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="special-offers" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="accessibility" role="tabpanel">
                <form method="POST" action="" class="row" id="section-access">
                    <?php if (is_countable($main_page_access) && count($main_page_access) > 0) :
                        for ($i = 0; $i < count($main_page_access); $i++) : ?>
                            <div class="col-12 col-md-6 mb-4">
                                <label class="form-label">متن لینک</label>
                                <input type="text" name="main_page_access[]" value="<?php echo $main_page_access[$i]['value'] ?>" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                <label class="form-label">مقصد لینک</label>
                                <input type="text" name="main_page_access_link[]" value="<?php echo $main_page_access_link[$i]['value']; ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                                <label class="form-label d-flex align-items-center mt-3">تصویر<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=main_page_access_image<?php echo $i; ?>" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                                <input hidden value="<?php echo $main_page_access_image[$i]['value']; ?>" type="text" name="main_page_access_image[]" id="main_page_access_image<?php echo $i; ?>">
                                <img src="<?php echo $main_page_access_image[$i]['value']; ?>" width="200px" class="mt-3 d-block mx-auto">
                            </div>
                    <?php endfor;
                    endif; ?>
                    <div class="col-12">
                        <button id="add-link" type="button" class="btn btn-primary ml-3">اضافه کردن</button>
                        <button type="submit" name="accessibility" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="categories" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">انتخاب دسته بندی اول</label>
                        <input name="category_section1" class="form-control" id="cat1" placeholder="انتخاب دسته بندی" value="<?php if (is_countable($cats1) && count($cats1) > 0) {
                                                                                                                                    echo $cats1['name'] . ",";
                                                                                                                                } ?>">
                    </div>
                    <div class="col-12 col-md-6 mb-4 d-flex align-items-end">
                        <label class="form-label mb-0 ml-3">ترتیب نمایش</label>
                        <input name="show_cat1" class="form-check-input mt-0 ml-1" type="radio" value="new" <?php if ($show_cat1 == 'new' || !$show_cat1) echo 'checked' ?>>
                        <label class="form-label ml-3 mb-0"> جدیدترین </label>
                        <input name="show_cat1" class="form-check-input mt-0 ml-1" type="radio" value="top" <?php if ($show_cat1 == 'top') echo 'checked' ?>>
                        <label class="form-label mb-0"> پرفروش ترین </label>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">انتخاب دسته بندی دوم</label>
                        <input name="category_section2" class="form-control" id="cat2" placeholder="انتخاب دسته بندی" value="<?php if (is_countable($cats2) && count($cats2) > 0) {
                                                                                                                                    echo $cats2['name'] . ",";
                                                                                                                                } ?>">
                    </div>
                    <div class="col-12 col-md-6 mb-4 d-flex align-items-end">
                        <label class="form-label mb-0 ml-3">ترتیب نمایش</label>
                        <input name="show_cat2" class="form-check-input mt-0 ml-1" type="radio" value="new" <?php if ($show_cat2 == 'new' || !$show_cat2) echo 'checked' ?>>
                        <label class="form-label ml-3 mb-0"> جدیدترین </label>
                        <input name="show_cat2" class="form-check-input mt-0 ml-1" type="radio" value="top" <?php if ($show_cat2 == 'top') echo 'checked' ?>>
                        <label class="form-label mb-0"> پرفروش ترین </label>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="categories_banner1_link" value="<?php echo $categories_banner1_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر اول<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=categories-banner1" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $categories_banner1; ?>" type="text" name="categories_banner1" id="categories-banner1">
                        <img src="<?php echo $categories_banner1; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="categories_banner2_link" value="<?php echo $categories_banner2_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر دوم<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=categories-banner2" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $categories_banner2; ?>" type="text" name="categories_banner2" id="categories-banner2">
                        <img src="<?php echo $categories_banner2; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="categories_banner3_link" value="<?php echo $categories_banner3_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر سوم<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=categories-banner3" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $categories_banner3; ?>" type="text" name="categories_banner3" id="categories-banner3">
                        <img src="<?php echo $categories_banner3; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="categories_banner4_link" value="<?php echo $categories_banner4_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر چهارم<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=categories-banner4" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $categories_banner4; ?>" type="text" name="categories_banner4" id="categories-banner4">
                        <img src="<?php echo $categories_banner4; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="categories" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="new-section" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="new_banner1_link" value="<?php echo $new_banner1_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر اول<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=new-banner1" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $new_banner1; ?>" type="text" name="new_banner1" id="new-banner1">
                        <img src="<?php echo $new_banner1; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینک بنر</label>
                        <input type="text" name="new_banner2_link" value="<?php echo $new_banner2_link; ?>" class="form-control" placeholder="لینک بنر خود را وارد کنید">
                        <label class="form-label d-flex align-items-center mt-3">بنر اول<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=new-banner2" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $new_banner2; ?>" type="text" name="new_banner2" id="new-banner2">
                        <img src="<?php echo $new_banner2; ?>" width="90%" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4 d-flex align-items-end">
                        <label class="form-label mb-0 ml-3">ترتیب نمایش</label>
                        <input name="show_new" class="form-check-input mt-0 ml-1" type="radio" value="new" <?php if ($show_new == 'new' || !$show_new) echo 'checked' ?>>
                        <label class="form-label ml-3 mb-0"> جدیدترین </label>
                        <input name="show_new" class="form-check-input mt-0 ml-1" type="radio" value="top" <?php if ($show_new == 'top') echo 'checked' ?>>
                        <label class="form-label mb-0"> پرفروش ترین </label>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="new-section" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";
    let flatpickrRange = document.querySelector('#flatpickr-range');
    let category_selection1 = document.querySelector('#cat1');
    let category_selection2 = document.querySelector('#cat2');
    let cats = [
        <?php if (is_countable($all_cats) && count($all_cats) > 0) {
            $tree = show_taxonomy($all_cats, 0, $cats);
            echo $tree;
        } ?>
    ];

    function tagTemplate(tagData) {
        return `
            <tag title="${tagData.name}"
            contenteditable='false'
            spellcheck='false'
            tabIndex="-1"
            class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
            ${this.getAttributes(tagData)}
            >
            <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
            <div>
                <span class='tagify__tag-text'>${tagData.name}</span>
            </div>
            </tag>
            `;
    }

    function suggestionItemTemplate(tagData) {
        return `
                <div ${this.getAttributes(tagData)}
                class='tagify__dropdown__item ${tagData.class ? tagData.class : ''}'
                tabindex="0"
                role="option"
                >
                <span>${tagData.name}</span>
                </div>
            `;
    }
    var all_categories1 = new Tagify(category_selection1, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
        maxTags: 1,
        skipInvalid: true, // do not remporarily add invalid tags
        dropdown: {
            classname: 'tags-inline',
            enabled: 0,
            closeOnSelect: false,
            searchKeys: ['name'] // very important to set by which keys to search for suggesttions when typing
        },
        templates: {
            tag: tagTemplate,
            dropdownItem: suggestionItemTemplate
        },
        whitelist: cats
    });
    var all_categories2 = new Tagify(category_selection2, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
        maxTags: 1,
        skipInvalid: true, // do not remporarily add invalid tags
        dropdown: {
            classname: 'tags-inline',
            enabled: 0,
            closeOnSelect: false,
            searchKeys: ['name'] // very important to set by which keys to search for suggesttions when typing
        },
        templates: {
            tag: tagTemplate,
            dropdownItem: suggestionItemTemplate
        },
        whitelist: cats
    });
    if (typeof flatpickrRange != undefined) {
        flatpickrRange.flatpickr({
            mode: 'range',
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            defaultDate: ["<?php echo $sale_start; ?>", "<?php echo $sale_end; ?>"]
        });
    }

    function responsive_filemanager_callback(field_id) {
        let input = $('#' + field_id);
        let images = input.val();
        $(input).parent().find('img').attr("src", images);
        input.val(images);
        close_window();
    }

    function close_window() {
        Fancybox.getInstance().close();
    }
    $('.remove-post-image').click(function() {
        $('#post_image_input').val('delete');
        $('#post_image').attr('src', '');
    });

    $('#add-link').click(function() {
        var count = $('#section-access .col-md-6').length;
        $(this).parent().before('<div class="col-12 col-md-6 mb-4"><label class="form-label">متن لینک</label><input type="text" name="main_page_access[]" value="" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید"><label class="form-label">مقصد لینک</label><input type="text" name="main_page_access_link[]" value="" class="form-control" placeholder="مقصد لینک خود را وارد کنید"><label class="form-label d-flex align-items-center mt-3">تصویر<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=main_page_access_image' + count + '" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label><input hidden value="" type="text" name="main_page_access_image[]" id="main_page_access_image' + count + '"><img src="" width="200px" class="mt-3 d-block mx-auto"></div>');
        count++;
    });
</script>