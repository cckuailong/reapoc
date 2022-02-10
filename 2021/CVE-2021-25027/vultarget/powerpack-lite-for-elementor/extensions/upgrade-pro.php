<?php
namespace PowerpackElementsLite\Extensions;

// Powerpack Elements classes
use PowerpackElementsLite\Base\Extension_Base;

// Elementor classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Upgrade Pro Extension
 *
 * Adds upgrade pro notice to PowerPack elements
 *
 * @since 2.4.1
 */
class Extension_Upgrade_Pro extends Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 2.4.1
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_common = true;

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 2.4.1
	 **/
	public function get_script_depends() {
		return [];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 2.4.1
	 **/
	public static function get_description() {
		return __( 'Adds upgrade PowerPack notice to all widgets of PowerPack.', 'powerpack' );
	}

	/**
	 * Is disabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page
	 *
	 * @access public
	 * @since 2.4.1
	 * @return bool
	 */
	public static function is_default_disabled() {
		return false;
	}

	/**
	 * Add Controls
	 *
	 * @since 2.4.1
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		$element_type = $element->get_name();

		$element->start_controls_section(
			'section_upgrade_powerpack_lite',
			array(
				'label' => apply_filters( 'upgrade_powerpack_title', __( 'Get PowerPack Pro', 'powerpack' ) ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$element->add_control(
			'upgrade_powerpack_lite_notice',
			array(
				'label'           => '',
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
				'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Add Actions
	 *
	 * @since 2.4.1
	 *
	 * @access protected
	 */
	protected function add_actions() {

		$widgets = pp_elements_lite_get_enabled_modules();

		foreach ( $widgets as $widget ) {
			if ( 'pp-hotspots' === $widget ) {
				$widget = 'pp-image-hotspots';
			}

			if ( 'pp-link-effects' === $widget ) {
				$widget = 'pa-link-effects';
			}

			add_action( 'elementor/element/' . $widget . '/section_help_docs/after_section_end', function( $element, $args ) {
					$this->add_controls( $element, $args );
			}, 10, 2 );
		}

	}
}
