<a href="<?php echo $functions->get_option('whatsapp'); ?>" style="position: fixed; bottom: 140px; right: 5px; cursor: pointer; z-index: 4;">
    <svg width="45px" height="45px" viewBox="0 0 1219.547 1225.016">
        <path fill="#E0E0E0" d="M1041.858 178.02C927.206 63.289 774.753.07 612.325 0 277.617 0 5.232 272.298 5.098 606.991c-.039 106.986 27.915 211.42 81.048 303.476L0 1225.016l321.898-84.406c88.689 48.368 188.547 73.855 290.166 73.896h.258.003c334.654 0 607.08-272.346 607.222-607.023.056-162.208-63.052-314.724-177.689-429.463zm-429.533 933.963h-.197c-90.578-.048-179.402-24.366-256.878-70.339l-18.438-10.93-191.021 50.083 51-186.176-12.013-19.087c-50.525-80.336-77.198-173.175-77.16-268.504.111-278.186 226.507-504.503 504.898-504.503 134.812.056 261.519 52.604 356.814 147.965 95.289 95.36 147.728 222.128 147.688 356.948-.118 278.195-226.522 504.543-504.693 504.543z"></path>
        <linearGradient id="htwaicona-chat-s4" gradientUnits="userSpaceOnUse" x1="609.77" y1="1190.114" x2="609.77" y2="21.084">
            <stop offset="0" stop-color="#20b038"></stop>
            <stop offset="1" stop-color="#60d66a"></stop>
        </linearGradient>
        <path fill="url(#htwaicona-chat-s4)" d="M27.875 1190.114l82.211-300.18c-50.719-87.852-77.391-187.523-77.359-289.602.133-319.398 260.078-579.25 579.469-579.25 155.016.07 300.508 60.398 409.898 169.891 109.414 109.492 169.633 255.031 169.57 409.812-.133 319.406-260.094 579.281-579.445 579.281-.023 0 .016 0 0 0h-.258c-96.977-.031-192.266-24.375-276.898-70.5l-307.188 80.548z"></path>
        <image overflow="visible" opacity=".08" width="682" height="639" transform="translate(270.984 291.372)"></image>
        <path fill-rule="evenodd" clip-rule="evenodd" fill="#FFF" d="M462.273 349.294c-11.234-24.977-23.062-25.477-33.75-25.914-8.742-.375-18.75-.352-28.742-.352-10 0-26.25 3.758-39.992 18.766-13.75 15.008-52.5 51.289-52.5 125.078 0 73.797 53.75 145.102 61.242 155.117 7.5 10 103.758 166.266 256.203 226.383 126.695 49.961 152.477 40.023 179.977 37.523s88.734-36.273 101.234-71.297c12.5-35.016 12.5-65.031 8.75-71.305-3.75-6.25-13.75-10-28.75-17.5s-88.734-43.789-102.484-48.789-23.75-7.5-33.75 7.516c-10 15-38.727 48.773-47.477 58.773-8.75 10.023-17.5 11.273-32.5 3.773-15-7.523-63.305-23.344-120.609-74.438-44.586-39.75-74.688-88.844-83.438-103.859-8.75-15-.938-23.125 6.586-30.602 6.734-6.719 15-17.508 22.5-26.266 7.484-8.758 9.984-15.008 14.984-25.008 5-10.016 2.5-18.773-1.25-26.273s-32.898-81.67-46.234-111.326z"></path>
        <path fill="#FFF" d="M1036.898 176.091C923.562 62.677 772.859.185 612.297.114 281.43.114 12.172 269.286 12.039 600.137 12 705.896 39.633 809.13 92.156 900.13L7 1211.067l318.203-83.438c87.672 47.812 186.383 73.008 286.836 73.047h.255.003c330.812 0 600.109-269.219 600.25-600.055.055-160.343-62.328-311.108-175.649-424.53zm-424.601 923.242h-.195c-89.539-.047-177.344-24.086-253.93-69.531l-18.227-10.805-188.828 49.508 50.414-184.039-11.875-18.867c-49.945-79.414-76.312-171.188-76.273-265.422.109-274.992 223.906-498.711 499.102-498.711 133.266.055 258.516 52 352.719 146.266 94.195 94.266 146.031 219.578 145.992 352.852-.118 274.999-223.923 498.749-498.899 498.749z"></path>
    </svg>
</a>
<?php
$flinks_text1 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text1' ORDER BY `option_id` DESC");
$flinks1 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link1' ORDER BY `option_id` DESC");
$flinks_text2 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text2' ORDER BY `option_id` DESC");
$flinks2 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link2' ORDER BY `option_id` DESC");
$flinks_text3 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text3' ORDER BY `option_id` DESC");
$flinks3 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link3' ORDER BY `option_id` DESC");
$flinks_text4 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link_text4' ORDER BY `option_id` DESC");
$flinks4 = base::FetchArray("SELECT `value` FROM `options` WHERE `name` = 'footer_link4' ORDER BY `option_id` DESC");
?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-6 footer-links">
                <div class="row">
                    <?php if (is_countable($flinks1) && count($flinks1) > 0) : ?>
                        <div class="col-6 col-md-4">
                            <p><?php echo $functions->get_language($_SESSION['lang'],'footer_menu1_title'); ?></p>
                            <ul>
                                <?php for ($i = 0; $i < count($flinks1); $i++) : ?>
                                    <li>
                                        <a href="<?php echo $flinks1[$i]['value']; ?>"><?php echo $flinks_text1[$i]['value']; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if (is_countable($flinks2) && count($flinks2) > 0) : ?>
                        <div class="col-6 col-md-4">
                            <p><?php echo $functions->get_language($_SESSION['lang'],'footer_menu2_title'); ?></p>
                            <ul>
                                <?php for ($i = 0; $i < count($flinks2); $i++) : ?>
                                    <li>
                                        <a href="<?php echo $flinks2[$i]['value']; ?>"><?php echo $flinks_text2[$i]['value']; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if (is_countable($flinks3) && count($flinks3) > 0) : ?>
                        <div class="col-6 col-md-4">
                            <p><?php echo $functions->get_language($_SESSION['lang'],'footer_menu3_title'); ?></p>
                            <ul>
                                <?php for ($i = 0; $i < count($flinks3); $i++) : ?>
                                    <li>
                                        <a href="<?php echo $flinks3[$i]['value']; ?>"><?php echo $flinks_text3[$i]['value']; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-lg-6 footer-newsletter">
                <span><?php echo $functions->get_language($_SESSION['lang'], 'footer-newsletter-title'); ?></span>
                <div class="newsletter">
                    <p><?php echo $functions->get_language($_SESSION['lang'], 'footer-newsletter-text'); ?></p>
                    <form role="form" action="" method="POST">
                        <label for="phone"><input id="phone" name="phone" type="text" placeholder="<?php echo $functions->get_language($_SESSION['lang'], 'footer-newsletter-number-placeholder'); ?>" required></label>
                        <button type="submit" name="newsletter" value="submit"><?php echo $functions->get_language($_SESSION['lang'], 'footer-newsletter-title-btn'); ?></button>
                    </form>
                    <div class="socials mt-4">
                        <a style="color: #4267B2;" href="<?php echo $functions->get_option('telegram'); ?>" class="telegram">تلگرام</a>
                        <a style="color: #3DCC99;" href="<?php echo $functions->get_option('whatsapp'); ?>" class="whatsapp">واتساپ</a>
                        <a style="color: #FF5757;" href="<?php echo $functions->get_option('instagram'); ?>" class="instagram">اینستاگرام</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="footer-contact">
                    <p><a href="#"><?php echo $functions->get_option('address'); ?></a></p>
                    <p><a href="tel:<?php echo $functions->get_option('phone_number1'); ?>"><?php echo $functions->get_option('phone_number1'); ?></a></p>
                    <p><a href="mailto:<?php echo $functions->get_option('email1'); ?>"><?php echo $functions->get_option('email1'); ?></a></p>
                </div>
                <?php if (is_countable($flinks4) && count($flinks4) > 0) : ?>
                    <div class="bold-links">
                        <p><?php echo $functions->get_language($_SESSION['lang'], 'bold-links-title'); ?></p>
                        <ul>
                            <?php for ($i = 0; $i < count($flinks4); $i++) : ?>
                                <li>
                                    <a href="<?php echo $flinks4[$i]['value']; ?>"><?php echo $flinks_text4[$i]['value']; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-lg-6 enamad">
                <div class="row justify-content-between">
                    <div class="col-4"><img class="p-2 p-md-4" src="../../includes/image/enamad.png"></div>
                    <div class="col-4"><a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=298430&amp;Code=dpO0HxEumpi4fLxYwv72"><img referrerpolicy="origin" src="https://Trustseal.eNamad.ir/logo.aspx?id=298430&amp;Code=dpO0HxEumpi4fLxYwv72" alt="" style="cursor:pointer" id="dpO0HxEumpi4fLxYwv72"></a></div>
                    <div class="col-4"><img class="p-2 p-md-4" src="../../includes/image/anjoman.png"></div>
                </div>
            </div>
        </div>
    </div>

    <p style="background-color: #fafafa;margin-top:20px;width:100%;padding:10px;text-align:center;color:#222;"> طراحی ، توسعه و سئو شده توسط <a href="https://dabelclick.com"> شرکت نرم افزاری دبل کلیک </a></p>

</footer>
<?php if ($functions->ismobile()) { ?>
    <div class="mobile-menu d-sm-none">
        <a href="/"><?php echo $functions->get_language($_SESSION['lang'], 'mobile_menu_home'); ?></a>
        <span id="mb-cat"><?php echo $functions->get_language($_SESSION['lang'], 'mobile_menu_categories'); ?></span>
        <a href="/cart" class="f-cart"><?php echo $functions->get_language($_SESSION['lang'], 'mobile_menu_cart'); ?> <?php if (is_countable($_SESSION['cart']) && count($_SESSION['cart']) > 0) { ?><span><?php echo $count; ?></span><?php } ?></a>
        <span class="mb-contact"><?php echo $functions->get_language($_SESSION['lang'], 'mobile_menu_contact'); ?></span>
        <span id="mb-login">
            <?php if (isset($_SESSION['user_info'])) : ?>
                <?php echo $functions->get_language($_SESSION['lang'], 'mobile_menu_user_info'); ?>
            <?php else : ?>
                <?php echo $functions->get_language($_SESSION['lang'], 'header-account-sign-up'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php } ?>



<script src="/themes/aso/includes/assets/js/lazysizes.min.js"></script>
<script src="/themes/aso/includes/assets/js/sweetalert2@10.js"></script>
<!-- <link rel="stylesheet" href="/themes/aso/includes/assets/css/slick.css" />
    <link rel="stylesheet" href="/themes/aso/includes/assets/css/slick-theme.css" /> -->
<link rel="stylesheet" href="/themes/aso/includes/assets/css/owl.carousel.min.css" />
<link rel="stylesheet" href="/themes/aso/includes/assets/css/toast.style.min.css" />
<script src="/themes/aso/includes/assets/js/bootstrap.min.js"></script>
<!-- <script src="/themes/aso/includes/assets/js/slick.min.js" ></script> -->
<script src="/themes/aso/includes/assets/js/owl.carousel.min.js"></script>
<script src="/themes/aso/includes/assets/js/toast.script.js"></script>
<script src="/themes/aso/includes/assets/js/script.js"></script>
<script>
    var getJSON = function(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'json';
        xhr.onload = function() {
            var status = xhr.status;
            if (status === 200) {
                products = [];
                callback(null, xhr.response);
            } else {
                callback(status, xhr.response);
            }
        };

        xhr.send();
    };

    qty = parseInt($('.header-cart>span').html());

    function addToCart(id, num = 1) {
        $(this).addClass('disabled');
        getJSON('/themes/aso/includes/API/v1/addToCart.php?id=' + id + '&number=' + num, function(err, data) {
            if (data['error']) {
                var error = data['error'];
                $.Toast("خطایی رخ داده است!", error, "error", {
                    has_icon: true,
                    has_close_btn: true,
                    stack: true,
                    fullscreen: false,
                    timeout: 3000,
                    sticky: false,
                    has_progress: true,
                    rtl: false,
                });
            } else {
                if (qty) {
                    qty += num;
                    $('.header-cart > span').html(qty);
                    $('.f-cart span').html(qty);
                    $('.wmc-products').append('<li class="mini-cart-item"><div class="wmc-remove"><span onclick="removeFromCart(' + data["post_id"] + ');" class="remove remove_from_cart_button">X</span></div><div class="wmc-image"><a href="' + data["url"] + '"><img loading="lazy" width="600" height="600" src="' + data["thumbnail"] + '" class="img-cart-list" alt="" loading="lazy"></a></div><div class="wmc-details"><a class="wmc-product-title" href="' + data["url"] + '"><p>' + data["post_title"] + '</p></a><div class="item-detail"><span class="count">تعداد: ' + num + '</span><p><span class="wmc-price"><span class="cart-Price-amount amount">' + separate(data["price"]) + '</span><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></span></p></div></div></li>');
                } else {
                    qty = num;
                    $('.header-cart').prepend('<span>' + qty + '</span>');
                    $('.f-cart').append('<span>' + qty + '</span>');
                    $('.wmc-products').html('<li class="mini-cart-item"><div class="wmc-remove"><span onclick="removeFromCart(' + data["post_id"] + '); class="remove remove_from_cart_button">X</span></div><div class="wmc-image"><a href="' + data["url"] + '"><img loading="lazy" width="600" height="600" src="' + data["thumbnail"] + '" class="img-cart-list" alt="" loading="lazy"></a></div><div class="wmc-details"><a class="wmc-product-title" href="' + data["url"] + '"><p>' + data["post_title"] + '</p></a><div class="item-detail"><span class="count">تعداد: ' + num + '</span><p><span class="wmc-price"><span class="cart-Price-amount amount">' + separate(data["price"]) + '</span><span class="Price-currencySymbol"> <?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></span></p></div></div></li>');
                }
                $.Toast("افزودن به سبد خرید", "با موفقیت انجام شد", "success", {
                    has_icon: true,
                    has_close_btn: true,
                    stack: true,
                    fullscreen: false,
                    timeout: 3000,
                    sticky: false,
                    has_progress: true,
                    rtl: false,
                });
            }
        });
        $(this).removeClass('disabled');
    }

    function removeFromCart(id) {
        $(this).parents('.mini-cart-item').remove();
        getJSON('/themes/aso/includes/API/v1/removeFromCart.php?id=' + id, function(err, data) {
            if (qty > 1) {
                qty -= 1;
                $('.header-cart > span').html(qty);
            } else {
                $('.header-cart > span').remove();
                $('.wmc-products').html('<span class="error-text">سبد خرید شما خالی میباشد</span>')
            }
            $.Toast("حذف از سبد خرید", "با موفقیت انجام شد", "success", {
                has_icon: true,
                has_close_btn: true,
                stack: true,
                fullscreen: false,
                timeout: 3000,
                sticky: false,
                has_progress: true,
                rtl: false,
            });
        });
    }

    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 1000; //time in ms (5 seconds)

    $('#search-bar-content').keyup(function() {
        clearTimeout(typingTimer);
        if ($('#search-bar-content').val()) {
            typingTimer = setTimeout(Search, doneTypingInterval);
            $('.search-results').slideDown();
        } else {
            $('.search-results').slideUp();
        }
    });

    function separate(Number) {
        Number += '';
        Number = Number.replace(',', '');
        x = Number.split('.');
        y = x[0];
        z = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(y))
            y = y.replace(rgx, '$1' + ',' + '$2');
        return y + z;
    }
    // Favorites Section
    function addToFavorites(product) {
        getJSON('/themes/aso/includes/API/v1/Favorites.php?id=' + product,
            function(err, data) {
                var heart = document.getElementsByClassName('heart');
            });
    };

    function like(post_id) {
        getJSON('/themes/aso/includes/API/v1/Like.php?pid=' + post_id, function(err, data) {});
    };

    function Search(content = $('#search-bar-content').val(), filter = $('#search-filters').val()) {
        getJSON('/themes/aso/includes/API/v1/Search.php?content=' + content + "&filter=" + filter, function(err, data) {
            $('.search-results').html('');
            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    var stock_status = data[i]["stock_status"];
                    var tag = '';
                    if (stock_status == 'outofstock')
                        tag = '<span class="tag">نا موجود</span>';
                    else if (stock_status == 'call')
                        tag = '<apan class="tag">تماس بگیرید</span>';

                    let sale_price = data[i]["_sale_price"];
                    let regular_price = data[i]["_regular_price"];
                    var price = '<ins>' + separate(parseInt(regular_price)) + ' <span><?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></ins>';
                    if (sale_price == "" || sale_price === undefined) {

                    } else
                        price = '<ins>' + separate(parseInt(sale_price)) + ' <span><?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></ins><del>' + separate(parseInt(regular_price)) + ' <span><?php echo $functions->get_language($_SESSION['lang'], 'currency') ?></span></del>';

                    $('.search-results').append('<a href="' + decodeURIComponent(data[i]['url']) + '"><div class="search-result"><img loading="lazy" src="' + data[i]['img'] + '" width="100" height="100"><div class="thumb-det"><p>' + data[i]["post_title"] + '</p><div class="thumb-price">' + price + '</div>' + tag + '</div></div></a>');
                }
            } else {
                $('.search-results').append('<div class="search-result"><p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">محصولی پیدا نشد</p></div>');
            }
        });
    }

    function get_code(number) {
        getJSON('/themes/aso/includes/API/v1/Send-sms.php?phone=' + number, function(err, data) {
            $('#login-message').text(data);
        });
    }

    function login(number, code) {
        getJSON('/themes/aso/includes/API/v1/Login.php?phone=' + number + '&code=' + code, function(err, data) {
            $('#login-message').text(data['message']);
            if (data['status'] == 200) {
                <?php if ($guid == 'cart') echo 'window.location.reload(true);'; ?>
                $('#user_info input[name="fname"]').val(data['firstname']);
                $('#user_info input[name="lname"]').val(data['lastname']);
                $('#login-number').val(data['phonenumber']);
                $('.header-account span').text(data['phonenumber']);
                $('#mb-login').text('مشخصات');
                $('#user_info input[name="providence"]').val(data['providence']);
                $('#user_info input[name="city"]').val(data['city']);
                $('#user_info input[name="address"]').val(data['address']);
                $('#login_form').fadeOut();
                $('#user_info').fadeIn();
                $('#user_info').removeClass('d-none');
                $('.muodal.account').addClass('loged-in');
            }
        });
    }

    const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))

    function replace_digits(input) {
        var temp_inp = input.value;
        var replaced = p2e(temp_inp);
        input.value = replaced;
    }
    $(document).ready(function() {
        $('.userInput input[name="phone"]').on({
            keyup: function(e) {
                e.preventDefault();
                replace_digits(this);
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
                if ($(this).is(':last-child') && $(this).val() != '') {
                    $('#login-code-button').removeClass('disabled')
                } else {
                    if ((e.which == 8 || e.which == 46))
                        $(this).prev().focus();
                    else
                        $(this).next().focus();
                }
            },
            focus: function() {
                $(this).val('');
            }
        });
        $('#login-code-button').click(function() {
            var phone_inputs = $('.userInput input[name="phone"]');
            var phone = '';
            phone_inputs.each(function() {
                phone += $(this).val();
            });
            if (phone.length < 11) {
                $('#login-message').text('لطفا تمامی فیلد هارو پر کنید');
            } else {
                get_code(phone);
                $('#lcode-container').addClass('show');
                $('#lcode-container input').val('');
            }
        });
        $("#lcode-container input").on({
            paste: function(e) {
                e.preventDefault();
                $('.userInput input[name="phone"]').focus();
                var inputs = $("#lcode-container input");
                var phone_inputs = $('.userInput input[name="phone"]');
                var phone = '';
                phone_inputs.each(function() {
                    phone += $(this).val();
                });
                var code = e.originalEvent.clipboardData.getData('text');
                var i = 0;
                code.split('').forEach(function(c) {
                    $(inputs[i]).val('');
                    $(inputs[i]).val(c);
                    i++;
                });
                if (phone.length < 11) {
                    $('#login-message').text('شماره تلفن اشتباه است');
                } else if (code.length < 4) {
                    $('#login-message').text('کد ورود اشتباه است');
                } else {
                    login(phone, code);
                }
            },
            focus: function() {
                $(this).val('');
            },
            keyup: function(e) {
                e.preventDefault();
                replace_digits(this);
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
                if ($(this).is(':last-child')) {
                    var inputs = $("#lcode-container input");
                    var phone_inputs = $('.userInput input[name="phone"]');
                    var phone = '';
                    var code = '';
                    inputs.each(function() {
                        code += $(this).val();
                    });
                    phone_inputs.each(function() {
                        phone += $(this).val();
                    });
                    if (phone.length < 11) {
                        $('#login-message').text('شماره تلفن اشتباه است');
                    } else if (code.length < 4) {
                        $('#login-message').text('کد ورود اشتباه است');
                    } else {
                        login(phone, code);
                    }
                } else {
                    if ((e.which == 8 || e.which == 46))
                        $(this).prev().focus();
                    else
                        $(this).next().focus();
                }
            }
        });
        $('#submitCode').click(function() {
            var inputs = $("#lcode-container input");
            var phone_inputs = $('.userInput input[name="phone"]');
            var phone = '';
            var code = '';
            inputs.each(function() {
                code += $(this).val();
            });
            phone_inputs.each(function() {
                phone += $(this).val();
            });
            if (phone.length < 11) {
                $('#login-message').text('شماره تلفن اشتباه است');
            } else if (code.length < 4) {
                $('#login-message').text('کد ورود اشتباه است');
            } else {
                login(phone, code);
            }
        });
        $('html').removeAttr("style");
        $('#loading-screen').fadeOut();
    })
</script>

</body>

</html>