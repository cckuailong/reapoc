<div class='ewd-upcp-pagination' data-max_pages='<?php echo $this->max_pages; ?>'>

	<span class='displaying-num'>

		<span class='product-count'><?php echo $this->product_count; ?></span>

		<?php echo esc_html( $this->get_label( 'label-products-pagination' ) ); ?>

	</span>
	
	<span class='pagination-links'>
	
		<a class='first-page <?php echo ( $this->current_page == 1 ? 'disabled' : '' ); ?>' title='Go to the first page'>&#171;</a>
		<a class='prev-page <?php echo ( $this->current_page == 1 ? 'disabled' : '' ); ?>' title='Go to the previous page'>&#8249;</a>

		<span class='paging-input'>

			<?php echo esc_html( $this->get_label( 'label-page' ) ); ?> 

			<span class='current-page'><?php echo $this->current_page; ?></span> 

			<?php echo esc_html( $this->get_label( 'label-pagination-of' ) ); ?> 

			<span class='total-pages'><?php echo $this->max_pages; ?></span>

		</span>

		<a class='next-page <?php echo ( $this->current_page == $this->max_pages ? 'disabled' : '' ); ?>' title='Go to the next page'>&#8250;</a>
		<a class='last-page <?php echo ( $this->current_page == $this->max_pages ? 'disabled' : '' ); ?>' title='Go to the last page'>&#187;</a>
	
	</span>

</div>