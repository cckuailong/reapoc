<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

class UpdraftPlus_Database_Utility {

	private $whichdb;

	private $table_prefix_raw;

	private $dbhandle;

	/**
	 * Constructor
	 */
	/**
	 * Constructor
	 *
	 * @param String $whichdb          - which database is being backed up
	 * @param String $table_prefix_raw - the base table prefix
	 * @param Object $dbhandle         - WPDB object
	 */
	public function __construct($whichdb, $table_prefix_raw, $dbhandle) {
		$this->whichdb = $whichdb;
		$this->table_prefix_raw = $table_prefix_raw;
		$this->dbhandle = $dbhandle;
	}

	/**
	 * The purpose of this function is to make sure that the options table is put in the database first, then the users table, then the site + blogs tables (if present - multisite), then the usermeta table; and after that the core WP tables - so that when restoring we restore the core tables first
	 *
	 * @param Array $a_arr the first array
	 * @param Array $b_arr the second array
	 *
	 * @return Integer - the sort result, according to the rules of PHP custom sorting functions
	 */
	public function backup_db_sorttables($a_arr, $b_arr) {

		$a = $a_arr['name'];
		$a_table_type = $a_arr['type'];
		$b = $b_arr['name'];
		$b_table_type = $b_arr['type'];
	
		// Views must always go after tables (since they can depend upon them)
		if ('VIEW' == $a_table_type && 'VIEW' != $b_table_type) return 1;
		if ('VIEW' == $b_table_type && 'VIEW' != $a_table_type) return -1;
	
		if ('wp' != $this->whichdb) return strcmp($a, $b);

		global $updraftplus;
		if ($a == $b) return 0;
		$our_table_prefix = $this->table_prefix_raw;
		if ($a == $our_table_prefix.'options') return -1;
		if ($b == $our_table_prefix.'options') return 1;
		if ($a == $our_table_prefix.'site') return -1;
		if ($b == $our_table_prefix.'site') return 1;
		if ($a == $our_table_prefix.'blogs') return -1;
		if ($b == $our_table_prefix.'blogs') return 1;
		if ($a == $our_table_prefix.'users') return -1;
		if ($b == $our_table_prefix.'users') return 1;
		if ($a == $our_table_prefix.'usermeta') return -1;
		if ($b == $our_table_prefix.'usermeta') return 1;

		if (empty($our_table_prefix)) return strcmp($a, $b);

		try {
			$core_tables = array_merge($this->dbhandle->tables, $this->dbhandle->global_tables, $this->dbhandle->ms_global_tables);
		} catch (Exception $e) {
			$updraftplus->log($e->getMessage());
		}
		
		if (empty($core_tables)) $core_tables = array('terms', 'term_taxonomy', 'termmeta', 'term_relationships', 'commentmeta', 'comments', 'links', 'postmeta', 'posts', 'site', 'sitemeta', 'blogs', 'blogversions', 'blogmeta');

		$na = UpdraftPlus_Manipulation_Functions::str_replace_once($our_table_prefix, '', $a);
		$nb = UpdraftPlus_Manipulation_Functions::str_replace_once($our_table_prefix, '', $b);
		if (in_array($na, $core_tables) && !in_array($nb, $core_tables)) return -1;
		if (!in_array($na, $core_tables) && in_array($nb, $core_tables)) return 1;
		return strcmp($a, $b);
	}

	/**
	 * Detect if the table has a composite primary key (composed from multiple columns)
	 *
	 * @param String	  $table	- table to examine
	 * @param Object|Null $wpdb_obj - WPDB-like object (requires the get_results() method), or null to use the global default
	 *
	 * @return Boolean
	 */
	public static function table_has_composite_private_key($table, $wpdb_obj = null) {
	
		$wpdb = (null === $wpdb_obj) ? $GLOBALS['wpdb'] : $wpdb_obj;
	
		$table_structure = $wpdb->get_results("DESCRIBE ".UpdraftPlus_Manipulation_Functions::backquote($table));
		if (!$table_structure) return false;
		
		$primary_key_columns_found = 0;
		
		foreach ($table_structure as $struct) {
			if (isset($struct->Key) && 'PRI' == $struct->Key) {
				$primary_key_columns_found++;
				if ($primary_key_columns_found > 1) return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Set MySQL server system variable
	 *
	 * @param String          $variable  The name of the system variable
	 * @param String          $value     The variable value
	 * @param Resource|Object $db_handle The database link identifier(resource) given by mysqli_init or mysql_connect
	 * @return Boolean Returns true on success, false otherwise
	 */
	public static function set_system_variable($variable, $value, $db_handle) {

		$is_mysqli = is_a($db_handle, 'mysqli');
		if (!is_resource($db_handle) && !$is_mysqli) return false;

		$sql = "SET SESSION %s='%s'";
		if ($is_mysqli) {
			// @codingStandardsIgnoreLine
			$res = @mysqli_query($db_handle, sprintf($sql, mysqli_real_escape_string($db_handle, $variable), mysqli_real_escape_string($db_handle, $value)));
		} else {
			// @codingStandardsIgnoreLine
			$res = @mysql_query(sprintf($sql, mysql_real_escape_string($variable, $db_handle), mysql_real_escape_string($value, $db_handle)), $db_handle);
		}

		return $res;
	}

	/**
	 * Get MySQL server system variable.
	 *
	 * @param String          $variable  The name of the system variable
	 * @param Resource|Object $db_handle The database link identifier(resource) given by mysqli_init or mysql_connect
	 * @return String|Boolean|Null Returns value of the system variable, false on query failure or null if there is no result for the corresponding variable
	 */
	public static function get_system_variable($variable, $db_handle) {

		$is_mysqli = is_a($db_handle, 'mysqli');
		if (!is_resource($db_handle) && !$is_mysqli) return false;

		$sql = 'SELECT @@SESSION.%s';

		if ($is_mysqli) {
			// @codingStandardsIgnoreLine
			$res = @mysqli_query($db_handle, sprintf($sql, mysqli_real_escape_string($db_handle, $variable)));
		} else {
			// @codingStandardsIgnoreLine
			$res = @mysql_query(sprintf($sql, mysql_real_escape_string($variable, $db_handle)), $db_handle);
		}
		if (false === $res) {
			return $res;
		}
		if ($is_mysqli) {
			// @codingStandardsIgnoreLine
			$res = mysqli_fetch_array($res);
			return isset($res[0]) ? $res[0] : null;
		} else {
			// @codingStandardsIgnoreLine
			$res = mysql_result($res, 0);
			return false === $res ? null : $res;
		}
	}

	/**
	 *
	 * This function is adapted from the set_sql_mode() method in WordPress wpdb class but with few modifications applied, this can be used to switch between different sets of SQL modes.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wpdb/set_sql_mode/
	 * @see https://dev.mysql.com/doc/refman/5.6/en/sql-mode.html
	 * @see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html
	 * @see https://dev.mysql.com/doc/refman/8.0/en/sql-mode.html
	 * @see https://dev.mysql.com/doc/refman/5.6/en/server-system-variables.html#sysvar_sql_mode
	 * @see https://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_sql_mode
	 * @see https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html#sysvar_sql_mode
	 * @see https://mariadb.com/kb/en/library/sql-mode/#strict-mode
	 * @see https://mariadb.com/kb/en/library/sql-mode/#setting-sql_mode
	 *
	 * @param Array				   $modes		 - Optional. A list of SQL modes to set.
	 * @param Array				   $remove_modes - modes to remove if they are currently active
	 * @param Resource|Object|NULL $db_handle	 - Optional. If specified, it should either the valid database link identifier(resource) given by mysql(i) or null to instead use the global WPDB object, or a WPDB-compatible object.
	 */
	public static function set_sql_mode($modes = array(), $remove_modes = array(), $db_handle = null) {

		global $updraftplus, $wpdb;
		
		$wpdb_handle_if_used = (null !== $db_handle && is_a($db_handle, 'WPDB')) ? $db_handle : $wpdb;
		
		// If any of these are set, they will be unset
		$strict_modes = array(
			// according to mariadb and mysql docs, strict mode can be one of these or both
			'STRICT_TRANS_TABLES',
			'STRICT_ALL_TABLES',
		);

		$incompatible_modes = array_unique(array_merge(array(
			'NO_ZERO_DATE',
			'ONLY_FULL_GROUP_BY',
			'TRADITIONAL',
		), $strict_modes));

		$class = get_class();

		if (is_null($db_handle) || is_a($db_handle, 'WPDB')) {
			$initial_modes_str = $wpdb_handle_if_used->get_var('SELECT @@SESSION.sql_mode');
		} else {
			$initial_modes_str = call_user_func_array(array($class, 'get_system_variable'), array('sql_mode', $db_handle));
		}
		if (is_scalar($initial_modes_str) && !is_bool($initial_modes_str)) {
			$modes = array_unique(array_merge($modes, array_change_key_case(explode(',', $initial_modes_str), CASE_UPPER)));
		} else {
			$updraftplus->log("Couldn't get the sql_mode value (".serialize($initial_modes_str)."); will not attempt any adjustment");
			return;
		}

		$modes = array_change_key_case($modes, CASE_UPPER);

		$unwanted_modes = array_merge($incompatible_modes, $remove_modes);
		
		foreach ($modes as $i => $mode) {
			if (in_array($mode, $unwanted_modes)) {
				unset($modes[$i]);
			}
		}

		$modes_str = implode(',', $modes);

		if (is_null($db_handle) || is_a($db_handle, 'WPDB')) {
			$res = $wpdb_handle_if_used->query($wpdb_handle_if_used->prepare("SET SESSION sql_mode = %s", $modes_str));
		} else {
			$res = call_user_func_array(array($class, 'set_system_variable'), array('sql_mode', $modes_str, $db_handle));
		}

		if (isset($initial_modes_str) && false == array_diff(explode(',', $initial_modes_str), $modes)) {
			$updraftplus->log("SQL compatibility mode is: $modes_str");
		} else {
			$updraftplus->log("SQL compatibility mode".((false === $res) ? " not" : "")." successfully changed".(isset($initial_modes_str) ? " from $initial_modes_str" : "")." to $modes_str");
		}
	}

	/**
	 * Parse the SQL "create table" column definition (non validating) and check whether it's a generated column and retrieve its column options
	 *
	 * @see https://dev.mysql.com/doc/refman/8.0/en/create-table.html
	 * @see https://mariadb.com/kb/en/create-table/
	 *
	 * @param String  $table_column_definition the column definition statement in which the generated column needs to be identified
	 * @param Integer $starting_offset         the string position of the column definition in a "create table" statement
	 * @return Array|False an array of generated column fragment (column definition, column name, generated column type, etc); false otherwise
	 *
	 * Example input:
	 *
	 *     $column_definition = "fullname varchar(101) GENERATED ALWAYS AS (CONCAT(first_name,' ',last_name)) VIRTUAL NOT NULL COMMENT 'this is the comment',"
	 *
	 * Corresponding result:
	 *
	 *     [
	 *         "column_definition" => "fullname varchar(101) GENERATED ALWAYS AS (CONCAT(first_name,' ',last_name)) VIRTUAL NOT NULL COMMENT 'this is the comment',",
	 *         "column_name" => "fullname",
	 *         "column_data_type_definition" => [
	 *              [
	 *                  "GENERATED ALWAYS AS (CONCAT(first_name,' ',last_name))",
	 *                   90
	 *              ],
	 *              [
	 *                   "VIRTUAL NOT NULL",
	 *                   123 // string position
	 *              ],
	 *              [
	 *                   "COMMENT 'this is the comment'",
	 *                   345 // string position
	 *              ]
	 *         ],
	 *         "is_virtual" => true
	 *     ]
	 */
	public static function get_generated_column_info($table_column_definition, $starting_offset) {

		// check whether or not the column definition ($table_column_definition) is a generated column, if so then get all the column definitions
		// https://regex101.com/r/Fy2Bkd/12
		if (preg_match_all('/^\s*\`((?:[^`]|``)+)\`([^,\'"]+?)(?:((?:GENERATED\s*ALWAYS\s*)?AS\s*\(.+\))([\w\s]*)(COMMENT\s*(?:\'(?:[^\']|\'\')*\'|\"(?:[^"]|"")*\"))([\w\s]*)|((?:GENERATED\s*ALWAYS\s*)?AS\s*\(.+\)([\w\s]*)))/i', $table_column_definition, $column_definitions, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {

			if (empty($column_definitions)) return false;

			/**
			 * If the above preg_match_all function succeed, it returns an array with the following format:
			 *
			 *	Array(3) {
			 *	[0]=> // 1st set of the matched/captured string
			 *	array(6) {
			 *		[0]=> // 1st index represents a full column definition
			 *		array(2) {
			 *			[0]=> string(131) ", `full_name` char(41) GENERATED ALWAYS AS (concat(`firstname`,'()`)(()',`lastname`)) VIRTUAL NOT NULL COMMENT 'fu(ll)'_name'' COLUMN_FORMAT DEFAULT"
			 *			[1]=> int(541)
			 *		}
			 *		[1]=> // 2nd index represents a column name
			 *		array(2) {
			 *			[0]=> string(9) "full_name"
			 *			[1]=> int(547)
			 *		}
			 *		[2]=> // 3rd index represents data type option that is captured before "generated always as"
			 *		array(2) {
			 *			[0]=> string(18) " char(41) "
			 *			[1]=> int(555)
			 *		}
			 *		[3]=> // 4rd index represents data type option which is specific for "generated always as"
			 *		array(2) {
			 *			[0]=> string(18) "GENERATED ALWAYS AS (concat(`firstname`,'()`)(()',`lastname`))"
			 *			[1]=> int(629) // this is the position or starting offset of the captured data type's option, this can later be used to help with the unsupported keyword replacement stuff among db server
			 *		}
			 *		[4]=> // 5th index represents data type option that is captured before COMMENT keyword and after "generated alwasy as"
			 *		array(2) {
			 *			[0]=> string(13) " VIRTUAL NOT NULL " // this is the comment string that could be filled with any word even the reserved keyword (e.g. not null, virtual, stored, etc..)
			 *			[1]=> int(656) // this is the position or starting offset of the captured data type's option, this can later be used to help with the unsupported keyword replacement stuff among db server
			 *		}
			 *		[5]=> // 6th index represents the comment
			 *		array(2) {
			 *			[0]=> string(2) "COMMENT 'fu(ll)'_name''"
			 *			[1]=> int(670) // this is the position or starting offset of the captured comment's string
			 *		}
			 *		[6]=> // 7th index represents data type option that is captured after the COMMENT keyword
			 *		array(2) {
			 *			[0]=> string(2) "COLUMN_FORMAT DEFAULT"
			 *			[1]=> int(670)
			 *		}
			 *	}
			 *	array(8) { // 2nd set
			 *		[0]=>
			 *		array(2) {
			 *			[0]=> string(95) ", `full_name6` char(41) GENERATED ALWAYS AS (concat(`firstname`,' ',`lastname2`))STORED NULL"
			 *			[1]=> int(1121)
			 *		}
			 *		[1]=>
			 *		array(2) {
			 *			[0]=> string(10) "full_name6"
			 *			[1]=> int(1127)
			 *		}
			 *		[2]=>
			 *		array(2) {
			 *			[0]=> string(0) " char(41) "
			 *			[1]=> int(1139)
			 *		}
			 *		[3]=>
			 *		array(2) {
			 *			[0]=> string(0) ""
			 *			[1]=> int(-1)
			 *		}
			 *		[4]=>
			 *		array(2) {
			 *			[0]=> string(0) ""
			 *			[1]=> int(-1)
			 *		}
			 *		[5]=>
			 *		array(2) {
			 *			[0]=> string(0) "" // an empty string of this captured token indicates that the column definition doesn't have COMMENT keyword
			 *			[1]=> int(-1)
			 *		}
			 *		[6]=>
			 *		array(2) {
			 *			[0]=> string(0) ""
			 *			[1]=> int(-1)
			 *		}
			 *		[7]=> // 8th index will appear if there's no COMMENT keyword found in the column definition and it represents data type option that is specific for "generated always as"
			 *		array(2) {
			 *			[0]=> string(11) "GENERATED ALWAYS AS (concat(`firstname`,' ',`lastname2`))"
			 *			[1]=> int(1205)
			 *		}
			 *		[8]=> // 9th index will appear if there's no COMMENT keyword found in the column definition and it represents the captured data type options
			 *		array(2) {
			 *			[0]=> string(11) "STORED NULL"
			 *			[1]=> int(1270)
			 *		}
			 *	}
			 *  }
			 */

			foreach ($column_definitions as $column_definition) {
				$data_type_definition = (!empty($column_definition[4][0]) ? $column_definition[4][0] : '').(!empty($column_definition[6][0]) ? $column_definition[6][0] : '').(!empty($column_definition[8][0]) ? $column_definition[8][0] : '');
				// if no virtual, stored or persistent option is specified then it's virtual by default. It's not possible having two generated columns type in the column definition e.g fullname varchar(101) GENERATED ALWAYS AS (CONCAT(first_name,' ',last_name)) VIRTUAL STORED NOT NULL COMMENT 'comment text', both MySQL and MariaDB will produces an error
				$is_virtual = preg_match('/\bvirtual\b/i', $data_type_definition) || (!preg_match('/\bstored\b/i', $data_type_definition) && !preg_match('/\bpersistent\b/i', $data_type_definition));

				$fragment = array(
					// full syntax of the column definition
					"column_definition" => $column_definition[0][0],
					// the extracted column name
					"column_name" => $column_definition[1][0],
					'column_data_type_definition' => array(),
					"is_virtual" => $is_virtual,
				);
				if (!empty($column_definition[2])) {
					$fragment['column_data_type_definition']['DATA_TYPE_TOKEN'] = $column_definition[2];
					$fragment['column_data_type_definition']['DATA_TYPE_TOKEN'][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition']['DATA_TYPE_TOKEN'][1];
				}
				if (!empty($column_definition[3])) {
					$fragment['column_data_type_definition']['GENERATED_ALWAYS_TOKEN'] = $column_definition[3];
					if (empty($fragment['column_data_type_definition'][1]) && !empty($column_definition[7][0])) $fragment['column_data_type_definition']['GENERATED_ALWAYS_TOKEN'] = $column_definition[7];
					$fragment['column_data_type_definition']['GENERATED_ALWAYS_TOKEN'][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition']['GENERATED_ALWAYS_TOKEN'][1];
				}
				if (!empty($column_definition[4])) {
					$fragment['column_data_type_definition'][2] = $column_definition[4];
					$fragment['column_data_type_definition'][2][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition'][2][1];
				}
				if (!empty($column_definition[5])) {
					$fragment['column_data_type_definition']['COMMENT_TOKEN'] = $column_definition[5];
					$fragment['column_data_type_definition']['COMMENT_TOKEN'][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition']['COMMENT_TOKEN'][1];
				}
				if (!empty($column_definition[6])) {
					$fragment['column_data_type_definition'][4] = $column_definition[6];
					$fragment['column_data_type_definition'][4][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition'][4][1];
				}
				if (!empty($column_definition[8])) {
					$fragment['column_data_type_definition'][5] = $column_definition[8];
					$fragment['column_data_type_definition'][5][1] = (int) $starting_offset + (int) $fragment['column_data_type_definition'][5][1];
				}
			}
		}
		return isset($fragment) ? $fragment : false;
	}

	/**
	 * Retrieve information concerning whether the currently running database server supports generated columns (VIRTUAL, STORED, PERSISTENT)
	 *
	 * @param String $engine Optional. If specified, it should either a well-known database engine like InnoDB, MyISAM, etc or an empty string to instead use database default storage engine; e.g. 'MyISAM'
	 * @return Array|Boolean an array of supported generated column syntax options (whether or not persistent type, not null, virtual index are supported) or false if generated column isn't supported
	 *
	 * The return value is structured thus:
	 *
	 *     [
	 *         // InnoDB supports PERSISTENT generated columns type, whereas MyISAM does not
	 *         "is_persistent_supported" => false,
	 *         // InnoDB supports NOT NULL constraint, whereas MyISAM does not
	 *         "is_not_null_supported" => true,
	 *         // if it's on MariaDB, you can use insert ignore statement to prevent generated columns errors but not on MySQL
	 *         "can_insert_ignore_to_generated_column" => true,
	 *         // No matter what the database engine you use, MySQL doesn't yet support indexing on generated columns
	 *         "is_virtual_index_supported" => false
	 *     ]
	 */
	public static function is_generated_column_supported($engine = '') {

		global $table_prefix, $wpdb;

		$random_table_name = $table_prefix.'updraft_tmp_'.rand(0, 9999999).md5(microtime(true));
		
		$drop_statement = "DROP TABLE IF EXISTS `$random_table_name`;";

		// both mysql and mariadb support generated column, virtual is the default type and the other option type is called stored, mariadb has an alias for stored type which is called persistent, whereas mysql doesn't have such thing.
		// MySQL supports NULL and NOT NULL constraints. On the other hand, MariaDB doesn't support it.
		$sql = array(
			"CREATE TABLE `$random_table_name` (`virtual_column` varchar(17) GENERATED ALWAYS AS ('virtual_column') VIRTUAL COMMENT 'virtual_column')".(!empty($engine) ? " ENGINE=$engine" : "").";",
			"ALTER TABLE `$random_table_name` ADD `persistent_column` VARCHAR(17) AS ('persistent_column') PERSISTENT COMMENT 'generated_column';",
			"ALTER TABLE `$random_table_name` ADD `virtual_column_not_null` VARCHAR(17) AS ('virtual_column_not_null') VIRTUAL NOT NULL COMMENT 'virtual_column_not_null';",
			// check if we can get through this: Error Code: 3105. The value specified for generated column 'generated_column' in table 'wp_generated_column_test' is not allowed.
			// DEFAULT is the only allowed value for virtual and stored type (i.e INSERT IGNORE INTO `wp_generated_column_test` (`virtual_column`) VALUES(DEFAULT)), other than that will produce an error, luckily insert ignore works fine on MariaDB but not on MySQL
			"INSERT IGNORE INTO `$random_table_name` (`virtual_column`) VALUES('virtual_column');",
			// MySQL does not support the create option 'Index on virtual generated column' on MyISAM storage engine
			"CREATE INDEX `idx_wp_udp_generated_column_test_generated_column` ON `$random_table_name` (virtual_column) COMMENT 'virtual_column' ALGORITHM DEFAULT LOCK DEFAULT;",
		);

		$old_val = $wpdb->suppress_errors();
		$wpdb->query($drop_statement);
		$is_generated_column_supported = $wpdb->query($sql[0]);
		if ($is_generated_column_supported) {
			$is_generated_column_supported = array(
				'is_persistent_supported' => $wpdb->query($sql[1]),
				'is_not_null_supported' => $wpdb->query($sql[2]),
				'can_insert_ignore_to_generated_column' => (bool) $wpdb->query($sql[3]),
				'is_virtual_index_supported' => $wpdb->query($sql[4])
			);
		} else {
			$is_generated_column_supported = false;
		}
		$wpdb->query($drop_statement);
		$wpdb->suppress_errors($old_val);
		
		return $is_generated_column_supported;
	}

	/**
	 * Parse the "insert into" statement, capture the column names (if any) and check whether one of the captured columns matches the given list of the "$generated_columns"
	 *
	 * @see https://regex101.com/r/JZiJqH/2
	 *
	 * @param String $insert_statement  the insert statement in which the generated columns will be checked
	 * @param Array  $generated_columns the list of the available "generated columns"
	 * @return Boolean|Null True if "generated columns" exist in the "insert into" statement, false otherwise, null on empty or unmatched insert statement
	 */
	public static function generated_columns_exist_in_the_statement($insert_statement, $generated_columns) {

		$exist = null;
		if (preg_match('/\s*insert.+?into(?:\s*`(?:[^`]|`)+?`|[^\(]+)(?:\s*\((.+?)\))?\s*values.+/i', $insert_statement, $matches)) {
			/**
			 * the reqex above will search for matches of either the insert statement gives data based on the specified column names (i.e INSERT INTO `table_name`(`col1`,'col2`,`virtual_column`,`stored_column`,`col5`) values('1','2','3','4','5')) or not (i.e INSERT INTO `table_name` values('1',',2','3','4','5')), and if the above preg_match function succeed, it returns an array with the following format:
			 *
			 *	Array(2) {
			 *		[0]=> "INSERT INTO `table_name`(`col1`,'col2`,`virtual_column`,`stored_column`,`col5`) values('1','2','3','4','5')"
			*		[1]=> "`col1`,`col2`,`virtual_column`,`col4`,`stored_column`"
			*	}
			*	OR
			*	Array(1) {
			*		[0]=> "INSERT INTO `table_name` values('1','2','3','4','5')"
			*	}
			*/
			$columns = isset($matches[1]) ? preg_split('/\`\s*,\s*\`/', preg_replace('/\`((?:[^\`]|\`)+)\`/', "$1", trim($matches[1]))) : array();
			/**
			*	the preg_replace is used to remove the leading and trailing backtick, so that the string becomes: col1`,`col2`,`virtual_column`,`col4`,`stored_column
			*	the preg_split is used to split all strings that match `,` pattern
			*	Array(5) {
			*		[0]=> string(5) "col1"
			*		[1]=> string(4) "col2"
			*		[2]=> string(14) "virtual_column"
			*		[3]=> string(4) "col4"
			*		[4]=> string(14) "stored_column"
			*	}
			*/
			$exist = (false == $columns) || (true == array_intersect($generated_columns, $columns));
		}
		return $exist;
	}

	/**
	 * Check whether the currently running database server supports stored routines
	 *
	 * @return Array|WP_Error an array of booleans indicating whether or not some of syntax variations are supported, or WP_Error object if stored routine isn't supported
	 *
	 * Return format example:
	 *
	 *     [
	 *         "is_create_or_replace_supported" => true, // true on MariaDB, false on MySQL
	 *         "is_if_not_exists_function_supported" => true, // true on MariaDB, false on MySQL
	 *         "is_aggregate_function_supported" => true, // true on MariaDB, false on MySQL
	 *         "is_binary_logging_enabled" => true, // true if --bin-log is specified for both MariaDB and MySQL
	 *         "is_function_creators_trusted" => false // the default value is false (MariaDB/MySQL)
	 *     ]
	 *
	 *     OR a database error message, e.g. "Access denied for user 'root'@'localhost' to database 'wordpress'"
	 */
	public static function is_stored_routine_supported() {

		global $wpdb;

		$function_name = 'updraft_test_stored_routine';
		$sql = array(
			"DROP_FUNCTION" => "DROP FUNCTION IF EXISTS ".$function_name,
			// sql to check whether stored routines is supported
			"CREATE_FUNCTION" => "CREATE FUNCTION ".$function_name."() RETURNS tinyint(1) DETERMINISTIC READS SQL DATA RETURN true",
			// sql to check whether create or replace syntax is supported
			"CREATE_REPLACE_FUNCTION" => "CREATE OR REPLACE FUNCTION ".$function_name."() RETURNS tinyint(1) DETERMINISTIC READS SQL DATA RETURN true",
			// sql to check whether if not exists syntax is supported (mariadb starting with 10.1.3)
			"CREATE_FUNCTION_IF_NOT_EXISTS" => "CREATE FUNCTION IF NOT EXISTS ".$function_name."() RETURNS tinyint(1) DETERMINISTIC READS SQL DATA RETURN true",
			// sql to check whether aggregate function is supported (mariadb starting with 10.3.3)
			"CREATE_REPLACE_AGGREGATE" => "CREATE OR REPLACE AGGREGATE FUNCTION ".$function_name."() RETURNS tinyint(1) DETERMINISTIC READS SQL DATA BEGIN RETURN true; FETCH GROUP NEXT ROW; END;"
		);

		$old_val = $wpdb->suppress_errors();
		$wpdb->query($sql['DROP_FUNCTION']);
		$is_stored_routine_supported = $wpdb->query($sql['CREATE_FUNCTION']);
		if ($is_stored_routine_supported) {
			$is_binary_logging_enabled = 1 == $wpdb->get_var('SELECT @@GLOBAL.log_bin');
			// not sure why the log_bin variable cant be retrieved on mysql 5.0, seems like there's a bug on that version, so we use another alternative to check whether or not binary logging is enabled
			$is_binary_logging_enabled = false === $is_binary_logging_enabled ? $wpdb->get_results("SHOW GLOBAL VARIABLES LIKE 'log_bin'", ARRAY_A) : $is_binary_logging_enabled;
			$is_binary_logging_enabled = is_array($is_binary_logging_enabled) && isset($is_binary_logging_enabled[0]['Value']) && '' != $is_binary_logging_enabled[0]['Value'] ? $is_binary_logging_enabled[0]['Value'] : $is_binary_logging_enabled;
			$is_binary_logging_enabled = is_string($is_binary_logging_enabled) && ('ON' === strtoupper($is_binary_logging_enabled) || '1' === $is_binary_logging_enabled) ? true : $is_binary_logging_enabled;
			$is_binary_logging_enabled = is_string($is_binary_logging_enabled) && ('OFF' === strtoupper($is_binary_logging_enabled) || '0' === $is_binary_logging_enabled) ? false : $is_binary_logging_enabled;
			$is_stored_routine_supported = array(
				'is_create_or_replace_supported' => $wpdb->query($sql['CREATE_REPLACE_FUNCTION']),
				'is_if_not_exists_function_supported' => $wpdb->query($sql['CREATE_FUNCTION_IF_NOT_EXISTS']),
				'is_aggregate_function_supported' => $wpdb->query($sql['CREATE_REPLACE_AGGREGATE']),
				'is_binary_logging_enabled' => $is_binary_logging_enabled,
				'is_function_creators_trusted' => 1 == $wpdb->get_var('SELECT @@GLOBAL.log_bin_trust_function_creators'),
			);
			$wpdb->query($sql['DROP_FUNCTION']);
		} else {
			$is_stored_routine_supported = new WP_Error('routine_creation_error', sprintf(__('An error occurred while attempting to check the support of stored routines creation (%s %s)', 'updraftplus'), $wpdb->last_error.' -', $sql['CREATE_FUNCTION']));
		}
		$wpdb->suppress_errors($old_val);

		return $is_stored_routine_supported;
	}

	/**
	 * Retrieve all the stored routines (functions and procedures) in the currently running database
	 *
	 * @return Array|WP_Error an array of routine statuses, or an empty array if there is no stored routine in the database, or WP_Error object on failure
	 *
	 * Output example:
	 *
	 *     [
	 *         [
	 *              "Db" => "wordpress",
	 *              "Name" => "_NextVal",
	 *              "Type" => "FUNCTION",
	 *              "Definer" => "root@localhost",
	 *              "Modified" => "2019-11-22 15:11:15",
	 *              "Created" => "2019-11-22 14:20:29",
	 *              "Security_type" => "DEFINER",
	 *              "Comment" => "",
	 *              "Function" => "_NextVal",
	 *              "sql_mode" => "",
	 *              "Create Function" => "
	 *                  CREATE DEFINER=`root`@`localhost` FUNCTION `_NextVal`(vname VARCHAR(30)) RETURNS int(11)
	 *                  BEGIN
	 *                      -- Retrieve and update in single statement
	 *                      UPDATE _sequences
	 *                          SET next = next + 1
	 *                          WHERE name = vname;
	 *                      RETURN (SELECT next FROM _sequences LIMIT 1);
	 *                  END"
	 *         ],
	 *         [
	 *              "Db" => "wordpress",
	 *              "Name" => "CreateSequence",
	 *              "Type" => "Procedure",
	 *              "Definer" => "root@localhost",
	 *              "Modified" => "2019-11-22 15:11:15",
	 *              "Created" => "2019-11-22 14:20:29",
	 *              "Security_type" => "DEFINER",
	 *              "Comment" => "",
	 *              "Procedure" => "CreateSequence",
	 *              "sql_mode" => "",
	 *              "Create Procedure" => "
	 *                   CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateSequence`(name VARCHAR(30), start INT, inc INT)
	 *                   BEGIN
	 *                       -- Create a table to store sequences
	 *                       CREATE TABLE  _sequences (
	 *                           name VARCHAR(70) NOT NULL UNIQUE,
	 *                           next INT NOT NULL,
	 *                           inc INT NOT NULL,
	 *                       );
	 *                       -- Add the new sequence
	 *                       INSERT INTO _sequences VALUES (name, start, inc);
	 *                   END"
	 *         ]
	 *     ]
	 */
	public static function get_stored_routines() {

		global $wpdb;

		$old_val = $wpdb->suppress_errors();
		try {
			$err_msg = __('An error occurred while attempting to retrieve routine status (%s %s)', 'updraftplus');
			$function_status = $wpdb->get_results($wpdb->prepare('SHOW FUNCTION STATUS WHERE DB = %s', DB_NAME), ARRAY_A);
			if (!empty($wpdb->last_error)) throw new Exception(sprintf($err_msg, $wpdb->last_error.' -', $wpdb->last_query), 0);
			$procedure_status = $wpdb->get_results($wpdb->prepare('SHOW PROCEDURE STATUS WHERE DB = %s', DB_NAME), ARRAY_A);
			if (!empty($wpdb->last_error)) throw new Exception(sprintf($err_msg, $wpdb->last_error.' -', $wpdb->last_query), 0);
			$stored_routines = array_merge((array) $function_status, (array) $procedure_status);
			foreach ((array) $stored_routines as $key => $routine) {
				if (empty($routine['Name']) || empty($routine['Type'])) continue;
				$routine_name = $routine['Name'];
				// Since routine name can include backquotes and routine name is typically enclosed with backquotes as well, the backquote escaping for the routine name can be done by adding a leading backquote
				$quoted_escaped_routine_name = UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $routine_name));
				$routine = $wpdb->get_results($wpdb->prepare('SHOW CREATE %1$s %2$s', $routine['Type'], $quoted_escaped_routine_name), ARRAY_A);
				if (!empty($wpdb->last_error)) throw new Exception(sprintf(__('An error occurred while attempting to retrieve the routine SQL/DDL statement (%s %s)', 'updraftplus'), $wpdb->last_error.' -', $wpdb->last_query), 1);
				$stored_routines[$key] = array_merge($stored_routines[$key], $routine ? $routine[0] : array());
			}
		} catch (Exception $ex) {
			$stored_routines = new WP_Error(1 === $ex->getCode() ? 'routine_sql_error' : 'routine_status_error', $ex->getMessage());
		}
		$wpdb->suppress_errors($old_val);

		return $stored_routines;
	}
}

class UpdraftPlus_WPDB_OtherDB_Utility extends wpdb {
	/**
	 * This adjusted bail() does two things: 1) Never dies and 2) logs in the UD log
	 *
	 * @param String $message    a string containing a message
	 * @param String $error_code a string containing an error code
	 * @return Boolean returns false
	 */
	public function bail($message, $error_code = '500') {
		global $updraftplus;
		if ('db_connect_fail' == $error_code) $message = 'Connection failed: check your access details, that the database server is up, and that the network connection is not firewalled.';
		$updraftplus->log("WPDB_OtherDB error: $message ($error_code)");
		// Now do the things that would have been done anyway
		$this->error = class_exists('WP_Error') ? new WP_Error($error_code, $message) : $message;
		return false;
	}
}
