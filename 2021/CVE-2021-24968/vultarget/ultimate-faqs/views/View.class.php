<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 2.0.0
 */
class ewdufaqView extends ewdufaqBase {

	/**
	 * Post type to render
	 */
	public $post_type = null;

	/**
	 * Map types of content to the template which will render them
	 */
	public $content_map = array(
		'title'							 => 'content/title',
	);

	/**
	 * Initialize the class
	 * @since 2.0.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'us_content_map_ewdufaqView'
		$this->content_map = apply_filters( 'ewd_ufaq_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 2.0.0
	 */
	public function render() {

		$this->set_error(
			array( 
				'type'		=> 'render() called on wrong class'
			)
		);
	}

	/**
	 * Load a template file for views
	 *
	 * First, it looks in the current theme's /ewd-ufaq-templates/ directory. Then it
	 * will check a parent theme's /ewd-ufaq-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 2.0.0
	 * @param string template Type of template to load (eg - reviews, review)
	 */
	function find_template( $template ) {

		$this->template_dirs = array(
			get_stylesheet_directory() . '/' . EWD_UFAQ_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . EWD_UFAQ_TEMPLATE_DIR . '/',
			EWD_UFAQ_PLUGIN_DIR . '/' . EWD_UFAQ_TEMPLATE_DIR . '/'
		);
		
		$this->template_dirs = apply_filters( 'ewd_ufaq_template_directories', $this->template_dirs );

		foreach ( $this->template_dirs as $dir ) {
			if ( file_exists( $dir . $template . '.php' ) ) {
				return $dir . $template . '.php';
			}
		}

		return false;
	}

	/**
	 * Enqueue stylesheets
	 */
	public function enqueue_assets() {

		//enqueue assets here
	}

	public function get_option( $option_name ) {
		global $ewd_ufaq_controller;

		return ! empty( $this->$option_name ) ? $this->$option_name : $ewd_ufaq_controller->settings->get_setting( $option_name );
	}

	public function get_label( $label_name ) {
		global $ewd_ufaq_controller;

		if ( empty( $this->label_defaults ) ) { $this->set_label_defaults(); }

		return ! empty( $ewd_ufaq_controller->settings->get_setting( $label_name ) ) ? $ewd_ufaq_controller->settings->get_setting( $label_name ) : $this->label_defaults[ $label_name ];
	}

	public function set_label_defaults() {

		$this->label_defaults = array(
			'label-posted'								=> __( 'Posted', 'ultimate-faqs' ),
			'label-by'									=> __( 'by', 'ultimate-faqs' ),
			'label-on'									=> __( 'on', 'ultimate-faqs' ),
			'label-enter-question'						=> __( 'Enter your question', 'ultimate-faqs' ),
			'label-search'								=> __( 'Search', 'ultimate-faqs' ),
			'label-permalink'							=> __( 'Permalink', 'ultimate-faqs' ),
			'label-back-to-top'							=> __( 'Back to Top', 'ultimate-faqs' ),
			'label-expand-all'							=> __( 'Expand All', 'ultimate-faqs' ),
			'label-collapse-all'						=> __( 'Collapse All', 'ultimate-faqs' ),
			'label-previous'							=> __( 'Previous', 'ultimate-faqs' ),
			'label-next'								=> __( 'Next', 'ultimate-faqs' ),
			'label-load-more'							=> __( 'Load More', 'ultimate-faqs' ),
			'label-woocommerce-tab'						=> __( 'FAQs', 'ultimate-faqs' ),
			'label-share-faq'							=> __( 'Share', 'ultimate-faqs' ),
			'label-find-faq-helpful'					=> __( 'Did you find this FAQ helpful?', 'ultimate-faqs' ),
			'label-search-placeholder'					=> __( 'Enter your question...', 'ultimate-faqs' ),
			'label-thank-you-submit'					=> __( 'Thank you for submitting an FAQ.', 'ultimate-faqs' ),
			'label-submit-question'						=> __( 'Submit a Question', 'ultimate-faqs' ),
			'label-please-fill-form-below'				=> __( 'Please fill out the form below to submit a question.', 'ultimate-faqs' ),
			'label-send-question'						=> __( 'Send Question', 'ultimate-faqs' ),
			'label-question-title'						=> __( 'Question Title', 'ultimate-faqs' ),
			'label-question-title-explanation'			=> __( 'What question is being answered?', 'ultimate-faqs' ),
			'label-proposed-answer'						=> __( 'Proposed Answer', 'ultimate-faqs' ),
			'label-proposed-answer-explanation'			=> __( 'What answer should be displayed for this question?', 'ultimate-faqs' ),
			'label-question-author'						=> __( 'FAQ Author', 'ultimate-faqs' ),
			'label-question-author-explanation'			=> __( 'What name should be displayed with your FAQ?', 'ultimate-faqs' ),
			'label-question-author-email'				=> __( 'Author Email', 'ultimate-faqs' ),
			'label-question-author-email-explanation'	=> __( 'This is only used to verify the authenticity of the FAQ. It is not displayed anywhere.', 'ultimate-faqs' ),
			'label-captcha-image-number'				=> __( 'Image Number', 'ultimate-faqs' ),
			'label-no-results-found'					=> __( 'No result FAQs contained the term \'%s\'', 'ultimate-faqs' ),
		);
	}

	public function add_custom_styling() {
		global $ewd_ufaq_controller;

		$heading_type = $ewd_ufaq_controller->settings->get_setting( 'styling-faq-heading-type' );
		$category_heading_type = $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-type' );

		$css = '';

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-background-color' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { background-color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-background-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-font-color' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-size' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { border-width: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-color' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { border-color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-radius' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { border-radius: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-border-radius' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-symbol-size' ) != '' ) { $css .= '.ewd-ufaq-post-margin-symbol { font-size: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-toggle-symbol-size' ) . ' !important; }'; }
			
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-block-background-color' ) != '' ) { 

				$css .= ".ewd-ufaq-faq-display-style-block.ewd-ufaq-post-active, .ewd-ufaq-faq-display-style-block.ewd-ufaq-post-active a,.ewd-ufaq-faq-display-style-block:hover, .ewd-ufaq-faq-display-style-block:hover a, .ewd-ufaq-faq-display-style-block:hover $heading_type { background-color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-block-background-color' ) . ' !important; }';
				$css .= ".ewd-ufaq-faq-display-style-border_block.ewd-ufaq-post-active, .ewd-ufaq-faq-display-style-border_block:hover, .ewd-ufaq-faq-display-style-border_block:hover $heading_type { background-color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-block-background-color' ) . ' !important; }';
				$css .= ".ewd-ufaq-faq-display-style-border_block .ewd-ufaq-faq-body { border-color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-block-background-color' ) . ' !important; }';
				$css .= ".ewd-ufaq-faq-display-style-border_block .comment-reply-title, .ewd-ufaq-faq-display-style-border_block:hover .comment-reply-title { background-color: transparent !important; }";
			}

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-block-font-color' ) != '' ) { 

				$css .= ".ewd-ufaq-faq-display-style-block:hover .ewd-ufaq-post-margin-symbol, .ewd-ufaq-faq-display-style-block:hover $heading_type { color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-block-font-color' ) . ' !important; }';
				$css .= ".ewd-ufaq-faq-display-style-border_block:hover .ewd-ufaq-post-margin-symbol, .ewd-ufaq-faq-display-style-border_block:hover $heading_type { color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-block-font-color' ) . ' !important; }';
			}

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-list-font' ) != '' ) { $css .= '.ewd-ufaq-faq-header-title a { font-family: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-list-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-list-font-size' ) != '' ) { $css .= '.ewd-ufaq-faq-header-title a { font-size: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-list-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-list-font-color' ) != '' ) { $css .= '.ewd-ufaq-faq-header-title a { color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-list-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-list-margin' ) != '' ) { $css .= '.ewd-ufaq-faq-header-title a { margin: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-list-margin' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-list-padding' ) != '' ) { $css .= '.ewd-ufaq-faq-header-title a { padding: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-list-padding' ) . ' !important; }'; }

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-font' ) != '' ) { $css .= "div.ewd-ufaq-faq-title $heading_type { font-family: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-font-size' ) != '' ) { $css .= "div.ewd-ufaq-faq-title $heading_type { font-size: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-font-color' ) != '' ) { $css .= "div.ewd-ufaq-faq-title $heading_type { color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-margin' ) != '' ) { $css .= "div.ewd-ufaq-faq-title $heading_type { margin: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-margin' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-padding' ) != '' ) { $css .= "div.ewd-ufaq-faq-title $heading_type { padding: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-padding' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-question-icon-top-margin' ) != '' ) { $css .= ".ewd-ufaq-post-margin-symbol { margin-top: " . $ewd_ufaq_controller->settings->get_setting( 'styling-question-icon-top-margin' ) . ' !important; }'; }
			
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font' ) != '' ) { $css .= 'div.ewd-ufaq-faq-post p { font-family: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font-size' ) != '' ) { $css .= 'div.ewd-ufaq-faq-post p { font-size: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font-color' ) != '' ) { $css .= 'div.ewd-ufaq-faq-post p { color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-answer-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-answer-margin' ) != '' ) { $css .= 'div.ewd-ufaq-faq-post p { margin: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-answer-margin' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-answer-padding' ) != '' ) { $css .= 'div.ewd-ufaq-faq-post p { padding: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-answer-padding' ) . ' !important; }'; }

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font' ) != '' ) { $css .= 'div.ewd-ufaq-author-date { font-family: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font-size' ) != '' ) { $css .= 'div.ewd-ufaq-author-date { font-size: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font-color' ) != '' ) { $css .= 'div.ewd-ufaq-author-date { color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-margin' ) != '' ) { $css .= 'div.ewd-ufaq-author-date { margin: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-margin' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-padding' ) != '' ) { $css .= 'div.ewd-ufaq-author-date { padding: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-postdate-padding' ) . ' !important; }'; }

			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font' ) != '' ) { $css .= "div.ewd-ufaq-faq-category-title $category_heading_type { font-family: " . $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font-size' ) != '' ) { $css .= "div.ewd-ufaq-faq-category-title $category_heading_type { font-size: " . $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font-color' ) != '' ) { $css .= "div.ewd-ufaq-faq-category-title $category_heading_type { color: " . $ewd_ufaq_controller->settings->get_setting( 'styling-category-heading-font-color' ) . ' !important; }'; }
			
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-font' ) != '' ) { $css .= 'div.ewd-ufaq-faq-categories, div.ewd-ufaq-faq-tags { font-family: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-category-font' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-font-size' ) != '' ) { $css .= 'div.ewd-ufaq-faq-categories, div.ewd-ufaq-faq-tags { font-size: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-category-font-size' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-font-color' ) != '' ) { $css .= 'div.ewd-ufaq-faq-categories, div.ewd-ufaq-faq-tags { color: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-category-font-color' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-margin' ) != '' ) { $css .= 'div.ewd-ufaq-faq-categories, div.ewd-ufaq-faq-tags { margin: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-category-margin' ) . ' !important; }'; }
			if ( $ewd_ufaq_controller->settings->get_setting( 'styling-category-padding' ) != '' ) { $css .= 'div.ewd-ufaq-faq-categories, div.ewd-ufaq-faq-tags { padding: ' . $ewd_ufaq_controller->settings->get_setting( 'styling-category-padding' ) . ' !important; }'; }

			$css .= $ewd_ufaq_controller->settings->get_setting( 'custom-css' );

		if( ! empty( $css ) ) {
			echo '<style>';
				echo $css;
			echo '</style>';
		}
	}

}
