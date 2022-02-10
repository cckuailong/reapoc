<?php
namespace PowerpackElementsLite\Base;

use PowerpackElementsLite\Classes\PP_Admin_Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Module_Base {

	/**
	 * @var \ReflectionClass
	 */
	private $reflection;

	private $components = [];

	/**
	 * @var Module_Base
	 */
	protected static $_instances = [];

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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'powerpack' ), '1.0.0' );
	}

	public static function is_active() {
		return true;
	}

	public static function class_name() {
		return get_called_class();
	}

	/**
	 * @return static
	 */
	public static function instance() {
		if ( empty( static::$_instances[ static::class_name() ] ) ) {
			static::$_instances[ static::class_name() ] = new static();
		}

		return static::$_instances[ static::class_name() ];
	}

	abstract public function get_name();

	public function get_widgets() {
		return [];
	}

	public function __construct() {
		$this->reflection = new \ReflectionClass( $this );

		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
	}

	public function init_widgets() {
		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			$widget_name = strtolower( $widget );
			$widget_filename = 'pp-' . str_replace( '_', '-', $widget_name );

			if ( $this->is_widget_active( $widget_filename ) ) {
				$class_name = $this->reflection->getNamespaceName() . '\Widgets\\' . $widget;
				$widget_manager->register_widget_type( new $class_name() );
			}
		}
	}

	public static function is_widget_active( $widget = '' ) {
		$enabled_modules = pp_elements_lite_get_enabled_modules();

		if ( in_array( $widget, $enabled_modules ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add module component.
	 *
	 * Add new component to the current module.
	 *
	 * @since 1.2.9
	 * @access public
	 *
	 * @param string $id       Component ID.
	 * @param mixed  $instance An instance of the component.
	 */
	public function add_component( $id, $instance ) {
		$this->components[ $id ] = $instance;
	}

	/**
	 * Get Components.
	 *
	 * Retrieve the module components.
	 *
	 * @since 1.2.9
	 * @access public
	 * @return Module[]
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Get Component.
	 *
	 * Retrieve the module component.
	 *
	 * @since 1.2.9
	 * @access public
	 *
	 * @param string $id Component ID.
	 *
	 * @return mixed An instance of the component, or `false` if the component
	 *               doesn't exist.
	 */
	public function get_component( $id ) {
		if ( isset( $this->components[ $id ] ) ) {
			return $this->components[ $id ];
		}

		return false;
	}
}
