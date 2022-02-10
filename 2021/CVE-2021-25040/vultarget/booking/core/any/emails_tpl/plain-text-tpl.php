<?php  /**
 * @version 1.0
 * @package: Emails
 * @category: Templates
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-06-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * get email template
 * 
 * @param array $fields_values
 */
function wpbc_email_template_plain_text( $fields_values ) {
    
    ob_start();

    if ( ! empty($fields_values['header_content'] ) ) {
        
        echo wp_kses_post( wptexturize( $fields_values['header_content'] ) ) . "\n\n";  //Header
    }
    
    echo ( wp_kses_post( wptexturize( $fields_values['content'] ) ) );          //Content

    if ( ! empty( $fields_values['footer_content'] ) ) {
        
        echo "\n\n" .  wp_kses_post( wptexturize( $fields_values['footer_content'] ) ); //Footer 
    }
    return ob_get_clean();                                                      // Return this email content
}
