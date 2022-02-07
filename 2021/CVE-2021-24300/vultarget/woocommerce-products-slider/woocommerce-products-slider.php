<?php
/*
Plugin Name: PickPlugins Product Slider for WooCommerce
Plugin URI: http://pickplugins.com/items/woocommerce-product-slider-for-wordpress/
Description: Fully responsive and mobile ready Carousel Slider for your WooCommerce product. unlimited slider anywhere via short-codes and easy admin setting.
Version: 1.13.21
WC requires at least: 3.0.0
WC tested up to: 4.3
Author: PickPlugins
Text Domain: woocommerce-products-slider
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


class WoocommerceProductsSlider{

	public function __construct(){

		define('wcps_plugin_url', plugins_url('/', __FILE__)  );
		define('wcps_plugin_dir', plugin_dir_path( __FILE__ ) );
		define('wcps_plugin_name', 'PickPlugins Product Slider' );
        define('wcps_plugin_version', '1.13.21' );
        define('wcps_server_url', 'https://www.pickplugins.com/demo/woocommerce-products-slider/');
        //define('wcps_server_url', 'http://localhost/wp/');



        require_once( wcps_plugin_dir . 'includes/class-post-types.php');
        require_once( wcps_plugin_dir . 'includes/class-metabox-wcps.php');
        require_once( wcps_plugin_dir . 'includes/class-metabox-wcps-hook.php');
        require_once( wcps_plugin_dir . 'includes/functions-layout-api.php');

        require_once( wcps_plugin_dir . 'includes/class-metabox-wcps-layout.php');
        require_once( wcps_plugin_dir . 'includes/class-metabox-wcps-layout-hook.php');
        require_once( wcps_plugin_dir . 'includes/functions-layout-hook.php');
        require_once( wcps_plugin_dir . 'includes/functions-layout-element.php');

        require_once( wcps_plugin_dir . 'templates/wcps-slider/wcps-slider-hook.php');

        require_once( wcps_plugin_dir . 'includes/class-admin-notices.php');
        require_once( wcps_plugin_dir . 'includes/functions-data-upgrade.php');
        require_once( wcps_plugin_dir . 'includes/class-settings.php');
        require_once( wcps_plugin_dir . 'includes/functions-settings-hook.php');

        require_once( wcps_plugin_dir . 'includes/3rd-party/3rd-party.php');


        require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php');
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-functions.php');
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-shortcodes.php');
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-update.php');
        require_once( plugin_dir_path( __FILE__ ) . 'includes/class-settings-tabs.php');


		// to work upload button
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );

		//short-code support into sidebar.
		add_filter('widget_text', 'do_shortcode');

		add_action( 'wp_enqueue_scripts', array( $this, '_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );

		add_action( 'plugins_loaded', array( $this, '_textdomain' ));


		register_activation_hook( __FILE__, array( $this, '_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivation' ) );
        //register_uninstall_hook( __FILE__, array( $this, '_uninstall' ) );
        add_filter('cron_schedules', array($this, 'cron_recurrence_interval'));


		}



	public function _textdomain() {

        $locale = apply_filters('plugin_locale', get_locale(), 'woocommerce-products-slider');
        load_textdomain('woocommerce-products-slider', WP_LANG_DIR . '/woocommerce-products-slider/woocommerce-products-slider-' . $locale . '.mo');

        load_plugin_textdomain('woocommerce-products-slider', false, plugin_basename(dirname(__FILE__)) . '/languages/');

	}




    function cron_recurrence_interval($schedules){

        $schedules['1minute'] = array(
            'interval' => 40,
            'display' => __('1 Minute', 'woocommerce-products-slider')
        );


        return $schedules;
    }


	public function _activation(){

	    $class_wcps_post_types = new class_wcps_post_types();
	    $_posttype_wcps = $class_wcps_post_types->_posttype_wcps();
        flush_rewrite_rules();
		do_action( 'wcps_plugin_activation' );

		}

    public function _deactivation(){

        wp_clear_scheduled_hook('wcps_cron_upgrade_settings');
        wp_clear_scheduled_hook('wcps_cron_upgrade_wcps');

        do_action( 'wcps_plugin_deactivation' );
    }

	public function _uninstall(){

		do_action( 'wcps_plugin_uninstall' );
		}



	public function _front_scripts(){


        wp_register_style('font-awesome-4', wcps_plugin_url.'assets/global/css/font-awesome-4.css');
        wp_register_style('font-awesome-5', wcps_plugin_url.'assets/global/css/font-awesome-5.css');

        wp_register_script('owl.carousel', wcps_plugin_url.'/assets/front/js/owl.carousel.js' , array( 'jquery' ));
        wp_register_style('owl.carousel', wcps_plugin_url.'assets/front/css/owl.carousel.css');

        wp_register_style('animate', wcps_plugin_url.'assets/front/css/animate.css');


	}

	public function _admin_scripts(){

        wp_enqueue_script('wcps_js', plugins_url('assets/admin/js/scripts-layouts.js', __FILE__), array('jquery'));
        wp_localize_script('wcps_js', 'wcps_ajax', array(
                'wcps_ajaxurl' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('wcps_ajax_nonce'),
            )
        );

        wp_register_style('font-awesome-4', wcps_plugin_url.'assets/global/css/font-awesome-4.css');
        wp_register_style('font-awesome-5', wcps_plugin_url.'assets/global/css/font-awesome-5.css');

        wp_register_style('settings-tabs', wcps_plugin_url.'assets/settings-tabs/settings-tabs.css');
        wp_register_script('settings-tabs', wcps_plugin_url.'assets/settings-tabs/settings-tabs.js'  , array( 'jquery' ));
        //wp_register_script('wcps-layouts-api', wcps_plugin_url.'assets/admin/js/scripts-layouts.js'  , array( 'jquery' ));




        $settings_tabs_field = new settings_tabs_field();
        $settings_tabs_field->admin_scripts();


	}


}


new WoocommerceProductsSlider();
