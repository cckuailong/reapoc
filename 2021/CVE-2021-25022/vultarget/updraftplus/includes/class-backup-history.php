<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No access.');

/**
 * A class to deal with management of backup history.
 * N.B. All database access should come through here. However, this class is not the only place with knowledge of the data structure.
 */
class UpdraftPlus_Backup_History {

	/**
	 * Get the backup history for an indicated timestamp, or the complete set of all backup histories
	 *
	 * @param Integer|Boolean $timestamp - Indicate a particular timestamp to get a particular backup job, or false to get a list of them all (sorted by most recent first).
	 *
	 * @return Array - either the particular backup indicated, or the full list.
	 */
	public static function get_history($timestamp = false) {

		$backup_history = UpdraftPlus_Options::get_updraft_option('updraft_backup_history');
		// N.B. Doing a direct wpdb->get_var() here actually *introduces* a race condition
		
		if (!is_array($backup_history)) $backup_history = array();

		$backup_history = self::build_incremental_sets($backup_history);

		if ($timestamp) return isset($backup_history[$timestamp]) ? $backup_history[$timestamp] : array();

		// The most recent backup will be first. Then we can array_pop().
		krsort($backup_history);

		return $backup_history;

	}
	
	/**
	 * Add jobdata to all entries in an array of history items which do not already have it (key: 'jobdata'). If none is found, it will still be set, but empty.
	 *
	 * @param Array $backup_history - the list of history items
	 *
	 * @return Array
	 */
	public static function add_jobdata($backup_history) {
	
		global $wpdb;
		$table = is_multisite() ? $wpdb->sitemeta : $wpdb->options;
		$key_column = is_multisite() ? 'meta_key' : 'option_name';
		$value_column = is_multisite() ? 'meta_value' : 'option_value';

		$any_more = true;
		
		while ($any_more) {
		
			$any_more = false;
			$columns = array();
			$nonces_map = array();
		
			foreach ($backup_history as $timestamp => $backup) {
				if (isset($backup['jobdata'])) continue;
				$nonce = $backup['nonce'];
				$nonces_map[$nonce] = $timestamp;
				$columns[] = $nonce;
				// Approx. 2.5MB of data would be expected if they all had 5KB each (though in reality we expect very few of them to have any)
				if (count($columns) >= 500) {
					$any_more = true;
					break;
				}
			}
			
			if (empty($columns)) break;
			
			$columns_sql = '';
			foreach ($columns as $nonce) {
				if ($columns_sql) $columns_sql .= ',';
				$columns_sql .= "'updraft_jobdata_".esc_sql($nonce)."'";
			}
			
			$sql = 'SELECT '.$key_column.', '.$value_column.' FROM '.$table.' WHERE '.$key_column.' IN ('.$columns_sql.')';
			$all_jobdata = $wpdb->get_results($sql);

			foreach ($all_jobdata as $values) {
				// The 16 here is the length of 'updraft_jobdata_'
				$nonce = substr($values->$key_column, 16);
				if (empty($nonces_map[$nonce]) || empty($values->$value_column)) continue;
				$jobdata = maybe_unserialize($values->$value_column);
				$backup_history[$nonces_map[$nonce]]['jobdata'] = empty($jobdata) ? array() : $jobdata;
			}
			foreach ($columns as $nonce) {
				if (!empty($nonces_map[$nonce]) && !isset($backup_history[$nonces_map[$nonce]]['jobdata'])) $backup_history[$nonces_map[$nonce]]['jobdata'] = array();
			}
		}
		
		return $backup_history;
	}
	
	/**
	 * Get the backup history for an indicated nonce
	 *
	 * @param String $nonce - Backup nonce to get a particular backup job
	 *
	 * @return Array|Boolean - either the particular backup indicated, or false
	 */
	public static function get_backup_set_by_nonce($nonce) {
		if (empty($nonce)) return false;
		$backup_history = self::get_history();
		foreach ($backup_history as $timestamp => $backup_info) {
			if ($nonce == $backup_info['nonce']) {
				$backup_info['timestamp'] = $timestamp;
				return $backup_info;
			}
		}
		return false;
	}

	/**
	 * Get the HTML for the table of existing backups
	 *
	 * @param Array|Boolean $backup_history - a list of backups to use, or false to get the current list from the database
	 * @param Boolean       $backup_count   - the amount of backups currently displayed in the existing backups table
	 *
	 * @uses UpdraftPlus_Admin::include_template()
	 *
	 * @return String - HTML for the table
	 */
	public static function existing_backup_table($backup_history = false, $backup_count = 0) {

		global $updraftplus, $updraftplus_admin;

		if (false === $backup_history) $backup_history = self::get_history();
		
		if (!is_array($backup_history) || empty($backup_history)) return '<div class="postbox"><p class="updraft-no-backups-msg">'.__('You have not yet made any backups.', 'updraftplus').'</p> <p class="updraft-no-backups-msg">'.__('If you have an existing backup that you wish to upload and restore from, then please use the "Upload backup files" link above.', 'updraftplus').' '.__('Or, if they are in remote storage, you can connect that remote storage (in the "Settings" tab), save your settings, and use the "Rescan remote storage" link.', 'updraftplus').'</p></div>';

		if (empty($backup_count)) {
			$backup_count = defined('UPDRAFTPLUS_EXISTING_BACKUPS_LIMIT') ? UPDRAFTPLUS_EXISTING_BACKUPS_LIMIT : 100;
		}

		// Reverse date sort - i.e. most recent first
		krsort($backup_history);
		
		$pass_values = array(
			'backup_history' => self::add_jobdata($backup_history),
			'updraft_dir' => $updraftplus->backups_dir_location(),
			'backupable_entities' => $updraftplus->get_backupable_file_entities(true, true),
			'backup_count' => $backup_count,
			'show_paging_actions' => false,
		);
		
		return $updraftplus_admin->include_template('wp-admin/settings/existing-backups-table.php', true, $pass_values);
	
	}
	
	/**
	 * This function will scan the backup history and split the files up in to incremental sets, foreign backup sets will only have one incremental set.
	 *
	 * @param Array $backup_history - the saved backup history
	 *
	 * @return Array - returns the backup history but also includes the incremental sets
	 */
	public static function build_incremental_sets($backup_history) {

		global $updraftplus;

		$backupable_entities = array_keys($updraftplus->get_backupable_file_entities(true, false));

		$accept = apply_filters('updraftplus_accept_archivename', array());

		foreach ($backup_history as $btime => $bdata) {

			$incremental_sets = array();

			foreach ($backupable_entities as $entity) {

				if (empty($bdata[$entity]) || !is_array($bdata[$entity])) continue;

				foreach ($bdata[$entity] as $key => $filename) {

					if (preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-[\-a-z]+([0-9]+)?+(\.(zip|gz|gz\.crypt))?$/i', $filename, $matches)) {

						$timestamp = strtotime($matches[1]);
						
						if (!isset($incremental_sets[$timestamp])) $incremental_sets[$timestamp] = array();

						if (!isset($incremental_sets[$timestamp][$entity])) $incremental_sets[$timestamp][$entity] = array();

						$incremental_sets[$timestamp][$entity][$key] = $filename;
					} else {
						$accepted = false;
						
						foreach ($accept as $fkey => $acc) {
							if (preg_match('/'.$acc['pattern'].'/i', $filename)) $accepted = $fkey;
						}
						
						if (!empty($accepted) && (false != ($btime = apply_filters('updraftplus_foreign_gettime', false, $accepted, $filename))) && $btime > 0) {
							
							$timestamp = $btime;
							
							if (!isset($incremental_sets[$timestamp])) $incremental_sets[$timestamp] = array();

							if (!isset($incremental_sets[$timestamp][$entity])) $incremental_sets[$timestamp][$entity] = array();
							
							$incremental_sets[$timestamp][$entity][] = $filename;
						}
					}
				}
			}
			ksort($incremental_sets);
			$backup_history[$btime]["incremental_sets"] = $incremental_sets;
		}
		
		return $backup_history;
	}

	/**
	 * Save the backup history. An abstraction function to make future changes easier.
	 *
	 * @param Array	  $backup_history - the backup history
	 * @param Boolean $use_cache	  - whether or not to use the WP options cache
	 */
	public static function save_history($backup_history, $use_cache = true) {
		
		global $updraftplus;

		// This data is constructed at run-time from the other keys; we do not wish to save redundant data
		foreach ($backup_history as $btime => $bdata) {
			unset($backup_history[$btime]['incremental_sets']);
		}
		
		// Explicitly set autoload to 'no', as the backup history can get quite big.
		$changed = UpdraftPlus_Options::update_updraft_option('updraft_backup_history', $backup_history, $use_cache, 'no');

		if (!$changed) {
		
			$max_packet_size = $updraftplus->max_packet_size(false, false);
			$serialization_size = strlen(addslashes(serialize($backup_history)));
			
			// Take off the *approximate* over-head of UPDATE wp_options SET option_value='' WHERE option_name='updraft_backup_history'; (no need to be exact)
			if ($max_packet_size < ($serialization_size + 100)) {
			
				$max_packet_size = $updraftplus->max_packet_size();
				
				$changed = UpdraftPlus_Options::update_updraft_option('updraft_backup_history', $backup_history, $use_cache, 'no');
				
				if (!$changed) {
		
					$updraftplus->log('The attempt to write the backup history to the WP database returned a negative code and the max packet size looked small. However, WP does not distinguish between a failure and no change from a previous update, so, this code is not conclusive and if no other symptoms are observed then there is no reason to infer any problem. Info: The updated database packet size is '.$max_packet_size.'; the serialization size is '.$serialization_size);
			
				}
				
			}
			
		}
	}
	
	/**
	 * Used by self::always_get_from_db()
	 *
	 * @return Mixed - the database option
	 */
	public static function filter_updraft_backup_history() {
		global $wpdb;
		$row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", 'updraft_backup_history'));
		if (is_object($row)) return maybe_unserialize($row->option_value);
		return false;
	}
	
	/**
	 * Make sure we get the value afresh from the db, instead of using the auto-loaded/cached value (which can be out of date, especially since backups are, by their nature, long-running)
	 */
	public static function always_get_from_db() {
		add_filter('pre_option_updraft_backup_history', array('UpdraftPlus_Backup_History', 'filter_updraft_backup_history'));
	}
	
	/**
	 * This function examines inside the updraft directory to see if any new archives have been uploaded. If so, it adds them to the backup set. (Non-present items are also removed, only if the service is 'none').
	 *
	 * N.B. The logic is a bit more subtle than it needs to be, because of backups being keyed by backup time, instead of backup nonce, and the subsequent introduction of the possibility of incremental backup sets taken at different times. This could be cleaned up to reduce the amount of code and make it simpler in places.
	 *
	 * @param Boolean	   $remote_scan		   - scan not only local, but also remote storage
	 * @param Array|String $only_add_this_file - if set to an array (with keys 'file' and (optionally) 'label'), then a file will only be taken notice of if the filename matches the 'file' key (and the label will be associated with the backup set)
	 * @param Boolean	   $debug			   - include debugging messages. These will be keyed with keys beginning 'debug-' so that they can be distinguished.
	 *
	 * @return Array - an array of messages which the caller may wish to display to the user. N.B. Messages are not necessarily just strings.
	 */
	public static function rebuild($remote_scan = false, $only_add_this_file = false, $debug = false) {

		global $updraftplus;
	
		$messages = array();
		$gmt_offset = get_option('gmt_offset');

		// Array of nonces keyed by filename
		$backup_nonces_by_filename = array();
		// Array of backup times keyed by nonce
		$backup_times_by_nonce = array();
		
		$changes = false;

		$backupable_entities = $updraftplus->get_backupable_file_entities(true, false);

		$backup_history = self::get_history();

		$updraft_dir = $updraftplus->backups_dir_location();
		if (!is_dir($updraft_dir)) return array("Internal directory path does not indicate a directory ($updraft_dir)");

		$accept = apply_filters('updraftplus_accept_archivename', array());
		
		// First, process the database backup history to get a record of what is already known there. This means populating the arrays $backup_nonces_by_filename and $backup_times_by_nonce
		foreach ($backup_history as $btime => $bdata) {
			$found_file = false;
			foreach ($bdata as $key => $values) {
				if ('db' != $key && !isset($backupable_entities[$key])) continue;
				// Record which set this file is found in
				if (!is_array($values)) $values = array($values);
				foreach ($values as $filename) {
					if (!is_string($filename)) continue;
					if (preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-[\-a-z]+([0-9]+)?+(\.(zip|gz|gz\.crypt))?$/i', $filename, $matches)) {
						$nonce = $matches[2];
						if (isset($bdata['service']) && ('none' === $bdata['service'] || (is_array($bdata['service']) && (array('none') === $bdata['service'] || (1 == count($bdata['service']) && isset($bdata['service'][0]) && empty($bdata['service'][0]))))) && !is_file($updraft_dir.'/'.$filename)) {
							// File without remote storage is no longer locally present
						} else {
							$found_file = true;
							$backup_nonces_by_filename[$filename] = $nonce;
							if (empty($backup_times_by_nonce[$nonce]) || $backup_times_by_nonce[$nonce] < 100) {
								$backup_times_by_nonce[$nonce] = $btime;
							} elseif ($btime < $backup_times_by_nonce[$nonce]) {
								$backup_times_by_nonce[$nonce] = $btime;
							}
						}
					} else {
						$accepted = false;
						foreach ($accept as $fkey => $acc) {
							if (preg_match('/'.$acc['pattern'].'/i', $filename)) $accepted = $fkey;
						}
						if (!empty($accepted) && (false != ($btime = apply_filters('updraftplus_foreign_gettime', false, $accepted, $filename))) && $btime > 0) {
							$found_file = true;
							// Generate a nonce; this needs to be deterministic and based on the filename only
							$nonce = substr(md5($filename), 0, 12);
							$backup_nonces_by_filename[$filename] = $nonce;
							if (empty($backup_times_by_nonce[$nonce]) || $backup_times_by_nonce[$nonce] < 100) {
								$backup_times_by_nonce[$nonce] = $btime;
							} elseif ($btime < $backup_times_by_nonce[$nonce]) {
								$backup_times_by_nonce[$nonce] = $btime;
							}
						}
					}
				}
			}
			if (!$found_file) {
				// File recorded as being without remote storage is no longer present. It may in fact exist in remote storage, and this will be picked up later (when we scan the remote storage).
				unset($backup_history[$btime]);
				$changes = true;
			}
		}

		// Secondly, scan remote storage and get back lists of files and their sizes

		// $remote_files has a key for each filename (basename), and the value is a list of remote destinations (e.g. 'dropbox', 's3') in which the file was found
		$remote_files = array();
		
		// A list of nonces found remotely (to help with handling sets split across destinations)
		$remote_nonces_found = array();
		
		$remote_sizes = array();
		
		if ($remote_scan) {
		
			$updraftplus->register_wp_http_option_hooks(true);
			
			$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array_keys($updraftplus->backup_methods));

			foreach ($storage_objects_and_ids as $method => $method_information) {
				
				$object = $method_information['object'];
 
				if (!method_exists($object, 'listfiles')) continue;

				// Support of multi_options is now required for storage methods that implement listfiles()
				if (!$object->supports_feature('multi_options')) {
					error_log("UpdraftPlus: Multi-options not supported by: ".$method);
					continue;
				}

				foreach ($method_information['instance_settings'] as $instance_id => $options) {
					
					$object->set_options($options, false, $instance_id);
					
					$files = $object->listfiles('backup_');
					
					$method_description = $object->get_description();
					
					if (is_array($files)) {

						if ($debug) {
							$messages[] = array(
								'method' => $method,
								'desc' => $method_description,
								'code' => 'file-listing',
								'message' => '',
								'data' => $files,
								'service_instance_id' => $instance_id,
							);
						}
						
						foreach ($files as $entry) {
							$filename = $entry['name'];
							if (!preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?(\.(zip|gz|gz\.crypt))?$/i', $filename, $matches)) continue;

							$nonce = $matches[2];
							$btime = strtotime($matches[1]);
							// Of course, it's possible that the site doing the scanning has a different timezone from the site that the backups were created in, in which case, this calculation will have a confusing result to the user. That outcome cannot be completely eliminated (without making the filename to reflect UTC, which confuses more users).
							if (!empty($gmt_offset)) $btime -= $gmt_offset * 3600;

							// Is the set already known?
							if (isset($backup_times_by_nonce[$nonce])) {
								// N.B. With an incremental set, the newly found file may be earlier than the known elements, so tha the backup array should be re-keyed.
								$btime_exact = $backup_times_by_nonce[$nonce];
								if ($btime > 100 && $btime_exact - $btime > 60 && !empty($backup_history[$btime_exact])) {
									$changes = true;
									$backup_history[$btime] = $backup_history[$btime_exact];
									unset($backup_history[$btime_exact]);
									$btime_exact = $btime;
									$backup_times_by_nonce[$nonce] = $btime;
								}
								$btime = $btime_exact;
							}
							if ($btime <= 100) continue;
							
							// We need to set this so that if a second file is found in remote storage then the time will be picked up.
							$backup_times_by_nonce[$nonce] = $btime;

							if (empty($backup_history[$btime]['service_instance_ids']) || empty($backup_history[$btime]['service_instance_ids'][$method])) {
								$backup_history[$btime]['service_instance_ids'][$method] = array($instance_id);
								$changes = true;
							} elseif (!in_array($instance_id, $backup_history[$btime]['service_instance_ids'][$method])) {
								$backup_history[$btime]['service_instance_ids'][$method][] = $instance_id;
								$changes = true;
							}

							if (isset($remote_files[$filename])) {
								$remote_files[$filename][] = $method;
							} else {
								$remote_files[$filename] = array($method);
							}
							
							if (!in_array($nonce, $remote_nonces_found)) $remote_nonces_found[] = $nonce;
							
							if (!empty($entry['size'])) {
								if (empty($remote_sizes[$filename]) || $remote_sizes[$filename] < $entry['size']) $remote_sizes[$filename] = $entry['size'];
							}
						}
					} elseif (is_wp_error($files)) {
						foreach ($files->get_error_codes() as $code) {
							// Skip various codes which are not conditions to show to the user
							if (in_array($code, array('no_settings', 'no_addon', 'insufficient_php', 'no_listing'))) continue;
							$messages[] = array(
								'method' => $method,
								'desc' => $method_description,
								'code' => $code,
								'message' => $files->get_error_message($code),
								'data' => $files->get_error_data($code),
								'service_instance_id' => $instance_id,
							);
						}
					}
				}
			}
			$updraftplus->register_wp_http_option_hooks(false);
		}

		// Thirdly, see if there are any more files in the local directory than the ones already known about (possibly subject to a limitation specified via $only_add_this_file)
		if (!$handle = opendir($updraft_dir)) return array("Failed to open the internal directory ($updraft_dir)");

		while (false !== ($entry = readdir($handle))) {

			if ('.' == $entry || '..' == $entry) continue;

			$accepted_foreign = false;
			$potmessage = false;

			if (false !== $only_add_this_file && $entry != $only_add_this_file['file']) continue;

			if (preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?(\.(zip|gz|gz\.crypt))?$/i', $entry, $matches)) {
				// Interpret the time as one from the blog's local timezone, rather than as UTC
				// $matches[1] is YYYY-MM-DD-HHmm, to be interpreted as being the local timezone
				$btime = strtotime($matches[1]);
				if (!empty($gmt_offset)) $btime -= $gmt_offset * 3600;
				$nonce = $matches[2];
				$type = $matches[3];
				if ('db' == $type) {
					$type .= empty($matches[4]) ? '' : $matches[4];
					$index = 0;
				} else {
					$index = empty($matches[4]) ? '0' : max((int) $matches[4]-1, 0);
				}
				$itext = (0 == $index) ? '' : $index;
			} elseif (false != ($accepted_foreign = apply_filters('updraftplus_accept_foreign', false, $entry)) && false !== ($btime = apply_filters('updraftplus_foreign_gettime', false, $accepted_foreign, $entry))) {
				$nonce = substr(md5($entry), 0, 12);
				$type = (preg_match('/\.sql(\.(bz2|gz))?$/i', $entry) || preg_match('/-database-([-0-9]+)\.zip$/i', $entry) || preg_match('/backup_db_/', $entry)) ? 'db' : 'wpcore';
				$index = apply_filters('updraftplus_accepted_foreign_index', 0, $entry, $accepted_foreign);
				$itext = $index ? $index : '';
				$potmessage = array(
					'code' => 'foundforeign_'.md5($entry),
					'desc' => $entry,
					'method' => '',
					'message' => sprintf(__('Backup created by: %s.', 'updraftplus'), $accept[$accepted_foreign]['desc'])
				);
			} elseif ('.zip' == strtolower(substr($entry, -4, 4)) || preg_match('/\.sql(\.(bz2|gz))?$/i', $entry)) {
				$potmessage = array(
					'code' => 'possibleforeign_'.md5($entry),
					'desc' => $entry,
					'method' => '',
					'message' => __('This file does not appear to be an UpdraftPlus backup archive (such files are .zip or .gz files which have a name like: backup_(time)_(site name)_(code)_(type).(zip|gz)).', 'updraftplus').' <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('If this is a backup created by a different backup plugin, then UpdraftPlus Premium may be able to help you.', 'updraftplus').'</a>'
				);
				$messages[$potmessage['code']] = $potmessage;
				continue;
			} else {
				// The filename pattern does not indicate any sort of backup archive
				continue;
			}
			
			// The time from the filename does not include seconds. We need to identify the seconds to get the right time for storing it.
			if (isset($backup_times_by_nonce[$nonce])) {
				$btime_exact = $backup_times_by_nonce[$nonce];
				// If the btime we had was more than 60 seconds earlier, then this must be an increment - we then need to change the $backup_history array accordingly. We can pad the '60 second' test, as there's no option to run an increment more frequently than every 4 hours (though someone could run one manually from the CLI)
				if ($btime > 100 && $btime_exact - $btime > 60 && !empty($backup_history[$btime_exact])) {
					$changes = true;
					// We assume that $backup_history[$btime] is presently empty (except that the 'service_instance_ids' key may have been set earlier
					// Re-key array, indicating the newly-found time to be the start of the backup set
					$merge_services = false;
					if (!empty($backup_history[$btime]['service_instance_ids'])) {
						$merge_services = $backup_history[$btime]['service_instance_ids'];
					}
					$backup_history[$btime] = $backup_history[$btime_exact];
					if (is_array($merge_services)) {
						if (empty($backup_history[$btime]['service_instance_ids'])) {
							$backup_history[$btime]['service_instance_ids'] = $merge_services;
						} else {
							foreach ($merge_services as $service => $instance_ids) {
								if (empty($backup_history[$btime]['service_instance_ids'][$service])) {
									$backup_history[$btime]['service_instance_ids'][$service] = $instance_ids;
								} else {
									$backup_history[$btime]['service_instance_ids'][$service] = array_unique(array_merge($backup_history[$btime]['service_instance_ids'][$service], $instance_ids));
								}
							}
						}
					}
					unset($backup_history[$btime_exact]);
					$backup_times_by_nonce[$nonce] = $btime;
					$btime_exact = $btime;
				}
				$btime = $btime_exact;
			} else {
				$backup_times_by_nonce[$nonce] = $btime;
			}
			if ($btime <= 100) continue;
			$file_size = @filesize($updraft_dir.'/'.$entry);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

			if (!isset($backup_nonces_by_filename[$entry])) {
				$changes = true;
				if (is_array($potmessage)) $messages[$potmessage['code']] = $potmessage;
				if (is_array($only_add_this_file)) {
					if (isset($only_add_this_file['label'])) $backup_history[$btime]['label'] = $only_add_this_file['label'];
					$backup_history[$btime]['native'] = false;
				} elseif ('db' == $type && !$accepted_foreign) {
					list ($mess, $warn, $err, $info) = $updraftplus->analyse_db_file(false, array(), $updraft_dir.'/'.$entry, true);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
					if (!empty($info['label'])) {
						$backup_history[$btime]['label'] = $info['label'];
					}
					if (!empty($info['created_by_version'])) {
						$backup_history[$btime]['created_by_version'] = $info['created_by_version'];
					}
				}
			}

			// Make sure we have the right list of services, as an array
			$current_services = (!empty($backup_history[$btime]) && !empty($backup_history[$btime]['service'])) ? $backup_history[$btime]['service'] : array();
			if (is_string($current_services)) $current_services = array($current_services);
			if (!is_array($current_services)) $current_services = array();
			foreach ($current_services as $k => $v) {
				if ('none' === $v || '' == $v) unset($current_services[$k]);
			}
			
			// If the file (the one we just found locally) was also found in the scan of remote storage...
			if (!empty($remote_files[$entry])) {
				// ... store the record if all services which previously stored it still do
				if (0 == count(array_diff($current_services, $remote_files[$entry]))) {
					if ($current_services != $remote_files[$entry]) $changes = true;
					$backup_history[$btime]['service'] = $remote_files[$entry];
				} else {
					// There are services in $current_services which are not in $remote_files[$entry]
					// This can be because the backup set files are split across different services
					$changes = true;
					$backup_history[$btime]['service'] = array_unique(array_merge($current_services, $remote_files[$entry]));
				}
				// Get the right size (our local copy may be too small)
				if (!empty($remote_sizes[$entry]) && $remote_sizes[$entry] > $file_size) {
					$file_size = $remote_sizes[$entry];
					$changes = true;
				}
				// Remove from $remote_files, so that we can later see what was left over (i.e. $remote_files will exclude files which are present locally).
				unset($remote_files[$entry]);
				
			} elseif ($remote_scan && !in_array($nonce, $remote_nonces_found)) {
				// The file is not known remotely, and neither is any other from the same set, and a remote scan was done
				
				if (!empty($backup_history[$btime])) {
					if (empty($backup_history[$btime]['service']) || ('none' !== $backup_history[$btime]['service'] && '' !== $backup_history[$btime]['service'] && array('none') !== $backup_history[$btime]['service'])) {
						$backup_history[$btime]['service'] = 'none';
						$changes = true;
					}
				} else {
					$backup_history[$btime]['service'] = 'none';
					$changes = true;
				}
			}

			$backup_history[$btime][$type][$index] = $entry;
			
			if (!empty($backup_history[$btime][$type.$itext.'-size']) && $backup_history[$btime][$type.$itext.'-size'] < $file_size) {
				$backup_history[$btime][$type.$itext.'-size'] = $file_size;
				$changes = true;
			} elseif (empty($backup_history[$btime][$type.$itext.'-size']) && $file_size > 0) {
				$backup_history[$btime][$type.$itext.'-size'] = $file_size;
				$changes = true;
			}
			
			$backup_history[$btime]['nonce'] = $nonce;
			if (!empty($accepted_foreign)) $backup_history[$btime]['meta_foreign'] = $accepted_foreign;
		}

		// Fourthly: are there any files found in remote storage that are not stored locally?
		// If so, then we compare $remote_files with $backup_nonces_by_filename / $backup_times_by_nonce, and adjust $backup_history

		foreach ($remote_files as $file => $services) {
			if (!preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?(\.(zip|gz|gz\.crypt))?$/i', $file, $matches)) continue;
			
			$nonce = $matches[2];
			$type = $matches[3];
			
			if ('db' == $type) {
				$index = 0;
				$type .= !empty($matches[4]) ? $matches[4] : '';
			} else {
				$index = empty($matches[4]) ? '0' : max((int) $matches[4]-1, 0);
			}
			
			$itext = (0 == $index) ? '' : $index;
			$btime = strtotime($matches[1]);
			if (!empty($gmt_offset)) $btime -= $gmt_offset * 3600;

			// N.B. We don't need to check if the backup set needs re-keying by an earlier time here, because that was already done when processing the whole list of remote files, above.
			if (isset($backup_times_by_nonce[$nonce])) $btime = $backup_times_by_nonce[$nonce];

			if ($btime <= 100) continue;

			// Remember that at this point, we already know that the file is not stored locally (else it would have been pruned earlier from $remote_files)
			// The check for a new set needs to take into account that $backup_history[$btime]['service_instance_ids'] may have been created further up this method
			if (isset($backup_history[$btime]) && array('service_instance_ids') !== array_keys($backup_history[$btime])) {
				if (!isset($backup_history[$btime]['service']) || (is_array($backup_history[$btime]['service']) && $backup_history[$btime]['service'] !== $services) || (is_string($backup_history[$btime]['service']) && (1 != count($services) || $services[0] !== $backup_history[$btime]['service']))) {
					$changes = true;
					if (isset($backup_history[$btime]['service'])) {
						$existing_services = is_array($backup_history[$btime]['service']) ? $backup_history[$btime]['service'] : array($backup_history[$btime]['service']);
						$backup_history[$btime]['service'] = array_unique(array_merge($services, $existing_services));
						foreach ($backup_history[$btime]['service'] as $k => $v) {
							if ('none' === $v || '' == $v) unset($backup_history[$btime]['service'][$k]);
						}
					} else {
						$backup_history[$btime]['service'] = $services;
					}
					$backup_history[$btime]['nonce'] = $nonce;
				}
				
				if (!isset($backup_history[$btime][$type][$index])) {
					$changes = true;
					$backup_history[$btime][$type][$index] = $file;
					$backup_history[$btime]['nonce'] = $nonce;
					if (!empty($remote_sizes[$file])) $backup_history[$btime][$type.$itext.'-size'] = $remote_sizes[$file];
				}
			} else {
				$changes = true;
				$backup_history[$btime]['service'] = $services;
				$backup_history[$btime][$type][$index] = $file;
				$backup_history[$btime]['nonce'] = $nonce;
				if (!empty($remote_sizes[$file])) $backup_history[$btime][$type.$itext.'-size'] = $remote_sizes[$file];
				$backup_history[$btime]['native'] = false;
				$messages['nonnative'] = array(
					'message' => __('One or more backups has been added from scanning remote storage; note that these backups will not be automatically deleted through the "retain" settings; if/when you wish to delete them then you must do so manually.', 'updraftplus'),
					'code' => 'nonnative',
					'desc' => '',
					'method' => ''
				);
			}

		}
		
		// This is for consistency - if something is no longer present in the service list, then neither should it be in the ids list
		foreach ($backup_history as $btime => $bdata) {
			if (!empty($bdata['service_instance_ids'])) {
				foreach ($bdata['service_instance_ids'] as $method => $instance_ids) {
					if ((is_array($bdata['service']) && !in_array($method, $bdata['service'])) || (is_string($bdata['service']) && $method !== $bdata['service'])) {
						unset($backup_history[$btime]['service_instance_ids'][$method]);
						$changes = true;
					}
				}
			}
		}

		$more_backup_history = apply_filters('updraftplus_more_rebuild', $backup_history);
		
		if ($more_backup_history) {
			$backup_history = $more_backup_history;
			$changes = true;
		}

		if ($changes) self::save_history($backup_history);

		return $messages;

	}
	
	/**
	 * This function will look through the backup history and return the nonce of the latest full backup that has everything that is set in the UpdraftPlus settings to be backed up (this will exclude full backups sent to another site, e.g. for a migration or clone)
	 *
	 * @return String - the backup nonce of a full backup or an empty string if none are found
	 */
	public static function get_latest_full_backup() {
		
		$backup_history = self::get_history();

		global $updraftplus;
		
		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

		foreach ($backupable_entities as $key => $info) {
			if (!UpdraftPlus_Options::get_updraft_option("updraft_include_$key", false)) {
				unset($backupable_entities[$key]);
			}
		}
		
		foreach ($backup_history as $key => $backup) {
			
			$remote_sent = !empty($backup['service']) && ((is_array($backup['service']) && in_array('remotesend', $backup['service'])) || 'remotesend' === $backup['service']);
			if ($remote_sent) continue;
			
			foreach ($backupable_entities as $key => $info) {
				if (!isset($backup[$key])) continue 2;
			}
			
			return $backup['nonce'];

		}

		return '';
	}

	/**
	 * This function will look through the backup history and return the nonce of the latest backup that can be used for an incremental backup (this will exclude full backups sent to another site, e.g. for a migration or clone)
	 *
	 * @param array $entities - an array of file entities that are included in this job
	 *
	 * @return String         - the backup nonce of a full backup or an empty string if none are found
	 */
	public static function get_latest_backup($entities) {

		if (empty($entities)) return '';

		$backup_history = self::get_history();

		foreach ($backup_history as $backup) {

			$remote_sent = !empty($backup['service']) && ((is_array($backup['service']) && in_array('remotesend', $backup['service'])) || 'remotesend' === $backup['service']);
			if ($remote_sent) continue;

			foreach ($entities as $type) {
				if (!isset($backup[$type])) continue 2;
			}

			return $backup['nonce'];

		}

		return '';
	}

	/**
	 * This function will look through the backup history and return an array of entity types found in the history
	 *
	 * @return array - an array of backup entities found in the history or an empty array if there are none
	 */
	public static function get_existing_backup_entities() {

		$backup_history = self::get_history();

		global $updraftplus;

		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

		$entities = array();

		foreach ($backup_history as $key => $backup) {

			$remote_sent = !empty($backup['service']) && ((is_array($backup['service']) && in_array('remotesend', $backup['service'])) || 'remotesend' === $backup['service']);
			if ($remote_sent) continue;

			foreach ($backupable_entities as $key => $info) {
				if (isset($backup[$key])) $entities[] = $key;
			}
		}

		return $entities;
	}
	
	/**
	 * Save a backup into the history
	 *
	 * @param Integer $backup_time  - the time of the backup
	 * @param Array	  $backup_array - the backup
	 */
	public static function save_backup($backup_time, $backup_array) {
		$backup_history = self::get_history();

		$backup_history[$backup_time] = isset($backup_history[$backup_time]) ? apply_filters('updraftplus_merge_backup_history', $backup_array, $backup_history[$backup_time]) : $backup_array;
		
		self::save_history($backup_history, false);
	}
}
