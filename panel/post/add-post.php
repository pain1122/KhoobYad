<?php
if (isset($_POST['submit']) || isset($_POST['draft'])) {
    $post_id = 'new_post';
    if (isset($_GET['id']))
        $post_id = $_GET['id'];
    $obj = new blog($post_id);
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
    $seo_title = $_POST['seo_title'];
    $seo_keywords = $_POST['seo_keywords'];
    $seo_desc = $_POST['seo_desc'];
    $recomandeds = explode(",",$_POST['recomandeds']);
    $recomandeds = join(",",array_unique($recomandeds));
    $noindex = $_POST['noindex'];
    if(empty($noindex))
        $noindex = 'index,follow';
    $canonical = $_POST['canonical'];
    $tags = json_decode($_POST['tag_selection'], true);
    $cats = json_decode($_POST['category_selection'], true);
    $thumbnail_src = $_POST['post_image'];
    $thumbnail_alt = $_POST['post_image_alt'];
    if (strlen($thumbnail_src) > 1) {
        if(strpos($thumbnail_src,upload_folder) === false){
            $thumbnail_src = site_url . upload_folder .$thumbnail_src;
        }
        $obj->set_thumbnail_src($thumbnail_src);
    }
    $thumbnail_id = $obj->get_thumbnail();
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_slug($guid);
    $obj->set_content($content);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    $metas = [
        "noindex" => $noindex,
        "canonical" => $canonical,
        "_thumbnail_id" => $thumbnail_id,
        "image_alt" => $thumbnail_alt,
        "recomandeds" => $recomandeds,
        seo_desc_name => $seo_desc,
        seo_title_name => $seo_title,
        seo_keywords_name => $seo_keywords
    ];
    $obj->insert_meta($metas);
    $categories = [];
    $post_tags = [];
    foreach ($cats as $cat) :
        $categories[$cat['name']] = $cat['value'];
    endforeach;
    foreach ($tags as $tag) :
        $post_tags[$tag['name']] = $tag['value'];
    endforeach;
    $obj->set_tags($post_tags);
    $obj->set_cats($categories);
    if (!isset($_GET['id'])) {
        $url = site_url ."panel/index.php?page=post/add-post.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $obj = new blog($post_id);
    $title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $content = $obj->get_content();
    $seo_title = $obj->get_seo_title();
    $seo_keywords = $obj->get_seo_keywords();
    $seo_desc = $obj->get_seo_desc();
    $recomandeds = $obj->get_meta('recomandeds');
    $noindex = $obj->get_meta('noindex');
    $canonical = $obj->get_meta('canonical');
    $tags = $obj->get_tags();
    $cats = $obj->get_cats();
    $thumbnail_src = $obj->get_thumbnail_src();
    $thumbnail_alt = $obj->get_image_alt();
}
$all_cats = tag::get_taxonomies(['type' => 'category']);
$all_tags = tag::get_taxonomies(['type' => 'post_tag']);

function show_taxonomy(array $tags, $parentId = 0, &$categories)
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
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">عنوان مقاله</label>
                    <input type="text" name="post_title" value="<?php echo $title; ?>" onchange="insert_sku(this,'post_guid')" class="form-control" placeholder="عنوان مقاله خود را وارد کنید">
                </div>
                <div>
                    <label class="form-label">نامک</label>
                    <input type="text" onkeyup="validate_name(this,'post')" onchange="validate_name(this,'post')" name="post_guid" id="post_guid" value="<?php echo $guid; ?>" class="form-control" placeholder="نامک خود را وارد کنید">
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">محتوای مقاله</h5>
            <div class="card-body">
                <textarea name="post_content" row="25" id="editor1" placeholder="محتوای خود را وارد کنید"><?php echo $content; ?></textarea>
            </div>
        </div>
        <div class="accordion" id="collapsibleSection">
            <div class="card accordion-item">
                <h2 class="accordion-header">
                    <button type="button" aria-expanded="false" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#seo">
                        اطلاعات سئو
                    </button>
                </h2>
                <div id="seo" class="accordion-collapse collapse card-body" data-bs-parent="#collapsibleSection">
                    <div class="mb-3 position-relative">
                        <label class="form-label">متا تایتل</label>
                        <input type="text" name="seo_title" onkeyup="countChar(this,65,'seo_title')" onchange="countChar(this,'seo_title')" value="<?php echo $seo_title; ?>" class="form-control" placeholder="متا تایتل خود را وارد کنید">
                        <div id="seo_title" style="position: absolute;top: 5px;left: 10px;"><span></span>/<span>65</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">متا کیوردز</label>
                        <input type="text" name="seo_keywords" value="<?php echo $seo_keywords; ?>" class="form-control" placeholder="متا کیوردز خود را وارد کنید">
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">متا دیسکریپشن</label>
                        <input type="text" name="seo_desc" onkeyup="countChar(this,165,'seo_desc')" onchange="countChar(this,'seo_desc')" value="<?php echo $seo_desc; ?>" class="form-control" placeholder="متا دیسکریپشن خود را وارد کنید">
                        <div id="seo_desc" style="position: absolute;top: 5px;left: 10px;"><span></span>/<span>165</span></div>
                    </div>
                    <div>
                        <label class="form-label">Canonical</label>
                        <input type="text" name="canonical" value="<?php echo $canonical; ?>" class="form-control" placeholder="لینک canonical خود را وارد کنید">
                    </div>
                </div>
            </div>
            <div class="card accordion-item">
                <h2 class="accordion-header">
                    <button type="button" aria-expanded="false" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#related_produtcs">
                        محصولات مرتبط
                    </button>
                </h2>
                <div id="related_produtcs" class="accordion-collapse collapse card-body" data-bs-parent="#collapsibleSection">
                    <div class="mb-3 search-bar">
                        <label class="form-label">جستوجو محصول</label>
                        <input class="form-control product-search" name="name" onkeyup="Search(this,'recomandeds')" type="text" placeholder="نام محصول موردنظر خود را وارد کنید">
                        <div class="search-results card-body">
                            <div class="search-result">
                                <p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">در حال جست و جو</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="<?php echo $recomandeds; ?>" name="recomandeds" id="recomandeds">
                    <div class="complementaries">
                        <?php if (strlen($recomandeds) > 0) :
                            $recomandeds = str_replace(',,', '', $recomandeds);
                            $recomandeds = explode(',', $recomandeds);
                            foreach ($recomandeds as $recomanded) :
                                $cproduct = base::Fetchassoc("SELECT `post_title`,`post_id` from `post` WHERE `post_id` = $recomanded"); ?>
                                <div class="complementary">
                                    <p><?php echo $cproduct['post_title'] ?></p>
                                    <span class="close" onclick="remcomp(<?php echo $cproduct['post_id'] ?>,'recomandeds');$(this).parent().remove();">✖</span>
                                </div>
                        <?php endforeach;
                        endif; ?>
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
                <input class="form-check-input" type="checkbox" value="noindex,nofollow" name="noindex" <?php if ($noindex == "noindex,nofollow") echo "checked"; ?>>
                    <label class="form-check-label"> NoIndex</label>
                </div>
            </div>

            <div class="card-body d-flex justify-content-around p-3 pt-0">
                <button type="submit" name="submit" class="btn btn-sm btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> انتشار</button>
                <button type="submit" name="draft" class="btn btn-sm btn-primary p-2"><i class="fa-regular fa-square-pen"></i> پیش نویس</button>
                <?php if(!empty($guid)): ?>
                <a href="/<?php echo blog_url . $guid; ?>" class="btn btn-sm btn-info p-2"><i class="fa-regular fa-eye"></i> پیش نمایش</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">دسته بندی ها</h5>
            <div class="card-body">
                <label class="form-label">انتخاب دسته بندی</label>
                <input name="category_selection" class="form-control category_selection" placeholder="انتخاب دسته بندی" value="<?php if (is_countable($cats) && count($cats) > 0) {foreach ($cats as $cat) {echo $cat['name'] . ",";}} ?>">
                <button type="button" class="btn btn-primary mt-3" id="add-category" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTaxonomy">ایجاد دسته</button>
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">برچسب ها</h5>
            <div class="card-body">
                <label class="form-label">انتخاب برچسب</label>
                <input name="tag_selection" class="form-control tag_selection" placeholder="انتخاب برچسب" value="<?php if (is_countable($tags) && count($tags) > 0) {foreach ($tags as $tag) {echo $tag['name'] . ",";}} ?>">
                <button type="button" class="btn btn-primary mt-3" id="add-tag" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTaxonomy">ایجاد برچسب</button>
            </div>
        </div>
        <div class="card">
            <h5 class="card-header d-flex align-items-center">تصویر شاخص<a class="btn btn-sm btn-success mr-auto" href="filemanager/dialog.php?type=1s&field_id=post_image_input" data-fancybox data-type="iframe" data-preload="false">انتخاب عکس</a></h5>
            <div class="card-body">
                <div class="mb-3">
                    <input hidden value="<?php echo $thumbnail_src; ?>" type="text" name="post_image" id="post_image_input">
                    <img src="<?php echo $thumbnail_src; ?>" id="post_image" width="85%" class="mt-3 mx-auto d-block">
                </div>
                <div>
                    <label for="formFileMultiple" class="form-label">متن جایگزین</label>
                    <input class="form-control input-air-primary" type="text" placeholder=" (alt text)نوشته جایگزین" value="<?php echo $thumbnail_alt ?>" name="post_image_alt">
                </div>
            </div>
        </div>
    </div>
</form>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasAddTaxonomy" aria-labelledby="offcanvasAddTaxonomyLabel">
    <div class="offcanvas-header border-bottom">
        <h6 id="offcanvasAddTaxonomyLabel" class="offcanvas-title">افزودن <span>دسته بندی</span></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="add-new-user pt-0" id="addNewTaxonomy" data-taxonomy='category' onsubmit="return false">
            <div class="mb-3">
                <label class="form-label" for="add-user-fullname">نام</label>
                <input type="text" onchange="insert_sku(this,'type')" class="form-control" placeholder="نام" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">نامک</label>
                <input type="text" onkeyup="validate_name(this,'category')" onchange="validate_name(this,'category')" id="type" class="form-control text-start" placeholder="نامک" name="slug" required>
            </div>
            <div class="mb-3" id="category-parent">
                <label class="form-label">دسته مادر</label>
                <input name="parent" class="form-control parent_selection" placeholder="انتخاب دسته">
            </div>
            <button type="submit" id="taxonomy-submit" class="btn btn-primary me-sm-3 me-1">ثبت</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">انصراف</button>
        </form>
    </div>
</div>
<script src="assets/vendor/libs/tagify/tagify.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script src="assets/vendor/libs/ckeditor/ckeditor.custom.js"></script>
<script>
    $( document ).ready(function() {
        let seo_desc = document.getElementsByName('seo_desc')[0];
        countChar(seo_desc,165,'seo_desc');
        let seo_title = document.getElementsByName('seo_title')[0];
        countChar(seo_title,165,'seo_title');
    });
    let image_directory = "<?php echo site_url.upload_folder ?>";
    function responsive_filemanager_callback(field_id){
        let image_url = $('#'+field_id).val();
        $('#post_image').attr("src",image_url);
        close_window();
}
function close_window() {
    Fancybox.getInstance().close();
} 
    const category_selection = document.querySelector('.category_selection');
    const category_parent = document.querySelector('.parent_selection')
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
    $('#add-tag').click(function() {
        $('#addNewTaxonomy').attr('data-taxonomy', 'post_tag');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('برچسب');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'post_tag')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'post_tag')");
        $('#offcanvasAddTaxonomy #category-parent').hide();
    });
    $('#add-category').click(function() {
        $('#addNewTaxonomy').attr('data-taxonomy', 'category');
        $('#offcanvasAddTaxonomy #offcanvasAddTaxonomyLabel span').text('دسته بندی');
        $('#offcanvasAddTaxonomy #type').attr("onkeyup", "validate_name(this,'category')");
        $('#offcanvasAddTaxonomy #type').attr("onchange", "validate_name(this,'category')");
        $('#offcanvasAddTaxonomy #category-parent').show();
    });
    $('#taxonomy-submit').click(function() {
        var name = $.trim($('#addNewTaxonomy input[name="name"]').val());
        var slug = $.trim($('#addNewTaxonomy input[name="slug"]').val());
        var parent = $('#addNewTaxonomy input[name="parent"]').val();
        if (parent && parent.length > 0) {
            parent = JSON.parse(parent);
            parent = parent[0].value;
        } else {
            parent = 0;
        }
        var type = $('#addNewTaxonomy').attr('data-taxonomy');

        getJSON('API/v1/InsertTaxonomy.php?name=' + name + '&slug=' + slug + '&parent=' + parent + '&type=' + type, function(err, data) {
            if (data != null && data.length > 0) {
                $('#addNewTaxonomy input[name="slug"]').parent().find(".validate-failed").remove();
                if (data == 'exsists') {
                    $('#addNewTaxonomy input[name="slug"]').parent().append('<p class="alert alert-danger validate-failed">این نام قبلا استفاده شده است</p>');
                    return false;
                } else {
                    var name = data[0]['name'];
                    var id = data[0]['tag_id'];
                    if (data[0]['type'] == 'category') {
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
                        toastr.success('دسته بندی با موقیت اضافه شد');
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
                        toastr.success(' با موقیت اضافه شد');
                    }
                    $('#addNewTaxonomy').trigger("reset");
                }
            }
        });
    })
</script>