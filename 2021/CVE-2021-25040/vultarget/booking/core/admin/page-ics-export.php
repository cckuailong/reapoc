<?php
/**
 * @version     1.0
 * @menu		Booking > Settings > (Sync) Export page
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2017-07-09
 * 
 * This is COMMERCIAL SCRIPT
 * We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

//FixIn: 8.0

/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsExportFeeds extends WPBC_Page_Structure {
    

    public $settings_api = false;

	
    public function in_page() {
        return 'wpbc-settings';
    }
    

    public function tabs() {
        
        $tabs = array();
                
        $tabs[ 'sync' ] = array(
                              'title'     => __( 'Sync', 'booking')					   // Title of TAB    
                            , 'page_title'=> __( 'Sync', 'booking')           // Title of Page   
                            , 'hint'      => __('Import' ,'booking') . ' / '  . __('Export' ,'booking')
                            //, 'link'      => ''                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position'  => ''                                 // 'left'  ||  'right'  ||  ''
                            //, 'css_classes'=> ''                                // CSS class(es)
                            //, 'icon'      => ''                                 // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-import'         // CSS definition  of forn Icon
                            //, 'default'   => false                               // Is this tab activated by default or not: true || false. 
                            //, 'disabled'  => false                              // Is this tab disbaled: true || false. 
                            //, 'hided'     => false                              // Is this tab hided: true || false. 
                            , 'subtabs'   => array()   
                    );
        
        
        $subtabs = array();
        
        $subtabs[ 'export' ] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
							, 'title' => __('Export' ,'booking') . ' - .ics'           // Title of TAB    
                            , 'page_title' => __('Export' ,'booking') . ' .ics '  
											. ' <span style="padding: 10px;font-size: 12px;font-style: italic;vertical-align: top;">Beta</span>'  // Title of Page   
                            , 'hint' => __('Export' ,'booking') . ' .ics/ical ' . __('feed', 'booking')				// Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  false                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $tabs[ 'sync' ]['subtabs'] = $subtabs;
        
        
        return $tabs;
    }


    /** Show Content of Settings page */
    public function content() {

        $this->css();
        
        ////////////////////////////////////////////////////////////////////////
        // Checking 
        ////////////////////////////////////////////////////////////////////////

        do_action( 'wpbc_hook_settings_page_header', 'export_feeds_settings');    // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        // if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.        
        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////

		
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_export_feeds';                         // Define form name
        
        // $this->get_api()->validated_form_id = $submit_form_name;             // Define ID of Form for ability to  validate fields (like required field) before submit.
        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        
         
        ////////////////////////////////////////////////////////////////////////
        // JavaScript: Tooltips, Popover, Datepick (js & css) 
        ////////////////////////////////////////////////////////////////////////
        
        echo '<span class="wpdevelop">';
        
        wpbc_js_for_bookings_page();                                        
        
        echo '</span>';

        ?><div class="clear" style="margin-bottom:0px;"></div><?php
		

		if ( ! function_exists( 'mb_detect_encoding' ) ) {                      //FixIn: 2.0.5.3							//FixIn: 8.1.3.25
			?>
			<span class="metabox-holder">
				<div class="clear" style="height:15px;"></div>
				<div class="wpbc-settings-notice notice-error" style="text-align:left;font-size: 16px;padding: 5px 20px;">
					<strong><?php _e('Warning!' ,'booking'); ?></strong> <?php

						printf( __( 'This feature require %s', 'booking' ), 'PHP <strong>mbstring</strong> extension.'
							);
					?>
				</div>
				<div class="clear" style="height:25px;"></div><?php
		}


		$wpbm_version = wpbc_get_wpbm_version();				
				
		// If lower than 2,  than  show warning
		if ( version_compare( $wpbm_version, '2.0', '<') ) {
			$is_bm_exist = false;
		} else {
			$is_bm_exist = true;
		}

		if ( ! $is_bm_exist ) {			// Not Exist
			?>
			<span class="metabox-holder">
				<div class="clear" style="height:15px;"></div>
				<div class="wpbc-settings-notice notice-error" style="text-align:left;font-size: 16px;padding: 5px 20px;">
					<strong><?php _e('Important!' ,'booking'); ?></strong> <?php 

						printf( __( 'This feature require %s plugin. You can install %s plugin from this %spage%s.', 'booking' )
									, '<strong><a class="" href="'. home_url() .'/wp-admin/plugin-install.php?s=booking+manager+by+oplugins&tab=search&type=term">'
									// , '<strong><a class="thickbox open-plugin-details-modal" href="'. home_url() .'/wp-admin/plugin-install.php?tab=plugin-information&plugin=booking-manager&TB_iframe=true&width=772&height=741"  target="_blank">'
									  . '' . 'Booking Manager' . '</a></strong>'	
									,  '<strong>' . 'Booking Manager' . '</strong>'	
									, '<a target="_blank" href="https://wordpress.org/plugins/booking-manager/">'
									, '</a>'
							);
					?>
				</div>
				<div class="clear" style="height:25px;"></div><?php

				wpbc_open_meta_box_section( 'wpbc_settings_ics_import_help_how', __('How it works', 'booking') );

					wpbc_ics_import_export__show_help_info( false );

				wpbc_close_meta_box_section();
			?></span><?php

		} else {						// Exist 
		
			if ( class_exists('wpdev_bk_personal') )
				wpbc_toolbar_search_by_id__top_form( array( 
														'search_form_id' => 'wpbc_booking_resources_search_form'
													  , 'search_get_key' => 'wh_resource_id'
													  , 'is_pseudo'      => false
												) );

			////////////////////////////////////////////////////////////////////////
			// Content  
			////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-bottom:0px;"></div>
			<span class="metabox-holder">
				<form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off">
					<?php 
					   // N o n c e   field, and key for checking   S u b m i t 
					   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
					?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" /><?php    
					?>
					<div class="clear" style="margin-top:10px;"></div>
					<?php
	            		// Add hidden input SEARCH KEY field into  main form, if previosly was searching by ID or Title
						if ( class_exists('wpdev_bk_personal') )
	            			wpbc_hidden_search_by_id_field_in_main_form( array( 'search_get_key' => 'wh_resource_id' ) );													//FixIn: 8.0.1.12
					?>
					<div id="wpbc_resources_link" class="clear"></div>
					<?php  if ( class_exists('wpdev_bk_personal') ) {  ?>
					<div id="wpbc_resource_table_gcal_id" class="wpbc_settings_row wpbc_settings_row_rightNO"><?php 

						//wpbc_open_meta_box_section( 'wpbc_settings_export_feeds_resources', __('Resources', 'booking') );

							wpbc_export_feeds__show_table();

						//wpbc_close_meta_box_section();
					?>
					</div>
					<div class="clear"></div>
					<?php  } else { 
					
						
						// Booking Calendar Free version
						
						wpbc_open_meta_box_section( 'wpbc_settings_export_feeds_resources',  __('Export' ,'booking') . ' .ics/ical ' . __('feed', 'booking') );

							wpbc_export_ics_feed__table();

						wpbc_close_meta_box_section();
					
					
					} ?>
					<input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" />  
					
					<div class="clear" style="height:25px;"></div><?php

					wpbc_open_meta_box_section( 'wpbc_settings_ics_import_help_how', __('How it works', 'booking') );

						wpbc_ics_import_export__show_help_info( false );

					wpbc_close_meta_box_section();
					?><div class="clear"></div>
					
					
				</form>
			</span>
			<?php       
    
		}
        do_action( 'wpbc_hook_settings_page_footer', 'export_feeds_settings' );
        
        $this->enqueue_js();
    }


    /** Save Chanages */
    public function update() {

        if ( function_exists( 'wpbc_export_feeds__update') )
            wpbc_export_feeds__update();
		else 
			wpbc_export_ics_feed__update();		// Free version
		
        // Get Validated Email fields
		//$validated_fields = $this->get_api()->validate_post();        
		//$validated_fields = apply_filters( 'wpbc_fields_before_saving_to_db__export_feeds', $validated_fields );   //Hook for validated fields.
		//$this->get_api()->save_to_db( $validated_fields );
        
        // Old way of saving:
        // update_bk_option( 'booking_cache_expiration' , WPBC_Settings_API::validate_text_post_static( 'booking_cache_expiration' ) );
		
		wpbc_show_changes_saved_message();        
    }

//TODO: claer unused CSS here

    // <editor-fold     defaultstate="collapsed"                        desc=" CSS  &   JS   "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
            .wpbc-help-message {
                border:none;
            }
            /* toolbar fix */
            .wpdevelop .visibility_container .control-group {
                margin: 0 8px 5px 0;
            }
            /* Selectbox element in toolbar */
            .visibility_container select optgroup{                            
                color:#999;
                vertical-align: middle;
                font-style: italic;
                font-weight: 400;
            }
            .visibility_container select option {
                padding:5px;
                font-weight: 600;
            }
            .visibility_container select optgroup option{
                padding: 5px 20px;       
                color:#555;
                font-weight: 600;
            }
            #wpbc_create_new_custom_form_name_fields {
                width: 360px;
                display:none;
            }
			.wpbc_tr_booking_export_feed_free input[type="text"].regular-text {
				margin-right:10px;
			}
            @media (max-width: 782px) {
                #wpbc_create_new_custom_form_name_fields {
                    width: 100%;
                }    
				.wpbc_tr_booking_export_feed_free code {
					display: inline;
					line-height: 2.7em;
					vertical-align: top;
					padding: 7px 10px;
					margin: 0;
				}
				.wpbc_tr_booking_export_feed_free input[type="text"].regular-text {
					width:65%;
					display: inline;
				}
            }
        </style>
        <?php
    }
    
    
    
    /**
	 * Add Custon JavaScript - for some specific settings options
     *      Executed After post content, after initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     */
    private function enqueue_js(){                                                        
        
        // JavaScript //////////////////////////////////////////////////////////////
        
        $js_script = '';
        

        //Show|Hide grayed section      
        $js_script .= " 
                        if ( ! jQuery('#export_feeds_booking_gcal_auto_import_is_active').is(':checked') ) {   
                            jQuery('.wpbc_tr_auto_import').addClass('hidden_items'); 
                        }
                      ";        
        // Hide|Show  on Click      Checkbox
        $js_script .= " jQuery('#export_feeds_booking_gcal_auto_import_is_active').on( 'change', function(){    
                                if ( this.checked ) { 
                                    jQuery('.wpbc_tr_auto_import').removeClass('hidden_items');
                                } else {
                                    jQuery('.wpbc_tr_auto_import').addClass('hidden_items');
                                }
                            } ); ";             
        //   F R O M
        $js_script .= " 
                        if ( jQuery('#export_feeds_booking_gcal_events_from').val() != 'date' ) {   
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#export_feeds_booking_gcal_events_from').on( 'change', function(){    
                            jQuery('#export_feeds_booking_gcal_events_from_offset').val('');
                            if ( jQuery(this).val() != 'date' ){ 
                                jQuery('.wpbc_tr_from_offset .wpbc_offset_value').removeClass('hidden_items');
                                jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').addClass('hidden_items');
                            } else {
                                jQuery('.wpbc_tr_from_offset .wpbc_offset_value').addClass('hidden_items');
                                jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                            }
                        } ); ";        
        //   U n t i l 
        $js_script .= " 
                        if ( jQuery('#export_feeds_booking_gcal_events_until').val() != 'date' ) {   
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#export_feeds_booking_gcal_events_until').on( 'change', function(){    
                            jQuery('#export_feeds_booking_gcal_events_until_offset').val('');
                            if ( jQuery(this).val() != 'date' ){ 
                                jQuery('.wpbc_tr_until_offset .wpbc_offset_value').removeClass('hidden_items');
                                jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').addClass('hidden_items');
                            } else {
                                jQuery('.wpbc_tr_until_offset .wpbc_offset_value').addClass('hidden_items');
                                jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                            }
                        } ); ";        
        
        
//        // Hide|Show  on Click      Radion
//        $js_script .= " jQuery('input[name=\"paypal_pro_hosted_solution\"]').on( 'change', function(){    
//                                jQuery('.wpbc_sub_settings_paypal_account_type').addClass('hidden_items'); 
//                                if ( jQuery('#paypal_type_standard').is(':checked') ) {   
//                                    jQuery('.wpbc_sub_settings_paypal_standard').removeClass('hidden_items');
//                                } else {
//                                    jQuery('.wpbc_sub_settings_paypal_pro_hosted').removeClass('hidden_items');
//                                }
//                            } ); ";        
        
        ////////////////////////////////////////////////////////////////////////
        
        
        // Eneque JS to  the footer of the page
        wpbc_enqueue_js( $js_script );                
    }

    // </editor-fold>
    
}
add_action('wpbc_menu_created', array( new WPBC_Page_SettingsExportFeeds() , '__construct') );    // Executed after creation of Menu


 
/** Show .ics ULR section for Free version */
function wpbc_export_ics_feed__table() {
	
	$resource_export = get_bk_option( 'booking_resource_export_ics_url' );
	?>
	<table class="form-table">
		<tbody><tr class="wpbc_tr_booking_export_feed_free" valign="top">
			<th scope="row">
                <label for="booking_export_feed1" class="wpbc-form-text"><?php _e( '.ics feed URL', 'booking' ); ?></label>
			</th>
            <td><fieldset class="wpdevelop">                
				<legend class="screen-reader-text"><span><?php _e( '.ics feed URL', 'booking' ); ?></span></legend>
				<code style="font-size:12px;line-height: 2.4em;background: #ddd;color:#000;"><a
						href="<?php echo trim( home_url(), '/' ) . '/' . trim( $resource_export, '/');  ?>"
						target="_blank"><?php
							$wpbc_h_u = home_url();																		//FixIn: 8.1.3.6
							if (strlen( $wpbc_h_u ) > 23 ) {
								echo substr( $wpbc_h_u, 0, 10 ) . '...' . substr( $wpbc_h_u, -10 );
							} else {
								echo $wpbc_h_u;
							}?></a></code>
				<input id="booking_export_feed1" 
					   name="booking_export_feed1" 
					   value="<?php echo $resource_export; ?>" 
					   placeholder="" 
					   autocomplete="off" 
					   type="text"						
					   class="regular-text" 					   					   
				/> 
				<a href="<?php echo trim( home_url(), '/' ) . '/' . trim( $resource_export, '/');  ?>" 
						   title="<?php _e( 'Open in new window', 'booking' ); ?>"
						   target="_blank"><i class="menu_icon icon-1x glyphicon glyphicon-new-window"></i></a>
				<p class="description"><?php _e( 'Please enter URL for generating .ics feed', 'booking' ); ?></p>
				<div class="wpbc-settings-notice notice-info" style="text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0;">
					<strong><?php _e( 'Note', 'booking' ) ?></strong>.  <?php 					
					printf( __( 'This .ics feed of bookings starting from today for 1 year', 'booking' ) );						
					?>
				</div>				
			</fieldset></td>
        </tr></tbody>
	</table>		
	<?php
}


/**
	 * Save changes to  URL in Free version
 * 
 * @return string - validated value
 */
function wpbc_export_ics_feed__update() {
	
	$validated_value = WPBC_Settings_API::validate_text_post_static( 'booking_export_feed1' );

	$validated_value = explode( '/', $validated_value );
	foreach ( $validated_value as $v_i => $v_val ) {                                                                    //FixIn: 8.1.1.9
		if ( strpos( $v_val, '.') !== false ) {
			$v_val = sanitize_file_name( $v_val );
		}
		$validated_value[ $v_i ] = $v_val;
	}
	// $validated_value = array_map( 'sanitize_file_name', $validated_value );
	$validated_value = implode( '/', $validated_value );
	$validated_value = strtolower( $validated_value );
	$validated_value = wpbc_make_link_relative( $validated_value );

	if ( empty( $validated_value ) )
		$validated_value = '/ics/' . wpbc_get_slug_format( 'default' );
	
	update_bk_option( 'booking_resource_export_ics_url', $validated_value );
	
	return $validated_value;	
}