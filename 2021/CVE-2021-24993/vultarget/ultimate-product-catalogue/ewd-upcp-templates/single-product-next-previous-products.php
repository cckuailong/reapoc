<div class='ewd-upcp-next-previous-products'>

	<?php if ( ! empty( $this->product->next_product ) ) { ?>

		<div class='ewd-upcp-next-product'>

			<div class='ewd-upcp-next-previous-title'>
				<?php echo esc_html( $this->get_label( 'label-next-product' ) ); ?>
			</div>

			<?php echo $this->render_minimal_product( $this->product->next_product ); ?>

		</div>

	<?php } ?>

	<?php if ( ! empty( $this->product->previous_product ) ) { ?>

		<div class='ewd-upcp-previous-product'>

			<div class='ewd-upcp-next-previous-title'>
				<?php echo esc_html( $this->get_label( 'label-previous-product' ) ); ?>
			</div>

			<?php echo $this->render_minimal_product( $this->product->previous_product ); ?>

		</div>

	<?php } ?>

</div>