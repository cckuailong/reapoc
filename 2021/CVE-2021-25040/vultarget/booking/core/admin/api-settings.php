<?php
/**
 * @version     1.0
 * @package     General Settings API - Saving different options
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2016-02-24
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// General Settings API - Saving different options
class  WPBC_Settings_API_General extends WPBC_Settings_API {
    

    /**
	 * Override Settings API Constructor
     *   During creation,  system try to load values from DB, if exist.
     * 
     *  @param type $id - of Settings
     */
    public function __construct( $id = '' ){
          
        $options = array( 
                        'db_prefix_option' => ''                                // 'booking_' 
                      , 'db_saving_type'   => 'separate' 
                      , 'id'               => 'set_gen'
            ); 
        
        $id = empty($id) ? $options['id'] : $id;
                
        parent::__construct( $id, $options );                                   // Define ID of Setting page and options
                
        add_action( 'wpbc_after_settings_content', array($this, 'enqueue_js'), 10, 3 );
    }

    
    /** Init all fields rows for settings page */
    public function init_settings_fields() {
        
        $this->fields = array();

        $default_options_values = wpbc_get_default_options();
        
        
        // <editor-fold     defaultstate="collapsed"                        desc=" C a l e n d a r    S e c t i o n "  >
        
        //  Calendar Skin  /////////////////////////////////////////////////////
        $calendar_skins_options  = array();
        
        // Skins in the Custom User folder (need to create it manually):    http://example.com/wp-content/uploads/wpbc_skins/ ( This folder do not owerwrited during update of plugin )
        $upload_dir = wp_upload_dir();             
        $files_in_folder = wpbc_dir_list( array(  '/css/skins/', $upload_dir['basedir'].'/wpbc_skins/' ) );  // Folders where to look about calendar skins

        foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';                        
            $skin_file[1] = str_replace( array( WPBC_PLUGIN_URL, $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for calendar skin  
            $calendar_skins_options[ $skin_file[1] ] = $skin_file[2];
        } 

        $this->fields['booking_skin'] = array(   
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_skin']      // '/css/skins/traditional.css'         // Activation|Deactivation  of this options in wpbc-activation  file.  // Default value in wpbc_get_default_options('booking_skin')
                                    //, 'value' => '/css/skins/standard.css'    //This will override value loaded from DB
                                    , 'title'       => __('Calendar Skin', 'booking')
                                    , 'description' => __('Select the skin of the booking calendar' ,'booking')
                                    , 'options'     => $calendar_skins_options
                                    , 'group'       => 'calendar'
                            );

//        //Show | Hide links for Advanced JavaScript section 
//        $this->fields['booking_skin_help'] = array(    
//                                  'type' => 'html'
//                                , 'html'  => 
//                                          '<div class="wpbc-settings-notice notice-info" style="text-align:left;">' 
//                                            . '<strong>' . __('Note!' ,'booking') . '</strong> '
//                                            . sprintf( __( 'If you have customized your own calendar skin, please save it to: %s Its will save your custom skin during future updates of plugin.', 'booking' ), '<code>/wp-content/uploads/wpbc_skins/</code><br/>' )                
//                                          . '</div>'  
//                                , 'cols'  => 2
//                                , 'group' => 'calendar'
//            );
        
        //  Number of months  //////////////////////////////////////////////////
        $months_options = array();
        for ($mm = 1; $mm < 12; $mm++) { $months_options[ $mm . 'm' ] = $mm . ' ' .  __('month(s)' ,'booking'); }
        for ($yy = 1; $yy < 11; $yy++) { $months_options[ $yy . 'y' ] = $yy . ' ' .  __('year(s)' ,'booking');  }
        
        $this->fields['booking_max_monthes_in_calendar'] = array(   
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_max_monthes_in_calendar']                   // '1y'            
                                    , 'title'       => __('Number of months to scroll', 'booking')
                                    , 'description' => __('Select the maximum number of months to show (scroll)' ,'booking')
                                    , 'options'     => $months_options
                                    , 'group'       => 'calendar'
                            );
        
        
        //  Start Day of the week  /////////////////////////////////////////////
        $this->fields['booking_start_day_weeek'] = array(   
                                    'type'          => 'select'
                                    , 'default' => $default_options_values['booking_start_day_weeek']                   // '2'            
                                    // , 'value' => false
                                    , 'title'       => __('Start Day of the week', 'booking')
                                    , 'description' => __('Select your start day of the week' ,'booking')
                                    , 'options'     => array(
                                                                  '0' => __('Sunday' ,'booking')
                                                                , '1' => __('Monday' ,'booking')
                                                                , '2' => __('Tuesday' ,'booking')
                                                                , '3' => __('Wednesday' ,'booking')
                                                                , '4' => __('Thursday' ,'booking')
                                                                , '5' => __('Friday' ,'booking')
                                                                , '6' => __('Saturday' ,'booking')                                        
                                                            )
                                    , 'group'       => 'calendar'
                            );
        
        //  Divider  ///////////////////////////////////////////////////////////        
        $this->fields['hr_calendar_after_week_day'] = array( 'type' => 'hr', 'group' => 'calendar' );
                
        

        $field_options = array(
                                  'single' => array(
                                                      'title' =>  __('Single day' ,'booking')
                                                    , 'attr' =>  array(
                                                                        'id' => 'type_of_day_selections_single'
                                                                    )
                                                )
                                , 'multiple' => array(
                                                      'title' =>  __('Multiple days' ,'booking')
                                                    , 'attr' =>  array(
                                                                        'id' => 'type_of_day_selections_multiple'
                                                                    )
                                                )
                            );
        //  Days  ///////////////////////////////////////////////////////////  
        $this->fields['booking_type_of_day_selections'] = array(   
                                    'type'          => 'radio'
                                    , 'default'     => $default_options_values['booking_type_of_day_selections']                   // 'multiple'            
                                    , 'title'       => __('Type of days selection in calendar', 'booking')
                                    , 'description' => ''
                                    , 'options'     => $field_options
                                    , 'group'       => 'calendar'
                            );
        
        ////////////////////////////////////////////////////////////////////////                                
        
        $this->fields = apply_filters( 'wpbc_settings_calendar_range_days_selection', $this->fields, $default_options_values );      // Range days
        $this->fields = apply_filters( 'wpbc_settings_calendar_recurrent_time_slots', $this->fields, $default_options_values );      // Recurent Times        
        $this->fields = apply_filters( 'wpbc_settings_calendar_check_in_out_times',   $this->fields, $default_options_values );      // Check In/Out Times
        $this->fields = apply_filters( 'wpbc_settings_calendar_showing_info_in_cal',  $this->fields, $default_options_values );      // Showing Cost, Availability in calendar...


        // </editor-fold>


        // <editor-fold     defaultstate="collapsed"                        desc=" T i m e    S l o t s "  >

		//FixIn: 8.7.11.10
	    $this->fields['booking_timeslot_picker'] = array(
	                            'type'          => 'checkbox'
	                            , 'default'     => $default_options_values['booking_timeslot_picker']   //'Off'
	                            , 'title'       => __('Time picker for time slots' ,'booking')
	                            , 'label'       => __('Show time slots as a time picker instead of a select box.' ,'booking')
	                            , 'description' => ''

	                            , 'group'       => 'time_slots'
	                            , 'tr_class'    => 'wpbc_timeslot_picker'
	        );


        //  Time Picker Skin  /////////////////////////////////////////////////////
        $timeslot_picker_skins_options  = array();

        // Skins in the Custom User folder (need to create it manually):    http://example.com/wp-content/uploads/wpbc_skins/ ( This folder do not owerwrited during update of plugin )
        $upload_dir = wp_upload_dir();
        $files_in_folder = wpbc_dir_list( array(  '/css/time_picker_skins/', $upload_dir['basedir'].'/wpbc_time_picker_skins/' ) );  // Folders where to look about Time Picker skins

        foreach ( $files_in_folder as $skin_file ) {                                                                            // Example: $skin_file['/css/skins/standard.css'] => 'Standard';
            $skin_file[1] = str_replace( array( WPBC_PLUGIN_URL, $upload_dir['basedir'] ), '', $skin_file[1] );                 // Get relative path for Time Picker skin
            $timeslot_picker_skins_options[ $skin_file[1] ] = $skin_file[2];
        }

        $this->fields['booking_timeslot_picker_skin'] = array(
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_timeslot_picker_skin']      // '/css/skins/traditional.css'         // Activation|Deactivation  of this options in wpbc-activation  file.  // Default value in wpbc_get_default_options('booking_skin')
                                    //, 'value' => '/css/time_picker_skins/grey.css'    //This will override value loaded from DB
                                    , 'title'       => __('Time Picker Skin', 'booking')
                                    , 'description' => __('Select the skin of the time picker' ,'booking')
                                    , 'options'     => $timeslot_picker_skins_options
                                    , 'group'       => 'time_slots'
                            );



		//FixIn: 8.2.1.27
	    $this->fields['booking_timeslot_day_bg_as_available'] = array(
	                            'type'          => 'checkbox'
	                            , 'default'     => $default_options_values['booking_timeslot_day_bg_as_available']   //'Off'
	                            , 'title'       => __('Do not change background color for partially booked days' ,'booking')
	                            , 'label'       => __('Show partially booked days with same background as in legend item' ,'booking')
	                            , 'description' => '<span class="description0" style="line-height: 1.7em;margin: 0 0 0 -10px;"><strong>' . __('Note' ,'booking') .':</strong> '
                                                        . sprintf(__('Partially booked item - day, which is booked for the specific time-slot(s).' ,'booking'),'<b>','</b>')
                                                   . '</span>'

	                            , 'group'       => 'time_slots'
	                            , 'tr_class'    => 'wpbc_timeslot_day_bg_as_available'
	        );

	    $this->fields = apply_filters( 'wpbc_settings_calendar_title_for_timeslots',  $this->fields, $default_options_values );      // Showing Title near timeslots in tooltip at calendar...
	    // </editor-fold>


        // <editor-fold     defaultstate="collapsed"                        desc=" A v a i l a b i l i t y "  >
        
        //  Unavailable week days  /////////////////////////////////////////////

        $this->fields['booking_unavailable_day_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'availability'
                                    , 'html'        => '<tr valign="top">
                                                            <th scope="row">
                                                                <label class="wpbc-form-checkbox" for="' 
                                                                                // . esc_attr( 'unavailable_day0' ) 
                                                                . '">' . wp_kses_post(  __('Unavailable week days' ,'booking') ) 
                                                                . '</label>
                                                            </th>
                                                            <td><fieldset>'
                            );        
        $this->fields['booking_unavailable_day0'] = array(  'label'  => __('Sunday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day0'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day1'] = array(  'label'  => __('Monday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day1'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day2'] = array(  'label'  => __('Tuesday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day2'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day3'] = array(  'label'  => __('Wednesday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day3'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day4'] = array(  'label'  => __('Thursday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day4'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day5'] = array(  'label'  => __('Friday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day5'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day6'] = array(  'label'  => __('Saturday' ,'booking')             
                                                    , 'type' => 'checkbox', 'default' => $default_options_values['booking_unavailable_day6'], 'only_field' => true, 'group' => 'availability', 'is_new_line' => false
                                            );
        $this->fields['booking_unavailable_day_html_sufix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'availability'
                                    , 'html'        => '    </fieldset><p class="description">' 
                                                            . __('Check unavailable days in calendars. This option will overwrite all other settings.' ,'booking') 
                                                            . '</p>
                                                            </td>
                                                        </tr>'            
                            );        
 
        //  Divider  ///////////////////////////////////////////////////////////        
        $this->fields['hr_calendar_after_unavailable_day'] = array( 'type' => 'hr', 'group' => 'availability' );
        
        
        
        //  Unavailable days from today  ///////////////////////////////////////
        $field_options = array();
        for ($ii = 0; $ii < 32; $ii++) { $field_options[ $ii ] = $ii; }
        
        $this->fields['booking_unavailable_days_num_from_today'] = array(   
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_unavailable_days_num_from_today']                                  //'0'            
                                    , 'title'       => __('Unavailable days from today', 'booking')
                                    , 'description' => __('Select number of unavailable days in calendar start from today.' ,'booking')
                                    , 'options'     => $field_options
                                    , 'group'       => 'availability'
                            );

        //  Limit available days from today  ///////////////////////////////////        
        $this->fields = apply_filters( 'wpbc_settings_calendar_unavailable_days', $this->fields, $default_options_values );
        

        //  Extend unavailable booking dates interval - cleaning  //////////////        
        $this->fields = apply_filters( 'wpbc_settings_calendar_extend_unavailable_interval', $this->fields, $default_options_values );
        
        // </editor-fold>
        
        
        // <editor-fold     defaultstate="collapsed"                        desc=" F o r m    S e c t i o n "  >


        //  Start Day of the week  /////////////////////////////////////////////
		/*
	    if ( ! class_exists('wpdev_bk_personal') )
		    $this->fields['booking_form_structure_type'] = array(
                                    'type'          => 'select'
                                    , 'default' => $default_options_values['booking_form_structure_type']                   // '2'
                                    // , 'value' => false
                                    , 'title'       => __('Booking form structure', 'booking')
                                    , 'description' => __('Select how to show your booking form.' ,'booking')
		                            , 'description_tag' => 'p'
                                    , 'options'     => array(
                                                              'vertical' => __('Form under calendar' ,'booking')
                                                            , 'form_right' => __('Form at right side of calendar' ,'booking')
                                                            )
                                    , 'group'       => 'form'
                            );
		*/
		if ( class_exists( 'wpdev_bk_personal' ) )                                                                      //FixIn: 8.1.1.12
            $this->fields['booking_is_use_simple_booking_form'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_simple_booking_form']        //'Off'
                                , 'title'       => __('Simple' ,'booking') . ' ' . __('Booking Form', 'booking')
                                , 'label'       => __('Check the box, if you want to use simple booking form customization from Free plugin version at Settings - Form page.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );
		if ( class_exists( 'wpdev_bk_personal' ) )                                                                      //FixIn: 8.1.1.12
            $this->fields['booking_is_use_codehighlighter_booking_form'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_codehighlighter_booking_form']        //'Off'
                                , 'title'       => __('Syntax highlighter' ,'booking')
                                , 'label'       => __('Check the box, if you want to use syntax highlighter during customization booking form.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );




        $this->fields['booking_is_use_captcha'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_captcha']           //'Off'
                                , 'title'       => __('CAPTCHA' ,'booking')
                                , 'label'       => __('Check the box to activate CAPTCHA inside the booking form.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );
        $this->fields['booking_is_use_autofill_4_logged_user'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_autofill_4_logged_user']         // 'Off'            
                                , 'title'       => __('Auto-fill fields' ,'booking')
                                , 'label'       => __('Check the box to activate auto-fill form fields for logged in users.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'form'
            );       
        $this->fields['booking_form_is_using_bs_css'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_form_is_using_bs_css']         // 'On'            
                                , 'title'       => __('Use CSS BootStrap' ,'booking')
                                , 'label'       => __('Using BootStrap CSS for the form fields' ,'booking')
                                , 'description' => '<strong>' . __('Note' ,'booking') . ':</strong> ' . __('You must not deactivate loading BootStrap files at advanced section of these settings!' ,'booking')
                                , 'description_tag' => 'p'    
                                , 'group'       => 'form'
            );       

//        if (  class_exists( 'wpdev_bk_personal' ) ){        //FixIn: 8.8.1.14
//
//	        $this->fields['booking_send_button_title'] = array(
//	                                'type'          => 'text'
//	                                , 'default'     => $default_options_values['booking_send_button_title']             // 'Send'
//	                                , 'placeholder' => __( 'Send', 'booking' )
//	                                , 'title'       => __( 'Title of send button' ,'booking' )
//	                                , 'description' => sprintf(__('Enter %stitle of submit button%s in the booking form' ,'booking'),'<b>','</b>')
//	                                , 'description_tag' => 'p'
//	                                , 'css'         => 'width:100%'
//	                                , 'group'       => 'form'
//	                                , 'tr_class'    => 'wpbc_send_button_title'
//	                        );
//		}

        // <editor-fold     defaultstate="collapsed"                        desc=" L e g e n d    I t e m s "  >
        // Legend Items ////////////////////////////////////////////////////////
        $this->fields['booking_is_show_legend'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_show_legend']         // 'Off'            
                                , 'title'       => __('Show legend below calendar' ,'booking')
                                , 'label'       => __('Check this box to display a legend of dates below the booking calendar.' ,'booking')
                                , 'description' => '' 
                                , 'group'       => 'form'
            );             
        // Available item
        $this->fields['booking_legend_is_show_item_available_prefix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_legend_is_show_item_available 
                                                                            wpbc_calendar_legend_items wpbc_sub_settings_grayed">
                                                        <th scope="row">'.
                                                            WPBC_Settings_API::label_static( 'set_gen_booking_legend_is_show_item_available'
                                                                , array(   'title'=> __('Available item' ,'booking'), 'label_css' => '' ) )
                                                        .'</th>
                                                        <td><fieldset>'
                        );                
        $this->fields['booking_legend_is_show_item_available'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_legend_is_show_item_available']         // 'On'            
                                , 'is_new_line' => false
                                , 'group'       => 'form'
                                , 'only_field'  => true
            ); 
        $this->fields['booking_legend_text_for_item_available'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_legend_text_for_item_available']         // __('Available' ,'booking')
                                , 'placeholder' => __('Available' ,'booking')
                                , 'css'         => '' //'width:8em;'
                                , 'group'       => 'form'
                                , 'only_field'  => true           
                        );
        $this->fields['booking_legend_is_show_item_available_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        =>    '<p class="description" style="line-height: 1.7em;margin: 0;">' 
                                                        . sprintf(__('Activate and type your %stitle of available%s item in legend' ,'booking'),'<b>','</b>')
                                                    . '</p>
                                                           </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
        // Pending item
        $this->fields['booking_legend_is_show_item_pending_prefix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_legend_is_show_item_pending 
                                                                            wpbc_calendar_legend_items wpbc_sub_settings_grayed">
                                                        <th scope="row">'.
                                                            WPBC_Settings_API::label_static( 'set_gen_booking_legend_is_show_item_pending'
                                                                , array(   'title'=> __('Pending item' ,'booking'), 'label_css' => '' ) )
                                                        .'</th>
                                                        <td><fieldset>'
                        );                
        $this->fields['booking_legend_is_show_item_pending'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_legend_is_show_item_pending']         // 'On'            
                                , 'is_new_line' => false
                                , 'group'       => 'form'
                                , 'only_field'  => true
            ); 
        $this->fields['booking_legend_text_for_item_pending'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_legend_text_for_item_pending']         // __('Pending' ,'booking')
                                , 'placeholder' => __('Pending' ,'booking')
                                , 'css'         => '' //'width:8em;'
                                , 'group'       => 'form'
                                , 'only_field'  => true           
                        );
        $this->fields['booking_legend_is_show_item_pending_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        =>    '<p class="description" style="line-height: 1.7em;margin: 0;">' 
                                                        . sprintf(__('Activate and type your %stitle of pending%s item in legend' ,'booking'),'<b>','</b>')
                                                    . '</p>
                                                           </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
        // Approved item
        $this->fields['booking_legend_is_show_item_approved_prefix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_legend_is_show_item_approved 
                                                                            wpbc_calendar_legend_items wpbc_sub_settings_grayed">
                                                        <th scope="row">'.
                                                            WPBC_Settings_API::label_static( 'set_gen_booking_legend_is_show_item_approved'
                                                                , array(   'title'=> __('Approved item' ,'booking'), 'label_css' => '' ) )
                                                        .'</th>
                                                        <td><fieldset>'
                        );                
        $this->fields['booking_legend_is_show_item_approved'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_legend_is_show_item_approved']         // 'On'            
                                , 'is_new_line' => false
                                , 'group'       => 'form'
                                , 'only_field'  => true
            ); 
        $this->fields['booking_legend_text_for_item_approved'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_legend_text_for_item_approved']         //__('Booked' ,'booking')
                                , 'placeholder' => __('Booked' ,'booking')
                                , 'css'         => '' //'width:8em;'
                                , 'group'       => 'form'
                                , 'only_field'  => true           
                        );
        $this->fields['booking_legend_is_show_item_approved_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        =>    '<p class="description" style="line-height: 1.7em;margin: 0;">' 
                                                        . sprintf(__('Activate and type your %stitle of approved%s item in legend' ,'booking'),'<b>','</b>')
                                                    . '</p>
                                                           </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
        if ( class_exists('wpdev_bk_biz_s') ) { 
        // Partially booked item
        $this->fields['booking_legend_is_show_item_partially_prefix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_legend_is_show_item_partially 
                                                                            wpbc_calendar_legend_items wpbc_sub_settings_grayed">
                                                        <th scope="row">'.
                                                            WPBC_Settings_API::label_static( 'set_gen_booking_legend_is_show_item_partially'
                                                                , array(   'title'=> __('Partially booked item' ,'booking'), 'label_css' => '' ) )
                                                        .'</th>
                                                        <td><fieldset>'
                        );                
        $this->fields['booking_legend_is_show_item_partially'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_legend_is_show_item_partially']         //'On'            
                                , 'is_new_line' => false
                                , 'group'       => 'form'
                                , 'only_field'  => true
            ); 
        $this->fields['booking_legend_text_for_item_partially'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_legend_text_for_item_partially']         //__('Partially booked' ,'booking')
                                , 'placeholder' => __('Partially booked' ,'booking')
                                , 'css'         => '' //'width:8em;'
                                , 'group'       => 'form'
                                , 'only_field'  => true           
                        );
        $this->fields['booking_legend_is_show_item_partially_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        =>    '<p class="description" style="line-height: 1.7em;margin: 0;">' 
                                                        . sprintf(__('Activate and type your %stitle of partially booked%s item in legend' ,'booking'),'<b>','</b>')
                                                    . '</p>'
                                                    . '<p class="description" style="line-height: 1.7em;margin: 0;"><strong>' . __('Note' ,'booking') .':</strong> ' 
                                                        . sprintf(__('Partially booked item - day, which is booked for the specific time-slot(s).' ,'booking'),'<b>','</b>') 
                                                    . '</p>'    
                                                           .'</fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
        }
//        //  Help Section ///////////////////////////////////////////////////////
//        $this->fields['booking_help_translation_section_after_legend_items'] = array(   
//                                  'type'              => 'help'
//                                , 'value'             => wpbc_get_help_rows_about_config_in_several_languges()
//                                , 'class'             => ''
//                                , 'css'               => ''
//                                , 'description'       => ''
//                                , 'cols'              => 2 
//                                , 'group'             => 'form'
//                                , 'tr_class'          => 'wpbc_calendar_legend_items wpbc_sub_settings_grayed'
//                                , 'description_tag'   => 'span'
//                        );
        $this->fields['booking_legend_is_show_numbers'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_legend_is_show_numbers']         //'On'            
                                , 'title'       => __('Show date number in legend' ,'booking')
                                , 'label'       => sprintf(__('Check this box to display today date number in legend cells. ' ,'booking'),'<b>','</b>')
                                , 'description' => '' 
                                , 'tr_class'    => 'wpbc_calendar_legend_items wpbc_sub_settings_grayed'                    
                                , 'group'       => 'form'
            );                             
        // </editor-fold>
        
        $field_options = array(
                                    'message'  => array( 'title' => __('Show "Thank You" message' ,'booking'), 'attr' => array( 'id' => 'type_of_thank_you_message_message' ) )
                                  , 'page' => array( 'title' => __('Redirect visitor to a new "Thank You" page' ,'booking'), 'attr' => array( 'id' => 'type_of_thank_you_message_page' ) )
                            ); 
        if ( class_exists('wpdev_bk_biz_s') )   $description_text = '<strong>' . __('Note' ,'booking') . ':</strong> ' . __('This action will have no effect, if the payment form(s) is active!' ,'booking');
        else                                    $description_text = '';
        $this->fields['booking_type_of_thank_you_message'] = array(   
                                    'type'          => 'radio'
                                    , 'default'     => $default_options_values['booking_type_of_thank_you_message']         //'message'            
                                    , 'title'       => __('Action after booking is done' ,'booking')
                                    , 'description' => $description_text
                                    , 'options'     => $field_options
                                    , 'group'       => 'form'
                            );

        $this->fields['booking_title_after_reservation'] = array(   
                                'type'          => 'textarea'
                                , 'default'     => $default_options_values['booking_title_after_reservation']         //sprintf(__('Thank you for your online booking. %s We will send confirmation of your booking as soon as possible.' ,'booking'), '')
                                , 'placeholder' => sprintf(__('Thank you for your online booking. %s We will send confirmation of your booking as soon as possible.' ,'booking'), '')
                                , 'title'       => __('Message title' ,'booking')
                                , 'description' => sprintf(__('Type title of message %safter booking has done by user%s' ,'booking'),'<b>','</b>')
                                ,'description_tag' => 'p'
                                , 'css'         => 'width:100%'
                                , 'rows' => 2
                                , 'group'       => 'form'
                                , 'tr_class'    => 'wpbc_calendar_thank_you_message wpbc_calendar_thank_you wpbc_sub_settings_grayed'
                        );
        $this->fields['booking_title_after_reservation_time'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_title_after_reservation_time']         //'7000'
                                , 'placeholder' => '7000'
                                , 'title'       => __('Time of message showing' ,'booking')
                                , 'description' => sprintf(__('Set duration of time (milliseconds) to show this message' ,'booking'),'<b>','</b>')
                                , 'description_tag' => 'span'
                                , 'css'         => 'width:5em'
                                , 'group'       => 'form'
                                , 'tr_class'    => 'wpbc_calendar_thank_you_message wpbc_calendar_thank_you wpbc_sub_settings_grayed'
                        );
//        //  Help Section ///////////////////////////////////////////////////////
//        $this->fields['booking_help_translation_section_after_thank_you_message'] = array(   
//                                  'type'              => 'help'
//                                , 'value'             => wpbc_get_help_rows_about_config_in_several_languges()
//                                , 'class'             => ''
//                                , 'css'               => ''
//                                , 'description'       => ''
//                                , 'cols'              => 2 
//                                , 'group'             => 'form'
//                                , 'tr_class'          => 'wpbc_calendar_thank_you_message wpbc_calendar_thank_you wpbc_sub_settings_grayed'
//                                , 'description_tag'   => 'span'
//                        );
        
        //  URL of "Thank you page"
        $this->fields['booking_thank_you_page_URL_prefix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_thank_you_page_URL 
                                                                            wpbc_calendar_thank_you_page wpbc_calendar_thank_you wpbc_sub_settings_grayed">
                                                        <th scope="row">'.
                                                            WPBC_Settings_API::label_static( 'set_gen_booking_thank_you_page_URL'
                                                                , array(   'title'=> __('URL of "thank you" page' ,'booking'), 'label_css' => '' ) )
                                                        .'</th>
                                                        <td><fieldset>' . '<code style="font-size:14px;">' . home_url() . '</code>'         //FixIn: 7.0.1.20
                        );                
        $this->fields['booking_thank_you_page_URL'] = array(   
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_thank_you_page_URL']         //'/thank-you'
                                , 'placeholder' => '/thank-you'
                                , 'css'         => 'width:75%'
                                , 'group'       => 'form'
                                , 'only_field'  => true           
                        );
        $this->fields['booking_thank_you_page_URL_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'form'
                                , 'html'        =>    '<p class="description" style="line-height: 1.7em;margin: 0;">' 
                                                        . sprintf(__('Type URL of %s"Thank You" page%s' ,'booking'),'<b>','</b>')
                                                    . '</p>
                                                           </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        
        // </editor-fold>


        // <editor-fold     defaultstate="collapsed"                        desc=" Booking Admin Panel "  >
        
        $field_options = array(
                                  'vm_listing' => __('Bookings Listing' ,'booking') 
                                , 'vm_calendar' => __('Calendar Overview' ,'booking')
                            );   
        $this->fields['booking_listing_default_view_mode'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_listing_default_view_mode']         //'vm_calendar'            
                                , 'title'       => __('Default booking admin page', 'booking')
                                , 'description' => __('Select your default view mode of bookings at the booking listing page' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_listing'
                        );
        
        //Default booking resources 
        $this->fields = apply_filters( 'wpbc_settings_booking_listing_br_default_count', $this->fields, $default_options_values );

        //  Divider  ///////////////////////////////////////////////////////////    
        $this->fields['hr_booking_listing_before_view_days_num'] = array( 'type' => 'hr', 'group' => 'booking_listing' );

		//FixIn: 8.6.1.13

        // Calendar Default View mode 
        if ( class_exists( 'wpdev_bk_personal' ) ) 
            $field_options = array(
                                      '1' => __('Day' ,'booking')
                                    , '7' => __('Week' ,'booking')
                                    , '30' => __('Month' ,'booking')
                                    , '60' => __('2 Months' ,'booking')
                                    , '90' => __('3 Months' ,'booking')
                                    , '365' => __('Year' ,'booking')
                                );                                                      
        else
             $field_options = array(
                                      '30' => __('Month' ,'booking')
                                    , '90' => __('3 Months' ,'booking')
                                    , '365' => __('Year' ,'booking')
                                );                                                      
        $this->fields['booking_view_days_num'] = array( 
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_view_days_num']         //'30'            
                                , 'title'       => __('Default calendar view mode', 'booking')
                                , 'description' => __('Select your default calendar view mode at booking calendar overview page' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_timeline'   //FixIn: 8.5.2.20
                        );
        
        //Default Titles in Calendar cells
        $this->fields = apply_filters( 'wpbc_settings_booking_listing_timeline_title_in_day', $this->fields, $default_options_values ); 
        
        // Default Toolbar
        $field_options = array(
                                 'filter' => __('Filter tab' ,'booking')
                               , 'actions' => __('Actions tab' ,'booking')
                           );                                                      
        $this->fields['booking_default_toolbar_tab'] = array( 
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_default_toolbar_tab']         //'filter'            
                                , 'title'       => __('Default toolbar tab', 'booking')
                                , 'description' => __('Select your default opened tab in toolbar at booking listing page' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_listing'
                        );
        // Bookings Number / page
        $field_options = array();
        foreach ( array( 5, 10, 20, 25, 50, 75, 100 ) as $value ) {
            $field_options[ $value ] = $value;
        }           
        $this->fields['booking_num_per_page'] = array(  
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_num_per_page']         //'10'            
                                , 'title'       => __('Bookings number per page', 'booking')
                                , 'description' => __('Select number of bookings per page in booking listing' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_listing'
                        );
        
        // Bookings Sort Order
        $field_options = array(
                                  ''                => __( 'ID', 'booking' ) . '&nbsp;' . __( 'ASC', 'booking' )
                                , 'booking_id_asc'  => __( 'ID', 'booking' ) . '&nbsp;' . __( 'DESC', 'booking' )
                                , 'sort_date'       => __( 'Dates', 'booking' ) . '&nbsp;' . __( 'ASC', 'booking' )
                                , 'sort_date_asc'   => __( 'Dates', 'booking' ) . '&nbsp;' . __( 'DESC', 'booking' )
        ); 
        if ( class_exists( 'wpdev_bk_personal' ) ) {
            $field_options['booking_type']      = __('Resource', 'booking') . '&nbsp;' . __('ASC', 'booking');
            $field_options['booking_type_asc']  = __('Resource', 'booking') . '&nbsp;' . __('DESC', 'booking');
        }
        if ( class_exists( 'wpdev_bk_biz_s' ) ) {
            $field_options['cost']              = __('Cost', 'booking') . '&nbsp;' . __('ASC', 'booking');
            $field_options['cost_asc']          = __('Cost', 'booking') . '&nbsp;' . __('DESC', 'booking');
        }
        $this->fields['booking_sort_order'] = array( 
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_sort_order']         //''            
                                , 'title'       => __('Bookings default order', 'booking')
                                , 'description' => __('Select your default order of bookings in the booking listing' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_listing'
                        );
 
        // CSV data separator
        $this->fields = apply_filters( 'wpbc_settings_booking_listing_csv_separator', $this->fields, $default_options_values ); 
        
        
        // Dates Format ////////////////////////////////////////////////////////

        $this->fields['booking_date_format_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'booking_listing'
                                    , 'html'        => '<tr valign="top" class="wpbc_tr_set_gen_booking_date_format">
                                                            <th scope="row">'.
                                                                WPBC_Settings_API::label_static( 'set_gen_booking_date_format'
                                                                    , array(   'title'=> __('Date Format' ,'booking'), 'label_css' => 'margin: 0.25em 0 !important;vertical-align: middle;' ) )
                                                            .'</th>
                                                            <td><fieldset>'
                            );          
        $field_options = array();
        foreach ( array( __('F j, Y'), 'Y/m/d', 'm/d/Y', 'd/m/Y' ) as $format ) {
            $field_options[ esc_attr($format) ] = array( 'title' => date_i18n( $format ) );
        }
        $field_options['custom'] =  array( 'title' =>  __('Custom' ,'booking') . ':', 'attr' =>  array( 'id' => 'date_format_selection_custom' ) );

        $this->fields['booking_date_format_selection'] = array(   
                                    'type'          => 'radio'
                                    , 'default'     => get_option('date_format')
                                    , 'options'     => $field_options
                                    , 'group'       => 'booking_listing'
                                    , 'only_field'  => true
                            );

        $booking_date_format = get_bk_option( 'booking_date_format');       
        $this->fields['booking_date_format'] = array(  
                                'type'          => 'text'
                                , 'default'     => $default_options_values['booking_date_format']         //get_option('date_format')
                                , 'value'       => htmlentities( $booking_date_format )      // Display value of this field in specific way
                                , 'group'       => 'booking_listing'
                                , 'placeholder' => get_option('date_format')
                                , 'css'         => 'width:10em;'
                                , 'only_field'  => true
            );    

        $this->fields['booking_date_format_html_sufix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'booking_listing'
                                    , 'html'        => '          <span class="description"><code>' . date_i18n( $booking_date_format ) . '</code></span>'
                                                                . '<p class="description">' 
                                                                    . sprintf(__('Type your date format for emails and the booking table. %sDocumentation on date formatting%s' ,'booking'),'<br/><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>')
                                                            . '   </p>
                                                               </fieldset>
                                                            </td>
                                                        </tr>'            
                            );        
        
        // Time Format
        $this->fields = apply_filters( 'wpbc_settings_booking_time_format', $this->fields, $default_options_values );

	    //FixIn: 8.7.4.1
        // Is show help hints on the admin panel ///////////////////////////////
        $this->fields['booking_is_use_localized_time_format'] = array(
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_localized_time_format']         //'Off'
                                , 'title'       => __('Use localized time format' ,'booking')
                                , 'label'       => __('This option useful only, if you have issue with translation of time format. If you activated this option, at some servers possible issue with "Daylight Saving Time" - booked times can be later on 1 hour.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'booking_listing'
            );

        
        // Default Dates View Mode /////////////////////////////////////////////
        $field_options = array(
                                 'short' => __('Short days view' ,'booking')
                               , 'wide' => __('Wide days view' ,'booking')
                           );                                                      
        $this->fields['booking_date_view_type'] = array( 
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_date_view_type']         //'short'            
                                , 'title'       => __('Dates view', 'booking')
                                , 'description' => __('Select the default view for dates on the booking tables' ,'booking')
                                , 'options'     => $field_options
                                , 'group'       => 'booking_listing'
                        );

        //  Divider  ///////////////////////////////////////////////////////////////       
        $this->fields['hr_booking_listing_before_is_use_hints_at_admin_panel'] = array( 'type' => 'hr', 'group' => 'booking_listing' );


        // Show hide Notes
        $this->fields = apply_filters( 'wpbc_settings_booking_show_hide_options', $this->fields, $default_options_values );         //FixIn: 8.1.3.32

        // Is show help hints on the admin panel ///////////////////////////////
        $this->fields['booking_is_use_hints_at_admin_panel'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_use_hints_at_admin_panel']         //'On'            
                                , 'title'       => __('Show / hide hints' ,'booking')
                                , 'label'       => __('Check this box if you want to show help hints on the admin panel.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'booking_listing'
            );       
        
        // </editor-fold>
        
        
        // <editor-fold     defaultstate="collapsed"                        desc=" auto_cancelation_approval "  >
        
        // auto_cancelation_approval
        $this->fields = apply_filters( 'wpbc_settings_auto_cancelation_approval_section', $this->fields, $default_options_values );
        // </editor-fold>
        
        
        // <editor-fold     defaultstate="collapsed"                        desc=" Advanced "  >
        
        $this->fields['booking_is_days_always_available'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_days_always_available']         //'On'            
                                , 'title'       => __('Allow unlimited bookings per same day(s)' ,'booking')
                                , 'label'       => sprintf(__('Check this box, if you want to %sset any days as available%s in calendar. Your visitors will be able to make %sunlimited bookings per same date(s) in calendar and do not see any booked date(s)%s of other visitors.' ,'booking'), '<strong>', '</strong>' , '<strong>', '</strong>' )
                                , 'description' => ''
                                , 'group'       => 'advanced'
            );      

        //FixIn: 8.3.2.2
        if ( ! class_exists('wpdev_bk_biz_l') )
		    $this->fields['booking_is_show_pending_days_as_available'] = array(
		                            'type'          => 'checkbox'
		                            , 'default'     => $default_options_values['booking_is_show_pending_days_as_available']   //_'Off'
		                            , 'title'       =>  __('Use pending days as available' ,'booking')
		                            , 'label'       => sprintf(__('Check this box if you want to show the pending days as available in calendars' ,'booking') )
		                            , 'description' => ''
		                            , 'group'       => 'advanced'
		                            , 'tr_class'    => ''
		        );

        $this->fields = apply_filters( 'wpbc_settings_pending_days_as_available', $this->fields, $default_options_values ); 
        
        $this->fields['booking_check_on_server_if_dates_free'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_check_on_server_if_dates_free']         //'Off'            
                                , 'title'       => __('Checking to prevent double booking, during submitting booking' ,'booking')
                                , 'label'       => sprintf(__('Check this box, if you want to %sre-check if the selected dates available during submitting booking%s.' ,'booking'), '<strong>', '</strong>' , '<strong>', '</strong>' )
                                , 'description' => '<strong>' . __('Note' ,'booking') . '!</strong> '
                                                    . __('This feature useful to prevent double booking of the same date(s) or time(s), if several visitors try to book the same date(s) in same calendar during the same time.' ,'booking')
                                                    . ( ( class_exists('wpdev_bk_biz_l')) ?  ' ' . __('This feature does not work for booking resources with capacity higher than one.' ,'booking') : '' )            
                                , 'group'       => 'advanced'
            );       
        
        $this->fields = apply_filters( 'wpbc_settings_capacity_based_on_visitors', $this->fields, $default_options_values ); 
                
        $this->fields = apply_filters( 'wpbc_settings_edit_url_hash', $this->fields, $default_options_values ); 
        
        // Show advanced settings of JavaScript loading
        
        //  Divider  ///////////////////////////////////////////////////////////////        
        $this->fields['hr_calendar_before_advanced_js_loading_settings'] = array( 'type' => 'hr', 'group' => 'advanced' );
        
        //Show | Hide links for Advanced JavaScript section 
        $this->fields['booking_advanced_js_loading_settings'] = array(    
                                  'type' => 'html'
                                , 'html'  =>  
                                          '<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings of JavaScript loading' ,'booking') . '</a>'
                                        . '<a id="wpbc_show_advanced_section_link_hide" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings of JavaScript loading' ,'booking') . '</a>'
                                , 'cols'  => 2
                                , 'group' => 'advanced'
            );

        $this->fields['booking_is_not_load_bs_script_in_client'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_not_load_bs_script_in_client']         //'Off'            
                                , 'title'       => __('Disable Bootstrap loading on Front-End' ,'booking')
                                , 'label'       => __(' If your theme or some other plugin is load the BootStrap JavaScripts, you can disable  loading of this script by this plugin.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items'
            );       
        $this->fields['booking_is_not_load_bs_script_in_admin'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_not_load_bs_script_in_admin']         //'Off'            
                                , 'title'       => __('Disable Bootstrap loading on Back-End' ,'booking')
                                , 'label'       => __(' If your theme or some other plugin is load the BootStrap JavaScripts, you can disable  loading of this script by this plugin.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items'
            );       
        $this->fields['hr_calendar_before_is_load_js_css_on_specific_pages'] = array( 'type' => 'hr', 'group' => 'advanced', 'tr_class' => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items' );
        $this->fields['booking_is_load_js_css_on_specific_pages'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_load_js_css_on_specific_pages']         //'Off'            
                                , 'title'       => __('Load JS and CSS files only on specific pages' ,'booking')
                                , 'label'       => __('Activate loading of CSS and JavaScript files of plugin only at specific pages.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items'
                                , 'is_demo_safe' => wpbc_is_this_demo()
            );       
        $this->fields['booking_pages_for_load_js_css'] = array(   
                                'type'          => 'textarea'
                                , 'default'     => $default_options_values['booking_pages_for_load_js_css']         //''
                                , 'placeholder' => '/booking-form/'
                                , 'title'       => __('Relative URLs of pages, where to load plugin CSS and JS files' ,'booking')
                                , 'description' => sprintf(__('Enter relative URLs of pages, where you have Booking Calendar elements (booking forms or availability calendars). Please enter one URL per line. Example: %s' ,'booking'),'<code>/booking-form/</code>')
                                ,'description_tag' => 'p'
                                , 'css'         => 'width:100%'
                                , 'rows'        => 5
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_advanced_js_loading_settings wpbc_is_load_js_css_on_specific_pages wpbc_sub_settings_grayed hidden_items'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );        
		
		$this->fields['hr_booking_is_show_system_debug_log'] = array( 'type' => 'hr', 'group' => 'advanced', 'tr_class' => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items' );
		//FixIn: 7.2.1.15
        $this->fields[ 'booking_is_show_system_debug_log' ] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_show_system_debug_log']         //'Off'            
                                , 'title'       => __('Show system debugging log for beta features' ,'booking')
                                , 'label'       => __('Activate this option only for testing beta features' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items'
                                , 'is_demo_safe' => wpbc_is_this_demo()
            );       

		
        if ( wpbc_is_this_demo() ) 
            $this->fields['booking_pages_for_load_js_css_demo'] = array( 'group' => 'advanced', 'type' => 'html', 'html' => wpbc_get_warning_text_in_demo_mode(), 'cols' => 2 , 'tr_class' => 'wpbc_advanced_js_loading_settings wpbc_sub_settings_grayed hidden_items' ); 
        
        
        // Show settings of powered by notice
        $this->fields['booking_advanced_powered_by_notice_settings'] = array(    
                                  'type' => 'html'
                                , 'html'  =>  
                                          '<a id="wpbc_powered_by_link_show" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show settings of powered by notice' ,'booking') . '</a>'
                                        . '<a id="wpbc_powered_by_link_hide" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide settings of powered by notice' ,'booking') . '</a>'
                                , 'cols'  => 2
                                , 'group' => 'advanced'
            );
        $this->fields['booking_is_show_powered_by_notice'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_show_powered_by_notice']         //'On'            
                                , 'title'       => __('Powered by notice' ,'booking')
                                , 'label'       => sprintf(__(' Turn On/Off powered by "Booking Calendar" notice under the calendar.' ,'booking'),'wpbookingcalendar.com')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_is_show_powered_by_notice wpbc_sub_settings_grayed hidden_items'
            );       
        $this->fields['booking_wpdev_copyright_adminpanel'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_wpdev_copyright_adminpanel']         //'On'            
                                , 'title'       => __('Help and info notices' ,'booking')
                                , 'label'       => sprintf(__(' Turn On/Off version notice and help info links at booking admin panel.' ,'booking'),'wpbookingcalendar.com')
                                , 'description' => ''
                                , 'group'       => 'advanced'
                                , 'tr_class'    => 'wpbc_is_show_powered_by_notice wpbc_sub_settings_grayed hidden_items'
	                            , 'is_demo_safe' => wpbc_is_this_demo()                                                 //FixIn: 8.1.3.9
            );       
        
        // </editor-fold>
                                 
        
        // <editor-fold     defaultstate="collapsed"                        desc=" Information "  >
        if (  function_exists( 'wpbc_get_dashboard_info' ) ) {
            $this->fields['booking_information'] = array(   
                               'type'              => 'html'
                             , 'html'              => wpbc_get_dashboard_info()
                             , 'cols'              => 2
                             , 'group'             => 'information'
                     ); 
        }
        // </editor-fold>

        
        // <editor-fold     defaultstate="collapsed"                        desc=" User permissions for plugin menu pages "  >
        
        
        $this->fields['booking_menu_position'] = array(   
                                'type'          => 'select'
                                , 'default'     => 'top'
                                , 'title'       => __('Plugin menu position', 'booking')
                                , 'description' => ''
                                , 'options'     => array(
                                                              'top'     => __('Top', 'booking')
                                                            , 'middle'  => __('Middle', 'booking')
                                                            , 'bottom'  => __('Bottom', 'booking')
                                                        )
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );
        
        $this->fields['booking_user_role_booking_header'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'permissions'
                                    , 'html'        => '<tr valign="top">
                                                            <th scope="row" colspan="2">
                                                                <hr/><p><strong>' . wp_kses_post(  __('User permissions for plugin menu pages' ,'booking') )  . ':</strong></p>
                                                            </th>
                                                        </tr>'
                            );        
        
        $field_options = array();
        $field_options['subscriber']    = translate_user_role('Subscriber');
        $field_options['contributor']   = translate_user_role('Contributor');
        $field_options['author']        = translate_user_role('Author');
        $field_options['editor']        = translate_user_role('Editor');
        $field_options['administrator'] = translate_user_role('Administrator');
        
        $this->fields['booking_user_role_booking'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_user_role_booking']         //'editor'            
                                , 'title'       => __('Bookings', 'booking')
                                , 'description' => ''
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );
        $this->fields['booking_user_role_addbooking'] = array(   
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_user_role_addbooking']         //'editor'            
                                , 'title'       => __('Add booking', 'booking')
                                , 'description' => ''
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );
        if ( class_exists( 'wpdev_bk_personal' ) ) 
            $this->fields['booking_user_role_resources'] = array(   
                                    'type'          => 'select'
                                    , 'default'     => $default_options_values['booking_user_role_resources']         //'editor'            
                                    , 'title'       => __('Resources', 'booking')
                                    , 'description' => ''
                                    , 'options'     => $field_options
                                    , 'group'       => 'permissions'
                                    , 'is_demo_safe' => wpbc_is_this_demo()
                            );
        $this->fields['booking_user_role_settings'] = array(
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_user_role_settings']         //'administrator'
                                , 'title'       => __('Settings', 'booking')
                                , 'description' => __('Select user access level for the menu pages of plugin' ,'booking')
                                , 'description_tag' => 'p'
                                , 'options'     => $field_options
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );

	    if ( ! class_exists( 'wpdev_bk_personal' ) )
		    $this->fields['booking_menu_go_pro'] = array(
                                'type'          => 'select'
                                , 'default'     => $default_options_values['booking_menu_go_pro']         //'administrator'
                                , 'title'       => __('Premium', 'booking')
                                , 'description' => __('Show / hide menu' ,'booking')
                                , 'description_tag' => 'p'
                                , 'options'     => array(
				                                          'show' => __('Show', 'booking')
                                	                    , 'hide' => __('Hide', 'booking')
			                                        )
                                , 'group'       => 'permissions'
                                , 'is_demo_safe' => wpbc_is_this_demo()
                        );

        if ( wpbc_is_this_demo() )
            $this->fields['booking_user_role_settings_demo'] = array( 'group' => 'permissions', 'type' => 'html', 'html' => wpbc_get_warning_text_in_demo_mode(), 'cols' => 2 ); 
        
        
        // </editor-fold>
        
                
        // <editor-fold     defaultstate="collapsed"                        desc=" Uninstall "  >
        $this->fields['booking_is_delete_if_deactive'] = array(   
                                'type'          => 'checkbox'
                                , 'default'     => $default_options_values['booking_is_delete_if_deactive']         //'Off'            
                                , 'title'       => __('Delete booking data, when plugin deactivated' ,'booking')
                                , 'label'       => __('Check this box to delete all booking data when you uninstal this plugin.' ,'booking')
                                , 'description' => ''
                                , 'group'       => 'uninstall'
            );       
        // </editor-fold>
        
        
        // <editor-fold     defaultstate="collapsed"                        desc=" Help "  >
        $this->fields['help_translation_section_after_legend_items'] = array(   
                           'type'              => 'help'
                         , 'value'             => wpbc_get_help_rows_about_config_in_several_languges()
                         , 'class'             => ''
                         , 'css'               => 'margin:0;padding:0;border:0;'
                         , 'description'       => ''
                         , 'cols'              => 2 
                         , 'group'             => 'help'
                         , 'tr_class'          => ''
                         , 'description_tag'   => 'p'
                 ); 

        if ( ( ! wpbc_is_this_demo() ) && ( current_user_can( 'activate_plugins' ) ) ) {         
        
            $this->fields['help_translation_section_after_legend_items']['value'][] = 
                '<div class="clear"></div><hr/><center><a class="button button" href="' 
                                                                        . wpbc_get_settings_url() 
                                                                        . '&system_info=show#wpbc_general_settings_system_info_metabox">' 
                                                                                . 'Booking System ' . __('Info' ,'booking') 
                                                        . '</a></center>';

            if (  $_SERVER['HTTP_HOST'] === 'beta'  ) {
	            // // Link: http://server.com/wp-admin/admin.php?page=wpbc-settings&system_info=show&pot=1#wpbc_general_settings_system_info_metabox
	            $this->fields['help_translation_section_after_legend_items']['value'][] =
		            '<div class="clear"></div><hr/><center><a class="button button" href="'
		            . wpbc_get_settings_url()
		            . '&system_info=show&pot=1#wpbc_general_settings_system_info_metabox">'
		            . 'Generate POT file'
		            . '</a></center>';
	            // Link: http://server.com/wp-admin/admin.php?page=wpbc-settings&system_info=show&reset=custom_forms#wpbc_general_settings_system_info_metabox
	            $this->fields['help_translation_section_after_legend_items']['value'][] =
		            '<div class="clear"></div><hr/><center><a class="button button" href="'
		            . wpbc_get_settings_url()
		            . '&system_info=show&reset=custom_forms#wpbc_general_settings_system_info_metabox">'
		            . 'Reset custom forms'
		            . '</a></center>';
            }

	        //FixIn: 8.4.7.19
            $this->fields['help_translation_section_after_legend_items']['value'][] =
	            '<div class="clear"></div><hr/><center><a class="button button" href="'
	            . wpbc_get_bookings_url()
	              . '&wh_booking_type=lost">'
	            //. '&wh_booking_datenext=1&wh_booking_dateprior=1&wh_booking_date=3&wh_trash=any&wh_modification_dateprior=1&wh_modification_date=3&wh_pay_status=all&view_mode=vm_listing&wh_booking_type">'
	            . 'Find Lost Bookings'
	            . '</a></center>';  //FixIn: 8.5.2.19
        }
        if ( 0 ) { // ! wpbc_is_this_demo() ) {

            $this->fields['help_translation_section_after_legend_items']['value'][] =
                '<div class="clear"></div><hr/><center><a class="button button" href="'
                                                                        . wpbc_get_settings_url()
                                                                        . '&restore_dismissed=On#wpbc_general_settings_restore_dismissed_metabox">'
                                                                                . __('Restore all dismissed windows' ,'booking')
                                                        . '</a></center>';
        }

        // </editor-fold>
        
//debuge($this->fields);die;                
    }      
    

    /**
	 * Add Custon JavaScript - for some specific settings options
     *      Need to executes after showing of entire settings page (on hook: wpbc_after_settings_content).
     *      After initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     * 
     */
    public function enqueue_js( $menu_slug, $active_page_tab, $active_page_subtab ) {

        $js_script = '';
        
        // Hide Legend items 
        $js_script .= " 
                        if ( ! jQuery('#set_gen_booking_is_show_legend').is(':checked') ) {   
                            jQuery('.wpbc_calendar_legend_items').addClass('hidden_items'); 
                        }
                      ";        
        // Hide or Show Legend items on click checkbox
        $js_script .= " jQuery('#set_gen_booking_is_show_legend').on( 'change', function(){    
                                if ( this.checked ) { 
                                    jQuery('.wpbc_calendar_legend_items').removeClass('hidden_items');
                                } else {
                                    jQuery('.wpbc_calendar_legend_items').addClass('hidden_items');
                                }
                            } ); ";        
        // Thank you Message or Page
        $js_script .= " 
                        if ( jQuery('#type_of_thank_you_message_message').is(':checked') ) {   
                            jQuery('.wpbc_calendar_thank_you_page').addClass('hidden_items'); 
                        }
                        if ( jQuery('#type_of_thank_you_message_page').is(':checked') ) {   
                            jQuery('.wpbc_calendar_thank_you_message').addClass('hidden_items'); 
                        }
                      ";        
        $js_script .= " jQuery('input[name=\"set_gen_booking_type_of_thank_you_message\"]').on( 'change', function(){    
                                if ( jQuery('#type_of_thank_you_message_message').is(':checked') ) {   
                                    jQuery('.wpbc_calendar_thank_you_message').removeClass('hidden_items');
                                    jQuery('.wpbc_calendar_thank_you_page').addClass('hidden_items'); 
                                } else {
                                    jQuery('.wpbc_calendar_thank_you_message').addClass('hidden_items');
                                    jQuery('.wpbc_calendar_thank_you_page').removeClass('hidden_items'); 
                                }
                            } ); ";    
        
        // Default calendar view mode (Booking Listing) - set  active / inctive options depend from  resource selection.
        $js_script .= " jQuery('#set_gen_booking_view_days_num').on( 'focus', function(){    
                            if ( jQuery('#set_gen_booking_default_booking_resource').length > 0 ) {
                                jQuery('#set_gen_booking_default_booking_resource').on('change', function() {
                                    jQuery('#set_gen_booking_view_days_num option:eq(2)').prop('selected', true);
                                });
                                if ( jQuery('#set_gen_booking_default_booking_resource').val() == '' ) { 
                                    jQuery('#set_gen_booking_view_days_num option:eq(0)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(1)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(2)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(3)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(4)').prop('disabled', true);
                                    jQuery('#set_gen_booking_view_days_num option:eq(5)').prop('disabled', true);
                                } else {
                                    jQuery('#set_gen_booking_view_days_num option:eq(0)').prop('disabled', true);
                                    jQuery('#set_gen_booking_view_days_num option:eq(1)').prop('disabled', true);
                                    jQuery('#set_gen_booking_view_days_num option:eq(2)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(3)').prop('disabled', true);
                                    jQuery('#set_gen_booking_view_days_num option:eq(4)').prop('disabled', false);
                                    jQuery('#set_gen_booking_view_days_num option:eq(5)').prop('disabled', false);                                                                
                                }
                            }
                        } ); ";        
        
        ////////////////////////////////////////////////////////////////////////
        // Set  correct  value for dates format,  depend from selection of radio buttons
        $booking_date_format = get_bk_option( 'booking_date_format');       
        // On initial Load set correct text value and correct radio button
        $js_script .= " 
                        // Select by  default Custom  value, later  check all other predefined values
                        jQuery( '#date_format_selection_custom' ).prop('checked', true);

                        jQuery('input[name=\"set_gen_booking_date_format_selection\"]').each(function() {
                           var radio_button_value = jQuery( this ).val()
                           var encodedStr = radio_button_value.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                                                                                        return '&#'+i.charCodeAt(0)+';';
                                                                                    });
                           if ( encodedStr == '". $booking_date_format ."' ) {
                                jQuery( this ).prop('checked', true);                     
                           }
                        });
                        
                        jQuery('#set_gen_booking_date_format').val('". $booking_date_format ."');
                        ";
        // On click Radio button "Date Format", - set value in custom Text field
        $js_script .= " jQuery('input[name=\"set_gen_booking_date_format_selection\"]').on( 'change', function(){    
                                if (  ( this.checked ) && ( jQuery(this).val() != 'custom' )  ){ 

                                    jQuery('#set_gen_booking_date_format').val( jQuery(this).val().replace(/[\u00A0-\u9999<>\&]/gim, 
                                        function(i) {
                                            return '&#'+i.charCodeAt(0)+';';
                                        }) 
                                    );
                                }                            
                            } ); "; 
        // If we edit custom "Date Format" Text  field - select Custom Radio button.                                 
        $js_script .= " jQuery('#set_gen_booking_date_format').on( 'change', function(){                                              
                                jQuery( '#date_format_selection_custom' ).prop('checked', true);
                            } ); ";        
        
        
        ////////////////////////////////////////////////////////////////////////
        // Advanced section
        ////////////////////////////////////////////////////////////////////////
        
        // Click on "Allow unlimited bookings per same day(s)"
        $js_script .= " jQuery('#set_gen_booking_is_days_always_available').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'booking' ) ) . '! '
                                              . esc_js( __( 'You allow unlimited number of bookings per same dates, its can be a reason of double bookings on the same date. Do you really want to do this?', 'booking' ) ) 
                                      .  "' );  
                                if ( answer) { 
                                    this.checked = true;   
                                    jQuery('#set_gen_booking_check_on_server_if_dates_free').prop('checked', false );                                    
                                    jQuery('#set_gen_booking_is_show_pending_days_as_available').prop('checked', false );            
                                    jQuery('.wpbc_pending_days_as_available_sub_settings').addClass('hidden_items'); 
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";

        //FixIn: 8.3.2.2
	    if ( ! class_exists('wpdev_bk_biz_l') ) {
	    	// Click on "Use pending days as available"
	        $js_script .= " jQuery('#set_gen_booking_is_show_pending_days_as_available').on( 'change', function(){            
                            if ( this.checked ) { 
                                jQuery('#set_gen_booking_check_on_server_if_dates_free').prop('checked', false );
                                jQuery('#set_gen_booking_is_days_always_available').prop('checked', false );
                            } else {

                            }
                        } ); ";
        }
        //FixIn: 8.3.2.2
        // Click on "Checking to prevent double booking, during submitting booking"
        $js_script .= " jQuery('#set_gen_booking_check_on_server_if_dates_free').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'booking' ) ) . '! '
                                              . esc_js( __( 'This feature can impact to speed of submitting booking. Do you really want to do this?', 'booking' ) ) 
                                      .  "' );  
                                if ( answer) { 
                                    this.checked = true;   
                                    jQuery('#set_gen_booking_is_days_always_available').prop('checked', false );
                                    jQuery('#set_gen_booking_is_show_pending_days_as_available').prop('checked', false );
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";   
        
        // Click  on Show Advanced JavaScript section  link
        $js_script .= " jQuery('#wpbc_show_advanced_section_link_show').on( 'click', function(){                                 
                            jQuery('#wpbc_show_advanced_section_link_show').toggle(200);                            
                            jQuery('#wpbc_show_advanced_section_link_hide').animate( {opacity: 1}, 200 ).toggle(200);     
                            jQuery('.wpbc_advanced_js_loading_settings').removeClass('hidden_items'); 
                            
                            if ( ! jQuery('#set_gen_booking_is_load_js_css_on_specific_pages').is(':checked') ) {   
                                jQuery('.wpbc_is_load_js_css_on_specific_pages').addClass('hidden_items'); 
                            }
                        } ); ";   
        $js_script .= " jQuery('#wpbc_show_advanced_section_link_hide').on( 'click', function(){    
                            jQuery('#wpbc_show_advanced_section_link_hide').toggle(200);                            
                            jQuery('#wpbc_show_advanced_section_link_show').animate( {opacity: 1}, 200 ).toggle(200);                        
                            jQuery('.wpbc_advanced_js_loading_settings').addClass('hidden_items'); 
                        } ); ";   
        // Click on "is_not_load_bs_script_in_client"
        $js_script .= " jQuery('#set_gen_booking_is_not_load_bs_script_in_client, #set_gen_booking_is_not_load_bs_script_in_admin').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'booking' ) ) . '! '
                                              . esc_js( __( 'You are need to be sure what you are doing. You are disable of loading some JavaScripts Do you really want to do this?', 'booking' ) )                                                              
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                       
                                } else { 
                                    this.checked = false; 
                                } 
                            }                            
                        } ); ";       
        $js_script .= " jQuery('#set_gen_booking_is_load_js_css_on_specific_pages').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'booking' ) ) . '! '
                                              . esc_js( __( 'You are need to be sure what you are doing. You are disable of loading some JavaScripts Do you really want to do this?', 'booking' ) )                                                                                                                           
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                       
                                    jQuery('.wpbc_is_load_js_css_on_specific_pages').removeClass('hidden_items'); 
                                } else { 
                                    this.checked = false; 
                                } 
                            } else {
                                jQuery('.wpbc_is_load_js_css_on_specific_pages').addClass('hidden_items'); 
                            }
                        } );                         
                        ";         
        
        
        // Click  on Powered by  links
        $js_script .= " jQuery('#wpbc_powered_by_link_show').on( 'click', function(){                                 
                            jQuery('#wpbc_powered_by_link_show').toggle(200);                            
                            jQuery('#wpbc_powered_by_link_hide').animate( {opacity: 1}, 200 ).toggle(200);  
                            jQuery('.wpbc_is_show_powered_by_notice').removeClass('hidden_items');                             
                        } ); ";   
        $js_script .= " jQuery('#wpbc_powered_by_link_hide').on( 'click', function(){    
                            jQuery('#wpbc_powered_by_link_hide').toggle(200);                            
                            jQuery('#wpbc_powered_by_link_show').animate( {opacity: 1}, 200 ).toggle(200);   
                            jQuery('.wpbc_is_show_powered_by_notice').addClass('hidden_items'); 
                        } ); ";   

        
        // Show confirmation window,  if user activate this checkbox
        $js_script .= " jQuery('#set_gen_booking_is_delete_if_deactive').on( 'change', function(){    
                            if ( this.checked ) { 
                                var answer = confirm('"                 
                                              . esc_js( __( 'Warning', 'booking' ) ) . '! '
                                              . esc_js( __( 'If you check this option, all booking data will be deleted when you uninstall this plugin. Do you really want to do this?', 'booking' ) )                                                        
                                      .  "' );  
                                if ( answer) {
                                    this.checked = true;                                                                           
                                } else { 
                                    this.checked = false; 
                                } 
                            }
                        } );                         
                        ";         


        // Select  specific Time Picker skin,  depending from  selection  of Calendar skin      //FixIn: 8.7.11.10
        $js_script .= " jQuery('#set_gen_booking_skin').on( 'change', function(){    
        
                            var wpbc_selected_skin = jQuery('select[name=\"set_gen_booking_skin\"] option:selected').val(); 
                            var wpbc_cal_skin_arr = [      
													'/css/skins/black-2.css',
													'/css/skins/black.css',
													'/css/skins/multidays.css',
													'/css/skins/premium-black.css',
													'/css/skins/premium-light.css',
													'/css/skins/premium-marine.css',
													'/css/skins/premium-steel.css',
													'/css/skins/standard.css',
													'/css/skins/traditional-light.css',
													'/css/skins/traditional.css'
                                                ];
                            var wpbc_time_skin_arr = [      
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/green.css',
													'/css/time_picker_skins/black.css',
													'/css/time_picker_skins/grey.css',
													'/css/time_picker_skins/marine.css',
													'/css/time_picker_skins/grey.css',
													'/css/time_picker_skins/blue.css',
													'/css/time_picker_skins/orange.css',
													'/css/time_picker_skins/grey.css',
                                                ];  
                            if ( wpbc_cal_skin_arr.indexOf( wpbc_selected_skin ) >= 0 ) {
								jQuery( '#set_gen_booking_timeslot_picker_skin' ).find( 'option' ).prop( 'selected', false );								
								jQuery( '#set_gen_booking_timeslot_picker_skin' ).find( 'option[value=\"'+ wpbc_time_skin_arr[ wpbc_cal_skin_arr.indexOf( wpbc_selected_skin ) ]  +'\"]' ).prop( 'selected', true );																
                            }
        
						} ); ";


        // Eneque JS to  the footer of the page
        wpbc_enqueue_js( $js_script );
    }
    
}


/**
 * Override VALIDATED fields BEFORE saving to DB
 * Description:
 * Check "Thank you page" URL
 *
 * @param array $validated_fields
 */
function wpbc_settings_validate_fields_before_saving__all( $validated_fields ) {


    $validated_fields['booking_thank_you_page_URL'] = wpbc_make_link_relative( $validated_fields['booking_thank_you_page_URL'] );
    
    unset( $validated_fields[ 'booking_date_format_selection' ] );                      // We do not need to this field,  because saving to DB only: "date_format" field
    
    return $validated_fields;
}
add_filter( 'wpbc_settings_validate_fields_before_saving', 'wpbc_settings_validate_fields_before_saving__all', 10, 1 );   // Hook for validated fields.