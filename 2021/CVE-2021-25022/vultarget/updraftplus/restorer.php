<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('Updraft_Restorer_Skin')) require_once(UPDRAFTPLUS_DIR.'/includes/updraft-restorer-skin.php');
if (!class_exists('UpdraftPlus_Search_Replace')) require_once(UPDRAFTPLUS_DIR.'/includes/class-search-replace.php');

class Updraft_Restorer {

	// This just stores the result of is_multisite()
	private $is_multisite;

	// This is just used so far for detecting whether we're on the second run for an entity or not.
	public $been_restored = array();
	
	private $tables_been_dropped = array();

	// Public: it is manipulated by the caller after the caller gets the object
	public $delete = false;

	private $created_by_version = false;

	// This one can be set externally, if the information is available
	public $ud_backup_is_multisite = -1;

	private $ud_backup_set;

	public $ud_foreign;

	// store restored table names
	public $restored_table_names = array();

	public $is_dummy_db_restore = false;

	// The default of false means "use the global $wpdb"
	private $wpdb_obj = false;

	private $line_last_logged = 0;

	private $our_siteurl;

	private $configuration_bundle;

	private $ajax_restore_auth_code;
	
	private $restore_options;
	
	private $restore_this_site = array();
	
	private $restore_this_table = array();

	private $restoring_table = '';

	private $line = 0;

	private $statements_run = 0;
	
	private $use_wpdb = null;
	
	private $import_table_prefix = null;

	private $final_import_table_prefix = null;

	private $disable_atomic_on_current_table = false;

	private $table_engine = '';

	private $table_name = '';
	
	private $continuation_data;

	private $current_index = 0;

	private $current_type = '';

	private $previous_table_name = '';

	private $include_unspecified_tables = false;

	private $tables_to_restore = array();

	private $stored_routine_supported = null;
	
	private $tables_to_skip = array();

	public $search_replace_obj = null;
	
	// Constants for use with the move_backup_in method
	// These can't be arbitrarily changed; there is legacy code doing bitwise operations and numerical comparisons, and possibly legacy code still using the values directly.
	const MOVEIN_OVERWRITE_NO_BACKUP = 0;
	const MOVEIN_MAKE_BACKUP_OF_EXISTING = 1;
	const MOVEIN_DO_NOTHING_IF_EXISTING = 2;
	const MOVEIN_COPY_IN_CONTENTS = 3;
	
	private $wp_upgrader;
	
	public $skin = null;
	
	public $strings = array();
	
	private $generated_columns = array();

	private $supported_generated_column_engines = array();

	private $generated_columns_exist_in_the_statement = array();

	private $printed_new_table_prefix = false;
	
	private $old_table_prefix = null;

	/**
	 * Constructor
	 *
	 * @param WP_Upgrader_Skin|Null	$skin			   - an upgrader skin
	 * @param Array|Null			$backup_set		   - the backup set to restore
	 * @param Boolean				$short_init		   - whether just to do a minimal initialisation
	 * @param Array					$restore_options   - options to guide the restoration
	 * @param Array|Null			$continuation_data - continuation data; the jobdata of the job thus far (but only a few properties are used - including second_loop_entities; $restore_options will have come from there too if relevant, but that is passed in here separately); the 'last_index_*' entries also indicate unzipping progress
	 */
	public function __construct($skin = null, $backup_set = null, $short_init = false, $restore_options = array(), $continuation_data = null) {

		$this->our_siteurl = untrailingslashit(site_url());
		
		$this->continuation_data = $continuation_data;

		$this->setup_database_objects();

		$this->search_replace_obj = new UpdraftPlus_Search_Replace();
		
		if ($short_init) return;
		
		// If updraft_incremental_restore_point is equal to -1 then this is either not a incremental restore or we are going to restore up to the latest increment, so there is no need to prune the backup set of any unwanted backup archives.
		if (isset($restore_options['updraft_incremental_restore_point']) && $restore_options['updraft_incremental_restore_point'] > 0) {
			$restore_point = $restore_options['updraft_incremental_restore_point'];
			foreach ($backup_set['incremental_sets'] as $increment_timestamp => $entities) {
				if ($increment_timestamp > $restore_point) {
					foreach ($entities as $entity => $backups) {
						foreach ($backups as $key => $value) {
							unset($backup_set[$entity][$key]);
						}
					}
				}
			}
		}

		// if updraft_include_more_path is set then, the user has chosen where they want these backup files to be restored as either UD did not know where to restore them or the original location is not found on disk any more
		if (isset($restore_options['updraft_include_more_path'])) {
			if (!isset($backup_set['morefiles_linked_indexes']) || !isset($backup_set['morefiles_more_locations'])) {
				$backup_set['morefiles_more_locations'] = $restore_options['updraft_include_more_path'];
				foreach ($restore_options['updraft_include_more_path'] as $key => $path) {
					$backup_set['morefiles_linked_indexes'][] = $key;
				}
			} else {
				foreach ($restore_options['updraft_include_more_path'] as $key => $path) {
					$backup_set['morefiles_more_locations'][$key] = $path;
				}
			}

			if (isset($restore_options['updraft_include_more_index'])) {
				// unset any backups the user has chosen not to restore
				foreach (array_keys($backup_set['more']) as $key) {
					if (!in_array($key, $restore_options['updraft_include_more_index'])) unset($backup_set['more'][$key]);
				}
			}
		}

		if (isset($restore_options['include_unspecified_tables'])) $this->include_unspecified_tables = $restore_options['include_unspecified_tables'];
		if (isset($restore_options['tables_to_restore'])) $this->tables_to_restore = $restore_options['tables_to_restore'];
		if (isset($restore_options['tables_to_skip'])) $this->tables_to_skip = $restore_options['tables_to_skip'];

		// Restore in the most helpful order
		uksort($backup_set, array('UpdraftPlus_Manipulation_Functions', 'sort_restoration_entities'));
		
		$this->ud_backup_set = $backup_set;
		
		add_filter('updraftplus_logline', array($this, 'updraftplus_logline'), 10, 5);
		
		do_action('updraftplus_restorer_restore_options', $restore_options);
		$this->ud_multisite_selective_restore = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;
		$this->restore_options = $restore_options;
		
		$this->ud_foreign = empty($backup_set['meta_foreign']) ? false : $backup_set['meta_foreign'];
		if (isset($backup_set['is_multisite'])) $this->ud_backup_is_multisite = $backup_set['is_multisite'];
		if (isset($backup_set['created_by_version'])) $this->created_by_version = $backup_set['created_by_version'];

		$this->backup_strings();

		$this->is_multisite = is_multisite();

		require_once(UPDRAFTPLUS_DIR.'/includes/class-database-utility.php');
		
		if (!class_exists('WP_Upgrader')) include_once(ABSPATH.'wp-admin/includes/class-wp-upgrader.php');
		$this->skin = $skin;
		$this->wp_upgrader = new WP_Upgrader($skin);
		$this->wp_upgrader->init();
	}

	/**
	 * This function will check if we are using wpdb, if we are not then it will setup our wpdb-like objects
	 *
	 * @param boolean $reconnect_wpdb - if we should include and create a new instance of wpdb
	 *
	 * @return void
	 */
	private function setup_database_objects($reconnect_wpdb = false) {
		global $wpdb;

		if ($reconnect_wpdb) {
			$wpdb->db_connect(true);
		}

		// Line up a wpdb-like object
		if (!$this->use_wpdb()) {
			// We have our own extension which drops lots of the overhead on the query
			$wpdb_obj = new UpdraftPlus_WPDB(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
			// Was that successful?
			if (!$wpdb_obj->is_mysql || !$wpdb_obj->ready) {
				$this->use_wpdb = true;
			} else {
				$this->wpdb_obj = $wpdb_obj;
				$this->mysql_dbh = $wpdb_obj->updraftplus_get_database_handle();
				$this->use_mysqli = $wpdb_obj->updraftplus_use_mysqli();
			}
		}
	}

	/**
	 * This function will try to restore the database connection, if it succeeds then it returns true otherwise false
	 *
	 * @return boolean - true if the connection is restored, otherwise false
	 */
	private function restore_database_connection() {
		global $updraftplus, $wpdb;

		$wpdb_connected = $updraftplus->check_db_connection($wpdb, false, false, true);

		if (false === $wpdb_connected || -1 === $wpdb_connected) {
			sleep(10);
			$this->setup_database_objects(true);
			return false;
		}

		return true;
	}

	/**
	 * Get the wpdb-like object that we are using, if we are using one
	 *
	 * @return UpdraftPlus_WPDB|Boolean
	 */
	public function get_db_object() {
		return $this->wpdb_obj;
	}

	/**
	 * Restore has been completed - clean some things up
	 *
	 * @param Boolean|WP_Error $successful		- if the restore was successful (true) or not (false or WP_Error). If not, then only a minimum of necessary clean-up things is done.
	 * @param Boolean		   $browser_context - if true, then extra messages will be echo-ed
	 *
	 * @uses UpdraftPlus::log()
	 */
	public function post_restore_clean_up($successful = true, $browser_context = true) {
		
		global $updraftplus, $updraftplus_admin;
		
		$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'cleaning', 'data' => array()));

		if (is_wp_error($successful)) {
			foreach ($successful->get_error_codes() as $code) {
				if ('already_exists' == $code) {
					if ($browser_context) {
						global $updraftplus_admin;
						$updraftplus_admin->print_delete_old_dirs_form(false);
					} else {
						$updraftplus->log(__('Your WordPress install has old directories from its state before you restored/migrated (technical information: these are suffixed with -old).', 'updraftplus'));
					}
				}
				$data = $successful->get_error_data($code);
				if (!empty($data)) {
					$pdata = is_string($data) ? $data : serialize($data);
					$updraftplus->log(__('Error data:', 'updraftplus').' '.$pdata, 'warning-restore');
					if (false !== strpos($pdata, 'PCLZIP_ERR_BAD_FORMAT (-10)')) {
						$url = apply_filters('updraftplus_com_link', 'https://updraftplus.com/faqs/error-message-pclzip_err_bad_format-10-invalid-archive-structure-mean/');
						if ($browser_context) {
							echo '<a href="'.$url.'" target="_blank"><strong>'.__('Follow this link for more information', 'updraftplus').'</strong></a><br>';
						} else {
							$updraftplus->log(__('Follow this link for more information', 'updraftplus').': '.$url);
						}
					}
				}
				
			}
			$successful = false;
		}
		
		// From this point on, $successful is a boolean
		if ($successful) {
			// All done - remove the intermediate marker
			delete_site_option('updraft_restore_in_progress');

			foreach (array('template', 'stylesheet', 'template_root', 'stylesheet_root') as $opt) {
				add_filter('pre_option_'.$opt, array($this, 'option_filter_'.$opt));
			}

			// Clear any cached pages after the restore
			$this->clear_caches();

			// Have seen a case where the current theme in the DB began with a capital, but not on disk - and this breaks migrating from Windows to a case-sensitive system
			$template = get_option('template');
			if (!empty($template) && WP_DEFAULT_THEME != $template && strtolower($template) != $template) {

				$theme_root = get_theme_root($template);

				if (!file_exists("$theme_root/$template/style.css") && file_exists("$theme_root/".strtolower($template)."/style.css")) {
					$updraftplus->log_e("Theme directory (%s) not found, but lower-case version exists; updating database option accordingly", $template);
					update_option('template', strtolower($template));
				}

			}

			if (!function_exists('validate_current_theme')) include_once(ABSPATH.WPINC.'/themes');

			if (!validate_current_theme()) {
				if ($browser_context) echo '<strong>';
				$updraftplus->log_e("The current theme was not found; to prevent this stopping the site from loading, your theme has been reverted to the default theme");
				if ($browser_context) echo '</strong>';
			}

			do_action('updraftplus_restore_completed');
		}

		if ($browser_context) echo '</div>'; // Close the updraft_restore_progress div

		restore_error_handler();
		
	}
	
	/**
	 * Whether or not we must use the global $wpdb object for database queries.
	 * That is to say: we *can* always use it. But we prefer to avoid the overhead since we are potentially doing very many queries.
	 *
	 * This is the getter. We have no use-case for a setter outside of this class, so we just set it directly.
	 *
	 * @return Boolean
	 */
	public function use_wpdb() {
		if (!is_bool($this->use_wpdb)) {
			global $wpdb;
			if (defined('UPDRAFTPLUS_USE_WPDB')) {
				$this->use_wpdb = (bool) UPDRAFTPLUS_USE_WPDB;
			} else {
				$this->use_wpdb = ((!function_exists('mysql_query') && !function_exists('mysqli_query')) || !$wpdb->is_mysql || !$wpdb->ready) ? true : false;
			}
		}
		return $this->use_wpdb;
	}
	
	/**
	 * Get the skin
	 *
	 * @return WP_Upgrader_Skin
	 */
	public function ud_get_skin() {
		return $this->skin;
	}
	
	/**
	 * Ensure that needed files are present locally, and return data for the next step (plus do some internal configuration)
	 *
	 * @param Array $entities_to_restore - as returned by self::get_entities_to_restore()
	 * @param Array $backupable_entities - list of entities that can be backed u
	 * @param Array $services			 - list of services that the backup can be found at
	 *
	 * @uses self::pre_restore_backup() (and some other internal properties)
	 * @uses UpdraftPlus::log()
	 *
	 * @return Boolean|Array|WP_Error - a sorted array (of entity types and files for each entity type) or false or a WP_Error if there was an error
	 */
	private function ensure_restore_files_present($entities_to_restore, $backupable_entities, $services) {
		
		global $updraftplus;
		
		$entities_to_download = $this->get_entities_to_download($entities_to_restore);
		
		$backup_set = $this->ud_backup_set;
		$timestamp = $backup_set['timestamp'];
		$second_loop = array();
		
		$updraft_dir = $updraftplus->backups_dir_location();
		$foreign_known = apply_filters('updraftplus_accept_archivename', array());
		
		// First loop: make sure that files are present + readable; and populate array for second loop
		foreach ($backup_set as $type => $files) {
		
			// All restorable entities must be given explicitly, as we can store other arbitrary data in the history array
			if (!isset($backupable_entities[$type]) && 'db' != $type) continue;
			
			if (isset($backupable_entities[$type]['restorable']) && false == $backupable_entities[$type]['restorable']) continue;

			if (!isset($entities_to_download[$type])) continue;
			
			if ('wpcore' == $type && is_multisite() && 0 === $this->ud_backup_is_multisite) {
				$updraftplus->log('wpcore: '.__('Skipping restoration of WordPress core when importing a single site into a multisite installation. If you had anything necessary in your WordPress directory then you will need to re-add it manually from the zip file.', 'updraftplus'), 'notice-restore');
				// TODO
				// $updraftplus->log_e('Skipping restoration of WordPress core when importing a single site into a multisite installation. If you had anything necessary in your WordPress directory then you will need to re-add it manually from the zip file.');
				continue;
			}

			if (is_string($files)) $files = array($files);

			foreach ($files as $ind => $file) {

				$fullpath = $updraft_dir.'/'.$file;
				$updraftplus->log(sprintf(__("Looking for %s archive: file name: %s", 'updraftplus'), $type, $file), 'notice-restore');

				if (is_array($this->continuation_data) && isset($this->continuation_data['second_loop_entities'][$type]) && !in_array($file, $this->continuation_data['second_loop_entities'][$type])) {
					$updraftplus->log(__('Skipping: this archive was already restored.', 'updraftplus'), 'notice-restore');
					// Set the marker so that the existing directory isn't moved out of the way
					$this->been_restored[$type] = true;
					continue;
				}

				if (!is_readable($fullpath) || 0 == filesize($fullpath)) UpdraftPlus_Storage_Methods_Interface::get_remote_file($services, $file, $timestamp, true);

				$index = (0 == $ind) ? '' : $ind;
				// If a file size is stored in the backup data, then verify correctness of the local file
				if (isset($backup_set[$type.$index.'-size'])) {
					$fs = $backup_set[$type.$index.'-size'];
					$print_message = __("Archive is expected to be size:", 'updraftplus')." ".round($fs/1024, 1)." KB: ";
					$as = @filesize($fullpath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					if ($as == $fs) {
						$updraftplus->log($print_message.__('OK', 'updraftplus'), 'notice-restore');
					} else {
						$updraftplus->log($print_message.__('Error:', 'updraftplus')." ".__('file is size:', 'updraftplus')." ".round($as/1024)." ($fs, $as)", 'warning-restore');
					}
				} else {
					$updraftplus->log(__("The backup records do not contain information about the proper size of this file.", 'updraftplus'), 'notice-restore');
				}
				if (!is_readable($fullpath)) {
					$updraftplus->log(__('Could not read one of the files for restoration', 'updraftplus')." ($file)", 'warning-restore');
					$updraftplus->log("$file: ".__('Could not read one of the files for restoration', 'updraftplus'), 'error');
					return false;
				}
			}

			if (empty($this->ud_foreign)) {
				$types = array($type);
			} else {
				if ('db' != $type || empty($foreign_known[$this->ud_foreign]['separatedb'])) {
					$types = array('wpcore');
				} else {
					$types = array('db');
				}
			}

			foreach ($types as $check_type) {
				$info = isset($backupable_entities[$check_type]) ? $backupable_entities[$check_type] : array();
				$val = $this->pre_restore_backup($files, $check_type, $info);
				if (is_wp_error($val)) {
					$updraftplus->log_wp_error($val);
					foreach ($val->get_error_messages() as $msg) {
						$updraftplus->log(__('Error:',  'updraftplus').' '.$msg, 'warning-restore');
					}
					return $val;
				} elseif (false === $val) {
					return false;
				}
			}

			foreach ($entities_to_restore as $entity => $via) {
				if ($via == $type) {
					if ('wpcore' == $via && 'db' == $entity && count($files) > 1) {
						$second_loop[$entity] = apply_filters('updraftplus_select_wpcore_file_with_db', $files, $this->ud_foreign);
					} else {
						$second_loop[$entity] = $files;
					}
				}
			}
		
		}

		$this->delete = UpdraftPlus_Options::get_updraft_option('updraft_delete_local', 1) ? true : false;
		if (empty($services) || array('email') === $services || !empty($this->ud_foreign)) {
			if ($this->delete) $updraftplus->log_e('Will not delete any archives after unpacking them, because there was no cloud storage for this backup');
			$this->delete = false;
		}

		if (!empty($this->ud_foreign)) $updraftplus->log("Foreign backup; created by: ".$this->ud_foreign);

		// Second loop: now actually do the restoration
		uksort($second_loop, array('UpdraftPlus_Manipulation_Functions', 'sort_restoration_entities'));

		// If continuing, then prune those already done
		if (is_array($this->continuation_data) && isset($this->continuation_data['second_loop_entities'])) {
			foreach ($second_loop as $type => $files) {
				if (isset($this->continuation_data['second_loop_entities'][$type])) {
					$second_loop[$type] = $this->continuation_data['second_loop_entities'][$type];
				} else {
					$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'files', 'data' => array('entity' => $type, 'index' => 0, 'file' => '', 'fileindex' => 0, 'size_written' => 0, 'total_files' => 0)));
					unset($second_loop[$type]);
				}
			}
		}
		
		return $second_loop;
	}
	
	/**
	 * Perform the restoration. No code here (or called) should assume anything about the method used to call it (e.g. wp-admin or WP-CLI); it should be independent of how it is being called.
	 *
	 * The path through this class is perform_restore() -> restore_backup() -> unpack_package() -> unpack_package_(archive|database) and then (for standard UD archives) UpdraftPlus_Filesystem_Functions::unzip_file()
	 *
	 * @param Array $entities_to_restore - entities to restore
	 * @param Array $restore_options	 - restoration options
	 *
	 * @uses the WordPress action updraftplus_restoration_title, allowing the title to be printed
	 *
	 * @return Boolean
	 */
	public function perform_restore($entities_to_restore, $restore_options) {
		global $updraftplus;
		
		$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'verifying', 'data' => implode(', ', array_flip($entities_to_restore))));

		// Now log. We first remove any encryption passphrase from the log data.
		$copy_restore_options = $restore_options;
		if (!empty($copy_restore_options['updraft_encryptionphrase'])) $copy_restore_options['updraft_encryptionphrase'] = '***';
		$updraftplus->log("Restore job started. Entities to restore: ".implode(', ', array_flip($entities_to_restore)).'. Restore options: '.json_encode($copy_restore_options));
		
		do_action('updraftplus_restoration_title', __('Final checks', 'updraftplus'));
		add_action('updraftplus_unzip_progress_restore_info', array($this, 'unzip_progress_restore_info'), 10, 4);
		$backup_set = $this->ud_backup_set;
		
		$services = isset($backup_set['service']) ? $updraftplus->get_canonical_service_list($backup_set['service']) : array();
		
		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);
		
		$remove_zip = isset($restore_options['delete_during_restore']) ? $restore_options['delete_during_restore'] : false;

		if (!empty($restore_options['dummy_db_restore'])) {
			$this->is_dummy_db_restore = true;
			add_filter('updraftplus_restore_table_prefix', array($this, 'updraftplus_random_restore_table_prefix'));
		}

		// Allow add-ons to adjust the restore directory (but only in the case of restore - otherwise, they could just use the filter built into UpdraftPlus::get_backupable_file_entities)
		$backupable_entities = apply_filters('updraft_backupable_file_entities_on_restore', $backupable_entities, $restore_options, $backup_set);

		if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
		// Get an ordered list of things to restore
		// This requires the global $updraft_restorer to be set up
		$second_loop = $this->ensure_restore_files_present($entities_to_restore, $backupable_entities, $services);
		
		if (!is_array($second_loop)) return $second_loop;
		
		$timestamp = $backup_set['timestamp'];
		
		$updraftplus->jobdata_set('second_loop_entities', $second_loop);
		$updraftplus->jobdata_set('backup_timestamp', $timestamp);
		
		// Use a site option, as otherwise on multisite when all the array of options is updated via UpdraftPlus_Options::update_site_option(), it will over-write any restored UD options from the backup
		update_site_option('updraft_restore_in_progress', $updraftplus->nonce);

		// Now process the actual restoration of the entities
		foreach ($second_loop as $type => $files) {

			$this->current_type = $type;

			// Types: uploads, themes, plugins, others, db
			$info = isset($backupable_entities[$type]) ? $backupable_entities[$type] : array();

			$restoration_title = ('db' == $type) ? __('Database', 'updraftplus') : $info['description'];

			// Indicate the type changed
			$updraftplus->log_restore_update(array('type' => 'state_change', 'stage' => $type, 'data' => array()));

			do_action('updraftplus_restoration_title', $restoration_title);
			
			$updraftplus->log('Entity: '.$type);

			if (is_string($files)) $files = array($files);
			
			// Don't assume that the caller pre-sorted the array. We do need it sorted, so that incremental zips get restored in the right order
			ksort($files);
			
			foreach ($files as $fkey => $file) {
				$this->current_index = $fkey;
				$last_one = (1 == count($second_loop) && 1 == count($files));
				$last_entity = (1 == count($files));
				try {
					// Returns a boolean or WP_Error
					$restore_result = $this->restore_backup($file, $type, $info, $last_one, $last_entity);
				} catch (Exception $e) {
					$log_message = 'Exception ('.get_class($e).') occurred during restore: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
					$display_log_message = sprintf(__('A PHP exception (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage());
					error_log($log_message);
					// @codingStandardsIgnoreLine
					if (function_exists('wp_debug_backtrace_summary')) $log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
					$updraftplus->log($display_log_message, 'notice-restore');
					die();
				// @codingStandardsIgnoreLine
				} catch (Error $e) {
					$log_message = 'PHP Fatal error ('.get_class($e).') has occurred. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
					error_log($log_message);
					// @codingStandardsIgnoreLine
					if (function_exists('wp_debug_backtrace_summary')) $log_message .= ' Backtrace: '.str_replace(array(ABSPATH, "\n"), array('', ', '), $e->getTraceAsString());
					$display_log_message = sprintf(__('A PHP fatal error (%s) has occurred: %s', 'updraftplus'), get_class($e), $e->getMessage());
					$updraftplus->log($display_log_message, 'notice-restore');
					die();
				}
				
				if (is_wp_error($restore_result)) {
					$codes = $restore_result->get_error_codes();
					if (is_array($codes) && in_array('not_found', $codes) && !empty($this->ud_foreign) && apply_filters('updraftplus_foreign_allow_missing_entity', false, $type, $this->ud_foreign)) {
						$updraftplus->log('Entity to move not found in this zip - but this is possible with this foreign backup type');
					} else {
						$updraftplus->log_e($restore_result);
						foreach ($restore_result->get_error_messages() as $msg) {
							$updraftplus->log(__('Error message',  'updraftplus').': '.$msg, 'notice-restore');
						}
						return $restore_result;
					}
				} elseif (false === $restore_result) {
					return false;
				} elseif ($restore_result && $remove_zip) {
					$deleted = unlink($updraftplus->backups_dir_location().'/'.$file);
					$updraftplus->log("Delete zip during restore active; removing backup file: $file: ".($deleted ? 'OK' : 'Failed'));
				}
				
				unset($files[$fkey]);
				$second_loop[$type] = $files;
				$updraftplus->jobdata_set_multi(array('second_loop_entities' => $second_loop, 'backup_timestamp' => $timestamp));

				do_action('updraft_restored_archive', $file, $type, $restore_result, $fkey, $timestamp);

			}
			
			// Update the job data each time we go round the loop, so that if it aborts, it can be resumed from the correct point
			unset($second_loop[$type]);
			update_site_option('updraft_restore_in_progress', $updraftplus->nonce);
			$updraftplus->jobdata_set_multi(array('second_loop_entities' => $second_loop, 'backup_timestamp' => $timestamp));
		}
		
		// If the database was restored, then check active plugins and make sure they all exist; otherwise, the site may go down
		if (null !== $this->final_import_table_prefix) $this->check_active_plugins($this->final_import_table_prefix);

		return true;
	}
	
	/**
	 * Calculate the entities to download for a given backup set
	 *
	 * @param Array $entities_to_restore - entities to restore, in the format returned by UpdraftPlus_Admin::get_entities_to_restore
	 *
	 * @return Array - keys are entities, and values are 0|1
	 */
	public function get_entities_to_download($entities_to_restore) {
	
		$backup_set = $this->ud_backup_set;
	
		$foreign_known = apply_filters('updraftplus_accept_archivename', array());
	
		if (empty($backup_set['meta_foreign'])) return $entities_to_restore;
		
		if (empty($foreign_known[$backup_set['meta_foreign']]['separatedb'])) return array('wpcore' => 1);
		
		$entities_to_download = array();
		
		if (in_array('db', $entities_to_restore)) $entities_to_download['db'] = 1;

		if (count($entities_to_restore) > 1 || !in_array('db', $entities_to_restore)) {
			$entities_to_download['wpcore'] = 1;
		}

		return $entities_to_download;
	}
	
	/**
	 * Logs a line from the restore process, being called from UpdraftPlus::log(). Currently, this means adding it to the browser output log file and either (depending on the constant WP_CLI) echoing it or passing it to a WPCLI method.
	 * Hooks the WordPress filter updraftplus_logline
	 * In future, this can get more sophisticated. For now, things are funnelled through here, giving the future possibility.
	 *
	 * @param String 		 $line 		  the line to be logged
	 * @param String         $nonce 	  the job ID of the restore job
	 * @param String         $level 	  the level of the log notice
	 * @param String|Boolean $uniq_id     a unique ID for the log if it should only be logged once; or false otherwise
	 * @param String 		 $destination the type of job ongoing. If it is not 'restore', then we will skip the logging.
	 * @return The filtered value. If set to false, then UpdraftPlus::log() will stop processing the log line.
	 */
	public function updraftplus_logline($line, $nonce, $level, $uniq_id, $destination) {
		if ('restore' != $destination) return $line;
		
		global $updraftplus;
		static $logfile_handle;
		
		if (empty($logfile_handle)) {
			$logfile_name = $updraftplus->backups_dir_location()."/log.$nonce-browser.txt";
			$logfile_handle = fopen($logfile_name, 'a');
		}
		
		if (!empty($logfile_handle)) {
			$rtime = microtime(true)-$updraftplus->job_time_ms;
			fwrite($logfile_handle, sprintf("%08.03f", round($rtime, 3))." (R) ".'['.$level.'] '.$line."\n");
		}
		if (defined('WP_CLI') && WP_CLI) {
			switch ($level) {
				case 'error':
				case 'warning':
					// WP_CLI::error() displays message with the prefix "Error: ", We don't like message which are double prefixed like the "Error: Error: ".
					if (0 === stripos($line, 'Error: ')) {
						$log_line = substr($line, 7);
					} else {
						$log_line = $line;
					}
					WP_CLI::error($log_line, false);
					break;
				case 'notice':
				default:
					WP_CLI::log($line, false);
					break;
			}
		} else {
			if ('warning' == $level || 'error' == $level || $uniq_id) {
				$line = '<strong>'.htmlspecialchars($line).'</strong>';
			} else {
				$line = htmlspecialchars($line);
			}
	
			$updraftplus->output_to_browser($line.'<br>');
		}
		return false;
	}
	
	private function backup_strings() {
		$this->strings['not_possible'] = __('UpdraftPlus is not able to directly restore this kind of entity. It must be restored manually.', 'updraftplus');
		$this->strings['no_package'] = __('Backup file not available.', 'updraftplus');
		$this->strings['copy_failed'] = __('Copying this entity failed.', 'updraftplus');
		$this->strings['unpack_package'] = __('Unpacking backup...', 'updraftplus');
		$this->strings['decrypt_database'] = __('Decrypting database (can take a while)...', 'updraftplus');
		$this->strings['decrypted_database'] = __('Database successfully decrypted.', 'updraftplus');
		$this->strings['moving_old'] = __('Moving old data out of the way...', 'updraftplus');
		$this->strings['moving_backup'] = __('Moving unpacked backup into place...', 'updraftplus');
		$this->strings['restore_database'] = __('Restoring the database (on a large site this can take a long time - if it times out (which can happen if your web hosting company has configured your hosting to limit resources) then you should use a different method, such as phpMyAdmin)...', 'updraftplus');
		$this->strings['cleaning_up'] = __('Cleaning up rubbish...', 'updraftplus');
		$this->strings['old_move_failed'] = __('Could not move old files out of the way.', 'updraftplus').' '.__('You should check the file ownerships and permissions in your WordPress installation', 'updraftplus');
		$this->strings['old_delete_failed'] = __('Could not delete old path.', 'updraftplus');
		$this->strings['new_move_failed'] = __('Could not move new files into place. Check your wp-content/upgrade folder.', 'updraftplus');
		$this->strings['move_failed'] = __('Could not move the files into place. Check your file permissions.', 'updraftplus');
		$this->strings['delete_failed'] = __('Failed to delete working directory after restoring.', 'updraftplus');
		$this->strings['multisite_error'] = __('You are running on WordPress multisite - but your backup is not of a multisite site.', 'updraftplus');
		$this->strings['unpack_failed'] = __('Failed to unpack the archive', 'updraftplus');
		$this->strings['read_manifest_failed'] = __('Failed to read the manifest file from backup.', 'updraftplus');
		$this->strings['manifest_not_found'] = __('Failed to find a manifest file in the backup.', 'updraftplus');
		$this->strings['read_working_dir_failed'] = __('Failed to read from the working directory.', 'updraftplus');
	}

	/**
	 * This function will build the unzip progress restore info array ready to be output to the js
	 *
	 * @param string  $filepath     - the current file we are working on
	 * @param integer $fileindex    - how far into the zip we got
	 * @param Integer $size_written - net total number of bytes thus far
	 * @param Integer $num_files    - the total number of files (i.e. one more than the the maximum value of $fileindex)
	 *
	 * @return void
	 */
	public function unzip_progress_restore_info($filepath, $fileindex, $size_written, $num_files) {

		global $updraftplus;

		$index = $this->current_index;
		$file_type = $this->current_type;

		$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'files', 'data' => array('entity' => $file_type, 'index' => $index, 'file' => basename($filepath), 'fileindex' => $fileindex, 'size_written' => $size_written, 'total_files' => $num_files)));
	}

	/**
	 * This function is copied from class WP_Upgrader (WP 3.8 - no significant changes since 3.2 at least); we only had to fork it because it hard-codes using the basename of the zip file as its unpack directory; which can be long; and then combining that with long pathnames in the zip being unpacked can overflow a 256-character path limit (yes, they apparently still exist - amazing!)
	 * Subsequently, we have also added the ability to unpack tarballs
	 *
	 * In the 'ordinary' case of unzipping a UD zip backup, this method basically does some preparation, and then calls UpdraftPlus_Filesystem_Functions::unzip_file() for the actual unzipping
	 *
	 * @used-by self::unpack_package()
	 *
	 * @param  String		  $package        specify package - full filepath
	 * @param  Boolean		  $delete_package check to delete package
	 * @param  String|Boolean $type           type of archive e.g. db.
	 *
	 * @return String|WP_Error If successful, then this indicates the working directory that the archive was unpacked in; a WP_Filesystem path
	 */
	private function unpack_package_archive($package, $delete_package = true, $type = false) {

		global $wp_filesystem, $updraftplus;
		
		// If it is a non-UD archive that is already unpacked, then don't re-run, but return the existing result
		if (!empty($this->ud_foreign) && !empty($this->ud_foreign_working_dir) && $package == $this->ud_foreign_package) {
			if (is_dir($this->ud_foreign_working_dir)) {
				return $this->ud_foreign_working_dir;
			} else {
				$updraftplus->log('Previously unpacked directory seems to have disappeared; will unpack again');
			}
		}

		$this->skin->feedback($this->strings['unpack_package'].' ('.basename($package).', '.round(filesize($package)/1048576, 1).' MB)');

		$upgrade_folder = $wp_filesystem->wp_content_dir().'upgrade/';

		$zip_starting_index = 0;
		
		// We need a working directory. This has a change from the WP core version - minimise path length
		// N.B. It is deterministic; the same package file will get the same working directory
		// $working_dir = $upgrade_folder . basename($package, '.zip');
		$working_dir = $upgrade_folder.substr(md5($package), 0, 8);
		
		if ('.zip' == strtolower(substr($package, -4, 4))) {
		
			$last_index_key = UpdraftPlus_Filesystem_Functions::get_jobdata_progress_key($package);
			
			// Turn off the feature with define('UPDRAFTPLUS_UNZIP_RESUME_ENABLED', false);
			if ((!defined('UPDRAFTPLUS_UNZIP_RESUME_ENABLED') || UPDRAFTPLUS_UNZIP_RESUME_ENABLED) && !empty($this->continuation_data[$last_index_key]) && !empty($this->continuation_data[$last_index_key]['info']['name'])) {
				
				$reached = $this->continuation_data[$last_index_key];
				
				$last_exists = $wp_filesystem->exists($working_dir.'/'.$reached['info']['name']);
				$last_size = $last_exists ? $wp_filesystem->size($working_dir.'/'.$reached['info']['name']) : 'n/a';
				
				if ($last_exists && $last_size == $reached['info']['size'] && isset($reached['index'])) $zip_starting_index = $reached['index'];
				
				$updraftplus->log("Unpack resumption may be possible: zip_starting_index=$zip_starting_index, last_exists=$last_exists, last_size=$last_size, last_status=".serialize($this->continuation_data[$last_index_key]));
			}
			
		}

		if (0 == $zip_starting_index) {
			// Clean up contents of upgrade directory beforehand.
			$upgrade_files = $wp_filesystem->dirlist($upgrade_folder);
			if (!empty($upgrade_files)) {
				foreach ($upgrade_files as $file) {
					if (!$wp_filesystem->delete($upgrade_folder . $file['name'], true)) {
						$this->restore_log_permission_failure_message($upgrade_folder, 'Delete '.$upgrade_folder.$file['name']);
					}
				}
			}

			// Clean up working directory - this is redundant, as we already cleared out the parent folder
			if ($wp_filesystem->is_dir($working_dir)) {
				if (!$wp_filesystem->delete($working_dir, true)) {
					$this->restore_log_permission_failure_message(dirname($working_dir), 'Delete '.$working_dir);
				}
			}
		}

		// Unzip package to working directory
		if ('.zip' == strtolower(substr($package, -4, 4))) {
		
			$result = UpdraftPlus_Filesystem_Functions::unzip_file($package, $working_dir, $zip_starting_index);
			
		} elseif ('.tar' == strtolower(substr($package, -4, 4)) || '.tar.gz' == strtolower(substr($package, -7, 7)) || '.tar.bz2' == strtolower(substr($package, -8, 8))) {
			if (!class_exists('UpdraftPlus_Archive_Tar')) {
				if (false === strpos(get_include_path(), UPDRAFTPLUS_DIR.'/includes/PEAR')) set_include_path(UPDRAFTPLUS_DIR.'/includes/PEAR'.PATH_SEPARATOR.get_include_path());
				include_once(UPDRAFTPLUS_DIR.'/includes/PEAR/Archive/Tar.php');
			}

			$p_compress = null;
			if ('.tar.gz' == strtolower(substr($package, -7, 7))) {
				$p_compress = 'gz';
			} elseif ('.tar.bz2' == strtolower(substr($package, -8, 8))) {
				$p_compress = 'bz2';
			}

			// It's not pretty, but it works.
			if (is_a($wp_filesystem, 'WP_Filesystem_Direct')) {
				$extract_dir = $working_dir;
			} else {
				$updraft_dir = $updraftplus->backups_dir_location();
				if (!UpdraftPlus_Filesystem_Functions::really_is_writable($updraft_dir)) {
					$updraftplus->log_e("Backup directory (%s) is not writable, or does not exist.", $updraft_dir);
					$result = new WP_Error('unpack_failed', $this->strings['unpack_failed']);
				} else {
					$extract_dir = $updraft_dir.'/'.basename($working_dir).'-old';
					if (file_exists($extract_dir)) UpdraftPlus_Filesystem_Functions::remove_local_directory($extract_dir);
					$updraftplus->log("Using a temporary folder to extract before moving over WPFS: $extract_dir");
				}
			}

			// Slightly hackish - rather than re-write Archive_Tar to use wp_filesystem, we instead unpack into the location that we already require to be directly writable for other reasons, and then move from there.
		
			if (empty($result)) {
				
				$this->ud_extract_count = 0;
				$this->ud_working_dir = trailingslashit($working_dir);
				$this->ud_extract_dir = untrailingslashit($extract_dir);
				$this->ud_made_dirs = array();
				add_filter('updraftplus_tar_wrote', array($this, 'tar_wrote'), 10, 2);
				$tar = new UpdraftPlus_Archive_Tar($package, $p_compress);
				$result = $tar->extract($extract_dir, false);
				if (!is_a($wp_filesystem, 'WP_Filesystem_Direct')) UpdraftPlus_Filesystem_Functions::remove_local_directory($extract_dir);
				if (true != $result) {
					$result = new WP_Error('unpack_failed', $this->strings['unpack_failed'], $result);
				} else {
					if (!is_a($wp_filesystem, 'WP_Filesystem_Direct')) {
						$updraftplus->log('Moved unpacked tarball contents');
					}
				}
				remove_filter('updraftplus_tar_wrote', array($this, 'tar_wrote'), 10, 2);
			}
		}

		// Once extracted, delete the package if required.
		if ($delete_package) unlink($package);

		if (is_wp_error($result)) {
			$wp_filesystem->delete($working_dir, true);
			if ('incompatible_archive' == $result->get_error_code()) {
				return new WP_Error('incompatible_archive', $this->wp_upgrader->strings['incompatible_archive'], $result->get_error_data());
			}
			return $result;
		}

		if (!empty($this->ud_foreign)) {
			$this->ud_foreign_working_dir = $working_dir;
			$this->ud_foreign_package = $package;
			// Zip containing an SQL file. We try a default pattern.
			if ('db' === $type) {
				$basepack = basename($package, '.zip');
				if ($wp_filesystem->exists($working_dir.'/'.$basepack.'.sql')) {
					if (!$wp_filesystem->move($working_dir.'/'.$basepack.'.sql', $working_dir . "/backup.db", true)) {
						$this->restore_log_permission_failure_message($working_dir, 'Move '. $working_dir.'/'.$basepack.'.sql'.' -> '.$working_dir . "/backup.db", 'Destination');
					}
					$updraftplus->log("Moving database file $basepack.sql to backup.db");
				}
			}
		}

		return $working_dir;
	}

	public function tar_wrote($result, $file) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		if (0 !== strpos($file, $this->ud_extract_dir)) return false;
		global $wp_filesystem, $updraftplus;
		if (!is_a($wp_filesystem, 'WP_Filesystem_Direct')) {
			$modint = 100;
			$leaf = substr($file, strlen($this->ud_extract_dir));
			$dirname = dirname($leaf);
			$need_dirs = explode('/', $dirname);
			if (empty($this->ud_made_dirs[$dirname])) {
				$cdir = '';
				foreach ($need_dirs as $ndir) {
					$cdir .= ($cdir) ? '/'.$ndir : $ndir;
					if (empty($this->ud_made_dirs[$cdir])) {
						if (!$wp_filesystem->mkdir($this->ud_working_dir.$cdir, FS_CHMOD_DIR) && !$wp_filesystem->is_dir($this->ud_working_dir.$cdir)) {
							$updraftplus->log("Failed to create WPFS directory: ".$this->ud_working_dir.$cdir);
							return false;
						} else {
							$this->ud_made_dirs[$cdir] = true;
						}
					}
				}
			}
			$put = $wp_filesystem->put_contents($this->ud_working_dir.$leaf, file_get_contents($file));
			if (is_wp_error($put)) $updraftplus->log_wp_error($put);
			@unlink($file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {
			$modint = 500;
			$put = true;
		}
		if ($put) {
			$this->ud_extract_count++;
			if (0 == $this->ud_extract_count % $modint) {
				$updraftplus->log_e("%s files have been extracted", $this->ud_extract_count);
			}
		}
		return (true == $put);
	}

	// This returns a wp_filesystem location (and we musn't change that, as we must retain compatibility with the class parent)
	
	/**
	 * This returns a wp_filesystem location (and we musn't change that, as we must retain compatibility with the class parent)
	 * along with unpacking the encrypted db file and checking its contents before going off and restoring the Db
	 *
	 * @param  string  $package        The file name of the encrypted File
	 * @param  boolean $delete_package the file can be removed before going off to the restore stage (this is just incase the user dont want to proceed)
	 * @param  boolean $type 		   Check if the type is true or false
	 * @return string                  Returns success or Fail depending on errors and restors DB
	 */
	public function unpack_package($package, $delete_package = true, $type = false) {

		if (preg_match('/-db(\.gz(\.crypt)?)?$/i', $package) || preg_match('/\.sql(\.gz|\.bz2)?$/i', $package)) {
			return $this->unpack_package_database($package, $delete_package);
		} else {
			global $updraftplus;
			// If not database, then it is a zip - unpack in the usual way
			return $this->unpack_package_archive($updraftplus->backups_dir_location().'/'.$package, $delete_package, $type);
		}
		
	}
	
	/**
	 * Unpack a database backup file
	 *
	 * @used-by self::unpack_package()
	 *
	 * @param  String  $package        - file to unpack; relative filepath
	 * @param  Boolean $delete_package - check to delete package
	 *
	 * @return String|WP_Error If successful, then this indicates the working directory that the archive was unpacked in (a WP_Filesystem path)
	 */
	private function unpack_package_database($package, $delete_package = true) {
		
		global $wp_filesystem, $updraftplus;
		
		$updraft_dir = $updraftplus->backups_dir_location();
		
		// The general shape of the following comes from class-wp-upgrader.php

		$backup_dir = $wp_filesystem->find_folder($updraft_dir);

		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$packsize = round(filesize($backup_dir.$package)/1048576, 1).' Mb';
		
		$this->skin->feedback($this->strings['unpack_package'].' ('.basename($package).', '.$packsize.')');

		$upgrade_folder = $wp_filesystem->wp_content_dir() . 'upgrade/';
		@$wp_filesystem->mkdir($upgrade_folder, octdec($this->calculate_additive_chmod_oct(FS_CHMOD_DIR, 0775)));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		// Clean up contents of upgrade directory beforehand.
		$upgrade_files = $wp_filesystem->dirlist($upgrade_folder);
		if (!empty($upgrade_files)) {
			foreach ($upgrade_files as $file) {
				if (!$wp_filesystem->delete($upgrade_folder.$file['name'], true)) {
					$this->restore_log_permission_failure_message($upgrade_folder, 'Delete '.$upgrade_folder.$file['name']);
				}
			}
		}

		// We need a working directory
		$working_dir = $upgrade_folder . basename($package, '.crypt');

		// Clean up working directory
		if ($wp_filesystem->is_dir($working_dir)) {
			if (!$wp_filesystem->delete($working_dir, true)) {
				$this->restore_log_permission_failure_message(dirname($working_dir), 'Delete '.$working_dir);
			}
		}

		if (!$wp_filesystem->mkdir($working_dir, octdec($this->calculate_additive_chmod_oct(FS_CHMOD_DIR, 0775)))) return new WP_Error('mkdir_failed', __('Failed to create a temporary directory', 'updraftplus').' ('.$working_dir.')');

		// Unpack package to working directory
		if (UpdraftPlus_Encryption::is_file_encrypted($package)) {
			$this->skin->feedback($this->strings['decrypt_database']);

			$encryption = empty($this->restore_options['updraft_encryptionphrase']) ? UpdraftPlus_Options::get_updraft_option('updraft_encryptionphrase') : $this->restore_options['updraft_encryptionphrase'];

			if (!$encryption) return new WP_Error('no_encryption_key', __('Decryption failed. The database file is encrypted, but you have no encryption key entered.', 'updraftplus'));

			// function decrypt
			$decrypted_file = UpdraftPlus_Encryption::decrypt($backup_dir.$package, $encryption);

			if (is_array($decrypted_file)) {
				$this->skin->feedback($this->strings['decrypted_database']);
				if (!copy($decrypted_file['fullpath'], $working_dir.'/backup.db.gz')) {
					return new WP_Error('write_failed', __('Failed to write out the decrypted database to the filesystem', 'updraftplus'));
				} else {
					unlink($decrypted_file['fullpath']);
				}
			} else {
				return new WP_Error('decryption_failed', __('Decryption failed. The most likely cause is that you used the wrong key.', 'updraftplus'));
			}
		} else {
			if (preg_match('/\.sql$/i', $package)) {
				if (!$wp_filesystem->copy($backup_dir.$package, $working_dir.'/backup.db')) {
					if ($wp_filesystem->errors->get_error_code()) {
						foreach ($wp_filesystem->errors->get_error_messages() as $message) show_message($message);
					}
					return new WP_Error('copy_failed', $this->strings['copy_failed']);
				}
			} elseif (preg_match('/\.bz2$/i', $package)) {
				if (!$wp_filesystem->copy($backup_dir.$package, $working_dir.'/backup.db.bz2')) {
					if ($wp_filesystem->errors->get_error_code()) {
						foreach ($wp_filesystem->errors->get_error_messages() as $message) show_message($message);
					}
					return new WP_Error('copy_failed', $this->strings['copy_failed']);
				}
			} elseif (!$wp_filesystem->copy($backup_dir.$package, $working_dir.'/backup.db.gz')) {
				if ($wp_filesystem->errors->get_error_code()) {
					foreach ($wp_filesystem->errors->get_error_messages() as $message) show_message($message);
				}
				return new WP_Error('copy_failed', $this->strings['copy_failed']);
			}
		}

		// Once extracted, delete the package if required (non-recursive, is a file)
		// if ($delete_package) $wp_filesystem->delete($decrypted_file['fullpath'], false, true);
		if ($delete_package) {
			if (!$wp_filesystem->delete($backup_dir.$package, false, true)) {
				$this->restore_log_permission_failure_message($backup_dir, 'Delete '.$backup_dir.$package);
			}
		}

		$updraftplus->log('Database successfully unpacked');

		return $working_dir;
	}

	/**
	 * For moving files out of a directory into their new location
	 * The purposes of the $type parameter are 1) to detect 'others' and apply a historical bugfix 2) to detect wpcore, and apply the setting for what to do with wp-config.php 3) to work out whether to delete the directory itself
	 * Must use only wp_filesystem
	 * $dest_dir must already have a trailing slash
	 *
	 * @param  string  $working_dir       specify working directory
	 * @param  string  $dest_dir          specify destination directory (a WP_Filesystem path)
	 * @param  integer $preserve_existing this setting only applies at the top level: 0 = overwrite with no backup; 1 = make backup of existing; 2 = do nothing if there is existing, 3 = do nothing to the top level directory, but do copy-in contents (and over-write files). Thus, on a multi-archive set where you want a backup, you'd do this: first call with $preserve_existing === 1, then on subsequent zips call with 3
	 * @param  array   $do_not_overwrite  Specify files or directories not to overwrite
	 * @param  string  $type              specify type
	 * @param  boolean $send_actions      send actions
	 * @param  boolean $force_local       force local
	 * @return boolean
	 */
	public function move_backup_in($working_dir, $dest_dir, $preserve_existing = 1, $do_not_overwrite = array('plugins', 'themes', 'uploads', 'upgrade'), $type = 'not-others', $send_actions = false, $force_local = false) {

		global $wp_filesystem, $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();

		if (true == $force_local) {
			$wpfs = new UpdraftPlus_WP_Filesystem_Direct(true);
		} else {
			$wpfs = $wp_filesystem;
		}

		// Get the content to be moved in. Include hidden files = true. Recursion is only required if we're likely to copy-in
		$recursive = (self::MOVEIN_COPY_IN_CONTENTS == $preserve_existing) ? true : false;
		$upgrade_files = $wpfs->dirlist($working_dir, true, $recursive);

		if (empty($upgrade_files)) return true;

		// check if our path is a file by looking to see if it's in the list of files we want to restore, if it is then remove the file from the path
		if (isset($upgrade_files[basename($dest_dir)]) && 'f' == $upgrade_files[basename($dest_dir)]['type']) $dest_dir = trailingslashit(dirname($dest_dir));
		
		if (!$wpfs->is_dir($dest_dir)) {
			if ($wpfs->is_dir(dirname($dest_dir))) {
				if (!$wpfs->mkdir($dest_dir, FS_CHMOD_DIR)) return new WP_Error('mkdir_failed', __('The directory does not exist, and the attempt to create it failed', 'updraftplus') . ' (' . $dest_dir . ')');
				$updraftplus->log("Destination directory did not exist, but was successfully created ($dest_dir)");
			} else {
				return new WP_Error('no_such_dir', __('The directory does not exist', 'updraftplus') . " ($dest_dir)");
			}
		}

		$wpcore_config_moved = false;

		if ('plugins' == $type || 'themes' == $type) $updraftplus->log("Top-level entities being moved: ".implode(', ', array_keys($upgrade_files)));

		foreach ($upgrade_files as $file => $filestruc) {

			if (empty($file)) continue;

			if ($dest_dir.$file == $updraft_dir) {
				$updraftplus->log('Skipping attempt to replace updraft_dir whilst processing '.$type);
				continue;
			}

			// Correctly restore files in 'others' in no directory that were wrongly backed up in versions 1.4.0 - 1.4.48
			if (('others' == $type || 'wpcore' == $type) && preg_match('/^([\-_A-Za-z0-9]+\.php)$/i', $file, $matches) && $wpfs->exists($working_dir . "/$file/$file")) {
				if ('others' == $type) {
					$updraftplus->log("Found file: $file/$file: presuming this is a backup with a known fault (backup made with versions 1.4.0 - 1.4.48, and sometimes up to 1.6.55 on some Windows servers); will rename to simply $file", 'notice-restore');
				} else {
					$updraftplus->log("Found file: $file/$file: presuming this is a backup with a known fault (backup made with versions before 1.6.55 in certain situations on Windows servers); will rename to simply $file", 'notice-restore');
				}
				$updraftplus->log("$file/$file: rename to $file");
				$file = $matches[1];
				$tmp_file = rand(0, 999999999).'.php';
				// Rename directory
				if (!$wpfs->move($working_dir . "/$file", $working_dir . "/".$tmp_file, true)) {
					$this->restore_log_permission_failure_message($working_dir, 'Move '. $working_dir . "/$file -> ".$working_dir . "/".$tmp_file, 'Destination');
				}
				if (!$wpfs->move($working_dir . "/$tmp_file/$file", $working_dir ."/".$file, true)) {
					$this->restore_log_permission_failure_message($working_dir, 'Move '.$working_dir . "/$tmp_file/$file -> ".$working_dir ."/".$file, 'Destination');
				}
				if (!$wpfs->rmdir($working_dir . "/$tmp_file", false)) {
					$this->restore_log_permission_failure_message($working_dir, 'Delete '.$working_dir . "/$tmp_file");
				}
			}

			if ('wp-config.php' == $file && 'wpcore' == $type) {
				if (empty($this->restore_options['updraft_restorer_wpcore_includewpconfig'])) {
					$updraftplus->log_e('wp-config.php from backup: will restore as wp-config-backup.php', 'updraftplus');
					if (!$wpfs->move($working_dir . "/$file", $working_dir . "/wp-config-backup.php", true)) {
						$this->restore_log_permission_failure_message($working_dir, 'Move '.$working_dir . "/$file -> ".$working_dir . "/wp-config-backup.php", 'Destination');
					}
					$file = "wp-config-backup.php";
					$wpcore_config_moved = true;
				} else {
					$updraftplus->log_e("wp-config.php from backup: restoring (as per user's request)", 'updraftplus');
				}
			} elseif ('wpcore' == $type && 'wp-config-backup.php' == $file && $wpcore_config_moved) {
				// The file is already gone; nothing to do
				continue;
			}

			// Sanity check (should not be possible as these were excluded at backup time)
			if (in_array($file, $do_not_overwrite)) continue;

			if (('object-cache.php' == $file || 'advanced-cache.php' == $file) && 'others' == $type) {
				if (false == apply_filters('updraftplus_restorecachefiles', true, $file)) {
					$nfile = preg_replace('/\.php$/', '-backup.php', $file);
					if (!$wpfs->move($working_dir . "/$file", $working_dir . "/" .$nfile, true)) {
						$this->restore_log_permission_failure_message($working_dir, 'Move '. $working_dir . '/' . $file .' -> '.$working_dir . '/' . $nfile, 'Destination');
					}
					$file = $nfile;
				}
			} elseif (('object-cache-backup.php' == $file || 'advanced-cache-backup.php' == $file) && 'others' == $type) {
				if (!$wpfs->delete($working_dir."/".$file)) {
					$this->restore_log_permission_failure_message($working_dir, 'Delete '.$working_dir."/".$file);
				}
				continue;
			}

			// First, move the existing one, if necessary (may not be present)
			if ($wpfs->exists($dest_dir.$file)) {
				if (self::MOVEIN_MAKE_BACKUP_OF_EXISTING == $preserve_existing) {
					if (!$wpfs->move($dest_dir.$file, $dest_dir.$file.'-old', true)) {
						$this->restore_log_permission_failure_message($dest_dir, 'Move '. $dest_dir.$file.' -> '.$dest_dir.$file.'-old', 'Destination');
						return new WP_Error('old_move_failed', $this->strings['old_move_failed']." ($dest_dir$file)");
					}
				} elseif (self::MOVEIN_OVERWRITE_NO_BACKUP == $preserve_existing) {
					if (!$wpfs->delete($dest_dir.$file, true)) {
						$this->restore_log_permission_failure_message($dest_dir, 'Delete '.$dest_dir.$file);
						return new WP_Error('old_delete_failed', $this->strings['old_delete_failed']." ($file)");
					}
				}
			}


			// Secondly, move in the new one
			$is_dir = $wpfs->is_dir($working_dir."/".$file);

			if (self::MOVEIN_DO_NOTHING_IF_EXISTING == $preserve_existing && $wpfs->exists($dest_dir.$file)) {
				// Something exists - no move. Remove it from the temporary directory - so that it will be clean later
				@$wpfs->delete($working_dir.'/'.$file, true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			// The $is_dir check was added in version 1.11.18; without this, files in the top-level that weren't in the first archive didn't get over-written
			} elseif (self::MOVEIN_COPY_IN_CONTENTS != $preserve_existing || !$wpfs->exists($dest_dir.$file) || !$is_dir) {

				if ($wpfs->move($working_dir."/".$file, $dest_dir.$file, true)) {
					if ($send_actions) do_action('updraftplus_restored_'.$type.'_one', $file);
					// Make sure permissions are at least as great as those of the parent
					if ($is_dir) {
						// This method is broken due to https://core.trac.wordpress.org/ticket/26598
						if (empty($chmod)) $chmod = octdec(sprintf("%04d", $this->get_current_chmod($dest_dir, $wpfs)));// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
						if (!empty($chmod)) $this->chmod_if_needed($dest_dir.$file, $chmod, false, $wpfs);
					}
				} else {
					$this->restore_log_permission_failure_message($dest_dir, 'Move '. $working_dir."/".$file." -> ".$dest_dir.$file, 'Destination');
					return new WP_Error('move_failed', $this->strings['move_failed'], $working_dir."/".$file." -> ".$dest_dir.$file);
				}
			} elseif (self::MOVEIN_COPY_IN_CONTENTS == $preserve_existing && !empty($filestruc['files'])) {
				// The directory ($dest_dir) already exists, and we've been requested to copy-in. We need to perform the recursive copy-in
				// $filestruc['files'] is then a new structure like $upgrade_files
				// First pass: create directory structure
				// Get chmod value for the parent directory, and re-use it (instead of passing false)

				// This method is broken due to https://core.trac.wordpress.org/ticket/26598
				if (empty($chmod)) $chmod = octdec(sprintf("%04d", $this->get_current_chmod($dest_dir, $wpfs)));
				// Copy in the files. This also needs to make sure the directories exist, in case the zip file lacks entries
				$delete_root = ('others' == $type || 'wpcore' == $type) ? false : true;

				$copy_in = $this->copy_files_in($working_dir.'/'.$file, $dest_dir.$file, $filestruc['files'], $chmod, $delete_root);
				if (!empty($chmod)) $this->chmod_if_needed($dest_dir.$file, $chmod, false, $wpfs);

				if (is_wp_error($copy_in) || !$copy_in) {
					$this->restore_log_permission_failure_message($dest_dir, 'Move '. $working_dir."/".$file." -> ".$dest_dir.$file, 'Destination');
				}
				if (is_wp_error($copy_in)) return $copy_in;
				if (!$copy_in) return new WP_Error('move_failed', $this->strings['move_failed'], "(2) ".$working_dir.'/'.$file." -> ".$dest_dir.$file);

				if (!$wpfs->rmdir($working_dir.'/'.$file)) {
					$this->restore_log_permission_failure_message($working_dir, 'Delete '.$working_dir.'/'.$file);
				}
			} else {
				if (!$wpfs->rmdir($working_dir.'/'.$file)) {
					$this->restore_log_permission_failure_message($working_dir, 'Delete '.$working_dir.'/'.$file);
				}
			}
		}

		return true;

	}

	/**
	 * $dest_dir must already exist
	 *
	 * @param  string  $source_dir    source directory
	 * @param  string  $dest_dir      destintion directory
	 * @param  string  $files         files to be placed in directory
	 * @param  boolean $chmod         chmod type
	 * @param  boolean $delete_source indicate whether source needs deleting
	 * @return boolean
	 */
	private function copy_files_in($source_dir, $dest_dir, $files, $chmod = false, $delete_source = false) {
		global $wp_filesystem, $updraftplus;
		foreach ($files as $rname => $rfile) {
			if ('d' != $rfile['type']) {
				// Delete it if it already exists (or perhaps WP does it for us)
				if (!$wp_filesystem->move($source_dir.'/'.$rname, $dest_dir.'/'.$rname, true)) {
					$this->restore_log_permission_failure_message($dest_dir, $source_dir.'/'.$rname.' -> '.$dest_dir.'/'.$rname, 'Destination');
					return false;
				}
			} else {
				// Directory
				if ($wp_filesystem->is_file($dest_dir.'/'.$rname)) @$wp_filesystem->delete($dest_dir.'/'.$rname, false, 'f');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// No such directory yet: just move it
				if (!$wp_filesystem->is_dir($dest_dir.'/'.$rname)) {
					if (!$wp_filesystem->move($source_dir.'/'.$rname, $dest_dir.'/'.$rname, false)) {
						$this->restore_log_permission_failure_message($dest_dir, 'Move '.$source_dir.'/'.$rname.' -> '.$dest_dir.'/'.$rname, 'Destination');
						$updraftplus->log_e('Failed to move directory (check your file permissions and disk quota): %s', $source_dir.'/'.$rname." -&gt; ".$dest_dir.'/'.$rname);
						return false;
					}
				} elseif (!empty($rfile['files'])) {
					// There is a directory - and we want to to copy in
					$docopy = $this->copy_files_in($source_dir.'/'.$rname, $dest_dir.'/'.$rname, $rfile['files'], $chmod, false);
					if (is_wp_error($docopy)) return $docopy;
					if (false === $docopy) {
						return false;
					}
				} else {
					// There is a directory: but nothing to copy in to it
					@$wp_filesystem->rmdir($source_dir.'/'.$rname);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				}
			}
		}
		// We are meant to leave the working directory empty. Hence, need to rmdir() once a directory is empty. But not the root of it all in case of others/wpcore.
		if ($delete_source || strpos($source_dir, '/') !== false) {
			if (!$wp_filesystem->rmdir($source_dir, false)) {
				$this->restore_log_permission_failure_message($source_dir, 'Delete '.$source_dir);
			}
		}

		return true;

	}

	/**
	 * Pre-flight check: chance to complain and abort before anything at all is done
	 *
	 * @param  Array  $backup_files - An array of backup files
	 * @param  String $type			- Type of file
	 * @param  Array  $info			- Information about the backup
	 *
	 * @return Boolean|WP_Error
	 */
	private function pre_restore_backup($backup_files, $type, $info) {
		
		if (is_string($backup_files)) $backup_files = array($backup_files);

		// convert this to an array if it's not one as more files uses an array of paths
		if (empty($info['path'])) $info['path'] = array();
		if (!is_array($info['path'])) $info['path'] = array($info['path']);

		// Ensure access to the indicated directory - and to WP_CONTENT_DIR (in which we use upgrade/)
		$need_these = array(WP_CONTENT_DIR);

		foreach ($info['path'] as $path) {
			$need_these[] = $path;
		}

		$res = $this->wp_upgrader->fs_connect($need_these);
		if (false === $res || is_wp_error($res)) return $res;

		// Check upgrade directory is writable (instead of having non-obvious messages when we try to write)
		// In theory, this is redundant (since we already checked for access to WP_CONTENT_DIR); but in practice, this extra check has been needed

		global $wp_filesystem, $updraftplus, $updraftplus_addons_migrator;

		if (empty($this->pre_restore_updatedir_writable)) {
			$upgrade_folder = $wp_filesystem->wp_content_dir() . 'upgrade/';
			@$wp_filesystem->mkdir($upgrade_folder, octdec($this->calculate_additive_chmod_oct(FS_CHMOD_DIR, 0775)));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if (!$wp_filesystem->is_dir($upgrade_folder)) {
				return new WP_Error('no_dir', sprintf(__('UpdraftPlus needed to create a %s in your content directory, but failed - please check your file permissions and enable the access (%s)', 'updraftplus'), __('folder', 'updraftplus'), $upgrade_folder));
			}
			$rand_file = 'testfile_'.rand(0, 9999999).md5(microtime(true)).'.txt';
			if ($wp_filesystem->put_contents($upgrade_folder.$rand_file, 'testing...')) {
				@$wp_filesystem->delete($upgrade_folder.$rand_file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$this->pre_restore_updatedir_writable = true;
			} else {
				$this->restore_log_permission_failure_message($upgrade_folder, 'Put contents '.$upgrade_folder.$rand_file, 'Destination');
				return new WP_Error('no_file', sprintf(__('UpdraftPlus needed to create a %s in your content directory, but failed - please check your file permissions and enable the access (%s)', 'updraftplus'), __('file', 'updraftplus'), $upgrade_folder.$rand_file));
			}
		}

		// Code below here assumes that we're dealing with file-based entities
		if ('db' == $type) return true;

		foreach ($info['path'] as $path) {
			$wp_filesystem_dir = $this->get_wp_filesystem_dir($path);
			if (false === $wp_filesystem_dir) return false;

			$ret_val = true;
			$updraft_dir = $updraftplus->backups_dir_location();

			if (isset($this->continuation_data['updraftplus_ajax_restore']) && 'continue_ajax_restore' != $this->continuation_data['updraftplus_ajax_restore'] && (('plugins' == $type || 'uploads' == $type || 'themes' == $type || 'more' == $type) && (!is_multisite() || 0 !== $this->ud_backup_is_multisite || ('uploads' != $type || empty($updraftplus_addons_migrator->new_blogid))))) {
				if (file_exists($updraft_dir.'/'.basename($wp_filesystem_dir)."-old")) {
					$ret_val = new WP_Error('already_exists', sprintf(__('Existing unremoved folders from a previous restore exist (please use the "Delete Old Directories" button to delete them before trying again): %s', 'updraftplus'), $updraft_dir.'/'.basename($wp_filesystem_dir)."-old"));
				}
			}
		}

		if (!empty($this->ud_foreign)) {
			$known_foreigners = apply_filters('updraftplus_accept_archivename', array());
			if (!is_array($known_foreigners) || empty($known_foreigners[$this->ud_foreign])) {
				return new WP_Error('uk_foreign', __('This version of UpdraftPlus does not know how to handle this type of foreign backup', 'updraftplus').' ('.$this->ud_foreign.')');
			}
		}

		return $ret_val;
	}

	private function get_wp_filesystem_dir($path) {
		global $wp_filesystem;
		// Get the wp_filesystem location for the folder on the local install
		switch ($path) {
			case ABSPATH:
			case '':
				$wp_filesystem_dir = $wp_filesystem->abspath();
				break;
			case WP_CONTENT_DIR:
				$wp_filesystem_dir = $wp_filesystem->wp_content_dir();
				break;
			case WP_PLUGIN_DIR:
				$wp_filesystem_dir = $wp_filesystem->wp_plugins_dir();
				break;
			case WP_CONTENT_DIR . '/themes':
				$wp_filesystem_dir = $wp_filesystem->wp_themes_dir();
				// If the themes directory does not exist then it's possible to get a broken path, confirm and try to resolve this
				if (!$wp_filesystem->exists($wp_filesystem_dir) && $wp_filesystem_dir == $wp_filesystem->find_folder(WP_CONTENT_DIR.get_theme_root()) && $wp_filesystem->exists($wp_filesystem->wp_content_dir())) {
					$wp_filesystem_dir = $wp_filesystem->find_folder(WP_CONTENT_DIR.'/themes');
				}
				break;
			default:
				$wp_filesystem_dir = $wp_filesystem->find_folder($path);
				break;
		}
		if (!$wp_filesystem_dir) return false;
		return untrailingslashit($wp_filesystem_dir);
	}

	/**
	 * $backup_file is just the basename, and must be a string; we expect the caller to deal with looping over an array (multi-archive sets). We do, however, record whether we have already unpacked an entity of the same type - so that we know to add (not replace).
	 *
	 * @param  string  $backup_file name of file being backed up
	 * @param  string  $type        type of file
	 * @param  array   $info        information array
	 * @param  boolean $last_one    indicate if this is the last file to be restored
	 * @param  boolean $last_entity indicate if this is the last entity of this type to be restored
	 * @return WP_Error|Boolean - true if successful; otherwise false or an error
	 */
	private function restore_backup($backup_file, $type, $info, $last_one = false, $last_entity = false) {

		global $wp_filesystem, $updraftplus;

		$updraftplus->log("restore_backup(backup_file=$backup_file, type=$type, info=".serialize($info).", last_one=$last_one)");

		if ('db' == $type) {
			$get_dir = '';
		} else {
			$entity_path = isset($info['path']) ? $info['path'] : '';

			$path = apply_filters('updraftplus_restore_path', $entity_path, $backup_file, $this->ud_backup_set, $type);

			$get_dir = empty($path) ? '' : $path;
		}

		if (false === ($wp_filesystem_dir = $this->get_wp_filesystem_dir($get_dir))) return false;

		if (empty($this->abspath)) $this->abspath = trailingslashit($wp_filesystem->abspath());

		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		// This returns the wp_filesystem path
		$working_dir = $this->unpack_package($backup_file, $this->delete, $type);
		if (is_wp_error($working_dir)) return $working_dir;

		$working_dir_localpath = WP_CONTENT_DIR.'/upgrade/'.basename($working_dir);
		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		// We copy the variable because we may be importing with a different prefix (e.g. on multisite imports of individual blog data)
		// The filter allows you to restore to a completely different prefix - i.e. don't replace this site; possibly useful for testing the restore process (but not yet tested)
		$import_table_prefix = apply_filters('updraftplus_restore_table_prefix', $updraftplus->get_table_prefix(false));

		$this->import_table_prefix = $import_table_prefix;
		$this->final_import_table_prefix = $updraftplus->get_table_prefix(false);
		
		$now_done = apply_filters('updraftplus_pre_restore_move_in', false, $type, $working_dir, $info, $this->ud_backup_set, $this, $wp_filesystem_dir);
		if (is_wp_error($now_done)) return $now_done;

		// A slightly ugly way of getting a particular result back
		if (is_string($now_done)) {
			$wp_filesystem_dir = $now_done;
			$now_done = false;
			$do_not_move_old = true;
		}
		
		if (!$now_done) {
		
			if ('db' == $type) {
				$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'db', 'data' => array('stage' => 'begun', 'table' => '')));
				$rdb = $this->restore_backup_db($working_dir, $working_dir_localpath, $import_table_prefix);
				if (false === $rdb || is_wp_error($rdb)) return $rdb;
				$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'db', 'data' => array('stage' => 'finished', 'table' => '')));
			} elseif ('others' == $type) {

				// For foreign 'Simple Backup', we need to keep going down until we find wp-content
				if (empty($this->ud_foreign)) {
					$move_from = $working_dir;
				} else {
					$move_from = $this->search_for_folder('wp-content', $working_dir);
					if (!is_string($move_from)) return new WP_Error('not_found', __('The WordPress content folder (wp-content) was not found in this zip file.', 'updraftplus'));
				}

				// In this special case, the backup contents are not in a folder, so it is not simply a case of moving the folder around, but rather looping over all that we find

				// On subsequent archives of a multi-archive set, don't move anything; but do on the first
				$preserve_existing = isset($this->been_restored['others']) ? self::MOVEIN_COPY_IN_CONTENTS : self::MOVEIN_MAKE_BACKUP_OF_EXISTING;

				$preserve_existing = apply_filters('updraft_move_others_preserve_existing', $preserve_existing, $this->been_restored, $this->restore_options, $this->ud_backup_set);
				
				$new_move_from = apply_filters('updraft_restore_backup_move_from', $move_from, 'others', $this->restore_options, $this->ud_backup_set);
				
				if ($new_move_from != $move_from && 0 === strpos($new_move_from, $move_from)) {
					$new_suffix = substr($new_move_from, strlen($move_from));
					$wp_filesystem_dir .= $new_suffix;
					$move_from = $new_move_from;
				}

				$move_in = $this->move_backup_in($move_from, trailingslashit($wp_filesystem_dir), $preserve_existing, array('plugins', 'themes', 'uploads', 'upgrade'), 'others');
				if (is_wp_error($move_in)) return $move_in;
				if (!$move_in) return new WP_Error('new_move_failed', $this->strings['new_move_failed']);
				
				$this->been_restored['others'] = true;

			} else {

				// Default action: used for plugins, themes and uploads (and wpcore, via a filter)
				// Multi-archive sets: we record what we've already begun on, and on subsequent runs, copy in instead of replacing
				$movedin = apply_filters('updraftplus_restore_movein_'.$type, $working_dir, $this->abspath, $wp_filesystem_dir);

				// A filter, to allow add-ons to perform the install of non-standard entities, or to indicate that it's not possible
				if (false === $movedin) {
					$this->skin->feedback($this->strings['not_possible']);
				} elseif (is_wp_error($movedin)) {
					return $movedin;
				} elseif (true !== $movedin) {

					// We get the directory to move from early, in case there is a problem with the backup that affects the result - we want to detect that before moving existing data out of the way
					
					$short_circuit = false;
					
					// For foreign 'Simple Backup', we need to keep going down until we find wp-content
					if (empty($this->ud_foreign)) {
						$working_dir_use = $working_dir;
					} else {
						$working_dir_use = $this->search_for_folder('wp-content', $working_dir);
						if (!is_string($working_dir_use)) {
							if (empty($this->ud_foreign) || !apply_filters('updraftplus_foreign_allow_missing_entity', false, $type, $this->ud_foreign)) {
								return new WP_Error('not_found', __('The WordPress content folder (wp-content) was not found in this zip file.', 'updraftplus'));
							} else {
								$short_circuit = true;
							}
						}
					}
					
					// The backup may not actually have /$type, since that is info from the present site
					$move_from = $this->get_first_directory($working_dir_use, array(basename($path), $type));
					if (false !== $move_from) $move_from = apply_filters('updraft_restore_backup_move_from', $move_from, $type, $this->restore_options, $this->ud_backup_set);
					
					if (false === $move_from) {
						if (!empty($this->ud_foreign) && !apply_filters('updraftplus_foreign_allow_missing_entity', false, $type, $this->ud_foreign)) {
							return new WP_Error('new_move_failed', $this->strings['new_move_failed']);
						}
					}

					// On the first time, create the -old directory in updraft_dir
					// (Old style was: On the first time, move the existing data to -old)
					if (!isset($this->been_restored[$type]) && empty($do_not_move_old)) {
						$this->move_existing_to_old($type, $get_dir, $wp_filesystem, $wp_filesystem_dir);
					}
					
					if (empty($short_circuit)) {
										
						if (false === $move_from) {
							if (!empty($this->ud_foreign) && !apply_filters('updraftplus_foreign_allow_missing_entity', false, $type, $this->ud_foreign)) {
								return new WP_Error('new_move_failed', $this->strings['new_move_failed']);
							}
						} else {

							$this->skin->feedback($this->strings['moving_backup']);

							$move_in = $this->move_backup_in($move_from, trailingslashit($wp_filesystem_dir), self::MOVEIN_COPY_IN_CONTENTS, array(), $type);

							if (is_wp_error($move_in)) return $move_in;
							if (!$move_in) return new WP_Error('new_move_failed', $this->strings['new_move_failed']);

							if (!$wp_filesystem->rmdir($move_from)) {
								$this->restore_log_permission_failure_message(dirname($move_from), 'Delete '.$move_from);
							}
						}
					}

				}

				$this->been_restored[$type] = true;

			}
		}

		$attempt_delete = (!empty($this->ud_foreign) && !$last_one) ? false : true;
		
		if ($attempt_delete) {

			// Non-recursive, so the directory needs to be empty
			$this->skin->feedback($this->strings['cleaning_up']);
		
			if (!empty($do_not_move_old)) @$wp_filesystem->delete($working_dir.'/'.$type);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
			// Foreign backups can contain extra data and thus leave stuff behind, thus causing errors
			$recurse = empty($this->ud_foreign) ? false : true;
			$recurse = apply_filters('updraftplus_restore_delete_recursive', $recurse, $this->ud_foreign, $this->restore_options, $type);
		
			if (!$wp_filesystem->delete($working_dir, $recurse)) {

				// Can remove this after 1-Jan-2015; or at least, make it so that it requires the version number to be present.
				$fixed_it_now = false;
				// Deal with a corner-case in version 1.8.5
				if ('uploads' == $type && (empty($this->created_by_version) || (version_compare($this->created_by_version, '1.8.5', '>=') && version_compare($this->created_by_version, '1.8.8', '<')))) {
					$updraftplus->log("Clean-up failed with uploads: will attempt 1.8.5-1.8.7 fix (".$this->created_by_version.")");
					$move_in = @$this->move_backup_in(dirname($move_from), trailingslashit($wp_filesystem_dir), 3, array(), $type);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$updraftplus->log("Result: ".serialize($move_in));
					if ($wp_filesystem->delete($working_dir)) $fixed_it_now = true;
				}
				
				if (file_exists($working_dir.DIRECTORY_SEPARATOR.'updraftplus-manifest.json')) {
					// Before we cleanup and remove the manifest check if this is the last entity of this type, if it is then we want to remove anything that no longer exists in this manifest
					if ($last_entity) {
						$incremental_restore_prune = $this->incremental_restore_prune_files($working_dir, $type);
						if (is_wp_error($incremental_restore_prune)) return $incremental_restore_prune;
					}

					$wp_filesystem->delete($working_dir.DIRECTORY_SEPARATOR.'updraftplus-manifest.json');
					if ($wp_filesystem->delete($working_dir)) $fixed_it_now = true;
				}

				if (!$fixed_it_now) {
					$updraftplus->log_e('Error: %s', $this->strings['delete_failed'].' ('.$working_dir.')');
					// List contents
					// No need to make this a restoration-aborting error condition - it's not
					$dirlist = $wp_filesystem->dirlist($working_dir, true, true);
					if (is_array($dirlist)) {
						$updraftplus->log(__('Files found:', 'updraftplus'), 'notice-restore');
						foreach ($dirlist as $name => $struc) {
							$updraftplus->log("* $name", 'notice-restore');
						}
					} else {
						$updraftplus->log_e('Unable to enumerate files in that directory.');
					}
				}
			}
		}

		// Permissions changes (at the top level - i.e. this does not apply if using recursion) are now *additive* - i.e. there's no danger of permissions being removed from what's on-disk
		switch ($type) {
			case 'wpcore':
				$this->adjust_auto_prepend_directive();
				$this->chmod_if_needed($wp_filesystem_dir, FS_CHMOD_DIR, false, $wp_filesystem);
				// In case we restored a .htaccess which is incorrect for the local setup
				$this->flush_rewrite_rules();
				break;
			case 'uploads':
				$this->chmod_if_needed($wp_filesystem_dir, FS_CHMOD_DIR, false, $wp_filesystem);
				break;
			case 'themes':
				// Cherry Framework needs its cache files removing after migration
				if ((empty($this->old_siteurl) || ($this->old_siteurl != $this->our_siteurl)) && function_exists('glob')) {
					$cherry_child = glob(WP_CONTENT_DIR.'/themes/theme*');
					if (is_array($cherry_child)) {
						foreach ($cherry_child as $theme) {
							if (file_exists($theme.'/style.less.cache')) unlink($theme.'/style.less.cache');
							if (file_exists($theme.'/bootstrap/less/bootstrap.less.cache')) unlink($theme.'/bootstrap/less/bootstrap.less.cache');
						}
					}
				}
				break;
			case 'db':
				if (function_exists('wp_cache_flush')) wp_cache_flush();
				do_action('updraftplus_restored_db', array(
					'expected_oldsiteurl' => $this->old_siteurl,
					'expected_oldhome' => $this->old_home,
					'expected_oldcontent' => $this->old_content
				), $import_table_prefix);
				
				// N.B. flush_rewrite_rules() causes $wp_rewrite to become up to date again - important for the no_mod_rewrite() call
				$this->flush_rewrite_rules();

				if ($updraftplus->mod_rewrite_unavailable()) {
					$updraftplus->log("Using Apache, with permalinks (".get_option('permalink_structure').") but no mod_rewrite enabled - enable it to make your permalinks work");
					$warn_no_rewrite = sprintf(__('You are using the %s webserver, but do not seem to have the %s module loaded.', 'updraftplus'), 'Apache', 'mod_rewrite').' '.sprintf(__('You should enable %s to make any pretty permalinks (e.g. %s) work', 'updraftplus'), 'mod_rewrite', 'http://example.com/my-page/');
					$updraftplus->log($warn_no_rewrite, 'warning-restore');
				}
				break;
			default:
				$this->chmod_if_needed($wp_filesystem_dir, FS_CHMOD_DIR, false, $wp_filesystem);
		}
		// db was already done
		if ('db' != $type) do_action('updraftplus_restored_'.$type);

		return true;

	}

	/**
	 * This method will read in the latest manifest file for an entity type and start the file prune.
	 *
	 * @param  string $working_dir - the directory we are working in
	 * @param  string $type        - the type of file
	 * @return boolean|WP_Error
	 */
	private function incremental_restore_prune_files($working_dir, $type) {
		// Check file exists again just in case it some how got removed
		$manifest_file = $working_dir.DIRECTORY_SEPARATOR.'updraftplus-manifest.json';
		if (file_exists($manifest_file) && filesize($manifest_file) > 0) {
			$entity_manifest = file_get_contents($working_dir.DIRECTORY_SEPARATOR.'updraftplus-manifest.json');
			$decoded_manifest = json_decode($entity_manifest, true);
			if (null === $decoded_manifest) {
				// 2.16.0 could fail to put a comma after the first 'files' item
				$entity_manifest = preg_replace('/files":(.*)""/', 'files":$1","', $entity_manifest);
				$decoded_manifest = json_decode($entity_manifest, true);
				if (null === $decoded_manifest) return new WP_Error('decode_manifest_failed', 'Failed to JSON-decode the manifest file');
				global $updraftplus;
				$updraftplus->log('Manifest file had invalid JSON, but it was successfully patched');
			}
			$base_path = trailingslashit(WP_CONTENT_DIR);
			$path = $base_path.$type;
			return $this->incremental_restore_scan_dir($base_path, $path, 1, $decoded_manifest);
		} else {
			return new WP_Error('manifest_not_found', $this->strings['manifest_not_found']);
		}
	}

	/**
	 * This method will recursively scan each directory to the given listed_level which is located in the manifest and prune and files or folders that do not exist in the manifest.
	 *
	 * @param string  $base_path       - the base path of the entity type
	 * @param string  $path            - the current path we are scanning
	 * @param integer $current_level   - the level we are currently scanning at
	 * @param array   $entity_manifest - the manifest array which includes the listed_level, directories and files to keep
	 * @return boolean|WP_Error
	 */
	private function incremental_restore_scan_dir($base_path, $path, $current_level, $entity_manifest) {
		
		global $wp_filesystem;

		$directory_level = $entity_manifest['listed_levels'];
		$entity_directories = $entity_manifest['contents']['directories'];
		$entity_files = $entity_manifest['contents']['files'];

		if (!isset($directory_level) || !isset($entity_directories) || !isset($entity_files)) return new WP_Error('read_manifest_failed', $this->strings['read_manifest_failed']);

		$directory_files = $wp_filesystem->dirlist($path);

		if (isset($directory_files)) {
			foreach ($directory_files as $file => $filestruc) {
				if ($wp_filesystem->is_dir($path . DIRECTORY_SEPARATOR . $file)) {
					$directory = $path . DIRECTORY_SEPARATOR . $file;
					// Check if we should go deeper in the file path, if not then check if this directory exists in the manifest, if not then remove it.
					if ($current_level + 1 < $directory_level) {
						$incremental_restore_prune = $this->incremental_restore_scan_dir($base_path, $directory, $current_level + 1, $entity_manifest);
						if (is_wp_error($incremental_restore_prune)) return $incremental_restore_prune;
					} else {
						$directory = str_replace($base_path, "", $directory);
						if (!in_array($directory, $entity_directories)) {
							$wp_filesystem->delete($base_path . $directory, true);
						}
					}
				} else {
					$file = str_replace($base_path, "", $path . DIRECTORY_SEPARATOR . $file);
					if (!in_array($file, $entity_files)) {
						$wp_filesystem->delete($base_path . $file, false);
					}
				}
			}

			return true;
		} else {
			return new WP_Error('read_working_dir_failed', $this->strings['read_working_dir_failed']);
		}
	}

	private function move_existing_to_old($type, $get_dir, $wp_filesystem, $wp_filesystem_dir) {

		if (apply_filters('updraft_move_existing_to_old_short_circuit', false, $type, $this->restore_options)) {
			// Users of the filter should do their own logging
			return;
		}

		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();
	
		// Firstly, if there's already an '-old' directory, get rid of it

		// Try filesystem-level move
		$old_dir = $updraft_dir.'/'.$type.'-old';
		if (is_dir($old_dir)) {
			$updraftplus->log_e('%s: This directory already exists, and will be replaced', $old_dir);
			UpdraftPlus_Filesystem_Functions::remove_local_directory($old_dir);
		}

		$move_old_destination = apply_filters('updraftplus_restore_move_old_mode', 0, $type, $this->restore_options);

		if (0 == $move_old_destination && @mkdir($old_dir)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$updraftplus->log("Moving old data: filesystem method / updraft_dir is potentially possible");
			$move_old_destination = 1;
		}

		// Try wp_filesystem instead
		if ($wp_filesystem->exists($wp_filesystem_dir."-old")) {
			// Is better to warn and delete the restore than abort mid-restore and leave inconsistent site
			$updraftplus->log_e('%s: This directory already exists, and will be replaced', $wp_filesystem_dir."-old");
			// In theory, supplying true as the 3rd parameter achieves this; in practice, not always so (leads to support requests)
			$wp_filesystem->delete($wp_filesystem_dir."-old", true);
			if ($wp_filesystem->exists($wp_filesystem_dir."-old")) {
				$updraftplus->log("Failed to remove existing directory (".$wp_filesystem_dir."-old");
				$failed_to_remove = true;
			}
		}

		if (-1 != $move_old_destination && empty($failed_to_remove) && @$wp_filesystem->mkdir($wp_filesystem_dir."-old")) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$updraftplus->log("Moving old data: can potentially use wp_filesystem method / -old");
			$move_old_destination += 2;
		}

		if (0 == $move_old_destination) {
			$updraftplus->log_e("File permissions do not allow the old data to be moved and retained; instead, it will be deleted.");
		}

		$this->skin->feedback($this->strings['moving_old']);

		// Firstly, try direct filesystem method into updraft_dir
		if ($move_old_destination > 0 && 1 == $move_old_destination % 2) {
			// The final 'true' forces direct filesystem access
			$move_old = @$this->move_backup_in($get_dir, $updraft_dir.'/'.$type.'-old/', 3, array(), $type, false, true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if (is_wp_error($move_old)) $updraftplus->log_wp_error($move_old);
		}

		// Try wp_filesystem method into -old if that failed
		if (2 >= $move_old_destination && (0 == $move_old_destination % 2 || (!empty($move_old) && is_wp_error($move_old)))) {
			$move_old = @$this->move_backup_in($wp_filesystem_dir, $wp_filesystem_dir."-old/", 3, array(), $type);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if (is_wp_error($move_old)) $updraftplus->log_wp_error($move_old);
		}

		// Finally, when all else fails, nuke it
		if (-1 == $move_old_destination || 0 == $move_old_destination || (!empty($move_old) && is_wp_error($move_old))) {
			if (-1 == $move_old_destination) {
				$updraftplus->log("$type: $wp_filesystem_dir: deleting contents");
			} else {
				$updraftplus->log("$type: $wp_filesystem_dir: deleting contents (as attempts to copy failed)");
			}
			$del_files = $wp_filesystem->dirlist($wp_filesystem_dir, true, false);
			if (empty($del_files)) $del_files = array();
			foreach ($del_files as $file => $filestruc) {
				if (empty($file)) continue;
				if (!$wp_filesystem->delete($wp_filesystem_dir.'/'.$file, true)) {
					$this->restore_log_permission_failure_message($wp_filesystem_dir, 'Delete '.$wp_filesystem_dir.'/'.$file);
				}
			}
		}

	}
	
	/**
	 * First added in UD 1.9.47.
	 */
	private function clear_caches() {
		global $updraftplus;
		
		// Functions called here need to not assume that the relevant plugin actually exists - they should check for any functions they intend to call, before calling them.
		$methods = array(
			'clear_cache_wpsupercache',
			'clear_avada_fusion_cache', // avada theme with its theme engine called fusion
			'clear_elementor_cache',
		);
		
		foreach ($methods as $method) {
			try {
				call_user_func(array($this, $method));
			} catch (Exception $e) {
				$log_message = 'Exception ('.get_class($e).") occurred when cleaning up third-party cache ($method) during post-restore: ".$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				$updraftplus->log($log_message);
			} catch (Error $e) { // phpcs:ignore PHPCompatibility.Classes.NewClasses.errorFound
				$log_message = 'Error ('.get_class($e).") occurred when cleaning up third-party cache ($method) during post-restore: ".$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				$updraftplus->log($log_message);
			}
		}
			
		// It should be harmless to just purge the standard directory anyway (it's not backed up by default), and any others from other plugins
		$cache_sub_directories = array('cache', 'wphb-cache', 'endurance-page-cache');
		foreach ($cache_sub_directories as $sub_dir) {
			if (!is_dir(WP_CONTENT_DIR.'/'.$sub_dir)) continue;
			UpdraftPlus_Filesystem_Functions::remove_local_directory(WP_CONTENT_DIR.'/'.$sub_dir, true);
		}
	}

	/**
	 * Clear cached Fusion-theme's Dynamic CSS
	 */
	private function clear_avada_fusion_cache() {
		global $updraftplus;
		$upload_dir = $updraftplus->wp_upload_dir();
		$fusion_css_dir = realpath($upload_dir['basedir']).DIRECTORY_SEPARATOR.'fusion-styles';
		if (is_dir($fusion_css_dir)) {
			$updraftplus->log("Avada/Fusion's dynamic CSS folder exists, and will be emptied (so that it will be automatically regenerated)");
			UpdraftPlus_Filesystem_Functions::remove_local_directory($fusion_css_dir, true);
		}
	}

	/**
	 * Clear cached Elementor styles that are saved in CSS files in the uploads folder. Clearing the caches will regenerate new CSS files, according to the most recent settings.
	 */
	private function clear_elementor_cache() {
		global $updraftplus;
		$cache_uncleared = true;
		if (apply_filters('updraftplus_elementor_direct_clear_cache', $cache_uncleared)) {
			if (class_exists('\Elementor\Plugin')) class_alias('\Elementor\Plugin', 'UpdraftPlus_Elementor_Plugin'); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.class_aliasFound
			if (class_exists('UpdraftPlus_Elementor_Plugin') && isset(UpdraftPlus_Elementor_Plugin::$instance) && isset(UpdraftPlus_Elementor_Plugin::$instance->files_manager) && is_object(UpdraftPlus_Elementor_Plugin::$instance->files_manager) && method_exists(UpdraftPlus_Elementor_Plugin::$instance->files_manager, 'clear_cache')) {
				$updraftplus->log("Elementor's clear cache method exists and will be executed");
				UpdraftPlus_Elementor_Plugin::$instance->files_manager->clear_cache();
				$cache_uncleared = false;
			}
		}
		if (apply_filters('updraftplus_elementor_manual_clear_cache', $cache_uncleared)) {
			$upload_dir = $updraftplus->wp_upload_dir();
			$elementor_css_dir = realpath($upload_dir['basedir']).DIRECTORY_SEPARATOR.'elementor'.DIRECTORY_SEPARATOR.'css';
			if (is_dir($elementor_css_dir)) {
				$updraftplus->log("Elementor's CSS directory exists, and will be emptied (so that it will be automatically regenerated)");
				UpdraftPlus_Filesystem_Functions::remove_local_directory($elementor_css_dir, true);
			}
			// deleting only the Elementor's CSS files won't make Elementor regenerate the new CSS files the next time the website is loaded/visited again
			// Elementor will regenerate the files only if these meta and options get deleted as well
			delete_post_meta_by_key('_elementor_css');
			delete_option('_elementor_global_css');
			delete_option('elementor-custom-breakpoints-files');
		}
	}

	/**
	 * Adapted from wp_cache_clean_cache($file_prefix, $all = false) in WP Super Cache (wp-cache.php)
	 *
	 * @return boolean
	 */
	private function clear_cache_wpsupercache() {
		$all = true;

		global $updraftplus, $cache_path, $wp_cache_object_cache;

		if ($wp_cache_object_cache && function_exists('reset_oc_version')) reset_oc_version();

		// Removed check: && wpsupercache_site_admin()
		if (true == $all && function_exists('prune_super_cache')) {
			if (!empty($cache_path)) {
				$updraftplus->log_e("Clearing cached pages (%s)...", 'WP Super Cache');
				prune_super_cache($cache_path, true);
			}
			return true;
		}
	}

	private function search_for_folder($folder, $startat) {
		if (!is_dir($startat)) return false;
		// Exists in this folder?
		if (is_dir($startat.'/'.$folder)) return trailingslashit($startat).$folder;
		// Does not
		if ($handle = opendir($startat)) {
			while (($file = readdir($handle)) !== false) {
				if ('.' != $file && '..' != $file && is_dir($startat).'/'.$file) {
					$ss = $this->search_for_folder($folder, trailingslashit($startat).$file);
					if (is_string($ss)) return $ss;
				}
			}
			closedir($handle);
		}
		return false;
	}

	/**
	 * Returns an octal string (but not an octal number)
	 *
	 * @param  String				 $file The file to get the permissions for
	 * @param  WP_Filesystem|Boolean $wpfs WP_Filesystem object, at least support the getchmod() method
	 *
	 * @return String
	 */
	private function get_current_chmod($file, $wpfs = false) {
		if (false == $wpfs) {
			global $wp_filesystem;
			$wpfs = $wp_filesystem;
		}
		// getchmod() is broken at least as recently as WP3.8 - see: https://core.trac.wordpress.org/ticket/26598
		return (is_a($wpfs, 'WP_Filesystem_Direct')) ? substr(sprintf("%06d", decoct(@fileperms($file))), 3) : $wpfs->getchmod($file);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	}

	/**
	 * Returns a string in octal format
	 * $new_chmod should be an octal, i.e. what you'd pass to chmod()
	 *
	 * @param  string $old_chmod specify old chmod
	 * @param  string $new_chmod specify new chmod
	 * @return string
	 */
	private function calculate_additive_chmod_oct($old_chmod, $new_chmod) {
		// chmod() expects octal form, which means a preceding zero - see http://php.net/chmod
		$old_chmod = sprintf("%04d", $old_chmod);
		$new_chmod = sprintf("%04d", decoct($new_chmod));

		for ($i=1; $i<=3; $i++) {
			$oldbit = substr($old_chmod, $i, 1);
			$newbit = substr($new_chmod, $i, 1);
			for ($j=0; $j<=2; $j++) {
				if (($oldbit & (1<<$j)) && !($newbit & (1<<$j))) {
					$newbit = (string) ($newbit | 1<<$j);
					$new_chmod = sprintf("%04d", substr($new_chmod, 0, $i).$newbit.substr($new_chmod, $i+1));
				}
			}
		}

		return $new_chmod;
	}

	/**
	 * "If needed" means, "If the permissions are not already more permissive than this". i.e. This will not tighten permissions from what the user had before (we trust them)
	 * $chmod should be an octal - i.e. the same as you'd pass to chmod()
	 *
	 * @param  String  $dir       a WP_Filesystem path
	 * @param  String  $chmod     specific chmod
	 * @param  Boolean $recursive indicate if recursive chmod is needed
	 * @param  Boolean $wpfs      indicate whether to use wpfs access methods
	 * @param  Boolean $suppress  suppress PHP error/warning output
	 *
	 * @return Boolean - whether the operation was successfully carried out
	 */
	private function chmod_if_needed($dir, $chmod, $recursive = false, $wpfs = false, $suppress = true) {

		// Do nothing on Windows
		if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) return true;

		if (false == $wpfs) {
			global $wp_filesystem;
			$wpfs = $wp_filesystem;
		}

		$old_chmod = $this->get_current_chmod($dir, $wpfs);

		// Sanity check
		if (strlen($old_chmod) < 3) return false;

		$new_chmod = $this->calculate_additive_chmod_oct($old_chmod, $chmod);

		// Don't fix what isn't broken
		if (!$recursive && $new_chmod == $old_chmod) return true;

		$new_chmod = octdec($new_chmod);

		if ($suppress) {
			return @$wpfs->chmod($dir, $new_chmod, $recursive);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {
			return $wpfs->chmod($dir, $new_chmod, $recursive);
		}
	}

	/**
	 * This will return the path with the actual content we want to restore, ignoring any other files that may be in the top level of the zip file
	 * $dirnames: an array of preferred names
	 *
	 * @param  string $working_dir specify working directory
	 * @param  string $dirnames    directory names
	 * @return string the final path with the content we want to restore
	 */
	public function get_first_directory($working_dir, $dirnames) {
		global $wp_filesystem, $updraftplus;
		$fdirnames = array_flip($dirnames);
		$dirlist = $wp_filesystem->dirlist($working_dir, true, false);
		if (is_array($dirlist)) {
			$move_from = false;
			foreach ($dirlist as $name => $struc) {
				if (isset($struc['type']) && 'd' != $struc['type']) continue;
				if (false === $move_from) {
					if (isset($fdirnames[$name])) {
						$move_from = $working_dir . "/".$name;
					} elseif (preg_match('/^([^\.].*)$/', $name, $fmatch)) {
						// In the case of a third-party backup, the first entry may be the wrong entity. We could try a more sophisticated algorithm, but a third party backup requiring one has never been seen (and it is not easy to envisage what the algorithm might be).
						if (empty($this->ud_foreign)) {
							$first_entry = $working_dir."/".$fmatch[1];
						}
					}
				}
			}
			if (false === $move_from && isset($first_entry)) {
				$updraftplus->log_e('Using directory from backup: %s', basename($first_entry));
				$move_from = $first_entry;
			}
		} else {
			// That shouldn't happen. Fall back to default
			$move_from = $working_dir."/".$dirnames[0];
		}
		return $move_from;
	}

	/**
	 * Enter or leave maintenance mode
	 *
	 * @param Boolean $active - whether to activate, or de-activate, maintenance mode
	 */
	private function maintenance_mode($active) {
		// This allows add-ons to do something different if they prefer
		if (apply_filters('updraft_restore_maintenance_mode', true, $active, $this, $this->wp_upgrader)) {
			$this->wp_upgrader->maintenance_mode($active);
		}
	}
	
	/**
	 * Gets the table prefix to use, using the filter updraftplus_restore_set_import_table_prefix
	 *
	 * @param String $import_table_prefix - table prefix to act upon
	 *
	 * @return String|WP_Error|Boolean - the modified table prefix, or an error or indication of an error
	 */
	private function pre_sql_actions($import_table_prefix) {

		global $updraftplus;

		$import_table_prefix = apply_filters('updraftplus_restore_set_table_prefix', $import_table_prefix, $this->ud_backup_is_multisite);

		if (!is_string($import_table_prefix)) {
			$this->maintenance_mode(false);
			if (false === $import_table_prefix) {
				$updraftplus->log(__('Please supply the requested information, and then continue.', 'updraftplus'), 'notice-restore');
				return false;
			} elseif (is_wp_error($import_table_prefix)) {
				return $import_table_prefix;
			} else {
				return new WP_Error('invalid_table_prefix', __('Error:', 'updraftplus').' '.serialize($import_table_prefix));
			}
		}

		$updraftplus->log_e('New table prefix: %s', $import_table_prefix);

		return $import_table_prefix;

	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_permalink_structure($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('permalink_structure');
	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_page_on_front($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('page_on_front');
	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_rewrite_rules($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('rewrite_rules');
	}

	/**
	 * Assign a value to log_bin_trust_function_creators system variable and return its previous value
	 *
	 * @see https://mariadb.com/kb/en/library/binary-logging-of-stored-routines/
	 * @see https://dev.mysql.com/doc/refman/8.0/en/stored-programs-logging.html
	 *
	 * @param String $value It can only be set to ON or OFF
	 * @return String|WP_Error the variable value before it got assigned a new value, or WP_Error object on failure
	 */
	private function set_log_bin_trust_function_creators($value) {
		
		global $wpdb;
		static $saved_value = null;
		static $initial_value = null;

		$old_val = $wpdb->suppress_errors();
		try {
			if (is_null($initial_value) || is_wp_error($initial_value)) {
				$creators_val = $wpdb->get_var("SELECT @@GLOBAL.log_bin_trust_function_creators");
				if (is_null($creators_val)) throw new Exception(sprintf(__('An error occurred while attempting to retrieve the MySQL global log_bin_trust_function_creators variable %s', 'updraftplus'), '('. $wpdb->last_error.' - '.$wpdb->last_query.')'), 0);
				$initial_value = '1' === $creators_val || 'on' === strtolower($creators_val) ? 'ON' : 'OFF';
			}
			if ((is_null($saved_value) || ($saved_value != $value))) {
				$res = $wpdb->query("SET GLOBAL log_bin_trust_function_creators = ".$value);
				if (false === $res) {
					$saved_value = null;
					throw new Exception(sprintf(__('An error occurred while attempting to set a new value to the MySQL global log_bin_trust_function_creators variable %s', 'updraftplus'), '('. $wpdb->last_error.' - '.$wpdb->last_query.')'), 0);
				}
				if (!is_null($saved_value)) {
					$initial_value = $saved_value;
				}
				$saved_value = $value;
			}
		} catch (Exception $ex) {
			$initial_value = new WP_Error('log_bin_trust_function_creators', $ex->getMessage());
		}
		$wpdb->suppress_errors($old_val);

		return $initial_value;
	}

	/**
	 * Prepare the create table statement before sending it to the query execution
	 *
	 * @param String $create_table_statement an SQL create table statement in which some part of the SQL is going to be parsed and/or replaced
	 * @param String $import_table_prefix    table prefix to use
	 * @param Array  $supported_engines      the list of supported DB engines
	 * @param Array  $supported_charsets     the list of supported DB charsets
	 * @param Array  $supported_collations   the list of supported DB collations
	 * @return String the processed create table statement that may have been transformed, sanitised or cleaned
	 */
	private function prepare_create_table($create_table_statement, $import_table_prefix, $supported_engines, $supported_charsets, $supported_collations) {

		global $updraftplus, $wpdb;

		$updraft_restorer_collate = isset($this->restore_options['updraft_restorer_collate']) ? $this->restore_options['updraft_restorer_collate'] : '';

		$non_wp_table = false;

		// Legacy, less reliable - in case it was not caught before. We added it in here (CREATE) as well as in DROP because of SQL dumps which lack DROP statements.
		if (null === $this->old_table_prefix && preg_match('/^([a-z0-9]+)_.*$/i', $this->table_name, $tmatches)) {
			$this->old_table_prefix = $tmatches[1].'_';
			$updraftplus->log(__('Old table prefix:', 'updraftplus').' '.$this->old_table_prefix, 'notice-restore', 'old-table-prefix');
			$updraftplus->log("Old table prefix (detected from creating first table): ".$this->old_table_prefix);
		}

		// MySQL 4.1 outputs TYPE=, but accepts ENGINE=; 5.1 onwards accept *only* ENGINE=
		$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace('TYPE=', 'ENGINE=', $create_table_statement);

		if ('' === $this->old_table_prefix) {
			$this->new_table_name = $import_table_prefix.$this->table_name;
		} else {
			$this->new_table_name = $this->old_table_prefix ? UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $this->table_name) : $this->table_name;
			// if we have a different prefix but the table name has not changed after the replace then we are dealing with a table that does not use the WordPress table prefix, in order for an Atmoic restore to work on this table we need to attach our temporary prefix
			if (!$this->rename_forbidden && $this->old_table_prefix && $this->new_table_name == $this->table_name) {
				$non_wp_table = true;
				$this->new_table_name = $import_table_prefix.$this->table_name;
			}
		}

		// This CREATE TABLE command may be the de-facto mark for the end of processing a previous table (which is so if this is not the first table in the SQL dump)
		if ($this->restoring_table) {

			// Attempt to reconnect if the DB connection dropped (may not succeed, of course - but that will soon become evident)
			$updraftplus->check_db_connection($this->wpdb_obj);

			// After restoring the options table, we can set old_siteurl if on legacy (i.e. not already set)
			if ($this->restoring_table == $import_table_prefix.'options') {
				if ('' == $this->old_siteurl || '' == $this->old_home || '' == $this->old_content) {
					global $updraftplus_addons_migrator;
					if (!empty($updraftplus_addons_migrator->new_blogid)) switch_to_blog($updraftplus_addons_migrator->new_blogid);

					if ('' == $this->old_siteurl) {
						$this->old_siteurl = untrailingslashit($wpdb->get_row("SELECT option_value FROM ".$import_table_prefix.'options'." WHERE option_name='siteurl'")->option_value);
						do_action('updraftplus_restore_db_record_old_siteurl', $this->old_siteurl);
					}
					if ('' == $this->old_home) {
						$this->old_home = untrailingslashit($wpdb->get_row("SELECT option_value FROM ".$import_table_prefix.'options'." WHERE option_name='home'")->option_value);
						do_action('updraftplus_restore_db_record_old_home', $this->old_home);
					}
					if ('' == $this->old_content) {
						$this->old_content = $this->old_siteurl.'/wp-content';
						do_action('updraftplus_restore_db_record_old_content', $this->old_content);
					}
					if (!empty($updraftplus_addons_migrator->new_blogid)) restore_current_blog();
				}
			}

			if ($this->restoring_table != $this->new_table_name) {
				$final_table_name = $this->maybe_rename_restored_table();
				$this->restored_table($final_table_name, $this->final_import_table_prefix, $this->old_table_prefix, $this->table_engine);
			}

		}

		// Detect this as early as possible so we can turn off atomic restores if needed. If the table prefix has changed and key constraints are found, make sure they are updated
		$constraint_change_message = '';
		$constraints = array();
		$constraint_found = false;
		if (preg_match_all('/CONSTRAINT ([\a-zA-Z0-9_\']+) FOREIGN KEY \([a-zA-z0-9_\', ]+\) REFERENCES \'?([a-zA-z0-9_]+)\'? /i', $create_table_statement, $constraint_matches)) {
			$constraints = $constraint_matches;
			$constraint_found = true;
		} elseif (preg_match_all('/ FOREIGN KEY \([a-zA-z0-9_\', ]+\) REFERENCES \'?([a-zA-z0-9_]+)\'? /i', $create_table_statement, $constraint_matches)) {
			$constraints = $constraint_matches;
			$constraint_found = true;
		}

		// Constraints were found so we need to disable the atomic restore for this table, which means resetting the import table prefix and current table name and finally dropping the original table if it exists
		if ($constraint_found && !$this->rename_forbidden && !$this->is_dummy_db_restore) {
			
			$import_table_prefix = $this->final_import_table_prefix;
			$this->disable_atomic_on_current_table = true;
			
			if ('' === $this->old_table_prefix) {
				$this->new_table_name = $import_table_prefix.$this->table_name;
			} else {
				$this->new_table_name = $this->old_table_prefix ? UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $this->table_name) : $this->table_name;
			}

			$updraftplus->log('Constraints found, will disable atomic restore for current table ('.$this->table_name.')', 'notice-restore');

			$this->drop_tables(array($this->new_table_name));

		}

		$this->table_engine = "(?)";
		$engine_change_message = '';
		if (preg_match('/ENGINE=([^\s;]+)/', $create_table_statement, $eng_match)) {
			$this->table_engine = $eng_match[1];
			if (isset($supported_engines[strtolower($this->table_engine)])) {
				if ('myisam' == strtolower($this->table_engine)) {
					$create_table_statement = preg_replace('/PAGE_CHECKSUM=\d\s?/', '', $create_table_statement, 1);
				}
			} else {
				$engine_change_message = sprintf(__('Requested table engine (%s) is not present - changing to MyISAM.', 'updraftplus'), $this->table_engine)."<br>";
				$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace("ENGINE=$this->table_engine", "ENGINE=MyISAM", $create_table_statement);
				$this->table_engine = "MyISAM";
				// Remove (M)aria options
				if ('maria' == strtolower($this->table_engine) || 'aria' == strtolower($this->table_engine) || 'myisam' == strtolower($this->table_engine)) {
					$create_table_statement = preg_replace('/PAGE_CHECKSUM=\d\s?/', '', $create_table_statement, 1);
					$create_table_statement = preg_replace('/TRANSACTIONAL=\d\s?/', '', $create_table_statement, 1);
				}
			}
		}
		$charset_change_message = '';
		if (preg_match('/ CHARSET=([^\s;]+)/i', $create_table_statement, $charset_match)) {
			$charset = $charset_match[1];
			if (!isset($supported_charsets[strtolower($charset)])) {
				$charset_change_message = sprintf(__('Requested table character set (%s) is not present - changing to %s.', 'updraftplus'), esc_html($charset), esc_html($this->restore_options['updraft_restorer_charset']));
				$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace("CHARSET=$charset", "CHARSET=".$this->restore_options['updraft_restorer_charset'], $create_table_statement);
				// Allow default COLLLATE to database
				if (preg_match('/ COLLATE=([^\s;]+)/i', $create_table_statement, $collate_match)) {
					$collate = $collate_match[1];
					$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace(" COLLATE=$collate", "", $create_table_statement);
				}
			}
		}

		if (!empty($constraints) && $this->old_table_prefix != $this->final_import_table_prefix) {
			foreach ($constraints[0] as $constraint) {
				$updated_constraint = str_replace($this->old_table_prefix, $this->final_import_table_prefix, $constraint);
				$create_table_statement = str_replace($constraint, $updated_constraint, $create_table_statement);
			}
			$constraint_change_message = __('Found and replaced existing table foreign key constraints as the table prefix has changed.', 'updraftplus');
		}

		$collate_change_message = '';
		$unsupported_collates_in_sql_line = array();
		if (!empty($updraft_restorer_collate) && preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $create_table_statement, $collate_match)) {
			$collate = $collate_match[1];
			if (!isset($supported_collations[strtolower($collate)])) {
				$unsupported_collates_in_sql_line[] = $collate;
				if ('choose_a_default_for_each_table' == $updraft_restorer_collate) {
					$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace("COLLATE=$collate", "", $create_table_statement, false);
				} else {
					$create_table_statement = UpdraftPlus_Manipulation_Functions::str_lreplace("COLLATE=$collate", "COLLATE=".$updraft_restorer_collate, $create_table_statement, false);
				}
			}
		}
		if (!empty($updraft_restorer_collate) && preg_match_all('/ COLLATE ([a-zA-Z0-9._-]+) /i', $create_table_statement, $collate_matches)) {
			$collates = array_unique($collate_matches[1]);
			foreach ($collates as $collate) {
				if (!isset($supported_collations[strtolower($collate)])) {
					$unsupported_collates_in_sql_line[] = $collate;
					if ('choose_a_default_for_each_table' == $updraft_restorer_collate) {
						$create_table_statement = str_ireplace("COLLATE $collate ", "", $create_table_statement);
					} else {
						$create_table_statement = str_ireplace("COLLATE $collate ", "COLLATE ".$updraft_restorer_collate." ", $create_table_statement);
					}
				}
			}
		}
		if (!empty($updraft_restorer_collate) && preg_match_all('/ COLLATE ([a-zA-Z0-9._-]+),/i', $create_table_statement, $collate_matches)) {
			$collates = array_unique($collate_matches[1]);
			foreach ($collates as $collate) {
				if (!isset($supported_collations[strtolower($collate)])) {
					$unsupported_collates_in_sql_line[] = $collate;
					if ('choose_a_default_for_each_table' == $updraft_restorer_collate) {
						$create_table_statement = str_ireplace("COLLATE $collate,", ",", $create_table_statement);
					} else {
						$create_table_statement = str_ireplace("COLLATE $collate,", "COLLATE ".$updraft_restorer_collate.",", $create_table_statement);
					}
				}
			}
		}
		if (count($unsupported_collates_in_sql_line) > 0) {
			$unsupported_unique_collates_in_sql_line = array_unique($unsupported_collates_in_sql_line);
			$collate_change_message = sprintf(_n('Requested table collation (%1$s) is not present - changing to %2$s.', 'Requested table collations (%1$s) are not present - changing to %2$s.', count($unsupported_unique_collates_in_sql_line), 'updraftplus'), esc_html(implode(', ', $unsupported_unique_collates_in_sql_line)), esc_html($this->restore_options['updraft_restorer_collate']));
		}
		$print_line = sprintf(__('Processing table (%s)', 'updraftplus'), $this->table_engine).":  ".$this->table_name;
		$logline = "Processing table ($this->table_engine): ".$this->table_name;
		if (null !== $this->old_table_prefix && $import_table_prefix != $this->old_table_prefix) {
			if ($this->restore_this_table($this->table_name)) {
				$print_line .= ' - '.__('will restore as:', 'updraftplus').' '.htmlspecialchars($this->new_table_name);
				$logline .= " - will restore as: ".$this->new_table_name;
			} else {
				$logline .= ' - skipping';
			}
			if ('' === $this->old_table_prefix || $non_wp_table) {
				$create_table_statement = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->new_table_name, $create_table_statement);
			} else {
				$create_table_statement = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $create_table_statement);
			}

			$this->restored_table_names[] = $this->new_table_name;
		}

		if (!empty($this->generated_columns[$this->table_name]) && isset($this->generated_columns[$this->table_name]['columns'])) {

			// get all the keys definition in the create table statement if any.
			// preg_match_all('/\s*[^,]+?key\s*[^(]+\(\s*(`.+?(?:\)|`))\s*\),?/i', $create_table_statement, $key_definitions);
			// https://regex101.com/r/NEXaLy/1/
			preg_match_all('/(?<![\S"\',])[^"\',]+?KEY\s*[^(]+\(\s*(`.+?(?:\)|`))\s*\)\s*(?:,|\))(?![\S"\',])/i', $create_table_statement, $key_definitions);

			$reversed_generated_columns = array_reverse((array) $this->generated_columns[$this->table_name]['columns']);
			foreach ((array) $reversed_generated_columns as $generated_column) {
				if (empty($generated_column)) continue;
				if (!isset($this->supported_generated_column_engines[strtolower($this->table_engine)])) $this->supported_generated_column_engines[strtolower($this->table_engine)] = UpdraftPlus_Database_Utility::is_generated_column_supported($this->table_engine);
				if ($generated_column_db_info = $this->supported_generated_column_engines[strtolower($this->table_engine)]) {

					$reversed_data_type_definition = array_reverse((array) $generated_column['column_data_type_definition']);
					foreach ($reversed_data_type_definition as $key => &$data_type_definition) {
						if (in_array($key, array('DATA_TYPE_TOKEN', 'GENERATED_ALWAYS_TOKEN', 'COMMENT_TOKEN'))) continue; // we dont want to replace "not null" in the "generated always as" expression, neither in the comments' string as well, so we continue
						if (empty($data_type_definition) || 0 === strlen(trim($data_type_definition[0]))) continue;
						if (!$generated_column_db_info['is_not_null_supported']) {
							// If the database server doesn't support either null or not null constraint on generated virtual/stored/persistent column then the constraints need to be removed
							$replaced_data_type_definition = preg_replace('/\b(?:not\s+null|null)\b/i', '', $data_type_definition[0]);
							$create_table_statement = substr_replace($create_table_statement, $replaced_data_type_definition, $data_type_definition[1], strlen($data_type_definition[0]));
							$data_type_definition[0] = $replaced_data_type_definition;
						}
						if (!$generated_column['is_virtual'] && !$generated_column_db_info['is_persistent_supported']) {
							// If the persistent type is not supported it likely means that the currently running db server is MySQL, Mariadb uses persistent as an alias for stored type so if the backup file is taken from MariaDB then it needs to be changed to stored
							$replaced_data_type_definition = preg_replace('/\bpersistent\b/i', 'STORED', $data_type_definition[0]);
							$create_table_statement = substr_replace($create_table_statement, $replaced_data_type_definition, $data_type_definition[1], strlen($data_type_definition[0]));
							$data_type_definition[0] = $replaced_data_type_definition;
						}
					}

					if ($generated_column['is_virtual'] && ($generated_column_db_info['can_insert_ignore_to_generated_column'] || (isset($this->generated_columns_exist_in_the_statement[$this->table_name]) && false === $this->generated_columns_exist_in_the_statement[$this->table_name])) && !$generated_column_db_info['is_virtual_index_supported'] && !empty($key_definitions)) {
						// MySQL doesn't support index on MyISAM's virtual generated column, in case that the restoration process is importing from MariaDB backup file which contains create index definition on virtual generated column then it needs to be removed too
						// the column can be defined as a single or composite index, so we have no choice but loop until the end
						foreach ($key_definitions[1] as $array_index => $column_names) {
							if (empty($column_names)) continue;
							if (empty($key_definitions[0][$array_index])) continue;
							if (is_numeric(stripos($column_names, $generated_column['column_name']))) {
								$replaced_key_definition = preg_replace('/(\s*,?[^,]+?key\s*[^(]+\(.*?)(`'.$generated_column['column_name'].'`\s*(?:\([0-9]+\)\s*)?,|,\s*`'.$generated_column['column_name'].'`\s*(?:\([0-9]+\)\s*)?|\s*`'.$generated_column['column_name'].'`\s*(?:\([0-9]+\)\s*)?)(.*\))/ism', '$1$3', $key_definitions[0][$array_index]);
								$create_table_statement = str_ireplace($key_definitions[0][$array_index], $replaced_key_definition, $create_table_statement);
								$key_definitions[0][$array_index] = $replaced_key_definition;
								if (preg_match('/\s*,?[^,]+?key\s*[^(]+\(\s*\)\s*,?/i', $key_definitions[0][$array_index])) {
									$create_table_statement = preg_replace('/\s*,?[^,]+?key\s+[^(]+\(\s*\)\s*/i', '', $create_table_statement);
									$key_definitions[0][$array_index] = '';
								}
							}
						}
					}

					if (!$generated_column_db_info['can_insert_ignore_to_generated_column'] && isset($this->generated_columns_exist_in_the_statement[$this->table_name]) && true === $this->generated_columns_exist_in_the_statement[$this->table_name]) {
						foreach ($reversed_data_type_definition as $key => &$data_type_definition) {
							if (empty($data_type_definition) || 0 === strlen(trim($data_type_definition[0]))) continue;
							if ('GENERATED_ALWAYS_TOKEN' === $key) {
								// if it's not possible to use insert ignore for the generated column even if the sql strict mode has been turned off then first we need to change the generated column to a normal/standard column
								$create_table_statement = substr_replace($create_table_statement, '', $data_type_definition[1], strlen($data_type_definition[0]));
								$data_type_definition[0] = '';
							} elseif (!in_array($key, array('DATA_TYPE_TOKEN', 'COMMENT_TOKEN'))) {
								// since "comments" and "generated always as" could contain a string of these keywords (virtual/stored/persistent), so we can't use preg_replace and $generated_column['column_definition'] var as the subject to replace the keyword to an empty string, but instead we lookup the keyword through column_data_type_definition that has captured data type definitions
								$replaced_data_type_definition = preg_replace('/\b(?:virtual|stored|persistent)\b/i', '', $data_type_definition[0]);
								$create_table_statement = substr_replace($create_table_statement, $replaced_data_type_definition, $data_type_definition[1], strlen($data_type_definition[0]));
								$data_type_definition[0] = $replaced_data_type_definition;
							}
						}
						// once the create table and also the insert ignore statement for the corresponding table have been executed, we will use alter table statement to change back the columns to STORED type
						// this is the only way to avoid "value specified for generated column is not allowed" error, and I think it is the best we can do for now rather than checking the insert statement for virtual columns and replacing the value with DEFAULT
					}
				} else {
					// generated column is not supported but we found a virtual/stored/persistent column type, so it needs to be changed to a normal/standard column
					// we need to keep the generated column along with its single key index and composite key index so that the select statement on the upper layer of the application which selects the the virtual column does not break the application itself due to unknown column error
					$reversed_data_type_definition = array_reverse((array) $generated_column['column_data_type_definition']);
					foreach ($reversed_data_type_definition as $key => &$data_type_definition) {
						if (empty($data_type_definition) || 0 === strlen(trim($data_type_definition[0]))) continue;
						if ('GENERATED_ALWAYS_TOKEN' === $key) {
							$create_table_statement = substr_replace($create_table_statement, '', $data_type_definition[1], strlen($data_type_definition[0]));
							$data_type_definition[0] = '';
						} elseif (!in_array($key, array('DATA_TYPE_TOKEN', 'COMMENT_TOKEN'))) {
							$replaced_data_type_definition = preg_replace('/\b(?:virtual|stored|persistent)\b/i', '', $data_type_definition[0]);
							$create_table_statement = substr_replace($create_table_statement, $replaced_data_type_definition, $data_type_definition[1], strlen($data_type_definition[0]));
							$data_type_definition[0] = $replaced_data_type_definition;
						}
					}
				}
			}
		}

		$updraftplus->log($logline);
		$updraftplus->log($print_line, 'notice-restore');
		// If this is a non wp table we don't want to replace our temp prefix with the final prefix, we need to drop our prefix
		if ($non_wp_table) {
			$this->original_table_name = UpdraftPlus_Manipulation_Functions::str_replace_once($this->import_table_prefix, '', $this->new_table_name);
		} else {
			$this->original_table_name = UpdraftPlus_Manipulation_Functions::str_replace_once($this->import_table_prefix, $this->final_import_table_prefix, $this->new_table_name);
		}
		$this->restoring_table = $this->new_table_name;
		if ($charset_change_message) $updraftplus->log($charset_change_message, 'notice-restore');
		if ($constraint_change_message) $updraftplus->log($constraint_change_message, 'notice-restore');
		if ($collate_change_message) $updraftplus->log($collate_change_message, 'notice-restore');
		if ($engine_change_message) $updraftplus->log($engine_change_message, 'notice-restore');
		return $create_table_statement;
	}

	/**
	 * Callback function that will be triggered when the script execution ends. Reset log_bin_trust_function_creators variable's value to its original value
	 */
	public function on_shutdown() {

		global $updraftplus;

		if (!empty($this->stored_routine_supported) && is_array($this->stored_routine_supported) && $this->stored_routine_supported['is_binary_logging_enabled']) {
			// if this condition is met, it means that db server binary logging is enabled and the value of the log_bin_trust_function_system_variable has previously been set to ON (1), and now the value must be changed back to what it originally was
			if (isset($this->continuation_data['old_log_bin_trust_function_creators'])) {
				$old_log_bin_trust_function_creators = $this->continuation_data['old_log_bin_trust_function_creators'];
			} else {
				$old_log_bin_trust_function_creators = $updraftplus->jobdata_get('old_log_bin_trust_function_creators');
			}
			if (is_string($old_log_bin_trust_function_creators) && '' !== $old_log_bin_trust_function_creators) $this->set_log_bin_trust_function_creators($old_log_bin_trust_function_creators);
		}
	}

	/**
	 * Restore the database backup
	 *
	 * @param  string $working_dir           specify working directory
	 * @param  string $working_dir_localpath specify working local directory
	 * @param  string $import_table_prefix   table prefix to use
	 * @return boolean|WP_Error
	 */
	private function restore_backup_db($working_dir, $working_dir_localpath, $import_table_prefix) {

		global $updraftplus;
	
		do_action('updraftplus_restore_db_pre');

		// This is now a legacy option (at least on the front end), so we should not see it much
		$this->prior_upload_path = get_option('upload_path');

		// There is a file backup.db(.gz) inside the working directory

		// The 'off' check is for badly configured setups - http://wordpress.org/support/topic/plugin-wp-super-cache-warning-php-safe-mode-enabled-but-safe-mode-is-off
		// @codingStandardsIgnoreLine
		if (@ini_get('safe_mode') && 'off' != strtolower(@ini_get('safe_mode'))) {
			$updraftplus->log(__('Warning: PHP safe_mode is active on your server. Timeouts are much more likely. If these happen, then you will need to manually restore the file via phpMyAdmin or another method.', 'updraftplus'), 'notice-restore');
		}

		$db_basename = 'backup.db.gz';
		if (!empty($this->ud_foreign)) {
			$plugins = apply_filters('updraftplus_accept_archivename', array());

			if (empty($plugins[$this->ud_foreign])) return new WP_Error('unknown', sprintf(__('Backup created by unknown source (%s) - cannot be restored.', 'updraftplus'), $this->ud_foreign));

			if (!file_exists($working_dir_localpath.'/'.$db_basename) && file_exists($working_dir_localpath.'/backup.db')) {
				$db_basename = 'backup.db';
			} elseif (!file_exists($working_dir_localpath.'/'.$db_basename) && file_exists($working_dir_localpath.'/backup.db.bz2')) {
				$db_basename = 'backup.db.bz2';
			}

			if (!file_exists($working_dir_localpath.'/'.$db_basename)) {
				$separatedb = empty($plugins[$this->ud_foreign]['separatedb']) ? false : true;
				$filtered_db_name = apply_filters('updraftplus_foreign_dbfilename', false, $this->ud_foreign, $this->ud_backup_set, $working_dir_localpath, $separatedb);
				if (is_string($filtered_db_name)) $db_basename = $filtered_db_name;
			}
		}

		// wp_filesystem has no gzopen method, so we switch to using the local filesystem (which is harmless, since we are performing read-only operations)
		if (false === $db_basename || !is_readable($working_dir_localpath.'/'.$db_basename)) return new WP_Error('dbopen_failed', __('Failed to find database file', 'updraftplus')." ($working_dir/".$db_basename.")");

		global $wpdb, $updraftplus;
		
		$this->skin->feedback($this->strings['restore_database']);

		$is_plain = ('.db' == substr($db_basename, -3, 3));
		$is_bz2 = ('.db.bz2' == substr($db_basename, -7, 7));

		// Read-only access: don't need to go through WP_Filesystem
		if ($is_plain) {
			$dbhandle = fopen($working_dir_localpath.'/'.$db_basename, 'r');
		} elseif ($is_bz2) {
			if (!function_exists('bzopen')) {
				$updraftplus->log_e("Your web server's PHP installation has these functions disabled: %s.", 'bzopen');
				$updraftplus->log_e('Your hosting company must enable these functions before %s can work.', __('restoration', 'updraftplus'));
			}
			$dbhandle = bzopen($working_dir_localpath.'/'.$db_basename, 'r');
		} else {
			$dbhandle = gzopen($working_dir_localpath.'/'.$db_basename, 'r');
		}
		if (!$dbhandle) return new WP_Error('dbopen_failed', __('Failed to open database file', 'updraftplus'));

		$this->line = 0;

		if ($this->use_wpdb()) {
			$updraftplus->log_e('Database access: Direct MySQL access is not available, so we are falling back to wpdb (this will be considerably slower)');
		} else {
			$updraftplus->log("Using direct MySQL access; value of use_mysqli is: ".($this->use_mysqli ? '1' : '0'));
			if ($this->use_mysqli) {
				@mysqli_query($this->mysql_dbh, 'SET SESSION query_cache_type = OFF;');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			} else {
				// @codingStandardsIgnoreLine
				@mysql_query('SET SESSION query_cache_type = OFF;', $this->mysql_dbh);
			}
		}

		UpdraftPlus_Database_Utility::set_sql_mode(array('NO_AUTO_VALUE_ON_ZERO'), array(), $this->use_wpdb() ? null : $this->mysql_dbh);

		// register restoration shutdown event so that we can set some mysql's global variable back to its original value
		register_shutdown_function(array($this, 'on_shutdown'));

		// Find the supported engines - in case the dump had something else (case seen: saved from MariaDB with engine Aria; imported into plain MySQL without)
		$supported_engines = array_change_key_case((array) $wpdb->get_results("SHOW ENGINES", OBJECT_K));
		$supported_charsets = array_change_key_case((array) $wpdb->get_results("SHOW CHARACTER SET", OBJECT_K));
		$supported_collations = array_change_key_case((array) $wpdb->get_results('SHOW COLLATION', OBJECT_K));

		$this->table_engine = '';
		
		$this->errors = 0;
		$this->statements_run = 0;
		$this->insert_statements_run = 0;
		$this->tables_created = 0;

		$sql_line = "";
		$sql_type = -1;

		$this->start_time = microtime(true);

		$this->old_siteurl = '';
		$this->old_home = '';
		$this->old_content = '';
		$this->old_uploads = '';
		$this->old_table_prefix = (defined('UPDRAFTPLUS_OVERRIDE_IMPORT_PREFIX') && UPDRAFTPLUS_OVERRIDE_IMPORT_PREFIX) ? UPDRAFTPLUS_OVERRIDE_IMPORT_PREFIX : null;
		$old_siteinfo = array();
		$gathering_siteinfo = true;

		$this->create_forbidden = false;
		$this->drop_forbidden = false;
		$this->lock_forbidden = false;
		$this->rename_forbidden = false;
		
		// This will get flipped if positive success is confirmed
		$this->triggers_forbidden = true;

		$this->last_error = '';
		$random_table_name = 'updraft_tmp_'.rand(0, 9999999).md5(microtime(true));
		$renamed_random_table_name = 'updraft_tmp_'.rand(0, 9999999).md5(microtime(true));
		$last_created_generated_columns_table = '';

		// The only purpose in funnelling queries directly here is to be able to get the error number
		if ($this->use_wpdb()) {
		
			$req = $wpdb->query("CREATE TABLE $random_table_name (test INT)");
			// WPDB, for several query types, returns the number of rows changed; in distinction from an error, indicated by (bool)false
			if (0 === $req) $req = true;
			if (!$req) $this->last_error = $wpdb->last_error;
			$this->last_error_no = false;

			if ($req && false !== $wpdb->query("CREATE TRIGGER test_trigger BEFORE INSERT ON $random_table_name FOR EACH ROW SET @sum = @sum + NEW.test")) $this->triggers_forbidden = false;

		} else {
		
			if ($this->use_mysqli) {
				$req = mysqli_query($this->mysql_dbh, "CREATE TABLE $random_table_name (test INT)");
			} else {
				// @codingStandardsIgnoreLine
				$req = mysql_unbuffered_query("CREATE TABLE $random_table_name (test INT)", $this->mysql_dbh);
			}
			
			if (!$req) {
				// @codingStandardsIgnoreLine
				$this->last_error = $this->use_mysqli ? mysqli_error($this->mysql_dbh) : mysql_error($this->mysql_dbh);
				// @codingStandardsIgnoreLine
				$this->last_error_no = $this->use_mysqli ? mysqli_errno($this->mysql_dbh) : mysql_errno($this->mysql_dbh);
			} else {
				if ($this->use_mysqli) {
					$reqtrigger = mysqli_query($this->mysql_dbh, "CREATE TRIGGER test_trigger BEFORE INSERT ON $random_table_name FOR EACH ROW SET @sum = @sum + NEW.test");
				} else {
					// @codingStandardsIgnoreLine
					$reqtrigger = mysql_unbuffered_query("CREATE TRIGGER test_trigger BEFORE INSERT ON $random_table_name FOR EACH ROW SET @sum = @sum + NEW.test", $this->mysql_dbh);
				}
				if ($reqtrigger) $this->triggers_forbidden = false;
			}
		}

		if (!$req && ($this->use_wpdb() || 1142 === $this->last_error_no)) {
			$this->create_forbidden = true;
			// If we can't create, then there's no point dropping
			$this->drop_forbidden = true;

			// abort dummy restore process
			if ($this->is_dummy_db_restore) {
				return new WP_Error('abort_dummy_restore', __('Your database user does not have permission to drop tables', 'updraftplus'));
			}
			
			$updraftplus->log(__('Your database user does not have permission to create tables. We will attempt to restore by simply emptying the tables; this should work as long as a) you are restoring from a WordPress version with the same database structure, and b) Your imported database does not contain any tables which are not already present on the importing site.', 'updraftplus'), 'warning-restore');
			
			$updraftplus->log('Your database user does not have permission to create tables. We will attempt to restore by simply emptying the tables; this should work as long as a) you are restoring from a WordPress version with the same database structure, and b) Your imported database does not contain any tables which are not already present on the importing site.');
			
			$updraftplus->log('Error was: '.$this->last_error.' ('.$this->last_error_no.')');
		} else {

			if (1142 === $this->rename_table($random_table_name, $renamed_random_table_name)) {
				$this->rename_forbidden = true;
				$updraftplus->log('Database user has no permission to rename tables - restoration will be non-atomic', 'warning-restore');
			} else {
				// We renamed the table so update the $random_table_name
				$random_table_name = $renamed_random_table_name;
			}
		
			if (1142 === $this->lock_table($random_table_name)) {
				$this->lock_forbidden = true;
				$updraftplus->log("Database user has no permission to lock tables - will not lock after CREATE", "warning-restore");
			}
		
			if ($this->use_wpdb()) {
				$req = $wpdb->query("DROP TABLE $random_table_name");
				// WPDB, for several query types, returns the number of rows changed; in distinction from an error, indicated by (bool)false
				if (0 === $req) {
					$req = true;
				}
				if (!$req) $this->last_error = $wpdb->last_error;
				$this->last_error_no = false;
			} else {
				if ($this->use_mysqli) {
					$req = mysqli_query($this->mysql_dbh, "DROP TABLE $random_table_name");
				} else {
					// @codingStandardsIgnoreLine
					$req = mysql_unbuffered_query("DROP TABLE $random_table_name", $this->mysql_dbh);
				}
				if (!$req) {
					// @codingStandardsIgnoreLine
					$this->last_error = ($this->use_mysqli) ? mysqli_error($this->mysql_dbh) : mysql_error($this->mysql_dbh);
					// @codingStandardsIgnoreLine
					$this->last_error_no = ($this->use_mysqli) ? mysqli_errno($this->mysql_dbh) : mysql_errno($this->mysql_dbh);
				}
			}
			if (!$req && ($this->use_wpdb() || 1142 === $this->last_error_no)) {
				$this->drop_forbidden = true;
				$this->rename_forbidden = true;

				// abort dummy restore process
				if ($this->is_dummy_db_restore) {
					return new WP_Error('abort_dummy_restore', __('Your database user does not have permission to drop tables', 'updraftplus'));
				}

				$updraftplus->log(sprintf('Your database user does not have permission to drop tables. We will attempt to restore by simply emptying the tables; this should work as long as you are restoring from a WordPress version with the same database structure (%s)', '('.$this->last_error.', '.$this->last_error_no.')'));
				
				$updraftplus->log(sprintf(__('Your database user does not have permission to drop tables. We will attempt to restore by simply emptying the tables; this should work as long as you are restoring from a WordPress version with the same database structure (%s)', 'updraftplus'), '('.$this->last_error.', '.$this->last_error_no.')'), 'warning-restore');
				
			}
		}

		if (defined('UPDRAFTPLUS_ATOMIC_RESTORE_DISABLED') && UPDRAFTPLUS_ATOMIC_RESTORE_DISABLED) {
			$updraftplus->log('Atomic restore disabled by constant UPDRAFTPLUS_ATOMIC_RESTORE_DISABLED - restoration will be non-atomic', 'warning-restore');
			$this->rename_forbidden = true;
		}

		// If this is not a dummy restore or not importing a single site into a multisite and we can rename and drop tables then change the import prefix and proceed with atomic restore
		if (!$this->rename_forbidden && !$this->is_dummy_db_restore && !isset($this->restore_options['updraftplus_migrate_blogname']) && empty($this->ud_foreign)) {
			add_filter('updraftplus_restore_table_prefix', array($this, 'updraftplus_random_restore_table_prefix'));
			$import_table_prefix = isset($this->continuation_data['temp_import_table_prefix']) ? $this->continuation_data['temp_import_table_prefix'] : apply_filters('updraftplus_restore_table_prefix', $this->final_import_table_prefix);
			$this->import_table_prefix = $import_table_prefix;
			$updraftplus->jobdata_set('temp_import_table_prefix', $this->import_table_prefix);
		} else {
			$this->rename_forbidden = true;
		}

		$this->restoring_table = '';

		$this->max_allowed_packet = $updraftplus->max_packet_size();

		$updraftplus->log('Entering maintenance mode');
		$this->maintenance_mode(true);

		$delimiter = ';';
		$delimiter_regex = ';';
		$virtual_columns_exist = false;

		$old_log_bin_trust_function_creators = null;

		if (is_null($this->stored_routine_supported)) $this->stored_routine_supported = UpdraftPlus_Database_Utility::is_stored_routine_supported();
		if (is_wp_error($this->stored_routine_supported)) $updraftplus->log('is_stored_routine_supported(): '.$this->stored_routine_supported->get_error_message());

		// N.B. There is no such function as bzeof() - we have to detect that another way
		while (($is_plain && !feof($dbhandle)) || (!$is_plain && (($is_bz2) || (!$is_bz2 && !gzeof($dbhandle))))) {
			// Up to 1Mb
			if ($is_plain) {
				$buffer = rtrim(fgets($dbhandle, 1048576));
			} elseif ($is_bz2) {
				if (!isset($bz2_buffer)) $bz2_buffer = '';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
				$buffer = '';
				if (strlen($bz2_buffer) < 524288) $bz2_buffer .= bzread($dbhandle, 1048576);
				if (bzerrno($dbhandle) !== 0) {
					$updraftplus->log("bz2 error: ".bzerrstr($dbhandle)." (code: ".bzerrno($dbhandle).")");
					break;
				}
				if (false !== $bz2_buffer && '' !== $bz2_buffer) {
					if (false !== ($p = strpos($bz2_buffer, "\n"))) {
						$buffer .= substr($bz2_buffer, 0, $p+1);
						$bz2_buffer = substr($bz2_buffer, $p+1);
					} else {
						$buffer .= $bz2_buffer;
						$bz2_buffer = '';
					}
				} else {
					break;
				}
				$buffer = rtrim($buffer);
			} else {
				$buffer = rtrim(gzgets($dbhandle, 1048576));
			}

			// Discard comments
			if (empty($buffer) || '#' == substr($buffer, 0, 1) || preg_match('/^--(\s|$)/', substr($buffer, 0, 3))) {
				if ('' == $this->old_siteurl && preg_match('/^\# Backup of: (http(.*))$/', $buffer, $matches)) {
					$this->old_siteurl = untrailingslashit($matches[1]);
					$updraftplus->log("Backup of: ".$this->old_siteurl);
					$updraftplus->log(sprintf(__('Backup of: %s', 'updraftplus'), $this->old_siteurl), 'notice-restore', 'backup-of');
					do_action('updraftplus_restore_db_record_old_siteurl', $this->old_siteurl);

					$this->save_configuration_bundle();

				} elseif (false === $this->created_by_version && preg_match('/^\# Created by UpdraftPlus version ([\d\.]+)/', $buffer, $matches)) {
					$this->created_by_version = trim($matches[1]);
					$updraftplus->log(__('Backup created by:', 'updraftplus').' '.$this->created_by_version, 'notice-restore', 'created-by');
					$updraftplus->log('Backup created by: '.$this->created_by_version);
				} elseif ('' == $this->old_home && preg_match('/^\# Home URL: (http(.*))$/', $buffer, $matches)) {
					$this->old_home = untrailingslashit($matches[1]);
					if ($this->old_siteurl && $this->old_home != $this->old_siteurl) {
						$updraftplus->log(__('Site home:', 'updraftplus').' '.$this->old_home, 'notice-restore', 'site-home');
						$updraftplus->log('Site home: '.$this->old_home);
					}
					do_action('updraftplus_restore_db_record_old_home', $this->old_home);
				} elseif ('' == $this->old_content && preg_match('/^\# Content URL: (http(.*))$/', $buffer, $matches)) {
					$this->old_content = untrailingslashit($matches[1]);
					$updraftplus->log(__('Content URL:', 'updraftplus').' '.$this->old_content, 'notice-restore', 'content-url');
					$updraftplus->log('Content URL: '.$this->old_content);
					do_action('updraftplus_restore_db_record_old_content', $this->old_content);
				} elseif ('' == $this->old_uploads && preg_match('/^\# Uploads URL: (http(.*))$/', $buffer, $matches)) {
					$this->old_uploads = untrailingslashit($matches[1]);
					$updraftplus->log(__('Uploads URL:', 'updraftplus').' '.$this->old_uploads, 'notice-restore', 'uploads-url');
					$updraftplus->log('Uploads URL: '.$this->old_uploads);
					do_action('updraftplus_restore_db_record_old_uploads', $this->old_uploads);
				} elseif (null === $this->old_table_prefix && (preg_match('/^\# Table prefix: ?(\S*)$/', $buffer, $matches) || preg_match('/^-- Table Prefix: ?(\S*)$/i', $buffer, $matches))) {
					// We also support backwpup style:
					// -- Table Prefix: wp_
					$this->old_table_prefix = $matches[1];
					$updraftplus->log(__('Old table prefix:', 'updraftplus').' '.$this->old_table_prefix, 'notice-restore', 'old-table-prefix');
					$updraftplus->log("Old table prefix: ".$this->old_table_prefix);
				} elseif (preg_match('/^\# Skipped tables: (.*)$/', $buffer, $matches)) {
					$updraftplus->log(__('Skipped tables:', 'updraftplus').' '.$matches[1], 'notice-restore', 'skipped-tables');
					$updraftplus->log("Skipped tables: ".$matches[1]);
				} elseif ($gathering_siteinfo && preg_match('/^\# Site info: (\S+)$/', $buffer, $matches)) {
					if ('end' == $matches[1]) {
						$gathering_siteinfo = false;
						// Sanity checks
						if (isset($old_siteinfo['multisite']) && !$old_siteinfo['multisite'] && is_multisite()) {
							if (!class_exists('UpdraftPlusAddOn_MultiSite') || !class_exists('UpdraftPlus_Addons_Migrator')) {
								return new WP_Error('missing_addons', sprintf(__('To import an ordinary WordPress site into a multisite installation requires %s.', 'updraftplus'), 'UpdraftPlus Premium'));
							}
						}
					} elseif (preg_match('/^([^=]+)=(.*)$/', $matches[1], $kvmatches)) {
						$key = $kvmatches[1];
						$val = $kvmatches[2];
						$updraftplus->log(__('Site information:', 'updraftplus')." $key = $val", 'notice-restore', 'site-information');
						$updraftplus->log("Site information: $key=$val");
						$old_siteinfo[$key] = $val;
						if ('multisite' == $key) {
							$this->ud_backup_is_multisite = ($val) ? 1 : 0;
						}
					}
				}
				continue;
			}

			// Detect INSERT and various other commands early, so that we can split or combine them if necessary
			if (preg_match('/^\s*(insert\s\s*into(?:\s*`(.+?)`|[^\(]+)(?:\s*\(.+?\))?\s*(?:values|\())/i', $sql_line.$buffer, $matches)) {
				// https://regex101.com/r/zrQquQ/2
				$this->table_name = $matches[2];
				$sql_type = 3;
				$insert_prefix = $matches[1];

				// if the current table is a generated columns table, that means at this stage the table creation is being postponed and the block of code below will get executed first to filter the create statement
				if (!empty($this->generated_columns[$this->table_name])) {
					// parse the generated columns insert statement so that later we can instantly retrieve the information needed when creating the table
					$this->generated_columns_exist_in_the_statement[$this->table_name] = UpdraftPlus_Database_Utility::generated_columns_exist_in_the_statement($sql_line.$buffer, $this->generated_columns[$this->table_name]['column_names']);
					if ($this->table_name != $last_created_generated_columns_table) {
						$create_statement = $this->prepare_create_table($this->generated_columns[$this->table_name]['create_statement'], $import_table_prefix, $supported_engines, $supported_charsets, $supported_collations);
						// after getting the filtered create statement, continue with the table creation
						$do_exec = $this->sql_exec($create_statement, 2, $import_table_prefix);
						$last_created_generated_columns_table = $this->table_name;
						if (is_wp_error($do_exec)) return $do_exec;
					}
					// on MySQL 5.7.x, we could get an error "the value specified for generated column is not allowed", disabling strict mode doesn't work, adding insert ignore doesn't work either
					// disabling strict mode works fine on MariaDB, it may be good if we can strengthen the insert statement by adding ignore keyword into it
					$sql_line = preg_replace('/^(\s*insert\s\s*into)(.+)$/is', 'insert ignore into$2', $sql_line);
					$insert_prefix = preg_replace('/^(\s*insert\s\s*into)(.+)$/is', 'insert ignore into$2', $matches[0]);
				}

			} elseif (preg_match('/^\s*delimiter (\S+)\s*$/i', $sql_line.$buffer, $matches)) {
				// This also needs processing early so that the correct delimiter is used a few lines down
				$sql_type = 10;
				$delimiter = $matches[1];
				// Obviously, what is supported here is quite limited
				$delimiter_regex = str_replace(array('$', '#', '/'), array('\$', '\#', '\/'), $delimiter);
			} elseif (preg_match('/^\s*create trigger /i', $sql_line.$buffer)) {
				$sql_type = 9;
				$buffer = $buffer."\n";
			} elseif (preg_match("/^\s*CREATE\s\s*(?:DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|'.{1,17}'@'[^\s]+'\s*|[^\s]+?\s))?(?:AGGREGATE\s\s*)?(?:PROCEDURE|FUNCTION)((?:\s\s*[^\(`]+|\s*`(?:[^`]|``)+`))\s*\(/is", $sql_line.$buffer)) {
				$sql_type = 12;
				$buffer = $buffer."\n"; // need to do this so that the functions/procedures which have double dash and/or shell comment style (i.e -- comment, # comment) doesn't block the rest of the code in the routines body and also because we want to keep the routines as it is or in multiline (in a form that people prefer) so they who will edit it later don't get surprised by the look of it in a single line/one line
			} elseif (preg_match('/^\s*create table \`?([^\`\(]*)\`?\s*\(/i', $sql_line.$buffer, $matches)) {
				$sql_type = 2;
				$this->table_name = $matches[1];
				// check whether the column definition is a generated column
				$generated_column_info = UpdraftPlus_Database_Utility::get_generated_column_info($buffer, strlen($sql_line));
				if ($generated_column_info) {
					if (!isset($this->generated_columns[$this->table_name])) $this->generated_columns[$this->table_name] = array();
					if (!$virtual_columns_exist) $virtual_columns_exist = $generated_column_info['is_virtual'];
					$this->generated_columns[$this->table_name]['columns'][] = $generated_column_info;
					$this->generated_columns[$this->table_name]['column_names'][] = $generated_column_info['column_name'];
				}
				if (!empty($this->generated_columns[$this->table_name]) && substr($sql_line.$buffer, -strlen($delimiter), strlen($delimiter)) == $delimiter) {
					$this->generated_columns[$this->table_name]['create_statement'] = $sql_line.$buffer;
					$this->generated_columns[$this->table_name]['virtual_columns_exist'] = $virtual_columns_exist;
					$virtual_columns_exist = false;
				}
			}

			// Deal with case where adding this line will take us over the MySQL max_allowed_packet limit - must split, if we can (if it looks like consecutive rows)
			// Allow a 100-byte margin for error (including searching/replacing table prefix)
			if (3 == $sql_type && $sql_line && strlen($sql_line.$buffer) > ($this->max_allowed_packet - 100) && preg_match('/,\s*$/', $sql_line) && preg_match('/^\s*\(/', $buffer)) {
				
				if ($this->table_should_be_skipped($this->table_name)) {
					// Reset - we need the insert prefix so following lines get detected correctly
					$sql_line = $insert_prefix." ";
					continue;
				}
				
				// Remove the final comma; replace with delimiter
				$sql_line = substr(rtrim($sql_line), 0, strlen($sql_line)-1).';';
				if ($import_table_prefix != $this->old_table_prefix) {
					if ('' != $this->old_table_prefix) {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $sql_line);
					} else {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->table_prefix.$this->table_name, $sql_line);
					}
				}
				// Run the SQL command; then set up for the next one.
				$this->line++;
				$updraftplus->log(__("Split line to avoid exceeding maximum packet size", 'updraftplus')." (".strlen($sql_line)." + ".strlen($buffer)." : ".$this->max_allowed_packet.")", 'notice-restore');
				$updraftplus->log("Split line to avoid exceeding maximum packet size (".strlen($sql_line)." + ".strlen($buffer)." : ".$this->max_allowed_packet.")");
				$do_exec = $this->sql_exec($sql_line, $sql_type, $import_table_prefix);
				if (is_wp_error($do_exec)) return $do_exec;
				// Reset, then carry on
				$sql_line = $insert_prefix." ";
			}

			$sql_line .= (9 == $sql_type && '' != $sql_line) ? ' '.$buffer : $buffer;
			
			// Do we have a complete line yet? We used to just test the final character for ';' here (up to 1.8.12), but that was too unsophisticated
			// From 1.16.16, we don't hard-code the delimiter here, and we also add the knowledge that CREATE TRIGGER statements finish with END
			// The aim, in order to get something valid to execute, is to get something like this, with the entire trigger on a single line:
			//
			// $wpdb->query("CREATE TRIGGER `civicrm_acl_after_insert` AFTER INSERT ON `civicrm_acl` FOR EACH ROW BEGIN IF (@civicrm_disable_logging IS NULL OR @civicrm_disable_logging = 0 ) THEN INSERT INTO log_civicrm_acl (`id`, `name`, `deny`, `entity_table`, `entity_id`, `operation`, `object_table`, `object_id`, `acl_table`, `acl_id`, `is_active`, log_conn_id, log_user_id, log_action) VALUES ( NEW.`id`, NEW.`name`, NEW.`deny`, NEW.`entity_table`, NEW.`entity_id`, NEW.`operation`, NEW.`object_table`, NEW.`object_id`, NEW.`acl_table`, NEW.`acl_id`, NEW.`is_active`, COALESCE(@uniqueID, LEFT(CONCAT('c_', unix_timestamp()/3600, CONNECTION_ID()), 17)), @civicrm_user_id, 'insert'); END IF; END"));

			if ((3 == $sql_type && !preg_match('/\)\s*'.$delimiter_regex.'$/', substr($sql_line, -5, 5))) || (!in_array($sql_type, array(3, 9, 10, 12)) && substr($sql_line, -strlen($delimiter), strlen($delimiter)) != $delimiter) || (9 == $sql_type && !preg_match('/(?:END)?\s*'.$delimiter_regex.'\s*$/', $sql_line))) continue;

			$this->line++;

			// We now have a complete line - process it

			if (3 == $sql_type && $sql_line && strlen($sql_line) > $this->max_allowed_packet) {
				$this->log_oversized_packet($sql_line);
				// Reset
				$sql_line = '';
				$sql_type = -1;
				// If this is the very first SQL line of the options table, we need to bail; it's essential
				if (0 == $this->insert_statements_run && $this->restoring_table && $this->restoring_table == $import_table_prefix.'options') {
					$updraftplus->log("Leaving maintenance mode");
					$this->maintenance_mode(false);
					return new WP_Error('initial_db_error', sprintf(__('An error occurred on the first %s command - aborting run', 'updraftplus'), 'INSERT (options)'));
				}
				continue;
			}

			// The timed overhead of this is negligible
			if (preg_match('/^\s*drop table (if exists )?\`?([^\`]*)\`?\s*'.$delimiter_regex.'/i', $sql_line, $matches)) {
				$sql_type = 1;

				if (!$this->printed_new_table_prefix) {
					$import_table_prefix = $this->pre_sql_actions($import_table_prefix);
					if (false === $import_table_prefix || is_wp_error($import_table_prefix)) return $import_table_prefix;
					$this->printed_new_table_prefix = true;
				}

				$this->table_name = $matches[2];
				if ($this->table_should_be_skipped($this->table_name)) {
					// Reset
					$sql_line = '';
					$sql_type = -1;
					continue;
				}
				
				// Legacy, less reliable - in case it was not caught before
				if (null === $this->old_table_prefix && preg_match('/^([a-z0-9]+)_.*$/i', $this->table_name, $tmatches)) {
					$this->old_table_prefix = $tmatches[1].'_';
					$updraftplus->log(__('Old table prefix:', 'updraftplus').' '.$this->old_table_prefix, 'notice-restore', 'old-table-prefix');
					$updraftplus->log("Old table prefix (detected from first table): ".$this->old_table_prefix);
				}

				$this->new_table_name = $this->old_table_prefix ? UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $this->table_name) : $this->table_name;

				$non_wp_table = false;
				
				// if we have a different prefix but the table name has not changed after the replace then we are dealing with a table that does not use the WordPress table prefix, in order for an Atmoic restore to work on this table we need to attach our temporary prefix
				if (!$this->rename_forbidden && $this->old_table_prefix && $this->new_table_name == $this->table_name) {
					$non_wp_table = true;
					$this->new_table_name = $import_table_prefix.$this->table_name;
				}

				if ($import_table_prefix != $this->old_table_prefix) {
					if ('' === $this->old_table_prefix || $non_wp_table) {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->new_table_name, $sql_line);
					} else {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $sql_line);
					}
				}
				
				if (empty($matches[1])) {
					// Seen with some foreign backups
					$sql_line = preg_replace('/drop table/i', 'drop table if exists', $sql_line, 1);
				}
				
				$this->tables_been_dropped[] = $this->new_table_name;

			} elseif (preg_match('/^\s*create table \`?([^\`\(]*)\`?\s*\(/i', $sql_line, $matches)) {

				$sql_type = 2;
				$this->insert_statements_run = 0;
				$this->table_name = $matches[1];

				// regardless of whether or not the table should be skipped, the table creation should also be postponed if the table contains one or more generated columns
				if ($this->table_should_be_skipped($this->table_name) || !empty($this->generated_columns[$this->table_name])) {
					// Reset
					$sql_line = '';
					$sql_type = -1;
					continue;
				}

				if (!$this->printed_new_table_prefix) {
					$import_table_prefix = $this->pre_sql_actions($import_table_prefix);
					if (false === $import_table_prefix || is_wp_error($import_table_prefix)) return $import_table_prefix;
					$this->printed_new_table_prefix = true;
				}

				$sql_line = $this->prepare_create_table($sql_line, $import_table_prefix, $supported_engines, $supported_charsets, $supported_collations);

			} elseif (preg_match('/^\s*insert(?:\s\s*ignore)?\s\s*into(?:\s*`(.+?)`|[^\(]+)(?:\s*\(.+?\))?\s*(?:values|\()/i', $sql_line, $matches)) {
				$sql_type = 3;
				$this->table_name = $matches[1];
				if ($this->table_should_be_skipped($this->table_name)) {
					// Reset
					$sql_line = '';
					$sql_type = -1;
					continue;
				}
				
				$non_wp_table = false;

				$this->new_table_name = $this->old_table_prefix ? UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $this->table_name) : $this->table_name;
				
				// if we have a different prefix but the table name has not changed after the replace then we are dealing with a table that does not use the WordPress table prefix, in order for an Atmoic restore to work on this table we need to attach our temporary prefix
				if (!$this->rename_forbidden && $this->old_table_prefix && $this->new_table_name == $this->table_name) {
					$non_wp_table = true;
					$this->new_table_name = $import_table_prefix.$this->table_name;
				}

				// If this is set then we have disabled atomic restores for this table so we need to make sure we are using the correct prefix when inserting the data
				$temp_insert_table_prefix = $this->disable_atomic_on_current_table ? $this->final_import_table_prefix : $import_table_prefix;
				if ($temp_insert_table_prefix != $this->old_table_prefix) {
					if ('' === $this->old_table_prefix || $non_wp_table) {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->new_table_name, $sql_line);
					} else {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $temp_insert_table_prefix, $sql_line);
					}
				}
			} elseif (preg_match('/^\s*(\/\*\!40000 )?(alter|lock) tables? \`?([^\`\(]*)\`?\s+(write|disable|enable)/i', $sql_line, $matches)) {
				// Only binary mysqldump produces this pattern (LOCK TABLES `table` WRITE, ALTER TABLE `table` (DISABLE|ENABLE) KEYS)
				$sql_type = 4;
				if ($import_table_prefix != $this->old_table_prefix) {
					if ('' != $this->old_table_prefix) {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $sql_line);
					} else {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->new_table_name, $sql_line);
					}
				}
			} elseif (preg_match('/^(un)?lock tables/i', $sql_line)) {
				// BackWPup produces these
				$sql_type = 15;
			} elseif (preg_match('/^(create|drop) database /i', $sql_line)) {
				// WPB2D produces these, as do some phpMyAdmin dumps
				$sql_type = 6;
			} elseif (preg_match('/^use /i', $sql_line)) {
				// WPB2D produces these, as do some phpMyAdmin dumps
				$sql_type = 7;
			} elseif (preg_match('#^\s*/\*\!40\d+ (SET NAMES) (.*)\*\/#i', $sql_line, $smatches)) {
				$sql_type = 8;
				$charset = rtrim($smatches[2]);
				$connection_charset = $updraftplus->get_connection_charset();
				if ('utf8' === $charset && 'utf8mb4' === $connection_charset) {
					$sql_line = UpdraftPlus_Manipulation_Functions::str_lreplace("SET NAMES $charset", "SET NAMES $connection_charset", $sql_line);
					$updraftplus->log(sprintf(__('Found SET NAMES %s, but changing to %s as suggested by WPDB::determine_charset().', 'updraftplus'), $charset, $connection_charset), 'notice-restore');
					$charset = $connection_charset;
				}
				$this->set_names = $charset;
				if (!isset($supported_charsets[strtolower($charset)])) {
					$sql_line = UpdraftPlus_Manipulation_Functions::str_lreplace($smatches[1]." ".$charset, "SET NAMES ".$this->restore_options['updraft_restorer_charset'], $sql_line);
					$updraftplus->log('SET NAMES: '.sprintf(__('Requested character set (%s) is not present - changing to %s.', 'updraftplus'), esc_html($charset), esc_html($this->restore_options['updraft_restorer_charset'])), 'notice-restore');
				}
			} elseif (preg_match('/^\s*create trigger /i', $sql_line)) {
				$sql_type = 9;
				// If the statement is not yet complete, then continue (to get the next line)
				if (!preg_match('/(?:END)?\s*'.$delimiter_regex.'\s*$/', $sql_line)) continue;
				// If it's a comment then continue;
				if (preg_match('/(?:--|#).+?'.$delimiter_regex.'\s*$/i', $buffer)) continue;
				$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'db', 'data' => array('stage' => 'trigger', 'table' => '')));
				if ($import_table_prefix != $this->old_table_prefix) {
					if ('' != $this->old_table_prefix) {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $sql_line);
					} else {
						$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once($this->table_name, $this->new_table_name, $sql_line);
					}
				}
				if (';' !== $delimiter) {
					$sql_line = preg_replace('/END\s*'.$delimiter_regex.'\s*$/', 'END', $sql_line);
					// handle trigger statement which doesn't include begin and end in the trigger body, and remove the delimiter
					$sql_line = preg_replace('/\s*'.$delimiter_regex.'\s*$/', '', $sql_line);
				}
				if ($this->triggers_forbidden) $updraftplus->log("Database user lacks permission to create triggers; statement will not be executed ($sql_line)");
			} elseif (preg_match('/^\s*drop trigger /i', $sql_line)) {
				// Avoid sending unrecognised delimiters to the SQL server (this only affects backups created outside UD; we use ";;" which is cunningly compatible)
				if (';' !== $delimiter) $sql_line = preg_replace('/'.$delimiter_regex.'\s*$/', '', $sql_line);
			} elseif (preg_match("/^\s*CREATE\s\s*(?:OR\s\s*REPLACE\s\s*)?(?:DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|'.{1,17}'@'[^\s]+'\s*|[^\s]+?\s))?(?:AGGREGATE\s\s*)?(?:PROCEDURE|FUNCTION)((?:\s\s*[^\(`]+|\s*`(?:[^`]|``)+`))\s*\(/is", $sql_line, $routine_matches)) {
				// ^\s*create\s\s*(?:or\s\s*replace\s\s*)?.*?(?:(?:aggregate\s\s*)?function|procedure)\s\s*`(.+)`(?:\s\s*if\s\s*not\s\s*exists\s*|\s*)?\(
				// ^[^'\"]*create[^'\"]*(?:function(?:\s\s*if\s\s*not\s\s*exists)?|procedure)\s*`([^\r\n]+)`
				$sql_type = 12;
				// it's possible that a routine doesn't have BEGIN and END statements in its routine body, unfortunately sometimes it does have the statement separator (;) at the end of the routine statement but sometimes it doesn't
				// if it has the statement separator (;) then we add the statement separator into the regex and the preceding delimiter, if it doesn't have statement separator then check only the delimiter
				if (!preg_match('/END\s*(?:\*\/)?'.$delimiter_regex.'\s*$/is', rtrim($sql_line)) && !preg_match('/\;\s*'.$delimiter_regex.'\s*$/is', rtrim($sql_line)) && !preg_match('/\s*(?:\*\/)?'.$delimiter_regex.'\s*$/is', rtrim($sql_line))) continue;
				// if it's already at the end of the statement check whether it's a comment or not (e.g. SET @VARIABLE = 'the value'; -- the comment END;; or SET @VARIABLE = 'the value'; # the comment END;;;)
				if (preg_match('/(?:--|#).+?END\s*'.$delimiter_regex.'\s*$/i', rtrim($sql_line)) && preg_match('/(?:--|#).+?'.$delimiter_regex.'\s*$/i', rtrim($sql_line))) continue;
				if (is_array($this->stored_routine_supported) && !empty($this->stored_routine_supported) && !is_wp_error($old_log_bin_trust_function_creators)) {
					$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'db', 'data' => array('stage' => 'stored_routine', 'routine_name' => preg_replace('/^`?(.+?)`?$/i', "$1", trim(str_replace('``', '`', $routine_matches[1]))))));
					if ($this->stored_routine_supported['is_binary_logging_enabled'] && !$this->stored_routine_supported['is_function_creators_trusted'] && !isset($this->continuation_data['old_log_bin_trust_function_creators']) && is_null($old_log_bin_trust_function_creators)) {
						// it's a new restoration
						// no matter what the database server is and the priviliges the current DB user has, if the binary logging is enabled, log_bin_trust_function_creators is set to off and DB current, we could end up getting the below error when restoring routines
						// ERROR 1418 (HY000): This function has none of DETERMINISTIC, NO SQL, or READS SQL DATA in its declaration and binary logging is enabled (you *might* want to use the less safe log_bin_trust_function_creators variable)
						// we need to set the log_bin_trust_function_creators "ON" so that the db server will treat all functions as deterministic safe functions.
						// https://mariadb.com/kb/en/library/binary-logging-of-stored-routines/
						// https://dev.mysql.com/doc/refman/8.0/en/stored-programs-logging.html
						// if the DB current user is a non super admin, binary logging is enabled and log_bin_trust_function_creators is set to OFF/0 it will produce this error "(You do not have the SUPER privilege and binary logging is enabled (you *might* want to use the less safe log_bin_trust_function_creators variable)"
						// the log_bin_tust_function_creators variable is a global variable that should only be changed with caution because other plugins may also use it for some purpose, this can lead to an inaccurate value of log_bin_trust_function_creators especially if the restoration failed and the resumption could not be triggered
						$old_log_bin_trust_function_creators = $this->set_log_bin_trust_function_creators('ON');
						if (is_wp_error($old_log_bin_trust_function_creators)) {
							$updraftplus->log('set_log_bin_trust_function_creators(ON): '.$old_log_bin_trust_function_creators);
						} else {
							$updraftplus->log('log_bin_trust_function_creators value has been set to: ON');
							// we also need to store the original value of the log_bin_trust_function_creator variable to UDP jobdata so that if something goes wrong in the restoration, we're stil able in a earlier time to set this global variable back to what it was (register_shutdown_function seems to be the good one)
							$updraftplus->jobdata_set('old_log_bin_trust_function_creators', $old_log_bin_trust_function_creators);
							$updraftplus->log('The original value of log_bin_trust_function_creators variable has been successfully added to UDP jobdata');
						}
					} elseif ($this->stored_routine_supported['is_binary_logging_enabled'] && !$this->stored_routine_supported['is_function_creators_trusted'] && is_null($old_log_bin_trust_function_creators) && isset($this->continuation_data['old_log_bin_trust_function_creators'])) {
						// it's a resumption of the previous run, it can be recognised from the existence of old_log_bin_trust_function_creators index in $continuation_data variable
						$old_log_bin_trust_function_creators = $this->continuation_data['old_log_bin_trust_function_creators'];
						$updraftplus->log('Running a resumption from the previous restoration');
						$this->set_log_bin_trust_function_creators('ON');
						$updraftplus->log('log_bin_trust_function_creators value has been set to: ON');
					}

					// the routine's definer in the backup file could be different with the user and host on the targeted restore site, so we need to replace the user and host information for the definer option with the current user account or remove the DEFINER clause and let the system use the default value (which is current user)
					// replace the user and host with db_user and db_host
					// $sql_line = preg_replace("/^([^'\"]*create[^'\"]*definer\s*=\s*)(?:`.{1,17}`@`[^\s]+`|'.{1,17}'@'[^\s]+')(.+?(?:function(?:\s\s*if\s\s*not\s\s*exists)?|procedure)\s*`)/is", "$1`".DB_USER."`@`".DB_HOST."`$2", $sql_line);
					// remove the DEFINER clause
					$sql_line = preg_replace("/^\s*(CREATE(?:\s\s*OR\s\s*REPLACE)?)\s\s*DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|'.{1,17}'@'[^\s]+'\s*|[^\s]+?\s)((?:AGGREGATE\s\s*)?(?:PROCEDURE|FUNCTION))/is", "$1 $2", $sql_line);

					if (preg_match('/^\s*CREATE(?:\s\s*OR\s\s*REPLACE)?\s\s*(?:DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|\'.{1,17}\'@\'[^\s]+\'\s*|[^\s]+?\s))?PROCEDURE(?:\s*`(?:[^`]|``)+`\s*|\s[^\(]+)(?\'params\'(?:[^()]+|\((?1)*\)))(?:(.*?)COMMENT\s\s*\'[^\']+\'|COMMENT\s\s*\'[^\']+\'(.*?)|(.*?))(?:(.*?)BEGIN|([^\'"]+))/is', $sql_line, $sql_security_matches, PREG_OFFSET_CAPTURE) || preg_match('/^\s*CREATE(?:\s\s*OR\s\s*REPLACE)?\s\s*(?:DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|\'.{1,17}\'@\'[^\s]+\'\s*|[^\s]+?\s))?(?:AGGREGATE\s\s*)?FUNCTION(?:\s*`(?:[^`]|``)+`\s*|\s[^\(]+)(?\'params\'(?:[^()]+|\((?1)*\)))\s*RETURNS\s[\w]+(?:\(.*?\))?\s*(?:CHARSET\s\s*[^\s]+\s\s*)?(?:COLLATE\s\s*[^\s]+\s\s*)?(?:(.*?)COMMENT\s\s*\'[^\']+\'|COMMENT\s\s*\'[^\']+\'(.*?)|(.*?))(?:(.*?)BEGIN|(.*?)RETURN)/is', $sql_line, $sql_security_matches, PREG_OFFSET_CAPTURE)) {
						// replace SQL SECURITY DEFINER and add SQL SECURITY INVOKER
						$is_last_index_replaced = false;
						$sql_security_matches = array_reverse($sql_security_matches);
						foreach ($sql_security_matches as $key => $match) {
							if ((int) $match[1] <= 0 || 'params' === $key) continue;
							$length = strlen($match[0]);
							$match[0] = preg_replace('/SQL\s\s*SECURITY\s\s*(?:DEFINER|INVOKER)/is', ' ', $match[0]);
							if (!$is_last_index_replaced) {
								// $match[0] .= ' SQL SECURITY INVOKER ';
								$match[0] = ' SQL SECURITY INVOKER ' . $match[0];
								$is_last_index_replaced = true;
							}
							$sql_line = substr_replace($sql_line, $match[0], $match[1], max(0, $length));
						}
					}

					// there could be a text/varchar variable declaration in the routine body in which the charset is also being specified in it, this could lead to an error when restoring the routine because of unsupported charset. for example, the declaration for a varchar/text variable in the routine body or the function parameter with the uf8mb4 charset defined but the running DB doesn't support utf8mb4
					// to handle this we first check the routine creation statement and collect all the variable declarations which in relation with text/char variables that contain charset specification and then replace or remove the charset if necessary
					/* e.g:
					create procedure `test`(_user_id varchar(50) CHARSET utf8, _user_passwd varchar(255) CHARSET utf8, success_text text CHARSET utf8mb4, failure_text text CHARSET utf8)
					begin
						declare insert_status int default 0;
						declare _user_id varchar(2049) CHARSET utf8;
						declare _password varchar(255) CHARSET utf8mb4;
						declare _parent_nid bigint;
						declare _uid bigint;
						declare _node varchar(255) CHARSET utf8mb4;
					end ;;
					*/
					if (preg_match_all('/(\s(?:long|medium|tiny)?text\s*(?:\([0-9]+\))?|\s(?:var)?char\s*(?:\([0-9]+\))?).*?charset\s([^;,\)\s]+).*?(?:,|;|\))/is', $sql_line, $charset_matches)) {
						foreach ((array) $charset_matches[2] as $key => $charset) {
							$replaced_charset_declaration = $charset_matches[0][$key];
							if (!empty($charset) && !isset($supported_charsets[strtolower(trim($charset))])) {
								$replaced_charset_declaration = !empty($this->restore_options['updraft_restorer_charset']) ? str_ireplace($charset, $this->restore_options['updraft_restorer_charset'], $replaced_charset_declaration) : str_ireplace(array('charset', $charset), array('', ''), $replaced_charset_declaration);
								$sql_line = str_ireplace($charset_matches[0][$key], $replaced_charset_declaration, $sql_line);
							}
						}
					}
					if (!$this->stored_routine_supported['is_create_or_replace_supported']) {
						// "create or replace" syntax is used by MariaDB only, in case we are restoring from the MariaDB backup file to MySQL database server, we need to remove the "or replace" syntax if any
						$sql_line = preg_replace("/^([^'\"]*)create\s\s*or\s\s*replace([^'\"]*(?:function(?:\s\s*if\s\s*not\s\s*exists)?|procedure)\s*`)/is", "$1create$2", $sql_line);
					}
					if (!$this->stored_routine_supported['is_if_not_exists_function_supported']) {
						// MariaDB supports IF NOT EXISTS syntax after the FUNCTION keyword (e.g create function if not exists function_name), we need to remove it to ensure the syntax compatility with MySQL
						$sql_line = preg_replace("/^([^'\"]*create[^'\"]*function)\s\s*if\s\s*not\s\s*exists(\s*`)/is", "$1$2", $sql_line);
					}
					if (!$this->stored_routine_supported['is_aggregate_function_supported'] && preg_match("/^[^'\"]*create[^'\"]*(?:\baggregate\s\s*function\b)\s*`([^\r\n]+)`/is", $sql_line, $aggregate_matches)) {
						// there's no way that we can make mariadb aggregate function compatible with MySQL, the function itself must contain "FETCH GROUP NEXT ROW" in the routine body which mysql doesn't know what that is
						$aggregate_log = "Function {$aggregate_matches[1]} has been neglected due to the unsupported function type (aggregate)";
						$updraftplus->log($aggregate_log);
						$updraftplus->log($aggregate_log, 'notice-restore');
						$sql_line = ''; // we ignore the routine and move to the next line
						$sql_type = -1;
						continue;
					}
				} else {
					$updraftplus->log("Stored function/procedure {$routine_matches[1]} has been neglected due to the unsupported database routine creation");
					// move to the next line if stored routine is not supported
					$sql_line = '';
					$sql_type = -1;
					continue;
				}
			} elseif (preg_match('/^.*?drop\s\s*(?:function|procedure)\s\s*(?:if\s\s*exists\s\s*)?/i', $sql_line)) {
				$sql_type = 13;
				// if (';' !== $delimiter) $sql_line = preg_replace('/'.$delimiter_regex.'\s*$/', '', $sql_line);
			} elseif (preg_match('/^\s*delimiter (\S+)\s*$/i', $sql_line, $matches)) {
				// Nothing to do here - deliberate no-op (is processed earlier)
				$sql_type = 10;
			} elseif (preg_match('/^CREATE(\s+ALGORITHM=\S+)?(\s+DEFINER=\S+)?(\s+SQL SECURITY (\S+))?\s+VIEW/i', $sql_line, $matches)) {
				$sql_type = 11;
				// remove DEFINER clause from the create view statement and add or replace SQL SECURITY DEFINER with INVOKER
				// https://regex101.com/r/2tOEhe/4/
				$sql_line = preg_replace('/^(\s*CREATE\s\s*(?\'or_replace\'OR\s\s*REPLACE\s\s*)?(?\'algorithm\'ALGORITHM\s*=\s*[^\s]+\s\s*)?)(?\'definer\'DEFINER\s*=\s*(?:`.{1,17}`@`[^\s]+`\s*|\'.{1,17}\'@\'[^\s]+\'\s*|[^\s]+?\s\s*))?(?\'sql_security\'SQL\s\s*SECURITY\s\s*[^\s]+?\s\s*)?(VIEW(?:\s\s*IF\s\s*NOT\s\s*EXISTS)?(?:\s*`(?:[^`]|``)+`\s*|\s\s*[^\s]+\s\s*)AS)/is', "$1 SQL SECURITY INVOKER $6", $sql_line);
				if (null !== $this->old_table_prefix) {
					foreach (array_keys($this->restore_this_table) as $table_name) {
						// Code for a view can contain pretty much anything. As such, we want to be minimise the risks of unwanted matches.
						if (false !== strpos($sql_line, $table_name)) {
							$new_table_name = ('' == $this->old_table_prefix) ? $import_table_prefix.$table_name : UpdraftPlus_Manipulation_Functions::str_replace_once($this->old_table_prefix, $import_table_prefix, $table_name);
							$sql_line = str_replace($table_name, $new_table_name, $sql_line);
						}
					}
				}
			} else {
				// Prevent the previous value of $sql_type being retained for an unknown type
				$sql_type = 0;
			}

			// Do not execute "USE" or "CREATE|DROP DATABASE" commands
			if (6 != $sql_type && 7 != $sql_type && (9 != $sql_type || false == $this->triggers_forbidden) && 10 != $sql_type) {
				$do_exec = $this->sql_exec($sql_line, $sql_type);
				if (is_wp_error($do_exec)) return $do_exec;
			} else {
				$updraftplus->log("Skipped execution of SQL statement (unwanted or internally handled type=$sql_type): $sql_line");
			}

			// currently, the way UDP backups the generated column is different with the way mysqldump does it.
			// mysqldump doesn't include all of the columns/fields value into the insert statement but instead it specifies only non generated-columns to be included in the insert statement (i.e insert into `table`(`non-generated-column1`,`non-generated-column2`) values('value','value2'))
			// UDP includes all the columns (i.e insert into `table` values ('non-generated-column-value1', 'non-generated-column-value2', 'virtual-column-value', 'stored-column-value'))
			// the code below will only get executed if the running DB server is MySQL and the insert statement has all the columns included. If we change the way UDP backup the generated columns to be the same as mysqldump then the code below is no longer necessary and can be removed
			if (3 == $sql_type && !empty($this->generated_columns[$this->table_name])) {
				// MySQL doesn't allow the "generated columns" value takes its place in the insert statement, and because of that the only solution is to change the generated columns to standard columns which have been done previously
				// now we need to change it back to generated columns but unfortunately changing it to virtual type wont work, it can only be changed to stored type
				if (!isset($this->supported_generated_column_engines[strtolower($this->table_engine)])) $this->supported_generated_column_engines[strtolower($this->table_engine)] = UpdraftPlus_Database_Utility::is_generated_column_supported($this->table_engine);
				if (($generated_column_db_info = $this->supported_generated_column_engines[strtolower($this->table_engine)]) && !$generated_column_db_info['can_insert_ignore_to_generated_column'] && isset($this->generated_columns_exist_in_the_statement[$this->table_name]) && true === $this->generated_columns_exist_in_the_statement[$this->table_name]) {
					foreach ((array) $this->generated_columns[$this->table_name]['columns'] as $generated_column) {
						$new_data_type_definition = "`{$generated_column['column_name']}`";
						foreach ((array) $generated_column['column_data_type_definition'] as $key => $data_type_definition) {
							if (empty($data_type_definition) || 0 === strlen(trim($data_type_definition[0]))) continue;
							if (in_array($key, array('DATA_TYPE_TOKEN', 'GENERATED_ALWAYS_TOKEN', 'COMMENT_TOKEN'))) {
								$new_data_type_definition .= " ".$data_type_definition[0];
								continue; // we only want the data type options after the "generated always as()", so we continue
							}
							// If the database server doesn't support either null or not null constraint on generated virtual/stored/persistent column then the constraints need to be removed
							$new_data_type_definition .= $generated_column_db_info['is_not_null_supported'] ? $data_type_definition[0] : preg_replace('/\b(?:not\s+null|null)\b/i', '', $data_type_definition[0]);
							if (!$generated_column['is_virtual']) {
								// If the persistent type is not supported it likely means that the currently running db server is MySQL, Mariadb uses persistent as an alias for stored type so if the backup file is taken from MariaDB then it needs to be changed to stored
								$new_data_type_definition = $generated_column_db_info['is_persistent_supported'] ? $new_data_type_definition : preg_replace('/\bpersistent\b/i', 'STORED', $new_data_type_definition);
							}
						}
						$new_data_type_definition = preg_replace('/\bvirtual\b/i', 'STORED', $new_data_type_definition);
						// altering table could take minutes or hours to complete depending on the size of the rows of the table
						$do_exec = $this->sql_exec(sprintf("alter table `%s` change `%s` %s", $this->new_table_name, $generated_column['column_name'], $new_data_type_definition), -1);
						if (is_wp_error($do_exec)) return $do_exec;
					}
				}
			}

			// Reset
			$sql_line = '';
			$sql_type = -1;

		}

		if (is_array($this->stored_routine_supported) && $this->stored_routine_supported['is_binary_logging_enabled']) {
			// if this condition is met, it means that db server binary logging is enabled and the value of the log_bin_trust_function_system_variable has previously been set to ON (1), and now the value must be changed back to what it originally was
			if (isset($this->continuation_data['old_log_bin_trust_function_creators'])) { // it's a resumption
				$old_log_bin_trust_function_creators = $this->continuation_data['old_log_bin_trust_function_creators'];
			} else {
				$old_log_bin_trust_function_creators = $updraftplus->jobdata_get('old_log_bin_trust_function_creators');
			}
			if (is_string($old_log_bin_trust_function_creators) && '' !== $old_log_bin_trust_function_creators) {
				$this->set_log_bin_trust_function_creators($old_log_bin_trust_function_creators);
				// no need to check the return value of the set_log_bin_trust_function_creators here as if it is an wp error it has been handled already and this block of code wont be executed
				$updraftplus->log("log_bin_trust_function_creators variable has been resetted: ".$old_log_bin_trust_function_creators);
				// unset the old_log_bin_trust_function_creators index from the continuation_data so that the on_shutdown function won't check it again
				unset($this->continuation_data['old_log_bin_trust_function_creators']);
				// also delete the old_log_bin_trust_function_creators jobdata to prevent the on_shutdown function accessing and deleting it twice
				$updraftplus->jobdata_delete('old_log_bin_trust_function_creators');
				$updraftplus->log("log_bin_trust_function_creators variable has successfully been removed from UDP jobdata");
			}
		}

		// Rescan storage, but only if there was remote storage and a database; otherwise just re-scan locally
		if (!empty($this->ud_backup_set['db']) && !empty($this->ud_backup_set['service']) && ('none' !== $this->ud_backup_set['service'] && 'email' !== $this->ud_backup_set['service'] && array('') !== $this->ud_backup_set['service'] && array('none') !== $this->ud_backup_set['service'] && array('email') !== $this->ud_backup_set['service'])) {
			$only_add_this_file = array('file' => $this->ud_backup_set['db']);
			UpdraftPlus_Backup_History::rebuild(true, $only_add_this_file);
		} else {
			UpdraftPlus_Backup_History::rebuild();
		}

		if (!empty($this->lock_forbidden)) {
			$updraftplus->log("Leaving maintenance mode");
		} else {
			$updraftplus->log("Unlocking database and leaving maintenance mode");
			$this->unlock_tables();
		}
		$this->maintenance_mode(false);

		if ($this->restoring_table) {
			$final_table_name = $this->maybe_rename_restored_table();
			$this->restored_table($final_table_name, $this->final_import_table_prefix, $this->old_table_prefix, $this->table_engine);
		}


		// drop the dummy restored tables
		if ($this->is_dummy_db_restore) $this->drop_tables($this->restored_table_names);

		$time_taken = microtime(true) - $this->start_time;
		$updraftplus->log_e('Finished: lines processed: %d in %.2f seconds', $this->line, $time_taken);
		if ($is_plain) {
			fclose($dbhandle);
		} elseif ($is_bz2) {
			bzclose($dbhandle);
		} else {
			gzclose($dbhandle);
		}

		global $wp_filesystem;

		if (!$wp_filesystem->delete($working_dir.'/'.$db_basename, false, 'f')) {
			$this->restore_log_permission_failure_message($working_dir, 'Delete '.$working_dir.'/'.$db_basename);
		}

		return true;

	}

	/**
	 * This function will check if the passed in table should be skipped e.g it was processed on another run
	 *
	 * @param string $table_name - the name of the table we want to check
	 *
	 * @return boolean - returns true if we should skip the table, otherwise false if the table should be processed
	 */
	private function table_should_be_skipped($table_name) {

		global $updraftplus;

		$skip_table = false;
		$last_table = isset($this->continuation_data['last_processed_db_table']) ? $this->continuation_data['last_processed_db_table'] : '';

		$table_should_be_skipped = false;
		
		if (!empty($this->tables_to_skip) && in_array($table_name, $this->tables_to_skip)) {
			$table_should_be_skipped = true;
		} elseif (!empty($this->tables_to_restore) && !in_array($table_name, $this->tables_to_restore) && !$this->include_unspecified_tables) {
			$table_should_be_skipped = true;
		}

		if ($table_should_be_skipped) {
			if (empty($this->previous_table_name) || $table_name != $this->previous_table_name) $updraftplus->log(sprintf(__('Skipping table %s: user has chosen not to restore this table', 'updraftplus'), $table_name), 'notice-restore');
			$skip_table = true;
		} elseif (!empty($last_table) && !empty($table_name) && $table_name != $last_table) {
			if (empty($this->previous_table_name) || $table_name != $this->previous_table_name) $updraftplus->log(sprintf(__('Skipping table %s: already restored on a prior run; next table to restore: %s', 'updraftplus'), $table_name, $last_table), 'notice-restore');
			$skip_table = true;
		} elseif (!empty($last_table) && !empty($table_name) && $table_name == $last_table) {
			unset($this->continuation_data['last_processed_db_table']);
			$skip_table = false;
		}

		$this->previous_table_name = $table_name;

		if (!$skip_table) {
			$updraftplus->log_restore_update(array('type' => 'state', 'stage' => 'db', 'data' => array('stage' => 'table', 'table' => $table_name)));
			$updraftplus->jobdata_set('last_processed_db_table', $table_name);
		}

		return $skip_table;
	}

	/**
	 * This function will check if we are performing an atomic restore by renaming a temporary table to the final table name and returning the final table name
	 *
	 * @return string - returns the final table name
	 */
	private function maybe_rename_restored_table() {
		global $updraftplus;

		// If this is set then we do not want to attempt an atomic restore as it will remove the final table
		if ($this->disable_atomic_on_current_table) {
			$this->disable_atomic_on_current_table = false;
			return $this->original_table_name;
		}
		
		// If the table names are the same then we do not want to attempt an atomic restore as it will remove the final table
		if ($this->original_table_name == $this->restoring_table) return $this->original_table_name;

		// If we have skipped this table then we don't want to attempt the atomic restore
		if (!$this->restore_this_table($this->original_table_name)) {
			return $this->original_table_name;
		}
		
		if (!$this->rename_forbidden) {
			$updraftplus->log_e('Atomic restore: dropping original table (%s)', $this->original_table_name);
			$this->drop_tables(array($this->original_table_name));
			$updraftplus->log_e('Atomic restore: renaming new table (%s) to final table name (%s)', $this->restoring_table, $this->original_table_name);
			$this->rename_table($this->restoring_table, $this->original_table_name);
		}
		
		return $this->original_table_name;
	}

	/**
	 * This function will try to rename a table, if successful returns true otherwise returns an error number
	 *
	 * @param string $current_table_name - the current table name
	 * @param string $new_table_name     - the new table name
	 *
	 * @return boolean|integer - returns true if successful otherwise an error number
	 */
	private function rename_table($current_table_name, $new_table_name) {
		$current_table_name = UpdraftPlus_Manipulation_Functions::backquote($current_table_name);
		$new_table_name = UpdraftPlus_Manipulation_Functions::backquote($new_table_name);

		return $this->sql_exec("ALTER TABLE $current_table_name RENAME TO $new_table_name;", 14);
	}

	private function lock_table($table) {
	
		// Not yet working
		return true;
	
		global $updraftplus;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Function isnt being used yet
		$table = UpdraftPlus_Manipulation_Functions::backquote($table);
		
		if ($this->use_wpdb()) {
			$req = $wpdb->query("LOCK TABLES $table WRITE;");// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- Function isnt being used yet
		} else {
			if ($this->use_mysqli) {
				$req = mysqli_query($this->mysql_dbh, "LOCK TABLES $table WRITE;");
			} else {
				// @codingStandardsIgnoreLine
				$req = mysql_unbuffered_query("LOCK TABLES $table WRITE;", $this->mysql_dbh);
			}
			if (!$req) {
				// @codingStandardsIgnoreLine
				$lock_error_no = $this->use_mysqli ? mysqli_errno($this->mysql_dbh) : mysql_errno($this->mysql_dbh);
			}
		}
		if (!$req && ($this->use_wpdb() || 1142 === $lock_error_no)) {
			// Permission denied
			return 1142;
		}
		return true;
	}
	
	public function unlock_tables() {
		return;
		// Not yet working
		if ($this->use_wpdb()) {
			$wpdb->query("UNLOCK TABLES;");// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- Function isnt being used yet
		} elseif ($this->use_mysqli) {
			$req = mysqli_query($this->mysql_dbh, "UNLOCK TABLES;");// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		} else {
			// @codingStandardsIgnoreLine
			$req = mysql_unbuffered_query("UNLOCK TABLES;");
		}
	}

	/**
	 * Save configuration bundle, ready to restore it once the options table has been restored
	 */
	private function save_configuration_bundle() {
		$this->configuration_bundle = array();
		// Some items must always be saved + restored; others only on a migration
		// Remember, if modifying this, that a restoration can include restoring a destroyed site from a backup onto a fresh WP install on the same URL. So, it is not necessarily desirable to retain the current settings and drop the ones in the backup.
		$keys_to_save = array('updraft_remotesites', 'updraft_migrator_localkeys', 'updraft_central_localkeys', 'updraft_restore_in_progress');

		if ($this->old_siteurl != $this->our_siteurl || (defined('UPDRAFTPLUS_RESTORE_ALL_SETTINGS') && UPDRAFTPLUS_RESTORE_ALL_SETTINGS)) {
			global $updraftplus;
			$keys_to_save = array_merge($keys_to_save, $updraftplus->get_settings_keys());
			$keys_to_save[] = 'updraft_backup_history';
		}

		foreach ($keys_to_save as $key) {
			$this->configuration_bundle[$key] = UpdraftPlus_Options::get_updraft_option($key);
		}
	}

	/**
	 * The table here is just for logging/info. The actual restoration itself is done via the standard options class.
	 *
	 * @param  string $table specific table
	 */
	private function restore_configuration_bundle($table) {

		if (!is_array($this->configuration_bundle)) return;
		global $updraftplus;
		$updraftplus->log("Restoring prior UD configuration (table: $table; keys: ".count($this->configuration_bundle).")");
		foreach ($this->configuration_bundle as $key => $value) {
			UpdraftPlus_Options::delete_updraft_option($key);
			UpdraftPlus_Options::update_updraft_option($key, $value);
		}
	}

	/**
	 * Log the information that a particular SQL commandment is too long
	 *
	 * @param String $sql_line - the SQL
	 */
	private function log_oversized_packet($sql_line) {
		global $updraftplus;
		$logit = substr($sql_line, 0, 100);
		$updraftplus->log(sprintf("An SQL line that is larger than the maximum packet size and cannot be split was found: %s", '('.strlen($sql_line).', '.$logit.' ...)'));
		
		$updraftplus->log(__('Warning:', 'updraftplus').' '.sprintf(__("An SQL line that is larger than the maximum packet size and cannot be split was found; this line will not be processed, but will be dropped: %s", 'updraftplus'), '('.strlen($sql_line).', '.$this->max_allowed_packet.', '.$logit.' ...)'), 'notice-restore');
	}

	private function restore_this_table($table_name) {
	
		global $updraftplus;
		$unprefixed_table_name = substr($table_name, strlen($this->old_table_prefix));
	
		// First, check whether it's a multisite site which we're not restoring. This is stored in restore_this_site (once we know the site).
		if (!empty($this->ud_multisite_selective_restore)) {
			if (preg_match('/^(\d+)_.*$/', $unprefixed_table_name, $matches)) {
				$site_id = $matches[1];
			
				if (!isset($this->restore_this_site[$site_id])) {
					$this->restore_this_site[$site_id] = apply_filters(
						'updraftplus_restore_this_site',
						true,
						$site_id,
						$unprefixed_table_name,
						$this->restore_options
					);
				}
				
				if (false === $this->restore_this_site[$site_id]) {
					// The first time it's looked into, it gets logged
					$updraftplus->log_e('Skipping site %s: this table (%s) and others from the site will not be restored', $site_id, $table_name);
					$this->restore_this_site[$site_id] = 0;
				}
				
				if (!$this->restore_this_site[$site_id]) {
					return false;
				}
				
			}
		
		}
		
		// Secondly, if we're still intending to proceed, check the table specifically
		if (!isset($this->restore_this_table[$table_name])) {
		
			$this->restore_this_table[$table_name] = apply_filters(
				'updraftplus_restore_this_table',
				true,
				$unprefixed_table_name,
				$this->restore_options
			);
			
			if (false === $this->restore_this_table[$table_name]) {
				// The first time it's looked into, it gets logged
				$updraftplus->log_e('Skipping table %s: this table will not be restored', $table_name);
				$this->restore_this_table[$table_name] = 0;
			}
			
		}
		
		return $this->restore_this_table[$table_name];
	}
	
	/**
	 * UPDATE is sql_type=5 (not used in the function, but used in Migrator and so noted here for reference)
	 * $import_table_prefix is only use in one place in this function (long INSERTs), and otherwise need/should not be supplied
	 *
	 * SQL Types:
	 * 1 DROP
	 * 2 CREATE
	 * 3 INSERT
	 * 4 LOCK
	 * 5 UPDATE
	 * 6 WPB2D CREATE/DROP
	 * 7 WPB2D USE
	 * 8 SET NAMES
	 * 9 TRIGGER
	 * 10 DELIMITER
	 * 11 CREATE ALGORITHM
	 * 12 ROUTINE
	 * 13 DROP FUNCTION|PROCEDURE
	 * 14 ALTER
	 * 15 UNLOCK
	 *
	 * @param  String  $sql_line            sql line to execute
	 * @param  Integer $sql_type            sql type
	 * @param  String  $import_table_prefix import type prefix
	 * @param  Boolean $check_skipping      if true, then check whether the table is on the list of tables to skip
	 *
	 * @return Boolean|WP_Error|Void
	 */
	public function sql_exec($sql_line, $sql_type, $import_table_prefix = '', $check_skipping = true) {
		global $wpdb, $updraftplus;

		if ($check_skipping && !empty($this->table_name) && !$this->restore_this_table($this->table_name)) return;
		
		$ignore_errors = false;
		// Type 2 = CREATE TABLE
		if (2 == $sql_type && $this->create_forbidden) {
			$updraftplus->log_e('Cannot create new tables, so skipping this command (%s)', htmlspecialchars($sql_line));
			$req = true;
		} else {

			if (2 == $sql_type && !$this->drop_forbidden) {
				// We choose, for now, to be very conservative - we only do the apparently-missing drop if we have never seen any drop - i.e. assume that in SQL dumps with missing DROPs, that it's because there are no DROPs at all
				if (!in_array($this->new_table_name, $this->tables_been_dropped)) {
					$updraftplus->log_e('Table to be implicitly dropped: %s', $this->new_table_name);
					$this->sql_exec('DROP TABLE IF EXISTS '.UpdraftPlus_Manipulation_Functions::backquote($this->new_table_name), 1, '', false);
					$this->tables_been_dropped[] = $this->new_table_name;
				}
			}

			// Type 1 = DROP TABLE
			if (1 == $sql_type) {
				if ($this->drop_forbidden) {
					$sql_line = "DELETE FROM ".UpdraftPlus_Manipulation_Functions::backquote($this->new_table_name);
					$updraftplus->log_e('Cannot drop tables, so deleting instead (%s)', $sql_line);
					$ignore_errors = true;
				}
			}

			if (3 == $sql_type && $sql_line && strlen($sql_line) > $this->max_allowed_packet) {
				$this->log_oversized_packet($sql_line);
				// If this is the very first SQL line of the options table, we need to bail; it's essential
				$this->errors++;
				if (0 == $this->insert_statements_run && $this->new_table_name && $this->new_table_name == $import_table_prefix.'options') {
					$updraftplus->log('Leaving maintenance mode');
					$this->maintenance_mode(false);
					return new WP_Error('initial_db_error', sprintf(__('An error occurred on the first %s command - aborting run', 'updraftplus'), 'INSERT (options)'));
				}
				return false;
			}
			
			static $first_trigger = true;
			if (9 == $sql_type && $first_trigger) {
				$first_trigger = false;
				$updraftplus->log('Restoring TRIGGERs...');
			}

			static $first_stored_routine = true;
			if (12 == $sql_type && $first_stored_routine) {
				$first_stored_routine = false;
				$updraftplus->log('Restoring STORED ROUTINES...');
			}

			if ($this->use_wpdb()) {
				$req = $wpdb->query($sql_line);
				// WPDB, for several query types, returns the number of rows changed; in distinction from an error, indicated by (bool)false
				if (0 === $req) {
					$req = true;
				}
				if (!$req) $this->last_error = $wpdb->last_error;
			} else {
				if ($this->use_mysqli) {
					$req = mysqli_query($this->mysql_dbh, $sql_line);
					if (!$req) $this->last_error = mysqli_error($this->mysql_dbh);
				} else {
					// @codingStandardsIgnoreLine
					$req = mysql_unbuffered_query($sql_line, $this->mysql_dbh);
					// @codingStandardsIgnoreLine
					if (!$req) $this->last_error = mysql_error($this->mysql_dbh);
				}
			}
			if (3 == $sql_type) $this->insert_statements_run++;
			if (1 == $sql_type) $this->tables_been_dropped[] = $this->new_table_name;
			$this->statements_run++;
		}

		if (!$req) {
			if (!$ignore_errors) $this->errors++;
			$print_err = (strlen($sql_line) > 100) ? substr($sql_line, 0, 100).' ...' : $sql_line;
			$updraftplus->log(sprintf(_x('An error (%s) occurred:', 'The user is being told the number of times an error has happened, e.g. An error (27) occurred', 'updraftplus'), $this->errors)." - ".$this->last_error." - ".__('the database query being run was:', 'updraftplus').' '.$print_err, 'notice-restore');
			$updraftplus->log("An error (".$this->errors.") occurred: ".$this->last_error." - SQL query was (type=$sql_type): ".substr($sql_line, 0, 65536));

			if ('MySQL server has gone away' == $this->last_error || 'Connection was killed' == $this->last_error) {
				
				$restored = false;

				for ($i = 0; $i < 3; $i++) {
					 if ($this->restore_database_connection()) {
						$restored = true;
						break;
					 }
				}

				if (!$restored) {
					$updraftplus->log("The Database connection has been closed and cannot be reopened.");
					$updraftplus->log("Leaving maintenance mode");
					$this->maintenance_mode(false);
					return new WP_Error('db_connection_closed', __('The Database connection has been closed and cannot be reopened.', 'updraftplus'));
				}
				return $this->sql_exec($sql_line, $sql_type, $import_table_prefix, $check_skipping);
			}

			// First command is expected to be DROP TABLE
			if (1 == $this->errors && 2 == $sql_type && 0 == $this->tables_created) {
				if ($this->drop_forbidden) {
					$updraftplus->log_e("Create table failed - probably because there is no permission to drop tables and the table already exists; will continue");
				} else {
					$updraftplus->log("Leaving maintenance mode");
					$this->maintenance_mode(false);
					return new WP_Error('initial_db_error', sprintf(__('An error occurred on the first %s command - aborting run', 'updraftplus'), 'CREATE TABLE'));
				}
			} elseif (2 == $sql_type && 0 == $this->tables_created && $this->drop_forbidden) {
				// Decrease error counter again; otherwise, we'll cease if there are >=50 tables
				if (!$ignore_errors) $this->errors--;
			} elseif (3 == $sql_type && false !== strpos($this->last_error, 'Duplicate entry') && false !== strpos($sql_line, 'INSERT')) {
				$sql_line = UpdraftPlus_Manipulation_Functions::str_replace_once('INSERT', 'INSERT IGNORE', $sql_line);
				$updraftplus->log('Retrying SQL query with INSERT IGNORE', 'notice-restore');
				$this->sql_exec($sql_line, $sql_type, $import_table_prefix, $check_skipping);
			} elseif (8 == $sql_type && 1 == $this->errors) {
				$updraftplus->log("Aborted: SET NAMES ".$this->set_names." failed: leaving maintenance mode");
				$this->maintenance_mode(false);
				$extra_msg = '';
				$dbv = $wpdb->db_version();
				if ('utf8mb4' == strtolower($this->set_names) && $dbv && version_compare($dbv, '5.2.0', '<=')) {
					$extra_msg = ' '.__('This problem is caused by trying to restore a database on a very old MySQL version that is incompatible with the source database.', 'updraftplus').' '.sprintf(__('This database needs to be deployed on MySQL version %s or later.', 'updraftplus'), '5.5');
				}
				return new WP_Error('initial_db_error', sprintf(__('An error occurred on the first %s command - aborting run', 'updraftplus'), 'SET NAMES').'. '.sprintf(__('To use this backup, your database server needs to support the %s character set.', 'updraftplus'), $this->set_names).$extra_msg);
			} elseif (12 == $sql_type) {
				// sql_type 12 is stored routine creation
				// in case we dealt with an sql syntax error from the stored routine body, the restore operation should not be stopped
				$req = true;
			// Type 14 = ALTER TABLE
			} elseif (14 == $sql_type && 1 == $this->errors) {
				return 1142;
			}
			
			if ($this->errors >= (defined('UPDRAFTPLUS_SQLEXEC_MAXIMUM_ERRORS') ? UPDRAFTPLUS_SQLEXEC_MAXIMUM_ERRORS : 50)) {
				$this->maintenance_mode(false);
				return new WP_Error('too_many_db_errors', __('Too many database errors have occurred - aborting', 'updraftplus'));
			}
		} elseif (2 == $sql_type) {
			if (!$this->lock_forbidden) $this->lock_table($this->new_table_name);
			$this->tables_created++;
			do_action('updraftplus_creating_table', $this->new_table_name);
		}

		if ($this->line >0 && 0 == $this->line % 50) {
			if ($this->line > $this->line_last_logged && (0 == $this->line % 250 || $this->line < 250)) {
				$this->line_last_logged = $this->line;
				$time_taken = microtime(true) - $this->start_time;
				$updraftplus->log_e('Database queries processed: %d in %.2f seconds', $this->line, $time_taken);
			}
		}
		return $req;
	}

	private function flush_rewrite_rules() {

		// We have to deal with the fact that the procedures used call get_option, which could be looking at the wrong table prefix, or have the wrong thing cached

		global $updraftplus_addons_migrator;
		if (!empty($updraftplus_addons_migrator->new_blogid)) switch_to_blog($updraftplus_addons_migrator->new_blogid);

		$filter_these = array('permalink_structure', 'rewrite_rules', 'page_on_front');
		
		foreach ($filter_these as $opt) {
			add_filter('pre_option_'.$opt, array($this, 'option_filter_'.$opt));
		}

		global $wp_rewrite;
		$wp_rewrite->init();
		// Don't do this: it will cause rules created by plugins that weren't active at the start of the restore run to be lost
		// flush_rewrite_rules(true);

		if (function_exists('save_mod_rewrite_rules')) save_mod_rewrite_rules();
		if (function_exists('iis7_save_url_rewrite_rules')) iis7_save_url_rewrite_rules();

		foreach ($filter_these as $opt) {
			remove_filter('pre_option_'.$opt, array($this, 'option_filter_'.$opt));
		}

		if (!empty($updraftplus_addons_migrator->new_blogid)) restore_current_blog();

	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_template($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('template');
	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_stylesheet($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('stylesheet');
	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_template_root($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('template_root');
	}

	/**
	 * WordPress options filter
	 *
	 * @param String $val - pre-filter value
	 *
	 * @return String - filtered value
	 */
	public function option_filter_stylesheet_root($val) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		return $updraftplus->option_filter_get('stylesheet_root');
	}
	
	/**
	 * Called when a table has been restored
	 *
	 * @param String $table				  - The full table name that has been restored
	 * @param String $import_table_prefix - The table prefix being used to import
	 * @param String $old_table_prefix	  - The table prefix in the backup file
	 * @param String $engine			  - The database engine, if known
	 */
	private function restored_table($table, $import_table_prefix, $old_table_prefix, $engine = '') {

		$table_without_prefix = substr($table, strlen($import_table_prefix));
	
		if (isset($this->restore_this_table[$old_table_prefix.$table_without_prefix]) && !$this->restore_this_table[$old_table_prefix.$table_without_prefix]) return;

		global $wpdb, $updraftplus;
		
		if ($table == $import_table_prefix.UpdraftPlus_Options::options_table()) {
			// This became necessary somewhere around WP 4.5 - otherwise deleting and re-saving options stopped working
			wp_cache_flush();
			$this->restore_configuration_bundle($table);
		}

		if (preg_match('/^([\d+]_)?options$/', substr($table, strlen($import_table_prefix)), $matches)) {
			// The second prefix here used to have a '!$this->is_multisite' on it (i.e. 'options' table on non-multisite). However, the user_roles entry exists in the main options table on multisite too.
			if (($this->is_multisite && !empty($matches[1])) || $table == $import_table_prefix.'options') {
			
				$updraftplus->wipe_state_data();
			
				$mprefix = empty($matches[1]) ? '' : $matches[1];

				$new_table_name = $import_table_prefix.$mprefix."options";

				// WordPress has an option name predicated upon the table prefix. Yuk.
				if ($import_table_prefix != $old_table_prefix) {
					$updraftplus->log("Table prefix has changed: changing options table field(s) accordingly (".$mprefix."options)");
					$print_line = sprintf(__('Table prefix has changed: changing %s table field(s) accordingly:', 'updraftplus'), 'option').' ';
					if (false === $wpdb->query("UPDATE $new_table_name SET option_name='${import_table_prefix}".$mprefix."user_roles' WHERE option_name='${old_table_prefix}".$mprefix."user_roles' LIMIT 1")) {
						$print_line .= __('Error', 'updraftplus');
						$updraftplus->log("Error when changing options table fields: ".$wpdb->last_error);
					} else {
						$updraftplus->log("Options table fields changed OK");
						$print_line .= __('OK', 'updraftplus');
					}
					$updraftplus->log($print_line, 'notice-restore');
				}
				
				// Now deal with the situation where the imported database sets a new over-ride upload_path that is absolute - which may not be wanted
				$new_upload_path = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM ${import_table_prefix}".$mprefix."options WHERE option_name = %s LIMIT 1", 'upload_path'));
				$new_upload_path = (is_object($new_upload_path)) ? $new_upload_path->option_value : '';
				// The danger situation is absolute and points somewhere that is now perhaps not accessible at all

				if (!empty($new_upload_path) && $new_upload_path != $this->prior_upload_path && (strpos($new_upload_path, '/') === 0) || preg_match('#^[A-Za-z]:[/\\\]#', $new_upload_path)) {

					// $this->old_siteurl != untrailingslashit(site_url()) is not a perfect proxy for "is a migration" (other possibilities exist), but since the upload_path option should not exist since WP 3.5 anyway, the chances of other possibilities are vanishingly small
					if (!file_exists($new_upload_path) || $this->old_siteurl != $this->our_siteurl) {

						if (!file_exists($new_upload_path)) {
							$updraftplus->log_e("Uploads path (%s) does not exist - resetting (%s)", $new_upload_path, $this->prior_upload_path);
						} else {
							$updraftplus->log_e("Uploads path (%s) has changed during a migration - resetting (to: %s)", $new_upload_path, $this->prior_upload_path);
						}
						if (false === $wpdb->query($wpdb->prepare("UPDATE ${import_table_prefix}".$mprefix."options SET option_value='%s' WHERE option_name='upload_path' LIMIT 1", array($this->prior_upload_path)))) {
							$updraftplus->log(__('Error', 'updraftplus'), 'notice-restore');
							$updraftplus->log("Error when changing upload path: ".$wpdb->last_error);
							$updraftplus->log("Failed");
						}
					}
				}

				// TODO:Do on all WPMU tables
				if ($table == $import_table_prefix.'options') {
					// Bad plugin that hard-codes path references - https://wordpress.org/plugins/custom-content-type-manager/
					$cctm_data = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $new_table_name WHERE option_name = %s LIMIT 1", 'cctm_data'));
					if (!empty($cctm_data->option_value)) {
						$cctm_data = maybe_unserialize($cctm_data->option_value);
						if (is_array($cctm_data) && !empty($cctm_data['cache']) && is_array($cctm_data['cache'])) {
							$cctm_data['cache'] = array();
							$updraftplus->log_e("Custom content type manager plugin data detected: clearing option cache");
							update_option('cctm_data', $cctm_data);
						}
					}
					// Another - http://www.elegantthemes.com/gallery/elegant-builder/
					$elegant_data = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $new_table_name WHERE option_name = %s LIMIT 1", 'et_images_temp_folder'));
					if (!empty($elegant_data->option_value)) {
						$dbase = basename($elegant_data->option_value);
						$wp_upload_dir = wp_upload_dir();
						$edir = $wp_upload_dir['basedir'];
						if (!is_dir($edir.'/'.$dbase)) @mkdir($edir.'/'.$dbase);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						$updraftplus->log_e("Elegant themes theme builder plugin data detected: resetting temporary folder");
						update_option('et_images_temp_folder', $edir.'/'.$dbase);
					}
				}

				// The gantry menu plugin sometimes uses too-long transient names, causing the timeout option to be missing; and hence the transient becomes permanent.
				// WP 3.4 onwards has $wpdb->delete(). But we support 3.2 onwards.
				$wpdb->query("DELETE FROM $new_table_name WHERE option_name LIKE '_transient_gantry-menu%' OR option_name LIKE '_transient_timeout_gantry-menu%'");

				// Jetpack: see: https://wordpress.org/support/topic/issues-with-dev-site
				if ($this->old_siteurl != $this->our_siteurl) {
					$wpdb->query("DELETE FROM $new_table_name WHERE option_name = 'jetpack_options'");
				}

				// if we are importing a single site into a multisite (which means we have the multisite add-on) we need to clear our saved options and crons to prevent unwanted backups
				if (isset($this->restore_options['updraftplus_migrate_blogname'])) {
					$wpdb->query("DELETE FROM {$import_table_prefix}{$mprefix}options WHERE option_name LIKE 'updraft_%'");
					$crons = maybe_unserialize($wpdb->get_var("SELECT option_value FROM {$import_table_prefix}{$mprefix}options WHERE option_name = 'cron'"));
					foreach ($crons as $timestamp => $cron) {
						if (!is_array($cron)) continue;
						foreach (array_keys($cron) as $key) {
							if (false !== strpos($key, 'updraft_')) unset($crons[$timestamp][$key]);
							if (empty($crons[$timestamp])) unset($crons[$timestamp]);
						}
					}
					$crons = serialize($crons);
					$wpdb->query($wpdb->prepare("UPDATE {$import_table_prefix}{$mprefix}options SET option_value='%s' WHERE option_name='cron'", $crons));
				}

			}

		} elseif ($import_table_prefix != $old_table_prefix && preg_match('/^([\d+]_)?usermeta$/', substr($table, strlen($import_table_prefix)), $matches)) {

			// This table is not a per-site table, but per-install

			$updraftplus->log("Table prefix has changed: changing usermeta table field(s) accordingly");

			$print_line = sprintf(__('Table prefix has changed: changing %s table field(s) accordingly:', 'updraftplus'), 'usermeta').' ';

			$errors_occurred = false;

			if (false === strpos($old_table_prefix, '_')) {
				// Old, slow way: do it row-by-row
				// By Jul 2015, doing this on the updraftplus.com database took 20 minutes on a slow test machine
				$old_prefix_length = strlen($old_table_prefix);

				$um_sql = "SELECT umeta_id, meta_key 
					FROM ${import_table_prefix}usermeta 
					WHERE meta_key 
					LIKE '".str_replace('_', '\_', $old_table_prefix)."%'";
				$meta_keys = $wpdb->get_results($um_sql);

				foreach ($meta_keys as $meta_key) {
					// Create new meta key
					$new_meta_key = $import_table_prefix . substr($meta_key->meta_key, $old_prefix_length);
					
					$query = "UPDATE " . $import_table_prefix . "usermeta 
						SET meta_key='".$new_meta_key."' 
						WHERE umeta_id=".$meta_key->umeta_id;

					if (false === $wpdb->query($query)) $errors_occurred = true;
				}
			} else {
				// New, fast way: do it in a single query
				$sql = "UPDATE ${import_table_prefix}usermeta SET meta_key = REPLACE(meta_key, '$old_table_prefix', '${import_table_prefix}') WHERE meta_key LIKE '".str_replace('_', '\_', $old_table_prefix)."%';";
				if (false === $wpdb->query($sql)) $errors_occurred = true;
			}

			if ($errors_occurred) {
				$updraftplus->log("Error when changing usermeta table fields");
				$print_line .= __('Error', 'updraftplus');
			} else {
				$updraftplus->log("Usermeta table fields changed OK");
				$print_line .= __('OK', 'updraftplus');
			}
			$updraftplus->log($print_line, 'notice-restore');

		}

		do_action('updraftplus_restored_db_table', $table, $import_table_prefix, $engine);

		// Re-generate permalinks. Do this last - i.e. make sure everything else is fixed up first.
		if ($table == $import_table_prefix.'options') $this->flush_rewrite_rules();

	}

	/**
	 * Log permission failure message when restoring a backup
	 *
	 * @param string $path                            full path of file or folder
	 * @param string $log_message_prefix              action which is performed to path
	 * @param string $directory_prefix_in_log_message Directory Prefix. It should be either "Parent" or "Destination"
	 */
	private function restore_log_permission_failure_message($path, $log_message_prefix, $directory_prefix_in_log_message = 'Parent') {
		global $updraftplus;
		$log_message = $updraftplus->log_permission_failure_message($path, $log_message_prefix, $directory_prefix_in_log_message);
		if ($log_message) {
			$updraftplus->log($log_message, 'warning-restore');
		}
	}

	/**
	 * This function will loop through all the sites available and get their active plugins and ensure any missing plugins are removed from the active list to prevent crashes.
	 *
	 * @param string $import_table_prefix - the table prefix
	 *
	 * @return void
	 */
	private function check_active_plugins($import_table_prefix) {
		global $wpdb;

		if ($this->is_multisite) {
			// Get the site wide active plugins
			$plugins = $wpdb->get_row("SELECT meta_value FROM ${import_table_prefix}sitemeta WHERE meta_key = 'active_sitewide_plugins'");
			if (!empty($plugins->meta_value)) {
				$plugins = $this->deactivate_missing_plugins($plugins->meta_value);
				$wpdb->query($wpdb->prepare("UPDATE ${import_table_prefix}sitemeta SET meta_value=%s WHERE meta_key='active_sitewide_plugins'", $plugins));
			}
			
			$offset = 0;
			$limit = 250;

			while (true) {
				// Loop over and get each sites active plugins
				$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs} LIMIT ${offset}, ${limit}", ARRAY_A);

				if (empty($blogs)) break;
				
				foreach ($blogs as $row) {
					if (!apply_filters('updraftplus_restore_this_site', true, $row['blog_id'], '', $this->restore_options)) continue;
					$plugins = $wpdb->get_row("SELECT option_value FROM ".$wpdb->get_blog_prefix($row['blog_id'])."options WHERE option_name = 'active_plugins'");
					if (empty($plugins->option_value)) continue;
					$plugins = $this->deactivate_missing_plugins($plugins->option_value);
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->get_blog_prefix($row['blog_id'])."options SET option_value=%s WHERE option_name='active_plugins'", $plugins));
				}

				$offset += $limit;
			}

		} else {
			$plugins = $wpdb->get_row("SELECT option_value FROM ${import_table_prefix}options WHERE option_name = 'active_plugins'");
			if (empty($plugins->option_value)) return;
			$plugins = $this->deactivate_missing_plugins($plugins->option_value);
			$wpdb->query($wpdb->prepare("UPDATE ${import_table_prefix}options SET option_value=%s WHERE option_name='active_plugins'", $plugins));
		}
	}

	/**
	 * This function will check the list of active plugins and ensure they are still installed, if any are missing it will deactivate them to prevent the site from crashing.
	 *
	 * @param String $plugins - serialized active plugins
	 *
	 * @return String - filtered results
	 */
	private function deactivate_missing_plugins($plugins) {
		global $updraftplus;

		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$installed_plugins = array_keys(get_plugins());
		$plugins = maybe_unserialize($plugins);
		
		foreach ($plugins as $key => $path) {
			// Single site and multisite have a different array structure, in single site the path is the array value, in multisite the path is the array key.
			if (!in_array($key, $installed_plugins) && !in_array($path, $installed_plugins)) {
				$log_path = $this->is_multisite ? $key : $path;
				$updraftplus->log_e('Plugin path %s not found: de-activating.', $log_path);
				unset($plugins[$key]);
			}
		}

		$plugins = serialize($plugins);

		return $plugins;
	}

	/**
	 * This function will return a random table prefix
	 *
	 * @param String $string - default prefix
	 *
	 * @return String - the random prefix
	 */
	public function updraftplus_random_restore_table_prefix($string) {
		global $wpdb;
		while (true) {
			$random_string = UpdraftPlus_Manipulation_Functions::generate_random_string(2). '_';
			if ($string != $random_string) {
				if (0 === $wpdb->query("SHOW TABLES LIKE '".$random_string."%'")) return $random_string;
			}
		}
	}

	/**
	 * This function will drop all tables from the database
	 *
	 * @param Array $tables - list of table names
	 */
	private function drop_tables($tables) {
		foreach ($tables as $table) $this->sql_exec('DROP TABLE IF EXISTS '.UpdraftPlus_Manipulation_Functions::backquote($table), 1, '', false);
	}

	/**
	 * Adjust and replace the invalid root path of the auto_prepend_file values with the current server's root path
	 */
	private function adjust_auto_prepend_directive() {

		global $wp_filesystem, $updraftplus;

		$external_plugins = array(
			'wordfence' => array(
				'filename' => 'wordfence-waf.php', // this file is located in the root directory so there's no additional path in it, other plugins may place the corresponding file in its plugin directory, in that case the additional path should be added (e.g. 'wp-content/plugins/plugin-name/file-name.php')
				'callback' => 'adjust_wordfencewaf_root_path',
			)
		);

		foreach ($updraftplus->server_configuration_file_list() as $server_config_file) {
			if (empty($server_config_file)) continue;
			if (file_exists($this->abspath.$server_config_file)) {
				$updraftplus->log("$server_config_file configuration file has been detected during the restoration. Trying to open the file now for various-fixing tasks");
				$server_config_file_content = file_get_contents($this->abspath.$server_config_file);
				if (false !== $server_config_file_content) {
					foreach ($external_plugins as $data) {
						$file_pattern = str_replace(array('/', '.', "'", '"'), array('\/', '\.', "\'", '\"'), $data['filename']);
						if (file_exists($this->abspath.$data['filename'])) {
							if (!$wp_filesystem->put_contents($this->abspath.$server_config_file, preg_replace('/((?:php_value\s\s*)?auto_prepend_file(?:\s*=)?\s*(?:\'|")).+?'.$file_pattern.'(\'|")/is', "$1{$this->abspath}{$data['filename']}$2", $server_config_file_content))) {
								$updraftplus->log("Couldn't write a fix into the $server_config_file file");
							}
							if (isset($data['callback']) && method_exists($this, $data['callback'])) call_user_func(array($this, $data['callback']));
						} else {
							// if somehow, some way, the plugin's auto prepended file is missing then the auto_prepend_file directive in the config file needs to be removed or it will cause a fatal error
							if (!$wp_filesystem->put_contents($this->abspath.$server_config_file, preg_replace('/((?:php_value\s\s*)?auto_prepend_file(?:\s*=)?\s*(?:\'|")).+?'.$file_pattern.'(\'|")/is', "", $server_config_file_content))) {
								$updraftplus->log("The {$data['filename']} file doesn't exist, couldn't write a fix into the $server_config_file file");
							}
						}
					}
				} else {
					$updraftplus->log("Failed to read the $server_config_file file");
				}
			}
		}
	}

	/**
	 * Adjust and replace the root paths in the wordfence-waf.php file with the current server's root path
	 */
	private function adjust_wordfencewaf_root_path() {
		global $wp_filesystem, $updraftplus;
		if (file_exists($this->abspath.'wordfence-waf.php')) {
			$updraftplus->log("Wordfence auto-prepended file has been detected during the restoration. Trying to open the file now for various-fixing tasks");
			$wordfence_waf = file_get_contents($this->abspath.'wordfence-waf.php');
			if (false !== $wordfence_waf) {
				// https://regex101.com/r/VeCwzH/1/
				if (preg_match_all('/(?:wp-content[\/\\\]+plugins[\/\\\]+wordfence[\/\\\]+waf[\/\\\]+bootstrap\.php|wp-content[\/\\\]+wflogs[\/\\\]*)((?:\'|"))/is', $wordfence_waf, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
					$matches = array_reverse($matches);
					foreach ($matches as $match) {
						$enclosure_cnt = 0;
						$start = (int) $match[0][1];
						$enclosure = $match[1][0];
						$offset = -1;
						for ($i=$start; $i>=0; $i--) {
							if ($enclosure_cnt > 0) {
								if ('\\' === $wordfence_waf[$i]) {
									$enclosure_cnt--;
								} else {
									$offset = $i+2;
									break;
								}
							} else {
								if ($enclosure === $wordfence_waf[$i]) {
									$enclosure_cnt++;
								}
							}
						}
						if ($offset >= 0) {
							if (false !== stripos($match[0][0], 'wflogs')) {
								$wordfence_waf = substr_replace($wordfence_waf, WP_CONTENT_DIR.'/wflogs/', $offset, ((int) $match[1][1]) - $offset);
							} else {
								$wordfence_waf = substr_replace($wordfence_waf, WP_PLUGIN_DIR.'/wordfence/waf/bootstrap.php', $offset, ((int) $match[1][1]) - $offset);
							}
						}
					}
					if (!$wp_filesystem->put_contents($this->abspath.'wordfence-waf.php', $wordfence_waf)) {
						$updraftplus->log("Couldn't write fixes into the wordfence-waf.php file");
					}
				}
			} else {
				$updraftplus->log("Failed to read the wordfence-waf.php file");
			}
		}
	}
}

// The purpose of this is that, in a certain case, we want to forbid the "move" operation from doing a copy/delete if a direct move fails... because we have our own method for retrying (and don't want to risk copying a tonne of data if we can avoid it)
if (!class_exists('WP_Filesystem_Direct')) {
	if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-base.php');
	include_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-direct.php');
}
class UpdraftPlus_WP_Filesystem_Direct extends WP_Filesystem_Direct {

	/**
	 * Moves a file
	 *
	 * @param String  $source      Path to the source file.
	 * @param String  $destination Path to the destination file.
	 * @param Boolean $overwrite   Whether to overwrite the destination file if it exists.
	 *
	 * @return Boolean Success status
	 */
	public function move($source, $destination, $overwrite = false) {
		if (!$overwrite && $this->exists($destination))
			return false;

		// try using rename first. if that fails (for example, source is read only) try copy
		if (@rename($source, $destination))// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			return true;

		return false;
	}
}

/**
 * Plugin class that is used for Elementor's namespace aliasing or could also be used for a variety of purposes of Elementor need
 */
if (!class_exists('\Elementor\Plugin')) {
	class UpdraftPlus_Elementor_Plugin {
	}
}

/**
 * Get a protected property
 */
class UpdraftPlus_WPDB extends wpdb {

	/**
	 * Get the database handle
	 *
	 * @return Mixed - the database handle
	 */
	public function updraftplus_get_database_handle() {
		return $this->dbh;
	}
	
	/**
	 * Return whether the object is using mysqli or not.
	 *
	 * @return Boolean
	 */
	public function updraftplus_use_mysqli() {
		return !empty($this->use_mysqli);
	}
}
