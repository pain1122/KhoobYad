<aside id="layout-menu" class="layout-menu menu-vertical overflow-hidden menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.php" class="app-brand-link">
            <img width="100px" src="assets/img/logo_color.png">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <?php if ($role != 'student') :
            foreach ($menu_array as $menu => $detail) : ?>
                <li id="<?php echo $detail['access']; ?>" class="menu-item <?php if (strpos($detail['url'], $page) !== false) echo "active" ?>">
                    <a href="<?php echo $detail['url'] ?>" class="menu-link <?php if (is_countable($detail['sub']) && count($detail['sub']) > 0) echo "menu-toggle" ?> ">
                        <i class="<?php echo $detail['icon']  ?>"></i>
                        <div><?php echo $menu; ?></div>
                        <?php if($detail['access'] == 'tickets') echo $new_message; ?>
                    </a>
                    <?php if (is_countable($detail['sub']) && count($detail['sub']) > 0) : ?>
                        <ul class="menu-sub">
                            <?php foreach ($detail['sub'] as $sub_menu => $sub_detail) : ?>
                                <?php if (strpos($user_granted_access, $sub_detail['access']) !== false ||  ($role == 'admin')) : ?>
                                    <li id="<?php echo $sub_detail['access']; ?>" class="menu-item <?php if (strpos($sub_detail['url'], $page) !== false) : echo "active";
                                                                $menu_title =  $sub_menu;
                                                            endif; ?>">
                                        <a href="<?php echo $sub_detail['url'] ?>" class="menu-link">
                                            <div><?php echo $sub_menu ?></div>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach;
        else :
            foreach ($students_menu_array as $menu => $detail) : ?>
                <li class="menu-item <?php if (strpos($detail['url'], $page) !== false) echo "active" ?>">
                    <a href="<?php echo $detail['url'] ?>" class="menu-link <?php if (is_countable($detail['sub']) && count($detail['sub']) > 0) echo "menu-toggle" ?> ">
                        <i class="<?php echo $detail['icon']  ?>"></i>
                        <div><?php echo $menu ?></div>
                        <?php if($detail['access'] == 'tickets') echo $new_message; ?>
                    </a>
                    <?php if (is_countable($detail['sub']) && count($detail['sub']) > 0) : ?>
                        <ul class="menu-sub">
                            <?php foreach ($detail['sub'] as $sub_menu => $sub_detail) : ?>
                                <li class="menu-item <?php if (strpos($sub_detail['url'], $page) !== false) : echo "active";
                                                            $menu_title =  $sub_menu;
                                                        endif; ?>">
                                    <a href="<?php echo $sub_detail['url'] ?>" class="menu-link <?php if (is_countable($sub_detail['sub']) && count($sub_detail['sub']) > 0) echo "menu-toggle" ?> ">
                                        <div><?php echo $sub_menu ?></div>
                                    </a>
                                    <?php if (is_countable($sub_detail['sub']) && count($sub_detail['sub']) > 0) : ?>
                                        <ul class="menu-sub">
                                            <?php foreach ($sub_detail['sub'] as $sub_menu2 => $sub_detail2) : ?>
                                                <?php //if (strpos($user_granted_access, $sub_detail2['access']) !== false) : ?>
                                                    <li id="<?php echo $sub_detail2['access']; ?>" class="menu-item <?php if (strpos($sub_detail2['url'], $page) !== false) : echo "active";
                                                                                $menu_title =  $sub_menu2;
                                                                            endif; ?>">
                                                        <a href="<?php echo $sub_detail2['url'] ?>" class="menu-link">
                                                            <div><?php echo $sub_menu2 ?></div>
                                                        </a>
                                                    </li>
                                                <?php //endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</aside>
<title><?php echo $menu_title; ?></title>