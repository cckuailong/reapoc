<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
$view_state = DUP_UI_ViewState::getArray();
$ui_css_general = (isset($view_state['dup-package-dtl-general-panel']) && $view_state['dup-package-dtl-general-panel']) ? 'display:block' : 'display:none';
$ui_css_storage = (isset($view_state['dup-package-dtl-storage-panel']) && $view_state['dup-package-dtl-storage-panel']) ? 'display:block' : 'display:none';
$ui_css_archive = (isset($view_state['dup-package-dtl-archive-panel']) && $view_state['dup-package-dtl-archive-panel']) ? 'display:block' : 'display:none';
$ui_css_install = (isset($view_state['dup-package-dtl-install-panel']) && $view_state['dup-package-dtl-install-panel']) ? 'display:block' : 'display:none';

$format = strtolower($package->Archive->Format);

$base_url			= admin_url('admin-ajax.php');
$link_sql			= add_query_arg(
							array(
								'action' => 'duplicator_download',
								'file' => "{$package->NameHash}_database.sql",
							),
							$base_url
						);
$link_archive 		= add_query_arg(
							array(
								'action' => 'duplicator_download',
								'file' => "{$package->NameHash}_archive.{$format}",
							),
							$base_url
						);
$link_installer		= add_query_arg(
							array(
								'action' => 'duplicator_download',
								'file' => $package->NameHash.'_installer.php',
							),
							$base_url
						);
$link_log			= "{$package->StoreURL}{$package->NameHash}.log";
$link_scan			= "{$package->StoreURL}{$package->NameHash}_scan.json";

$debug_on	     = DUP_Settings::Get('package_debug');
$mysqldump_on	 = DUP_Settings::Get('package_mysqldump') && DUP_DB::getMySqlDumpPath();
$mysqlcompat_on  = isset($Package->Database->Compatible) && strlen($Package->Database->Compatible);
$mysqlcompat_on  = ($mysqldump_on && $mysqlcompat_on) ? true : false;
$dbbuild_mode    = ($mysqldump_on) ? 'mysqldump' : 'PHP';
$archive_build_mode = ($package->Archive->Format === 'ZIP') ? 'ZipArchive (zip)' : 'DupArchive (daf)';
$dup_install_secure_on   = isset($package->Installer->OptsSecureOn) ? $package->Installer->OptsSecureOn : 0;
$dup_install_secure_pass = isset($package->Installer->OptsSecurePass) ? DUP_Util::installerUnscramble($package->Installer->OptsSecurePass) : '';
?>

<style>
	/*COMMON*/
	div.toggle-box {float:right; margin: 5px 5px 5px 0}
	div.dup-box {margin-top: 15px; font-size:14px; clear: both}
	table.dup-dtl-data-tbl {width:100%}
	table.dup-dtl-data-tbl tr {vertical-align: top}
	table.dup-dtl-data-tbl tr:first-child td {margin:0; padding-top:0 !important;}
	table.dup-dtl-data-tbl td {padding:0 5px 0 0; padding-top:10px !important;}
	table.dup-dtl-data-tbl td:first-child {font-weight: bold; width:130px}
	table.dup-sub-list td:first-child {white-space: nowrap; vertical-align: middle; width: 70px !important;}
	table.dup-sub-list td {white-space: nowrap; vertical-align:top; padding:0 !important; font-size:12px}
	div.dup-box-panel-hdr {font-size:14px; display:block; border-bottom: 1px dotted #efefef; margin:5px 0 5px 0; font-weight: bold; padding: 0 0 5px 0}
	tr.sub-item td:first-child {padding:0 0 0 40px}
	tr.sub-item td {font-size: 12px}
	tr.sub-item-disabled td {color:gray}

	/*STORAGE*/
	div.dup-store-pro {font-size:12px; font-style:italic;}
	div.dup-store-pro img {height:14px; width:14px; vertical-align: text-top}
	div.dup-store-pro a {text-decoration: underline}

	/*GENERAL*/
	div#dup-name-info, div#dup-version-info {display: none; font-size:11px; line-height:20px; margin:4px 0 0 0}
	div#dup-downloads-area {padding: 5px 0 5px 0; }
	div#dup-downloads-msg {margin-bottom:-5px; font-style: italic}
	div.sub-section {padding:7px 0 0 0}
	textarea.file-info {width:100%; height:100px; font-size:12px }

	/*INSTALLER*/
	div#dup-pass-toggle {position: relative; margin:0; width:273px}
	input#secure-pass {border-radius:4px 0 0 4px; width:250px; height: 23px; margin:0}
	button#secure-btn {height:23px; width:27px; position:absolute; top:0px; right:0px;border:1px solid silver;  border-radius:0 4px 4px 0; cursor:pointer}
	div.dup-install-hdr-2 {font-weight:bold; border-bottom:1px solid #dfdfdf; padding-bottom:2px; width:100%}
</style>

<?php if ($package_id == 0) :?>
	<div class="notice notice-error is-dismissible"><p><?php esc_html_e('Invalid Package ID request.  Please try again!', 'duplicator'); ?></p></div>
<?php endif; ?>

<div class="toggle-box">
	<a href="javascript:void(0)" onclick="Duplicator.Pack.OpenAll()">[open all]</a> &nbsp;
	<a href="javascript:void(0)" onclick="Duplicator.Pack.CloseAll()">[close all]</a>
</div>

<!-- ===============================
GENERAL -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="fa fa-archive fa-sm"></i> <?php esc_html_e('General', 'duplicator') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-package-dtl-general-panel" style="<?php echo esc_attr($ui_css_general); ?>">
	<table class='dup-dtl-data-tbl'>
		<tr>
			<td><?php esc_html_e('Name', 'duplicator') ?>:</td>
			<td>
				<a href="javascript:void(0);" onclick="jQuery('#dup-name-info').toggle()"><?php echo esc_js($package->Name); ?></a>
				<div id="dup-name-info">
					<b><?php esc_html_e('ID', 'duplicator') ?>:</b> <?php echo absint($package->ID); ?><br/>
					<b><?php esc_html_e('Hash', 'duplicator') ?>:</b> <?php echo esc_html($package->Hash); ?><br/>
					<b><?php esc_html_e('Full Name', 'duplicator') ?>:</b> <?php echo esc_html($package->NameHash); ?><br/>
				</div>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e('Notes', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->Notes) ? $package->Notes : esc_html__('- no notes -', 'duplicator') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Versions', 'duplicator') ?>:</td>
			<td>
				<a href="javascript:void(0);" onclick="jQuery('#dup-version-info').toggle()"><?php echo esc_html($package->Version); ?></a>
				<div id="dup-version-info">
					<b><?php esc_html_e('WordPress', 'duplicator') ?>:</b> <?php echo strlen($package->VersionWP) ? esc_html($package->VersionWP) : esc_html__('- unknown -', 'duplicator') ?><br/>
					<b><?php esc_html_e('PHP', 'duplicator') ?>:</b> <?php echo strlen($package->VersionPHP) ? esc_html($package->VersionPHP) : esc_html__('- unknown -', 'duplicator') ?><br/>
                    <b><?php esc_html_e('Mysql', 'duplicator') ?>:</b>
                    <?php echo strlen($package->VersionDB) ? esc_html($package->VersionDB) : esc_html__('- unknown -', 'duplicator') ?> |
                    <?php echo strlen($package->Database->Comments) ? esc_html($package->Database->Comments) : esc_html__('- unknown -', 'duplicator') ?><br/>
				</div>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e('Runtime', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->Runtime) ? esc_html($package->Runtime) : esc_html__("error running", 'duplicator'); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Status', 'duplicator') ?>:</td>
			<td><?php echo ($package->Status >= 100) ? esc_html__('completed', 'duplicator')  : esc_html__('in-complete', 'duplicator') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('User', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->WPUser) ? esc_html($package->WPUser) : esc_html__('- unknown -', 'duplicator') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Files', 'duplicator') ?>: </td>
			<td>
				<div id="dup-downloads-area">
					<?php if  (!$err_found) :?>

                        <button class="button" onclick="Duplicator.Pack.DownloadPackageFile(0, <?php echo absint($package->ID); ?>);return false;"><i class="fa fa-bolt fa-sm"></i> Installer</button>
                        <button class="button" onclick="Duplicator.Pack.DownloadPackageFile(1, <?php echo absint($package->ID); ?>);return false;"><i class="far fa-file-archive"></i> Archive - <?php echo esc_html($package->ZipSize); ?></button>
                        <button class="button" onclick="Duplicator.Pack.DownloadPackageFile(2, <?php echo absint($package->ID); ?>);return false;"><i class="fa fa-table fa-sm"></i> &nbsp; SQL - <?php echo esc_html(DUP_Util::byteSize($package->Database->Size))  ?></button>
                        <button class="button" onclick="Duplicator.Pack.DownloadPackageFile(3, <?php echo absint($package->ID); ?>);return false;"><i class="fa fa-table fa-sm"></i> &nbsp; <?php esc_html_e('Log', 'duplicator'); ?> </button>
						<button class="button" onclick="Duplicator.Pack.ShowLinksDialog('<?php echo esc_js($link_sql);?>','<?php echo esc_js($link_archive); ?>','<?php echo esc_js($link_installer); ?>','<?php echo esc_js($link_log);?>');" class="thickbox"><i class="fa fa-lock fa-xs"></i> &nbsp; <?php esc_html_e("Share", 'duplicator')?></button>
					<?php else: ?>
                        <button class="button" onclick="Duplicator.Pack.DownloadPackageFile(3, <?php echo absint($package->ID); ?>);return false;"><i class="fa fa-table fa-sm"></i> &nbsp; Log </button>
					<?php endif; ?>
				</div>
				<?php if (!$err_found) :?>
				<table class="dup-sub-list">
					<tr>
						<td><?php esc_html_e('Archive', 'duplicator') ?>: </td>
						<td><a href="<?php echo esc_url($link_archive); ?>"><?php echo esc_html($package->Archive->File); ?></a></td>
					</tr>
					<tr>
						<td><?php esc_html_e('Installer', 'duplicator') ?>: </td>
						<td><a href="#" onclick="Duplicator.Pack.DownloadPackageFile(0, <?php echo absint($package->ID); ?>);return false;" ><?php echo esc_html($package->Installer->File) ?></a></td>
					</tr>
					<tr>
						<td><?php esc_html_e('Database', 'duplicator') ?>: </td>
						<td><a href="<?php echo esc_url($link_sql); ?>" target="_blank"><?php echo esc_html($package->Database->File); ?></a></td>
					</tr>
				</table>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>
</div>

<!-- ==========================================
DIALOG: QUICK PATH -->
<?php add_thickbox(); ?>
<div id="dup-dlg-quick-path" title="<?php esc_attr_e('Download Links', 'duplicator'); ?>" style="display:none">
	<p>
		<i class="fa fa-lock fa-xs"></i>
		<?php esc_html_e("The following links contain sensitive data.  Please share with caution!", 'duplicator');	?>
	</p>

	<div style="padding: 0px 15px 15px 15px;">
		<a href="javascript:void(0)" style="display:inline-block; text-align:right" onclick="Duplicator.Pack.GetLinksText()">[Select All]</a> <br/>
		<textarea id="dup-dlg-quick-path-data" style='border:1px solid silver; border-radius:3px; width:99%; height:225px; font-size:11px'></textarea><br/>
		<i style='font-size:11px'><?php esc_html_e("The database SQL script is a quick link to your database backup script.  An exact copy is also stored in the package.", 'duplicator'); ?></i>
	</div>
</div>

<!-- ===============================
STORAGE -->
<div class="dup-box">
<div class="dup-box-title">
	<i class="fas fa-database fa-sm"></i> <?php esc_html_e('Storage', 'duplicator') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-package-dtl-storage-panel" style="<?php echo esc_attr($ui_css_storage); ?>">
	<table class="widefat package-tbl">
		<thead>
			<tr>
				<th style='width:150px'><?php esc_html_e('Name', 'duplicator') ?></th>
				<th style='width:100px'><?php esc_html_e('Type', 'duplicator') ?></th>
				<th style="white-space: nowrap"><?php esc_html_e('Location', 'duplicator') ?></th>
			</tr>
		</thead>
			<tbody>
				<tr class="package-row">
					<td><i class="fa fa-server"></i>&nbsp;<?php esc_html_e('Default', 'duplicator');?></td>
					<td><?php esc_html_e("Local", 'duplicator'); ?></td>
					<td><?php echo esc_html(DUPLICATOR_SSDIR_PATH); ?></td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="dup-store-pro">
							<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/amazon-64.png"); ?>" />
							<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/dropbox-64.png"); ?>" />
							<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/google_drive_64px.png"); ?>" />
							<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/onedrive-48px.png"); ?>" />
							<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/ftp-64.png"); ?>" />
							<?php echo sprintf(esc_html__('%1$s, %2$s, %3$s, %4$s, %5$s and other storage options available in', 'duplicator'), 'Amazon', 'Dropbox', 'Google Drive', 'OneDrive', 'FTP/SFTP'); ?>
                            <a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_storage_detail&utm_campaign=duplicator_pro" target="_blank"><?php esc_html_e('Duplicator Pro', 'duplicator');?></a>
							 <i class="far fa-lightbulb"
								data-tooltip-title="<?php esc_attr_e('Additional Storage:', 'duplicator'); ?>"
								data-tooltip="<?php esc_attr_e('Duplicator Pro allows you to create a package and then store it at a custom location on this server or to a cloud '
										. 'based location such as Google Drive, Amazon, Dropbox or FTP.', 'duplicator'); ?>">
							 </i>
                        </div>
					</td>
				</tr>
			</tbody>
	</table>
</div>
</div>


<!-- ===============================
ARCHIVE -->
<?php
	$css_db_filter_on   = $package->Database->FilterOn == 1 ? '' : 'sub-item-disabled';
?>
<div class="dup-box">
<div class="dup-box-title">
	<i class="far fa-file-archive"></i> <?php esc_html_e('Archive', 'duplicator') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-package-dtl-archive-panel" style="<?php echo esc_attr($ui_css_archive); ?>">

	<!-- FILES -->
	<table class='dup-dtl-data-tbl'>
		<tr>
			<td><?php esc_html_e('Build Mode', 'duplicator') ?>: </td>

			<td><?php echo esc_html($archive_build_mode); ?></td>
		</tr>

		<?php if ($package->Archive->ExportOnlyDB) : ?>
			<tr>
				<td><?php esc_html_e('Database Mode', 'duplicator') ?>: </td>
				<td><?php esc_html_e('Archive Database Only Enabled', 'duplicator')	?></td>
			</tr>
		<?php else : ?>
			<tr>
				<td><?php esc_html_e('Filters', 'duplicator') ?>: </td>
				<td>
					<?php echo $package->Archive->FilterOn == 1 ? 'On' : 'Off'; ?>
					<div class="sub-section">
						<b><?php esc_html_e('Directories', 'duplicator') ?>:</b> <br/>
						<?php
							$txt = strlen($package->Archive->FilterDirs)
								? str_replace(';', ";\n", $package->Archive->FilterDirs)
								: esc_html__('- no filters -', 'duplicator');
						?>
						<textarea class='file-info' readonly="true"><?php echo esc_textarea($txt); ?></textarea>
					</div>

					<div class="sub-section">
						<b><?php esc_html_e('Extensions', 'duplicator') ?>: </b><br/>
						<?php
						echo isset($package->Archive->FilterExts) && strlen($package->Archive->FilterExts)
							? esc_html($package->Archive->FilterExts)
							: esc_html__('- no filters -', 'duplicator');
						?>
					</div>

					<div class="sub-section">
						<b><?php esc_html_e('Files', 'duplicator') ?>:</b><br/>
						<?php
							$txt = strlen($package->Archive->FilterFiles)
								? str_replace(';', ";\n", $package->Archive->FilterFiles)
								: esc_html__('- no filters -', 'duplicator');
						?>
						<textarea class='file-info' readonly="true"><?php echo esc_html($txt); ?></textarea>
					</div>
				</td>
			</tr>
		<?php endif; ?>
	</table><br/>

	<!-- DATABASE -->
	<div class="dup-box-panel-hdr"><i class="fa fa-table fa-sm"></i> <?php esc_html_e('DATABASE', 'duplicator'); ?></div>
	<table class='dup-dtl-data-tbl'>
		<tr>
			<td><?php esc_html_e('Type', 'duplicator') ?>: </td>
			<td><?php echo esc_html($package->Database->Type); ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Build Mode', 'duplicator') ?>: </td>
			<td>
				<a href="?page=duplicator-settings&tab=package" target="_blank"><?php echo esc_html($dbbuild_mode); ?></a>
				<?php if ($mysqlcompat_on) : ?>
					<br/>
					<small style="font-style:italic; color:maroon">
						<i class="fa fa-exclamation-circle"></i> <?php esc_html_e('MySQL Compatibility Mode Enabled', 'duplicator'); ?>
						<a href="https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html#option_mysqldump_compatible" target="_blank">[<?php esc_html_e('details', 'duplicator'); ?>]</a>
					</small>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php esc_html_e('Filters', 'duplicator') ?>: </td>
			<td><?php echo $package->Database->FilterOn == 1 ? 'On' : 'Off'; ?></td>
		</tr>
		<tr class="sub-item <?php echo esc_attr($css_db_filter_on); ?>">
			<td><?php esc_html_e('Tables', 'duplicator') ?>: </td>
			<td>
				<?php
					echo isset($package->Database->FilterTables) && strlen($package->Database->FilterTables)
						? str_replace(',', "<br>\n", $package->Database->FilterTables)
						: esc_html__('- no filters -', 'duplicator');
				?>
			</td>
		</tr>
	</table>
</div>
</div>


<!-- ===============================
INSTALLER -->
<div class="dup-box" style="margin-bottom: 50px">
<div class="dup-box-title">
	<i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'duplicator') ?>
	<div class="dup-box-arrow"></div>
</div>
<div class="dup-box-panel" id="dup-package-dtl-install-panel" style="<?php echo esc_html($ui_css_install); ?>">

	<table class='dup-dtl-data-tbl'>
		<tr>
            <td colspan="2"><div class="dup-install-hdr-2"><?php esc_html_e(" Security", 'duplicator') ?></div></td>
        </tr>
		<tr>
			<td colspan="2">
				<?php esc_html_e("Password Protection", 'duplicator');?>:
				<?php echo $dup_install_secure_on ? "&nbsp; On" : "&nbsp; Off" ?>
			</td>
		</tr>
		<?php if ($dup_install_secure_on) :?>
			<tr>
				<td colspan="2">
					<div id="dup-pass-toggle">
						<input type="password" name="secure-pass" id="secure-pass" readonly="true" value="<?php echo esc_attr($dup_install_secure_pass); ?>" />
						<button type="button" id="secure-btn" onclick="Duplicator.Pack.TogglePassword()" title="<?php esc_attr_e('Show/Hide Password', 'duplicator'); ?>"><i class="fas fa-eye fa-xs"></i></button>
					</div>
				</td>
			</tr>
		<?php endif; ?>
	</table>
	<br/><br/>

	<table class='dup-dtl-data-tbl'>
		<tr>
			<td colspan="2"><div class="dup-install-hdr-2"><?php esc_html_e(" MySQL Server", 'duplicator') ?></div></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Host', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->Installer->OptsDBHost) ? esc_html($package->Installer->OptsDBHost) : esc_html__('- not set -', 'duplicator') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('Database', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->Installer->OptsDBName) ? esc_html($package->Installer->OptsDBName) : esc_html__('- not set -', 'duplicator') ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e('User', 'duplicator') ?>:</td>
			<td><?php echo strlen($package->Installer->OptsDBUser) ? esc_html($package->Installer->OptsDBUser) : esc_html__('- not set -', 'duplicator') ?></td>
		</tr>
	</table>
</div>
</div>

<?php if ($debug_on) : ?>
	<div style="margin:0">
		<a href="javascript:void(0)" onclick="jQuery(this).parent().find('.dup-pack-debug').toggle()">[<?php esc_html_e('View Package Object', 'duplicator') ?>]</a><br/>
		<pre class="dup-pack-debug" style="display:none"><?php @print_r($package); ?> </pre>
	</div>
<?php endif; ?>


<script>
jQuery(document).ready(function($)
{

	/*	Shows the 'Download Links' dialog
	 *	@param db		The path to the sql file
	 *	@param install	The path to the install file
	 *	@param pack		The path to the package file */
	Duplicator.Pack.ShowLinksDialog = function(db, install, pack, log)
	{
		var url = '#TB_inline?width=650&height=350&inlineId=dup-dlg-quick-path';
		tb_show("<?php esc_html_e('Package File Links', 'duplicator') ?>", url);

		var msg = <?php printf('"%s:\n" + db + "\n\n%s:\n" + install + "\n\n%s:\n" + pack + "\n\n%s:\n" + log;',
			esc_html__("DATABASE",  'duplicator'),
			esc_html__("PACKAGE", 'duplicator'),
			esc_html__("INSTALLER",   'duplicator'),
			esc_html__("LOG", 'duplicator'));
		?>
		$("#dup-dlg-quick-path-data").val(msg);
		return false;
	}

	//LOAD: 'Download Links' Dialog and other misc setup
	Duplicator.Pack.GetLinksText = function() {$('#dup-dlg-quick-path-data').select();};

	Duplicator.Pack.OpenAll = function () {
		Duplicator.UI.IsSaveViewState = false;
		var states = [];
		$("div.dup-box").each(function() {
			var pan = $(this).find('div.dup-box-panel');
			var panel_open = pan.is(':visible');
			if (! panel_open)
				$( this ).find('div.dup-box-title').trigger("click");
			states.push({
				key: pan.attr('id'),
				value: 1
			});
		});
		Duplicator.UI.SaveMulViewStates(states);
		Duplicator.UI.IsSaveViewState = true;
	};

	Duplicator.Pack.CloseAll = function () {
		Duplicator.UI.IsSaveViewState = false;
		var states = [];
		$("div.dup-box").each(function() {
			var pan = $(this).find('div.dup-box-panel');
			var panel_open = pan.is(':visible');
			if (panel_open)
				$( this ).find('div.dup-box-title').trigger("click");
			states.push({
				key: pan.attr('id'),
				value: 0
			});
		});
		Duplicator.UI.SaveMulViewStates(states);
		Duplicator.UI.IsSaveViewState = true;
	};

	Duplicator.Pack.TogglePassword = function()
	{
		var $input  = $('#secure-pass');
		var $button =  $('#secure-btn');
		if (($input).attr('type') == 'text') {
			$input.attr('type', 'password');
			$button.html('<i class="fas fa-eye fa-xs"></i>');
		} else {
			$input.attr('type', 'text');
			$button.html('<i class="fas fa-eye-slash fa-xs"></i>');
		}
	}
});
</script>