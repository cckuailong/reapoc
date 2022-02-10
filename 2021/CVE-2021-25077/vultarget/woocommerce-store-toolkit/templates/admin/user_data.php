<?php
echo '<table class="form-table">';
echo '<tr>';
echo '<th>';
echo '<label>' . __( 'User Meta', 'woocommerce-store-toolkit' ) . '</label>';
echo '</th>';
echo '<td>';

echo '<table class="widefat page fixed user_data">';

echo '<thead>';
echo '<tr>';
echo '<th class="manage-column">' . __( 'Meta key', 'woocommerce-store-toolkit' ) . '</th>';
echo '<th class="manage-column">' . __( 'Meta value', 'woocommerce-store-toolkit' ) . '</th>';
echo '<th class="manage-column">' . __( 'Actions', 'woocommerce-store-toolkit' ) . '</th>';
echo '</tr>';
echo '</thead>';

echo '<tbody>';
if( !empty( $user_meta ) ) {
	foreach( $user_meta as $meta_name => $meta_value ) {

		if( count( maybe_unserialize( $meta_value ) ) == 1 )
			$meta_value = $meta_value[0];
		$meta_value = maybe_unserialize( $meta_value );

		echo '<tr>';
		if(
			is_array( $meta_value ) || 
			is_object( $meta_value )
		) {
			echo '<tr>';
			echo '<th colspan="3">' . $meta_name . '</th> ';
			echo '</tr>';
			foreach( $meta_value as $inner_meta_name => $inner_meta_value ) {
				echo '<tr>';
				echo '<th style="width:20%;">&raquo; ' . $inner_meta_name . '</th>';
				echo '<td>' . ( is_array( $inner_meta_value ) || is_object( $inner_meta_value ) ? print_r( $inner_meta_value, true ) : $inner_meta_value ) . '</td>';
				echo '<td>&nbsp;</td>';
				echo '</tr>';
			}
		} else {
			echo '<td>' . $meta_name . '</td>';
			echo '<td>' . ( is_array( $meta_value ) || is_object( $meta_value ) ? print_r( $meta_value, true ) : $meta_value ) . '</td>';
			echo '<td class="actions">';
			do_action( 'woo_st_user_data_actions', $user_id, $meta_name );
			echo '</td>';
		}
		echo '</tr>';

	}
} else {

	echo '<tr>';
	echo '<td colspan="3">' . __( 'No custom User meta is associated with this User.', 'woocommerce-store-toolkit' ) . '</td>';
	echo '</tr>';

}
echo '</tbody>';
echo '</table>';
echo '</td>';
echo '</tr>';
echo '</table>';