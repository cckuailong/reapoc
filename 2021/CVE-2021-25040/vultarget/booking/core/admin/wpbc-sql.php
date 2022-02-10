<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Data Engine for Booking Listing / Calendar Overview pages
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-12-08
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
// R e q u e s t     S u p p o r t
////////////////////////////////////////////////////////////////////////////////

/**
	 * Get here default View mode saved in a General Booking Settings page or From REQUEST view_mode
 * 
 * @return string = 'vm_calendar' | 'vm_listing'
 */
function wpbc_get_default_saved_view_mode_for_wpbc_page() {
    
    if ( ! isset( $_REQUEST['view_mode'] ) )         
        $booking_default_view_mode = get_bk_option( 'booking_listing_default_view_mode' );        
    else 
        $booking_default_view_mode = $_REQUEST['view_mode'];

	if ( ! in_array( $booking_default_view_mode, array( 'vm_calendar', 'vm_listing' ) ) )
        $booking_default_view_mode = 'vm_listing';    
    
    return $booking_default_view_mode;                                          // 'vm_calendar' / 'vm_listing' ;
}


/**
	 * Get from SETTINGS (if its not set in request yet) the "tab"  & "view_mode" and set to $_REQUEST
    If we have the "saved" filter set so LOAD it and set to REQUEST, if REQUEST was not set previously
    & skip "wh_booking_type" from the saved filter set
 * 
 * @param string $filter_name - name of saved filter set. Currntly  is using only  one "Default"
 */
function wpbc_set_default_saved_params_to_request_for_booking_listing( $filter_name ) {
//debuge($_REQUEST);
    // Exclude some parameters from the saved Default parameters - the values of these parameters are loading from General Booking Settings page or from the request.
    $exclude_options_from_saved_params = array( 
                                                'tab'                           // Default
                                                , 'page'                        // From plugin
                                                , 'view_mode'                   // Default
                                                , 'wh_booking_type'             // Default
                                                , 'view_days_num'               // Default
                                                , 'blank_field__this_field_only_for_formatting_buttons'         // Skip this, this parameter for formating purpose in toolbar
                                              );         
    $wpdevbk_filter_params = array();

    // Get here default selected tab saved in a General Booking Settings page
    if ( !isset( $_REQUEST['tab'] ) ) {
        $booking_default_toolbar_tab = get_bk_option( 'booking_default_toolbar_tab' );
        if ( $booking_default_toolbar_tab !== false ) {
            $wpdevbk_filter_params['tab'] = $booking_default_toolbar_tab;       // 'filter' / 'actions' ;
            $_REQUEST['tab'] = $booking_default_toolbar_tab;                    // Set to REQUEST
        }
    }

    // Get here default View mode saved in a General Booking Settings page
    $booking_default_view_mode = wpbc_get_default_saved_view_mode_for_wpbc_page();
    if ( !isset( $_REQUEST['view_mode'] ) ) 
        $wpdevbk_filter_params['view_mode'] = $booking_default_view_mode;       // 'vm_calendar' / 'vm_listing' ;
    $_REQUEST['view_mode'] = $booking_default_view_mode;                        // Set to REQUEST
    
//    if ( !isset( $_REQUEST['view_mode'] ) ) {
//        $booking_default_view_mode = get_bk_option( 'booking_listing_default_view_mode' );
//        if ( $booking_default_view_mode !== false ) {
//            $wpdevbk_filter_params['view_mode'] = $booking_default_view_mode;   // 'vm_calendar' / 'vm_listing' ;
//            $_REQUEST['view_mode'] = $booking_default_view_mode;                // Set to REQUEST
//        } else
//            $_REQUEST['view_mode'] = 'vm_listing';
//    }

    // Get here default view_days_num
    if ( !isset( $_REQUEST['view_days_num'] ) ) {
        $booking_view_days_num = get_bk_option( 'booking_view_days_num' );
        if ( $booking_view_days_num !== false ) {
            $wpdevbk_filter_params['view_days_num'] = $booking_view_days_num;   // '30' 
            $_REQUEST['view_days_num'] = $booking_view_days_num;
        } else
            $_REQUEST['view_days_num'] = '365';
    }


    $wpdevbk_saved_filter  = get_user_option( 'booking_listing_filter_' . $filter_name ) ;
    
    if (  ($booking_default_view_mode == 'vm_listing' ) && ( $wpdevbk_saved_filter !== false )  ) { // Load Saved filter set  only at Listing page

        $wpdevbk_saved_filter = parse_url( $wpdevbk_saved_filter, PHP_URL_QUERY );                  // $wpdevbk_saved_filter = str_replace('admin.php?', '', $wpdevbk_saved_filter);
        $wpdevbk_saved_filter = explode( '&', $wpdevbk_saved_filter );

        foreach ( $wpdevbk_saved_filter as $bkfilter ) {
            $bkfilter_key_value = explode( '=', $bkfilter );                    
            if ( ! isset( $bkfilter_key_value[1] ) ) {                              //FixIn: 6.0.1.13
                $bkfilter_key_value[1] = '';
            }           
            if ( !in_array( $bkfilter_key_value[0], $exclude_options_from_saved_params ) ) {        // Exclude some parameters from the saved Default parameters - the values of these parameters are loading from General Booking Settings page or from the request.
                $wpdevbk_filter_params[$bkfilter_key_value[0]] = trim( $bkfilter_key_value[1] );
            }
        }

        // If we are do not Apply POST or custom GET, so  Saved params apply to REQUEST
        if ( (!isset( $_REQUEST['wh_approved'] )) && (!isset( $_REQUEST['scroll_day'] )) ) {        // We are do not have approved or pending value, so its mean that user open the page as default, without clicking on Filter apply.
            foreach ( $wpdevbk_filter_params as $filter_key => $filter_value ) {
                $_REQUEST[$filter_key] = $filter_value;                                             // Set to REQUEST
            }
        }
    }
}



/**
	 * Clean Request Parameters
 * 
 */
function wpbc_check_request_paramters() {                                       //FixIn:6.2.1.4
    
//debuge($_REQUEST);
    $clean_params = array();  

    $clean_params['wh_booking_id']                  = 'digit_or_csd';
	if ( ( ! empty( $_REQUEST['wh_booking_type'] ) ) && ( 'lost' == $_REQUEST['wh_booking_type'] ) ) {       //FixIn: 8.5.2.19
		$clean_params['wh_booking_type'] = 'checked_skip_it';
	} else {
		$clean_params['wh_booking_type'] =  'digit_or_csd';
	}
    $clean_params['wh_approved']                    = 'digit_or_csd';       // '0' | '1' | ''

    $clean_params['wh_booking_date']                = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_booking_date2']               = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_booking_datenext']            = 'd';                  // '1' | '2' ....
    $clean_params['wh_booking_dateprior']           = 'd';                  // '1' | '2' ....
    $clean_params['wh_booking_datefixeddates']      = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_booking_date2fixeddates']     = 'digit_or_date';      // number | date 2016-07-20

    $clean_params['wh_is_new']                      = 'd';                  // '1' | ''

    $clean_params['wh_modification_date']           = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_modification_date2']          = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_modification_dateprior']      = 'd';                  // '1' | '2' ....
    $clean_params['wh_modification_datefixeddates'] = 'digit_or_date';      // number | date 2016-07-20
    $clean_params['wh_modification_date2fixeddates']= 'digit_or_date';      // number | date 2016-07-20

    $clean_params['wh_keyword']                     = 's';                  //string

    $clean_params['wh_pay_statuscustom']            = 's';                      //string   !!! LIKE  !!!
    $clean_params['wh_pay_status']                  = array('all', 'group_ok', 'group_unknown', 'group_pending', 'group_failed');

    $clean_params['wh_cost']                        = 'd';                  // '1' | ''
    $clean_params['wh_cost2']                       = 'd';                  // '1' | ''
    
    $clean_params['or_sort']                        = array('', 'sort_date', 'booking_type', 'cost', 'booking_id_asc', 'sort_date_asc', 'booking_type_asc', 'cost_asc');
    $clean_params['wh_trash']                       = array('0' , 'trash', 'any');


    $clean_params['page_num']                       = 'd';                  // '' | '1' ...         // does not exist  in 6.2.1.4
    $clean_params['page_items_count']               = 'd';                  // '' | '1' ...         // does not exist  in 6.2.1.4
    $clean_params['view_days_num']               = 'd';                  // '' | '1' ...         // does not exist  in 6.2.1.4
//debuge($_REQUEST);
    foreach ( $clean_params as $request_key => $clean_type ) {
        
        // elements only listed in array::
        if (  is_array( $clean_type ) ) {                                       // check  only values from  the list  in this array
            
            if ( ( isset( $_REQUEST[ $request_key ] ) ) &&  ( ! in_array( $_REQUEST[ $request_key ], $clean_type ) ) )
                $clean_type = 's';    
            else 
                $clean_type = 'checked_skip_it';
        } 
        
        switch ( $clean_type ) {

            case 'checked_skip_it':

                break;

            case 'digit_or_date':                                            // digit or comma separated digit
                if ( isset( $_REQUEST[ $request_key ] ) ) 
                    $_REQUEST[ $request_key ] = wpbc_clean_digit_or_date( $_REQUEST[ $request_key ] );        // nums    

                break;

            case 'digit_or_csd':                                            // digit or comma separated digit
                if ( isset( $_REQUEST[ $request_key ] ) ) 
                    $_REQUEST[ $request_key ] = wpbc_clean_digit_or_csd( $_REQUEST[ $request_key ] );        // nums    

                break;

            case 's':                                                       // string
                if ( isset( $_REQUEST[ $request_key ] ) ) 
                    $_REQUEST[ $request_key ] = wpbc_clean_like_string_for_db( $_REQUEST[ $request_key ] );

                break;

            case 'd':                                                       // digit
                if ( isset( $_REQUEST[ $request_key ] ) ) 
                    if ( $_REQUEST[ $request_key ] !== '' )
                        $_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] );

                break;

            default:
                if ( isset( $_REQUEST[ $request_key ] ) ) {
                    $_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] );                    
                }
                break;
        }

        
    }

//debuge($_REQUEST);
}


/**
	 * Get array of cleaned (limited number) paramas from REQUEST
 * 
 * @return array
 */
function wpbc_get_clean_paramas_from_request_for_booking_listing(){
           
    $booking_num_per_page = get_bk_option( 'booking_num_per_page' );
    if ( empty( $booking_num_per_page ) ) {
        $booking_num_per_page = '10';
        update_bk_option( 'booking_num_per_page', $booking_num_per_page );
    }
    
    $args = array(
        'wh_booking_type'       => (isset( $_REQUEST['wh_booking_type'] )) ? wpbc_clean_parameter( $_REQUEST['wh_booking_type'] ) : '',
        'wh_approved'           => (isset( $_REQUEST['wh_approved'] )) ? wpbc_clean_parameter( $_REQUEST['wh_approved'] ) : '',
        'wh_booking_id'         => (isset( $_REQUEST['wh_booking_id'] )) ? wpbc_clean_parameter( $_REQUEST['wh_booking_id'] ) : '',
        'wh_is_new'             => (isset( $_REQUEST['wh_is_new'] )) ? wpbc_clean_parameter( $_REQUEST['wh_is_new'] ) : '',
        'wh_pay_status'         => (isset( $_REQUEST['wh_pay_status'] )) ? wpbc_clean_parameter( $_REQUEST['wh_pay_status'] ) : '',
        'wh_keyword'            => (isset( $_REQUEST['wh_keyword'] )) ? wpbc_clean_parameter( $_REQUEST['wh_keyword'] ) : '',
        'wh_booking_date'       => (isset( $_REQUEST['wh_booking_date'] )) ? wpbc_clean_parameter( $_REQUEST['wh_booking_date'] ) : '',
        'wh_booking_date2'      => (isset( $_REQUEST['wh_booking_date2'] )) ? wpbc_clean_parameter( $_REQUEST['wh_booking_date2'] ) : '',
        'wh_modification_date'  => (isset( $_REQUEST['wh_modification_date'] )) ? wpbc_clean_parameter( $_REQUEST['wh_modification_date'] ) : '',
        'wh_modification_date2' => (isset( $_REQUEST['wh_modification_date2'] )) ? wpbc_clean_parameter( $_REQUEST['wh_modification_date2'] ) : '',
        'wh_cost'               => (isset( $_REQUEST['wh_cost'] )) ? wpbc_clean_parameter( $_REQUEST['wh_cost'] ) : '',
        'wh_cost2'              => (isset( $_REQUEST['wh_cost2'] )) ? wpbc_clean_parameter( $_REQUEST['wh_cost2'] ) : '',
        'or_sort'               => (isset( $_REQUEST['or_sort'] )) ? wpbc_clean_parameter( $_REQUEST['or_sort'] ) : get_bk_option( 'booking_sort_order' ),
        'page_num'              => (isset( $_REQUEST['page_num'] )) ? wpbc_clean_parameter( $_REQUEST['page_num'] ) : '1',
        'wh_trash'              => (isset( $_REQUEST['wh_trash']))  ? wpbc_clean_parameter( $_REQUEST['wh_trash'] ):'',                                     //FixIn:6.1.1.10
        'page_items_count'      => (isset( $_REQUEST['page_items_count'] )) ? wpbc_clean_parameter( $_REQUEST['page_items_count'] ) : $booking_num_per_page,
    );

    return $args;
}

/**
	 * Get array of cleaned (limited number) paramas from REQUEST
 * 
 * @return array
 */
function wpbc_get_clean_paramas_from_request_for_timeline() {
  
    
        // Reset
        $start_year = date("Y");            //2012
        $start_month = date("m");           //09
        $start_day = 1;//date("d");//1;     //31
        if (! empty($_REQUEST['scroll_start_date'])) {   // scroll_start_date=2013-07-01
            $scroll_start_date= explode('-',$_REQUEST['scroll_start_date']);
            
            $start_year     = $scroll_start_date[0];            //2012
            $start_month    = $scroll_start_date[1];           //09
            $start_day      = $scroll_start_date[2];    //date("d");//1;     //31
        } 
        
        $scroll_day     = 0;
        $scroll_month   = 0;        

        if (isset($_REQUEST['view_days_num']))  $view_days_num = $_REQUEST['view_days_num'];
        else                                    $view_days_num = get_bk_option( 'booking_view_days_num');
        
        if  ((isset($_REQUEST['wh_booking_type'])) && ( strpos($_REQUEST['wh_booking_type'], ',') !== false ) )
                $is_show_resources_matrix = true;
        else    $is_show_resources_matrix = false;

        if ($is_show_resources_matrix) {
            
            switch ($view_days_num) {
                
                case '1':
                    if (empty($_REQUEST['scroll_start_date']))  $start_day = date("d");
                    
                    if (isset($_REQUEST['scroll_day'])) $scroll_day = $_REQUEST['scroll_day'];
                    
                    $real_date = mktime(0, 0, 0, $start_month,    ($start_day + $scroll_day) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-11-29';

                    $real_date = mktime(0, 0, 0, $start_month,    ($start_day + 0 +$scroll_day) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2013-12-3';                    
                    break;
                    
                case '7':
                    if (empty($_REQUEST['scroll_start_date']))  $start_day = date("d");
                    $start_week_day_num = date("w");
                    $start_day_weeek  = get_bk_option( 'booking_start_day_weeek' ); //[0]:Sun .. [6]:Sut
                    if ($start_week_day_num != $start_day_weeek) {
                        for ($d_inc = 1; $d_inc < 8; $d_inc++) {                // Just get week  back
                            $real_date = mktime(0, 0, 0, $start_month, ($start_day-$d_inc ) , $start_year);
                            $start_week_day_num = date("w", $real_date);
                            if ($start_week_day_num == $start_day_weeek) {
                                $start_day = date("d", $real_date);
                                $start_year = date("Y", $real_date);
                                $start_month = date("m", $real_date);
                                $d_inc=9;
                            }
                        }
                    }
                    
                    if (isset($_REQUEST['scroll_day'])) $scroll_day = $_REQUEST['scroll_day'];

                    $real_date = mktime(0, 0, 0, $start_month,    ( $start_day +$scroll_day) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, $start_month,    ($start_day+7+$scroll_day) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2012-12-7';                    
                    break;
                    
                case '30':
                    if (isset($_REQUEST['scroll_month'])) $scroll_month = $_REQUEST['scroll_month'];
                    
                    $real_date = mktime(0, 0, 0, ($start_month+$scroll_month),    ( $start_day ) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, ($start_month+1+$scroll_month),    ($start_day-1) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2012-12-31';                    
                    break;
                    
                case '60':
                    if (isset($_REQUEST['scroll_month'])) $scroll_month = $_REQUEST['scroll_month'];

                    $real_date = mktime(0, 0, 0, ($start_month+$scroll_month),    ( $start_day ) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, ($start_month+2+$scroll_month),   ($start_day-1) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2013-02-31';                    
                    break;
                    
////////////////////////////////////////////////////////////////////////////////
                default:  // 30 - default
                    if (isset($_REQUEST['scroll_month'])) $scroll_month = $_REQUEST['scroll_month'];                    
                    
                    $real_date = mktime(0, 0, 0, ($start_month+$scroll_month),    ( $start_day ) ,  $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, ($start_month+1+$scroll_month),    ($start_day-1) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2012-12-31';
                    break;
            }
            
        } else {   // Single resource
            
            switch ($view_days_num) {
                case '90':

                    if (empty($_REQUEST['scroll_start_date'])) $start_day = date("d");
                    $start_week_day_num = date("w");
                    $start_day_weeek  = get_bk_option( 'booking_start_day_weeek' ); //[0]:Sun .. [6]:Sut

                    if ($start_week_day_num != $start_day_weeek) {
                        for ($d_inc = 1; $d_inc < 8; $d_inc++) {                // Just get week  back
                            $real_date = mktime(0, 0, 0, $start_month, ($start_day-$d_inc ) , $start_year);
                            $start_week_day_num = date("w", $real_date);
                            if ($start_week_day_num == $start_day_weeek) {
                                $start_day = date("d", $real_date);
                                $start_year = date("Y", $real_date);
                                $start_month = date("m", $real_date);
                                $d_inc=9;
                                //break;
                            }
                        }
                    }

                    if (isset($_REQUEST['scroll_day'])) $scroll_day = $_REQUEST['scroll_day'];

                    $real_date = mktime(0, 0, 0, $start_month,    ( $start_day +$scroll_day) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, $start_month,    ($start_day+7*12+7+$scroll_day) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2013-12-31';
                    break;

                case '30':
                    if (empty($_REQUEST['scroll_start_date'])) $start_day = date("d");

                    if (isset($_REQUEST['scroll_day'])) $scroll_day = $_REQUEST['scroll_day'];

                    $real_date = mktime(0, 0, 0, $start_month,    ( $start_day +$scroll_day) ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, $start_month,    ($start_day+31+$scroll_day) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2013-12-31';
                    break;

                default:  // 365

                    if (isset($_REQUEST['scroll_month'])) $scroll_month = $_REQUEST['scroll_month'];
                    else $scroll_month = 0;

                    $real_date = mktime(0, 0, 0, ($start_month+$scroll_month),     $start_day ,     $start_year);
                    $wh_booking_date  = date("Y-m-d", $real_date);                          // '2012-12-01';

                    $real_date = mktime(0, 0, 0, ($start_month+$scroll_month+13), ($start_day-1) ,  $start_year);
                    $wh_booking_date2 = date("Y-m-d", $real_date);                          // '2013-12-31';

                    break;
            }
        }
        
        
        $or_sort = get_bk_option( 'booking_sort_order') ;        

        $args = array(
		'wh_booking_type' =>    (isset($_REQUEST['wh_booking_type']))?$_REQUEST['wh_booking_type']:'',
                'wh_approved' =>        '',                                     // Any
		'wh_booking_id' =>      '',                                     // Any
                'wh_is_new' =>          '',         //(isset($_REQUEST['wh_is_new']))?$_REQUEST['wh_is_new']:'',                  // ?
		'wh_pay_status' =>      'all',      //(isset($_REQUEST['wh_pay_status']))?$_REQUEST['wh_pay_status']:'',          // ?
                'wh_keyword' =>         '',         //(isset($_REQUEST['wh_keyword']))?$_REQUEST['wh_keyword']:'',                // ?
		'wh_booking_date' =>    $wh_booking_date,
                'wh_booking_date2' =>   $wh_booking_date2, 
		'wh_modification_date' =>  '3',     //(isset($_REQUEST['wh_modification_date']))?$_REQUEST['wh_modification_date']:'',     // ?
                'wh_modification_date2' => '',      //(isset($_REQUEST['wh_modification_date2']))?$_REQUEST['wh_modification_date2']:'',   // ?
		'wh_cost' =>            '',         //(isset($_REQUEST['wh_cost']))?$_REQUEST['wh_cost']:'',                      // ?
                'wh_cost2' =>           '',         //(isset($_REQUEST['wh_cost2']))?$_REQUEST['wh_cost2']:'',                    // ?
		'or_sort' =>            $or_sort,
		'page_num' =>           '1',
                'page_items_count' =>   '100000'
	);

        return $args;    
}


/** Set initial $_REQUEST['view_days_num'] depend from selected booking resources  */
function wpbc_set_request_params_for_timeline() {
    
    if ( (isset( $_REQUEST['wh_booking_type'] )) && ( strpos( $_REQUEST['wh_booking_type'], ',' ) !== false ) )
        $is_show_resources_matrix = true;
    else
        $is_show_resources_matrix = false;

    if ( !isset( $_REQUEST['view_days_num'] ) )
        $_REQUEST['view_days_num'] = get_bk_option( 'booking_view_days_num' );
    
    //We do not have the Year (365) and (90) view modes in the Matrix mode so  we are set to the closest variant. And the same backward.
    if ( ($is_show_resources_matrix ) ) { // Switching from the Single to Matrix mode.
        if ( $_REQUEST['view_days_num'] == '365' ) {
            $_REQUEST['view_days_num'] = 60;
        }
        if ( $_REQUEST['view_days_num'] == '90' ) {
            $_REQUEST['view_days_num'] = 7;
        }
    } else { // Switching from the Matrix to Single  mode.
        if ( $_REQUEST['view_days_num'] == '60' ) {
            $_REQUEST['view_days_num'] = 365;
        }
        if ( ($_REQUEST['view_days_num'] == '7') || ($_REQUEST['view_days_num'] == '1') ) {
            $_REQUEST['view_days_num'] = 30;
        }
    }
}


/** Define default booking resource to $_GET request and check if user can  be here in MU version. */
function wpbc_set_default_resource_to__get() { 
        
    if ( isset( $_GET['booking_type'] ) ) {                                     
        
        // Check if User can be here in MultiUser version for this booking resource (is this user owner of this resource or not)
        if ( class_exists( 'wpdev_bk_multiuser' ) ) {
            
            $default_booking_resource = $_GET['booking_type'];
            
            // Check if this MU user activated or superadmin,  otherwise show warning
            if ( ! wpbc_is_mu_user_can_be_here('activated_user') )                  
                return  false;

            // Check if this MU user owner of this resource or superadmin,  otherwise show warning
            if ( ! wpbc_is_mu_user_can_be_here( 'resource_owner', $default_booking_resource ) ) 
                return false;             
        }
        
    } else {                                                                    // Set default booking resource to  $_GET
        
        // Get ID of default booking resource, or return false (in case if user have no access to  this resource and show some warnings).
        $default_booking_resource = wpbc_get_default_resource();

        if ( empty( $default_booking_resource ) ) {

            return  false;                                                      // User can  not be here, Warnings have shown

        } else {
            
            $_GET['booking_type'] = $default_booking_resource;            
        }

        // Check if this resource parent and has some additional childs, if yes then assign $_GET['parent_res'] = 1  only  in case,  if its for loading default booking resource.  
        make_bk_action( 'check_if_bk_res_parent_with_childs_set_parent_res', $default_booking_resource );                       
    }
          
    
    return  true;
}


/**
	 * Get ID of default booking resource, or return false (in case if user have no access to  this resource and show some warnings).
 *  
 * @return boolean|int
 */
function wpbc_get_default_resource() { 

    if ( ! class_exists( 'wpdev_bk_personal' ) )
        return 1;                                                               // Free, i.e., default 1
    
    // Get assigned default booking resource from  General Booking Settings page 
    $default_booking_resource = get_bk_option( 'booking_default_booking_resource' );        // If empty, i.e., "" - its all  resources - no default booking resource
        
    if ( empty( $default_booking_resource ) ) { //We do  not have default resource
                
        // Get first resource in a list 
        // If its MU, then  for superadmin get first resource in a list OR if user DO NOT have resources get FIRST resource in LIST FROM ALL resources
        $default_booking_resource = get__default_type();
    }

    //MU
    if ( class_exists( 'wpdev_bk_multiuser' ) ) {  

        // Check if this MU user activated or superadmin,  otherwise show warning
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') )                  
            return  false;
        
        // Check if this MU user owner of this resource or superadmin,  otherwise show warning
        if ( ! wpbc_is_mu_user_can_be_here( 'resource_owner', $default_booking_resource ) ) 
            return false;             
    }
        
    return $default_booking_resource;    
}


/** Get list of all booking resources.
 *
 * @return array (
		            [1] => Array (
				                    [title] => Default [parent resource]
				                    [attr] => Array (
		                                                [class] => wpbc_parent_resource
		                                            )
		                )
		            [5] => Array (
		                            [title] =>      Default-1
		                            [attr] => Array (
						                            [class] => wpbc_child_resource
						                        )
		                )
		            [6] => Array (  ...
 */
function wpbc_get_all_booking_resources_list(){                                                                         //FixIn: 8.1.3.5.2

        if ( ! class_exists( 'wpdev_bk_personal' ) )
        return array();

        $resources_cache = wpbc_br_cache();                                     // Get booking resources from  cache

        $resource_objects = $resources_cache->get_resources();
        // $resource_objects = $resources_cache->get_single_parent_resources();

        //$resource_options = $params['resources'];

        foreach ( $resource_objects as $br) {

            $br_option = array();
            $br_option['title'] = apply_bk_filter('wpdev_check_for_active_language', $br['title'] );

            if ( (isset( $br['parent'] )) && ($br['parent'] == 0 ) && (isset( $br['count'] )) && ($br['count'] > 1 ) )
                $br_option['title'] .= ' [' . __('parent resource', 'booking') . ']';

            $br_option['attr'] = array();
            $br_option['attr']['class'] = 'wpbc_single_resource';
            if ( isset( $br['parent'] ) ) {
                if ( $br['parent'] == 0 ) {
                    if (  ( isset( $br['count'] ) ) && ( $br['count'] > 1 )  )
                        $br_option['attr']['class'] = 'wpbc_parent_resource';
                } else {
                    $br_option['attr']['class'] = 'wpbc_child_resource';
                }
            }

            $sufix = '';

            $resource_options[ $br['id'] . $sufix ] = $br_option;

            if ( $resource_options[ $br['id'] ]['attr']['class'] === 'wpbc_child_resource' ) {
                $resource_options[ $br['id'] ]['title'] = ' &nbsp;&nbsp;&nbsp; ' . $resource_options[ $br['id'] ]['title'];
            }
        }
        return $resource_options;
}

//////////////////////////////////////////////////////////////////////////////// 
//   S u p p o r t 
////////////////////////////////////////////////////////////////////////////////

/**
	 * Check if current WP user can be here
 * Checking if the user activated,  and booking resource belong to  specific user
 * 
 * @param string $check_condition   'activated_user' | 'resource_owner' | 'only_super_admin'
 * @param string $booking_resource - Optional. ID of booking resource for 'resource_owner' condition
 * @return boolean
 */
function wpbc_is_mu_user_can_be_here( $check_condition,  $booking_resource  = '' ) {
    
    $is_can = true;
    
    if ( $check_condition == 'activated_user' )
        $is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'check_for_active_users' );
    
    if ( $check_condition == 'only_super_admin' )
        $is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, 'only_super_admin' );
            
    if ( $check_condition == 'resource_owner' )        
        $is_can = apply_bk_filter( 'multiuser_is_user_can_be_here', true, $booking_resource );
    
    return $is_can;    
}


////////////////////////////////////////////////////////////////////////////////
// E n g i n e     B o o k i n g    L i s t i n g
////////////////////////////////////////////////////////////////////////////////

/**
	 * E n g i n e   for getting Bookings objects for Booking Listing pages
 * 
 * @param array $args  - Request paramters from filters
 * @return array(
                    'bookings' => $bookings
                    , 'resources' => $booking_types
                    , 'bookings_count' => $bookings_count
                    , 'page_num' => $page_num
                    , 'count_per_page' => $page_items_count
                );     
 */
function wpbc_get_bookings_objects( $args ){
    
    global $wpdb;

    // Initial  variables //////////////////////////////////////////////////////

    $sql_boking_listing = wpbc_get_sql_for_booking_listing( $args );
    $sql_start_count    = $sql_boking_listing[ 'sql_start_count' ];
    $sql_start_select   = $sql_boking_listing[ 'sql_start_select' ];
    $sql                = $sql_boking_listing[ 'sql' ];
    $sql_where          = $sql_boking_listing[ 'where' ];
    $sql_order          = $sql_boking_listing[ 'order' ];
    $sql_limit          = $sql_boking_listing[ 'limit' ];

    $num_per_page_check = get_bk_option( 'booking_num_per_page' );
    $defaults = array(
          'wh_booking_type' => ''
        , 'wh_approved' => ''
        , 'wh_booking_id' => ''
        , 'wh_is_new' => ''
        , 'wh_pay_status' => ''
        , 'wh_keyword' => ''
        , 'wh_booking_date' => ''
        , 'wh_booking_date2' => ''
        , 'wh_modification_date' => ''
        , 'wh_modification_date2' => ''
        , 'wh_cost' => ''
        , 'wh_cost2' => ''
        , 'or_sort' => get_bk_option( 'booking_sort_order' )
        , 'page_num' => '1'
        , 'wh_trash' => ''                                                      //FixIn: 6.1.1.10
        , 'page_items_count' => ( ( empty( $num_per_page_check ) ) ? '10' : $num_per_page_check )
    );

    $r = wp_parse_args( $args, $defaults );
    
    $r = apply_filters( 'wpbc_request_params_for_get_booking_obj', $r );               //FixIn: 7.0.1.41

    extract( $r, EXTR_SKIP );

    $page_start = ( $page_num - 1 ) * $page_items_count;

//debuge( $sql_start_select . $sql . $sql_where . $sql_order . $sql_limit );
    
    ////////////////////////////////////////////////////////////////////////////


    $bookings_res = $wpdb->get_results( $sql_start_select . $sql . $sql_where . $sql_order . $sql_limit );      // Get Bookings
    
    $bookings_count = $wpdb->get_results( $sql_start_count . $sql . $sql_where );                               // Get Number of bookings    
    $bookings_count = ( ( count( $bookings_count ) > 0 ) ? $bookings_count[0]->count : 0 );
    
    $booking_types = apply_bk_filter( 'wpdebk_get_keyed_all_bk_resources', array() );                           // Get Resources

    
    ////////////////////////////////////////////////////////////////////////////
    
    $booking_id_list    = array();                                              // ID list of ALL bookings
    $bookings           = array();
    $short_days         = array();
    $short_days_type_id = array();

    foreach ( $bookings_res as $booking ) {

        if ( ! in_array( $booking->booking_id, $booking_id_list ) )
            $booking_id_list[] = $booking->booking_id;

        $bookings[$booking->booking_id] = $booking;
        $bookings[$booking->booking_id]->dates = array();
        $bookings[$booking->booking_id]->dates_short = array();

        $bk_list_type = (isset( $booking->booking_type )) ? $booking->booking_type : '1';

        if ( ( isset( $booking->sync_gid ) ) && (!empty( $booking->sync_gid )) ) {
            $booking->form .= "~text^sync_gid{$booking->booking_type}^{$booking->sync_gid}";
        }

        $cont = get_form_content(   $booking->form
                                    , $bk_list_type
                                    , ''
                                    , array( 
                                            'booking_id'     => $booking->booking_id
                                          , 'resource_title' => (isset( $booking_types[$booking->booking_type] )) ? $booking_types[$booking->booking_type] : ''
                                        )
                                );
//debuge($cont);
        $search  = array( "'(<br[ ]?[/]?>)+'si", "'(<[/]?p[ ]?>)+'si"/*, "'(<[/]?div[ ]?>)+'si"*/ );                        //FixIn: 8.8.1.6
        $replace = array( "&nbsp;&nbsp;", " &nbsp; ", " &nbsp; " );
        $cont['content'] = preg_replace( $search, $replace, $cont['content'] );
//debuge( htmlentities(  $cont['content'] ) );die;
        $bookings[$booking->booking_id]->form_show = $cont['content'];
        unset( $cont['content'] );
        $bookings[$booking->booking_id]->form_data = $cont;
    }
    $booking_id_list = implode( ",", $booking_id_list );
    $booking_id_list = wpbc_clean_like_string_for_db( $booking_id_list );

    ////////////////////////////////////////////////////////////////////////////
    
    if ( ! empty( $booking_id_list ) ) {                                        // Get Dates for all our Bookings
        $sql = "SELECT *
                FROM {$wpdb->prefix}bookingdates as dt
                WHERE dt.booking_id in ( {$booking_id_list} ) ";

        if ( class_exists( 'wpdev_bk_biz_l' ) )
            $sql .= " ORDER BY booking_id, type_id, booking_date   ";
        else
            $sql .= " ORDER BY booking_id, booking_date   ";

        $booking_dates = $wpdb->get_results( $sql );
    } else
        $booking_dates = array();


    ////////////////////////////////////////////////////////////////////////////
    
    $last_booking_id = '';
    
    foreach ( $booking_dates as $date ) {                                       // Add Dates to Bookings array
        
        $bookings[ $date->booking_id ]->dates[] = $date;

        if ( $date->booking_id != $last_booking_id ) {
            if ( !empty( $last_booking_id ) ) {
                if ( $last_show_day != $dte ) {
                    $short_days[] = $dte;
                    $short_days_type_id[] = $last_day_id;
                }

                $bookings[$last_booking_id]->dates_short = $short_days;
                $bookings[$last_booking_id]->dates_short_id = $short_days_type_id;
            }
            $last_day = '';
            $last_day_id = '';
            $last_show_day = '';
            $short_days = array();
            $short_days_type_id = array();
        }

        $last_booking_id = $date->booking_id;
        $dte = $date->booking_date;

        if ( empty( $last_day ) ) { // First date
            $short_days[] = $dte;
            $short_days_type_id[] = (isset( $date->type_id )) ? $date->type_id : '';
            $last_show_day = $dte;
        } else {                // All other days
            if ( wpbc_is_next_day( $dte, $last_day ) ) {
                if ( $last_show_day != '-' ) {
                    $short_days[] = '-';
                    $short_days_type_id[] = '';
                }
                $last_show_day = '-';
            } else {
                if ( $last_show_day != $last_day ) {
                    $short_days[] = $last_day;
                    $short_days_type_id[] = $last_day_id;
                }
                $short_days[] = ',';
                $short_days_type_id[] = '';
                $short_days[] = $dte;
                $short_days_type_id[] = (isset( $date->type_id )) ? $date->type_id : '';
                $last_show_day = $dte;
            }
        }
        $last_day = $dte;
        $last_day_id = (isset( $date->type_id )) ? $date->type_id : '';
    }

    if ( isset( $dte ) )
        if ( $last_show_day != $dte ) {
            $short_days[] = $dte;
            $short_days_type_id[] = (isset( $date->type_id )) ? $date->type_id : '';
        }
    if ( isset( $bookings[$last_booking_id] ) ) {
        $bookings[$last_booking_id]->dates_short = $short_days;
        $bookings[$last_booking_id]->dates_short_id = $short_days_type_id;
    }

//debuge( 'Before filtering:',  date('Y-m-d', time() +86400 ), $_REQUEST, $bookings ) ;       

    ////////////////////////////////////////////////////////////////////////////
    // Filter some bookings
    ////////////////////////////////////////////////////////////////////////////
    
    // Showing only  bookings that starting or ending during "Today"            ( Today check in/out )
    if ( (isset( $args['wh_booking_date'] )) && ($args['wh_booking_date'] == '9') ) {

        $today_mysql_format = date_i18n( 'Y-m-d', time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) + 0 * DAY_IN_SECONDS ); // Today day with gmt offset
        
        foreach ( $bookings as $bc_id => $bc_value ) {

            $check_in_date = $bc_value->dates_short[0];
            $check_in_date = explode( ' ', $check_in_date );
            $check_in_date = $check_in_date[0];                                 // 2014-02-25

            if ( count( $bc_value->dates_short ) == 1 )
                $check_out_date = $bc_value->dates_short[0];
            else
                $check_out_date = $bc_value->dates_short[2];
            $check_out_date = explode( ' ', $check_out_date );
            $check_out_date = $check_out_date[0];                               // 2014-02-25

            if ( ( $today_mysql_format != $check_in_date ) && ( $today_mysql_format != $check_out_date ) ) {
                unset( $bookings[$bc_id] );
                $bookings_count--;
            }
        }
    }


    // If we selected the Dates as "Check In - Today/Tommorow", then show only the bookings, where check in date is Today 
    if ( (isset( $args['wh_booking_date'] )) && ($args['wh_booking_date'] == '7') ) {
        //$today_mysql_format = date('Y-m-d');
        //$today_mysql_format = date('Y-m-d', time() +86400 );                  // 1 Day = 24*60*60 = 86400
        $today_mysql_format = date_i18n( 'Y-m-d', time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) + DAY_IN_SECONDS ); // Tommorow day with gmt offset
        foreach ( $bookings as $bc_id => $bc_value ) {
            $check_in_date = $bc_value->dates_short[0];
            $check_in_date = explode( ' ', $check_in_date );
            $check_in_date = $check_in_date[0]; // 2014-02-25
            if ( $today_mysql_format != $check_in_date ) {
                unset( $bookings[$bc_id] );
                $bookings_count--;
            }
        }
    }


    // If we selected the Dates as "Check Out - Tomorow", then show only the bookings, where check out date is Tomorrow 
    if ( (isset( $args['wh_booking_date'] )) && ($args['wh_booking_date'] == '8') ) {
        //$tomorrow_mysql_format = date('Y-m-d', time() +86400 );               // 1 Day = 24*60*60 = 86400
        $tomorrow_mysql_format = date_i18n( 'Y-m-d', time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) + DAY_IN_SECONDS ); // Tommorow day with gmt offset
        foreach ( $bookings as $bc_id => $bc_value ) {
            if ( count( $bc_value->dates_short ) == 1 )
                $check_out_date = $bc_value->dates_short[0];
            else
                $check_out_date = $bc_value->dates_short[2];
            $check_out_date = explode( ' ', $check_out_date );
            $check_out_date = $check_out_date[0];                               // 2014-02-25
            if ( $tomorrow_mysql_format != $check_out_date ) {
                unset( $bookings[$bc_id] );
                $bookings_count--;
            }
        }
    }
    
    
    $return_booking_structure = array(
                                        'bookings' => $bookings
                                        , 'resources' => $booking_types
                                        , 'bookings_count' => $bookings_count
                                        , 'page_num' => $page_num
                                        , 'count_per_page' => $page_items_count
                                    );     
//debuge( $return_booking_structure );

    return $return_booking_structure;
}


////////////////////////////////////////////////////////////////////////////////
// S Q L 
////////////////////////////////////////////////////////////////////////////////

/**
	 * Get   S Q L    for bookings at Booking Listing page
 * 
 * @param array $args   - Request paramters from filters
 * @return array        - Get array with SQL for getting bookings
 */
function wpbc_get_sql_for_booking_listing( $args ){

    global $wpdb;
    
    $num_per_page_check = get_bk_option( 'booking_num_per_page' );
    if ( empty( $num_per_page_check ) ) {
        $num_per_page_check = '10';
        update_bk_option( 'booking_num_per_page', $num_per_page_check );
    }

    $defaults = array(
          'wh_booking_type' => ''
        , 'wh_approved' => ''
        , 'wh_booking_id' => ''
        , 'wh_is_new' => ''
        , 'wh_pay_status' => ''
        , 'wh_keyword' => ''
        , 'wh_booking_date' => ''
        , 'wh_booking_date2' => ''
        , 'wh_modification_date' => ''
        , 'wh_modification_date2' => ''
        , 'wh_cost' => ''
        , 'wh_cost2' => ''
        , 'or_sort' => get_bk_option( 'booking_sort_order' )
        , 'page_num' => '1'
        , 'wh_trash' => ''                                                      //FixIn: 6.1.1.10
        , 'wh_sync_gid' => ''                                       // '' | 'imported' | 'plugin'                       //FixIn: 8.8.3.19
        , 'page_items_count' => $num_per_page_check
    );
    $r = wp_parse_args( $args, $defaults );
    extract( $r, EXTR_SKIP );

    $page_start = ( $page_num - 1 ) * $page_items_count;


    $posible_sorts = array( 'booking_id_asc', 'sort_date', 'sort_date_asc', 'booking_type', 'booking_type_asc', 'cost', 'cost_asc' );
    
    if ( ($or_sort == '') || ($or_sort == 'id') || ( !in_array( $or_sort, $posible_sorts ) ) ) 
            $or_sort = 'booking_id';

    
    ////////////////////////////////////////////////////////////////////////
    // S Q L
    ////////////////////////////////////////////////////////////////////////    
    $sql_start_select = " SELECT * ";
    $sql_start_count  = " SELECT COUNT(*) as count";
    $sql              = " FROM {$wpdb->prefix}booking as bk";                   // GET ONLY ROWS OF THE     B o o k i n g s    - So we can limit the requests
    
//debuge($wh_trash);        
                                                                                //FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings}
    $trash_bookings = " AND bk.trash = 0 ";
    if ( isset( $wh_trash ) ) {

        if ( $wh_trash == "trash" )    $trash_bookings = " AND bk.trash = 1 ";            
        else if ( $wh_trash == "any" ) $trash_bookings = '';   
    }
//debuge($trash_bookings);
    
    if ( empty( $wh_booking_id ) ) {
        
        $sql_where =    " WHERE " .                                             // Date (single) connection (Its required for the correct Pages in SQL: LIMIT Keyword)
                                " EXISTS (
                                           SELECT *
                                           FROM {$wpdb->prefix}bookingdates as dt
                                           WHERE  bk.booking_id = dt.booking_id ";

        if ( 'lost' == $wh_booking_type ) {     //FixIn: 8.5.2.19

	    	$sql_where.=                  " AND bk.booking_type NOT IN ( SELECT DISTINCT booking_type_id FROM {$wpdb->prefix}bookingtypes ) ";
	    	$sql_where.=                  " ) ";
			$wh_booking_type = '';

	    } else {

	        if ( $wh_approved !== '' )
	            $sql_where.=                       " AND approved = $wh_approved ";             // Approved or Pending

	        $sql_where .= wpbc_set_sql_where_for_dates( $wh_booking_date, $wh_booking_date2 );

	        $sql_where.=                   " ) ";

	        $sql_where .= " {$trash_bookings} ";                                    //FixIn: 6.1.1.10

	        //FixIn: 8.8.3.19
	        if ( 'imported' === $wh_sync_gid ) {
		        $sql_where .= " AND  bk.sync_gid != '' ";
	        }
	        if ( 'plugin' === $wh_sync_gid ) {
		        $sql_where .= " AND  bk.sync_gid = '' ";
	        }

	        if ( $wh_is_new !== '' )
	            $sql_where .=       " AND  bk.is_new = " . $wh_is_new . " ";

	        $sql_where .= apply_bk_filter( 'get_bklist_sql_keyword', '', $wh_keyword );             // P

	        $sql_where .= wpbc_set_sql_where_for_modification_date( $wh_modification_date, $wh_modification_date2 );

	        $sql_where .= apply_bk_filter( 'get_bklist_sql_paystatus', '', $wh_pay_status );        // BS
	        $sql_where .= apply_bk_filter( 'get_bklist_sql_cost', '', $wh_cost, $wh_cost2 );        // BS

	        $sql_where .= apply_bk_filter( 'get_bklist_sql_resources', '', $wh_booking_type, $wh_approved, $wh_booking_date, $wh_booking_date2 );   // P  || BL
        }

    } else {

	    //FixIn: 8.7.7.10
    	if ( strpos( $wh_booking_id, '<' ) !== false ) {
		    $wh_booking_id = str_replace( '<', '', $wh_booking_id );
		    $wh_booking_id = intval( $wh_booking_id );
		    $sql_where = " WHERE bk.booking_id < " . $wh_booking_id . " ";
	    }

    	else if ( strpos( $wh_booking_id, '>' ) !== false ) {
		    $wh_booking_id = str_replace( '>', '', $wh_booking_id );
		    $wh_booking_id = intval( $wh_booking_id );
		    $sql_where = " WHERE bk.booking_id > " . $wh_booking_id . " ";

	    }

    	else if ( strpos( $wh_booking_id, ',' ) !== false ) {
		    $sql_where = " WHERE bk.booking_id IN (" . $wh_booking_id . ") ";

	    } else {
		    $sql_where = " WHERE bk.booking_id = " . $wh_booking_id . " ";
	    }

        // Check  if searching booking is belonging to specific user in  Booking Calendar MultiUser version 
        $sql_where = apply_bk_filter('update_where_sql_for_getting_bookings_in_multiuser', $sql_where );

    }

    if ( strpos( $or_sort, '_asc' ) !== false ) {                               // Order
        $or_sort = str_replace( '_asc', '', $or_sort );
        $sql_order = " ORDER BY " . $or_sort . " ASC ";
    } else
        $sql_order = " ORDER BY " . $or_sort . " DESC ";


    $sql_limit = $wpdb->prepare( " LIMIT %d, %d ", $page_start, $page_items_count );

    $return_res = array(
                      'sql_start_count'  => $sql_start_count
                    , 'sql_start_select' => $sql_start_select
                    , 'sql'   => $sql
                    , 'where' => $sql_where
                    , 'order' => $sql_order
                    , 'limit' => $sql_limit 
                );

//debuge($return_res);
    return $return_res;
}


////////////////////////////////////////////////////////////////////////////////
// S Q L     W H E R E
////////////////////////////////////////////////////////////////////////////////

/**
	 * Get SQL   W H E R E   conditions for   D a t e s   of  bookings
 * 
 * @param string $wh_booking_date     - Parameter from Booking Listing request (usually  its number)
 * @param string $wh_booking_date2    - Parameter from Booking Listing request (usually  its number)
 * @param string $pref                - Optional. Prefix for table.
 * @return string - WHERE conditions for SQL
 */
function wpbc_set_sql_where_for_dates( $wh_booking_date, $wh_booking_date2, $pref = 'dt.' ) {

    $sql_where= '';
    if ($pref == 'dt.')  { $and_pre = ' AND '; $and_suf = ''; }
    else                 { $and_pre = ''; $and_suf = ' AND '; }

                                                                                // Actual
    if (  ( ( $wh_booking_date  === '' ) && ( $wh_booking_date2  === '' ) ) || ($wh_booking_date  === '0') ) {
        $sql_where =               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL '00:00:01' HOUR_SECOND ) ) ".$and_suf ;      //FixIn: 8.5.2.14

    } else  if ($wh_booking_date  === '1') {                                    // Today								//FixIn: 7.1.2.8
        $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL '23:59:59' HOUR_SECOND ) ) ".$and_suf ;
        $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL '00:00:01' HOUR_SECOND ) ) ".$and_suf ;     //FixIn: 8.4.7.21


    } else if ($wh_booking_date  === '2') {                                     // Previous
        $sql_where =               $and_pre."( ".$pref."booking_date <= ( CURDATE() - INTERVAL '00:00:01' HOUR_SECOND ) ) ".$and_suf ;      //FixIn: 8.5.2.16

    } else if ($wh_booking_date  === '3') {                                     // All
        $sql_where =  '';

    } else if ($wh_booking_date  === '4') {                                     // Next
        $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL ". $wh_booking_date2 . " DAY ) ) ".$and_suf ;
        // $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;
	    $sql_where .=               $and_pre."( ".$pref."booking_date > ( CURDATE() ) ) ".$and_suf ;                    //FixIn: 8.0.1.1

    } else if ($wh_booking_date  === '5') {                                     // Prior
        $wh_booking_date2 = str_replace('-', '', $wh_booking_date2);
        $sql_where  =               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL ". $wh_booking_date2 . " DAY ) ) ".$and_suf ;
        $sql_where .=               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

    } else  if ($wh_booking_date  === '7') {                                    // Check In date - Today/Tomorrow
          // $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL '23:59:59' HOUR_SECOND ) ) ".$and_suf ;
          // $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() ) ) ".$and_suf ;
          $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL '1 23:59:59' DAY_SECOND ) ) ".$and_suf ;
          $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

    } else  if ($wh_booking_date  === '8') {                                    // Check Out date - Tomorrow
        $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL '1 23:59:59' DAY_SECOND ) ) ".$and_suf ;
        $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

    } else  if ($wh_booking_date  === '9') {                                    // Today check in/out
        $sql_where  =               $and_pre."( ".$pref."booking_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;
        $sql_where .=               $and_pre."( ".$pref."booking_date >= ( CURDATE() - INTERVAL 1 DAY ) ) ".$and_suf ;

    } else {                                                                    // Fixed

        if ( $wh_booking_date  !== '' )
            if ( strpos($wh_booking_date,':')===false )                         // we are do not have the time in this date, so  set it
                 $sql_where.= $and_pre."( ".$pref."booking_date >= '" . $wh_booking_date . " 00:00:00' ) ".$and_suf;
            else $sql_where.= $and_pre."( ".$pref."booking_date >= '" . $wh_booking_date . "' ) ".$and_suf;

        if ( $wh_booking_date2  !== '' )
            if ( strpos($wh_booking_date2,':')===false )                        // we are do not have the time in this date, so  set it
                 $sql_where.=               $and_pre."( ".$pref."booking_date <= '" . $wh_booking_date2 . " 23:59:59' ) ".$and_suf;
            else $sql_where.=               $and_pre."( ".$pref."booking_date <= '" . $wh_booking_date2 . "' ) ".$and_suf;
    }

    return $sql_where;
}


/**
	 * Get SQL   W H E R E   conditions for   M o d i f i c a t i o n    D a t e   of  bookings
 * 
 * @param type $wh_modification_date    - Parameter from Booking Listing request (usually  its number)
 * @param type $wh_modification_date2   - Parameter from Booking Listing request (usually  its number)
 * @param string $pref                  - Optional. Prefix for table.
 * @return string - WHERE conditions for SQL
 */
function wpbc_set_sql_where_for_modification_date( $wh_modification_date, $wh_modification_date2, $pref = 'bk.' ) {

    $sql_where = '';
    
    if ($pref == 'bk.')  { $and_pre = ' AND '; $and_suf = ''; }
    else                 { $and_pre = ''; $and_suf = ' AND '; }

    if ($wh_modification_date  === '1') {                                       // Today
        $sql_where  =               $and_pre."( ".$pref."modification_date <= ( CURDATE() + INTERVAL '23:59:59' HOUR_SECOND ) ) ".$and_suf ;    //FixIn: 8.4.7.22
        $sql_where .=               $and_pre."( ".$pref."modification_date >= ( CURDATE() - INTERVAL '00:00:01' HOUR_SECOND ) ) ".$and_suf ;    //FixIn: 8.4.7.22

    } else if ($wh_modification_date  === '3') {                                // All
        $sql_where =  '';

    } else if ($wh_modification_date  === '5') {                                // Prior
        $wh_modification_date2 = str_replace('-', '', $wh_modification_date2);
        $sql_where  =               $and_pre."( ".$pref."modification_date >= ( CURDATE() - INTERVAL ". $wh_modification_date2 . " DAY ) ) ".$and_suf ;
        $sql_where .=               $and_pre."( ".$pref."modification_date <= ( CURDATE() + INTERVAL 1 DAY ) ) ".$and_suf ;

    } else {                                                                    // Fixed

        if ( $wh_modification_date  !== '' )
            $sql_where.=               $and_pre."( ".$pref."modification_date >= '" . $wh_modification_date . "' ) ".$and_suf;

        if ( $wh_modification_date2  !== '' )
            $sql_where.=               $and_pre."( ".$pref."modification_date <= '" . $wh_modification_date2 . "' ) ".$and_suf;
    }

    return $sql_where;
}
