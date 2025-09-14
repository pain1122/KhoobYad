<?php

include_once('header.php');

if (isset($_POST['submit'])) :
    $insert_query = "INSERT INTO `post`( `post_content`, `post_title`,`post_excerpt`,`post_type`,`post_status`,`post_parent`)
        VALUES ('" . $_POST['message'] . "','" . $_POST['name'] . "','" . $_POST['tel'] . "','contact','sent','کهن کالا')";
    $functions->RunQuery($insert_query);
endif;
?>

<main>
    <section class="contact-page">
        <div class="container">
            <div class="contact-header">
                <h1 class="page-title">تماس با ما</h1>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="contact-info">
                            <div>
                                <h4>آدرس شرکت</h4>
                                <p><?php echo $functions->get_option('address'); ?></p>
                            </div>
                            <div>
                                <h4>تماس با پشتیبانی</h4>
                                <a href="tel:<?php echo $functions->get_option('phone_number1'); ?>"><?php echo $functions->get_option('phone_number1'); ?></a>
                            </div>
                            <div>
                                <h4>ایمیل پشتیبانی</h4>
                                <a href="mailto:<?php echo $functions->get_option('email1'); ?>"><?php echo $functions->get_option('email1'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="contact-map">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contact-form">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                        <h3 class="page-title">شرایط خرید حضوری</h3>
                        <p><?php echo $functions->get_option('expectation'); ?></p>
                    </div>
                    <div class="col-12 col-lg-6">
                    <form action="" method="post">
                            <label>نام و نام خانوادگی<input type="text" name="name"></label>
                            <label>تلفن همراه<input type="text" onchange="replace_digits(this)" name="tel" pattern="/(09|00989|+989)\d{9}/"></label>
                            <label>پیام<textarea name="message"></textarea></label>
                            <button type="submit" name="submit">ثبت نظر</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="/includes/js/map.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5dZxgFNtOUTARzD5bWAc3W4w4ar93MY&callback=initMap&v=weekly" async></script>
<?php include_once('footer.php'); ?>