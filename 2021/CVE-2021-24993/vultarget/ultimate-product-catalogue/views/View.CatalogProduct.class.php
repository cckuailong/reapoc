<?php

/**
 * Class to display a single product on the front end.
 *
 * @since 5.0.0
 */
class ewdupcpViewCatalogProduct extends ewdupcpViewProduct {

	public function __construct( $args ) {

		parent::__construct( $args );

		$this->set_variables();

		$this->set_catalog_links();
	} 

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 5.0.0
	 */
	public function render_view( $view ) {

		$this->view = $view;

		$this->classes = $this->get_classes();
		
		ob_start();

		if ( $view == 'thumbnail' ) { $template = $this->find_template( 'catalog-product-thumbnail' ); }
		elseif ( $view == 'list' ) { $template = $this->find_template( 'catalog-product-list' ); }
		elseif ( $view == 'detail' ) { $template = $this->find_template( 'catalog-product-detail' ); }
		elseif ( $view == 'minimal' ) { $template = $this->find_template( 'minimal-product' ); }
		
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'ewd_upcp_catalog_product_output_' . $view, $output, $this );
	}

	/**
	 * Print cart action button (add to cart or inquire ), depending on catalog style and location
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_cart_action_button( $location ) {
		global $ewd_upcp_controller;

		if ( $location == 'top' and ( $this->style != 'main-minimalist' and $this->style != 'contemporary' and $this->style != 'showcase' ) ) { return; }

		if ( $location == 'bottom' and ( $this->style == 'main-minimalist' or $this->style == 'contemporary' or $this->style == 'showcase' ) ) { return; }

		if ( ( empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-checkout' ) ) or empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-sync' ) ) ) and empty( $ewd_upcp_controller->settings->get_setting( 'product-inquiry-cart' ) ) ) { return; }
		
		$template = $this->find_template( 'catalog-product-cart-action-button' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print product comparison button, if enabled
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_product_comparison_button() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'product-comparison' ) ) ) { return; }
		
		$template = $this->find_template( 'catalog-product-product-comparison-button' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print sale flag, if enabled
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_sale_flag() {
		global $ewd_upcp_controller;

		if ( $this->product->current_price == $this->product->regular_price ) { return; }
		
		$template = $this->find_template( 'catalog-product-sale-flag' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print a product's sale price, if different from its regular price
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_sale_price() {
		global $ewd_upcp_controller;

		if ( $this->product->current_price == $this->product->regular_price ) { return; }
		
		$template = $this->find_template( 'catalog-product-sale-price' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the main image for this product
	 *
	 * @since 5.0.0
	 */
	public function print_image() {
		global $ewd_upcp_controller;
		
		if ( $ewd_upcp_controller->settings->get_setting( 'styling-catalog-skin' ) == 'main-hover' ) { $template = $this->find_template( 'catalog-product-image-hover' ); }
		else { $template = $this->find_template( 'catalog-product-image' ); }
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print a product's reviews rating, if reviews are turned on
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_rating() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'catalog-display-reviews' ) ) ) { return; }
		
		$template = $this->find_template( 'catalog-product-rating' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the product's title
	 *
	 * @since 5.0.0
	 */
	public function print_title() {
		
		$template = $this->find_template( 'catalog-product-title' );
		
		if ( $template ) {
			include( $template );
		}
	}	

	/**
	 * Print the product's content
	 *
	 * @since 5.0.0
	 */
	public function print_content() {
		
		$template = $this->find_template( 'catalog-product-content' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the product's content
	 *
	 * @since 5.0.0
	 */
	public function print_description_content() {
		
		$template = $this->find_template( 'catalog-product-decription-content' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Decide whether to print the categories in the current context
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_categories() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'display-categories-in-product-thumbnail' ) ) or empty( $this->product->categories ) ) { return; }
		
		$this->print_categories();
	}

	/**
	 * Print the product's categories
	 *
	 * @since 5.0.0
	 */
	public function print_categories() {
		
		$template = $this->find_template( 'catalog-product-categories' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Decide whether to print the sub-categories in the current context
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_subcategories() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'display-categories-in-product-thumbnail' ) ) or empty( $this->product->subcategories ) ) { return; }
		
		$this->print_subcategories();
	}

	/**
	 * Print the product's sub-categories
	 *
	 * @since 5.0.0
	 */
	public function print_subcategories() {
		
		$template = $this->find_template( 'catalog-product-subcategories' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Decide whether to print the tags in the current context
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_tags() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'display-tags-in-product-thumbnail' ) ) or empty( $this->product->tags ) ) { return; }
		
		$this->print_tags();
	}

	/**
	 * Print the product's tags
	 *
	 * @since 5.0.0
	 */
	public function print_tags() {
		
		$template = $this->find_template( 'catalog-product-tags' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Find the custom fields that should be printed for this product
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_custom_fields() {
		global $ewd_upcp_controller;

		$custom_fields = $ewd_upcp_controller->settings->get_custom_fields();

		foreach ( $custom_fields as $custom_field ) {

			if ( ! in_array( $this->view, $custom_field->displays ) ) { continue; }

			if ( $ewd_upcp_controller->settings->get_setting( 'hide-blank-custom-fields' ) and empty( $this->product->custom_fields[ $custom_field->id ] ) )  { continue; }

			$this->print_custom_field( $custom_field );
		}
	}

	/**
	 * Prints the hover styling
	 *
	 * @since 5.0.0
	 */
	public function maybe_print_hover_style() {
		global $ewd_upcp_controller;
		
		if ( $ewd_upcp_controller->settings->get_setting( 'styling-catalog-skin' ) != 'main-hover' ) { return; } 

		$template = $this->find_template( 'catalog-product-hover' );

		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the product's custom fields
	 *
	 * @since 5.0.0
	 */
	public function print_custom_field( $custom_field ) {

		$this->custom_field = $custom_field;

		$template = $this->find_template( 'catalog-product-custom-field' );

		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the product's price
	 *
	 * @since 5.0.0
	 */
	public function print_product_price() {
		
		$template = $this->find_template( 'catalog-product-price' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Returns the category label, based on the label or number of category terms
	 *
	 * @since 5.0.0
	 */
  	public function get_categories_label() {
  		global $ewd_upcp_controller;

  		return $ewd_upcp_controller->settings->get_setting( 'label-categories' ) ? $ewd_upcp_controller->settings->get_setting( 'label-categories' ) : ( sizeOf( $this->product->categories ) == 1 ? __( 'Category:', 'ultimate-products' ) : __( 'Categories:', 'ultimate-products' ) );
  	}

  	/**
	 * Returns the sub-category label, based on the label or number of category terms
	 *
	 * @since 5.0.0
	 */
  	public function get_subcategories_label() {
  		global $ewd_upcp_controller;

  		return $ewd_upcp_controller->settings->get_setting( 'label-subcategories' ) ? $ewd_upcp_controller->settings->get_setting( 'label-subcategories' ) : ( sizeOf( $this->product->subcategories ) == 1 ? __( 'Sub-Category:', 'ultimate-products' ) : __( 'Sub-Categories:', 'ultimate-products' ) );
  	}

  	/**
	 * Returns the tag label, based on the label or number of tag terms
	 *
	 * @since 5.0.0
	 */
  	public function get_tags_label() {
  		global $ewd_upcp_controller;

  		return $ewd_upcp_controller->settings->get_setting( 'label-tags' ) ? $ewd_upcp_controller->settings->get_setting( 'label-tags' ) : ( sizeOf( $this->product->tags ) == 1 ? __( 'Tag:', 'ultimate-products' ) : __( 'Tags:', 'ultimate-products' ) );
  	}

	/**
	 * Get the target for product details links
	 * @since 5.0.0
	 */
	public function get_product_link_target() {
		global $ewd_upcp_controller;

		return ( $ewd_upcp_controller->settings->get_setting( 'product-links' ) and $this->product->link ) ? 'target="_blank"' : '';
	}

	/**
	 * Returns the correct label for the product action button
	 * @since 5.0.0
	 */
	public function get_action_button_label() {
		global $ewd_upcp_controller;

		return ( ! empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-checkout' ) ) and ! empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-sync' ) ) ) ? $this->get_label( 'label-add-to-cart-button' ) : $this->get_label( 'label-inquire-button' );
	}

	/**
	 * Returns the product description for the 'Detail' view, with read more if necessary
	 * @since 5.0.0
	 */
	public function get_detail_view_content() {
		global $ewd_upcp_controller;

		$description = esc_html( strip_tags( $this->product->get_product_description() ) );

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'details-read-more' ) ) ) {

			return $description;
		}

		return substr( $description, 0, $ewd_upcp_controller->settings->get_setting( 'details-description-characters' ) ) . ( strlen( $description ) > $ewd_upcp_controller->settings->get_setting( 'details-description-characters' ) ? $this->get_read_more() : '' );
	}

	/**
	 * Gets the 'Read More' text linking to a product's details
	 * @since 5.0.0
	 */
	public function get_read_more() {

		$template = $this->find_template( 'catalog-product-read-more' );

		ob_start();
		
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get the initial submit product css classes
	 * @since 5.0.0
	 */
	public function get_classes( $classes = array() ) {
		global $ewd_upcp_controller;

		$classes = array_merge(
			$classes,
			array(
				'ewd-upcp-catalog-product-div',
				'ewd-upcp-catalog-product-' . $this->view			
			)
		);

		return apply_filters( 'ewd_upcp_product_classes', $classes, $this );
	}


	/**
	 * Set any neccessary variables when the Product is created
	 * @since 5.0.0
	 */
	public function set_variables() {
		global $ewd_upcp_controller;

		$this->style = $ewd_upcp_controller->settings->get_setting( 'styling-catalog-skin' );
	}
}