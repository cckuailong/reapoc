<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
	$dbvar_maxtime  = DUP_DB::getVariable('wait_timeout');
	$dbvar_maxpacks = DUP_DB::getVariable('max_allowed_packet');
	$dbvar_maxtime  = is_null($dbvar_maxtime)  ? __("unknow", 'duplicator') : $dbvar_maxtime;
	$dbvar_maxpacks = is_null($dbvar_maxpacks) ? __("unknow", 'duplicator') : $dbvar_maxpacks;

	$abs_path = duplicator_get_abs_path();
	$space = @disk_total_space($abs_path);
	$space_free = @disk_free_space($abs_path);
	$perc = @round((100/$space)*$space_free,2);
	$mysqldumpPath = DUP_DB::getMySqlDumpPath();
	$mysqlDumpSupport = ($mysqldumpPath) ? $mysqldumpPath : 'Path Not Found';

	$client_ip_address = DUP_Server::getClientIP();
	$error_log_path = ini_get('error_log');
?>

<!-- ==============================
SERVER SETTINGS -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="fas fa-tachometer-alt"></i>
	<?php esc_html_e("Server Settings", 'duplicator') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-settings-diag-srv-panel" style="<?php echo esc_html($ui_css_srv_panel); ?>">
	<table class="widefat" cellspacing="0">
		<tr>
			<td class='dup-settings-diag-header' colspan="2"><?php esc_html_e("General", 'duplicator'); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Duplicator Version", 'duplicator'); ?></td>
			<td>
				<?php echo esc_html(DUPLICATOR_VERSION); ?> -
				<?php echo esc_html(DUPLICATOR_VERSION_BUILD); ?>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e("Operating System", 'duplicator'); ?></td>
			<td><?php echo esc_html(PHP_OS) ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Timezone", 'duplicator'); ?></td>
			<td><?php echo esc_html(date_default_timezone_get()); ?> &nbsp; <small><i>This is a <a href='options-general.php'>WordPress setting</a></i></small></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Server Time", 'duplicator'); ?></td>
			<td><?php echo date("Y-m-d H:i:s"); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Web Server", 'duplicator'); ?></td>
			<td><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></td>
		</tr>
		<?php
		$abs_path = duplicator_get_abs_path();
		?>
		<tr>
			<td><?php esc_html_e("Root Path", 'duplicator'); ?></td>
			<td><?php echo esc_html($abs_path); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("ABSPATH", 'duplicator'); ?></td>
			<td><?php echo esc_html($abs_path); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Plugins Path", 'duplicator'); ?></td>
			<td><?php echo esc_html(DUP_Util::safePath(WP_PLUGIN_DIR)); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Loaded PHP INI", 'duplicator'); ?></td>
			<td><?php echo esc_html(php_ini_loaded_file()); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Server IP", 'duplicator'); ?></td>
			<?php
			if (isset($_SERVER['SERVER_ADDR'])) {
				$server_address = $_SERVER['SERVER_ADDR'];
			} elseif (isset($_SERVER['SERVER_NAME']) && function_exists('gethostbyname')) {
				$server_address = gethostbyname($_SERVER['SERVER_NAME']);
			} else {
				$server_address = __("Can't detect", 'duplicator');
			}
			?>
			<td><?php echo esc_html($server_address); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Client IP", 'duplicator'); ?></td>
			<td><?php echo esc_html($client_ip_address);?></td>
		</tr>
		<tr>
			<td class='dup-settings-diag-header' colspan="2">WordPress</td>
		</tr>
		<tr>
			<td><?php esc_html_e("Version", 'duplicator'); ?></td>
			<td><?php echo esc_html($wp_version); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Language", 'duplicator'); ?></td>
			<td><?php bloginfo('language'); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Charset", 'duplicator'); ?></td>
			<td><?php bloginfo('charset'); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Memory Limit ", 'duplicator'); ?></td>
			<td><?php echo esc_html(WP_MEMORY_LIMIT); ?> (<?php esc_html_e("Max", 'duplicator'); echo '&nbsp;' . esc_html(WP_MAX_MEMORY_LIMIT); ?>)</td>
		</tr>
		<tr>
			<td class='dup-settings-diag-header' colspan="2">PHP</td>
		</tr>
		<tr>
			<td><?php esc_html_e("Version", 'duplicator'); ?></td>
			<td><?php echo esc_html(phpversion()); ?></td>
		</tr>
		<tr>
			<td>SAPI</td>
			<td><?php echo esc_html(PHP_SAPI); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("User", 'duplicator'); ?></td>
			<td><?php echo DUP_Util::getCurrentUser(); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Process", 'duplicator'); ?></td>
			<td><?php echo esc_html(DUP_Util::getProcessOwner()); ?></td>
		</tr>
		<tr>
			<td><a href="http://php.net/manual/en/features.safe-mode.php" target="_blank"><?php esc_html_e("Safe Mode", 'duplicator'); ?></a></td>
			<td>
			<?php echo (((strtolower(@ini_get('safe_mode')) == 'on')	  ||  (strtolower(@ini_get('safe_mode')) == 'yes') ||
						 (strtolower(@ini_get('safe_mode')) == 'true') ||  (ini_get("safe_mode") == 1 )))
						 ? esc_html__('On', 'duplicator') : esc_html__('Off', 'duplicator');
			?>
			</td>
		</tr>
		<tr>
			<td><a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><?php esc_html_e("Memory Limit", 'duplicator'); ?></a></td>
			<td><?php echo @ini_get('memory_limit') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Memory In Use", 'duplicator'); ?></td>
			<td><?php echo size_format(@memory_get_usage(TRUE), 2) ?></td>
		</tr>
		<tr>
			<td><a href="http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank"><?php esc_html_e("Max Execution Time", 'duplicator'); ?></a></td>
			<td>
				<?php
					echo @ini_get('max_execution_time');
					$try_update = set_time_limit(0);
					$try_update = $try_update ? 'is dynamic' : 'value is fixed';
					echo " (default) - {$try_update}";
				?>
				<i class="fa fa-question-circle data-size-help"
					data-tooltip-title="<?php esc_attr_e("Max Execution Time", 'duplicator'); ?>"
					data-tooltip="<?php esc_attr_e('If the value shows dynamic then this means its possible for PHP to run longer than the default.  '
						. 'If the value is fixed then PHP will not be allowed to run longer than the default.', 'duplicator'); ?>"></i>
			</td>
		</tr>
		<tr>
			<td><a href="http://us3.php.net/shell_exec" target="_blank"><?php esc_html_e("Shell Exec", 'duplicator'); ?></a></td>
			<td><?php echo (DUP_Util::hasShellExec()) ? esc_html__("Is Supported", 'duplicator') : esc_html__("Not Supported", 'duplicator'); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Shell Exec Zip", 'duplicator'); ?></td>
			<td><?php echo (DUP_Util::getZipPath() != null) ? esc_html__("Is Supported", 'duplicator') : esc_html__("Not Supported", 'duplicator'); ?></td>
		</tr>
        <tr>
            <td><a href="https://suhosin.org/stories/index.html" target="_blank"><?php esc_html_e("Suhosin Extension", 'duplicator'); ?></a></td>
            <td><?php echo extension_loaded('suhosin') ? esc_html__("Enabled", 'duplicator') : esc_html__("Disabled", 'duplicator'); ?></td>
        </tr>
		<tr>
			<td><?php esc_html_e("Architecture ", 'duplicator'); ?></td>
			<td>                    
				<?php echo DUP_Util::getArchitectureString(); ?>
			</td>
		</tr>
		<tr>
            <td><?php esc_html_e("Error Log File ", 'duplicator'); ?></td>
            <td><?php echo esc_html($error_log_path); ?></td>
        </tr>
		<tr>
			<td class='dup-settings-diag-header' colspan="2">MySQL</td>
		</tr>
		<tr>
			<td><?php esc_html_e("Version", 'duplicator'); ?></td>
			<td><?php echo esc_html(DUP_DB::getVersion()); ?></td>
		</tr>
        <tr>
			<td><?php esc_html_e("Comments", 'duplicator'); ?></td>
            <td><?php echo esc_html(DUP_DB::getVariable('version_comment')); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e("Charset", 'duplicator'); ?></td>
			<td><?php echo DB_CHARSET ?></td>
		</tr>
		<tr>
			<td><a href="http://dev.mysql.com/doc/refman/5.0/en/server-system-variables.html#sysvar_wait_timeout" target="_blank"><?php esc_html_e("Wait Timeout", 'duplicator'); ?></a></td>
			<td><?php echo esc_html($dbvar_maxtime); ?></td>
		</tr>
		<tr>
			<td style="white-space:nowrap"><a href="http://dev.mysql.com/doc/refman/5.0/en/server-system-variables.html#sysvar_max_allowed_packet" target="_blank"><?php esc_html_e("Max Allowed Packets", 'duplicator'); ?></a></td>
			<td><?php echo esc_html($dbvar_maxpacks); ?></td>
		</tr>
		<tr>
			<td><a href="http://dev.mysql.com/doc/refman/5.0/en/mysqldump.html" target="_blank"><?php esc_html_e("msyqldump Path", 'duplicator'); ?></a></td>
			<td><?php echo esc_html($mysqlDumpSupport); ?></td>
		</tr>
		 <tr>
			 <td class='dup-settings-diag-header' colspan="2"><?php esc_html_e("Server Disk", 'duplicator'); ?></td>
		 </tr>
		 <tr valign="top">
			 <td><?php esc_html_e('Free space', 'hyper-cache'); ?></td>
			 <td><?php echo esc_html($perc);?>% -- <?php echo esc_html(DUP_Util::byteSize($space_free));?> from <?php echo esc_html(DUP_Util::byteSize($space));?><br/>
				  <small>
					  <?php esc_html_e("Note: This value is the physical servers hard-drive allocation.", 'duplicator'); ?> <br/>
					  <?php esc_html_e("On shared hosts check your control panel for the 'TRUE' disk space quota value.", 'duplicator'); ?>
				  </small>
			 </td>
		 </tr>

	</table><br/>

</div> <!-- end .dup-box-panel -->
</div> <!-- end .dup-box -->
<br/>