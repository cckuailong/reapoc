<?php
/**
 * Plugin Name: PowerPack Lite for Beaver Builder
 * Plugin URI: https://wpbeaveraddons.com
 * Description: A set of custom, creative, unique modules for Beaver Builder to speed up your web design and development process.
 * Version: 1.2.9
 * Author: Beaver Addons
 * Author URI: https://wpbeaveraddons.com
 * Copyright: (c) 2016 IdeaBox Creations
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bb-powerpack-lite
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class BB_PowerPack_Lite {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public static $instance;

	/**
	 * Holds FontAwesome CSS class.
	 *
	 * @since 1.2.3
	 * @var string
	 */
	public $fa_css = '';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		/* Constants */
		$this->define_constants();

		require_once 'includes/sdk/pp_freemius.php';

		/* Classes */
		require_once 'classes/class-admin-settings.php';
		require_once 'classes/class-wpml-compatibility.php';

		/* Includes */
		require_once 'includes/helper-functions.php';

		/* Hooks */
		$this->init_hooks();
	}

	/**
	 * Define PowerPack constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_constants() {
		define( 'BB_POWERPACK_LITE', true );
		define( 'BB_POWERPACK_PRO', 'https://wpbeaveraddons.com/upgrade/?utm_medium=bb-powerpack-lite&utm_source=module-settings&utm_campaign=module-settings' );
		define( 'BB_POWERPACK_VER', '1.2.9' );
		define( 'BB_POWERPACK_DIR', plugin_dir_path( __FILE__ ) );
		define( 'BB_POWERPACK_URL', plugins_url( '/', __FILE__ ) );
		define( 'BB_POWERPACK_PATH', plugin_basename( __FILE__ ) );
		define( 'BB_POWERPACK_CAT', __( 'PowerPack Modules', 'bb-powerpack-lite' ) );
	}

	/**
	 * Initializes actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_modules' ) );
		add_action( 'plugins_loaded', array( $this, 'loader' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 100 );
		add_action( 'wp_head', array( $this, 'render_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Include modules.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_modules() {
		if ( class_exists( 'FLBuilder' ) ) {

			$enabled_icons = FLBuilderModel::get_enabled_icons();

			if ( in_array( 'font-awesome-5-solid', $enabled_icons )
				|| in_array( 'font-awesome-5-regular', $enabled_icons )
				|| in_array( 'font-awesome-5-brands', $enabled_icons ) ) {
					$this->fa_css = 'font-awesome-5';
			} else {
				$this->fa_css = 'font-awesome';
			}

			// Fields
			require_once 'classes/class-module-fields.php';

			$load_modules_in_admin = apply_filters( 'pp_load_modules_in_admin', true );

			if ( $load_modules_in_admin ) {
				require_once 'includes/modules.php';
			} else if ( ! is_admin() ) {
				require_once 'includes/modules.php';
			}
		}
	}

	/**
	 * Include row and column setting extendor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function loader() {
		if ( ! is_admin() && class_exists( 'FLBuilder' ) ) {

			// Panel functions
			require_once 'includes/panel-functions.php';

			$extensions = BB_PowerPack_Admin_Settings::get_enabled_extensions();

			// Extend row settings
			if ( isset( $extensions['row'] ) && count( $extensions['row'] ) > 0 ) {
				require_once 'includes/row.php';
			}

			// Extend column settings
			if ( isset( $extensions['col'] ) && count( $extensions['col'] ) > 0 ) {
				require_once 'includes/column.php';
			}
		}
	}

	/**
	 * Register the styles and scripts.
	 *
	 * @since 1.2.6
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script( 'modernizr-custom', BB_POWERPACK_URL . 'assets/js/modernizr.custom.53451.js', array(), '3.6.0', true );
		wp_register_script( 'pp-twitter-widgets', BB_POWERPACK_URL . 'assets/js/twitter-widgets.js', array(), BB_POWERPACK_VER, true );
	}

	/**
	 * Custom scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_scripts() {
		wp_enqueue_style( 'animate', BB_POWERPACK_URL . 'assets/css/animate.min.css', array(), '3.5.1' );
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			wp_enqueue_style( 'pp-fields-style', BB_POWERPACK_URL . 'assets/css/fields.css', array(), BB_POWERPACK_VER );
			wp_enqueue_script( 'pp-fields-script', BB_POWERPACK_URL . 'assets/js/fields.js', array( 'jquery' ), BB_POWERPACK_VER, true );
			wp_enqueue_style( 'pp-panel-style', BB_POWERPACK_URL . 'assets/css/panel.css', array(), BB_POWERPACK_VER );
			wp_enqueue_script( 'pp-panel-script', BB_POWERPACK_URL . 'assets/js/panel.js', array( 'jquery' ), BB_POWERPACK_VER, true );
		}
	}

	/**
	 * Custom inline scripts.
	 *
	 * @since 1.2.3
	 * @return void
	 */
	public function render_scripts() {
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
		?>
		<style>
		form[class*="fl-builder-pp-"] .fl-lightbox-header h1:before {
			content: "PowerPack ";
			position: relative;
			display: inline-block;
			margin-right: 5px;
		}
		</style>
		<?php
		}
	}

	/**
	 * Admin notices.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_notices() {
		if ( ! is_admin() ) {
			return;
		} elseif ( ! is_user_logged_in() ) {
			return;
		} elseif ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		if ( ! is_plugin_active( 'bb-plugin/fl-builder.php' ) ) {
			if ( ! is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) ) {
				echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', __( 'Please install and activate <a href="https://wordpress.org/plugins/beaver-builder-lite-version/" target="_blank">Beaver Builder Lite</a> or <a href="https://www.wpbeaverbuilder.com/pricing/" target="_blank">Beaver Builder Pro / Agency</a> to use PowerPack add-on.', 'bb-powerpack-lite' ) );
			}
		}
		if ( class_exists( 'BB_PowerPack' ) ) {
			echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', __( 'You already have PowerPack Pro version. PowerPack Lite cannot be used with the Pro version.', 'bb-powerpack-lite' ) );
		}
		/* Check transient, if available display notice */
		if ( get_transient( 'bb-powerpack-lite-admin-notices' ) ) {
			if ( ! class_exists( 'BB_PowerPack' ) && ( is_plugin_active( 'bb-plugin/fl-builder.php' ) || is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) ) ) {
				echo sprintf( '<div class="notice notice-info is-dismissible"><p>%s</p></div>', __( 'Thank you for choosing PowerPack Lite for Beaver Builder. Checkout <a href="https://wpbeaveraddons.com/?utm_medium=powerpack-lite&utm_source=plugin-page&utm_campaign=activation-message" target="_blank">Pro version</a> for more features.', 'bb-powerpack-lite' ) );
				delete_transient( 'bb-powerpack-lite-admin-notices' );
			}
		}
	}

	/**
	 * Add CSS class to body.
	 *
	 * @since 1.1.1
	 * @return array $classes Array of body CSS classes.
	 */
	public function body_class( $classes ) {
		if ( class_exists( 'FLBuilder' ) && class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			$classes[] = 'bb-powerpack';
			if ( function_exists( 'pp_panel_search' ) && pp_panel_search() == 1 ) {
				$classes[] = 'bb-powerpack-search-enabled';
			}
			if ( class_exists( 'FLBuilderUIContentPanel' ) ) {
				$classes[] = 'bb-powerpack-ui';
			}
		}

		return $classes;
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The BB_PowerPack_Lite object.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BB_PowerPack_Lite ) ) {
			self::$instance = new BB_PowerPack_Lite();
		}

		return self::$instance;
	}

}

include( 'includes/notice.php' );
register_activation_hook( __FILE__,  'pp_lite_set_review_trigger_date' );
/**
 * Set Trigger Date.
 *
 * @since  1.0.0
 */
function pp_lite_set_review_trigger_date() {
	// Number of days you want the notice delayed by.
	$delayindays = 30;
	// Create timestamp for when plugin was activated.
	$triggerdate = mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + $delayindays, date( 'Y' ) );
	// If our option doesn't exist already, we'll create it with today's timestamp.
	if ( ! get_option( 'pp_lite_activation_date' ) ) {
		add_option( 'pp_lite_activation_date', $triggerdate, '', 'yes' );
	}
}

// Load the PowerPack class.
function BB_POWERPACK_LITE() {
	return BB_PowerPack_Lite::get_instance();
}

BB_POWERPACK_LITE();
