<?php

class DomainCheckAdminSslSearch {

	public static $domains_obj;

	public static function ssl_check_init() {
		global $wpdb;

		return DomainCheckSearch::ssl_search($_GET['domain_check_ssl_search']);


		$exists_in_db = false;
		$use_cache = false;
		$force_return = false;
		$domain_result = array();
		$search = parse_url(strtolower($_GET['domain_check_ssl_search']));
		$search = $search['path'];
		$search = preg_replace("/[^a-z0-9.-]+/i", '', $search);

		$valarr = array(
			'ssl_domain_id' => null,
			'domain_id' => null,
			'domain_url' => $_GET['domain_check_ssl_search'],
			'user_id' => 0,
			'status' => 0,
			'date_added' => time(),
			'search_date' => time(),
			'domain_last_check' => time(),
			'domain_next_check' => 0,
			'domain_expires' => 0,
			'domain_settings' => null,
			'cache' => null,
		);

		if (strpos($_GET['domain_check_ssl_search'], 'http') === false) {
			$_GET['domain_check_ssl_search'] = 'http://' . $_GET['domain_check_ssl_search'];
		}



		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_url ="' . strtolower($search) . '"';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		if ( count ( $result ) ) {
			$domain_result = array_pop($result);
			$exists_in_db = $domain_result['ssl_domain_id'];
			$valarr = array(
				'date_added' => $domain_result['date_added'],
				'search_date' => time(),
				'domain_last_check' => time(),
			);
		}

		$orignal_parse = parse_url($_GET['domain_check_ssl_search'], PHP_URL_HOST);
		$search = $orignal_parse;
		$get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
		try {
			//fear leads to anger, anger leads to hate, hate leads to suppression, supression leads to the Dark Side - Yoda
			$read = @stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
		} catch (Exception $e) {
			DomainCheckAdmin::admin_notices_add(
				'No SSL certificate found for <strong>' . $search . '</strong>',
				'error',
				null,
				'145-unlocked'
			);
			$force_return = true;
			//return;
		}

		if (!$read && !$force_return) {
			DomainCheckAdmin::admin_notices_add(
				'Unable to read SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong>',
				'error',
				null,
				'145-unlocked'
			);
			$force_return = true;
			//return;
		}

		if (!$read) {
			DomainCheckAdmin::admin_notices_add(
				'No SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong>',
				'error',
				null,
				'145-unlocked'
			);
			$force_return = true;
			//return;
		}

		if (!$force_return) {
			$cert = stream_context_get_params($read);
			$certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
		}

		if (!isset($certinfo['validFrom_time_t']) && !$force_return) {
			DomainCheckAdmin::admin_notices_add(
				'SSL<span class="hidden-mobile"> Certificate</span> found but no expiration date<span class="hidden-mobile"> given</span> for <strong>' . $search . '</strong>',
				'error',
				null,
				'145-unlocked'
			);
			$force_return = true;
			//return;
		}

		if (!$force_return) {
			$valarr['cache'] = gzcompress(json_encode($certinfo));
			if ($certinfo['validFrom_time_t'] > time() || $certinfo['validTo_time_t'] < time() ) {
				$valarr['domain_expires'] = $certinfo['validTo_time_t'];
				$valarr['status'] = 0;
				DomainCheckAdmin::admin_notices_add(
					'SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong> is not valid. Expires ' . date('m/d/Y', $certinfo['validTo_time_t']),
					'error',
					null,
					'145-unlocked'
				);
			} else {
				$valarr['domain_expires'] = $certinfo['validTo_time_t'];
				$valarr['status'] = 1;
				DomainCheckAdmin::admin_notices_add(
					'Yes! SSL<span class="hidden-mobile"> Certificate</span> for <strong>' . $search . '</strong> is valid!',
					'updated',
					null,
					'144-lock'
				);
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
	}

	public static function ssl_check() {
		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/lock-locked-yellow.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-update-nag">
				<span class="hidden-mobile">Domain Check - </span>SSL Check
			</h2>
			<?php
			DomainCheckAdminHeader::admin_header();
			self::ssl_search_box();
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								self::$domains_obj->prepare_items();
								self::$domains_obj->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<?php
			DomainCheckAdminHeader::admin_header_nav();
			DomainCheckAdminHeader::footer();
			?>
		</div>
		<?php
	}

	/**
	 * Screen options
	 */
	public static function ssl_check_screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'SSL Check',
			'default' => 100,
			'option'  => 'domains_per_page'
		);

		add_screen_option( $option, $args );

		self::$domains_obj = new DomainCheck_SSL_Search_List();
	}

	public static function ssl_delete_init() {
		global $wpdb;

		$domain = strtolower($_GET['domain_check_ssl_delete']);

		if (!isset($_GET['domain_check_ssl_delete_confirm'])) {
			$message = 'Are you sure you want to delete <strong> ' . $domain . ' </strong>? It will no longer be watched and may expire! This cannot be undone.';
			$message_options = array(
			'Delete' => '?page=domain-check-ssl-check&domain_check_ssl_delete=' . $domain . '&domain_check_ssl_delete_confirm=1',
			'Cancel' => '?page=domain-check-ssl-check'
		);
			DomainCheckAdmin::admin_notices_add($message, 'error', $message_options, '174-bin2');
		} else {
			$wpdb->delete(
				DomainCheck::$db_prefix . '_ssl',
				array(
					'domain_url' => $domain
				)
			);
			$message = 'Success! You deleted <strong>' . $domain . '</strong>!';
			DomainCheckAdmin::admin_notices_add($message, 'updated', null, '174-bin2');
		}
	}

	public static function ssl_search_box($dashboard = false) {
		$css_class = 'domain-check-admin-search-input';
		if ( $dashboard ) {
			$css_class .= '-dashboard';
		}
		$css_class_button = $css_class . '-btn';
		?>
		<script type="text/javascript">
			function domain_check_ssl_search_click(evt) {
				document.getElementById('domain-check-ssl-search-box-form').submit();
			}
		</script>
		<form id="domain-check-ssl-search-box-form" action="" method="GET">
			<input type="text" name="domain_check_ssl_search" id="domain_check_ssl_search" class="<?php echo $css_class; ?>">
			<input type="hidden" name="page" value="domain-check-ssl-check">
			<?php if ( !$dashboard ) { ?>
			<div type="button" class="button domain-check-admin-search-input-btn" onclick="domain_check_ssl_search_click();">
				<img src="<?php echo plugins_url('/images/icons/color/lock-locked-yellow.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-update-nag">
				<div style="display: inline-block;">Check SSL</div>
			</div>
			<?php } else { ?>
			<input type="submit" class="button <?php echo $css_class_button; ?>" value="Check SSL" />
			<?php } ?>
		</form>
		<?php
	}
	
}