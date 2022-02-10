<?php
namespace PowerpackElementsLite\Base;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Base\Condition
 *
 * @since  1.4.13.1
 */
abstract class Condition {

	/**
	 * @var Module_Base
	 */
	protected static $_instances = [];

	protected $element_id;

	/**
	 * Return the current module class name
	 *
	 * @access public
	 * @since 1.4.13.1
	 *
	 * @eturn string
	 */
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

	/**
	 * Checks if current condition is supported
	 * Defaults to true
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public static function is_supported() {
		return true;
	}

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public function get_group() {}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public function get_name() {}

	/**
	 * Get Name
	 * 
	 * Get the title of the module
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public function get_title() {}

	/**
	 * Get Default Value
	 * 
	 * Get the default value of the value control
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public function get_name_control() { return false; }

	/**
	 * Get Default Value
	 * 
	 * Get the default value of the value control
	 *
	 * @since  1.4.13.1
	 * @return string
	 */
	public function get_value_control() {}

	/**
	 * Check user login status
	 *
	 * @since 2.0.0
	 *
	 * @access protected
	 *
	 * @param mixed  $name  	The control name to check
	 * @param mixed  $value  	The control value to check
	 * @param string $operator  Comparison operator.
	 */
	public function check( $name, $operator, $value ) {}

	/**
	 * Compare conditions.
	 *
	 * Calls compare method
	 *
	 * @since 1.4.13.1
	 * @access public
	 * @static
	 *
	 * @param mixed  $left_value  First value to compare.
	 * @param mixed  $right_value Second value to compare.
	 * @param string $operator    Comparison operator.
	 *
	 * @return bool
	 */
	public function compare( $left_value, $right_value, $operator ) {
		switch ( $operator ) {
			case 'is':
				return $left_value == $right_value;
			case 'not':
				return $left_value != $right_value;
			default:
				return $left_value === $right_value;
		}
	}

	/**
	 * Set Condition Element ID
	 * 
	 * Set the element ID for this condition
	 *
	 * @since  2.2.2
	 * @return string
	 */
	public function set_element_id( $id ) {
		$this->element_id = $id;
	}

	/**
	 * Get Condition Element ID
	 * 
	 * Returns the previously set element id
	 *
	 * @since  2.2.2
	 * @return string
	 */
	protected function get_element_id() {
		return $this->element_id;
	}
}
