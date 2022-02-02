<?php
/*
Plugin Name: Payment Form for PayPal Pro
Plugin URI: http://wordpress.dwbooster.com/forms/paypal-payment-pro-form
Description: Payment Form for PayPal Pro to accept credit cards directly into your website. Official PayPal Partner.             
Version: 1.0.1
Author: CodePeople.net
Author URI: http://codepeople.net
License: GPL
*/


/* initialization / install / uninstall functions */


// Payment Form for PayPal Pro constants

define('CP_PPP_DEFAULT_CURRENCY_SYMBOL','$');
define('CP_PPP_GBP_CURRENCY_SYMBOL',chr(163));
define('CP_PPP_EUR_CURRENCY_SYMBOL_A',chr(226)); //'EUR '
define('CP_PPP_EUR_CURRENCY_SYMBOL_B',chr(128));

define('CP_PPP_DEFAULT_DEFER_SCRIPTS_LOADING', (get_option('CP_CFPP_LOAD_SCRIPTS',"1") == "1"?true:false));
define('CP_PPP_DEFAULT_DEFER_SCRIPTS_LOADING_ADMIN', (get_option('CP_CFPP_LOAD_SCRIPTS_ADMIN',"1") == "1"?true:false));

define('CP_PPP_DEFAULT_form_structure', '[[{"form_identifier":"","name":"email","shortlabel":"","index":0,"ftype":"femail","userhelp":"","userhelpTooltip":false,"csslayout":"","title":"Email","predefined":"","predefinedClick":false,"required":true,"size":"medium","equalTo":"","fBuild":{}}],[{"title":"","description":"","formlayout":"top_aligned","formtemplate":"","evalequations":1,"autocomplete":1}]]');

define('CP_PPP_DEFAULT_fp_subject', 'Payment received...');
define('CP_PPP_DEFAULT_fp_inc_additional_info', 'true');
define('CP_PPP_DEFAULT_fp_return_page', get_site_url());
define('CP_PPP_DEFAULT_fp_message', "The following payment has been received:\n\n<"."%INFO%".">\n\n");

define('CP_PPP_DEFAULT_cu_enable_copy_to_user', 'true');
define('CP_PPP_DEFAULT_cu_user_email_field', '');
define('CP_PPP_DEFAULT_cu_subject', 'Confirmation: Message received...');
define('CP_PPP_DEFAULT_cu_message', "Thank you for your message. We will reply you as soon as possible.\n\nThis is a copy of the data sent:\n\n<"."%INFO%".">\n\nBest Regards.");
define('CP_PPP_DEFAULT_email_format','text');

define('CP_PPP_DEFAULT_vs_use_validation', 'true');

define('CP_PPP_DEFAULT_vs_text_is_required', 'This field is required.');
define('CP_PPP_DEFAULT_vs_text_is_email', 'Please enter a valid email address.');

define('CP_PPP_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with this format(mm/dd/yyyy)');
define('CP_PPP_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with this format(dd/mm/yyyy)');
define('CP_PPP_DEFAULT_vs_text_number', 'Please enter a valid number.');
define('CP_PPP_DEFAULT_vs_text_digits', 'Please enter only digits.');
define('CP_PPP_DEFAULT_vs_text_max', 'Please enter a value less than or equal to {0}.');
define('CP_PPP_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to {0}.');


define('CP_PPP_DEFAULT_cv_enable_captcha', 'true');
define('CP_PPP_DEFAULT_cv_width', '180');
define('CP_PPP_DEFAULT_cv_height', '60');
define('CP_PPP_DEFAULT_cv_chars', '5');
define('CP_PPP_DEFAULT_cv_font', 'font-1.ttf');
define('CP_PPP_DEFAULT_cv_min_font_size', '25');
define('CP_PPP_DEFAULT_cv_max_font_size', '35');
define('CP_PPP_DEFAULT_cv_noise', '200');
define('CP_PPP_DEFAULT_cv_noise_length', '4');
define('CP_PPP_DEFAULT_cv_background', 'ffffff');
define('CP_PPP_DEFAULT_cv_border', '000000');
define('CP_PPP_DEFAULT_cv_text_enter_valid_captcha', 'Please enter a valid captcha code.');

define('CP_PPP_PAYPAL_OPTION_YES', 'Pay with PayPal.');
define('CP_PPP_PAYPAL_OPTION_NO', 'Pay later.');

define('CP_PPP_DEFAULT_ENABLE_PAYPAL', 3);
define('CP_PPP_DEFAULT_PAYPAL_MODE', 'production');
define('CP_PPP_DEFAULT_PAYPAL_RECURRENT', '0');
define('CP_PPP_DEFAULT_PAYPAL_IDENTIFY_PRICES', '0');
define('CP_PPP_DEFAULT_PAYPAL_ZERO_PAYMENT', '0');
define('CP_PPP_DEFAULT_PAYPAL_EMAIL','put_your@email_here.com');
define('CP_PPP_DEFAULT_PRODUCT_NAME','Reservation');
define('CP_PPP_DEFAULT_COST','25');
define('CP_PPP_DEFAULT_CURRENCY','USD');
define('CP_PPP_DEFAULT_PAYPAL_LANGUAGE','EN');

// database
define('CP_PPP_FORMS_TABLE', 'cp_ppp_settings');

define('CP_PPP_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX', "cp_ppp_discount_codes");
define('CP_PPP_DISCOUNT_CODES_TABLE_NAME', @$wpdb->prefix ."cp_ppp_discount_codes");

define('CP_PPP_POSTS_TABLE_NAME_NO_PREFIX', "cp_ppp_posts");
define('CP_PPP_POSTS_TABLE_NAME', @$wpdb->prefix ."cp_ppp_posts");

require_once 'cp_ppp_data_source.inc.php';

// end Payment Form for PayPal Pro constants

// code initialization, hooks
// -----------------------------------------

register_activation_hook(__FILE__,'cp_ppp_install');

add_action( 'init', 'cp_ppp_check_posted_data', 11 );
add_action( 'widgets_init', create_function('', 'return register_widget("CP_PPP_Widget");') );

function cpppp_plugin_init() {
   load_plugin_textdomain( 'cpppp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'cpppp_plugin_init');


if ( is_admin() ) {
    add_action('media_buttons', 'set_cp_ppp_insert_button', 100);
    add_action('admin_enqueue_scripts', 'set_cp_ppp_insert_adminScripts', 1);
    add_action('admin_menu', 'cp_ppp_admin_menu');

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_".$plugin, 'cp_ppp_customAdjustmentsLink');
    add_filter("plugin_action_links_".$plugin, 'cp_ppp_settingsLink');
    add_filter("plugin_action_links_".$plugin, 'cp_ppp_helpLink');

    function cp_ppp_admin_menu() {
        add_options_page('Payment Form for PayPal Pro Options', 'Payment Form for PayPal Pro', 'manage_options', 'cp_ppp', 'cp_ppp_html_post_page' );
        add_menu_page( 'Payment Form for PayPal Pro', 'Payment Form for PayPal Pro', 'read', 'cp_ppp', 'cp_ppp_html_post_page' );
        
        add_submenu_page( 'cp_ppp', 'Manage Forms', 'Manage Forms', 'manage_options', "cp_ppp",  'cp_ppp_html_post_page' );        
        add_submenu_page( 'cp_ppp', 'Upgrade', 'Upgrade', 'edit_pages', "cp_ppp_upgrade", 'cp_ppp_html_post_page' );
    }
} else { // if not admin
    add_shortcode( 'CP_PPP', 'cp_ppp_filter_content' );
    add_shortcode( 'CP_PPP_LIST', 'cp_ppp_filter_list' );
}


// functions
//------------------------------------------

function cp_ppp_install($networkwide)  {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
	                $old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_cp_ppp_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	_cp_ppp_install();
}

function _cp_ppp_install() {
    global $wpdb;

    define('CP_PPP_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
    define('CP_PPP_DEFAULT_fp_destination_emails', CP_PPP_DEFAULT_fp_from_email);

    $table_name = $wpdb->prefix.CP_PPP_FORMS_TABLE;

    $sql = "CREATE TABLE ".$wpdb->prefix.CP_PPP_POSTS_TABLE_NAME_NO_PREFIX." (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         formid INT NOT NULL,
         time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         ipaddr VARCHAR(32) DEFAULT '' NOT NULL,
         notifyto VARCHAR(250) DEFAULT '' NOT NULL,
         data mediumtext,
         paypal_post mediumtext,
         posted_data mediumtext,
         paid INT DEFAULT 0 NOT NULL,
         UNIQUE KEY id (id)
         );";
    $wpdb->query( $wpdb->prepare ($sql, array()) );

    $sql = "CREATE TABLE ".$wpdb->prefix.CP_PPP_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX." (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         form_id mediumint(9) NOT NULL DEFAULT 1,
         code VARCHAR(250) DEFAULT '' NOT NULL,
         discount VARCHAR(250) DEFAULT '' NOT NULL,
         dc_times VARCHAR(10) DEFAULT '0' NOT NULL,         
         expires datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         availability int(10) unsigned NOT NULL DEFAULT 0,
         used int(10) unsigned NOT NULL DEFAULT 0,
         UNIQUE KEY id (id)
         );";
    $wpdb->query( $wpdb->prepare ($sql, array()) );


    $sql = "CREATE TABLE $table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,

         form_name VARCHAR(250) DEFAULT '' NOT NULL,

         form_structure mediumtext,

         fp_from_email VARCHAR(250) DEFAULT '' NOT NULL,
         fp_destination_emails text,
         fp_subject VARCHAR(250) DEFAULT '' NOT NULL,
         fp_inc_additional_info VARCHAR(10) DEFAULT '' NOT NULL,
         fp_return_page VARCHAR(250) DEFAULT '' NOT NULL,
         fp_message text,
         fp_emailformat VARCHAR(10) DEFAULT '' NOT NULL,

         cu_enable_copy_to_user VARCHAR(10) DEFAULT '' NOT NULL,
         cu_user_email_field VARCHAR(250) DEFAULT '' NOT NULL,
         cu_subject VARCHAR(250) DEFAULT '' NOT NULL,
         cu_message text,
         cu_emailformat VARCHAR(10) DEFAULT '' NOT NULL,

         enable_paypal_option_yes VARCHAR(250) DEFAULT '' NOT NULL,
         enable_paypal_option_no VARCHAR(250) DEFAULT '' NOT NULL,
         paypal_recurrent_setup VARCHAR(20) DEFAULT '' NOT NULL,
         vs_use_validation VARCHAR(10) DEFAULT '' NOT NULL,
         vs_text_is_required VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_is_email VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_datemmddyyyy VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_dateddmmyyyy VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_number VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_digits VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_max VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_min VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_submitbtn VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_previousbtn VARCHAR(250) DEFAULT '' NOT NULL,
         vs_text_nextbtn VARCHAR(250) DEFAULT '' NOT NULL,

         enable_paypal varchar(10) DEFAULT '' NOT NULL,
         paypal_notiemails varchar(10) DEFAULT '' NOT NULL,
         paypal_email varchar(255) DEFAULT '' NOT NULL ,         
         request_cost varchar(255) DEFAULT '' NOT NULL ,
         paypal_price_field varchar(255) DEFAULT '' NOT NULL ,
         request_taxes varchar(20) DEFAULT '' NOT NULL ,
         request_address varchar(20) DEFAULT '' NOT NULL ,
         paypal_product_name varchar(255) DEFAULT '' NOT NULL,
         currency varchar(10) DEFAULT '' NOT NULL,
         paypal_language varchar(10) DEFAULT '' NOT NULL,
         paypal_mode varchar(20) DEFAULT '' NOT NULL ,
         paypal_recurrent varchar(20) DEFAULT '' NOT NULL ,
         paypal_identify_prices varchar(20) DEFAULT '' NOT NULL ,
         paypal_zero_payment varchar(10) DEFAULT '' NOT NULL ,
         
         paypalpro_api_username varchar(255) DEFAULT '' NOT NULL ,
         paypalpro_api_password varchar(255) DEFAULT '' NOT NULL ,
         paypalpro_api_signature varchar(255) DEFAULT '' NOT NULL ,
         
         cp_user_access text,
         
         script_load_method varchar(10) DEFAULT '' NOT NULL ,

         cv_enable_captcha VARCHAR(20) DEFAULT '' NOT NULL,
         cv_width VARCHAR(20) DEFAULT '' NOT NULL,
         cv_height VARCHAR(20) DEFAULT '' NOT NULL,
         cv_chars VARCHAR(20) DEFAULT '' NOT NULL,
         cv_font VARCHAR(20) DEFAULT '' NOT NULL,
         cv_min_font_size VARCHAR(20) DEFAULT '' NOT NULL,
         cv_max_font_size VARCHAR(20) DEFAULT '' NOT NULL,
         cv_noise VARCHAR(20) DEFAULT '' NOT NULL,
         cv_noise_length VARCHAR(20) DEFAULT '' NOT NULL,
         cv_background VARCHAR(20) DEFAULT '' NOT NULL,
         cv_border VARCHAR(20) DEFAULT '' NOT NULL,
         cv_text_enter_valid_captcha VARCHAR(200) DEFAULT '' NOT NULL,

         UNIQUE KEY id (id)
         );";
    $wpdb->query( $wpdb->prepare ($sql, array()) );

    $count = $wpdb->get_var(  "SELECT COUNT(id) FROM ".$table_name  );
    if (!$count)
    {
        $wpdb->insert( $table_name, array( 'id' => 1,
                                      'form_name' => 'Form 1',

                                      'form_structure' => cp_ppp_get_option('form_structure', CP_PPP_DEFAULT_form_structure),

                                      'fp_from_email' => cp_ppp_get_option('fp_from_email', CP_PPP_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => cp_ppp_get_option('fp_destination_emails', CP_PPP_DEFAULT_fp_destination_emails),
                                      'fp_subject' => cp_ppp_get_option('fp_subject', CP_PPP_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => cp_ppp_get_option('fp_inc_additional_info', CP_PPP_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => cp_ppp_get_option('fp_return_page', CP_PPP_DEFAULT_fp_return_page),
                                      'fp_message' => cp_ppp_get_option('fp_message', CP_PPP_DEFAULT_fp_message),
                                      'fp_emailformat' => cp_ppp_get_option('fp_emailformat', CP_PPP_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => cp_ppp_get_option('cu_enable_copy_to_user', CP_PPP_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => cp_ppp_get_option('cu_user_email_field', CP_PPP_DEFAULT_cu_user_email_field),
                                      'cu_subject' => cp_ppp_get_option('cu_subject', CP_PPP_DEFAULT_cu_subject),
                                      'cu_message' => cp_ppp_get_option('cu_message', CP_PPP_DEFAULT_cu_message),
                                      'cu_emailformat' => cp_ppp_get_option('cu_emailformat', CP_PPP_DEFAULT_email_format),

                                      'vs_use_validation' => cp_ppp_get_option('vs_use_validation', CP_PPP_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => cp_ppp_get_option('vs_text_is_required', CP_PPP_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => cp_ppp_get_option('vs_text_is_email', CP_PPP_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => cp_ppp_get_option('vs_text_datemmddyyyy', CP_PPP_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => cp_ppp_get_option('vs_text_dateddmmyyyy', CP_PPP_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => cp_ppp_get_option('vs_text_number', CP_PPP_DEFAULT_vs_text_number),
                                      'vs_text_digits' => cp_ppp_get_option('vs_text_digits', CP_PPP_DEFAULT_vs_text_digits),
                                      'vs_text_max' => cp_ppp_get_option('vs_text_max', CP_PPP_DEFAULT_vs_text_max),
                                      'vs_text_min' => cp_ppp_get_option('vs_text_min', CP_PPP_DEFAULT_vs_text_min),
                                      'vs_text_submitbtn' => cp_ppp_get_option('vs_text_submitbtn', 'Submit'),
                                      'vs_text_previousbtn' => cp_ppp_get_option('vs_text_previousbtn', 'Previous'),
                                      'vs_text_nextbtn' => cp_ppp_get_option('vs_text_nextbtn', 'Next'),                                      
                                      
                                      'script_load_method' => cp_ppp_get_option('script_load_method', '0'),

                                      'enable_paypal' => cp_ppp_get_option('enable_paypal', CP_PPP_DEFAULT_ENABLE_PAYPAL),
                                      'paypal_notiemails' => cp_ppp_get_option('paypal_notiemails', '0'),
                                      'paypal_email' => cp_ppp_get_option('paypal_email', CP_PPP_DEFAULT_PAYPAL_EMAIL),
                                      'request_cost' => cp_ppp_get_option('request_cost', CP_PPP_DEFAULT_COST),
                                      'paypal_price_field' => cp_ppp_get_option('paypal_price_field', ''),
                                      'request_taxes' => cp_ppp_get_option('request_taxes', '0'),                                      
                                      'request_address' => cp_ppp_get_option('request_address', '0'),                                      
                                      'paypal_product_name' => cp_ppp_get_option('paypal_product_name', CP_PPP_DEFAULT_PRODUCT_NAME),
                                      'currency' => cp_ppp_get_option('currency', CP_PPP_DEFAULT_CURRENCY),
                                      'paypal_language' => cp_ppp_get_option('paypal_language', CP_PPP_DEFAULT_PAYPAL_LANGUAGE),
                                      'paypal_mode' => cp_ppp_get_option('paypal_mode', CP_PPP_DEFAULT_PAYPAL_MODE),
                                      'paypal_recurrent' => cp_ppp_get_option('paypal_recurrent', CP_PPP_DEFAULT_PAYPAL_RECURRENT),
                                      'paypal_identify_prices' => cp_ppp_get_option('paypal_identify_prices', CP_PPP_DEFAULT_PAYPAL_IDENTIFY_PRICES),
                                      'paypal_zero_payment' => cp_ppp_get_option('paypal_zero_payment', CP_PPP_DEFAULT_PAYPAL_ZERO_PAYMENT),

                                      'cv_enable_captcha' => cp_ppp_get_option('cv_enable_captcha', CP_PPP_DEFAULT_cv_enable_captcha),
                                      'cv_width' => cp_ppp_get_option('cv_width', CP_PPP_DEFAULT_cv_width),
                                      'cv_height' => cp_ppp_get_option('cv_height', CP_PPP_DEFAULT_cv_height),
                                      'cv_chars' => cp_ppp_get_option('cv_chars', CP_PPP_DEFAULT_cv_chars),
                                      'cv_font' => cp_ppp_get_option('cv_font', CP_PPP_DEFAULT_cv_font),
                                      'cv_min_font_size' => cp_ppp_get_option('cv_min_font_size', CP_PPP_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => cp_ppp_get_option('cv_max_font_size', CP_PPP_DEFAULT_cv_max_font_size),
                                      'cv_noise' => cp_ppp_get_option('cv_noise', CP_PPP_DEFAULT_cv_noise),
                                      'cv_noise_length' => cp_ppp_get_option('cv_noise_length', CP_PPP_DEFAULT_cv_noise_length),
                                      'cv_background' => cp_ppp_get_option('cv_background', CP_PPP_DEFAULT_cv_background),
                                      'cv_border' => cp_ppp_get_option('cv_border', CP_PPP_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => cp_ppp_get_option('cv_text_enter_valid_captcha', CP_PPP_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );
    }

}

function cp_ppp_filter_list($atts) {
    global $wpdb;
    extract( shortcode_atts( array(
		'id' => '',
		'from' => 'today -30 days',
		'to' => 'today +30 days',
		'fields' => 'time,email,fieldname1,fieldname2',
	), $atts ) );    
    ob_start();
    
    $from = date("Y-m-d 00:00:00", strtotime($from));
	$to = date("Y-m-d 23:59:59", strtotime($to));
	$cond = "(`time` >= '".esc_sql($from)."')";
	$cond .= " AND (`time` <= '".esc_sql($to)."')";	

    if ($id != '')
        $myrows = $wpdb->get_results(  $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.CP_PPP_FORMS_TABLE." WHERE id=%d",$id) );
    else
        $myrows = $wpdb->get_results(  $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.CP_PPP_FORMS_TABLE,array()) );	
        
        
	if ($id == '') $id = $myrows[0]->id;  	
	$cond = "(`formid` = %d)";
	
    $events = $wpdb->get_results( $wpdb->prepare(
                                                 "SELECT * FROM ".CP_PPP_POSTS_TABLE_NAME." WHERE ".$cond." ORDER BY `time` DESC",
                                                 $id
                                                 )  
                                 );
    
    $fields = explode(",",$fields);
    
    for ($k=0; $k<count($fields); $k++)
        $fields[$k] = trim($fields[$k]);
                
    wp_enqueue_style ('cp_ppp_buikder_script_f_list_styles', plugins_url('css/stylepublic.css', __FILE__));
    foreach ($events as $event)
    {
        $posted_data = unserialize($event->posted_data);		             
        
	    for ($k=0;$k<count($fields); $k++)	 
	       if ($fields[$k] == 'time')
	           echo '<div class="cfpp_field_'.$k.'">'.date("Y-m-d H:i:s", strtotime($event->time) ).'</div>';
	       else   
	           echo '<div class="cfpp_field_'.$k.'">'.(isset($posted_data[$fields[$k]])?$posted_data[$fields[$k]]:"&nbsp;").'</div>';
	    echo '<div class="cfpp_field_clear"></div>';   
    }
    
    $buffered_contents = ob_get_contents();
    ob_end_clean();
    return $buffered_contents;
}

function cp_ppp_available_templates(){	
	global $CP_CFPP_global_templates;
	
	if( empty( $CP_CFPP_global_templates ) )
	{
		// Get available designs
		$tpls_dir = dir( plugin_dir_path( __FILE__ ).'templates' );
		$CP_CFPP_global_templates = array();
		while( false !== ( $entry = $tpls_dir->read() ) ) 
		{    
			if ( $entry != '.' && $entry != '..' && is_dir( $tpls_dir->path.'/'.$entry ) && file_exists( $tpls_dir->path.'/'.$entry.'/config.ini' ) )
			{
				if( ( $ini_array = parse_ini_file( $tpls_dir->path.'/'.$entry.'/config.ini' ) ) !== false )
				{
					if( !empty( $ini_array[ 'file' ] ) ) $ini_array[ 'file' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'file' ], __FILE__ );
					if( !empty( $ini_array[ 'thumbnail' ] ) ) $ini_array[ 'thumbnail' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'thumbnail' ], __FILE__ );
					$CP_CFPP_global_templates[ $ini_array[ 'prefix' ] ] = $ini_array;
				}
			}			
		}
	}
		
	return $CP_CFPP_global_templates;
}	

function cp_ppp_filter_content($atts) {
    global $wpdb;
    extract( shortcode_atts( array(
		'id' => '',
	), $atts ) );
    //if ($id != '')
    //    define ('CP_PPP_ID',$id);
    ob_start();
    cp_ppp_get_public_form($id);
    $buffered_contents = ob_get_contents();
    ob_end_clean();
    return $buffered_contents;
}

$CP_CFPP_global_form_count_number = 0;
$CP_CPP_global_form_count = "_".$CP_CFPP_global_form_count_number;

function cp_ppp_get_public_form($id) {
    global $wpdb;
    global $CP_CPP_global_form_count;
    global $CP_CFPP_global_form_count_number;
    $CP_CFPP_global_form_count_number++;
    $CP_CPP_global_form_count = "_".$CP_CFPP_global_form_count_number;  
    if (!defined('CP_AUTH_INCLUDE')) define('CP_AUTH_INCLUDE', true);

    if ($id != '')
        $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.CP_PPP_FORMS_TABLE." WHERE id=%d", $id) );
    else
        $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.CP_PPP_FORMS_TABLE, array()) );
        
    $previous_label = cp_ppp_get_option('vs_text_previousbtn', 'Previous',$id);
    $previous_label = ($previous_label==''?'Previous':$previous_label);
    $next_label = cp_ppp_get_option('vs_text_nextbtn', 'Next',$id);
    $next_label = ($next_label==''?'Next':$next_label);  
    if ($id == '') $id = $myrows[0]->id;        

    wp_deregister_script('query-stringify');
    wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));
    
    wp_deregister_script('cp_ppp_validate_script');
    wp_register_script('cp_ppp_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));
    
    wp_enqueue_script( 'cp_ppp_buikder_script',
    get_site_url( get_current_blog_id() ).'?cp_ppp_resources=public',array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify","cp_ppp_validate_script"), false, true );
    
    
    wp_localize_script('cp_ppp_buikder_script', 'cp_ppp_fbuilder_config'.$CP_CPP_global_form_count, array('obj'  	=>
    '{"pub":true,"identifier":"'.$CP_CPP_global_form_count.'","messages": {
    	                	"required": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_is_required', CP_PPP_DEFAULT_vs_text_is_required,$id)).'",
    	                	"email": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_is_email', CP_PPP_DEFAULT_vs_text_is_email,$id)).'",
    	                	"datemmddyyyy": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_datemmddyyyy', CP_PPP_DEFAULT_vs_text_datemmddyyyy,$id)).'",
    	                	"dateddmmyyyy": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_dateddmmyyyy', CP_PPP_DEFAULT_vs_text_dateddmmyyyy,$id)).'",
    	                	"number": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_number', CP_PPP_DEFAULT_vs_text_number,$id)).'",
    	                	"digits": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_digits', CP_PPP_DEFAULT_vs_text_digits,$id)).'",
    	                	"max": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_max', CP_PPP_DEFAULT_vs_text_max,$id)).'",
    	                	"min": "'.str_replace(array('"'),array('\\"'),cp_ppp_get_option('vs_text_min', CP_PPP_DEFAULT_vs_text_min,$id)).'",
                     	"previous": "'.str_replace(array('"'),array('\\"'),$previous_label).'",
                     	"next": "'.str_replace(array('"'),array('\\"'),$next_label).'"
    	                }}'
    ));
  
    
    wp_enqueue_style ('cp_ppp_buikder_script_f_p_styles', plugins_url('css/stylepublic.css', __FILE__));
    wp_enqueue_style('cp_ppp_buikder_script_jq_styles', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__));  
    
    $codes = array();

    $button_label = cp_ppp_get_option('vs_text_submitbtn', 'Submit',$id);
    $button_label = ($button_label==''?'Submit':$button_label);
    @include dirname( __FILE__ ) . '/cp_ppp_public_int.inc.php';
}


function cp_ppp_settingsLink($links) {
    $settings_link = '<a href="options-general.php?page=cp_ppp">'.__('Settings').'</a>';
	array_unshift($links, $settings_link);
	return $links;
}


function cp_ppp_helpLink($links) {
    $help_link = '<a href="http://wordpress.dwbooster.com/forms/cp-contact-form-with-paypal">'.__('Help').'</a>';
	array_unshift($links, $help_link);
	return $links;
}


function cp_ppp_customAdjustmentsLink($links) {
    $customAdjustments_link = '<a href="http://wordpress.dwbooster.com/contact-us">'.__('Request custom changes').'</a>';
	array_unshift($links, $customAdjustments_link);
	return $links;
}


function set_cp_ppp_insert_button() {
    print '<a href="javascript:cp_ppp_insertForm();" title="'.__('Insert Payment Form for PayPal Pro').'"><img hspace="5" src="'.plugins_url('/images/cp_form.gif', __FILE__).'" alt="'.__('Insert Payment Form for PayPal Pro').'" /></a>';
}


function cp_ppp_html_post_page() {
    if (isset($_GET["cal"]) && $_GET["cal"] != '')
    {
        if (isset($_GET["list"]) && $_GET["list"] == '1')
            @include_once dirname( __FILE__ ) . '/cp_ppp_admin_int_message_list.inc.php';
        else if (current_user_can('manage_options'))
            @include_once dirname( __FILE__ ) . '/cp_ppp_admin_int.php';
        else 
            echo 'Current user permissions aren\'t enough for accesing this page.';
    }
    else
    {
        if (isset($_GET["page"]) &&$_GET["page"] == 'cp_ppp_upgrade')
        {
            echo("Redirecting to upgrade page...<script type='text/javascript'>document.location='http://wordpress.dwbooster.com/forms/paypal-payment-pro-form#download';</script>");
            exit;
        } 
        else if (isset($_GET["page"]) &&$_GET["page"] == 'cp_ppp_demo')
        {
            echo("Redirecting to demo page...<script type='text/javascript'>document.location='http://wordpress.dwbooster.com/forms/cp-contact-form-with-paypal#demo';</script>");
            exit;
        } 
        else        
            @include_once dirname( __FILE__ ) . '/cp_ppp_admin_int_list.inc.php';
    }    
}


function set_cp_ppp_insert_adminScripts($hook) {
    if (isset($_GET["page"]) && $_GET["page"] == "cp_ppp")
    {
        wp_deregister_script('query-stringify');                                                                                
        wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));                                
        wp_enqueue_script( 'cp_ppp_buikder_script', get_site_url( get_current_blog_id() ).'?cp_ppp_resources=admin',array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button","jquery-ui-datepicker","query-stringify") );
        
        
        wp_enqueue_style('cp_ppp_buikder_script_f_styles', plugins_url('css/style.css', __FILE__));
        wp_enqueue_style('cp_ppp_buikder_script_jq_styles', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__));
    }

    if( 'post.php' != $hook  && 'post-new.php' != $hook )
        return;
    wp_enqueue_script( 'cp_ppp_script', plugins_url('/cp_ppp_scripts.js', __FILE__) );
}


function cp_ppp_get_site_url($admin = false)
{
    $blog = get_current_blog_id();
    if( $admin ) 
        $url = get_admin_url( $blog );	
    else 
        $url = get_home_url( $blog );	

    $url = parse_url($url);
    $url = rtrim(@$url["path"],"/");
    return $url;
}

function cp_ppp_get_FULL_site_url($admin = false)
{
    $url = cp_ppp_get_site_url($admin);
    $pos = strpos($url, "://");    
    if ($pos === false)
        $url = 'http://'.$_SERVER["HTTP_HOST"].$url;
//    if (!empty($_SERVER['HTTPS']))     
//        $url = str_replace("http://","https://",$url);        
    return $url;
}

function cp_ppp_cleanJSON($str)
{
    $str = str_replace('&qquot;','"',$str);
    $str = str_replace('	',' ',$str);
    $str = str_replace("\n",'\n',$str);
    $str = str_replace("\r",'',$str);
    return $str;
}


function cp_ppp_load_discount_codes() {
    global $wpdb;

    if ( ! current_user_can('edit_pages') ) // prevent loading coupons from outside admin area
    {
        echo 'No enough privilegies to load this content.';
        exit;
    }

    if (!defined('CP_PPP_ID'))
        define ('CP_PPP_ID',intval($_GET["dex_item"]));

    cp_ppp_add_field_verify($wpdb->prefix.CP_PPP_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX ,"dc_times", "varchar(10) DEFAULT '0' NOT NULL");     
    
    if (isset($_GET["add"]) && $_GET["add"] == "1")
        $wpdb->insert( CP_PPP_DISCOUNT_CODES_TABLE_NAME, array('form_id' => CP_PPP_ID,
                                                                         'code' => esc_sql($_GET["code"]),
                                                                         'discount' => $_GET["discount"],
                                                                         'availability' => $_GET["discounttype"],
                                                                         'dc_times' => $_GET["tm"],
                                                                         'expires' => esc_sql($_GET["expires"]),
                                                                         ));

    if (isset($_GET["delete"]) && $_GET["delete"] == "1")
        $wpdb->query( $wpdb->prepare( "DELETE FROM ".CP_PPP_DISCOUNT_CODES_TABLE_NAME." WHERE id = %d", $_GET["code"] ));

    $codes = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '.CP_PPP_DISCOUNT_CODES_TABLE_NAME.' WHERE `form_id`=%d', CP_PPP_ID) );
    if (count ($codes))
    {
        echo '<table>';
        echo '<tr>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Cupon Code</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Discount</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Type</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;" nowrap>Can be used?</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;" nowrap>Used so far</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Valid until</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Options</th>';
        echo '</tr>';
        foreach ($codes as $value)
        {
           echo '<tr>';
           echo '<td>'.$value->code.'</td>';
           echo '<td>'.$value->discount.'</td>';
           echo '<td>'.($value->availability==1?"Fixed Value":"Percent").'</td>';
           echo '<td nowrap>'.($value->dc_times=='0'?'Unlimited':$value->dc_times.' times').'</td>';
           echo '<td nowrap>'.$value->used.' times</td>';
           echo '<td>'.substr($value->expires,0,10).'</td>';
           echo '<td>[<a href="javascript:dex_delete_coupon('.$value->id.')">Delete</a>]</td>';
           echo '</tr>';
        }
        echo '</table>';
    }
    else
        echo 'No discount codes listed for this form yet.';
    exit;
}


function cp_ppp_check_posted_data() {

    global $wpdb;


	if( isset( $_REQUEST[ 'cp_ppp_resources' ] ) )
	{
		if( $_REQUEST[ 'cp_ppp_resources' ] == 'admin' )
		{
			require_once dirname( __FILE__ ).'/js/fbuilder-loader-admin.php';
		}
		else
		{
			require_once dirname( __FILE__ ).'/js/fbuilder-loader-public.php';
		}
		exit;
	}		

    if (isset( $_GET['cp_ppp_encodingfix'] ) && $_GET['cp_ppp_encodingfix'] == '1')
    {		
        $wpdb->query( $wpdb->prepare('alter table '.CP_PPP_DISCOUNT_CODES_TABLE_NAME.' convert to character set utf8 collate utf8_unicode_ci;', array()) );
        $wpdb->query( $wpdb->prepare('alter table '.CP_PPP_FORMS_TABLE.' convert to character set utf8 collate utf8_unicode_ci;', array()) );
        $wpdb->query( $wpdb->prepare('alter table '.CP_PPP_POSTS_TABLE_NAME.' convert to character set utf8 collate utf8_unicode_ci;', array()) ); 
        echo 'Ok, encoding fixed.';
        exit;		
    }    

    if(isset($_GET) && array_key_exists('cp_ppp_post',$_GET)) {
        if ($_GET["cp_ppp_post"] == 'loadcoupons')
            cp_ppp_load_discount_codes();            
    }
    
    if (isset( $_GET['cp_ppp'] ) && $_GET['cp_ppp'] == 'captcha' )
    {
        @include_once dirname( __FILE__ ) . '/captcha/captcha.php';            
        exit;        
    }        

    if (isset( $_GET['cp_ppp_csv'] ) && is_admin() )
    {
        cp_ppp_export_csv();
        return;
    }
    
    if (isset( $_GET['script_load_method'] ) )
    {
        cp_ppp_update_script_method();
        return;
    }    

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['cp_ppp_post_options'] ) && is_admin() )
    {
        cp_ppp_save_options();
        return;
    }

	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cp_ppp_pform_process'] ) )
	    if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_cp_ppp_post'] ) )
		    return;

    if (isset($_GET["cp_ppp_id"])) $_POST["cp_ppp_id"] = intval($_GET["cp_ppp_id"]);
    if (isset($_POST["cp_ppp_id"])) define("CP_PPP_ID",intval($_POST["cp_ppp_id"]));

    @session_start();
    if (isset($_GET["ps"])) $sequence = $_GET["ps"]; else if (isset($_POST["cp_pform_psequence"])) $sequence = $_POST["cp_pform_psequence"];
    if (!isset($_GET['hdcaptcha_cp_ppp_post']) || $_GET['hdcaptcha_cp_ppp_post'] == '') $_GET['hdcaptcha_cp_ppp_post'] = @$_POST['hdcaptcha_cp_ppp_post'];
    if (
           (cp_ppp_get_option('cv_enable_captcha', CP_PPP_DEFAULT_cv_enable_captcha) != 'false') &&
           ( (strtolower($_GET['hdcaptcha_cp_ppp_post']) != strtolower(@$_SESSION['rand_code'.$sequence])) ||
             ($_SESSION['rand_code'.$sequence] == '')
           )
           &&
           ( (md5(strtolower($_GET['hdcaptcha_cp_ppp_post'])) != $_COOKIE['rand_code'.$sequence]) ||
             ($_COOKIE['rand_code'.$sequence] == '')
           )
       )
    {
        echo 'captchafailed';
        exit;
    }

	// if this isn't the real post (it was the captcha verification) then echo ok and exit
    if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cp_ppp_pform_process'] ) )
	{
	    echo 'ok';
        exit;
	}


	// get base price
    $price = cp_ppp_get_option('request_cost', CP_PPP_DEFAULT_COST);
    $price = trim(str_replace(',','', str_replace(CP_PPP_DEFAULT_CURRENCY_SYMBOL,'', 
                                     str_replace(CP_PPP_GBP_CURRENCY_SYMBOL,'', 
                                     str_replace(CP_PPP_EUR_CURRENCY_SYMBOL_A, '',
                                     str_replace(CP_PPP_EUR_CURRENCY_SYMBOL_B,'', $price )))) ));     
    $added_cost = @$_POST[cp_ppp_get_option('paypal_price_field', '').$sequence];
    $added_cost = str_replace('$','',$added_cost);
    $added_cost = str_replace('USD','',$added_cost);
    $added_cost = str_replace('EUR','',$added_cost);
    $added_cost = str_replace('GBP','',$added_cost);
    $added_cost = trim($added_cost);    
    if (!is_numeric($added_cost))
        $added_cost = 0;
    $price += $added_cost;    
    $taxes = trim(str_replace("%","",cp_ppp_get_option('request_taxes', '0')));

    // get form info
    //---------------------------
    $identify_prices = cp_ppp_get_option('paypal_identify_prices',CP_PPP_DEFAULT_PAYPAL_IDENTIFY_PRICES);
    $paypal_zero_payment = cp_ppp_get_option('paypal_zero_payment',CP_PPP_DEFAULT_PAYPAL_ZERO_PAYMENT);
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    $form_data = json_decode(cp_ppp_cleanJSON(cp_ppp_get_option('form_structure', CP_PPP_DEFAULT_form_structure)));
    $fields = array();
    foreach ($form_data[0] as $item)
    {
        $fields[$item->name] = $item->title;
        if ($item->predefined == $_POST[$item->name.$sequence] && $item->predefinedClick == '1')
            $_POST[$item->name.$sequence] = '';           
        if ($item->ftype == 'fPhone') // join fields for phone fields
        {
            if (isset($_POST[$item->name.$sequence."_0"]))
            {            
                $_POST[$item->name.$sequence] = '';
                for($i=0; $i<=substr_count($item->dformat," "); $i++)
                {
                    $_POST[$item->name.$sequence] .= ($_POST[$item->name.$sequence."_".$i]!=''?($i==0?'':'-').$_POST[$item->name.$sequence."_".$i]:'');
                    unset($_POST[$item->name.$sequence."_".$i]);
                }   
            } 
        }
        else if (isset($_POST[$item->name.$sequence]) && $identify_prices && ($item->ftype == 'fcheck' || $item->ftype == 'fradio' || $item->ftype == 'fdropdown'))
        {
            $values = ( is_array($_POST[$item->name.$sequence]) ? $_POST[$item->name.$sequence] : array($_POST[$item->name.$sequence]) );            
            
            foreach ($values as $value) 
            {
                $matches_eur_a = array();
                preg_match_all ('/([0-9,]+(\.[0-9]{2})?)/', $value, $matches_eur_a);                        
                $matches = $matches_eur_a[0]; 
                           
                foreach ($matches as $item)
                {
                    $item = trim(str_replace(',','', str_replace(CP_PPP_DEFAULT_CURRENCY_SYMBOL,'', 
                                                     str_replace(CP_PPP_GBP_CURRENCY_SYMBOL,'', 
                                                     str_replace(CP_PPP_EUR_CURRENCY_SYMBOL_A, '',
                                                     str_replace(CP_PPP_EUR_CURRENCY_SYMBOL_B,'', $item )))) ));                                               
                    if (is_numeric($item)) {
                        $nindex = strpos($value, " ".$item);
                        $nindex2 = substr($value,0,strlen($item));
                        if ($nindex === false && $nindex2 != $item) // exclude items with a black space before it
                            $price += $item;
                    } 
                }
            }
        }
    }

    // calculate discounts if any
    //---------------------------
    $discount_note = "";
    $coupon = false;
    $codes = $wpdb->get_results( $wpdb->prepare(
                                                 "SELECT * FROM ".CP_PPP_DISCOUNT_CODES_TABLE_NAME." WHERE ((dc_times>used) OR dc_times='0') AND  code=%s AND expires>=%s AND `form_id`=%d",
                                                 @$_POST["couponcode"], date("Y-m-d")." 00:00:00", CP_PPP_ID
                                                )
                                );
    if (count($codes))
    {
        $coupon = $codes[0];
        if ($coupon->availability==1)
        {
            $price = number_format (floatval ($price) - $coupon->discount,2);
            $discount_note = " (".cp_ppp_get_option('currency', CP_PPP_DEFAULT_CURRENCY)." ".$coupon->discount." discount applied)";
        }    
        else
        {
            $price = number_format (floatval ($price) - $price*$coupon->discount/100,2);
            $discount_note = " (".$coupon->discount."% discount applied)";
        }
    }

    if (  cp_ppp_get_option('enable_paypal',CP_PPP_DEFAULT_ENABLE_PAYPAL) == "3" && ($_POST['cp_ppp_paymentspro'.$sequence] == "1") )
    {                                            
        cp_ppp_payments_pro($price);
        exit;
    }
    
    // grab posted data
    //---------------------------
    $buffer = "";
    foreach ($_POST as $item => $value)
        if (isset($fields[str_replace($sequence,'',$item)]))
        {
            $buffer .= $fields[str_replace($sequence,'',$item)] . ": ". (is_array($value)?(implode(", ",$value)):($value)) . "\n\n";
            $params[str_replace($sequence,'',$item)] = $value;
        }
       
    $buffer_A = $buffer;

    $paypal_product_name = cp_ppp_get_option('paypal_product_name', CP_PPP_DEFAULT_PRODUCT_NAME).$discount_note;
    $params["PayPal Product Name"] = $paypal_product_name; 
    $params["Cost"] = $price;
    $params["Costtax"] = $price + round($price * ($taxes/100),2);
    $params["coupon"] = ($coupon?$coupon->code:"");
    
    $current_user = wp_get_current_user();
    $params["user_login"] = $current_user->user_login;
    $params["user_id"] = $current_user->ID;
    $params["user_email"] = $current_user->user_email;
    $params["user_firstname"] = $current_user->user_firstname; 
    $params["user_lastname"] = $current_user->user_lastname; 
    $params["display_name"] = $current_user->display_name;     
    
    if (isset($_POST["bccf_payment_option_paypal"]) && $_POST["bccf_payment_option_paypal"] == '0')
        $params["payment_type"] = 'Other';
    else    
        $params["payment_type"] = 'PayPal';
    
    cp_ppp_add_field_verify(CP_PPP_POSTS_TABLE_NAME,'posted_data');

    // insert into database
    //---------------------------
    $to = cp_ppp_get_option('cu_user_email_field', CP_PPP_DEFAULT_cu_user_email_field).$sequence;    
    $rows_affected = $wpdb->insert( CP_PPP_POSTS_TABLE_NAME, array( 'formid' => CP_PPP_ID,
                                                                        'time' => current_time('mysql'),
                                                                        'ipaddr' => $_SERVER['REMOTE_ADDR'],
                                                                        'notifyto' => @$_POST[$to],
                                                                        'paypal_post' => serialize($params),
                                                                        'posted_data' => serialize($params),
                                                                        'data' =>$buffer_A .($coupon?"\n\nCoupon code:".$coupon->code.$discount_note:"")
                                                                         ) );
    if (!$rows_affected)
    {
        echo 'Error saving data! Please try again.';
        echo '<br /><br />Error debug information: '.mysql_error();
        exit;
    }

    $myrows = $wpdb->get_results( "SELECT MAX(id) as max_id FROM ".CP_PPP_POSTS_TABLE_NAME );


 	// save data here
    $item_number = $myrows[0]->max_id;
    
    $paypal_optional = (cp_ppp_get_option('enable_paypal',CP_PPP_DEFAULT_ENABLE_PAYPAL) == '2');
    $paypal_recurrent = cp_ppp_get_option('paypal_recurrent_setup','0');  
        
    //if (cp_ppp_get_option('enable_paypal',CP_PPP_DEFAULT_ENABLE_PAYPAL) == "3")                                                                        
    $wpdb->query( $wpdb->prepare(
                                  "UPDATE ".CP_PPP_POSTS_TABLE_NAME." SET paid=1,paypal_post='' WHERE id=%d",
                                  $item_number
                                 ) 
                 );        
    
    cp_ppp_process_ready_to_go_reservation($item_number, "", $params);     
    header("Location: ".cp_ppp_get_option('fp_return_page', CP_PPP_DEFAULT_fp_return_page));
    exit;

}

function cp_ppp_add_field_verify ($table, $field, $type = "text") 
{
    global $wpdb;
    $results = $wpdb->get_results( $wpdb->prepare(
                                                   "SHOW columns FROM `".$table."` where field=%s",
                                                   $field
                                                  )
                                  );    
    if (!count($results))
    {               
        $sql = "ALTER TABLE  `".$table."` ADD `".$field."` ".$type; 
        $wpdb->query($sql);
    }
}

function cp_ppp_check_upload($uploadfiles) {
    $filetmp = $uploadfiles['tmp_name'];
    //clean filename and extract extension
    $filename = $uploadfiles['name'];
    // get file info
    $filetype = wp_check_filetype( basename( $filename ), null );

    if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe")) )
        return false;
    else
        return true;
}

function cp_ppp_payments_pro_POST($methodName_, $nvpStr_) {
    global $wpdb;
    

	// Set up your API credentials, PayPal end point, and API version.
	$API_UserName = urlencode(cp_ppp_get_option('paypalpro_api_username',''));
	$API_Password = urlencode(cp_ppp_get_option('paypalpro_api_password',''));
	$API_Signature = urlencode(cp_ppp_get_option('paypalpro_api_signature',''));
    if (cp_ppp_get_option('paypal_mode',CP_PPP_DEFAULT_PAYPAL_MODE) == "sandbox")
        $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    else
        $API_Endpoint = "https://api-3t.paypal.com/nvp";		
	$version = urlencode('51.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// Get response from the server.
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;    
}

function cp_ppp_payments_pro($price) {
    global $wpdb;
 
    // Set request-specific fields.
    $paymentType = urlencode('Sale');				// or 'Authorization'
    
    $firstName = urlencode($_POST['cfpp_customer_first_name']);
    $lastName = urlencode($_POST['cfpp_customer_last_name']);
    $creditCardType = urlencode($_POST['cfpp_customer_credit_card_type']);
    $creditCardNumber = urlencode($_POST['cfpp_customer_credit_card_number']);
    $expDateMonth = $_POST['cfpp_cc_expiration_month'];
    // Month must be padded with leading zero
    $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
    
    $expDateYear = urlencode($_POST['cfpp_cc_expiration_year']);
    $cvv2Number = urlencode($_POST['cfpp_cc_cvv2_number']);
    $address1 = urlencode($_POST['cfpp_customer_address1']);
    $address2 = urlencode($_POST['cfpp_customer_address2']);
    $city = urlencode($_POST['cfpp_customer_city']);
    $state = urlencode($_POST['cfpp_customer_state']);
    $zip = urlencode($_POST['cfpp_customer_zip']);
    $country = urlencode($_POST['cfpp_customer_country']);				// US or other valid country code
    
    $amount = urlencode($price);
    $currencyID = urlencode(strtoupper(cp_ppp_get_option('currency', CP_PPP_DEFAULT_CURRENCY)));
    
    // Add request-specific fields to the request string.
    $nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
    			"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
    			"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID&BUTTONSOURCE=NetFactorSL_SI_Custom";
    
    // Execute the API operation; see the PPHttpPost function above.
    $httpParsedResponseAr = cp_ppp_payments_pro_POST('DoDirectPayment', $nvpStr);
    foreach ($httpParsedResponseAr as $item => $value)
        $httpParsedResponseAr[$item] = urldecode($value);
    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
    	exit('OK');
    } else  {
    	exit("Payment failed: ".$httpParsedResponseAr["L_LONGMESSAGE0"]."\n\nError Code: " . $httpParsedResponseAr["L_ERRORCODE0"]." (". $httpParsedResponseAr["L_SHORTMESSAGE0"].")");
    }
    
}


function cp_ppp_process_ready_to_go_reservation($itemnumber, $payer_email = "", $params = array())
{

   global $wpdb;
   
    if (!defined('CP_PPP_DEFAULT_fp_from_email'))  define('CP_PPP_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
    if (!defined('CP_PPP_DEFAULT_fp_destination_emails')) define('CP_PPP_DEFAULT_fp_destination_emails', CP_PPP_DEFAULT_fp_from_email);

   $myrows = $wpdb->get_results( $wpdb->prepare(
                                                 "SELECT * FROM ".CP_PPP_POSTS_TABLE_NAME." WHERE id=%d",
                                                 $itemnumber
                                                )
                                );

   $mycalendarrows = $wpdb->get_results( $wpdb->prepare( 
                                                        'SELECT * FROM '. $wpdb->prefix.CP_PPP_FORMS_TABLE .' WHERE `id`=%d',
                                                        $myrows[0]->formid
                                                        )
                                        );                                                        

   if (!defined('CP_PPP_ID'))
        define ('CP_PPP_ID',$myrows[0]->formid);

    $buffer_A = $myrows[0]->data;
    $buffer = $buffer_A;

    if ($params["coupon"] != '')
      $wpdb->query( $wpdb->prepare(
                                    "UPDATE ".CP_PPP_DISCOUNT_CODES_TABLE_NAME." SET used=used+1 WHERE code=%s AND expires>=%s AND `form_id`=%d",
                                    @$params["coupon"], date("Y-m-d")." 00:00:00", $myrows[0]->formid
                                   ) 
                   );    

    if ('true' == cp_ppp_get_option('fp_inc_additional_info', CP_PPP_DEFAULT_fp_inc_additional_info))
    {
        $buffer .="ADDITIONAL INFORMATION\n"
              ."*********************************\n"
              ."IP: ".$myrows[0]->ipaddr."\n"
              ."Server Time:  ".date("Y-m-d H:i:s")."\n";
    }

    // 1- Send email
    //---------------------------
   
   /**     
    $username = "user".$itemnumber;
    $password = wp_generate_password( $length=12, $include_standard_special_chars=false );
    $email = trim($myrows[0]->notifyto);
    wp_create_user( $username, $password, $email ); 
    */    
    $attachments = array();
    if ('html' == cp_ppp_get_option('fp_emailformat', CP_PPP_DEFAULT_email_format))
        $message = str_replace('<'.'%INFO%'.'>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer)),cp_ppp_get_option('fp_message', CP_PPP_DEFAULT_fp_message));
    else    
        $message = str_replace('<'.'%INFO%'.'>',$buffer,cp_ppp_get_option('fp_message', CP_PPP_DEFAULT_fp_message));    
    foreach ($params as $item => $value)
    {
        $message = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$message);
        if (strpos($item,"_link"))
        {
            foreach ($value as $filevalue)
                $attachments[] = $filevalue;            
        }    
    }
    for ($i=0;$i<500;$i++)
        $message = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$message);        
    $message = str_replace('<'.'%itemnumber%'.'>',$itemnumber,$message);    
    
    $message = str_replace('<'.'%username%'.'>',$username,$message);        
    $message = str_replace('<'.'%password%'.'>',$password,$message);  
        
    $subject = cp_ppp_get_option('fp_subject', CP_PPP_DEFAULT_fp_subject);
    $from = cp_ppp_get_option('fp_from_email', CP_PPP_DEFAULT_fp_from_email);
    $to = explode(",",cp_ppp_get_option('fp_destination_emails', CP_PPP_DEFAULT_fp_destination_emails));
    if ('html' == cp_ppp_get_option('fp_emailformat', CP_PPP_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
    $replyto = $myrows[0]->notifyto;
    
    foreach ($to as $item)
        if (trim($item) != '')
        {
            wp_mail(trim($item), $subject, $message,
                "From: \"$from\" <".$from.">\r\n".
                ($replyto!=''?"Reply-To: \"$replyto\" <".$replyto.">\r\n":'').
                $content_type.
                "X-Mailer: PHP/" . phpversion(), $attachments);
        }

    // 2- Send copy to user
    //---------------------------
    

    
    $to = cp_ppp_get_option('cu_user_email_field', CP_PPP_DEFAULT_cu_user_email_field);
    $_POST[$to] = $myrows[0]->notifyto;
    if ((trim($_POST[$to]) != '' || $payer_email != '') && 'true' == cp_ppp_get_option('cu_enable_copy_to_user', CP_PPP_DEFAULT_cu_enable_copy_to_user))
    {
        
        if ('html' == cp_ppp_get_option('cu_emailformat', CP_PPP_DEFAULT_email_format))
            $message = str_replace('<'.'%INFO%'.'>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer_A)).'</pre>',cp_ppp_get_option('cu_message', CP_PPP_DEFAULT_cu_message));
        else    
            $message = str_replace('<'.'%INFO%'.'>',$buffer_A,cp_ppp_get_option('cu_message', CP_PPP_DEFAULT_cu_message));
        foreach ($params as $item => $value)
            $message = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$message);
        for ($i=0;$i<500;$i++)
            $message = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$message); 
            
        $message = str_replace('<'.'%itemnumber%'.'>',$itemnumber,$message);        
        
        $message = str_replace('<'.'%username%'.'>',$username,$message);        
        $message = str_replace('<'.'%password%'.'>',$password,$message);        
        
        
        $subject = cp_ppp_get_option('cu_subject', CP_PPP_DEFAULT_cu_subject);
        if ('html' == cp_ppp_get_option('cu_emailformat', CP_PPP_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
        if ($_POST[$to] != '')
            wp_mail(trim($_POST[$to]), $subject, $message,
                    "From: \"$from\" <".$from.">\r\n".
                    $content_type.
                    "X-Mailer: PHP/" . phpversion());
        if ($_POST[$to] != $payer_email && $payer_email != '')
            wp_mail(trim($payer_email), $subject, $message,
                    "From: \"$from\" <".$from.">\r\n".
                    $content_type.
                    "X-Mailer: PHP/" . phpversion());
    }

}

function cp_ppp_get_field_name ($fieldid, $form) 
{
    if (is_array($form))
        foreach($form as $item)
            if ($item->name == $fieldid)
                return $item->title;
    return $fieldid;
}

function cp_ppp_export_csv ()
{
    if (!is_admin())
        return;
    global $wpdb;
    
    if (!defined('CP_PPP_ID'))
        define ('CP_PPP_ID',intval($_GET["cal"]));
    
    $form_data = json_decode(cp_ppp_cleanJSON(cp_ppp_get_option('form_structure', CP_PPP_DEFAULT_form_structure)));
    
    $cond = '';
    if ($_GET["search"] != '') $cond .= " AND (data like '%".esc_sql($_GET["search"])."%' OR paypal_post LIKE '%".esc_sql($_GET["search"])."%')";
    if ($_GET["dfrom"] != '') $cond .= " AND (`time` >= '".esc_sql($_GET["dfrom"])."')";
    if ($_GET["dto"] != '') $cond .= " AND (`time` <= '".esc_sql($_GET["dto"])." 23:59:59')";
    if (CP_PPP_ID != 0) $cond .= " AND formid=".CP_PPP_ID;
    
    $events = $wpdb->get_results( "SELECT * FROM ".CP_PPP_POSTS_TABLE_NAME." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );
    
    $fields = array("Form ID", "ItemNumber","Time", "IP Address", "email", "Paid");
    $values = array();
    foreach ($events as $item)
    {
        $value = array($item->formid, $item->id, $item->time, $item->ipaddr, $item->notifyto, ($item->paid?"Yes":"No"));
        $data = array();
        if ($item->posted_data)
            $data = unserialize($item->posted_data);
        else if (!$item->paid)
            $data = unserialize($item->paypal_post);
            
        $end = count($fields); 
        for ($i=0; $i<$end; $i++) 
            if (isset($data[$fields[$i]]) ){
                $value[$i] = $data[$fields[$i]];
                unset($data[$fields[$i]]);
            }    
        
        foreach ($data as $k => $d)    
        {
           $fields[] = $k;
           $value[] = $d;
        }        
        $values[] = $value;        
    }    
    
    
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=export.csv");  
    
    $end = count($fields); 
    for ($i=0; $i<$end; $i++)
        echo '"'.str_replace('"','""', cp_ppp_get_field_name($fields[$i],@$form_data[0])).'",';
    echo "\n";
    foreach ($values as $item)    
    {        
        for ($i=0; $i<$end; $i++)
        {
            if (!isset($item[$i])) 
                $item[$i] = '';
            if (is_array($item[$i]))    
                $item[$i] = implode($item[$i],',');                
            echo '"'.str_replace('"','""', $item[$i]).'",';
        }    
        echo "\n";
    }
    
    exit;    
}

function cp_ppp_update_script_method()
{
    global $wpdb;    
    update_option( 'CP_CFPP_LOAD_SCRIPTS', ($_GET['script_load_method']=="1"?false:true) );
    echo '<br />Script Loading Method Updated.';
    exit;
}

function cp_ppp_save_options()
{
    global $wpdb;
    if (!defined('CP_PPP_ID'))
        define ('CP_PPP_ID',intval($_POST["cp_ppp_id"]));

    foreach ($_POST as $item => $value)
      if (!is_array($value))
          $_POST[$item] = stripcslashes($value);
        
    $data = array(
                  'form_structure' => $_POST['form_structure'],

                  'fp_from_email' => $_POST['fp_from_email'],
                  'fp_destination_emails' => $_POST['fp_destination_emails'],
                  'fp_subject' => $_POST['fp_subject'],
                  'fp_inc_additional_info' => $_POST['fp_inc_additional_info'],
                  'fp_return_page' => $_POST['fp_return_page'],
                  'fp_message' => $_POST['fp_message'],
                  'fp_emailformat' => $_POST['fp_emailformat'],

                  'cu_enable_copy_to_user' => $_POST['cu_enable_copy_to_user'],
                  'cu_user_email_field' => $_POST['cu_user_email_field'],
                  'cu_subject' => $_POST['cu_subject'],
                  'cu_message' => $_POST['cu_message'],
                  'cu_emailformat' => $_POST['cu_emailformat'],

                  'enable_paypal' => @$_POST["enable_paypal"],
                  'paypal_notiemails' => @$_POST["paypal_notiemails"],
                  'paypal_email' => $_POST["paypal_email"],
                  'request_cost' => $_POST["request_cost"],
                  'paypal_price_field' => @$_POST["paypal_price_field"],
                  'request_taxes' => $_POST["request_taxes"],
                  'request_address' => $_POST["request_address"],
                  'paypal_product_name' => $_POST["paypal_product_name"],
                  'currency' => $_POST["currency"],
                  'paypal_language' => $_POST["paypal_language"],
                  'paypal_mode' => $_POST["paypal_mode"],
                  'paypal_recurrent' => $_POST["paypal_recurrent"],
                  'paypal_identify_prices' => @$_POST["paypal_identify_prices"],
                  'paypal_zero_payment' => $_POST["paypal_zero_payment"],
                  
                  'paypalpro_api_username' => $_POST["paypalpro_api_username"],
                  'paypalpro_api_password' => $_POST["paypalpro_api_password"],
                  'paypalpro_api_signature' => $_POST["paypalpro_api_signature"],                  
                  
                  'cp_user_access' => serialize($_POST["cp_user_access"]),

                  'enable_paypal_option_yes' => (@$_POST['enable_paypal_option_yes']?$_POST['enable_paypal_option_yes']:CP_PPP_PAYPAL_OPTION_YES),
                  'enable_paypal_option_no' => (@$_POST['enable_paypal_option_no']?$_POST['enable_paypal_option_no']:CP_PPP_PAYPAL_OPTION_NO),
                  
                  'paypal_recurrent_setup' => @$_POST["paypal_recurrent_setup"],

                  //'vs_use_validation' => $_POST['vs_use_validation'],
                  'vs_text_is_required' => $_POST['vs_text_is_required'],
                  'vs_text_is_email' => $_POST['vs_text_is_email'],
                  'vs_text_datemmddyyyy' => $_POST['vs_text_datemmddyyyy'],
                  'vs_text_dateddmmyyyy' => $_POST['vs_text_dateddmmyyyy'],
                  'vs_text_number' => $_POST['vs_text_number'],
                  'vs_text_digits' => $_POST['vs_text_digits'],
                  'vs_text_max' => $_POST['vs_text_max'],
                  'vs_text_min' => $_POST['vs_text_min'],
                  'vs_text_submitbtn' => $_POST['vs_text_submitbtn'],
                  'vs_text_previousbtn' => $_POST['vs_text_previousbtn'],
                  'vs_text_nextbtn' => $_POST['vs_text_nextbtn'],

                  'cv_enable_captcha' => $_POST['cv_enable_captcha'],
                  'cv_width' => $_POST['cv_width'],
                  'cv_height' => $_POST['cv_height'],
                  'cv_chars' => $_POST['cv_chars'],
                  'cv_font' => $_POST['cv_font'],
                  'cv_min_font_size' => $_POST['cv_min_font_size'],
                  'cv_max_font_size' => $_POST['cv_max_font_size'],
                  'cv_noise' => $_POST['cv_noise'],
                  'cv_noise_length' => $_POST['cv_noise_length'],
                  'cv_background' => $_POST['cv_background'],
                  'cv_border' => $_POST['cv_border'],
                  'cv_text_enter_valid_captcha' => $_POST['cv_text_enter_valid_captcha']
	);
    $wpdb->update ( $wpdb->prefix.CP_PPP_FORMS_TABLE, $data, array( 'id' => CP_PPP_ID ));

}

// cp_ppp_get_option:
$cp_ppp_option_buffered_item = false;
$cp_ppp_option_buffered_id = -1;

function cp_ppp_get_option ($field, $default_value, $id = '')
{
    if (!defined("CP_PPP_ID"))
    {
        if (!(isset($_GET["itemnumber"]) && intval($_GET["itemnumber"]) != ''))
            define ("CP_PPP_ID", 1);
    }    
    if ($id == '') 
        $id = CP_PPP_ID;
    global $wpdb, $cp_ppp_option_buffered_item, $cp_ppp_option_buffered_id;
    if ($cp_ppp_option_buffered_id == $id)
        $value = @$cp_ppp_option_buffered_item->$field;
    else
    {
       $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.CP_PPP_FORMS_TABLE." WHERE id=%d", $id) );
       $value = @$myrows[0]->$field;
       $cp_ppp_option_buffered_item = $myrows[0];
       $cp_ppp_option_buffered_id  = $id;
    }
    if ($value == '' && $cp_ppp_option_buffered_item->form_structure == '')
        $value = $default_value;
    return $value;
}


// WIDGET CODE BELOW
// ***********************************************************************

class CP_PPP_Widget extends WP_Widget
{
  function __construct()
  {
    $widget_ops = array('classname' => 'CP_PPP_Widget', 'description' => 'Displays a form integrated with Paypal' );
    parent::__construct('CP_PPP_Widget', 'Payment Form for PayPal Pro', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'formid' => '' ) );
    $title = $instance['title'];
    $formid = $instance['formid'];
    ?><p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
    <label for="<?php echo $this->get_field_id('formid'); ?>">Form ID: <input class="widefat" id="<?php echo $this->get_field_id('formid'); ?>" name="<?php echo $this->get_field_name('formid'); ?>" type="text" value="<?php echo esc_attr($formid); ?>" /></label>
    </p><?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['formid'] = $new_instance['formid'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $formid = $instance['formid'];
    
    if (!empty($title))
      echo $before_title . $title . $after_title;

    if ($formid != '' && !defined('CP_PPP_ID'))
        define ('CP_PPP_ID',$formid);

    // WIDGET CODE GOES HERE
    cp_ppp_get_public_form($formid);

    echo $after_widget;
  }

}



?>