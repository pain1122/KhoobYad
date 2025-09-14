<?php

if (isset($_POST['submit_identity']) || isset($_POST['submit_header']) || isset($_POST['submit_contact'])) {
    foreach($_POST as $key => $value){
        base::set_option($key, $value);
    }
}



if (isset($_POST['submit_footer'])) {
    for ($i = 1; $i < 5; $i++) {
        base::RunQuery("DELETE FROM `options` WHERE `name` LIKE '%footer_link%$i'");
        $footer_link_text = $_POST['footer_link' . $i . '_text'];
        $footer_link = $_POST['footer_link' . $i];
        if (is_countable($footer_link)) {
            for ($j = 0; $j < count($footer_link); $j++) {
                $text = $footer_link_text[$j];
                $link = $footer_link[$j];
                if(strlen($text)>0 && strlen($link)>0)
                base::RunQuery("INSERT INTO `options` (`name`,`value`) VALUES ('footer_link$i','$link'),('footer_link_text$i','$text')");
            }
        }
    }
}


$flinks_text1 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text1' ORDER BY `option_id` DESC");
$flinks1 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link1' ORDER BY `option_id` DESC");
$flinks_text2 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text2' ORDER BY `option_id` DESC");
$flinks2 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link2' ORDER BY `option_id` DESC");
$flinks_text3 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text3' ORDER BY `option_id` DESC");
$flinks3 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link3' ORDER BY `option_id` DESC");
$flinks_text4 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text4' ORDER BY `option_id` DESC");
$flinks4 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link4' ORDER BY `option_id` DESC");
$site_logo = base::get_option('site_logo');
$fav_icon = base::get_option('fav_icon');
$seo_title = base::get_option('seo_title');
$seo_keywords = base::get_option('seo_keywords');
$seo_desc = base::get_option('seo_desc');
$s_free = base::get_option('s_free');
$s_delivery = base::get_option('s_delivery');
$s_post = base::get_option('s_post');
$header_banner = base::get_option('header_banner');
$mobile_header_banner = base::get_option('mobile_header_banner');
$address = base::get_option('address');
$providence = base::get_option('providence');
$phone_number1 = base::get_option('phone_number1');
$phone_number2 = base::get_option('phone_number2');
$phone_number3 = base::get_option('phone_number3');
$phone_number4 = base::get_option('phone_number4');
$email1 = base::get_option('email1');
$email2 = base::get_option('email2');
$whatsapp = base::get_option('whatsapp');
$telegram = base::get_option('telegram');
$instagram = base::get_option('instagram');
$linkedin = base::get_option('linkedin');
$twitter = base::get_option('twitter');
$facebook = base::get_option('facebook');
$expectation = base::get_option('expectation');
?>
<div class="card">
    <h5 class="card-header">تنظیمات سایت</h5>
    <div class="nav-align-top pb-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#site-identity" aria-controls="site-identity" aria-selected="true">
                    هویت سایت
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#site-header" aria-controls="site-header" aria-selected="false">
                    هدر
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#site-footer" aria-controls="site-footer" aria-selected="false">
                    فوتر
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#site-contact" aria-controls="site-contact" aria-selected="false">
                    راه های ارتباطی
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="site-identity" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label d-flex align-items-center">لوگو سایت<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=logo" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $site_logo; ?>" type="text" name="site_logo" id="logo">
                        <img src="<?php echo $site_logo; ?>" id="site_logo" width="100px" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label d-flex align-items-center">فاو آیکن<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=fav" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $fav_icon; ?>" type="text" name="fav_icon" id="fav">
                        <img src="<?php echo $fav_icon; ?>" id="fav_icon" width="100px" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <div class="position-relative">
                            <label class="form-label">متا تایتل</label>
                            <input type="text" name="seo_title" onkeyup="countChar(this,65,'seo_title')" onchange="countChar(this,'seo_title')" value="<?php echo $seo_title; ?>" class="form-control" placeholder="متا تایتل خود را وارد کنید">
                            <div id="seo_title" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_title); ?></span>/<span>65</span></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">متا کیوردز</label>
                        <input type="text" name="seo_keywords" value="<?php echo $seo_keywords; ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                    </div>
                    <div class="col-12 mb-4">
                        <div class="mb-3 position-relative">
                            <label class="form-label">متا دیسکریپشن</label>
                            <input type="text" name="seo_desc" onkeyup="countChar(this,165,'seo_desc')" onchange="countChar(this,'seo_desc')" value="<?php echo $seo_desc; ?>" class="form-control" placeholder="متا دیسکریپشن خود را وارد کنید">
                            <div id="seo_desc" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_desc,); ?></span>/<span>165</span></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-4">
                        <label class="form-label">هزینه ارسال پست</label>
                        <input type="text" name="s_post" value="<?php echo $s_post; ?>" class="form-control" placeholder="هزینه ارسال پست را وارد کنید">
                    </div>
                    <div class="col-12 col-md-4 mb-4">
                        <label class="form-label">هزینه ارسال پیک</label>
                        <input type="text" name="s_delivery" value="<?php echo $s_delivery; ?>" class="form-control" placeholder="هزینه ارسال پیک را وارد کنید">
                    </div>
                    <div class="col-12 col-md-4 mb-4">
                        <label class="form-label">حداقل هزینه ارسال رایگان</label>
                        <input type="text" name="s_free" value="<?php echo $s_free; ?>" class="form-control" placeholder="حداقل هزینه ارسال رایگان را وارد کنید">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit_identity" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="site-header" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label d-flex align-items-center">بنر هدر<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=header-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $header_banner; ?>" type="text" name="header_banner" id="header-banner">
                        <img src="<?php echo $header_banner; ?>" id="header_banner" width="100px" class="mt-3 d-block">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label d-flex align-items-center">بنر هدر موبایل<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=mobile-header-banner" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                        <input hidden value="<?php echo $mobile_header_banner; ?>" type="text" name="mobile_header_banner" id="mobile-header-banner">
                        <img src="<?php echo $mobile_header_banner; ?>" id="mobile_header_banner" width="100px" class="mt-3 d-block">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit_header" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="site-footer" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <h6>لینک های ستون اول</h6>
                        <?php if (is_countable($flinks1) && count($flinks1) > 0) :
                            for ($i = 0; $i < count($flinks1); $i++) : ?>
                                <div class="mb-3">
                                    <label class="form-label">متن لینک</label>
                                    <input type="text" name="footer_link1_text[]" value="<?php echo $flinks_text1[$i]['value'] ?>" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                    <label class="form-label">مقصد لینک</label>
                                    <input type="text" name="footer_link1[]" value="<?php echo $flinks1[$i]['value']; ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                                </div>
                            <?php endfor;
                        else : ?>
                            <div class="mb-3">
                                <label class="form-label">متن لینک</label>
                                <input type="text" name="footer_link1_text[]" value="" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                <label class="form-label">مقصد لینک</label>
                                <input type="text" name="footer_link1[]" value="" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                            </div>
                        <?php endif; ?>
                        <button onclick="add_link(1,this)" type="button" class="btn btn-primary btn-sm">اضافه کردن</button>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <h6>لینک های ستون دوم</h6>
                        <?php if (is_countable($flinks2) && count($flinks2) > 0) :
                            for ($i = 0; $i < count($flinks2); $i++) : ?>
                                <div class="mb-3">
                                    <label class="form-label">متن لینک</label>
                                    <input type="text" name="footer_link2_text[]" value="<?php echo $flinks_text2[$i]['value']; ?>" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                    <label class="form-label">مقصد لینک</label>
                                    <input type="text" name="footer_link2[]" value="<?php echo $flinks2[$i]['value'] ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                                </div>
                            <?php endfor;
                        else : ?>
                            <div class="mb-3">
                                <label class="form-label">متن لینک</label>
                                <input type="text" name="footer_link2_text[]" value="" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                <label class="form-label">مقصد لینک</label>
                                <input type="text" name="footer_link2[]" value="" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                            </div>
                        <?php endif; ?>
                        <button onclick="add_link(2,this)" type="button" class="btn btn-primary btn-sm">اضافه کردن</button>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <h6>لینک های ستون سوم</h6>
                        <?php if (is_countable($flinks3) && count($flinks3) > 0) :
                            for ($i = 0; $i < count($flinks3); $i++) : ?>
                                <div class="mb-3">
                                    <label class="form-label">متن لینک</label>
                                    <input type="text" name="footer_link3_text[]" value="<?php echo $flinks_text3[$i]['value']; ?>" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                    <label class="form-label">مقصد لینک</label>
                                    <input type="text" name="footer_link3[]" value="<?php echo $flinks3[$i]['value'] ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                                </div>
                            <?php endfor;
                        else : ?>
                            <div class="mb-3">
                                <label class="form-label">متن لینک</label>
                                <input type="text" name="footer_link3_text[]" value="" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                <label class="form-label">مقصد لینک</label>
                                <input type="text" name="footer_link3[]" value="" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                            </div>
                        <?php endif; ?>
                        <button onclick="add_link(3,this)" type="button" class="btn btn-primary btn-sm">اضافه کردن</button>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <h6>لینک های مهم</h6>
                        <?php if (is_countable($flinks4) && count($flinks4) > 0) :
                            for ($i = 0; $i < count($flinks4); $i++) : ?>
                                <div class="mb-3">
                                    <label class="form-label">متن لینک</label>
                                    <input type="text" name="footer_link4_text[]" value="<?php echo $flinks_text4[$i]['value']; ?>" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                    <label class="form-label">مقصد لینک</label>
                                    <input type="text" name="footer_link4[]" value="<?php echo $flinks4[$i]['value'] ?>" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                                </div>
                            <?php endfor;
                        else : ?>
                            <div class="mb-3">
                                <label class="form-label">متن لینک</label>
                                <input type="text" name="footer_link4_text[]" value="" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید">
                                <label class="form-label">مقصد لینک</label>
                                <input type="text" name="footer_link4[]" value="" class="form-control" placeholder="مقصد لینک خود را وارد کنید">
                            </div>
                        <?php endif; ?>
                        <button onclick="add_link(4,this)" type="button" class="btn btn-primary btn-sm">اضافه کردن</button>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit_footer" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="site-contact" role="tabpanel">
                <form method="POST" action="" class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">آدرس</label>
                        <input type="text" name="address" value="<?php echo $address; ?>" class="form-control" placeholder="آدرس خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">استان/شهر</label>
                        <input type="text" name="providence" value="<?php echo $providence; ?>" class="form-control" placeholder="شهر یا استان خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">شماره تماس اول</label>
                        <input type="text" name="phone_number1" value="<?php echo $phone_number1; ?>" class="form-control" placeholder="شماره تماس اول خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">شماره تماس دوم</label>
                        <input type="text" name="phone_number2" value="<?php echo $phone_number2; ?>" class="form-control" placeholder="شماره تماس دوم خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">شماره تماس سوم</label>
                        <input type="text" name="phone_number3" value="<?php echo $phone_number3; ?>" class="form-control" placeholder="شماره تماس سوم خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">شماره تماس چهارم</label>
                        <input type="text" name="phone_number4" value="<?php echo $phone_number4; ?>" class="form-control" placeholder="شماره تماس چهارم خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">ایمیل اول</label>
                        <input type="text" name="email1" value="<?php echo $email1; ?>" class="form-control" placeholder="ایمیل اول خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">ایمیل دوم</label>
                        <input type="text" name="email2" value="<?php echo $email2; ?>" class="form-control" placeholder="ایمیل دوم خود را وارد کنید">
                    </div>
                    <hr>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">واتس اپ</label>
                        <input type="text" name="whatsapp" value="<?php echo $whatsapp; ?>" class="form-control" placeholder="لینک واتس اپ خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">تلگرام</label>
                        <input type="text" name="telegram" value="<?php echo $telegram; ?>" class="form-control" placeholder="لینک تلگرام خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">اینستاگرام</label>
                        <input type="text" name="instagram" value="<?php echo $instagram; ?>" class="form-control" placeholder="لینک اینستاگرام خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">لینکداین</label>
                        <input type="text" name="linkedin" value="<?php echo $linkedin; ?>" class="form-control" placeholder="لینک لینکداین خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">توییتر</label>
                        <input type="text" name="twitter" value="<?php echo $twitter; ?>" class="form-control" placeholder="لینک توییتر خود را وارد کنید">
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">فیسبوک</label>
                        <input type="text" name="facebook" value="<?php echo $facebook; ?>" class="form-control" placeholder="لینک فیسبوک خود را وارد کنید">
                    </div>
                    <hr>
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <h2 class="form-label">شرایط خرید حضوری</h2>
                            <div class="card p-0">
                                <textarea id="editor1" name="expectation"><?php echo $expectation; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit_contact" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";

    function responsive_filemanager_callback(field_id) {
        let input = $('#' + field_id);
        let images = input.val();
        let name = input.attr('name');
        $('#' + name).attr("src", images);
        input.val(images);
        close_window();
    }

    function close_window() {
        Fancybox.getInstance().close();
    }
    $('.remove-post-image').click(function(){
        $('#post_image_input').val('delete');
        $('#post_image').attr('src','');
    });

    function add_link(i, element) {
        $(element).before('<div class="mb-3"><label class="form-label">متن لینک</label><input type="text" name="footer_link' + i + '_text[]" class="form-control mb-2" placeholder="متن لینک خود را وارد کنید"><label class="form-label">مقصد لینک</label><input type="text" name="footer_link' + i + '[]" class="form-control" placeholder="مقصد لینک خود را وارد کنید"></div>');
    }
</script>
<script src="assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.custom.js"></script>