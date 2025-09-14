<?php
$page_title = "";
$page_bread_crumb = ["تنظیمات", "صفحه اصلی"];
$page_type = "";
$menu_style = "";
$name_valide = true;
$slug_valid = true;
$parent_valid = true;
$description_valid = true;
$icon_valid = true;
$mega_menu_name = "mega_menu";
if(isset($_GET['mobile']))
    $mega_menu_name = "mobile_mega_menu";
$lang = $_GET['lang'];
if (isset($_POST['submit'])) {
    $mega_menu = $_POST['menujson'];
    $con->query("DELETE FROM `options` WHERE `name` = '$mega_menu_name'");
    $insert_q = "INSERT INTO `options`( `name`, `value`) VALUES ('$mega_menu_name','$mega_menu');";
    $con->query($insert_q);
}
$mega_menu = "[{}]";
if(strlen($functions->get_option($mega_menu_name, $con))>0)
    $mega_menu = $functions->get_option($mega_menu_name, $con);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
<link rel="stylesheet" href="/includes/vendor/css/bootstrap-iconpicker.min.css" />
<div class="page-body">
    <div class="container-fluid">
        <?php include_once('bread.php') ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header p-3">
                        <h5 class="mb-0">مگامنو</h5>
                    </div>
                    <div class="card-body px-3">
                        <ul id="myEditor" class="sortableLists list-group">
                        </ul>
                    </div>
                    <div class="card-footer pt-0">
                        <form accept="" method="post" class="float-right">
                            <button type="submit" name="submit" class="btn btn-success"><i class="fas fa-check-square"></i> ذخیره</button>
                            <input name="menujson" id="out" class="form-control" type="hidden" value='<?php echo $functions->get_option($mega_menu_name, $con) ?>'>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-header p-3 bg-primary text-white">آیتم های مگامنو</div>
                    <div class="card-body p-3 pb-0">
                        <form id="frmEdit" class="form-horizontal">
                            <div class="mb-3">
                                <label class="form-label">متن</label>
                                <input type="text" class="form-control item-menu" name="text" id="text" placeholder="متن خود را وارد کنید">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">لینک</label>
                                <input type="text" class="form-control item-menu" id="href" name="href" placeholder="لینک مقصد را وارد کنید">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">لینک آیکن</label>
                                <input type="text" class="form-control item-menu" id="icon" name="icon" placeholder="لینک آیکن را وارد کنید">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="addstr()" id="btnUpdate" class="btn btn-primary btnOutput" disabled><i class="fas fa-sync-alt"></i> آپدیت</button>
                        <button type="button" onclick="addstr()" id="btnAdd" class="btn btn-success btnOutput"><i class="fas fa-plus"></i> اضافه</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src='assets/vendor/js/jquery-menu-editor.min.js'></script>
<script src="assets/vendor/js/fontawesome5-3-1.min.js"></script>
<script src="assets/vendor/js/bootstrap-iconpicker.min.js"></script>
<script>
    jQuery(document).ready(function() {

        /* =============== DEMO =============== */
        // menu items
        var arrayjson = <?php echo $mega_menu?>;
        // icon picker options
        var iconPickerOptions = {
            searchText: "Buscar...",
            labelHeader: "{0}/{1}"
        };
        // sortable list options
        var sortableListOptions = {
            placeholderCss: {
                'background-color': "#cccccc"
            },
            onChange: function(cEl) {
                var str = editor.getString();
                $("#out").val(str);
            }
        };

        var editor = new MenuEditor('myEditor', {
            listOptions: sortableListOptions,
            iconPicker: iconPickerOptions
        });

        function addstr() {
            var str = editor.getString();
            $("#out").val(str);
        }
        editor.setForm($('#frmEdit'));
        editor.setUpdateButton($('#btnUpdate'));
        if (arrayjson.length > 0) {
            editor.setData(arrayjson);
        }
        $("#btnUpdate").click(function() {
            editor.update();
        });

        $('#btnAdd').click(function() {
            editor.add();
        });

        /* ====================================== */

        /** PAGE ELEMENTS **/
        $('[data-toggle="tooltip"]').tooltip();
        $('.btnOutput').on('click', function() {
            var str = editor.getString();
            $("#out").val(str);
        });
        $.getJSON("https://api.github.com/repos/davicotico/jQuery-Menu-Editor", function(data) {
            $('#btnStars').html(data.stargazers_count);
            $('#btnForks').html(data.forks_count);
        });
    });
</script>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
</script>
<script>
    try {
        fetch(new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", {
            method: 'HEAD',
            mode: 'no-cors'
        })).then(function(response) {
            return true;
        }).catch(function(e) {
            var carbonScript = document.createElement("script");
            carbonScript.src = "//cdn.carbonads.com/carbon.js?serve=CK7DKKQU&placement=wwwjqueryscriptnet";
            carbonScript.id = "_carbonads_js";
            document.getElementById("carbon-block").appendChild(carbonScript);
        });
    } catch (error) {
        console.log(error);
    }
    
</script>