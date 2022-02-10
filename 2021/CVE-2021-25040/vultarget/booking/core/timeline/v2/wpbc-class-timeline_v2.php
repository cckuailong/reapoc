<?php /**
 * @version 1.1
 * @package Booking Calendar
 * @category Timeline for Admin Panel
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-01-18
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_TimelineFlex {
     
    
    public $bookings;       // Booking objects from external function
    public $booking_types;  // Resources objects from external function
        
    public $dates_array;    // Dates for Timeline format
    public $time_array_new; // Times for Timeline format
        
    public $request_args;   // Parsed paramaters

    private $is_frontend;   // Client ot Admin  sides.
    
    public $timeline_titles;
    
    private $week_days_titles;
    private $current_user_id;
    
    private $html_client_id;        // ID of border element at  client side.
    public $options;                                                            //FixIn:7.0.1.50

	private $data_in_previous_cell;	//FixIn: 8.5.2.6

    public function __construct(){// $bookings, $booking_types ) {

    	$this->reset_data_in_previous_cell();

        $this->options = array();                                               //FixIn:7.0.1.50
        
        $this->html_client_id = false;
        
        $this->current_user_id = 0;
        
        $this->is_frontend = false;

	    //FixIn: 8.1.3.31
        $calendar_overview_start_time = get_bk_option( 'booking_calendar_overview_start_time' );
        $calendar_overview_end_time   = get_bk_option( 'booking_calendar_overview_end_time' );
		$hours_limit = ( empty( $calendar_overview_start_time ) ? '0' : $calendar_overview_start_time )
					   . ','
                       . ( empty( $calendar_overview_end_time ) ? '24' : $calendar_overview_end_time );
        $this->request_args = array(                                     
                                      'wh_booking_type' => '1'            
                                    , 'is_matrix' => false
                                    , 'view_days_num' => '90'
                                    , 'scroll_start_date' => ''
                                    , 'scroll_day' => 0
                                    , 'scroll_month' => 0
                                    , 'wh_trash' => ''
                                    , 'limit_hours' => $hours_limit        									// '0,24'      //FixIn: 7.0.1.14  if ( ! ( ( $tt >= $start_hour_for_1day_view ) && ( $tt <= $end_hour_for_1day_view ) ) ) continue;
                                    , 'only_booked_resources' => ( isset( $_REQUEST['only_booked_resources'] ) ) ? 1 : 0              //FixIn: 7.0.1.51
                                    , 'booking_hash' => ( isset( $_REQUEST['booking_hash'] ) ) ? $_REQUEST['booking_hash'] : ''              //FixIn: 8.1.3.5

        ); 

        $this->timeline_titles = array( 
                                    'header_column1' => __('Resources', 'booking')
                                    , 'header_column2' => __('Dates', 'booking')
                                    , 'header_title' => __('Bookings', 'booking')
                                );
        
        
        $this->week_days_titles = array(
                                        'full' => array( 
                                              1 => __( 'Monday', 'booking' )
                                            , 2 => __( 'Tuesday', 'booking' )
                                            , 3 => __( 'Wednesday', 'booking' )
                                            , 4 => __( 'Thursday', 'booking' )
                                            , 5 => __( 'Friday', 'booking' )
                                            , 6 => __( 'Saturday', 'booking' )
                                            , 7 => __( 'Sunday', 'booking' ) 
                                            )
                                        , '3' => array(                         //FixIn: 7.0.1.11
                                              1 =>  __( 'Mon', 'booking' )
                                            , 2 =>  __( 'Tue', 'booking' )
                                            , 3 =>  __( 'Wed', 'booking' )
                                            , 4 =>  __( 'Thu', 'booking' )
                                            , 5 =>  __( 'Fri', 'booking' )
                                            , 6 =>  __( 'Sat', 'booking' )
                                            , 7 =>  __( 'Sun', 'booking' )
                                            )
                                        , '1' => array(                         //FixIn: 7.0.1.11						//FixIn: 8.7.7.14
                                              1 => mb_substr( __( 'Mon', 'booking' ), 0, -1 )
                                            , 2 => mb_substr( __( 'Tue', 'booking' ), 0, -1 )
                                            , 3 => mb_substr( __( 'Wed', 'booking' ), 0, -1 )
                                            , 4 => mb_substr( __( 'Thu', 'booking' ), 0, -1 )
                                            , 5 => mb_substr( __( 'Fri', 'booking' ), 0, -1 )
                                            , 6 => mb_substr( __( 'Sat', 'booking' ), 0, -1 )
                                            , 7 => mb_substr( __( 'Sun', 'booking' ), 0, -1 )
                                            )
                                        , 'short' => array(
                                              1 => mb_substr( __( 'Mon', 'booking' ), 0, 1 )
                                            , 2 => mb_substr( __( 'Tue', 'booking' ), 0, 1 )
                                            , 3 => mb_substr( __( 'Wed', 'booking' ), 0, 1 )
                                            , 4 => mb_substr( __( 'Thu', 'booking' ), 0, 1 )
                                            , 5 => mb_substr( __( 'Fri', 'booking' ), 0, 1 )
                                            , 6 => mb_substr( __( 'Sat', 'booking' ), 0, 1 )
                                            , 7 => mb_substr( __( 'Sun', 'booking' ), 0, 1 )
                                            )
                                    );
        
        
    }


	/**
	 * Rezet data in previos cell
	 */
    private function reset_data_in_previous_cell(){

		$this->data_in_previous_cell = array(
									'bookings_in_cell' => array(),
									'previous_month'   => ''
								);

    }

    ////////////////////////////////////////////////////////////////////////////

    /**
	 * Init Timeline From page shortcode
     * 
     * @param array $attr = array(                                     
                                      'wh_booking_type' => ''            
                                    , 'is_matrix' => false
                                    , 'view_days_num' => '30'
                                    , 'scroll_start_date' => ''
                                    , 'scroll_day' => 0
                                    , 'scroll_month' => 0
                                );        
     */
    public function client_init( $attr ) {

        $this->is_frontend = true;

        //FixIn:7.0.1.50
        if ( isset( $attr['options'] ) ) {

            $bk_otions = $attr['options'];
            $custom_params = array();
            if (! empty($bk_otions)) {
                $param ='\s*([^\s]+)=[\'"]{1}([^\'"]+)[\'"]{1}\s*';      // Find all possible options
                $pattern_to_search='%\s*{([^\s]+)' . $param .'}\s*[,]?\s*%';
                preg_match_all($pattern_to_search, $bk_otions, $matches, PREG_SET_ORDER);
                //debuge($matches);
                /**
	 * [bookingtimeline  ... options='{resource_link 3="http://beta/resource-apartment3-id3/"},{resource_link 4="http://beta/resource-3-id4/"}' ... ]
                    [0] => {resource_link 3="http://beta/resource-apartment3-id3/"},
                    [1] => resource_link                                        // Name
                    [2] => 3                                                    // ID
                    [3] => http://beta/resource-apartment3-id3/                 // Value
                 */
                foreach ( $matches as $matche_value ) {

                    if ( ! isset( $this->options[ $matche_value[1] ] ) ) {
                        $this->options[ $matche_value[1] ] = array();
                    }
                    $this->options[ $matche_value[1] ][ $matche_value[2] ] = $matche_value[3];
                }
            }

//debuge($this->options);
        }
        //FixIn:7.0.1.50


        //Ovverride some parameters
        if ( isset( $attr['type'] ) ) {
            $attr['wh_booking_type'] = $attr['type'];                           //Instead of 'wh_booking_type' paramter  in shortcode is used 'type' parameter
        }

        // Get paramaters from shortcode paramaters
        $this->define_request_view_params_from_params( $attr );

        if ( ! $this->request_args['is_matrix'] )
            $this->timeline_titles['header_column1'] = '';

        //Override any possible titles from shortcode paramaters
        $this->timeline_titles = wp_parse_args( $attr, $this->timeline_titles );

        // Get clean parameters to  request booking data
        $args = $this->wpbc_get_clean_paramas_from_request_for_timeline();


		//FixIn: 8.1.3.5
		/**	Client - Page first load
		 *
		 * If provided valid request_args['booking_hash']
		 *		- Firstly  defined in constructor in $_REQUEST['booking_hash']
		 * 		- or overwrited in 		define_request_view_params_from_params		from  parameters in shortcode 'booking_hash'
		 * then check, if exist booking for this hash.
		 * If exist, get Email of this booking,  and
		 * filter getting all  other bookings by email keyword.
		 * Addtionly set param ['only_booked_resources'] for showing only booking resources with  exist bookings.
		 */
		if ( isset( $this->request_args['booking_hash'] ) ) {

			// Get booking details by HASH,  and then  return Email (or other data of booking,  or false if error
			$booking_details_email = wpbc_check_hash_get_booking_details( $this->request_args['booking_hash'] , 'email' );

			if ( ! empty( $booking_details_email ) ) {

				// Do  not show booking resources with  no bookings
				$this->request_args['only_booked_resources'] = 1;

				//Set keyword for showing bookings ony  relative to this email
				$args['wh_keyword'] = $booking_details_email;															// 'jo@wpbookingcalendar.com';
			}
		}
		//FixIn: 8.1.3.5 	-	End


        // Get booking data
        $bk_listing = wpbc_get_bookings_objects( $args );   
        $this->bookings = $bk_listing['bookings'];
        $this->booking_types = $bk_listing['resources'];

        //Get Dates and Times for Timeline format
//debuge($this->bookings[84]);        
        $bookings_date_time = $this->wpbc_get_dates_and_times_for_timeline( $this->bookings );
        $this->dates_array = $bookings_date_time[0];
        $this->time_array_new = $bookings_date_time[1];
//debuge($this->time_array_new['2017-01-13']);


        //$milliseconds = round(microtime(true) * 1000);                       //FixIn: 7.0.Beta.18
        $milliseconds = rand( 10000, 99999 );

        $this->html_client_id = 'wpbc_timeline_' . $milliseconds;

        return $this->html_client_id;
    }


    /**
	 * Init parameters after Ajax Navigation actions
     * 
     * @param array $attr
     * @return string html_client_id - exist  from input parameters
     */
    public function ajax_init( $attr ) {

	    if ( ! defined( 'WPBC_TIMELINE_AJAX' ) ) { define( 'WPBC_TIMELINE_AJAX', true ); }        //FixIn: 8.4.7.13

        $this->is_frontend = (bool) $attr['is_frontend'];;

        //Ovverride some parameters
        if ( isset( $attr['type'] ) ) {
            $attr['wh_booking_type'] = $attr['type'];                           //Instead of 'wh_booking_type' paramter  in shortcode is used 'type' parameter
        }
//debuge($this->request_args, $attr);
        // Get paramaters from shortcode paramaters
        $this->define_request_view_params_from_params( $attr );

//debuge($this->request_args);
        if ( ! $this->request_args['is_matrix'] ) {

            switch ( $this->request_args['view_days_num'] ) {
                case '90':
                case '30':
                    if ( isset( $this->request_args['scroll_day'] ) ) $scroll_day = intval( $this->request_args['scroll_day'] );
                    else                                              $scroll_day = 0;

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_day'] = intval( $scroll_day - 7 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_day'] = intval( $scroll_day + 7 );

                    /*
                    $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                            '&scroll_day='.intval($scroll_day-7),
                                            '&scroll_day=0',
                                            '&scroll_day='.intval($scroll_day+7 ),
                                            '&scroll_day='.intval($scroll_day+4*7) );
                    $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                             __('Previous week' ,'booking'),
                                             __('Current week' ,'booking'),
                                             __('Next week' ,'booking'),
                                             __('Next 4 weeks' ,'booking') ); */
                    break;
                default:  // 365
                    if ( !isset( $this->request_args['scroll_month'] ) )    $this->request_args['scroll_month'] = 0;
                    $scroll_month = intval( $this->request_args['scroll_month'] );

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_month'] = intval( $scroll_month - 1 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_month'] = intval( $scroll_month + 1 );
                    /*
                    $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                            '&scroll_month='.intval($scroll_month-1),
                                            '&scroll_month=0',
                                            '&scroll_month='.intval($scroll_month+1 ),
                                            '&scroll_month='.intval($scroll_month+3) );
                    $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                             __('Previous month' ,'booking'),
                                             __('Current month' ,'booking'),
                                             __('Next month' ,'booking'),
                                             __('Next 3 months' ,'booking') );*/
                    break;
            }
        } else { // Matrix

            switch ( $this->request_args['view_days_num'] ) {
                case '1': //Day
                    if ( isset( $this->request_args['scroll_day'] ) )   $scroll_day = intval( $this->request_args['scroll_day'] );
                    else                                                $scroll_day = 0;

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_day'] = intval( $scroll_day - 1 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_day'] = intval( $scroll_day + 1 );
                    /*
                    $scroll_params = array( '&scroll_day='.intval($scroll_day-7),
                                            '&scroll_day='.intval($scroll_day-1),
                                            '&scroll_day=0',
                                            '&scroll_day='.intval($scroll_day+1 ),
                                            '&scroll_day='.intval($scroll_day+7) );
                    $scroll_titles = array(  __('Previous 7 days' ,'booking'),
                                             __('Previous day' ,'booking'),
                                             __('Current day' ,'booking'),
                                             __('Next day' ,'booking'),
                                             __('Next 7 days' ,'booking') );*/
                    break;

                case '7': //Week

                    if ( isset( $this->request_args['scroll_day'] ) )   $scroll_day = intval( $this->request_args['scroll_day'] );
                    else                                                $scroll_day = 0;

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_day'] = intval( $scroll_day - 7 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_day'] = intval( $scroll_day + 7 );
                    /*
                    $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                            '&scroll_day='.intval($scroll_day-7),
                                            '&scroll_day=0',
                                            '&scroll_day='.intval($scroll_day+7 ),
                                            '&scroll_day='.intval($scroll_day+4*7) );
                    $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                             __('Previous week' ,'booking'),
                                             __('Current week' ,'booking'),
                                             __('Next week' ,'booking'),
                                             __('Next 4 weeks' ,'booking') );*/
                    break;

                case '30':
                case '60':
                case '90': //3 months

                    if ( !isset( $this->request_args['scroll_month'] ) )    $this->request_args['scroll_month'] = 0;
                    $scroll_month = intval( $this->request_args['scroll_month'] );

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_month'] = intval( $scroll_month - 1 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_month'] = intval( $scroll_month + 1 );
                    /*
                    $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                            '&scroll_month='.intval($scroll_month-1),
                                            '&scroll_month=0',
                                            '&scroll_month='.intval($scroll_month+1 ),
                                            '&scroll_month='.intval($scroll_month+3) );
                    $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                             __('Previous month' ,'booking'),
                                             __('Current month' ,'booking'),
                                             __('Next month' ,'booking'),
                                             __('Next 3 months' ,'booking') );*/
                    break;

                default:  // 30, 60, 90...
                    if ( !isset( $this->request_args['scroll_month'] ) )    $this->request_args['scroll_month'] = 0;
                    $scroll_month = intval( $this->request_args['scroll_month'] );

                    if ( $attr['nav_step'] == '-1' )    $this->request_args['scroll_month'] = intval( $scroll_month - 1 );
                    if ( $attr['nav_step'] == '1' )     $this->request_args['scroll_month'] = intval( $scroll_month + 1 );
                    /*
                    $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                            '&scroll_month='.intval($scroll_month-1),
                                            '&scroll_month=0',
                                            '&scroll_month='.intval($scroll_month+1 ),
                                            '&scroll_month='.intval($scroll_month+3) );
                    $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                             __('Previous month' ,'booking'),
                                             __('Current month' ,'booking'),
                                             __('Next month' ,'booking'),
                                             __('Next 3 months' ,'booking') );
                     */
                    break;
            }
        }

                // Titles
                if ( ! $this->request_args['is_matrix'] )
                    $this->timeline_titles['header_column1'] = '';

                //Override any possible titles from shortcode paramaters
                $this->timeline_titles = wp_parse_args( $attr, $this->timeline_titles );


        // Get clean parameters to  request booking data
        $args = $this->wpbc_get_clean_paramas_from_request_for_timeline();


		//FixIn: 8.1.3.5
	    /**
	     * If provided valid ['booking_hash'] in timeline_obj in JavaScript param during Ajax request,
	     * then check, if exist booking for this hash. If exist, get Email of this booking,  and
	     * filter getting all  other bookings by email keyword.
		 * Addtionly set param ['only_booked_resources'] for showing only booking resources with  exist bookings
	     */
		if ( isset( $attr['booking_hash'] ) ) {

			// Get booking details by HASH,  and then  return Email (or other data of booking,  or false if error
			$booking_details_email = wpbc_check_hash_get_booking_details( $attr['booking_hash'] , 'email' );
//debuge($attr, $booking_details_email);
			if ( ! empty( $booking_details_email ) ) {

				// Do  not show booking resources with  no bookings
				$this->request_args['only_booked_resources'] = 1;

				//Set keyword for showing bookings ony  relative to this email
				$args['wh_keyword'] = $booking_details_email;															// 'jo@wpbookingcalendar.com';
			}
			if ( ( empty( $booking_details_email ) ) && ( ! empty( $attr['booking_hash'] ) ) ) {						 //FixIn: 8.4.6.1
				//FixIn: 8.4.5.13
				$this->request_args['only_booked_resources'] = 1;
				$args['wh_keyword'] = '``^`````^^````^`````````';
			}
		}
		//FixIn: 8.1.3.5 	-	End


        // Get booking data
        $bk_listing = wpbc_get_bookings_objects( $args );

        $this->bookings = $bk_listing['bookings'];
        $this->booking_types = $bk_listing['resources'];

        //Get Dates and Times for Timeline format        
        $bookings_date_time = $this->wpbc_get_dates_and_times_for_timeline( $this->bookings );
        $this->dates_array = $bookings_date_time[0];
        $this->time_array_new = $bookings_date_time[1];
                
    
        $this->html_client_id = $attr['html_client_id'];

        return $this->html_client_id;
    }


	/**
	 * Define initial REQUEST parameters for Admin Panel  and Get bookings and resources
	 */
    public function admin_init() {
        
        // User ////////////////////////////////////////////////////////////////
        $user = wp_get_current_user();
        $this->current_user_id = $user->ID;
        
        $this->is_frontend = false;
        
        // Get paramaters from REQUEST
        $this->define_request_view_params();
        
        if ( ! $this->request_args['is_matrix'] )
            $this->timeline_titles['header_column1'] = '';
        
// debuge($this->request_args);

        // Get clean parameters to  request booking data
        $args = $this->wpbc_get_clean_paramas_from_request_for_timeline();

        // Get booking data
        $bk_listing = wpbc_get_bookings_objects( $args );
        $this->bookings = $bk_listing['bookings'];
        $this->booking_types = $bk_listing['resources'];

        //Get Dates and Times for Timeline format
        $bookings_date_time = $this->wpbc_get_dates_and_times_for_timeline( $this->bookings );
        $this->dates_array = $bookings_date_time[0];
        $this->time_array_new = $bookings_date_time[1];
    }
    
    
    public function client_navigation( $param ) {		
        ?>
        <script type="text/javascript">
            wpbc_timeline_obj["<?php echo  $this->html_client_id; ?>"] = {     
                                        is_frontend: "<?php       echo  ( $this->is_frontend ? '1' : '0' ); ?>"
                                        , html_client_id: "<?php    echo  $this->html_client_id; ?>"
                                        , wh_booking_type: "<?php   echo  $this->request_args['wh_booking_type']; ?>"
                                        , is_matrix: "<?php         echo  ( $this->request_args['is_matrix'] ? '1' : '0' ); ?>"
                                        , view_days_num: "<?php     echo  $this->request_args['view_days_num']; ?>"
                                        , scroll_start_date: "<?php echo  $this->request_args['scroll_start_date']; ?>"
                                        , scroll_day: "<?php        echo  $this->request_args['scroll_day']; ?>"
                                        , scroll_month: "<?php      echo  $this->request_args['scroll_month']; ?>"
                                      , 'header_column1': "<?php    echo esc_js( $this->timeline_titles['header_column1'] ); ?>"
                                      , 'header_column2': "<?php    echo esc_js( $this->timeline_titles['header_column2'] ); ?>"
                                      , 'header_title': "<?php      echo esc_js( $this->timeline_titles['header_title'] ); ?>"
                                      , 'wh_trash': "<?php          echo esc_js( $this->request_args['wh_trash'] ); ?>"
                                      , 'limit_hours': "<?php           echo esc_js( $this->request_args['limit_hours'] ); ?>"                //FixIn: 7.0.1.14
                                      , 'only_booked_resources': "<?php echo esc_js( $this->request_args['only_booked_resources'] ); ?>"      //FixIn: 7.0.1.51
									  , 'options': '<?php echo  maybe_serialize( $this->options ) ; ?>'			//FixIn: 7.2.1.14
				  				      , 'booking_hash': "<?php          echo esc_js( $this->request_args['booking_hash'] ); ?>"				//FixIn: 8.1.3.5
                                    };
        </script>
        <div class="flex_tl_nav">
            <div class="flex_tl_prev" href="javascript:void(0)" onclick="javascript:wpbc_flextimeline_nav( wpbc_timeline_obj['<?php echo  $this->html_client_id; ?>'], -1 );"><a>&laquo;</a></div>
            <div class="flex_tl_title"><?php echo $param['title'] ?></div>
            <div class="flex_tl_next" href="javascript:void(0)" onclick="javascript:wpbc_flextimeline_nav( wpbc_timeline_obj['<?php echo  $this->html_client_id; ?>'],  1 );"><a>&raquo;</a></div>
        </div>
       <?php 
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //  S u p p o r t
    ////////////////////////////////////////////////////////////////////////////        

    /**
	 * Get array of cleaned (limited number) paramas from request for getting bookings by "wpbc_get_bookings_objects"
     * 
     * @return array
     */
    public function wpbc_get_clean_paramas_from_request_for_timeline() {

        //FixIn: 7.0.1.15       -   replacing in this file from date( to  date_i18n(
        $start_year  = intval( date_i18n( "Y" ) ); 
        $start_month = intval( date_i18n( "m" ) );
        $start_day = 1;
//debuge( '1.( $start_year, $start_month, $start_day , $this->request_args ',  $start_year, $start_month, $start_day , $this->request_args );        
        if ( ! empty( $this->request_args['scroll_start_date'] ) ) {            // scroll_start_date=2013-07-01
            
            list( $start_year, $start_month, $start_day ) = explode( '-', $this->request_args['scroll_start_date'] );
            $start_year  = intval( $start_year );
            $start_month = intval( $start_month );            
            $start_day   = intval( $start_day );                    
        }
//debuge( '2.( $start_year, $start_month, $start_day )',  $start_year, $start_month, $start_day );
        $scroll_day = 0;
        $scroll_month = 0;

        if ( ( isset( $this->request_args['view_days_num'] ) ) 
            //&& ($this->request_args['view_days_num'] != '30') 
            )
            $view_days_num = $this->request_args['view_days_num'];
        else
            $view_days_num = get_bk_option( 'booking_view_days_num' );

        $view_days_num = intval( $view_days_num );
//debuge( '2.1( $view_days_num )', $view_days_num );        
        $is_matrix = (bool) $this->request_args['is_matrix'];

        if ( $is_matrix ) {

            switch ( $view_days_num ) {

                case '1':
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = intval( date_i18n( "d" ) );                               // Today date

                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = intval( $this->request_args['scroll_day'] );

                    $real_date = mktime( 0, 0, 0, $start_month, ($start_day + $scroll_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );

                    $real_date = mktime( 0, 0, 0, $start_month, ($start_day + 0 + $scroll_day ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );
                    break;

                case '7':
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = intval( date_i18n( "d" ) );   //Today  date
                    $start_week_day_num = intval( date_i18n( "w" ) );
                    $start_day_weeek = intval( get_bk_option( 'booking_start_day_weeek' ) ); //[0]:Sun .. [6]:Sut
                    if ( $start_week_day_num != $start_day_weeek ) {
                        for ( $d_inc = 1; $d_inc < 8; $d_inc++ ) {              // Just get week  back
                            $real_date = mktime( 0, 0, 0, $start_month, ($start_day - $d_inc ), $start_year );
                            $start_week_day_num = intval( date_i18n( "w", $real_date ) );
                            if ( $start_week_day_num == $start_day_weeek ) {
                                $start_day   = intval( date_i18n( "d", $real_date ) );
                                $start_year  = intval( date_i18n( "Y", $real_date ) );
                                $start_month = intval( date_i18n( "m", $real_date ) );
                                $d_inc = 9;
                            }
                        }
                    }

                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = intval( $this->request_args['scroll_day'] );

                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );

                    $real_date = mktime( 0, 0, 0, $start_month, ($start_day + 7 + $scroll_day ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );
                    break;

                case '30':
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = intval( $this->request_args['scroll_month'] );

//debuge('3.$scroll_month, $start_month, $start_day, $start_year', $scroll_month, $start_month, $start_day, $start_year );
                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), ( $start_day ), $start_year );
//debuge('4.$real_date',$real_date);
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );
//debuge('5.$wh_booking_date',$wh_booking_date);                    
                    $real_date = mktime( 0, 0, 0, ($start_month + 1 + $scroll_month ), ($start_day - 1 ), $start_year );
//debuge('6.$real_date',$real_date);         
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );
//debuge('7.$wh_booking_date2', $wh_booking_date2);                    
                    break;

                case '60':
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = intval( $this->request_args['scroll_month'] );

                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), ( $start_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );                          // '2012-12-01';

                    $real_date = mktime( 0, 0, 0, ($start_month + 2 + $scroll_month ), ($start_day - 1 ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );                          // '2013-02-31';                    
                    break;

                ////////////////////////////////////////////////////////////////////////////////
                default:  // 30 - default
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = intval( $this->request_args['scroll_month'] );

                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), ( $start_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );                          // '2012-12-01';

                    $real_date = mktime( 0, 0, 0, ($start_month + 1 + $scroll_month ), ($start_day - 1 ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );                          // '2012-12-31';
                    break;
            }
            
        } else {   // Single resource
            
            switch ( $view_days_num ) {
                
                case '90':

                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = intval( date_i18n( "d" ) );    //Today Date
                    $start_week_day_num = intval( date_i18n( "w" ) );
                    $start_day_weeek = intval( get_bk_option( 'booking_start_day_weeek' ) ); //[0]:Sun .. [6]:Sut

                    if ( $start_week_day_num != $start_day_weeek ) {
                        for ( $d_inc = 1; $d_inc < 8; $d_inc++ ) {              // Just get week  back
                            $real_date = mktime( 0, 0, 0, $start_month, ($start_day - $d_inc ), $start_year );
                            $start_week_day_num = intval( date_i18n( "w", $real_date ) );
                            if ( $start_week_day_num == $start_day_weeek ) {
                                $start_day   = intval( date_i18n( "d", $real_date ) );
                                $start_year  = intval( date_i18n( "Y", $real_date ) );
                                $start_month = intval( date_i18n( "m", $real_date ) );
                                $d_inc = 9;
                            }
                        }
                    }

                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = intval( $this->request_args['scroll_day'] );

                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );                          // '2012-12-01';

                    $real_date = mktime( 0, 0, 0, $start_month, ($start_day + 7 * 12 + 7 + $scroll_day ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );                          // '2013-12-31';
                    break;

                case '30':
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = intval( date_i18n( "d" ) );    //Today Date

                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = intval( $this->request_args['scroll_day'] );

                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );                          // '2012-12-01';

                    $real_date = mktime( 0, 0, 0, $start_month, ($start_day + 31 + $scroll_day ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );                          // '2013-12-31';
                    break;

                default:  // 365

                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = intval( $this->request_args['scroll_month'] );
                    else
                        $scroll_month = 0;

                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                    $wh_booking_date = date_i18n( "Y-m-d", $real_date );                          // '2012-12-01';

                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month + 13 ), ($start_day - 1 ), $start_year );
                    $wh_booking_date2 = date_i18n( "Y-m-d", $real_date );                          // '2013-12-31';

                    break;
            }
        }

        $or_sort = get_bk_option( 'booking_sort_order' );

        $args = array(
            'wh_booking_type' => $this->request_args['wh_booking_type'],
            'wh_approved' => '',
            'wh_booking_id' => '',
            'wh_is_new' => '',
            'wh_pay_status' => 'all',
            'wh_keyword' => '',
            'wh_booking_date' => $wh_booking_date,
            'wh_booking_date2' => $wh_booking_date2,
            'wh_modification_date' => '3',
            'wh_modification_date2' => '',
            'wh_cost' => '',
            'wh_cost2' => '',
            'or_sort' => $or_sort,
            'page_num' => '1',
            'wh_trash' => $this->request_args['wh_trash'], 
            'limit_hours' => $this->request_args['limit_hours'], 
            'only_booked_resources' => $this->request_args['only_booked_resources'],    //FixIn: 7.0.1.51
            'page_items_count' => '100000'
        );
//debuge('8.',$args);
        return $args;
    }


	/**
	 * Define View Params from  $_REQUEST
	 */
    public function define_request_view_params() {
        
        if ( isset( $_REQUEST['wh_booking_type'] ) ) {                          
                                                        $this->request_args['wh_booking_type'] = $_REQUEST['wh_booking_type'];          // Used once for comma seperated resources only.            
        } elseif ( isset( $_GET['booking_type'] ) ) {   $this->request_args['wh_booking_type'] = $_GET['booking_type'];
        } 
        
        if (  ( isset( $_REQUEST['wh_booking_type'] ) ) && ( strpos( $_REQUEST['wh_booking_type'], ',' ) !== false )  ) 
                                                        $this->request_args['is_matrix'] = true;                    
        if ( isset( $_REQUEST['view_days_num'] ) )      $this->request_args['view_days_num'] = $_REQUEST['view_days_num'];        
        if ( isset( $_REQUEST['scroll_start_date'] ) )  $this->request_args['scroll_start_date'] = $_REQUEST['scroll_start_date'];                
        if ( isset( $_REQUEST['scroll_day'] ) )         $this->request_args['scroll_day'] = $_REQUEST['scroll_day'];        
        if ( isset( $_REQUEST['scroll_month'] ) )       $this->request_args['scroll_month'] = $_REQUEST['scroll_month'];        
        if ( isset( $_REQUEST['wh_trash'] ) )           $this->request_args['wh_trash'] = $_REQUEST['wh_trash'];
        
        if ( isset( $_REQUEST['limit_hours'] ) )            $this->request_args['limit_hours'] = $_REQUEST['limit_hours'];                      //FixIn: 7.0.1.14
        if ( isset( $_REQUEST['only_booked_resources'] ) )  $this->request_args['only_booked_resources'] = 1;//$_REQUEST['only_booked_resources'];  //FixIn: 7.0.1.51
    }
     
    
    /**
	 * Define Request View Params
     * 
     * @param array $param = = array(                                     
                                      'wh_booking_type' => ''            
                                    , 'is_matrix' => false
                                    , 'view_days_num' => '30'
                                        , 'scroll_start_date' => ''
                                        , 'scroll_day' => 0
                                        , 'scroll_month' => 0
                                );    
     */
    public function define_request_view_params_from_params( $param ) {
        //debuge(  $param , $this->options , maybe_unserialize( wp_unslash( $param['options'] ) ) );die;
        if ( isset( $param['wh_booking_type'] ) )    $this->request_args['wh_booking_type'] = $param['wh_booking_type'];          // Used once for comma seperated resources only.            
        
        if (  ( isset( $param['wh_booking_type'] ) ) && ( strpos( $param['wh_booking_type'], ',' ) !== false )  ) 
                                                     $this->request_args['is_matrix'] = true;                    
        if ( isset( $param['view_days_num'] ) )      $this->request_args['view_days_num'] = $param['view_days_num'];        
        if ( isset( $param['scroll_start_date'] ) )  $this->request_args['scroll_start_date'] = $param['scroll_start_date'];                
        if ( isset( $param['scroll_day'] ) )         $this->request_args['scroll_day'] = $param['scroll_day'];        
        if ( isset( $param['scroll_month'] ) )       $this->request_args['scroll_month'] = $param['scroll_month'];        
        if ( isset( $param['wh_trash'] ) )           $this->request_args['wh_trash'] = $param['wh_trash'];
        if ( isset( $param['limit_hours'] ) )        $this->request_args['limit_hours'] = $param['limit_hours'];                                //FixIn: 7.0.1.14
        if ( isset( $param['only_booked_resources'] ) )  $this->request_args['only_booked_resources'] = $param['only_booked_resources'];        //FixIn: 7.0.1.14
        if ( isset( $param['booking_hash'] ) )  	 $this->request_args['booking_hash'] = $param['booking_hash'];        						//FixIn: 8.1.3.5
		if ( ( empty( $this->options ) ) && ( isset( $param['options'] ) )  ) {
			$this->options = maybe_unserialize( wp_unslash( $param['options'] ) );        //FixIn: 7.2.1.14
        }
			
    }
    

    /**
	 * Get  D A T E S  and  T I M E S  from   B o o k i n g s
     * 
     * @param array $bookings - Booking input array
     * @return array          - array( $dates_array, $time_array_new )  
     */    
    public function wpbc_get_dates_and_times_for_timeline( $bookings ) {

        // Generate: Array ( [0] => array(), [3600] =>  array(), [7200] => array(), ..... [43200] => array(),.... [82800] => array()   ) 
        $fixed_time_hours_array = array();                                      
        for ( $tt = 0; $tt < 24; $tt++ ) {
            $fixed_time_hours_array[$tt * 60 * 60] = array();
        }
//debuge($bookings[911],$bookings[910]);
        // Dates array: { '2012-12-24' => array( Booking ID 1, Booking ID 2, ....), ... }
        $dates_array = $time_array = array();
        foreach ( $bookings as $bk ) {


	        /**
	         * Check situation ,  while we are having end time but do not have start  time, like here:
			 * $bk->dates = Array(
									[0] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-22 00:00:00
											[approved] => 1
											[type_id] =>  )

									[1] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-23 00:00:00
											[approved] => 1
											[type_id] =>  )

									[2] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-24 15:00:02
											[approved] => 1
											[type_id] =>  )
								)
			 *
			 * So  we need to  add the new Start  time before End time,  like this:
			 *
									[0] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-22 00:00:00
											[approved] => 1
											[type_id] =>  )

									[1] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-23 00:00:00
											[approved] => 1
											[type_id] =>  )
									[2] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-24 00:00:01
											[approved] => 1
											[type_id] =>  )
									[2] => stdClass Object (
											[booking_id] => 911
											[booking_date] => 2019-07-24 15:00:02
											[approved] => 1
											[type_id] =>  )
			 *
	         */
			$dates_to_check = array();
			$is_started = 0;
	        foreach ( $bk->dates as $dt ) {

	        	$last_second = substr( $dt->booking_date, -1);

	        	// Count start  end end times started.
				switch ( $last_second ) {
				    case '1':		// Start
				        $is_started++;
				        break;
				    case '2':		// End
				        $is_started--;
				        break;
				    case '0':    	// Full
				    default:
				}

				// Its means that  we have now end time,  but was not having start time
		        if ( $is_started < 0 ) {
		        	$is_started++;
					$my_temp_start_time = clone $dt;

					$temp_time = explode( ' ' ,$my_temp_start_time->booking_date );
					$temp_time = $temp_time[0] . ' 00:00:01';

					$my_temp_start_time->booking_date = $temp_time;
					// Add start time day
					$dates_to_check[] = $my_temp_start_time;
				}
				$dates_to_check[] = $dt;
			}

			if ( 0 != $is_started ) {
				?><div class="warning_check_in_out_not_equal"><?php
				debuge( 'Warning! Number of check  in != check out times.', $dates_to_check, $is_started)  ;
				?></div><script type="text/javascript"> jQuery( '.warning_check_in_out_not_equal' ).animate( {opacity: 1}, 3000 ).toggle(1000); </script><?php
			}


			foreach ( $dates_to_check as $dt ) {
            //foreach ( $bk->dates as $dt ) {

                // Transform from MySQL date to PHP date
                $dt->booking_date = trim( $dt->booking_date );
                $dta = explode( ' ', $dt->booking_date );
                $tms = $dta[1];
//FixIn: 8.2.1.21
//if ( substr( $dta[1], - 1 ) == '2' ) { continue; }
                $tms = explode( ':', $tms );                                        // array('13','30','40')
                $dta = $dta[0];
                $dta = explode( '-', $dta );                                        // array('2012','12','30')
                $php_dt = mktime( $tms[0], $tms[1], $tms[2], $dta[1], $dta[2], $dta[0] );

                if ( ( isset( $dt->type_id ) ) && (!empty( $dt->type_id )) )
                    $date_bk_res_id = $dt->type_id;
                else
                    $date_bk_res_id = $bk->booking_type;


                $my_date = date_i18n( "Y-m-d", $php_dt );                                // '2012-12-01';
                if ( !isset( $dates_array[$my_date] ) ) {
                    $dates_array[$my_date] = array( array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id ) );
                } else {
                    $dates_array[$my_date][] = array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id );
                }

                $my_time = date_i18n( "H:i:s", $php_dt );                                // '21:55:01';

                $my_time_index = explode( ':', $my_time );
                $my_time_index = (int) ($my_time_index[0] * 60 * 60 + $my_time_index[1] * 60 + $my_time_index[2]);

                $my_time = strtotime( $my_time );                     //FixIn: 8.1.1.6

                if ( !isset( $time_array[$my_date] ) ) {
                    $time_array[$my_date] = array( $my_time_index => array( $my_time => array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id ) ) );
                } else {

                    if ( !isset( $time_array[$my_date][$my_time_index] ) )
                        $time_array[$my_date][$my_time_index] = array( $my_time => array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id ) );
                    else {
                        if ( !isset( $time_array[$my_date][$my_time_index][$my_time] ) )
                            $time_array[$my_date][$my_time_index][$my_time] = array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id );
                        else {
                            $my_time_inc = 3;
                            while ( isset( $time_array[$my_date][$my_time_index][$my_time + $my_time_inc] ) ) {
                                $my_time_inc++;
                            }
                            //Just in case if we are have the booking in the same time, so we are
                            $time_array[$my_date][$my_time_index][($my_time + $my_time_inc)] = array( 'id' => $bk->booking_id, 'resource' => $date_bk_res_id ); 
                        }
                    }
                }
            }
        }


//debuge($time_array);
        // Sorting ..........
        foreach ( $time_array as $key => $value_t ) {                           // Sort the times from lower to higher
            ksort( $value_t );
            $time_array[$key] = $value_t;
        }
        ksort( $time_array );                                                   // Sort array by dates from lower to higher.
        /* $time_array:
          $key_date     $value_t
          [2012-12-13] => Array ( $tt_index          $times_bk_id_array
          [44401] => Array ( [12:20:01] => 19)
          ),
          [2012-12-14] => Array (
          [10802] => Array([03:00:02] => 19),
          [43801] => Array([12:10:01] => 2)
          ),
          .... */

//debuge($time_array);

        $time_array_new = array();
        foreach ( $time_array as $key_date => $value_t ) {                          // fill the $time_array_new - by bookings of full dates....
            $new_times_array = $fixed_time_hours_array;                             // Array ( [0] => Array, [3600] => Array, [7200] => Array .....

            foreach ( $value_t as $tt_index => $times_bk_id_array ) {               //  [44401] => Array ( [12:20:01] => 19 ), .....
                $tt_index_round = floor( ($tt_index / 60) / 60 ) * 60 * 60;         // 14400, 18000,
                $is_bk_for_full_date = $tt_index % 10;                              // 0, 1, 2

                switch ( $is_bk_for_full_date ) {
                    case 0:                                                         // Full date - fill every time slot
                        foreach ( $new_times_array as $round_time_slot => $bk_id_array ) {
                            $new_times_array[$round_time_slot] = array_merge( $bk_id_array, array_values( $times_bk_id_array ) );
                        }
//debuge('$time_array[$key_date][$tt_index]',$time_array[$key_date][$tt_index], $round_time_slot);
                        unset( $time_array[$key_date][$tt_index] );
                        break;

                    case 1: break;
                    case 2: break;
                    default: break;
                }
            }
            if ( count( $time_array[$key_date] ) == 0 )
                unset( $time_array[$key_date] );

            $time_array_new[$key_date] = $new_times_array;
        }
		//$time_array_new - Array  filled by  bookings FOR FULL DAY booking only

//debuge($time_array_new);


        foreach ( $time_array as $key_date => $value_t ) {
            $new_times_array_for_day_start = $new_times_array_for_day_end = array();
            foreach ( $value_t as $tt_index => $times_bk_id_array ) {               //  [44401] => Array ( [12:20:01] => 19 ), .....
                $tt_index_round = floor( ($tt_index / 60) / 60 ) * 60 * 60;         // 14400, 18000,
//debuge($tt_index, $tt_index_round);                
                $is_bk_for_full_date = $tt_index % 10;                              // 0, 1, 2

                if ( $is_bk_for_full_date == 1 ) {
                    if ( !isset( $new_times_array_for_day_start[$tt_index_round] ) )
                        $new_times_array_for_day_start[$tt_index_round] = array();
                    $new_times_array_for_day_start[$tt_index_round] = array_merge( $new_times_array_for_day_start[$tt_index_round], array_values( $times_bk_id_array ) );
                }
                if ( $is_bk_for_full_date == 2 ) {

                    // Its mean that  the booking is finished exactly  at  the beginig of this hour, 
                    // so  we will not fill the end of booking in this hour, but in previous
                    if ( ($tt_index_round - $tt_index) == -2 ) {
                        $tt_index_round = $tt_index_round - 60 * 60;
                    }

                    if ( !isset( $new_times_array_for_day_end[$tt_index_round] ) )
                        $new_times_array_for_day_end[$tt_index_round] = array();
                    $new_times_array_for_day_end[$tt_index_round] = array_merge( $new_times_array_for_day_end[$tt_index_round], array_values( $times_bk_id_array ) );
                }
            }
            $time_array[$key_date] = array( 'start' => $new_times_array_for_day_start, 'end' => $new_times_array_for_day_end );
        }

//debuge($time_array);
//$time_array['2019-07-24']['start'][0] = $time_array['2019-07-24']['end'][82800];

         /* $time_array
          [2012-12-24] => Array
          (
          [start] => Array (
          [68400] => Array ( [0] => 15 ) )
          [end] => Array (
          [64800] => Array ( [0] => 6 ) )

          ) */
        $fill_this_date = array();
//debuge($time_array_new['2017-01-13']);
        
//debuge($time_array_new);
        // Fil specific times based on start  and end times
        foreach ( $time_array_new as $ddate => $ttime_round_array ) {
            foreach ( $ttime_round_array as $ttime_round => $bk_id_array ) {    // [3600] => Array( [0] => Array ( [id] => 214 [resource] => 9 ), [1] => Array ( [id] => 154 [resource] => 7    

//if ('2019-11-27' == $ddate ) {
 //debuge( ' $fill_this_date', $dates_array[ $ddate ], $fill_this_date );
//}

				////////////////////////////////////////////////////////////////////////////////////////////////////////
	            /**
	             * Search  for situation,  when  at some previous date  was started some booking at  specific start  time, like:
	             * 																										Booking #77: "October 30 18:00; December 4; December 25 20:00"
	             * 	This booking exist in $fill_this_date 	-	array (  [0] => array( [id] => 77, [resource] => 1 )  )
	             *
	             *    but at  current date "November 6" (where start  other 											Booking #76:	November 6 18:00; November 13; November 27 20:00"
	             *  this booking #77 does not exist  at  all !!!
				 * 	so  we nee to  remove this booking(s)  from  array $fill_this_date
	             */
	            //FixIn: 8.7.1.1
if(1){
				////////////////////////////////////////////////////////////////////////////////////////////////////////
				// Search  to  remove
				$remove_arr_keys = array();
				foreach ( $fill_this_date as $is_remove_key => $is_remove_value ) {
					/**
					 * Remove elements from  					$fill_this_date
					 * if such  elements does not exist  in 	$dates_array[$ddate]
					 */
					if ( ! in_array( $is_remove_value, $dates_array[ $ddate ] ) ) {
						$remove_arr_keys[] = $is_remove_key;
//if ($is_remove_value['id']==76) {
//debuge('$ddate,$ttime_round_array',$ddate);
//}
					}
				}
			 	// Removing
				foreach ( $remove_arr_keys as $is_remove_key ) {
					unset($fill_this_date[$is_remove_key]);				// Remove item at index 1 which is 'for'
				}
				$fill_this_date = array_values($fill_this_date);		// Re-index the array elements
}
				////////////////////////////////////////////////////////////////////////////////////////////////////////

//if ('2019-11-27' == $ddate ) {
//debuge( 'updated $fill_this_date',   $fill_this_date );
//}


                if ( isset( $time_array[$ddate] ) ) {

                    if ( isset( $time_array[$ddate]['start'][$ttime_round] ) )  // array
                        $fill_this_date = array_merge( $fill_this_date, array_values( $time_array[$ddate]['start'][$ttime_round] ) );

//debuge($fill_this_date);

                    $time_array_new[$ddate][$ttime_round] = array_merge( $time_array_new[$ddate][$ttime_round], $fill_this_date );
//debuge('$ttime_round',$ttime_round);
//debuge($ddate, $ttime_round, $time_array_new[$ddate][$ttime_round]);

                    //FixIn: 7.0.1.16 - advanced checking about delettion  of times in $time_array[$ddate]['end']
                    
                    // End array checking for deleting.
                    if ( isset( $time_array[$ddate]['end'][$ttime_round] ) )    // array
                        foreach ( $time_array[$ddate]['end'][$ttime_round] as $toDelete ) {
//debuge($ddate, $ttime_round, $fill_this_date);
//if ( $ddate == '2019-07-24' ) {
//    debuge('$toDelete, $fill_this_date',$toDelete, $fill_this_date);
//}
//debuge($toDelete);
                            $fill_this_date_keys_to_delete = array();
                            foreach ( $fill_this_date as $fill_this_date_key => $check_element_array ) {        // [0] => Array ( [id] => 54 [resource] => 5 )
//debuge($ddate,'$toDelete, $check_element_array', $toDelete, $check_element_array);
                                if (                                            // Check  if arrays equals - identical
                                           ( is_array( $toDelete ) && is_array( $check_element_array ) )
                                        && ( count( $toDelete ) == count( $check_element_array ) )
                                        && ( array_diff( $toDelete, $check_element_array ) === array_diff( $check_element_array, $toDelete ) )
                                    )  {       
                                      $fill_this_date_keys_to_delete[] = $fill_this_date_key;               // $toDelete element exist so  save key in original  array 
                                }
                            }


	                        //FixIn:  on 2019-07-24 14:22
							/**
                            // Fix, when  we are having END time but was not having START time (usually  when first  booking
							// was booked for entire day,  and last  day  booking, have the end time.
							// So  in this case at  day  with  end time we need to  fill  all  "round times" with  End time data
							*/
//FixIn: 8.7.1.1
if(1)
							if ( empty( $fill_this_date_keys_to_delete ) ) {
		                        // Fill  the date by data $toDelete
		                        foreach ( $time_array_new[ $ddate ] as $time_round_refill => $time_array_new_value_refill ) {

			                        if ( $time_round_refill <= $ttime_round ) {
				                        // Refill
				                        $time_array_new[ $ddate ][ $time_round_refill ][] = $toDelete;
			                        }
		                        }
	                        }


//debuge(' $fill_this_date_keys_to_delete ',$fill_this_date_keys_to_delete );
                            $fill_this_date_new = array();
                            foreach ( $fill_this_date as $fill_this_date_key => $fill_this_date_value ) {
                                if (  ! in_array( $fill_this_date_key, $fill_this_date_keys_to_delete ) ) {
                                    $fill_this_date_new[] = $fill_this_date_value;
                                }
                            }
                            $fill_this_date = $fill_this_date_new;              // Reassign cleared array (with  deleted values)

//debuge($toDelete);
                            if ( !empty( $fill_this_date ) ) {
//                                $fill_this_date = array_diff( $fill_this_date, array( $toDelete ) );
                                
//if ( $ddate == '2017-01-13' ) {
//    debuge('AFTER:: $toDelete, $fill_this_date',$toDelete, $fill_this_date);
//}                                
                            }
                        }
                }
            }
        }
//debuge( '$dates_array, $time_array_new',$dates_array, $time_array_new );
        return array( $dates_array, $time_array_new );
    }


    ////////////////////////////////////////////////////////////////////////////
    //  C a l e n d a r    T i m e l i n e       ///////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    private function wpbc_dates_only_of_specific_resource( $booked_dates_array, $resource_id, $bookings ) {

        foreach ( $booked_dates_array as $key => $value ) {

            $new_array = array();
            foreach ( $value as $bk_id ) {
                if ( $bk_id['resource'] == $resource_id ) {
                    $new_array[] = $bk_id['id'];
                }
            }
            if ( !empty( $new_array ) )
                $booked_dates_array[$key] = $new_array;
            else
                unset( $booked_dates_array[$key] );
        }
        return $booked_dates_array;
    }

    
    private function wpbc_times_only_of_specific_resource( $time_array_new, $resource_id, $bookings ) {

        foreach ( $time_array_new as $date_key => $times_array ) {

            foreach ( $times_array as $time_key => $value ) {

                $new_array = array();
                foreach ( $value as $bk_id ) {

                    if ( $bk_id['resource'] == $resource_id ) {
                        $new_array[] = $bk_id['id'];
                    }
                }
                $time_array_new[$date_key][$time_key] = $new_array;
            }
        }
        return $time_array_new;
    }


    private function wpbc_write_bk_id_css_classes( $prefix, $previous_booking_id ) {

        if ( (!isset( $previous_booking_id )) || (empty( $previous_booking_id )) )
            return '';

        if ( is_string( $previous_booking_id ) )
            $bk_id_array = explode( ',', $previous_booking_id );
        else if ( is_array( $previous_booking_id ) )
            $bk_id_array = $previous_booking_id;
        else // Some Unknown situation
            return '';

        $bk_id_array = array_unique( $bk_id_array );

        // If we are have several bookings,  so  add this special class
        if ( count( $bk_id_array ) > 1 )
            $css_class = 'here_several_bk_id ';
        else
            $css_class = '';

        foreach ( $bk_id_array as $bk_id ) {
            $css_class .= $prefix . $bk_id . ' ';
        }

        return $css_class;
    }    

    
    ////////////////////////////////////////////////////////////////////////////
    //  Header
    ////////////////////////////////////////////////////////////////////////////

    /** Header */
    public function wpbc_show_timeline_header_row( $start_date = false ) {

	    $current_resource_id      = '';
	    $is_matrix                = $this->request_args['is_matrix'];
	    $view_days_num            = $this->request_args['view_days_num'];
	    $start_hour_for_1day_view = 0;                                          //FixIn: 7.0.1.14
	    $end_hour_for_1day_view   = 24;
	    $limit_hours              = 24;

        if ( $is_matrix ) {

            // MATRIX VIEW
            switch ( $view_days_num ) {
                case '1':
                    $days_num = 1;
                    $dwa = $this->week_days_titles['full'];
                    $time_selles_num = 24;
                    if ( isset( $this->request_args[ 'limit_hours' ] ) ) {     //FixIn: 7.0.1.14
                        $limit_hours = explode(',',$this->request_args[ 'limit_hours' ]);
                        $start_hour_for_1day_view = intval( $limit_hours[0] );
                        $end_hour_for_1day_view   = intval( $limit_hours[1] );
                        $limit_hours = $limit_hours[1] - $limit_hours[0];
                    }
                    break;
                case '7':
                    $days_num = 7;
                    $dwa = $this->week_days_titles['3'];
                    $time_selles_num = 1;
                    break;
                case '30':
                    $days_num = 31;
                    $days_num = intval( date_i18n('t',$start_date) );           // num of days in the specific  month,  wchih  relative to $real_date from  header        //FixIn: 7.0.1.47
                    $dwa = $this->week_days_titles['1'];
                    $time_selles_num = 1;
                    break;
                case '60':
                    $days_num = 62;
                    $dwa = $this->week_days_titles['short'];
                    $time_selles_num = 1;
                    break;
                default:  // 30
                    $days_num = 31;
                    $dwa = $this->week_days_titles['3'];
                    $time_selles_num = 1;
                    break;
            }
            
        } else {

            switch ( $view_days_num ) {
                case '90':
                    $days_num = 7;
                    $dwa = $this->week_days_titles['3'];
                    $time_selles_num = 1;
                    break;
                case '365':
                    $days_num = 31;        //FixIn: 8.7.6.5
                    //$days_num = intval( date_i18n('t',$start_date) );           // num of days in the specific  month,  wchih  relative to $real_date from  header        //FixIn: 7.0.1.47
                    $dwa = $this->week_days_titles['3'];
                    $time_selles_num = 1;
                    break;
                default:  // 30
                    $days_num = 1;
                    $dwa = $this->week_days_titles['full'];
                    $time_selles_num = 24;
                    if ( isset( $this->request_args[ 'limit_hours' ] ) ) {     //FixIn: 7.0.1.14
                        $limit_hours = explode(',',$this->request_args[ 'limit_hours' ]);
                        $start_hour_for_1day_view = intval( $limit_hours[0] );
                        $end_hour_for_1day_view   = intval( $limit_hours[1] );
                        $limit_hours = $limit_hours[1] - $limit_hours[0];    
                    }                    
                    break;
            }
        }

        if ( $start_date === false ) {
            
            if ( ! empty( $this->request_args['scroll_start_date'] ) )           
                list( $start_year, $start_month, $start_day ) = explode( '-', $this->request_args['scroll_start_date'] );   // scroll_start_date=2013-07-01
            else 
                list( $start_year, $start_month, $start_day ) = explode( '-', date_i18n( 'Y-n-j' ) );
            
        } else {
                list( $start_year, $start_month, $start_day ) = explode( '-', date_i18n( 'Y-m-d', $start_date ) );
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Month Line
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		?><div class="flex_tl_dates_bar flex_tl_dates_bar_month"><?php
			$previous_month = '';
			//Scroll months Firstly
			for ( $d_inc = 0; $d_inc < $days_num; $d_inc++ ) {

				$real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $d_inc ), $start_year );
				$yy = date_i18n( "Y", $real_date );    //2012
				$mm = date_i18n( "m", $real_date );    //09
				$dd = date_i18n( "d", $real_date );    //31
				$ww = date_i18n( "N", $real_date );    //7
				$day_week = $dwa[$ww];          	   //Su

				if ( ( $previous_month != $mm ) || ( 1 == $dd ) ) {
					$previous_month = $mm;
					$month_title = date_i18n( "F", $real_date );    //09
					$month_class = ' new_month ';
				} else {
					$month_title = '';
					$month_class = '';
				}
				?>
				<div class="<?php  echo implode(' ', array(
									'flex_tl_day_cell',
									'flex_tl_day_cell_header',
									'flex_time_in_days_num_' . $view_days_num,
									$month_class
					 )); ?>"
				><?php
						// New Month !
						if ($month_title != '') {
							?><div class="in_cell_month_year"><?php

								if ( $is_matrix ) {

									echo $dd . ' ' . $month_title .', ' . $yy ;

									if ( '1' == $view_days_num ) {
										echo ' &nbsp; (' . $day_week . ')';
									}

								} else {
									if ( '30' == $view_days_num ) {
										//echo '(' . $day_week . ') &nbsp; ' . $dd . ' ';
									}
								}


							?></div><?php
						}
				?> </div> <?php
			}
		?>
		</div><?php


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Dates / Times  Line
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		?><div class="flex_tl_dates_bar <?php echo $is_matrix ? ' flex_tl_matrix_resources ' : ' flex_tl_single_resource '; ?>"
			 id="timeline_scroller<?php echo $current_resource_id; ?>"
				 ><?php
                    $previous_month = '';

                    $bk_admin_url_today = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), array( 'scroll_month', 'scroll_day', 'scroll_start_date' ) );
                    
                    for ( $d_inc = 0; $d_inc < $days_num; $d_inc++ ) {

                        $real_date = mktime( 0, 0, 0, $start_month, ($start_day + $d_inc ), $start_year );

                        if ( date_i18n( 'm.d.Y' ) == date_i18n( "m.d.Y", $real_date ) )
                            $is_today = ' today_date ';
                        else
                            $is_today = '';

                        $yy = date_i18n( "Y", $real_date );    //2012
                        $mm = date_i18n( "m", $real_date );    //09
                        $dd = date_i18n( "d", $real_date );    //31
                        $ww = date_i18n( "N", $real_date );    //7
                        $day_week = $dwa[$ww];          	   //Su

                        $day_title = $dd . ' ' . $day_week;
                        if ( $is_matrix ) {
                            if ( $view_days_num == 1 ) {
	                            $day_title = '<div class="in_cell_day_num">' . __( 'Times', 'booking' ) . '</div>';
                            }
                            if ( $view_days_num == 7 ) {
                                $day_title =   '<div class="in_cell_day_num">' . $dd . '</div><div class="in_cell_day_week">' . $day_week . '</div>';
                            }
                            if ( $view_days_num == 30 ) {
                                $day_title =   '<div class="in_cell_day_num">' . $dd . '</div><div class="in_cell_day_week">' . $day_week . '</div>';
                            }
                            if ( $view_days_num == 60 ) {
                                $day_title =   '<div class="in_cell_day_num">' . $dd . '</div><div class="in_cell_day_week">' . $day_week . '</div>';
                            }
                        } else {
                            if ( $view_days_num == 30 ) {
                                $day_title = '<div class="in_cell_day_num">' . __( 'Times', 'booking' )  . '</div>';
                            }
                            if ( $view_days_num == 90 ) {
                                $day_title =   '<div class="in_cell_day_week">' . $day_week . '</div>';
                            }
                            if ( $view_days_num == 365 ) {
                                $day_title =   '<div class="in_cell_day_num">' . $dd . '</div>';
                            }
                        }
                        $day_filter_id = $yy . '-' . $mm . '-' . $dd;

	                    if ( ( $previous_month != $mm ) || ( 1 == $dd ) ) {
                            $previous_month = $mm;
                            $month_class = ' new_month ';
                        } else {
                            $month_class = '';
                        }
                     
                        ?>
                        <div id="cell_<?php  echo $current_resource_id . '_' . $day_filter_id ; ?>" 
                             class="<?php  echo implode(' ', array(
							 				'flex_tl_day_cell',
											'flex_tl_day_cell_header',
											'flex_time_in_days_num_' . $view_days_num,
											'flex_tl_weekday' . $ww,
											$day_filter_id,
											$month_class
							 )); ?>"
					    ><?php

	                    		if ( ( $is_matrix ) && ( ( $view_days_num == 30 ) || ( $view_days_num == 60 ) ) ) {

                                	?><div class="in_cell_date_container day_num<?php echo $d_inc ?>"><?php

										if ( ! $this->is_frontend ) {
											?><a href='<?php echo $bk_admin_url_today . '&scroll_start_date=' . $yy . '-' . $mm . '-' . $dd; ?>'><?php
										}
											echo $day_title;

										if ( ! $this->is_frontend ) {
											?></a><?php
										}

									?></div><?php

                                } else {
                                    ?><div class="in_cell_date_container day_num<?php echo $d_inc ?>"><?php echo $day_title;?></div><?php
                                }

	                    		////////////////////////////////////////////////////////////////////////////////////////
                                // T i m e   c e l l s
								////////////////////////////////////////////////////////////////////////////////////////
                                $tm = floor( 24 / $time_selles_num );
                                if ( $time_selles_num > 1 ) {
									?><div class="<?php  echo implode(' ', array(
																				'in_cell_time_section_in_day',
																				'flex_time_section_in_day_header',
																				'flex_time_in_days_num_' . $view_days_num
										 )); ?>"><?php

												for ( $tt = 0; $tt < $time_selles_num; $tt++ ) { ?>

													<?php  if ( ( $tt < $start_hour_for_1day_view ) || ( $tt > $end_hour_for_1day_view ) ) { continue; } //FixIn: 7.0.1.14 ?>

													<div class="<?php  echo 'in_cell_time_hour time_hour' . ( $tt * $tm ); ?>" ><?php

														$this->show_time_number_in_correct_format( $tt * $tm , $view_days_num );

													?></div><?php
												}
								  ?></div><?php
                                }
                      ?></div><?php        

                    }
		?></div><?php

        return $real_date ;
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Booking Row  Support functions
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * Define Init Row settings parameters
		 *
		 * @param $row_settings array
		 *
		 * @return array
		 */
		private function get_init_row_settings( $row_settings ){

			// Initial  params
			$row_settings['is_matrix']                = $this->request_args['is_matrix'];
			$row_settings['start_hour_for_1day_view'] = 0;
			$row_settings['end_hour_for_1day_view']   = 24;
			$row_settings['limit_hours']              = 24;
			$row_settings['view_days_num']            = $this->request_args['view_days_num'];

			// Single booking resource
			if ( ! $row_settings['is_matrix'] ) {

				switch ($row_settings['view_days_num']) {
					case '90':
						$row_settings['days_num'] = 7;
						$row_settings['dwa'] = $this->week_days_titles['full'];
						$row_settings['time_selles_num']  = 1;
						break;
					case '365':
						$row_settings['days_num'] = 31;
						//$row_settings['days_num'] = intval( date_i18n('t',$row_settings['start_date']) );           		// num of days in the specific  month,  wchih  relative to $row_settings['real_date'] from  header        //FixIn: 7.0.1.47
						$row_settings['dwa'] = $this->week_days_titles['1'];
						$row_settings['time_selles_num']  = 1;
						break;
					default:  // 30
						$row_settings['days_num'] = 1;
						$row_settings['dwa'] = $this->week_days_titles['3'];
						$row_settings['time_selles_num']  = 24;//25;

						if ( isset( $this->request_args[ 'limit_hours' ] ) ) {     //FixIn: 7.0.1.14
							$row_settings['limit_hours'] = explode(',',$this->request_args[ 'limit_hours' ]);
							$row_settings['start_hour_for_1day_view'] = intval( $row_settings['limit_hours'][0] );
							$row_settings['end_hour_for_1day_view']   = intval( $row_settings['limit_hours'][1] );
							$row_settings['limit_hours'] = $row_settings['limit_hours'][1] - $row_settings['limit_hours'][0];
						}

						//$row_settings['view_days_num'] = 1;
						break;
				}

			} else {	// Multiple booking resources

				//$row_settings['view_days_num'] = 365;
				switch ($row_settings['view_days_num']) {
					case '1':
						$row_settings['days_num'] = 1;
						$row_settings['dwa'] = $this->week_days_titles['full'];
						$row_settings['time_selles_num']  = 24;

						if ( isset( $this->request_args[ 'limit_hours' ] ) ) {     //FixIn: 7.0.1.14
							$row_settings['limit_hours'] = explode(',',$this->request_args[ 'limit_hours' ]);
							$row_settings['start_hour_for_1day_view'] = intval( $row_settings['limit_hours'][0] );
							$row_settings['end_hour_for_1day_view']   = intval( $row_settings['limit_hours'][1] );
							$row_settings['limit_hours'] = $row_settings['limit_hours'][1] - $row_settings['limit_hours'][0];
						}

						break;
					case '7':
						$row_settings['days_num'] = 7;
						$row_settings['dwa'] = $this->week_days_titles['full'];
						$row_settings['time_selles_num']  = 1;
						break;
					case '60':
						$row_settings['days_num'] = 62;
						$row_settings['dwa'] = $this->week_days_titles['1'];
						$row_settings['time_selles_num']  = 1;
						break;
					case 'old_365':
						$row_settings['days_num'] = 365;
						$row_settings['time_selles_num']  = 1;
						$row_settings['dwa'] = $this->week_days_titles['1'];
						break;

					default:  // 30
						$row_settings['days_num'] = 32;
						$row_settings['days_num'] = intval( date_i18n('t',$row_settings['start_date']) );           // num of days in the specific  month,  wchih  relative to $row_settings['real_date'] from  header        //FixIn: 7.0.1.47
						$row_settings['dwa'] = $this->week_days_titles['3'];
						$row_settings['time_selles_num']  = 1;//25;
						break;
				}
			}

			return $row_settings;
		}


		/**
		 * Show time in correct  format - showing in Header,  and in time CELLs
		 *
		 * @param $time_milliseconds
		 * @param $view_days_num
		 */
		private function show_time_number_in_correct_format( $time_milliseconds , $view_days_num ){

			//FixIn: 8.1.3.34
			$bc_time_format = get_bk_option( 'booking_time_format' );
			if ( ! empty( $bc_time_format ) ) {                            //FixIn: 8.2.1.2
				$time_show = date_i18n( str_replace( ':i', '', get_bk_option( 'booking_time_format' ) ), mktime( $time_milliseconds, 0, 0 ) );
				echo ( $view_days_num < 31 ) ? $time_show : '';
			} else {
				echo( ( $view_days_num < 31 ) ? ( ( ( $time_milliseconds ) < 10 ? '0' : '' ) . ( $time_milliseconds ) . '<sup>:00</sup>' ) : '' );
			}

		}


		/**
		 * Is show Day View ( showing days || times )
		 * @param $row_settings
		 *
		 * @return bool
		 */
		private function is_show_day_view( $row_settings ){
		    if (
					   ( ( 30 == $row_settings['view_days_num'] ) && ( ! $row_settings['is_matrix'] ) )			// Day View for Single
					|| ( (  1 == $row_settings['view_days_num'] ) && (   $row_settings['is_matrix'] ) )			// Day View for Matrix
				) {
		    	return  true;
			} else {
		    	return  false;
			}
		}


		/**
		 * Check, if starting new booking (something changed) relative to  previous CELL
		 *
		 * @param array $bookings_in_cell
		 * @param array $previous_bookings_in_cell
		 *
		 * @return bool
		 */
		private function is_start_new_booking_cell( $bookings_in_cell, $previous_bookings_in_cell ) {

			$bookings_in_cell          = implode( '|', $bookings_in_cell );
			$previous_bookings_in_cell = implode( '|', $previous_bookings_in_cell );

			if ( $bookings_in_cell !== $previous_bookings_in_cell ) {
				return true;
			} else {
				return false;
			}
		}


		/**
		 * Get Text for booking Title in PIPELINE
		 *
		 * @param $bk_id
		 * @param $bookings
		 *
		 * @return string
		 */
		private function get_booking_title_for_timeline( $bk_id, $bookings ){

			$text_in_day_cell = '';

			if ( $this->is_frontend ) $what_show_in_day_template = get_bk_option( 'booking_default_title_in_day_for_timeline_front_end' );
			else                      $what_show_in_day_template = get_bk_option( 'booking_default_title_in_day_for_calendar_view_mode' );

			if ( function_exists( 'get_title_for_showing_in_day' ) ) {
				$text_in_day_cell .= esc_textarea( get_title_for_showing_in_day( $bk_id, $bookings, $what_show_in_day_template ) );						//FixIn: 7.1.1.2
			} else {
				if ( ( ! $this->is_frontend ) && ( isset( $bookings[ $bk_id ]->form_data['_all_fields_']['name'] ) ) )
					$text_in_day_cell .= $bk_id . ':' . esc_textarea( $bookings[$bk_id]->form_data['_all_fields_']['name'] );       // Default Free		//FixIn: 7.1.1.2
			}

			return $text_in_day_cell;
		}


		/**
		 * Title, when  mouse-over booking Pipeline (bar with  background)
		 *
		 * @param $booking_id
		 * @param $row_settings
		 */
		private function show_booking_title_for_pipeline_bar( $booking_id, $row_settings ){

			//FixIn: 8.7.1.4
			$bk_title = '';

			$is_date = wpbc_get_date_in_correct_format( date_i18n( "Y-m-d", $row_settings['real_date'] ) );
			if ( ( is_array( $is_date ) )  && ( ! empty( $is_date ) ) ) {
				$bk_title = $is_date[0];
			}

			$bk_title .= " \n" . $this->get_booking_title_for_timeline( $booking_id, $row_settings['bookings'] );

			$bk_title .= " \n" . strip_tags( wpbc_get_short_dates_formated_to_show( $row_settings['bookings'][ $booking_id ]->dates_short ) )  ;

			?><a  href="javascript:void(0)"
				  class="in_cell_date_booking_pipeline_a"
				  title="<?php echo str_replace( '"', "", $bk_title ); ?>"
			><?php
					?><div class="in_cell_date_booking_pipeline_a_sizer"></div><?php
			?></a><?php
		}


		/**
		 * Show booking TITLE in  PIPELINE
		 *
		 * @param $bookings_in_cell
		 * @param $row_settings
		 */
		private function show_booking_title_for_timeline( $bookings_in_cell, $row_settings ){

			$bk_a_title_arr    = array();
			$popup_content_arr = array();
			$popup_title_arr = array();

			$is_show_popover_in_timeline  = wpbc_is_show_popover_in_flex_timeline( $this->is_frontend, $this->request_args['booking_hash'] );    	//FixIn: 8.1.3.5

			foreach ( $bookings_in_cell as $booking_id ) {

				$bk_a_title = $this->get_booking_title_for_timeline( $booking_id, $row_settings['bookings'] );
				$bk_a_title = htmlspecialchars_decode( $bk_a_title, ENT_NOQUOTES );        //FixIn: 8.7.11.5
				$bk_a_title_arr[] = $bk_a_title;

				$title_in_day =  $title =  $title_hint = '';

				if ( $is_show_popover_in_timeline ) {
					$popup_content       = $this->wpbc_get_booking_info_4_popover( $booking_id, $row_settings['bookings'], $row_settings['booking_types'] );


					$popup_title_arr[]   = $popup_content['title'];
					$popup_content_arr[] = $popup_content['content'];
				}
				//$popup_content_arr[] = $bk_a_title . ': ' .  strip_tags( wpbc_get_short_dates_formated_to_show( $row_settings['bookings'][ $booking_id ]->dates_short ) )  ;
			}

			// Title A link
			$bk_a_title__text =  implode( ', ', $bk_a_title_arr );

			if ( strlen( $bk_a_title__text ) > 20 ) {
				$bk_a_title__text = substr( $bk_a_title__text, 0, 20 ) . '...';
			}
			if ( count( $bookings_in_cell ) > 1 ) {
				$bk_a_title__text = '<sup>[' . count( $bookings_in_cell ) . ']</sup> ' . $bk_a_title__text;
			}


			if ( ! $this->is_frontend ) { $line_separator = '<div class=\'clear\'></div>'; }
			else 						{ $line_separator = ''; }
			$popup_title_arr   = implode( $line_separator, $popup_title_arr );

			$line_separator = '<hr class="wpbc_tl_popover_booking_separator" />';
			$popup_content_arr = implode( $line_separator, $popup_content_arr );

			// Booking CELL Title
			?><a href="javascript:void(0)"
			 	 class="<?php  echo implode(' ', array(
									'in_cell_date_booking_title',
									( $is_show_popover_in_timeline ) ? 'popover_bottom' : '',
									( $is_show_popover_in_timeline ) ? 'popover_click' : '',
					 				( count( $bookings_in_cell ) > 1 ) ? 'several_bookings_in_cell' : ''
						 )); ?>"
				 <?php if ( $is_show_popover_in_timeline ) { ?>
					 data-content="<?php echo str_replace( '"', "", $popup_content_arr ); ?>"
					 data-original-title="<?php echo str_replace( '"', "", $popup_title_arr ); ?>"
				 <?php } ?>
			><?php
				echo $bk_a_title__text;
			?></a><?php
		}


		/**
		 * Get array of CSS classes for booking bar
		 * @param int $booking_id
		 * @param array $bookings
		 *
		 * @return array
		 */
		function get_booking_class_arr( $booking_id, $row_settings , $real_date, $time_hour ){

			$bookings = $row_settings['bookings'];

			$class_arr = array();

			if (   ( ! empty( $booking_id ) ) && isset( $bookings[ $booking_id ] ) ) {

				// Appoved | Pending
				if ( count( $bookings[ $booking_id ]->dates ) > 0 ) {
					$is_approved = $bookings[ $booking_id ]->dates[0]->approved;
				} else {
					$is_approved = 0;
				}
				$class_arr[] = $is_approved ? 'approved_booking' : 'pending_booking';

				/////////////////////////////////////////////////////////////////

				if ( isset( $bookings[ $booking_id ]->trash ) ) {
					$is_trash = $bookings[ $booking_id ]->trash;
				} else {
					$is_trash = false;
				}
				$class_arr[] = $is_trash ? 'booking_trash' : '';

				/////////////////////////////////////////////////////////////////

				if (    ( isset( $bookings[ $booking_id ]->form_data['email'] ) )
				     && ( 'admin@blank.com' == $bookings[ $booking_id ]->form_data['email'] )
				) {
					$is_blank_bookings = true;
				} else {
					$is_blank_bookings = false;
				}
				$class_arr[] = $is_blank_bookings ? 'booking_blank' : '';

				/////////////////////////////////////////////////////////////////

				if ( date_i18n( 'Y.m.d' ) > date_i18n( "Y.m.d", $real_date ) ) {
					$class_arr[] = 'past_date';
				}
				if ( date_i18n( 'Y.m.d' ) == date_i18n( "Y.m.d", $real_date ) ) {
					$class_arr[] = 'today_date';
				}

				if ( $this->is_show_day_view( $row_settings ) ) {
					if ( ( date_i18n( 'm.d.Y' ) == date_i18n( "m.d.Y", $real_date ) )    // Today Date
					     && ( intval( date_i18n( 'H' ) ) > intval( $time_hour ) ) ) {
						$class_arr[] = 'past_date';
					}
				}
				/////////////////////////////////////////////////////////////////

				$css_class_additional = apply_filters( 'wpbc_timeline_booking_header_css', '', $booking_id, $bookings );           //FixIn: 7.0.1.41
                $class_arr[] = $css_class_additional;
			}

			// Remove empty values from  array
			$class_arr = array_filter( $class_arr );

			return $class_arr;
		}


		/**
		 * Show Date cell  :: 3 variants ::   Date Number | Booking Pipeline | Booking Title
		 * @param $row_settings
		 */
		private function show_day_cell( $row_settings ){

			$data_in_previous_cell = $this->data_in_previous_cell;

			$yy = date_i18n( "Y", $row_settings[ 'real_date' ] );    // 2012
			$mm = date_i18n( "m", $row_settings[ 'real_date' ] );    // 09
			$dd = date_i18n( "d", $row_settings[ 'real_date' ] );    // 31
			$ww = date_i18n( "N", $row_settings[ 'real_date' ] );    // 7
			$day_week = $row_settings[ 'dwa' ][$ww];          		 // Su

			$row_settings['day_filter_id'] = $yy . '-' . $mm . '-' . $dd;


			// <editor-fold     defaultstate="collapsed"                        desc=" = $bookings_in_day_cell - Array of booking ID  - Sorted by Time in this date cell = "  >

			$bookings_in__times_arr = array();
			/**
			 * $bookings_in__times_arr    =    array(  [0] => array( [0] => 918, [1] => 917 ),    [3600] => array( [0] => 918 [1] => 917 ), ...
															   [82800] => Array (
																	[0] => 918
																	[1] => 917
																)
										)
			*/
			if ( ! empty ( $row_settings['time_array_new'][ $row_settings['day_filter_id'] ] ) ) {						// $row_settings['time_array_new'][ '2019-07-19' ]
				$bookings_in__times_arr = $row_settings['time_array_new'][ $row_settings['day_filter_id'] ];
			}

			$bookings_in_day_cell = array();
			if ( ! empty ( $bookings_in__times_arr ) ) {
				foreach ( $bookings_in__times_arr as $times_milliseconds => $bookings_in_times_arr ) {

					foreach ( $bookings_in_times_arr as $booking_id_in_time_interval ) {

						if ( ! in_array( $booking_id_in_time_interval, $bookings_in_day_cell ) ) {
							/**
							 * Array ( 	[0] => 15 		[1] => 16 		[2] => 13 		[3] => 14  )
							 */
							$bookings_in_day_cell[] = $booking_id_in_time_interval;
						}
					}
				}
			}
			// </editor-fold>

//debuge($bookings_in__times_arr);
			// <editor-fold     defaultstate="collapsed"                        desc=" = $cell_css[] - Define CSS classes of day  cell = "  >

			$cell_css = array();
				$cell_css[] = 'flex_tl_day_cell';
				$cell_css[] = 'flex_tl_weekday' . $ww;
				$cell_css[] = $row_settings['day_filter_id'];

			if ( date_i18n( 'm.d.Y' ) == date_i18n( "m.d.Y", $row_settings['real_date'] ) ) {
				$cell_css[] = 'today_date';
			} else if ( date_i18n( 'Y.m.d' ) > date_i18n( "Y.m.d", $row_settings['real_date'] ) ) {
				$cell_css[] = 'past_date';
			}

			if ( ( $data_in_previous_cell['previous_month'] != $mm ) || ( 1 == $dd ) ) {

				$data_in_previous_cell['previous_month'] = $mm;
				$cell_css[] = 'new_month';
			}

			if ( ! empty( $bookings_in_day_cell ) ) {
				$cell_css[] = 'exist_booking_in_cell';
			} else {
				$cell_css[] = 'no_booking_in_cell';
			}

			// </editor-fold>


			?><div  id="cell_<?php  echo $row_settings[ 'current_resource_id' ] . '_' . $row_settings['day_filter_id'] ; ?>"
					class="<?php  echo implode(' ', $cell_css ); ?>"
			><?php

				////////////////////////////////////////////////////////////////////////////////////////
				// T i m e   c e l l s
				////////////////////////////////////////////////////////////////////////////////////////
				$time_selles_num = $row_settings['time_selles_num'];
				$tm              = floor( 24 / $time_selles_num );


				?><div class="<?php  echo implode(' ', array(
															'in_cell_time_section_in_day',
															'flex_time_section_in_day_booking',
															'flex_time_in_days_num_' . $row_settings['view_days_num']
					 				)); ?>"
				><?php

						for ( $tt = 0; $tt < $time_selles_num; $tt++ ) {

							// Get bookings for time-slot
							if ( $this->is_show_day_view( $row_settings ) ) {

								$time_ms                   = $tt * 60 * 60;
								$bookings_in_this_time_arr = empty( $bookings_in__times_arr[ $time_ms ] ) ? array() : $bookings_in__times_arr[ $time_ms ];
								$is_start_new_booking      = $this->is_start_new_booking_cell( $bookings_in_this_time_arr, $data_in_previous_cell['bookings_in_cell'] );

								$bookings_in_day_cell = $bookings_in_this_time_arr;

							} else { // Get bookings for a day

								$is_start_new_booking = $this->is_start_new_booking_cell( $bookings_in_day_cell, $data_in_previous_cell['bookings_in_cell'] );
							}

							// Skip timeslots for day  view
							if ( $this->is_show_day_view( $row_settings ) ) {
								if ( ( $tt < $row_settings['start_hour_for_1day_view'] ) || ( $tt > $row_settings['end_hour_for_1day_view'] ) ) {
									continue;
								}
							}


							$today_time_css_class='';
							if ( date_i18n( 'Y.m.d' ) == date_i18n( "Y.m.d", $row_settings[ 'real_date' ] ) ) {
								if ( $this->is_show_day_view( $row_settings ) ) {
									if ( ( date_i18n( 'm.d.Y' ) == date_i18n( "m.d.Y", $row_settings[ 'real_date' ] ) )    // Today Date
									     && ( intval( date_i18n( 'H' ) ) == intval( $tt ) ) ) {
										$today_time_css_class = 'today_time';
									}
								}

							}

							?><div class="<?php  echo 'in_cell_time_hour time_hour' . ( $tt * $tm ).  ' ' . $today_time_css_class; ?>" ><?php


								if ( 'show_dates' == $row_settings['what_to_show'] ) {

									 ?>
									 <div class="in_cell_date_container in_cell_date_container_<?php echo $row_settings['what_to_show'] ?>"><?php
									 //if ( empty( $bookings_in_day_cell ) ) {
										if ( $this->is_show_day_view( $row_settings ) ) {
											$this->show_time_number_in_correct_format( $tt * $tm , $row_settings['view_days_num'] );
										} else {
											echo $dd;
										}
									 //}
									 ?></div><?php
								}


								if ( 'show_bookings' == $row_settings['what_to_show'] ) {

									if ( ! empty( $bookings_in_day_cell ) ) {

										$booking_id_title = array();

										?>
										<div class="in_cell_date_container in_cell_date_container_<?php echo $row_settings['what_to_show'] ?>"><?php

											foreach ( $bookings_in_day_cell as $booking_id ) {

												$booking_css_class_arr = $this->get_booking_class_arr( $booking_id, $row_settings, $row_settings['real_date'], $tt );

												?>
												<div  class="<?php echo implode( ' ', array(
																			'booking_id',
																			'booking_id_' . $booking_id,
																			$is_start_new_booking ? 'start_new_booking' : '',
														) );
														echo ' ' . implode( ' ', $booking_css_class_arr );
														?>"
												><?php

													// Booking CELL background
													$this->show_booking_title_for_pipeline_bar( $booking_id, $row_settings );    // Title, when  mouse-over booking Pipeline (bar with  background)

												?></div><?php
											}
										?></div><?php
									}
								}


								if ( 'show_booking_titles' == $row_settings['what_to_show'] ) {

									if ( ! empty( $bookings_in_day_cell ) ) {

										// Title of new booking(s)
										if ( $is_start_new_booking ) {

											?><div  class="<?php echo implode( ' ', array(
																			'in_cell_date_container',
																			'in_cell_date_container_booking_id_title',
																			'in_cell_date_container_' . $row_settings['what_to_show']
														) );
														echo ' booking_id_' . implode( '_', $bookings_in_day_cell );
													?>"
											><?php

												$this->show_booking_title_for_timeline( $bookings_in_day_cell, $row_settings );

											?></div><?php
										}
									}
								}

							?></div><?php

							$data_in_previous_cell['bookings_in_cell'] = $bookings_in_day_cell;
					   }

				?></div><?php

			?></div><?php


			$this->data_in_previous_cell = $data_in_previous_cell;
		}

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Booking R O W
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /** Row */
    public function wpbc_show_timeline_booking_row( $start_date, $booking_arr = array() ) {

	    $row_settings = array( 'start_date' => $start_date );

	    $row_settings = $this->get_init_row_settings( $row_settings );

        if ( $row_settings['start_date'] !== false ) {
        		list( $start_year, $start_month, $start_day ) = explode( '-', date_i18n( 'Y-m-d', $row_settings['start_date'] ) );
        } else {
            if ( ! empty( $this->request_args['scroll_start_date'] ) )
                list( $start_year, $start_month, $start_day ) = explode( '-', $this->request_args['scroll_start_date'] );   			// scroll_start_date=2013-07-01
            else
                list( $start_year, $start_month, $start_day ) = explode( '-', date_i18n( 'Y-n-j' ) );
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Dates / Times  Line
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$this->data_in_previous_cell['previous_booking_id'] = false;

		$saved_previos_data_cell = $this->data_in_previous_cell;

	    foreach ( array( 'show_dates', 'show_bookings', 'show_booking_titles' ) as $what_to_show ) {

			$this->data_in_previous_cell = $saved_previos_data_cell;

		    ?><div id="flex_resource_row_<?php echo $what_to_show . '_' . $booking_arr['current_resource_id']; ?>"
				   class="<?php  echo implode(' ', array(
								'flex_tl_dates_bar',
								'flex_tl_row_bar_' . $what_to_show ,
								$row_settings['is_matrix'] ? 'flex_tl_matrix_resources' : 'flex_tl_single_resource'
						) ); ?>"
			 ><?php

					for ( $d_inc = 0; $d_inc < $row_settings['days_num']; $d_inc ++ ) {

						$row_settings['real_date'] = mktime( 0, 0, 0, $start_month, ( $start_day + $d_inc ), $start_year );

						$day_cell_params = array_merge( $row_settings, $booking_arr );

						$day_cell_params['what_to_show'] = $what_to_show;

						$this->show_day_cell( $day_cell_params );
					}

		    ?></div><?php
	    }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Timeline
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /** Show Structure of the TimeLine */
    public function wpbc_show_timeline( $dates_array, $bookings, $booking_types, $time_array_new = array() ){
//debuge( $time_array_new['2019-07-24']);die;
        // Skip showing rows of booking resource(s) in TimeLine or Calendar Overview, if no any exist booking(s) for current view
        $booked_booking_resources = array();                                    //FixIn: 7.0.1.51  
        if ( ! empty( $this->request_args['only_booked_resources'] ) ) {
           
            foreach ( $bookings as $single_booking ) {

                if ( ! empty( $single_booking->booking_type ) )
                    $booked_booking_resources[] = $single_booking->booking_type;

                foreach ( $single_booking->dates as $booking_date_obj ) {
                    if ( ( isset( $booking_date_obj->type_id ) ) && ( ! empty( $booking_date_obj->type_id ) ) )
                        $booked_booking_resources[] = $booking_date_obj->type_id;
                }    
            }
            $booked_booking_resources = array_unique( $booked_booking_resources );
        }

        $view_days_num  = $this->request_args['view_days_num'];                 // Get start date and number of rows, which is depend from the view days mode        
        $is_matrix      = $this->request_args['is_matrix'];
        $scroll_day     = 0;
        $scroll_month   = 0;
        $start_year     = date_i18n( "Y" );
        $start_month    = date_i18n( "m" );                                          // 09            
                        
        if ( ! empty( $this->request_args['scroll_start_date'] ) ) {            // scroll_start_date=2013-07-01
                                                                                // Set the correct  start  date, if was selected the stard date different from the today  in the Filters Tab.
            list( $start_year, $start_month, $start_day ) = explode( '-', $this->request_args['scroll_start_date'] );            
        }
       
        ////////////////////////////////////////////////////////////////////////
        // Get Start Date and Scroll Day/Month Variables 
        ////////////////////////////////////////////////////////////////////////
        if ( $is_matrix ) {                                      // MATRIX VIEW
            
            $bk_resources_id = explode( ',', $this->request_args['wh_booking_type'] );
            $max_rows_number = count( $bk_resources_id );

            switch ( $view_days_num ) {
                case '1':
                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = $this->request_args['scroll_day'];
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = date_i18n( "d" );                          //FixIn: 7.0.1.13
                    break;

                case '30':
                case '60':
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = $this->request_args['scroll_month'];
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = 1;
                    break;

                case '7':                                                       // 7 Week - start from Monday (or other start week day)
                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = $this->request_args['scroll_day'];
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = date_i18n( "d" );
                    $start_week_day_num = date_i18n( "w" );
                    $start_day_weeek = get_bk_option( 'booking_start_day_weeek' ); //[0]:Sun .. [6]:Sut

                    if ( $start_week_day_num != $start_day_weeek ) {
                        for ( $d_inc = 1; $d_inc < 8; $d_inc++ ) {                // Just get week  back
                            
                            $real_date = mktime( 0, 0, 0, $start_month, ($start_day - $d_inc ), $start_year );

                            $start_week_day_num = date_i18n( "w", $real_date );
                            if ( $start_week_day_num == $start_day_weeek ) {
                                $start_day = date_i18n( "d", $real_date );
                                $start_year = date_i18n( "Y", $real_date );
                                $start_month = date_i18n( "m", $real_date );
                                $d_inc = 9;
                            }
                        }
                    }
                    break;

                default:  //30
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = $this->request_args['scroll_month'];
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = 1;
                    break;
            }
            
        } else {                                                                // SINGLE Resource VIEW
            
            switch ( $view_days_num ) {
                case '90':
                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = $this->request_args['scroll_day'];
                    else
                        $scroll_day = 0;

                    $max_rows_number = 12;
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = date_i18n( "d" );
                    $start_week_day_num = date_i18n( "w" );
                    $start_day_weeek = get_bk_option( 'booking_start_day_weeek' ); //[0]:Sun .. [6]:Sut

                    if ( $start_week_day_num != $start_day_weeek ) {
                        for ( $d_inc = 1; $d_inc < 8; $d_inc++ ) {                // Just get week  back
                            $real_date = mktime( 0, 0, 0, $start_month, ($start_day - $d_inc ), $start_year );

                            $start_week_day_num = date_i18n( "w", $real_date );
                            if ( $start_week_day_num == $start_day_weeek ) {
                                $start_day = date_i18n( "d", $real_date );
                                $start_year = date_i18n( "Y", $real_date );
                                $start_month = date_i18n( "m", $real_date );
                                $d_inc = 9;
                            }
                        }
                    }
                    break;

                case '365':
                    if ( isset( $this->request_args['scroll_month'] ) )
                        $scroll_month = $this->request_args['scroll_month'];
                    else
                        $scroll_month = 0;
                    $max_rows_number = 12;
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = 1;
                    break;

                default:  // 30
                    if ( isset( $this->request_args['scroll_day'] ) )
                        $scroll_day = $this->request_args['scroll_day'];
                    else
                        $scroll_day = 0;

                    $max_rows_number = 31;
                    if ( empty( $this->request_args['scroll_start_date'] ) )
                        $start_day = date_i18n( "d" );                          //FixIn: 7.0.1.13
                    break;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////

//TODO: Start  from  replacing Table to DIV flex elements here

        ?><div class="flex_timeline_frame<?php
						if ( $this->is_frontend ) echo ' wpbc_timeline_front_end' ?> flex_frame_view_days_num_<?php echo $view_days_num;
						 if ($is_matrix) { echo ' flex_tl_matrix_resources '; } else { echo ' flex_tl_single_resource '; }
				   ?>">
            <div class="flex_tl_table">
                <div class="flex_tl_table_header">
                    <?php 
                    if ( $this->is_frontend ) {
                        ?><div class="flex_tl_collumn_2"><?php
                        
                            $title =  apply_bk_filter('wpdev_check_for_active_language', $this->timeline_titles['header_title'] );
                            
                            $params_nav = array();
                            $params_nav['title'] = $title;
                            $this->client_navigation( $params_nav );
                            
                        ?></div><?php
                        
                    } else {
                        ?>
                        <div class="flex_tl_collumn_1"><?php
                                $title =  apply_bk_filter('wpdev_check_for_active_language', $this->timeline_titles['header_column1'] );
                                echo $title;          // Resources
                        ?></div>
                        <div class="flex_tl_collumn_2"><?php
                            $title =  apply_bk_filter('wpdev_check_for_active_language', $this->timeline_titles['header_column2'] );
                            echo $title;              // Dates
                        ?></div>
                    <?php } ?>
                </div>
                <div class="flex_tl_table_titles">
                    <div class="flex_tl_collumn_1"></div>
                    <div class="flex_tl_collumn_2"><?php
                        // Header above the calendar table
                        $real_date = mktime( 0, 0, 0, ($start_month ), $start_day, $start_year );

                        if ( $is_matrix ) {    // MATRIX VIEW                    
                            switch ( $view_days_num ) {                                        // Set real start date for the each rows in calendar
                                case '1':
                                case '7':
                                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                                    break;

                                case '30':
                                case '60':
                                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                                    break;

                                default:  // 30
                                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                                    break;
                            }
                        } else {                            // Single Resource View
                            switch ( $view_days_num ) {                                        // Set real start date for the each rows in calendar
                                case '90':
                                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                                    break;

                                case '365':
                                    $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                                    break;

                                default:  // 30
                                    $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                                    break;
                            }
                        }

                        $this->wpbc_show_timeline_header_row( $real_date );
                        ?>
                    </div>
                </div><?php
                
                for ( $d_inc = 0; $d_inc < $max_rows_number; $d_inc++ ) {

                    // Skip showing rows of booking resource(s) in TimeLine or Calendar Overview, if no any exist booking(s) for current view
                    if ( ! empty( $this->request_args['only_booked_resources'] ) ) {                         //FixIn: 7.0.1.51  

                        if ( $is_matrix ) $resource_id = $bk_resources_id[$d_inc];
                        else              $resource_id = $this->request_args['wh_booking_type'];  // Request from  GET or REQUEST

                        if ( ! in_array( $resource_id, $booked_booking_resources ) ) {
                            continue;
                        }
                    }                    
                    
                    
                    // Ger Start Date to real_date  variabale  /////////////////////
                    if ( $is_matrix ) {    // MATRIX VIEW                    
                        switch ( $view_days_num ) {                                        // Set real start date for the each rows in calendar
                            case '1':
                            case '7':
                                $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $scroll_day ), $start_year );
                                break;

                            case '30':
                            case '90':
                                $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                                break;

                            default:  // 30
                                $real_date = mktime( 0, 0, 0, ($start_month + $scroll_month ), $start_day, $start_year );
                                break;
                        }
                    } else {                            // Single Resource View
                        switch ( $view_days_num ) {                                        // Set real start date for the each rows in calendar
                            case '90':
                                $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $d_inc * 7 + $scroll_day ), $start_year );
                                break;

                            case '365':
                                $real_date = mktime( 0, 0, 0, ($start_month + $d_inc + $scroll_month ), $start_day, $start_year );
                                break;

                            default:  // 30
                                $real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $d_inc + $scroll_day ), $start_year );
                                break;
                        }
                    }
                    ////////////////////////////////////////////////////////////////
                    ?>
                    <div class="flex_tl_table_row_bookings">
                        <div class="flex_tl_collumn_1"><?php

                                // Title in first collumn of the each row in calendar //////
                                if ( ( $is_matrix ) && ( isset( $bk_resources_id[$d_inc] ) ) && (isset( $booking_types[$bk_resources_id[$d_inc]] )) ) {  // Matrix - resource titles

                                    $resource_value = $booking_types[$bk_resources_id[$d_inc]];
                                    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), array( 'wh_booking_type' ) );                                                                       
                                    
                                    ?><div class="flex_tl_resource_title <?php
                                                    if ( isset( $resource_value->parent ) ) { if ( $resource_value->parent == 0 ) { echo 'parent'; } else { echo 'child'; } }
                                        ?> "><?php 
                                            if ( $this->is_frontend ) {

                                                if ( ( isset( $this->options['resource_link'] ) ) && ( isset( $this->options['resource_link'][ $resource_value->booking_type_id ] ) ) ){        //FixIn: 7.0.1.50
                                                    
													?><a href="<?php echo $this->options['resource_link'][ $resource_value->booking_type_id ]; ?>" ><?php //FixIn: 7.2.1.14
                                                }

                                                echo apply_bk_filter('wpdev_check_for_active_language', $resource_value->title );       //FixIn: 7.0.1.11
                                                
                                                if ( ( isset( $this->options['resource_link'] ) ) && ( isset( $this->options['resource_link'][ $resource_value->booking_type_id ] ) ) ){        //FixIn: 7.0.1.50  
                                                    ?></a><?php 
                                                }
                                            } else {
                                            ?><a href="<?php echo $bk_admin_url . '&wh_booking_type=' . $bk_resources_id[$d_inc]; ?>"
												 title="<?php echo apply_bk_filter('wpdev_check_for_active_language', $resource_value->title ); ?>"
											  ><?php
                                                echo apply_bk_filter('wpdev_check_for_active_language', $resource_value->title ); 												
                                            ?></a><?php 
                                            }
                                  ?></div><?php
                                        
                                } else {    // Single Resource - Dates titles
                                    
                                    ?><div class="flex_tl_resource_title"><?php
                                    
                                    switch ( $view_days_num ) {
                                        case '90':
                                            $end_real_date = mktime( 0, 0, 0, $start_month, ( $start_day + $d_inc * 7 + $scroll_day ) + 6, $start_year );
                                            $date_format = ' j, Y'; //get_bk_option( 'booking_date_format');
                                            echo __( date_i18n( "M", $real_date ) ) . date_i18n( $date_format, $real_date ) . ' - ' . __( date_i18n( "M", $end_real_date ) ) . date_i18n( $date_format, $end_real_date );
                                            break;

                                        case '365':
                                            echo __( date_i18n( "F", $real_date ) ) . ', ' . date_i18n( "Y", $real_date );
                                            break;

                                        default:  // 30
                                            //$date_format = 'd / m / Y';
                                            $date_format = get_bk_option( 'booking_date_format' );                           //FixIn:5.4.5.13
											$ww = date_i18n( "N", $real_date );    //7
                                            ?>
												<div class="flex_tl_resource_title_dates_container">
													<div class="flex_tl_resource_title_dates_days  flex_tl_weekday<?php echo $ww; ?>"><?php echo  date_i18n( $date_format, $real_date ); ?></div>
													<div class="flex_tl_resource_title_dates_weeks flex_tl_weekday<?php echo $ww; ?>"><?php echo __( date_i18n( "D", $real_date ) ); ?></div>
												</div>
											<?php
                                            break;
                                    }
                                    
                                    ?></div><?php      
                                }
                            ?>
                        </div>
                        <div  class="flex_tl_collumn_2"><?php

	                            if ( $is_matrix ) {

	                            	$this->reset_data_in_previous_cell();

		                            $resource_id = $bk_resources_id[ $d_inc ];

	                            } else {
		                            $resource_id = $this->request_args['wh_booking_type'];
	                            }  // Request from  GET or REQUEST

								$booking_arr = array(
													'current_resource_id' => ( ( ! empty( $resource_id ) ) ? $resource_id : 1 ),		// Remove dates and Times from  the arrays, which is not belong to the $booking_arr['current_resource_id'] We do not remove it only, when  the $booking_arr['current_resource_id'] - is empty - OLD ALL Resources VIEW
													'booked_dates_array'  => $dates_array,
													'bookings'            => $bookings,
													'booking_types'       => $booking_types,
													'time_array_new'      => $time_array_new
												);

								/**
								 * FROM:   [2019-07-19] => Array ( [0] => Array (
								 *                                    [id] => 19
																	  [resource] => 3
																  )
															  [1] => Array (
																	  [id] => 19
																	  [resource] => 3
																  ), ....
								 * TO
								 *
								 *  [2019-07-19] => Array (
															* [0] => 19
															* [1] => 19
															* [2] => 18
															* [3] => 18 ...
								 */
								$booking_arr['booked_dates_array'] = $this->wpbc_dates_only_of_specific_resource(
																				$booking_arr['booked_dates_array'],
																				$booking_arr['current_resource_id'],
																				$booking_arr['bookings']
																			);
								/**
								 * FROM:   [2019-07-19] => Array (   ...  , [28800] => Array(
																							[0] => Array (
																									[id] => 15
																									[resource] => 3
																								)
																							[1] => Array (
																									[id] => 16
																									[resource] => 3
																								)
																							[2] => Array (
																									[id] => 13
																									[resource] => 3
																								) ....
								 * TO
								 *   [2019-07-19] => Array (   ...  , [28800] => Array(
																							[0] => 15
																							[1] => 16
																							[2] => 13
																						)
								 */
								$booking_arr['time_array_new']     = $this->wpbc_times_only_of_specific_resource(
																				$booking_arr['time_array_new'],
																				$booking_arr['current_resource_id'],
																				$booking_arr['bookings']
																			);

                                $this->wpbc_show_timeline_booking_row(  $real_date, $booking_arr );


                            ?>
                        </div>
                    </div><?php
                }

        ?></div></div><?php
    }


    /**
	 * Show timeline
     *  All  parameters must  be defined.
     */
    public function show_timeline() {

    	?><div class="flex_tl_table_loading">
				<span class="glyphicon glyphicon-refresh wpbc_spin"></span>
				<span><?php _e('Loading','booking'); ?>...</span>
		</div><?php


        $this->wpbc_show_timeline( $this->dates_array, $this->bookings, $this->booking_types, $this->time_array_new );

        ?><script type="text/javascript">
			jQuery( '.flex_tl_table_loading').hide()
			jQuery( '.flex_tl_table' ).show();
       	 </script><?php
    }


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// P O P O V E R
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//TODO: Refactor this: 2019-07-29 09:01

    /**
	 * Get Data for Popover
     * 
     * @param int $bk_id
     * @param array $bookings
     * @param array $booking_types
     * @param string $text_in_day_cell
     * @param string $header_title
     * @param string $content_text
     * 
     * @return array
     */
    public function wpbc_get_booking_info_4_popover( $bk_id, $bookings, $booking_types ){

		if ( isset( $bookings[ $bk_id ] ) ) {
			$bookings[ $bk_id ]->form_show = str_replace( "&amp;", '&', $bookings[ $bk_id ]->form_show );				//FixIn:7.1.2.12
		}

	    if ( count( $bookings[ $bk_id ]->dates ) > 0 ) {
		    $is_approved = $bookings[ $bk_id ]->dates[0]->approved;
	    } else {
		    $is_approved = 0;
	    }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Title
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $header_title = '<div class=\'popover-title-id\' > ID: ' . $bk_id . '</div>'; // ID


		$is_can = true; //current_user_can( 'edit_posts' );

		if (
			       ( ( $is_can ) && ( ! $this->is_frontend ) )
				|| ( ( $this->is_frontend ) && ( ! empty( $this->request_args['booking_hash'] ) ) )
		) {

			// Buttons
			$header_title .= '<div class=\'popover-title-buttons control-group timeline_info_bk_actionsbar_' . $bk_id . '\' >';


				if ( ( ! $this->is_frontend ) && ( $is_can ) ) {
					// Link
					$header_title .= '<a 	class=\'button button-secondary\' 
											title=\'' . esc_js( str_replace( "'", '', __( 'Booking Listing', 'booking' ) ) ) . '\' 
											href=\''.wpbc_get_bookings_url( true, false ).'&wh_booking_id='.$bk_id.'&view_mode=vm_listing&tab=actions\' ><i class=\'glyphicon glyphicon-screenshot\'></i></a>';
					//Edit
					if ( class_exists( 'wpdev_bk_personal' ) ) {
						$bk_url_add = wpbc_get_new_booking_url( true, false );
						$bk_hash = (isset( $bookings[$bk_id]->hash )) ? $bookings[$bk_id]->hash : '';
						$bk_booking_type = $bookings[$bk_id]->booking_type;
						$edit_booking_url = $bk_url_add . '&booking_type=' . $bk_booking_type . '&booking_hash=' . $bk_hash . '&parent_res=1';
						$header_title .= '<a 	class=\'button button-secondary\' 
												title=\'' . esc_js( str_replace( "'", '', __( 'Edit', 'booking' ) ) ) . '\' 
												href=\'' . $edit_booking_url . '\' onclick=\'\' ><i class=\'glyphicon glyphicon-edit\'></i></a>';

						// Print
						if ( class_exists( 'wpdev_bk_biz_s' ) )
							$header_title .= '<a href=\'javascript:void(0)\' 
												 onclick=\'javascript: wpbc_print_specific_booking_for_timeline( '.$bk_id.' );\'
												 class=\'tooltip_top button-secondary button timeline_button_print\'
												 title=\'' . esc_js( str_replace( "'", '', __( 'Print', 'booking' ) ) ) . '\'
							 ><i class=\'glyphicon glyphicon-print\'></i></a>';

						$header_title .= '<span class=\'wpbc-buttons-separator\'></span>';
					}
					// Trash
					//$header_title .= '<a class=\'button button-secondary\' href=\'javascript:;\' onclick=\'javascript:delete_booking(' . $bk_id . ', ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-trash\'></i></a>';
					//FixIn: 6.1.1.10
					$is_trash = $bookings[$bk_id]->trash;

					// Trash
					$header_title .= '<a class=\'button button-secondary trash_bk_link'.(( $is_trash)?' hidden_items ':'').'\'  
										 title=\'' . esc_js( str_replace( "'", '', __( 'Trash / Reject', 'booking' ) ) ) . '\'
										 href=\'javascript:;\' onclick=\'javascript:if ( wpbc_are_you_sure_popup() ) trash__restore_booking(1,' . $bk_id . ', ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-trash\'></i></a>';		//FixIn: 8.4.7.14
					// Restore
					$header_title .= '<a 	class=\'button button-secondary restore_bk_link'.((!$is_trash)?' hidden_items ':'').'\'  
											title=\'' . esc_js( str_replace( "'", '', __( 'Restore', 'booking' ) ) ) . '\'
											href=\'javascript:;\' onclick=\'javascript:trash__restore_booking(0,' . $bk_id . ', ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-repeat\'></i></a>';
					// Delete
					$header_title .= '<a 	class=\'button button-secondary delete_bk_link'.((!$is_trash)?' hidden_items ':'').'\'  
											title=\'' . esc_js( str_replace( "'", '', __( 'Delete', 'booking' ) ) ) . '\'
											href=\'javascript:;\' onclick=\'javascript:if ( wpbc_are_you_sure_popup() ) delete_booking(' . $bk_id . ', ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-remove\'></i></a>';				//FixIn: 8.4.7.14
					//End FixIn: 6.1.1.10

					// Approve | Decline
					$header_title .= '<a 	class=\'button button-secondary approve_bk_link ' . ($is_approved ? 'hidden_items' : '') . '\'
											title=\'' . esc_js( str_replace( "'", '', __( 'Approve', 'booking' ) ) ) . '\' 
											href=\'javascript:;\' onclick=\'javascript:approve_unapprove_booking(' . $bk_id . ',1, ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-ok-circle\'></i></a>';
					$header_title .= '<a 	class=\'button button-secondary pending_bk_link ' . ($is_approved ? '' : 'hidden_items') . '\' 
											title=\'' . esc_js( str_replace( "'", '', __( 'Pending', 'booking' ) ) ) . '\'
											href=\'javascript:;\' onclick=\'javascript:approve_unapprove_booking(' . $bk_id . ',0, ' . $this->current_user_id . ', &quot;' . wpbc_get_booking_locale() . '&quot; , 1   );\' ><i class=\'glyphicon glyphicon-ban-circle\'></i></a>';


				}

				else if ( ( $this->is_frontend ) && ( ! empty( $this->request_args['booking_hash'] ) ) ) {							//FixIn: 8.1.3.5
					// Valid or not valid hash  we was checked at beginning of function.

					//Edit
					if ( class_exists( 'wpdev_bk_personal' ) ) {

						// $edit_booking_url_admin = wpbc_get_bookings_url( true, false ).'&wh_booking_id='.$bk_id.'&view_mode=vm_listing&tab=actions';
						// $trash_booking_url_admin = wpbc_get_bookings_url( true, false ).'&wh_booking_id='.$bk_id.'&view_mode=vm_listing&tab=actions';

						$is_change_hash_after_approvement = get_bk_option( 'booking_is_change_hash_after_approvement' );    //FixIn: 8.6.1.6
						if ( ( ! $is_approved ) || ( 'On' != $is_change_hash_after_approvement ) ) {                                                                 //FixIn: 8.2.1.14
							$visitorbookingediturl   = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingediturl]', $bk_id );
							$visitorbookingcancelurl = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingcancelurl]', $bk_id );
							$visitorbookingpayurl    = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingpayurl]', $bk_id );

							$header_title .= '<a class=\'btn btn-default wpbc_btn_in_timeline\' title=\''. esc_js( __( 'Edit', 'booking' ) ).'\' href=\'' . $visitorbookingediturl . '\' ><i class=\'glyphicon glyphicon-edit\'></i></a>';
							$header_title .= '<a class=\'btn btn-default wpbc_btn_in_timeline\' title=\''. esc_js( __( 'Cancel', 'booking' ) ).'\'  href=\'' . $visitorbookingcancelurl . '\' ><i class=\'glyphicon glyphicon-trash\'></i></a>';
							$header_title .= '<a class=\'btn btn-default wpbc_btn_in_timeline\' title=\''. esc_js( __( 'Pay', 'booking' ) ).'\'  href=\'' . $visitorbookingpayurl . '\' ><i class=\'glyphicon glyphicon-credit-card\'></i></a>';
						}
					}
				}

			$header_title .= '</div>';
		}
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Content 
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Container
        $content_text = '<div id=\'wpbc-booking-id-'.$bk_id.'\' class=\'flex-popover-content-data\' >';

        
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Labels
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $content_text .= '<div class=\'flex-popover-bars\' >';

			$content_text .= '<div class=\'flex-popover-labels-bar\' >';

				// ID
				$content_text .= '<div class=\'flex-label flex-label-id\'>';
				$content_text .= '<span class=\'label0\'>' . $bk_id . '</span>';
				$content_text .= '</div>';

				// Labels
				$content_text .= '<div class=\'flex-label flex-label-booking-status label-pending' . ( $is_approved ? ' hidden_items' : '' ) . '\'>'
								 . __('Pending' ,'booking')
								 . '</div>';
				$content_text .= '<div class=\'flex-label flex-label-booking-status label-approved' . ( ! $is_approved ? ' hidden_items' : '' ) . '\'>'
								 . __('Approved' ,'booking')
								 . '</div>';

				// Resource
				if ( function_exists( 'get_booking_title' ) ) {

					if ( isset( $booking_types[$bookings[$bk_id]->booking_type] ) )     $bk_title = $booking_types[$bookings[$bk_id]->booking_type]->title;
					else                                                                $bk_title = get_booking_title( $bookings[$bk_id]->booking_type );

					$content_text .= '<div class=\'flex-label flex-label-resource\'>';
					$content_text .= '<span class=\'\'>' . esc_textarea( $bk_title ) . '</span>';	//FixIn: 7.1.1.2
					$content_text .= '</div>';
				}

				// Payment Status
				if (  	   ( function_exists( 'wpdev_bk_get_payment_status_simple' ) )
						&& (  floatval( $bookings[ $bk_id ]->cost ) > 0 )
				   ) {
						$pay_status    = wpdev_bk_get_payment_status_simple( $bookings[ $bk_id ]->pay_status );
						$is_payment_ok = wpbc_is_payment_status_ok( trim( $bookings[ $bk_id ]->pay_status ) );

						$payment_label_status = $is_payment_ok ? 'payment-label-success' : 'payment-label-unknown';

						$content_text .= '<div class=\'flex-label flex-label-payment '.  $payment_label_status .'\'>';

						if ( $is_payment_ok )
							$content_text .= '<span class=\'label-prefix\'>' . esc_js( __( 'Payment', 'booking' ) ). '</span> ' . esc_js( $pay_status );
						else {
							$content_text .= '<span class=\'label-prefix\'>' . esc_js( __( 'Payment', 'booking' ) ) . '</span> ' . esc_js( $pay_status );        //FixIn: 7.1.1.3
						}

						$content_text .= '</div>';
				}

				if ( ! $this->is_frontend ) {
					// Trash
					$content_text .= '<div class=\'flex-label flex-label-trash' . ( ( ! $bookings[$bk_id]->trash ) ? ' hidden_items ' : '' ) . '\'>';
					$content_text .= '<span class=\'\'>' . esc_js( __('Trash / Reject' ,'booking') ) . '</span>';    //FixIn: 6.1.1.10 //FixIn: 7.1.1.3
					$content_text .= '</div>';
				}


			$content_text .= '</div>';


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		    //  Cost Bar
		    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if ( ( class_exists( 'wpdev_bk_biz_s' ) ) ) {	//&& ( ! $this->is_frontend )  ){

			$content_text .= '<div class=\'flex-popover-cost-bar\' >';

					//FixIn: 8.3.3.9
					if ( floatval( $bookings[ $bk_id ]->cost ) > 0 ) {
						// Cost
						$booking_cost = wpbc_get_cost_with_currency_for_user( $bookings[ $bk_id ]->cost, $bookings[ $bk_id ]->booking_type );

						$content_text .= '<div class=\'flex-label flex-label-cost\' >';
						//$content_text .= '<div class=\'text-left field-labels booking-labels\'>';
							$content_text .= '<div class=\'\'>' . $booking_cost . '</div>';
						//$content_text .= '</div>';
						$content_text .= '</div>';
					}

			$content_text .= '</div>';

				}

		$content_text .= '</div>';



		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Booking Data
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Booking Data
	    $content_text .= '<div class=\'clear\'></div>';        //FixIn: 8.7.9.4
        $content_text .= '<div class=\'flex-popover-booking-data\'>' . esc_textarea( $bookings[$bk_id]->form_show ) . '</div>'; //FixIn: 7.1.1.2
	    $content_text .= '<div class=\'clear\'></div>';        //FixIn: 8.7.9.4

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Notes
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Notes
        if ( ! empty( $bookings[$bk_id]->remark ) ) {        
            $content_text .= '<div class=\'wpbc-popover-booking-notes\'>' . '<strong>' . esc_js( __('Note', 'booking') ). ':</strong> ' . esc_textarea( $bookings[$bk_id]->remark ) . '</div>'; //FixIn: 7.1.1.2		//FixIn: 7.1.1.3
        }
        
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Dates
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $bk_dates_short_id = array();                                           //BL
        if ( count( $bookings[$bk_id]->dates ) > 0 )
            $bk_dates_short_id = (isset( $bookings[$bk_id]->dates_short_id )) ? $bookings[$bk_id]->dates_short_id : array();      // Array ([0] => [1] => .... [4] => 6... [11] => [12] => 8 )
        
        $short_dates_content = wpbc_get_short_dates_formated_to_show( $bookings[$bk_id]->dates_short, $is_approved, $bk_dates_short_id, $booking_types );
        $short_dates_content = str_replace( '"', "'", $short_dates_content );

        $content_text .= '<div class=\'flex-label-dates \'>';
        $content_text .= 	  $short_dates_content;
        $content_text .= '</div>';                



        $content_text .= '</div>';	// Main Container: 'flex-popover-content-data'

	    return array(
		    'title'   => $header_title,
		    'content' => $content_text
	    );
    }        
    
}



/** Navigation of Timeline in Ajax request */
function wpbc_ajax_flex_timeline() {
    /*
     [timeline_obj] => Array
                (
                    [is_frontend] => 1
                    [html_client_id] => wpbc_timeline_1454680376080
                    [wh_booking_type] => 3,4,1,5,6,7,8,9,2,10,11,12,14
                    [is_matrix] => 1
                    [view_days_num] => 30
                    [scroll_start_date] => 
                    [scroll_day] => 0
                    [scroll_month] => 0
                )
     */
    

    $attr = $_POST['timeline_obj'];
    $attr['nav_step'] = $_POST['nav_step'];
    
    ob_start();

    $timeline = new WPBC_TimelineFlex();

    $html_client_id = $timeline->ajax_init( $attr );                            // Define arameters and get bookings
//debuge($timeline->options);            
    
    //echo '<div class="wpbc_timeline_ajax_replace">';                          // Replace content of this container
        $timeline->show_timeline();


        $is_show_popover_in_timeline  = wpbc_is_show_popover_in_flex_timeline( $attr['is_frontend'], $attr['booking_hash'] );    	//FixIn: 8.1.3.5

        if ( $is_show_popover_in_timeline ) {                                   // Update New Popovers
            
            ?><script type="text/javascript">   
                    if ( 'function' === typeof( jQuery(".popover_click.popover_bottom" ).popover )  ) {      //FixIn: 7.0.1.2  - 2016-12-10
                        jQuery('.popover_click.popover_bottom').popover( {
                              placement: 'bottom'														//FixIn: 8.4.5.12
                            , trigger:'manual'  
                            //, delay: {show: 100, hide: 8}
                            , content: ''
                            , template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-close"><a href="javascript:void(0)" data-dismiss="popover" aria-hidden="true">&times;</a></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                            , container: '.wpbc_timeline_frame,.flex_timeline_frame'
                            , html: 'true'
                        });      
                    }
            </script><?php 
        }
    //echo '</div>'; 

            
    $timeline_results = ob_get_contents();

    ob_end_clean();

    echo  $timeline_results ;    
}
add_bk_action('wpbc_ajax_flex_timeline', 'wpbc_ajax_flex_timeline');



/** Check if we are showing booking details or not
 *  Admin panel - always show
 *  Timeline - show if activated setting option
 *  Customer listing - show always,  if valid hash.
 *
 * @param $is_frontend
 * @param $booking_hash
 *
 * @return bool
 */
function wpbc_is_show_popover_in_flex_timeline( $is_frontend, $booking_hash ){

	// Default for admin
	$is_show_popover_in_timeline = true;

	// For client Timeline
	if ( $is_frontend )
		$is_show_popover_in_timeline  =  ( get_bk_option( 'booking_is_show_popover_in_timeline_front_end' ) == 'On' ) ? true : false ;

	// For customer booking listing with  ability to  edit
	//FixIn: 8.1.3.5
	if ( ( $is_frontend ) && ( ! empty( $booking_hash ) ) ) {

		//In case if we have valid valid hash  then  show booking details
		$my_booking_id_type = apply_bk_filter( 'wpdev_booking_get_hash_to_id', false, $booking_hash );

		if ( ! empty( $my_booking_id_type ) ) {
			$is_show_popover_in_timeline = true;
		} else {
			$is_show_popover_in_timeline = false;
		}
	}
	return $is_show_popover_in_timeline;
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
function bookingflextimeline_shortcode($attr) {

	//if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache

	if ( empty( $attr['type'] ) ) {                                                                        // 8.7.7.4
		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$br_list      = wpbc_get_all_booking_resources_list();
			$br_list      = array_keys( $br_list );
			$br_list      = implode( ',', $br_list );
			$attr['type'] = $br_list;
		} else {
			$attr['type'] = wpbc_get_default_resource();
		}
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
		$attr['header_title' ] = '';
	}

	ob_start();

	$timeline = new WPBC_TimelineFlex();

	$html_client_id = $timeline->client_init( $attr );                                        // Define arameters and get bookings

	//wpbc_datepicker_js();                                                 // JS  Datepicker
	//wpbc_datepicker_css();                                                // CSS DatePicker


	echo '<div class="wpdevelop">';

		echo '<div id="'.$html_client_id.'" class="wpbc_timeline_client_border">';

			echo  wp_nonce_field( 'WPBC_FLEXTIMELINE_NAV', 'wpbc_nonce_' . $html_client_id ,  true , false );
			echo '<div id="ajax_respond_insert'.$html_client_id.'" class="ajax_respond_insert" style="display:none;"></div>';

			echo '<div class="wpbc_timeline_ajax_replace">';
				if ( ! WPBC()->booking_obj->popover_front_end_js_is_writed ) {                        //Write this JS only  once at  page
					wpbc_bs_javascript_popover();                                       // JS Popover
					WPBC()->booking_obj->popover_front_end_js_is_writed = true;

					//Define Global JavaScript Object - array of objects.
					?>
					<script type="text/javascript">
						var wpbc_timeline_obj = {};
					</script>
					<?php
				}

				$timeline->show_timeline();
			echo '</div>';

		echo '</div>';

	echo '</div>';

	$timeline_results = ob_get_contents();

	ob_end_clean();

	return $timeline_results ;
}
add_shortcode( 'bookingflextimeline',   'bookingflextimeline_shortcode'  );



/** JSS */
function wpbc_timeline_js_load_files( $where_to_load ) {

	//FixIn: 8.6.1.13

	$is_in_footer = ! true;

	if  ( in_array( $where_to_load, array( 'admin', 'both', 'client' ) ) ) {

		wp_enqueue_script(    'wpbc-timeline-flex'
							, trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/timeline_v2.js'                  /* wpbc_plugin_url( '/core/timeline/wpbc-flex-timeline.js' ) */
							, array( 'wpbc-global-vars' /*, 'wp-element'*/ )
							, WP_BK_VERSION_NUM
							, $is_in_footer
						);
	}
}
add_action( 'wpbc_enqueue_js_files',  'wpbc_timeline_js_load_files', 50 );


/** CSS */
function wpbc_timeline_enqueue_css_files( $where_to_load ) {

	//FixIn: 8.6.1.13

	if  ( in_array( $where_to_load, array( 'admin', 'both', 'client' ) ) ) {

		if (    ( ( isset($_REQUEST['view_mode']) ) && ( $_REQUEST['view_mode']== 'vm_calendar' ) )
				|| ( 'client' == $where_to_load )  ){
//wp_deregister_style( 'wpbc-admin-timeline');
			wp_enqueue_style( 'wpbc-flex-timeline'
				, trailingslashit( plugins_url( '', __FILE__ ) ) . 'css/timeline_v2.css'                      /* wpbc_plugin_url( '/src/css/codemirror.css' ) */
				, array()
				, WP_BK_VERSION_NUM );
			wp_enqueue_style( 'wpbc-flex-timeline-skin'
				, trailingslashit( plugins_url( '', __FILE__ ) ) . 'css/timeline_skin_v2.css'                      /* wpbc_plugin_url( '/src/css/codemirror.css' ) */
				, array()
				, WP_BK_VERSION_NUM );
		}

	}
}
add_action( 'wpbc_enqueue_css_files', 'wpbc_timeline_enqueue_css_files', 50 );