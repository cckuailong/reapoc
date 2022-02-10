<?php
/**
 * @var string $total_sold_label
 * @var string $total_complete_label
 * @var string $total_cancelled_label
 * @var string $total_sold
 * @var string $total_complete
 * @var string $total_cancelled
 * @var string $total_sold_tooltip
 * @var string $total_completed_tooltip
 * @var string $total_cancelled_tooltip
 */
?>

<ul>
	<li> <strong><?php esc_html_e( $total_sold_label ); ?></strong> <?php esc_html_e( $total_sold ); ?> <?php echo wp_kses_post( $total_sold_tooltip ); ?> </li>
	<li> <?php esc_html_e( $total_complete_label . ' ' . $total_complete ); ?> <?php echo wp_kses_post( $total_completed_tooltip ); ?> </li>
	<li> <?php esc_html_e( $total_cancelled_label . ' ' . $total_cancelled ); ?> </li>
</ul>
