<div class='ewd-upcp-catalog-sidebar-price-filter'>

	<span>
		<?php echo esc_html( $this->get_label( 'label-price-filter' ) ); ?>
	</span>

	<div id='ewd-upcp-price-filter'></div>

	<div id='ewd-upcp-price-range'>
		
		<span>
			
			<?php $this->maybe_print_currency_symbol( 'before' ); ?>

			<input type='text' value='<?php echo $this->sidebar_min_price; ?>' name='ewd-upcp-price-slider-min' <?php echo ( $this->get_option( 'disable-slider-filter-text-inputs' ) ? 'disabled' : '' ); ?> data-min_price='<?php echo $this->sidebar_min_price; ?>' />

			<?php $this->maybe_print_currency_symbol( 'after' ); ?>

		</span>

		<span class='ewd-upcp-price-slider-divider'> - </span>

		<span>
			
			<?php $this->maybe_print_currency_symbol( 'before' ); ?>

			<input type='text' value='<?php echo $this->sidebar_max_price; ?>' name='ewd-upcp-price-slider-max' <?php echo ( $this->get_option( 'disable-slider-filter-text-inputs' ) ? 'disabled' : '' ); ?> data-max_price='<?php echo $this->sidebar_max_price; ?>' />

			<?php $this->maybe_print_currency_symbol( 'after' ); ?>

		</span>

	</div>

</div>