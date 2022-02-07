<?php
/*
Plugin Name: Domain Check
Plugin URI: http://domaincheckplugin.com
Description: Domain Check lets you search domain names in your admin using your Wordpress blog, set domain expiration reminders for yourself or multiple email addresses, check SSL certificates, and set SSL expiration reminders. Get email reminders when domains expire or SSL certificates expire and set multiple emails for domain expiration reminders. Watch domain names on your own domain watch list and do your own domain lookups! Get the latest daily coupon codes from all the major domain registrars, SSL certificate providers, and hosting companies right in your Wordpress admin!
Version: 1.0.16
Author: Domain Check
Author URI: http://domaincheckplugin.com

Domain Check
Copyright (c) 2015 Domain Check <info@domaincheckplugin.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

//might be installing
if (defined('WP_INSTALLING') && WP_INSTALLING) {
	return;
}

//define wp-plugin class, must be self contained!
if(!class_exists('DomainCheck')) {

	if (is_file(dirname(__FILE__) . '/domain-check-debug.php')) {
		require_once(dirname(__FILE__) . '/domain-check-debug.php');
	}

	class DomainCheck {

		const CAPABILITY = 'edit_domain_check';

		const PHP_REQUIRED_VERSION = '5.3.0';

		//plugin
		const PLUGIN_CLASSNAME = 'DomainCheck';
		const PLUGIN_NAME = 'domain-check';
		const PLUGIN_OPTION_PREFIX = 'domain_check';
		const PLUGIN_VERSION = '1.0.16';

		public static $db_table; //db table (has to be dynamic for wp prefix)

		public static $db_prefix = 'domain_check';

		public static $basename = null;

		//public static $add_script = false;

		private $version_field_name = 'domain_check_version';

		private static $is_pro = 0;

		//constructor for wp-plugin object
		public function __construct() {
			global $wpdb;

			//setup db table names w/ wp prefix
			if ($wpdb && isset($wpdb->prefix)) {
				self::$db_prefix = $wpdb->prefix . self::$db_prefix;
			}
			self::$db_table = self::$db_prefix;

			//include libs...
			$pluginDir = dirname(__FILE__) . '/';
			require_once($pluginDir . self::PLUGIN_NAME . '-config.php'); //config / settings / options
			require_once($pluginDir . self::PLUGIN_NAME . '-util.php'); //util functions
			require_once($pluginDir . 'lib/domain-check-whois.php'); //util functions
			require_once($pluginDir . 'lib/domain-check-coupon-data.php'); //coupon functions
			require_once($pluginDir . 'lib/domain-check-links.php'); //link functions
			require_once($pluginDir . 'lib/domain-check-search.php'); //search functions
			require_once($pluginDir . 'lib/domain-check-help.php'); //help functions
			require_once($pluginDir . 'lib/domain-check-email.php'); //email functions
			require_once($pluginDir . 'lib/domain-check-cron.php'); //cron functions

			self::$basename = plugin_basename(__FILE__);

			//pro plugin is active
			//in instantiated class but referencing by static
			if ( class_exists( 'DomainCheckPro' ) ) {
				self::$is_pro = 1;
			}

			//-----------------------------------
			//WP
			//-----------------------------------
			//activate
			if (function_exists('register_activation_hook')) {
				register_activation_hook(__FILE__, array(&$this, 'activate_plugin')); //activate
			}
			if (function_exists('register_deactivation_hook')) {
				register_deactivation_hook(__FILE__, array(&$this, 'deactivate_plugin')); //deactivate, (this will delete db tables, wp-plugin options, etc.)
			}

			//actions
			if (function_exists('add_action')) {
				add_action('init', array(&$this, 'init'));
				add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
				add_action('domain_check_cron_email', array(&$this, 'domain_check_cron_email'));
				add_action('domain_check_cron_coupons', array(&$this, 'domain_check_cron_coupons'));
				add_action('domain_check_cron_watch', array(&$this, 'domain_check_cron_watch'));
				//add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
				//add_action('wp_footer', array(&$this, 'enqueue_scripts'));
			}

			//filters
			//add_filter('post_class', array( &$this, 'add_fancy_product_class'));
			add_filter( 'cron_schedules', array( 'DomainCheckCron', 'add_intervals' ) );

			//language
			//load_plugin_textdomain('mystyle-wp-plugin', false, dirname(plugin_basename( __FILE__)) . '/language/');
			//load_plugin_textdomain('radykal', false, dirname(plugin_basename( __FILE__)) . '/languages/');

			//-----------------------------------
			//WP ADMIN
			//-----------------------------------
			//include admin...
			if ( ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) && is_admin() ) {
				$pluginAdminDir = dirname(__FILE__) . '/admin/';
				require_once($pluginAdminDir . self::PLUGIN_NAME . '-admin.php');

				if (class_exists('DomainCheckDebug') && isset($_GET['test_cron'])) {
					DomainCheckDebug::debug();
				}

				if (class_exists('DomainCheckDebug') && isset($_GET['test_update'])) {
					DomainCheckDebug::debug();
				}

			}
			if (class_exists('DomainCheckDebug') && isset($_GET['test_option'])) {
				DomainCheckDebug::debug();
			}
			if (class_exists('DomainCheckDebug') && isset($_GET['test_show_option'])) {
				DomainCheckDebug::debug();
			}
		}

		public function add_action_links( $links ) {
			$faq_link = '<a title="Help" href="' . esc_url( admin_url( 'admin.php?page=domain-check-help' ) ) . '">Help</a>';
			array_unshift( $links, $faq_link );

			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=domain-check-settings' ) ) . '">Settings</a>';
			array_unshift( $links, $settings_link );

			$dashboard_link = '<a href="' . esc_url( admin_url( 'admin.php?page=domain-check' ) ) . '">Dashboard</a>';
			array_unshift( $links, $dashboard_link );

			return $links;
		}

		//init to get things moving
		public function init() {

		}

		public function domain_check_cron_coupons() {
			DomainCheckCouponData::update();
		}

		//here there be monsters...
		public function domain_check_cron_email() {
			global $wpdb;

			//error_log('domain check in email cron...');

			$send_email = false;

			$subject = 'Domain Check';

			$not_owned_domains = array();

			$skip_admin_email_domains = array();
			$skip_admin_email_ssl = array();

			$emails = array();
			$admin_email = false;
			$blog_name = false;
			$site_url = false;
			$email_additional_emails = null;
			if (function_exists('get_option')) {
				$admin_email = get_option('admin_email');
				if (get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_primary_email')) {
					$admin_email = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_primary_email');
				}
				if ($admin_email) {
					$emails[strtolower($admin_email)] = array(
						'owned' => array(),
						'taken' => array(),
						'ssl'	=> array()
					);
				}

				$email_additional_emails = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_additional_emails');
				if (is_array($email_additional_emails) && count($email_additional_emails)) {
					foreach ($email_additional_emails as $email_additional_email) {
						$emails[strtolower($email_additional_email)] = array(
							'owned' => array(),
							'taken' => array(),
							'ssl' => array()
						);
					}
				}

				$blog_name = get_option('blogname');
				$site_url = site_url();
			}

			//get general emails from settings
			//get email settings from settings



			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_expires < ' . (time() + (60*60*24*100)) . ' AND domain_expires > 0 AND domain_watch > 0 ORDER BY domain_expires ASC';
			$domain_result = $wpdb->get_results( $sql, 'ARRAY_A' );
			$expiring_domain_count = count($domain_result);

			foreach ($domain_result as $domain_result_idx => $domain_result_data) {
				if ($domain_result[$domain_result_idx]['domain_settings']) {
					$domain_result[$domain_result_idx]['domain_settings'] = json_decode(gzuncompress($domain_result[$domain_result_idx]['domain_settings']), true);
				}
				if (isset($domain_result[$domain_result_idx]['domain_settings']['watch_emails']) &&
					is_array($domain_result[$domain_result_idx]['domain_settings']['watch_emails']) &&
					count($domain_result[$domain_result_idx]['domain_settings']['watch_emails'])
				) {
					foreach ($domain_result[$domain_result_idx]['domain_settings']['watch_emails'] as $watch_email_idx => $watch_email) {
						$watch_email = trim($watch_email);
						if (!isset($emails[$watch_email])) {
							$emails[$watch_email] = array(
								'owned' => array(),
								'taken' => array(),
								'ssl' => array()
							);
						}
						if ($domain_result_data['status'] == 2) {
							$emails[$watch_email]['owned'][] = $domain_result_idx;
						} else if ($domain_result_data['status'] == 1) {
							$emails[$watch_email]['taken'][] = $domain_result_idx;
						}
					}
				}
				if (isset($domain_result[$domain_result_idx]['domain_settings']['skip_admin_email']) &&
					$domain_result[$domain_result_idx]['domain_settings']['skip_admin_email']
				) {
					$skip_admin_email_domains[] = $domain_result_idx;
				} else {
					if ($admin_email) {
						if ($domain_result_data['status'] == 2) {
							$emails[$admin_email]['owned'][] = $domain_result_idx;
						} else if ($domain_result_data['status'] == 1) {
							$emails[$admin_email]['taken'][] = $domain_result_idx;
						}
					}
					if (is_array($email_additional_emails) && count($email_additional_emails)) {
						foreach ($email_additional_emails as $email_additional_email) {
							if ($domain_result_data['status'] == 2) {
								$emails[$email_additional_email]['owned'][] = $domain_result_idx;
							} else if ($domain_result_data['status'] == 1) {
								$emails[$email_additional_email]['taken'][] = $domain_result_idx;
							}
						}
					}
				}
			}

			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_expires < ' . (time() + (60*60*24*100)) . ' AND domain_expires > 0 AND domain_watch > 0 ORDER BY domain_expires ASC';
			$ssl_result = $wpdb->get_results( $sql, 'ARRAY_A' );
			$expiring_ssl_count = count($ssl_result);
			foreach ($ssl_result as $domain_result_idx => $domain_result_data) {
				if (isset($ssl_result[$domain_result_idx]['domain_settings']) && $ssl_result[$domain_result_idx]['domain_settings']) {
					$ssl_result[$domain_result_idx]['domain_settings'] = json_decode(gzuncompress($ssl_result[$domain_result_idx]['domain_settings']), true);
				} else {
					$ssl_result[$domain_result_idx]['domain_settings'] = array();
				}
				if (isset($ssl_result[$domain_result_idx]['domain_settings']['watch_emails']) &&
					is_array($ssl_result[$domain_result_idx]['domain_settings']['watch_emails']) &&
					count($ssl_result[$domain_result_idx]['domain_settings']['watch_emails'])
				) {
					foreach ($ssl_result[$domain_result_idx]['domain_settings']['watch_emails'] as $watch_email_idx => $watch_email) {
						$watch_email = trim($watch_email);
						if (!isset($emails[$watch_email])) {
							$emails[$watch_email] = array(
								'owned' => array(),
								'taken' => array(),
								'ssl' => array()
							);
						}
						$emails[$watch_email]['ssl'][] = $domain_result_idx;
					}
				}
				if (isset($ssl_result[$domain_result_idx]['domain_settings']['skip_admin_email']) &&
					$ssl_result[$domain_result_idx]['domain_settings']['skip_admin_email']
				) {
					$skip_admin_email_ssl[] = $domain_result_idx;
				} else {
					if ($admin_email) {
						$emails[$admin_email]['ssl'][] = $domain_result_idx;
					}
					if (is_array($email_additional_emails) && count($email_additional_emails)) {
						foreach ($email_additional_emails as $email_additional_email) {
							$emails[$email_additional_email]['ssl'][] = $domain_result_idx;
						}
					}
				}
			}

			foreach ($emails as $email_address => $email_domains) {
				$is_admin = true;
				if ($email_address == $admin_email) {
					$is_admin = true;
				}
				$subject = 'Domain Check';
				$message = '<html>' . "\n";
				$message .= '<h1>Domain Check - ' . $blog_name . '<h1>' . "\n";
				$send_email = false;
				if ( count($email_domains['owned']) ) {
					//$send_email = true;
					$subject .= ' - ' . count($email_domains['owned']) . ' Expiring Domains';
					$message .= '<h2><img src="' . plugins_url('domain-check/images/icons/flag-blue-24x24.png') . '">Your Expiring Domains</h2>' . "\n";
					$message .= '<table>';
					foreach ($email_domains['owned'] as $result_idx => $domain_result_idx) {
						if (isset($domain_result[$domain_result_idx]['domain_expires']) && $domain_result[$domain_result_idx]['domain_expires']) {
							$domain_expires = $domain_result[$domain_result_idx]['domain_expires'];
							if ($domain_expires > (time() + (86400 * 27)) && $domain_expires < (time() + (86400 * 28))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 13)) && $domain_expires < (time() + (86400 * 14))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 7)) && $domain_expires < (time() + (86400 * 8))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 3)) && $domain_expires < (time() + (86400 * 4))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 2)) && $domain_expires < (time() + (86400 * 3))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 1)) && $domain_expires < (time() + (86400 * 2))) {
								$send_email = true;
							}
						}
						$cta = '';
						if (isset($domain_result[$domain_result_idx]['domain_expires']) && $domain_result[$domain_result_idx]['domain_expires']) {
							$days_flat = number_format(($domain_result[$domain_result_idx]['domain_expires'] - time())/60/60/24, 0);
							$expires =  $days_flat . ' Days';
							if ($expires < 0) {
								$expires = 'Expired';
							}
							$expires = '' . $expires;
							if ($domain_result[$domain_result_idx]['domain_expires'] - time() < (86400 * 60)) {
								$cta = '<a href="' . DomainCheckLinks::homepage($domain_result[$domain_result_idx]['domain_url']) . '" target="_blank">Renew [&raquo;]</a>';
							}
						} else {
							$expires = 'n/a';
						}
						$message .= '<tr>';
						$message .= '<td><strong>' . $domain_result[$domain_result_idx]['domain_url'] . '</strong></td>' . "\n";
						$message .= '<td>' . $expires . '</td>' . "\n";
						$message .= '<td>' . $cta . '</td>' . "\n";
						$message .= '</tr>';
					}
					$message .= '</table>';
				}

				if ( count($email_domains['ssl']) ) {
					//$send_email = true;
					$subject .= ' - ' . count($email_domains['ssl']) . ' Expiring SSL Certificates';
					$message .= '<h2><img src="' . plugins_url('domain-check/images/icons/lock-locked-yellow-24x24.png') . '">Your Expiring SSL Certificates</h2>' . "\n";
					$message .= '<table>';
					$counter = 0;
					foreach ($email_domains['ssl'] as $result_idx => $domain_result_idx) {
						$cta = '';
						if (isset($ssl_result[$domain_result_idx]['domain_expires']) && $ssl_result[$domain_result_idx]['domain_expires']) {
							$domain_expires = $ssl_result[$domain_result_idx]['domain_expires'];
							if ($domain_expires > (time() + (86400 * 27)) && $domain_expires < (time() + (86400 * 28))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 13)) && $domain_expires < (time() + (86400 * 14))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 7)) && $domain_expires < (time() + (86400 * 8))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 3)) && $domain_expires < (time() + (86400 * 4))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 2)) && $domain_expires < (time() + (86400 * 3))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 1)) && $domain_expires < (time() + (86400 * 2))) {
								$send_email = true;
							}
						}
						if (isset($ssl_result[$domain_result_idx]['domain_expires']) && $ssl_result[$domain_result_idx]['domain_expires']) {
							$days_flat = number_format(($ssl_result[$domain_result_idx]['domain_expires'] - time())/60/60/24, 0);
							$expires =  $days_flat . ' Days';
							if ($days_flat < 60) {

							}
							if ($expires < 0) {
								$expires = 'Expired';
							}

							if ($ssl_result[$domain_result_idx]['domain_expires'] - time() < (86400 * 60)) {
								$cta = '<a href="' . DomainCheckLinks::ssl($domain_result[$domain_result_idx]['domain_url']) . '" target="_blank">Renew [&raquo;]</a>';
							}
						} else {
							$expires = 'n/a';
						}
						$message .= '<tr>';
						$message .= '<td><strong>' . $ssl_result[$domain_result_idx]['domain_url'] . '</strong></td>' . "\n";
						$message .= '<td>' . $expires . '</td>' . "\n";
						$message .= '<td>' . $cta . '</td>' ."\n";
						$message .= '</tr>';
						$counter++;
					}
					$message .= '</table>';
				}

				if ( count($email_domains['taken']) ) {
					//$send_email = true;
					$subject .= ' - ' . count($email_domains['taken']) . ' Expiring Watched Domains';
					$message .= '<h2><img src="' . plugins_url('domain-check/images/icons/eye-24x24.png') . '">Your Expiring Watched Domains</h2>' . "\n";
					$message .= '<table>';
					$counter = 0;
					foreach ($email_domains['taken'] as $result_idx => $domain_result_idx) {
						$cta = '';
						if (isset($domain_result[$domain_result_idx]['domain_expires']) && $domain_result[$domain_result_idx]['domain_expires']) {
							$domain_expires = $domain_result[$domain_result_idx]['domain_expires'];
							if ($domain_expires > (time() + (86400 * 27)) && $domain_expires < (time() + (86400 * 28))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 13)) && $domain_expires < (time() + (86400 * 14))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 7)) && $domain_expires < (time() + (86400 * 8))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 3)) && $domain_expires < (time() + (86400 * 4))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 2)) && $domain_expires < (time() + (86400 * 3))) {
								$send_email = true;
							}
							if ($domain_expires > (time() + (86400 * 1)) && $domain_expires < (time() + (86400 * 2))) {
								$send_email = true;
							}
						}
						if (isset($domain_result[$domain_result_idx]['domain_expires']) && $domain_result[$domain_result_idx]['domain_expires']) {
							$days_flat = number_format(($domain_result[$domain_result_idx]['domain_expires'] - time())/60/60/24, 0);
							$expires =  $days_flat . ' Days';
							if ($days_flat < 60) {

							}
							if ($expires < 0) {
								$expires = 'Expired';
							}
							if ($domain_result[$domain_result_idx]['domain_expires'] - time() < (86400 * 60)) {
								$cta = '<a href="' . DomainCheckLinks::homepage($domain_result[$domain_result_idx]['domain_url']) . '" target="_blank">Renew [&raquo;]</a>';
							}
						} else {
							$expires = 'n/a';
						}
						$message .= '<tr>';
						$message .= '<td><strong>' . $domain_result[$domain_result_idx]['domain_url'] . '</strong></td>' . "\n";
						$message .= '<td>' . $expires . '</td>' . "\n";
						$message .= '<td>' . $cta . '</td>' . "\n";
						$message .= '</tr>';
						$counter++;
					}
					$message .= '</table>';
				}

				/*
				//coupons make the email go to the Updates tab in Gmail, its annoying I don't like it, make it a setting
				$message .= '<h2><img src="' . plugins_url('domain-check/images/icons/tags-green-24x24.png') . '">Daily Coupons and Deals</h2>' . "\n";
				$coupons = DomainCheckCouponData::get_data();
				$coupons  = $coupons[DomainCheckLinks::$primary_domain];
				$coupon_ads = array();
				$text_ads = array();
				foreach ($coupons['links']['link'] as $coupon_link_idx => $coupon_link_data) {
					if (isset($coupon_link_data['link-type']) && $coupon_link_data['link-type']) {
						if ($coupon_link_data['link-type'] == 'Text Link') {
							if (isset($coupon_link_data['coupon-code'])
								&& ((is_array($coupon_link_data['coupon-code']) && count($coupon_link_data['coupon-code'])) || $coupon_link_data['coupon-code'])
							) {
								$coupon_ads[] = $coupon_link_idx;
							} else {
								$text_ads[] = $coupon_link_idx;
							}

						} else {
							$img_ads[] = $coupon_link_idx;
						}

					}
				}
				$message .= '<div class="coupon-ad-wrapper" style="width: 100%; display:block; clear: both;">';
				$message .= '<h3>' . DomainCheckLinks::$primary_domain . '</h3>';
				$limit = 5;
				$count = 0;
				shuffle($coupon_ads);
				foreach ($coupon_ads as $coupon_link_idx) {
					if ($count >= $limit) {
						break;
					}
					$coupon_link_data = $coupons['links']['link'][$coupon_link_idx];

					$message .= '<div class="domain-check-coupon-ad">
						<p style="text-align: left;">
							<strong>
								<a href="' . $coupon_link_data['clickUrl'] . '" target="_blank">
								' . $coupon_link_data['link-code-html'] . '
								</a>
							</strong>
						</p>

						<p style="text-align: center;">

						<div style="text-align: center; display: inline-block;">
							Coupon Code:
						</div>
						<div style="text-align: center; display: inline-block;">
							<a href="' . $coupon_link_data['clickUrl'] . '" style="border:1px #000000 dashed; background-color: #00AA00; color: #FFFFFF; font-size: 20px; margin: 10px; padding: 10px;" target="_blank">
								<strong>
									' . $coupon_link_data['coupon-code'] . '
								</strong>
							</a>
						</div>
						</p>
					</div>';
					$count++;
				}
				shuffle($text_ads);
				foreach ($text_ads as $text_link_idx) {
					if ($count >= $limit) {
						break;
					}
					$text_link_data = $coupons['links']['link'][$text_link_idx];

					$message .= '<div class="domain-check-coupon-ad">
						<p style="text-align: left;">
							<strong>
								<a href="' . $coupon_link_data['clickUrl'] . '">
								' . $text_link_data['link-code-html'] . '
								</a>
							</strong>
						</p>
					</div>';
					$count++;
				}
				$message .= '</div>';
				*/

				$message .= '<br>';

				$message .= '<p>
				You are listed as an administrator of these domains by <a href="' . $site_url . '">' . $blog_name .'</a>.
				You may be expected to take action renewing or inspecting these domains or SSL certificates!
				If you have questions or would like to unsubscribe from some some of these alerts please log in to <a href="' . $site_url . '">' . $blog_name . '</a> or contact the ' . $blog_name . ' administrators at <a href="mailto:'.$admin_email.'">' . $admin_email . '</a>.
				</p>' . "\n";
				$message .= '<p>
				This email is generated automatically using a Wordpress plugin named <a href="https://wordpress.org/plugins/domain-check/">Domain Check</a> that <a href="' . $site_url . '">' . $blog_name . '</a> uses to help you monitor expiring domains and expiring SSL certificates.
				</p>' . "\n";
				$message .= '</html>';

				//error_log($message);

				if ( class_exists('DomainCheckDebug') ) {
					$send_email = 1;
				}


				if ($send_email) {
					$headers = array(
						'Content-Type: text/html; charset=UTF-8'
					);
					if ($admin_email) {
						$headers[] = 'From: ' . $blog_name . ' <' . $admin_email . '>';
					}
					wp_mail(
						$email_address,
						$subject,
						$message,
						$headers
					);
				}
			}

			//send owner email...
		}

		public function domain_check_cron_watch() {
			global $wpdb;

			$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$yesterday = $today - 86400;
			$expires_tomorrow = $today + 86400 + 86400;

			//update any domains expiring tomorrow
			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_watch > 0 AND domain_expires >= ' . $yesterday . ' AND domain_expires <= ' . $expires_tomorrow;
			$domain_result = $wpdb->get_results( $sql, 'ARRAY_A' );
			foreach ($domain_result as $domain_result_idx => $domain_result_data) {
				DomainCheckSearch::domain_search($domain_result_data['domain_url']);
			}

			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_watch > 0 AND domain_expires >= ' . $yesterday . ' AND domain_expires <= ' . $expires_tomorrow;
			$ssl_result = $wpdb->get_results( $sql, 'ARRAY_A' );
			$expiring_ssl_count = count($ssl_result);
			foreach ($ssl_result as $domain_result_idx => $domain_result_data) {
				DomainCheckSearch::ssl_search($domain_result_data['domain_url']);
			}
		}

		//load wp-plugin callback
		public function plugins_loaded() {

			//-----------------------------------
			//WP
			//-----------------------------------
			if ( ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) && is_admin() ) {
				if (self::PLUGIN_VERSION != get_option(DomainCheckConfig::OPTIONS_PREFIX . 'version') ) {
					require_once(dirname(__FILE__) . '/admin/domain-check-update.php');
					DomainCheckUpdate::init();
				}
			}

			//-----------------------------------
			//WP-ADMIN
			//-----------------------------------
		}

		//activate
		public function activate_plugin() {
			global $wpdb, $charset_collate;

			//ob_start();

			//validate env
			//plugin could be on any server, make sure they have the bare minimum PHP
			if (version_compare(PHP_VERSION, self::PHP_REQUIRED_VERSION, '<')) {
				//yikes...
				deactivate_plugins(plugin_basename(__FILE__));
				wp_die(self::PLUGIN_NAME . ' - this plugin requires at least PHP v' . self::PHP_REQUIRED_VERSION);
				return;
			}

			if (!wp_get_schedule('domain_check_cron_watch')) {
				wp_schedule_event(time(), 'daily', 'domain_check_cron_watch');
			}
			if (!wp_get_schedule('domain_check_cron_coupons')) {
				wp_schedule_event(time() + 300, 'daily', 'domain_check_cron_coupons');
			}
			if (!wp_get_schedule('domain_check_cron_email')) {
				wp_schedule_event(time() + 600, 'daily', 'domain_check_cron_email');
			}

			$this->domain_check_cron_coupons();


			//upgrade!
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			//db create
			$charset_collate = $wpdb->get_charset_collate();

			$query = 'CREATE TABLE ' . self::$db_prefix . '_domains (
				domain_id BIGINT NOT NULL AUTO_INCREMENT,
				domain_url VARCHAR(255) NOT NULL,
				domain_extension VARCHAR(255) DEFAULT NULL,
				user_id BIGINT DEFAULT 0,
				status VARCHAR(255),
				date_added BIGINT DEFAULT 0,
				search_date BIGINT DEFAULT 0,
				domain_watch INT(11) DEFAULT 0,
				domain_last_check BIGINT DEFAULT 0,
				domain_next_check BIGINT DEFAULT 0,
				domain_created BIGINT DEFAULT 0,
				domain_expires BIGINT DEFAULT 0,
				owner VARCHAR(255) DEFAULT NULL,
				registrar VARCHAR(255) DEFAULT NULL,
				nameserver VARCHAR(255) DEFAULT NULL,
				autorenew VARCHAR(255) DEFAULT NULL,
				domain_settings BLOB DEFAULT null,
				cache BLOB DEFAULT null,
				PRIMARY KEY  (domain_id))' . $charset_collate;

			dbDelta($query);

			$query = 'CREATE TABLE ' . self::$db_prefix . '_ssl (
				ssl_domain_id BIGINT NOT NULL AUTO_INCREMENT,
				domain_id BIGINT DEFAULT 0,
				domain_url VARCHAR(255) NOT NULL,
				user_id BIGINT DEFAULT 0,
				status VARCHAR(255),
				search_date BIGINT DEFAULT 0,
				date_added BIGINT DEFAULT 0,
				domain_watch INT(11) DEFAULT 0,
				domain_last_check BIGINT DEFAULT 0,
				domain_next_check BIGINT DEFAULT 0,
				domain_created BIGINT DEFAULT 0,
				domain_expires BIGINT DEFAULT 0,
				owner VARCHAR(255) DEFAULT NULL,
				domain_settings BLOB DEFAULT NULL,
				cache BLOB DEFAULT NULL,
				PRIMARY KEY  (ssl_domain_id)) ' . $charset_collate;

			dbDelta($query);

			$query = 'CREATE TABLE ' . self::$db_prefix . '_coupons (
				coupon_id BIGINT NOT NULL,
				coupon_site VARCHAR(255) NOT NULL,
				cache BLOB DEFAULT NULL,
				PRIMARY KEY  (coupon_id)) ' . $charset_collate;

			dbDelta($query);

			//add wp-plugin options...
			foreach(DomainCheckConfig::$options as $key => $value) {
				if ($key == DomainCheckConfig::OPTIONS_PREFIX . 'version') {
					$value = self::PLUGIN_VERSION;
				}
				add_option($key, $value);
			}

			$wpdb->flush();

			//add entry for current url...
			$update_res = DomainCheckCouponData::update();

			DomainCheckSearch::domain_search(site_url(), false, true, true);
			DomainCheckSearch::ssl_search(site_url(), true);

			//trigger_error(ob_get_contents(), E_USER_ERROR);
		}

		//deactivate
		public function deactivate_plugin() {
			global $wpdb;

			wp_clear_scheduled_hook('domain_check_cron_email');
			wp_clear_scheduled_hook('domain_check_cron_coupons');
			wp_clear_scheduled_hook('domain_check_cron_watch');

			//db drop tables
			$wpdb->query('SET FOREIGN_KEY_CHECKS=0;');
			//$wpdb->query('DROP TABLE ' . self::$db_prefix . '_domains');
			//$wpdb->query('DROP TABLE ' . self::$db_prefix . '_ssl');
			//$wpdb->query('DROP TABLE ' . self::$db_prefix . '_coupons');
			$wpdb->query('SET FOREIGN_KEY_CHECKS=1;');

			//clean up wp-plugin options...
			foreach(DomainCheckConfig::$options as $key => $value) {
				delete_option($key);
			}
		}

		public static function is_pro() {
			return self::$is_pro;
		}

		public static function pro($class, $method, $data) {
			if ( self::$is_pro ) {
				return DomainCheckPro::filter($class, $method, $data);
			} else {
				return null;
			}
		}
	}

	$domain_check = new DomainCheck();
}