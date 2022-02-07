<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * Handles Analytics Commands
 *
 * @method array ga_checker()
 * @method array get_access_token()
 * @method array set_authorization_code()
 */
class UpdraftCentral_Analytics_Commands extends UpdraftCentral_Commands {

	private $scope = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/analytics.readonly';
	
	private $endpoint = 'https://accounts.google.com/o/oauth2/auth';
	
	private $token_info_endpoint = 'https://www.googleapis.com/oauth2/v1/tokeninfo';
	
	private $access_key = 'updraftcentral_auth_server_access';
	
	private $auth_endpoint;
	
	private $client_id;
	
	private $view_key = 'updraftcentral_analytics_views';
	
	private $tracking_id_key = 'updraftcentral_analytics_tracking_id';
	
	private $expiration;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->auth_endpoint = defined('UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL') ? UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL : 'https://auth.updraftplus.com/auth/googleanalytics';
		$this->client_id = defined('UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID') ? UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID : '306245874349-6s896c3tjpra26ns3dpplhqcl6rv6qlb.apps.googleusercontent.com';

		// Set transient expiration - default for 24 hours
		$this->expiration = 86400;
	}
	
	/**
	 * Checks whether Google Analytics (GA) is installed or setup
	 *
	 * N.B. This check assumes GA is installed either using "wp_head" or "wp_footer" (e.g. attached
	 * to the <head/> or somewhere before </body>). It does not recursively check all the pages
	 * of the website to find if GA is installed on each or one of those pages, but only on the main/root page.
	 *
	 * @return array $result An array containing "ga_installed" property which returns "true" if GA (Google Analytics) is installed, "false" otherwise.
	 */
	public function ga_checker() {

		try {
			
			// Retrieves the tracking code/id if available
			$tracking_id = $this->get_tracking_id();
			$installed = true;

			// If tracking code/id is currently not available then we
			// parse the needed information from the buffered content through
			// the "wp_head" and "wp_footer" hooks.
			if (false === $tracking_id) {
				$info = $this->extract_tracking_id();

				$installed = $info['installed'];
				$tracking_id = $info['tracking_id'];
			}

			// Get access token to be use to generate the report.
			$access_token = $this->_get_token();

			if (empty($access_token)) {
				// If we don't get a valid access token then that would mean
				// the access has been revoked by the user or UpdraftCentral was not authorized yet
				// to access the user's analytics data, thus, we're clearing
				// any previously stored user access so we're doing some housekeeping here.
				$this->clear_user_access();
			}

			// Wrap and combined information for the requesting
			// client's consumption
			$result = array(
				'ga_installed' => $installed,
				'tracking_id' => $tracking_id,
				'client_id' => $this->client_id,
				'redirect_uri' => $this->auth_endpoint,
				'scope' => $this->scope,
				'access_token' => $access_token,
				'endpoint' => $this->endpoint
			);

		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}
		
		return $this->_response($result);
	}

	/**
	 * Extracts Google Tracking ID from contents rendered through the "wp_head" and "wp_footer" action hooks
	 *
	 * @internal
	 * @return array $result An array containing the result of the extraction.
	 */
	private function extract_tracking_id() {

		// Define result array
		$result = array();

		// Retrieve header content
		ob_start();
		do_action('wp_head');
		$header_content = ob_get_clean();
		
		// Extract analytics information if available.
		$output = $this->parse_content($header_content);
		$result['installed'] = $output['installed'];
		$result['tracking_id'] = $output['tracking_id'];
		
		// If it was not found, then now try the footer
		if (empty($result['tracking_id'])) {
			// Retrieve footer content
			ob_start();
			do_action('wp_footer');
			$footer_content = ob_get_clean();
			$output = $this->parse_content($footer_content);
			$result['installed'] = $output['installed'];
			$result['tracking_id'] = $output['tracking_id'];
		}

		if (!empty($result['tracking_id'])) {
			set_transient($this->tracking_id_key, $result['tracking_id'], $this->expiration);
		}

		return $result;
	}
	
	/**
	 * Gets access token
	 *
	 * Validates whether the system currently have a valid token to use when connecting to Google Analytics API.
	 * If not, then it will send a token request based on the authorization code we stored during the
	 * authorization phase. Otherwise, it will return an empty token.
	 *
	 * @return array $result An array containing the Google Analytics API access token.
	 */
	public function get_access_token() {
		
		try {

			// Loads or request a valid token to use
			$access_token = $this->_get_token();
			
			if (!empty($access_token)) {
				$result = array('access_token' => $access_token);
			} else {
				$result = array('error' => true, 'message' => 'ga_token_retrieval_failed', 'values' => array());
			}
		
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}
		
		return $this->_response($result);
	}

	/**
	 * Clears any previously stored user access
	 *
	 * @return bool
	 */
	public function clear_user_access() {
		return delete_option($this->access_key);
	}

	/**
	 * Saves user is and access token received from the auth server
	 *
	 * @param array $query Parameter array containing the user id and access token from the auth server.
	 * @return array $result An array containing a "success" or "failure" message as a result of the current process.
	 */
	public function save_user_access($query) {
		
		try {

			$token = get_option($this->access_key, false);
			$result = array();

			if (false === $token) {
				$token = array(
					'user_id' => base64_decode(urldecode($query['user_id'])),
					'access_token' => base64_decode(urldecode($query['access_token']))
				);

				if (false !== update_option($this->access_key, $token)) {
					$result = array('error' => false, 'message' => 'ga_access_saved', 'values' => array());
				} else {
					$result = array('error' => true, 'message' => 'ga_access_saving_failed', 'values' => array($query['access_token']));
				}
			}
		
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}
		
		return $this->_response($result);
	}

	/**
	 * Saves the tracking code/id manually (user input)
	 *
	 * @param array $query Parameter array containing the tracking code/id to save.
	 * @return array $result An array containing the result of the process.
	 */
	public function save_tracking_id($query) {
		try {
			$tracking_id = $query['tracking_id'];
			$saved = false;

			if (!empty($tracking_id)) {
				$saved = set_transient($this->tracking_id_key, $tracking_id, $this->expiration);
			}

			$result = array('saved' => $saved);
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}

		return $this->_response($result);
	}

	/**
	 * Retrieves any available access token either previously saved info or
	 * from a new request from the Google Server.
	 *
	 * @internal
	 * @return string $authorization_code
	 */
	private function _get_token() {

		// Retrieves the tracking code/id if available
		$tracking_id = $this->get_tracking_id();
		$access_token = '';
		
		$token = get_option($this->access_key, false);
		if (false !== $token) {
			$access_token = isset($token['access_token']) ? $token['access_token'] : '';
			$user_id = isset($token['user_id']) ? $token['user_id'] : '';

			if ((!empty($access_token) && !$this->_token_valid($access_token)) || (!empty($user_id) && empty($access_token) && !empty($tracking_id))) {
				if (!empty($user_id)) {
					$args = array(
						'headers' => apply_filters('updraftplus_auth_headers', array())
					);
					
					$response = wp_remote_get($this->auth_endpoint.'?user_id='.$user_id.'&code=ud_googleanalytics_code', $args);
					if (is_wp_error($response)) {
						throw new Exception($response->get_error_message());
					} else {
						if (is_array($response)) {
							
							$body = json_decode($response['body'], true);
							$token_response = array();

							if (is_array($body) && !isset($body['error'])) {
								$token_response = json_decode(base64_decode($body[0]), true);
							}

							if (is_array($token_response) && isset($token_response['access_token'])) {
								$access_token = $token_response['access_token'];
							} else {
								// If we don't get any valid response then that would mean that the
								// permission was already revoked. Thus, we need to re-authorize the
								// user before using the analytics feature once again.
								$access_token = '';
							}

							$token['access_token'] = $access_token;
							update_option($this->access_key, $token);
						}
					}
				}
			}
		}
		
		return $access_token;
	}
	
	/**
	 * Verifies whether the access token is still valid for use
	 *
	 * @internal
	 * @param string $token The access token to be check and validated
	 * @return bool
	 * @throws Exception If an error has occurred while connecting to the Google Server.
	 */
	private function _token_valid($token) {
		
		$response = wp_remote_get($this->token_info_endpoint.'?access_token='.$token);
		if (is_wp_error($response)) {
			throw new Exception($response->get_error_message());
		} else {
			if (is_array($response)) {
				$response = json_decode($response['body'], true);
				if (!empty($response)) {
					if (!isset($response['error']) && !isset($response['error_description'])) {
						return true;
					}
				}
			}
		}
		
		return false;
	}

	/**
	 * Parses and extracts the google analytics information (NEEDED)
	 *
	 * @internal
	 * @param string $content The content to parse
	 * @return array An array containing the status of the process along with the tracking code/id
	 */
	private function parse_content($content) {

		$installed = false;
		$gtm_installed = false;
		$tracking_id = '';
		$script_file_found = false;
		$tracking_id_found = false;

		// Pull google analytics script file(s)
		preg_match_all('/<script\b[^>]*>([\s\S]*?)<\/script>/i', $content, $scripts);
		for ($i=0; $i < count($scripts[0]); $i++) {
			// Check for Google Analytics file
			if (stristr($scripts[0][$i], 'ga.js') || stristr($scripts[0][$i], 'analytics.js')) {
				$script_file_found = true;
			}
			
			// Check for Google Tag Manager file
			// N.B. We are not checking for GTM but this check will be useful when
			// showing the notice to the user if we haven't found Google Analytics
			// directly being installed on the page.
			if (stristr($scripts[0][$i], 'gtm.js')) {
				$gtm_installed = true;
			}
		}

		// Pull tracking code
		preg_match_all('/UA-[0-9]{5,}-[0-9]{1,}/i', $content, $codes);
		if (count($codes) > 0) {
			if (!empty($codes[0])) {
				$tracking_id_found = true;
				$tracking_id = $codes[0][0];
			}
		}

		// If we found both the script and the tracking code then it is safe
		// to say that Google Analytics (GA) is installed. Thus, we're returning
		// "true" as a response.
		if ($script_file_found && $tracking_id_found) {
			$installed = true;
		}

		// Return result of process.
		return array(
			'installed' => $installed,
			'gtm_installed' => $gtm_installed,
			'tracking_id' => $tracking_id
		);
	}

	/**
	 * Retrieves the "analytics_tracking_id" transient
	 *
	 * @internal
	 * @return mixed Returns the value of the saved transient. Returns "false" if the transient does not exist.
	 */
	private function get_tracking_id() {
		return get_transient($this->tracking_id_key);
	}


	/**
	 * Returns the current tracking id
	 *
	 * @return array $result An array containing the Google Tracking ID.
	 */
	public function get_current_tracking_id() {
		try {

			// Get current site transient stored for this key
			$tracking_id = get_transient($this->tracking_id_key);

			// Checks whether we have a valid token
			$access_token = $this->_get_token();
			if (empty($access_token)) {
				$tracking_id = '';
			}

			if (false === $tracking_id) {
				$result = $this->extract_tracking_id();
			} else {
				$result = array(
					'installed' => true,
					'tracking_id' => $tracking_id
				);
			}
		
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}
		
		return $this->_response($result);
	}

	/**
	 * Clears user access from database
	 *
	 * @return array $result An array containing the "Remove" confirmation whether the action succeeded or not.
	 */
	public function remove_user_access() {
		try {

			// Clear user access
			$is_cleared = $this->clear_user_access();
			
			if (false !== $is_cleared) {
				$result = array('removed' => true);
			} else {
				$result = array('error' => true, 'message' => 'user_access_remove_failed', 'values' => array());
			}
		
		} catch (Exception $e) {
			$result = array('error' => true, 'message' => 'generic_response_error', 'values' => array($e->getMessage()));
		}
		
		return $this->_response($result);
	}
}
