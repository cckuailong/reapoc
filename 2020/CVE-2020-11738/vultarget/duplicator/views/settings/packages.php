<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

global $wp_version;
global $wpdb;

$action_updated = null;
$action_response = __("Package Settings Saved", 'duplicator');
$mysqldump_exe_file	= '';

//SAVE RESULTS
if (isset($_POST['action']) && $_POST['action'] == 'save') {

	//Nonce Check
	$nonce = sanitize_text_field($_POST['dup_settings_save_nonce_field']);
	if (! isset( $_POST['dup_settings_save_nonce_field'] ) || ! wp_verify_nonce($nonce, 'dup_settings_save')) {
		die('Invalid token permissions to perform this request.');
	}

    //Package
	$mysqldump_enabled	= isset($_POST['package_dbmode']) && $_POST['package_dbmode'] == 'mysql' ? "1" : "0";
	if (isset($_POST['package_mysqldump_path'])) {
		$mysqldump_exe_file	= DUP_Util::safePath(sanitize_text_field(trim($_POST['package_mysqldump_path'])));
		$mysqldump_exe_file	= DUP_DB::escSQL(strip_tags($mysqldump_exe_file), true);
	}

	DUP_Settings::Set('last_updated', date('Y-m-d-H-i-s'));
    DUP_Settings::Set('package_zip_flush', isset($_POST['package_zip_flush']) ? "1" : "0");
	DUP_Settings::Set('archive_build_mode', sanitize_text_field($_POST['archive_build_mode']));
	DUP_Settings::Set('package_mysqldump', $mysqldump_enabled ? "1" : "0");
	DUP_Settings::Set('package_phpdump_qrylimit', isset($_POST['package_phpdump_qrylimit']) ? $_POST['package_phpdump_qrylimit'] : "100");
	DUP_Settings::Set('package_mysqldump_path', $mysqldump_exe_file);
	DUP_Settings::Set('package_ui_created', sanitize_text_field($_POST['package_ui_created']));

	$action_updated = DUP_Settings::Save();
    DUP_Util::initSnapshotDirectory();
}

$is_shellexec_on        = DUP_Util::hasShellExec();
$package_zip_flush		= DUP_Settings::Get('package_zip_flush');
$phpdump_chunkopts		= array("20", "100", "500", "1000", "2000");
$phpdump_qrylimit		= DUP_Settings::Get('package_phpdump_qrylimit');
$package_mysqldump		= DUP_Settings::Get('package_mysqldump');
$package_mysqldump_path = trim(DUP_Settings::Get('package_mysqldump_path'));
$package_ui_created		= is_numeric(DUP_Settings::Get('package_ui_created')) ? DUP_Settings::Get('package_ui_created') : 1;
$mysqlDumpPath			= DUP_DB::getMySqlDumpPath();
$mysqlDumpFound			= ($mysqlDumpPath) ? true : false;
$archive_build_mode		= DUP_Settings::Get('archive_build_mode')
?>

<style>
    form#dup-settings-form input[type=text] {width:500px; }
    div.dup-feature-found {padding:10px 0 5px 0; color:green;}
    div.dup-feature-notfound {color:maroon; width:600px; line-height: 18px}
	select#package_ui_created {font-family: monospace}
	div.engine-radio {float: left; min-width: 100px}
	div.engine-sub-opts {padding:5px 0 10px 15px; display:none }
</style>

<form id="dup-settings-form" action="<?php echo admin_url('admin.php?page=duplicator-settings&tab=package'); ?>" method="post">
<?php wp_nonce_field('dup_settings_save', 'dup_settings_save_nonce_field', false); ?>
<input type="hidden" name="action" value="save">
<input type="hidden" name="page"   value="duplicator-settings">

<?php if ($action_updated) : ?>
	<div id="message" class="notice notice-success is-dismissible dup-wpnotice-box"><p><?php echo esc_html($action_response); ?></p></div>
<?php endif; ?>


<h3 class="title"><?php esc_html_e("Database", 'duplicator') ?> </h3>
<hr size="1" />
<table class="form-table">
<tr>
	<th scope="row"><label><?php esc_html_e("SQL Script", 'duplicator'); ?></label></th>
	<td>
		<div class="engine-radio <?php echo ($is_shellexec_on) ? '' : 'engine-radio-disabled'; ?>">
			<input type="radio" name="package_dbmode" value="mysql" id="package_mysqldump" <?php echo ($package_mysqldump) ? 'checked="checked"' : ''; ?> />
			<label for="package_mysqldump"><?php esc_html_e("Mysqldump", 'duplicator'); ?></label>
		</div>

		<div class="engine-radio" >
			<!-- PHP MODE -->
			<?php if (!$mysqlDumpFound) : ?>
				<input type="radio" name="package_dbmode" id="package_phpdump" value="php" checked="checked" />
			<?php else : ?>
				<input type="radio" name="package_dbmode" id="package_phpdump" value="php" <?php echo (!$package_mysqldump) ? 'checked="checked"' : ''; ?> />
			<?php endif; ?>
			<label for="package_phpdump"><?php esc_html_e("PHP Code", 'duplicator'); ?></label>
		</div>

		<br style="clear:both"/><br/>

		<!-- SHELL EXEC  -->
		<div class="engine-sub-opts" id="dbengine-details-1" style="display:none">
		<?php if (!$is_shellexec_on) : ?>
			<p class="description" style="width:550px; margin:5px 0 0 20px">
				<?php
					_e("This server does not support the PHP shell_exec or exec function which is required for mysqldump to run. ", 'duplicator');
					_e("Please contact the host or server administrator to enable this feature.", 'duplicator');
				?>
				<br/>
				<small>
					<i style="cursor: pointer"
						data-tooltip-title="<?php esc_html_e("Host Recommendation:", 'duplicator'); ?>"
						data-tooltip="<?php esc_html_e('Duplicator recommends going with the high performance pro plan or better from our recommended list', 'duplicator'); ?>">
					<i class="far fa-lightbulb" aria-hidden="true"></i>
						<?php
							printf("%s <a target='_blank' href='//snapcreek.com/wordpress-hosting/'>%s</a> %s",
								__("Please visit our recommended", 'duplicator'),
								__("host list", 'duplicator'),
								__("for reliable access to mysqldump", 'duplicator'));
						?>
					</i>
				</small>
				<br/><br/>
			</p>
		<?php else : ?>
			<div style="margin:0 0 0 15px">
				<?php if ($mysqlDumpFound) : ?>
					<div class="dup-feature-found">
						<i class="fa fa-check-circle"></i>
						<?php esc_html_e("Successfully Found:", 'duplicator'); ?> &nbsp;
						<i><?php echo esc_html($mysqlDumpPath); ?></i>
					</div><br/>
				<?php else : ?>
					<div class="dup-feature-notfound">
						<i class="fa fa-exclamation-triangle fa-sm"></i>
						<?php
							_e('Mysqldump was not found at its default location or the location provided.  Please enter a custom path to a valid location where mysqldump can run.  '
								. 'If the problem persist contact your host or server administrator.  ', 'duplicator');

							printf("%s <a target='_blank' href='//snapcreek.com/wordpress-hosting/'>%s</a> %s",
								__("See the", 'duplicator'),
								__("host list", 'duplicator'),
								__("for reliable access to mysqldump.", 'duplicator'));

						?>
					</div><br/>
				<?php endif; ?>

				<label><?php esc_html_e("Custom Path", 'duplicator'); ?></label>
				<i class="fas fa-question-circle fa-sm"
					data-tooltip-title="<?php esc_attr_e("mysqldump path:", 'duplicator'); ?>"
					data-tooltip="<?php esc_attr_e('Add a custom path if the path to mysqldump is not properly detected.   For all paths use a forward slash as the '
					. 'path seperator.  On Linux systems use mysqldump for Windows systems use mysqldump.exe.  If the path tried does not work please contact your hosting '
					. 'provider for details on the correct path.', 'duplicator'); ?>"></i>
				<br/>
				<input type="text" name="package_mysqldump_path" id="package_mysqldump_path" value="<?php echo esc_attr($package_mysqldump_path); ?>" placeholder="<?php esc_attr_e("/usr/bin/mypath/mysqldump", 'duplicator'); ?>" />
				<div class="dup-feature-notfound">
				<?php
					if (!$mysqlDumpFound && strlen($mysqldump_exe_file)) {
						_e('<i class="fa fa-exclamation-triangle fa-sm"></i> The custom path provided is not recognized as a valid mysqldump file:<br/>', 'duplicator');
						$mysqldump_path = esc_html($package_mysqldump_path);
						echo "'".esc_html($mysqldump_path)."'";
					}
				?>
				</div>
				<br/>
			</div>

		<?php endif; ?>
		</div>

		<!-- PHP OPTION -->
		<div class="engine-sub-opts" id="dbengine-details-2" style="display:none; line-height: 35px; margin:0 0 0 15px">
			<!-- PRO ONLY -->
			<label><?php esc_html_e("Mode",'duplicator'); ?>:</label>
			<select name="">
				<option selected="selected" value="1">
					<?php esc_html_e("Single-Threaded",'duplicator'); ?>
				</option>
				<option  disabled="disabled"  value="0">
					<?php esc_html_e("Multi-Threaded",'duplicator'); ?>
				</option>
			</select>
			<i style="margin-right:7px;" class="fas fa-question-circle fa-sm"
				data-tooltip-title="<?php esc_attr_e("PHP Code Mode:",'duplicator'); ?>"
				data-tooltip="<?php
					esc_attr_e('Single-Threaded mode attempts to create the entire database script in one request.  Multi-Threaded mode allows the database script '
						. 'to be chunked over multiple requests.  Multi-Threaded mode is typically slower but much more reliable especially for larger databases.','duplicator');
					esc_attr_e('<br><br><i>Multi-Threaded mode is only available in Duplicator Pro.</i>','duplicator');
					?>"></i>
			<div>
			   <label for="package_phpdump_qrylimit"><?php esc_html_e("Query Limit Size", 'duplicator'); ?>:</label> &nbsp;
				<select name="package_phpdump_qrylimit" id="package_phpdump_qrylimit">
					<?php
						foreach($phpdump_chunkopts as $value) {
							$selected = ( $phpdump_qrylimit == $value ? "selected='selected'" : '' );
							echo "<option {$selected} value='".esc_attr($value)."'>" . number_format($value)  . '</option>';
						}
					?>
				</select>
				<i class="fas fa-question-circle fa-sm"
				   data-tooltip-title="<?php esc_attr_e("PHP Query Limit Size", 'duplicator'); ?>"
				   data-tooltip="<?php esc_attr_e('A higher limit size will speed up the database build time, however it will use more memory.  If your host has memory caps start off low.', 'duplicator'); ?>"></i>

			</div>
		</div>
	</td>
</tr>
</table>


<h3 class="title"><?php esc_html_e("Archive", 'duplicator') ?> </h3>
<hr size="1" />
<table class="form-table">
<tr>
	<th scope="row"><label><?php esc_html_e('Archive Engine', 'duplicator'); ?></label></th>
	<td>
		<div class="engine-radio">
			<input type="radio" name="archive_build_mode" id="archive_build_mode1" onclick="Duplicator.Pack.ToggleArchiveEngine()"
				   value="<?php echo esc_attr(DUP_Archive_Build_Mode::ZipArchive); ?>" <?php echo ($archive_build_mode == DUP_Archive_Build_Mode::ZipArchive) ? 'checked="checked"' : ''; ?> />
			<label for="archive_build_mode1"><?php esc_html_e('ZipArchive', 'duplicator'); ?></label>
		</div>

		<div class="engine-radio">
			<input type="radio" name="archive_build_mode" id="archive_build_mode2"  onclick="Duplicator.Pack.ToggleArchiveEngine()"
				   value="<?php echo esc_attr(DUP_Archive_Build_Mode::DupArchive); ?>" <?php echo ($archive_build_mode == DUP_Archive_Build_Mode::DupArchive) ? 'checked="checked"' : ''; ?> />
			<label for="archive_build_mode2"><?php esc_html_e('DupArchive'); ?></label> &nbsp; &nbsp;
		</div>

		<br style="clear:both"/>

		<!-- ZIPARCHIVE -->
		<div class="engine-sub-opts" id="engine-details-1" style="display:none">
			<p class="description">
				<?php
					esc_html_e('Creates a archive format (archive.zip).', 'duplicator'); echo '<br/>';
					esc_html_e('This option uses the internal PHP ZipArchive classes to create a Zip file.', 'duplicator');
				?>
			</p>
		</div>

		<!-- DUPARCHIVE -->
		<div class="engine-sub-opts" id="engine-details-2" style="display:none">
			<p class="description">
				<?php
					esc_html_e('Creates a custom archive format (archive.daf).', 'duplicator'); echo '<br/>';
					esc_html_e('This option is recommended for large sites or sites on constrained servers.', 'duplicator');
				?>
			</p>
		</div>
	</td>
</tr>
<tr>
	<th scope="row"><label><?php esc_html_e("Archive Flush", 'duplicator'); ?></label></th>
	<td>
		<input type="checkbox" name="package_zip_flush" id="package_zip_flush" <?php echo ($package_zip_flush) ? 'checked="checked"' : ''; ?> />
		<label for="package_zip_flush"><?php esc_html_e("Attempt Network Keep Alive", 'duplicator'); ?></label>
		<i style="font-size:12px">(<?php esc_html_e("enable only for large archives", 'duplicator'); ?>)</i>
		<p class="description">
			<?php
				esc_html_e("This will attempt to keep a network connection established for large archives.", 'duplicator'); echo '<br/>';
				esc_html_e(" Valid only when Archive Engine for ZipArchive is enabled.");
			?>
		</p>
	</td>
</tr>
</table><br/>

<h3 class="title"><?php esc_html_e("Visual", 'duplicator') ?> </h3>
<hr size="1" />
<table class="form-table">
<tr>
	<th scope="row"><label><?php esc_html_e("Created Format", 'duplicator'); ?></label></th>
	<td>
		<select name="package_ui_created" id="package_ui_created">
			<!-- YEAR -->
			<optgroup label="<?php esc_html_e("By Year", 'duplicator'); ?>">
				<option value="1">Y-m-d H:i &nbsp;	[2000-01-05 12:00]</option>
				<option value="2">Y-m-d H:i:s		[2000-01-05 12:00:01]</option>
				<option value="3">y-m-d H:i &nbsp;	[00-01-05   12:00]</option>
				<option value="4">y-m-d H:i:s		[00-01-05   12:00:01]</option>
			</optgroup>
			<!-- MONTH -->
			<optgroup label="<?php esc_html_e("By Month", 'duplicator'); ?>">
				<option value="5">m-d-Y H:i  &nbsp; [01-05-2000 12:00]</option>
				<option value="6">m-d-Y H:i:s		[01-05-2000 12:00:01]</option>
				<option value="7">m-d-y H:i  &nbsp; [01-05-00   12:00]</option>
				<option value="8">m-d-y H:i:s		[01-05-00   12:00:01]</option>
			</optgroup>
			<!-- DAY -->
			<optgroup label="<?php esc_html_e("By Day", 'duplicator'); ?>">
				<option value="9"> d-m-Y H:i &nbsp;	[05-01-2000 12:00]</option>
				<option value="10">d-m-Y H:i:s		[05-01-2000 12:00:01]</option>
				<option value="11">d-m-y H:i &nbsp;	[05-01-00	12:00]</option>
				<option value="12">d-m-y H:i:s		[05-01-00	12:00:01]</option>
			</optgroup>
		</select>
		<p class="description">
			<?php esc_html_e("The UTC date format shown in the 'Created' column on the Packages screen.", 'duplicator'); ?> <br/>
			<small><?php esc_html_e("To use WordPress timezone formats consider an upgrade to Duplicator Pro.", 'duplicator'); ?></small>
		</p>
	</td>
</tr>
</table><br/>


<p class="submit" style="margin: 20px 0px 0xp 5px;">
	<br/>
	<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e("Save Package Settings", 'duplicator') ?>" style="display: inline-block;" />
</p>

</form>

<script>
jQuery(document).ready(function($)
{
    Duplicator.Pack.SetDBEngineMode = function()
	{
		var isMysqlDump	= $('#package_mysqldump').is(':checked');
		var isPHPMode	= $('#package_phpdump').is(':checked');
		var isPHPChunkMode = $('#package_phpchunkingdump').is(':checked');

		$('#dbengine-details-1, #dbengine-details-2').hide();
		switch (true) {
			case isMysqlDump :
                $('#dbengine-details-1').show();
                break;
			case isPHPMode	 :
			case isPHPChunkMode :
				$('#dbengine-details-2').show();
				break;
		}
	};

	Duplicator.Pack.ToggleArchiveEngine = function ()
	{
		$('#engine-details-1, #engine-details-2').hide();
		if ($('#archive_build_mode1').is(':checked')) {
			$('#engine-details-1').show();
			$('#package_zip_flush').removeAttr('disabled');
		} else {
			$('#engine-details-2').show();
			$('#package_zip_flush').attr('disabled', true);
		}
	};

    Duplicator.Pack.SetDBEngineMode();
    $('#package_mysqldump , #package_phpdump').change(function() {
        Duplicator.Pack.SetDBEngineMode();
    });
	Duplicator.Pack.ToggleArchiveEngine();

	$('#package_ui_created').val(<?php echo esc_js($package_ui_created); ?> );

});
</script>