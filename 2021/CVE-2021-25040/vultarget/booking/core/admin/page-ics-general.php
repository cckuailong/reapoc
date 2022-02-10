<?php
/**
 * @version     1.0
 * @menu		Booking > Settings > (Sync) General page
 * @category    Settings API
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com
 * @modified    2017-12-18
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

//FixIn: 8.1.1.10
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/** API  for  Settings Page  */
class WPBC_API_SettingsGeneralSync extends WPBC_Settings_API  {

    /**
	 * Settings API Constructor
     *  During creation,  system try to load values from DB, if exist.
     *
     * @param type $id - "Pure Name"
     */
    public function __construct( $id,  $init_fields_values = array(), $options = array() ) {

    	// This configuration meaning  that  options will be saved separately, without $id of this CLASS,  just  by using names of fields --  update_bk_option( $this->options['db_prefix_option'] . $field_name , $field_value );
        $default_options = array(
                        'db_prefix_option' => ''
                      , 'db_saving_type'   => 'separate'
            );
                                                                                // separate_prefix: update_bk_option( $this->options['db_prefix_option'] . $settings_id . '_' . $field_name , $value );
        $options = wp_parse_args( $options, $default_options );

        /**
	 	 * Activation  and deactivation  of these options already  done at  the wpbc-gcal.php file

         // add_bk_action( 'wpbc_other_versions_activation',   array( $this, 'activate'   ) );      // Activate
         // add_bk_action( 'wpbc_other_versions_deactivation', array( $this, 'deactivate' ) );      // Deactivate
        */

        parent::__construct( $id, $options, $init_fields_values );              // Define ID of Setting page and options
    }



    /** Define settings Fields  */
    public function init_settings_fields() {

        $this->fields = array();

        // TODO:
        //      add_bk_option( 'booking_gcal_events_form_fields', 'a:3:{s:5:"title";s:9:"text^name";s:11:"description";s:12:"text^details";s:5:"where";s:5:"text^";}');

        // Problem with  email^email  field,  previously  its was text^email
        // problem with  textarea^details  field,  previously  its was text^details


        ////////////////////////////////////////////////////////////////////////
        // Get form fields from different forms in the paid versions
        ////////////////////////////////////////////////////////////////////////
        $options = array();
        $options[ 'text^' ] = __('None' ,'booking');

        $booking_forms = wpbc_get_fields_list_in_booking_form();
//debuge($booking_forms);
        foreach ( $booking_forms as $single_booking_form ) {

            //OPTGROUP - Open
            $options[ $single_booking_form['name'] ] = array(
                                                  'title' => ucfirst( trim( $single_booking_form['name'] ) )
                                                , 'optgroup' => true
                                                , 'close'    => false );

                $field_listing = $single_booking_form['listing'];
                for ( $i = 0; $i < $single_booking_form['num']; $i++ ) {
                    $options[   (  ( trim( $single_booking_form['name'] ) != 'standard' ) ? trim( $single_booking_form['name'] ) . '^'  : '' ) .
                                trim( $single_booking_form['listing']['fields_type'][$i] ). '^' . trim( $single_booking_form['listing']['fields'][$i] )
                            ] = ( trim( $single_booking_form['listing']['labels'][$i] ) );
                }

            //OPTGROUP - Close
            $options[ $single_booking_form['name'] . '_close' ] = array(
                                                  'title' => $single_booking_form['name'] // ucfirst( trim( $option_group_name ) )
                                                , 'optgroup' => true
                                                , 'close'    => true );
        }
        ////////////////////////////////////////////////////////////////////////
//debuge($options);


        //             booking_gcal_events_form_fields                          - !!! real  name of option to  save. This option  have to  be skipped during saving and during loading need some actions.
        $this->fields['booking_gcal_events_form_fields_title'] = array(
                                    'type' => 'select'
                                    , 'default' => ''
                                    , 'title' => __('Event Title', 'booking')
                                    , 'description' => sprintf( __( 'Select field for assigning to %sevent property%s' ,'booking'), '<b>', '</b>' )
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'events_fields'
                            );
        $this->fields['booking_gcal_events_form_fields_description'] = array(
                                    'type' => 'select'
                                    , 'default' => ''
                                    , 'title' => __('Event Description (optional field)' ,'booking')
                                    , 'description' => sprintf( __( 'Select field for assigning to %sevent property%s' ,'booking'), '<b>', '</b>' )
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'events_fields'
                            );
        $this->fields['booking_gcal_events_form_fields_where'] = array(
                                    'type' => 'select'
                                    , 'default' => ''
                                    , 'title' => __('Location' ,'booking')
                                    , 'description' => sprintf( __( 'Select field for assigning to %sevent property%s' ,'booking'), '<b>', '</b>' )
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'events_fields'
                            );
        ////////////////////////////////////////////////////////////////////////


        $options = array();
        $options[''] = __('Default' ,'booking');

        global $wpbc_booking_region_cities_list;                    // structure: $wpbc_booking_region_cities_list["Pacific"]["Fiji"] = "Fiji";

        foreach ( $wpbc_booking_region_cities_list as $region => $region_cities) {

            //OPTGROUP - Open
            $options[ $region ] = array(
                                                  'title' => ucfirst( trim( $region ) )
                                                , 'optgroup' => true
                                                , 'close'    => false );

                foreach ($region_cities as $city_key => $city_title) {

                    $options[   trim( $region .'/'. $city_key )  ] = ( trim( $city_title ) );
                }

            //OPTGROUP - Close
            $options[ $region . '_close' ] = array(
                                                  'title' => $region // ucfirst( trim( $option_group_name ) )
                                                , 'optgroup' => true
                                                , 'close'    => true );
        }
        $this->fields['booking_gcal_timezone'] = array(
                                    'type' => 'select'
                                    , 'default' => ''
                                    , 'title' => __('Timezone', 'booking')
                                    , 'description' => __('Select a city in your required timezone, if you are having problems with dates and times.' ,'booking')
                                    , 'description_tag' => 'p'
                                    , 'css' => ''
                                    , 'options' => $options
                                    , 'group' => 'general'
                            );

        if ( class_exists( 'wpdev_bk_biz_s' ) ) {
			//FixIn: 8.1.3.29
			$this->fields['booking_ics_import_add_change_over_time'] = array(
										  'type'        => 'checkbox'
										, 'default'     => 'On'
										, 'title'       => __('Use check in/out time', 'booking')
										, 'label'       => __('Use check in/out time of plugin, during import .ics feeds' ,'booking')
										, 'description' => ''
										, 'group'       => 'import_advanced'
								);
			$this->fields['booking_ics_import_append_checkout_day'] = array(
										  'type'        => 'checkbox'
										, 'default'     => 'On'
										, 'title'       => __('Append check out day', 'booking')
										, 'label'       => __('Append one check out day, during import .ics feeds, if activated using check in/out times' ,'booking')
										, 'description' => ''
										, 'group'       => 'import_advanced'
								);
			//FixIn: 8.5.2.3
			$this->fields['booking_is_ics_export_only_approved'] = array(
										  'type'        => 'checkbox'
										, 'default'     => 'Off'
										, 'title'       => __('Export only approved bookings', 'booking')
										, 'label'       => __('Enable of export only approved bookings in .ics feeds' ,'booking')
										, 'description' => ''
										, 'group'       => 'import_advanced'
								);

			//FixIn: 8.8.3.19
			$this->fields['booking_is_ics_export_imported_bookings'] = array(
										  'type' => 'select'
										, 'default' => ''
										, 'title' => __('Export bookings type', 'booking')
										, 'description' => __('Select which type of bookings to export' ,'booking')
										, 'description_tag' => 'p'
										, 'css' => ''
										, 'options' => array (
												'' 			=> __('All bookings'),
												'plugin' 	=> __('Bookings created in Booking Calendar'),
												'imported' 	=> __('Imported bookings'),
											)
										, 'group'       => 'import_advanced'
								);
        }

        //FixIn: 8.5.1.1

		//FixIn: 8.4.7.1
		$this->fields['booking_ics_force_import'] = array(
									  'type'        => 'checkbox'
									, 'default'     => 'Off'
									, 'title'       => __('Force import', 'booking')
									, 'label'       => __('Import bookings without checking, if such bookings already have been imported.' ,'booking')
									, 'description' => ''
									, 'group'       => 'import_advanced'
							);
		//FixIn: 8.4.7.12
		$this->fields['booking_ics_force_trash_before_import'] = array(
									  'type'        => 'checkbox'
									, 'default'     => 'Off'
									, 'title'       => __('Trash all imported bookings before new import', 'booking') //. ' ' . '[Experimental Feature]'
									, 'label'       => __('Move all previously imported bookings to trash  before new import bookings. Its can resolve issue of updating deleted and edited events in external sources. Its work only, if you are using one source (.ics feed) for importing into specific booking resource!' ,'booking')
									, 'description' => ''
									, 'group'       => 'import_advanced'
							);
        ////////////////////////////////////////////////////////////////////////

    }

}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsGeneralSync extends WPBC_Page_Structure {

    public $settings_api = false;


    /**
	 * API - for Fields of this Settings Page
     *
     * @param array $init_fields_values - array of init form  fields data - this array  can  ovveride "default" fields and loaded data.
     * @return object API
     */
    public function get_api( $init_fields_values = array() ){

        if ( $this->settings_api === false ) {
            $this->settings_api = new WPBC_API_SettingsGeneralSync( 'general_sync' , $init_fields_values );
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

        $subtabs[ 'general' ] = array(
                              'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
							, 'title'      => __( 'General', 'booking' ) 												// Title of TAB
							, 'page_title' => __( 'General Settings', 'booking' ) . ' - ' . __( 'Sync', 'booking' )		// Title of Page
							, 'hint'       => __( 'General Settings', 'booking' ) . ' - ' . __( 'Sync', 'booking' )		// Hint
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  true                                // Is this sub tab activated by default or not: true || false.
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

        do_action( 'wpbc_hook_settings_page_header', 'ics_general_settings' );    // Define Notices Section and show some static messages, if needed

        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.

        // if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.



        ////////////////////////////////////////////////////////////////////////
        // Load Data
        ////////////////////////////////////////////////////////////////////////

        $this->get_api();                                                       // Load fields Data from DB

        /**
	 	 * O v e r l o a d    some   values   for our pseudo options - during normal  opening of page
         * if we making saving,  so  we need to overload these options
         * one more time
         */
        $booking_gcal_events_form_fields = get_bk_option( 'booking_gcal_events_form_fields');
        $booking_gcal_events_form_fields = maybe_unserialize( $booking_gcal_events_form_fields );

        if (isset($booking_gcal_events_form_fields['title']))
            $this->get_api()->set_field_value( 'booking_gcal_events_form_fields_title', $booking_gcal_events_form_fields['title'] );

        if (isset($booking_gcal_events_form_fields['description']))
            $this->get_api()->set_field_value( 'booking_gcal_events_form_fields_description', $booking_gcal_events_form_fields['description'] );

        if (isset($booking_gcal_events_form_fields['where']))
            $this->get_api()->set_field_value( 'booking_gcal_events_form_fields_where', $booking_gcal_events_form_fields['where'] );

        $this->get_api()->set_values_to_fields();


        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form
        ////////////////////////////////////////////////////////////////////////

        $submit_form_name = 'wpbc_general_sync';                         // Define form name

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


                    wpbc_open_meta_box_section( 'wpbc_settings_general_sync_events_general', __('General Settings' ,'booking') );

                        $this->get_api()->show( 'general' );

                    wpbc_close_meta_box_section();


                    wpbc_open_meta_box_section( 'wpbc_settings_general_sync_events_fields',  __('Import' ,'booking') . ' > ' . __('Assign events fields to specific booking form field' ,'booking') );

                        $this->get_api()->show( 'events_fields' );

                    wpbc_close_meta_box_section();

                    //FixIn: 8.5.1.1
					// if ( class_exists( 'wpdev_bk_biz_s' ) ) {

						wpbc_open_meta_box_section( 'wpbc_settings_general_sync_events_fields', __( 'Import', 'booking' ) . ' ' . __( 'Advanced', 'booking' ) );

							$this->get_api()->show( 'import_advanced' );

						wpbc_close_meta_box_section();
					// }
					?>

					<div class="clear"></div>

					<input type="submit" value="<?php _e('Save Changes','booking'); ?>" class="button button-primary wpbc_submit_button" />
				<?php

				?>
            </form>
        </span>
        <?php

        do_action( 'wpbc_hook_settings_page_footer', 'ics_general_settings' );

        $this->enqueue_js();

    }


    /** Save Chanages */
    public function update() {
//        if ( function_exists( 'wpbc_general_sync__update') )
//            wpbc_general_sync__update();

        // Get Validated Email fields
        $validated_fields = $this->get_api()->validate_post();

        $validated_fields = apply_filters( 'wpbc_fields_before_saving_to_db__general_sync', $validated_fields );   //Hook for validated fields.

//debuge($validated_fields);

        $this->get_api()->save_to_db( $validated_fields );

        wpbc_show_changes_saved_message();
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
				height: 2em;
				padding: 2px;
				border-radius: 0;
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

//        //Show|Hide grayed section
//        $js_script .= "
//                        if ( ! jQuery('#ics_general_booking_gcal_auto_import_is_active').is(':checked') ) {
//                            jQuery('.wpbc_tr_auto_import').addClass('hidden_items');
//                        }
//                      ";

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
add_action('wpbc_menu_created', array( new WPBC_Page_SettingsGeneralSync() , '__construct') );    // Executed after creation of Menu


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 *  Validate some fields during saving to DB
 *  Skip  saving some pseudo  options,  instead of that  creare new real  option.
 *
 * @param array $validated_fields
 * @return type
 */
function wpbc_fields_before_saving_to_db__general_sync( $validated_fields ) {

    // Set  new option based on pseudo  options
    $validated_fields['booking_gcal_events_form_fields'] = array(
                                                                  'title'       => $validated_fields['booking_gcal_events_form_fields_title']
                                                                , 'description' => $validated_fields['booking_gcal_events_form_fields_description']
                                                                , 'where'       => $validated_fields['booking_gcal_events_form_fields_where']
                                                                );
    // Unset  several pseudo options.
    unset( $validated_fields['booking_gcal_events_form_fields_title'] );
    unset( $validated_fields['booking_gcal_events_form_fields_description'] );
    unset( $validated_fields['booking_gcal_events_form_fields_where'] );

    return $validated_fields;
}
add_filter('wpbc_fields_before_saving_to_db__general_sync', 'wpbc_fields_before_saving_to_db__general_sync');



/**
	 * Override fields array  of Settings page,  AFTER saving to  DB. Some fields have to have different Values.
 *  Set  here values for our pseudo-options, after saving to  DB
 *  Because they was not overloading during this saving
 *
 * @param array $fields
 * @param string $page_id
 * @return array - fields
 */
function wpbc_fields_after_saving_to_db__general_sync( $fields, $page_id ) {

    if ( $page_id == 'general_sync' ) {                                          // Check our API ID  relative saving of this settings page

        $booking_gcal_events_form_fields = get_bk_option( 'booking_gcal_events_form_fields');
        $booking_gcal_events_form_fields = maybe_unserialize( $booking_gcal_events_form_fields );

        if (isset($booking_gcal_events_form_fields['title']))
            $fields[ 'booking_gcal_events_form_fields_title' ]['value'] = $booking_gcal_events_form_fields['title'];

        if (isset($booking_gcal_events_form_fields['description']))
            $fields[ 'booking_gcal_events_form_fields_description' ]['value'] = $booking_gcal_events_form_fields['description'];

        if (isset($booking_gcal_events_form_fields['where']))
            $fields[ 'booking_gcal_events_form_fields_where' ]['value'] = $booking_gcal_events_form_fields['where'];

    }
    return $fields;
}
add_filter('wpbc_fields_after_saving_to_db', 'wpbc_fields_after_saving_to_db__general_sync', 10, 2);
