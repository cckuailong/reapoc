<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Content of Settings page 
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsGeneral extends WPBC_Page_Structure {
    
    private $settings_api = false;
    
    public function __construct() {
        
        if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all

            if  (
                    ( isset( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'wpbc-settings' )  )                  // Check  if this Settings page
                &&
                    (  ( ! isset( $_REQUEST['tab'] ) ) || ( $_REQUEST['tab'] == 'general' )  ) 
                ) {     // If tab  was not selected or selected default,  then  redirect  it to the "form" tab.            
                $_REQUEST['tab'] = 'form';
            }

        }
        else {
            parent::__construct();
        }
        
    }
    
    public function in_page() {
        
        if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all            
            return (string) rand(100000, 1000000);
        }
        
        return 'wpbc-settings';
    }        
    

    /**
	 * Get Settings API class - define, show, update "Fields".
     * 
     * @return object Settings API
     */    
    public function settings_api(){
        
        if ( $this->settings_api === false )             
             $this->settings_api = new WPBC_Settings_API_General(); 
        
        return $this->settings_api;
    }
    
    
    public function tabs() {
       
        $tabs = array();
                
        $tabs[ 'general' ] = array(
                                        'title' => __( 'General', 'booking')                     // Title of TAB    
                                      , 'page_title' => __( 'General Settings', 'booking')                // Title of Page    
                                      , 'hint'      => __( 'General Settings', 'booking')               // Hint    
                                      , 'link' => ''                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                                      , 'position' => ''                  // 'left'  ||  'right'  ||  ''
                                      , 'css_classes' => ''               // CSS class(es)
                                      , 'icon' => ''                      // Icon - link to the real PNG img
                                      , 'font_icon' => 'glyphicon glyphicon-cog'                 // CSS definition  of forn Icon
                                      , 'default' => true                // Is this tab activated by default or not: true || false. 
                    );

        $subtabs = array();
        
        $subtabs['wpbc-settings-calendar'] = array(   'type' => 'goto-link' 
                                                    , 'title' => __('Calendar','booking') 
                                                    , 'show_section' => 'wpbc_general_settings_calendar_metabox'
                                                );

		//FixIn: 8.7.11.10
        $subtabs['wpbc-settings-time-slots'] = array(   'type' => 'goto-link'
                                                    , 'title' => __('Time Slots','booking')
                                                    , 'show_section' => 'wpbc_general_settings_time_slots_metabox'
                                                );

        $subtabs['wpbc-settings-availability'] = array(   'type' => 'goto-link' 
                                                    , 'title' => __('Availability','booking') 
                                                    , 'show_section' => 'wpbc_general_settings_availability_metabox'
                                                );
        
        $subtabs['wpbc-settings-form'] = array(     'type' => 'goto-link'                               
                                                    , 'title' => __('Form','booking')            
                                                    , 'show_section' => 'wpbc_general_settings_form_metabox'
                                                );
        
        $subtabs['wpbc-settings-booking-listing'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Booking Admin Panel','booking')
                                                    , 'show_section' => 'wpbc_general_settings_booking_listing_metabox'
                                                );

        $subtabs['wpbc-settings-booking-timeline'] = array(  'type' => 'goto-link'
                                                    , 'title' =>  __('Timeline', 'booking')
                                                    , 'show_section' => 'wpbc_general_settings_booking_timeline_metabox'
                                                );

        if ( class_exists('wpdev_bk_biz_s') ) {
            

            $subtabs['wpbc-settings-auto-cancelation-approval'] = array(  'type' => 'goto-link'                               
                                                        , 'title' => __('Auto cancellation / approval','booking')            
                                                        , 'show_section' => 'wpbc_general_settings_auto_cancelation_approval_metabox'
                                                    );
        }
        $subtabs['wpbc-settings-advanced'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Advanced','booking')            
                                                    , 'show_section' => 'wpbc_general_settings_advanced_metabox'
                                                );
        
        $subtabs['wpbc-settings-menu-access'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Plugin Menu','booking')            
                                                    , 'show_section' => 'wpbc_general_settings_permissions_metabox'
                                                );
                
        $subtabs['wpbc-settings-uninstall'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Uninstall','booking')            
                                                    , 'show_section' => 'wpbc_general_settings_uninstall_metabox'
                                                );
                
        $subtabs['wpbc-settings-technical'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Help', 'booking')            
                                                    , 'show_section' => 'wpbc_general_settings_help_metabox'
                                                );
        
        $subtabs['form-save'] = array( 
                                        'type' => 'button'                                  
                                        , 'title' => __('Save Changes','booking')        
                                        , 'form' => 'wpbc_general_settings_form'                
                                    );
                        
        
        $tabs[ 'general' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;
    }


    public function content() {
                
        // Checking ////////////////////////////////////////////////////////////
        
        do_action( 'wpbc_hook_settings_page_header', 'general_settings');       // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.
            
        $is_can = apply_bk_filter('recheck_version', true); if ( ! $is_can ) { ?><script type="text/javascript"> jQuery(document).ready(function(){ jQuery( '.wpdvlp-sub-tabs').remove(); }); </script><?php return; }
        
        
        // Init Settings API & Get Data from DB ////////////////////////////////
        $this->settings_api();                                                  // Define all fields and get values from DB
        
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_general_settings_form';                       // Define form name
                
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        //$wpbc_user_role_master   = get_wpbc_option( 'wpbc_user_role_master' );    // O L D   W A Y:   Get Fields Data
        
        
        // JavaScript: Tooltips, Popover, Datepick (js & css) //////////////////
        echo '<span class="wpdevelop">';
        wpbc_js_for_bookings_page();                                        
        echo '</span>';

              
        
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />

                <div class="wpbc_settings_row wpbc_settings_row_left" >

                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_calendar', __('Calendar', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'calendar' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>

                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_time_slots', __('Time Slots', 'booking') );  	//FixIn: 8.7.11.10 ?>

                    <?php $this->settings_api()->show( 'time_slots' ); ?>

                    <?php wpbc_close_meta_box_section(); ?>

                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_availability', __('Availability', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'availability' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>

                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_form', __('Form', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'form' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>

                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_booking_listing', __('Booking Admin Panel', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'booking_listing' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>


                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_booking_timeline', __('Calendar Overview', 'booking') . ' | ' . __('Timeline', 'booking') );  ?>

                    <?php //FixIn: 8.5.2.20
						$this->settings_api()->show( 'booking_timeline' );
					?>

                    <?php wpbc_close_meta_box_section(); ?>


                    <?php if ( class_exists('wpdev_bk_biz_s') ) { ?>

                    
                        <?php wpbc_open_meta_box_section( 'wpbc_general_settings_auto_cancelation_approval', __('Auto cancellation / auto approval of bookings', 'booking') );  ?>

                        <?php $this->settings_api()->show( 'auto_cancelation_approval' ); ?>                                      

                        <?php wpbc_close_meta_box_section(); ?>
                                        
                    <?php } ?>
                    
                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_advanced', __('Advanced', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'advanced' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>
                    
                </div>  
                <div class="wpbc_settings_row wpbc_settings_row_right">
                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_information', __('Information', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'information' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>                    

                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_permissions', __('Plugin Menu', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'permissions' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>                    

                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_uninstall', __('Uninstall / deactivation', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'uninstall' ); ?>                                      
                    
                    <?php wpbc_close_meta_box_section(); ?>                    

                    
                    <?php wpbc_open_meta_box_section( 'wpbc_general_settings_help', __('Help', 'booking') );  ?>

                    <?php $this->settings_api()->show( 'help' ); ?>                                      
                 
                    <?php wpbc_close_meta_box_section(); ?>                    
                    
                </div>                
                <div class="clear"></div>
                <input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" />
				<?php
						if ( ! wpbc_is_this_demo() ) {

							echo  '<a style="margin:0 2em;" class="button button" href="' . wpbc_get_settings_url()
								  									     . '&restore_dismissed=On#wpbc_general_settings_restore_dismissed_metabox">'
								  										 . __('Restore all dismissed windows' ,'booking')
								. '</a>';
						}
				?>
            </form>
            <?php if ( ( isset( $_GET['system_info'] ) ) && ( $_GET['system_info'] == 'show' ) ) { ?>
                
                <div class="clear" style="height:30px;"></div>
                
                <?php wpbc_open_meta_box_section( 'wpbc_general_settings_system_info', 'System Info' );  ?>

                <?php wpbc_system_info(); ?>

                <?php wpbc_close_meta_box_section(); ?>                    

            <?php } ?>

            <?php if ( ( isset( $_GET['restore_dismissed'] ) ) && ( $_GET['restore_dismissed'] == 'On' ) ) {            //FixIn: 8.1.3.10


				update_bk_option( 'booking_is_show_powered_by_notice' , 'On' );

				update_bk_option( 'booking_wpdev_copyright_adminpanel' , 'On'  );

            	global $wpdb;
				// Delete all users booking windows states
				if ( false === $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%booking_win_%'" ) ){    // All users data
					debuge_error('Error during deleting user meta at DB',__FILE__,__LINE__);
					die();
				} else {
					?><div class="clear" style="height:30px;"></div><?php
					wpbc_open_meta_box_section( 'wpbc_general_settings_restore_dismissed', 'Info' );

						?><h2>All dismissed windows have been resored.</h2><?php

						echo '<div class="clear"></div><hr/><center><a class="button button" href="' . wpbc_get_settings_url() . '">'
                                                                                . 'Reload Page'
                                                        . '</a></center>';

					wpbc_close_meta_box_section();
				}
            }
			?>

        </span>
    <?php 

    
    
        do_action( 'wpbc_hook_settings_page_footer', 'general_settings' );
    
//debuge( 'Content <strong>' . basename(__FILE__ ) . '</strong> <span style="font-size:9px;">' . __FILE__  . '</span>');                  
    }


    public function update() {
//debuge($_POST);
        $validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.
        
        $validated_fields = apply_filters( 'wpbc_settings_validate_fields_before_saving', $validated_fields );   //Hook for validated fields.
//debuge($validated_fields);
        // Skip saving specific option, for example in Demo mode.
        // unset($validated_fields['booking_start_day_weeek']);
//debuge('$_POST',$_POST)        ;
//debuge('$validated_fields',$validated_fields);
        $this->settings_api()->save_to_db( $validated_fields );                 // Save fields to DB
        wpbc_show_changes_saved_message();
        
//debuge( basename(__FILE__), 'UPDATE',  $_POST, $validated_fields);          
                
        // O L D   W A Y:   Saving Fields Data
        //      update_bk_option( 'booking_is_delete_if_deactive'
        //                       , WPBC_Settings_API::validate_checkbox_post('booking_is_delete_if_deactive') );  
        //      ( (isset( $_POST['booking_is_delete_if_deactive'] ))?'On':'Off') );

    }
}



//if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {                    // If this User not "super admin",  then  do  not load this page at all
//    
//    if (  ( ! isset( $_GET['tab'] ) ) || ( $_GET['tab'] == 'general' )  ) {     // If tab  was not selected or selected default,  then  redirect  it to the "form" tab.            
//        $_GET['tab'] = 'form';
//    }
//} else {
//    add_action('wpbc_menu_created', array( new WPBC_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
//}
//
//
//
 add_action('wpbc_menu_created', array( new WPBC_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
 