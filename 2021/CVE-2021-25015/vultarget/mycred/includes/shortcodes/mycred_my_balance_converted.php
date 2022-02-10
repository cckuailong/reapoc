<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: my_balance_converted
 * Returns the current users balance.
 * @see http://codex.mycred.me/shortcodes/mycred_my_balance_converted/
 * @since 1.8.6
 * @version 2.0
 */
if ( ! function_exists( 'mycred_render_shortcode_my_balance_converted' ) ) :
	function mycred_render_shortcode_my_balance_converted( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'ctype'   	=>	MYCRED_DEFAULT_TYPE_KEY,
			'rate'    	=>	1,
			'prefix'  	=>	'',
			'suffix' 	=>	'',
			'decimal' 	=>	1,
			'timeframe'	=>	''
		), $atts, MYCRED_SLUG . '_my_balance_converted' ) );

		$output = '';

		$timeframe_balance = '';

		// Not logged in
		if ( ! is_user_logged_in() )
			return $content;

		// Get user ID
		$user_id = mycred_get_user_id( get_current_user_id() );

		// Make sure we have a valid point type
		if ( ! mycred_point_type_exists( $ctype ) )
			$ctype = MYCRED_DEFAULT_TYPE_KEY;

		// Get the users myCRED account object
		$account = mycred_get_account( $user_id );
		if ( $account === false ) return;

		// Check for exclusion
		if ( empty( $account->balance ) || ! array_key_exists( $ctype, $account->balance ) || $account->balance[ $ctype ] === false ) return;

		if( empty( $timeframe ) )
			$balance = $account->balance[ $ctype ];
		else
			$timeframe_balance = mycred_my_bc_get_balance( $user_id, $timeframe, $ctype );

		$output = '<div class="mycred-my-balance-converted-wrapper">';

		if ( ! empty( $prefix ) )
			$output .= '<span class="mycred-my-balance-converted-prefix">'.esc_attr( $prefix ).'</span>';

		if( floatval( $rate ) == 0 ) $rate = 1;

		$converted_balance = floatval( empty( $timeframe ) ? $balance->current : $timeframe_balance ) * floatval( $rate );

		$output .= number_format( $converted_balance, intval( $decimal ), '.', '' );

		if ( ! empty( $suffix ) )
			$output .= '<span class="mycred-my-balance-converted-suffix">'.esc_attr( $suffix ).'</span>';

		$output .= '</div>';

		return $output;

	}
endif;
add_shortcode( MYCRED_SLUG . '_my_balance_converted', 'mycred_render_shortcode_my_balance_converted' );

if( !function_exists( 'mycred_my_bc_get_balance' ) ):
function mycred_my_bc_get_balance( $user_id, $timeframe, $ctype )
{
	global $wpdb, $mycred_log_table;

	//Current Timestamp
	$timestamp = time();

	//Current day end time
	$end_day = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($timestamp)->format('Y-m-d 23:59:59'))->getTimestamp();

	$day_start = '';
	$day_end = '';
	
	
	if( $timeframe == 'today' )
	{
		$day_start = DateTime::createFromFormat( 'Y-m-d H:i:s', ( new DateTime() )->setTimestamp( $timestamp )->format( 'Y-m-d 00:00:00' ) )->getTimestamp();
	}

	if( $timeframe == 'yesterday' )
	{
		$timestamp = strtotime( '-1 day', $timestamp );
		
		$day_start = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($timestamp)->format('Y-m-d 00:00:00'))->getTimestamp();

		$end_day   = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($timestamp)->format('Y-m-d 23:59:59'))->getTimestamp();
	}

	if( $timeframe == 'this-week' )
	{
		$week_day = apply_filters( 'mycred_my_bc_last_week_day', 'sunday' );

		$day_start = strtotime( "{$week_day} last week", $timestamp );
	}

	if( $timeframe == 'this-month' )
	{
		$day_start = strtotime( "first day of this month", $timestamp );
		
		$day_start = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($day_start)->format('Y-m-d 00:00:00'))->getTimestamp();
	}

	if( $timeframe == 'last-month' )
	{
		$day_start = strtotime( "first day of -1 month", $timestamp );

		$end_day = strtotime( "last day of -1 month", $timestamp );
		
		$day_start = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($day_start)->format('Y-m-d 00:00:00'))->getTimestamp();

		$end_day = DateTime::createFromFormat('Y-m-d H:i:s', (new DateTime())->setTimestamp($end_day)->format('Y-m-d 23:59:59'))->getTimestamp();
	}

	

	$balance = $wpdb->get_var( 
		$wpdb->prepare(
			"SELECT SUM(creds) 
			FROM {$mycred_log_table} 
			WHERE `user_id` = %d 
			AND `ctype` = %s 
			AND `time` BETWEEN %d AND %d", 
			$user_id, 
			$ctype,
			$day_start,
			$end_day
		)
	);

	return $balance;
}
endif;