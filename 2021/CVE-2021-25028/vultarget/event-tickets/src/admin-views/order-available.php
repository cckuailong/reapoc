<?php
/**
 * @var array $available
 */
?>

<p>
	<?php echo esc_html__( 'Availability for this ticket type is counted using', 'event-tickets' ) . ','; ?>
	<br>
	<?php echo esc_html( array_search( min( $available ), $available ) . ' - ' . min( $available ) ); ?>
</p>
<p>
	<a href="https://evnt.is/1aek" target="_blank"><?php esc_html_e( 'Learn more about how Availability is calculated.', 'event-tickets' ); ?></a>
</p>
