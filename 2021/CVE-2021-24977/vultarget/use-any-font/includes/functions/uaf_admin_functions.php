<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
function uaf_create_menu() {
	add_menu_page( 'Use Any Font', 'Use Any Font', 'manage_options', 'use-any-font', 'uaf_interface', 'dashicons-editor-textcolor');
}

function uaf_admin_assets(){
	wp_register_style('uaf_admin_css', plugins_url('use-any-font/includes/assets/css/uaf_admin.css'),'', $GLOBALS['uaf_current_version']);
  	wp_enqueue_style('uaf_admin_css');

  	$uaf_upload 	= wp_upload_dir();
	$uaf_upload_url = set_url_scheme($uaf_upload['baseurl']);
	$uaf_upload_url = $uaf_upload_url . '/useanyfont/';
	wp_register_style('uaf-font-style', $uaf_upload_url.'admin-uaf.css', array(), get_option('uaf_css_updated_timestamp'));
    wp_enqueue_style('uaf-font-style');
	add_editor_style($uaf_upload_url.'admin-uaf.css');	

  	wp_register_script('uaf_admin_js', plugins_url('use-any-font/includes/assets/js/uaf_admin.js'), '', $GLOBALS['uaf_current_version'], true);	
  	$uaf_server_url =  array($GLOBALS['uaf_user_settings']['uaf_server_url']);
  	wp_localize_script( 'uaf_admin_js', 'uaf_server_url', $uaf_server_url );
  	wp_enqueue_script( 'uaf_admin_js' );
}

function uaf_interface(){
	$uaf_tabs = array(
				'api' => array('name'=>'API Key','path'=>'uaf_api_interface.php'),
				'font_upload' => array('name'=>'Upload Font','path'=>'uaf_font_upload_'.$GLOBALS['uaf_user_settings']['uaf_uploader_type'].'.php'),
				'font_assign' => array('name'=>'Assign Font','path'=>'uaf_font_assign.php'),
				'settings' => array('name'=>"Settings",'path'=>'uaf_settings.php'),
				'instructions' => array('name'=>"Instructions",'path'=>'uaf_instructions.php')
	);

	$uaf_tabs = apply_filters( 'uaf_tabs_filter', $uaf_tabs);
	
	include UAF_FILE_PATH.'includes/views/uaf_main.php';
}

function uaf_predefined_font_interface(){
	include UAF_FILE_PATH.'includes/views/uaf_predefined_fonts.php';
	wp_die();
}

function uaf_get_options(){
	$uaf_user_settings 		 = $GLOBALS['uaf_user_settings'];
	foreach ($uaf_user_settings as $option_name => $option_value) {
		$GLOBALS['uaf_user_settings'][$option_name] = get_option($option_name, $option_value);
	}
	$GLOBALS['uaf_user_settings']['uaf_server_url']		= $GLOBALS['uaf_fix_settings']['serverUrl'][$GLOBALS['uaf_user_settings']['uaf_server_url_type']];
}

function uaf_api_key_activate(){
	$uaf_api_key 			= trim($_POST['uaf_api_key']);
	$uaf_site_url			= site_url();
	if (!empty($uaf_api_key)){
		$api_key_return = wp_remote_get($GLOBALS['uaf_user_settings']['uaf_server_url'].'/uaf_convertor/validate_key.php?license_key='.$uaf_api_key.'&url='.$uaf_site_url, array('timeout'=>300,'sslverify'=>false,'user-agent'=>get_bloginfo( 'url' )));
		if ( is_wp_error( $api_key_return ) ) {
		   $error_message 		= $api_key_return->get_error_message();
		   $return['body'] 		= "Something went wrong: $error_message";
		   $return['status'] 	= 'error';
		} else {
		    $api_key_return = json_decode($api_key_return['body']);
			if ($api_key_return->status == 'success'){
				update_option('uaf_api_key', $uaf_api_key);
				update_option('uaf_activated_url', base64_encode($uaf_site_url));
				update_option('uaf_hide_key', 'no');
				uaf_get_options();
				uaf_write_css();
			}
			$return['body'] 	= $api_key_return->msg;
		   	$return['status'] 	= $api_key_return->status;
		}
	} else {
		$return['body'] 	= 'Please keep API key to activate.';
		$return['status'] 	= 'error';
	}
	return $return;
}


function uaf_api_key_deactivate(){
	$uaf_api_key			= $GLOBALS['uaf_user_settings']['uaf_api_key'];
	$uaf_activated_url		= base64_decode($GLOBALS['uaf_user_settings']['uaf_activated_url']);

	$api_key_return 	= wp_remote_get($GLOBALS['uaf_user_settings']['uaf_server_url'].'/uaf_convertor/deactivate_key.php?license_key='.$uaf_api_key.'&url='.$uaf_activated_url, array('timeout'=>300,'sslverify'=>false,'user-agent'=>get_bloginfo( 'url' )));
	if ( is_wp_error( $api_key_return ) ) {
	   $error_message 	= $api_key_return->get_error_message();
	   $return['body']  	= "Something went wrong: $error_message";
	   $return['status']     = 'error';
	} else {
	    $api_key_return = json_decode($api_key_return['body']);
		if ($api_key_return->status == 'success'){
			update_option('uaf_api_key', '');
			update_option('uaf_activated_url', '');
			uaf_get_options();
			uaf_write_css();
		}
		$return['status']   = $api_key_return->status;
		$return['body'] 	= $api_key_return->msg;
	}	
	return $return;
}

function uaf_api_key_hide(){
	update_option('uaf_hide_key','yes');
	$return['status']   = 'success';
	$return['body'] 	= 'Key hidden. You must remove the key and add again to see it back.';
	uaf_get_options();
	return $return;
}

function uaf_plugin_initialize(){	
	if (get_option('uaf_current_version') != $GLOBALS['uaf_current_version']){
		add_option('uaf_install_date', date('Y-m-d'));
		
		if (!empty(trim(get_option('uaf_api_key')))){
			update_option('uaf_activated_url',base64_encode(site_url()));
		}

		uaf_create_folder();
		uaf_write_css();
		
		update_option('uaf_current_version', $GLOBALS['uaf_current_version']);

		$uaf_user_settings 		 = $GLOBALS['uaf_user_settings'];
		foreach ($uaf_user_settings as $option_name => $option_value) {
			add_option($option_name, $option_value);
		}
	}
	uaf_get_options();
	uaf_trigger_actions();
	uaf_check_site_url();

	include UAF_FILE_PATH.'includes/functions/uaf_editor_functions.php'; // EDITOR FILTERS AND HOOKS
}

function uaf_plugin_activated(){
	uaf_create_folder();
	uaf_get_options();
	uaf_write_css();
}

function uaf_max_upload_size_for_php($sendinbytes = false){
	$maxUploadSizeForPHP 	= $GLOBALS['uaf_fix_settings']['allowedFontSize'];
	$wpAllowedMaxSize 		= wp_max_upload_size(); 
	$wpAllowedMaxSizeToMB	= $wpAllowedMaxSize / 1048576 ;
	if ($wpAllowedMaxSizeToMB < $GLOBALS['uaf_fix_settings']['allowedFontSize']){
		$maxUploadSizeForPHP = $wpAllowedMaxSizeToMB;
	}
	if ($sendinbytes) {
		return $maxUploadSizeForPHP * 1048576;	
	} else {
		return $maxUploadSizeForPHP;	
	}	
}

function uaf_check_site_url(){
	$uaf_api_key 		= $GLOBALS['uaf_user_settings']['uaf_api_key'];
	$uaf_site_url 		= $GLOBALS['uaf_user_settings']['uaf_site_url'];
	$uaf_current_url	= base64_encode(site_url());

	if (!empty($uaf_api_key) && ($uaf_site_url != $uaf_current_url)){
		uaf_write_css();
	}
}

function uaf_save_options(){
		
	$all_fields_name = array(
							'uaf_server_url_type',
							'uaf_uploader_type',
							'uaf_use_absolute_font_path',
							'uaf_disbale_editor_font_list',
							'uaf_enable_multi_lang_support',
							'uaf_font_display_property'
							);

	foreach ($all_fields_name as $fieldname) {
		update_option($fieldname,sanitize_text_field($_POST[$fieldname]));
	}
	
	uaf_get_options();
	uaf_write_css();
	$return['status']   = 'ok';
	$return['body'] 	= 'Settings Saved';	
	return $return;
}


function uaf_admin_notices(){
	// ASKING FOR REVIEW
	if (isset($_GET['uaf_reviews_notice_hide']) == 1){
    	update_option('uaf_reviews_notice_hide','yes');
	}

	if (get_option('uaf_reviews_notice_hide') != 'yes'){
        $installedDate 		= strtotime(get_option('uaf_install_date'));
        $todayDate     		= time();        
        $installedDays 		= round(($todayDate - $installedDate)  / (60 * 60 * 24));
        $fonts_uploaded     = uaf_count_uploaded_fonts();

        
        if ($installedDays > 30 && $fonts_uploaded > 1){
            echo '<div class="updated success" style="padding:10px; font-size:16px; line-height:1.6;color:#205225;">
                    Hey, You have been using Use Any Font for more than 30 days - that’s awesome! Could you please do us a BIG favor and give it a 5-star rating on WordPress ? Just to help us spread the word and boost our motivation.<br/>
                    <ul style="padding-left:50px;list-style-type: square;">
                        <li><a href="https://wordpress.org/support/plugin/use-any-font/reviews/?filter=5" target="_blank">Ok, you deserve it</a></li>
                        <li><a href="https://dineshkarki.com.np/contact" target="_blank">I still have problem !!</a></li>
                        <li><a href="?uaf_reviews_notice_hide=1">I already did</a></li>
                        <li><a href="?uaf_reviews_notice_hide=1">Hide this message</a></li>
                    </ul>
             </div>';
        }
    }
    // EOF ASKING FOR REVIEW    
}

function uaf_trigger_actions(){
	$actionReturn = array();
	$actionReturnNonceError = array(
									'status' 		=> 'error',
									'body'			=> 'Sorry, your nonce did not verify. Please try again.'
									);

	if (isset($_POST['uaf_api_key_activate'])){
    	if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_api_key_actions')) {
    		$actionReturn = uaf_api_key_activate();
    	} else {
    		$actionReturn = $actionReturnNonceError;
    	}
	}

	if (isset($_POST['uaf_api_key_deactivate'])){
	    if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_api_key_actions')) {
	    	$actionReturn = uaf_api_key_deactivate();
	    } else {
	    	$actionReturn = $actionReturnNonceError;
	    }
	}

	if (isset($_POST['uaf_api_key_hide'])){
	    if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_api_key_actions')) {
	    	$actionReturn = uaf_api_key_hide();
	    } else {
	    	$actionReturn = $actionReturnNonceError;
	    }
	}

	if (isset($_POST['submit-uaf-font-js'])){   
	    $font_weight = $font_style  = '';
	    if (isset($_POST['enable_font_variation'])){
	    	$font_weight 	= $_POST['font_weight'];
	    	$font_style 	= $_POST['font_style'];
	    }
	    $actionReturn = uaf_save_font_files($_POST['font_name'], $font_weight, $font_style, $_POST['convert_response']);
	}

	if (isset($_POST['submit-uaf-font-php'])){  
	    if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_font_upload_php')) {
		    $actionReturn = uaf_upload_font_to_server();
		    if ($actionReturn['status'] == 'success'){
		        $font_weight = $font_style  = '';
			    if (isset($_POST['enable_font_variation'])){
			    	$font_weight 	= $_POST['font_weight'];
			    	$font_style 	= $_POST['font_style'];
			    }
		        $actionReturn = uaf_save_font_files($_POST['font_name'], $font_weight, $font_style, $actionReturn['body']);
		    }
		} else {
			$actionReturn = $actionReturnNonceError;
		}
	}

	if (isset($_POST['submit-uaf-font-assign'])){
	    if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_font_assign')) {
	    	$actionReturn = uaf_save_font_assign();
		} else {
			$actionReturn = $actionReturnNonceError;
		}
	}

	if (isset($_GET['delete_font_assign_key'])){
	    if ( isset($_GET['uaf_nonce']) && wp_verify_nonce($_GET['uaf_nonce'], 'uaf_delete_font_assign')) {
	    	$actionReturn = uaf_delete_font_assign();
	    } else {
	    	$actionReturn = $actionReturnNonceError;
	    }
	}

	if (isset($_GET['delete_font_key'])){
	    if ( isset($_GET['uaf_nonce']) && wp_verify_nonce($_GET['uaf_nonce'], 'uaf_delete_font')) {
	    	$actionReturn = uaf_delete_font();
	    } else {
	    	$actionReturn = $actionReturnNonceError;
	    }
	}

	if (isset($_POST['save-uaf-options'])){
	    if ( isset($_POST['uaf_nonce']) && wp_verify_nonce($_POST['uaf_nonce'], 'uaf_save_settings')) {
	    	$actionReturn = uaf_save_options();
	    } else {
	    	$actionReturn = $actionReturnNonceError;
	    }
	}

	if (isset($_GET['predefinedfontid'])){
	    $actionReturn = uaf_add_pre_defined_font($_GET['predefinedfontid']);
	}
	
	$GLOBALS['uaf_action_return'] = $actionReturn;
}