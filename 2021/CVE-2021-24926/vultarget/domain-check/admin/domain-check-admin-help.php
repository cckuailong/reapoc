<?php

class DomainCheckAdminHelp {

	public static $current_page = null;

	public static function admin_header() {
		$nav_pages = array(
			array(
				'domain-check-help',
				'General Help',
				'266-question',
				'help_general'
			),
			array(
				'domain-check-help',
				'Domain Extension List',
				'266-question',
				'help_extension_list'
			),
			array(
				'domain-check-help',
				'Email Help',
				'266-question',
				'help_email'
			),
			array(
				'domain-check-help',
				'Advanced',
				'266-question',
				'help_advanced'
			),
		);
		DomainCheckAdminHeader::admin_header_nav(
			$nav_pages,
			self::$current_page,
			'Help'
		);
	}

	public static function help() {
		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/266-question.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				<span class="hidden-mobile">Domain Check - </span>Help
			</h2>
			<?php
			DomainCheckAdminHeader::admin_header(true, null, 'domain-check-help');
			?>

			<?php
			if (isset($_GET['domain-check-page'])) {
				self::$current_page = $_GET['domain-check-page'];
			} else {
				self::$current_page = 'help_general';
			}
			self::admin_header();

			$do_return = false;
			if (self::$current_page) {
				switch (self::$current_page) {
					case 'help_advanced':
						self::help_advanced();
						$do_return = true;
						break;
					case 'help_extension_list':
						self::help_extension_list();
						$do_return = true;
						break;
					case 'help_email':
						self::help_email();
						$do_return = true;
						break;
					case 'help_general':
					default:
						break;
				}
			}

			if ( $do_return ) {
				echo '</div>';
				self::admin_header();
				DomainCheckAdminHeader::admin_header_nav();
				DomainCheckAdminHeader::footer();
				return;
			}

			$test_domains = array(
				'domaincheck' . time(),
				'radio',
				'shopping',
			);

			?>
			<div class="setting-div">
				<h3>
					<a href="admin.php?page=domain-check">
						<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						Domains / Dashboard
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_dashboard'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-your-domains">
						<img src="<?php echo plugins_url('/images/icons/color/flag.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-blue">
						Your Domains
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_your_domains'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-search">
						<img src="<?php echo plugins_url('/images/icons/color/magnifying-glass.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						Domain Search
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_domain_search'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-watch">
						<img src="<?php echo plugins_url('/images/icons/color/207-eye.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						Domain Watch
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_domain_watch'); ?>
				</p>
			</div>
			<div class="setting-div">
				<h3>
					<a href="admin.php?page=domain-check-ssl-check">
						<img src="<?php echo plugins_url('/images/icons/color/lock-locked-yellow.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-update-nag">
						SSL Check
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_ssl_check'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-ssl-watch">
						<img src="<?php echo plugins_url('/images/icons/color/bell.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						SSL Expiration Alerts
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_ssl_watch'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-import-export">
						<img src="<?php echo plugins_url('/images/icons/color/data-transfer-upload.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						<img src="<?php echo plugins_url('/images/icons/color/data-transfer-download.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						Import / Export
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_import_export'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-settings">
						<img src="<?php echo plugins_url('/images/icons/color/cog.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
						Settings
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_settings'); ?>
				</p>
				<h3>
					<a href="admin.php?page=domain-check-coupons">
						<img src="<?php echo plugins_url('/images/icons/color/055-price-tags.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-updated">
						Coupons &amp; Deals
					</a>
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_coupons'); ?>
				</p>
			</div>
			<div class="setting-div">
				<h3>
					<img src="<?php echo plugins_url('/images/icons/color/266-question.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
					FAQ
				</h3>
				<p class="p">
					<?php echo DomainCheckHelp::get_help('page_faq'); ?>
				</p>
			</div>
			<?php
			self::admin_header();
			DomainCheckAdminHeader::admin_header_nav();
			DomainCheckAdminHeader::footer();
			?>
		</div>
		<?php
	}

	public static function help_advanced() {
	?>
	<div class="setting-div">
		<h3>Versions</h3>
		<p>
			Version: <?php echo DomainCheck::PLUGIN_VERSION; ?><br>
			Version DB: <?php echo get_option('domain_check_version'); ?>
		</p>
		<h3>Compatibility Checks</h3>
		<p>
			<h4>PHP Compatibility Checks</h4>
			PHP Version: Compatible<br>
			<?php
			$func_arr = array(
				'curl_init',
				'fsockopen',
				'socket_set_timeout',
				'stream_context_create',
				'stream_context_get_params',
				'openssl_x509_parse'
			);
			foreach ( $func_arr as $func_arr_idx => $func_name ) {
				$val = 'Yes';
				if ( !function_exists( $func_name ) || !is_callable( $func_name ) ) {
					$val = 'No';
				}
				?>
				<?php echo $func_name; ?>(): <?php echo $val; ?><br>
				<?php
			}
			?>
		</p>
		<h3>Cron Info</h3>
		<p>
			Current Server Time: <?php echo date("F jS, Y l h:i:s A", time() ); ?><br><br>
		<?php
			$cron_function_exists = 'No';
			$cron_data = array();
			if ( function_exists( '_get_cron_array' ) ) {
				$cron_function_exists = 'Yes';
				$cron_data = _get_cron_array();
				$cron_list = array(
					'domain_check_'
				);
				foreach ( $cron_data as $cron_data_timestamp => $cron_data_items ) {
					foreach ( $cron_data_items as $cron_data_items_idx => $cron_data_item) {
						//echo $cron_data_items_idx . '<br>';
						if ( strpos( $cron_data_items_idx, 'domain_check_' ) === 0 ) {
							$cron_name = ucwords( str_replace( '_', ' ', $cron_data_items_idx) );
							$cron_schedule = null;
							$count = 0;
							foreach ( $cron_data_item as $cron_data_item_hash => $cron_data_item_hash_data) {
								if ( $count ) {
									break;
								}
								$cron_schedule = $cron_data_item_hash_data['schedule'];
								$count++;
							}
							echo $cron_name . ': ' . ucwords( $cron_schedule ) . '<br>' . "\n";
							echo 'Next Run: '. date("F jS, Y l h:i:s A", wp_next_scheduled( $cron_data_items_idx ) ) . "<br><br>" . "\n";

						}
					}
				}
			}
		?>
		</p>
		<?php

		$pro_directory_exists = 'No';
		$pro_class_exists = 'No';
		$pro_plugin_active = 'No';
		$pro_version = 'None';
		if ( file_exists( dirname( __FILE__ ) . '/../../domain-check-pro' ) || file_exists( dirname( __FILE__ ) . '/../../plugin-pro' ) ) {
			$pro_directory_exists = 'Yes';
		}
		if ( class_exists( 'DomainCheckPro' ) ) {
			$pro_class_exists = 'Yes';
		}
		if ( is_plugin_active( 'domain-check-pro/domain-check-pro.php') )  {
			$pro_plugin_active = 'Yes';
		}

		if ( $pro_class_exists !== 'No' ) {
			$pro_version = DomainCheckPro::PLUGIN_VERSION;
		}
		?>
	</div>
	<div class="setting-div">
		<h3>Domain Check PRO</h3>
		<p>
			PRO Plugin Directory Exists: <?php echo $pro_directory_exists; ?><br>
			PRO Plugin Class Exists: <?php echo $pro_class_exists; ?><br>
			PRO Plugin Is Active: <?php echo $pro_plugin_active; ?><br>
			PRO Plugin Version: <?php echo $pro_version; ?><br>
		</p>
	</div>
		<script type="text/javascript">

			var data_migration_active = {};
			var data_migration_total = {};
			var data_migration_page = {};
			var data_migration_results = {};
			var data_migration_timer = {};

			function data_migration_start( migration_type ) {

			}

			function data_migration_start_callback( res ) {

			}

			function data_migration_confirm( message ) {

			}

			function data_migration_confirm_callback( res ) {

			}

			function data_migration_process_page( migration_type, page ) {

			}

			function data_migration_process_page_callback( res ) {

			}

			function data_migration_complete( migration_type ) {

			}

			function data_migration_error( message ) {

			}

			function data_migration_ui_update( migration_type ) {

			}

		</script>
	<!--div class="setting-div">
		<h3>Data Migration</h3>
		<p>
			<span style="color: #FF0000;"><strong>WARNING!!!</strong></span> - Backup your database before attempting any data migrations. Running this on medium or large installations may cause process timeouts, failed PHP processeses, database strain, database corruption, or other server related issues. If you are unsure of the implications of this please consult a developer or programmer familiar with your installation.
			<br><br>
			This loops through every domain or SSL entry within your database and attempts to update, upgrade, and sanitize any new, out of date, missing, or corrupted data fields from previous installations. This process does not do WHOIS lookups or SSL lookups.
			<br><br>
			<strong>Example Use:</strong>
			<br>
			Domain Check installations before version 1.0.15 did not store the domain extension, registrar, or nameserver as separate columns in the database. Upgrading old Domain Check installations to 1.0.15 or higher shows all extension, registrar, and nameservers as blank fields. Running the Domain Data Migration after upgrading fills in those blank fields with the correct extension, registrar, and nameserver for each domain.
			<h3>Domain Data Migration</h3>
			Migration Updates:<br>
			Version 1.0.15 - Creation of extension, registrar, and nameserver for domains.
			<br><br>
			<input type="button" class="button btn" value="Start Domain Data Migration" onclick="data_migration_start( 'domain' );" />
			<br><br>
			<h3>SSL Data Migration</h3>
			<input type="button" class="button btn" value="Start SSL Data Migration" onclick="data_migration_start( 'ssl' );" />
			<br><br>
		</p>
	</div-->
	<?php
	}

	public static function help_extension_list() {
		$extensions = DomainCheckWhois::getextensions();

		?>
		<div style="width: 90%; background-color: #ffffff; clear: both; padding: 10px; margin: 10px;">
			<h3>Domain Extension List</h3>
			<p class="p">
				Here is the full list of domain extensions recognized by this plugin. Not all of these extensions are working this is simply the list of current know extensions! Remember that domain extensions are also know as top level domains, or TLDs. This is a list of all the top level domains (TLDs) that are currently recognized and that have some known registrar and WHOIS service.
			</p>
			<strong><?php echo count($extensions); ?> Total Extensions</strong>
			<div style="clear: both;"></div>
			<br>
			<?php

			foreach ($extensions as $extension => $whois_data) {
				?>
				<div style="display: inline-block; width: 20%;">

					<?php
					//if (isset($_GET['show_support'])) {
					$available_color = '#CCCCCC';
					$expires_color = '#CCCCCC';
					if (isset($whois_data['available']) && $whois_data['available']) {
						//$available_color = '#ffba00';
						$available_color = '#00AA00';
						$found_available = true;
					}
					if (isset($whois_data['expires']) && $whois_data['expires']) {
						//$expires_color = '#ffba00';
						$expires_color = '#00AA00';
						$found_expire = true;
					}
					if ($available_color != '#CCCCCC' && $expires_color != '#CCCCCC') {
						$available_color = '#00AA00';
						$expires_color = '#00AA00';
					}
					?>
					<div style="display: inline-block; margin-right: 5px; width: 14px; height: 14px; background-color: <?php echo $available_color; ?>;" alt="Availability Checks Supported" title="Availability Checks Supported"></div>
					<div style="display: inline-block; margin-right: 5px; width: 14px; height: 14px; background-color: <?php echo $expires_color; ?>;" alt="Expiration Date Supported" title="Expiration Dates Supported"></div>
						<?php
			//}
			?>
					<strong>
						.<?php echo $extension; ?>
					</strong>
				</div>
				<?php
			}
			?>
			</div>
		<?php
	}

	public static function help_email() {
		?>
		<script type="text/javascript">
			function email_test(setting_id) {
				var data_obj = {};
				switch (setting_id) {
					case 'email_test':
						data_obj = {
							action: "settings",
							method: "email_test",
							email_address: document.getElementById('email-test-email-address').value
						};
						break;
				}
				domain_check_ajax_call(
					data_obj,
					email_test_callback
				);
			}

			function email_test_callback(data) {
				if (!data.hasOwnProperty('error')) {
					jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
				} else {
					jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.error + '</p></div>');
				}
			}
		</script>
		<div class="setting-div">
			<h3>Email Address Test</h3>
			<p>
				This tool will send a sample Domain Check email to any email address from <strong><?php echo get_option('admin_email'); ?></strong>. If you do not get the email check the spam folder and mark the email as Not Spam. <strong>Make sure the email address is set up to accept all emails from <?php echo get_option('admin_email'); ?></strong>.
			</p>
			<div>
				<input type="text" id="email-test-email-address" class="domain-check-text-input" style="width: 100%;">
				<br><br>
				<a href="#" class="button-primary" onclick="email_test('email_test');">Send Test Email</a>
			</div>

		</div>
		<?php
	}
}