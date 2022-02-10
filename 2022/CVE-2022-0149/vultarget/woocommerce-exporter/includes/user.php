<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_user_count' ) ) {
		function woo_ce_get_export_type_user_count() {

			$count = 0;
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_user_count' );
			if( $cached == false ) {
				if( $users = count_users() )
					$count = ( isset( $users['total_users'] ) ? $users['total_users'] : 0 );
				set_transient( WOO_CE_PREFIX . '_user_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}
			return $count;

		}
	}

	/* End of: WordPress Administration */

}

// Returns a list of User export columns
function woo_ce_get_user_fields( $format = 'full' ) {

	$export_type = 'user';

	$fields = array();
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Username', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_role',
		'label' => __( 'User Role', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'first_name',
		'label' => __( 'First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'last_name',
		'label' => __( 'Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'full_name',
		'label' => __( 'Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'nick_name',
		'label' => __( 'Nickname', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'email',
		'label' => __( 'E-mail', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'orders',
		'label' => __( 'Orders', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'money_spent',
		'label' => __( 'Money Spent', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'url',
		'label' => __( 'Website', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'date_registered',
		'label' => __( 'Date Registered', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Biographical Info', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'aim',
		'label' => __( 'AIM', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'yim',
		'label' => __( 'Yahoo IM', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'jabber',
		'label' => __( 'Jabber / Google Talk', 'woocommerce-exporter' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( sprintf( WOO_CE_PREFIX . '_%s_fields', $export_type ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	$remember = woo_ce_get_option( $export_type . '_fields', array() );
	if( !empty( $remember ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
			// If not found turn off default
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

// Check if we should override field labels from the Field Editor
function woo_ce_override_user_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'user_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_user_fields', 'woo_ce_override_user_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_user_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_user_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}

// Returns a list of User IDs
function woo_ce_get_users( $args = array() ) {

	global $export;

	$limit_volume = 0;
	$offset = 0;
	$orderby = 'login';
	$order = 'ASC';

	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : 0 );
		if( $limit_volume == -1 )
			$limit_volume = 0;
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : 0 );
		$orderby = ( isset( $args['user_orderby'] ) ? $args['user_orderby'] : 'login' );
		$order = ( isset( $args['user_order'] ) ? $args['user_order'] : 'ASC' );
	}
	$args = array(
		'offset' => $offset,
		'number' => $limit_volume,
		'order' => $order,
		'offset' => $offset,
		'fields' => 'ids'
	);
	if( $user_ids = new WP_User_Query( $args ) ) {
		$users = array();
		$export->total_rows = $user_ids->total_users;
		foreach( $user_ids->results as $user_id )
			$users[] = $user_id;
		return $users;
	}

}

function woo_ce_get_user_data( $user_id = 0, $args = array() ) {

	$defaults = array();
	$args = wp_parse_args( $args, $defaults );

	// Get User details
	$user_data = get_userdata( $user_id );

	$user = new stdClass;
	if( $user_data !== false ) {
		$user->ID = $user_data->ID;
		$user->user_id = $user_data->ID;
		$user->user_name = $user_data->user_login;
		$user->user_role = ( isset( $user_data->roles[0] ) ? $user_data->roles[0] : false );
		$user->first_name = $user_data->first_name;
		$user->last_name = $user_data->last_name;
		$user->full_name = sprintf( apply_filters( 'woo_ce_get_user_data_full_name', '%s %s' ), $user->first_name, $user->last_name );
		$user->nick_name = $user_data->user_nicename;
		$user->email = $user_data->user_email;
		$user->url = $user_data->user_url;
		$user->date_registered = $user_data->user_registered;
		$user->description = $user_data->description;
		$user->aim = $user_data->aim;
		$user->yim = $user_data->yim;
		$user->jabber = $user_data->jabber;
	}

	// Allow Plugin/Theme authors to add support for additional User columns
	return apply_filters( 'woo_ce_user', $user );
	
}

function woo_ce_export_dataset_override_user( $output = null, $export_type = null ) {

	global $export;

	if( $users = woo_ce_get_users( $export->args ) ) {
		$separator = $export->delimiter;
		$size = $export->total_columns;
		$export->total_rows = count( $users );
		// Generate the export headers
		if( in_array( $export->export_format, array( 'csv' ) ) ) {
			for( $i = 0; $i < $size; $i++ ) {
				if( $i == ( $size - 1 ) )
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
				else
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
			}
		}
		if( !empty( $export->fields ) ) {
			foreach( $users as $user ) {

				$user = woo_ce_get_user_data( $user, $export->args );

				foreach( $export->fields as $key => $field ) {
					if( isset( $user->$key ) ) {
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output .= woo_ce_escape_csv_value( $user->$key, $export->delimiter, $export->escape_formatting );
					}
					if( in_array( $export->export_format, array( 'csv' ) ) )
						$output .= $separator;
				}

				if( in_array( $export->export_format, array( 'csv' ) ) )
					$output = substr( $output, 0, -1 ) . "\n";

			}
		}
		unset( $users, $user );
	}
	return $output;

}

// Returns a list of WordPress User Roles
function woo_ce_get_user_roles() {

	global $wp_roles;

	$user_roles = $wp_roles->roles;
	return $user_roles;

}

// Returns the Username of a User
function woo_ce_get_username( $user_id = 0 ) {

	$output = '';
	if( $user_id ) {
		if( $user = get_userdata( $user_id ) )
			$output = $user->user_login;
		unset( $user );
	}
	return $output;

}

// Returns the User Role of a User
function woo_ce_get_user_role( $user_id = 0 ) {

	$output = '';
	if( $user_id ) {
		$user = get_userdata( $user_id );
		if( $user ) {
			$user_role = ( isset( $user->roles[0] ) ? $user->roles[0] : false );
			if( !empty( $user_role ) )
				$output = $user_role;
		}
		unset( $user );
	}
	return $output;

}

function woo_ce_format_user_role_label( $user_role = '' ) {

	global $wp_roles;

	$output = $user_role;
	if( !empty( $user_role ) ) {
		$user_roles = woo_ce_get_user_roles();
		if( !empty( $user_roles ) ) {
			if( isset( $user_roles[$user_role] ) ) {
				if( !empty( $user_roles[$user_role]['name'] ) )
					$output = ucfirst( $user_roles[$user_role]['name'] );
			}
		}
		unset( $user_roles );
	}
	return $output;

}