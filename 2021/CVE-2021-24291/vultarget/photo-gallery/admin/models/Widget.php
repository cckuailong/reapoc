<?php

/**
 * Class WidgetModel_bwg
 */
class WidgetModel_bwg {
	/**
	* @return array|null|object
	*/
	public function get_gallery_rows_data() {
		global $wpdb;
		$query = 'SELECT * FROM ' . $wpdb->prefix . 'bwg_gallery WHERE `published`=1';
		$rows = $wpdb->get_results($query);
		return $rows;
	}

	/**
	* @return array|null|object
	*/
	public function get_album_rows_data() {
		global $wpdb;
		$query = 'SELECT * FROM ' . $wpdb->prefix . 'bwg_album WHERE `published`=1';
		$rows = $wpdb->get_results($query);
		return $rows;
	}

	/**
	* @return array|null|object
	*/
	public function get_theme_rows_data() {
		global $wpdb;
		$query = 'SELECT `id`,`name`,`default_theme` FROM `' . $wpdb->prefix . 'bwg_theme` ORDER BY `default_theme` DESC';
		$rows = $wpdb->get_results($query);
		return $rows;
	}
}
