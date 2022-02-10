<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Content of Booking Listing page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_CalendarOverview extends WPBC_Page_Structure {
        
    private $timeline;
    
    public function __construct() {
        
        parent::__construct();

        // Redefine TAGs Names,  becasue 'tab' slug already used in the system  for definition  of active toolbar.
        $this->tags['tab']    = 'view_mode';
        $this->tags['subtab'] = 'bottom_nav';
    }
    
    
    public function in_page() {
        return 'wpbc';
    }

    public function tabs() {
        
        $tabs = array();
        $tabs[ 'vm_calendar' ] = array(
                              'title' => __('Calendar Overview','booking')            // Title of TAB    
                            , 'hint' => __('Calendar Overview', 'booking')                      // Hint    
                            , 'page_title' => __('Calendar Overview', 'booking')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphon glyphicon-calendar'             // CSS definition  of forn Icon
                            , 'default' => false                                 // Is this tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   => true                                 // Is this tab hided: true || false.
                            , 'subtabs' => array()
            
        );
        
        // $subtabs = array();                
        // $tabs[ 'bookings' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;        
    }


    public function content() {                
        
        wpbc_check_request_paramters();                                         //Cleanup REQUEST parameters        //FixIn:6.2.1.4
        
        do_action( 'wpbc_hook_booking_page_header', 'timeline' );               // Define Notices Section and show some static messages, if needed.
                                        
        if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) return false;  // Check if MU user activated,  otherwise show Warning message.

        if ( ! wpbc_set_default_resource_to__get() ) return false;              // Define default booking resources for $_GET  and  check if booking resource belong to user.
        
        ?><span class="wpdevelop"><?php                                         // BS UI CSS Class
        
        make_bk_action( 'wpbc_write_content_for_modals' );                      // Content for modal windows
        
        wpbc_js_for_bookings_page();                                            // JavaScript functions
        
        wpbc_welcome_panel();                                                   // Welcome Panel (links)
        
        /* Executed in \core\admin\page-bookings.php on hook wpbc_define_nav_tabs
         * 
         * Get saved filters set, (if its not set in request yet). Like "tab"  & "view_mode" and set to $_REQUEST  
         * If we have "saved" filter-set, then LOAD and set it to REQUEST, if REQUEST was not setting previously 
         * It skip "wh_booking_type" param, load it in next  code line     
         */
        //wpbc_set_default_saved_params_to_request_for_booking_listing( 'default' );          

        make_bk_action( 'wpbc_check_request_param__wh_booking_type' );          // Setting $_REQUEST['wh_booking_type'] - remove empty and duplicates ID of booking resources in this list        
        
        make_bk_action( 'check_for_resources_of_notsuperadmin_in_booking_listing' );    // If "Regular User",  then filter resources in $_REQUEST['wh_booking_type'] to show only resources of this user
        
        wpbc_set_request_params_for_timeline();                                 // Set initial $_REQUEST['view_days_num'] depend from selected booking resources
        
        //   T o o l b a r s   /////////////////////////////////////////////////
        wpbc_timeline_toolbar();                                                
     
        ?><div class="clear" style="height:40px;"></div><?php

            // Show    T i m e L i n e   ///////////////////////////////////////

			//FixIn: 8.6.1.13
			$this->timeline = new WPBC_TimelineFlex();

			$this->timeline->admin_init();                                      // Define all REQUEST parameters and get bookings

			$this->timeline->show_timeline();


            ////////////////////////////////////////////////////////////////////
            
            wpbc_show_booking_footer();           
        
        ?></span><!-- wpdevelop class --><?php 
    }

}
add_action('wpbc_menu_created', array( new WPBC_Page_CalendarOverview() , '__construct') );    // Executed after creation of Menu
