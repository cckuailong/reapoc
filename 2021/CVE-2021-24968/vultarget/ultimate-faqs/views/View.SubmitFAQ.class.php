<?php

/**
 * Class to display the submit FAQ form on the front end.
 *
 * @since 2.0.0
 */
class ewdufaqViewSubmitFAQ extends ewdufaqView {

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 2.0.0
	 */
	public function render() {

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		$this->set_variables();

		// Add css classes to the slider
		$this->classes = $this->get_classes();

		ob_start();
		$this->add_custom_styling();
		$template = $this->find_template( 'submit-faq' );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'ewd_ufaq_submit_faq_output', $output, $this );
	}

	/**
	 * Display the result of a submitted faq, if one was submitted
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_submitted_faq_message() {
		
		if ( empty( $this->update_message ) ) { return; }
		
		$template = $this->find_template( 'submit-faq-submitted-faq-message' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the question title field
	 *
	 * @since 2.0.0
	 */
	public function print_question_title_field() {
		
		$template = $this->find_template( 'submit-faq-field-faq-question' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the answer field, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_answer_field() {
		global $ewd_ufaq_controller;
		
		if ( ! $ewd_ufaq_controller->settings->get_setting( 'allow-proposed-answer' ) ) { return; }
		
		$template = $this->find_template( 'submit-faq-field-faq-answer' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the custom fields, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_custom_fields() {
		global $ewd_ufaq_controller;
		
		if ( ! $ewd_ufaq_controller->settings->get_setting( 'submit-custom-fields' ) ) { return; }
		
		foreach ( $this->get_custom_fields() as $field ) { 

			$this->print_custom_field( $field );
		}
	}

	/**
	 * Returns the template for a custom form field
	 *
	 * @since 2.0.0
	 */
	public function print_custom_field( $field ) {

		$this->current_field = $field;

		$template = '';

		if ( $this->current_field->type == 'text' ) {

			$template = $this->find_template( 'submit-faq-custom-field-text' ); 
		}
		elseif ( $this->current_field->type == 'textarea' ) {

			$template = $this->find_template( 'submit-faq-custom-field-textarea' ); 
		}
		elseif ( $this->current_field->type == 'dropdown' ) {

			$template = $this->find_template( 'submit-faq-custom-field-select' ); 
		}
		elseif ( $this->current_field->type == 'checkbox' ) {

			$template = $this->find_template( 'submit-faq-custom-field-checkbox' ); 
		}
		elseif ( $this->current_field->type == 'radio' ) {

			$template = $this->find_template( 'submit-faq-custom-field-radio' ); 
		}
		elseif ( $this->current_field->type == 'date' ) {

			$template = $this->find_template( 'submit-faq-custom-field-date' ); 
		}
		elseif ( $this->current_field->type == 'datetime' ) {

			$template = $this->find_template( 'submit-faq-custom-field-datetime' ); 
		}

		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the answer field, if enabled
	 *
	 * @since 2.0.0
	 */
	public function print_author_field() {

		$template = $this->find_template( 'submit-faq-field-author' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the custom fields, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_author_email_field() {
		global $ewd_ufaq_controller;
		
		if ( ! $ewd_ufaq_controller->settings->get_setting( 'submit-faq-email' ) ) { return; }
		
		$template = $this->find_template( 'submit-faq-field-author-email' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Display the captcha field, if enabled
	 *
	 * @since 2.0.0
	 */
	public function maybe_print_captcha_field() {
		global $ewd_ufaq_controller;
		
		if ( ! $ewd_ufaq_controller->settings->get_setting( 'submit-question-captcha' ) ) { return; }
		
		$template = $this->find_template( 'submit-faq-captcha' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print a nonce field that can be checked when an FAQ is submitted
	 *
	 * @since 2.0.0
	 */
	public function print_nonce_field() {
		
		echo wp_nonce_field( -1, '_wp_nonce', true, false);
	}

	/**
	 * Print a referer field that can be checked when an FAQ is submitted
	 *
	 * @since 2.0.0
	 */
	public function print_referer_field() {
		
		echo wp_referer_field( false );
	}

	/**
	 * Returns an array of the custom fields that exist for this site
	 *
	 * @since 2.0.0
	 */
  	public function get_custom_fields() {
  		global $ewd_ufaq_controller;

  		return ewd_ufaq_decode_infinite_table_setting( $ewd_ufaq_controller->settings->get_setting( 'faq-fields' ) );
  	}

	/**
	 * Returns the name of a field, if it exists
	 *
	 * @since 2.0.0
	 */
	public function get_custom_field_name() {

		if ( empty( $this->current_field ) or empty( $this->current_field->name ) ) { 

			return; 
		}

		return $this->current_field->name;
	}

	/**
	 * Returns the input name of a field, if it exists
	 *
	 * @since 2.0.0
	 */
	public function get_custom_field_input_name() {

		if ( empty( $this->current_field ) or empty( $this->current_field->id ) ) { 

			return; 
		}

		return 'ewd_ufaq_custom_field_' . $this->current_field->id;
	}

	/**
	 * Returns an array of options for the current faq field, or an empty array if not set
	 *
	 * @since 2.0.0
	 */
	public function get_custom_field_options() {

		if ( empty( $this->current_field ) or empty( $this->current_field->options ) ) { 

			return array(); 
		}

		return explode( ',', $this->current_field->options );
	}

	/**
	 * Returns the image for the captcha field
	 *
	 * @since 2.0.0
	 */
	public function create_captcha_image() {

		$im = imagecreatetruecolor( 50, 24 );
		$bg = imagecolorallocate( $im, 22, 86, 165 );  
		$fg = imagecolorallocate( $im, 255, 255, 255 ); 
		imagefill( $im, 0, 0, $bg );
		imagestring( $im, 5, 5, 5,  $this->get_captcha_image_code(), $fg );

  		$five_mb = 5 * 1024 * 1024;
  		$stream = fopen( 'php://temp/maxmemory:{$five_mb}', 'r+' );
  		imagepng( $im, $stream );
  		imagedestroy( $im );
  		rewind( $stream );

  		return base64_encode( stream_get_contents( $stream ) );
  	}

  	public function get_captcha_image_code() {

  		return ( $this->captcha_form_code / 3 ) - 5;
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
				'ewd-ufaq-submit-faq-form',
			)
		);

		return apply_filters( 'ewd_ufaq_submit_faq_classes', $classes, $this );
	}


	/**
	 * Set any neccessary variables before displaying the form
	 * @since 2.0.0
	 */
	public function set_variables() {
		global $ewd_ufaq_controller;

		$this->submit_text = ! empty( $this->submit_text ) ? $this->submit_text : $this->get_label( 'label-send-question' );
		$this->submit_faq_form_title = ! empty( $this->submit_faq_form_title ) ? $this->submit_faq_form_title : $this->get_label( 'label-submit-question' );
		$this->submit_faq_instructions = ! empty( $this->submit_faq_instructions ) ? $this->submit_faq_instructions : $this->get_label( 'label-please-fill-form-below' );

		if ( $ewd_ufaq_controller->settings->get_setting( 'submit-question-captcha' ) ) {
			
			$this->captcha_form_code = ( rand( 1000, 9999 ) + 5 ) * 3;
		}
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		
		wp_enqueue_style( 'ewd-ufaq-rrssb' );
		wp_enqueue_style( 'ewd-ufaq-jquery-ui' );
		wp_enqueue_style( 'ewd-ufaq-css' );
	}
}
