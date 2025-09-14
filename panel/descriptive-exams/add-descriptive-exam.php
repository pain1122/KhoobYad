<?php
if (isset($_POST['submit']) || isset($_POST['draft'])) {
    if (isset($_GET['id']))
        $post_id = intval($_GET['id']);
    else
        $post_id = 'new_post';
    $obj = new product($post_id);
    $obj->set_post_type('descriptive-exam');
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
    $term_date = explode('-', $_POST['term_date']);
    $term_start = $term_end = 0;
    if ($_POST['term_start'] > 0 && $_POST['term_end']) {
        $term_start = $_POST['term_start'];
        $term_end = $_POST['term_end'];
    }
    $stock = $_POST['stock'];
    $duration = $_POST['duration'];
    $negative_score = $_POST['negative-score'];
    $on_time = $_POST['on-time'];
    $buy_for_others = $_POST['buy-for-others'];
    $descriptive_exam_scale = $_POST['descriptive-exam-scale'];
    $descriptive_exam_rank = $_POST['descriptive-exam-rank'];
    $descriptive_exam_questions = $_POST['descriptive_exam_questions'];
    $descriptive_exam_answers = $_POST['descriptive_exam_answers'];
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
    if (is_countable($descriptive_exam_rank) && count($descriptive_exam_rank) > 0) {
        for ($i = 0; $i < count($descriptive_exam_rank); $i++) {
            $descriptive_exam_prediction[$descriptive_exam_scale[$i]] = $descriptive_exam_rank[$i];
        }
        $descriptive_exam_prediction = json_encode($descriptive_exam_prediction, JSON_UNESCAPED_UNICODE);
    } else {
        $descriptive_exam_prediction = '';
    }
    $metas = [
        "_price" => $price,
        "_regular_price" => $regular_price,
        "_sale_price" => $sale_price,
        "_stock" => $stock,
        "duration" => $duration,
        "descriptive_exam_prediction" => $descriptive_exam_prediction,
        "buy_for_others" => $buy_for_others,
        "on_time" => $on_time,
        "negative_score" => $negative_score,
        "members" => $members,
        "classes" => $classes,
        "descriptive_exam_answers" => $descriptive_exam_answers,
        "descriptive_exam_questions" => $descriptive_exam_questions,
        "lessons" => $lessons,
        "term_start" => $term_start,
        "term_end" => $term_end
    ];
    $obj->insert_meta($metas);
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=descriptive-exams/add-descriptive-exam.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $obj = new product($post_id);
    $obj->set_post_type('descriptive-exam');
    $title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $price = $obj->get_regular_price();
    $sale_price = $obj->get_sale_price();
    $term_start = $obj->get_meta('term_start');
    $term_end = $obj->get_meta('term_end');
    $stock = $obj->get_meta('_stock');
    $duration = $obj->get_meta('duration');
    $buy_for_others = $obj->get_meta('buy_for_others');
    $on_time = $obj->get_meta('on_time');
    $negative_score = $obj->get_meta('negative_score');
    $descriptive_exam_questions = $obj->get_meta('descriptive_exam_questions');
    $descriptive_exam_answers = $obj->get_meta('descriptive_exam_answers');
    $descriptive_exam_prediction = json_decode($obj->get_meta('descriptive_exam_prediction'), true);
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
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
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
                            placeholder="عنوان آزمون خود را وارد کنید" required>
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">ظرفیت آزمون</label>
                        <input type="text" name="stock" value="<?php echo $stock; ?>" class="form-control"
                            placeholder="خالی مساوی است با نامحدود">
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">مهلت آزمون</label>
                        <input type="number" name="duration" value="<?php echo $duration; ?>" min="0"
                            class="form-control" placeholder="خالی مساوی است با نامحدود">
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">شروع آزمون</label>
                        <input type="text" name="term_start" class="form-control"
                            placeholder="YYYY/MM/DD H:i" id="flatpickr-start" required>
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">پایان آزمون</label>
                        <input type="text" name="term_end" class="form-control"
                            placeholder="YYYY/MM/DD H:i" id="flatpickr-end" required>
                    </div>
                    <div class="mb-3 col-12 col-lg-6">
                        <label class="form-label">قیمت آزمون</label>
                        <input type="text" name="price" value="<?php echo $price; ?>" onkeyup="replace_digits(this);"
                            class="form-control mb-3" placeholder="قیمت آزمون خود را وارد کنید">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">قیمت تخفیف خورده</label>
                        <input type="text" name="sale_price" value="<?php echo $sale_price; ?>"
                            onkeyup="replace_digits(this);" class="form-control mb-3"
                            placeholder="قیمت تخفیف خورده خود را وارد کنید">
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4" id="lessons-wrapper">
            <div class="nav-align-top">
                <div class="tab-content" style="box-shadow: none;" id="lesson-content">
                    <?php
                    if (is_countable($lessons) && count($lessons) > 0) {
                        for ($i = 1; $i < $lessons_count + 1; $i++) {
                            $questions = $lessons[$i]['questions'];
                            ?>
                            <div class='tab-pane fade active show' id='lesson-content0' role='tabpanel'>
                                <h5 class="mb-0">افزودن سوالات</h5>
                                <?php $j = 1;
                                foreach ($questions as $question) {
                                    $answer_video = $question['answer_video'];
                                    $answer_topic = $question['answer_topic'];?>
                                    <div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-4">
                                        <h5 class="d-flex align-items-center w-100 mb-3">سوال شماره <?php echo $j; ?></h5>
                                        <label class="form-check-label flex-column d-flex w-100 mb-3">لینک ویدئو سوال
                                            <?php echo $j; ?><input class="form-control ml-3 w-100" type="text"
                                                name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_video]"
                                                value="<?php echo $answer_video; ?>"></label>
                                        <label class="form-check-label flex-column d-flex w-100 mb-3">مبحث سوال
                                            <?php echo $j; ?><input class="form-control ml-3 w-100" type="text"
                                                name="lessons[<?php echo $i; ?>][questions][<?php echo $j; ?>][answer_topic]"
                                                value="<?php echo $answer_topic; ?>"></label>
                                    </div>
                                    <?php $j++;
                                } ?>
                                <span onclick='add_question(this,<?php echo $i; ?>)' class='btn btn-success'>اضافه کردن
                                    سوال</span>
                                <span onclick='remove_question(this)' class='btn btn-danger mr-3'>حذف سوال</span>
                            </div>
                        <?php }
                    } else { ?>
                        <div class='tab-pane fade active show' id='lesson-content0' role='tabpanel'>
                            <h5 class="mb-0">افزودن سوالات</h5>
                            <span onclick='add_question(this,<?php echo $i; ?>)' class='btn btn-success'>اضافه کردن
                                سوال</span>
                            <span onclick='remove_question(this)' class='btn btn-danger mr-3'>حذف سوال</span>
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
                    <a href="/panel/index.php?page=descriptive-exams/descriptive-view.php&exam=<?php echo $post_id; ?>"
                        class="btn btn-info p-2"><i class="fa-regular fa-eye"></i> پیش نمایش</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">مقررات</h5>
            <div class="card-body">
                <div class="form-check mb-2">
                    <label class="form-check-label"> آزمون رأس ساعت مقرر برگذار خواهد شد ؟</label><input
                        class="form-check-input" type="checkbox" <?php if ($on_time == 'true')
                            echo "checked"; ?>
                        value="true" name="on-time">
                </div>
                <div class="form-check mb-0">
                    <label class="form-check-label"> آزمون قابل خرید برای سایر کاربران میباشد ؟</label><input
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
            <h5 class="card-header d-flex align-items-center">انتخاب فایل سوال های آزمون<a
                    class='btn btn-sm btn-success mr-auto mb-2'
                    href='filemanager/dialog.php?type=1s&field_id=descriptive_exam_questions' data-fancybox
                    data-type='iframe' data-preload='false'>انتخاب فایل</a></h5>
            <div class="card-body">
                <input type='text' name='descriptive_exam_questions' class='form-control mb-3'
                    id='descriptive_exam_questions' value="<?php echo $descriptive_exam_questions; ?>">
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">انتخاب فایل جواب های آزمون<a
                    class='btn btn-sm btn-success mr-auto mb-2'
                    href='filemanager/dialog.php?type=1s&field_id=descriptive_exam_answers' data-fancybox
                    data-type='iframe' data-preload='false'>انتخاب فایل</a></h5>
            <div class="card-body">
                <input type='text' name='descriptive_exam_answers' class='form-control mb-3'
                    id='descriptive_exam_answers' value="<?php echo $descriptive_exam_answers; ?>">
            </div>
        </div>
    </div>
</form>

<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";
    const flatpickrStart = document.querySelector('#flatpickr-start');
    const flatpickrEnd = document.querySelector('#flatpickr-end');

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

    function add_question(element, lesson) {
        var questions_count = $(element).parent().find('.lesson-question').length;
        questions_count++;
        $(element).before('<div class="lesson-question d-flex flex-wrap align-items-center w-100 mb-4"><h5 class="d-flex align-items-center w-100 mb-3">سوال شماره ' + questions_count + '</h5><label class="form-check-label flex-column d-flex w-100 mb-3">لینک ویدئو سوال ' + questions_count + '<input class="form-control ml-3 w-100" type="text" name="lessons[1][questions][' + questions_count + '][answer_video]" value=""></label><label class="form-check-label flex-column d-flex w-100 mb-3">مبحث سوال ' + questions_count + '<input class="form-control ml-3 w-100" type="text" name="lessons[1][questions][' + questions_count + '][answer_topic]" value=""></label></div>');
    }

    function remove_question(element) {
        var questions_count = $(element).parent().find('.lesson-question').length;
        questions_count--;
        $(element).parent().find('.lesson-question')[questions_count].remove();
    }



    if (typeof flatpickrStart != undefined) {
        flatpickrStart.flatpickr({
            locale: 'fa',
            altInput: true,
            enableTime: true,
            // minuteIncrement: 1,
            altFormat: 'Y/m/d H:i',
            dateFormat: "U",
            disableMobile: true,
            <?php if ($term_start > 0 && $term_end > 0): ?>
                                                        defaultDate: ["<?php echo $term_start; ?>"]
            <?php endif; ?>
        });
    }
    if (typeof flatpickrEnd != undefined) {
        flatpickrEnd.flatpickr({
            locale: 'fa',
            altInput: true,
            enableTime: true,
            // minuteIncrement: 1,
            altFormat: 'Y/m/d H:i',
            dateFormat: "U",
            disableMobile: true,
            <?php if ($term_start > 0 && $term_end > 0): ?>
                                                        defaultDate: ["<?php echo $term_end; ?>"]
            <?php endif; ?>
        });
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