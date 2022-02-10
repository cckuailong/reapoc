<style>

	.date {
		font-size: 12pt;
	}
	.booking {
		font-size: 12pt;
		line-height: 100%;
	}
	.time {
		width: 15%;
	}
	.party {
		width: 5%;
	}
	.name {
		width: 25%;
		font-size: 12pt;
	}
	.details {
		width: 55%;
		font-size: 10pt;
		line-height: 120%;
		color: #333;
	}

</style>

<div class="bookings">

	<?php foreach( $bookings as $booking ) : ?>
	<?php $booking_date = apply_filters( 'get_the_date', mysql2date( get_option( 'date_format' ), $booking->date ) ); ?>

	<?php // Display the date if we've hit a new day ?>
	<?php if ( !isset( $current_date ) || $booking_date !== $current_date ) : ?>
	<h1 class="date" style="vertical-align:middle;"><?php echo $booking_date; ?></h1>
	<?php $current_date = $booking_date; ?>
	<?php endif; ?>

	<table class="booking">
		<tr>
			<td class="time">
				<?php echo apply_filters( 'get_the_date', mysql2date( get_option( 'time_format' ), $booking->date ) ); ?>
			</td>
			<td class="party">
				<?php echo $booking->party; ?>
			</td>
			<td class="name">
				<?php echo $booking->name; ?>
			</td>
			<td class="details">
				<?php do_action( 'ebfrtb_tcpdf_before_details', $booking ); ?>
					<?php
						global $rtb_controller;
						if ( empty( $this->query_args['location'] ) && !empty( $booking->location ) ) {
							$term = get_term( $booking->location );
							if ( is_a( $term, 'WP_Term' ) ) :
								?>
								<div class="location">
									<?php echo $term->name; ?>
								</div>
								<?php
							endif;
						}
					?>
				<div class="email"><?php echo $booking->email; ?></div>

				<?php if ( !empty( $booking->phone ) ) : ?>
				<div class="phone"><?php echo $booking->phone; ?></div>
				<?php endif; ?>

				<?php if ( !empty( $booking->message ) ) : ?>
				<div class="message"><?php echo $booking->message; ?></div>
				<?php endif; ?>
				<?php do_action( 'ebfrtb_tcpdf_after_details', $booking ); ?>
			</td>
		</tr>
		<tr><td colspan="4"></td></tr>
	</table>

	<?php endforeach; ?>

</div>
