<?php
/**
 * Email used to notify someone that their RSVP was received.
 *
 * This email is used on those occasions where the user noted
 * that they would *not* be attending. You may override this
 * template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/tickets/email-non-attendance.php
 *
 * @var int   $event_id
 * @var int   $order_id
 * @var array $attendees
 *
 * @version 4.7.4
 */

$event_date = null;

/**
 * Filters whether or not the event date should be included in the ticket email.
 *
 * @since 4.7.4
 *
 * @param bool Include event date? Defaults to true.
 * @param int  Event ID
 */
$include_event_date = apply_filters( 'tribe_tickets_email_include_event_date', true, $event_id );

if ( $include_event_date && function_exists( 'tribe_events_event_schedule_details' ) ) {
	$event_date = tribe_events_event_schedule_details( $event_id );
}

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

		.appleLinks a {
			color           : #006caa;
			text-decoration : underline;
		}

		@media only screen and (max-width: 480px) {
			body, table, td, p, a, li, blockquote {
				-webkit-text-size-adjust : none !important;
			}

			body {
				width     : 100% !important;
				min-width : 100% !important;
			}

			body[yahoo] h2 {
				line-height : 120% !important;
				font-size   : 28px !important;
				margin      : 15px 0 10px 0 !important;
			}

			table.content,
			table.wrapper,
			table.inner-wrapper {
				width : 100% !important;
			}

			table.ticket-content {
				width   : 90% !important;
				padding : 20px 0 !important;
			}

			table.ticket-details {
				position       : relative;
				padding-bottom : 100px !important;
			}

			table.ticket-break {
				width : 100% !important;
			}

			td.wrapper {
				width : 100% !important;
			}

			td.ticket-content {
				width : 100% !important;
			}

			td.ticket-image img {
				max-width : 100% !important;
				width     : 100% !important;
				height    : auto !important;
			}

			td.ticket-details {
				width         : 33% !important;
				padding-right : 10px !important;
				border-top    : 1px solid #ddd !important;
			}

			td.ticket-details h6 {
				margin-top : 20px !important;
			}

			td.ticket-details.new-row {
				width      : 50% !important;
				height     : 80px !important;
				border-top : 0 !important;
				position   : absolute !important;
				bottom     : 0 !important;
				display    : block !important;
			}

			td.ticket-details.new-left-row {
				left : 0 !important;
			}

			td.ticket-details.new-right-row {
				right : 0 !important;
			}

			table.ticket-venue {
				position       : relative !important;
				width          : 100% !important;
				padding-bottom : 150px !important;
			}

			td.ticket-venue,
			td.ticket-organizer,
			td.ticket-qr {
				width      : 100% !important;
				border-top : 1px solid #ddd !important;
			}

			td.ticket-venue h6,
			td.ticket-organizer h6 {
				margin-top : 20px !important;
			}

			td.ticket-qr {
				text-align : left !important
			}

			td.ticket-qr img {
				float      : none !important;
				margin-top : 20px !important
			}

			td.ticket-organizer,
			td.ticket-qr {
				position : absolute;
				display  : block;
				left     : 0;
				bottom   : 0;
			}

			td.ticket-organizer {
				bottom : 0px;
				height : 100px !important;
			}

			td.ticket-venue-child {
				width : 50% !important;
			}

			table.venue-details {
				position : relative !important;
				width    : 100% !important;
			}

			a[href^="tel"], a[href^="sms"] {
				text-decoration : none;
				color           : black;
				pointer-events  : none;
				cursor          : default;
			}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				text-decoration : default;
				color           : #006caa !important;
				pointer-events  : auto;
				cursor          : default;
			}
		}

		@media only screen and (max-width: 320px) {
			td.ticket-venue h6,
			td.ticket-organizer h6,
			td.ticket-details h6 {
				font-size : 12px !important;
			}
		}

		@media print {
			.ticket-break {
				page-break-before : always !important;
			}
		}

		<?php do_action( 'tribe_tickets_ticket_email_styles' );?>

	</style>
</head>
<body yahoo="fix" alink="#006caa" link="#006caa" text="#000000" bgcolor="#ffffff" style="width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0 auto; padding:20px 0 0 0; background:#ffffff; min-height:1000px;">
<div style="margin:0; padding:0; width:100% !important; font-family: 'Helvetica Neue', Helvetica, sans-serif; font-size:14px; line-height:145%; text-align:left;">
	<center>
		<?php
		/**
		 * Fires immediately before the main body of content within ticket emails
		 * is rendered.
		 */
		do_action( 'tribe_tickets_ticket_email_top' );
		?>

		<table class="content" align="center" width="620" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" style="margin:0 auto; padding:0;">
			<tr>
				<td align="center" valign="top" class="wrapper" width="620">
					<h2 style="color:#0a0a0e; margin:0 0 10px 0 !important; font-family: 'Helvetica Neue', Helvetica, sans-serif; font-style:normal; font-weight:700; font-size:28px; letter-spacing:normal; line-height: 100%; text-align:left;">
						<span style="color:#0a0a0e !important">
							<?php echo esc_html( get_the_title( $event_id ) ); ?>
						</span>
					</h2>

					<?php if ( ! empty( $event_date ) ) : ?>
						<h4 style="color:#0a0a0e; margin:0 !important; font-family: 'Helvetica Neue', Helvetica, sans-serif; font-style:normal; font-weight:700; font-size:15px; letter-spacing:normal; line-height: 100%; text-align:left;">
							<span style="color:#0a0a0e !important"><?php echo $event_date; ?></span>
						</h4>
					<?php endif; ?>

					<p style="text-align:left;">
						<?php _e( 'Thank you for confirming that you will not be attending the above event.', 'event-tickets' ); ?>
					</p>

					<p>
						<a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>"><?php echo esc_html( get_the_title( $event_id ) ); ?></a>
						<a href="<?php echo esc_url( get_home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
					</p>
				</td>
			</tr>
		</table>

		<?php
		/**
		 * Fires immediately after the main body of content within ticket emails
		 * is rendered.
		 */
		do_action( 'tribe_tickets_ticket_email_bottom' );
		?>

	</center>
</div>
</body>
</html>
