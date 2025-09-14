<?php
if (isset($_POST['post_content']) || isset($_POST['draft'])) {
    $post_id = 'new_post';
    if (isset($_GET['id']))
        $post_id = $_GET['id'];
    $obj = new blog($post_id);
    $post_id = $obj->get_id();
    $post_status = 'publish';
    if (isset($_POST['draft']))
        $post_status = 'draft';
    $obj->set_post_type('page');
    $author = $uid;
    $title = trim($_POST['post_title']);
    $guid = urlencode(str_replace(' ', '-', $_POST['post_guid']));
    if(empty($guid))
        $guid = urlencode(str_replace(' ', '-', $title));
    $content = htmlentities($_POST['post_content'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
    $seo_title = $_POST['seo_title'];
    $seo_keywords = $_POST['seo_keywords'];
    $seo_desc = $_POST['seo_desc'];
    $noindex = $_POST['noindex'];
    if(empty($noindex))
        $noindex = 'index,follow';
    $canonical = $_POST['canonical'];
    $obj->set_author($author);
    $obj->set_title($title);
    $obj->set_guid($guid);
    $obj->set_content($content);
    $obj->set_status($post_status);
    $obj->set_post_modify('CURRENT_TIMESTAMP');
    $metas = [
        "noindex" => $noindex,
        "canonical" => $canonical,
        seo_desc_name => $seo_desc,
        seo_title_name => $seo_title,
        seo_keywords_name => $seo_keywords
    ];
    $obj->insert_meta($metas);
    if (!isset($_GET['id'])) {
        $url = site_url . "panel/index.php?page=pages/add-page.php&id=" . $post_id;
        base::redirect($url);
    }
}
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $obj = new blog($post_id);
    $title = $obj->get_title();
    $guid = urldecode($obj->get_guid());
    $content = $obj->get_content();
    $seo_title = $obj->get_seo_title();
    $seo_keywords = $obj->get_seo_keywords();
    $seo_desc = $obj->get_seo_desc();
    $noindex = $obj->get_meta('noindex');
    $canonical = $obj->get_meta('canonical');
}
?>
<link rel="stylesheet" href="assets/vendor/libs/grapes/grapes.min.css">
<link rel="stylesheet" href="assets/vendor/libs/grapes/grapesjs-preset-webpage.min.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7 order-1">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">عنوان</label>
                    <input type="text" name="post_title" value="<?php echo $title; ?>" onchange="insert_sku(this,'post_guid')" class="form-control" placeholder="عنوان خود را وارد کنید">
                </div>
                <div>
                    <label class="form-label">نامک</label>
                    <input type="text" onkeyup="validate_name(this,'post')" onchange="validate_name(this,'post')" name="post_guid" id="post_guid" value="<?php echo $guid; ?>" class="form-control" placeholder="نامک خود را وارد کنید">
                </div>
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
                        <div id="seo_title" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_title); ?></span>/<span>65</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">متا کیوردز</label>
                        <input type="text" name="seo_keywords" value="<?php echo $seo_keywords; ?>" class="form-control" placeholder="متا کیوردز خود را وارد کنید">
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">متا دیسکریپشن</label>
                        <input type="text" name="seo_desc" onkeyup="countChar(this,165,'seo_desc')" onchange="countChar(this,'seo_desc')" value="<?php echo $seo_desc; ?>" class="form-control" placeholder="متا دیسکریپشن خود را وارد کنید">
                        <div id="seo_desc" style="position: absolute;top: 5px;left: 10px;"><span><?php echo strlen($seo_desc,); ?></span>/<span>165</span></div>
                    </div>
                    <div>
                        <label class="form-label">Canonical</label>
                        <input type="text" name="canonical" value="<?php echo $canonical; ?>" class="form-control" placeholder="لینک canonical خود را وارد کنید">
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-12 col-lg-5 order-3 order-md-2">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">عملیات</h5>
                <div class="form-check mr-3">
                <input class="form-check-input" type="checkbox" value="noindex,nofollow" name="noindex" <?php if ($noindex == "noindex,nofollow") echo "checked"; ?>>
                    <label class="form-check-label"> NoIndex</label>
                </div>
            </div>

            <div class="card-body d-flex justify-content-around p-3 pt-0">
                <button type="button" onclick="returnHtml()" name="btnsubmit" class="btn btn-sm btn-success p-2"><i class="fa-regular fa-pen-to-square"></i> انتشار</button>
                <a href="/<?php echo $guid ?>" class="btn btn-sm btn-info p-2"><i class="fa-regular fa-eye"></i> پیش نمایش</a>
            </div>
        </div>
    </div>
    <div class="col-12 order-2 order-md-3">
        <div id="gjs">
        </div>
    </div>
    <input type="hidden" id="html" name="post_content" />
</form>
<script src="assets/vendor/libs/grapes/grapes.min.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-blocks-basic.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-component-countdown.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-custom-code.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-parser-postcss.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-plugin-export.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-plugin-forms.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-preset-webpage.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-style-bg.min.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-tabs.min.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-tooltip.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-touch.min.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-tui-image-editor.min.js"></script>
<script src="assets/vendor/libs/grapes/grapesjs-typed.min.js"></script>

<script type="text/javascript">
    var editor = grapesjs.init({
        container: '#gjs',
        projectData: {
            pages: [{
                component: `<?php echo $content ?>`
            }]
        },

        storageManager: false,
        plugins: ['gjs-blocks-basic',
            'grapesjs-plugin-forms',
            'grapesjs-component-countdown',
            'grapesjs-plugin-export',
            'grapesjs-tabs',
            'grapesjs-custom-code',
            'grapesjs-touch',
            'grapesjs-parser-postcss',
            'grapesjs-tooltip',
            'grapesjs-tui-image-editor',
            'grapesjs-typed',
            'grapesjs-style-bg',
            'grapesjs-preset-webpage'
        ]
    });


    function returnHtml() {
        var mjml = editor.getHtml();
        var css = editor.getCss();
        var html = `<style>${css}</style>`;
        html += mjml;
        var inp = document.getElementById("html");
        inp.value = html;
        let form = document.getElementsByTagName('form')[0];
        form.submit();
    }
</script>