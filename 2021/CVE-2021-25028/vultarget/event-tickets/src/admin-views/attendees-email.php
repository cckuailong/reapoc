<?php

$users_args = [
	'name'             => 'email_to_user',
	'id'               => 'email_to_user',
	'show_option_none' => esc_html__( 'Select...', 'event-tickets' ),
	'selected'         => '',
];

if ( ! current_user_can( 'list_users' ) ) {
	$users_args['include'] = get_current_user_id();
}

/**
 * Filters the args for the Email Users Dropdown menu
 *
 * @see wp_dropdown_users()
 * @since 4.5.2
 *
 * @param array $users_args Args that get passed to wp_dropdown_users()
 */
$users_args = apply_filters( 'tribe_event_tickets_email_attendee_list_dropdown', $users_args );
$referer    = ! empty( $_SERVER['HTTP_REFERER'] ) ? "action='" . esc_url( $_SERVER['HTTP_REFERER'] ) . "'" : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

?><div id="tribe-loading"><span></span></div>
<form method="POST" class="tribe-attendees-email" <?php echo $referer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<div id="plugin-information-title">
		<?php esc_html_e( 'Send the attendee list by email', 'event-tickets' ); ?>
	</div>

	<div id="attendees_email_wrapper">
		<?php wp_nonce_field( 'email-attendees-list' ); ?>
		<label for="email_to_user">
			<span><?php esc_html_e( 'Select a User:', 'event-tickets' ); ?></span>
			<?php wp_dropdown_users( $users_args ); ?>
		</label>
		<span class="attendees_or"><?php esc_html_e( 'or', 'event-tickets' ); ?></span>
		<label for="email_to_address">
			<span><?php esc_html_e( 'Email Address:', 'event-tickets' ); ?></span>
			<input type="text" name="email_to_address" id="email_to_address" value="">
		</label>
		<input type="hidden" value="email" name="action" />
	</div>
		<div id="plugin-information-footer">
		<?php
		if ( false !== $status ) {
			echo '<div class="tribe-attendees-email-message ' . ( is_wp_error( $status ) ? 'error ' : 'updated ' ) . 'notice is-dismissible">';
			if ( is_wp_error( $status ) ) {
				echo '<ul>';
				foreach ( $status->errors as $key => $error ) {
					echo '<li>' . wp_kses( reset( $error ), [] ) . '</li>';
				}
				echo '</ul>';
			} else {
				echo '<p>' . wp_kses( $status, [] ) . '</p>';
			}
			echo '</div>';
		}
		?>

		<?php echo '<button type="submit" class="button button-primary right" name="tribe-send-email" value="1">' . esc_html__( 'Send Email', 'event-tickets' ) . '</button>'; ?>
	</div>
</form>
