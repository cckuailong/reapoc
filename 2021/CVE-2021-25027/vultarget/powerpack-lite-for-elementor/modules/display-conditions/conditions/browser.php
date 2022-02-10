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
 * \Modules\DisplayConditions\Conditions\Browser
 *
 * @since  1.2.7
 */
class Browser extends Condition {

	/**
	 * Get Group
	 *
	 * Get the group of the condition
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_group() {
		return 'misc';
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
		return 'browser';
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
		return __( 'Browser', 'powerpack' );
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
			'default'       => array_keys( $this->get_browser_options() )[0],
			'label_block'   => true,
			'options'       => $this->get_browser_options(),
		];
	}

	/**
	 * Get browser options for control
	 *
	 * @since 1.2.7
	 *
	 * @access protected
	 */
	protected function get_browser_options() {
		return [
			'ie'            => 'Internet Explorer',
			'firefox'       => 'Mozilla Firefox',
			'chrome'        => 'Google Chrome',
			'opera_mini'    => 'Opera Mini',
			'opera'         => 'Opera',
			'safari'        => 'Safari',
			'edge'          => 'Microsoft Edge',
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
		$browsers = [
			'ie'            => [
				'MSIE',
				'Trident',
			],
			'firefox'       => 'Firefox',
			'chrome'        => 'Chrome',
			'opera_mini'    => 'Opera Mini',
			'opera'         => 'Opera',
			'safari'        => 'Safari',
		];

		$show = false;

		if ( 'ie' === $value ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][1] ) ) {
				$show = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ] ) ) {
				$show = true;

				// Additional check for Chrome that returns Safari
				if ( 'safari' === $value || 'firefox' === $value ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$show = false;
					}
				}
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
