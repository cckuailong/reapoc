<?php
/**
 * Event Tickets Privacy
 *
 * @since 4.10.9 Use customizable ticket name functions.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="tribe-tickets-privacy">

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'Hello,', 'event-tickets' ); ?></p>
	<p class="privacy-policy-tutorial"><?php esc_html_e( 'This information serves as a guide on what sections need to be modified due to usage of Event Tickets and its Add-ons.', 'event-tickets' ); ?></p>
	<p class="privacy-policy-tutorial"><?php esc_html_e( 'You should include the information below in the correct sections of you privacy policy.', 'event-tickets' ); ?></p>
	<p class="privacy-policy-tutorial"><strong> <?php esc_html_e( 'Disclaimer:', 'event-tickets' ); ?></strong> <?php esc_html_e( 'This information is only for guidance and not to be considered as legal advice.', 'event-tickets' ); ?></p>

	<h2><?php esc_html_e( 'What personal data we collect and why we collect it', 'event-tickets' ); ?></h2>

	<h3><?php esc_html_e( 'Event, Attendee, and Ticket Purchaser Information', 'event-tickets' ); ?></h3>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'Through the usage of Event Tickets, Event Tickets Plus, and Community Tickets, information may be collected and stored within your website’s database.', 'event-tickets' ); ?></p>
	<p class="privacy-policy-tutorial"><strong><?php esc_html_e( 'Suggested text:', 'event-tickets' ); ?></strong></p>
	<p><?php echo esc_html(
			sprintf(
				__( 'If you create, submit, import, save, or publish event %1$s information, as well as obtain %2$s or purchase %3$s to events, such information is retained in the local database:', 'event-tickets' ),
				tribe_get_ticket_label_singular_lowercase( basename( __FILE__ ) ),
				tribe_get_rsvp_label_plural( basename( __FILE__ ) ),
				tribe_get_ticket_label_plural_lowercase( basename( __FILE__ ) )
			)
		); ?></p>

	<ol>
		<li><?php echo esc_html(
				sprintf(
					__( 'Attendees information (%1$s and %2$s): name and email address', 'event-tickets' ),
					tribe_get_rsvp_label_plural( basename( __FILE__ ) ),
					tribe_get_ticket_label_plural( basename( __FILE__ ) )
				)
			); ?></li>
		<li><?php echo esc_html(
				sprintf(
					__( '%1$s information (%2$s and %3$s): name, email address, and %4$s number/SKU (via check-in page)', 'event-tickets' ),
					tribe_get_ticket_label_singular( basename( __FILE__ ) ),
					tribe_get_rsvp_label_plural( basename( __FILE__ ) ),
					tribe_get_ticket_label_plural( basename( __FILE__ ) ),
					tribe_get_ticket_label_singular_lowercase( basename( __FILE__ ) )
				)
			); ?></li>
		<li><?php
			echo esc_html(
				sprintf(
					__( '%s purchaser information: name and email address', 'event-tickets' ),
					tribe_get_ticket_label_singular( basename( __FILE__ ) )
				)
			); ?>
		</li>
		<li><?php
			echo esc_html(
				sprintf(
					__( '%s purchaser billing address, which is collected through the use of WooCommerce, Easy Digital Downloads, or PayPal', 'event-tickets' ),
					tribe_get_ticket_label_singular( basename( __FILE__ ) )
				)
			); ?>
		</li>
	</ol>

	<p><?php
		echo esc_html(
			sprintf(
				__( 'Please note: The website owner can collect nearly any Attendee Information requested from %s buyers by creating a custom registration form.', 'event-tickets' ),
				tribe_get_ticket_label_singular_lowercase( basename( __FILE__ ) )
			)
		); ?>
	</p>

	<h3><?php esc_html_e( 'API Keys', 'event-tickets' ); ?></h3>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'Event Tickets suite offers the use of third-party API keys. The primary functions are to enhance the features we\'ve built in, some of which use Google Maps and PayPal. These API keys are not supplied by Modern Tribe.', 'event-tickets' ); ?></p>

	<p class="privacy-policy-tutorial"><strong><?php esc_html_e( 'Suggested text:', 'event-tickets' ); ?></strong></p>

	<p><?php esc_html_e( 'We make use of certain API keys, in order to provide specific features.', 'event-tickets' ); ?></p>

	<p><?php esc_html_e( 'These API keys may include the following third party services: Google Maps and PayPal.', 'event-tickets' ); ?></p>

	<h3 class="privacy-policy-tutorial"><?php esc_html_e( 'How Long You Retain this Data', 'event-tickets' ); ?></h3>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'All information (data) is retained in the local database indefinitely, unless otherwise deleted.', 'event-tickets' ); ?></p>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'Certain data may be exported or removed upon users request via the existing Exporter or Eraser. Please note, however, that several "edge cases" exist in which we are unable to perfect the gathering and export of all data for your end users. We suggest running a search in your local database, as well as within the WordPress Dashboard, in order to identify all data collected and stored for your specific user requests.', 'event-tickets' ); ?></p>

	<h3 class="privacy-policy-tutorial"><?php esc_html_e( 'Where We Send Your Data', 'event-tickets' ); ?></h3>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'Modern Tribe does not send any user data outside of your website by default.', 'event-tickets' ); ?></p>

	<p class="privacy-policy-tutorial"><?php esc_html_e( 'If you have extended our plugin(s) to send data to a third-party service such as Eventbrite, Google Maps, or PayPal, user information may be passed to these external services. These services may be located abroad.', 'event-tickets' ); ?></p>

</div>
