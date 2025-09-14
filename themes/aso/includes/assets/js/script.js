$(document).ready(function() {
    $('.nav .toggle').click(function() {
        $('.nav ul').toggleClass('active');
    });
    $('.mm-menu>li>span').click(function() {
        if (!$(this).hasClass('active')) {
            $('.mm-links').removeClass('active');
            $('.mm-menu>li>span').removeClass('active');
            $(this).addClass('active');
            $(this).next().addClass('active');
        }
    })
    $('.brand-slider').owlCarousel({
        rtl: true,
        margin: 60,
        lazyLoad: true,
        nav: true,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        responsive: {
            0: {
                items: 2
            },
            420: {
                items: 3
            },
            768: {
                items: 5
            },
            991: {
                items: 7
            }
        }
    });

    $('.whishlist-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        stagePadding: 2,
        nav: false,
        loop: true,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1.5
            },
            768: {
                items: 1.3
            },
            991: {
                items: 1
            },
            1400: {
                items: 1.5
            }
        }
    });

    $('.product-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        stagePadding: 2,
        nav: false,
        loop: true,
        dots: false,
        responsive: {
            0: {
                items: 1.8
            },
            420: {
                items: 1.5
            },
            576: {
                items: 2.1
            },
            768: {
                items: 2.8
            },
            991: {
                items: 3.3
            },
            1200: {
                items: 5
            }
        }
    });

    $('.recomanded-product-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        stagePadding: 2,
        nav: false,
        loop: true,
        dots: false,
        responsive: {
            0: {
                items: 1.7
            },
            576: {
                items: 2
            }
        }
    });

    $('.banner-slider2.banner-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        // nav: true,
        // navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: true,
        responsive: {
            0: {
                items: 2
            },
            768: {
                items: 3
            }
        }
    });

    $('.banner-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        // nav: true,
        // navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            }
        }
    });

    $('.blog-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        loop: true,
        margin: 10,
        stagePadding: 5,
        nav: false,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        responsive: {
            0: {
                items: 2
            },
            420: {
                items: 2
            },
            576: {
                items: 3
            },
            768: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });

    $('.simblog-slider').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        stagePadding: 5,
        nav: true,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            420: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 1
            },
            1200: {
                items: 1
            }
        }
    });

    $('.art-wishlist').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 20,
        nav: true,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            991: {
                items: 3
            }
        }
    });

    $('.special-offer').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        loop: true,
        stagePadding: 2,
        nav: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        // navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: false,
        autoPlay: true,
        responsive: {
            0: {
                items: 1.8
            },
            420: {
                items: 1.5
            },
            576: {
                items: 2.1
            },
            768: {
                items: 2.8
            },
            991: {
                items: 3.3
            },
            1200: {
                items: 4
            },
            1400: {
                items: 5
            }
        }
    });

    $('.categories.owl-carousel').owlCarousel({
        rtl: true,
        lazyLoad: true,
        margin: 10,
        nav: true,
        navText: ["<i class='icon-1'></i>", "<i class='icon-3'></i>"],
        dots: true,
        responsive: {
            0: {
                items: 2
            },
            576: {
                items: 3
            },
            768: {
                items: 4
            },
            991: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });

    $('.days-form.owl-carousel').owlCarousel({
        rtl: true,
        margin: 10,
        stagePadding: 5,
        nav: false,
        dots: true,
        loop: false,
        responsive: {
            0: {
                items: 3
            },
            768: {
                items: 4,
                loop: false,
                stagePadding: 0,
            },
            991: {
                items: 5
            },
            1200: {
                items: 7
            }
        }
    });

    $('.po-tabs > span').click(function() {
        var t = $(this).attr('id');
        if (!$(this).hasClass('active')) {
            $('.po-tabs > span').removeClass('active');
            $(this).addClass('active');
            $('.tab-slider-holder').removeClass('active');
            $('#' + t + '-slider').addClass('active');
        }
    });

    $('.header-account').click(function() {
        $('.cm').removeClass('open');
        $('.account').addClass('open');
    });
    $('#mb-login').click(function() {
        $('.cm').removeClass('open');
        $('.account').addClass('open');
    });
    $('.cart-login').click(function() {
        $('.account').addClass('open');
    });
    $('.account').click(function(e) {
        if (!e.target.className == "login-container" || !$(e.target).parents(".account").length || e.target.className == "close") {
            $(this).removeClass('open');
        }
    });

    $('.mb-contact').click(function() {
        $('.account').removeClass('open');
        $('.cm').addClass('open');
    });
    $('.cm').click(function(e) {
        if (!e.target.className == "container" || !$(e.target).parents(".cm").length) {
            $(this).removeClass('open');
        }
    });

    $('.filter-show').click(function() {
        $('#sidebar').addClass('open');
    });
    $('#sidebar').click(function(e) {
        if (!e.target.className == ".sidebar" || !$(e.target).parents("#sidebar").length || e.target.className == "close") {
            $(this).removeClass('open');
        }
    });

    $('.header-menu').click(function() {
        $('.mm').addClass('open');
    });
    $('#mb-cat').click(function() {
        $('.mm').addClass('open');
    });
    $('.mm').click(function(e) {
        if (!e.target.className == "mm-wrapper" || !$(e.target).parents(".mm").length) {
            $(this).removeClass('open');
        }
    });

    $('.mm-header .close').click(function() {
        $('.mm').removeClass('open');
    });

    $('.header-cart').click(function() {
        if ($(window).width() <= 420) {
            $('.mc').addClass('open');
        }
    });
    $('.mc').click(function(e) {
        if (!e.target.className == "mc-wrapper" || !$(e.target).parents(".mc").length) {
            $(this).removeClass('open');
        }
    });

    $('.mc-header .close').click(function() {
        $('.mc').removeClass('open');
    });

    $('.login-container input[type="password"]').keyup(function() {
        if ($(this).val().length == 0) {
            $('.login-container button[name="submitCode"]').attr("disabled", true);
        } else {
            $('.login-container button[name="submitCode"]').removeAttr("disabled");
        }
    });
    $('.login-container input[type="number"]').keydown(function() {
        if ($(this).val().length == 0) {
            $('.login-container button[name="submit"]').attr("disabled", true);
        } else {
            $('.login-container button[name="submit"]').removeAttr("disabled");
        }
    });
    $('.login-container input[type="password"]').keydown(function() {
        if ($(this).val().length == 0) {
            $('.login-container button[name="submitCode"]').attr("disabled", true);
        } else {
            $('.login-container button[name="submitCode"]').removeAttr("disabled");
        }
    });

    $('span.share').click(function() {
        $(this).next().slideToggle();
    })


    $('.mm-container ul li>i').click(function() {
        $(this).next().addClass('open');
    })

    $('.sub-cat-header i').click(function() {
        $(this).closest('ul[class^="sub-cat"]').removeClass('open');
    })

    $('.widget-title').click(function() {
        if (!$(this).parent().hasClass('opened')) {
            $('.widget-side .attr').slideUp();
            $('.widget-side.attribut').removeClass('opened');
            $(this).next().slideToggle();
            $(this).parent().toggleClass('opened');
        }
    })

    $('.prperties-header > span').click(function() {
        var t = $(this).attr('id');
        // alert('clicked');
        if (!$(this).hasClass('active')) {
            $('.prperties-header > span').removeClass('active');
            $(this).addClass('active');
            $('.properties-wrapper .content-box').fadeOut(0);
            $('#' + t + 'c').fadeIn(0);
        }
    });
    $('.show-factor').click(function() {
        var t = $(this).attr('id');
        $('#' + t + 'f').toggleClass('fade');
    });

    $('.panel-nav > span').click(function() {
        var t = $(this).attr('id');
        if (!$(this).hasClass('active')) {
            $('.panel-nav > span').removeClass('active');
            $(this).addClass('active');
            $('.orders .col-sm-8>div').fadeOut(0);
            $('#' + t + '-sec').fadeIn(0);
        }
    });

    $(window).scroll(function() {
        if ($(window).width() < 421) {
            var bottom = '52px';
        } else {
            var bottom = '94px';
        }
        if ($(this).scrollTop() > 40) {
            if ($(window).width() < 421) {
                var bottom = '52px';
            } else {
                var bottom = '94px';
            }
            $('.pro-count form>.button').css('bottom', bottom);
        } else {
            $('.pro-count form>.button').css('bottom', '-10px');
        }
    });

    $('.theme-form .modal').click(function(e) {
        if (!e.target.className == ".modal-content" || !$(e.target).parents(".theme-form .modal").length || e.target.className == "close") {
            $(this).addClass('fade');
        }
    });
    $('.pc-header .heart').click(function() {
        if ($(this).hasClass('wished')) {
            $(this).removeClass('wished');
        } else {
            $(this).addClass('wished');
        }
    });
    // Search
    // $('.search-bar-content-desktop input[type="text"]').keydown(function() {
    //     if ($(this).val().length == 0) {
    //         $('.search-results').slideUp();
    //     } else {
    //         $('.search-results').slideDown();
    //     }
    // });

    // $('.search-bar-content input[type="text"]').keydown(function() {
    //     if ($(this).val().length == 0) {
    //         $('.search-results').slideUp();
    //     } else {
    //         $('.search-results').slideDown();
    //     }
    // });

    $('body').click(function(e) {
        if (!e.target.className == "search-results" || !$(e.target).parents(".search-box").length) {
            $('.search-results').slideUp();
        }
    });

    $('#search-bar-content').click(function() {
        if ($(this).val()) {
            $('.search-results').slideDown();
        }
    });

    $('#search-bar-content').keyup(function() {
        $('.search-results').html('<div class="search-result"><p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">در حال جست و جو</p></div>');
        if ($(window).width() < 576) {
            if (!$(this).val()) {
                $('.right-side').fadeIn();
            } else {
                $('.right-side').fadeOut();
            }
        }
    });

    $('#search-bar-content-desktop').keyup(function() {
        $('.search-results').html('<div class="search-result"><p style="color: var(--color2);font-weight: bold;text-align: center; width: 100%;">در حال جست و جو</p></div>');
    });


    $('.blog-details .heart').click(function() {
        $a = parseInt($('.like-text', this).html());
        if ($(this).hasClass('liked')) {
            $a -= 1;
            $(this).removeClass('liked');
            $('.like-text', this).html($a);
        } else {
            $a += 1;
            $(this).addClass('liked');
            $('.like-text', this).html($a);
        }
    })

    $('.list-group.accordion > .d-flex').click(function() {
        $(this).parent().toggleClass('open');
        $(this).next().slideToggle();
    })

    $('.custom-option-content input').on('click', function() {
        if (!$(this).parent().parent().hasClass('checked')) {
            $('.custom-option').removeClass('checked')
            $(this).parent().parent().addClass('checked');
        }
    })

    var sync1 = $("#sync1");
    var sync2 = $("#sync2");
    var thumbs = 3;
    var duration = 400;
    var flag;
    if (sync1.length > 0) {
        sync1.on('click', '.owl-next', function() {
            sync2.trigger('next.owl.carousel')
        });

        sync1.on('click', '.owl-prev', function() {
            sync2.trigger('prev.owl.carousel')
        });

        sync1.owlCarousel({
                loop: false,
                lazyLoad: true,
                rtl: true,
                items: 1,
                margin: 0,
                nav: false,
                dots: true,
                responsive: {
                    768: {
                        dots: false,
                    }
                }
            })
            .on('dragged.owl.carousel', function(e) {
                if (e.relatedTarget.state.direction == 'left') {
                    sync2.trigger('next.owl.carousel')
                } else {
                    sync2.trigger('prev.owl.carousel')
                }
            });
        lightGallery(document.getElementById('sync1'), {
            thumbnail: true,
            selector: ".gallery-item",
            zoom: true,
        });
        sync2.owlCarousel({
                loop: false,
                dots: false,
                lazyLoad: true,
                rtl: true,
                items: thumbs,
                margin: 20,
                nav: true,
                navText: ["&lsaquo;", "&rsaquo;"]
            })
            .on('click', '.owl-item', function() {
                var i = $(this).index();
                sync2.trigger('to.owl.carousel', [i, duration, true]);
                sync1.trigger('to.owl.carousel', [i, duration, true]);
                $("#sync2 .owl-item").removeClass('activated');
                $(this).addClass('activated');
            })
        lightGallery(document.getElementById('sync1'), {
            thumbnail: true,
            selector: ".gallery-item",
            zoom: true,
        });
    }
});

function openCategory(evt, cityName) {
    // Declare all variables
    var i, tabcontents, tablinks;

    // Get all elements with class="tabcontents" and hide them
    tabcontents = document.getElementsByClassName("tabcontents");
    for (i = 0; i < tabcontents.length; i++) {
        tabcontents[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

function showsearch(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablink;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablink" and remove the class "active"
    tablink = document.getElementsByClassName("tablink");
    for (i = 0; i < tablink.length; i++) {
        tablink[i].className = tablink[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}
// for (i = 0; i < tablink.length; i++) {
//     tablink[i].className = tablink[i].className.replace(" active", "");
// }

// Show the current tab, and add an "active" class to the button that opened the tab
// document.getElementById(cityName).style.display = "block";
// evt.currentTarget.className += " active";