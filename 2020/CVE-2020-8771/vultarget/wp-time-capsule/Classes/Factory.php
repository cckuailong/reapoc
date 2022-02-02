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

class WPTC_Factory {
	private static
	$objectCache = array(),
	$aliases = array(
		'dropbox' => 'DropboxFacade',
		'g_drive' => 'GdriveFacade',
		's3' => 'S3Facade',
		'wasabi' => 'WasabiFacade',
	);

	private static function getClassName($name) {
		if (isset(self::$aliases[$name])) {
			$name = self::$aliases[$name];
		}

		$class = '';
		foreach (explode('-', $name) as $bit) {
			$class .= '_' . ucfirst($bit);
		}

		return 'WPTC' . $class;
	}

	public static function db() {
		if (!isset(self::$objectCache['WPDB'])) {
			global $wpdb;

			if ($wpdb) {
				$wpdb->hide_errors();
			}

			if (defined('WPTC_TEST_MODE')) {
				$wpdb->show_errors();
			}

			self::$objectCache['WPDB'] = $wpdb;
		}

		return self::$objectCache['WPDB'];
	}

	public static function get($name, $noCache = false) {
		$className = self::getClassName($name);

		if (!class_exists($className)) {
			return null;
		}

		if (!isset(self::$objectCache[$className]) || $noCache == true) {
			self::$objectCache[$className] = new $className();
		}

		return self::$objectCache[$className];
	}

	public static function set($name, $object) {
		if ($name == 'db') {
			self::$objectCache['WPDB'] = $object;
		} else {
			self::$objectCache[self::getClassName($name)] = $object;
		}
	}

	public static function reset() {
		self::$objectCache = array();
	}

	public static function secret($data) {
		return hash_hmac('sha1', $data, uniqid(mt_rand(), true)) . '-wptc-secret';
	}
}
