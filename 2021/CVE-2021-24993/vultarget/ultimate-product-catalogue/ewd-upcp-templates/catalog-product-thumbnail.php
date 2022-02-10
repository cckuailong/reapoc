<div id='ewd-upcp-catalog-product-thumbnail-<?php echo esc_attr( $this->product->id ); ?>' <?php echo ewd_format_classes( $this->classes ); ?> data-product_id='<?php echo esc_attr( $this->product->id ); ?>'>
		
	<div class='ewd-upcp-catalog-product-thumbnail-image-div'>

		<?php $this->maybe_print_cart_action_button( 'top' ); ?>

		<?php $this->maybe_print_product_comparison_button(); ?>

		<?php $this->maybe_print_sale_flag(); ?>

		<?php $this->print_image(); ?>

	</div>

	<div class='ewd-upcp-catalog-product-thumbnail-body-div'>

		<?php $this->maybe_print_rating(); ?>

		<?php $this->print_title(); ?>

		<?php $this->print_product_price(); ?>

		<?php $this->maybe_print_categories(); ?>

		<?php $this->maybe_print_subcategories(); ?>

		<?php $this->maybe_print_tags(); ?>

		<?php $this->maybe_print_custom_fields(); ?>
		
	</div>

	<?php $this->maybe_print_cart_action_button( 'bottom' ); ?>

</div>