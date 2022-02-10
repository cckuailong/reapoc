<div id="tribe-attendees-summary" class="welcome-panel tribe-report-panel">
	<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">

			<?php
			/**
			 * Fires before the individual panels within the attendee screen summary
			 * are rendered.
			 *
			 * @since 5.2.0
			 *
			 * @param int $post_id
			 */
			do_action( 'tribe_events_tickets_attendees_event_details_top', $post_id );
			?>

			<div class="welcome-panel-column welcome-panel-first">
				<h3>
					<?php
					echo esc_html(
							sprintf(
									_x( '%s Details', 'attendee screen summary', 'event-tickets' ),
									$post_singular_label
							)
					);
					?>
				</h3>

				<ul>
					<?php
					/**
					 * Provides an action that allows for the injections of fields at the top of the event details meta
					 * ul
					 *
					 * @since 5.2.0
					 *
					 * @var $post_id
					 */
					do_action( 'tribe_tickets_attendees_event_details_list_top', $post_id );

					/**
					 * Provides an action that allows for the injections of fields at the bottom of the event details
					 * meta ul
					 *
					 * @since 5.2.0
					 *
					 * @var $post_id
					 */
					do_action( 'tribe_tickets_attendees_event_details_list_bottom', $post_id );
					?>
				</ul>
				<?php
				/**
				 * Provides an opportunity for various action links to be added below
				 * the event name, within the attendee screen.
				 *
				 * @since 5.2.0
				 *
				 * @param int $post_id
				 */
				do_action( 'tribe_tickets_attendees_do_event_action_links', $post_id );

				/**
				 * Provides an opportunity for various action links to be added below
				 * the action links
				 *
				 * @since 5.2.0
				 *
				 * @param int $post_id
				 */
				do_action( 'tribe_events_tickets_attendees_event_details_bottom', $post_id );
				?>

			</div>
			<div class="welcome-panel-column welcome-panel-middle">
				<h3><?php echo esc_html_x( 'Overview', 'attendee screen summary', 'event-tickets' ); ?></h3>
				<?php do_action( 'tribe_events_tickets_attendees_ticket_sales_top', $post_id ); ?>

				<ul>
					<?php
					/** @var Tribe__Tickets__Ticket_Object $ticket */
					foreach ( $tickets as $ticket ) {
						$ticket_name = sprintf( '%s [#%d]', $ticket->name, $ticket->ID );
						?>
						<li>
							<strong><?php echo esc_html( $ticket_name ); ?>:&nbsp;</strong>
							<?php
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
				<?php do_action( 'tribe_events_tickets_attendees_ticket_sales_bottom', $post_id ); ?>
			</div>
			<div class="welcome-panel-column welcome-panel-last alternate">
				<?php
				/**
				 * Fires before the main body of attendee totals are rendered.
				 *
				 * @since 5.2.0
				 *
				 * @param int $post_id
				 */
				do_action( 'tribe_events_tickets_attendees_totals_top', $post_id );

				/**
				 * Trigger for the creation of attendee totals within the attendee
				 * screen summary box.
				 *
				 * @since 5.2.0
				 *
				 * @param int $post_id
				 */
				do_action( 'tribe_tickets_attendees_totals', $post_id );

				/**
				 * Fires after the main body of attendee totals are rendered.
				 *
				 * @since 5.2.0
				 *
				 * @param int $post_id
				 */
				do_action( 'tribe_events_tickets_attendees_totals_bottom', $post_id );
				?>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'tribe_events_tickets_attendees_event_summary_table_after', $post_id ); ?>
