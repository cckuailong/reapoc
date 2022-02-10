<?php
/** @var Tribe__Tickets__Attendees $attendees */
$attendees = tribe( 'tickets.attendees' );

$attendees->attendees_table->prepare_items();

$event_id = $attendees->attendees_table->event->ID;
$event    = $attendees->attendees_table->event;
$tickets  = Tribe__Tickets__Tickets::get_event_tickets( $event_id );
$pto      = get_post_type_object( $event->post_type );
$singular = $pto->labels->singular_name;

/**
 * Whether we should display the "Attendees for: %s" title.
 *
 * @since  4.6.2
 * @since  4.12.1 Append the post ID to the Attendees page title and each Ticket's name.
 * @since  5.0.1 Change default to the result of `is_admin()`.
 *
 * @param boolean                   $show_title Whether to show the title.
 * @param Tribe__Tickets__Attendees $attendees  The attendees object.
 */
$show_title = apply_filters( 'tribe_tickets_attendees_show_title', is_admin(), $attendees );
$export_url = tribe( 'tickets.attendees' )->get_export_url();
?>

<div class="wrap tribe-report-page">
	<?php if ( $show_title ) : ?>
		<h1>
			<?php
			echo esc_html(
				sprintf(
					// Translators: %1$s: the post/event title, %2$d: the post/event ID.
					_x( 'Attendees for: %1$s [#%2$d]', 'attendees report screen heading', 'event-tickets' ),
					get_the_title( $event ),
					$event_id
				)
			);
			/**
			 * Add an action to render content after text title.
			 *
			 * @since 5.1.0
			 * @since 5.1.7 Added the attendees information.
			 *
			 * @param int $event_id Post ID.
			 * @param Tribe__Tickets__Attendees $attendees The attendees object.
			 */
			do_action( 'tribe_report_page_after_text_label', $event_id, $attendees );

			?>
		</h1>
	<?php endif; ?>
	<div id="tribe-attendees-summary" class="welcome-panel tribe-report-panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container">

				<?php
				/**
				 * Fires before the individual panels within the attendee screen summary
				 * are rendered.
				 *
				 * @param int $event_id
				 */
				do_action( 'tribe_events_tickets_attendees_event_details_top', $event_id );
				?>

				<div class="welcome-panel-column welcome-panel-first">
					<h3><?php
						echo esc_html(
							sprintf(
								_x( '%s Details', 'attendee screen summary', 'event-tickets' ),
								$singular
							)
						); ?>
					</h3>

					<ul>
						<?php
						/**
						 * Provides an action that allows for the injections of fields at the top of the event details meta ul
						 *
						 * @var $event_id
						 */
						do_action( 'tribe_tickets_attendees_event_details_list_top', $event_id );

						/**
						 * Provides an action that allows for the injections of fields at the bottom of the event details meta ul
						 *
						 * @var $event_id
						 */
						do_action( 'tribe_tickets_attendees_event_details_list_bottom', $event_id );
						?>
					</ul>
					<?php
					/**
					 * Provides an opportunity for various action links to be added below
					 * the event name, within the attendee screen.
					 *
					 * @param int $event_id
					 */
					do_action( 'tribe_tickets_attendees_do_event_action_links', $event_id );

					/**
					 * Provides an opportunity for various action links to be added below
					 * the action links
					 *
					 * @param int $event_id
					 */
					do_action( 'tribe_events_tickets_attendees_event_details_bottom', $event_id ); ?>

				</div>
				<div class="welcome-panel-column welcome-panel-middle">
					<h3><?php echo esc_html_x( 'Overview', 'attendee screen summary', 'event-tickets' ); ?></h3>
					<?php do_action( 'tribe_events_tickets_attendees_ticket_sales_top', $event_id ); ?>

					<ul>
						<?php
						/** @var Tribe__Tickets__Ticket_Object $ticket */
						foreach ( $tickets as $ticket ) {
							$ticket_name = sprintf( '%s [#%d]', $ticket->name, $ticket->ID );
							?>
							<li>
								<strong><?php echo esc_html( $ticket_name ) ?>:&nbsp;</strong><?php
								echo esc_html( tribe_tickets_get_ticket_stock_message( $ticket ) );

								/**
								 * Adds an entry point to inject additional info for ticket.
								 *
								 * @since 5.0.3
								 */
								$this->set( 'ticket_item_for_overview', $ticket );
								$this->do_entry_point( 'overview_section_after_ticket_name' );
								?>
							</li>
						<?php } ?>
					</ul>
					<?php do_action( 'tribe_events_tickets_attendees_ticket_sales_bottom', $event_id ); ?>
				</div>
				<div class="welcome-panel-column welcome-panel-last alternate">
					<?php
					/**
					 * Fires before the main body of attendee totals are rendered.
					 *
					 * @param int $event_id
					 */
					do_action( 'tribe_events_tickets_attendees_totals_top', $event_id );

					/**
					 * Trigger for the creation of attendee totals within the attendee
					 * screen summary box.
					 *
					 * @param int $event_id
					 */
					do_action( 'tribe_tickets_attendees_totals', $event_id );

					/**
					 * Fires after the main body of attendee totals are rendered.
					 *
					 * @param int $event_id
					 */
					do_action( 'tribe_events_tickets_attendees_totals_bottom', $event_id );
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action( 'tribe_events_tickets_attendees_event_summary_table_after', $event_id ); ?>

	<form id="event-tickets__attendees-admin-form" class="topics-filter event-tickets__attendees-admin-form" method="post">
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'page' : 'tribe[page]' ); ?>" value="<?php echo esc_attr( isset( $_GET['page'] ) ? $_GET['page'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'event_id' : 'tribe[event_id]' ); ?>" id="event_id" value="<?php echo esc_attr( $event_id ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'post_type' : 'tribe[post_type]' ); ?>" value="<?php echo esc_attr( $event->post_type ); ?>" />
		<?php $attendees->attendees_table->search_box( __( 'Search attendees', 'event-tickets' ), 'attendees-search' ); ?>
		<?php $attendees->attendees_table->display(); ?>
	</form>
</div>
