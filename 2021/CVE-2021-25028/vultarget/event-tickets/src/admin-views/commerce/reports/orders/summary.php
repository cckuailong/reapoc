<?php

use \TEC\Tickets\Commerce\Status\Completed;
use \TEC\Tickets\Commerce\Status\Pending;
use TEC\Tickets\Commerce\Utils\Price;

?>
<div id="tribe-order-summary" class="welcome-panel tribe-report-panel">
	<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column welcome-panel-first">
				<h3><?php
					echo esc_html(
						sprintf(
							_x( '%s Details', 'post type details', 'event-tickets' ),
							$post_singular_label
						)
					); ?>
				</h3>
				<ul>
					<?php
					/**
					 * Provides an action that allows for the injections of fields at the top of the order report details meta ul
					 *
					 * @since 4.7
					 *
					 * @var $post_id
					 */
					do_action( 'tribe_tickets_report_event_details_list_top', $post_id );

					/**
					 * Provides an action that allows for the injections of fields at the bottom of the order report details ul
					 *
					 * @since 4.7
					 *
					 * @var $event_id
					 */
					do_action( 'tribe_tickets_report_event_details_list_bottom', $post_id );
					?>
				</ul>

				<?php
				/**
				 * Fires after the event details list (in the context of the  Orders Report admin view).
				 *
				 * @since 4.7
				 *
				 * @param WP_Post      $post
				 * @param bool|WP_User $author
				 */
				do_action( 'tribe_tickets_after_event_details_list', $post );
				?>

			</div>
			<div class="welcome-panel-column welcome-panel-middle">
				<h3>
					<?php
					echo esc_html(
						sprintf(
							__( 'Sales by %s Type', 'event-tickets' ),
							tribe_get_ticket_label_singular( 'sales_by_type' )
						)
					);
					?>
					<?php echo $tooltip->render_tooltip( esc_html__( 'Sold counts tickets from completed orders only.', 'event-tickets' ) ); ?>
				</h3>
				<ul>
					<?php
					/**
					 * @todo @juanfra We need to determine what counts as "sale" we have all the statuses here, I am currently only using
					 *       pending and completed, but we need to make sure user stories here.
					 * @todo @juanfra Raw HTML here, we need to modify the styling and add some classes.
					 */
					foreach ( $tickets as $ticket ) :
						$data = $tickets_data[ $ticket->ID ];
						$total = Price::total( [
							$data['total_by_status'][ Completed::SLUG ],
							$data['total_by_status'][ Pending::SLUG ]
						] );
						$total = str_replace( $thousands_sep, '', $total );
						$ticket_sales = sprintf(
							'%1$s: %2$s (%3$s)',
							$ticket->name,
							tribe_format_currency( number_format( $total, 2 ), $post_id ), // Price
							$data['qty_by_status'][ Completed::SLUG ] + $data['qty_by_status'][ Pending::SLUG ]
						);
						?>
						<li>
							<?php echo esc_html( $ticket_sales ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="welcome-panel-column welcome-panel-last alternate">
				<div class="totals-header">
					<h3>
						<?php
						$text_total_sales = sprintf(
							esc_html__( 'Total %s Sales', 'event-tickets' ),
							tribe_get_ticket_label_singular( 'total_sales' )
						);

						$totals_header = sprintf(
							'%1$s: %2$s (%3$s)',
							$text_total_sales,
							tribe_format_currency( number_format( $event_data['total_by_status'][ Completed::SLUG ], 2 ), $post_id ), // Price
							$event_data['qty_by_status'][ Completed::SLUG ]
						);
						echo esc_html( $totals_header );
						echo $tooltip->render_tooltip( sprintf(
							esc_html__( 'Total Sales counts %s from all completed orders.', 'event-tickets' ),
							tribe_get_ticket_label_plural_lowercase( 'total_sales' )
						) );
						?>
					</h3>

					<div class="order-total">
						<?php
						$text_total_ordered = sprintf(
							esc_html__( 'Total %s Ordered', 'event-tickets' ),
							tribe_get_ticket_label_plural( 'total_ordered' )
						);

						$total         = Price::total( [
							$event_data['total_by_status'][ Completed::SLUG ],
							$event_data['total_by_status'][ Pending::SLUG ]
						] );
						$total         = str_replace( $thousands_sep, '', $total );
						$totals_header = sprintf(
							'%1$s: %2$s (%3$s)',
							$text_total_ordered,
							tribe_format_currency( number_format( $total, 2 ), $post_id ),
							$event_data['qty_by_status'][ Completed::SLUG ] + $event_data['qty_by_status'][ Pending::SLUG ]
						);
						echo esc_html( $totals_header );
						echo $tooltip->render_tooltip( esc_html__( 'Total Ordered counts tickets from orders of any status, including pending and refunded.', 'event-tickets' ) );
						?>
					</div>
				</div>

				<ul id="sales_breakdown_wrapper" class="tribe-event-meta-note">
					<?php
					// Loop on all status to get items
					foreach ( $event_data['qty_by_status'] as $status_slug => $quantity ) :
						$status = tribe( \TEC\Tickets\Commerce\Status\Status_Handler::class )->get_by_slug( $status_slug );
						$total = $event_data['total_by_status'][ $status_slug ];
						// do not show status if no tickets
						if ( 0 >= (int) $quantity ) {
							continue;
						}
						?>
						<li>
							<strong><?php echo esc_html( $status->get_name() ) ?>:</strong>
							<?php echo esc_html( tribe_format_currency( number_format( $total, 2 ), $post_id ) ); ?>
							<span id="total_issued">(<?php echo esc_html( $quantity ); ?>)</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>