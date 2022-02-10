<div class='ewd-upcp-catalog-inquiry-form'>

	<h4>
		<?php echo esc_html( $this->get_label( 'label-product-inquiry-form-title' ) ); ?>
	</h4>

	<div class='ewd-upcp-inquiry-form-explanation'>

		<div class='ewd-upcp-inquiry-form-back-link'>

			<a href='<?php echo ( ! empty( $_POST['return_URL'] ) ? esc_attr( esc_url_raw( $_POST['return_URL'] ) ) : '' ); ?>' >
				<?php _e( 'Back to Catalog', 'ultimate-product-catalogue' ); ?>
			</a>

		</div>

	</div>

	<?php echo $this->get_inquiry_form(); ?>

</div>