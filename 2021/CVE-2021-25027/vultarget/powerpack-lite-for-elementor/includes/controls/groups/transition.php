<?php
namespace PowerpackElementsLite;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom transition group control
 *
 * @since 1.2.9
 */
class Group_Control_Transition extends Group_Control_Base {

	protected static $fields;

	/**
	 * @since 1.2.9
	 * @access public
	 */
	public static function get_type() {
		return 'pp-transition';
	}

	/**
	 * Retrieve the effect easings
	 *
	 * @since 1.2.9
	 * @access public
	 *
	 * @return array.  The available array of transition effects
	 */
	public static function get_transition_effects() {
		return [
			'linear' 		=> __( 'Linear', 'powerpack' ),
			'ease'			=> __( 'Ease', 'powerpack' ),
			'ease-in' 		=> __( 'Ease In', 'powerpack' ),
			'ease-out' 		=> __( 'Ease Out', 'powerpack' ),
			'ease-in-out' 	=> __( 'Ease In Out', 'powerpack' ),
		];
	}

	/**
	 * @since 1.2.9
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['property'] = [
			'label'			=> _x( 'Property', 'Transition Control', 'powerpack' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'all',
			'options'		=> [
				'all'		=> __( 'All', 'powerpack' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'transition-property: {{VALUE}}',
			],
		];

		$controls['function'] = [
			'label'			=> _x( 'Effect', 'Transition Control', 'powerpack' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'linear',
			'options'		=> self::get_transition_effects(),
			'selectors' => [
				'{{SELECTOR}}' => 'transition-timing-function: {{VALUE}}',
			],
		];

		$controls['duration'] = [
			'label'			=> _x( 'Duration', 'Transition Control', 'powerpack' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0.25,
			'min' 			=> 0.05,
			'max' 			=> 2,
			'step' 			=> 0.05,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-duration: {{VALUE}}s;',
			],
		];

		$controls['delay'] = [
			'label'			=> _x( 'Delay', 'Transition Control', 'powerpack' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0,
			'min' 			=> 0,
			'max' 			=> 2,
			'step' 			=> 0.01,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-delay: {{VALUE}}s;',
			],
			'separator' 	=> 'after',
		];

		return $controls;
	}

	/**
	 * Prepare fields.
	 *
	 * @since 1.2.9
	 * @access protected
	 *
	 * @param array $fields Control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {

		array_walk(
			$fields, function( &$field, $field_name ) {

				if ( in_array( $field_name, [ 'transition', 'popover_toggle' ] ) ) {
					return;
				}

				$field['condition']['transition'] = 'custom';
			}
		);

		return parent::prepare_fields( $fields );
	}

	/**
	 * @since 1.2.9
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name' 	=> 'transition',
				'starter_title' => _x( 'Transition', 'Transition Control', 'powerpack' ),
			],
		];
	}
}
