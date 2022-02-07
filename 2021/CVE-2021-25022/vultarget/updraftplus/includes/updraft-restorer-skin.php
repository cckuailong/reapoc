<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('WP_Upgrader_Skin')) require_once(ABSPATH.'wp-admin/includes/class-wp-upgrader.php');

abstract class Updraft_Restorer_Skin_Main extends WP_Upgrader_Skin {

	// @codingStandardsIgnoreStart
	public function header() {}
	public function footer() {}
	public function bulk_header() {}
	public function bulk_footer() {}
	// @codingStandardsIgnoreEnd

	/**
	 * return error
	 *
	 * @param  string $error error message
	 * @return string
	 */
	public function error($error) {
		if (!$error) return;
		global $updraftplus;
		if (is_wp_error($error)) {
			$updraftplus->log_wp_error($error, true);
		} elseif (is_string($error)) {
			$updraftplus->log($error);
			$updraftplus->log($error, 'warning-restore');
		}
	}

	protected function updraft_feedback($string) {

		if (isset($this->upgrader->strings[$string])) {
			$string = $this->upgrader->strings[$string];
		}

		if (false !== strpos($string, '%')) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ($args) {
				$args = array_map('strip_tags', $args);
				$args = array_map('esc_html', $args);
				$string = vsprintf($string, $args);
			}
		}
		if (empty($string)) return;

		global $updraftplus;
		$updraftplus->log_e($string);
	}
}

global $updraftplus;
$wp_version = $updraftplus->get_wordpress_version();

if (version_compare($wp_version, '5.3', '>=')) {
	if (!class_exists('Updraft_Restorer_Skin')) require_once(UPDRAFTPLUS_DIR.'/includes/updraft-restorer-skin-compatibility.php');
} else {
	class Updraft_Restorer_Skin extends Updraft_Restorer_Skin_Main {

		public function feedback($string) {
			parent::updraft_feedback($string);
		}
	}
}
