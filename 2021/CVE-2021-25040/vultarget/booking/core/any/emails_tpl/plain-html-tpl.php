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
function wpbc_email_template_plain_html( $fields_values ) {
    ob_start();

//    $base_color         = 'background-color:' . ( empty( $fields_values['base_color'] ) ? '#557da1' : $fields_values['base_color'] ) . ';'; 
//    $background_color   = 'background-color:' . ( empty( $fields_values['background_color'] ) ? '#FDFDFD' : $fields_values['background_color'] ) . ';';
//    $body_color         = 'background-color:' . ( empty( $fields_values['body_color'] ) ? '#F5F5F5' : $fields_values['body_color'] ) . ';';
//    $text_color         = 'color:'            . ( empty( $fields_values['text_color'] ) ? '#333333' : $fields_values['text_color'] ) . ';';

    $base_color         = '';
    $background_color   = '';
    $body_color         = '';
    $text_color         = '';
    
////////////////////////////////////////////////////////////////////////////////
//  HTML Email Template
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">                                     <!--  dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>" -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body style="Margin:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;min-width:100%;<?php echo $body_color; ?>" <?php echo is_rtl() ? 'rightmargin="0"' : 'leftmargin="0"'; ?> >
<div class="wrapper" style="table-layout:fixed;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;Margin:0;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;<?php echo $body_color; ?>" <?php echo is_rtl() ? 'dir="rtl"' : 'dir="ltr"'?> >
    <?php 
    
    if ( ! empty( $fields_values['header_content'] ) ) {
         ?><p style="Margin:0;Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;line-height:150%;font-size:14px;" ><?php 
         
         echo ( wp_kses_post( wptexturize( $fields_values['header_content'] ) ) ); 
         
         ?></p><?php
    }
    
    ?><p style="Margin:0;font-size:14px;Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;line-height:150%;<?php echo $text_color; ?>" ><?php 
                                        
        $h2_headers = array('<p class="h2" style="Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;display:block;font-size:18px;font-weight:bold;line-height:130%;Margin:16px 0 8px;text-align:left;color:#557da1;" >', '</p>');
        $fields_values['content'] = str_replace( array( '<h2>', '</h2>' ), $h2_headers, $fields_values['content'] );

        echo ( wp_kses_post( wptexturize( $fields_values['content'] ) ) ); 
    
    ?></p><?php 
    
    if ( ! empty( $fields_values['footer_content'] ) ) {
        
        ?><p style="Margin:0;Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;line-height:150%;font-size:11px;" ><?php 
        
        echo ( wp_kses_post( wptexturize( $fields_values['footer_content'] ) ) );
        
        ?></p><?php
    }
?>    
</div>   
</body>
<?php

    return ob_get_clean();                                                      // Return this email content
}
