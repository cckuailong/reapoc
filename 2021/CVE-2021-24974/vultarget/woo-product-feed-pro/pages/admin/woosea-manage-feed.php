<?php
$cron_projects = get_option( 'cron_projects' );
$license_information = get_option( 'license_information' );
$count_variation = wp_count_posts('product_variation');
$count_single = wp_count_posts('product');
$published_single = $count_single->publish;
$published_variation = $count_variation->publish;
$published_products = $published_single+$published_variation;
$host = $_SERVER['HTTP_HOST'];
$add_manipulation_support = get_option ('add_manipulation_support');

$product_numbers = array (
	"Single products" => $published_single,
	"Variation products" => $published_variation,
	"Total products" => $published_products
);

$plugin_data = get_plugin_data( __FILE__ );

$versions = array (
	"PHP" => (float)phpversion(),
	"Wordpress" => get_bloginfo('version'),
	"WooCommerce" => WC()->version,
	"WooCommerce Product Feed PRO" => WOOCOMMERCESEA_PLUGIN_VERSION
);

// Get the sales from created product feeds
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'adtribes_my_conversions';
$order_rows = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return _e( 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!','woo-product-feed-pro');
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object and get message and message type as WooCommerce is inactive
 * also set variable allowed on 0 to disable submit button on step 1 of configuration
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( "9", "false" );
} else {
        $notifications_box = $notifications_obj->get_admin_notifications ( '8', 'false' );
}

if ($versions['PHP'] < 5.6){
        $notifications_box = $notifications_obj->get_admin_notifications ( '11', 'false' );
}

if ($versions['WooCommerce'] < 3){
        $notifications_box = $notifications_obj->get_admin_notifications ( '13', 'false' );
}

if(!empty($license_information)){
	if($license_information['notice'] == "true"){
        	$notifications_box['message_type'] = $license_information['message_type'];
        	$notifications_box['message'] = $license_information['message'];
	}
}

if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
	$notifications_box = $notifications_obj->get_admin_notifications ( '12', 'false' );
}

?>
<div class="wrap">
        <div class="woo-product-feed-pro-form-style-2">
                <tbody class="woo-product-feed-pro-body">
			<?php
			if (array_key_exists('debug', $_GET)){

				// KILL SWITCH, THIS WILL REMOVE ALL YOUR FEED PROJECTS
				// delete_option( 'cron_projects');

				if(sanitize_text_field($_GET['debug']) == "true"){
					$external_debug_file = $notifications_obj->woosea_debug_informations ($versions, $product_numbers, $order_rows, $cron_projects);
				?>	
                        		<div class="woo-product-feed-pro-form-style-2-heading"><?php _e( 'Debugging mode', 'woo-product-feed-pro' );?></div>
					<div class="notice notice-error is-dismissible">
                				<p>
						<?php _e( 'Thank you for taking the time to help us find bugs in our plugin. It is greatly appreciated by us and your feedback will help all current and future users of this plugin. Could you please copy / paste the debug URL in the box below and send it to <a href="mailto:support@adtribes.io">support@adtribes.io</a> so we can analyse how your feed projects are configured and discover potential problems.','woo-product-feed-pro' );?><br/><br/>
							<?php
							print "<strong>Debug file:</strong><br/><a href=\"$external_debug_file\" target=\"_blank\">$external_debug_file</a>";
							?>
						</p>
					</div><br/>
				<?php
				}
			} elseif (array_key_exists('force-active', $_GET)){
				// Force active all feeds
				foreach($cron_projects as $key => $value){
					$cron_projects[$key]['active'] = "true";
				}
				update_option('cron_projects', $cron_projects,'no');
                        } elseif (array_key_exists('force-clean', $_GET)){
                                // Forcefully remove all feed and plugin configurations
                                delete_option( 'cron_projects' );
                                delete_option( 'channel_statics' );
                                delete_option( 'woosea_getelite_notification' );
                                delete_option( 'woosea_license_notification_closed' );
                                wp_clear_scheduled_hook( 'woosea_cron_hook' );
                                wp_clear_scheduled_hook( 'woosea_check_license' );
			} else {
                                // Set default notification to show
                                $getelite_notice = get_option('woosea_getelite_notification');
                                if(empty($getelite_notice['show'])){
                                        $getelite_notice['show'] = "yes";
                                        $getelite_notice['timestamp'] = date( 'd-m-Y' );
                                }

                                if($getelite_notice['show'] <> "no"){
                                ?>
					<div class="notice notice-info get_elite is-dismissible">
                				<p>
						<strong><?php _e( 'Would you like to get more out of your product feeds? Upgrade to the Elite version of the plugin and you will get:', 'woo-product-feed-pro' );?></strong><br/></br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Priority support - we will help you to get your product feed(s) up-and-running;', 'woo-product-feed-pro' );?><br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'GTIN, Brand, MPN, EAN, Condition and more fields for your product feeds', 'woo-product-feed-pro' );?> [<a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=adding%20fields" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Enhanched structured data on your product pages: more products approved in your Google Merchant Center', 'woo-product-feed-pro' );?> [<a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=structured%20data%20bug" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Advanced product data manipulation','woo-product-feed-pro' );?> [<a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=product%20data%20manipulation" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'WPML support - including their currency switcher','woo-product-feed-pro' );?> [<a href="https://adtribes.io/wpml-support/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=wpml%20support" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Aelia currency switcher support','woo-product-feed-pro' );?> [<a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=aelia%20support" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Polylang support','woo-product-feed-pro' );?> [<a href="https://adtribes.io/polylang-support-product-feeds/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=polylang%20support" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
						<span class="dashicons dashicons-yes"></span><?php _e( 'Facebook pixel feature','woo-product-feed-pro' );?> [<a href="https://adtribes.io/facebook-pixel-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=facebook pixel feature" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/><br/>
 						<?php _e( 'Upgrade to the','woo-product-feed-pro' );?> <strong><a href="https://adtribes.io/pro-vs-elite/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=top-notification&utm_content=notification" target="_blank"><?php _e( 'Elite version of our plugin</a></strong> to get all these features.','woo-product-feed-pro' );?>
						</p>
					</div>
				<?php
				}
			}


			if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
				?>				
					<div class="notice notice-error is-dismissible">
                				<p>
						<strong><?php _e( 'WARNING: Your WP-Cron is disabled', 'woo-product-feed-pro' );?></strong><br/></br/>
						We detected that your WP-cron has been disabled in your wp-config.php file. Our plugin heavily depends on the WP-cron being active otherwise it cannot update and generate your product feeds. <a href="https://adtribes.io/help-my-feed-processing-is-stuck/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=cron-warning&utm_content=notification" target="_blank"><strong>Please enable your WP-cron first</strong></a>.
						</p>
					</div>
				<?php
			}

			// Double check if the woosea_cron_hook is there, when it is not create a new one
                	if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
                        	wp_schedule_event ( time(), 'hourly', 'woosea_cron_hook');
                	}
			?>

                        <div class="woo-product-feed-pro-form-style-2-heading"><?php _e( 'Manage feeds','woo-product-feed-pro' );?></div>
			<div class="woo-product-feed-pro-table-wrapper">
			<div class="woo-product-feed-pro-table-left">

		        <table id="woosea_main_table" class="woo-product-feed-pro-table">
			<tr>
				<td><strong><?php _e( 'Active','woo-product-feed-pro' );?></strong></td>
				<td><strong><?php _e( 'Project name and channel','woo-product-feed-pro' );?></strong></td>
				<td><strong><?php _e( 'Format','woo-product-feed-pro' );?></strong></td>
				<td><strong><?php _e( 'Refresh interval','woo-product-feed-pro' );?></strong></td>
				<td><strong><?php _e( 'Status','woo-product-feed-pro' );?></strong></td>
				<td></td>
			</tr>
	
			<?php
			if($cron_projects){
				$toggle_count = 1;
				$class = "";

				foreach ($cron_projects as $key=>$val){
				
					//echo '<pre>' . print_r( $val, true ) . '</pre>';

					if(isset($val['active']) AND ($val['active'] == "true")){
						$checked = "checked";
						$class = "";
					} else {
						$checked = "";
					}

					if(isset($val['filename'])){
						$projectname = ucfirst($val['projectname']);
					?>
					<form action="" method="post">
					<tr class="<?php print "$class";?>">
						<td>
                                                <label class="woo-product-feed-pro-switch">
                                                        <input type="hidden" name="manage_record" value="<?php print "$val[project_hash]";?>"><input type="checkbox" name="project_active[]" class="checkbox-field" value="<?php print "$val[project_hash]";?>" <?php print "$checked";?>>
                                                        <div class="woo-product-feed-pro-slider round"></div>
                                                </label>
						</td>
						<td><span><?php print "$projectname</span><br/><span class=\"woo-product-feed-pro-channel\">Channel: $val[name]</span>";?></span></td>
						<td><span><?php print "$val[fileformat]";?></span></td>
						<td><span><?php print "$val[cron]";?></span></td>
						<?php
							if ($val['running'] == "processing"){
								$proc_perc = round(($val['nr_products_processed']/$val['nr_products'])*100);
								print "<td><span class=\"woo-product-feed-pro-blink_me\" id=\"woosea_proc_$val[project_hash]\">$val[running] ($proc_perc%)</span></td>";
							} else {
								print "<td><span class=\"woo-product-feed-pro-blink_off_$val[project_hash]\" id=\"woosea_proc_$val[project_hash]\">$val[running]</span></td>";
							}
						?>
						<td>
							<div class="actions">
								<span class="gear dashicons dashicons-admin-generic" id="gear_<?php print "$val[project_hash]";?>" title="project settings" style="display: inline-block;"></span>
								<?php 
								if ($val['running'] != "processing"){
								?>
									<?php
									if ($val['active'] == "true"){
										print "<span class=\"dashicons dashicons-admin-page\" id=\"copy_$val[project_hash]\" title=\"copy project\" style=\"display: inline-block;\"></span>";
										print "<span class=\"dashicons dashicons-update\" id=\"refresh_$val[project_hash]\" title=\"manually refresh productfeed\" style=\"display: inline-block;\"></span>";
										
										if($val['running'] != "not run yet"){
											print "<a href=\"$val[external_file]\" target=\"_blank\" class=\"dashicons dashicons-download\" id=\"download\" title=\"download productfeed\" style=\"display: inline-block\"></a>";
										}
									}?>
									<span class="trash dashicons dashicons-trash" id="trash_<?php print "$val[project_hash]";?>" title="delete project and productfeed" style="display: inline-block;"></span>
								<?php
								} else {
									print "<span class=\"dashicons dashicons-dismiss\" id=\"cancel_$val[project_hash]\" title=\"cancel processing productfeed\" style=\"display: inline-block;\"></span>";
								}
								?>
							</div>
						</td>
					</tr>
					<tr>
						<td id="manage_inline" colspan="8">
							<div>
								<table class="woo-product-feed-pro-inline_manage">

									<?php
									if (($val['running'] == "ready") OR ($val['running'] == "stopped") OR($val['running'] == "not run yet")){
									?>
									<tr>
										<td>
											<strong><?php _e( 'Change settings','woo-product-feed-pro' );?></strong><br/>
											<span class="dashicons dashicons-arrow-right" style="display: inline-block;"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=0&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>"><?php _e( 'General feed settings','woo-product-feed-pro' );?></a><br/>
											<?php
											if ($val['fields'] == "standard"){
												print "<span class=\"dashicons dashicons-arrow-right\" style=\"display: inline-block;\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=2&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">"; _e( 'Attribute selection','woo-product-feed-pro' ); print"</a></br/>";
											} else {
												print "<span class=\"dashicons dashicons-arrow-right\" style=\"display: inline-block;\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=7&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">"; _e( 'Field mapping','woo-product-feed-pro' ); print"</a><br/>";
											}
											
											if ($val['taxonomy'] != "none"){
												print "<span class=\"dashicons dashicons-arrow-right\" style=\"display: inline-block;\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=1&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">"; _e( 'Category mapping','woo-product-feed-pro' ); print"</a><br/>";
											}
											?>
											
											<?php
											if ((isset($add_manipulation_support)) AND ($add_manipulation_support == "yes")){
											?>
											<span class="dashicons dashicons-arrow-right" style="display: inline-block;"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=9&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>"><?php _e( 'Product data manipulation','woo-product-feed-pro');?></a><br/>
											<?php
											}
											?>
											<span class="dashicons dashicons-arrow-right" style="display: inline-block;"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=4&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>"><?php _e( 'Feed filters and rules','woo-product-feed-pro' );?></a><br/>
											<span class="dashicons dashicons-arrow-right" style="display: inline-block;"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=5&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>"><?php _e( 'Conversion & Google Analytics settings' );?></a><br/>
										</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td>
											<strong><?php _e( 'Feed URL','woo-product-feed-pro' );?></strong><br/>
											<?php
											if (($val['active'] == "true") AND ($val['running'] != "not run yet")){
											 	print "<span class=\"dashicons dashicons-arrow-right\" style=\"display: inline-block;\"></span> <a href=\"$val[external_file]\" target=\"_blank\">$val[external_file]</a>";
											} else {
												print "<span class=\"dashicons dashicons-warning\"></span> Whoops, there is no active product feed for this project as the project has been disabled or did not run yet.";
											}
											?>
										</td>
									</tr>
									
								</table>
							</div>
						</td>
					</tr>	
					</form>
					<?php
					$toggle_count++;
					} else {
						// Removing this partly configured feed as it results in PHP warnings
						unset($cron_projects[$key]);
		                                update_option('cron_projects', $cron_projects,'no');
					}	
				}
			} else {
				?>
				<tr>
					<td colspan="6"><br/><span class="dashicons dashicons-warning"></span> <?php _e( 'You didn\'t configured a product feed yet','woo-product-feed-pro' );?>, <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php"><?php _e( 'please create one first</a> or read our tutorial on','woo-product-feed-pro' );?> <a href="https://adtribes.io/setting-up-your-first-google-shopping-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=first shopping feed" target="_blank"><?php _e( 'how to set up your very first Google Shopping product feed','woo-product-feed-pro' );?></a>.<br/><br/></td>
				</tr>
				<?php
			}
			?>
			</table>
			</div>
			<div class="woo-product-feed-pro-table-right">
                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'Why upgrade to Elite?','woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e ('Enjoy all priviliges of our Elite features and priority support and upgrade to the Elite version of our plugin now!','woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong>1.</strong> <?php _e( 'Priority support: get your feeds live faster','woo-product-feed-pro' );?></li>
                                                                <li><strong>2.</strong> <?php _e( 'More products approved by Google','woo-product-feed-pro' );?></li>
                                                                <li><strong>3.</strong> <?php _e( 'Add GTIN, brand and more fields to your store','woo-product-feed-pro' );?></li>
                                                                <li><strong>4.</strong> <?php _e( 'Exclude individual products from your feeds','woo-product-feed-pro' );?></li>
                                                                <li><strong>5.</strong> <?php _e( 'WPML support','woo-product-feed-pro' );?></li>
                                                               	<li><strong>6.</strong> <?php _e( 'Aelia currency switcher support','woo-product-feed-pro' );?></li>
                                                               	<li><strong>7.</strong> <?php _e( 'Facebook pixel feature','woo-product-feed-pro' );?></li>
                                                               	<li><strong>8.</strong> <?php _e( 'Polylang support','woo-product-feed-pro' );?></li>
							 </ul>
                                                        <strong>
                                                        <a href="https://adtribes.io/pro-vs-elite/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=why-upgrade-box" target="_blank"><?php _e( 'Upgrade to Elite here!','woo-product-feed-pro' );?></a>
                                                        </strong>
                                                </td>
                                        </tr>
                                </table><br/>

                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'We have got you covered!','woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e( 'Need assistance? Check out our:','woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong><a href="https://adtribes.io/support/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=faq" target="_blank"><?php _e( 'Frequently Asked Questions','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://www.youtube.com/channel/UCXp1NsK-G_w0XzkfHW-NZCw" target="_blank"><?php _e( 'YouTube tutorials','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://adtribes.io/tutorials/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=tutorials" target="_blank"><?php _e( 'Tutorials','woo-product-feed-pro' );?></a></strong></li>
                                                        </ul>
                                                        <?php _e( 'Or just reach out to us at','woo-product-feed-pro' );?>  <strong><a href="https://wordpress.org/support/plugin/woo-product-feed-pro/" target="_blank"><?php _e( 'our Wordpress forum','woo-product-feed-pro' );?></a></strong> <?php _e( 'and we will make sure your product feeds will be up-and-running within no-time.','woo-product-feed-pro' );?>
                                                </td>
                                        </tr>
                                </table><br/>

				<table class="woo-product-feed-pro-table">
        		                <tr>
						<td><strong><?php _e( 'Our latest tutorials','woo-product-feed-pro' );?></strong></td>
					</tr>
					<tr>
						<td>
							<ul>
								<li><strong>1. <a href="https://adtribes.io/setting-up-your-first-google-shopping-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=first shopping feed" target="_blank"><?php _e( 'Create a Google Shopping feed','woo-product-feed-pro' );?></a></strong></li>

								<li><strong>2. <a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=product_data_manipulation" target="_blank"><?php _e( 'Product data manipulation','woo-product-feed-pro' );?></a></strong></li>

								<li><strong>3. <a href="https://adtribes.io/how-to-create-filters-for-your-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=how to create filters" target="_blank"><?php _e( 'How to create filters for your product feed','woo-product-feed-pro' );?></a></strong></li>
								<li><strong>4. <a href="https://adtribes.io/how-to-create-rules/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=how to create rules" target="_blank"><?php _e( 'How to set rules for your product feed','woo-product-feed-pro' );?></a></strong></li>
								<li><strong>5. <a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=adding fields" target="_blank"><?php _e( 'Adding GTIN, Brand, MPN and more','woo-product-feed-pro' );?></a></strong></li>
								<li><strong>6. <a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=structured data bug" target="_blank"><?php _e( 'WooCommerce structured data markup bug','woo-product-feed-pro' );?></a></strong></li>
						 		<li><strong>7. <a href="https://adtribes.io/wpml-support/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=wpml support" target="_blank"><?php _e( 'Enable WPML support','woo-product-feed-pro' );?></a></strong></li>
						 		<li><strong>8. <a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=aelia support" target="_blank"><?php _e( 'Enable Aelia currency switcher support','woo-product-feed-pro' );?></a></strong></li>
							 	<li><strong>9. <a href="https://adtribes.io/help-my-feed-processing-is-stuck/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=feed stuck" target="_blank"><?php _e( 'Help, my feed is stuck!','woo-product-feed-pro' );?></a></strong></li>
							 	<li><strong>10. <a href="https://adtribes.io/help-i-have-none-or-less-products-in-my-product-feed-than-expected/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=too few products" target="_blank"><?php _e( 'Help, my feed has no or too few products!','woo-product-feed-pro');?></a></strong></li>
                                                                <li><strong>11. <a href="https://adtribes.io/polylang-support-product-feeds/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=polylang support" target="_blank"><?php _e( 'How to use the Polylang feature', 'woo-product-feed-pro' );?></a></strong></li>
							</ul>
						</td>
					</tr>
				</table><br/>

			</div>
			</div>
		</tbody>
	</div>
</div>
