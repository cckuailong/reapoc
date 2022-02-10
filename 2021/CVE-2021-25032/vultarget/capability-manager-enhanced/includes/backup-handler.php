<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process update operations from Backup screen
 * 
 */

class Capsman_BackupHandler
{
	var $cm;

	function __construct( $manager_obj ) {
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('restore_roles'))
			wp_die( __( 'You do not have permission to restore roles.', 'capsman-enhanced' ) );
	
		$this->cm = $manager_obj;
	}
	
	/**
	 * Processes backups and restores.
	 *
	 * @return void
	 */
	function processBackupTool ()
	{
        if (isset($_POST['save_backup'])) {
			check_admin_referer('pp-capabilities-backup');
		
			global $wpdb;
			$wp_roles = $wpdb->prefix . 'user_roles';
			$cm_roles = $this->cm->ID . '_backup';
			$cm_roles_initial = $this->cm->ID . '_backup_initial';

			if ( ! get_option( $cm_roles_initial ) ) {
				if ( $current_backup = get_option( $cm_roles ) ) {
					update_option( $cm_roles_initial, $current_backup, false );

					if ( $initial_datestamp = get_option( $this->cm->ID . '_backup_datestamp' ) ) {
						update_option($this->cm->ID . '_backup_initial_datestamp', $initial_datestamp, false );
					}
				}
			}

			$roles = get_option($wp_roles);
			update_option($cm_roles, $roles, false);
			update_option($this->cm->ID . '_backup_datestamp', current_time( 'timestamp' ), false );
			ak_admin_notify(__('New backup saved.', 'capsman-enhanced'));
				
        }

        if (isset($_POST['restore_backup'])) {
            check_admin_referer('pp-capabilities-backup');

            global $wpdb;
            $wp_roles = $wpdb->prefix . 'user_roles';
            $cm_roles = $this->cm->ID . '_backup';
            $cm_roles_initial = $this->cm->ID . '_backup_initial';

            switch ($_POST['select_restore']) {
				case 'restore_initial':
					if ($roles = get_option($cm_roles_initial)) {
						update_option($wp_roles, $roles);
						ak_admin_notify(__('Roles and Capabilities restored from initial backup.', 'capsman-enhanced'));
					} else {
						ak_admin_error(__('Restore failed. No backup found.', 'capsman-enhanced'));
					}
					break;

				case 'restore':
					if ($roles = get_option($cm_roles)) {
						update_option($wp_roles, $roles);
						ak_admin_notify(__('Roles and Capabilities restored from last backup.', 'capsman-enhanced'));
					} else {
						ak_admin_error(__('Restore failed. No backup found.', 'capsman-enhanced'));
					}
					break;

				default:
                    if ($roles = get_option($_POST['select_restore'])) {
						update_option($wp_roles, $roles);
						ak_admin_notify(__('Roles and Capabilities restored from selected auto-backup.', 'capsman-enhanced'));
					} else {
						ak_admin_error(__('Restore failed. No backup found.', 'capsman-enhanced'));
					}
			}
		}
	}
	
	/**
	 * Resets roles to WordPress defaults.
	 *
	 * @return void
	 */
	function backupToolReset ()
	{
		check_admin_referer('capsman-reset-defaults');
	
		require_once(ABSPATH . 'wp-admin/includes/schema.php');

		if ( ! function_exists('populate_roles') ) {
			ak_admin_error(__('Needed function to create default roles not found!', 'capsman-enhanced'));
			return;
		}

		$roles = array_keys( ak_get_roles(true) );

		foreach ( $roles as $role) {
			remove_role($role);
		}

		populate_roles();
		$this->cm->setAdminCapability();

		$msg = __('Roles and Capabilities reset to WordPress defaults', 'capsman-enhanced');
		
		if ( function_exists( 'pp_populate_roles' ) ) {
			pp_populate_roles();
		} else {
			// force PP to repopulate roles
			$pp_ver = get_option( 'pp_c_version', true );
			if ( $pp_ver && is_array($pp_ver) ) {
				$pp_ver['version'] = ( preg_match( "/dev|alpha|beta|rc/i", $pp_ver['version'] ) ) ? '0.1-beta' : 0.1;
			} else {
				$pp_ver = array( 'version' => '0.1', 'db_version' => '1.0' );
			}

			update_option( 'pp_c_version', $pp_ver );
			delete_option( 'ppperm_added_role_caps_10beta' );
		}
		
		ak_admin_notify($msg);
	}
}
