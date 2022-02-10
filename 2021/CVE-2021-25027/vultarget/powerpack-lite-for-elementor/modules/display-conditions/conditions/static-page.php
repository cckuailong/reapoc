<?php
namespace PowerpackElementsLite\Modules\DisplayConditions\Conditions;

// Powerpack Elements Classes
use PowerpackElementsLite\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * \Modules\DisplayConditions\Conditions\Static_Page
 *
 * @since  1.2.7
 */
class Static_Page extends Condition {

	/**
	 * Get Group
	 *
	 * Get the group of the condition
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_group() {
		return 'single';
	}

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_name() {
		return 'static_page';
	}

	/**
	 * Get Title
	 *
	 * Get the title of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_title() {
		return __( 'Static Page', 'powerpack' );
	}

	/**
	 * Get Value Control
	 *
	 * Get the settings for the value control
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type'          => Controls_Manager::SELECT,
			'default'       => 'home',
			'label_block'   => true,
			'options'       => [
				'home'      => __( 'Homepage', 'powerpack' ),
				'static'    => __( 'Front Page', 'powerpack' ),
				'blog'      => __( 'Blog', 'powerpack' ),
				'404'       => __( '404 Page', 'powerpack' ),
			],
		];
	}

	/**
	 * Check condition
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 *
	 * @param string    $name       The control name to check
	 * @param string    $operator   Comparison operator
	 * @param mixed     $value      The control value to check
	 */
	public function check( $name, $operator, $value ) {
		if ( 'home' === $value ) {
			return $this->compare( ( is_front_page() && is_home() ), true, $operator );
		} elseif ( 'static' === $value ) {
			return $this->compare( ( is_front_page() && ! is_home() ), true, $operator );
		} elseif ( 'blog' === $value ) {
			return $this->compare( ( ! is_front_page() && is_home() ), true, $operator );
		} elseif ( '404' === $value ) {
			return $this->compare( is_404(), true, $operator );
		}
	}
}
