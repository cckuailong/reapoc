<?php
/**
 * The DB handle class for the options framework
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_Meta')) :

class Updraft_Task_Meta {

	/**
	 * This method gets data from the task meta table in the WordPress database
	 *
	 * @param  int    $id  the instance id of the task
	 * @param  String $key the key to get
	 *
	 * @return Mixed  The option from the database
	 */
	public static function get_task_meta($id, $key) {
		global $wpdb;

		$id = (int) $id;
		if (!$id) return false;

		$sql = $wpdb->prepare("SELECT meta_value FROM {$wpdb->base_prefix}tm_taskmeta WHERE task_id = %d AND meta_key = %s LIMIT 1", $id, $key);

		$meta = $wpdb->get_var($sql);

		if ($meta)
			return maybe_unserialize($meta);
		else return false;
	}


	/**
	 * This method is used to update data stored in the WordPress database
	 *
	 * @param  int    $id    the instance id of the task
	 * @param  String $key   the key of the data to update
	 * @param  Mixed  $value the value to save to the option
	 *
	 * @return Mixed            the status of the update operation
	 */
	public static function update_task_meta($id, $key, $value) {
		global $wpdb;

		$id = (int) $id;
		if (!$id) return false;

		$value = maybe_serialize($value);

		if (false !== self::get_task_meta($id, $key)) {
			$sql = $wpdb->prepare("UPDATE {$wpdb->base_prefix}tm_taskmeta SET meta_value = %s WHERE meta_key = %s AND task_id = %d", $value, $key, $id);
		} else {
			$sql = $wpdb->prepare("INSERT INTO {$wpdb->base_prefix}tm_taskmeta (task_id, meta_key, meta_value) VALUES (%d, %s, %s)", $id, $key, $value);
		}

		return $wpdb->query($sql);
	}

	/**
	 * This method is used to delete task data stored in the WordPress database
	 *
	 * @param  int    $id  the instance id of the task
	 * @param  String $key the key to delete
	 *
	 * @return Mixed  the status of the delete operation
	 */
	public static function delete_task_meta($id, $key) {
		global $wpdb;

		$id = (int) $id;
		if (!$id) return false;

		$sql = $wpdb->prepare("DELETE FROM {$wpdb->base_prefix}tm_taskmeta WHERE task_id = %d AND meta_key = %s LIMIT 1", $id, $key);
		return $wpdb->query($sql);
	}

	/**
	 * Bulk delete task
	 *
	 * @param  int $id the instance id of the task
	 */
	public static function bulk_delete_task_meta($id) {
		global $wpdb;

		$id = (int) $id;
		if (!$id) return false;

		$sql = $wpdb->prepare("DELETE FROM {$wpdb->base_prefix}tm_taskmeta WHERE task_id = %d", $id);
		return $wpdb->query($sql);
	}
}

endif;
