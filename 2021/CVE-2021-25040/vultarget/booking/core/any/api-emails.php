<?php
/**
 * @version     1.1
 * @package     General Emails API
 * @category    General Emails API Fields for Settings page and Functions for Sending emails.
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2016-05-12, 2015-10-06
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// Email Settings API - Saving different options                                   
abstract class WPBC_Emails_API extends WPBC_Settings_API  {
           
    public $sending;    
    public $replace = array();
    
        
    
    /**
	 * Email Settings API Constructor
     *  During creation,  system try to load values from DB, if exist.
     * 
     * @param type $id - "Pure Email Template name
     */
    public function __construct( $id,  $init_fields_values = array()) {

        $options = array( 
                        'db_prefix_option' => 'booking_email_' 
                      , 'db_saving_type'   => 'togather'                        // { 'togather' (Default), 'separate', 'separate_prefix' } 
            );                 
                                                                                // Email template saved as: 
                                                                                //                          ( "booking_email_" . $id  ) >>> "booking_email_new_admin"
        
        parent::__construct( $id, $options, $init_fields_values );                   // Define ID of Setting page and options
        
        add_filter( 'phpmailer_init', array( $this, 'process_multipart' ) );            // For multipart messages
      
        add_action( 'wp_mail_failed', array($this, 'email_error_parse') );              // Parse any errors during sending emails.
    }

    

    /**
	 * This function  must  be overriden - Initialise Settings Form Fields
     
    public function init_settings_fields() {
        
            $this->fields = array();
                
            $this->fields['enabled'] = array(   
                                          'type'          => 'checkbox'
                                        , 'default'     => 'On'            
                                        , 'title'       => __('Enable / Disable', 'booking')
                                        , 'label'       => __('Enable this email notification', 'booking')   
                                        , 'description' => ''
                                        , 'group'       => 'general'

                                    );
            // ...                         
    }    
    /**/
    
        

    // <editor-fold     defaultstate="collapsed"                        desc=" Suport functions "  >
    
    /**
	 * List of preg* regular expression patterns to search for replace in plain emails.
     *  More: https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
     */
    private function get_plain_search_array() {
        return array(
                               "/\r/",                                          // Non-legal carriage return
                               '/&(nbsp|#160);/i',                              // Non-breaking space
                               '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i', // Double quotes
                               '/&(apos|rsquo|lsquo|#8216|#8217);/i',           // Single quotes
                               '/&gt;/i',                                       // Greater-than
                               '/&lt;/i',                                       // Less-than
                               '/&#38;/i',                                      // Ampersand
                               '/&#038;/i',                                     // Ampersand
                               '/&amp;/i',                                      // Ampersand
                               '/&(copy|#169);/i',                              // Copyright
                               '/&(trade|#8482|#153);/i',                       // Trademark
                               '/&(reg|#174);/i',                               // Registered
                               '/&(mdash|#151|#8212);/i',                       // mdash
                               '/&(ndash|minus|#8211|#8722);/i',                // ndash
                               '/&(bull|#149|#8226);/i',                        // Bullet
                               '/&(pound|#163);/i',                             // Pound sign
                               '/&(euro|#8364);/i',                             // Euro sign
                               '/&#36;/',                                       // Dollar sign
                               '/&[^&;]+;/i',                                   // Unknown/unhandled entities
                               '/[ ]{2,}/'                                      // Runs of spaces, post-handling
                        );

    }
    
    
    /** List of symbols "for Replace To" */
    private function get_plain_replace_array() {
        
        return array(
                                '',                                             // Non-legal carriage return
                                ' ',                                            // Non-breaking space
                                '"',                                            // Double quotes
                                "'",                                            // Single quotes
                                '>',                                            // Greater-than
                                '<',                                            // Less-than
                                '&',                                            // Ampersand
                                '&',                                            // Ampersand
                                '&',                                            // Ampersand
                                '(c)',                                          // Copyright
                                '(tm)',                                         // Trademark
                                '(R)',                                          // Registered
                                '--',                                           // mdash
                                '-',                                            // ndash
                                '*',                                            // Bullet
                                '£',                                            // Pound sign
                                'EUR',                                          // Euro sign. € ?
                                '$',                                            // Dollar sign
                                '',                                             // Unknown/unhandled entities
                                ' '                                             // Runs of spaces, post-handling
                    );
    }
    
            
    /**
	 * Esacpe and replace any HTML entities
     * 
     * @param type $string
     * @return string
     */
    public function esc_to_plain_text( $string ) {
        
//        //Replace <a href="http://server.com">Link</a> to Link( http://server.com )
//        $pattern = "/<a(.*)+href=[\"|']+([^\"']+)(?=(\"|'))[^>]*>(.*)<\/a>/" ;      //"/(?<=href=(\"|'))[^\"']+(?=(\"|'))/i";
//        $newurl = "$4 ($2)";
//        $string = preg_replace($pattern,$newurl,$string);

        $newstring = preg_replace( $this->get_plain_search_array(), $this->get_plain_replace_array(), strip_tags( $string ) );
        return $newstring;
    }
    
    
    /**
	 * Set array for replacing shortcodes in Email Content and Subject
     * 
     * @param array $replace - if this parameter skipped,  then array reseted to empty array
     */
    function set_replace( $replace = array() ) {
      $this->replace = $replace;  
    }


    /**
	 * Replace shortcodes in givven string. Usually its Email Content and Subject
     * 
     * @param string $subject
     */
    public function replace_shortcodes( $subject ) {
        
        $defaults = array(
            'ip'                => apply_bk_filter( 'wpbc_get_user_ip' )
            , 'blogname'        => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
            , 'siteurl'         => get_site_url()
        );

        $replace = wp_parse_args( $this->replace, $defaults );
        
        foreach ( $replace as $replace_shortcode => $replace_value ) {
            
            $subject = str_replace( array(   '[' . $replace_shortcode . ']'
                                           , '{' . $replace_shortcode . '}' )
                                    , $replace_value
                                    , $subject );
        }
        
        return $subject;        
    }

    // </editor-fold>
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" Pure Email functions "  >
    
    /**
	 * For MultiPart email define plain text - AltBody
     * 
     * Also  additionaly  fix Sender - its have to be same as From
     *
     * @param PHPMailer $mailer
     * @return PHPMailer
     */
    public function process_multipart( $mailer )  {

        //if ( $this->sending && $this->get_email_content_type() == 'multipart' ) {
        if ( $this->sending  ) {
            
            if ( $this->get_email_content_type() == 'multipart' )
                $mailer->AltBody = wordwrap( $this->esc_to_plain_text( $this->replace_shortcodes( $this->get_content_plain() ) ) );
            
            $mailer->Sender = $mailer->From;
            
            //$this->sending = false;                                           // If we set  it to  false,  then  we can not trigger for any Mail errors in this CLASS
        }

        return $mailer;
    }
    
    
    /**
	 * Get type of Email: 'plain' | 'html' | 'multipart'
     *
     * @return string
     */
    public function get_email_content_type() {
        
        return $this->fields_values['email_content_type'] && class_exists( 'DOMDocument' ) ? $this->fields_values['email_content_type'] : 'plain';        
    }

    
    /**
	 * Get Email Content Type: 'text/plain' | 'text/html' | 'multipart/alternative'
     *
     * @return string
     */
    public function get_content_type() {
        
        switch ( $this->get_email_content_type() ) {
            case 'html' :
                return 'text/html';
            case 'multipart' :
                return 'multipart/alternative';
            default :
                return 'text/plain';
        }
    }

    
    /**
	 * Define Email Headers.  For exmaple: "Conte type:" 'text/plain' | 'text/html' | 'multipart/alternative' or "From": Name <email@server.com>
     * 
     * @return string
     */
    public function get_headers( $additional_params = array() ) {
        
        $mail_headers = '';
        
        $from_address = $this->get_from__email_address();
        
        if ( ! empty( $from_address ) ) {
            $mail_headers  .= 'From: ' . $this->get_from__name() . ' <' .  $from_address . '> ' . "\r\n" ;
        } else {
            /* If we don't have an email from the input headers default to wordpress@$sitename
             * Some hosts will block outgoing mail from this address if it doesn't exist but
             * there's no easy alternative. Defaulting to admin_email might appear to be another
             * option but some hosts may refuse to relay mail from an unknown domain. See
             * https://core.trac.wordpress.org/ticket/5007.
             */
        }

        $mail_headers .= 'Content-Type: ' . $this->get_content_type() . "\r\n" ;
        
        $mail_headers = apply_filters( 'wpbc_email_api_get_headers_after', $mail_headers, $this->id, $this->fields_values , $this->replace, $additional_params );
        
        return $mail_headers;
    }
    
    
    /**
	 * Get Content of Email
     *
     * @return string
     */
    public function get_content() {

        if ( $this->get_email_content_type() == 'plain' ) {
            $email_content = $this->esc_to_plain_text( $this->replace_shortcodes( $this->get_content_plain() ) );
        } else {
            $email_content = $this->replace_shortcodes( $this->get_content_html() );
        }

        $email_content = apply_filters( 'wpbc_email_api_get_content_after' , $email_content, $this->id, $this->get_email_content_type() );
        
        // Remove all shortcodes, which is not replaced early.
        $email_content = preg_replace ('/[\[\{][a-zA-Z0-9.,_-]{0,}[\]\}]/', '', $email_content);                                            
        
        return wordwrap( $email_content, 100 );
    }


        /**
	 * Get Email Content as Text (from Plain Text template)
         * 
         * @return string 
         */
        function get_content_plain() {
            
            //require_once( dirname(__FILE__)  . '/emails_tpl/standard-text-tpl.php' );              
            //require_once( dirname(__FILE__)  . '/emails_tpl/plain-text-tpl.php' );              
            if ( isset( $this->fields_values['template_file'] ) ) {
                $email_tpl_name = $this->fields_values['template_file'];
            } else {
                $email_tpl_name = 'plain';
            }
            
            if ( file_exists( dirname(__FILE__)  . '/emails_tpl/'. $email_tpl_name .'-text-tpl.php' ) ) 
                require_once( dirname(__FILE__)  . '/emails_tpl/'. $email_tpl_name .'-text-tpl.php' );
            else 
                require_once( dirname(__FILE__)  . '/emails_tpl/plain-text-tpl.php' );            

            $fields_values = $this->fields_values;
            
            $fields_values = apply_filters( 'wpbc_email_api_get_content_before' , $fields_values, $this->id , 'plain' );    //Ability to parse 'content', 'header_content', 'footer_content' for different languges, etc ....
            
            if (  function_exists( 'wpbc_email_template_' . $email_tpl_name . '_text' ) )               
                $plain_email = call_user_func_array( 'wpbc_email_template_' . $email_tpl_name . '_text' , array( $fields_values ) );
            else 
                $plain_email = wpbc_email_template_text( $fields_values );

            // Replace <p> | <br> to \n
            $plain_email = preg_replace( '/<(br|p)[\t\s]*[\/]?>/i', "\n", $plain_email );      
            // $plain_email = str_replace( array('<br/>', '<br>'), "\n", $plain_email );
            
            return $plain_email;        
        }


        /**
	 * Get Email Content as HTML (from HTML template)
         * 
         * @return type
         */
        function get_content_html() {

            // Load specific Email Template: ///////////////////////////////////
            if ( isset( $this->fields_values['template_file'] ) ) {
                $email_tpl_name = $this->fields_values['template_file'];
            } else {
                $email_tpl_name = 'plain';
            }
            //require_once( dirname(__FILE__)  . '/emails_tpl/standard-html-tpl.php' );              
            //require_once( dirname(__FILE__)  . '/emails_tpl/plain-html-tpl.php' );              
            
            if ( file_exists( dirname(__FILE__)  . '/emails_tpl/'. $email_tpl_name .'-html-tpl.php' ) ) 
                require_once( dirname(__FILE__)  . '/emails_tpl/'. $email_tpl_name .'-html-tpl.php' );
            else 
                require_once( dirname(__FILE__)  . '/emails_tpl/plain-html-tpl.php' );            
            ////////////////////////////////////////////////////////////////////
            
            $fields_values = $this->fields_values;
            
            $fields_values = apply_filters( 'wpbc_email_api_get_content_before' , $fields_values, $this->id , 'html' );    //Ability to parse 'content', 'header_content', 'footer_content' for different languges, etc ....

            if (  function_exists( 'wpbc_email_template_' . $email_tpl_name . '_html' ) )               
                $html_email = call_user_func_array( 'wpbc_email_template_' . $email_tpl_name . '_html' , array( $fields_values ) );
            else 
                $html_email = wpbc_email_template_html( $fields_values );        
         
            return $html_email;
        }

    
    /**
	 * Get escaped Email Subject
     * 
     * @return string
     */
    public function get_subject() {
        
        $subject = $this->fields_values['subject'];
        
        $subject = apply_filters( 'wpbc_email_api_get_subject_before' , $subject, $this->id );
        
        $subject = $this->esc_to_plain_text( $this->replace_shortcodes( $subject ) );
        
        $subject = apply_filters( 'wpbc_email_api_get_subject_after' , $subject, $this->id );
        
        return $subject;        
    }   

    
    /**
	 * Get escapeed Name from any not supporting characters
     * 
     * @return string
     */
    public function get_from__name() {
        return wp_specialchars_decode( esc_html( stripslashes( $this->fields_values['from_name'] ) ), ENT_QUOTES );
    }
    
    
    /**
	 * Get sanitized Email address
     * 
     * @return type
     */
    public function get_from__email_address() {
        return sanitize_email( $this->fields_values['from'] );
    }
    

    /**
	 * For future support, right now does not support
     * 
     * @return empty string
     */
    public function get_attachments() {
        return '';
    }    
    
    
    /**
	 * Check email and format  it
     * 
     * @param string $emails
     * @return string
     */
    public function validate_emails( $emails ) {

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
                if( preg_match( '/([\w\.\-_]+)?\w+@[\w\-_]+(\.\w+){1,}/im', $recipient, $matches ) ) {                  //FixIn: 8.7.7.2
                    $recipient = $matches[0];
                }             
            }

            //$recipient_name = str_replace('"', '', $recipient_name);
            $recipient_name = trim( wp_specialchars_decode( esc_html( stripslashes( $recipient_name ) ), ENT_QUOTES ) );
            
            $emails_list[] =   ( empty( $recipient_name ) ? '' : $recipient_name . ' '  )
                               . '<' . sanitize_email( $recipient ) . '>';		
	}
        
        $emails_list = implode( ',', $emails_list );

        return $emails_list;
    }

    
    /**
     * Make Email Sending by using wp_mail standard function. 
     * 
     * @param string $to - Email
     * @param array $replace - accociated array  of values to  replace in email Body and Subject. Exmaple: array( 'name' => 'Jo', 'secondname' => 'Smith' )
     * @return boolean Sent or Failed to send.
     */ 
    public function send( $to = '', $replace = array() ) {

        $return = false;
        
//        if ( empty( $to ) )
//            return  $return;
        
        $this->sending = true;       


        $this->set_replace( $replace );

        $to = $this->validate_emails( $to );

        $subject        = $this->get_subject();
        $message        = $this->get_content();
        $headers        = $this->get_headers();
        $attachments    = $this->get_attachments();
//debuge('In email', htmlentities($to), $subject, htmlentities($message), $headers, $attachments)  ;      
        $is_send_email = true;
        
        $is_send_email = apply_filters( 'wpbc_email_api_is_allow_send', $is_send_email, $this->id, $this->fields_values );
        
        if ( $is_send_email ) {
        	$to = apply_filters( 'wpbc_email_api_send_field_to', $to );                                                 //FixIn: 8.1.3.1
	        //FixIn: 8.5.2.22
	        if ( ! empty( $to ) ) {
// debuge( '$to, $subject, $message, $headers, $attachments',htmlspecialchars($to), htmlspecialchars($subject), htmlspecialchars($message), htmlspecialchars($headers), htmlspecialchars($attachments));
		        $return = wp_mail( $to, $subject, $message, $headers, $attachments );
	        }
        }

        $this->sending = false;

        // Send Copy to admin email
        if (
                ( isset( $this->fields_values['copy_to_admin'] ) )
             && ( $this->fields_values['copy_to_admin'] == 'On' ) 
            ){
            
            $this->sending = true;    
            
            $subject = __('Email copy to', 'booking') . ' ' . str_replace( array( '<', '>' ), '', $to) . ' - ' . $subject;
             
            $headers = $this->get_headers( array( 'reply' => $to ) );

//FixIn: 8.1.2.17
if ( ! empty( $this->fields_values['to'] ) ) {
	$admin_to = $this->fields_values['to'];
	if ( ! empty( $this->fields_values['to_name'] )) {
		$admin_to_name = $this->fields_values['to_name'];
	}


    if ( (  strpos( $this->fields_values['to'], ',') === false ) && (  strpos( $this->fields_values['to'], ';') === false ) ) {

        $valid_email = sanitize_email( $this->fields_values['to'] );
        if ( ! empty( $valid_email ) )
            $to = trim( wp_specialchars_decode( esc_html( stripslashes( $this->fields_values['to_name'] ) ), ENT_QUOTES ) )
                  . ' <' .  $valid_email . '> ';
    } else {

        // Comma separated several  emails - validate  all  these emails in Email API class
        $to_array = $this->fields_values['to'];

        $to_array = str_replace(';', ',', $to_array);
	if ( !is_array( $to_array ) ) $to_array = explode( ',', $to_array );

        $to_name = str_replace(';', ',', $this->fields_values['to_name']);
	if ( !is_array( $to_name ) ) $to_name = explode( ',', $to_name );

        $to = array();
        foreach ( $to_array as $to_ind => $to_email) {

            $to_name_str = $to_name[ ( count( $to_name ) - 1 ) ];

            if ( isset( $to_name[ $to_ind ] ) )
                $to_name_str = $to_name[ $to_ind ];

            $valid_email = sanitize_email( $to_email );
            if ( ! empty( $valid_email ) )
                $to[] = trim( wp_specialchars_decode( esc_html( stripslashes( $to_name_str ) ), ENT_QUOTES ) ) . ' <' . $valid_email  . '> ';
        }
        $to = implode(',', $to);

    }


} else {    //FixIn: 8.1.2.17 - END

			$to = $this->validate_emails( $this->get_from__name() . ' <' . $this->get_from__email_address() . '> ' );

}           //FixIn: 8.1.2.17

            $is_send_email = apply_filters( 'wpbc_email_api_is_allow_send_copy', $is_send_email, $this->id, $this->fields_values );
            
            if ( $is_send_email ) {
            	$to = apply_filters( 'wpbc_email_api_send_copy_field_to', $to );                                        //FixIn: 8.1.3.1
	            //FixIn: 8.5.2.22
	            if ( ! empty( $to ) ) {
		            $return_copy = wp_mail( $to, $subject, $message, $headers, $attachments );
	            }
            }
            
            $this->sending = false;
        }
                
        $this->set_replace();       // Reset replace parameter
                        
        return $return;
    }
    // </editor-fold>
    

    /**
	 * Show possible erros during sending emails.
     * 
     * @param type $wp_error_object - new WP_Error( $e->getCode(), $e->getMessage(), $mail_error_data )
     */
    public function email_error_parse( $wp_error_object ) {
//debuge( $wp_error_object );
        if ( $this->sending ) {                                                 // Check  if this error generated for email relative to  this class.
          
            $error_description_array = array();
            if ( isset( $wp_error_object->errors  ) )
                foreach ( $wp_error_object->errors as $key_error => $error_description ) {
                    $error_description_array[] = implode(' ',  $error_description );
                }
                
            if ( ! empty( $error_description_array ) ) {
                $error_description = implode(' ', $error_description_array ) ;
                
                do_action('wpbc_email_sending_error', $wp_error_object, $error_description );

            } else {
                do_action('wpbc_email_sending_error', $wp_error_object, '' );
            }
        }        
    }

}

