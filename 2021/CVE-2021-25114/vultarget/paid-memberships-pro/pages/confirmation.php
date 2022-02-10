<?php
/**
 * Template: Confirmation
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */
?>
<div class="<?php echo pmpro_get_element_class( 'pmpro_confirmation_wrap' ); ?>">
<?php
	global $wpdb, $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt;

	if($pmpro_msg)
	{
	?>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo wp_kses_post( $pmpro_msg );?></div>
	<?php
	}

	if(empty($current_user->membership_level))
		$confirmation_message = "<p>" . __('Your payment has been submitted. Your membership will be activated shortly.', 'paid-memberships-pro' ) . "</p>";
	else
		$confirmation_message = "<p>" . sprintf(__('Thank you for your membership to %s. Your %s membership is now active.', 'paid-memberships-pro' ), get_bloginfo("name"), $current_user->membership_level->name) . "</p>";

	//confirmation message for this level
	$level_message = $wpdb->get_var("SELECT l.confirmation FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.status = 'active' AND mu.user_id = '" . $current_user->ID . "' LIMIT 1");
	if(!empty($level_message))
		$confirmation_message .= "\n" . stripslashes($level_message) . "\n";
?>

<?php if(!empty($pmpro_invoice) && !empty($pmpro_invoice->id)) { ?>

	<?php
		$pmpro_invoice->getUser();
		$pmpro_invoice->getMembershipLevel();

		$confirmation_message .= "<p>" . sprintf(__('Below are details about your membership account and a receipt for your initial membership invoice. A welcome email with a copy of your initial membership invoice has been sent to %s.', 'paid-memberships-pro' ), $pmpro_invoice->user->user_email) . "</p>";

		// Check instructions
		if ( $pmpro_invoice->gateway == "check" && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
			$confirmation_message .= '<div class="' . pmpro_get_element_class( 'pmpro_payment_instructions' ) . '">' . wpautop( wp_unslash( pmpro_getOption("instructions") ) ) . '</div>';
		}

		/**
		 * All devs to filter the confirmation message.
		 * We also have a function in includes/filters.php that applies the the_content filters to this message.
		 * @param string $confirmation_message The confirmation message.
		 * @param object $pmpro_invoice The PMPro Invoice/Order object.
		 */
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, $pmpro_invoice);

		echo wp_kses_post( $confirmation_message );
	?>
	<h3>
		<?php printf(__('Invoice #%s on %s', 'paid-memberships-pro' ), $pmpro_invoice->code, date_i18n(get_option('date_format'), $pmpro_invoice->getTimestamp()));?>
	</h3>
	<a class="<?php echo pmpro_get_element_class( 'pmpro_a-print' ); ?>" href="javascript:window.print()"><?php _e('Print', 'paid-memberships-pro' );?></a>
	<ul>
		<?php do_action("pmpro_invoice_bullets_top", $pmpro_invoice); ?>
		<li><strong><?php _e('Account', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $current_user->display_name );?> (<?php echo esc_html( $current_user->user_email );?>)</li>
		<li><strong><?php _e('Membership Level', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $current_user->membership_level->name);?></li>
		<?php if($current_user->membership_level->enddate) { ?>
			<li><strong><?php _e('Membership Expires', 'paid-memberships-pro' );?>:</strong> <?php echo date_i18n(get_option('date_format'), $current_user->membership_level->enddate)?></li>
		<?php } ?>
		<?php if($pmpro_invoice->getDiscountCode()) { ?>
			<li><strong><?php _e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $pmpro_invoice->discount_code->code );?></li>
		<?php } ?>
		<?php do_action("pmpro_invoice_bullets_bottom", $pmpro_invoice); ?>
	</ul>
	<hr />
	<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice_details' ); ?>">
		<?php if(!empty($pmpro_invoice->billing->street)) { ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-billing-address' ); ?>">
				<strong><?php _e('Billing Address', 'paid-memberships-pro' );?></strong>
				<p>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_name' ); ?>"><?php echo $pmpro_invoice->billing->name; ?></span>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_street' ); ?>"><?php echo $pmpro_invoice->billing->street; ?></span>
					<?php if ( $pmpro_invoice->billing->city && $pmpro_invoice->billing->state ) { ?>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_city' ); ?>"><?php echo $pmpro_invoice->billing->city; ?></span>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_state' ); ?>"><?php echo $pmpro_invoice->billing->state; ?></span>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_zip' ); ?>"><?php echo $pmpro_invoice->billing->zip; ?></span>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_country' ); ?>"><?php echo $pmpro_invoice->billing->country; ?></span>
					<?php } ?>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_phone' ); ?>"><?php echo formatPhone($pmpro_invoice->billing->phone); ?></span>
				</p>
			</div> <!-- end pmpro_invoice-billing-address -->
		<?php } ?>

		<?php if ( ! empty( $pmpro_invoice->accountnumber ) || ! empty( $pmpro_invoice->payment_type ) ) { ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-payment-method' ); ?>">
				<strong><?php _e('Payment Method', 'paid-memberships-pro' );?></strong>
				<?php if($pmpro_invoice->accountnumber) { ?>
					<p><?php echo esc_html( ucwords( $pmpro_invoice->cardtype ) ); ?> <?php _e('ending in', 'paid-memberships-pro' );?> <?php echo esc_html( last4($pmpro_invoice->accountnumber ) );?>
					<br />
					<?php _e('Expiration', 'paid-memberships-pro' );?>: <?php echo esc_html( $pmpro_invoice->expirationmonth );?>/<?php echo esc_html( $pmpro_invoice->expirationyear );?></p>
				<?php } else { ?>
					<p><?php echo esc_html( $pmpro_invoice->payment_type ); ?></p>
				<?php } ?>
			</div> <!-- end pmpro_invoice-payment-method -->
		<?php } ?>

		<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-total' ); ?>">
			<strong><?php _e('Total Billed', 'paid-memberships-pro' );?></strong>
			<p>
				<?php
					if ( (float)$pmpro_invoice->total > 0 ) {
						echo pmpro_get_price_parts( $pmpro_invoice, 'span' );
					} else {
						echo pmpro_escape_price( pmpro_formatPrice(0) );
					}
				?>
			</p>
		</div> <!-- end pmpro_invoice-total -->

	</div> <!-- end pmpro_invoice -->
	<hr />
<?php
	}
	else
	{
		$confirmation_message .= "<p>" . sprintf(__('Below are details about your membership account. A welcome email has been sent to %s.', 'paid-memberships-pro' ), $current_user->user_email) . "</p>";

		/**
		 * All devs to filter the confirmation message.
		 * Documented above.
		 * We also have a function in includes/filters.php that applies the the_content filters to this message.
		 */
		$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, false);

		echo wp_kses_post( $confirmation_message );
	?>
	<ul>
		<li><strong><?php _e('Account', 'paid-memberships-pro' );?>:</strong> <?php echo esc_html( $current_user->display_name );?> (<?php echo esc_html( $current_user->user_email );?>)</li>
		<li><strong><?php _e('Membership Level', 'paid-memberships-pro' );?>:</strong> <?php if(!empty($current_user->membership_level)) echo esc_html( $current_user->membership_level->name ); else _e("Pending", 'paid-memberships-pro' );?></li>
		<?php if( !empty( $current_user->membership_level->expiration_period ) && $current_user->membership_level->expiration_period == 'Hour' && apply_filters( 'pmpro_confirmation_display_hour_expiration', true, $current_user ) ){ ?>
		<li><strong><?php _e('Expires In', 'paid-memberships-pro' );?>:</strong> <?php echo $current_user->membership_level->expiration_number .' '.pmpro_translate_billing_period( $current_user->membership_level->expiration_period, $current_user->membership_level->expiration_number ); ?></li>
		<?php }
		?>
	</ul>
<?php
	}
?>
<p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<?php if ( ! empty( $current_user->membership_level ) ) { ?>
		<a href="<?php echo pmpro_url( 'account' ); ?>"><?php _e( 'View Your Membership Account &rarr;', 'paid-memberships-pro' ); ?></a>
	<?php } else { ?>
		<?php _e( 'If your account is not activated within a few minutes, please contact the site owner.', 'paid-memberships-pro' ); ?>
	<?php } ?>
</p> <!-- end pmpro_actions_nav -->
</div> <!-- end pmpro_confirmation_wrap -->
