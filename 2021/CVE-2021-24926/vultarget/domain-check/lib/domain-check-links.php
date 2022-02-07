<?php
//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

class DomainCheckLinks {

	public static $primary_site = 'GoDaddy';
	public static $primary_domain = 'GoDaddy';
	public static $primary_ssl = 'GoDaddy';
	public static $primary_wordpress = 'GoDaddy';
	public static $primary_extension = 'GoDaddy';

	private static $is_init = false;

	public static function init() {
		if (!self::$is_init) {
			self::set_primary();
			self::$is_init = true;
		}
	}

	public static function set_primary() {
		$coupons_primary_site = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_primary_site');
		if (!$coupons_primary_site) {
			$coupons_primary_site = 'GoDaddy';
		}
		$data = DomainCheckCouponData::get_data();
		if (!isset($data[$coupons_primary_site])) {
			$counter = 0;
			foreach ($data as $coupons_primary_site_idx => $coupon_primary_data) {
				if (!$counter) {
					$coupons_primary_site = $coupons_primary_site_idx;
				}
				$counter++;
			}
		}
		self::$primary_domain = $coupons_primary_site;
		self::$primary_site = $coupons_primary_site;
		self::$primary_ssl = $coupons_primary_site;
		self::$primary_wordpress = $coupons_primary_site;
		self::$primary_extension = $coupons_primary_site;
	}

	public static function domain_search($domain) {
		self::init();
		return self::homepage($domain);
	}

	public static function domain_renew($domain) {
		self::init();
		return self::homepage($domain);
	}

	public static function ssl($domain) {
		self::init();
		$coupons = DomainCheckCouponData::search('ssl', 'en');
		if (isset($coupons[self::$primary_extension]) && count($coupons[self::$primary_extension]['links']['link'])) {
			$tmp_coupon = array_pop($coupons[self::$primary_extension]['links']['link']);
			return $tmp_coupon['clickUrl'];
		}
		return self::homepage($domain);
	}

	public static function wordpress($domain) {
		self::init();
		$coupons = DomainCheckCouponData::search('wordpress', 'en');
		if (isset($coupons[self::$primary_extension]) && count($coupons[self::$primary_extension]['links']['link'])) {
			$tmp_coupon = array_pop($coupons[self::$primary_extension]['links']['link']);
			return $tmp_coupon['clickUrl'];
		}
		return self::homepage($domain);
	}

	public static function domain_extension($domain) {
		self::init();
		$extension = DomainCheckWhois::getextension($domain);
		if ($extension) {
			$coupons = DomainCheckCouponData::search($extension, 'en');
			if (isset($coupons[self::$primary_extension]) && count($coupons[self::$primary_extension]['links']['link'])) {
				$tmp_coupon = array_pop($coupons[self::$primary_extension]['links']['link']);
				return $tmp_coupon['clickUrl'];
			} else {
				return self::homepage($domain);
			}
		} else {
			return self::homepage($domain);
		}
	}

	public static function homepage($domain, $site = null) {
		self::init();
		$coupons = DomainCheckCouponData::search('home', 'en');

		if (!$site) {
			$site = self::$primary_extension;
		}

		if (isset($coupons[$site]) && count($coupons[$site]['links']['link'])) {
			$tmp_coupon = array_pop($coupons[$site]['links']['link']);
			return $tmp_coupon['clickUrl'];
		}
		$coupons = DomainCheckCouponData::search('homepage', 'en');
		if (isset($coupons[$site]) && count($coupons[$site]['links']['link'])) {
			$tmp_coupon = array_pop($coupons[$site]['links']['link']);
			return $tmp_coupon['clickUrl'];
		}
		$coupons = DomainCheckCouponData::search('domain', 'en');
		if (isset($coupons[$site]) && count($coupons[$site]['links']['link'])) {
			foreach ($coupons[$site]['links']['link'] as $tmp_coupon) {
				//$tmp_coupon = array_pop($coupons[$site]['links']['link']);
				if (isset($tmp_coupon['clickUrl'])) {
					return $tmp_coupon['clickUrl'];
				}
			}
		}
		return false;
	}

	public static function registrar_link( $iana ) {

	}
}