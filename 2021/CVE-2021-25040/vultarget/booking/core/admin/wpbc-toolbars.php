<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Toolbar. Data for UI Elements at Booking Calendar admin pages
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2015-11-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
//   T o o l b a r s
////////////////////////////////////////////////////////////////////////////////

/** T o o l b a r   C o n t a i n e r   f o r   Booking Listing */
function wpbc_bookings_toolbar() {

    wpbc_clear_div();

    wpbc_toolbar_search_by_id_bookings();                                       // Search bookings by  ID - form  at the top  right side of the page

    wpbc_toolbar_btn__view_mode();                                              //  Vertical Buttons

    //  Toolbar ////////////////////////////////////////////////////////////////

    ?><div id="toolbar_booking_listing" style="margin-left: 50px;position:relative;"><?php

        wpbc_bs_toolbar_tabs_html_container_start();

            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            if ( ! isset( $_REQUEST['tab'] ) )  $_REQUEST['tab'] = 'filter';
            $selected_tab = $_REQUEST['tab'];

            wpbc_bs_display_tab(   array(
                                                'title'         => '&nbsp;' . __('Filters', 'booking')
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#filter_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'glyphicon glyphicon-random'
                                                , 'default'     => ( $selected_tab == 'filter' ) ? true : false
                                ) );
            wpbc_bs_display_tab(   array(
                                                'title'         => __('Actions', 'booking')
                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#actions_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"

                                                , 'font_icon'   => 'glyphicon glyphicon-fire'
                                                , 'default'     => ( $selected_tab == 'actions' ) ? true : false

                                ) );

            wpbc_bs_dropdown_menu_help();

            // </editor-fold>

        wpbc_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////

        wpbc_bs_toolbar_sub_html_container_start();

        //  F i l t e r   T o o l b a r   f o r   B o o k i n g   L i s t i n g

        ?><div id="filter_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'filter' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php

            ?><form  name="booking_filters_form" action="" method="post" id="booking_filters_form"  class="form-inline">
                <input type="hidden" name="page_num" id ="page_num" value="1" /><?php

                wpbc_toolbar_btn__apply_reset();                                        //   A p p l y     R e s e t    B u  t t o n s

                wpbc_toolbar_filter__approve_pending();                                 //   A p p r o v e d   |   P e n d i n g   F i l t e r

                wpbc_toolbar_filter__booked_dates();                                    //   B o o k e d   D a t e s   F i l t e r

                wpbc_toolbar_filter__sort();                                            //   S o r t     F i l t e r

                wpbc_toolbar_filter__trash();                                           //   T r a s h   F i l t e r

                ?><span class="advanced_booking_filter" style="display:none;"><?php

                wpbc_toolbar_filter__new_bookings();                                    //   N e w   |   A l l   B o o k i n g s     F i l t e r

                wpbc_toolbar_filter__creation_date();                                   //   C r e a t i o n   D a t e     F i l t e r

                if ( function_exists( 'wpbc_filter_payment_status' ) )  wpbc_filter_payment_status();

                if ( function_exists( 'wpbc_filter_min_max_cost' ) )    wpbc_filter_min_max_cost();

                if ( function_exists( 'wpbc_filter_text_keyword' ) )    wpbc_filter_text_keyword();

                if ( function_exists( 'wpbc_filter_find_lost_bookings' ) )   wpbc_filter_find_lost_bookings();          //FixIn: 8.5.2.19

                ?></span><?php

                make_bk_action( 'wpbc_br_selection_for_listing' );

                if ( function_exists( 'wpbc_filter_template_save_delete' ) ) wpbc_filter_template_save_delete();

            ?></form><?php

            wpbc_clear_div();

            wpbc_toolbar_expand_collapse_btn( 'advanced_booking_filter' );

        ?></div><?php


        // A c t i o n s   T o o l b a r   f o r   B o o k i n g   L i s t i n g

        ?><div id="actions_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'actions' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php

            $user = wp_get_current_user();
            $user_bk_id = $user->ID;

            wpbc_toolbar_btn__approve_reject( $user_bk_id );                            //   A p p r o v e   |   R e j e c t

            wpbc_toolbar_btn__delete_reason( $user_bk_id );                             //   D e l e t e

            wpbc_toolbar_btn__read_all( $user_bk_id );                                  //   R e a d   |   A l  l   |    U n  r e a  d

            if ( function_exists('wpbc_toolbar_action_print_button') ) wpbc_toolbar_action_print_button();

            make_bk_action('wpbc_extend_buttons_in_action_toolbar_booking_listing' );

            if ( function_exists('wpbc_toolbar_action_export_print_buttons') ) wpbc_toolbar_action_export_print_buttons();

            wpbc_toolbar_btn__empty_trash( $user_bk_id );                             	//   Empty Trash				//FixIn: 8.5.2.24

            wpbc_clear_div();

        ?></div><?php

        wpbc_bs_toolbar_sub_html_container_end();

        wpbc_toolbar_is_send_emails_btn();

    ?></div><?php

    wpbc_clear_div();
}


/** T o o l b a r   C o n t a i n e r   f o r   Timeline */
function wpbc_timeline_toolbar() {

    wpbc_clear_div();

    wpbc_toolbar_search_by_id_bookings();                                       // Search bookings by  ID - form  at the top  right side of the page

    wpbc_toolbar_btn__view_mode();                                              //  Vertical Buttons

    //  Toolbar ////////////////////////////////////////////////////////////////

    ?><div id="toolbar_booking_listing" style="margin-left: 50px;position:relative;"><?php

        wpbc_bs_toolbar_tabs_html_container_start();

            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            if ( ! isset( $_REQUEST['tab_cvm'] ) )  $_REQUEST['tab_cvm'] = 'actions_cvm';
            $selected_tab = $_REQUEST['tab_cvm'];

            wpbc_bs_display_tab(   array(
                                                'title'         => __('Actions', 'booking')
                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#actions_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'glyphicon glyphicon-fire'
                                                , 'default'     => ( $selected_tab == 'actions_cvm' ) ? true : false

                                ) );


            wpbc_bs_dropdown_menu_help();

            // </editor-fold>

        wpbc_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////

        wpbc_bs_toolbar_sub_html_container_start();

        // A c t i o n s   T o o l b a r   f o r     T i m e l i n e

        ?><div id="actions_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'actions_cvm' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php

            wpbc_toolbar_btn__timeline_view_mode();                             //  View Mode    Buttons

            wpbc_toolbar_btn__timeline_navigation();                            //  Navigation   Buttons

            make_bk_action( 'wpbc_br_selection_for_timeline' );

        ?></div><?php

        wpbc_bs_toolbar_sub_html_container_end();

        wpbc_toolbar_is_send_emails_btn();

    ?></div><?php

    wpbc_clear_div();

}


/** T o o l b a r   C o n t a i n e r   f o r   Add New Booking */
function wpbc_add_new_booking_toolbar() {

    wpbc_clear_div();

    //  Toolbar ////////////////////////////////////////////////////////////////

    ?><div id="toolbar_booking_listing" style="position:relative;"><?php

        wpbc_bs_toolbar_tabs_html_container_start();

            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            if ( ! isset( $_REQUEST['toolbar'] ) )  $_REQUEST['toolbar'] = 'filter';
            $selected_tab = $_REQUEST['toolbar'];

            wpbc_bs_display_tab(   array(
                                                'title'         => __('Options', 'booking')
                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#filter_toolbar_container').show();"
                                                                    . "jQuery('.nav-tab').removeClass('nav-tab-active');"
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'glyphicon glyphicon-fire'
                                                , 'default'     => ( $selected_tab == 'filter' ) ? true : false

                                ) );


            wpbc_bs_dropdown_menu_help();

            // </editor-fold>

        wpbc_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////

        wpbc_bs_toolbar_sub_html_container_start();

        //  T o o l b a r
        ?><div id="filter_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'filter' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php

            if (    (  function_exists( 'wpbc_toolbar_btn__resource_selection' ) )
                 && ( empty( $_GET['booking_hash'] ) )  )                     //Do not show resource seleciton  if editing booking.	//FixIn:7.1.2.10
                wpbc_toolbar_btn__resource_selection();

            if (  function_exists( 'wpbc_toolbar_btn__form_selection' ) )
                wpbc_toolbar_btn__form_selection();

            ////////////////////////////////////////////////////////////////////
            ?><div class="clear-for-mobile"></div><?php

            ?><div class="control-group wpbc-no-padding" style="float:right;margin-right: 0;margin-left: 15px;"><?php

                if ( function_exists( 'wpbc_toolbar_btn__auto_fill' ) )
                    wpbc_toolbar_btn__auto_fill();

                wpbc_toolbar_btn__add_new_booking();

            ?></div><?php
            ////////////////////////////////////////////////////////////////////


            ?><span class="advanced_booking_filter" style="display:none;"><div class="clear" style="width:100%;border-bottom:1px solid #ccc;height:10px;"></div><?php

                // Get possible saved previous "Custom User Calendar data"
                $user_calendar_options = get_user_option( 'booking_custom_' . 'add_booking_calendar_options', get_wpbc_current_user_id() );

                if ( $user_calendar_options === false ) {                       // Default, if no saved previously.
                    $user_calendar_options = array();
                    $user_calendar_options['calendar_months_count'] = 1;
                    $user_calendar_options['calendar_months_num_in_1_row'] = 0 ;
                    $user_calendar_options['calendar_width'] = '';
                    $user_calendar_options['calendar_widthunits'] = 'px';
                    $user_calendar_options['calendar_cell_height'] = '';
                    $user_calendar_options['calendar_cell_heightunits'] = 'px';
                } else {
                    $user_calendar_options = maybe_unserialize( $user_calendar_options );
                }

                wpbc_toolbar_btn__calendar_months_number_selection( $user_calendar_options );

                wpbc_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options );

                wpbc_toolbar_btn__calendar_width( $user_calendar_options );

                wpbc_toolbar_btn__calendar_cell_height( $user_calendar_options );

                wpbc_toolbar_btn__calendar_options_save();

            ?><div class="clear"></div></span><?php


            wpbc_clear_div();

            wpbc_toolbar_expand_collapse_btn( 'advanced_booking_filter' );

        ?></div><?php


        wpbc_bs_toolbar_sub_html_container_end();

        wpbc_toolbar_is_send_emails_btn();

    ?></div><?php

    wpbc_clear_div();

}


////////////////////////////////////////////////////////////////////////////////
//   HTML elements for Toolbar
////////////////////////////////////////////////////////////////////////////////

/**
	 * Expand or Collapse Advanced Filter set
 *
 * @param string $css_class_of_expand_element - CSS Class of element section  to  show or hide
 */
function wpbc_toolbar_expand_collapse_btn( $css_class_of_expand_element ) {

      ?><span id="show_link_advanced_booking_filter" class="tab-bottom tooltip_right"
            title="<?php _e('Expand Advanced Toolbar' ,'booking'); ?>"
            ><a href="javascript:void(0)"
                onclick="javascript:jQuery('.<?php echo $css_class_of_expand_element; ?>').show();
                                    jQuery('#show_link_advanced_booking_filter').hide();
                                    jQuery('#hide_link_advanced_booking_filter').show();"><i
                    class="glyphicon glyphicon-chevron-down"></i></a></span>
        <span id="hide_link_advanced_booking_filter" class="tab-bottom tooltip_right" style="display:none;"
            title="<?php _e('Collapse Advanced Toolbar' ,'booking'); ?>"
            ><a href="javascript:void(0)"
                onclick="javascript:jQuery('.<?php echo $css_class_of_expand_element; ?>').hide();
                                    jQuery('#hide_link_advanced_booking_filter').hide();
                                    jQuery('#show_link_advanced_booking_filter').show();"><i
                    class="glyphicon glyphicon-chevron-up"></i></a></span><?php

}


/** Checkbox - sending emails or not */
function wpbc_toolbar_is_send_emails_btn() {
    ?>
    <div class="btn-group" style="position:absolute;right:0px;margin-top:10px;">
        <fieldset>
            <label for="is_send_email_for_pending" style="display: inline-block;"    >
                <input style="margin:0 4px 2px;"
                    type="checkbox"
					<?php if ( get_bk_option('booking_send_emails_off_listing') === 'On' ) { } else { //FixIn: 8.4.5.4 ?>
					checked="CHECKED"
					<?php } ?>
					id="is_send_email_for_pending" name="is_send_email_for_pending" class="tooltip_top"
                    title="<?php echo esc_js( __( 'Send email notification to customer after approval, cancellation or deletion of bookings', 'booking' ) ); ?>"
                /><?php _e( 'Emails sending', 'booking' ) ?>
            </label>
        </fieldset>
    </div>
    <?php
}


/** Search form  by booking ID (at top right side of page)  */
function wpbc_toolbar_search_by_id_bookings() {

    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( true, false ), array('view_mode', 'wh_booking_id', 'page_num' ) );

    ?>
    <div style=" position: absolute; right: 20px; top: 10px;">
        <form name="booking_filters_formID" action="<?php echo $bk_admin_url . '&view_mode=vm_listing' ; ?>" method="post" id="booking_filters_formID" >
        <?php

            if (isset($_REQUEST['wh_booking_id']))  $wh_booking_id = wpbc_clean_digit_or_csd( $_REQUEST['wh_booking_id'] );                  //  {'1', '2', .... }
            else                                    $wh_booking_id = '';


            $params = array(  'label_for' => 'wh_booking_id'
                                      , 'label' => ''//__('Keyword:', 'booking')
                                      , 'items' => array(
                                 array( 'type' => 'text', 'id' => 'wh_booking_id', 'value' => $wh_booking_id, 'placeholder' => __('Booking ID', 'booking') )
                                , array(
                                    'type' => 'button'
                                    , 'title' => __('Go', 'booking')
                                    , 'class' => 'button-secondary'
                                    , 'font_icon' => 'glyphicon glyphicon-search'
                                    , 'icon_position' => 'right'
                                    , 'action' => "jQuery('#booking_filters_formID').trigger( 'submit' );" )
                                       )
                                );
            ?><div class="control-group wpbc-no-padding" ><?php
                      wpbc_bs_input_group( $params );
            ?></div><?php
        ?>
        </form>
        <?php wpbc_clear_div(); ?>
    </div>
    <?php
}


////////////////////////////////////////////////////////////////////////////////
//   U I    E l e m e n t s
////////////////////////////////////////////////////////////////////////////////

/** Help   -   Drop Down Menu  -  T a b  */
function wpbc_bs_dropdown_menu_help() {

    wpbc_bs_dropdown_menu( array(
                                        'title' => __( 'Help', 'booking' )
                                      , 'font_icon' => 'glyphicon glyphicon-question-sign'
                                      , 'position' => 'right'
                                      , 'items' => array(
                                               array( 'type' => 'link', 'title' => "What's New"/*__('Get Started')*/, 'url' => esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) ) )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('Help', 'booking'), 'url' => 'https://wpbookingcalendar.com/help/' )
                                             , array( 'type' => 'link', 'title' => __('FAQ', 'booking'), 'url' => 'https://wpbookingcalendar.com/faq/' )
                                             , array( 'type' => 'link', 'title' => __('Technical Support', 'booking'), 'url' => 'https://wpbookingcalendar.com/support/' )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('About Booking Calendar', 'booking')
																		// , 'url' => wpbc_up_link()
																		, 'url' =>  esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about-premium' ), 'index.php' ) ) )
                                                                        , 'attr' => array(
                                                                            //  'target' => '_blank'
                                                                            'style' => 'font-weight: 600;font-size: 1em;'
                                                                        )
                                                    )
                                        )
                        ) );
}


/** View Mode   -   B u t t o n */
function wpbc_toolbar_btn__view_mode() {

    $selected_view_mode = $_REQUEST['view_mode'];

    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array('view_mode', 'wh_booking_id', 'page_num' ) );

    $params = array();
    $params['btn_vm_listing'] = array(
                                  'title' => ''
                                , 'hint' => array( 'title' => __('Booking Listing' ,'booking') , 'position' => 'top' )
                                , 'selected' => ( $selected_view_mode == 'vm_listing' ) ? true : false
                                , 'link' => $bk_admin_url . '&view_mode=vm_listing'
                                , 'icon' => ''
                                , 'font_icon' => 'glyphicon glyphicon-align-justify'
                            );


    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ) , array()              // Exclude Value of this parameter
                                            , array( 'page', 'tab', 'tab_cvm', 'wh_booking_type', 'scroll_start_date', 'scroll_month', 'view_days_num'
                                                     , 'wh_trash'               //FixIn: 6.1.1.10
                                                ) // Only  this parameters
                                           );
    $params['btn_vm_calendar'] = array(
                                  'title' => ''
                                , 'hint' => array( 'title' => __('Calendar Overview' ,'booking') , 'position' => 'bottom' )
                                , 'selected' => ( $selected_view_mode == 'vm_calendar' ) ? true : false
                                , 'link' => $bk_admin_url . '&view_mode=vm_calendar'
                                , 'icon' => ''
                                , 'font_icon' => 'glyphicon glyphicon-calendar'
                            );

    ?><div style="position:absolute;"><?php

        wpbc_bs_vertical_buttons_group( $params );

    ?></div><?php
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Filter    B u t t o n s
////////////////////////////////////////////////////////////////////////////////

/** Apply | Reset   -   B u t t o n s */
function wpbc_toolbar_btn__apply_reset(){

    $params = array(
                      'label_for' => 'wpbc_refresh'                                 // "For" parameter  of button group element
                    , 'label' => ''//&nbsp;'//__('Refresh listing', 'booking')      // Label above the button group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => __('Apply', 'booking')     // Title of the button
                                            , 'hint' => array( 'title' => __('Refresh booking listing' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => 'booking_filters_form.submit();'                // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-primary'           // button-secondary  | button-primary
                                            , 'style' => ''                         // Any CSS class here
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-refresh'  // glyphicon-white
                                            , 'icon_position' => 'right'            // Position  of icon relative to Text: left | right
                                            , 'attr' => array()
                                            , 'mobile_show_text' => true            // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        )
                                        , array(
                                            'type' => 'button'
                                            , 'title' => ''
                                            , 'hint' => array( 'title' => __('Reset filter to default values' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => wpbc_get_bookings_url( true, false ) . '&view_mode=vm_listing'
                                            , 'action' => ''
                                            , 'class' => ''
                                            , 'style' => ''  //
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-remove'
                                            , 'icon_position' => 'left'
                                            , 'attr' => array()
                                            , 'mobile_show_text' => false        // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        )
                                    )
    );
    wpbc_bs_button_group( $params );
}


/** Approved | Pending   -   F i l t e r */
function wpbc_toolbar_filter__approve_pending(){

    $params = array(
                    'id' => 'wh_approved'
                    , 'options' => array (
                                            __('Pending', 'booking') => '0',
                                            __('Approved', 'booking') => '1',
                                            'divider0' => 'divider',
                                            __('Any', 'booking') => ''
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_approved' ] ) ) ? esc_attr( $_REQUEST[ 'wh_approved' ] ) : ''
                    , 'label' => ''//__('Status', 'booking') . ':'
                    , 'title' => __('Bookings', 'booking')
                );

    wpbc_bs_dropdown_list( $params );
}


/** Booked Dates   -   F i l t e r */
function wpbc_toolbar_filter__booked_dates(){

    $dates_interval = array(
                                1 => '1' . ' ' . __('day' ,'booking')
                              , 2 => '2' . ' ' . __('days' ,'booking')
                              , 3 => '3' . ' ' . __('days' ,'booking')
                              , 4 => '4' . ' ' . __('days' ,'booking')
                              , 5 => '5' . ' ' . __('days' ,'booking')
                              , 6 => '6' . ' ' . __('days' ,'booking')
                              , 7 => '1' . ' ' . __('week' ,'booking')
                              , 14 => '2' . ' ' . __('weeks' ,'booking')
                              , 30 => '1' . ' ' . __('month' ,'booking')
                              , 60 => '2' . ' ' . __('months' ,'booking')
                              , 90 => '3' . ' ' . __('months' ,'booking')
                              , 183 => '6' . ' ' . __('months' ,'booking')
                              , 365 => '1' . ' ' . __('Year' ,'booking')
                        );
    $params = array(
                      'id'  => 'wh_booking_date'
                    , 'id2' => 'wh_booking_date2'
                    , 'default' =>  ( isset( $_REQUEST[ 'wh_booking_date' ] ) ) ? esc_attr( $_REQUEST[ 'wh_booking_date' ] ) : ''
                    , 'default2' => ( isset( $_REQUEST[ 'wh_booking_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_booking_date2' ] ) : ''
                    , 'hint' => array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
                    , 'label' => ''//__('Booked Dates', 'booking') . ':'
                    , 'title' => __('Dates', 'booking')
                    , 'options' => array (
                                              __('Current dates' ,'booking')    => '0'
                                            , __('Today' ,'booking')            => '1'
                                            , __('Previous dates' ,'booking')   => '2'
                                            , __('All dates' ,'booking')        => '3'
                                            , 'divider1' => 'divider'
                                            , __('Today check in/out' ,'booking')   => '9'
                                            , __('Check In - Tomorrow' ,'booking')  => '7'
                                            , __('Check Out - Tomorrow' ,'booking') => '8'
                                            , 'divider2' => 'divider'
                                            , 'next' => array(
                                                                array(
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Next' ,'booking')
                                                                      , 'id' => 'wh_booking_datedays_interval1'
                                                                      , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                      , 'legend' => ''                    // aria-label parameter
                                                                      , 'value' => '4'                     // Some Value from optins array that selected by default
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                        && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '4' ) ) ? true : false
                                                                      )
                                                                , array(
                                                                        'type' => 'select'
                                                                      , 'attr' => array()
                                                                      , 'name' => 'wh_booking_datenext'
                                                                      , 'id' => 'wh_booking_datenext'
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_booking_datenext'] ) ? esc_attr( $_REQUEST[ 'wh_booking_datenext'] ) : ''
                                                                      )
                                                             )
                                            , 'prior' => array(
                                                                array(
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Prior' ,'booking')
                                                                      , 'id' => 'wh_booking_datedays_interval2'
                                                                      , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                      , 'legend' => ''                    // aria-label parameter
                                                                      , 'value' => '5'                     // Some Value from optins array that selected by default
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                        && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '5' ) ) ? true : false
                                                                      )
                                                                , array(
                                                                        'type' => 'select'
                                                                      , 'attr' => array()
                                                                      , 'name' => 'wh_booking_dateprior'
                                                                      , 'id' => 'wh_booking_dateprior'
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_booking_dateprior'] ) ? esc_attr( $_REQUEST[ 'wh_booking_dateprior'] ) : ''
                                                                      )
                                                             )
                                            , 'fixed' => array( array(  'type' => 'group', 'class' => 'input-group text-group'),
                                                                array(
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Dates' ,'booking')
                                                                      , 'id' => 'wh_booking_datedays_interval3'
                                                                      , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                      , 'legend' => ''                    // aria-label parameter
                                                                      , 'value' => '6'                     // Some Value from optins array that selected by default
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                        && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '6' ) ) ? true : false
                                                                      )
                                                                , array(
                                                                        'type'          => 'text'
                                                                        , 'id'          => 'wh_booking_datefixeddates'
                                                                        , 'name'        => 'wh_booking_datefixeddates'
                                                                        , 'label'       => __('Check-in' ,'booking') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'wpdevbk-filters-section-calendar'           // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )
                                                                        , 'attr'        => array()
                                                                        , 'value' => isset( $_REQUEST[ 'wh_booking_datefixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_booking_datefixeddates'] ) : ''
                                                                      )
                                                                , array(
                                                                        'type'          => 'text'
                                                                        , 'id'          => 'wh_booking_date2fixeddates'
                                                                        , 'name'        => 'wh_booking_date2fixeddates'
                                                                        , 'label'       => __('Check-out' ,'booking') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'wpdevbk-filters-section-calendar'                  // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )
                                                                        , 'attr'        => array()
                                                                        , 'value' => isset( $_REQUEST[ 'wh_booking_date2fixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_booking_date2fixeddates'] ) : ''
                                                                      )
                                                             )
                                            , 'divider3' => 'divider'
                                            , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ),
                                                                array(
                                                                          'type' => 'button'
                                                                        , 'title' => __('Apply' ,'booking') // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        , 'action' => "wpbc_show_selected_in_dropdown__radio_select_option("
                                                                                            . "  'wh_booking_date'"
                                                                                            . ", 'wh_booking_date2'"
                                                                                            . ", 'wh_booking_datedays_interval_Radios' "
                                                                                        . ");"
                                                                        , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()

                                                                      )
                                                                , array(
                                                                          'type' => 'button'
                                                                        , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                        , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()
                                                                      )
                                                                )
                                        )
                );

    wpbc_bs_dropdown_list( $params );

}


/** Sort   -   F i l t e r */
function wpbc_toolbar_filter__sort(){

        $selectors = array(
                            __('ID' ,'booking') . '&nbsp;<i class="glyphicon glyphicon-arrow-up "></i>' => '',
                            __('Dates' ,'booking') . '&nbsp;<i class="glyphicon glyphicon-arrow-up "></i>' => 'sort_date',
                            'divider0'=>'divider',
                            __('ID' ,'booking') . '&nbsp;<i class="glyphicon glyphicon-arrow-down "></i>' => 'booking_id_asc',
                            __('Dates' ,'booking') . '&nbsp;<i class="glyphicon glyphicon-arrow-down "></i>' => 'sort_date_asc'
                        );

        $selectors = apply_bk_filter('bk_filter_sort_options', $selectors);
        $default_value = get_bk_option( 'booking_sort_order');

        $params = array(                                                        // Pending, Active, Suspended, Terminated, Cancelled, Fraud
                        'id' => 'or_sort'
                        , 'options' => $selectors
                        , 'default' => ( isset( $_REQUEST[ 'or_sort' ] ) ) ? esc_attr( $_REQUEST[ 'or_sort' ] ) : $default_value
                        , 'label' => ''//__('Status', 'booking') . ':'
                        , 'title' => __('Order by', 'booking')
                    );

        wpbc_bs_dropdown_list( $params );
}


/** Trash   -   F i l t e r */
function wpbc_toolbar_filter__trash(){                                          //FixIn: 6.1.1.10


    $params = array(
                    'id' => 'wh_trash'
                    , 'options' => array (
                                            __('Exist', 'booking') => '0',
                                            __('In Trash / Rejected', 'booking') => 'trash',
                                            'divider0' => 'divider',
                                            __('Any', 'booking') => 'any'
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_trash' ] ) ) ? esc_attr( $_REQUEST[ 'wh_trash' ] ) : ''
                    , 'label' => ''//__('Status', 'booking') . ':'
                    , 'title' => __('Bookings', 'booking')
                );

    wpbc_bs_dropdown_list( $params );

}


/** New Bookings   -   F i l t e r */
function wpbc_toolbar_filter__new_bookings() {

    $params = array(
                    'id' => 'wh_is_new'
                    , 'options' => array (
                                            __('All bookings', 'booking') => '',
                                            __('New bookings', 'booking') => '1'
                                         )
                    , 'default' => ( isset( $_REQUEST[ 'wh_is_new' ] ) ) ? esc_attr( $_REQUEST[ 'wh_is_new' ] ) : ''
                    , 'label' => ''//__('Status', 'booking') . ':'
                    , 'title' => __('Show', 'booking')
                );

    wpbc_bs_dropdown_list( $params );
}


/** Creation Date   -   F i l t e r */
function wpbc_toolbar_filter__creation_date(){

    $dates_interval = array(
                                1 => '1' . ' ' . __('day' ,'booking')
                              , 2 => '2' . ' ' . __('days' ,'booking')
                              , 3 => '3' . ' ' . __('days' ,'booking')
                              , 4 => '4' . ' ' . __('days' ,'booking')
                              , 5 => '5' . ' ' . __('days' ,'booking')
                              , 6 => '6' . ' ' . __('days' ,'booking')
                              , 7 => '1' . ' ' . __('week' ,'booking')
                              , 14 => '2' . ' ' . __('weeks' ,'booking')
                              , 30 => '1' . ' ' . __('month' ,'booking')
                              , 60 => '2' . ' ' . __('months' ,'booking')
                              , 90 => '3' . ' ' . __('months' ,'booking')
                              , 183 => '6' . ' ' . __('months' ,'booking')
                              , 365 => '1' . ' ' . __('Year' ,'booking')
                        );

    $params = array(
                      'id'  => 'wh_modification_date'
                    , 'id2' => 'wh_modification_date2'
                    , 'default' =>  ( isset( $_REQUEST[ 'wh_modification_date' ] ) )  ? esc_attr( $_REQUEST[ 'wh_modification_date' ] ) : '3'
                    , 'default2' => ( isset( $_REQUEST[ 'wh_modification_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_modification_date2' ] ) : ''
                    , 'hint' => array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
                    , 'label' => ''//__('Booking Creation Date', 'booking') . ':'
                    , 'title' => __('Creation', 'booking')
                    , 'options' => array (
                                              __('Today' ,'booking')            => '1'
                                            , __('All dates' ,'booking')        => '3'
                                            , 'divider1' => 'divider'
                                            , 'prior' => array(
                                                                array(
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Prior' ,'booking')
                                                                      , 'id' => 'wh_modification_datedays_interval2'
                                                                      , 'name' => 'wh_modification_datedays_interval_Radios'
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                      , 'legend' => ''                    // aria-label parameter
                                                                      , 'value' => '5'                     // Some Value from optins array that selected by default
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_modification_datedays_interval_Radios'] )
                                                                                        && ( $_REQUEST[ 'wh_modification_datedays_interval_Radios'] == '5' ) ) ? true : false
                                                                      )
                                                                , array(
                                                                        'type' => 'select'
                                                                      , 'attr' => array()
                                                                      , 'name' => 'wh_modification_dateprior'
                                                                      , 'id' => 'wh_modification_dateprior'
                                                                      , 'options' => $dates_interval
                                                                      , 'value' => isset( $_REQUEST[ 'wh_modification_dateprior'] ) ? esc_attr( $_REQUEST[ 'wh_modification_dateprior'] ) : ''
                                                                      )
                                                             )
                                            , 'fixed' => array( array(  'type' => 'group', 'class' => 'input-group text-group'),
                                                                array(
                                                                        'type' => 'radio'
                                                                      , 'label' => __('Dates' ,'booking')
                                                                      , 'id' => 'wh_modification_datedays_interval3'
                                                                      , 'name' => 'wh_modification_datedays_interval_Radios'
                                                                      , 'style' => ''                     // CSS of select element
                                                                      , 'class' => ''                     // CSS Class of select element
                                                                      , 'disabled' => false
                                                                      , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                      , 'legend' => ''                    // aria-label parameter
                                                                      , 'value' => '6'                     // Some Value from optins array that selected by default
                                                                      , 'selected' => ( isset($_REQUEST[ 'wh_modification_datedays_interval_Radios'] )
                                                                                        && ( $_REQUEST[ 'wh_modification_datedays_interval_Radios'] == '6' ) ) ? true : false
                                                                      )
                                                                , array(
                                                                        'type'          => 'text'
                                                                        , 'id'          => 'wh_modification_datefixeddates'
                                                                        , 'name'        => 'wh_modification_datefixeddates'
                                                                        , 'label'       => __('Check-in' ,'booking') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'wpdevbk-filters-section-calendar'           // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )
                                                                        , 'attr'        => array()
                                                                        , 'value' => isset( $_REQUEST[ 'wh_modification_datefixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_modification_datefixeddates'] ) : ''
                                                                      )
                                                                , array(
                                                                        'type'          => 'text'
                                                                        , 'id'          => 'wh_modification_date2fixeddates'
                                                                        , 'name'        => 'wh_modification_date2fixeddates'
                                                                        , 'label'       => __('Check-out' ,'booking') . ':'
                                                                        , 'disabled'    => false
                                                                        , 'class'       => 'wpdevbk-filters-section-calendar'                  // This class add datepicker
                                                                        , 'style'       => ''
                                                                        , 'placeholder' => date( 'Y-m-d' )
                                                                        , 'attr'        => array()
                                                                        , 'value' => isset( $_REQUEST[ 'wh_modification_date2fixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_modification_date2fixeddates'] ) : ''
                                                                      )
                                                             )
                                            , 'divider3' => 'divider'
                                            , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ),
                                                                array(
                                                                          'type' => 'button'
                                                                        , 'title' => __('Apply' ,'booking') // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        , 'action' => "wpbc_show_selected_in_dropdown__radio_select_option("
                                                                                            . "  'wh_modification_date'"
                                                                                            . ", 'wh_modification_date2'"
                                                                                            . ", 'wh_modification_datedays_interval_Radios' "
                                                                                        . ");"
                                                                        , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()

                                                                      )
                                                                , array(
                                                                          'type' => 'button'
                                                                        , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                        , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                        , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                        //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                        , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                        , 'icon' => ''
                                                                        , 'font_icon' => ''
                                                                        , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                        , 'style' => ''                     // Any CSS class here
                                                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                        , 'attr' => array()
                                                                      )
                                                                )
                                        )
                );

    wpbc_bs_dropdown_list( $params );
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Actions    B u t t o n s
////////////////////////////////////////////////////////////////////////////////

/** Approve | Reject   -   B u t t o n s */
function wpbc_toolbar_btn__approve_reject( $user_bk_id ) {

    $params = array(
                      'label_for' => 'actions'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Actions:', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => __('Approve', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Approve selected bookings' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "approve_unapprove_booking( get_selected_bookings_id_in_booking_listing(), 1, " .
                                                            $user_bk_id . ", '" . wpbc_get_booking_locale() . "' , 1);"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-primary'                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-ok-circle glyphicon-white'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => __('Pending', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Set selected bookings as pending' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "if ( wpbc_are_you_sure('" . esc_js(__('Do you really want to set booking as pending ?' ,'booking')) . "') )
                                                            approve_unapprove_booking( get_selected_bookings_id_in_booking_listing() ,
                                                                    0, " . $user_bk_id . ", '" . wpbc_get_booking_locale() . "' , 1);"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-ban-circle'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );
    wpbc_bs_button_group( $params );
}


/** Delete | Reason   -   B u t t o n s */
function wpbc_toolbar_btn__delete_reason( $user_bk_id ) {

    $params = array(
                  'label_for' => 'denyreason'                               // "For" parameter  of label element
                , 'label' => ''                                             // Label above the input group
                , 'style' => ''                                             // CSS Style of entire div element
                , 'items' => array(
                                    array(                                      //FixIn: 6.1.1.10
                                          'type' => 'button'
                                        , 'title' => __('Trash / Reject', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Reject booking - move selected bookings to trash' ,'booking') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( wpbc_are_you_sure('" . esc_js( __('Do you really want to do this ?' ,'booking') ) . "') )
                                                         trash__restore_booking( 1, get_selected_bookings_id_in_booking_listing() , "
                                                        . $user_bk_id . ", '"
                                                        . wpbc_get_booking_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-trash'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(
                                          'type' => 'button'
                                        , 'title' => __('Restore', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Restore selected bookings' ,'booking') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( wpbc_are_you_sure('" . esc_js( __('Do you really want to do this ?' ,'booking') ) . "') )
                                                         trash__restore_booking( 0, get_selected_bookings_id_in_booking_listing() , "
                                                        . $user_bk_id . ", '"
                                                        . wpbc_get_booking_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-repeat'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(
                                          'type' => 'button'
                                        , 'title' => __('Delete', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                        , 'hint' => array( 'title' => __('Delete selected bookings' ,'booking') , 'position' => 'top' ) // Hint
                                        , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                        , 'action' => "if ( wpbc_are_you_sure('" . esc_js( __('Do you really want to delete selected booking(s) ?' ,'booking') ) . "') )
                                                        delete_booking( get_selected_bookings_id_in_booking_listing() , "
                                                        . $user_bk_id . ", '"
                                                        . wpbc_get_booking_locale() . "' , 1  );"                // Some JavaScript to execure, for example run  the function
                                        , 'class' => ''                 // button-secondary  | button-primary
                                        , 'icon' => ''
                                        , 'font_icon' => 'glyphicon glyphicon-remove'
                                        , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                        , 'style' => ''                 // Any CSS class here
                                        , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'attr' => array()
                                    )
                                    , array(
                                        'type' => 'text'
                                        , 'id' => 'denyreason'            // HTML ID  of element
                                        , 'value' => ''                 // Value of Text field
                                        , 'placeholder' => __('Reason of cancellation', 'booking')
                                        , 'style' => ''                 // CSS of select element
                                        , 'class' => ''                 // CSS Class of select element
                                        , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                                    )
                )
          );
    ?><div class="control-group wpbc-no-padding wpbc-sm-100" ><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


//FixIn: 8.5.2.24
/**
 * Empty Trash
 *
 * @param $user_bk_id
 */
function  wpbc_toolbar_btn__empty_trash( $user_bk_id ) {

        $params = array(
                          'label_for' => 'actions_empty_trash'                              // "For" parameter  of button group element
                        , 'label' => '' //__('Actions:', 'booking')                  // Label above the button group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                            array(
                                                  'type' => 'button'
                                                , 'title' => __('Empty Trash', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                                , 'hint' => array( 'title' => __('Empty Trash' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => 'javascript:void(0)'        // Direct link or skip  it
												, 'action' => "if ( wpbc_are_you_sure('" . esc_js( __('Do you really want to do this ?' ,'booking') ) . "') )
																 wpbc_empty_trash( " . $user_bk_id .
															  		", '" . wpbc_get_booking_locale() . "' );"                // Some JavaScript to execure, for example run  the function

                                                , 'class' => ''                        // button-secondary  | button-primary
                                                , 'icon' => ''
                                                , 'font_icon' => 'glyphicon glyphicon-remove'
                                                , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                                , 'style' => ''                 // Any CSS class here
                                                , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                        )
        );
        wpbc_bs_button_group( $params );
}


/** Read All   -   B u t t o n s */
function wpbc_toolbar_btn__read_all( $user_bk_id ) {

    $params = array(
                      'label_for' => 'actions'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Actions:', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => __('Read All', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Mark as read all bookings' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "mark_read_booking( 'all', 0, " . $user_bk_id . ", '" . wpbc_get_booking_locale() . "' );"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-eye-close'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => __('Read', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Mark as read selected bookings' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "mark_read_booking( get_selected_bookings_id_in_booking_listing(), 0, "
                                                            . $user_bk_id . ", '" . wpbc_get_booking_locale() . "' );"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-eye-close'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => __('Unread', 'booking') . '&nbsp;&nbsp;'    // Title of the button
                                            , 'hint' => array( 'title' => __('Mark as Unread selected bookings' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => "mark_read_booking( get_selected_bookings_id_in_booking_listing() , 1, "
                                                            . $user_bk_id . ", '" . wpbc_get_booking_locale() . "' );"                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-eye-open'
                                            , 'icon_position' => 'right'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => true       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );
    wpbc_bs_button_group( $params );
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar   Actions    B u t t o n s   -   T i m e l i n e   //////////////////
////////////////////////////////////////////////////////////////////////////////


/** View Mode Timeline   -   B u t t o n s */
function wpbc_toolbar_btn__timeline_view_mode() {

    if  ((isset($_REQUEST['wh_booking_type'])) && ( strpos($_REQUEST['wh_booking_type'], ',') !== false ) )
            $is_show_resources_matrix = true;
    else    $is_show_resources_matrix = false;


    if ( isset( $_REQUEST['view_days_num'] ) )
         $view_days_num = intval( $_REQUEST['view_days_num'] );
    else $view_days_num = get_bk_option( 'booking_view_days_num');


    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array('view_days_num') );


    if ( ! $is_show_resources_matrix ) {

        $params = array(
                          'label_for' => 'calendar_overview_number_of_days_to_show'                              // "For" parameter  of button group element
                        , 'label' => '' //__('Calendar view mode', 'booking')                  // Label above the button group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                            array(
                                                  'type' => 'button'
                                                , 'title' => __('Day', 'booking') . '&nbsp;&nbsp;'        						// Title of the button		//FixIn: Flex TimeLine 1.0
                                                , 'hint' => array( 'title' => __('Show day' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'font_icon' => 'glyphicon glyphicon-stop'
//                                                , 'title' => __('Month', 'booking') . '&nbsp;&nbsp;'        // Title of the button
//                                                , 'hint' => array( 'title' => __('Show month' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=30'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_30'                   // button-secondary  | button-primary
                                                , 'icon' => ''
//                                                , 'font_icon' => 'glyphicon glyphicon-align-justify'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                            , array(
                                                  'type' => 'button'
                                                , 'title' => __('Week', 'booking') . '&nbsp;&nbsp;'        							// Title of the button		//FixIn: Flex TimeLine 1.0
                                                , 'hint' => array( 'title' => __('Show week' ,'booking') , 'position' => 'top' ) 	// Hint
                                                , 'font_icon' => 'glyphicon glyphicon-th-large'
//                                                , 'title' => __('3 Months', 'booking') . '&nbsp;&nbsp;'        // Title of the button
//                                                , 'hint' => array( 'title' => __('Show 3 months' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=90'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_90'                   // button-secondary  | button-primary
                                                , 'icon' => ''
//                                                , 'font_icon' => 'glyphicon glyphicon-th-list'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                            , array(
                                                  'type' => 'button'
                                                , 'title' => __('Month', 'booking') . '&nbsp;&nbsp;'        						// Title of the button
                                                , 'hint' => array( 'title' => __('Show month' ,'booking') , 'position' => 'top' ) 	// Hint
                                                , 'font_icon' => 'glyphicon glyphicon-th'
//                                                , 'title' => __('Year', 'booking') . '&nbsp;&nbsp;'        // Title of the button
//                                                , 'hint' => array( 'title' => __('Show year' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=365'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_365'                  // button-secondary  | button-primary
                                                , 'icon' => ''
//                                                , 'font_icon' => 'glyphicon glyphicon-th'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                        )
        );
    } else {

        $params = array(
                          'label_for' => 'calendar_overview_number_of_days_to_show'                              // "For" parameter  of button group element
                        , 'label' => '' //__('Calendar view mode:', 'booking')                  // Label above the button group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                            array(
                                                  'type' => 'button'
                                                , 'title' => __('Day', 'booking') . '&nbsp;&nbsp;'        // Title of the button
                                                , 'hint' => array( 'title' => __('Show day' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=1'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_1'                   // button-secondary  | button-primary
                                                , 'icon' => ''
                                                , 'font_icon' => 'glyphicon glyphicon-stop'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                            , array(
                                                  'type' => 'button'
                                                , 'title' => __('Week', 'booking') . '&nbsp;&nbsp;'        // Title of the button
                                                , 'hint' => array( 'title' => __('Show week' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=7'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_7'                   // button-secondary  | button-primary
                                                , 'icon' => ''
                                                , 'font_icon' => 'glyphicon glyphicon-th-large'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                            , array(
                                                  'type' => 'button'
                                                , 'title' => __('Month', 'booking') . '&nbsp;&nbsp;'        // Title of the button
                                                , 'hint' => array( 'title' => __('Show month' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=30'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_30'                  // button-secondary  | button-primary
                                                , 'icon' => ''
                                                , 'font_icon' => 'glyphicon glyphicon-th'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                            , array(
                                                  'type' => 'button'
                                                , 'title' => __('2 Months', 'booking') . '&nbsp;&nbsp;'        // Title of the button
                                                , 'hint' => array( 'title' => __('Show 2 months' ,'booking') , 'position' => 'top' ) // Hint
                                                , 'link' => $bk_admin_url . '&view_days_num=60'             // Direct link or skip  it
                                                , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                                , 'class' => 'button-secondary btn_dn_60'                  // button-secondary  | button-primary
                                                , 'icon' => ''
                                                , 'font_icon' => 'glyphicon glyphicon-th-list'
                                                , 'icon_position' => 'right'                                // Position  of icon relative to Text: left | right
                                                , 'style' => ''                                             // Any CSS class here
                                                , 'mobile_show_text' => true                                // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                , 'attr' => array()
                                            )
                                        )
        );

    }
    wpbc_bs_button_group( $params );

    //FixIn: 7.0.1.10
    ?><script type="text/javascript">
        if ( 'function' === typeof( jQuery('#calendar_overview_number_of_days_to_show .button').button ) ) {
                jQuery('#calendar_overview_number_of_days_to_show .button').button();
                jQuery('#calendar_overview_number_of_days_to_show .button.btn_dn_<?php echo $view_days_num; ?>').button('toggle');
        } else {
            console.log('Warning! Booking Calendar. Its seems that  you have deactivated loading of Bootstrap JS files at Booking Settings General page in Advanced section.')
        }
    </script><?php
}


/** Navigation Timeline   -   B u t t o n s */
function wpbc_toolbar_btn__timeline_navigation() {

    if  ((isset($_REQUEST['wh_booking_type'])) && ( strpos($_REQUEST['wh_booking_type'], ',') !== false ) )
            $is_show_resources_matrix = true;
    else    $is_show_resources_matrix = false;


    if ( isset( $_REQUEST['view_days_num'] ) )
         $view_days_num = intval( $_REQUEST['view_days_num'] );
    else $view_days_num = get_bk_option( 'booking_view_days_num');


    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array('scroll_month', 'scroll_day') );
//debuge($_REQUEST, $bk_admin_url);

    // Get Data For buttons
    if (! $is_show_resources_matrix) {

        switch ($view_days_num) {
            case '90':
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                        '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+7 ),
                                        '&scroll_day='.intval($scroll_day+4*7) );
                $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                         __('Previous week' ,'booking'),
                                         __('Current week' ,'booking'),
                                         __('Next week' ,'booking'),
                                         __('Next 4 weeks' ,'booking') );
                break;
            case '30':
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                        '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+7 ),
                                        '&scroll_day='.intval($scroll_day+4*7) );
                $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                         __('Previous week' ,'booking'),
                                         __('Current week' ,'booking'),
                                         __('Next week' ,'booking'),
                                         __('Next 4 weeks' ,'booking') );
                break;
            default:  // 365
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;
        }
    } else { // Matrix

        switch ($view_days_num) {
            case '1': //Day
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day='.intval($scroll_day-1),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+1 ),
                                        '&scroll_day='.intval($scroll_day+7) );
                $scroll_titles = array(  __('Previous 7 days' ,'booking'),
                                         __('Previous day' ,'booking'),
                                         __('Current day' ,'booking'),
                                         __('Next day' ,'booking'),
                                         __('Next 7 days' ,'booking') );
                break;

            case '7': //Week
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                        '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+7 ),
                                        '&scroll_day='.intval($scroll_day+4*7) );
                $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                         __('Previous week' ,'booking'),
                                         __('Current week' ,'booking'),
                                         __('Next week' ,'booking'),
                                         __('Next 4 weeks' ,'booking') );
                break;

            case '30':
            case '60':
            case '90': //3 months
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;

            default:  // 30, 60, 90...
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;
        }
    }


    $params = array(
                      'label_for' => 'calendar_overview_navigation'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Calendar Navigation', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[0] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[0]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-backward'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[1] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[1]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-chevron-left'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'dropdown'
                                            , 'id' => 'timeline_navigation_date'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => __('Custom' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'class' => 'button-secondary'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-screenshot'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                            , 'options' => array(
                                                      $scroll_titles[2] => "window.location.href='"
                                                                            . wpbc_get_params_in_url( wpbc_get_bookings_url( false )
                                                                                                    , array('scroll_month', 'scroll_day', 'scroll_start_date') )
                                                                            . $scroll_params[2] . "'"
                                                    , 'divider1' => 'divider'
                                                    , 'custom' => array( array(  'type' => 'group', 'class' => 'input-group text-group')
                                                                        , array(
                                                                                'type'          => 'text'
                                                                                , 'id'          => 'calendar_overview_navigation_currentdate'
                                                                                , 'name'        => 'calendar_overview_navigation_currentdate'
                                                                                , 'label'       => __('Start Date' ,'booking') . ':'
                                                                                , 'disabled'    => false
                                                                                , 'class'       => 'wpdevbk-filters-section-calendar'
                                                                                , 'style'       => ''
                                                                                , 'placeholder' => date('Y-m-d')
                                                                                , 'attr'        => array()
                                                                                , 'value' => ''
                                                                              )
                                                                        )
                                                    , 'divider2' => 'divider'
                                                    , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ),
                                                                        array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Apply' ,'booking') // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                , 'action' => "jQuery('#calendar_overview_navigation_container').hide();
                                                                                               window.location.href='"
                                                                                               . wpbc_get_params_in_url( wpbc_get_bookings_url( false )
                                                                                                                        , array('scroll_month', 'scroll_day', 'scroll_start_date') )
                                                                                               . "&scroll_start_date=' + jQuery('#calendar_overview_navigation_currentdate').val();"

                                                                                , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => ''                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()

                                                                              )
                                                                        , array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                                , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => ''                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()
                                                                              )
                                                                        )
                                               )
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[3] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[3]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-chevron-right'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[4] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[4]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'glyphicon glyphicon-forward'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );

    wpbc_bs_button_group( $params );
}



////////////////////////////////////////////////////////////////////////////////
// Toolbar   Options    B u t t o n s   -   Add New Booking   //////////////////
////////////////////////////////////////////////////////////////////////////////

/** Genereate URL based on GET parameters */
function wpbc_get_new_booking_url__base( $skip_parameters = array() ) {

    $link_base = wpbc_get_new_booking_url( true, false );

    $link_params = array();
    if ( ( isset( $_GET['booking_type'] ) ) && ( $_GET['booking_type'] > 0 ) )      $link_params['booking_type'] = $_GET['booking_type'];
    if ( isset( $_GET['booking_hash'] ) )                   $link_params['booking_hash'] = $_GET['booking_hash'];
    if ( isset( $_GET['parent_res'] ) )                     $link_params['parent_res'] = $_GET['parent_res'];
    if ( isset( $_GET['booking_form'] ) )                   $link_params['booking_form'] = $_GET['booking_form'];
    if ( isset( $_GET['calendar_months_count'] ) )          $link_params['calendar_months_count'] = intval( $_GET['calendar_months_count'] );
    if ( isset( $_GET['calendar_months_num_in_1_row'] ) )   $link_params['calendar_months_num_in_1_row'] = intval( $_GET['calendar_months_num_in_1_row'] );


    foreach ( $link_params as $key => $value ) {

        if ( ! in_array( $key, $skip_parameters) ) {
            $link_base .= '&' . $key . '=' . $value;
        }
    }

    return $link_base;
}

/** Selection Number of visible months */
function wpbc_toolbar_btn__calendar_months_number_selection( $user_calendar_options = array() ) {

    $text_label = __('Visible months' ,'booking') .':' ;

    $form_options = array(  1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );

    $parameter_name = 'calendar_months_count';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 1;

    $link_base = wpbc_get_new_booking_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;

    $on_change = '';    //'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $on_change
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding" style="width:auto;"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


/** Selection Number of calendar months in one row */
function wpbc_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options = array() ) {

    $text_label = __('Number of months in one row' ,'booking') . ':';
    $form_options = array( 0 => __('All', 'booking'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );

    $parameter_name = 'calendar_months_num_in_1_row';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 0;

    $link_base = wpbc_get_new_booking_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;

    $on_change = ''; // 'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $on_change
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


function wpbc_toolbar_btn__calendar_width( $user_calendar_options = array() ){

    $text_label     = __('Calendar width' ,'booking') . ':';
    $parameter_name = 'calendar_width';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';

    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';

    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'text'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '100%'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}

function wpbc_toolbar_btn__calendar_cell_height( $user_calendar_options = array() ){

    $text_label     = __('Calendar cell height' ,'booking') . ':';
    $parameter_name = 'calendar_cell_height';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';

    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';

    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'text'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '39px'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )

                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


/** Add New Booking   Button*/
function wpbc_toolbar_btn__calendar_options_save() {

    ?><div class="control-group wpbc-no-padding"><?php
    ?><a
             class="button button-primary "
             href="javascript:void(0)"
             onclick="javascript:var data_params = {};
			data_params.calendar_months_count = jQuery('#calendar_months_count').val();
			data_params.calendar_months_num_in_1_row = jQuery('#calendar_months_num_in_1_row').val();
			data_params.calendar_width = jQuery('#calendar_width').val();
			data_params.calendar_widthunits = jQuery('#calendar_widthunits').val();
			data_params.calendar_cell_height = jQuery('#calendar_cell_height').val();
			data_params.calendar_cell_heightunits = jQuery('#calendar_cell_heightunits').val();
			var ajax_data_params = jQuery.param( data_params );
                        wpbc_save_custom_user_data(<?php echo get_wpbc_current_user_id(); ?>
                                                , '<?php echo 'add_booking_calendar_options'; ?>'
                                                , ajax_data_params
                                                , 1
                                                );"
             ><?php _e('Save Changes' , 'booking') ?></a><?php
    ?></div><?php
}



/** Add New Booking   Button*/
function wpbc_toolbar_btn__add_new_booking() {

    if ( isset( $_GET['booking_type'] ) )
         $bk_type = intval ( $_GET['booking_type'] );
    else $bk_type = 1;

    ?><a
             class="button button-primary wpbc_submit_button"
             href="javascript:void(0)"
             onclick="mybooking_submit(
                                        document.getElementById('booking_form<?php echo $bk_type; ?>' )
                                        , <?php echo $bk_type; ?>
                                        , '<?php echo wpbc_get_booking_locale(); ?>'
                                    );"
             ><?php _e('Add booking' , 'booking') ?></a><?php
}


/** Checkbox - sending emails or not - duplicated button, usually at the bottom of page*/
function wpbc_toolbar_is_send_emails_btn_duplicated() {

    ?>
    <div class="btn-group" style="color:#888;">
        <fieldset>
            <label for="is_send_email_for_new_booking" style="display: inline-block;"  >
                <input  onchange="javascript:document.getElementById('is_send_email_for_pending').checked = this.checked;"
                        type="checkbox"
						<?php if ( get_bk_option('booking_send_emails_off_addbooking') === 'On' ) { } else { //FixIn: 8.4.5.4 ?>
                        checked="CHECKED"
						<?php } ?>
                        id="is_send_email_for_new_booking"
                        name="is_send_email_for_new_booking"
                        class="tooltip_top"
                        style="margin:0 4px 2px;"
                        title="<?php echo esc_js( __( 'Send email notification to customer about this operation', 'booking' ) ); ?>"
                /><?php _e( 'Send email notification to customer about this operation', 'booking' ) ?>
            </label>
        </fieldset>
    </div>
    <script type="text/javascript">

		jQuery(document).ready(function(){
			<?php if ( get_bk_option('booking_send_emails_off_addbooking') === 'On' ) { //FixIn: 8.4.5.4 ?>
				   document.getElementById('is_send_email_for_pending').checked = false;
			<?php } else { ?>
				   document.getElementById('is_send_email_for_pending').checked = true;
			<?php } ?>
		});

        jQuery( '#is_send_email_for_pending' ).on('change', function() {
			//FixIn: 8.7.9.5
			if ( jQuery( '#is_send_email_for_pending' ).is( ':checked' ) ){
				document.getElementById( 'is_send_email_for_new_booking' ).checked = true;
			} else {
				document.getElementById( 'is_send_email_for_new_booking' ).checked = false;
			}
        });
    </script>
    <?php
}


/**
	 * Show Link (button) for adding booking to Google Calendar
 *
 * @param int $booking_id
 * @param array $button_attr
 * @param bool $echo
 * @return string
 */
function wpbc_btn_add_booking_to_google_calendar( $booking_data, $button_attr = array(), $echo = true ) {					//FixIn: 7.1.2.5

//debuge($booking_data);
	if ( ! $echo ) {
            ob_start();
	}

	$defaults = array(
		  'title' => __( 'Add to Google Calendar', 'booking' )
		, 'hint' => __( 'Add to Google Calendar', 'booking' )
		, 'class' => 'button-secondary button'
		, 'is_show_icon' => true
		, 'is_only_url'  => false
	);
	$button_attr = wp_parse_args( $button_attr, $defaults );

	$params = array();
	$params['timezone'] = get_bk_option('booking_gcal_timezone');

	$booking_gcal_events_form_fields = get_bk_option( 'booking_gcal_events_form_fields');
	if ( is_serialized( $booking_gcal_events_form_fields ) )
		$booking_gcal_events_form_fields = unserialize( $booking_gcal_events_form_fields );

	/**
	 * Array
        (
            [title] => text^name
            [description] => textarea^details
            [where] => text^
        )
	 */


	// Fields
	$fields = array( 'title' => '', 'description' => '', 'where' => '' );

	foreach ( $fields as $key_name => $field_value ) {

		if ( ! empty( $booking_gcal_events_form_fields[ $key_name ] ) ) {

			$field_name = explode( '^', $booking_gcal_events_form_fields[ $key_name ] );

			$field_name = $field_name[ ( count( $field_name ) - 1 ) ];                                                  //FixIn: 8.7.7.6

			if (   (! empty($field_name))
				&& (! empty($booking_data['form_data']))
				&& (! empty($booking_data['form_data']['_all_fields_']))
				&& (! empty($booking_data['form_data']['_all_fields_'][ $field_name ]))
				) {

					if ( 'description' === $key_name ) {                                                                //FixIn: 8.1.3.2
						if ( isset( $booking_data['form_show'] ) ) {                                                    //FixIn: 8.7.3.14
							//FixIn: 8.7.11.4
							$fields[ $key_name ] = $booking_data['form_show'];
							$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
							$fields[ $key_name ] = urlencode($fields[ $key_name ]);
							$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
							$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
						}
					} else {
						//FixIn: 8.7.11.4
						$fields[ $key_name ] = $booking_data['form_data']['_all_fields_'][ $field_name ];
						$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
						// Convert here from  usual  symbols to URL symbols https://www.url-encode-decode.com/
//						$fields[ $key_name ] = str_replace(    array( '%','#', '+', '&' )
//							                                 , array( '%25','%23', '%2B', '%26')
//							     							 , $fields[ $key_name ]
//												);
						$fields[ $key_name ] = urlencode($fields[ $key_name ]);
						$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
						$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
					}
			}
		}
	}
//debuge($booking_gcal_events_form_fields, $fields,$booking_data['form_data']);

	// Dates.

	$check_in_timestamp = $check_out_timestamp = '';
	if ( ! empty( $booking_data[ 'dates_short' ] ) ) {


		/* all day events, you can use 20161208/20161209 - note that the old google documentation gets it wrong.
		 * You must use the following date as the end date for a one day all day event,
		 * or +1 day to whatever you want the end date to be.
		 */

		$check_in_timestamp  = strtotime( $booking_data[ 'dates_short' ][ 0 ], current_time( 'timestamp' ) );
		if ( trim( substr( $booking_data[ 'dates_short' ][ 0 ], 11 ) ) == '00:00:00' ) {
			$check_in_timestamp = date( "Ymd", $check_in_timestamp );		// All day
		} else {
			$check_in_timestamp = date( "Ymd\THis", $check_in_timestamp );
			//$check_in_timestamp = date( "Ymd\THis\Z", $check_in_timestamp );
		}

		$check_out_timestamp = strtotime( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], current_time( 'timestamp' ) );
		if ( trim( substr( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], 11 ) ) == '00:00:00' ) {
			$check_out_timestamp = strtotime( '+1 day', $check_out_timestamp );
			$check_out_timestamp = date( "Ymd", $check_out_timestamp );		// All day
		} else {
			$check_out_timestamp = date( "Ymd\THis", $check_out_timestamp );
			//$check_out_timestamp = date( "Ymd\THis\Z", $check_out_timestamp );
		}

	}

	//debuge($check_in_timestamp,$check_out_timestamp, $fields );die;
    //Convert an ISO date/time to a UNIX timestamp
    //function iso_to_ts( $iso ) {
    //    sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );
    //    return mktime( $hour, $minute, $second, $month, $day, $year );
	// 20140127T224000Z
	// date("Ymd\THis\Z", time());

	/**
action:
    action=TEMPLATE
    A default required parameter.

src:
    Example: src=default%40gmail.com
    Format: src=text
    This is not covered by Google help but is an optional parameter in order to add an event to a shared calendar rather than a user's default.

text:
    Example: text=Garden%20Waste%20Collection
    Format: text=text
    This is a required parameter giving the event title.

dates:
    Example: dates=20090621T063000Z/20090621T080000Z (i.e. an event on 21 June 2009 from 7.30am to 9.0am British Summer Time (=GMT+1)).
    Format: dates=YYYYMMDDToHHMMSSZ/YYYYMMDDToHHMMSSZ
    This required parameter gives the start and end dates and times (in Greenwich Mean Time) for the event.

location:
    Example: location=Home
    Format: location=text
    The obvious location field.

trp:
    Example: trp=false
    Format: trp=true/false
    Show event as busy (true) or available (false)

sprop:
    Example: sprop=http%3A%2F%2Fwww.me.org
    Example: sprop=name:Home%20Page
    Format: sprop=website and/or sprop=name:website_name
	 */

//	$link_add2gcal  = 'http://www.google.com/calendar/event?action=TEMPLATE';
//	$link_add2gcal .= '&text=' . $fields['title'];
	//FixIn: 8.7.3.10
	$link_add2gcal = 'https://calendar.google.com/calendar/r/eventedit?';
	$link_add2gcal .= 'text=' . $fields['title'];							//FixIn: 8.7.11.4
	//$link_add2gcal .= '&dates=[start-custom format='Ymd\\THi00\\Z']/[end-custom format='Ymd\\THi00\\Z']';
	$link_add2gcal .= '&dates=' . $check_in_timestamp . '/' . $check_out_timestamp;
	$link_add2gcal .= '&details=' . $fields['description'];                	//FixIn: 8.7.11.4
	$link_add2gcal .= '&location=' . $fields['where'];                     	//FixIn: 8.7.11.4
	$link_add2gcal .= '&trp=false';
	if ( ! empty( $params['timezone'] ) ) {
		$link_add2gcal .= '&ctz=' . str_replace( '%', '%25', $params['timezone'] );                   //FixIn: 8.7.3.10				//TimeZone
	}


	//$link_add2gcal .= '&sprop=';
	//$link_add2gcal .= '&sprop=name:';

	if ( $button_attr['is_only_url'] ) {
		echo $link_add2gcal;
	} else {

		?><a href="<?php echo $link_add2gcal; ?>" target="_blank" rel="nofollow"
			class="tooltip_top <?php echo esc_attr( $button_attr['class'] ) ?>"
			title="<?php echo esc_js( $button_attr['hint'] ); ?>"
		><?php
		if ( $button_attr['is_show_icon'] ) {
		?><i class="glyphicon icon-1x glyphicon-export"></i><?php
		} else {
			echo esc_js( $button_attr['title'] );
		}
		?></a><?php
	}

	if ( ! $echo ) {
		return ob_get_clean();
	}

}
////////////////////////////////////////////////////////////////////////////////
// Toolbar   Other UI elements - General
////////////////////////////////////////////////////////////////////////////////

/**
	 * Selection elements in toolbar UI selectbox
 *
 * @param array $params
 *
 * Exmaple:
            wpbc_toolbar_btn__selection_element( array(
                                                            'name' => 'resources_count'
                                                          , 'title' => __('Resources count' ,'booking') . ':'
                                                          , 'selected' => 1
                                                          , 'options' => array_combine( range(1, 201) ,range(1, 201) )
                                            ) ) ;

 */
function wpbc_toolbar_btn__selection_element( $params ) {

    $defaults = array(
                          'name'        => 'random_' . rand( 1000, 10000 )
                        , 'title'       => __('Total', 'booking') . ':'
                        , 'on_change'   => ''                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = wpbc_get_new_booking_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;
                        , 'options'     => array()
                        , 'selected'    => 0
                    );
    $params = wp_parse_args( $params, $defaults );




    for ( $i = 1; $i < 201; $i++ ) {
        $form_options[ $i ] = $i;
    }

    $params = array(
                      'label_for' => $params['name']                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text'  => $params['title']
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'             // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id'   =>      $params['name']              // HTML ID  of element
                                        , 'name' =>      $params['name']              // HTML ID  of element
                                        , 'options' => $params['options']           // Associated array  of titles and values
                                        , 'value' =>   $params['selected']          // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $params['on_change']
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


////////////////////////////////////////////////////////////////////////////////
// Toolbar     S e a r c h    F o r m     at    Top  Right side of Settings page
////////////////////////////////////////////////////////////////////////////////

//FixIn: 8.0.1.12
/**
 * Add hidden input SEARCH KEY field into  main form, if previosly was searching by ID or Title
 * @param array $params			=>  array( 'search_get_key'  => 'wh_resource_id' )
 */
function wpbc_hidden_search_by_id_field_in_main_form( $params = array() ){

	$defaults = array(
	    				'search_get_key'  => 'wh_search_id'
				);
	$params = wp_parse_args( $params, $defaults );


	$search_form_value = '';
	if ( isset( $_REQUEST[ $params[ 'search_get_key' ] ] ) ) {
		$wh_resource_id    = wpbc_clean_digit_or_csd( $_REQUEST[ $params[ 'search_get_key' ] ] );          // '12,0,45,9' or '10'
		$wh_resource_title = wpbc_clean_string_for_form( $_REQUEST[ $params[ 'search_get_key' ] ] );       // Clean string
		if ( ! empty( $wh_resource_id ) ) {
			$search_form_value = $wh_resource_id;
		} else {
			$search_form_value = $wh_resource_title;
		}
	}

	if ( '' !== $search_form_value ) {
		?><input name="<?php echo $params['search_get_key']; ?>" value="<?php echo $search_form_value; ?>" type="hidden"><?php
	}
}

/**
	 * Real Search booking data  by ID | Title (at top right side of page)
 *
 * @param array $params - array of parameters
 * Exmaple:
                wpbc_toolbar_search_by_id__top_form( array(
                                                            'search_form_id' => 'wpbc_seasonfilters_search_form'
                                                          , 'search_get_key' => 'wh_search_id'
                                                          , 'is_pseudo'      => false
                                                    ) );

 */
function wpbc_toolbar_search_by_id__top_form( $params ) {

    $defaults = array(
                          'search_form_id'  => 'wpbc_seasonfilters_search_form'
                        , 'search_get_key'  => 'wh_search_id'
                        , 'is_pseudo'       => false                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = wpbc_get_new_booking_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;
                    );
    $params = wp_parse_args( $params, $defaults );


    $exclude_params         = array();                                          //array('page_num', 'orderby', 'order');  - if using "only_these_parameters",  then this parameter does NOT require
    $only_these_parameters  = array( 'page', 'tab', 'subtab', $params[ 'search_get_key' ] );        //FixIn: 8.1.1.11	-	added , 'subtab'	- ability to  search  booking resources in sub tab  pages in settings
    $wpbc_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), $exclude_params, $only_these_parameters );


    $search_form_value = '';
    if ( isset( $_REQUEST[ $params[ 'search_get_key' ] ] ) ) {
        $wh_resource_id    = wpbc_clean_digit_or_csd( $_REQUEST[ $params[ 'search_get_key' ] ] );          // '12,0,45,9' or '10'
        $wh_resource_title = wpbc_clean_string_for_form( $_REQUEST[ $params[ 'search_get_key' ] ] );       // Clean string
        if ( ! empty( $wh_resource_id ) ) {
            $search_form_value = $wh_resource_id;
        } else {
            $search_form_value = $wh_resource_title;
        }
    }


    wpbc_clear_div();

    ?>
    <span class="wpdevelop">

    <?php if ( ! $params['is_pseudo'] ) { ?>
        <div style="position: absolute; right: 20px; top: 10px;">
            <form action="<?php echo $wpbc_admin_url; ?>" method="post" id="<?php echo $params[ 'search_form_id' ]; ?>"  name="<?php echo $params[ 'search_form_id' ]; ?>"  >
            <?php
    } else {
      ?><div style="float:right;" id="<?php echo $params['search_form_id'] . '_pseudo'; ?>"><?php
    }

                $params_for_element = array(  'label_for' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                          , 'label' => ''//__('Keyword:', 'booking')
                                          , 'items' => array(
                                                                array(   'type' => 'text'
                                                                       , 'id' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                                                       , 'value' => $search_form_value
                                                                       , 'placeholder' => __('ID or Title', 'booking')
                                                                    )
                                                                , array(
                                                                    'type' => 'button'
                                                                    , 'title' => __('Go', 'booking')
                                                                    , 'class' => 'button-secondary'
                                                                    , 'font_icon' => 'glyphicon glyphicon-search'
                                                                    , 'icon_position' => 'right'
                                                                    , 'action' => ( ( ! $params['is_pseudo'] ) ? "jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );"
                                                                                                             : "jQuery('#" . $params[ 'search_get_key' ] . "').val( jQuery('#" . $params[ 'search_get_key' ] . "_pseudo').val() ); jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );" )           //Submit real form  at the top of page.
                                                                    )
                                                        )
                                    );

                ?><div class="control-group wpbc-no-padding" ><?php
                          wpbc_bs_input_group( $params_for_element );
                ?></div><?php

            if ( ! $params['is_pseudo'] ) { ?>
            </form>
            <?php } ?>
            <?php wpbc_clear_div(); ?>

            <?php
                if ( $params['is_pseudo'] ) {
                    // Required for opening specific page NUM during saving ////////
                    ?><input type="hidden" value="<?php echo $search_form_value; ?>" name="<?php echo $params[ 'search_get_key' ]; ?>" /><?php
                    ?><div class="clear" style="height:20px;"></div><?php
                }
            ?>
        </div>
    </span>
    <?php

    if ( $params['is_pseudo'] ) {

        // Hide pseudo form, if real  search  form does not exist
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                if ( jQuery('#<?php echo $params[ 'search_form_id' ]; ?>').length == 0 ) {
                    jQuery('#<?php echo $params['search_form_id'] . '_pseudo'; ?>').hide();
                }
            });
        </script>
        <?php
    }
}


////////////////////////////////////////////////////////////////////////////////
//  M o d a l s
////////////////////////////////////////////////////////////////////////////////

/** Start Loyouts - Modal Window structure */
function wpbc_write_content_for_modals_start_here() {

    ?><span id="wpbc_content_for_modals"></span><?php
}
add_bk_action( 'wpbc_write_content_for_modals', 'wpbc_write_content_for_modals_start_here');


////////////////////////////////////////////////////////////////////////////////
// JS & CSS
////////////////////////////////////////////////////////////////////////////////

/** Load suport JavaScript for "Bookings" page*/
function wpbc_js_for_bookings_page() {

    $is_use_hints = get_bk_option( 'booking_is_use_hints_at_admin_panel'  );
    if ( $is_use_hints == 'On' )
      wpbc_bs_javascript_tooltips();                                            // JS Tooltips

    wpbc_bs_javascript_popover();                                               // JS Popover

    wpbc_datepicker_js();                                                       // JS  Datepicker
    wpbc_datepicker_css();                                                      // CSS DatePicker
}


/** Datepicker activation JavaScript */
function wpbc_datepicker_js() {

    ?><script type="text/javascript">
        jQuery(document).ready( function(){

            function applyCSStoDays( date ){
                return [true, 'date_available'];
            }
            jQuery('input.wpdevbk-filters-section-calendar').datepick(
                {   beforeShowDay: applyCSStoDays,
                    showOn: 'focus',
                    multiSelect: 0,
                    numberOfMonths: 1,
                    stepMonths: 1,
                    prevText: '&laquo;',
                    nextText: '&raquo;',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: false,
                    changeYear: false,
                    minDate: null,
                    maxDate: null, //'1Y',
                    showStatus: false,
                    multiSeparator: ', ',
                    closeAtTop: false,
                    firstDay:<?php echo get_bk_option( 'booking_start_day_weeek' ); ?>,
                    gotoCurrent: false,
                    hideIfNoPrevNext:true,
                    useThemeRoller :false,
                    mandatory: true
                }
            );
        });
        </script><?php
}


/** Support CSS - datepick,  etc... */
function wpbc_datepicker_css(){
    ?>
    <style type="text/css">
        #datepick-div .datepick-header {
               width: 172px !important;
        }
        #datepick-div {
            -border-radius: 3px;
            -box-shadow: 0 0 2px #888888;
            -webkit-border-radius: 3px;
            -webkit-box-shadow: 0 0 2px #888888;
            -moz-border-radius: 3px;
            -moz-box-shadow: 0 0 2px #888888;
            width: 172px !important;
        }
        #datepick-div .datepick .datepick-days-cell a{
            font-size: 12px;
        }
        #datepick-div table.datepick tr td {
            border-top: 0 none !important;
            line-height: 24px;
            padding: 0 !important;
            width: 24px;
        }
        #datepick-div .datepick-control {
            font-size: 10px;
            text-align: center;
        }
        #datepick-div .datepick-one-month {
            height: auto;
        }
    </style>
    <?php
}


/** Sortable Table JavaScript */
function wpbc_sortable_js() {
    ?>
    <script type="text/javascript">
        // Activate Sortable Functionality
        jQuery( document ).ready(function(){

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
                    placeholder: '.wpbc_sortable_table .sort',
                    start:function(event,ui){
                            ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                            ui.item.removeAttr('style');
                    }
            });
        });
    </script>
    <?php

}