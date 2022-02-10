<?php

function link_library_process_ajax_tracker( $my_link_library_plugin ) {

	if ( !current_user_can('administrator') ) {
		check_ajax_referer( 'll_tracker' );

		$link_id = intval( $_POST['id'] );

		$link_data = get_post( $link_id );
		if ( !empty( $link_data ) ) {
			$link_visits = intval( get_post_meta( $link_id, 'link_visits', true ) );
			$updated_visits = $link_visits + 1;

			update_post_meta( $link_id, 'link_visits', $updated_visits );
		}
	}

    exit;
}

?>
