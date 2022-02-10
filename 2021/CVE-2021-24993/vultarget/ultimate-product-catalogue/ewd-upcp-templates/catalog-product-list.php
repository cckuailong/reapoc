<div id='ewd-upcp-catalog-product-list-<?php echo esc_attr( $this->product->id ); ?>' <?php echo ewd_format_classes( $this->classes ); ?> data-product_id='<?php echo esc_attr( $this->product->id ); ?>'>
		
	<?php $this->maybe_print_rating(); ?>

	<?php $this->print_title(); ?>

	<?php $this->print_product_price(); ?>

	<div class='ewd-upcp-catalog-product-list-content ewd-upcp-hidden'>

		<div class='ewd-upcp-catalog-product-list-image-div'>

			<?php $this->maybe_print_cart_action_button( 'top' ); ?>

			<?php $this->maybe_print_product_comparison_button(); ?>

			<?php $this->maybe_print_sale_flag(); ?>

			<?php $this->print_image(); ?>

		</div>

		<?php $this->print_content(); ?>

		<?php $this->maybe_print_categories(); ?>

		<?php $this->maybe_print_subcategories(); ?>

		<?php $this->maybe_print_tags(); ?>

		<?php $this->maybe_print_custom_fields(); ?>

		<?php $this->maybe_print_cart_action_button( 'bottom' ); ?>

	</div>

</div>