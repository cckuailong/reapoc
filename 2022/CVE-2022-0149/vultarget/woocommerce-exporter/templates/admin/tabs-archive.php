<ul class="subsubsub">
	<li><a href="<?php echo esc_url( add_query_arg( 'filter', null ) ); ?>"<?php woo_ce_archives_quicklink_current( 'all' ); ?>><?php _e( 'All', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count(); ?>)</span></a> |</li>
	<li><a href="<?php echo esc_url( add_query_arg( 'filter', 'product' ) ); ?>"<?php woo_ce_archives_quicklink_current( 'product' ); ?>><?php _e( 'Products', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'product' ); ?>)</span></a> |</li>
	<li><a href="<?php echo esc_url( add_query_arg( 'filter', 'category' ) ); ?>"<?php woo_ce_archives_quicklink_current( 'category' ); ?>><?php _e( 'Categories', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'category' ); ?>)</span></a> |</li>
	<li><a href="<?php echo esc_url( add_query_arg( 'filter', 'tag' ) ); ?>"<?php woo_ce_archives_quicklink_current( 'tag' ); ?>><?php _e( 'Tags', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'tag' ); ?>)</span></a> |</li>
	<li><?php _e( 'Brands', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'brand' ); ?>)</span> |</li>
	<li><?php _e( 'Orders', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'order' ); ?>)</span> |</li>
	<li><?php _e( 'Customers', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'customer' ); ?>)</span> |</li>
	<li><a href="<?php echo esc_url( add_query_arg( 'filter', 'user' ) ); ?>"<?php woo_ce_archives_quicklink_current( 'user' ); ?>><?php _e( 'Users', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'user' ); ?>)</span></a> |</li>
	<li><?php _e( 'Coupon', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'coupon' ); ?>)</span> |</li>
	<li><?php _e( 'Subscriptions', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'subscription' ); ?>)</span> |</li>
	<li><?php _e( 'Product Vendors', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'product_vendor' ); ?>)</span> |</li>
	<li><?php _e( 'Shipping Classes', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'shipping_class' ); ?>)</span></li>
	<!-- <li><?php _e( 'Attributes', 'woocommerce-exporter' ); ?> <span class="count">(<?php echo woo_ce_archives_quicklink_count( 'attribute' ); ?>)</span></li> -->
</ul>
<!-- .subsubsub -->
<br class="clear" />
<form action="" method="GET">
	<table class="widefat fixed media" cellspacing="0">
		<thead>

			<tr>
				<th scope="col" id="icon" class="manage-column column-icon"></th>
				<th scope="col" id="title" class="manage-column column-title"><?php _e( 'Filename', 'woocommerce-exporter' ); ?></th>
				<th scope="col" class="manage-column column-type"><?php _e( 'Type', 'woocommerce-exporter' ); ?></th>
				<th scope="col" class="manage-column column-author"><?php _e( 'Author', 'woocommerce-exporter' ); ?></th>
				<th scope="col" id="title" class="manage-column column-title"><?php _e( 'Date', 'woocommerce-exporter' ); ?></th>
			</tr>

		</thead>
		<tfoot>

			<tr>
				<th scope="col" class="manage-column column-icon"></th>
				<th scope="col" class="manage-column column-title"><?php _e( 'Filename', 'woocommerce-exporter' ); ?></th>
				<th scope="col" class="manage-column column-type"><?php _e( 'Type', 'woocommerce-exporter' ); ?></th>
				<th scope="col" class="manage-column column-author"><?php _e( 'Author', 'woocommerce-exporter' ); ?></th>
				<th scope="col" class="manage-column column-title"><?php _e( 'Date', 'woocommerce-exporter' ); ?></th>
			</tr>

		</tfoot>
		<tbody id="the-list">

<?php if( $files ) { ?>
	<?php foreach( $files as $file ) { ?>
			<tr id="post-<?php echo $file->ID; ?>" class="author-self status-<?php echo $file->post_status; ?>" valign="top">
				<td class="column-icon media-icon">
					<?php echo $file->media_icon; ?>
				</td>
				<td class="post-title page-title column-title">
					<strong><a href="<?php echo $file->guid; ?>" class="row-title"><?php echo $file->post_title; ?></a></strong>
					<div class="row-actions">
						<span class="view"><a href="<?php echo get_edit_post_link( $file->ID ); ?>" title="<?php _e( 'Edit', 'woocommerce-exporter' ); ?>"><?php _e( 'Edit', 'woocommerce-exporter' ); ?></a></span> | 
						<span class="trash"><a href="<?php echo get_delete_post_link( $file->ID, '', true ); ?>" title="<?php _e( 'Delete Permanently', 'woocommerce-exporter' ); ?>"><?php _e( 'Delete', 'woocommerce-exporter' ); ?></a></span>
					</div>
				</td>
				<td class="title">
					<a href="<?php echo esc_url( add_query_arg( 'filter', $file->export_type ) ); ?>"><?php echo $file->export_type_label; ?></a>
				</td>
				<td class="author column-author"><?php echo $file->post_author_name; ?></td>
				<td class="date column-date"><?php echo $file->post_date; ?></td>
			</tr>
	<?php } ?>
<?php } else { ?>
			<tr id="post-<?php echo $file->ID; ?>" class="author-self" valign="top">
				<td colspan="3" class="colspanchange"><?php _e( 'No past exports were found.', 'woocommerce-exporter' ); ?></td>
			</tr>
<?php } ?>

		</tbody>
	</table>
	<div class="tablenav bottom">
		<div class="tablenav-pages one-page">
			<span class="displaying-num"><?php printf( __( '%d items', 'woocommerce-exporter' ), woo_ce_archives_quicklink_count() ); ?></span>
		</div>
		<!-- .tablenav-pages -->
		<br class="clear">
	</div>
	<!-- .tablenav -->
</form>