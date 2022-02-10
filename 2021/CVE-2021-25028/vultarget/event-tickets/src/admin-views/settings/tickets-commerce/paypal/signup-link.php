<?php
$countries             = tribe( \TEC\Tickets\Commerce\Gateways\PayPal\Location\Country::class )->get_list();
$default_country_code  = \TEC\Tickets\Commerce\Gateways\PayPal\Location\Country::DEFAULT_COUNTRY_CODE;
$selected_country_code = $country_code;
if ( empty( $selected_country_code ) ) {
	$selected_country_code = $default_country_code;
}
?>
<div
	class="tec-tickets__admin-settings-tickets-commerce-paypal-signup-settings"
>
	<p
		class="tec-tickets__admin-settings-tickets-commerce-paypal-merchant-country-container"
	>
		<select
			name='tec-tickets-commerce-gateway-paypal-merchant-country'
			class="tribe-dropdown"
			data-prevent-clear
			data-dropdown-css-width="false"
			style="width: 100%; max-width: 340px;"
			data-placeholder="<?php esc_attr_e( 'Select your country of operation', 'event-tickets' ); ?>"
		>
			<?php foreach ( $countries as $country_code => $country_label ) : ?>
				<option
					value="<?php echo esc_attr( $country_code ); ?>"
					<?php selected( $country_code === $selected_country_code ); ?>
				>
					<?php echo esc_html( $country_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>

	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connect-button">
		<a
			target="_blank"
			data-paypal-onboard-complete="tecTicketsCommerceGatewayPayPalSignupCallback"
			href="<?php echo esc_url( $url ) ?>&displayMode=minibrowser"
			data-paypal-button="true"
			id="connect_to_paypal"
			class="tec-tickets__admin-settings-tickets-commerce-paypal-connect-button-link"
		>
			<?php echo wp_kses( __( 'Connect Automatically with <i>PayPal</i>', 'event-tickets' ), 'post' ); ?>
		</a>
	</div>
</div>
