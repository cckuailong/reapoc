<?php 
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Dates Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//  Dates      /////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


/**
	 * Get SQL for inserttion Dates into DB
 * 
 * @param type $dates_in_diff_formats
 * @param type $is_approved_dates
 * @param type $booking_id
 * @return string - sql
 */
function wpbc_get_insert_sql_for_dates( $dates_in_diff_formats , $is_approved_dates, $booking_id , $is_return_only_array = false) {

	//FixIn: 7.2.1.9
	$is_return_dates = false;
	// Hook for overwriting dates saving into databse
	$is_return_dates = apply_filters( 'wpbc_get_insert_sql_for_dates', $is_return_dates, $dates_in_diff_formats , $is_approved_dates, $booking_id, $is_return_only_array );	
	if ( false !== $is_return_dates )
		return $is_return_dates;
	
    $my_dates = $dates_in_diff_formats['array'];
    
    //if ( WP_BK_LAST_CHECKOUT_DAY_AVAILABLE )
//FixIn: 8.2.1.15
//	if ( get_bk_option( 'booking_last_checkout_day_available' ) === 'On' )                                              //FixIn: 8.1.3.28
//        if ( count($my_dates)>1 ) { unset( $my_dates[ ( count($my_dates)-1 ) ] ); }   // Remove LAST selected day in calendar //FixIn: 6.2.3.6
    
    $start_time = $dates_in_diff_formats['start_time'];
    $end_time = $dates_in_diff_formats['end_time'];

    $i=0;
    $insert = '';
    $insert_arr = array();
    $my_date_previos = '';
    $my_dates4emeil = '';
    foreach ($my_dates as $my_date) {
        $i++;          // Loop through all dates
        $my_date= str_replace('-','.',$my_date);

        if (strpos($my_date,'.')!==false) {

            if ( get_bk_option( 'booking_recurrent_time' ) !== 'On') {
                    $my_date = explode('.',$my_date);
                    if ($i == 1) {
                        if ( (! isset($start_time[0])) || (empty($start_time[0])) ) $start_time[0] = '00';
                        if ( (! isset($start_time[1])) || (empty($start_time[1])) ) $start_time[1] = '00';                        
                        if ( ($start_time[0] == '00') && ($start_time[1] == '00') ) $start_time[2] = '00';

                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
                    }elseif ($i == count($my_dates)) {

                        if ( (! isset($end_time[0])) || (empty($end_time[0])) ) $end_time[0] = '00';
                        if ( (! isset($end_time[1])) || (empty($end_time[1])) ) $end_time[1] = '00';                        
                        if ( ($end_time[0] == '00') && ($end_time[1] == '00') ) $end_time[2] = '00';

                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
                    }else {
                        $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], '00', '00', '00' );
                    }
                    $my_dates4emeil .= $date . ',';
                    if ( !empty($insert) ) $insert .= ', ';
                    $insert .= "('$booking_id', '$date', '$is_approved_dates' )";
                    $insert_arr[] = array( $booking_id, $date, $is_approved_dates );
            } else {
                    if ($my_date_previos  == $my_date) continue; // escape for single day selections.

                    $my_date_previos  = $my_date;
                    $my_date = explode('.',$my_date);
                    $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
                    $my_dates4emeil .= $date . ',';
                    if ( !empty($insert) ) $insert .= ', ';
                    $insert .= "('$booking_id', '$date', '$is_approved_dates' )";
                    $insert_arr[] = array( $booking_id, $date, $is_approved_dates );

                    $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
                    $my_dates4emeil .= $date . ',';
                    if ( !empty($insert) ) $insert .= ', ';
                    $insert .= "('$booking_id', '$date', '$is_approved_dates' )";
                    $insert_arr[] = array( $booking_id, $date, $is_approved_dates );
            }

        }
    }
//    $my_dates4emeil = substr($my_dates4emeil,0,-1);
//    
//    return array($insert, $my_dates4emeil) ;
    
    
    if ( $is_return_only_array )
        return $insert_arr;
    else
        return $insert;
}


/**
	 * Get the dates in different formats
 * 
 * @param type $str_dates__dd_mm_yyyy
 * @param type $booking_type
 * @param type $booking_form_data
 * @return 
            array(
                      'string' => "30.02.2014, 31.02.2014, 01.03.2014" 
                    , 'array'  => array("2014-02-30", "2014-02-31", ....
                    , 'start_time'  => array('00','00','00');
                    , 'end_time'    => array('00','00','00');
                );
 */
function wpbc_get_dates_in_diff_formats( $str_dates__dd_mm_yyyy, $booking_type, $booking_form_data ){

    $str_dates__dd_mm_yyyy = str_replace( '|', ',', $str_dates__dd_mm_yyyy);    // Check  this for some old versions of plugin

    if ( strpos($str_dates__dd_mm_yyyy,' - ') !== false ) {                     // Recheck for any type of Range Days Formats
        $arr_check_in_out_dates = explode(' - ', $str_dates__dd_mm_yyyy );
        $str_dates__dd_mm_yyyy = wpbc_get_comma_seprated_dates_from_to_day( $arr_check_in_out_dates[0], $arr_check_in_out_dates[1] );
    }    


    $days_array = explode(',', $str_dates__dd_mm_yyyy);                         // Create dates Array
    $only_days  = array();

    foreach ($days_array as $new_day) {

        if ( ! empty($new_day) ) {

            $new_day = trim( $new_day );       

            $new_day = str_replace( '-', '.', $new_day);

            $new_day = explode( '.', $new_day);

            $only_days[] = sprintf( "%04d-%02d-%02d", intval( $new_day[2] ), intval( $new_day[1] ), intval( $new_day[0] ) );            
        }
    }
    sort($only_days);                                                           // Sort Dates


    // Get Times from booking form if these fields exist
    $start_end_time = wpbc_get_times_in_form( $booking_form_data, $booking_type );

	if ( $start_end_time !== false ) {
		$start_time = $start_end_time[0];                                       // array('00','00','01');
		$end_time   = $start_end_time[1];                                       // array('00','00','01');

		if ( ( '00' == $start_time[0] ) && ( '00' == $start_time[1] ) ) {       //FixIn: 8.7.8.8
			$start_time = array( '00', '00', '00' );
			$end_time   = array( '00', '00', '00' );
		} else {
			if ( count( $only_days ) == 1 ) {         // add end date if selected 1 day only and times is exist
				$only_days[] = $only_days[0];
			}
		}
	} else {
		$start_time = array( '00', '00', '00' );
		$end_time   = array( '00', '00', '00' );
	}


    return array(
                    'string' => $str_dates__dd_mm_yyyy                          // dd_mm_yyyy
                  , 'array'  => $only_days  
                  , 'start_time'  => $start_time
                  , 'end_time'    => $end_time
    );
}


/**
 * Check  for minimum  and maximum  available times,  and restrict value to  these limits.
 *
 * @param string $time          24:00
 * @param string $min_time      00:01
 * @param string $max_time      23:59
 *
 * @return string               23:59
 */
function wpbc_check_min_max_available_times( $time = '00:00', $min_time = '00:00', $max_time = '23:59' ) {

	// Time in minutes
	$time_m = explode( ':', trim( $time ) );
	$time_m = intval( $time_m[0] ) * 60 + intval( $time_m[1] );

	// Min time in minutes
	$min_time_m = explode( ':', trim( $min_time ) );
	$min_time_m = intval( $min_time_m[0] ) * 60 + intval( $min_time_m[1] );

	// Max time in minutes
	$max_time_m = explode( ':', trim( $max_time ) );
	$max_time_m = intval( $max_time_m[0] ) * 60 + intval( $max_time_m[1] );


	if ( $time_m < $min_time_m ) {
		$time_m = $min_time_m;
	}

	if ( $time_m > $max_time_m ) {
		$time_m = $max_time_m;
	}

	// Convert time in minutes back  to  string HH:MM
	$time_m_h = floor( $time_m / 60 );
	$time_m_m = $time_m - $time_m_h * 60;

	// Check leading 0
	if ( $time_m_h < 10 ) {
		$time_m_h = '0' . $time_m_h;
	}
	if ( $time_m_m < 10 ) {
		$time_m_m = '0' . $time_m_m;
	}

	return $time_m_h . ':' . $time_m_m;
}


/**
	 * Get Times from booking Form, if these times fields exist
 * 
 * @param type $booking_form_data
 * @param type $booking_type
 * @return mixed
                    array ( array('00','00','01'), array('00','00','01') )
                      ||
                    false
 */
function wpbc_get_times_in_form( $booking_form_data, $booking_type ){

    $is_time_exist = false;

if ( false )  //2018-04-21 - if use times in paid so comment it                                 //FixIn: 8.0.1.4        //FixIn:  TimeFree 2    //UnComment it for  Free version
        if ( ! class_exists('wpdev_bk_biz_s') )
            return $is_time_exist;

    $start_time = $end_time = '00:00:00';


    if ( strpos( $booking_form_data, 'rangetime' . $booking_type ) !== false ) {   

        // ~checkbox^mymultiple4^~checkbox^rangetime4^ ~checkbox^rangetime4^12:00 - 13:00~ checkbox^rangetime4^~checkbox^rangetime4^~text^name4^Jonny~ 

        // Types of the conditions
        $f_type =  '[^\^]*';     
        $f_name =  'rangetime[\d]*[\[\]]{0,2}';     
        $f_value =  '[\s]*([0-9:]*)[\s]*\-[\s]*([0-9:]*)[\s]*[^~]*';     

        $pattern_to_search='%[~]?'.$f_type.'\^'.$f_name.'\^'.$f_value.'[~]?%';

        preg_match_all($pattern_to_search, $booking_form_data, $matches, PREG_SET_ORDER);
        /* Exmaple of $matches:

         Array (  [0] => Array (
                        [0] => ~checkbox^rangetime4^13:00 - 14:00~
                        [1] => 13:00
                        [2] => 14:00
                                ) )
        */   

        if (count($matches)>0){

                $start_time = wpbc_get_time_in_24_hours_format( trim($matches[0][1]) );
                $start_time[2]='01';

                $end_time   = wpbc_get_time_in_24_hours_format( trim($matches[0][2]) );
                $end_time[2]='02';

                $is_time_exist = true; 

        } else {
            $start_time = array('00','00','01');
            $end_time   = array('00','00','02');
        }

    } else {

        if ( strpos($booking_form_data,'starttime' . $booking_type ) !== false ) {      // Get START TIME From form request
            $pos1 = strpos($booking_form_data,'starttime' . $booking_type );            // Find start time pos
            $pos1 = strpos($booking_form_data,'^',$pos1)+1;                             // Find TIME pos
            $pos2 = strpos($booking_form_data,'~',$pos1);                               // Find TIME length
            if ($pos2 === false) $pos2 = strlen($booking_form_data);
            $pos2 = $pos2-$pos1;
            $start_time = substr( $booking_form_data, $pos1,$pos2)  ;
            if ($start_time == '') $start_time = '00:00';

            $start_time = wpbc_check_min_max_available_times( $start_time, '00:01', '23:59' );                          //FixIn: 8.7.11.1

            $start_time = explode(':',$start_time);

			$start_time[2]='01';
        } else 
            $start_time = explode(':',$start_time);

        if ( strpos($booking_form_data,'endtime' . $booking_type ) !== false ) {    // Get END TIME From form request
            $pos1 = strpos($booking_form_data,'endtime' . $booking_type );          // Find start time pos
            $pos1 = strpos($booking_form_data,'^',$pos1)+1;                         // Find TIME pos
            $pos2 = strpos($booking_form_data,'~',$pos1);                           // Find TIME length
            if ($pos2 === false) $pos2 = strlen($booking_form_data);
            $pos2 = $pos2-$pos1;
	        $end_time = substr( $booking_form_data, $pos1, $pos2 );
	        if ( $end_time == '' ) {
		        $end_time = '00:00';
	        }

	        $end_time = wpbc_check_min_max_available_times( $end_time, '00:01', '23:59' );                              //FixIn: 8.7.11.1

            $is_time_exist = true; 

            $end_time = explode(':',$end_time);
            $end_time[2]='02';
        } else {
	        $end_time = explode( ':', $end_time );
        }

        if ( strpos($booking_form_data,'durationtime' . $booking_type ) !== false ) {   // Get END TIME From form request
            $pos1 = strpos($booking_form_data,'durationtime' . $booking_type );         // Find start time pos
            $pos1 = strpos($booking_form_data,'^',$pos1)+1;                             // Find TIME pos
            $pos2 = strpos($booking_form_data,'~',$pos1);                               // Find TIME length
            if ($pos2 === false) $pos2 = strlen($booking_form_data);
            $pos2 = $pos2-$pos1;
            $end_time = substr( $booking_form_data, $pos1,$pos2)  ;

            $is_time_exist = true; 

            $end_time = explode(':',$end_time);

            // Here we are get start time and add duration for end time
            $new_end_time = mktime(intval($start_time[0]), intval($start_time[1]));
            $new_end_time = $new_end_time + $end_time[0]*60*60 + $end_time[1]*60;
            $end_time = date('H:i',$new_end_time);
            if ($end_time == '00:00') $end_time = '23:59';
            $end_time = explode(':',$end_time);
            $end_time[2]='02';
        }

    }

    if ( $is_time_exist )
        return array( $start_time, $end_time );
    else 
        return  false;
}    

 
/**
 * Change Dates format from  SQL Dates to predefined settings  Date/Time format
 * 
 * @param string $dates_in_sql_format - '2015-02-29 00:00:00, 2015-02-30 00:00:00'
 * @return string - Formated Dates to show for visitor
 */
function wpbc_change_dates_format( $dates_in_sql_format ) {
    
    if ( empty( $dates_in_sql_format ) ) 
        return '';
    
    $dates_array_in_sql_format = explode( ',', $dates_in_sql_format );

    $mydates_result = '';
    $date_format = get_bk_option( 'booking_date_format');
    $time_format = get_bk_option( 'booking_time_format');
    
    if ( $time_format !== false  )  $time_format = ' ' . $time_format;
    else                            $time_format = '';

    //FixIn: TimeFreeGenerator //FixIn: 8.2.1.26 - only for Booking Calendar Free version  show times in AM/PM fomrat  or other depend from  time format  at the WordPress > Settings > General  page.
	if ( empty( $time_format ) ) {
		$time_format = ' ' . get_option( 'time_format' );				// Get  WordPress default
	}

    if ( $date_format == '' )       $date_format = "d.m.Y";

    foreach ( $dates_array_in_sql_format as $dt ) {
        $dt = trim( $dt );
        $dta = explode(' ',$dt);
        $tms = $dta[1];
        $tms = explode(':' , $tms);
        $dta = $dta[0];
        $dta = explode( '-', $dta );

        $date_format_now = $date_format . $time_format;
        
        if ( $tms == array( '00','00','00' ) ) $date_format_now = $date_format;

        //   H        M        S        M        D        Y
        //$mydates_result .= date_i18n( $date_format_now, mktime( $tms[0], $tms[1], $tms[2], $dta[1], $dta[2], $dta[0] ) ) . ', ';

        //FixIn: 8.7.3.9    - fix issue of Daylight Saving Time - in some systems after ~ 29 of March, system generate minus several hours which  show incorrect  selected dates
	    $booking_is_use_localized_time_format = get_bk_option( 'booking_is_use_localized_time_format' );                //FixIn: 8.7.4.1
	    if ( ( 'On' == $booking_is_use_localized_time_format ) && ( function_exists( 'wp_date' ) ) ) {
	        $mydates_result .= date_i18n( $date_format_now, strtotime(  $dt   ) ) . ', ';           //FixIn: 8.8.2.2
	    } else {
	    	$mydates_result .= date( $date_format_now, strtotime(  $dt   ) ) . ', ';
	    }

    }

    return substr( $mydates_result, 0, -2 );
}

																														//FixIn: TimeFreeGenerator
/**
 * Convert timeslot "10:00 - 12:00" to  specfic timeformat,  like "10:00 AM - 12:00 PM"
 * @param string $timeslot 		- "10:00 - 12:00"
 * @param string $time_format	= "g:i A"
 */
function wpbc_time_slot_in_format( $timeslot, $time_format = false ){

	if ( empty( $time_format ) ) {
		$time_format = get_bk_option( 'booking_time_format' );    	// get  from  Booking Calendar
		if ( empty( $time_format ) ) {
			$time_format = get_option( 'time_format' );				// Get  WordPress default
		}
	}

	$value_times = explode( '-', $timeslot );
	$value_times[0] = trim( $value_times[0] );
	$value_times[1] = trim( $value_times[1] );

	$s_tm = explode( ':', $value_times[0] );
	$e_tm = explode( ':', $value_times[1] );

//	$s_tm = date_i18n( $time_format, mktime( intval( $s_tm[0] ), intval( $s_tm[1] ) ) );
//	$e_tm = date_i18n( $time_format, mktime( intval( $e_tm[0] ), intval( $e_tm[1] ) ) );
	//FixIn: 8.7.3.9    - fix issue of Daylight Saving Time - in some systems after ~ 29 of March, system generate minus several hours which  show incorrect  selected dates
    $booking_is_use_localized_time_format = get_bk_option( 'booking_is_use_localized_time_format' );                //FixIn: 8.7.4.1
    if ( ( 'On' == $booking_is_use_localized_time_format ) && ( function_exists( 'wp_date' ) ) ) {
		$s_tm = date_i18n( $time_format, mktime( intval( $s_tm[0] ), intval( $s_tm[1] ) ) );                        //FixIn: 8.8.2.2
		$e_tm = date_i18n( $time_format, mktime( intval( $e_tm[0] ), intval( $e_tm[1] ) ) );
	} else{
		$s_tm = date( $time_format, mktime( intval( $s_tm[0] ), intval( $s_tm[1] ) ) );
		$e_tm = date( $time_format, mktime( intval( $e_tm[0] ), intval( $e_tm[1] ) ) );
	}

	$t_delimeter = ' - ';

	return $s_tm . $t_delimeter . $e_tm ;
}


//FixIn: 8.4.2.11
/**
 * Convert timeslot "10:00" to  specfic timeformat,  like "10:00 AM"
 * @param string $timeslot 		- "10:00"
 * @param string $time_format	= "g:i A"
 */
function wpbc_time_in_format( $timeslot, $time_format = false ){

	if ( empty( $time_format ) ) {
		$time_format = get_bk_option( 'booking_time_format' );    	// get  from  Booking Calendar
		if ( empty( $time_format ) ) {
			$time_format = get_option( 'time_format' );				// Get  WordPress default
		}
	}

	$timeslot = trim( $timeslot );
	$s_tm = explode( ':', $timeslot );

	$s_tm = date_i18n( $time_format, mktime( intval( $s_tm[0] ), intval( $s_tm[1] ) ) );

	return $s_tm;
}


/**
	 * Get dates from DB of specific booking
 * 
 * @global type $wpdb
 * @param type $booking_id_str - booking ID
 * @return string - comma separated dates in SQL format
 */
function wpbc_get_str_sql_dates_in_booking( $booking_id_str ) {
    
    global $wpdb;
    
    $dates_result = $wpdb->get_results( "SELECT DISTINCT booking_date FROM {$wpdb->prefix}bookingdates WHERE booking_id IN ({$booking_id_str}) ORDER BY booking_date" );
            
    $dates_str = array();
    
    foreach ( $dates_result as $my_date ) {

        $dates_str[] = $my_date->booking_date;   
    }
    $dates_str = implode( ', ', $dates_str );
    
    if ( 0 ) {                                                                  // Add one additional day,  to  set  it as check-out day. Useful for some configuration
        $my_dates4emeil = explode(',', $dates_str );
        $my_dates4emeil = array_map('trim', $my_dates4emeil);
        $last_selected_date = $my_dates4emeil[ count($my_dates4emeil) - 1 ];
        $last_selected_date = explode(' ', $last_selected_date );
        $last_selected_date_time = $last_selected_date[1];
        $my_dates4emeil[ count($my_dates4emeil) - 1 ] = $last_selected_date[0] . ' 00:00:00';
        $my_dates4emeil[] = date( 'Y-m-d', wpbc_get_tommorow_day( $my_dates4emeil[ count($my_dates4emeil) - 1 ] ) ) . ' ' . $last_selected_date_time;               
        $dates_str = implode(', ', $my_dates4emeil );
    }

    return $dates_str;
}


/**
	 * Get Time in 24 hours (military) format,  from  possible AM/PM format
 * 
 * @param string $time_str  - '01:20 PM'
 * @return string           - '13:20'
 */
function wpbc_get_time_in_24_hours_format( $time_str ) {

    $time_str = trim( $time_str );
    $time_str_plus = 0;

    if ( strpos( strtolower( $time_str) ,'am' ) !== false ) {
        $time_str = str_replace('am', '',  $time_str );
        $time_str = str_replace('AM', '',  $time_str );
    }

    if ( strpos( strtolower( $time_str) ,'pm' ) !== false ) {
        $time_str = str_replace('pm', '',  $time_str );
        $time_str = str_replace('PM', '',  $time_str );
        $time_str_plus = 12;
    }

    $time_str = explode( ':', trim( $time_str ) );

    $time_str[0] = $time_str[0] + $time_str_plus;
    $time_str[1] = $time_str[1] + 0;

    if ($time_str[0] < 10 ) $time_str[0] = '0' . $time_str[0];
    if ($time_str[1] < 10 ) $time_str[1] = '0' . $time_str[1];

    return $time_str;
}


/**
	 * Get number of days between 2 dates (dates in mySQL format)
 * 
 * @param string $day1 - Day  in MySQL format
 * @param string $day2 - Day  in MySQL format
 * @return int - number of days
 */
function wpbc_get_difference_in_days( $day1, $day2 ) {
    return floor( ( strtotime( $day1 ) - strtotime( $day2 ) ) / 86400 );        //FixIn: 8.2.1.11
}


/**
	 * Get Sorted Days array in SQL format
 * 
 * @param string $booking_days - comma separated dates: 06.04.2015, 05.04.2015, 07.04.2015, 08.04.2015, 26.03.2015, 09.04.2015, 27.03.2015
 * @return array - sorted dates array
 */
function wpbc_get_sorted_days_array( $booking_days ) {

    if ( strpos($booking_days,' - ') !== false ) {
        $booking_days = explode(' - ', $booking_days );
        $booking_days = wpbc_get_comma_seprated_dates_from_to_day($booking_days[0],$booking_days[1]);
    }

    $days_array = explode(',', $booking_days);
    $only_days  = array();

    foreach ($days_array as $new_day) {
        if ( ! empty( $new_day ) ) {
            $new_day = trim( $new_day );
            if ( strpos( $new_day, '.' ) !== false ) $new_day = explode('.',$new_day);
            else                                     $new_day = explode('-',$new_day);
            $only_days[] = $new_day[2] .'-' . $new_day[1] .'-' . $new_day[0] . ' 00:00:00';
        }
    }
    
    if ( ! empty( $only_days ) ) {
        sort($only_days);
    }
    
    return $only_days;
}


/**
	 * Get Dates in Comma seperated format, based on start and end dates.
 * 
 * @param string $date_str_from - start date: 06.04.2015
 * @param string $date_str_to   - end date:   08.04.2015 
 * @return string               - comma seperated dates: 06.04.2015, 07.04.2015, 08.04.2015
 */
function wpbc_get_comma_seprated_dates_from_to_day( $date_str_from, $date_str_to ) {
    
    $date_str_from = explode('.', $date_str_from);
    $date_str_to   = explode('.', $date_str_to);
    $iDateFrom = mktime( 1, 0, 0, ($date_str_from[1]+0), ($date_str_from[0]+0), ($date_str_from[2]+0));
    $iDateTo   = mktime( 1, 0, 0, ($date_str_to[1] + 0), ($date_str_to[0] + 0), ($date_str_to[2] + 0));
    
    $aryRange=array();
    
    if ( $iDateTo >= $iDateFrom ) {
        array_push( $aryRange, date( 'd.m.Y', $iDateFrom ) );                   // first entry

        while ($iDateFrom<$iDateTo) {
            $iDateFrom+=86400;                                                  // add 24 hours
            array_push( $aryRange, date( 'd.m.Y', $iDateFrom ) );
        }
    }
    
    $aryRange = implode(', ', $aryRange);
    
    return $aryRange;
}


/**
	 * Get dates array based on start and end dates.
 * 
 * @param string $sStartDate - start date: 2015-04-06
 * @param string $sEndDate   - end date:   2015-04-08 
 * @return array             - array( 2015-04-06, 2015-04-07, 2015-04-08 )   
 */
function wpbc_get_dates_array_from_start_end_days( $sStartDate, $sEndDate ){
    // Firstly, format the provided dates.
    // This function works best with YYYY-MM-DD
    // but other date formats will work thanks
    // to strtotime().
    $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
    $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));

    // Start the variable off with the start date 
    $aDays[] = $sStartDate;

    // Set a 'temp' variable, sCurrentDate, with
    // the start date - before beginning the loop
    $sCurrentDate = $sStartDate;

    // While the current date is less than the end date
    while($sCurrentDate < $sEndDate){
        // Add a day to the current date
        $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));

        // Add this new day to the aDays array
        $aDays[] = $sCurrentDate;
    }
    // Once the loop has finished, return the
    // array of days.
    return $aDays;
}


/**
	 * Check if nowday is tommorow from previosday
 * 
 * @param string $nowday        : 2015-02-29
 * @param string $previosday    : 2015-02-30
 * @return boolean              : true | false
 */
function wpbc_is_next_day( $nowday, $previosday ) {

    if ( empty( $previosday ) ) 
        return false;                                                           // have empty  date
    
    $nowday_d  = date( 'm.d.Y',  mysql2date( 'U', $nowday ) );
    $prior_day = date( 'm.d.Y',  mysql2date( 'U', $previosday ) );
    
    if ( $prior_day == $nowday_d )    
        return true;                                                            // its same dates

    $previos_array = date( 'm.d.Y', mysql2date( 'U', $previosday ) ) ;
    $previos_array = explode( '.', $previos_array );    
                                                                                // We are adding 1 day to our $prior_day
    $prior_day = date( 'm.d.Y', mktime( 0, 0, 0, $previos_array[0], ($previos_array[1]+1), $previos_array[2] ) );   

                                                                                // Now checking $prior_day and $nowday_d
    if ( $prior_day == $nowday_d ) return true; 
    else                           return false;
}


/**
	 * Get tommorow day from  input value
 * 
 * @param string $nowday    : 2015-02-29
 * @return int              : Unix timestamp for a date like this 2015-02-30
 */
function wpbc_get_tommorow_day( $nowday ){

    $nowday_d = date( 'm.d.Y', mysql2date( 'U', $nowday ) );
    $previos_array = explode( '.', $nowday_d );
    $tommorow_day = mktime( 0, 0, 0, $previos_array[0], ( $previos_array[1] + 1 ), $previos_array[2] ) ;
    return $tommorow_day;
}


/**
	 * Check if this date is today day
 * 
 * @param string $some_day  : '2015-05-29'
 * @return boolean          : true | false
 */
function wpbc_is_today_date( $some_day ) {

    $some_day_d = date( 'm.d.Y',  mysql2date( 'U', $some_day ) );
    $today_day =  date( 'm.d.Y' );
    
    if ( $today_day == $some_day_d ) return true; 
    else                             return false; 
}


//FixIn: 8.8.1.2
/**
 * Check if this date in past
 *
 * @param string $some_day  : '2015-05-29'
 * @return boolean          : true | false
 */
function wpbc_is_date_in_past( $some_day ) {

	$some_day_d = date( 'm.d.Y', mysql2date( 'U', $some_day ) );
	$some_array = explode( '.', $some_day_d );
	$some_day   = mktime( 0, 0, 0, $some_array[0], ( $some_array[1] + 1 ), $some_array[2] );

	$today_day = time();

	if ( $today_day > $some_day ) {
		return true;
	} else {
		return false;
	}
}


/**
	 * Get days in short format view
 * 
 * @param string $days        Dates: 15.05.2015, 16.05.2015, 17.05.2015
 * @return string           Dates in format: 15.05.2015 - 17.05.2015
 */
function wpbc_get_dates_short_format( $days ) {                                 // $days - string with comma seperated dates

    if (empty($days)) return '';

    $days = explode(',', $days);

    $previosday = false;
    $result_string = '';
    $last_show_day = '';

    foreach ($days as $day) {
        $is_fin_at_end = false;
        if ($previosday !== false) {                                            // Not first day
            if ( wpbc_is_next_day($day, $previosday) ) {
                $previosday = $day;                                             // Set previos day for next loop
                $is_fin_at_end = true;
            } else {
                if ($last_show_day !== $previosday) {                           // check if previos day was show or no
                    $result_string .= ' - ' . wpbc_change_dates_format($previosday);    // assign in needed format this day
                }
                $result_string .= ', ' . wpbc_change_dates_format($day);        // assign in needed format this day
                $previosday = $day;                                             // Set previos day for next loop
                $last_show_day = $day;
            }
        } else {                                                                // First day
            $result_string = wpbc_change_dates_format($day);                    // assign in needed format first day
            $last_show_day = $day;
            $previosday = $day;                                                 // Set previos day for next loop
        }
    }

    if ($is_fin_at_end) {
        $result_string .= ' - ' . wpbc_change_dates_format($day);
    }  

    return $result_string;
}



/**
	 * Change date / time format
 * 
 * @param string $dt        - MySQL Date - '2015-11-21 00:00:00'
 * @param type $date_format - Optional. Date format
 * @param type $time_format - Optional. Time format
 * @return array( 'DATE in custom Format', 'TIME in custom Format' )
 */
function wpbc_get_date_in_correct_format( $dt, $date_format = false, $time_format = false ) {

    if ( $date_format === false )   $date_format = get_bk_option( 'booking_date_format');
    if ( empty( $date_format ) )    $date_format = "m / d / Y, D";
    
    if ( $time_format === false )   $time_format = get_bk_option( 'booking_time_format');        
    if ( empty( $time_format ) )    $time_format = get_option( 'time_format' );       //'h:i a';                        //FixIn:  TimeFree 2    -  in Booking Calendar Free version  show by  default times hints in AM/PM format
    
    $my_time = date( 'H:i:s' , mysql2date( 'U', $dt ) );    
    if ( $my_time == '00:00:00' )   $time_format = '';
    
    $bk_date = date_i18n( $date_format, mysql2date( 'U', $dt ) );
    $bk_time = date_i18n( ' ' . $time_format  , mysql2date( 'U', $dt ) );
    
    if ( $bk_time == ' ' ) $bk_time = '';

    return array($bk_date, $bk_time);
}


/**
	 * Get SHORT Dates showing data
 * 
 * @param array $bk_dates_short - Array  of dates
 * @param bool $is_approved     - is dates approved or not
 * @param type $bk_dates_short_id
 * @param type $booking_types
 * @return string
 */    
function wpbc_get_short_dates_formated_to_show( $bk_dates_short, $is_approved = false, $bk_dates_short_id = array() , $booking_types = array() ){
    
    $short_dates_content = '';
    $dcnt = 0;
    foreach ( $bk_dates_short as $dt ) {
        if ( $dt == '-' ) {
            $short_dates_content .= '<span class="date_tire"> - </span>';
        } elseif ( $dt == ',' ) {
            $short_dates_content .= '<span class="date_tire">, </span>';
        } else {
            $short_dates_content .= '<a href="javascript:void(0)" class="field-booking-date label flex-label ';
            if ( $is_approved )
                $short_dates_content .= ' approved';
            $short_dates_content .= '">';

            $bk_date = wpbc_get_date_in_correct_format( $dt );
            $short_dates_content .= $bk_date[0];
            $short_dates_content .= '<sup class="field-booking-time">' . $bk_date[1] . '</sup>';
            
            if ( class_exists( 'wpdev_bk_biz_l' ) ) {                           // BL
                if (  ( !empty( $bk_dates_short_id[$dcnt] ) ) && ( isset( $booking_types[$bk_dates_short_id[$dcnt]] ) )   ){
                    $bk_booking_type_name_date = $booking_types[$bk_dates_short_id[$dcnt]]->title;        // Default
                    
                    if ( strlen( $bk_booking_type_name_date ) > 19 )
                        $bk_booking_type_name_date = substr( $bk_booking_type_name_date, 0, 13 ) 
                                                    . '...' 
                                                    . substr( $bk_booking_type_name_date, -3 );

                    $short_dates_content .= '<sup class="field-booking-time date_from_dif_type"> ' . $bk_booking_type_name_date . '</sup>';
                }
            }
            $short_dates_content .= '</a>';
        }
        $dcnt++;
    }
    return $short_dates_content;    
}

/**
	 * Get Wide Dates showing data
 * 
 * @param array $bk_dates       - Array of dates Objects
 * @param bool $is_approved     - is dates approved or not
 * @param type $booking_types   - array of booking resources objects
 * @return string
 */    
function wpbc_get_wide_dates_formated_to_show( $bk_dates, $is_approved = false, $booking_types = array() ){

    // Get WIDE Dates showing data /////////////////////////////////////
    $wide_dates_content = '';
    $dates_count = count( $bk_dates );
    $dcnt = 0;
    foreach ( $bk_dates as $dt ) {
        $dcnt++;
        $wide_dates_content .= '<a href="javascript:void(0)" class="field-booking-date label ';
        if ( $is_approved )
            $wide_dates_content .= ' approved';
        $wide_dates_content .= ' ">';

        $bk_date = wpbc_get_date_in_correct_format( $dt->booking_date );
        $wide_dates_content .= $bk_date[0];
        $wide_dates_content .= '<sup class="field-booking-time">' . $bk_date[1] . '</sup>';
        // BL
        if ( class_exists( 'wpdev_bk_biz_l' ) ) {
            if ( ( $dt->type_id != '' ) && ( isset( $booking_types[$dt->type_id] ) ) ) {
                $bk_booking_type_name_date = $booking_types[$dt->type_id]->title;        // Default
                if ( strlen( $bk_booking_type_name_date ) > 19 )
                    $bk_booking_type_name_date = substr( $bk_booking_type_name_date, 0, 13 ) . '...' . substr( $bk_booking_type_name_date, -3 );
                $wide_dates_content .= '<sup class="field-booking-time date_from_dif_type"> ' . $bk_booking_type_name_date . '</sup>';
            }
        }
        $wide_dates_content .= '</a>';
        if ( $dcnt < $dates_count ) {
            $wide_dates_content .= '<span class="date_tire">, </span>';
        }
    }


    return $wide_dates_content;
}