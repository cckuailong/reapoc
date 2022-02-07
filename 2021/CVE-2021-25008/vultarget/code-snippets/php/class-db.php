<?php

/**
 * Functions used to manage the database tables
 *
 * @package Code_Snippets
 */
class Code_Snippets_DB {

	/**
	 * Unprefixed site-wide table name
	 */
	const TABLE_NAME = 'snippets';

	/**
	 * Unprefixed network-wide table name
	 */
	const MS_TABLE_NAME = 'ms_snippets';

	/**
	 * Side-wide table name
	 *
	 * @var string
	 */
	public $table;

	/**
	 * Network-wide table name
	 *
	 * @var string
	 */
	public $ms_table;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->set_table_vars();
	}

	/**
	 * Register the snippet table names with WordPress
	 *
	 * @since 2.0
	 * @uses  $wpdb
	 */
	function set_table_vars() {
		global $wpdb;

		$this->table = $wpdb->prefix . self::TABLE_NAME;
		$this->ms_table = $wpdb->base_prefix . self::MS_TABLE_NAME;

		/* Register the snippet table names with WordPress */
		$wpdb->snippets = $this->table;
		$wpdb->ms_snippets = $this->ms_table;

		$wpdb->tables[] = self::TABLE_NAME;
		$wpdb->ms_global_tables[] = self::MS_TABLE_NAME;
	}

	/**
	 * Validate the multisite parameter of the get_table_name() function
	 *
	 * @param bool|null $network
	 *
	 * @return bool
	 */
	function validate_network_param( $network ) {

		/* If multisite is not active, then the parameter should always be false */
		if ( ! is_multisite() ) {
			return false;
		}

		/* If $multisite is null, try to base it on the current admin page */
		if ( is_null( $network ) && function_exists( 'is_network_admin' ) ) {
			$network = is_network_admin();
		}

		return $network;
	}

	/**
	 * Return the appropriate snippet table name
	 *
	 * @param string|bool|null $multisite Retrieve the multisite table name or the site table name?
	 *
	 * @return string The snippet table name
	 * @since 2.0
	 *
	 */
	function get_table_name( $multisite = null ) {

		/* If the first parameter is a string, assume it is a table name */
		if ( is_string( $multisite ) ) {
			return $multisite;
		}

		/* Validate the multisite parameter */
		$multisite = $this->validate_network_param( $multisite );

		/* Retrieve the table name from $wpdb depending on the value of $multisite */

		return ( $multisite ? $this->ms_table : $this->table );
	}

	/**
	 * Create the snippet tables if they do not already exist
	 */
	public function create_missing_tables() {
		global $wpdb;

		/* Create the network snippets table if it doesn't exist */
		if ( is_multisite() && $wpdb->get_var( "SHOW TABLES LIKE '$this->ms_table'" ) !== $this->ms_table ) {
			$this->create_table( $this->ms_table );
		}

		/* Create the table if it doesn't exist */
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$this->table'" ) !== $this->table ) {
			$this->create_table( $this->table );
		}
	}

	/**
	 * Create the snippet tables, or upgrade them if they already exist
	 */
	public function create_or_upgrade_tables() {
		if ( is_multisite() ) {
			$this->create_table( $this->ms_table );
		}

		$this->create_table( $this->table );
	}

	/**
	 * Create a snippet table if it does not already exist
	 *
	 * @param $table_name
	 */
	public static function create_missing_table( $table_name ) {
		global $wpdb;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name ) {
			return;
		}

		self::create_table( $table_name );
	}

	/**
	 * Create a single snippet table
	 *
	 * @param string $table_name The name of the table to create
	 *
	 * @return bool whether the table creation was successful
	 * @since 1.6
	 * @uses  dbDelta() to apply the SQL code
	 */
	public static function create_table( $table_name ) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		/* Create the database table */
		$sql = "CREATE TABLE $table_name (
				id          BIGINT(20)  NOT NULL AUTO_INCREMENT,
				name        TINYTEXT    NOT NULL DEFAULT '',
				description TEXT        NOT NULL DEFAULT '',
				code        LONGTEXT    NOT NULL DEFAULT '',
				tags        LONGTEXT    NOT NULL DEFAULT '',
				scope       VARCHAR(15) NOT NULL DEFAULT 'global',
				priority    SMALLINT    NOT NULL DEFAULT 10,
				active      TINYINT(1)  NOT NULL DEFAULT 0,
				modified    DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY  (id)
			) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		$success = empty( $wpdb->last_error );

		if ( $success ) {
			do_action( 'code_snippets/create_table', $table_name );
		}

		return $success;
	}
}
