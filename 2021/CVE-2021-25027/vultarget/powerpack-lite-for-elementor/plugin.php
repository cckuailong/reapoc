<?php
namespace PowerpackElementsLite;

use Elementor\Utils;
use PowerpackElementsLite\Classes\PP_Config;
use PowerpackElementsLite\Classes\PP_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; } // Exit if accessed directly

/**
 * Main class plugin
 */
class PowerpackLitePlugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_extensions_manager;

	/**
	 * @var Manager
	 */
	public $modules_manager;

	/**
	 * @var array
	 */
	private $_localize_settings = [];

	/**
	 * @return string
	 */
	public function get_version() {
		return POWERPACK_ELEMENTS_LITE_VER;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function _includes() {
		require POWERPACK_ELEMENTS_LITE_PATH . 'includes/extensions-manager.php';
		require POWERPACK_ELEMENTS_LITE_PATH . 'includes/modules-manager.php';
	}

	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class
			)
		);
		$filename = POWERPACK_ELEMENTS_LITE_PATH . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include $filename;
		}
	}

	public function get_localize_settings() {
		return $this->_localize_settings;
	}

	public function add_localize_settings( $setting_key, $setting_value = null ) {
		if ( is_array( $setting_key ) ) {
			$this->_localize_settings = array_replace_recursive( $this->_localize_settings, $setting_key );

			return;
		}

		if ( ! is_array( $setting_value ) || ! isset( $this->_localize_settings[ $setting_key ] ) || ! is_array( $this->_localize_settings[ $setting_key ] ) ) {
			$this->_localize_settings[ $setting_key ] = $setting_value;

			return;
		}

		$this->_localize_settings[ $setting_key ] = array_replace_recursive( $this->_localize_settings[ $setting_key ], $setting_value );
	}

	/**
	 * Enqueue frontend styles
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_frontend_styles() {
		$debug_suffix     = ( PP_Helper::is_script_debug() ) ? '' : '.min';
		$direction_suffix = is_rtl() ? '-rtl' : '';
		$suffix           = $direction_suffix . $debug_suffix;
		$path             = ( PP_Helper::is_script_debug() ) ? 'assets/css/' : 'assets/css/min/';

		wp_enqueue_style(
			'powerpack-frontend',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'frontend' . $suffix . '.css',
			[],
			POWERPACK_ELEMENTS_LITE_VER
		);

		wp_register_style(
			'odometer',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/odometer/odometer-theme-default.css',
			[],
			POWERPACK_ELEMENTS_LITE_VER
		);

		wp_register_style(
			'pp-twentytwenty',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/twentytwenty/twentytwenty.css',
			[],
			POWERPACK_ELEMENTS_LITE_VER
		);

		wp_register_style(
			'pp-magnific-popup',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/magnific-popup/magnific-popup' . $suffix . '.css',
			array(),
			POWERPACK_ELEMENTS_LITE_VER
		);

		if ( class_exists( 'GFCommon' && \Elementor\Plugin::$instance->preview->is_preview_mode() && PP_Helper::is_widget_active( 'Gravity_Forms' ) ) ) {
			$gf_forms = \RGFormsModel::get_forms( null, 'title' );
			foreach ( $gf_forms as $form ) {
				if ( '0' !== $form->id ) {
					wp_enqueue_script( 'gform_gravityforms' );
					gravity_form_enqueue_scripts( $form->id );
				}
			}
		}

		if ( function_exists( 'wpforms' ) ) {
			wpforms()->frontend->assets_css();
		}
	}

	/**
	 * Enqueue frontend scripts
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_frontend_scripts() {
		$settings = \PowerpackElementsLite\Classes\PP_Admin_Settings::get_settings();
		$suffix = ( PP_Helper::is_script_debug() ) ? '' : '.min';
		$path = ( PP_Helper::is_script_debug() ) ? 'assets/js/' : 'assets/js/min/';

		wp_register_script(
			'isotope',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/isotope/isotope.pkgd' . $suffix . '.js',
			array(
				'jquery',
			),
			'0.5.3',
			true
		);

		wp_register_script(
			'pp-twentytwenty',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/twentytwenty/jquery.twentytwenty.js',
			[
				'jquery',
			],
			'2.0.0',
			true
		);

		wp_register_script(
			'jquery-event-move',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/js/jquery.event.move.js',
			[
				'jquery',
			],
			'2.0.0',
			true
		);

		wp_register_script(
			'pp-magnific-popup',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/magnific-popup/jquery.magnific-popup' . $suffix . '.js',
			[
				'jquery',
			],
			'2.2.1',
			true
		);

		wp_register_script(
			'odometer',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/odometer/odometer.min.js',
			[
				'jquery',
			],
			'0.4.8',
			true
		);

		wp_register_script(
			'pp-jquery-plugin',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/js/jquery.plugin.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		wp_register_script(
			'twitter-widgets',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'twitter-widgets' . $suffix . '.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		wp_register_script(
			'pp-slick',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/slick/slick' . $suffix . '.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_register_script(
			'powerpack-pp-posts',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'pp-posts' . $suffix . '.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_localize_script(
			'powerpack-pp-posts',
			'pp_posts_script',
			[
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'posts_nonce' => wp_create_nonce( 'pp-posts-widget-nonce' ),
			]
		);

		wp_register_script(
			'pp-tooltipster',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/tooltipster/tooltipster' . $suffix . '.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_register_script(
			'powerpack-frontend',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'frontend' . $suffix . '.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_register_script(
			'pp-animated-gradient-bg',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'pp-gradient-bg-animation' . $suffix . '.js',
			array(
				'jquery',
			),
			'1.0.0',
			true
		);

		$pp_localize = apply_filters(
			'pp_elements_lite_js_localize',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_localize_script( 'jquery', 'pp', $pp_localize );
	}

	/**
	 * Enqueue editor styles
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'powerpack-editor',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/css/editor.css',
			[],
			POWERPACK_ELEMENTS_LITE_VER
		);

		wp_enqueue_style(
			'powerpack-icons',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/lib/ppicons/css/powerpack-icons.css',
			[],
			POWERPACK_ELEMENTS_LITE_VER
		);
	}

	/**
	 * Enqueue editor scripts
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'powerpack-editor',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/js/editor.js',
			[
				'jquery',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_enqueue_script(
			'pp-magnific-popup'
		);
	}

	public function enqueue_panel_scripts() {}

	public function enqueue_editor_preview_styles() {
		$debug_suffix     = ( PP_Helper::is_script_debug() ) ? '' : '.min';
		$direction_suffix = is_rtl() ? '-rtl' : '';
		$suffix           = $direction_suffix . $debug_suffix;
		$path             = ( PP_Helper::is_script_debug() ) ? 'assets/css/' : 'assets/css/min/';

		wp_enqueue_style(
			'powerpack-editor',
			POWERPACK_ELEMENTS_LITE_URL . $path . 'editor' . $debug_suffix . '.css',
			array(),
			POWERPACK_ELEMENTS_LITE_VER
		);

		wp_enqueue_style( 'odometer' );
		wp_enqueue_style( 'pp-magnific-popup' );
		wp_enqueue_style( 'pp-twentytwenty' );
	}

	/**
	 * Register Group Controls
	 *
	 * @since 1.2.9
	 */
	public function include_group_controls() {
		// Include Control Groups
		require POWERPACK_ELEMENTS_LITE_PATH . 'includes/controls/groups/transition.php';

		// Add Control Groups
		\Elementor\Plugin::instance()->controls_manager->add_group_control( 'pp-transition', new Group_Control_Transition() );
	}

	/**
	 * Register Controls
	 *
	 * @since 1.2.9
	 *
	 * @access private
	 */
	public function register_controls() {

		// Include Controls
		require POWERPACK_ELEMENTS_LITE_PATH . 'includes/controls/query.php';

		// Register Controls
		\Elementor\Plugin::instance()->controls_manager->register_control( 'pp-query', new Control_Query() );
	}

	public function elementor_init() {
		$this->modules_manager = new Modules_Manager();
		$this->_extensions_manager = new Extensions_Manager();

		// Add element category in panel
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'powerpack-elements', // This is the name of your addon's category and will be used to group your widgets/elements in the Edit sidebar pane!
			[
				'title' => __( 'PowerPack Elements', 'powerpack' ), // The title of your modules category - keep it simple and short!
				'icon' => 'font',
			],
			1
		);
	}

	public function get_promotion_widgets( $config ) {

		if ( is_pp_elements_active() ) {
			return $config;
		}

		$promotion_widgets = [];

		if ( isset( $config['promotionWidgets'] ) ) {
			$promotion_widgets = $config['promotionWidgets'];
		}

		$pro_widgets = PP_Config::get_pro_widgets();

		$combine_array = array_merge( $promotion_widgets, $pro_widgets );

		$config['promotionWidgets'] = $combine_array;

		return $config;
	}

	protected function add_actions() {
		add_action( 'elementor/init', [ $this, 'elementor_init' ] );

		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'include_group_controls' ] );

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_preview_styles' ] );

		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );

		add_filter( 'elementor/editor/localize_settings', [ $this, 'get_promotion_widgets' ] );
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();
		$this->add_actions();
		Classes\UsageTracking::get_instance();
	}

}

if ( ! defined( 'POWERPACK_ELEMENTS_TESTS' ) ) {
	// In tests we run the instance manually.
	PowerpackLitePlugin::instance();
}
