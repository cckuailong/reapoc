<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class UpdraftPlus_Temporary_Clone_User_Notice {
	
	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_filter('wp_authenticate_user', array($this, 'wp_authenticate_user'));
		add_action('wp_ajax_updraftplus_user_notice_ajax', array($this, 'updraftplus_user_notice_ajax'));
		add_action('all_admin_notices', array($this, 'all_admin_notices_users_notice'));
	}

	/**
	 * This function will add a dashboard notice to the users page, that gives the user the option to enable admin only logins to the clone.
	 *
	 * @return void
	 */
	public function all_admin_notices_users_notice() {
		global $pagenow;

		if ('users.php' != $pagenow) return;

		$admin_login = get_site_option('updraftplus_clone_admin_only_login');

		?>
		<div id="updraftplus_temporary_clone-usernotice" class="updated">
			<h1><?php _e('UpdraftPlus temporary clone user login settings:', 'updraftplus'); ?></h1>
			<p><?php _e('You can forbid non-admins logins to this cloned site by checking the checkbox below', 'updraftplus'); ?></p>
			<input type="checkbox" name="updraftplus_clone_admin_only" value="1" <?php if ($admin_login) echo 'checked="checked"'; ?> onclick="jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'updraftplus_user_notice_ajax', subaction: 'admin_only_login', nonce: '<?php echo wp_create_nonce('updraftplus_admin_only_login');?>', admin_only_login: jQuery(this).is(':checked') });"> <?php _e('Allow only administrators to log in', 'updraftplus'); ?><br>
		</div>
		<?php
	}

	/**
	 * This function will check if the user trying to login is an admin and if non admin logins have been disabled. If non admin logins are disabled and the user logging in is not a admin then it will stop the login and return an error.
	 * Runs upon the WP filter wp_authenticate_user
	 *
	 * @param object $user - the user login object
	 *
	 * @return object|WP_Error - retruns the logged in user or a WP_Error stopping non admin logins
	 */
	public function wp_authenticate_user($user) {
		// The WP_User object does not exist in WP 3.2, so we don't check for that
		if (is_wp_error($user) || !is_object($user) || empty($user->ID)) return $user;
		
		$admin_login = get_site_option('updraftplus_clone_admin_only_login');
		$user_is_admin = user_can($user->ID, 'manage_options');

		if (!$user_is_admin && $admin_login) {
			return new WP_Error('user_login_disabled', '<strong>ERROR</strong>: This user account is not allowed to login.');
		}

		return $user;
	}

	/**
	 * This function will perform security checks before allowing the ajax calls for the UpdraftPlus clone VPS mu-plugin be processed.
	 *
	 * @return void
	 */
	public function updraftplus_user_notice_ajax() {

		if (is_user_logged_in() && current_user_can('manage_options')) {
			$this->process_user_notice_ajax();
		}
	}

	/**
	 * This function will handle the ajax calls for the UpdraftPlus clone user notice mu-plugin.
	 *
	 * @return void
	 */
	public function process_user_notice_ajax() {
		$return = array('code' => 'fail', 'data' => '');
		
		if (!isset($_POST['subaction'])) {
			$return['code'] = 'error';
			$return['data'] = 'Missing subaction';
			echo json_encode($return);
			die();
		}

		if ('admin_only_login' == $_POST['subaction']) {
			check_ajax_referer('updraftplus_admin_only_login', 'nonce');

			if (!isset($_POST['admin_only_login'])) {
				$return['code'] = 'error';
				$return['data'] = 'Missing parameter';
				echo json_encode($return);
				die();
			}

			$admin_only = ('true' === $_POST['admin_only_login']);
			
			update_site_option('updraftplus_clone_admin_only_login', $admin_only);

			$return['code'] = 'success';
			$return['data'] = 'Option updated';
			echo json_encode($return);
			die();
		} else {
			$return['code'] = 'error';
			$return['data'] = 'Unknown action';
			echo json_encode($return);
			die();
		}
	}
}

if (defined('UPDRAFTPLUS_THIS_IS_CLONE')) {
	new UpdraftPlus_Temporary_Clone_User_Notice();
}
