<?php
// HTML template for User Sorting widget on Store Exporter screen
function woo_ce_user_sorting() {

	$orderby = woo_ce_get_option( 'user_orderby', 'ID' );
	$order = woo_ce_get_option( 'user_order', 'ASC' );

	ob_start(); ?>
<p><label><?php _e( 'User Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="user_orderby">
		<option value="ID"<?php selected( 'ID', $orderby ); ?>><?php _e( 'User ID', 'woocommerce-exporter' ); ?></option>
		<option value="display_name"<?php selected( 'display_name', $orderby ); ?>><?php _e( 'Display Name', 'woocommerce-exporter' ); ?></option>
		<option value="user_name"<?php selected( 'user_name', $orderby ); ?>><?php _e( 'Name', 'woocommerce-exporter' ); ?></option>
		<option value="user_login"<?php selected( 'user_login', $orderby ); ?>><?php _e( 'Username', 'woocommerce-exporter' ); ?></option>
		<option value="nicename"<?php selected( 'nicename', $orderby ); ?>><?php _e( 'Nickname', 'woocommerce-exporter' ); ?></option>
		<option value="email"<?php selected( 'email', $orderby ); ?>><?php _e( 'E-mail', 'woocommerce-exporter' ); ?></option>
		<option value="url"<?php selected( 'url', $orderby ); ?>><?php _e( 'Website', 'woocommerce-exporter' ); ?></option>
		<option value="registered"<?php selected( 'registered', $orderby ); ?>><?php _e( 'Date Registered', 'woocommerce-exporter' ); ?></option>
		<option value="rand"<?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="user_order">
		<option value="ASC"<?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Users within the exported file. By default this is set to export User by User ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}

// HTML template for disabled Custom Users widget on Store Exporter screen
function woo_ce_users_custom_fields() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$custom_users = ' - ';

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<form method="post" id="export-users-custom-fields" class="export-options user-options">
	<div id="poststuff">

		<div class="postbox" id="export-options user-options">
			<h3 class="hndle"><?php _e( 'Custom User Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom User meta in the Export Users table above fill the Users text box then click Save Custom Fields.', 'woocommerce-exporter' ); ?></p>
				<p class="description"><?php printf( __( 'For more information on exporting custom User meta consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'User meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_users" rows="5" cols="70"><?php echo esc_textarea( $custom_users ); ?></textarea>
							<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
							<p class="description"><?php _e( 'Include additional custom User meta in your export file by adding each custom User meta name to a new line above.<br />For example: <code>Customer UA, Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

				</table>
				<p class="submit">
					<input type="button" class="button button-disabled" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" />
				</p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-users-custom-fields -->
<?php
	ob_end_flush();

}