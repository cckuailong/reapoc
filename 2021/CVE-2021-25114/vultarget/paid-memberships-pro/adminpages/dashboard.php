<?php
/**
 * The Memberships Dashboard admin page for Paid Memberships Pro
 * @since 2.0
 */

/**
 * Add all the meta boxes for the dashboard.
 */
add_meta_box(
	'pmpro_dashboard_welcome',
	__( 'Welcome to Paid Memberships Pro', 'paid-memberships-pro' ),
	'pmpro_dashboard_welcome_callback',
	'toplevel_page_pmpro-dashboard',
	'normal'
);
add_meta_box(
	'pmpro_dashboard_report_sales',
	__( 'Sales and Revenue', 'paid-memberships-pro' ),
	'pmpro_report_sales_widget',
	'toplevel_page_pmpro-dashboard',
	'advanced'
);
add_meta_box(
	'pmpro_dashboard_report_membership_stats',
	__( 'Membership Stats', 'paid-memberships-pro' ),
	'pmpro_report_memberships_widget',
	'toplevel_page_pmpro-dashboard',
	'advanced'
);
add_meta_box(
	'pmpro_dashboard_report_logins',
	__( 'Visits, Views, and Logins', 'paid-memberships-pro' ),
	'pmpro_report_login_widget',
	'toplevel_page_pmpro-dashboard',
	'advanced'
);
add_meta_box(
	'pmpro_dashboard_report_recent_members',
	__( 'Recent Members', 'paid-memberships-pro' ),
	'pmpro_dashboard_report_recent_members_callback',
	'toplevel_page_pmpro-dashboard',
	'side'
);
add_meta_box(
	'pmpro_dashboard_report_recent_orders',
	__( 'Recent Orders', 'paid-memberships-pro' ),
	'pmpro_dashboard_report_recent_orders_callback',
	'toplevel_page_pmpro-dashboard',
	'side'
);
add_meta_box(
	'pmpro_dashboard_news_updates',
	__( 'Paid Memberships Pro News and Updates', 'paid-memberships-pro' ),
	'pmpro_dashboard_news_updates_callback',
	'toplevel_page_pmpro-dashboard',
	'side'
);

/**
 * Load the Paid Memberships Pro dashboard-area header
 */
require_once( dirname( __FILE__ ) . '/admin_header.php' ); ?>

<form id="pmpro-dashboard-form" method="post" action="admin-post.php">

	<div class="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">

			<?php do_meta_boxes( 'toplevel_page_pmpro-dashboard', 'normal', '' ); ?>

			<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( 'toplevel_page_pmpro-dashboard', 'advanced', '' ); ?>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( 'toplevel_page_pmpro-dashboard', 'side', '' ); ?>
			</div>

        <br class="clear">

    	</div> <!-- end dashboard-widgets -->

		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>

	</div> <!-- end dashboard-widgets-wrap -->
</form>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
		postboxes.add_postbox_toggles('toplevel_page_pmpro-dashboard');
	});
	//]]>
</script>
<?php

/**
 * Callback function for pmpro_dashboard_welcome meta box.
 */
function pmpro_dashboard_welcome_callback() { ?>
	<div class="pmpro-dashboard-welcome-columns">
        <div class="pmpro-dashboard-welcome-column">
    		<?php global $pmpro_level_ready, $pmpro_gateway_ready, $pmpro_pages_ready; ?>
    		<h3><?php echo esc_attr_e( 'Initial Setup', 'paid-memberships-pro' ); ?></h3>
    		<ul>
    			<?php if ( current_user_can( 'pmpro_membershiplevels' ) ) { ?>
    				<li>
    					<?php if ( empty( $pmpro_level_ready ) ) { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-membershiplevels&edit=-1' );?>"><i class="dashicons dashicons-admin-users"></i> <?php echo esc_attr_e( 'Create a Membership Level', 'paid-memberships-pro' ); ?></a>
    					<?php } else { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-membershiplevels' );?>"><i class="dashicons dashicons-admin-users"></i> <?php echo esc_attr_e( 'View Membership Levels', 'paid-memberships-pro' ); ?></a>
    					<?php } ?>
    				</li>
    			<?php } ?>

    			<?php if ( current_user_can( 'pmpro_pagesettings' ) ) { ?>
    				<li>
    					<?php if ( empty( $pmpro_pages_ready ) ) { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-pagesettings' );?>"><i class="dashicons dashicons-welcome-add-page"></i> <?php echo esc_attr_e( 'Generate Membership Pages', 'paid-memberships-pro' ); ?></a>
    					<?php } else { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-pagesettings' );?>"><i class="dashicons dashicons-welcome-add-page"></i> <?php echo esc_attr_e( 'Manage Membership Pages', 'paid-memberships-pro' ); ?>
    					<?php } ?>
    				</li>
    			<?php } ?>

    			<?php if ( current_user_can( 'pmpro_pagesettings' ) ) { ?>
    				<li>
    					<?php if ( empty( $pmpro_gateway_ready ) ) { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-paymentsettings' );?>"><i class="dashicons dashicons-cart"></i> <?php echo esc_attr_e( 'Configure Payment Settings', 'paid-memberships-pro' ); ?></a>
    					<?php } else { ?>
    						<a href="<?php echo admin_url( 'admin.php?page=pmpro-paymentsettings' );?>"><i class="dashicons dashicons-cart"></i> <?php echo esc_attr_e( 'Configure Payment Settings', 'paid-memberships-pro' ); ?></a>
    					<?php } ?>
    				</li>
    			<?php } ?>
    		</ul>
    		<h3><?php echo esc_attr_e( 'Other Settings', 'paid-memberships-pro' ); ?></h3>
    		<ul>
    			<?php if ( current_user_can( 'pmpro_emailsettings' ) ) { ?>
    				<li><a href="<?php echo admin_url( 'admin.php?page=pmpro-emailsettings' );?>"><i class="dashicons dashicons-email"></i> <?php echo esc_attr_e( 'Confirm Email Settings', 'paid-memberships-pro' );?></a></li>
    			<?php } ?>

				<?php if ( current_user_can( 'pmpro_emailtemplates' ) ) { ?>
					<li><a href="<?php echo admin_url( 'admin.php?page=pmpro-emailtemplates' );?>"><i class="dashicons dashicons-editor-spellcheck"></i> <?php echo esc_attr_e( 'Customize Email Templates', 'paid-memberships-pro' );?></a></li>
				<?php } ?>

    			<?php if ( current_user_can( 'pmpro_advancedsettings' ) ) { ?>
    				<li><a href="<?php echo admin_url( 'admin.php?page=pmpro-advancedsettings' );?>"><i class="dashicons dashicons-admin-settings"></i> <?php echo esc_attr_e( 'View Advanced Settings', 'paid-memberships-pro' ); ?></a></li>
    			<?php } ?>

    			<?php if ( current_user_can( 'pmpro_addons' ) ) { ?>
    				<li><a href="<?php echo admin_url( 'admin.php?page=pmpro-addons' );?>"><i class="dashicons dashicons-admin-plugins"></i> <?php echo esc_attr_e( 'Explore Add Ons for Additional Features', 'paid-memberships-pro' ); ?></a></li>
    			<?php } ?>
    		</ul>
    		<hr />
    		<p class="text-center">
    			<?php echo esc_html( __( 'For guidance as your begin these steps,', 'paid-memberships-pro' ) ); ?>
    			<a href="https://www.paidmembershipspro.com/documentation/initial-plugin-setup/?utm_source=plugin&utm_medium=pmpro-dashboard&utm_campaign=documentation&utm_content=initial-plugin-setup" target="_blank"><?php echo esc_attr_e( 'view the Initial Setup Video and Docs.', 'paid-memberships-pro' ); ?></a>
    		</p>
    	</div> <!-- end pmpro-dashboard-welcome-column -->
    	<div class="pmpro-dashboard-welcome-column">
    		<h3><?php echo esc_attr_e( 'Support License', 'paid-memberships-pro' ); ?></h3>
    		<?php
    			// Get saved license.
    			$key = get_option( 'pmpro_license_key', '' );
    			$pmpro_license_check = get_option( 'pmpro_license_check', array( 'license' => false, 'enddate' => 0 ) );
    		?>
    		<?php if ( ! pmpro_license_isValid() && empty( $key ) ) { ?>
    			<p class="pmpro_message pmpro_error">
    				<strong><?php echo esc_html_e( 'No support license key found.', 'paid-memberships-pro' ); ?></strong><br />
    				<?php printf(__( '<a href="%s">Enter your key here &raquo;</a>', 'paid-memberships-pro' ), admin_url( 'admin.php?page=pmpro-license' ) );?>
    			</p>
    		<?php } elseif ( ! pmpro_license_isValid() ) { ?>
    			<p class="pmpro_message pmpro_alert">
    				<strong><?php echo esc_html_e( 'Your license is invalid or expired.', 'paid-memberships-pro' ); ?></strong><br />
					<?php printf(__( '<a href="%s">View your membership account</a> to verify your license key.', 'paid-memberships-pro' ), 'https://www.paidmembershipspro.com/login/?redirect_to=%2Fmembership-account%2F%3Futm_source%3Dplugin%26utm_medium%3Dpmpro-dashboard%26utm_campaign%3Dmembership-account%26utm_content%3Dverify-license-key' );?>
    		<?php } else { ?>
    			<p class="pmpro_message pmpro_success"><?php printf(__( '<strong>Thank you!</strong> A valid <strong>%s</strong> license key has been used to activate your support license on this site.', 'paid-memberships-pro' ), ucwords($pmpro_license_check['license']));?></p>
    		<?php } ?>

    		<?php if ( ! pmpro_license_isValid() ) { ?>
    			<p><?php esc_html_e( 'An annual support license is recommended for websites running Paid Memberships Pro.', 'paid-memberships-pro' ); ?><br /><a href="https://www.paidmembershipspro.com/pricing/?utm_source=plugin&utm_medium=pmpro-dashboard&utm_campaign=pricing&utm_content=upgrade" target="_blank"><?php esc_html_e( 'View Pricing &raquo;' , 'paid-memberships-pro' ); ?></a></p>
    			<p><a href="https://www.paidmembershipspro.com/membership-checkout/?level=20&utm_source=plugin&utm_medium=pmpro-dashboard&utm_campaign=plus-checkout&utm_content=upgrade" target="_blank" class="button button-action button-hero"><?php esc_attr_e( 'Upgrade', 'paid-memberships-pro' ); ?></a>
    		<?php } ?>
    		<hr />
    		<p><?php echo wp_kses_post( sprintf( __( 'Paid Memberships Pro and our add ons are distributed under the <a target="_blank" href="%s">GPLv2 license</a>. This means, among other things, that you may use the software on this site or any other site free of charge.', 'paid-memberships-pro' ), 'http://www.gnu.org/licenses/gpl-2.0.html' ) ); ?></p>
    	</div> <!-- end pmpro-dashboard-welcome-column -->
    	<div class="pmpro-dashboard-welcome-column">
    		<h3><?php esc_html_e( 'Get Involved', 'paid-memberships-pro' ); ?></h3>
    		<p><?php esc_html_e( 'There are many ways you can help support Paid Memberships Pro.', 'paid-memberships-pro' ); ?></p>
    		<p><?php esc_html_e( 'Get involved with our plugin development via GitHub.', 'paid-memberships-pro' ); ?> <a href="https://github.com/strangerstudios/paid-memberships-pro" target="_blank"><?php esc_html_e( 'View on GitHub', 'paid-memberships-pro' ); ?></a></p>
    		<ul>
				<li><a href="https://www.youtube.com/channel/UCFtMIeYJ4_YVidi1aq9kl5g/" target="_blank"><i class="dashicons dashicons-format-video"></i> <?php esc_html_e( 'Subscribe to our YouTube Channel.', 'paid-memberships-pro' ); ?></a></li>
				<li><a href="https://www.facebook.com/PaidMembershipsPro" target="_blank"><i class="dashicons dashicons-facebook"></i> <?php esc_html_e( 'Follow us on Facebook.', 'paid-memberships-pro' ); ?></a></li>
				<li><a href="https://twitter.com/pmproplugin" target="_blank"><i class="dashicons dashicons-twitter"></i> <?php esc_html_e( 'Follow @pmproplugin on Twitter.', 'paid-memberships-pro' ); ?></a></li>
				<li><a href="https://wordpress.org/plugins/paid-memberships-pro/#reviews" target="_blank"><i class="dashicons dashicons-wordpress"></i> <?php esc_html_e( 'Share an honest review at WordPress.org.', 'paid-memberships-pro' ); ?></a></li>
			</ul>
    		<hr />
    		<p><?php esc_html_e( 'Help translate Paid Memberships Pro into your language.', 'paid-memberships-pro' ); ?> <a href="https://translate.wordpress.org/projects/wp-plugins/paid-memberships-pro" target="_blank"><?php esc_html_e( 'Translation Dashboard', 'paid-memberships-pro' ); ?></a></p>
    	</div> <!-- end pmpro-dashboard-welcome-column -->
    </div> <!-- end pmpro-dashboard-welcome-columns -->
	<?php
}

/*
 * Callback function for pmpro_dashboard_report_recent_members meta box to show last 5 recent members and a link to the Members List.
 */
function pmpro_dashboard_report_recent_members_callback() {
	global $wpdb;

	$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(CONVERT_TZ(u.user_registered, '+00:00', @@global.time_zone)) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP( CONVERT_TZ(mu.startdate, '+00:00', @@global.time_zone) ) as startdate, UNIX_TIMESTAMP( CONVERT_TZ(mu.enddate, '+00:00', @@global.time_zone) ) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id WHERE mu.membership_id > 0 AND mu.status = 'active' GROUP BY u.ID ORDER BY u.user_registered DESC LIMIT 5";

	$sqlQuery = apply_filters( 'pmpro_members_list_sql', $sqlQuery );

	$theusers = $wpdb->get_results( $sqlQuery ); ?>
    <span id="pmpro_report_members" class="pmpro_report-holder">
    	<table class="wp-list-table widefat fixed striped">
    		<thead>
    			<tr>
    				<th><?php _e( 'Username', 'paid-memberships-pro' );?></th>
    				<th><?php _e( 'Level', 'paid-memberships-pro' );?></th>
    				<th><?php _e( 'Joined', 'paid-memberships-pro' );?></th>
    				<th><?php _e( 'Expires', 'paid-memberships-pro' ); ?></th>
    			</tr>
    		</thead>
    		<tbody>
    		<?php if ( empty( $theusers ) ) { ?>
                <tr>
                    <td colspan="4"><p><?php _e( 'No members found.', 'paid-memberships-pro' ); ?></p></td>
                </tr>
            <?php } else {
    			foreach ( $theusers as $auser ) {
    				$auser = apply_filters( 'pmpro_members_list_user', $auser );
    				//get meta
    				$theuser = get_userdata( $auser->ID ); ?>
    				<tr>
    					<td class="username column-username">
    						<?php echo get_avatar($theuser->ID, 32)?>
    						<strong>
    							<?php
    								$userlink = '<a href="' . get_edit_user_link( $theuser->ID ) . '">' . esc_attr( $theuser->user_login ) . '</a>';
    								$userlink = apply_filters( 'pmpro_members_list_user_link', $userlink, $theuser );
    								echo $userlink;
    							?>
    						</strong>
    					</td>
    					<td><?php esc_attr_e( $auser->membership ); ?></td>
    					<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( $theuser->user_registered ), current_time( 'timestamp' ) ) ); ?></td>
    					<td>
    						<?php
    							if($auser->enddate)
    								echo apply_filters("pmpro_memberslist_expires_column", date_i18n(get_option('date_format'), $auser->enddate), $auser);
    							else
    								echo __(apply_filters("pmpro_memberslist_expires_column", "Never", $auser), "pmpro");
    						?>
    					</td>
    				</tr>
    				<?php
    			}
            }
    		?>
    		</tbody>
    	</table>
    </span>
    <?php if ( ! empty( $theusers ) ) { ?>
        <p class="text-center"><a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=pmpro-memberslist' ); ?>"><?php esc_attr_e( 'View All Members ', 'paid-memberships-pro' ); ?></a></p>
    <?php } ?>
	<?php
}

/*
 * Callback function for pmpro_dashboard_report_recent_orders meta box to show last 5 recent orders and a link to view all Orders.
 */
function pmpro_dashboard_report_recent_orders_callback() {
	global $wpdb;

	$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS id FROM $wpdb->pmpro_membership_orders ORDER BY id DESC, timestamp DESC LIMIT 5";

	$order_ids = $wpdb->get_col( $sqlQuery );

	$totalrows = $wpdb->get_var( 'SELECT FOUND_ROWS() as found_rows' );
	?>
    <span id="pmpro_report_orders" class="pmpro_report-holder">
    	<table class="wp-list-table widefat fixed striped">
    	<thead>
    		<tr class="thead">
    			<th><?php _e( 'Code', 'paid-memberships-pro' ); ?></th>
    			<th><?php _e( 'User', 'paid-memberships-pro' ); ?></th>
    			<th><?php _e( 'Level', 'paid-memberships-pro' ); ?></th>
    			<th><?php _e( 'Total', 'paid-memberships-pro' ); ?></th>
    			<th><?php _e( 'Status', 'paid-memberships-pro' ); ?></th>
    			<th><?php _e( 'Date', 'paid-memberships-pro' ); ?></th>
    		</tr>
    		</thead>
    		<tbody id="orders" class="orders-list">
        	<?php
                if ( empty( $order_ids ) ) { ?>
                    <tr>
                        <td colspan="6"><p><?php _e( 'No orders found.', 'paid-memberships-pro' ); ?></p></td>
                    </tr>
                <?php } else {
                    foreach ( $order_ids as $order_id ) {
        			$order            = new MemberOrder();
        			$order->nogateway = true;
        			$order->getMemberOrderByID( $order_id );
        			?>
        			<tr>
        				<td>
        					<a href="admin.php?page=pmpro-orders&order=<?php echo $order->id; ?>"><?php echo $order->code; ?></a>
        				</td>
        				<td class="username column-username">
        					<?php $order->getUser(); ?>
        					<?php if ( ! empty( $order->user ) ) { ?>
        						<a href="user-edit.php?user_id=<?php echo $order->user->ID; ?>"><?php echo $order->user->user_login; ?></a>
        					<?php } elseif ( $order->user_id > 0 ) { ?>
        						[<?php _e( 'deleted', 'paid-memberships-pro' ); ?>]
        					<?php } else { ?>
        						[<?php _e( 'none', 'paid-memberships-pro' ); ?>]
        					<?php } ?>
                            
                            <?php if ( ! empty( $order->billing->name ) ) { ?>
                                <br /><?php echo $order->billing->name; ?>
                            <?php } ?>
        				</td>
                        <td>
							<?php
								$level = pmpro_getLevel( $order->membership_id );
								if ( ! empty( $level ) ) {
									echo $level->name;
								} elseif ( $order->membership_id > 0 ) { ?>
									[<?php _e( 'deleted', 'paid-memberships-pro' ); ?>]
								<?php } else { ?>
									[<?php _e( 'none', 'paid-memberships-pro' ); ?>]
								<?php }
							?>
                        </td>
        				<td><?php echo pmpro_escape_price( pmpro_formatPrice( $order->total ) ); ?></td>
        				<td>
                            <?php echo $order->gateway; ?>
                            <?php if ( $order->gateway_environment == 'test' ) {
                                echo '(test)';
                            } ?>
                            <?php if ( ! empty( $order->status ) ) {
                                echo '<br />(' . $order->status . ')'; 
                            } ?>
                        </td>
                        <td><?php echo date_i18n( get_option( 'date_format' ), $order->getTimestamp() ); ?></td>
        			</tr>
                    <?php
                }
            }
        	?>
    		</tbody>
    	</table>
    </span>
    <?php if ( ! empty( $order_ids ) ) { ?>
        <p class="text-center"><a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=pmpro-orders' ); ?>"><?php esc_attr_e( 'View All Orders ', 'paid-memberships-pro' ); ?></a></p>
    <?php } ?>
	<?php
}

/*
 * Callback function for pmpro_dashboard_news_updates meta box to show RSS Feed from Paid Memberships Pro blog.
 */
function pmpro_dashboard_news_updates_callback() {

	// Get RSS Feed(s)
	include_once( ABSPATH . WPINC . '/feed.php' );

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed( 'https://www.paidmembershipspro.com/feed/' );

	$maxitems = 0;

	if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

	    // Figure out how many total items there are, but limit it to 5.
	    $maxitems = $rss->get_item_quantity( 5 );

	    // Build an array of all the items, starting with element 0 (first element).
	    $rss_items = $rss->get_items( 0, $maxitems );

	endif;
	?>

	<ul>
	    <?php if ( $maxitems == 0 ) : ?>
	        <li><?php _e( 'No news found.', 'paid-memberships-pro' ); ?></li>
	    <?php else : ?>
	        <?php // Loop through each feed item and display each item as a hyperlink. ?>
	        <?php foreach ( $rss_items as $item ) : ?>
	            <li>
	                <a href="<?php echo esc_url( $item->get_permalink() ); ?>"
	                    title="<?php printf( __( 'Posted %s', 'paid-memberships-pro' ), $item->get_date( get_option( 'date_format' ) ) ); ?>">
	                    <?php echo esc_html( $item->get_title() ); ?>
	                </a>
					<?php echo esc_html( $item->get_date( get_option( 'date_format' ) ) ); ?>
	            </li>
	        <?php endforeach; ?>
	    <?php endif; ?>
	</ul>
	<p class="text-center"><a class="button button-primary" href="<?php echo esc_url( 'https://www.paidmembershipspro.com/blog/?utm_source=plugin&utm_medium=pmpro-dashboard&utm_campaign=blog&utm_content=news-updates-metabox' ); ?>"><?php esc_attr_e( 'View More', 'paid-memberships-pro' ); ?></a></p>
	<?php
}

/**
 * Load the Paid Memberships Pro dashboard-area footer
 */
require_once( dirname( __FILE__ ) . '/admin_footer.php' );
