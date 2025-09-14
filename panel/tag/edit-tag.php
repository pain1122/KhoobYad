<?php
if (isset($_GET['type']))
    $type = $_GET['type'];
else
    base::redirect('index.php');
$content_valid = true;
$current = 0;
if ($type == "category") {
    $name_valid = true;
    $slug_valid = true;
    $parent_valid = true;
    $description_valid = true;
    $title_valid = true;
    $key_valid = true;
    $icon_valid = true;
}
if ($type == "post_tag") {
    $name_valid = true;
    $slug_valid = true;
    $parent_valid = false;
    $description_valid = true;
    $title_valid = true;
    $key_valid = true;
    $icon_valid = true;
}
if ($type == "off_boxes_tag" || $type == "online_class_tag" || $type == "study_course" || $type == "study_grade") {
    $name_valid = true;
    $slug_valid = true;
    $parent_valid = false;
    $description_valid = false;
    $title_valid = false;
    $key_valid = false;
    $icon_valid = false;
    $content_valid = false;
}
if ($type == "off_boxes_category" || $type == "online_class_category") {
    $name_valid = true;
    $slug_valid = true;
    $parent_valid = true;
    $description_valid = false;
    $title_valid = false;
    $key_valid = false;
    $icon_valid = false;
    $content_valid = false;
}
if ($type == "product_attribiute") {
    $name_valid = true;
    $slug_valid = true;
    $parent_valid = false;
    $description_valid = false;
    $title_valid = false;
    $key_valid = false;
    $icon_valid = false;
    $content_valid = false;
}

if (isset($_POST['submit'])) {
    $metas = [];
    $tag_id = "new_tag";
    if (isset($_GET['id']))
        $tag_id = $_GET['id'];
    $obj = new tag($tag_id);
    $tag_id = $obj->get_id();
    $obj->set_type($type);
    if ($name_valid) {
        $name = trim($_POST['tag_name']);
        $obj->set_name($name);
    }
    if ($slug_valid) {
        $slug = urlencode(str_replace(' ', '-', $_POST['tag_slug']));
        $obj->set_slug($slug);
    }
    if ($parent_valid) {
        $parent = $_POST['tag_parent'];
        $obj->set_parent($parent);
    }
    if ($content_valid) {
        $description = htmlentities($_POST['cat_meta'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
        $obj->set_description($description);
    }
    if ($title_valid) {
        $seo_title = $_POST['seo_title'];
        $metas[seo_title_name] = $seo_title;
    }
    if ($key_valid) {
        $seo_keywords = $_POST['seo_keywords'];
        $metas[seo_keywords_name] = $seo_keywords;
    }
    if ($description_valid) {
        $seo_desc = $_POST['seo_desc'];
        $metas[seo_desc_name] = $seo_desc;
    }
    if($icon_valid){
        $icon = $_FILES['tag_icon'];
        if (strlen($icon['tmp_name']) > 1) {
            $icon = base::Upload($icon);
        } else {
            $icon = $obj->get_meta('image');
        }
        $metas["image"] = $icon;
    }
    $noindex = $_POST['noindex'];
    $metas['noindex'] = $noindex;
    if($icon_valid || $description_valid || $key_valid || $title_valid)
        $obj->set_tag_meta($metas);

    if (!isset($_GET['id'])) {
        $url = site_url . $_SERVER["REQUEST_URI"] . "&id=" . $tag_id;
        base::redirect($url);
    }
}

if (isset($_GET['id'])) {
    $taxonomy_id = intval($_GET['id']);
    $obj = new tag($taxonomy_id);
    $name = $obj->get_name();
    $slug = urldecode($obj->get_slug());
    $parent = $obj->get_parent();
    $count = $obj->get_count();
    $description = $obj->get_description();
    $noindex = $obj->get_meta('noindex');
    $icon = base::displayphoto($obj->get_meta('image'));
    $seo_title = $obj->get_seo_title();
    $seo_keywords = $obj->get_seo_keywords();
    $seo_desc = $obj->get_seo_desc();
    $current = $parent;
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    base::RunQuery("DELETE `tag`,`tag_meta`,`tag_relationships` FROM `tag` LEFT JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` LEFT JOIN `tag_relationships` ON `tag`.`tag_id` = `tag_relationships`.`tag_id` WHERE `tag`.`tag_id` = $id");
}
$all_taxonomis = tag::get_taxonomies(['type' => $type]);
$i = 0;
function show_taxonomy(array $tags, $parentId = 0, &$i, $current)
{
    $branch = "";
    foreach ($tags as $tag) {
        $tagid = intval($tag['tag_id']);
        // if (! base::in_array_recursive($tagid,$categories)) {
        $cat = new tag($tagid);
        $tag_parent = $cat->get_parent();
        if ($tag_parent == $parentId) {
            $tagname = '';
            for ($j = 0; $j < $i; $j++) $tagname .= "- ";
            $tagname .= $tag['name']; ?>
            <option value="<?php echo $tagid; ?>" <?php if ($current == $tagid) echo "selected"; ?>><?php echo $tagname; ?></option>
<?php $i++;
            show_taxonomy($tags, $tagid, $i, $current);
        }
        // }
    }
    $i--;
    return $branch;
}
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">

        
        <div class="row">
            <div class="col-12 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title mb-0">افزودن</h4>
                    </div>
                    <div class="card-body p-3">
                        <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3 col-12">
                                <input class="form-check-input" type="checkbox" value="noindex,nofollow" name="noindex" <?php if ($noindex == "noindex,nofollow") echo "checked"; ?>>
                                <label class="form-check-label"> NoIndex</label>
                            </div>
                            <?php
                            if ($name_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <label class="form-label">نام</label>
                                    <input required onkeyup="replace_digits(this);" onchange="insert_sku(this,'slug')" name="tag_name" class="form-control" type="text" value="<?php echo $name; ?>">
                                </div>
                            <?php
                            endif;
                            if ($slug_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <label class="form-label">نامک</label>
                                    <input onkeyup="validate_name(this,'<?php echo $type ?>')" onchange="replace_digits(this);validate_name(this,'<?php echo $type ?>')" required name="tag_slug" class="form-control" type="text" id="slug" value="<?php echo $slug; ?>">
                                </div>
                            <?php
                            endif;
                            if ($parent_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <label class="form-label">دسته مادر</label>
                                    <select name="tag_parent" class="col-sm-12 select2">
                                        <option value="0">دسته مادر</option>
                                        <?php if (is_countable($all_taxonomis) && count($all_taxonomis) > 0) {
                                            $tree = show_taxonomy($all_taxonomis, 0, $i, $current);
                                            echo $tree;
                                        } ?>
                                    </select>
                                </div>
                            <?php
                            endif;
                            if ($title_valid) :
                            ?>
                                <div class="mb-3 col-12 position-relative">
                                    <label class="form-label">متا تایتل</label>
                                    <input name="seo_title" class="form-control" type="text" onkeyup="countChar(this,65,'seo_title')" onchange="countChar(this,'seo_title')" value="<?php echo $seo_title; ?>">
                                    <div id="seo_title" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_title); ?></span>/<span>65</span></div>
                                </div>
                            <?php
                            endif;
                            if ($key_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <label class="form-label">کیورد ها</label>
                                    <input name="seo_keywords" class="form-control" type="text" value="<?php echo $seo_keywords; ?>">
                                </div>
                            <?php
                            endif;
                            if ($description_valid) :
                            ?>
                                <div class="mb-3 col-12 position-relative">
                                    <h2 class="form-label">متا دیسکریپشن</h2>
                                    <input type="text" name="seo_desc" class="form-control" onkeyup="countChar(this,165,'seo_desc')" onchange="countChar(this,'seo_desc')" value="<?php echo $seo_desc; ?>">
                                    <div id="seo_desc" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_desc,); ?></span>/<span>165</span></div>
                                </div>
                            <?php
                            endif;

                            if ($icon_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <h2 class="form-label">تصویر</h2>
                                    <input name="tag_icon" accept="image/*" type="file" class="form-control mb-4">
                                    <img width="200px" src="<?php echo $icon; ?>">
                                </div>
                            <?php
                            endif;
                            if ($content_valid) :
                            ?>
                                <div class="mb-3 col-12">
                                    <h2 class="form-label">محتوای اضافی</h2>
                                    <div class="card p-0">
                                        <textarea id="editor1" name="cat_meta"><?php echo $description; ?></textarea>
                                    </div>
                                </div>
                            <?php
                            endif;
                            ?>
                            <div class="card-footer col-12">
                                <button type="submit" name="submit" class="btn btn-primary btn-pill"> ذخیره کنید </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card">
                    <form class="theme-form" action="" method="post">
                        <div class="card-body">
                            <div class="card-datatable text-nowrap overflow-auto">
                                <table class="datatables-ajax table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>آیدی</th>
                                            <th>آیکن</th>
                                            <th>نام</th>
                                            <th>نامک</th>
                                            <th>والد</th>
                                            <th>تعداد</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.custom.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script>
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
    var dt_ajax_table = $('.datatables-ajax');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: 'API/v1/GetTaxonomy.php?type=<?php echo $type ?>'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    }
</script>