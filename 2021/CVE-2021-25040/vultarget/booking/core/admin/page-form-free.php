<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Booking Form Settings
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-03-23
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


require_once( WPBC_PLUGIN_DIR . '/core/admin/page-form-timeslots.php' );         // Timeslots Generator


/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsFormFieldsFree extends WPBC_Page_Structure {
    
    /** Need define some filters */
    public function __construct() {

        // Get booking form  in real  HTML
        add_bk_filter('wpbc_get_free_booking_form',         array( $this, 'get_form_in__html' ) );        
        
        // Get content of booking form show in shortcodes
        add_bk_filter('wpbc_get_free_booking_show_form',        array( $this, 'get_form_show_in__shortcodes' ) );
        // Get booking form  in Shortcodes 
        add_bk_filter('wpbc_get_free_booking_form_shortcodes',  array( $this, 'get_form_in__shortcodes' ) );
        
        
        /**
	 	 * We need to  update these fields after  usual update process :
         * 'booking_form'
         * 'booking_form_show'
         * 'booking_form_visual'
        */
        add_bk_action( 'wpbc_other_versions_activation',   array( $this, 'activate'   ) );      // Activate
        add_bk_action( 'wpbc_other_versions_deactivation', array( $this, 'deactivate' ) );      // Deactivate

        parent::__construct();
    }
    
    public function in_page() {
        return 'wpbc-settings';
    }
    
    
    public function tabs() {
        
        $tabs = array();
                
        $tabs[ 'form' ] = array(
                              'title'     => __( 'Form', 'booking')             // Title of TAB    
                            , 'page_title'=> __( 'Fields Settings', 'booking')      // Title of Page    
                            , 'hint'      => __( 'Customizaton of Form Fields', 'booking')               // Hint    
                            //, 'link'      => ''                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position'  => ''                                 // 'left'  ||  'right'  ||  ''
                            //, 'css_classes'=> ''                                // CSS class(es)
                            //, 'icon'      => ''                                 // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-edit'         // CSS definition  of forn Icon
                            //, 'default'   => false                               // Is this tab activated by default or not: true || false. 
                            //, 'disabled'  => false                              // Is this tab disbaled: true || false. 
                            //, 'hided'     => false                              // Is this tab hided: true || false. 
                            , 'subtabs'   => array()   
                    );
        if ( ! class_exists( 'wpdev_bk_personal' ) )																	//FixIn: 8.1.1.12
        	$tabs[ 'upgrade-link' ] = array(
                              'title' => __('Check Premium Features','booking')                // Title of TAB    
                            , 'hint'  => __('Upgrade to higher versions', 'booking')              // Hint    
                            //, 'page_title' => __('Upgrade', 'booking')        // Title of Page    
                            , 'link' => 'https://wpbookingcalendar.com/overview/'                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => 'right'                             // 'left'  ||  'right'  ||  ''
                            //, 'css_classes' => ''                             // CSS class(es)
                            //, 'icon' => ''                                    // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-play-circle'// CSS definition  of forn Icon
                            //, 'default' => false                              // Is this tab activated by default or not: true || false. 
                            //, 'subtabs' => array()            
        );
        
        return $tabs;
    }

        
    public function content() {
        
        $this->css();

        // Checking ////////////////////////////////////////////////////////////

		// Define Notices Section and show some static messages, if needed
        do_action( 'wpbc_hook_settings_page_header', 'form_field_free_settings');
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        //if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.
            
              
        // Init Settings API & Get Data from DB ////////////////////////////////
        // $this->settings_api();                                               // Define all fields and get values from DB
        
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_form_field_free';                             // Define form name
                
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        //$wpbc_user_role_master   = get_wpbc_option( 'wpbc_user_role_master' );     // O L D   W A Y:   Get Fields Data
        
        // Get Form  Fields ////////////////////////////////////////////////////        
        $booking_form_structure = $this->get_booking_form_structure_for_visual();   // Get saved or Import form  fields from  OLD Free version        
        $booking_form_structure = serialize( $booking_form_structure );
        
        
        ////////////////////////////////////////////////////////////////////////
        // Toolbar /////////////////////////////////////////////////////////////
        wpbc_bs_toolbar_sub_html_container_start();

        ?><span class="wpdevelop"><div class="visibility_container clearfix-height" style="display:block;"><?php
            
            wpbc_js_for_bookings_page();                                            // JavaScript functions
        
            $this->toolbar_select_field();                                      // Select Field Type
                   
            $this->toolbar_reset_booking_form();                                // Reset button

			$this->toolbar_select_form_structure();								// Select Form structure

			if ( function_exists( 'toolbar_use_simple_booking_form' ) ) {
				toolbar_use_simple_booking_form();
			}

            $save_button = array( 'title' => __('Save Changes', 'booking'), 'form' => 'wpbc_form_field_free' );
            $this->toolbar_save_button( $save_button );                         // Save Button 
            
        ?></div></span><?php
        
        wpbc_bs_toolbar_sub_html_container_end();
        
        ?><div class="clear" style="margin-top:20px;"></div><?php

        
        
        
        ////////////////////////////////////////////////////////////////////////
        // Fields Generator ////////////////////////////////////////////////////
        ?>
        <span class="metabox_wpbc_form_field_free_generator" style="display:none;">
            <div class="clear" style="margin-bottom:10px;"></div>
            <span class="metabox-holder">

                <div class="wpbc_settings_row " >                               
                    <?php
                    wpbc_open_meta_box_section( 'wpbc_form_field_free_generator', __('Form Field Configuration', 'booking') ); 
                    
                    $this->fields_generator_section();
                        
                    wpbc_close_meta_box_section();    
                    ?>
                </div>
            </span>
        </span>
        <?php 
        
		?><span class="wpdevelop">
			<?php $my_close_open_alert_id = 'bk_alert_timessettings_form_in_free'; ?>
			<div    class="alert alert-block alert-info <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_alert_id ) ) echo 'hide'; ?>"
					id="<?php echo $my_close_open_alert_id; ?>"
					style="line-height: 1.7em; color: #3a87ad; background: #d9edf7 ; text-shadow:0 1px 0 #f5f5f5;margin:0;">
				<a  data-original-title="Don't show the message anymore"
					class="close tooltip_left"
					style="margin-top:0px;" rel="tooltip" data-dismiss="alert"
					href="javascript:void(0)"
					onclick="javascript:wpbc_verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_alert_id; ?>');"
				>&times;</a>
				<strong class="alert-heading"><?php _e( 'Note', 'booking' ); ?>!</strong>
					<?php printf( __( 'You can add %sTime Slots%s to booking form, by activating and configure %sTime Slots%s field in booking form (below) or by adding this field from (above) toolbar.', 'booking' ),
						'<strong>', '</strong>',
						'<strong>', '</strong>'
					); ?>
			</div>
		</span><?php
        ////////////////////////////////////////////////////////////////////////
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" /><?php 
                
                ?><input type="hidden" name="reset_to_default_form" id="reset_to_default_form" value="" /><?php 
                ?><input type="hidden" name="booking_form_structure_type" id="booking_form_structure_type" value="<?php echo get_bk_option( 'booking_form_structure_type' ); ?>" /><?php

				$this->show_booking_form_fields_table( $booking_form_structure );

				?><div class="clear" style="height:10px;"></div><?php

				if (  true ){        //FixIn: 8.8.1.14

					$default_options_values = wpbc_get_default_options();

					?><table class="form-table"><?php

					$field_name = 'booking_send_button_title';
					$form_title_value = ( empty( get_bk_option( 'booking_send_button_title' ) ) ? $default_options_values['booking_send_button_title'] : get_bk_option( 'booking_send_button_title' ) );

					WPBC_Settings_API::field_text_row_static(   $field_name . '_name'
																, array(
																		'type'              => 'text'
																		, 'title'             => __( 'Title of send button' ,'booking' )
																		, 'disabled'          => false
																		, 'class'             => ''
																		, 'css'               => 'width:100%'
																		, 'placeholder'       =>  __( 'Send', 'booking' )
																		, 'description'       => sprintf(__('Enter %stitle of submit button%s in the booking form' ,'booking'),'<b>','</b>')
																		, 'group'             => 'form'
																		, 'tr_class'          => 'wpbc_send_button_title'
																		, 'only_field'        => false
																		, 'description_tag'   => 'p'
																		, 'value' 			  => $form_title_value             // 'Send'
																		, 'attr'              => array()
																)
																, true
															);
					?></table><?php
				}

                ?>
                <div class="clear" style="height:5px;"></div>
                <input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" />  
            </form>
        </span>
    <?php   
    

		// Define templates and write  JavaScript for Timeslots in ../core/admin/page-form-timeslots.php
        do_action( 'wpbc_hook_settings_page_footer', 'form_field_free_settings' );

    }

    
    //TODO: 
    //  Here need to  check user server confuguration  relative to:
    //  suhosin.post.max_array_index_length - Defines the maximum length of array indices for variables registered through a POST request
    //  suhosin.post.max_array_depth - https://suhosin.org/stories/configuration.html
    public function update() {

        if ( $_POST['reset_to_default_form'] == 'standard' ) {

        	update_bk_option( 'booking_form_structure_type',  'vertical'  );

            $visual_form_structure = $this->import_old_booking_form();              // We are importing old structure to  have default booking form.
            update_bk_option( 'booking_form_visual',  $visual_form_structure  );        
            wpbc_show_changes_saved_message();
            return;        
        }

        // Update booking form structure
        update_bk_option( 'booking_form_structure_type',  WPBC_Settings_API::validate_text_post_static( 'booking_form_structure_type' )  );

        update_bk_option( 'booking_send_button_title',  WPBC_Settings_API::validate_text_post_static( 'booking_send_button_title_name' )  );


        $skip_obligatory_field_types = array( 'calendar', 'submit', 'captcha', 'email' );

        $if_exist_required = array( 'rangetime' );																		//FixIn:  TimeFreeGenerator

        $visual_form_structure = array();

        $visual_form_structure[] = array(
                                      'type'     => 'calendar'
                                    , 'obligatory' => 'On'
                                );

        // Loop  all form  filds for saving them.
        if ( isset( $_POST['form_field_name'] ) ) {
            foreach ( $_POST['form_field_name'] as $field_key => $field_name ) {


                $visual_form_structure[] = array(
                                              'type'     => esc_attr( $_POST['form_field_type'][ $field_key ] )
                                            , 'name'     => esc_attr( $field_name )
                                            , 'obligatory' => ( ( in_array( esc_attr( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types  ) ) ? 'On' : 'Off' )
                                            , 'active'   => ( ( in_array( esc_attr( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types  ) ) ? 'On' : ( isset($_POST['form_field_active'][ $field_key ] ) ? 'On': 'Off' ) )         //FixIn: 7.0.1.22
											//FixIn:  TimeFreeGenerator
                                            , 'required' => (
                                            					( in_array( esc_attr( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types  ) )
																? 'On'
																: (
																	( in_array( esc_attr( $field_name ), $if_exist_required  ) )
																	? 'On'
																	: ( isset($_POST['form_field_required'][ $field_key ] ) ? 'On': 'Off' )
																  )
															)       //FixIn: 7.0.1.22
											, 'if_exist_required' => ( ( in_array( esc_attr( $field_name ), $if_exist_required  ) ) ? 'On': 'Off' ) 	//FixIn:  TimeFreeGenerator
                                            , 'label'    => WPBC_Settings_API::validate_text_post_static( 'form_field_label', $field_key )
                                            , 'value'    => WPBC_Settings_API::validate_text_post_static( 'form_field_value', $field_key ) 
                                        );
            }
        }

        $visual_form_structure[] = array(
                                      'type'     => 'captcha'
                                    , 'name'     => 'captcha'
                                    , 'obligatory' => 'On'
                                    , 'active'   => get_bk_option( 'booking_is_use_captcha' )
                                    , 'required' => 'On'
                                    , 'label'    => ''
                                );
    
        $visual_form_structure[] = array(
                                      'type'     => 'submit'
                                    , 'name'     => 'submit'
                                    , 'obligatory' => 'On'
                                    , 'active'   => 'On'
                                    , 'required' => 'On'
                                    , 'label'    => get_bk_option( 'booking_send_button_title' )  						//FixIn:  8.8.1.14		// __('Send', 'booking')
                                );
//debuge($visual_form_structure);
        update_bk_option( 'booking_form_visual',  $visual_form_structure  );
                        
        update_bk_option( 'booking_form',      str_replace( '\\n\\', '', $this->get_form_in__shortcodes( $visual_form_structure ) ) );
        update_bk_option( 'booking_form_show', str_replace( '\\n\\', '', $this->get_form_show_in__shortcodes() ) );
//debuge(get_bk_option( 'booking_form') );
        wpbc_show_changes_saved_message();
    }

    
    // <editor-fold     defaultstate="collapsed"                        desc=" Support "  >
    
    /** Show notice */
    private function show_pro_notice() {
    ?>  
    <span class="wpdevelop">   
        <?php $my_close_open_alert_id = 'bk_alert_settings_form_in_free'; ?>       
        <div    class="alert alert-block alert-info <?php if ( '1' == get_user_option( 'booking_win_' . $my_close_open_alert_id ) ) echo 'hide'; ?>" 
                id="<?php echo $my_close_open_alert_id; ?>"
                style="line-height: 1.7em; color: #3a87ad; background: #d9edf7 ; text-shadow:0 1px 0 #f5f5f5;margin:0;">
            <a  data-original-title="Don't show the message anymore" 
                class="close tooltip_left" 
                style="margin-top:0px;" rel="tooltip" data-dismiss="alert" 
                href="javascript:void(0)" 
                onclick="javascript:wpbc_verify_window_opening(<?php echo get_bk_current_user_id(); ?>, '<?php echo $my_close_open_alert_id; ?>');"
            >&times;</a>
            <strong class="alert-heading">Note!</strong>
                Check how in <a href="https://wpbookingcalendar.com/overview/" target="_blank" style="text-decoration:underline;">other versions of Booking Calendar</a> 
                possible fully <a href="https://wpbookingcalendar.com/faq/booking-form-fields/" target="_blank" style="text-decoration:underline;">customize the booking form</a> 
                <em>(add or remove fields, configure time-slots, change structure of booking form, etc...).</em>
        </div>    
    </span>    
    <?php
}
    
    // </editor-fold>

    
    // <editor-fold     defaultstate="collapsed"                        desc=" Import and Get Forms  "  >

    /** Get Visual Structure of booking form,  that imported from OLD Free version */
    private function import_old_booking_form() {

        $visual_form_structure = array();

        // calendar
        $visual_form_structure[] = array(
                                          'type'     => 'calendar'
                                        , 'obligatory' => 'On'
                                    );
        // 1
        $visual_form_structure[] = array(
                                          'type'     => 'text'
                                        , 'name'     => 'name'
                                        , 'obligatory' => 'Off'
                                        , 'active'   => get_bk_option( 'booking_form_field_active1')
                                        , 'required' => get_bk_option( 'booking_form_field_required1')
                                        , 'label'    => get_bk_option( 'booking_form_field_label1')            
                                    );
        // 2
        $visual_form_structure[] = array(
                                          'type'     => 'text'
                                        , 'name'     => 'secondname'
                                        , 'obligatory' => 'Off'
                                        , 'active'   => get_bk_option( 'booking_form_field_active2')
                                        , 'required' => get_bk_option( 'booking_form_field_required2')
                                        , 'label'    => get_bk_option( 'booking_form_field_label2')            
                                    );
        // 3
        $visual_form_structure[] = array(
                                          'type'     => 'email'
                                        , 'name'     => 'email'
                                        , 'obligatory' => 'On'
                                        , 'active'   => get_bk_option( 'booking_form_field_active3')
                                        , 'required' => get_bk_option( 'booking_form_field_required3')
                                        , 'label'    => get_bk_option( 'booking_form_field_label3')            
                                    );
        // 6 - select
        $visual_form_structure[] = array(
                                          'type'     => 'select'
                                        , 'name'     => 'visitors'
                                        , 'obligatory' => 'Off'
                                        , 'active'   => get_bk_option( 'booking_form_field_active6')
                                        , 'required' => get_bk_option( 'booking_form_field_required6')
                                        , 'label'    => get_bk_option( 'booking_form_field_label6')     
                                        , 'value'    => get_bk_option( 'booking_form_field_values6' )
                                    );
        // 4
        $visual_form_structure[] = array(
                                          'type'     => 'text'
                                        , 'name'     => 'phone'
                                        , 'obligatory' => 'Off'
                                        , 'active'   => get_bk_option( 'booking_form_field_active4')
                                        , 'required' => get_bk_option( 'booking_form_field_required4')
                                        , 'label'    => get_bk_option( 'booking_form_field_label4')            
                                    );
        // 5 - textarea
        $visual_form_structure[] = array(
                                          'type'     => 'textarea'
                                        , 'name'     => 'details'
                                        , 'obligatory' => 'Off'
                                        , 'active'   => get_bk_option( 'booking_form_field_active5')
                                        , 'required' => get_bk_option( 'booking_form_field_required5')
                                        , 'label'    => get_bk_option( 'booking_form_field_label5')            
                                    );
        // captcha
        $visual_form_structure[] = array(
                                          'type'     => 'captcha'
                                        , 'name'     => 'captcha'
                                        , 'obligatory' => 'On'
                                        , 'active'   => get_bk_option( 'booking_is_use_captcha' )
                                        , 'required' => 'On'
                                        , 'label'    => ''
                                    );
        // submit
        $visual_form_structure[] = array(
                                          'type'     => 'submit'
                                        , 'name'     => 'submit'
                                        , 'obligatory' => 'On'
                                        , 'active'   => 'On'
                                        , 'required' => 'On'
                                        , 'label'    => get_bk_option( 'booking_send_button_title' )    				//FixIn:  8.8.1.14		// __('Send', 'booking')
                                    );

        return $visual_form_structure;                
    }

    /** Get booking form Structure for Visual  Table */
    public function get_booking_form_structure_for_visual() {
        
        $visual_form_structure = get_bk_option( 'booking_form_visual' );        
        
        if ( $visual_form_structure == false )
            $visual_form_structure = $this->import_old_booking_form();
        
        return $visual_form_structure;
    }
    
    /** Get HTML of booking form based on Visual Structure */
    public function get_form_in__html( $my_boook_type = 1 ) {
    
        $visual_form_structure = $this->get_booking_form_structure_for_visual();        
        $visual_form_structure = maybe_unserialize( $visual_form_structure );

	    //FixIn: 8.0.1.5
	    $booking_form_structure = get_bk_option( 'booking_form_structure_type' );
        if ( empty( $booking_form_structure ) ) {
	        $booking_form_structure = 'vertical';
		}
	    $booking_form_structure = 'wpbc_' . $booking_form_structure;


	    $my_form = '<div class="wpbc_booking_form_structure '. $booking_form_structure . '">' . "\n";


	    $my_form .= '  <div class="wpbc_structure_calendar">' . "\n";
        $my_form .= '    [calendar]' . "\n";

	    $my_form .= '  </div>' . "\n";
	    $my_form .= '  <div class="wpbc_structure_form">' . "\n";


        $skip_already_exist_field_types = array( 'calendar', 'submit', 'captcha' );

        foreach ( $visual_form_structure as $key => $form_field ) {

            $defaults = array(
                                'type'     => 'text'
                              , 'name'     => 'unique_name'
                              , 'obligatory' => 'Off'
                              , 'active'   => 'On'
                              , 'required' => 'Off'
                              , 'label'    => 'Label'
                              , 'value'    => ''
            );        
            $form_field = wp_parse_args( $form_field, $defaults );
                        
            if (  
                       ( ! in_array( $form_field['type'], $skip_already_exist_field_types  ) ) 
                   &&  (  ( $form_field['active'] != 'Off' ) || ( $form_field['obligatory'] == 'On' )  )
                ){

                    // Label ///////////////////////////////////////////////////
                    
                    $form_field['label'] = apply_bk_filter('wpdev_check_for_active_language', $form_field['label'] );
                    if ( function_exists('icl_translate') )                             // WPML    
                        $form_field['label'] = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label_' . $form_field['name'] , $form_field['label'] );
                 
                    $my_form.='  <div class="form-group">';
                    
                    if ( $form_field['type'] != 'checkbox' )
                        $my_form.='  <label for="'. $form_field['name'] . $my_boook_type.'" class="control-label">'
                                    . $form_field['label']
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                  . ':</label>';
                    
                    $my_form.='   <div class="controls">';

                    
                    // Fields //////////////////////////////////////////////////
                    if ( $form_field['type'] == 'text' )
                        $my_form.='   <input type="text" name="'. $form_field['name'] . $my_boook_type.'" id="'. $form_field['name'] . $my_boook_type.'" class="input-xlarge'
                                        . ( ( $form_field['required'] == 'On' ) ? ' wpdev-validates-as-required' : '' )
                                        //. ( ( strpos( $form_field['name'], 'phone' ) !== false ) ? ' validate_as_digit' : '' )
                                      .'" />';

                    if ( $form_field['type'] == 'email' )
                        $my_form.='   <input type="text" name="'. $form_field['name'] . $my_boook_type.'" id="'. $form_field['name'] . $my_boook_type.'" class="input-xlarge wpdev-validates-as-email'
                                        . ( ( $form_field['required'] == 'On' ) ? ' wpdev-validates-as-required' : '' )
                                        . ' wpdev-validates-as-required'        //FixIn: 7.0.1.22
                                      .'" />';

                    if ( $form_field['type'] == 'select' ) {

                        $my_form.='   <select name="'. $form_field['name'] . $my_boook_type.'" id="'. $form_field['name'] . $my_boook_type.'" class="input-xlarge'
                                    . ( ( $form_field['required'] == 'On' ) ? ' wpdev-validates-as-required' : '' )
                                    . '" >';																			//FixIn: 8.1.1.4

                                $form_field['value'] = preg_split( '/\r\n|\r|\n/', $form_field['value'] );
                                
                                foreach ($form_field['value'] as $key => $select_option) {  //FixIn: 7.0.1.21

                                    
                                    $select_option = apply_bk_filter('wpdev_check_for_active_language', $select_option );
                                    if ( function_exists('icl_translate') )                             // WPML    
                                        $select_option = icl_translate( 'wpml_custom', 'wpbc_custom_form_select_value_' 
                                                                                        . wpbc_get_slug_format( $form_field['name']) . '_' .$key
                                                                                        , $select_option );
                                                                                            // //FixIn: 7.0.1.21
                                    $select_option = str_replace(array("'",'"'), '', $select_option);

                                    																					//FixIn:  TimeFreeGenerator
	                                if ( strpos( $select_option, '@@' ) !== false ) {
		                                $select_option_title = explode( '@@', $select_option );
		                                $select_option_val = esc_attr( $select_option_title[1] );
		                                $select_option_title = trim( $select_option_title[0] );
	                                } else {
		                                $select_option_val = esc_attr( $select_option );
		                                $select_option_title = trim( $select_option );

		                                if ( 'rangetime' == $form_field['name'] ) {
		                                	$select_option_title = wpbc_time_slot_in_format(  $select_option_title );
										}
	                                }
                                    $my_form.='  <option value="' . $select_option_val . '">' . $select_option_title . '</option>';

                                    // $my_form.='  <option value="' . $select_option . '">' . $select_option . '</option>';
                                }

                        $my_form.='     </select>'; 
                    }

                    if ( $form_field['type'] == 'checkbox' ) {

                        $my_form.='    <label for="'. $form_field['name'] . $my_boook_type.'" class="control-label" style="display: inline-block;">';
                        
                        $my_form.='   <input type="checkbox" name="'. $form_field['name'] . $my_boook_type.'" id="'. $form_field['name'] . $my_boook_type.'" class="wpdev-checkbox '
                                        . ( ( $form_field['required'] == 'On' ) ? ' wpdev-validates-as-required' : '' ) 
                                      .'" style="margin:0 4px 2px;" value="true" />';
                        
                        $my_form.=   '&nbsp;' . $form_field['label']
                                    . ( ( $form_field['required'] == 'On' ) ? '' : '' )
                                  . '</label>';
                  
                    }

                    if ( $form_field['type'] == 'textarea' ) {
                        $my_form.='   <textarea  rows="3" name="'. $form_field['name'] . $my_boook_type.'" id="'. $form_field['name'] . $my_boook_type.'" class="input-xlarge'
                                    . ( ( $form_field['required'] == 'On' ) ? ' wpdev-validates-as-required' : '' ) 
                                    . '" >';																			//FixIn: 8.1.1.4

                        $my_form.='</textarea>'; 
                    }
                    
                    $my_form.='</div></div>';
            }            
        }

        $my_form.='<div class="form-group">[captcha]</div>' . "\n";
        //FixIn:  8.8.1.14
        $submit_button_title = str_replace( '"','', html_entity_decode( esc_js( apply_bk_filter( 'wpdev_check_for_active_language', get_bk_option( 'booking_send_button_title' ) ) ),ENT_QUOTES) );
		$my_form.='<div class="form-group"><button class="btn btn-default" type="button" onclick="mybooking_submit(this.form,'.$my_boook_type.',\''.wpbc_get_booking_locale().'\');" >'
				  		. $submit_button_title
				  . '</button></div>' . "\n";

	    //FixIn: 8.0.1.5
	    $my_form .= '  </div>' . "\n";		// .wpbc_structure_form					|| .wpbc_structure_submit
	    $my_form .= '</div>' . "\n";		// .wpbc_booking_form_structure
	    $my_form .= '<div class="wpbc_booking_form_footer"></div>';

        return $my_form;
    }
    
    /** Get Booking form in Shortcodes - format  compatible with  premium versions */
    public function get_form_in__shortcodes( $visual_form_structure = false ) {
    
        if ( empty( $visual_form_structure ) )
            $visual_form_structure = $this->get_booking_form_structure_for_visual();
        
        $visual_form_structure = maybe_unserialize( $visual_form_structure );



	    //FixIn: 8.0.1.5
	    $booking_form_structure = get_bk_option( 'booking_form_structure_type' );
	    if ( empty( $booking_form_structure ) ) {
		    $booking_form_structure = 'vertical';
	    }
	    $booking_form_structure = 'wpbc_' . $booking_form_structure;


	    $my_form = '<div class="wpbc_booking_form_structure '. $booking_form_structure . '">' . "\n";
	    $my_form .= '  <div class="wpbc_structure_calendar">' . "\n";
	    $my_form .=  '    [calendar]' . "\n";
	    $my_form .= '  </div>' . "\n";
	    $my_form .= '  <div class="wpbc_structure_form">' . "\n";

//$my_form = '<div style="float:left;margin-right:10px;">[calendar]</div>' . "\n";										//FixIn: 8.0.1.5 	//Fix: Form2collumns
//$my_form.= '<div style="float:left;" class="standard-form">' . "\n";													//FixIn: 8.0.1.5 	//Fix: Form2collumns


	    $skip_already_exist_field_types = array( 'calendar', 'submit', 'captcha' );
      
        foreach ( $visual_form_structure as $key => $form_field ) {

            $defaults = array(
                                'type'     => 'text'
                              , 'name'     => 'unique_name'
                              , 'obligatory' => 'Off'
                              , 'active'   => 'On'
                              , 'required' => 'Off'
                              , 'label'    => 'Label'
                              , 'value'    => ''
            );        
            $form_field = wp_parse_args( $form_field, $defaults );

            if (  
                       ( ! in_array( $form_field['type'], $skip_already_exist_field_types  ) ) 
                   &&  (  ( $form_field['active'] != 'Off' ) || ( $form_field['obligatory'] == 'On' )  )
                ){


                    // Label ///////////////////////////////////////////////////
                    $form_field['label'] = apply_bk_filter('wpdev_check_for_active_language', $form_field['label'] );
                    if ( function_exists('icl_translate') )                             // WPML    
                        $form_field['label'] = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label_' . $form_field['name'] , $form_field['label'] );
                
                    if ( $form_field['type'] != 'checkbox' )  
                        $my_form .= '     <p>' . $form_field['label'] . ( ( $form_field['required'] == 'On' ) ? '*' : '' ) . ':<br />';
                    else 
                        $my_form .= '     <p>' ;

                    
                    // Field ///////////////////////////////////////////////////

                    if ( $form_field['type'] == 'text' )                        // Text
                        $my_form .= '[text'
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                    . ' '. $form_field['name']                                                  
                                    .']';

                    if ( $form_field['type'] == 'email' )                       // Email
                        $my_form .= '[email'
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                    . ' '. $form_field['name']                                                  
                                    .']';

                    if ( $form_field['type'] == 'select' ) {                    // Select
                        $my_form .= '[select'
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                    . ' '. $form_field['name'];

                            $form_field['value'] = preg_split( '/\r\n|\r|\n/', $form_field['value'] );
                            foreach ($form_field['value'] as $select_option) {

                                $select_option = str_replace(array("'",'"'), '', $select_option);

                                $my_form.='  "' . $select_option . '"';    
                            }

                        $my_form .= ']';
                    }
                    
                    if ( $form_field['type'] == 'textarea' )                    // Textarea
                        $my_form .= '[textarea'
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                    . ' '. $form_field['name']                                                  
                                    .']';
                                        
                    
                    if ( $form_field['type'] == 'checkbox' ) {                    // Checkbox
                        $my_form .= '[checkbox'
                                    . ( ( $form_field['required'] == 'On' ) ? '*' : '' )
                                    . ' '. $form_field['name']                                                  
//                                    .' ""]';
//                        $my_form .= '' . $form_field['label'];
                                ;
                        $my_form .= ' use_label_element';
                        $my_form .= ' "' . str_replace( array('"', "'"), '', $form_field['label'] ) .'"]';                                     
                    }
                    
                    $my_form.='</p>' . "\n";                    
            }                
            
        }


        $my_form.='     <p>[captcha]</p>' . "\n";                    
        //$my_form.='     <p>[submit class:btn "Send"]</p>' . "\n";

        //FixIn:  8.8.1.14
        $submit_button_title = str_replace( '"','', html_entity_decode( esc_js( apply_bk_filter( 'wpdev_check_for_active_language', get_bk_option( 'booking_send_button_title' ) ) ),ENT_QUOTES) );
		$my_form.='     <p>[submit class:btn "' . $submit_button_title .'"]</p>' . "\n";

	    //FixIn: 8.0.1.5
	    $my_form .= '  </div>' . "\n";		// .wpbc_structure_form
	    $my_form .= '</div>' . "\n";		// .wpbc_booking_form_structure
	    $my_form .= '<div class="wpbc_booking_form_footer"></div>';

        return $my_form;

    }
        
    /** Get "Content of booking fields data" form based on Visual Structure table for showing booking details in Listing page */
    public function get_form_show_in__shortcodes() {
        
        $visual_form_structure = $this->get_booking_form_structure_for_visual();        
        $visual_form_structure = maybe_unserialize( $visual_form_structure );
        

        $booking_form_show = '<div style="text-align:left;word-wrap: break-word;">'  . "\n";
        
        $skip_already_exist_field_types = array( 'calendar', 'submit', 'captcha' );

        foreach ( $visual_form_structure as $key => $form_field ) {

            $defaults = array(
                                'type'     => 'text'
                              , 'name'     => 'unique_name'
                              , 'obligatory' => 'Off'
                              , 'active'   => 'On'
                              , 'required' => 'Off'
                              , 'label'    => 'Label'
                              , 'value'    => ''
            );        
            $form_field = wp_parse_args( $form_field, $defaults );
                        
            if (  
                       ( ! in_array( $form_field['type'], $skip_already_exist_field_types  ) ) 
                   &&  (  ( $form_field['active'] != 'Off' ) || ( $form_field['obligatory'] == 'On' )  )
                ){
                    // Label language                    
                    $form_field['label'] = apply_bk_filter('wpdev_check_for_active_language', $form_field['label'] );
                    if ( function_exists('icl_translate') )                     // WPML    
                        $form_field['label'] = icl_translate( 'wpml_custom', 'wpbc_custom_form_field_label_' . $form_field['name'] , $form_field['label'] );
                 
                    
                    $booking_form_show.= '  <strong>' . $form_field['label'] . '</strong>: ' . '<span class="fieldvalue">[' . $form_field['name'] . ']</span><br/>'  . "\n";        
            }            
        }
        
        $booking_form_show.='</div>'; 
        
        return $booking_form_show;                 
    }
    
    // </editor-fold>
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" Toolbar "  >
    
    /** Show Save button  in toolbar  for saving form */
    private function toolbar_save_button( $save_button ) {
                
        ?>
        <div class="clear-for-mobile"></div><input 
                                type="button" 
                                class="button button-primary wpbc_submit_button" 
                                value="<?php echo $save_button['title']; ?>" 
                                onclick="if (typeof document.forms['<?php echo $save_button['form']; ?>'] !== 'undefined'){ 
                                            document.forms['<?php echo $save_button['form']; ?>'].submit(); 
                                         } else { 
                                             wpbc_admin_show_message( '<?php echo  ' <strong>Error!</strong> Form <strong>' , $save_button['form'] , '</strong> does not exist.'; ?>.', 'error', 10000 );   //FixIn: 7.0.1.56
                                         }" 
                                />
        <?php
    }
    
    
    /**
	 * Button for Reseting to default booking form
     * (import form  fields  from OLD  free version 
     */
    private function toolbar_reset_booking_form() {
        
        $params = array(  
                      'label_for' => 'min_cost'                             // "For" parameter  of label element
                    , 'label' => '' //__('Add New Field', 'booking')        // Label above the input group
                    , 'style' => 'margin-right:20px;'                                         // CSS Style of entire div element
                    , 'items' => array(     /*
                                            array(      
                                                'type' => 'addon' 
                                                , 'element' => 'text'           // text | radio | checkbox
                                                , 'text' => __('Reset to default form', 'booking')
                                                , 'class' => ''                 // Any CSS class here
                                                , 'style' => 'font-weight:600;' // CSS Style of entire div element
                                            )  
                                            // Warning! Can be text or selectbox, not both  OR you need to define width                     
                                            , array(                                            
                                              'type' => 'select'                              
                                            , 'id' => 'reset_to_default_form_selector'  
                                            , 'name' => 'reset_to_default_form_selector'  
                                            , 'options' => array(                       // Associated array  of titles and values   
                                                                'optgroup_sf_s' => array( 
                                                                                'optgroup' => true
                                                                                , 'close'  => false
                                                                                , 'title'  => '&nbsp;' . __('Standard Fields' ,'booking') 
                                                                            )
                                                                , 'standard' => array(  
                                                                                'title' => __('Standard', 'booking')
                                                                                , 'id' => ''   
                                                                                , 'name' => ''  
                                                                                , 'style' => ''
                                                                                , 'class' => ''     
                                                                                , 'disabled' => false
                                                                                , 'selected' => false
                                                                                , 'attr' => array()   
                                                                            )
                                                                , 'long' => array(  
                                                                                'title' => __('Long', 'booking')
                                                                                , 'id' => ''   
                                                                                , 'name' => ''  
                                                                                , 'style' => ''
                                                                                , 'class' => ''     
                                                                                , 'disabled' => false
                                                                                , 'selected' => false
                                                                                , 'attr' => array()   
                                                                            )
                                                                , 'optgroup_af_s' => array( 
                                                                                'optgroup' => true
                                                                                , 'close'  => false
                                                                                , 'title'  => '&nbsp;' . __('Advanced Fields' ,'booking') 
                                                                            )
                                                                , 'advanced' => array(  
                                                                                'title' => __('Advanced', 'booking')
                                                                                , 'id' => ''   
                                                                                , 'name' => ''  
                                                                                , 'style' => ''
                                                                                , 'class' => ''     
                                                                                , 'disabled' => false
                                                                                , 'selected' => false
                                                                                , 'attr' => array()   
                                                                            )
                                                                , 'optgroup_af_e' => array( 'optgroup' => true, 'close'  => true )

                                                            )
                                        )                                           
                                        , */
                                        array( 
                                            'type' => 'button'
                                            , 'title' => __('Reset to default form', 'booking')  // __('Reset', 'booking')
                                            , 'class' => 'button' 
                                            , 'font_icon' => 'glyphicon glyphicon-repeat'
                                            , 'icon_position' => 'right'
                                            , 'action' => "if ( wpbc_are_you_sure('" . esc_js(__('Do you really want to do this ?' ,'booking')) . "') ) {"
                                                        //. "var selected_val = jQuery('#reset_to_default_form_selector').val();"
                                                        . "var selected_val = 'standard';"
                                                        . "jQuery('#reset_to_default_form').val( selected_val );jQuery('#wpbc_form_field_free').trigger( 'submit' );"
                                                        . "}"  
                                        )                            
                            )
                    );

        ?><div class="control-group wpbc-no-padding"><?php 
                wpbc_bs_input_group( $params );                   
        ?></div><?php

    }


    /** Show selectbox for selection Field Elements in Toolbar */
    private function toolbar_select_field() {


            $params = array(
                          'label_for' => 'min_cost'                             // "For" parameter  of label element
                        , 'label' => '' //__('Add New Field', 'booking')        // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                array(
                                    'type' => 'addon'
                                    , 'element' => 'text'           // text | radio | checkbox
                                    , 'text' => __('Add New Field', 'booking') . ':'
                                    , 'class' => ''                 // Any CSS class here
                                    , 'style' => 'font-weight:600;' // CSS Style of entire div element
                                )
                                // Warning! Can be text or selectbox, not both  OR you need to define width
                                , array(
                                      'type' => 'select'
                                    , 'id' => 'select_form_help_shortcode'
                                    , 'name' => 'select_form_help_shortcode'
                                    , 'style' => ''
                                    , 'class' => ''
                                    , 'multiple' => false
                                    , 'disabled' => false
                                    , 'disabled_options' => array()             // If some options disbaled,  then its must list  here
                                    , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    , 'options' => array(                       // Associated array  of titles and values
                                                          'selector_hint' => array(
                                                                        'title' => __('Select', 'booking') . ' ' .  __('Form Field', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => 'font-weight: 400;border-bottom:1px dashed #ccc;'
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
//                                                          , 'info' => array(
//                                                                        'title' => __('General Info', 'booking')
//                                                                        , 'id' => ''
//                                                                        , 'name' => ''
//                                                                        , 'style' => ''
//                                                                        , 'class' => ''
//                                                                        , 'disabled' => false
//                                                                        , 'selected' => false
//                                                                        , 'attr' => array()
//                                                                    )
                                                        , 'optgroup_sf_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Standard Fields' ,'booking')
                                                                    )
                                                        , 'text' => array(
                                                                        'title' => __('Text', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'select' => array(
                                                                        'title' => __('Select', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'textarea' => array(
                                                                        'title' => __('Textarea', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'checkbox' => array(
                                                                        'title' => __('Checkbox', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_sf_e' => array( 'optgroup' => true, 'close'  => true )


                                                        , 'optgroup_af_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Advanced Fields' ,'booking')
                                                                    )
				            																							//FixIn: TimeFreeGenerator
                                                        , 'rangetime' => array(
                                                                        'title' => __('Time Slots', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )

                                                        , 'info_advanced' => array(
                                                                        'title' => __('Info', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_af_e' => array( 'optgroup' => true, 'close'  => true )

                                                    )
                                    , 'value' => ''                             // Some Value from optins array that selected by default
                                    , 'onfocus' => ''
                                    , 'onchange' => "wpbc_show_fields_generator( this.options[this.selectedIndex].value );"
                                )
                        )
                    );


		//FixIn:  TimeFreeGenerator
		//If the 'rangetime' already  exist  in the booking form,  so  we do NOT show it as add new field in generator,  because it can exist  only  once in booking form.
        $visual_form_structure = $this->get_booking_form_structure_for_visual();
        $visual_form_structure = maybe_unserialize( $visual_form_structure );

        // Update Field Type Selector in Toolbar
        $params = apply_filters( 'wpbc_form_gen_free_fields_selection', $params,  $visual_form_structure );

        ?>
        <?php
        ?><div class="control-group wpbc-no-padding"><?php
                wpbc_bs_input_group( $params );
        ?></div><?php
    }


    /** Show selectbox for selection Field Elements in Toolbar */
    private function toolbar_select_form_structure() {

            $params = array(
                          'label_for' => 'form_structure'                             // "For" parameter  of label element
                        , 'label' => '' //__('Add New Field', 'booking')        // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                array(
                                    'type' => 'addon'
                                    , 'element' => 'text'           // text | radio | checkbox
                                    , 'text' => __('View', 'booking') . ':'
                                    , 'class' => ''                 // Any CSS class here
                                    , 'style' => 'font-weight:600;' // CSS Style of entire div element
                                )
                                // Warning! Can be text or selectbox, not both  OR you need to define width
                                , array(
                                      'type' => 'select'
                                    , 'id' => 'form_structure'
                                    , 'name' => 'form_structure'
                                    , 'style' => ''
                                    , 'class' => ''
                                    , 'multiple' => false
                                    , 'disabled' => false
                                    , 'disabled_options' => array()             // If some options disbaled,  then its must list  here
                                    , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    , 'options' => array(                       // Associated array  of titles and values
                                                          'optgroup_sf_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Standard Forms' ,'booking')
                                                                    )
                                                        , 'vertical' => array(
                                                                        'title' => __('Form under calendar', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'form_right' => array(
                                                                        'title' => __('Form at right side of calendar', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'form_center' => array(
                                                                        'title' => __('Form and calendar are centered', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'form_dark' => array(
                                                                        'title' => __('Form for dark background', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_sf_e' => array( 'optgroup' => true, 'close'  => true )
														/*
                                                        , 'optgroup_af_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Advanced Fields' ,'booking')
                                                                    )
                                                        , 'wizard' => array(
                                                                        'title' => __('Step by step wizard', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_af_e' => array( 'optgroup' => true, 'close'  => true )
														*/
                                                    )
                                    , 'value' => get_bk_option( 'booking_form_structure_type' ) //''                    // Some Value from optins array that selected by default
                                    , 'onfocus' => ''
                                    , 'onchange' =>  "var selected_val = jQuery('#form_structure').val();"
													. "jQuery('#booking_form_structure_type').val( selected_val );"
													//. "jQuery('#wpbc_form_field_free').trigger( 'submit' );"

                                )
                        )
                    );
        ?>
        <?php
        ?><div class="control-group wpbc-no-padding"><?php
                wpbc_bs_input_group( $params );
        ?></div><?php
    }

    // </editor-fold>
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" T a b l e   of    F i e l d s"  >
    /**
	 * Show Fields Table */
    private function show_booking_form_fields_table( $booking_form_structure ) {
       
        $booking_form_structure = maybe_unserialize( $booking_form_structure );  
//debuge($booking_form_structure);     
        $skip_obligatory_field_types = array( 'calendar', 'submit', 'captcha' );
        ?><table class="widefat wpbc_input_table sortable wpdevelop wpbc_table_form_free" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="sort"><span class="glyphicon glyphicon-sort" aria-hidden="true"></span></th>
                    <th class="field_active"><?php      echo esc_js( __('Active', 'booking') ); ?></th>
                    <th class="field_label"><?php       echo esc_js( __('Field Label', 'booking') ); ?></th>
                    <th class="field_required"><?php    echo esc_js( __('Required', 'booking') ); ?></th>                    
                    <th class="field_options"><?php     echo esc_js( __('Type', 'booking') ) . ' | ' . esc_js( __('Name', 'booking') ); ?></th>
                    <th class="field_actions"><?php     echo esc_js( __('Actions', 'booking') ); ?></th>
                </tr>
            </thead>
            <tbody class="wpbc_form_fields_body">
            <?php 

            $i=0;
            
            foreach ( $booking_form_structure as $form_field ) {
                
                $defaults = array(
                                    'type'     => 'text'
                                  , 'name'     => 'unique_name'
                                  , 'obligatory' => 'Off'
                                  , 'active'   => 'On'
                                  , 'required' => 'Off'
                                  , 'label'    => 'Label'
                                  , 'value'    => ''
                );        
                $form_field = wp_parse_args( $form_field, $defaults );
                                
                if( ! in_array( $form_field['type'], $skip_obligatory_field_types  ) ) {
                    
                    $i++;
                
                    $row = '<tr class="account">';
                    
                    $row .= '<td class="sort"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></td>';
                    
                    $row .= '<td class="field_active">'
                                . ( ( $form_field['obligatory'] != 'On' ) ?
                                 '<input    type="checkbox" 
                                            name="form_field_active[' . $i . ']"
                                            value="' . esc_attr( $form_field['active'] ) . '" 
                                            ' . checked(  $form_field['active'], 'On' , false ) . '
                                            autocomplete="off"
                                />' : '' )
                            
                            .'</td>';        
                    $row .= '<td class="field_label">'
                                . '<legend class="screen-reader-text"><span>' . esc_attr( $form_field['label'] ) . '</span></legend>                    
                                   <input  type="text" 
                                        name="form_field_label[' . $i . ']"
                                        value="' . esc_attr( $form_field['label'] ) . '" 
                                        class="regular-text"                                 
                                        placeholder="' . esc_attr( $form_field['label'] ) . '" 
                                        autocomplete="off"
                                    /> '                            
                            .'</td>';

                    																									//FixIn:  TimeFreeGenerator
                    $is_show_required_checkbox = true;
                    if ( $form_field['obligatory'] == 'On' ) {
                    	$is_show_required_checkbox = false;
					}
                    if (  isset( $form_field['if_exist_required'] ) &&  ( $form_field['if_exist_required'] == 'On' )  ) {
                    	$is_show_required_checkbox = false;
					}

                    $row .= '<td class="field_required">'
                                . ( $is_show_required_checkbox
									? '<input    type="checkbox" 
                                            name="form_field_required[' . $i . ']"
                                            value="' . esc_attr( $form_field['required'] ) . '" 
                                            ' . checked(  $form_field['required'], 'On'  , false ) . '
                                            autocomplete="off" />'
							        : '' )
                            .'</td>';                                                                
                    $row .= '<td class="field_options"> '
                                . '<input type="text" disabled="DISABLED" value="'. '' . $form_field['type']. ' | ' . $form_field['name'] . '"  autocomplete="off" />'
                                . '<input type="hidden"  value="'. esc_attr( $form_field['type'] ) . '"  name="form_field_type[' . $i . ']" autocomplete="off" />'
                                . '<input type="hidden"  value="'. esc_attr( $form_field['name'] ) . '"  name="form_field_name[' . $i . ']" autocomplete="off" />'
                                . '<input type="hidden"  value="'. esc_attr( $form_field['value'] ) . '"  name="form_field_value[' . $i . ']" autocomplete="off" />'
                            .' </td>';        
                    $row .= '<td class="field_actions">'; 
                    if ( $form_field['obligatory'] != 'On' ) {
                    $row .= '<a href="javascript:void(0)" onclick="javascript:wpbc_start_edit_form_field(' . $i . ');" class="tooltip_top button-secondary button" title="'.__('Edit' ,'booking').'"><i class="glyphicon glyphicon-edit"></i></a>';        
                    $row .= '<a href="javascript:void(0)" class="tooltip_top button-secondary button delete_bk_link" title="'.__('Remove' ,'booking').'"><i class="glyphicon glyphicon-remove"></i></a>';        
                    }
                    $row .= '</td>';   
                    
                    $row .= '</tr>'; 
                            
                    echo $row;        
                }
            }            

            ?>
            </tbody>
            <?php /* ?>
            <tfoot>
                <tr>
                    <th colspan="6">
                        <a href="#" class="remove_rows button"><?php _e( 'Remove selected field' ,'booking'); ?></a>
                    </th>
                </tr>
            </tfoot>
            <?php  /**/ ?>
        </table><?php  
        
        $this->js();
    } 
    
    // </editor-fold>

    
    ////////////////////////////////////////////////////////////////////////////
    // CSS & JS 
    ////////////////////////////////////////////////////////////////////////////
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css"> 
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
            /* Generator elements */
            .wpbc_field_generator {
                display:none;
            }
            .wpbc_field_generator_info {
                display:block;
            }
            /* Table with Fields elements */
            .wpbc_table_form_free tr th{
                font-size: 12px; 
                text-align: center; 
                font-weight: 600; 
                width: 60px;
                line-height: 3em;
            }
            .wpbc_table_form_free tr th.sort{
                color: #777;
                font-size: 11px;
                text-align: center;
                width: 43px;                            
            }
            .wpbc_table_form_free tr th.field_active,
            .wpbc_table_form_free tr th.field_required{
                width:50px;
            }
            .wpbc_table_form_free tr th.field_label{
                width:55%;
            }
            .wpbc_table_form_free tr th.field_options {
                
            }
            .wpbc_table_form_free tr th {
                width:auto;
            }
            .wpbc_table_form_free tr th.field_actions {
                width:100px;
            }                        
            .wpbc_table_form_free tr td{
                padding: 5px 0;
                text-align: center;
                border-bottom: 1px solid #eee;
            }
            .wpbc_table_form_free tr td.sort{
                color: #888;
                font-size: 13px;
                vertical-align: middle;                           
            }
            .wpbc_table_form_free tr td.field_label input {
                width:90%
            }
            .wpbc_table_form_free tr td.field_options input {
                width: 90%; 
                color: #aaa;
            }
            .wpbc_table_form_free tr td.field_options > input:disabled {
                background: #f8f8f8 none repeat scroll 0 0;
                color: #aaaaaa;
            }
            .wpbc_table_form_free tfoot tr th {
                font-weight: 400;
                line-height: 2em;
                padding: 10px 12px 12px;
                text-align: right;    
            }
            .wpbc_table_form_free tfoot tr th a.button{
                vertical-align: middle;   
            }
            .wpbc_table_form_free  a.button {
                 margin: 0 5px 5px 0;
            }  
            .wpbc_add_field_row,
            .wpbc_edit_field_row {
                display:none;
            }
/*            @media (max-width: 599px) {*/
            @media (max-width: 782px) {
                .wpbc_table_form_free tr th.field_options,  
                .wpbc_table_form_free tr td.field_options {
                    display:none;
                }
            }
        </style>
        <?php
		wpbc_timeslots_free_css();																						//FixIn: TimeFreeGenerator
    }


//TODO: Refacttor this function.
//TODO: Transfer some  JavaScript realtive timeslots to the booking/core/admin/page-form-timeslots.php and finish it.
//TODO: 2018-05-27
    /** JS for Sorting, removing form fields */
    private function js() {
        ?>
        <script type="text/javascript">

			/**
			 *  Add 'last_selected', 'current' CSS classes  on FOCUS to table rows
			 */
            ( function( $ ){
                var controlled = false;
                var shifted = false;
                var hasFocus = false;

                $(document).on('keyup keydown', function(e){ shifted = e.shiftKey; controlled = e.ctrlKey || e.metaKey } );

                $('.wpbc_input_table').on( 'focus click', 'input', function( e ) {

                        $this_table = $(this).closest('table');
                        $this_row   = $(this).closest('tr');

                        if ( ( e.type == 'focus' && hasFocus != $this_row.index() ) || ( e.type == 'click' && $(this).is(':focus') ) ) {

                                hasFocus = $this_row.index();

                                if ( ! shifted && ! controlled ) {
                                        $('tr', $this_table).removeClass('current').removeClass('last_selected');
                                        $this_row.addClass('current').addClass('last_selected');
                                } else if ( shifted ) {
                                        $('tr', $this_table).removeClass('current');
                                        $this_row.addClass('selected_now').addClass('current');

                                        if ( $('tr.last_selected', $this_table).size() > 0 ) {
                                                if ( $this_row.index() > $('tr.last_selected, $this_table').index() ) {
                                                        $('tr', $this_table).slice( $('tr.last_selected', $this_table).index(), $this_row.index() ).addClass('current');
                                                } else {
                                                        $('tr', $this_table).slice( $this_row.index(), $('tr.last_selected', $this_table).index() + 1 ).addClass('current');
                                                }
                                        }

                                        $('tr', $this_table).removeClass('last_selected');
                                        $this_row.addClass('last_selected');
                                } else {
                                        $('tr', $this_table).removeClass('last_selected');
                                        if ( controlled && $(this).closest('tr').is('.current') ) {
                                                $this_row.removeClass('current');
                                        } else {
                                                $this_row.addClass('current').addClass('last_selected');
                                        }
                                }

                                $('tr', $this_table).removeClass('selected_now');

                        }
                }).on( 'blur', 'input', function( e ) {
                        hasFocus = false;
                });

            }( jQuery ) );


			// Make Table sortable
			function wpbc_make_table_sortable(){

				jQuery('.wpbc_input_table tbody th').css('cursor','move');

				jQuery('.wpbc_input_table tbody td.sort').css('cursor','move');

				jQuery('.wpbc_input_table.sortable tbody').sortable({
						items:'tr',
						cursor:'move',
						axis:'y',
						scrollSensitivity:40,
						forcePlaceholderSize: true,
						helper: 'clone',
						opacity: 0.65,
						placeholder: '.wpbc_input_table .sort',
						start:function(event,ui){
								ui.item.css('background-color','#f6f6f6');
						},
						stop:function(event,ui){
								ui.item.removeAttr('style');
						}
				});
			}


			// Activate row delete
			function wpbc_activate_table_row_delete( del_btn_css_class, is_confirm ){

				// Delete Row
				jQuery( del_btn_css_class ).on( 'click', function(){                   //FixIn: 8.7.11.12

					if ( true === is_confirm ){
						if ( ! wpbc_are_you_sure( '<?php echo esc_js( __( 'Do you really want to do this ?', 'booking' ) ); ?>' ) ){
							return false;
						}
					}

					var $current = jQuery(this).closest('tr');
					if ( $current.size() > 0 ) {
						$current.each(function(){
								jQuery(this).remove();
						});
						return true;
					}

					return false;
				});

			}


		//////////////////////////////////////////////////////////
		// Fields Generator Section
		//////////////////////////////////////////////////////////


            /**
	 		 * Check  Name  in  "field form" about possible usage of this name and about  any Duplicates in Filds Table
             * @param {string} field_name
             */
            function wpbc_check_typed_name( field_name ){

                // Set Name only Letters
                if (    ( jQuery('#' + field_name + '_name').val() != '' )
                     && ( ! jQuery('#' + field_name + '_name').is(':disabled') )
                    ){
                    var p_name = jQuery('#' + field_name + '_name').val();
                    p_name = p_name.replace(/[^A-Za-z0-9_-]*[0-9]*$/g,'').replace(/[^A-Za-z0-9_-]/g,'');
                    p_name = p_name.toLowerCase();


                    jQuery('input[name^=form_field_name]').each(function(){
                        var text_value = jQuery(this).val();
                        if( text_value == p_name ) {                            // error element with this name exist

                            p_name +=  '_' + Math.round( new Date().getTime()  ) + '_rand';         //Add random sufix
                        }
                    });

                    jQuery('#' + field_name + '_name').val( p_name );
                }
            }


            /** Reset to default values all Form  fields for creation new fields */
            function wpbc_reset_all_forms(){

                jQuery('.wpbc_table_form_free tr').removeClass('highlight');
                jQuery('.wpbc_add_field_row').hide();
                jQuery('.wpbc_edit_field_row').hide();

                var field_type_array = [ 'text', 'textarea', 'select', 'checkbox' , 'rangetime'];						//FixIn: TimeFreeGenerator
                var field_type;

                for (i = 0; i < field_type_array.length; i++) {
                    field_type = field_type_array[i];

                    if ( ! jQuery('#' + field_type + '_field_generator_name').is(':disabled') ){						//FixIn: TimeFreeGenerator
						jQuery( '#' + field_type + '_field_generator_active' ).prop( 'checked', true );
						jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked', false );
						jQuery( '#' + field_type + '_field_generator_label' ).val( '' );

						jQuery( '#' + field_type + '_field_generator_name' ).prop( 'disabled', false );
						jQuery( '#' + field_type + '_field_generator_name' ).val( '' );
						jQuery( '#' + field_type + '_field_generator_value' ).val( '' );
					}
                }
            }


            /**
	 		 * Show selected Add New Field form, and reset fields in this form
             *  
             * @param string selected_field_value
             */
            function wpbc_show_fields_generator( selected_field_value ) {
            	wpbc_reset_all_forms();
                if (selected_field_value == 'selector_hint') { 
                    jQuery('.metabox_wpbc_form_field_free_generator').hide();
                    jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show();						//FixIn: 8.7.11.7
                } else {
                    jQuery('.metabox_wpbc_form_field_free_generator').show();
                    jQuery('.wpbc_field_generator').hide();
                    jQuery('.wpbc_field_generator_' + selected_field_value ).show();
                    jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html( jQuery('#select_form_help_shortcode option:selected').text() );                    
                    jQuery('.wpbc_add_field_row').show();
                    jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').hide();						//FixIn: 8.7.11.7
                }            
            }


            /** Hide all Add New Field forms, and reset fields in these forms*/
            function wpbc_hide_fields_generators() {
                wpbc_reset_all_forms();
                jQuery('.metabox_wpbc_form_field_free_generator').hide();
                jQuery('#select_form_help_shortcode>option:eq(0)').attr('selected', true);

                jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').show();						//FixIn: 8.7.11.7
            }


            /**
	 		 * Add New Row with new Field to Table and Submit Saving changes.
             *
             * @param {string} field_name
             * @param {string} field_type
             */
            function wpbc_add_field ( field_name, field_type ) {
            
//FixIn: TimeFreeGenerator
if ( 'rangetime_field_generator' == field_name ) {
	var replaced_result = wpbc_get_saved_value_from_timeslots_table();
	if ( false === replaced_result ){
		wpbc_hide_fields_generators();
		//TOO: Show warning at  the top of page,  about error during saving timeslots
		console.log( 'error during parsing timeslots tbale and savig it.' )
		return;
	}
}

                if ( jQuery('#' + field_name + '_name').val() != '' ) { 
                    
                    wpbc_check_typed_name( field_name );
                    /*
                    console.log(
                        jQuery('#' + field_name + '_active').is( ":checked" ),  
                        jQuery('#' + field_name + '_required').is( ":checked" ),  
                        jQuery('#' + field_name + '_name').val(),
                        jQuery('#' + field_name + '_label').val(),
                        jQuery('#' + field_name + '_value').val()
                    );
                    */
                    
                    var row_num = jQuery('.wpbc_table_form_free tbody tr').length + Math.round( new Date().getTime()  ) ;                    
                    
                    var row_active = 'Off';
                    var row_active_checked = '';
                    if ( jQuery('#' + field_name + '_active').is( ":checked" ) ) {
                        row_active = 'On';
                        row_active_checked = ' checked="checked" ';
                    }
                    
                    var row_required = 'Off';
                    var row_required_checked = '';
                    if ( jQuery('#' + field_name + '_required').is( ":checked" ) ) {
                        row_required = 'On';
                        row_required_checked = ' checked="checked" ';
                    }
                    
                    
                    var row;
                    row = '<tr class="account ui-sortable-handle">';
                    
                    ////////////////////////////////////////////////////////////
                    row += '<td class="sort" style="cursor: move;"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></td>';
                    
                    row += '<td class="field_active">';                                
                    row +=      '<input type="checkbox" name="form_field_active['+ row_num +']" value="' + row_active + '" ' + row_active_checked + ' autocomplete="off" />';
                    row += '</td>';        
                    
                    ////////////////////////////////////////////////////////////
                    row += '<td class="field_label">';
                    
                    row +=      '<legend class="screen-reader-text"><span>' + jQuery('#' + field_name + '_label').val() + '</span></legend>';
                    
                    row +=      '<input type="text" name="form_field_label['+ row_num +']" value="' 
                                        + jQuery('#' + field_name + '_label').val() + '" placeholder="'  
                                        + jQuery('#' + field_name + '_label').val() + '" class="regular-text" autocomplete="off" />';                                                                                   
                    row += '</td>';        
                    
                    ////////////////////////////////////////////////////////////
                    row += '<td class="field_required">';

//FixIn:  TimeFreeGenerator
if ( 'rangetime' == field_name ) {
	row +=      '<input type="checkbox" disabled="DISABLED" name="form_field_required['+ row_num +']" value="' + 'On' + '" ' + ' checked="checked" ' + ' autocomplete="off" />';
} else
	row +=      '<input type="checkbox" name="form_field_required['+ row_num +']" value="' + row_required + '" ' + row_required_checked + ' autocomplete="off" />';
                    
                    row += '</td>'; 
                    
                    ////////////////////////////////////////////////////////////
                    row += '<td class="field_options">';                    
                    row +=        '<input type="text" disabled="DISABLED" value="' + field_type + ' | ' + jQuery('#' + field_name + '_name').val() + '"  autocomplete="off" />';
                    row +=        '<input type="hidden" value="' + field_type +  '"  name="form_field_type[' + row_num + ']" autocomplete="off" />';
                    row +=        '<input type="hidden" value="' + jQuery('#' + field_name + '_name').val() + '"  name="form_field_name[' + row_num + ']" autocomplete="off" />';
                    row +=        '<input type="hidden" value="' + jQuery('#' + field_name + '_value').val() + '"  name="form_field_value[' + row_num + ']" autocomplete="off" />';
                    row += '</td>';   
                    
                    ////////////////////////////////////////////////////////////
                    row += '<td class="field_options">';
                    
                    //row +=      '<a href="javascript:void(0)" class="tooltip_top button-secondary button" title="<?php echo esc_js( __('Edit' ,'booking') ) ; ?>"><i class="glyphicon glyphicon-edit"></i></a>';
                    //row +=      '<a href="javascript:void(0)" class="tooltip_top button-secondary button delete_bk_link" title="<?php echo esc_js( __('Remove' ,'booking') ) ; ?>"><i class="glyphicon glyphicon-remove"></i></a>';
                    
                    row += '</td>';   
                    ////////////////////////////////////////////////////////////
                    row += '</tr>'; 
                    
                    jQuery('.wpbc_table_form_free tbody').append( row );
                    
                    wpbc_hide_fields_generators();
                    
                    document.forms['wpbc_form_field_free'].submit();            //Submit form
                    
                } else {                    
                    wpbc_field_highlight( '#' + field_name + '_name' );
                }
            }
             

			/**
			 * Prepare Edit section for editing specific field.
			 * @param row_number
			 */
			function wpbc_start_edit_form_field( row_number ) {

                wpbc_reset_all_forms();																					// Reset Fields in all generator rows (text,select,...) to init (empty) values
                jQuery('.wpbc_edit_field_row').show();																	// Show row with edit btn
                
                jQuery('.wpbc_table_form_free tr').removeClass('highlight');
                jQuery('input[name="form_field_name['+row_number+']"]').closest('tr').addClass('highlight');			//Highlight row

				// Get exist data from EXIST fields Table
                var field_active = jQuery('input[name="form_field_active['+row_number+']"]').is( ":checked" );
                var field_required = jQuery('input[name="form_field_required['+row_number+']"]').is( ":checked" );
                var field_label = jQuery('input[name="form_field_label['+row_number+']"]').val();
                var field_value = jQuery('input[name="form_field_value['+row_number+']"]').val();
                var field_name = jQuery('input[name="form_field_name['+row_number+']"]').val();
                var field_type = jQuery('input[name="form_field_type['+row_number+']"]').val();
//console.log( 'field_active, field_required, field_label, field_value, field_name, field_type', field_active, field_required, field_label, field_value, field_name, field_type );

				jQuery('.metabox_wpbc_form_field_free_generator').show();												// Show Generator section
                jQuery('.wpbc_field_generator').hide();																	// Hide inside of generator sub section  relative to fields types



//FixIn: TimeFreeGenerator	- Exception - field with  name 'rangetime, have type 'rangetype' in Generator BUT, it have to  be saved as 'select' type'
if ( 'rangetime' == field_name ) {
/**
 *  Field 'rangetime_field_generator' have DIV section, which have CSS class 'wpbc_field_generator_rangetime',
 *  but its also  defined with  type 'select'  for adding this field via    javascript:wpbc_add_field ( 'rangetime_field_generator', 'select' );
 */

	field_type = 'rangetime';

/**
 * During editing 'field_required' == false,  because this field does not exist  in the Table with exist fields,  but we need to  set it to  true and disabled.
 */

}

                jQuery('.wpbc_field_generator_' + field_type ).show();													// Show specific generator sub section  relative to selected Field Type
                jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html( '<?php echo __('Edit', 'booking') . ': '  ?>' + field_name );
                //jQuery('#wpbc_form_field_free_generator_metabox h3.hndle span').html( this.options[this.selectedIndex].text )

                jQuery( '#' + field_type + '_field_generator_active' ).prop( 'checked', field_active );
                jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked', field_required );
                jQuery( '#' + field_type + '_field_generator_label' ).val( field_label );
                jQuery( '#' + field_type + '_field_generator_name' ).val( field_name );
                jQuery( '#' + field_type + '_field_generator_value' ).val( field_value );
                jQuery( '#' + field_type + '_field_generator_name' ).prop('disabled' , true);

//FixIn: TimeFreeGenerator
if ( 'rangetime' == field_name ) {
	jQuery( '#' + field_type + '_field_generator_required' ).prop( 'checked',  true ).prop( 'disabled', true );			// Set Disabled and Checked -- Required field
	wpbc_check_typed_values( field_name + '_field_generator' );															// Update Options and Titles for TimeSlots
	wpbc_timeslots_table__fill_rows();
}

				jQuery( '#wpbc_form_field_free input.wpbc_submit_button[type="submit"],input.wpbc_submit_button[type="button"]').hide();						//FixIn: 8.7.11.7

                wpbc_scroll_to('#wpbc_form_field_free_generator_metabox' );
            }


			/**
			 * Prepare fields data, and submit Edited field by clicking "Save changes" btn.
			 *
			 * @param field_name
			 * @param field_type
			 */
			function wpbc_finish_edit_form_field( field_name, field_type ) {


//FixIn: TimeFreeGenerator
if ( 'rangetime_field_generator' == field_name ) {
	var replaced_result = wpbc_get_saved_value_from_timeslots_table();
	if ( false === replaced_result ){
		wpbc_hide_fields_generators();
		//TOO: Show warning at  the top of page,  about error during saving timeslots
		console.log( 'error during parsing timeslots tbale and savig it.' )
		return;
	}
}


                // Get Values in  Edit Form ////////////////////////////////////
                
                //0: var field_type
                //1:
                var row_active = 'Off';
                var row_active_checked = false;
                if ( jQuery('#' + field_name + '_active').is( ":checked" ) ) {
                    row_active = 'On';
                    row_active_checked = true;
                }
                //2:    
                var row_required = 'Off';
                var row_required_checked = false;
                if ( jQuery('#' + field_name + '_required').is( ":checked" ) ) {
                    row_required = 'On';
                    row_required_checked = true;
                }
                //3:
                var row_label = jQuery('#' + field_name + '_label').val();                
                //4:
                var row_name = jQuery('#' + field_name + '_name').val();
                //5:
                var row_value = jQuery('#' + field_name + '_value').val();

                // Set  values to  the ROW in Fields Table /////////////////////
                //1:
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').prop( 'checked', row_active_checked );
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_active]').val( row_active );
                //2:
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').prop( 'checked', row_required_checked );
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_required]').val( row_required );
                //3:
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_label]').val( row_label );
//                //4:
//                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_name]').val( row_name );
//                //0:
//                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_type]').val( field_type );
                //5:
                jQuery('.wpbc_table_form_free tr.highlight input[name^=form_field_value]').val( row_value );                
//                // Options field:
//                jQuery('.wpbc_table_form_free tr.highlight td.field_options input:disabled').val( field_type + '|' +  row_name );
                
                
                //Hide generators and Reset forms  and Disable highlighting ////
                wpbc_hide_fields_generators();
                
                //Send submit //////////////////////////////////////////////////
                document.forms['wpbc_form_field_free'].submit();                // Submit form


                
            }


            /**
	 		 * Check  Value and parse it to Options and Titles
             * @param {string} field_name
             */
            function wpbc_check_typed_values( field_name ){

            	var t_options_titles_arr = wpbc_get_titles_options_from_values( '#' + field_name + '_value' );

            	if ( false !== t_options_titles_arr ) {

					var t_options = t_options_titles_arr[0].join( "\n" );
                    var t_titles  = t_options_titles_arr[1].join( "\n" );
					jQuery('#' + field_name + '_options_options').val( t_options );
					jQuery('#' + field_name + '_options_titles').val( t_titles );

				}
            }


			/**
			 * Get array  with  Options and Titles from  Values,  if in values was defined constrution  like this 			' Option @@ Title '
			 * @param field_id string
			 * @returns array | false
			 */
			function wpbc_get_titles_options_from_values( field_id ){
                if (    ( jQuery( field_id ).val() != '' )
                     && ( ! jQuery( field_id ).is(':disabled') )
                    ){

                    var tslots = jQuery( field_id ).val();
                    tslots = tslots.split('\n');
                    var t_options = [];
                    var t_titles  = [];
                    var slot_t = '';

                    if ( ( typeof tslots !== 'undefined' ) && ( tslots.length > 0 ) ){

                    	for ( var i=0; i < tslots.length; i++ ) {

                    		slot_t = tslots[ i ].split( '@@' );

							if ( slot_t.length > 1 ){
								t_options.push( slot_t[ 1 ].trim() );
								t_titles.push(  slot_t[ 0 ].trim() );
							} else {
								t_options.push( slot_t[ 0 ].trim() );
								t_titles.push(  '' );
							}
						}

					}
					var t_options_titles_arr = [];
                    t_options_titles_arr.push( t_options );
                    t_options_titles_arr.push( t_titles );

					return t_options_titles_arr;
                }
                return false;
			}

        </script>
        <?php
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Generators
    ////////////////////////////////////////////////////////////////////////////
    
    /** Sections with Add New Fields forms */
    private function fields_generator_section() {
        ?>
        <div class="wpbc_field_generator wpbc_field_generator_info">
        <?php 
            
            echo
                '<p><strong>' . __('Shortcodes' ,'booking') . '.</strong> ' 
                           . sprintf(__('You can generate the form fields for your form (at the left side) by selection specific field in the above selectbox.' ,'booking'),'<code><strong>[email* email]</strong></code>')
                .'<br/>'   . sprintf(__('Please read more about the booking form fields configuration %shere%s.' ,'booking'),'<a href="https://wpbookingcalendar.com/faq/booking-form-fields/" target="_blank">', '</a>' ) 

                . '</p><p><strong>' . __('Default Form Templates' ,'booking') . '.</strong> ' . 
                             sprintf(__('You can reset your active form template by selecting default %sform template%s at the top toolbar. Please select the form template and click on %sReset%s button for resetting only active form (Booking Form or Content of Booking Fields form). Click  on %sBoth%s button if you want to reset both forms: Booking Form and Content of Booking Fields form.' ,'booking')
                                        ,'<strong>','</strong>'
                                        ,'<strong>','</strong>'
                                        ,'<strong>','</strong>'
                                     )
                .'</p>';

            $this->show_pro_notice();             
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_text">
        <?php 
        
            $this->generate_field(  
                                    'text_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'text' 
                                    )  
                                );            
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_textarea">
        <?php  
        
            $this->generate_field(  
                                    'textarea_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'textarea' 
                                    )  
                                );        
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_select">
        <?php 
            $this->generate_field(  
                                    'select_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => true 
                                        , 'type' => 'select' 
                                    )  
                                );        
        ?>    
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_checkbox">
        <?php 
        
            $this->generate_field(  
                                    'checkbox_field_generator'
                                    , array( 
                                        'active' => true
                                        , 'required' => true
                                        , 'label' => true
                                        , 'name' => true
                                        , 'value' => false 
                                        , 'type' => 'checkbox' 
                                    )  
                                );        
        ?>
        </div>
		<?php
																														//FixIn: TimeFreeGenerator
		?>
        <div class="wpbc_field_generator wpbc_field_generator_rangetime">
        <?php

            $this->generate_field(
                                    'rangetime_field_generator'
                                    , array(
                                          'active' 	 => true
                                        , 'required' => true
                                        , 'label' 	 => true
                                        , 'name' 	 => true
                                        , 'value' 	 => true
                                        , 'type' 	 => 'select'

										, 'required_attr' 	=> array( 'disabled' => true
																	, 'value' => 'On'
																)
										, 'label_attr' 		=> array( 'placeholder' => __( 'Time Slots', 'booking' )
																	, 'value' 		=> __( 'Time Slots', 'booking' )
																)
										, 'name_attr' 		=> array( 'disabled' 	=> true
																	, 'placeholder' => 'rangetime'
																	, 'value' 		=> 'rangetime'
																)
										, 'value_attr' 		=> array( 'value' => "10:00 AM - 12:00 PM@@10:00 - 12:00\n12:00 PM - 02:00 PM@@12:00 - 14:00\n13:00 - 14:00\n11:00 - 15:00\n14:00 - 16:00\n16:00 - 18:00\n18:00 - 20:00"
																	, 'attr' => array(
																						'placeholder' => "10:00 AM - 12:00 PM@@10:00 - 12:00\n12:00 PM - 02:00 PM@@12:00 - 14:00\n13:00 - 14:00\n11:00 - 15:00\n14:00 - 16:00\n16:00 - 18:00\n18:00 - 20:00"
																					)
																	, 'rows' => 5
																	, 'cols' => 37
																)
                                    )
                                );
        ?>
        </div>
        <div class="wpbc_field_generator wpbc_field_generator_info_advanced">
            <?php  $this->show_pro_notice(); ?>
        </div>        
        <?php
    }
    
    
    /** General Fields Generator */
    private function generate_field( $field_name = 'some_field_name', $field_options = array()  ) {

        $defaults = array(
                    'active'   => true
                  , 'required' => true
                  , 'label'    => true
                  , 'name'     => true
                  , 'value'    => true
																														//FixIn: TimeFreeGenerator 	(inside of form fields edited,  as well)
				  , 'required_attr' => array( 	  'disabled' => false
												, 'value' => 'Off'
										)
				  , 'label_attr' 	=> array( 	  'placeholder' => __('First Name', 'booking')
												, 'value' => ''
										)
				  , 'name_attr' 	=> array( 	  'disabled' => false
												, 'placeholder' => 'first_name'
												, 'value' => ''
										)
				  , 'value_attr' 	=> array( 	  'value' => ''
												, 'attr' => array( 'placeholder' => "1\n2\n3\n4" )
												, 'rows' => 2
												, 'cols' => 37
										)
				  );
        $field_options = wp_parse_args( $field_options, $defaults );
        
        ?><table class="form-table"><?php 
            
        if ( $field_options['active'] )
            WPBC_Settings_API::field_checkbox_row_static(   $field_name . '_active'
                                                        , array(
                                                                'type'              => 'checkbox'
                                                                , 'title'             => __('Active', 'booking')
                                                                , 'label'             => __('Show / hide field in booking form', 'booking')
                                                                , 'disabled'          => false
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'type'              => 'checkbox'
                                                                , 'description'       => ''
                                                                , 'attr'              => array()
                                                                , 'group'             => 'general'
                                                                , 'tr_class'          => ''
                                                                , 'only_field'        => false
                                                                , 'is_new_line'       => true
                                                                , 'description_tag'   => 'span'
                                                                , 'value' => 'On'
                                                        )
                                                        , true
                                                    );
        if ( $field_options['required'] )    
            WPBC_Settings_API::field_checkbox_row_static(   $field_name . '_required'
                                                        , array(
                                                                'type'              => 'checkbox'
                                                                , 'title'             => __('Required', 'booking')
                                                                , 'label'             => __('Set field as required', 'booking')
                                                                , 'disabled'          => $field_options[ 'required_attr' ][ 'disabled' ]				//false
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'type'              => 'checkbox'
                                                                , 'description'       => ''
                                                                , 'attr'              => array()
                                                                , 'group'             => 'general'
                                                                , 'tr_class'          => ''
                                                                , 'only_field'        => false
                                                                , 'is_new_line'       => true
                                                                , 'description_tag'   => 'span'
                                                                , 'value' 			  => $field_options[ 'required_attr' ][ 'value' ]				//'Off'
                                                        )
                                                        , true
                                                    );
        if ( $field_options['label'] )    
            WPBC_Settings_API::field_text_row_static(   $field_name . '_label'
                                                        , array(
                                                                'type'                => 'text'
                                                                , 'title'             => __('Label', 'booking')
                                                                , 'disabled'          => false
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'placeholder'       => $field_options[ 'label_attr' ][ 'placeholder' ]				//'First Name'
                                                                , 'description'       => ''//__('Enter field label', 'booking')                                                                
                                                                , 'group'             => 'general'
                                                                , 'tr_class'          => ''
                                                                , 'only_field'        => false
                                                                , 'description_tag'   => 'p'
                                                                , 'value' 			  => $field_options[ 'label_attr' ][ 'value' ]				//''
                                                                , 'attr'              => array(
                                                                      'oninput'   => "javascript:this.onchange();" 
                                                                    , 'onpaste'   => "javascript:this.onchange();" 
                                                                    , 'onkeypress'=> "javascript:this.onchange();" 
                                                                    , 'onchange'  => "javascript:if ( ! jQuery('#".$field_name . '_name'."').is(':disabled') ) { jQuery('#".$field_name . '_name'."').val(jQuery(this).val() );} wpbc_check_typed_name('".$field_name."');" 
                                                                )
                                                        )
                                                        , true
                                                    );
		if ( $field_options['name'] )
			WPBC_Settings_API::field_text_row_static(   $field_name . '_name'
                                                        , array(
                                                                'type'              => 'text'
                                                                , 'title'             => __('Name', 'booking') . '  *'
                                                                , 'disabled'          => $field_options[ 'name_attr' ][ 'disabled' ]				//false
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'placeholder'       => $field_options[ 'name_attr' ][ 'placeholder' ]				//'first_name'
                                                                , 'description'       => sprintf( __('Type only %sunique field name%s, that is not using in form', 'booking'), '<strong>', '</strong>' )
                                                                , 'group'             => 'general'
                                                                , 'tr_class'          => ''
                                                                , 'only_field'        => false
                                                                , 'description_tag'   => 'p'
                                                                , 'value' 			  => $field_options[ 'name_attr' ][ 'value' ]					//''
                                                                , 'attr'              => array(
                                                                      'oninput'   => "javascript:this.onchange();" 
                                                                    , 'onpaste'   => "javascript:this.onchange();" 
                                                                    , 'onkeypress'=> "javascript:this.onchange();" 
                                                                    , 'onchange'  => "javascript:wpbc_check_typed_name('".$field_name."');" 

                                                                )
                                                            
                                                        )
                                                        , true
                                                    );
		if ( $field_options['value'] )
			WPBC_Settings_API::field_textarea_row_static(   $field_name . '_value'
                                                        , array(
                                                                
                                                                 'title'             => __('Values', 'booking')
                                                                , 'disabled'          => false
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'placeholder'       => ''
                                                                , 'description'       => sprintf( __('Enter dropdown options. One option per line.', 'booking'), '<strong>', '</strong>' )
                                                                , 'group'             => 'general'
                                                                , 'tr_class'          => ''
                                                                , 'only_field'        => false
                                                                , 'description_tag'   => 'p'
                                                                , 'value' 			  => $field_options[ 'value_attr' ][ 'value' ]					// ''
                                                                , 'attr'              => $field_options[ 'value_attr' ][ 'attr' ]					//array( 'placeholder' => "1\n2\n3\n4" )   //Override Placeholder value, because of escaping \n symbols
                                                                , 'rows'              => $field_options[ 'value_attr' ][ 'rows' ]					//2
                                                                , 'cols'              => $field_options[ 'value_attr' ][ 'cols' ]					//37
                                                                , 'show_in_2_cols'    => false
                                                                , 'attr'              => array(
                                                                      'oninput'   => "javascript:this.onchange();"
                                                                    , 'onpaste'   => "javascript:this.onchange();"
                                                                    , 'onkeypress'=> "javascript:this.onchange();"
                                                                    , 'onchange'  => "javascript:wpbc_check_typed_values('".$field_name."');"
                                                                )
                                                        )
                                                        , true
                                                    );

			do_action( 'wpbc_settings_form_page_after_values', $field_name, $field_options );                            //FixIn: TimeFreeGenerator

            ?>
            <tr><th colspan="2" style="border-bottom:1px solid #eee;padding:10px 0 0;"></th></tr>
            
            <tr class="wpbc_add_field_row">
                <th colspan="2" class="wpdevelop">                    
                    <a onclick="javascript:wpbc_add_field ( '<?php echo $field_name; ?>', '<?php echo $field_options['type']; ?>' );" 
                       href="javascript:void(0)" 
                       style="" 
                       class="button button-primary"><i class="menu_icon icon-1x glyphicon glyphicon-plus"></i>&nbsp;&nbsp;<?php _e( 'Add New Field' ,'booking'); ?></a>
                    &nbsp;&nbsp;
                    <a onclick="javascript:wpbc_hide_fields_generators();" 
                       href="javascript:void(0)" 
                       style="" 
                       class="button button"><i class="menu_icon icon-1x glyphicon glyphicon-eye-close"></i>&nbsp;&nbsp;<?php _e( 'Close' ,'booking'); ?></a>
                </th>
            </tr>

            <tr class="wpbc_edit_field_row">
                <th colspan="2" class="wpdevelop">                    
                    <a onclick="javascript:wpbc_finish_edit_form_field ( '<?php echo $field_name; ?>', '<?php echo $field_options['type']; ?>' );" 
                       href="javascript:void(0)" 
                       style="" 
                       class="button button-primary"><i class="menu_icon icon-1x glyphicon glyphicon-edit"></i>&nbsp;&nbsp;<?php _e( 'Save Changes' ,'booking'); ?></a>
                    &nbsp;&nbsp;
                    <a onclick="javascript:wpbc_hide_fields_generators();" 
                       href="javascript:void(0)" 
                       style="" 
                       class="button button"><i class="menu_icon icon-1x glyphicon glyphicon-remove"></i>&nbsp;&nbsp;<?php _e( 'Cancel' ,'booking'); ?></a>
                </th>
            </tr>
                        
        </table><?php
    }


    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Activate | Deactivate " >    
    
    public function activate() {
        
        add_bk_option( 'booking_form',          $this->get_form_in__shortcodes() );
        add_bk_option( 'booking_form_show',     $this->get_form_show_in__shortcodes() );
        add_bk_option( 'booking_form_visual',   $this->import_old_booking_form() );
    }
    
    public function deactivate() {
        
        delete_bk_option( 'booking_form' );
        delete_bk_option( 'booking_form_show' );
        delete_bk_option( 'booking_form_visual');
    }
    //                                                                              </editor-fold>
}

add_action('wpbc_menu_created', array( new WPBC_Page_SettingsFormFieldsFree() , '__construct') );    // Executed after creation of Menu