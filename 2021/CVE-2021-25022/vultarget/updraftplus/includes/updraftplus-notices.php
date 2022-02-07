<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('Updraft_Notices')) require_once(UPDRAFTPLUS_DIR.'/includes/updraft-notices.php');

class UpdraftPlus_Notices extends Updraft_Notices {

	protected static $_instance = null;

	private $initialized = false;

	protected $notices_content = array();
	
	protected $self_affiliate_id = 212;

	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function populate_notices_content() {
		
		$parent_notice_content = parent::populate_notices_content();

		$child_notice_content = array(
			1 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('Support', 'updraftplus'),
				'text' => __('Enjoy professional, fast, and friendly help whenever you need it with Premium.', 'updraftplus'),
				'image' => 'notices/support.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'support',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			2 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('UpdraftVault storage', 'updraftplus'),
				'text' => __('The ultimately secure and convenient place to store your backups.', 'updraftplus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://updraftplus.com/landing/vault',
				'campaign' => 'vault',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			3 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('enhanced remote storage options', 'updraftplus'),
				'text' => __('Enhanced storage options for Dropbox, Google Drive and S3. Plus many more options.', 'updraftplus'),
				'image' => 'addons-images/morestorage.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'morestorage',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			4 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('advanced options', 'updraftplus'),
				'text' => __('Secure multisite installation, advanced reporting and much more.', 'updraftplus'),
				'image' => 'addons-images/reporting.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'reporting',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			5 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('secure your backups', 'updraftplus'),
				'text' => __('Add SFTP to send your data securely, lock settings and encrypt your database backups for extra security.', 'updraftplus'),
				'image' => 'addons-images/lockadmin.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'lockadmin',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			6 => array(
				'prefix' => __('UpdraftPlus Premium:', 'updraftplus'),
				'title' => __('easily migrate or clone your site in minutes', 'updraftplus'),
				'text' => __('Copy your site to another domain directly. Includes find-and-replace tool for database references.', 'updraftplus'),
				'image' => 'addons-images/migrator.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'migrator',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->anywhere,
			),
			7 => array(
				'prefix' => '',
				'title' => __('Introducing UpdraftCentral', 'updraftplus'),
				'text' => __('UpdraftCentral is a highly efficient way to manage, update and backup multiple websites from one place.', 'updraftplus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://updraftcentral.com',
				'button_meta' => 'updraftcentral',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			8 => array(
				'prefix' => '',
				'title' => __('Do you use UpdraftPlus on multiple sites?', 'updraftplus'),
				'text' => __('Control all your WordPress installations from one place using UpdraftCentral remote site management!', 'updraftplus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://updraftcentral.com',
				'button_meta' => 'updraftcentral',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->anywhere,
			),
			'rate' => array(
				'text' => __("Hey - We noticed UpdraftPlus has kept your site safe for a while.  If you like us, please consider leaving a positive review to spread the word.  Or if you have any issues or questions please leave us a support message", 'updraftplus') . ' <a href="https://wordpress.org/support/plugin/updraftplus/" target="_blank">' . __('here', 'updraftplus') . '.</a><br>' . __('Thank you so much!', 'updraftplus') . '<br><br> - <b>' . __('Team Updraft', 'updraftplus') . '</b><br>',
				'image' => 'notices/ud_smile.png',
				'button_link' => 'https://wordpress.org/support/plugin/updraftplus/reviews/?rate=5#new-post',
				'button_meta' => 'review',
				'dismiss_time' => 'dismiss_review_notice',
				'supported_positions' => $this->dashboard_top,
				'validity_function' => 'show_rate_notice'
			),
			'translation_needed' => array(
				'prefix' => '',
				'title' => 'Can you translate? Want to improve UpdraftPlus for speakers of your language?',
				'text' => $this->url_start(true, 'updraftplus.com/translate/')."Please go here for instructions - it is easy.".$this->url_end(true, 'updraftplus.com/translate/'),
				'text_plain' => $this->url_start(false, 'updraftplus.com/translate/')."Please go here for instructions - it is easy.".$this->url_end(false, 'updraftplus.com/translate/'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => false,
				'dismiss_time' => false,
				'supported_positions' => $this->anywhere,
				'validity_function' => 'translation_needed',
			),
			'social_media' => array(
				'prefix' => '',
				'title' => __('UpdraftPlus is on social media - check us out!', 'updraftplus'),
				'text' => $this->url_start(true, 'twitter.com/updraftplus', true). __('Twitter', 'updraftplus'). $this->url_end(true, 'twitter.com/updraftplus', true).
						' - '.
						$this->url_start(true, 'facebook.com/updraftplus', true). __('Facebook', 'updraftplus'). $this->url_end(true, 'facebook.com/updraftplus', true),
				'text_plain' => $this->url_start(false, 'twitter.com/updraftplus', true). __('Twitter', 'updraftplus'). $this->url_end(false, 'twitter.com/updraftplus', true).
						' - '.
						$this->url_start(false, 'facebook.com/updraftplus', true). __('Facebook', 'updraftplus'). $this->url_end(false, 'facebook.com/updraftplus', true),
				'image' => 'notices/updraft_logo.png',
				'dismiss_time' => false,
				'supported_positions' => $this->anywhere,
			),
			'newsletter' => array(
				'prefix' => '',
				'title' => __('UpdraftPlus Newsletter', 'updraftplus'),
				'text' => __("Follow this link to sign up for the UpdraftPlus newsletter.", 'updraftplus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://updraftplus.com/newsletter-signup',
				'campaign' => 'newsletter',
				'button_meta' => 'signup',
				'supported_positions' => $this->anywhere,
				'dismiss_time' => false
			),
			'subscribe_blog' => array(
				'prefix' => '',
				'title' => __('UpdraftPlus Blog - get up-to-date news and offers', 'updraftplus'),
				'text' => $this->url_start(true, 'updraftplus.com/news/').__("Blog link", 'updraftplus').$this->url_end(true, 'updraftplus.com/news/').' - '.$this->url_start(true, 'feeds.feedburner.com/UpdraftPlus').__("RSS link", 'updraftplus').$this->url_end(true, 'feeds.feedburner.com/UpdraftPlus'),
				'text_plain' => $this->url_start(false, 'updraftplus.com/news/').__("Blog link", 'updraftplus').$this->url_end(false, 'updraftplus.com/news/').' - '.$this->url_start(false, 'feeds.feedburner.com/UpdraftPlus').__("RSS link", 'updraftplus').$this->url_end(false, 'feeds.feedburner.com/UpdraftPlus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => false,
				'supported_positions' => $this->anywhere,
				'dismiss_time' => false
			),
			'check_out_updraftplus_com' => array(
				'prefix' => '',
				'title' => __('UpdraftPlus Blog - get up-to-date news and offers', 'updraftplus'),
				'text' => $this->url_start(true, 'updraftplus.com/news/').__("Blog link", 'updraftplus').$this->url_end(true, 'updraftplus.com/news/').' - '.$this->url_start(true, 'feeds.feedburner.com/UpdraftPlus').__("RSS link", 'updraftplus').$this->url_end(true, 'feeds.feedburner.com/UpdraftPlus'),
				'text_plain' => $this->url_start(false, 'updraftplus.com/news/').__("Blog link", 'updraftplus').$this->url_end(false, 'updraftplus.com/news/').' - '.$this->url_start(false, 'feeds.feedburner.com/UpdraftPlus').__("RSS link", 'updraftplus').$this->url_end(false, 'feeds.feedburner.com/UpdraftPlus'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => false,
				'supported_positions' => $this->dashboard_bottom_or_report,
				'dismiss_time' => false
			),
			'autobackup' => array(
				'prefix' => '',
				'title' => __('Make updates easy with UpdraftPlus', 'updraftplus'),
				'text' => __('Be safe', 'updraftplus') . ' - ' . $this->url_start(true, 'updraftplus.com/shop/updraftplus-premium/') . 'UpdraftPlus Premium' . $this->url_end(true, 'updraftplus.com/shop/updraftplus-premium/') . ' ' . __('backs up automatically when you update plugins, themes or core', 'updraftplus'),
				'text2' => __('Save time', 'updraftplus') . ' - ' . $this->url_start(true, 'wordpress.org/plugins/stops-core-theme-and-plugin-updates/') . 'Easy Updates Manager' . $this->url_end(true, 'wordpress.org/plugins/stops-core-theme-and-plugin-updates/') . ' ' . __('handles updates automatically as you want them', 'updraftplus'),
				'text3' => __('Many sites?', 'updraftplus') . ' - ' . $this->url_start(true, 'updraftplus.com/updraftcentral/') . 'UpdraftCentral' . $this->url_end(true, 'updraftplus.com/updraftcentral/') . ' ' . __('manages all your WordPress sites at once from one place', 'updraftplus'),
				'image' => 'addons-images/autobackup.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'autobackup',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismissautobackup',
				'supported_positions' => $this->autobackup_bottom_or_report,
			),
			'subscriben' => array(
				'prefix' => '',
				'title' => 'Subscriben ' .__('by', 'updraftplus'). ' UpdraftPlus',
				'text' => __("The WordPress subscription extension for WooCommerce store owners.", "updraftplus"),
				'image' => 'notices/subscriben.png',
				'button_link' => 'https://subscribenplugin.com',
				'button_meta' => 'read_more',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->anywhere,
			),
			'wp-optimize' => array(
				'prefix' => '',
				'title' => 'WP-Optimize',
				'text' => __("After you've backed up your database, we recommend you install our WP-Optimize plugin to streamline it for better website performance.", "updraftplus"),
				'image' => 'notices/wp_optimize_logo.png',
				'button_link' => 'https://wordpress.org/plugins/wp-optimize/',
				'button_meta' => 'wp-optimize',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->anywhere,
				'validity_function' => 'wp_optimize_installed',
			),
			
			// The sale adverts content starts here
			'blackfriday' => array(
				'prefix' => '',
				'title' => __('Black Friday - 20% off UpdraftPlus Premium until November 30th', 'updraftplus'),
				'text' => __('To benefit, use this discount code:', 'updraftplus').' ',
				'image' => 'notices/black_friday.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'blackfriday',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_season',
				'discount_code' => 'blackfridaysale2021',
				'valid_from' => '2021-11-20 00:00:00',
				'valid_to' => '2021-11-30 23:59:59',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			'newyear' => array(
				'prefix' => '',
				'title' => __('Happy New Year - 20% off UpdraftPlus Premium until January 14th', 'updraftplus'),
				'text' => __('To benefit, use this discount code:', 'updraftplus').' ',
				'image' => 'notices/new_year.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'newyear',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_season',
				'discount_code' => 'newyearsale2022',
				'valid_from' => '2021-12-26 00:00:00',
				'valid_to' => '2022-01-14 23:59:59',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			'spring' => array(
				'prefix' => '',
				'title' => __('Spring sale - 20% off UpdraftPlus Premium until May 31st', 'updraftplus'),
				'text' => __('To benefit, use this discount code:', 'updraftplus').' ',
				'image' => 'notices/spring.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'spring',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_season',
				'discount_code' => 'springsale2021',
				'valid_from' => '2021-05-01 00:00:00',
				'valid_to' => '2021-05-31 23:59:59',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			'summer' => array(
				'prefix' => '',
				'title' => __('Summer sale - 20% off UpdraftPlus Premium until July 31st', 'updraftplus'),
				'text' => __('To benefit, use this discount code:', 'updraftplus').' ',
				'image' => 'notices/summer.png',
				'button_link' => 'https://updraftplus.com/landing/updraftplus-premium',
				'campaign' => 'summer',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_season',
				'discount_code' => 'summersale2021',
				'valid_from' => '2021-07-01 00:00:00',
				'valid_to' => '2021-07-31 23:59:59',
				'supported_positions' => $this->dashboard_top_or_report,
			),
			'collection' => array(
				'prefix' => '',
				'title' => __('The Updraft Plugin Collection Sale', 'updraftplus'),
				'text' => __('Get 20% off any of our plugins. But hurry - offer ends 30th September, use this discount code:', 'updraftplus').' ',
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://teamupdraft.com',
				'campaign' => 'collection',
				'button_meta' => 'collection',
				'dismiss_time' => 'dismiss_season',
				'discount_code' => 'UDP2021',
				'valid_from' => '2021-09-01 00:00:00',
				'valid_to' => '2021-09-30 23:59:59',
				'supported_positions' => $this->dashboard_top_or_report,
			)
		);

		return array_merge($parent_notice_content, $child_notice_content);
	}
	
	/**
	 * Call this method to setup the notices
	 */
	public function notices_init() {
		if ($this->initialized) return;
		$this->initialized = true;
		// parent::notices_init();
		$this->notices_content = (defined('UPDRAFTPLUS_NOADS_B') && UPDRAFTPLUS_NOADS_B) ? array() : $this->populate_notices_content();
		global $updraftplus;
		$enqueue_version = $updraftplus->use_unminified_scripts() ? $updraftplus->version.'.'.time() : $updraftplus->version;
		$updraft_min_or_not = $updraftplus->get_updraftplus_file_version();

		wp_enqueue_style('updraftplus-notices-css',  UPDRAFTPLUS_URL.'/css/updraftplus-notices'.$updraft_min_or_not.'.css', array(), $enqueue_version);
	}

	protected function translation_needed($plugin_base_dir = null, $product_name = null) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Filter use
		return parent::translation_needed(UPDRAFTPLUS_DIR, 'updraftplus');
	}

	/**
	 * This function will check if we should display the rate notice or not
	 *
	 * @return boolean - to indicate if we should show the notice or not
	 */
	protected function show_rate_notice() {
		global $updraftplus;

		$backup_history = UpdraftPlus_Backup_History::get_history();
		
		$backup_dir = $updraftplus->backups_dir_location();
		// N.B. Not an exact proxy for the installed time; they may have tweaked the expert option to move the directory
		$installed = @filemtime($backup_dir.'/index.html');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$installed_for = time() - $installed;

		if (!empty($backup_history) && $installed && $installed_for > 28*86400) {
			return true;
		}

		return false;
	}
	
	protected function wp_optimize_installed($plugin_base_dir = null, $product_name = null) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$plugins = get_plugins();

		foreach ($plugins as $value) {
			if ('wp-optimize' == $value['TextDomain']) {
				return false;
			}
		}
		return true;
	}
	
	protected function url_start($html_allowed, $url, $https = false, $website_home = 'updraftplus.com') {
		return parent::url_start($html_allowed, $url, $https, $website_home);
	}

	protected function skip_seasonal_notices($notice_data) {
		global $updraftplus;

		$time_now = defined('UPDRAFTPLUS_NOTICES_FORCE_TIME') ? UPDRAFTPLUS_NOTICES_FORCE_TIME : time();
		// Do not show seasonal notices to people with an updraftplus.com version and no-addons yet
		if (!file_exists(UPDRAFTPLUS_DIR.'/udaddons') || $updraftplus->have_addons) {
			$valid_from = strtotime($notice_data['valid_from']);
			$valid_to = strtotime($notice_data['valid_to']);
			$dismiss = $this->check_notice_dismissed($notice_data['dismiss_time']);
			if (($time_now >= $valid_from && $time_now <= $valid_to) && !$dismiss) {
				// return true so that we return this notice to be displayed
				return true;
			}
		}
		
		return false;
	}
	
	protected function check_notice_dismissed($dismiss_time) {

		$time_now = defined('UPDRAFTPLUS_NOTICES_FORCE_TIME') ? UPDRAFTPLUS_NOTICES_FORCE_TIME : time();
	
		$notice_dismiss = ($time_now < UpdraftPlus_Options::get_updraft_option('dismissed_general_notices_until', 0));
		$review_dismiss = ($time_now < UpdraftPlus_Options::get_updraft_option('dismissed_review_notice', 0));
		$seasonal_dismiss = ($time_now < UpdraftPlus_Options::get_updraft_option('dismissed_season_notices_until', 0));
		$autobackup_dismiss = ($time_now < UpdraftPlus_Options::get_updraft_option('updraftplus_dismissedautobackup', 0));

		$dismiss = false;

		if ('dismiss_notice' == $dismiss_time) $dismiss = $notice_dismiss;
		if ('dismiss_review_notice' == $dismiss_time) $dismiss = $review_dismiss;
		if ('dismiss_season' == $dismiss_time) $dismiss = $seasonal_dismiss;
		if ('dismissautobackup' == $dismiss_time) $dismiss = $autobackup_dismiss;

		return $dismiss;
	}

	protected function render_specified_notice($advert_information, $return_instead_of_echo = false, $position = 'top') {
	
		if ('bottom' == $position) {
			$template_file = 'bottom-notice.php';
		} elseif ('report' == $position) {
			$template_file = 'report.php';
		} elseif ('report-plain' == $position) {
			$template_file = 'report-plain.php';
		} elseif ('autobackup' == $position) {
			$template_file = 'autobackup-notice.php';
		} else {
			$template_file = 'horizontal-notice.php';
		}
		
		/*
			Check to see if the updraftplus_com_link filter is being used, if it's not then add our tracking to the link.
		*/
	
		if (!has_filter('updraftplus_com_link') && isset($advert_information['button_link']) && false !== strpos($advert_information['button_link'], '//updraftplus.com')) {
			$advert_information['button_link'] = trailingslashit($advert_information['button_link']).'?afref='.$this->self_affiliate_id;
			if (isset($advert_information['campaign'])) $advert_information['button_link'] .= '&utm_source=updraftplus&utm_medium=banner&utm_campaign='.$advert_information['campaign'];
		}

		include_once(UPDRAFTPLUS_DIR.'/admin.php');
		global $updraftplus_admin;
		return $updraftplus_admin->include_template('wp-admin/notices/'.$template_file, $return_instead_of_echo, $advert_information);
	}
}

$GLOBALS['updraftplus_notices'] = UpdraftPlus_Notices::instance();
