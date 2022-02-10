<?php
/**
 * The Template for displaying the Tickets Commerce PayPal modal content when connected.
 *
 * @version 5.2.1
 *
 * @since   5.2.1
 */

?>
<div class="tec-tickets__admin-settings-tickets-commerce-paypal-modal-content tec-tickets__admin-modal tribe-common-b2">

	<?php $this->template( 'settings/tickets-commerce/paypal/modal/signup-complete/notice-test-mode' ); ?>

	<p>
		<?php
		printf(
			// Translators: %1$s: opening `a` tag to the knowledge base article. %2$s: closing `a` tag to the knowledge base article.
			esc_html__( 'PayPal allows you to accept credit or debit cards directly on your website. Because of this, your site needs to maintain %1$sPCI-DSS compliance%2$s.', 'event-tickets' ),
			'<a href="https://theeventscalendar.com/knowledgebase/k/pci-compliance/" target="_blank" rel="noopener noreferrer" class="tribe-common-anchor-alt">',
			'</a>'
		);
		?>
	</p>

	<p><?php esc_html_e( 'Event Tickets never stores sensitive information like card details to your server and works seamlessly with SSL certificates.', 'event-tickets' ); ?></p>

	<p><?php esc_html_e( 'Compliance is comprised of, but not limited to:', 'event-tickets' ); ?></p>

	<ul class="tec-tickets__admin-modal-list">
		<li class="tec-tickets__admin-modal-list-item tribe-common-b2">
			<?php esc_html_e( 'Using a trusted, secure hosting provider &mdash; preferably one which claims and actively promotes PCI compliance.', 'event-tickets' ); ?>
		</li>
		<li class="tec-tickets__admin-modal-list-item tribe-common-b2">
			<?php esc_html_e( 'Maintain security best practices when setting passwords and limit access to your server.', 'event-tickets' ); ?>
		</li>
		<li class="tec-tickets__admin-modal-list-item tribe-common-b2">
			<?php esc_html_e( 'Implement an SSL certificate to keep your ticket sales secure.', 'event-tickets' ); ?>
		</li>
		<li class="tec-tickets__admin-modal-list-item tribe-common-b2">
			<?php esc_html_e( 'Keep WordPress and plugins up to date to ensure latest security fixes are present.', 'event-tickets' ); ?>
		</li>
	</ul>

	<div class="tec-tickets__admin-modal-buttons">

		<button
			data-js="a11y-close-button"
			class="tribe-common-c-btn tribe-common-b1 tribe-common-b2--min-medium tribe-modal__close-button"
			type="button"
			aria-label="<?php esc_attr_e( 'Close this modal window', 'event-tickets' ); ?>"
		>
			<?php esc_html_e( 'Got it, thanks!', 'event-tickets' ); ?>
		</button>

	</div>

</div>

