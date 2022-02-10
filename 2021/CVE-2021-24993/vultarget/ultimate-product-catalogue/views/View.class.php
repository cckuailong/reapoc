<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 5.0.0
 */
class ewdupcpView extends ewdupcpBase {

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
	 * @since 5.0.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'us_content_map_ewdupcpView'
		$this->content_map = apply_filters( 'ewd_upcp_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 5.0.0
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
	 * First, it looks in the current theme's /ewd-upcp-templates/ directory. Then it
	 * will check a parent theme's /ewd-upcp-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 5.0.0
	 * @param string template Type of template to load (eg - reviews, review)
	 */
	function find_template( $template ) {

		$this->template_dirs = array(
			get_stylesheet_directory() . '/' . EWD_UPCP_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . EWD_UPCP_TEMPLATE_DIR . '/',
			EWD_UPCP_PLUGIN_DIR . '/' . EWD_UPCP_TEMPLATE_DIR . '/'
		);
		
		$this->template_dirs = apply_filters( 'ewd_upcp_template_directories', $this->template_dirs );

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
		global $ewd_upcp_controller;

		return ! empty( $this->$option_name ) ? $this->$option_name : $ewd_upcp_controller->settings->get_setting( $option_name );
	}

	public function get_label( $label_name ) { 
		global $ewd_upcp_controller;
		
		if ( empty( $this->label_defaults ) ) { $this->set_label_defaults(); }

		return ! empty( $ewd_upcp_controller->settings->get_setting( $label_name ) ) ? $ewd_upcp_controller->settings->get_setting( $label_name ) : $this->label_defaults[ $label_name ];
	}

	public function set_label_defaults() {

		$this->label_defaults = array(
			'label-categories'					=> __( 'Categories', 'ultimate-product-catalogue' ),
			'label-subcategories'				=> __( 'Sub-Categories', 'ultimate-product-catalogue' ),
			'label-tags'						=> __( 'Tags', 'ultimate-product-catalogue' ),
			'label-custom-fields'				=> __( 'Custom Fields', 'ultimate-product-catalogue' ),
			'label-show-all'					=> __( 'Clear All', 'ultimate-product-catalogue' ),
			'label-details'						=> __( 'Details', 'ultimate-product-catalogue' ),
			'label-sort-by'						=> __( 'Sort By', 'ultimate-product-catalogue' ),
			'label-price-ascending'				=> __( 'Price (Ascending)', 'ultimate-product-catalogue' ),
			'label-price-descending'			=> __( 'Price (Descending)', 'ultimate-product-catalogue' ),
			'label-name-ascending'				=> __( 'Name (Ascending)', 'ultimate-product-catalogue' ),
			'label-name-descending'				=> __( 'Name (Descending)', 'ultimate-product-catalogue' ),
			'label-rating-ascending'			=> __( 'Rating (Ascending)', 'ultimate-product-catalogue' ),
			'label-rating-descending'			=> __( 'Rating (Descending)', 'ultimate-product-catalogue' ),
			'label-date-ascending'				=> __( 'Newest', 'ultimate-product-catalogue' ),
			'label-date-descending'				=> __( 'Oldest', 'ultimate-product-catalogue' ),
			'label-product-name-search'			=> __( 'Product Search', 'ultimate-product-catalogue' ),
			'label-product-name-text'			=> __( 'Search...', 'ultimate-product-catalogue' ),
			'label-back-to-catalog'				=> __( 'Catalog', 'ultimate-product-catalogue' ),
			'label-updating-results'			=> __( 'Updating Results...', 'ultimate-product-catalogue' ),
			'label-no-results-found'			=> __( 'No Results Found', 'ultimate-product-catalogue' ),
			'label-products-pagination'			=> __( ' products', 'ultimate-product-catalogue' ),
			'label-read-more'					=> __( 'Read More', 'ultimate-product-catalogue' ),
			'label-product-details-tab'			=> __( 'Product Details', 'ultimate-product-catalogue' ),
			'label-additional-info-tab'			=> __( 'Additional Information', 'ultimate-product-catalogue' ),
			'label-contact-form-tab'			=> __( 'Contact Us', 'ultimate-product-catalogue' ),
			'label-faqs-tab'					=> __( 'FAQs', 'ultimate-product-catalogue' ),
			'label-product-inquiry-form-title'	=> __( 'Product Inquiry Form', 'ultimate-product-catalogue' ),
			'label-customer-reviews-tab'		=> __( 'Customer Reviews', 'ultimate-product-catalogue' ),
			'label-related-products'			=> __( 'Related Products', 'ultimate-product-catalogue' ),
			'label-next-product'				=> __( 'Next Product', 'ultimate-product-catalogue' ),
			'label-previous-product'			=> __( 'Previous Product', 'ultimate-product-catalogue' ),
			'label-page'						=> __( 'Page ', 'ultimate-product-catalogue' ),
			'label-pagination-of'				=> __( ' of ', 'ultimate-product-catalogue' ),
			'label-compare'						=> __( 'Compare', 'ultimate-product-catalogue' ),
			'label-sale'						=> __( 'Sale', 'ultimate-product-catalogue' ),
			'label-side-by-side'				=> __( 'side by side', 'ultimate-product-catalogue' ),
			'label-inquire-button'				=> __( 'Inquire', 'ultimate-product-catalogue' ),
			'label-add-to-cart-button'			=> __( 'Add to Cart', 'ultimate-product-catalogue' ),
			'label-send-inquiry'				=> __( 'Send Inquiry!', 'ultimate-product-catalogue' ),
			'label-checkout'					=> __( 'Checkout!', 'ultimate-product-catalogue' ),
			'label-empty-cart'					=> __( 'or empty cart', 'ultimate-product-catalogue' ),
			'label-cart-items'					=> __( '%s items in cart', 'ultimate-product-catalogue' ),
			'label-additional-info-category'	=> __( 'Category', 'ultimate-product-catalogue' ),
			'label-additional-info-subcategory'	=> __( 'Sub-Category', 'ultimate-product-catalogue' ),
			'label-additional-info-tags'		=> __( 'Tags', 'ultimate-product-catalogue' ),
			'label-price-filter'				=> __( 'Price', 'ultimate-product-catalogue' ),
			'label-product-inquiry-please-use'	=> __( '??', 'ultimate-product-catalogue' ),	
		);
	}

	public function add_custom_styling() {
		global $ewd_upcp_controller;

		echo '<style>';
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-action-button-background-color' ) != '' ) { echo '.ewd-upcp-product-action-button { border-color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-action-button-background-color' ) . ' !important; color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-action-button-background-color' ) . ' !important; } .ewd-upcp-product-action-button:hover { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-action-button-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-action-button-text-color' ) != '' ) { echo '.ewd-upcp-product-action-button:hover { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-action-button-text-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-background-color' ) != '' ) { echo '.ewd-upcp-product-comparison-button { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-text-color' ) != '' ) { echo '.ewd-upcp-product-comparison-button { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-text-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-clicked-background-color' ) != '' ) { echo '.ewd-upcp-product-comparison-button.ewd-upcp-comparison-clicked { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-clicked-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-clicked-text-color' ) != '' ) { echo '.ewd-upcp-product-comparison-button.ewd-upcp-comparison-clicked { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-clicked-text-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-font-size' ) != '' ) { echo '.ewd-upcp-product-comparison-button span { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sale-button-background-color' ) != '' ) { echo '.ewd-upcp-sale-price { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sale-button-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sale-button-text-color' ) != '' ) { echo '.ewd-upcp-sale-price { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sale-button-text-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sale-button-font-size' ) != '' ) { echo '.ewd-upcp-sale-price span { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-compare-button-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-title-font-color' ) != '' ) { echo '.ewd-upcp-product-comparison-product a { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-title-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-title-font-size' ) != '' ) { echo '.ewd-upcp-product-comparison-product a { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-title-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-font-size' ) != '' ) { echo '.ewd-upcp-product-comparison-price { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-font-color' ) != '' ) { echo '.ewd-upcp-product-comparison-price { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-background-color' ) != '' ) { echo '.ewd-upcp-product-comparison-price { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-product-comparison-price-background-color' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-image-border-color' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail-image-div { border: 5px solid ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-image-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-min-height' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail { min-height: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-min-height' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-max-height' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail { max-height: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-max-height' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-padding' ) != '' ) { echo '.ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-thumbnail { padding: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-box-padding' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-border-color' ) != '' ) { echo '.ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-thumbnail, .ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-thumbnail .ewd-upcp-product-action-button { border-color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail-body-div a { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail-body-div a { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail-body-div a { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-title-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail .ewd-upcp-catalog-product-price { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail .ewd-upcp-catalog-product-price { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail .ewd-upcp-catalog-product-price { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-price-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-background-color' ) != '' ) { echo '.ewd-upcp-catalog-product-thumbnail { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-thumbnail-view-background-color' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-image-border-color' ) != '' ) { echo '.ewd-upcp-catalog-product-list-image-div { border: 5px solid ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-image-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-border-color' ) != '' ) { echo '.ewd-upcp-catalog-product-list-content { border: 5px solid ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-padding' ) != '' ) { echo '.ewd-upcp-catalog-product-list-content { padding: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-padding' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-margin-top' ) != '' ) { echo '.ewd-upcp-catalog-product-list-content { margin-top: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-box-margin-top' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font' ) != '' ) { echo '.ewd-upcp-catalog-product-list a.ewd-upcp-product-title { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-list a.ewd-upcp-product-title { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-list a.ewd-upcp-product-title { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-title-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font' ) != '' ) { echo '.ewd-upcp-catalog-product-list .ewd-upcp-catalog-product-price { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-list .ewd-upcp-catalog-product-price { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-list .ewd-upcp-catalog-product-price { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-list-view-price-font-color' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-image-border-color' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-image-div img { border: 5px solid ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-image-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-padding' ) != '' ) { echo '.ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-detail { padding: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-padding' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-margin' ) != '' ) { echo '.ewd-upcp-catalog-product-detail { margin-top: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-margin' ) . 'px !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-background-color' ) != '' ) { echo '.ewd-upcp-catalog-product-detail { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-box-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-border-color' ) != '' ) { echo '.ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-detail, .ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-detail-mid-div, .ewd-upcp-catalog-main-block .ewd-upcp-catalog-product-detail .ewd-upcp-product-action-button { border-color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-border-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-mid-div a.ewd-upcp-product-title { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-mid-div a.ewd-upcp-product-title { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-mid-div a.ewd-upcp-product-title { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-title-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-end-div .ewd-upcp-catalog-product-price { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font-size' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-end-div .ewd-upcp-catalog-product-price { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font-color' ) != '' ) { echo '.ewd-upcp-catalog-product-detail-end-div .ewd-upcp-catalog-product-price { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-detail-view-price-font-color' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-title-hover' ) == 'underline' ) { echo '.ewd-upcp-catalog-sidebar-sort > span:hover, .ewd-upcp-catalog-sidebar-search > span:hover, .ewd-upcp-catalog-sidebar-price-filter > span:hover, .ewd-upcp-catalog-sidebar-title:hover { text-decoration: underline !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-sort > span, .ewd-upcp-catalog-sidebar-search > span, .ewd-upcp-catalog-sidebar-price-filter > span, .ewd-upcp-catalog-sidebar-title { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-size' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-sort > span, .ewd-upcp-catalog-sidebar-search > span, .ewd-upcp-catalog-sidebar-price-filter > span, .ewd-upcp-catalog-sidebar-title { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-color' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-sort > span, .ewd-upcp-catalog-sidebar-search > span, .ewd-upcp-catalog-sidebar-price-filter > span, .ewd-upcp-catalog-sidebar-title { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-weight' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-sort > span, .ewd-upcp-catalog-sidebar-search > span, .ewd-upcp-catalog-sidebar-price-filter > span, .ewd-upcp-catalog-sidebar-title { font-weight: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-header-font-weight' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-content label { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-size' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-content label { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-color' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-content label { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-weight' ) != '' ) { echo '.ewd-upcp-catalog-sidebar-content label { font-weight: ' . $ewd_upcp_controller->settings->get_setting( 'styling-sidebar-checkbox-font-weight' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font' ) != '' ) { echo '.ewd-upcp-single-product-breadcrumb-link a { font-family: ' . $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-size' ) != '' ) { echo '.ewd-upcp-single-product-breadcrumb-link a { font-size: ' . $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-size' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-color' ) != '' ) { echo '.ewd-upcp-single-product-breadcrumb-link a { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-hover-color' ) != '' ) { echo '.ewd-upcp-single-product-breadcrumb-link a:hover { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-breadcrumbs-font-hover-color' ) . ' !important; }'; }

			if ( $ewd_upcp_controller->settings->get_setting( 'styling-pagination-background-color' ) != '' ) { echo '.ewd-upcp-pagination .pagination-links a { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-pagination-background-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-pagination-text-color' ) != '' ) { echo '.ewd-upcp-pagination .pagination-links a { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-pagination-text-color' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-pagination-background-color-hover' ) != '' ) { echo '.ewd-upcp-pagination .pagination-links a:hover { background: ' . $ewd_upcp_controller->settings->get_setting( 'styling-pagination-background-color-hover' ) . ' !important; }'; }
			if ( $ewd_upcp_controller->settings->get_setting( 'styling-pagination-text-color-hover' ) != '' ) { echo '.ewd-upcp-pagination .pagination-links a:hover { color: ' . $ewd_upcp_controller->settings->get_setting( 'styling-pagination-text-color-hover' ) . ' !important; }'; }

		echo  '</style>';
	}

}
