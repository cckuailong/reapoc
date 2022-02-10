<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_advancedsettings")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}

	global $wpdb, $msg, $msgt, $allowedposttags;

	//check nonce for saving settings
	if (!empty($_REQUEST['savesettings']) && (empty($_REQUEST['pmpro_advancedsettings_nonce']) || !check_admin_referer('savesettings', 'pmpro_advancedsettings_nonce'))) {
		$msg = -1;
		$msgt = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
		unset($_REQUEST['savesettings']);
	}
	
	//get/set settings
	if(!empty($_REQUEST['savesettings']))
	{
		// Dashboard settings.
		pmpro_setOption( 'hide_toolbar' );
		pmpro_setOption( 'block_dashboard' );
		
		// Message settings.
		// These use wp_kses for better security handling.
		$nonmembertext = wp_kses(wp_unslash($_POST['nonmembertext']), $allowedposttags);
		update_option('pmpro_nonmembertext', $nonmembertext);
		
		$notloggedintext = wp_kses(wp_unslash($_POST['notloggedintext']), $allowedposttags);
		update_option('pmpro_notloggedintext', $notloggedintext);
		
		$rsstext = wp_kses(wp_unslash($_POST['rsstext']), $allowedposttags);
		update_option('pmpro_rsstext', $rsstext);		
		
		// Content settings.
		pmpro_setOption("filterqueries");
		pmpro_setOption("showexcerpts");		

		// Checkout settings.
		pmpro_setOption("tospage");
		pmpro_setOption("recaptcha");
		pmpro_setOption("recaptcha_version");
		pmpro_setOption("recaptcha_publickey");
		pmpro_setOption("recaptcha_privatekey");		

		// Communication settings.
		pmpro_setOption("maxnotificationpriority");
		pmpro_setOption("activity_email_frequency");

		// Other settings.
		pmpro_setOption("hideads");
		pmpro_setOption("hideadslevels");
		pmpro_setOption("redirecttosubscription");
		pmpro_setOption("uninstall");

        /**
         * Filter to add custom settings to the advanced settings page.
         * @param array $settings Array of settings, each setting an array with keys field_name, field_type, label, description.
         */
        $custom_settings = apply_filters('pmpro_custom_advanced_settings', array());
        foreach($custom_settings as $setting) {
        	if(!empty($setting['field_name']))
        		pmpro_setOption($setting['field_name']);
        }
        
		// Assume success.
		$msg = true;
		$msgt = __("Your advanced settings have been updated.", 'paid-memberships-pro' );
	}

	// Dashboard settings.
	$hide_toolbar = pmpro_getOption( 'hide_toolbar' );
	$block_dashboard = pmpro_getOption( 'block_dashboard' );
	
	// Message settings.
	$nonmembertext = pmpro_getOption("nonmembertext");
	$notloggedintext = pmpro_getOption("notloggedintext");
	$rsstext = pmpro_getOption("rsstext");
    
	// Content settings.
	$filterqueries = pmpro_getOption('filterqueries');
	$showexcerpts = pmpro_getOption("showexcerpts");	

	// Checkout settings.
	$tospage = pmpro_getOption("tospage");
	$recaptcha = pmpro_getOption("recaptcha");
	$recaptcha_version = pmpro_getOption("recaptcha_version");
	$recaptcha_publickey = pmpro_getOption("recaptcha_publickey");
	$recaptcha_privatekey = pmpro_getOption("recaptcha_privatekey");

	// Communication settings.
	$maxnotificationpriority = pmpro_getOption("maxnotificationpriority");
	$activity_email_frequency = pmpro_getOption("activity_email_frequency");

	// Other settings.
	$hideads = pmpro_getOption("hideads");
	$hideadslevels = pmpro_getOption("hideadslevels");
	if( is_multisite() ) {
		$redirecttosubscription = pmpro_getOption("redirecttosubscription");
	}
	$uninstall = pmpro_getOption('uninstall');

	// Default settings.
	if(!$nonmembertext)
	{
		$nonmembertext = sprintf( __( 'This content is for !!levels!! members only.<br /><a href="%s">Join Now</a>', 'paid-memberships-pro' ), "!!levels_page_url!!" );
		pmpro_setOption("nonmembertext", $nonmembertext);
	}
	if(!$notloggedintext)
	{
		$notloggedintext = sprintf( __( 'This content is for !!levels!! members only.<br /><a href="%s">Log In</a> <a href="%s">Join Now</a>', 'paid-memberships-pro' ), '!!login_url!!', "!!levels_page_url!!" );
		pmpro_setOption("notloggedintext", $notloggedintext);
	}
	if(!$rsstext)
	{
		$rsstext = __( 'This content is for members only. Visit the site and log in/register to read.', 'paid-memberships-pro' );
		pmpro_setOption("rsstext", $rsstext);
	}

	$levels = $wpdb->get_results( "SELECT * FROM {$wpdb->pmpro_membership_levels}", OBJECT );

	if ( empty( $activity_email_frequency ) ) {
		$activity_email_frequency = 'week';
	}

	require_once(dirname(__FILE__) . "/admin_header.php");
?>

	<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('savesettings', 'pmpro_advancedsettings_nonce');?>
		
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Advanced Settings', 'paid-memberships-pro' ); ?></h1>
		<hr class="wp-header-end">
		<div class="pmpro_admin_section pmpro_admin_section-restrict-dashboard">
			<h2 class="title"><?php esc_html_e( 'Restrict Dashboard Access', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="block_dashboard"><?php _e('WordPress Dashboard', 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<input id="block_dashboard" name="block_dashboard" type="checkbox" value="yes" <?php checked( $block_dashboard, 'yes' ); ?> /> <label for="block_dashboard"><?php _e('Block all users with the Subscriber role from accessing the Dashboard.', 'paid-memberships-pro' );?></label>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="hide_toolbar"><?php _e('WordPress Toolbar', 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<input id="hide_toolbar" name="hide_toolbar" type="checkbox" value="yes" <?php checked( $hide_toolbar, 'yes' ); ?> /> <label for="hide_toolbar"><?php _e('Hide the Toolbar from all users with the Subscriber role.', 'paid-memberships-pro' );?></label>
					</td>
				</tr>
			</tbody>
			</table>
		</div> <!-- end pmpro_admin_section-restrict-dashboard -->
		<hr />
		<div class="pmpro_admin_section pmpro_admin_section-message-settings">
			<h2 class="title"><?php esc_html_e( 'Message Settings', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="nonmembertext"><?php _e('Message for Logged-in Non-members', 'paid-memberships-pro' );?>:</label>
					</th>
					<td>
						<textarea name="nonmembertext" rows="3" cols="50" class="large-text"><?php echo stripslashes($nonmembertext)?></textarea>
						<p class="description"><?php _e('This message replaces the post content for non-members. Available variables', 'paid-memberships-pro' );?>: <code>!!levels!!</code> <code>!!referrer!!</code> <code>!!levels_page_url!!</code></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="notloggedintext"><?php _e('Message for Logged-out Users', 'paid-memberships-pro' );?>:</label>
					</th>
					<td>
						<textarea name="notloggedintext" rows="3" cols="50" class="large-text"><?php echo stripslashes($notloggedintext)?></textarea>
						<p class="description"><?php _e('This message replaces the post content for logged-out visitors.', 'paid-memberships-pro' );?> <?php _e('Available variables', 'paid-memberships-pro' );?>: <code>!!levels!!</code> <code>!!referrer!!</code> <code>!!login_url!!</code> <code>!!levels_page_url!!</code></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="rsstext"><?php _e('Message for RSS Feed', 'paid-memberships-pro' );?>:</label>
					</th>
					<td>
						<textarea name="rsstext" rows="3" cols="50" class="large-text"><?php echo stripslashes($rsstext)?></textarea>
						<p class="description"><?php _e('This message replaces the post content in RSS feeds.', 'paid-memberships-pro' );?> <?php _e('Available variables', 'paid-memberships-pro' );?>: <code>!!levels!!</code></p>
					</td>
				</tr>
			</tbody>
			</table>
		</div> <!-- end pmpro_admin_section-message-settings -->
		<hr />
		<div class="pmpro_admin_section pmpro_admin_section-content-settings">
			<h2 class="title"><?php esc_html_e( 'Content Settings', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="filterqueries"><?php _e("Filter searches and archives?", 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<select id="filterqueries" name="filterqueries">
							<option value="0" <?php if(!$filterqueries) { ?>selected="selected"<?php } ?>><?php _e('No - Non-members will see restricted posts/pages in searches and archives.', 'paid-memberships-pro' );?></option>
							<option value="1" <?php if($filterqueries == 1) { ?>selected="selected"<?php } ?>><?php _e('Yes - Only members will see restricted posts/pages in searches and archives.', 'paid-memberships-pro' );?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="showexcerpts"><?php _e('Show Excerpts to Non-Members?', 'paid-memberships-pro' );?></label>
	            </th>
	            <td>
	                <select id="showexcerpts" name="showexcerpts">
	                    <option value="0" <?php if(!$showexcerpts) { ?>selected="selected"<?php } ?>><?php _e('No - Hide excerpts.', 'paid-memberships-pro' );?></option>
	                    <option value="1" <?php if($showexcerpts == 1) { ?>selected="selected"<?php } ?>><?php _e('Yes - Show excerpts.', 'paid-memberships-pro' );?></option>
	                </select>
	            </td>
	            </tr>
			</tbody>
			</table>
		</div> <!-- end pmpro_admin_section-content-settings -->
		<hr />
		<div class="pmpro_admin_section pmpro_admin_section-checkout-settings">
			<h2 class="title"><?php esc_html_e( 'Checkout Settings', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="tospage"><?php _e('Require Terms of Service on signups?', 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<?php
							wp_dropdown_pages(array("name"=>"tospage", "show_option_none"=>"No", "selected"=>$tospage));
						?>
						<br />
						<p class="description"><?php _e('If yes, create a WordPress page containing your TOS agreement and assign it using the dropdown above.', 'paid-memberships-pro' );?></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="recaptcha"><?php _e('Use reCAPTCHA?', 'paid-memberships-pro' );?>:</label>
					</th>
					<td>
						<select id="recaptcha" name="recaptcha" onchange="pmpro_updateRecaptchaTRs();">
							<option value="0" <?php if(!$recaptcha) { ?>selected="selected"<?php } ?>><?php _e('No', 'paid-memberships-pro' );?></option>
							<option value="1" <?php if($recaptcha == 1) { ?>selected="selected"<?php } ?>><?php _e('Yes - Free memberships only.', 'paid-memberships-pro' );?></option>
							<option value="2" <?php if($recaptcha == 2) { ?>selected="selected"<?php } ?>><?php _e('Yes - All memberships.', 'paid-memberships-pro' );?></option>
						</select>
						<p class="description"><?php _e('A free reCAPTCHA key is required.', 'paid-memberships-pro' );?> <a href="https://www.google.com/recaptcha/admin/create"><?php _e('Click here to signup for reCAPTCHA', 'paid-memberships-pro' );?></a>.</p>
					</td>
				</tr>
			</tbody>
			</table>
			<table class="form-table" id="recaptcha_settings" <?php if(!$recaptcha) { ?>style="display: none;"<?php } ?>>
			<tbody>
				<tr>
					<th scope="row" valign="top"><label for="recaptcha_version"><?php _e( 'reCAPTCHA Version', 'paid-memberships-pro' );?>:</label></th>
					<td>					
						<select id="recaptcha_version" name="recaptcha_version">
							<option value="2_checkbox" <?php selected( '2_checkbox', $recaptcha_version ); ?>><?php _e( ' v2 - Checkbox', 'paid-memberships-pro' ); ?></option>
							<option value="3_invisible" <?php selected( '3_invisible', $recaptcha_version ); ?>><?php _e( 'v3 - Invisible', 'paid-memberships-pro' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Changing your version will require new API keys.', 'paid-memberships-pro' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_publickey"><?php _e('reCAPTCHA Site Key', 'paid-memberships-pro' );?>:</label></th>
					<td>
						<input type="text" id="recaptcha_publickey" name="recaptcha_publickey" value="<?php echo esc_attr($recaptcha_publickey);?>" class="regular-text code" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recaptcha_privatekey"><?php _e('reCAPTCHA Secret Key', 'paid-memberships-pro' );?>:</label></th>
					<td>
						<input type="text" id="recaptcha_privatekey" name="recaptcha_privatekey" value="<?php echo esc_attr($recaptcha_privatekey);?>" class="regular-text code" />
					</td>
				</tr>
			</tbody>
			</table>
		</div> <!-- end pmpro_admin_section-checkout-settings -->
		<hr />
		<div class="pmpro_admin_section pmpro_admin_section-communication-settings">
			<h2 class="title"><?php esc_html_e( 'Communication Settings', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><?php _e( 'Notifications', 'paid-memberships-pro' ); ?></th>
					<td>
						<select name="maxnotificationpriority">
							<option value="5" <?php selected( $maxnotificationpriority, 5 ); ?>>
								<?php _e( 'Show all notifications.', 'paid-memberships-pro' ); ?>
							</option>
							<option value="1" <?php selected( $maxnotificationpriority, 1 ); ?>>
								<?php _e( 'Show only security notifications.', 'paid-memberships-pro' ); ?>
							</option>
						</select>
						<br />
						<p class="description"><?php _e('Notifications are occasionally shown on the Paid Memberships Pro settings pages.', 'paid-memberships-pro' );?></p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="activity_email_frequency"><?php _e('Activity Email Frequency', 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<select name="activity_email_frequency">
							<option value="day" <?php selected( $activity_email_frequency, 'day' ); ?>>
								<?php _e( 'Daily', 'paid-memberships-pro' ); ?>
							</option>
							<option value="week" <?php selected( $activity_email_frequency, 'week' ); ?>>
								<?php _e( 'Weekly', 'paid-memberships-pro' ); ?>
							</option>
							<option value="month" <?php selected( $activity_email_frequency, 'month' ); ?>>
								<?php _e( 'Monthly', 'paid-memberships-pro' ); ?>
							</option>
							<option value="never" <?php selected( $activity_email_frequency, 'never' ); ?>>
								<?php _e( 'Never', 'paid-memberships-pro' ); ?>
							</option>
						</select>
						<br />
						<p class="description"><?php _e( 'Send periodic sales and revenue updates from this site to the administration email address.', 'paid-memberships-pro' );?></p>
					</td>
				</tr>
			</tbody>
			</table>
		</div> <!-- end pmpro_admin_section-communication-settings -->
		<hr />
		<div class="pmpro_admin_section pmpro_admin_section-other-settings">
			<h2 class="title"><?php esc_html_e( 'Other Settings', 'paid-memberships-pro' ); ?></h2>
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="hideads"><?php _e("Hide Ads From Members?", 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<select id="hideads" name="hideads" onchange="pmpro_updateHideAdsTRs();">
							<option value="0" <?php if(!$hideads) { ?>selected="selected"<?php } ?>><?php _e('No', 'paid-memberships-pro' );?></option>
							<option value="1" <?php if($hideads == 1) { ?>selected="selected"<?php } ?>><?php _e('Hide Ads From All Members', 'paid-memberships-pro' );?></option>
							<option value="2" <?php if($hideads == 2) { ?>selected="selected"<?php } ?>><?php _e('Hide Ads From Certain Members', 'paid-memberships-pro' );?></option>
						</select>
					</td>
				</tr>
				<tr id="hideads_explanation" <?php if($hideads < 2) { ?>style="display: none;"<?php } ?>>
					<th scope="row" valign="top">&nbsp;</th>
					<td>
						<p><?php _e('To hide ads in your template code, use code like the following', 'paid-memberships-pro' );?>:</p>
					<pre lang="PHP">
if ( function_exists( 'pmpro_displayAds' ) && pmpro_displayAds() ) {
	//insert ad code here
}</pre>
					</td>
				</tr>			
				<tr id="hideadslevels_tr" <?php if($hideads != 2) { ?>style="display: none;"<?php } ?>>
					<th scope="row" valign="top">
						<label for="hideadslevels"><?php _e('Choose Levels to Hide Ads From', 'paid-memberships-pro' );?>:</label>
					</th>
					<td>
						<?php
							// Build the selectors for the checkbox list based on number of levels.
							$classes = array();
							$classes[] = "pmpro_checkbox_box";
							if ( count( $levels ) > 5 ) {
								$classes[] = "pmpro_scrollable";
							}
							$class = implode( ' ', array_unique( $classes ) );
						?>
						<div class="<?php echo esc_attr( $class ); ?>">
							<?php
								$hideadslevels = pmpro_getOption("hideadslevels");
								if(!is_array($hideadslevels))
									$hideadslevels = explode(",", $hideadslevels);

								$sqlQuery = "SELECT * FROM $wpdb->pmpro_membership_levels ";
								$levels = $wpdb->get_results($sqlQuery, OBJECT);
								$levels = pmpro_sort_levels_by_order( $levels );
								foreach($levels as $level)
								{
							?>
								<div class="pmpro_clickable">
									<input type="checkbox" id="hideadslevels_<?php echo esc_attr( $level->id ); ?>" name="hideadslevels[]" value="<?php echo esc_attr( $level->id); ?>" <?php checked( in_array( $level->id, $hideadslevels ), true ); ?>>
									<label for="hideadslevels_<?php echo esc_attr( $level->id ); ?>"><?php echo esc_html( $level->name ); ?></label>
								</div>
							<?php
								}
							?>
						</div>
					</td>
				</tr>
				<?php if(is_multisite()) { ?>
				<tr>
					<th scope="row" valign="top">
						<label for="redirecttosubscription"><?php _e('Redirect all traffic from registration page to /susbcription/?', 'paid-memberships-pro' );?>: <em>(<?php _e('multisite only', 'paid-memberships-pro' );?>)</em></label>
					</th>
					<td>
						<select id="redirecttosubscription" name="redirecttosubscription">
							<option value="0" <?php if(!$redirecttosubscription) { ?>selected="selected"<?php } ?>><?php _e('No', 'paid-memberships-pro' );?></option>
							<option value="1" <?php if($redirecttosubscription == 1) { ?>selected="selected"<?php } ?>><?php _e('Yes', 'paid-memberships-pro' );?></option>
						</select>
					</td>
				</tr>
				<?php } ?>			
				<?php
	            // Filter to Add More Advanced Settings for Misc Plugin Options, etc.
	            if (has_action('pmpro_custom_advanced_settings')) {
		            $custom_fields = apply_filters('pmpro_custom_advanced_settings', array());
		            foreach ($custom_fields as $field) {
		            ?>
		            <tr>
		                <th valign="top" scope="row">
		                    <label
		                        for="<?php echo esc_attr( $field['field_name'] ); ?>"><?php echo esc_textarea( $field['label'] ); ?></label>
		                </th>
		                <td>
		                    <?php
		                    switch ($field['field_type']) {
		                        case 'select':
		                            ?>
		                            <select id="<?php echo esc_attr( $field['field_name'] ); ?>"
		                                    name="<?php echo esc_attr( $field['field_name'] ); ?>">
		                                <?php 
		                                	//For associative arrays, we use the array keys as values. For numerically indexed arrays, we use the array values.
		                                	$is_associative = (bool)count(array_filter(array_keys($field['options']), 'is_string'));
		                                	foreach ($field['options'] as $key => $option) {
		                                    	if(!$is_associative) $key = $option;
		                                    	?>
		                                    	<option value="<?php echo esc_attr($key); ?>" <?php selected($key, pmpro_getOption($field['field_name']));?>>
		                                    		<?php echo esc_textarea($option); ?>
		                                    	</option>
		                               			<?php
		                                	} 
		                                ?>
		                            </select>
		                            <?php
		                            break;
		                        case 'text':
		                            ?>
		                            <input id="<?php echo esc_attr( $field['field_name'] ); ?>"
		                                   name="<?php echo esc_attr( $field['field_name'] ); ?>"
		                                   type="<?php echo esc_attr( $field['field_type'] ); ?>"
		                                   value="<?php echo esc_attr(pmpro_getOption($field['field_name'])); ?> "
		                                   class="regular-text">
		                            <?php
		                            break;
		                        case 'textarea':
		                            ?>
		                            <textarea id="<?php echo esc_attr( $field['field_name'] ); ?>"
		                                      name="<?php echo esc_attr( $field['field_name'] ); ?>"
		                                      class="large-text">
		                                <?php echo esc_textarea(pmpro_getOption($field['field_name'])); ?>
		                            </textarea>
		                            <?php
		                            break;
		                        default:
		                            break;
		                    }
							if ( ! empty( $field['description'] ) ) {
								$allowed_pmpro_custom_advanced_settings_html = array (
									'a' => array (
										'href' => array(),
										'target' => array(),
										'title' => array(),
									),
								);
								?>
								<p class="description"><?php echo wp_kses( $field['description'], $allowed_pmpro_custom_advanced_settings_html ); ?></p>
								<?php } ?>
		                </td>
		            </tr>
		            <?php
		            }
		        } 
		        ?>
				<tr>
					<th scope="row" valign="top">
						<label for="uninstall"><?php _e('Uninstall PMPro on deletion?', 'paid-memberships-pro' );?></label>
					</th>
					<td>
						<select id="uninstall" name="uninstall">
							<option value="0" <?php if ( ! $uninstall ) { ?>selected="selected"<?php } ?>><?php _e( 'No', 'paid-memberships-pro' );?></option>
							<option value="1" <?php if ( $uninstall == 1 ) { ?>selected="selected"<?php } ?>><?php _e( 'Yes - Delete all PMPro Data.', 'paid-memberships-pro' );?></option>
						</select>
						<p class="description"><?php esc_html_e( 'To delete all PMPro data from the database, set to Yes, deactivate PMPro, and then click to delete PMPro from the plugins page.' ); ?></p>
					</td>
				</tr>
	        </tbody>
			</table>
			<script>
				function pmpro_updateHideAdsTRs()
				{
					var hideads = jQuery('#hideads').val();
					if(hideads == 2)
					{
						jQuery('#hideadslevels_tr').show();
					}
					else
					{
						jQuery('#hideadslevels_tr').hide();
					}

					if(hideads > 0)
					{
						jQuery('#hideads_explanation').show();
					}
					else
					{
						jQuery('#hideads_explanation').hide();
					}
				}
				pmpro_updateHideAdsTRs();

				function pmpro_updateRecaptchaTRs()
				{
					var recaptcha = jQuery('#recaptcha').val();
					if(recaptcha > 0)
					{
						jQuery('#recaptcha_settings').show();
					}
					else
					{
						jQuery('#recaptcha_settings').hide();
					}
				}
				pmpro_updateRecaptchaTRs();
			</script>
		</div> <!-- end pmpro_admin_section-other-settings -->		
		<p class="submit">
			<input name="savesettings" type="submit" class="button button-primary" value="<?php _e('Save Settings', 'paid-memberships-pro' );?>" />
		</p>
	</form>

<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");
?>
