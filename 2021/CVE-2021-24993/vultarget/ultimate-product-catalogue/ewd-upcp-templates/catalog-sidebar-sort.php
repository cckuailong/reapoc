<div class='ewd-upcp-catalog-sidebar-sort'>

	<span>
		<?php echo esc_html( $this->get_label( 'label-sort-by' ) ); ?>
	</span>

	<div>

		<select name='ewd-upcp-sort-by'>
			
			<option value=''></option>

			<?php if ( in_array( 'price', $this->get_option( 'product-sort' ) ) ) { ?>

				<option value='price_asc'><?php echo esc_html( $this->get_label( 'label-price-ascending' ) ); ?></option>
				<option value='price_desc'><?php echo esc_html( $this->get_label( 'label-price-descending' ) ); ?></option>

			<?php } ?>

			<?php if ( in_array( 'name', $this->get_option( 'product-sort' ) ) ) { ?>

				<option value='name_asc'><?php echo esc_html( $this->get_label( 'label-name-ascending' ) ); ?></option>
				<option value='name_desc'><?php echo esc_html( $this->get_label( 'label-name-descending' ) ); ?></option>
				
			<?php } ?>

			<?php if ( in_array( 'rating', $this->get_option( 'product-sort' ) ) ) { ?>

				<option value='rating_asc'><?php echo esc_html( $this->get_label( 'label-rating-ascending' ) ); ?></option>
				<option value='rating_desc'><?php echo esc_html( $this->get_label( 'label-rating-descending' ) ); ?></option>
				
			<?php } ?>

			<?php if ( in_array( 'date', $this->get_option( 'product-sort' ) ) ) { ?>

				<option value='date_asc'><?php echo esc_html( $this->get_label( 'label-date-ascending' ) ); ?></option>
				<option value='date_desc'><?php echo esc_html( $this->get_label( 'label-date-descending' ) ); ?></option>
				
			<?php } ?>

		</select>
	
	</div>

</div>