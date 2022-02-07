<?php
//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

class DomainCheckCouponData {

	private static $data = array();
	public static $class_init = false;
	public static $api_url = 'http://static.domaincheckplugin.com/';
	public static $updating = false;
	public static $update_start = 0;
	public static $bitly_links = array();

	public static function get_data() {
		self::init();
		return self::$data;
	}

	public static function init() {
		if (!self::$class_init) {
			self::_db_import();
			$coupons_found = false;
			foreach (self::$data as $site => $site_data) {
				if (count($site_data['links']['link'])) {
					$coupons_found = true;
					break;
				}
			}

			//last resort
			if (!$coupons_found) {
				self::_json_import();
			}
		}
	}

	private static function _json_import() {
		$return_data = array();
		//if (!is_file(dirname(__FILE__) . '/../admin/cache/coupons.json')) {
		//	self::update();
		//}
		if (is_file(dirname(__FILE__) . '/../admin/cache/coupons.json')) {
			ob_start();
			include(dirname(__FILE__) . '/../admin/cache/coupons.json');
			$data = ob_get_contents();
			ob_end_clean();
			$data = json_decode($data, true);
			if ($data) {
				$return_data = $data;
			}
		}
		self::$data = $return_data;
		return self::$data;
	}

	private static function _db_import() {
		global $wpdb;

		$return_data = array();

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_coupons';

		$res = $wpdb->get_results( $sql, 'ARRAY_A' );
		if ($res && count($res)) {
			foreach ($res as $res_idx => $res_data) {
				if (isset($res_data['coupon_site']) && $res_data['coupon_site']) {
					if (!isset($return_data[$res_data['coupon_site']])) {
						$return_data[$res_data['coupon_site']] = array('links' => array('link'=> array()));
					} else {

					}
					$decoded_data = json_decode(gzuncompress($res_data['cache']), true);
					if ($decoded_data) {
						array_push($return_data[$res_data['coupon_site']]['links']['link'], $decoded_data);
					} else {
					}
				} else {
				}
			}
		} else {
		}

		self::$data = $return_data;
	}

	public static function search($needle, $language = 'all', $site = null) {
		self::init();

		$ret = array();
		$needle_lower = strtolower($needle);
		$found = 0;
		foreach (self::$data as $coupon_site => $coupon_data) {
			$ret[$coupon_site] = array();
			$ret[$coupon_site]['links'] = array();
			$ret[$coupon_site]['links']['link'] = array();
			if (isset($coupon_data['links']['link'])) {
				foreach ($coupon_data['links']['link'] as $coupon_link_idx => $coupon_link_data) {
					if (isset($coupon_link_data['link-code-html'])) {
						if (strpos(strtolower($coupon_link_data['description']), $needle_lower) !== false) {
							if ($language != 'all' && $coupon_link_data['language'] != $language) {
								continue;
							}
							$ret[$coupon_site]['links']['link'][] = $coupon_link_data;
							$found++;
						}
					}
				}
			}
		}
		return $ret;
	}

	public static function update() {
		global $wpdb;

		if (self::$updating || (self::$update_start !== 0 && self::$update_start < (time() - 10))) {
			return false;
		}

		self::$updating = true;
		self::$update_start = time();

		//curl to get coupon data from S3...
		$url = self::$api_url . 'coupons.json';

		//verify actual wordpress site & not a bot
		$url .= '?verify=' . site_url();

		$coupons_raw = null;
		if ( function_exists('curl_init') ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$coupons_raw = curl_exec($ch);
			curl_close($ch);
		} else if ( ini_get('allow_url_fopen') ) {
			$coupons_raw = file_get_contents($url);
		} else {

		}

		$coupons_json = json_decode($coupons_raw, true);
		if ($coupons_json && count($coupons_json)) {

			//(too complicated in WP to do file caching for coupons)
			//$fp = fopen(dirname(__FILE__) . '/../admin/cache/coupons.json', 'w+' );
			//fwrite($fp, $coupons_raw);
			//fclose($fp);

			//clear DB
			$sql = 'DELETE FROM ' . DomainCheck::$db_prefix . '_coupons';

			/*
			$sql = $wpdb->prepare(
				'DELETE FROM ' . DomainCheck::$db_prefix . '_coupons
				 WHERE coupon_site != %s',
				array(
					'0'
				)
			);
			*/
			$del_res = $wpdb->query($sql);
			$wpdb->flush();


			$coupon_count = 0;
			foreach ($coupons_json as $coupon_idx => $coupon_data) {
				$coupon_data['updated'] = time();
				foreach ($coupon_data['links']['link'] as $coupon_link_idx => $coupon_link) {
					$valarr = array(
						'coupon_id' => $coupon_count,
						'coupon_site' => $coupon_idx,
						'cache' => gzcompress(json_encode($coupon_link))
					);
					$wpdb->insert(
						DomainCheck::$db_prefix . '_coupons',
						$valarr
					);
					$coupon_count++;
				}
			}

			self::$data = $coupons_json;

			if (get_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_updated')) {
				update_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_updated', time());
			} else {
				add_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_updated', time());
			}
			self::$updating = false;
			return true;
		}
		self::$updating = false;
		return false;
	}

	public static function last_updated() {
		return get_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_updated');
	}

	public static function valid_site($site) {
		self::init();
		if (isset(self::$data[$site])) {
			return true;
		}
		return false;
	}

	public static function get_coupon_link_domain( $domain ) {

		$coupon_link = 'http://www.anrdoezrs.net/interactive?domainToCheck='
			. urlencode($domain)
			. '&tld=' . urlencode('.com')
			. '&aid=10451087'
			. '&pid=7922728'
			. '&cvosrc=' . urlencode('affiliate.cj.7922728')
			. '&url=' . urlencode('https://www.godaddy.com/domains/searchresults.aspx?isc=cjcdomsb3');

		return $coupon_link;
	}

}