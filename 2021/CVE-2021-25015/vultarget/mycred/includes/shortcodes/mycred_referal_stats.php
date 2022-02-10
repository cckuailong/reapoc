<?php 
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_referal_stats
 * Returns the referal stats.
 * @see http://codex.mycred.me/shortcodes/mycred_referal_stats/
 * @since 2.1.1
 * @version 1.0
 */

if ( ! function_exists( 'mycred_referal_front' ) ) :	
	function mycred_referal_front( $atts ){

		extract( shortcode_atts( array(

			'ctype'    => MYCRED_DEFAULT_TYPE_KEY

		), $atts, MYCRED_SLUG . '_referal' ) );

		$hooks    = mycred_get_option( 'mycred_pref_hooks', false );

		if ( $ctype != MYCRED_DEFAULT_TYPE_KEY )
			$hooks = mycred_get_option( 'mycred_pref_hooks_' . sanitize_key( $ctype ), false );
			$active = $hooks['active'];
		if( is_array( $active) && in_array( 'affiliate' , $active )){

			$visit = $hooks['hook_prefs']['affiliate']['visit'];
			$signup = $hooks['hook_prefs']['affiliate']['signup'];
			
			$output = '';
			
			$user_id = get_current_user_id();

			$output .= '<table class="profile-fields">';
			
			// Show Visitor referral count
			if ( $visit['creds'] != 0 )
				$output .= sprintf( '<tr class="field_2 field_ref_count_visit"><td class="label">%s</td><td>%s</td></tr>', __( 'Visitors Referred', 'mycred' ), mycred_count_ref_instances( 'visitor_referral', $user_id, $ctype ) );

			// Show Signup referral count
			if ( $signup['creds'] != 0 )
				$output .= sprintf( '<tr class="field_3 field_ref_count_signup"><td class="label">%s</td><td>%s</td></tr>', __( 'Signups Referred', 'mycred' ), mycred_count_ref_instances( 'signup_referral', $user_id, $ctype ) );
				
			$output .= '</table>';
			return $output;
		}
	}
endif;

add_shortcode( MYCRED_SLUG . '_referral_stats' , 'mycred_referal_front' );
