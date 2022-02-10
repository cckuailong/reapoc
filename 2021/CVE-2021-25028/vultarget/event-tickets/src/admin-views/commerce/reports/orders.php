<?php
/**
 * @var int     $post_id             The current post ID.
 * @var WP_Post $post                The current post object.
 * @var string  $post_singular_label The post type singular label.
 * @var int     $total_sold          The total number of tickets sold.
 * @var array   $tickets_sold        A list of PayPal tickets that have at least one sale.
 * @var Orders  $orders_table        The orders table output.
 */

use \TEC\Tickets\Commerce\Admin_Tables\Orders;

?>

<div class="wrap tribe-report-page">
	<?php if ( ! empty( $title ) ) : ?>
		<h1><?php echo esc_html( $title ); ?></h1>
	<?php endif; ?>
	<div id="icon-edit" class="icon32 icon32-tickets-orders"><br></div>

	<?php $this->template( 'orders/summary' ); ?>

	<form id="topics-filter" method="get">
		<input
			type="hidden" name="<?php echo esc_attr( is_admin() ? 'page' : 'tribe[page]' ); ?>"
			value="<?php echo esc_attr( tribe_get_request_var( 'page' ) ); ?>"
		/>
		<input
			type="hidden" name="<?php echo esc_attr( is_admin() ? 'post_id' : 'tribe[event_id]' ); ?>"
			id="event_id"
			value="<?php echo esc_attr( $post_id ); ?>"
		/>
		<input
			type="hidden" name="<?php echo esc_attr( is_admin() ? 'post_type' : 'tribe[post_type]' ); ?>"
			value="<?php echo esc_attr( $post->post_type ); ?>"
		/>
		<?php
		$orders_table->search_box( __( 'Search Orders', 'event-tickets' ), 'tpp-orders' );
		$orders_table->display();
		?>
	</form>
</div>
