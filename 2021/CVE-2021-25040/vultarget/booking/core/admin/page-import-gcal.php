<?php
/**
 * @version     1.0
 * @package     Booking > Settings > Import page
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2016-08-07
 * 
 * This is COMMERCIAL SCRIPT
 * We are do not guarantee correct work and support of Booking Calendar, if some file(s) was modified by someone else then wpdevelop.
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** API  for  Settings Page  */
class WPBC_API_SettingsImportGCal extends WPBC_Settings_API  {                             
    
    /**
	 * Settings API Constructor
     *  During creation,  system try to load values from DB, if exist.
     * 
     * @param type $id - "Pure Name"
     */
    public function __construct( $id,  $init_fields_values = array(), $options = array() ) {
        
        $default_options = array( 
                        'db_prefix_option' => '' 
                      , 'db_saving_type'   => 'separate'                 
            );                 
                                                                                // separate_prefix: update_bk_option( $this->options['db_prefix_option'] . $settings_id . '_' . $field_name , $value );
        $options = wp_parse_args( $options, $default_options );

        /**
	 	 * Activation  and deactivation  of these options already  done at  the wpbc-gcal.php file
         //
         // add_bk_action( 'wpbc_other_versions_activation',   array( $this, 'activate'   ) );      // Activate
         // add_bk_action( 'wpbc_other_versions_deactivation', array( $this, 'deactivate' ) );      // Deactivate
        */
        
        parent::__construct( $id, $options, $init_fields_values );              // Define ID of Setting page and options                
    }


    /** Define settings Fields  */
    public function init_settings_fields() {

        $this->fields = array();


        // Auto import
        if ( wpbc_is_mu_user_can_be_here('only_super_admin') ){ 

            $this->fields['booking_gcal_auto_import_is_active'] = array(   
                                          'type'        => 'checkbox'
                                        , 'default'     => 'Off'            
                                        , 'title'       => __( 'Activate auto import', 'booking' )
                                        , 'label'       => sprintf(__('Check this box to %sactivate%s auto import events and creation bookings from them' ,'booking'),'<b>','</b>')
                                        , 'description' => ''
                                        , 'group'       => 'auto_import'

                                    );
            $options = array();
            $options[1] = '1 ' . __( 'hour', 'booking' );
            for ( $i = 2; $i < 24; $i++ ) {
                $options[$i] = $i . ' ' . __( 'hours', 'booking' );
            }
            $options[24] = '1 ' . __( 'day', 'booking' );
            for ( $i = 2; $i < 32; $i++ ) {
                $options[( $i * 24)] = $i . ' ' . __( 'days', 'booking' );
            }
            $this->fields['booking_gcal_auto_import_time'] = array(   
                                        'type' => 'select'
                                        , 'default' => '24'
                                        , 'title' => __('Import events every' ,'booking')
                                        , 'description' => __('Select time duration of import requests.' ,'booking')
                                        , 'description_tag' => 'span'
                                        , 'css' => ''
                                        , 'options' => $options
                                        , 'tr_class'    => 'wpbc_sub_settings_grayed wpbc_tr_auto_import'
                                        , 'group' => 'auto_import'
                                );    
        }
        ////////////////////////////////////////////////////////////////////////
        
        
        // General Google Calendar Settings
        $this->fields['booking_gcal_api_key'] = array(   
                                      'type'        => 'text'
                                    , 'default'     => ''
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Google API Key', 'booking')
                                    , 'description' => __('Please enter your Google API key. This field required to import events.' ,'booking')
                                                        . '<div class="wpbc-settings-notice notice-info" style="text-align:left;"><strong>' 
                                                            . __('Note:' ,'booking') . '</strong> '
                                                            . sprintf( __('You can check in this %sinstruction how to generate and use your Google API key%s.' ,'booking')
                                                                        , '<a href="https://wpbookingcalendar.com/faq/import-gc-events/">'
                                                                        ,'</a>'
                                                                    )
                                                        . '</div>'
                                                        . ( ( wpbc_is_this_demo() ) ? wpbc_get_warning_text_in_demo_mode() : '' )
                                    , 'description_tag' => 'p'
                                    , 'css'         => ''//'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''                       // 'wpbc_sub_settings_grayed'
                                    //, 'validate_as' => array( 'required' )
                                    , 'is_demo_safe' => wpbc_is_this_demo()
                            );
        
        
        // General Google ID for Free version
        if ( ! class_exists('wpdev_bk_personal') ) 
            $this->fields['booking_gcal_feed'] = array(   
                                      'type'        => 'text'
                                    , 'default'     => ''
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Google Calendar ID', 'booking')
                                    , 'description' => ( ( wpbc_is_this_demo() ) ? wpbc_get_warning_text_in_demo_mode() : '' )
                                    , 'description_tag' => 'span'
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''                       // 'wpbc_sub_settings_grayed'
                                    //, 'validate_as' => array( 'required' )
                                    , 'is_demo_safe' => wpbc_is_this_demo()
                            );

        ////////////////////////////////////////////////////////////////////////
        

        //   M A X num   -    Default Settings
        $this->fields['booking_gcal_events_max'] = array(   
                                      'type'        => 'text'
                                    , 'default'     => '25'
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Maximum number', 'booking')
                                    , 'description' => __('You can specify the maximum number of events to import during one session.' ,'booking')
                                    , 'description_tag' => 'span'
                                    , 'css'         => 'width:4em'
                                    , 'group'       => 'default_settings'
                                    , 'tr_class'    => ''                       // 'wpbc_sub_settings_grayed'
                                    //, 'validate_as' => array( 'required' )
                            );
        //   F R O M   /////////////////////////////////////////////////////////
        $options = array(
                          "now" => __('Now' ,'booking')
                        , "today" => __('00:00 today' ,'booking')
                        , "week" => __('Start of current week' ,'booking')
                        , "month-start" => __('Start of current month' ,'booking')
                        , "month-end" => __('End of current month' ,'booking')
                        , "any" => __('The start of time' ,'booking')
                        , "date" => __('Specific date / time' ,'booking')
                        );
        $this->fields['booking_gcal_events_from'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'month-start'
                                    , 'title' => __('From', 'booking')
                                    , 'description' => __('Select option, when to start retrieving events.' ,'booking')
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'default_settings'
                            );   
        /////////////
                    $this->fields['booking_gcal_events_from_offset_html_prefix'] = array(   
                                                'type'          => 'pure_html'
                                                , 'group'       => 'default_settings'
                                                , 'html'        => '<tr valign="top" class="wpbc_tr_import_gcal_booking_gcal_events_from_offset wpbc_sub_settings_grayed wpbc_tr_from_offset">
                                                                        <th scope="row">
                                                                            <label class="wpbc-form-text" for="' 
                                                                                    . esc_attr( 'import_gcal_booking_gcal_events_from_offset' ) 
                                                                            . '">'  
                                                                                . '<span class="wpbc_offset_value">'     . wp_kses_post(  __('Offset' ,'booking') ) . '</span>'
                                                                                . '<span class="wpbc_offset_datetime">'  . wp_kses_post(  __('Enter date / time' ,'booking') ) . '</span>'
                                                                            . '</label>
                                                                        </th>
                                                                        <td><fieldset>'
                                        );                
                    $this->fields['booking_gcal_events_from_offset'] = array(   
                                                  'type'        => 'text'
                                                , 'default'     => ''
                                                //, 'placeholder' => ''
                                                , 'title'       => ''
                                                , 'description' => ''
                                                , 'description_tag' => 'span'
                                                , 'css'         => 'width:6em;height:28px;margin:0;vertical-align:middle;'
                                                , 'group'       => 'default_settings'
                                                , 'tr_class'    => 'wpbc_sub_settings_grayed wpbc_tr_from_offset'
                                            , 'only_field' => true
                                        );
                    $option_types = array(
                                          'second' => __('seconds' ,'booking')
                                        , 'minute' => __('minutes' ,'booking')
                                        , 'hour'   => __('hours' ,'booking')
                                        , 'day'    => __('days' ,'booking')
                                    );            
                    $this->fields['booking_gcal_events_from_offset_type'] = array(   
                                                'type' => 'select'
                                                , 'default' => ''
                                                , 'title' => ''
                                                , 'description' => ''
                                                , 'description_tag' => 'span'
                                                , 'css' => ''
                                                , 'options' => $option_types
                                                , 'group' => 'default_settings'
                                                , 'class'       => 'wpbc_offset_value'
                                                , 'tr_class'    => 'wpbc_sub_settings_grayed'
                                            , 'only_field' => true
                                        );  
                    $this->fields['booking_gcal_events_from_offset_html_sufix'] = array(   
                                                'type'          => 'pure_html'
                                                , 'group'       => 'default_settings'
                                                , 'html'        => '    <span class="description wpbc_offset_value">' 
                                                                        . __('You can specify an additional offset from you chosen start point. The offset can be negative.' ,'booking')
                                                                      . '</span>
                                                                         <span class="description wpbc_offset_datetime">' 
                                                                        . sprintf( __('Type your date in format %s. Example: %s' ,'booking'), '<code>Y-m-d</code>', '<code>' . date_i18n( 'Y-m-d' ) . '</code>' )
                                                                       . '</span>
                                                                           </fieldset>    
                                                                        </td>
                                                                    </tr>'            
                                        );                            
        /////////////
        
        
        //   U N T I L     /////////////////////////////////////////////////////
        $options = array(
                          "now" => __('Now' ,'booking')
                        , "today" => __('00:00 today' ,'booking')
                        , "week" => __('Start of current week' ,'booking')
                        , "month-start" => __('Start of current month' ,'booking')
                        , "month-end" => __('End of current month' ,'booking')
                        , "any" => __('The end of time' ,'booking')
                        , "date" => __('Specific date / time' ,'booking')
                        );
        $this->fields['booking_gcal_events_until'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'any'
                                    , 'title' => __('Until', 'booking')
                                    , 'description' => __('Select option, when to stop retrieving events.' ,'booking')
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'default_settings'
                            );        
        /////////////
                    $this->fields['booking_gcal_events_until_offset_html_prefix'] = array(   
                                                'type'          => 'pure_html'
                                                , 'group'       => 'default_settings'
                                                , 'html'        => '<tr valign="top" class="wpbc_tr_import_gcal_booking_gcal_events_until_offset wpbc_sub_settings_grayed wpbc_tr_until_offset">
                                                                        <th scope="row">
                                                                            <label class="wpbc-form-text" for="' 
                                                                                    . esc_attr( 'import_gcal_booking_gcal_events_until_offset' ) 
                                                                            . '">'  
                                                                                . '<span class="wpbc_offset_value">'     . wp_kses_post(  __('Offset' ,'booking') ) . '</span>'
                                                                                . '<span class="wpbc_offset_datetime">'  . wp_kses_post(  __('Enter date / time' ,'booking') ) . '</span>'
                                                                            . '</label>
                                                                        </th>
                                                                        <td><fieldset>'
                                        );                
                    $this->fields['booking_gcal_events_until_offset'] = array(   
                                                  'type'        => 'text'
                                                , 'default'     => ''
                                                //, 'placeholder' => ''
                                                , 'title'       => ''
                                                , 'description' => ''
                                                , 'description_tag' => 'span'
                                                , 'css'         => 'width:6em;height:28px;margin:0;vertical-align:middle;'
                                                , 'group'       => 'default_settings'
                                                , 'tr_class'    => 'wpbc_sub_settings_grayed wpbc_tr_until_offset'
                                            , 'only_field' => true
                                        );
                    $option_types = array(
                                          'second' => __('seconds' ,'booking')
                                        , 'minute' => __('minutes' ,'booking')
                                        , 'hour'   => __('hours' ,'booking')
                                        , 'day'    => __('days' ,'booking')
                                    );            
                    $this->fields['booking_gcal_events_until_offset_type'] = array(   
                                                'type' => 'select'
                                                , 'default' => ''
                                                , 'title' => ''
                                                , 'description' => ''
                                                , 'description_tag' => 'span'
                                                , 'css' => ''
                                                , 'options' => $option_types
                                                , 'group' => 'default_settings'
                                                , 'class'       => 'wpbc_offset_value'
                                                , 'tr_class'    => 'wpbc_sub_settings_grayed'
                                            , 'only_field' => true
                                        );  
                    $this->fields['booking_gcal_events_until_offset_html_sufix'] = array(   
                                                'type'          => 'pure_html'
                                                , 'group'       => 'default_settings'
                                                , 'html'        => '    <span class="description wpbc_offset_value">' 
                                                                        . __('You can specify an additional offset from you chosen start point. The offset can be negative.' ,'booking')
                                                                      . '</span>
                                                                         <span class="description wpbc_offset_datetime">' 
                                                                        . sprintf( __('Type your date in format %s. Example: %s' ,'booking'), '<code>Y-m-d</code>', '<code>' . date_i18n( 'Y-m-d' ) . '</code>' )
                                                                       . '</span>
                                                                           </fieldset>    
                                                                        </td>
                                                                    </tr>'            
                                        );                            
        /////////////

        ////////////////////////////////////////////////////////////////////////
        
        // Help 
        $this->fields['booking_gcal_events_help'] = array(   
                                        'type' => 'help'                                        
                                        , 'value' => array()
                                        , 'cols' => 2
                                        , 'group' => 'help'
                                );
        $this->fields['booking_gcal_events_help']['value'][] = '<h4 style="margin-top:-20px;">01. ' . __('To get Google Calendar API key please follow this instruction' ,'booking') . ':</h4>';
        $this->fields['booking_gcal_events_help']['value'][] = '<ol style="list-style-type: decimal !important;margin-left: 15px;font-size:0.86em;">';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Go to Google Developer Console: %s.' ,'booking'),'<a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>') . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Give your project a name and click "Create".' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('In the sidebar click on "APIs & auth".' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Click APIs and make sure "Calendar API" is set to ON.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Now click on "Credentials" in the sidebar.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Under the section "Public API access" click the button "Create new Key".' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('On the popup click the button "Server Key" and click "Create".' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('You will now see a table loaded with the top item being the API Key. Copy this and paste it into %sGoogle API Key%s field at this page.' ,'booking'),'<strong>','</strong>') . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '</ol>';
                            
        $this->fields['booking_gcal_events_help']['value'][] = '<h4>02. ' . __('Set Your Calendar to Public' ,'booking') . ':</h4>';
        $this->fields['booking_gcal_events_help']['value'][] = '<ol style="list-style-type: decimal !important;margin-left: 15px;font-size:0.86em;">';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Navigate to your Google calendars.' ,'booking'),'<a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>') . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Open the settings for the calendar.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Click the "Share this Calendar" link.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Click the checkbox to make calendar public. Do not check the other option.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '</ol>';
        
        $this->fields['booking_gcal_events_help']['value'][] = '<h4>03. ' . __('Find Your Calendar ID' ,'booking') . ':</h4>';
        $this->fields['booking_gcal_events_help']['value'][] = '<ol style="list-style-type: decimal !important;margin-left: 15px;font-size:0.86em;">';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Navigate to your Google calendars.' ,'booking'),'<a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>') . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Open the settings for the calendar.' ,'booking')) . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '<li>' . sprintf(__('Now copy the Calendar ID to use in the plugin settings in your WordPress admin. Make sure to %suse the Calendar ID only, not the entire XML feed URL%s.' ,'booking'),'<strong>','</strong>') . '</li>';
        $this->fields['booking_gcal_events_help']['value'][] = '</ol>';
        
        ////////////////////////////////////////////////////////////////////////
    }

}



/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsImportGCal extends WPBC_Page_Structure {
    

    public $settings_api = false;
    
    /**
	 * API - for Fields of this Settings Page
     * 
     * @param array $init_fields_values - array of init form  fields data - this array  can  ovveride "default" fields and loaded data.
     * @return object API
     */
    public function get_api( $init_fields_values = array() ){
        
        if ( $this->settings_api === false ) {
            $this->settings_api = new WPBC_API_SettingsImportGCal( 'import_gcal' , $init_fields_values );    
        }        
        return $this->settings_api;
    }


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
        
        $subtabs[ 'gcal' ] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' => __('Import Google Calendar Events', 'booking')	//__('Google Calendar' ,'booking') . '  - ' . __('Events Import' ,'booking')         // Title of TAB    
                            , 'page_title' => __('Import Google Calendar Events', 'booking')		//__('Import Settings' ,'booking')    // Title of Page   
                            , 'hint' => __('Import Google Calendar Events' ,'booking')      // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  false                               // Is this sub tab activated by default or not: true || false.
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

        do_action( 'wpbc_hook_settings_page_header', 'import_gcal_settings');    // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        // if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.
        
        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////
        
        $this->get_api();                                                       // Load fields Data from DB              

        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_import_gcal';                         // Define form name
        
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
        
        
        // Scroll links ////////////////////////////////////////////////////////
        ?>
        <div class="wpdvlp-sub-tabs" style="background:none;border:none;box-shadow: none;padding:0;"><span class="nav-tabs" style="text-align:right;">
            <a href="javascript:void(0);" onclick="javascript:wpbc_scroll_to('#wpbc_settings_import_gcal_events_general_metabox' );" original-title="" class="nav-tab go-to-link"><span><?php _e('General Settings', 'booking'); ?></span></a>
            <?php  if ( wpbc_is_mu_user_can_be_here('only_super_admin') ) {  ?>
            <a href="javascript:void(0);" onclick="javascript:wpbc_scroll_to('#wpbc_settings_import_gcal_events_auto_import_metabox' );" original-title="" class="nav-tab go-to-link"><span><?php _e('Auto import events' ,'booking'); ?></span></a>
            <?php } ?>
            <a href="javascript:void(0);" onclick="javascript:wpbc_scroll_to('#wpbc_settings_import_gcal_events_default_settings_metabox' );" original-title="" class="nav-tab go-to-link"><span><?php _e('Default settings for retrieving events' ,'booking'); ?></span></a>
            <?php  if ( class_exists('wpdev_bk_personal') ) {  ?>
            <a href="javascript:void(0);" onclick="javascript:wpbc_scroll_to('#wpbc_resource_table_gcal_id' );" original-title="" class="nav-tab go-to-link"><span><?php _e('Resources' ,'booking'); ?></span></a>
            <?php } ?>
            </span></div>
        <?php

        if ( class_exists('wpdev_bk_personal') )
            wpbc_toolbar_search_by_id__top_form( array( 
                                                    'search_form_id' => 'wpbc_booking_resources_search_form'
                                                  , 'search_get_key' => 'wh_resource_id'
                                                  , 'is_pseudo'      => false
                                            ) );

        
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

				// Add hidden input SEARCH KEY field into  main form, if previosly was searching by ID or Title
				if ( class_exists('wpdev_bk_personal') )
					wpbc_hidden_search_by_id_field_in_main_form( array( 'search_get_key' => 'wh_resource_id' ) );													//FixIn: 8.0.1.12

                ?><div class="clear" style="height:10px;"></div><?php
                     
                ?><div class="wpbc_settings_row wpbc_settings_row_left"><?php
                
                
                    wpbc_open_meta_box_section( 'wpbc_settings_import_gcal_events_general', __('Google Calendar - General Settings' ,'booking') );                               

                        $this->get_api()->show( 'general' );  
                        
                    wpbc_close_meta_box_section();


                    if ( wpbc_is_mu_user_can_be_here('only_super_admin') ){
                    
                        wpbc_open_meta_box_section( 'wpbc_settings_import_gcal_events_auto_import', __('Auto import events' ,'booking') );

                            $this->get_api()->show( 'auto_import' );                               

                        wpbc_close_meta_box_section();
                    }


                    wpbc_open_meta_box_section( 'wpbc_settings_import_gcal_events_default_settings', __('Default settings for retrieving events' ,'booking') );

                        $this->get_api()->show( 'default_settings' );  
                        
                    wpbc_close_meta_box_section();
                    
                ?>
                </div>  
                <div class="wpbc_settings_row wpbc_settings_row_right"><?php                
                
                    wpbc_open_meta_box_section( 'wpbc_settings_import_gcal_form_help', __('Help', 'booking') );
                        $this->get_api()->show( 'help' );   
                    wpbc_close_meta_box_section();
                ?>
                </div>
                <div id="wpbc_resources_link" class="clear"></div>
                <?php  if ( class_exists('wpdev_bk_personal') ) {  ?>
                <div id="wpbc_resource_table_gcal_id" class="wpbc_settings_row wpbc_settings_row_rightNO"><?php 
                
                    //wpbc_open_meta_box_section( 'wpbc_settings_import_gcal_resources', __('Resources', 'booking') );
                        
                        wpbc_import_gcal__show_table();
                        
                    //wpbc_close_meta_box_section();
                ?>
                </div>
                <div class="clear"></div>
                <?php  } ?>
                <input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" />  
            </form>
        </span>
        <?php       
    
        do_action( 'wpbc_hook_settings_page_footer', 'import_gcal_settings' );
        
        $this->enqueue_js();
    }


    /** Save Chanages */  
    public function update() {
//debuge($_POST);
        if ( function_exists( 'wpbc_import_gcal__update') )
            wpbc_import_gcal__update();

        // Get Validated Email fields
        $validated_fields = $this->get_api()->validate_post();
        
        $validated_fields = apply_filters( 'wpbc_fields_before_saving_to_db__import_gcal', $validated_fields );   //Hook for validated fields.
        
//debuge($validated_fields);        
        
        $this->get_api()->save_to_db( $validated_fields );
        
        wpbc_show_changes_saved_message();        
        
        // Old way of saving:
        // update_bk_option( 'booking_cache_expiration' , WPBC_Settings_API::validate_text_post_static( 'booking_cache_expiration' ) );
    }

	//TODO: clear unused CSS here

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
                        if ( ! jQuery('#import_gcal_booking_gcal_auto_import_is_active').is(':checked') ) {   
                            jQuery('.wpbc_tr_auto_import').addClass('hidden_items'); 
                        }
                      ";        
        // Hide|Show  on Click      Checkbox
        $js_script .= " jQuery('#import_gcal_booking_gcal_auto_import_is_active').on( 'change', function(){    
                                if ( this.checked ) { 
                                    jQuery('.wpbc_tr_auto_import').removeClass('hidden_items');
                                } else {
                                    jQuery('.wpbc_tr_auto_import').addClass('hidden_items');
                                }
                            } ); ";             
        //   F R O M
        $js_script .= " 
                        if ( jQuery('#import_gcal_booking_gcal_events_from').val() != 'date' ) {   
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_from_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#import_gcal_booking_gcal_events_from').on( 'change', function(){    
                            jQuery('#import_gcal_booking_gcal_events_from_offset').val('');
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
                        if ( jQuery('#import_gcal_booking_gcal_events_until').val() != 'date' ) {   
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').removeClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').addClass('hidden_items');
                        } else {
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_value').addClass('hidden_items');
                            jQuery('.wpbc_tr_until_offset .wpbc_offset_datetime').removeClass('hidden_items');                                    
                        }
                      ";
        // On select option in selectbox
        $js_script .= " jQuery('#import_gcal_booking_gcal_events_until').on( 'change', function(){    
                            jQuery('#import_gcal_booking_gcal_events_until_offset').val('');
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
add_action('wpbc_menu_created', array( new WPBC_Page_SettingsImportGCal() , '__construct') );    // Executed after creation of Menu


/**
	 * Override fields array  of Settings page,  AFTER saving to  DB. Some fields have to have different Values.
 *  Set  here values for our pseudo-options, after saving to  DB
 *  Because they was not overloading during this saving
 * 
 * @param array $fields
 * @param string $page_id
 * @return array - fields
 */
function wpbc_fields_after_saving_to_db__import_gcal( $fields, $page_id ) {
    
    if ( $page_id == 'import_gcal' ) {                                          // Check our API ID  relative saving of this settings page

        // Update Cron                                                          //FixIn: 7.0.1.9
        if ( $fields['booking_gcal_auto_import_is_active']['value'] == 'On' ) {
            
            update_bk_option( 'booking_gcal_auto_import_time', intval( $fields['booking_gcal_auto_import_time']['value'] ) );
            // add
            WPBC()->cron->update( 'wpbc_import_gcal'
                                        , array(     
                                               'action' => array( 'wpbc_silent_import_all_events' )                 // Action and parameters
                                             , 'start_time' => time()                                               // Now
                                             , 'recurrence' => intval( $fields['booking_gcal_auto_import_time']['value'] )    // Set  time in Hours
                                                ) 
                                             );

        } else {
            // delete
            WPBC()->cron->delete( 'wpbc_import_gcal' );
        }
        
    }
    return $fields;
}
add_filter('wpbc_fields_after_saving_to_db', 'wpbc_fields_after_saving_to_db__import_gcal', 10, 2);