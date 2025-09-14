<?php
if (isset($_POST['submit']) || isset($_POST['draft'])) {
    if (isset($_GET['id']))
        $post_id = intval($_GET['id']);
    else
        $post_id = 'new_post';
    $obj = new product($post_id);
    $obj->set_post_type('test-book');
    $post_id = $obj->get_id();
    $post_status = 'publish';
    if (isset($_POST['draft']))
        $post_status = 'draft';
    $author = $uid;
    $title = trim($_POST['post_title']);
    $guid = urlencode(str_replace(' ', '-', $title . '-' . $post_id));
    $regular_price = $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    if ($sale_price >= 0)
        $price = $sale_price;
    $duration = $_POST['duration'];
    $negative_score = $_POST['negative-score'];
    $buy_for_others = $_POST['buy-for-others'];
    $test_book_scale = $_POST['test-book-scale'];
    $test_book_rank = $_POST['test-book-rank'];
    $test_book_questions = $_POST['test_book_questions'];
    $test_book_answers = $_POST['test_book_answers'];
    $lessons = $_POST['lessons'];
    if (!empty($lessons))
        $lessons = json_encode($lessons, JSON_UNESCAPED_UNICODE);
    $members = json_encode(json_decode($_POST['member_selection'], true), JSON_UNESCAPED_UNICODE);
    $classes = json_encode(json_decode($_POST['class_selection'], true), JSON_UNESCAPED_UNICODE);
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_slug($guid);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    if (is_countable($test_book_rank) && count($test_book_rank) > 0){
        for ($i = 0; $i < count($test_book_rank); $i++) {
            $test_book_prediction[$test_book_scale[$i]] = $test_book_rank[$i];
        }
        $test_book_prediction = json_encode($test_book_prediction, JSON_UNESCAPED_UNICODE);
    }else{
        $test_book_prediction = '';
    }
    $metas = [
        "_price" => $price,
        "_regular_price" => $regular_price,
        "_sale_price" => $sale_price,
        "duration" => $duration,
        "test_book_prediction" => $test_book_prediction,
        "buy_for_others" => $buy_for_others,
        "negative_score" => $negative_score,
        "members" => $members,
        "classes" => $classes,
        "test_book_answers" => $test_book_answers,
        "test_book_questions" => $test_book_questions,
        "lessons" => $lessons,
        "term_start" => $term_start,
        "term_end" => $term_end
    ];
    $obj->insert_meta($metas);
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=test-books/add-test-book.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $obj = new product($post_id);
    $obj->set_post_type('test-book');
    $title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $price = $obj->get_regular_price();
    $sale_price = $obj->get_sale_price();
    $term_start = $obj->get_meta('term_start');
    $term_end = $obj->get_meta('term_end');
    $duration = $obj->get_meta('duration');
    $buy_for_others = $obj->get_meta('buy_for_others');
    $on_time = $obj->get_meta('on_time');
    $negative_score = $obj->get_meta('negative_score');
    $test_book_questions = $obj->get_meta('test_book_questions');
    $test_book_answers = $obj->get_meta('test_book_answers');
    $test_book_prediction = json_decode($obj->get_meta('test_book_prediction'), true);
    $members = json_decode($obj->get_meta('members'), true);
    $classes = json_decode($obj->get_meta('classes'), true);
    $lessons = json_decode($obj->get_meta('lessons'), true);
    if (is_countable($lessons))
        $lessons_count = count($lessons);
}
$select_classes = "SELECT `tag`.`tag_id` as `id`,`name` FROM `tag` INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id` WHERE `type` = 'class_group' GROUP BY `tag`.`tag_id`";
$all_classes = base::FetchArray($select_classes);
$members_q = "SELECT `users`.`user_id` AS `id`,CONCAT(`user_meta2`.`value`, ' ',`user_meta3`.`value`) AS `name` FROM `users`
    INNER JOIN `user_meta` as `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
    INNER JOIN `user_meta` as `user_meta2` ON `users`.`user_id` = `user_meta2`.`user_id`
    INNER JOIN `user_meta` as `user_meta3` ON `users`.`user_id` = `user_meta3`.`user_id`
    WHERE `user_meta`.`key` = 'role' AND `user_meta`.`value` = 'student'
    AND `user_meta2`.`key` = 'firstname' 
    AND `user_meta3`.`key` = 'lastname'
    GROUP BY `user_meta`.`user_id`";
$all_members = base::FetchArray($members_q);
function show_taxonomy(array $array)
{
    $branch = "";
    foreach ($array as $arr) {
        $tagid = intval($arr['id']);
        $tagname = $arr['name']; ?>
        {name: '<?php echo $tagname; ?>', value:<?php echo $tagid; ?>},
        <?php
    }
    return $branch;
}
?>
<link rel="stylesheet" href="assets/vendor/libs/tagify/tagify.css">
<link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
<link rel="stylesheet" href="assets/vendor/libs/sweetalert2/sweetalert2.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 co-12">
                        <label class="form-label">عنوان</label>
                        <input type="text" name="post_title" value="<?php echo $title; ?>"
                            onkeyup="replace_digits(this);insert_sku(this,'post_guid')"
                            onchange="insert_sku(this,'post_guid');" class="form-control"
                            placeholder="عنوان تست بوک خود را وارد کنید" required>
                    </div>
                    <div class="mb-3 col-12 col-lg-4">
                        <label class="form-label">مهلت تست بوک</label>
                        <input type="number" name="duration" value="<?php echo $duration; ?>" min="0" class="form-control"
                            placeholder="خالی مساوی است با نامحدود">
                    </div>
                    <div class="mb-3 col-12 col-lg-4">
                        <label class="form-label">قیمت تست بوک</label>
                        <input type="text" name="price" value="<?php echo $price; ?>" onkeyup="replace_digits(this);"
                            class="form-control mb-3" placeholder="قیمت تست بوک خود را وارد کنید">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label">قیمت تخفیف خورده</label>
                        <input type="text" name="sale_price" value="<?php echo $sale_price; ?>"
                            onkeyup="replace_digits(this);" class="form-control mb-3"
                            placeholder="قیمت تخفیف خورده خود را وارد کنید">
                    </div>
                </div>
                <div class="card-footer row" id="test-book-scales">
                    <?php foreach ($test_book_prediction as $scale => $rank) { ?>
                        <div class="col-12 col-md-5 mb-3">
                            <label class="form-label">بازه تراز</label>
                            <input type="text" name="test-book-scale[]" value="<?php echo $scale; ?>" placeholder="مثلا 0-1000"
                                onkeyup="replace_digits(this);" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-5 mb-3">
                            <label class="form-label">بازه رتبه</label>
                            <input type="text" name="test-book-rank[]" value="<?php echo $rank; ?>" placeholder="مثلا 0-1000"
                                onkeyup="replace_digits(this);" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-2 mb-3"><button type="button" class="btn btn-sm btn-danger mr-3"
                                onclick="$(this).parent().prev().remove();$(this).parent().prev().remove();$(this).parent().remove();">حذف</button>
                        </div>
                    <?php } ?>
                    <button type="button" id="add-overall-scale" class="btn btn-secondary col-4 col-lg-2"><i
                            class="fa fa-plus-square"></i></button>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">افزودن درس</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label">نام درس</label>
                        <input type="text" id="lesson-title" onkeyup="replace_digits(this);" class="form-control">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label">ضریب درس</label>
                        <input type="text" id="lesson-coefficient" onkeyup="replace_digits(this);" class="form-control">
                    </div>
                    <hr>
                    <div class="card-footer row" id="scales">
                        <button type="button" id="add-scale" class="btn btn-secondary col-4 col-lg-2"><i
                                class="fa fa-plus-square"></i></button>
                    </div>
                    <div class="col-12">
                        <button type="button" id="add-lesson" class='btn btn-primary'
                            value="<?php echo $lessons_count; ?>">اضافه کردن</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4<?php if (!$lessons_count)
            echo " d-none"; ?>" id="lessons-wrapper">
            <div class="nav-align-top">
                <ul class="nav nav-pills p-4 justify-content-between justify-content-sm-start" role="tablist"
                    id="lesson-tab">
                    <?php for ($i = 1; $i < $lessons_count + 1; $i++) { ?>
                        <li id="lesson-tab<?php echo $i; ?>">
                            <button type="button" class="nav-link px-2 px-md-4 <?php if ($i == 1)
                                echo 'active'; ?>" role="tab" data-bs-toggle="tab"
                                data-bs-target="#lesson-content<?php echo $i; ?>"
                                aria-controls="lesson-content<?php echo $i; ?>" aria-selected="true">درس
                                <?php echo $lessons[$i]['title']; ?></button>
                        </li>
                    <?php } ?>
                </ul>
                <div class="tab-content" style="box-shadow: none;" id="lesson-content">
                    <?php for ($i = 1; $i < $lessons_count + 1; $i++) {
                        $questions = $lessons[$i]['questions'];
                        $prediction = $lessons[$i]['prediction'];
                        ?>
                        <div class='tab-pane fade  <?php if ($i == 1)
                            echo 'active show'; ?>' id='lesson-content<?php echo $i; ?>' role='tabpanel'>
                            <h5 class='w-100 mb-4'>سوالات درس <?php echo $lessons[$i]['title']; ?></h5>
                            <?php $j = 1;
                            foreach ($questions as $question) {
                                $answer_count = $question['answer_count'];
                                $answer_video = $question['answer_video'];
                                $answer_topic = $question['answer_topic'];
                                $answer_option = $question['answer_option']; ?>
                                <div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-4">
                                    <h5 class="d-flex align-items-center w-100 mb-3">سوال شماره <?php echo $j; ?><span
                                            onclick="add_answer(this,<?php echo $j; ?>,<?php echo $i; ?>)"
                                            class="badge btn btn-success mr-auto">اضافه کردن جواب</span><span
                                            onclick="remove_answer(this)" class="badge btn btn-danger mr-3">حذف
                                            جواب</span><input type="hidden"
                                            name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_count]"
                                            value="<?php echo $answer_count; ?>"></h5>
                                    <label class="form-check-label flex-column d-flex w-100 mb-3">لینک ویدئو سوال
                                        <?php echo $j; ?><input class="form-control ml-3 w-100" type="text"
                                            name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_video]"
                                            value="<?php echo $answer_video; ?>"></label>
                                    <label class="form-check-label flex-column d-flex w-100 mb-3">مبحث سوال
                                        <?php echo $j; ?><input class="form-control ml-3 w-100" type="text"
                                            name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_topic]"
                                            value="<?php echo $answer_topic; ?>"></label>
                                    <?php for ($coun_answers = 1; $coun_answers < $answer_count + 1; $coun_answers++) { ?>
                                        <div class="form-check form-check-inline answer ml-3 mb-2">
                                            <label class="form-check-label"><input class="form-check-input" type="radio" <?php if ($coun_answers == $answer_option)
                                                echo "checked"; ?>
                                                    id="answer<?php echo $coun_answers; ?>"
                                                    name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_option]"
                                                    value="<?php echo $coun_answers; ?>">گزینه <?php echo $coun_answers; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php $j++;
                            } ?>
                            <span onclick='add_question(this,<?php echo $i; ?>)' class='btn btn-success'>اضافه کردن
                                سوال</span>
                            <span onclick='remove_question(this)' class='btn btn-danger mr-3'>حذف سوال</span>
                            <button type='button' class='btn btn-danger mr-3' onclick='remove_lesson(<?php echo $i; ?>)'>حذف
                                درس</button>
                            <input type='hidden' name='lessons[<?php echo $i; ?>][coefficient]'
                                value='<?php echo $lessons[$i]['coefficient']; ?>'>
                            <input type='hidden' name='lessons[<?php echo $i; ?>][title]'
                                value='<?php echo $lessons[$i]['title']; ?>'>
                            <?php foreach ($prediction as $scale => $rank) {
                                echo "<input type='hidden' name='lessons[$i][prediction][$scale]'
                                value='$rank'>";
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">عملیات</h5>
            </div>

            <div class="card-body d-flex justify-content-around p-3 pt-0">
                <button type="submit" name="submit" class="btn btn-success p-2"><i
                        class="fa-regular fa-pen-to-square"></i> انتشار</button>
                <button type="submit" name="draft" class="btn btn-primary p-2"><i class="fa-regular fa-square-pen"></i>
                    پیش نویس</button>
                <?php if (!empty($guid)): ?>
                    <a href="/panel/index.php?page=test-books/view.php&test-book=<?php echo $post_id; ?>" class="btn btn-info p-2"><i
                            class="fa-regular fa-eye"></i> پیش نمایش</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">مقررات</h5>
            <div class="card-body">
                <div class="form-check mb-2">
                    <label class="form-check-label"> تست بوک نمره منفی دارد ؟</label><input class="form-check-input"
                        type="checkbox" value="true" <?php if ($negative_score == 'true')
                            echo "checked"; ?>
                        name="negative-score">
                </div>
                <div class="form-check mb-0">
                    <label class="form-check-label"> تست بوک قابل خرید برای سایر کاربران میباشد ؟</label><input
                        class="form-check-input" type="checkbox" <?php if ($buy_for_others == 'true')
                            echo "checked"; ?>
                        value="true" name="buy-for-others">
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">کلاس ها</h5>
            <div class="card-body">
                <label class="form-label">انتخاب کلاس ها</label>
                <input name="class_selection" class="form-control class_selection" placeholder="انتخاب کلاس ها" value="<?php if (is_countable($classes) && count($classes) > 0) {
                    foreach ($classes as $class) {
                        echo $class['name'] . ",";
                    }
                } ?>">
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">دانش آموزان</h5>
            <div class="card-body">
                <label class="form-label">انتخاب دانش آموزان</label>
                <input name="member_selection" class="form-control member_selection" placeholder="انتخاب دانش آموزان"
                    value="<?php if (is_countable($members) && count($members) > 0) {
                        foreach ($members as $member) {
                            echo $member['name'] . ",";
                        }
                    } ?>">
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">انتخاب فایل سوال های تست بوک<a
                    class='btn btn-sm btn-success mr-auto mb-2'
                    href='filemanager/dialog.php?type=1s&field_id=test_book_questions' data-fancybox data-type='iframe'
                    data-preload='false'>انتخاب فایل</a></h5>
            <div class="card-body">
                <input type='text' name='test_book_questions' class='form-control mb-3' id='test_book_questions'
                    value="<?php echo $test_book_questions; ?>">
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">انتخاب فایل جواب های تست بوک<a
                    class='btn btn-sm btn-success mr-auto mb-2'
                    href='filemanager/dialog.php?type=1s&field_id=test_book_answers' data-fancybox data-type='iframe'
                    data-preload='false'>انتخاب فایل</a></h5>
            <div class="card-body">
                <input type='text' name='test_book_answers' class='form-control mb-3' id='test_book_answers'
                    value="<?php echo $test_book_answers; ?>">
            </div>
        </div>
    </div>
</form>

<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";

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
    $('.remove-post-image').click(function () {
        $('#post_image_input').val('delete');
        $('#post_image').attr('src', '');
    });
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

    $("#add-scale").click(function () {
        $(this).before(`<div class="col-12 col-md-5 mb-3">
                        <label class="form-label">بازه تراز</label>
                        <input type="text" data-id="lesson-scale" placeholder="مثلا 0-1000" onkeyup="replace_digits(this);" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-5 mb-3">
                        <label class="form-label">بازه رتبه</label>
                        <input type="text" data-id="lesson-rank" placeholder="مثلا 0-1000" onkeyup="replace_digits(this);" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-2 mb-3"><button type="button" class="btn btn-sm btn-danger mr-3" onclick="$(this).parent().prev().remove();$(this).parent().prev().remove();$(this).parent().remove();">حذف</button></div>`)
    });
    $("#add-overall-scale").click(function () {
        $(this).before(`<div class="col-12 col-md-5 mb-3">
                        <label class="form-label">بازه تراز</label>
                        <input type="text" name="test-book-scale[]" placeholder="مثلا 0-1000" onkeyup="replace_digits(this);" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-5 mb-3">
                        <label class="form-label">بازه رتبه</label>
                        <input type="text" name="test-book-rank[]" placeholder="مثلا 0-1000" onkeyup="replace_digits(this);" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-2 mb-3"><button type="button" class="btn btn-sm btn-danger mr-3" onclick="$(this).parent().prev().remove();$(this).parent().prev().remove();$(this).parent().remove();">حذف</button></div>`)
    });

    $('#add-lesson').click(function () {
        var count = $(this).val();
        count++;
        $(this).val(count);
        var title = $('#lesson-title').val();
        var active = prediction_q = '';
        var scale = $('input[data-id="lesson-scale"]');
        var rank = $('input[data-id="lesson-rank"]');
        if (count == 1)
            active = 'active';
        var coefficient = $('#lesson-coefficient').val();
        if (isNaN(coefficient) || coefficient < 0) {
            Swal.fire(
                'خطا!',
                'مقدار ضریب باید عدد صحیح باشد',
                'error'
            )
        } else if (!title || !coefficient) {
            Swal.fire(
                'خطا!',
                'لطفا هر دو مقادیر را پر کنید',
                'error'
            )
        } else {
            if (scale.length > 0 && rank.length > 0) {
                for (var i = 0; i < scale.length; i++) {
                    if (!scale[i].value || !rank[i].value) {
                        Swal.fire(
                            'خطا!',
                            'لطفا هر دو مقادیر تراز را پر کنید',
                            'error'
                        );
                        prediction_q = "";
                        return;
                    } else {
                        prediction_q = prediction_q.concat("<input type='hidden' name='lessons[" + count + "][prediction][" + scale[i].value + "]' value='" + rank[i].value + "'>");
                    }
                }
            }
            $('#lessons-wrapper').removeClass('d-none');
            $('#lesson-tab').append('<li id="lesson-tab' + count + '"><button type="button" class="nav-link px-2 px-md-4 ' + active + '" role="tab" data-bs-toggle="tab" data-bs-target="#lesson-content' + count + '" aria-controls="lesson-content' + count + '" aria-selected="true">درس ' + title + '</button></li>');
            if (count == 1)
                active = 'active show';
            $('#lesson-content').append("<div class='tab-pane fade  " + active + "' id='lesson-content" + count + "' role='tabpanel'><h5 class='w-100 mb-4'>سوالات درس " + title + "</h5><span onclick='add_question(this," + count + ")' class='btn btn-success'>اضافه کردن سوال</span><span onclick='remove_question(this)' class='btn btn-danger mr-3'>حذف سوال</span><button type='button' class='btn btn-danger mr-3' onclick='remove_lesson(" + count + ")'>حذف درس</button><input type='hidden' name='lessons[" + count + "][coefficient]' value='" + coefficient + "'><input type='hidden' name='lessons[" + count + "][title]' value='" + title + "'>" + prediction_q + "</div>");

            $('#lesson-coefficient').val('');
            $('#lesson-title').val('');
            $('#scales div').remove();
        }

    })

    function remove_lesson(count) {
        $("#lesson-tab" + count).remove();
        $("#lesson-content" + count).remove();
        if ($("#lesson-tab li").length < 1) {
            $('#lessons-wrapper').addClass('d-none');
            $('#add-lesson').val(0);
        }
    }

    function add_question(element, lesson) {
        var questions_count = $(element).parent().find('.lesson-question').length;
        questions_count++;
        $(element).before('<div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-4"><h5 class="d-flex align-items-center w-100 mb-3">سوال شماره ' + questions_count + '<span onclick="add_answer(this,' + questions_count + ',' + lesson + ')" class="badge btn btn-success mr-auto">اضافه کردن جواب</span><span onclick="remove_answer(this)" class="badge btn btn-danger mr-3">حذف جواب</span><input type="hidden" name="lessons[' + lesson + '][questions][' + questions_count + '][answer_count]" value="0"></h5><label class="form-check-label flex-column d-flex w-100 mb-3">لینک ویدئو سوال ' + questions_count + '<input class="form-control ml-3 w-100" type="text" name="lessons[' + lesson + '][questions][' + questions_count + '][answer_video]" value=""></label><label class="form-check-label flex-column d-flex w-100 mb-3">مبحث سوال ' + questions_count + '<input class="form-control ml-3 w-100" type="text" name="lessons[' + lesson + '][questions][' + questions_count + '][answer_topic]" value=""></label></div>');
    }

    function remove_question(element) {
        var questions_count = $(element).parent().find('.lesson-question').length;
        questions_count--;
        $(element).parent().find('.lesson-question')[questions_count].remove();
    }

    function add_answer(element, questions_count, lesson) {
        var answer_count = parseInt($(element).parent().find('input[type=hidden]').val());
        answer_count++;
        $(element).parent().parent().append('<div class="form-check form-check-inline answer ml-3 mb-2"><label class="form-check-label"><input class="form-check-input" type="radio" id="answer' + answer_count + '" name="lessons[' + lesson + '][questions][' + questions_count + '][answer_option]" value="' + answer_count + '">گزینه ' + answer_count + '</label></div>')
        $(element).parent().find('input[type=hidden]').val(answer_count);
    }

    function remove_answer(element) {
        var answer_count = parseInt($(element).parent().find('input[type=hidden]').val());
        answer_count--;
        $(element).parent().parent().find('.answer')[answer_count].remove();
        $(element).parent().find('input[type=hidden]').val(answer_count);
    }

    const class_selection = document.querySelector('.class_selection');
    const member_selection = document.querySelector('.member_selection');

    var classes = [
        <?php if (is_countable($all_classes) && count($all_classes) > 0) {
            $tree = show_taxonomy($all_classes, 0, $classes);
            echo $tree;
        } ?>
    ];
    var members = [
        <?php if (is_countable($all_members) && count($all_members) > 0) {
            $tree = show_taxonomy($all_members, 0, $members);
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
    var all_members = new Tagify(member_selection, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
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
        whitelist: members
    });
    var all_classess = new Tagify(class_selection, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
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
        whitelist: classes
    });
</script>