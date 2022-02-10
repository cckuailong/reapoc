<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Controls_Manager;

class PMPro_Elementor_Content_Restriction extends PMPro_Elementor {
	protected function content_restriction() {
		// Setup controls
		$this->register_controls();

		// Filter elementor render_content hook
		add_action( 'elementor/widget/render_content', array( $this, 'pmpro_elementor_render_content' ), 10, 2 );
		add_action( 'elementor/frontend/section/should_render', array( $this, 'pmpro_elementor_should_render' ), 10, 2 );

	}

	// Register controls to sections and widgets
	protected function register_controls() {
		foreach( $this->locations as $where ) {
				add_action('elementor/element/'.$where['element'].'/'.$this->section_name.'/before_section_end', array( $this, 'add_controls' ), 10, 2 );
		}
	}

	// Define controls
	public function add_controls( $element, $args ) {
		$element->add_control(
			'pmpro_require_membership_heading', array(
				'label'     => __( 'Require Membership Level', 'paid-memberships-pro' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			)
		);

		$element->add_control(
            'pmpro_require_membership', array(
                'type'        => Controls_Manager::SELECT2,
                'options'     => pmpro_elementor_get_all_levels(),
                'multiple'    => 'true',
				'label_block' => 'true',
				'description' => __( 'Require membership level to see this content.', 'paid-memberships-pro' ),
            )
        );

	}

	/**
	 * Filter sections to render content or not.
	 * If user doesn't have access, hide the section.
	 * @return boolean whether to show or hide section.
	 * @since 2.3
	 */
	public function pmpro_elementor_should_render( $should_render, $element ) {

		// Don't hide content in editor mode.
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return $should_render;
		}

		// Bypass if it's already hidden.
		if ( $should_render === false ) {
			return $should_render;
		}
		
		// Checks if the element is restricted and then if the user has access.
		$should_render = $this->pmpro_elementor_has_access( $element );

		return apply_filters( 'pmpro_elementor_section_access', $should_render, $element );
	}

	/**
	 * Filter individual content for members.
	 * @return string Returns the content set from Elementor.
	 * @since 2.0
	 */
	public function pmpro_elementor_render_content( $content, $widget ){

        // Don't hide content in editor mode.
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return $content;
        }

		$show = $this->pmpro_elementor_has_access( $widget );
		
		if ( ! $show ) {
			$content = '';
		}
        
        return $content;
	}

	/**
	 * Figure out if the user has access to restricted content.
	 * @return bool True or false based if the user has access to the content or not.
	 * @since 2.3
	 */
	public function pmpro_elementor_has_access( $element ) {

		$element_settings = $element->get_active_settings();

		$restricted_levels = $element_settings['pmpro_require_membership'];

		// Just bail if the content isn't restricted at all.
		if ( ! $restricted_levels ) {
			return true;
		}
		
		if ( ! pmpro_hasMembershipLevel( $restricted_levels ) ) {
			$access = false;
		} else {
			$access = true;
		}

		return apply_filters( 'pmpro_elementor_has_access', $access, $element, $restricted_levels );
	}
}

new PMPro_Elementor_Content_Restriction;
