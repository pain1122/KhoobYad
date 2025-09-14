<?php
include('header.php');
?>
<div class="error" style="margin-bottom: 50px; text-align: center;">
    <h1 style="font-size: 78px; color: var(--color2);"><?php echo $functions->get_language($_SESSION['lang'], '404_title'); ?></h1>
    <p style="font-size: 22px; margin-bottom: 25px;"><?php echo $error_404; ?></p>
    <a href="/" class="button"><?php echo $functions->get_language($_SESSION['lang'], '404_button'); ?></a>
</div>
<?php
include('footer.php');
?>