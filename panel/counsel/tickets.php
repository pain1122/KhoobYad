<?php
if(isset($_GET['status']))
$status = $_GET['status'];
if($status)
$status_q = "AND `post_status` = '$status' ";
if ($role == 'admin') {
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` 
    WHERE `post_type` = 'chat' $status_q 
    ORDER BY `post_id` DESC;";
} elseif ($role == 'school') {
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` 
    WHERE `post_type` = 'chat' 
    $status_q 
    AND `school` = $uid 
    ORDER BY `post_id` DESC;";
} else {
    if (is_countable($user_classes) && count($user_classes) > 0) {
        $classes = $user_classes;
        $classes_q = "OR (`key` = 'clases' AND (";
        for ($i = 0; $i < count($classes); $i++) {
            if ($i === 0)
                $classes_q .= "`value` LIKE '%$classes[$i]%'";
            else
                $classes_q .= " OR `value` LIKE '%$class[$i]%'";
        }
        $classes_q .= '))';
    } else {
        $classes_q = '';
    }
    $subject_query = "SELECT `post`.`post_id`,`post_title`,`post_excerpt` FROM `post` 
    INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id`
     WHERE `post_type` = 'chat' $status_q
      AND ((`key` = 'members' AND `value` LIKE '%$uid%') $classes_q) 
      GROUP BY `post`.`post_id` ORDER BY `post`.`post_id` DESC;";
    //   echo $subject_query;
}
$subjects = $functions->FetchArray($subject_query, $con);

?>

<script>
    var ids = [];

    function chat_start(post_id) {
        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + post_id, function(err, data) {
            ids = [];
            var title = data['title'];
            var members = data['members'];
            var messages = data['tickets'];
            var message_count = 0;
            if (messages)
                message_count = messages.length;
            $('#chat .chat-content').html('');
            $('#submit-pic').attr('onclick', 'message(' + post_id + ')');
            $('#submit-chat').attr('onclick', 'message(' + post_id + ')');
            $('#chat-title').text(title);
            var pined_message = data['pined_message'];
            $('#pinned-message').addClass('d-none');
            $('#members').text(members);
            <?php if ($role == 'admin' || $role == 'school' || $uid == $author) : ?>
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
            setInterval(get_message, 5000, post_id);
            if(pined_message && pined_message.length > 0){
                $('#pinned-message span').text(pined_message);
                $('#pinned-message').removeClass('d-none');
            }
        });
    };

    function get_message(id) {
        if ($('.chat-box').hasClass('open')) {
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function(err, data) {
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
                success: function(data) {
                    error = ['file too large', 'upload failed!'];
                    image = data;
                    if (!error.includes(image)) {
                        getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&image=' + image + '&title=' + title, function(err, data) {});
                        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function(err, data) {
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
            getJSON('<?php echo site_url; ?>panel/API/v1/ticket.php?id=' + id + '&text=' + text, function(err, data) {});
            $('#chat .chat-footer input').val('');
            $('#chat .photo-wrapper input').val('');
            $('#chat .photo-wrapper img').attr('src', '');
            getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + id, function(err, data) {
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

    function get_last_message(id, indicator) {
        getJSON('<?php echo site_url; ?>panel/API/v1/last_ticket.php?pid=' + id, function(err, data) {
            var message = $('#card-' + indicator + ' .bg-label-warning').text();
            if (data != null) {
                if (data["no-message"]) {
                    $('#card-' + indicator + ' .chat-info').html("<p class=\"info\">" + data["no-message"] + "<span class=\"btn btn-primary\" onclick=\"chat_start(" + id + ")\">مشاهده چت</span></p>");
                } else {
                    if (data["file"] == 'true') {
                        var text = "فایل";
                    } else {
                        if (data["post_content"].length > 100) {
                            var text = data["post_content"].substring(0, 100) + ' ...';
                        } else {
                            var text = data["post_content"];
                        }
                    }
                    $('#card-' + indicator + ' .chat-info').html("<p class=\"info\">" + data["username"] + "<span>" + data["date"] + "</span></p><p class=\"message\">" + text + "<span class=\"btn btn-primary\" onclick=\"chat_start(" + id + ")\">مشاهده چت</span></p>");
                    if(data['not_seen'] && message == ''){
                        $('#card-' + indicator + ' .content > h2').append('<span class="btn-sm bg-label-warning">پبام جدید</span>');
                    }else{
                        $('#card-' + indicator + ' .content > h2 span').remove();
                    }
                }
            }
        });
    }
</script>
<div class="container-fluid product-wrapper sidebaron">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <?php
                if (is_countable($subjects) && count($subjects) > 0) {
                    $indicator = 0;
                    foreach ($subjects as $subject) {
                        $id = $subject['post_id'];
                        if ($id) {
                            $chat = new post($id);
                            $author = $chat->get_author();
                            $members = json_decode($chat->get_meta('members'), true);
                            if (is_countable($members))
                                $members_count = count($members);
                            $indicator++; 
                            if($members_count == 2)
                            {
                                $student = new user($members[0]);
                                $master = new user($members[1]);
                                $suffix_title = " - ".$student->get_nick_name() . " - ".$master->get_nick_name();
                            }
                            ?>
                            <div class="col-12 mb-4 chat-card" id="card-<?php echo $indicator; ?>">
                                <div>
                                    <div class="content">
                                        <h5><?php echo $subject['post_title'] . $suffix_title; ?></h2>
                                        <div class="chat-info">
                                            <script>
                                                <?php echo "get_last_message($id,$indicator)"; ?>
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <?php }
                    }
                } else { ?>
                    <div class="notice">
                        <h5 class="my-0">در حال حاضر شما هیچ چت فعالی ندارید</h5>
                    </div>
                <?php }
                ?>
            </div>
            <div class="chat-box ticket" id="chat">
                <div class="chat-container">
                    <div class="chat-header">
                        <span class="fa fa-close close-chat"></span>
                        <p id="chat-title"><?php echo $subject['post_title']; ?></p>
                        <?php if ($role == 'admin' || $role == 'school' || $uid == $author) : ?>
                            <a data-fancybox data-type='iframe' data-preload='false'><i class="fa fa-users ml-2"></i><span id="members"></span></a>
                        <?php else : ?>
                            <p class="m-0"><i class="fa fa-users ml-2"></i><span id="members"></span></p>
                        <?php endif; ?>
                    </div>
                    <p class="pinned-message d-none" id="pinned-message"><span></span><i class="fa fa-close close-pinned"></i></p>
                    <div class="chat-content">

                    </div>
                    <div class="chat-footer">
                        <input type="text" placeholder="پیام خود را بنویسید ..." id="message" name="chat">
                        <label class="image-icon"><input type="file" accept="image/*" onchange="loadFile(this,event);$('#chat .photo-preveiw').addClass('open');" id="chat-pic" name="pic"></label>
                        <label class="file-icon"><input type="file" accept="*" onchange="getFileExt(event,'ext');$('#chat .photo-preveiw').addClass('open file');" id="chat-file" name="pic"></label>
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
        </div>
    </div>
</div>
<script>
    $('.chat-box').click(function(e) {
        if (!$(e.target).parents(".chat-box").length || $(e.target).hasClass("close-chat")) {
            $(this).removeClass('open');
        }
    });
    $('.photo-preveiw').click(function(e) {
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
    $('.close-pinned').click(function(){
        $(this).parent().addClass('deactive');
    })
    <?php if (is_countable($subjects) && count($subjects) > 0) :
        $indicator = 0;
        foreach ($subjects as $subject) :
            $id = $subject['post_id'];
            if ($id) :
                $indicator++;
                echo "setInterval(get_last_message, 5000,$id,$indicator);";
            endif;
        endforeach;
    endif; ?>
</script>