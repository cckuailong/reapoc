<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

class UpdraftPlus_Search_Replace {

	private $known_incomplete_classes = array();
	private $columns = array();
	private $current_row = 0;

	private $use_wpdb = false;
	private $use_mysqli = false;
	private $wpdb_obj = null;
	private $mysql_dbh = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action('updraftplus_restore_db_pre', array($this, 'updraftplus_restore_db_pre'));
	}

	/**
	 * This function is called via the filter updraftplus_restore_db_pre it sets up the search and replace database objects
	 *
	 * @return void
	 */
	public function updraftplus_restore_db_pre() {
		global $wpdb, $updraftplus_restorer;
		
		$this->use_wpdb = $updraftplus_restorer->use_wpdb();
		$this->wpdb_obj = $wpdb;

		$mysql_dbh = false;
		$use_mysqli = false;

		if (!$this->use_wpdb) {
			// We have our own extension which drops lots of the overhead on the query
			$wpdb_obj = $updraftplus_restorer->get_db_object();
			// Was that successful?
			if (!$wpdb_obj->is_mysql || !$wpdb_obj->ready) {
				$this->use_wpdb = true;
			} else {
				$this->wpdb_obj = $wpdb_obj;
				$mysql_dbh = $wpdb_obj->updraftplus_get_database_handle();
				$use_mysqli = $wpdb_obj->updraftplus_use_mysqli();
			}
		}

		$this->mysql_dbh = $mysql_dbh;
		$this->use_mysqli = $use_mysqli;
	}

	/**
	 * The engine
	 *
	 * @param string|array   $search    - a string or array of things to search for
	 * @param string|array   $replace   - a string or array of things to replace the search terms with
	 * @param array          $tables    - an array of tables
	 * @param integer        $page_size - the page size
	 */
	public function icit_srdb_replacer($search, $replace, $tables, $page_size) {

		if (!is_array($tables)) return false;

		global $wpdb, $updraftplus;

		$report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'start' => microtime(true),
			'end' => microtime(true),
			'errors' => array(),
		);

		$page_size = (empty($page_size) || !is_numeric($page_size)) ? 5000 : $page_size;

		foreach ($tables as $table => $stripped_table) {

			$report['tables']++;

			if ($search === $replace) {
				$updraftplus->log("No search/replace required: would-be search and replacement are identical");
				continue;
			}

			$this->columns = array();

			$print_line = __('Search and replacing table:', 'updraftplus').' '.$table;

			$updraftplus->check_db_connection($this->wpdb_obj, true);

			// Get a list of columns in this table
			$fields = $wpdb->get_results('DESCRIBE '.UpdraftPlus_Manipulation_Functions::backquote($table), ARRAY_A);

			$prikey_field = false;
			foreach ($fields as $column) {
				$primary_key = ('PRI' == $column['Key']) ? true : false;
				if ($primary_key) $prikey_field = $column['Field'];
				if ('posts' == $stripped_table && 'guid' == $column['Field']) {
					$updraftplus->log('Skipping search/replace on GUID column in posts table');
					continue;
				}
				$this->columns[$column['Field']] = $primary_key;
			}

			// Count the number of rows we have in the table if large we'll split into blocks, This is a mod from Simon Wheatley

			// InnoDB does not do count(*) quickly. You can use an index for more speed - see: http://www.cloudspace.com/blog/2009/08/06/fast-mysql-innodb-count-really-fast/

			$where = '';
			// Opportunity to use internal knowledge on tables which may be huge
			if ('postmeta' == $stripped_table && ((is_array($search) && strpos($search[0], 'http') === 0) || strpos($search, 'http') === 0)) {
				$where = " WHERE meta_value LIKE '%http%'";
			}

			$count_rows_sql = 'SELECT COUNT(*) FROM '.$table;
			if ($prikey_field) $count_rows_sql .= " USE INDEX (PRIMARY)";
			$count_rows_sql .= $where;

			$row_countr = $wpdb->get_results($count_rows_sql, ARRAY_N);

			// If that failed, try this
			if (false !== $prikey_field && $wpdb->last_error) {
				$row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table USE INDEX ($prikey_field)".$where, ARRAY_N);
				if ($wpdb->last_error) $row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table", ARRAY_N);
			}

			$row_count = $row_countr[0][0];
			$print_line .= ': '.sprintf(__('rows: %d', 'updraftplus'), $row_count);
			$updraftplus->log($print_line, 'notice-restore', 'restoring-table-'.$table);
			$updraftplus->log('Search and replacing table: '.$table.": rows: ".$row_count);

			if (0 == $row_count) continue;

			for ($on_row = 0; $on_row <= $row_count; $on_row = $on_row+$page_size) {

				$this->current_row = 0;

				if ($on_row>0) $updraftplus->log_e("Searching and replacing reached row: %d", $on_row);

				// Grab the contents of the table
				list($data, $page_size) = $this->fetch_sql_result($table, $on_row, $page_size, $where);
				// $sql_line is calculated here only for the purpose of logging errors
				// $where might contain a %, so don't place it inside the main parameter

				$sql_line = sprintf('SELECT * FROM %s LIMIT %d, %d', $table.$where, $on_row, $on_row+$page_size);

				// Our strategy here is to minimise memory usage if possible; to process one row at a time if we can, rather than reading everything into memory
				if ($this->use_wpdb) {

					if ($wpdb->last_error) {
						$report['errors'][] = $this->print_error($sql_line);
					} else {
						foreach ($data as $row) {
							$rowrep = $this->process_row($table, $row, $search, $replace, $stripped_table);
							$report['rows']++;
							$report['updates'] += $rowrep['updates'];
							$report['change'] += $rowrep['change'];
							foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
						}
					}
				} else {
					if (false === $data) {
						$report['errors'][] = $this->print_error($sql_line);
					} elseif (true !== $data && null !== $data) {
						if ($this->use_mysqli) {
							while ($row = mysqli_fetch_array($data)) {
								$rowrep = $this->process_row($table, $row, $search, $replace, $stripped_table);
								$report['rows']++;
								$report['updates'] += $rowrep['updates'];
								$report['change'] += $rowrep['change'];
								foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
							}
							mysqli_free_result($data);
						} else {
							// phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
							while ($row = mysql_fetch_array($data)) {
								$rowrep = $this->process_row($table, $row, $search, $replace, $stripped_table);
								$report['rows']++;
								$report['updates'] += $rowrep['updates'];
								$report['change'] += $rowrep['change'];
								foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
							}
							// phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
							@mysql_free_result($data);
						}
					}
				}

			}

		}

		$report['end'] = microtime(true);

		return $report;
	}

	/**
	 * This function will get data from the passed in table ready to be search and replaced
	 *
	 * @param string  $table     - the table name
	 * @param integer $on_row    - the row to start from
	 * @param integer $page_size - the page size
	 * @param string  $where     - the where condition
	 *
	 * @return array - an array of data or an array with a false value
	 */
	private function fetch_sql_result($table, $on_row, $page_size, $where = '') {

		$sql_line = sprintf('SELECT * FROM %s%s LIMIT %d, %d', $table, $where, $on_row, $page_size);

		global $updraftplus;
		$updraftplus->check_db_connection($this->wpdb_obj, true);

		if ($this->use_wpdb) {
			global $wpdb;
			$data = $wpdb->get_results($sql_line, ARRAY_A);
			if (!$wpdb->last_error) return array($data, $page_size);
		} else {
			if ($this->use_mysqli) {
				$data = mysqli_query($this->mysql_dbh, $sql_line);
			} else {
				// phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
				$data = mysql_query($sql_line, $this->mysql_dbh);
			}
			if (false !== $data) return array($data, $page_size);
		}
		
		if (5000 <= $page_size) return $this->fetch_sql_result($table, $on_row, 2000, $where);
		if (2000 <= $page_size) return $this->fetch_sql_result($table, $on_row, 500, $where);

		// At this point, $page_size should be 500; and that failed
		return array(false, $page_size);

	}

	/**
	 * This function will process a single row from the database calling recursive_unserialize_replace to search and replace the data found in the search and replace arrays
	 *
	 * @param string $table          - the current table we are working on
	 * @param array  $row            - the current row we are working on
	 * @param array  $search         - an array of things to search for
	 * @param array  $replace        - an array of things to replace the search terms with
	 * @param string $stripped_table - the stripped table
	 *
	 * @return array - returns an array report which includes changes made and any errors
	 */
	private function process_row($table, $row, $search, $replace, $stripped_table) {

		global $updraftplus, $wpdb, $updraftplus_restorer;

		$report = array('change' => 0, 'errors' => array(), 'updates' => 0);

		$this->current_row++;
		
		$update_sql = array();
		$where_sql = array();
		$upd = false;

		foreach ($this->columns as $column => $primary_key) {
		
			// Don't search/replace these
			if (('options' == $stripped_table && 'option_value' == $column && !empty($row['option_name']) && 'updraft_remotesites' == $row['option_name']) || ('sitemeta' == $stripped_table && 'meta_value' == $column && !empty($row['meta_key']) && 'updraftplus_options' == $row['meta_key'])) {
				continue;
			}
		
			$edited_data = $data_to_fix = $row[$column];
			$successful = false;

			// We catch errors/exceptions so that they're not fatal. Once saw a fatal ("Cannot access empty property") on "if (is_a($value, '__PHP_Incomplete_Class')) {" (not clear what $value has to be to cause that).
			try {
				// Run a search replace on the data that'll respect the serialisation.
				$edited_data = $this->recursive_unserialize_replace($search, $replace, $data_to_fix);
				$successful = true;
			} catch (Exception $e) {
				$log_message = 'An Exception ('.get_class($e).') occurred during the recursive search/replace. Exception message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$report['errors'][] = $log_message;
				error_log($log_message);
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP exception (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'warning-restore');
				// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'A PHP Fatal error (recoverable, '.get_class($e).') occurred during the recursive search/replace. Exception message: Error message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				$report['errors'][] = $log_message;
				error_log($log_message);
				$updraftplus->log($log_message);
				$updraftplus->log(sprintf(__('A PHP fatal error (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage()), 'warning-restore');
			}

			// Something was changed
			if ($successful && $edited_data != $data_to_fix) {
				$report['change']++;
				$ed = $edited_data;
				$wpdb->escape_by_ref($ed);
				// Undo breakage introduced in WP 4.8.3 core
				if (is_callable(array($wpdb, 'remove_placeholder_escape'))) $ed = $wpdb->remove_placeholder_escape($ed);
				$update_sql[] = UpdraftPlus_Manipulation_Functions::backquote($column) . ' = "' . $ed . '"';
				$upd = true;
			}

			if ($primary_key) {
				$df = $data_to_fix;
				$wpdb->escape_by_ref($df);
				// Undo breakage introduced in WP 4.8.3 core
				if (is_callable(array($wpdb, 'remove_placeholder_escape'))) $df = $wpdb->remove_placeholder_escape($df);
				$where_sql[] = UpdraftPlus_Manipulation_Functions::backquote($column) . ' = "' . $df . '"';
			}
		}

		if ($upd && !empty($where_sql)) {
			$sql = 'UPDATE '.UpdraftPlus_Manipulation_Functions::backquote($table).' SET '.implode(', ', $update_sql).' WHERE '.implode(' AND ', array_filter($where_sql));
			$result = $updraftplus_restorer->sql_exec($sql, 5, '', false);
			if (false === $result || is_wp_error($result)) {
				$last_error = $this->print_error($sql);
				$report['errors'][] = $last_error;
			} else {
				$report['updates']++;
			}

		} elseif ($upd) {
			$report['errors'][] = sprintf('"%s" has no primary key, manual change needed on row %s.', $table, $this->current_row);
			$updraftplus->log(__('Error:', 'updraftplus').' '.sprintf(__('"%s" has no primary key, manual change needed on row %s.', 'updraftplus'), $table, $this->current_row), 'warning-restore');
		}

		return $report;

	}
	
	/**
	 * Inspect incomplete class object and make a note in the restoration log if it is a new class
	 *
	 * @param object $data Object expected to be of __PHP_Incomplete_Class_Name
	 */
	private function unserialize_log_incomplete_class($data) {
		global $updraftplus;
		
		try {
			$patch_object = new ArrayObject($data);
			$class_name = $patch_object['__PHP_Incomplete_Class_Name'];
		} catch (Exception $e) {
			error_log('unserialize_log_incomplete_class: '.$e->getMessage());
			// @codingStandardsIgnoreLine
		} catch (Error $e) {
			error_log('unserialize_log_incomplete_class: '.$e->getMessage());
		}
		
		// Check if this class is known
		// Have to serialize incomplete class to find original class name
		if (!in_array($class_name, $this->known_incomplete_classes)) {
			$this->known_incomplete_classes[] = $class_name;
			$updraftplus->log('Incomplete object detected in database: '.$class_name.'; Search and replace will be skipped for these entries');
		}
	}
	
	/**
	 * Take a serialised array and unserialise it replacing elements as needed and
	 * unserialising any subordinate arrays and performing the replace on those too.
	 * N.B. $from and $to can be arrays - they get passed only to str_replace(), which can take an array
	 *
	 * @param string $from       String we're looking to replace.
	 * @param string $to         What we want it to be replaced with
	 * @param array  $data       Used to pass any subordinate arrays back to in.
	 * @param bool   $serialised Does the array passed via $data need serialising.
	 *
	 * @return array	The original array with all elements replaced as needed.
	 */
	private function recursive_unserialize_replace($from = '', $to = '', $data = '', $serialised = false) {

		global $updraftplus;

		static $error_count = 0;

		// some unserialised data cannot be re-serialised eg. SimpleXMLElements
		try {
			$case_insensitive = false;

			if (is_array($from) && is_array($to)) {
				$case_insensitive = preg_match('#^https?:#i', implode($from)) && preg_match('#^https?:#i', implode($to)) ? true : false;
			} else {
				$case_insensitive = preg_match('#^https?:#i', $from) && preg_match('#^https?:#i', $to) ? true : false;
			}

			// O:8:"DateTime":0:{} : see https://bugs.php.net/bug.php?id=62852
			if (is_serialized($data) && false === strpos($data, 'O:8:"DateTime":0:{}') && false !== ($unserialized = @unserialize($data))) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$data = $this->recursive_unserialize_replace($from, $to, $unserialized, true);
			} elseif (is_array($data)) {
				$_tmp = array();
				foreach ($data as $key => $value) {
					// Check that we aren't attempting search/replace on an incomplete class
					// We assume that if $data is an __PHP_Incomplete_Class, it is extremely likely that the original did not contain the domain
					if (is_a($value, '__PHP_Incomplete_Class')) {
						// Check if this class is known
						$this->unserialize_log_incomplete_class($value);
						
						// return original data
						$_tmp[$key] = $value;
					} else {
						$_tmp[$key] = $this->recursive_unserialize_replace($from, $to, $value, false);
					}
				}

				$data = $_tmp;
				unset($_tmp);
			} elseif (is_object($data)) {
				$_tmp = $data; // new $data_class();
				// Check that we aren't attempting search/replace on an incomplete class
				// We assume that if $data is an __PHP_Incomplete_Class, it is extremely likely that the original did not contain the domain
				if (is_a($data, '__PHP_Incomplete_Class')) {
					// Check if this class is known
					$this->unserialize_log_incomplete_class($data);
				} else {
					$props = get_object_vars($data);
					foreach ($props as $key => $value) {
						$_tmp->$key = $this->recursive_unserialize_replace($from, $to, $value, false);
					}
				}
				$data = $_tmp;
				unset($_tmp);
			} elseif (is_string($data) && (null !== ($_tmp = json_decode($data, true)))) {

				if (is_array($_tmp)) {
					foreach ($_tmp as $key => $value) {
						// Check that we aren't attempting search/replace on an incomplete class
						// We assume that if $data is an __PHP_Incomplete_Class, it is extremely likely that the original did not contain the domain
						if (is_a($value, '__PHP_Incomplete_Class')) {
							// Check if this class is known
							$this->unserialize_log_incomplete_class($value);
							
							// return original data
							$_tmp[$key] = $value;
						} else {
							$_tmp[$key] = $this->recursive_unserialize_replace($from, $to, $value, false);
						}
					}

					$data = json_encode($_tmp);
					unset($_tmp);
				}

			} else {
				if (is_string($data)) {
					if ($case_insensitive) {
						$data = str_ireplace($from, $to, $data);
					} else {
						$data = str_replace($from, $to, $data);
					}
// Below is the wrong approach. In fact, in the problematic case, the resolution is an extra search/replace to undo unnecessary ones
// if (is_string($from)) {
// $data = str_replace($from, $to, $data);
// } else {
// # Array. We only want a maximum of one replacement to take place. This is only an issue in non-default setups, but in those situations, carrying out all the search/replaces can be wrong. This is also why the most specific URL should be done first.
// foreach ($from as $i => $f) {
// $ndata = str_replace($f, $to[$i], $data);
// if ($ndata != $data) {
// $data = $ndata;
// break;
// }
// }
// }
				}
			}

			if ($serialised)
				return serialize($data);

		} catch (Exception $error) {
			if (3 > $error_count) {
				$log_message = 'PHP Fatal Exception error ('.get_class($error).') has occurred during recursive_unserialize_replace. Error Message: '.$error->getMessage().' (Code: '.$error->getCode().', line '.$error->getLine().' in '.$error->getFile().')';
				$updraftplus->log($log_message, 'warning-restore');
				$error_count++;
			}
		}

		return $data;
	}

	/**
	 * This function will get the last database error and log it
	 *
	 * @param string $sql_line - the sql line that caused the error
	 *
	 * @return void
	 */
	public function print_error($sql_line) {
		global $wpdb, $updraftplus;
		if ($this->use_wpdb) {
			$last_error = $wpdb->last_error;
		} else {
			// phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			$last_error = ($this->use_mysqli) ? mysqli_error($this->mysql_dbh) : mysql_error($this->mysql_dbh);
		}
		$updraftplus->log(__('Error:', 'updraftplus')." ".$last_error." - ".__('the database query being run was:', 'updraftplus').' '.$sql_line, 'warning-restore');
		return $last_error;
	}
}
