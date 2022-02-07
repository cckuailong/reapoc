<?php
/**
 * Initialise the tasks module and create the needed DB tables
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Tasks_Activation')) :

class Updraft_Tasks_Activation {

	private static $table_prefix;

	/**
	 * Format: key=<version>, value=array of method names to call
	 * Example Usage:
	 *	private static $db_updates = array(
	 *		'1.0.1' => array(
	 *			'update_101_add_new_column',
	 *		),
	 *	);
	 *
	 * @var Mixed
	 */
	private static $db_updates = array(
		'0.0.1' => array('create_tables'),
		'1.0.1' => array('add_attempts_and_class_identifier'),
		'1.1' => array('add_lock_column'),
	);


	const UPDRAFT_TASKS_DB_VERSION = '1.1';

	/**
	 * Initialise this class
	 */
	public static function init_db() {
		self::$table_prefix = defined('UPDRAFT_TASKS_TABLE_PREFIX') ? UPDRAFT_TASKS_TABLE_PREFIX : 'tm_';
	}
	
	/**
	 * This is the class entry point
	 */
	public static function install() {
		self::init_db();
		self::create_tables();
		// we need walk through all updates when install at first.
		self::check_updates();
	}

	/**
	 * Check needed tables in data base and if one of them doesn't exist force reinstall.
	 */
	public static function reinstall_if_needed() {
		static $done = false;

		if ($done) return;

		if (!self::check_if_tables_exist()) self::reinstall();

		$done = true;
	}

	/**
	 * Drop database version variable from option from database and run install again.
	 */
	public static function reinstall() {
		delete_option('updraft_task_manager_dbversion');
		self::install();
	}

	/**
	 * Check if needed task manager tables exist.
	 *
	 * @return bool
	 */
	public static function check_if_tables_exist() {
		global $wpdb;
		$our_prefix = $wpdb->base_prefix.self::$table_prefix;
		$tables = array($our_prefix.'tasks', $our_prefix.'taskmeta');

		foreach ($tables as $table) {
			$query = "SHOW TABLES LIKE '{$table}'";
			$tables = $wpdb->get_results($query, ARRAY_A);
			if (!is_array($tables) || 0 == count($tables)) return false;
		}

		return true;
	}

	/**
	 * See if any database schema updates are needed, and perform them if so.
	 * Example Usage:
	 * public static function update_101_add_new_column() {
	 *		$wpdb = $GLOBALS['wpdb'];
	 *		$wpdb->query('ALTER TABLE tm_tasks ADD task_expiry varchar(300) AFTER id');
	 *	}
	 */
	public static function check_updates() {
		self::init_db();
		$our_version = self::get_version();
		$db_version = get_option('updraft_task_manager_dbversion');
		if (!$db_version || version_compare($our_version, $db_version, '>')) {
			foreach (self::$db_updates as $version => $updates) {
				if (version_compare($version, $db_version, '>')) {
					foreach ($updates as $update) {
						call_user_func(array(__CLASS__, $update));
					}
				}
			}
			update_option('updraft_task_manager_dbversion', self::get_version());
		}
	}

	/**
	 * Returns the current version of the plugin
	 */
	public static function get_version() {
		return self::UPDRAFT_TASKS_DB_VERSION;
	}

	/**
	 * Create the database tables
	 */
	public static function create_tables() {
	
		$wpdb = $GLOBALS['wpdb'];

		$our_prefix = $wpdb->base_prefix.self::$table_prefix;
		$collate = '';

		if ($wpdb->has_cap('collation')) {
			if (!empty($wpdb->charset)) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if (!empty($wpdb->collate)) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		include_once ABSPATH.'wp-admin/includes/upgrade.php';

		// Important: obey the magical/arbitrary rules for formatting this stuff: https://codex.wordpress.org/Creating_Tables_with_Plugins
		// Otherwise, you get SQL errors and unwanted header output warnings when activating
		
		$create_tables = 'CREATE TABLE '.$our_prefix."tasks (
			task_id bigint(20) NOT NULL auto_increment,
			user_id bigint(20) NOT NULL,
			type varchar(300) NOT NULL,
			description varchar(300),
			PRIMARY KEY  (task_id),
			KEY user_id (user_id),
			time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
			status varchar(300)
			) $collate;
		";
		// KEY attribute_name (attribute_name)
		dbDelta($create_tables);

		$max_index_length = 191;
		
		$create_tables = 'CREATE TABLE '.$our_prefix."taskmeta (
			meta_id bigint(20) NOT NULL auto_increment,
			task_id bigint(20) NOT NULL default '0',
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY meta_key (meta_key($max_index_length)),
			KEY task_id (task_id)
			) $collate;
		";

		dbDelta($create_tables);
	}

	public static function add_attempts_and_class_identifier() {
		$wpdb = $GLOBALS['wpdb'];
		$our_prefix = $wpdb->base_prefix.self::$table_prefix;

		$wpdb->query("ALTER TABLE ".$our_prefix."tasks CHANGE COLUMN `task_id` `id` INT NOT NULL");
		$wpdb->query("ALTER TABLE ".$our_prefix."tasks MODIFY COLUMN `id` INT auto_increment");
		$wpdb->query("ALTER TABLE ".$our_prefix."tasks ADD attempts INT DEFAULT 0 AFTER type");
		$wpdb->query("ALTER TABLE ".$our_prefix."tasks ADD class_identifier varchar(300) DEFAULT 0 AFTER type");
	}
	
	public static function add_lock_column() {
		$wpdb = $GLOBALS['wpdb'];
		$our_prefix = $wpdb->base_prefix.self::$table_prefix;
		$wpdb->query('ALTER TABLE '.$our_prefix.'tasks ADD last_locked_at BIGINT DEFAULT 0 AFTER time_created');
	}

}

endif;
