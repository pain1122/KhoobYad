<?php

include_once('header.php');
$post_id = $post['post_id'];
?>
<section class="cart-page">
	<div class="container">
		<div class="mb-5">
			<h2 class="fav-title"><?php echo $functions->get_language($_SESSION['lang'], 'favorite_product_title'); ?></h2>
			<?php
			if ($user_id) {
				$wishes = [];
				$wishlist = $_SESSION['wishlist'];
				$wishlist_query = "SELECT * FROM `user_meta` WHERE `user_id` = '$user_id' AND `key` = 'wishlist';";
				$wishlist_res = $functions->FetchArray($wishlist_query);
			} else {
				$wishes = [];
				$wishlist = $_SESSION['wishlist'];
			}

			if ($wishlist) : ?>
				<div class="product-slider owl-carousel">
					<?php
					foreach ($wishlist as $id) :
						$query = "SELECT * from `post` 
						INNER JOIN `post_meta` on `post_meta`.`post_id` = `post`.`post_id`
						WHERE `post`.`post_id` = '$id'
                		GROUP BY `post`.`post_id`;";
						$res = $functions->FetchAssoc($query);
						array_push($wishes, $res);
					endforeach;

					foreach ($wishes as $product) :
						$product = new product($product['post_id']);
						include('product-part.php');
					endforeach;
					?>
				</div>
			<?php elseif ($wishlist_res) : ?>
				<div class="product-slider owl-carousel">
					<?php
					foreach ($wishlist_res as $id) :
						$query = "SELECT * from `post` 
						INNER JOIN `post_meta` on `post_meta`.`post_id` = `post`.`post_id`
						WHERE `post`.`post_id` = '" . $id['value'] . "'
						GROUP BY `post`.`post_id`;";
						$res = $functions->FetchAssoc($query);
						array_push($wishes, $res);
					endforeach;

					foreach ($wishes as $product) :
						$product = new product($product['post_id']);
						include('product-part.php');
					endforeach;
					?>
				</div>
			<?php else : ?>
				<p class="non"><?php echo $functions->get_language($_SESSION['lang'], 'no_favorite_product'); ?></p>
			<?php endif; ?>
		</div>
		<div class="mb-5">
			<h2 class="fav-title"><?php echo $functions->get_language($_SESSION['lang'], 'favorite_blog_title'); ?></h2>
			<?php
			$query = "SELECT * FROM `post_meta` WHERE `value` = '$user_id'";
			$bookmarks = $functions->FetchArray($query);
			if ($bookmarks) :
			?>
				<div class="art-wishlist owl-carousel">
					<?php
					foreach ($bookmarks as $post) :
						$post = new blog($post['post_id']);
						include('post-part.php');
					endforeach;
					?>
				</div>
			<?php else : ?>
				<p class="non"><?php echo $functions->get_language($_SESSION['lang'], 'no_favorite_blog'); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php include_once('footer.php'); ?>