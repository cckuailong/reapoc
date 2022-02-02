<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */
/* @var $archive_config DUPX_ArchiveConfig */
/* @var $installer_state DUPX_InstallerState */

require_once($GLOBALS['DUPX_INIT'] . '/classes/config/class.archive.config.php');

$root_path			  = $GLOBALS['DUPX_ROOT'];
$is_wpconfarc_present	= file_exists(DUPX_Package::getWpconfigArkPath());
//ARCHIVE FILE
if (file_exists($GLOBALS['FW_PACKAGE_PATH'])) {
    $arcCheck = 'Pass';
} else {
    if ($is_wpconfarc_present) {
        $arcCheck = 'Warn';
    } else {
        $arcCheck = 'Fail';
    }
}
$arcSize = file_exists($GLOBALS['FW_PACKAGE_PATH']) ? @filesize($GLOBALS['FW_PACKAGE_PATH']) : 0;
$arcSize = is_numeric($arcSize) ? $arcSize : 0;

$installer_state	  = DUPX_InstallerState::getInstance();
$is_overwrite_mode    =  ($installer_state->mode === DUPX_InstallerMode::OverwriteInstall);
$is_wordpress		  = DUPX_Server::isWordPress();
$is_dbonly			  = $GLOBALS['DUPX_AC']->exportOnlyDB;

//REQUIRMENTS
$req = array();
$req['10'] = DUPX_Server::is_dir_writable($GLOBALS['DUPX_ROOT'])	? 'Pass' : 'Fail';
$req['20'] = function_exists('mysqli_connect')						? 'Pass' : 'Fail';
$req['30'] = DUPX_Server::$php_version_safe							? 'Pass' : 'Fail';
$all_req = in_array('Fail', $req)									? 'Fail' : 'Pass';

//NOTICES
$openbase	= ini_get("open_basedir");
$datetime1	= $GLOBALS['DUPX_AC']->created;
$datetime2	= date("Y-m-d H:i:s");
$fulldays	= round(abs(strtotime($datetime1) - strtotime($datetime2))/86400);
$root_path	= DupLiteSnapLibIOU::safePath($GLOBALS['DUPX_ROOT'], true);
$archive_path	= DupLiteSnapLibIOU::safePath($GLOBALS['FW_PACKAGE_PATH'], true);
$wpconf_path	= "{$root_path}/wp-config.php";
$max_time_zero	= ($GLOBALS['DUPX_ENFORCE_PHP_INI']) ? false : @set_time_limit(0);
$max_time_size	= 314572800;  //300MB
$max_time_ini	= ini_get('max_execution_time');
$max_time_warn	= (is_numeric($max_time_ini) && $max_time_ini < 31 && $max_time_ini > 0) && $arcSize > $max_time_size;


$notice = array();
$notice['10'] = ! $is_overwrite_mode				? 'Good' : 'Warn';
$notice['20'] = ! $is_wpconfarc_present				? 'Good' : 'Warn';
if ($is_dbonly) {
	$notice['25'] =	$is_wordpress ? 'Good' : 'Warn';
}
$notice['30'] = $fulldays <= 180					? 'Good' : 'Warn';
$notice['40'] = DUPX_Server::$php_version_53_plus	? 'Good' : 'Warn';

$packagePHP = $GLOBALS['DUPX_AC']->version_php;
$currentPHP = DUPX_Server::$php_version;
$packagePHPMajor = intval($packagePHP);
$currentPHPMajor = intval($currentPHP);
$notice['45'] = ($packagePHPMajor === $currentPHPMajor || $GLOBALS['DUPX_AC']->exportOnlyDB) ? 'Good' : 'Warn';

$notice['50'] = empty($openbase)					? 'Good' : 'Warn';
$notice['60'] = !$max_time_warn						? 'Good' : 'Warn';
$notice['70'] = $GLOBALS['DUPX_AC']->mu_mode == 0	? 'Good' : 'Warn';
$notice['80'] = !$GLOBALS['DUPX_AC']->is_outer_root_wp_config_file	? 'Good' : 'Warn';
if ($GLOBALS['DUPX_AC']->exportOnlyDB) {
	$notice['90'] = 'Good';
} else {
	$notice['90'] = (!$GLOBALS['DUPX_AC']->is_outer_root_wp_content_dir) 
						? 'Good' 
						: 'Warn';
}

$space_free = @disk_free_space($GLOBALS['DUPX_ROOT']); 
$archive_size = file_exists($GLOBALS['FW_PACKAGE_PATH']) ? filesize($GLOBALS['FW_PACKAGE_PATH']) : 0;
$notice['100'] = ($space_free && $archive_size > $space_free) 
                    ? 'Warn'
					: 'Good';

$all_notice	  = in_array('Warn', $notice)			? 'Warn' : 'Good';

//SUMMATION
$req_success	= ($all_req == 'Pass');
$req_notice		= ($all_notice == 'Good');
$all_success	= ($req_success && $req_notice);
$agree_msg		= "To enable this button the checkbox above under the 'Terms & Notices' must be checked.";

$shell_exec_unzip_path = DUPX_Server::get_unzip_filepath();
$shell_exec_zip_enabled = ($shell_exec_unzip_path != null);
$zip_archive_enabled = class_exists('ZipArchive') ? 'Enabled' : 'Not Enabled';
$archive_config  = DUPX_ArchiveConfig::getInstance();
?>

<form id="s1-input-form" method="post" class="content-form" autocomplete="off">
<input type="hidden" name="view" value="step1" />
<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step1'); ?>"> 
<input type="hidden" name="ctrl_action" value="ctrl-step1" />
<input type="hidden" name="ctrl_csrf_token" value="<?php echo DUPX_U::esc_attr(DUPX_CSRF::generate('ctrl-step1')); ?>"> 
<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_html($_POST['secure-pass']); ?>" />
<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
<input type="hidden" id="s1-input-form-extra-data" name="extra_data" />

<div class="hdr-main">
	Step <span class="step">1</span> of 4: Deployment
	<div class="sub-header">This step will extract the archive file contents.</div>
</div><br/>

<!-- ====================================
SETUP TYPE: @todo implement
==================================== -->
<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s1-area-setup-type" style="display:none">
	<a id="s1-area-setup-type-link"><i class="fa fa-plus-square"></i>Setup</a>
</div>
<div id="s1-area-setup-type" style="display:none">

	<!-- STANDARD INSTALL -->
	<input type="radio" id="setup-type-fresh" name="setup_type" value="1" checked="true" onclick="DUPX.toggleSetupType()" />
	<label for="setup-type-fresh"><b>Standard Install</b></label>
	<i class="fas fa-question-circle fa-sm"
		data-tooltip-title="Standard Install"
		data-tooltip="A standard install is the default way Duplicator has always worked.  Setup your package in an empty directory and run the installer."></i>
	<br/>
	<div class="s1-setup-type-sub" id="s1-setup-type-sub-1">
		<input type="checkbox" name="setup-backup-files" id="setup-backup-files-fresh" />
		<label for="setup-backup-files-fresh">Backup Existing Files</label><br/>
		<input type="checkbox" name="setup-remove-files" id="setup-remove-files-fresh" />
		<label for="setup-remove-files-fresh">Remove Existing Files</label><br/>
	</div><br/>

	<!-- OVERWRITE INSTALL -->
	<input type="radio" id="setup-type-overwrite" name="setup_type" value="2" onclick="DUPX.toggleSetupType()" />
	<label for="setup-type-overwrite"><b>Overwrite Install</b></label>
	<i class="fas fa-question-circle fa-sm"
		data-tooltip-title="Overwrite Install"
		data-tooltip="An Overwrite Install allows Duplicator to overwrite an existing WordPress Site."></i><br/>
	<div class="s1-setup-type-sub" id="s1-setup-type-sub-2">
		<input type="checkbox" name="setup-backup-files" id="setup-backup-files-overwrite" />
		<label for="setup-backup-files-overwrite">Backup Existing Files</label><br/>
		<input type="checkbox" name="setup-remove-files" id="setup-remove-files-overwrite" />
		<label for="setup-remove-files-overwrite">Remove Existing Files</label><br/>
		<input type="checkbox" name="setup-backup-database" id="setup-backup-database-overwrite" />
		<label for="setup-backup-database-overwrite">Backup Existing Database</label> <br/>
	</div><br/>

	<!-- DB-ONLY INSTALL -->
	<input type="radio" id="setup-type-db" name="setup_type" value="3" onclick="DUPX.toggleSetupType()" />
	<label for="setup-type-db"><b>Database Only Install</b></label>
	<i class="fas fa-question-circle fa-sm"
		data-tooltip-title="Database Only"
		data-tooltip="A database only install allows Duplicator to connect to a database and install only the database."></i><br/>
	<div class="s1-setup-type-sub" id="s1-setup-type-sub-3">
		<input type="checkbox" name="setup-backup-database" id="setup-backup-database-db" />
		<label for="setup-backup-database-db">Backup Existing Database</label> <br/>
	</div><br/>

</div>


<!-- ====================================
ARCHIVE
==================================== -->
<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s1-area-archive-file">
	<a id="s1-area-archive-file-link"><i class="fa fa-plus-square"></i>Setup</a>
	<?php
	$badge = DUPX_View_Funcs::getBadgeClassFromCheckStatus($arcCheck);
	?>
	<div class="<?php echo $badge;?>">
		<?php echo $arcCheck;?>
	</div>
</div>
<div id="s1-area-archive-file" style="display:none" class="hdr-sub1-area">
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Archive</a></li>
	</ul>
	<div id="tabs-1">

		<table class="s1-archive-local">
			<tr>
				<td colspan="2"><div class="hdr-sub3">Site Details</div></td>
			</tr>
			 <tr>
				<td>Site:</td>
				<td><?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->blogname);?> </td>
			</tr>
			<tr>
				<td>Notes:</td>
				<td><?php echo strlen($GLOBALS['DUPX_AC']->package_notes) ? DUPX_U::esc_html($GLOBALS['DUPX_AC']->package_notes) : " - no notes - "; ?></td>
			</tr>
			<?php if ($GLOBALS['DUPX_AC']->exportOnlyDB) :?>
			<tr>
				<td>Mode:</td>
				<td>Archive only database was enabled during package package creation.</td>
			</tr>
			<?php endif; ?>
		</table>

		<table class="s1-archive-local">
			<tr>
				<td colspan="2"><div class="hdr-sub3">File Details</div></td>
			</tr>
			<tr>
				<td style="vertical-align:top">Status:</td>
				<td>
					<?php if ($arcCheck == 'Fail' || $arcCheck == 'Warn') : ?>
							<span class="dupx-fail" style="font-style:italic">
								<?php
								if ($arcCheck == 'Warn') {
								?>
									The archive file named above must be the <u>exact</u> name of the archive file placed in the root path (character for character). But you can proceed with choosing Manual Archive Extraction.
								<?php
								} else {
								?>
									The archive file named above must be the <u>exact</u> name of the archive file placed in the root path (character for character).
									When downloading the package files make sure both files are from the same package line.  <br/><br/>

									If the contents of the archive were manually transferred to this location without the archive file then simply create a temp file named with
									the exact name shown above and place the file in the same directory as the installer.php file.  The temp file will not need to contain any data.
									Afterward, refresh this page and continue with the install process.
								<?php
								}
								?>
							</span>
					<?php else : ?>
						<span class="dupx-pass">Archive file successfully detected.</span>                                
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Path:</td>
				<td><?php echo DUPX_U::esc_html($root_path); ?> </td>
			</tr>
			<tr>
				<td>Size:</td>
				<td><?php echo DUPX_U::readableByteSize($arcSize);?> </td>
			</tr>
		</table>

	</div>
	<!--div id="tabs-2"><p>Content Here</p></div-->
</div>
</div><br/><br/>

<!-- ====================================
VALIDATION
==================================== -->
<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s1-area-sys-setup">
	<a id="s1-area-sys-setup-link"><i class="fa fa-plus-square"></i>Validation</a>
	<div class="<?php echo ( $req_success) ? 'status-badge-pass' : 'status-badge-fail'; ?>	">
		<?php echo ( $req_success) ? 'Pass' : 'Fail'; ?>
	</div>
</div>
<div id="s1-area-sys-setup" style="display:none" class="hdr-sub1-area">
	<div class='info-top'>The system validation checks help to make sure the system is ready for install.</div>

	<!-- REQUIREMENTS -->
	<div class="s1-reqs" id="s1-reqs-all">
		<div class="header">
			<table class="s1-checks-area">
				<tr>
					<td class="title">Requirements <small>(must pass)</small></td>
					<td class="toggle"><a href="javascript:void(0)" onclick="DUPX.toggleAll('#s1-reqs-all')">[toggle]</a></td>
				</tr>
			</table>
		</div>

		<!-- REQ 10 -->
		<?php
		$status = strtolower($req['10']);
		?>
		<div class="status <?php echo DUPX_U::esc_attr($status); ?>"><?php echo DUPX_U::esc_html($req['10']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-reqs10"><i class="fa fa-caret-right"></i> Permissions</div>
		<div class="info" id="s1-reqs10">
			<table>
				<tr>
					<td><b>Deployment Path:</b> </td>
					<td><i><?php echo "{$GLOBALS['DUPX_ROOT']}"; ?></i> </td>
				</tr>
				<tr>
					<td><b>Suhosin Extension:</b> </td>
					<td><?php echo extension_loaded('suhosin') ? "<i class='dupx-fail'>Enabled</i>" : "<i class='dupx-pass'>Disabled</i>"; ?> </td>
				</tr>
				<tr>
					<td><b>PHP Safe Mode:</b> </td>
					<td><?php echo (DUPX_Server::$php_safe_mode_on) ? "<i class='dupx-fail'>Enabled</i>" : "<i class='dupx-pass'>Disabled</i>"; ?> </td>
				</tr>
			</table><br/>

			The deployment path must be writable by PHP in order to extract the archive file.  Incorrect permissions and extension such as
			<a href="https://suhosin.org/stories/index.html" target="_blank">suhosin</a> can sometimes interfere with PHP being able to write/extract files.
			Please see the <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-055-q" target="_blank">FAQ permission</a> help link for complete details.
			PHP with <a href='http://php.net/manual/en/features.safe-mode.php' target='_blank'>safe mode</a> should be disabled.  If Safe Mode is enabled then
			please contact your hosting provider or server administrator to disable PHP safe mode.
		</div>

		<!-- REQ 20 -->
		<div class="status <?php echo strtolower($req['20']); ?>"><?php echo DUPX_U::esc_html($req['20']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-reqs20"><i class="fa fa-caret-right"></i> PHP Mysqli</div>
		<div class="info" id="s1-reqs20">
			Support for the PHP <a href='http://us2.php.net/manual/en/mysqli.installation.php' target='_blank'>mysqli extension</a> is required.
			Please contact your hosting provider or server administrator to enable the mysqli extension.  <i>The detection for this call uses
				the function_exists('mysqli_connect') call.</i>
		</div>

		<!-- REQ 30 -->
		<div class="status <?php echo strtolower($req['30']); ?>"><?php echo DUPX_U::esc_html($req['30']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-reqs30"><i class="fa fa-caret-right"></i> PHP Version</div>
		<div class="info" id="s1-reqs30">
			This server is running PHP: <b><?php echo DUPX_Server::$php_version ?></b>. <i>A minimum of PHP 5.2.17 is required</i>.
			Contact your hosting provider or server administrator and let them know you would like to upgrade your PHP version.
		</div>
	</div><br/>


	<!-- ====================================
	NOTICES  -->
	<div class="s1-reqs" id="s1-notice-all">
		<div class="header">
			<table class="s1-checks-area">
				<tr>
					<td class="title">Notices <small>(optional)</small></td>
					<td class="toggle"><a href="javascript:void(0)" onclick="DUPX.toggleAll('#s1-notice-all')">[toggle]</a></td>
				</tr>
			</table>
		</div>

		<!-- NOTICE 10: OVERWRITE INSTALL -->
		<?php if ($is_overwrite_mode && $is_wordpress) :?>
			<div class="status fail">Warn</div>
			<div class="title" data-type="toggle" data-target="#s1-notice10"><i class="fa fa-caret-right"></i> Overwrite Install</div>
			<div class="info" id="s1-notice10">
				<b>Deployment Path:</b> <i><?php echo "{$GLOBALS['DUPX_ROOT']}"; ?></i>
				<br/><br/>
				<?php
				if ($GLOBALS['DUPX_AC']->installSiteOverwriteOn || $is_dbonly) {
				?>
					Duplicator is in "Overwrite Install" mode because it has detected an existing WordPress site at the deployment path above.  This mode allows for the installer
					to be dropped directly into an existing WordPress site and overwrite its contents.   Any content inside of the archive file
					will <u>overwrite</u> the contents from the deployment path.  To continue choose one of these options:

					<ol>
						<li>Ignore this notice and continue with the install if you want to overwrite this sites files.</li>
						<li>Move this installer and archive to another empty directory path to keep this sites files.</li>
					</ol>

					<small style="color:maroon">
						<b>Notice:</b> Existing content such as plugin/themes/images will still show-up after the install is complete if they did not already exist in
						the archive file. For example if you have an SEO plugin in the current site but that same SEO plugin <u>does not exist</u> in the archive file
						then that plugin will display as a disabled plugin after the install is completed. The same concept with themes and images applies.  This will
						not impact the sites operation, and the behavior is expected.
					</small>
					<br/><br/>

					<small style="color:#025d02">
						<b>Recommendation:</b> It is recommended you only overwrite WordPress sites that have a minimal	setup (plugins/themes).  Typically a fresh install or a
						cPanel 'one click' install is the best baseline to work from when using this mode but is not required.
					</small>
				<?php
				} else {
                    ?>
					Duplicator works best by placing the installer and archive files into an empty directory.  If a wp-config.php file is found in the extraction
					directory it might indicate that a pre-existing WordPress site exists which can lead to a bad install.
					<br/><br/>
					<b>Options:</b>
					<ul style="margin-bottom: 0">
						<li>If the archive was already manually extracted then <a href="javascript:void(0)" onclick="DUPX.getManaualArchiveOpt()">[Enable Manual Archive Extraction]</a></li>
						<li>Empty the directory of all files, except for the installer.php and archive.zip/daf files.</li>
						<li>Advanced Users: Can attempt to manually remove the wp-config file only if the archive was manually extracted.</li>
					</ul>
				<?php
                }
				?>
			</div>

		<!-- NOTICE 20: ARCHIVE EXTRACTED -->
		<?php elseif ($is_wpconfarc_present && file_exists('{$root_path}/dup-installer')) :?>
			<div class="status fail">Warn</div>
			<div class="title" data-type="toggle" data-target="#s1-notice20"><i class="fa fa-caret-right"></i> Archive Extracted</div>
			<div class="info" id="s1-notice20">
				<b>Deployment Path:</b> <i><?php echo "{$GLOBALS['DUPX_ROOT']}"; ?></i>
				<br/><br/>
				
				The installer has detected that the archive file has been extracted to the deployment path above.  To continue choose one of these options:

				<ol>
					<li>Skip the extraction process by <a href="javascript:void(0)" onclick="DUPX.getManaualArchiveOpt()">[enabling manual archive extraction]</a> </li>
					<li>Ignore this message and continue with the install process to re-extract the archive file.</li>
				</ol>

				<small>Note: This test looks for a file named <i>dup-wp-config-arc__[HASH].txt</i> in the dup-installer directory.  If the file exists then this notice is shown.
				The <i>dup-wp-config-arc__[HASH].txt</i> file is created with every archive and removed once the install is complete.  For more details on this process see the
				<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q" target="_blank">manual extraction FAQ</a>.</small>
			</div>
		<?php endif; ?>

		<!-- NOTICE 25: DATABASE ONLY -->
		<?php if ($is_dbonly && ! $is_wordpress) :?>
			<div class="status fail">Warn</div>
			<div class="title" data-type="toggle" data-target="#s1-notice25"><i class="fa fa-caret-right"></i> Database Only</div>
			<div class="info" id="s1-notice25">
				<b>Deployment Path:</b> <i><?php echo "{$GLOBALS['DUPX_ROOT']}"; ?></i>
				<br/><br/>

				The installer has detected that a WordPress site does not exist at the deployment path above. This installer is currently in 'Database Only' mode because that is
				how the archive was created.  If core WordPress site files do not exist at the path above then they will need to be placed there in order for a WordPress site
				to properly work.  To continue choose one of these options:

				<ol>
					<li>Place this installer and archive at a path where core WordPress files already exist to hide this message. </li>
					<li>Create a new package that includes both the database and the core WordPress files.</li>
					<li>Ignore this message and install only the database (for advanced users only).</li>
				</ol>

				<small>Note: This test simply looks for the directories <?php echo DUPX_Server::$wpCoreDirsList; ?> and a wp-config.php file.  If they are not found in the
				deployment path above then this notice is shown.</small>
				
			</div>
		<?php endif; ?>

		<!-- NOTICE 30 -->
		<div class="status <?php echo ($notice['30'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['30']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice30"><i class="fa fa-caret-right"></i> Package Age</div>
		<div class="info" id="s1-notice30">
			This package is <?php echo "{$fulldays}"; ?> day(s) old. Packages older than 180 days might be considered stale.  It is recommended to build a new
			package unless your aware of the content and its data.  This is message is simply a recommendation.
		</div>


		<!-- NOTICE 40 -->
		<div class="status <?php echo ($notice['40'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['40']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice40"><i class="fa fa-caret-right"></i> PHP Version 5.2</div>
		<div class="info" id="s1-notice40">
			<?php
				$cssStyle   = DUPX_Server::$php_version_53_plus	 ? 'color:green' : 'color:red';
				echo "<b style='{$cssStyle}'>This server is currently running PHP version [{$currentPHP}]</b>.<br/>"
				. "Duplicator allows PHP 5.2 to be used during install but does not officially support it.  If you're using PHP 5.2 we strongly recommend NOT using it and having your "
				. "host upgrade to a newer more stable, secure and widely supported version.  The <a href='http://php.net/eol.php' target='_blank'>end of life for PHP 5.2</a> "
				. "was in January of 2011 and is not recommended for use.<br/><br/>";

				echo "Many plugin and theme authors are no longer supporting PHP 5.2 and trying to use it can result in site wide problems and compatibility warnings and errors.  "
				. "Please note if you continue with the install using PHP 5.2 the Duplicator support team will not be able to help with issues or troubleshoot your site.  "
				. "If your server is running <b>PHP 5.3+</b> please feel free to reach out for help if you run into issues with your migration/install.";
			?>
		</div>

		<!-- NOTICE 45 -->
		<div class="status <?php echo ($notice['45'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo $notice['45']; ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice45"><i class="fa fa-caret-right"></i> PHP Version Mismatch</div>
		<div class="info" id="s1-notice45">
			<?php
                $cssStyle   = $notice['45'] == 'Good' ? 'color:green' : 'color:red';
				echo "<b style='{$cssStyle}'>You are migrating site from the PHP {$packagePHP} to the PHP {$currentPHP}</b>.<br/>"
                    ."If this servers PHP version is different to the PHP version of your package was created it might cause problems with proper functioning of your website
						and/or plugins and themes.   It is highly recommended to try and use the same version of PHP if you are able to do so. <br/>";
                ?>
            </div>

		<!-- NOTICE 50 -->
		<div class="status <?php echo ($notice['50'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['50']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice50"><i class="fa fa-caret-right"></i> PHP Open Base</div>
		<div class="info" id="s1-notice50">
			<b>Open BaseDir:</b> <i><?php echo $notice['50'] == 'Good' ? "<i class='dupx-pass'>Disabled</i>" : "<i class='dupx-fail'>Enabled</i>"; ?></i>
			<br/><br/>

			If <a href="http://php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">open_basedir</a> is enabled and your
			having issues getting your site to install properly; please work with your host and follow these steps to prevent issues:
			<ol style="margin:7px; line-height:19px">
				<li>Disable the open_basedir setting in the php.ini file</li>
				<li>If the host will not disable, then add the path below to the open_basedir setting in the php.ini<br/>
					<i style="color:maroon">"<?php echo str_replace('\\', '/', dirname( __FILE__ )); ?>"</i>
				</li>
				<li>Save the settings and restart the web server</li>
			</ol>
			Note: This warning will still show if you choose option #2 and open_basedir is enabled, but should allow the installer to run properly.  Please work with your
			hosting provider or server administrator to set this up correctly.
		</div>

		<!-- NOTICE 60 -->
		<div class="status <?php echo ($notice['60'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['60']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice60"><i class="fa fa-caret-right"></i> PHP Timeout</div>
		<div class="info" id="s1-notice60">
			<b>Archive Size:</b> <?php echo DUPX_U::readableByteSize($arcSize) ?>  <small>(detection limit is set at <?php echo DUPX_U::readableByteSize($max_time_size) ?>) </small><br/>
			<b>PHP max_execution_time:</b> <?php echo "{$max_time_ini}"; ?> <small>(zero means not limit)</small> <br/>
			<b>PHP set_time_limit:</b> <?php echo ($max_time_zero) ? '<i style="color:green">Success</i>' : '<i style="color:maroon">Failed</i>' ?>
			<br/><br/>

			The PHP <a href="http://php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank">max_execution_time</a> setting is used to
			determine how long a PHP process is allowed to run.  If the setting is too small and the archive file size is too large then PHP may not have enough
			time to finish running before the process is killed causing a timeout.
			<br/><br/>

			Duplicator attempts to turn off the timeout by using the
			<a href="http://php.net/manual/en/function.set-time-limit.php" target="_blank">set_time_limit</a> setting.   If this notice shows as a warning then it is
			still safe to continue with the install.  However, if a timeout occurs then you will need to consider working with the max_execution_time setting or extracting the
			archive file using the 'Manual Archive Extraction' method.
			Please see the	<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-100-q" target="_blank">FAQ timeout</a> help link for more details.
		</div>


		<!-- NOTICE 8 -->
		<div class="status <?php echo ($notice['70'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['70']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice70"><i class="fa fa-caret-right"></i> WordPress Multisite</div>
		<div class="info" id="s1-notice70">
			<b>Status:</b> <?php echo $notice['70'] <= 0 ? 'This archive is not a multisite' : 'This is an unsupported multisite archive'; ?>
			<br/><br/>

			 Duplicator does not support WordPress multisite migrations.  We recommend using Duplicator Pro which currently supports full multisite migrations and subsite to
			 standalone site migrations.
			 <br/><br/>
			 While it is not recommended you can still continue with the build of this package.  Please note that after the install the site may not be working correctly.
			 Additional manual custom configurations will need to be made to finalize this multisite migration.

			 <i><a href='https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_is_mu_warn_exe&utm_campaign=duplicator_pro' target='_blank'>[upgrade to pro]</a></i>
		</div>

		<!-- NOTICE 80 -->
		<div class="status <?php echo ($notice['80'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['80']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice80"><i class="fa fa-caret-right"></i> WordPress wp-config Location</div>
		<div class="info" id="s1-notice80">
			If the wp-config.php file was moved up one level and out of the WordPress root folder in the package creation site then this test will show a warning.
			<br/><br/>
			This Duplicator installer will place this wp-config.php file in the WordPress setup root folder of this installation site to help stabilize the install process.
			This process will not break anything in your installation site, but the details are here for your information.
		</div>

		<!-- NOTICE 90 -->
		<div class="status <?php echo ($notice['90'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['90']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice90"><i class="fa fa-caret-right"></i> WordPress wp-content Location</div>
		<div class="info" id="s1-notice90">
			If the wp-content directory was moved and not located at the WordPress root folder in the package creation site then this test will show a warning.
			<br/><br/>
			Duplicator Installer will place this wp-content directory in the WordPress setup root folder of this installation site. It will not break anything in your installation
			site. It is just for your information.
		</div>

		<!-- NOTICE 100 -->
		<div class="status <?php echo ($notice['100'] == 'Good') ? 'pass' : 'fail' ?>"><?php echo DUPX_U::esc_html($notice['100']); ?></div>
		<div class="title" data-type="toggle" data-target="#s1-notice100"><i class="fa fa-caret-right"></i> Sufficient disk space</div>
		<div class="info" id="s1-notice100">
        <?php
        echo ($notice['100'] == 'Good')
                ? 'You have sufficient disk space in your machine to extract the archive.'
                : 'You don’t have sufficient disk space in your machine to extract the archive. Ask your host to increase disk space.'
        ?>
		</div>

	</div>

</div>
<br/><br/>


<!-- ====================================
OPTIONS
==================================== -->
<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s1-area-adv-opts">
	<a href="javascript:void(0)"><i class="fa fa-plus-square"></i>Options</a>
</div>
<div id="s1-area-adv-opts" class="hdr-sub1-area" style="display:none">
	<div class="help-target">
        <?php DUPX_View_Funcs::helpIconLink('step1'); ?>
	</div>
	<div class="hdr-sub3">General</div>
	<table class="dupx-opts dupx-advopts">
        <tr>
            <td>Extraction:</td>
            <td>
                <?php 
                $options = array();
                $extra_attr = ($arcCheck == 'Warn' && $is_wpconfarc_present) ? ' selected="selected"' : '';
                $options[] = '<option '.($is_wpconfarc_present ? '' : 'disabled').$extra_attr.' value="manual">Manual Archive Extraction '.($is_wpconfarc_present ? '' : '*').'</option>';
                if($archive_config->isZipArchive()){
					//ZIP-ARCHIVE
					$extra_attr = ('Pass' == $arcCheck && $zip_archive_enabled && !$shell_exec_zip_enabled) 
										? ' selected="selected"'
										: '';
					$extra_attr .= ('Pass' != $arcCheck || !$zip_archive_enabled) 
										? ' disabled="disabled"'
										: '';
					$options[] = '<option value="ziparchive"'.$extra_attr.'>PHP ZipArchive</option>';
					
					//SHELL-EXEC UNZIP
					$extra_attr = ('Pass' != $arcCheck || !$shell_exec_zip_enabled) ? ' disabled="disabled"' : '';
					$extra_attr .= ('Pass' == $arcCheck && $shell_exec_zip_enabled) ? ' selected="selected"' : '';
					$options[] = '<option value="shellexec_unzip"'.$extra_attr.'>Shell Exec Unzip</option>';
                } else { // DUPARCHIVE
                    $extra_attr = ('Pass' == $arcCheck) ? ' selected="selected"' : 'disabled="disabled"';
                    $options[] = '<option value="duparchive"'.$extra_attr.'>DupArchive</option>';
                }
                $num_selections = count($options);
                ?>
                <select id="archive_engine" name="archive_engine" size="<?php echo DUPX_U::esc_attr($num_selections); ?>">
					<?php echo implode('', $options); ?>
                </select><br/>
				<?php if (!$is_wpconfarc_present):?>
					<span class="sub-notes">
						*Option enabled when archive has been pre-extracted
						<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q" target="_blank">[more info]</a>
					</span>
			<?php endif; ?>
            </td>
        </tr>
		<tr>
			<td>Permissions:</td>
			<td>
				<input type="checkbox" name="set_file_perms" id="set_file_perms" value="1" onclick="jQuery('#file_perms_value').prop('disabled', !jQuery(this).is(':checked'));"/>
				<label for="set_file_perms">All Files</label><input name="file_perms_value" id="file_perms_value" style="width:45px; margin-left:7px;" value="644" disabled> &nbsp;
				<input type="checkbox" name="set_dir_perms" id="set_dir_perms" value="1" onclick="jQuery('#dir_perms_value').prop('disabled', !jQuery(this).is(':checked'));"/>
				<label for="set_dir_perms">All Directories</label><input name="dir_perms_value" id="dir_perms_value" style="width:45px; margin-left:7px;" value="755" disabled>
			</td>
		</tr>
	</table><br/><br/>

	<div class="hdr-sub3">Advanced</div>
	<table class="dupx-opts dupx-advopts">
        <tr>
			<td>Safe Mode:</td>
			<td>
				<select name="exe_safe_mode" id="exe_safe_mode" onchange="DUPX.onSafeModeSwitch();" style="width:250px;">
					<option value="0">Off</option>
					<option value="1">Basic</option>
					<option value="2">Advanced</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Config Files:</td>
			<td>
				<select name="config_mode" id="config_mode"  style="width:250px;">
					<option value="NEW">Create New (recommended)</option>
					<optgroup label="Advanced">
						<option value="RESTORE">Restore Original</option>
						<option value="IGNORE">Ignore All</option>
					</optgroup>
				</select> <br/>
				<span class="sub-notes" style="font-weight: normal">
					Controls how .htaccess, .user.ini and web.config are used.<br/>
					These options are not applied until step 3 is ran.
                    <?php DUPX_View_Funcs::helpLink('step1', '[more info]'); ?>
				</span>
			</td>
		</tr>
		<tr>
			<td>File Times:</td>
			<td>
				<input type="radio" name="zip_filetime" id="zip_filetime_now" value="current" checked="checked" />
				<label class="radio" for="zip_filetime_now" title='Set the files current date time to now'>Current</label> &nbsp;
				<input type="radio" name="zip_filetime" id="zip_filetime_orginal" value="original" />
				<label class="radio" for="zip_filetime_orginal" title="Keep the files date time the same">Original</label>
			</td>
		</tr>
		<tr>
			<td>Logging:</td>
			<td>
                <input type="radio" name="logging" id="logging-light" value="<?php echo DUPX_Log::LV_DEFAULT; ?>" checked="true"> <label for="logging-light" class="radio">Light</label> &nbsp;
                <input type="radio" name="logging" id="logging-detailed" value="<?php echo DUPX_Log::LV_DETAILED; ?>"> <label for="logging-detailed" class="radio">Detailed</label> &nbsp;
                <input type="radio" name="logging" id="logging-debug" value="<?php echo DUPX_Log::LV_DEBUG; ?>"> <label for="logging-debug" class="radio">Debug</label> &nbsp;
                <input type="radio" name="logging" id="logging-h-debug" value="<?php echo DUPX_Log::LV_HARD_DEBUG; ?>"> <label for="logging-h-debug" class="radio">Hard debug</label>
			</td>
		</tr>
		<?php if(!$archive_config->isZipArchive()): ?>
		<tr>
			<td>Client-Kickoff:</td>
			<td>
				<input type="checkbox" name="clientside_kickoff" id="clientside_kickoff" value="1" checked/>
				<label for="clientside_kickoff" style="font-weight: normal">Browser drives the archive engine.</label>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td>Testing:</td>
			<td>
				<a href="javascript:void(0)" target="db-test" onclick="DUPX.openDBValidationWindow(); return false;">[Quick Database Connection Test]</a>
			</td>
		</tr>
	</table>
</div><br/>

<?php include ('view.s1.terms.php') ;?>

<div id="s1-warning-check">
	<input id="accept-warnings" name="accpet-warnings" type="checkbox" onclick="DUPX.acceptWarning()" />
	<label for="accept-warnings">I have read and accept all <a href="javascript:void(0)" onclick="DUPX.viewTerms()">terms &amp; notices</a> <small style="font-style:italic">(required to continue)</small></label><br/>
</div>
<br/><br/>
<br/><br/>


<?php if (!$req_success || $arcCheck == 'Fail') : ?>
	<div class="s1-err-msg">
		<i>
			This installation will not be able to proceed until the archive and validation sections both pass. Please adjust your servers settings or contact your
			server administrator, hosting provider or visit the resources below for additional help.
		</i>
		<div style="padding:10px">
			&raquo; <a href="https://snapcreek.com/duplicator/docs/faqs-tech/" target="_blank">Technical FAQs</a> <br/>
			&raquo; <a href="https://snapcreek.com/support/docs/" target="_blank">Online Documentation</a> <br/>
		</div>
	</div>
<?php else : ?>
	<div class="footer-buttons" >
		<button id="s1-deploy-btn" type="button" title="<?php echo DUPX_U::esc_attr($agree_msg); ?>" onclick="DUPX.processNext()"  class="default-btn"> Next <i class="fa fa-caret-right"></i> </button>
	</div>
<?php endif; ?>

</form>

<!-- =========================================
VIEW: STEP 1 - DB QUICK TEST
========================================= -->
<form id="s1-dbtest-form" method="post" target="_blank" autocomplete="off">
	<input type="hidden" name="dbonlytest" value="1" />
	<input type="hidden" name="view" value="step2" />
	<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step2'); ?>">
	<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
	<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
	<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
</form>


<!-- =========================================
VIEW: STEP 1 - AJAX RESULT
Auto Posts to view.step2.php
========================================= -->
<form id='s1-result-form' method="post" class="content-form" style="display:none" autocomplete="off">

    <div class="dupx-logfile-link"><?php DUPX_View_Funcs::installerLogLink(); ?></div>
    <div class="hdr-main">
        Step <span class="step">1</span> of 4: Deployment
		<div class="sub-header">This step will extract the archive file contents.</div>
    </div>

    <!--  POST PARAMS -->
    <div class="dupx-debug">
		<i>Step 1 - AJAX Response</i>
        <input type="hidden" name="view" value="step2" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step2'); ?>">
		<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
		<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
		<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
		<input type="hidden" name="logging" id="ajax-logging"  />
        <input type="hidden" name="config_mode" id="ajax-config-mode" />
        <input type="hidden" name="exe_safe_mode" id="exe-safe-mode"  value="0" />
		<input type="hidden" name="json" id="ajax-json" />
        <textarea id='ajax-json-debug' name='json_debug_view'></textarea>
        <input type='submit' value='manual submit'>
    </div>

    <!--  PROGRESS BAR -->
    <div id="progress-area">
        <div style="width:500px; margin:auto">
            <div style="font-size:1.7em; margin-bottom:20px"><i class="fas fa-circle-notch fa-spin"></i> Extracting Archive Files<span id="progress-pct"></span></div>
            <div id="progress-bar"></div>
            <h3> Please Wait...</h3><br/><br/>
            <i>Keep this window open during the extraction process.</i><br/>
            <i>This can take several minutes.</i>
        </div>
    </div>

    <!--  AJAX SYSTEM ERROR -->
    <div id="ajaxerr-area" style="display:none">
        <p>Please try again an issue has occurred.</p>
        <div style="padding: 0px 10px 10px 0px;">
            <div id="ajaxerr-data">An unknown issue has occurred with the file and database setup process.  Please see the <?php DUPX_View_Funcs::installerLogLink(); ?> file for more details.</div>
            <div style="text-align:center; margin:10px auto 0px auto">
                <!-- <input type="button" class="default-btn" onclick="DUPX.hideErrorResult()" value="&laquo; Try Again" /> -->
				<br/>
				<a href="../<?php echo $GLOBALS['BOOTLOADER_NAME'];?>" class="default-btn">&laquo; Try Again</a>
				<br/><br/>
                <i style='font-size:11px'>See online help for more details at <a href='https://snapcreek.com/ticket' target='_blank'>snapcreek.com</a></i>
            </div>
        </div>
    </div>
</form>

<script>
DUPX.openDBValidationWindow = function()
{
	console.log('test');
	$('#s1-dbtest-form').submit();
}

DUPX.toggleSetupType = function ()
{
	var val = $("input:radio[name='setup_type']:checked").val();
	$('div.s1-setup-type-sub').hide();
	$('#s1-setup-type-sub-' + val).show(200);
};

DUPX.getManaualArchiveOpt = function ()
{
	$("html, body").animate({scrollTop: $(document).height()}, 1500);
	$("div[data-target='#s1-area-adv-opts']").find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
	$('#s1-area-adv-opts').show(1000);
	$('select#archive_engine').val('manual').focus();
};

DUPX.startExtraction = function()
{
	var isManualExtraction = ($("#archive_engine").val() == "manual");
	var zipEnabled = <?php echo DupLiteSnapLibStringU::boolToString($archive_config->isZipArchive()); ?>;

	$("#operation-text").text("Extracting Archive Files");

	if (zipEnabled || isManualExtraction) {
		DUPX.runStandardExtraction();
	} else {
		DUPX.kickOffDupArchiveExtract();
	}
}

DUPX.processNext = function ()
{
	DUPX.startExtraction();
};

DUPX.updateProgressPercent = function (percent)
{
	var percentString = '';
	if (percent > 0) {
		percentString = ' ' + percent + '%';
	}
	$("#progress-pct").text(percentString);
};

DUPX.clearDupArchiveStatusTimer = function ()
{
	if (DUPX.dupArchiveStatusIntervalID != -1) {
		clearInterval(DUPX.dupArchiveStatusIntervalID);
		DUPX.dupArchiveStatusIntervalID = -1;
	}
};

DUPX.getCriticalFailureText = function(failures)
{
	var retVal = null;

	if((failures !== null) && (typeof failures !== 'undefined')) {
		var len = failures.length;

		for(var j = 0; j < len; j++) {
			failure = failures[j];

			if(failure.isCritical) {
				retVal = failure.description;
				break;
			}
		}
	}

	return retVal;
};

DUPX.DAWSProcessingFailed = function(errorText)
{
	DUPX.clearDupArchiveStatusTimer();
	$('#ajaxerr-data').html(errorText);
	DUPX.hideProgressBar();
}

DUPX.handleDAWSProcessingProblem = function(errorText, pingDAWS) {

	DUPX.DAWS.FailureCount++;

	if(DUPX.DAWS.FailureCount <= DUPX.DAWS.MaxRetries) {
		var callback = DUPX.pingDAWS;

		if(pingDAWS) {
			console.log('!!!PING FAILURE #' + DUPX.DAWS.FailureCount);
		} else {
			console.log('!!!KICKOFF FAILURE #' + DUPX.DAWS.FailureCount);
			callback = DUPX.kickOffDupArchiveExtract;
		}

		DUPX.throttleDelay = 9;	// Equivalent of 'low' server throttling
		console.log('Relaunching in ' + DUPX.DAWS.RetryDelayInMs);
		setTimeout(callback, DUPX.DAWS.RetryDelayInMs);
	}
	else {
		console.log('Too many failures.');
		DUPX.DAWSProcessingFailed(errorText);
	}
};


DUPX.handleDAWSCommunicationProblem = function(xHr, pingDAWS, textStatus, page)
{
	DUPX.DAWS.FailureCount++;

	if(DUPX.DAWS.FailureCount <= DUPX.DAWS.MaxRetries) {

		var callback = DUPX.pingDAWS;

		if(pingDAWS) {
			console.log('!!!PING FAILURE #' + DUPX.DAWS.FailureCount);
		} else {
			console.log('!!!KICKOFF FAILURE #' + DUPX.DAWS.FailureCount);
			callback = DUPX.kickOffDupArchiveExtract;
		}
		console.log(xHr);
		DUPX.throttleDelay = 9;	// Equivalent of 'low' server throttling
		console.log('Relaunching in ' + DUPX.DAWS.RetryDelayInMs);
		setTimeout(callback, DUPX.DAWS.RetryDelayInMs);
	}
	else {
		console.log('Too many failures.');
		DUPX.ajaxCommunicationFailed(xHr, textStatus, page);
	}
};

// Will either query for status or push it to continue the extraction
DUPX.pingDAWS = function ()
{
	console.log('pingDAWS:start');
	var request = new Object();
	var isClientSideKickoff = DUPX.isClientSideKickoff();

	if (isClientSideKickoff) {
		console.log('pingDAWS:client side kickoff');
		request.action = "expand";
		request.client_driven = 1;
		request.throttle_delay = DUPX.throttleDelay;
		request.worker_time = DUPX.DAWS.PingWorkerTimeInSec;
	} else {
		console.log('pingDAWS:not client side kickoff');
		request.action = "get_status";
	}

	console.log("pingDAWS:action=" + request.action);

	$.ajax({
		type: "POST",
		timeout: DUPX.DAWS.PingWorkerTimeInSec * 2000, // Double worker time and convert to ms
		url: DUPX.DAWS.Url,
		data: JSON.stringify(request),
		success: function (respData, textStatus, xHr) {
			try {
                var data = DUPX.parseJSON(respData);
            } catch(err) {
                console.error(err);
                console.error('JSON parse failed for response data: ' + respData);
                console.log('AJAX error. textStatus=');
				console.log(textStatus);
				DUPX.handleDAWSCommunicationProblem(xHr, true, textStatus, 'ping');
                return false;
            }

			DUPX.DAWS.FailureCount = 0;
			console.log("pingDAWS:AJAX success. Resetting failure count");

			// DATA FIELDS
			// archive_offset, archive_size, failures, file_index, is_done, timestamp

			if (typeof (data) != 'undefined' && data.pass == 1) {

				console.log("pingDAWS:Passed");

				var status = data.status;
				var percent = Math.round((status.archive_offset * 100.0) / status.archive_size);

				console.log("pingDAWS:updating progress percent");
				DUPX.updateProgressPercent(percent);

				var criticalFailureText = DUPX.getCriticalFailureText(status.failures);

				if(status.failures.length > 0) {
					console.log("pingDAWS:There are failures present. (" + status.failures.length) + ")";
				}

				if (criticalFailureText === null) {
					console.log("pingDAWS:No critical failures");
					if (status.is_done) {

						console.log("pingDAWS:archive has completed");
						if(status.failures.length > 0) {

							console.log(status.failures);
							var errorMessage = "pingDAWS:Problems during extract. These may be non-critical so continue with install.\n------\n";
							var len = status.failures.length;

							for(var j = 0; j < len; j++) {
								failure = status.failures[j];
								errorMessage += failure.subject + ":" + failure.description + "\n";
							}

							alert(errorMessage);
						}

						DUPX.clearDupArchiveStatusTimer();
						console.log("pingDAWS:calling finalizeDupArchiveExtraction");
						DUPX.finalizeDupArchiveExtraction(status);
						console.log("pingDAWS:after finalizeDupArchiveExtraction");

						var dataJSON = JSON.stringify(data);

						// Don't stop for non-critical failures - just display those at the end

						$("#ajax-logging").val($("input:radio[name=logging]:checked").val());
						$("#ajax-config-mode").val($("#config_mode").val());
						$("#ajax-json").val(escape(dataJSON));

						<?php if (!$GLOBALS['DUPX_DEBUG']) : ?>
						setTimeout(function () {
							$('#s1-result-form').submit();
						}, 500);
						<?php endif; ?>
						$('#progress-area').fadeOut(1000);
						//Failures aren't necessarily fatal - just record them for later display

						$("#ajax-json-debug").val(dataJSON);
					} else if (isClientSideKickoff) {
						console.log('pingDAWS:Archive not completed so continue ping DAWS in 500');
						setTimeout(DUPX.pingDAWS, 500);
					}
				}
				else {
					console.log("pingDAWS:critical failures present");
					// If we get a critical failure it means it's something we can't recover from so no purpose in retrying, just fail immediately.
					var errorString = 'Error Processing Step 1<br/>';

					errorString += criticalFailureText;

					DUPX.DAWSProcessingFailed(errorString);
				}
			} else {
				var errorString = 'Error Processing Step 1<br/>';
				errorString += data.error;

				DUPX.handleDAWSProcessingProblem(errorString, true);
			}
		},
		error: function (xHr, textStatus) {
			console.log('AJAX error. textStatus=');
			console.log(textStatus);
			DUPX.handleDAWSCommunicationProblem(xHr, true, textStatus, 'ping');
		}
	});
};


DUPX.isClientSideKickoff = function()
{
	return $('#clientside_kickoff').is(':checked');
}

DUPX.areConfigFilesPreserved = function()
{
	return $('#config_mode').is(':checked');
}

DUPX.kickOffDupArchiveExtract = function ()
{
	console.log('kickOffDupArchiveExtract:start');
	var $form = $('#s1-input-form');
	var request = new Object();
	var isClientSideKickoff = DUPX.isClientSideKickoff();

	request.action = "start_expand";
	request.archive_filepath = '<?php echo DUPX_U::esc_js($archive_path); ?>';
	request.restore_directory = '<?php echo DUPX_U::esc_js($root_path); ?>';
	request.worker_time = DUPX.DAWS.KickoffWorkerTimeInSec;
	request.client_driven = isClientSideKickoff ? 1 : 0;
	request.throttle_delay = DUPX.throttleDelay;
	request.filtered_directories = ['dup-installer'];

	var requestString = JSON.stringify(request);

	if (!isClientSideKickoff) {
		console.log('kickOffDupArchiveExtract:Setting timer');
		// If server is driving things we need to poll the status
		DUPX.dupArchiveStatusIntervalID = setInterval(DUPX.pingDAWS, DUPX.DAWS.StatusPeriodInMS);
	}
	else {
		console.log('kickOffDupArchiveExtract:client side kickoff');
	}

	console.log("daws url=" + DUPX.DAWS.Url);
	console.log("requeststring=" + requestString);

	$.ajax({
		type: "POST",
		timeout: DUPX.DAWS.KickoffWorkerTimeInSec * 2000,  // Double worker time and convert to ms
		url: DUPX.DAWS.Url + '&daws_action=start_expand',
		data: requestString,
		beforeSend: function () {
			DUPX.showProgressBar();
			$form.hide();
			$('#s1-result-form').show();
			DUPX.updateProgressPercent(0);
		},
		success: function (respData, textStatus, xHr) {
			try {
                var data = DUPX.parseJSON(respData);
            } catch(err) {
                console.error(err);
                console.error('JSON parse failed for response data: ' + respData);
				console.log('kickOffDupArchiveExtract:AJAX error. textStatus=', textStatus);
				DUPX.handleDAWSCommunicationProblem(xHr, false, textStatus);
                return false;
            }
			console.log('kickOffDupArchiveExtract:success');
			if (typeof (data) != 'undefined' && data.pass == 1) {

				var criticalFailureText = DUPX.getCriticalFailureText(status.failures);

				if (criticalFailureText === null) {

					var dataJSON = JSON.stringify(data);

					//RSR TODO:Need to check only for FATAL errors right now - have similar failure check as in pingdaws
					DUPX.DAWS.FailureCount = 0;
					console.log("kickOffDupArchiveExtract:Resetting failure count");

					$("#ajax-json-debug").val(dataJSON);
					if (typeof (data) != 'undefined' && data.pass == 1) {

						if (isClientSideKickoff) {
							console.log('kickOffDupArchiveExtract:Initial ping DAWS in 500');
							setTimeout(DUPX.pingDAWS, 500);
						}

					} else {
						$('#ajaxerr-data').html('Error Processing Step 1');
						DUPX.hideProgressBar();
					}
				} else {
					// If we get a critical failure it means it's something we can't recover from so no purpose in retrying, just fail immediately.
					var errorString = 'kickOffDupArchiveExtract:Error Processing Step 1<br/>';
					errorString += criticalFailureText;
					DUPX.DAWSProcessingFailed(errorString);
				}
			} else {
				if ('undefined' !== typeof data.isWPAlreadyExistsError
				&& data.isWPAlreadyExistsError) {
					DUPX.DAWSProcessingFailed(data.error);
				} else {
					var errorString = 'kickOffDupArchiveExtract:Error Processing Step 1<br/>';
					errorString += data.error;
					DUPX.handleDAWSProcessingProblem(errorString, false);
				}
			}
		},
		error: function (xHr, textStatus) {

			console.log('kickOffDupArchiveExtract:AJAX error. textStatus=', textStatus);
			DUPX.handleDAWSCommunicationProblem(xHr, false, textStatus);
		}
	});
};

DUPX.finalizeDupArchiveExtraction = function(dawsStatus)
{
	console.log("finalizeDupArchiveExtraction:start");
	var $form = $('#s1-input-form');
	$("#s1-input-form-extra-data").val(JSON.stringify(dawsStatus));
	console.log("finalizeDupArchiveExtraction:after stringify dawsstatus");
	var formData = $form.serialize();

	$.ajax({
		type: "POST",
		timeout: 30000,
		url: window.location.href,
		data: formData,		
		success: function (respData, textStatus, xHr) {
			try {
                var data = DUPX.parseJSON(respData);
            } catch(err) {
                console.error(err);
                console.error('JSON parse failed for response data: ' + respData);
				console.log("finalizeDupArchiveExtraction:error");
				console.log(xHr.statusText);
				console.log(xHr.getAllResponseHeaders());
				console.log(xHr.responseText);
                return false;
            }
			console.log("finalizeDupArchiveExtraction:success");
		},
		error: function (xHr) {
			console.log("finalizeDupArchiveExtraction:error");
			console.log(xHr.statusText);
			console.log(xHr.getAllResponseHeaders());
			console.log(xHr.responseText);
		}
	});
};

/**
 * Performs Ajax post to either do a zip or manual extract and then create db
 */
DUPX.runStandardExtraction = function ()
{
	var $form = $('#s1-input-form');

	//3600000 = 60 minutes
	//If the extraction takes longer than 30 minutes then user
	//will probably want to do a manual extraction or even FTP
	$.ajax({
		type: "POST",
		timeout: 3600000,
		cache: false,
		url: window.location.href,
		data: $form.serialize(),
		beforeSend: function () {
			DUPX.showProgressBar();
			$form.hide();
			$('#s1-result-form').show();
		},
		success: function (data, textStatus, xHr) {
			$("#ajax-json-debug").val(data);
			var dataJSON = data;
			data = DUPX.parseJSON(data, xHr, textStatus);
            if (false === data) {
                return;
            }
			$("#ajax-json-debug").val(dataJSON);
			if (typeof (data) != 'undefined' && data.pass == 1) {
				$("#ajax-logging").val($("input:radio[name=logging]:checked").val());
				$("#ajax-config-mode").val($("#config_mode").val());
				$("#ajax-json").val(escape(dataJSON));

				<?php if (!$GLOBALS['DUPX_DEBUG']) : ?>
					setTimeout(function () {$('#s1-result-form').submit();}, 500);
				<?php endif; ?>
				$('#progress-area').fadeOut(1000);
			} else {
				$('#ajaxerr-data').html('Error Processing Step 1');
				DUPX.hideProgressBar();
			}
		},
		error: function (xHr) {
			DUPX.ajaxCommunicationFailed(xHr, '', 'extract');
		}
	});
};

DUPX.ajaxCommunicationFailed = function (xhr, textStatus, page)
{
	var status = "<b>Server Code:</b> " + xhr.status + "<br/>";
	status += "<b>Status:</b> " + xhr.statusText + "<br/>";
	status += "<b>Response:</b> " + xhr.responseText + "<hr/>";

	if(textStatus && textStatus.toLowerCase() == "timeout" || textStatus.toLowerCase() == "service unavailable") {

		var default_timeout_message = "<b>Recommendation:</b><br/>";
			default_timeout_message += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?180116102141#faq-trouble-100-q'>this FAQ item</a> for possible resolutions.";
			default_timeout_message += "<hr>";
			default_timeout_message += "<b>Additional Resources...</b><br/>";
			default_timeout_message += "With thousands of different permutations it's difficult to try and debug/diagnose a server. If you're running into timeout issues and need help we suggest you follow these steps:<br/><br/>";
			default_timeout_message += "<ol>";
				default_timeout_message += "<li><strong>Contact Host:</strong> Tell your host that you're running into PHP/Web Server timeout issues and ask them if they have any recommendations</li>";
				default_timeout_message += "<li><strong>Dedicated Help:</strong> If you're in a time-crunch we suggest that you contact <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?180116150030#faq-resource-030-q'>professional server administrator</a>. A dedicated resource like this will be able to work with you around the clock to the solve the issue much faster than we can in most cases.</li>";
				default_timeout_message += "<li><strong>Consider Upgrading:</strong> If you're on a budget host then you may run into constraints. If you're running a larger or more complex site it might be worth upgrading to a <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?180116150030#faq-resource-040-q'>managed VPS server</a>. These systems will pretty much give you full control to use the software without constraints and come with excellent support from the hosting company.</li>";
				default_timeout_message += "<li><strong>Contact SnapCreek:</strong> We will try our best to help configure and point users in the right direction, however these types of issues can be time-consuming and can take time from our support staff.</li>";
			default_timeout_message += "</ol>";

		if(page) {
			switch(page) {
				default:
					status += default_timeout_message;
					break;
				case 'extract':
					status += "<b>Recommendation:</b><br/>";
					status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q'>this FAQ item</a> for possible resolutions.<br/><br/>";
					break;
				case 'ping':
					status += "<b>Recommendation:</b><br/>";
					status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?180116152758#faq-trouble-030-q'>this FAQ item</a> for possible resolutions.<br/><br/>";
					break;
                case 'delete-site':
                    status += "<b>Recommendation:</b><br/>";
					status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?180116153643#faq-installer-120-q'>this FAQ item</a> for possible resolutions.<br/><br/>";
					break;
			}
		} else {
			status += default_timeout_message;
		}

	}
	else if ((xhr.status == 403) || (xhr.status == 500)) {
		status += "<b>Recommendation:</b><br/>";
		status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-120-q'>this FAQ item</a> for possible resolutions.<br/><br/>"
	} else if ((xhr.status == 0) || (xhr.status == 200)) {
		status += "<b>Recommendation:</b><br/>";
		status += "Possible server timeout! Performing a 'Manual Extraction' can avoid timeouts.";
		status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q'>this FAQ item</a> for a complete overview.<br/><br/>"
	} else {
		status += "<b>Additional Resources:</b><br/> ";
		status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/'>Help Resources</a><br/>";
		status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/'>Technical FAQ</a>";
	}

	$('#ajaxerr-data').html(status);
	DUPX.hideProgressBar();
};

DUPX.parseJSON = function(mixData, xHr, textStatus) {
    try {
		var parsed = JSON.parse(mixData);
		return parsed;
	} catch (e) {
		console.log("JSON parse failed - 1");
	}

	var jsonStartPos = mixData.indexOf('{');
	var jsonLastPos = mixData.lastIndexOf('}');
	if (jsonStartPos > -1 && jsonLastPos > -1) {
		var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
		try {
			var parsed = JSON.parse(expectedJsonStr);
			return parsed;
		} catch (e) {
            console.log("JSON parse failed - 2");
            DUPX.ajaxCommunicationFailed(xHr, textStatus, 'extract');
            return false;
		}
	}
    DUPX.ajaxCommunicationFailed(xHr, textStatus, 'extract');
    return false;
}

/** Go back on AJAX result view */
DUPX.hideErrorResult = function ()
{
	$('#s1-result-form').hide();
	$('#s1-input-form').show(200);
}

/**
 * Accetps Usage Warning */
DUPX.acceptWarning = function ()
{
	if ($("#accept-warnings").is(':checked')) {
		$("#s1-deploy-btn").removeAttr("disabled");
		$("#s1-deploy-btn").removeAttr("title");
	} else {
		$("#s1-deploy-btn").attr("disabled", "true");
		$("#s1-deploy-btn").attr("title", "<?php echo DUPX_U::esc_js($agree_msg); ?>");
	}
};

DUPX.onSafeModeSwitch = function ()
{
    var mode = $('#exe_safe_mode').val();
    if (mode == 0) {
        $("#config_mode").removeAttr("disabled");
    } else if(mode == 1 || mode ==2) {
        $("#config_mode").val("NEW");
		$("#config_mode").attr("disabled", true);
		$('#config_mode option:eq(0)').prop('selected', true)
    }

    $('#exe-safe-mode').val(mode);
};

//DOCUMENT LOAD
$(document).ready(function() {
	DUPX.DAWS = new Object();
	DUPX.DAWS.Url = window.location.href + '?is_daws=1&daws_csrf_token=<?php echo urlencode(DUPX_CSRF::generate('daws'));?>';
	DUPX.DAWS.StatusPeriodInMS = 5000;
	DUPX.DAWS.PingWorkerTimeInSec = 9;
	DUPX.DAWS.KickoffWorkerTimeInSec = 6; // Want the initial progress % to come back quicker

    DUPX.DAWS.MaxRetries = 10;
	DUPX.DAWS.RetryDelayInMs = 8000;

	DUPX.dupArchiveStatusIntervalID = -1;
	DUPX.DAWS.FailureCount = 0;
	DUPX.throttleDelay = 0;

	//INIT Routines
	$("*[data-type='toggle']").click(DUPX.toggleClick);
	$("#tabs").tabs();
	DUPX.acceptWarning();
	<?php
    $isWindows = DUPX_U::isWindows();
    if (!$isWindows) {
    ?>
		$('#set_file_perms').trigger("click");
		$('#set_dir_perms').trigger("click");
	<?php
    }
    ?>
	DUPX.toggleSetupType();

	<?php echo ($arcCheck == 'Fail') ? "$('#s1-area-archive-file-link').trigger('click');" : ""; ?>
	<?php echo (!$all_success) ? "$('#s1-area-sys-setup-link').trigger('click');" : ""; ?>
});
</script>
