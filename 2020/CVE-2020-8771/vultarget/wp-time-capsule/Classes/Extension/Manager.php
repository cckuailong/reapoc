<?php
/**
* A class with functions the perform a backup of WordPress
*
* @copyright Copyright (C) 2011-2014 Awesoft Pty. Ltd. All rights reserved.
* @author Michael De Wildt (http://www.mikeyd.com.au/)
* @license This program is free software; you can redistribute it and/or modify
*          it under the terms of the GNU General Public License as published by
*          the Free Software Foundation; either version 2 of the License, or
*          (at your option) any later version.
*
*          This program is distributed in the hope that it will be useful,
*          but WITHOUT ANY WARRANTY; without even the implied warranty of
*          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*          GNU General Public License for more details.
*
*          You should have received a copy of the GNU General Public License
*          along with this program; if not, write to the Free Software
*          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
*/

class WPTC_Extension_Manager {
	const API_VERSION = 0;
	const API_KEY = '7121664d208a603de9d93e564adfcd0a';

	private
	$objectCache = array(),
	$extensionsCache,
	$new_site = true
	;

	public static function construct() {
		return new self();
	}

	public function __construct() {
		$extensions = array();
	}

	public function get_url($api = false) {
		if (defined('WPTC_URL')) {
			$url = WPTC_URL;
		} else {
			$url = 'http://wptc.com';
		}

		if ($api) {
			$url .= '/' . self::API_VERSION;
		}

		return $url;
	}

	public function get_install_url() {
		return 'admin.php?page=backup-to-dropbox-premium';
	}

	public function get_buy_url() {
		return $this->get_url() . '/buy';
	}

	public function get_extensions() {
		if (!$this->extensionsCache) {
			$params = array(
				'apikey' => self::API_KEY,
				'site' => home_url(),
			);

			$response = wp_remote_get("{$this->get_url(true)}/products?" . http_build_query($params, '', '&'));

			if (is_wp_error($response)) {
				throw new Exception(__('There was an error getting the list of premium extensions', 'wptc'));
			}

			$this->extensionsCache = json_decode($response['body'], true);
		}

		return $this->extensionsCache;
	}

	public function is_installed($name) {
		return $this->get_instance($name);
	}

	public function install($name) {
		if (!defined('FS_METHOD')) {
			define('FS_METHOD', 'direct');
		}

		WP_Filesystem();

		$params = array(
			'apikey' => self::API_KEY,
			'name' => $name,
			'site' => home_url(),
			'version' => WPTC_VERSION,
		);

		$download_file = download_url("{$this->get_url(true)}/download?" . http_build_query($params, '', '&'));

		if (is_wp_error($download_file)) {
			$errorMsg = strtolower($download_file->get_error_message());
			if ($errorMsg == 'Forbidden') {
				$errorMsg = __('access is deined, this could be because your payment has expired.', 'wptc');
			} elseif (!$errorMsg) {
				$errorMsg = __('you have exceeded your download limit for this extension on this site.', 'wptc');
			}

			throw new Exception(__('There was an error downloading your premium extension because', 'wptc') . " $errorMsg");
		}

		$result = unzip_file($download_file, WPTC_EXTENSIONS_DIR);
		if (is_wp_error($result)) {
			$errorCode = $result->get_error_code();
			$errorMsg = strtolower($result->get_error_message());

			if (in_array($errorCode, array('copy_failed', 'incompatible_archive'))) {
				$errorMsg = sprintf(__("'%s' is not writeable.", 'wptc'), WPTC_EXTENSIONS_DIR);
			}

			throw new Exception(__('There was an error installing your premium extension because', 'wptc') . " $errorMsg");
		}

		@unlink($download_file);

		$extensions = get_option('wptc-premium-extensions');

		$extensions[$name] = str_replace(' ', '', ucwords($name)) . '.php';

		update_option('wptc-premium-extensions', $extensions);

		//Support for pre PHP 5.3
		if (!function_exists('spl_autoload_register')) {
			require_once WPTC_EXTENSIONS_DIR . $extensions[$name];
		}

		return $this->get_instance($name);
	}

	public function get_output() {
		foreach ($this->objectCache as $obj) {
			if ($obj && $obj->get_type() == WPTC_Extension_Base::TYPE_OUTPUT && $obj->is_enabled()) {
				return $obj;
			}
		}
		return $this->get_instance('DefaultOutput');
	}

	public function add_menu_items() {
		foreach ($this->objectCache as $obj) {
			$title = $obj->get_menu();
			$slug = $this->get_menu_slug($obj);
			$func = $this->get_menu_func($obj);

			add_submenu_page('backup-to-dropbox', $title, $title, 'activate_plugins', $slug, $func);
		}
	}

	public function complete() {
		$this->call('complete');
	}

	public function failure() {
		$this->call('failure');
	}

	public function get_menu_slug($obj) {
		return str_replace('_', '-', strtolower(get_class($obj)));
	}

	public function get_menu_func($obj) {
		return strtolower(get_class($obj));
	}

	private function call($func) {
		foreach ($this->objectCache as $obj) {
			if ($obj && $obj->is_enabled()) {
				$obj->$func();
			}
		}
	}

	private function get_instance($name) {
		$class = 'WPTC_Extension_' . str_replace(' ', '', ucwords($name));

		if (!isset($this->objectCache[$class])) {
			if (!class_exists($class)) {
				return false;
			}

			$this->objectCache[$class] = new $class();
		}

		return $this->objectCache[$class];
	}
}
