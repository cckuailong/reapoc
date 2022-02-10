<?php

/**
 * Class to display a catalog on the front end.
 *
 * @since 5.0.0
 */
class ewdupcpViewMinimalProducts extends ewdupcpView {

	// Array holding all view items to be displayed
	public $items = array();

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 5.0.0
	 */
	public function render() {
		global $ewd_upcp_controller;

		$this->set_items();

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Add css classes to the slider
		$this->classes = $this->get_classes();

		ob_start();
		
		$this->add_custom_styling();

		$template = $this->find_template( 'minimal-products' );
		
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'ewd_upcp_minimal_products_output', $output, $this );
	}

	/**
	 * Loop through requested items, printing them out
	 *
	 * @since 5.0.0
	 */
	public function print_items() {
		
		foreach ( $this->items as $count => $item ) {

			if ( $count >= $this->product_count ) { break; }

			echo $item->render_view( 'minimal' );
		}
	}

	/**
	 * Sets the items in this minimal products listing
	 *
	 * @since 5.0.0
	 */
	public function set_items() {

		$product_ids = array();
		$category_ids = array();

		if ( ! empty( $this->product_ids ) ) {

			$product_ids = array_merge( $product_ids, explode( ',', $this->product_ids ) );
		}

		// overwrite the default product_count attribute if specific product_ids are provided
		$this->product_count = ! empty( $product_ids ) ? max( $this->product_count, sizeof( $product_ids ) ) : $this->product_count;

		if ( ! empty( $this->catalogue_id ) ) {

			if ( get_post_type( $this->catalogue_id ) != EWD_UPCP_CATALOG_POST_TYPE ) {

				$args = array(
					'post_type'		=> EWD_UPCP_CATALOG_POST_TYPE,
					'meta_query' 	=> array(
						array(
							'key'		=> 'old_catalog_id',
							'value'		=> $this->catalogue_id
						)
					)
				);

				$posts = get_posts( $args );

				$this->catalogue_id = ! empty( $posts ) ? reset( $posts )->ID : null;
			}

			$catalog_product_ids = array();
			$catalog_category_ids = array();

			$catalog_items = get_post_meta( intval( $this->catalogue_id ), 'items', true );

			foreach ( $catalog_items as $catalog_item ) {

				if ( $catalog_item->type == 'product' ) { $catalog_product_ids[] = $catalog_item->id; }
				if ( $catalog_item->type == 'category' ) { $catalog_category_ids[] = $catalog_item->id; }
			}

			$product_ids = array_merge( $product_ids, $catalog_product_ids );
			$category_ids = array_merge( $category_ids, $catalog_category_ids );
		}

		if ( ! empty( $this->category_id ) ) {

			$category_ids = array_merge( $category_ids, explode( ',', $this->category_id ) );
		}

		if ( ! empty( $this->subcategory_id ) ) {

			$category_ids = array_merge( $category_ids, explode( ',', $this->subcategory_id ) );
		}

		$args = array(
			'posts_per_page'	=> $this->product_count,
			'post_type'			=> EWD_UPCP_PRODUCT_POST_TYPE
		);

		if ( ! empty( $product_ids ) ) { 

			$post_args = array_merge( $args, array( 'post__in' => $product_ids ) ); 

			$products = get_posts( $post_args );
		}
		else {

			$products = array();
		}

		if ( ! empty( $category_ids ) ) { 

			$tax_query = array(
				array(
					'taxonomy'	=> EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY,
					'field'		=> 'term_id',
					'terms'		=> $category_ids
				)
			);

			$post_args = array_merge( $args, array( 'tax_query' => $tax_query ) ); 

			$category_products = get_posts( $post_args );
		}
		else {

			$category_products = array();
		}
		
		$product_posts = array_merge( $products, $category_products );

		foreach ( $product_posts as $product_post ) {

			$product = new ewdupcpProduct();

			$product->load_post( $product_post );

			$args = array(
				'product'		=> $product,
				'catalogue_url'	=> $this->catalogue_url
			);

			$this->items[] = new ewdupcpViewCatalogProduct( $args );
		}
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
				'ewd-upcp-minimal-products-div',
				'ewd-upcp-minimal-products-' . $this->products_wide
			)
		);

		return apply_filters( 'ewd_upcp_catalog_classes', $classes, $this );
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 5.0.0
	 */
	public function enqueue_assets() {
		
		wp_enqueue_style( 'ewd-upcp-css' );

		wp_enqueue_script( 'ewd-upcp-js' );

	}
}
