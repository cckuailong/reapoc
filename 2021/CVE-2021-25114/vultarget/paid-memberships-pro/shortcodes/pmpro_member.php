<?php
/*
	Shortcode to show a specific user field for current user or specified user ID.
*/
function pmpro_member_shortcode($atts, $content=null, $code='')
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_member field='last_name']
	global $current_user;

	extract(shortcode_atts(array(
		'user_id' => $current_user->ID,
		'field' => NULL
	), $atts));
	
	/*
		- pmpro_next_payment()
		- 
	*/

	//membership level fields
	$pmpro_level_fields = array(
		'membership_id',
		'membership_name',
		'membership_description',
		'membership_confirmation',
		'membership_initial_payment',
		'membership_billing_amount',
		'membership_cycle_number',
		'membership_cycle_period',
		'membership_billing_limit',
		'membership_trial_amount',
		'membership_trial_limit',
		'membership_startdate',
		'membership_enddate',
	);

	//pmpro-related fields stored in user meta
	$pmpro_user_meta_fields = array(
		'bfirstname',
		'blastname',
		'baddress1',
		'baddress2',
		'bcity',
		'bstate',
		'bzipcode',
		'bcountry',
		'bphone',
		'bemail',
		'CardType',
		'AccountNumber',
		'ExpirationMonth',
		'ExpirationYear',
	);

	//fields stored in wp_users column
	$user_column_fields = array(
		'user_login',
		'user_email',
		'user_url',
		'user_registered',
		'display_name',
	);

	//date fields
	$date_fields = array(
		'startdate',
		'enddate',
		'modified',
		'user_registered',
		'next_payment_date',
	);

	//price fields
	$price_fields = array(
		'initial_payment',
		'billing_amount',
		'trial_amount',
	);

	if($field == 'level_cost') {
		$membership_level = pmpro_getMembershipLevelForUser($user_id);
		if( !empty($membership_level ) )
			$r = pmpro_getLevelCost($membership_level, false, true);
		else
			$r = '';
	} elseif($field == 'next_payment_date') {
		//next_payment_date
		$r = pmpro_next_payment($user_id);
	} elseif(in_array( $field, $pmpro_level_fields )) {
		//membership level fields
		$field = str_replace('membership_', '', $field);
		$membership_level = pmpro_getMembershipLevelForUser($user_id);
		if(!empty($membership_level))
			$r = $membership_level->{$field};
		else
			$r = '';
	} elseif(in_array( $field, $pmpro_user_meta_fields )) {
		//pmpro-related fields stored in user meta
		$field = 'pmpro_' . $field;
		$r = get_user_meta($user_id, $field, true);
	} elseif(in_array( $field, $user_column_fields )) {
		//wp_users column
		$user = get_userdata($user_id);
		$r = $user->{$field};
	} elseif( $field == 'avatar' ) {
		// Get the user's avatar.
		$r = get_avatar( $user_id );
	} else {
		//assume user meta
		$r = get_user_meta($user_id, $field, true);
	}

	//Check for dates to reformat them.
	if(in_array( $field, $date_fields )) {
		if(empty($r) || $r == '0000-00-00 00:00:00')
			$r = '';
		elseif(is_numeric($r))
			$r = date_i18n(get_option('date_format'), $r);											//timestamp
		else
			$r = date_i18n(get_option('date_format'), strtotime($r, current_time('timestamp')));	//YYYY-MM-DD/etc format
	}

	//Check for prices to reformat them.
	if(in_array( $field, $price_fields )) {
		if(empty($r) || $r == '0.00')
			$r = '';
		else
			$r = pmpro_escape_price( pmpro_formatPrice($r) );
	}

	/** 
	 * Filter
	 */
	$r = apply_filters('pmpro_member_shortcode_field', $r, $user_id, $field);

	return $r;
}
add_shortcode('pmpro_member', 'pmpro_member_shortcode');
