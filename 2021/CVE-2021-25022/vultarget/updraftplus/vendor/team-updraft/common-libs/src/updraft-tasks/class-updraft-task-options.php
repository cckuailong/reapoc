<?php
/**
 * The options framework for tasks
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_Options')) :

class Updraft_Task_Options {
	
	/**
	 * This method gets an option from the task meta table in the WordPress database
	 *
	 * @param  int    $instance_id the instance id of the task
	 * @param  String $option      the name of the option to get
	 * @param  Mixed  $default     a value to return if the option is not currently set
	 *
	 * @return Mixed  The option from the database
	 */
	public static function get_task_option($instance_id, $option, $default = null) {

		$tmp = Updraft_Task_Meta::get_task_meta($instance_id, 'task_options');

		if (isset($tmp[$option])) {
			$value = $tmp[$option];
		} else {
			$value = $default;
		}

		/**
		 * Filters the value of an existing option.
		 *
		 * The dynamic portion of the hook name, `$option`, refers to the option name.
		 */
		return apply_filters("ud_task_option_{$option}", maybe_unserialize($value), $option, $default, $instance_id);
	}

	/**
	 * This method is used to update a task option stored in the WordPress database
	 *
	 * @param  int    $instance_id the instance id of the task
	 * @param  String $option      the name of the option to update
	 * @param  Mixed  $value       the value to save to the option
	 *
	 * @return Mixed           	   the status of the update operation
	 */
	public static function update_task_option($instance_id, $option, $value) {

		$option = trim($option);

		if (empty($option)) return false;

		$old_value = self::get_task_option($instance_id, $option);

		/**
		 * Filters a specific option before its value is (maybe) serialized and updated.
		 */
		$value = apply_filters("ud_pre_update_task_option_{$option}", $value, $old_value, $option, $instance_id);

		$tmp = Updraft_Task_Meta::get_task_meta($instance_id, 'task_options');

		if (!is_array($tmp)) $tmp = array();
		$tmp[$option] = maybe_serialize($value);
		
		$result = Updraft_Task_Meta::update_task_meta($instance_id, 'task_options', $tmp);

		if ($result) {

			/**
			 * Fires after the value of a specific option has been successfully updated.
			 */
			do_action("ud_update_task_option_{$option}", $value, $old_value, $option, $instance_id);
		}

		return $result;
	}

	/**
	 * This method is used to delete a task option stored in the WordPress database
	 *
	 * @param  int    $instance_id the instance id of the task
	 * @param  String $option      the option to delete
	 */
	public static function delete_task_option($instance_id, $option) {

		/**
		 * Fires immediately before an option is deleted.
		 */
		do_action("ud_before_delete_task_option", $option, $instance_id);

		$tmp = Updraft_Task_Meta::get_task_meta($instance_id, 'task_options');

		if (is_array($tmp)) {
			if (isset($tmp[$option])) unset($tmp[$option]);
		} else {
			$tmp = array();
		}

		$result = Updraft_Task_Meta::update_task_meta($instance_id, 'task_options', $tmp);

		if ($result) {
			
			/**
			 * Fires after a specific option has been successfully deleted.
			*/
			do_action("ud_delete_task_option_{$option}", $option);
		}

		return $result;
	}

	/**
	 * This method gets all options assoicated with a task
	 *
	 * @param  int $instance_id the instance id of the task
	 *
	 * @return Mixed  The options from the database
	 */
	public static function get_all_task_options($instance_id) {

		$value = Updraft_Task_Meta::get_task_meta($instance_id, 'task_options');
		return apply_filters("ud_all_task_options", maybe_unserialize($value), $instance_id);
	}
}

endif;
