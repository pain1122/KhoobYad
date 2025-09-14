<!DOCTYPE html>
<html lang="fa" style="overflow: hidden;">
<?php
if ($id > 0) {
    $meta_title = $obj->get_seo_title();
    $meta_desc = $obj->get_seo_desc();
    $meta_keywords = $obj->get_seo_keywords();
    $canonical = $obj->get_meta('canonical');
    $index = $obj->get_meta('noindex');
    $type = $obj->get_type();
} else {
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = array();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fa';
}



if (isset($_SESSION['user_info'])) {
    $user_id = $_SESSION['user_info']["uid"];
    $user = new user($user_id);
    $wishlist = $_SESSION['wishlist'];
    $firstname = $user->get_user_meta('firstname');
    $lastname = $user->get_user_meta('lastname');
    $phonenumber = $user->get_user_meta('phone_number');
    $providence = $user->get_user_meta('providence');
    $city = $user->get_user_meta('city');
    $address = $user->get_user_meta('address');
}


// pop-up user info
if (isset($_POST['info'])) {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $providence = $_POST['providence'];
    $city = $_POST['city'];
    $phonenumber = $_SESSION['user_info']["user"];
    $address = $_POST['address'];
    $meta = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'providence' => $providence,
        'city' => $city,
        'phonenumber' => $phonenumber,
        'address' => $address
    ];
    $user->insert_user_meta($meta);
}

// newsletter submit
if (isset($_POST['newsletter'])) {
    $phone = strval($_POST['phone']);
    $user = new user($phone);
    $user_id = $user->get_id();
    $random = rand(1000, 9999);
    $meta = ['code' => $random, 'role' => 'user', 'phone_number' => $phone, 'RSS', '1'];
    $user->insert_user_meta($meta);
}

// bread crumbs
$homepage = "/index.php";
$uri = explode("?", $page_type[0])[0];
$routes = [
    "product",
    "blog",
    "article"
];
if (in_array($uri, $routes)) {
    if ($type == "product") {
        $main_p = $functions->get_language($_SESSION['lang'], 'bread_crumbs_product');
        $main_u = "shop";
        $cat_type = 'product_cat';
        $url = 'product-category';
    } elseif ($type == "blog" || $type == "article") {
        $main_p = $functions->get_language($_SESSION['lang'], 'bread_crumbs_blog');
        $main_u = "category/";
        $url = $cat_type = 'category';
    }
    $categories_q = "SELECT * FROM `tag` `t`
        INNER JOIN `tag_relationships` `tr` ON `t`.`tag_id` = `tr`.`tag_id`
        INNER JOIN `tag_meta` `tm` ON `t`.`tag_id` =`tm`.`tag_id`
        WHERE `tm`.`type` = '$cat_type'
        AND `tr`.`object_id` = " . $post['post_id'];
    $categories = $functions->FetchArray($categories_q);
    $page_bread_crumb = [
        $main_p => $main_u

    ];
    if (is_countable($categories) && count($categories) > 0) {
        foreach ($categories as $category) {
            $page_bread_crumb[$category['name']] = "$url/" . $category['slug'];
        }
    }
}


// search category list
$select_cat = "SELECT * FROM `tag`
INNER JOIN `tag_meta` ON `tag`.`tag_id` = `tag_meta`.`tag_id`
where `tag_meta`.`type` = 'product_cat' and `tag_meta`.`parent` = 0";
$tags = $functions->FetchArray($select_cat);

function show_tags_table(array $tags, $parentId = 0, $functions, &$i, &$m)
{
    $branch = array();
    foreach ($tags as $tag) {
        if ($tag['parent'] == $parentId && ($tag['type'] != 'product_attribute' && $tag['type'] != 'post_category' && $tag['type'] != 'post_tag')) {
            $mr = $i * 25; ?>
            <option value="<?php echo $tag['slug']; ?>"><?php echo $tag['name']; ?></option>
<?php
            $i++;
            $m = $m + 75;
            $children = show_tags_table($tags, $tag['tag_id'], $functions, $i, $m);

            if ($children) {
                $tag['children'] = $children;
            }
            $branch[] = $tag;
        }
    }
    $i--;
    return $branch;
}
$logo = $functions->displayphoto($functions->get_option('site_logo'));
$fav_icon = $functions->displayphoto($functions->get_option('fav_icon'));
?>

<head>
    <meta charset="UTF-8">
    <title><?php echo $meta_title; ?></title>
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords ?>">
    <link rel="canonical" href="<?php echo $canonical;  ?>">
    <meta name="googlebot" content="<?php echo $index;  ?>">
    <meta name="robots" content="<?php echo $index;  ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=10">
    <meta name="distribution" content="global">
    <meta name="language" content="fa">
    <meta name="copyright" content="Powered By https://dabelclick.com/">
    <meta name="author" content="info@dabelclick.com">
    <meta property="og:site_name" content="خوب یاد">
    <meta property="og:type" content="<?php echo $page_type ?>">
    <meta property="og:image" content="<?php echo $fav_icon; ?>">
    <meta property="og:title" content="<?php echo $meta_title ?>">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:description" content="<?php echo $meta_desc ?>">
    <meta property="og:locale" content="fa_IR">
    <meta itemprop="name" content="خوب یاد">
    <meta itemprop="description" content="<?php echo $meta_desc ?>">
    <meta itemprop="image" content="<?php echo $fav_icon; ?>">
    <link rel="icon" href="<?php echo $fav_icon ?>">
    <link rel="apple-touch-icon" href="<?php echo $fav_icon ?>">
    <?php if ($main_page == true) : ?>
        <link rel="stylesheet" type="text/css" href="/themes/aso/includes/assets/css/slick.css" />
        <link rel="stylesheet" type="text/css" href="/themes/aso/includes/assets/css/slick-theme.css" />
        <link rel="stylesheet" href="/themes/aso/includes/assets/css/flipclock.min.css" />
    <?php endif; ?>
    <link rel="stylesheet" href="/themes/aso/includes/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/themes/aso/includes/assets/css/style.css" />
    <script src="/themes/aso/includes/assets/js/jquery.min.js"></script>
    <base href="<?php echo site_url;  ?>">
</head>

<body style="position:relative;" <?php if ($is_product_page == true) : echo 'class="product-page"';
        endif; ?>>
        <div id="loading-screen">
        <style>
            @-webkit-keyframes SPINNER {
                0% {
                    -webkit-transform: translate(-50%, -50%) rotate(0deg);
                }

                100% {
                    -webkit-transform: translate(-50%, -50%) rotate(360deg);
                }
            }

            @-moz-keyframes SPINNER {
                0% {
                    -moz-transform: translate(-50%, -50%) rotate(0deg);
                }

                100% {
                    -moz-transform: translate(-50%, -50%) rotate(360deg);
                }
            }

            @-ms-keyframes SPINNER {
                0% {
                    -ms-transform: translate(-50%, -50%) rotate(0deg);
                }

                100% {
                    -ms-transform: translate(-50%, -50%) rotate(360deg);
                }
            }

            @-o-keyframes SPINNER {
                0% {
                    -o-transform: translate(-50%, -50%) rotate(0deg);
                }

                100% {
                    -o-transform: translate(-50%, -50%) rotate(360deg);
                }
            }

            @keyframes SPINNER {
                0% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }

                100% {
                    transform: translate(-50%, -50%) rotate(360deg);
                }
            }

            @-webkit-keyframes LABEL {
                0% {
                    opacity: 1.0;
                    -webkit-transform: translate(-50%, -50%) scale(1.0);
                }

                5% {
                    opacity: 0.5;
                    -webkit-transform: translate(-50%, -50%) scale(0.5);
                }

                95% {
                    opacity: 0.5;
                    -webkit-transform: translate(-50%, -50%) scale(0.5);
                }

                100% {
                    opacity: 1.0;
                    -webkit-transform: translate(-50%, -50%) scale(1.0);
                }
            }

            @-moz-keyframes LABEL {
                0% {
                    opacity: 1.0;
                    -moz-transform: translate(-50%, -50%) scale(1.0);
                }

                5% {
                    opacity: 0.5;
                    -moz-transform: translate(-50%, -50%) scale(0.5);
                }

                95% {
                    opacity: 0.5;
                    -moz-transform: translate(-50%, -50%) scale(0.5);
                }

                100% {
                    opacity: 1.0;
                    -moz-transform: translate(-50%, -50%) scale(1.0);
                }
            }

            @-ms-keyframes LABEL {
                0% {
                    opacity: 1.0;
                    -ms-transform: translate(-50%, -50%) scale(1.0);
                }

                5% {
                    opacity: 0.5;
                    -ms-transform: translate(-50%, -50%) scale(0.5);
                }

                95% {
                    opacity: 0.5;
                    -ms-transform: translate(-50%, -50%) scale(0.5);
                }

                100% {
                    opacity: 1.0;
                    -ms-transform: translate(-50%, -50%) scale(1.0);
                }
            }

            @-o-keyframes LABEL {
                0% {
                    opacity: 1.0;
                    -o-transform: translate(-50%, -50%) scale(1.0);
                }

                5% {
                    opacity: 0.5;
                    -o-transform: translate(-50%, -50%) scale(0.5);
                }

                95% {
                    opacity: 0.5;
                    -o-transform: translate(-50%, -50%) scale(0.5);
                }

                100% {
                    opacity: 1.0;
                    -o-transform: translate(-50%, -50%) scale(1.0);
                }
            }

            @keyframes LABEL {
                0% {
                    opacity: 1.0;
                    transform: translate(-50%, -50%) scale(1.0);
                }

                5% {
                    opacity: 0.5;
                    transform: translate(-50%, -50%) scale(0.5);
                }

                95% {
                    opacity: 0.5;
                    transform: translate(-50%, -50%) scale(0.5);
                }

                100% {
                    opacity: 1.0;
                    transform: translate(-50%, -50%) scale(1.0);
                }
            }

            #loading-screen {
                position: absolute;
                width: 100vw;
                height: 100vh;
                z-index: 99999;
                background: #feefff;
            }

            .overlay .spinner,
            .overlay .spinner:before,
            .overlay .spinner:after {
                border: 10px solid transparent;
                border-top: 10px solid #494949;
                border-bottom: 10px solid #494949;
                border-radius: 50px;
                position: absolute;
                top: 50%;
                left: 50%;
            }

            .overlay .spinner:before,
            .overlay .spinner:after {
                content: "";
            }

            .overlay .spinner {
                width: 100px;
                height: 100px;
                -webkit-animation: SPINNER 5s linear infinite;
                -moz-animation: SPINNER 5s linear infinite;
                -ms-animation: SPINNER 5s linear infinite;
                -o-animation: SPINNER 5s linear infinite;
                animation: SPINNER 5s linear infinite;
            }

            .overlay .spinner:before {
                width: 80px;
                height: 80px;
                -webkit-animation: SPINNER 10s linear infinite;
                -moz-animation: SPINNER 10s linear infinite;
                -ms-animation: SPINNER 10s linear infinite;
                -o-animation: SPINNER 10s linear infinite;
                animation: SPINNER 10s linear infinite;
            }

            .overlay .spinner:after {
                width: 60px;
                height: 60px;
                -webkit-animation: SPINNER 5s linear infinite;
                -moz-animation: SPINNER 5s linear infinite;
                -ms-animation: SPINNER 5s linear infinite;
                -o-animation: SPINNER 5s linear infinite;
                animation: SPINNER 5s linear infinite;
            }

            .overlay .label {
                color: #494949;
                text-transform: uppercase;
                font-family: sans-serif;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 2px;
                position: absolute;
                top: 50%;
                left: 50%;
                -webkit-animation: LABEL 5s linear infinite;
                -moz-animation: LABEL 5s linear infinite;
                -ms-animation: LABEL 5s linear infinite;
                -o-animation: LABEL 5s linear infinite;
                animation: LABEL 5s linear infinite;
            }
        </style>
        <div class="overlay">
            <div class="spinner"></div>
            <div class="label">Loading</div>
        </div>
    </div>
    <div class="muodal account <?php if (isset($_SESSION['user_info'])) echo 'loged-in'; ?>">
        <div class="login-container">
            <i class="close"></i>
            <img src="<?php echo $logo; ?>" />
            <?php if (!isset($_SESSION['user_info'])) : ?>
                <div id="login_form">
                    <div class="login-header">
                        <p><?php echo $functions->get_language($_SESSION['lang'], 'login-container-header'); ?></p>
                        <p id="login-message"><?php echo $functions->get_language($_SESSION['lang'], 'enter_mobile_number_login'); ?></p>
                    </div>
                    <div>
                        <label class="mb-3" for="phone"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_login_phone_number'); ?></label>
                        <div class="userInput">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                            <input name="phone" type="text" maxlength="1" inputmode="numeric">
                        </div>
                        <button class="disabled" type="button" id="login-code-button"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_login_phone_number_submit'); ?></button>
                    </div>
                    <div id="lcode-container">
                        <label for="code"><?php echo $functions->get_language($_SESSION['lang'], 'login-container-code-label'); ?></label>
                        <div class="userInput">
                            <input type="text" maxlength="1" inputmode="numeric">
                            <input type="text" maxlength="1" inputmode="numeric">
                            <input type="text" maxlength="1" inputmode="numeric">
                            <input type="text" maxlength="1" inputmode="numeric">
                        </div>
                        <button type="button" id="submitCode"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_login_code_submit'); ?></button>
                        <button type="button" onclick="$(this).parent().removeClass('show');" name="submitBack"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_login_code_back'); ?></button>
                    </div>
                </div>
            <?php endif; ?>
            <div <?php if (!isset($_SESSION['user_info'])) echo 'class="d-none"'; ?> id="user_info">
                <div class="login-header">
                    <h2><?php echo $functions->get_language($_SESSION['lang'], 'popup_account_info'); ?></h2>
                </div>
                <form role="form" action="" method="POST">
                    <div class="form-items">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'popup_account_name'); ?></label>
                        <input class="form-control" name="fname" type="text" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'popup_account_name_placeholder'); ?>" value="<?php echo $firstname; ?>">
                    </div>
                    <div class="form-items">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'popup_account_fname'); ?></label>
                        <input class="form-control" name="lname" type="text" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'popup_account_fname_placeholder'); ?>" value="<?php echo $lastname; ?>">
                    </div>
                    <div class="form-items">
                        <label class="form-label"><?php echo $functions->get_language($_SESSION['lang'], 'popup_account_phonenumber'); ?></label>
                        <input class="form-control" id="login-number" onchange="replace_digits(this)" type="text" placeholder="<?php echo $phonenumber; ?>" disabled>
                    </div>
                    <button class="button" type="submit" name="info"><?php echo $functions->get_language($_SESSION['lang'], 'popup_account_submit'); ?></button>
                </form>
                <div class="form-info-pay row justify-content-between">
                    <a class="button" href="/orders"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_panel'); ?></a>
                    <a class="button" href="/logout"><?php echo $functions->get_language($_SESSION['lang'], 'popup_user_logout'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php if ($functions->ismobile()) { ?>
        <div class="mm">
            <div class="mm-wrapper">
                <div class="mm-header">
                    <a href="/"><img src="<?php echo $logo ?>" /></a><i class="close"></i>
                </div>
                <div class="mm-container">
                    <?php $mega_menu = json_decode($functions->get_option('mega_menu'), true);
                    if (is_countable($mega_menu) && count($mega_menu) > 0) : ?>
                        <ul class="mm-menu">
                            <?php foreach ($mega_menu as $menu) : ?>
                                <li class="mm-title">
                                    <span <?php if ($mega_menu[0] == $menu) echo "class='active'"; ?>>
                                        <?php if (strlen($menu['icon']) > 0) { ?>
                                            <img src="<?php echo $menu['icon']; ?>" />
                                        <?php }
                                        echo $menu['text']; ?>
                                    </span>
                                    <?php if ($menu['children']) : $sub_menu = $menu['children']; ?>
                                        <ul class="mm-links <?php if ($mega_menu[0] == $menu) echo "active"; ?>">
                                            <?php foreach ($sub_menu as $sub) : ?>
                                                <li>
                                                    <p>
                                                        <?php if (strlen($sub['icon']) > 0) { ?>
                                                            <img src="<?php echo $sub['icon']; ?>" />
                                                        <?php }
                                                        echo $sub['text']; ?>
                                                        <a href="<?php echo $sub['href']; ?>"><?php echo $functions->get_language($_SESSION['lang'], 'mega_menu_mobile_see_all'); ?></a>
                                                    </p>
                                                    <?php if ($sub['children']) : $ssub_menu = $sub['children']; ?>
                                                        <ul class="mm-submenu">
                                                            <?php foreach ($ssub_menu as $ssub) :?>
                                                                <li>
                                                                    <a href="<?php echo $ssub['href']; ?>">
                                                                        <?php if (strlen($ssub['icon']) > 0) { ?>
                                                                            <img src="<?php echo $ssub['icon']; ?>" />
                                                                        <?php }
                                                                        echo $ssub['text']; ?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="muodal cm">
        <div class="container">
            <a href="/"><img src="<?php echo $logo ?>"></a>
            <div class="cm-contact">
                <p><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_title'); ?></p>
                <p><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_address'); ?><br><span><?php echo $functions->get_option('address'); ?></span></p>
                <p><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_phone1'); ?><br><a href="tel:<?php echo $functions->get_option('phone_number1'); ?>"><?php echo $functions->get_option('phone_number1'); ?></a></p>
                <p><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_phone2'); ?><br><a href="tel:<?php echo $functions->get_option('phone_number2'); ?>"><?php echo $functions->get_option('phone_number2'); ?></a></p>
                <p><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_email'); ?><br><a href="mailto:<?php echo $functions->get_option('email1'); ?>"><?php echo $functions->get_option('email1'); ?></a></p>
            </div>
            <div class="cm-footer">
                <a class="whatsapp" href="<?php echo $functions->get_option('whatsapp'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_whatsapp'); ?></a>
                <a href="tel:<?php echo $functions->get_option('phone_number1'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_phone3'); ?></a>
                <a href="/درباره-ما"><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_about'); ?></a>
                <a class="instagram" href="<?php echo $functions->get_option('instagram'); ?>"><?php echo $functions->get_language($_SESSION['lang'], 'contact_modal_instagram'); ?></a>
            </div>
        </div>
    </div>
    <?php if ($functions->ismobile()) {
        if (strlen($functions->get_option('top_banner_mobile')) > 0)
            echo "<a href='" . $functions->get_option('top_banner_mobile_link') . "' style='position: sticky;top: 19px;z-index: 9;'><img src='" . $functions->displayphoto($functions->get_option('top_banner_mobile')) . "' width='100%'></a>";
    } else {
        if (strlen($functions->get_option('top_banner')) > 0)
            echo "<a href='" . $functions->get_option('top_banner_link') . "' style='position: sticky;top: 19px;z-index: 9;'><img src='" . $functions->displayphoto($functions->get_option('top_banner')) . "' width='100%'></a>";
    }

    ?>
    <header>
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-12 top-header">
                        <div class="right-side">
                            <div class="header-menu d-none d-sm-block d-md-none"></div>
                            <a href="/"><img src="<?php echo $logo ?>" height="94" width="414" /></a>
                        </div>
                        <div class="search-box">
                            <form action="/shop" id="search_form" method="GET" style="display: flex;">
                                <select onchange="
                                    $('#search_form').attr('action','/product-category/'+$(this).val());
                                    new_act = '/product-category/'+$(this).val();
                                    Search(document.getElementById('search-bar-content').value,this.value);
                                    " id="search-filters">
                                    <option disabled selected><?php echo $functions->get_language($_SESSION['lang'], 'search_category_defualt'); ?></option>
                                    <?php
                                    $i = 0;
                                    $m = 0;
                                    if(!empty($tags))
                                        $tree = show_tags_table($tags, 0, $functions, $i, $m);
                                    ?>
                                </select>
                                <input class="search-bar" name="name" id="search-bar-content" type="text" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'search-bar-text'); ?>">
                                <button type="submit" class="submit-search"></button>
                            </form>
                            <div class="search-results">
                                <div class="search-result">
                                    <p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;"><?php echo $functions->get_language($_SESSION['lang'], 'searching'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="left-side d-none d-sm-flex">
                            <div class="header-cart">
                                <div class="wmc-content">

                                    <ul class="wmc-products">

                                        <?php
                                        $count = 0;
                                        if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                            $cart = $_SESSION['cart'];

                                            foreach ($cart as $item_id => $item_count) :
                                                if ($item_id == null)
                                                    break;
                                                $cart = new product($item_id);
                                                $url = $cart->get_url();
                                                $title = $cart->get_title();
                                                $price = $cart->get_price();
                                                $stock_status = $cart->get_status();
                                                $parent = $cart->get_parent();
                                                if ($stock_status == 'outofstock') {
                                                    unset($_SESSION['cart'][$item_id]);
                                                    unset($cart[$item_id]);
                                                    break;
                                                } else {
                                                    $price = $cart->get_price() * $item_count;
                                                    if ($parent > 0) {
                                                        $cart = new product($parent);
                                                        $url = $cart->get_url();
                                                        $thumbnail = $cart->display_post_image();
                                                    } else {
                                                        $thumbnail = $cart->display_post_image();
                                                    }
                                                    $count += $item_count;
                                                }
                                        ?>
                                                <li class="mini-cart-item">
                                                    <div class="wmc-remove">
                                                        <span onclick="removeFromCart(<?php echo $item_id ?>);" class="remove remove_from_cart_button">X</span>
                                                    </div>
                                                    <div class="wmc-image">
                                                        <a href="<?php echo $url; ?>">
                                                            <img width="600" height="600" src="<?php echo $thumbnail ?>" class="img-cart-list" alt="" loading="lazy"> </a>
                                                    </div>
                                                    <div class="wmc-details">
                                                        <a class="wmc-product-title" href="<?php echo $url; ?>">
                                                            <p><?php echo $title ?></p>
                                                        </a>
                                                        <div class="item-detail">
                                                            <span class="count">
                                                                <?php echo $functions->get_language($_SESSION['lang'], 'cart_item_count'); ?>: <?php echo $item_count ?>
                                                            </span>
                                                            <p>
                                                                <span class="wmc-price"><span class="cart-Price-amount amount"><?php echo number_format($price); ?></span><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach;
                                        } else { ?>
                                            <span class="error-text"><?php echo $functions->get_language($_SESSION['lang'], 'empty_cart'); ?></span>
                                        <?php } ?>

                                    </ul>

                                    <div class="wmc-bottom-buttons">
                                        <a href="/cart" class="view-cart-btn"><?php echo $functions->get_language($_SESSION['lang'], 'view_cart'); ?></a>
                                    </div>
                                </div>
                                <?php if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) { ?><span><?php echo $count; ?></span><?php } ?>
                                <i></i>
                            </div>
                            <div class="header-account">
                                <?php if (isset($_SESSION['user_info'])) : ?>
                                    <span><?php echo $phonenumber ?></span>
                                <?php else : ?>
                                    <span><?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-in'); ?> / <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-up'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php $mega_menu = json_decode($functions->get_option('mega_menu'), true);
                        if (is_countable($mega_menu) && count($mega_menu) > 0) : ?>
                            <ul class="mega-menu d-none d-md-flex">
                                <?php foreach ($mega_menu as $menu) : ?>
                                    <li class="mega-title">
                                        <a href="<?php echo $menu['href']; ?>">
                                            <?php if (strlen($menu['icon']) > 0) { ?>
                                                <img src="<?php echo $menu['icon']; ?>" />
                                            <?php }
                                            echo $menu['text']; ?>
                                        </a>
                                        <?php if ($menu['children']) : $sub_menu = $menu['children']; ?>
                                            <ul class="mega-links">
                                                <?php foreach ($sub_menu as $sub) : ?>
                                                    <li>
                                                        <a href="<?php echo $sub['href']; ?>">
                                                            <?php if (strlen($sub['icon']) > 0) { ?>
                                                                <img src="<?php echo $sub['icon']; ?>" />
                                                            <?php }
                                                            echo $sub['text']; ?>
                                                        </a>
                                                        <?php if ($sub['children']) : $sub_menu = $sub['children']; ?>
                                                            <i></i>
                                                            <ul class="mega-links">
                                                                <?php foreach ($sub_menu as $sub) : ?>
                                                                    <li>
                                                                        <a href="<?php echo $sub['href']; ?>">
                                                                            <?php if (strlen($sub['icon']) > 0) { ?>
                                                                                <img src="<?php echo $sub['icon']; ?>" />
                                                                            <?php }
                                                                            echo $sub['text']; ?>
                                                                        </a>
                                                                        <?php if ($sub['children']) : $sub_menu = $sub['children']; ?>
                                                                            <i></i>
                                                                            <ul class="mega-links">
                                                                                <?php foreach ($sub_menu as $sub) : ?>
                                                                                    <li>
                                                                                        <a href="<?php echo $sub['href']; ?>">
                                                                                            <?php if (strlen($sub['icon']) > 0) { ?>
                                                                                                <img src="<?php echo $sub['icon']; ?>" />
                                                                                            <?php }
                                                                                            echo $sub['text']; ?>
                                                                                        </a>
                                                                                        <?php if ($sub['children']) : $sub_menu = $sub['children']; ?>
                                                                                            <i></i>
                                                                                            <ul class="mega-links">
                                                                                                <?php foreach ($sub_menu as $sub) : ?>
                                                                                                    <li>
                                                                                                        <a href="<?php echo $sub['href']; ?>">
                                                                                                            <?php if (strlen($sub['icon']) > 0) { ?>
                                                                                                                <img src="<?php echo $sub['icon']; ?>" />
                                                                                                            <?php }
                                                                                                            echo $sub['text']; ?>
                                                                                                        </a>
                                                                                                    </li>
                                                                                                <?php endforeach; ?>
                                                                                            </ul>
                                                                                        <?php endif; ?>
                                                                                    </li>
                                                                                <?php endforeach; ?>
                                                                            </ul>
                                                                        <?php endif; ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php
                    if (in_array($uri, $routes)) :
                    ?>
                        <div class="col-12 bread-crumbs">
                            <div class="bread-crumbs-container">
                                <?php
                                foreach ($page_bread_crumb as $bread => $value) :
                                ?>
                                    <?php
                                    if (!next($page_bread_crumb) && strlen($bread) > 0) :
                                    ?>
                                        <a href="<?php echo $value ?>"> <?php echo $bread ?> </a>
                                    <?php

                                    elseif (strlen($bread) > 0) :
                                    ?>
                                        <a href="<?php echo $value ?>"> <?php echo $bread ?> </a>
                                <?php
                                    endif;

                                endforeach;
                                ?>
                                <span><?php if ($tag_type['name']) {
                                            echo $tag_type['name'];
                                        } else {
                                            echo $post['post_title'];
                                        } ?></span>
                            </div>
                            <div class="share">
                                <span class="share"><?php echo $functions->get_language($_SESSION['lang'], 'share'); ?></span>
                                <div class="socials">
                                    <a href="https://api.whatsapp.com/send?text=<?php echo site_url . $_SERVER['REQUEST_URI'] ?>" data-action="share/whatsapp/share" class="whatsapp"></a>
                                    <!-- <a href="https://telegram.me/share/url?url=<?php echo $_SERVER['REQUEST_URI'] ?>" class="telegram"></a> -->
                                </div>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>