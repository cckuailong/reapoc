<?php
//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

require_once(dirname(__FILE__) . '/domain-check-whois-data.php');

class DomainCheckWhois {

	private static $m_data = null;
	private static $m_init = false;

	public static function init() {
		if (!self::$m_init) {
			DomainCheckWhoisData::init();
			self::$m_init = true;
		}
	}

	public static function dolookup($domain, $raw = false) {

		self::init();
		//echo 'CHECKING FOR....... ' . $domain;
		$connectiontimeout = 5;
		$sockettimeout = 15;
		$domain = strtolower($domain);
		$domain_len = strlen($domain);
		$possible_tld = '';
		$whois_servers = array();
		$data = '';
		$extension = '';
		$extension_data = null;

		$extension = self::getextension($domain);
		$extension_data = DomainCheckWhoisData::$whoisData[$extension];


		if ( $extension_data && isset( $extension_data['whois'] ) ) {
			$server = $extension_data['whois'];
		} else {
			DomainCheckAdmin::admin_notices_add('No WHOIS servers exist for domain extension <strong>' . $domain . '</strong>.', 'error', null, 'circle-x');
			return array('error' => 'No WHOIS servers exist for domain extension ' . $domain);
		}

		$fp = @fsockopen(
			$server,
			43,
			$errno,
			$errstr,
			$connectiontimeout
		);
		$starttime = time();
		if ( $fp ){
			fputs($fp, $domain . "\r\n");
			socket_set_timeout($fp, $sockettimeout);
			while( !feof($fp) ){
				$data .= fread($fp, 4096);
				if (time() - $starttime > 30) {
					break;
				}
			}
			fclose($fp);

			//convert otherwise the json_encode/json_decode won't work right
			$data = utf8_encode($data);

			if ($raw) {
				return $data;
			}

			//echo $data;
			$ret = array(
				'data' => $data,
				'extension' => $extension,
				'status' => 0,
				'domain_expires' => 0,
				'registrar' => null,
				'nameserver' => null
			);

			//if we generated pattern before
			$ret['status'] = DomainCheckWhoisData::get_status($extension, $data);

			if ($ret['status']) {
				$ret['domain_expires'] = DomainCheckWhoisData::get_expiration( $extension, $data );
				$ret['registrar'] = DomainCheckWhoisData::get_registrar( $extension, $data );
				$ret['nameserver'] = DomainCheckWhoisData::get_nameserver( $extension, $data );
			}

			return $ret;
		} else {
			DomainCheckAdmin::admin_notices_add('Error - could not open a connection to ' . $server, 'error', null, 'circle-x');
			return array('error' => 'Error - could not open a connection to ' . $server);
		}
	}

	public static function getextension($domain) {
		self::init();
		$domain = strtolower($domain);
		$domain_len = strlen($domain);
		$possible_tld = '';

		$extension = '';
		$extension_data = null;

		//break up the domain by dots for .com.mx and .mx and .com and such
		$domain_arr = explode( '.', $domain );
		$domain_arr_len = count($domain_arr);
		$domain_arr_last_idx = $domain_arr_len - 1;
		$last_idx = $domain_arr_last_idx + 1; //cheese this for now for checking later

		$tmp_tdl = null;
		$extension = '';

		for ( $i = $domain_arr_last_idx; $i >= 0; $i-- ) {
			if ($tmp_tdl === null) {
				$tmp_tdl = $domain_arr[$i];
			} else {
				$tmp_tdl = $domain_arr[$i] . '.' . $tmp_tdl;
			}
			if ( isset( DomainCheckWhoisData::$whoisData[$tmp_tdl] ) ) {
				$extension = $tmp_tdl;
			}
		}

		return $extension;

	}

	public static function getextensions() {
		self::init();

		return DomainCheckWhoisData::$whoisData;
	}

	public static function validdomain($domain) {

		$valid_domain = false;
		$domain_extension = null;
		if ( mb_strpos( $domain, '.' ) !== false ) {
			$domain_parse = parse_url( mb_strtolower( trim( $domain ) ) );

			if (isset($domain_parse['path']) && $domain_parse['path'] != '/') {
				$domain_parse = $domain_parse['path'];
			} else if (isset($domain_parse['host'])) {
				$domain_parse = $domain_parse['host'];
			}

			$domain_parse = preg_replace( "/[^a-z0-9.-]+/i", '', $domain_parse );
			if ( $domain_parse && mb_strpos( $domain_parse, '.' ) !== false) {
				$domain_extension = DomainCheckWhois::getextension( $domain_parse );
				$domain_preface = str_replace( '.' . $domain_extension, '', $domain_parse );
				if ($domain_extension && $domain_preface && $domain_preface != '.' && $domain_preface != '-' && $domain_preface != '' ) {

					$fqdn = str_replace('.' . $domain_extension, '', $domain_parse);
					$fqdn = explode('.', $fqdn);
					$fqdn = $fqdn[(count($fqdn) - 1)] . '.' . $domain_extension;

					$domain_root = $fqdn;
					$valid_domain = array(
						'domain_extension' => $domain_extension,
						'domain' => $domain_parse,
						'fqdn' => $fqdn
					);

				}
			}
		}
		return $valid_domain;
	}

}