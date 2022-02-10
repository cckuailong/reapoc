<div class='ewd-upcp-catalog-cart <?php echo ( empty( $this->cart_products ) ? 'ewd-upcp-hidden' : '' ); ?>'>

	<div class='ewd-upcp-cart-item-count-div <?php echo ( $this->get_option( 'woocommerce-disable-cart-count' ) ? 'ewd-upcp-hidden' : '' ); ?>'>
		<?php echo sprintf( esc_html( $this->get_label( 'label-cart-items' ) ), '<span class=\'ewd-upcp-cart-item-count\'>' . sizeOf( $this->cart_products ) . '</span>' ); ?>
	</div>
	
	<form id='ewd-upcp-cart-form' method='post' action='<?php echo esc_attr( $this->get_cart_action_url() ); ?>'>
		<input type='hidden' name='return_url' value='<?php echo get_permalink(); ?>' />
		<input type='submit' name='ewd_upcp_submit_cart' value='<?php echo esc_attr( $this->get_cart_submit_label() ); ?>' />
	</form>

	<span class='ewd-upcp-clear-cart'><?php echo esc_html( $this->get_label( 'label-empty-cart' ) ); ?></span>

</div>