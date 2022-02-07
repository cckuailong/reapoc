<?php


class DomainCheckSearch {

	public static function domain_search($domain, $use_cache = false, $force_owned = false, $force_watch = false, $ajax = false) {
		global $wpdb;

		//add .com to any search to help out
		if ( strpos( $domain, '.' ) === false ) {
			$domain = $domain . '.com';
		}

		$domain_data = DomainCheckWhois::validdomain($domain);

		if ($domain_data) {
			$domain_parse = $domain_data['domain'];
			$domain_root = $domain_data['domain'];
			$search = $domain_data['domain'];
			$domain_extension = $domain_data['domain_extension'];

			$fqdn = str_replace('.' . $domain_extension, '', $domain_parse);
			$fqdn = explode('.', $fqdn);
			$fqdn = $fqdn[(count($fqdn) - 1)] . '.' . $domain_extension;

			$domain_root = $fqdn;
			$search = $fqdn;

			//favorites
			$fqdn = str_replace('.' . $domain_extension, '', $domain_parse);
			$domain_extension_favorites = DomainCheckConfig::$options[DomainCheckConfig::OPTIONS_PREFIX . 'settings']['domain_extension_favorites'];
			if (function_exists('get_option')) {
				$tmp_domain_extension_favorites = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'domain_extension_favorites');
				if ($tmp_domain_extension_favorites && count($tmp_domain_extension_favorites)) {
					$domain_extension_favorites = $tmp_domain_extension_favorites;
				}
			}
			$message_options = array();
			foreach ($domain_extension_favorites as $domain_extension_favorite) {
				if ($domain_extension_favorite != $domain_extension) {
					$message_options['.' . $domain_extension_favorite] = 'admin.php?page=domain-check-search&domain_check_search='.$fqdn.'.'.$domain_extension_favorite;
				}
			}
			$message_options['WHOIS'] = 'admin.php?page=domain-check-profile&domain='.$search;
			$message_options['<img src="' . plugins_url('domain-check/images/icons/external-link.svg') .
										'" class="svg svg-h2 svg-fill-gray">'] = '//' . $search;


			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_url ="' . strtolower($search) . '"';
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );
			$can_cache = false;
			$in_db = false;
			if ( count ( $result ) ) {
				$in_db = true;
				$domain_result = array_pop($result);
				if ($domain_result['domain_expires'] > time() &&
					$domain_result['domain_last_check'] > (time() - (60 * 60 * 24 * 30))
				) {
					$can_cache = true;
				}
			}
			if (isset($_GET['cache'])) {
				$use_cache = (int)$_GET['cache'];
			}
			if ($can_cache && $use_cache) {
				//domain exists in DB...
				$new_data = array (
					'search_date' => time(),
				);
				if ($domain_result['status'] && $force_owned) {
					$new_data['status'] = 2;
					$domain_result['status'] = 2;
				}
				if ($force_watch) {
					$new_data['domain_watch'] = 1;
					$domain_result['domain_watch'] = 1;
				}
				$wpdb->update(
					DomainCheck::$db_prefix . '_domains',
					$new_data,
					array (
						'domain_url' => strtolower($search)
					)

				);

				if (!$domain_result['status']) {
					$message = 'Yes! <strong><a href="' . DomainCheckLinks::homepage($search) . '" target="_blank">' . $search . '</a></strong> is available!';
					if (class_exists('DomainCheckAdmin')) {
						DomainCheckAdmin::admin_notices_add($message, 'updated', $message_options, 'circle-check');
					}
					$ajax_response = DomainCheckAdmin::ajax_success(array('message' => $message, 'status' => 0, 'domain' => $search));
				} else {
					if ($domain_result['status'] == 2) {
						$message = 'Success! You own <strong><a href="admin.php?page=domain-check-profile&domain=' . $search . '">' . $search . '</a></strong>!';
						if (class_exists('DomainCheckAdmin')) {
							DomainCheckAdmin::admin_notices_add($message, 'owned', $message_options, 'flag');
						}
						$ajax_response = array('message' => $message, 'status' => 2, 'domain' => $search, 'domain' => $search);
					} else {
						$status = 1;
						$message = '<strong><a href="admin.php?page=domain-check-profile&domain=' . $search . '">' . $search . '</a></strong> <span class="hidden-mobile">is not available and </span>is taken.';
						if (class_exists('DomainCheckAdmin')) {
							DomainCheckAdmin::admin_notices_add($message, 'error', $message_options, 'ban');
						}
						$ajax_response = array('message' => $message, 'status' => 1, 'domain' => $search);
					}
				}

				if ($ajax) {
					DomainCheckAdmin::ajax_success($ajax_response);
				}
			} else {

				$dot = strpos($domain_root, '.');
				$sld = substr($domain_root, 0, $dot);
				$tld = substr($domain_root, $dot + 1);

				$whois = DomainCheckWhois::dolookup($domain_root);

				if (!isset($whois['error'])) {
					$ajax_response = null;
					$status = 0;
					$expires = 0;
					if ($whois['status'] == 0) {
						$message = 'Yes! <strong><a href="' . DomainCheckLinks::homepage($search) .'" target="_blank">' . $search . '</a></strong> is available!';
						if (class_exists('DomainCheckAdmin')) {
							DomainCheckAdmin::admin_notices_add(
								$message,
								'updated',
								$message_options,
								'circle-check'
							);
						}
						$ajax_response = array('message' => $message, 'status' => 0, 'domain' => $search);
					} else {
						if (isset($message_options['.' . $domain_parse[count($domain_parse)-1]])) {
							unset($message_options['.' . $domain_parse[count($domain_parse)-1]]);
						}
						if (($in_db && isset($domain_result) && $domain_result['status'] == 2) || $force_owned) {
							$status = 2;
							$domain_result['status'] = 2;
							$message = 'Success! You own <strong><a href="admin.php?page=domain-check-profile&domain=' . $search . '">' . $search . '</a></strong>!';
							if (class_exists('DomainCheckAdmin')) {
								DomainCheckAdmin::admin_notices_add($message, 'owned', $message_options, 'flag');
							}
							$ajax_response = array('message' => $message, 'status' => 2, 'domain' => $search);
						} else {
							$status = 1;
							$message = '<strong><a href="admin.php?page=domain-check-profile&domain=' . $search . '">' . $search . '</a></strong> <span class="hidden-mobile">is not available and </span>is taken.';
							if (class_exists('DomainCheckAdmin')) {
								DomainCheckAdmin::admin_notices_add($message, 'error', $message_options, 'ban');
							}
							$ajax_response = array('message' => $message, 'status' => 1, 'domain' => $search);
						}
						$expires = $whois['domain_expires'];
					}

					//$sql = 'INSERT INTO wp_domain_check_domains VALUES (null, "' . $search . '", 0, ' . $status . ', '.time().', 0, '.$expires.', null, null)';
					if (!$in_db) {
						$valarr = array(
							'domain_id' => null,
							'domain_url' => $search,
							'domain_extension' => $whois['extension'],
							'user_id' => 0,
							'status' => $status,
							'date_added' => time(),
							'search_date' => time(),
							'domain_created' => 0,
							'domain_last_check' => time(),
							'domain_next_check' => 0,
							'domain_expires' => $expires,
							'registrar' => $whois['registrar'],
							'nameserver' => $whois['nameserver'],
							'domain_settings' => null,
							'cache' => gzcompress(json_encode($whois)),
						);
						if ($force_watch) {
							$valarr['domain_watch'] = 1;
						}

						$wpdb->insert(
							DomainCheck::$db_prefix . '_domains',
							$valarr
						);
					} else {
						if ($domain_result['status'] == 2) {
							$status = 2;
						}
						$valarr = array(
							'status' => $status,
							'search_date' => time(),
							'domain_created' => 0,
							'domain_last_check' => time(),
							'registrar' => $whois['registrar'],
							'nameserver' => $whois['nameserver'],
							'domain_extension' => $whois['extension'],
							'domain_expires' => $expires,
							'cache' => gzcompress(json_encode($whois)),
						);
						if ($force_watch) {
							$valarr['domain_watch'] = 1;
						}
						$wpdb->update(
							DomainCheck::$db_prefix . '_domains',
							$valarr,
							array (
								'domain_url' => $search
							)
						);
					}

					if ($ajax) {
						if (class_exists('DomainCheckAdmin')) {
							DomainCheckAdmin::ajax_success($ajax_response);
						}
					}
				} else {
					if (class_exists('DomainCheckAdmin')) {
						DomainCheckAdmin::admin_notices_add($whois['error'], 'error', null, 'circle-x');
					}
					if ($ajax) {
						if (class_exists('DomainCheckAdmin')) {
							DomainCheckAdmin::ajax_error(strip_tags($whois['error']));
						}
					}
				}
			}
		} else {
			if (class_exists('DomainCheckAdmin')) {
				DomainCheckAdmin::admin_notices_add('<strong>' . htmlentities($domain) . '</strong> is not a valid domain.', 'error', null, 'circle-x');
			}
			if ($ajax) {
				if (class_exists('DomainCheckAdmin')) {
					DomainCheckAdmin::ajax_error(strip_tags($domain) . ' is not a valid domain.');
				}
			}
		}
	}

	public static function ssl_search($domain, $force_watch = false, $ajax = false) {
		global $wpdb;

		$exists_in_db = false;
		$use_cache = false;
		$force_return = false;
		$domain_result = array();
		if ($force_watch) {
			$force_watch = 1;
		} else {
			$force_watch = 0;
		}
		$ajax_response = array();

		$search = parse_url(strtolower(trim($domain)));

		if (isset($search['path']) && $search['path'] != '/') {
			$search = $search['path'];
		} else if (isset($search['host'])) {
			$search = $search['host'];
		} else {
			DomainCheckAdmin::admin_notices_add(
				'<strong>' . $search . '</strong> is not a URL.',
				'error',
				null,
				'145-unlocked'
			);
			return false;
		}


		$search = preg_replace("/[^a-z0-9.-]+/i", '', $search);

		$domain_data = DomainCheckWhois::validdomain($search);

		if (!$domain_data) {
			DomainCheckAdmin::admin_notices_add(
				'<strong>' . $search . '</strong> is not a valid domain.',
				'error',
				null,
				'145-unlocked'
			);
			return false;
		}

		$valarr = array(
			'ssl_domain_id' => null,
			'domain_id' => null,
			'domain_url' => $search,
			'user_id' => 0,
			'status' => 0,
			'date_added' => time(),
			'search_date' => time(),
			'domain_last_check' => time(),
			'domain_next_check' => 0,
			'domain_expires' => 0,
			'domain_watch' => $force_watch,
			'domain_settings' => null,
			'cache' => null,
		);

		if (strpos($search, 'http') === false) {
			$search = 'http://' . $search;
		}

		$original_parse = parse_url($search, PHP_URL_HOST);
		$search = $original_parse;

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_url ="' . strtolower($search) . '"';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		if ( count ( $result ) ) {
			$domain_result = array_pop($result);
			$exists_in_db = $domain_result['ssl_domain_id'];
			$valarr = array(
				'date_added' => $domain_result['date_added'],
				'search_date' => time(),
				'domain_last_check' => time(),
				'domain_watch' => $domain_result['domain_watch']
			);
			if ($force_watch) {
				$valarr['domain_watch'] = 1;
			}
		}

		$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
		try {
			//fear leads to anger, anger leads to hate, hate leads to suppression, supression leads to the Dark Side - Yoda
			$read = @stream_socket_client("ssl://".$original_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
		} catch (Exception $e) {
			$message = 'No SSL certificate found for <strong>' . $search . '</strong>';
			if (class_exists('DomainCheckAdmin')) {
				DomainCheckAdmin::admin_notices_add($message, 'error', null, '145-unlocked');
			}
			$ajax_response = array('message' => $message, 'status' => 0, 'domain' => $search);
			$force_return = true;
			$valarr['status'] = 0;
			//return;
		}

		if (!$read && !$force_return) {
			$message = 'Unable to read SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong>';
			if (class_exists('DomainCheckAdmin')) {
				DomainCheckAdmin::admin_notices_add($message, 'error', null, '145-unlocked');
			}
			$ajax_response = array('message' => $message, 'status' => 0, 'domain' => $search);
			$force_return = true;
			$valarr['status'] = 0;
			//return;
		}

		if (!$force_return) {
			$cert = stream_context_get_params($read);
			$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
		}

		if (!isset($certinfo['validFrom_time_t']) && !$force_return) {
			$message = 'SSL<span class="hidden-mobile"> Certificate</span> found but no expiration date<span class="hidden-mobile"> given</span> for <strong>' . $search . '</strong>';
			if (class_exists('DomainCheckAdmin')) {
				DomainCheckAdmin::admin_notices_add($message, 'error', null, '145-unlocked');
			}
			$ajax_response = array('message' => $message, 'status' => 0, 'domain' => $search);
			$force_return = true;
			//return;
		}

		if (!$force_return) {


			$valarr['cache'] = gzcompress(json_encode(self::utf8Array($certinfo)));
			if ($certinfo['validFrom_time_t'] > time() || $certinfo['validTo_time_t'] < time() ) {
				$valarr['domain_expires'] = $certinfo['validTo_time_t'];
				$valarr['status'] = 0;
				$message = 'SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong> is not valid. Expired ' . date('m/d/Y', $certinfo['validTo_time_t']);
				if (class_exists('DomainCheckAdmin')) {
					DomainCheckAdmin::admin_notices_add($message, 'error', null, '145-unlocked');
				}
				$ajax_response = array('message' => $message, 'status' => 0, 'domain' => $search);
			} else {
				$valarr['domain_expires'] = $certinfo['validTo_time_t'];
				$valarr['status'] = 1;
				$message = 'Yes! SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong> is valid!';
				if (class_exists('DomainCheckAdmin')) {
					DomainCheckAdmin::admin_notices_add($message, 'updated', null, '144-lock');
				}
				$ajax_response = array('message' => $message, 'status' => 1, 'domain' => $search);
			}
		}

		if ($exists_in_db) {
			$wpdb->update(
				DomainCheck::$db_prefix . '_ssl',
				$valarr,
				array (
					'domain_url' => strtolower($search)
				)

			);
		} else {
			$wpdb->insert(
				DomainCheck::$db_prefix . '_ssl',
				$valarr
			);
		}

		if (class_exists('DomainCheckAdmin') && $ajax) {
			if ($valarr['status'] || !isset($valarr['status'])) {
				DomainCheckAdmin::ajax_success($ajax_response);
			} else {
				$ajax_response['domain'] = $search;
				DomainCheckAdmin::ajax_error($ajax_response);
			}
		}
	}

	public static function utf8Array($arr) {
		foreach ($arr as $arr_idx => $arr_val) {
			if ( is_array( $arr_val ) ) {
				$arr[$arr_idx] = self::utf8Array( $arr_val );
			} else {
				$arr[$arr_idx] = utf8_encode($arr_val);
			}
		}
		return $arr;
	}
}