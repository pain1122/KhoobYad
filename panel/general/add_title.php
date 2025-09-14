<?php
$page_title = "";
$page_bread_crumb = ["تنظیمات", "عناوین"];
$page_type = "";
$menu_style = "";
$name_valide = true;
$slug_valid = true;
$parent_valid = true;
$description_valid = true;
$icon_valid = true;
$lang = $_GET['lang'];
if (isset($_POST['submit'])) :
    $titles = $_POST;
    foreach ($titles as  $title_key => $title_value) :
        if ($title_key != 'submit') :
            base::insert_title($lang, $title_key, $title_value, $con);
        endif;
    endforeach;
endif;
$select_cat = "SELECT * FROM `language` WHERE `lang` = 'fa'";
$tags = base::FetchArray($select_cat);
?>
<div class="page-body">
    <div class="container-fluid">
        <?php include_once('bread.php') ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">عناوین</h4>
                        <div class="card-options"><a class="card-options-collapse" href="#" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                    </div>
                    <div class="card-body">
                        <form class="theme-form" action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                            <?php
                            foreach ($tags as $tag) :
                            ?>
                                <div class="form-group col-12 col-md-6 mb-3">
                                    <label class="form-label"><?php echo $tag['key']; ?></label>
                                    <input required name="<?php echo $tag['key']; ?>" class="form-control" type="text" value="<?php echo Base::get_lang_title($lang, $tag['key']); ?>">
                                </div>
                            <?php
                            endforeach;
                            ?>
                            </div>
                            <div class="form-footer">
                                <input type="submit" name="submit" class="btn btn-primary btn-block btn-pill" value="ذخیره کنید">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>