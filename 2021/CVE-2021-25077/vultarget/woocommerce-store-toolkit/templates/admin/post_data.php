<?php
if( !empty( $post_meta ) ) {

	echo '<table class="widefat striped" style="font-family:monospace; text-align:left; width:100%;">';
	echo '<tbody>';

	foreach( $post_meta as $meta_name => $meta_value ) {

		if(
			is_array( $meta_value ) && 
			count( $meta_value ) == 1 && 
			isset( $meta_value[0] )
		) {
			$meta_value = maybe_unserialize( $meta_value[0] );
		} else {
			$meta_value = maybe_unserialize( $meta_value );
		}

		if(
			is_array( $meta_value ) || 
			is_object( $meta_value )
		) {

			echo '<tr>';
			echo '<th colspan="3">' . $meta_name . '</th>';
			echo '</tr>';

			foreach( $meta_value as $inner_meta_name => $inner_meta_value ) {

				$inner_meta_value = maybe_unserialize( $inner_meta_value );

				if(
					is_array( $inner_meta_value ) || 
					is_object( $inner_meta_value )
				) {

					echo '<tr>';
					echo '<th colspan="3">&raquo; ' . $inner_meta_name . '</th>';
					echo '</tr>';
					foreach( $inner_meta_value as $inner_meta_name => $inner_meta_value ) {
						echo '<tr>';
						echo '<th style="width:20%;">&raquo; &raquo; ' . $inner_meta_name . '</th>';
						echo '<td>' . ( is_array( $inner_meta_value ) || is_object( $inner_meta_value ) ? print_r( $inner_meta_value, true ) : $inner_meta_value ) . '</td>';
						echo '<td>&nbsp;</td>';
						echo '</tr>';
					}

				} else {

					echo '<tr>';
					echo '<th style="width:20%;">&raquo; ' . $inner_meta_name . '</th>';
					echo '<td>' . ( is_array( $inner_meta_value ) || is_object( $inner_meta_value ) ? print_r( $inner_meta_value, true ) : $inner_meta_value ) . '</td>';
					echo '<td>&nbsp;</td>';
					echo '</tr>';

				}

			}

		} else {

			echo '<tr>';
			echo '<th style="width:20%;">' . $meta_name . '</th>';
			echo '<td>' . ( is_array( $meta_value ) || is_object( $meta_value ) ? print_r( $meta_value, true ) : $meta_value ) . '</td>';
			echo '<td class="actions">';
			do_action( sprintf( 'woo_st_%s_data_actions', $type ), $post->ID, $meta_name );
			echo '</td>';
			echo '</tr>';

		}

	}

	echo '</tbody>';
	echo '</table>';

} else {
	echo '<p>';
	_e( 'No custom Post meta is associated with this Post.', 'woocommerce-store-toolkit' );
	echo '</p>';
}