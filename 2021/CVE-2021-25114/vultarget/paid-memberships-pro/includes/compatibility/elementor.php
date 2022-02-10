<?php

// Include custom settings to restrict Elementor widgets.
require_once( 'elementor/class-pmpro-elementor.php' );


/**
 * Elementor Compatibility
 */
function pmpro_elementor_compatibility() {
	// Remove the default the_content filter added to membership level descriptions and confirmation messages in PMPro.
	remove_filter( 'the_content', 'pmpro_level_description' );
	remove_filter( 'pmpro_level_description', 'pmpro_pmpro_level_description' );
	remove_filter( 'the_content', 'pmpro_confirmation_message' );
	remove_filter( 'pmpro_confirmation_message', 'pmpro_pmpro_confirmation_message' );
	
    // Filter members-only content later so that the builder's filters run before PMPro.
	remove_filter('the_content', 'pmpro_membership_content_filter', 5);
	add_filter('the_content', 'pmpro_membership_content_filter', 15);
}

/**
 * Get all available levels for elementor widget setting.
 * @return array Associative array of level ID and name.
 * @since 2.2.6
 */
function pmpro_elementor_get_all_levels() {

	$levels_array = get_transient( 'pmpro_elementor_levels_cache' );

	if ( empty( $levels_array ) ) {
		$all_levels = pmpro_getAllLevels( true, false );

		$levels_array = array();

		$levels_array[0] = __( 'Non-members', 'paid-memberships-pro' );
		foreach( $all_levels as $level ) {
			$levels_array[ $level->id ] = $level->name;
		}

		set_transient( 'pmpro_elementor_levels_cache', $levels_array, 1 * DAY_IN_SECONDS );
	}
	
	$levels_array = apply_filters( 'pmpro_elementor_levels_array', $levels_array );

	return $levels_array;
}
add_action( 'plugins_loaded', 'pmpro_elementor_compatibility', 15 );



function pmpro_elementor_clear_level_cache( $level_id ) {
	delete_transient( 'pmpro_elementor_levels_cache' );
}
add_action( 'pmpro_save_membership_level', 'pmpro_elementor_clear_level_cache' );
