<?php
$type = "product_attribute";
if (isset($_GET['type']))
    $type = $_GET['type'];
$title = "ویژگی";
if (isset($_POST['submit'])) {
    $tag_id = null;
    if (isset($_GET['id']))
        $tag_id = $_GET['id'];
    $obj = new tag($tag_id);
    $tag_id = $obj->get_id();
    $tag_name = trim($_POST['tag_name']);
    $obj->set_name($tag_name);
    $obj->set_type($type);
    if (isset($_GET['parent'])) {
        $obj->set_parent($_GET['parent']);
        if ($type == 'product_attribute') 
        {
            $tag_slug = urlencode(str_replace(' ', '-', 'pa_' . $_POST['tag_slug']));
        } elseif ($type == 'product_variable') 
        {
            $var_type = $_POST['variable_type'];
            $obj->set_tag_meta(['variable_type' => $var_type]);
            $tag_slug = urlencode(str_replace(' ', '-', 'var_' . $_POST['tag_slug']));
        }
    } else {
        $tag_slug = urlencode(str_replace(' ', '-', $_POST['tag_slug']));
    }
    $obj->set_slug($tag_slug);
}
if (isset($_GET['id'])) {
    $taxonomy_id = intval($_GET['id']);
    $obj = new tag($taxonomy_id);
    $type = $obj->get_type();
    if (isset($_GET['parent'])) 
    {
        $name = $obj->get_name();
        $slug = urldecode($obj->get_slug());
        $taxonomy_id = $_GET['parent'];
        if ($type == 'product_attribute') {
            $slug = str_replace('pa_', '', $slug);
        } elseif ($type == 'product_variable') {
            $slug = str_replace('var_', '', $slug);
        }
    }
    $variable_type = $obj->get_meta('variable_type');
    if (isset($_GET['edit'])) {
        $name = $obj->get_name();
        $slug = urldecode($obj->get_slug());
        $taxonomy_id = null;
    }
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    base::RunQuery("UPDATE `tag_meta` SET `parent` = 0 WHERE `parent` = $id");
    base::RunQuery("DELETE `tag`,`tag_meta`,`tag_relationships` FROM `tag` LEFT JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id` LEFT JOIN `tag_relationships` ON `tag`.`tag_id` = `tag_relationships`.`tag_id` WHERE `tag`.`tag_id` = $id");
}
if (isset($_GET['parent'])) {
    $taxonomy_id = $_GET['parent'];
    $parent_tag = new tag($taxonomy_id);
    $variable_type = $parent_tag->get_meta('variable_type');
}
if ($type == 'product_variable')
    $title = "متغییر";
elseif (strpos($slug, 'pa_') || strpos($slug, 'var_'))
    $title = 'مقادیر ' . $name;
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css"/>
<div class="row">
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h4 class="card-title mb-0">افزودن <?php echo $title; ?></h4>
            </div>
            <div class="card-body p-3">
                <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3 col-12">
                        <label class="form-label">نام</label>
                        <input required onkeyup="replace_digits(this);" onchange="insert_sku(this,'slug')" name="tag_name" class="form-control" type="text" value="<?php echo $name; ?>">
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label"><?php if ($variable_type == 'color') : ?>کد رنگ<?php else : ?>نامک<?php endif; ?></label>
                        <?php if ($variable_type != 'color') : ?>
                        <input onchange="replace_digits(this);" required name="tag_slug" class="form-control" type="text" id="slug" value="<?php echo $slug; ?>">
                        <?php else: ?>
                                <div class="monolith col col-sm-3 col-lg-2">
                                    <div class="pickr">
                                        <div id="color-picker"></div>
                                        <input hidden onchange="replace_digits(this);" required name="tag_slug" class="form-control" type="text" id="slug" value="<?php echo $slug; ?>">
                                    </div>
                                </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($type == 'product_variable' && !isset($_GET['parent'])) : ?>
                        <div class="mb-3 col-12 d-flex flex-wrap align-items-center">
                            <h5 class="mb-0 ml-3 w-100">نوع محصول</h5>
                            <input name="variable_type" class="form-check-input mt-0 ml-1" type="radio" value="text" <?php if ($variable_type == 'text' || !$variable_type) echo 'checked' ?>>
                            <label class="form-check-label ml-3"> متنی </label>
                            <input name="variable_type" class="form-check-input mt-0 ml-1" type="radio" value="color" <?php if ($variable_type == 'color') echo 'checked' ?>>
                            <label class="form-check-label"> رنگ </label>
                        </div>
                    <?php endif; ?>
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
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-ajax table table-bordered">
                            <thead>
                                <tr>
                                    <th>آیدی</th>
                                    <th>نام</th>
                                    <th><?php if ($variable_type == 'color') : ?>کد رنگ<?php else : ?>نامک<?php endif; ?></th>
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
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<script>
    var dt_ajax_table = $('.datatables-ajax');
    if (dt_ajax_table.length) {
        var dt_ajax = dt_ajax_table.dataTable({
            processing: true,
            serverSide: true,
            select: true,
            ajax: {
                url: 'API/v1/GetAttribiute.php?type=<?php echo $type; ?>&parent=<?php echo $taxonomy_id; ?>'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    }
        $( document ).ready(function() {
            var pickr = Pickr.create({
                el: '#color-picker',
                theme: 'nano',
                <?php if (isset($_GET['id'])): ?>
                default: '<?php echo $slug; ?>',
                <?php endif; ?>
                swatches: [
                'rgba(244, 67, 54, 1)',
                'rgba(233, 30, 99, 0.95)',
                'rgba(156, 39, 176, 0.9)',
                'rgba(103, 58, 183, 0.85)',
                'rgba(63, 81, 181, 0.8)',
                'rgba(33, 150, 243, 0.75)',
                'rgba(3, 169, 244, 0.7)',
                'rgba(0, 188, 212, 0.7)',
                'rgba(0, 150, 136, 0.75)',
                'rgba(76, 175, 80, 0.8)',
                'rgba(139, 195, 74, 0.85)',
                'rgba(205, 220, 57, 0.9)',
                'rgba(255, 235, 59, 0.95)',
                'rgba(255, 193, 7, 1)'
            ],

            components: {

                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: true,
                    save: true
                }
            }
            }).on('save', (color, instance) => {
                $('#slug').val(''+color.toHEXA());
            });
        });
</script>