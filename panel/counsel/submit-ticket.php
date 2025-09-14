<?php

if (isset($_POST['submit'])) {
    if(isset($_GET['chat_id'])){
        $chat_id = $_GET['chat_id'];
        $chat = new post($chat_id);
        $title = $_POST['title'];
        $pined_message = $_POST['pined_message'];
        $members = $_POST['members'];
        $chat->set_title($title);
        $chat->set_content($pined_message);
        if ($role == 'admin' || $role == 'school' || $role == 'adviser' || $role == 'teacher'){
            $meta['members'] = json_encode($members);
            $chat->insert_meta($meta);
        }
    }else{
        $title = $_POST['title'];
        $pined_message = $_POST['pined_message'];
        $members = $_POST['members'];
        $classes = $_POST['classes'];
        $content = htmlentities($_POST['content'],ENT_QUOTES | ENT_HTML5 ,'UTF-8');
        $meta = [];
        $chat = new post('new');
        $chat_id = $chat->get_id();
        $chat->set_title($title);
        $chat->set_content($pined_message);
        $chat->set_author($uid);
        $chat->set_post_type('chat');
        $chat->set_status('ongoing');
        if ($role == 'student') {
            $adviser = base::FetchAssoc("SELECT `value` FROM `user_meta` WHERE `key` = 'adviser' AND `user_id` = $uid")['value'];
            $chat->set_parent($adviser);
            $members[] = $uid;
            $members[] = $adviser;
        } else {
            $members[] = $uid;
            $chat->set_parent($uid);
        }
        $meta['members'] = json_encode($members);
        if ($role == 'admin' || $role == 'school')
            $meta['classes'] = json_encode($classes);
        $chat->insert_meta($meta);
        $type = 'text';
        $message = new post('new');
        $message->set_author($uid);
        $message->set_excerpt($nickname);
        $message->set_content($content);
        $message->set_status('sent');
        $message->set_post_type('message');
        $message->set_parent($chat_id);
        $message->set_mime_type($type);
        if (is_countable($_POST['images']) && count($_POST['images']) > 0) {
            $images = array_map('basename', $_POST['images']);
            $allow = ["docx", "doc", "pdf", "mp3", "rar", "mp4", "wav", "avi", "mov", "zip", "xlsx"];
            foreach ($images as $image) {
                $file = json_encode($image, JSON_UNESCAPED_UNICODE);
                $file_type = substr(end(explode('.', $file)), 0, -1);
                if (in_array(strtolower($file_type), $allow))
                    $type = 'file';
                else
                    $type = 'image';
                $message = new post('new');
                $message->set_author($uid);
                $message->set_excerpt($nickname);
                $message->set_content($file);
                $message->set_status('sent');
                $message->set_post_type('message');
                $message->set_parent($chat_id);
                $message->set_mime_type($type);
            }
        }
    }
    base::redirect("index.php?page=counsel/tickets.php");
}
if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    $chat = new post($chat_id);
    $title = $chat->get_title();
    $pined_message = $chat->get_content();
    $selected_members = json_decode($chat->get_meta('members'), true);
}

    $select_members = "SELECT `users`.`user_id`,`nicename` FROM `users`
    INNER JOIN `user_meta` ON `users`.`user_id` = `user_meta`.`user_id`
    WHERE `key` = 'role' AND (`value` = 'student' OR `value` = 'teacher' OR `value` = 'adviser')
    GROUP BY `users`.`user_id`";
    $members = base::FetchArray($select_members);
if ($role == 'admin' || $role == 'school') :
    $select_classes = "SELECT `tag`.`tag_id` as `id`,`name` FROM `tag`
    INNER JOIN `tag_meta` ON `tag_meta`.`tag_id` = `tag`.`tag_id`
    WHERE `type` = 'class_group'
    GROUP BY `tag`.`tag_id`";
    $classes = base::FetchArray($select_classes);
endif;
?>
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<link rel="stylesheet" type="text/css" href="assets/vendor/libs/dropzone/dropzone.css">
<style>
    .btn-showcase {
        position: absolute;
        bottom: 15px;
    }
</style>
<div class="row">
    <div class="col-12 col-md-10 mx-auto">
        <div class="card p-5">
            <form class="row ad-form flex-row" id="multiFileUpload" method="post" enctype="multipart/form-data">
                <label class="col-12 mb-3">عنوان :<input class="form-control" type="text" value="<?php echo $title; ?>" name="title" placeholder="عنوان خود را وارد کنید"></label>
                <label class="col-12 mb-3">پیام پین شده :<textarea class="form-control" name="pined_message" placeholder="پیام پین شده را وارد کنید"><?php echo $pined_message; ?></textarea></label>
                <label class="col-12 mb-3">انتخاب اعضا :<select class="form-control select2" name="members[]" multiple="multiple">
                        <option></option>
                        <?php if ($role == 'admin' || $role == 'school' || $role == 'adviser' || $role == 'teacher') :
                            foreach ($members as $member) : 
                                if ($member['user_id'] != $uid) : ?>
                                    <option value="<?php echo $member['user_id']; ?>" <?php if(isset($_GET['chat_id']) && in_array($member['user_id'],$selected_members)) echo 'selected'; ?>><?php echo $member['nicename']; ?></option>
                                <?php endif;
                            endforeach; 
                        else : 
                            foreach ($members as $member) : 
                                if (isset($_GET['chat_id']) && in_array($member['user_id'],$selected_members)) : ?>
                                    <option value="<?php echo $member['user_id']; ?>" selected><?php echo $member['nicename']; ?></option>
                                <?php endif;
                            endforeach; 
                        endif;?>
                    </select>
                </label>
                <?php if ($role == 'school' || $role == 'admin' && !isset($chat_id)) : ?>
                    <label class="col-12 mb-3">انتخاب کلاس :
                        <select class="form-control select2" name="classes[]" multiple="multiple">
                            <option></option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>
                <?php if (!isset($chat_id)) : ?>
                    <label class="col-12 mb-3">توضیحات :<textarea rows="8" id="content" class="form-control" name="content" placeholder="توضیحات خود را وارد کنید"></textarea></label>
                    <div class="paths d-none"></div>
                <?php endif; ?>
                <div class="btn-showcase">
                    <button onclick="process()" name="submit" class="btn btn-primary ml-2">ثبت</button>
                    <input class="btn btn-light" type="reset" value="انصراف">
                </div>
            </form>
            <?php if (!isset($chat_id)) : ?>
                <label class="col-12 mb-2">فایل ضمیمه : </label>
                <form class="dropzone dz-clickable w-100 mb-5" id="multiFileUpload" action="/panel/upload.php" enctype="multipart/form-data">
                    <div class="m-0 col-12 dz-message needsclick"><i class="bx bx-upload"></i>
                        <h6 class="mb-0">فایل مورد نظر را بکشید و اینجا رها کنید</h6>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script src="assets/vendor/libs/select2/i18n/fa.js"></script>
<script src="assets/vendor/libs/dropzone/dropzone.js"></script>
<script src="assets/vendor/libs/dropzone/dropzone-script.js"></script>
<script>
    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function() {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'انتخاب',
                multiple: true,
                dropdownParent: $this.parent()
            });
        });
    }

    function process() {
        var textareaText = $('#content').val();
        textareaText = textareaText.replace(/\r?\n/g, '\\n');
        $('#content').val(textareaText);
    }
</script>