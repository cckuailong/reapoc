<?php $average_rating = $this->product->get_average_product_rating(); ?>

<?php if ( empty( $average_rating ) ) { return; } ?>

<span class='ewd-upcp-urp-review-score' title='<?php _e( 'Average Rating: ', 'ultimate-product-catalogue' ); ?> <?php echo $average_rating; ?>'>

	<?php for ( $i = 1; $i <= 5; $i++ ) { ?>

		<?php if ( $i <= $average_rating + 0.25 ) { ?>

			<span class='dashicons dashicons-star-filled'></span>

		<?php } elseif ( $i <= $average_rating + 0.75 ) { ?>
			
			<span class='dashicons dashicons-star-half'></span>

		<?php } else { ?>

			<span class='dashicons dashicons-star-empty'></span>

		<?php } ?>

	<?php } ?>

</span>