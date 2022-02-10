
<div class="wrap">
	<h2><?php _e('Tutor Environment Status', 'tutor'); ?></h2>

	<?php
	$environment      = \TUTOR\Admin::get_environment_info();
	?>

	<table class="tutor_status_table widefat" cellspacing="0" id="status">
		<thead>
		<tr>
			<th colspan="3" data-export-label="WordPress Environment"><h2><?php esc_html_e( 'WordPress environment', 'tutor' ); ?></h2></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td data-export-label="Home URL"><?php esc_html_e( 'Home URL', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The homepage URL of your site.', 'tutor' ) );?></td>
			<td><?php echo esc_html( $environment['home_url'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Site URL"><?php esc_html_e( 'Site URL', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The root URL of your site.', 'tutor' ) );  ?></td>
			<td><?php echo esc_html( $environment['site_url'] ); ?></td>
		</tr>

		<tr>
			<td data-export-label="WP Version"><?php esc_html_e( 'WordPress version', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The version of WordPress installed on your site.', 'tutor' ) );  ?></td>
			<td>
				<?php
				$latest_version = get_transient( 'tutor_system_status_wp_version_check' );

				if ( false === $latest_version ) {
					$version_check = wp_remote_get( 'https://api.wordpress.org/core/version-check/1.7/' );
					$api_response  = json_decode( wp_remote_retrieve_body( $version_check ), true );

					if ( $api_response && isset( $api_response['offers'], $api_response['offers'][0], $api_response['offers'][0]['version'] ) ) {
						$latest_version = $api_response['offers'][0]['version'];
					} else {
						$latest_version = $environment['wp_version'];
					}
					set_transient( 'tutor_system_status_wp_version_check', $latest_version, DAY_IN_SECONDS );
				}

				if ( version_compare( $environment['wp_version'], $latest_version, '<' ) ) {
					/* Translators: %1$s: Current version, %2$s: New version */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - There is a newer version of WordPress available (%2$s)', 'tutor' ), esc_html( $environment['wp_version'] ), esc_html( $latest_version ) ) . '</mark>';
				} else {
					echo '<mark class="yes">' . esc_html( $environment['wp_version'] ) . '</mark>';
				}
				?>
			</td>
		</tr>

		<tr>
			<td data-export-label="Tutor Version"><?php esc_html_e( 'Tutor Version', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The version of tutor.', 'tutor' ) );  ?></td>
			<td><mark class="yes"><?php echo esc_html( $environment['version'] ); ?></mark></td>
		</tr>

		<tr>
			<td data-export-label="WP Multisite"><?php esc_html_e( 'WordPress multisite', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Whether or not you have WordPress Multisite enabled.', 'tutor' ) );  ?></td>
			<td><?php echo ( $environment['wp_multisite'] ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
		</tr>
		<tr>
			<td data-export-label="WP Memory Limit"><?php esc_html_e( 'WordPress memory limit', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( $environment['wp_memory_limit'] < 67108864 ) {
					/* Translators: %1$s: Memory limit, %2$s: Docs link. */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend setting memory to at least 64MB. See: %2$s', 'tutor' ), esc_html( size_format( $environment['wp_memory_limit'] ) ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'tutor' ) . '</a>' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . esc_html( size_format( $environment['wp_memory_limit'] ) ) . '</mark>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="WP Debug Mode"><?php esc_html_e( 'WordPress debug mode', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Displays whether or not WordPress is in Debug Mode.', 'tutor' ) );  ?></td>
			<td>
				<?php if ( $environment['wp_debug_mode'] ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="no">&ndash;</mark>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="WP Cron"><?php esc_html_e( 'WordPress cron', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Displays whether or not WP Cron Jobs are enabled.', 'tutor' ) );  ?></td>
			<td>
				<?php if ( $environment['wp_cron'] ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="no">&ndash;</mark>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Language"><?php esc_html_e( 'Language', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The current language used by WordPress. Default = English', 'tutor' ) );  ?></td>
			<td><?php echo esc_html( $environment['language'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="External object cache"><?php esc_html_e( 'External object cache', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Displays whether or not WordPress is using an external object cache.', 'tutor' ) ); ?></td>
			<td>
				<?php if ( $environment['external_object_cache'] ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="no">&ndash;</mark>
				<?php endif; ?>
			</td>
		</tr>

		</tbody>
	</table>


	<table class="tutor_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="3" data-export-label="Server Environment"><h2><?php esc_html_e( 'Server environment', 'tutor' ); ?></h2></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td data-export-label="Server Info"><?php esc_html_e( 'Server info', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Information about the web server that is currently hosting your site.', 'tutor' ) );  ?></td>
			<td><?php echo esc_html( $environment['server_info'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="PHP Version"><?php esc_html_e( 'PHP version', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The version of PHP installed on your hosting server.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( version_compare( $environment['php_version'], '7.2', '>=' ) ) {
					echo '<mark class="yes">' . esc_html( $environment['php_version'] ) . '</mark>';
				} else {
					$class       = 'error';

					if ( version_compare( $environment['php_version'], '5.6', '<' ) ) {
						$notice = '<span class="dashicons dashicons-warning"></span> ' . __( 'Tutor will run under this version of PHP, however, it has reached end of life. We recommend using PHP version 7.2 or above for greater performance and security.', 'tutor' );
					} elseif ( version_compare( $environment['php_version'], '7.2', '<' ) ) {
						$notice = __( 'We recommend using PHP version 7.2 or above for greater performance and security.', 'tutor' );
						$class  = 'recommendation';
					}

					echo '<mark class="' . esc_attr( $class ) . '">' . esc_html( $environment['php_version'] ) . ' - ' . wp_kses_post( $notice ) . '</mark>';
				}
				?>
			</td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP post max size', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The largest filesize that can be contained in one post.', 'tutor' ) );  ?></td>
				<td><?php echo esc_html( size_format( $environment['php_post_max_size'] ) ); ?></td>
			</tr>
			<tr>
				<td data-export-label="PHP Time Limit"><?php esc_html_e( 'PHP time limit', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'tutor' ) );  ?></td>
				<td><?php echo esc_html( $environment['php_max_execution_time'] ); ?></td>
			</tr>
			<tr>
				<td data-export-label="PHP Max Input Vars"><?php esc_html_e( 'PHP max input vars', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'tutor' ) );  ?></td>
				<td><?php echo esc_html( $environment['php_max_input_vars'] ); ?></td>
			</tr>
			<tr>
				<td data-export-label="cURL Version"><?php esc_html_e( 'cURL version', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The version of cURL installed on your server.', 'tutor' ) );  ?></td>
				<td><?php echo esc_html( $environment['curl_version'] ); ?></td>
			</tr>
			<tr>
				<td data-export-label="SUHOSIN Installed"><?php esc_html_e( 'SUHOSIN installed', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself. If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'tutor' ) );  ?></td>
				<td><?php echo $environment['suhosin_installed'] ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
			</tr>
		<?php endif; ?>

		<?php

		if ( $environment['mysql_version'] ) :
			?>
			<tr>
				<td data-export-label="MySQL Version"><?php esc_html_e( 'MySQL version', 'tutor' ); ?>:</td>
				<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The version of MySQL installed on your hosting server.', 'tutor' ) );  ?></td>
				<td>
					<?php
					if ( version_compare( $environment['mysql_version'], '5.6', '<' ) && ! strstr( $environment['mysql_version_string'], 'MariaDB' ) ) {
						/* Translators: %1$s: MySQL version, %2$s: Recommended MySQL version. */
						echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum MySQL version of 5.6. See: %2$s', 'tutor' ), esc_html( $environment['mysql_version_string'] ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . esc_html__( 'WordPress requirements', 'tutor' ) . '</a>' ) . '</mark>';
					} else {
						echo '<mark class="yes">' . esc_html( $environment['mysql_version_string'] ) . '</mark>';
					}
					?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td data-export-label="Max Upload Size"><?php esc_html_e( 'Max upload size', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The largest filesize that can be uploaded to your WordPress installation.', 'tutor' ) );  ?></td>
			<td><?php echo esc_html( size_format( $environment['max_upload_size'] ) ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Default Timezone is UTC"><?php esc_html_e( 'Default timezone is UTC', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'The default timezone for your server.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( 'UTC' !== $environment['default_timezone'] ) {
					/* Translators: %s: default timezone.. */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Default timezone is %s - it should be UTC', 'tutor' ), esc_html( $environment['default_timezone'] ) ) . '</mark>';
				} else {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="fsockopen/cURL"><?php esc_html_e( 'fsockopen/cURL', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Payment gateways can use cURL to communicate with remote servers to authorize payments, other plugins may also use it when communicating with remote services.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( $environment['fsockopen_or_curl_enabled'] ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				} else {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'tutor' ) . '</mark>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="DOMDocument"><?php esc_html_e( 'DOMDocument', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'HTML/Multipart emails use DOMDocument to generate inline CSS in templates.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( $environment['domdocument_enabled'] ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				} else {
					/* Translators: %s: classname and link. */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'tutor' ), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>' ) . '</mark>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="GZip"><?php esc_html_e( 'GZip', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'GZip (gzopen) is used to open the GEOIP database from MaxMind.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( $environment['gzip_enabled'] ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				} else {
					/* Translators: %s: classname and link. */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not support the %s function - this is required to use the GeoIP database from MaxMind.', 'tutor' ), '<a href="https://php.net/manual/en/zlib.installation.php">gzopen</a>' ) . '</mark>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Multibyte String"><?php esc_html_e( 'Multibyte string', 'tutor' ); ?>:</td>
			<td class="help"><?php echo tutor_utils()->help_tip( esc_html__( 'Multibyte String (mbstring) is used to convert character encoding, like for emails or converting characters to lowercase.', 'tutor' ) );  ?></td>
			<td>
				<?php
				if ( $environment['mbstring_enabled'] ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				} else {
					/* Translators: %s: classname and link. */
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( 'Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'tutor' ), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>' ) . '</mark>';
				}
				?>
			</td>
		</tr>



		</tbody>
	</table>





	<table class="tutor_status_table widefat" cellspacing="0">
		<thead>
		<tr>
			<th colspan="3" data-export-label="Templates">
				<h2>Templates </h2>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td data-export-label="Overrides">Overrides</td>
			<td class="help">&nbsp;</td>
			<td>
				<?php
				$override_files = \TUTOR\Admin::template_overridden_files();
				if (is_array($override_files) && count($override_files)){
					foreach ($override_files as $file){
						echo "{$file} ,<br />";
					}
				}
				?>
			</td>
		</tr>
		</tbody>
	</table>

</div>