<?php if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    base::RunQuery("DELETE FROM `post` WHERE `post_id` = " . $post_id);
    base::RunQuery("DELETE FROM `post_meta` WHERE `post_id` = " . $post_id);
} ?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<div class="card">
    <h5 class="card-header">همه تیکت ها</h5>
    <div class="card-datatable table-responsive">
        <table class="datatables-posts table border-top">
            <thead>
                <tr>
                    <th>آیدی</th>
                    <th>عنوان تیکت</th>
                    <th>وضعیت</th>
                    <th>نویسنده</th>
                    <th>تعداد اعضا</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="chat-box ticket" id="chat">
    <div class="chat-container">
        <div class="chat-header">
            <span class="fa fa-close close-chat"></span>
            <p id="chat-title"></p>
            <?php if ($role == 'admin' || $role == 'school' || $uid == $author) : ?>
                <a data-fancybox data-type='iframe' data-preload='false'><i class="fa fa-users ml-2"></i><span id="members"></span></a>
            <?php else : ?>
                <p class="m-0"><i class="fa fa-users ml-2"></i><span id="members"></span></p>
            <?php endif; ?>
        </div>
        <p class="pinned-message d-none" id="pinned-message"></p>
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
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script>
    $(window).resize(function() {
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }

    });
    $(function() {
        $(window).ready(function() {
            if ($(window).width() < 560) {
                $('.datatables-posts tbody td,.modal-body tbody td').css({
                    'font-size': '12px',
                    'padding': '15px'
                });
            } else {
                $('.datatables-posts tbody td,.modal-body tbody td').css({
                    'font-size': '14px',
                    'padding': '0.625rem 1.5rem'
                });
            }
            // Variable declaration for table
            var dt_user_table = $('.datatables-posts');

            // Users datatable
            if (dt_user_table.length) {
                var dt_user = dt_user_table.DataTable({
                    processing: true,
                    serverSide: true,
                    select: true,
                    ajax: {
                        url: 'API/v1/GetTickets.php?uid=<?php echo $uid; ?>'
                    },
                    columns: [
                        // columns according to JSON
                        {
                            data: 'post_id'
                        },
                        {
                            data: 'title'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'author'
                        },
                        {
                            data: 'count'
                        },
                        {
                            data: 'date'
                        },
                        {
                            data: 'op'
                        }
                    ],
                    columnDefs: [{
                            // For Responsive
                            // className: 'control',
                            targets: 0,
                            render: function(data, type, full, meta) {
                                var $id = full['post_id'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                            }
                        },
                        {
                            // User full name 
                            targets: 1,
                            responsivePriority: 4,
                            render: function(data, type, full, meta) {
                                var $name = full['title'],
                                    $id = full['post_id'];
                                var $row_output =
                                    '<p onclick="chat_start(' + $id + ')">' +
                                    '<span class="fw-semibold">' +
                                    $name +
                                    '</span>' +
                                    '</p>';
                                return $row_output;
                            }
                        },
                        {
                            // status
                            targets: 2,
                            render: function(data, type, full, meta) {
                                var $status = full['status'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $status + '</span>';
                            }
                        },
                        {
                            // author
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var $author = full['author'];
                                return '<span class="badge bg-label-warning">' + $author + '</span>';
                            }
                        },
                        {
                            // count
                            targets: 4,
                            render: function(data, type, full, meta) {
                                var $count = full['count'];
                                return '<span class="badge bg-label-warning">' + $count + '</span>';
                            }
                        },
                        {
                            // date
                            targets: 5,
                            render: function(data, type, full, meta) {
                                var $date = full['date'];
                                return "<span class='text-truncate d-flex align-items-center'>" + $date + '</span>';
                            }
                        },
                        {
                            // op
                            targets: 6,
                            title: 'عمل‌ها',
                            searchable: false,
                            orderable: false,
                            render: function(data, type, full, meta) {
                                var $op = full['op'];
                                return $op;
                            }
                        }
                    ],
                    order: [
                        [0, 'DESC']
                    ],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'جزئیات ' + data['title'];
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                        ?
                                        '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +
                                        '<td>' +
                                        col.data +
                                        '</td>' +
                                        '</tr>' :
                                        '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });
            }
        })
    });
    var ids = [];
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

    function chat_start(post_id) {
        getJSON('<?php echo site_url; ?>panel/API/v1/get_ticket.php?post_id=' + post_id, function(err, data) {
            ids = [];
            var title = data['title'];
            $('#chat .chat-content').html('');
            $('#submit-pic').attr('onclick', 'message(' + post_id + ')');
            $('#submit-chat').attr('onclick', 'message(' + post_id + ')');
            $('#chat-title').text(title);
            var pined_message = data['pined_message'];
            $('#pinned-message').addClass('d-none');
            if(pined_message && pined_message.length > 0){
                $('#pinned-message').text(pined_message);
                $('#pinned-message').removeClass('d-none');
            }
            $('#members').text(members);
            var message_count = 0;
            if (messages)
                message_count = messages.length;
            <?php if ($role == 'admin' || $role == 'school' || $uid == $author) : ?>
                $('#members').parent().attr('href', '?page=counsel/submit-ticket.php&chat_id=' + post_id);
            <?php endif; ?>
            var messages = data['tickets'];
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
</script>