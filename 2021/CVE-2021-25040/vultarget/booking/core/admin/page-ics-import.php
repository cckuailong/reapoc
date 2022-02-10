<?php
/**
 * @version     1.0
 * @menu		Booking > Settings > (Sync) Import page
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
class WPBC_Page_SettingsImportFeeds extends WPBC_Page_Structure {
    
    // public $settings_api = false;
	
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
                            , 'font_icon' => 'glyphicon glyphicon-refresh'         // CSS definition  of forn Icon
                            //, 'default'   => false                               // Is this tab activated by default or not: true || false. 
                            //, 'disabled'  => false                              // Is this tab disbaled: true || false. 
                            //, 'hided'     => false                              // Is this tab hided: true || false. 
                            , 'subtabs'   => array()   
                    );
        
        
        $subtabs = array();
        
        $subtabs[ 'import' ] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' => __('Import' ,'booking') . ' - .ics'           // Title of TAB    
                            , 'page_title' => __('Import' ,'booking') . ' .ics '  
											. ' <span style="padding: 10px;font-size: 12px;font-style: italic;vertical-align: top;">Beta</span>'  // Title of Page   
                            , 'hint' => __('Import' ,'booking') . ' .ics/ical ' . __('feeds', 'booking')		 // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  ! true                                // Is this sub tab activated by default or not: true || false.		//FixIn: 8.1.1.10
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

        do_action( 'wpbc_hook_settings_page_header', 'ics_import_settings');    // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        // if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.
        
        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////
        //$booking_gcal_events_form_fields = get_bk_option( 'booking_gcal_events_form_fields'); 
        //$booking_gcal_events_form_fields = maybe_unserialize( $booking_gcal_events_form_fields );

		
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_ics_import';                         // Define form name
        
        //$this->get_api()->validated_form_id = $submit_form_name;             // Define ID of Form for ability to  validate fields (like required field) before submit.
        
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
        
		$wpbm_version = wpbc_get_wpbm_version();				
				
		// If lower than 2,  than  show warning
		if ( version_compare( $wpbm_version, '2.0', '<') ) {
			$is_bm_exist = false;
		} else {
			$is_bm_exist = true;
		}
		
		
        ////////////////////////////////////////////////////////////////////////
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:0px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" /><?php                 
                ?><div class="clear"></div><?php 
                
                ?><div class="clear" style="height:10px;"></div><?php 
								
				if ( ! $is_bm_exist ) {			// Not Exist
					?>
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
                        
                        wpbc_ics_import_export__show_help_info();
                        
                    wpbc_close_meta_box_section();
					
				} else {						// Exist 

					

						wpbc_open_meta_box_section( 'wpbc_settings_ics_import_single', __('Import', 'booking') );

							$this->show_toolbar_import_fields();

						wpbc_close_meta_box_section();


						
						wpbc_open_meta_box_section( 'wpbc_settings_ics_import_help_info', __('Help', 'booking') );

							wpbc_ics_import_export__show_help_info();

						wpbc_close_meta_box_section();
					?>
					
					<div class="clear"></div>

					<!--input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" /-->  
				<?php 
				}
				?>
            </form>
        </span>
        <?php       
    
        do_action( 'wpbc_hook_settings_page_footer', 'ics_import_settings' );
        
        $this->enqueue_js();
		
		wpbc_ics_import_ajax_js();
    }


    /** Save Chanages */  
    public function update() {
//debuge($_POST);		
        // Get Validated Email fields
        // $validated_fields = $this->get_api()->validate_post();
        
        // $validated_fields = apply_filters( 'wpbc_fields_before_saving_to_db__ics_import', $validated_fields );   //Hook for validated fields.
        
//debuge($validated_fields);        

        //$this->get_api()->save_to_db( $validated_fields );
 
//        wpbc_show_changes_saved_message();                
        // Old way of saving:
        // update_bk_option( 'booking_cache_expiration' , WPBC_Settings_API::validate_text_post_static( 'booking_cache_expiration' ) );
    }

    // <editor-fold     defaultstate="collapsed"                        desc=" CSS  &   JS   "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
			.wpbc_import_ics_bar .wpbc_import_btn, 
			.wpbc_import_ics_bar .wpbc_upload_btn{
				float:left;
				margin:9px 5px 10px 1px;	
			
			}		
			.wpbc_import_ics_bar .wpbc_import_div {
				float:left;
				width:70%;				
			}
			.wpbc_import_ics_bar .wpbc_import_br_selection,
			.wpbc_import_ics_bar .wpbc_import_url {
				float:left;
				width:28%;
				/*height: 2em;*/
				line-height: 1.4;
				padding: 2px;
				/*border-radius: 0;*/
				margin:10px 5px 10px 1px;				
			}
			.wpbc_import_ics_bar .wpbc_import_url {
				width:70%;				
				padding: 2px 5px;
			}			
			.wpbc_system_info_log {
				font-size: 11px;
				line-height: 1.5em;
				border: 2px dashed #e85;
				padding: 5px 20px;
				display: none;
			}
			/**********************************************************************************************************/
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
			@media (max-width: 782px) {
				.wpbc_import_ics_bar .wpbc_import_br_selection,
				.wpbc_import_ics_bar .wpbc_import_url {
					line-height: 1.74;
				}
				.wpbc_import_ics_bar .wpbc_import_br_selection,
				.wpbc_import_ics_bar .wpbc_import_div {
					float:none;
					width:100%;				
				}				
			}
            @media (max-width: 399px) {
                #wpbc_create_new_custom_form_name_fields {
                    width: 100%;
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
                        if ( ! jQuery('#ics_import_booking_gcal_auto_import_is_active').is(':checked') ) {   
                            jQuery('.wpbc_tr_auto_import').addClass('hidden_items'); 
                        }
                      ";        
        // Hide|Show  on Click      Checkbox
        $js_script .= " jQuery('#ics_import_booking_gcal_auto_import_is_active').on( 'change', function(){    
                                if ( this.checked ) { 
                                    jQuery('.wpbc_tr_auto_import').removeClass('hidden_items');
                                } else {
                                    jQuery('.wpbc_tr_auto_import').addClass('hidden_items');
                                }
                            } ); ";             
        //   F R O M
        $js_script .= " 
                        if ( jQuery('#ics_import_booking_gcal_events_from').val() != 'date' ) {   
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#ics_import_booking_gcal_events_from').on( 'change', function(){    
                            jQuery('#ics_import_booking_gcal_events_from_offset').val('');
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
                        if ( jQuery('#ics_import_booking_gcal_events_until').val() != 'date' ) {   
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#ics_import_booking_gcal_events_until').on( 'change', function(){    
                            jQuery('#ics_import_booking_gcal_events_until_offset').val('');
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
    
	
	
	/** Show Toolbar with import fields */
	function show_toolbar_import_fields() {		

		// Parameters for Ajax: 
		
		?><div  class="wpbc_import_ics_bar"	 id="wpbc_import_ics_bar"		 
				data-nonce="<?php echo wp_create_nonce( $nonce_name = 'wpbc_import_ics_nonce_actn' ); ?>"	
				data-user-id="<?php echo get_current_user_id(); ?>"
			 ><?php

			if ( function_exists( 'wpbc_get_br_as_objects' ) ) {

				$bk_resources = wpbc_get_br_as_objects();  

				?><select id="wpbc_import_br_selection" name="wpbc_import_br_selection" class="wpbc_import_br_selection"><?php 

					foreach ( $bk_resources as $res ) {

						$res_title = $res->title;

						if ( ( isset( $res->parent ) ) && ( $res->parent == 0 ) && ( isset( $res->count ) ) && ( $res->count > 1 ) ) {

							$option_class = 'wpbc_parent_resource';
							$res_title = $res_title. ' [' . __('parent resource', 'booking') . ']';

						} elseif ( ( isset( $res->parent ) ) && ( $res->parent != 0 ) ) {

							$option_class = 'wpbc_child_resource';
							$res_title = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $res_title;

						} else {
							$option_class = 'wpbc_single_resource';
						}

						?><option value="<?php echo $res->id; ?>" class="<?php echo  $option_class; ?>" ><?php echo $res_title; ?></option><?php 

					} 

				?></select><?php 	
			}

			?>
			<div class="wpbc_import_div">
				<input type="text" 
					   class="wpbc_import_url" name="wpbc_import_url" id="wpbc_import_url" 
					   placeholder="<?php _e( 'Enter URL to .ics feed', 'booking' ) ?>"				   
					   value="" wrap="off" 
					   />
				<?php if ( function_exists( 'wpbm_upload' ) ) {  ?>
					<a href="javascript:void(0)" class="button button-secondary wpbc_upload_btn"
							data-modal_title="<?php echo esc_attr( __( 'Choose file', 'booking' ) ); ?>" 
							data-btn_title="<?php echo esc_attr( __( 'Insert file URL', 'booking' ) ); ?>" 						   
						><?php _e('Upload / Select ', 'booking' ); ?> <strong>(.ics)</strong></a>
				<?php } ?>
				<a class="button button-primary wpbc_import_btn" href="javascript:void(0)"><?php _e('Import', 'booking'); ?></a>				
			</div>
			<?php 
			if ( function_exists( 'wpbm_upload' ) ) {																	// Get WPBM_Upload obj. instance
				
				$wpbm_upload = wpbm_upload();	

				$wpbm_upload->set_upload_button( '.wpbc_upload_btn' );

				$wpbm_upload->set_element_insert_url( '.wpbc_import_url' );
			}
			?>
			<div class="clear"></div>
			<div class="wpbc_system_info_log"></div>
			<div class="clear"></div>
		</div>

		<?php		
	}
	
}
add_action('wpbc_menu_created', array( new WPBC_Page_SettingsImportFeeds() , '__construct') );    // Executed after creation of Menu



/**
	 * Show Help  info about .ics import/export at  Booking > Settings > Sync pages
 * 
 * @param bool $is_import
 */
function wpbc_ics_import_export__show_help_info( $is_import = true ) {
	?>
	<div class="wpbc-help-message ">
		<h4 style="margin-top:0;font-size:1.1em;">
			<?php 
				$message_ics = sprintf( __( 'What does .ics feeds import/export mean?', 'booking' ) );
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics; 
			?>						
		</h4>
		<p  class="code" >
			<?php 
				$message_ics = sprintf( 
						__( 'Its useful, if you need to import/export bookings from/to external websites, like %s', 'booking' ), 
						' <br/><em><strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
						. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), 
									 __( 'and any other calendar that uses .ics format', 'booking' )
									)
						. '</em>.<br/>'					
					);
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics; 
			?>
		</p>
		<div class="clear" style="margin:20px 0;"></div>
		<div class="wpbc-settings-notice notice-info" 
			 style="text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0; line-height: 2em;padding: 5px 20px;"
			 >
			<?php
				$message_ics = sprintf( 
						__( '.ics - is a file format of iCalendar standard for exchanging calendar and scheduling information between different sources %s Using a common calendar format (.ics), you can keep all your calendars updated and synchronized.', 'booking' )
						, '<br/>' /*
						'<br/><em>(<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
						. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), 
									 __( 'and any other calendar that uses .ics format', 'booking' )
									)
						. ')</em>.<br/>' */
					);
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics;
			?>
		</div>
		<?php if ( $is_import ) { ?>
		<h4 style="font-size:1.1em;">
			<?php
				// FixIn: 8.4.2.12
				$message_ics = sprintf( __( 'Is it automatic process?', 'booking' ) );
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics;
			?>
		</h4>
		<div class="wpbc-settings-notice notice-warning"
			 style="text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0; line-height: 2em;padding: 5px 20px;"
			 >
			<?php
				$message_ics = sprintf(
						__( 'By default .ics import is not automatic process. You need to set up CRON script on your server to periodically access front-end page(s) with import .ics feeds shortcodes.', 'booking' )
						, '<br/>' /*
						'<br/><em>(<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
						. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ),
									 __( 'and any other calendar that uses .ics format', 'booking' )
									)
						. ')</em>.<br/>' */
					);
				$message_ics = str_replace( array( '.ics', 'iCalendar' , 'CRON'), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' , '<a target="_blank" href="https://wpbookingcalendar.com/faq/cron-script/"><strong>CRON</strong></a>' ), $message_ics );
				echo $message_ics;
			?>
		</div>
		<h4 style="font-size:1.1em;">
			<?php 
				$message_ics = sprintf( __( 'How to start import of .ics feeds (files)?', 'booking' ) );
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics; 
			?>						
		</h4>
		<ol style="list-style-type: decimal !important;list-style-position: inside;margin-left: 15px;">
			<li><?php 				
				printf( __( 'Install %s plugin.', 'booking' ) 
				, '<a target="_blank" href="https://wordpress.org/plugins/booking-manager/"><strong>Booking Manager</strong></a>' );
			?></li>
			<li><?php 
				printf( __( 'Insert %s shortcode into  some post(s) or page(s). Check more info about this %sshortcode configuration%s', 'booking' ) 
				, '<code>[booking-manager-import ...]</code>'
				, '<a target="_blank" href="https://wpbookingcalendar.com/faq/booking-manager/">'
				, '</a>'
				);
			?>.
				<div class="wpbc-settings-notice notice-info" 
					 style='margin-left:25px;text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0;'><?php
					 
					$message_ics = sprintf( __( 'Using such shortcodes in pages give a great flexibility to import from  different .ics feeds (sources) into the same resource.%sAlso  its possible to define different CRON parameters for accessing such different pages with  different time intervals.', 'booking' )
											, '<br/>'
											);
					$message_ics = str_replace( array( '.ics', 'CRON' ), array( '<strong>.ics</strong>', '<a target="_blank" href="https://wpbookingcalendar.com/faq/cron-script/"><strong>CRON</strong></a>' ), $message_ics );
					echo $message_ics; 					 
				?>					
				</div>
				<span style="padding:0 15px;">
				<?php 
					$message_ics = sprintf( __( 'Or you can import .ics feed or file directly at current page.', 'booking' ) );
					$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
					echo $message_ics; 
				?>											
				</span>
			</li>
			<li>				<?php 
					$message_ics = sprintf( __( 'If you have inserted import shortcodes from %s, then  you can configure your CRON for periodically access these pages and import .ics feeds.', 'booking' )
											, '<a target="_blank" href="https://wordpress.org/plugins/booking-manager/"><strong>Booking Manager</strong></a> <code>[booking-manager-import ...]</code>'
										);
					$message_ics = str_replace( array( '.ics', 'CRON' ), array( '<strong>.ics</strong>', '<a target="_blank" href="https://wpbookingcalendar.com/faq/cron-script/"><strong>CRON</strong></a>' ), $message_ics );
					echo $message_ics; 
				?>											
			</li>
		</ol>
		<?php } else { ?>
		<h4 style="font-size:1.1em;">
			<?php 
				$message_ics = sprintf( __( 'How to start export of .ics feeds (files)?', 'booking' ) );
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics; 
			?>						
		</h4>
		<ol style="list-style-type: decimal !important;list-style-position: inside;margin-left: 15px;">
			<li><?php 				
				printf( __( 'Install %s plugin.', 'booking' ) 
				, '<a target="_blank" href="https://wordpress.org/plugins/booking-manager/"><strong>Booking Manager</strong></a>' );
			?></li>
			<li>
				<?php _e( 'Configure ULR feed(s) at this settings page.', 'booking' );  ?>
				<div class="wpbc-settings-notice notice-info" 
					 style='margin-left:25px;text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0;'>
				<?php 
					$message_ics = sprintf( 
										__( 'Using such URL(s) you can import .ics feeds, from  interface of other websites. %sCheck  more info  about how to import .ics feeds into other websites at the support pages of sepcific website.',  'booking' )
										, '<br/>');
					$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
					echo $message_ics; 
				?>						
				</div>
			</li>
			<li>			
			<?php 
				$message_ics = sprintf( __( 'Visit these (previously configured URL feeds) pages for downloading .ics files.', 'booking' ) );
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics; 
			?>						
			</li>
		</ol>		
		<?php } ?>		
	</div>		
	<?php
}



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// AJAX  Request
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/** JavaScript for Ajax */
function wpbc_ics_import_ajax_js() {
	
	$ajx_el_id = 'wpbc_import_ics_bar';
	
	// "wpbc-ajax.php" having this:			, 'WPBC_IMPORT_ICS_URL' => 'admin'
	
	?>
	<script type="text/javascript">
		// Ajax Request
		jQuery( function ( $ ) {																						// Shortcut to  jQuery(document).ready(function(){ ... });

			jQuery( '.wpbc_import_ics_bar' ).on( 'click', '.wpbc_import_btn', function ( event ) {						// This delegated event, can be run, when DOM element added after page loaded

				wpbc_admin_show_message_processing( '' ); 

				var jq_el = jQuery( this ).closest( '.wpbc_import_ics_bar' );

				var params_obj = {};
				params_obj.id      = jq_el.attr( 'id' );
				params_obj.nonce   = jq_el.attr( 'data-nonce' );
				params_obj.user_id = jq_el.attr( 'data-user-id' );
				
				params_obj.wpbc_import_url			= jQuery( '#wpbc_import_url' ).val();
				params_obj.wpbc_import_br_selection = 1;
				if ( jQuery( '#wpbc_import_br_selection option' ).length > 0 )
					params_obj.wpbc_import_br_selection = jQuery( '#wpbc_import_br_selection option' ).filter( ':selected' ).val();
				
// console.log(params_obj);

				jQuery.post( wpbc_ajaxurl, {
											action:     'WPBC_IMPORT_ICS_URL',
											user_id:    params_obj.user_id ,
											nonce:      params_obj.nonce,
											params:		params_obj
										},                                            
								function ( response_data, textStatus, jqXHR ) {                             // success	
									
									var my_message = '<?php echo html_entity_decode( esc_js( __('Done' ,'booking') ),ENT_QUOTES) ; ?>';
									wpbc_admin_show_message( my_message, 'info', 10000 , false );
								
									//console.log( response_data ); console.log( textStatus); console.log( jqXHR );        // Debug
									//jQuery( '.wpbc_system_info_log' ).show();				//Show Debug info
									jQuery( '.wpbc_system_info_log' ).html( response_data );                                     // For ability to show response, add such  DIV element to page
								}
						).fail( function ( jqXHR, textStatus, errorThrown ) {    
							wpbc_admin_show_message( '<strong style="text-transform: uppercase;">' + textStatus + '</strong> ~ ' + errorThrown , 'error', 5000 );
							if ( window.console && window.console.log ){ console.log( 'Ajax_Error', jqXHR, textStatus, errorThrown ); }     
						})  
						// .done( function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
						// .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
						;

			});

		});		
	</script>
	<?php	
}


/** Ajax Response */
function wpbc_ajax_WPBC_IMPORT_ICS_URL() {

		if ( ! isset( $_POST['params'] ) || empty( $_POST['params'] ) ) {
			exit;
		}
		
		// Check Security
		$action_nonce_name	= 'wpbc_import_ics_nonce_actn';
		$nonce_post_key = 'nonce';		
		$result = check_ajax_referer( $action_nonce_name, $nonce_post_key );							// Check Security

		
		$is_show_debug_info = (  ( get_bk_option( 'booking_is_show_system_debug_log' ) == 'On' ) ? true : false );
		if ( $is_show_debug_info )
			add_action( 'wpbc_show_debug', 'wpbc_start_showing_debug', 10, 1 );
		
		//////////////////////////////////////////////////////////////////////
		// Import events from .ics feed to specific booking resource
		//////////////////////////////////////////////////////////////////////
		do_action( 'wpbm_ics_import_start'
							, array(
									'url' => esc_url_raw( $_POST['params']['wpbc_import_url'] )
								  , 'resource_id' => intval( $_POST['params']['wpbc_import_br_selection'] )
								  , 'import_conditions' => ''													// Check dates availability and process only  
																												// if dates available in specific booking resource!
							) 
				);
		
		if ( $is_show_debug_info )		
			remove_action( 'wpbc_show_debug', 'wpbc_start_showing_debug', 10 );

		/*	
		if ( $is_show_debug_info ) {
			// Showingdebug log section
			?><script type="text/javascript"> jQuery( '.wpbc_system_info_log' ).show(); </script><?php
		}
		*/

		// send JSON
		//FixIn: 8.0.2.1		//Fix: We need to  comment this line,  because previously its possible that  we already  sent some messages,  and its does not correct  json format in this case.
							    //Fix: of showing "parsererror ~ SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data"
	    //wp_send_json( array( 'response' => 'success' ) );												// Return JS OBJ: response_data = { response: "success" }
		wp_die( '', '', array( 'response' => null  ) );
}
