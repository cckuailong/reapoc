<?php

class DomainCheckAdminSslWatch {

	public static $domains_obj;

	public static function ssl_watch() {
		global $wpdb;
		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/bell.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-update-gray">
				<span class="hidden-mobile">Domain Check - </span>SSL Expiration Alerts
			</h2>
			<?php
			DomainCheckAdminHeader::admin_header();
			DomainCheckAdminSslWatch::ssl_search_box();
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form action="" method="post">
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

	public static function ssl_watch_email_add($domain, $watch_email_content) {
		global $wpdb;

		$new_watch_email_content = $watch_email_content;
		$new_watch_email_content = strtolower($new_watch_email_content);
		$new_watch_email_content = str_replace(',', ' ', $new_watch_email_content);
		$new_watch_email_content = str_replace(' ', "\n", $new_watch_email_content);
		$new_watch_email_content = explode("\n", $new_watch_email_content);
		$new_watch_emails = array();
		foreach ($new_watch_email_content as $new_watch_email) {
			if ($new_watch_email != '' && $new_watch_email) {
				$new_watch_emails[] = $new_watch_email;
			}
		}
		sort($new_watch_emails);

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_url ="' . strtolower($domain) . '"';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		$use_cache = false;
		if ( count ( $result ) ) {
			$domain_result = array_pop($result);
			$domain_result['cache'] = ($domain_result['cache'] ? json_decode(gzuncompress($domain_result['cache']), true) : null);
			$domain_result['domain_settings'] = ($domain_result['domain_settings'] ? json_decode(gzuncompress($domain_result['domain_settings']), true) : null);
			if (!is_array($domain_result['domain_settings'])) {
				$domain_result['domain_settings'] = array();
			}
			$domain_result['domain_settings']['watch_emails'] = $new_watch_emails;
			$new_settings = array('domain_settings' => gzcompress(json_encode($domain_result['domain_settings'])));
			$wpdb->update(
				DomainCheck::$db_prefix . '_ssl',
				$new_settings,
				array (
					'domain_url' => strtolower($domain)
				)

			);
		}
	}

	/**
	 * Screen options
	 */
	public static function ssl_watch_screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'SSL Expiration Alerts',
			'default' => 100,
			'option'  => 'domains_per_page'
		);

		add_screen_option( $option, $args );

		self::$domains_obj = new DomainCheck_SSL_Watch_List();
	}

	public static function ssl_watch_start($domain) {
		global $wpdb;

		$domain = strtolower($domain);

		$wpdb->update(
			DomainCheck::$db_prefix . '_ssl',
			array(
				'domain_watch' => 1
			),
			array (
				'domain_url' => $domain
			)
		);

		DomainCheckAdmin::admin_notices_add('Started watching SSL expiration for <strong>' . $domain . '</strong>!', 'updated', null, '208-eye-plus');
	}

	public static function ssl_watch_stop($domain) {
		global $wpdb;

		$domain = strtolower($domain);

		$wpdb->update(
			DomainCheck::$db_prefix . '_ssl',
			array(
				'domain_watch' => 0
			),
			array (
				'domain_url' => $domain
			)
		);

		DomainCheckAdmin::admin_notices_add('Stopped watching SSL expiration for <strong>' . $domain . '</strong>!', 'error', null, '209-eye-minus');
	}

	public static function ssl_watch_trigger($domain, $ajax = 0) {
		//this function sucks because it has to do a select first
		global $wpdb;

		if (isset($_POST['domain'])) {
			$ajax = 1;
			$domain = strtolower($_POST['domain']);
		}

		$domain = strtolower($domain);

		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_url ="' . strtolower($domain) . '"';
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		if ( count ( $result ) ) {
			$result = array_pop($result);
			$new_status = $result['domain_watch'] ? 0 : 1;
			$wpdb->update(
				DomainCheck::$db_prefix . '_ssl',
				array(
					'domain_watch' => $new_status
				),
				array(
					'domain_url' => $domain
				)
			);

			$message_start = 'Started watching <strong>' . $domain . '</strong>!';
			$message_stop = 'Stopped watching <strong>' . $domain . '</strong>!';

			if (!$ajax) {
				if ($new_status) {
					DomainCheckAdmin::admin_notices_add($message_start, 'updated', null, '208-eye-plus');
				} else {
					DomainCheckAdmin::admin_notices_add($message_stop, 'error', null, '209-eye-minus');
				}
			} else {
				DomainCheckAdmin::ajax_success(
					$data = array(
						'watch' => $new_status,
						'message' => ($new_status ? $message_start : $message_stop),
						'domain' => $domain
					)
				);
			}
		}
	}

	public static function ssl_search_box( $dashboard = false ) {
		$css_class = 'domain-check-admin-search-input';
		if ( $dashboard ) {
			$css_class .= '-dashboard';
		}
		$css_class_button = $css_class . '-btn';
		?>
		<script type="text/javascript">
			function domain_check_ssl_watch_click(evt) {
				document.getElementById('domain-check-ssl-watch-box-form').submit();
			}
		</script>
		<form id="domain-check-ssl-watch-box-form" action="" method="GET">
			<input type="text" name="domain_check_ssl_watch" id="domain_check_ssl_watch" class="<?php echo $css_class; ?>">
			<input type="hidden" name="page" value="domain-check-ssl-watch">
			<?php if ( !$dashboard ) { ?>
			<div type="button" class="button domain-check-admin-search-input-btn" onclick="domain_check_ssl_watch_click();">
				<img src="<?php echo plugins_url('/images/icons/color/bell.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-update-nag">
				<div style="display: inline-block;">Add SSL Alert</div>
			</div>
			<?php } else { ?>
			<input type="submit" class="button <?php echo $css_class_button; ?>" value="Watch SSL" />
			<?php } ?>
		</form>
		<?php
	}

}