<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Create new bookings functions
 * @category Bookings
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2014.04.23
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


//  CAPTCHA CHECKING   //////////////////////////////////////////////////////////////////////////////////////
function wpbc_check_CAPTCHA( $the_answer_from_respondent, $prefix, $bktype ) {
        
    if (! ( ($the_answer_from_respondent == '') && ($prefix == '') ) ) {
        $captcha_instance = new wpdevReallySimpleCaptcha();
        $correct = $captcha_instance->check($prefix, $the_answer_from_respondent);

        if (! $correct) {
            $word = $captcha_instance->generate_random_word();
            $prefix = mt_rand();
            $captcha_instance->generate_image($prefix, $word);

            $filename = $prefix . '.png';
            $captcha_url = WPBC_PLUGIN_URL . '/js/captcha/tmp/' .$filename;
            $ref = substr($filename, 0, strrpos($filename, '.'));
            ?> <script type="text/javascript">
                document.getElementById('captcha_input<?php echo $bktype; ?>').value = '';
                document.getElementById('captcha_img<?php echo $bktype; ?>').src = '<?php echo $captcha_url; ?>';
                document.getElementById('wpdev_captcha_challenge_<?php echo $bktype; ?>').value = '<?php echo $ref; ?>';
                document.getElementById('captcha_msg<?php echo $bktype; ?>').innerHTML = '<span class="alert alert-warning" style="padding: 5px 5px 4px;vertical-align: middle;text-align:center;margin:5px;"><?php echo __('The code you entered is incorrect' ,'booking'); ?></span>';
                document.getElementById('submiting<?php echo $bktype; ?>').innerHTML ='';
                jQuery('#captcha_input<?php echo $bktype; ?>')
                  .fadeOut( 350 ).fadeIn( 300 )
                  .fadeOut( 350 ).fadeIn( 400 )
                  .animate( {opacity: 1}, 4000 );  
                jQuery("span.wpdev-help-message span.alert.alert-warning")
                  .fadeIn( 1 )
                  //.css( {'color' : 'red'} )
                  .animate( {opacity: 1}, 10000 )
                  .fadeOut( 2000 );   // hide message
				jQuery( '#captcha_input<?php echo $bktype; ?>' ).trigger( 'focus' );    		//FixIn: 8.7.11.12
                jQuery( '#booking_form_div<?php echo $bktype; ?> input[type=button]').prop("disabled", false);
                jQuery( '#booking_form_div<?php echo $bktype; ?> button' ).prop( "disabled", false );		//FixIn: 8.6.1.8
            </script> <?php
            return false;
        }
    }//////////////////////////////////////////////////////////////////////////////////////////////////////////
    return true;
}

// Customization  for the integration  of Mail Chimp Subscription.
function wpbc_integrate_MailChimp($formdata , $bktype) {

return false;   // Exit                                                         // Comment this line,  if you need to  use MailChimp integration

    // Start Mail Chimp Customization
    $booking_form_show = get_form_content ( $formdata, $bktype );
    
    if ( ( isset ($booking_form_show['subscribe_me'] )) && ( $booking_form_show['subscribe_me'] == 'yes') ) {   
                                                                                // In booking form at the Booking > Settings > Fields page you need to have this: <p>[checkbox subscribe_me ""] Subscribe Me</p>

        if (file_exists(WPBC_PLUGIN_DIR. '/core/lib/MailChimp.php')) {      // Include MailChimp class (You can download (API v2) !!! from  here https://github.com/drewm/mailchimp-api/tree/api-v2
            
            require_once( WPBC_PLUGIN_DIR. '/core/lib/MailChimp.php' );     // Additioannly in this file (some servers) require to comment  line #3. Like this:     // namespace Drewm;    
            $MailChimp = new MailChimp('key-my');                               // You are need to specify here YOUR KEY !!!!
            $list_id = '3344044af8';                                            // Specify List ID here 

            $result = $MailChimp->call('lists/subscribe', array(
                                        'id'                => $list_id, //'id' . $booking_id ,          
                                        'email'             => array('email'=>$booking_form_show['email']),
                                        'merge_vars'        => array('FNAME'=>$booking_form_show['name'], 'LNAME'=>$booking_form_show['secondname']),
                                        'double_optin'      => false,
                                        'update_existing'   => true,
                                        'replace_interests' => false,
                                        'send_welcome'      => false,
                                    ));
//debuge($result);   
        }
    } // End Mail Chimp Customization
}


// Create new booking and make actions on HTML page
function wpdev_bk_insert_new_booking() {

    $is_edit_booking = false;
    
    if ( isset($_POST['my_booking_hash']) && (! empty($_POST['my_booking_hash']) ) ) {
        
        $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_POST['my_booking_hash'] );
        if ($my_booking_id_type !== false) {
            $is_edit_booking = array();
            $is_edit_booking['booking_id'] = $my_booking_id_type[0];
            $is_edit_booking['booking_type'] = $my_booking_id_type[1];
            $bktype = intval( $is_edit_booking['booking_type'] );
            
            //FixIn: 6.1.1.9   
            // Check situation when  we have editing "child booking resource",  so  need to  reupdate calendar and form  to have it for parent resource.
            if  (  ( function_exists( 'wpbc_is_this_child_resource') ) && ( wpbc_is_this_child_resource( $bktype ) )  ){
                $bk_parent_br_id = wpbc_get_parent_resource( $bktype );        
            
                $is_edit_booking['booking_type'] = $bk_parent_br_id;
                $bktype = $bk_parent_br_id; 
            }
            // End: 6.1.1.9   
            
        }
        
    } else {
        $bktype = intval( $_POST[ "bktype" ] ); 
    }


    if ( $bktype <= 0 ) {
        ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error of saving data into DB. Unknown booking resource.',__FILE__,__LINE__); ?></div>'; }</script> <?php
        die('Error of saving data into DB. Unknown booking resource.');        
    }
    

    //  CAPTCHA CHECKING  
    if ( isset($_POST['captcha_user_input']) && isset($_POST['captcha_chalange']) )    
        if (! wpbc_check_CAPTCHA( $_POST['captcha_user_input'], $_POST['captcha_chalange'], $bktype ) ) 
            die;

    $admin_uri = ltrim( str_replace( get_site_url( null, '', 'admin' ), '', admin_url('admin.php?') ), '/' ) ;    
    if ( $is_edit_booking !== false ) 
        if ( strpos($_SERVER['HTTP_REFERER'], $admin_uri ) !==false ) {
            ?> <script type="text/javascript">
                if ( jQuery('#ajax_working' ).length ) {
                  document.getElementById('ajax_working').innerHTML =
                    '<div class="updated ajax_message" id="ajax_message">\n\
                        <div style="float:left;"><?php echo __('Updating...' ,'booking'); ?></div> \n\
                        <div class="wpbc_spin_loader">\n\
                               <img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+wpdev_bk_plugin_url+'/assets/img/ajax-loader.gif">\n\
                        </div>\n\
                    </div>'; 
               }
            </script> <?php
        }

	$_POST["skip_page_checking_for_updating"] = 0;
	$_POST["is_show_payment_form"]            = 1;

	if ( isset( $_POST["is_send_emeils"] ) ) {								//FixIn: 8.7.11.8
		$is_send_emeils = $_POST["is_send_emeils"];
	} else {
		$is_send_emeils = 1;
	}

    $result_bk_id = wpbc_add_new_booking( $_POST , $is_edit_booking  );
    
    if ( $result_bk_id !== false ) {
        ?> <script type="text/javascript"> <?php

        $admin_uri = ltrim( str_replace( get_site_url( null, '', 'admin' ), '', admin_url('admin.php?') ), '/' ) ;    
        
        if ( $is_edit_booking !== false ) {

            if ( strpos($_SERVER['HTTP_REFERER'], $admin_uri ) === false ) { 

                ?> setReservedSelectedDates('<?php echo $bktype; ?>'); <?php

            }  else { ?>
                var my_message = '<?php echo html_entity_decode( esc_js( __('Updated successfully' ,'booking') ),ENT_QUOTES) ; ?>';
                wpbc_admin_show_message( my_message, 'success', 3000 );                                                                                                                                                               
                if ( jQuery('#wpdev_http_referer').length > 0 ) {
                       location.href=jQuery('#wpdev_http_referer').val();
                } else location.href='<?php echo wpbc_get_bookings_url() ;?>&view_mode=vm_listing&tab=actions&wh_booking_id=<?php echo  $is_edit_booking['booking_id'] ; ?>';
                <?php             
            } 

        } else {

        	make_bk_action('check_multiuser_params_for_client_side',  $bktype );        // Activate working with specific user in WP MU

        	// if (  ( defined( 'WP_BK_AUTO_APPROVE_IF_ADD_IN_ADMIN_PANEL' ) ) && ( WP_BK_AUTO_APPROVE_IF_ADD_IN_ADMIN_PANEL )  ){
        	if ( get_bk_option( 'booking_auto_approve_bookings_if_added_in_admin_panel' ) == 'On' ) {					//FixIn: 8.1.3.27
				if ( strpos($_SERVER['HTTP_REFERER'], $admin_uri ) !== false ) {
					global $wpdb;
					$is_approve_or_pending = "1";
					$appr_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}bookingdates SET approved = %s WHERE booking_id IN ({$result_bk_id})", $is_approve_or_pending );
					if ( false === $wpdb->query( $appr_sql ) ){
						?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during updating BD - Dates',__FILE__,__LINE__); ?></div>'; }</script> <?php
						die('Error during updating BD - Dates');
					}

					do_action( 'wpbc_booking_approved' , $result_bk_id , $is_approve_or_pending );                      //FixIn: 8.7.6.1

					if ( ! empty( $is_send_emeils ) ) {                                                                 //FixIn: 8.7.11.8
						wpbc_send_email_approved( $result_bk_id, 1, "" );
					}
				}
            }

			//if (  ( defined( 'WP_BK_AUTO_SEND_PAY_REQUEST_IF_ADD_IN_ADMIN_PANEL' ) ) && ( WP_BK_AUTO_SEND_PAY_REQUEST_IF_ADD_IN_ADMIN_PANEL )  ){
			if ( ( get_bk_option( 'booking_payment_request_auto_send_in_bap' ) == 'On' ) && ( ! empty( $is_send_emeils ) ) ) {						 //FixIn: 8.1.3.24		//FixIn: 8.7.11.8
				if ( strpos($_SERVER['HTTP_REFERER'], $admin_uri ) !== false ) {
					if ( function_exists( 'wpbc_send_email_payment_request' ) ) {
						$formdata = escape_any_xss( $_POST[ "form" ] );
						$payment_reason = '';
						$is_send = wpbc_send_email_payment_request( $result_bk_id , $bktype , $formdata , $payment_reason );
					}
				}
			}

			make_bk_action('finish_check_multiuser_params_for_client_side', $bktype );  // Deactivate working with  specific user in WP MU

            ?> setReservedSelectedDates('<?php echo $bktype; ?>'); <?php
        }
        ?> </script> <?php
    }
    
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// A D D     N e w     B o o k i n g
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//$params = array(
//    ["bktype"] => 4
//    ["dates"] => 24.09.2014, 25.09.2014, 26.09.2014
//    ["form"] => select-one^rangetime4^14:00 - 16:00~text^name4^Costa~text^secondname4^Rika~email^email4^rika@cost.com~text^phone4^2423432~text^address4^Ferrari~text^city4^Rome~text^postcode4^2343~select-one^country4^IT~select-one^visitors4^1~select-one^children4^0~textarea^details4^dhfjksdhfkdhjs~checkbox^term_and_condition4[]^I Accept term and conditions
//    ["is_send_emeils"] => 1
//    ["booking_form_type"] => 
//          [wpdev_active_locale] => en_US
//
//          // Paramters for adding booking in the HTML:
//          ["skip_page_checking_for_updating"] = 0;
//          ["is_show_payment_form"] = 1;
//  ) 
//    
// Update Booking params:
//   $is_edit_booking = array(
//      'booking_id' => 10
//    , 'booking_type' => 1    
//   )         
//
function wpbc_add_new_booking( $params , $is_edit_booking = false ){  

	$is_duplicate_booking = false; //FixIn: 8.4.2.9

    if ( $is_edit_booking !== false ) { // Edit booking
        
        $booking_id = $is_edit_booking['booking_id'];
        $bktype     = $is_edit_booking['booking_type'];

    } else {                            // New booking 
        if (! isset($params[ "bktype" ]))
            return false;                                                       // Error: Unknown booking resources 
        else
            $bktype = intval( $params[ "bktype" ] ); 
        if ( $bktype == 0 ) 
            return false;                                                       // Error: Unknown booking resources 
    }

    make_bk_action('check_multiuser_params_for_client_side',  $bktype );        // Activate working with specific user in WP MU
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Define init variables
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    global $wpdb;
    
    $formdata = escape_any_xss( $params[ "form" ] );

    //FixIn: 8.4.2.9
	if ( $is_edit_booking ) {

		$formdata_array       = explode( '~', $formdata );
		$formdata_array_count = count( $formdata_array );
		for ( $i = 0; $i < $formdata_array_count; $i ++ ) {
			$elemnts = explode( '^', $formdata_array[ $i ] );
			if ( ( 'wpbc_other_action' == $elemnts[1] ) && ( 'duplicate_booking' == $elemnts[2] ) ) {
				$is_duplicate_booking = true;
				break;
			}
		}
	}
    $my_modification_date = "'" . date_i18n( 'Y-m-d H:i:s'  ) ."'" ;            // Localize booking modification date 
       
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Get Dates
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $dates_in_diff_formats = wpbc_get_dates_in_diff_formats( $params[ "dates" ], $bktype, $formdata  );


    // SET LAST CHECK OUT DAY AS AVAILABLE  - REMOVE IT
	if ( get_bk_option( 'booking_last_checkout_day_available' ) === 'On' ){                                             //FixIn: 8.2.1.15
		 if ( ! empty( $dates_in_diff_formats['array'] ) ) {
			if ( count( $dates_in_diff_formats['array'] ) > 1 ) {
			  unset( $dates_in_diff_formats['array'][ ( count( $dates_in_diff_formats['array'] )-1 ) ] );    				// Remove LAST selected day in calendar //FixIn: 6.2.3.6
			}
		 }
		$only_days  = array();
		foreach ( $dates_in_diff_formats[ "array" ] as $new_day) {

			if ( ! empty($new_day) ) {
				$new_day = explode( '-', $new_day);

				$only_days[] = sprintf( "%02d.%02d.%04d", intval( $new_day[2] ), intval( $new_day[1] ), intval( $new_day[0] ) );
			}
		}
		$only_days = implode(',', $only_days );
		$dates_in_diff_formats = wpbc_get_dates_in_diff_formats( $only_days, $bktype, $formdata  );
    }



//debuge($dates_in_diff_formats);

    $str_dates__dd_mm_yyyy = $dates_in_diff_formats['string'];
    // $my_dates   = $dates_in_diff_formats['array'];
    $start_time = $dates_in_diff_formats['start_time'];
    $end_time   = $dates_in_diff_formats['end_time'];

    
    $exclude_bookings = array();                                                //FixIn: 7.0.1.36
    if ( $is_edit_booking !== false ) {
        $exclude_bookings[] = $is_edit_booking['booking_id'];
    }
    
    //Here we need to check for double booking for the same sessions
    if ( // ( ! $is_edit_booking ) &&                                           //FixIn: 7.0.1.36
         (! wpbc_check_if_dates_free( $bktype, $formdata ,$dates_in_diff_formats, $start_time, $end_time , $exclude_bookings ) )    //FixIn: 7.0.1.36
       ) { 
		if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;											//FixIn: 7.2.1.7
		die( 'Dates unavailable' );
	}
    
    
    $my_check_in_date = explode( '-', $dates_in_diff_formats['array'][0] );
    $my_check_in_date_sql = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_check_in_date[0], $my_check_in_date[1], $my_check_in_date[2], $start_time[0], $start_time[1], $start_time[2] );
    
    
    if ( empty( $str_dates__dd_mm_yyyy ) ){
        ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) { document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error! No Dates',__FILE__,__LINE__); ?></div>'; } </script> <?php
		if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;											//FixIn: 7.2.1.7
        die('Error! No Dates');
    }

    
    $auto_approve_new_bookings_is_active = trim( get_bk_option( 'booking_auto_approve_new_bookings_is_active' ) );
    $is_approved_dates = ( $auto_approve_new_bookings_is_active == 'On' ) ? '1' : '0';

	//FixIn: 8.5.2.27
	// Auto approve only for specific booking resources
	$booking_resources_to_approve = array();
	$booking_resources_to_approve = apply_filters( 'wpbc_get_booking_resources_arr_to_auto_approve', $booking_resources_to_approve );
	if ( in_array( $bktype, $booking_resources_to_approve ) ) {
		$is_approved_dates = 1;
	}
	/**
	 * How to use "Auto approve bookings only for specific booking resources" ?
	 * Add code similar  to this in your functions.php file in your theme,  or in some other php file:
	 *
			function my_wpbc_get_booking_resources_arr_to_auto_approve( $resources_to_approve ) {
				$resources_to_approve = array( 9, 12, 33 );		// Array  of booking resources ID,  which  you need to auto  approve
				return $resources_to_approve;
			}
	 		add_filter( 'wpbc_get_booking_resources_arr_to_auto_approve', 'my_wpbc_get_booking_resources_arr_to_auto_approve' );
	 */


//    // Auto  approve if number of visitors < 10
//    $booking_form_show = get_form_content ( $formdata, $bktype );
//	if (  ( ! empty( $booking_form_show['_all_fields_']['visitors'] ) ) && ( intval( $booking_form_show['_all_fields_']['visitors'] ) < 10 ) ) { $is_approved_dates = 1; }

    $additional_fields = $additional_fields_vlaues = '';
    if ( isset( $params["sync_gid"] ) ) {
       $additional_fields = ", sync_gid" ;
       $additional_fields_vlaues = ", '" . wpbc_clean_parameter($params["sync_gid"]) . "'" ;        
    }

	//FixIn: 8.4.2.9
    if ( ( $is_edit_booking === false )
    	|| ( true === $is_duplicate_booking )
	) {
        
        ////////////////////////////////////////////////////////////////////////////
        // Add new booking
        ////////////////////////////////////////////////////////////////////////////
        $sql_insertion = "INSERT INTO {$wpdb->prefix}booking (form, booking_type, modification_date, sort_date{$additional_fields}) VALUES ('{$formdata}', {$bktype}, {$my_modification_date}, '{$my_check_in_date_sql}' {$additional_fields_vlaues})" ;

        if ( false === $wpdb->query( $sql_insertion ) ){
            ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during inserting into DB',__FILE__,__LINE__); ?></div>'; }</script> <?php
            if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;										//FixIn: 7.2.1.7	
			die('Error during inserting into DB');
        }
        $booking_id = (int) $wpdb->insert_id;                                       // Get ID of booking

    } else {
        
        ////////////////////////////////////////////////////////////////////////////
        // Edit booking
        ////////////////////////////////////////////////////////////////////////////
        $update_sql = "UPDATE {$wpdb->prefix}booking AS bk SET bk.form='{$formdata}', bk.booking_type={$bktype}, bk.modification_date={$my_modification_date}, sort_date='{$my_check_in_date_sql}' WHERE bk.booking_id={$booking_id};";
        if ( false === $wpdb->query( $update_sql  ) ){
            ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during updating exist booking in DB',__FILE__,__LINE__); ?></div>'; }</script> <?php
            if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;										//FixIn: 7.2.1.7
			die('Error during updating exist booking in DB');
        }

        // Check if dates already aproved or no
        $slct_sql = "SELECT approved FROM {$wpdb->prefix}bookingdates WHERE booking_id IN ({$booking_id}) LIMIT 0,1";
        $slct_sql_results  = $wpdb->get_results( $slct_sql );
        if ( count($slct_sql_results) > 0 ) {
            $is_approved_dates = $slct_sql_results[0]->approved;
        }
//$is_approved_dates = '0';
        $delete_sql = "DELETE FROM {$wpdb->prefix}bookingdates WHERE booking_id IN ({$booking_id})";
        if ( false === $wpdb->query( $delete_sql  ) ){
            ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during updating exist booking for deleting dates in DB' ,__FILE__,__LINE__); ?></div>'; }</script> <?php
            if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;										//FixIn: 7.2.1.7	
			die('Error during updating exist booking for deleting dates in DB');
        }        
    }
    
   
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    // Update the Hash and Cost  of the booking 
    make_bk_action('wpbc_update_booking_hash', $booking_id, $bktype );

    //FixIn: 8.6.1.24
    $is_update_cost_after_editing = get_bk_option( 'booking_payment_update_cost_after_edit_in_bap' );

	if ( ( $is_edit_booking === false )
	     || ( 'On' == $is_update_cost_after_editing ) ) {
    	//FixIn: 8.5.2.1	-	do not updare cost of booking, while editing this booking.
    	make_bk_action('wpdev_booking_post_inserted', $booking_id, $bktype, $str_dates__dd_mm_yyyy,  array($start_time, $end_time ) , $formdata );
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     
    $insert = wpbc_get_insert_sql_for_dates( $dates_in_diff_formats , $is_approved_dates, $booking_id ); 
//debuge('$insert',$insert);
    if ( !empty($insert) )
        if ( false === $wpdb->query( "INSERT INTO {$wpdb->prefix}bookingdates (booking_id, booking_date, approved) VALUES " . $insert ) ){
            ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during inserting into BD - Dates',__FILE__,__LINE__); ?></div>'; }</script> <?php
            if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;										//FixIn: 7.2.1.7
			die('Error during inserting into BD - Dates');
        }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
    if ( isset( $params["is_send_emeils"] ) ) $is_send_emeils = $params["is_send_emeils"];
    else                                      $is_send_emeils = 1; 
    

	// Here checking if we are using the [cost_corrections] shortcode in the booking form, and then
	// we need to  re-update shortcodes [corrected_total_cost], [corrected_deposit_cost],  [corrected_balance_cost] for using in New booking email
	//FixIn: 8.8.3.12
	$fin_cost_corrections_sum = apply_bk_filter( 'check_if_cost_exist_in_field', false, $formdata, $bktype );
	if ( false !== $fin_cost_corrections_sum ) {
		$booking_cost_array = apply_bk_filter( 'wpbc_get_cost_of_new_booking', $booking_id, $bktype, $str_dates__dd_mm_yyyy, array( $start_time, $end_time ), $formdata );
		/**
		return array(
					'total_cost' 	=> 100
				  , 'deposit_cost' 	=> 15
				  , 'wp_nonce' 				=> $wp_nonce
				  , 'is_deposit' 			=> true
				  , 'additional_calendars' 	=> []
			);
		 */
		$cur_sym = wpbc_get_currency_symbol();

		$booking_cost_array['balance_cost'] = floatval($booking_cost_array['total_cost']) - floatval($booking_cost_array['deposit_cost']);

		// Shortcode [corrected_total_cost]
		$cost_text = $booking_cost_array['total_cost'] ;
		$cost_text = number_format( floatval( $cost_text ), wpbc_get_cost_decimals(), '.', '' );
		$cost_text = strip_tags( wpbc_cost_show( $cost_text, array(  'currency' => 'CURRENCY_SYMBOL' ) ) );
		$cost_text = str_replace( array( 'CURRENCY_SYMBOL', '&' ), array( $cur_sym, '&amp;' ), $cost_text );
		$formdata .= "~text^corrected_total_cost$bktype^" . $cost_text;

		// Shortcode [corrected_deposit_cost]
		$cost_text = $booking_cost_array['deposit_cost'] ;
		$cost_text = number_format( floatval( $cost_text ), wpbc_get_cost_decimals(), '.', '' );
		$cost_text = strip_tags( wpbc_cost_show( $cost_text, array(  'currency' => 'CURRENCY_SYMBOL' ) ) );
		$cost_text = str_replace( array( 'CURRENCY_SYMBOL', '&' ), array( $cur_sym, '&amp;' ), $cost_text );
		$formdata .= "~text^corrected_deposit_cost$bktype^" . $cost_text;

		//Shortcode [corrected_balance_cost]
		$cost_text = $booking_cost_array['balance_cost'] ;
		$cost_text = number_format( floatval( $cost_text ), wpbc_get_cost_decimals(), '.', '' );
		$cost_text = strip_tags( wpbc_cost_show( $cost_text, array(  'currency' => 'CURRENCY_SYMBOL' ) ) );
		$cost_text = str_replace( array( 'CURRENCY_SYMBOL', '&' ), array( $cur_sym, '&amp;' ), $cost_text );
		$formdata .= "~text^corrected_balance_cost$bktype^" . $cost_text;

		// Update new form data for having it in approved emails.
		$update_sql = "UPDATE {$wpdb->prefix}booking AS bk SET bk.form='{$formdata}' WHERE bk.booking_id={$booking_id};";
		if ( false === $wpdb->query( $update_sql  ) ){
			?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during updating exist booking in DB',__FILE__,__LINE__); ?></div>'; }</script> <?php
			if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;
			die('Error during updating exist booking in DB');
		}
	}
	//FixIn: 8.8.3.12


	//FixIn: 8.4.2.9
    if ( ( $is_edit_booking === false )
    	|| ( true === $is_duplicate_booking )
	) {

	    if ( ( isset( $params["is_show_payment_form"] ) ) && ( $params["is_show_payment_form"] == 1 ) ) {
		    do_action( 'wpdev_new_booking', 			  $booking_id, $bktype, $str_dates__dd_mm_yyyy, array( $start_time, $end_time ), $formdata );
	    } else {
		    do_action( 'wpbc_update_cost_of_new_booking', $booking_id, $bktype, $str_dates__dd_mm_yyyy, array( $start_time, $end_time ), $formdata );
	    }

        if ( $is_send_emeils != 0 ) {
            // wpbc_send_email_new_REPLACED( $booking_id, $bktype, $formdata ) ;         // Old Sending emails.

            $email_content = str_replace( array("\r\n","\r","\n","\\r","\\n","\\r\\n"), "<br/>", $formdata );			//FixIn: 8.1.3.4

            wpbc_send_email_new_admin(   $booking_id, $bktype, $email_content ) ;
            wpbc_send_email_new_visitor( $booking_id, $bktype, $email_content ) ;
        }

	    // Useful hook  for Google Adwords Conversion tracking.															//FixIn: 8.5.2.25
	    do_action( 'wpbc_track_new_booking', array(
												'booking_id'  => $booking_id,
												'resource_id' => $bktype,
												'dates'       => $str_dates__dd_mm_yyyy,
												'times'       => array( $start_time, $end_time ),
												'formdata'    => $formdata
										)
		);
	    /**
	     * How to  use this hook?
		 *
		 * Add code similar  to this in your functions.php file in your theme,  or in some other php file:

									// Track  Google Adwords Conversion
									//
									// @param $params = array(
									//						'booking_id'  => $booking_id,
									//						'resource_id' => $bktype,
									//						'dates'       => $str_dates__dd_mm_yyyy,
									//						'times'       => array( $start_time, $end_time ),
									//						'formdata'    => $formdata
									//				)
									function my_booking_tracking( $params ){

										?><!-- Google Code for Booking Conversion Page -->
										  <script type="text/javascript">
											 // Insert bellow your Google Conversion Code
										  </script><?php
									}
		 							add_action( 'wpbc_track_new_booking', 'my_booking_tracking' );
	     */

        wpbc_integrate_MailChimp($formdata, $bktype);

        do_action( 'wpbc_booking_approved' , $booking_id , $is_approved_dates );                                        //FixIn: 8.7.6.1
        if ( ( $auto_approve_new_bookings_is_active == 'On') && ($is_send_emeils != 0 ) ){
            wpbc_send_email_approved($booking_id, 1);
        }

    } else { 
        
        $admin_uri = ltrim( str_replace( get_site_url( null, '', 'admin' ), '', admin_url('admin.php?') ), '/' ) ;

        $is_show_payment_form_after_editing = true;					// Default
        // if ( $is_edit_booking !== false ) {
        //	$is_show_payment_form_after_editing = ! false;            //FixIn: 8.4.6.0
		// }
        if ( strpos($_SERVER['HTTP_REFERER'], $admin_uri ) === false ) {

        	// Front End
            if ( ( isset( $params["is_show_payment_form"]) ) && ( $params["is_show_payment_form"] == 1 ) && ( $is_show_payment_form_after_editing ) )
                do_action('wpdev_new_booking',$booking_id, $bktype, $str_dates__dd_mm_yyyy, array($start_time, $end_time ) ,$formdata );
            else
                do_action('wpbc_update_cost_of_new_booking',$booking_id, $bktype, $str_dates__dd_mm_yyyy, array($start_time, $end_time ) ,$formdata );

        } else {     // Admin panel

			//FixIn: 8.6.1.24
	        if  ( ( $is_edit_booking !== false ) && ( 'On' == $is_update_cost_after_editing ) )
                do_action('wpdev_new_booking',$booking_id, $bktype, $str_dates__dd_mm_yyyy, array($start_time, $end_time ) ,$formdata );						// Update NOTES, Cost, Show Payment form,
            // else
            //    do_action('wpbc_update_cost_of_new_booking',$booking_id, $bktype, $str_dates__dd_mm_yyyy, array($start_time, $end_time ) ,$formdata );		// Update Only cost field
		}

        if ($is_send_emeils != 0 ) {
        	$email_content = str_replace( array("\r\n","\r","\n","\\r","\\n","\\r\\n"), "<br/>", $formdata );			//FixIn: 8.1.3.4
            wpbc_send_email_modified($booking_id, $bktype, $email_content  );
        }

	    // Useful hook  booking edit tracking.																			//FixIn: 8.7.11.15
	    do_action( 'wpbc_track_edit_booking', array(
												'booking_id'  => $booking_id,
												'resource_id' => $bktype,
												'dates'       => $str_dates__dd_mm_yyyy,
												'times'       => array( $start_time, $end_time ),
												'formdata'    => $formdata
										)
		);
	    /**
	     * How to  use this hook?
		 *
		 * Add code similar  to this in your functions.php file in your theme,  or in some other php file:

									// Track  Google Adwords Conversion
									//
									// @param $params = array(
									//						'booking_id'  => $booking_id,
									//						'resource_id' => $bktype,
									//						'dates'       => $str_dates__dd_mm_yyyy,
									//						'times'       => array( $start_time, $end_time ),
									//						'formdata'    => $formdata
									//				)
									function my_edit_booking_tracking( $params ){

										?><!-- Google Code for Booking Conversion Page -->
										  <script type="text/javascript">
											 // Insert bellow your Google Conversion Code
										  </script><?php
									}
		 							add_action( 'wpbc_track_edit_booking', 'my_edit_booking_tracking' );
	     */

    }    


    // Re-Update booking resource TYPE if its needed here
    if ( isset( $params["skip_page_checking_for_updating"] ) )  $skip_page_checking_for_updating = (bool) $params["skip_page_checking_for_updating"];
    else                                                        $skip_page_checking_for_updating = true;
    make_bk_action('wpdev_booking_reupdate_bk_type_to_childs', $booking_id, $bktype, $str_dates__dd_mm_yyyy,  array($start_time, $end_time ) , $formdata , $skip_page_checking_for_updating );    

    //if ( ( wpbc_is_new_booking_page( 'HTTP_REFERER' ) ) ||
    //if (   ( ( defined( 'WP_BK_AUTO_APPROVE_WHEN_ZERO_COST' ) ) && ( WP_BK_AUTO_APPROVE_WHEN_ZERO_COST ) )  ){   		// Auto  approve booking if cost = 0.
    if ( get_bk_option( 'booking_auto_approve_bookings_when_zero_cost' ) == 'On' ) {									//FixIn: 8.1.3.27
        $booking_cost = apply_bk_filter('get_booking_cost_from_db', '', $booking_id);
        if  ( ( empty( $booking_cost ) ) || ( intval( $booking_cost ) === 0 ) ) {
            $is_approve_or_pending = "1";
             $appr_sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}bookingdates SET approved = %s WHERE booking_id IN ({$booking_id})", $is_approve_or_pending );
            if ( false === $wpdb->query( $appr_sql ) ){
                ?> <script type="text/javascript"> if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {  document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '<div style=&quot;height:20px;width:100%;text-align:center;margin:15px auto;&quot;><?php debuge_error('Error during updating BD - Dates',__FILE__,__LINE__); ?></div>'; }</script> <?php
                if ( ! empty( $params[ 'return_instead_die_on_error' ] ) ) return 0;									//FixIn: 7.2.1.7
				die('Error during updating BD - Dates');
            }
            do_action( 'wpbc_booking_approved' , $booking_id , $is_approve_or_pending );                      			//FixIn: 8.7.6.1
            wpbc_send_email_approved( $booking_id, 1, "" );
        }
    }    
    make_bk_action('finish_check_multiuser_params_for_client_side', $bktype );  // Deactivate working with  specific user in WP MU
    
    return $booking_id;
}
add_bk_filter('wpbc_add_new_booking_filter' , 'wpbc_add_new_booking' );
add_bk_action('wpbc_add_new_booking' , 'wpbc_add_new_booking' ); 
/*
make_bk_action('wpbc_add_new_booking' , array(    
 'bktype' => 1
 , 'dates' => '27.08.2014, 28.08.2014, 29.08.2014'
 , 'form' => 'select-one^rangetime1^10:00 - 12:00~text^name1^Jo~text^secondname1^Smith~email^email1^smith@gmail.com~text^phone1^678676678~text^address1^Linkoln Street~text^city1^London~text^postcode1^78788~select-one^country1^GB~select-one^visitors1^1~select-one^children1^1~textarea^details1^Rooms with sea view~checkbox^term_and_condition1[]^I Accept term and conditions'
 , 'is_send_emeils' => 0
// , 'booking_form_type' => ''
// , 'wpdev_active_locale' => 'en_US'    
) ); /**/




/** 
	 * Check if dates intersect with  other dates array
 * 
 * @param array $dates_for_check                            - Dates Array of specific booking, which we checking            - date in SQL format: '2014-11-21 10:00:01'
 * @param array $dates_exist                                - Other dates from booking resource(s),  that  already  exist   - date in SQL format: '2014-11-21 15:00:02'
 * @return bool true - intersect, false - not intersect
 */
function wpbc_check_dates_intersections( $dates_for_check, $dates_exist  ) {    //FixIn: 5.4.5 
    
    $is_intersected = false;

    $booked_dates        = array(); 
    $what_dates_to_check = array();
    
//debuge($dates_for_check, $dates_exist);

    foreach ( $dates_exist as $value ) {

        if (  ( is_object( $value ) ) && ( isset( $value->booking_date ) )  ) 
            $booking_date = $value->booking_date;                               // Its object  with date value
        else 
            $booking_date = $value;                                             // Its array of string dates
                
        
        if ( intval( substr( $booking_date, -1 ) ) == 1 ) {                     // We require time shift  for situation,  when  previous booking end in the same time,  when  new booking start
            $time_shift = 10;                                                   // Plus 10  seconds
        } elseif ( intval( substr( $booking_date, -1 ) ) == 2 ) {
            $time_shift = -10;                                                  // Minus 10  seconds
        } else    
            $time_shift = 0; 
        
        // Booked dates in destination resource,  that can intersect
        $booked_dates[ $booking_date ] = strtotime( $booking_date ) + $time_shift;;

        // Get here only  dates,  without times:                                [2015-11-09] => 1447027200
        $what_dates_to_check[ substr($booking_date, 0, 10) ] = strtotime( substr($booking_date, 0, 10) );
    }            

    asort( $booked_dates );                                                     // Sort dates   
    
    
    $keyi=0;
    $dates_to_add = array();
    foreach ( $booked_dates as $date_key => $date_value ) {
        
        if ( $keyi == 0 ) {                                                     // First element
            if ( intval( substr( $date_key, -1 ) ) == 2 ) {
                // We are having first  date as ending date, its means that  starting date exist somewhere before,  and we need to  set it at the begining 
                $dates_to_add[ substr($date_key, 0, 10) . ' 00:00:11' ] = strtotime( substr($date_key, 0, 10) . ' 00:00:11' );
            }
        }                
        
        if ( $keyi == ( count($booked_dates) - 1 ) ) {                                                     // last  element
            if ( intval( substr( $date_key, -1 ) ) == 1 ) {
                // We are having last  date as ending date, its means that  ending  date exist somewhere after,  and we need to  set it at the end of array 
                $dates_to_add[ substr($date_key, 0, 10) . ' 23:59:42' ] = strtotime( substr($date_key, 0, 10) . ' 23:59:42' );
            }
        }                
        $keyi++;
    }
    $booked_dates = array_merge($booked_dates, $dates_to_add);
    asort( $booked_dates );                                                     // Sort dates       
    
    
    // Skip dates (in original booking) that does not exist in destination  resource at all
    $check_dates = array();  
    foreach ( $dates_for_check as $value ) {

        if (  ( is_object( $value ) ) && ( isset( $value->booking_date ) )  ) 
            $booking_date = $value->booking_date;                               // Its object  with date value
        else 
            $booking_date = $value;                                             // Its array of string dates
        
        // Check  dates only if these dates already  exist in $what_dates_to_check array
        if ( isset( $what_dates_to_check[ substr($booking_date, 0, 10) ] ) ) 
            $check_dates[] = $value;
    }

    if ( count( $check_dates ) == 0 ) return $is_intersected;                   // No intersected dates at all in exist bookings. Return.       //FixIn: 6.0.1.13
    
    foreach ( $check_dates as $value ) {

        if (  ( is_object( $value ) ) && ( isset( $value->booking_date ) )  ) 
            $booking_date = $value->booking_date;                               // Its object  with date value
        else
            $booking_date = $value;                                             // Its array of string dates
        
        if ( isset( $booked_dates[ $booking_date ] ) ) {                        // Already have exactly  this date as booked
            $is_intersected = true;
            break;
        }
                
        if ( intval( substr( $booking_date, -1 ) ) == 1 ) {                     // We require time shift  for situation,  when  previous booking end in the same time,  when  new booking start
            $time_shift = 10;                                                   // Plus 10  seconds
        } elseif ( intval( substr( $booking_date, -1 ) ) == 2 ) {
            $time_shift = -10;                                                  // Minus 10  seconds
        } else    
            $time_shift = 0; 
        
        $booked_dates[ $booking_date ] = strtotime( $booking_date ) + $time_shift;
    }   
    
    
    asort( $booked_dates );                                                     // Sort dates   

//debuge($booked_dates);    
    if ( ! $is_intersected ) {
        
        // check  dates and times for intersections 
        $previos_date_key = 0;
        foreach ( $booked_dates as $date_key => $value ) {
            
            $date_key = intval( substr( $date_key, -1 ) );                      // Get last second

            // Check  if the date fully booked (key = 0), or we are having 2 the same keys,  like 1 and 1 or 2 and 2 one under other. Its means that  we are having time intersection.
            if ( ( $date_key !== 0 ) && ( $date_key != $previos_date_key ) )    
                $previos_date_key = $date_key;
            else {
                $is_intersected = true;
                break;
            }
        }
    }

    return  $is_intersected ;
}




/**
 * Checking for  bookings in the same session and prevention of the double booking 
 * 
 * @param type $bktype
 * @param type $formdata
 * @param type $str_dates__dd_mm_yyyy
 * @param type $start_time
 * @param type $end_time
 * @return true|false - free or unavailable
 */
function wpbc_check_if_dates_free($bktype, $formdata ,$dates_in_diff_formats, $start_time, $end_time, $exclude_bookings = array() ) {           //FixIn: 7.0.1.36
        
    if (     ( get_bk_option( 'booking_check_on_server_if_dates_free' ) == 'Off')     // Check if this feature active or not
          || ( get_bk_option( 'booking_is_days_always_available' ) == 'On')           // Check if any days available feature is active
       ) return true;
    
    //TODO: Finish  checking for parent booking resources 
    //      We need to  get  availabaility  for the specific dates
    //      We need to rewrite 
    //      function show_availability_at_calendar 
    //      for getting availability  only  for the specific dates
    //      and then  based on the availability approve or decline this current booking.
    if ( class_exists('wpdev_bk_biz_l')) {         
        $number_of_child_resources = apply_bk_filter('wpbc_get_number_of_child_resources', $bktype );        
        if ( $number_of_child_resources > 1 )                                   // if this booking resources - parent,  then  do  not chekc it, yet!
             return true;
    }
    
    $is_days_free =  true;                  
    
    ////////////////////////////////////////////////////////////////////////////
    // Get Selected Dates Array in full format - all possible dates and times //
    ////////////////////////////////////////////////////////////////////////////
    //Example:
    //            [0] => 2014-11-21 10:00:01
    //            [1] => 2014-11-21 12:00:02
    //            [2] => 2014-11-22 10:00:01
    //            [3] => 2014-11-22 12:00:02
    $selected_dates_array = array();
    $i=0;
//debuge('$dates_in_diff_formats["array]',$dates_in_diff_formats['array'])    ;

    // if we selected only 1 day,  system  retur in this array 2 same dates. But in situation with recurenttime we need only one day
    if ( get_bk_option( 'booking_recurrent_time' ) == 'On')        
        $dates_in_diff_formats['array'] = array_unique( $dates_in_diff_formats['array'] );
    
    foreach ($dates_in_diff_formats['array'] as $my_date) {
        $i++;
        $my_date = explode('-', $my_date);
    
        
        // For start and end times we cut 10 seconds,  so we can  check for sure if this times inside of the booked times or not
        if ( get_bk_option( 'booking_recurrent_time' ) !== 'On') {

            if ($i == 1) {
                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
                $date = date('Y-m-d H:i:s', strtotime( '+20 second', strtotime( $date ) ) );
            }elseif ($i == count( $dates_in_diff_formats['array'] )) {
                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
                $date = date('Y-m-d H:i:s', strtotime( '-20 second', strtotime( $date ) ) );
            }else {
                $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], '00', '00', '00' );
            }
            $selected_dates_array[] = $date;
        } else {
            $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $start_time[0], $start_time[1], $start_time[2] );
            $date = date('Y-m-d H:i:s', strtotime( '+20 second', strtotime( $date ) ) );
            $selected_dates_array[] = $date;
            $date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $my_date[0], $my_date[1], $my_date[2], $end_time[0], $end_time[1], $end_time[2] );
            $date = date('Y-m-d H:i:s', strtotime( '-20 second', strtotime( $date ) ) );
            $selected_dates_array[] = $date;
        }    
    }
    ////////////////////////////////////////////////////////////////////////////
    

    
    // SQL condition for getting any booked dates,  which  inside of  selected dates intervals
    $dates_sql_where = '';
    foreach ($dates_in_diff_formats['array'] as $selected_date) {
        $selected_date = explode( '-', $selected_date );
        $my_check_in_date_sql  = sprintf( "%04d-%02d-%02d 00:00:00", $selected_date[0], $selected_date[1], $selected_date[2] );
        $my_check_out_date_sql = sprintf( "%04d-%02d-%02d 23:59:59", $selected_date[0], $selected_date[1], $selected_date[2] );
        
        if ( ! empty( $dates_sql_where ) )
            $dates_sql_where .= " OR ";
        $dates_sql_where .= " ( dt.booking_date >= '{$my_check_in_date_sql}' AND dt.booking_date <= '{$my_check_out_date_sql}' ) ";
    }
    if ( ! empty( $dates_sql_where ) )
        $dates_sql_where = " ({$dates_sql_where}) ";
    ////////////////////////////////////////////////////////////////////////////
        
    /*  
    // WE can not use this type of days condition  for check  in and check out dates,  because user can  select several not consecutive days      
    $my_check_in_date = explode( '-', $dates_in_diff_formats['array'][0] );
    $my_check_in_date_sql = sprintf( "%04d-%02d-%02d 00:00:00", $my_check_in_date[0], $my_check_in_date[1], $my_check_in_date[2] );
    
    $my_check_out_date = explode( '-', $dates_in_diff_formats['array'][ ( count($dates_in_diff_formats['array']) - 1 ) ] );
    $my_check_out_date_sql = sprintf( "%04d-%02d-%02d 23:59:59", $my_check_out_date[0], $my_check_out_date[1], $my_check_out_date[2] );
    
    $dates_sql_where = " ( dt.booking_date >= '{$my_check_in_date_sql}' AND dt.booking_date <= '{$my_check_out_date_sql}' ) ";
    /**/    
                
    global $wpdb;
    
    // Checking only for approved bookings,  if pending days available is active
    if ( get_bk_option( 'booking_is_show_pending_days_as_available') == 'On' ) 
        $approved_only = ' dt.approved = 1 AND ';
    else 
        $approved_only = '';
    
    $trash_bookings = ' AND bk.trash != 1 ';                                    //FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings}
 
    if ( ! empty( $exclude_bookings ) ) {                                       //FixIn: 7.0.1.36    
        $exclude_bookings = implode( ',', $exclude_bookings );
        $exclude_bookings = wpbc_clean_digit_or_csd( $exclude_bookings );
        $exclude_bookings_sql = " AND ( bk.booking_id NOT IN ( {$exclude_bookings} ) ) ";
    } else {
        $exclude_bookings_sql = '';
    }
    
    // Get all booked dates ////////////////////////////////////////////////////
    //FixIn: 7.0.1.36    
    $sql_req =  "SELECT DISTINCT dt.booking_date
        
                        FROM {$wpdb->prefix}bookingdates as dt

                        INNER JOIN {$wpdb->prefix}booking as bk

                        ON bk.booking_id = dt.booking_id

                        WHERE   {$approved_only} {$dates_sql_where} {$trash_bookings} {$exclude_bookings_sql} AND bk.booking_type IN ({$bktype})
                         
                        ORDER BY dt.booking_date" ;
                     
    $exist_dates_results = $wpdb->get_results( $sql_req );
 
    ////////////////////////////////////////////////////////////////////////////
    
    if ( count($exist_dates_results) == 0 )                                           // We do not have here booked dates at all,  so - TRUE
        return  true;

    
    //FixIn: 5.4.5
    $is_dates_intersections = wpbc_check_dates_intersections( $selected_dates_array, $exist_dates_results );
    
    if ( $is_dates_intersections ) {                                          
        // Show Warning message and return FALSE ///////////////////////////////          
     ?> <script type="text/javascript">                 
            if ( jQuery('#submiting<?php echo $bktype; ?>' ).length ) {
              // Disable spinning              
              document.getElementById('submiting<?php echo $bktype; ?>').innerHTML = '';
              // Show Error message under the calendar
              showMessageUnderElement( '#date_booking<?php echo $bktype; ?>',  
                                       '<?php echo html_entity_decode( '<strong>' . esc_js( __('Error!' ,'booking') ) . '</strong> ' 
                                               . esc_js( __('Probably these date(s) just was booking by other visitor. Please reload this page and make booking again.' ,'booking') ) ,ENT_QUOTES); ?>' , 
                                       'alert-danger');
              // Scroll to the calendar                 
              makeScroll('#calendar_booking<?php echo $bktype; ?>');              
              // Enable Submit button
              jQuery('#booking_form_div<?php echo $bktype; ?> input[type=button]').prop("disabled", false);
              jQuery( '#booking_form_div<?php echo $bktype; ?> button' ).prop( "disabled", false );		//FixIn: 8.6.1.8
           }
        </script> <?php
    
        return false;
    } //////////////////////////////////////////////////////////////////////////


    return true;
}