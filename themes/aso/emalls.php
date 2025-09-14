

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>لیست خوب یاد</title>
<meta name="googlebot" content="noindex,nofollow">
<meta name="robots" content="noindex,nofollow">
    <style type="text/css">
        body {
            direction : rtl;
        }
		.container {
			margin-top: 50px;
			margin-bottom: 50px;
		}
		table th {
			text-align: right;
		}
		table tr td.price {
			font-size: 18px;
		}
		table th {
			font-size: 22px;
		}
		table th.img{ width: 300px;}
		table th.price{ width: 250px;}
		td {
			vertical-align: middle !important;
		}
		h1 {
			padding-bottom: 20px;
			padding-right: 20px;
		}
     
    </style>
</head>
<body>

		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<h1>لیست محصولات موجود و فعال فروشگاه پارس می</h1>
							
									<table class="table table-bordered">
										<thead>
											<th class="title">عنوان</th>
											<th  class="img">تصویر</th>																					
										    <th  class="price">قیمت (<?php echo $functions->get_language($_SESSION['lang'], 'currency') ?>)</th>
											<th  class="price">قیمت با تخفیف (<?php echo $functions->get_language($_SESSION['lang'], 'currency') ?>)</th>
											<th  class="price">وضعیت موجودی</th>
										</thead>
										<tbody>
                                            <?php
                                            $products = $functions->Fetcharray("SELECT `post_title`,`post_name`,`post_id` FROM `post` WHERE `post_type` = 'product' AND `post_status` = 'publish'");
                                            foreach($products as $product):
                                                $price_sale_product_part = $obj->get_meta( '_sale_price');
                                                $price_product_part = $obj->get_meta( '_regular_price');
                                            ?>
                                                <tr>
                                                    <td>
                                                        <a href='/product/<?php echo $product['post_name'] ?>'><?php echo $product['post_title'] ?></a>
                                                    </td>
                                                    <td style="text-align : center">
                                                        <img style="max-width: 64px; max-height: 48px;" src="<?php echo $obj->display_post_image() ?>"/>
                                                    </td>
                                                        <td class="price">
                                                        <?php echo $price_product_part ?>
                                                    </td>
                                                        <td class="price">
                                                        <?php echo $price_sale_product_part ?>
                                                    </td>
													</td>
                                                        <td class="price">
                                                        <?php $stock_status = $obj->get_meta( '_stock_status');
														if ($stock_status == 'instock')
														echo "موجود";
														else{
															echo "ناموجود";
														}
														?>
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
										</tbody>
									</table>
								
						</div>
					</div>
				</div>
			</div>
		</div>
      
    </form>
</body>
</html>
