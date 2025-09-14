<?php
if (isset($_POST['submit']) || isset($_POST['draft'])) {
    $post_id = 'new_post';
    if (isset($_GET['id']))
        $post_id = intval($_GET['id']);
    $obj = new blog($post_id);
    $post_id = $obj->get_id();
    $post_status = 'publish';
    if (isset($_POST['draft']))
        $post_status = 'draft';
    $author = $uid;
    $title = trim($_POST['post_title']);
    $guid = urlencode(str_replace(' ', '-', $_POST['post_guid']));
    if (empty($guid))
        $guid = urlencode(str_replace(' ', '-', $title));
    $tags = json_decode($_POST['tag_selection'], true);
    $cats = json_decode($_POST['category_selection'], true);
    $obj->set_post_type('defined_plan');
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_slug($guid);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    if (isset($_POST['var'])) {
        $variables = $_POST['var'];
        $var_ids = '0';
        foreach ($variables as $variable) {
            //Inserting Variables
            $var_id = $variable['var_id'];
            if (intval($var_id) > 0) {
                $variable_post = new blog($var_id);
            } else {
                $variable_post = new blog("new_post");
                $var_id = $variable_post->get_id();
            }
            $variable_post->set_parent($post_id);
            $variable_post->set_post_type('defined_plan');
            $var_ids .= ',' . $var_id;
            $var_title = $variable['title'];
            $var_desc = $variable['desc'];
            $var_time = $variable['time'];
            $var_video = array_filter($variable['video']);
            $var_note = array_filter($variable['note']);
            $note_title = array_filter($variable['note_title']);
            $video_title = array_filter($variable['video_title']);
            if (!empty($var_video))
                $var_video = json_encode($var_video, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            else
                $var_video = "[]";
            if (!empty($var_note))
                $var_note = json_encode($var_note, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            else
                $var_note = "[]";
            if (!empty($note_title))
                $note_title = json_encode($note_title, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            else
                $note_title = "[]";
            if (!empty($video_title))
                $video_title = json_encode($video_title, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            else
                $video_title = "[]";
            $variable_post->set_title($var_title);
            $array_attributes['desc'] = $var_desc;
            $array_attributes['time'] = $var_time;
            $array_attributes['video'] = $var_video;
            $array_attributes['note'] = $var_note;
            $array_attributes['note_title'] = $note_title;
            $array_attributes['video_title'] = $video_title;
            base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = $var_id");
            $variable_post->insert_meta($array_attributes);
        }
        $delete_var_q = "DELETE `post`,`post_meta` FROM `post` INNER JOIN `post_meta` ON `post_meta`.`post_id` = `post`.`post_id` WHERE `post`.`post_id` NOT IN ($var_ids) AND `post`.`post_parent` = $post_id AND `post`.`post_type` = 'defined_plan';";
        base::RunQuery($delete_var_q);
    }
    // $metas = [
    //     "catalogue" => $catalogue,
    //     "image_alt" => $thumbnail_alt,
    //     "galleries" => $galleries,
    //     "exviews" => $exviews,
    // ];
    // $obj->insert_meta($metas);
    $categories = $post_tags = [];
    if (!empty($cats)) {
        foreach ($cats as $cat):
            $categories[$cat['name']] = $cat['value'];
        endforeach;
    }
    if (!empty($tags)) {
        foreach ($tags as $tag):
            $post_tags[$tag['name']] = $tag['value'];
        endforeach;
    }
    $obj->set_taxonomy('study_course', $categories);
    $obj->set_taxonomy('study_grade', $post_tags);

    // print_r($attrbuites);
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=defined-plans/add-plan.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $obj = new blog($post_id);
    $obj->set_post_type('defined_plan');
    $product_title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $cats = $obj->get_taxonomy('study_course');
    $tags = $obj->get_taxonomy('study_grade');
    $variables = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_parent` = $post_id AND `post_type` = 'defined_plan'");
}
$all_cats = tag::get_taxonomies(['type' => "study_course"]);
$all_tags = tag::get_taxonomies(['type' => "study_grade"]);


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
            $tagname .= $tag['name']; ?>
            {name: '<?php echo $tagname; ?>', value:<?php echo $tagid; ?>},
            <?php
            show_taxonomy($tags, $tagid, $categories);
        }
        // }
    }
    return $branch;
}
?>
<link rel="stylesheet" href="assets/vendor/libs/tagify/tagify.css">
<link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">عنوان برنامه</label>
                    <input type="text" name="post_title" value="<?php echo $product_title; ?>"
                        onkeyup="replace_digits(this);insert_sku(this,'post_guid')"
                        onchange="insert_sku(this,'post_guid');" class="form-control"
                        placeholder="عنوان برنامه خود را وارد کنید">
                </div>
                <div>
                    <label class="form-label">نامک</label>
                    <input type="text" onkeyup="replace_digits(this);validate_name(this,'product')"
                        onchange="validate_name(this,'product')" name="post_guid" id="post_guid"
                        value="<?php echo $guid; ?>" class="form-control" placeholder="نامک خود را وارد کنید">
                </div>
            </div>
        </div>
        <div class="accordion mb-4" id="collapsibleSection">
            <div class="card">
                <div class="card-header">
                    <h4>مباحث</h4>
                </div>
                <div class="card-body">
                    <div id="variables_container" class="list-group mb-3">
                        <?php
                        $variable_count = 0;
                        if ($post_id > 0 && count($variables) > 0) {
                            foreach ($variables as $variable) {
                                $var_id = $variable['post_id'];
                                $var = new blog($var_id);
                                $obj->set_post_type('defined_plan');
                                $var_title = $var->get_title();
                                $var_desc = $var->get_meta('desc');
                                $var_time = $var->get_meta('time');
                                $var_video = $var->get_meta('video');
                                $var_note = $var->get_meta('note');
                                $note_title = $var->get_meta('note_title');
                                $video_title = $var->get_meta('video_title');
                                if (!empty($var_video))
                                    $var_video = json_decode($var_video, true);
                                if (!empty($var_note))
                                    $var_note = json_decode($var_note, true);
                                if (!empty($note_title))
                                    $note_title = json_decode($note_title, true);
                                if (!empty($video_title))
                                    $video_title = json_decode($video_title, true);
                                ?>
                                <ul class="list-group-item p-3 mb-0" id="main-variable<?php echo $variable_count; ?>">
                                    <?php if ($variable_count > 0)
                                        echo '<button type="button" onclick="$(this).parent().remove();" class="btn-close"></button>'; ?>
                                    <li class="row">
                                        <div class="col-12">
                                            <label class="form-label">عنوان مبحث : </label>
                                            <input type="text" name="var[<?php echo $variable_count; ?>][title]"
                                                value="<?php echo $var_title; ?>" onkeyup="replace_digits(this)"
                                                class="form-control" placeholder="عنوان مبحث را وارد کنید">
                                            <input type="hidden" name="var[<?php echo $variable_count; ?>][var_id]"
                                                value="<?php echo $var_id; ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label mt-3">توضیحات : </label>
                                            <textarea name="var[<?php echo $variable_count; ?>][desc]" class="form-control"
                                                placeholder="توضیحات مبحث را وارد کنید"><?php echo $var_desc; ?></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">مدت زمان مبحث (به دقیقه) : </label>
                                            <input type="number" name="var[<?php echo $variable_count; ?>][time]"
                                                value="<?php echo $var_time; ?>" onkeyup="replace_digits(this)"
                                                class="form-control" placeholder="مدت زمان مبحث را وارد کنید">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">عنوان ویدئو</label>
                                            <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                                value="<?php echo $video_title[0]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">
                                            <label class="form-label">ویدئو مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                                value="<?php echo $var_video[0]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان ویدئو</label>
                                            <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                                value="<?php echo $video_title[1]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">
                                            <label class="form-label">ویدئو مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                                value="<?php echo $var_video[1]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان ویدئو</label>
                                            <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                                value="<?php echo $video_title[2]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">
                                            <label class="form-label">ویدئو مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                                value="<?php echo $var_video[2]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان ویدئو</label>
                                            <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                                value="<?php echo $video_title[3]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">
                                            <label class="form-label">ویدئو مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                                value="<?php echo $var_video[3]; ?>"
                                                id="var_video_input<?php echo $variable_count; ?>">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">عنوان جزوه</label>
                                            <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                                value="<?php echo $note_title[0]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">
                                            <label class="form-label">جزوه مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                                value="<?php echo $var_note[0]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان جزوه</label>
                                            <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                                value="<?php echo $note_title[1]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">
                                            <label class="form-label">جزوه مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                                value="<?php echo $var_note[1]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان جزوه</label>
                                            <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                                value="<?php echo $note_title[2]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">
                                            <label class="form-label">جزوه مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                                value="<?php echo $var_note[2]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">

                                            <label class="form-label mt-5">عنوان جزوه</label>
                                            <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                                value="<?php echo $note_title[3]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">
                                            <label class="form-label">جزوه مبحث</label>
                                            <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                                type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                                value="<?php echo $var_note[3]; ?>"
                                                id="var_note_input<?php echo $variable_count; ?>">
                                        </div>
                                    </li>
                                </ul>
                                <?php $variable_count++;
                            }
                        } else { ?>
                            <ul class="list-group-item p-3 mb-0" id="main-variable0">
                                <li class="row">
                                    <div class="col-12">
                                        <label class="form-label">عنوان مبحث : </label>
                                        <input type="text" name="var[0][title]" value="" onkeyup="replace_digits(this)"
                                            class="form-control" placeholder="عنوان مبحث را وارد کنید">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mt-3">توضیحات : </label>
                                        <textarea name="var[0][desc]" class="form-control"
                                            placeholder="توضیحات مبحث را وارد کنید"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">مدت زمان مبحث (به دقیقه) : </label>
                                        <input type="number" name="var[0][time]" value="" onkeyup="replace_digits(this)"
                                            class="form-control" placeholder="مدت زمان مبحث را وارد کنید">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">عنوان ویدئو</label>
                                        <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                            value="<?php echo $video_title[0]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">
                                        <label class="form-label">ویدئو مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                            value="<?php echo $var_video[0]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان ویدئو</label>
                                        <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                            value="<?php echo $video_title[1]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">
                                        <label class="form-label">ویدئو مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                            value="<?php echo $var_video[1]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان ویدئو</label>
                                        <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                            value="<?php echo $video_title[2]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">
                                        <label class="form-label">ویدئو مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                            value="<?php echo $var_video[2]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان ویدئو</label>
                                        <input class="form-control mt-2" placeholder="عنوان ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video_title][]"
                                            value="<?php echo $video_title[3]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">
                                        <label class="form-label">ویدئو مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک ویدئو خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][video][]"
                                            value="<?php echo $var_video[3]; ?>"
                                            id="var_video_input<?php echo $variable_count; ?>">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">عنوان جزوه</label>
                                        <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                            value="<?php echo $note_title[0]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">
                                        <label class="form-label">جزوه مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                            value="<?php echo $var_note[0]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان جزوه</label>
                                        <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                            value="<?php echo $note_title[1]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">
                                        <label class="form-label">جزوه مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                            value="<?php echo $var_note[1]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان جزوه</label>
                                        <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                            value="<?php echo $note_title[2]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">
                                        <label class="form-label">جزوه مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                            value="<?php echo $var_note[2]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">

                                        <label class="form-label mt-5">عنوان جزوه</label>
                                        <input class="form-control mt-2" placeholder="عنوان جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note_title][]"
                                            value="<?php echo $note_title[3]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">
                                        <label class="form-label">جزوه مبحث</label>
                                        <input class="form-control mt-2" placeholder="لینک جزوه خود را وارد کنید"
                                            type="text" name="var[<?php echo $variable_count; ?>][note][]"
                                            value="<?php echo $var_note[3]; ?>"
                                            id="var_note_input<?php echo $variable_count; ?>">
                                    </div>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                    <button id="add-variable" type="button" class="btn btn-primary"><i
                            class="fa fa-plus-square"></i></button>
                    <div class="d-block mt-3">
                        <button type="button" class="btn btn-primary" id="add-new-variable" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasAddTaxonomy">تعریف متغییر</button>
                    </div>
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
                    <a href="/<?php echo product_url . '/' . $guid ?>" class="btn btn-info p-2"><i
                            class="fa-regular fa-eye"></i> پیش نمایش</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">رشته تحصیلی</h5>
            <div class="card-body">
                <label class="form-label">انتخاب رشته تحصیلی</label>
                <input name="category_selection" class="form-control category_selection"
                    placeholder="انتخاب رشته تحصیلی" value="<?php if (is_countable($cats) && count($cats) > 0) {
                        foreach ($cats as $cat) {
                            echo $cat['name'] . ",";
                        }
                    } ?>">
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">پایه تحصیلی</h5>
            <div class="card-body">
                <label class="form-label">انتخاب پایه تحصیلی</label>
                <input name="tag_selection" class="form-control tag_selection" placeholder="انتخاب پایه تحصیلی" value="<?php if (is_countable($tags) && count($tags) > 0) {
                    foreach ($tags as $tag) {
                        echo $tag['name'] . ",";
                    }
                } ?>">
            </div>
        </div>
        <!-- <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">منوآل<a class="btn btn-sm btn-success mr-auto"
                    href="filemanager/dialog.php?type=1s&field_id=post_manual_input" data-fancybox data-type="iframe"
                    data-preload="false">انتخاب فایل</a></h5>
            <div class="card-body">
                <div class="mb-3">
                    <input hidden type="text" name="post_manual" id="post_manual_input" value="<?php echo $manual; ?>">
                    <button type="button" class="btn-close text-reset ml-2 remove-post-manual"></button>
                    <img src="<?php echo $manual; ?>" id="post_manual" width="85%" class="mt-3 mx-auto d-block">
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">کاتالوگ<a class="btn btn-sm btn-success mr-auto"
                    href="filemanager/dialog.php?type=1s&field_id=post_catalogue_input" data-fancybox data-type="iframe"
                    data-preload="false">انتخاب فایل</a></h5>
            <div class="card-body">
                <div class="mb-3">
                    <input hidden type="text" name="post_catalogue" id="post_catalogue_input"
                        value="<?php echo $catalogue; ?>">
                    <button type="button" class="btn-close text-reset ml-2 remove-post-catalogue"></button>
                    <img src="<?php echo $catalogue; ?>" id="post_catalogue" width="85%" class="mt-3 mx-auto d-block">
                </div>
            </div>
        </div> -->
        <!-- <div class="card mb-4">
            <h5 class="card-header d-flex align-items-center">تصویر شاخص<a class="btn btn-sm btn-success mr-auto"
                    href="filemanager/dialog.php?type=1s&field_id=post_image_input" data-fancybox data-type="iframe"
                    data-preload="false">انتخاب عکس</a></h5>
            <div class="card-body">
                <div class="mb-3">
                    <input hidden type="text" name="post_image" id="post_image_input"
                        value="<?php echo $thumbnail_src; ?>">
                    <button type="button" class="btn-close text-reset ml-2 remove-post-image"></button>
                    <img src="<?php echo $thumbnail_src; ?>" id="post_image" width="85%" class="mt-3 mx-auto d-block">
                </div>
                <div>
                    <label for="formFileMultiple" class="form-label">متن جایگزین</label>
                    <input class="form-control input-air-primary" type="text" placeholder=" (alt text)نوشته جایگزین"
                        value="<?php echo $thumbnail_alt ?>" name="post_image_alt">
                </div>
            </div>
        </div> -->
    </div>
</form>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasAddTaxonomy"
    aria-labelledby="offcanvasAddTaxonomyLabel">
    <div class="offcanvas-header border-bottom">
        <h6 id="offcanvasAddTaxonomyLabel" class="offcanvas-title">افزودن <span>دسته بندی</span></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="add-new-user pt-0" id="addNewTaxonomy" data-taxonomy='product_cat' onsubmit="return false">
            <div class="mb-3">
                <label class="form-label" for="add-user-fullname">نام</label>
                <input type="text" onkeyup="replace_digits(this);insert_sku(this,'type')" class="form-control"
                    placeholder="نام" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">نامک</label>
                <input type="text" onkeyup="validate_name(this,'<?php echo product_cat; ?>')"
                    onchange="replace_digits(this);validate_name(this,'<?php echo product_cat; ?>')" id="type"
                    class="form-control text-start" placeholder="نامک" name="slug" required>
            </div>
            <div class="mb-3" id="category-parent">
                <label class="form-label">دسته مادر</label>
                <input name="product_cat_parent" class="form-control parent_selection" placeholder="انتخاب دسته">
            </div>
            <div class="mb-3" id="attribute-parent" style="display: none;">
                <label class="form-label">دسته مادر</label>
                <input name="product_attribute_parent" class="form-control attribute_selection"
                    placeholder="انتخاب دسته">
            </div>
            <div class="mb-3" id="variable-parent" style="display: none;">
                <label class="form-label">دسته مادر</label>
                <input name="product_variable_parent" class="form-control variable_selection" placeholder="انتخاب دسته">
            </div>
            <button type="submit" id="taxonomy-submit" class="btn btn-primary me-sm-3 me-1">ثبت</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">انصراف</button>
        </form>
    </div>
</div>
<script src="assets/vendor/libs/jdate/jdate.js"></script>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.custom.js"></script>
<script>
    $(document).ready(function () {
        let seo_desc = document.getElementsByName('seo_desc')[0];
        countChar(seo_desc, 165, 'seo_desc');
        let seo_title = document.getElementsByName('seo_title')[0];
        countChar(seo_title, 165, 'seo_title');
    });
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
    $('.remove-post-manual').click(function () {
        $('#post_manual_input').val('delete');
        $('#post_manual').attr('src', '');
    });
    $('.remove-post-catalogue').click(function () {
        $('#post_catalogue_input').val('delete');
        $('#post_catalogue').attr('src', '');
    });
    $('.remove-post-image').click(function () {
        $('#post_image_input').val('delete');
        $('#post_image').attr('src', '');
    });

    $('#add-gallery').click(function () {
        var count = $('#gallery-wrapper .card-body').length;
        $(this).parent().before('<div class="card-body"><div class="d-flex flex-wrap align-items-center"><button type="button" class="btn-close text-reset ml-2 remove-gallery"  onclick="$(this).parent().parent().remove();"></button><label for="formFileMultiple" class="form-label">متن جایگزین</label><a class="btn btn-sm btn-success mr-auto mb-2" href="filemanager/dialog.php?type=1s&field_id=post_gallery_input' + count + '" data-fancybox data-type="iframe" data-preload="false">انتخاب تصاویر</a><input class="form-control input-air-primary" type="text" placeholder=" (alt text)نوشته جایگزین" value="" name="post_gallery_alt[]"></div><div class="mb-3"><input hidden type="text" name="post_gallery[]" id="post_gallery_input' + count + '"><img src="" height="150px" class="mt-3 mx-auto d-block mw-100"></div></div>');
        count++;
    });

    $('.remove-gallery').click(function () {
        $(this).parent().parent().remove();
    })

    $('.remove-var-image').click(function () {
        $(this).parent().find('input').val('delete');
        $(this).parent().find('img').attr('src', '');
    });

    $('#add-exview').click(function () {
        var count = $('#exview-wrapper .card-body').length;
        $(this).parent().before('<div class="card-body"><div class="d-flex flex-wrap align-items-center"><button type="button" class="btn-close text-reset ml-2 remove-exview"  onclick="$(this).parent().parent().remove();"></button><label for="formFileMultiple" class="form-label">متن جایگزین</label><a class="btn btn-sm btn-success mr-auto mb-2" href="filemanager/dialog.php?type=1s&field_id=post_exview_input' + count + '" data-fancybox data-type="iframe" data-preload="false">انتخاب تصاویر</a><input class="form-control input-air-primary" type="text" placeholder=" (alt text)نوشته جایگزین" value="" name="post_exview_alt[]"></div><div class="mb-3"><input hidden type="text" name="post_exview[]" id="post_exview_input' + count + '"><img src="" height="150px" class="mt-3 mx-auto d-block mw-100"></div></div>');
        count++;
    });

    $('.remove-exview').click(function () {
        $(this).parent().parent().remove();
    })

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
    $("#type-variable").click(function () {
        $("ul.nav-tabs li:nth-child(1),ul.nav-tabs li:nth-child(2)").addClass('d-none');
        $("ul.nav-tabs li:nth-child(3) button").trigger('click');
        $("ul.nav-tabs li:nth-child(4)").removeClass('d-none');
    });
    $("#type-simple").click(function () {
        $("ul.nav-tabs li:nth-child(1),ul.nav-tabs li:nth-child(2)").removeClass('d-none');
        $("ul.nav-tabs li:nth-child(1) button").trigger('click');
        $("ul.nav-tabs li:nth-child(4)").addClass('d-none');
    });
    $('#add-new-attribute').click(function () {
        $('#addNewTaxonomy').attr('data-taxonomy', 'product_attribute');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('ویژگی');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'product_attribute')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'product_attribute')");
        $('#offcanvasAddTaxonomy #category-parent').hide();
        $('#offcanvasAddTaxonomy #attribute-parent').show();
        $('#offcanvasAddTaxonomy #variable-parent').hide();
    });
    $('#add-new-variable').click(function () {
        $('#addNewTaxonomy').attr('data-taxonomy', 'product_variable');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('متغییر');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'product_variable')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'product_variable')");
        $('#offcanvasAddTaxonomy #category-parent').hide();
        $('#offcanvasAddTaxonomy #attribute-parent').hide();
        $('#offcanvasAddTaxonomy #variable-parent').show();
    });

    const category_selection = document.querySelector('.category_selection');
    const category_parent = document.querySelector('.parent_selection')
    const attribute_parent = document.querySelector('.attribute_selection')
    const variable_parent = document.querySelector('.variable_selection')
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
    var books = [
        <?php if (is_countable($all_books) && count($all_books) > 0) {
            $tree = show_taxonomy($all_books, 0, $books);
            echo $tree;
        } ?>
    ];
    var attributes = [
        <?php if (is_countable($product_attributes) && count($product_attributes) > 0) {
            $tree = show_taxonomy($product_attributes, 0, $attributes);
            echo $tree;
        } ?>
    ];
    var variables = [
        <?php if (is_countable($product_variables) && count($product_variables) > 0) {
            $tree = show_taxonomy($product_variables, 0, $variables);
            echo $tree;
        } ?>
    ];

    var product_attributes = [];
    <?php
    foreach ($product_attributes as $product_attribute) {
        echo "product_attributes.push({'{$product_attribute['tag_id']}':'{$product_attribute['name']}'});\n";
    } ?>

    var product_variables = [];
    <?php
    foreach ($product_variables as $product_attribute) {
        echo "product_variables.push({'{$product_attribute['tag_id']}':'{$product_attribute['name']}'});\n";
    }
    ?>

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

    var parent_attribute = new Tagify(attribute_parent, {
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
        whitelist: attributes
    });

    var parent_variable = new Tagify(variable_parent, {
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
        whitelist: variables
    });

    $('#taxonomy-submit').click(function () {
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

        getJSON('API/v1/InsertTaxonomy.php?name=' + name + '&slug=' + slug + '&parent=' + parent + '&type=' + type, function (err, data) {
            if (data != null && data.length > 0) {
                $('#addNewTaxonomy input[name="slug"]').parent().find(".validate-failed").remove();
                if (data == 'exsists') {
                    $('#addNewTaxonomy input[name="slug"]').parent().append('<p class="alert alert-danger validate-failed">این نام قبلا استفاده شده است</p>');
                    return false;
                } else {
                    $('#addNewTaxonomy .alert-danger').remove();
                    var name = data[0]['name'];
                    var id = data[0]['tag_id'];
                    if (data[0]['type'] == '<?php echo product_cat; ?>') {
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
                    } else if (data[0]['type'] == 'product_attribute') {
                        attributes.push({
                            "name": name,
                            "value": id
                        });
                        product_attributes.push({
                            [id]: name
                        });
                        if (parent > 0)
                            var attr_selects = $("#attribute_container select[name='']");
                        else
                            var attr_selects = $("#attribute_container select[name='product_attributes[]']");
                        $.each(attr_selects, function (i, obj) {
                            if ($(obj).data('select2')) {
                                $(obj).select2('destroy');
                            }
                            $(obj).find(":selected").attr("selected", "selected");
                            $(obj).append('<option value="' + id + '">' + name + '</option>');
                            $(obj).select2();
                        });
                        parent_attribute.destroy();
                        parent_attribute = new Tagify(attribute_parent, {
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
                            whitelist: attributes
                        });
                        toastr.success('ویژگی با موفقیت اضافه شد');
                    } else if (data[0]['type'] == 'product_variable') {
                        variables.push({
                            "name": name,
                            "value": id
                        });
                        product_variables.push({
                            [id]: name
                        });
                        if (parent > 0)
                            var var_selects = $(".variable-attribute-container select[name*='attr-value']");
                        else
                            var var_selects = $(".variable-attribute-container select[name*='attr-name']");
                        $.each(var_selects, function (i, obj) {
                            if ($(obj).data('select2')) {
                                $(obj).select2('destroy');
                            }
                            $(obj).find(":selected").attr("selected", "selected");
                            $(obj).append('<option value="' + id + '">' + name + '</option>');
                            $(obj).select2();
                        });
                        parent_variable.destroy();
                        parent_variable = new Tagify(variable_parent, {
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
                            whitelist: variables
                        });
                        toastr.success('متغییر با موفقیت اضافه شد');
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


    var add_variable = document.getElementById("add-variable");
    var variable_count = <?php echo $variable_count ?>;

    $(add_variable).click(function () {
        // var selects1 = $("#main-variable0 select");
        // $.each(selects1, function (i, obj) {
        //     if ($(obj).data('select2')) {
        //         $(obj).select2('destroy');
        //     }
        //     $(obj).find(":selected").attr("selected", "selected");
        // });
        $('#main-variable0').clone().appendTo("#variables_container");
        var ids = $('ul[id^="main-variable"]:last [name^="var[0]"]');
        variable_count++;
        $.each(ids, function () {
            var new_id = $(this).attr('id');
            var new_name = $(this).attr('name');
            if (new_id) {
                new_id = new_id.replace("var0", "var" + variable_count);
                $(this).attr('id', new_id);
            }
            new_name = new_name.replace("var[0", "var[" + variable_count);
            $(this).attr('name', new_name);
        });
        $("ul[id^='main-variable']:last input").val('');
        $("ul[id^='main-variable']:last textarea").val('');
        $("ul[id^='main-variable']:last img").attr('src', '');
        $("ul[id^='main-variable']:last").attr('id', 'main-variable' + variable_count);
        $("ul[id^='main-variable']:last").find('a').attr('href', 'filemanager/dialog.php?type=1s&field_id=var_image_input' + variable_count);
        $("ul[id^='main-variable']:last input[name$='[thumbnail]']").attr('id', 'var_image_input' + variable_count);
        $("#main-variable" + variable_count + " .col-2 button").remove();
        $("ul[id^='main-variable" + variable_count + "'] select[id*='-name']").prop('disabled', 'disabled');
        // $("#main-variable" + variable_count + " select").select2();
        // $("#main-variable0 select").select2();
    });

    function variable_attr_add(element) {
        for (var i = 0; i <= variable_count; i++) {
            var variable_container = $("#main-variable" + i);
            var attribute_count = $("select[id^='var" + i + "-name']").length;
            let select = generate_select(product_variables, 'var[' + i + '][attr-name][]', false, true);
            select.setAttribute('id', 'var' + i + '-name-' + attribute_count);
            let div1 = document.createElement('div');
            div1.setAttribute('class', 'col-12 col-md-5 mb-3');
            div1.appendChild(select);
            let sub_select = generate_select([], 'var[' + i + '][attr-value][]', true, true);
            sub_select.setAttribute('id', 'var' + i + '-value-' + attribute_count);
            let div2 = document.createElement('div');
            div2.setAttribute('class', 'col-12 col-md-5 mb-3');
            div2.appendChild(sub_select);
            $("#main-variable" + i + ' .col-2').before(div1);
            $("#main-variable" + i + ' .col-2').before(div2);
            var selects = $("#main-variable" + i + " select");
            $.each(selects, function (j, obj) {
                var id = $(obj).attr('id');
                if ($(obj).data('select2')) {
                    $(obj).select2('destroy');
                }

                $(obj).find(":selected").attr("selected", "selected");
                if (i > 0 && id.indexOf("name") >= 0)
                    $(obj).prop('disabled', 'disabled');
                $(obj).select2();
            });
        }


    };

    function get_product_sub_variable(select) {
        var get_select = select;
        let value = select.value;
        getJSON('API/v1/get_var.php?parent=' + value,
            function (err, data) {
                if (data != null && data.length > 0) {
                    for (var i = 0; i <= variable_count; i++) {
                        var id = $(get_select).attr('id');
                        var name_id = id.slice(4);
                        id = id.replace('name', 'value');
                        id = id.slice(4);
                        id = 'var' + i + id;
                        name_id = 'var' + i + name_id;
                        let select = document.getElementById(id);
                        let name_select = document.getElementById(name_id);
                        if ($(select).data('select2'))
                            $(select).select2('destroy');
                        if ($(name_select).data('select2'))
                            $(name_select).select2('destroy');
                        $(name_select).find(":selected").removeAttr("selected");
                        $("#" + name_id + " option[value='" + value + "']").attr("selected", "selected");
                        select.innerHTML = "";
                        for (const [key, value] of Object.entries(data)) {
                            let opt = document.createElement("option");
                            opt.innerHTML = value.name;
                            opt.setAttribute("value", value.tag_id);

                            select.appendChild(opt);
                        }
                        $(select).select2();
                        $(name_select).select2();
                    }

                }
            });
    }


    function generate_select(list, name, is_sub_pa, is_variable) {
        var select = document.createElement("select");
        select.setAttribute("name", name);

        if (!is_sub_pa) {
            if (!is_variable)
                select.setAttribute("onchange", "get_product_sub_attribute(this)");
            else
                select.setAttribute("onchange", "get_product_sub_variable(this)");
        }
        let opt_initial = document.createElement("option");
        opt_initial.innerHTML = 'حذف';
        opt_initial.setAttribute('value', 'delete');
        select.appendChild(opt_initial);
        for (const [key, value] of Object.entries(list)) {
            let opt = document.createElement("option");
            if (value.tag_id > 0) {
                opt.innerHTML = value.name;
                opt.setAttribute("value", value.tag_id);
            } else {
                for ([val_key, val_value] of Object.entries(value)) {
                    opt.innerHTML = val_value;
                    opt.setAttribute("value", val_key);
                }
            }
            select.appendChild(opt);
        }
        return select;
    }
</script>