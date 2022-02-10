<?php

global $current_user;

$store = get_user_meta(get_current_user_id(), '__wpdm_public_profile', true);

?>

<div class="w3eden user-dashboard">
    <div class="row">
        <div id="wpdm-dashboard-sidebar" class="col-md-3">

            <div id="logo-block">
                <img class="shop-logo" id="shop-logo" src="<?php echo isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : get_avatar_url( $current_user->user_email, array('size' => 512) ); ?>"/>
            </div>
            <div id="tabs">
                <?php
                if(is_array($this->dashboard_menu)) {
                    foreach ($this->dashboard_menu as $section_id => $section) {
                        echo "<div id='udm-{$section_id}'>";
                        if (isset($section['title']) && $section['title'] != '') echo "<h3><i class='udbsap'></i> &nbsp; {$section['title']} </h3>";
                        foreach ($section['items'] as $page_id => $menu_item) {
                            $menu_url = get_permalink(get_the_ID()) . ($page_id != '' ? '?udb_page=' . $page_id : '');
                            if (isset($params['flaturl']) && $params['flaturl'] == 1)
                                $menu_url = get_permalink(get_the_ID()) . $page_id . ($page_id != '' ? '/' : '');
                            ?>
                            <a class="udb-item <?php echo $udb_page == $page_id ? 'selected' : ''; ?>"
                               href="<?php echo $menu_url; ?>"><i
                                        class="<?php echo isset($menu_item['icon']) ? $menu_item['icon'] : (isset($default_icons[$page_id]) ? $default_icons[$page_id] : 'fab fa-buffer'); ?> mr-3"></i><?php echo $menu_item['name']; ?>
                            </a>
                        <?php }
                        echo "</div>";
                    }
                }
                ?>
                <a class="udb-item" href="<?php echo wpdm_logout_url(); ?>"><i class="fas fa-sign-out-alt color-danger mr-3"></i><span class="color-red"><?php _e('Logout', 'wmdpro'); ?></span></a>

            </div>

            <?php do_action("wpdm_user_dashboard_sidebar") ?>

        </div>
        <div class="col-md-9" id="wpdm-dashboard-contents">


            <?php echo isset($dashboard_contents) ? $dashboard_contents : ''; ?>


        </div>





    </div>
</div>



