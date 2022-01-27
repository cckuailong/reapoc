<?php
// Direct calls to this file are Forbidden when wp core files are not present
/*
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
*/
if ( !current_user_can('manage_options') ){ 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/*
// BPS Class - not doing anything with this Class in BPS Free
if ( !class_exists('Bulletproof_Security') ) {
	
	if ( !defined('BULLETPROOF_VERSION' ) )
	define( 'BULLETPROOF_VERSION', '.48.8' );
	
	class Bulletproof_Security {
	
	public function __construct() {
        add_action('init', array($this, 'load_plugin_textdomain'));
	}
 
    public function load_plugin_textdomain() {
        load_plugin_textdomain('bulletproof-security', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    }

	public function bulletproof_save_options() {
		return update_option('bulletproof_security', $this->options);
	}

	public function bulletproof_set_error($code = '', $error = '', $data = '') {
		if ( empty($code) )
			$this->errors = new WP_Error();
		elseif ( is_a($code, 'WP_Error') )
			$this->errors = $code;
		elseif ( is_a($this->errors, 'WP_Error') )
			$this->errors->add($code, $error, $data);
		else
			$this->errors = new WP_Error($code, $error, $data);
	}

	public function bulletproof_get_error($code = '') {
		if ( is_a($this->errors, 'WP_Error') )
			return $this->errors->get_error_message($code);
			return false;
		}
	}
}//end if
*/
?>