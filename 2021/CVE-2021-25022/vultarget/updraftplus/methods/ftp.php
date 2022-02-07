<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// Converted to array options: yes
// Converted to job_options: yes

// Migrate options to new-style storage - May 2014
if (!is_array(UpdraftPlus_Options::get_updraft_option('updraft_ftp')) && '' != UpdraftPlus_Options::get_updraft_option('updraft_server_address', '')) {
	$opts = array(
		'user' => UpdraftPlus_Options::get_updraft_option('updraft_ftp_login'),
		'pass' => UpdraftPlus_Options::get_updraft_option('updraft_ftp_pass'),
		'host' => UpdraftPlus_Options::get_updraft_option('updraft_server_address'),
		'path' => UpdraftPlus_Options::get_updraft_option('updraft_ftp_remote_path'),
		'passive' => true
	);
	UpdraftPlus_Options::update_updraft_option('updraft_ftp', $opts);
	UpdraftPlus_Options::delete_updraft_option('updraft_server_address');
	UpdraftPlus_Options::delete_updraft_option('updraft_ftp_pass');
	UpdraftPlus_Options::delete_updraft_option('updraft_ftp_remote_path');
	UpdraftPlus_Options::delete_updraft_option('updraft_ftp_login');
}

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

class UpdraftPlus_BackupModule_ftp extends UpdraftPlus_BackupModule {

	/**
	 * Get FTP object with parameters set
	 *
	 * @param  String  $server 			 Specify Server
	 * @param  String  $user 			 Specify Username
	 * @param  String  $pass 			 Specify Password
	 * @param  Boolean $disable_ssl		 Indicate whether to disable SSL
	 * @param  Boolean $disable_verify	 Indicate whether to disable verifiction
	 * @param  Boolean $use_server_certs Indicate whether to use server certificates
	 * @param  Boolean $passive 		 Indicate whether to use passive FTP mode
	 * @return Array
	 */
	private function getFTP($server, $user, $pass, $disable_ssl = false, $disable_verify = true, $use_server_certs = false, $passive = true) {

		if ('' == trim($server) || '' == trim($user) || '' == trim($pass)) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), 'FTP'));

		if (!class_exists('UpdraftPlus_ftp_wrapper')) include_once(UPDRAFTPLUS_DIR.'/includes/ftp.class.php');

		$port = 21;
		if (preg_match('/^(.*):(\d+)$/', $server, $matches)) {
			$server = $matches[1];
			$port = $matches[2];
		}

		$ftp = new UpdraftPlus_ftp_wrapper($server, $user, $pass, $port);

		if ($disable_ssl) $ftp->ssl = false;
		$ftp->use_server_certs = $use_server_certs;
		$ftp->disable_verify = $disable_verify;
		$ftp->passive = ($passive) ? true : false;

		return $ftp;

	}
	
	/**
	 * WordPress options filter, sanitising the FTP options saved from the options page
	 *
	 * @param Array $settings - the options, prior to sanitisation
	 *
	 * @return Array - the sanitised options for saving
	 */
	public function options_filter($settings) {
		if (is_array($settings) && !empty($settings['version']) && !empty($settings['settings'])) {
			foreach ($settings['settings'] as $instance_id => $instance_settings) {
				if (!empty($instance_settings['host']) && preg_match('#ftp(es|s)?://(.*)#i', $instance_settings['host'], $matches)) {
					$settings['settings'][$instance_id]['host'] = rtrim($matches[2], "/ \t\n\r\0x0B");
				}
				if (isset($instance_settings['pass'])) {
					$settings['settings'][$instance_id]['pass'] = trim($instance_settings['pass'], "\n\r\0\x0B");
				}
			}
		}
		return $settings;
	}
	
	public function get_supported_features() {
		// The 'multi_options' options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	public function get_default_options() {
		return array(
			'host' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'passive' => 1
		);
	}
	
	public function backup($backup_array) {

		global $updraftplus;

		$opts = $this->get_options();

		$ftp = $this->getFTP(
			$opts['host'],
			$opts['user'],
			$opts['pass'],
			$updraftplus->get_job_option('updraft_ssl_nossl'),
			$updraftplus->get_job_option('updraft_ssl_disableverify'),
			$updraftplus->get_job_option('updraft_ssl_useservercerts'),
			$opts['passive']
		);

		if (is_wp_error($ftp) || !$ftp->connect()) {
			if (is_wp_error($ftp)) {
				$updraftplus->log_wp_error($ftp);
			} else {
				$this->log("Failure: we did not successfully log in with those credentials.");
			}
			$this->log(__("login failure", 'updraftplus'), 'error');
			return false;
		}

		// $ftp->make_dir(); we may need to recursively create dirs? TODO

		$updraft_dir = $updraftplus->backups_dir_location().'/';

		$ftp_remote_path = trailingslashit($opts['path']);
		foreach ($backup_array as $file) {
			$fullpath = $updraft_dir.$file;
			$this->log("upload attempt: $file -> ftp://".$opts['user']."@".$opts['host']."/${ftp_remote_path}${file}");
			$timer_start = microtime(true);
			$size_k = round(filesize($fullpath)/1024, 1);
			// Note :Setting $resume to true unnecessarily is not meant to be a problem. Only ever (Feb 2014) seen one weird FTP server where calling SIZE on a non-existent file did create a problem. So, this code just helps that case. (the check for non-empty upload_status[p] is being cautious.
			$upload_status = $updraftplus->jobdata_get('uploading_substatus');
			if (0 == $updraftplus->current_resumption || (is_array($upload_status) && !empty($upload_status['p']) && 0 == $upload_status['p'])) {
				$resume = false;
			} else {
				$resume = true;
			}

			if ($ftp->put($fullpath, $ftp_remote_path.$file, FTP_BINARY, $resume, $updraftplus)) {
				$this->log("upload attempt successful (".$size_k."KB in ".(round(microtime(true)-$timer_start, 2)).'s)');
				$updraftplus->uploaded_file($file);
			} else {
				$this->log("ERROR: FTP upload failed");
				$this->log(__("upload failed", 'updraftplus'), 'error');
			}
		}

		return array('ftp_object' => $ftp, 'ftp_remote_path' => $ftp_remote_path);
	}

	public function listfiles($match = 'backup_') {
		global $updraftplus;

		$opts = $this->get_options();

		$ftp = $this->getFTP(
			$opts['host'],
			$opts['user'],
			$opts['pass'],
			$updraftplus->get_job_option('updraft_ssl_nossl'),
			$updraftplus->get_job_option('updraft_ssl_disableverify'),
			$updraftplus->get_job_option('updraft_ssl_useservercerts'),
			$opts['passive']
		);

		if (is_wp_error($ftp)) return $ftp;

		if (!$ftp->connect()) return new WP_Error('ftp_login_failed', sprintf(__("%s login failure", 'updraftplus'), 'FTP'));

		$ftp_remote_path = $opts['path'];
		if ($ftp_remote_path) $ftp_remote_path = trailingslashit($ftp_remote_path);

		$dirlist = $ftp->dir_list($ftp_remote_path);
		if (!is_array($dirlist)) return array();

		$results = array();

		foreach ($dirlist as $k => $path) {

			if ($ftp_remote_path) {
				// Feb 2015 - found a case where the directory path was not prefixed on
				if (0 !== strpos($path, $ftp_remote_path) && (false !== strpos('/', $ftp_remote_path) && false !== strpos('\\', $ftp_remote_path))) continue;
				if (0 === strpos($path, $ftp_remote_path)) $path = substr($path, strlen($ftp_remote_path));
				// if (0 !== strpos($path, $ftp_remote_path)) continue;
				// $path = substr($path, strlen($ftp_remote_path));
				if (0 === strpos($path, $match)) $results[]['name'] = $path;
			} else {
				if ('/' == substr($path, 0, 1)) $path = substr($path, 1);
				if (false !== strpos($path, '/')) continue;
				if (0 === strpos($path, $match)) $results[]['name'] = $path;
			}

			unset($dirlist[$k]);
		}

		// ftp_nlist() doesn't return file sizes. rawlist() does, but is tricky to parse. So, we get the sizes manually.
		foreach ($results as $ind => $name) {
			$size = $ftp->size($ftp_remote_path.$name['name']);
			if (0 === $size) {
				unset($results[$ind]);
			} elseif ($size>0) {
				$results[$ind]['size'] = $size;
			}
		}

		return $results;

	}

	/**
	 * Delete a single file from the service using FTP protocols
	 *
	 * @param Array $files    - array of file names to delete
	 * @param Array $ftparr   - FTP details/credentials
	 * @param Array $sizeinfo - unused here
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete($files, $ftparr = array(), $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $sizeinfo unused

		global $updraftplus;
		if (is_string($files)) $files = array($files);

		$opts = $this->get_options();

		if (is_array($ftparr) && isset($ftparr['ftp_object'])) {
			$ftp = $ftparr['ftp_object'];
		} else {
			$ftp = $this->getFTP(
				$opts['host'],
				$opts['user'],
				$opts['pass'],
				$updraftplus->get_job_option('updraft_ssl_nossl'),
				$updraftplus->get_job_option('updraft_ssl_disableverify'),
				$updraftplus->get_job_option('updraft_ssl_useservercerts'),
				$opts['passive']
			);

			if (is_wp_error($ftp) || !$ftp->connect()) {
				if (is_wp_error($ftp)) $updraftplus->log_wp_error($ftp);
				$this->log("Failure: we did not successfully log in with those credentials (host=".$opts['host'].").");
				return 'authentication_fail';
			}

		}

		$ftp_remote_path = isset($ftparr['ftp_remote_path']) ? $ftparr['ftp_remote_path'] : trailingslashit($opts['path']);

		$ret = true;
		foreach ($files as $file) {
			if (@$ftp->delete($ftp_remote_path.$file)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$this->log("delete: succeeded (${ftp_remote_path}${file})");
			} else {
				$this->log("delete: failed (${ftp_remote_path}${file})");
				$ret = 'file_delete_error';
			}
		}
		return $ret;

	}

	public function download($file) {

		global $updraftplus;

		$opts = $this->get_options();

		$ftp = $this->getFTP(
			$opts['host'],
			$opts['user'],
			$opts['pass'],
			$updraftplus->get_job_option('updraft_ssl_nossl'),
			$updraftplus->get_job_option('updraft_ssl_disableverify'),
			$updraftplus->get_job_option('updraft_ssl_useservercerts'),
			$opts['passive']
		);

		if (is_wp_error($ftp)) {
			$this->log('Failure to get FTP object: '.$ftp->get_error_code().': '.$ftp->get_error_message());
			$this->log($ftp->get_error_message().' ('.$ftp->get_error_code().')', 'error');
			return false;
		}

		if (!$ftp->connect()) {
			$this->log('Failure: we did not successfully log in with those credentials.');
			$this->log(__('login failure', 'updraftplus'), 'error');
			return false;
		}

		// $ftp->make_dir(); we may need to recursively create dirs? TODO
		
		$ftp_remote_path = trailingslashit($opts['path']);
		$fullpath = $updraftplus->backups_dir_location().'/'.$file;

		$resume = false;
		if (file_exists($fullpath)) {
			$resume = true;
			$this->log("File already exists locally; will resume: size: ".filesize($fullpath));
		}

		return $ftp->get($fullpath, $ftp_remote_path.$file, FTP_BINARY, $resume, $updraftplus);
	}

	private function ftp_possible() {
		$funcs_disabled = array();
		foreach (array('ftp_connect', 'ftp_login', 'ftp_nb_fput') as $func) {
			if (!function_exists($func)) $funcs_disabled['ftp'][] = $func;
		}
		$funcs_disabled = apply_filters('updraftplus_ftp_possible', $funcs_disabled);
		return (0 == count($funcs_disabled)) ? true : $funcs_disabled;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		global $updraftplus_admin;
		global $updraftplus;

		$classes = $this->get_css_classes(false);
		
		?>
		<tr class="<?php echo $classes . ' ' . 'ftp_pre_config_container';?>">
			<td colspan="2">
				<h3><?php echo 'FTP'; ?></h3>
				<?php
				$possible = $this->ftp_possible();
				
				if (is_array($possible)) {
					// Check requirements.
					global $updraftplus_admin;
					$trans = array(
						'ftp' => __('regular non-encrypted FTP', 'updraftplus'),
						'ftpsslimplicit' => __('encrypted FTP (implicit encryption)', 'updraftplus'),
						'ftpsslexplicit' => __('encrypted FTP (explicit encryption)', 'updraftplus')
					);
					foreach ($possible as $type => $missing) {
					$updraftplus_admin->show_double_warning('<strong>'.__('Warning', 'updraftplus').':</strong> '. sprintf(__("Your web server's PHP installation has these functions disabled: %s.", 'updraftplus'), implode(', ', $missing)).' '.sprintf(__('Your hosting company must enable these functions before %s can work.', 'updraftplus'), $trans[$type]), 'ftp');
					}
				}

				?>

				<em><?php echo '<p>' . apply_filters('updraft_sftp_ftps_notice', '<strong>'.htmlspecialchars(__('Only non-encrypted FTP is supported by regular UpdraftPlus.')).'</strong> <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('If you want encryption (e.g. you are storing sensitive business data), then an add-on is available in the Premium version.', 'updraftplus')).'</a></p>'; ?></em>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {

		ob_start();
	
		$classes = $this->get_css_classes();
		
		?>

		<tr class="<?php echo $classes;?>">
			<th><?php _e('FTP server', 'updraftplus');?>:</th>
			<td><input class="updraft_input--wide" type="text" size="40" data-updraft_settings_test="server" <?php $this->output_settings_field_name_and_id('host');?> value="{{host}}" /></td>
		</tr>
		
		<tr class="<?php echo $classes;?>">
			<th><?php _e('FTP login', 'updraftplus');?>:</th>
			<td><input class="updraft_input--wide" type="text" size="40" data-updraft_settings_test="login" <?php $this->output_settings_field_name_and_id('user');?> value="{{user}}" /></td>
		</tr>
		
		<tr class="<?php echo $classes;?>">
			<th><?php _e('FTP password', 'updraftplus');?>:</th>
			<td><input class="updraft_input--wide" type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" size="40" data-updraft_settings_test="pass" <?php $this->output_settings_field_name_and_id('pass');?> value="{{pass}}" /></td>
		</tr>
		
		<tr class="<?php echo $classes;?>">
			<th><?php _e('Remote path', 'updraftplus');?>:</th>
			<td><input title="<?php _e('Needs to already exist', 'updraftplus'); ?>" class="updraft_input--wide" type="text" size="64" data-updraft_settings_test="path" <?php $this->output_settings_field_name_and_id('path');?> value="{{path}}" /> <em><?php _e('Needs to already exist', 'updraftplus');?></em></td>
		</tr>
		
		<tr class="<?php echo $classes;?>">
			<th><?php _e('Passive mode', 'updraftplus');?>:</th>
			<td>
			<input title="<?php echo __('Almost all FTP servers will want passive mode; but if you need active mode, then uncheck this.', 'updraftplus');?>" type="checkbox" data-updraft_settings_test="passive" <?php $this->output_settings_field_name_and_id('passive');?> value="1" {{#ifeq '1' passive}}checked="checked"{{/ifeq}}> <br><em><?php echo __('Almost all FTP servers will want passive mode; but if you need active mode, then uncheck this.', 'updraftplus');?></em></td>
		</tr>
		
		<?php
		
		echo $this->get_test_button_html('FTP');
		
		return ob_get_clean();
		
	}

	/**
	 * Perform a test of user-supplied credentials, and echo the result
	 *
	 * @param Array $posted_settings - settings to test
	 */
	public function credentials_test($posted_settings) {

		$server = $posted_settings['server'];
		$login = $posted_settings['login'];
		$pass = $posted_settings['pass'];
		$path = $posted_settings['path'];
		$nossl = $posted_settings['nossl'];
		$passive = empty($posted_settings['passive']) ? false : true;
		
		$disable_verify = $posted_settings['disableverify'];
		$use_server_certs = $posted_settings['useservercerts'];

		if (empty($server)) {
			_e('Failure: No server details were given.', 'updraftplus');
			return;
		}
		if (empty($login)) {
			printf(__('Failure: No %s was given.', 'updraftplus'), __('login', 'updraftplus'));
			return;
		}
		if (empty($pass)) {
			printf(__('Failure: No %s was given.', 'updraftplus'), __('password', 'updraftplus'));
			return;
		}

		if (preg_match('#ftp(es|s)?://(.*)#i', $server, $matches)) $server = untrailingslashit($matches[2]);

		// $ftp = $this->getFTP($server, $login, $pass, $nossl, $disable_verify, $use_server_certs);
		$ftp = $this->getFTP($server, $login, $pass, $nossl, $disable_verify, $use_server_certs, $passive);

		if (!$ftp->connect()) {
			_e('Failure: we did not successfully log in with those credentials.', 'updraftplus');
			return;
		}
		// $ftp->make_dir(); we may need to recursively create dirs? TODO

		$file = md5(rand(0, 99999999)).'.tmp';
		$fullpath = trailingslashit($path).$file;
		
		if ($ftp->put(ABSPATH.WPINC.'/version.php', $fullpath, FTP_BINARY, false, true)) {
			echo __("Success: we successfully logged in, and confirmed our ability to create a file in the given directory (login type:", 'updraftplus')." ".$ftp->login_type.')';
			@$ftp->delete($fullpath);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {
			_e('Failure: we successfully logged in, but were not able to create a file in the given directory.', 'updraftplus');
			if (!empty($ftp->ssl)) {
				echo ' '.__('This is sometimes caused by a firewall - try turning off SSL in the expert settings, and testing again.', 'updraftplus');
			}
		}

	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['host']) && isset($opts['user']) && '' != $opts['user']) return true;
		return false;
	}
}
