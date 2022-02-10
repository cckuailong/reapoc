<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Content of Add New Booking
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-10-31
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_AddNewBooking extends WPBC_Page_Structure {
    
    
    public function in_page() {
        return 'wpbc-new';
    }

    public function tabs() {
        
        $tabs = array();
        $tabs[ 'add-booking' ] = array(
                              'title' => __('Add booking','booking')            // Title of TAB    
                            , 'hint' => __('Add booking', 'booking')                      // Hint    
                            , 'page_title' => __('Add booking', 'booking')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-calendar'                 // CSS definition  of forn Icon
                            , 'default' => true                                 // Is this tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   =>  true                                 // Is this tab hided: true || false. 
                            , 'subtabs' => array()            
        );
        
        return $tabs;        
    }


    public function content() {                
                
        do_action( 'wpbc_hook_add_booking_page_header', 'add_booking');         // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) return false;  // Check if MU user activated,  otherwise show Warning message.
        
        if ( ! wpbc_set_default_resource_to__get() ) return false;              // Define default booking resources for $_GET and check if booking resource belong to user

        ?><span class="wpdevelop"><?php                                         // BS UI CSS Class
        
        wpbc_js_for_bookings_page();                                            // JavaScript functions
        
        //wpbc_welcome_panel();                                                   // Welcome Panel (links)
        
        
        //   T o o l b a r s   /////////////////////////////////////////////////
        wpbc_add_new_booking_toolbar();                                                

        ?></span><!-- wpdevelop class --><?php 
             
        ?><div class="clear" style="height:40px;"></div><?php                
           
        ?><div class="add_booking_page_content" style="width:100%;"><?php 
        
            // Previously we defined booking resources to  $_GET
            $bk_type = intval( $_GET['booking_type'] );

            // do_action( 'wpdev_bk_add_form', $bk_type, get_bk_option( 'booking_client_cal_count' ) );
            
            $saved_user_calendar_params = $this->get_saved_user_calendar_options();

            make_bk_action( 'wpdevbk_add_form'
                            , $bk_type                                              // $bk_type =1
                            , $saved_user_calendar_params['months_number']          // get_bk_option( 'booking_client_cal_count' )           // $cal_count = 1
                            , 1                                                     // $is_echo = 1
                            , 'standard'                                            // $my_booking_form = 'standard'
                            , ''                                                    // $my_selected_dates_without_calendar = ''
                            , false                                                 // $start_month_calendar = false
                            , '{calendar' . $saved_user_calendar_params['options_param'] . '}'          // $bk_otions=array() 
                          );                                                        //FixIn:6.0.1.6
        
        ?></div><?php
        
        
        ?><hr /><?php        
        wpbc_toolbar_is_send_emails_btn_duplicated();
        
        
        wpbc_bs_javascript_popover();                                           // JS Popover        

        do_action( 'wpbc_hook_add_booking_page_footer', 'add_booking' );
    }
    
    
    /**
	 * Get Calendar Options of specific User
     * 
     * @return array (number of months, options parameter
     */
    function get_saved_user_calendar_options() {
        
        // Get possible saved previous "Custom User Calendar data"
        $user_calendar_options = get_user_option( 'booking_custom_' . 'add_booking_calendar_options', get_wpbc_current_user_id() );

        if ( $user_calendar_options === false ) {                       // Default, if no saved previously.
            $user_calendar_options = array();       
            $user_calendar_options['calendar_months_count'] = 1;
            $user_calendar_options['calendar_months_num_in_1_row'] = 0 ;
            $user_calendar_options['calendar_width'] = '';
            $user_calendar_options['calendar_widthunits'] = 'px';      
            $user_calendar_options['calendar_cell_height'] = '';     
            $user_calendar_options['calendar_cell_heightunits'] = 'px';      
        } else {
            $user_calendar_options = maybe_unserialize( $user_calendar_options );
        }            

        if ( ! empty( $user_calendar_options['calendar_months_count'] ) ) 
             $selected_calendar_months_count = intval ( $user_calendar_options['calendar_months_count'] );
        else $selected_calendar_months_count = 1;            

        if ( ! empty( $user_calendar_options['calendar_months_num_in_1_row'] ) )
             $option_months_num_in_row = ' months_num_in_row=' . intval ( $user_calendar_options['calendar_months_num_in_1_row'] );
        else $option_months_num_in_row = '';            

        if ( ! empty( $user_calendar_options['calendar_width'] ) ) {
             $unit_value = ( esc_attr( $user_calendar_options['calendar_widthunits'] ) == 'percent' ) ? '%' : esc_attr( $user_calendar_options['calendar_widthunits'] );
             $option_width = ' width=' . intval( $user_calendar_options['calendar_width'] ) . $unit_value;
        } else $option_width = '';            

        if ( ! empty( $user_calendar_options['calendar_cell_height'] ) ) {
            $unit_value = ( esc_attr( $user_calendar_options['calendar_cell_heightunits'] ) == 'percent' ) ? '%' : esc_attr( $user_calendar_options['calendar_cell_heightunits'] );            
            $option_cell_height = ' cell_height=' . intval( $user_calendar_options['calendar_cell_height'] ) . $unit_value;
        } else $option_cell_height = '';            
        
        
        return array( 'months_number'=> $selected_calendar_months_count, 'options_param' => $option_months_num_in_row . $option_width . $option_cell_height );
    }

}
add_action('wpbc_menu_created', array( new WPBC_Page_AddNewBooking() , '__construct') );    // Executed after creation of Menu
