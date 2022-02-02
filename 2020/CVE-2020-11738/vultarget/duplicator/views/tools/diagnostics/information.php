<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
wp_enqueue_script('dup-handlebars');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/utilities/class.u.scancheck.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/classes/class.io.php');

$installer_files	= DUP_Server::getInstallerFiles();
$package_name		= (isset($_GET['package'])) ?  esc_html($_GET['package']) : '';
$abs_path			= duplicator_get_abs_path();

// For auto detect archive file name logic
if (empty($package_name)) {
    $installer_file_path = $abs_path . '/' . 'installer.php';
    if (file_exists($installer_file_path)) {
        $installer_file_data = file_get_contents($installer_file_path);
        if (preg_match("/const ARCHIVE_FILENAME	 = '(.*?)';/", $installer_file_data, $match)) {
            $temp_archive_file = esc_html($match[1]);
            $temp_archive_file_path = $abs_path . '/' . $temp_archive_file;
            if (file_exists($temp_archive_file_path)) {
                $package_name = $temp_archive_file;
            }
        }
    }
}
$package_path	= empty($package_name) ? '' : $abs_path . '/' . $package_name;
$txt_found		= __('File Found: Unable to remove', 'duplicator');
$txt_removed	= __('Removed', 'duplicator');
$nonce			= wp_create_nonce('duplicator_cleanup_page');
$section		= (isset($_GET['section'])) ?$_GET['section']:'';

if ($section == "info" || $section == '') {

	$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : 'display';

	if (isset($_REQUEST['_wpnonce'])) {
		if (($_GET['action'] == 'installer') || ($_GET['action'] == 'tmp-cache')) {
			if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'duplicator_cleanup_page')) {
				exit; // Get out of here bad nounce!
			}
		}
	}

	switch ($_GET['action']) {
		case 'installer' :
			$action_response = __('Installer file cleanup ran!', 'duplicator');
			break;
		case 'tmp-cache':
			DUP_Package::tempFileCleanup(true);
			$action_response = __('Build cache removed.', 'duplicator');
			break;
	}

	 if ($_GET['action'] != 'display')  :	?>
		<div id="message" class="notice notice-success is-dismissible  dup-wpnotice-box">
			<p><b><?php echo esc_html($action_response); ?></b></p>
			<?php 
                if ( $_GET['action'] == 'installer') :
                    $remove_error = false;

					// Move installer log before cleanup
					$installer_log_path = DUPLICATOR_INSTALLER_DIRECTORY.'/dup-installer-log__'.DUPLICATOR_INSTALLER_HASH_PATTERN.'.txt';
					$glob_files = glob($installer_log_path);
					if (!empty($glob_files) && wp_mkdir_p(DUPLICATOR_SSDIR_PATH_INSTALLER)) {
						foreach ($glob_files as $glob_file) {
							$installer_log_file_path = $glob_file;
							DUP_IO::copyFile($installer_log_file_path, DUPLICATOR_SSDIR_PATH_INSTALLER);
						}
					}

					$html = "";
					//REMOVE CORE INSTALLER FILES
					$installer_files = DUP_Server::getInstallerFiles();
					$removed_files = false;
					foreach ($installer_files as $filename => $path) {
						$file_path = '';
						if (stripos($filename, '[hash]') !== false) {
							$glob_files = glob($path);
							if (!empty($glob_files)) {
								foreach ($glob_files as $glob_file) {
									$file_path = $glob_file;
									DUP_IO::deleteFile($file_path);
									$removed_files = true;
								}
							}
						} else if (is_file($path)) {
							$file_path = $path;
							DUP_IO::deleteFile($path);
							$removed_files = true;
						} else if (is_dir($path)) {
							$file_path = $path;

							// Extra protection to ensure we only are deleting the installer directory
							if(DUP_STR::contains($path, 'dup-installer')) {
								DUP_IO::deleteTree($path);
								$removed_files = true;
							}
						}

						if (!empty($file_path)) {
                            if (file_exists($file_path)) {
                                echo "<div class='failed'><i class='fa fa-exclamation-triangle fa-sm'></i> {$txt_found} - ".esc_html($file_path)."  </div>";
                                $remove_error = true;
                            } else {
                                echo "<div class='success'> <i class='fa fa-check'></i> {$txt_removed} - ".esc_html($file_path)."	</div>";
                            }
						}
					}

					//No way to know exact name of archive file except from installer.
					//The only place where the package can be removed is from installer
					//So just show a message if removing from plugin.
					if (file_exists($package_path)) {
						$path_parts	 = pathinfo($package_name);
						$path_parts	 = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
						$valid_ext = ($path_parts == "zip" || $path_parts == "daf");
						if ($valid_ext && !is_dir($package_path)) {
							$html .= (@unlink($package_path))
										? "<div class='success'><i class='fa fa-check'></i> ".esc_html($txt_removed)." - ".esc_html($package_path)."</div>"
										: "<div class='failed'><i class='fa fa-exclamation-triangle fa-sm'></i> ".esc_html($txt_found)." - ".esc_html($package_path)."</div>";
						}
					}
					echo $html;

					if (!$removed_files) {
						echo '<div class="dup-alert-no-files-msg success">'
								. '<i class="fa fa-check"></i> <b>' . esc_html__('No Duplicator installer files found on this WordPress Site.', 'duplicator') . '</b>'
							. '</div>';
					}
				 ?>

				<div class="dup-alert-secure-note">
					<?php
						echo '<b><i class="fa fa-shield"></i> ' . esc_html__('Security Notes', 'duplicator') . ':</b>&nbsp;';
						_e('If the installer files do not successfully get removed with this action, then they WILL need to be removed manually through your hosts control panel  '
						 . 'or FTP.  Please remove all installer files to avoid any security issues on this site.  For more details please visit '
						 . 'the FAQ link <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-295-q" target="_blank">Which files need to be removed after an install?</a>', 'duplicator');

						echo '<br/><br/>';

                        if ($remove_error) {
                            echo  __('Some of the installer files did not get removed, ', 'duplicator').
                                    '<a href="#" onclick="Duplicator.Tools.deleteInstallerFiles(); return false;" >'.
                                    __('please retry the installer cleanup process', 'duplicator').
                                    '</a>.'.
                                    __(' If this process continues please see the previous FAQ link.', 'duplicator').
                                    '<br><br>';
                        }

						echo '<b><i class="fa fa-thumbs-o-up"></i> ' . esc_html__('Help Support Duplicator', 'duplicator') . ':</b>&nbsp;';
						_e('The Duplicator team has worked many years to make moving a WordPress site a much easier process.  Show your support with a '
						 . '<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5" target="_blank">5 star review</a>!  We would be thrilled if you could!', 'duplicator');
					?>
				</div>

			<?php endif; ?>
		</div>
	<?php endif;
	if(isset($_GET['action']) && $_GET['action']=="installer" && get_option("duplicator_exe_safe_mode")){
		$safe_title = __('This site has been successfully migrated!');
		$safe_msg = __('Please test the entire site to validate the migration process!');

		switch(get_option("duplicator_exe_safe_mode")){

			//safe_mode basic
			case 1:
				$safe_msg = __('NOTICE: Safe mode (Basic) was enabled during install, be sure to re-enable all your plugins.');
			break;

			//safe_mode advance
			case 2:
				$safe_msg = __('NOTICE: Safe mode (Advanced) was enabled during install, be sure to re-enable all your plugins.');

				$temp_theme = null;
				$active_theme = wp_get_theme();
				$available_themes = wp_get_themes();
				foreach($available_themes as $theme){
					if($temp_theme == null && $theme->stylesheet != $active_theme->stylesheet){
						$temp_theme = array('stylesheet' => $theme->stylesheet, 'template' => $theme->template);
						break;
					}
				}

				if($temp_theme != null){
					//switch to another theme then backto default
					switch_theme($temp_theme['template'], $temp_theme['stylesheet']);
					switch_theme($active_theme->template, $active_theme->stylesheet);
				}

			break;
		}

		if (! DUP_Server::hasInstallerFiles()) {
			echo  "<div class='notice notice-success cleanup-notice'><p><b class='title'><i class='fa fa-check-circle'></i> ".esc_html($safe_title)."</b> "
				. "<div class='notice-safemode'>".esc_html($safe_msg)."</p></div></div>";
		}

		delete_option("duplicator_exe_safe_mode");
	}
}
?>


<form id="dup-settings-form" action="<?php echo admin_url( 'admin.php?page=duplicator-tools&tab=diagnostics&section=info' ); ?>" method="post">
	<?php wp_nonce_field( 'duplicator_settings_page', '_wpnonce', false ); ?>
	<input type="hidden" id="dup-remove-options-value" name="remove-options" value="">

	<?php
		if (isset($_POST['remove-options'])) {
			$remove_options = sanitize_text_field($_POST['remove-options']);
			$action_result = DUP_Settings::DeleteWPOption($remove_options);
			switch ($remove_options)
			{
				case 'duplicator_settings'		 : 	$remove_response = __('Plugin settings reset.', 'duplicator');		break;
				case 'duplicator_ui_view_state'  : 	$remove_response = __('View state settings reset.', 'duplicator');	 break;
				case 'duplicator_package_active' : 	$remove_response = __('Active package settings reset.', 'duplicator'); break;
			}
		}

		if (! empty($remove_response))  {
			echo "<div id='message' class='notice notice-success is-dismissible dup-wpnotice-box'><p>".esc_html($remove_response)."</p></div>";
		}

		include_once 'inc.data.php';
		include_once 'inc.settings.php';
		include_once 'inc.validator.php';
		include_once 'inc.phpinfo.php';
	?>
</form>
