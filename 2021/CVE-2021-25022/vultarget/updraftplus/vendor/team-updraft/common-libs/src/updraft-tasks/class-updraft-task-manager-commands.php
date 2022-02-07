<?php
/**
 * The AJAX Commands manager class
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!defined('Updraft_Task_Manager_Commands_1_0')) :

class Updraft_Task_Manager_Commands_1_0 {

	protected $task_manager;

	/**
	 * Constructor
	 *
	 * @param Updraft_Task_Manager_1_2 $task_manager The task manager instance
	 */
	public function __construct($task_manager) {
		$this->task_manager = $task_manager;
	}

	/**
	 * A list of allowed commands via AJAX
	 *
	 * @return array - List of allowed commands
	 */
	public static function get_allowed_ajax_commands() {

		$commands = array(
			'process_task',
			'get_task_status',
			'end_task',
			'process_queue',
			'get_active_tasks',
			'clean_up_old_tasks',
		);

		return apply_filters('updraft_task_manager_allowed_ajax_commands', $commands);
	}

	/**
	 * Process a single task in the queue
	 *
	 * @param array $data data passed via AJAX
	 * @return void|WP_Error status of the operation
	 */
	public function process_task($data) {

		if (!isset($data['task_id']))
			return new WP_Error('id_missing', 'Task ID is missing or invalid');

		$task_id = (int) $data['task_id'];

		$response = apply_filters('updraft_task_manager_process_task_response', "Processing task: {$task_id}", $task_id);
		$this->close_browser_connection($response);
		$this->task_manager->process_task($task_id);
	}

	/**
	 * Process a single task in the queue
	 *
	 * @param array $data data passed via AJAX
	 * @return String - status of task or false if none found
	 */
	public function get_task_status($data) {

		if (!isset($data['task_id']))
			return new WP_Error('id_missing', 'Task ID is missing or invalid');

		$task_id = (int) $data['task_id'];

		return $this->task_manager->get_task_status($task_id);
	}

	/**
	 * Ends a given task
	 *
	 * @param array $data data passed via AJAX
	 * @return boolean - Status of the operation.
	 */
	public function end_task($data) {
		
		if (!isset($data['task_id']))
			return new WP_Error('id_missing', 'Task ID is missing or invalid');

		$task_id = (int) $data['task_id'];

		$status = $this->task_manager->end_task($task_id);
		
		if (!$status) return new WP_Error('end_task_failed', 'Task is already ended');

		$response = apply_filters('updraft_task_manager_end_task_response', "Successfully ended task with id : {$task_id}", $task_id);
		$this->close_browser_connection($response);
	}

	/**
	 * Fetches a list of all active tasks
	 *
	 * @param array $data data passed via AJAX
	 * @return Mixed - array of UpdraftPlus_Task ojects or NULL if none found
	 */
	public function get_active_tasks($data) {

		if (!isset($data['type']))
			return new WP_Error('type_missing', 'Task type is missing or invalid');

		$type = $data['type'];
		$tasks = $this->task_manager->get_active_tasks($type);

		$ids = array();

		if ($tasks) {
			foreach ($tasks as $task) {
				array_push($ids, $task->get_id());
			}
		}

		$response = apply_filters('updraft_task_manager_get_active_tasks_response', $ids, $type);
		return $response;
	}

	/**
	 * Cleans out all complete tasks from the DB.
	 *
	 * @param array $data data passed via AJAX
	 * @return void|WP_Error status of the operation 
	 */
	public function clean_up_old_tasks($data) {

		if (!isset($data['type']))
			return new WP_Error('type_missing', 'Task type is missing or invalid');

		$type = $data['type'];
		$status = $this->task_manager->clean_up_old_tasks($type);

		if (!$status) return new WP_Error('clean_up_failed', 'Queue is already empty or the task type invalid');
		
		$response = apply_filters('updraft_task_manager_clean_up_old_tasks_response', "Cleaned up old tasks of type : $type", $type);
		$this->close_browser_connection($response);
	}

	/**
	 * Processes a queue of a specific type of task
	 *
	 * @param array $data data passed via AJAX
	 * @return void|WP_Error status of the operation 
	 */
	public function process_queue($data) {
		if (!isset($data['type']))
			return new WP_Error('type_missing', 'Task type is missing or invalid');

		$type = $data['type'];

		$response = apply_filters('updraft_task_manager_process_queue_response', "Processing queue of type {$type}", $type);

		$this->close_browser_connection(json_encode($response));
		$status = $this->task_manager->process_queue($type);

		if (!$status)
			return new WP_Error('process_queue_operation_failed', 'Failed to process the queue');
	}

	/**
	 * Close browser connection so that it can resume AJAX polling
	 *
	 * @param array $txt Response to browser
	 * @return void 
	 */
	public function close_browser_connection($txt = '') {
		header('Content-Length: '.((!empty($txt)) ? 5+strlen($txt) : '0'));
		header('Connection: close');
		header('Content-Encoding: none');
		if (session_id()) session_write_close();
		echo "\r\n\r\n";
		echo $txt;

		$levels = ob_get_level();
		
		for ($i = 0; $i < $levels; $i++) {
			ob_end_flush();
		}

		flush();
	}
}

endif;
