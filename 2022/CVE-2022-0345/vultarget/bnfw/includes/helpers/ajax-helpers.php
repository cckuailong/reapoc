<?php
/**
 * BNFW AJAX Helper functions.
 *
 * @since 1.4
 */

/**
 * BNFW Search User AJAX Handler.
 *
 * @since 1.3.6
 */
function bnfw_search_users() {
	global $wp_roles;

	$roles_data = array();
	$user_count = count_users();
	$roles = $wp_roles->get_names();
	foreach ( $roles as $role_slug => $role_name ) {
		$count = 0;
		if ( isset( $user_count['avail_roles'][ $role_slug ] ) ) {
			$count = $user_count['avail_roles'][ $role_slug ];
		}

		$roles_data[] = array(
			'id'   => 'role-' . $role_slug,
			'text' => $role_name . ' (' . $count . ' Users)',
		);
	}

	$data = array(
		array(
			'id'       => 1,
			'text'     => esc_html__( 'User Roles', 'bnfw' ),
			'children' => $roles_data,
		),
	);

	$query = sanitize_text_field( $_GET['query'] );
	$users = get_users( array(
		'order_by' => 'email',
		'search'   => "$query*",
		'number'   => 100,
		'fields'   => array( 'ID', 'user_login' ),
	) );

	$user_data = array();
	foreach ( $users as $user ) {
		$user_data[] = array(
			'id'   => $user->ID,
			'text' => $user->user_login,
		);
	}

	$data[] = array(
		'id'       => 2,
		'text'     => esc_html__( 'Users', 'bnfw' ),
		'children' => $user_data,
	);

	echo json_encode( $data );
	wp_die();
}
add_action( 'wp_ajax_bnfw_search_users', 'bnfw_search_users' );
