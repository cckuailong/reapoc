<?php
if (!defined('ABSPATH')) die('No direct access allowed');
/**
 * Class UpdraftPlus_Tour
 *
 * Adds the guided tour when activating the plugin for the first time.
 */
class UpdraftPlus_Tour {

	/**
	 * The class instance
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Get the instance
	 *
	 * @return object
	 */
	public static function get_instance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * __construct
	 */
	private function __construct() {
	}
	
	/**
	 * Sets up the notices, security and loads assets for the admin page
	 */
	public function init() {
		// Add plugin action link
		add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

		// only init and load assets if the tour hasn't been canceled
		if (isset($_REQUEST['updraftplus_tour']) && 0 === (int) $_REQUEST['updraftplus_tour']) {
			$this->set_tour_status(array('current_step' => 'start'));
			return;
		}
		
		// if backups already exist and
		if ($this->updraftplus_was_already_installed() && !isset($_REQUEST['updraftplus_tour'])) {
			return;
		}

		// if 'Take tour' link was used, reset tour
		if (isset($_REQUEST['updraftplus_tour']) && 1 === (int) $_REQUEST['updraftplus_tour']) {
			$this->reset_tour_status();
		}

		if (!UpdraftPlus_Options::get_updraft_option('updraftplus_tour_cancelled_on')) {
			add_action('admin_enqueue_scripts', array($this, 'load_tour'));
		}
	}

	/**
	 * Loads in tour assets
	 *
	 * @param string $hook - current page
	 */
	public function load_tour($hook) {
		
		$pages = array('settings_page_updraftplus', 'plugins.php');

		if (!in_array($hook, $pages)) return;
		if (!UpdraftPlus_Options::user_can_manage()) return;

		global $updraftplus, $updraftplus_addons2, $updraftplus_checkout_embed;

		$checkout_embed_5gb_attribute = '';
		if ($updraftplus_checkout_embed) {
			$checkout_embed_5gb_attribute = $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-5-gb') ? 'data-embed-checkout="'.apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftplus-vault-storage-5-gb', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=settings')).'"' : '';
		}

		$script_suffix = $updraftplus->use_unminified_scripts() ? '' : '.min';
		$updraft_min_or_not = $updraftplus->get_updraftplus_file_version();
		wp_enqueue_script('updraftplus-tether-js', trailingslashit(UPDRAFTPLUS_URL).'includes/tether/tether'.$script_suffix.'.js', $updraftplus->version, true);
		wp_enqueue_script('updraftplus-shepherd-js', trailingslashit(UPDRAFTPLUS_URL).'includes/tether-shepherd/shepherd'.$script_suffix.'.js', array('updraftplus-tether-js'), $updraftplus->version, true);
		wp_enqueue_style('updraftplus-shepherd-css', trailingslashit(UPDRAFTPLUS_URL).'css/tether-shepherd/shepherd-theme-arrows-plain-buttons'.$script_suffix.'.css', false, $updraftplus->version);
		wp_enqueue_style('updraftplus-tour-css', trailingslashit(UPDRAFTPLUS_URL).'css/updraftplus-tour'.$updraft_min_or_not.'.css', false, $updraftplus->version);
		wp_register_script('updraftplus-tour-js', trailingslashit(UPDRAFTPLUS_URL).'js/tour.js', array('updraftplus-tether-js'), $updraftplus->version, true);
		
		$tour_data = array(
			'nonce' => wp_create_nonce('updraftplus-credentialtest-nonce'),
			'show_tab_on_load' => '#updraft-navtab-status',
			'next' => __('Next', 'updraftplus'),
			'back' => __('Back', 'updraftplus'),
			'skip' => __('Skip this step', 'updraftplus'),
			'end_tour' => __('End tour', 'updraftplus'),
			'close' => __('Close', 'updraftplus'),
			'plugins_page' => array(
				'title' => __("UpdraftPlus settings", 'updraftplus'),
				'text' => '<div class="updraftplus-welcome-logo"><img src="'.trailingslashit(UPDRAFTPLUS_URL).'images/ud-logo.png" alt="" /></div><strong>'.__('Welcome to UpdraftPlus', 'updraftplus').'</strong>, '.__("the world’s most trusted backup plugin!", 'updraftplus'),
				'button' => array(
					'url' => UpdraftPlus_Options::admin_page_url().'?page=updraftplus',
					'text' => __('Press here to start!', 'updraftplus')
				)
			),
			'backup_now' => array(
				'title' => __('Your first backup', 'updraftplus'),
				'text' => sprintf(_x('To make a simple backup to your server, press this button. Or to setup regular backups and remote storage, go to %s settings %s', 'updraftplus'), '<strong><a href="#settings" class="js--go-to-settings">', '</a></strong>')
			),
			'backup_options' => array(
				'title' => __("Manual backup options", 'updraftplus'),
				'text' => __('Select what you want to backup', 'updraftplus')
			),
			'backup_now_btn' => array(
				'title' => __("Creating your first backup", 'updraftplus'),
				'text' => __("Press here to run a manual backup.", 'updraftplus').'<br>'.sprintf(_x("But to avoid server-wide threats backup regularly to remote cloud storage in %s settings %s", 'Translators: %s is a bold tag.', 'updraftplus'), '<strong><a href="#settings" class="js--go-to-settings">', '</a></strong>'),
				'btn_text' => __('Go to settings', 'updraftplus')
			),
			'backup_now_btn_success' => array(
				'title' => __('Creating your first backup', 'updraftplus'),
				'text' => __('Congratulations! Your first backup is running.', 'updraftplus').'<br>'.sprintf(_x('But to avoid server-wide threats backup regularly to remote cloud storage in %s settings %s', 'Translators: %s is a bold tag.', 'updraftplus'), '<strong>', '</strong>'),
				'btn_text' => __('Go to settings', 'updraftplus')
			),
			'settings_timing' => array(
				'title' => __("Choose your backup schedule", 'updraftplus'),
				'text' => __("Choose the schedule that you want your backups to run on.", 'updraftplus')
			),
			'settings_remote_storage' => array(
				'title' => __("Remote storage", 'updraftplus'),
				'text' => __("Now select a remote storage destination to protect against server-wide threats. If not, your backups remain on the same server as your site.", 'updraftplus')
					.'<div class="ud-notice">'
					.'<h3>'.__('Try UpdraftVault!').'</h3>'
					.__("UpdraftVault is our remote storage which works seamlessly with UpdraftPlus.", 'updraftplus')
					.' <a href="'.apply_filters('updraftplus_com_link', 'https://updraftplus.com/updraftvault/').'" target="_blank">'.__('Find out more here.', 'updraftplus').'</a>'
					.'<p><a href="'.apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_vault_5')).'" target="_blank" '.$checkout_embed_5gb_attribute.' class="button button-primary">'.__('Try UpdraftVault for 1 month for only $1!', 'updraftplus').'</a></p>'
					.'</div>'
			),
			'settings_more' => array(
				'title' => __("More settings", 'updraftplus'),
				'text' => __("Look through the other settings here, making any changes you’d like.", 'updraftplus')
			),
			'settings_save' => array(
				'title' => __("Save", 'updraftplus'),
				'text' => __('Press here to save your settings.', 'updraftplus')
			),
			'settings_saved' => array(
				'title' => __("Save", 'updraftplus'),
				'text' => __('Congratulations, your settings have successfully been saved.', 'updraftplus')
			),
			'updraft_central' => array(
				'title' => __("UpdraftCentral", 'updraftplus'),
				'text' => '<div class="ud-notice">'
					.'<h3>'.__('Control all your backups in one place', 'updraftplus').'</h3>'
					.__('Do you have a few more WordPress sites you want to backup? If yes you can save hours by controlling all your backups in one place from UpdraftCentral.', 'updraftplus')
					.'</div>'
			),
			'premium' => array(
				'title' => 'UpdraftPlus Premium',
				'text' => __('Thank you for taking the tour.', 'updraftplus')
					.'<div class="ud-notice">'
					.'<h3>'.__('UpdraftPlus Premium and addons', 'updraftplus').'</h3>'
					.__('UpdraftPlus Premium has many more exciting features!', 'updraftplus').' <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('Find out more here.', 'updraftplus').'</a>'
					.'</div>',
				'attach_to' => '#updraft-navtab-addons top',
				'button' => __('Finish', 'updraftplus')
			),
			'vault_selected' => array(
				'title' => 'UpdraftVault',
				'text' => _x('To get started with UpdraftVault, select one of the options below:', 'Translators: UpdraftVault is a product name and should not be translated.', 'updraftplus')
			)
		);

		if (isset($_REQUEST['tab'])) {
			$tour_data['show_tab_on_load'] = '#updraft-navtab-'.esc_attr($_REQUEST['tab']);
		}

		// Change the data for premium users
		if ($updraftplus_addons2 && method_exists($updraftplus_addons2, 'connection_status')) {

			$tour_data['settings_remote_storage'] = array(
				'title' => __("Remote storage", 'updraftplus'),
				'text' => __("Now select a remote storage destination to protect against server-wide threats. If not, your backups remain on the same server as your site.", 'updraftplus')
					.'<div class="ud-notice">'
					.'<h3>'.__('Try UpdraftVault!').'</h3>'
					.__("UpdraftVault is our remote storage which works seamlessly with UpdraftPlus.", 'updraftplus')
					.' <a href="'.apply_filters('updraftplus_com_link', 'https://updraftplus.com/updraftvault/').'" target="_blank">'.__('Find out more here.', 'updraftplus').'</a>'
					.'<br>'
					.__("If you have a valid Premium license, you get 1GB of storage included.", 'updraftplus')
					.' <a href="'.apply_filters('updraftplus_com_link', 'https://updraftplus.com/shop/updraftplus-vault-storage-5-gb/').'" target="_blank" '.$checkout_embed_5gb_attribute.'>'.__('Otherwise, you can try UpdraftVault for 1 month for only $1!', 'updraftplus').'</a>'
					.'</div>'
			);

			if ($updraftplus_addons2->connection_status() && !is_wp_error($updraftplus_addons2->connection_status())) {
				$tour_data['premium'] = array(
					'title' => 'UpdraftPlus Premium',
					'text' => __('Thank you for taking the tour. You are now all set to use UpdraftPlus!', 'updraftplus'),
					'attach_to' => '#updraft-navtab-addons top',
					'button' => __('Finish', 'updraftplus')
				);
			} else {
				$tour_data['premium'] = array(
					'title' => 'UpdraftPlus Premium',
					'text' => __('Thank you for taking the tour.', 'updraftplus')
						.'<div class="ud-notice">'
						.'<h3>'.__('Connect to updraftplus.com', 'updraftplus').'</h3>'
						.__('Log in here to enable all the features you have access to.', 'updraftplus')
						.'</div>',
					'attach_to' => '#updraftplus-addons_options_email right',
					'button' => __('Finish', 'updraftplus')
				);
			}
		}

		wp_localize_script('updraftplus-tour-js', 'updraftplus_tour_i18n', $tour_data);
		wp_enqueue_script('updraftplus-tour-js');
	}

	/**
	 * Removes the tour status so the tour can be seen again
	 *
	 * @return string|WP_Error not visible by the user
	 */
	public function reset_tour_status() {

		// If the option isn't set, the tour hasn't been cancelled
		if (!UpdraftPlus_Options::get_updraft_option('updraftplus_tour_cancelled_on')) {
			// string not visible by the user
			return 'The tour is still active. Everything should be ok.';
		}

		$result = UpdraftPlus_Options::delete_updraft_option('updraftplus_tour_cancelled_on');
		// strings not visible by the user
		return $result ? 'The tour status was successfully reset' : new WP_Error('update_failed', 'The attempt to update the tour option failed.', array('status' => 409));
	}

	/**
	 * Updates the stored value for which step the tour ended on
	 *
	 * @param object $request - the http $_REQUEST obj
	 * @return bool
	 */
	public function set_tour_status($request) {
		if (!isset($request['current_step'])) return false;
		return UpdraftPlus_Options::update_updraft_option('updraftplus_tour_cancelled_on', $request['current_step']);
	}

	/**
	 * Adds the Tour link under the plugin on the plugin screen.
	 *
	 * @param  Array  $links Set of links for the plugin, before being filtered
	 * @param  String $file  File name (relative to the plugin directory)
	 * @return Array filtered results
	 */
	public function plugin_action_links($links, $file) {
		if (is_array($links) && 'updraftplus/updraftplus.php' === $file) {
			$links['updraftplus_tour'] = '<a href="'.UpdraftPlus_Options::admin_page_url().'?page=updraftplus&updraftplus_tour=1" class="js-updraftplus-tour">'.__("Take Tour", "updraftplus").'</a>';
		}
		return $links;
	}

	/**
	 * Checks if UDP was newly installed.
	 *
	 * Checks if there are backups, and if there are more than 1,
	 * checks if the folder is older than 1 day old
	 *
	 * @return bool
	 */
	public function updraftplus_was_already_installed() {
		// If backups already exist
		$backup_history = UpdraftPlus_Backup_History::get_history();

		// No backup history
		if (!$backup_history) return false;
		if (is_array($backup_history) && 0 === count($backup_history)) {
			return false;
		}
		// If there is at least 1 backup, we check if the folder is older than 1 day old
		if (0 < count($backup_history)) {
			$backups_timestamps = array_keys($backup_history);
			$last_backlup_age = time() - end($backups_timestamps);
			if (DAY_IN_SECONDS < $last_backlup_age) {
				// the oldest backup is older than 1 day old, so it's likely that UDP was already installed, and the backups aren't a product of the user testing while doing the tour.
				return true;
			}
		}
		return false;
	}
}

add_action('admin_init', array(UpdraftPlus_Tour::get_instance(), 'init'));
