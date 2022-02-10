<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Google Calendar Import
 * @category Data Sync
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2014.06.27
 * @since 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  A J A X
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function wpbc_import_gcal_events(){ global $wpdb;
/*   $_POST
 * 
 *   [action] => WPBC_IMPORT_GCAL_EVENTS
            [user_id] => 1
            [booking_gcal_events_from] => Array
                (
                    [0] => date
                    [1] => 2014-07-01
                    [2] => hour
                )

            [booking_gcal_events_until] => Array
                (
                    [0] => month-start
                    [1] => 
                    [2] => minute
                )

            [booking_gcal_events_max] => 5
            [wpbc_booking_resource] => 
 * 
 */       
    $user_id = (int) $_POST['user_id'];

    
    $wpbc_Google_Calendar = new WPBC_Google_Calendar();
    
    $wpbc_Google_Calendar->set_timezone( get_bk_option('booking_gcal_timezone') );
    
    $wpbc_Google_Calendar->set_events_from_with_array( $_POST['booking_gcal_events_from'] ); 
    
    $wpbc_Google_Calendar->set_events_until_with_array( $_POST['booking_gcal_events_until'] ); 
    
    $wpbc_Google_Calendar->set_events_max(   $_POST['booking_gcal_events_max']  );
        

    if ( ( isset($_POST['wpbc_booking_resource']) ) && ( empty($_POST['wpbc_booking_resource']) ) ) {
        
        $wpbc_Google_Calendar->setUrl( get_bk_option( 'booking_gcal_feed') );
        $import_result = $wpbc_Google_Calendar->run();
        
    } else {
        
        if ( $_POST['wpbc_booking_resource'] != 'all' ) {                             // One resource
            
            $wpbc_booking_resource_id = intval( $_POST['wpbc_booking_resource'] );    
            
            $wpbc_Google_Calendar->setResource($wpbc_booking_resource_id);
            
            $wpbc_booking_resource_feed = get_booking_resource_attr( $wpbc_booking_resource_id );      
            $wpbc_booking_resource_feed = $wpbc_booking_resource_feed->import;
            $wpbc_Google_Calendar->setUrl($wpbc_booking_resource_feed);
            
            $import_result = $wpbc_Google_Calendar->run();
        } else {                                                                // All  resources
            
            
            $where = '';                                                        // Where for the different situation: BL and MU
            $where = apply_bk_filter('multiuser_modify_SQL_for_current_user', $where);
            if ($where != '') 
                $where = ' WHERE ' . $where;
            $my_sql = "SELECT booking_type_id, import FROM {$wpdb->prefix}bookingtypes {$where}";

            $types_list = $wpdb->get_results( $my_sql );

            foreach ($types_list as $wpbc_booking_resource) {
                $wpbc_booking_resource_id = $wpbc_booking_resource->booking_type_id;
                $wpbc_booking_resource_feed = $wpbc_booking_resource->import;
                if ( (! empty($wpbc_booking_resource_feed) ) && ($wpbc_booking_resource_feed != NULL ) && ( $wpbc_booking_resource_feed != '/' ) ) {
                    
                    $wpbc_Google_Calendar->setUrl($wpbc_booking_resource_feed);
                    $wpbc_Google_Calendar->setResource($wpbc_booking_resource_id);
                    $import_result = $wpbc_Google_Calendar->run();                
                }
            }            
        }        
    }
    if ( (isset($import_result)) && ( $import_result!= false ) )
        $wpbc_Google_Calendar->show_message( __('Done' ,'booking') );
    // else $wpbc_Google_Calendar->show_message( __('Imported 0 events.' ,'booking') );
    ?> <script type="text/javascript">        
            jQuery('#ajax_message').animate({opacity:1},5000).fadeOut(1000);
       </script> <?php        
}
add_bk_action('wpbc_import_gcal_events' , 'wpbc_import_gcal_events' ); 


function wpbc_silent_import_all_events() {

    global $wpdb;
//    debuge(1);
    $wpbc_Google_Calendar = new WPBC_Google_Calendar();
    
    $wpbc_Google_Calendar->setSilent();
            
    $wpbc_Google_Calendar->set_timezone( get_bk_option('booking_gcal_timezone') );
    
    $wpbc_Google_Calendar->set_events_max( get_bk_option( 'booking_gcal_events_max') );
    
    $wpbc_Google_Calendar->set_events_from_with_array( 
                                                        array(  get_bk_option( 'booking_gcal_events_from')
                                                                , get_bk_option( 'booking_gcal_events_from_offset' )
                                                                , get_bk_option( 'booking_gcal_events_from_offset_type' ) ) 
                                                    ); 
    
    $wpbc_Google_Calendar->set_events_until_with_array( 
                                                        array(  get_bk_option( 'booking_gcal_events_until')
                                                                , get_bk_option( 'booking_gcal_events_until_offset' )
                                                                , get_bk_option( 'booking_gcal_events_until_offset_type' ) ) 
                                                    );
    
    if ( ! class_exists('wpdev_bk_personal') ) { 
        
        $wpbc_Google_Calendar->setUrl( get_bk_option( 'booking_gcal_feed') );
        $import_result = $wpbc_Google_Calendar->run();
        
    } else {
        
        $types_list = $wpdb->get_results( "SELECT booking_type_id, import FROM {$wpdb->prefix}bookingtypes" );

        foreach ($types_list as $wpbc_booking_resource) {
            $wpbc_booking_resource_id = $wpbc_booking_resource->booking_type_id;
            $wpbc_booking_resource_feed = $wpbc_booking_resource->import;
            if ( (! empty($wpbc_booking_resource_feed) ) && ($wpbc_booking_resource_feed != NULL ) && ( $wpbc_booking_resource_feed != '/' ) ) {

                $wpbc_Google_Calendar->setUrl($wpbc_booking_resource_feed);
                $wpbc_Google_Calendar->setResource($wpbc_booking_resource_id);
                $import_result = $wpbc_Google_Calendar->run();                
            }
        }                    
    }
//    debuge(2);
}
add_bk_action('wpbc_silent_import_all_events' , 'wpbc_silent_import_all_events' ); 

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Fields for Modal window
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    function wpbc_gcal_settings_content_field_from( $booking_gcal_events_from, $booking_gcal_events_from_offset = '', $booking_gcal_events_from_offset_type = '' ) {
        if ($booking_gcal_events_from == "date") {
            echo '<style type="text/css"> .booking_gcal_events_from .wpbc_offset_value { display:none; } </style>';
        } else {
            echo '<style type="text/css"> .booking_gcal_events_from .wpbc_offset_datetime { display:none; } </style>';            
        }        
        ?>
        <tr valign="top">
            <th scope="row"><label for="booking_gcal_events_from" ><?php _e('From' ,'booking'); ?>:</label></th>
            <td class="booking_gcal_events_from">                        
                <select id="booking_gcal_events_from" name="booking_gcal_events_from"
                        onchange="javascript: if(this.value=='date') {
                            jQuery('.booking_gcal_events_from .wpbc_offset_value').hide();
                            jQuery('.booking_gcal_events_from .wpbc_offset_datetime').show();
                        } else {
                            jQuery('.booking_gcal_events_from .wpbc_offset_value').show();
                            jQuery('.booking_gcal_events_from .wpbc_offset_datetime').hide();                               
                        }
                        jQuery('#booking_gcal_events_from_offset').val('');" >                        
                    <?php 
                    $wpbc_options = array(
                                            "now" => __('Now' ,'booking')
                                          , "today" => __('00:00 today' ,'booking')
                                          , "week" => __('Start of current week' ,'booking')
                                          , "month-start" => __('Start of current month' ,'booking')
                                          , "month-end" => __('End of current month' ,'booking')
                                          , "any" => __('The start of time' ,'booking')
                                          , "date" => __('Specific date / time' ,'booking')
                                    );
                    foreach ($wpbc_options as $key => $value) {
                        ?><option <?php if( $booking_gcal_events_from == $key ) echo "selected"; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                    }
                    ?>
                </select>
                <span class="description"><?php _e('Select option, when to start retrieving events.' ,'booking');?></span>                
                <div class="booking_gcal_events_from_offset" style="margin:10px 0 0;">
                    <label for="booking_gcal_events_from_offset"> <span class="wpbc_offset_value"><?php _e('Offset' ,'booking'); ?></span><span class="wpbc_offset_datetime" ><?php _e('Enter date / time' ,'booking'); ?></span>: </label>
                    <input type="text"  id="booking_gcal_events_from_offset" name="booking_gcal_events_from_offset" value="<?php echo $booking_gcal_events_from_offset; ?>" style="width:100px;text-align: right;" />
                    <span class="wpbc_offset_value">
                        <select id="booking_gcal_events_from_offset_type" name="booking_gcal_events_from_offset_type" style="margin-top: -2px;width: 99px;">
                            <?php 
                            $wpbc_options = array(
                                                    "second" => __('seconds' ,'booking')
                                                  , "minute" => __('minutes' ,'booking')
                                                  , "hour" => __('hours' ,'booking')
                                                  , "day" => __('days' ,'booking')
                                            );
                            foreach ($wpbc_options as $key => $value) {
                                ?><option <?php if( $booking_gcal_events_from_offset_type == $key ) echo "selected"; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                            }
                            ?>
                        </select>
                        <span class="description"><?php _e('You can specify an additional offset from you chosen start point. The offset can be negative.' ,'booking');?></span>
                    </span>
                    <span class="wpbc_offset_datetime">
                        <em><?php printf(__('Type your date in format %s. Example: %s' ,'booking'),'Y-m-d','2014-08-01'); ?></em>
                    </span>
                </div>
            </td>
        </tr>
        <?php
    }

    
    function wpbc_gcal_settings_content_field_until( $booking_gcal_events_until, $booking_gcal_events_until_offset = '', $booking_gcal_events_until_offset_type = '' ) {  
        if ($booking_gcal_events_until == "date") {
            echo '<style type="text/css"> .booking_gcal_events_until .wpbc_offset_value { display:none; } </style>';
        } else {
            echo '<style type="text/css"> .booking_gcal_events_until .wpbc_offset_datetime { display:none; } </style>';            
        }
        ?>
        <tr valign="top">
            <th scope="row"><label for="booking_gcal_events_until" ><?php _e('Until' ,'booking'); ?>:</label></th>
            <td class="booking_gcal_events_until">                                
                <select id="booking_gcal_events_until" name="booking_gcal_events_until"
                        onchange="javascript: if(this.value=='date') {
                            jQuery('.booking_gcal_events_until .wpbc_offset_value').hide();
                            jQuery('.booking_gcal_events_until .wpbc_offset_datetime').show();
                        } else {
                            jQuery('.booking_gcal_events_until .wpbc_offset_value').show();
                            jQuery('.booking_gcal_events_until .wpbc_offset_datetime').hide();                            
                        }
                        jQuery('#booking_gcal_events_until_offset').val('');" >
                    <?php 
                    $wpbc_options = array(
                                            "now" => __('Now' ,'booking')
                                          , "today" => __('00:00 today' ,'booking')
                                          , "week" => __('Start of current week' ,'booking')
                                          , "month-start" => __('Start of current month' ,'booking')
                                          , "month-end" => __('End of current month' ,'booking')
                                          , "any" => __('The end of time' ,'booking')
                                          , "date" => __('Specific date / time' ,'booking')
                                    );
                    foreach ($wpbc_options as $key => $value) {
                        ?><option <?php if( $booking_gcal_events_until == $key ) echo "selected"; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                    }
                    ?>
                </select>
                <span class="description"><?php _e('Select option, when to stop retrieving events.' ,'booking');?></span>
                <div class="booking_gcal_events_until_offset" style="margin:10px 0 0;">
                    <label for="booking_gcal_events_until_offset" > <span class="wpbc_offset_value"><?php _e('Offset' ,'booking'); ?></span><span class="wpbc_offset_datetime" ><?php _e('Enter date / time' ,'booking'); ?></span>: </label>
                    <input type="text" id="booking_gcal_events_until_offset" name="booking_gcal_events_until_offset" value="<?php echo $booking_gcal_events_until_offset; ?>" style="width:100px;text-align: right;" />
                    <span class="wpbc_offset_value">
                        <select id="booking_gcal_events_until_offset_type" name="booking_gcal_events_until_offset_type" style="margin-top: -2px;width: 99px;">
                            <?php 
                            $wpbc_options = array(
                                                    "second" => __('seconds' ,'booking')
                                                  , "minute" => __('minutes' ,'booking')
                                                  , "hour" => __('hours' ,'booking')
                                                  , "day" => __('days' ,'booking')
                                            );
                            foreach ($wpbc_options as $key => $value) {
                                ?><option <?php if( $booking_gcal_events_until_offset_type == $key ) echo "selected"; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                            }
                            ?>
                        </select>
                        <span class="description"><?php _e('You can specify an additional offset from you chosen end point. The offset can be negative.' ,'booking');?></span>
                    </span>
                    <span class="wpbc_offset_datetime">
                        <em><?php  printf(__('Type your date in format %s. Example: %s' ,'booking'),'Y-m-d','2014-08-30'); ?></em>
                    </span>
                    
                </div>
            </td>
        </tr>                        
        <?php
    }

    
    function wpbc_gcal_settings_content_field_max_feeds($booking_gcal_events_max) {
        ?>
        <tr valign="top">
            <th scope="row"><label for="booking_gcal_events_max" ><?php _e('Maximum number' ,'booking'); ?>:</label></th>
            <td><input id="booking_gcal_events_max"  name="booking_gcal_events_max" class="regular-text" type="text" value="<?php echo $booking_gcal_events_max; ?>" />
                <span class="description"><?php 
                    _e('You can specify the maximum number of events to import during one session.' ,'booking');
              ?></span>
            </td>
        </tr>                
        <?php
    }
    
        
    function wpbc_gcal_settings_content_field_timezone($booking_gcal_timezone) {
        ?>
        <tr valign="top">
            <th scope="row"><label for="booking_gcal_timezone" ><?php _e('Timezone' ,'booking'); ?>:</label></th>
            <td>                                
                <select id="booking_gcal_timezone" name="booking_gcal_timezone">
                    <?php 
                    $wpbc_options = array(
                                            "" => __('Default' ,'booking')
                                    );
                    foreach ($wpbc_options as $key => $value) {
                        ?><option <?php if( $booking_gcal_timezone == $key ) echo "selected"; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                    }
                    
                    
                    global $wpbc_booking_region_cities_list;                    // structure: $wpbc_booking_region_cities_list["Pacific"]["Fiji"] = "Fiji";
                    
                    foreach ($wpbc_booking_region_cities_list as $region => $region_cities) {
                        
                        echo '<optgroup label="'. $region .'">';
                        
                        foreach ($region_cities as $city_key => $city_title) {
                            
                            if( $booking_gcal_timezone == $region .'/'. $city_key ) 
                                $is_selected = 'selected'; 
                            else 
                                $is_selected = '';
                            
                            echo '<option '.$is_selected.' value="'. $region .'/'. $city_key .'">' . $city_title . '</option>';
                            
                        }
                        echo '</optgroup>';
                    }
                    
                    
                    ?>
                </select>
                <span class="description"><?php _e('Select a city in your required timezone, if you are having problems with dates and times.' ,'booking');?></span>
            </td>
        </tr>                        
        <?php
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Actions Toolbar Import Buttons
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function wpbc_gcal_extend_buttons_in_action_toolbar_booking_listing() {
    
    $booking_gcal_feed = get_bk_option( 'booking_gcal_feed');
    
    if ( ( ! class_exists('wpdev_bk_personal') ) && ( $booking_gcal_feed == '' ) ) 
         $is_this_btn_disabled = true;                                      
    else $is_this_btn_disabled = false;
    
    wpbc_toolbar_action_import_buttons( $is_this_btn_disabled );     
}
add_bk_action('wpbc_extend_buttons_in_action_toolbar_booking_listing', 'wpbc_gcal_extend_buttons_in_action_toolbar_booking_listing' ); 


/** Import Google Calendar Events Loyout - Modal Window structure */ 
function wpbc_write_content_for_modal_import_gce() {
    
    $booking_gcal_feed = get_bk_option( 'booking_gcal_feed');
    $is_this_btn_disabled = false;

    if ( ( ! class_exists('wpdev_bk_personal') ) && ( $booking_gcal_feed == '' ) ) {

        $is_this_btn_disabled = true;                              
        $settigns_link = wpbc_get_settings_url() ."&tab=sync" ;
    } else {
        $booking_gcal_events_from = get_bk_option( 'booking_gcal_events_from');
        $booking_gcal_events_from_offset = get_bk_option( 'booking_gcal_events_from_offset' );
        $booking_gcal_events_from_offset_type = get_bk_option( 'booking_gcal_events_from_offset_type' );
        
        $booking_gcal_events_until = get_bk_option( 'booking_gcal_events_until');
        $booking_gcal_events_until_offset = get_bk_option( 'booking_gcal_events_until_offset' );
        $booking_gcal_events_until_offset_type = get_bk_option( 'booking_gcal_events_until_offset_type' );
        
        $booking_gcal_events_max = get_bk_option( 'booking_gcal_events_max');
        // $booking_gcal_timezone = get_bk_option( 'booking_gcal_timezone');


    }        
    ?><div id="wpbc_gcal_import_events_modal" class="modal wpbc_popup_modal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">   
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php 
                        if ( $is_this_btn_disabled )    _e('Warning!' ,'booking'); 
                        else                            _e('Retrieve Google Calendar Events ' ,'booking'); 
                    ?></h4>                    
                </div>
                <div class="modal-body">
                    <?php if ($is_this_btn_disabled) { ?>   
                       <label class="help-block" style="display:block;">
                           <?php printf(__('Please configure settings for import Google Calendar events' ,'booking'),'<b>',',</b>'); ?> 
                           <a href="<?php echo $settigns_link; ?>"><?php _e('here' ,'booking');?></a>
                       </label>
                     <?php } else { ?>

                           <table class="visibility_gcal_feeds_settings form-table0 settings-table0 table"  >
                               <tbody>
                               <?php 
                                   if ( function_exists('wpbc_gcal_settings_content_field_selection_booking_resources') ) 
                                       wpbc_gcal_settings_content_field_selection_booking_resources(); 
                                   else {                                                     
                                       ?><input type="hidden" name="wpbc_booking_resource" id="wpbc_booking_resource" value="" /><?php
                                   }
                                   wpbc_gcal_settings_content_field_from( $booking_gcal_events_from, $booking_gcal_events_from_offset, $booking_gcal_events_from_offset_type ); 
                                   wpbc_gcal_settings_content_field_until( $booking_gcal_events_until, $booking_gcal_events_until_offset, $booking_gcal_events_until_offset_type ); 
                                   wpbc_gcal_settings_content_field_max_feeds( $booking_gcal_events_max ); 
                                   // wpbc_gcal_settings_content_field_timezone($booking_gcal_timezone);

                               ?>  
                               </tbody>
                           </table>

                     <?php }  ?>                     
                </div>
                <div class="modal-footer" style="text-align:center;"> 
                    <?php if ($is_this_btn_disabled) { ?>   
                    <a href="<?php  echo $settigns_link; ?>" 
                       class="button button-primary"  style="float:none;" >
                        <?php _e('Configure' ,'booking'); ?>
                    </a>
                    <?php } else { ?>
                    <a href="javascript:void(0)" class="button button-primary"  style="float:none;"                                        
                       onclick="javascript:wpbc_import_gcal_events('<?php echo get_bk_current_user_id(); ?>'
                                                                       , [ jQuery('#booking_gcal_events_from').val(), jQuery('#booking_gcal_events_from_offset').val(), jQuery('#booking_gcal_events_from_offset_type').val() ]
                                                                   , [ jQuery('#booking_gcal_events_until').val(), jQuery('#booking_gcal_events_until_offset').val(), jQuery('#booking_gcal_events_until_offset_type').val() ]
                                                                   , jQuery('#booking_gcal_events_max').val()
                                                                   , jQuery('#wpbc_booking_resource').val()
                            );jQuery('#wpbc_gcal_import_events_modal').modal('hide');"
                       ><?php _e('Import Google Calendar Events' ,'booking'); ?></a>
                    <?php } ?>   
                    <a href="javascript:void(0)" class="button" style="float:none;" data-dismiss="modal"><?php _e('Close' ,'booking'); ?></a>
               </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php         
     
}
add_bk_action( 'wpbc_write_content_for_modals', 'wpbc_write_content_for_modal_import_gce');    

// <editor-fold     defaultstate="collapsed"                        desc=" I m p o r  t     A c t i o n s     B u t t o n  "  >
function wpbc_toolbar_action_import_buttons( $is_this_btn_disabled ) {
         
    $params = array(  
                      'label_for' => 'actions'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Actions:', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(                                                 
                                              'type' => 'button' 
                                            , 'title' => __('Import', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Import Google Calendar Events' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => " if ( 'function' === typeof( jQuery('#wpbc_gcal_import_events_modal').modal ) ) {
                                                                jQuery('#wpbc_gcal_import_events_modal').modal('show');
                                                            } else {
                                                                alert('Warning! Booking Calendar. Its seems that  you have deactivated loading of Bootstrap JS files at Booking Settings General page in Advanced section.')
                                                            }"
                                                                                // Some JavaScript to execure, for example run  the function        //FixIn: 7.0.1.10
                                            , 'class' => ( $is_this_btn_disabled ? ' disabled' : '' )                        // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-import'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );             
    wpbc_bs_button_group( $params );            
}
// </editor-fold>




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Activation
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function wpbc_sync_gcal_activate() {
        
    add_bk_option( 'booking_gcal_feed' , '' );
    add_bk_option( 'booking_gcal_events_from', 'month-start');
    add_bk_option( 'booking_gcal_events_from_offset' , '' );
    add_bk_option( 'booking_gcal_events_from_offset_type' , '' );
    add_bk_option( 'booking_gcal_events_until', 'any');
    add_bk_option( 'booking_gcal_events_until_offset' , '' );
    add_bk_option( 'booking_gcal_events_until_offset_type' , '' );
    add_bk_option( 'booking_gcal_events_max', '25');
    add_bk_option( 'booking_gcal_api_key', '');
    	add_bk_option( 'booking_gcal_timezone','');
    add_bk_option( 'booking_gcal_is_send_email' , 'Off' );
    add_bk_option( 'booking_gcal_auto_import_is_active' , 'Off'  );
    add_bk_option( 'booking_gcal_auto_import_time', '24' );
    
    	add_bk_option( 'booking_gcal_events_form_fields', 'a:3:{s:5:"title";s:9:"text^name";s:11:"description";s:16:"textarea^details";s:5:"where";s:5:"text^";}');
}
add_bk_action('wpbc_other_versions_activation',   'wpbc_sync_gcal_activate' );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Deactivation
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function wpbc_sync_gcal_deactivate() {
    
    delete_bk_option( 'booking_gcal_feed' );
    delete_bk_option( 'booking_gcal_events_from');
    delete_bk_option( 'booking_gcal_events_from_offset' );
    delete_bk_option( 'booking_gcal_events_from_offset_type' );
    
    delete_bk_option( 'booking_gcal_events_until');
    delete_bk_option( 'booking_gcal_events_until_offset' );
    delete_bk_option( 'booking_gcal_events_until_offset_type' );
    
    delete_bk_option( 'booking_gcal_events_max' );    
    delete_bk_option( 'booking_gcal_api_key' );    
    	delete_bk_option( 'booking_gcal_timezone');
    delete_bk_option( 'booking_gcal_is_send_email' );
    delete_bk_option( 'booking_gcal_auto_import_is_active' );
    delete_bk_option( 'booking_gcal_auto_import_time' );
    
    	delete_bk_option( 'booking_gcal_events_form_fields');

}
add_bk_action('wpbc_other_versions_deactivation', 'wpbc_sync_gcal_deactivate' );
