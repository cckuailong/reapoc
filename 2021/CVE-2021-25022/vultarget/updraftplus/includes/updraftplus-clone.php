<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (!class_exists('UpdraftPlus_Login')) require_once('updraftplus-login.php');

class UpdraftPlus_Clone extends UpdraftPlus_Login {
	
	/**
	 * Pulls the appropriate message for the given code and translate it before
	 * returning it to the caller
	 *
	 * @internal
	 * @param string $code The code of the message to pull
	 * @return string - The translated message
	 */
	protected function translate_message($code) {
		switch ($code) {
			case 'generic':
			default:
				return __('An error has occurred while processing your request. The server might be busy or you have lost your connection to the internet at the time of the request. Please try again later.', 'updraftplus');
				break;
		}
	}

	/**
	 * This function will check the passed in response from the remote call and check for various errors and return the parsed response
	 *
	 * @param array $response - the response from the remote call
	 *
	 * @return array          - the parsed response
	 */
	private function parse_response($response) {
		
		if (is_wp_error($response)) {
			$response = array('status' => 'error', 'code' => $response->get_error_code(), 'message' => $response->get_error_message());
		} else {
			if (isset($response['status'])) {
				if ('error' === $response['status']) {
					$response = array(
						'status' => 'error',
						'code' => isset($response['code']) ? $response['code'] : -1,
						'message' => isset($response['message']) ? $response['message'] : $this->translate_message('generic'),
						'response' => $response,
					);
				}
			} else {
				$response = array('status' => 'error', 'message' => $this->translate_message('generic'));
			}
		}

		return $response;
	}

	/**
	 * Executes login or registration process. Connects and sends request to the UpdraftPlus clone
	 * and returns the response coming from the server
	 *
	 * @internal
	 * @param array   $data     The submitted form data
	 * @param boolean $register Indicates whether the current call is for a registration process or not. Defaults to false. Currently will always be false.
	 * @return array - The response from the request
	 */
	protected function login_or_register($data, $register = false) {

		$action = ($register) ? 'updraftplus_clone_register' : 'updraftplus_clone_login';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());

		$response = $this->send_remote_request($data, $action);

		return $this->parse_response($response);
	}

	/**
	 * The ajax based request point of entry for the create clone process
	 *
	 * @param array $data - The submitted form data
	 *
	 * @return array      - Response of the process
	 */
	public function ajax_process_clone($data = array()) {
		try {
			if (isset($data['form_data']) && is_array($data['form_data'])) {
				$form_data = $data['form_data'];
			}

			$response = $this->create_clone($form_data);
		} catch (Exception $e) {
			$response = array('error' => true, 'message' => $e->getMessage());
		}

		return $response;
	}

	/**
	 * Executes the create clone process. Connects and sends request to the UpdraftPlus clone and returns the response coming from the server
	 *
	 * @internal
	 * @param array $data - The submitted form data
	 * @return array      - The response from the request
	 */
	public function create_clone($data) {
		global $updraftplus, $table_prefix;
		
		$action = 'updraftplus_clone_create';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());
		if (empty($data['label'])) $data['label'] = sprintf(__('Clone of %s', 'updraftplus'), trailingslashit(network_site_url()));
		if (empty($data['install_info']['table_prefix'])) $data['install_info']['table_prefix'] = $table_prefix;
		$subdirectory = parse_url(network_site_url(), PHP_URL_PATH);
		if (empty($data['install_info']['package'])) $data['install_info']['package'] = 'starter';
		if (empty($data['install_info']['subdirectory'])) $data['install_info']['subdirectory'] = !empty($subdirectory) ? $subdirectory : '/';
		if (empty($data['install_info']['locale'])) $data['install_info']['locale'] = get_locale();
		if (empty($data['install_info']['owner_id']) && empty($data['install_info']['owner_login'])) {
			$user = wp_get_current_user();
			$data['install_info']['owner_id'] = $user->ID;
			$data['install_info']['owner_login'] = $user->user_login;
		}
		if (is_multisite()) {
			$data['install_info']['multisite'] = true;
			$data['install_info']['multisite_type'] = is_subdomain_install() ? 'subdomain' : 'subfolder';
		}
		if (empty($data['install_info']['requested_by'])) $data['install_info']['requested_by'] = $updraftplus->version;

		$response = $this->send_remote_request($data, $action);
		
		return $this->parse_response($response);
	}

	/**
	 * Executes the clone status process. Connects and sends request to the UpdraftPlus clone and returns the response coming from the server
	 *
	 * @internal
	 * @param array $data - The submitted form data
	 * @return array      - The response from the request
	 */
	public function clone_status($data) {

		$action = 'updraftplus_clone_status';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());

		$response = $this->send_remote_request($data, $action);
		
		return $this->parse_response($response);
	}

	/**
	 * Executes the clone info poll. Connects and sends request to the UpdraftPlus clone and returns the response coming from the server
	 *
	 * @internal
	 * @param array $data - The submitted form data
	 * @return array      - The response from the request
	 */
	public function clone_info_poll($data) {

		$action = 'updraftplus_clone_info_poll';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());

		$response = $this->send_remote_request($data, $action);
		
		return $this->parse_response($response);
	}

	/**
	 * Executes the backup checkin. Connects and sends request to UpdraftPlus and returns the response coming from the server
	 *
	 * @internal
	 * @param array $data - The submitted form data
	 * @return array      - The response from the request
	 */
	public function backup_checkin($data) {
		$action = 'updraftplus_backup_checkin';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());
		if (!empty($data['log_contents'])) {
			$data['log_contents'] = base64_encode(gzcompress($data['log_contents']));
			$data['format'] = 'gzcompress';
		}

		$response = $this->send_remote_request($data, $action);

		return $this->parse_response($response);
	}

	/**
	 * Executes the clone failed delete process. Connects and sends request to the UpdraftPlus clone and returns the response coming from the server
	 *
	 * @internal
	 * @param array $data - The submitted form data
	 * @return array      - The response from the request
	 */
	public function clone_failed_delete($data) {

		$action = 'updraftplus_clone_failed_delete';
		if (empty($data['site_url'])) $data['site_url'] = trailingslashit(network_site_url());

		$response = $this->send_remote_request($data, $action);
		
		return $this->parse_response($response);
	}
}
