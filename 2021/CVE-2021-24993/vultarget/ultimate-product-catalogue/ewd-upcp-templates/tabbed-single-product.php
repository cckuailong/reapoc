<div id='ewd-upcp-single-product-<?php echo $this->product->id; ?>' class='ewd-upcp-tabbed-product-page ewd-upcp-product-page'>

	<?php $this->print_product_breadcrumbs(); ?>

	<div class='ewd-upcp-single-product-images-div'>

		<?php $this->print_main_image(); ?>

		<div class='ewd-upcp-hidden'>

			<?php $this->maybe_print_videos(); ?>

		</div>

		<?php $this->maybe_print_previous_thumbnails_button(); ?>

		<?php $this->print_additional_images(); ?>

		<?php $this->maybe_print_next_thumbnails_button(); ?>

	</div>

	<div class='ewd-upcp-single-product-details'>

		<div class='ewd-upcp-single-product-details-title-and-price'>

			<?php $this->print_title(); ?>
		
			<?php $this->maybe_print_price(); ?>

			<?php $this->print_social_media_buttons(); ?>
		
		</div>

		<div class='ewd-upcp-single-product-tabs-container'>

			<div class='ewd-upcp-single-product-tabs-menu'>

				<?php $this->print_tabs_menu(); ?>

			</div>

			<div class='ewd-upcp-single-product-tab <?php echo ( ! $this->is_starting_tab( 'details' ) ? 'ewd-upcp-hidden' : '' ); ?>' data-tab='details'>

				<?php $this->print_product_description(); ?>

			</div>

			<div class='ewd-upcp-single-product-tab <?php echo ( ! $this->is_starting_tab( 'additional_information' ) ? 'ewd-upcp-hidden' : '' ); ?>' data-tab='additional_information'>

				<?php $this->print_extra_description_elements(); ?>

			</div>

			<?php $this->maybe_print_inquiry_form_tab(); ?>

			<?php $this->maybe_print_reviews_tab(); ?>

			<?php $this->maybe_print_faqs_tab(); ?>

			<?php $this->print_custom_tabs(); ?>
				
		</div>

	</div>

	<div class='ewd-upcp-single-product-bottom-div'>

		<?php $this->maybe_print_related_products(); ?>

		<?php $this->maybe_print_next_previous_products(); ?>

	</div> 

</div>