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
function wpbc_email_template_standard_html( $fields_values ) {
    ob_start();

    $base_color         = 'background-color:' . ( empty( $fields_values['base_color'] ) ? '#557da1' : $fields_values['base_color'] ) . ';'; 
    $background_color   = 'background-color:' . ( empty( $fields_values['background_color'] ) ? '#FDFDFD' : $fields_values['background_color'] ) . ';';
    $body_color         = 'background-color:' . ( empty( $fields_values['body_color'] ) ? '#F5F5F5' : $fields_values['body_color'] ) . ';';
    $text_color         = 'color:'            . ( empty( $fields_values['text_color'] ) ? '#333333' : $fields_values['text_color'] ) . ';';

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
    <style type="text/css">
        body {
            Margin: 0;
            padding: 0;
            min-width: 100%;
            <?php echo $background_color; ?>
        }
        table {
            border-spacing: 0;
            font-family: sans-serif;
            <?php echo $text_color; ?>
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;                                
                Margin: 0; 
                padding: 70px 0 70px 0;
                    <?php echo $background_color; ?> 
        }
        .webkit {
            max-width: 600px;
        }        
        @-ms-viewport { 
            width: device-width; 
        }              
        .outer {
            Margin: 0 auto;
            width: 96%;
            max-width: 600px;            
                box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; 
                border: 1px solid #dcdcdc; 
                border-radius: 3px !important;
                    <?php echo $body_color; ?> 
        }           
        .full-width-image img {
            width: 100%;
            height: auto;
        }
        .header {
                border-radius: 3px 3px 0 0 !important; 
                color: #ffffff; border-bottom: 0; 
                font-weight: bold; 
                line-height: 100%; 
                vertical-align: middle; 
                font-family: Helvetica, Roboto, Arial, sans-serif;
                    <?php echo $base_color; ?>
        }
        .footer .inner {
                text-align: center;
        }
        .footer .inner p {
                font-size: 11px;
        }
        .inner {
            padding: 48px;
        }
        .header .inner {
                padding: 10px;
        }
        p {
            Margin: 0;
        }
        p.footer {
            font-size: 11px;
        }
        a {
            color: #ee6a56;
            text-decoration: underline;
        }
        .h1 {
            font-size: 21px;
            font-weight: bold;
            Margin-bottom: 18px;                        
        }
        .header .inner .h1 {
                color: #ffffff; 
                display: block; 
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 30px; 
                font-weight: 300; 
                line-height: 150%; 
                Margin: 0; 
                padding: 26px 48px; 
                text-align: left; 
                text-shadow: 0 1px 0 #7797b4; 
                -webkit-font-smoothing: antialiased;
        }
        .h2 {
            font-size: 18px;
            font-weight: bold;
            Margin-bottom: 12px;
        }        
        .one-column .contents {
            text-align: left;
        }
        .one-column p {
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373; 
        }            
        .one-column p.h2,  
        .two-column .column .contents p.h2 {
                display: block;                 
                font-size: 18px; 
                font-weight: bold; 
                line-height: 130%; 
                Margin: 16px 0 8px; 
                text-align: left;        
                    color: #557da1; 
        }
        .two-column {
            text-align: center;
            font-size: 0;
        }
        
        .two-column .column {
            width: 100%;
            max-width: 280px;                                                   
            display: inline-block;
            vertical-align: top;
        }
        .two-column .column .contents p{
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373;            
        }
        .two-column .inner,
        .footer .inner {
                padding: 10px 48px;
        }        
        .contents {
            width: 100%;
        }
        .header .inner {
                width: 100%;
        }
        .footer .inner {
                width: 100%;
                border-top:1px solid #dddddd;
        }
        .two-column .contents {
            font-size: 14px;
            text-align: left;
        }
        .two-column img {
            width: 100%;
            height: auto;
        }
        .two-column .text {
            padding-top: 10px;
        }
        .two-column p {
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373; 
        }                    
        @media screen and (max-width: 400px) {
            .two-column .column {
                max-width: 100% !important;
            }
        }        
        @media screen and (min-width: 401px) and (max-width: 620px) {
            .two-column .column {
                max-width: 50% !important;
            }
        }
    </style>
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
        table {border-collapse: collapse !important;}
    </style>
    <![endif]-->
</head>
<body style="Margin:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;min-width:100%;<?php echo $background_color; ?>" <?php echo is_rtl() ? 'rightmargin="0"' : 'leftmargin="0"'; ?> > 
    <center class="wrapper" style="width:100%;table-layout:fixed;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;Margin:0;padding-top:70px;padding-bottom:70px;padding-right:0;padding-left:0;<?php echo $background_color; ?>" <?php echo is_rtl() ? 'dir="rtl"' : 'dir="ltr"'?> >
        <div class="webkit" style="max-width:600px;" >
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center" style="border-spacing:0;font-family:sans-serif;<?php echo $text_color; ?>" >
            <tr>
            <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
            <![endif]-->                                                       
            <table class="outer" align="center" style="border-spacing:0;font-family:sans-serif;<?php echo $text_color; ?><?php echo $body_color; ?>Margin:0 auto;width:96%;max-width:600px;box-shadow:0 1px 4px rgba(0,0,0,0.1) !important;border-width:1px;border-style:solid;border-color:#dcdcdc;border-radius:3px !important;" >
<?php if ( ! empty( $fields_values['header_img600_src'] ) ) { ?>
                <!-- Header IMG:  1 column template row -->
                <tr>                                                                <!-- This image must be 600px width (NOT wider). If less, then we need to set in CSS width in px of this image -->
                    <td class="full-width-image" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                        <img src="images/header.jpg" alt="" style="border-width:0;width:100%;height:auto;" />                  <!-- src="<?php $fields_values['header_img600_src']; ?>" -->   
                    </td>
                </tr>
<?php } ?>
                <!-- Header:  1 column template row -->
                <tr>                                                                        
                    <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                        <table width="100%" class="header" style="<?php echo $base_color; ?>border-spacing:0;border-radius:3px 3px 0 0 !important;color:#ffffff;border-bottom-width:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:Helvetica, Roboto, Arial, sans-serif;" >
                            <tr>
                                <td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;width:100%;" >
                                    <p class="h1" style="Margin-bottom:10px;color:#ffffff;display:block;font-family:Helvetica, Roboto, Arial, sans-serif;font-size:30px;font-weight:300;line-height:150%;Margin:0;padding-top:26px;padding-bottom:26px;padding-right:48px;padding-left:48px;text-align:left;text-shadow:0 1px 0 #7797b4;-webkit-font-smoothing:antialiased;" ><?php echo ( wp_kses_post( wptexturize( $fields_values['header_content'] ) ) ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>                     
                <!-- Content: 1 column template row -->
                <tr>                                                            
                    <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                        <table width="100%" style="border-spacing:0;font-family:sans-serif;<?php echo $text_color; ?>" >
                            <tr>
                                <td class="inner contents" style="padding-top:48px;padding-bottom:48px;padding-right:48px;padding-left:48px;width:100%;text-align:left;" >
                                    <p style="Margin:0;font-size:14px;Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;line-height:150%;color:#737373;" ><?php 
                                        
                                        $h2_headers = array('<p class="h2" style="Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;display:block;font-size:18px;font-weight:bold;line-height:130%;Margin:16px 0 8px;text-align:left;color:#557da1;" >', '</p>');
                                        $fields_values['content'] = str_replace( array( '<h2>', '</h2>' ), $h2_headers, $fields_values['content'] );
                                        
                                        echo ( wp_kses_post( wptexturize( $fields_values['content'] ) ) ); 
                                    ?></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Footer: 1 column template row  -->
                <tr>
                    <td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
                        <table width="100%" class="footer" style="border-spacing:0;font-family:sans-serif;<?php echo $text_color; ?>" >
                            <tr>
                                <td class="inner" style="text-align:center;padding-top:10px;padding-bottom:10px;padding-right:48px;padding-left:48px;width:100%;border-top-width:1px;border-top-style:solid;border-top-color:#dddddd;" >
                                    <p style="Margin:0;Margin-bottom:10px;font-family:Helvetica, Roboto, Arial, sans-serif;line-height:150%;color:#737373;font-size:11px;" >
                                        <?php /* <a style="color:#ee6a56;text-decoration:underline;" >Forward to a Friend</a> &nbsp; &nbsp; <a style="color:#ee6a56;text-decoration:underline;" >Unsubscribe</a> &nbsp; &nbsp; <a style="color:#ee6a56;text-decoration:underline;" >Preferences</a><br /> */ 
                                        echo ( wp_kses_post( wptexturize( $fields_values['footer_content'] ) ) );  ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>                
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>
    </center>
</body>
<?php

    return ob_get_clean();                                                      // Return this email content
}


return;                                                                         

// Exist from this file, at the bottom - original static HTML template for email

// <editor-fold     defaultstate="collapsed"                        desc=" Source Code of template for parsing at http://inliner.cm/ "  >

////////////////////////////////////////////////////////////////////////////////
// Source Code of template for parsing at http://inliner.cm/
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">                                     <!--  dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>" -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style type="text/css">
        body {
            Margin: 0;
            padding: 0;
            min-width: 100%;
            background-color: #ffffff;
        }
        table {
            border-spacing: 0;
            font-family: sans-serif;
            color: #333333;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;                                
                Margin: 0; 
                padding: 70px 0 70px 0;
                    background-color: #f5f5f5; 
        }
        .webkit {
            max-width: 600px;
        }        
        @-ms-viewport { 
            width: device-width; 
        }      
        /* width 100% */
        .outer {
            Margin: 0 auto;
            width: 96%;
            max-width: 600px;            
                box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; 
                border: 1px solid #dcdcdc; 
                border-radius: 3px !important;
                    background-color: #fdfdfd; 
        }   
        
        .full-width-image img {
            width: 100%;
            height: auto;
        }
        .header {
                border-radius: 3px 3px 0 0 !important; 
                color: #ffffff; border-bottom: 0; 
                font-weight: bold; 
                line-height: 100%; 
                vertical-align: middle; 
                font-family: Helvetica, Roboto, Arial, sans-serif;
                    background-color: #557da1; 
        }
        .footer .inner {
                text-align: center;
        }
        .footer .inner p {
                font-size: 11px;
        }
        .inner {
            padding: 48px;
        }
        .header .inner {
                padding: 10px;
        }
        p {
            Margin: 0;
        }
        p.footer {
            font-size: 11px;
        }
        a {
            color: #ee6a56;
            text-decoration: underline;
        }
        .h1 {
            font-size: 21px;
            font-weight: bold;
            Margin-bottom: 18px;                        
        }
        .header .inner .h1 {
                color: #ffffff; 
                display: block; 
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 30px; 
                font-weight: 300; 
                line-height: 150%; 
                Margin: 0; 
                padding: 26px 48px; 
                text-align: left; 
                text-shadow: 0 1px 0 #7797b4; 
                -webkit-font-smoothing: antialiased;
        }
        .h2 {
            font-size: 18px;
            font-weight: bold;
            Margin-bottom: 12px;
        }        
        .one-column .contents {
            text-align: left;
        }
        .one-column p {
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373; 
        }            
        .one-column p.h2,  
        .two-column .column .contents p.h2 {
                display: block;                 
                font-size: 18px; 
                font-weight: bold; 
                line-height: 130%; 
                Margin: 16px 0 8px; 
                text-align: left;        
                    color: #557da1; 
        }
        .two-column {
            text-align: center;
            font-size: 0;
        }
        /* Previously 300px. - TODO: need to test  if we really  need to have here 280 or 300px in the different email applications. Also  its possible that  we need to have header images,  not 280px but only 260px.... */
        .two-column .column {
            width: 100%;
            max-width: 280px;                                                   
            display: inline-block;
            vertical-align: top;
        }
        .two-column .column .contents p{
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373;            
        }
        .two-column .inner,
        .footer .inner {
                padding: 10px 48px;
        }        
        .contents {
            width: 100%;
        }
        .header .inner {
                width: 100%;
        }
        .footer .inner {
                width: 100%;
                border-top:1px solid #dddddd;
        }
        .two-column .contents {
            font-size: 14px;
            text-align: left;
        }
        .two-column img {
            width: 100%;
            height: auto;
        }
        .two-column .text {
            padding-top: 10px;
        }
        .two-column p {
            font-size: 14px;
            Margin-bottom: 10px;            
                font-family: Helvetica, Roboto, Arial, sans-serif; 
                font-size: 14px; 
                line-height: 150%;
                    color: #737373; 
        }            
        /*Media Queries*/
        @media screen and (max-width: 400px) {
            .two-column .column {
                max-width: 100% !important;
            }
        }        
        @media screen and (min-width: 401px) and (max-width: 620px) {
            .two-column .column {
                max-width: 50% !important;
            }
        }
    </style>
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
        table {border-collapse: collapse;}
    </style>
    <![endif]-->
</head>
<body>                                                                          <!-- <?php echo is_rtl() ? 'rightmargin="0"' : 'leftmargin="0"'; ?> -->
    <center class="wrapper" >                                                   <!-- <?php echo is_rtl() ? 'dir="rtl"' : 'dir="ltr"'?> -->
        <div class="webkit">
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center">
            <tr>
            <td>
            <![endif]-->
            <table class="outer" align="center">
                                                                                <!-- <?php if ( ! empty( $fields_values['header_img600_src'] ) ) { ?> -->
                <!-- Header IMG:  1 column template row -->
                <tr>                                                                <!-- This image must be 600px width (NOT wider). If less, then we need to set in CSS width in px of this image -->
                    <td class="full-width-image">
                        <img src="images/header.jpg" alt="" />                  <!-- src="<?php $fields_values['header_img600_src']; ?>" -->   
                    </td>
                </tr>
                                                                                <!-- <?php } ?> -->
                <!-- Header:  1 column template row -->
                <tr>                                                                        
                    <td class="one-column">
                        <table width="100%" class="header">
                            <tr>
                                <td class="inner">
                                    <p class="h1">HTML Email Header</p>         <!-- <?php echo ( wp_kses_post( wptexturize( $fields_values['header_content'] ) ) ); ?> -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>     
                
                <!-- Content: 1 column template row -->
                <tr>                                                            
                    <td class="one-column">
                        <table width="100%">
                            <tr>
                                <td class="inner contents">                     <!-- <?php echo ( wp_kses_post( wptexturize( $fields_values['content'] ) ) ); ?> -->
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquet diam a facilisis eleifend. Cras ac justo felis. Mauris faucibus, orci eu blandit fermentum, lorem nibh sollicitudin mi, sit amet interdum metus urna ut lacus.</p>
                                    <p class="h2">Lorem ipsum dolor</p>
                                    <p>Fusce eu euismod leo, a accumsan tellus. Quisque vitae dolor eu justo cursus egestas. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed sit amet sapien odio. Sed pellentesque arcu mi, quis malesuada lectus lacinia et. Cras a tempor leo.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                                                                                <!-- <?php if (  ( ! empty( $fields_values['content_column_1'] ) ) && ( ! empty( $fields_values['content_column_2'] ) )  ) { ?> -->
                <!-- Content: 2 columns template row -->
                <tr>
                    <td class="two-column">
                        <!--[if (gte mso 9)|(IE)]>
                        <table width="100%">
                        <tr>
                        <td width="50%" valign="top">
                        <![endif]-->
                        <div class="column">
                            <!-- Column 1 -->
                            <table width="100%">
                                <tr>
                                    <td class="inner">
                                        <table class="contents">
                                                                                <!-- <?php if ( ! empty( $fields_values['header_column_1_img260_src'] ) ) { ?> -->
                                            <tr>
                                                <td>
                                                    <!-- width  of image must  be 260px -->
                                                    <img src="images/two-column-01.jpg" alt="" />   <!--  src="<?php $fields_values['header_column_1_img260_src']; ?>"  -->
                                                </td>
                                            </tr>
                                                                                <!-- <?php } ?> -->
                                            <tr>
                                                <td class="text">               <!-- <?php echo ( wp_kses_post( wptexturize( $fields_values['content_column_1'] ) ) );  ?> -->
                                                    <p>Column 1 Content .... Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquet diam a facilisis eleifend. Cras ac justo felis. Mauris faucibus, orci eu blandit fermentum, lorem nibh sollicitudin mi, sit amet interdum metus urna ut lacus. </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if (gte mso 9)|(IE)]>
                        </td><td width="50%" valign="top">
                        <![endif]-->
                        <div class="column">
                            <!-- Column 2 -->
                            <table width="100%">
                                <tr>
                                    <td class="inner">
                                        <table class="contents">
                                                                                <!-- <?php if ( ! empty( $fields_values['header_column_2_img260_src'] ) ) { ?> -->
                                            <tr>
                                                <td>
                                                    <!-- width  of image must  be 260px -->
                                                    <img src="images/two-column-02.jpg" alt="" />   <!--  src="<?php $fields_values['header_column_2_img260_src']; ?>"  -->
                                                </td>
                                            </tr>
                                                                                <!-- <?php } ?> -->
                                            <tr>
                                                <td class="text">               <!-- <?php echo ( wp_kses_post( wptexturize( $fields_values['content_column_2'] ) ) );  ?> -->
                                                    <p>Column 2 Content .... Fusce eu euismod leo, a accumsan tellus. Quisque vitae dolor eu justo cursus egestas. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed sit amet sapien odio. Sed pellentesque arcu mi, quis malesuada lectus lacinia et. Cras a tempor leo. </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
                </tr>
                                                                                <!-- <?php } ?> -->
                <!-- Footer: 1 column template row  -->
                <tr>
                    <td class="one-column">
                        <table width="100%" class="footer">
                            <tr>
                                <td class="inner">              <!-- <?php echo ( wp_kses_post( wptexturize( $fields_values['footer_content'] ) ) );  ?> -->
                                    <p>
                                        <a>Forward to a Friend</a> &nbsp; &nbsp; <a>Unsubscribe</a> &nbsp; &nbsp; <a>Preferences</a><br /> 
                                        You are receiving this email because you are either purchased product at WPBC or you entered your email at WPBC
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>                

            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>
    </center>
</body>
<?php 
// </editor-fold>