<?php

/**
 * Class to display an FAQ search form, and possible FAQs on load, on the front end.
 *
 * @since 2.0.0
 */
class ewdufaqViewFAQSearch extends ewdufaqViewFAQs {

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 2.0.0
	 */
	public function render() {
		global $ewd_ufaq_controller;

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		if ( empty( $this->show_on_load ) ) { $this->faqs = array(); }

		$this->set_faqs_options();

		$this->create_faq_data();

		// Add css classes to the slider
		$this->classes = $this->get_classes();

		ob_start();

		$this->add_custom_styling();

		$template = $this->find_template( 'faqs-search' );
		if ( $template ) {
			include( $template );
		}

		$template = $this->find_template( 'faqs' );
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		if ( $ewd_ufaq_controller->settings->get_setting( 'display-style' ) == 'list' ){

			$output = $this->replace_list_header( $output );
		}

		return apply_filters( 'ewd_ufaq_faqs_output', $output, $this );
	}

	/**
	 * Print the shortcode args if they will not otherwise be printed
	 * @since 2.0.0
	 */
	public function maybe_print_shortcode_args() {

		if ( $this->show_on_load ) { return; }

		$template = $this->find_template( 'faq-shortcode-args' );
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the shortcode args if they will not otherwise be printed
	 * @since 2.0.0
	 */
	public function maybe_print_search_submit() {
		global $ewd_ufaq_controller;

		if ( $ewd_ufaq_controller->settings->get_setting( 'auto-complete-titles' ) ) { return; }

		$template = $this->find_template( 'faqs-search-submit' );
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Get the initial submit faq css classes
	 * @since 2.0.0
	 */
	public function get_classes( $classes = array() ) {
		global $ewd_ufaq_controller;

		$classes = array_merge(
			$classes,
			array(
				'ewd-ufaq-faq-list',
				'ewd-ufaq-page-type-' . $ewd_ufaq_controller->settings->get_setting( 'page-type' ),
				'ewd-ufaq-search'
			)
		);

		if ( $ewd_ufaq_controller->settings->get_setting( 'faq-category-accordion' ) ) {
			$classes[] = 'ewd-ufaq-faq-category-title-accordion';
		}

		return apply_filters( 'ewd_ufaq_faq_search_classes', $classes, $this );
	}
}
