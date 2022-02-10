<?php
/**
 * The Template for displaying the Tickets Commerce PayPal Modal when connected.
 *
 * @version 5.2.1
 *
 * @since   5.2.1
 */

$request_vars = tribe_get_request_vars();

// Bail if we're not in the correct context, when PayPal was connected.
if ( empty( $request_vars['tc-status'] ) || 'paypal-signup-complete' !== $request_vars['tc-status'] ) {
	return;
}

$dialog_view = tribe( 'dialog.view' );
$content     = $this->template( 'settings/tickets-commerce/paypal/modal/signup-complete/content', [], false );

$args = [
	'append_target'           => '#paypal-connected-modal-target',
	'button_id'               => 'paypal-connected-modal-button',
	'content_wrapper_classes' => 'tribe-dialog__wrapper tribe-tickets__admin-container event-tickets tribe-common tribe-modal__wrapper--paypal-connected',
	'title'                   => esc_html__( "You are now connected to PayPal, here's what's next...", 'event-tickets' ),
	'title_classes'           => [
		'tribe-dialog__title',
		'tribe-modal__title',
		'tribe-common-h5',
		'tribe-modal__title--paypal-connected',
	],
];

ob_start();
$dialog_view->render_modal( $content, $args, 'paypal-connected-modal-id' );
$modal_content = ob_get_clean();

$modal  = '<div class="tribe-common event-tickets">';
$modal .= '<span id="' . esc_attr( 'paypal-connected-modal-target' ) . '"></span>';
$modal .= $modal_content;
$modal .= '</div>';

echo $modal; // phpcs:ignore
