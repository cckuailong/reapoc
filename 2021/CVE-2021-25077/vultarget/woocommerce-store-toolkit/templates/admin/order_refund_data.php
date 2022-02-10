<?php
if( !empty( $refunds ) ) {
	echo '<table class="widefat striped" style="font-family:monospace; text-align:left; width:100%;">';
	echo '<tbody>';

	foreach( $refunds as $refund ) {

		echo '<tr>';
		echo '<th colspan="3">';
		echo 'refund_id: ' . $refund->ID;
		echo '<br />';
		echo 'refund_name: ' . $refund->post_title;
		echo '<br />';
		echo 'refund_status: ' . $refund->post_status;
		echo '</th>';
		echo '</tr>';

		if( !empty( $refund->meta ) ) {
			foreach( $refund->meta as $meta_key => $meta_value ) {

				echo '<tr>';
				echo '<th style="width:20%;">&raquo; ' . $meta_key . '</th>';
				echo '<td>';
				echo $meta_value[0];
				echo '</td>';
				echo '<td class="actions">';
				do_action( 'woo_st_order_refund_data_actions', $post->ID, $meta_key );
				echo '</td>';
				echo '</tr>';

			}
		}

	}
	echo '</tbody>';
	echo '</table>';

} else {
	echo '<p>';
	_e( 'No refund items are associated with this Order.', 'woocommerce-store-toolkit' );
	echo '</p>';
}