<?php

/**
 * Functions to operate with meta for GiveASAP 
 */

if( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Get the meta
 * @param  integer $entry_id Entry ID
 * @param  string  $meta_key 
 * @param  boolean $single   
 * @return mixed            
 *
 * @since  2.0.0 
 */
function giveasap_get_meta( $entry_id = 0, $meta_key = '', $single = false ) {
	return get_metadata( 'giveasap', $entry_id, $meta_key, $single );
}

/**
 * Add the meta
 * @param integer $entry_id 
 * @param string  $meta_key 
 * @param string  $value  
 *
 * @since  2.0.0 
 */												
function giveasap_add_meta( $entry_id = 0, $meta_key = '', $value = '' ) {
	return add_metadata( 'giveasap', $entry_id, $meta_key, $value );
}

/**
 * Update the meta
 * @param  integer $entry_id 
 * @param  string  $meta_key 
 * @param  string  $value    
 * @return mixed            
 *
 * @since  2.0.0 
 */
function giveasap_update_meta( $entry_id = 0, $meta_key = '', $value = '' ) {
	return update_metadata( 'giveasap', $entry_id, $meta_key, $value );
}

/**
 * Delete the meta
 * @param  integer $entry_id 
 * @param  string  $meta_key 
 * @return mixed            
 *
 * @since  2.0.0 
 */
function giveasap_delete_meta( $entry_id = 0, $meta_key = '' ) {
	return delete_metadata( 'giveasap', $entry_id, $meta_key );
}