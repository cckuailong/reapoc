<?php

namespace PowerpackElementsLite\Base;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Extension
 *
 * Class to easify extend Elementor controls and functionality
 *
 * @since 0.1.0
 */
class Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 1.8.0
	 * @access private
	 *
	 * @var bool
	 */
	protected $is_common = false;

	/**
	 * Depended scripts.
	 *
	 * Holds all the extension depended scripts to enqueue.
	 *
	 * @since 1.8.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_scripts = [];

	/**
	 * Depended styles.
	 *
	 * Holds all the extension depended styles to enqueue.
	 *
	 * @since 1.8.0
	 * @access private
	 *
	 * @var array
	 */
	private $depended_styles = [];

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public final function __construct() {

		// Enqueue scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Enqueue styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );

		// Elementor hooks

		if ( $this->is_common ) {
			// Add the advanced section required to display controls
			$this->add_common_sections_actions();
		}

		$this->add_actions();
	}

	/**
	 * Add script depends.
	 *
	 * Register new script to enqueue by the handler.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @param string $handler Depend script handler.
	 */
	public function add_script_depends( $handler ) {
		$this->depended_scripts[] = $handler;
	}

	/**
	 * Add style depends.
	 *
	 * Register new style to enqueue by the handler.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @param string $handler Depend style handler.
	 */
	public function add_style_depends( $handler ) {
		$this->depended_styles[] = $handler;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the extension requires.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return $this->depended_scripts;
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the scripts defined as extension dependencies and enqueues
	 * them. Use `get_script_depends()` method to add custom script dependencies.
	 *
	 * @since 1.8.0
	 * @access public
	 */
	final public function enqueue_scripts() {
		foreach ( $this->get_script_depends() as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Retrieve style dependencies.
	 *
	 * Get the list of style dependencies the extension requires.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	final public function get_style_depends() {
		return $this->depended_styles;
	}

	/**
	 * Retrieve extension description.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return string Widget description.
	 */
	public static function get_description() {}

	/**
	 * Enqueue styles.
	 *
	 * Registers all the styles defined as extension dependencies and enqueues
	 * them. Use `get_style_depends()` method to add custom style dependencies.
	 *
	 * @since 1.8.0
	 * @access public
	 */
	final public function enqueue_styles() {
		foreach ( $this->get_style_depends() as $style ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected final function add_common_sections( $element, $args ) {

		// The name of the section
		$section_name = 'section_powerpack_elements_advanced';

		// Check if this section exists
		$section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), $section_name );

		if ( ! is_wp_error( $section_exists ) ) {
			// We can't and should try to add this section to the stack
			return false;
		}

		$element->start_controls_section(
			$section_name,
			[
				'tab' 	=> Controls_Manager::TAB_ADVANCED,
				'label' => __( 'PowerPack', 'powerpack' ),
			]
		);	

		$element->end_controls_section();

	}

	/**
	 * Add common sections
	 *
	 * @since 1.8.0
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected function add_actions() {}

	/**
	 * Removes controls in bulk
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected function remove_controls( $element, $controls = null ) {
		if ( empty( $controls ) )
			return;

		if ( is_array( $controls ) ) {
			$control_id = $controls;

			foreach( $controls as $control_id ) {
				$element->remove_control( $control_id );
			}
		} else {
			$element->remove_control( $controls );
		}
	}

	/**
	 * Method for setting extension dependancy on Elementor Pro plugin
	 *
	 * When returning false it doesn't allow the extension to be registered
	 *
	 * @access public
	 * @since 1.8.0
	 * @return bool
	 */
	public static function requires_elementor_pro() {
		return false;
	}

	/**
	 * Is enabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page
	 *
	 * @access public
	 * @since 2.0.0
	 * @return bool
	 */
	public static function is_default_disabled() {
		return false;
	}

}