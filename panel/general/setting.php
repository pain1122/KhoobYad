<?php
$page_title = "";
$page_bread_crumb = ["داشبورد", "تنظیمات"];
$page_type = "";
$menu_style = "";
$name_valide = true;
$slug_valid = true;
$parent_valid = true;
$description_valid = true;
$icon_valid = true;
if (isset($_POST['submit'])) {
    if ($_POST['tag_name'] != "") {
        $tag_name = $_POST['tag_name'];
        if (isset($_POST['submit']) && isset($_GET['id'])) {
            //UPDATE Tag
            $id = intval($_GET['id']);
            $query = "UPDATE `options` SET `value`='$tag_name' WHERE `option_id`= $id AND `name`= 'lang'";
            $con->query($query);
        } else {
            //INSERT Tag
            $insert_tag_query = "INSERT INTO `options`(`name`, `value`) VALUES ('lang','$tag_name')";
            $con->query($insert_tag_query);
        }
    }
}
if (isset($_POST['delete']) || $_POST['action'] == "delete") {
    $tags = $_POST['tags'];

    foreach ($tags as $tag) {
        //DELETE Tag
        $con->query("DELETE FROM `options` WHERE `option_id`= $tag AND `name`= 'lang'");
    }
}

$select_cat = "SELECT * FROM `options` WHERE`name` = 'lang'";
$tags = $functions->FetchArray($select_cat, $con);
?>
<div class="page-body">
    <div class="container-fluid">
        <?php include_once('bread.php') ?>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">افزودن</h4>
                        <div class="card-options"><a class="card-options-collapse" href="#" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                    </div>
                    <div class="card-body">
                        <form class="theme-form" action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">نام</label>
                                <input required name="tag_name" class="form-control" type="text">
                            </div>
                            <div class="form-footer">
                                <input type="submit" name="submit" class="btn btn-primary btn-block btn-pill" value="ذخیره کنید">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card">
                    <form class="theme-form" action="" method="post">
                        <div class="card-body">
                            <div class="box digits">
                                <select name="action" class="js-example-placeholder-multiple col-sm-4">
                                    <option disabled selected>کارهای دسته‌جمعی</option>
                                    <option value="delete">حذف</option>
                                </select>
                                <button class="btn btn-primary btn-pill" type="submit"> اجرا </button>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr role="row">
                                            <th tabindex="0" aria-controls="basic-1" rowspan="1" colspan="1" style="width: 20.883px;"><input onClick="toggle(this)" name="toggle" type="checkbox" /></th>
                                            <th class="sorting_asc" tabindex="0" aria-controls="basic-1" rowspan="1" colspan="1" style="width: 174.883px;">نام</th>
                                            <th class="sorting" tabindex="0" aria-controls="basic-1" rowspan="1" colspan="1" style="width: 100.65px;">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($tags as $tag) {
                                        ?>
                                            <tr role="row">
                                                <td><input type="checkbox" name="tags[]" value="<?php echo $tag['option_id'] ?>" /></td>
                                                <td><?php echo $tag['value'] ?></td>
                                                <td>
                                                    <a href="?page=general/setting.php&id=<?php echo $tag['option_id']; ?>" class="btn btn-success btn-xs mr-2" data-original-title="btn btn-danger btn-xs" title="">ویرایش</a>
                                                    <a href="?page=general/add_title.php&lang=<?php echo $tag['value']; ?>" class="btn btn-success btn-xs" data-original-title="btn btn-danger btn-xs" title="">ویرایش عنوان ها</a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

