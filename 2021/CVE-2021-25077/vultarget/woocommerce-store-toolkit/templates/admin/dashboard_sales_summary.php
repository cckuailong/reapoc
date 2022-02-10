<div class="woocommerce_sales_summary">
	<div class="table table_content table_top">
		<p><strong><?php _e( 'Sales Today', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_today ); ?> <span<?php echo woo_st_percentage_symbol_class( $sales_today, $sales_yesterday ); ?>><?php echo woo_st_return_percentage( $sales_today, $sales_yesterday ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion table_top">
		<p><strong><?php _e( 'Sales Yesterday', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_yesterday ); ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

	<div class="table table_content">
		<p><strong><?php _e( 'Sales This Week', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_week ); ?> <span<?php echo woo_st_percentage_symbol_class( $sales_week, $sales_last_week ); ?>><?php echo woo_st_return_percentage( $sales_week, $sales_last_week ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion">
		<p><strong><?php _e( 'Sales Last Week', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_last_week ); ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

	<div class="table table_content">
		<p><strong><?php _e( 'Sales This Month', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_month ); ?> <span<?php echo woo_st_percentage_symbol_class( $sales_month, $sales_last_month ); ?>><?php echo woo_st_return_percentage( $sales_month, $sales_last_month ); ?>%</span></p>
	</div>
	<!-- .table -->
	<div class="table table_discussion">
		<p><strong><?php _e( 'Sales Last Month', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_last_month ); ?></p>
	</div>
	<!-- .table -->
	<br class="clear" />

	<div class="table table_content">
		<p><strong><?php _e( 'Sales All Time', 'woocommerce-store-toolkit' ); ?></strong></p>
		<p class="price"><?php echo wc_price( $sales_all_time ); ?></p>
	</div>
	<!-- .table -->

</div>
<!-- #jigoshop_sales_summary -->