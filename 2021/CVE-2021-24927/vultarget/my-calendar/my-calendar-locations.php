<?php
/**
 * Update & Add Locations
 *
 * @category Locations
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle updating location posts
 *
 * @param array $where Array with where query.
 * @param array $data saved location data.
 * @param int   $post POST data.
 *
 * @return int post ID
 */
function mc_update_location_post( $where, $data, $post ) {
	// if the location save was successful.
	$location_id = $where['location_id'];
	$post_id     = mc_get_location_post( $location_id, false );
	// If, after all that, the post doesn't exist, create it.
	if ( ! get_post_status( $post_id ) ) {
		mc_create_location_post( $location_id, $data, $post );
	}

	$title       = $data['location_label'];
	$post_status = 'publish';
	$auth        = get_current_user_id();
	$type        = 'mc-locations';
	$my_post     = array(
		'ID'          => $post_id,
		'post_title'  => $title,
		'post_status' => $post_status,
		'post_author' => $auth,
		'post_name'   => sanitize_title( $title ),
		'post_type'   => $type,
	);
	if ( mc_switch_sites() && defined( BLOG_ID_CURRENT_SITE ) ) {
		switch_to_blog( BLOG_ID_CURRENT_SITE );
	}
	$post_id = wp_update_post( $my_post );

	do_action( 'mc_update_location_post', $post_id, $_POST, $data, $location_id );
	if ( mc_switch_sites() ) {
		restore_current_blog();
	}

	return $post_id;
}
add_action( 'mc_modify_location', 'mc_update_location_post', 10, 3 );

/**
 * Create a post for My Calendar location data on save
 *
 * @param bool|int $location_id Result of save action; location ID or false.
 * @param array    $data Saved event data.
 * @param array    $post POST data.
 *
 * @return int newly created post ID
 */
function mc_create_location_post( $location_id, $data, $post ) {
	if ( ! $location_id ) {
		return;
	}
	$post_id = mc_get_location_post( $location_id, false );
	if ( ! $post_id ) {
		$title       = $data['location_label'];
		$post_status = 'publish';
		$auth        = get_current_user_id();
		$type        = 'mc-locations';
		$my_post     = array(
			'post_title'  => $title,
			'post_status' => $post_status,
			'post_author' => $auth,
			'post_name'   => sanitize_title( $title ),
			'post_date'   => current_time( 'Y-m-d H:i:s' ),
			'post_type'   => $type,
		);
		$post_id     = wp_insert_post( $my_post );
		update_post_meta( $post_id, '_mc_location_id', $location_id );

		do_action( 'mc_update_location_post', $post_id, $post, $data, $location_id );
		wp_publish_post( $post_id );
	}

	return $post_id;
}
add_action( 'mc_save_location', 'mc_create_location_post', 10, 3 );

/**
 * Update custom fields for a location.
 *
 * @param int   $post_id Post ID associated with location.
 * @param array $post POST data.
 * @param array $data Saved location data.
 * @param int   $location_id Location ID in table.
 *
 * @return array Errors.
 */
function mc_update_location_custom_fields( $post_id, $post, $data, $location_id ) {
	$fields       = mc_location_fields();
	$field_errors = array();
	foreach ( $fields as $name => $field ) {
		if ( isset( $post[ $name ] ) ) {
			if ( ! isset( $field['sanitize_callback'] ) || ( isset( $field['sanitize_callback'] ) && ! function_exists( $field['sanitize_callback'] ) ) ) {
				// if no sanitization is provided, we'll prep it for SQL and strip tags.
				$sanitized = esc_html( strip_tags( urldecode( $post[ $name ] ) ) );
			} else {
				$sanitized = call_user_func( $field['sanitize_callback'], urldecode( $post[ $name ] ) );
			}
			$success = update_post_meta( $post_id, $name, $sanitized );
			if ( ! $success ) {
				$field_errors[] = $name;
			}
		}
	}

	return $field_errors;
}
add_action( 'mc_update_location_post', 'mc_update_location_custom_fields', 10, 4 );

/**
 * Delete custom post type associated with event
 *
 * @param int $result   Result of delete action.
 * @param int $location_id Location ID.
 */
function mc_location_delete_post( $result, $location_id ) {
	$posts = get_posts(
		array(
			'post_type'  => 'mc-locations',
			'meta_key'   => '_mc_location_id',
			'meta_value' => $location_id,
		)
	);
	if ( isset( $posts[0] ) && is_object( $posts[0] ) ) {
		$post_id = $posts[0]->ID;
		wp_delete_post( $post_id, true );
		do_action( 'mc_delete_location_posts', $location_id, $posts );
	}
}
add_action( 'mc_delete_location', 'mc_location_delete_post', 10, 2 );

/**
 * Get the location post for a location.
 *
 * @param int  $location_id Location ID.
 * @param bool $type True for full post object.
 *
 * @return object $post
 */
function mc_get_location_post( $location_id, $type = true ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	$post_ID = false;
	$post    = false;
	$query   = $mcdb->prepare( "SELECT post_id FROM $wpdb->postmeta where meta_key ='_mc_location_id' and meta_value = %d", $location_id );
	$posts   = $mcdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( isset( $posts[0] ) ) {
		$post_ID = $posts[0];
	}

	return ( $type ) ? get_post( $post_ID ) : $post_ID;
}

/**
 * Update a single field in a location.
 *
 * @param string $field field name.
 * @param mixed  $data data to update to.
 * @param int    $location location ID.
 *
 * @return mixed boolean/int query result
 */
function mc_update_location( $field, $data, $location ) {
	global $wpdb;
	$field  = sanitize_key( $field );
	$result = $wpdb->query( $wpdb->prepare( 'UPDATE ' . my_calendar_locations_table() . " SET $field = %d WHERE location_id=%d", $data, $location ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

	return $result;
}

/**
 * Update settings for how location inputs are limited.
 */
function mc_update_location_controls() {
	if ( isset( $_POST['mc_locations'] ) && 'true' === $_POST['mc_locations'] ) {
		$nonce = $_POST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			wp_die( 'Invalid nonce' );
		}
		$locations            = $_POST['mc_location_controls'];
		$mc_location_controls = array();
		foreach ( $locations as $key => $value ) {
			$mc_location_controls[ $key ] = mc_csv_to_array( $value[0] );
		}
		update_option( 'mc_location_controls', $mc_location_controls );
		mc_show_notice( __( 'Location Controls Updated', 'my-calendar' ) );
	}
}

/**
 * Insert a new location.
 *
 * @param array $add Array of location details to add.
 *
 * @return mixed boolean/int query result.
 */
function mc_insert_location( $add ) {
	global $wpdb;
	$add     = array_map( 'mc_kses_post', $add );
	$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s', '%s', '%s' );
	$results = $wpdb->insert( my_calendar_locations_table(), $add, $formats );
	if ( $results ) {
		$insert_id = $wpdb->insert_id;
	} else {
		$insert_id = false;
	}

	return $insert_id;
}

/**
 * Get count of locations.
 *
 * @return int
 */
function mc_count_locations() {
	global $wpdb;
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . my_calendar_locations_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

	return $count;
}

/**
 * Update a location.
 *
 * @param array $update Array of location details to modify.
 * @param int   $where Location ID to update.
 *
 * @return mixed boolean/int query result.
 */
function mc_modify_location( $update, $where ) {
	global $wpdb;
	$update  = array_map( 'mc_kses_post', $update );
	$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s', '%s', '%s' );
	$results = $wpdb->update( my_calendar_locations_table(), $update, $where, $formats, '%d' );

	return $results;
}

/**
 * Handle results of form submit & display form.
 */
function my_calendar_add_locations() {
	global $wpdb;
	?>
	<div class="wrap my-calendar-admin">
	<?php
	my_calendar_check_db();
	// We do some checking to see what we're doing.
	mc_mass_delete_locations();
	if ( ! empty( $_POST ) && ( ! isset( $_POST['mc_locations'] ) && ! isset( $_POST['mass_delete'] ) ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( 'Security check failed' );
		}
	}
	if ( isset( $_POST['mode'] ) && 'add' === $_POST['mode'] ) {
		$add = array(
			'location_label'     => $_POST['location_label'],
			'location_street'    => $_POST['location_street'],
			'location_street2'   => $_POST['location_street2'],
			'location_city'      => $_POST['location_city'],
			'location_state'     => $_POST['location_state'],
			'location_postcode'  => $_POST['location_postcode'],
			'location_region'    => $_POST['location_region'],
			'location_country'   => $_POST['location_country'],
			'location_url'       => $_POST['location_url'],
			'location_longitude' => $_POST['location_longitude'],
			'location_latitude'  => $_POST['location_latitude'],
			'location_zoom'      => $_POST['location_zoom'],
			'location_phone'     => $_POST['location_phone'],
			'location_phone2'    => $_POST['location_phone2'],
			'location_access'    => isset( $_POST['location_access'] ) ? serialize( $_POST['location_access'] ) : '',
		);

		$results = mc_insert_location( $add );
		if ( isset( $_POST['mc_default_location'] ) && $results ) {
			update_option( 'mc_default_location', (int) $results );
		}
		do_action( 'mc_save_location', $results, $add, $_POST );
		if ( $results ) {
			mc_show_notice( __( 'Location added successfully', 'my-calendar' ) );
		} else {
			mc_show_error( __( 'Location could not be added to database', 'my-calendar' ) );
		}
	} elseif ( isset( $_GET['location_id'] ) && 'delete' === $_GET['mode'] ) {
		$results = $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_locations_table() . ' WHERE location_id=%d', $_GET['location_id'] ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		do_action( 'mc_delete_location', $results, (int) $_GET['location_id'] );
		if ( $results ) {
			mc_show_notice( __( 'Location deleted successfully', 'my-calendar' ) );
			$default_location = get_option( 'mc_default_location', false );
			if ( (int) $default_location === (int) $_GET['location_id'] ) {
				delete_option( 'mc_default_location' );
			}
		} else {
			mc_show_error( __( 'Location could not be deleted', 'my-calendar' ) );
		}
	} elseif ( isset( $_GET['mode'] ) && isset( $_GET['location_id'] ) && 'edit' === $_GET['mode'] && ! isset( $_POST['mode'] ) ) {
		$cur_loc = (int) $_GET['location_id'];
		mc_show_location_form( 'edit', $cur_loc );
	} elseif ( isset( $_POST['location_id'] ) && isset( $_POST['location_label'] ) && 'edit' === $_POST['mode'] ) {
		$update = array(
			'location_label'     => $_POST['location_label'],
			'location_street'    => $_POST['location_street'],
			'location_street2'   => $_POST['location_street2'],
			'location_city'      => $_POST['location_city'],
			'location_state'     => $_POST['location_state'],
			'location_postcode'  => $_POST['location_postcode'],
			'location_region'    => $_POST['location_region'],
			'location_country'   => $_POST['location_country'],
			'location_url'       => $_POST['location_url'],
			'location_longitude' => $_POST['location_longitude'],
			'location_latitude'  => $_POST['location_latitude'],
			'location_zoom'      => $_POST['location_zoom'],
			'location_phone'     => $_POST['location_phone'],
			'location_phone2'    => $_POST['location_phone2'],
			'location_access'    => isset( $_POST['location_access'] ) ? serialize( $_POST['location_access'] ) : '',
		);

		$where = array( 'location_id' => (int) $_POST['location_id'] );
		if ( isset( $_POST['mc_default_location'] ) ) {
			update_option( 'mc_default_location', (int) $_POST['location_id'] );
		}
		$results = mc_modify_location( $update, $where );

		do_action( 'mc_modify_location', $where, $update, $_POST );
		if ( false === $results ) {
			mc_show_error( __( 'Location could not be edited.', 'my-calendar' ) );
		} elseif ( 0 === $results ) {
			mc_show_error( __( 'Location was not changed.', 'my-calendar' ) );
		} else {
			mc_show_notice( __( 'Location edited successfully', 'my-calendar' ) );
		}
		$cur_loc = (int) $_POST['location_id'];
		mc_show_location_form( 'edit', $cur_loc );

	}

	if ( isset( $_GET['mode'] ) && 'edit' !== $_GET['mode'] || isset( $_POST['mode'] ) && 'edit' !== $_POST['mode'] || ! isset( $_GET['mode'] ) && ! isset( $_POST['mode'] ) ) {
		mc_show_location_form( 'add' );
	}
}

/**
 * Create location editing form.
 *
 * @param string $view type of view add/edit.
 * @param int    $loc_id Location ID.
 */
function mc_show_location_form( $view = 'add', $loc_id = '' ) {
	$cur_loc = false;
	if ( '' !== $loc_id ) {
		$cur_loc = mc_get_location( $loc_id );
	}
	$has_data = ( empty( $cur_loc ) ) ? false : true;
	if ( 'add' === $view ) {
		?>
		<h1><?php _e( 'Add New Location', 'my-calendar' ); ?></h1>
		<?php
	} else {
		?>
		<h1 class="wp-heading-inline"><?php _e( 'Edit Location', 'my-calendar' ); ?></h1>
		<a href="<?php echo admin_url( 'admin.php?page=my-calendar-locations' ); ?>" class="page-title-action"><?php _e( 'Add New', 'my-calendar' ); ?></a>
		<hr class="wp-header-end">
		<?php
	}
	?>
	<div class="postbox-container jcd-wide">
		<div class="metabox-holder">

			<div class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h2><?php _e( 'Location Editor', 'my-calendar' ); ?></h2>

					<div class="inside location_form">
						<?php
						$params = array();
						if ( isset( $_GET['location_id'] ) ) {
							$params = array(
								'mode'        => $_GET['mode'],
								'location_id' => $_GET['location_id'],
							);
						}
						?>
						<form id="my-calendar" method="post" action="<?php echo add_query_arg( $params, admin_url( 'admin.php?page=my-calendar-locations' ) ); ?>">
							<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/></div>
							<?php
							if ( 'add' === $view ) {
								?>
								<div>
									<input type="hidden" name="mode" value="add" />
									<input type="hidden" name="location_id" value="" />
								</div>
								<?php
							} else {
								?>
								<div>
									<input type="hidden" name="mode" value="edit"/>
									<input type="hidden" name="location_id" value="<?php echo $cur_loc->location_id; ?>"/>
								</div>
								<?php
							}
							echo mc_locations_fields( $has_data, $cur_loc, 'location' );
							?>
							<p>
								<input type="submit" name="save" class="button-primary" value="<?php echo ( 'edit' === $view ) ? __( 'Save Changes', 'my-calendar' ) : __( 'Add Location', 'my-calendar' ); ?> &raquo;"/>
							</p>
						</form>
					</div>
				</div>
			</div>
			<?php
			if ( 'edit' === $view ) {
				?>
				<p>
					<a href="<?php echo admin_url( 'admin.php?page=my-calendar-locations' ); ?>"><?php _e( 'Add a New Location', 'my-calendar' ); ?> &raquo;</a>
				</p>
				<?php
			}
			?>
		</div>
	</div>
		<?php
		$controls = array( __( 'Location Controls', 'my-calendar' ) => mc_location_controls() );
		mc_show_sidebar( '', $controls );
		?>
	</div>
	<?php
}

/**
 * Get details about one location.
 *
 * @param int $location_id Location ID.
 *
 * @return object location
 */
function mc_get_location( $location_id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	$location                = $mcdb->get_row( $mcdb->prepare( 'SELECT * FROM ' . my_calendar_locations_table() . ' WHERE location_id = %d', $location_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$location->location_post = mc_get_location_post( $location_id, false );

	return $location;
}

/**
 * Check whether this location field has pre-entered controls on input
 *
 * @param string $this_field field name.
 *
 * @return boolean true if location field is controlled
 */
function mc_controlled_field( $this_field ) {
	$this_field = trim( $this_field );
	$controls   = get_option( 'mc_location_controls' );
	if ( ! is_array( $controls ) || empty( $controls ) ) {
		return false;
	}
	$controlled = array_keys( $controls );
	if ( in_array( 'event_' . $this_field, $controlled, true ) && ! empty( $controls[ 'event_' . $this_field ] ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Return select element with the controlled values for a location field
 *
 * @param string $fieldname Name of field.
 * @param string $selected currently selected value.
 * @param string $context current context: entering new location or new event.
 *
 * @return string HTML select element with values
 */
function mc_location_controller( $fieldname, $selected, $context = 'location' ) {
	$field    = ( 'location' === $context ) ? 'location_' . $fieldname : 'event_' . $fieldname;
	$selected = esc_attr( trim( $selected ) );
	$options  = get_option( 'mc_location_controls' );
	$regions  = $options[ 'event_' . $fieldname ];
	$form     = "<select name='$field' id='e_$fieldname'>";
	$form    .= "<option value=''>" . __( 'Select', 'my-calendar' ) . '</option>';
	if ( is_admin() && '' !== $selected ) {
		$form .= "<option value='$selected'>$selected :" . __( '(Not a controlled value)', 'my-calendar' ) . '</option>';
	}
	foreach ( $regions as $key => $value ) {
		$key       = esc_attr( trim( $key ) );
		$value     = esc_html( trim( $value ) );
		$aselected = ( $selected === $key ) ? ' selected="selected"' : '';
		$form     .= "<option value='$key'$aselected>$value</option>\n";
	}
	$form .= '</select>';

	return $form;
}

/**
 * Location controls for limiting location submission options.
 *
 * @return string HTML controls.
 */
function mc_location_controls() {
	if ( current_user_can( 'mc_edit_settings' ) ) {
		$response             = mc_update_location_controls();
		$location_fields      = array(
			'event_label',
			'event_city',
			'event_state',
			'event_country',
			'event_postcode',
			'event_region',
		);
		$mc_location_controls = get_option( 'mc_location_controls' );

		$output = $response . '
		<form method="post" action="' . admin_url( 'admin.php?page=my-calendar-locations' ) . '">
		<div><input type="hidden" name="_wpnonce" value="' . wp_create_nonce( 'my-calendar-nonce' ) . '" /></div>
		<div><input type="hidden" name="mc_locations" value="true" /></div>
		<fieldset>
			<legend>' . __( 'Limit Input Options', 'my-calendar' ) . '</legend>
			<div id="mc-accordion">';
		foreach ( $location_fields as $field ) {
			$locations = '';
			$class     = '';
			$active    = '';
			if ( is_array( $mc_location_controls ) && isset( $mc_location_controls[ $field ] ) ) {
				foreach ( $mc_location_controls[ $field ] as $key => $value ) {
					$key        = esc_html( trim( $key ) );
					$value      = esc_html( trim( $value ) );
					$locations .= stripslashes( "$key,$value" ) . PHP_EOL;
				}
			}
			if ( '' !== trim( $locations ) ) {
				$class  = ' class="active-limit"';
				$active = ' (' . __( 'active limits', 'my-calendar' ) . ')';
			}
			$output .= '<h4' . $class . '><span class="dashicons" aria-hidden="true"> </span><button type="button" class="button-link">' . ucfirst( str_replace( 'event_', '', $field ) ) . $active . '</button></h4>';
			// Translators: Name of field being restricted, e.g. "Location Controls for State".
			$output .= '<div><label for="loc_values_' . $field . '">' . sprintf( __( 'Location Controls for %s', 'my-calendar' ), ucfirst( str_replace( 'event_', '', $field ) ) ) . '</label><br/><textarea name="mc_location_controls[' . $field . '][]" id="loc_values_' . $field . '" cols="80" rows="6">' . trim( $locations ) . '</textarea></div>';
		}
		$output .= "
			</div>
			<p><input type='submit' class='button secondary' value='" . __( 'Save Location Controls', 'my-calendar' ) . "'/></p>
		</fieldset>
		</form>";

		return $output;
	}
}

/**
 * Produce the form to submit location data
 *
 * @param boolean $has_data Whether currently have data.
 * @param object  $data event or location data.
 * @param string  $context whether currently in an event or a location context.
 *
 * @return string HTML form fields
 */
function mc_locations_fields( $has_data, $data, $context = 'location' ) {
	$return = '<div class="mc-locations">';
	if ( current_user_can( 'mc_edit_locations' ) && 'event' === $context ) {
		$return .= '<p class="checkboxes"><input type="checkbox" value="on" name="mc_copy_location" id="mc_copy_location" /> <label for="mc_copy_location">' . __( 'Copy this location into the locations table', 'my-calendar' ) . '</label></p>';
	}
	if ( current_user_can( 'mc_edit_settings' ) && isset( $_GET['page'] ) && 'my-calendar-locations' === $_GET['page'] ) {
		$checked = ( isset( $_GET['location_id'] ) && (int) get_option( 'mc_default_location' ) === (int) $_GET['location_id'] ) ? 'checked="checked"' : '';
		$return .= '<p class="checkbox">';
		$return .= '<input type="checkbox" name="mc_default_location" id="mc_default_location"' . $checked . ' /> <label for="mc_default_location">' . __( 'Default Location', 'my-calendar' ) . '</label>';
		$return .= '</p>';
	}
	$return   .= '
	<p>
	<label for="e_label">' . __( 'Name of Location (e.g. <em>Joe\'s Bar and Grill</em>)', 'my-calendar' ) . '</label>';
	$cur_label = ( ! empty( $data ) ) ? ( stripslashes( $data->{$context . '_label'} ) ) : '';
	if ( mc_controlled_field( 'label' ) ) {
		$return .= mc_location_controller( 'label', $cur_label, $context );
	} else {
		$return .= '<input type="text" id="e_label" name="' . $context . '_label" size="40" value="' . esc_attr( $cur_label ) . '" />';
	}
	$street_address  = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_street'} ) ) : '';
	$street_address2 = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_street2'} ) ) : '';
	$return         .= '
	</p>
	<div class="locations-container">
	<div class="location-primary">
	<fieldset>
	<legend>' . __( 'Location Address', 'my-calendar' ) . '</legend>
	<p>
		<label for="e_street">' . __( 'Street Address', 'my-calendar' ) . '</label> <input type="text" id="e_street" name="' . $context . '_street" size="40" value="' . $street_address . '" />
	</p>
	<p>
		<label for="e_street2">' . __( 'Street Address (2)', 'my-calendar' ) . '</label> <input type="text" id="e_street2" name="' . $context . '_street2" size="40" value="' . $street_address2 . '" />
	</p>
	<p>
		<label for="e_city">' . __( 'City', 'my-calendar' ) . '</label> ';
	$cur_city        = ( ! empty( $data ) ) ? ( stripslashes( $data->{$context . '_city'} ) ) : '';
	if ( mc_controlled_field( 'city' ) ) {
		$return .= mc_location_controller( 'city', $cur_city, $context );
	} else {
		$return .= '<input type="text" id="e_city" name="' . $context . '_city" size="40" value="' . esc_attr( $cur_city ) . '" />';
	}
	$return   .= '</p><p>';
	$return   .= '<label for="e_state">' . __( 'State/Province', 'my-calendar' ) . '</label> ';
	$cur_state = ( ! empty( $data ) ) ? ( stripslashes( $data->{$context . '_state'} ) ) : '';
	if ( mc_controlled_field( 'state' ) ) {
		$return .= mc_location_controller( 'state', $cur_state, $context );
	} else {
		$return .= '<input type="text" id="e_state" name="' . $context . '_state" size="10" value="' . esc_attr( $cur_state ) . '" />';
	}
	$return      .= '</p><p><label for="e_postcode">' . __( 'Postal Code', 'my-calendar' ) . '</label> ';
	$cur_postcode = ( ! empty( $data ) ) ? ( stripslashes( $data->{$context . '_postcode'} ) ) : '';
	if ( mc_controlled_field( 'postcode' ) ) {
		$return .= mc_location_controller( 'postcode', $cur_postcode, $context );
	} else {
		$return .= '<input type="text" id="e_postcode" name="' . $context . '_postcode" size="40" value="' . esc_attr( $cur_postcode ) . '" />';
	}
	$return    .= '</p><p>';
	$return    .= '<label for="e_region">' . __( 'Region', 'my-calendar' ) . '</label> ';
	$cur_region = ( ! empty( $data ) ) ? ( stripslashes( $data->{$context . '_region'} ) ) : '';
	if ( mc_controlled_field( 'region' ) ) {
		$return .= mc_location_controller( 'region', $cur_region, $context );
	} else {
		$return .= '<input type="text" id="e_region" name="' . $context . '_region" size="40" value="' . esc_attr( $cur_region ) . '" />';
	}
	$return     .= '</p><p><label for="e_country">' . __( 'Country', 'my-calendar' ) . '</label> ';
	$cur_country = ( $has_data ) ? ( stripslashes( $data->{$context . '_country'} ) ) : '';
	if ( mc_controlled_field( 'country' ) ) {
		$return .= mc_location_controller( 'country', $cur_country, $context );
	} else {
		$return .= '<input type="text" id="e_country" name="' . $context . '_country" size="10" value="' . esc_attr( $cur_country ) . '" />';
	}
	$zoom         = ( $has_data ) ? $data->{$context . '_zoom'} : '16';
	$event_phone  = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_phone'} ) ) : '';
	$event_phone2 = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_phone2'} ) ) : '';
	$event_url    = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_url'} ) ) : '';
	$event_lat    = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_latitude'} ) ) : '';
	$event_lon    = ( $has_data ) ? esc_attr( stripslashes( $data->{$context . '_longitude'} ) ) : '';
	$return      .= '</p>
	<p>
	<label for="e_zoom">' . __( 'Initial Zoom', 'my-calendar' ) . '</label>
		<select name="' . $context . '_zoom" id="e_zoom">
			<option value="16"' . mc_option_selected( $zoom, '16', 'option' ) . '>' . __( 'Neighborhood', 'my-calendar' ) . '</option>
			<option value="14"' . mc_option_selected( $zoom, '14', 'option' ) . '>' . __( 'Small City', 'my-calendar' ) . '</option>
			<option value="12"' . mc_option_selected( $zoom, '12', 'option' ) . '>' . __( 'Large City', 'my-calendar' ) . '</option>
			<option value="10"' . mc_option_selected( $zoom, '10', 'option' ) . '>' . __( 'Greater Metro Area', 'my-calendar' ) . '</option>
			<option value="8"' . mc_option_selected( $zoom, '8', 'option' ) . '>' . __( 'State', 'my-calendar' ) . '</option>
			<option value="6"' . mc_option_selected( $zoom, '6', 'option' ) . '>' . __( 'Region', 'my-calendar' ) . '</option>
		</select>
	</p>
	</fieldset>
	<fieldset>
	<legend>' . __( 'GPS Coordinates (optional)', 'my-calendar' ) . '</legend>
	<p>
	' . __( 'If you supply GPS coordinates for your location, they will be used in place of any other address information to provide your map link.', 'my-calendar' ) . '
	</p>
	<p>
	<label for="e_latitude">' . __( 'Latitude', 'my-calendar' ) . '</label> <input type="text" id="e_latitude" name="' . $context . '_latitude" size="10" value="' . $event_lat . '" /> <label for="e_longitude">' . __( 'Longitude', 'my-calendar' ) . '</label> <input type="text" id="e_longitude" name="' . $context . '_longitude" size="10" value="' . $event_lon . '" />
	</p>
	</fieldset>';
	$return      .= apply_filters( 'mc_location_container_primary', '', $data, $context );
	$return      .= '
	</div>
	<div class="location-secondary">
	<fieldset>
	<legend>' . __( 'Location Contact Information', 'my-calendar' ) . '</legend>
	<p>
	<label for="e_phone">' . __( 'Phone', 'my-calendar' ) . '</label> <input type="text" id="e_phone" name="' . $context . '_phone" size="32" value="' . $event_phone . '" />
	</p>
	<p>
	<label for="e_phone2">' . __( 'Secondary Phone', 'my-calendar' ) . '</label> <input type="text" id="e_phone2" name="' . $context . '_phone2" size="32" value="' . $event_phone2 . '" />
	</p>
	<p>
	<label for="e_url">' . __( 'Location URL', 'my-calendar' ) . '</label> <input type="text" id="e_url" name="' . $context . '_url" size="40" value="' . $event_url . '" />
	</p>
	</fieldset>
	<fieldset>
	<legend>' . __( 'Location Accessibility', 'my-calendar' ) . '</legend>
	<ul class="accessibility-features checkboxes">';
	$access       = apply_filters( 'mc_venue_accessibility', mc_location_access() );
	$access_list  = '';
	if ( $has_data ) {
		if ( 'location' === $context ) {
			$location_access = unserialize( $data->{$context . '_access'} );
		} else {
			if ( property_exists( $data, 'event_location' ) ) {
				$event_location = $data->event_location;
			} else {
				$event_location = false;
			}
			$location_access = unserialize( mc_location_data( 'location_access', $event_location ) );
		}
	} else {
		$location_access = array();
	}
	foreach ( $access as $k => $a ) {
		$id      = "loc_access_$k";
		$label   = $a;
		$checked = '';
		if ( is_array( $location_access ) ) {
			$checked = ( in_array( $a, $location_access, true ) || in_array( $k, $location_access, true ) ) ? " checked='checked'" : '';
		}
		$item         = sprintf( '<li><input type="checkbox" id="%1$s" name="' . $context . '_access[]" value="%4$s" class="checkbox" %2$s /> <label for="%1$s">%3$s</label></li>', esc_attr( $id ), $checked, esc_html( $label ), esc_attr( $a ) );
		$access_list .= $item;
	}
	$return .= $access_list;
	$return .= '</ul>
	</fieldset>';
	$fields  = mc_display_location_fields( mc_location_fields(), $data, $context );
	$return .= ( '' !== $fields ) ? '<div class="mc-custom-fields mc-locations"><fieldset><legend>' . __( 'Custom Fields', 'my-calendar' ) . '</legend>' . $fields . '</fieldset></div>' : '';
	$return .= apply_filters( 'mc_location_container_secondary', '', $data, $context );
	$return .= '</div>
	</div>
	</div>';

	$api_key = get_option( 'mc_gmap_api_key' );
	if ( $api_key ) {
		$return .= '<h3>' . __( 'Location Map', 'my-calendar' ) . '</h3>';
		$map     = mc_generate_map( $data, $context );

		$return .= ( '' === $map ) ? __( 'Not enough information to generate a map', 'my-calendar' ) : $map;
	} else {
		// Translators: URL to settings page to add key.
		$return .= sprintf( __( 'Add a <a href="%s">Google Maps API Key</a> to generate a location map.', 'my-calendar' ), admin_url( 'admin.php?page=my-calendar-config#mc-output' ) );
	}

	return $return;
}

/**
 * Return a set of location fields.
 *
 * @return array
 */
function mc_location_fields() {
	$fields = apply_filters( 'mc_location_fields', array() );

	return $fields;
}

/**
 * Get custom data for a location.
 *
 * @param int    $location_id Location ID.
 * @param int    $location_post Location Post ID.
 * @param string $field Custom field name.
 *
 * @return mixed
 */
function mc_location_custom_data( $location_id = false, $location_post = false, $field ) {
	$location_id = ( isset( $_GET['location_id'] ) ) ? (int) $_GET['location_id'] : $location_id;
	$value       = '';
	// Quick exit when location post is known.
	if ( $location_post ) {
		return get_post_meta( $location_post, $field, true );
	}
	if ( ! $location_id ) {
		$location_id = ( isset( $_POST['location_id'] ) ) ? (int) $_POST['location_id'] : false;
	}
	if ( $location_id ) {
		$post_id = mc_get_location_post( $location_id, false );
		$value   = get_post_meta( $post_id, $field, true );
	}

	return $value;
}

/**
 * Add custom fields to event data output.
 *
 * @param array  $e Event tag data.
 * @param object $event Event object.
 *
 * @return array
 */
function mc_template_location_fields( $e, $event ) {
	$fields = mc_location_fields();
	foreach ( $fields as $name => $field ) {
		$location_post = false;
		if ( is_object( $event ) && property_exists( $event, 'location' ) ) {
			$location_post = $event->location->location_post;
		}
		$value = mc_location_custom_data( $event->event_location, $location_post, $name );
		if ( ! isset( $field['display_callback'] ) || ( isset( $field['display_callback'] ) && ! function_exists( $field['display_callback'] ) ) ) {
			// if no display callback is provided.
			$display = stripslashes( $value );
		} else {
			$display = call_user_func( $field['display_callback'], $value, $field );
		}
		$key       = 'location_' . $name;
		$e[ $key ] = $display;
	}

	return $e;
}
add_filter( 'mc_filter_shortcodes', 'mc_template_location_fields', 10, 2 );

/**
 * Expand custom fields from array to field output
 *
 * @param array  $fields Array of field data.
 * @param array  $data Location data.
 * @param string $context Location or event.
 *
 * @return string
 */
function mc_display_location_fields( $fields, $data, $context ) {
	if ( empty( $fields ) ) {
		return '';
	}
	$output = '';
	$return = '';

	$custom_fields = apply_filters( 'mc_order_location_fields', $fields, $context );
	foreach ( $custom_fields as $name => $field ) {
		$user_value = mc_location_custom_data( $data, false, $name );
		$required   = isset( $field['required'] ) ? ' required' : '';
		$req_label  = isset( $field['required'] ) ? ' <span class="required">' . __( 'Required', 'my-calendar' ) . '</span>' : '';
		switch ( $field['input_type'] ) {
			case 'text':
			case 'number':
			case 'email':
			case 'url':
			case 'date':
			case 'tel':
				$output = "<input type='" . $field['input_type'] . "' name='$name' id='$name' value='$user_value'$required />";
				break;
			case 'hidden':
				$output = "<input type='hidden' name='$name' value='$user_value' />";
				break;
			case 'textarea':
				$output = "<textarea rows='6' cols='60' name='$name' id='$name'$required>$user_value</textarea>";
				break;
			case 'select':
				if ( isset( $field['input_values'] ) ) {
					$output = "<select name='$name' id='$name'$required>";
					foreach ( $field['input_values'] as $value ) {
						$value = esc_attr( stripslashes( $value ) );
						if ( $value === $user_value ) {
							$selected = " selected='selected'";
						} else {
							$selected = '';
						}
						$output .= "<option value='" . esc_attr( stripslashes( $value ) ) . "'$selected>" . $value . "</option>\n";
					}
					$output .= '</select>';
				}
				break;
			case 'checkbox':
			case 'radio':
				if ( isset( $field['input_values'] ) ) {
					$value = $field['input_values'];
					if ( (string) $value === (string) $user_value ) {
						$checked = ' checked="checked"';
					} else {
						$checked = '';
					}
					$output = "<input type='" . $field['input_type'] . "' name='$name' id='$name' value='" . esc_attr( stripslashes( $value ) ) . "'$checked $required />";
				}
				break;
			default:
				$output = "<input type='text' name='$name' id='$name' value='$user_value' $required />";
		}
		if ( 'hidden' !== $field['input_type'] ) {
			$return .= ( 'checkbox' === $field['input_type'] || 'radio' === $field['input_type'] ) ? '<p class="' . $field['input_type'] . '">' . $output . " <label for='$name'>" . $field['title'] . $req_label . '</label></p>' : "<p><label for='$name'>" . $field['title'] . $req_label . '</label> ' . $output . '</p>';
		} else {
			$return .= $output;
		}
	}

	return $return;
}

/**
 * Array of location access features
 *
 * @return array
 */
function mc_location_access() {
	$location_access = array(
		'1'  => __( 'Accessible Entrance', 'my-calendar' ),
		'2'  => __( 'Accessible Parking Designated', 'my-calendar' ),
		'3'  => __( 'Accessible Restrooms', 'my-calendar' ),
		'4'  => __( 'Accessible Seating', 'my-calendar' ),
		'5'  => __( 'Accessible Transportation Available', 'my-calendar' ),
		'6'  => __( 'Wheelchair Accessible', 'my-calendar' ),
		'7'  => __( 'Courtesy Wheelchairs', 'my-calendar' ),
		'8'  => __( 'Bariatric Seating Available', 'my-calendar' ),
		'9'  => __( 'Elevator to all public areas', 'my-calendar' ),
		'10' => __( 'Braille Signage', 'my-calendar' ),
		'11' => __( 'Fragrance-Free Policy', 'my-calendar' ),
		'12' => __( 'Other', 'my-calendar' ),
	);

	return apply_filters( 'mc_location_access_choices', $location_access );
}

/**
 * Get a specific field with an location ID
 *
 * @param string $field Specific field to get.
 * @param int    $id Location ID.
 *
 * @return mixed value
 */
function mc_location_data( $field, $id ) {
	if ( $id ) {
		global $wpdb;
		$mcdb = $wpdb;
		if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
			$mcdb = mc_remote_db();
		}
		$field  = $field;
		$sql    = $mcdb->prepare( "SELECT $field FROM " . my_calendar_locations_table() . ' WHERE location_id = %d', $id );
		$result = $mcdb->get_var( $sql );

		return $result;
	}
}


/**
 * Get options list of locations to choose from
 *
 * @param object $location location object.
 *
 * @return string set of option elements
 */
function mc_location_select( $location = false ) {
	// Grab all locations and list them.
	$list = '';
	$locs = mc_get_locations( 'select-locations' );

	foreach ( $locs as $loc ) {
		// If label is empty, display street.
		if ( '' === (string) $loc->location_label ) {
			$label = $loc->location_street;
		} else {
			$label = $loc->location_label;
		}
		// If neither label nor street, skip.
		if ( '' === (string) $label ) {
			continue;
		}
		$l = '<option value="' . $loc->location_id . '"';
		if ( $location ) {
			if ( (int) $location === (int) $loc->location_id ) {
				$l .= ' selected="selected"';
			}
		}
		$l    .= '>' . mc_kses_post( stripslashes( $label ) ) . '</option>';
		$list .= $l;
	}

	return '<option value="">' . __( 'Select', 'my-calendar' ) . '</option>' . $list;
}

/**
 * Get list of locations (IDs and labels)
 *
 * @param array $args array of relevant arguments.
 *
 * @return array locations (IDs and labels only)
 */
function mc_get_locations( $args ) {
	global $wpdb;
	if ( is_array( $args ) ) {
		$context = ( isset( $args['context'] ) ) ? $args['context'] : 'general';
		$orderby = ( isset( $args['orderby'] ) ) ? $args['orderby'] : 'location_label';
		$order   = ( isset( $args['order'] ) ) ? $args['order'] : 'ASC';
		$where   = ( isset( $args['where'] ) ) ? $args['where'] : '1';
		$is      = ( isset( $args['is'] ) ) ? $args['is'] : '1';
	} else {
		$context = $args;
		$orderby = 'location_label';
		$order   = 'ASC';
		$where   = '1';
		$is      = '1';
	}
	if ( ! ( 'ASC' === $order || 'DESC' === $order ) ) {
		// Prevent invalid order parameters.
		$order = 'ASC';
	}
	$valid_args = $wpdb->get_col( 'DESC ' . my_calendar_locations_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( ! ( in_array( $orderby, $valid_args, true ) ) ) {
		// Prevent invalid order columns.
		$orderby = 'location_label';
	}
	$results = $wpdb->get_results( $wpdb->prepare( 'SELECT location_id,location_label FROM ' . my_calendar_locations_table() . ' WHERE ' . esc_sql( $where ) . ' = %s ORDER BY ' . esc_sql( $orderby ) . ' ' . esc_sql( $order ), $is ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return apply_filters( 'mc_filter_results', $results, $args );
}

/**
 * Search location titles.
 *
 * @param string $query Location query.
 *
 * @return array locations
 */
function mc_core_search_locations( $query = '' ) {
	global $wpdb;
	$search  = '';
	$results = array();
	$current = empty( $_GET['paged'] ) ? 1 : intval( $_GET['paged'] );
	$db_type = mc_get_db_type();
	$query   = esc_sql( $query );

	if ( '' !== $query ) {
		// Fulltext is supported in InnoDB since MySQL 5.6; minimum required by WP is 5.0 as of WP 5.5.
		// 37% of installs still below 5.6 as of 11/30/2020.
		if ( 'MyISAM' === $db_type ) {
			$search = ' WHERE MATCH(' . apply_filters( 'mc_search_fields', 'location_label' ) . ") AGAINST ( '$query' IN BOOLEAN MODE ) ";
		} else {
			$search = " WHERE location_label LIKE '%$query%' ";
		}
	} else {
		$search = '';
	}

	$locations = $wpdb->get_results( 'SELECT SQL_CALC_FOUND_ROWS location_id, location_label FROM ' . my_calendar_locations_table() . " $search ORDER BY location_label ASC" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

	return $locations;
}

/**
 * Get information about locations.
 */
function mc_core_autocomplete_search_locations() {
	if ( isset( $_REQUEST['action'] ) && 'mc_core_autocomplete_search_locations' === $_REQUEST['action'] ) {
		$security = $_REQUEST['security'];
		if ( ! wp_verify_nonce( $security, 'mc-search-locations' ) ) {
			wp_send_json(
				array(
					'success'  => 0,
					'response' => array( 'error' => 'Invalid security value.' ),
				)
			);
		}
		$query = $_REQUEST['data'];

		$locations = mc_core_search_locations( $query, array( 'location_id', 'location_label' ) );
		$response  = array();
		foreach ( $locations as $location ) {
			$response[] = array(
				'location_id'    => $location->location_id,
				'location_label' => html_entity_decode( strip_tags( $location->location_label ) ),
			);
		}
		wp_send_json(
			array(
				'success'  => 1,
				'response' => $response,
			)
		);
	}
}
add_action( 'wp_ajax_mc_core_autocomplete_search_locations', 'mc_core_autocomplete_search_locations' );
add_action( 'wp_ajax_nopriv_mc_core_autocomplete_search_locations', 'mc_core_autocomplete_search_locations' );
