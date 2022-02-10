<?php
/**
 * Demos
 *
 * @package Futurio_Extra
 * @category Core
 * @author FuturioWP
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Start Class
if (!class_exists('FuturioWP_Demos')) {

    class FuturioWP_Demos {

        /**
         * Start things up
         */
        public function __construct() {

            // Return if not in admin
            if (!is_admin() || is_customize_preview()) {
                return;
            }

            // Import demos page
            if (version_compare(PHP_VERSION, '5.4', '>=')) {
                require_once( FE_PATH . '/classes/importers/class-helpers.php' );
                require_once( FE_PATH . '/classes/class-install-demos.php' );
            }

            // Disable Woo Wizard
            add_filter('woocommerce_enable_setup_wizard', '__return_false');
            add_filter('woocommerce_show_admin_notice', '__return_false');
            add_filter('woocommerce_prevent_automatic_wizard_redirect', '__return_false');

            // Start things
            add_action('admin_init', array($this, 'init'));

            // Demos scripts
            add_action('admin_enqueue_scripts', array($this, 'scripts'));

            // Allows xml uploads
            add_filter('upload_mimes', array($this, 'allow_xml_uploads'));

            // Demos popup
            add_action('admin_footer', array($this, 'popup'));
        }

        /**
         * Register the AJAX methods
         *
         * @since 1.0.0
         */
        public function init() {

            // Demos popup ajax
            add_action('wp_ajax_futurio_ajax_get_demo_data', array($this, 'ajax_demo_data'));
            add_action('wp_ajax_futurio_ajax_required_plugins_activate', array($this, 'ajax_required_plugins_activate'));

            // Get data to import
            add_action('wp_ajax_futurio_ajax_get_import_data', array($this, 'ajax_get_import_data'));

            // Import XML file
            add_action('wp_ajax_futurio_ajax_import_xml', array($this, 'ajax_import_xml'));

            // Import customizer settings
            add_action('wp_ajax_futurio_ajax_import_theme_settings', array($this, 'ajax_import_theme_settings'));

            // Import widgets
            add_action('wp_ajax_futurio_ajax_import_widgets', array($this, 'ajax_import_widgets'));

            // Reset theme mods
            add_action('wp_ajax_futurio_ajax_reset_mods', array($this, 'ajax_reset_mods'));

            // After import
            add_action('wp_ajax_futurio_after_import', array($this, 'ajax_after_import'));
        }

        /**
         * Load scripts
         *
         * @since 1.4.5
         */
        public static function scripts($hook_suffix) {

            if ('appearance_page_futurio-panel-install-demos' == $hook_suffix) {

                // CSS
                wp_enqueue_style('fwp-demos-style', plugins_url('/assets/css/demos.min.css', __FILE__));

                // JS
                wp_enqueue_script('fwp-demos-js', plugins_url('/assets/js/demos.min.js', __FILE__), array('jquery', 'wp-util', 'updates'), '1.0', true);

                wp_localize_script('fwp-demos-js', 'futurioDemos', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'demo_data_nonce' => wp_create_nonce('get-demo-data'),
                    'futurio_import_data_nonce' => wp_create_nonce('futurio_import_data_nonce'),
                    'content_importing_error' => esc_html__('There was a problem during the importing process resulting in the following error from your server:', 'futurio-extra'),
                    'button_activating' => esc_html__('Activating', 'futurio-extra') . '&hellip;',
                    'button_active' => esc_html__('Active', 'futurio-extra'),
                ));
            }
        }

        /**
         * Allows xml uploads so we can import from github
         *
         * @since 1.0.0
         */
        public function allow_xml_uploads($mimes) {
            $mimes = array_merge($mimes, array(
                'xml' => 'application/xml'
            ));
            return $mimes;
        }

        /**
         * Get demos data to add them in the Demo Import and Pro Demos plugins
         *
         * @since 1.4.5
         */
        public static function get_demos_data() {

            // Demos url
            $url = 'https://futuriodemos.com/wp-content/uploads/demos/';

            $data = array(
                'ocean-demo' => array(
                    'categories' => array('Elementor', 'Business', 'Free'),
                    'xml_file' => $url . 'ocean-demo/sample-data.xml',
                    'theme_settings' => $url . 'ocean-demo/futurio-export.json',
                    'widgets_file' => $url . 'ocean-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '6',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                    'premium' => array(),
                    'recommended' => array(),
                    ),
                ),
                'the-shopper-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'the-shopper-pro/sample-data.xml',
                    'theme_settings' => $url . 'the-shopper-pro/futurio-export.json',
                    'widgets_file' => $url . 'the-shopper-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'business-store-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor', 'Business'),
                    'xml_file' => $url . 'business-store-pro/sample-data.xml',
                    'theme_settings' => $url . 'business-store-pro/futurio-export.json',
                    'widgets_file' => $url . 'business-store-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_crop_width' => '3',
                    'woo_crop_height' => '4',
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'contact-form-7',
                                'init' => 'contact-form-7/wp-contact-form-7.php',
                                'name' => 'Contact Form 7',
                            ),
                            array(
                                'slug' => 'ajax-search-for-woocommerce',
                                'init' => 'ajax-search-for-woocommerce/ajax-search-for-woocommerce.php',
                                'name' => 'Ajax Search for WooCommerce',
                            ),
                        ),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'woocommerce-shoes-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'woocommerce-shoes-pro/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-shoes-pro/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-shoes-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'yith-woocommerce-wishlist',
                                'init' => 'yith-woocommerce-wishlist/init.php',
                                'name' => 'YITH WooCommerce Wishlist',
                            ),
                            array(
                                'slug' => 'yith-woocommerce-quick-view',
                                'init' => 'yith-woocommerce-quick-view/init.php',
                                'name' => 'YITH WooCommerce Quick View',
                            ),
                            array(
                                'slug' => 'yith-woocommerce-compare',
                                'init' => 'yith-woocommerce-compare/init.php',
                                'name' => 'YITH WooCommerce Compare',
                            ),
                        ),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'woocommerce-store-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'woocommerce-store-pro/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-store-pro/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-store-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'woocommerce-marketplace-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'woocommerce-marketplace-pro/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-marketplace-pro/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-marketplace-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'default-demo' => array(
                    'categories' => array('Business', 'Elementor', 'Free'),
                    'xml_file' => $url . 'default/sample-data.xml',
                    'theme_settings' => $url . 'default/futurio-export.json',
                    'widgets_file' => $url . 'default/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'architect-demo' => array(
                    'categories' => array('Business', 'Elementor', 'Free'),
                    'xml_file' => $url . 'architect-demo/sample-data.xml',
                    'theme_settings' => $url . 'architect-demo/futurio-export.json',
                    'widgets_file' => $url . 'architect-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '6',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'star-demo' => array(
                    'categories' => array('One Page', 'Elementor', 'Business', 'Free'),
                    'xml_file' => $url . 'star-demo/sample-data.xml',
                    'theme_settings' => $url . 'star-demo/futurio-export.json',
                    'widgets_file' => $url . 'star-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'product-landing-page-pro' => array(
                    'categories' => array('Business', 'Elementor', 'One Page', 'Landing Page'),
                    'xml_file' => $url . 'product-landing-page-pro/sample-data.xml',
                    'theme_settings' => $url . 'product-landing-page-pro/futurio-export.json',
                    'widgets_file' => $url . 'product-landing-page-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'woocommerce-fashion' => array(
                    'categories' => array('WooCommerce', 'Elementor', 'Free'),
                    'xml_file' => $url . 'woocommerce-fashion/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-fashion/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-fashion/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'woo_crop_width' => '3',
                    'woo_crop_height' => '4',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'ajax-search-for-woocommerce',
                                'init' => 'ajax-search-for-woocommerce/ajax-search-for-woocommerce.php',
                                'name' => 'Ajax Search for WooCommerce',
                            ),
                        ),
                        'premium' => array(),
                    ),
                ),
                'woocommerce-watches-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'woocommerce-watches-pro/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-watches-pro/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-watches-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_crop_width' => '3',
                    'woo_crop_height' => '4',
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'ajax-search-for-woocommerce',
                                'init' => 'ajax-search-for-woocommerce/ajax-search-for-woocommerce.php',
                                'name' => 'Ajax Search for WooCommerce',
                            ),
                        ),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'elementor-blog-demo' => array(
                    'categories' => array('Blog', 'Elementor', 'Free'),
                    'xml_file' => $url . 'elementor-blog-demo/sample-data.xml',
                    'theme_settings' => $url . 'elementor-blog-demo/futurio-export.json',
                    'widgets_file' => $url . 'elementor-blog-demo/widgets.wie',
                    'posts_to_show' => '6',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'business-demo' => array(
                    'categories' => array('Business', 'Elementor', 'Free'),
                    'xml_file' => $url . 'business-demo/sample-data.xml',
                    'theme_settings' => $url . 'business-demo/futurio-export.json',
                    'widgets_file' => $url . 'business-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'contact-form-7',
                                'init' => 'contact-form-7/wp-contact-form-7.php',
                                'name' => 'Contact Form 7',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'woocommerce-fashion-pro' => array(
                    'categories' => array('WooCommerce', 'Elementor'),
                    'xml_file' => $url . 'woocommerce-fashion-pro/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-fashion-pro/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-fashion-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'woo_crop_width' => '3',
                    'woo_crop_height' => '4',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'yith-woocommerce-wishlist',
                                'init' => 'yith-woocommerce-wishlist/init.php',
                                'name' => 'YITH WooCommerce Wishlist',
                            ),
                            array(
                                'slug' => 'yith-woocommerce-quick-view',
                                'init' => 'yith-woocommerce-quick-view/init.php',
                                'name' => 'YITH WooCommerce Quick View',
                            ),
                            array(
                                'slug' => 'yith-woocommerce-compare',
                                'init' => 'yith-woocommerce-compare/init.php',
                                'name' => 'YITH WooCommerce Compare',
                            ),
                        ),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'woocommerce-electronics' => array(
                    'categories' => array('WooCommerce', 'Elementor', 'Free'),
                    'xml_file' => $url . 'woocommerce-electronics/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-electronics/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-electronics/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'woo_crop_width' => '4',
                    'woo_crop_height' => '3',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'ajax-search-for-woocommerce',
                                'init' => 'ajax-search-for-woocommerce/ajax-search-for-woocommerce.php',
                                'name' => 'Ajax Search for WooCommerce',
                            ),
                        ),
                        'premium' => array(),
                    ),
                ),
                'creative-demo-pro' => array(
                    'categories' => array('One Page', 'Business', 'Elementor'),
                    'xml_file' => $url . 'creative-demo-pro/sample-data.xml',
                    'theme_settings' => $url . 'creative-demo-pro/futurio-export.json',
                    'widgets_file' => $url . 'creative-demo-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '6',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'fitness-trainer-pro' => array(
                    'categories' => array('One Page', 'Elementor'),
                    'xml_file' => $url . 'fitness-trainer-pro/sample-data.xml',
                    'theme_settings' => $url . 'fitness-trainer-pro/futurio-export.json',
                    'widgets_file' => $url . 'fitness-trainer-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => '',
                    'posts_to_show' => '6',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'gym-demo' => array(
                    'categories' => array('One Page', 'Elementor', 'Business', 'Free'),
                    'xml_file' => $url . 'gym-demo/sample-data.xml',
                    'theme_settings' => $url . 'gym-demo/futurio-export.json',
                    'widgets_file' => $url . 'gym-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'woocommerce-snowboard' => array(
                    'categories' => array('WooCommerce', 'Elementor', 'Free'),
                    'xml_file' => $url . 'woocommerce-snowboard/sample-data.xml',
                    'theme_settings' => $url . 'woocommerce-snowboard/futurio-export.json',
                    'widgets_file' => $url . 'woocommerce-snowboard/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'is_shop' => true,
                    'woo_image_size' => '600',
                    'woo_thumb_size' => '300',
                    'woo_crop_width' => '3',
                    'woo_crop_height' => '6',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                            array(
                                'slug' => 'woocommerce',
                                'init' => 'woocommerce/woocommerce.php',
                                'name' => 'WooCommerce',
                            ),
                        ),
                        'recommended' => array(
                            array(
                                'slug' => 'ajax-search-for-woocommerce',
                                'init' => 'ajax-search-for-woocommerce/ajax-search-for-woocommerce.php',
                                'name' => 'Ajax Search for WooCommerce',
                            ),
                        ),
                        'premium' => array(),
                    ),
                ),
                'lifestyle-blog' => array(
                    'categories' => array('Blog', 'Elementor', 'Free'),
                    'xml_file' => $url . 'lifestyle-blog/sample-data.xml',
                    'theme_settings' => $url . 'lifestyle-blog/futurio-export.json',
                    'widgets_file' => $url . 'lifestyle-blog/widgets.wie',
                    'home_title' => '',
                    'blog_title' => '',
                    'posts_to_show' => '7',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'coffee-demo' => array(
                    'categories' => array('One Page', 'Elementor', 'Business', 'Landing Page', 'Free'),
                    'xml_file' => $url . 'coffee-demo/sample-data.xml',
                    'theme_settings' => $url . 'coffee-demo/futurio-export.json',
                    'widgets_file' => $url . 'coffee-demo/widgets.wie',
                    'home_title' => 'Home',
                    //'blog_title'  		=> 'Blog',
                    //'posts_to_show'  	=> '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'travel-blog' => array(
                    'categories' => array('Blog', 'Free'),
                    'xml_file' => $url . 'travel-blog/sample-data.xml',
                    'theme_settings' => $url . 'travel-blog/futurio-export.json',
                    'widgets_file' => $url . 'travel-blog/widgets.wie',
                    'home_title' => '',
                    'blog_title' => '',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'one-page-demo' => array(
                    'categories' => array('One Page', 'Elementor', 'Free'),
                    'xml_file' => $url . 'one-page-demo/sample-data.xml',
                    'theme_settings' => $url . 'one-page-demo/futurio-export.json',
                    'widgets_file' => $url . 'one-page-demo/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'app-landing-page' => array(
                    'categories' => array('One Page', 'Elementor', 'Landing Page', 'Free'),
                    'xml_file' => $url . 'app-landing-page/sample-data.xml',
                    'theme_settings' => $url . 'app-landing-page/futurio-export.json',
                    'widgets_file' => $url . 'app-landing-page/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => '',
                    'posts_to_show' => '10',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'food-blog' => array(
                    'categories' => array('Blog', 'Free'),
                    'xml_file' => $url . 'food-blog/sample-data.xml',
                    'theme_settings' => $url . 'food-blog/futurio-export.json',
                    'widgets_file' => $url . 'food-blog/widgets.wie',
                    'home_title' => '',
                    'blog_title' => '',
                    'posts_to_show' => '7',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
                'restaurant-demo-pro' => array(
                    'categories' => array('Business', 'Elementor'),
                    'xml_file' => $url . 'restaurant-demo-pro/sample-data.xml',
                    'theme_settings' => $url . 'restaurant-demo-pro/futurio-export.json',
                    'widgets_file' => $url . 'restaurant-demo-pro/widgets.wie',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(
                            array(
                                'slug' => 'futurio-pro',
                                'init' => 'futurio-pro/futurio-pro.php',
                                'name' => 'Futurio PRO',
                            ),
                        ),
                    ),
                ),
                'blog-demo' => array(
                    'categories' => array('Blog', 'Free'),
                    'xml_file' => $url . 'blog-demo/sample-data.xml',
                    'theme_settings' => $url . 'blog-demo/futurio-export.json',
                    'widgets_file' => $url . 'blog-demo/widgets.wie',
                    'home_title' => '',
                    'blog_title' => '',
                    'posts_to_show' => '5',
                    'elementor_width' => '1140',
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'futurio-extra',
                                'init' => 'futurio-extra/futurio-extra.php',
                                'name' => 'Futurio Extra',
                            ),
                        ),
                        'recommended' => array(),
                        'premium' => array(),
                    ),
                ),
            );

            // Return
            return apply_filters('futurio_demos_data', $data);
        }

        /**
         * Get the category list of all categories used in the predefined demo imports array.
         *
         * @since 1.4.5
         */
        public static function get_demo_all_categories($demo_imports) {
            $categories = array();

            foreach ($demo_imports as $item) {
                if (!empty($item['categories']) && is_array($item['categories'])) {
                    foreach ($item['categories'] as $category) {
                        $categories[sanitize_key($category)] = $category;
                    }
                }
            }

            if (empty($categories)) {
                return false;
            }

            return $categories;
        }

        /**
         * Return the concatenated string of demo import item categories.
         * These should be separated by comma and sanitized properly.
         *
         * @since 1.4.5
         */
        public static function get_demo_item_categories($item) {
            $sanitized_categories = array();

            if (isset($item['categories'])) {
                foreach ($item['categories'] as $category) {
                    $sanitized_categories[] = sanitize_key($category);
                }
            }

            if (!empty($sanitized_categories)) {
                return implode(',', $sanitized_categories);
            }

            return false;
        }

        /**
         * Demos popup
         *
         * @since 1.4.5
         */
        public static function popup() {
            global $pagenow;

            // Display on the demos pages
            if (( 'themes.php' == $pagenow && isset($_GET['page']) && 'futurio-panel-install-demos' == $_GET['page'])) {
                ?>

                <div id="fwp-demo-popup-wrap">
                    <div class="fwp-demo-popup-container">
                        <div class="fwp-demo-popup-content-wrap">
                            <div class="fwp-demo-popup-content-inner">
                                <a href="#" class="fwp-demo-popup-close">×</a>
                                <div id="fwp-demo-popup-content"></div>
                            </div>
                        </div>
                    </div>
                    <div class="fwp-demo-popup-overlay"></div>
                </div>

                <?php
            }
        }

        /**
         * Demos popup ajax.
         *
         * @since 1.4.5
         */
        public static function ajax_demo_data() {



            // Database reset url
            if (is_plugin_active('wordpress-database-reset/wp-reset.php')) {
                $plugin_link = admin_url('tools.php?page=database-reset');
            } else {
                $plugin_link = admin_url('plugin-install.php?s=Wordpress+Database+Reset&tab=search');
            }

            // Get all demos
            $demos = self::get_demos_data();

            // Get selected demo
            $demo = $_GET['demo_name'];

            // Get required plugins
            $plugins = $demos[$demo]['required_plugins'];

            // Get free plugins
            $free = $plugins['free'];

            // Get recommended plugins
            $recommended = $plugins['recommended'];

            // Get premium plugins
            $premium = $plugins['premium'];
            ?>

            <div id="fwp-demo-plugins">

                <h2 class="title"><?php echo sprintf(esc_html__('Import the %1$s demo', 'futurio-extra'), esc_attr($demo)); ?></h2>

                <div class="fwp-popup-text">

                    <p><?php
                        echo
                        sprintf(
                                esc_html__('Importing demo data allow you to quickly edit everything instead of creating content from scratch. It is recommended uploading sample data on a fresh WordPress install to prevent conflicts with your current content. You can use this plugin to reset your site if needed: %1$sWordpress Database Reset%2$s.', 'futurio-extra'),
                                '<a href="' . $plugin_link . '" target="_blank">',
                                '</a>'
                        );
                        ?></p>

                    <div class="fwp-required-plugins-wrap">
                        <h3><?php esc_html_e('Required Plugins', 'futurio-extra'); ?></h3>
                        <p><?php esc_html_e('For your site to look exactly like this demo, the plugins below need to be activated.', 'futurio-extra'); ?></p>
                        <div class="fwp-required-plugins oe-plugin-installer">
                            <?php
                            self::required_plugins($free, 'free');
                            self::required_plugins($premium, 'premium');
                            ?>
                        </div>
                        <?php if (isset($recommended) && !empty($recommended)) { ?>
                            <h3><?php esc_html_e('Recommended Plugins', 'futurio-extra'); ?></h3>
                            <p><?php esc_html_e('These plugins are not required for the demo import. However, if you do not install them, some demo features will not be included.', 'futurio-extra'); ?></p>
                            <div class="fwp-required-plugins oe-plugin-installer">
                                <?php self::required_plugins($recommended, 'recommended'); ?>
                            </div>
                        <?php } ?>
                    </div>

                </div>

                <?php if (!defined('FUTURIO_PRO_CURRENT_VERSION') && !empty($premium) && $premium['0']['slug'] == 'futurio-pro') { ?>
                    <div class="fwp-button fwp-plugins-pro">
                        <a href="<?php echo esc_url('https://futuriowp.com/futurio-pro/'); ?>" target="_blank" >
                            <?php esc_html_e('Install and activate Futurio PRO', 'futurio-extra'); ?>
                        </a>
                    </div>
                <?php } elseif (defined('FUTURIO_PRO_CURRENT_VERSION') && !defined('FUTURIO_SLT_PRO') && $premium['0']['slug'] == 'futurio-pro') { ?>
                    <div class="fwp-button fwp-plugins-pro">
                        <a href="<?php echo esc_url(network_admin_url('options-general.php?page=futurio-license-options')) ?>" >
                            <?php esc_html_e('Activate Futurio PRO license', 'futurio-extra'); ?>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="fwp-button fwp-plugins-next">
                        <a href="#">
                            <?php esc_html_e('Go to the next step', 'futurio-extra'); ?>
                        </a>
                    </div>
                <?php } ?>


            </div>

            <form method="post" id="fwp-demo-import-form">

                <input id="futurio_import_demo" type="hidden" name="futurio_import_demo" value="<?php echo esc_attr($demo); ?>" />

                <div class="fwp-demo-import-form-types">

                    <h2 class="title"><?php esc_html_e('Select what you want to import:', 'futurio-extra'); ?></h2>

                    <ul class="fwp-popup-text">
                        <li>
                            <label for="futurio_reset_mods">
                                <input id="futurio_reset_mods" type="checkbox" name="futurio_reset_mods"  />
                                <strong><?php esc_html_e('Reset theme options', 'futurio-extra'); ?></strong> (<?php esc_html_e('Customizer options', 'futurio-extra'); ?>)
                            </label>
                        </li>
                        <li>
                            <label for="futurio_import_xml">
                                <input id="futurio_import_xml" type="checkbox" name="futurio_import_xml" checked="checked" />
                                <strong><?php esc_html_e('Import XML Data', 'futurio-extra'); ?></strong> (<?php esc_html_e('pages, posts, images, menus, etc...', 'futurio-extra'); ?>)
                            </label>
                        </li>

                        <li>
                            <label for="futurio_theme_settings">
                                <input id="futurio_theme_settings" type="checkbox" name="futurio_theme_settings" checked="checked" />
                                <strong><?php esc_html_e('Import Customizer Settings', 'futurio-extra'); ?></strong>
                            </label>
                        </li>

                        <li>
                            <label for="futurio_import_widgets">
                                <input id="futurio_import_widgets" type="checkbox" name="futurio_import_widgets" checked="checked" />
                                <strong><?php esc_html_e('Import Widgets', 'futurio-extra'); ?></strong>
                            </label>
                        </li>
                    </ul>

                </div>

                <?php wp_nonce_field('futurio_import_demo_data_nonce', 'futurio_import_demo_data_nonce'); ?>
                <input type="submit" name="submit" class="fwp-button fwp-import" value="<?php esc_html_e('Install this demo', 'futurio-extra'); ?>"  />

            </form>

            <div class="fwp-loader">
                <h2 class="title"><?php esc_html_e('The import process could take some time, please be patient', 'futurio-extra'); ?></h2>
                <div class="fwp-import-status fwp-popup-text"></div>
            </div>

            <div class="fwp-last">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
                <h3><?php esc_html_e('Demo Imported!', 'futurio-extra'); ?></h3>
                <a href="<?php echo esc_url(get_home_url()); ?>"" target="_blank"><?php esc_html_e('See the result', 'futurio-extra'); ?></a>
            </div>

            <?php
            die();
        }

        /**
         * Required plugins.
         *
         * @since 1.4.5
         */
        public static function required_plugins($plugins, $return) {

            foreach ($plugins as $key => $plugin) {

                $api = array(
                    'slug' => isset($plugin['slug']) ? $plugin['slug'] : '',
                    'init' => isset($plugin['init']) ? $plugin['init'] : '',
                    'name' => isset($plugin['name']) ? $plugin['name'] : '',
                );

                if (!is_wp_error($api)) { // confirm error free
                    // Installed but Inactive.
                    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['init']) && is_plugin_inactive($plugin['init'])) {

                        $button_classes = 'button activate-now button-primary';
                        $button_text = esc_html__('Activate', 'futurio-extra');

                        // Not Installed.
                    } elseif (!file_exists(WP_PLUGIN_DIR . '/' . $plugin['init'])) {

                        $button_classes = 'button install-now';
                        $button_text = esc_html__('Install Now', 'futurio-extra');

                        // Active.
                    } else {
                        $button_classes = 'button disabled';
                        $button_text = esc_html__('Activated', 'futurio-extra');
                    }
                    ?>

                    <div class="fwp-plugin fwp-clr fwp-plugin-<?php echo $api['slug']; ?>" data-slug="<?php echo $api['slug']; ?>" data-init="<?php echo $api['init']; ?>">
                        <h2><?php echo $api['name']; ?></h2>

                        <?php
                        // If premium plugins and not installed
                        if ('premium' == $return && !file_exists(WP_PLUGIN_DIR . '/' . $plugin['init'])) {
                            ?>
                            <a class="button" href="https://futuriowp.com/<?php echo $api['slug']; ?>/" target="_blank"><?php esc_html_e('Get This Addon', 'futurio-extra'); ?></a>
                        <?php } else {
                            ?>
                            <button class="<?php echo $button_classes; ?>" data-init="<?php echo $api['init']; ?>" data-slug="<?php echo $api['slug']; ?>" data-name="<?php echo $api['name']; ?>"><?php echo $button_text; ?></button>
                        <?php }
                        ?>
                    </div>

                    <?php
                }
            }
        }

        /**
         * Required plugins activate
         *
         * @since 1.4.5
         */
        public function ajax_required_plugins_activate() {

            if (!current_user_can('install_plugins') || !isset($_POST['init']) || !$_POST['init']) {
                wp_send_json_error(
                        array(
                            'success' => false,
                            'message' => __('No plugin specified', 'futurio-extra'),
                        )
                );
            }

            $plugin_init = ( isset($_POST['init']) ) ? esc_attr($_POST['init']) : '';
            $activate = activate_plugin($plugin_init, '', false, true);

            if (is_wp_error($activate)) {
                wp_send_json_error(
                        array(
                            'success' => false,
                            'message' => $activate->get_error_message(),
                        )
                );
            }

            wp_send_json_success(
                    array(
                        'success' => true,
                        'message' => __('Plugin Successfully Activated', 'futurio-extra'),
                    )
            );
        }

        /**
         * Returns an array containing all the importable content
         *
         * @since 1.4.5
         */
        public function ajax_get_import_data() {
            check_ajax_referer('futurio_import_data_nonce', 'security');

            echo json_encode(
                    array(
                        array(
                            'input_name' => 'futurio_reset_mods',
                            'action' => 'futurio_ajax_reset_mods',
                            'method' => 'ajax_reset_mods',
                            'loader' => esc_html__('Reseting Theme Options', 'futurio-extra')
                        ),
                        array(
                            'input_name' => 'futurio_import_xml',
                            'action' => 'futurio_ajax_import_xml',
                            'method' => 'ajax_import_xml',
                            'loader' => esc_html__('Importing XML Data', 'futurio-extra')
                        ),
                        array(
                            'input_name' => 'futurio_theme_settings',
                            'action' => 'futurio_ajax_import_theme_settings',
                            'method' => 'ajax_import_theme_settings',
                            'loader' => esc_html__('Importing Customizer Settings', 'futurio-extra')
                        ),
                        array(
                            'input_name' => 'futurio_import_widgets',
                            'action' => 'futurio_ajax_import_widgets',
                            'method' => 'ajax_import_widgets',
                            'loader' => esc_html__('Importing Widgets', 'futurio-extra')
                        ),
                    )
            );

            die();
        }

        /**
         * Import XML file
         *
         * @since 1.4.5
         */
        public function ajax_reset_mods() {
            if (!wp_verify_nonce($_POST['futurio_import_demo_data_nonce'], 'futurio_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }
            $save_result = 1;
            // Get the selected demo
            $demo_type = $_POST['futurio_reset_mods'];
            //just in case have these files included
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/template.php');

            //we are checking if file system can operate without FTP creds
            $url = wp_nonce_url(admin_url(), '');
            if (false === ( $creds = request_filesystem_credentials($url, '', false, false, null) )) {
                $save_result = 0;
            } elseif (!WP_Filesystem($creds)) {
                request_filesystem_credentials($url, '', true, false, null);
                $save_result = 0;
            }
            // Save the theme mods before deleting
            if ($save_result === 1) {
                global $wp_filesystem;
                $upload_dir = wp_upload_dir();
                $directory = trailingslashit($upload_dir['basedir']) . 'futurio-files';
                if (!is_dir($directory)) {
                    wp_mkdir_p($directory);
                }
                if (is_writable($directory)) {
                    $file = $directory . '/futurio' . date('Y-m-d-h-i-s') . '.json';

                    //in case of FTP access we need to make sure we have proper path
                    $file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);

                    $theme_mods = get_theme_mods();
                    $mods = array();
                    foreach ($theme_mods as $theme_mod => $value) {
                        $mods[$theme_mod] = maybe_unserialize($value);
                    }
                    $json = json_encode($mods);
                    $wp_filesystem->put_contents(
                            $file,
                            $json,
                            FS_CHMOD_FILE
                    );
                }
            }
            $result = '';

            // Get the selected demo
            remove_theme_mods();
            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }
            die();
        }

        /**
         * Import XML file
         *
         * @since 1.4.5
         */
        public function ajax_import_xml() {
            if (!wp_verify_nonce($_POST['futurio_import_demo_data_nonce'], 'futurio_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Get the selected demo
            $demo_type = $_POST['futurio_import_demo'];

            // Get demos data
            $demo = FuturioWP_Demos::get_demos_data()[$demo_type];

            // Content file
            $xml_file = isset($demo['xml_file']) ? $demo['xml_file'] : '';

            // Delete the default post and page
            $sample_page = get_page_by_path('sample-page', OBJECT, 'page');
            $hello_world_post = get_page_by_path('hello-world', OBJECT, 'post');

            if (!is_null($sample_page)) {
                wp_delete_post($sample_page->ID, true);
            }

            if (!is_null($hello_world_post)) {
                wp_delete_post($hello_world_post->ID, true);
            }

            // Import Posts, Pages, Images, Menus.
            $result = $this->process_xml($xml_file);

            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * Import customizer settings
         *
         * @since 1.4.5
         */
        public function ajax_import_theme_settings() {
            if (!wp_verify_nonce($_POST['futurio_import_demo_data_nonce'], 'futurio_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Include settings importer
            include FE_PATH . 'classes/importers/class-settings-importer.php';

            // Get the selected demo
            $demo_type = $_POST['futurio_import_demo'];

            // Get demos data
            $demo = FuturioWP_Demos::get_demos_data()[$demo_type];

            // Settings file
            $theme_settings = isset($demo['theme_settings']) ? $demo['theme_settings'] : '';

            // Import settings.
            $settings_importer = new FWP_Settings_Importer();
            $result = $settings_importer->process_import_file($theme_settings);

            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * Import widgets
         *
         * @since 1.4.5
         */
        public function ajax_import_widgets() {
            if (!wp_verify_nonce($_POST['futurio_import_demo_data_nonce'], 'futurio_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Include widget importer
            include FE_PATH . 'classes/importers/class-widget-importer.php';

            // Get the selected demo
            $demo_type = $_POST['futurio_import_demo'];

            // Get demos data
            $demo = FuturioWP_Demos::get_demos_data()[$demo_type];

            // Widgets file
            $widgets_file = isset($demo['widgets_file']) ? $demo['widgets_file'] : '';

            // Import settings.
            $widgets_importer = new FWP_Widget_Importer();
            $result = $widgets_importer->process_import_file($widgets_file);

            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * After import
         *
         * @since 1.4.5
         */
        public function ajax_after_import() {
            if (!wp_verify_nonce($_POST['futurio_import_demo_data_nonce'], 'futurio_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // If XML file is imported
            if ($_POST['futurio_import_is_xml'] === 'true') {

                // Get the selected demo
                $demo_type = $_POST['futurio_import_demo'];

                // Get demos data
                $demo = FuturioWP_Demos::get_demos_data()[$demo_type];

                // Elementor width setting
                $elementor_width = isset($demo['elementor_width']) ? $demo['elementor_width'] : '';

                // Reading settings
                $homepage_title = isset($demo['home_title']) ? $demo['home_title'] : 'Home';
                $blog_title = isset($demo['blog_title']) ? $demo['blog_title'] : '';

                // Posts to show on the blog page
                $posts_to_show = isset($demo['posts_to_show']) ? $demo['posts_to_show'] : '';

                // If shop demo
                $shop_demo = isset($demo['is_shop']) ? $demo['is_shop'] : false;

                // Product image size
                $image_size = isset($demo['woo_image_size']) ? $demo['woo_image_size'] : '';
                $thumbnail_size = isset($demo['woo_thumb_size']) ? $demo['woo_thumb_size'] : '';
                $crop_width = isset($demo['woo_crop_width']) ? $demo['woo_crop_width'] : '';
                $crop_height = isset($demo['woo_crop_height']) ? $demo['woo_crop_height'] : '';

                // Assign WooCommerce pages if WooCommerce Exists
                if (class_exists('WooCommerce') && true == $shop_demo) {

                    $woopages = array(
                        'woocommerce_shop_page_id' => 'Shop',
                        'woocommerce_cart_page_id' => 'Cart',
                        'woocommerce_checkout_page_id' => 'Checkout',
                        'woocommerce_pay_page_id' => 'Checkout &#8594; Pay',
                        'woocommerce_thanks_page_id' => 'Order Received',
                        'woocommerce_myaccount_page_id' => 'My Account',
                        'woocommerce_edit_address_page_id' => 'Edit My Address',
                        'woocommerce_view_order_page_id' => 'View Order',
                        'woocommerce_change_password_page_id' => 'Change Password',
                        'woocommerce_logout_page_id' => 'Logout',
                        'woocommerce_lost_password_page_id' => 'Lost Password'
                    );

                    foreach ($woopages as $woo_page_name => $woo_page_title) {

                        $woopage = get_page_by_title($woo_page_title);
                        if (isset($woopage) && $woopage->ID) {
                            update_option($woo_page_name, $woopage->ID);
                        }
                    }

                    // We no longer need to install pages
                    delete_option('_wc_needs_pages');
                    delete_transient('_wc_activation_redirect');

                    // Get products image size
                    update_option('woocommerce_single_image_width', $image_size);
                    update_option('woocommerce_thumbnail_image_width', $thumbnail_size);
                    update_option('woocommerce_thumbnail_cropping', 'custom');
                    update_option('woocommerce_thumbnail_cropping_custom_width', $crop_width);
                    update_option('woocommerce_thumbnail_cropping_custom_height', $crop_height);
                }

                // Set imported menus to registered theme locations
                $locations = get_theme_mod('nav_menu_locations');
                $menus = wp_get_nav_menus();

                if ($menus) {

                    foreach ($menus as $menu) {

                        if ($menu->name == 'Main Menu') {
                            $locations['main_menu'] = $menu->term_id;
                        } else if ($menu->name == 'Top Menu') {
                            $locations['topbar_menu'] = $menu->term_id;
                        } else if ($menu->name == 'Footer Menu') {
                            $locations['footer_menu'] = $menu->term_id;
                        } else if ($menu->name == 'Home Menu') {
                            $locations['main_menu_home'] = $menu->term_id;
                        }
                    }
                }

                // Set menus to locations
                set_theme_mod('nav_menu_locations', $locations);

                // Disable Elementor default settings
                update_option('elementor_disable_color_schemes', 'yes');
                update_option('elementor_disable_typography_schemes', 'yes');
                if (!empty($elementor_width)) {
                    update_option('elementor_container_width', $elementor_width);
                }

                // Assign front page and posts page (blog page).
                $home_page = get_page_by_title($homepage_title);
                $blog_page = get_page_by_title($blog_title);

                update_option('show_on_front', 'page');

                if (is_object($home_page)) {
                    update_option('page_on_front', $home_page->ID);
                }

                if (is_object($blog_page)) {
                    update_option('page_for_posts', $blog_page->ID);
                }

                // Posts to show on the blog page
                if (!empty($posts_to_show)) {
                    update_option('posts_per_page', $posts_to_show);
                }
            }

            die();
        }

        /**
         * Import XML data
         *
         * @since 1.0.0
         */
        public function process_xml($file) {

            $response = FWP_Demos_Helpers::get_remote($file);

            // No sample data found
            if ($response === false) {
                return new WP_Error('xml_import_error', __('Can not retrieve sample data xml file. Website may be down at the momment please try again later. If you still have issues contact the theme developer for assistance.', 'futurio-extra'));
            }

            // Write sample data content to temp xml file
            $temp_xml = FE_PATH . 'classes/importers/temp.xml';
            file_put_contents($temp_xml, $response);

            // Set temp xml to attachment url for use
            $attachment_url = $temp_xml;

            // If file exists lets import it
            if (file_exists($attachment_url)) {
                $this->import_xml($attachment_url);
            } else {
                // Import file can't be imported - we should die here since this is core for most people.
                return new WP_Error('xml_import_error', __('The xml import file could not be accessed. Please try again or contact the theme developer.', 'futurio-extra'));
            }
        }

        /**
         * Import XML file
         *
         * @since 1.0.0
         */
        private function import_xml($file) {

            // Make sure importers constant is defined
            if (!defined('WP_LOAD_IMPORTERS')) {
                define('WP_LOAD_IMPORTERS', true);
            }

            // Import file location
            $import_file = ABSPATH . 'wp-admin/includes/import.php';

            // Include import file
            if (!file_exists($import_file)) {
                return;
            }

            // Include import file
            require_once( $import_file );

            // Define error var
            $importer_error = false;

            if (!class_exists('WP_Importer')) {
                $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

                if (file_exists($class_wp_importer)) {
                    require_once $class_wp_importer;
                } else {
                    $importer_error = __('Can not retrieve class-wp-importer.php', 'futurio-extra');
                }
            }

            if (!class_exists('WP_Import')) {
                $class_wp_import = FE_PATH . 'classes/importers/class-wordpress-importer.php';

                if (file_exists($class_wp_import)) {
                    require_once $class_wp_import;
                } else {
                    $importer_error = __('Can not retrieve wordpress-importer.php', 'futurio-extra');
                }
            }

            // Display error
            if ($importer_error) {
                return new WP_Error('xml_import_error', $importer_error);
            } else {

                // No error, lets import things...
                if (!is_file($file)) {
                    $importer_error = __('Sample data file appears corrupt or can not be accessed.', 'futurio-extra');
                    return new WP_Error('xml_import_error', $importer_error);
                } else {
                    $importer = new WP_Import();
                    $importer->fetch_attachments = true;
                    $importer->import($file);

                    // Clear sample data content from temp xml file
                    $temp_xml = FE_PATH . 'classes/importers/temp.xml';
                    file_put_contents($temp_xml, '');
                }
            }
        }

    }

}
new FuturioWP_Demos();
