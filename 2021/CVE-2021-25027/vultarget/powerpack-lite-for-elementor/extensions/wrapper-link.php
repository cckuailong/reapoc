<?php
namespace PowerpackElementsLite\Extensions;

// Powerpack Elements classes
use PowerpackElementsLite\Base\Extension_Base;

// Elementor classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Wrapper Link Extension
 *
 * Adds link around sections, columns and widgets
 *
 * @since 2.4.0
 */
class Extension_Wrapper_Link extends Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 2.4.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_common = true;

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 2.4.0
	 **/
	public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array(
				'powerpack-frontend',
			);
		}

		return [];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 2.4.0
	 **/
	public static function get_description() {
		return __( 'Adds link around sections, columns and widgets.', 'powerpack' );
	}

	/**
	 * Is disabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page
	 *
	 * @access public
	 * @since 2.4.0
	 * @return bool
	 */
	public static function is_default_disabled() {
		return true;
	}

	/**
	 * Add common sections
	 *
	 * @since 2.4.0
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {

		// Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for columns
		add_action( 'elementor/element/column/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for widgets
		add_action( 'elementor/element/common/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for sections if elementor pro
		add_action( 'elementor/element/section/section_custom_css_pro/after_section_end', function( $element, $args ) {
			$this->add_common_sections( $element, $args );
		}, 10, 2 );

		// Activate sections for columns if elementor pro
		add_action( 'elementor/element/column/section_custom_css_pro/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for widgets if elementor pro
		add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );
	}

	/**
	 * Add Controls
	 *
	 * @since 2.4.0
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		$element_type = $element->get_type();

		$element->add_control(
			'pp_wrapper_link_enable',
			array(
				'label'        => __( 'Wrapper Link', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$element->add_control(
			'pp_wrapper_link',
			[
				'label'        => __( 'Link', 'powerpack' ),
				'type'         => Controls_Manager::URL,
				'dynamic'      => [
					'active' => true,
				],
				'placeholder'  => 'https://www.your-link.com',
				'condition'    => [
					'pp_wrapper_link_enable' => 'yes',
				],
			]
		);
	}

	protected function render() {
		$settings = $element->get_settings();
	}

	/**
	 * Add Actions
	 *
	 * @since 2.4.0
	 *
	 * @access protected
	 */
	protected function add_actions() {

		// Activate controls for section
		add_action( 'elementor/element/section/section_powerpack_elements_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		// Activate controls for columns
		add_action( 'elementor/element/column/section_powerpack_elements_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		// Activate controls for widgets
		add_action( 'elementor/element/common/section_powerpack_elements_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		// Conditions for sections
		add_action( 'elementor/frontend/before_render', function( $element ) {
			$settings  = $element->get_settings_for_display();
			$link = $settings['pp_wrapper_link'];

			if ( 'yes' === $settings['pp_wrapper_link_enable'] && $link['url'] ) {
				if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() || ! \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
					wp_enqueue_script( 'powerpack-frontend' );
				}

				$link_attributes = array(
					'url'         => esc_url( $link['url'] ),
					'is_external' => esc_attr( $link['is_external'] ),
					'nofollow'    => esc_attr( $link['nofollow'] ),
				);

				$element->add_render_attribute(
					'_wrapper', [
						'data-pp-wrapper-link' => wp_json_encode( $link_attributes ),
						'class'                => 'pp-wrapper-link',
					]
				);
			}
		}, 10, 1 );
	}
}
