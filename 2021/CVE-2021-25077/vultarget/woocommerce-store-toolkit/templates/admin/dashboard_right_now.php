<div id="woocommerce_right_now" class="woocommerce_right_now">
	<div class="table table_content">
		<p class="sub"><?php _e( 'Shop Content', 'woocommerce-store-toolkit' ); ?></p>
		<table>
			<tbody>
				<tr class="first">
					<td class="first b">
						<a href="<?php echo add_query_arg( 'post_type', 'product', 'edit.php' ); ?>">
<?php
$post_type = 'product';
$num_posts = wp_count_posts( $post_type );
if( !empty( $num_posts ) && !is_wp_error( $num_posts ) ) {
	$num = ( isset( $num_posts->publish ) ? number_format_i18n( $num_posts->publish ) : '-' );
	echo $num;
} else if( is_wp_error( $num_posts ) ) {
	error_log( sprintf( '[store-toolkit] Warning: Deprecation warning running wp_count_posts(): %s', $num_posts->get_error_message() ) );
}
?>
					</a></td>
					<td class="t">
						<a href="<?php echo add_query_arg( 'post_type', 'product', 'edit.php' ); ?>"><?php _e( 'Products', 'woocommerce-store-toolkit' ); ?></a>
					</td>
				</tr>
				<tr>
					<td class="first b">
						<a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_cat', 'post_type' => 'product' ), 'edit-tags.php' ); ?>">
<?php
$term_taxonomy = 'product_cat';
$num_terms = wp_count_terms( $term_taxonomy );
if( !empty( $num_terms ) && !is_wp_error( $num_terms ) ) {
	echo $num_terms;
} else if( is_wp_error( $num_terms ) ) {
	error_log( sprintf( '[store-toolkit] Warning: Deprecation warning running wp_count_terms(): %s', $num_terms->get_error_message() ) );
}
?>
						</a>
					</td>
					<td class="t">
						<a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_cat', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php _e( 'Product Categories', 'woocommerce-store-toolkit' ); ?></a>
					</td>
				</tr>
				<tr>
					<td class="first b">
						<a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_tag', 'post_type' => 'product' ), 'edit-tags.php' ); ?>">
<?php
$term_taxonomy = 'product_tag';
$num_terms = wp_count_terms( $term_taxonomy );
if( !empty( $num_terms ) && !is_wp_error( $num_terms ) ) {
	echo $num_terms;
} else if( is_wp_error( $num_terms ) ) {
	error_log( sprintf( '[store-toolkit] Warning: Deprecation warning running wp_count_terms(): %s', $num_terms->get_error_message() ) );
}
?>
						</a>
					</td>
					<td class="t">
						<a href="<?php echo add_query_arg( array( 'taxonomy' => 'product_tag', 'post_type' => 'product' ), 'edit-tags.php' ); ?>"><?php _e( 'Product Tags', 'woocommerce-store-toolkit' ); ?></a>
					</td>
				</tr>
				<tr>
					<td class="first b">
						<a href="<?php echo add_query_arg( array( 'post_type' => 'product', 'page' => 'product_attributes' ), 'edit.php' ); ?>">
<?php
$num_terms = '~';
echo $num_terms;
?>
						</a>
					</td>
					<td class="t">
						<a href="<?php echo add_query_arg( array( 'post_type' => 'product', 'page' => 'product_attributes' ), 'edit.php' ); ?>"><?php _e( 'Attributes', 'woocommerce-store-toolkit' ); ?></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- .table -->

	<div class="table table_discussion">
		<p class="sub"><?php _e( 'Orders', 'woocommerce-store-toolkit' ); ?></p>
		<table>
			<tbody>
				<tr class="first">
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'pending' ), 'edit.php' ); ?>"><span class="total-count"><?php echo ( isset( $order_count['pending'] ) ? $order_count['pending'] : 0 ); ?></span></a></td>
					<td class="last t"><a class="pending" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'pending' ), 'edit.php' ); ?>"><?php _e( 'Pending', 'woocommerce-store-toolkit' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'on-hold' ), 'edit.php' ); ?>"><span class="total-count"><?php echo ( isset( $order_count['onhold'] ) ? $order_count['onhold'] : 0 ); ?></span></a></td>
					<td class="last t"><a class="onhold" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'on-hold' ), 'edit.php' ); ?>"><?php _e( 'On-Hold', 'woocommerce-store-toolkit' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'processing' ), 'edit.php' ); ?>"><span class="total-count"><?php echo ( isset( $order_count['processing'] ) ? $order_count['processing'] : 0 ); ?></span></a></td>
					<td class="last t"><a class="processing" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'processing' ), 'edit.php' ); ?>"><?php _e( 'Processing', 'woocommerce-store-toolkit' ); ?></a></td>
				</tr>
				<tr>
					<td class="b"><a href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'completed' ), 'edit.php' ); ?>"><span class="total-count"><?php echo ( isset( $order_count['completed'] ) ? $order_count['completed'] : 0 ); ?></span></a></td>
					<td class="last t"><a class="complete" href="<?php echo add_query_arg( array( 'post_type' => 'shop_order', 'shop_order_status' => 'completed' ), 'edit.php' ); ?>"><?php _e( 'Completed', 'woocommerce-store-toolkit' ); ?></a></td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- .table -->
	<br class="clear"/>
	<div class="versions">
		<p id="wp-version-message"><?php _e( 'You are using', 'woocommerce-store-toolkit' ); ?>
			<strong>WooCommerce <?php echo get_option( 'woocommerce_version' ); ?></strong>
		</p>
	</div>
	<!-- .versions -->
	<br class="clear"/>
</div>
<!-- #woocommerce_right_now -->