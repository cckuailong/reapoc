<?php

/**
 * Class to display a single product, gets extended depending on whether the product is being displayed
 * on the catalog page or as a single product page
 *
 * @since 5.0.0
 */
class ewdupcpViewProduct extends ewdupcpView {

	/**
	 * Returns any custom fields that have been created
	 *
	 * @since 5.0.0
	 */
	public function get_custom_fields() {
		global $ewd_upcp_controller;

		return $ewd_upcp_controller->settings->get_custom_fields();
	}

	/**
	 * Returns the value for a given custom field
	 *
	 * @since 5.0.0
	 */
  	public function get_custom_field_value( $custom_field ) {

  		$value = get_post_meta( $this->product->ID, 'custom_field_' . $custom_field->id, true );

  		if ( $custom_field->type == 'file' ) {

  			return ! empty( $value ) ? '<a href="' . esc_attr( $value ) . '">' . strrpos( $value, '/' ) ? esc_html( substr( $value, strrpos( $value, '/' ) + 1 ) ) : esc_html( $value ) . '</a>' : '';
  		} 
		elseif ( $custom_field->type == 'link' ) {

			return ! empty( $value ) ? '<a href="' . esc_attr( $value ) . '" target="_blank">' . esc_attr( $value ) . '</a>' : '';
		} 
		else { return $value; }
	}

  	/**
	 * Sets the required catalog links for a product
	 *
	 * @since 5.0.0
	 */
  	public function set_catalog_links() {
  		global $ewd_upcp_controller;
  		
  		$this->catalog_url = ! empty( $this->catalog_url ) ? $this->catalog_url : ( ! empty( $this->catalogue_url ) ? $this->catalogue_url : get_permalink() );

  		$permalink_base = $ewd_upcp_controller->settings->get_setting( 'permalink-base' );
  		
  		if ( ! empty( $this->product->link ) ) { $this->details_link = $this->product->link; }
  		elseif ( ! empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-product-page' ) ) and ! empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-sync' ) ) ) { $this->details_link = add_query_arg( 'ewd_upcp_catalog_url', $this->catalog_url, get_permalink( $this->product->woocommerce_id ) ); }
  		elseif ( $ewd_upcp_controller->settings->get_setting( 'pretty-permalinks' ) ) { $this->details_link = $this->catalog_url . $permalink_base . '/' . $this->product->slug . '/'; }
  		else { $this->details_link = add_query_arg( 'singleproduct', $this->product->id, $this->catalog_url ); }
  	}

}
