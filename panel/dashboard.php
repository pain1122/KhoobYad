<link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css">
<!-- Content -->
<div class="row">
  <?php
  $plans_league_ranks_q = "SELECT `author`,SUM(CAST(`post_name` AS UNSIGNED)) as `duration` FROM `post`
  INNER JOIN `post_meta` ON `post_meta`.`post_id` =`post`.`post_id`
  INNER JOIN `user_meta` ON `user_meta`.`user_id` = `post`.`author`
  WHERE `post_date` >= curdate() - INTERVAL DAYOFWEEK(curdate())+30 DAY
  AND `post_date` < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
  AND `post_type` = 'plan'
  AND `user_meta`.`key` = 'plans_league'
  AND `user_meta`.`value` = 'true' 
  AND `post_meta`.`key` = 'score'
  AND (`post_meta`.`value` != 4 AND `post_meta`.`value` != 'انتخاب' )
  GROUP BY `author`  
  ORDER BY `duration` DESC
  LIMIT 0,10;";
  $plans_league_ranks = base::FetchArray($plans_league_ranks_q);
  $defiend_plans_league_ranks_q = "SELECT
  `plans`.`user_id`,
  SUM(CAST(`duration` AS UNSIGNED)) AS `duration`,
  CAST(`status` AS UNSIGNED) AS `status`
FROM `plans`
INNER JOIN `user_meta`
  ON `user_meta`.`user_id` = `plans`.`user_id`
WHERE
  `date` >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
  AND `date` < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
  AND `type` = 'plan'
  AND `user_meta`.`key` = 'defind_plans_league'
  AND `user_meta`.`value` = 'true'
  AND (`status` > 0 AND `status` < 4)
GROUP BY `plans`.`user_id`
ORDER BY `duration` DESC
LIMIT 0, 10;";
  // echo $defiend_plans_league_ranks_q;
  $defiend_plans_league_ranks = base::FetchArray($defiend_plans_league_ranks_q);
  if ($role == 'student') {
    if (is_countable($user_classes) && count($user_classes) > 0) {
      if (is_string($classes)) {
        $classes = json_decode($user_classes, true);
      }
      $classes_q = "OR (`key` = 'clases' AND (";
      foreach ($classes as $classe) {
        if ($classes[0] == $class)
          $classes_q .= "`value` LIKE '%\"$class\"%'";
        else
          $classes_q .= " OR `value` LIKE '%\"$class\"%'";
      }
      $classes_q = '))';
    } else {
      $classes_q = '';
    }
    $subject_query = "SELECT COUNT(`post`.`post_id`) AS `count` FROM `post` INNER JOIN `post_meta` ON `post`.`post_id` = `post_meta`.`post_id` WHERE `post_type` = 'chat' AND ((`key` = 'members' AND `value` LIKE '%\"$uid\"%') $classes_q);";
    $counsel_count = base::FetchAssoc($subject_query)['count'];

    $plans_q = "SELECT COUNT(`post_id`) AS `count` FROM `post` WHERE `post_type` = 'weekly_plan' AND `author` = $uid";
    $plans_count = base::FetchAssoc($plans_q)['count'];

    $offboxes_q = "SELECT COUNT(`post_id`) AS `count` FROM `post` WHERE `post_type` = 'off-boxes' AND `post_id` IN (SELECT `item_id` FROM `items_order` INNER JOIN `post` ON `post`.`post_id` = `items_order`.`order_id` WHERE `post_type` = 'shop_order' AND `author` = $uid GROUP BY `item_id`);";
    $offboxes_count = base::FetchAssoc($offboxes_q)['count'];

    $online_classes_q = "SELECT COUNT(`post_id`) AS `count` FROM `post` WHERE `post_type` = 'online-class' AND `post_id` IN (SELECT `item_id` FROM `items_order` INNER JOIN `post` ON `post`.`post_id` = `items_order`.`order_id` WHERE `post_type` = 'shop_order' AND `author` = $uid GROUP BY `item_id`);";
    $online_classes_count = base::FetchAssoc($online_classes_q)['count'];
    ?>
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center me-3">
            <img src="<?php if (!empty($avatar))
              echo $avatar;
            else
              echo "assets/img/avatars/1.png"; ?>" alt="Avatar" class="rounded-circle me-3" width="54">
            <div class="card-title mb-0">
              <h5 class="mb-0">گزارش عملکرد برای <?php echo $nickname; ?></h5>
              <small class="text-muted primary-font">یک برنامه عالی برای مدیریت دروس</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-4 mb-5 mt-4">
            <div class="d-flex flex-column me-2">
              <h6>مشاوره های من</h6>
              <span class="badge bg-label-success"><?php echo $counsel_count; ?></span>
            </div>
            <div class="d-flex flex-column me-2">
              <h6>برنامه های من</h6>
              <span class="badge bg-label-success"><?php echo $plans_count; ?></span>
            </div>
            <div class="d-flex flex-column me-2">
              <h6>کلاس های آفلاین من</h6>
              <span class="badge bg-label-success"><?php echo $offboxes_count; ?></span>
            </div>
            <div class="d-flex flex-column me-2">
              <h6>کلاس های آنلاین من</h6>
              <span class="badge bg-label-success"><?php echo $online_classes_count; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12">
      <?php if (!empty($is_in_plan_league) && $is_in_plan_league === 'true'): ?>
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">لیگ برنامه تحصیلی</h5>
          </div>
          <div class="card-body">

            <div class="table-responsive">
              <table class="table border-top">
                <thead>
                  <tr>
                    <th>رتبه</th>
                    <th>نام</th>
                    <th>مقطع</th>
                    <th>مقدار زمان مطالعه</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php
                  $rank = 1;
                  foreach ($plans_league_ranks as $item):
                    $names = base::fetcharray("SELECT `value` FROM `user_meta` WHERE user_id = {$item['user_id']} AND (`key` = 'firstname' OR `key` = 'lastname');");
                    if(is_countable($names))
                      $name = $names[0]['value'].' '.$names[1]['value'];
                    $fos = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'fos')")['name'];
                    $grade = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'grade')")['name'];
                    $duration = intval($item['duration']);
                    ?>
                    <tr>
                      <td><?php echo $rank ?></td>
                      <td><?php echo $name ?></td>
                      <td><?php echo $fos . ' - ' . $grade ?></td>
                      <td><?php echo $duration . ' دقیقه' ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php if (!empty($is_in_defind_plan_league) && $is_in_defind_plan_league === 'true'): ?>
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">لیگ خوب یار</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table border-top">
                <thead>
                  <tr>
                    <th>رتبه</th>
                    <th>نام</th>
                    <th>مقطع</th>
                    <th>مقدار زمان مطالعه</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php
                  $rank = 1;
                  foreach ($defiend_plans_league_ranks as $item):
                    $names = base::fetcharray("SELECT `value` FROM `user_meta` WHERE user_id = {$item['user_id']} AND (`key` = 'firstname' OR `key` = 'lastname');");
                    if(is_countable($names))
                      $name = $names[0]['value'].' '.$names[1]['value'];
                    $fos = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'fos')")['name'];
                    $grade = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'grade')")['name'];
                    $duration = intval($item['duration']);
                    ?>
                    <tr>
                      <td><?php echo $rank ?></td>
                      <td><?php echo $name ?></td>
                      <td><?php echo $fos . ' - ' . $grade ?></td>
                      <td><?php echo $duration . ' دقیقه' ?></td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php } else {
    ?>
    <div class="col-md-6 col-lg-6 col-xl-5 col-xl-5 mb-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between mb-3">
          <h5 class="card-title mb-0">خلاصه آمار سایت</h5>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-3">
              <div class="avatar avatar-sm flex-shrink-0 me-3">
                <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-cube"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <?php
                $product_count = Base::FetchAssoc("SELECT COUNT(`post_id`) as `total`,(SELECT COUNT(`post_id`) as `total` FROM `post` WHERE `post_type` = 'product' AND `post_status` = 'publish' AND `post_parent` = 0) as `published` FROM `post` WHERE `post_type` = 'product' AND `post_parent` = 0");
                ?>
                <div class="me-2">
                  <p class="mb-0">تعداد محصولات</p>
                  <small class="text-muted"><?php echo $product_count['published'] ?> محصول منتشر شده</small>
                </div>
                <div class="item-progress">
                  <?php echo $product_count['total'] ?>
                </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="avatar avatar-sm flex-shrink-0 me-3">
                <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-pie-chart-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <?php
                  $post_id_query = "SELECT `post_id` as `id` FROM `post` WHERE `post_type` = 'shop_order'";
                  $order_data = Base::fetchassoc("SELECT SUM(CAST(`amount`.`value` AS UNSIGNED)) as `sm` FROM `post` `orders`
                                    INNER JOIN `items_order` `items` ON `orders`.`post_id` = `items`.`order_id`
                                    INNER JOIN `items_order_meta` `amount` ON `items`.`items_order_id` = `amount`.`order_item_id`
                                    WHERE `amount`.`key` = 'total' AND `orders`.`post_id` IN ($post_id_query)");
                  ?>
                  <p class="mb-0">کل فروش</p>
                </div>
                <div class="item-progress"><?php if ($order_data['sm'] > 0)
                  echo number_format($order_data['sm']);
                else
                  echo 0; ?> </div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="avatar avatar-sm flex-shrink-0 me-3">
                <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-credit-card"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <?php
                  $user_count = Base::FetchAssoc("SELECT COUNT('user_id') as `user_count` FROM `users`");
                  ?>
                  <p class="mb-0">کل کاربران</p>
                </div>
                <div class="item-progress"><?php echo $user_count['user_count'] ?></div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar avatar-sm flex-shrink-0 me-3">
                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-dollar"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <?php
                $post_count = Base::FetchAssoc("SELECT COUNT(`post_id`) as `total`,(SELECT COUNT(`post_id`) as `total` FROM `post` WHERE `post_type` = 'post' AND `post_status` = 'publish') as `published` FROM `post` WHERE `post_type` = 'post'");
                ?>
                <div class="me-2">
                  <p class="mb-0">تعداد مقالات</p>
                  <small class="text-muted"><?php echo $post_count['published'] ?> مقاله منتشر شده</small>
                </div>
                <div class="item-progress">
                  <?php echo $post_count['total'] ?>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Weekly Order Summary -->
    <div class="col-md-6 col-lg-6 col-xl-7 col-xl-7 mb-4">
      <div class="card h-100">
        <div class="row row-bordered m-0">
          <!-- Order Summary -->
          <div class="col-md-8 col-12 pe-0">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">میانگین سفارش هفتگی</h5>

            </div>
            <div class="card-body p-0">
              <div id="orderSummaryChart"></div>
            </div>
          </div>
          <!-- Sales History -->
          <div class="col-md-4 col-12 px-0">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">نمای کلی فروش‌ها</h5>
            </div>
            <div class="card-body">
              <h6 class="mt-1">هفته قبل</h6>
              <ul class="list-unstyled m-0 pt-0">
                <li class="mb-4">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm flex-shrink-0 me-2">
                      <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-trending-up"></i></span>
                    </div>
                    <div>
                      <p class="mb-0 text-muted text-nowrap">درآمد این هفته</p>
                      <small class="fw-semibold text-nowrap">
                        <?php
                        $post_id_query = "SELECT `post_id` as `id` FROM `post` WHERE `post_type` = 'shop_order' AND post_date >= Date_add(Curdate(), INTERVAL -7 day)";
                        $week_data = Base::fetchassoc("SELECT SUM(CAST(`amount`.`value` AS UNSIGNED)) as `sm` FROM `post` `orders`
                        INNER JOIN `items_order` `items` ON `orders`.`post_id` = `items`.`order_id`
                        INNER JOIN `items_order_meta` `amount` ON `items`.`items_order_id` = `amount`.`order_item_id`
                        WHERE `amount`.`key` = 'total' AND `orders`.`post_id` IN ($post_id_query)");
                        if ($week_data['sm'] > 0)
                          echo number_format($week_data['sm']);
                        else
                          echo 0;
                        ?>
                      </small>
                    </div>
                  </div>
                  <div class="progress" style="height: 6px">
                    <div class="progress-bar bg-primary" style="width: 75%" role="progressbar" aria-valuenow="100"
                      aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </li>
                <li>
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm flex-shrink-0 me-2">
                      <span class="avatar-initial rounded bg-label-success"><i class="bx bx-dollar"></i></span>
                    </div>
                    <div>
                      <p class="mb-0 text-muted text-nowrap">میانگین فروش روزانه</p>
                      <small class="fw-semibold text-nowrap"><?php echo $week_data['sm'] / 7 ?></small>
                    </div>
                  </div>
                  <div class="progress" style="height: 6px">
                    <div class="progress-bar bg-success" style="width: 75%" role="progressbar" aria-valuenow="100"
                      aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Weekly Order Summary -->
    <!-- Donut Chart -->
    <div class="col-md-6 col-lg-6 col-xl-5 col-xl-5 mb-4">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title mb-1">نسبت وضعیت سفارشات</h5>
          </div>
        </div>
        <div class="card-body">
          <div id="donutChart"></div>
        </div>
      </div>
    </div>
    <!-- /Donut Chart -->
    <!-- Most sales -->
    <div class="col-md-6 col-lg-6 col-xl-7 col-xl-7 mb-4">

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">پر فروش ترین محصولات</h5>
        </div>
        <div class="card-body">
        </div>
        <div class="table-responsive">
          <table class="table border-top">
            <thead>
              <tr>
                <th>آیدی محصول</th>
                <th>نام محصول</th>
                <th>تعداد فروش</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              <?php
              $most_saled = Base::FetchArray("SELECT `item_id`,`post_title`,`cnt` FROM (SELECT COUNT(`item_id`) as `cnt`,`item_id` FROM `items_order` GROUP BY `item_id` ORDER BY `cnt` DESC LIMIT 0,5) `sales`
                          INNER JOIN `post` ON `sales`.`item_id` = `post`.`post_id`");

              foreach ($most_saled as $item):
                ?>
                <tr>
                  <td><?php echo $item['item_id'] ?></td>
                  <td><?php echo $item['post_title'] ?></td>
                  <td><?php echo $item['cnt'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--/ Most sales -->

    <!-- Most recent products -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">آخرین محصولات بروز شده</h5>
        </div>
        <div class="card-body">
        </div>
        <div class="table-responsive">
          <table class="table border-top">
            <thead>
              <tr>
                <th>آیدی محصول</th>
                <th>نام محصول</th>
                <th>آخرین تاریخ بروز رسانی</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              <?php
              $most_recent = Base::FetchArray("SELECT `post_id`,`modify_date`,`post_title` FROM `post`
                          WHERE `post_type` = 'product' AND `post_parent` = 0
                          ORDER BY `modify_date` DESC LIMIT 0,5");

              foreach ($most_recent as $item):
                ?>
                <tr>
                  <td><?php echo $item['post_id'] ?></td>
                  <td><?php echo $item['post_title'] ?></td>
                  <td><?php echo $item['modify_date'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--/ Most recent products -->

    <!-- Most favourite articles -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">محبوب ترین مقالات</h5>
        </div>
        <div class="card-body">
        </div>
        <div class="table-responsive">
          <table class="table border-top">
            <thead>
              <tr>
                <th>آیدی مقاله</th>
                <th>نام مقاله</th>
                <th>آخرین تاریخ بروز رسانی</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              <?php
              $most_favourite_posts = Base::FetchArray("SELECT `post_id`,`modify_date`,`post_title` FROM `post`
                          WHERE `post_type` = 'post' AND `post_parent` = 0
                          ORDER BY `modify_date` DESC LIMIT 0,5");

              foreach ($most_favourite_posts as $item):
                ?>
                <tr>
                  <td><?php echo $item['post_id'] ?></td>
                  <td><?php echo $item['post_title'] ?></td>
                  <td><?php echo $item['modify_date'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--/ Most favourite articles -->
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">لیگ برنامه تحصیلی</h5>
        </div>
        <div class="card-body">

          <div class="table-responsive">
            <table class="table border-top">
              <thead>
                <tr>
                  <th>رتبه</th>
                  <th>نام</th>
                  <th>مقطع</th>
                  <th>مقدار زمان مطالعه</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <?php
                $rank = 1;
                foreach ($plans_league_ranks as $item):
                  $names = base::fetcharray("SELECT `value` FROM `user_meta` WHERE user_id = {$item['user_id']} AND (`key` = 'firstname' OR `key` = 'lastname');");
                  if(is_countable($names))
                    $name = $names[0]['value'].' '.$names[1]['value'];
                  $fos = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'fos')")['name'];
                  $grade = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'grade')")['name'];
                  $duration = intval($item['duration']);
                  ?>
                  <tr>
                    <td><?php echo $rank ?></td>
                    <td><?php echo $name ?></td>
                    <td><?php echo $fos . ' - ' . $grade ?></td>
                    <td><?php echo $duration . ' دقیقه' ?></td>
                  </tr>
                  <?php $rank++; endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">لیگ خوب یار</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table border-top">
              <thead>
                <tr>
                  <th>رتبه</th>
                  <th>نام</th>
                  <th>مقطع</th>
                  <th>مقدار زمان مطالعه</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <?php
                $rank = 1;
                foreach ($defiend_plans_league_ranks as $item):
                  $names = base::fetcharray("SELECT `value` FROM `user_meta` WHERE user_id = {$item['user_id']} AND (`key` = 'firstname' OR `key` = 'lastname');");
                  if(is_countable($names))
                    $name = $names[0]['value'].' '.$names[1]['value'];
                  $fos = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'fos')")['name'];
                  $grade = base::fetchassoc("SELECT `name` FROM `tag` WHERE `tag_id` IN (SELECT `value` FROM `user_meta` WHERE `user_id` = {$item['user_id']} AND `key` = 'grade')")['name'];
                  $duration = intval($item['duration']);
                  ?>
                  <tr>
                    <td><?php echo $rank ?></td>
                    <td><?php echo $name ?></td>
                    <td><?php echo $fos . ' - ' . $grade ?></td>
                    <td><?php echo $duration . ' دقیقه' ?></td>
                  </tr>
                  <?php $rank++; endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

</div>
<!-- / Content -->
<script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
  let cardColor, headingColor, axisColor, borderColor, shadeColor;


  cardColor = config.colors.white;
  headingColor = config.colors.headingColor;
  axisColor = config.colors.axisColor;
  borderColor = config.colors.borderColor;
  shadeColor = 'light';
  // Order Summary - Area Chart
  // --------------------------------------------------------------------
  <?php
  $post_id_query = "SELECT `post_id` as `id` FROM `post` WHERE `post_type` = 'shop_order' AND post_date >= Date_add(Curdate(), INTERVAL -7 day)";
  $chart_data = Base::fetcharray("SELECT SUM(CAST(`amount`.`value` AS UNSIGNED)) as `sm`,DayName(orders.post_date) as `days` FROM `post` `orders`
      INNER JOIN `items_order` `items` ON `orders`.`post_id` = `items`.`order_id`
      INNER JOIN `items_order_meta` `amount` ON `items`.`items_order_id` = `amount`.`order_item_id`
      WHERE `amount`.`key` = 'total' AND `orders`.`post_id` IN ($post_id_query)
      GROUP  BY DayName(orders.post_date)");
  ?>
  const orderSummaryEl = document.querySelector('#orderSummaryChart'),
    orderSummaryConfig = {
      chart: {
        height: 264,
        type: 'area',
        toolbar: false,
        dropShadow: {
          enabled: true,
          top: 18,
          left: 2,
          blur: 3,
          color: config.colors.primary,
          opacity: 0.15
        }
      },
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [{
          fillColor: cardColor,
          seriesIndex: 0,
          dataPointIndex: 9,
          strokeColor: config.colors.primary,
          strokeWidth: 4,
          size: 6,
          radius: 2
        }],
        hover: {
          size: 7
        }
      },
      series: [{
        name: 'تومان',
        data: [<?php if ($chart_data['sm'])
          echo implode(",", array_column($chart_data, 'sm')) ?>]
        }],
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          lineCap: 'round'
        },
        colors: [config.colors.primary],
        fill: {
          type: 'gradient',
          gradient: {
            shade: shadeColor,
            shadeIntensity: 0.8,
            opacityFrom: 0.7,
            opacityTo: 0.25,
            stops: [0, 95, 100]
          }
        },
        grid: {
          show: true,
          borderColor: borderColor,
          padding: {
            top: -15,
            bottom: -10,
            left: 15,
            right: 10
          }
        },
        xaxis: {
          categories: [<?php if ($chart_data['days'])
          echo implode(",", $chart_data['days']) ?>],
          labels: {
            offsetX: 0,
            style: {
              colors: axisColor,
              fontSize: '13px'
            }
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          lines: {
            show: false
          }
        },
        yaxis: {
          labels: {
            offsetX: 7,
            formatter: function (val) {
              return val + ' تومان ';
            },
            style: {
              fontSize: '13px',
              colors: axisColor
            }
          },
          min: 0,
          max: <?php if ($chart_data['sm'])
          echo max($chart_data['sm']);
        else
          echo 0; ?>,
        tickAmount: 4
      }
    };
  if (typeof orderSummaryEl !== undefined && orderSummaryEl !== null) {
    const orderSummary = new ApexCharts(orderSummaryEl, orderSummaryConfig);
    orderSummary.render();
  }

  // Donut Chart
  // --------------------------------------------------------------------

  <?php
  $orders = Base::fetcharray("SELECT COUNT(`post_id`) as `count` ,
    concat('\'',`post_status`,'\'') as `post_status`,
    (SELECT COUNT(`post_id`) as `count` FROM `post`  WHERE `post_type` = 'shop_order' AND `post_status` = 'failed') as `failed_count`
    FROM `post` 
    WHERE `post_type` = 'shop_order'
    GROUP BY `post_status`");
  if (!empty($orders)):
    ?>
    const chartColors = {
      column: {
        series1: '#826af9',
        series2: '#d2b0ff',
        bg: '#f8d3ff'
      },
      donut: {
        series1: '#fee802',
        series2: '#3fd0bd',
        series3: '#826bf8',
        series4: '#2b9bf4'
      },
      area: {
        series1: '#29dac7',
        series2: '#60f2ca',
        series3: '#a5f8cd'
      }
    };
    const donutChartEl = document.querySelector('#donutChart'),
      donutChartConfig = {
        chart: {
          height: 390,
          type: 'donut'
        },
        labels: [<?php if ($orders[0]['post_status'])
          echo implode(",", array_column($orders, 'post_status')) ?>],
          series: [<?php if ($orders[0]['count'])
          echo implode(",", array_column($orders, 'count')) ?>],
          colors: [
            chartColors.donut.series1,
            chartColors.donut.series4,
            chartColors.donut.series3,
            chartColors.donut.series2
          ],
          stroke: {
            show: false,
            curve: 'straight'
          },
          dataLabels: {
            enabled: true,
            formatter: function (val, opt) {
              return parseInt(val) + '%';
            }
          },
          legend: {
            show: true,
            position: 'bottom',
            labels: {
              colors: axisColor,
              useSeriesColors: false
            }
          },
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  name: {
                    fontSize: '2rem',
                    offsetY: -13,
                    color: axisColor
                  },
                  value: {
                    fontSize: '1.2rem',
                    offsetY: 12,
                    color: axisColor,
                    formatter: function (val) {
                      return parseInt(val);
                    }
                  },
                  total: {
                    show: true,
                    fontSize: '1.5rem',
                    color: headingColor,
                    label: ' سفارشات انجام شده',
                    formatter: function (w) {
                      return <?php
        $total_orders = array_sum(array_column($orders, 'count'));
        // $failed_orders = $orders[];
        echo number_format(($total_orders - $orders[0]['failed_count']) / $total_orders, 2);
        ?>;
                  }
                }
              }
            }
          }
        },
        responsive: [{
          breakpoint: 992,
          options: {
            chart: {
              height: 380
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: axisColor,
                useSeriesColors: false
              }
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            chart: {
              height: 320
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '1.5rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '1.5rem'
                    }
                  }
                }
              }
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: axisColor,
                useSeriesColors: false
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 280
            },
            legend: {
              show: false
            }
          }
        },
        {
          breakpoint: 360,
          options: {
            chart: {
              height: 250
            },
            legend: {
              show: false
            }
          }
        }
        ]
      };
    if (typeof donutChartEl !== undefined && donutChartEl !== null) {
      const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
      donutChart.render();
    }
  <?php endif; ?>
</script>