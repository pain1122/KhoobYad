<?php
session_start();
include_once("includes/config.php");
include_once(base_dir . "/includes/classes/post.php");
include_once(base_dir . "/includes/classes/blog.php");
include_once(base_dir . "/includes/classes/product.php");
include_once(base_dir . "/includes/classes/tag.php");
include_once(base_dir . "/includes/classes/user.php");
include_once(base_dir . "/includes/classes/shop-order.php");
$uri = explode("?", $_SERVER['REQUEST_URI'])[0];
$guid = trim($uri, "/");
$page_type = explode("/", $guid);

// for meta type
$post_name = $page_type[0];
$guid = urldecode($guid);
switch ($guid) {
    case "/":
        base::redirect(site_url.'/panel');
        break;
    case '':
        base::redirect(site_url.'/panel');
        break;
    case 'sign-up':
        require __DIR__ . "/panel/sign-up-form.php";
        break;
    case 'subscriptions':
        require __DIR__ . "/panel/subscriptions.php";
        break;
    default:
        base::redirect(site_url.'/panel');
        break;
}