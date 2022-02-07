<?php
namespace WPO\WC\PDF_Invoices\Legacy;

defined( 'ABSPATH' ) or exit;

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Legacy\\WPO_WCPDF_Legacy' ) ) :

class WPO_WCPDF_Legacy {
	public static $version;
	public $enabled;
	public $settings;
	public $export;
	public $functions;

	protected static $_instance = null;

	/**
	 * Main Plugin Instance
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		self::$version   = WPO_WCPDF()->version;
		$this->enabled   = WPO_WCPDF()->legacy_mode_enabled();
		$this->settings  = include_once( 'class-wcpdf-legacy-settings.php' );
		$this->export    = include_once( 'class-wcpdf-legacy-export.php' );
		$this->functions = include_once( 'class-wcpdf-legacy-functions.php' );
	}

	/**
	 * Redirect function calls directly to legacy functions class
	 */
	public function __call( $name, $arguments ) {
		$human_readable_call = '$wpo_wcpdf->'.$name.'()';
		$this->auto_enable_check( $human_readable_call );

		if ( is_callable( array( WPO_WCPDF(), $name ) ) ) {
			wcpdf_deprecated_function( $human_readable_call, '2.0', 'WPO_WCPDF()->'.$name.'()' );
			return call_user_func_array( array( WPO_WCPDF(), $name ), $arguments );
		} elseif ( is_callable( array( $this->functions, $name ) ) ) {
			wcpdf_deprecated_function( $human_readable_call, '2.0', '$this->'.$name.'()' );
			return call_user_func_array( array( $this->functions, $name ), $arguments );
		} else {
			throw new \Exception("Call to undefined method ".__CLASS__."::{$name}()", 1);
		}
	}

	/**
	 * Fired when a call is made to the legacy class (also used by sub classes).
	 * If legacy mode was not enabled, it is automatically enabled and an error message is shown to the user
	 * Reloading the page should then work in legacy mode
	 */
	public function auto_enable_check( $call = '', $die = true ) {
		if ( $this->enabled === false ) {
			$debug_settings = get_option( 'wpo_wcpdf_settings_debug', array() );
			$debug_settings['legacy_mode'] = 1;
			update_option( 'wpo_wcpdf_settings_debug', $debug_settings );
			$this->enabled = true;
			add_action( 'wp_die_ajax_handler', function() {
				return '_default_wp_die_handler';
			} );
			$title = __( 'Error', 'woocommerce-pdf-invoices-packing-slips' );
			$message = __( 'An outdated template or action hook was used to generate the PDF. Legacy mode has been activated, please try again by reloading this page.', 'woocommerce-pdf-invoices-packing-slips' );
			
			if (!empty($call)) {
				$message = sprintf('%s</p><p>%s: <b>%s</b>', $message, __( 'The following function was called', 'woocommerce-pdf-invoices-packing-slips' ), $call );
			}
			wp_die( "<h1>{$title}</h1><p>{$message}</p>", $title );
		}
	}
}

endif; // Class exists check

function WPO_WCPDF_Legacy() {
	return WPO_WCPDF_Legacy::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpo_wcpdf'] = WPO_WCPDF_Legacy();