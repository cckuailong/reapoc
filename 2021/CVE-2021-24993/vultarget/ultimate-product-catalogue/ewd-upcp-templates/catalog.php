<div <?php echo ewd_format_classes( $this->classes ); ?>>

	<?php $this->maybe_print_cart_form(); ?>

	<?php $this->maybe_print_product_comparison_form(); ?>

	<?php $this->maybe_print_lightbox(); ?>

	<?php $this->print_shortcode_attributes(); ?>

	<?php $this->maybe_print_catalog_information(); ?>

	<?php $this->print_catalog_header(); ?>

	<?php $this->maybe_print_pagination( 'top' ); ?>

	<?php $this->maybe_print_sidebar(); ?>

	<div class='ewd-upcp-catalog-display'>

		<?php $this->maybe_print_thumbnail_display(); ?>

		<?php $this->maybe_print_list_display(); ?>

		<?php $this->maybe_print_detail_display(); ?>

	</div>

	<?php $this->maybe_print_pagination( 'bottom' ); ?>

</div>