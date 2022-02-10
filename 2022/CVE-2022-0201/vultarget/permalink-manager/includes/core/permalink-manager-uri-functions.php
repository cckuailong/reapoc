<?php

/**
 * Functions used to create, edit and remove custom permalinks
 */
class Permalink_Manager_URI_Functions extends Permalink_Manager_Class {

	public function __construct() {

	}

	public static function get_single_uri_key($element_id, $is_tax = false) {
		// Check if the element ID is numeric
		if(empty($element_id) || !is_numeric($element_id)) { return; }

		if($is_tax) {
			$element_id = "tax-{$element_id}";
		}

		return $element_id;
	}

	/**
	 * Save URI to the custom permalinks array
	 */
	public static function save_single_uri($element, $element_uri = null, $is_tax = false, $db_save = false) {
		global $permalink_manager_uris;

		// Get the element key
		$element_key = self::get_single_uri_key($element, $is_tax);

		// Save the custom permalink if the URI is not empty
		if(!empty($element_key) && !empty($element_uri)) {
			$permalink_manager_uris[$element_key] = Permalink_Manager_Helper_Functions::sanitize_title($element_uri, true);

			if($db_save) {
				self::save_all_uris($permalink_manager_uris);
			}
		}
	}

	/**
	 * Remove URI to the custom permalinks array
	 */
	public static function remove_single_uri($element, $is_tax = false, $db_save = false) {
		global $permalink_manager_uris;

		// Get the element key
		$element_key = self::get_single_uri_key($element, $is_tax);

		// Check if the custom permalink is assigned to this post
		if(!empty($element_key) && isset($permalink_manager_uris[$element_key])) {
			unset($permalink_manager_uris[$element_key]);
		}

		if($db_save) {
			self::save_all_uris($permalink_manager_uris);
		}
	}

	/**
	 * Save the array with custom permalinks
	 */
	public static function save_all_uris($updated_uris = null) {
		if(is_null($updated_uris)) {
			global $permalink_manager_uris;
			$updated_uris = $permalink_manager_uris;
		}

		if(is_array($updated_uris) && !empty($updated_uris)) {
			update_option('permalink-manager-uris', $updated_uris);
		}
	}

}

?>
