<?php
	if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');
?>
<div class="advanced_tools site_info">
	<h3><?php _e('Site information', 'updraftplus');?></h3>
	<table>
	<?php

	if (function_exists('php_uname')) {
		// It appears (Mar 2015) that some mod_security distributions block the output of the string el6.x86_64 in PHP output, on the silly assumption that only hackers are interested in knowing what environment PHP is running on.
		$uname_info = @php_uname('s').' '.@php_uname('n').' ';// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$release_name = @php_uname('r');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if (preg_match('/^(.*)\.(x86_64|[3456]86)$/', $release_name, $matches)) {
			$release_name = $matches[1].' ';
		} else {
			$release_name = '';
		}

		// In case someone does something similar with just the processor type string
		$mtype = @php_uname('m');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ('x86_64' == $mtype) {
			$mtype = '64-bit';
		} elseif (preg_match('/^i([3456]86)$/', $mtype, $matches)) {
			$mtype = $matches[1];
		}

		$uname_info .= $release_name.$mtype.' '.@php_uname('v');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
	} else {
		$uname_info = PHP_OS;
	}
	
	$web_server = $_SERVER["SERVER_SOFTWARE"];

	$updraftplus_admin->settings_debugrow(__('Web server:', 'updraftplus'), htmlspecialchars($web_server).' ('.htmlspecialchars($uname_info).')');

	if (defined('UPDRAFTPLUS_THIS_IS_CLONE')) {
		$response = wp_remote_get('http://169.254.169.254/metadata/v1/user-data', array('timeout' => 2));
		if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
			$json_body = wp_remote_retrieve_body($response);
			$metadata = json_decode($json_body, true);
			if (isset($metadata['image_id'])) $updraftplus_admin->settings_debugrow(__('UpdraftClone image:', 'updraftplus'), htmlspecialchars($metadata['image_id']));
		}
	}

	$updraftplus_admin->settings_debugrow('ABSPATH:', htmlspecialchars(ABSPATH));
	$updraftplus_admin->settings_debugrow('WP_CONTENT_DIR:', htmlspecialchars(WP_CONTENT_DIR));
	$updraftplus_admin->settings_debugrow('WP_PLUGIN_DIR:', htmlspecialchars(WP_PLUGIN_DIR));
	$updraftplus_admin->settings_debugrow('Table prefix:', htmlspecialchars($updraftplus->get_table_prefix()));

	$updraftplus_admin->settings_debugrow(__('Web-server disk space in use by UpdraftPlus', 'updraftplus').':', '<span class="updraft_diskspaceused">'.UpdraftPlus_Filesystem_Functions::get_disk_space_used('updraft').'</span> <a class="updraft_diskspaceused_update" href="'.UpdraftPlus::get_current_clean_url().'">'.__('refresh', 'updraftplus').'</a>');

	$peak_memory_usage = memory_get_peak_usage(true)/1048576;
	$memory_usage = memory_get_usage(true)/1048576;
	$updraftplus_admin->settings_debugrow(__('Peak memory usage', 'updraftplus').':', $peak_memory_usage.' MB');
	$updraftplus_admin->settings_debugrow(__('Current memory usage', 'updraftplus').':', $memory_usage.' MB');
	$updraftplus_admin->settings_debugrow(__('Memory limit', 'updraftplus').':', htmlspecialchars(ini_get('memory_limit')));
	$updraftplus_admin->settings_debugrow(sprintf(__('%s version:', 'updraftplus'), 'PHP'), htmlspecialchars(phpversion()).' - <a href="admin-ajax.php?page=updraftplus&amp;action=updraft_ajax&amp;subaction=phpinfo&amp;nonce='.wp_create_nonce('updraftplus-credentialtest-nonce').'" id="updraftplus-phpinfo">'.__('show PHP information (phpinfo)', 'updraftplus').'</a>');
	
	$db_version = $wpdb->get_var('SELECT VERSION()');
	// WPDB::db_version() uses mysqli_get_server_info() ; see: https://github.com/joomla/joomla-cms/issues/9062
	if ('' == $db_version) $db_version = $wpdb->db_version();
	
	$updraftplus_admin->settings_debugrow(sprintf(__('%s version:', 'updraftplus'), 'MySQL'), htmlspecialchars($db_version));
	$updraftplus_admin->settings_debugrow(__('Current SQL mode:', 'updraftplus'), htmlspecialchars($wpdb->get_var('SELECT @@GLOBAL.sql_mode')));
	if (function_exists('curl_version') && function_exists('curl_exec')) {
		$cv = curl_version();
		$cvs = $cv['version'].' / SSL: '.$cv['ssl_version'].' / libz: '.$cv['libz_version'];
	} else {
		$cvs = __('Not installed', 'updraftplus').' ('.__('required for some remote storage providers', 'updraftplus').')';
	}
	$updraftplus_admin->settings_debugrow(sprintf(__('%s version:', 'updraftplus'), 'Curl'), htmlspecialchars($cvs));
	$updraftplus_admin->settings_debugrow(sprintf(__('%s version:', 'updraftplus'), 'OpenSSL'), defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : '-');
	$updraftplus_admin->settings_debugrow('MCrypt:', function_exists('mcrypt_encrypt') ? __('Yes') : __('No'));
	
	if (version_compare(PHP_VERSION, '5.2.0', '>=') && extension_loaded('zip')) {
		$ziparchive_exists = __('Yes', 'updraftplus');
	} else {
		// First do class_exists, because method_exists still sometimes segfaults due to a rare PHP bug
		$ziparchive_exists = (class_exists('ZipArchive') && method_exists('ZipArchive', 'addFile')) ? __('Yes', 'updraftplus') : __('No', 'updraftplus');
	}
	$updraftplus_admin->settings_debugrow('ZipArchive::addFile:', $ziparchive_exists);
	$binzip = $updraftplus->find_working_bin_zip(false, false);
	$updraftplus_admin->settings_debugrow(__('zip executable found:', 'updraftplus'), ((is_string($binzip)) ? __('Yes').': '.$binzip : __('No')));
	$hosting_bytes_free = $updraftplus->get_hosting_disk_quota_free();
	if (is_array($hosting_bytes_free)) {
		$perc = round(100*$hosting_bytes_free[1]/(max($hosting_bytes_free[2], 1)), 1);
		$updraftplus_admin->settings_debugrow(__('Free disk space in account:', 'updraftplus'), sprintf(__('%s (%s used)', 'updraftplus'), round($hosting_bytes_free[3]/1048576, 1)." MB", "$perc %"));
	}
	
	if (function_exists('apache_get_modules')) {
		$apache_info = '';
		$apache_modules = apache_get_modules();
		if (is_array($apache_modules)) {
			sort($apache_modules, SORT_STRING);
			foreach ($apache_modules as $mod) {
				if (0 === strpos($mod, 'mod_')) {
					$apache_info .= ', '.substr($mod, 4);
				} else {
					$apache_info .= ', '.$mod;
				}
			}
		}
		$apache_info = substr($apache_info, 2);
		$updraftplus_admin->settings_debugrow(__('Apache modules', 'updraftplus').':', $apache_info);
	}
	
	if (empty($options['suppress_plugins_for_debugging'])) {
		$updraftplus_admin->settings_debugrow(__('Plugins for debugging:', 'updraftplus'), '<a href="'.wp_nonce_url(self_admin_url('update.php?action=install-plugin&amp;updraftplus_noautobackup=1&amp;plugin=wp-crontrol'), 'install-plugin_wp-crontrol').'">WP Crontrol</a> | <a href="'.wp_nonce_url(self_admin_url('update.php?action=install-plugin&amp;updraftplus_noautobackup=1&amp;plugin=query-monitor'), 'install-plugin_query-monitor').'">Query Monitor</a> | <a href="'.wp_nonce_url(self_admin_url('update.php?action=install-plugin&amp;updraftplus_noautobackup=1&amp;plugin=sql-executioner'), 'install-plugin_sql-executioner').'">SQL Executioner</a> | <a href="'.wp_nonce_url(self_admin_url('update.php?action=install-plugin&amp;updraftplus_noautobackup=1&amp;plugin=wp-file-manager'), 'install-plugin_wp-file-manager').'">WP Filemanager</a>');
	}

	$updraftplus_admin->settings_debugrow("HTTP Get: ", '<input id="updraftplus_httpget_uri" type="text" class="call-action"> <a href="'.UpdraftPlus::get_current_clean_url().'" id="updraftplus_httpget_go">'.__('Fetch', 'updraftplus').'</a> <a href="'.UpdraftPlus::get_current_clean_url().'" id="updraftplus_httpget_gocurl">'.__('Fetch', 'updraftplus').' (Curl)</a><p id="updraftplus_httpget_results"></p>');

	$updraftplus_admin->settings_debugrow(__("Call WordPress action:", 'updraftplus'), '<input id="updraftplus_callwpaction" type="text" class="call-action"> <a href="'.UpdraftPlus::get_current_clean_url().'" id="updraftplus_callwpaction_go">'.__('Call', 'updraftplus').'</a><div id="updraftplus_callwpaction_results"></div>');

	$updraftplus_admin->settings_debugrow('Site ID:', '(used to identify any Vault connections) <span id="updraft_show_sid">'.htmlspecialchars($updraftplus->siteid()).'</span> - <a href="'.UpdraftPlus::get_current_clean_url().'" id="updraft_reset_sid">'.__('reset', 'updraftplus')."</a>");
	
	$updraftplus_admin->settings_debugrow('', '<a href="admin-ajax.php?page=updraftplus&amp;action=updraft_ajax&amp;subaction=backuphistoryraw&amp;nonce='.wp_create_nonce('updraftplus-credentialtest-nonce').'" id="updraftplus-rawbackuphistory">'.__('Show raw backup and file list', 'updraftplus').'</a><br><span class="hidden-in-updraftcentral"><a id="updraftplus-remote-rescan-debug" href="#">'.__('Rescan remote storage', 'updraftplus').' - '.__('log results to console', 'updraftplus').'</a></span>');
	
	?>
	</table>
</div>
