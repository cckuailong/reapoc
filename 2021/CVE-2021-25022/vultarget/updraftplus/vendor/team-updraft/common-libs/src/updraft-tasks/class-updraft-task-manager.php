<?php
/**
 * A task manager that locks and processes the task queue
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_Manager_1_2')) :

abstract class Updraft_Task_Manager_1_2 {

	protected $loggers;

	public $commands;

	private $queue_semaphore;
	
	/**
	 * Set this to the number of seconds for the lock timeout, or 0 to not use a lock
	 */
	protected $use_per_task_lock = 0;

	/**
	 * The Task Manager constructor
	 */
	public function __construct() {

		if (!class_exists('Updraft_Task_1_1')) require_once('class-updraft-task.php');
		if (!class_exists('Updraft_Task_Manager_Commands_1_0')) require_once('class-updraft-task-manager-commands.php');
		if (!class_exists('Updraft_Semaphore_2_2')) require_once(dirname(__FILE__).'/../updraft-semaphore/class-updraft-semaphore.php');
		if (!class_exists('Updraft_Tasks_Activation')) require_once(dirname(__FILE__).'/class-updraft-tasks-activation.php');

		$this->commands = new Updraft_Task_Manager_Commands_1_0($this);

		add_action('wp_ajax_updraft_taskmanager_ajax', array($this, 'updraft_taskmanager_ajax'));

		do_action('updraft_task_manager_loaded', $this);
	}

	/**
	 * The Task Manager AJAX handler
	 */
	public function updraft_taskmanager_ajax() {

		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];

		if (!wp_verify_nonce($nonce, 'updraft-task-manager-ajax-nonce') || empty($_REQUEST['subaction']))
			die('Security check failed');

		$subaction = $_REQUEST['subaction'];

		$allowed_commands = Updraft_Task_Manager_Commands_1_0::get_allowed_ajax_commands();
		
		if (in_array($subaction, $allowed_commands)) {

			if (isset($_REQUEST['action_data']))
				$data = $_REQUEST['action_data'];

			$results = call_user_func(array($this->commands, $subaction), $data);
			
			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}
			
			echo json_encode($results);
		} else {
			echo json_encode("{'error' : 'No such command found'}");
		}
		die;
	}

	/**
	 * Process a single task in the queue
	 *
	 * @param int|Updraft_Task - $task Task ID or Updraft_Task object.
	 * @return boolean|WP_Error - status of task or error if task not found
	 */
	public function process_task($task) {

		if (!is_a($task, 'Updraft_Task_1_1')) {
			$task_id = (int) $task;
			$task = $this->get_task_instance($task_id);
		}
		
		if (!$task) return new WP_Error('id_invalid', 'Task not found or ID is invalid');

		return $task->attempt(apply_filters('updraft_task_lock_for', $this->use_per_task_lock, $this));
		
	}

	/**
	 * Gets a list of all tasks that matches the $status flag
	 *
	 * @param int|Updraft_Task - $task Task ID or Updraft_Task object.
	 * @return String|WP_Error - status of task or error if task not found.
	 */
	public function get_task_status($task) {

		if (!($task instanceof Updraft_Task_1_1)) {
			$task_id = (int) $task;
			$task = $this->get_task_instance($task_id);
		}
		
		if (!$task) return new WP_Error('id_invalid', 'Task not found or ID is invalid');

		return $task->get_status();
	}

	/**
	 * Ends a given task
	 *
	 * @param int|Updraft_Task - $task Task ID or Updraft_Task object.
	 * @return boolean|WP_Error - Status of the operation or error if task not found.
	 */
	public function end_task($task) {
		
		if (!($task instanceof Updraft_Task_1_1)) {
			$task_id = (int) $task;
			$task = $this->get_task_instance($task_id);
		}
		
		if (!$task) return new WP_Error('id_invalid', 'Task not found or ID is invalid');

		return $task->complete();
	}

    /**
     * Process a the queue of a specifed task type
     *
     * @param string $type queue type to process
     * @return bool true on success, false otherwise
     */
	public function process_queue($type) {

		$task_list = $this->get_active_tasks($type);
		$total = is_array($task_list) ? count($task_list) : 0;

		if (1 > $total) {
			$this->log(sprintf('The queue for tasks of type "%s" is empty. Aborting!', $type));
			return true;
		} else {
			$this->log(sprintf('A total of %d tasks of type %s found and will be processed in this iteration', $total, $type));
		}

		$this->queue_semaphore = new Updraft_Semaphore_2_2($type);
		
		$this->queue_semaphore->set_loggers($this->loggers);

		if (!$this->queue_semaphore->lock()) {

			$this->log(sprintf('Failed to gain semaphore lock (%s) - another process is already processing the queue - aborting (if this is wrong - i.e. if the other process crashed without removing the lock, then another can be started after 1 minute', $type));

			return false;
		}

		$done = 0;
		foreach ($task_list as $task) {
			$this->process_task($task);
			$this->queue_semaphore->update_lock(20);
			$done++;
			/**
			 * Filters if the queue should be interrupted. Used after processing each task.
			 *
			 * @param boolean $interrupt_queue - If the queue should be interrupted. Default to FALSE
			 * @param object  $task            - The current task object
			 * @param object  $task_manager    - The task manager instance
			 */
			if (apply_filters('updraft_interrupt_tasks_queue_'.$type, false, $task, $this)) {
				break;
			}
		}

		$this->queue_semaphore->unlock();
		$this->log(sprintf('Successfully processed the queue (%s). %d tasks were processed out of %d.', $type, $done, $total));
		$this->queue_semaphore->delete();

		return $done == $total;
	}

	/**
	 * Cleans out all complete tasks from the DB.
	 *
	 * @param String $type type of the task
	 */
	public function clean_up_old_tasks($type) {
		$completed_tasks = $this->get_completed_tasks($type);

		if (!$completed_tasks) return false;

		$this->log(sprintf('Cleaning up tasks of type (%s). A total of %d tasks will be deleted.', $type, count($completed_tasks)));

		foreach ($completed_tasks as $task) {
			$task->delete_meta();
			$task->delete();
		}

		return true;
	}

	/**
	 * Delete all tasks from queue.
	 *
	 * @param string $type
	 *
	 * @return boolean|integer Number of rows deleted, or (boolean)false upon error
	 */
	public function delete_tasks($task_type) {
		global $wpdb;

		$sql = "DELETE t, tm FROM `{$wpdb->base_prefix}tm_tasks` t LEFT JOIN `{$wpdb->base_prefix}tm_taskmeta` tm ON t.id = tm.task_id WHERE t.type = '{$task_type}'";

		return $wpdb->query($sql);
	}

	/**
	 * Get count of completed and all tasks.
	 *
	 * @return array - [ ['complete_tasks' => , 'all_tasks' => ] ]
	 */
	public function get_status($task_type) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT complete_tasks, all_tasks FROM (SELECT COUNT(*) AS complete_tasks FROM {$wpdb->base_prefix}tm_tasks WHERE `type` = %s AND `status` = %s) a, (SELECT COUNT(*) AS all_tasks FROM {$wpdb->base_prefix}tm_tasks WHERE `type` = %s) b",
			array(
				$task_type,
				'complete',
				$task_type,
			)
		);

		$status = $wpdb->get_row($query, ARRAY_A);

		if (empty($status)) {
			$status = array(
				'complete_tasks' => 0,
				'all_tasks' => 0,
			);
		}

		return $status;
	}

	/**
	 * Fetches  a list of all active tasks
	 *
	 * @param String $type type of the task
	 * @return Mixed - array of Task ojects or NULL if none found
	 */
	public function get_active_tasks($type) {
		return $this->get_tasks('active', $type);
	}

	/**
	 * Gets a list of all completed tasks
	 *
	 * @param String $type type of the task
	 * @return Mixed - array of Task ojects or NULL if none found
	 */
	public function get_completed_tasks($type) {
		return $this->get_tasks('complete', $type);
	}

	/**
	 * Gets a list of all tasks that matches the $status flag
	 *
	 * @param String $status - status of tasks to return, defaults to all tasks
	 * @param String $type   - type of task
	 *
	 * @return Mixed - array of Task objects or NULL if none found
	 */
	public function get_tasks($status, $type) {
		global $wpdb;

		$tasks = array();
		
		if (array_key_exists($status, Updraft_Task_1_1::get_allowed_statuses())) {
			$sql = $wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}tm_tasks WHERE status = %s AND type = %s", $status, $type);
		} else {
			$sql = $wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}tm_tasks WHERE type = %s", $type);
		}

		$_tasks = $wpdb->get_results($sql);

		if (!$_tasks) {
			// if we got an error then check if task manager tables are in the database
			// and recreate them if needed.
			if ($wpdb->last_error) {
				Updraft_Tasks_Activation::reinstall_if_needed();
			}
			return;
		}


		foreach ($_tasks as $_task) {
			$task = $this->get_task_instance($_task->id);
			if ($task) array_push($tasks, $task);
		}

		return $tasks;
	}

	/**
	 * Retrieve the task instance using its ID
	 *
	 * @access public
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $task_id Task ID.
	 * @return Task|Boolean Task object, false otherwise.
	 */
	public function get_task_instance($task_id) {
		global $wpdb;

		$task_id = (int) $task_id;
		if (!$task_id) return false;

		$sql = $wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}tm_tasks WHERE id = %d LIMIT 1", $task_id);
		$_task = $wpdb->get_row($sql);

		if (!$_task)
			return false;

		$class_identifier = $_task->class_identifier;

		if (class_exists($class_identifier))
			$task_instance = new $class_identifier($_task);
			$task_instance->set_loggers($this->loggers);
			return $task_instance;

		return false;
	}

	/**
	 * Sets the logger for this instance.
	 *
	 * @param array $loggers - the loggers for this task
	 */
	public function set_loggers($loggers) {
		foreach ($loggers as $logger) {
			$this->add_logger($logger);
		}
	}

	/**
	 * Add a logger to loggers list
	 *
	 * @param Object $logger - a logger for the instance
	 */
	public function add_logger($logger) {
		$this->loggers[] = $logger;
	}

	/**
	 * Return list of loggers
	 *
	 * @return array
	 */
	public function get_loggers() {
		return $this->loggers;
	}

	/**
	 * Captures and logs any interesting messages
	 *
	 * @param String $message    - the error message
	 * @param String $error_type - the error type
	 */
	public function log($message, $error_type = 'info') {
		if (isset($this->loggers)) {
			foreach ($this->loggers as $logger) {
				$logger->log($error_type, $message);
			}
		}
	}

}

endif;
