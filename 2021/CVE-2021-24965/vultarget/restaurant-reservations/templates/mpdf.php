<style>

	.hd {
		width: 100%;
		border-bottom: 1px solid #777;
		padding: 5px 0;
		font-size: 10pt;
		color: #777;
	}

	.ft {
		width: 100%;
		border-top: 1px solid #777;
		padding: 5px 0;
		font-size: 10pt;
		color: #777;
		text-align: right;
	}

	.booking-wrapper {
		width: 100%;
		page-break-inside: avoid;
	}

	.hd-date {
		text-align: right;
	}

	.date td {
		padding: 20px 0 0;
	}

	h1 {
		font-size: 10pt;
	}

	.booking {
		width: 100%;
		border: 1px solid #777;
		margin: 10px 0;
	}

	.head {
		background: #eee;
	}

	.head td,
	.details td {
		padding: 10px;
	}

	.head .name {
		width: 60%;
	}

	.head .time,
	.head .status {
		width: 20%;
		text-align: right;
	}

	.details td {
		font-size: 9pt;
		line-height: 135%;
		width: 100%;
	}

	.label {
		font-weight: bold;
	}

	.time-val {
		margin: 10px;
	}

	.status {
		font-size: 9pt;
		padding: 10px;
	}

	.pending {
		color: #f00;
		margin: 10px;
	}

	.closed {
		color: #777;
	}

</style>

<!-- This defines the header and footers that appear on each page -->
<htmlpageheader name="Header">
	<table class="hd" cellspacing="0" cellpadding="0">
		<tr>
			<td class="hd-title"><?php
				if ( !empty( $this->query_args['location'] ) ) {
					$term = get_term( $this->query_args['location'] );
					if ( is_a( $term, 'WP_Term' ) ) {
						echo $term->name;
					}
				} else {
					bloginfo( 'sitename' );
				}
			?></td>
			<td class="hd-date"><?php echo $this->get_date_phrase(); ?></td>
		</tr>
	</table>
</htmlpageheader>
<htmlpagefooter name="Footer">
	<div class="ft">{PAGENO}</div>
</htmlpagefooter>
<sethtmlpageheader name="Header" page="O" value="on" show-this-page="1" />
<sethtmlpageheader name="Header" page="E" value="on" />
<sethtmlpagefooter name="Footer" page="O" value="on" show-this-page="1" />
<sethtmlpagefooter name="Footer" page="E" value="on" />

<!-- Now the list of bookings begins -->
<div class="bookings">

	<?php foreach( $bookings as $booking ) : ?>
	<?php $booking_date = apply_filters( 'get_the_date', mysql2date( get_option( 'date_format' ), $booking->date ) ); ?>

	<table class="booking-wrapper" cellspacing="0" cellpadding="0">

		<?php // Display the date if we've hit a new day ?>
		<?php // putting it under the table keeps it from being split in a page break ?>
		<?php if ( !isset( $current_date ) || $booking_date !== $current_date ) : ?>
		<tr class="date"><td><h1><?php echo $booking_date; ?></h1></td></tr>
		<?php $current_date = $booking_date; ?>
		<?php endif; ?>

		<tr>
			<td>
				<table class="booking" cellspacing="0" cellpadding="0">
					<tr class="head">
						<td class="name">
							<?php echo $booking->name; ?>
							(<?php echo $booking->party; ?>)
						</td>
						<td class="status <?php echo esc_attr( $booking->post_status ); ?>">
							<?php echo $rtb_controller->cpts->booking_statuses[ $booking->post_status ]['label']; ?>
						</td>
						<td class="time">
							<?php echo apply_filters( 'get_the_date', mysql2date( get_option( 'time_format' ), $booking->date ) ); ?>
						</td>
					</tr>
					<tr class="details">
						<td colspan="3">
							<?php do_action( 'ebfrtb_mpdf_before_details', $booking ); ?>
							<?php
								global $rtb_controller;
								if ( empty( $this->query_args['location'] ) && !empty( $booking->location ) ) {
									$term = get_term( $booking->location );
									if ( is_a( $term, 'WP_Term' ) ) :
										?>
										<p class="location">
											<span class="label"><?php esc_html_e( 'Location: ', 'export-bookings-for-rtb' ); ?></span>
											<?php echo $term->name; ?>
										</p>
										<?php
									endif;
								}
							?>

							<p class="email">
								<span class="label"><?php esc_html_e( 'Email: ', 'export-bookings-for-rtb' ); ?></span>
								<?php echo $booking->email; ?>
							</p>

							<?php if ( !empty( $booking->phone ) ) : ?>
							<p class="phone">
								<span class="label"><?php esc_html_e( 'Phone: ', 'export-bookings-for-rtb' ); ?></span>
								<?php echo $booking->phone; ?>
							</p>
							<?php endif; ?>

							<?php if ( !empty( $booking->message ) ) : ?>
							<p class="message">
								<span class="label"><?php esc_html_e( 'Message: ', 'export-bookings-for-rtb' ); ?></span>
								<?php echo $booking->message; ?>
							</p>
							<?php endif; ?>
							<?php do_action( 'ebfrtb_mpdf_after_details', $booking ); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<?php endforeach; ?>

</div>
