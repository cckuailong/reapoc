<?php

class DomainCheckEmail {

	public static function email_test($email) {
		global $wpdb;

		$emails = array(
			$email => array(
				'owned' => array(),
				'ssl' => array(),
				'taken' => array()
			)
		);

		$blog_name = get_option('blogname');
		$site_url = site_url();
		$site_domain = null;
		$admin_email = get_option('admin_email');

		$domain_data = DomainCheckWhois::validdomain($site_url);

		if ($domain_data) {
			$domain_parse = $domain_data['domain'];
			$site_domain = $domain_parse;
			$domain_root = $domain_data['domain'];
			$search = $domain_data['domain'];
			$domain_extension = $domain_data['domain_extension'];

			$fqdn = str_replace('.' . $domain_extension, '', $domain_parse);
			$fqdn = explode('.', $fqdn);
			$fqdn = $fqdn[(count($fqdn) - 1)] . '.' . $domain_extension;

			$domain_root = $fqdn;
			$search = $fqdn;
			$site_domain = $fqdn;
		} else {
			return array('error' => $site_url . ' is not a valid domain.');
		}

		DomainCheckSearch::domain_search($search);
		DomainCheckSearch::ssl_search($search);

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_url="' . $site_domain . '" ORDER BY domain_expires ASC';
		$domain_result = $wpdb->get_results($sql, 'ARRAY_A');
		$expiring_domain_count = count($domain_result);

		foreach ($domain_result as $domain_result_idx => $domain_result_data) {
			if ($domain_result[$domain_result_idx]['domain_settings']) {
				$domain_result[$domain_result_idx]['domain_settings'] = json_decode(gzuncompress($domain_result[$domain_result_idx]['domain_settings']), true);
			}
			if ($domain_result_data['status'] == 2) {
				$emails[$email]['owned'][] = $domain_result_idx;
			} else if ($domain_result_data['status'] == 1) {
				$emails[$email]['taken'][] = $domain_result_idx;
			}
		}

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_url="' . $site_domain . '" ORDER BY domain_expires ASC';
		$ssl_result = $wpdb->get_results($sql, 'ARRAY_A');
		$expiring_ssl_count = count($ssl_result);
		foreach ($ssl_result as $domain_result_idx => $domain_result_data) {
			if (isset($ssl_result[$domain_result_idx]['domain_settings']) && $ssl_result[$domain_result_idx]['domain_settings']) {
				$ssl_result[$domain_result_idx]['domain_settings'] = json_decode(gzuncompress($ssl_result[$domain_result_idx]['domain_settings']), true);
			} else {
				$ssl_result[$domain_result_idx]['domain_settings'] = array();
			}
			$emails[$email]['ssl'][] = $domain_result_idx;
		}


		foreach ($emails as $email_address => $email_domains) {
			$subject = 'Domain Check';
			$message = '<html>' . "\n";
			$message .= '<h1>Domain Check - ' . $blog_name . '<h1>' . "\n";
			$send_email = false;
			if (count($email_domains['owned'])) {
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
						$days_flat = number_format(($domain_result[$domain_result_idx]['domain_expires'] - time()) / 60 / 60 / 24, 0);
						$expires = $days_flat . ' Days';
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

			if (count($email_domains['ssl'])) {
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
						$days_flat = number_format(($ssl_result[$domain_result_idx]['domain_expires'] - time()) / 60 / 60 / 24, 0);
						$expires = $days_flat . ' Days';
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
					$message .= '<td>' . $cta . '</td>' . "\n";
					$message .= '</tr>';
					$counter++;
				}
				$message .= '</table>';
			}

			if (count($email_domains['taken'])) {
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
						$days_flat = number_format(($domain_result[$domain_result_idx]['domain_expires'] - time()) / 60 / 60 / 24, 0);
						$expires = $days_flat . ' Days';
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
			You are listed as an administrator of these domains by <a href="' . $site_url . '">' . $blog_name . '</a>.
			You may be expected to take action renewing or inspecting these domains or SSL certificates!
			If you have questions or would like to unsubscribe from some some of these alerts please log in to <a href="' . $site_url . '">' . $blog_name . '</a> or contact the ' . $blog_name . ' administrators at <a href="mailto:' . $admin_email . '">' . $admin_email . '</a>.
			</p>' . "\n";
			$message .= '<p>
			This email is generated automatically using a Wordpress plugin named <a href="https://wordpress.org/plugins/domain-check/">Domain Check</a> that <a href="' . $site_url . '">' . $blog_name . '</a> uses to help you monitor expiring domains and expiring SSL certificates.
			</p>' . "\n";
			$message .= '</html>';

			//error_log($message);

			//if ($send_email) {
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
			//}
		}
		return true;
	}
}