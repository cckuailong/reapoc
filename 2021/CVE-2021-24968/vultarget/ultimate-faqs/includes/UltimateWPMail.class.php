<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqUltimateWPMail' ) ) {
	/**
	 * Class to handle Ultimate WP Mail integration for Ultimate FAQs
	 *
	 * @since 2.0.0
	 */
	class ewdufaqUltimateWPMail {

		public function __construct() {

			add_filter( 'uwpm_register_custom_element_section', array( $this, 'add_element_section' ) );
			add_action( 'uwpm_register_custom_element', array( $this, 'add_elements' ) );
		}

		/**
		 * Adds in a section for UFAQ tags in Ultimate WP Mail
		 * @since 2.0.0
		 */
		public function add_element_section() {

			if ( ! function_exists( 'uwpm_register_custom_element_section' ) ) { return; }

			$args = array(
				'label' => 'FAQ Tags'
			);

			uwpm_register_custom_element_section( 'ewd_ufaq_uwpm_elements', $args );
		}

		/**
		 * Adds in tags for author and author email
		 * @since 2.0.0
		 */
		public function add_elements() { 

			if ( ! function_exists( 'uwpm_register_custom_element' ) ) { return; }

			$args = array(
				'label' 			=> 'FAQ Author',
				'callback_function' => array( $this, 'get_faq_author' ),
				'section' 			=> 'ewd_ufaq_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_ufaq_author', $args );

			$args = array(
				'label' 			=> 'FAQ Author Email',
				'callback_function' => array( $this, 'get_faq_author_email' ),
				'section' 			=> 'ewd_ufaq_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_ufaq_author_email', $args );
		}

		/**
		 * Returns the author of a specified FAQ
		 * @since 2.0.0
		 */
		public function get_faq_author( $params, $user ) {

			return ! empty( $params['post_id'] ) ? get_post_meta( $params['post_id'], 'EWD_UFAQ_Post_Author', true ) : '';
		}

		/**
		 * Returns the author email of a specified FAQ
		 * @since 2.0.0
		 */
		public function get_faq_author_email( $params, $user ) {

			return ! empty( $params['post_id'] ) ? get_post_meta( $params['post_id'], 'EWD_UFAQ_Post_Author_Email', true ) : '';
		}
	}
}