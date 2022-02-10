<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Get Next Payout
 * Adds seconds to a given time based on the payout period set.
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_banking_addon_settings' ) ) :
	function mycred_get_banking_addon_settings( $service = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$default = array(
			'active'        => array(),
			'services'      => array(),
			'service_prefs' => array()
		);

		$option_id = 'mycred_pref_bank';
		if ( $point_type != MYCRED_DEFAULT_TYPE_KEY )
			$option_id .= '_' . $point_type;

		$settings = mycred_get_option( $option_id, $default );
		$settings = wp_parse_args( $settings, $default );

		if ( $service !== NULL && array_key_exists( $service, $settings['service_prefs'] ) )
			$settings = $settings['service_prefs'][ $service ];

		return $settings;

	}
endif;
