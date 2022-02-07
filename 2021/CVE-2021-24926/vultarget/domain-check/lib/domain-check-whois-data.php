<?php
//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

class DomainCheckWhoisData {

	private static $class_init = false; //have we initialized the class and imported data from the files in /db

	public static $whoisDataXml = null; //the imported whois XML data

	public static $registrarData = array(); //the imported whois XML data

	//someone is inevitably going to try to copy & paste this
	//I made this by hand so plz pause for a moment of silence, programmer to programmer, for how much work this took
	public static $whoisData = array(
		'app' => array(
			'available' => 'Domain not found.',
			'expires' => ''
		),
		'bet' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'biz' => array(
			'available' => 'Not found: ',
			'expires' => 'Domain Expiration Date:',
			'expires_func' => 'co'
		),
		'buzz' => array(
			'available' => 'No Data Found',
		),
		'ca' => array(
			'available' => 'Domain status:         available',
			'expires' => 'Expiry date:',
		),
		'cn' => array (
			'expires' => 'Expiration Time: ',
			'expires_func' => 'cn'
		),
		'cc' => array(
			'available' => 'No match for ',
			'expires' => 'Registry Expiry Date: '
		),
		'club' => array(
			'available' => 'queried object does not exist',
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois',
		),
		'co' => array(
			'available' => 'Not found: ',
			'expires' => 'Domain Expiration Date: ',
		),
		'co.uk' => array(
			'available' => 'This domain name has not been registered.',
			'expires' => 'Expiry date: ',
			'expires_func' => 'com'
		),
		'com' => array(
			'available' => 'No match for',
			'expires' => 'Expiration Date: ',
		),
		'com.au' => array(
			'available' => 'No Data Found',
			'expires' => '',
		),
		'com.br' => array(
			'expires' => 'expires:',
		),
		'com.mx' => array(
			'expires' => 'Expiration Date:',
			'expires_func' => 'io'
		),
		'creditcard' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'de' => array(
			'available' => 'Status: free',
		),
		'dk' => array(
			'expires' => 'Expires:',
			'expires_func' => 'io'
		),
		'domains' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'edu' => array(
			'available' => 'No Match',
			'expires' => 'Domain expires: ',
			'expires_func' => 'com'
		),
		'fm' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'fi' => array(
			'expires' => 'expires............:',
		),
		'fr' => array(
			'available' => 'No entries found',
			'expires' => 'Expiry Date: ',
		),
		'gov' => array(
			'available' => 'No match for ',
			'expires' => '',
		),
		'hosting' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois',
		),
		'io' => array(
			'available' => 'is available for purchase',
			'expires' => 'Expiry : ',
		),
		'it' => array(
			'expires' => 'Expire Date:',
			'expires_func' => 'io'
		),
		'info' => array(
			'available' => 'NOT FOUND',
			'expires' => 'Registry Expiry Date: ',
			'expires_func' => 'whois'
		),
		'in' => array(
			'expires' => 'Expiration Date:'
		),
		//'int' => array(),
		'jobs' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'la' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'live' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'ltd.uk' => array(
			'available' => 'No match for ',
			'expires' => 'Expiry date: ',
			'expires_func' => 'com'
		),
		'marketing' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'me' => array(
			'expires' => 'Registry Expiry Date: ',
			'expires_func' => 'whois'
		),
		'mx' => array(
			'expires' => 'Expiration Date:',
			'expires_func' => 'io'
		),
		//'mil' => array(),
		'mobi' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		//'name' => array(),
		'net' => array(
			'available' => 'No match for ',
			'expires' => 'Expiration Date: ',
			'expires_func' => 'com'
		),
		'net.au' => array(
			'available' => 'No Data Found',
			'expires' => '',
		),
		'nu' => array(
			'expires' => 'expires:',
			'expires_func' => 'io'
		),
		'org' => array(
			'available' => 'NOT FOUND',
			'expires' => 'Registry Expiry Date: ',
			'expires_func' => 'whois'
		),
		'org.uk' => array(
			'available' => 'No match for ',
			'expires' => 'Expiry date: ',
			'expires_func' => 'com',
		),
		//'pro' => array(),
		'rich' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'rocks' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'ru' => array(
			'expires' => 'free-date:     ',
			'expies_func' => 'ru'
		),
		'sc' => array(
			'available' => 'NOT FOUND',
			'expires' => 'Expiration Date:',
			'expires_func' => 'com'
		),
		'se' => array(
			'expires' => 'expires:',
			'expires_func' => 'io'
		),
		'shop' => array(
			'whois' => 'whois.nic.shop',
			'available' => 'DOMAIN NOT FOUND',
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'tax' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'top' => array(
			'expires' => 'Registry Expiry Date: ',
			'expires_func' => 'whois'
		),
		'tv' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'tel' => array(
			'expires' => 'Domain Expiration Date:',
		),
		'travel' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'us' => array(
			'available' => 'Not found: ',
			'expires' => 'Domain Expiration Date: ',
		),
		'ws' => array(
			'available' => 'No match for ',
			'expires' => 'Expiration Date: ',
			'expires_func' => 'io'
		),
		'xyz' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		),
		'xxx' => array(
			'expires' => 'Registry Expiry Date:',
			'expires_func' => 'whois'
		)
	);

	// YYYY/MM/DD
	public static function ext_ca_expires($expiry_date) {
		$dateArr = explode('/', $expiry_date);
		return mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);
	}

	// YYYY-MM-DDTHH:MM:SSZ w/ no seconds
	public static function ext_cc_expires($expiry_date) {
		$dateArr = explode('-', $expiry_date);
		$dayArr = explode('T', $dateArr[2]);
		return mktime(0, 0, 0, $dateArr[1], $dayArr[0], $dateArr[0]);
	}

	// YYYY-MM-DD HH:MM:SS
	public static function ext_cn_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		$dateDayArr = explode('-', $dateArr[0]);
		$dateMinArr = explode(':', $dateArr[1]);
		return mktime($dateMinArr[0], $dateMinArr[1], $dateMinArr[2], (int)$dateDayArr[1], (int)$dateDayArr[0], (int)$dateDayArr[2]);
	}

	public static function ext_co_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		return mktime(0, 0, 0, date('m', strtotime(ucfirst($dateArr[1]))), $dateArr[2], $dateArr[5]);
	}

	public static function ext_co_in_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		$dateArr = explode('-', $dateArr[0]);
		return mktime(0, 0, 0, date('m', strtotime(ucfirst($dateArr[1]))), $dateArr[0], $dateArr[2]);
	}

	public static function ext_com_expires($expiry_date) {
		$dateArr = explode('-', $expiry_date);
		return mktime(0, 0, 0, date('m', strtotime(ucfirst($dateArr[1]))), $dateArr[0], $dateArr[2]);
	}

	// YYYYMMDD
	// 20191223
	public static function ext_com_br_expires($expiry_date) {
		$year = substr( $expiry_date, 0, 4 );
		$month = substr( $expiry_date, 4, 2 );
		$day = substr( $expiry_date, 6, 2 );
		return mktime(0, 0, 0, $month, $day, $year);
	}

	// MM.DD.YYYY
	public static function ext_fi_expires($expiry_date) {
		$dateArr = explode('.', $expiry_date);
		return mktime(0, 0, 0, (int)$dateArr[0], (int)$dateArr[1], (int)$dateArr[2]);
	}

	// MM/DD/YYYY
	public static function ext_fr_expires($expiry_date) {
		$dateArr = explode('/', $expiry_date);
		return mktime(0, 0, 0, (int)$dateArr[0], (int)$dateArr[1], (int)$dateArr[2]);
	}

	public static function ext_in_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		$dateDayArr = explode('-', $dateArr[0]);
		$dateMinArr = explode(':', $dateArr[1]);

		return mktime($dateMinArr[0], $dateMinArr[1], $dateMinArr[2], date('m', strtotime(ucfirst($dateDayArr[1]))), (int)$dateDayArr[0], (int)$dateDayArr[2]);
	}

	//YYYY-MM-DD
	public static function ext_io_expires($expiry_date) {
		$dateArr = explode('-', $expiry_date);
		return mktime(0, 0, 0, (int)$dateArr[1], (int)$dateArr[2], (int)$dateArr[0]);
	}

	//YYYY-MM-DDTHH:MM:SSZ w/ seconds
	public static function ext_live_expires($expiry_date) {
		$dateArr = explode('T', $expiry_date);
		$dateDayArr = explode('-', $dateArr[0]);
		$dateMinArr = explode(':', str_replace( 'Z', '', $dateArr[1] ) );
		return mktime((int)$dateMinArr[0], (int)$dateMinArr[1], (int)$dateMinArr[2], (int)$dateDayArr[1], (int)$dateDayArr[2], (int)$dateDayArr[0]);
	}

	public static function ext_ru_expires($expiry_date) {
		$dateArr = explode('.', $expiry_date);
		return mktime(0, 0, 0, (int)$dateArr[1], (int)$dateArr[2], (int)$dateArr[0]);
	}

	// Mon Jun 05 23:59:59 GMT 2017
	public static function ext_tel_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		$dateMinArr = explode(':', $dateArr[3]);
		return mktime((int)$dateMinArr[0], (int)$dateMinArr[1], (int)$dateMinArr[2], date('m', strtotime(ucfirst($dateArr[1]))), (int)$dateArr[2], (int)$dateArr[5]);
	}

	public static function ext_us_expires($expiry_date) {
		$dateArr = explode(' ', $expiry_date);
		return mktime(0, 0, 0, (int)$dateArr[1], (int)$dateArr[2], (int)$dateArr[0]);
	}

	//YYYY-MM-DDTHH:MM:SSZ w/ seconds, WHOIS standard
	public static function ext_whois_expires($expiry_date) {
		$dateArr = explode('T', $expiry_date);
		$dateDayArr = explode('-', $dateArr[0]);
		$dateMinArr = explode(':', str_replace( 'Z', '', $dateArr[1] ) );
		return mktime((int)$dateMinArr[0], (int)$dateMinArr[1], (int)$dateMinArr[2], (int)$dateDayArr[1], (int)$dateDayArr[2], (int)$dateDayArr[0]);
	}

	public static function extension_supported($extension) {
		self::init();
		if (isset(self::$whoisData[$extension])) {
			return 1;
		} else {
			return 0;
		}
	}

	public static function get_expiration($extension, $data) {
		self::init();

		$replaced = null;
		if ( isset( DomainCheckWhoisData::$whoisData[$extension]['expires'] ) ) {
			$replaced = str_replace('[[domain_check]]', '', DomainCheckWhoisData::$whoisData[$extension]['expires']);
			$replaced = trim($replaced);
		}

		//best guess is standard whois format...
		if ( strpos( $data, 'Registry Expiry Date:' ) !== false ) {
			$replaced = 'Registry Expiry Date:';
			DomainCheckWhoisData::$whoisData[$extension]['expires_func'] = 'whois';
		}

		$whois_arr = explode("\n", $data);
		foreach ($whois_arr as $whois_arr_idx => $whois_arr_line) {
			if ($replaced && strpos($whois_arr_line, $replaced) !== false) {
				$expiry_date = trim(str_replace($replaced, '', $whois_arr_line));
				$extension_func = $extension;
				if (isset(DomainCheckWhoisData::$whoisData[$extension]['expires_func'])) {
					$extension_func = DomainCheckWhoisData::$whoisData[$extension]['expires_func'];
				}
				if (method_exists('DomainCheckWhoisData', 'ext_' . strtolower(str_replace('.', '_', $extension_func)) . '_expires')) {
					return call_user_func_array(
						'DomainCheckWhoisData' . '::' . 'ext_' . strtolower(str_replace('.', '_', $extension_func)) . '_expires',
						array(
							$expiry_date
						)
					);
				}
			}
		}

		return 0;
	}

	public static function get_status($extension, $data) {

		self::init();
		if (!$data) {
			return 1;
		}

		$available = 'not found';
		if (isset(self::$whoisData[$extension]['available'])  && self::$whoisData[$extension]['available']) {
			$available = strtolower(self::$whoisData[$extension]['available']);
		}

		$data = strtolower($data);
		$res = mb_strpos($data, $available);

		if (mb_strpos($data, $available) !== false) {
			return 0;
		} else {
			return 1;
		}
	}

	public static function get_data() {
		self::init();
		return self::$whoisData;
	}

	public static function get_registrar($extension, $data) {
		self::init();

		$ret = null;

		$replaced = null;

		if ( isset( DomainCheckWhoisData::$whoisData[$extension]['registrar_check'] ) ) {
			$replaced = str_replace('[[domain_check]]', '', DomainCheckWhoisData::$whoisData[$extension]['registrar_check']);
			$replaced = trim($replaced);
		}

		//best guess is standard whois format...
		if ( strpos( $data, 'Sponsoring Registrar IANA ID:' ) !== false && $replaced === null ) {
			$replaced = 'Sponsoring Registrar IANA ID:';
		}
		if ( strpos( $data, 'Registrar IANA ID:' ) !== false && $replaced === null ) {
			$replaced = 'Registrar IANA ID:';
		}

		$whois_arr = explode("\n", $data);
		foreach ( $whois_arr as $whois_arr_idx => $whois_arr_line ) {
			if ( $replaced && strpos($whois_arr_line, $replaced) !== false ) {
				$ret = trim( str_replace( $replaced, '', $whois_arr_line ) );
				break;
			}
		}

		return $ret;
	}

	public static function get_registrar_name( $id ) {
		self::init();

		if ( isset( self::$registrarData[$id] ) ) {
			return self::$registrarData[$id]['Registrar Name'];
		}

		return null;
	}

	public static function get_registrar_link( $id ) {

	}


	public static function get_nameserver($extension, $data) {
		$ret = null;

		$replaced = null;

		if ( isset( DomainCheckWhoisData::$whoisData[$extension]['nameserver'] ) ) {
			$replaced = str_replace('[[domain_check]]', '', DomainCheckWhoisData::$whoisData[$extension]['nameserver']);
			$replaced = trim($replaced);
		}

		//best guess is standard whois format...
		if ( strpos( $data, 'Name Server:' ) !== false ) {
			$replaced = 'Name Server:';
		}

		$whois_arr = explode("\n", $data);
		foreach ( $whois_arr as $whois_arr_idx => $whois_arr_line ) {
			if ( $replaced && strpos($whois_arr_line, $replaced) !== false ) {
				$ret = trim( str_replace( $replaced, '', $whois_arr_line ) );
				break;
			}
		}

		return $ret;
	}

	public static function init() {
		if (!self::$class_init) {

			self::xml_import(); //imports domain extension XML data file

			self::json_import(); //imports domain extension data file

			self::json_import_registrar(); //imports registrar ID file

			ksort(self::$whoisData);

			self::$class_init = true;
		}
	}

	public static function xml_import() {

		if (!self::$whoisDataXml) {
			self::$whoisDataXml = simplexml_load_file(dirname(__FILE__) . '/../db/whois-server-list.xml');
		}

		foreach (self::$whoisDataXml as $domain_xml_obj) {
			$name = null;
			$whois = null;
			$country_code = null;
			$registrar = null;
			$whois_server = null;
			if (
				count($domain_xml_obj->domain) ||
				isset($domain_xml_obj->registrationService) ||
				isset($domain_xml_obj->state) ||
				isset($domain_xml_obj->countryCode)
			) {
				foreach ( $domain_xml_obj->attributes() as $domain_attr_idx => $domain_attr) {
					//echo 'checking ' . substr($domain, $domain_len - strlen($domain_attr) - 1, strlen($domain_attr)) . ' ';
					if ( $domain_attr_idx == 'name' ) {

						$whois_server = $domain_xml_obj->whoisServer;
						$extension = strtolower(trim($domain_attr));
						if ($whois_server && $whois_server->attributes()) {
							foreach ($whois_server->attributes() as $whois_server_attr_idx => $whois_server_attr) {
								if ($whois_server_attr_idx == 'host') {
									if ( !isset( self::$whoisData[$extension] ) ) {
										self::$whoisData[$extension] = array();
									}
									if ( !isset(self::$whoisData[$extension]['whois']) ) {
										self::$whoisData[$extension]['whois'] = $whois_server_attr;
									}
									if ( $whois_server !== null && isset( $whois_server->availablePattern ) && $whois_server->availablePattern ) {
										//if we have pattern for a server prefer that server for lookups
										if ( !isset(self::$whoisData[$extension]['available']) || !self::$whoisData[$extension]['available'] ) {
											$found_pattern = $whois_server->availablePattern;
											$found_pattern = str_replace('\Q', '', $found_pattern);
											$found_pattern = str_replace('\E', '', $found_pattern);
											self::$whoisData[$extension]['available'] = $found_pattern;
										}
									}
									if ( $whois_server !== null && isset( $whois_server->errorPattern ) && $whois_server->errorPattern ) {
										//if we have pattern for a server prefer that server for lookups
										$found_pattern = $whois_server->errorPattern;
										$found_pattern = str_replace('\Q', '', $found_pattern);
										$found_pattern = str_replace('\E', '', $found_pattern);
										self::$whoisData[$extension]['error'] = $found_pattern;
									}
								}
							}
						}
					}
				}
				foreach ( $domain_xml_obj->domain as $inner_domain_obj ) {

					//filter garbage
					if ( $inner_domain_obj->source == 'PSL' && !isset( $inner_domain_obj->whoisServer ) ) {
						continue;
					}

					//set WHOIS
					$whois_server = $domain_xml_obj->whoisServer;
					if ( isset($inner_domain_obj->whoisServer) ) {
						$whois_server = $inner_domain_obj->whoisServer;
					}

					foreach ( $inner_domain_obj->attributes() as $domain_attr_idx => $domain_attr ) {
						if ( $domain_attr_idx == 'name' ) {
							$extension = strtolower(trim($domain_attr));
							if ($whois_server && $whois_server->attributes()) {
								foreach ($whois_server->attributes() as $whois_server_attr_idx => $whois_server_attr) {
									if ($whois_server_attr_idx == 'host') {
										if ( !isset( self::$whoisData[$extension] ) ) {
											self::$whoisData[$extension] = array();
										}
										if ( !isset(self::$whoisData[$extension]['whois']) ) {
											self::$whoisData[$extension]['whois'] = $whois_server_attr;
										}
										if ( $whois_server !== null && isset( $whois_server->availablePattern ) && $whois_server->availablePattern ) {
											if ( !isset(self::$whoisData[$extension]['available']) || !self::$whoisData[$extension]['available'] ) {
												$found_pattern = $whois_server->availablePattern;
												$found_pattern = str_replace('\Q', '', $found_pattern);
												$found_pattern = str_replace('\E', '', $found_pattern);
												$found_pattern = trim($found_pattern);
												self::$whoisData[$extension]['available'] = $found_pattern;
											}
										}
										if ( $whois_server !== null && isset( $whois_server->errorPattern ) && $whois_server->errorPattern ) {
											//if we have pattern for a server prefer that server for lookups
											$found_pattern = $whois_server->errorPattern;
											$found_pattern = str_replace('\Q', '', $found_pattern);
											$found_pattern = str_replace('\E', '', $found_pattern);
											self::$whoisData[$extension]['error'] = $found_pattern;
										}
									}
								}
							}

						}
					}
				}
			}
		}
	}

	public static function json_import() {
		if (is_file(dirname(__FILE__) . '/../db/whois.formats.json')) {
			ob_start();
			include (dirname(__FILE__) . '/../db/whois.formats.json');
			$whois_data = ob_get_contents();
			ob_end_clean();
			$whois_data = json_decode($whois_data, true);
			if ($whois_data && is_array($whois_data)) {
				foreach ($whois_data as $extension => $extension_data) {
					if ( !isset( self::$whoisData[$extension] ) ) {
						self::$whoisData[$extension] = array();
					}
					//available
					if (
						isset($extension_data['available'])
						&& $extension_data['available']
						&& (
							!isset( self::$whoisData[$extension]['available'] )
							|| !self::$whoisData[$extension]['available']
						)
					) {
						self::$whoisData[$extension]['available'] = $extension_data['available'];
					}
					//expires
					if (
						isset($extension_data['expires'])
						&& $extension_data['expires']
						&& (
							!isset( self::$whoisData[$extension]['expires'] )
							|| !self::$whoisData[$extension]['expires']
						)
					) {
						self::$whoisData[$extension]['expires'] = $extension_data['expires'];
					}
					//self::$whoisData = array_merge($whois_data, self::$whoisData);
				}
			}
		}
	}

	public static function json_import_registrar() {
		if ( is_file( dirname( __FILE__ ) . '/../db/registrar.json' ) ) {
			ob_start();
			include( dirname( __FILE__ ) . '/../db/registrar.json' );
			$data = ob_get_contents();
			ob_end_clean();
			$data = json_decode( $data, true);
			if ( $data && is_array( $data ) ) {
				//TODO - just convert the data file
				foreach ( $data as $data_data_idx => $data_data ) {
					$data_data['id'] = $data_data['ID'];
					unset($data_data['ID']);
					self::$registrarData[$data_data['id']] = $data_data;
				}
			}
		}
	}

}