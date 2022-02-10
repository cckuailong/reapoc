<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_emailsettings")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}	
	
	global $wpdb, $msg, $msgt;
	
	//get/set settings
	global $pmpro_pages;

	global $pmpro_email_templates_defaults, $current_user;	
				
	require_once(dirname(__FILE__) . "/admin_header.php");		
?>
<form action="" method="post" enctype="multipart/form-data"> 
	<?php wp_nonce_field('savesettings', 'pmpro_emailsettings_nonce');?>
	
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Email Templates', 'paid-memberships-pro' ); ?></h1>
	<hr class="wp-header-end">

	<p><?php esc_html_e( 'Select an email template from the dropdown below to customize the subject and body of emails sent through your membership site. You can also disable a specific email or send a test version through this admin page.', 'paid-memberships-pro' ); ?> <a href="https://www.paidmembershipspro.com/documentation/member-communications/list-of-pmpro-email-templates/" target="_blank"><?php esc_html_e( 'Click here for a description of each email sent to your members and admins at different stages of the member experience.', 'paid-memberships-pro'); ?></a></p>

	<div class="pmpro_admin_section pmpro_admin_section-email-templates-content">

		<table class="form-table">
			<tr class="status hide-while-loading" style="display:none;">
				<th scope="row" valign="top"></th>
				<td>
					<div id="message" class="status_message_wrapper">
						<p class="status_message"></p>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="pmpro_email_template_switcher"><?php esc_html_e( 'Email Template', 'paid-memberships-pro' ); ?></label>
				</th>
				<td>
					<select name="pmpro_email_template_switcher" id="pmpro_email_template_switcher">
						<option value="" selected="selected"><?php echo '--- ' . esc_html__( 'Select a Template to Edit', 'paid-memberships-pro' ) . ' ---'; ?></option>

					<?php foreach ( $pmpro_email_templates_defaults as $key => $template ): ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $template['description'] ); ?></option>

					<?php endforeach; ?>
					</select>
					<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="pmproet-spinner" style="display:none;"/>

					<p id="pmpro_email_template_help_text" class="description"></p>
				</td>
			</tr>
			<tr class="hide-while-loading">
				<th scope="row" valign="top"></th>
				<td>
					<label><input id="pmpro_email_template_disable" name="pmpro_email_template_disable" type="checkbox" /><span
							id="disable_label"><?php esc_html_e( 'Disable this email?', 'paid-memberships-pro' ); ?></span></label>


					<p id="disable_description" class="description"><?php esc_html_e( 'Emails with this template will not be sent.', 'paid-memberships-pro' ); ?></p>

				</td>
			</tr>
			<tr class="hide-while-loading">
				<th scope="row" valign="top"><label for="pmpro_email_template_subject"><?php esc_html_e( 'Subject', 'paid-memberships-pro' ); ?></label></th>

				<td>
					<input id="pmpro_email_template_subject" name="pmpro_email_template_subject" type="text" size="100"/>
				</td>
			</tr>
			<tr class="hide-while-loading">
				<th scope="row" valign="top"><label for="pmpro_email_template_body"><?php esc_html_e( 'Body', 'paid-memberships-pro' ); ?></label></th>

				<td>
					<div id="template_editor_container">
						<textarea rows="10" cols="80" name="pmpro_email_template_body" id="pmpro_email_template_body"></textarea>
					</div>
				</td>
			</tr>
			<tr class="hide-while-loading">
				<th scope="row" valign="top"></th>
				<td>
					<?php esc_html_e( 'Send a test email to ', 'paid-memberships-pro' ); ?>
					<input id="test_email_address" name="test_email_address" type="text"
						value="<?php echo esc_attr( $current_user->user_email ); ?>"/>
					<input id="send_test_email" class="button" name="send_test_email" value="<?php esc_attr_e( 'Save Template and Send Email', 'paid-memberships-pro' ); ?>"

						type="button"/>

					<p class="description">
						<?php esc_html_e( 'Your current membership will be used for any membership level data.', 'paid-memberships-pro' ); ?>
					</p>
				</td>
			</tr>
			<tr class="controls hide-while-loading">
				<th scope="row" valign="top"></th>
				<td>
					<p class="submit">
						<input id="pmpro_submit_template_data" name="pmpro_save_template" type="button" class="button-primary"
							value="<?php esc_attr_e( 'Save Template', 'paid-memberships-pro' ); ?>"/>

						<input id="pmpro_reset_template_data" name="pmpro_reset_template" type="button" class="button"
							value="<?php esc_attr_e( 'Reset Template', 'paid-memberships-pro' ); ?>"/>

					</p>
				</td>
			</tr>
		</table>

		<hr />

		<div class="pmpro-email-templates-variable-reference">
			<h1><?php esc_html_e( 'Variable Reference', 'paid-memberships-pro' ); ?></h1>

			<p><?php esc_html_e( 'Use the placeholder variables below to customize your member and admin emails with specific user or membership data.', 'paid-memberships-pro' ); ?></p>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><?php esc_html_e('General Settings / Membership Info', 'paid-memberships-pro'); ?></th>
					<td>
						<table class="widefat striped">
							<tbody>
								<?php
								$email_variables = [
									'!!name!!'                  => __( 'Display Name (Profile/Edit User > Display name publicly as)', 'paid-memberships-pro' ),
									'!!user_login!!'            => __( 'Username', 'paid-memberships-pro' ),
									'!!sitename!!'              => __( 'Site Title', 'paid-memberships-pro' ),
									'!!siteemail!!'             => __( 'Site Email Address (General Settings > Email OR Memberships > Settings > Email Settings)', 'paid-memberships-pro' ),
									'!!membership_id!!'         => __( 'Membership Level ID', 'paid-memberships-pro' ),
									'!!membership_level_name!!' => __( 'Membership Level Name', 'paid-memberships-pro' ),
									'!!membership_change!!'     => __( 'Membership Level Change', 'paid-memberships-pro' ),
									'!!membership_expiration!!' => __( 'Membership Level Expiration', 'paid-memberships-pro' ),
									'!!startdate!!'             => __( 'Membership Start Date', 'paid-memberships-pro' ),
									'!!enddate!!'               => __( 'Membership End Date', 'paid-memberships-pro' ),
									'!!display_name!!'          => __( 'Display Name (Profile/Edit User > Display name publicly as)', 'paid-memberships-pro' ),
									'!!user_email!!'            => __( 'User Email', 'paid-memberships-pro' ),
									'!!login_url!!'            => __( 'Login URL', 'paid-memberships-pro' ),
									'!!levels_url!!'           => __( 'Membership Levels Page URL', 'paid-memberships-pro' ),
								];
								
								foreach ( $email_variables as $email_variable => $description ) {
									?>
										<tr>
											<td><?php echo esc_html( $email_variable ); ?></td>
											<td><?php echo esc_html( $description ); ?></td>
										</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Billing Information', 'paid-memberships-pro' ); ?></th>
					<td>
						<table class="widefat striped">
							<tbody>
								<?php
								$email_variables = [
									'!!billing_address!!' => __( 'Billing Info Complete Address', 'paid-memberships-pro' ),
									'!!billing_name!!'    => __( 'Billing Info Name', 'paid-memberships-pro' ),
									'!!billing_street!!'  => __( 'Billing Info Street Address', 'paid-memberships-pro' ),
									'!!billing_city!!'    => __( 'Billing Info City', 'paid-memberships-pro' ),
									'!!billing_state!!'   => __( 'Billing Info State', 'paid-memberships-pro' ),
									'!!billing_zip!!'     => __( 'Billing Info ZIP Code', 'paid-memberships-pro' ),
									'!!billing_country!!' => __( 'Billing Info Country', 'paid-memberships-pro' ),
									'!!billing_phone!!'   => __( 'Billing Info Phone #', 'paid-memberships-pro' ),
									'!!cardtype!!'        => __( 'Credit Card Type', 'paid-memberships-pro' ),
									'!!accountnumber!!'   => __( 'Credit Card Number (last 4 digits)', 'paid-memberships-pro' ),
									'!!expirationmonth!!' => __( 'Credit Card Expiration Month (mm format)', 'paid-memberships-pro' ),
									'!!expirationyear!!'  => __( 'Credit Card Expiration Year (yyyy format)', 'paid-memberships-pro' ),
									'!!membership_cost!!' => __( 'Membership Level Cost Text', 'paid-memberships-pro' ),
									'!!instructions!!'    => __( 'Payment Instructions (used in Checkout - Email Template)', 'paid-memberships-pro' ),
									'!!invoice_id!!'      => __( 'Invoice ID', 'paid-memberships-pro' ),
									'!!invoice_total!!'   => __( 'Invoice Total', 'paid-memberships-pro' ),
									'!!invoice_date!!'    => __( 'Invoice Date', 'paid-memberships-pro' ),
									'!!invoice_url!!'    => __( 'Invoice Page URL', 'paid-memberships-pro' ),
									'!!discount_code!!'   => __( 'Discount Code Applied', 'paid-memberships-pro' ),
									'!!membership_level_confirmation_message!!' => __( 'Custom Level Confirmation Message', 'paid-memberships-pro' ),
									
								];

								foreach ( $email_variables as $email_variable => $description ) {
									?>
										<tr>
											<td><?php echo esc_html( $email_variable ); ?></td>
											<td><?php echo esc_html( $description ); ?></td>
										</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</div> <!-- end pmpro-email-templates-variable-reference -->

		<?php wp_nonce_field( 'pmproet', 'security' ); ?>

	</div> <!-- end pmpro_admin_section-email-templates-content -->
</form>
<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");
