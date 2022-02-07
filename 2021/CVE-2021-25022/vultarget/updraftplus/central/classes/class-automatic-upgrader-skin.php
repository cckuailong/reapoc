<?php
// @codingStandardsIgnoreFile
if (!defined('ABSPATH')) die('No direct access.');

// Extracted from 4.5.2/wordpress/wp-admin/includes/class-wp-upgrader-skins.php; with the bulk_*() methods added since they are not in the base class on all WP versions.
// Needed only on WP < 3.7

/**
 * Upgrader Skin for Automatic WordPress Upgrades
 *
 * Extracted from 4.5.2/wordpress/wp-admin/includes/class-wp-upgrader-skins.php; with the bulk_*() methods added since they are not in the base class on all WP versions.
 * Needed only on WP < 3.7
 *
 * This skin is designed to be used when no output is intended, all output
 * is captured and stored for the caller to process and log/email/discard.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.7.0
 */
class Automatic_Upgrader_Skin_Main extends WP_Upgrader_Skin {

	protected $messages = array();

	/**
	 * Request filesystem credentials
	 *
	 * @param bool   $error 					   Check if there is an error: default is false
	 * @param string $context 					   Context for credentails
	 * @param bool   $allow_relaxed_file_ownership Check if relaxed file ownership is allowed
	 * @return bool
	 */
	public function request_filesystem_credentials($error = false, $context = '', $allow_relaxed_file_ownership = false) {
		if ($context) {
			$this->options['context'] = $context;
		}
		// TODO: fix up request_filesystem_credentials(), or split it, to allow us to request a no-output version
		// This will output a credentials form in event of failure, We don't want that, so just hide with a buffer
		ob_start();
		$result = parent::request_filesystem_credentials($error, $context, $allow_relaxed_file_ownership);
		ob_end_clean();
		return $result;
	}

	/**
	 * Get update message
	 *
	 * @return array reti=urns an array of messages
	 */
	public function get_upgrade_messages() {
		return $this->messages;
	}

	/**
	 * Feedback
	 *
	 * @param  string|array|WP_Error $data THis is the data to be used for the feedback
	 */
	protected function updraft_feedback($data) {
		if (is_wp_error($data)) {
			$string = $data->get_error_message();
		} elseif (is_array($data)) {
			return;
		} else {
			$string = $data;
		}
		if (!empty($this->upgrader->strings[$string]))
			$string = $this->upgrader->strings[$string];

		if (false !== strpos($string, '%')) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if (!empty($args))
				$string = vsprintf($string, $args);
		}

		$string = trim($string);

		// Only allow basic HTML in the messages, as it'll be used in emails/logs rather than direct browser output.
		$string = wp_kses($string, array(
			'a' => array(
				'href' => true
			),
			'br' => true,
			'em' => true,
			'strong' => true,
		));

		if (empty($string))
			return;

		$this->messages[] = $string;
	}

	public function header() {
		ob_start();
	}

	public function footer() {
		$output = ob_get_clean();
		if (!empty($output))
			$this->feedback($output);
	}
	
	/**
	 * @access public
	 */
	public function bulk_header() {}

	public function bulk_footer() {
	}
}

global $updraftcentral_main;
$wp_version = $updraftcentral_main->get_wordpress_version();

if (version_compare($wp_version, '5.3', '>=')) {
	if (!class_exists('Automatic_Upgrader_Skin')) require_once(dirname(__FILE__).'/automatic-upgrader-skin-compatibility.php');
} else {
	class Automatic_Upgrader_Skin extends Automatic_Upgrader_Skin_Main {

		public function feedback($string) {
			parent::updraft_feedback($string);
		}
	}
}