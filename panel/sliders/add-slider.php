<?php

if (isset($_GET['delete'])) {
    Base::RunQuery("DELETE FROM `post` WHERE `post_id` = {$_GET['delete']} AND `post_type` = 'slider_image'");
    Base::redirect("?page=sliders/add-slider.php&id={$_GET['id']}");
}
if (isset($_POST['slider_title'])) {
    $post_id = null;
    if (isset($_GET['id']))
        $post_id = $_GET['id'];
    $obj = new post($post_id);
    $post_id = $obj->get_id();
    $post_status = 'publish';
    $obj->set_post_type('slider');
    $author = $uid;
    $title = $_POST['slider_title'];
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_guid($guid);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    $metas = [
        "autoplay" => $_POST['autoplay'],
        "lazyload" => $_POST['lazyload'],
        "fade" => $_POST['fade'],
        "infinite" => $_POST['infinite'],
        "vertical" => $_POST['vertical']
    ];
    $obj->insert_meta($metas);
    Base::RunQuery("DELETE FROM `post` WHERE `post_parent` = $post_id");
    if (is_countable($_POST['slider_images']) && count($_POST['slider_images']) > 0) {
        for ($i = 0; $i < count($_POST['slider_images']); $i++) {
            if ($_POST['slider_images'][$i] != "") {
                $image = new post('new');
                $image->set_post_type('slider_image');
                $image->set_author($uid);
                $image->set_title($_POST['slider_images'][$i]);
                $image->set_status('publish');
                $image->set_post_modify('CURRENT_TIMESTAMP');
                $image->set_parent($post_id);
                $metas = [
                    "title" => $_POST['slider_image_title'][$i],
                    "text" => $_POST['slider_image_text'][$i],
                ];
                $image->insert_meta($metas);
            }
        }
    }
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=sliders/add-slider.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $obj = new post($post_id);
    $title = $obj->get_title();
    $images = Base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_parent` = $post_id AND `post_type` = 'slider_image'");
}
?>
<link rel="stylesheet" href="assets/vendor/libs/grapes/grapes.min.css">
<link rel="stylesheet" href="assets/vendor/libs/grapes/grapesjs-preset-webpage.min.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">نام اسلایدر</label>
                    <input type="text" name="slider_title" value="<?php echo $title; ?>" class="form-control" placeholder="نام اسلایدر را وارد کنید" require>
                </div>
                <div class="card accordion-item mb-3">
                    <h6 class="mb-3">تنظیمات اسلایدر</h6>
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="form-check form-switch col-12 col-md-6">
                            <input <?php if ($post_id > 0 && $obj->get_meta('autoplay')) echo "checked" ?> type="checkbox" name="autoplay" value="true" id="flexSwitchCheckChecked" class="form-check-input">
                            <label class="form-check-label">autoplay</label>
                        </div>
                        <div class="form-check form-switch col-12 col-md-6">
                            <input <?php if ($post_id > 0 && $obj->get_meta('lazyload')) echo "checked" ?> type="checkbox" name="lazyload" value="progressive" id="flexSwitchCheckChecked" class="form-check-input">
                            <label class="form-check-label">lazy load</label>
                        </div>
                        <div class="form-check form-switch col-12 col-md-6">
                            <input <?php if ($post_id > 0 && $obj->get_meta('fade')) echo "checked" ?> type="checkbox" name="fade" value="true" id="flexSwitchCheckChecked" class="form-check-input">
                            <label class="form-check-label">fade</label>
                        </div>
                        <div class="form-check form-switch col-12 col-md-6">
                            <input <?php if ($post_id > 0 && $obj->get_meta('infinite')) echo "checked" ?> type="checkbox" name="infinite" value="true" id="flexSwitchCheckChecked" class="form-check-input">
                            <label class="form-check-label">بینهایت</label>
                        </div>
                        <div class="form-check form-switch col-12 col-md-6">
                            <input <?php if ($post_id > 0 && $obj->get_meta('vertical')) echo "checked" ?> type="checkbox" name="vertical" value="true" id="flexSwitchCheckChecked" class="form-check-input">
                            <label class="form-check-label">عمودی</label>
                        </div>
                    </div>
                </div>
                <div>
                    <button id="add-link" type="button" class="btn btn-primary ml-3">اضافه کردن</button>
                    <button type="submit" name="btnsubmit" class="btn btn-success"><i class="fa-regular fa-pen-to-square"></i> انتشار</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-12 order-3">
        <div class="row" id="slider-container">

            <?php if (is_countable($images) && count($images) > 0) :
                for ($i = 0; $i < count($images); $i++) :
                    $image = new post($images[$i]['post_id']); ?>
                    <div class="sliders_images col-4 mb-4">
                        <div class="card p-3">
                            <a href="?page=sliders/add-slider.php&id=<?php echo $_GET['id'] ?>&delete=<?php echo $image->get_id(); ?>"><button type="button" class="btn-close"></button></a>
                            <div class="card-body">
                                <label class="form-label d-flex align-items-center mb-3">تصویر<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=slider_image<?php echo $i; ?>" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label>
                                <input hidden value="<?php echo $image->get_title(); ?>" type="text" name="slider_images[]" id="slider_image<?php echo $i; ?>">
                                <img src="<?php echo $image->get_title(); ?>" width="90%" class="mb-3 mx-auto d-block">
                                <div class="mb-3">
                                    <label class="form-label">عنوان اسلاید</label>
                                    <input type="text" name="slider_image_title[]" value="<?php echo $image->get_meta('title') ?>" class="form-control" placeholder="عنوان اسلاید را وارد کنید" require="">
                                </div>
                                <div>
                                    <label class="form-label">متن اسلاید</label>
                                    <input type="text" name="slider_image_text[]" value="<?php echo $image->get_meta('text') ?>" class="form-control" placeholder="متن اسلاید را وارد کنید" require="">
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endfor;
            endif; ?>
        </div>
    </div>
</form>

<script>
    $('#add-link').click(function() {
        var count = $('.sliders_images').length;
        $('#slider-container').append('<div class="sliders_images col-4 mb-4"><div class="card p-3"><button type="button" class="btn-close" onclick="$(this).parent().parent().remove();"></button><div class="card-body"><label class="form-label d-flex align-items-center mb-3">تصویر<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&amp;field_id=slider_image' + count + '" data-fancybox="" data-type="iframe" data-preload="false">انتخاب عکس</a></label><input hidden type="text" name="slider_images[]" id="slider_image' + count +'"><img width="90%" class="mb-3 mx-auto d-block"><div class="mb-3"><label class="form-label">عنوان اسلاید</label><input type="text" name="slider_image_title[]" class="form-control" placeholder="عنوان اسلاید را وارد کنید" require=""></div><div><label class="form-label">متن اسلاید</label><input type="text" name="slider_image_text[]" class="form-control" placeholder="متن اسلاید را وارد کنید" require=""></div></div></div></div>');
        count++;
    });

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
    $('.remove-post-image').click(function(){
        $('#post_image_input').val('delete');
        $('#post_image').attr('src','');
    });
</script>