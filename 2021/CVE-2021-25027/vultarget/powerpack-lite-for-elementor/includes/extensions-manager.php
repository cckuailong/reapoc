<?php
namespace PowerpackElementsLite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Extensions_Manager {

	const DISPLAY_CONDITIONS           = 'display-conditions';
	const WRAPPER_LINK                 = 'wrapper-link';
	const UPGRADE_PRO                  = 'upgrade-pro';
	const ANIMATED_GRADIENT_BACKGROUND = 'animated-gradient-background';

	private $_extensions = null;

	public $available_extensions = [
		self::DISPLAY_CONDITIONS,
		self::WRAPPER_LINK,
		self::UPGRADE_PRO,
		self::ANIMATED_GRADIENT_BACKGROUND,
	];

	/**
	 * Loops though available extensions and registers them
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 * @return void
	 */
	public function register_extensions() {

		$this->_extensions = [];

		$available_extensions = $this->available_extensions;

		foreach ( $available_extensions as $index => $extension_id ) {
			$extension_filename = str_replace( '_', '-', $extension_id );
			$extension_name = str_replace( '-', '_', $extension_id );

			$extension_filename = POWERPACK_ELEMENTS_LITE_PATH . "extensions/{$extension_filename}.php";

			require $extension_filename;

			$class_name = str_replace( '-', '_', $extension_id );

			$class_name = 'PowerpackElementsLite\Extensions\Extension_' . ucwords( $class_name );

			if ( ! $this->is_available( $extension_name ) ) {
				unset( $this->available_extensions[ $index ] );
			}

			// Skip extension if it's disabled in admin settings
			if ( $this->is_extension_disabled( $extension_name ) ) {
				continue;
			}

			$this->register_extension( $extension_id, new $class_name() );
		}

		do_action( 'powerpack_elements/extensions/extensions_registered', $this );
	}

	/**
	 * Check if extension is enabled through admin settings
	 *
	 * @since 1.2.7.2
	 *
	 * @access public
	 * @return bool
	 */
	public function is_extension_disabled( $extension = '' ) {
		$enabled_extensions = pp_elements_lite_get_enabled_extensions();
		$enabled_extensions[] = 'pp-upgrade-pro';

		if ( ! is_array( $enabled_extensions ) ) {
			$enabled_extensions = array();
		}

		$extension = str_replace( '_', '-', $extension );

		$extension_name = 'pp-' . $extension;

		if ( in_array( $extension_name, $enabled_extensions ) || isset( $enabled_extensions[ $extension_name ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if extension is disabled by default
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 * @return bool
	 */
	public function is_default_disabled( $extension_name ) {
		if ( ! $extension_name ) {
			return false;
		}

		$class_name = str_replace( '-', '_', $extension_name );
		$class_name = 'PowerpackElementsLite\Extensions\Extension_' . ucwords( $class_name );

		if ( $class_name::is_default_disabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if extension is available at all
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 * @return bool
	 */
	public function is_available( $extension_name ) {
		if ( ! $extension_name ) {
			return false;
		}

		$class_name = str_replace( '-', '_', $extension_name );
		$class_name = 'PowerpackElementsLite\Extensions\Extension_' . ucwords( $class_name );

		if ( $class_name::requires_elementor_pro() && ! is_elementor_pro_active() ) {
			return false;
		}

		return true;

	}

	/**
	 * @since 1.2.7
	 *
	 * @param $extension_id
	 * @param Extension_Base $extension_instance
	 */
	public function register_extension( $extension_id, Base\Extension_Base $extension_instance ) {
		$this->_extensions[ $extension_id ] = $extension_instance;
	}

	/**
	 * @since 1.2.7
	 *
	 * @param $extension_id
	 * @return bool
	 */
	public function unregister_extension( $extension_id ) {
		if ( ! isset( $this->_extensions[ $extension_id ] ) ) {
			return false;
		}

		unset( $this->_extensions[ $extension_id ] );

		return true;
	}

	/**
	 * @since 1.2.7
	 *
	 * @return Extension_Base[]
	 */
	public function get_extensions() {
		if ( null === $this->_extensions ) {
			$this->register_extensions();
		}

		return $this->_extensions;
	}

	/**
	 * @since 1.2.7
	 *
	 * @param $extension_id
	 * @return bool|\PowerpackElementsLite\Extension_Base
	 */
	public function get_extension( $extension_id ) {
		$extensions = $this->get_extensions();

		return isset( $extensions[ $extension_id ] ) ? $extensions[ $extension_id ] : false;
	}

	private function require_files() {
		require POWERPACK_ELEMENTS_LITE_PATH . 'base/extension-base.php';
	}

	public function __construct() {
		$this->require_files();
		$this->register_extensions();
	}
}
