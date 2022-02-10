<?php
/**
 * This template is used for emails informing users of a change in tickets
 * (where they have been reassigned to another ticket type and/or to another
 * event entirely.
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/tickets/email-tickets-moved.php
 *
 * @var int    $original_event_id
 * @var string $original_event_name
 * @var int    $new_event_id
 * @var string $new_event_name
 * @var int    $ticket_type_id
 * @var string $ticket_type_name
 * @var array  $affected_tickets
 *
 * @version 4.5.1
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title><?php esc_html_e( 'Your tickets', 'event-tickets' ); ?></title>
	<meta name="viewport" content="width=device-width" />
	<style type="text/css">
		h1, h2, h3, h4, h5, h6 {
			color : #0a0a0e;
		}

		a, img {
			border  : 0;
			outline : 0;
		}

		#outlook a {
			padding : 0;
		}

		.ReadMsgBody, .ExternalClass {
			width : 100%
		}

		.yshortcuts, a .yshortcuts, a .yshortcuts:hover, a .yshortcuts:active, a .yshortcuts:focus {
			background-color : transparent !important;
			border           : none !important;
			color            : inherit !important;
		}

		body {
			background  : #ffffff;
			min-height  : 1000px;
			font-family : sans-serif;
			font-size   : 14px;
		}

		@media only screen and (max-width: 480px) {
			body, table, td, p, a, li, blockquote {
				-webkit-text-size-adjust : none !important;
			}

			body {
				width     : 100% !important;
				min-width : 100% !important;
			}

			@media print {
				.ticket-break {
					page-break-before : always !important;
				}
			}

		<?php
		/**
		 * Provides an opportunity to add further styles to the email created
		 * via the tickets/email-tickets-moved.php template.
 		 */
		do_action( 'tribe_tickets_moved_tickets_email_styles' );
		?>

	</style>
</head>
<body yahoo="fix" alink="#006caa" link="#006caa" text="#000000" bgcolor="#ffffff" style="width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0 auto; padding:20px 0 0 0; background:#ffffff; min-height:1000px;">
	<div style="margin:0; padding:0; width:100% !important; font-family: 'Helvetica Neue', Helvetica, sans-serif; font-size:14px; line-height:145%; text-align:left;">
		<center>
			<?php
			/**
			 * Fires before the main content is rendered, within the
			 * tickets/email-tickets-moved.php template.
			 *
			 * @param int $ticket_type_id
			 */
			do_action( 'tribe_tickets_moved_tickets_email_top', $ticket_type_id );
			?>
		</center>

		<h1><?php esc_html_e( 'Important changes to your tickets', 'event-tickets' ); ?></h1>

		<p>
			<?php
			$message = _n(
				'We wanted to let you know that a ticket you purchased for %2$s has been transferred to %3$s%4$s. Your ticket remains valid and no further action is needed on your part:',
				'We wanted to let you know that the following %1$s tickets for %2$s have been transferred to %3$s%4$s. Your existing tickets remain valid and no further action is needed on your part:',
				count( $affected_tickets ),
				'event-tickets'
			);

			$original_event = '<a href="' . esc_url( get_permalink( $original_event_id ) ) . '">' . esc_html( $original_event_name ) . '</a>';
			$new_event = '<a href="' . esc_url( get_permalink( $new_event_id ) ) . '">' . esc_html( $new_event_name ) . '</a>';

			$start_date = tribe_get_start_date( $new_event_id );
			$new_event_date = '';

			if ( $start_date ) {
				$new_event_date = sprintf( __( ' (taking place on %s)', 'event-tickets' ), $start_date );
			}

			printf( $message, count( $affected_tickets ), $original_event, $new_event, $new_event_date ); ?>
		</p>

		<ul> <?php foreach ( $affected_tickets as $attendee ): ?>
			<li>
				<tt> #<?php echo esc_html( $attendee[ 'attendee_id' ] ); ?> </tt>
				<strong> <?php echo esc_html( $attendee[ 'ticket' ] ); ?> </strong>
				&ndash; <?php echo esc_html( $attendee[ 'security' ] ); ?>
			</li>
		<?php endforeach; ?> </ul>

		<?php
		/**
		 * Fires after the main content is rendered, within the
		 * tickets/email-tickets-moved.php template.
		 *
		 * @param int $ticket_type_id
		 */
		do_action( 'tribe_tickets_moved_ticket_email_bottom', $ticket_type_id );
		?>
	</div>
</body>
</html>
