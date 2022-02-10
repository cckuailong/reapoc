<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Legend items
 *
 */
function wpbs_include_files_events() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include main Legend Item class
	if( file_exists( $dir_path . 'class-event.php' ) )
		include $dir_path . 'class-event.php';

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-object-db-events.php' ) )
		include $dir_path . 'class-object-db-events.php';

	if( file_exists( $dir_path . 'class-object-meta-db-events.php' ) )
		include $dir_path . 'class-object-meta-db-events.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_events' );


/**
 * Register the class that handles database queries for the Events
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_events( $classes ) {

	$classes['events']    = 'WPBS_Object_DB_Events';
	$classes['eventmeta'] = 'WPBS_Object_Meta_DB_Events';

	return $classes;

}
add_filter( 'wpbs_register_database_classes', 'wpbs_register_database_classes_events' );


/**
 * Returns an array with WPBS_Event objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_events( $args = array(), $count = false ) {

	return wp_booking_system()->db['events']->get_events( $args, $count );

}


/**
 * Gets an event from the database
 *
 * @param mixed int|object      - event id or object representing the event
 *
 * @return WPBS_Event|false
 *
 */
function wpbs_get_event( $event ) {

	return wp_booking_system()->db['events']->get_object( $event );

}


/**
 * Inserts a new event into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_event( $data ) {

	return wp_booking_system()->db['events']->insert( $data );

}

/**
 * Updates an event from the database
 *
 * @param int 	$event_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_event( $event_id, $data ) {

	return wp_booking_system()->db['events']->update( $event_id, $data );

}

/**
 * Deletes an event from the database
 *
 * @param int $event_id
 *
 * @return bool
 *
 */
function wpbs_delete_event( $event_id ) {

	return wp_booking_system()->db['events']->delete( $event_id );

}

/**
 * Inserts a new meta entry for the event
 *
 * @param int    $event_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_event_meta( $event_id, $meta_key, $meta_value, $unique = false ) {

	return wp_booking_system()->db['eventmeta']->add( $event_id, $meta_key, $meta_value, $unique );

}

/**
 * Updates a meta entry for the event
 *
 * @param int    $event_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_event_meta( $event_id, $meta_key, $meta_value, $prev_value = '' ) {

	return wp_booking_system()->db['eventmeta']->update( $event_id, $meta_key, $meta_value, $prev_value );

}

/**
 * Returns a meta entry for the event
 *
 * @param int    $event_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_event_meta( $event_id, $meta_key = '', $single = false ) {

	return wp_booking_system()->db['eventmeta']->get( $event_id, $meta_key, $single );

}

/**
 * Removes a meta entry for the event
 *
 * @param int    $event_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_event_meta( $event_id, $meta_key, $meta_value = '', $delete_all = '' ) {

	return wp_booking_system()->db['eventmeta']->delete( $event_id, $meta_key, $meta_value, $delete_all );

}