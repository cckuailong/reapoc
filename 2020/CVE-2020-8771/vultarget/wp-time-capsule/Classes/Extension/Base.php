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

abstract class WPTC_Extension_Base {
	const TYPE_DEFAULT = 1;
	const TYPE_OUTPUT = 2;

	protected
	$dropbox,
	$dropbox_path,
	$config,
	$backup_id
	;

	private $WPTC_CHUNKED_UPLOAD_THREASHOLD;

	public function __construct() {
		$this->dropbox = WPTC_Factory::get(DEFAULT_REPO);
		$this->config = WPTC_Factory::get('config');
		$this->backup_id = wptc_get_cookie('backupID');
	}

	public function wptc_func_map($func_dets = array()) {

	}

	public function set_chunked_upload_threashold($threashold) {
		$this->WPTC_CHUNKED_UPLOAD_THREASHOLD = $threashold;

		return $this;
	}

	public function get_chunked_upload_threashold() {
		if ($this->WPTC_CHUNKED_UPLOAD_THREASHOLD !== null) {
			return $this->WPTC_CHUNKED_UPLOAD_THREASHOLD;
		}

		return WPTC_CHUNKED_UPLOAD_THREASHOLD;
	}

	abstract public function complete();
	abstract public function failure();

	abstract public function get_menu();
	abstract public function get_type();

	abstract public function is_enabled();
	abstract public function set_enabled($bool);
}
