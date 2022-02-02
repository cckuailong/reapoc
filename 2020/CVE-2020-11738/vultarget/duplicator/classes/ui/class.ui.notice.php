<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Used to display notices in the WordPress Admin area
 * This class takes advantage of the admin_notice action.
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 *
 */

// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_UI_Notice
{
    /**
     * Shows a display message in the wp-admin if any reserved files are found
     *
     * @return string   Html formated text notice warnings
     */
    public static function showReservedFilesNotice()
    {
        //Show only on Duplicator pages and Dashboard when plugin is active
        $dup_active = is_plugin_active('duplicator/duplicator.php');
        $dup_perm   = current_user_can('manage_options');
        if (!$dup_active || !$dup_perm)
			return;

		$screen = get_current_screen();
        if (!isset($screen))
			return;

		$is_installer_cleanup_req = ($screen->id == 'duplicator_page_duplicator-tools' && isset($_GET['action']) && $_GET['action'] == 'installer');
        if (DUP_Server::hasInstallerFiles() && !$is_installer_cleanup_req) {

			$on_active_tab = isset($_GET['section'])? $_GET['section']: '';
            echo '<div class="dup-updated notice-success" id="dup-global-error-reserved-files"><p>';

			//Safe Mode Notice
			$safe_html = '';
			if(get_option("duplicator_exe_safe_mode", 0) > 0 ){
				$safe_msg1 = __('Safe Mode:', 'duplicator');
				$safe_msg2 = __('During the install safe mode was enabled deactivating all plugins.<br/> Please be sure to ', 'duplicator');
				$safe_msg3 = __('re-activate the plugins', 'duplicator');
				$safe_html = "<div class='notice-safemode'><b>{$safe_msg1}</b><br/>{$safe_msg2} <a href='plugins.php'>{$safe_msg3}</a>!</div><br/>";
			}

			//On Tools > Cleanup Page
            if ($screen->id == 'duplicator_page_duplicator-tools' && ($on_active_tab == "info" || $on_active_tab == '') ) {

				$title = __('This site has been successfully migrated!', 'duplicator');
				$msg1  = __('Final step(s):', 'duplicator');
				$msg2  = __('This message will be removed after all installer files are removed.  Installer files must be removed to maintain a secure site.  '
									. 'Click the link above or button below to remove all installer files and complete the migration.', 'duplicator');

				echo "<b class='pass-msg'><i class='fa fa-check-circle'></i> ".esc_html($title)."</b> <br/> {$safe_html} <b>".esc_html($msg1)."</b> <br/>";
				printf("1. <a href='javascript:void(0)' onclick='jQuery(\"#dup-remove-installer-files-btn\").click()'>%s</a><br/>", esc_html__('Remove Installation Files Now!', 'duplicator'));
                printf("2. <a href='https://wordpress.org/support/plugin/duplicator/reviews/?filter=5' target='wporg'>%s</a> <br/> ", esc_html__('Optionally, Review Duplicator at WordPress.org...', 'duplicator'));
				echo "<div class='pass-msg'>".esc_html($msg2)."</div>";

			//All other Pages
            } else {

				$title = __('Migration Almost Complete!', 'duplicator');
				$msg   = __('Reserved Duplicator installation files have been detected in the root directory.  Please delete these installation files to '
						. 'avoid security issues. <br/> Go to:Duplicator > Tools > Information >Stored Data and click the "Remove Installation Files" button', 'duplicator');

				$nonce = wp_create_nonce('duplicator_cleanup_page');
				$url   = self_admin_url('admin.php?page=duplicator-tools&tab=diagnostics&section=info&_wpnonce='.$nonce);
				echo "<b>{$title}</b><br/> {$safe_html} {$msg}";
				@printf("<br/><a href='{$url}'>%s</a>", __('Take me there now!', 'duplicator'));

            }
            echo "</p></div>";
        }
    }

    /**
     * Shows a message for redirecting a page
     *
     * @return string   The location to redirect to
     */
    public static function redirect($location)
    {
        echo '<div class="dup-redirect"><i class="fas fa-circle-notch fa-spin fa-fw"></i>';
		esc_html__('Redirecting Please Wait...', 'duplicator');
		echo '</div>';
		echo "<script>window.location = '{$location}';</script>";
		die(esc_html__('Invalid token permissions to perform this request.', 'duplicator'));
	}
	
	
    /**
     * Shows install deactivated function
     */
    public static function installAutoDeactivatePlugins() {
        $reactivatePluginsAfterInstallation = get_option('duplicator_reactivate_plugins_after_installation', false);
        if (is_array($reactivatePluginsAfterInstallation)) {
            $shouldBeActivated = array();
            foreach ($reactivatePluginsAfterInstallation as $pluginSlug => $pluginTitle) {
                if (!is_plugin_active($pluginSlug)) {
                    $shouldBeActivated[$pluginSlug] = $pluginTitle;
                }
            }
            
            if (empty($shouldBeActivated)) {
                delete_option('duplicator_reactivate_plugins_after_installation', false);
            } else {
                $activatePluginsAnchors = array();
                foreach ($shouldBeActivated as $slug => $title) {
                    $activateURL = wp_nonce_url(admin_url('plugins.php?action=activate&plugin='.$slug), 'activate-plugin_'.$slug);
                    $anchorTitle = sprintf(esc_html__('Activate %s', 'duplicator'), $title);
                    $activatePluginsAnchors[] = '<a href="'.$activateURL.'" 
                                                    title="'.esc_attr($anchorTitle).'">'.
                                                    $title.'</a>';
                }

                echo "<div class='update-nag dpro-admin-notice'>
                        <p>".
                            "<b>Warning!</b> Migration Almost Complete! <br/>".
                            "Plugin(s) listed here was deactivated during installation, Please activate them: <br/>".
                            implode(' ,', $activatePluginsAnchors).
                        "</p>".
                    "</div>";
            }
        }
    }

    /**
     * Shows feedback notices after certain no. of packages successfully created
     */
    public static function showFeedBackNotice() {
        $notice_id = 'rate_us_feedback';

		if (!current_user_can('manage_options')) {
			return;
		}

        $notices = get_user_meta(get_current_user_id(), DUPLICATOR_ADMIN_NOTICES_USER_META_KEY, true);

        $duplicator_pages = array(
            'toplevel_page_duplicator',
            'duplicator_page_duplicator-tools',
            'duplicator_page_duplicator-settings',
            'duplicator_page_duplicator-gopro',
        );

        if (!in_array(get_current_screen()->id, $duplicator_pages) || (isset($notices[$notice_id]) && 'true' === $notices[$notice_id])) {
			return;
		}

		$packagesCount = $GLOBALS['wpdb']->get_var('SELECT count(id)    FROM '.DUP_Util::getTablePrefix().'duplicator_packages         WHERE status=100');

		if ($packagesCount < DUPLICATOR_FEEDBACK_NOTICE_SHOW_AFTER_NO_PACKAGE) {
			return;
		}

		$dismiss_url = add_query_arg(array(
			'action' => 'duplicator_set_admin_notice_viewed',
			'notice_id' => esc_attr($notice_id),
        ), admin_url('admin-post.php'));
		?>
		<div class="notice updated duplicator-message duplicator-message-dismissed" data-notice_id="<?php echo esc_attr( $notice_id); ?>">
			<div class="duplicator-message-inner">
				<div class="duplicator-message-icon">
					<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/logo.png"); ?>" style="text-align:top; margin:0; height:60px; width:60px;" alt="Duplicator">
				</div>
				<div class="duplicator-message-content">
					<p><strong><?php echo __( 'Congrats!', 'duplicator' ); ?></strong> <?php printf(esc_html__('You created over %d packages with Duplicator. Great job! If you can spare a minute, please help us by leaving a five star review on WordPress.org.', 'duplicator'), DUPLICATOR_FEEDBACK_NOTICE_SHOW_AFTER_NO_PACKAGE); ?></p>
					<p class="duplicator-message-actions">
						<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5/#new-post" target="_blank" class="button button-primary duplicator-notice-rate-now"><?php esc_html_e("Sure! I'd love to help", 'duplicator' ); ?></a>
						<a href="<?php echo esc_url_raw($dismiss_url); ?>" class="button duplicator-notice-dismiss"><?php esc_html_e('Hide Notification', 'duplicator'); ?></a>
					</p>
				</div>
			</div>
		</div>
		<?php
    }
}