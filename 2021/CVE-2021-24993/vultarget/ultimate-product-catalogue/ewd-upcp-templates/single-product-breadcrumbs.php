<div class='ewd-upcp-single-product-breadcrumbs'>
	
	<?php if ( in_array( 'catalog', $this->get_option( 'breadcrumbs' ) ) ) { ?>

		<span class='ewd-upcp-single-product-breadcrumb-link'>

			<a href='<?php echo esc_attr( $this->catalog_url ); ?>'>
				<?php echo esc_html( $this->get_label( 'label-back-to-catalog' ) ); ?>
			</a>

		</span>

	<?php } ?>

	<?php if ( in_array( 'categories', $this->get_option( 'breadcrumbs' ) ) and ! empty( $this->product->category_ids ) ) { ?>

		<span class='ewd-upcp-single-product-breadcrumb-link'>

			<a href='<?php echo esc_attr( add_query_arg( 'categories', reset( $this->product->category_ids ), $this->catalog_url ) ); ?>'>
				<?php echo esc_html( reset( $this->product->categories )->name ); ?>
			</a>

		</span>

	<?php } ?>

	<?php if ( in_array( 'subcategories', $this->get_option( 'breadcrumbs' ) ) and ! empty( $this->product->subcategory_ids ) ) { ?>

		<span class='ewd-upcp-single-product-breadcrumb-link'>

			<a href='<?php echo esc_attr( add_query_arg( 'subcategories', reset( $this->product->subcategory_ids ), $this->catalog_url ) ); ?>'>
				<?php echo esc_html( reset( $this->product->subcategories )->name ); ?>
			</a>

		</span>

	<?php } ?>

</div>