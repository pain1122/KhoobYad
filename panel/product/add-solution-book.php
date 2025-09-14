<?php
if (isset($_POST['submit']) || isset($_POST['draft'])) {
    $post_id = 'new_post';
    if (isset($_GET['id']))
        $post_id = intval($_GET['id']);
        else
        $post_id = 'new_post';
    $obj = new product($post_id);
    $obj->set_post_type('solution-book');
    $post_id = $obj->get_id();
    $post_status = 'publish';
    if (isset($_POST['draft']))
        $post_status = 'draft';
    $author = $uid;
    $title = trim($_POST['post_title']);
    $guid = urlencode(str_replace(' ', '-', $_POST['post_guid']));
    if(empty($guid))
        $guid = urlencode(str_replace(' ', '-', $title));
    $content = htmlentities($_POST['post_content'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    $regular_price = $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    if ($sale_price >= 0)
        $price = $sale_price;
    $term_date = explode('-', $_POST['term_date']);
    $term_start = $term_end = 0;
    if ($term_date[1] > 0) {
        $term_start = $term_date[0];
        $term_end = $term_date[1];
    }
    $description = htmlentities($_POST['description'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    $stock = $_POST['stock'];
    $sequential = $_POST['sequential'];
    $teacher = $_POST['teacher'];
    $students = $_POST['students'];
    $demo = $_POST['demo'];
    $exclusive = $_POST['exclusive'];
    $tags = json_decode($_POST['tag_selection'], true);
    $cats = json_decode($_POST['category_selection'], true);
    $thumbnail_src = $_POST['post_image'];
    $thumbnail_alt = $_POST['post_image_alt'];
    if (strlen($thumbnail_src) > 1 && $thumbnail_src != 'delete') {
        if (strpos($thumbnail_src, upload_folder) === false) {
            $thumbnail_src = site_url . upload_folder . $thumbnail_src;
        }
        $obj->set_thumbnail_src($thumbnail_src);
    }elseif($thumbnail_src == 'delete'){
        $obj->set_thumbnail_src('');
    }
    $thumbnail_id = $obj->get_thumbnail();
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_slug($guid);
    $obj->set_content($content);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    $metas = [
        "_price" => $price,
        "_regular_price" => $regular_price,
        "_sale_price" => $sale_price,
        "_stock" => $stock,
        "limit" => $limit,
        "sequential" => $sequential,
        "teacher" => $teacher,
        "demo" => $demo,
        "description" => $description,
        "term_start" => $term_start,
        "term_end" => $term_end,
        "exclusive" => $exclusive,
        "image_alt" => $thumbnail_alt,
    ];
    $obj->insert_meta($metas);
    $categories = $post_tags = $sessions = [];
    if(!empty($cats)){
        foreach ($cats as $cat) :
            $categories[$cat['name']] = $cat['value'];
        endforeach;
    }
    if(!empty($tags)){
        foreach ($tags as $tag) :
            $post_tags[$tag['name']] = $tag['value'];
        endforeach;
    }
    $obj->set_taxonomy('solution_book_tag',$post_tags);
    $obj->set_taxonomy('solution_book_category',$categories);


    $new_sessions = $_POST['session'];
    $session_ids = '0';
    if (is_countable($new_sessions) && count($new_sessions) > 0) {

        foreach ($new_sessions as $session) {
            $session_id = $session['session_id'];
            $number = $session['number'];
            $title = $session['title'];
            $price = $session['price'];
            $link = $session['link'];
            $date = $session['date'];
            $files = $session['file'];
            $videos = $session['video'];
            $files_meta = $videos_meta = "";
            if (is_countable($videos) && count($videos) > 0) {
                $count_video = 1;
                foreach ($videos as $video) {
                    if ($count_video == 1)
                        $videos_meta .= "$video";
                    else
                        $videos_meta .= ",$video";
                    $count_video++;
                }
            }
            if (is_countable($files) && count($files) > 0) {
                $count_file = 1;
                foreach ($files as $file) {
                    if ($count_file == 1)
                        $files_meta .= "$file";
                    else
                        $files_meta .= ",$file";
                    $count_file++;
                }
            }
            if (!empty($session_id))
                $session = new post($session_id);
            else
                $session = new post('new_post');
            $session_id = $session->get_id();
            $session_ids .= ',' . $session_id;
            $session->set_parent($post_id);
            $session->set_post_type("session");
            $session->set_content($number);
            $session->set_title($title);
            $session->set_excerpt($link);
            $metas = [
                "date" => $date,
                "_price" => $price,
                "files" => $files_meta,
                "videos" => $videos_meta
            ];
            base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = $session_id");
            $session->insert_meta($metas);
        }
    }
    $delete_session_q = "DELETE `post`,`post_meta` FROM `post` INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` WHERE `post`.`post_id` NOT IN ($session_ids) AND `post`.`post_parent` = $post_id AND `post`.`post_type` = 'session';";
    base::RunQuery($delete_session_q);
    $selected_users = [];
    $selected_users = base::FetchArray("SELECT `author` FROM `post` INNER JOIN `items_order` ON `items_order`.`order_id` = `post`.`post_id` WHERE `items_order`.`item_id` = $post_id AND `post_status` = 'completed' GROUP BY `author`;");
    if (is_countable($students) && count($students) > 0) {
        foreach ($students as $student) {
            if (!empty($student)) {
                $student = new user($student);
                $student_id = $student->get_id();
                if (!in_array($student_id, $selected_users)) {
                    $phone_number = $student->get_user_meta('phonenumber');
                    $name = $student->get_nick_name();
                    $order = new order('new_post');
                    $order_id = $order->get_id();
                    $order->set_author($student_id);
                    $order->set_title($phone_number . '-' . $name);
                    $order->set_status('completed');

                    $post_meta_content = array(
                        'user_name' => $name,
                        'user_id' => $student_id,
                        'user_phone' => $phone_number,
                        'sum' => 0,
                    );

                    $order->insert_meta($post_meta_content);
                    $order->set_sum(0);
                    $order->set_item(['id' => $post_id, 'qty' => 1, 'price' => 0, 'total' => 0, 'off' => 0, 'coupon' => '']);
                    $student_classes = $student->get_user_meta('classes');
                    if ($student_classes)
                        $student_classes = json_decode($student_classes, true);
                    else
                        $student_classes = [];
                    $student_classes[$post_id] = [];
                    $student_classes = json_encode($student_classes, JSON_UNESCAPED_UNICODE);
                    $meta = array(
                        'classes' => $student_classes,
                    );
                    $student->insert_user_meta($meta);
                }
            }
        }
        $diff_users = array_diff($selected_users, $students);
        if (!empty($diff_users)) {
            foreach ($diff_users as $student) {
                $student = $student['author'];
                if (!empty($student) && !in_array($student, $students)) {
                    $student = new user($student);
                    $student_id = $student->get_id();
                    $student_classes = $student->get_user_meta('classes');
                    if ($student_classes) {
                        $student_classes = json_decode($student_classes, true);
                        unset($student_classes[$post_id]);
                        $student_classes = json_encode($student_classes, JSON_UNESCAPED_UNICODE);
                        $student->insert_user_meta(['classes' => $student_classes]);
                        $order_q = "SELECT `post_id` FROM `items_order` INNER JOIN `post` ON `post`.`post_id` = `items_order`.`order_id` WHERE `item_id` = $post_id AND `post`.`author` = $student_id AND `post_status` = 'completed' ORDER BY `post`.`post_id` ASC;";
                        $order_ids = base::FetchArray($order_q);
                        if($order_ids){
                            foreach($order_ids as $order_id){
                                if ($order_id) {
                                    $order = new order($order_id['post_id']);
                                    $order->set_status('canceled');
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        foreach ($selected_users as $student) {
            $student = $student['author'];
            if (!empty($student)) {
                $student = new user($student);
                $student_id = $student->get_id();
                $student_classes = $student->get_user_meta('classes');
                if ($student_classes) {
                    $student_classes = json_decode($student_classes, true);
                    unset($student_classes[$post_id]);
                    $student_classes = json_encode($student_classes, JSON_UNESCAPED_UNICODE);
                    $student->insert_user_meta(['classes' => $student_classes]);
                    $order_q = "SELECT `post_id` FROM `items_order` INNER JOIN `post` ON `post`.`post_id` = `items_order`.`order_id` WHERE `item_id` = $post_id AND `post`.`author` = $student_id AND `post_status` = 'completed' ORDER BY `post`.`post_id` ASC;";
                    $order_ids = base::FetchArray($order_q);
                    if($order_ids){
                        foreach($order_ids as $order_id){
                            if ($order_id) {
                                $order = new order($order_id['post_id']);
                                $order->set_status('canceled');
                            }
                        }
                    }
                }
            }
        }
    }
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=product/add-solution-book.php&id=" . $post_id;
        base::redirect($url);
    }
}
$selected_users = [];
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $obj = new product($post_id);
    $obj->set_post_type('solution-book');
    $title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $content = $obj->get_content();
    $price = $obj->get_regular_price();
    $sale_price = $obj->get_sale_price();
    $description = $obj->get_meta('description');
    $term_start = $obj->get_meta('term_start');
    $term_end = $obj->get_meta('term_end');
        $stock = $obj->get_meta('_stock');
    $sequential = $obj->get_meta('sequential');
    $master = $obj->get_meta('teacher');
    $demo = $obj->get_meta('demo');
    $exclusive = $obj->get_meta('exclusive');
    $type = $obj->get_type();
    $cateogory_type = str_replace('-','_',$type).'_category';
    $tag_type = str_replace('-','_',$type).'_tag';
    $tags = $obj->get_taxonomy($tag_type);
    $cats = $obj->get_taxonomy($cateogory_type);
    $thumbnail_src = $obj->get_thumbnail_src();
    $thumbnail_alt = $obj->get_image_alt();
    $sessions = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_type` = 'session' AND `post_parent` = $post_id");
    if ($role == 'admin' || $role == 'school') {
        $selected_users_array = base::FetchArray("SELECT `author` FROM `post` INNER JOIN `items_order` ON `items_order`.`order_id` = `post`.`post_id` WHERE `items_order`.`item_id` = $post_id AND `post_status` = 'completed' GROUP BY `author`;");
        foreach ($selected_users_array as $user) {
            $selected_users[] = $user['author'];
        }
    }
}
$all_cats = tag::get_taxonomies(['type' => 'solution_book_category']);
$all_tags = tag::get_taxonomies(['type' => 'solution_book_tag']);
$teachers = base::FetchArray("SELECT `users`.`user_id`,`display_name` FROM `users` INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id` WHERE `key` = 'role' AND `value` = 'teacher'");
$students = base::FetchArray("SELECT `users`.`user_id`,`nicename` FROM `users` INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id` WHERE `key` = 'role' AND `value` = 'student'");

function show_taxonomy(array $tags, $parentId, &$categories)
{
    $branch = "";
    foreach ($tags as $tag) {
        $tagid = intval($tag['tag_id']);
        // if (! base::in_array_recursive($tagid,$categories)) {
        $cat = new tag($tagid);
        $tag_parent = $cat->get_parent();
        if ($tag_parent == $parentId) {
            $tagname = '';
            $tagname .= $tag['name'];
            $branch .= "{name: '$tagname', value:$tagid},";
            show_taxonomy($tags, $tagid, $categories);
        }
        // }
    }
    return $branch;
}
?>
<link rel="stylesheet" href="assets/vendor/libs/tagify/tagify.css">
<link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
<link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">عنوان</label>
                    <input type="text" name="post_title" value="<?php echo $title; ?>" onkeyup="replace_digits(this);insert_sku(this,'post_guid')" onchange="insert_sku(this,'post_guid');" class="form-control" placeholder="عنوان کلاس خود را وارد کنید">
                </div>
                <div>
                    <label class="form-label">نامک</label>
                    <input type="text" onkeyup="replace_digits(this);validate_name(this,'product')" onchange="validate_name(this,'product')" name="post_guid" id="post_guid" value="<?php echo $guid; ?>" class="form-control" placeholder="نامک خود را وارد کنید">
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="nav-align-top">
                <ul class="nav nav-pills mb-3 p-4 justify-content-between justify-content-sm-start" role="tablist">
                    <li>
                        <button type="button" class="nav-link px-2 px-md-4 active" role="tab" data-bs-toggle="tab" data-bs-target="#product-content" aria-controls="product-content" aria-selected="true">توضیحات کلاس</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link px-2 px-md-4" role="tab" data-bs-toggle="tab" data-bs-target="#product-excerpt" aria-controls="product-excerpt" aria-selected="false">توضیحات کوتاه</button>
                    </li>
                </ul>
                <div class="tab-content" style="box-shadow: none;">
                    <div class="tab-pane fade active show" id="product-content" role="tabpanel">
                        <textarea name="post_content" id="editor1" placeholder="توضیحات کلاس را وارد کنید"><?php echo $content; ?></textarea>
                    </div>
                    <div class="tab-pane fade" id="product-excerpt" role="tabpanel">
                        <textarea name="description" id="editor2" placeholder="توضیحات کوتاه را وارد کنید"><?php echo $description; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion" id="collapsibleSection">
            <div class="card">
                <div class="card-body">
                    <label class="form-label">قیمت کلاس</label>
                    <input type="text" name="price" value="<?php echo $price; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="قیمت کلاس خود را وارد کنید">
                    <label class="form-label">قیمت تخفیف خورده</label>
                    <input type="text" name="sale_price" value="<?php echo $sale_price; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="قیمت تخفیف خورده خود را وارد کنید">
                    <label class="form-label">انتخاب بازه اعتبار</label>
                    <input type="text" name="term_date" class="form-control" placeholder="YYYY/MM/DD تا YYYY/MM/DD" id="flatpickr-range">
                    <label class="form-label mt-3">ظرفیت</label>
                    <input type="number" name="stock" value="<?php echo $stock; ?>" onkeyup="replace_digits(this)" class="form-control mb-3" placeholder="ظرفیت کلاس خود را وارد کنید">
                    <?php if (is_countable($teachers) && count($teachers)) : ?>
                        <label class="form-label mt-3">مدرس</label>
                        <select name="teacher" class="select2">
                            <option>انتخاب</option>
                            <?php foreach ($teachers as $teacher) : ?>
                                <option value='<?php echo $teacher['user_id']; ?>' <?php if ($teacher['user_id'] == $master) echo 'selected'; ?>><?php echo $teacher['name']; ?></option>;
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <label class="form-label">لینک دمو</label>
                    <input type="text" name="demo" value="<?php echo $demo; ?>" class="form-control" placeholder="لینک نمایش دمو خود را وارد کنید">
                    <label class="form-label mt-3">دانش آموزان</label>
                    <select class="select2 multiple" multiple="true" name="students[]">
                        <option>انتخاب</option>
                        <?php if (is_countable($students)) : foreach ($students as $student) : 
                            $student = new user($student['user_id']); $student_id = $student->get_id(); $user_name = $student->get_user_meta('firstname') ." ". $student->get_user_meta('lastname');?>
                                <option <?php if (in_array($student_id, $selected_users)) echo "selected" ?> value='<?php echo $student_id; ?>'><?php echo $user_name; ?></option>;
                        <?php endforeach;
                        endif; ?>
                    </select>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" value="true" name="exclusive" <?php if($exclusive == 'true') echo "checked"; ?>>
                        <label class="form-check-label">نمایش به صورت انحصاری</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">عملیات</h5>
                <div class="form-check mr-3">
                    <input class="form-check-input" type="checkbox" value="true" name="sequential" <?php if($sequential == 'true') echo "checked"; ?>>
                    <label class="form-check-label">نمایش ترتیبی</label>
                </div>
            </div>

            <div class="card-body d-flex justify-content-around p-3 pt-0">
                <button type="submit" name="submit" class="btn btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> انتشار</button>
                <button type="submit" name="draft" class="btn btn-primary p-2"><i class="fa-regular fa-square-pen"></i> پیش نویس</button>
                <?php if (!empty($guid)) : ?>
                    <a href="?page=product/view.php&id=<?php echo $post_id; ?>" class="btn btn-info p-2"><i class="fa-regular fa-eye"></i> پیش نمایش</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">دسته بندی ها</h5>
            <div class="card-body">
                <label class="form-label">انتخاب دسته بندی</label>
                <input name="category_selection" class="form-control category_selection" placeholder="انتخاب دسته بندی" value="<?php if (is_countable($cats) && count($cats) > 0) {
                                                                                                                                    foreach ($cats as $cat) {
                                                                                                                                        echo $cat['name'] . ",";
                                                                                                                                    }
                                                                                                                                } ?>">
                <button type="button" class="btn btn-primary mt-3" id="add-category" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTaxonomy">ایجاد دسته</button>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">برچسب ها</h5>
            <div class="card-body">
                <label class="form-label">انتخاب برچسب</label>
                <input name="tag_selection" class="form-control tag_selection" placeholder="انتخاب برچسب" value="<?php if (is_countable($tags) && count($tags) > 0) {
                                                                                                                        foreach ($tags as $tag) {
                                                                                                                            echo $tag['name'] . ",";
                                                                                                                        }
                                                                                                                    } ?>">
                <button type="button" class="btn btn-primary mt-3" id="add-tag" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTaxonomy">ایجاد برچسب</button>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">تصویر شاخص<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?field_id=post_image_input" data-fancybox data-type="iframe" data-preload="false">انتخاب عکس</a></h5>
            <div class="card-body">
                <div class="mb-3">
                    <input hidden type="text" name="post_image" id="post_image_input" value="<?php echo $thumbnail_src; ?>">
                    <button type="button" class="btn-close text-reset ml-2 remove-post-image"></button>
                    <img src="<?php echo $thumbnail_src; ?>" id="post_image" width="85%" class="mt-3 mx-auto d-block">
                </div>
                <div>
                    <label for="formFileMultiple" class="form-label">متن جایگزین</label>
                    <input class="form-control input-air-primary" type="text" placeholder=" (alt text)نوشته جایگزین" value="<?php echo $thumbnail_alt ?>" name="post_image_alt">
                </div>
            </div>
        </div>
        <div class="card" id="sessions-wrapper">
            <h5 class="card-header d-flex align-items-center">جلسات</h5>
            <?php
            $sessions_count = 1;
            if (is_countable($sessions) && count($sessions) > 0) {
                foreach ($sessions as $session) {
                    $session = new post($session['post_id']);
                    $id = $session->get_id();
                    $number = $session->get_content();
                    $title = $session->get_title();
                    $link = $session->get_excerpt();
                    $price = $session->get_meta('_price');
                    $date = $session->get_meta('date');
                    $videos = explode(',', $session->get_meta('videos'));
                    $files = explode(',', $session->get_meta('files'));
            ?>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <button type="button" class="btn-close text-reset ml-2" onclick="remove_session(this)"></button>
                            <input type="hidden" name="session[<?php echo $sessions_count; ?>][session_id]" value="<?php echo $id; ?>">
                            <label class="form-label">شماره جلسه</label>
                            <input type="text" name="session[<?php echo $sessions_count; ?>][number]" value="<?php echo $number; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="شماره جلسه خود را وارد کنید">
                            <label class="form-label">عنوان جلسه</label>
                            <input type="text" name="session[<?php echo $sessions_count; ?>][title]" value="<?php echo $title; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="عنوان جلسه خود را وارد کنید">
                            <label class="form-label">قیمت جلسه</label>
                            <input type="text" name="session[<?php echo $sessions_count; ?>][price]" value="<?php echo $price; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="قیمت جلسه خود را وارد کنید">
                            <label class="form-label">لینک جلسه</label>
                            <input type="text" name="session[<?php echo $sessions_count; ?>][link]" value="<?php echo $link; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="لینک جلسه خود را وارد کنید">

                                <label class="form-label">مدت زمان درس (بر حسب دقیقه)</label>
                                <input type="number" name="session[<?php echo $sessions_count; ?>][date]" value="<?php echo $date; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="مدت زمان درس خود را وارد کنید">
                            <hr>
                            <?php if (is_countable($files) && count($files) > 0) {
                                for ($i = 0; $i < count($files); $i++) { ?>
                                    <label for="formFileMultiple" class="form-label">فایل</label>
                                    <a class="btn btn-sm btn-success mr-auto mb-2" href="filemanager/dialog.php?field_id=session<?php echo $sessions_count; ?>_file<?php echo $i; ?>" data-fancybox data-type="iframe" data-preload="false">انتخاب فایل</a>
                                    <input type="text" name="session[<?php echo $sessions_count; ?>][file][]" class="form-control mb-3" value="<?php echo $files[$i]; ?>" id="session<?php echo $sessions_count; ?>_file<?php echo $i; ?>">
                            <?php }
                            } ?>
                            <button onclick='add_file(this)' type="button" value="<?php echo $sessions_count; ?>" class="btn btn-success">اضافه کردن فایل</button>
                            <hr>
                            <?php if (is_countable($videos) && count($videos) > 0) {
                                for ($i = 0; $i < count($videos); $i++) { ?>
                                    <label for="formFileMultiple" class="form-label">ویدئو</label>
                                    <a class="btn btn-sm btn-success mr-auto mb-2" href="filemanager/dialog.php?field_id=session<?php echo $sessions_count; ?>_video<?php echo $i; ?>" data-fancybox data-type="iframe" data-preload="false">انتخاب فایل</a>
                                    <input type="text" name="session[<?php echo $sessions_count; ?>][video][]" class="form-control mb-3" value="<?php echo $videos[$i]; ?>" id="session<?php echo $sessions_count; ?>_video<?php echo $i; ?>">
                            <?php }
                            } ?>
                            <button onclick='add_video(this)' type="button" value="<?php echo $sessions_count; ?>" class="btn btn-success">اضافه کردن ویدئو</button>
                        </div>
                    </div>
                <?php $sessions_count++;
                }
            } else { ?>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <button type="button" class="btn-close text-reset ml-2" onclick="remove_session(this)"></button>
                        <label class="form-label">شماره جلسه</label>
                        <input type="text" name="session[<?php echo $sessions_count; ?>][number]" value="" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="شماره جلسه خود را وارد کنید">
                        <label class="form-label">عنوان جلسه</label>
                        <input type="text" name="session[<?php echo $sessions_count; ?>][title]" value="" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="عنوان جلسه خود را وارد کنید">
                        <label class="form-label">قیمت جلسه</label>
                        <input type="text" name="session[<?php echo $sessions_count; ?>][price]" value="" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="قیمت جلسه خود را وارد کنید">
                        <label class="form-label">لینک جلسه</label>
                        <input type="text" name="session[<?php echo $sessions_count; ?>][link]" value="" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="لینک جلسه خود را وارد کنید">

                            <label class="form-label">مدت زمان درس (بر حسب دقیقه)</label>
                            <input type="number" name="session[<?php echo $sessions_count; ?>][date]" value="<?php echo $date; ?>" onkeyup="replace_digits(this);" class="form-control mb-3" placeholder="مدت زمان درس خود را وارد کنید">
                        <hr>
                        <button onclick='add_file(this)' type="button" value="<?php echo $sessions_count; ?>" class="btn btn-success">اضافه کردن فایل</button>
                        <hr>
                        <button onclick='add_video(this)' type="button" value="<?php echo $sessions_count; ?>" class="btn btn-success">اضافه کردن ویدئو</button>
                    </div>
                </div>
            <?php } ?>
            <div class="d-flex p-4"><button id="add-session" type="button" value="<?php echo $sessions_count; ?>" class="btn btn-primary">اضافه کردن</button></div>
        </div>
    </div>
</form>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasAddTaxonomy" aria-labelledby="offcanvasAddTaxonomyLabel">
    <div class="offcanvas-header border-bottom">
        <h6 id="offcanvasAddTaxonomyLabel" class="offcanvas-title">افزودن <span>دسته بندی</span></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="add-new-user pt-0" id="addNewTaxonomy" data-taxonomy='class_cat' onsubmit="return false">
            <div class="mb-3">
                <label class="form-label" for="add-user-fullname">نام</label>
                <input type="text" onkeyup="replace_digits(this);insert_sku(this,'type')" class="form-control" placeholder="نام" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">نامک</label>
                <input type="text" onkeyup="validate_name(this,'class_cat')" onchange="replace_digits(this);validate_name(this,'class_cat')" id="type" class="form-control text-start" placeholder="نامک" name="slug" required>
            </div>
            <div class="mb-3" id="category-parent">
                <label class="form-label">دسته مادر</label>
                <input name="class_cat_parent" class="form-control parent_selection" placeholder="انتخاب دسته">
            </div>
            <button type="submit" id="taxonomy-submit" class="btn btn-primary me-sm-3 me-1">ثبت</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">انصراف</button>
        </form>
    </div>
</div>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/flatpickr/flatpickr-jdate.js"></script>
<script src="assets/vendor/libs/flatpickr/l10n/fa-jdate.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.custom.js"></script>
<script>
    let image_directory = "<?php echo site_url . upload_folder ?>";
    const flatpickrRange = document.querySelector('#flatpickr-range');

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

    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function() {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'انتخاب',
                dropdownParent: $this.parent()
            });
        });
    }

    $('#add-session').click(function() {
        var count = $(this).val();
        count++;
        $(this).val(count);
        $(this).parent().before("<div class='card-body'><div class='d-flex flex-wrap align-items-center'><button type='button' class='btn-close text-reset ml-2' onclick='remove_session(this)'></button><label class='form-label'>شماره جلسه</label><input type='text' name='session[" + count + "][number]' value='' onkeyup='replace_digits(this);' class='form-control mb-3' placeholder='شماره جلسه خود را وارد کنید'><label class='form-label'>عنوان جلسه</label><input type='text' name='session[" + count + "][title]' value='' onkeyup='replace_digits(this);' class='form-control mb-3' placeholder='عنوان جلسه خود را وارد کنید'><label class='form-label'>قیمت جلسه</label><input type='text' name='session[" + count + "][price]' value='' onkeyup='replace_digits(this);' class='form-control mb-3' placeholder='قیمت جلسه خود را وارد کنید'><label class='form-label'>لینک جلسه</label><input type='text' name='session[" + count + "][link]' value='' onkeyup='replace_digits(this);' class='form-control mb-3' placeholder='لینک جلسه خود را وارد کنید'><label class='form-label'>مدت زمان درس (بر حسب دقیقه)</label><input type='number' name='session[" + count + "][date]' onkeyup='replace_digits(this);' class='form-control mb-3' placeholder='مدت زمان درس خود را وارد کنید'><hr><button onclick='add_file(this)' type='button' value='" + count + "' class='btn btn-success'>اضافه کردن فایل</button><hr><button onclick='add_video(this)' type='button' value='" + count + "' class='btn btn-success'>اضافه کردن ویدئو</button></div></div>");
    })

    function add_video(element) {
        var count = $(element).val();
        $(element).val(count);
        var vcount = $(element).parent().find("input[name*='video]'").length;
        $(element).before("<label for='formFileMultiple' class='form-label'>ویدئو</label><a class='btn btn-sm btn-success mr-auto mb-2' href='filemanager/dialog.php?field_id=session" + count + "_video" + vcount + "' data-fancybox data-type='iframe' data-preload='false'>انتخاب فایل</a><input type='text' class='form-control mb-3' name='session[" + count + "][video][]' id='session" + count + "_video" + vcount + "'>");
    }

    function add_file(element) {
        var count = $(element).val();
        var vcount = $(element).parent().find("input[name*='file]'").length;
        $(element).before("<label for='formFileMultiple' class='form-label'>فایل</label><a class='btn btn-sm btn-success mr-auto mb-2' href='filemanager/dialog.php?field_id=session" + count + "_file" + vcount + "' data-fancybox data-type='iframe' data-preload='false'>انتخاب فایل</a><input type='text' name='session[" + count + "][file][]' class='form-control mb-3' id='session" + count + "_file" + vcount + "'>");
        $(element).val(count);
    }

    function remove_session(element) {
        $(element).parent().parent().remove();
    }

    $('#add-tag').click(function() {
        $('#addNewTaxonomy').attr('data-taxonomy', 'class_tag');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('برچسب');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'class_tag')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'class_tag')");
        $('#offcanvasAddTaxonomy #category-parent').hide();
    });
    $('#add-category').click(function() {
        $('#addNewTaxonomy').attr('data-taxonomy', 'class_cat');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('دسته بندی');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'class_cat')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'class_cat')");
        $('#offcanvasAddTaxonomy #category-parent').show();
    });
    if (typeof flatpickrRange != undefined) {
        flatpickrRange.flatpickr({
            mode: 'range',
            locale: 'fa',
            altInput: true,
            altFormat: 'Y/m/d',
            dateFormat: "U",
            disableMobile: true,
            <?php if ($term_start > 0 && $term_end > 0) : ?>
                defaultDate: ["<?php echo $term_start; ?>", "<?php echo $term_end; ?>"]
            <?php endif; ?>
        });
    }

    const category_selection = document.querySelector('.category_selection');
    const category_parent = document.querySelector('.parent_selection');
    const tag_selection = document.querySelector('.tag_selection');

    var tags = [
        <?php if (is_countable($all_tags) && count($all_tags) > 0) {
            $tree = show_taxonomy($all_tags, 0, $tags);
            echo $tree;
        } ?>
    ];
    var cats = [
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
    var all_tags = new Tagify(tag_selection, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
        maxTags: 10,
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
        whitelist: tags
    });
    var all_categories = new Tagify(category_selection, {
        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
        enforceWhitelist: true,
        maxTags: 10,
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
    var parent_category = new Tagify(category_parent, {
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

    $('#taxonomy-submit').click(function() {
        var type = $('#addNewTaxonomy').attr('data-taxonomy');
        var name = $('#addNewTaxonomy input[name="name"]').val();
        var slug = $('#addNewTaxonomy input[name="slug"]').val();
        var parent = $('#addNewTaxonomy input[name="' + type + '_parent"]').val();
        if (parent && parent.length > 0) {
            parent = JSON.parse(parent);
            parent = parent[0].value;
        } else {
            parent = 0;
        }

        getJSON('API/v1/InsertTaxonomy.php?name=' + name + '&slug=' + slug + '&parent=' + parent + '&type=' + type, function(err, data) {
            if (data != null && data.length > 0) {
                $('#addNewTaxonomy input[name="slug"]').parent().find(".validate-failed").remove();
                if (data == 'exsists') {
                    $('#addNewTaxonomy input[name="slug"]').parent().append('<p class="alert alert-danger validate-failed">این نام قبلا استفاده شده است</p>');
                    return false;
                } else {
                    var name = data[0]['name'];
                    var id = data[0]['tag_id'];
                    if (data[0]['type'] == 'class_cat') {
                        cats.push({
                            "name": name,
                            "value": id
                        });
                        all_categories.destroy();
                        parent_category.destroy();
                        all_categories = new Tagify(category_selection, {
                            tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
                            enforceWhitelist: true,
                            maxTags: 10,
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
                        parent_category = new Tagify(category_parent, {
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
                        toastr.success('دسته بندی با موفقیت اضافه شد');
                    } else {
                        tags.push({
                            "name": name,
                            "value": id
                        });
                        all_tags.destroy();
                        all_tags = new Tagify(tag_selection, {
                            tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
                            enforceWhitelist: true,
                            maxTags: 10,
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
                            whitelist: tags
                        });
                        toastr.success(' با موفقیت اضافه شد');
                    }
                    $('#addNewTaxonomy').trigger("reset");
                }
            }
        });
    });
</script>