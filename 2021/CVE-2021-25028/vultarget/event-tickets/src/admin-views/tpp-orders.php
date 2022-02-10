<?php
/**
 * @var int                                              $post_id             The current post ID.
 * @var WP_Post                                          $post                The current post object.
 * @var WP_User                                          $author              The post author.
 * @var string                                           $post_singular_label The post type singular label.
 * @var int                                              $total_sold          The total number of tickets sold.
 * @var array                                            $tickets_sold        A list of PayPal tickets that have at least one sale.
 * @var Tribe__Tickets__Commerce__PayPal__Main           $paypal              The tickets handler object.
 * @var Tribe__Tickets__Commerce__PayPal__Status_Manager $order_overview      Order breakdown for all statuses.
 * @var string                                           $table               The orders table output.
 */

/**
 * Whether we should display order report title.
 *
 * @since  4.10.7
 *
 * @param boolean $show_title (false) Whether to show the title.
 */
$show_title = apply_filters( 'tribe_tickets_order_report_show_title', false );

/**
 * Whether we should display order report title for Tribe Commerce orders.
 *
 * @since  4.10.7
 *
 * @param boolean $show_title (false) Whether to show the title.
 */
$show_title = apply_filters( 'tribe_tickets_tpp_order_report_show_title', $show_title );

$title = __( 'Orders Report', 'event-tickets' );

/**
 * Allows filtering of the Tribe Commerce order report title
 *
 * @since  4.10.7
 *
 * @param string $title the title.
 */
$title = apply_filters( 'tribe_tickets_tpp_order_report_title', $title );

if ( $total_sold ) {
	$total_sold = '(' . $total_sold . ')';
}
?>

<div class="wrap tribe-report-page">
	<?php if ( $show_title ) : ?>
		<h1><?php echo esc_html( $title ); ?></h1>
	<?php endif; ?>
	<div id="icon-edit" class="icon32 icon32-tickets-orders"><br></div>

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
					do_action( 'tribe_tickets_after_event_details_list', $post, $author );
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
						<?php echo $order_overview->get_sale_by_ticket_tooltip(); ?>
					</h3>
					<?php
					foreach ( $tickets_sold as $ticket_sold ) {
						// Only display if a TPP Ticket, else kick out.
						if ( 'Tribe__Tickets__Commerce__PayPal__Main' != $ticket_sold['ticket']->provider_class ) {
							continue;
						}

						echo $order_overview->get_ticket_sale_infomation( $ticket_sold, $post_id );
					}
					?>
				</div>
				<div class="welcome-panel-column welcome-panel-last alternate">
					<div class="totals-header">
						<h3>
							<?php
							$completed_status = $order_overview->get_completed_status_class();

							$text_total_sales = sprintf(
								esc_html__( 'Total %s Sales', 'event-tickets' ),
								tribe_get_ticket_label_singular( 'total_sales' )
							);

							$totals_header = sprintf(
								'%1$s: %2$s (%3$s)',
								$text_total_sales,
								tribe_format_currency( number_format( $completed_status->get_line_total(), 2 ), $post_id ),
								$completed_status->get_qty()
							);
							echo esc_html( $totals_header );
							echo $order_overview->get_total_sale_tooltip();
							?>
						</h3>

						<div class="order-total">
							<?php
							$text_total_ordered = sprintf(
								esc_html__( 'Total %s Ordered', 'event-tickets' ),
								tribe_get_ticket_label_plural( 'total_ordered' )
							);

							$totals_header = sprintf(
								'%1$s: %2$s (%3$s)',
								$text_total_ordered,
								tribe_format_currency( number_format( $order_overview->get_line_total(), 2 ), $post_id ),
								$order_overview->get_qty()
							);
							echo esc_html( $totals_header );
							echo $order_overview->get_total_order_tooltip();
							?>
						</div>
					</div>

					<ul id="sales_breakdown_wrapper" class="tribe-event-meta-note">
						<?php
						/**
						 * Add Completed Status First and Skip in Loop
						 */
						?>
						<li>
							<strong><?php esc_html_e( 'Completed', 'event-tickets' ); ?>:</strong>
							<?php echo esc_html( tribe_format_currency( number_format( $completed_status->get_line_total(), 2 ), $post_id ) ); ?>
							<span id="total_issued">(<?php echo esc_html( $completed_status->get_qty() ); ?>)</span>
						</li>

						<?php
						foreach ( $order_overview->statuses as $provider_key => $status ) {
							// skip the completed order as we always display it above
							if ( $order_overview->completed_status_id === $provider_key ) {
								continue;
							}

							// do not show status if no tickets
							if ( 0 >= (int) $status->get_qty() ) {
								continue;
							}
							?>
							<li>
								<strong><?php esc_html_e( $status->name, 'event-tickets' ); ?>:</strong>
								<?php echo esc_html( tribe_format_currency( number_format( $status->get_line_total(), 2 ), $post_id ) ); ?>
								<span id="total_issued">(<?php echo esc_html( $status->get_qty() ); ?>)</span>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<form id="topics-filter" method="get">
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'page' : 'tribe[page]' ); ?>"
			   value="<?php echo esc_attr( isset( $_GET['page'] ) ? $_GET['page'] : '' ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'post_id' : 'tribe[event_id]' ); ?>" id="event_id"
			   value="<?php echo esc_attr( $post_id ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( is_admin() ? 'post_type' : 'tribe[post_type]' ); ?>"
			   value="<?php echo esc_attr( $post->post_type ); ?>" />
		<?php echo $table; ?>
	</form>
</div>
