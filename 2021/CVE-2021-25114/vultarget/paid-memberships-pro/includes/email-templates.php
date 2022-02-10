<?php
/**
 * File used to setup default email templates data.
 */

 global $pmpro_email_templates_defaults;
 /**
 * Default email templates.
 */
$pmpro_email_templates_defaults = array(
	'default'                  => array(
		'subject'     => __( "An email from !!sitename!!", 'paid-memberships-pro' ),
		'description' => __( 'Default Email', 'paid-memberships-pro'),
		'body' => __( '!!body!!', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent when there is a general message that needs to be communicated to the site administrator.', 'paid-memberships-pro' )
	),
	'footer'                  => array(
		'subject'     => __( '', 'paid-memberships-pro' ),
		'description' => __( 'Email Footer', 'paid-memberships-pro'),
		'body' => __( '<p>
	Respectfully,<br />
	!!sitename!!
</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the closing message included in every email sent to members and the site administrator through Paid Memberships Pro.', 'paid-memberships-pro' )
	),
	'header'                  => array(
		'subject'     => __( '', 'paid-memberships-pro' ),
		'description' => __( 'Email Header', 'paid-memberships-pro'),
		'body' => __( '<p>Dear !!name!!,</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the opening message included in every email sent to members and the site administrator through Paid Memberships Pro.', 'paid-memberships-pro' )
	),
	'admin_change'             => array(
		'subject'     => __( "Your membership at !!sitename!! has been changed", 'paid-memberships-pro' ),
		'description' => __( 'Admin Change', 'paid-memberships-pro'),
		'body' => __( '<p>An administrator at !!sitename!! has changed your membership level.</p>
		
<p>!!membership_change!!</p>
		
<p>If you did not request this membership change and would like more information please contact us at !!siteemail!!</p>
		
<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'The site administrator can manually update a user\'s membership level through the WordPress admin. This email notifies the member of the level update.', 'paid-memberships-pro' )
	),
	'admin_change_admin'       => array(
		'subject'     => __( "Membership for !!user_login!! at !!sitename!! has been changed", 'paid-memberships-pro' ),
		'description' => __('Admin Change (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>An administrator at !!sitename!! has changed a membership level for !!name!!.</p>

<p>!!membership_change!!</p>

<p>Log in to your WordPress admin here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'The site administrator can manually update a user\'s membership level through the WordPress admin. This email notifies the site administrator of the level update.', 'paid-memberships-pro' )
	),
	'billable_invoice' => array(
		'subject' => __( 'Invoice for order #: !!order_code!!', 'paid-memberships-pro' ),
        'description' => __( 'Billable Invoice', 'paid-membershps-pro' ),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Below is your invoice for order #: !!order_code!!</p>

!!invoice!!

<p>Log in to your membership account here: !!login_url!!</p>

<p>To view an online version of this invoice, click here: !!invoice_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'The site administrator can manually send a user a copy of this invoice email via the Memberships > Orders admin page.', 'paid-memberships-pro' )
	),
	'billing'	=> array(
		'subject'     => __( "Your billing information has been updated at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Billing', 'paid-memberships-pro'),
		'body' => __( '<p>Your billing information at !!sitename!! has been changed.</p><p>Account: !!display_name!! (!!user_email!!)</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p><p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p><p>If you did not request a billing information change please contact us at !!siteemail!!</p><p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'Members can update the payment method associated with their recurring subscription. This email is sent to the member as a confirmation of a payment method update.', 'paid-memberships-pro' )
	),
	'billing_admin'            => array(
		'subject'     => __( "Billing information has been updated for !!user_login!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Billing (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>The billing information for !!display_name!! at !!sitename!! has been changed.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>
	Billing Information:<br />
	!!billing_name!!<br />
	!!billing_street!!<br />
	!!billing_city!!, !!billing_state!! !!billing_zip!!	!!billing_country!!
	!!billing_phone!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your WordPress dashboard here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'Members can update the payment method associated with their recurring subscription. This email is sent to the site administrator as a confirmation of a payment method update.', 'paid-memberships-pro' )
	),
	'billing_failure'          => array(
		'subject'     => __( "Membership payment failed at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Billing Failure', 'paid-memberships-pro'),
		'body' => __( '<p>The current subscription payment for your !!sitename!! membership has failed. <strong>Please click the following link to log in and update your billing information to avoid account suspension. !!login_url!!</strong></p>

<p>Account: !!display_name!! (!!user_email!!)</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent out if a recurring payment has failed, usually due to an expired or cancelled credit card. This email is sent to the member to allowing them time to update payment information without a disruption in access to your site.', 'paid-memberships-pro' )
	),
	'billing_failure_admin'    => array(
		'subject'     => __( "Membership payment failed for !!display_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Billing Failure (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>The subscription payment for !!user_login!! at !!sitename!! has failed.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>

<p>Log in to your WordPress admin here: !!login_url!!</p>
', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent out if a recurring payment has failed for a member, usually due to an expired or cancelled credit card. This email is sent to the site administrator.', 'paid-memberships-pro' )
	),
	'cancel'                   => array(
		'subject'     => __( "Your membership at !!sitename!! has been CANCELLED", 'paid-memberships-pro' ),
		'description' => __('Cancel', 'paid-memberships-pro'),
		'body' => __( '<p>Your membership at !!sitename!! has been cancelled.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>

<p>If you did not request this cancellation and would like more information please contact us at !!siteemail!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'The site administrator can manually cancel a user\'s membership through the WordPress admin or the member can cancel their own membership through your site. This email is sent to the member as confirmation of a cancelled membership.', 'paid-memberships-pro' )
	),
	'cancel_admin'             => array(
		'subject'     => __( "Membership for !!user_login!! at !!sitename!! has been CANCELLED", 'paid-memberships-pro' ),
		'description' => __('Cancel (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>The membership for !!user_login!! at !!sitename!! has been cancelled.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Start Date: !!startdate!!</p>
<p>End Date: !!enddate!!</p>

<p>Log in to your WordPress admin here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'The site administrator can manually cancel a user\'s membership through the WordPress admin or the member can cancel their own membership through your site. This email is sent to the site administrator as confirmation of a cancelled membership.', 'paid-memberships-pro' )
	),
	'checkout_check'           => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Check', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>

!!membership_level_confirmation_message!!

!!instructions!!

<p>Below are details about your membership account and a receipt for your initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level using the "Pay by Check" gateway.', 'paid-memberships-pro' )
	),
	'checkout_check_admin'     => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Check (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>

<p><strong>They have chosen to pay by check.</strong></p>

<p>Below are details about the new membership account and a receipt for the initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every membership checkout using the "Pay by Check" gateway.', 'paid-memberships-pro' )
	),
	'checkout_express'         => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - PayPal Express', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>
!!membership_level_confirmation_message!!
<p>Below are details about your membership account and a receipt for your initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>
', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level using the "PayPal Express" gateway.', 'paid-memberships-pro' )

	),
	'checkout_express_admin'   => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - PayPal Express (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>
<p>Below are details about the new membership account and a receipt for the initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>
', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every membership checkout using the "PayPal Express" gateway.', 'paid-memberships-pro' )
	),
	'checkout_free'            => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Free', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>
!!membership_level_confirmation_message!!
<p>Below are details about your membership account.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
!!membership_expiration!! !!discount_code!!

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level when the level has no charge.', 'paid-memberships-pro' )
	),
	'checkout_free_admin'      => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Free (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>
<p>Below are details about the new membership account.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
!!membership_expiration!! !!discount_code!!

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every membership checkout that has no charge.', 'paid-memberships-pro' )
	),
	'checkout_freetrial'       => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Free Trial', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>
!!membership_level_confirmation_message!!
<p>Below are details about your membership account.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Billing Information on File:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level with a trial period.', 'paid-memberships-pro' )
	),
	'checkout_freetrial_admin' => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Free Trial (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>
<p>Below are details about the new membership account and a receipt for the initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Billing Information on File:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every membership checkout with a trial period.', 'paid-memberships-pro' )
	),
	'checkout_paid'            => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Paid', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>
!!membership_level_confirmation_message!!
<p>Below are details about your membership account and a receipt for your initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level and complete a paid checkout on the site.', 'paid-memberships-pro' )
	),
	'checkout_paid_admin'      => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Paid (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>
<p>Below are details about the new membership account and a receipt for the initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every paid membership checkout on the site.', 'paid-memberships-pro' )
	),
	'checkout_trial'           => array(
		'subject'     => __( "Your membership confirmation for !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Trial', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your membership account is now active.</p>
!!membership_level_confirmation_message!!
<p>Below are details about your membership account and a receipt for your initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is a membership confirmation welcome email sent to a new member or to existing members that change their level with a trial period.', 'paid-memberships-pro' )
	),
	'checkout_trial_admin'     => array(
		'subject'     => __( "Member checkout for !!membership_level_name!! at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Checkout - Trial (admin)', 'paid-memberships-pro'),
		'body' => __( '<p>There was a new member checkout at !!sitename!!.</p>
<p>Below are details about the new membership account and a receipt for the initial membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>
<p>Membership Fee: !!membership_cost!!</p>
!!membership_expiration!! !!discount_code!!

<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This is the membership confirmation email sent to the site administrator for every membership checkout with a trial period.', 'paid-memberships-pro' )
	),
	'credit_card_expiring'     => array(
		'subject'     => __( "Credit card on file expiring soon at !!sitename!!", 'paid-memberships-pro' ),
		'description' => __('Credit Card Expiring', 'paid-memberships-pro'),
		'body' => __( '<p>The payment method used for your membership at !!sitename!! will expire soon. <strong>Please click the following link to log in and update your billing information to avoid account suspension. !!login_url!!</strong></p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>The most recent account information we have on file is:</p>

<p>!!billing_name!!</br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent when a member\'s payment method will be expiring soon. This allows the member to update their payment method before a payment failure, which may result in lost access to member features.', 'paid-memberships-pro' )
	),
	'invoice'                  => array(
		'subject'     => __( "Invoice for !!sitename!! membership", 'paid-memberships-pro' ),
		'description' => __('Invoice', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Below is a receipt for your most recent membership invoice.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>
	Invoice #!!invoice_id!! on !!invoice_date!!<br />
	Total Billed: !!invoice_total!!
</p>
<p>
	Billing Information:<br />
	!!billing_address!!
</p>

<p>
	!!cardtype!!: !!accountnumber!!<br />
	Expires: !!expirationmonth!!/!!expirationyear!!
</p>

<p>Log in to your membership account here: !!login_url!!</p>
<p>To view an online version of this invoice, click here: !!invoice_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the member each time a new subscription payment is made.', 'paid-memberships-pro' )
	),
	'membership_expired'       => array(
		'subject'     => __( "Your membership at !!sitename!! has ended", 'paid-memberships-pro' ),
		'description' => __('Membership Expired', 'paid-memberships-pro'),
		'body' => __( '<p>Your membership at !!sitename!! has ended.</p>

<p>Thank you for your support.</p>

<p>View our current membership offerings here: !!levels_url!!</p>

<p>Log in to manage your account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the member when their membership expires.', 'paid-memberships-pro' )
	),
	'membership_expiring'      => array(
		'subject'     => __( "Your membership at !!sitename!! will end soon", 'paid-memberships-pro' ),
		'description' => __('Membership Expiring', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. This is just a reminder that your membership will end on !!enddate!!.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the member when their expiration date is approaching, at an interval based on the term of the membership.', 'paid-memberships-pro' )
	),
	'trial_ending'             => array(
		'subject'     => __( "Your trial at !!sitename!! is ending soon", 'paid-memberships-pro' ),
		'description' => __('Trial Ending', 'paid-memberships-pro'),
		'body' => __( '<p>Thank you for your membership to !!sitename!!. Your trial period is ending on !!trial_end!!.</p>

<p>Account: !!display_name!! (!!user_email!!)</p>
<p>Membership Level: !!membership_level_name!!</p>

<p>Your fee will be changing from !!trial_amount!! to !!billing_amount!! every !!cycle_number!! !!cycle_period!!(s).</p>

<p>Log in to your membership account here: !!login_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the member when the trial portion of their membership level is approaching, at an interval based on the term of the trial.', 'paid-memberships-pro' )
	),

);

// add SCA payment action required emails if we're using PMPro 2.1 or later
if( defined( 'PMPRO_VERSION' ) && version_compare( PMPRO_VERSION, '2.1' ) >= 0 ) {
	$pmpro_email_templates_defaults = array_merge( $pmpro_email_templates_defaults, array(
		'payment_action'            => array(
			'subject'     => __( "Payment action required for your !!sitename!! membership", 'paid-memberships-pro' ),
			'description' => __('Payment Action Required', 'paid-memberships-pro' ),
			'body' => __( '<p>Customer authentication is required to finish setting up your subscription at !!sitename!!.</p>

<p>Please complete the verification steps issued by your payment provider at the following link:</p>
<p>!!invoice_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the user when an attempted membership checkout requires additional customer authentication.', 'paid-memberships-pro' )
		),
		'payment_action_admin'      => array(
			'subject'     => __( "Payment action required: membership for !!user_login!! at !!sitename!!", 'paid-memberships-pro' ),
			'description' => __('Payment Action Required (admin)', 'paid-memberships-pro'),
			'body' => __( '<p>A payment at !!sitename!! for !!user_login!! requires additional customer authentication to complete.</p>
<p>Below is a copy of the email we sent to !!user_email!! to notify them that they need to complete their payment:</p>

<p>Customer authentication is required to finish setting up your subscription at !!sitename!!.</p>

<p>Please complete the verification steps issued by your payment provider at the following link:</p>
<p>!!invoice_url!!</p>', 'paid-memberships-pro' ),
		'help_text' => __( 'This email is sent to the site administrator when an attempted membership checkout requires additional customer authentication.', 'paid-memberships-pro' )
		)
	));
}

/**
 * Filter default template settings and add new templates.
 *
 * @since 0.5.7
 */
$pmpro_email_templates_defaults = apply_filters( 'pmproet_templates', $pmpro_email_templates_defaults );
