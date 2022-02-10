<?php
if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class wpdev_booking {

    public $popover_front_end_js_is_writed;		//FixIn: Flex TimeLine 1.0		-- previos this was private and not public property
    
    // <editor-fold defaultstate="collapsed" desc="  C O N S T R U C T O R  &  P r o p e r t i e s ">

    var $wpdev_bk_personal;
    var $captcha_instance;

    function __construct() {

	    $this->popover_front_end_js_is_writed = false;

	    $this->captcha_instance = new wpdevReallySimpleCaptcha();


	    if ( class_exists( 'wpdev_bk_personal' ) ) {
		    $this->wpdev_bk_personal = new wpdev_bk_personal();
	    } else {
		    $this->wpdev_bk_personal = false;
	    }

	    // Set loading translation
	    add_action( 'plugins_loaded', 'wpbc_load_translation', 1000 );

	    // Check content according language shortcodes
	    add_bk_filter( 'wpdev_check_for_active_language', 'wpdev_check_for_active_language' );

	    // User defined - hooks
	    add_action( 'wpdev_bk_add_calendar', array( &$this, 'add_calendar_action' ), 10, 2 );
	    add_action( 'wpdev_bk_add_form', array( &$this, 'add_booking_form_action' ), 10, 2 );
	    add_bk_action( 'wpdevbk_add_form', array( &$this, 'add_booking_form_action' ) );
	    add_filter( 'wpdev_bk_get_form', array( &$this, 'get_booking_form_action' ), 10, 2 );
	    add_bk_filter( 'wpdevbk_get_booking_form', array( &$this, 'get_booking_form_action' ) );
	    add_filter( 'wpdev_bk_get_showing_date_format', array( &$this, 'get_showing_date_format' ), 10, 1 );

	    // Get script for calendar activation
	    add_bk_filter( 'get_script_for_calendar', array( &$this, 'get_script_for_calendar' ) );
	    add_bk_filter( 'pre_get_calendar_html', array( &$this, 'pre_get_calendar_html' ) );

	    // S H O R T C O D E s - Booking
	    add_shortcode( 'booking', array( &$this, 'booking_shortcode' ) );
	    add_shortcode( 'bookingcalendar', array( &$this, 'booking_calendar_only_shortcode' ) );
	    add_shortcode( 'bookingform', array( &$this, 'bookingform_shortcode' ) );
	    add_shortcode( 'bookingedit', array( &$this, 'bookingedit_shortcode' ) );
	    add_shortcode( 'bookingsearch', array( &$this, 'bookingsearch_shortcode' ) );
	    add_shortcode( 'bookingsearchresults', array( &$this, 'bookingsearchresults_shortcode' ) );
	    add_shortcode( 'bookingselect', array( &$this, 'bookingselect_shortcode' ) );
	    add_shortcode( 'bookingresource', array( &$this, 'bookingresource_shortcode' ) );
	    add_shortcode( 'bookingtimeline', array( &$this, 'bookingtimeline_shortcode' ) );
	    add_shortcode( 'bookingcustomerlisting', array( &$this, 'bookingcustomerlisting_shortcode' ) );					//FixIn: 8.1.3.5

	    add_shortcode( 'booking_test_speed', array( &$this, 'booking_test_speed_shortcode' ) );                         //FixIn: 8.7.11.13
    }
    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc="   S U P P O R T     F U N C T I O N S     ">

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  S U P P O R T     F U N C T I O N S        ///////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function silent_deactivate_WPBC() {
        deactivate_plugins( WPBC_PLUGIN_DIRNAME . '/' . WPBC_PLUGIN_FILENAME, true );
    }
    



    // Change date format
    function get_showing_date_format($mydate ) {
        $date_format = get_bk_option( 'booking_date_format');
        if ($date_format == '') $date_format = "d.m.Y";

        $time_format = get_bk_option( 'booking_time_format');
        if ( $time_format !== false  ) {
            $time_format = ' ' . $time_format;
            $my_time = date('H:i:s' , $mydate);
            if ($my_time == '00:00:00')     $time_format='';
        }
        else  $time_format='';

        // return date($date_format . $time_format , $mydate);
        return date_i18n($date_format,$mydate) .'<sup class="booking-table-time">' . date_i18n($time_format  , $mydate).'</sup>';
    }
    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc="   B O O K I N G s       A D M I N       F U N C T I O N s   ">
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  B  O O K I N G s       A D M I N       F U N C T I O N s       ///////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Get dates
    function get_dates ($approved = 'all', $bk_type = 1, $additional_bk_types= array(),  $skip_booking_id = ''  ) {
        /*
        $bk_type_1 = explode(',', $bk_type); $bk_type = '';
        foreach ($bk_type_1 as $bkt) {
            if (!empty($bkt)) { $bk_type .= $bkt . ','; }
        }
        $bk_type = substr($bk_type, 0, -1);

        $additional_bk_types_1= array();
        foreach ($additional_bk_types as $bkt) {
            if (!empty($bkt)) { $additional_bk_types_1[] = $bkt; }
        }
        $additional_bk_types =$additional_bk_types_1;*/
//return    array(array(),array());
        // if ( ! defined('WP_ADMIN') ) if ($approved == 0)  return array(array(),array());
//debuge($bk_type, $additional_bk_types );
        make_bk_action('check_pending_not_paid_auto_cancell_bookings', $bk_type );

        if ( count($additional_bk_types)>0 ) $bk_type_additional = $bk_type .',' . implode(',', $additional_bk_types);
        else                                 $bk_type_additional = $bk_type;
        $bk_type_additional .= ',';

        $bk_type_additional = explode( ',', $bk_type_additional );
        $bk_type_additional = array_unique( $bk_type_additional );                        // Removes duplicate values from an array
        $bk_type_additional = array_filter( $bk_type_additional );                      // all entries of array equal to FALSE (0, '', '0' ) will be removed.
        $bk_type_additional = implode( ',', $bk_type_additional );

        global $wpdb;
        $dates_array = $time_array = array();
        
        $trash_bookings = ' AND bk.trash != 1 ';                                //FixIn: 6.1.1.10  - check also  below usage of {$trash_bookings}
        
        if ($approved == 'admin_blank') {

            $sql_req = "SELECT DISTINCT dt.booking_date

                     FROM {$wpdb->prefix}bookingdates as dt

                     INNER JOIN {$wpdb->prefix}booking as bk

                     ON    bk.booking_id = dt.booking_id

                     WHERE  dt.booking_date >= CURDATE() {$trash_bookings} AND bk.booking_type IN ($bk_type_additional) AND bk.form like '%admin@blank.com%'

                     ORDER BY dt.booking_date" ;
            $dates_approve = $wpdb->get_results(  $sql_req  );
            
        } else {
            
            if ($approved == 'all')
                $sql_req = apply_bk_filter('get_bk_dates_sql', "SELECT DISTINCT dt.booking_date

                     FROM {$wpdb->prefix}bookingdates as dt

                     INNER JOIN {$wpdb->prefix}booking as bk

                     ON    bk.booking_id = dt.booking_id

                     WHERE  dt.booking_date >= CURDATE() {$trash_bookings} AND bk.booking_type IN ($bk_type_additional)
                         
                     ". (($skip_booking_id != '') ? " AND dt.booking_id NOT IN ( ".$skip_booking_id." ) ":"") ."
                         
                     ORDER BY dt.booking_date", $bk_type_additional, 'all' , $skip_booking_id);

            else
                $sql_req = apply_bk_filter('get_bk_dates_sql', "SELECT DISTINCT dt.booking_date

                     FROM {$wpdb->prefix}bookingdates as dt

                     INNER JOIN {$wpdb->prefix}booking as bk

                     ON    bk.booking_id = dt.booking_id

                     WHERE  dt.approved = $approved AND dt.booking_date >= CURDATE() {$trash_bookings} AND bk.booking_type IN ($bk_type_additional)
                         
                     ". (($skip_booking_id != '') ? " AND dt.booking_id NOT IN ( ".$skip_booking_id." ) ":"") ."

                     ORDER BY dt.booking_date", $bk_type_additional, $approved, $skip_booking_id );
//$sql_req = str_replace( 'dt.booking_date >= CURDATE()  AND', '' , $sql_req);	//Show past bookings,  as well
            $dates_approve = apply_bk_filter('get_bk_dates', $wpdb->get_results( $sql_req ), $approved, 0,$bk_type );
        }

        // Make aggregation of the dates for the parent booking resources with  specific capacity
	    if ( 0 ) {
		    $bk_res_aggregate  = implode( ',', array_splice( $additional_bk_types, 1, 1 ) );
		    $sql_req_aggregate = "SELECT DISTINCT dt.booking_date
                     			FROM {$wpdb->prefix}bookingdates as dt
                     		INNER JOIN {$wpdb->prefix}booking as bk
                     			ON    bk.booking_id = dt.booking_id
                     		WHERE  dt.booking_date >= CURDATE() {$trash_bookings} AND bk.booking_type IN ({$bk_res_aggregate})                         
                     		" . ( ( $skip_booking_id != '' ) ? " AND dt.booking_id NOT IN ( " . $skip_booking_id . " ) " : "" ) . "                         
                            ORDER BY dt.booking_date";
		    $dates_aggregate   = $wpdb->get_results( $sql_req_aggregate );
		    $dates_approve     = array_merge( $dates_approve, $dates_aggregate );
	    }

        //FixIn: 6.1.1.18
        $prior_check_out_date = false;
        if ( ! empty( $dates_approve ) )
            foreach ($dates_approve as $my_date) {
            
                $blocked_days_range = array( $my_date->booking_date );    
//debuge( 'before', $blocked_days_range, $prior_check_out_date );
				//if ( ! in_array( (string) $bk_type, array( '2', '3' ) ) )  // Skip these booking resources '2' and '3' from adding additional unavailable times before after  the booking
					list( $blocked_days_range, $prior_check_out_date ) = apply_filters( 'wpbc_get_extended_block_dates_filter', array( $blocked_days_range, $prior_check_out_date ) );
//debuge( 'after', $blocked_days_range, $prior_check_out_date );
                // Define booked dates and times
                foreach ( $blocked_days_range as $in_date) {
                    
                    $my_date = explode(' ', $in_date );

                    $my_dt = explode('-',$my_date[0]);
                    $my_tm = explode(':',$my_date[1]);

                    array_push( $dates_array , $my_dt );
                    array_push( $time_array , $my_tm );                        
                }
            }
        //FixIn: 6.1.1.18   End
        return    array($dates_array,$time_array); 
    }

    // Generate booking CAPTCHA fields  for booking form
    function createCapthaContent($bk_tp) {

	    //FixIn: 8.8.3.5
    	if ( function_exists('gd_info') ) {
            $gd_info = gd_info();
            if ( isset( $gd_info['GD Version'] ) )
                $gd_info = $gd_info['GD Version'];
            else
                $gd_info = json_encode( $gd_info );
        } else {
            return '<strong>Error!</strong>  CAPTCHA requires the GD library activated in your PHP configuration. Please check more <a href="https://wpbookingcalendar.com/faq/captcha-showing-problems/">here</a>.';
        }


        $admin_uri = ltrim( str_replace( get_site_url( null, '', 'admin' ), '', admin_url('admin.php?') ), '/' ) ;
        if ( (  get_bk_option( 'booking_is_use_captcha' ) !== 'On' ) 
                || ( strpos($_SERVER['REQUEST_URI'], $admin_uri ) !== false ) 
           ) return '';
        else {
			// Clean up dead files older than  2 minutes
            $this->captcha_instance->cleanup(2);					//FixIn: 7.0.1.67				
			//$this->captcha_instance->img_size = array( 72, 24 );
			/* Background color of CAPTCHA image. RGB color 0-255 */
			//$this->captcha_instance->bg = array( 0, 0, 0 );//array( 255, 255, 255 );
			/* Foreground (character) color of CAPTCHA image. RGB color 0-255 */
			//$this->captcha_instance->fg = array( 255, 255, 255 );//array( 0, 0, 0 );
			/* Coordinates for a text in an image. I don't know the meaning. Just adjust. */
			//$this->captcha_instance->base = array( 6, 18 );
			/* Font size */
			//$this->captcha_instance->font_size = 14;
			/* Width of a character */
			//$this->captcha_instance->font_char_width = 15;
			/* Image type. 'png', 'gif' or 'jpeg' */
			//$this->captcha_instance->img_type = 'png';
			
            $word = $this->captcha_instance->generate_random_word();
            $prefix = mt_rand();
            $this->captcha_instance->generate_image($prefix, $word);

            $filename = $prefix . '.png';
            $captcha_url = WPBC_PLUGIN_URL . '/js/captcha/tmp/' .$filename;
            $html  = '<input  autocomplete="off" type="text" class="captachinput" value="" name="captcha_input'.$bk_tp.'" id="captcha_input'.$bk_tp.'" />';
            $html .= '<img class="captcha_img"  id="captcha_img' . $bk_tp . '" alt="captcha" src="' . $captcha_url . '" />';
            $ref = substr($filename, 0, strrpos($filename, '.'));
            $html = '<input  autocomplete="off" type="hidden" name="wpdev_captcha_challenge_' . $bk_tp . '"  id="wpdev_captcha_challenge_' . $bk_tp . '" value="' . $ref . '" />'
                    . $html
                    . '<span id="captcha_msg'.$bk_tp.'" class="wpdev-help-message" ></span>';
            return $html;
        }
    }

    // Get default Booking resource
    function get_default_type() {
        if( $this->wpdev_bk_personal !== false ) {
            if (( isset( $_GET['booking_type'] )  )  && ($_GET['booking_type'] != '')) $bk_type = $_GET['booking_type'];
            else $bk_type = $this->wpdev_bk_personal->get_default_booking_resource_id();
        } else $bk_type =1;
        return $bk_type;
    }
    // </editor-fold>
    

    // <editor-fold defaultstate="collapsed" desc="   C L I E N T   S I D E     &    H O O K S ">
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   C L I E N T   S I D E     &    H O O K S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Get scripts for calendar activation
    function get_script_for_calendar($bk_type, $additional_bk_types, $my_selected_dates_without_calendar, $my_boook_count, $start_month_calendar = false ){

        $my_boook_type = (int) $bk_type;
        $start_script_code = "<script type='text/javascript'>";
        $start_script_code .= "  jQuery(document).ready( function(){";

        
        $skip_booking_id = '';  // Id of booking to skip in calendar
        if (isset($_GET['booking_hash'])) {
            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
            if ($my_booking_id_type !== false) {
                $skip_booking_id = $my_booking_id_type[0];  
            }
        }
        
        
        // Blank days //////////////////////////////////////////////////////////////////
        $start_script_code .= "  date_admin_blank[". $bk_type. "] = [];";
        $dates_and_time_for_admin_blank = $this->get_dates('admin_blank', $bk_type, $additional_bk_types);
        $dates_blank = $dates_and_time_for_admin_blank[0];
        $times_blank = $dates_and_time_for_admin_blank[1];
        $i=-1;
        foreach ($dates_blank as $date_blank) {
            $i++;

            $td_class =   ($date_blank[1]+0). "-" . ($date_blank[2]+0). "-". $date_blank[0];

            $start_script_code .= " if (typeof( date_admin_blank[". $bk_type. "][ '". $td_class . "' ] ) == 'undefined'){ ";
            $start_script_code .= " date_admin_blank[". $bk_type. "][ '". $td_class . "' ] = [];} ";

            $start_script_code .= "  date_admin_blank[". $bk_type. "][ '". $td_class . "' ][  date_admin_blank[".$bk_type."]['".$td_class."'].length  ] = [".
                    ($date_blank[1]+0).", ". ($date_blank[2]+0).", ". ($date_blank[0]+0).", ".
                    ($times_blank[$i][0]+0).", ". ($times_blank[$i][1]+0).", ". ($times_blank[$i][2]+0).
                    "];";
        }
        ////////////////////////////////////////////////////////////////////////////////

        $start_script_code .= "  date2approve[". $bk_type. "] = [];";
        
        $booking_is_days_always_available = get_bk_option( 'booking_is_days_always_available' );
//        if ( strpos($_SERVER['REQUEST_URI'],'page=wpbc-new') !== false ) { $booking_is_days_always_available = 'On'; }
        // if ( in_array( $bk_type, array( '12', '15', '17' ) ) ) $booking_is_days_always_available = 'On';     // Set  dates in calendar always available only  for specific resources with specific ID
        if ( $booking_is_days_always_available == 'On' ) {
           // No Booked days
            
        } else {
				if ( get_bk_option( 'booking_is_show_pending_days_as_available') == 'On' ){    							//FixIn: 8.3.2.2
                // if ( (class_exists('wpdev_bk_biz_l')) && (get_bk_option( 'booking_is_show_pending_days_as_available') == 'On') ){
                    $dates_to_approve = array();
                    $times_to_approve = array();            
                } else {
                    $dates_and_time_to_approve = $this->get_dates('0', $bk_type, $additional_bk_types, $skip_booking_id);
                    $dates_to_approve = $dates_and_time_to_approve[0];
                    $times_to_approve = $dates_and_time_to_approve[1];
                }
                $i=-1;
                foreach ($dates_to_approve as $date_to_approve) {
                    $i++;

                    $td_class =   ($date_to_approve[1]+0). "-" . ($date_to_approve[2]+0). "-". $date_to_approve[0];

                    $start_script_code .= " if (typeof( date2approve[". $bk_type. "][ '". $td_class . "' ] ) == 'undefined'){ ";
                    $start_script_code .= " date2approve[". $bk_type. "][ '". $td_class . "' ] = [];} ";

                    $start_script_code .= "  date2approve[". $bk_type. "][ '". $td_class . "' ][  date2approve[".$bk_type."]['".$td_class."'].length  ] = [".
                            ($date_to_approve[1]+0).", ". ($date_to_approve[2]+0).", ". ($date_to_approve[0]+0).", ".
                            ($times_to_approve[$i][0]+0).", ". ($times_to_approve[$i][1]+0).", ". ($times_to_approve[$i][2]+0).
                            "];";
                }
        }
        
        $start_script_code .= "  var date_approved_par = [];";
        $start_script_code .= apply_filters('wpdev_booking_availability_filter', '', $bk_type);

        if ( $booking_is_days_always_available == 'On' ) {
            // No Booked days          
            
        } else {
            
            $dates_and_time_to_approve = $this->get_dates('1', $my_boook_type, $additional_bk_types, $skip_booking_id);

            $dates_approved =   $dates_and_time_to_approve[0];
            $times_to_approve = $dates_and_time_to_approve[1];
            $i=-1;


            foreach ($dates_approved as $date_to_approve) {
                $i++;

                $td_class =   ($date_to_approve[1]+0)."-".($date_to_approve[2]+0)."-".($date_to_approve[0]);

                $start_script_code .= " if (typeof( date_approved_par[ '". $td_class . "' ] ) == 'undefined'){ ";
                $start_script_code .= " date_approved_par[ '". $td_class . "' ] = [];} ";

                $start_script_code.=" date_approved_par[ '".$td_class."' ][  date_approved_par['".$td_class."'].length  ] = [".
                        ($date_to_approve[1]+0).",".($date_to_approve[2]+0).",".($date_to_approve[0]+0).", ".
                        ($times_to_approve[$i][0]+0).", ". ($times_to_approve[$i][1]+0).", ". ($times_to_approve[$i][2]+0).
                        "];";
            }
        }
        
        // TODO: This code section have the impact to the performace in  BM / BL / MU versions ////////////////
        if ($my_selected_dates_without_calendar == '')
            $start_script_code .= apply_filters('wpdev_booking_show_rates_at_calendar', '', $bk_type);
        $start_script_code .= apply_filters('wpdev_booking_show_availability_at_calendar', '', $bk_type);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        
        if ($my_selected_dates_without_calendar == '') {
	        $start_script_code .= apply_filters( 'wpbc_booking_get_additional_info_to_dates', '', $bk_type );           //FixIn: 8.1.3.15
            $start_script_code .= "  init_datepick_cal('". $my_boook_type ."', date_approved_par, ".
                                        $my_boook_count ." , ". get_bk_option( 'booking_start_day_weeek' ) ;
            $start_js_month = ", false " ;
            if ($start_month_calendar !== false)
                if (is_array($start_month_calendar))
                    $start_js_month = ", [" . ($start_month_calendar[0]+0) . "," . ($start_month_calendar[1]+0) . "] ";

            $start_script_code .= $start_js_month .  "  );  ";
        }
        $start_script_code .= "}); </script>";

        return $start_script_code;
    }

    // Get code of the legend here
    function get_legend(){
        $my_result = '';
        if (get_bk_option( 'booking_is_show_legend' ) == 'On') {  

            $booking_legend_is_show_item_available    = get_bk_option( 'booking_legend_is_show_item_available');
            $booking_legend_text_for_item_available   = get_bk_option( 'booking_legend_text_for_item_available');

            $booking_legend_is_show_item_pending    = get_bk_option( 'booking_legend_is_show_item_pending');
            $booking_legend_text_for_item_pending   = get_bk_option( 'booking_legend_text_for_item_pending');

            $booking_legend_is_show_item_approved    = get_bk_option( 'booking_legend_is_show_item_approved');
            $booking_legend_text_for_item_approved   = get_bk_option( 'booking_legend_text_for_item_approved');

            $booking_legend_text_for_item_available = apply_bk_filter('wpdev_check_for_active_language',  $booking_legend_text_for_item_available );
            $booking_legend_text_for_item_pending   = apply_bk_filter('wpdev_check_for_active_language',  $booking_legend_text_for_item_pending );
            $booking_legend_text_for_item_approved  =  apply_bk_filter('wpdev_check_for_active_language', $booking_legend_text_for_item_approved );

            $text_for_day_cell = ( (0)?'&nbsp;':date('d') );
            
            $booking_legend_is_show_numbers = get_bk_option( 'booking_legend_is_show_numbers');     //FixIn:6.0.1.4
	        //FixIn: 8.1.3.8
            if ( $booking_legend_is_show_numbers != 'On' )
                $text_for_day_cell = '&nbsp;';
            else
                $text_for_day_cell = date('d');
            
            $my_result .= '<div class="block_hints datepick">';
            //$my_result .= '<div class="wpdev_hint_with_text"><div class="block_free datepick-days-cell datepick-unselectable" style="background-color: #fff;"><a>'.$text_for_day_cell.'</a></div><div class="block_text">- '. 'Unavailable' .'</div></div>';
            if ($booking_legend_is_show_item_available  == 'On') // __('Available' ,'booking')
                $my_result .= '<div class="wpdev_hint_with_text"><div class="block_free datepick-days-cell"><a>'.$text_for_day_cell.'</a></div><div class="block_text">- '. $booking_legend_text_for_item_available.'</div></div>';
            if ($booking_legend_is_show_item_approved  == 'On') // __('Booked' ,'booking') 
                $my_result .= '<div class="wpdev_hint_with_text"><div class="block_booked date_approved">'.$text_for_day_cell.'</div><div class="block_text">- '.$booking_legend_text_for_item_approved.'</div></div>';
            if ($booking_legend_is_show_item_pending  == 'On') // __('Pending' ,'booking') 
                $my_result .= '<div class="wpdev_hint_with_text"><div class="block_pending date2approve">'.$text_for_day_cell.'</div><div class="block_text">- '.$booking_legend_text_for_item_pending.'</div></div>';

            if ( class_exists('wpdev_bk_biz_s') ) {

                $booking_legend_is_show_item_partially    = get_bk_option( 'booking_legend_is_show_item_partially');
                $booking_legend_text_for_item_partially   = get_bk_option( 'booking_legend_text_for_item_partially');
                $booking_legend_text_for_item_partially  =  apply_bk_filter('wpdev_check_for_active_language', $booking_legend_text_for_item_partially );
                
                if ($booking_legend_is_show_item_partially  == 'On') { // __('Partially booked' ,'booking')                    
                    if ( get_bk_option( 'booking_range_selection_time_is_active' ) === 'On') {                        
                        $my_result .=  '<div class="wpdev_hint_with_text">' . 
                                                '<div class="block_check_in_out date_available date_approved check_in_time"  >
                                                    <div class="check-in-div"><div></div></div>
                                                    <div class="check-out-div"><div></div></div>
                                                    <em>'.$text_for_day_cell.'</em>
                                                </div>'.
                                                '<div class="block_text">- '. $booking_legend_text_for_item_partially .'</div>'.
                                        '</div>';                        
                    } else {
                        $my_result .= '<div class="wpdev_hint_with_text"><div class="block_time timespartly">'.$text_for_day_cell.'</div><div class="block_text">- '. $booking_legend_text_for_item_partially .'</div></div>';
                    }                        
                }
                
            }
            $my_result .= '</div><div class="wpdev_clear_hint"></div>';
        }
        return $my_result;
    }

    
    // Get HTML for the initilizing inline calendars
    function pre_get_calendar_html( $bk_type=1, $cal_count=1, $bk_otions=array() ){
        //SHORTCODE:
        /*
         * [booking type=56 form_type='standard' nummonths=4 
         *          options='{calendar months_num_in_row=2 width=568px cell_height=30px}']
         */
        
        $bk_otions = parse_calendar_options($bk_otions);
        /*  options:
            [months_num_in_row] => 2
            [width] => 284px
            [cell_height] => 40px
         */
        $width = $months_num_in_row = $cell_height = '';
        
        if (!empty($bk_otions)){
            
             if (isset($bk_otions['months_num_in_row'])) 
                 $months_num_in_row = $bk_otions['months_num_in_row'];
             
             if (isset($bk_otions['width'])) 
                 $width = 'width:'.$bk_otions['width'].';';
             
             if (isset($bk_otions['cell_height'])) 
                 $cell_height = $bk_otions['cell_height'];             
        }
        
        if (empty($width)){
            if (!empty($months_num_in_row))
                $width = 'width:'.($months_num_in_row*284).'px;';
            else
                $width = 'width:'.($cal_count*284).'px;';
        }
        
        if (!empty($cell_height))
             $style= '<style type="text/css" rel="stylesheet" >'.
                        '.hasDatepick .datepick-inline .datepick-title-row th,'.
                        '.hasDatepick .datepick-inline .datepick-days-cell{'.
                            ' height: '.$cell_height.' !important; '.
                        '}'.
                     '</style>';
        else $style= '';

        //FixIn: 8.2.1.27
        $booking_timeslot_day_bg_as_available = get_bk_option( 'booking_timeslot_day_bg_as_available' );

        $booking_timeslot_day_bg_as_available = ( $booking_timeslot_day_bg_as_available === 'On' ) ? ' wpbc_timeslot_day_bg_as_available' : '';

        $calendar  = $style. 
                     '<div class="bk_calendar_frame months_num_in_row_'.$months_num_in_row.' cal_month_num_'.$cal_count . $booking_timeslot_day_bg_as_available . '" style="'.$width.'">'.
                        '<div id="calendar_booking'.$bk_type.'">'.
                            __('Calendar is loading...' ,'booking').
                        '</div>'.
                     '</div>'.
                     '';
        
        $booking_is_show_powered_by_notice = get_bk_option( 'booking_is_show_powered_by_notice' );          
        if ( (!class_exists('wpdev_bk_personal')) && ($booking_is_show_powered_by_notice == 'On') )
            $calendar .= '<div style="font-size:9px;text-align:left;margin-top:3px;">Powered by <a style="font-size:9px;" href="https://wpbookingcalendar.com" target="_blank" title="Booking Calendar plugin for WordPress">Booking Calendar</a></div>';
                
        $calendar .= '<textarea id="date_booking'.$bk_type.'" name="date_booking'.$bk_type.'" autocomplete="off" style="display:none;"></textarea>';   // Calendar code
        
        $calendar  .= $this->get_legend(); 
        
        
        //FixIn: 7.0.1.24
        $is_booking_change_over_days_triangles = get_bk_option( 'booking_change_over_days_triangles' );
        if ( $is_booking_change_over_days_triangles == 'On' ) {
            $calendar = '<div class="wpbc_change_over_triangle">' . $calendar . '</div>';
        }
        
        return $calendar;
    }
    
    
    // Get form
    function get_booking_form( $my_boook_type ) {
        
        $my_form = apply_bk_filter( 'wpbc_get_free_booking_form' , $my_boook_type );

        return  $my_form;
        /*
        $booking_form_field_active1     = get_bk_option( 'booking_form_field_active1');
        $booking_form_field_required1   = get_bk_option( 'booking_form_field_required1');
        $booking_form_field_label1      = get_bk_option( 'booking_form_field_label1');
        $booking_form_field_label1 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label1 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label1 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label1', $booking_form_field_label1);

        $booking_form_field_active2     = get_bk_option( 'booking_form_field_active2');
        $booking_form_field_required2   = get_bk_option( 'booking_form_field_required2');
        $booking_form_field_label2      = get_bk_option( 'booking_form_field_label2');
        $booking_form_field_label2 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label2 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label2 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label2', $booking_form_field_label2);
        
        $booking_form_field_active3     = get_bk_option( 'booking_form_field_active3');
        $booking_form_field_required3   = get_bk_option( 'booking_form_field_required3');
        $booking_form_field_label3      = get_bk_option( 'booking_form_field_label3');
        $booking_form_field_label3 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label3 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label3 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label3', $booking_form_field_label3);
        
        $booking_form_field_active4     = get_bk_option( 'booking_form_field_active4');
        $booking_form_field_required4   = get_bk_option( 'booking_form_field_required4');
        $booking_form_field_label4      = get_bk_option( 'booking_form_field_label4');
        $booking_form_field_label4 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label4 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label4 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label4', $booking_form_field_label4);
        
        $booking_form_field_active5     = get_bk_option( 'booking_form_field_active5');
        $booking_form_field_required5   = get_bk_option( 'booking_form_field_required5');
        $booking_form_field_label5      = get_bk_option( 'booking_form_field_label5');
        $booking_form_field_label5 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label5 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label5 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label5', $booking_form_field_label5);
        
        $booking_form_field_active6     = get_bk_option( 'booking_form_field_active6');
        $booking_form_field_required6   = get_bk_option( 'booking_form_field_required6');
        $booking_form_field_label6      = get_bk_option( 'booking_form_field_label6');
        $booking_form_field_label6 = apply_bk_filter('wpdev_check_for_active_language', $booking_form_field_label6 );
        if (function_exists('icl_translate')) 
            $booking_form_field_label6 = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label6', $booking_form_field_label6);
        $booking_form_field_values6     = get_bk_option( 'booking_form_field_values6' );
        
        $my_form =  '[calendar]';
                //'<div style="text-align:left;">'.
                //'<p>'.__('First Name (required)' ,'booking').':<br />  <span class="wpdev-form-control-wrap name'.$my_boook_type.'"><input type="text" name="name'.$my_boook_type.'" value="" class="wpdev-validates-as-required" size="40" /></span> </p>'.
                    
        if ($booking_form_field_active1  != 'Off')
        $my_form.='  <div class="form-group">
                      <label for="name'.$my_boook_type.'" class="control-label">'.$booking_form_field_label1.(($booking_form_field_required1=='On')?'*':'').':</label>
                      <div class="controls">
                        <input type="text" name="name'.$my_boook_type.'" id="name'.$my_boook_type.'" class="input-xlarge'.(($booking_form_field_required1=='On')?' wpdev-validates-as-required ':'').'">
                      </div>
                    </div>';
        
        if ($booking_form_field_active2  != 'Off')
        $my_form.='  <div class="form-group">
                      <label for="secondname'.$my_boook_type.'" class="control-label">'.$booking_form_field_label2.(($booking_form_field_required2=='On')?'*':'').':</label>
                      <div class="controls">
                        <input type="text" name="secondname'.$my_boook_type.'" id="secondname'.$my_boook_type.'" class="input-xlarge'.(($booking_form_field_required2=='On')?' wpdev-validates-as-required ':'').'">
                      </div>
                    </div>';                    
                  
        if ($booking_form_field_active3  != 'Off')
        $my_form.='  <div class="form-group">
                      <label for="email'.$my_boook_type.'" class="control-label">'.$booking_form_field_label3.(($booking_form_field_required3=='On')?'*':'').':</label>
                      <div class="controls">
                        <input type="text" name="email'.$my_boook_type.'" id="email'.$my_boook_type.'" class="input-xlarge wpdev-validates-as-email'.(($booking_form_field_required3=='On')?' wpdev-validates-as-required ':'').'">
                      </div>
                    </div>';

        if ($booking_form_field_active6  == 'On') {
            $my_form.='  <div class="form-group">
                          <label for="visitors'.$my_boook_type.'" class="control-label">'.$booking_form_field_label6.(($booking_form_field_required6=='On')?'*':'').':</label>
                          <div class="controls">
                            <select name="visitors'.$my_boook_type.'" id="visitors'.$my_boook_type.'" class="input-xlarge'.(($booking_form_field_required6=='On')?' wpdev-validates-as-required ':'').'">';
            
            //$booking_form_field_values6 = explode("\n",$booking_form_field_values6);
            $booking_form_field_values6 = preg_split('/\r\n|\r|\n/', $booking_form_field_values6);
            foreach ($booking_form_field_values6 as $select_option) {
                $select_option = str_replace(array("'",'"'), '', $select_option);
                $my_form.='  <option value="'.$select_option.'">'.$select_option.'</option>';    
            }
            
            $my_form.='     </select>                            
                            <p class="help-block"></p>
                          </div>
                        </div>';                    
        }
        
        if ($booking_form_field_active4  != 'Off')
        $my_form.='  <div class="form-group">
                      <label for="phone'.$my_boook_type.'" class="control-label">'.$booking_form_field_label4.(($booking_form_field_required4=='On')?'*':'').':</label>
                      <div class="controls">
                        <input type="text" name="phone'.$my_boook_type.'" id="phone'.$my_boook_type.'" class="input-xlarge'.(($booking_form_field_required4=='On')?' wpdev-validates-as-required ':'').'">
                        <p class="help-block"></p>
                      </div>
                    </div>';                    
        
        if ($booking_form_field_active5  != 'Off')
        $my_form.='  <div class="form-group">
                      <label for="details" class="control-label">'.$booking_form_field_label5.(($booking_form_field_required5=='On')?'*':'').':</label>
                      <div class="controls">
                        <textarea rows="3" name="details'.$my_boook_type.'" id="details'.$my_boook_type.'" class="input-xlarge'.(($booking_form_field_required5=='On')?' wpdev-validates-as-required ':'').'"></textarea>
                      </div>
                    </div>';
        
        $my_form.='  <div class="form-group">[captcha]</div>';
                    
        $my_form.='  <button class="btn btn-default" type="button" onclick="mybooking_submit(this.form,'.$my_boook_type.',\''.wpbc_get_booking_locale().'\');" >'.__('Send' ,'booking').'</button> ';
                  
                //.'<p>'.__('Last Name (required)' ,'booking').':<br />  <span class="wpdev-form-control-wrap secondname'.$my_boook_type.'"><input type="text" name="secondname'.$my_boook_type.'" value="" class="wpdev-validates-as-required" size="40" /></span> </p>'.
                //'<p>'.__('Email (required)' ,'booking').':<br /> <span class="wpdev-form-control-wrap email'.$my_boook_type.'"><input type="text" name="email'.$my_boook_type.'" value="" class="wpdev-validates-as-email wpdev-validates-as-required" size="40" /></span> </p>'.
                //'<p>'.__('Phone' ,'booking').':<br />            <span class="wpdev-form-control-wrap phone'.$my_boook_type.'"><input type="text" name="phone'.$my_boook_type.'" value="" size="40" /></span> </p>'.
                //'<p>'.__('Details' ,'booking').':<br />          <span class="wpdev-form-control-wrap details'.$my_boook_type.'"><textarea name="details'.$my_boook_type.'" cols="40" rows="10"></textarea></span> </p>';
                
                //$my_form .=  '<p>[captcha]</p>';
                //$my_form .=  '<p><input type="button" value="'.__('Send' ,'booking').'" onclick="mybooking_submit(this.form,'.$my_boook_type.',\''.wpbc_get_booking_locale().'\');" /></p>
                //        </div>';

        return $my_form;
        */
    }

    // Get booking form
    function get_booking_form_action($my_boook_type=1,$my_boook_count=1, $my_booking_form = 'standard',  $my_selected_dates_without_calendar = '', $start_month_calendar = false, $bk_otions=array()) {

        $res = $this->add_booking_form_action($my_boook_type,$my_boook_count, 0, $my_booking_form , $my_selected_dates_without_calendar, $start_month_calendar, $bk_otions );
        return $res;
    }

    //Show booking form from action call - wpdev_bk_add_form
    function add_booking_form_action($bk_type =1, $cal_count =1, $is_echo = 1, $my_booking_form = 'standard', $my_selected_dates_without_calendar = '', $start_month_calendar = false, $bk_otions=array() ) {
        
        $additional_bk_types = array();
        if ( strpos($bk_type,';') !== false ) {
            $additional_bk_types = explode(';',$bk_type);
            $bk_type = $additional_bk_types[0];
        }

        $is_booking_resource_exist = apply_bk_filter('wpdev_is_booking_resource_exist',true, $bk_type, $is_echo );
        if (! $is_booking_resource_exist) {
            if ( $is_echo )     echo 'Booking resource does not exist.' . ' [ID='.$bk_type.']';
            return 'Booking resource does not exist.' . ' [ID='.$bk_type.']';
        }

        make_bk_action('check_multiuser_params_for_client_side', $bk_type );


        if (isset($_GET['booking_hash'])) {
            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
            if ($my_booking_id_type != false)
                if ($my_booking_id_type[1]=='') {
                    $my_result = __('Wrong booking hash in URL (probably expired)' ,'booking');
                    if ( $is_echo )            echo $my_result;
                    else                       return $my_result;
                    return;
                }
        }

        if ($bk_type == '') {
            $my_result = __('Booking resource type is not defined. This can be, when at the URL is wrong booking hash.' ,'booking');
            if ( $is_echo )            echo $my_result;
            else                       return $my_result;
            return;
        }

        
        //FixIn: 6.1.1.9             
        if ( isset( $_GET['booking_hash'] ) && (! isset($_GET['booking_pay'])) ) {  //Fix:2016-10-12
            $bk_edit_id = $my_booking_id_type[0];
            $bk_br_id   = $my_booking_id_type[1];    
            // Check situation when  we have editing "child booking resource",  so  need to  reupdate calendar and form  to have it for parent resource.
            if  (  ( function_exists( 'wpbc_is_this_child_resource') ) && ( wpbc_is_this_child_resource( $bk_br_id ) )  ){
                $bk_parent_br_id = wpbc_get_parent_resource( $bk_br_id );        

                $bk_type = $bk_parent_br_id;
            }
        }        
        // End: 6.1.1.9  
//debuge($bk_type);
         
        $start_script_code = $this->get_script_for_calendar($bk_type, $additional_bk_types, $my_selected_dates_without_calendar, $cal_count, $start_month_calendar );

        // Apply scripts for the conditions in the rnage days selections
        $start_script_code = apply_bk_filter('wpdev_bk_define_additional_js_options_for_bk_shortcode', $start_script_code, $bk_type, $bk_otions);  
        
        $my_result =  ' ' . $this->get__client_side_booking_content($bk_type, $my_booking_form, $my_selected_dates_without_calendar, $cal_count, $bk_otions ) . ' ' . $start_script_code ;

        
        $my_result = apply_filters('wpdev_booking_form', $my_result , $bk_type);        // Add DIV structure, where to show payment form  
        

        make_bk_action('finish_check_multiuser_params_for_client_side', $bk_type );

        if ( $is_echo )            echo $my_result;
        else                       return $my_result;
    }

    //Show only calendar from action call - wpdev_bk_add_calendar
    function add_calendar_action($bk_type =1, $cal_count =1, $is_echo = 1, $start_month_calendar = false, $bk_otions=array()) {

        $additional_bk_types = array();
        if ( strpos($bk_type,';') !== false ) {
            $additional_bk_types = explode(';',$bk_type);
            $bk_type = $additional_bk_types[0];
        }

        make_bk_action('check_multiuser_params_for_client_side', $bk_type );

        if (isset($_GET['booking_hash'])) {
            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
            if ($my_booking_id_type != false)
                if ($my_booking_id_type[1]=='') {
                    $my_result = __('Wrong booking hash in URL (probably expired)' ,'booking');
                    if ( $is_echo )            echo $my_result;
                    else                       return $my_result;
                    return;
                }
        }

        $start_script_code = $this->get_script_for_calendar($bk_type, $additional_bk_types, '' , $cal_count, $start_month_calendar );

        $my_result = '<div style="clear:both;height:10px;"></div>' . $this->pre_get_calendar_html( $bk_type, $cal_count, $bk_otions );

        // $my_result .= $this->get_legend();                                  // Get Legend code here

        $my_result .=   ' ' . $start_script_code ;

        $my_result = apply_filters('wpdev_booking_calendar', $my_result , $bk_type);

        
        $booking_form_is_using_bs_css = get_bk_option( 'booking_form_is_using_bs_css' );
        $my_result = '<span ' . (($booking_form_is_using_bs_css == 'On') ? 'class="wpdevelop"' : '') . '>' . $my_result . '</span>';

        make_bk_action('finish_check_multiuser_params_for_client_side', $bk_type );

        if ( $is_echo )            echo $my_result;
        else                       return $my_result;
    }

    // Get content at client side of  C A L E N D A R
    function get__client_side_booking_content($my_boook_type = 1 , $my_booking_form = 'standard', $my_selected_dates_without_calendar = '', $cal_count = 1, $bk_otions = array() ) {

        $nl = '<div style="clear:both;height:10px;"></div>';                                                            // New line
        if ($my_selected_dates_without_calendar=='') {
            $calendar = $this->pre_get_calendar_html( $my_boook_type, $cal_count, $bk_otions );
        } else {
            $calendar = '<textarea rows="3" cols="50" id="date_booking'.$my_boook_type.'" name="date_booking'.$my_boook_type.'"  autocomplete="off" style="display:none;">'.$my_selected_dates_without_calendar.'</textarea>';   // Calendar code
        }
        // $calendar  .= $this->get_legend();                                  // Get Legend code here


	    //FixIn: 8.2.1.1
        $form = '<a name="bklnk'.$my_boook_type.'" id="bklnk'.$my_boook_type.'"></a><div id="booking_form_div'.$my_boook_type.'" class="booking_form_div">';
        //FixIn:6.0.1.5
        $custom_params = array();
        if (! empty($bk_otions)) {
            $param ='\s*([name|value]+)=[\'"]{1}([^\'"]+)[\'"]{1}\s*'; // Find all possible options
            $pattern_to_search='%\s*{([^\s]+)' . $param . $param .'}\s*[,]?\s*%';
            preg_match_all($pattern_to_search, $bk_otions, $matches, PREG_SET_ORDER);
            //debuge($matches);  
            foreach ( $matches as $matche_value ) {
                if ( $matche_value[1] == 'parameter' ) {
                    $custom_params[ $matche_value[3] ]= $matche_value[5];
                }
            }
        }
        //FixIn:6.0.1.5

	    if ( $this->wpdev_bk_personal !== false ) {
		    $form .= $this->wpdev_bk_personal->get_booking_form( $my_boook_type, $my_booking_form, $custom_params );
	    } else {
		    $form .= $this->get_booking_form( $my_boook_type );
	    }

        // Insert calendar into form
        if ( strpos($form, '[calendar]') !== false )  $form = str_replace('[calendar]', $calendar ,$form);
        else                                          $form = '<div class="booking_form_div">' . $calendar . '</div>' . $nl . $form ;

        // Replace additional calendars like [calendar id=9] to  HTML and JS code
        $form = apply_bk_filter( 'wpdev_check_for_additional_calendars_in_form'
                                                                            , $form
                                                                            , $my_boook_type 
                                                                            , array( 
                                                                                    'booking_form' => $my_booking_form , 
                                                                                    'selected_dates' => $my_selected_dates_without_calendar , 
                                                                                    'cal_count' => $cal_count , 
                                                                                    'otions' => $bk_otions     
                                                                                    )  
                                    );

        if ( strpos($form, '[captcha]') !== false ) {
            $captcha = $this->createCapthaContent($my_boook_type);
            $form =str_replace('[captcha]', $captcha ,$form);
        }
        
        // Set additional "Check in/out" times, if activated to  use change-over days!
        $form = apply_filters('wpdev_booking_form_content', $form , $my_boook_type);        
        
        
        
        // Add booking type field
        $form      .= '<input id="bk_type'.$my_boook_type.'" name="bk_type'.$my_boook_type.'" class="" type="hidden" value="'.$my_boook_type.'" /></div>';        
        $submitting = '<div id="submiting'.$my_boook_type.'"></div><div class="form_bk_messages" id="form_bk_messages'.$my_boook_type.'" ></div>';
        
        //Params: $action = -1, $name = "_wpnonce", $referer = true , $echo = true
        $wpbc_nonce  = wp_nonce_field('INSERT_INTO_TABLE',  ("wpbc_nonce" . $my_boook_type) ,  true , false );
        $wpbc_nonce .= wp_nonce_field('CALCULATE_THE_COST', ("wpbc_nonceCALCULATE_THE_COST" . $my_boook_type) ,  true , false );
        
        
        $res = $form . $submitting . $wpbc_nonce;

        $my_random_id = time() * rand(0,1000);
        $my_random_id = 'form_id'. $my_random_id;        
        
        $booking_form_is_using_bs_css = get_bk_option( 'booking_form_is_using_bs_css');
        $booking_form_format_type     = get_bk_option( 'booking_form_format_type');
        
        $return_form = '<div id="'.$my_random_id.'" '.(($booking_form_is_using_bs_css=='On')?'class="wpdevelop"':'').'>'.
                         '<form  id="booking_form'.$my_boook_type.'"   class="booking_form '.$booking_form_format_type.'" method="post" action="">'.
                           '<div id="ajax_respond_insert'.$my_boook_type.'" class="ajax_respond_insert" style="display:none;"></div>'.
                           $res.
                         '</form></div>';
        
        $return_form .= '<div id="booking_form_garbage'.$my_boook_type.'" class="booking_form_garbage"></div>';
        
        if ($my_selected_dates_without_calendar == '' ) {
            // Check according already shown Booking Calendar  and set do not visible of it
            $return_form .= '<script type="text/javascript">
                                jQuery(document).ready( function(){
                                    jQuery(".widget_wpdev_booking .booking_form.form-horizontal").removeClass("form-horizontal");
                                    var visible_booking_id_on_page_num = visible_booking_id_on_page.length;
                                    if (visible_booking_id_on_page_num !== null ) {
                                        for (var i=0;i< visible_booking_id_on_page_num ;i++){
                                          if ( visible_booking_id_on_page[i]=="booking_form_div'.$my_boook_type.'" ) {
                                              document.getElementById("'.$my_random_id.'").innerHTML = "<span style=\'color:#A00;font-size:10px;\'>'.                                                      
                                                       sprintf( esc_js( __('%sWarning! Booking calendar for this booking resource are already at the page, please check more about this issue at %sthis page%s' ,'booking') )
                                                                , ''
                                                                , ''
                                                                , ': https://wpbookingcalendar.com/faq/why-the-booking-calendar-widget-not-show-on-page/'                                                            
                                                        ) 
                                                .'</span>";                                                                                                  
                                              jQuery("#'.$my_random_id.'").animate( {opacity: 1}, 10000 ).fadeOut(5000);
                                              return;
                                          }
                                        }
                                        visible_booking_id_on_page[ visible_booking_id_on_page_num ]="booking_form_div'.$my_boook_type.'";
                                    }
                                });
                            </script>';
        } else {
            if (1)                                                              //FixIn:6.1.1.16	//FixIn: 8.2.1.13
            $return_form .= '<script type="text/javascript">
                                jQuery(document).ready( function(){            
                                    if(typeof( showCostHintInsideBkForm ) == "function") {
                                        showCostHintInsideBkForm('.$my_boook_type.');
                                    }
                                });
                            </script>';
        }

        $is_use_auto_fill_for_logged = get_bk_option( 'booking_is_use_autofill_4_logged_user' ) ;


        if (! isset($_GET['booking_hash']))
            if ($is_use_auto_fill_for_logged == 'On') {

                $curr_user = wp_get_current_user();
                if ( $curr_user->ID > 0 ) {
//$user_nick_name =get_user_meta($curr_user->ID, 'nickname')[0];
//debuge( $user_nick_name );
	                //FixIn: 8.7.1.5
					$user_nick_name = get_user_meta( $curr_user->ID, 'nickname' );
					if ( empty( $user_nick_name ) ) {
						$user_nick_name = '';
					} else {
						$user_nick_name = $user_nick_name[0];
					}
                    $return_form .= '<script type="text/javascript">
                                jQuery(document).ready( function(){
                                    var bk_af_submit_form = document.getElementById( "booking_form'.$my_boook_type.'" );
                                    var bk_af_count = bk_af_submit_form.elements.length;
                                    var bk_af_element;
                                    var bk_af_reg;
                                    for (var bk_af_i=0; bk_af_i<bk_af_count; bk_af_i++)   {
                                        bk_af_element = bk_af_submit_form.elements[bk_af_i];
                                        if (
                                            (bk_af_element.type == "text") &&
                                            (bk_af_element.type !=="button") &&
                                            (bk_af_element.type !=="hidden") &&
                                            (bk_af_element.name !== ("date_booking'.$my_boook_type.'" ) )
                                           ) {
                                                // NickName	//FixIn: 8.6.1.2
                                                bk_af_reg = /^([A-Za-z0-9_\-\.])*(nickname){1}([A-Za-z0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "' . str_replace( "'", '',  $user_nick_name ) . '";                                                        
                                                // Second Name
                                                bk_af_reg = /^([A-Za-z0-9_\-\.])*(last|second){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "'.str_replace("'",'',$curr_user->last_name).'";
                                                // First Name
                                                bk_af_reg = /^name([0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "'.str_replace("'",'',$curr_user->first_name).'";
                                                bk_af_reg = /^([A-Za-z0-9_\-\.])*(first|my){1}([_\-\.])?name([A-Za-z0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "'.str_replace("'",'',$curr_user->first_name).'";
                                                // Email
                                                bk_af_reg = /^(e)?([_\-\.])?mail([0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "'.str_replace("'",'',$curr_user->user_email).'";
							// Phone
                            bk_af_reg = /^([A-Za-z0-9_\-\.])*(phone|fone){1}([A-Za-z0-9_\-\.])*$/;
                            if(bk_af_reg.test(bk_af_element.name) != false)
                                if (bk_af_element.value == "" )
                                    bk_af_element.value  = "'.str_replace("'",'',$curr_user->phone_number).'";
                            // NB Enfants
                            bk_af_reg = /^(e)?([_\-\.])?nb_enfant([0-9_\-\.])*$/;
                            if(bk_af_reg.test(bk_af_element.name) != false)
                                if (bk_af_element.value == "" )
                                    bk_af_element.value  = "'.str_replace("'",'',$curr_user->nb_enfant).'";
                                                                                            
                                                // URL
                                                bk_af_reg = /^([A-Za-z0-9_\-\.])*(URL|site|web|WEB){1}([A-Za-z0-9_\-\.])*$/;
                                                if(bk_af_reg.test(bk_af_element.name) != false)
                                                    if (bk_af_element.value == "" )
                                                        bk_af_element.value  = "'.str_replace("'",'',$curr_user->user_url).'";
                                           }
                                    }
                                });
                                </script>';
                }
             }

        return $return_form ;
    }
    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc="   S H O R T    C O D E S ">
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   S H O R T    C O D E S
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//FixIn: 8.7.11.13
	function booking_test_speed_shortcode( $attr ) {

    	 echo '<h4>Booking Calendar Test</h4>';

    	 $datesArray = Array(
	            '2021-09-10'
		     ,  '2021-09-11'
		     ,  '2021-09-12'
		     ,  '2021-09-13'
		     ,  '2021-09-14'
		     ,  '2021-09-15'
		     ,  '2021-09-16'
		     ,  '2021-09-17'
		     ,  '2021-09-18'
		) ;
    	 for( $i = 0; $i < 1 ; $i++) {
$result = wpbc_api_is_dates_booked( $datesArray, $resource_id = 13 );
//debuge((int) $result );
    	 }

    	debuge_speed('(int)');
    	 echo '<hr/>';
    	 echo '<hr/>';
	}

	//FixIn: 8.1.3.5
	/** Listing customners bookings in timeline view
	 *
	 * @param $attr	- The same parameters as for bookingtimeline shortcode (function)
	 *
	 * @return mixed|string|void
	 */
	function bookingcustomerlisting_shortcode( $attr ){

		//FixIn: 8.4.5.11
		if (! is_array($attr)) {
			$attr = array();
		}
		if ( ( isset( $_GET['booking_hash'] ) ) || ( isset( $attr['booking_hash'] ) ) ) {


			if ( isset( $_GET['booking_hash'] ) ) {
				$my_booking_id_type = apply_bk_filter( 'wpdev_booking_get_hash_to_id', false, $_GET['booking_hash'] );

				$attr['booking_hash'] = $_GET['booking_hash'];
			} else {
				$my_booking_id_type = apply_bk_filter( 'wpdev_booking_get_hash_to_id', false, $attr['booking_hash'] );
			}

			if ( $my_booking_id_type !== false ) {

				if ( ! isset( $attr['type' ] ) ) {																		// 8.1.3.5.2

					$br_list = wpbc_get_all_booking_resources_list();
					$br_list = array_keys( $br_list );
					$br_list = implode(',',$br_list);
					$attr['type' ] = $br_list;		//wpbc_get_default_resource();
				}
				if ( ! isset( $attr['view_days_num' ] ) ) {
					$attr['view_days_num' ] = 30;
				}
				if ( ! isset( $attr['scroll_start_date' ] ) ) {
					$attr['scroll_start_date' ] = '';
				}
				if ( ! isset( $attr['scroll_day' ] ) ) {
					$attr['scroll_day' ] = 0;
				}
				if ( ! isset( $attr['scroll_month' ] ) ) {
					$attr['scroll_month' ] = 0;
				}
				if ( ! isset( $attr['header_title' ] ) ) {
					$attr['header_title' ] = __( 'My bookings' , 'booking');
				}

				$timeline_results = $this->bookingtimeline_shortcode( $attr );

				return $timeline_results ;

			} else {
				return __( 'Wrong booking hash in URL. Probably hash is expired.', 'booking' );
			}

		} else {
			return __( 'You do not set any parameters for booking editing', 'booking' )
			       . ' <br/><em>'
			       . sprintf( __( 'Please check more about configuration at  %sthis page%s', 'booking' ), '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/" target="_blank">', '</a>.' )
			       . '</em>';
		}
	}


    /**
	 * TimeLine shortcode
     * 
     * @param type $attr
     * @return type
     * 
     * Shortcodes exmaples:
     * 
     * 
** Matrix:
     * 1 Month View Mode:
[bookingtimeline type="3,4,1,5,6,7,8,9,2,10,11,12,14" view_days_num=30 scroll_start_date="" scroll_month=0 header_title='All Bookings']
     * 2 Months View Mode:
[bookingtimeline type="1,5,6,7,8,9,2,10,11,12,3,4,14" view_days_num=60 scroll_start_date="" scroll_month=-1 header_title='All Bookings']
     * 1 Week View Mode:
[bookingtimeline type="3,4" view_days_num=7 scroll_start_date="" scroll_day=-7 header_title='All Bookings']
     * 1 Day View Mode:
[bookingtimeline type="3,4" view_days_num=1 scroll_start_date="" scroll_day=0 header_title='All Bookings']

** Single:
     * 1 Month  View Mode:
[bookingtimeline type="4" view_days_num=30 scroll_start_date="" scroll_day=-15 scroll_month=0 header_title='All Bookings']
     * 3 Months View Mode:
[bookingtimeline type="4" view_days_num=90 scroll_start_date="" scroll_day=-30]
     * 1 Year View Mode:
[bookingtimeline type="4" view_days_num=365 scroll_start_date="" scroll_month=-3]


     */
    function bookingtimeline_shortcode($attr) {

    	//FixIn: 8.6.1.13
		$timeline_results = bookingflextimeline_shortcode($attr);
		return $timeline_results;
    }
    
    // Replace MARK at post with content at client side   -----    [booking nummonths='1' type='1']
    function booking_shortcode($attr) {
//debuge($attr);

        if (isset($_GET['booking_hash'])) return __('You need to use special shortcode [bookingedit] for booking editing.' ,'booking');

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $my_boook_count = get_bk_option( 'booking_client_cal_count' );
        $my_boook_type = 1;
        $my_booking_form = 'standard';
        $start_month_calendar = false;
        $bk_otions = array();

        if ( isset( $attr['nummonths'] ) ) { $my_boook_count = $attr['nummonths'];  }
        if ( isset( $attr['type'] ) )      { $my_boook_type = $attr['type'];        }
        
//        if (isset($_GET['resource_id'])) {                                                // Get ID of booking resource from  URL parameter ?resource_id=3
//            $my_boook_type = intval( $_GET['resource_id'] );       
//        }
// $custom_field_in_post = intval( get_post_meta( get_the_ID() , 'resource_id' , true ) );  // Get ID of booking resource from  custom  field with  name  'resource_id=4'
// if (! empty( $custom_field_in_post )){
//      $my_boook_type = $custom_field_in_post;       
// }
        
        if ( isset( $attr['form_type'] ) ) { $my_booking_form = $attr['form_type']; }

        if ( isset( $attr['agregate'] )  && (! empty( $attr['agregate'] )) ) {
            $additional_bk_types = $attr['agregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }
        if ( isset( $attr['aggregate'] )  && (! empty( $attr['aggregate'] )) ) {
            $additional_bk_types = $attr['aggregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }


        if ( isset( $attr['startmonth'] ) ) { // Set start month of calendar, fomrat: '2011-1'

            $start_month_calendar = explode( '-', $attr['startmonth'] );
            if ( (is_array($start_month_calendar))  && ( count($start_month_calendar) > 1) ) { }
            else $start_month_calendar = false;

        }

        if ( isset( $attr['options'] ) ) { $bk_otions = $attr['options']; }
        
        $res = $this->add_booking_form_action($my_boook_type,$my_boook_count, 0 , $my_booking_form , '', $start_month_calendar, $bk_otions );

        return $res;
    }

    // Replace MARK at post with content at client side   -----    [booking nummonths='1' type='1']
    function booking_calendar_only_shortcode($attr) {
        
        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $my_boook_count = get_bk_option( 'booking_client_cal_count' );
        $my_boook_type = 1;
        $start_month_calendar = false;
        $bk_otions = array();
        if ( isset( $attr['nummonths'] ) ) { $my_boook_count = $attr['nummonths']; }
        if ( isset( $attr['type'] ) )      { $my_boook_type = $attr['type'];       }
        if ( isset( $attr['agregate'] )  && (! empty( $attr['agregate'] )) ) {
            $additional_bk_types = $attr['agregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }
        if ( isset( $attr['aggregate'] )  && (! empty( $attr['aggregate'] )) ) {                                        //FixIn: 8.3.3.8
            $additional_bk_types = $attr['aggregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }

        if ( isset( $attr['startmonth'] ) ) { // Set start month of calendar, fomrat: '2011-1'
            $start_month_calendar = explode( '-', $attr['startmonth'] );
            if ( (is_array($start_month_calendar))  && ( count($start_month_calendar) > 1) ) { }
            else $start_month_calendar = false;
        }
        
        if ( isset( $attr['options'] ) ) { $bk_otions = $attr['options']; }
        $res = $this->add_calendar_action($my_boook_type,$my_boook_count, 0, $start_month_calendar, $bk_otions  );


        $start_script_code = "<div id='calendar_booking_unselectable".$my_boook_type."'></div>";
	    return "<div class='wpbc_only_calendar'>" . $start_script_code . $res . '</div>';                               //FixIn: 8.0.1.2
    }

    // Show only booking form, with already selected dates
    function bookingform_shortcode($attr) {

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $my_boook_type = 1;
        $my_booking_form = 'standard';
        $my_boook_count = 1;
        $my_selected_dates_without_calendar = '';

        if ( isset( $attr['type'] ) )           { $my_boook_type = $attr['type'];                                }
        if ( isset( $attr['form_type'] ) )      { $my_booking_form = $attr['form_type'];                         }
        if ( isset( $attr['selected_dates'] ) ) { $my_selected_dates_without_calendar = $attr['selected_dates']; }  //$my_selected_dates_without_calendar = '20.08.2010, 29.08.2010';

        $res = $this->add_booking_form_action($my_boook_type,$my_boook_count, 0 , $my_booking_form, $my_selected_dates_without_calendar, false );
        
        $res .= "<script type='text/javascript'> ";                             //FixIn:6.1.1.16
        $res .= "jQuery(document).ready( function(){";                          
        $res .= apply_filters('wpdev_booking_show_availability_at_calendar', '', $my_boook_type);
        $res .= "}); ";
        $res .= "</script>";
                
        return $res;
    }

    // Show booking form for editing
    function bookingedit_shortcode($attr) {
        
        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache

	    if ( isset( $_GET['wpbc_hash'] ) ) {

	    	if ( function_exists( 'wpbc_parse_one_way_hash' ) ) {

			    $one_way_hash_response = wpbc_parse_one_way_hash( $_GET['wpbc_hash'] );

			    return $one_way_hash_response;
		    }
	    }

        $my_boook_count = get_bk_option( 'booking_client_cal_count' );
        $my_boook_type = 1;
        $my_booking_form = 'standard';
        $bk_otions = array();
        if ( isset( $attr['nummonths'] ) )   { $my_boook_count = $attr['nummonths'];  }
        if ( isset( $attr['type'] ) )        { $my_boook_type = $attr['type'];        }
        if ( isset( $attr['form_type'] ) )   { $my_booking_form = $attr['form_type']; }
        if ( isset( $attr['agregate'] )  && (! empty( $attr['agregate'] )) ) {  //FixIn:7.0.1.26
            $additional_bk_types = $attr['agregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }
        if ( isset( $attr['aggregate'] )  && (! empty( $attr['aggregate'] )) ) {
            $additional_bk_types = $attr['aggregate'];
            $my_boook_type .= ';'.$additional_bk_types;
        }
		if ( isset( $attr['options'] ) ) { $bk_otions = $attr['options']; }


        if (isset($_GET['booking_hash'])) {
            $my_booking_id_type = apply_bk_filter('wpdev_booking_get_hash_to_id',false, $_GET['booking_hash'] );
            if ($my_booking_id_type !== false) {
	            $my_edited_bk_id = $my_booking_id_type[0];
	            $my_boook_type   = $my_booking_id_type[1];
                if ($my_boook_type == '') return __('Wrong booking hash in URL. Probably hash is expired.' ,'booking');
            } else {
                return __('Wrong booking hash in URL. Probably hash is expired.' ,'booking');
            }

        } else {
            return __('You do not set any parameters for booking editing' ,'booking')
                    . ' <br/><em>' 
                        . sprintf( __('Please check more about configuration at  %sthis page%s' ,'booking')
									, '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/" target="_blank">' , '</a>.')
                    . '</em>';
        }


        $res = $this->add_booking_form_action($my_boook_type,$my_boook_count, 0 , $my_booking_form, '', false, $bk_otions );

        if (isset($_GET['booking_pay'])) {
            // Payment form
            $res .= apply_bk_filter('wpdev_get_payment_form',$my_edited_bk_id, $my_boook_type );
        }

        return $res;
    }

    // Search form
    function bookingsearch_shortcode($attr) {

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $search_form = apply_bk_filter('wpdev_get_booking_search_form','', $attr );

        return $search_form ;
    }

    // Search Results form
    function bookingsearchresults_shortcode($attr) {

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $search_results = apply_bk_filter('wpdev_get_booking_search_results','', $attr );

        return $search_results ;
    }

    // Select Booking form using the selectbox
    function bookingselect_shortcode($attr) {

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $search_form = apply_bk_filter('wpdev_get_booking_select_form','', $attr );

        return $search_form ;
    }

    // Select Booking form using the selectbox
    function bookingresource_shortcode($attr) {

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache
        
        $search_form = apply_bk_filter('wpbc_booking_resource_info','', $attr );

        return $search_form ;
    }
    
    
    // </editor-fold>
}