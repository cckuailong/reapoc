<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function plugOPB_check_installation_date() {
 
    $nobug = "";
    $userId = get_current_user_id();
    $nobug = get_option('plugOPB_hide_bugs1two_'.$userId);

    $nobugHoliday = get_option('plugOPB_hide_holiday_'.$userId);

    
    if (!$nobug) {

        $install_date = get_option( 'plugOps_activation_date' );
        $past_date = strtotime( '-7 days' );
 
        if ( (int)$past_date > (int)$install_date ) {
 
            add_action( 'admin_notices', 'plugOPB_display_admin_Rev_Notice' );
 
        }

    }


    if (!$nobugHoliday) {

        $future_date = strtotime( '2 January 2021' );
        $currentData = strtotime("now");
 
        if ( (int)$future_date > (int)$currentData ) {

            if ( ! function_exists('ulpb_available_pro_widgets') ) {
                //add_action( 'admin_notices', 'plugOPB_display_admin_holiday_notice' );
            }
 
            
 
        }

    }

    
    

    $noWelcomeNotice = get_option('plugOPB_hide_welcomeNotice_'.$userId);
    if ($noWelcomeNotice != 'hideNotice') {
        add_action( 'admin_notices', 'plugOPB_display_admin_welcomeNotice' );
    }
 
}
add_action( 'admin_init', 'plugOPB_check_installation_date' );

function plugOPB_display_admin_Rev_Notice() {
 
    $reviewurl = 'https://wordpress.org/support/plugin/page-builder-add/reviews/?rate=5#new-post';
    
    global $wp;
    $nobugurl = home_url( $wp->request ) . '?plugOPB_hide_bugs=1';

    if(strpos($nobugurl, 'wp-admin') == false){
        $nobugurl = get_admin_url() . '?plugOPB_hide_bugs=1';
    }


    $thisAdminURL = get_admin_url();
    $thisDefaultUrlProtocol =  'http://';
    if (strpos($thisAdminURL, 'https') !== false ) {
        $thisDefaultUrlProtocol =  'https://';
    }

    $actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (strpos($actual_link, '?') == false) {
        $nobugurl = $actual_link . '?plugOPB_hide_bugs=1';
    }else{
        $nobugurl = $actual_link . '&plugOPB_hide_bugs=1';
    }

    $nobugurl = $thisDefaultUrlProtocol.$nobugurl;

    $install_date = get_option( 'plugOPB_activation_date' );

    echo "<div class='notice notice-success  pluginopsWriteReview'>
        
        <p style='display:inline-block;'>Do you like <strong> PluginOps Landing Page Builder ? </strong> <br> Please help us by posting your feedback here : <b> <a href=".$reviewurl." target='_blank'> Feedback Page </a> </b> </p>

        <a href=".$nobugurl."><button type='button' class='notice-dismiss' style='display:inline-block; position:relative; float:right;'><span class='screen-reader-text'>Dismiss this notice.</span></button></a>
    </div>";

}


function plugOPB_display_admin_holiday_notice() {
 
    $reviewurl = 'https://pluginops.com/page-builder/?ref=Happyholidays';
    
    global $wp;
    $nobugurl = home_url( $wp->request ) . '?plugOPB_hide_holiday=1';

    if(strpos($nobugurl, 'wp-admin') == false){
        $nobugurl = get_admin_url() . '?plugOPB_hide_holiday=1';
    }


    $thisAdminURL = get_admin_url();
    $thisDefaultUrlProtocol =  'http://';
    if (strpos($thisAdminURL, 'https') !== false ) {
        $thisDefaultUrlProtocol =  'https://';
    }

    $imgUrl = ULPB_PLUGIN_URL.'/images/icons/holiday-icons.png';

    $actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (strpos($actual_link, '?') == false) {
        $nobugurl = $actual_link . '?plugOPB_hide_holiday=1';
    }else{
        $nobugurl = $actual_link . '&plugOPB_hide_holiday=1';
    }

    $nobugurl = $thisDefaultUrlProtocol.$nobugurl;

    $install_date = get_option( 'plugOPB_activation_date' );

    echo "<div class='notice notice-success  pluginopsWriteReview'>
        
        <p style='display:inline-block;'> 
            <b> Happy Holidays from PluginOps Team <span> <img src='".$imgUrl."' style='width:70px; vertical-align:middle;'></span> </b> 
            <br>
            <br>
            Save 30% on all annual plans use code on checkout : <span style='padding:4px 10px; background:#fff; color:#333; border:1px dashed #333;'><b>Holidays2020</b></span>
            <br>
            <b> <a href=".$reviewurl." target='_blank'>  Click here to avail this discount. </a> </b> 
            <br>
            <br>
            <a href=".$nobugurl.">Dismiss</a>

        </p>

        <a href=".$nobugurl."><button type='button' class='notice-dismiss' style='display:inline-block; position:relative; float:right;margin-top:45px;'><span class='screen-reader-text'>Dismiss this notice.</span></button></a>
    </div>";

}

function plugOPB_display_admin_welcomeNotice() {
 
    
    global $wp;


    $thisAdminURL = get_admin_url();
    $thisDefaultUrlProtocol =  'http://';
    if (strpos($thisAdminURL, 'https') !== false ) {
        $thisDefaultUrlProtocol =  'https://';
    }

    $actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (strpos($actual_link, '?') === false) {
        $nobugurl = $actual_link . '?plugOPB_hide_welcome_notice=hidewelcomenotice';
    }else{
        $nobugurl = $actual_link . '&plugOPB_hide_welcome_notice=hidewelcomenotice';
    }

    $nobugurl = $thisDefaultUrlProtocol.$nobugurl;


    $install_date = get_option( 'plugOPB_activation_date' );

    echo "<div class='notice notice-success  pluginopsWelcomeNotice'>
        
        <p style='display:inline-block;'>Welcome  😀  - Thanks for installing PluginOps Landing Page Builder   | <b> <a href='https://pluginops.com/docs/home/' target='_blank'> Documentation </a> </b> </p>

        <a href=".$nobugurl."><button type='button' class='notice-dismiss' style='display:inline-block; position:relative; float:right;'><span class='screen-reader-text'>Dismiss this notice.</span></button></a>
    </div>";

}


function plugOPB_set_no_bug() {
 
    $nobug = "";
    $holidaybug = '';
    $welcomeNoticeNobug  = '';
 
    if ( isset( $_GET['plugOPB_hide_bugs'] ) ) {
        $nobug = esc_attr( $_GET['plugOPB_hide_bugs'] );
    }

    if ( isset( $_GET['plugOPB_hide_welcome_notice'] ) ) {
        $welcomeNoticeNobug = esc_attr( $_GET['plugOPB_hide_welcome_notice'] );
    }

    if ( isset( $_GET['plugOPB_hide_holiday'] ) ) {
        $holidaybug = esc_attr( $_GET['plugOPB_hide_holiday'] );
    }

    if ( 1 == $nobug ) {
        $userId = get_current_user_id();
        add_option( 'plugOPB_hide_bugs1two_'.$userId, TRUE );
    }

    if ( 1 == $holidaybug ) {
        $userId = get_current_user_id();
        add_option( 'plugOPB_hide_holiday_'.$userId, TRUE );
    }
    // delete_option( 'plugOPB_hide_bugs1two_'.get_current_user_id() );
    // delete_option( 'plugOPB_hide_holiday_'.get_current_user_id() );
    // delete_option( 'plugOPB_hide_welcomeNotice_'.get_current_user_id() );

    if ($welcomeNoticeNobug == 'hidewelcomenotice') {
        $userId = get_current_user_id();
        update_option( 'plugOPB_hide_welcomeNotice_'.$userId, 'hideNotice' );
    }
    //delete_option( 'plugOPB_hide_welcomeNotice_'.get_current_user_id() );
 
 
} add_action( 'admin_init', 'plugOPB_set_no_bug', 5 );

?>