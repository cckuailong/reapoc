<?php
/*
 * Plugin Name: Futurio Extra
 * Plugin URI: https://futuriowp.com/
 * Description: Extra addon for Futurio Theme
 * Version: 1.6.2
 * Author: FuturioWP
 * Author URI: https://futuriowp.com/
 * License: GPL-2.0+
 * WC requires at least: 3.3.0
 * WC tested up to: 5.6.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('add_action')) {
    die('Nothing to do...');
}

$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$plugin_version = $plugin_data['Version'];
// Define WC_PLUGIN_FILE.
if (!defined('FUTURIO_EXTRA_CURRENT_VERSION')) {
    define('FUTURIO_EXTRA_CURRENT_VERSION', $plugin_version);
}

//plugin constants
define('FUTURIO_EXTRA_PATH', plugin_dir_path(__FILE__));
define('FUTURIO_EXTRA_PLUGIN_BASE', plugin_basename(__FILE__));
define('FUTURIO_EXTRA_PLUGIN_URL', plugins_url('/', __FILE__));


add_action('plugins_loaded', 'futurio_extra_load_textdomain');

function futurio_extra_load_textdomain() {
    load_plugin_textdomain('futurio-extra', false, basename(dirname(__FILE__)) . '/languages/');
}

function futurio_extra_scripts() {
    wp_enqueue_style('futurio-extra', plugin_dir_url(__FILE__) . 'css/style.css', array(), FUTURIO_EXTRA_CURRENT_VERSION);
    wp_enqueue_script('futurio-extra-js', plugin_dir_url(__FILE__) . 'js/futurio-extra.js', array('jquery'), FUTURIO_EXTRA_CURRENT_VERSION, true);
}

add_action('wp_enqueue_scripts', 'futurio_extra_scripts');

/**
 * Enqueue script for custom customize control.
 */
function futurio_extra_customize_enqueue() {
    wp_enqueue_style('futurio-extra-customizer', plugin_dir_url(__FILE__) . 'css/admin/customizer.css', array(), FUTURIO_EXTRA_CURRENT_VERSION);
    wp_enqueue_style('font-awesome-css', plugin_dir_url(__FILE__) . 'include/assets/vendor/fontawesome/css/font-awesome.min.css', array(), '4.7.0');
}

add_action('customize_controls_print_footer_scripts', 'futurio_extra_customize_enqueue', 10);

/**
 * Footer copyright function
 */
if (!function_exists('futurio_extra_text')) {

    function futurio_extra_text($rewritetexts) {

        $currentyear = date('Y');
        $copy = '&copy;';

        return str_replace(
                array('%current_year%', '%copy%'), array($currentyear, $copy), $rewritetexts
        );
    }

}

add_filter('futurio_extra_footer_text', 'futurio_extra_text');

/**
 * Footer extra actions - footer text and preloader
 */
function futurio_extra_action() {

    remove_action('futurio_generate_footer', 'futurio_generate_construct_footer');
    add_action('futurio_generate_footer', 'futurio_extra_generate_construct_footer');
    add_action('futurio_header_body', 'futurio_extra_preloader');
}

add_action('after_setup_theme', 'futurio_extra_action', 0);

/**
 * Footer footer text
 */
function futurio_extra_generate_construct_footer() {
    if (get_theme_mod('custom_footer', '') != '' && futurio_extra_check_for_elementor()) {
        $elementor_section_ID = get_theme_mod('custom_footer', '');
        ?>
        <footer id="colophon" class="elementor-footer-credits">
            <?php echo do_shortcode('[elementor-template id="' . $elementor_section_ID . '"]'); ?>	
        </footer>
    <?php } elseif (get_theme_mod('footer-credits', '') != '') { ?>
        <footer id="colophon" class="footer-credits container-fluid">
            <div class="container">
                <div class="footer-credits-text text-center">
                    <?php echo apply_filters('futurio_extra_footer_text', get_theme_mod('footer-credits', '')); ?>
                </div>
            </div>	
        </footer>
    <?php } else { ?>
        <footer id="colophon" class="footer-credits container-fluid">
            <div class="container">
                <div class="footer-credits-text text-center">
                    <?php printf(__('Proudly powered by %s', 'futurio-extra'), '<a href="' . esc_url(__('https://wordpress.org/', 'futurio-extra')) . '">WordPress</a>'); ?>
                    <span class="sep"> | </span>
                    <?php printf(__('Theme: %1$s', 'futurio-extra'), '<a href="https://futuriowp.com/" title="Free Multi-Purpose WordPress Theme">Futurio</a>'); ?>
                </div>
            </div>	
        </footer>
        <?php
    }
}

/**
 * Footer footer preloader
 */
function futurio_extra_preloader() {
    if (get_theme_mod('site_preloader', 0) == 1) :
        ?>
        <div id="loader-wrapper">
            <div id="loader"></div>

            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>

        </div>
        <?php
    endif;
}

if (!class_exists('Kirki')) {
    include_once( plugin_dir_path(__FILE__) . 'include/kirki.php' );
}

/**
 * Remove Kirki telemetry
 */
function futurio_extra_remove_kirki_module($modules) {
    unset($modules['telemetry']);
    unset($modules['gutenberg']);
    return $modules;
}

add_filter('kirki_modules', 'futurio_extra_remove_kirki_module');
add_filter( 'kirki_telemetry', '__return_false' );

/**
 * Add Kirki CSS into a file
 */
function futurio_extra_stylesheet() {
    $output = get_theme_mod('css_stylesheet', '0');
    return $output;
}

add_filter('kirki_output_inline_styles', 'futurio_extra_stylesheet');

/* Register the config */
Kirki::add_config('futurio_extra', array(
    'capability' => 'edit_theme_options',
    'option_type' => 'theme_mod',
));

/* Make the CSS of kirki tabs available after switch */
add_filter('kirki_futurio_extra_webfonts_skip_hidden', '__return_false', 99);
add_filter('kirki_futurio_extra_css_skip_hidden', '__return_false', 99);

add_action('plugins_loaded', 'futurio_extra_check_for_woocommerce');

require_once( plugin_dir_path(__FILE__) . 'options/extra.php' );

function futurio_extra_check_for_woocommerce() {
    if (!defined('WC_VERSION')) {
        // no woocommerce :(
    } else {
        require_once( plugin_dir_path(__FILE__) . 'options/woocommerce.php' );
    }
}

require_once( plugin_dir_path(__FILE__) . 'options/colors-typography.php' );
require_once( plugin_dir_path(__FILE__) . 'options/footer-credits.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-archive.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-posts-pages.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-presets.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-top-bar.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-header-title.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-main-menu.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-widget.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-footer-widget.php' );
require_once( plugin_dir_path(__FILE__) . 'options/colors-typography-footer-credits.php' );
require_once( plugin_dir_path(__FILE__) . 'options/header.php' );
require_once( plugin_dir_path(__FILE__) . 'options/global.php' );
require_once( plugin_dir_path(__FILE__) . 'options/top-bar.php' );
require_once( plugin_dir_path(__FILE__) . 'options/menu-icons.php' );
require_once( plugin_dir_path(__FILE__) . 'options/posts-pages.php' );
require_once( plugin_dir_path(__FILE__) . 'options/sidebar.php' );
require_once( plugin_dir_path(__FILE__) . 'options/custom-codes.php' );

require_once( plugin_dir_path(__FILE__) . 'options/demo-import.php' );
require_once( plugin_dir_path(__FILE__) . 'options/documentation.php' );
require_once( plugin_dir_path(__FILE__) . 'options/footer-credits.php' );

if (!class_exists('DilazMetabox')) {
    require_once( plugin_dir_path(__FILE__) . 'lib/metabox/dilaz-metabox.php' );
}
if (class_exists('DilazMetabox')) {
    require_once( plugin_dir_path(__FILE__) . 'lib/metabox/metabox-config.php' );
}

require_once( plugin_dir_path(__FILE__) . 'lib/shortcodes/shortcodes.php' );

require_once( plugin_dir_path(__FILE__) . 'lib/notify.php' );

include_once( plugin_dir_path(__FILE__) . 'lib/widgets.php' );

include_once( plugin_dir_path(__FILE__) . 'lib/demo/futurio-demos.php' );
include_once( plugin_dir_path(__FILE__) . 'lib/admin/dashboard.php' );
include_once( plugin_dir_path(__FILE__) . 'lib/admin/redirect.php' );

add_action('customize_register', 'futurio_extra_theme_customize_register', 99);

function futurio_extra_theme_customize_register($wp_customize) {

    $wp_customize->remove_control('header_textcolor');
    $wp_customize->remove_section('futurio_page_view_pro');
}

function futurio_extra_get_meta($name = '', $output = '') {
    if (is_singular(array('post', 'page')) || ( function_exists('is_shop') && is_shop() )) {
        global $post;
        if (( function_exists('is_shop') && is_shop())) {
            $post_id = get_option('woocommerce_shop_page_id');
            ;
        } else {
            $post_id = $post->ID;
        }
        $meta = get_post_meta($post_id, 'futurio_meta_' . $name, true);
        if (isset($meta) && $meta != '') {
            if ($output == 'echo') {
                echo esc_html($meta);
            } else {
                return $meta;
            }
        } else {
            return;
        }
    }
}

/**
 * Add custom CSS styles
 */
function futurio_extra_enqueue_header_custom_css() {

    $image = futurio_extra_get_meta('featured_image_height');
    $content_spacing = futurio_extra_get_meta('content_spacing_option');
    $spacing = futurio_extra_get_meta('content_spacing');
    // $spacing_padding = ( $spacing / 2 );
    $css = '';

    if ($image != '' && $image != '0') {
        $css .= '.full-head-img {
    padding-bottom: ' . absint($image) . 'px;
    padding-top: ' . absint($image) . 'px;
    }';
    }

    if ($content_spacing == 'enable' && $spacing != '') {
        $css .= '.futurio-content.main-content-page, .futurio-content.single-content, .futurio-woo-content {
    padding-left: ' . $spacing . '%;
    padding-right: ' . $spacing . '%;
    }';
    }

    wp_add_inline_style('futurio-stylesheet', $css);
}

add_action('wp_enqueue_scripts', 'futurio_extra_enqueue_header_custom_css', 9999);

if (!function_exists('futurio_extra_widget_date_comments')) :

    /**
     * Returns date for widgets.
     */
    function futurio_extra_widget_date_comments() {
        ?>
        <span class="extra-posted-date">
            <?php echo esc_html(get_the_date()); ?>
        </span>
        <span class="extra-comments-meta">
            <?php
            if (!comments_open()) {
                esc_html_e('Off', 'futurio-extra');
            } else {
                ?>
                <a href="<?php echo esc_url(get_comments_link()); ?>" rel="nofollow" title="<?php esc_html_e('Comment on ', 'futurio-extra') . the_title_attribute(); ?>">
                    <?php echo absint(get_comments_number()); ?>
                </a>
            <?php } ?>
            <i class="fa fa-comments-o"></i>
        </span>
        <?php
    }

endif;

register_activation_hook(__FILE__, 'futurio_extra_plugin_activate');
add_action('admin_init', 'futurio_extra_plugin_redirect');

function futurio_extra_plugin_activate() {
    add_option('fe_plugin_do_activation_redirect', true);
}

/**
 * Redirect after plugin activation
 */
function futurio_extra_plugin_redirect() {
    if (get_option('fe_plugin_do_activation_redirect', false)) {
        delete_option('fe_plugin_do_activation_redirect');
        if (!is_network_admin() || !isset($_GET['activate-multi'])) {
            wp_redirect('themes.php?page=futurio-panel-install-demos');
        }
    }
}

/**
 * Check Elementor plugin
 */
function futurio_extra_check_for_elementor() {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    return is_plugin_active('elementor/elementor.php');
}

/**
 * Check Elementor PRO plugin
 */
function futurio_extra_check_for_elementor_pro() {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    return is_plugin_active('elementor-pro/elementor-pro.php');
}

/**
 * Register Elementor features
 */
if (futurio_extra_check_for_elementor()) {
    include_once( plugin_dir_path(__FILE__) . 'lib/elementor/widgets.php' );
    if (!futurio_extra_check_for_elementor_pro()) {
        include_once( plugin_dir_path(__FILE__) . 'lib/elementor/shortcode.php' );
    }
}

/**
 * Check PRO plugin
 */
function futurio_extra_check_for_futurio_pro() {
    if (in_array('futurio-pro/futurio-pro.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return true;
    }
    return;
}

/**
 * Custom background image - posts/pages
 */
function futurio_extra_custom_field_background() {
    $background = futurio_extra_get_meta('image_bg');
    $position = futurio_extra_get_meta('image_bg_position');
    $position = str_replace('_', ' ', $position);
    if ($background && ( is_singular(array('post', 'page')) || ( function_exists('is_shop') && is_shop() ) )) {
        ?>
        <style type="text/css">
            body#blog { 
                background-image: url( "<?php echo esc_url(wp_get_attachment_url(absint($background[0]))); ?>" );
                background-position: <?php echo esc_attr($position); ?>; 
                background-repeat: <?php echo esc_attr(futurio_extra_get_meta('image_bg_repeat')); ?>;
                background-size: <?php echo esc_attr(futurio_extra_get_meta('image_bg_size')); ?>;
                background-attachment: <?php echo esc_attr(futurio_extra_get_meta('image_bg_attachment')); ?>;
            }
        </style>
        <?php
    }
}

add_action('wp_head', 'futurio_extra_custom_field_background');

/**
 * Add Metadata on plugin activation.
 */
function futurio_extra_activate() {
    add_site_option('futurio_active_time', time());
    add_site_option('futurio_active_time', time());
    add_option( 'kirki_telemetry_no_consent', true );
}

register_activation_hook(__FILE__, 'futurio_extra_activate');

/**
 * Remove Metadata on plugin Deactivation.
 */
function futurio_extra_deactivate() {
    delete_option('futurio_active_time');
    delete_option('futurio_maybe_later');
    delete_option('futurio_review_dismiss');
    delete_option('futurio_active_time');
}

register_deactivation_hook(__FILE__, 'futurio_extra_deactivate');


add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'futurio_pro_action_links');

function futurio_pro_action_links($links) {
    $links['install_demos'] = sprintf('<a href="%1$s" class="install-demos">%2$s</a>', esc_url(admin_url('themes.php?page=futurio-panel-install-demos')), esc_html__('Install Demos', 'futurio-extra'));
    if (!futurio_extra_check_for_futurio_pro()) {
        $links['go_pro'] = sprintf('<a href="%1$s" target="_blank" class="elementor-plugins-gopro">%2$s</a>', esc_url('https://futuriowp.com/futurio-pro/'), esc_html__('Go Pro', 'futurio-extra'));
    }
    return $links;
}

function megamenu_add_theme_futurio_1547210075($themes) {
    $themes["futurio_mega_menu"] = array(
        'title' => 'Futurio',
        'container_background_from' => 'rgba(34, 34, 34, 0)',
        'container_background_to' => 'rgba(34, 34, 34, 0)',
        'arrow_up' => 'dash-f343',
        'arrow_down' => 'dash-f347',
        'arrow_left' => 'dash-f341',
        'arrow_right' => 'dash-f345',
        'menu_item_link_height' => '80px',
        'menu_item_link_color' => 'rgb(51, 51, 51)',
        'panel_background_from' => 'rgb(255, 255, 255)',
        'panel_background_to' => 'rgb(255, 255, 255)',
        'panel_width' => '.container, .elementor-container',
        'panel_border_color' => 'rgb(173, 173, 173)',
        'panel_border_left' => '1px',
        'panel_border_right' => '1px',
        'panel_border_top' => '3px',
        'panel_border_bottom' => '1px',
        'panel_header_border_color' => '#555',
        'panel_font_size' => '14px',
        'panel_font_color' => '#666',
        'panel_font_family' => 'inherit',
        'panel_second_level_font_color' => '#555',
        'panel_second_level_font_color_hover' => '#555',
        'panel_second_level_text_transform' => 'uppercase',
        'panel_second_level_font' => 'inherit',
        'panel_second_level_font_size' => '16px',
        'panel_second_level_font_weight' => 'bold',
        'panel_second_level_font_weight_hover' => 'bold',
        'panel_second_level_text_decoration' => 'none',
        'panel_second_level_text_decoration_hover' => 'none',
        'panel_second_level_border_color' => '#555',
        'panel_third_level_font_color' => '#666',
        'panel_third_level_font_color_hover' => '#666',
        'panel_third_level_font' => 'inherit',
        'panel_third_level_font_size' => '14px',
        'flyout_menu_background_from' => 'rgb(255, 255, 255)',
        'flyout_menu_background_to' => 'rgb(255, 255, 255)',
        'flyout_border_color' => 'rgb(221, 221, 221)',
        'flyout_border_left' => '1px',
        'flyout_border_right' => '1px',
        'flyout_border_top' => '3px',
        'flyout_border_bottom' => '1px',
        'flyout_background_from' => 'rgb(255, 255, 255)',
        'flyout_background_to' => 'rgb(255, 255, 255)',
        'flyout_link_size' => '14px',
        'flyout_link_color' => '#666',
        'flyout_link_color_hover' => '#666',
        'flyout_link_family' => 'inherit',
        'line_height' => '1.6',
        'shadow' => 'on',
        'shadow_color' => 'rgba(0, 0, 0, 0.39)',
        'toggle_background_from' => 'rgba(34, 34, 34, 0)',
        'toggle_background_to' => 'rgba(34, 34, 34, 0)',
        'toggle_bar_height' => '80px',
        'mobile_background_from' => '#222',
        'mobile_background_to' => '#222',
        'mobile_menu_item_link_font_size' => '14px',
        'mobile_menu_item_link_color' => '#ffffff',
        'mobile_menu_item_link_text_align' => 'left',
        'mobile_menu_item_link_color_hover' => '#ffffff',
        'mobile_menu_item_background_hover_from' => '#333',
        'mobile_menu_item_background_hover_to' => '#333',
        'custom_css' => '/** Push menu onto new line **/
@include mobile{
#{$wrap} { 
    clear: both;
    position: absolute;
    top: 50%;
    right: 0;
    left: 0;
    margin-top: -40px;
}
}',
    );
    return $themes;
}

add_filter("megamenu_themes", "megamenu_add_theme_futurio_1547210075");

function futurio_extra_megamenu_override_default_theme($value) {
    // change 'primary' to your menu location ID
    if (!isset($value['main_menu']['theme'])) {
        $value['main_menu']['theme'] = 'futurio_mega_menu'; // change my_custom_theme_key to the ID of your exported theme
        $value['main_menu_home']['theme'] = 'futurio_mega_menu';
    }

    return $value;
}

add_filter('default_option_megamenu_settings', 'futurio_extra_megamenu_override_default_theme');

/**
 * Hide custom fields on pages - not used
 */
function futurio_extra_hide_custom_fields_postbox() {
    global $pagenow;
    if (( $pagenow == 'post.php' ) || (get_post_type() == 'post')) {
        global $post;
        $post_id = get_option('woocommerce_shop_page_id');
        $id = $post->ID;
        if (is_admin() && 'page' == get_post_type()) {
            ?>
            <style type="text/css">
                div#dilaz-mb-field-futurio_meta_disable_navigation, div#dilaz-mb-field-futurio_meta_disable_author_box, div#dilaz-mb-field-futurio_meta_disable_comments, div#dilaz-mb-field-futurio_meta_disable_meta, div#dilaz-mb-field-futurio_meta_disable_cats_tags {display:none !important;}
            </style>
            <?php
        }
        if (is_admin() && 'page' == get_post_type() && $post_id == $id) {
            ?>
            <style type="text/css">
                li.dilaz-mb-tabs-nav-item:nth-child(2), li.dilaz-mb-tabs-nav-item:nth-child(5) {display: none;}
            </style>
            <?php
        }
    }
}

add_action('admin_head', 'futurio_extra_hide_custom_fields_postbox');

remove_filter( 'wp_import_post_meta', 'Elementor\Compatibility::on_wp_import_post_meta');
remove_filter( 'wxr_importer.pre_process.post_meta', 'Elementor\Compatibility::on_wxr_importer_pre_process_post_meta');
