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
class WPBC_Page_Bookings extends WPBC_Page_Structure {
        
    private $listing_table;
    
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
        $tabs[ 'vm_listing' ] = array(
                              'title' => __('Booking Listing','booking')            // Title of TAB    
                            , 'hint' => __('Booking Listing', 'booking')                      // Hint    
                            , 'page_title' => __('Booking Listing', 'booking')                                // Title of Page    
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphon glyphon-form'             // CSS definition  of forn Icon
                            , 'default' => true                                 // Is this tab activated by default or not: true || false. 
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
        
        do_action( 'wpbc_hook_booking_page_header', 'booking' );                // Define Notices Section and show some static messages, if needed.
        
        if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) return false;  // Check if MU user activated,  otherwise show Warning message.

        if ( ! wpbc_set_default_resource_to__get() ) return false;                  // Define default booking resources for $_GET  and  check if booking resource belong to user.
        
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

        //   T o o l b a r s   /////////////////////////////////////////////////
        wpbc_bookings_toolbar();                                                

     
        ?><div class="clear" style="height:40px;"></div><?php
//debuge($_REQUEST);
        $args = wpbc_get_clean_paramas_from_request_for_booking_listing();      // Get safy PARAMS from REQUEST
        echo '<textarea id="bk_request_params" style="display:none;">', serialize( $args ), '</textarea>';
//debuge($args);        
        ////////////////////////////////////////////////////////////////////////
        // B O O K I N G    L I S T I N G    P A G E     
        ////////////////////////////////////////////////////////////////////////
        $bk_listing = wpbc_get_bookings_objects( $args );                       // Get Bookings structure
        $bookings           = $bk_listing[ 'bookings' ];
        $booking_types      = $bk_listing[ 'resources' ];
        $bookings_count     = $bk_listing[ 'bookings_count' ];
        $page_num           = $bk_listing[ 'page_num' ];
        $page_items_count   = $bk_listing[ 'count_per_page' ];
                
//debuge( '$args, $_REQUEST, $bk_listing', $args, $_REQUEST, $bk_listing );
        
        $this->listing_table = new WPBC_Booking_Listing_Table( $bookings, $booking_types );
        $this->listing_table->show();
        

        wpbc_show_pagination($bookings_count, $page_num, $page_items_count);   // Show Pagination  

        wpbc_show_booking_footer();           
        
        ?></span><!-- wpdevelop class --><?php 
        
 

    }

}
add_action('wpbc_menu_created', array( new WPBC_Page_Bookings() , '__construct') );    // Executed after creation of Menu



/** Trick here to  overload default REQUST parameters before page is loading */
function wpbc_define_listing_page_parameters( $page_tag ) {
    
    // $page_tag - here can be all defined in plugin menu pages
    // So  we need to  check activated page. By default its inside of $_GET['page'], 
    
    // Execute it only  for Booking Listing & Timeline admin pages.
    //if (  ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc' )  ) {                

    if ( wpbc_is_bookings_page() ) {                                            // We are inside of this page. Menu item selected. 
        // Get saved filters set, (if its not set in request yet), like "tab"  & "view_mode" and overload $_REQUEST    
        wpbc_set_default_saved_params_to_request_for_booking_listing( 'default' );          
    }
}
// We are set  9  to  execute early  than hook in WPBC_Admin_Menus
add_action('wpbc_define_nav_tabs', 'wpbc_define_listing_page_parameters', 1  );             // This Hook fire in the class WPBC_Admin_Menus for showing page content of specific menu                