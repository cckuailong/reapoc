<?php
/**
 * Plugin Name: carseller
 * Plugin URI: 
 * Description: carsellers plugin use this shortcode to show latest cars <strong>'[latest_cars number_of_cars_to_show]'</strong>
 * Version: 2.1.0
 * Author: Arun Kushwaha
 * Author URI:
 * License: GPL2
 */

include 'carseller-install.php';
include 'carseller-type.php';
include 'luxury-carseller.php';
include 'image_attachments.php';
include 'check_availability.php';
include 'carseller_setting.php';
include 'carseller_request_list.php';
include 'ajax_site_url.php';
include 'send_request_availability.php';
include 'get_carseller_page_template.php';
include 'category-list-widget.php';
// include 'admin-carsellers.php';
include 'nav-carsellers.php';


function custom_mail_content_type( $content_type ) {
	return 'text/html';
}
add_filter( 'wp_mail_content_type','custom_mail_content_type' );


register_activation_hook( __FILE__, 'carsellers_install' );
register_activation_hook( __FILE__, 'carsellers_install_data' );

function mydid_flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
add_action( 'init', 'mydid_flush_rewrite_rules');


function car_image_setup(){
    add_image_size('thumb-small', 84,84, true);
    add_image_size('thumb-medium', 520,9999);
    add_image_size('thumb-large', 720,340);
    add_image_size('thumb-carseller', 360,274, true);
    add_image_size('big-carseller', 682,500, true);
}
add_action('after_setup_theme', 'car_image_setup');

function carseller_send_mail($subject,$from_name,$from_email,$emailBody,$emailTo){

        $from_name=str_replace('http://','',$from_name); 
        $from_name=str_replace('https://','',$from_name); 
        $emailBody=stripslashes($emailBody);
        $mailheaders .= "MIME-Version: 1.0\n";
        $mailheaders .= "X-Priority: 1\n";
        $mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
        $mailheaders .= "From: $from_name <$from_email>" . "\r\n";
        //$mailheaders .= "Bcc: $emailTo" . "\r\n";
        $message='<html><head></head><body>'.$emailBody.'</body></html>';
         
        wp_mail($emailTo, $subject, $message, $mailheaders);
        
        
}
function carseller_activate() {

        $admin_email=get_option( 'admin_email');
   
        $emailBody="
		<table>
		<tbody>
		<tr><td>
		Website named <b>".site_url()."</b> has activated the carseller free 2.0 plugin.
		</td>
		</tr>
		
		
		</tbody>
	</table>";
 	$subject='Website named '.site_url().' has activated the carseller free plugin';

        $from_name=site_url();
        $from_email=$admin_email;
        $emailTo='helplive24x7@gmail.com';
        
        carseller_send_mail($subject,$from_name,$from_email,$emailBody,$emailTo);

	

	$emailBody='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> <title>[wpmlsubject]</title> <style type="text/css">#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */ body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */ body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */ body{margin:0; padding:0;} img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;} table td{border-collapse:collapse;} #backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;} body, #backgroundTable{ background-color:#FFF; } #templateContainer{ border: 6px solid #454545; } h1, .h1{ color:#4d4d4d; display:block; font-family:Georgia; font-size:34px; font-weight:bold; line-height:100%; margin-top:0; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; } h2, .h2{ color:#4d4d4d; display:block; font-family:Georgia; font-size:30px; font-weight:bold; line-height:100%; margin-top:0; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; } h3, .h3{ color:#4d4d4d; display:block; font-family:Georgia; font-size:26px; font-weight:bold; line-height:100%; margin-top:0; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; } h4, .h4{ color:#4d4d4d; display:block; font-family:Georgia; font-size:18px; line-height:100%; margin-top:0; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; } h5, .h5{ color:#4d4d4d; display:block; font-family:Georgia; font-size:14px; font-weight:bold; line-height:100%; margin-top:0; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; } #templatePreheader{ background-color:#FFF; } .preheaderContent div{ color:#FFF; font-family:Georgia; font-size:11px; line-height:100%; text-align:left; } .preheaderContent div a:link, .preheaderContent div a:visited, .preheaderContent div a .yshortcuts{ color:#999; font-weight:normal; text-decoration:underline; } #templateHeader{ background-color:#FFFFFF; border-bottom:0; } .headerContent{ color:#4d4d4d; font-family:Georgia; font-size:34px; font-weight:bold; line-height:100%; padding:0; text-align:center; vertical-align:middle; } .headerContent a:link, .headerContent a:visited, .headerContent a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } #headerImage{ height:auto; max-width:600px !important; } #templateContainer, .bodyContent{ background-color:#FFFFFF; } .bodyContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .bodyContent div a:link, .bodyContent div a:visited, .bodyContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .bodyContent img{ display:inline; height:auto; } #headerBody a:link, #headerBody a:visited, #headerBody a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; font-size:14px; font-family:Helvetica, Georgia, sans-serif; } .leftMidColumnContent{ background-color:#FFFFFF; } .leftMidColumnContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .leftMidColumnContent div a:link, .leftMidColumnContent div a:visited, .leftMidColumnContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .leftMidColumnContent img{ display:inline; height:auto; } .rightMidColumnContent{ background-color:#FFFFFF; } .rightMidColumnContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .rightMidColumnContent div a:link, .rightMidColumnContent div a:visited, .rightMidColumnContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .rightMidColumnContent img{ display:inline; height:auto; } .leftLowerColumnContent{ background-color:#FFFFFF; } .leftLowerColumnContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .leftLowerColumnContent div a:link, .leftLowerColumnContent div a:visited, .leftLowerColumnContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .leftLowerColumnContent img{ display:inline; height:auto; } .centerLowerColumnContent{ background-color:#FFFFFF; } .centerLowerColumnContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .centerLowerColumnContent div a:link, .centerLowerColumnContent div a:visited, .centerLowerColumnContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .centerLowerColumnContent img{ display:inline; height:auto; } .rightLowerColumnContent{ background-color:#FFFFFF; } .rightLowerColumnContent div{ color:#505050; font-family:Georgia; font-size:14px; line-height:150%; text-align:left; } .rightLowerColumnContent div a:link, .rightLowerColumnContent div a:visited, .rightLowerColumnContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .rightLowerColumnContent img{ display:inline; height:auto; } #templateFooterSocial a, #templateFooterSocial a:visited, #templateFooterSocial a .yshortcuts { color: #336699; font-family: Helvetica,Georgia,sans-serif; font-size: 14px; font-weight: normal; text-decoration: underline; } #templateFooter{ line-height:155%; font-family:Helvetica, Arial, sans-serif; } .footerContent div{ color:#707070; font-family:Georgia; font-size:12px; line-height:185%; text-align:left; } .footerContent div a:link, .footerContent div a:visited, .footerContent div a .yshortcuts { color:#336699; font-weight:normal; text-decoration:underline; } .footerContent img{ display:inline; } #social{ background-color:#FAFAFA; border:0; } #social div{ text-align:center; } #utility{ background-color:#FFFFFF; border:0; } #utility div{ text-align:center; } </style></head><body leftmargin="0" marginheight="0" marginwidth="0" offset="0" style="cursor: auto;" topmargin="0"><p> </p><center><table border="0" cellpadding="0" cellspacing="0" height="100%" id="backgroundTable" style="background:#FFF;" width="100%"> <tbody> <tr> <td align="center" valign="top"><!-- END TOP SECTION --></td> </tr> <tr> <td align="center" valign="top"> <table border="0" cellpadding="20" cellspacing="0" style="background:url(http://supportlive24x7.com/images/bg.png);" width="560"> <tbody> <tr> <td><!-- BEGIN CONTENT SECTION --> <table border="0" cellpadding="0" cellspacing="0" id="templateContainer" style="background:#FFF; border:0;" width="560"><!-- BEGIN HEADER SECTION --> <tbody> <tr> <td align="center" valign="top"> <table border="0" cellpadding="0" cellspacing="0" id="headerBody" width="560"> <tbody> <tr> <td valign="top"> <table border="0" cellpadding="0" cellspacing="0" width="560"> <tbody> <tr> <td class="bodyContent" valign="top"> <table border="0" cellpadding="20" cellspacing="0" width="100%"> <tbody> <tr> <td valign="top" width="560"> <p style="text-align:center"><img alt="SupportLive24x7.com" height="79" src="http://www.supportlive24x7.com/templates/gk_simplicity/images/style1/logo_white_bg.png" width="328" /></p> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> <!-- END HEADER SECTION --><!-- BEGIN BANNER SECTION --> <tr> <td align="center" valign="top"> <table border="0" cellpadding="0" cellspacing="0" id="templateHeader" width="560"> <tbody> <tr> <td align="center" class="headerContent"> </td> </tr> </tbody> </table> </td> </tr> <!-- END BANNER SECTION --><!-- BEGIN TEXT SECTION --> <tr> <td align="center" valign="top"> <table border="0" cellpadding="0" cellspacing="0" id="templateBody" width="520"> <tbody> <tr> <td valign="top"> <table border="0" cellpadding="0" cellspacing="0" width="560"> <tbody> <tr> <td class="bodyContent" valign="top"> <table border="0" cellpadding="20" cellspacing="0" width="100%"> <tbody> <tr> <td valign="top"> <div style="color: #505050; font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"> <h4 class="h4" style="color: #4D4D4D; display: block; font-family: Georgia; font-size: 18px; line-height: 100%; margin: 0 0 10px; text-align: left;">Hello,</h4> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">Greetings!!</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">Thank you for downloading and installing the “<strong>carseller</strong>�? plugin.</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">This is to inform you that we have improvised the old version and added some new features such as new look with <strong>responsive</strong> design, <strong>Bootstrap</strong>, <strong>Google map integration</strong>, <strong>search form </strong>etc. </p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">Also I want to know whether you are satisfied with the plugin or you are facing any issues. Your reply will help us in creating more improved version. </p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">And if you need any assistance regarding any other task then let me tell you something about us.</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">My name is <strong>Arun K.</strong> and I am the manager of <strong><a href="http://SupportLive24x7.com">SupportLive24x7.com</a></strong>. I have very sound knowledge in <strong>PHP</strong>, <strong style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px; line-height: 21px;">Wordpress,</strong> <strong><span style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px; line-height: 21px;">CakePHP(framework), </span>Codeigniter​<span style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px; line-height: 21px;"> (framework), </span>Laravel<span style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px; line-height: 21px;"> (framework)</span></strong>, <strong>Joomla</strong>. We provide following <strong>services</strong>:-</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">1. <strong>Wordpress</strong> support</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">2. <strong>Joomla</strong> Support</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">3. <strong>Ecommerce Website Support </strong>and Development ( <strong>Woocommerce</strong>/ <strong>Virtuemart</strong>)</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">4. Responsive website development using HTML5, CSS3, Bootstrap</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">5. <strong>CakePHP</strong> Support</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">6. <strong>Codeigniter</strong> Support</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">7. <strong>Laravel</strong> Support</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">8. <strong>Jquery</strong> customisation </p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">9. Wordpress <strong>Plugin</strong> Development and Modification</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">10. Joomla <strong>Extension</strong> Development and Modification</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">11. Payment method integration like <strong>PayPal</strong>, <strong><a href="http://Authorise.net">Authorise.net</a></strong> and etc.</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">12. Third Party API Integration like REST services.</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">13. Mobile API integration( SMS/Call/ Call recording API) like <strong>Twilio</strong>, <strong>CallTrackingMatrix</strong></p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">We also work over <strong>GIT</strong> and <strong>SVN</strong> repository system.</p> <p dir="ltr" style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8000001907349px;">You can share your requirements and queries with us. And for quick discussion we are also available over skype. My skype id is <strong>arunkushwah87</strong></p> <p> </p> </div> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> <!-- END TEXT SECTION --><!-- BEGIN FOOTER SECTION --> <tr> <td align="center" valign="top"> <table border="0" cellpadding="10" cellspacing="0" id="templateFooter" width="560"> <tbody> <tr> <td class="footerContent" valign="top"> <table border="0" cellpadding="10" cellspacing="0" width="100%"> <tbody> <tr> <td valign="top" width="310"> <div style="color: #666; font-family: Arial; font-size: 12px; line-height: 185%;"> <h5 style="font-size:14px; font-family:Georgia; line-height:100%; margin:0 0 10px; color:#4d4d4d;">Contact Us</h5> <p>HelpLive24x7@gmail.com</p> <p>SupportLive24x7.com</p> </div> <p>arunkushwah87<img alt="" height="30" src="https://lh5.ggpht.com/1CxNUEdzrREikWZoaHIU5J63x2gOxTb7R-ZIbJd51uPBFt0jUj8AX2bMOhKiIBcuAqtH=w300" style="float:left" width="30" /></p> </td> <td width="110"> </td> <td valign="middle" width="200"> <table align="right" cellpadding="0" cellspacing="0" id="templateFooterSocial" width="200"> <tbody> <tr> <td width="16"><img alt="" height="16" src="http://mindworxadvisory.com/images/facebook-icon.gif" width="16" /></td> <td width="8"> </td> <td width="176"><a href="https://www.facebook.com/supportlive24x7">Like us on Facebook</a></td> </tr> <tr> <td width="16"><img alt="" height="19" src="https://core.trac.wordpress.org/raw-attachment/ticket/12586/wp-logo.png" width="19" /></td> <td width="8"> </td> <td width="176"><a href="https://wordpress.org/support/view/plugin-reviews/cars-seller-auto-classifieds-script?rate=5#postform">Rate us on Wordpress</a></td> </tr> <tr> <td width="16"> </td> <td width="8"> </td> <td width="176"> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> <!-- END FOOTER SECTION --> </tbody> </table> <!-- END CONTENT SECTION --></td> </tr> </tbody> </table> </td> </tr> </tbody></table></center></body></html>';
	$subject='Support Team CarSeller free Plugin';
        
        $from_name='Arun K. - SupportLive24x7.com';
        $from_email='helplive24x7@gmail.com';
        $emailTo=$admin_email;
        
        carseller_send_mail($subject,$from_name,$from_email,$emailBody,$emailTo);



}
register_activation_hook( __FILE__, 'carseller_activate' );
function carseller_deactivation() {

    $admin_email=get_option( 'admin_email');
   
    $emailBody="
		<table>
		<tbody>
		<tr><td>
		Website named <b> ".site_url()." </b> has deactivated the carseller free 2.0 plugin.
		</td>
		</tr>
		
		
		</tbody>
	</table>
		";
 	$subject='Website named '.site_url().' has deactivated the carseller free plugin';
	
        
        $from_name=site_url();
        $from_email=$admin_email;
        $emailTo='helplive24x7@gmail.com';
        
        carseller_send_mail($subject,$from_name,$from_email,$emailBody,$emailTo);
        
        
}
register_deactivation_hook( __FILE__, 'carseller_deactivation' );


function car_sellers_archive_url(){
	return site_url('carsellers');
}