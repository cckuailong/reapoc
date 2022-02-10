<?php
/**
 * Reset some theme mod from url in admin area - administrators only
 */
add_action('admin_init', 'futurio_extra_reset_mod');

function futurio_extra_reset_mod() {
    $current_user = wp_get_current_user();
    // This will help you reset some theme mods from URL. Perfect if you put something wrong into custom code and it will break your page
    // Code example:
    // Reset code added to the HEAD: ?reset-theme-mods=1&option=header-code
    // Reset code added to the footer: ?reset-theme-mods=1&option=footer-code
    // You can use it if you are site administrator and your are in admin area.
    if (isset($_GET['reset-theme-mods']) && '1' === $_GET['reset-theme-mods'] && $_GET['option'] != '' && is_admin() !== false && current_user_can('administrator')) {
        remove_theme_mod($_GET['option']);
    }
}

function futurio_extra_col() {
    if (futurio_extra_check_for_futurio_pro()) {
        return 'color';
    } else {
        return;
    }
}

function futurio_extra_g_fonts() {
    if (futurio_extra_check_for_futurio_pro()) {
        $fonts = array();
    } else {
        $fonts = array(
            'fonts' => array(
                'google' => array(
                    'Roboto',
                    'Open Sans',
                    'Lato',
                    'Roboto Condensed',
                    'Slabo 27px',
                    'Montserrat',
                    'Oswald',
                    'Source Sans Pro',
                    'Raleway',
                    'Merriweather',
                ),
            ),
        );
    }
    return $fonts;
}

function futurio_extra_adjust_customizer_responsive_sizes() {

    $mobile_margin_left = '-240px'; //Half of -$mobile_width
    $mobile_width = '480px';
    $mobile_height = '720px';

    $tablet_margin_left = '-384px'; //Half of -$tablet_width
    $tablet_width = '768px';
    $tablet_height = '925px';
    ?>
    <style>
        .wp-customizer .preview-mobile .wp-full-overlay-main {
            margin-left: <?php echo $mobile_margin_left; ?>;
            width: <?php echo $mobile_width; ?>;
            height: <?php echo $mobile_height; ?>;
        }

        .wp-customizer .preview-tablet .wp-full-overlay-main {
            margin-left: <?php echo $tablet_margin_left; ?>;
            width: <?php echo $tablet_width; ?>;
            height: <?php echo $tablet_height; ?>;
        }
    </style>
    <?php
}

add_action('customize_controls_print_styles', 'futurio_extra_adjust_customizer_responsive_sizes');

if (!futurio_extra_check_for_futurio_pro()) {
    Kirki::add_section('futurio_pro_customizer', array(
        'priority' => 1,
        'title' => esc_html__('Futurio PRO Available!', 'futurio-extra'),
        'type' => 'link',
        'button_text' => 'Go PRO',
        'button_url' => 'https://futuriowp.com/futurio-pro/',
    ));

    $sections = array('main_colors_section', 'post_page_colors_section', 'archive_colors_section', 'footer_typography_section', 'header_colors_section', 'main_menu_colors_section', 'top_bar_colors_section', 'sidebar_widget_section', 'presets_colors_section');
    foreach ($sections as $keys) {
        Kirki::add_field('futurio_extra', array(
            'type' => 'custom',
            'priority' => 30,
            'settings' => $keys . '_pro_link',
            'label' => esc_html__('Custom Colors and All Google Fonts Available in Futurio PRO!', 'futurio-extra'),
            'default' => '<a href="https://futuriowp.com/futurio-pro/#pro-features" class="button button-primary" target="_blank" rel="noopener">' . esc_html__('Learn More', 'futurio-extra') . '</a>',
            'section' => $keys,
        ));
    }

    $sections = array('global_section', 'main_menu_icons', 'blog_posts', 'main_sidebar');
    foreach ($sections as $keys) {
        Kirki::add_field('futurio_extra', array(
            'type' => 'custom',
            'priority' => 30,
            'settings' => $keys . '_pro_link',
            'label' => esc_html__('More Options Available in Futurio PRO!', 'futurio-extra'),
            'default' => '<a href="https://futuriowp.com/futurio-pro/#pro-features" class="button button-primary" target="_blank" rel="noopener">' . esc_html__('Learn More', 'futurio-extra') . '</a>',
            'section' => $keys,
        ));
    }
    $sections = array('woo_section', 'main_typography_woo_archive_section', 'main_typography_woo_product_section', 'woo_global_buttons_section', 'woo_global_other_section');
    foreach ($sections as $keys) {
        Kirki::add_field('futurio_extra', array(
            'type' => 'custom',
            'priority' => 30,
            'settings' => $keys . '_pro_link',
            'label' => esc_html__('More WooCommerce Options Available in Futurio PRO!', 'futurio-extra'),
            'default' => '<a href="https://futuriowp.com/futurio-pro/#woocommerce" class="button button-primary" target="_blank" rel="noopener">' . esc_html__('Learn More', 'futurio-extra') . '</a>',
            'section' => $keys,
        ));
    }
}