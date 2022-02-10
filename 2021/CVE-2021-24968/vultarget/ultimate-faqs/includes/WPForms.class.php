<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqWPForms' ) ) {
	/**
	 * Class to handle WP Forms integration for Ultimate FAQs
	 *
	 * @since 2.0.0
	 */
	class ewdufaqWPForms {

		public function __construct() {

			add_filter( 'wpforms_builder_settings_sections', array( $this, 'add_settings_panel' ) );
			add_action( 'wpforms_form_settings_panel_content', array( $this, 'add_settings' ) );

			add_action( 'wpforms_frontend_output_before', array( $this, 'js_localization' ), 10, 2 );

			add_action( 'wpforms_display_field_before', array( $this, 'display_faqs' ), 10, 2 );
			add_action( 'wpforms_display_field_after', array( $this, 'display_faqs' ), 10, 2 );

			add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Adds an FAQs settings panel to the WP Forms admin screen
		 * @since 2.0.0
		 */
		public function add_settings_panel( $panels ) {

			$panels['ufaq'] = esc_html__( 'FAQs', 'ultimate-faqs' );

			return $panels;

		}

		/**
		 * Adds an the settings to enable WP Forms integration
		 * @since 2.0.0
		 */
		public function add_settings( $instance ) { ?>

			<div class="wpforms-panel-content-section wpforms-panel-content-section-ufaq">

				<div class="wpforms-panel-content-section-title">
					<?php esc_html_e( 'Ultimate FAQs', 'ultimate-faqs' ); ?>
				</div>
		
				<?php

				wpforms_panel_field(
					'radio',
					'settings',
					'ufaq_enabled',
					$instance->form_data,
					esc_html__( 'Disable FAQ display for this form, or enable it only on specific fields.', 'wpforms-lite' ),
					array(
						'options' => array(
							'enabled' => array( 'label' => 'Enable' ),
							'disabled' => array( 'label' => 'Disable' ),
							'specific' => array( 'label' => 'Specific Field' )
						)
					)
				);
		
				wpforms_panel_field(
					'select',
					'settings',
					'ufaq_selected_field',
					$instance->form_data,
					esc_html__( 'If FAQs are set to a specific field, which field should FAQs be displayed for?', 'wpforms-lite' ),
					array(
						'field_map' => array(
							'text',
							'textarea'
						),
						'placeholder' => __( '-- Select Field --', 'ultimate-faqs' ),
					)
				);

				?>
		
			</div>

		<?php }

		/**
		 * Pass the target field and other form data to the front-end
		 * @since 2.0.0
		 */
		public function js_localization( $form_data, $form ) {

			$target_field = $this->get_target_field( $form_data );

			wp_localize_script( 
				'ewd-ufaq-js', 
				'wpforms_integration', 
				array(
					'ufaq_enabled' => isset ( $form_data['settings']['ufaq_enabled'] ) ? $form_data['settings']['ufaq_enabled'] : 'enabled',
					'ufaq_selected_field' => $target_field,
					'form_id' => $form_data['id']
				)
			);
		}

		public function enqueue_scripts() {

			wp_enqueue_style( 'ewd-ufaq-wpforms-css', EWD_UFAQ_PLUGIN_URL . '/assets/css/ewd-ufaq-wpforms.css', array(), EWD_UFAQ_VERSION );
		}
	
		public function display_faqs( $field, $form_data ) {
			global $ewd_ufaq_controller;

			$target_field = $this->get_target_field( $form_data );

			if ( isset( $form_data['settings']['ufaq_enabled'] ) and $form_data['settings']['ufaq_enabled'] == 'disabled' ) { return; }

			if ( $field['id'] != $target_field ) { return; }

			if ( current_action() == 'wpforms_display_field_after' and $ewd_ufaq_controller->settings->get_setting( 'wpforms-faq-location' ) == 'above' ) { return; }

			if ( current_action() == 'wpforms_display_field_before' and $ewd_ufaq_controller->settings->get_setting( 'wpforms-faq-location' ) == 'below' ) { return; }

			$post_count = $ewd_ufaq_controller->settings->get_setting( 'wpforms-post-count' );
		
			?>

			<div class="ewd-ufaq-wpforms-label ewd-ufaq-hidden"><?php esc_html_e( 'Possible FAQs related to your message:', 'ultimate-faqs' ); ?></div>
			<div class="ewd-ufaq-wpforms-faq-results ewd-ufaq-hidden"><?php echo do_shortcode( '[ultimate-faqs post__in_string="-1" post_count="' . $post_count . '"]' ); ?></div>

		<?php }

		/**
		 * Determine which field FAQs should be displayed for
		 * @since 2.0.0
		 */
		public function get_target_field( $form_data ) {
			
			if ( isset ( $form_data['settings']['ufaq_enabled'] ) and $form_data['settings']['ufaq_enabled'] == 'specific' ) {

				return isset( $form_data['settings']['ufaq_selected_field'] ) ? $form_data['settings']['ufaq_selected_field'] : 0;
			}

			if ( ! isset( $form_data['settings']['ufaq_enabled'] ) or ( isset ( $form_data['settings']['ufaq_enabled'] ) and $form_data['settings']['ufaq_enabled'] != 'disabled' ) ) {
				
				foreach ( $form_data['fields'] as $field_id => $field ){
					
					if ( $field['type'] == 'textarea' ) {
						
						return $field['id'];
					}
				}
			}

			return false;
		}
	}
}