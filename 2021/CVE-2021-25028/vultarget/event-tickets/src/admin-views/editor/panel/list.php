<?php
/** @var Tribe__Tickets__Attendees $tickets_attendees */
$tickets_attendees = tribe( 'tickets.attendees' );

$attendees_url = $tickets_attendees->get_report_link( get_post( $post_id ) );

$total_tickets = tribe_get_event_capacity( $post_id );

$container_class = 'tribe_sectionheader ticket_list_container';
$container_class .= ( empty( $total_tickets ) ) ? ' tribe_no_capacity' : '';
$ticket_providing_modules = array_diff_key( Tribe__Tickets__Tickets::modules(), [ 'Tribe__Tickets__RSVP' => true ] );
$add_new_ticket_label = count( $ticket_providing_modules ) > 0
	? esc_attr__( 'Add a new ticket', 'event-tickets' )
	: esc_attr__( 'No commerce providers available', 'event-tickets' )
?>
<div
	id="tribe_panel_base"
	class="ticket_panel panel_base"
	aria-hidden="false"
	data-save-prompt="<?php echo esc_attr( __( 'You have unsaved changes to your tickets. Discard those changes?', 'event-tickets' ) ); ?>"
>
	<div class="<?php echo esc_attr( $container_class ); ?>">
		<?php if ( ! empty( $tickets ) ) : ?>
			<div class="ticket_table_intro">
				<?php
				/**
				 * Allows for the insertion of total capacity element into the main ticket admin panel "header".
				 *
				 * @since 4.6
				 *
				 * @param int $post_id Post ID.
				 */
				do_action( 'tribe_events_tickets_capacity', $post_id );

				/**
				 * Allows for the insertion of additional elements (buttons/links) into the main ticket admin panel "header".
				 *
				 * @since 4.6
				 *
				 * @param int $post_id Post ID.
				 */
				do_action( 'tribe_events_tickets_post_capacity', $post_id );
				?>
				<a
					class="button-secondary"
					href="<?php echo esc_url( $attendees_url ); ?>"
				>
					<?php esc_html_e( 'View Attendees', 'event-tickets' ); ?>
				</a>
			</div>
			<?php
			/** @var Tribe__Tickets__Admin__Views $admin_views */
			$admin_views = tribe( 'tickets.admin.views' );

			$admin_views->template( 'editor/list-table', [ 'tickets' => $tickets ] );
			?>
		<?php endif; ?>
	</div>
	<div class="tribe-ticket-control-wrap">
		<?php
		/**
		 * Allows for the insertion of additional content into the main ticket admin panel after the tickets listing.
		 *
		 * @since 4.6
		 *
		 * @param int $post_id Post ID.
		 */
		do_action( 'tribe_events_tickets_new_ticket_buttons', $post_id ); ?>

		<button
			id="ticket_form_toggle"
			class="button-secondary ticket_form_toggle tribe-button-icon tribe-button-icon-plus"
			aria-label="<?php echo $add_new_ticket_label ?>"
			"<?php echo disabled( count( $ticket_providing_modules ) === 0 ) ?>"
		>
		<?php
		echo esc_html(
			sprintf(
				_x( 'New %s', 'admin editor panel list button label', 'event-tickets' ),
				tribe_get_ticket_label_singular_lowercase( 'admin_editor_panel_list_button_label' )
			)
		); ?>
		</button>

		<button
			id="rsvp_form_toggle"
			class="button-secondary ticket_form_toggle tribe-button-icon tribe-button-icon-plus"
			aria-label="<?php echo esc_attr( sprintf( _x( 'Add a new %s', 'RSVP form toggle button label', 'event-tickets' ), tribe_get_rsvp_label_singular( 'rsvp_form_toggle_button_label' ) ) ); ?>"
		>
			<?php
			echo esc_html(
				sprintf(
					_x( 'New %s', 'RSVP form toggle button text', 'event-tickets' ),
					tribe_get_rsvp_label_singular( 'rsvp_form_toggle_button_text' )
				)
			); ?>
		</button>


		<button id="settings_form_toggle" class="button-secondary tribe-button-icon tribe-button-icon-settings">
			<?php esc_html_e( 'Settings', 'event-tickets' ); ?>
		</button>

		<?php
		/**
		 * Allows for the insertion of warnings before the settings button.
		 *
		 * @since 4.6
		 *
		 * @param int Post ID.
		 */
		do_action( 'tribe_events_tickets_new_ticket_warnings', $post_id );
		?>

	</div>
	<?php
	/**
	 * Allows for the insertion of content at the end of the new ticket admin panel.
	 *
	 * @since 4.6
	 *
	 * @param int Post ID.
	 */
	do_action( 'tribe_events_tickets_after_new_ticket_panel', $post_id );
	?>

</div>