<?php
if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

/*
This is a small glue class, which makes available all the commands in UpdraftPlus_Commands, and translates the response from UpdraftPlus_Commands (which is either data to return, or a WP_Error) into the format used by UpdraftCentral.
*/

if (class_exists('UpdraftCentral_UpdraftPlus_Commands')) return;

class UpdraftCentral_UpdraftPlus_Commands extends UpdraftCentral_Commands {

	private $commands;

	public function __construct($rc) {
	
		parent::__construct($rc);
	
		if (!class_exists('UpdraftPlus_Commands')) include_once(UPDRAFTPLUS_DIR.'/includes/class-commands.php');
		$this->commands = new UpdraftPlus_Commands($this);
		
	}

	public function __call($name, $arguments) {
	
		if ('_' == substr($name, 0, 1) || !method_exists($this->commands, $name)) return $this->_generic_error_response('unknown_rpc_command', array(
			'prefix' => 'updraftplus',
			'command' => $name,
			'class' => 'UpdraftCentral_UpdraftPlus_Commands'
		));
	
		$result = call_user_func_array(array($this->commands, $name), $arguments);
		
		if (is_wp_error($result)) {
		
			return $this->_generic_error_response($result->get_error_code(), $result->get_error_data());
		
		} else {
		
			return $this->_response($result);
		
		}
		
	}
	
	public function _updraftplus_background_operation_started($msg) {

		// Under-the-hood hackery to allow the browser connection to be closed, and the backup/download to continue
		
		$rpc_response = $this->rc->return_rpc_message($this->_response($msg));
		
		$data = isset($rpc_response['data']) ? $rpc_response['data'] : null;

		$ud_rpc = $this->rc->get_current_udrpc();
		
		$encoded = json_encode($ud_rpc->create_message($rpc_response['response'], $data, true));
		
		global $updraftplus;
		$updraftplus->close_browser_connection($encoded);

	}
}
