<?php
/**
 * @version 1.1
 * @package Booking Calendar 
 * @category Send Emails
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 15.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



////////////////////////////////////////////////////////////////////////////////
// Emails
////////////////////////////////////////////////////////////////////////////////

/**
 * Check email and format  it
 * 
 * @param string $emails
 * @return string
 */
function wpbc_validate_emails( $emails ) {

    $emails = str_replace(';', ',', $emails);

    if ( !is_array( $emails ) )
            $emails = explode( ',', $emails );

    $emails_list = array();
    foreach ( (array) $emails as $recipient ) {

        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
        $recipient_name = '';
        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
            if ( count( $matches ) == 3 ) {
                $recipient_name = $matches[1];
                $recipient = $matches[2];                 
            }
        } else {                
            // Check about correct  format  of email
            if( preg_match( '/([\w\.\-_]+)?\w+@[\w\-_]+(\.\w+){1,}/im', $recipient, $matches ) ) {                      //FixIn: 8.7.7.2
                $recipient = $matches[0];
            }             
        }

        $recipient_name = str_replace('"', '', $recipient_name);
        $recipient_name = trim( wp_specialchars_decode( esc_html( stripslashes( $recipient_name ) ), ENT_QUOTES ) );

        $emails_list[] =   ( empty( $recipient_name ) ? '' : $recipient_name . ' '  )
                           . '<' . sanitize_email( $recipient ) . '>';		
    }

    $emails_list = implode( ',', $emails_list );

    return $emails_list;
}    


/**
 * Convert email  address to the correct  format  like  "Jony Smith" <smith@server.com> - tp  prevent "WordPress" title in email.
 * @param string $wpbc_mail - just  simple email
 * @param type $booking_form_show - array with  name and secondname of the person - title
 * @return string - formated email.
 */
function wpbc_email_prepand_person_name( $wpbc_mail, $booking_form_show = array() ) {

    //FixIn:5.4.4
    $wpbc_email_title =  ((isset($booking_form_show['firstname']))?$booking_form_show['firstname'].' ':'')
                        .((isset($booking_form_show['name']))?$booking_form_show['name'].' ':'')
                        .((isset($booking_form_show['lastname']))?$booking_form_show['lastname'].' ':'')
                        .((isset($booking_form_show['secondname']))?$booking_form_show['secondname'].' ':'');
    $wpbc_email_title = ( ( empty($wpbc_email_title ) ) ? __('Booking system' ,'booking') : substr( $wpbc_email_title, 0 , -1 ) );

    $wpbc_email_title = str_replace('"', '', $wpbc_email_title);
    $wpbc_email_title = trim( wp_specialchars_decode( esc_html( stripslashes( $wpbc_email_title ) ), ENT_QUOTES ) );

    $wpbc_mail = wpbc_validate_emails( $wpbc_email_title . ' <' .  $wpbc_mail . '> ' );

    return $wpbc_mail;
}

// Old, Replaced in api-emails
class wpbc_email_return_path {
    function __construct() {
            add_action( 'phpmailer_init', array( $this, 'fix' ) );    
    }

    function fix( $phpmailer ) {
            $phpmailer->Sender = $phpmailer->From;
    }
}


function wpbc_wp_mail( $mail_recipient, $mail_subject, $mail_body, $mail_headers ){

    $wpbc_email_return_path = new wpbc_email_return_path();

    // $mail_recipient = str_replace( '"', '', $mail_recipient );               //FixIn:5.4.3
    if ( ! wpbc_is_this_demo() )                                                //FixIn:6.1.1.19
        @wp_mail($mail_recipient, $mail_subject, $mail_body, $mail_headers);

    unset( $wpbc_email_return_path );
}





function wpbc_check_for_several_emails_in_form( $mail_recipients, $formdata, $bktype ) {  //FixIn: 6.0.1.9

    $possible_other_emails = explode('~',$formdata);
    $possible_other_emails = array_map("explode", array_fill(0,count($possible_other_emails),'^'), $possible_other_emails);
    $other_emails = array();
    foreach ( $possible_other_emails as $possible_emails ) {
        if (       ( $possible_emails[0] == 'email' ) 
                //&& ( $possible_emails[1] != 'email' . $bktype ) 
                && ( ! empty($possible_emails[2]) ) 
            )
                $other_emails[]= trim( $possible_emails[2] );				//FixIn: 8.2.1.6
    }
    $other_emails = array_unique( $other_emails );							//FixIn: 8.2.1.6
    if ( count( $other_emails ) > 1 ) {
        $other_emails = implode(',',$other_emails);
        $mail_recipients =  $other_emails;
    }
    return $mail_recipients;
}


//  N E W  /////////////////////////////////////////////////////////////////////


/** 
	 * Parse email and get Parts of Email - Name and Email
 * 
 * @param string $email
 * @return array [email] => beta@wpbookingcalendar.com
                 [title] => Booking system
                 [original] => "Booking system" 
                 [original_to_show] => "Booking system" <beta@wpbookingcalendar.com>
 */         
function wpbc_get_email_parts( $email ) {
        
    $email_to_parse =  html_entity_decode( $email );                                 // Convert &quot; to " etc...
    
    $pure_name  = '';
    $pure_email = '';
    if( preg_match( '/(.*)<(.+)>/', $email_to_parse, $matches ) ) {
        if ( count( $matches ) == 3 ) {
            $pure_name = $matches[1];
            $pure_email = $matches[2];                 
        }
    } else {                                                                    // Check about correct  format  of email
        if( preg_match( '/([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}/im', $email_to_parse, $matches ) ) {
            $pure_email = $matches[0];
        }             
    }
    
    $pure_name = trim( wp_specialchars_decode( esc_html( stripslashes( $pure_name ) ), ENT_QUOTES ) , ' "');
    
    $return_email = array(
                            'email' => sanitize_email( $pure_email )
                            , 'title' => $pure_name
                            , 'original' => $email_to_parse
                            , 'original_to_show' => htmlentities( $email_to_parse )         // Convert " to  &quot;  etc...
                    );
    
    return $return_email;
}


// Get Emails Help Shortcodes for Settings pages
function wpbc_get_email_help_shortcodes( $skip_shortcodes = array() , $email_example = '') {
    
    $fields = array();
    
    if ( class_exists('wpdev_bk_personal') ) { 
        $fields[] = sprintf(__('You can use (in subject and content of email template) any shortcodes, which you used in the booking form. Use the shortcodes in the same way as you used them in the content form at Settings Fields page.' ,'booking'));
        $fields[] = '<hr/>';
    }
    $fields[] = '<strong>' . __('You can use following shortcodes in content of this template' ,'booking') . '</strong>';
    
    // [content]
    if ( class_exists('wpdev_bk_personal') )  
        $fields[] = sprintf(__('%s - inserting data info about the booking, which you configured in the content form at Settings Fields page' ,'booking'),'<code>[content]</code>');
    else
        $fields[] = sprintf(__('%s - inserting data info about the booking' ,'booking'),'<code>[content]</code>');


    // [dates]    
    $fields[] = sprintf(__('%s - inserting the dates of booking' ,'booking'),'<code>[dates]</code>');
    
    // [check_in_date]
    if ( ! in_array( 'check_in_date', $skip_shortcodes ) ) {
	    $fields[] = sprintf( __( '%s - inserting check-in date (first day of reservation),', 'booking' ), '<code>[check_in_date]</code>' );
	    $fields[] = sprintf( __( '%s - inserting check-in date (only date without time) (first day of reservation),', 'booking' ), '<code>[check_in_only_date]</code>' );	//FixIn: 8.7.2.5
    }
    // [check_out_date] [check_out_plus1day]
    if ( ! in_array( 'check_out_date', $skip_shortcodes ) ) {
        $fields[] = sprintf(__('%s - inserting check-out date (last day of reservation),' ,'booking'),'<code>[check_out_date]</code>');
        $fields[] = sprintf(__('%s - inserting check-out date (only date without time) (last day of reservation),' ,'booking'),'<code>[check_out_only_date]</code>');		//FixIn: 8.7.2.5
        $fields[] = sprintf(__('%s - inserting check-out date (last day of reservation),' ,'booking'),'<code>[check_out_plus1day]</code>') .  ' + 1 ' . __('day', 'booking');
    }
    
    // [dates_count]
    if ( ! in_array( 'dates_count', $skip_shortcodes ) )
        $fields[] = sprintf(__('%s - inserting the number of booking dates ' ,'booking'),'<code>[dates_count]</code>');
    
    $fields[] = '<hr/>';

    
    // [id]
    $fields[] = sprintf(__('%s - inserting ID of booking ' ,'booking'),'<code>[id]</code>');
    
    // [resource_title]  [bookingtype]
    if ( class_exists('wpdev_bk_personal') ) 
        if ( ! in_array( 'bookingtype', $skip_shortcodes ) )
            $fields[] = sprintf(__('%s or %s - inserting the title of the booking resource ' ,'booking'),'<code>[resource_title]</code>','<code>[bookingtype]</code>');
    
        
    // [cost]    
    if ( class_exists('wpdev_bk_biz_s') )     
        if ( ! in_array( 'cost', $skip_shortcodes ) )
            $fields[] = sprintf(__('%s - inserting the cost of  booking ' ,'booking'),'<code>[cost]</code>');
    
    $fields[] = '<hr/>';   
    
    
    // [siteurl]
    $fields[] = sprintf(__('%s - inserting your site URL ' ,'booking'),'<code>[siteurl]</code>');   
    
    if ( class_exists('wpdev_bk_personal') ) {    
        $fields[] = sprintf(__('%s - inserting IP address of the user who made this action ' ,'booking'),'<code>[remote_ip]</code>');
        $fields[] = sprintf(__('%s - inserting contents of the User-Agent: header from the current request, if there is one ' ,'booking'),'<code>[user_agent]</code>');
        $fields[] = sprintf(__('%s - inserting address of the page (if any), where visitor make this action ' ,'booking'),'<code>[request_url]</code>');
        $fields[] = sprintf(__('%s - inserting time of this action ' ,'booking'),'<code>[current_time]</code>');
    }

    $fields[] = sprintf(__('%s - inserting date of this action ' ,'booking'),'<code>[current_date]</code>');

    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_date]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_year]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_month]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_day]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_hour]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_minutes]</code>');
    $fields[] = sprintf(__('%s - inserting modification date of booking ' ,'booking'),'<code>[modification_seconds]</code>');



    // [moderatelink]
    if ( ! in_array( 'moderatelink', $skip_shortcodes ) ) {
        $fields[] = sprintf(__('%s - inserting moderate link of new booking ' ,'booking'),'<code>[moderatelink]</code>');

        //FixIn: 8.4.7.25
        $fields[] = sprintf(__('%s - inserting link to approve booking in 1 mouse click ' ,'booking'),'<code>[click2approve]</code>');
        $fields[] = sprintf(__('%s - inserting link to set booking as pending in 1 mouse click ' ,'booking'),'<code>[click2decline]</code>');
        $fields[] = sprintf(__('%s - inserting link for move booking to trash in 1 mouse click ' ,'booking'),'<code>[click2trash]</code>');

        $fields[] = sprintf(__('%s - inserting link for export booking to' ,'booking'),'<code>[add_to_google_cal_url]</code>') . ' Google Calendar';	//FixIn: 7.1.2.5
	}
    
    if ( class_exists('wpdev_bk_personal') ) { 

    	//FixIn: 8.1.3.5.1
        if ( ! in_array( 'visitorbookingslisting', $skip_shortcodes ) )
            $fields[] = sprintf(__('%s - inserting link to the page where visitor can see listing of own bookings,  (possible to use the %s parameter for setting different %s of this page. Example: %s )' ,'booking'),'<code>[visitorbookingslisting]</code>', '"url"', 'URL', '<em>[visitorbookingslisting url="http://www.server.com/custom-page/"]</em>');

        if ( ! in_array( 'visitorbookingediturl', $skip_shortcodes ) )
            $fields[] = sprintf(__('%s - inserting link to the page where visitor can edit the reservation,  (possible to use the %s parameter for setting different %s of this page. Example: %s )' ,'booking'),'<code>[visitorbookingediturl]</code>', '"url"', 'URL', '<em>[visitorbookingediturl url="http://www.server.com/custom-page/"]</em>');

        // [visitorbookingcancelurl]
        if ( ! in_array( 'visitorbookingcancelurl', $skip_shortcodes ) ) 
            $fields[] = sprintf(__('%s - inserting link to the page where visitor can cancel the reservation, (possible to use the %s parameter for setting different %s of this page. Example: %s )' ,'booking'),'<code>[visitorbookingcancelurl]</code>', '"url"', 'URL', '<em>[visitorbookingcancelurl url="http://www.server.com/custom-page/"]</em>');
        
        if ( class_exists('wpdev_bk_biz_s') )  {
            // [visitorbookingpayurl]
            if ( ! in_array( 'visitorbookingpayurl', $skip_shortcodes ) ) 
                $fields[] = sprintf(__('%s - inserting link to payment page where visitor can pay for the reservation  (possible to use the %s parameter for setting different %s of this page. Example: %s )' ,'booking'),'<code>[visitorbookingpayurl]</code>', '"url"', 'URL', '<em>[visitorbookingpayurl url="http://www.server.com/custom-page/"]</em>');    
            
            // [paymentreason]
            if ( ! in_array( 'paymentreason', $skip_shortcodes ) ) 
                $fields[] = sprintf(__('%s - add the reason for booking payment, you can enter it before sending email, ' ,'booking'),'<code>[paymentreason]</code>');
        }
    }
    
    // [denyreason]
    if ( ! in_array( 'denyreason', $skip_shortcodes ) )     
        $fields[] = sprintf(__('%s - add the reason booking was cancelled, you can enter it before sending email, ' ,'booking'),'<code>[denyreason]</code>');
    
    
    //$fields[] = __('HTML tags is accepted.' ,'booking');
    
    $fields[] = '<hr/>';
    
    // show_additional_translation_shortcode_help
    $fields[] = '<strong>' . sprintf(__('Configuration in several languages' ,'booking') ) . '.</strong>';
    $fields[] = sprintf(__('%s - start new translation section, where %s - locale of translation' ,'booking'),'<code>[lang=LOCALE]</code>','<code>LOCALE</code>');
    $fields[] = sprintf(__('Example #1: %s - start French translation section' ,'booking'),'<code>[lang=fr_FR]</code>');
    $fields[] = sprintf(__('Example #2: "%s" - English and French translation of some message' ,'booking'),'<code>Thank you for your booking.[lang=fr_FR]Je vous remercie de votre reservation.</code>');
    

    return $fields;           
}


/** 
	 * Check  Email  subject  about Language sections
 * 
 * @param string $subject
 * @param string $email_id
 * @return string
 */
function wpbc_email_api_get_subject_before( $subject, $email_id ) {
            
    $subject =  apply_bk_filter('wpdev_check_for_active_language', $subject );

    return  $subject;
}
add_filter( 'wpbc_email_api_get_subject_before', 'wpbc_email_api_get_subject_before', 10, 2 );    // Hook fire in api-email.php


/** 
	 * Check  Email  sections content  about Language sections
 * 
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbc_email_api_get_content_before( $fields_values, $email_id , $email_type ) {
    
    if ( isset( $fields_values['content'] ) ) {
        $fields_values['content'] =  apply_bk_filter('wpdev_check_for_active_language', $fields_values['content'] );
        if ($email_type == 'html')
            $fields_values['content'] = make_clickable( $fields_values['content'] );
    }
    
    if ( isset( $fields_values['header_content'] ) )
        $fields_values['header_content'] =  apply_bk_filter('wpdev_check_for_active_language', $fields_values['header_content'] );
    
    if ( isset( $fields_values['footer_content'] ) )
        $fields_values['footer_content'] =  apply_bk_filter('wpdev_check_for_active_language', $fields_values['footer_content'] );
    
    return $fields_values;
}
add_filter( 'wpbc_email_api_get_content_before', 'wpbc_email_api_get_content_before', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Modify email  content,  if needed. - In HTML mail content,  make links clickable.
 * 
 * @param array $email_content - content of Email
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbc_email_api_get_content_after( $email_content, $email_id , $email_type ) {
    
    if (  ( $email_type == 'html' ) || ( $email_type == 'multipart' )  )
       $email_content = make_clickable( $email_content );

    return $email_content;
}
add_filter( 'wpbc_email_api_get_content_after', 'wpbc_email_api_get_content_after', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Check  Email  Headers  -  in New Booking Email (to admin) set Reply-To header to visitor email.
 * 
 * @param string $headers
 * @param string $email_id - Email ID
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param array $replace_array - list  of relpaced shortcodes
 * @return string
 */
function wpbc_email_api_get_headers_after( $mail_headers, $email_id , $fields_values , $replace_array, $additional_params = array() ) {
       
/*
// Default in api-emails.php:
//        $mail_headers  = 'From: ' . $this->get_from__name() . ' <' .  $this->get_from__email_address() . '> ' . "\r\n" ;
//        $mail_headers .= 'Content-Type: ' . $this->get_content_type() . "\r\n" ;
//        
//            $mail_headers = "From: $mail_sender\n";
//            preg_match('/<(.*)>/', $mail_sender, $simple_email_matches );
//            $reply_to_email = ( count( $simple_email_matches ) > 1 ) ? $simple_email_matches[1] : $mail_sender;
//            $mail_headers .= 'Reply-To: ' . $reply_to_email . "\n";        
//            $mail_headers .= 'X-Sender: ' . $reply_to_email . "\n";
//            $mail_headers .= 'Return-Path: ' . $reply_to_email . "\n";
*/

//debuge($mail_headers, $email_id , $fields_values , $replace_array);    
    if (
        ( $email_id == 'new_admin' )                                            // Only  for email: "New Booking to Admin"
       || ( isset( $additional_params['reply'] ) )  
    ) {
        if ( isset( $replace_array['email'] ) ) {                                // Get email from  the booking form.
           
            $reply_to_email = sanitize_email( $replace_array['email'] );
            if ( ! empty( $reply_to_email ) )
                $mail_headers .= 'Reply-To: '    . $reply_to_email  . "\r\n" ;
            
           // $mail_headers .= 'X-Sender: '    . $reply_to_email  . "\r\n" ;
           // $mail_headers .= 'Return-Path: ' . $reply_to_email  . "\r\n" ;           
        }
    }

    return  $mail_headers;
}
add_filter( 'wpbc_email_api_get_headers_after', 'wpbc_email_api_get_headers_after', 10, 5 );    // Hook fire in api-email.php


/** 
	 * Check if we can send Email - block  sending in live demos
 * 
 * @param bool $is_send_email 
 * @param string $email_id
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @return bool
 */
function wpbc_email_api_is_allow_send( $is_send_email, $email_id, $fields_values ) {
//debuge($fields_values);    
    if ( wpbc_is_this_demo() )   
        $is_send_email = false;

    return  $is_send_email;
}
add_filter( 'wpbc_email_api_is_allow_send', 'wpbc_email_api_is_allow_send', 10, 3 );    // Hook fire in api-email.php


/** 
	 * Show warning about not sending emails,  and reason about this.
 * 
 * @param object $wp_error_object     - WP Error object
 * @param string $error_description   - Description
 */
function wpbc_email_sending_error( $wp_error_object, $error_description = '' ) {
    
    if ( empty( $error_description ) ) {
//        $error_description = __( 'Unknown exception', 'booking' ) . '.';        // Overwrite to  show error, if no description ???    
    }
    
    if ( ! empty( $error_description ) ) {

        $error_description = '' . __('Error', 'booking')  . '! ' . __('Email was not sent. An error occurred.', 'booking') .  ' ' . $error_description;
        
        // Admin side
        if (  function_exists( 'wpbc_show_message' ) ) {
            wpbc_show_message ( $error_description , 15 , 'error');     

        }
        
        // Front-end
        ?>   
        <script type="text/javascript">  
            if (typeof( showMessageUnderElement ) == 'function') {
                showMessageUnderElement( '.booking_form' , '<?php echo esc_js( $error_description ) ; ?>', '');
            }
        </script>    
        <?php    
    } else {
        
        // Error that have no description. Its can be Empty Object like this: WP_Error Object(  'errors' => array(), 'error_data' => array() ),  or NOT
        // debuge( $wp_error_object );        
    }
}
add_action('wpbc_email_sending_error', 'wpbc_email_sending_error', 10, 2);