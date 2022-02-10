<?php
/**
 * Event Manager. Creation & Editing of events.
 *
 * @category Events
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle post generation, updating, and processing after event is saved
 *
 * @param string   $action edit, copy, add.
 * @param array    $data saved event data.
 * @param int      $event_id My Calendar event ID.
 * @param bool|int $result Results of DB query.
 *
 * @return int post ID
 */
function mc_event_post( $action, $data, $event_id, $result = false ) {
	// if the event save was successful.
	if ( 'add' === $action || 'copy' === $action ) {
		$post_id = mc_create_event_post( $data, $event_id );
	} elseif ( 'edit' === $action ) {
		if ( isset( $_POST['event_post'] ) && ( 0 === (int) $_POST['event_post'] || '' === $_POST['event_post'] ) ) {
			$post_id = mc_create_event_post( $data, $event_id );
		} else {
			$post_id = ( isset( $_POST['event_post'] ) ) ? absint( $_POST['event_post'] ) : false;
		}
		// If, after all that, the post doesn't exist, create it.
		if ( ! get_post_status( $post_id ) ) {
			mc_create_event_post( $data, $event_id );
		}
		$categories = mc_get_categories( $event_id );
		$terms      = array();
		$privacy    = 'publish';

		foreach ( $categories as $category ) {
			$term = mc_get_category_detail( $category, 'category_term' );
			if ( ! $term ) {
				$term = wp_insert_term( mc_get_category_detail( $category, 'category_name' ), 'mc-event-category' );
				$term = ( ! is_wp_error( $term ) ) ? $term['term_id'] : false;
				if ( $term ) {
					$update = mc_update_category( 'category_term', $term, $category );
				}
			}
			// if any selected category is private, make private.
			if ( 'private' !== $privacy ) {
				$privacy = ( '1' === mc_get_category_detail( $category, 'category_private' ) ) ? 'private' : 'publish';
			}
			$terms[] = (int) $term;
		}

		$title             = $data['event_title'];
		$template          = apply_filters( 'mc_post_template', 'details', $terms );
		$data['shortcode'] = "[my_calendar_event event='$event_id' template='$template' list='']";
		$description       = $data['event_desc'];
		$excerpt           = $data['event_short'];
		$post_status       = $privacy;
		$auth              = ( isset( $data['event_author'] ) ) ? $data['event_author'] : get_current_user_id();
		$type              = 'mc-events';
		$my_post           = array(
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => $post_status,
			'post_author'  => $auth,
			'post_name'    => sanitize_title( $title ),
			'post_type'    => $type,
			'post_excerpt' => $excerpt,
		);
		if ( mc_switch_sites() && defined( BLOG_ID_CURRENT_SITE ) ) {
			switch_to_blog( BLOG_ID_CURRENT_SITE );
		}
		$post_id = wp_update_post( $my_post );
		wp_set_object_terms( $post_id, $terms, 'mc-event-category' );
		if ( '' === $data['event_image'] ) {
			delete_post_thumbnail( $post_id );
		} else {
			// check POST data.
			$attachment_id = ( isset( $_POST['event_image_id'] ) && is_numeric( $_POST['event_image_id'] ) ) ? $_POST['event_image_id'] : false;
			if ( $attachment_id ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
		$access       = ( isset( $_POST['events_access'] ) ) ? $_POST['events_access'] : array();
		$access_terms = implode( ',', array_values( $access ) );
		mc_update_event( 'event_access', $access_terms, $event_id, '%s' );
		mc_add_post_meta_data( $post_id, $_POST, $data, $event_id );
		do_action( 'mc_update_event_post', $post_id, $_POST, $data, $event_id );
		if ( mc_switch_sites() ) {
			restore_current_blog();
		}
	}

	return $post_id;
}

/**
 * Add post meta data to an event post.
 *
 * @param int   $post_id Post ID.
 * @param array $post Post object.
 * @param array $data Event POST data or event data.
 * @param int   $event_id Event ID.
 */
function mc_add_post_meta_data( $post_id, $post, $data, $event_id ) {
	// access features for the event.
	$description = isset( $data['event_desc'] ) ? $data['event_desc'] : '';
	$image       = isset( $data['event_image'] ) ? esc_url_raw( $data['event_image'] ) : '';
	$guid        = get_post_meta( $post_id, '_mc_guid', true );
	if ( '' === $guid ) {
		$guid = md5( $post_id . $event_id . $data['event_title'] );
		update_post_meta( $post_id, '_mc_guid', $guid );
	}
	update_post_meta( $post_id, '_mc_event_shortcode', $data['shortcode'] );
	$events_access = '';
	if ( isset( $_POST['events_access'] ) ) {
		$events_access = $_POST['events_access'];
	} else {
		// My Calendar Rest API.
		if ( isset( $post['data'] ) && isset( $post['data']['events_access'] ) ) {
			$events_access = $post['data']['events_access'];
		}
	}
	$time_label = '';
	if ( isset( $_POST['event_time_label'] ) ) {
		$time_label = $_POST['event_time_label'];
	} else {
		// My Calendar Rest API.
		if ( isset( $post['data'] ) && isset( $post['data']['event_time_label'] ) ) {
			$time_label = $post['data']['event_time_label'];
		}
	}
	update_post_meta( $post_id, '_mc_event_access', $events_access );
	update_post_meta( $post_id, '_event_time_label', $time_label );

	$mc_event_id = get_post_meta( $post_id, '_mc_event_id', true );
	if ( ! $mc_event_id ) {
		update_post_meta( $post_id, '_mc_event_id', $event_id );
	}
	update_post_meta( $post_id, '_mc_event_desc', $description );
	update_post_meta( $post_id, '_mc_event_image', $image );
	// This is only used by My Tickets, so only the first date occurrence is required.
	$event_date = ( is_array( $data['event_begin'] ) ) ? $data['event_begin'][0] : $data['event_begin'];
	update_post_meta( $post_id, '_mc_event_date', strtotime( $event_date ) );
	$location_id = ( isset( $post['location_preset'] ) ) ? (int) $post['location_preset'] : false;
	if ( $location_id ) { // only change location ID if dropdown set.
		update_post_meta( $post_id, '_mc_event_location', $location_id );
		mc_update_event( 'event_location', $location_id, $event_id );
	}
	update_post_meta( $post_id, '_mc_event_data', $data );
}

/**
 * Create a post for My Calendar event data on save
 *
 * @param array $data Saved event data.
 * @param int   $event_id Newly-saved event ID.
 *
 * @return int newly created post ID
 */
function mc_create_event_post( $data, $event_id ) {
	$post_id = mc_get_event_post( $event_id );
	if ( ! $post_id ) {
		$categories = mc_get_categories( $event_id );
		$terms      = array();
		$term       = null;
		$privacy    = 'publish';
		foreach ( $categories as $category ) {
			$term = mc_get_category_detail( $category, 'category_term' );
			// if any selected category is private, make private.
			if ( 'private' !== $privacy ) {
				$privacy = ( '1' === mc_get_category_detail( $category, 'category_private' ) ) ? 'private' : 'publish';
			}
			$terms[] = (int) $term;
		}

		$title             = $data['event_title'];
		$template          = apply_filters( 'mc_post_template', 'details', $term );
		$data['shortcode'] = "[my_calendar_event event='$event_id' template='$template' list='']";
		$description       = isset( $data['event_desc'] ) ? $data['event_desc'] : '';
		$excerpt           = isset( $data['event_short'] ) ? $data['event_short'] : '';
		$location_id       = ( isset( $_POST['location_preset'] ) ) ? (int) $_POST['location_preset'] : 0;
		$post_status       = $privacy;
		$auth              = $data['event_author'];
		$type              = 'mc-events';
		$my_post           = array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => $post_status,
			'post_author'  => $auth,
			'post_name'    => sanitize_title( $title ),
			'post_date'    => current_time( 'Y-m-d H:i:s' ),
			'post_type'    => $type,
			'post_excerpt' => $excerpt,
		);
		$post_id           = wp_insert_post( $my_post );
		wp_set_object_terms( $post_id, $terms, 'mc-event-category' );
		$attachment_id = ( isset( $_POST['event_image_id'] ) && is_numeric( $_POST['event_image_id'] ) ) ? $_POST['event_image_id'] : false;
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
		mc_update_event( 'event_post', $post_id, $event_id );
		mc_update_event( 'event_location', $location_id, $event_id );
		mc_add_post_meta_data( $post_id, $_POST, $data, $event_id );
		do_action( 'mc_update_event_post', $post_id, $_POST, $data, $event_id );
		wp_publish_post( $post_id );
	}

	return $post_id;
}

/**
 * Delete event posts when event is deleted
 *
 * @param array $deleted Array of event IDs.
 */
function mc_event_delete_posts( $deleted ) {
	foreach ( $deleted as $delete ) {
		$posts = get_posts(
			array(
				'post_type'  => 'mc-events',
				'meta_key'   => '_mc_event_id',
				'meta_value' => $delete,
			)
		);
		if ( isset( $posts[0] ) && is_object( $posts[0] ) ) {
			$post_id = $posts[0]->ID;
			wp_delete_post( $post_id, true );
		}
	}
}

/**
 * Delete custom post type associated with event
 *
 * @param int $event_id Event ID.
 * @param int $post_id Post ID.
 */
function mc_event_delete_post( $event_id, $post_id ) {
	do_action( 'mc_deleted_post', $event_id, $post_id );
	wp_delete_post( $post_id, true );
}

/**
 * Update a single field in an event.
 *
 * @param string               $field database column.
 * @param mixed                $data value to be saved.
 * @param mixed string/integer $event could be integer or string.
 * @param string               $type signifier representing data type of $data (e.g. %d or %s).
 *
 * @return database result
 */
function mc_update_event( $field, $data, $event, $type = '%d' ) {
	global $wpdb;
	$field = sanitize_key( $field );
	if ( '%d' === $type ) {
		$sql = 'UPDATE ' . my_calendar_table() . " SET $field = %d WHERE event_id=%d";
	} elseif ( '%s' === $type ) {
		$sql = 'UPDATE ' . my_calendar_table() . " SET $field = %s WHERE event_id=%d";
	} else {
		$sql = 'UPDATE ' . my_calendar_table() . " SET $field = %f WHERE event_id=%d";
	}
	$result = $wpdb->query( $wpdb->prepare( $sql, $data, $event ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return $result;
}

/**
 * Handle a bulk action.
 *
 * @param string $action type of action.
 *
 * @return array bulk action details.
 */
function mc_bulk_action( $action ) {
	global $wpdb;
	$events  = $_POST['mass_edit'];
	$i       = 0;
	$total   = 0;
	$ids     = array();
	$prepare = array();

	foreach ( $events as $value ) {
		$value = (int) $value;
		$total = count( $events );
		if ( 'delete' === $action ) {
			$result = $wpdb->get_results( $wpdb->prepare( 'SELECT event_author FROM ' . my_calendar_table() . ' WHERE event_id = %d', $value ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( mc_can_edit_event( $value ) ) {
				$occurrences = 'DELETE FROM ' . my_calendar_event_table() . ' WHERE occur_event_id = %d';
				$wpdb->query( $wpdb->prepare( $occurrences, $value ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$ids[]     = (int) $value;
				$prepare[] = '%d';
				$i ++;
			}
		}
		if ( 'delete' !== $action && current_user_can( 'mc_approve_events' ) ) {
			$ids[]     = (int) $value;
			$prepare[] = '%d';
			$i ++;
		}
	}
	$prepared = implode( ',', $prepare );

	switch ( $action ) {
		case 'delete':
			$sql = 'DELETE FROM ' . my_calendar_table() . ' WHERE event_id IN (' . $prepared . ')';
			break;
		case 'unarchive':
			$sql = 'UPDATE ' . my_calendar_table() . ' SET event_status = 1 WHERE event_id IN (' . $prepared . ')';
			break;
		case 'archive':
			$sql = 'UPDATE ' . my_calendar_table() . ' SET event_status = 0 WHERE event_id IN (' . $prepared . ')';
			break;
		case 'approve':
			$sql = 'UPDATE ' . my_calendar_table() . ' SET event_approved = 1 WHERE event_id IN (' . $prepared . ')';
			break;
		case 'trash':
			$sql = 'UPDATE ' . my_calendar_table() . ' SET event_approved = 2 WHERE event_id IN (' . $prepared . ')';
			break;
		case 'unspam':
			$sql = 'UPDATE ' . my_calendar_table() . ' SET event_flagged = 0 WHERE event_id IN (' . $prepared . ')';
			// send notifications.
			foreach ( $ids as $id ) {
				$post_ID   = mc_get_event_post( $id );
				$submitter = get_post_meta( $post_ID, '_submitter_details', true );
				if ( is_array( $submitter ) && ! empty( $submitter ) ) {
					$name  = $submitter['first_name'] . ' ' . $submitter['last_name'];
					$email = $submitter['email'];
					do_action( 'mcs_complete_submission', $name, $email, $id, 'edit' );
				}
			}
			break;
	}

	$result = $wpdb->query( $wpdb->prepare( $sql, $ids ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	mc_update_count_cache();
	$results = array(
		'count'  => $i,
		'total'  => $total,
		'ids'    => $ids,
		'result' => $result,
	);

	return mc_bulk_message( $results, $action );
}

/**
 * Generate a notification for bulk actions.
 *
 * @param array  $results of bulk action.
 * @param string $action Type of action.
 *
 * @return string message
 */
function mc_bulk_message( $results, $action ) {
	$count  = $results['count'];
	$total  = $results['total'];
	$ids    = $results['ids'];
	$result = $results['result'];

	switch ( $action ) {
		case 'delete':
			// Translators: Number of events deleted, number selected.
			$success = __( '%1$d events deleted successfully out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not been deleted. Please investigate.', 'my-calendar' );
			break;
		case 'trash':
			// Translators: Number of events trashed, number of events selected.
			$success = __( '%1$d events trashed successfully out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not been trashed. Please investigate.', 'my-calendar' );
			break;
		case 'approve':
			// Translators: Number of events approved, number of events selected.
			$success = __( '%1$d events approved out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not been approved. Please investigate.', 'my-calendar' );
			break;
		case 'archive':
			// Translators: Number of events arcnived, number of events selected.
			$success = __( '%1$d events archived successfully out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not been archived. Please investigate.', 'my-calendar' );
			break;
		case 'unarchive':
			// Translators: Number of events removed from archive, number of events selected.
			$success = __( '%1$d events removed from archive successfully out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not been removed from the archive. Please investigate.', 'my-calendar' );
			break;
		case 'unspam':
			// Translators: Number of events removed from archive, number of events selected.
			$success = __( '%1$d events successfully unmarked as spam out of %2$d selected', 'my-calendar' );
			$error   = __( 'Your events have not unmarked as spam. Please investigate.', 'my-calendar' );
			break;
	}

	if ( 0 !== $result && false !== $result ) {
		do_action( 'mc_mass_' . $action . '_events', $ids );
		$message = mc_show_notice( sprintf( $success, $count, $total ) );
	} else {
		$message = mc_show_error( $error, false );
	}

	return $message;
}

/**
 * Display an error message.
 *
 * @param string  $message Error message.
 * @param boolean $echo Echo or return. Default true (echo).
 *
 * @return string
 */
function mc_show_error( $message, $echo = true ) {
	if ( trim( $message ) === '' ) {
		return '';
	}
	$message = strip_tags( $message, mc_admin_strip_tags() );
	$message = "<div class='error'><p>$message</p></div>";
	if ( $echo ) {
		echo $message;
	} else {
		return $message;
	}
}


/**
 * Display an update message.
 *
 * @param string         $message Update message.
 * @param boolean        $echo Echo or return. Default true (echo).
 * @param boolean|string $code Message code.
 *
 * @return string
 */
function mc_show_notice( $message, $echo = true, $code = false ) {
	if ( trim( $message ) === '' ) {
		return '';
	}
	$message = strip_tags( apply_filters( 'mc_filter_notice', $message, $code ), mc_admin_strip_tags() );
	$message = "<div class='updated'><p>$message</p></div>";
	if ( $echo ) {
		echo $message;
	} else {
		return $message;
	}
}

/**
 * Generate form for listing events that are editable by current user
 */
function my_calendar_manage() {
	my_calendar_check();
	global $wpdb;
	if ( isset( $_GET['mode'] ) && 'delete' === $_GET['mode'] ) {
		$event_id = ( isset( $_GET['event_id'] ) ) ? absint( $_GET['event_id'] ) : false;
		$result   = $wpdb->get_results( $wpdb->prepare( 'SELECT event_title, event_author FROM ' . my_calendar_table() . ' WHERE event_id=%d', $event_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( mc_can_edit_event( $event_id ) ) {
			if ( isset( $_GET['date'] ) ) {
				$event_instance = (int) $_GET['date'];
				$inst           = $wpdb->get_var( $wpdb->prepare( 'SELECT occur_begin FROM ' . my_calendar_event_table() . ' WHERE occur_id=%d', $event_instance ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$instance_date  = '(' . mc_date( 'Y-m-d', mc_strtotime( $inst ), false ) . ')';
			} else {
				$instance_date = '';
			} ?>
			<div class="error">
				<form action="<?php echo admin_url( 'admin.php?page=my-calendar-manage' ); ?>" method="post">
					<p><strong><?php _e( 'Delete Event', 'my-calendar' ); ?>:</strong> <?php _e( 'Are you sure you want to delete this event?', 'my-calendar' ); ?>
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
						<input type="hidden" value="delete" name="event_action" />
						<?php
						if ( ! empty( $_GET['date'] ) ) {
							?>
						<input type="hidden" name="event_instance" value="<?php echo (int) $_GET['date']; ?>"/>
							<?php
						}
						if ( isset( $_GET['ref'] ) ) {
							?>
						<input type="hidden" name="ref" value="<?php echo esc_url( $_GET['ref'] ); ?>" />
							<?php
						}
						?>
						<input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
						<?php
							$event_info = ' &quot;' . stripslashes( $result[0]['event_title'] ) . "&quot; $instance_date";
							// Translators: Title & date of event to delete.
							$delete_text = sprintf( __( 'Delete %s', 'my-calendar' ), $event_info );
						?>
						<input type="submit" name="submit" class="button-secondary delete" value="<?php echo esc_attr( $delete_text ); ?>"/>
				</form>
			</div>
			<?php
		} else {
			mc_show_error( __( 'You do not have permission to delete that event.', 'my-calendar' ) );
		}
	}

	// Approve and show an Event ...originally by Roland.
	if ( isset( $_GET['mode'] ) && 'publish' === $_GET['mode'] ) {
		if ( current_user_can( 'mc_approve_events' ) ) {
			$event_id = absint( $_GET['event_id'] );
			$wpdb->get_results( $wpdb->prepare( 'UPDATE ' . my_calendar_table() . ' SET event_approved = 1 WHERE event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			mc_update_count_cache();
		} else {
			mc_show_error( __( 'You do not have permission to approve that event.', 'my-calendar' ) );
		}
	}

	// Reject and hide an Event ...by Roland.
	if ( isset( $_GET['mode'] ) && 'reject' === $_GET['mode'] ) {
		if ( current_user_can( 'mc_approve_events' ) ) {
			$event_id = absint( $_GET['event_id'] );
			$wpdb->get_results( $wpdb->prepare( 'UPDATE ' . my_calendar_table() . ' SET event_approved = 2 WHERE event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			mc_update_count_cache();
		} else {
			mc_show_error( __( 'You do not have permission to trash that event.', 'my-calendar' ) );
		}
	}

	if ( ! empty( $_POST['mass_edit'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( 'Security check failed' );
		}
		if ( isset( $_POST['mass_delete'] ) ) {
			$results = mc_bulk_action( 'delete' );
			echo $results;
		}

		if ( isset( $_POST['mass_trash'] ) ) {
			$results = mc_bulk_action( 'trash' );
			echo $results;
		}

		if ( isset( $_POST['mass_approve'] ) ) {
			$results = mc_bulk_action( 'approve' );
			echo $results;
		}

		if ( isset( $_POST['mass_archive'] ) ) {
			$results = mc_bulk_action( 'archive' );
			echo $results;
		}

		if ( isset( $_POST['mass_undo_archive'] ) ) {
			$results = mc_bulk_action( 'unarchive' );
			echo $results;
		}

		if ( isset( $_POST['mass_not_spam'] ) ) {
			$results = mc_bulk_action( 'unspam' );
			echo $results;
		}
	}
	?>
	<div class='wrap my-calendar-admin'>
		<h1 id="mc-manage" class="wp-heading-inline"><?php _e( 'Manage Events', 'my-calendar' ); ?></h1>
		<a href="<?php echo admin_url( 'admin.php?page=my-calendar' ); ?>" class="page-title-action"><?php _e( 'Add New', 'my-calendar' ); ?></a>
		<hr class="wp-header-end">

		<div class="postbox-container jcd-wide">
			<div class="metabox-holder">
				<div class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h2><?php _e( 'My Events', 'my-calendar' ); ?></h2>

						<div class="inside">
							<?php mc_list_events(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		$problems = mc_list_problems();
		mc_show_sidebar( '', $problems );
		?>
	</div>
	<?php
}

/**
 * Generate inner wrapper for editing and managing events
 */
function my_calendar_edit() {
	mc_check_imports();

	$action   = ! empty( $_POST['event_action'] ) ? $_POST['event_action'] : '';
	$event_id = ! empty( $_POST['event_id'] ) ? $_POST['event_id'] : '';

	if ( isset( $_GET['mode'] ) ) {
		$action = $_GET['mode'];
		if ( 'edit' === $action || 'copy' === $action ) {
			$event_id = (int) $_GET['event_id'];
		}
	}

	if ( isset( $_POST['event_action'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( 'Security check failed' );
		}

		global $mc_output;
		$count = 0;

		if ( isset( $_POST['event_begin'] ) && is_array( $_POST['event_begin'] ) ) {
			$count = count( $_POST['event_begin'] );
		} else {
			$response = my_calendar_save( $action, $mc_output, (int) $_POST['event_id'] );
			echo $response['message'];
		}
		for ( $i = 0; $i < $count; $i ++ ) {
			$mc_output = mc_check_data( $action, $_POST, $i );
			if ( 'add' === $action || 'copy' === $action ) {
				$response = my_calendar_save( $action, $mc_output );
			} else {
				$response = my_calendar_save( $action, $mc_output, (int) $_POST['event_id'] );
			}
			echo $response['message'];
		}
		if ( isset( $_POST['ref'] ) ) {
			$url = esc_url( urldecode( $_POST['ref'] ) );
			echo "<p class='return'><a href='$url'>" . __( 'Return to Calendar', 'my-calendar' ) . '</a></p>';
		}
	}
	?>

	<div class="wrap my-calendar-admin">
	<?php
	my_calendar_check_db();
	if ( '2' === get_site_option( 'mc_multisite' ) ) {
		if ( '0' === get_option( 'mc_current_table' ) ) {
			$message = __( 'Currently editing your local calendar', 'my-calendar' );
		} else {
			$message = __( 'Currently editing your central calendar', 'my-calendar' );
		}
		mc_show_notice( $message );
	}
	if ( 'edit' === $action ) {
		?>
		<h1><?php _e( 'Edit Event', 'my-calendar' ); ?></h1>
		<?php
		if ( empty( $event_id ) ) {
			mc_show_error( __( 'You must provide an event ID to edit events.', 'my-calendar' ) );
		} else {
			mc_edit_event_form( 'edit', $event_id );
		}
	} elseif ( 'copy' === $action ) {
		?>
		<h1><?php _e( 'Copy Event', 'my-calendar' ); ?></h1>
		<?php
		if ( empty( $event_id ) ) {
			mc_show_error( __( 'You must provide an event ID to copy events.', 'my-calendar' ) );
		} else {
			mc_edit_event_form( 'copy', $event_id );
		}
	} else {
		?>
		<h1><?php _e( 'Add Event', 'my-calendar' ); ?></h1>
		<?php
		mc_edit_event_form();
	}
	mc_show_sidebar();
	?>
	</div>
	<?php
}

/**
 * Save an event to the database
 *
 * @param string $action Type of action.
 * @param array  $output Checked event data.
 * @param int    $event_id Event ID.
 *
 * @return string message
 */
function my_calendar_save( $action, $output, $event_id = false ) {
	global $wpdb;
	$proceed = (bool) $output[0];
	$message = '';
	$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%f', '%f' );

	if ( ( 'add' === $action || 'copy' === $action ) && true === $proceed ) {
		$add  = $output[2]; // add format here.
		$cats = $add['event_categories'];

		unset( $add['event_categories'] );
		$add      = apply_filters( 'mc_before_save_insert', $add );
		$result   = $wpdb->insert( my_calendar_table(), $add, $formats );
		$event_id = $wpdb->insert_id;
		mc_increment_event( $event_id );
		mc_set_category_relationships( $cats, $event_id );
		if ( ! $result ) {
			$message = mc_show_error( __( "I'm sorry! I couldn't add that event to the database.", 'my-calendar' ), false );
		} else {
			// do an action using the $action and processed event data.
			$data        = $add;
			$event_error = '';
			mc_event_post( $action, $data, $event_id, $result );
			do_action( 'mc_save_event', $action, $data, $event_id, $result );

			if ( 'true' === get_option( 'mc_event_mail' ) ) {
				// insert_id is last occurrence inserted in the db.
				$event = mc_get_first_event( $event_id );
				my_calendar_send_email( $event );
			}
			if ( '0' === (string) $add['event_approved'] ) {
				$message = mc_show_notice( __( 'Event draft saved.', 'my-calendar' ), false, 'draft-saved' );
			} else {
				// jd_doTwitterAPIPost was changed to wpt_post_to_twitter on 1.19.2017.
				if ( function_exists( 'wpt_post_to_twitter' ) && isset( $_POST['mc_twitter'] ) && '' !== trim( $_POST['mc_twitter'] ) ) {
					wpt_post_to_twitter( stripslashes( $_POST['mc_twitter'] ) );
				}
				if ( mc_get_uri( 'boolean' ) ) {
					$event_ids   = mc_get_occurrences( $event_id );
					$event_link  = mc_get_details_link( $event_ids[0]->occur_id );
					$event_error = mc_error_check( $event_ids[0]->occur_event_id );
				} else {
					$event_link = false;
				}
				if ( '' !== trim( $event_error ) ) {
					$message = $event_error;
				} else {
					$message = __( 'Event added. It will now show on the calendar.', 'my-calendar' );
					if ( false !== $event_link ) {
						// Translators: URL to view event in calendar.
						$message .= sprintf( __( ' <a href="%s">View Event</a>', 'my-calendar' ), $event_link );
					}
					$message = mc_show_notice( $message, false, 'new-event' );
				}
			}
		}
	}

	if ( 'edit' === $action && true === $proceed ) {
		$result = true;
		// Translators: URL to view calendar.
		$url = sprintf( __( 'View <a href="%s">your calendar</a>.', 'my-calendar' ), mc_get_uri() );
		if ( mc_can_edit_event( $event_id ) ) {
			$update = $output[2];
			$cats   = $update['event_categories'];
			unset( $update['event_categories'] );
			mc_update_category_relationships( $cats, $event_id );

			$update       = apply_filters( 'mc_before_save_update', $update, $event_id );
			$endtime      = mc_date( 'H:i:00', mc_strtotime( $update['event_endtime'] ), false );
			$prev_eb      = ( isset( $_POST['prev_event_begin'] ) ) ? $_POST['prev_event_begin'] : '';
			$prev_et      = ( isset( $_POST['prev_event_time'] ) ) ? $_POST['prev_event_time'] : '';
			$prev_ee      = ( isset( $_POST['prev_event_end'] ) ) ? $_POST['prev_event_end'] : '';
			$prev_eet     = ( isset( $_POST['prev_event_endtime'] ) ) ? $_POST['prev_event_endtime'] : '';
			$update_time  = mc_date( 'H:i:00', mc_strtotime( $update['event_time'] ), false );
			$date_changed = ( $update['event_begin'] !== $prev_eb || $update_time !== $prev_et || $update['event_end'] !== $prev_ee || ( $endtime !== $prev_eet && ( '' !== $prev_eet && '23:59:59' !== $endtime ) ) ) ? true : false;
			if ( isset( $_POST['event_instance'] ) ) {
				// compares the information sent to the information saved for a given event.
				$is_changed     = mc_compare( $update, $event_id );
				$event_instance = (int) $_POST['event_instance'];
				if ( $is_changed ) {
					// if changed, create new event, match group id, update instance to reflect event connection, same group id.
					// if group ID == 0, need to add group ID to both records.
					// if a single instance is edited, it should not inherit recurring settings from parent.
					$update['event_recur'] = 'S1';
					if ( 0 === (int) $update['event_group_id'] ) {
						$update['event_group_id'] = $event_id;
						mc_update_data( $event_id, 'event_group_id', $event_id );
					}
					// retain saved location unless actively changed.
					if ( isset( $_POST['preset_location'] ) && 'none' === $_POST['location_preset'] ) {
						$location                 = absint( $_POST['preset_location'] );
						$update['event_location'] = $location;
					}
					$wpdb->insert( my_calendar_table(), $update, $formats );
					// need to get this variable into URL for form submit.
					$new_event = $wpdb->insert_id;
					mc_update_category_relationships( $cats, $new_event );
					$result = mc_update_instance( $event_instance, $new_event, $update );
				} else {
					if ( $update['event_begin'][0] === $_POST['prev_event_begin'] && $update['event_end'][0] === $_POST['prev_event_end'] ) {
						// There were no changes at all.
					} else {
						// Only dates were changed.
						$result  = mc_update_instance( $event_instance, $event_id, $update );
						$message = mc_show_notice( __( 'Date/time information for this event has been updated.', 'my-calendar' ) . " $url", false, 'date-updated' );
					}
				}
			} else {
				$result = $wpdb->update(
					my_calendar_table(),
					$update,
					array(
						'event_id' => $event_id,
					),
					$formats,
					'%d'
				);
				if ( isset( $_POST['prev_event_repeats'] ) && isset( $_POST['prev_event_recur'] ) ) {
					$recur_changed = ( $update['event_repeats'] !== $_POST['prev_event_repeats'] || $update['event_recur'] !== $_POST['prev_event_recur'] ) ? true : false;
				} else {
					$recur_changed = false;
				}
				if ( $date_changed || $recur_changed ) {
					// Function mc_increment_event uses previous events and re-uses same ID if new has same date as old event.
					$instances = mc_get_instances( $event_id );
					mc_delete_instances( $event_id );
					// Delete previously created custom & deleted instance records.
					$post_ID = mc_get_data( 'event_post', $event_id );
					delete_post_meta( $post_ID, '_mc_custom_instances' );
					delete_post_meta( $post_ID, '_mc_deleted_instances' );
					mc_increment_event( $event_id, array(), false, $instances );
				}
			}
			$data = $update;
			mc_event_post( $action, $data, $event_id, $result );
			do_action( 'mc_save_event', $action, $data, $event_id, $result );
			if ( false === $result ) {
				$message = mc_show_error( __( 'Your event was not updated.', 'my-calendar' ) . " $url", false );
			} else {
				// do an action using the $action and processed event data.
				$event_approved = ( current_user_can( 'mc_approve_events' ) ) ? 1 : 0;
				// check for event_approved provides support for older versions of My Calendar Pro.
				if ( isset( $post['event_approved'] ) && $post['event_approved'] !== $event_approved ) {
					$event_approved = absint( $post['event_approved'] );
				}
				if ( isset( $_POST['prev_event_status'] ) ) {
					// Don't execute transition actions if prev status not known.
					do_action( 'mc_transition_event', (int) $_POST['prev_event_status'], $event_approved, $action, $data, $event_id );
				}
				$message = mc_show_notice( __( 'Event updated successfully', 'my-calendar' ) . ". $url", false, 'event-updated' );
			}
		} else {
			$message = mc_show_error( __( 'You do not have sufficient permissions to edit that event.', 'my-calendar' ), false );
		}
	}

	$message        = $message . "\n" . $output[3];
	$saved_response = array(
		'event_id' => $event_id,
		'message'  => $message,
	);
	mc_update_count_cache();

	return apply_filters( 'mc_event_saved_message', $saved_response );
}

/**
 * Set up new category relationships for assigned cats to an event
 *
 * @param array $cats array of category IDs.
 * @param int   $event_id My Calendar event ID.
 */
function mc_set_category_relationships( $cats, $event_id ) {
	global $wpdb;
	if ( is_array( $cats ) ) {
		foreach ( $cats as $cat ) {
			$wpdb->insert(
				my_calendar_category_relationships_table(),
				array(
					'event_id'    => $event_id,
					'category_id' => $cat,
				),
				array( '%d', '%d' )
			);
		}
	}
}

/**
 * Update existing category relationships for an event
 *
 * @param array $cats array of category IDs.
 * @param int   $event_id My Calendar event ID.
 */
function mc_update_category_relationships( $cats, $event_id ) {
	global $wpdb;
	$old_cats = mc_get_categories( $event_id, 'testing' );
	if ( $old_cats === $cats ) {
		return;
	}
	$wpdb->delete( my_calendar_category_relationships_table(), array( 'event_id' => $event_id ), '%d' );

	if ( is_array( $cats ) && ! empty( $cats ) ) {
		foreach ( $cats as $cat ) {
			$wpdb->insert(
				my_calendar_category_relationships_table(),
				array(
					'event_id'    => $event_id,
					'category_id' => $cat,
				),
				array( '%d', '%d' )
			);
		}
	}
}

/**
 * Check an event for any occurrence overlap problems. Used in admin only.
 *
 * @param integer $event_id Event ID.
 *
 * @return string with edit link to go to event.
 */
function mc_error_check( $event_id ) {
	$data      = mc_form_data( $event_id );
	$test      = ( $data ) ? mc_test_occurrence_overlap( $data, true ) : '';
	$args      = array(
		'mode'     => 'edit',
		'event_id' => $event_id,
	);
	$edit_link = ' <a href="' . esc_url( add_query_arg( $args, admin_url( 'admin.php?page=my-calendar' ) ) ) . '">' . __( 'Edit Event', 'my-calendar' ) . '</a>';
	$test      = ( '' !== $test ) ? str_replace( '</p></div>', "$edit_link</p></div>", $test ) : $test;

	return $test;
}

/**
 * Delete an event given event ID
 *
 * @param int $event_id Event ID.
 *
 * @return string message
 */
function mc_delete_event( $event_id ) {
	global $wpdb;
	// Deal with deleting an event from the database.
	if ( empty( $event_id ) ) {
		$message = mc_show_error( __( "You can't delete an event if you haven't submitted an event id", 'my-calendar' ), false );
	} else {
		$event_id = absint( $event_id );
		$event_in = false;
		$instance = false;
		$post_id  = mc_get_data( 'event_post', $event_id );
		if ( empty( $_POST['event_instance'] ) ) {
			// Delete from instance table.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_event_table() . ' WHERE occur_event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			// Delete from event table.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_table() . ' WHERE event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$result = $wpdb->get_results( $wpdb->prepare( 'SELECT event_id FROM ' . my_calendar_table() . ' WHERE event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			// Delete category relationship records.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_category_relationships_table() . ' WHERE event_id=%d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$event_in = absint( $_POST['event_instance'] );
			$result   = $wpdb->get_results( $wpdb->prepare( 'DELETE FROM ' . my_calendar_event_table() . ' WHERE occur_id=%d', $event_in ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$instance = true;
		}
		if ( empty( $result ) || empty( $result[0]->event_id ) ) {
			// Do an action using the event_id.
			if ( $instance ) {
				do_action( 'mc_delete_event_instance', $event_id, $post_id, $event_in );
			} else {
				do_action( 'mc_delete_event', $event_id, $post_id );
			}
			$message = mc_show_notice( __( 'Event deleted successfully', 'my-calendar' ), false, 'event-deleted' );
		} else {
			$message = mc_show_error( __( 'Despite issuing a request to delete, the event still remains in the database. Please investigate.', 'my-calendar' ), false );
		}
	}

	return $message;
}

/**
 * Get form data for an event ID
 *
 * @param mixed int/boolean $event_id My Calendar event ID or false if submission had errors.
 *
 * @return mixed array/object submitted or saved data
 */
function mc_form_data( $event_id = false ) {
	global $wpdb, $submission;
	if ( false !== $event_id ) {
		$event_id = absint( $event_id );
		$data     = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_table() . ' WHERE event_id=%d LIMIT 1', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( empty( $data ) ) {
			return mc_show_error( __( "Sorry! We couldn't find an event with that ID.", 'my-calendar' ), false );
		}
		$data = $data[0];
		// Recover users entries if there was an error.
		if ( ! empty( $submission ) ) {
			$data = $submission;
		}
	} else {
		// Deal with possibility that form was submitted but not saved due to error - recover user's entries.
		$data = $submission;
	}

	return $data;
}

/**
 * The event edit form for the manage events admin page
 *
 * @param string            $mode add, edit, or copy.
 * @param mixed int/boolean $event_id My Calendar event ID (false for new events).
 *
 * @return string HTML form
 */
function mc_edit_event_form( $mode = 'add', $event_id = false ) {
	global $submission;

	if ( $event_id && ! mc_can_edit_event( $event_id ) ) {
		mc_show_error( __( 'You do not have permission to edit this event.', 'my-calendar' ) );

		return;
	}

	if ( $event_id ) {
		$data = mc_form_data( $event_id );
	} else {
		$data = $submission;
	}

	apply_filters( 'mc_event_notices', '', $data, $event_id );

	if ( is_object( $data ) && 1 !== (int) $data->event_approved && 'edit' === $mode ) {
		if ( 0 === (int) $data->event_approved ) {
			mc_show_error( __( '<strong>Draft</strong>: Publish this event to show it on the calendar.', 'my-calendar' ) );
		} else {
			mc_show_error( __( '<strong>Trash</strong>: Remove from the trash to show this event on the calendar.', 'my-calendar' ) );
		}
	}

	mc_form_fields( $data, $mode, $event_id );
}

/**
 * Get the instance-specific information about a single event instance.
 *
 * @param int $instance_id Event instance ID.
 *
 * @return query result
 */
function mc_get_instance_data( $instance_id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$result = $mcdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_event_table() . ' WHERE occur_id = %d', $instance_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return $result;
}

/**
 * Whether we should show the edit fields for an enabled block of fields.
 *
 * @param string $field Name of field group.
 *
 * @return string.
 */
function mc_show_edit_block( $field ) {
	$admin = ( 'true' === get_option( 'mc_input_options_administrators' ) && current_user_can( 'manage_options' ) ) ? true : false;
	$input = get_option( 'mc_input_options' );
	// Array of all options in off position.
	$defaults = array(
		'event_location_dropdown' => 'on',
		'event_short'             => 'on',
		'event_desc'              => 'on',
		'event_category'          => 'on',
		'event_image'             => 'on',
		'event_link'              => 'on',
		'event_recurs'            => 'on',
		'event_open'              => 'on',
		'event_location'          => 'off',
		'event_specials'          => 'on',
		'event_access'            => 'on',
		'event_host'              => 'on',
	);

	$input  = array_merge( $defaults, $input );
	$user   = get_current_user_id();
	$screen = get_current_screen();
	$option = $screen->get_option( 'mc_show_on_page', 'option' );
	$show   = get_user_meta( $user, $option, true );
	if ( empty( $show ) || $show < 1 ) {
		$show = $screen->get_option( 'mc_show_on_page', 'default' );
	}
	// if this doesn't exist in array, leave it on.
	if ( ! isset( $input[ $field ] ) || ! isset( $show[ $field ] ) ) {
		return true;
	}
	if ( $admin ) {
		if ( isset( $show[ $field ] ) && 'on' === $show[ $field ] ) {
			return true;
		} else {
			return false;
		}
	} else {
		if ( 'off' === $input[ $field ] || '' === $input[ $field ] ) {
			return false;
		} elseif ( 'off' === $show[ $field ] ) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * Does an editing block contain visible fields.
 *
 * @param string $field Name of field group.
 *
 * @return bool
 */
function mc_edit_block_is_visible( $field ) {
	$admin = ( 'true' === get_option( 'mc_input_options_administrators' ) && current_user_can( 'manage_options' ) ) ? true : false;
	$input = get_option( 'mc_input_options' );
	// Array of all options in off position.
	$defaults = array(
		'event_location_dropdown' => 'on',
		'event_short'             => 'on',
		'event_desc'              => 'on',
		'event_category'          => 'on',
		'event_image'             => 'on',
		'event_link'              => 'on',
		'event_recurs'            => 'on',
		'event_open'              => 'on',
		'event_location'          => 'off',
		'event_specials'          => 'on',
		'event_access'            => 'on',
		'event_host'              => 'on',
	);

	$input  = array_merge( $defaults, $input );
	$user   = get_current_user_id();
	$screen = get_current_screen();
	$option = $screen->get_option( 'mc_show_on_page', 'option' );
	$show   = get_user_meta( $user, $option, true );
	if ( empty( $show ) || $show < 1 ) {
		$show = $screen->get_option( 'mc_show_on_page', 'default' );
	}
	// if this doesn't exist in array, return false. Field is hidden.
	if ( ! isset( $input[ $field ] ) || ! isset( $show[ $field ] ) ) {
		return false;
	}
	if ( $admin ) {
		if ( isset( $show[ $field ] ) && 'on' === $show[ $field ] ) {
			return true;
		} else {
			return false;
		}
	} else {
		if ( 'off' === $input[ $field ] || '' === $input[ $field ] ) {
			return false;
		} elseif ( 'off' === $show[ $field ] ) {
			return false;
		} else {
			return true;
		}
	}
}

/**
 * Determine whether any of a set of fields are enabled.
 *
 * @param array $fields Array of field keys.
 *
 * @return bool
 */
function mc_show_edit_blocks( $fields ) {
	foreach ( $fields as $field ) {
		if ( mc_edit_block_is_visible( $field ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Show a block of enabled fields.
 *
 * @param string             $field name of field group.
 * @param boolean            $has_data Whether fields have data.
 * @param mixed array/object $data Current data.
 * @param boolean            $echo whether to return or echo.
 * @param string             $default Default string value.
 *
 * @return string.
 */
function mc_show_block( $field, $has_data, $data, $echo = true, $default = '' ) {
	global $user_ID;
	$return     = '';
	$checked    = '';
	$value      = '';
	$show_block = mc_show_edit_block( $field );
	$pre        = '<div class="ui-sortable meta-box-sortables"><div class="postbox">';
	$post       = '</div></div>';
	switch ( $field ) {
		case 'event_host':
			if ( $show_block ) {
				$host   = ( empty( $data->event_host ) ) ? $user_ID : $data->event_host;
				$select = mc_selected_users( $host, 'hosts' );
				$return = '
					<p>
					<label for="e_host">' . __( 'Host', 'my-calendar' ) . '</label>
					<select id="e_host" name="event_host">' .
						$select
					. '</select>
				</p>';
			}
			break;
		case 'event_author':
			if ( $show_block && is_object( $data ) && ( '0' === $data->event_author || ! get_user_by( 'ID', $data->event_author ) ) ) {
				$author = ( empty( $data->event_author ) ) ? $user_ID : $data->event_author;
				$select = mc_selected_users( $author, 'authors' );
				$return = '
					<p>
					<label for="e_author">' . __( 'Author', 'my-calendar' ) . '</label>
					<select id="e_author" name="event_author">
						<option value="0" selected="selected">Public Submitter</option>' .
						$select
					. '</select>
				</p>';
			} else {
				$return = '<input type="hidden" name="event_author" value="' . $default . '" />';
			}
			break;
		case 'event_desc':
			if ( $show_block ) {
				global $current_screen;
				// Because wp_editor cannot return a value, event_desc fields cannot be filtered if its enabled.
				$value         = ( $has_data ) ? stripslashes( $data->event_desc ) : '';
				$custom_editor = apply_filters( 'mc_custom_content_editor', false, $value, $data );
				if ( false !== $custom_editor ) {
					$return = $custom_editor;
				} else {
					if ( 'post' === $current_screen->base ) {
						$return = '<div class="event_description">
										<label for="content" class="screen-reader-text">' . __( 'Event Description', 'my-calendar' ) . '</label>
										<textarea id="content" name="content" class="event_desc" rows="8" cols="80">' . stripslashes( esc_attr( $value ) ) . '</textarea>
									</div>';
					} else {
						echo '
						<div class="event_description">
						<label for="content" class="screen-reader-text">' . __( 'Event Description', 'my-calendar' ) . '</label>';
						if ( user_can_richedit() ) {
							wp_editor( $value, 'content', array( 'textarea_rows' => 10 ) );
						} else {
							echo '<textarea id="content" name="content" class="event_desc" rows="8" cols="80">' . stripslashes( esc_attr( $value ) ) . '</textarea>';
						}
						echo '</div>';
					}
				}
			}
			break;
		case 'event_short':
			if ( $show_block ) {
				$value  = ( $has_data ) ? stripslashes( esc_attr( $data->event_short ) ) : '';
				$return = '
				<p>
					<label for="e_short">' . __( 'Short Description', 'my-calendar' ) . '</label><br /><textarea id="e_short" name="event_short" rows="2" cols="80">' . $value . '</textarea>
				</p>';
			}
			break;
		case 'event_image':
			if ( $has_data && property_exists( $data, 'event_post' ) ) {
				$image    = ( has_post_thumbnail( $data->event_post ) ) ? get_the_post_thumbnail_url( $data->event_post ) : $data->event_image;
				$image_id = ( has_post_thumbnail( $data->event_post ) ) ? get_post_thumbnail_id( $data->event_post ) : '';
			} else {
				$image    = ( $has_data && '' !== $data->event_image ) ? $data->event_image : '';
				$image_id = '';
			}
			if ( $show_block ) {
				$return = '
				<div class="mc-image-upload field-holder">
					<input type="hidden" name="event_image_id" value="' . esc_attr( $image_id ) . '" class="textfield" id="e_image_id" />
					<label for="e_image">' . __( 'Add an image:', 'my-calendar' ) . '</label><br /><input type="text" name="event_image" id="e_image" size="60" value="' . esc_attr( $image ) . '" placeholder="http://yourdomain.com/image.jpg" /> <button type="button" class="button textfield-field">' . __( 'Upload', 'my-calendar' ) . '</button>';
				if ( '' !== $image ) {
					$image   = ( has_post_thumbnail( $data->event_post ) ) ? get_the_post_thumbnail_url( $data->event_post ) : $data->event_image;
					$return .= '<div class="event_image"><img src="' . esc_attr( $image ) . '" alt="" /></div>';
				} else {
					$return .= '<div class="event_image"></div>';
				}
				$return .= '</div>';
			} else {
				$return = '<input type="hidden" name="event_image" value="' . esc_attr( $image ) . '" />';
			}
			break;
		case 'event_category':
			if ( $show_block ) {
				if ( 'true' !== get_option( 'mc_multiple_categories' ) ) {
					$select = mc_category_select( $data, true, false );
					$return = '
						<p class="mc_category">
							<label for="event_category">' . __( 'Category', 'my-calendar-submissions' ) . '</label>
							<select class="widefat" name="event_category" id="e_category">' . $select . '</select>
						</p>';
				} else {
					$return = '<fieldset><legend>' . __( 'Categories', 'my-calendar' ) . '</legend><ul class="checkboxes">' .
						mc_category_select( $data, true, true ) . '
					</ul></fieldset>';
				}
			} else {
				$categories = mc_get_categories( $data );
				$return     = '<div>';
				if ( is_array( $categories ) ) {
					foreach ( $categories as $category ) {
						$return .= '<input type="hidden" name="event_category[]" value="' . absint( $category ) . '" />';
					}
				} else {
					$return .= '<input type="hidden" name="event_category[]" value="1" />';
				}
				$return .= '</div>';
			}
			break;
		case 'event_link':
			if ( $show_block ) {
				$value = ( $has_data ) ? esc_url( $data->event_link ) : '';
				if ( $has_data && '1' === $data->event_link_expires ) {
					$checked = ' checked="checked"';
				} elseif ( $has_data && '0' === $data->event_link_expires ) {
					$checked = '';
				} elseif ( 'true' === get_option( 'mc_event_link_expires' ) ) {
					$checked = ' checked="checked"';
				}
				$return = '
					<p>
						<label for="e_link">' . __( 'URL', 'my-calendar' ) . '</label> <input type="text" id="e_link" name="event_link" size="40" value="' . $value . '" /> <input type="checkbox" value="1" id="e_link_expires" name="event_link_expires"' . $checked . ' /> <label for="e_link_expires">' . __( 'Link will expire after event', 'my-calendar' ) . '</label>
					</p>';
			}
			break;
		case 'event_recurs':
			if ( is_object( $data ) ) {
				$event_recur = ( is_object( $data ) ) ? $data->event_recur : '';
				$recurs      = str_split( $event_recur, 1 );
				$recur       = $recurs[0];
				$every       = ( isset( $recurs[1] ) ) ? str_replace( $recurs[0], '', $event_recur ) : 1;
				if ( 1 === (int) $every && 'B' === $recur ) {
					$every = 2;
				}
				$prev = '<input type="hidden" name="prev_event_repeats" value="' . $data->event_repeats . '" /><input type="hidden" name="prev_event_recur" value="' . $data->event_recur . '" />';
			} else {
				$recur = false;
				$every = 1;
				$prev  = '';
			}
			if ( is_object( $data ) && null !== $data->event_repeats ) {
				$repeats = $data->event_repeats;
			} else {
				$repeats = 0;
			}
			if ( $show_block && empty( $_GET['date'] ) ) {
				$return = $pre . '
	<h2>' . __( 'Repetition Pattern', 'my-calendar' ) . '</h2>
	<div class="inside">' . $prev . '
		<fieldset>
		<legend class="screen-reader-text">' . __( 'Recurring Events', 'my-calendar' ) . '</legend>
			<p>
				<label for="e_repeats">' . __( 'Repeats', 'my-calendar' ) . ' <input type="text" name="event_repeats" aria-labelledby="e_repeats_label" aria-describedby="e_repeats_desc" id="e_repeats" size="2" value="' . esc_attr( $repeats ) . '" /> <span id="e_repeats_label">' . __( 'times', 'my-calendar' ) . '</span>, </label>
				<label for="e_every">' . __( 'every', 'my-calendar' ) . '</label> <input type="number" name="event_every" id="e_every" size="2" min="1" max="99" maxlength="2" value="' . esc_attr( $every ) . '" />
				<label for="e_recur" class="screen-reader-text">' . __( 'Units', 'my-calendar' ) . '</label>
				<select name="event_recur" id="e_recur">
					' . mc_recur_options( $recur ) . '
				</select>
			</p>
			<p id="e_repeats_desc">
				' . __( 'Your entry is the number of events after the first occurrence of the event: a recurrence of <em>2</em> means the event will happen three times.', 'my-calendar' ) . '
			</p>
		</fieldset>
	</div>
							' . $post;
			} else {
				if ( '' === $every && '' === $repeats ) {
					$every   = 'S';
					$repeats = '0';
				}
				$return = '
				<div>' . $prev . '<input type="hidden" name="event_repeats" value="' . esc_attr( $repeats ) . '" /><input type="hidden" name="event_every" value="' . esc_attr( $every ) . '" /><input type="hidden" name="event_recur" value="' . esc_attr( $recur ) . '" /></div>';
			}
			break;
		case 'event_access':
			if ( $show_block ) {
				$label  = __( 'Event Access', 'my-calendar' );
				$return = $pre . '<h2>' . $label . '</h2><div class="inside">' . mc_event_accessibility( '', $data, $label ) . apply_filters( 'mc_event_access_fields', '', $has_data, $data ) . '</div>' . $post;
			}
			break;
		case 'event_open':
			if ( $show_block ) {
				$return = $pre . '<h2>' . __( 'Event Registration Settings', 'my-calendar' ) . '</h2><div class="inside"><fieldset><legend class="screen-reader-text">' . __( 'Event Registration', 'my-calendar' ) . '</legend>' . apply_filters( 'mc_event_registration', '', $has_data, $data, 'admin' ) . '</fieldset></div>' . $post;
			} else {
				$tickets      = ( $has_data ) ? esc_url( $data->event_tickets ) : '';
				$registration = ( $has_data ) ? esc_attr( $data->event_registration ) : '';
				$return       = '
				<div><input type="hidden"  name="event_tickets" value="' . $tickets . '" /><input type="hidden" name="event_registration" value="' . $registration . '" /></div>';
			}
			break;
		case 'event_location':
			if ( $show_block ) {
				$return = mc_locations_fields( $has_data, $data, 'event' );
			} else {
				if ( $has_data ) {
					$return = "<div>
                    <input type='hidden' name='event_label' value='" . esc_attr( stripslashes( $data->event_label ) ) . "' />
                    <input type='hidden' name='event_street' value='" . esc_attr( stripslashes( $data->event_street ) ) . "' />
                    <input type='hidden' name='event_street2' value='" . esc_attr( stripslashes( $data->event_street2 ) ) . "' />
                    <input type='hidden' name='event_phone' value='" . esc_attr( stripslashes( $data->event_phone ) ) . "' />
                    <input type='hidden' name='event_phone2' value='" . esc_attr( stripslashes( $data->event_phone2 ) ) . "' />
                    <input type='hidden' name='event_city' value='" . esc_attr( stripslashes( $data->event_city ) ) . "' />
                    <input type='hidden' name='event_state' value='" . esc_attr( stripslashes( $data->event_state ) ) . "' />
                    <input type='hidden' name='event_postcode' value='" . esc_attr( stripslashes( $data->event_postcode ) ) . "' />
                    <input type='hidden' name='event_region' value='" . esc_attr( stripslashes( $data->event_region ) ) . "' />
                    <input type='hidden' name='event_country' value='" . esc_attr( stripslashes( $data->event_country ) ) . "' />
                    <input type='hidden' name='event_zoom' value='" . esc_attr( stripslashes( $data->event_zoom ) ) . "' />
                    <input type='hidden' name='event_url' value='" . esc_attr( stripslashes( $data->event_url ) ) . "' />
                    <input type='hidden' name='event_latitude' value='" . esc_attr( stripslashes( $data->event_latitude ) ) . "' />
                    <input type='hidden' name='event_longitude' value='" . esc_attr( stripslashes( $data->event_longitude ) ) . "' /></div>";
				}
			}
			break;
		default:
			return;
	}
	$return = apply_filters( 'mc_show_block', $return, $data, $field, $has_data );
	if ( true === $echo ) {
		echo $return;
	} else {
		return $return;
	}
}


/**
 * Test whether an event has an invalid overlap.
 *
 * @param object  $data Event object.
 * @param boolean $return Return or echo.
 *
 * @return string Warning text about problem with event.
 */
function mc_test_occurrence_overlap( $data, $return = false ) {
	$warning = '';
	// If this event is single, skip query.
	$single_recur = ( 'S' === $data->event_recur || 'S1' === $data->event_recur ) ? true : false;
	// If event starts and ends on same day, skip query.
	$start_end = ( $data->event_begin === $data->event_end ) ? true : false;
	// Only run test when an event is set up to recur & starts/ends on different days.
	if ( ! $single_recur && ! $start_end ) {
		$check = mc_increment_event( $data->event_id, array(), 'test' );
		if ( my_calendar_date_xcomp( $check['occur_begin'], $data->event_end . '' . $data->event_endtime ) ) {
			$warning = "<div class='error'><span class='problem-icon dashicons dashicons-performance' aria-hidden='true'></span> <p><strong>" . __( 'Event hidden from public view.', 'my-calendar' ) . '</strong> ' . __( 'This event ends after the next occurrence begins. Events must end <strong>before</strong> the next occurrence begins.', 'my-calendar' ) . '</p><p>';
			// Translators: End date, end time, beginning of next event.
			$warning .= sprintf( __( 'Event end date: <strong>%1$s %2$s</strong>. Next occurrence starts: <strong>%3$s</strong>', 'my-calendar' ), $data->event_end, $data->event_endtime, $check['occur_begin'] ) . '</p></div>';
			update_post_meta( $data->event_post, '_occurrence_overlap', 'false' );
		} else {
			delete_post_meta( $data->event_post, '_occurrence_overlap' );
		}
	} else {
		// If event has been changed to same date, still delete meta.
		delete_post_meta( $data->event_post, '_occurrence_overlap' );
	}
	if ( $return ) {
		return $warning;
	} else {
		echo $warning;
	}
}

/**
 * Display all enabled form fields.
 *
 * @param mixed array/object $data Passed data.
 * @param string             $mode Copy/edit/add.
 * @param int                $event_id Event ID.
 */
function mc_form_fields( $data, $mode, $event_id ) {
	global $wpdb, $user_ID;
	$has_data = ( empty( $data ) ) ? false : true;
	if ( $data ) {
		// Don't execute occurrence test if displaying pre-process error messages.
		if ( ! is_object( $data ) ) {
			$test = mc_test_occurrence_overlap( $data );
		}
	}
	$instance = ( isset( $_GET['date'] ) ) ? (int) $_GET['date'] : false;
	if ( $instance ) {
		$ins      = mc_get_instance_data( $instance );
		$event_id = $ins->occur_event_id;
		$data     = mc_get_first_event( $event_id );
	}
	?>
	<div class="postbox-container jcd-wide">
		<div class="metabox-holder">
		<?php
		if ( 'add' === $mode || 'copy' === $mode ) {
			$query_args = array();
		} else {
			$query_args = array(
				'mode'     => $mode,
				'event_id' => $event_id,
			);
			if ( $instance ) {
				$query_args['date'] = $instance;
			}
		}
		echo apply_filters( 'mc_before_event_form', '', $event_id );
		$action       = add_query_arg( $query_args, admin_url( 'admin.php?page=my-calendar' ) );
		$group_id     = ( ! empty( $data->event_group_id ) && 'copy' !== $mode ) ? $data->event_group_id : mc_group_id();
		$event_author = ( 'edit' !== $mode ) ? $user_ID : $data->event_author;
		?>
<form id="my-calendar" method="post" action="<?php echo $action; ?>">
<div>
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>" />
	<?php
	if ( isset( $_GET['ref'] ) ) {
		echo '<input type="hidden" name="ref" value="' . esc_url( $_GET['ref'] ) . '" />';
	}
	?>
	<input type="hidden" name="event_group_id" value="<?php echo $group_id; ?>" />
	<input type="hidden" name="event_action" value="<?php echo esc_attr( $mode ); ?>" />
	<?php
	if ( ! empty( $_GET['date'] ) ) {
		echo '<input type="hidden" name="event_instance" value="' . (int) $_GET['date'] . '"/>';
	}
	?>
	<input type="hidden" name="event_id" value="<?php echo (int) $event_id; ?>"/>
	<?php
	if ( 'edit' === $mode ) {
		if ( $has_data && ( ! property_exists( $data, 'event_post' ) || ! $data->event_post ) ) {
			$array_data = (array) $data;
			$post_id    = mc_event_post( 'add', $array_data, $event_id );
		} else {
			$post_id = ( $has_data ) ? $data->event_post : false;
		}
		echo '<input type="hidden" name="event_post" value="' . $post_id . '" />';
	} else {
		$post_id = false;
	}
	?>
	<input type="hidden" name="event_nonce_name" value="<?php echo wp_create_nonce( 'event_nonce' ); ?>" />
</div>

<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<?php
			$text = ( 'edit' === $mode ) ? __( 'Edit Event', 'my-calendar' ) : __( 'Add Event', 'my-calendar' );
		?>
		<h2><?php esc_html( $text ); ?></h2>
		<div class="inside">
		<div class='mc-controls'>
			<?php
			if ( $post_id ) {
				$deleted = get_post_meta( $post_id, '_mc_deleted_instances', true );
				$custom  = get_post_meta( $post_id, '_mc_custom_instances', true );
				if ( $deleted || $custom ) {
					mc_show_notice( __( 'Some repetitions of this recurring event have been deleted or modified. Update the date or recurring pattern for the event to reset its repeat events.', 'my-calendar' ) );
				}
			}
			echo mc_controls( $mode, $has_data, $data );
			?>
		</div>
			<?php
			if ( ! empty( $_GET['date'] ) && 'S' !== $data->event_recur ) {
				$event = mc_get_event( $instance );
				$date  = date_i18n( mc_date_format(), mc_strtotime( $event->occur_begin ) );
				// Translators: Date of a specific event occurrence.
				$message = sprintf( __( 'You are editing the <strong>%s</strong> instance of this event. Other instances of this event will not be changed.', 'my-calendar' ), $date );
				mc_show_notice( $message );
			} elseif ( isset( $_GET['date'] ) && empty( $_GET['date'] ) ) {
				mc_show_notice( __( 'The ID for this event instance was not provided. <strong>You are editing this entire recurring event series.</strong>', 'my-calendar' ) );
			}
			?>
			<fieldset>
				<legend class="screen-reader-text"><?php _e( 'Event Details', 'my-calendar' ); ?></legend>
				<p>
					<label for="e_title"><?php _e( 'Event Title', 'my-calendar' ); ?></label><br/>
					<input type="text" id="e_title" name="event_title" size="50" maxlength="255" value="<?php echo ( $has_data ) ? apply_filters( 'mc_manage_event_title', stripslashes( esc_attr( $data->event_title ) ), $data ) : ''; ?>" />
				</p>
				<?php
				if ( is_object( $data ) && 1 === (int) $data->event_flagged ) {
					if ( '0' === $data->event_flagged ) {
						$flagged = ' checked="checked"';
					} elseif ( '1' === $data->event_flagged ) {
						$flagged = '';
					}
					?>
					<div class="error">
						<p>
							<input type="checkbox" value="0" id="e_flagged" name="event_flagged"<?php echo $flagged; ?> />
							<label for="e_flagged"><?php _e( 'This event is not spam', 'my-calendar' ); ?></label>
						</p>
					</div>
					<?php
				}
				apply_filters( 'mc_insert_custom_fields', '', $has_data, $data );

				if ( function_exists( 'wpt_post_to_twitter' ) && current_user_can( 'wpt_can_tweet' ) ) {
					if ( ! ( 'edit' === $mode && 1 === (int) $data->event_approved ) ) {
						$mc_allowed = absint( ( get_option( 'wpt_tweet_length' ) ) ? get_option( 'wpt_tweet_length' ) : 140 );
						?>
						<p class='mc-twitter'>
							<label for='mc_twitter'><?php _e( 'Post to Twitter (via WP to Twitter)', 'my-calendar' ); ?></label><br/>
							<textarea cols='70' rows='2' id='mc_twitter' name='mc_twitter' data-allowed="<?php echo $mc_allowed; ?>"><?php echo apply_filters( 'mc_twitter_text', '', $data ); ?></textarea>
						</p>
						<?php
					}
				}
				mc_show_block( 'event_desc', $has_data, $data );
				mc_show_block( 'event_category', $has_data, $data );
				?>
			</fieldset>
		</div>
	</div>
</div>

<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<h2><?php _e( 'Date and Time', 'my-calendar' ); ?></h2>

		<div class="inside">
			<?php
			if ( is_object( $data ) ) { // Information for rewriting recurring data.
				?>
				<input type="hidden" name="prev_event_begin" value="<?php echo esc_attr( $data->event_begin ); ?>"/>
				<input type="hidden" name="prev_event_time" value="<?php echo esc_attr( $data->event_time ); ?>"/>
				<input type="hidden" name="prev_event_end" value="<?php echo esc_attr( $data->event_end ); ?>"/>
				<input type="hidden" name="prev_event_endtime" value="<?php echo esc_attr( $data->event_endtime ); ?>"/>
				<?php
			}
			?>
			<fieldset>
				<legend class="screen-reader-text"><?php _e( 'Event Date and Time', 'my-calendar' ); ?></legend>
				<div id="e_schedule">
					<div id="event1" class="clonedInput" aria-live="assertive">
						<?php echo apply_filters( 'mc_datetime_inputs', '', $has_data, $data, 'admin' ); ?>
					</div>
					<?php
					if ( 'edit' !== $mode ) {
						$span_checked = '';
						if ( $has_data && '1' === $data->event_span ) {
							$span_checked = ' checked="checked"';
						} elseif ( $has_data && '0' === $data->event_span ) {
							$span_checked = '';
						}
						?>
					<p id="event_span">
						<input type="checkbox" value="1" id="e_span" name="event_span"<?php echo $span_checked; ?> />
						<label for="e_span"><?php _e( 'This is a multi-day event.', 'my-calendar' ); ?></label>
					</p>
					<p class="note">
						<em><?php _e( 'Enter start and end dates/times for each occurrence of the event.', 'my-calendar' ); ?></em>
					</p>
					<div>
						<input type="button" id="add_field" value="<?php _e( 'Add another occurrence', 'my-calendar' ); ?>" class="button" />
						<input type="button" id="del_field" value="<?php _e( 'Remove last occurrence', 'my-calendar' ); ?>" class="button" />
					</div>
						<?php
					} else {
						?>
						<div id='mc-accordion'>
							<?php
							if ( 'S' !== $data->event_recur ) {
								?>
								<h4><span class='dashicons' aria-hidden='true'> </span><button type="button" class="button-link"><?php _e( 'Scheduled dates for this event', 'my-calendar' ); ?></button></h4>
								<div>
									<p>
									<?php _e( 'Editing a single date of an event changes only that date. Editing the root event changes all events in the series.', 'my-calendar' ); ?>
									</p>
									<div class='mc_response' aria-live='assertive'></div>
									<ul class="columns instance-list">
										<?php
										if ( isset( $_GET['date'] ) ) {
											$date = (int) $_GET['date'];
										} else {
											$date = false;
										}
										echo mc_admin_instances( $data->event_id, $date );
										?>
									</ul>
									<p><button type='button' class='add-occurrence' aria-expanded="false"><span class='dashicons' aria-hidden='true'> </span><?php _e( 'Add another date', 'my-calendar' ); ?></button></p>
									<div class='mc_add_new'>
									<?php echo mc_recur_datetime_input( $data ); ?>
									<button type='button' class='save-occurrence'><?php _e( 'Add Date', 'my-calendar' ); ?></button>
									</div>
								</div>
								<?php
							}
							if ( 0 !== (int) $data->event_group_id ) {
								$edit_group_url = admin_url( 'admin.php?page=my-calendar-groups&mode=edit&event_id=' . $data->event_id . '&group_id=' . $data->event_group_id );
								?>
								<h4><span class='dashicons' aria-hidden='true'> </span><button type="button" class="button-link"><?php _e( 'Related Events:', 'my-calendar' ); ?></button> (<a href='<?php echo $edit_group_url; ?>'><?php _e( 'Edit group', 'my-calendar' ); ?></a>)
								</h4>
								<div>
									<ul class="columns">
										<?php mc_related_events( $data->event_group_id ); ?>
									</ul>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
			</fieldset>
		</div>
	</div>
</div>
	<?php
	mc_show_block( 'event_recurs', $has_data, $data );
	if ( mc_show_edit_blocks( array( 'event_short', 'event_image', 'event_host', 'event_author', 'event_link' ) ) ) {
		?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2><?php _e( 'Event Details', 'my-calendar' ); ?></h2>
				<div class="inside">
		<?php
	}
	mc_show_block( 'event_short', $has_data, $data );
	mc_show_block( 'event_image', $has_data, $data );
	mc_show_block( 'event_host', $has_data, $data );
	mc_show_block( 'event_author', $has_data, $data, true, $event_author );
	mc_show_block( 'event_link', $has_data, $data );
	if ( mc_show_edit_blocks( array( 'event_short', 'event_image', 'event_host', 'event_author', 'event_link' ) ) ) {
		?>
				</div>
			</div>
		</div>
		<?php
	}
	$custom_fields = apply_filters( 'mc_event_details', '', $has_data, $data, 'admin' );
	if ( '' !== $custom_fields ) {
		?>
<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<h2><?php _e( 'Event Custom Fields', 'my-calendar' ); ?></h2>
		<div class="inside">
			<?php echo apply_filters( 'mc_event_details', '', $has_data, $data, 'admin' ); ?>
		</div>
	</div>
</div>
		<?php
	}
	mc_show_block( 'event_access', $has_data, $data );
	mc_show_block( 'event_open', $has_data, $data );
	if ( mc_show_edit_block( 'event_location' ) || mc_show_edit_block( 'event_location_dropdown' ) ) {
		?>

<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<h2><?php _e( 'Event Location', 'my-calendar' ); ?></h2>

		<div class="inside location_form">
			<fieldset>
				<legend class='screen-reader-text'><?php _e( 'Event Location', 'my-calendar' ); ?></legend>
		<?php
	}
	if ( mc_show_edit_block( 'event_location_dropdown' ) ) {
		echo mc_event_location_dropdown_block( $data );
	} else {
		?>
		<input type="hidden" name="location_preset" value="none" />
		<?php
	}
	mc_show_block( 'event_location', $has_data, $data );
	if ( mc_show_edit_block( 'event_location' ) || mc_show_edit_block( 'event_location_dropdown' ) ) {
		?>
			</fieldset>
		</div>
	</div>
</div>
		<?php
	}
	if ( mc_show_edit_block( 'event_specials' ) ) {
		$hol_checked   = ( 'true' === get_option( 'mc_skip_holidays' ) ) ? ' checked="checked"' : '';
		$fifth_checked = ( 'true' === get_option( 'mc_no_fifth_week' ) ) ? ' checked="checked"' : '';
		if ( $has_data ) {
			$hol_checked   = ( '1' === $data->event_holiday ) ? ' checked="checked"' : '';
			$fifth_checked = ( '1' === $data->event_fifth_week ) ? ' checked="checked"' : '';
		}
		?>
		<div class="ui-sortable meta-box-sortables">
		<div class="postbox">
			<h2><?php _e( 'Special scheduling options', 'my-calendar' ); ?></h2>

			<div class="inside">
				<fieldset>
					<legend class="screen-reader-text"><?php _e( 'Special Options', 'my-calendar' ); ?></legend>
					<p>
						<label for="e_holiday"><?php _e( 'Cancel this event if it occurs on a date with an event in the Holidays category', 'my-calendar' ); ?></label>
						<input type="checkbox" value="true" id="e_holiday" name="event_holiday"<?php echo $hol_checked; ?> />
					</p>
					<p>
						<label for="e_fifth_week"><?php _e( 'If this event recurs, and falls on the 5th week of the month in a month with only four weeks, move it back one week.', 'my-calendar' ); ?></label>
						<input type="checkbox" value="true" id="e_fifth_week" name="event_fifth_week"<?php echo $fifth_checked; ?> />
					</p>
				</fieldset>
			</div>
		</div>
		</div>
		<?php
	} else {
		if ( $has_data ) {
			$event_holiday = ( '1' === $data->event_holiday ) ? 'true' : 'false';
			$event_fifth   = ( '1' === $data->event_fifth_week ) ? 'true' : 'false';
		} else {
			$event_holiday = get_option( 'mc_skip_holidays' );
			$event_fifth   = get_option( 'mc_no_fifth_week' );
		}
		?>
		<div>
		<input type="hidden" name="event_holiday" value="<?php echo esc_attr( $event_holiday ); ?>" />
		<input type="hidden" name="event_fifth_week" value="<?php echo esc_attr( $event_fifth ); ?>" />
		</div>
		<?php
	}
	?>
	<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
		<div class="inside">
			<div class='mc-controls footer'>
				<?php echo mc_controls( $mode, $has_data, $data, 'footer' ); ?>
			</div>
		</div>
	</div>
	</div>
</form>
</div>
	</div>
	<?php
}

/**
 * Produce Event location dropdown.
 *
 * @param object $data Current event data.
 *
 * @return string
 */
function mc_event_location_dropdown_block( $data ) {
	$current_location = '';
	$event_location   = false;
	$output           = '<div class="mc-event-location-dropdown">';
	$autocomplete     = false;
	$count            = mc_count_locations();
	if ( $count > apply_filters( 'mc_convert_locations_select_to_autocomplete', 50 ) ) {
		$autocomplete = true;
	}
	if ( 0 !== $count ) {
		$output .= '<label for="l_preset">' . __( 'Choose location:', 'my-calendar' ) . '</label>';
		if ( is_object( $data ) ) {
			$selected = '';
			if ( property_exists( $data, 'event_location' ) ) {
				$event_location = $data->event_location;
			}
		}
		if ( ! $autocomplete ) {
			$locs    = mc_get_locations( 'select-locations' );
			$output .= '
			 <select name="location_preset" id="l_preset" aria-describedby="mc-current-location">
				<option value="none">--</option>';
			foreach ( $locs as $loc ) {
				if ( is_object( $loc ) ) {
					$loc_name = strip_tags( stripslashes( $loc->location_label ), mc_strip_tags() );
					$selected = ( is_numeric( get_option( 'mc_default_location' ) ) && (int) get_option( 'mc_default_location' ) === (int) $loc->location_id ) ? ' selected="selected"' : '';
					if ( (int) $loc->location_id === (int) $event_location ) {
						// Translators: label for current location.
						$current_location  = "<span id='mc-current-location'>" . sprintf( __( 'Current location: %s', 'my-calendar' ), $loc_name ) . '</span>';
						$current_location .= "<input type='hidden' name='preset_location' value='$event_location' />";
					}
					$output .= "<option value='" . $loc->location_id . "'$selected />" . $loc_name . '</option>';
				}
			}
			$output .= '</select>' . $current_location . '</p>';
		} else {
			$location_label = ( $event_location && is_numeric( $event_location ) ) ? mc_get_location( $event_location )->location_label : '';
			$output        .= '<div id="mc-locations-autocomplete" class="autocomplete">
				<input class="autocomplete-input" type="text" id="l_preset" value="' . esc_attr( $location_label ) . '" />
				<ul class="autocomplete-result-list"></ul>
				<input type="hidden" name="location_preset" id="mc_event_location_value" value="' . esc_attr( $event_location ) . '" />
			</div>';
		}
	} else {
		$output .= '<input type="hidden" name="location_preset" value="none" />
		<p>
		<a href="' . admin_url( 'admin.php?page=my-calendar-locations' ) . '>">' . __( 'Add recurring locations for later use.', 'my-calendar' ) . '</a>
		</p>';
	}
	$output .= '</div>';

	return $output;
}

/**
 * Get users.
 *
 * @param string $group Not used except in filters.
 *
 * @return array of users
 */
function mc_get_users( $group = 'authors' ) {
	global $blog_id;
	$users = apply_filters( 'mc_get_users', false, $group, $blog_id );
	if ( $users ) {
		return $users;
	}
	$count = count_users( 'time' );
	$args  = array(
		'blog_id' => $blog_id,
		'orderby' => 'display_name',
		'fields'  => array( 'ID', 'user_nicename', 'display_name' ),
	);
	$args  = apply_filters( 'mc_filter_user_arguments', $args, $count, $group );
	$users = new WP_User_Query( $args );

	return $users->get_results();
}

/**
 * Get users as options in a select
 *
 * @param string $selected Group of selected users. Comma-separated IDs.
 * @param string $group Type of roles to fetch.
 *
 * @return string select options.
 */
function mc_selected_users( $selected = '', $group = 'authors' ) {
	$options = apply_filters( 'mc_custom_user_select', '', $selected, $group );
	if ( '' !== $options ) {
		return $options;
	}
	$selected = explode( ',', $selected );
	$users    = mc_get_users( $group );
	foreach ( $users as $u ) {
		if ( in_array( $u->ID, $selected, true ) ) {
			$checked = ' selected="selected"';
		} else {
			$checked = '';
		}
		$display_name = ( '' === $u->display_name ) ? $u->user_nicename : $u->display_name;
		$options     .= '<option value="' . $u->ID . '"' . $checked . ">$display_name</option>\n";
	}

	return $options;
}

/**
 * Return valid accessibility features for events.
 *
 * @return array
 */
function mc_event_access() {
	$event_access = apply_filters(
		'mc_event_access_choices',
		array(
			'1'  => __( 'Audio Description', 'my-calendar' ),
			'2'  => __( 'ASL Interpretation', 'my-calendar' ),
			'3'  => __( 'ASL Interpretation with voicing', 'my-calendar' ),
			'4'  => __( 'Deaf-Blind ASL', 'my-calendar' ),
			'5'  => __( 'Real-time Captioning', 'my-calendar' ),
			'6'  => __( 'Scripted Captioning', 'my-calendar' ),
			'7'  => __( 'Assisted Listening Devices', 'my-calendar' ),
			'8'  => __( 'Tactile/Touch Tour', 'my-calendar' ),
			'9'  => __( 'Braille Playbill', 'my-calendar' ),
			'10' => __( 'Large Print Playbill', 'my-calendar' ),
			'11' => __( 'Sensory Friendly', 'my-calendar' ),
			'12' => __( 'Other', 'my-calendar' ),
		)
	);

	return $event_access;
}

/**
 * Form to select accessibility features.
 *
 * @param string             $form Form HTML.
 * @param mixed array/object $data Event data.
 * @param string             $label Primary label for fields.
 */
function mc_event_accessibility( $form, $data, $label ) {
	$note_value    = '';
	$events_access = array();
	$class         = ( is_admin() ) ? 'screen-reader-text' : 'mc-event-access';
	$form         .= "
		<fieldset>
			<legend class='$class'>$label</legend>
			<ul class='accessibility-features checkboxes'>";
	$access        = apply_filters( 'mc_event_accessibility', mc_event_access() );
	if ( ! empty( $data ) ) {
		if ( property_exists( $data, 'event_post' ) ) {
			$events_access = get_post_meta( $data->event_post, '_mc_event_access', true );
		} else {
			$events_access = array();
		}
	}
	foreach ( $access as $k => $a ) {
		$id      = "events_access_$k";
		$label   = $a;
		$checked = '';
		if ( is_array( $events_access ) ) {
			$checked = ( in_array( $k, $events_access, true ) || in_array( $a, $events_access, true ) ) ? ' checked="checked"' : '';
		}
		$item  = sprintf( '<li><input type="checkbox" id="%1$s" name="events_access[]" value="%4$s" class="checkbox" %2$s /> <label for="%1$s">%3$s</label></li>', esc_attr( $id ), $checked, esc_html( $label ), esc_attr( $a ) );
		$form .= $item;
	}
	if ( isset( $events_access['notes'] ) ) {
		$note_value = esc_attr( $events_access['notes'] );
	}
	$form .= '<li class="events_access_notes"><label for="events_access_notes">' . __( 'Notes', 'my-calendar' ) . '</label> <input type="text" id="events_access_notes" name="events_access[notes]" value="' . esc_attr( $note_value ) . '" /></li>';
	$form .= '</ul>
	</fieldset>';

	return $form;
}

/**
 * Used on the manage events admin page to display a list of events
 */
function mc_list_events() {
	global $wpdb;
	if ( current_user_can( 'mc_approve_events' ) || current_user_can( 'mc_manage_events' ) || current_user_can( 'mc_add_events' ) ) {

		$action   = ! empty( $_POST['event_action'] ) ? $_POST['event_action'] : '';
		$event_id = ! empty( $_POST['event_id'] ) ? $_POST['event_id'] : '';
		if ( 'delete' === $action ) {
			$message = mc_delete_event( $event_id );
			echo $message;
		}

		if ( isset( $_GET['order'] ) ) {
			$sortdir = ( isset( $_GET['order'] ) && 'ASC' === $_GET['order'] ) ? 'ASC' : 'default';
			$sortdir = ( isset( $_GET['order'] ) && 'DESC' === $_GET['order'] ) ? 'DESC' : $sortdir;
		} else {
			$sortdir = 'default';
		}

		$default_direction = ( '' === get_option( 'mc_default_direction', '' ) ) ? 'ASC' : get_option( 'mc_default_direction' );
		$sortbydirection   = ( 'default' === $sortdir ) ? $default_direction : $sortdir;

		$sortby = ( isset( $_GET['sort'] ) ) ? $_GET['sort'] : get_option( 'mc_default_sort' );
		if ( empty( $sortby ) ) {
			$sortbyvalue = 'event_begin';
		} else {
			switch ( $sortby ) {
				case 1:
					$sortbyvalue = 'event_ID';
					break;
				case 2:
					$sortbyvalue = 'event_title';
					break;
				case 3:
					$sortbyvalue = 'event_desc';
					break;
				case 4:
					$sortbyvalue = "event_begin $sortbydirection, event_time";
					break;
				case 5:
					$sortbyvalue = 'event_author';
					break;
				case 6:
					$sortbyvalue = 'event_category';
					break;
				case 7:
					$sortbyvalue = 'event_label';
					break;
				default:
					$sortbyvalue = "event_begin $sortbydirection, event_time";
			}
		}
		$sorting       = ( 'DESC' === $sortbydirection ) ? '&amp;order=ASC' : '&amp;order=DESC';
		$allow_filters = true;
		$status        = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : '';
		$restrict      = ( isset( $_GET['restrict'] ) ) ? $_GET['restrict'] : 'all';
		switch ( $status ) {
			case 'all':
				$limit = '';
				break;
			case 'draft':
				$limit = 'WHERE event_approved = 0';
				break;
			case 'published':
				$limit = 'WHERE event_approved = 1';
				break;
			case 'trashed':
				$limit = 'WHERE event_approved = 2';
				break;
			default:
				$limit = 'WHERE event_approved != 2';
		}
		switch ( $restrict ) {
			case 'all':
				$filter = '';
				break;
			case 'where':
				$filter   = ( isset( $_GET['filter'] ) ) ? $_GET['filter'] : '';
				$restrict = 'event_label';
				break;
			case 'author':
				$filter   = ( isset( $_GET['filter'] ) ) ? (int) $_GET['filter'] : '';
				$restrict = 'event_author';
				break;
			case 'category':
				$filter   = ( isset( $_GET['filter'] ) ) ? (int) $_GET['filter'] : '';
				$restrict = 'event_category';
				break;
			case 'flagged':
				$filter   = ( isset( $_GET['filter'] ) ) ? (int) $_GET['filter'] : '';
				$restrict = 'event_flagged';
				break;
			default:
				$filter = '';
		}
		if ( ! current_user_can( 'mc_manage_events' ) && ! current_user_can( 'mc_approve_events' ) ) {
			$restrict      = 'event_author';
			$filter        = get_current_user_id();
			$allow_filters = false;
		}
		$filter = esc_sql( urldecode( $filter ) );
		if ( 'event_label' === $restrict ) {
			$filter = "'$filter'";
		}
		if ( '' === $limit && '' !== $filter ) {
			$limit = "WHERE $restrict = $filter";
		} elseif ( '' !== $limit && '' !== $filter ) {
			$limit .= " AND $restrict = $filter";
		}
		if ( '' === $filter || ! $allow_filters ) {
			$filtered = '';
		} else {
			$filtered = "<span class='dashicons dashicons-no' aria-hidden='true'></span><a href='" . admin_url( 'admin.php?page=my-calendar-manage' ) . "'>" . __( 'Clear filters', 'my-calendar' ) . '</a>';
		}
		$current        = empty( $_GET['paged'] ) ? 1 : intval( $_GET['paged'] );
		$user           = get_current_user_id();
		$screen         = get_current_screen();
		$option         = $screen->get_option( 'per_page', 'option' );
		$items_per_page = get_user_meta( $user, $option, true );
		if ( empty( $items_per_page ) || $items_per_page < 1 ) {
			$items_per_page = $screen->get_option( 'per_page', 'default' );
		}
		// Default limits.
		if ( '' === $limit ) {
			$limit .= ( 'event_flagged' !== $restrict ) ? ' WHERE event_flagged = 0' : '';
		} else {
			$limit .= ( 'event_flagged' !== $restrict ) ? ' AND event_flagged = 0' : '';
		}
		if ( isset( $_POST['mcs'] ) ) {
			$query  = $_POST['mcs'];
			$limit .= mc_prepare_search_query( $query );
		}
		$query_limit = ( ( $current - 1 ) * $items_per_page );
		$limit      .= ( 'archived' !== $restrict ) ? ' AND event_status = 1' : ' AND event_status = 0';
		if ( 'event_category' !== $sortbyvalue ) {
			$events = $wpdb->get_results( $wpdb->prepare( 'SELECT SQL_CALC_FOUND_ROWS event_id FROM ' . my_calendar_table() . " $limit ORDER BY $sortbyvalue $sortbydirection " . 'LIMIT %d, %d', $query_limit, $items_per_page ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$limit  = str_replace( array( 'WHERE ' ), '', $limit );
			$limit  = ( strpos( $limit, 'AND' ) === 0 ) ? $limit : 'AND ' . $limit;
			$events = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT SQL_CALC_FOUND_ROWS events.event_id FROM ' . my_calendar_table() . ' AS events JOIN ' . my_calendar_categories_table() . " AS categories WHERE events.event_category = categories.category_id $limit ORDER BY categories.category_name $sortbydirection " . 'LIMIT %d, %d', $query_limit, $items_per_page ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared
		}
		$found_rows = $wpdb->get_col( 'SELECT FOUND_ROWS();' );
		$items      = $found_rows[0];
		$counts     = get_option( 'mc_count_cache' );
		if ( empty( $counts ) ) {
			$counts = mc_update_count_cache();
		}
		?>
		<ul class="links">
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'published' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=published' ); ?>">
				<?php
					// Translators: Number of published events.
					printf( __( 'Published (%d)', 'my-calendar' ), $counts['published'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'draft' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=draft' ); ?>">
				<?php
					// Translators: Number of draft events.
					printf( __( 'Drafts (%d)', 'my-calendar' ), $counts['draft'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'trashed' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=trashed' ); ?>">
				<?php
					// Translators: Number of trashed events.
					printf( __( 'Trash (%d)', 'my-calendar' ), $counts['trash'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['restrict'] ) && 'archived' === $_GET['restrict'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;restrict=archived' ); ?>">
				<?php
					// Translators: Number of archived events.
					printf( __( 'Archived (%d)', 'my-calendar' ), $counts['archive'] );
				?>
				</a>
			</li>
			<?php
			if ( function_exists( 'akismet_http_post' ) && $allow_filters ) {
				?>
			<li>
				<a <?php echo ( isset( $_GET['restrict'] ) && 'flagged' === $_GET['restrict'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;restrict=flagged&amp;filter=1' ); ?>">
				<?php
					// Translators: Number of events marked as spam.
					printf( __( 'Spam (%d)', 'my-calendar' ), $counts['spam'] );
				?>
				</a>
			</li>
				<?php
			}
			?>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'all' === $_GET['limit'] || ( ! isset( $_GET['limit'] ) && ! isset( $_GET['restrict'] ) ) ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=all' ); ?>"><?php _e( 'All', 'my-calendar' ); ?></a>
			</li>
		</ul>
		<?php
		$search_text = ( isset( $_POST['mcs'] ) ) ? $_POST['mcs'] : '';
		?>
		<div class='mc-search'>
			<form action="<?php echo esc_url( add_query_arg( $_GET, admin_url( 'admin.php' ) ) ); ?>" method="post">
				<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
				</div>
				<div>
					<label for="mc_search" class='screen-reader-text'><?php _e( 'Search', 'my-calendar' ); ?></label>
					<input type='text' role='search' name='mcs' id='mc_search' value='<?php echo esc_attr( $search_text ); ?>' />
					<input type='submit' value='<?php _e( 'Search Events', 'my-calendar' ); ?>' class='button-secondary'/>
				</div>
			</form>
		</div>
		<?php
		echo $filtered;
		$num_pages = ceil( $items / $items_per_page );
		if ( $num_pages > 1 ) {
			$page_links = paginate_links(
				array(
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'prev_text' => __( '&laquo; Previous<span class="screen-reader-text"> Events</span>', 'my-calendar' ),
					'next_text' => __( 'Next<span class="screen-reader-text"> Events</span> &raquo;', 'my-calendar' ),
					'total'     => $num_pages,
					'current'   => $current,
					'mid_size'  => 1,
				)
			);
			printf( "<div class='tablenav'><div class='tablenav-pages'>%s</div></div>", $page_links );
		}
		if ( ! empty( $events ) ) {
			?>
			<form action="<?php echo esc_url( add_query_arg( $_GET, admin_url( 'admin.php' ) ) ); ?>" method="post">
				<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>" /></div>
				<div class='mc-actions'>
					<?php
					echo '<input type="submit" class="button-secondary delete" name="mass_delete" value="' . __( 'Delete events', 'my-calendar' ) . '"/> ';
					echo '<input type="submit" class="button-secondary trash" name="mass_trash" value="' . __( 'Trash events', 'my-calendar' ) . '"/> ';
					if ( current_user_can( 'mc_approve_events' ) ) {
						echo '<input type="submit" class="button-secondary mc-approve" name="mass_approve" value="' . __( 'Publish events', 'my-calendar' ) . '" /> ';
					}
					if ( ! ( isset( $_GET['restrict'] ) && 'archived' === $_GET['restrict'] ) ) {
						echo '<input type="submit" class="button-secondary mc-archive" name="mass_archive" value="' . __( 'Archive events', 'my-calendar' ) . '" /> ';
					} else {
						echo '<input type="submit" class="button-secondary mc-archive" name="mass_undo_archive" value="' . __( 'Remove from archive', 'my-calendar' ) . '" /> ';
					}
					if ( isset( $_GET['restrict'] ) && 'flagged' === $_GET['restrict'] ) {
						echo '<input type="submit" class="button-secondary mc-archive" name="mass_not_spam" value="' . __( 'Not spam', 'my-calendar' ) . '" /> ';
					}
					?>
				</div>

			<table class="widefat wp-list-table" id="my-calendar-admin-table">
				<thead>
					<tr>
						<th scope="col" style="width: 50px;"><input type='checkbox' class='selectall' id='mass_edit'/>
							<label for='mass_edit' class="screen-reader-text"><?php _e( 'Check/Uncheck all', 'my-calendar' ); ?></label>
							<a class="<?php echo ( 1 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=1$sorting" ); ?>"><?php _e( 'ID', 'my-calendar' ); ?></a>
						</th>
						<th scope="col">
							<a class="<?php echo ( 2 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=2$sorting" ); ?>"><?php _e( 'Title', 'my-calendar' ); ?></a>
						</th>
						<th scope="col">
							<a class="<?php echo ( 7 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=7$sorting" ); ?>"><?php _e( 'Location', 'my-calendar' ); ?></a>
						</th>
						<th scope="col">
							<a class="<?php echo ( 4 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=4$sorting" ); ?>"><?php _e( 'Date/Time', 'my-calendar' ); ?></a>
						</th>
						<th scope="col">
							<a class="<?php echo ( 5 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=5$sorting" ); ?>"><?php _e( 'Author', 'my-calendar' ); ?></a>
						</th>
						<th scope="col">
							<a class="<?php echo ( 6 === (int) $sortby ) ? 'active' : ''; ?>" href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;sort=6$sorting" ); ?>"><?php _e( 'Category', 'my-calendar' ); ?></a>
						</th>
					</tr>
				</thead>
				<?php
				$class      = '';
				$categories = $wpdb->get_results( 'SELECT * FROM ' . my_calendar_categories_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				foreach ( array_keys( $events ) as $key ) {
					$e       =& $events[ $key ];
					$event   = mc_get_first_event( $e->event_id );
					$invalid = false;
					if ( ! is_object( $event ) ) {
						$event   = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_table() . ' WHERE event_id = %d', $e->event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
						$invalid = true;
					}
					$class   = ( 'alternate' === $class ) ? 'even' : 'alternate';
					$class   = ( $invalid ) ? $class . ' invalid' : $class;
					$pending = ( 0 === (int) $event->event_approved ) ? 'pending' : '';
					$trashed = ( 2 === (int) $event->event_approved ) ? 'trashed' : '';
					$author  = ( 0 !== (int) $event->event_author ) ? get_userdata( $event->event_author ) : 'Public Submitter';

					if ( 1 === (int) $event->event_flagged && ( isset( $_GET['restrict'] ) && 'flagged' === $_GET['restrict'] ) ) {
						$spam       = 'spam';
						$pending    = '';
						$spam_label = '<strong>' . __( 'Possible spam', 'my-calendar' ) . ':</strong> ';
					} else {
						$spam       = '';
						$spam_label = '';
					}

					$trash    = ( '' !== $trashed ) ? ' - ' . __( 'Trash', 'my-calendar' ) : '';
					$draft    = ( '' !== $pending ) ? ' - ' . __( 'Draft', 'my-calendar' ) : $trash;
					$invalid  = ( $invalid ) ? ' - ' . __( 'Invalid Event', 'my-calendar' ) : $trash;
					$check    = mc_test_occurrence_overlap( $event, true );
					$problem  = ( '' !== $check ) ? 'problem' : '';
					$edit_url = admin_url( "admin.php?page=my-calendar&amp;mode=edit&amp;event_id=$event->event_id" );
					$copy_url = admin_url( "admin.php?page=my-calendar&amp;mode=copy&amp;event_id=$event->event_id" );
					if ( ! $invalid ) {
						$view_url = mc_get_details_link( $event );
					} else {
						$view_url = '';
					}
					$group_url  = admin_url( "admin.php?page=my-calendar-groups&amp;mode=edit&amp;event_id=$event->event_id&amp;group_id=$event->event_group_id" );
					$delete_url = admin_url( "admin.php?page=my-calendar-manage&amp;mode=delete&amp;event_id=$event->event_id" );
					$can_edit   = mc_can_edit_event( $event );
					if ( current_user_can( 'mc_manage_events' ) || current_user_can( 'mc_approve_events' ) || $can_edit ) {
						?>
						<tr class="<?php echo "$class $spam $pending $trashed $problem"; ?>">
							<th scope="row">
								<input type="checkbox" value="<?php echo $event->event_id; ?>" name="mass_edit[]" id="mc<?php echo $event->event_id; ?>" <?php echo ( 1 === (int) $event->event_flagged ) ? 'checked="checked"' : ''; ?> />
								<label for="mc<?php echo $event->event_id; ?>">
								<?php
								// Translators: Event ID.
								printf( __( "<span class='screen-reader-text'>Select event </span>%d", 'my-calendar' ), $event->event_id );
								?>
								</label>
							</th>
							<td>
								<strong>
								<?php
								if ( $can_edit ) {
									?>
									<a href="<?php echo $edit_url; ?>" class='edit'><span class="dashicons dashicons-edit" aria-hidden="true"></span>
									<?php
								}
								echo $spam_label;
								echo strip_tags( stripslashes( $event->event_title ) );
								if ( $can_edit ) {
									echo '</a>';
									if ( '' !== $check ) {
										// Translators: URL to edit event.
										echo '<br /><strong class="error">' . sprintf( __( 'There is a problem with this event. <a href="%s">Edit</a>', 'my-calendar' ), $edit_url ) . '</strong>';
									}
								}
								echo $draft;
								echo $invalid;
								?>
								</strong>

								<div class='row-actions'>
									<?php
									if ( mc_event_published( $event ) ) {
										?>
										<a href="<?php echo $view_url; ?>" class='view'><?php _e( 'View', 'my-calendar' ); ?></a> |
										<?php
									} elseif ( current_user_can( 'mc_manage_events' ) ) {
										?>
										<a href="<?php echo add_query_arg( 'preview', 'true', $view_url ); ?>" class='view'><?php _e( 'Preview', 'my-calendar' ); ?></a> |
										<?php
									}
									if ( $can_edit ) {
										?>
										<a href="<?php echo $copy_url; ?>" class='copy'><?php _e( 'Copy', 'my-calendar' ); ?></a>
										<?php
									}
									if ( $can_edit ) {
										if ( mc_event_is_grouped( $event->event_group_id ) ) {
											?>
											| <a href="<?php echo $group_url; ?>" class='edit group'><?php _e( 'Edit Group', 'my-calendar' ); ?></a>
											<?php
										}
										?>
										| <a href="<?php echo $delete_url; ?>" class="delete"><?php _e( 'Delete', 'my-calendar' ); ?></a>
										<?php
									} else {
										_e( 'Not editable.', 'my-calendar' );
									}
									?>
									|
									<?php
									if ( current_user_can( 'mc_approve_events' ) && $can_edit ) {
										if ( 1 === (int) $event->event_approved ) {
											$mo = 'reject';
											$te = __( 'Trash', 'my-calendar' );
										} else {
											$mo = 'publish';
											$te = __( 'Publish', 'my-calendar' );
										}
										?>
										<a href="<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;mode=$mo&amp;event_id=$event->event_id" ); ?>" class='<?php echo $mo; ?>'><?php echo $te; ?></a>
										<?php
									} else {
										switch ( $event->event_approved ) {
											case 1:
												_e( 'Published', 'my-calendar' );
												break;
											case 2:
												_e( 'Trashed', 'my-calendar' );
												break;
											default:
												_e( 'Awaiting Approval', 'my-calendar' );
										}
									}
									?>
								</div>
							</td>
							<td>
								<?php
								if ( '' !== $event->event_label ) {
									$elabel = urlencode( $event->event_label );
									?>
								<a class='mc_filter' href='<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;filter=$elabel&amp;restrict=where" ); ?>' title="<?php _e( 'Filter by location', 'my-calendar' ); ?>"><span class="screen-reader-text"><?php _e( 'Show only: ', 'my-calendar' ); ?></span><?php echo strip_tags( stripslashes( $event->event_label ) ); ?></a>
									<?php
								}
								?>
							</td>
							<td>
							<?php
							if ( '23:59:59' !== $event->event_endtime ) {
								$event_time = date_i18n( get_option( 'mc_time_format' ), mc_strtotime( $event->event_time ) );
							} else {
								$event_time = mc_notime_label( $event );
							}
							$begin = date_i18n( mc_date_format(), mc_strtotime( $event->event_begin ) );
							echo esc_html( "$begin, $event_time" );
							?>
								<div class="recurs">
									<?php echo mc_recur_string( $event ); ?>
								</div>
							</td>
							<?php
							$auth   = ( is_object( $author ) ) ? $author->ID : 0;
							$filter = admin_url( "admin.php?page=my-calendar-manage&amp;filter=$auth&amp;restrict=author" );
							$author = ( is_object( $author ) ? $author->display_name : $author );
							?>
							<td>
								<a class='mc_filter' href="<?php echo $filter; ?>" title="<?php _e( 'Filter by author', 'my-calendar' ); ?>">
									<span class="screen-reader-text"><?php _e( 'Show only: ', 'my-calendar' ); ?></span><?php echo $author; ?>
								</a>
							</td>
							<?php
							if ( ! $event->event_category ) {
								// Events *must* have a category.
								mc_update_event( 'event_category', 1, $event->event_id, '%d' );
							}
							$cat = mc_get_category_detail( $event->event_category, false );
							if ( ! is_object( $cat ) ) {
								$cat = (object) array(
									'category_color' => '',
									'category_id'    => '',
									'category_name'  => '',
								);
							}
							$color      = $cat->category_color;
							$color      = ( 0 !== strpos( $color, '#' ) ) ? '#' . $color : $color;
							$categories = mc_get_categories( $event );
							$cats       = array();
							?>
							<td>
								<div class="category-color" style="background-color:<?php echo $color; ?>;"></div>
								<a class='mc_filter' href='<?php echo admin_url( "admin.php?page=my-calendar-manage&amp;filter=$event->event_category&amp;restrict=category" ); ?>' title="<?php _e( 'Filter by category', 'my-calendar' ); ?>"><span class="screen-reader-text"><?php _e( 'Show only: ', 'my-calendar' ); ?></span><?php echo strip_tags( $cat->category_name ); ?>
								</a>
								<?php
								$string = '';
								if ( is_array( $categories ) ) {
									foreach ( $categories as $category ) {
										if ( (int) $category !== (int) $event->event_category ) {
											$cats[] = mc_get_category_detail( $category, 'category_name' );
										}
										$string = implode( ', ', $cats );
									}
									echo ( '' !== $string ) ? '(' . $string . ')' : '';
								}
								?>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</table>
		<ul class="links">
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'published' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=published' ); ?>">
				<?php
					// Translators: Number of published events.
					printf( __( 'Published (%d)', 'my-calendar' ), $counts['published'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'draft' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=draft' ); ?>">
				<?php
					// Translators: Number of draft events.
					printf( __( 'Drafts (%d)', 'my-calendar' ), $counts['draft'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'trashed' === $_GET['limit'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=trashed' ); ?>">
				<?php
					// Translators: Number of trashed events.
					printf( __( 'Trash (%d)', 'my-calendar' ), $counts['trash'] );
				?>
				</a>
			</li>
			<li>
				<a <?php echo ( isset( $_GET['restrict'] ) && 'archived' === $_GET['restrict'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;restrict=archived' ); ?>">
				<?php
					// Translators: Number of archived events.
					printf( __( 'Archived (%d)', 'my-calendar' ), $counts['archive'] );
				?>
				</a>
			</li>
			<?php
			if ( function_exists( 'akismet_http_post' ) && $allow_filters ) {
				?>
			<li>
				<a <?php echo ( isset( $_GET['restrict'] ) && 'flagged' === $_GET['restrict'] ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;restrict=flagged&amp;filter=1' ); ?>">
				<?php
					// Translators: Number of events marked as spam.
					printf( __( 'Spam (%d)', 'my-calendar' ), $counts['spam'] );
				?>
				</a>
			</li>
				<?php
			}
			?>
			<li>
				<a <?php echo ( isset( $_GET['limit'] ) && 'all' === $_GET['limit'] || ( ! isset( $_GET['limit'] ) && ! isset( $_GET['restrict'] ) ) ) ? 'class="active-link" aria-current="true"' : ''; ?>
					href="<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;limit=all' ); ?>"><?php _e( 'All', 'my-calendar' ); ?></a>
			</li>
		</ul>
			<?php
			echo $filtered;
			$num_pages = ceil( $items / $items_per_page );
			if ( $num_pages > 1 ) {
				$page_links = paginate_links(
					array(
						'base'      => add_query_arg( 'paged', '%#%' ),
						'format'    => '',
						'prev_text' => __( '&laquo; Previous<span class="screen-reader-text"> Events</span>', 'my-calendar' ),
						'next_text' => __( 'Next<span class="screen-reader-text"> Events</span> &raquo;', 'my-calendar' ),
						'total'     => $num_pages,
						'current'   => $current,
						'mid_size'  => 1,
					)
				);
				printf( "<div class='tablenav'><div class='tablenav-pages'>%s</div></div>", $page_links );
			}
			?>
		<div class='mc-admin-footer'>
			<div class="mc-actions">
				<input type="submit" class="button-secondary delete" name="mass_delete" value="<?php _e( 'Delete events', 'my-calendar' ); ?>"/>
				<input type="submit" class="button-secondary trash" name="mass_trash" value="<?php _e( 'Trash events', 'my-calendar' ); ?>"/>
				<?php
				if ( current_user_can( 'mc_approve_events' ) ) {
					?>
					<input type="submit" class="button-secondary mc-approve" name="mass_approve" value="<?php _e( 'Publish events', 'my-calendar' ); ?>"/>
					<?php
				}
				if ( ! ( isset( $_GET['restrict'] ) && 'archived' === $_GET['restrict'] ) ) {
					?>
					<input type="submit" class="button-secondary mc-archive" name="mass_archive" value="<?php _e( 'Archive events', 'my-calendar' ); ?>"/>
					<?php
				}
				?>
			</div>

			<p>
				<?php
				if ( ! ( isset( $_GET['restrict'] ) && 'archived' === $_GET['restrict'] ) ) {
					?>
					<a class='mc_filter' href='<?php echo admin_url( 'admin.php?page=my-calendar-manage&amp;restrict=archived' ); ?>'><?php _e( 'View Archived Events', 'my-calendar' ); ?></a>
					<?php
				} else {
					?>
					<a class='mc_filter' href='<?php echo admin_url( 'admin.php?page=my-calendar-manage' ); ?>'><?php _e( 'Return to Manage Events', 'my-calendar' ); ?></a>
					<?php
				}
				?>
			</p>
			</form>
			<div class='mc-search'>
			<form action="<?php echo esc_url( add_query_arg( $_GET, admin_url( 'admin.php' ) ) ); ?>" method="post">
				<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
				</div>
				<div>
					<label for="mc_search_footer" class='screen-reader-text'><?php _e( 'Search', 'my-calendar' ); ?></label>
					<input type='text' role='search' name='mcs' id='mc_search_footer' value='<?php echo ( isset( $_POST['mcs'] ) ? esc_attr( $_POST['mcs'] ) : '' ); ?>' />
					<input type='submit' value='<?php _e( 'Search Events', 'my-calendar' ); ?>' class='button-secondary'/>
				</div>
			</form>
			</div>
		</div>
			<?php
		} else {
			?>
			<p class='mc-none'><?php _e( 'There are no events in the database meeting your current criteria.', 'my-calendar' ); ?></p>
			<?php
		}
	}
}

/**
 * Review data submitted and verify.
 *
 * @param string $action Type of action being performed.
 * @param array  $post Post data.
 * @param int    $i If multiple events submitted, which index this is.
 * @param bool   $ignore_required Pass 'true' to ignore required fields.
 *
 * @return array Modified data and information about approval.
 */
function mc_check_data( $action, $post, $i, $ignore_required = false ) {
	global $wpdb, $submission;
	$user               = wp_get_current_user();
	$post               = apply_filters( 'mc_pre_checkdata', $post, $action, $i );
	$submit             = array();
	$errors             = '';
	$approved           = 0;
	$every              = '';
	$recur              = '';
	$events_access      = '';
	$begin              = '';
	$end                = '';
	$short              = '';
	$time               = '';
	$endtime            = '';
	$event_label        = '';
	$event_street       = '';
	$event_street2      = '';
	$event_city         = '';
	$event_state        = '';
	$event_postcode     = '';
	$event_region       = '';
	$event_country      = '';
	$event_url          = '';
	$event_image        = '';
	$event_phone        = '';
	$event_phone2       = '';
	$event_access       = '';
	$event_tickets      = '';
	$event_registration = '';
	$event_author       = '';
	$category           = '';
	$expires            = '';
	$event_zoom         = '';
	$host               = '';
	$event_fifth_week   = '';
	$event_holiday      = '';
	$event_group_id     = '';
	$event_span         = '';
	$event_hide_end     = '';
	$event_longitude    = '';
	$event_latitude     = '';

	if ( version_compare( PHP_VERSION, '7.4', '<' ) && get_magic_quotes_gpc() ) {
		$post = array_map( 'stripslashes_deep', $post );
	}
	if ( ! wp_verify_nonce( $post['event_nonce_name'], 'event_nonce' ) ) {
		return array();
	}

	if ( 'add' === $action || 'edit' === $action || 'copy' === $action ) {
		$title = ! empty( $post['event_title'] ) ? trim( $post['event_title'] ) : '';
		$desc  = ! empty( $post['content'] ) ? trim( $post['content'] ) : '';
		$short = ! empty( $post['event_short'] ) ? trim( $post['event_short'] ) : '';
		$recur = ! empty( $post['event_recur'] ) ? trim( $post['event_recur'] ) : '';
		$every = ! empty( $post['event_every'] ) ? (int) $post['event_every'] : 1;
		// if this is an all weekdays event, and it's been scheduled to start on a weekend, the math gets nasty.
		// ...AND there's no reason to allow it, since weekday events will NEVER happen on the weekend.
		$begin = trim( $post['event_begin'][ $i ] );
		$end   = ( ! empty( $post['event_end'] ) ) ? trim( $post['event_end'][ $i ] ) : $post['event_begin'][ $i ];
		if ( 'E' === $recur && '0' === ( mc_date( 'w', mc_strtotime( $begin ), false ) || '6' === mc_date( 'w', mc_strtotime( $begin ), false ) ) ) {
			if ( 0 === (int) mc_date( 'w', mc_strtotime( $begin ), false ) ) {
				$newbegin = my_calendar_add_date( $begin, 1 );
				if ( ! empty( $post['event_end'][ $i ] ) ) {
					$newend = my_calendar_add_date( $end, 1 );
				} else {
					$newend = $newbegin;
				}
			} elseif ( 6 === (int) mc_date( 'w', mc_strtotime( $begin ), false ) ) {
				$newbegin = my_calendar_add_date( $begin, 2 );
				if ( ! empty( $post['event_end'][ $i ] ) ) {
					$newend = my_calendar_add_date( $end, 2 );
				} else {
					$newend = $newbegin;
				}
			}
			$begin = $newbegin;
			$end   = $newend;
		} else {
			$begin = ! empty( $post['event_begin'][ $i ] ) ? trim( $post['event_begin'][ $i ] ) : '';
			$end   = ! empty( $post['event_end'][ $i ] ) ? trim( $post['event_end'][ $i ] ) : $begin;
		}

		$begin = mc_date( 'Y-m-d', mc_strtotime( $begin ), false );// regardless of entry format, convert.
		$time  = ! empty( $post['event_time'][ $i ] ) ? trim( $post['event_time'][ $i ] ) : '';
		if ( '' !== $time ) {
			$default_modifier = apply_filters( 'mc_default_event_length', '1 hour' );
			$endtime          = ! empty( $post['event_endtime'][ $i ] ) ? trim( $post['event_endtime'][ $i ] ) : mc_date( 'H:i:s', mc_strtotime( $time . ' +' . $default_modifier ), false );
			if ( empty( $post['event_endtime'][ $i ] ) && mc_date( 'H', mc_strtotime( $endtime ), false ) === '00' ) {
				// If one hour pushes event into next day, reset to 11:59pm.
				$endtime = '23:59:00';
			}
		} else {
			$endtime = ! empty( $post['event_endtime'][ $i ] ) ? trim( $post['event_endtime'][ $i ] ) : '';
		}
		$time    = ( '' === $time || '00:00:00' === $time ) ? '00:00:00' : $time; // Set at midnight if not provided.
		$endtime = ( '' === $endtime && '00:00:00' === $time ) ? '23:59:59' : $endtime; // Set at end of night if np.

		// Prevent setting enddate to incorrect value on copy.
		if ( mc_strtotime( $end ) < mc_strtotime( $begin ) && 'copy' === $action ) {
			$end = mc_date( 'Y-m-d', ( mc_strtotime( $begin ) + ( mc_strtotime( $post['prev_event_end'] ) - mc_strtotime( $post['prev_event_begin'] ) ) ), false );
		}
		if ( isset( $post['event_allday'] ) && 0 !== (int) $post['event_allday'] ) {
			$time    = '00:00:00';
			$endtime = '23:59:59';
		}

		// Verify formats.
		$time    = mc_date( 'H:i:s', mc_strtotime( $time ), false );
		$endtime = mc_date( 'H:i:s', mc_strtotime( $endtime ), false );
		$end     = mc_date( 'Y-m-d', mc_strtotime( $end ), false ); // regardless of entry format, convert.
		$repeats = ( isset( $post['event_repeats'] ) ) ? trim( $post['event_repeats'] ) : 0;
		$host    = ! empty( $post['event_host'] ) ? $post['event_host'] : $user->ID;
		$primary = false;

		if ( isset( $post['event_category'] ) ) {
			$cats = $post['event_category'];
			if ( is_array( $cats ) ) {
				// Set first category as primary.
				$primary = ( is_numeric( $cats[0] ) ) ? $cats[0] : 1;
				foreach ( $cats as $cat ) {
					$private = mc_get_category_detail( $cat, 'category_private' );
					// If a selected category is private, set that category as primary instead.
					if ( 1 === (int) $private ) {
						$primary = $cat;
					}
				}
				// Backwards compatibility for old versions of My Calendar Pro.
			} else {
				$primary = $cats;
				$cats    = array( $cats );
			}
		} else {
			$default = get_option( 'mc_default_category' );
			$default = ( ! $default ) ? mc_no_category_default( true ) : $default;
			$cats    = array( $default );
			$primary = $default;
		}
		$event_author = ( isset( $post['event_author'] ) && is_numeric( $post['event_author'] ) ) ? $post['event_author'] : 0;
		$event_link   = ! empty( $post['event_link'] ) ? trim( $post['event_link'] ) : '';
		$expires      = ! empty( $post['event_link_expires'] ) ? $post['event_link_expires'] : '0';
		$approved     = ( current_user_can( 'mc_approve_events' ) ) ? 1 : 0;
		// Check for event_approved provides support for older versions of My Calendar Pro.
		if ( isset( $post['event_approved'] ) && $post['event_approved'] !== $approved ) {
			$approved = absint( $post['event_approved'] );
		}

		$location_preset    = ! empty( $post['location_preset'] ) ? $post['location_preset'] : '';
		$event_tickets      = ( isset( $post['event_tickets'] ) ) ? trim( $post['event_tickets'] ) : '';
		$event_registration = ( isset( $post['event_registration'] ) ) ? trim( $post['event_registration'] ) : '';
		$event_image        = ( isset( $post['event_image'] ) ) ? esc_url_raw( $post['event_image'] ) : '';
		$event_fifth_week   = ! empty( $post['event_fifth_week'] ) ? 1 : 0;
		$event_holiday      = ! empty( $post['event_holiday'] ) ? 1 : 0;
		$group_id           = (int) $post['event_group_id'];
		$event_group_id     = ( ( is_array( $post['event_begin'] ) && count( $post['event_begin'] ) > 1 ) || mc_event_is_grouped( $group_id ) ) ? $group_id : 0;
		$event_span         = ( ! empty( $post['event_span'] ) && 0 !== (int) $event_group_id ) ? 1 : 0;
		$event_hide_end     = ( ! empty( $post['event_hide_end'] ) ) ? (int) $post['event_hide_end'] : 0;
		$event_hide_end     = ( '' === $time || '23:59:59' === $time ) ? 1 : $event_hide_end; // Hide end time on all day events.
		// Set location.
		if ( 'none' !== $location_preset && is_numeric( $location_preset ) ) {
			$location        = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_locations_table() . ' WHERE location_id = %d', $location_preset ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$event_label     = $location->location_label;
			$event_street    = $location->location_street;
			$event_street2   = $location->location_street2;
			$event_city      = $location->location_city;
			$event_state     = $location->location_state;
			$event_postcode  = $location->location_postcode;
			$event_region    = $location->location_region;
			$event_country   = $location->location_country;
			$event_url       = $location->location_url;
			$event_longitude = $location->location_longitude;
			$event_latitude  = $location->location_latitude;
			$event_zoom      = $location->location_zoom;
			$event_phone     = $location->location_phone;
			$event_phone2    = $location->location_phone2;
			$event_access    = $location->location_access;
		} else {
			$event_label     = ! empty( $post['event_label'] ) ? $post['event_label'] : '';
			$event_street    = ! empty( $post['event_street'] ) ? $post['event_street'] : '';
			$event_street2   = ! empty( $post['event_street2'] ) ? $post['event_street2'] : '';
			$event_city      = ! empty( $post['event_city'] ) ? $post['event_city'] : '';
			$event_state     = ! empty( $post['event_state'] ) ? $post['event_state'] : '';
			$event_postcode  = ! empty( $post['event_postcode'] ) ? $post['event_postcode'] : '';
			$event_region    = ! empty( $post['event_region'] ) ? $post['event_region'] : '';
			$event_country   = ! empty( $post['event_country'] ) ? $post['event_country'] : '';
			$event_url       = ! empty( $post['event_url'] ) ? $post['event_url'] : '';
			$event_longitude = ! empty( $post['event_longitude'] ) ? $post['event_longitude'] : '';
			$event_latitude  = ! empty( $post['event_latitude'] ) ? $post['event_latitude'] : '';
			$event_zoom      = ! empty( $post['event_zoom'] ) ? $post['event_zoom'] : '';
			$event_phone     = ! empty( $post['event_phone'] ) ? $post['event_phone'] : '';
			$event_phone2    = ! empty( $post['event_phone2'] ) ? $post['event_phone2'] : '';
			$event_access    = ! empty( $post['event_access'] ) ? $post['event_access'] : '';
			$event_access    = ! empty( $post['event_access_hidden'] ) ? unserialize( $post['event_access_hidden'] ) : $event_access;
			if ( isset( $post['mc_copy_location'] ) && 'on' === $post['mc_copy_location'] && 0 === $i ) {
				// Only the first event, if adding multiples.
				$add_loc = array(
					'location_label'     => $event_label,
					'location_street'    => $event_street,
					'location_street2'   => $event_street2,
					'location_city'      => $event_city,
					'location_state'     => $event_state,
					'location_postcode'  => $event_postcode,
					'location_region'    => $event_region,
					'location_country'   => $event_country,
					'location_url'       => $event_url,
					'location_longitude' => $event_longitude,
					'location_latitude'  => $event_latitude,
					'location_zoom'      => $event_zoom,
					'location_phone'     => $event_phone,
					'location_phone2'    => $event_phone2,
					'location_access'    => ( is_array( $event_access ) ) ? serialize( $event_access ) : '',
				);

				$add_loc     = array_map( 'mc_kses_post', $add_loc );
				$loc_formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s', '%s', '%s' );
				$wpdb->insert( my_calendar_locations_table(), $add_loc, $loc_formats );
			}
		}
		// Perform validation on the submitted dates - checks for valid years and months.
		if ( mc_checkdate( $begin ) && mc_checkdate( $end ) ) {
			// Make sure dates are equal or end date is later than start date.
			if ( mc_strtotime( "$end $endtime" ) < mc_strtotime( "$begin $time" ) ) {
				$errors .= mc_show_error( __( 'Your event end date must be either after or the same as your event begin date', 'my-calendar' ), false );
			}
		} else {
			$errors .= mc_show_error( __( 'Your date format is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.', 'my-calendar' ), false );
		}

		// Check for a valid or empty time.
		$time            = ( '' === $time ) ? '23:59:59' : mc_date( 'H:i:00', mc_strtotime( $time ), false );
		$time_format_one = '/^([0-1][0-9]):([0-5][0-9]):([0-5][0-9])$/';
		$time_format_two = '/^([2][0-3]):([0-5][0-9]):([0-5][0-9])$/';
		if ( preg_match( $time_format_one, $time ) || preg_match( $time_format_two, $time ) ) {
		} else {
			$errors .= mc_show_error( __( 'The time field must either be blank or be entered in the format hh:mm am/pm', 'my-calendar' ), false );
		}
		// Check for a valid or empty end time.
		if ( preg_match( $time_format_one, $endtime ) || preg_match( $time_format_two, $endtime ) || '' === $endtime ) {
		} else {
			$errors .= mc_show_error( __( 'The end time field must either be blank or be entered in the format hh:mm am/pm', 'my-calendar' ), false );
		}
		// Check for valid URL (blank or starting with http://).
		if ( ! ( '' === $event_link || preg_match( '/^(http)(s?)(:)\/\//', $event_link ) ) ) {
			$event_link = 'http://' . $event_link;
		}
	}
	// A title is required, and can't be more than 255 characters.
	$title_length = strlen( $title );
	if ( ! ( $title_length >= 1 && $title_length <= 255 ) ) {
		$title = __( 'Untitled Event', 'my-calendar' );
	}
	// Run checks on recurrence profile.
	$valid_recur = array( 'W', 'B', 'M', 'U', 'Y', 'D', 'E' );
	if ( ( 0 === (int) $repeats && 'S' === $recur ) || ( ( $repeats >= 0 ) && in_array( $recur, $valid_recur, true ) ) ) {
		$recur = $recur . $every;
	} else {
		// if it's not valid, assign a default value.
		$repeats = 0;
		$recur   = 'S1';
	}
	if ( isset( $post['mcs_check_conflicts'] ) ) {
		$conflicts = mcs_check_conflicts( $begin, $time, $end, $endtime, $event_label );
		$conflicts = apply_filters( 'mcs_check_conflicts', $conflicts, $post );
		if ( $conflicts ) {
			$conflict_id = $conflicts[0]->occur_id;
			$conflict_ev = mc_get_event( $conflict_id );
			if ( '1' === $conflict_ev->event_approved ) {
				$conflict = mc_get_details_link( $conflict_ev );
				// Translators: URL to event details.
				$errors .= mc_show_error( sprintf( __( 'That event conflicts with a <a href="%s">previously scheduled event</a>.', 'my-calendar' ), $conflict ), false, 'conflict' );
			} else {
				if ( mc_can_edit_event( $conflict_ev->event_id ) ) {
					$referer = urlencode( mc_get_current_url() );
					$link    = admin_url( "admin.php?page=my-calendar&amp;mode=edit&amp;event_id=$event->event_id&amp;ref=$referer" );
					// Translators: Link to edit event draft.
					$error = sprintf( __( 'That event conflicts with a <a href="%s">previously submitted draft</a>.', 'my-calendar' ), $link );
				} else {
					$error = __( 'That event conflicts with an unpublished draft event.', 'my-calendar' );
				}
				$errors .= mc_show_error( $error, false, 'draft-conflict' );
			}
		}
	}
	$spam_content = ( '' !== $desc ) ? $desc : $short;
	$spam         = mc_spam( $event_link, $spam_content, $post );
	// Likelihood that event will be flagged as spam, have a zero start time and be legit is minimal. Just kill it.
	if ( 1 === (int) $spam && '1970-01-01' === $begin ) {
		die;
	}

	$current_user = wp_get_current_user();
	$event_author = ( $event_author === $current_user->ID || current_user_can( 'mc_manage_events' ) ) ? $event_author : $current_user->ID;
	$primary      = ( ! $primary ) ? 1 : $primary;
	$cats         = ( isset( $cats ) && is_array( $cats ) ) ? $cats : array( 1 );
	$submit       = array(
		// Begin strings.
		'event_begin'        => $begin,
		'event_end'          => $end,
		'event_title'        => $title,
		'event_desc'         => force_balance_tags( $desc ),
		'event_short'        => force_balance_tags( $short ),
		'event_time'         => $time,
		'event_endtime'      => $endtime,
		'event_link'         => $event_link,
		'event_label'        => $event_label,
		'event_street'       => $event_street,
		'event_street2'      => $event_street2,
		'event_city'         => $event_city,
		'event_state'        => $event_state,
		'event_postcode'     => $event_postcode,
		'event_region'       => $event_region,
		'event_country'      => $event_country,
		'event_url'          => $event_url,
		'event_recur'        => $recur,
		'event_image'        => $event_image,
		'event_phone'        => $event_phone,
		'event_phone2'       => $event_phone2,
		'event_access'       => ( is_array( $event_access ) ) ? serialize( $event_access ) : '',
		'event_tickets'      => $event_tickets,
		'event_registration' => $event_registration,
		// Begin integers.
		'event_repeats'      => $repeats,
		'event_author'       => $event_author,
		'event_category'     => $primary,
		'event_link_expires' => $expires,
		'event_zoom'         => $event_zoom,
		'event_approved'     => $approved,
		'event_host'         => $host,
		'event_flagged'      => $spam,
		'event_fifth_week'   => $event_fifth_week,
		'event_holiday'      => $event_holiday,
		'event_group_id'     => $event_group_id,
		'event_span'         => $event_span,
		'event_hide_end'     => $event_hide_end,
		// Begin floats.
		'event_longitude'    => $event_longitude,
		'event_latitude'     => $event_latitude,
		// Array: removed before DB insertion.
		'event_categories'   => $cats,
	);
	$errors       = ( $ignore_required ) ? $errors : apply_filters( 'mc_fields_required', $errors, $submit );

	if ( '' === $errors ) {
		$ok = true;

		$submit = array_map( 'mc_kses_post', $submit );
	} else {
		$ok           = false;
		$event_access = ( is_array( $event_access ) ) ? serialize( $event_access ) : '';
		// The form is going to be rejected due to field validation issues, so we preserve the users entries here.
		// All submitted data should be in this object, regardless of data destination.
		$submission                     = ( ! is_object( $submission ) ) ? new stdClass() : $submission;
		$submission->event_id           = ( isset( $_GET['event_id'] ) && is_numeric( $_GET['event_id'] ) ) ? $_GET['event_id'] : false;
		$submission->event_title        = $title;
		$submission->event_desc         = $desc;
		$submission->event_begin        = $begin;
		$submission->event_end          = $end;
		$submission->event_time         = $time;
		$submission->event_endtime      = $endtime;
		$submission->event_recur        = $recur;
		$submission->event_repeats      = $repeats;
		$submission->event_host         = $host;
		$submission->event_category     = $primary;
		$submission->event_link         = $event_link;
		$submission->event_link_expires = $expires;
		$submission->event_label        = $event_label;
		$submission->event_street       = $event_street;
		$submission->event_street2      = $event_street2;
		$submission->event_city         = $event_city;
		$submission->event_state        = $event_state;
		$submission->event_postcode     = $event_postcode;
		$submission->event_country      = $event_country;
		$submission->event_region       = $event_region;
		$submission->event_url          = $event_url;
		$submission->event_longitude    = $event_longitude;
		$submission->event_latitude     = $event_latitude;
		$submission->event_zoom         = $event_zoom;
		$submission->event_phone        = $event_phone;
		$submission->event_phone2       = $event_phone2;
		$submission->event_author       = $event_author;
		$submission->event_short        = $short;
		$submission->event_approved     = $approved;
		$submission->event_image        = $event_image;
		$submission->event_fifth_week   = $event_fifth_week;
		$submission->event_holiday      = $event_holiday;
		$submission->event_flagged      = 0;
		$submission->event_group_id     = $event_group_id;
		$submission->event_span         = $event_span;
		$submission->event_hide_end     = $event_hide_end;
		$submission->event_access       = $event_access;
		$submission->events_access      = serialize( $events_access );
		$submission->event_tickets      = $event_tickets;
		$submission->event_registration = $event_registration;
		$submission->event_categories   = $cats;
		$submission->user_error         = true;
	}

	$data = array( $ok, $submission, $submit, $errors );

	return $data;
}

/**
 * Find event that conflicts with newly scheduled events based on time and location.
 *
 * @param string $begin date of event.
 * @param string $time time of event.
 * @param string $end date of event.
 * @param string $endtime time of event.
 * @param string $event_label location of event.
 *
 * @return mixed results array or false
 */
function mcs_check_conflicts( $begin, $time, $end, $endtime, $event_label ) {
	global $wpdb;
	$select_location = ( '' !== $event_label ) ? "event_label = '" . esc_sql( $event_label ) . "' AND" : '';
	$begin_time      = $begin . ' ' . $time;
	$end_time        = $end . ' ' . $endtime;
	// Need two queries; one to find outer events, one to find inner events.
	$event_query = 'SELECT occur_id
					FROM ' . my_calendar_event_table() . '
					JOIN ' . my_calendar_table() . "
					ON (event_id=occur_event_id)
					WHERE $select_location " . '
					( occur_begin BETWEEN cast( \'%1$s\' AS DATETIME ) AND cast( \'%2$s\' AS DATETIME )
					OR occur_end BETWEEN cast( \'%3$s\' AS DATETIME ) AND cast( \'%4$s\' AS DATETIME ) )';

	$results = $wpdb->get_results( $wpdb->prepare( $event_query, $begin_time, $end_time, $begin_time, $end_time ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	if ( empty( $results ) ) {
		// Finds events that conflict if they either start or end during another event.
		$event_query2 = 'SELECT occur_id
						FROM ' . my_calendar_event_table() . '
						JOIN ' . my_calendar_table() . "
						ON (event_id=occur_event_id)
						WHERE $select_location " . '
						( cast( \'%1$s\' AS DATETIME ) BETWEEN occur_begin AND occur_end
						OR cast( \'%2$s\' AS DATETIME ) BETWEEN occur_begin AND occur_end )';

		$results = $wpdb->get_results( $wpdb->prepare( $event_query2, $begin_time, $end_time ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	return ( ! empty( $results ) ) ? $results : false;
}

/**
 * Compare whether event date or recurrence characteristics have changed.
 *
 * @param array $update data being saved in update.
 * @param int   $id id of event being modified.
 *
 * @return boolean false if unmodified.
 */
function mc_compare( $update, $id ) {
	$event         = mc_get_first_event( $id );
	$update_string = '';
	$event_string  = '';

	foreach ( $update as $k => $v ) {
		// Event_recur and event_repeats always set to single and 0; event_begin and event_end need to be checked elsewhere.
		if ( 'event_recur' !== $k && 'event_repeats' !== $k && 'event_begin' !== $k && 'event_end' !== $k ) {
			$update_string .= trim( $v );
			$event_string  .= trim( $event->$k );
		}
	}
	$update_hash = md5( $update_string );
	$event_hash  = md5( $event_string );
	if ( $update_hash === $event_hash ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Update a single event instance.
 *
 * @param int   $event_instance Instance ID.
 * @param int   $event_id New event ID.
 * @param array $update New date array.
 *
 * Return query result.
 */
function mc_update_instance( $event_instance, $event_id, $update = array() ) {
	global $wpdb;
	if ( ! empty( $update ) ) {
		$event   = mc_get_event( $event_instance );
		$formats = array( '%d', '%s', '%s', '%d' );
		$begin   = ( ! empty( $update ) ) ? $update['event_begin'] . ' ' . $update['event_time'] : $event->occur_begin;
		$end     = ( ! empty( $update ) ) ? $update['event_end'] . ' ' . $update['event_endtime'] : $event->occur_end;
		$data    = array(
			'occur_event_id' => $event_id,
			'occur_begin'    => $begin,
			'occur_end'      => $end,
			'occur_group_id' => $update['event_group_id'],
		);
	} else {
		$formats  = array( '%d', '%d' );
		$group_id = mc_get_data( 'event_group_id', $event_id );
		$data     = array(
			'occur_event_id' => $event_id,
			'occur_group_id' => $group_id,
		);
	}

	$result = $wpdb->update( my_calendar_event_table(), $data, array( 'occur_id' => $event_instance ), $formats, '%d' );

	return $result;
}

/**
 * Update a single arbitrary field in event table
 *
 * @param int    $event_id Event to modify.
 * @param string $field column name for field.
 * @param mixed  $value required value for field.
 * @param string $format type of data format.
 *
 * @return mixed boolean/int $result Success condition
 */
function mc_update_data( $event_id, $field, $value, $format = '%d' ) {
	global $wpdb;
	$data    = array( $field => $value );
	$formats = ( $format );
	$result  = $wpdb->update( my_calendar_table(), $data, array( 'event_id' => $event_id ), $formats, '%d' );

	return $result;
}

/**
 * Get next available group ID
 *
 * @return int
 */
function mc_group_id() {
	global $wpdb;
	$result = $wpdb->get_var( 'SELECT MAX(event_id) FROM ' . my_calendar_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$next   = $result + 1;

	return $next;
}

/**
 * Return all instances of a given event.
 *
 * @param array $args Arguments describing the output type.
 *
 * @return string HTML list of instance data & single event view
 */
function mc_instance_list( $args ) {
	$id = isset( $args['event'] ) ? $args['event'] : false;
	if ( ! $id ) {
		return;
	}
	$template = isset( $args['template'] ) ? $args['template'] : '<h3>{title}</h3>{description}';
	$list     = isset( $args['list'] ) ? $args['list'] : '<li>{date}, {time}</li>';
	$before   = isset( $args['before'] ) ? $args['before'] : '<ul>';
	$after    = isset( $args['after'] ) ? $args['after'] : '</ul>';
	$instance = isset( $args['instance'] ) ? $args['instance'] : false;

	global $wpdb;
	$output = '';
	if ( true === $instance || '1' === $instance ) {
		$sql = 'SELECT * FROM ' . my_calendar_event_table() . ' WHERE occur_id=%d ORDER BY occur_begin ASC';
	} else {
		$sql = 'SELECT * FROM ' . my_calendar_event_table() . ' WHERE occur_event_id=%d ORDER BY occur_begin ASC';
	}
	$results = $wpdb->get_results( $wpdb->prepare( $sql, $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( is_array( $results ) ) {
		$details = '';
		foreach ( $results as $result ) {
			$event_id = $result->occur_id;
			$event    = mc_get_event( $event_id );
			$array    = mc_create_tags( $event );
			if ( in_array( $template, array( 'details', 'grid', 'list', 'mini' ), true ) || mc_key_exists( $template ) ) {
				if ( 1 === (int) get_option( 'mc_use_' . $template . '_template' ) ) {
					$template = mc_get_template( $template );
				} elseif ( mc_key_exists( $template ) ) {
					$template = mc_get_custom_template( $template );
				} else {
					$details = my_calendar_draw_event( $event, 'single', $event->event_begin, $event->event_time, '' );
				}
			}
			$item = ( '' !== $list ) ? mc_draw_template( $array, $list ) : '';
			if ( '' === $details ) {
				$details = ( '' !== $template ) ? mc_draw_template( $array, $template ) : '';
			}
			$output .= $item;
			if ( '' === $list ) {
				break;
			}
		}
		$output = $details . $before . $output . $after;

	}

	return mc_run_shortcodes( $output );
}

/**
 * Generate a list of instances for the currently edited event
 *
 * @param int $id Event ID.
 * @param int $occur Specific occurrence ID.
 *
 * @return string list of event dates
 */
function mc_admin_instances( $id, $occur = false ) {
	global $wpdb;
	$output     = '';
	$results    = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_event_table() . ' WHERE occur_event_id=%d ORDER BY occur_begin ASC', $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$event_post = mc_get_event_post( $id );
	$deleted    = get_post_meta( $event_post, '_mc_deleted_instances', true );
	if ( is_array( $results ) && is_admin() ) {
		foreach ( $results as $result ) {
			$begin = "<span id='occur_date_$result->occur_id'>" . date_i18n( mc_date_format(), mc_strtotime( $result->occur_begin ) ) . ', ' . mc_date( get_option( 'mc_time_format' ), mc_strtotime( $result->occur_begin ), false ) . '</span>';
			if ( $result->occur_id === $occur ) {
				$control = '';
				$edit    = '<em>' . __( 'Editing Now', 'my-calendar' ) . '</em>';
			} else {
				$control = "$begin: <button class='delete_occurrence' type='button' data-event='$result->occur_event_id' data-begin='$result->occur_begin' data-end='$result->occur_end' data-value='$result->occur_id' aria-describedby='occur_date_$result->occur_id' />" . __( 'Delete', 'my-calendar' ) . '</button> ';
				$edit    = "<a href='" . admin_url( 'admin.php?page=my-calendar' ) . "&amp;mode=edit&amp;event_id=$id&amp;date=$result->occur_id' aria-describedby='occur_date_$result->occur_id'>" . __( 'Edit', 'my-calendar' ) . '</a>';
			}
			$output .= "<li>$control$edit</li>";
		}
	}

	return $output;
}

/**
 * Check whether an event is a member of a group
 *
 * @param int $group_id Event Group ID.
 *
 * @return boolean
 */
function mc_event_is_grouped( $group_id ) {
	global $wpdb;
	if ( 0 === (int) $group_id ) {
		return false;
	} else {
		$value = $wpdb->get_var( $wpdb->prepare( 'SELECT count( event_group_id ) FROM ' . my_calendar_table() . ' WHERE event_group_id = %d', $group_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( $value > 1 ) {

			return true;
		} else {

			return false;
		}
	}
}

/**
 * Test an event and see if it's an all day event.
 *
 * @param object $event Event object.
 *
 * @return boolean
 */
function mc_is_all_day( $event ) {

	return ( '00:00:00' === $event->event_time && '23:59:59' === $event->event_endtime ) ? true : false;
}

/**
 * Generate normal date time input fields
 *
 * @param string  $form Previous defined values.
 * @param boolean $has_data Whether field has data.
 * @param object  $data form data object.
 * @param int     $instance [not used here].
 * @param string  $context rendering context [not used].
 *
 * @return string submission form part
 */
function mc_standard_datetime_input( $form, $has_data, $data, $instance, $context = 'admin' ) {
	if ( $has_data ) {
		$event_begin = esc_attr( $data->event_begin );
		$event_end   = esc_attr( $data->event_end );

		if ( isset( $_GET['date'] ) ) {
			$event       = mc_get_event( (int) $_GET['date'] );
			$event_begin = mc_date( 'Y-m-d', mc_strtotime( $event->occur_begin ), false );
			$event_end   = mc_date( 'Y-m-d', mc_strtotime( $event->occur_end ), false );
		}
		// Set event end to empty if matches begin. Makes input and changes easier.
		if ( $event_begin === $event_end ) {
			$event_end = '';
		}
		$starttime = ( mc_is_all_day( $data ) ) ? '' : mc_date( apply_filters( 'mc_time_format', 'h:i A' ), mc_strtotime( $data->event_time ), false );
		$endtime   = ( mc_is_all_day( $data ) ) ? '' : mc_date( apply_filters( 'mc_time_format', 'h:i A' ), mc_strtotime( $data->event_endtime ), false );
	} else {
		$event_begin = mc_date( 'Y-m-d' );
		$event_end   = '';
		$starttime   = '';
		$endtime     = '';
	}

	$allday       = ( $has_data && ( mc_is_all_day( $data ) ) ) ? ' checked="checked"' : '';
	$hide         = ( $has_data && '1' === $data->event_hide_end ) ? ' checked="checked"' : '';
	$allday_label = ( $has_data ) ? mc_notime_label( $data ) : get_option( 'mc_notime_text' );

	$form .= '<div>
		<label for="mc_event_date" id="eblabel">' . __( 'Date (YYYY-MM-DD)', 'my-calendar' ) . '</label> <div class="picker-container"><input type="text" id="mc_event_date" class="mc-datepicker" name="event_begin[]" size="10" value="" data-value="' . esc_attr( $event_begin ) . '" /></div>
		<label for="mc_event_time">' . __( 'From', 'my-calendar' ) . '</label>
		<div class="picker-container"><input type="text" id="mc_event_time" class="mc-timepicker" name="event_time[]" size="8" value="' . esc_attr( $starttime ) . '" /></div>
		<label for="mc_event_endtime">' . __( 'To', 'my-calendar' ) . '</label>
		<div class="picker-container"><input type="text" id="mc_event_endtime" class="mc-timepicker" name="event_endtime[]" size="8" value="' . esc_attr( $endtime ) . '" /></div>
	</div>
	<ul>
		<li><input type="checkbox" value="1" id="e_allday" name="event_allday"' . $allday . ' /> <label for="e_allday">' . __( 'All day event', 'my-calendar' ) . '</label> <span class="event_time_label"><label for="e_time_label">' . __( 'Time label:', 'my-calendar' ) . '</label> <input type="text" name="event_time_label" id="e_time_label" value="' . esc_attr( $allday_label ) . '" /> </li>
		<li><input type="checkbox" value="1" id="e_hide_end" name="event_hide_end"' . $hide . ' /> <label for="e_hide_end">' . __( 'Hide end time', 'my-calendar' ) . '</label></li>
	</ul>
	<div>
		<label for="mc_event_enddate" id="eelabel"><em>' . __( 'End Date (YYYY-MM-DD, optional)', 'my-calendar' ) . '</em></label> <div class="picker-container"><input type="text" name="event_end[]" id="mc_event_enddate" class="mc-datepicker" size="10" value="" data-value="' . esc_attr( $event_end ) . '" /></div>
	</div>';

	return $form;
}

/**
 * Date time inputs to add a single instance to recurring event info
 *
 * @param object $data Source event data.
 *
 * @return string form HTML
 */
function mc_recur_datetime_input( $data ) {
	$event_begin = ( $data->event_begin ) ? $data->event_begin : mc_date( 'Y-m-d' );
	$event_end   = ( $data->event_end && $data->event_end !== $data->event_begin ) ? $data->event_end : '';
	$starttime   = ( $data->event_time ) ? $data->event_time : '';
	$endtime     = ( $data->event_endtime ) ? $data->event_endtime : '';

	$form = '<p>
		<label for="r_begin">' . __( 'Date (YYYY-MM-DD)', 'my-calendar' ) . '</label> <input type="text" id="r_begin" class="mc-datepicker" name="recur_begin[]" size="10" value="" data-value="' . esc_attr( $event_begin ) . '" />
		<label for="r_time">' . __( 'From', 'my-calendar' ) . '</label>
		<input type="text" id="r_time" class="mc-timepicker" name="recur_time[]" size="8" value="' . esc_attr( $starttime ) . '" />
		<label for="r_endtime">' . __( 'To', 'my-calendar' ) . '</label>
		<input type="text" id="r_endtime" class="mc-timepicker" name="recur_endtime[]" size="8" value="' . esc_attr( $endtime ) . '" />
	</p>
	<p>
		<label for="r_end"><em>' . __( 'End Date (YYYY-MM-DD, optional)', 'my-calendar' ) . '</em></label> <input type="text" name="recur_end[]" id="r_end" class="mc-datepicker" size="10" value="" data-value="' . esc_attr( $event_end ) . '" />
	</p>';

	return $form;
}

/**
 * Generate standard event registration info fields.
 *
 * @param string  $form Form HTML.
 * @param boolean $has_data Does this event have data.
 * @param object  $data Data for event.
 * @param string  $context Context displayed in.
 *
 * @return string HTML output for form
 */
function mc_standard_event_registration( $form, $has_data, $data, $context = 'admin' ) {
	if ( $has_data ) {
		$tickets      = $data->event_tickets;
		$registration = stripslashes( esc_attr( $data->event_registration ) );
	} else {
		$tickets      = '';
		$registration = '';
		$default      = 'checked="checked"';
	}

	$form .= "<p>
				<label for='event_tickets'>" . __( 'Tickets URL', 'my-calendar' ) . "</label> <input type='url' name='event_tickets' id='event_tickets' value='" . esc_attr( $tickets ) . "' />
			</p>
			<p>
				<label for='event_registration'>" . __( 'Registration Information', 'my-calendar' ) . "</label> <textarea name='event_registration'id='event_registration'cols='40'rows='4'/>$registration</textarea>
			</p>";

	return apply_filters( 'mc_event_registration_form', $form, $has_data, $data, 'admin' );
}


add_action( 'save_post', 'mc_post_update_event' );
/**
 * When updating event post, make sure changed featured image is copied into event.
 *
 * @param int $id Post ID.
 */
function mc_post_update_event( $id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $id ) || ! ( get_post_type( $id ) === 'mc-events' ) ) {
		return $id;
	}
	$post           = get_post( $id );
	$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
	$event_id       = get_post_meta( $post->ID, '_mc_event_id', true );
	if ( esc_url( $featured_image ) ) {
		mc_update_data( $event_id, 'event_image', $featured_image, '%s' );
	}
}

/**
 * Parse a string and replace internationalized months with English so strtotime() will parse correctly
 *
 * @param string $string Date information.
 *
 * @return string de-internationalized change
 */
function mc_strtotime( $string ) {
	$months  = array(
		date_i18n( 'F', strtotime( 'January 1' ) ),
		date_i18n( 'F', strtotime( 'February 1' ) ),
		date_i18n( 'F', strtotime( 'March 1' ) ),
		date_i18n( 'F', strtotime( 'April 1' ) ),
		date_i18n( 'F', strtotime( 'May 1' ) ),
		date_i18n( 'F', strtotime( 'June 1' ) ),
		date_i18n( 'F', strtotime( 'July 1' ) ),
		date_i18n( 'F', strtotime( 'August 1' ) ),
		date_i18n( 'F', strtotime( 'September 1' ) ),
		date_i18n( 'F', strtotime( 'October 1' ) ),
		date_i18n( 'F', strtotime( 'November 1' ) ),
		date_i18n( 'F', strtotime( 'December 1' ) ),
		date_i18n( 'M', strtotime( 'January 1' ) ),
		date_i18n( 'M', strtotime( 'February 1' ) ),
		date_i18n( 'M', strtotime( 'March 1' ) ),
		date_i18n( 'M', strtotime( 'April 1' ) ),
		date_i18n( 'M', strtotime( 'May 1' ) ),
		date_i18n( 'M', strtotime( 'June 1' ) ),
		date_i18n( 'M', strtotime( 'July 1' ) ),
		date_i18n( 'M', strtotime( 'August 1' ) ),
		date_i18n( 'M', strtotime( 'September 1' ) ),
		date_i18n( 'M', strtotime( 'October 1' ) ),
		date_i18n( 'M', strtotime( 'November 1' ) ),
		date_i18n( 'M', strtotime( 'December 1' ) ),
	);
	$english = array(
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December',
		'Jan',
		'Feb',
		'Mar',
		'Apr',
		'May',
		'Jun',
		'Jul',
		'Aug',
		'Sep',
		'Oct',
		'Nov',
		'Dec',
	);

	return strtotime( str_replace( $months, $english, $string ) );
}

/**
 * Generate controls for a given event
 *
 * @param string  $mode Context of event editing page.
 * @param boolean $has_data Does this event have data.
 * @param object  $event Event data.
 * @param string  $position location of form.
 *
 * @return string output controls
 */
function mc_controls( $mode, $has_data, $event, $position = 'header' ) {
	$text_link = '';
	$controls  = array();

	if ( 'edit' === $mode ) {
		$publish_text = __( 'Save', 'my-calendar' );
		$event_id     = $event->event_id;
		$args         = '';
		if ( isset( $_GET['date'] ) ) {
			$id = ( is_numeric( $_GET['date'] ) ) ? $_GET['date'] : false;
			if ( $id ) {
				$args = "&amp;date=$id";
			}
		}
		$controls['delete'] = "<span class='dashicons dashicons-no' aria-hidden='true'></span><a href='" . admin_url( "admin.php?page=my-calendar-manage&amp;mode=delete&amp;event_id=$event_id$args" ) . "' class='delete'>" . __( 'Delete', 'my-calendar' ) . '</a>';
		if ( 'true' === apply_filters( 'mc_use_permalinks', get_option( 'mc_use_permalinks' ) ) ) {
			$post_id          = $event->event_post;
			$post_link        = ( $post_id ) ? get_edit_post_link( $post_id ) : false;
			$controls['post'] = ( $post_link ) ? sprintf( "<span class='dashicons dashicons-admin-post' aria-hidden='true'></span><a href='%s'>" . __( 'Edit Event Post', 'my-calendar' ) . '</a>', $post_link ) : '';
		}
	} else {
		$publish_text = __( 'Publish', 'my-calendar' );
	}

	if ( $has_data && is_object( $event ) ) {
		$first    = mc_get_first_event( $event->event_id );
		$view_url = mc_get_details_link( $first );
		if ( mc_event_published( $event ) ) {
			$controls['view'] = "<span class='dashicons dashicons-laptop' aria-hidden='true'></span><a href='" . $view_url . "' class='view'>" . __( 'View', 'my-calendar' ) . '</a>';
		} elseif ( current_user_can( 'mc_manage_events' ) ) {
			$controls['view'] = "<span class='dashicons dashicons-laptop' aria-hidden='true'></span><a href='" . add_query_arg( 'preview', 'true', $view_url ) . "' class='view'>" . __( 'Preview', 'my-calendar' ) . '</a>';
		}
	}

	$manage_text         = ( current_user_can( 'mc_manage_events' ) ) ? __( 'Manage events', 'my-calendar' ) : __( 'Manage your events', 'my-calendar' );
	$controls['manage']  = "<span class='dashicons dashicons-calendar' aria-hidden='true'></span>" . '<a href="' . admin_url( 'admin.php?page=my-calendar-manage' ) . '">' . $manage_text . '</a>';
	$controls['publish'] = '<input type="submit" name="save" class="button-primary" value="' . esc_attr( $publish_text ) . '" />';
	// Event Status settings: draft, published, trash, (custom).
	// Switch to select status.
	if ( 'header' === $position ) {
		if ( 'edit' === $mode ) {
			$controls['prev_status'] = "<input type='hidden' name='prev_event_status' value='" . absint( $event->event_approved ) . "' />";
			if ( current_user_can( 'mc_approve_events' ) || current_user_can( 'mc_publish_events' ) ) { // Added by Roland P.
				if ( $has_data && '1' === $event->event_approved ) {
					$checked = ' checked="checked"';
				} elseif ( $has_data && 0 === (int) $event->event_approved ) {
					$checked = '';
				}
				$status_control = "
						<option value='1'" . selected( $event->event_approved, '1', false ) . '>' . __( 'Publish', 'my-calendar' ) . "</option>
						<option value='0'" . selected( $event->event_approved, '0', false ) . '>' . __( 'Draft', 'my-calendar' ) . "</option>
						<option value='2'" . selected( $event->event_approved, '2', false ) . '>' . __( 'Trash', 'my-calendar' ) . '</option>';
			} else {
				$status_control = "
						<option value='0'" . selected( $event->event_approved, '0', false ) . '>' . __( 'Draft', 'my-calendar' ) . "</option>
						<option value='2'" . selected( $event->event_approved, '2', false ) . '>' . __( 'Trash', 'my-calendar' ) . '</option>';
			}
		} else { // Case: adding new event (if user can, then 1, else 0).
			if ( current_user_can( 'mc_approve_events' ) || current_user_can( 'mc_publish_events' ) ) {
				$status_control = "
						<option value='1'>" . __( 'Published', 'my-calendar' ) . "</option>
						<option value='0'>" . __( 'Draft', 'my-calendar' ) . '</option>';
			} else {
				$status_control = "
						<option value='0'>" . __( 'Draft', 'my-calendar' ) . '</option>';
			}
		}
		$controls['status'] = "
					<label for='e_approved' class='screen-reader-text'>" . __( 'Status', 'my-calendar' ) . "</label>
					<select name='event_approved' id='e_approved'>
						$status_control
					</select>";
	}

	$controls_output = '';
	foreach ( $controls as $key => $control ) {
		if ( 'prev_status' !== $key ) {
			$control = '<li>' . $control . '</li>';
		}

		$controls_output .= $control;
	}

	return '<ul>' . $controls_output . '</ul>';
}

/**
 * Get a list of related events and list admin editing links
 *
 * @param int $id group ID.
 */
function mc_related_events( $id ) {
	global $wpdb;
	$id     = (int) $id;
	$output = '';

	$results = mc_get_related( $id );
	if ( is_array( $results ) && ! empty( $results ) ) {
		foreach ( $results as $result ) {
			$result = mc_get_first_event( $result->event_id );
			if ( ! is_object( $result ) ) {
				continue;
			}
			$event    = $result->occur_event_id;
			$current  = '<a href="' . admin_url( 'admin.php?page=my-calendar' ) . '&amp;mode=edit&amp;event_id=' . $event . '">';
			$end      = '</a>';
			$begin    = date_i18n( mc_date_format(), strtotime( $result->occur_begin ) ) . ', ' . mc_date( get_option( 'mc_time_format' ), strtotime( $result->occur_begin ), false );
			$template = $current . $begin . $end;
			$output  .= "<li>$template</li>";
		}
	} else {
		$output = '<li>' . __( 'No related events', 'my-calendar' ) . '</li>';
	}

	echo $output;
}

/**
 * Can the current user edit this category?
 *
 * @param int $category Category ID.
 * @param int $user User ID.
 *
 * @return boolean
 */
function mc_can_edit_category( $category, $user ) {
	$permissions = get_user_meta( $user, 'mc_user_permissions', true );
	$permissions = apply_filters( 'mc_user_permissions', $permissions, $category, $user );

	if ( ( ! $permissions || empty( $permissions ) ) || in_array( 'all', $permissions, true ) || in_array( $category, $permissions, true ) || current_user_can( 'manage_options' ) ) {
		return true;
	}

	return false;
}


/**
 * Unless an admin, authors can only edit their own events if they don't have mc_manage_events capabilities.
 *
 * @param mixed object/boolean $event Event object.
 *
 * @return boolean
 */
function mc_can_edit_event( $event = false ) {
	global $wpdb;
	if ( ! $event ) {

		return false;
	}

	$api = apply_filters( 'mc_api_can_edit_event', false, $event );
	if ( $api ) {

		return $api;
	}

	if ( ! is_user_logged_in() ) {

		return false;
	}

	if ( is_object( $event ) ) {
		$event_id     = $event->event_id;
		$event_author = $event->event_author;
	} elseif ( is_int( $event ) ) {
		$event_id = $event;
		$event    = mc_get_first_event( $event );
		if ( ! is_object( $event ) ) {
			$event = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_table() . ' WHERE event_id=%d LIMIT 1', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		$event_author = $event->event_author;
	} else {
		// What is the case where the event is neither an object, int, or falsey? Hmm.
		$event_author = wp_get_current_user()->ID;
		$event_id     = $event;
	}

	$current_user    = wp_get_current_user();
	$user            = $current_user->ID;
	$categories      = mc_get_categories( $event_id );
	$has_permissions = true;
	if ( is_array( $categories ) ) {
		foreach ( $categories as $cat ) {
			// If user doesn't have access to all relevant categories, prevent editing.
			if ( ! $has_permissions ) {
				continue;
			}
			$has_permissions = mc_can_edit_category( $cat, $user );
		}
	}
	$return = false;

	if ( ( current_user_can( 'mc_manage_events' ) && $has_permissions ) || ( $user === (int) $event_author ) ) {

		$return = true;
	}

	return apply_filters( 'mc_can_edit_event', $return, $event_id );
}

/**
 * Produce the human-readable string for recurrence.
 *
 * @param object $event Event object.
 *
 * @return string Type of recurrence
 */
function mc_recur_string( $event ) {
	$recurs = str_split( $event->event_recur, 1 );
	$recur  = $recurs[0];
	$every  = ( isset( $recurs[1] ) ) ? str_replace( $recurs[0], '', $event->event_recur ) : 1;
	$string = '';
	// Interpret the DB values into something human readable.
	switch ( $recur ) {
		case 'D':
			// Translators: number of days between repetitions.
			$string = ( 1 === (int) $every ) ? __( 'Daily', 'my-calendar' ) : sprintf( __( 'Every %d days', 'my-calendar' ), $every );
			break;
		case 'E':
			// Translators: number of days between repetitions.
			$string = ( 1 === (int) $every ) ? __( 'Weekdays', 'my-calendar' ) : sprintf( __( 'Every %d weekdays', 'my-calendar' ), $every );
			break;
		case 'W':
			// Translators: number of weeks between repetitions.
			$string = ( 1 === (int) $every ) ? __( 'Weekly', 'my-calendar' ) : sprintf( __( 'Every %d weeks', 'my-calendar' ), $every );
			break;
		case 'B':
			$string = __( 'Bi-Weekly', 'my-calendar' );
			break;
		case 'M':
			// Translators: number of months between repetitions.
			$string = ( 1 === (int) $every ) ? __( 'Monthly (by date)', 'my-calendar' ) : sprintf( __( 'Every %d months (by date)', 'my-calendar' ), $every );
			break;
		case 'U':
			$string = __( 'Monthly (by day)', 'my-calendar' );
			break;
		case 'Y':
			// Translators: number of years between repetitions.
			$string = ( 1 === (int) $every ) ? __( 'Yearly', 'my-calendar' ) : sprintf( __( 'Every %d years', 'my-calendar' ), $every );
			break;
	}
	$eternity = _mc_increment_values( $recur );
	if ( $event->event_repeats > 0 && 'S' !== $recur ) {
		// Translators: number of repeats.
		$string .= ' ' . sprintf( __( '&ndash; %d Times', 'my-calendar' ), $event->event_repeats );
	} elseif ( $eternity ) {
		// Translators: number of repeats.
		$string .= ' ' . sprintf( __( '&ndash; %d Times', 'my-calendar' ), $eternity );
	}

	return $string;
}


/**
 * Generate recurrence options list
 *
 * @param string $value current event's value.
 *
 * @return string form options
 */
function mc_recur_options( $value ) {
	$s = ( 'S' === $value ) ? ' selected="selected"' : '';
	$d = ( 'D' === $value ) ? ' selected="selected"' : '';
	$e = ( 'E' === $value ) ? ' selected="selected"' : '';
	$w = ( 'W' === $value || 'B' === $value ) ? ' selected="selected"' : '';
	$m = ( 'M' === $value ) ? ' selected="selected"' : '';
	$u = ( 'U' === $value ) ? ' selected="selected"' : '';
	$y = ( 'Y' === $value ) ? ' selected="selected"' : '';

	$return = "
				<option class='input' value='S' $s>" . __( 'Does not recur', 'my-calendar' ) . "</option>
				<option class='input' value='D' $d>" . __( 'Days', 'my-calendar' ) . "</option>
				<option class='input' value='E' $e>" . __( 'Days, weekdays only', 'my-calendar' ) . "</option>
				<option class='input' value='W' $w>" . __( 'Weeks', 'my-calendar' ) . "</option>
				<option class='input' value='M' $m>" . __( 'Months by date (e.g., the 24th of each month)', 'my-calendar' ) . "</option>
				<option class='input' value='U' $u>" . __( 'Month by day (e.g., the 3rd Monday of each month)', 'my-calendar' ) . "</option>
				<option class='input' value='Y' $y>" . __( 'Year', 'my-calendar' ) . '</option>';

	return $return;
}

/**
 * Determine max values to increment
 *
 * @param string $recur Type of recurrence.
 */
function _mc_increment_values( $recur ) {
	switch ( $recur ) {
		case 'S': // Single.
			return 0;
			break;
		case 'D': // Daily.
			return 500;
			break;
		case 'E': // Weekdays.
			return 400;
			break;
		case 'W': // Weekly.
			return 240;
			break;
		case 'B': // Biweekly.
			return 240;
			break;
		case 'M': // Monthly.
		case 'U':
			return 240;
			break;
		case 'Y':
			return 50;
			break;
		default:
			false;
	}
}

/**
 * Get all existing instances of an ID. Assemble into array with dates as keys
 *
 * @param int $id Event ID.
 *
 * @return array of event dates & instance IDs
 */
function mc_get_instances( $id ) {
	global $wpdb;
	$id      = (int) $id;
	$results = $wpdb->get_results( $wpdb->prepare( 'SELECT occur_id, occur_begin FROM ' . my_calendar_event_table() . ' WHERE occur_event_id = %d', $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$return  = array();

	foreach ( $results as $result ) {
		$key            = sanitize_key( mc_date( 'Y-m-d', strtotime( $result->occur_begin ), false ) );
		$return[ $key ] = $result->occur_id;
	}

	return $return;
}

/**
 * Deletes all instances of an event without deleting the event details. Sets stage for rebuilding event instances.
 *
 * @param int $id Event ID.
 */
function mc_delete_instances( $id ) {
	global $wpdb;
	$id = (int) $id;
	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_event_table() . ' WHERE occur_event_id = %d', $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	// After bulk deletion, optimize table.
	$wpdb->query( 'OPTIMIZE TABLE ' . my_calendar_event_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

add_filter( 'mc_instance_data', 'mc_reuse_id', 10, 3 );
/**
 * If an instance ID is for the same starting date (date *only*), use same ID
 *
 * @param array  $data data to be inserted into occurrences.
 * @param string $begin Starting time for the new occurrence.
 * @param array  $instances Array of previous instances for this event.
 *
 * @return array new data to insert
 */
function mc_reuse_id( $data, $begin, $instances ) {
	$begin = sanitize_key( mc_date( 'Y-m-d', $begin, false ) );
	$keys  = array_keys( $instances );
	if ( ! empty( $instances ) && in_array( $begin, $keys, true ) ) {
		$restore_id       = $instances[ $begin ];
		$data['occur_id'] = $restore_id;
	}

	return $data;
}

add_filter( 'mc_instance_format', 'mc_reuse_id_format', 10, 3 );
/**
 * If an instance ID is for the same starting date (date *only*), return format for altered insertion.
 *
 * @param array  $format Original formats array.
 * @param string $begin Starting time for the new occurrence.
 * @param array  $instances Array of previous instances for this event.
 *
 * @return array new formats for data
 */
function mc_reuse_id_format( $format, $begin, $instances ) {
	$begin = sanitize_key( mc_date( 'Y-m-d', $begin, false ) );
	$keys  = array_keys( $instances );
	if ( ! empty( $instances ) && in_array( $begin, $keys, true ) ) {
		$format = array( '%d', '%s', '%s', '%d', '%d' );
	}

	return $format;
}

/**
 * Given a recurrence pattern and a start date/time, increment the additional instances of an event.
 *
 * @param integer $id Event ID in my_calendar db.
 * @param array   $post an array of POST data (or array containing dates).
 * @param boolean $test true if testing.
 * @param array   $instances When rebuilding, an array of all prior event dates & ids.
 *
 * @return null by default; data array if testing
 */
function mc_increment_event( $id, $post = array(), $test = false, $instances = array() ) {
	global $wpdb;
	$event  = mc_get_event_core( $id, true );
	$data   = array();
	$return = array();
	if ( empty( $post ) ) {
		$orig_begin = $event->event_begin . ' ' . $event->event_time;
		$orig_end   = $event->event_end . ' ' . $event->event_endtime;
	} else {
		$post_begin   = ( isset( $post['event_begin'] ) ) ? $post['event_begin'] : '';
		$post_time    = ( isset( $post['event_time'] ) ) ? $post['event_time'] : '';
		$post_end     = ( isset( $post['event_end'] ) ) ? $post['event_end'] : '';
		$post_endtime = ( isset( $post['event_endtime'] ) ) ? $post['event_endtime'] : '';
		$orig_begin   = $post_begin . ' ' . $post_time;
		$orig_end     = $post_end . ' ' . $post_endtime;
	}

	$group_id = $event->event_group_id;
	$format   = array( '%d', '%s', '%s', '%d' );
	$recurs   = str_split( $event->event_recur, 1 );
	$recur    = $recurs[0];
	// Can't use 2nd value directly if it's two digits.
	$every = ( isset( $recurs[1] ) ) ? str_replace( $recurs[0], '', $event->event_recur ) : 1;
	if ( 'S' !== $recur ) {
		// If this event had a rep of 0, translate that.
		$event_repetition = ( 0 !== (int) $event->event_repeats ) ? $event->event_repeats : _mc_increment_values( $recur );
		$numforward       = (int) $event_repetition;
		if ( 'S' !== $recur ) {
			switch ( $recur ) {
				case 'D':
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$begin = my_calendar_add_date( $orig_begin, $i * $every, 0, 0 );
						$end   = my_calendar_add_date( $orig_end, $i * $every, 0, 0 );

						$data = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'daily' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
					}
					break;
				case 'E':
					// This doesn't work for weekdays unless the period is less than one week, as it doesn't account for day repetitions.
					// Need to set up two nested loops to ID the number of days forward for x week days.
					// Every = $every = e.g. every 14 weekdays.
					// Num forward = $numforward = e.g. 7 times.
					if ( $every < 7 ) {
						for ( $i = 0; $i <= $numforward; $i ++ ) {
							$begin = my_calendar_add_date( $orig_begin, $i * $every, 0, 0 );
							$end   = my_calendar_add_date( $orig_end, $i * $every, 0, 0 );
							if ( 0 !== (int) ( mc_date( 'w', $begin, false ) && 6 !== (int) mc_date( 'w', $begin, false ) ) ) {
								$data = array(
									'occur_event_id' => $id,
									'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
									'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
									'occur_group_id' => $group_id,
								);
								if ( 'test' === $test && $i > 0 ) {
									return $data;
								}
								$return[] = $data;
								if ( ! $test ) {
									$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'daily' );
									if ( ! $insert ) {
										$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
										$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
										$wpdb->insert( my_calendar_event_table(), $data, $format );
									}
								}
							} else {
								$numforward ++;
							}
						}
					} else {
						// Get number of weeks included in data.
						for ( $i = 0; $i <= $event_repetition; $i ++ ) {
							$begin = strtotime( $orig_begin . ' ' . ( $every * $i ) . ' weekdays' );
							$end   = strtotime( $orig_end . ' ' . ( $every * $i ) . ' weekdays' );
							$data  = array(
								'occur_event_id' => $id,
								'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
								'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
								'occur_group_id' => $group_id,
							);
							if ( 'test' === $test && $i > 0 ) {
								return $data;
							}
							$return[] = $data;
							if ( ! $test ) {
								$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'daily' );
								if ( ! $insert ) {
									$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
									$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
									$wpdb->insert( my_calendar_event_table(), $data, $format );
								}
							}
						}
					}
					break;
				case 'W':
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$begin = my_calendar_add_date( $orig_begin, ( $i * 7 ) * $every, 0, 0 );
						$end   = my_calendar_add_date( $orig_end, ( $i * 7 ) * $every, 0, 0 );
						$data  = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'weekly' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
					}
					break;
				case 'B':
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$begin = my_calendar_add_date( $orig_begin, ( $i * 14 ), 0, 0 );
						$end   = my_calendar_add_date( $orig_end, ( $i * 14 ), 0, 0 );
						$data  = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'biweekly' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
					}
					break;
				case 'M':
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$begin = my_calendar_add_date( $orig_begin, 0, $i * $every, 0 );
						$end   = my_calendar_add_date( $orig_end, 0, $i * $every, 0 );
						$data  = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'monthly' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
					}
					break;
				case 'U':
					// Important to keep track of which date variables are strings and which are timestamps.
					$week_of_event = mc_week_of_month( mc_date( 'd', strtotime( $event->event_begin ), false ) );
					$newbegin      = my_calendar_add_date( $orig_begin, 28, 0, 0 );
					$newend        = my_calendar_add_date( $orig_end, 28, 0, 0 );
					$fifth_week    = $event->event_fifth_week;
					$data          = array(
						'occur_event_id' => $id,
						'occur_begin'    => mc_date( 'Y-m-d  H:i:s', strtotime( $orig_begin ), false ),
						'occur_end'      => mc_date( 'Y-m-d  H:i:s', strtotime( $orig_end ), false ),
						'occur_group_id' => $group_id,
					);

					if ( ! $test ) {
						$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'month-by-day' );
						if ( ! $insert ) {
							$data   = apply_filters( 'mc_instance_data', $data, strtotime( $orig_begin ), $instances );
							$format = apply_filters( 'mc_instance_format', $format, strtotime( $orig_begin ), $instances );
							$wpdb->insert( my_calendar_event_table(), $data, $format );
						}
					}
					$numforward = ( $numforward - 1 );
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$next_week_diff = ( mc_date( 'm', $newbegin, false ) === mc_date( 'm', my_calendar_add_date( mc_date( 'Y-m-d', $newbegin, false ), 7, 0, 0 ) ) ) ? false : true;
						$move_event     = ( ( 1 === (int) $fifth_week ) && ( ( mc_week_of_month( mc_date( 'd', $newbegin ), false ) + 1 ) === (int) $week_of_event ) && true === $next_week_diff ) ? true : false;
						if ( mc_week_of_month( mc_date( 'd', $newbegin, false ) ) === $week_of_event || true === $move_event ) {
						} else {
							$newbegin   = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newbegin, false ), 7, 0, 0 );
							$newend     = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newend, false ), 7, 0, 0 );
							$move_event = ( 1 === (int) $fifth_week && mc_week_of_month( mc_date( 'd', $newbegin ), false ) + 1 === (int) $week_of_event ) ? true : false;
							if ( mc_week_of_month( mc_date( 'd', $newbegin, false ) ) === $week_of_event || true === $move_event ) {
							} else {
								$newbegin = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newbegin, false ), 14, 0, 0 );
								$newend   = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newend, false ), 14, 0, 0 );
							}
						}
						$data = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $newbegin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $newend, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'month-by-day' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $newbegin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $newbegin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
						$newbegin = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newbegin, false ), 28, 0, 0 );
						$newend   = my_calendar_add_date( mc_date( 'Y-m-d  H:i:s', $newend, false ), 28, 0, 0 );
					}
					break;
				case 'Y':
					for ( $i = 0; $i <= $numforward; $i ++ ) {
						$begin = my_calendar_add_date( $orig_begin, 0, 0, $i * $every );
						$end   = my_calendar_add_date( $orig_end, 0, 0, $i * $every );
						$data  = array(
							'occur_event_id' => $id,
							'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
							'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
							'occur_group_id' => $group_id,
						);
						if ( 'test' === $test && $i > 0 ) {
							return $data;
						}
						$return[] = $data;
						if ( ! $test ) {
							$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'annual' );
							if ( ! $insert ) {
								$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
								$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
								$wpdb->insert( my_calendar_event_table(), $data, $format );
							}
						}
					}
					break;
			}
		}
	} else {
		$begin = strtotime( $orig_begin );
		$end   = strtotime( $orig_end );
		$data  = array(
			'occur_event_id' => $id,
			'occur_begin'    => mc_date( 'Y-m-d  H:i:s', $begin, false ),
			'occur_end'      => mc_date( 'Y-m-d  H:i:s', $end, false ),
			'occur_group_id' => $group_id,
		);
		if ( ! $test ) {
			$insert = apply_filters( 'mc_insert_recurring', false, $data, $format, $id, 'single' );
			if ( ! $insert ) {
				$data   = apply_filters( 'mc_instance_data', $data, $begin, $instances );
				$format = apply_filters( 'mc_instance_format', $format, $begin, $instances );
				$wpdb->insert( my_calendar_event_table(), $data, $format );
			}
		}
	}

	if ( true === $test ) {
		return $return;
	}

	return $data;
}

/**
 * Check for events with known occurrence overlap problems.
 */
function mc_list_problems() {
	$events   = get_posts(
		array(
			'post_type'  => 'mc-events',
			'meta_key'   => '_occurrence_overlap',
			'meta_value' => 'false',
		)
	);
	$list     = array();
	$problems = array();

	if ( is_array( $events ) && count( $events ) > 0 ) {
		foreach ( $events as $event ) {
			$event_id  = get_post_meta( $event->ID, '_mc_event_id', true );
			$event_url = admin_url( 'admin.php?page=my-calendar&mode=edit&event_id=' . absint( $event_id ) );
			$list[]    = '<a href="' . esc_url( $event_url ) . '">' . esc_html( $event->post_title ) . '</a>';
		}
	}

	if ( ! empty( $list ) ) {
		$problems = array( 'Problem Events' => '<ul><li>' . implode( '</li><li>', $list ) . '</li></ul>' );
	}

	return $problems;
}
