<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class UpdraftPlus_Temporary_Clone_Dash_Notice {
	
	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action('updraftplus_temporary_clone_refresh_connection', array($this, 'refresh_connection'));
		add_action('wp_ajax_updraftplus_dash_notice_ajax', array($this, 'updraftplus_dash_notice_ajax'));
		add_action('all_admin_notices', array($this, 'all_admin_notices_dashboard_notice'));
		
		if (!wp_next_scheduled('updraftplus_temporary_clone_refresh_connection')) {
			wp_schedule_event(time(), 'twicedaily', 'updraftplus_temporary_clone_refresh_connection');
		}

		if ('' == get_site_option('updraftplus_clone_scheduled_removal', '') || 0 == get_site_option('updraftplus_clone_package_cost', 0)) {
			$this->refresh_connection();
		}
	}

	/**
	 * This function will add a dashboard notice to every page, that shows the user when their clone will expire and directs them to UpdraftPlus.com to extend their clones life.
	 *
	 * @return void
	 */
	public function all_admin_notices_dashboard_notice() {
		$date = strtotime(get_site_option('updraftplus_clone_scheduled_removal', ''));
		if ('' == $date) {
			$pretty_date = __('Unable to get renew date', 'updraftplus');
			$date_diff = '';
		} else {
			$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int) $date), 'M d, Y G:i');
			$date_diff = sprintf(__('%s from now', 'updraftplus'), human_time_diff($date));
		}

		$package_cost = get_site_option('updraftplus_clone_package_cost', 0);
		$package_cost = empty($package_cost) ? 1 : $package_cost;
		?>
		<div id="updraftplus_temporary_clone-dashnotice" class="updated">
			<div style="float:right;"><a href="#" onclick="jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'updraftplus_dash_notice_ajax', subaction: 'refresh_connection', nonce: '<?php echo wp_create_nonce('updraftplus_refresh_connection');?>' }, function() { location.reload(); });"><?php _e('Refresh connection', 'updraftplus'); ?></a></div>
			<h1><?php _e('Welcome to your UpdraftClone (temporary clone)', 'updraftplus'); ?></h1>
			<p>
				<?php echo __('Your clone will renew on:', 'updraftplus') . ' ' . $pretty_date . ' ' . get_option('timezone_string') . ' (' . $date_diff . ')'; ?>.
				<?php echo sprintf(__('Each time your clone renews (weekly) it costs %s. You can shut this clone down at the following link:', 'updraftplus'), sprintf(_n('%d token', '%d tokens', $package_cost, 'updraftplus'), $package_cost)); ?> <a target="_blank" href="https://updraftplus.com/my-account/clones/"><?php _e('Manage your clones', 'updraftplus'); ?></a>
			</p>
			<?php
			$show_removal_warning = get_site_option('updraftplus_clone_removal_warning', false);

			if ($show_removal_warning) echo '<p>'.__('Warning: You have no clone tokens remaining and either no subscriptions or no subscription that will renew before the clone expiry date.', 'updraftplus').'</p>'
			?>
		</div>
		<?php
	}

	/**
	 * This function will perform security checks before allowing the ajax calls for the UpdraftPlus clone VPS mu-plugin be processed.
	 *
	 * @return void
	 */
	public function updraftplus_dash_notice_ajax() {

		if (is_user_logged_in() && current_user_can('manage_options')) {
			$this->process_dash_notice_ajax();
		} else {
			return;
		}
	}

	/**
	 * This function will handle the ajax calls for the UpdraftPlus clone notice mu-plugin.
	 *
	 * @return void
	 */
	public function process_dash_notice_ajax() {
		$return = array('code' => 'fail', 'data' => '');

		if (!isset($_POST['subaction'])) {
			$return['code'] = 'error';
			$return['data'] = 'Missing subaction';
			echo json_encode($return);
			die();
		}

		if ('refresh_connection' === $_POST['subaction']) {
			check_ajax_referer('updraftplus_refresh_connection', 'nonce');

			$result = $this->refresh_connection();

			if ($result) {
				$return['code'] = 'success';
				$return['data'] = $result;
			} else {
				$return['code'] = 'error';
				$return['data'] = $result;
			}

			echo json_encode($return);
			die();
		} else {
			$return['code'] = 'error';
			$return['data'] = 'Unknown action';
			echo json_encode($return);
			die();
		}
	}

	/**
	 * This function will refresh the stored clones expire date by calling UpdraftPlus.com and getting the latest value.
	 * Note this function needs three defines to work UPDRAFTPLUS_USER_ID and UPDRAFTPLUS_VPS_ID and UPDRAFTPLUS_UNIQUE_TOKEN.
	 *
	 * @return array - that contains the updated expire data or error information
	 */
	public function refresh_connection() {
		global $updraftplus;

		if (!defined('UPDRAFTPLUS_USER_ID') || !is_numeric(UPDRAFTPLUS_USER_ID) || !defined('UPDRAFTPLUS_VPS_ID') || !is_numeric(UPDRAFTPLUS_VPS_ID)) {
			return array('code' => 'error', 'data' => 'No user or VPS ID found');
		}

		if (!defined('UPDRAFTPLUS_UNIQUE_TOKEN')) return array('code' => 'error', 'data' => 'No unique token found');

		$user_id = UPDRAFTPLUS_USER_ID;
		$vps_id = UPDRAFTPLUS_VPS_ID;
		$token = UPDRAFTPLUS_UNIQUE_TOKEN;

		$data = array('user_id' => $user_id, 'vps_id' => $vps_id, 'token' => $token);
		$result = $updraftplus->get_updraftplus_clone()->clone_status($data);

		if (!isset($result['data'])) return array('code' => 'error', 'data' => 'No data returned from clone status call');

		$vps_info = $result['data'];

		if (empty($vps_info['scheduled_removal'])) return array('code' => 'error', 'data' => 'No scheduled removal date found');
		if (empty($vps_info['package_cost'])) return array('code' => 'error', 'data' => 'Missing the expected clone package cost information');
		
		update_site_option('updraftplus_clone_scheduled_removal', $vps_info['scheduled_removal']);
		update_site_option('updraftplus_clone_package_cost', $vps_info['package_cost']);

		$clone_removal_warning = false;

		if (isset($vps_info['tokens']) && 0 == $vps_info['tokens']) {
			if (empty($vps_info['subscription_renewals'])) {
				$clone_removal_warning = true;
			} else {
				$subscription_before_expire = false;
				foreach ($vps_info['subscription_renewals'] as $renewal) {
					if ($renewal < $vps_info['scheduled_removal']) $subscription_before_expire = true;
				}

				if (!$subscription_before_expire) $clone_removal_warning = true;
			}
		}

		update_site_option('updraftplus_clone_removal_warning', $clone_removal_warning);

		$vps_data = array(
			'scheduled_removal' => $vps_info['scheduled_removal'],
			'package_cost' => $vps_info['package_cost']
		);

		return array('code' => 'success', 'data' => $vps_data);
	}
}

if (defined('UPDRAFTPLUS_THIS_IS_CLONE')) {
	new UpdraftPlus_Temporary_Clone_Dash_Notice();
}
