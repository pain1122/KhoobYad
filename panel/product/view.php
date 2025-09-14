<?php
if (isset($_GET['id'])) {
    $owned = false;
    $post_id = intval($_GET['id']);
    $obj = new post($post_id);
    $type = $obj->get_type();
    $obj = new product($post_id);
    $obj->set_post_type($type);
    $title = $obj->get_title();
    $guid = urldecode($obj->get_slug());
    $content = $obj->get_content();
    $price = $obj->get_regular_price();
    $sale_price = $obj->get_sale_price();
    $description = $obj->get_meta('description');
    $term_start = $obj->get_meta('term_start');
    $term_end = $obj->get_meta('term_end');
    $stock = $obj->get_meta('_stock');
    $sequential = $obj->get_meta('sequential');
    $master = $obj->get_meta('teacher');
    if (!empty($master)) {
        $master = new user($master);
        $master_id = $master->get_id();
        $master_name = $master->get_display_name();
        $master_pic = $master->get_user_meta('avatar');
    }
    $demo = $obj->get_meta('demo');
    $exam = $obj->get_meta('exam');
    $testbook = $obj->get_meta('testbook');
    $type = $obj->get_type();
    $cateogory_type = str_replace('-', '_', $type) . '_category';
    $tag_type = str_replace('-', '_', $type) . '_tag';
    $tags = $obj->get_taxonomy($tag_type);
    $cats = $obj->get_taxonomy($cateogory_type);
    $thumbnail_src = $obj->get_thumbnail_src();
    $thumbnail_alt = $obj->get_image_alt();
    $mime = end(explode(',', $thumbnail_src));
    $allow_file = ["m4a", "mp4", "wav", "avi", "mov", "mkv", "VCD", "dvd", "ogg", "ogm", "flv"];
    $post_type = $obj->get_type();
    $see_session = '';
    $see_session_id = $_GET['session_id'];
    if($see_session_id > 0)
        $see_session = " AND `post_id` = $see_session_id";
    $sessions = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_type` = 'session' AND `post_parent` = $post_id $see_session");
    if (is_null($user_classes))
        $user_classes = [];
    $lesson_chat = intval("$post_id" . "$uid");
    $chat_id = base::FetchAssoc("SELECT `post_id` FROM `post` WHERE `post_parent` = $lesson_chat AND `post_type` = 'chat'")['post_id'];
    if ($chat_id)
        $chat = new post($chat_id);
    elseif ($master_id > 0) {
        $chat = new post('new_post');
        $chat_id = $chat->get_id();
        $members = [$uid, $master_id];
        $meta['members'] = json_encode($members);
        $chat_title = "رفع اشکال  $title";
        $chat->set_title($chat_title);
        $chat->set_author($master_id);
        $chat->set_post_type('chat');
        $chat->set_status('debug');
        $chat->set_parent($lesson_chat);
        $chat->insert_meta($meta);
        $chat_title = $chat->get_title();
    }
    if ((is_countable($user_classes) && preg_match('/"' . preg_quote($post_id, '/') . '"/i', json_encode($user_classes))) || ($role == 'admin' || $role == 'school')){
        $owned = true;
        echo '1';
    }else if ((in_array($type,['online-class','off-boxes']) && $subscription == 'online-class') || (in_array($type,['class-book','solution-book']) && $subscription == 'class-book') || $subscription == 'all'){
        $owned = true;
        echo '2';
    }else if (array_search($post_id, $user_order_items)){
        $owned = true;
        echo '3';
    }
} else {
    base::redirect('index.php');
}
?>
<script src="assets/vendor/libs/plyr/plyr.js"></script>
<link rel="stylesheet" href="assets/vendor/libs/plyr/plyr.css">
<form action="" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card mb-4 mb-lg-0">
            <div class="card-header pb-0">
                <h2 class="mb-0"><?php echo $title; ?></h2>
            </div>
            <div class="card-body">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5 postion-relative">
        <div class="card" style="position: sticky;top: 70px;">
            <?php if ($demo || $exam || $testbook || $master_id > 0): ?>
                <div class="card-header">
                    <h2 class="mb-0">دسترسی ها</h2>
                    <div class="card-body row align-items-center justify-content-around p-3 pt-0">
                        <?php if ($master_id > 0): ?><span class="btn btn-success p-2 col-12 mt-1"
                                onclick="chat_start(<?php echo $chat_id; ?>)">رفع اشکال با استاد</span><?php endif; ?>
                        <?php if ($demo): ?><a href="<?php echo $demo; ?>" class="btn btn-primary p-2 col-12 mt-1">لینک
                                دمو</a><?php endif; ?>
                        <?php if ($exam): ?><a href="<?php echo $exam; ?>" class="btn btn-primary p-2 col-12  mt-1">لینک
                                آزمون</a><?php endif; ?>
                        <?php if ($testbook): ?><a href="<?php echo $testbook; ?>"
                                class="btn btn-primary p-2 col-12 mt-1 ">لینک تست بوک</a><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card-body">

                <?php

                if (in_array($imageFileType, $allow_file)) {
                    echo "<video id='videoPlayer2' playsinline controls><source src='$thumbnail_src' type='video/mp4'/></video>";
                } else {
                    echo "<img src='$thumbnail_src' id='post_image' width='85%' class='mx-auto d-block mb-3'>";
                } ?>
                <?php if ((is_countable($user_classes) && !preg_match('/"' . preg_quote($post_id, '/') . '"/i', json_encode($user_classes))) && $price > 0) {
                    ?>
                    <form action="" method="post" class="price-box mt-3">
                        <h6>قیمت :</h6>
                        <div>
                            <?php if ($sale_price > 0) { ?>
                                <del><?php echo number_format($obj->get_regular_price()) . ' تومان'; ?></del>
                                <span><?php echo number_format($sale_price) . ' تومان' ?></span>
                            <?php } else { ?>
                                <span><?php echo number_format($price) . ' تومان'; ?></span>
                            <?php } ?>
                        </div>
                        <?php if ($term_end > 0) { ?>
                            <p class="text-start">آخرین مهلت تخفیف : <?php echo jdate('H:s Y/m/j', $date) ?></p>
                        <?php }
                        if (!isset($_SESSION['cart'][$item_id])) { ?>
                            <button class="btn btn-success mt-1 mb-0" value="<?php echo $post_id; ?>" type="submit"
                                name="add-cart-item">افزودن به سبد خرید</button>
                        <?php } ?>
                    </form>
                <?php }
                ?>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">

        <div id="accordionPayment" class="accordion accordion-header-primary">
            <?php
            $sessions_count = 1;
            if (is_countable($sessions) && count($sessions) > 0) {
                foreach ($sessions as $session) {
                    $session_owned = false;
                    if ($sequential == 'true' && !preg_match('/"' . preg_quote($session['post_id'], '/') . '"/i', json_encode($user_classes)) && $session['post_id'] != $sessions[0]['post_id'])
                        break;
                    $session = new post($session['post_id']);
                    $session_id = $session->get_id();
                    $title = $session->get_title();
                    $session->set_post_type('session');
                    if (empty($title))
                        $title = 'جلسه شماره ' . $sessions_count;
                    $link = $session->get_excerpt();
                    $price = $session->get_meta('price');
                    $date = $session->get_meta('date');
                    $videos = explode(',', $session->get_meta('videos'));
                    $files = explode(',', $session->get_meta('files'));
                    
                    if (!$owned) {
                        foreach($user_order_items as $order){
                            if($post_id == $order['item_id']){
                                $session_owned = true;
                                break;
                            }
                        }
                    }else {
                        $session_owned = true;
                    } ?>
                    <div class="card accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                aria-expanded="false" data-bs-target="#accordionPayment-<?php echo $sessions_count; ?>"
                                aria-controls="accordionPayment-<?php echo $sessions_count; ?>">
                                <i class='bx bx-check-circle ml-2'></i><?php echo $title; ?>
                            </button>
                        </h2>

                        <div id="accordionPayment-<?php echo $sessions_count; ?>" class="accordion-collapse collapse">
                            <div class="accordion-body d-flex align-items-center flex-wrap">
                                <?php if ($type != 'online-class') { ?>
                                    <span class="ml-auto mb-2"><i class='bx bxs-timer ml-1'></i>تایم جلسه‌ : <?php echo $date; ?>
                                        دقیقه</span>
                                <?php } else { ?>
                                    <span class="ml-auto mb-2"><i class='bx bxs-time-five ml-1'></i>تاریخ جلسه‌ :
                                        <?php echo $date; ?></span>
                                <?php } ?>
                                <?php if (!empty($price) && $price > 0 && !$session_owned) { ?>
                                    <button class='btn btn-success mt-1 mb-0' value='<?php echo $session_id; ?>' type='submit'
                                        name='add-cart-item'><i
                                            class='fas fa-shopping-cart me-2'></i><?php echo number_format($price); ?>
                                        تومان</button>
                                <?php } elseif($owned == true || $session_owned == true) {
                                    if (is_countable($files) && count($files) > 0) {
                                        foreach ($files as $key => $file) {
                                            if (!empty($file)): ?>
                                                <div class="btn-group ml-2 mb-2" id="dropdown-icon-demo">
                                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm p-2"
                                                        data-bs-toggle="dropdown" aria-expanded="true">
                                                        <i class="bx bx-file"></i> فایل شماره <?php echo intval($key) + 1; ?>
                                                    </button>
                                                    <ul class="dropdown-menu" data-popper-placement="top-end">
                                                        <li>
                                                            <a href="<?php echo $file; ?>" class="dropdown-item d-flex align-items-center"
                                                                download><i class="fa-solid fa-download ml-2"></i>دانلود</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif;
                                        }
                                    }
                                    if (is_countable($videos) && count($videos) > 0) {
                                        foreach ($videos as $key => $video) {
                                            if (!empty($video)):
                                                $video = urldecode($video); ?>
                                                <div class="btn-group ml-2 mb-2" id="dropdown-icon-demo">
                                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm p-2"
                                                        data-bs-toggle="dropdown" aria-expanded="true">
                                                        <i class='bx bx-play-circle'></i> ویدئو شماره <?php echo intval($key) + 1; ?>
                                                    </button>
                                                    <ul class="dropdown-menu" data-popper-placement="top-end">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                                                data-bs-toggle="modal" data-bs-target="#video"
                                                                onclick='loadVideo("<?php echo $video; ?>","accordionPayment-<?php echo $sessions_count; ?>");'><i
                                                                    class="fa-solid fa-eye ml-2"></i>مشاهده</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif;
                                        }
                                    } ?>
                                    <a class="btn btn-primary btn-sm p-2 mr-3 mb-2" href="<?php echo $link; ?>"
                                        data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-color="primary"
                                        title="" data-bs-original-title="لینک جلسه"><i class="bx bx-link"></i></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $sessions_count++;
                }
            } ?>
        </div>
    </div>
</form>
<div class="modal fade" id="video" tabindex="-1" aria-hidden="true" style="background: #000;">
    <div class="modal-dialog modal-xl modal-simple" style="top: 20vh">
        <div class="modal-content p-0 pt-4" style="background:none;box-shadow:none">
            <div class="modal-body p-0 d-flex align-items-center justify-content-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <video controlsList="nodownload" id="videoPlayer" controls width="80%">
                    <source id="videoPlayersrc" src="" type="video/mp4" />
                </video>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($chat_id)) { ?>
    <div class="chat-box ticket" id="chat">
        <div class="chat-container">
            <div class="chat-header">
                <span class="fa fa-close close-chat"></span>
                <p id="chat-title"></p>
                <?php if ($role == 'admin' || $role == 'school' || $uid == $author): ?>
                    <a data-fancybox data-type='iframe' data-preload='false'><i class="fa fa-users ml-2"></i><span
                            id="members"></span></a>
                <?php else: ?>
                    <p class="m-0"><i class="fa fa-users ml-2"></i><span id="members"></span></p>
                <?php endif; ?>
            </div>
            <p class="pinned-message d-none" id="pinned-message"></p>
            <div class="chat-content">

            </div>
            <div class="chat-footer">
                <input type="text" placeholder="پیام خود را بنویسید ..." id="message" name="chat">
                <label class="image-icon"><input type="file" accept="image/*"
                        onchange="loadFile(this,event);$('#chat .photo-preveiw').addClass('open');" id="chat-pic"
                        name="pic"></label>
                <label class="file-icon"><input type="file" accept="*"
                        onchange="getFileExt(event,'ext');$('#chat .photo-preveiw').addClass('open file');" id="chat-file"
                        name="pic"></label>
                <button name="submit-chat" id="submit-chat">ارسال</button>
            </div>
        </div>
        <div class="photo-preveiw">
            <div class="container">
                <div class="photo-wrapper">
                    <i class="close" onclick="$('.photo-preveiw').removeClass('open');"></i>
                    <span id='ext'></span>
                    <img src="" id="pic">
                    <input type="text" placeholder="پیام خود را بنویسید ..." id="title">
                    <button id="submit-pic">ارسال</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    var ids = [];
    $('.chat-box').click(function (e) {
        if (!$(e.target).parents(".chat-box").length || $(e.target).hasClass("close-chat")) {
            $(this).removeClass('open');
        }
    });
    $('.photo-preveiw').click(function (e) {
        if (!$(e.target).parents(".photo-wrapper").length || $(e.target).hasClass("close-preveiw")) {
            $(this).removeClass('open');
            $(this).removeClass('file');
            $('#chat-pic').val('');
            $('#chat-file').val('');
            $('.photo-wrapper img').attr('src', '');
            $('.photo-wrapper span').html('');
            $('.photo-wrapper input').val('');

        }
    });

    function chat_start(post_id) {
        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + post_id, function (err, data) {
            ids = [];
            var title = data['title'];
            var members = data['members'];
            $('#chat .chat-content').html('');
            $('#submit-pic').attr('onclick', 'message(' + post_id + ')');
            $('#submit-chat').attr('onclick', 'message(' + post_id + ')');
            $('#chat-title').text(title);
            var pinned_message = data['pinned_message'];
            $('#pinned-message').addClass('d-none');
            if (pinned_message && pinned_message.length > 0) {
                $('#pinned-message').text(pinned_message);
                $('#pinned-message').removeClass('d-none');
            }
            $('#members').text(members);
            var messages = data['tickets'];
            var message_count = 0;
            if (messages)
                message_count = messages.length;
            <?php if ($role == 'admin' || $role == 'school' || $uid == $author): ?>
                $('#members').parent().attr('href', '?page=counsel/submit-ticket.php&chat_id=' + post_id);
            <?php endif; ?>
            for (var i = 0; i < message_count; i++) {
                if (!ids.includes(messages[i]["post_id"])) {
                    if (messages[i]["mime_type"] == 'text') {
                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                    } else {
                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                    }
                    ids.push(messages[i]["post_id"]);
                }
            }
            $('#chat').addClass('open');
            $('#chat .chat-content').delay(1000).animate({
                scrollTop: $('#chat .chat-content').prop("scrollHeight")
            }, 1000);
            setInterval(get_message, 1000, post_id);
        });
    };

    function get_message(id) {
        if ($('.chat-box').hasClass('open')) {
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                var messages = data['tickets'];
                var message_count = 0;
                if (messages)
                    message_count = messages.length;
                for (var i = 0; i < message_count; i++) {
                    if (!ids.includes(messages[i]["post_id"])) {
                        if (messages[i]["mime_type"] == 'text') {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                        } else {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                        }
                        ids.push(messages[i]["post_id"]);
                        $('#chat .chat-content').animate({
                            scrollTop: $('#chat .chat-content').prop("scrollHeight")
                        }, 1000);
                    }
                }

            });
        }
    }

    function message(id) {
        var text = $('#chat #message').val();
        var image = $('#chat #chat-pic').val();
        var file = $('#chat #chat-file').val();
        $('#chat button').attr('disabled', true);
        $('#chat .photo-wrapper button').attr('disabled', true);
        if (image != '' || file != '') {
            $('.loading').addClass('open');
            var title = $('#chat #title').val();
            if (image != '')
                var file_data = $('#chat #chat-pic').prop('files')[0];
            else
                var file_data = $('#chat #chat-file').prop('files')[0];

            var form_data = new FormData();
            form_data.append('file', file_data);
            $.ajax({
                url: "upload.php",
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    error = ['file too large', 'upload failed!'];
                    image = data;
                    if (!error.includes(image)) {
                        getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&image=' + image + '&title=' + title, function (err, data) { });
                        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                            var messages = data['tickets'];
                            var message_count = 0;
                            if (messages)
                                message_count = messages.length;
                            for (var i = 0; i < message_count; i++) {
                                if (!ids.includes(messages[i]["post_id"])) {
                                    if (messages[i]["mime_type"] == 'text') {
                                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                                    } else {
                                        $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                                    }
                                    ids.push(messages[i]["post_id"]);
                                }
                            }
                        });

                        $('#chat .photo-preveiw').removeClass('open');
                        $('#chat .photo-preveiw').removeClass('file');
                        $('#chat .chat-footer input').val('');
                        $('#chat .photo-wrapper input').val('');
                        $('#chat .photo-wrapper span').html('');
                        $('#chat .photo-wrapper img').attr('src', '');
                        $('#chat.chat-content').animate({
                            scrollTop: $('#chat .chat-content').prop("scrollHeight")
                        }, 1000);
                        $('.loading').removeClass('open');
                    } else {
                        Swal.fire(
                            'خطا',
                            image,
                            'error'
                        );
                        $('.loading').removeClass('open');
                    }
                }
            });
            setTimeout(() => {
                $('#chat button').attr('disabled', false);
                $('#chat .photo-wrapper button').attr('disabled', false);
            }, 500);
        } else {
            getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&text=' + text, function (err, data) { });
            $('#chat .chat-footer input').val('');
            $('#chat .photo-wrapper input').val('');
            $('#chat .photo-wrapper img').attr('src', '');
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function (err, data) {
                var messages = data['tickets'];
                var message_count = 0;
                if (messages)
                    message_count = messages.length;
                for (var i = 0; i < message_count; i++) {
                    if (!ids.includes(messages[i]["post_id"])) {
                        if (messages[i]["mime_type"] == 'text') {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["post_content"] + "</p></div>");
                        } else {
                            $('#chat .chat-content').append("<div class=\"message-" + messages[i]["role"] + "\"><p class=\"box-header\"><span>" + messages[i]["username"] + "</span><span>" + messages[i]["date"] + "</span></p><p class=\"box-text\">" + messages[i]["photo"] + messages[i]["post_title"] + "</p></div>");
                        }
                        ids.push(messages[i]["post_id"]);
                    }
                }
            });
            $('#chat .chat-content').animate({
                scrollTop: $('#chat .chat-content').prop("scrollHeight")
            }, 1000);
        }
        $('#chat button').attr('disabled', false);
        $('#chat .photo-wrapper button').attr('disabled', false);
    };
    $('.modal .btn-close').click(function () {
        $(this).parent().find('video').get(0).pause();
    })
    // const player = new Plyr('#videoPlayer');

    var options = { fluid: true };
    // var player = videojs('videoPlayer', options, function onPlayerReady() {
    //     this.play();
    // });
    const player2 = new Plyr('#videoPlayer2');
    const src = document.getElementById("videoPlayersrc");
    const player = document.getElementById("videoPlayer");

    function loadVideo(file, id) {
        player.pause();
        src.setAttribute("src", file);
        player.load();
        showNextSession(id);
    }

    function showNextSession(id) {
        if ($('#' + id).parents('.accordion-item').is(':last-child')) {
            var sessions_count = $('#accordionPayment >div').length;
            getJSON('API/v1/GetSessions.php?lid=<?php echo $post_id; ?>&count=' + sessions_count, function (err, data) {
                if (data['data'].length > 0) {
                    $('#accordionPayment' + sessions_count + ' .accordion-button').prepend("<i class='bx bx-check-circle ml-2'></i>");
                    sessions_count += 1;
                    data = data['data'];
                    var id = data[0]['id'];
                    var title = data[0]['title'];
                    if (!title)
                        title = 'جلسه شماره ' + (sessions_count);
                    var date = data[0]['date'];
                    var files = data[0]['files'];
                    var price = data[0]['price'];
                    var owned = data[0]['owned'];
                    var videos = data[0]['videos'];
                    var link = data[0]['link'];
                    var files_html = videos_html = cart_button = '';
                    if (files.length > 0) {
                        for (var i = 1; i < files.length + 1; i++) {
                            files_html += '<div class="btn-group ml-2 mb-2" id="dropdown-icon-demo"><button type="button" class="btn btn-primary dropdown-toggle btn-sm p-2" data-bs-toggle="dropdown" aria-expanded="true"><i class="bx bx-file"></i> فایل شماره ' + i + '</button><ul class="dropdown-menu" data-popper-placement="top-end"><li><a href="' + files[i] + '" class="dropdown-item d-flex align-items-center" download><i class="fa-solid fa-download ml-2"></i>دانلود</a></li></ul></div>';
                        }
                    }
                    if (videos.length > 0) {
                        for (var i = 1; i < videos.length + 1; i++) {
                            videos_html += '<div class="btn-group ml-2 mb-2" id="dropdown-icon-demo"><button type="button" class="btn btn-primary dropdown-toggle btn-sm p-2" data-bs-toggle="dropdown" aria-expanded="true"><i class="bx bx-play-circle"></i> ویدئو شماره ' + i + '</button><ul class="dropdown-menu" data-popper-placement="top-end"><li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#video" onclick="loadVideo(\'' + video[i] + '\',\'accordionPayment-' + sessions_count + '\');"><i class="fa-solid fa-eye ml-2"></i>مشاهده</a></li></ul></div>';
                        }
                    }
                    if (parseInt(price) > 0 && owned === 'false') {
                        const formattedPrice = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(price);
                        cart_button = "<button class='btn btn-success mt-1 mb-0' value='" + id + "' type='submit' name='add-cart-item'><i class='fas fa-shopping-cart me-2'></i>" + formattedPrice + " تومان</button>"
                    }
                    var session_item = '<div class="card accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#accordionPayment-' + sessions_count + '" aria-controls="accordionPayment-' + sessions_count + '">' + title + '</button></h2><div id="accordionPayment-' + sessions_count + '" class="accordion-collapse collapse"><div class="accordion-body d-flex align-items-center flex-wrap"><span class="ml-auto mb-2"><?php if ($type != 'online-class')
                        echo '<i class="bx bxs-timer ml-1"></i>تایم جلسه‌ : ';
                    else
                        echo '<i class="bx bxs-time-five ml-1"></i>تاریخ جلسه‌ : '; ?>' + date + '</span>' + files_html + videos_html + '<a class="btn btn-primary btn-sm p-2 mr-3 mb-2" href="' + link + '" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-color="primary" title="" data-bs-original-title="لینک جلسه"><i class="bx bx-link"></i></a></div></div></div>';
                    $("#accordionPayment").append(session_item);
                }
            })
        }
    }
</script>
<script src="assets/vendor/libs/jdate/jdate.js"></script>